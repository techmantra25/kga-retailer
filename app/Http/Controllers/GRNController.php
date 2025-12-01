<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stock;
use App\Models\StockProduct;
use App\Models\StockBarcode;
use App\Models\StockInventory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class GRNController extends Controller
{
    //

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
        $paginate = 20;
        $data = Stock::with(['purchase_order' => function($q){
            $q->select('id','order_no','supplier_id','type')->with('supplier:id,name,public_name,phone');
        }]);

        $data = $data->orderBy('id', 'desc')->paginate($paginate);

        $totalResult = Stock::count();

        // dd($data);
        return view('grn.list', compact('data','totalResult'));
    }

    public function show($idStr,$getQueryString='')
    {
        try{
            $id = Crypt::decrypt($idStr);
            $stock = Stock::find($id);
            $data = StockProduct::with('product')->where('stock_id',$id)->get();
            return view('grn.detail', compact('id','stock','data'));
        } catch (DecryptException $e) {
            return abort(404);
        }
    }

    public function barcodes(Request $request,$idStr)
    {
        # View Barcode ...
        try{
            $search = !empty($request->search)?$request->search:'';
            $id = Crypt::decrypt($idStr);
            $data = StockBarcode::with('product')->where('stock_id',$id);
            $totalData = StockBarcode::with('product')->where('stock_id',$id);
            
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
            $stock = Stock::find($id);
            $grn_no = $stock->grn_no;
            return view('grn.barcodes', compact('data','totalData','id','grn_no','search'));
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
            $data = StockBarcode::where('stock_id',$id);
            
            if(!empty($search)){
                $data = $data->where(function($q) use ($search){
                    $q->where('barcode_no','LIKE', '%'.$search.'%')->orWhereHas('product', function ($product) use ($search) {
                        $product->where('title', 'LIKE','%'.$search.'%');
                    });
                });
            }

            $data = $data->pluck('barcode_no')->toArray();
            // dd($data);
            $stock = Stock::find($id);
            $grn_no = $stock->grn_no;

            $fileName = "Barcodes-GRN-".$grn_no.".csv";
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
