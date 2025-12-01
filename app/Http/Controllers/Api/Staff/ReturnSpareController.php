<?php

namespace App\Http\Controllers\Api\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use App\User;
use App\Models\ReturnSpare;
use App\Models\ReturnSpareBarcode;
use App\Models\ReturnSpareItem;

class ReturnSpareController extends Controller
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
        # Open / Not Stock In Return Spare Items.....

        $data = ReturnSpare::select('id','transaction_id','is_goods_in','goods_in_type','status')->with(['items' => function($po){
            $po->select('id','return_spare_id','product_id','quantity')->with('product:id,title,unique_id,type');
        }]);

        
        $data = $data->where('status','!=',3); # Not cancelled
        $data = $data->where('is_goods_in', 0);
        $data = $data->get();

        // dd($products);
        return Response::json([
            'status'=>true,
            'message'=>"Receivable Return Spare List", 
            'data' => array(
                'count_po' => count($data),
                'list' => $data
            )
        ],200);

    }

    public function bulk_goods_in(Request $request)
    {
        # Bulk Goods In...
        $validator = Validator::make($request->all(),[
            'return_spare_id' => 'required|exists:return_spare,id'
        ]);

        if(!$validator->fails()){
            $params = $request->except('_token');
            $return_spare_id = $params['return_spare_id'];

            $return_spares = ReturnSpare::find($return_spare_id);
            if($return_spares->goods_in_type == 'scan'){
                return Response::json(['status' => false, 'message' => "This PO is set for scan out" ],200);
            }
            if(!empty($return_spares->is_goods_in)){
                return Response::json(['status' => false, 'message' => "Already goods in" ],200);
            }

            if($return_spares->status == 3){
                return Response::json(['status' => false, 'message' => "Order is cancelled, Cannot make the operation" ],200);
            }

            
            ReturnSpare::where('id',$return_spare_id)->update([
                'is_goods_in' => 1
            ]);

            return Response::json([
                'status' => true,
                'message' => "Goods in for bulk successfully",
                'data' => array(
                    
                )
            ],200);

        } else {
            return Response::json(['status' => false, 'message' => $validator->errors()->first() , 'data' => array( $validator->errors() ) ],400);
        }
    }
}
