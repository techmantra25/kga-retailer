<?php

namespace App\Http\Controllers\Api\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use App\User;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderBarcode;
use App\Models\PurchaseOrderProduct;

class PurchaseOrderController extends Controller
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
        # Open / Not Stock In PO Products...
                
        $data = PurchaseOrder::select('id','order_no','type','is_goods_in','goods_in_type','status')->with(['purchase_order_products' => function($po){
            $po->select('id','purchase_order_id','product_id','pack_of','quantity_in_pack','quantity')->with('product:id,title,unique_id,type');
        }]);

        
        $data = $data->where('status','!=',3); # Not cancelled
        $data = $data->where('is_goods_in', 0);
        $data = $data->get();

        // dd($products);
        return Response::json([
            'status'=>true,
            'message'=>"Receivable Purchase Order List", 
            'data' => array(
                'count_po' => count($data),
                'po' => $data
            )
        ],200);

    }

    public function bulk_goods_in(Request $request)
    {
        # code...

        $validator = Validator::make($request->all(),[
            'purchase_order_id' => 'required|exists:purchase_orders,id'
        ]);

        if(!$validator->fails()){
            $params = $request->except('_token');
            $purchase_order_id = $params['purchase_order_id'];

            $purchase_orders = PurchaseOrder::find($purchase_order_id);
            if($purchase_orders->goods_in_type == 'scan'){
                return Response::json(['status' => false, 'message' => "This PO is set for scan out" ],200);
            }
            if(!empty($purchase_orders->is_goods_in)){
                return Response::json(['status' => false, 'message' => "Already goods in" ],200);
            }

            PurchaseOrder::where('id',$purchase_order_id)->update([
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
