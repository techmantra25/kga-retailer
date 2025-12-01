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
use App\Models\DapServicePayment;
use App\Models\MaintenanceSpare;
use App\Models\GoodsWarranty;
use App\Models\Product;
use App\Models\SpareGoods;
use App\Models\DapSpearPartOrder;
use App\Models\DapSpearPartFinalOrder;
use App\Models\CustomerPointService;
use App\Models\CustomerPointServiceSpare;
use App\Models\CRPFinalSpare;
use App\Models\CRPPayment;
use App\Models\ProductWarranty;
use App\Models\CrpServicePayment;
use App\Models\ServicePartnerCharge;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Barryvdh\DomPDF\Facade\Pdf;



class CrpController extends Controller
{ 
    public function list($id,Request $request)
    {
        if(isset($request->type)){
            if($request->type=='cancelled'){
                $status = 9;
                $crp_product = CustomerPointService::where('assign_service_perter_id',$id)->orderBy('id', 'DESC')->where('status',$status)->get()->toArray();
            }elseif($request->type=='completed'){
                $status = 8;
                $crp_product = CustomerPointService::where('assign_service_perter_id',$id)->orderBy('id', 'DESC')->where('status',$status)->get()->toArray();
            }else{
                $crp_product = CustomerPointService::where('assign_service_perter_id',$id)->orderBy('id', 'DESC')->where('status', '>', 2)->whereNotIn('status', [8,9])->get()->toArray();
            }      
            if(!empty($crp_product)){
                return Response::json(['status' => true, 'data' => $crp_product], 200);
            }else{
                return Response::json(['status' => false, 'message' => "No product found" ],200);
            }
        }else{
            return Response::json(['status' => false, 'message' => "No product found" ],200);
        }
    }
    public function add_spare($auth_id,$cpr_id) 
    {
        $crp_product = CustomerPointService::find($cpr_id);
        if(!empty($crp_product)){
            $product_id = $crp_product->product_id;
            $spare_parts_data = SpareGoods::with('spare')->where('goods_id', $product_id)->get()->toArray();
            if(count($spare_parts_data)>0){
                return Response::json(['status' => true,'crp_data'=>$crp_product, 'data' => $spare_parts_data], 200);
            }else{     
                return Response::json(['status' => false, 'message' => "No Sapare recored found" ], 200);
            }
        }else{
            return Response::json(['status' => false, 'message' => "No Service found" ],200);
        }
        
    }
    public function add_spare_store(Request $request) {
        dd($request->all()); 
    }
    // public function product_details($id){    backup

    //     $data = CustomerPointService::find($id);
    //     if($data){      
    //         $GoodsWarranty = ProductWarranty::where('dealer_type', $data->dealer_type)->where('goods_id', $data->product_id)->get(); // for only khosla r non khosla product
    //         $warranty=[]; 
    //         $invoice=[]; 
    //        if(count($GoodsWarranty)>0){
    //                 foreach($GoodsWarranty as $key => $value){
    //                     $array = [];
    //                     $array['warranty_type']=$value->warranty_type;
                        
    //                     $array['additional_warranty_type']=$value->additional_warranty_type;
    //                     if($value->warranty_type=="cleaning"){
    //                         $GetCleaningWarranty = GetCleaningWarranty($data->product_id);
    //                         $left_cleaning = $value->number_of_cleaning-$GetCleaningWarranty;
    //                         $array['number_of_cleaning']=$left_cleaning;
    //                     }else{
    //                         $array['number_of_cleaning']=$value->number_of_cleaning;
    //                     }
                       
    //                     $array['parts']=$value->spear_goods?$value->spear_goods->title:null;
    //                     $array['warranty_period']=$value->warranty_period;
    //                     $array['dealer_type']=$value->dealer_type;
    //                     $warranty_period = $value->warranty_period;
    //                     $warranty_end_date = date('Y-m-d', strtotime($data->bill_date. ' + '.$warranty_period.' months'));
    //                     $warranty_date = date('Y-m-d', strtotime($warranty_end_date . ' -1 days'));
    //                     $array['warranty_end_date']=date('d-m-Y',strtotime($warranty_date));
    //                     if($data->entry_date < $warranty_date){
    //                         $array['warranty_status']="YES";
    //                     }else{
    //                         $array['warranty_status']="NO";
    //                     }
    //                     $warranty[]= $array;
    //                 }
    //             $invoice['spare_charge']=$data->final_amount-$data->repair_charge;
    //             $invoice['service_charge']=$data->repair_charge;
    //             $invoice['in_warranty']=$data->in_warranty;
    //             $invoice['final_amount']=$data->final_amount;
    //             return response()->json(['status' => true, 'data' => $data , 'warranty'=>$warranty, 'invoice'=>$invoice], 200);
    //        }else{

    //            return response()->json(['status' => false, 'message' => "No warranty record found"], 200);
    //        }
    //     }else{
    //         return response()->json(['status' => false, 'message' => "No record found"], 200);
    //     }


    // }    
    public function product_details($id){

        $data = CustomerPointService::find($id);
        if($data){      
            $GoodsWarranty = ProductWarranty::where('dealer_type', $data->dealer_type)->where('goods_id', $data->product_id)->get(); // for only khosla r non khosla product
            $warranty=[]; 
            $invoice=[]; 
           if(count($GoodsWarranty)>0){
                    foreach($GoodsWarranty as $key => $value){
                        $array = [];
                        $array['warranty_type']=$value->warranty_type;
                        $array['additional_warranty_type']=$value->additional_warranty_type;

                        if($value->warranty_type=="cleaning"){
                            $GetCleaningWarranty = GetCleaningWarranty($data->product_id);
                            $left_cleaning = $value->number_of_cleaning-$GetCleaningWarranty;
                            $array['number_of_cleaning']=$left_cleaning;
                        }else{
                            $array['number_of_cleaning']=$value->number_of_cleaning;
                        }

                        $array['parts']=$value->spear_goods?$value->spear_goods->title:null;

                        if ($value->warranty_type === 'additional') {
                            // Get comprehensive warranty period if available
                            $comprehensive_warranty_period = ProductWarranty::where('goods_id', $value->goods_id)
                                ->where('dealer_type', $value->dealer_type)
                                ->where('warranty_type', 'comprehensive')
                                ->pluck('warranty_period')
                                ->first();
                            $comprehensive_warranty_period = $comprehensive_warranty_period ? $comprehensive_warranty_period : 0;

                            // Add comprehensive period to additional warranty period
                            $array['warranty_period'] = $value->warranty_period + $comprehensive_warranty_period;
                        } else {
                            $array['warranty_period'] = $value->warranty_period;
                        }

                        // $array['warranty_period']=$value->warranty_period;
                        $array['dealer_type']=$value->dealer_type;
                        $warranty_period = $array['warranty_period'];
                        $warranty_end_date = date('Y-m-d', strtotime($data->bill_date. ' + '.$warranty_period.' months'));
                        $warranty_date = date('Y-m-d', strtotime($warranty_end_date . ' -1 days'));
                        $array['warranty_end_date']=date('d-m-Y',strtotime($warranty_date));
                        if($data->entry_date < $warranty_date){
                            $array['warranty_status']="YES";
                        }else{
                            $array['warranty_status']="NO";
                        }
                        $warranty[]= $array;
                    }
                $invoice['spare_charge']=$data->final_amount-$data->repair_charge;
                $invoice['service_charge']=$data->repair_charge;
                $invoice['in_warranty']=$data->in_warranty;
                $invoice['final_amount']=$data->final_amount;
                return response()->json(['status' => true, 'data' => $data , 'warranty'=>$warranty, 'invoice'=>$invoice], 200);
           }else{

               return response()->json(['status' => false, 'message' => "No warranty record found"], 200);
           }
        }else{
            return response()->json(['status' => false, 'message' => "No record found"], 200);
        }


    }
    public function service_closed_with_out_spare(Request $request){
        // $data = json_decode($request->data);
        // Check if $data is null
        if (is_null($request->all())) {
            return response()->json(['status' => false, 'message' => 'Something went wrong, Try again'], 200);
        }else{
            if(empty($request->remarks)){
                return response()->json(['status' => false, 'message' => 'Reamks field is required'], 200);
            }
            if (empty($request->crp_id)) {
                return response()->json(['status' => false, 'message' => 'CRP ID is required'], 200);
            }
            
            $crp_product = CustomerPointService::find($request->crp_id);
            if (!$crp_product) {
                return response()->json(['status' => false, 'message' => 'Service record not found'], 200);
            }

            $crp_product->remarks = $request->remarks;
            // $crp_product->is_spare_required = $request->is_spare_required;  // after otp verify then we push $crp_product->is_spare_required =0;
            // $crp_product->is_closed = 1;
            $crp_product->save();
            // $product_name = $crp_product->item;
            $mobile = $crp_product->alternate_no;
            // $mobile = 7908115612;

            $otp = rand(1000,9999);

            $checkPhoneNumberValid = checkPhoneNumberValid($mobile);
            if($checkPhoneNumberValid){
                $query_calling_number = "6291117317";
                $sms_entity_id = getSingleAttributeTable('settings','id',1,'sms_entity_id');
                $sms_template_id = "1707172847011954423";
                
                $myMessage = urlencode('Your OTP for confirming the closure of your recent call is: '.$otp.'. If you did not request this, please contact '.$query_calling_number.'.AMMR TECHNOLOGY LLP');

                $sms_url = 'https://sms.bluwaves.in/sendsms/bulk.php?username=ammrllp&password=123456789&type=TEXT&sender=AMMRTL&mobile=' . $mobile . '&message=' . $myMessage . '&entityId=' . $sms_entity_id . '&templateId=' . $sms_template_id;
                
                // // echo $myMessage; die;
                
                $curl = curl_init();
                
                curl_setopt_array($curl, array(
                    CURLOPT_URL => $sms_url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                ));
                
                $response = curl_exec($curl);
                curl_close($curl);
                // echo '<pre>'; echo $response; 
                DB::table('sms_api_response')->insert([
                    'sms_template_id' => $sms_template_id,
                    'sms_entity_id' => $sms_entity_id,
                    'phone' => $mobile,
                    'message_body' => $myMessage,
                    'response_body' => $response,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                
                $crp_product->closing_otp = $otp;
                $crp_product->closing_otp_time = date('Y-m-d H:i:s');
                $crp_product->verify_closing_otp = 0;
                $crp_product->save();
                return response()->json(['status' => true, 'message' => "OTP has been sent successfully"], 200);
            }else{
                return response()->json(['status' => false, 'message' => "Mobile number must be 10 digits"], 200);
            }
        }
        
    }
    public function service_closed_with_warranty_no_payment(Request $request){
        // $data = json_decode($request->data);
        // Check if $data is null
        if (is_null($request->all())) {
            return response()->json(['status' => false, 'message' => 'Something went wrong, Try again'], 200);
        }else{
            if (empty($request->crp_id)) {
                return response()->json(['status' => false, 'message' => 'CRP ID is required'], 200);
            }
            $crp_product = CustomerPointService::find($request->crp_id);
            if (!$crp_product) {
                return response()->json(['status' => false, 'message' => 'Service record not found'], 200);
            }
            if($crp_product->final_amount > 0){
            //when $crp_product->final_amount>0 this template send

            // $product_name = $crp_product->item;
            $mobile = $crp_product->alternate_no;
            // $mobile = 7908115612;

            $otp = rand(1000,9999);

            $checkPhoneNumberValid = checkPhoneNumberValid($mobile);
            if($checkPhoneNumberValid){
                $query_calling_number = "6291117317";
                $sms_entity_id = getSingleAttributeTable('settings','id',1,'sms_entity_id');
                $sms_template_id = "1707172847011954423";

                
                $myMessage = urlencode('Your OTP for confirming the closure of your recent call is: '.$otp.'. If you did not request this, please contact '.$query_calling_number.'.AMMR TECHNOLOGY LLP');

                $sms_url = 'https://sms.bluwaves.in/sendsms/bulk.php?username=ammrllp&password=123456789&type=TEXT&sender=AMMRTL&mobile=' . $mobile . '&message=' . $myMessage . '&entityId=' . $sms_entity_id . '&templateId=' . $sms_template_id;
                
                // // echo $myMessage; die;
                
                $curl = curl_init();
                
                curl_setopt_array($curl, array(
                    CURLOPT_URL => $sms_url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                ));
                
                $response = curl_exec($curl);
                curl_close($curl);
                // echo '<pre>'; echo $response; 
                DB::table('sms_api_response')->insert([
                    'sms_template_id' => $sms_template_id,
                    'sms_entity_id' => $sms_entity_id,
                    'phone' => $mobile,
                    'message_body' => $myMessage,
                    'response_body' => $response,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                
                $crp_product->closing_otp = $otp;
                $crp_product->closing_otp_time = date('Y-m-d H:i:s');
                $crp_product->verify_closing_otp = 0;
                $crp_product->save();
                return response()->json(['status' => true, 'message' => "OTP has been sent successfully"], 200);
                }else{
                    return response()->json(['status' => false, 'message' => "Mobile number must be 10 digits"], 200);
                }
            }else{
                //when $crp_product->final_amount == 0 ; this template send
                $mobile = $crp_product->alternate_no;
                $item = $crp_product->item;
                if (strlen($item) > 30) {
                    $item = substr($item, 0, 28) . '..';
                }
                $call_id = $crp_product->unique_id;
                $otp = rand(1000,9999);
                $checkPhoneNumberValid = checkPhoneNumberValid($mobile);
                if($checkPhoneNumberValid){
                    $query_calling_number = "6291117317";
                    $sms_entity_id = getSingleAttributeTable('settings','id',1,'sms_entity_id');
                    $sms_template_id = "1707172916770617544";

                    // Your product {#var#} with Call ID {#var#} is under warranty. Repair is free. Share OTP {#var#} to accept. Customer Care 6291117317.AMMR TECHNOLOGY LLP
                    $myMessage = urlencode('Your product '.$item.' with Call ID '.$call_id.' is under warranty. Repair is free. Share OTP '.$otp.' to accept. Customer Care '.$query_calling_number.'.AMMR TECHNOLOGY LLP');

                    $sms_url = 'https://sms.bluwaves.in/sendsms/bulk.php?username=ammrllp&password=123456789&type=TEXT&sender=AMMRTL&mobile=' . $mobile . '&message=' . $myMessage . '&entityId=' . $sms_entity_id . '&templateId=' . $sms_template_id;
                    
                    // // echo $myMessage; die;
                    
                    $curl = curl_init();
                    
                    curl_setopt_array($curl, array(
                        CURLOPT_URL => $sms_url,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'GET',
                    ));
                    
                    $response = curl_exec($curl);
                    curl_close($curl);
                    // echo '<pre>'; echo $response; 
                    DB::table('sms_api_response')->insert([
                        'sms_template_id' => $sms_template_id,
                        'sms_entity_id' => $sms_entity_id,
                        'phone' => $mobile,
                        'message_body' => $myMessage,
                        'response_body' => $response,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                    
                    $crp_product->closing_otp = $otp;
                    $crp_product->closing_otp_time = date('Y-m-d H:i:s');
                    $crp_product->verify_closing_otp = 0;
                    $crp_product->save();
                    return response()->json(['status' => true, 'message' => "OTP has been sent successfully"], 200);
                    }else{
                        return response()->json(['status' => false, 'message' => "Mobile number must be 10 digits"], 200);
                    }
            }
        }
        
    }
 

    public function service_closed_otp_verify(Request $request){
        $crp_product = CustomerPointService::find($request->crp_id);
        if (!$crp_product) {
            return Response::json(['status' => false, 'message' => 'Service not found'], 200);
        }else{
            $existing_otp = $crp_product->closing_otp;
            if ($existing_otp != $request->otp) {
                return Response::json(['status' => false, 'message' => 'The OTP you entered does not match our records'], 200);
            }else{
                $crp_product->is_spare_required = 0; // as because no spare required for repairing..
                $crp_product->verify_closing_otp = 1; //verified
                $crp_product->is_closed = 1;
                $crp_product->status = 4;//admin approval
                $crp_product->save();
                return Response::json(['status' => true, 'message' => 'The OTP matched & the service is closed with out spare parts'], 200);
            }
        }
    }
    // public function service_closed_otp_verify_with_warranty_no_payment(Request $request){
    //     $crp_product = CustomerPointService::find($request->crp_id);
    //     if (!$crp_product) {
    //         return Response::json(['status' => false, 'message' => 'Service not found'], 200);
    //     }
    //     // Begin a transaction
    //     DB::beginTransaction();

    //     try{
    //             $existing_otp = $crp_product->closing_otp;
    //             if ($existing_otp != $request->otp) {
    //                 return Response::json(['status' => false, 'message' => 'The OTP you entered does not match our records'], 200);
    //             }else{
    //                 $crp_product->verify_closing_otp = 1; //verified
    //                 $crp_product->is_closed = 1;
    //                 $crp_product->status = 8;//closed
    //                 $crp_product->save();
    //                 DB::commit();

    //                 $service_charge = ServicePartnerCharge::select('repair')->where('service_partner_id',$crp_product->assign_service_perter_id)->where('product_id',$crp_product->product_id)->first();

    //                 if (!$service_charge) {
    //                     DB::rollBack(); // Rollback the transaction on error
    //                     return redirect()->back()->with('warning', 'Service charge not found for this product.');
    //                 }

    //                     // Ledger Entry
    //                     $ledgerData = [
    //                         'type' => 'credit',
    //                         'service_partner_id' => $data->assign_service_perter_id,
    //                         'amount' => $service_charge->repair,
    //                         'entry_date' => date('Y-m-d'),
    //                         'user_type' => 'servicepartner',
    //                         'purpose' => 'Customer Point Repair',
    //                         'transaction_id' => $data->unique_id,
    //                         'crp_id' => $data->id,
    //                         'created_at' => date('Y-m-d H:i:s'),
    //                         'updated_at' => date('Y-m-d H:i:s')
    //                     ];
        
    //                     $existLedger = Ledger::where('crp_id', $data->id)->where('type','credit')->first();
    //                     if (empty($existLedger)) {
    //                         Ledger::insert($ledgerData);
    //                     }
    //                 }


    //                 return Response::json(['status' => true, 'message' => 'The OTP matched & the service is closed'], 200);
                
    //     } catch (\Exception $e) {
    //         // Rollback the transaction on error
    //         DB::rollBack();
            
    //         return response()->json(['status' => false, 'message' => 'Failed to update admin approval', 'error' => $e->getMessage()], 500);
    //     }
    //     }
    // }

    public function service_closed_otp_verify_with_warranty_no_payment(Request $request)
        {
            // Fetch the service by ID
            $crp_product = CustomerPointService::find($request->crp_id);
            if (!$crp_product) {
                return Response::json(['status' => false, 'message' => 'Service not found'], 200);
            }

            // Begin a transaction
            DB::beginTransaction();

            try {
                $existing_otp = $crp_product->closing_otp;
                if ($existing_otp != $request->otp) {
                    return Response::json(['status' => false, 'message' => 'The OTP you entered does not match our records'], 200);
                }

                // Update the service as closed and OTP verified
                $crp_product->verify_closing_otp = 1; // OTP verified
                $crp_product->is_closed = 1;
                $crp_product->status = 8; // Closed status
                $crp_product->save();

                // Get the service charge
                $service_charge = ServicePartnerCharge::select('repair')
                    ->where('service_partner_id', $crp_product->assign_service_perter_id)
                    ->where('product_id', $crp_product->product_id)
                    ->first();

                if (!$service_charge) {
                    DB::rollBack(); // Rollback the transaction on error
                    return Response::json(['status' => false, 'message' => 'Service charge not found for this product'], 200);
                }



                  // if repeat_call === 1 and $data->repeat_crp_id found
                  if($crp_product->repeat_call === 1 && $crp_product->repeat_crp_id != NULL){
                    //debit ledger
                    $pre_crp_data = CustomerPointService::find($crp_product->repeat_crp_id);
                    $pre_service_partner = $pre_crp_data->assign_service_perter_id;
                    // $pre_service_partner_service_charge = Ledger::where('service_partner_id',$pre_service_partner)->where('crp_id',$data->repeat_crp_id)->where('purpose','Customer Point Repair')->where('type','credit')->pluck('amount');
                    $pre_service_partner_service_charge = Ledger::where('service_partner_id',$pre_service_partner)->where('crp_id',$crp_product->repeat_crp_id)->where('transaction_id',$pre_crp_data->unique_id)->where('type','credit')->pluck('amount');


                    $ledgerData = [
                        'type' => 'debit',
                        'service_partner_id' => $pre_service_partner,
                        'amount' => $pre_service_partner_service_charge,
                        'entry_date' => date('Y-m-d'),
                        'user_type' => 'servicepartner',
                        'purpose' => 'Customer Point Repair(repeat call)',
                        'transaction_id' => $pre_crp_data->unique_id,
                        'crp_id' => $crp_product->repeat_crp_id,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ];
                    Ledger::insert($ledgerData);
                }


                // Prepare ledger data
                $ledgerData = [
                    'type' => 'credit',
                    'service_partner_id' => $crp_product->assign_service_perter_id,
                    'amount' => $service_charge->repair,
                    'entry_date' => date('Y-m-d'),
                    'user_type' => 'servicepartner',
                    'purpose' => 'Customer Point Repair',
                    'transaction_id' => $crp_product->unique_id,
                    'crp_id' => $crp_product->id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                // Check if the ledger entry already exists
                $existLedger = Ledger::where('crp_id', $crp_product->id)->where('type', 'credit')->first();
                if (empty($existLedger)) {
                    Ledger::insert($ledgerData);
                }

                DB::commit();

                return Response::json(['status' => true, 'message' => 'The OTP matched & the service is closed'], 200);

            } catch (\Exception $e) {
                // Rollback the transaction on error
                DB::rollBack();
                return response()->json(['status' => false, 'message' => 'Failed to update service closure', 'error' => $e->getMessage()], 500);
            }
        }


    
    public function sapre_warranty_check_product_no_damage($crp_id,$sp_barcode){  // spare warranty check when product is no damage
        $crp_data = CustomerPointService::find($crp_id);
        $crp_entry_date =$crp_data->entry_date;
        $product_id=$crp_data->product_id;
        
        $spare_parts_data = SpareGoods::where('goods_id', $product_id)->pluck('spare_id')->toArray();
        $sp_id = PurchaseOrderBarcode::where('barcode_no',$sp_barcode)->value('product_id');
        if($sp_id){
            $sp_data= Product::find($sp_id);
            // Check if the sp_id exists in the spare_parts_data array
            if (in_array($sp_id, $spare_parts_data)) {
                $warranty_data = get_spare_warranty($crp_id, $sp_id);
                $final_amount = CustomerPointServiceSpare::where('sp_id', $sp_id)->value('final_amount');
                // Add product damage information to warranty data
                $warranty_data['spare_name'] = $sp_data?$sp_data->title:"Not Found";
                $warranty_data['spare_id'] = $sp_id;
                $warranty_data['product_damage'] = "No";
                $warranty_data['crp_id'] = $crp_id;
                $warranty_data['sp_barcode'] = $sp_barcode;
                $warranty_data['spare_price'] = $final_amount;
            return response()->json(['status' => true, 'data' => $warranty_data]);
            } else {
                return response()->json(['status' => false, 'message' => 'Spare part is not fit for this product']);
            }
        }else{
            return response()->json(['status' => false, 'message' => 'The scanned barcode is not found in our purchase records'], 200);
        }
    }
    public function sapre_warranty_check_product_damage($crp_id,$sp_barcode){
        $crp_data = CustomerPointService::find($crp_id);
        $product_id=$crp_data->product_id; 
        $spare_parts_data = SpareGoods::where('goods_id', $product_id)->pluck('spare_id')->toArray();

        $sp_id = PurchaseOrderBarcode::where('barcode_no',$sp_barcode)->value('product_id');
        if($sp_id){
            $sp_data= Product::find($sp_id);
            // Check if the sp_id exists in the spare_parts_data array
            if (in_array($sp_id, $spare_parts_data)) {
                $warranty_data =[];
                // Add product damage information to warranty data
                $final_amount = CustomerPointServiceSpare::where('sp_id', $sp_id)->value('final_amount');
                $warranty_data['warranty_status'] = 0; // out of warranty as bcz product damage
                $warranty_data['product_damage'] = "Yes";
                $warranty_data['spare_name'] = $sp_data?$sp_data->title:"Not Found";
                $warranty_data['spare_id'] = $sp_id;
                $warranty_data['crp_id'] = $crp_id;
                $warranty_data['sp_barcode'] = $sp_barcode;
                $warranty_data['spare_price'] = $final_amount;
            return response()->json(['status' => true, 'data' => $warranty_data]);
            } else {
                return response()->json(['status' => false, 'message' => 'Spare part is not for this product']);
            }
        }else{
            return response()->json(['status' => false, 'message' => 'The scanned barcode is not found in our purchase records'], 200);
        }
    }
    public function final_spare_warranty_check(Request $request){
        $data = json_decode($request->data);
        if(count($data)>0){
            $final_amount = 0;
            $warranty_product = 0;
            $service_charge_warranty = 0;
            $outof_warranty_product = 0;
            $response = [];
            foreach($data as $key=>$item){
                $CRPFinalSpare = new CRPFinalSpare;;
                if($item->warranty_status==1){
                    $warranty_product += 1;
                    $service_charge_warranty+= $item->service_chargeable_warranty;
                    $final_amount += 0;
                }else{
                    $outof_warranty_product +=1;
                    $service_charge_warranty+= $item->service_chargeable_warranty;
                    $final_amount += $item->spare_price;
                }
            }
           
            $crp_data = CustomerPointService::find($data[0]->crp_id);
            if(!$crp_data){
                return response()->json(['status' => false, 'message'=>"Service not found!"]);
            }
            
            $repair_charge = Product::where('id', $crp_data->product_id)->value('repair_charge');
            
            $repair_charge = $repair_charge?$repair_charge:0;//For Customer
           
           
            if($repair_charge==0){
                return response()->json(['status' => false, 'message'=>"Don't have assign repair charge for this product"]);
            }
            // Response
            $response['message'] = 'There are '.$warranty_product.' product in warranty and '.$outof_warranty_product.' product out of warranty';
            if($outof_warranty_product>0){
                
                $repair_charge = $service_charge_warranty==1?0:$repair_charge;
                $response['service_charge'] = $repair_charge;
                $response['final_amount'] = $final_amount+$repair_charge;
                $response['status'] = 0;
                $response['status_response'] = "OutSide Warranty";
            }else{
                $response['service_charge'] = 0;
                $response['final_amount'] = $final_amount;
                $response['status'] = 1;
                $response['status_response'] = "Within Warranty";
            }
            return response()->json(['status' => true, 'data' => $response]);
        }else{
            return response()->json(['status' => false, 'message' => 'Kindly choose a spare part.']);
        }
    }
    
    public function final_spare_warranty_confirm(Request $request)
    {
        $data = json_decode($request->data);
        
        if (count($data) > 0) {
            // Start DB transaction
            DB::beginTransaction();

            try {
                $final_amount = 0;
                $spare_charge = 0;
                $warranty_product = 0;
                $service_charge_warranty = 0;
                $outof_warranty_product = 0;
                $response = [];
                $crp_data = CustomerPointService::find($data[0]->crp_id);
                $existing_crp_final_spare = CRPFinalSpare::where('crp_id', $data[0]->crp_id)->delete(); //Delete Exising CRP data

                foreach ($data as $key => $item) {
                    $CRPFinalSpare = new CRPFinalSpare;
                    $CRPFinalSpare->actual_price = $item->spare_price;

                    if ($item->warranty_status == 1) {
                        $service_charge_warranty = $item->service_chargeable_warranty;
                        $CRPFinalSpare->selling_price = 0; // For Product Under Warranty
                        $warranty_product += 1;
                    } else {
                        $service_charge_warranty = $item->service_chargeable_warranty;
                        $CRPFinalSpare->selling_price = $item->spare_price; // For Out of Warranty Product
                        $outof_warranty_product += 1;
                    }
                    $new_barcode_no = "RETSPR" . $item->sp_barcode;
                    $barcodeGeneratorWithNo = barcodeGeneratorWithNo($new_barcode_no);
                    // Generate Old Barcode
                    // $uniue_id = 'RTRN'.genAutoIncreNoYearWiseOrder(4,'return_spare',date('Y'),date('m'));
                    // $barcodeGeneratorWithNo = barcodeGeneratorWithNo($uniue_id);
                    $code_html = $barcodeGeneratorWithNo['code_html'];
                    $code_base64_img = $barcodeGeneratorWithNo['code_base64_img'];

                    // $barcodeGenerator = genAutoIncreNoBarcode($item->spare_id,date('Y'),'return_spare');
                    // $barcode_no = $barcodeGenerator['barcode_no'];
                    // $code_html = $barcodeGenerator['code_html'];
                    // $code_base64_img = $barcodeGenerator['code_base64_img'];

                    $CRPFinalSpare->old_barcode = $item->sp_barcode;
                    $CRPFinalSpare->new_barcode = $new_barcode_no;
                    $CRPFinalSpare->new_code_html = $code_html ?: "";
                    $CRPFinalSpare->new_code_base64_img = $code_base64_img ?: "";
                    $CRPFinalSpare->crp_id = $item->crp_id;
                    $CRPFinalSpare->spare_id = $item->spare_id;
                    $CRPFinalSpare->product_damage = $item->product_damage;
                    $CRPFinalSpare->warranty_status = $item->warranty_status;
                    $CRPFinalSpare->created_at = now();
                    $CRPFinalSpare->save();

                    $final_amount += $CRPFinalSpare->selling_price;
                    $spare_charge += $item->spare_price;
                }

                // Fetching repair and service charges
                $crp_data = CustomerPointService::find($data[0]->crp_id);
                $service_charge = ServicePartnerCharge::select('repair')->where('service_partner_id', $crp_data->assign_service_perter_id)
                    ->where('product_id', $crp_data->product_id)->first();

                if (!$service_charge) {
                    return response()->json(['status' => false, 'message' => "Service charge not found for you of this product"]);
                }

                
                $repair_charge = Product::where('id', $crp_data->product_id)->value('repair_charge');
                $repair_charge = $repair_charge ? $repair_charge: 0;

                if ($repair_charge == 0) {
                    return response()->json(['status' => false, 'message' => "No assigned repair charge for this product"]);
                }

                // Calculate final amount and prepare response
                if ($outof_warranty_product > 0) {
                    $repair_charge = $service_charge_warranty==1?0:$repair_charge;
                    $response['service_charge'] = $repair_charge;
                    $response['final_amount'] = $final_amount + $repair_charge;
                    $response['status'] = 0;
                    $response['status_response'] = "OutSide Warranty";
                } else {
                    $response['service_charge'] = 0;
                    $response['final_amount'] = $final_amount;
                    $response['status'] = 1;
                    $response['status_response'] = "Within Warranty";
                }

                // Update CRP data
                $crp_data->repair_charge = $repair_charge;
                $crp_data->spare_charge = $spare_charge;
                $crp_data->status = 5; //Invoice Generated For Customer
                $crp_data->in_warranty = $response['status']; // here $response['status'] == 0 means uotside warranty and 1 means in warranty
                $crp_data->total_amount = $response['final_amount'];
                $crp_data->final_amount = $response['final_amount'];
                $crp_data->total_service_charge = $service_charge->repair;
                $crp_data->save();
                // dd($crp_data);
                // Commit the transaction
                DB::commit();
                return response()->json(['status' => true, 'data' => $response]);
            } catch (\Exception $e) {
                // Rollback the transaction in case of error
                // dd($e->getMessage());
                DB::rollBack();
                return response()->json(['status' => false, 'message' => 'Transaction failed: ' . $e->getMessage()]);
            }
        } else {
            return response()->json(['status' => false, 'message' => 'Kindly choose a spare part.']);
        }
    }
    public function quotation_send_customer($crp_id){

        $data = CustomerPointService::find($crp_id);
        if($data){
            $product_name = $data->item;
            if (strlen($product_name) > 30) {
                $product_name = substr($product_name, 0, 28) . '..';
            }
            $mobile = $data->alternate_no;
            $call_id = $data->unique_id;
            $final_amount = $data->final_amount;
            $otp = rand(1000,9999);
            
            $checkPhoneNumberValid = checkPhoneNumberValid($mobile);
            if($checkPhoneNumberValid){
                $query_calling_number = "6291117317";
                    
                $sms_entity_id = getSingleAttributeTable('settings','id',1,'sms_entity_id');
                $sms_template_id = "1707172110563286721";
                
        
                $myMessage = urlencode('Your '.$product_name.' Call ID '.$call_id.' repair charge is '.$final_amount.'. To accept the repair please share OTP '.$otp.' with customer care '.$query_calling_number.' AMMRTL');
        
        
                $sms_url = 'https://sms.bluwaves.in/sendsms/bulk.php?username=ammrllp&password=123456789&type=TEXT&sender=AMMRTL&mobile='.$mobile.'&message='.$myMessage.'&entityId='.$sms_entity_id.'&templateId='.$sms_template_id;
        
                // // echo $myMessage; die;
        
                $curl = curl_init();
        
                curl_setopt_array($curl, array(
                CURLOPT_URL => $sms_url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                ));
        
                $response = curl_exec($curl);
                curl_close($curl);
                // echo '<pre>'; echo $response; 
                DB::table('sms_api_response')->insert([
                    'sms_template_id' => $sms_template_id,
                    'sms_entity_id' => $sms_entity_id,
                    'phone' => $mobile,
                    'message_body' => $myMessage,
                    'response_body' => $response,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                $data->send_otp = $otp;
                $data->send_otp_time = date('Y-m-d H:i:s');
                $data->otp_verified = 0;
                $data->save();
                return response()->json(['status' => true, 'message' => "OTP has been sent successfully"], 200);
            }else{
                return response()->json(['status' => false, 'message' => "Mobile number must be 10 digits"], 200);
            }
        }else{
            return response()->json(['status' => false, 'message' => "Data not found!"], 200);
        }
    }
    public function quotation_otp_verify(Request $request){
        DB::beginTransaction();
        try {
            $CRP_data = CustomerPointService::find($request->crp_id);
            if (!$CRP_data) {
                DB::rollBack();
                return Response::json(['status' => false, 'message' => 'Service not found'], 200);
            }
            $mobile = $CRP_data->alternate_no ?? 'N/A';
            $mobile2 = strval($mobile);// mobile no string
            $checkPhoneNumberValid = checkPhoneNumberValid($mobile);
            if(!$checkPhoneNumberValid){
                DB::rollBack();
                return Response::json(['status' => false, 'message' => 'Mobile number must be 10 digits'], 200);
            }
    
            $existing_otp = $CRP_data->send_otp;
            $amount = $CRP_data->final_amount;
            $name = $CRP_data->customer_name ?? 'N/A';
          
            
            if ($existing_otp != $request->otp) {
                DB::rollBack();
                return Response::json(['status' => false, 'message' => 'The OTP you entered does not match our records'], 200);
            } else {
                 $crp_id = $request->crp_id;
                 
                // $DapServicePayment = DapServicePayment::where('dap_service_id',$crp_id)->get();
               
                // Your success logic here
                // For example, updating the record to mark OTP as verified
                $url = env('CASHFREE_BASE_URL')."/pg/orders";

                $headers = array(
                    "Content-Type: application/json",
                    "x-api-version: ".env('CASHFREE_API_VERSION'),
                    "x-client-id: ".env('CASHFREE_API_KEY'),
                    "x-client-secret: ".env('CASHFREE_API_SECRET')
                );
                $return_url = route('crp_payment_success');
               
                $data = json_encode([
                     'order_id' =>  'order_'.time().'_'.rand(11111,99999),
                     'order_amount' => $amount,
                     "order_currency" => "INR",
                     "customer_details" => [
                          "customer_id" => 'customer_'.time().'_'.rand(11111,99999),
                          "customer_name" => $name,
                          "customer_phone" => $mobile2,
                     ],
                     "order_meta" => [
                          'return_url' => $return_url . '/?order_id={order_id}&order_token={order_token}&crp_id=' . $crp_id
                          ]
                ]);
               
                $curl = curl_init($url);
                
                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
              
                $resp = curl_exec($curl);
                curl_close($curl);
                $link = json_decode($resp)->payment_link;
               
                $CRP_data->otp_verified = 1;
                $CRP_data->is_paid = 2; //Pending Or Link Send
                $CRP_data->status = 6; //Peyment link Send and waiting for payment
                $CRP_data->save();
                DB::table('crp_payment_links')->updateOrInsert(
                    ['crp_id' => $CRP_data->id],  // The condition to check for existing record
                    [
                        'link' => $link,            // The values to update or insert
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]
                );
                $record = DB::table('crp_payment_links')->where('crp_id', $CRP_data->id)->first();
                if($link){
                    DB::commit();
                    $url_link = route('CRP_payment_link',['d'=>$record->id]);  
                    $query_calling_number = "6291117317";
                
                    $sms_entity_id = getSingleAttributeTable('settings','id',1,'sms_entity_id');
                    $sms_template_id = "1707172234124956959";
                    
                    $myMessage = urlencode('We are pleased to inform you that your product repair charge is now ready for payment. Kindly use the following link to complete the transaction: '.$url_link.' .AMMR TECHNOLOGY LLP');

                    $sms_url = 'https://sms.bluwaves.in/sendsms/bulk.php?username=ammrllp&password=123456789&type=TEXT&sender=AMMRTL&mobile='.$mobile.'&message='.$myMessage.'&entityId='.$sms_entity_id.'&templateId='.$sms_template_id;
                 
                    // // echo $myMessage; die;
            
                    $curl = curl_init();
            
                    curl_setopt_array($curl, array(
                    CURLOPT_URL => $sms_url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    ));

                    $response = curl_exec($curl);
                    curl_close($curl);
                    return Response::json(['status' => true, 'message' => 'OTP has been successfully verified. A payment link has been sent to customer']);
                }else{
                    DB::rollBack();
                    return Response::json(['status' => false, 'message' => 'An error occurred while verifying OTP']);
                }
                
            }
        } catch (\Exception $e) {
            // dd($e->getMessage());
            DB::rollBack();
            return Response::json(['status' => false, 'message' => 'An error occurred while verifying OTP', 'error' => $e->getMessage()], 200);
        }
    }

    public function offline_payment(Request $request){
        try{
            $crp_id = $request->crp_id;
            $data = CustomerPointService::find($crp_id);
            if($data->status == 7){
                return Response::json(['status' => false, 'message' => 'Payment Already done'], 200);
            }
            if($data){
                $offline_pay_amount = $data->final_amount;
                $data->status = 7; //paid
                $data->is_paid = 1; //paid
                $data->payment_date = now(); 
                $data->payment_method = "cash"; 
                $data->save();
                if($data){    
                    $CrpServicePayment = new CrpServicePayment;
                    $CrpServicePayment->payment_id = 'PAY_OFFLINE'.date('dmy').'_'.rand(11111,99999);
                    $CrpServicePayment->crp_service_id = $crp_id;
                    $CrpServicePayment->status = 'PAID';
                    $CrpServicePayment->invoice_id = generateInvoiceId();
                    $CrpServicePayment->customer_name = $data->customer_name;
                    $CrpServicePayment->customer_phone = $data->alternate_no;
                    $CrpServicePayment->amount = $offline_pay_amount;
                    $CrpServicePayment->type = 'offline';
                    $CrpServicePayment->payment_date = date('Y-m-d');
                    $CrpServicePayment->created_at = Carbon::now();
                    $CrpServicePayment->save();
                // Ledger Entry
                $ledgerData = [
                    'type' => 'debit',
                    'service_partner_id' => $data->assign_service_perter_id,
                    'amount' => $offline_pay_amount,
                    'entry_date' => date('Y-m-d'),
                    'user_type' => 'servicepartner',
                    'purpose' => 'Customer Point Repair(Cash Payment)',
                    'transaction_id' => $data->unique_id,
                    'crp_id' => $data->id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                Ledger::insert($ledgerData);
                    $product_name = $data->item;
                    if (strlen($product_name) > 30) {
                        $product_name = substr($product_name, 0, 28) . '..';
                    }
                    $mobile = $data->alternate_no;
                    $call_id = $data->unique_id;
                    $download_url = route('c_invoice',['d'=>$data->id]);

                    
                    // $final_amount = ($data->total_amount + $data->total_service_charge) - $data->discount_amount;
                    $query_calling_number = "6291117317";
                    $sms_entity_id = getSingleAttributeTable('settings', 'id', 1, 'sms_entity_id');
                    $sms_template_id = "1707172846606238636";
                    // Your product {#var#} Call ID {#var#} has been repaired. Click to download bill {#var#}. For assistance call 6291117317.AMMRTL
                    // Your product {#var#} Call ID {#var#} has been repaired. Click to download bill {#download_url#}. For assistance call 6291117317.AMMRTL
                    $myMessage = urlencode('Your product ' . $product_name . ' Call ID ' . $call_id . ' has been repaired. Click to download bill '.$download_url.'. For assistance call 6291117317.AMMRTL');
                    
                    $sms_url = 'https://sms.bluwaves.in/sendsms/bulk.php?username=ammrllp&password=123456789&type=TEXT&sender=AMMRTL&mobile=' . $mobile . '&message=' . $myMessage . '&entityId=' . $sms_entity_id . '&templateId=' . $sms_template_id;
                    
                    $curl = curl_init();
                    curl_setopt_array($curl, array(
                        CURLOPT_URL => $sms_url,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'GET',
                    ));
                    
                    $response = curl_exec($curl);
                    // dd($response);
                    curl_close($curl);
                    
                    DB::table('sms_api_response')->insert([
                        'sms_template_id' => $sms_template_id,
                        'sms_entity_id' => $sms_entity_id,
                        'phone' => $mobile,
                        'message_body' => $myMessage,
                        'response_body' => $response,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                }
                return Response::json(['status' => true, 'message' => 'Payment successfull by cash'], 200);
                
                
            }else{
                return Response::json(['status' => false, 'message' => 'No Service record found'], 200);
            }

        } catch (\Exception $e) {
            // Handle any errors or exceptions
            return Response::json(['status' => false, 'message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
    
    public function regenerate_payment_link($crp_id){
        $CRP_data = CustomerPointService::find($crp_id);
        $CRP_data->otp_verified = 0;
        $CRP_data->is_paid = 0;
        $CRP_data->status = 5;
        $CRP_data->save();
        if($CRP_data){
            
            return Response::json(['status' => true, 'message' => 'success'], 200);
        }else{
            
            return Response::json(['status' => false, 'message' => 'failed'], 200);
        }

    }
}