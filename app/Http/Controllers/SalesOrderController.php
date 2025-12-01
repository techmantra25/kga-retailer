<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SalesOrder;
use App\Models\SalesOrderProduct;
use App\Models\Customer;
use App\Models\Dealer;
use App\Models\ServicePartner;
use App\Models\Packingslip;
use App\Models\PackingslipBarcode;
use App\Models\PackingslipProduct;
use App\Models\CustomerPointServiceSpare;
use App\Models\CustomerPointService;
use Illuminate\Support\Facades\DB;
use App\Models\StockBarcode;
use App\Models\StockProduct;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class SalesOrderController extends Controller
{
    public function __construct(Request $request)
    {
        $this->middleware('auth');
      //  $this->middleware(function ($request, $next) {
            
          //  $accessSalesOrder = userAccess(Auth::user()->role_id,11);
           // if(!$accessSalesOrder){
            //    abort(404);
          //  }           

           // return $next($request);
      //  });
    }


    public function index(Request $request)
    {
        # sales order...
        $search = !empty($request->search)?$request->search:'';
        $type = !empty($request->type)?$request->type:'';
        $status = !empty($request->status)?$request->status:'';
        $product_name = !empty($request->product_name)?$request->product_name:'';
        $product_id = !empty($request->product_id)?$request->product_id:'';
        $paginate = !empty($request->paginate)?$request->paginate:25;
        $total = SalesOrder::count();
        
        $data = SalesOrder::select('*');
        $totalResult = SalesOrder::select('id');

        $countAll = SalesOrder::count();
        $countPending = SalesOrder::where('status','pending')->count();
        $countOngoing = SalesOrder::where('status','ongoing')->count();
        $countCancelled = SalesOrder::where('status','cancelled')->count();
        $countCompleted = SalesOrder::where('status','completed')->count();
        
        
        if(!empty($search)){
            $data = $data->where(function($query) use ($search){
                $query->where('order_no', 'LIKE','%'.$search.'%')->orWhere('details', 'LIKE', '%'.$search.'%')->orWhereHas('dealer', function ($dealer) use ($search) {
                    $dealer->where('name', 'LIKE','%'.$search.'%')->orWhere('phone', 'LIKE','%'.$search.'%')->orWhere('email', 'LIKE','%'.$search.'%');
                })->orWhereHas('service_partner', function ($service_partner) use ($search) {
                    $service_partner->where('company_name', 'LIKE','%'.$search.'%')->orWhere('person_name', 'LIKE','%'.$search.'%')->orWhere('email', 'LIKE','%'.$search.'%')->orWhere('email', 'LIKE','%'.$search.'%');
                });
            });
            $totalResult = $totalResult->where(function($query) use ($search){
                $query->where('order_no', 'LIKE','%'.$search.'%')->orWhere('details', 'LIKE', '%'.$search.'%')->orWhereHas('dealer', function ($dealer) use ($search) {
                    $dealer->where('name', 'LIKE','%'.$search.'%')->orWhere('phone', 'LIKE','%'.$search.'%')->orWhere('email', 'LIKE','%'.$search.'%');
                })->orWhereHas('service_partner', function ($service_partner) use ($search) {
                    $service_partner->where('company_name', 'LIKE','%'.$search.'%')->orWhere('person_name', 'LIKE','%'.$search.'%')->orWhere('email', 'LIKE','%'.$search.'%')->orWhere('email', 'LIKE','%'.$search.'%');
                });
            });
        }

        if(!empty($type)){
            $data = $data->where('type', $type);
            $totalResult = $totalResult->where('type', $type);
        }
        if(!empty($status)){
            $data = $data->where('status', $status);
            $totalResult = $totalResult->where('status', $status);
        }

        if(!empty($product_id)){
            $data = $data->whereHas('products', function($products) use($product_id){
                $products->where('product_id', $product_id);
            });
            $totalResult = $totalResult->whereHas('products', function($products) use($product_id){
                $products->where('product_id', $product_id);
            });
        }
        
        $data = $data->orderBy('id','desc')->paginate($paginate);
        $totalResult = $totalResult->count();

        $data = $data->appends([
            'search'=>$search,
            'type' => $type,
            'status' => $status,
            'page'=>$request->page,
            'paginate'=>$request->paginate,
            'product_id' => $product_id,
            'product_name' => $product_name
        ]);

        return view('salesorder.list', compact('data','totalResult','total','search','type','paginate','status','countAll','countPending','countOngoing','countCancelled','countCompleted','product_id','product_name'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $service_partner_id = !empty($request->service_partner)?$request->service_partner:'';
        $crp_id = !empty($request->crp_id)?$request->crp_id:'';//Customer Repair Point Id
        $dap_spare_data = [];
        if($crp_id){
            $dap_spare_data = CustomerPointServiceSpare::where('crp_id', $crp_id)->get();
        }
        $customer_id = !empty($request->customer_id)?$request->customer_id:'';
        $type = !empty($request->type)?$request->type:'';

        $customer = Customer::where('status', 1)->orderBy('name','asc')->get();
        $dealer = Dealer::where('status', 1)->orderBy('name','asc')->get();
        $service_partners = ServicePartner::select('id','company_name','person_name')->where('status', 1)->orderBy('company_name','asc')->get();

        return view('salesorder.add',compact('customer','dealer','service_partners','customer_id','type', 'service_partner_id', 'dap_spare_data', 'crp_id'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'type' => 'required|in:sp,fg',
            'service_partner_id' => 'required_if:type,sp', 
            'dealer_id' => 'required_if:type,fg',           
            'details.*.product_id' => 'required',
            'details.*.quantity' => 'required',
            'details.*.product_price' => 'required|not_in:0',
            'details.*.tax' => 'required',
            'details.*.hsn_code' => 'required',
        ],[
            'dealer_id.required_if' => 'Please choose dealer when type is Finished Goods',
            'service_partner_id.required_if' => 'Please choose service partner when type is Spare Parts',
            'details.*.product_id.required' => 'Please add product',
            'details.*.quantity.required' => 'Please add quantity',
            'details.*.product_price.required' => 'Please add rate',
            'details.*.product_price.not_in' => 'Please add price',
            'details.*.tax.required' => 'Please add tax',
            'details.*.hsn_code.required' => 'Please add HSN',
        ]);
        DB::beginTransaction();

        try {
            $params = $request->except('_token');
            $params['crp_id'] = isset($params['crp_id']) ? $params['crp_id'] : null;
            // Retrieve CustomerPointService record using the provided 'crp_id'
            $dap_data = CustomerPointService::where('id', $params['crp_id'])->first();

            if ($dap_data) {
                $spare_data = CustomerPointServiceSpare::where('crp_id', $params['crp_id'])
                ->get(); // Retrieve all matching records
                    foreach ($spare_data as $data) {
                        // Perform any additional operations if needed
                        $data->is_grn_generate = 0;
                        // Save the updated record
                        $data->save();
                    }


                // Retrieve the SalesOrder using the 'sales_orders_id' from the CustomerPointService record
                $SalesOrder = SalesOrder::where('id', $dap_data->sales_orders_id)->first();

                if ($SalesOrder) {
                    // Delete all SalesOrderProduct records associated with the 'sales_orders_id'
                    SalesOrderProduct::where('sales_orders_id', $dap_data->sales_orders_id)->delete();
                    // Delete the SalesOrder itself
                    SalesOrder::where('id', $dap_data->sales_orders_id)->delete();
                }
            }


            $order_no = 'ORD'.genAutoIncreNoYearWiseOrder(4,'sales_orders',date('Y'),date('m'));
            // dd($order_no);
            $salesOrderData = array(
                'order_no' => $order_no,
                'crp_id' => isset($params['crp_id'])?$params['crp_id']:NULL,
                'dealer_id' => isset($params['dealer_id'])?$params['dealer_id']:NULL,
                'service_partner_id' => isset($params['service_partner_id'])?$params['service_partner_id']:NULL,
                'user_id' => Auth::user()->id,
                'type' => $params['type'],
                'details' => json_encode($params['details']),
                'created_at' => date('Y-m-d H:i:s')
            );
            // echo '<pre>'; print_r($salesOrderData); die;
            $id = SalesOrder::insertGetId($salesOrderData);

            $details = $params['details'];
            $total_amount = 0;
            foreach($details as $detail){
                
                // $mop = getSingleAttributeTable('products','id',$detail['product_id'],'mop');
                // $product_total_price = ($detail['quantity'] * $mop);
                $product_total_price = ($detail['quantity'] * $detail['product_price']);
                $salesOrderProductData = array(
                    'sales_orders_id' => $id,
                    'product_id' => $detail['product_id'],
                    'quantity' => $detail['quantity'],
                    'product_price' => $detail['product_price'],
                    'product_total_price' => $product_total_price,
                    'tax' => $detail['tax'],
                    'hsn_code' => $detail['hsn_code'],
                    'created_at' => date('Y-m-d H:i:s')
                );
                $total_amount += $product_total_price;
                SalesOrderProduct::insert($salesOrderProductData);
                if(!empty($params['crp_id'])){
                    // Check if the CustomerPointServiceSpare record exists for the given 'crp_id' and 'sp_id'
                    $dap_spare_data = CustomerPointServiceSpare::where('crp_id', $params['crp_id'])
                    ->where('sp_id', $detail['product_id'])
                    ->first();

                    if ($dap_spare_data) {
                        // Loop based on the quantity, to create a new item for each unit if quantity > 1
                        for ($i = 1; $i <= $detail['quantity']; $i++) {
                        // Create or update the spare data for each quantity
                        $new_spare_data = $dap_spare_data->replicate();  // Clone the existing record

                        // Update fields for the new item
                        $new_spare_data->is_grn_generate = 1;
                        $new_spare_data->quantity = 1; // Since each loop represents one item
                        $new_spare_data->mop = $detail['product_price'];
                        $new_spare_data->sp_name = $detail['product'];
                        $new_spare_data->last_po_cost_price = $detail['product_price'];
                        $new_spare_data->final_amount = $product_total_price / $detail['quantity']; // Divide final amount by quantity
                        $new_spare_data->save(); // Save the new or updated record
                        }
                    } else {
                        // If the record doesn't exist, insert a new one
                        for ($i = 1; $i <= $detail['quantity']; $i++) {
                        $new_spare_data = new CustomerPointServiceSpare();  // Create a new instance

                        // Set the fields for the new item
                        $new_spare_data->crp_id = $params['crp_id'];
                        $new_spare_data->sp_id = $detail['product_id'];
                        $new_spare_data->sp_name = $detail['product'];
                        $new_spare_data->is_grn_generate = 1;
                        $new_spare_data->quantity = 1; // Set quantity for each loop iteration
                        $new_spare_data->mop = $detail['product_price'];
                        $new_spare_data->last_po_cost_price = $detail['product_price'];
                        $new_spare_data->final_amount = $product_total_price / $detail['quantity']; // Divide final amount by quantity

                        // Save the new record
                        $new_spare_data->save();
                        }
                    }
                }
            }
            if($dap_data){
                $dap_data->status = 1;
                $dap_data->sales_orders_id = $id;
                $dap_data->save();
            }
            SalesOrder::where('id',$id)->update(['order_amount'=>$total_amount]);
            DB::commit(); // Commit the transaction
            Session::flash('message', 'Sales Order Created Successfully');
            if($params['crp_id']){
                $dap_spare_delete = CustomerPointServiceSpare::where('crp_id', $params['crp_id'])->where('is_grn_generate', 0)->delete();
                return redirect()->route('customer-point-repair.list-booking');
            }else{
                return redirect()->route('sales-order.list');
            }
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback on failure
            Session::flash('message', 'An error occurred while creating the Sales Order');
            return back()->withInput();
        }
       
    }

    /*
    ** Cancel Sales Order
    */    
    public function cancel($idStr,$getQueryString='')
    {
        try {
            $id = Crypt::decrypt($idStr);
            SalesOrder::where('id',$id)->update(['status'=>3]);
            Session::flash('message', 'Sales Order Cancelled Successfully');
            return redirect('/sales-order/list?'.$getQueryString);
        } catch ( DecryptException $e) {
            return abort(404);
        }
        
    }

    public function show($idStr,$getQueryString='')
    {
        # order details...
        try{
            $id = Crypt::decrypt($idStr);
            $order = SalesOrder::find($id);
            $data = SalesOrderProduct::with('product')->where('sales_orders_id',$id)->get();
            return view('salesorder.detail', compact('id','order','data'));
        } catch (DecryptException $e) {
            return abort(404);
        }
        
    }

    public function generate_packing_slip(Request $request,$idStr,$getQueryString='')
    {
        # view goods out order...
        try{
            $id = Crypt::decrypt($idStr);
            $data = SalesOrderProduct::with('product')->with('order')->where('sales_orders_id',$id)->get();
            // dd($data);
        return view('salesorder.generateps', compact('idStr','data','getQueryString'));
        } catch (DecryptException $e){
            return abort(404);
        }
        
    }

    
    public function save_packing_slip(Request $request)
    {
        # code...
        $request->validate([
            'csv' => 'required'
        ]);

        $params = $request->except('_token');
        $csv = $params['csv'];
        
        $rows = Excel::toArray([],$request->file('csv'));
        $data = $rows[0];
        $barcodeArr = array();

        foreach($data as $item){
            $org_barcode_no = $item[0];
            $barcode_no = trim($org_barcode_no);
            // dd($barcode_no);
            ## Check Barcode

            $checkBarcode = StockBarcode::where('barcode_no',$barcode_no)->first();
            $sales_orders_id = SalesOrder::find($params['sales_orders_id']);
            if(!empty($checkBarcode)){
                
                if(empty($checkBarcode->packingslip_id)){
                    $barcodeArr[] = $barcode_no;
                } else {
                    return redirect()->back()->withErrors(['csv' => $barcode_no.' already stock out for different order !!!'])->withInput(); 
                }

            } else {
                return redirect()->back()->withErrors(['csv' => $barcode_no.' not found, Please check !!!'])->withInput(); 
            }
        }

        $stock_products = StockBarcode::selectRaw('product_id, count(product_id) AS count_product')->whereIn('barcode_no',$barcodeArr)->groupBy('product_id')->get()->toArray();
        // dd($stock_products);
        $proIdArr = array();
        $sales_order = SalesOrderProduct::where('sales_orders_id',$params['sales_orders_id'])->get()->toArray();
        foreach($sales_order as $order){
            $proIdArr[] = $order['product_id'];
        }

        if(!empty($stock_products)){
            foreach($stock_products as $pro){
                $sales_order_product = SalesOrderProduct::where('sales_orders_id',$params['sales_orders_id'])->where('product_id',$pro['product_id'])->first();

                if(!in_array($pro['product_id'],$proIdArr)){
                    return redirect()->back()->withErrors(['csv' => 'Unknown Product Barcode Found In CSV Which Are Not Required Here'])->withInput(); 
                }
                if($sales_order_product->quantity != $pro['count_product']){
                    return redirect()->back()->withErrors(['csv' => 'Mismatched Product Barcode Quantity With Order, Please check'])->withInput(); 
                }
                if(!empty($sales_orders_id->crp_id)){
                    $dap_data = CustomerPointService::where('id', $sales_orders_id->crp_id)->first();
                    if($dap_data){
                        $dap_data->packing_slip = $params['slipno'];
                        $dap_data->packing_slip_status = 1;
                        $dap_data->status = 2;
                        $dap_data->save();
                    }
                }
                
            }
        }

        ## Packing Slip Creation
        $packingslip_id = Packingslip::insertGetId([
            'sales_order_id' => $params['sales_orders_id'],
            'goods_out_type' => 'bulk',
            'slipno' => $params['slipno'],
            'is_goods_out' => 1,
            'created_at' => date('Y-m-d H:i:s') 
        ]);

        foreach($stock_products as $pro){
            
            
            SalesOrderProduct::where('sales_orders_id',$params['sales_orders_id'])->where('product_id',$pro['product_id'])->update([
                'delivered_quantity' => $pro['count_product']
            ]);

            PackingslipProduct::insert([
                'packingslip_id' => $packingslip_id,
                'product_id' => $pro['product_id'],
                'quantity' => $pro['count_product'],
                'created_at' => date('Y-m-d H:i:s') 
            ]);
            

            ## Update quantity of stock inventory
            updateStockInvetory($pro['product_id'],$pro['count_product'],'out',$packingslip_id,'packingslip');
            
        }

        ## Set As Disbursed ... 
        StockBarcode::whereIn('barcode_no',$barcodeArr)->update([
            'packingslip_id' => $packingslip_id,
            'is_stock_out' => 1,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        ## Sales Order Make Completed

        SalesOrder::where('id', $params['sales_orders_id'])->update([
            'status' => 'completed'
        ]);

        $this->savePackingslipBarcodes($packingslip_id,$barcodeArr);

        Session::flash('message', 'Packing Slip Generated & Items Disbursed Successfully');
        if($sales_orders_id->crp_id){
            return redirect()->route('customer-point-repair.list-booking');
        }else{
            return redirect()->route('packingslip.list');
        }
    }

    private function savePackingslipBarcodes($packingslip_id,$barcodeArr){
        foreach($barcodeArr as $barcode_no){

            $stock_barcode = StockBarcode::select('id', 'product_id')->where('barcode_no', $barcode_no)->first();
            $product_id = $stock_barcode->product_id;
            $psBarcodeArr = array(
                'packingslip_id' => $packingslip_id,
                'product_id' => $product_id,
                'barcode_no' => $barcode_no,
                'created_at' => date('Y-m-d H:i:s')
            );
            PackingslipBarcode::insert($psBarcodeArr);
        }
    }
}
