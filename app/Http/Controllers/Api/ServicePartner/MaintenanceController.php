<?php

namespace App\Http\Controllers\Api\ServicePartner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\DB;
use App\Models\ServicePartner;
use App\Models\Maintenance;
use App\Models\Ledger;
use App\Models\MaintenanceSpare;


class MaintenanceController extends Controller
{
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

    public function list($service_type='cleaning',Request $request)
    {
        # Maintenance Request List...
        $service_partner_id = $this->service_partner_id;
        // $service_partner_id =17;
        $take = !empty($request->take)?$request->take:20;
        $page = isset($request->page)?$request->page:0;
        $skip = ($take*$page);
        
        $data = Maintenance::with('dealer:id,name')->where('service_type',$service_type);
        $data = $data->where('service_partner_id',$service_partner_id)
        ->where('is_closed', 0)->where('is_cancelled',0)
        ->orderBy('id','asc')
        ->skip($skip)->take($take)->get();

        $count_data = Maintenance::where('service_partner_id',$service_partner_id)->where('service_type',$service_type)->where('is_closed', 0)->where('is_cancelled',0)->count();

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
            'message' => " ".ucwords($service_type)." Maintenance and Repair List ",
            'data' => array(
                'count_data' => $count_data,
                'isPrev' => $isPrev,
                'isNext' => $isNext,
                'list' => $data
            )
        ]);


    }
    // public function repaire_list($service_type='repairing',Request $request)
    // {
    //     # Maintenance Request List...
    //     $service_partner_id = $this->service_partner_id;
    //     // $service_partner_id =17;
    //     $take = !empty($request->take)?$request->take:20;
    //     $page = isset($request->page)?$request->page:0;
    //     $skip = ($take*$page);
        
    //     $data = Maintenance::with('dealer:id,name')->where('service_type',$service_type);
    //     $data = $data->where('service_partner_id',$service_partner_id)
    //     ->where('is_closed', 0)->where('is_cancelled',0)
    //     ->orderBy('id','asc')
    //     ->skip($skip)->take($take)->get();

    //     $count_data = Maintenance::where('service_partner_id',$service_partner_id)->where('service_type',$service_type)->where('is_closed', 0)->where('is_cancelled',0)->count();

    //     $isPrev = 0;
    //     $isNext = 0;

    //     if($page == 0){
    //         if($count_data > $take){
    //             $isNext = 1;
    //         }
    //     } else {
    //         if($page > 0){
    //             $isPrev = 1;
    //             $page = ($page + 1);
    //             $skips = ($take * $page);
    //             // echo $skips; die;
    //             if($skips < $count_data){
    //                 $isNext = 1;
    //             } 
    //         }
    //     }

    //     return Response::json([
    //         'status' => true,
    //         'message' => " ".ucwords($service_type)." Maintenance and Repair List ",
    //         'data' => array(
    //             'count_data' => $count_data,
    //             'isPrev' => $isPrev,
    //             'isNext' => $isNext,
    //             'list' => $data
    //         )
    //     ]);


    // }

    public function closed_list($service_type='cleaning',Request $request)
    {
        # all closed list...

        $service_partner_id = $this->service_partner_id;
        // $service_partner_id = 10;
        
        $take = !empty($request->take)?$request->take:20;
        $page = isset($request->page)?$request->page:0;
        $skip = ($take*$page);

        $data = Maintenance::with('dealer:id,name')->where('service_type',$service_type)
        ->where('service_partner_id',$service_partner_id)
        ->where('is_closed', 1)
        ->orderBy('id','asc')
        ->skip($skip)->take($take)->get();

        $count_data = Maintenance::where('service_type',$service_type)->where('service_partner_id',$service_partner_id)
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
            'message' => "Closed Maintenance list - ".ucwords($service_type)." Warranty ",
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
            'id' => 'required|exists:maintenances,id'
        ]);

        // dd($request->all());

        if(!$validator->fails()){
            $params = $request->except('_token');
            $id = $params['id'];
            // dd($id);
            $data = Maintenance::find($id);
            
            if($data->service_partner_id == $this->service_partner_id){
                $otp = random_int(100000, 999999);

                $customer_name = $data->customer_name;
                $customer_phone = $data->customer_phone;

                // echo 'otp:- '.$otp.'<br/>'; 
                if(empty($data->is_closed)){
                    $closing_otp_started_at = date('Y-m-d H:i');
                    $closing_otp_expired_at = date('Y-m-d H:i', strtotime("+7 days"));

                    $this->sendOTPCustomer($id,$otp,$customer_name,$customer_phone);

                    Maintenance::where('id',$id)->update([
                        'closing_otp' => $otp,
                        'closing_otp_started_at' => $closing_otp_started_at,
                        'closing_otp_expired_at' => $closing_otp_expired_at
                    ]);

                    return Response::json([
                        'status' => true, 
                        'message' => "A new OTP has been generated. Please use it within 7 days.",
                        'data' => array(
                            'otp' => $otp
                        )
                    ],200);

                } else {
                    return Response::json(['status' => false, 'message' => "This call is already closed" , 'data' => (object) array() ],200);
                }

            } else {
                return Response::json(['status' => false, 'message' => "This is not your service call" , 'data' => (object) array() ],200);
            }
            
        } else {
            return Response::json(['status' => false, 'message' => $validator->errors()->first() , 'data' => array( $validator->errors() ) ],400);
        }

    }

    public function submit_close(Request $request)
    {
        # Submit OTP to close the call...

        $service_partner_id = $this->service_partner_id;
        $validator = Validator::make($request->all(),[
            'id' => 'required|exists:maintenances,id',
            'otp' => 'required|min:6|max:6'
        ]);

        if(!$validator->fails()){
            $params = $request->except('_token');
            $id = $params['id'];
            $otp = $params['otp'];
            $data = Maintenance::find($id);
            $service_charge = $data->service_charge;
            $unique_id = $data->unique_id;

            if($data->service_partner_id == $service_partner_id){
                
                if(empty($data->is_closed)){
                    
                    $now = date('Y-m-d H:i');

                    if($now <= $data->closing_otp_expired_at){
                        if($data->closing_otp == $otp){
                            Maintenance::where('id',$id)->update([
                                'closing_otp' => null,
                                'closing_otp_started_at' => null,
                                'closing_otp_expired_at' => null,
                                'is_closed' => 1
                            ]);

                            # Ledger Entry Service Partner
                            $this->ledgerEntryCallClose($this->service_partner_id,$service_charge,$unique_id,$id);

                            return Response::json([
                                'status' => true, 
                                'message' => "Call closed successfully via OTP",
                                'data' => array(
                                    'id' => $id,
                                    'otp' => $otp
                                )
                            ],200);
                        } else {
                            return Response::json(['status' => false, 'message' => "Wrong OTP" , 'data' => (object) array() ],200);
                        }
                        
    
                    } else {
                        return Response::json(['status' => false, 'message' => "OTP is expired" , 'data' => (object) array() ],200);
                    }

                    
                } else {
                    return Response::json(['status' => false, 'message' => "This call is already closed" , 'data' => (object) array() ],200);
                }

            } else {
                return Response::json(['status' => false, 'message' => "This is not your service call" , 'data' => (object) array() ],200);
            }



        } else {
            return Response::json(['status' => false, 'message' => $validator->errors()->first() , 'data' => array( $validator->errors() ) ],400);
        }

    }

    public function add_spares(Request $request)
    {
        # Add Spares For Servicing...
        $validator = Validator::make($request->all(),[
            'maintenance_id' => 'required|exists:maintenances,id',
            'details.*.product_id' => 'required|exists:products,id',
            'details.*.quantity' => 'required|numeric|max:2'
        ]);

        if(!$validator->fails()){
            $params = $request->except('_token');            
            $maintenance_id = $params['maintenance_id'];

            $details = !empty($params['details'])?$params['details']:'';
            if(!empty($details)){
                $items = json_decode($details);

                $maintenance = Maintenance::find($maintenance_id);
                if(empty($maintenance->is_closed)){
                    $get_maintenance_spares = MaintenanceSpare::where('maintenance_id',$maintenance_id)->get()->toArray();
                    
                    if(!empty($get_maintenance_spares)){
                        return Response::json(['status' => false, 'message' => "Already spares added for this maintenance request" , 'data' => (object) array() ],200);
                    }

                    foreach($items as $item){
                        // dd($item->product_id);
                        $spareArr = array(
                            'maintenance_id' => $maintenance_id,
                            'product_id' => $item->product_id,
                            'quantity' => $item->quantity,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s')
                        );
                        // dd($spareArr);
                        MaintenanceSpare::insert($spareArr);
                    }
                    // dd($items);

                    $maintenance = Maintenance::find($maintenance_id);
                    $customer_name = $maintenance->customer_name;
                    $customer_phone = $maintenance->customer_phone;

                    $otp = random_int(100000, 999999);
                    $closing_otp_started_at = date('Y-m-d H:i');
                    $closing_otp_expired_at = date('Y-m-d H:i', strtotime("+7 days"));


                    $this->sendOTPCustomer($maintenance_id,$otp,$customer_name,$customer_phone);
                    Maintenance::where('id',$maintenance_id)->update([
                        'is_spare_added' => 1,
                        'closing_otp' => $otp,
                        'closing_otp_started_at' => $closing_otp_started_at,
                        'closing_otp_expired_at' => $closing_otp_expired_at
                    ]);

                    return Response::json([
                        'status' => true, 
                        'message' => " Spares added and a new OTP generated successfully",
                        'data' => array(
                            'otp' => $otp,
                            'params' => $params
                        )
                    ],200);
                } else {
                    return Response::json(['status' => false, 'message' => "Call already closed", 'data' => (object) array() ],200);
                }
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
            $ins_rep_end_point = 'form-maintenance?id=';
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

    private function ledgerEntryCallClose($service_partner_id,$amount,$unique_id){
        $ledgerData = array(
            'type' => 'credit',
            'service_partner_id' => $service_partner_id,
            'amount' => $amount,
            'entry_date' => date('Y-m-d'),
            'user_type' => 'servicepartner',
            'purpose' => 'maintenance',
            'transaction_id' => $unique_id,
            'maintenance_id' => $id,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        );

        Ledger::insert($ledgerData);
    }

    
}
