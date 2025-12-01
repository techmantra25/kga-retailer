<?php

namespace App\Http\Controllers\ServicePartner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Models\Repair;
use App\Models\CustomerPointService;
use App\Models\CustomerPointServiceSpare;
use App\Models\RepairSpare;
use App\Models\RepairSpareRequisitionNote;
use App\Models\PurchaseOrderProduct;
use App\Models\PurchaseOrder;
use App\Models\Product;
use App\Models\Ledger;

class CustomerRepairPointController extends Controller
{
    public function __construct(Request $request)
    {
        $this->middleware('auth:servicepartner');
    }

    public function add_spare($idStr)
    {
        # Add Spare Parts Form For Customer Repair point Request  from Service partner side...
        try {
            // $repair_id = Crypt::decrypt($repair_id_str);
            $id = Crypt::decrypt($idStr);
            
            $data = CustomerPointService::find($id);
            $spare_data =CustomerPointServiceSpare::where('crp_id',$id)->orderBy('created_at','desc')->get();
            return view('servicepartnerweb.customer-repair-point.add-spare', compact('data','spare_data'));
        } catch ( DecryptException $e) {
            return abort(404);
        }
        
    }
    public function save_spare(Request $request)
    {
        // CustomerPointServiceSpare::where('crp_id',$request->crp_id)->delete();
        $product_id = array_filter($request->product_id, function ($value) {
            return !is_null($value);
        });
        if(count($product_id)==0){
            return redirect()->back()->with('error', 'Please select a product.');
        }
        foreach ($product_id as $index => $productId) {
            $product = Product::find($productId);
            $product_name = $product->title;
           // $mop = $product->mop?$product->mop:0;
           $profit_percentage = $product->profit_percentage?$product->profit_percentage:0;
           $last_po_cost_price = $product->last_po_cost_price?$product->last_po_cost_price:0;
           $mop = $last_po_cost_price *(1+($profit_percentage/100));
            
            $purchase_order_ids = PurchaseOrderProduct::where('product_id',$productId)->pluck('purchase_order_id')->toArray();
            // dd($productId);
            if (!empty($purchase_order_ids)) {
                // Get the latest purchase order ID based on the condition
                $latest_purchase_order_id = PurchaseOrder::whereIn('id', $purchase_order_ids)
                ->where('is_goods_in', 1)
                ->orderBy('created_at', 'desc') // Adjust if needed, e.g., 'updated_at'
                ->pluck('id')
                ->first(); // Get the latest (first) ID
                if($latest_purchase_order_id){
                    $last_po_cost_price = PurchaseOrderProduct::where('purchase_order_id',$latest_purchase_order_id)->pluck('cost_price')->first();
                    // Get the quantity for the current product from the request, default to 1 if not provided
                    $quantity = isset($request->product_qty[$index]) && $request->product_qty[$index] > 0 
                    ? $request->product_qty[$index] 
                    : 1;
                    
                    // Calculate final amount
                    $final_amount = $quantity * $mop;
                        // Calculate profit percentage
                        $profit_percentage = 0; // Default to 0 if last_po_cost_price is 0 to avoid division by zero
                        if ($last_po_cost_price > 0) {
                            $profit_percentage = (($mop - $last_po_cost_price) / $last_po_cost_price) * 100;
                            $profit_percentage = round($profit_percentage, 2); // Round to 2 decimal places
                        }

                    
                        $data = CustomerPointServiceSpare::where('crp_id', $request->crp_id)->where('sp_id', $productId)->first();
                    if(!$data){
                        $data = new CustomerPointServiceSpare();
                        $data->quantity = $quantity;

                    }else{
                        $data->quantity = $data->quantity+$quantity; 
                    }
                    $data->crp_id = $request->crp_id;  // Assuming `crp_id` is needed for every record
                    $data->generate_by = $request->generate_by; // If applicable, set other fields accordingly
                    $data->sp_id = $productId;  // Set the current product ID
                    $data->sp_name = $product_name;  
                    $data->mop = $mop;  
                    $data->last_po_cost_price = $last_po_cost_price;  
                    $data->profit_percentage = $profit_percentage;  
                    $data->final_amount = $data->quantity * $mop;  
                    $data->save();
                    
                }else{
                    return redirect()->back()->with('error', 'This ('.$product_name.') is out of stock.');
                }
            }else{
                return redirect()->back()->with('error', 'This product ('.$product_name.') has no purchase order record.');
            }
        }
        return redirect()->back()->with('success', 'Spares added successfully.');
        
    }

    public function delete_spare(Request $request)
    {
        $data = CustomerPointServiceSpare::find($request->id);
        $data->delete();
        // Return a JSON response indicating success
        return response()->json([
            'status' => 'success',
            'message' => 'Spare deleted successfully.'
        ]);
    }

}