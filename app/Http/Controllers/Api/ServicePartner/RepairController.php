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
use App\Models\Repair;
use App\Models\RepairSpare;
use App\Models\Settings;
use App\Models\Product;
use App\Models\PurchaseOrderProduct;
use App\Models\Ledger;

class RepairController extends Controller
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
        # code...
        # open service notification list...
        $service_partner_id = $this->service_partner_id;
        // $service_partner_id = 17;
        $from_date = !empty($request->from_date)?$request->from_date:date('Y-m-d');
        $to_date = !empty($request->to_date)?$request->to_date:date('Y-m-d', strtotime("-3 days"));
        $take = !empty($request->take)?$request->take:20;
        $page = isset($request->page)?$request->page:0;
        $skip = ($take*$page);

        $data = Repair::select('id','service_partner_id','dealer_user_id','dealer_user_name','service_partner_email','pincode','order_date','bill_no','customer_name','customer_phone','product_value','product_sl_no','product_name','is_closed','snapshot_file','created_at',
        \DB::raw("(CASE 
        
        WHEN DATE_FORMAT(created_at,'%Y-%m-%d') = '".date('Y-m-d',strtotime('-1 days'))."' THEN 'yellow' 
        WHEN DATE_FORMAT(created_at,'%Y-%m-%d') = '".date('Y-m-d')."' THEN 'green' 
        WHEN DATE_FORMAT(created_at,'%Y-%m-%d') < '".date('Y-m-d')."' THEN 'red' 
        END) AS pending_status"))
        ->where('service_partner_id',$service_partner_id)
        ->where('is_closed', 0)->where('is_cancelled',0)
        // ->whereBetween(DB::raw('DATE(created_at)'), [$to_date,$from_date])
        ->orderBy('id','asc')
        ->skip($skip)->take($take)->get();

        $count_data = Repair::where('service_partner_id',$service_partner_id)
        ->where('is_closed', 0)->where('is_cancelled',0)
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
            'message' => "Pending Repair list",
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

        $data = Repair::select('*')
        ->where('service_partner_id',$service_partner_id)
        ->where('is_closed', 1)
        ->orderBy('id','asc')
        ->skip($skip)->take($take)->get();

        $count_data = Repair::where('service_partner_id',$service_partner_id)
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
            'message' => "Closed Repair list",
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
            'id' => 'required|exists:repairs,id'
        ]);

        // dd($request->all());

        if(!$validator->fails()){
            $params = $request->except('_token');
            $id = $params['id'];
            // dd($id);
            $data = Repair::find($id);
            
            if($data->service_partner_id == $this->service_partner_id){
                $otp = random_int(100000, 999999);

                $customer_name = $data->customer_name;
                $customer_phone = $data->customer_phone;

                // echo 'otp:- '.$otp.'<br/>'; 
                if(empty($data->is_closed)){
                    $closing_otp_started_at = date('Y-m-d H:i');
                    $closing_otp_expired_at = date('Y-m-d H:i', strtotime("+7 days"));

                    $this->sendOTPCustomer($id,$otp,$customer_name,$customer_phone);
                    // $this->sendOTPCustomer($id,$otp,'Rohit Das','6290391954'); # Rohit Das Phone

                    Repair::where('id',$id)->update([
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
        $service_partner_id = $this->service_partner_id;
        $validator = Validator::make($request->all(),[
            'id' => 'required|exists:installations,id',
            'otp' => 'required|min:6|max:6'
        ]);

        if(!$validator->fails()){
            $params = $request->except('_token');
            $id = $params['id'];
            $otp = $params['otp'];
            $data = Repair::find($id);
            $service_charge = $data->service_charge;
            $unique_id = $data->unique_id;

            if($data->service_partner_id == $service_partner_id){
                
                if(empty($data->is_closed)){
                    
                    $now = date('Y-m-d H:i');

                    if($now <= $data->closing_otp_expired_at){
                        if($data->closing_otp == $otp){
                            Repair::where('id',$id)->update([
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

    public function add_spares(Request $request)
    {
        $service_partner_id = $this->service_partner_id;
        # Add Spares To Repair Request...
        $validator = Validator::make($request->all(),[
            'repair_id' => 'required|exists:repairs,id',
            'details.*.product_id' => 'required|exists:products,id',
            'details.*.quantity' => 'required|numeric|max:2'
        ]);

        if(!$validator->fails()){
            $params = $request->except('_token');            
            $repair_id = $params['repair_id'];

            $details = $params['details'];
            $items = json_decode($details);

            $get_repair_spares = RepairSpare::where('repair_id',$repair_id)->get()->toArray();
            
            if(!empty($get_repair_spares)){
                return Response::json(['status' => false, 'message' => "Already spares added for this repair request" , 'data' => array(  ) ],200);
            }



            $repair = Repair::find($repair_id);
            $goods_id = $repair->product_id;
            $in_warranty = $repair->in_warranty;
            $unique_id = $repair->unique_id;
            $product = Product::find($goods_id);
            $repair_charge = !empty($product->repair_charge)?$product->repair_charge:0;

            $total_spare_charge = 0;
            foreach($details as $item){

                ## If Out of warranty set goods repair charge and spare charges

                $spare_id = isset($item->product_id)?$item->product_id:NULL;
                $quantity = isset($item->quantity)?$item->quantity:NULL;

                $highest_spare_cost_price_po = PurchaseOrderProduct::where('product_id',$spare_id)->max('cost_price');

                $spare_profit_percentage = getSingleAttributeTable('products','id',$spare_id,'profit_percentage');

                $spare_name = getSingleAttributeTable('products','id',$spare_id,'title');

                if(empty($in_warranty)){
                    if(empty($highest_spare_cost_price_po)){
                        return Response::json([
                            'status' => false, 
                            'message' => "No Spare Price Found For ".$spare_name." . Please talk to admin",
                            'data' => (object) array()
                        ],200);
                        
                    }
                }

                if(empty($spare_profit_percentage)){
                    return Response::json([
                        'status' => false, 
                        'message' => "No Profit Percentage Added For Spare From Master . Please talk to System Admin",
                        'data' => (object) array()
                    ],200);
                }


            }
            

           




            foreach($items as $item){

                $highest_spare_cost_price_po = PurchaseOrderProduct::where('product_id',$item->product_id)->max('cost_price');

                $spare_profit_percentage = getSingleAttributeTable('products','id',$item->product_id,'profit_percentage');

                $highest_spare_cost_price_po = !empty($highest_spare_cost_price_po)?$highest_spare_cost_price_po:0;
                $spare_profit_val = getPercentageVal($spare_profit_percentage,$highest_spare_cost_price_po);
                $spare_price = ($highest_spare_cost_price_po + $spare_profit_val);
                $spare_charge = ($quantity*$spare_price);
                $total_spare_charge += $spare_charge;


                $spareArr = array(
                    'repair_id' => $repair_id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'cost_price' => $highest_spare_cost_price_po,
                    'profit_percentage' => $spare_profit_percentage,
                    'spare_profit_val' => $spare_profit_val,
                    'total_spare_charge' => $spare_charge,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                );
                // dd($spareArr);
                RepairSpare::insert($spareArr);
            }
            $total_service_charge = ($repair_charge+$total_spare_charge);

            ## Ledger Debit Entry Total Service Charge For Out Of Warranty

            if(empty($in_warranty)){
                $this->ledgerOutOfWarrantyCharge($repair_id,$service_partner_id,$unique_id,$total_service_charge);
            }

            // dd($items);

            $repair = Repair::find($repair_id);
            $customer_name = $repair->customer_name;
            $customer_phone = $repair->customer_phone;

            $otp = random_int(100000, 999999);
            $closing_otp_started_at = date('Y-m-d H:i');
            $closing_otp_expired_at = date('Y-m-d H:i', strtotime("+7 days"));


            $this->sendOTPCustomer($repair_id,$otp,$customer_name,$customer_phone);
            Repair::where('id',$repair_id)->update([
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
            return Response::json(['status' => false, 'message' => $validator->errors()->first() , 'data' => array( $validator->errors() ) ],400);
        }


    }

    private function sendOTPCustomer($id,$otp,$customer_name,$customer_phone){

        $sms_entity_id = getSingleAttributeTable('settings','id',1,'sms_entity_id');
        // $sms_template_id = getSingleAttributeTable('settings','id',1,'sms_template_id');
        $sms_template_id = "1707173107738290074";

        $checkPhoneNumberValid = checkPhoneNumberValid($customer_phone);
        if($checkPhoneNumberValid){
            
            $sender = 'AMMRTL';
            // $csat_base_url = 'https://kgaelectronics.com/retailer/feedback/';
            $csat_base_url = 'https://kgaerp.in/retailer/feedback/';
            $ins_rep_end_point = 'form-repair?id=';
            $ins_rep_id = $id;
            $csat_full_url = $csat_base_url.''.$ins_rep_end_point.''.$ins_rep_id;

            // Kindly share OTP with engineer {#var#} after service/repair, Provide feedback at https://kgaerp.in/retailer/feedback{#var#} AMMRTL
            $myMessage = urlencode("Kindly share OTP with engineer ".$otp." after service/repair, Provide feedback at ".$csat_full_url." AMMRTL");

            $sms_url = 'https://sms.bluwaves.in/sendsms/bulk.php?username=ammrllp&password=123456789&type=TEXT&sender='.$sender.'&mobile='.$customer_phone.'&message='.$myMessage.'&entityId='.$sms_entity_id.'&templateId='.$sms_template_id;

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
                'phone' => $customer_phone,
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
            'purpose' => 'repair',
            'transaction_id' => $unique_id,
            'repair_id' => $id,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        );

        Ledger::insert($ledgerData);
    }

    private function ledgerOutOfWarrantyCharge($repair_id,$service_partner_id,$unique_id,$amount){

        ## Check exist ledger entry

        $checkExistLedger = Ledger::where('purpose','repair_charges')->where('repair_id',$repair_id)->first();

        if(empty($checkExistLedger)){
            $ledgerArr = array(
                'type' => 'debit',
                'user_type' => 'servicepartner',
                'service_partner_id' => $service_partner_id,
                'repair_id' => $repair_id,
                'amount' => $amount,
                'entry_date' => date('Y-m-d'),
                'purpose' => 'repair_charges',
                'transaction_id' => $unique_id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            );
    
            Ledger::insert($ledgerArr);
        } else {
            $ledgerArr = array(                
                'amount' => $amount,
                'entry_date' => date('Y-m-d'),
                'updated_at' => date('Y-m-d H:i:s')
            );
    
            Ledger::where('id', $checkExistLedger->id)->update($ledgerArr);
        }

        
    }


}
