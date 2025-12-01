<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

use App\Models\ReturnSpare;
use App\Models\ReturnSpareItem;
use App\Models\ReturnSpareBarcode;
use App\Models\ServicePartner;
use App\Models\SpareInventory;
use App\Models\SpareReturn;
use App\Models\Invoice;
use App\Models\CustomerPointServiceSpare;
use App\Models\Product;
use App\Models\PurchaseOrderBarcode;
use App\Models\CRPFinalSpare;
use App\Models\CustomerPointService;
use App\Models\Dealer;
use App\Models\InvoiceItem;
use App\Models\Stock;
use App\Models\StockProduct;
use Illuminate\Support\Facades\DB;
use App\Models\StockBarcode;
use App\Models\Ledger;
use App\Models\SalesOrder;
use App\Models\SalesOrderProduct;
use Barryvdh\DomPDF\Facade\Pdf;
use PHPUnit\Framework\Constraint\SameSize;

class ReturnSpareController extends Controller
{

    public function __construct(Request $request)
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            
            if(Auth::user()->id == 8){
                abort(404);
            }            

            return $next($request);
        });
    }
    
    public function index(Request $request)
    {
        # List Returns...
        $type = !empty($request->type)?$request->type:'service_partner';
        // dd($type);
        $search = !empty($request->search)?$request->search:'';
        if($type ==='service_partner'){
            $data = ReturnSpare::with('service_partner','dealer')->whereNull('dealer_id')->select('*');
            $totalResult = ReturnSpare::with('service_partner','dealer')->whereNull('dealer_id')->select('id');
        }else{
            $data = ReturnSpare::with('service_partner','dealer')->whereNull('service_partner_id')->select('*');
            $totalResult = ReturnSpare::with('service_partner','dealer')->whereNull('service_partner_id')->select('id');
        }


        // if(!empty($search) && $type ==='service_partner' ){
        //     $data = $data->where('transaction_id', 'LIKE', '%'.$search.'%')->orWhereHas('service_partner', function($service_partner) use ($search){
        //         $service_partner->where('person_name','LIKE','%'.$search.'%')->orWhere('company_name','LIKE','%'.$search.'%')->orWhere('phone','LIKE','%'.$search.'%')->orWhere('email','LIKE','%'.$search.'%');
        //     });
        //     $totalResult = $totalResult->where('transaction_id', 'LIKE', '%'.$search.'%')->orWhereHas('service_partner', function($service_partner) use ($search){
        //         $service_partner->where('person_name','LIKE','%'.$search.'%')->orWhere('company_name','LIKE','%'.$search.'%')->orWhere('phone','LIKE','%'.$search.'%')->orWhere('email','LIKE','%'.$search.'%');
        //     });
        // }

        // if(!empty($search) && $type ==='dealer' ){
        //     $data = $data->where('transaction_id', 'LIKE', '%'.$search.'%')->orWhereHas('dealer', function($dealer) use ($search){
        //         $dealer->where('name','LIKE','%'.$search.'%')->orWhere('phone','LIKE','%'.$search.'%')->orWhere('email','LIKE','%'.$search.'%');
        //     });
        //     $totalResult = $totalResult->where('transaction_id', 'LIKE', '%'.$search.'%')->orWhereHas('dealer', function($dealer) use ($search){
        //         $dealer->where('name','LIKE','%'.$search.'%')->orWhere('phone','LIKE','%'.$search.'%')->orWhere('email','LIKE','%'.$search.'%');
        //     });
        // }

         // Apply search filters
            if (!empty($search)) {
                $data = $data->where(function ($query) use ($search, $type) {
                    $query->where('transaction_id', 'LIKE', "%$search%");

                    if ($type === 'service_partner') {
                        $query->orWhereHas('service_partner', function ($subQuery) use ($search) {
                            $subQuery->where('person_name', 'LIKE', "%$search%")
                                ->orWhere('company_name', 'LIKE', "%$search%")
                                ->orWhere('phone', 'LIKE', "%$search%")
                                ->orWhere('email', 'LIKE', "%$search%");
                        });
                    } elseif ($type === 'dealer') {
                        $query->orWhereHas('dealer', function ($subQuery) use ($search) {
                            $subQuery->where('name', 'LIKE', "%$search%")
                                ->orWhere('phone', 'LIKE', "%$search%")
                                ->orWhere('email', 'LIKE', "%$search%");
                        });
                    }
                });

                $totalResult = $totalResult->where(function ($query) use ($search, $type) {
                    $query->where('transaction_id', 'LIKE', "%$search%");

                    if ($type === 'service_partner') {
                        $query->orWhereHas('service_partner', function ($subQuery) use ($search) {
                            $subQuery->where('person_name', 'LIKE', "%$search%")
                                ->orWhere('company_name', 'LIKE', "%$search%")
                                ->orWhere('phone', 'LIKE', "%$search%")
                                ->orWhere('email', 'LIKE', "%$search%");
                        });
                    } elseif ($type === 'dealer') {
                        $query->orWhereHas('dealer', function ($subQuery) use ($search) {
                            $subQuery->where('name', 'LIKE', "%$search%")
                                ->orWhere('phone', 'LIKE', "%$search%")
                                ->orWhere('email', 'LIKE', "%$search%");
                        });
                    }
                });
            }

        $data = $data->orderBy('id','desc')->paginate(20);
        $totalResult = $totalResult->count();
        // dd($data);
        return view('returnspare.list', compact('type','search','data','totalResult'));
    }

    public function add(Request $request)
    {
        # Add Return Spare...
        $goods_type = !empty($request->goods_type)?$request->goods_type:'';
        $return_for = !empty($request->return_for)?$request->return_for:'';
        $dealers = Dealer::where('status', 1)->orderBy('name', 'asc')->get();
        $dealer_id = !empty($request->dealer_id)?$request->dealer_id:'';
        $service_partners = ServicePartner::where('is_default', 0)->where('status', 1)->orderBy('person_name', 'asc')->get();
        $service_partner_id = !empty($request->service_partner_id)?$request->service_partner_id:'';
        return view('returnspare.add', compact('return_for','goods_type','service_partners','service_partner_id','dealers','dealer_id'));
    }
    // public function add(Request $request)
    // {
    //     # Add Return Spare..
    //     $service_partners = ServicePartner::where('is_default', 0)->where('status', 1)->orderBy('person_name', 'asc')->get();
    //     $service_partner_id = !empty($request->service_partner_id)?$request->service_partner_id:'';
    //     return view('returnspare.add-copy', compact('service_partners','service_partner_id'));
    // }

    public function save(Request $request)
    {
        // dd($request->all());
        # Save New Return Spare...
        $request->validate([
            'service_partner_id' => 'nullable', 
            'dealer_id' => 'nullable', 
            'details.*.product_id' => 'required',
            'details.*.quantity' => 'required',
            'details.*.product_price' => 'required|not_in:0',
            'details.*.tax' => 'required',
            'details.*.hsn_code' => 'required',
        ],[
            'details.*.product_id.required' => 'Please add product',
            'details.*.quantity.required' => 'Please add quantity',
            'details.*.product_price.required' => 'Please add rate',
            'details.*.product_price.not_in' => 'Please add price',
            'details.*.tax.required' => 'Please add tax',
            'details.*.hsn_code.required' => 'Please add HSN',
        ]);


        $params = $request->except('_token');

        $transaction_id = 'RTRN'.genAutoIncreNoYearWiseOrder(4,'return_spare',date('Y'),date('m'));
        
        // // dd($transaction_id);
        
        $service_partner_id = $params['service_partner_id'];
        $dealer_id = $params['dealer_id'];
        $details = $params['details'];
        $errorMessage = array();
        foreach($details as $key => $item){
            // $check_last_invoice_item = InvoiceItem::where('product_id',$item['product_id'])->whereHas('invoice', function($inv) use($service_partner_id){
            //     $inv->where('service_partner_id',$service_partner_id);
            // })->orderBy('id','desc')->first();

            // // echo '<pre>'; print_r($check_last_invoice_item);
            // if($check_last_invoice_item->price != $item['product_price']){
            //     $errorMessage = array(
            //         'details.'.$key.'.product_price' => 'Wrong Product Price'
            //     );                
            // }

            $check_sales_orders_ids = SalesOrderProduct::where('product_id',$item['product_id'])->pluck('sales_orders_id')->toArray();
            $dealer_ids = SalesOrder::whereIn('id', $check_sales_orders_ids)->groupBy('dealer_id')->pluck('dealer_id')->toArray();
            
            $service_partner_ids = SalesOrder::whereIn('id', $check_sales_orders_ids)->groupBy('service_partner_id')->pluck('service_partner_id')->toArray();
            $exists = false;
            if(!empty($service_partner_id)){
                $exists = in_array($service_partner_id,$service_partner_ids);
            }else{
                $exists = in_array($dealer_id,$dealer_ids);
                // dd($exists);
            }

            if(!$exists){
                $errorMessage = array(
                            'details.'.$key.'.product_id' => !empty($service_partner_id) 
                            ? 'This Service Partner has no sales record for this product.' 
                            : 'This Dealer has no sales record for this product.'
                        );   
            }
            
            
        }
        if(!empty($errorMessage)){            
            return redirect()->back()->withErrors($errorMessage)->withInput();
        }
        // dd($params);

        $returnSpareArr = array(
            'transaction_id' => $transaction_id,
            'dealer_id' => $dealer_id,
            'service_partner_id' => $service_partner_id,
            'amount' => $params['order_amount_val'],
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        );

        $id = ReturnSpare::insertGetId($returnSpareArr);
        foreach($details as $key => $item){
            $returnSpareItemsArr = array(
                'return_spare_id' => $id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'product_price' => $item['product_price'],
                'product_total_price' => $item['product_total_price'],
                'hsn_code' => $item['hsn_code'],
                'tax' => $item['tax'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            );
            ReturnSpareItem::insert($returnSpareItemsArr);
            for($i=0; $i<$item['quantity'];$i++){
                // $barcodeGenerator = barcodeGenerator();
                $barcodeGenerator = genAutoIncreNoBarcode($item['product_id'],date('Y'),'return_spare');
                $barcode_no = $barcodeGenerator['barcode_no'];
                $code_html = $barcodeGenerator['code_html'];
                $code_base64_img = $barcodeGenerator['code_base64_img'];
                $returnSpareBarcodeData = array(
                    'return_spare_id' => $id,
                    'product_id' => $item['product_id'],
                    'barcode_no' => $barcode_no,
                    'code_html' => $code_html,
                    'code_base64_img' => $code_base64_img,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                );
                ReturnSpareBarcode::insert($returnSpareBarcodeData);
            }
        }

        Session::flash('message', 'Return Order Created Successfully');
        return redirect()->route('return-spares.list');

    }
    public function store_crp_spare(Request $request){
        // dd($request->all());
        if(!isset($request->crp_id)){
            return redirect()->back()->with('error', 'Please select CRP first.');
        }
        $data = CustomerPointService::find($request->crp_id);
        
        if($data->return_spare==1){
            return redirect()->back()->with('error', 'Already return spare ordered for this call');
        }
        // No spare will be used for this call
    //   dd($data->is_spare_required);
        if(!is_null($data->is_spare_required) || $data->status==9){
            $only_estimate_spare = CustomerPointServiceSpare::where('crp_id', $data->id)->get();
           
            if(count($only_estimate_spare)==0){
                return redirect()->back()->with('error', 'Sorry! You do not have any spare for for this call.');
            }
            $final_amount = CustomerPointServiceSpare::where('crp_id', $data->id)->sum('final_amount');
            $ledger_credit_amount = $final_amount;

            // Generate for ReturnSpare
            DB::beginTransaction(); // Start the transaction

            try {
                $transaction_id = 'RTRN'.genAutoIncreNoYearWiseOrder(4,'return_spare',date('Y'),date('m'));
                $service_partner_id = $data->assign_service_perter_id;
                $returnSpareArr = array(
                    'transaction_id' => $transaction_id,
                    'crp_id' => $data->id,
                    'service_partner_id' => $service_partner_id,
                    'amount' => $ledger_credit_amount,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                );
        
                $id = ReturnSpare::insertGetId($returnSpareArr);
                foreach($only_estimate_spare as $key => $item){
                    $product = Product::find($item['sp_id']);
                    $hsn_code = $product?$product->hsn_code:"";
                    $gst = $product?$product->gst:"";
                    $returnSpareItemsArr = array(
                        'return_spare_id' => $id,
                        'product_id' => $item['sp_id'],
                        'quantity' => $item['quantity'],
                        'product_price' => $item['final_amount'],
                        'product_total_price' => $item['final_amount'],
                        'hsn_code' => $hsn_code,
                        'tax' => $gst,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    );
                    ReturnSpareItem::insert($returnSpareItemsArr);
                    for($i=0; $i<$item['quantity'];$i++){
                        // $barcodeGenerator = barcodeGenerator();
                        $barcodeGenerator = genAutoIncreNoBarcode($item['sp_id'],date('Y'),'return_spare');
                        $barcode_no = $barcodeGenerator['barcode_no'];
                        $code_html = $barcodeGenerator['code_html'];
                        $code_base64_img = $barcodeGenerator['code_base64_img'];
                        $returnSpareBarcodeData = array(
                            'return_spare_id' => $id,
                            'product_id' => $item['sp_id'],
                            'barcode_no' => $barcode_no,
                            'code_html' => $code_html,
                            'code_base64_img' => $code_base64_img,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s')
                        );
                        ReturnSpareBarcode::insert($returnSpareBarcodeData);
                    }
                }
                $data->return_spare = 1;
                $data->return_spare_order = $transaction_id;
                $data->save();
                DB::commit(); // Commit the transaction if everything is successful
                Session::flash('message', 'Return Order Created Successfully');
                return redirect()->route('return-spares.list', ['search'=>$transaction_id]);

            } catch (\Exception $e) {
                DB::rollBack(); // Rollback the transaction on failure
        
                return redirect()->back()->with('error', 'Failed to create return order: ' . $e->getMessage());
            }
        }else{
           return redirect()->route('return-spares.crp_spare_without_warranty_check', ['crp_id'=>$request->crp_id]);
        }
    }
    public function crp_spare_without_warranty_check(Request $request){
        $id = $request->crp_id;
        if(!isset($request->crp_id)){
            return redirect()->back()->with('error', 'Please select CRP first.');
        }
        $data = CustomerPointService::select('return_spare', 'status')->where('id',$id)->first();
        $final_spare_data = CRPFinalSpare::where('crp_id',$request->crp_id)->get();
        $debited_amount = CustomerPointServiceSpare::where('crp_id', $request->id)->sum('final_amount');
        return view('customer-point-repair.return_spare_check', compact('final_spare_data', 'debited_amount', 'id', 'data'));
    }
    public function spare_not_required($id, $status){
        $spare = CRPFinalSpare::find($id);
        if ($spare) {
            $spare->return_required = $status;
            $spare->save();
            return redirect()->back()->with('success', 'Status updated successfully.');
        } else {
            return redirect()->back()->with('error', 'Item not found.');
        }
    }
    public function return_old_spare(Request $request)
    {
        // dd($request->all());
        try {
            if (!isset($request->crp_id)) {
                return redirect()->back()->with('error', 'Please select CRP first.');
            }
    
            $data = CustomerPointService::find($request->crp_id);
    
            if (!$data) {
                return redirect()->back()->with('error', 'CRP not found.');
            }
    
            if ($data->return_spare == 1) {
                return redirect()->back()->with('error', 'Already return spare ordered for this call.');
            }
    
            // dd($data->status);
            // No spare will be used for this call
            if ($data->status == 8) {
                $final_spare = CRPFinalSpare::where('crp_id', $data->id)->get();
    
                if (count($final_spare) == 0) {
                    return redirect()->back()->with('error', 'Sorry! You do not have any spare for this call.');
                }
    
                // Begin Transaction
                DB::beginTransaction();
    
                $final_amount = CRPFinalSpare::where('crp_id', $data->id)->sum('actual_price');
                $ledger_credit_amount = $final_amount;
                $service_partner_id = $data->assign_service_perter_id;
                $product = Product::find($data->product_id);
                $supplier_warranty_period = $product->supplier_warranty_period;
    
                foreach ($final_spare as $key => $item) {
                    $spare_id = $item->spare_id;
                    $stock_box = \App\Models\StockBarcode::where('barcode_no', $item->old_barcode)->first();
    
                    if (!$stock_box) {
                        throw new \Exception("Stock box not found for barcode: {$item->old_barcode}");
                    }
    
                    // $stock_prod = \App\Models\StockProduct::where('stock_id', $stock_box->stock_id)->where('product_id', $spare_id)->first();
    
                    // if (!$stock_prod) {
                    //     throw new \Exception("Stock product not found for spare id: {$spare_id}");
                    // }
    
                    $cost_price = $item->actual_price;
    
                    $is_item_supplier_warranty = is_item_supplier_warranty($data->product_id, $data->bill_date); // Assuming $data->bill_date instead of undefined $repair->bill_date
    
                    // $new_barcode_no = "RETSPR" . $item->old_barcode;
                    // $barcodeGeneratorWithNo = barcodeGeneratorWithNo($new_barcode_no);
    
                    $spareBarcodeArr = [
                        'service_partner_id' => $service_partner_id,
                        'spare_id' => $item->spare_id,
                        'barcode_no' => $item->old_barcode,
                        'crp_id' => $data->id,
                        'goods_id' => $data->product_id,
                        'rate' => $cost_price,
                        'goods_supplier_warranty_period' => $supplier_warranty_period,
                        'in_warranty' => $is_item_supplier_warranty,
                        'new_barcode_no' => $item->new_barcode,
                        'code_html' => $item->new_code_html,
                        'code_base64_img' => $item->new_code_base64_img,
                        'is_returned' => 1,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ];
    
                    // Insert spare return details
                    SpareReturn::insert($spareBarcodeArr);
    
                    $po = PurchaseOrderBarcode::where('barcode_no', $item->old_barcode)->first();
                    $supplier_id = $po->order->supplier_id;
    
                    $spare_return = SpareReturn::where('barcode_no', $item->old_barcode)->first(); // Fetching inserted spare return
    
                    $spareInventoryArr = [
                        'spare_return_id' => $spare_return->id,
                        'spare_id' => $spare_return->spare_id,
                        'barcode_no' => $spare_return->new_barcode_no,
                        'supplier_id' => $supplier_id,
                        'service_partner_id' => $spare_return->service_partner_id,
                        'goods_id' => $spare_return->goods_id,
                        'rate' => $cost_price,
                        'created_at' => now(),
                    ];
    
                    // Insert spare inventory details
                    SpareInventory::insert($spareInventoryArr);
    
                    $ledgerData = [
                        'type' => 'credit',
                        'amount' => $cost_price,
                        'entry_date' => now()->format('Y-m-d'),
                        'user_type' => 'servicepartner',
                        'service_partner_id' => $spare_return->service_partner_id,
                        'purpose' => 'spare_return',
                        'transaction_id' => $spare_return->new_barcode_no,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
    
                    // Insert ledger entry
                    Ledger::insert($ledgerData);
                }
    
                $data->return_spare = 1;
                $data->save();
                // Commit Transaction
                DB::commit();
                return redirect()->back()->with('success', 'Spare return processed successfully.');
    
            } else {
                return redirect()->route('return-spares.crp_spare_without_warranty_check', ['crp_id' => $request->crp_id])->with('error', 'Please close the call first.');
            }
        } catch (\Exception $e) {
            // Rollback in case of an error
            DB::rollBack();
            return redirect()->back()->with('error', 'Error occurred: ' . $e->getMessage());
        }
    }
    

    public function show($idStr,$getQueryString='')
    {
        # detail...
        try{
            $id = Crypt::decrypt($idStr);
            $data = ReturnSpare::find($id);
            // dd($data);
        return view('returnspare.detail', compact('idStr','data','getQueryString'));
        } catch (DecryptException $e){
            return abort(404);
        }
    }

    public function barcodes(Request $request,$idStr,$getQueryString='')
    {
        # barcodes...
        try{
            $id = Crypt::decrypt($idStr);
            $search = !empty($request->search)?$request->search:'';
            
            $data = ReturnSpareBarcode::where('return_spare_id',$id);
            if(!empty($search)){
                $data = $data->where(function($q) use ($search){
                    $q->where('barcode_no','LIKE', '%'.$search.'%')->orWhereHas('products', function ($product) use ($search) {
                        $product->where('title', 'LIKE','%'.$search.'%');
                    });
                });
            }
            $data = $data->get();
            // dd($data);
            return view('returnspare.barcodes', compact('idStr','getQueryString','data','id','search'));
        } catch (DecryptException $e){
            return abort(404);
        }
    }

    public function barcode_csv(Request $request,$idStr)
    {
        # code...
        try{            
            $search = !empty($request->search)?$request->search:'';
            $id = Crypt::decrypt($idStr);
            $data = ReturnSpareBarcode::where('return_spare_id',$id);     
            if(!empty($search)){
                $data = $data->where(function($q) use ($search){
                    $q->where('barcode_no','LIKE', '%'.$search.'%')->orWhereHas('products', function ($product) use ($search) {
                        $product->where('title', 'LIKE','%'.$search.'%');
                    });
                });
            }
       
            $data = $data->pluck('barcode_no')->toArray();
            // dd($data);
            $rs = ReturnSpare::find($id);
            $transaction_id = $rs->transaction_id;
            $fileName = "Barcodes-Return-Spares-".$transaction_id.".csv";
            // dd($data);
            $headers = array(
                "Content-type"        => "text/csv",
                "Content-Disposition" => "attachment; filename=$fileName",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            );

            $columns = array('Barcodes');

            $callback = function() use($data, $columns) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);
                $net_value = 0;
                
                foreach ($data as $key => $value) {                    
                    $row['Barcodes'] = $value;

                    fputcsv($file, array($row['Barcodes']));                
                }
                fclose($file);
            };
            return response()->stream($callback, 200, $headers);
            // $pdf = Pdf::loadView('returnspare.download', compact('data','id','transaction_id'));
            // return $pdf->download($pobarcodepdfname.'.pdf');

            // return view('purchaseorder.download', compact('data','id','status'));
        } catch (DecryptException $e) {
            return abort(404);
        }
        
    }

    public function make_grn(Request $request,$idStr,$getQueryString='')
    {
        try {
            $id = Crypt::decrypt($idStr);
            $search = !empty($request->search)?$request->search:'';
            $goods_in_type = !empty($request->goods_in_type)?$request->goods_in_type:'';
            $returnspare = ReturnSpare::find($id);

            $transaction_id = $returnspare->transaction_id;
            $data = ReturnSpareBarcode::with('products')->where('return_spare_id',$id);        
            if(!empty($search)){
                $data = $data->where(function($q) use ($search){
                    $q->where('barcode_no','LIKE', '%'.$search.'%')->orWhereHas('products', function ($products) use ($search) {
                        $products->where('title', 'LIKE','%'.$search.'%');
                    });
                });
            }
            $data = $data->orderBy('barcode_no','asc')->get()->groupBy('products.id');
            // dd($data);
            if(!empty($goods_in_type)){
                ReturnSpare::where('id',$id)->update([
                    'goods_in_type' => $goods_in_type
                ]);
            }

            $count_scanned = ReturnSpareBarcode::where('return_spare_id',$id)->where('is_stock_in',1)->count();

            $count_products = ReturnSpareBarcode::where('return_spare_id',$id)->count();
            
            return view('returnspare.makegrn', compact('id','idStr','transaction_id','getQueryString','data','search','goods_in_type','count_scanned','count_products'));
        } catch ( DecryptException $e) {
            return abort(404);
        }        
    }

    public function generategrn(Request $request)
    {
        // dd($request->all());
        # generate GRN... 
        $request->validate([
            'barcode_no' => 'required'
        ],[
            'barcode_no.required' => 'Please add barcode'
        ]);      

        $params = $request->except('_token');
        $return_spare_id = $params['id'];
        // $grn_no = genAutoIncreNo(10,'stock');
        $grn_no = 'GRN'.genAutoIncreNoYearWiseOrder(4,'stock',date('Y'),date('m'));
        $params['grn_no'] = $grn_no;
        // dd($params);
        
        $returnspare = ReturnSpare::find($return_spare_id);
        $amount = $returnspare->amount;
        $service_partner_id = $returnspare->service_partner_id;
        $dealer_id = $returnspare->dealer_id;
        
        $stock_id = Stock::insertGetId([
            'return_spare_id'=>$return_spare_id,
            'grn_no' => $grn_no,
            'goods_in_type' => $returnspare->goods_in_type,
            'amount' => $amount,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        for($i=0;$i<count($params['product_id']);$i++){
            StockProduct::insert([
                'stock_id' => $stock_id,
                'product_id' => $params['product_id'][$i],
                'count' => $params['count'][$i],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            updateStockInvetory($params['product_id'][$i],$params['count'][$i],'in',$return_spare_id,'return_spares');
        }
        

        
        $barcodeArr = array();
        for($i=0;$i<count($params['barcode_no']);$i++){
            $barcodeArr[] = array(
                'stock_id' => $stock_id,
                'barcode_no' => $params['barcode_no'][$i],
                'product_id' => getSingleAttributeTable('return_spare_barcodes','barcode_no',$params['barcode_no'][$i],'product_id'),
                'code_html' => getSingleAttributeTable('return_spare_barcodes','barcode_no',$params['barcode_no'][$i],'code_html'),                
                'code_base64_img' => getSingleAttributeTable('return_spare_barcodes','barcode_no',$params['barcode_no'][$i],'code_base64_img'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            );
        }
        StockBarcode::insert($barcodeArr);
        
        // dd($barcodeArr);

        
        if($returnspare->goods_in_type == 'scan'){
            ReturnSpare::where('id',$return_spare_id)->update([
                'status'=>2,
                'is_goods_in'=>1,
                'grn_no'=>$params['grn_no']
            ]);
        } else {
            ReturnSpare::where('id',$return_spare_id)->update([
                'status'=>2,
                'is_goods_in'=>0,
                'grn_no'=>$params['grn_no']
            ]);
        }
        # Ledger Entry Service Partner
        $this->ledgerEntryCallClose($dealer_id,$service_partner_id,$amount,$grn_no);

        Session::flash('message', 'Goods Received Note Created Successfully');
        return redirect()->route('return-spares.list');
    }

    private function ledgerEntryCallClose($dealer_id,$service_partner_id,$amount,$grn_no){
        Ledger::insert([
            'type' => 'credit',
            'dealer_id' => $dealer_id,
            'service_partner_id' => $service_partner_id,
            'amount' => $amount,
            'entry_date' => date('Y-m-d'),
            'user_type' => 'servicepartner',
            'purpose' => 'return_spares',
            'transaction_id' => $grn_no,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function cancel($idStr,$getQueryString='')
    {
        # Cancel Order...
        try {
            $id = Crypt::decrypt($idStr);
            $ReturnSpare = ReturnSpare::find($id);
            if($ReturnSpare->crp_id){
                $data = CustomerPointService::find($ReturnSpare->crp_id);
                $data->return_spare = 0;
                $data->return_spare_order = NULL;
                $data->save();
            }
            ReturnSpare::where('id',$id)->update(['status'=>3,'updated_at'=>date('Y-m-d H:i:s')]);

            Session::flash('message', 'Order Cancelled Successfully');
            return redirect()->route('return-spares.list',$getQueryString);
        } catch ( DecryptException $e) {
            return abort(404);
        }
        
    }


}
