<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderProduct;
use App\Models\PurchaseOrderBarcode;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Stock;
use App\Models\StockProduct;
use App\Models\StockBarcode;
use App\Models\PurchaseOrderRemoveItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Encryption\DecryptException;
use Barryvdh\DomPDF\Facade\Pdf;

class PurchaseOrderController extends Controller
{
    public function __construct(Request $request)
    {
        $this->middleware('auth');
        // $this->middleware(function ($request, $next) {
            
        //     $accessPO = userAccess(Auth::user()->role_id,9);
        //     if(!$accessPO){
        //         abort(404);
        //     }          

        //     return $next($request);
        // });
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = !empty($request->search)?$request->search:'';
        $status = !empty($request->status)?$request->status:'';
        $type = !empty($request->type)?$request->type:'';
        $paginate = !empty($request->paginate)?$request->paginate:25;
        $po_type = !empty($request->po_type)?$request->po_type:'po';

        $page = $request->page;
        if(!is_numeric($page)){
            $page = 1;
        }
        if(!is_numeric($paginate)){
            $paginate = 25;
        }
        
        $total = PurchaseOrder::count();
        
        $data = PurchaseOrder::select('*');
        $totalResult = PurchaseOrder::select('id');
        if($po_type == 'grn'){
            return redirect()->route('grn.index'); 
            // $data = $data->where('status', 2);
            // $totalResult = $totalResult->where('status', 2);
        } 
        
        if(!empty($search)){
            $data = $data->where(function($query) use ($search){
                $query->where('order_no', 'LIKE','%'.$search.'%')->orWhereHas('supplier', function ($supplier) use ($search) {
                    $supplier->where('public_name', 'LIKE','%'.$search.'%');
                });
            });
            $totalResult = $totalResult->where(function($query) use ($search){
                $query->where('order_no', 'LIKE','%'.$search.'%')->orWhereHas('supplier', function ($supplier) use ($search) {
                    $supplier->where('public_name', 'LIKE','%'.$search.'%');
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
        
        $data = $data->orderBy('id','desc')->paginate($paginate);
        $totalResult = $totalResult->count();

        $data = $data->appends([
            'search'=>$search,
            'type' => $type,
            'po_type' => $po_type,
            'status' => $status,
            'page'=>$page,
            'paginate'=>$request->paginate
        ]);

        // dd($data);
        return view('purchaseorder.list', compact('data','totalResult','total','search','type','status','po_type','paginate'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $supplier_id = !empty($request->supplier_id)?$request->supplier_id:'';
        $type = !empty($request->type)?$request->type:'';
        $supplier = Supplier::where('status', 1)->orderBy('name','asc')->get();
        // if(isset($request->supplier_id) && isset($request->type)){
        //     $request->validate([
        //         'supplier_id' => 'required',
        //         'type' => 'required'
        //     ]);
        // }
        return view('purchaseorder.add',compact('supplier','supplier_id','type'));
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
            'supplier_id' => 'required|exists:suppliers,id',
            'type' => 'required|in:sp,fg',
            'details.*.product_id' => 'required',
            'details.*.quantity' => 'required',
            'details.*.pack_of' => 'required_if:type,sp',
            'details.*.quantity_in_pack' => 'required_if:type,sp',
            'details.*.cost_price' => 'required',
            'details.*.hsn_code' => 'required',
            'details.*.mrp' => 'required',
            'details.*.tax' => 'required'
        ],[
            'details.*.product_id.required' => 'Please add product',
            'details.*.quantity.required' => 'Please add quantity',
            'details.*.cost_price.required' => 'Please add cost price',
            'details.*.mrp.required' => 'Please add MRP',
            'details.*.tax.required' => 'Please add Tax',
            'details.*.hsn_code.required' => 'Please add HSN'
        ]);

        $params = $request->except('_token');
        $order_no = 'PO'.genAutoIncreNoYearWiseOrder(4,'purchase_orders',date('Y'),date('m'));
        $params['order_no'] = $order_no;
        $details = $params['details'];

        $totalQty = 0;
        $maxQty = 500;
        
        foreach($details as $detail){
            $qty = $detail['quantity'];
            if($params['type'] == 'sp'){
                $qty = $detail['pack_of'];
            }
            $totalQty += $qty;
            
        }

        // dd($totalQty);
        $last_pro = count($details);

        if($totalQty > $maxQty){
             

            return  redirect()->back()->withErrors([
                'max_qty_validation1'=> "Total item quantity of this order is ".$totalQty.". Please add total product quantity within ".$maxQty.". ",
                'max_qty_validation2'=> $totalQty." barcode creation will make problem through system once at a time. Please split your quantity with next order. ",
                ])->withInput(); 
        }

                
        $purchaseOrderData = array(
            'created_by' => Auth::user()->id,
            'order_no' => $order_no,
            'supplier_id' => $params['supplier_id'],
            'type' => $params['type'],
            'details' => json_encode($params['details']),
            'created_at' => date('Y-m-d H:i:s')
        );
        $id = PurchaseOrder::insertGetId($purchaseOrderData);

        
        $total_amount = 0;
        foreach($details as $detail){
            
            $pack_of = 1;
            $quantity_in_pack = $detail['quantity'];
            if($params['type'] == 'sp'){
                $pack_of = $detail['pack_of'];
                $quantity_in_pack = $detail['quantity_in_pack'];
            }

            $purchaseOrderProductData = array(
                'purchase_order_id' => $id,
                'product_id' => $detail['product_id'],
                'pack_of' => $pack_of,
                'quantity_in_pack' => $quantity_in_pack,
                'quantity' => $detail['quantity'],
                'cost_price' => $detail['cost_price'],
                'total_price' => $detail['total_price'],
                'mrp' => $detail['mrp'],
                'tax' => $detail['tax'],
                'hsn_code' => $detail['hsn_code'],
                'created_at' => date('Y-m-d H:i:s')
            );
            $total_amount += $detail['total_price'];
            PurchaseOrderProduct::insert($purchaseOrderProductData);

            $quantity = $detail['quantity'];
            if($params['type'] == 'sp'){
                $quantity = $detail['pack_of'];
            }

            for($i=0; $i<$quantity;$i++){
                // $barcodeGenerator = barcodeGenerator();
                $barcodeGenerator = genAutoIncreNoBarcode($detail['product_id'],date('Y'));
                $barcode_no = $barcodeGenerator['barcode_no'];
                $code_html = $barcodeGenerator['code_html'];
                $code_base64_img = $barcodeGenerator['code_base64_img'];
                $purchaseOrderBarcodeData = array(
                    'purchase_order_id' => $id,
                    'product_id' => $detail['product_id'],
                    'barcode_no' => $barcode_no,
                    'code_html' => $code_html,
                    'code_base64_img' => $code_base64_img,
                    'created_at' => date('Y-m-d H:i:s')
                );
                PurchaseOrderBarcode::insert($purchaseOrderBarcodeData);
            }

        }

        PurchaseOrder::where('id',$id)->update(['amount'=>$total_amount]);

        Session::flash('message', 'Purchase Order Created Successfully');
        return redirect()->route('purchase-order.list');
    }
    
    
    /*
    ** Edit Form Purchase Order
    */ 

    public function edit(Request $request,$idStr,$getQueryString='')
    {

        try{
            $id = Crypt::decrypt($idStr);
            $order = PurchaseOrder::find($id);
            $data = PurchaseOrderProduct::with('product')->where('purchase_order_id',$id)->get();
            return view('purchaseorder.edit', compact('id','order','data','idStr','getQueryString'));
        } catch (DecryptException $e) {
            return abort(404);
        }

    }

    public function update(Request $request,$idStr,$getQueryString='')
    {
        try{
            $id = Crypt::decrypt($idStr);            
            $params = $request->except('_token');
            $details = $params['details'];
            
            $total_amount = 0;
            foreach($details as $detail){                   
                $purchaseOrderProductData = array(                    
                    'cost_price' => $detail['cost_price'],
                    'total_price' => $detail['total_price'],
                    'mrp' => $detail['mrp'],
                    'updated_at' => date('Y-m-d H:i:s')
                );
                $total_amount += $detail['total_price'];
                PurchaseOrderProduct::where('purchase_order_id',$id)->where('product_id',$detail['product_id'])->update($purchaseOrderProductData);
            }    
            PurchaseOrder::where('id',$id)->update(['amount'=>$total_amount]);

            $browser_name = isset($params['browser_name'])?($params['browser_name']):NULL;
            $navigator_useragent = isset($params['navigator_useragent'])?$params['navigator_useragent']:NULL;
            unset($params['browser_name']);
            unset($params['navigator_useragent']);


            addChangeLog(Auth::user()->id,$request->ip(),'edit_po',$browser_name,$navigator_useragent,$params);
    
            Session::flash('message', 'Purchase Order Updated Successfully');
            return redirect('/purchase-order/list?'.$getQueryString);

        } catch (DecryptException $e) {
            return abort(404);
        }

       
    }




    /*
    ** Cancel Purchase Order
    */    
    public function cancel($idStr,$getQueryString='')
    {
        try {
            $id = Crypt::decrypt($idStr);
            PurchaseOrder::where('id',$id)->update(['status'=>3,'updated_at'=>date('Y-m-d H:i:s')]);
            Session::flash('message', 'Purchase Order Cancelled Successfully');
            return redirect('/purchase-order/list?'.$getQueryString);
        } catch ( DecryptException $e) {
            return abort(404);
        }
    }

    /*
    ** View Barcodes
    */

    public function make_grn(Request $request,$idStr,$getQueryString=''){
        try {
            $id = Crypt::decrypt($idStr);
            $search = !empty($request->search)?$request->search:'';
            $goods_in_type = !empty($request->goods_in_type)?$request->goods_in_type:'';
            $purchaseorder = PurchaseOrder::find($id);
            $type = $purchaseorder->type;
            $order_no = $purchaseorder->order_no;
            $data = PurchaseOrderBarcode::with('product')->where('purchase_order_id',$id)->where('is_archived', 0)->where('is_stock_in', 0);  
                  
            if(!empty($search)){
                $data = $data->where(function($q) use ($search){
                    $q->where('barcode_no','LIKE', '%'.$search.'%')->orWhereHas('product', function ($product) use ($search) {
                        $product->where('title', 'LIKE','%'.$search.'%');
                    });
                });
            }
            $data = $data->orderBy('barcode_no','asc')->get()->groupBy('product.id');
            // dd($data);
            if(!empty($goods_in_type)){
                PurchaseOrder::where('id',$id)->update([
                    'goods_in_type' => $goods_in_type
                ]);
            }

            $count_scanned = PurchaseOrderBarcode::where('purchase_order_id',$id)->where('is_archived', 0)->where(function($sc){
                $sc->where('is_scanned', 1)->orWhere('is_bulk_scanned', 0);
            })->count();

            // $count_scanned = PurchaseOrderBarcode::where('purchase_order_id',$id)->where('is_archived', 0)->where('is_scanned', 1)->orWhere('is_bulk_scanned', 1)->count();
            $count_barcodes = PurchaseOrderBarcode::where('purchase_order_id',$id)->where('is_archived', 0)->count();
            
            return view('purchaseorder.makegrn', compact('id','idStr','order_no','getQueryString','data','search','goods_in_type','type','count_scanned','count_barcodes'));
        } catch ( DecryptException $e) {
            return abort(404);
        }        
    }

    /*
    ** Generate GRN
    */

    // public function generategrn(Request $request)
    // {
		
    //     # generate GRN... 
    //     $request->validate([
    //         'barcode_no' => 'required'
    //     ],[
    //         'barcode_no.required' => 'Please add barcode'
    //     ]);      

    //     $params = $request->except('_token');
    //     $purchase_order_id = $params['id'];
    //     $grn_no = 'GRN'.genAutoIncreNoYearWiseOrder(4,'stock',date('Y'),date('m'));
    //     $params['grn_no'] = $grn_no;
    //     $purchaseorder = PurchaseOrder::find($purchase_order_id);
    //     // dd($params);
    //     $barcode_no = $params['barcode_no'];        
    //     // dd($params['barcode_no']);        
    //     $stock_id = Stock::insertGetId([
    //         'purchase_order_id'=>$purchase_order_id,
    //         'grn_no' => $grn_no,
    //         'goods_in_type' => $purchaseorder->goods_in_type,
    //         'created_at' => date('Y-m-d H:i:s'),
    //         'updated_at' => date('Y-m-d H:i:s')
    //     ]);

    //     foreach($barcode_no as $barcode){
    //         $getBarcodeDetails = getBarcodeDetails($barcode);
    //         $stockBoxArr = array(
    //             'stock_id' => $stock_id,
    //             'product_id' => $getBarcodeDetails['product_id'],
    //             'barcode_no' => $barcode,
    //             'code_html' => $getBarcodeDetails['code_html'],
    //             'code_base64_img' => $getBarcodeDetails['code_base64_img'],
    //             'created_at' => date('Y-m-d H:i:s')
    //         );
    //         StockBarcode::insert($stockBoxArr);
    //     }
        
    //     PurchaseOrderBarcode::whereIn('barcode_no', $barcode_no)->update(['is_stock_in' => 1]);
    //     PurchaseOrder::where('id',$purchase_order_id)->update(['is_goods_in'=>1]);

    //     ### Change Log ###
    //     $browser_name = isset($params['browser_name'])?($params['browser_name']):NULL;
    //     $navigator_useragent = isset($params['navigator_useragent'])?$params['navigator_useragent']:NULL;
    //     unset($params['browser_name']);
    //     unset($params['navigator_useragent']);        
    //     addChangeLog(Auth::user()->id,$request->ip(),'grn',$browser_name,$navigator_useragent,$params);
    //     ### Set Stock Product ###
    //     $this->setStockProduct($params['id'],$stock_id,$grn_no,$barcode_no);
    //     Session::flash('message', 'Goods Received Note Created Successfully');
    //     return redirect()->route('grn.index');
    // }

    public function generategrn(Request $request)
    {
        # generate GRN... 
        $request->validate([
            'barcode_no' => 'required'
        ],[
            'barcode_no.required' => 'Please add barcode'
        ]);      

        $params = $request->except('_token');
        $purchase_order_id = $params['id'];
        $grn_no = 'GRN'.genAutoIncreNoYearWiseOrder(4,'stock',date('Y'),date('m'));
        $params['grn_no'] = $grn_no;
		DB::beginTransaction();
		try {
			$purchaseorder = PurchaseOrder::find($purchase_order_id);
			// dd($params);
			$barcode_no = $params['barcode_no'];        
			// dd($params['barcode_no']);        
			$stock_id = Stock::insertGetId([
				'purchase_order_id'=>$purchase_order_id,
				'grn_no' => $grn_no,
				'goods_in_type' => $purchaseorder->goods_in_type,
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s')
			]);

			foreach($barcode_no as $barcode){
				$getBarcodeDetails = getBarcodeDetails($barcode);
				$stockBoxArr = array(
					'stock_id' => $stock_id,
					'product_id' => $getBarcodeDetails['product_id'],
					'barcode_no' => $barcode,
					'code_html' => $getBarcodeDetails['code_html'],
					'code_base64_img' => $getBarcodeDetails['code_base64_img'],
					'created_at' => date('Y-m-d H:i:s')
				);
				StockBarcode::insert($stockBoxArr);
			}

			PurchaseOrderBarcode::whereIn('barcode_no', $barcode_no)->update(['is_stock_in' => 1]);
			PurchaseOrder::where('id',$purchase_order_id)->update(['is_goods_in'=>1]);

			### Change Log ###
			$browser_name = isset($params['browser_name'])?($params['browser_name']):NULL;
			$navigator_useragent = isset($params['navigator_useragent'])?$params['navigator_useragent']:NULL;
			unset($params['browser_name']);
			unset($params['navigator_useragent']);        
			addChangeLog(Auth::user()->id,$request->ip(),'grn',$browser_name,$navigator_useragent,$params);
			### Set Stock Product ###
			$this->setStockProduct($params['id'],$stock_id,$grn_no,$barcode_no);
			 DB::commit();
			Session::flash('message', 'Goods Received Note Created Successfully');
			return redirect()->route('grn.index');
		} catch (\Exception $e) {
			// Rollback transaction in case of error
			DB::rollBack();
			Session::flash('error', 'Failed to create Goods Received Note: ' . $e->getMessage());
			return redirect()->back();
		}
    }

    private function setStockProduct($purchase_order_id,$stock_id,$grn_no,$barcodes){
        ## Set Stock Product Quanties and Prices Against Barcodes

        $stock_boxes = StockBarcode::select('product_id')->selectRaw("COUNT(barcode_no) AS quantity")->whereIn('barcode_no',$barcodes)->groupBy('product_id')->get()->toArray();

        $totalPriceSum = 0;
        $product_ids = '';
        $proids = array();
        foreach($stock_boxes as $stock_product){
            $proids[] = $stock_product['product_id'];
            $purchase_order_product = PurchaseOrderProduct::where('purchase_order_id',$purchase_order_id)->where('product_id',$stock_product['product_id'])->first();
            
            $cost_price = $purchase_order_product->cost_price;
            $quantity = $stock_product['quantity'];
            $total_price = ($quantity * $cost_price);
            $totalPriceSum += $total_price;
            $stockProArr = array(
                'stock_id' => $stock_id,
                'product_id' => $stock_product['product_id'],
                'count' => $stock_product['quantity'],
                'cost_price' => $cost_price,
                'total_price' => $total_price,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            );
            StockProduct::insert($stockProArr);

            updateStockInvetory($stock_product['product_id'],$stock_product['quantity'],'in',$purchase_order_id,'purchase_order');

            ## Update Product Master Last PO Cost Price
            Product::where('id', $stock_product['product_id'])->update([
                'last_po_cost_price' => $cost_price
            ]);

        }
        $stockUpdateArr = array(
            'amount' => $totalPriceSum,
            'updated_at' => date('Y-m-d H:i:s')
        );
        Stock::where('id',$stock_id)->update($stockUpdateArr);

        

        $total_box = PurchaseOrderBarcode::where('purchase_order_id',$purchase_order_id)->where('is_archived', 0)->count();
        $total_scanned_box = PurchaseOrderBarcode::where('purchase_order_id',$purchase_order_id)->where('is_archived', 0)->where('is_stock_in', 1)->count();

        if($total_box == $total_scanned_box){
            PurchaseOrder::where('id',$purchase_order_id)->update([
                'status' => 2
            ]);
        }

    }

    /*
    ** Remove Item From Make GRN Page
    */

    public function remove_item(Request $request,$id,$product_id)
    {
        

        $purchaseorder = PurchaseOrder::find($id);
        $amount = $purchaseorder->amount;
        $purchase_order_product = PurchaseOrderProduct::where('purchase_order_id',$id)->where('product_id',$product_id)->first();
        $params['purchase_order_id'] = $id;
        $params['product_id'] = $product_id;
        $params['cost_price'] = $purchase_order_product->cost_price;
        $params['total_price'] = $purchase_order_product->total_price;
        $params['pack_of'] = $purchase_order_product->pack_of;
        $params['quantity_in_pack'] = $purchase_order_product->quantity_in_pack;
        $params['quantity'] = $purchase_order_product->quantity;
        $params['removed_by'] = Auth::user()->id;
        $params['created_at'] = date('Y-m-d H:i:s');
        PurchaseOrderRemoveItem::insert($params);

        $new_amount = ($amount - $params['total_price']);
        // dd($new_amount);
        PurchaseOrder::where('id',$id)->update(['amount'=>$new_amount]);
        PurchaseOrderProduct::where('purchase_order_id',$id)->where('product_id',$product_id)->delete();
        PurchaseOrderBarcode::where('purchase_order_id',$id)->where('product_id',$product_id)->delete();

        Session::flash('message', 'Items Permanently Removed From Order Successfully');
        return redirect()->route('purchase-order.make-grn', [Crypt::encrypt($id),'goods_in_type'=>$purchaseorder->goods_in_type]);

        // dd($id.' '.$product_id);
    }

    /*
    ** View GRN
    */

    public function viewgrn(Request $request,$idStr)
    {
        # view GRN...
        try{
            $id = Crypt::decrypt($idStr);
            $data = PurchaseOrderProduct::where('purchase_order_id',$id)->get();
            foreach($data as $product){
                $barcodes = PurchaseOrderBarcode::where('product_id',$product->product_id)->get();
                $product->barcodes = $barcodes;
            }
            return view('purchaseorder.viewgrn', compact('data','id'));
        } catch (DecryptException $e) {
            return abort(404);
        }
        
    }

    /*
    ** View Barcode
    */

    public function barcodes(Request $request,$idStr)
    {
        # View Barcode ...
        try{
            $search = !empty($request->search)?$request->search:'';
            $id = Crypt::decrypt($idStr);
            $data = PurchaseOrderBarcode::with('product')->where('purchase_order_id',$id)->where('is_archived', 0);
            $totalData = PurchaseOrderBarcode::with('product')->where('purchase_order_id',$id)->where('is_archived', 0);
            
            if(!empty($search)){
                $data = $data->where(function($q) use ($search){
                    $q->where('barcode_no','LIKE', '%'.$search.'%')->orWhereHas('product', function ($product) use ($search) {
                        $product->where('title', 'LIKE','%'.$search.'%');
                    });
                });
                $totalData = $totalData->where(function($q) use ($search){
                    $q->where('barcode_no','LIKE', '%'.$search.'%')->orWhereHas('product', function ($product) use ($search) {
                        $product->where('title', 'LIKE','%'.$search.'%');
                    });
                });
            }
            
            $data = $data->get();
            $totalData = $totalData->count();
            $po = PurchaseOrder::find($id);
            $status = $po->status;
            $order_no = $po->order_no;
            return view('purchaseorder.barcodes', compact('data','totalData','id','status','order_no','search'));
        } catch (DecryptException $e) {
            return abort(404);
        }
        
    }

    /*
    ** Download Barcodes
    */

    public function barcode_csv(Request $request,$idStr)
    {
        # Download Barcodes ...
        try{
            $search = !empty($request->search)?$request->search:'';
            $id = Crypt::decrypt($idStr);
            $data = PurchaseOrderBarcode::where('purchase_order_id',$id);
            
            if(!empty($search)){
                $data = $data->where(function($q) use ($search){
                    $q->where('barcode_no','LIKE', '%'.$search.'%')->orWhereHas('product', function ($product) use ($search) {
                        $product->where('title', 'LIKE','%'.$search.'%');
                    });
                });
            }

            $data = $data->pluck('barcode_no')->toArray();
            // dd($data);
            $po = PurchaseOrder::find($id);
            $status = $po->status;
            $order_no = $po->order_no;

            $fileName = "Barcodes-Purchase-Order-".$order_no.".csv";
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

            
        } catch (DecryptException $e) {
            return abort(404);
        }
        
    }

    public function barcode_number_pdf(Request $request,$idStr)
    {
        # code...
        try{
            $search = !empty($request->search)?$request->search:'';
            $id = Crypt::decrypt($idStr);
            $data = PurchaseOrderBarcode::where('purchase_order_id',$id);
            
            if(!empty($search)){
                $data = $data->where(function($q) use ($search){
                    $q->where('barcode_no','LIKE', '%'.$search.'%')->orWhereHas('product', function ($product) use ($search) {
                        $product->where('title', 'LIKE','%'.$search.'%');
                    });
                });
            }

            $data = $data->pluck('barcode_no')->toArray();
            // dd($data);
            $po = PurchaseOrder::find($id);
            $status = $po->status;
            $order_no = $po->order_no;

            // dd($order_no);
            $pdf = Pdf::loadView('purchaseorder.number-pdf', compact('data','id','order_no'));
            return $pdf->download($order_no.'.pdf');

            
        } catch (DecryptException $e) {
            return abort(404);
        }
    }

    /*
    ** View PO Details
    */

    public function show($idStr,$getQueryString='')
    {
        # View PO Details ...
        try{
            $id = Crypt::decrypt($idStr);
            $order = PurchaseOrder::find($id);
            $data = PurchaseOrderProduct::with('product')->where('purchase_order_id',$id)->get();
            return view('purchaseorder.detail', compact('id','order','data'));
        } catch (DecryptException $e) {
            return abort(404);
        }
        
    }

    /*
    ** Archive PO Box / Barcode 
    */

    public function archive(Request $request,$type,$id,$product_id,$barcode_no,$goods_in_type='',$getQueryString='')
    {
        # Archive PO Box / Barcode...
        
        # Purchase Order Product - Decrease Single Quantity , Decrease Single Total Price

        $purchaseorderproduct = PurchaseOrderProduct::where('purchase_order_id',$id)->where('product_id',$product_id)->first();

        $quantity = $purchaseorderproduct->quantity;
        $cost_price = $purchaseorderproduct->cost_price;
        $latestquantity = ($quantity - 1);
        $total_price = ($latestquantity * $cost_price);
        

        $pack_of = 1;
        if($type == 'sp'){
            $pack_of = $latestquantity;
        }
        $purchaseorderproductUpdateArr = array(
            'quantity' => $latestquantity,
            'pack_of' => $pack_of,
            'total_price' => $total_price
        );
        PurchaseOrderProduct::where('purchase_order_id',$id)->where('product_id',$product_id)->update($purchaseorderproductUpdateArr);

        # Set Archive Status In Barcode

        $purchaseOrderBarcodeArr = array(
            'is_archived' => 1,
            'archived_at' => date('Y-m-d H:i:s')
        );
        PurchaseOrderBarcode::where('purchase_order_id',$id)->where('product_id',$product_id)->where('barcode_no',$barcode_no)->update($purchaseOrderBarcodeArr);

        # Purchase Order Amount Update

        $newamount = PurchaseOrderProduct::where('purchase_order_id',$id)->sum('total_price');
        // dd($newamount);
        PurchaseOrder::where('id',$id)->update(['amount'=>$newamount]);
        Session::flash('message', $barcode_no.' archived successfully');
        return redirect()->route('purchase-order.make-grn', [Crypt::encrypt($id),$getQueryString.'?goods_in_type='.$goods_in_type] );
    }

    public function archived(Request $request,$idStr){
        # View Archived...
        try{
            $search = !empty($request->search)?$request->search:'';
            $id = Crypt::decrypt($idStr);
            $data = PurchaseOrderBarcode::with('product')->where('purchase_order_id',$id)->where('is_archived', 1);
            $totalData = PurchaseOrderBarcode::with('product')->where('purchase_order_id',$id)->where('is_archived', 1);
            
            if(!empty($search)){
                $data = $data->where(function($q) use ($search){
                    $q->where('barcode_no','LIKE', '%'.$search.'%')->orWhereHas('product', function ($product) use ($search) {
                        $product->where('title', 'LIKE','%'.$search.'%');
                    });
                });
                $totalData = $totalData->where(function($q) use ($search){
                    $q->where('barcode_no','LIKE', '%'.$search.'%')->orWhereHas('product', function ($product) use ($search) {
                        $product->where('title', 'LIKE','%'.$search.'%');
                    });
                });
            }
            
            $data = $data->get();
            $totalData = $totalData->count();
            $po = PurchaseOrder::find($id);
            $status = $po->status;
            $order_no = $po->order_no;
            return view('purchaseorder.archived', compact('data','totalData','id','status','order_no','search'));
        } catch (DecryptException $e) {
            return abort(404);
        }
    }

    public function bulk_archive(Request $request){
        
        foreach($request->data as $key=>$item){
            $product_id = $item['product_id'];
            $type = $item['type'];
            $id = $item['id'];
            $barcode_no = $item['barcode_no'];
            # Archive PO Box / Barcode...
            # Purchase Order Product - Decrease Single Quantity , Decrease Single Total Price

            $purchaseorderproduct = PurchaseOrderProduct::where('purchase_order_id',$id)->where('product_id',$product_id)->first();

            $quantity = $purchaseorderproduct->quantity;
            $cost_price = $purchaseorderproduct->cost_price;
            $latestquantity = ($quantity - 1);
            $total_price = ($latestquantity * $cost_price);
            

            $pack_of = 1;
            if($type == 'sp'){
                $pack_of = $latestquantity;
            }
            $purchaseorderproductUpdateArr = array(
                'quantity' => $latestquantity,
                'pack_of' => $pack_of,
                'total_price' => $total_price
            );
            PurchaseOrderProduct::where('purchase_order_id',$id)->where('product_id',$product_id)->update($purchaseorderproductUpdateArr);

            # Set Archive Status In Barcode

            $purchaseOrderBarcodeArr = array(
                'is_archived' => 1,
                'archived_at' => date('Y-m-d H:i:s')
            );
            PurchaseOrderBarcode::where('purchase_order_id',$id)->where('product_id',$product_id)->where('barcode_no',$barcode_no)->update($purchaseOrderBarcodeArr);

            # Purchase Order Amount Update

            $newamount = PurchaseOrderProduct::where('purchase_order_id',$id)->sum('total_price');
            // dd($newamount);
            PurchaseOrder::where('id',$id)->update(['amount'=>$newamount]);
        }
        
        Session::flash('message', 'Archived successfully');
        return response()->json(['status'=>200]);
    }
}
