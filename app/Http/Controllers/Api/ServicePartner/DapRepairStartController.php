<?php

namespace App\Http\Controllers\Api\ServicePartner;

use App\Http\Controllers\Controller;
use App\Models\DapDiscountRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\DB;
use App\Models\ServicePartner;
use App\Models\DapService;
use App\Models\PurchaseOrderBarcode;
use App\Models\PurchaseOrderProduct;
use App\Models\Maintenance;
use App\Models\Ledger;
use App\Models\MaintenanceSpare;
use App\Models\GoodsWarranty;
use App\Models\Product;
use App\Models\DapSpearPartOrder;
use App\Models\ServicePartnerCharge;
use Carbon\Carbon;

class DapRepairStartController extends Controller
{ 
    // private $service_partner_id;
    // public function __construct(Request $request)
    // {
    //     # pass bearer token in Authorizations key...
    //     if (! $request->hasHeader('Authorizations')) {
    //         response()->json(["status"=>false,"message"=>"Unauthorized"],401)->send();
    //         exit();
    //     } else {
    //         $bearer_token = $request->header('Authorizations');
    //         $token = str_replace("Bearer ","",$bearer_token);            
    //         try {
    //             $this->service_partner_id = Crypt::decrypt($token);
    //             $staff = ServicePartner::find($this->service_partner_id);           
    //         } catch (DecryptException $e) {
    //             response()->json(["status"=>false,"message"=>"Mismatched token"],400)->send();
    //             exit();
    //         }
    //     }
    // }

    public function dap_repair_start($dap_id,$engg_id)
    {
        $dap_product = DapService::where('id',$dap_id)->where('assign_service_perter_id',$engg_id)->where('is_closed',0)->first();
        if(!empty($dap_product)){
            if($dap_product->is_paid == 0){
                return Response::json(['status' => false, 'message' => "Still payment is not complete by customer"], 200);
            }else{
                return Response::json(['status' => true, 'message' => "Payment done,now you can strat to repair"], 200);
            }
        }else{
            return Response::json(['status' => false, 'message' => "No product found" ],200);
        }

    }
   
}