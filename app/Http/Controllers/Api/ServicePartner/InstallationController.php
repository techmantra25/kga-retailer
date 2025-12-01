<?php

namespace App\Http\Controllers\Api\ServicePartner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\DB;
use File;
use App\Models\ServicePartner;
use App\Models\Pincode;
use App\Models\ServicePartnerPincode;
use App\Models\Installation;
use App\Models\Settings;
use App\Models\Ledger;
use App\Models\Payment;

class InstallationController extends Controller
{
    //
    private $service_partner_id;
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
                $this->service_partner_id = Crypt::decrypt($token);
                $staff = ServicePartner::find($this->service_partner_id);           
            } catch (DecryptException $e) {
                response()->json(["status"=>false,"message"=>"Mismatched token"],400)->send();
                exit();
            }
        }
    }

    public function list(Request $request)
    {
        # open service notification list...
        $service_partner_id = $this->service_partner_id;
        // $service_partner_id = 17;
        $from_date = !empty($request->from_date)?$request->from_date:date('Y-m-d');
        $to_date = !empty($request->to_date)?$request->to_date:date('Y-m-d', strtotime("-3 days"));
        $take = !empty($request->take)?$request->take:20;
        $page = isset($request->page)?$request->page:0;
        $skip = ($take*$page);

        $data = Installation::select('id','unique_id','service_partner_id','dealer_user_id','service_partner_email','pincode','branch','entry_date','bill_no','customer_name','address','district','mobile_no','phone_no','delivery_date','brand','class AS class_name','salesman','salesman_mobile_no','product_value','product_sl_no','product_name','csv_file_name','is_closed','is_urgent','snapshot_file','created_at',
        \DB::raw("(CASE 
        
        WHEN DATE_FORMAT(created_at,'%Y-%m-%d') = '".date('Y-m-d',strtotime('-1 days'))."' THEN 'yellow' 
        WHEN DATE_FORMAT(created_at,'%Y-%m-%d') = '".date('Y-m-d')."' or DATE_FORMAT(created_at,'%Y-%m') = '".date('Y-m-d 23:10', strtotime('-1 days'))."' THEN 'green' 
        WHEN DATE_FORMAT(created_at,'%Y-%m-%d') < '".date('Y-m-d')."' THEN 'red' 
        END) AS pending_status"))
        ->where('service_partner_id',$service_partner_id)
        ->where('is_closed', 0)->where('is_cancelled',0) // only pending list
        // ->whereBetween(DB::raw('DATE(created_at)'), [$to_date,$from_date])
        ->orderBy('id','asc')
        ->skip($skip)->take($take)->get();

        $count_data = Installation::where('service_partner_id',$service_partner_id)
        ->where('is_closed', 0)->where('is_cancelled',0)  //// only pending list
        // ->whereBetween(DB::raw('DATE(created_at)'), [$to_date,$from_date])
        ->count();

        $isPrev = 0;
        $isNext = 0;

        if($page == 0){
            if($count_data > $take){
                $isNext = 1;
            }
        } else {
            if($page > 0){
                $isPrev = 1;
                $page = ($page + 1);
                $skips = ($take * $page);
                // echo $skips; die;
                if($skips < $count_data){
                    $isNext = 1;
                } 
            }
        }

        return Response::json([
            'status' => true,
            'message' => "Notification list",
            'data' => array(
                'count_data' => $count_data,
                'isPrev' => $isPrev,
                'isNext' => $isNext,
                'list' => $data
            )
        ]);

    }

    public function closed_list(Request $request)
    {
        # all closed list...

        $service_partner_id = $this->service_partner_id;
        // $service_partner_id = 10;
        
        $take = !empty($request->take)?$request->take:20;
        $page = isset($request->page)?$request->page:0;
        $skip = ($take*$page);

        $data = Installation::select('id','service_partner_id','dealer_user_id','service_partner_email','pincode','branch','entry_date','bill_no','customer_name','address','district','mobile_no','phone_no','delivery_date','brand','class AS class_name','salesman','salesman_mobile_no','product_value','product_sl_no','product_name','csv_file_name','is_closed','is_urgent','snapshot_file','created_at')
        ->where('service_partner_id',$service_partner_id)
        ->where('is_closed', 1)
        ->orderBy('id','asc')
        ->skip($skip)->take($take)->get();

        $count_data = Installation::where('service_partner_id',$service_partner_id)
        ->where('is_closed', 1)
        ->count();

        $isPrev = 0;
        $isNext = 0;

        if($page == 0){
            if($count_data > $take){
                $isNext = 1;
            }
        } else {
            if($page > 0){
                $isPrev = 1;
                $page = ($page + 1);
                $skips = ($take * $page);
                // echo $skips; die;
                if($skips < $count_data){
                    $isNext = 1;
                } 
            }
        }

        return Response::json([
            'status' => true,
            'message' => "Notification list",
            'data' => array(
                'count_data' => $count_data,
                'isPrev' => $isPrev,
                'isNext' => $isNext,
                'list' => $data
            )
        ]);

    }

    public function request_to_close(Request $request)
    {
        # request OTP to close the call...
        $validator = Validator::make($request->all(),[
            'id' => 'required|exists:installations,id'
        ]);

        // dd($request->all());

        if(!$validator->fails()){
            $params = $request->except('_token');
            $id = $params['id'];
            // dd($id);
            $data = Installation::find($id);
            
            if($data->service_partner_id == $this->service_partner_id){
                $otp = random_int(100000, 999999);

                $customer_name = $data->customer_name;
                $customer_mobile_no = $data->mobile_no;
                // echo 'otp:- '.$otp.'<br/>'; 
                if(empty($data->is_closed)){
                    $closing_otp_started_at = date('Y-m-d H:i');
                    $closing_otp_expired_at = date('Y-m-d H:i', strtotime("+7 days"));

                    $this->sendOTPCustomer($id,$otp,$customer_name,$customer_mobile_no);
                    // $this->sendOTPCustomer($id,$otp,'Rohit Das','6290391954'); # Rohit Das Phone
                    
                    Installation::where('id',$id)->update([
                        'closing_otp' => $otp,
                        'closing_otp_started_at' => $closing_otp_started_at,
                        'closing_otp_expired_at' => $closing_otp_expired_at
                    ]);

                    return Response::json([
                        'status' => true, 
                        'message' => "A new OTP has been generated. Please use it within +7 days.",
                        'data' => array(
                            'otp' => $otp
                        )
                    ],200);

                } else {
                    return Response::json(['status' => false, 'message' => "This call is already closed" ],200);
                }

            } else {
                return Response::json(['status' => false, 'message' => "This is not your service call" ],200);
            }
            
        } else {
            return Response::json(['status' => false, 'message' => $validator->errors()->first() , 'data' => array( $validator->errors() ) ],400);
        }

    }

    public function submit_close(Request $request)
    {
        # close service via otp...
        $validator = Validator::make($request->all(),[
            'id' => 'required|exists:installations,id',
            'otp' => 'required|min:6|max:6'
        ]);

        if(!$validator->fails()){
            $params = $request->except('_token');
            $id = $params['id'];
            $otp = $params['otp'];
            $data = Installation::find($id);
            $service_charge = $data->service_charge;
            $unique_id = $data->unique_id;
            $product_id = $data->product_id;

            if($data->service_partner_id == $this->service_partner_id){
                
                if(empty($data->is_closed)){
                    
                    $now = date('Y-m-d H:i');

                    if($now <= $data->closing_otp_expired_at){
                        if($data->closing_otp == $otp){
                            Installation::where('id',$id)->update([
                                'closing_otp' => null,
                                'closing_otp_started_at' => null,
                                'closing_otp_expired_at' => null,
                                'is_closed' => 1
                            ]);

                            # Ledger Entry Service Partner
                            $this->ledgerEntryCallClose($this->service_partner_id,$service_charge,$unique_id,$id);
                            $this->ledgerDebitEntryCallClose($this->service_partner_id,$product_id,$id);

                            return Response::json([
                                'status' => true, 
                                'message' => "Call closed successfully via OTP",
                                'data' => array(
                                    'id' => $id,
                                    'otp' => $otp
                                )
                            ],200);
                        } else {
                            return Response::json(['status' => false, 'message' => "Wrong OTP" ],200);
                        }
                        
    
                    } else {
                        return Response::json(['status' => false, 'message' => "OTP is expired" ],200);
                    }

                    
                } else {
                    return Response::json(['status' => false, 'message' => "This call is already closed" ],200);
                }

            } else {
                return Response::json(['status' => false, 'message' => "This is not your service call" ],200);
            }



        } else {
            return Response::json(['status' => false, 'message' => $validator->errors()->first() , 'data' => array( $validator->errors() ) ],400);
        }
    }

    private function sendOTPCustomer($id,$otp,$customer_name,$customer_mobile_no){

        $sms_entity_id = getSingleAttributeTable('settings','id',1,'sms_entity_id');
        // $sms_template_id = getSingleAttributeTable('settings','id',1,'sms_template_id');
        $sms_template_id = "1707173107738290074";

        $checkPhoneNumberValid = checkPhoneNumberValid($customer_mobile_no);
        if($checkPhoneNumberValid){
            $sender = 'AMMRTL';
            // $csat_base_url = 'https://kgaelectronics.com/retailer/feedback/';
            $csat_base_url = 'https://kgaerp.in/retailer/feedback/';
            $ins_rep_end_point = 'form-installation?id=';
            $ins_rep_id = $id;
            $csat_full_url = $csat_base_url.''.$ins_rep_end_point.''.$ins_rep_id;

            // Kindly share OTP with engineer {#var#} after service/repair, Provide feedback at https://kgaerp.in/retailer/feedback{#var#} AMMRTL
            $myMessage = urlencode("Kindly share OTP with engineer ".$otp." after service/repair, Provide feedback at ".$csat_full_url." AMMRTL");

            $sms_url = 'https://sms.bluwaves.in/sendsms/bulk.php?username=ammrllp&password=123456789&type=TEXT&sender='.$sender.'&mobile='.$customer_mobile_no.'&message='.$myMessage.'&entityId='.$sms_entity_id.'&templateId='.$sms_template_id;

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
                'phone' => $customer_mobile_no,
                'message_body' => $myMessage,
                'response_body' => $response,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

        }
        
    }

    private function ledgerEntryCallClose($service_partner_id,$amount,$unique_id,$id){
        $ledgerData = array(
            'type' => 'credit',
            'service_partner_id' => $service_partner_id,
            'amount' => $amount,
            'entry_date' => date('Y-m-d'),
            'user_type' => 'servicepartner',
            'purpose' => 'installation',
            'transaction_id' => $unique_id,
            'installation_id' => $id,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        );
        Ledger::insert($ledgerData);
    }
    private function ledgerDebitEntryCallClose($service_partner_id,$product_id,$installation_id){
        $product = Product::findOrFail($product_id);
        if(isset($product)){
            $amount = $product->installable_amount;
            if($amount>0){
                $params =[];
                unset($params['bank_name_hidden']);
                $params['service_partner_id'] = $service_partner_id;
                $params['amount'] = $amount;
                $params['user_type'] = "servicepartner";
                $params['payment_mode'] = "cash";
                $params['voucher_no'] = 'PAYT'.genAutoIncreNoYearWiseOrder(5,'payments',date('Y'),date('m'));
                $params['entry_date'] = date('Y-m-d');
                $params['created_at'] = date('Y-m-d H:i:s');
                $params['updated_at'] = date('Y-m-d H:i:s');
                
                $payment_id = Payment::insertGetId($params);
                $ledgerData = array(
                    'type' => 'debit',
                    'amount' => $amount,
                    'entry_date' => $params['entry_date'],
                    'user_type' => 'servicepartner',
                    'service_partner_id' => $service_partner_id,
                    'payment_id' => $payment_id,
                    'purpose' => 'payment',
                    'transaction_id' => $params['voucher_no'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                );
                $existLedger = Ledger::where('installation_id',$installation_id)->where('type', 'debit')->first();
                if(empty($existLedger)){
                    Ledger::insert($ledgerData);
                }
            }
        }
    }

}
