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
use App\Models\Repair;
use App\Models\RepairSpare;
use App\Models\RepairSpareRequisitionNote;
use App\Models\PurchaseOrderProduct;
use App\Models\Product;
use App\Models\Ledger;

class RepairSpareController extends Controller
{
    public function __construct(Request $request)
    {
        $this->middleware('auth:servicepartner');
    }

    public function add(Request $request, $repair_id_str,$getQueryString='')
    {
        // dd($request->all(),$repair_id_str,$getQueryString);
        # Add Spare Parts Form For Repair Request ...
        try {
            $repair_id = Crypt::decrypt($repair_id_str);
            $repair = Repair::find($repair_id);
            $data = RepairSpare::where('repair_id',$repair_id)->get()->toArray();
            // $reqNotes = RepairSpareRequisitionNote::where('repair_id',$repair_id)->first();
            return view('servicepartnerweb.notification.add-repair-parts', compact('data','repair_id','repair_id_str','getQueryString','repair'));
        } catch ( DecryptException $e) {
            return abort(404);
        }
        
    }

    public function save(Request $request,$getQueryString='')
    {
        # Save Form & Close Call...
        $request->validate([
            'details.*.product_id' => 'required',
            'details.*.quantity' => 'required',
            'note' => 'required'
        ],[
            'details.*.product_id.required' => 'Please choose product',
            'details.*.quantity.required' => 'Please add quantity',
            'note.required' => 'Please add note'
        ]);
        $params = $request->except('_token');

        // dd($params);
        $details = $params['details'];

        $oldProIds = $currentProIds = $removeProIdArr = array();

        $all_pre_prods = RepairSpare::where('repair_id',$params['repair_id'])->get();
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
                RepairSpare::where('repair_id',$params['repair_id'])->where('product_id',$value)->delete();
            }
        }

        $repair = Repair::find($params['repair_id']);
        $goods_id = $repair->product_id;
        $in_warranty = $repair->in_warranty;
        $unique_id = $repair->unique_id;
        $product = Product::find($goods_id);
        $repair_charge = !empty($product->repair_charge)?$product->repair_charge:0;

        $total_spare_charge = 0;
        foreach($details as $item){

            ## If Out of warranty set goods repair charge and spare charges

            $spare_id = isset($item['product_id'])?$item['product_id']:NULL;
            $quantity = isset($item['quantity'])?$item['quantity']:NULL;

            $highest_spare_cost_price_po = PurchaseOrderProduct::where('product_id',$spare_id)->max('cost_price');

            $spare_profit_percentage = getSingleAttributeTable('products','id',$spare_id,'profit_percentage');

            $spare_name = getSingleAttributeTable('products','id',$spare_id,'title');

            if(empty($in_warranty)){
                if(empty($highest_spare_cost_price_po)){
                    return redirect()->back()->withErrors(['spare_err_msg' => 'No Spare Price Found For '.$spare_name.' . Please talk to admin'])->withInput();
                }
            }

            if(empty($spare_profit_percentage)){
                return redirect()->back()->withErrors(['spare_err_msg' => 'No Spare Profit Percentage Added From Master . Please talk to admin'])->withInput();
            }

            

        }
        

        
        // dd($total_service_charge);
        foreach($details as $item){

            $highest_spare_cost_price_po = PurchaseOrderProduct::where('product_id',$item['product_id'])->max('cost_price');
            $spare_profit_percentage = getSingleAttributeTable('products','id',$item['product_id'],'profit_percentage');

            $highest_spare_cost_price_po = !empty($highest_spare_cost_price_po)?$highest_spare_cost_price_po:0;
            $spare_profit_val = getPercentageVal($spare_profit_percentage,$highest_spare_cost_price_po);
            $spare_price = ($highest_spare_cost_price_po + $spare_profit_val);
            $spare_charge = ($quantity*$spare_price);
            $total_spare_charge += $spare_charge;


            if($item['isNew'] == 0){
                $spareArr = array(
                    'repair_id' => $params['repair_id'],
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'is_broken' => $item['is_broken'],
                    'cost_price' => $highest_spare_cost_price_po,
                    'profit_percentage' => $spare_profit_percentage,
                    'spare_profit_val' => $spare_profit_val,
                    'total_spare_charge' => $spare_charge,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                );
                RepairSpare::insert($spareArr);
            }
            if($item['isNew'] == 1){
                RepairSpare::where('repair_id',$params['repair_id'])->where('product_id', $item['product_id'])->update([
                    'quantity' => $item['quantity'],
                    'is_broken' => $item['is_broken'],
                    'cost_price' => $highest_spare_cost_price_po,
                    'profit_percentage' => $spare_profit_percentage,
                    'spare_profit_val' => $spare_profit_val,
                    'total_spare_charge' => $spare_charge,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }

                        
        }
        $total_service_charge = ($repair_charge+$total_spare_charge);

        ## Ledger Debit Entry Total Service Charge For Out Of Warranty

        if(empty($in_warranty)){
            $this->ledgerOutOfWarrantyCharge($params['repair_id'],Auth::user()->id,$unique_id,$total_service_charge);
        }

        
        # Send OTP To Customer for closing the call
        $repair = Repair::find($params['repair_id']);
        $customer_name = $repair->customer_name;
        $customer_phone = $repair->customer_phone;

        
        $successMsg = 'Spare parts added and OTP generated successfully for closing the call';
        if(!empty($repair->is_spare_added)){
            $successMsg = 'Spare parts saved successfully';
        }
        $otp = random_int(100000, 999999);    
        $closing_otp_started_at = date('Y-m-d H:i');
        $closing_otp_expired_at = date('Y-m-d H:i', strtotime("+7 days"));    
        if(!empty($repair->closing_otp)){
            $successMsg = 'Spare parts saved successfully';
            $otp = $repair->closing_otp;
            $closing_otp_started_at = $repair->closing_otp_started_at;
            $closing_otp_expired_at = date('Y-m-d H:i', strtotime("+7 days"));    
        } 
        
        if(empty($repair->closing_otp)){
            $this->sendOTPCustomer('repair',$params['repair_id'],$otp,$customer_name,$customer_phone);
            
        }
        
        
        Repair::where('id',$params['repair_id'])->update([
            'is_spare_added' => 1,
            'closing_otp' => $otp,
            'closing_otp_started_at' => $closing_otp_started_at,
            'closing_otp_expired_at' => $closing_otp_expired_at
        ]);

        ## RepairSpareRequisitionNote ##
        
        $existSpareRequisition = RepairSpareRequisitionNote::where('repair_id',$params['repair_id'])->first();
        if(empty($existSpareRequisition)){
            # Insert ...
            $repairSpareRequisitionNoteArr = array(
                'repair_id' => $params['repair_id'],
                'service_partner_id' => Auth::user()->id,
                'note' => $params['note'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            );
            RepairSpareRequisitionNote::insert($repairSpareRequisitionNoteArr);
        } else {
            RepairSpareRequisitionNote::where('id',$existSpareRequisition->id)->update([
                'note' => $params['note'],
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }

        Session::flash('message', $successMsg);
        return redirect('/servicepartnerweb/notification/list-repair?'.$getQueryString);
        
    }

    private function sendOTPCustomer($type,$id,$otp,$customer_name,$customer_mobile_no){

        $sms_entity_id = getSingleAttributeTable('settings','id',1,'sms_entity_id');
        // $sms_template_id = getSingleAttributeTable('settings','id',1,'sms_template_id');
        $sms_template_id = "1707173107738290074";

        $checkPhoneNumberValid = checkPhoneNumberValid($customer_mobile_no);
        if($checkPhoneNumberValid){            
            $ins_rep_end_point = 'form-repair?id=';
            $sender = 'AMMRTL';
            // $csat_base_url = 'https://kgaelectronics.com/retailer/feedback/'; 
            $csat_base_url = 'https://kgaerp.in/retailer/feedback/';               
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

    public function clear($repair_id_str,$getQueryString='')
    {
        # Clear Spare ...
        try {
            $repair_id = Crypt::decrypt($repair_id_str);
            // dd($repair_id);
            Repair::where('id',$repair_id)->update(['is_spare_added'=>0]);
            RepairSpare::where('repair_id',$repair_id)->delete();

            Session::flash('message', 'Spare requisition removed for the repair request successfully');
            return redirect('/servicepartnerweb/notification/list-repair?'.$getQueryString);
        } catch ( DecryptException $e) {
            return abort(404);
        }
        

        
    }

    public function save_requisition_note(Request $request,$getQueryString='')
    {
        # Save Requisition Note...
        $params = $request->except('_token');
        // dd($getQueryString);

        $existSpareRequisition = RepairSpareRequisitionNote::where('repair_id',$params['repair_id'])->first();
        if(empty($existSpareRequisition)){
            # Insert ...
            $repairSpareRequisitionNoteArr = array(
                'repair_id' => $params['repair_id'],
                'service_partner_id' => Auth::user()->id,
                'note' => $params['note'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            );
            RepairSpareRequisitionNote::insert($repairSpareRequisitionNoteArr);
        } else {
            RepairSpareRequisitionNote::where('id',$existSpareRequisition->id)->update([
                'note' => $params['note'],
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
        $successMsg = "Requisition Notes Saved Successfully";
        Session::flash('message', $successMsg);
        return redirect('/servicepartnerweb/notification/list-repair?'.$getQueryString);

    }
}
