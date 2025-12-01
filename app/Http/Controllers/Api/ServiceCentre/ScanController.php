<?php

namespace App\Http\Controllers\Api\ServiceCentre;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\DB;
use App\Models\ServiceCentre;
use App\Models\DapService;
use App\Models\DapRequestReceives;
use App\Models\DapRequestReceiveDrop;



class ScanController extends Controller
{
    private $service_centre_id;
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
                $this->service_centre_id = Crypt::decrypt($token);
                $staff = ServiceCentre::find($this->service_centre_id);           
            } catch (DecryptException $e) {
                response()->json(["status"=>false,"message"=>"Mismatched token"],400)->send();
                exit();
            }
        }
    }

    public function goods_in(Request $request)
    {
        # Goods In ...
        $service_centre_id = $this->service_centre_id;

        $validator = Validator::make($request->all(),[
            'dap_request_receive_drop_id' => 'required|exists:dap_request_receive_drops,id',
            'dap_service_id' => 'required|exists:dap_services,id',
            'product_id' => 'required|exists:products,id',
            'barcode' => 'required'
        ]);

        if(!$validator->fails()){
            $dap_request_receive_drop_id = $request->dap_request_receive_drop_id;
            $dap_service_id = $request->dap_service_id;
            $product_id = $request->product_id;
            $barcode = $request->barcode;

            $checkItem = DapRequestReceives::where('dap_request_receive_drop_id',$dap_request_receive_drop_id)->where('dap_service_id',$dap_service_id)->where('product_id',$product_id)->where('barcode',$barcode)->first();

            if(!empty($checkItem)){

                if(empty($checkItem->is_scanned)){
                    if(empty($checkItem->is_service_centre_received)){
                        DapRequestReceives::where('dap_request_receive_drop_id',$dap_request_receive_drop_id)->where('dap_service_id',$dap_service_id)->where('product_id',$product_id)->where('barcode',$barcode)->update([
                            'is_scanned' => 1
                        ]);

                        $totalDapServiceItems = DapRequestReceives::where('dap_request_receive_drop_id',$dap_request_receive_drop_id)->count();
                        $checkScannedAll = DapRequestReceives::where('dap_request_receive_drop_id',$dap_request_receive_drop_id)->where('is_scanned', 1)->count();

                        if($totalDapServiceItems == $checkScannedAll){
                            DapRequestReceives::where('dap_request_receive_drop_id',$dap_request_receive_drop_id)->update([
                                'is_service_centre_received' => 1
                            ]);
                        }

                        return Response::json([
                            'status' => true, 
                            'message' => "Scanned Successfully",
                            'data' => array(
                                'totalDapServiceItems' => $totalDapServiceItems,
                                'checkScannedAll' => $checkScannedAll,
                                'yetToScan' => ($totalDapServiceItems - $checkScannedAll)
                            )
                        ],200);


                    } else {
                        return Response::json([
                            'status' => false, 
                            'message' => "Already Scanned And Received Items By Service Centre !!!",
                            'data' => (object) array()
                        ],200);
                    }
                } else {
                    return Response::json([
                        'status' => false, 
                        'message' => "Already Scanned This Item !!!",
                        'data' => (object) array()
                    ],200);
                }

            } else {
                return Response::json([
                    'status' => false, 
                    'message' => "Item Not Found !!!",
                    'data' => (object) array()
                ],200);
            }

        } else {
            return Response::json(['status' => false, 'message' => $validator->errors()->first() , 'data' => array( $validator->errors() ) ],400);
        }

    }

    public function goods_out(Request $request)
    {
        # Goods Out...
    }


}
