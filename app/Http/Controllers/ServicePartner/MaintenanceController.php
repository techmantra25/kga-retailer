<?php

namespace App\Http\Controllers\ServicePartner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Models\ServicePartner;
use App\Models\Maintenance;
use App\Models\MaintenanceSpare;
use App\Models\Ledger;


class MaintenanceController extends Controller
{
    public function __construct(Request $request)
    {
        $this->middleware('auth:servicepartner');
    }

    public function list(Request $request)
    {
        # List Maintenace...

        $paginate = 20;
        $search = !empty($request->search)?$request->search:'';
        $created_at = !empty($request->created_at)?$request->created_at:'';
        $closing_type = !empty($request->closing_type)?$request->closing_type:'';

        $data = Maintenance::where('service_partner_id', Auth::user()->id);
        $totalResult = Maintenance::where('service_partner_id', Auth::user()->id);

        if(!empty($search)){
            $data = $data->where('unique_id', 'LIKE', '%'.$search.'%');
            $totalResult = $totalResult->where('unique_id', 'LIKE', '%'.$search.'%');
        }

        if(!empty($created_at)){
            $data = $data->whereRaw(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d') = '".$created_at."'"));
            $totalResult = $totalResult->whereRaw(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d') = '".$created_at."'"));
        }
        
        if(!empty($closing_type)){
            if($closing_type == 'cancelled'){
                $data = $data->where('is_cancelled', 1);
                $totalResult = $totalResult->where('is_cancelled', 1);
                $data = $data->orderBy('id','desc')->paginate($paginate);
            } else if($closing_type == 'pending'){
                $data = $data->where('is_closed', 0)->where('is_cancelled', 0);
                $totalResult = $totalResult->where('is_closed', 0)->where('is_cancelled', 0);
                $data = $data->orderBy('id','asc')->paginate($paginate);
            } else {
                $data = $data->where('is_closed', 1);
                $totalResult = $totalResult->where('is_closed', 1);
                $data = $data->orderBy('id','desc')->paginate($paginate);
            }
            
        } else {
            # Default id desc
            $data = $data->orderBy('id','desc')->paginate($paginate);
        }

        // $data = $data->orderBy('id','desc')->paginate($paginate);
        $totalResult = $totalResult->count();

        $data = $data->appends([
            'search'=>$search,
            'created_at'=>$created_at,
            'page'=>$request->page,
            'closing_type'=>$closing_type
        ]);

        return view('servicepartnerweb.maintenance.list', compact('search','paginate','data','totalResult','created_at','closing_type'));

    }

    public function save_remark(Request $request)
    {        
        # save remark ...
        $request->validate([
            'remarks' => 'required'
        ]);
        $params = $request->except('_token');
        // dd($params);
        $request_url = $params['request_url'];

        Maintenance::where('id',$params['maintenance_id'])->update([
            'remarks' => $params['remarks']
        ]);

        // dd($params);

        Session::flash('message', "Remark added successfully.");
        return redirect('/servicepartnerweb/maintenance/list');
    }

    public function close_otp_request(Request $request,$idStr,$getQueryString='')
    {
        # Request OTP Close Repair...
        try {
            $id = Crypt::decrypt($idStr);
            // dd($getQueryString);
            $data = Maintenance::find($id);
            $otp = random_int(100000, 999999);

            $customer_name = $data->customer_name;
            $customer_mobile_no = $data->customer_phone;
            $unique_id = $data->unique_id;
            // echo 'otp:- '.$otp.'<br/>'; die;
            
            $closing_otp_started_at = date('Y-m-d H:i');
            $closing_otp_expired_at = date('Y-m-d H:i', strtotime("+7 days"));

            $this->sendOTPCustomer('repair',$id,$otp,$customer_name,$customer_mobile_no);
           
            Maintenance::where('id',$id)->update([
                'closing_otp' => $otp,
                'closing_otp_started_at' => $closing_otp_started_at,
                'closing_otp_expired_at' => $closing_otp_expired_at
            ]);
            
            Session::flash('message', 'OTP generated successfully for '.$unique_id);
            return redirect('/servicepartnerweb/maintenance/list?'.$getQueryString);
            
        } catch ( DecryptException $e) {
            return abort(404);
        }

    }

    public function submit_closing_otp(Request $request,$getQueryString='')
    {
        // dd($request->all());
        # Verify OTP And Close Call...
        $request->validate([
            'maintenance_id' => 'required|exists:maintenances,id',
            'otp' => 'required'
        ]);
        
        $params = $request->except('_token');
        // dd($getQueryString);
        $id = $params['maintenance_id'];
        $otp = $params['otp'];
        $data = Maintenance::find($id);
        $service_charge = $data->service_charge;
        $unique_id = $data->unique_id;
        $product_sl_no = $data->product_sl_no;
            
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
                $this->ledgerEntryCallClose('maintenance',Auth::user()->id,$service_charge,$unique_id,$id);

                

                Session::flash('message', 'Call closed successfully via OTP for '.$unique_id.'');
                return redirect('/servicepartnerweb/maintenance/list?'.$getQueryString);
            } else {
                return redirect('/servicepartnerweb/maintenance/list?'.$getQueryString)->withErrors(['otp'=>'Wrong OTP'])->withInput($request->all());
                
            }
            

        } else {
            return redirect('servicepartnerweb/maintenance/list?'.$getQueryString)->withErrors(['otp'=>'OTP is expired'])->withInput($request->all());
            
        }

    }

    private function sendOTPCustomer($type,$id,$otp,$customer_name,$customer_mobile_no){

        $sms_entity_id = getSingleAttributeTable('settings','id',1,'sms_entity_id');
        // $sms_template_id = getSingleAttributeTable('settings','id',1,'sms_template_id');
        $sms_template_id = "1707173107738290074";

        $checkPhoneNumberValid = checkPhoneNumberValid($customer_mobile_no);
        if($checkPhoneNumberValid){
            
            
            $ins_rep_end_point = 'form-maintenance?id=';
            


            $sender = 'AMMRTL';
            // $csat_base_url = 'https://kgaelectronics.com/retailer/feedback/'; 
            $csat_base_url = 'https://kgaerp.in/retailer/feedback/';
            $ins_rep_id = $id;
            $csat_full_url = $csat_base_url.''.$ins_rep_end_point.''.$ins_rep_id;
            // dd($csat_full_url);

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

    private function ledgerEntryCallClose($type,$service_partner_id,$amount,$unique_id,$id){

        $data = Maintenance::find($id);
        if($data->repeat_call === 1 && $data->repeat_id !== NULL){
            $pre_data = Ledger::where('maintenance_id',$data->repeat_id)->where('type','credit')->orderBy('id','DESC')->first();
            // dd($pre_data);
            $ledgerData = array(
                'type' => 'debit',
                'service_partner_id' => $pre_data->service_partner_id,
                'amount' => $pre_data->amount,
                'entry_date' => date('Y-m-d'),
                'user_type' => 'servicepartner',
                'purpose' => 'maintenance(repeat call)',
                'transaction_id' => $pre_data->transaction_id,
                'maintenance_id' => $pre_data->maintenance_id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            );
    
            Ledger::insert($ledgerData);
        }

        $ledgerData = array(
            'type' => 'credit',
            'service_partner_id' => $service_partner_id,
            'amount' => $amount,
            'entry_date' => date('Y-m-d'),
            'user_type' => 'servicepartner',
            'purpose' => $type,
            'transaction_id' => $unique_id,
            'maintenance_id' => $id,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        );

        Ledger::insert($ledgerData);
    }

    # +++++++++++++++++++++++++ Add Spare Parts +++++++++++++++++++++++++++ #

    public function add_spare_parts(Request $request,$maintenance_id_str,$getQueryString='')
    {
        # Add Spare Parts Form...

        try {
            $maintenance_id = Crypt::decrypt($maintenance_id_str);
            $maintenance = Maintenance::find($maintenance_id);
            // $service_type = $maintenance->service_type;
            // dd($service_type);
            $data = MaintenanceSpare::where('maintenance_id',$maintenance_id)->get()->toArray();
            return view('servicepartnerweb.maintenance.add-spare-parts', compact('data','maintenance','maintenance_id','maintenance_id_str','getQueryString'));
        } catch ( DecryptException $e) {
            return abort(404);
        }
    }

    public function save_spare_parts(Request $request,$getQueryString='')
    {
        // dd($request->all());
        # Save Form & Close Call...
        $request->validate([
            'details.*.product_id' => 'required',
            'details.*.quantity' => 'required'
        ],[
            'details.*.product_id.required' => 'Please choose product',
            'details.*.quantity.required' => 'Please add quantity',
        ]);
        $params = $request->except('_token');

        $details = $params['details'];
        $service_type = $params['service_type'];
        // dd($service_type);

        $oldProIds = $currentProIds = $removeProIdArr = array();

        $all_pre_prods = MaintenanceSpare::where('maintenance_id',$params['maintenance_id'])->get();
        foreach($all_pre_prods as $pro){
            $oldProIds[] = $pro->product_id;
        }
        foreach($details as $newItem){
            $currentProIds[] = $newItem['product_id'];            
        }
        foreach($oldProIds as $value){
            if(!in_array($value,$currentProIds)){
                $removeProIdArr[] = $value;
            }
        }
        if(!empty($removeProIdArr)){
            foreach($removeProIdArr as $value){
                MaintenanceSpare::where('maintenance_id',$params['maintenance_id'])->where('product_id',$value)->delete();
            }
        }


        foreach($details as $item){
            if($item['isNew'] == 0){
                $spareArr = array(
                    'maintenance_id' => $params['maintenance_id'],
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                );
                MaintenanceSpare::insert($spareArr);
            }
            if($item['isNew'] == 1){
                MaintenanceSpare::where('maintenance_id',$params['maintenance_id'])->where('product_id', $item['product_id'])->update([
                    'quantity' => $item['quantity'],
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }
            
        }
        # Send OTP To Customer for closing the call
        $maintenance = Maintenance::find($params['maintenance_id']);
        $customer_name = $maintenance->customer_name;
        $customer_phone = $maintenance->customer_phone;

        
        $successMsg = 'Spare parts added and OTP generated successfully for closing the call';
        if(!empty($maintenance->is_spare_added)){
            $successMsg = 'Spare parts saved successfully';
        }
        $otp = random_int(100000, 999999);    
        $closing_otp_started_at = date('Y-m-d H:i');
        $closing_otp_expired_at = date('Y-m-d H:i', strtotime("+7 days"));    
        if(!empty($maintenance->closing_otp)){
            $successMsg = 'Spare parts saved successfully';
            $otp = $maintenance->closing_otp;
            $closing_otp_started_at = $maintenance->closing_otp_started_at;
            $closing_otp_expired_at = date('Y-m-d H:i', strtotime("+7 days"));    
        } 
        
        if(empty($maintenance->closing_otp)){
            // $this->sendOTPCustomer('maintenance',$params['maintenance_id'],$otp,$customer_name,$customer_phone);
        }
        
        
        Maintenance::where('id',$params['maintenance_id'])->update([
            'is_spare_added' => 1,
            'closing_otp' => $otp,
            'closing_otp_started_at' => $closing_otp_started_at,
            'closing_otp_expired_at' => $closing_otp_expired_at
        ]);

        


        Session::flash('message', $successMsg);
        return redirect('/servicepartnerweb/maintenance/list/'.$service_type.'?'.$getQueryString);
        
    }

    public function clear_spares($maintenance_id_str,$getQueryString='')
    {
        # Clear Spare ...
        try {
            $maintenance_id = Crypt::decrypt($maintenance_id_str);
            
            $maintenance = Maintenance::find($maintenance_id);
            $service_type = $maintenance->service_type;
            Maintenance::where('id',$maintenance_id)->update(['is_spare_added'=>0]);
            MaintenanceSpare::where('maintenance_id',$maintenance_id)->delete();

            Session::flash('message', 'Spare requisition removed for the request successfully');
            return redirect('/servicepartnerweb/maintenance/list/'.$service_type.'?'.$getQueryString);
        } catch ( DecryptException $e) {
            return abort(404);
        }
        

        
    }


}
