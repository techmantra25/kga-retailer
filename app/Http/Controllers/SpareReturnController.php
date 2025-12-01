<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ServicePartner;
use App\Models\SpareReturn;
use App\Models\SpareInventory;
use App\Models\Ledger;
use App\Models\PurchaseOrderBarcode;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class SpareReturnController extends Controller
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

    /*
    ** List of Returned or Not Returned Spares of a Service Partner 
    ** GET
    */

    public function index(Request $request)
    {
        $service_partner_id = !empty($request->service_partner_id)?$request->service_partner_id:'';
        $search = !empty($request->search)?$request->search:'';
        $return_type = !empty($request->return_type)?$request->return_type:'';
        $paginate = !empty($request->paginate)?$request->paginate:'';

        $service_partners = ServicePartner::orderBy('person_name', 'asc')->get();
        
        $data = SpareReturn::where('service_partner_id', $service_partner_id);
        $totalResult = SpareReturn::where('service_partner_id', $service_partner_id);

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
                $data = $data->where('is_returned', 1);
                $totalResult = $data->where('is_returned', 1);
            } else if($return_type == 'no'){
                $data = $data->where('is_returned', 0);
                $totalResult = $data->where('is_returned', 0);
            }
            
        }
        

        $data = $data->orderBy('id', 'desc')->paginate($paginate);
        $totalResult = $totalResult->count();

        // dd($data);
        $data = $data->appends([
            'paginate' => $paginate,
            'page' => $request->page,
            'service_partner_id' => $service_partner_id,
            'return_type' => $return_type,
            'search' => $search
        ]);

        return view('returned-spares.list', compact('data','totalResult','paginate','service_partners','service_partner_id','return_type','search'));
    }

    /*
    ** Return Single Spare To Inventory
    ** GET
    */

    public function return_items(Request $request,$idStr,$getQueryString='')
    {
        try {
            $id = Crypt::decrypt($idStr);
            // dd($id);
            $spare_return = SpareReturn::find($id);

            $po = PurchaseOrderBarcode::where('barcode_no', $spare_return->barcode_no)->first();
            $supplier_id = $po->order->supplier_id;
            

            $spareInventoryArr = array(
                'spare_return_id' => $id,
                'spare_id' => $spare_return->spare_id,
                'barcode_no' => $spare_return->new_barcode_no,
                'supplier_id' => $supplier_id,
                'service_partner_id' => $spare_return->service_partner_id,
                'goods_id' => $spare_return->goods_id,
                'rate' => $spare_return->rate,
                'created_at' => date('Y-m-d H:i:s')
            );
            // dd($spareInventoryArr);
            SpareInventory::insert($spareInventoryArr);
            SpareReturn::where('id',$id)->update([
                'is_returned' => 1,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            $ledgerData = array(
                'type' => 'credit',
                'amount' => $spare_return->rate,
                'entry_date' => date('Y-m-d'),
                'user_type' => 'servicepartner',
                'service_partner_id' => $spare_return->service_partner_id,                
                'purpose' => 'spare_return',
                'transaction_id' => $spare_return->new_barcode_no,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            );
            Ledger::insert($ledgerData);

            Session::flash('message', "Spares returned to inventory successfully.");
            return redirect('/spare-return/list?'.$getQueryString);

        } catch ( DecryptException $e) {
            return abort(404);
        }
    }

    /*
    ** Generate New barcode
    ** GET
    */

    public function generate_new_barcode(Request $request,$idStr,$getQueryString='')
    {
        try {
            $id = Crypt::decrypt($idStr);
            // dd($id);
            $spare_return = SpareReturn::find($id);
            $barcode_no = $spare_return->barcode_no;
            $new_barcode_no = "RETSPR".$barcode_no;
            // dd($new_barcode_no);
            $barcodeGeneratorWithNo =  barcodeGeneratorWithNo($new_barcode_no);

            // dd($barcodeGeneratorWithNo);
            SpareReturn::where('id', $id)->update([
                'new_barcode_no' => $new_barcode_no,
                'code_html' => $barcodeGeneratorWithNo['code_html'],
                'code_base64_img' => $barcodeGeneratorWithNo['code_base64_img']
            ]);

            Session::flash('message', "New barcode generated successfully.");
            return redirect('/spare-return/list?'.$getQueryString);

        } catch ( DecryptException $e) {
            return abort(404);
        }    
        
    }



}
