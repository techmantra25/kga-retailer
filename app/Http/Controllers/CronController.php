<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\Repair;
use App\Models\Category;
use App\Models\Branch;
use App\Models\Ledger;
use App\Models\User;
use App\Models\KgaSalesData;
use App\Models\Installation;
use App\Models\IncompleteInstallation;
use App\Models\CustomerAmcRequest;
use App\Models\ServicePartner;
use App\Models\DapService;
use App\Models\CustomerPointService;
use App\Models\DapSpearPartFinalOrder;
use App\Models\DapServicePayment;
use App\Models\CrpServicePayment;
use App\Models\AmcServicePayment;
use App\Models\AmcSubscription;
use App\Models\CRPFinalSpare;
use App\Models\ProductAmc;
use App\Models\BeforeAmcSubscription;
use App\Models\ServicePartnerCharge;
use App\Models\ServicePartnerPincode;
use App\Models\AmcDuration;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;


class CronController extends Controller
{
    //
    /**
     * Test CRON JOB.
     *
     */
    public function test(Request $request)
    {
        # code...
        DB::table('test')->insert([
            'data' => date('Y-m-d H:i:s')." new test cron initiated",
            'created_at' => date('Y-m-d H:i:s')
        ]);
        
    }
    /**
     * Fetch KGA Live Sales Data On A Date (default yesterday)
     *
     */
    public function get_daily_sales(Request $request)
    {
        $order_date = !empty($request->order_date)?$request->order_date:date('Y-m-d',strtotime("-1 day"));

        // $order_date = date('Y-m-d');

        DB::table('test')->insert([
            'data' => date('Y-m-d H:i:s')." get_daily_sales",
            'created_at' => date('Y-m-d H:i:s')
        ]);        

        /*$curl = curl_init();
        curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://sweet-darwin.43-225-52-242.plesk.page/order.php?order_date='.$order_date,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
            )
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json'
            )
        );
        $response = curl_exec($curl);
        curl_close($curl);
        // echo $response;*/

        $url = 'http://sysmaco.fortidyndns.com:8063/Api/Softtech-KGAInteg/DailyKGASales?Brand=KGA&Dealer=KEPL';
        // Create a new cURL resource
        $ch = curl_init($url);
        // Setup request to send json via POST`
        $payload = json_encode(array(
                'Brand' => 'KGA',
                'MTDDate' => $order_date
            )
        );
        // Attach encoded JSON string to the POST fields
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        // Set the content type to application/json
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Softtech-Subscription-Key: kga96752527@softtech', 
                'Content-Type: application/json'
            )
        );
        // Return response instead of outputting
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // Execute the POST request
        $result = curl_exec($ch);
        // Get the POST request header status
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        // If header status is not Created or not OK, return error message
        //if ( $status !== 201 || $status !== 200 ) {
            //die("Error: call to URL $url failed with status $status, response $result, curl_error " . curl_error($ch) . ", curl_errno " . curl_errno($ch));
        //}
        // Close cURL resource
        curl_close($ch);
        // if you need to process the response from the API further
        $resp = json_decode($result, true);
        //echo 'DailyKGASales:- '.count($response['DailyKGASales']);
        //echo '<pre>'; print_r($response);
        $response = json_encode($resp);
        $myArr = json_decode($response);
        $Response = $myArr->Response;
        if($Response == "True"){
            $DailyKGASales = $myArr->DailyKGASales;
            $TotalDailyKGASales = count($myArr->DailyKGASales);
            if(!empty($DailyKGASales)){
                foreach($DailyKGASales as $value){

                    # Check product item
                    $product_id = null;
                    $item = trim($value->Item);
                    $checkProduct = Product::where('title','LIKE',$item)->first();
                    if(!empty($checkProduct)){
                        $product_id = $checkProduct->id;
                    }
                    # Check class_name as category or cat_id
                    $cat_id = null;
                    $checkCategory = Category::where('name','LIKE',$value->Class)->first();
                    if(!empty($checkCategory)){
                        $cat_id = $checkCategory->id;
                    }
                    # Check Branch / Showroom
                    $branch_id = null;
                    $checkBranch = Branch::where('name','LIKE',$value->Branch)->first();
                    if(!empty($checkBranch)){
                        $branch_id = $checkBranch->id;
                    } else {
                        $branch_id = Branch::insertGetId([
                            'name' => $value->Branch,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);
                    }

                    $existSame = DB::table('kga_sales_data')->where('bill_date',$order_date)->where('bill_no',$value->BillNo)->where('barcode', $value->Barcode)->where('serial', $value->Serial)->first();
                    if(empty($existSame)){
                        $salesData = array(
                            'product_id' => $product_id,
                            'cat_id' => $cat_id,
                            'bill_date' => $order_date,
                            'bill_no' => $value->BillNo,
                            'item' => $value->Item,
                            'brand' => $value->Brand,
                            'class_name' => $value->Class,
                            'customer_name' => $value->Customer,
                            'address' => $value->Address,
                            'near_location' => $value->NearLocation,
                            'pincode' => $value->PIN,  
                            'mobile' => $value->Mobile,  
                            'phone' => $value->Phone,  
                            'barcode' => $value->Barcode,  
                            'serial' => $value->Serial,  
                            'branch' => $value->Branch,
                            'branch_id' => $branch_id,
                            'created_at' => date('Y-m-d H:i:s')
                        );
        
                        DB::table('kga_sales_data')->insert($salesData);
                    }
                
                    
    
                }
            }

            echo '<pre>'; print_r($myArr);
            echo $TotalDailyKGASales; die;
        }        
        // dd($response);
    }
    /**
     * Send Notification & Book Installation Call On A Date (default current) Which is Not Covered Recently
     *
     */
    public function installation_request(Request $request)
    {
        DB::table('test')->insert([
            'data' => date('Y-m-d H:i:s')." installation_request",
            'created_at' => date('Y-m-d H:i:s')
        ]); 
        // $bill_date = !empty($request->bill_date)?$request->bill_date:date('Y-m-d');
        // $bill_date = date('Y-m-d'); 
        // echo $bill_date; die;

        // $sales_data = KgaSalesData::select('*')->with('product:id,goods_type,is_installable')->where('bill_date',$bill_date)->where('is_covered', 0);        
        // $sales_data = $sales_data->whereHas('product', function ($q){
        //     $q->where('is_installable', 1);
        // });
        $sales_data = KgaSalesData::select('*')->with('product:id,goods_type,is_installable')->where('is_covered', 0);        
        $sales_data = $sales_data->whereHas('product', function ($q){
            $q->where('is_installable', 1);
        });        
        $sales_data = $sales_data->get()->toArray();
        //dd($sales_data);
        if(!empty($sales_data)){
            foreach($sales_data as $item){

                $existSame = DB::table('installations')->where('entry_date',$item['bill_date'])->where('bill_no',$item['bill_no'])->where('product_sl_no', $item['serial'])->first();

                if(empty($existSame)){

                    $goods_type = $item['product']['goods_type'];
                    // dd($goods_type);

                    $pincode = $item['pincode'];
                    $get_service_patner_pincode = ServicePartnerPincode::where('number',$pincode)->where('product_type',$goods_type)->first();
                    if(!empty($get_service_patner_pincode)){
                        $service_partner_id = $get_service_patner_pincode->service_partner_id;
                        
                        $service_partner = ServicePartner::find($service_partner_id);
                        $email = $service_partner->email;
                        $person_name = $service_partner->person_name;
                        // dd($person_name);

                        $service_charge = null;
                        $exist_partner_charge = ServicePartnerCharge::where('service_partner_id',$service_partner_id)->where('product_id', $item['product_id'])->first();

                        if(!empty($exist_partner_charge)){
                            if(!empty($exist_partner_charge->installation)){
                                $service_charge = $exist_partner_charge->installation;
                            }
                        }
                        
                        // echo '<pre>'; print_r($exist_partner_charge);
                        // echo $item['id'].' - charge is '.$service_charge.'<br/>';
                        // dd('Hi');
                        if(!empty($service_charge)){
                            // dd("Service Charge Found. Installation call booking");
                            $installationData = array(
                                'unique_id' => 'INSTAL'.genAutoIncreNoYearWise(6,'installations',date('Y')),
                                'service_partner_id' => $service_partner_id,
                                'bill_no' => $item['bill_no'],
                                'mail_send' => 1,
                                'service_partner_email' => $email,
                                'pincode' => $item['pincode'],
                                'branch' => $item['branch'],
                                'entry_date' => $item['bill_date'],
                                'address' => $item['address'],
                                'mobile_no' => $item['mobile'],
                                'phone_no' => $item['phone'],
                                'delivery_date' => $item['bill_date'],
                                'brand' => $item['brand'],
                                'class' => $item['class_name'],
                                'service_charge' => $service_charge,
                                'product_name' => $item['item'],
                                'product_id' => $item['product_id'],
                                'product_sl_no' => $item['serial'],
                                'customer_name' => $item['customer_name'],
                                'created_at' => date('Y-m-d H:i:s')
                            );
                            
                            Installation::insert($installationData);
                            // dd($installationData);
                        } else {
                            // dd("Service Charge Not Found. Incomplete installation call booking");
                            $incomplete_installation = array(
                                'product_id' => $item['product_id'],
                                'cat_id' => $item['cat_id'],
                                'service_partner_id' => $service_partner_id,
                                'item' => $item['item'],
                                'class_name' => $item['class_name'],
                                'bill_date' => $item['bill_date'],
                                'bill_no' => $item['bill_no'],
                                'barcode' => $item['barcode'],
                                'serial' => $item['serial'],
                                'branch' => $item['branch'],
                                'pincode' => $item['pincode'],
                                'customer_name' => $item['customer_name'],
                                'address' => $item['address'],
                                'near_location' => $item['near_location'],
                                'mobile' => $item['mobile'],
                                'phone' => $item['phone'],
                                'created_at' => date('Y-m-d H:i:s')
                            );
                            IncompleteInstallation::insert($incomplete_installation);
                        }


                    } else {
                        # Default email id
                        $email = ServicePartner::find(1)->email;
                        $person_name = "KGA Admin";
                        // dd($person_name);
                        $installationData = array(
                            'unique_id' => 'INSTAL'.genAutoIncreNoYearWise(6,'installations',date('Y')),
                            'service_partner_id' => 1,
                            'bill_no' => $item['bill_no'],
                            'mail_send' => 1,
                            'service_partner_email' => $email,
                            'pincode' => $item['pincode'],
                            'branch' => $item['branch'],
                            'entry_date' => $item['bill_date'],
                            'address' => $item['address'],
                            'mobile_no' => $item['mobile'],
                            'phone_no' => $item['phone'],
                            'delivery_date' => $item['bill_date'],
                            'brand' => $item['brand'],
                            'class' => $item['class_name'],
                            'product_name' => $item['item'],
                            'product_id' => $item['product_id'],
                            'product_sl_no' => $item['serial'],
                            'customer_name' => $item['customer_name'],
                            'created_at' => date('Y-m-d H:i:s')
                        );
                        
                        Installation::insert($installationData);
                    }


                    /* Mail Send Service Partner */
                    $mailData['email'] = $email;
                    $mailData['name'] = $person_name;
                    $mailData['subject'] = "KGA SERVICE NOTIFICATION";
                    $mailData['bill_no'] = $item['bill_no'];
                    $mailData['customer_name'] = $item['customer_name'];
                    $mailData['branch'] = $item['branch'];
                    $mailData['address'] = $item['address'];
                    $mailData['mobile_no'] = $item['mobile'];
                    $mailData['phone_no'] = $item['phone'];
                    $mailData['bill_date'] = $item['bill_date'];
                    $mailData['brand'] = $item['brand'];
                    $mailData['class'] = $item['class_name'];
                    $mailData['product_sl_no'] = $item['serial'];
                    $mailData['product_name'] = $item['item'];
                    $mailData['pincode'] = $item['pincode'];
                    // echo '<pre>Service Partner mailData:- '; print_r($mailData); 
                    // die;
                    $this->mailSendData($mailData); 

                    $customer_name = $item['customer_name'];
                    $customer_mobile_no = $item['mobile'];

                

                    /* Greetings SMS Send To PANEL_LED Customer */

                    if($item['class_name'] == 'PANEL_LED'){     
                                      
                        $this->sendTVGreetingsSMSCustomer($customer_name,$customer_mobile_no);
                    }                
                    /* ++++++++++++++ */
                    
                }

            }
            // echo 'Not Covered';
        } 
        else {
            // echo 'All Covered';
        }

        KgaSalesData::where('is_covered', 0)->update(['is_covered'=>1]);
        // KgaSalesData::where('bill_date',$bill_date)->update(['is_covered'=>1]);
       // dd($sales_data);
    }

    public function send_chimney_greeting(Request $request)
    {
        # Send Greeting SMS To Chimney Customer ... 
        DB::table('test')->insert([
            'data' => date('Y-m-d H:i:s')." send_chimney_greeting",
            'created_at' => date('Y-m-d H:i:s')
        ]);   

        $data = Installation::whereHas('product', function($p){
            $p->where('goods_type', 'chimney');
        })->where('is_closed', 0)->where('is_cancelled', 0)->where('is_chimney_sms_sent', 0)->get();  
       
        if(!empty($data)){
            foreach($data as $key =>$item){
                $product = Product::findOrFail($item->product_id);
                $product_title = Str::limit($product->title, 27);
                $customer_mobile_no = $item->mobile_no;
                if($product->installable_amount>0){
                    $this->sendChimneyWithChargesGreetingsSMSCustomer($customer_mobile_no, $product_title, $product->installable_amount);
                    Installation::where('id', $item->id)->update([
                        'is_chimney_sms_sent' => 1
                    ]);
                }else{
                    $this->sendChimneyGreetingsSMSCustomer($customer_mobile_no);
                    Installation::where('id', $item->id)->update([
                        'is_chimney_sms_sent' => 1
                    ]);
                }
            }
        }

     
    }

    private function mailSendData($data)
    {
        # mail send data...
        $mailData['email'] = $data['email'];
        // $mailData['email'] = 'arnabm.oneness@gmail.com';
        $mailData['name'] = $data['name'];
        $mailData['subject'] = $data['subject'];
        $mailBody = "";
        
        $mailBody .= "<h1>Hi, ".$data['name']."!</h1> <br/>";
        $mailBody .= "<p>You have a new notification for installing goods.<p>";
        $mailBody .= "Please find the details below , <br/>";
        
        
        $mailBody .= "
        <table cellspacing='0' cellpadding='0' style='border: 1px solid #ddd;'>
            <thead>
                <tr>
                    <th style='padding:5px; border: 1px solid #ddd;'>Order Detail</th>
                    <th style='padding:5px; border: 1px solid #ddd;'>Product Detail</th>
                    <th style='padding:5px; border: 1px solid #ddd;'>Customer Detail</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style='padding:5px; border: 1px solid #ddd;'>Bill No: <strong>".$data['bill_no']."</strong> </td>
                    <td style='padding:5px; border: 1px solid #ddd;'>&nbsp;</td>
                    <td style='padding:5px; border: 1px solid #ddd;'>Customer Name: <strong>".$data['customer_name']."</strong> </td>
                </tr>
                <tr>
                    <td style='padding:5px; border: 1px solid #ddd;'>Bill Date:<strong>".$data['bill_date']."</strong></td>
                    <td style='padding:5px; border: 1px solid #ddd;'>Product Name: <strong>".$data['product_name']."</strong></td>
                    <td style='padding:5px; border: 1px solid #ddd;'>Address: <strong>".$data['address']." </strong></td>
                </tr>
                <tr>
                    <td style='padding:5px; border: 1px solid #ddd;'>Branch: <strong>".$data['branch']."</strong></td>
                    <td style='padding:5px; border: 1px solid #ddd;'>Brand: <strong>".$data['brand']."</strong> </td>
                    <td style='padding:5px; border: 1px solid #ddd;'>&nbsp;</td>
                </tr>
                <tr>
                    <td style='padding:5px; border: 1px solid #ddd;'>&nbsp;</td>
                    <td style='padding:5px; border: 1px solid #ddd;'>Class: <strong>".$data['class']."</strong></td>
                    <td style='padding:5px; border: 1px solid #ddd;'>Customer PIN Code: <strong>".$data['pincode']."</strong></td>
                </tr>            
                <tr>
                    <td style='padding:5px; border: 1px solid #ddd;'>&nbsp;</td>
                    <td style='padding:5px; border: 1px solid #ddd;'>&nbsp;</td>
                    <td style='padding:5px; border: 1px solid #ddd;'>Contact Number: <strong>".$data['mobile_no']." / ".$data['phone_no']."</strong></td>
                </tr>
            </tbody>
        </table>
        ";


        $mailData['body'] = $mailBody;

        // dd($mailBody);
        
        $mail = sendMail($mailData);
        if($mail) {
            $details = json_encode($data);
            DB::table('mail_send')->insert([
                'email' => $data['email'],
                'bill_no' =>  $data['bill_no'],
                'details' => $details,
                'created_at' => date('Y-m-d H:i:s')
            ]);        
        }
    }

    private function sendChimneyGreetingsSMSCustomer($customer_mobile_no){
        $sms_entity_id = getSingleAttributeTable('settings','id',1,'sms_entity_id');
        $sms_template_id = "1707171266721515222";
        // $sms_template_id = "1707169745869123723";
        $myMessage = urlencode('"Congratulations on your brand new KGA Chimney! Please do not pay any installation charge to the service person as it is free and included for all KGA Customers. Also a 5feet pipe comes free with your KGA Chimney. It is included in your product packaging and is free of charge." AMMRTL');

        $sms_url = 'https://sms.bluwaves.in/sendsms/bulk.php?username=ammrllp&password=123456789&type=TEXT&sender=AMMRTL&mobile='.$customer_mobile_no.'&message='.$myMessage.'&entityId='.$sms_entity_id.'&templateId='.$sms_template_id;

        // echo $myMessage; die;

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
    private function sendChimneyWithChargesGreetingsSMSCustomer($customer_mobile_no, $product_title, $installable_amount){
        $sms_entity_id = getSingleAttributeTable('settings','id',1,'sms_entity_id');
        $sms_template_id = "1707171266716632896";
        $myMessage = urlencode('Congratulations on your brand '.$product_title.' ! Please pay an installation charge of '.$installable_amount.'/- to the service person. A 4.5feet pipe comes free with your KGA Chimney. It is included in your product packaging and is free of charge.AMMRTL');
        $sms_url = 'https://sms.bluwaves.in/sendsms/bulk.php?username=ammrllp&password=123456789&type=TEXT&sender=AMMRTL&mobile='.$customer_mobile_no.'&message='.$myMessage.'&entityId='.$sms_entity_id.'&templateId='.$sms_template_id;

        // echo $myMessage; die;

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

    private function sendTVGreetingsSMSCustomer($customer_name,$customer_mobile_no){
        
        $query_calling_number = "6291117317";
                
        $sms_entity_id = getSingleAttributeTable('settings','id',1,'sms_entity_id');
        $sms_template_id = "1707169658704777082";
        

        $myMessage = urlencode('Dear '.$customer_name.' Welcome to KGA Family and congratulations on your brand new KGA LED TV! Experience true colours of life with KGA LED TVs. We are at your assistance 24x7. For any query please call '.$query_calling_number.' AMMRTL');


        $sms_url = 'https://sms.bluwaves.in/sendsms/bulk.php?username=ammrllp&password=123456789&type=TEXT&sender=AMMRTL&mobile='.$customer_mobile_no.'&message='.$myMessage.'&entityId='.$sms_entity_id.'&templateId='.$sms_template_id;

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
            'phone' => $customer_mobile_no,
            'message_body' => $myMessage,
            'response_body' => $response,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Fetch KGA Live Stock Data On A Date (default yesterday)
     *
     */
    public function get_daily_stock(Request $request)
    {
        DB::table('test')->insert([
            'data' => date('Y-m-d H:i:s')." get_daily_stock",
            'created_at' => date('Y-m-d H:i:s')
        ]);  


        $mt_date = !empty($request->mt_date)?$request->mt_date:date('Y-m-d',strtotime("-1 day"));  
        // $mt_date = date('Y-m-d');  
        // dd($mt_date);     

        /*$curl = curl_init();
        curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://sweet-darwin.43-225-52-242.plesk.page/stock.php?mt_date='.$mt_date,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
            )
        );

        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json'
            )
        );

        $response = curl_exec($curl);
        dd(curl_errno($curl));
        if (curl_errno($curl)) { 
            dd(curl_error($curl));
            // print curl_error($curl); 
        } 
        curl_close($curl);*/

        $url = 'http://sysmaco.fortidyndns.com:8063/Api/Softtech-KGAInteg/DailyKGAStock?Brand=KGA&Dealer=KEPL';
        // Create a new cURL resource
        $ch = curl_init($url);
        // Setup request to send json via POST`
        $payload = json_encode(array(
                    'Brand' => 'KGA',
                    'MTDDate' => $mt_date
                )
            );
        // Attach encoded JSON string to the POST fields
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        // Set the content type to application/json
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Softtech-Subscription-Key: kga96752527@softtech', 
                'Content-Type: application/json'
            )
        );
        // Return response instead of outputting
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // Execute the POST request
        $result = curl_exec($ch);
        // Get the POST request header status
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        // If header status is not Created or not OK, return error message
        //if ( $status !== 201 || $status !== 200 ) {
            //die("Error: call to URL $url failed with status $status, response $result, curl_error " . curl_error($ch) . ", curl_errno " . curl_errno($ch));
        //}
        // Close cURL resource
        curl_close($ch);
        // if you need to process the response from the API further
        $resp = json_decode($result, true);
        //echo 'DailyKGASales:- '.count($response['DailyKGASales']);
        //echo '<pre>'; print_r($response);
        $response = json_encode($resp);
        // echo $response; 
        // die;
        $myArr = json_decode($response);
        // dd($myArr);
        $Response = $myArr->Response;
        $ResponseMessage = $myArr->ResponseMessage;
        if($Response == "True"){
            $DailyKGAStock = $myArr->DailyKGAStock;
            $TotalDailyKGAStock = count($myArr->DailyKGAStock);
            if(!empty($DailyKGAStock)){
                foreach($DailyKGAStock as $value){
        
                    # Check product item
                    $product_id = null;
                    $item = trim($value->ITEMCODE);
                    $checkProduct = Product::where('title','LIKE',$item)->first();
                    if(!empty($checkProduct)){
                        $product_id = $checkProduct->id;
                    }

                    # Check Branch / Showroom
                    $branch_id = null;
                    $checkBranch = Branch::where('name','LIKE',$value->SITECODE_INFO)->first();
                    if(!empty($checkBranch)){
                        $branch_id = $checkBranch->id;
                    } else {
                        $branch_id = Branch::insertGetId([
                            'name' => $value->SITECODE_INFO,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);
                    }

                    $stock_date = date('Y-m-d', strtotime($value->STOCK_DATE));

                    $checkExistSame = DB::table('kga_stock_data')->where('stock_date',$stock_date)->where('product_id',$product_id)->where('branch_id',$branch_id)->first();

                    if(empty($checkExistSame)){                      
                                                        
                        $stockData = array(
                            'product_id' => $product_id,
                            'branch_id' => $branch_id,
                            'stock_date' => $stock_date,
                            'sitecode' => $value->SITECODE,
                            'dealer' => $value->DEALER,
                            'sitecode_info' => $value->SITECODE_INFO,
                            'itemcode' => $value->ITEMCODE,
                            'itemdesc' => $value->ITEMDESC,
                            'product_class_name' => $value->PRODUCT_CLASS_NAME,
                            'opening' => $value->OPENING,
                            'received' => $value->RECEIVED,
                            'issued' => $value->ISSUED,
                            'closing' => $value->CLOSING,
                            'available' => $value->AVAILABLE,
                            'defective' => $value->DEFECTIVE,
                            'display' => $value->DISPLAY,
                            'transit' => $value->TRANSIT,
                            'defective_transit' => $value->DEFECTIVE_TRANSIT,
                            'created_at' => date('Y-m-d H:i:s')
                        );
            
                        DB::table('kga_stock_data')->insert($stockData);
                      
                    }
        
                }
            }
            
            echo 'TotalDailyKGAStock:- '.$TotalDailyKGAStock;
            echo '<pre>'; print_r($myArr);
            die;
        } else {
            echo 'Stock Date:- '.$mt_date.' <br/>';
            echo 'Response:- '.$Response.' <br/>';
            echo 'ResponseMessage:- '.$ResponseMessage.' <br/>';
        }
        

    }

    public function send_amc_request(Request $request)
    {
        ### Currently This Cron Stopped ###
        dd('Hi');
        // $bill_date = !empty($request->bill_date)?$request->bill_date:date('Y-m-d',strtotime("-1 day"));
        $bill_date = date('Y-m-d'); 
        // echo $bill_date; die;

        $sales_data = KgaSalesData::select('*')->with('product:id,goods_type,is_amc_applicable')->where('bill_date',$bill_date);        
        $sales_data = $sales_data->whereHas('product', function ($q){
            $q->where('is_amc_applicable', 1);
        });        
        $sales_data = $sales_data->get();

        if(!empty($sales_data)){
            foreach($sales_data as $item){

                $reqArr = array(
                    'customer_name' => $item->customer_name,
                    'customer_phone' => $item->mobile,
                    'product_id' => $item->product_id,
                    'bill_date' => $item->bill_date,
                    'bill_no' => $item->bill_no,
                    'barcode' => $item->barcode,
                    'serial' => $item->serial,
                    'created_at' => date('Y-m-d H:i:s')
                );
                CustomerAmcRequest::insert($reqArr);
            }
        }

        


    }

 public function update_product_warranty_status(){
        $RepairList = Repair::orderBy('id', 'DESC')->where('in_warranty', 1)->get();
        if(count($RepairList)>0){
            foreach($RepairList as $key =>$repair){
                $created_at = $repair->created_at;      
                $product_id = $repair->product_id;
                $in_warranty = 1;      
                $product = Product::find($product_id);
                $warranty_period = $product->warranty_period;
                $warranty_date = null;
                if(!empty($warranty_period)){
                    $warranty_end_date = date('Y-m-d', strtotime($repair->order_date. ' + '.$warranty_period.' months'));
                    $warranty_date = date('Y-m-d', strtotime($warranty_end_date.'-1 days'));
                    
                    // if(date('Y-m-d', strtotime($created_at)) < $warranty_date ){
                    //     $in_warranty = 0;
                    // }
        
                    if(date('Y-m-d', strtotime($created_at)) > $warranty_date){
                        $in_warranty = 0;
                    }
                }
                $updateArr = array(
                    'in_warranty' => $in_warranty,
                    'updated_at' => date('Y-m-d H:i:s')
                );
                Repair::where('id',$repair->id)->update($updateArr);
            }
            DB::table('test')->insert([
                'data' => date('Y-m-d H:i:s')." update_product_warranty_status",
                'created_at' => date('Y-m-d H:i:s')
            ]); 
        }
    }
    public function Dap_payment_success(Request $request){
        if (!isset($request->order_id, $request->order_token, $request->dap_id)) {
            return view('errors.404');
        }
        $order_id = $request->order_id;
        $dap_id = $request->dap_id;
        $headers = array(
            "Content-Type: application/json",
            "x-api-version: ".env('CASHFREE_API_VERSION'),
            "x-client-id: ".env('CASHFREE_API_KEY'),
            "x-client-secret: ".env('CASHFREE_API_SECRET')
        );
        // Set the Cashfree sandbox URL for checking order status
            $status_url = env('CASHFREE_BASE_URL')."/pg/orders/{$order_id}";

            // Initialize cURL session
            $curl = curl_init($status_url);

            // Set cURL options for GET request
            curl_setopt($curl, CURLOPT_URL, $status_url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

            // Execute the cURL request and get the response
            $status_resp = curl_exec($curl);

            // Close the cURL session
            curl_close($curl);

            // Decode the JSON response to get the order status
            $response = json_decode($status_resp);
            // Extract order status and customer details
            $order_status = $response->order_status;
            $customer_id = $response->customer_details->customer_id;
            $customer_name = $response->customer_details->customer_name;
            $customer_phone = $response->customer_details->customer_phone;
            $order_amount = $response->order_amount;
            if($dap_id){
                $data = DapServicePayment::where('dap_service_id',$dap_id)->first();
                if(!$data){
                    $DapServicePayment = new DapServicePayment;
                    $DapServicePayment->cashfree_order_id = $order_id;
                    $DapServicePayment->payment_id = 'payment_'.time().'_'.rand(11111,99999);
                    $DapServicePayment->dap_service_id = $dap_id;
                    $DapServicePayment->invoice_id = generateDapInvoiceId();
                    $DapServicePayment->status = $order_status;
                    $DapServicePayment->cashfree_customer_id = $customer_id;
                    $DapServicePayment->customer_name = $customer_name;
                    $DapServicePayment->customer_phone = $customer_phone;
                    $DapServicePayment->amount = $order_amount;
                    $DapServicePayment->payment_date = date('Y-m-d');
                    $DapServicePayment->created_at = Carbon::now();
                    $DapServicePayment->save();
                    if($order_status==="PAID"){
                        $DapService = DapService::find($dap_id);
                        $DapService->is_paid = 1; //Pending Or Link Send
                        $DapService->payment_date = now(); //Pending Or Link Send
                        $DapService->save();
                        // $DapServicePayment->save();
                        return view('errors.200');
                    }else{
                        return view('errors.200');
                    }
                }else{
                    return view('errors.201');
                }
            }else{
                abort(500);
            }
    }
    public function Dap_payment_link(Request $request){
        $id = $request->d;   
        $paymentLink = DB::table('dap_payment_links')->where('id', $id)->first();
        if($paymentLink && $paymentLink->link){
            return redirect($paymentLink->link);
        }else{
            abort(404);
        }
    }
    public function CRP_payment_link(Request $request){
        $id = $request->d;   
        $paymentLink = DB::table('crp_payment_links')->where('id', $id)->first();
        if($paymentLink && $paymentLink->link){
            return redirect($paymentLink->link);
        }else{
            abort(404);
        }
    }
    public function AMC_payment_link(Request $request){
        $id = $request->d;
        $amc_serial = $request->amc_serial;   
        $paymentLink = DB::table('amc_payment_links')->where('kga_sales_id', $id)->where('amc_unique_number',$amc_serial)->first();
        if($paymentLink && $paymentLink->link){
            return redirect($paymentLink->link);
        }else{
            abort(404);
        }
    }
    public function dap_invoice(Request $request){
        $id = $request->d;
        if(!isset($request->mobile)){
            return view('dap-services.blank');
        }else{
            $dap = DapService::find($id);
            if($dap->alternate_no == $request->mobile){
                $data = DapService::with('branch','return_branch','paymentData')->find($id);
                if($data){
                    $parts_data  = DapSpearPartFinalOrder::with('productData')->where('dap_id',$id)->get();
                    $Todate = date('d-M-y');
                    $pdf = Pdf::loadView('dap-services.dap_invoice_update', compact('data','parts_data','Todate'));
                    return $pdf->download('DAP Invoice.pdf');
                }else{
                    // If the mobile number does not match, show an error message
                    $error = 'Mobile number invalid for this invoice';
                    $text = "danger";
                    return view('dap-services.blank', compact('error', 'text'));
                } 
            }else{
             // If the mobile number does not match, show an error message
             $error = 'Mobile number invalid for this invoice';
             $text = "danger";
             return view('dap-services.blank', compact('error', 'text'));
            }
        }
    }
    public function crp_invoice(Request $request){       
        $id = $request->d;
        $crp_data = CustomerPointService::with('servicePartner','paymentData')->find($id);
        if($crp_data){
            $sp_final_data = CRPFinalSpare::with('productData')->where('crp_id',$id)->get();
                if($crp_data->payment_method == 'online'){
                    $data = [
                        'crp_data' => $crp_data,
                        'sp_final_data' => $sp_final_data,
                        'Todate' => date('Y-m-d')
                    ];
                    $pdf = Pdf::loadView('customer-point-repair.online-payment-pdf', $data);
                    return $pdf->download('CRP_ONLINE_INVOICE.pdf');
                }else{
                    $data = [
                        'crp_data' => $crp_data,
                        'sp_final_data' => $sp_final_data,
                        'Todate' => date('Y-m-d')
                    ];
                    $pdf = Pdf::loadView('customer-point-repair.offline-payment-pdf', $data);
                    return $pdf->download('CRP_OFFLINE_INVOICE.pdf');
                }
        }else{
            return Response::json(['status' => false, 'message' => 'Norecord found'], 200);
        }
    }

	public function send_whatsapp(Request $request) {
		$instanceId = "cm1rl8ifs1sfbx0xll7v1maup";
		$phoneNumber = 918617207525; // Make sure to format the number correctly
		$message = "Hi Rajib";
		$url = "https://enotify.app/api/sendText?token=$instanceId&phone=$phoneNumber&message=$message";
		try {
			// Initialize cURL
			$ch = curl_init($url);

			// Set cURL options
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POST, true); // Specify that this is a POST request

			// Execute the request and get the response
			$response = curl_exec($ch);

			// Check for cURL errors
			if ($response === false) {
				throw new \Exception('cURL Error: ' . curl_error($ch)); // Throw an exception if there is an error
			}

			// Close cURL
			curl_close($ch);
			dd($response);
			// Return the response from the API
			return json_decode($response, true); // Decode JSON response

		} catch (\Exception $e) {
			// Handle exceptions
			dd($e->getMessage());
			return response()->json([
				'error' => $e->getMessage()
			], 500); // Return a JSON response with a 500 status code
		}
	}


    public function Crp_payment_success(Request $request){
        if (!isset($request->order_id, $request->order_token, $request->crp_id)) {
            return view('errors.404');
        }
        $order_id = $request->order_id;
        $crp_id = $request->crp_id;
        $headers = array(
            "Content-Type: application/json",
            "x-api-version: ".env('CASHFREE_API_VERSION'),
            "x-client-id: ".env('CASHFREE_API_KEY'),
            "x-client-secret: ".env('CASHFREE_API_SECRET')
        );
        // Set the Cashfree sandbox URL for checking order status
            $status_url = env('CASHFREE_BASE_URL')."/pg/orders/{$order_id}";

            // Initialize cURL session
            $curl = curl_init($status_url);

            // Set cURL options for GET request
            curl_setopt($curl, CURLOPT_URL, $status_url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

            // Execute the cURL request and get the response
            $status_resp = curl_exec($curl);

            // Close the cURL session
            curl_close($curl);

            // Decode the JSON response to get the order status
            $response = json_decode($status_resp);
            // Extract order status and customer details
            $order_status = $response->order_status;
            $customer_id = $response->customer_details->customer_id;
            $customer_name = $response->customer_details->customer_name;
            $customer_phone = $response->customer_details->customer_phone;
            $order_amount = $response->order_amount;
            if($crp_id){
                $data = CrpServicePayment::where('crp_service_id',$crp_id)->first();
                if(!$data){
                    $CrpServicePayment = new CrpServicePayment;
                    $CrpServicePayment->cashfree_order_id = $order_id;
                    $CrpServicePayment->payment_id = 'PAY_ONLINE'.date('dmy').'_'.rand(11111,99999);
                    $CrpServicePayment->crp_service_id = $crp_id;
                    $CrpServicePayment->status = $order_status;
                    $CrpServicePayment->cashfree_customer_id = $customer_id;
                    $CrpServicePayment->invoice_id = generateInvoiceId();
                    $CrpServicePayment->customer_name = $customer_name;
                    $CrpServicePayment->customer_phone = $customer_phone;
                    $CrpServicePayment->amount = $order_amount;
                    $CrpServicePayment->type = 'online';
                    $CrpServicePayment->payment_date = date('Y-m-d');
                    $CrpServicePayment->created_at = Carbon::now();
                    $CrpServicePayment->save();
                    if($order_status==="PAID"){
                        $CustomerPointService = CustomerPointService::find($crp_id);
                        $CustomerPointService->status = 7; //paid
                        $CustomerPointService->is_paid = 1; //paid
                        $CustomerPointService->payment_date = now(); 
                        $CustomerPointService->payment_method = "online"; 
                        $CustomerPointService->save();
                        // $CrpServicePayment->save(); 
                        //crp_invoice send through sms after payment successfull
                        if($CustomerPointService){
                            $crp =CustomerPointService::find($request->crp_id);
                            $product_name = $crp->item;
                            $mobile = $crp->alternate_no;
                            $call_id = $crp->unique_id;
                            $download_url = route('c_invoice',['d'=>$crp->id]);
            
                            // $final_amount = ($data->total_amount + $data->total_service_charge) - $data->discount_amount;
                            $query_calling_number = "6291117317";
                            $sms_entity_id = getSingleAttributeTable('settings', 'id', 1, 'sms_entity_id');
                            $sms_template_id = "1707172110576165514";
            
                            // Your product {#var#} Call ID {#var#} has been repaired. Click to download bill {#download_url#}. For assistance call 6291117317.AMMRTL
                            $myMessage = urlencode('Your product ' . $product_name . ' Call ID ' . $call_id . ' has been repaired. Click to download bill '.$download_url.'. For assistance call '.$query_calling_number.'.AMMRTL');
            
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


                        return view('errors.200');
                    }else{
                        return view('errors.200');
                    }
                }else{
                    return view('errors.201');
                }
            }else{
                abort(500);
            }
    }
	private function generateAndSendInvoice($kga_sales_id, $amc_unique_number){
		try{
		
	   $amcData = BeforeAmcSubscription::with([
		   'productAmc.AmcPlanData',
		   'productAmc.AmcDurationData'
	      ])
            ->where('kga_sales_id', $kga_sales_id)
            ->where('amc_unique_number', $amc_unique_number)
            ->first();
			
		$durations = AmcDuration::where('amc_id', $amcData->productAmc->id ?? null)
									->orderBy('duration', 'ASC')
									->get();
		 // Calculate tax values
        $taxableValue = $amcData->actual_amount;
        $gstPercentage = 9; // 9% CGST + 9% SGST = 18% total GST
        $cgst = ($taxableValue * $gstPercentage) / 100;
        $sgst = $cgst; // Assuming same state (CGST+SGST)
        $totalAmount = $taxableValue + $cgst + $sgst;

			
		// Get plan assets from ProductAmc's relationship
        $planAssets = [];
        if($amcData->productAmc && $amcData->productAmc->plan_asset_id) {
            $assetIds = explode(',', $amcData->productAmc->plan_asset_id);
            $planAssets = PlanAsset::whereIn('id', $assetIds)
                ->orderBy('name', 'asc')
                ->pluck('name')
                ->toArray();
        }

        $kgaSalesData = KgaSalesData::with('productWarranty')
            ->find($kga_sales_id);
		
		 // Generate PDF
		$pdf = PDF::loadView('amc.invoice-template',[
		    'amc' => $amcData,
			'sale' => $kgaSalesData,
			'invoice_no' => 'AMC/2526/'.$amc_unique_number,
			 'planAssets' => $planAssets,
			'normal_clean' => $amcData->productAmc->duration->normal_cleaning,
            'deep_clean' => $amcData->productAmc->duration->deep_cleaning,
            'duration' => $amcData->productAmc->duration->duration,
			'taxableValue' => $taxableValue,
            'cgst' => $cgst,
            'sgst' => $sgst,
			'totalAmount' => $totalAmount,
			'date' => now()->format('d-m-Y')
		]);
		// Save PDF
		$fileName = 'invoice_'.$amc_unique_number.'.pdf';
		$path = public_path('invoices/'.$fileName);
		$pdf->save($path);
		$pdfUrl = url('invoices/'.$fileName);
		
		// Send SMS
		$this->sendSMS($kgaSalesData->mobile,$pdfUrl);
		
		return true;
			
		}catch (\Exception $e) {
			\Log::error('Invoice Error: '.$e->getMessage());
			return false;
    	}
		
	}
	
	private function sendSMS($mobile,$pdfUrl){
		$sms_entity_id = getSingleAttributeTable('settings','id',1,'sms_entity_id');
		$sms_template_id = "1707172234124956959";

		$message = urlencode("Your AMC invoice is ready. Download: $pdfUrl - AMMR TECHNOLOGY LLP");
		$sms_url = 'https://sms.bluwaves.in/sendsms/bulk.php?username=ammrllp&password=123456789&type=TEXT&sender=AMMRTL&mobile='.$mobile.'&message='.$message.'&entityId='.$sms_entity_id.'&templateId='.$sms_template_id;
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
		
	}
	//public function testGenerateInvoice(){
		//$kga_sales_id = 108882;
		//$amc_unique_number = "AMC202501000001";
		// $data = $this->generateAndSendInvoice($kga_sales_id, $amc_unique_number);
		//dd($data['amcData'], $data['kgaSalesData'],$data['pdfUrl']);
	//}
    public function Amc_payment_success(Request $request)
    {
        //  1. Validate required params
        if (!isset(
            $request->order_id, 
            $request->order_token, 
            $request->kga_sales_id, 
            $request->amc_unique_number, 
            $request->product_comprehensive_warranty, 
            $request->amc_id, 
            $request->type, 
            $request->auth_id
        )) {
            return view('errors.404');
        }

        // 🔹 2. Initialize variables
        $type   = $request->type;
        $auth_id= $request->auth_id;
        $order_id   = $request->order_id;
        $kga_sales_id = $request->kga_sales_id;
        $amc_unique_number = $request->amc_unique_number;
        $product_comprehensive_warranty = $request->product_comprehensive_warranty; // in months
        $amc_id = $request->amc_id;

        // 🔹 3. Check if already processed
        $alreadyProcessed = AmcServicePayment::where('cashfree_order_id', $order_id)->exists()
                            || AmcSubscription::where('amc_unique_number', $amc_unique_number)->exists();

        if ($alreadyProcessed) {
            return view('errors.200'); // Already success
        }

        // 🔹 4. Prepare Cashfree headers & URL
        $headers = [
            "Content-Type: application/json",
            "x-api-version: " . env('CASHFREE_API_VERSION'),
            "x-client-id: " . env('CASHFREE_API_KEY'),
            "x-client-secret: " . env('CASHFREE_API_SECRET')
        ];
        $status_url = env('CASHFREE_BASE_URL') . "/pg/orders/{$order_id}";

        DB::beginTransaction();
        try {
            // 🔹 5. Cashfree API call
            $curl = curl_init($status_url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            $status_resp = curl_exec($curl);
            curl_close($curl);

            $response = json_decode($status_resp);
            $order_status   = $response->order_status;
            $customer_id    = $response->customer_details->customer_id;
            $customer_name  = $response->customer_details->customer_name;
            $customer_phone = $response->customer_details->customer_phone;
            $order_amount   = $response->order_amount;

            // 🔹 6. Validate Paid Order
            if (!$kga_sales_id || $order_status !== "PAID") {
                throw new Exception("Order not marked as PAID.");
            }

            // 🔹 7. Update Payment Link + Before AMC Subscription
            DB::table('amc_payment_links')->updateOrInsert(
                ['kga_sales_id' => $kga_sales_id, 'amc_unique_number' => $amc_unique_number],
                ['status' => 1, 'updated_at' => now()]
            );

            DB::table('before_amc_subscription')->updateOrInsert(
                ['kga_sales_id' => $kga_sales_id, 'amc_unique_number' => $amc_unique_number],
                ['status' => 1, 'type' => $type, 'sell_by' => $auth_id, 'payment_time' => now()]
            );

            // 🔹 8. Fetch Before AMC + Product AMC Data
            $before_amc_suscription_data = BeforeAmcSubscription::where('kga_sales_id', $kga_sales_id)
                ->where('amc_unique_number', $amc_unique_number)
                ->first();

            $amc_duration = ProductAmc::find($amc_id);
            $duration = $amc_duration->duration;

            $kga_data = KgaSalesData::find($kga_sales_id);
            $bill_date = $kga_data->bill_date;
            $comprehensive_warranty_end_date = Carbon::parse($bill_date)->addMonths($product_comprehensive_warranty);

            // 🔹 9. Calculate AMC Start & End Dates
            $todate = Carbon::now();
            if ($todate >= $comprehensive_warranty_end_date) {
                $amc_start_date = $todate;
            } else {
                $amc_start_date = $comprehensive_warranty_end_date;
            }
            $amc_end_date = Carbon::parse($amc_start_date)->addDays($duration);

            // 🔹 10. Save AMC Service Payment
            $AmcServicePayment = new AmcServicePayment();
            $AmcServicePayment->kga_sales_id = $kga_sales_id;
            $AmcServicePayment->payment_id = 'PAY_ONLINE' . date('dmy') . '_' . rand(11111, 99999);
            $AmcServicePayment->amount = $order_amount;
            $AmcServicePayment->cashfree_customer_id = $customer_id;
            $AmcServicePayment->cashfree_order_id = $order_id;
            $AmcServicePayment->status = $order_status;
            $AmcServicePayment->invoice_id = generateAmcInvoiceId();
            $AmcServicePayment->customer_name = $customer_name;
            $AmcServicePayment->customer_phone = $customer_phone;
            $AmcServicePayment->type = 'online';
            $AmcServicePayment->payment_date = date('Y-m-d');
            $AmcServicePayment->created_at = now();
            $AmcServicePayment->save();
            $AmcServicePaymentId = $AmcServicePayment->id;

            // 🔹 11. Save AMC Subscription
            $AmcSubscription = new AmcSubscription();
            $AmcSubscription->kga_sales_id = $kga_sales_id;
            $AmcSubscription->amc_unique_number = $amc_unique_number;
            $AmcSubscription->product_id = $before_amc_suscription_data->product_id;
            $AmcSubscription->serial = $before_amc_suscription_data->serial;
            $AmcSubscription->comprehensive_warranty = $product_comprehensive_warranty;
            $AmcSubscription->comprehensive_warranty_end_date = $comprehensive_warranty_end_date;
            $AmcSubscription->amc_id = $amc_id;
            $AmcSubscription->purchase_date = date('Y-m-d');
            $AmcSubscription->actual_amount = $before_amc_suscription_data->actual_amount;
            $AmcSubscription->discount = $before_amc_suscription_data->discount;
            $AmcSubscription->purchase_amount = $before_amc_suscription_data->purchase_amount;
            $AmcSubscription->amc_start_date = $amc_start_date;
            $AmcSubscription->amc_end_date = $amc_end_date;
            $AmcSubscription->type = $type;
            $AmcSubscription->sell_by = $auth_id;
            $AmcSubscription->save();
            $amcSubscriptionId = $AmcSubscription->id;

            // 🔹 12. WhatsApp Invoice Sending
            $mobile = $customer_phone;
            $download_url = route('whatsapp_amc_invoice', $amcSubscriptionId);

            $apiDomainUrl   = config('whatsapp.api_domain_url');
            $channelNumber  = config('whatsapp.channel_number');
            $apiKey         = config('whatsapp.api_key');
            $templateName   = 'billpay';
            $languageCode   = config('whatsapp.language_code');
            $recipientPhone = '91' . $mobile;

            $data = [
                "MessagingProduct" => "whatsapp",
                "RecipientType"    => "individual",
                "to"               => $recipientPhone,
                "Type"             => "template",
                "Template" => [
                    "Name"     => $templateName,
                    "Language" => ["Code" => $languageCode],
                    "components" => [[
                        "type"       => "body",
                        "parameters" => [["type" => "text", "text" => $download_url]]
                    ]]
                ]
            ];

            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL            => "$apiDomainUrl/api/v1.0/messages/send-template/$channelNumber",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_HTTPHEADER     => [
                    "Authorization: Bearer $apiKey",
                    "Content-Type: application/json"
                ],
                CURLOPT_POSTFIELDS     => json_encode($data),
            ]);
            $response = curl_exec($ch);
            curl_close($ch);

            // 🔹 13. Ledger Insertion
            $resolved_type = $type;
            if ($type === 'staff') {
                $user_data = User::find($auth_id);
                $amc_incentive_percentage = $user_data->amc_incentive;

                if ($user_data->role_id == 4) {
                    $resolved_type = 'service_centre';
                } elseif ($user_data->role_id == 6) {
                    $resolved_type = 'ho_sale';
                }

                $amc_incentive_amount = $AmcSubscription->purchase_amount * ($amc_incentive_percentage / 100);
                $amc_incentive_amount = number_format($amc_incentive_amount, 2, '.', '');
                $ledgerData = [
                    'type' => 'credit',
                    'user_id' => $auth_id,
                    'amount' => $amc_incentive_amount,
                    'entry_date' => date('Y-m-d'),
                    'user_type' => $resolved_type,
                    'purpose' => 'For AMC Sell',
                    'payment_id' => $AmcServicePaymentId,
                    'transaction_id' => $amc_unique_number,
                    'amc_id' => $amcSubscriptionId,
                    'kga_sales_id' => $kga_sales_id,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
                Ledger::insert($ledgerData);

                // Admin incentive
                $admin = User::find(1);
                $admin_incentive_percentage = $admin->amc_incentive;
                $admin_incentive_amount = $AmcSubscription->purchase_amount * ($admin_incentive_percentage / 100);
                $admin_incentive_amount = number_format($admin_incentive_amount, 2, '.', '');
                $adminLedgerData = [
                    'type' => 'credit',
                    'user_id' => $admin->id,
                    'amount' => $admin_incentive_amount,
                    'entry_date' => date('Y-m-d'),
                    'user_type' => $admin->type,
                    'purpose' => 'Admin Incentive for AMC Sell',
                    'payment_id' => $AmcServicePaymentId,
                    'transaction_id' => $amc_unique_number,
                    'amc_id' => $amcSubscriptionId,
                    'kga_sales_id' => $kga_sales_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                Ledger::insert($adminLedgerData);
            } else {
                $user_data = ServicePartner::find($auth_id);
                $amc_incentive_percentage = $user_data->amc_incentive;
                $amc_incentive_amount = $AmcSubscription->purchase_amount * ($amc_incentive_percentage / 100);
                $amc_incentive_amount = number_format($amc_incentive_amount, 2, '.', '');

                $ledgerData = [
                    'type' => 'credit',
                    'service_partner_id' => $auth_id,
                    'amount' => $amc_incentive_amount,
                    'entry_date' => date('Y-m-d'),
                    'user_type' => $type,
                    'purpose' => 'For AMC Sell',
                    'payment_id' => $AmcServicePaymentId,
                    'transaction_id' => $amc_unique_number,
                    'amc_id' => $amcSubscriptionId,
                    'kga_sales_id' => $kga_sales_id,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
                Ledger::insert($ledgerData);
            }

            // 🔹 14. Finalize
            DB::commit();
            $this->generateAndSendInvoice($kga_sales_id, $amc_unique_number);

            return view('errors.200');

        } catch (Exception $e) {
            DB::rollBack();
            return view('errors.201', ['error_message' => $e->getMessage()]);
        }
    }


    function Ami_Amit(){
        $id = 1;
        $customer_name = 'AMIT SAHA';
        $otp = 1234;
        $customer_mobile_no = 7908115612;

        $sms_entity_id = getSingleAttributeTable('settings','id',1,'sms_entity_id');
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
            echo '<pre>'; echo $response;
            die();
            
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
	
	public function whatsapp_amc_invoice($id){
		$subscription = AmcSubscription::with([
		 				'servicePayments',
			              'SalesData',
			 'AmcData.AmcPlanData.AmcDurationData',
		 ])->findOrFail($id);
		 $pdf = PDF::loadView('amc.subscription-pdf',compact('subscription'));
		return $pdf->download("amc-subscription-{$subscription->amc_unique_number}.pdf");
	}
}
