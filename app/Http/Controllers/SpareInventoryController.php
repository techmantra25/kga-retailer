<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SpareInventory;
use App\Models\Supplier;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;


class SpareInventoryController extends Controller
{
    //
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

    public function index(Request $request)
    {
        $service_partner_id = !empty($request->service_partner_id)?$request->service_partner_id:'';
        $search = !empty($request->search)?$request->search:'';
        $return_type = !empty($request->return_type)?$request->return_type:'';
        $paginate = !empty($request->paginate)?$request->paginate:'';

        $data = SpareInventory::select('*');
        $totalResult = SpareInventory::select('*');

        if(!empty($search)){
            $data = $data->where('barcode_no', 'LIKE', '%'.$search.'%')->orWhereHas('spare', function($spare) use ($search){
                $spare->where('title', 'LIKE', '%'.$search.'%');
            })->orWhereHas('goods', function($goods) use ($search){
                $goods->where('title', 'LIKE', '%'.$search.'%');
            });
            $totalResult = $totalResult->where('barcode_no', 'LIKE', '%'.$search.'%')->orWhereHas('spare', function($spare) use ($search){
                $spare->where('title', 'LIKE', '%'.$search.'%');
            })->orWhereHas('goods', function($goods) use ($search){
                $goods->where('title', 'LIKE', '%'.$search.'%');
            });
        }
        
        if(!empty($return_type)){
            if($return_type == 'yes'){
                $data = SpareInventory::where('is_returned', 1);
                $totalResult = SpareInventory::where('is_returned', 1);
            } else if($return_type == 'no'){
                $data = SpareInventory::where('is_returned', 0);
                $totalResult = SpareInventory::where('is_returned', 0);
            }
            
        }
        

        $data = $data->orderBy('id', 'desc')->paginate($paginate);
        $totalResult = $totalResult->count();

        // dd($data);
        $data = $data->appends([
            'paginate' => $paginate,
            'page' => $request->page,
            'return_type' => $return_type,
            'search' => $search
        ]);

        return view('spare-inventory.list', compact('data','totalResult','paginate','return_type','search'));
    }

    public function list_return(Request $request)
    {
        $suppliers = Supplier::orderBy('public_name')->get();
        $supplier_id = !empty($request->supplier_id)?$request->supplier_id:'';

        $data = SpareInventory::where('supplier_id', $supplier_id)->where('is_returned', 0)->orderBy('id', 'desc')->get();
        return view('spare-inventory.return', compact('suppliers','supplier_id','data'));
    }

    public function save_return(Request $request)
    {
        $params = $request->except('_token');
        // dd($params);

        $ids = $params['ids'];
        // dd($ids);
        SpareInventory::whereIn('id',$ids)->update(['is_returned' => 1, 'updated_at' => date('Y-m-d H:i:s')]);
        

        Session::flash('message', "Spares returned to inventory successfully.");
        return redirect('/spare-inventory/list');
    }

    public function supplier_return_list(Request $request)
    {
        $suppliers = Supplier::orderBy('public_name')->get();
        $supplier_id = !empty($request->supplier_id)?$request->supplier_id:'';

        $data = SpareInventory::where('supplier_id', $supplier_id)->where('is_returned', 1)->orderBy('id', 'desc')->paginate(25);
        $totalResult = SpareInventory::where('supplier_id', $supplier_id)->where('is_returned', 1)->count();
        return view('spare-inventory.supplier-returns', compact('suppliers','supplier_id','data','totalResult'));
        
    }

    public function supplier_return_csv(Request $request)
    {
        $supplier_id = !empty($request->supplier_id)?$request->supplier_id:'';
        $supplier = Supplier::find($supplier_id);
        $s_name = $supplier->public_name;

        $data = SpareInventory::where('supplier_id', $supplier_id)->where('is_returned', 1)->get();

        if(!empty($data)){
            foreach($data as $item){
                $myArr[] = array(
                    'barcode_no' => $item->barcode_no,
                    'spare_info' => $item->spare->title,
                    'date' => $item->updated_at
                ); 
            }
        }

        $fileName = "kgamaster-returned-spare-".$s_name."-".date('Ymd')."";
        $fileName .= ".csv";
        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $splColumn = array('',''.$s_name.'');
        $spaceColumn1 = array('','','','','','','','','','');
        $columns = array('#','Date','Barcode','Spare Description');
        
        $callback = function() use($myArr,$splColumn,$spaceColumn1,$columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $splColumn);
            fputcsv($file, $spaceColumn1);
            fputcsv($file, $columns);
            
            $i = 1;

            foreach ($myArr as $item) {     
                $row['#'] = $i;      
                $row['Date'] = date('d/m/Y', strtotime($item['date']));
                $row['Barcode'] = $item['barcode_no'];
                $row['Spare Description'] = $item['spare_info'];

                $fputcsvRowArr = array(
                    $row['#'],
                    $row['Date'],
                    $row['Barcode'],
                    $row['Spare Description']
                );
                
                fputcsv($file, $fputcsvRowArr );  
                $i++;              
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);


    }


}
