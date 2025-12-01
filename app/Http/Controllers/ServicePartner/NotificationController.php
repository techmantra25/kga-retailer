<?php

namespace App\Http\Controllers\ServicePartner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use File; 
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Models\ServicePartner;
use App\Models\CustomerPointService;
use App\Models\Pincode;
use App\Models\ServicePartnerPincode;
use App\Models\Installation;
use App\Models\Repair;
use App\Models\Ledger;
use App\Models\Payment;
use App\Models\RepairSpare;
use App\Models\Product;

class NotificationController extends Controller
{
    public function __construct(Request $request)
    {
        $this->middleware('auth:servicepartner');
    }

    public function list_installation(Request $request)
    {
        # my installation notifications...
        $paginate = 20;
        $search = !empty($request->search)?$request->search:'';
        $uploaded_at = !empty($request->uploaded_at)?$request->uploaded_at:'';
        $service_partner = !empty($request->service_partner)?$request->service_partner:'';

        $closing_type = !empty($request->closing_type)?$request->closing_type:'';



        $service_partners = ServicePartner::get();

        $data = Installation::with('service_partner');
        $totalResult = Installation::select('id');

        $data = $data->where('service_partner_id',Auth::user()->id);
        $totalResult = $totalResult->where('service_partner_id',Auth::user()->id);

        if(!empty($search)){
            $data = $data->where(function($query) use ($search){
                $query->where('pincode', 'LIKE','%'.$search.'%')->orWhere('bill_no', 'LIKE','%'.$search.'%')->orWhere('unique_id', 'LIKE','%'.$search.'%')->orWhere('customer_name', 'LIKE', '%'.$search.'%')->orWhere('mobile_no', 'LIKE', '%'.$search.'%')->orWhere('phone_no', 'LIKE', '%'.$search.'%');
            });
            $totalResult = $totalResult->where(function($query) use ($search){
                $query->where('pincode', 'LIKE','%'.$search.'%')->orWhere('bill_no', 'LIKE','%'.$search.'%')->orWhere('unique_id', 'LIKE','%'.$search.'%')->orWhere('customer_name', 'LIKE', '%'.$search.'%')->orWhere('mobile_no', 'LIKE', '%'.$search.'%')->orWhere('phone_no', 'LIKE', '%'.$search.'%');
            });
        }

        if(!empty($uploaded_at)){
            $data = $data->whereRaw(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d') = '".$uploaded_at."'"));
            $totalResult = $totalResult->whereRaw(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d') = '".$uploaded_at."'"));
        }
        
        if(!empty($closing_type)){
            if($closing_type == 'cancelled'){
                $data = $data->where('is_cancelled', 1);
                $totalResult = $totalResult->where('is_cancelled', 1);
                $data = $data->orderBy('id','asc')->paginate($paginate);
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
        

        
        $totalResult = $totalResult->count();

        $data = $data->appends([
            'search'=>$search,
            'service_partner'=>$service_partner,
            'uploaded_at'=>$uploaded_at,
            'page'=>$request->page,
            'closing_type'=>$closing_type
        ]);
        // dd($data);
        return view('servicepartnerweb.notification.list-installation', compact('data','totalResult','paginate', 'search','uploaded_at','service_partners','service_partner','closing_type'));


    }

    public function list_customer_repair_point(Request $request)
    {
        // Pagination, Search, and Filter Parameters
        $paginate = 20;
        $search = !empty($request->search) ? $request->search : '';
        $uploaded_at = !empty($request->uploaded_at) ? $request->uploaded_at : '';
        $service_partner = !empty($request->service_partner) ? $request->service_partner : '';
        $closing_type = !empty($request->closing_type) ? $request->closing_type : '';
    
        // Service Partners List
        $service_partners = ServicePartner::get();
    
        // Base Query for Data and Count
        $data = CustomerPointService::with('servicePartner');
        $totalResult = CustomerPointService::select('id');
    
        // Filter for logged-in service partner
        $data = $data->where('assign_service_perter_id', Auth::user()->id);
        $totalResult = $totalResult->where('assign_service_perter_id', Auth::user()->id);
    
        // Search Filter
        if (!empty($search)) {
            $data = $data->where(function ($query) use ($search) {
                $query->where('pincode', 'LIKE', '%' . $search . '%')
                    ->orWhere('bill_no', 'LIKE', '%' . $search . '%')
                    ->orWhere('unique_id', 'LIKE', '%' . $search . '%')
                    ->orWhere('customer_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('customer_phone', 'LIKE', '%' . $search . '%');
            });
            $totalResult = $totalResult->where(function ($query) use ($search) {
                $query->where('pincode', 'LIKE', '%' . $search . '%')
                    ->orWhere('bill_no', 'LIKE', '%' . $search . '%')
                    ->orWhere('unique_id', 'LIKE', '%' . $search . '%')
                    ->orWhere('customer_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('customer_phone', 'LIKE', '%' . $search . '%');
            });
        }
    
        // Closing Type Filter
        if (!empty($closing_type)) {
            switch ($closing_type) {
                case 'pending':
                    $data = $data->where('status', 0);
                    $totalResult = $totalResult->where('status', 0);
                    break;
                case 'repairing':
                    $data = $data->where('status', 3);
                    $totalResult = $totalResult->where('status', 3);
                    break;
                case 'pending-approval':
                    $data = $data->where('status', 4);
                    $totalResult = $totalResult->where('status', 4);
                    break;
                case 'closed':
                    $data = $data->where('status', 8);
                    $totalResult = $totalResult->where('status', 8);
                    break;
                case 'cancelled':
                    $data = $data->where('status', 9);
                    $totalResult = $totalResult->where('status', 9);
                    break;
            }
        }
    
        // Default sorting and pagination
        $data = $data->orderBy('id', 'desc')->paginate($paginate);
    
        // Count total results
        $totalResult = $totalResult->count();
    
        // Apply appends to the paginated data
        $data = $data->appends([
            'search' => $search,
            'service_partner' => $service_partner,
            'uploaded_at' => $uploaded_at,
            'page' => $request->page,
            'closing_type' => $closing_type
        ]);
    
        // Return view with data
        return view('servicepartnerweb.notification.list-customer-repair-point', compact('data', 'totalResult', 'paginate', 'search', 'uploaded_at', 'service_partners', 'service_partner', 'closing_type'));
    }
    
    public function list_repair(Request $request)
    {
        # my repair notification...


        $paginate = 20;
        $search = !empty($request->search)?$request->search:'';
        $uploaded_at = !empty($request->uploaded_at)?$request->uploaded_at:'';
        $service_partner = !empty($request->service_partner)?$request->service_partner:'';

        $closing_type = !empty($request->closing_type)?$request->closing_type:'';



        $service_partners = ServicePartner::get();

        $data = Repair::with('service_partner');
        $totalResult = Repair::select('id');

        $data = $data->where('service_partner_id',Auth::user()->id);
        $totalResult = $totalResult->where('service_partner_id',Auth::user()->id);

        if(!empty($search)){
            $data = $data->where(function($query) use ($search){
                $query->where('pincode', 'LIKE','%'.$search.'%')->orWhere('bill_no', 'LIKE','%'.$search.'%')->orWhere('unique_id', 'LIKE','%'.$search.'%')->orWhere('customer_name', 'LIKE', '%'.$search.'%')->orWhere('customer_phone', 'LIKE', '%'.$search.'%');
            });
            $totalResult = $totalResult->where(function($query) use ($search){
                $query->where('pincode', 'LIKE','%'.$search.'%')->orWhere('bill_no', 'LIKE','%'.$search.'%')->orWhere('unique_id', 'LIKE','%'.$search.'%')->orWhere('customer_name', 'LIKE', '%'.$search.'%')->orWhere('customer_phone', 'LIKE', '%'.$search.'%');
            });
        }

        if(!empty($uploaded_at)){
            $data = $data->whereRaw(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d') = '".$uploaded_at."'"));
            $totalResult = $totalResult->whereRaw(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d') = '".$uploaded_at."'"));
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
        

        
        $totalResult = $totalResult->count();

        $data = $data->appends([
            'search'=>$search,
            'service_partner'=>$service_partner,
            'uploaded_at'=>$uploaded_at,
            'page'=>$request->page,
            'closing_type'=>$closing_type
        ]);
        // dd($data);
        return view('servicepartnerweb.notification.list-repair', compact('data','totalResult','paginate', 'search','uploaded_at','service_partners','service_partner','closing_type'));


        
    }

    public function close_otp_installation(Request $request,$idStr,$getQueryString='')
    {
        # Request OTP Close Installation...
        try {
            $id = Crypt::decrypt($idStr);
            // dd($id);
            $data = Installation::find($id);
            $otp = random_int(100000, 999999);

            $customer_name = $data->customer_name;
            $customer_mobile_no = $data->mobile_no;
            $bill_no = $data->bill_no;
            // echo 'otp:- '.$otp.'<br/>'; die;
            
            $closing_otp_started_at = date('Y-m-d H:i');
            $closing_otp_expired_at = date('Y-m-d H:i', strtotime("+7 days"));

            $this->sendOTPCustomer('installation',$id,$otp,$customer_name,$customer_mobile_no);
            // $this->sendOTPCustomer('installation',$id,$otp,'Rohit Das','6290391954'); # Rohit Das Phone
            
            Installation::where('id',$id)->update([
                'closing_otp' => $otp,
                'closing_otp_started_at' => $closing_otp_started_at,
                'closing_otp_expired_at' => $closing_otp_expired_at
            ]);
            
            Session::flash('message', 'OTP generated successfully for '.$bill_no);
            return redirect('/servicepartnerweb/notification/list-installation?'.$getQueryString);

            
        } catch ( DecryptException $e) {
            return abort(404);
        }

    }

    public function submit_otp_installation(Request $request,$getQueryString='')
    {
        # close service via otp...        
        $request->validate([
            'installation_id' => 'required|exists:installations,id',
            'otp' => 'required'
        ]);
        
        $params = $request->except('_token');
        // dd($params);
        $id = $params['installation_id'];
        $otp = $params['otp'];
        $data = Installation::find($id);
        $service_charge = $data->service_charge;
        $unique_id = $data->unique_id;
        $product_sl_no = $data->product_sl_no;
            
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
                $this->ledgerEntryCallClose('installation',Auth::user()->id,$service_charge,$unique_id,$id);
                $this->ledgerDebitEntryCallClose($data->service_partner_id,$data->product_id,$id);

                Session::flash('message', 'Call closed successfully via OTP for '.$unique_id.'');
                return redirect('/servicepartnerweb/notification/list-installation?'.$getQueryString);
            } else {
                return redirect('servicepartnerweb/notification/list-installation?'.$getQueryString)->withErrors(['otp'=>'Wrong OTP'])->withInput($request->all());
                
            }
            

        } else {
            return redirect('servicepartnerweb/notification/list-installation?'.$getQueryString)->withErrors(['otp'=>'OTP is expired'])->withInput($request->all());
            
        }

            
        

            



        
    }

    public function close_otp_repair(Request $request,$idStr,$getQueryString='')
    {
        # Request OTP Close Repair...
        try {
            $id = Crypt::decrypt($idStr);
            // dd($id);
            $data = Repair::find($id);
            $otp = random_int(100000, 999999);

            $customer_name = $data->customer_name;
            $customer_mobile_no = $data->customer_phone;
            $bill_no = $data->bill_no;
            // echo 'otp:- '.$otp.'<br/>'; die;
            
            $closing_otp_started_at = date('Y-m-d H:i');
            $closing_otp_expired_at = date('Y-m-d H:i', strtotime("+7 days"));

            $this->sendOTPCustomer('repair',$id,$otp,$customer_name,$customer_mobile_no);
            // $this->sendOTPCustomer('repair',$id,$otp,'Rohit Das','6290391954'); # Rohit Das Phone
            
            Repair::where('id',$id)->update([
                'closing_otp' => $otp,
                'closing_otp_started_at' => $closing_otp_started_at,
                'closing_otp_expired_at' => $closing_otp_expired_at
            ]);
            
            Session::flash('message', 'OTP generated successfully for '.$bill_no);
            return redirect('/servicepartnerweb/notification/list-repair?'.$getQueryString);

            
        } catch ( DecryptException $e) {
            return abort(404);
        }

    }

    public function submit_otp_repair(Request $request,$getQueryString='')
    {
        # close repair via otp...        
        $request->validate([
            'repair_id' => 'required|exists:repairs,id',
            'otp' => 'required'
        ]);
        
        $params = $request->except('_token');
        // dd($params);
        $id = $params['repair_id'];
        $otp = $params['otp'];
        $data = Repair::find($id);
        $service_charge = $data->service_charge;
        $unique_id = $data->unique_id;
        $product_sl_no = $data->product_sl_no;
        $in_warranty = $data->in_warranty;
        $goods_id = $data->product_id;
        $is_repeated = $data->is_repeated;
            
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
                if(empty($is_repeated)){
                    # If the call is not repeated
                    $this->ledgerEntryCallClose('repair',Auth::user()->id,$service_charge,$unique_id,$id);
                }
                if(empty($in_warranty)){
                    ## If Spares are not added (For Spare Addition This Calculation Already Done)
                    ## Ledger Debit Entry Total Service Charge For Out Of Warranty

                    $repair_spares =  RepairSpare::where('repair_id',$id)->count();
                    $product = Product::find($goods_id);
                    $repair_charge = !empty($product->repair_charge)?$product->repair_charge:0;

                    if(empty($repair_spares)){
                        if(!empty($repair_charge)){
                            $this->ledgerOutOfWarrantyCharge($id,Auth::user()->id,$unique_id,$repair_charge);
                        }                        
                    }                    
                }

                Session::flash('message', 'Call closed successfully via OTP for '.$unique_id.'');
                return redirect('/servicepartnerweb/notification/list-repair?'.$getQueryString);
            } else {
                return redirect('servicepartnerweb/notification/list-repair?'.$getQueryString)->withErrors(['otp'=>'Wrong OTP'])->withInput($request->all());
                
            }            

        } else {
            return redirect('servicepartnerweb/notification/list-repair?'.$getQueryString)->withErrors(['otp'=>'OTP is expired'])->withInput($request->all());
            
        }
        
    }

    private function sendOTPCustomer($type,$id,$otp,$customer_name,$customer_mobile_no){

        $sms_entity_id = getSingleAttributeTable('settings','id',1,'sms_entity_id');
        // $sms_template_id = getSingleAttributeTable('settings','id',1,'sms_template_id');
        $sms_template_id = "1707173107738290074";

        $checkPhoneNumberValid = checkPhoneNumberValid($customer_mobile_no);
        if($checkPhoneNumberValid){
            
            $ins_rep_end_point = 'form-installation?id=';
            if($type == 'installation'){
                $ins_rep_end_point = 'form-installation?id=';
            } else {
                $ins_rep_end_point = 'form-repair?id=';
            }


            $sender = 'AMMRTL';
            // $csat_base_url = 'https://kgaelectronics.com/retailer/feedback/';            
            $csat_base_url = 'https://kgaerp.in/retailer/feedback/';            
            $ins_rep_id = $id;
            $csat_full_url = $csat_base_url.''.$ins_rep_end_point.''.$ins_rep_id;
            // $csat_full_url = $csat_base_url;

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
        
        $ledgerData = array(
            'type' => 'credit',
            'service_partner_id' => $service_partner_id,
            'amount' => $amount,
            'entry_date' => date('Y-m-d'),
            'user_type' => 'servicepartner',
            'purpose' => $type,
            'transaction_id' => $unique_id,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        );
        if($type == 'installation'){
            $installationData = array('installation_id' => $id);
            $ledgerData = array_merge($ledgerData,$installationData);
        } else if ($type == 'repair'){
            $repairData = array('repair_id' => $id);
            $ledgerData = array_merge($ledgerData,$repairData);
        }

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


    public function save_remarks_installation(Request $request)
    {
        # Save Remark Installation SP...

        $request->validate([
            'remarks' => 'required'
        ]);
        $params = $request->except('_token');
        $request_url = $params['request_url'];

        Installation::where('id',$params['installation_id'])->update([
            'remarks' => $params['remarks']
        ]);

        // dd($params);

        Session::flash('message', "Remark added successfully.");
        return redirect('/servicepartnerweb/notification/list-installation?'.$request_url);
    }

    public function save_remarks_repair(Request $request)
    {
        # Save Remark Repair SP...
        $request->validate([
            'remarks' => 'required'
        ]);
        $params = $request->except('_token');
        $request_url = $params['request_url'];

        Repair::where('id',$params['repair_id'])->update([
            'remarks' => $params['remarks']
        ]);

        // dd($params);

        Session::flash('message', "Remark added successfully.");
        return redirect('/servicepartnerweb/notification/list-repair?'.$request_url);
    }

    public function submit_invoice_image_installation(Request $request,$getQueryString='')
    {
        $request->validate([
            'installation_id' => 'required|exists:installations,id',
            'invoice_image' => 'required|file|mimes:png,jpg,jpeg|max:10000'
        ]);

        $uplaod_base_url_prefix = config('app.uplaod_base_url_prefix');
        
        $params = $request->except('_token');
        // dd($params);
        $id = $params['installation_id'];
        // $otp = $params['otp'];
        $data = Installation::find($id);
        $unique_id = $data->unique_id;

        if(!empty($params['invoice_image'])){
            $upload_path = $uplaod_base_url_prefix."uploads/service-snapshot/";
            $image = $params['invoice_image'];
            // $imageName = time() . "." . $image->getClientOriginalName();            
            $imageName = time() . "." . $image->getClientOriginalExtension();
            $image->move($upload_path, $imageName);
            $uploadedImage = $imageName;
            $invoice_image = $upload_path . $uploadedImage;

            Installation::where('id',$id)->update([
                'invoice_image' => $invoice_image
            ]);
        }

        Session::flash('message', 'Invoice Image Uploaded Successfully For ID '.$unique_id.'');
        return redirect('/servicepartnerweb/notification/list-installation?'.$getQueryString);

        // dd($params);
    }


}
