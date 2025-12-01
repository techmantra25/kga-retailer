<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\StockProduct;
use App\Models\StockInventory;
use App\Models\StockLog;
use App\Models\Product;
use App\Models\Category;
use App\Models\CustomerPointService;
use App\Models\DapService;
use App\Models\KgaSalesData;
use App\Models\StockBarcode;
use App\Models\SalesOrderProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class StockController extends Controller
{
    public function __construct(Request $request)
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {

            $accessProductManagement = userAccess(Auth::user()->role_id,8);
            
            if(!$accessProductManagement){
                abort(404);
            }            

            return $next($request);
        });
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = !empty($request->search)?$request->search:'';
        $search_barcode = !empty($request->search_barcode)?$request->search_barcode:'';
        $type = !empty($request->type)?$request->type:'';
        $cat_id = !empty($request->cat_id)?$request->cat_id:'';
        $subcat_id = !empty($request->subcat_id)?$request->subcat_id:'';
        $ordering = 'desc';
        $paginate = !empty($request->paginate)?$request->paginate:25;

        $category = Category::select('id','name')->whereNull('parent_id');
        $sub_category = Category::select('id','name');

        $stock_barodes = StockBarcode::with('product')->where('is_stock_out',0)->limit(25)->get();
        $stock_barodes_count = count($stock_barodes);
        $stock_damage_barcodes = StockBarcode::with('product')->where('is_stock_out',0)->where('is_damage',1)->get();
        $stock_damage_barcodes_count = count($stock_damage_barcodes);

        $data = StockInventory::with('product');
        $totalResult = StockInventory::with('product');
        $data = $data->where(function($q) use ($search){
            $q->orWhereHas('product', function ($q) use ($search) {
                $q->where('title', 'LIKE','%'.$search.'%')->orWhere('unique_id', 'LIKE', '%'.$search.'%');
            });
        });
        $totalResult = $totalResult->where(function($q) use ($search){
            $q->orWhereHas('product', function ($q) use ($search) {
                $q->where('title', 'LIKE','%'.$search.'%')->orWhere('unique_id', 'LIKE', '%'.$search.'%');
            });
        });

        if(!empty($type)){
            $data = $data->where(function($q_type) use ($type) {
                $q_type->orWhereHas('product', function($q_type) use ($type){
                    $q_type->where('type', '=', $type);
                });
            });
            $totalResult = $totalResult->where(function($q_type) use ($type) {
                $q_type->orWhereHas('product', function($q_type) use ($type){
                    $q_type->where('type', '=', $type);
                });
            });

            $category = $category->where('product_type', $type);
            $sub_category = $sub_category->where('product_type', $type)->where('parent_id',$cat_id);
        }

        if(!empty($cat_id)){
            $data = $data->whereHas('product', function($c) use($cat_id){
                $c->where('cat_id',$cat_id);
            });
            $totalResult = $totalResult->whereHas('product', function($c) use($cat_id){
                $c->where('cat_id',$cat_id);
            });

        }
        if(!empty($subcat_id)){

            $data = $data->whereHas('product', function($c) use($subcat_id){
                $c->where('subcat_id',$subcat_id);
            });
            $totalResult = $totalResult->whereHas('product', function($c) use($subcat_id){
                $c->where('subcat_id',$subcat_id);
            });

        }
        if(!empty($search_barcode)){
            $data = StockBarcode::with('product')->where('barcode_no',$search_barcode)->first();
            return view('stock.defective-by-barcode', compact('data'));
        }


        
        $data = $data->orderBy('id', 'desc')->paginate($paginate);
        $totalResult = $totalResult->count();

        $category = $category->where('status', 1)->orderBy('name')->get();
        $sub_category = $sub_category->where('status', 1)->orderBy('name')->get();
        
        $data = $data->appends([
            'page' => $request->page,
            'search' => $search,
            'search_barcode' => $search_barcode,
            'type' => $type,
            'paginate'=>$paginate
        ]);
        return view('stock.list', compact('data','totalResult','search','type','paginate','cat_id','subcat_id','category','sub_category','stock_barodes_count','stock_damage_barcodes_count','search_barcode'));
    }

    public function logs(Request $request,$product_idStr,$getQueryString='')
    {
        # product wise stock logs...
        try {
            $paginate = 10;
            $product_id = Crypt::decrypt($product_idStr);
            $product = Product::find($product_id);
            $stock_inventory = StockInventory::where('product_id',$product_id)->first();
            $count = $stock_inventory->quantity;
            $data = StockLog::where('product_id',$product_id)->orderBy('id','desc')->paginate($paginate);

            $data = $data->appends([
                'page' => $request->page
            ]);
            return view('stock.logs', compact('data','product_id','product_idStr','product','getQueryString','count'));
        } catch ( DecryptException $e) {
            return abort(404);
        }
    }
    public function barcodes(Request $request,$product_idStr,$getQueryString='')
    {

        # product wise stock logs...
        try {
            $paginate = 10;
            $product_id = Crypt::decrypt($product_idStr);
            $product = Product::find($product_id);
            $data = StockBarcode::with('product')->where('product_id',$product_id)->where('is_stock_out',0)->orderBy('id','desc')->paginate($paginate);
            $data_damage = StockBarcode::with('product')->where('product_id',$product_id)->where('is_stock_out',0)->where('is_damage',1)->orderBy('id','desc')->paginate($paginate);
            $count = count($data);
            $damage_count = count($data_damage);
            $data = $data->appends([
                'page' => $request->page
            ]);
            return view('stock.barcode', compact('data','product_id','product_idStr','product','getQueryString','count','damage_count'));
        } catch ( DecryptException $e) {
            return abort(404);
        }
    }


    public function barcodeDamageCheck(Request $request,$id, $status){
        $spare = StockBarcode::find($id);
        if ($spare) {
            $spare->is_damage = $status;
            $spare->save();
             // Capture browser_name and navigator_useragent from the request
            $browser_name = $request->query('browser_name');
            $navigator_useragent = $request->query('navigator_useragent');
            $params['barcode_no'] = $spare->barcode_no;
            $params['is_damage'] = $spare->is_damage;


            addChangeLog(Auth::user()->id,$request->ip(),'stock_barcode_defective_identification',$browser_name,$navigator_useragent,$params);

            return redirect()->back()->with('success', 'Status updated successfully.');
        } else {
            return redirect()->back()->with('error', 'Item not found.');
        }
    }
    public function all_damage_stock_barcodes(Request $request){

        $search = !empty($request->search)?$request->search:'';

        $data = StockBarcode::with('product')->where('is_stock_out',0)->where('is_damage',1)->when($search, function($q) use ($search) {
            $q->where('barcode_no', 'LIKE', '%' . $search . '%');
        })->orderBy('id','desc')->paginate(20);
      
        $count = count($data);
        return view('stock.damage_barcodes', compact('data','count','search'));
    }


    public function product_by_barcode(Request $request)
    {
        $kga_sales_data="";
        $dap_data=[];
        $crp_data=[];
        # Get Product Name, GRN & Sales Details...
        $search = !empty($request->search)?$request->search:'';
        $product = $stock_products = $sales_order_products = null;
        if(!empty($search)){
            $product = StockBarcode::with('product:id,title','stock_product','packingslip')->where('barcode_no',$search)->first();
                       

            ### Sales Order Details

            if(!empty($product->packingslip)){
                ### Purchase Order Details

                $stock_products = StockProduct::where('stock_id', $product->stock_id)->where('product_id', $product->product_id)->first();
                
                $sales_orders_id = $product->packingslip->sales_order->id;
                $sales_order_products = SalesOrderProduct::where('sales_orders_id',$sales_orders_id)->where('product_id', $product->product_id)->first();
                ### kga_sales_data,dap_data,crp_data add
                $kga_sales_data = KgaSalesData::where('serial',$search)->first();
                
                $dap_data = DapService::with('servicePartner','branch','return_branch')->where('serial',$search)->orderBy('id','ASC')->get();
                $crp_data = CustomerPointService::where('serial',$search)->orderBy('id','ASC')->get();
               
            }
            
        }
        return view('stock.product-by-barcode', compact('search','product','stock_products','sales_order_products','dap_data','crp_data','kga_sales_data'));
    }

    public function stock_list_csv(Request $request)
    {
        $search = !empty($request->search)?$request->search:'';
        $type = !empty($request->type)?$request->type:'';
        $cat_id = !empty($request->cat_id)?$request->cat_id:'';
        $subcat_id = !empty($request->subcat_id)?$request->subcat_id:'';

        $data = StockInventory::with('product');

        $data = $data->where(function($q) use ($search){
            $q->orWhereHas('product', function ($q) use ($search) {
                $q->where('title', 'LIKE','%'.$search.'%')->orWhere('unique_id', 'LIKE', '%'.$search.'%');
            });
        });
        
        if(!empty($type)){
            $data = $data->where(function($q_type) use ($type) {
                $q_type->orWhereHas('product', function($q_type) use ($type){
                    $q_type->where('type', '=', $type);
                });
            });
            
        }

        if(!empty($cat_id)){
            $data = $data->whereHas('product', function($c) use($cat_id){
                $c->where('cat_id',$cat_id);
            });
           
        }
        if(!empty($subcat_id)){

            $data = $data->whereHas('product', function($c) use($subcat_id){
                $c->where('subcat_id',$subcat_id);
            });           

        }
        
        $data = $data->orderBy('id', 'desc')->get();
        


        $fileName = date('Y-m-d-H-i');
        $type_name = $cat_name = $subcat_name = "";
        if(!empty($type)){
            $type_name = ($type == 'fg')?"Type:- Finished Goods":"Type:- Spare Parts";
            $fileName .= "".strtoupper($type)."";
        }
        if(!empty($cat_id)){
            $cat_name = "Class:- ".getSingleAttributeTable('categories','id',$cat_id,'name');
            $fileName .= "".getSingleAttributeTable('categories','id',$cat_id,'name')."";
        }
        if(!empty($subcat_id)){
            $subcat_name = "Group:- ".getSingleAttributeTable('categories','id',$subcat_id,'name');
            $fileName .= "".getSingleAttributeTable('categories','id',$subcat_id,'name')."";
        }

        $fileName .= "-kgamaster-stock.csv";

        // dd($fileName);
        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        
        $myArr = array();
        if(!empty($data)){
            foreach($data as $item){
                $myArr[] = array(
                    'product_title' => $item->product->title,
                    'quantity' => $item->quantity
                );
            }
        }

        $spaceColumn1 = array('','','');
        $timestampColumn = array('','Timestamp:- '.date('Y-m-d h:i A').'',''); 
        $typeNameColumn = array('',$type_name);
        $catNameColumn = array('',$cat_name);
        $subcatNameColumn = array('',$subcat_name);
        $columns = array('#','Product','Quantity');

        $callback = function() use($myArr,$spaceColumn1,$timestampColumn,$typeNameColumn,$catNameColumn,$subcatNameColumn,$columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $spaceColumn1);
            fputcsv($file, $timestampColumn);
            fputcsv($file, $typeNameColumn);
            fputcsv($file, $catNameColumn);
            fputcsv($file, $subcatNameColumn);
            fputcsv($file, $columns);
            $net_value = 0;
            $i = 1;
            foreach ($myArr as $item) {  
                $row['#'] = $i;              
                $row['Product'] = $item['product_title'];
                $row['Quantity'] = $item['quantity'];
                                
                fputcsv($file, array(
                        $row['#'],
                        $row['Product'],
                        $row['Quantity']                        
                    )
                );  
                
                $i++;
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);


    }
}
