<?php

namespace App\Http\Controllers\Api\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use App\User;
use App\Models\Packingslip;
use App\Models\PackingslipProduct;
use App\Models\PackingslipBarcode;

class PackingslipController extends Controller
{
    private $staff_id;
    public function __construct(Request $request)
    {
        # pass bearer token in Authorizations key...
        if (! $request->hasHeader('Authorizations')) {
            response()->json(["status"=>false,"message"=>"Unauthorized"],401)->send();
            exit();
        } else {
            $bearer_token = $request->header('Authorizations');
            $token = str_replace("Bearer ","",$bearer_token);            
            try {
                $this->staff_id = Crypt::decrypt($token);  
                $staff = User::find($this->staff_id);           
            } catch (DecryptException $e) {
                response()->json(["status"=>false,"message"=>"Mismatched token"],400)->send();
                exit();
            }
        }

    }

    public function list(Request $request)
    {
        # open scannable ps product list...
        
        $data = Packingslip::select('id','sales_order_id','slipno','goods_out_type','is_goods_out')->with('sales_order:id,order_no')->with([
            'packingslip_products' => function($q){
                $q->select('id','packingslip_id','product_id','quantity')->with('product:id,title,unique_id,type');
            }
        ])->where('is_goods_out', 0)->get();

        return Response::json([
            'status' => true,
            'message' => "Disburseable PS List",
            'data' => array(
                'count_ps' => count($data),
                'ps' => $data
            )
        ],200);
    }

    public function bulk_goods_out(Request $request)
    {
        # code...

        $validator = Validator::make($request->all(),[
            'packingslip_id' => 'required|exists:packingslips,id'
        ]);

        if(!$validator->fails()){
            $params = $request->except('_token');
            $packingslip_id = $params['packingslip_id'];

            $packingslips = Packingslip::find($packingslip_id);
            if($packingslips->goods_out_type == 'scan'){
                return Response::json(['status' => false, 'message' => "This packing slip is set for scan out", 'data' => (object)  array() ],200);
            }
            if(!empty($packingslips->is_goods_out)){
                return Response::json(['status' => false, 'message' => "Already goods out", 'data' => (object)  array() ],200);
            }

            Packingslip::where('id',$packingslip_id)->update([
                'is_goods_out' => 1
            ]);

            $packingslips = Packingslip::select('id','sales_order_id','slipno','goods_out_type')->with('sales_order:id,order_no')->find($packingslip_id);

            $packingslip_products = PackingslipProduct::select('id','packingslip_id','product_id','quantity')->with('product:id,title,unique_id')->where('packingslip_id',$packingslip_id)->get();

            $packingslips->packingslip_products = $packingslip_products;

            return Response::json([
                'status' => true,
                'message' => "Goods out for bulk successfully",
                'data' => array(
                    'packingslips' => $packingslips
                )
            ],200);

        } else {
            return Response::json(['status' => false, 'message' => $validator->errors()->first() , 'data' => array( $validator->errors() ) ],400);
        }


    }
}
