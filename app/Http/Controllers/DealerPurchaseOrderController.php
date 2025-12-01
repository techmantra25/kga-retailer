<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

use App\Models\Dealer;
use App\Models\DealerPurchaseOrder;
use App\Models\DealerPurchaseOrderProduct;
use App\Models\DealerPurchaseOrderBarcode;

use App\Models\PackingslipBarcode;
use App\Models\StockBarcode;


class DealerPurchaseOrderController extends Controller
{
    //

    public function __construct(Request $request)
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            
            $accessDealerPurchaseOrder = userAccess(Auth::user()->role_id,16);
            if(!$accessDealerPurchaseOrder){
                abort(404);
            }           

            return $next($request);
        });
    }

    /*
    ** List Orders
    ** GET
    */   

    public function index(Request $request)
    {
        // dd('Hi');
        $search = !empty($request->search)?$request->search:'';
        $paginate = 10;      
        $data = DealerPurchaseOrder::select('*');
        $totalResult = DealerPurchaseOrder::select('id');

        
        
        if(!empty($search)){
            $data = $data->where(function($query) use ($search){
                $query->where('order_no', 'LIKE','%'.$search.'%')->orWhereHas('dealer', function ($dealer) use ($search) {
                    $dealer->where('name', 'LIKE','%'.$search.'%')->orWhere('phone', 'LIKE','%'.$search.'%')->orWhere('email', 'LIKE','%'.$search.'%');
                });
            });
            $totalResult = $totalResult->where(function($query) use ($search){
                $query->where('order_no', 'LIKE','%'.$search.'%')->orWhereHas('dealer', function ($dealer) use ($search) {
                    $dealer->where('name', 'LIKE','%'.$search.'%')->orWhere('phone', 'LIKE','%'.$search.'%')->orWhere('email', 'LIKE','%'.$search.'%');
                });
            });            
        }
        
        $data = $data->orderBy('id','desc')->paginate($paginate);
        $totalResult = $totalResult->count();

        $data = $data->appends([
            'search'=>$search,
            'page'=>$request->page
        ]);
        return view('dealer-purchase-order.list', compact('data','totalResult','search','paginate'));
    }

    /*
    ** Cancel Add Order Form
    ** GET
    */   

    public function create(Request $request)
    {
        // dd('Create');
        $dealer_id = !empty($request->dealer_id)?$request->dealer_id:'';
        $dealer = Dealer::where('status', 1)->orderBy('name')->get();
        return view('dealer-purchase-order.add', compact('dealer','dealer_id'));
    }

    /*
    ** Cancel Store Order
    ** POST
    */   

    public function store(Request $request)
    {
        $request->validate([
            'dealer_id' => 'required',           
            'details.*.product_id' => 'required',
            'details.*.quantity' => 'required',
            'details.*.product_price' => 'required|not_in:0'
        ],[
            'dealer_id.required' => 'Please choose dealer',            
            'details.*.product_id.required' => 'Please add product',
            'details.*.quantity.required' => 'Please add quantity',
            'details.*.product_price.required' => 'Please add rate',
            'details.*.product_price.not_in' => 'Please add price'
        ]);

        $params = $request->except('_token');

        // dd($params);

        $order_no = 'DLRPURORD'.genAutoIncreNoYearWiseOrder(4,'dealer_purchase_orders',date('Y'),date('m'));
        // dd($order_no);
        $orderData = array(
            'order_no' => $order_no,
            'dealer_id' => $params['dealer_id'],
            'created_by' => Auth::user()->id,
            'amount' => $params['order_amount_val'],
            'created_at' => date('Y-m-d H:i:s')
        );
        // echo '<pre>'; print_r($salesOrderData); die;
        $id = DealerPurchaseOrder::insertGetId($orderData);

        $details = $params['details'];
        foreach($details as $detail){
            $product_total_price = ($detail['quantity'] * $detail['product_price']);
            $orderProductData = array(
                'dealer_purchase_order_id' => $id,
                'product_id' => $detail['product_id'],
                'quantity' => $detail['quantity'],
                'cost_price' => $detail['product_price'],
                'total_price' => $product_total_price,
                'created_at' => date('Y-m-d H:i:s')
            );
            DealerPurchaseOrderProduct::insert($orderProductData);
        }

        Session::flash('message', 'Dealer Purchase Order Created Successfully');
        return redirect()->route('dealer-purchase-order.list');
    }

    /*
    ** Cancel Purchase Order
    ** GET
    */    
    public function cancel($idStr,$getQueryString='')
    {
        try {
            $id = Crypt::decrypt($idStr);
            DealerPurchaseOrder::where('id',$id)->update(['is_cancelled'=>1]);
            Session::flash('message', 'Order Cancelled Successfully');
            return redirect('/dealer-purchase-order/list?'.$getQueryString);
        } catch ( DecryptException $e) {
            return abort(404);
        }
        
    }


    /*
    ** View Details
    */

    public function show($idStr,$getQueryString='')
    {
        # View Details ...
        try{
            $id = Crypt::decrypt($idStr);
            $order = DealerPurchaseOrder::find($id);
            $data = DealerPurchaseOrderProduct::with('product')->where('dealer_purchase_order_id',$id)->get();
            return view('dealer-purchase-order.detail', compact('id','order','data'));
        } catch (DecryptException $e) {
            return abort(404);
        }
        
    }

    /*
    ** Upload CSV For GRN
    ** View Page
    ** GET
    */

    public function generate_grn($idStr,$getQueryString='')
    {
        try{
            $id = Crypt::decrypt($idStr);
            $order = DealerPurchaseOrder::find($id);
            $data = DealerPurchaseOrderProduct::with('product')->where('dealer_purchase_order_id',$id)->get();
            return view('dealer-purchase-order.upload-barcode-grn', compact('id','order','data','getQueryString') );
        } catch (DecryptException $e) {
            return abort(404);
        }
        
    }

    /*
    ** Submit CSV To GRN
    ** POST
    */

    public function save_grn(Request $request)
    {
        $request->validate([
            'csv' => 'required|file'
        ]);

        $params = $request->except('_token');
        $dealer_id = $params['dealer_id'];
        $csv = $params['csv'];

        
        $rows = Excel::toArray([],$request->file('csv'));
        $data = $rows[0];
        $barcodeArr = array();

        foreach($data as $item){
            $org_barcode_no = $item[0];
            $barcode_no = trim($org_barcode_no);

            $barcodeArr[] = $barcode_no;
            

            $checkBarcode = PackingslipBarcode::where('barcode_no',$barcode_no)->first();
            // dd($checkBarcode->packingslip->sales_order->dealer_id);
            if(!empty($checkBarcode)){
                if($checkBarcode->packingslip->sales_order->dealer_id != $dealer_id){
                    return redirect()->back()->withErrors(['csv' => 'The barcode no '. $barcode_no.' is not from this dealer !!!'])->withInput();
                }
                $checkRepeatedOrderBarcode = DealerPurchaseOrderBarcode::where('barcode_no', $barcode_no)->first();

                if(!empty($checkRepeatedOrderBarcode)){
                    if($checkRepeatedOrderBarcode->order->dealer_id == $dealer_id){
                        return redirect()->back()->withErrors(['csv' => 'This item is repeating for this dealer order. '])->withInput(); 
                    }
                }

                // dd($checkRepeatedOrderBarcode);
            } else {
                return redirect()->back()->withErrors(['csv' => $barcode_no.' not found, Please check !!!'])->withInput(); 
            }


        }

        $barcode_pro = PackingslipBarcode::selectRaw('product_id, count(product_id) AS count_product')->whereIn('barcode_no',$barcodeArr)->groupBy('product_id')->get()->toArray();

        // dd($barcode_pro);

        $proIdArr = array();
        $dealer_purchase_order = DealerPurchaseOrderProduct::where('dealer_purchase_order_id',$params['id'])->get()->toArray();
        foreach($dealer_purchase_order as $order){
            $proIdArr[] = $order['product_id'];
        }

        

        if(!empty($barcode_pro)){
            foreach($barcode_pro as $pro){
                $dealer_purchase_order_product = DealerPurchaseOrderProduct::where('dealer_purchase_order_id',$params['id'])->where('product_id',$pro['product_id'])->first();

                if(!in_array($pro['product_id'],$proIdArr)){
                    return redirect()->back()->withErrors(['csv' => 'Unknown Product Barcode Found In CSV Which Are Not Required Here'])->withInput(); 
                }
                if($dealer_purchase_order_product->quantity != $pro['count_product']){
                    return redirect()->back()->withErrors(['csv' => 'Mismatched Product Barcode Quantity With Order, Please check'])->withInput(); 
                }
                
            }
        }

        ### DealerPurchaseOrderBarcode Entry ###

        foreach($barcodeArr as $barcode_no){
            $stockBarcode = StockBarcode::where('barcode_no', $barcode_no)->first();
            $product_id = $stockBarcode->product_id;

            $dealerPurchaseOrderBarcodeArr = array(
                'dealer_purchase_order_id' => $params['id'],
                'product_id' => $product_id,
                'barcode_no' => $barcode_no,
                'created_at' => date('Y-m-d H:i:s')
            );

            DealerPurchaseOrderBarcode::insert($dealerPurchaseOrderBarcodeArr);
        }

        ### Stock Barcode Revert ###
        StockBarcode::whereIn('barcode_no', $barcodeArr)->update(['packingslip_id' => null, 'is_stock_out'=>0]);
        ### Change Status Goods In Order ###
        DealerPurchaseOrder::where('id',$params['id'])->update([
            'is_goods_in' => 1,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        $dealer_purchase_order_product = DealerPurchaseOrderProduct::where('dealer_purchase_order_id',$params['id'])->get();

        foreach($dealer_purchase_order_product as $item){
            updateStockInvetory($item['product_id'],$item['quantity'],'in',$params['id'],'dealer_purchase_order');
        }

        // dd($barcodeArr);

        Session::flash('message', 'Goods Received Successfully');
        return redirect()->route('dealer-purchase-order.list');        

    }

    /*
    ** View Barcodes
    ** GET
    */

    public function barcodes(Request $request,$idStr)
    {
        # View Barcode ...
        try{
            $search = !empty($request->search)?$request->search:'';
            $id = Crypt::decrypt($idStr);
            $data = DealerPurchaseOrderBarcode::with('product')->where('dealer_purchase_order_id',$id);
            $totalData = DealerPurchaseOrderBarcode::with('product')->where('dealer_purchase_order_id',$id);
            
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
            $order = DealerPurchaseOrder::find($id);
            
            $order_no = $order->order_no;
            return view('dealer-purchase-order.barcodes', compact('data','totalData','id','order_no','search'));
        } catch (DecryptException $e) {
            return abort(404);
        }
        
    }

    /*
    ** Download Barcodes
    ** GET
    */

    public function barcode_csv(Request $request,$idStr)
    {
        # Download Barcodes ...
        try{
            $search = !empty($request->search)?$request->search:'';
            $id = Crypt::decrypt($idStr);
            $data = DealerPurchaseOrderBarcode::where('dealer_purchase_order_id',$id);
            
            if(!empty($search)){
                $data = $data->where(function($q) use ($search){
                    $q->where('barcode_no','LIKE', '%'.$search.'%')->orWhereHas('product', function ($product) use ($search) {
                        $product->where('title', 'LIKE','%'.$search.'%');
                    });
                });
            }

            $data = $data->pluck('barcode_no')->toArray();
            // dd($data);
            $order = DealerPurchaseOrder::find($id);
            $order_no = $order->order_no;

            $fileName = "Barcodes-Dealer-Purchase-Order-".$order_no.".csv";
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


}
