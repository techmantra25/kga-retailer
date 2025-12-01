<?php

namespace App\Http\Controllers\Api\Employee;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Models\DealerEmployee;
use App\Models\KgaSalesData;
use App\Models\ServiceCentre;
use App\Models\DapService;
use App\Models\Product;
use App\Models\GoodsWarranty;
use App\Models\ProductWarranty;
use App\Models\ServicePartner;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

use Auth;

class AuthController extends Controller
{
    //

    public function login(Request $request)
    {
        # code...
        $validator = Validator::make($request->all(),[
            'phone' => 'required|exists:dealer_employee,phone',
            'password' => 'required',
            'mac_address' => 'required'
        ]);

        if (!$validator->fails()){
            $params = $request->except('_token');
            $phone = $params['phone'];
            $password = $params['password'];
            $checkUser = DealerEmployee::where('phone',$phone)->first();
            if(!empty($checkUser)){
                $checkPassword = ($password == $checkUser->password);
                if($checkPassword){
                    if($checkUser->status == 0){
                        return Response::json(['status' => false, 'message' => "Employee is inactive" ],200);
                    }
                    if($checkUser->dealer_type === 'khosla'){
                        return Response::json(['status' => true, 'data'=>$checkUser,'type'=>"khosla",'message' => "Employee is working under Khosla dealer" ],200);
                    }else{
                        return Response::json(['status' => true, 'data'=>$checkUser, 'type'=>"nonkhosla",'message' => "Employee is working under Non-Khosla dealer" ],200);
                    }
                    if(!empty($checkUser->mac_address)){
                        return Response::json(['status' => false, 'message' => "Already logged in a device. Please logout first" ],200);
                    }
                    DealerEmployee::where('id',$checkUser->id)->update([
                        'mac_address' => $params['mac_address']
                    ]);
                    $token = Crypt::encrypt($checkUser->id);
                    return Response::json(['status' => true, 'message' => "Logged in successfully", 'data' => array('token'=>$token,'user'=>$checkUser) ],200);
                }else{
                    return Response::json(['status' => false, 'message' => "Password mismatched" ],200);
                }
            }else{
                return Response::json(['status' => false, 'message' => "No user found" ],200);
            }
        } else {
            return Response::json(['status' => false, 'message' => $validator->errors()->first() , 'data' => array( $validator->errors() ) ],400);
        }


    }

    public function logout(Request $request)
    {
        # logout...
        
        if (! $request->hasHeader('Authorizations')) {
            response()->json(["status"=>false,"message"=>"Unauthorized"],400)->send();
            exit();
        } else {
            $bearer_token = $request->header('Authorizations');
            $token = str_replace("Bearer ","",$bearer_token);            
            try {
                $user_id = Crypt::decrypt($token);  
                $user = DealerEmployee::find($user_id);
                
                DealerEmployee::where('id',$user_id)->update([
                    'mac_address' => null
                ]);
                return Response::json(['status'=>true,'message'=>"Logged out successfully", 'data' => (object) array('user' => $user)],200);         
            } catch (DecryptException $e) {
                response()->json(["status"=>false,"message"=>"Mismatched token"],400)->send();
            }
        }
        
    }
    
    public function DapBarcodeInfo($barcode){
        $dap_product = DapService::with('branch','return_branch','servicePartner','callBookedEmployee','dapProductDispatchedEmployee','serviceCentre','product','FinalSpareParts')->where('unique_id', $barcode)->first();
        if($dap_product){
            return response()->json(['status'=>true,'data'=>$dap_product],200);
        }else{
            return response()->json(['status' => false, 'message' => 'No data found'], 400);

        }

    }
    public function CheckDapItem(Request $request)
    {
    $contact_type = $request->input('contact_type', 'mobile');
    $mobile = $request->input('mobile', '');
    $phone = $request->input('phone', '');
    $bill_no = $request->input('bill_no', '');
    $serial = $request->input('serial', '');
    $barcode = $request->input('barcode', '');

    // Initialize an empty collection for kga_sales_data
    $kga_sales_data = collect();

    // Check if any of the input parameters are provided
    if (!empty($mobile) || !empty($phone) || !empty($bill_no) || !empty($serial) || !empty($barcode)) {
        // Start building the query
        $query = KgaSalesData::whereNotNull('product_id');

        if ($contact_type == 'mobile' && !empty($mobile)) {
            $query->where('mobile', $mobile);
        } elseif ($contact_type == 'phone' && !empty($phone)) {
            $query->where('phone', $phone);
        }
        if (!empty($bill_no)) {
            $query->where('bill_no', $bill_no);
        }
        if (!empty($serial)) {
            $query->where('serial', $serial);
        }
        if (!empty($barcode)) {
            $query->where('barcode', $barcode);
        }

        // Execute the query and get the results
        $kga_sales_data = $query->with('product','dapCategory')->get();

        // Add warranty status to each item
        $kga_sales_data->transform(function ($item) {      
            $GoodsWarranty = ProductWarranty::where('dealer_type', 'khosla')->where('goods_id', $item->product_id)->get(); // for only khosla product
            $khosla_warranty=[]; 
            $item->call_booking_status = $item->dapCategory?1:0;                          
            if ($GoodsWarranty->isNotEmpty()) {
                foreach($GoodsWarranty as $key => $value){
                    $array = [];
                    $array['warranty_type']=$value->warranty_type;
                    $array['additional_warranty_type']=$value->additional_warranty_type;
                    $array['number_of_cleaning']=$value->number_of_cleaning;
                    $array['parts']=$value->spear_goods?$value->spear_goods->title:null;
                    $array['warranty_period']=$value->warranty_period;
                    $array['dealer_type']=$value->dealer_type;
                    $warranty_period = $value->warranty_period;
                    $warranty_end_date = date('Y-m-d', strtotime($item->bill_date. ' + '.$warranty_period.' months'));
                    $warranty_date = date('Y-m-d', strtotime($warranty_end_date . ' -1 days'));
                    $array['warranty_end_date']=date('d-m-Y',strtotime($warranty_date));
                    if(date('Y-m-d') < $warranty_date){
                        $array['warranty_status']="YES";
                    }else{
                        $array['warranty_status']="NO";
                    }
                    $khosla_warranty[]= $array;
                }
            }else {
                // No warranty found for the product, add an empty warranty response
                // return Response::json(['status' => false,  'message' => "No product warranty data found",], 200);

            }
          // Add the warranty details to the item
          $item->khosla_warranty = $khosla_warranty;
          return $item;
        });

        // Return the response with the results
        return Response::json(['status' => true, 'kga_sales_data' => $kga_sales_data], 200);

    } else {
        // Return a response indicating no product found
        return Response::json(['status' => false, 'message' => "No product found"], 200);
    }
}


    public function create($id)
    {

        // Retrieve the KgaSalesData item with the related product
        $kga_sales_data = KgaSalesData::with('product')->where('id', $id)->first();
     
        if ($kga_sales_data) {
            $GoodsWarranty = ProductWarranty::where('dealer_type', 'khosla')->where('goods_id', $kga_sales_data->product_id)->get();
            $khosla_warranty = [];
            if(count($GoodsWarranty)>0){
                    foreach($GoodsWarranty as $value) {
                        $array = [];
                        $array['warranty_type'] = $value->warranty_type;
                        $array['additional_warranty_type'] = $value->additional_warranty_type;
                        $array['number_of_cleaning'] = $value->number_of_cleaning;
                        $array['parts'] = $value->spear_goods ? $value->spear_goods->title : null;
                        $array['warranty_period'] = $value->warranty_period;
                        $warranty_end_date = date('Y-m-d', strtotime($kga_sales_data->bill_date . ' + ' . $value->warranty_period . ' months'));
                        $warranty_date = date('Y-m-d', strtotime($warranty_end_date . ' -1 days'));
                        $array['warranty_end_date'] = date('d-m-Y', strtotime($warranty_date));
                        $array['warranty_status'] = (date('Y-m-d') < $warranty_date) ? "YES" : "NO";
                        $khosla_warranty[] = $array;
                    }
                $kga_sales_data->khosla_warranty = $khosla_warranty;
                
                // Fetch showrooms
                $showroom = Branch::all();
                
                return response()->json(['status' => true, 'kga_sales_data' => $kga_sales_data, 'showroom' => $showroom], 200);
            }else{
                // Return a response indicating no product found
                $kga_sales_data->khosla_warranty = $khosla_warranty;
                return response()->json(['status' => false, 'kga_sales_data' => $kga_sales_data], 200);     //'message' => "No product warranty data found"
            }
        }else {
            // Return a response indicating no product found
            return response()->json(['status' => false, 'message' => "No product found"], 200);
        }
    }
    

    public function store(Request $request)
    {
        // Define validation rules and custom messages
        $rules = [
            'branch_id' => 'required',
            'alternate_no' => 'required|digits:10',
            'issue' => 'required'
        ];
        
        $messages = [
            'branch_id.required' => 'Please select a branch',
            'alternate_no.required' => 'Alternate number is required',
            'alternate_no.digits' => 'Alternate number must be exactly 10 digits',
            'issue.required' => 'Please specify the issue'
        ];
        
        // Validate request data
        $validator = Validator::make($request->all(), $rules, $messages);
        
        if ($validator->fails()) {
            return Response::json(['status' => false, 'message' => $validator->errors()->first(), 'data' => $validator->errors()], 400);
        }
        
        try {
            // Prepare parameters
            $params = $request->except('_token');
            $uniue_id = genAutoIncreNoYearWiseCallBook(3, 'dap_services', date('Y'), date('m'), 'DAP');
            $params['unique_id'] = $uniue_id;
            $params['is_dispatched_from_branch'] = 0;
            $params['entry_date'] = date('Y-m-d');
            $params['address'] = $request->address;
            $params['created_at'] = date('Y-m-d H:i:s');
            
            unset($params['branch_name']);
            
            // Generate barcode
            $barcodeGeneratorWithNo = barcodeGeneratorWithNo($params['barcode']);
            $params['code_html'] = $barcodeGeneratorWithNo['code_html'];
            $params['code_base64_img'] = $barcodeGeneratorWithNo['code_base64_img'];
            $params['repeat_call'] = 0;       //if the dap_call is not repeat in 30 days
            $params['repeat_dap_id'] = NULL;       //if the dap_call is not repeat in 30 days
            
            // Check if serial already exists
            $checkExistSerial = DapService::where('serial', $params['serial'])->where('is_closed',1)->orderBy('id','DESC')->first();
            if ($checkExistSerial) {
                $last_entry_date = $checkExistSerial->entry_date;
                $date1 = date_create($last_entry_date);
                $date2 = date_create(date('Y-m-d'));
                $diff = date_diff($date1, $date2);
                // $days = $diff->format("%d");
                $days = $diff->days;

                
                if ($days <= 30) {
                    // return response::json(['status' => false, 'message' => 'You cannot add same item within 30 days']);
                    $params['repeat_call'] = 1;
                    $params['repeat_dap_id'] = $checkExistSerial->id;
                }
            }
            
            // Check if out of warranty
            // if ($params['in_warranty'] == 0) {
                $product = Product::find($params['product_id']);
                $repair_charge = $product->repair_charge;
                if (empty($repair_charge)) {
                    return response::json(['status' => false, 'item' => 'This goods has no repair charge for out of warranty servicing. Please set from product master']);
                }
                $params['repair_charge'] = $repair_charge;
            // }
            
            // Insert data into DapService
            // return $params;
            $dap_data = DapService::create($params);
            $call_id = $dap_data->unique_id;
            $mobile = $dap_data->alternate_no;
            $item = $dap_data->item ?? "";
            if (strlen($item) > 30) {
                $item = substr($item, 0, 28) . '..';
            }
            
            // // Validate phone number
            $checkPhoneNumberValid = checkPhoneNumberValid($mobile);
            if ($checkPhoneNumberValid) {
                $query_calling_number = "6291117317";
                $sms_entity_id = getSingleAttributeTable('settings', 'id', 1, 'sms_entity_id');
                $sms_template_id = "1707172110551659557";
                
                $myMessage = urlencode('Call ID ' . $call_id . ' KGA is happy to help you to repair your product ' . $item . '. For tracking status contact KGA customer care - ' . $query_calling_number . ' AMMRTL');
                $sms_url = 'https://sms.bluwaves.in/sendsms/bulk.php?username=ammrllp&password=123456789&type=TEXT&sender=AMMRTL&mobile=' . $mobile . '&message=' . $myMessage . '&entityId=' . $sms_entity_id . '&templateId=' . $sms_template_id;
                
                $curl = curl_init();
                
                curl_setopt_array($curl, [
                    CURLOPT_URL => $sms_url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                ]);
                
                $response = curl_exec($curl);
                curl_close($curl);
            //     // Insert SMS API response into the database
                DB::table('sms_api_response')->insert([
                    'sms_template_id' => $sms_template_id,
                    'sms_entity_id' => $sms_entity_id,
                    'phone' => $mobile,
                    'message_body' => $myMessage,
                    'response_body' => $response,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                
                return response::json(['status' => true,'data'=>$dap_data, 'message' => 'Call Booked Successfully'], 200);
            } else {
                return response::json(['status' => false, 'message' => 'Invalid phone number'], 400);
            }
        } catch (\Exception $e) {
            // Handle exceptions
            return response::json(['status' => false, 'message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }


    public function barcode($barcode){
        // Retrieve the KgaSalesData item with the related product
        $kga_sales_data = KgaSalesData::with('product')->where('barcode',$barcode)->first();
        
        // Check if the item exists
        if ($kga_sales_data) {
            // Add warranty status to the item
            $warranty_end_date = date('Y-m-d', strtotime($kga_sales_data->bill_date . ' + ' . $kga_sales_data->product->warranty_period . ' months'));
            $warranty_date = date('Y-m-d', strtotime($warranty_end_date . ' -1 days'));
            $current_date = date('Y-m-d');
            
            if ($current_date < $warranty_date) {
                $kga_sales_data->in_warranty = true;
            } else {
                $kga_sales_data->in_warranty = false;
            }
            
            return Response::json(['status' => true, 'kga_sales_data' => $kga_sales_data ], 200);
        } else {
            // Return a response indicating no product found
            return Response::json(['status' => false, 'message' => "No product found"], 200);
        }
    }
    public function send_service_centre($emp_id){
        $dap_product = DapService::with('sales_data','product','branch')->where('employee_id',$emp_id)->get()->toArray();
        if(!empty($dap_product)){
            return Response::json(['status' => true, 'data' => $dap_product], 200);
        }else{
            return Response::json(['status' => false, 'message' => "No product found" ],200);
        }
    }
    public function dispatch_from_branch(Request $request){
        $dap_barcode = $request->barcode;
        $dap_product= DapService::where('unique_id',$dap_barcode)->first();
        if(!empty($dap_product)){
            if($dap_product->is_dispatched_from_branch == 0){
                DapService::where('id',$dap_product->id)->update([
                    'is_dispatched_from_branch' => 1,
                    'is_dispatched_from_branch_date' => now(),
                    'dispatch_by' => $request->dispatch_by,
                ]);
                return response::json(['status'=>true,'item' => 'The product dispatched from branch'],200);
            }else{
                return response::json(['status'=>false,'item' => 'The product is already dispatched from branch'],400);
            }
        }else{
            return Response::json(['status' => false, 'message' => "No product found" ],200);
        }
    }
    public function wearhouse(){
        $data = ServiceCentre::get()->toArray();
        return response()->json(['status' => true, 'data'=>$data], 200);
        
    }
    public function download_road_challan($dap_barcode){
        $dapService = DapService::with('branch','serviceCentre','product')->where('unique_id', $dap_barcode)->first();
        // Check if the record exists
        if ($dapService) {
            // Convert the record to an array
            $data = $dapService->toArray();
            $date = date('d-M-y');
            $pdf = Pdf::loadView('dap-services.download-road-challan', compact('data','date'));
            return $pdf->download('Road-Challan.pdf');
        }
    }
    public function generate_road_challan(Request $request){
        $dap_product= DapService::where('unique_id',$request->dap_barcode)->first();
        $validator = Validator::make($request->all(), [
            'wearhouse_id' => 'required',
            'vehicle_number' => 'required'
        ], [
            'wearhouse_id.required' => 'Please select a Service center',
            'vehicle_number.required' => 'Please put a vehicle_number'
        ]);
        if (!$validator->fails()){
            DapService::where('id',$dap_product->id)->update([
                'wearhouse_id' => $request->wearhouse_id,
                'vehicle_number' => $request->vehicle_number
            ]);
            return response::json(['status'=>true,'message' => 'Road challan generated successfully'],200);
        }else{
            return Response::json(['status' => false, 'message' => $validator->errors()->first() , 'data' => array( $validator->errors() ) ],400);

        }

    }
    public function receive_at_wearhouse(Request $request){
        $dap_barcode = $request->barcode;
        $dap_product= DapService::where('unique_id',$dap_barcode)->first();
        if(!empty($dap_product)){
            if($dap_product->is_reached_service_centre == 0){
                DapService::where('id',$dap_product->id)->update([
                    'is_reached_service_centre' => 1,
                    'is_reached_service_centre_date' => now()
                ]);
               
                // Retrieve all service partners of type 2
                $service_partners = ServicePartner::where('type', 2)->where('status', 1)->where('phone', '!=', '6291117317')->pluck('id')->toArray();
                
                if (empty($service_partners)) {
                    return response()->json(['status' => false, 'message' => 'No service partners available'], 400);
                }
                
                // Retrieve the most recently assigned service partner ID
                $last_assigned_service_partner_id = DapService::whereIn('assign_service_perter_id', $service_partners)
                ->orderBy('updated_at', 'desc')->pluck('assign_service_perter_id')->first();  
                                                            
                                                          
                                                          // Determine the next available service partner
                                                          $next_partner_id = null;
                                                          if (!$last_assigned_service_partner_id) {
                                                              // If no partner has been assigned yet, use the first partner
                                                              $next_partner_id = $service_partners[0];
                                                            } else {
                                                                // Find the position of the last assigned partner
                                                                $current_key = array_search($last_assigned_service_partner_id, $service_partners);
                                                                
                                                                // Get the next partner in the list, or the first one if at the end
                                                                $next_key = ($current_key !== false && $current_key < count($service_partners) - 1) ? $current_key + 1 : 0;
                                                                $next_partner_id = $service_partners[$next_key];
                                                            }
                                                            
                                                            DapService::where('id', $dap_product->id)->update([
                                                                'assign_service_perter_id' => $next_partner_id
                                                            ]);
                                                            return response()->json(['status' => true, 'message' => 'The product has been received at the warehouse'], 200);
                                                        }else{
                                                            return response::json(['status'=>false,'message' => 'The product has already been received at the wearhouse'],400);
                                                        }
                                                    }else{
                                                        return Response::json(['status' => false, 'message' => "No product found" ],200);
                                                    }
                                                }
                                                
                                                
                                                
    public function all_showroom(){
        $showroom = Branch::get()->toArray();
        if(!empty($showroom)){
            return Response::json(['status' => true, 'data' =>$showroom  ],200);
        }else{
            return Response::json(['status' => false, 'message' => "No showroom/branch found" ],200);
        }

    }
    public function branch_wise_dap_product($id){
        $dap_product = DapService::with('sales_data','product','branch')->where('branch_id',$id)->get()->toArray();
       
        if(!empty($dap_product)){
            $final_data = [];
            foreach($dap_product as $item){
                    $general_warranty = $comprehensive_warranty = $extra_warranty = $motor_warranty = 0;
    
                    $GoodsWarranty = GoodsWarranty::where('dealer_type', 'khosla')
                        ->where('goods_id', $item['product_id'])
                        ->first();
                    
                    if ($GoodsWarranty) {
                        $general_warranty = $GoodsWarranty->general_warranty ?: 0;
                        $comprehensive_warranty = $GoodsWarranty->comprehensive_warranty ?: 0;
                        $extra_warranty = $GoodsWarranty->extra_warranty ?: 0;
                        $motor_warranty = $GoodsWarranty->motor_warranty ?: 0;
                    }
        
                    $warranty_period = $general_warranty + $extra_warranty;
                    $warranty_end_date = date('Y-m-d', strtotime($item['bill_date'] . ' + ' . $warranty_period . ' months'));
                    $warranty_date = date('Y-m-d', strtotime($warranty_end_date . ' -1 days'));
                    $current_date = date('Y-m-d');
        
                    $item['general_warranty'] = false;
                    $item['general_expiry_date'] = $warranty_date;
                    $item['comprehensive_warranty'] = false;
                    $item['comprehensive_expiry_date'] = null;
                    $item['motor_warranty'] = false;
                    $item['motor_expiry_date'] = null;
                    $item['in_warranty'] = false;
        
                    if ($current_date < $warranty_date) {
                        $item['general_warranty'] = true;
                        $item['comprehensive_warranty'] = true;
                        $item['motor_warranty'] = true;
                        $item['in_warranty'] = true;
                    } else {
                        if ($comprehensive_warranty > 0) {
                            $warranty_end_date = date('Y-m-d', strtotime($item['bill_date'] . ' + ' . $comprehensive_warranty . ' months'));
                            $warranty_date = date('Y-m-d', strtotime($warranty_end_date . ' -1 days'));
                            if ($current_date < $warranty_date) {
                                $item['in_warranty'] = true;
                                $item['comprehensive_warranty'] = true;
                                $item['comprehensive_expiry_date'] = $warranty_date;
                            }
                        }
                        if ($motor_warranty > 0) {
                            $warranty_end_date = date('Y-m-d', strtotime($item['bill_date'] . ' + ' . $motor_warranty . ' months'));
                            $warranty_date = date('Y-m-d', strtotime($warranty_end_date . ' -1 days'));
                            if ($current_date < $warranty_date) {
                                $item['in_warranty'] = true;
                                $item['motor_warranty'] = true;
                                $item['motor_expiry_date'] = $warranty_date;
                            }
                        }
                    }
                    $final_data[]=$item;
                }
            return Response::json(['status' => true, 'data' =>$final_data  ],200);
        }else{
            return Response::json(['status' => false, 'message' => "No product found" ],200);
        }
    }       
                               
    public function receive_repaire_dap_product_at_showroom($showrromId,$barcode){
        $data = DapService::where('unique_id',$barcode)->where('is_closed',1)->first();
        
        if($data){
            if($data->return_branch_id == $showrromId ){
                $data->is_received_at_branch =1;
                $data->is_received_at_branch_date = now();
                $data->save();
                return Response::json(['status' => true, 'message'=>"Dap repaire product successfully received at showroom"  ],200);
            }else{
                return Response::json(['status' => false, 'message'=>"This dap repaire product is not recomended for this showroom"  ],400);
            }
        }else{
            return Response::json(['status' => false, 'message'=>"No record found"  ],400);
            
        }
    }
    
    public function customer_delivery_otp_verify(Request $request){
        $DapService = DapService::find($request->dap_id);
        if (!$DapService) {
            return Response::json(['status' => false, 'message' => 'Service not found'], 404);
        }else{
            $existing_otp = $DapService->delivery_otp;
            if ($existing_otp != $request->otp) {
                return Response::json(['status' => false, 'message' => 'The OTP you entered does not match our records'], 400);
            }else{
                $DapService->verify_delivery_otp = 1;
                $DapService->customer_delivery_time = now();

                $DapService->save();
                return Response::json(['status' => true, 'message' => 'The OTP matched & delivered the product to customer'], 200);
            }
        }
    }
    public function customer_delivery_otp($uniqueId){
        $data = DapService::where('unique_id',$uniqueId)->first();
        $product_name = $data->dap_product_name;
        $mobile = $data->alternate_no;

        $otp = rand(1000,9999);
        
        $checkPhoneNumberValid = checkPhoneNumberValid($mobile);
        if($checkPhoneNumberValid){
            $sms_entity_id = getSingleAttributeTable('settings','id',1,'sms_entity_id');
            $sms_template_id = "1707172259152605307";

            
            $myMessage = urlencode('Please use the OTP '.$otp.' to collect your repaired KGA product. Show this OTP to the counter person. Thank you! AMMR TECHNOLOGY LLP');

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
            
            $data->delivery_otp = $otp;
            $data->delivery_otp_time = date('Y-m-d H:i:s');
            $data->verify_delivery_otp = 0;
            $data->save();
            return response()->json(['status' => true, 'message' => "OTP has been sent successfully"], 200);
        }else{
            return response()->json(['status' => false, 'message' => "Mobile number must be 10 digits"], 500);
        }
        
    }
    
    public function return_service_centre_product($showrromId){
        $data = DapService::where('is_closed',1)->where('return_branch_id',$showrromId)->where('is_received_at_branch',1)->get()->toArray();
        if(empty($data)){
            return response()->json(['status' => false, 'message' => "No record found"], 400);
        }else{
            return response()->json(['status' => true, 'data' => $data], 200);
        }
    }
}
                                            