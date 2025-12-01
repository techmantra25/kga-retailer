<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Installation;
use App\Models\Repair;
use App\Models\Packingslip;
use App\Models\StockInventory;
use App\Models\SalesOrder;
use App\Models\SalesOrderProduct;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderBarcode;
use App\Models\PurchaseOrderProduct;
use App\Models\Ledger;
use App\Models\Product;
use App\Models\SpareGoods;
use App\Models\Invoice;
use App\Models\Branch;
use App\Models\KgaSalesData;
use App\Models\ServicePartnerPincode;
use App\Models\CustomerAmcRequest;
use App\Models\IncompleteInstallation;
use App\Models\ServicePartner;
use App\Models\ServicePartnerCharge;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;

class TestController extends Controller
{
    //

    public function index(Request $request)
    {        
        echo 'uplaod_base_url_prefix:- '.$uplaod_base_url_prefix; die;
        $project = !empty($request->project)?$request->project:'';
        echo $this->showClientName($project);
    }

    protected function showClientName($project)
    {
        # code...

        switch ($project) {
            case "KGA":
                $clientName = "Mayank";
                break;
            case "WM":
                $clientName = "Abhinav";
                break;
            default:
                $clientName = "Unknown";
        }
        return $clientName;
    }

    public function mail_send(Request $request)
    {
        # mail send...
        $to = !empty($request->to)?$request->to:'';

        $smtp =  config('mail.mailers.smtp');
        echo '<pre>'; print_r($smtp);

        if(!empty($to)){
            $data['email'] = $to;
            $data['name'] = "Arnab M";
            $data['subject'] = "Test Email KGA";
            // $mailBody = "<h1>Hi, Arnab!</h1>";
            $mailBody = "";
            
            $mailBody .= "<h1>Hi, Arnab!</h1> <br/>";
            $mailBody .= "<p>You have a new notification for servicing new goods.<p>";
            $mailBody .= "Please find the details below , <br/>";
            $mailBody .= "
            <table cellspacing='0' cellpadding='0'>
                <thead>
                    <tr>
                        <th style='padding:5px; border: 1px solid #ddd;'>Order Detail</th>
                        <th style='padding:5px; border: 1px solid #ddd;'>Product Detail</th>
                        <th style='padding:5px; border: 1px solid #ddd;'>Customer Detail</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style='padding:5px; border: 1px solid #ddd;'>Bill No: <strong>K066242223/09866</strong> </td>
                        <td style='padding:5px; border: 1px solid #ddd;'>Product Sl No: <strong>23520220003006</strong> </td>
                        <td style='padding:5px; border: 1px solid #ddd;'>Customer Name: <strong>SK MUKUL</strong> </td>
                    </tr>
                    <tr>
                        <td style='padding:5px; border: 1px solid #ddd;'>Delivery Date:<strong>24/11/2022</strong></td>
                        <td style='padding:5px; border: 1px solid #ddd;'>Product Name: <strong>KGA Ultra HD LED TV Pro Series- NS (Black)</td>
                        <td style='padding:5px; border: 1px solid #ddd;'>Address: <strong>BALARAM PUR SAMAJ UNNOION SAMITY CLUB </strong></td>
                    </tr>
                    <tr>
                        <td style='padding:5px; border: 1px solid #ddd;'>Branch: <strong>BAGUIATI SHOWROOM</strong></td>
                        <td style='padding:5px; border: 1px solid #ddd;'>Brand: <strong>ABC</strong> </td>
                        <td style='padding:5px; border: 1px solid #ddd;'>District: <strong>SOUTH 24 PARGANAS<strong></strong></td>                
                    </tr>
                    <tr>
                        <td style='padding:5px; border: 1px solid #ddd;'>&nbsp;</td>
                        <td style='padding:5px; border: 1px solid #ddd;'>Class: <strong>PANEL_LED</strong></td>
                        <td style='padding:5px; border: 1px solid #ddd;'>Customer PIN Code: <strong>700114</strong></td>
                    </tr>            
                    <tr>
                        <td style='padding:5px; border: 1px solid #ddd;'>&nbsp;</td>
                        <td style='padding:5px; border: 1px solid #ddd;'>&nbsp;</td>
                        <td style='padding:5px; border: 1px solid #ddd;'>Contact Number: <strong>9876543210</strong></td>
                    </tr>
                </tbody>
            </table>
            ";
            $data['body'] = $mailBody;
            // $data['blade_file'] = "mailview/test";
            // dd($data);
            // $files = array(
            //     'https://devbackend.kgaelectronics.com/public/uploads/service-snapshot/1683013206.jpg',
            //     'https://devbackend.kgaelectronics.com/public/uploads/service-snapshot/1683099047.png'
            // );
            $mail = sendMail($data);
            // $mail = mailSendAttachments($data,$files);
            // print_r($mail);
            if($mail) {
                echo "Sent";                
            }else {
                $errors = 'Failed to send password reset email, please try again.';
                echo $errors;
            }

        } else {
            echo "Please send <strong>to</strong> as query param";
        }
    }

    public function cookie(Request $request)
    {
        
    }

    public function changelog(Request $request)
    {
        
    }

    public function getKGAAPI(Request $request)
    {
        // dd('Hii');
        // Set The API URL
        $order_date = !empty($request->order_date)?$request->order_date:'';
        if(!empty($order_date)){
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
            // if ( $status !== 201 || $status !== 200 ) {
            //     die("Error: call to URL $url failed with status $status, response $result, curl_error " . curl_error($ch) . ", curl_errno " . curl_errno($ch));
            // }

            // Close cURL resource
            curl_close($ch);

            // if you need to process the response from the API further
            $response = json_decode($result, true);

            dd($response);
        } else {
            echo "Please add <storng>order_date</storng> as query parameter";
            die;
        }
        


    }

    public function whatsapp(Request $request)
    {
        /*$purchase_order_id = !empty($request->purchase_order_id)?$request->purchase_order_id:'';
        $product_id = !empty($request->product_id)?$request->product_id:'';  
        
        if(!empty($purchase_order_id) && !empty($product_id)){

            $po_prod = PurchaseOrderProduct::where('purchase_order_id', $purchase_order_id)->where('product_id', $product_id)->first();

            $quantity =  $po_prod->quantity_in_pack;

            $checkBarcodesExists = PurchaseOrderBarcode::where('purchase_order_id', $purchase_order_id)->where('product_id', $product_id)->count();

            // dd($checkBarcodesExists);

            if($quantity == $checkBarcodesExists){
                die('Already exists barcode');
            }

            for($i=0; $i<$quantity;$i++){
                // $barcodeGenerator = barcodeGenerator();
                $barcodeGenerator = genAutoIncreNoBarcode($product_id,date('Y'));
                $barcode_no = $barcodeGenerator['barcode_no'];
                $code_html = $barcodeGenerator['code_html'];
                $code_base64_img = $barcodeGenerator['code_base64_img'];
                $purchaseOrderBarcodeData = array(
                    'purchase_order_id' => $purchase_order_id,
                    'product_id' => $product_id,
                    'barcode_no' => $barcode_no,
                    'code_html' => $code_html,
                    'code_base64_img' => $code_base64_img,
                    'created_at' => date('Y-m-d H:i:s')
                );

                // dd($purchaseOrderBarcodeData);
                // PurchaseOrderBarcode::insert($purchaseOrderBarcodeData);

                
            }

            echo 'Barcode generated for purchase_order_id:- '.$purchase_order_id.' and product_id:- '.$product_id.' ...';

            die;

        } else {
            echo 'Please add <strong>purchase_order_id</strong> and <strong>product_id</strong> as query params ';
        }*/

        $data = Product::select('id','title','warranty_period')->where('type', 'fg')->where('warranty_status', 'yes')->whereNotNull('warranty_period')->get()->toArray();

        foreach($data as $item){
            // dd($item['id']);
            $existDealerGoodsWarranty = \App\Models\GoodsWarranty::where('goods_id', $item['id'])->where('dealer_type', 'khosla')->where('warranty_type', 'general')->first();

            if(!empty($existDealerGoodsWarranty)){
                \App\Models\GoodsWarranty::where('id', $existDealerGoodsWarranty->id)->update([
                    'goods_id' => $item['id'],
                    'dealer_type' => 'khosla',
                    'warranty_type' => 'general',
                    'general_warranty' => $item['warranty_period'],
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            } else {
                \App\Models\GoodsWarranty::insert([
                    'goods_id' => $item['id'],
                    'dealer_type' => 'khosla',
                    'warranty_type' => 'general',
                    'general_warranty' => $item['warranty_period'],
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }
        }
        dd($data);
    }

    public function get_product_title(Request $request)
    {   
        // $a = getPercentageVal(10,150);    
        // echo $a; 
        // dd($request->ip());
        
        // $sms_entity_id = getSingleAttributeTable('settings','id',1,'sms_entity_id');
        // $sms_template_id = "1707169579011343699";
        // $customer_mobile_no = "8961741161";
        // $repair_charge = 150.00;
        // $otp = "321654";
        // $payment_link = "https://payu.in/";

        // $myMessage = urlencode('"Your KGA Product is out of warranty & has been examined by KGA Engineers. The Total Repair Charge is Rs.'.$repair_charge.' Your OTP to repair is '.$otp.' Please share OTP to approve this repair charge and begin repair. You can click on this link to make online payment '.$payment_link.' or pay cash while collecting your repaired product."AMMRTL');

        // $sms_url = 'https://sms.bluwaves.in/sendsms/bulk.php?username=ammrllp&password=123456789&type=TEXT&sender=AMMRTL&mobile='.$customer_mobile_no.'&message='.$myMessage.'&entityId='.$sms_entity_id.'&templateId='.$sms_template_id;

        // // // echo $myMessage; die;

        // $curl = curl_init();

        // curl_setopt_array($curl, array(
        // CURLOPT_URL => $sms_url,
        // CURLOPT_RETURNTRANSFER => true,
        // CURLOPT_ENCODING => '',
        // CURLOPT_MAXREDIRS => 10,
        // CURLOPT_TIMEOUT => 0,
        // CURLOPT_FOLLOWLOCATION => true,
        // CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        // CURLOPT_CUSTOMREQUEST => 'GET',
        // ));

        // $response = curl_exec($curl);
        // curl_close($curl);
        // echo '<pre>'; echo $response;      
        
        /* ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ */
        /* ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ */
        /* ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ */
        /* ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ */

        // $customer_name = "Arnab";
        // $query_calling_number = "6291117317";
        // // PANEL_LED, PANEL_LED WALL MOUNT

        
        /*$sms_entity_id = getSingleAttributeTable('settings','id',1,'sms_entity_id');
        $sms_template_id = "1707169745869123723";
        $customer_mobile_no = "8961741161";
        $myMessage = urlencode('"Congratulations on your brand new KGA Chimney! Please do not pay any installation charge to the service person as it is free and included for all KGA Customers. Also a 6.5 feet pipe comes free with your KGA Chimney. It is included in your product packaging and is free of charge." AMMRTL');
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
        echo '<pre>'; echo $response;*/

        /* ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ */
        /*$query_calling_number = "6291117317";                
        $sms_entity_id = getSingleAttributeTable('settings','id',1,'sms_entity_id');
        $sms_template_id = "1707169658704777082";
        $customer_name = "Arnab Mukherjee";
        $customer_mobile_no = "8961741161";
        $myMessage = urlencode('Dear '.$customer_name.' Welcome to KGA Family and congratulations on your brand new KGA LED TV! Experience true colours of life with KGA LED TVs. We are at your assistance 24x7. For any query please call '.$query_calling_number.' AMMRTL');

        $sms_url = 'https://sms.bluwaves.in/sendsms/bulk.php?username=ammrllp&password=123456789&type=TEXT&sender=AMMRTL&mobile='.$customer_mobile_no.'&message='.$myMessage.'&entityId='.$sms_entity_id.'&templateId='.$sms_template_id;

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
        echo '<pre>'; echo $response; */

        // $sms_template_id = getSingleAttributeTable('settings','id',1,'sms_template_id');
        $sms_entity_id = getSingleAttributeTable('settings','id',1,'sms_entity_id');
        $sms_template_id = '1707173107738290074';

        $ins_rep_end_point = 'form-installation?id=';
        
        $ins_rep_id = 4317;
        $customer_mobile_no = '7908115612';
        $otp = random_int(100000, 999999);

        $sender = 'AMMRTL';
        //  $csat_base_url = 'https://kgaelectronics.com/retailer/feedback/';           
        $csat_base_url = 'https://kgaerp.in/retailer/feedback/';            
        
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
        if(curl_exec($curl) === false)
        {
            echo 'Curl error: ' . curl_error($curl);
        }
        
        // $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        // echo 'status:-'.$status;
        // if ( $status !== 201 || $status !== 200 ) {
        //     die("Error: call to URL $sms_url failed with status $status, response $response, curl_error " . curl_error($curl) . ", curl_errno " . curl_errno($curl));
        // }

        // dd(curl_errno($curl));
        if ($errno = curl_errno($curl)) { 
            $error_message = curl_strerror($errno);
            $error_message = curl_strerror($errno);
            echo "cURL error ({$errno}):\n {$error_message}";
        } 

        curl_close($curl);
        echo $response;
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

    public function upload_csv(Request $request)
    {
        # Test CSV Upload ...
        return view('test-csv-upload');
    }

    public function submit_csv(Request $request)
    {
        # Test Submit CSV Upload...

        ### **** purchase_order_id should be changed ... 
        


        $params = $request->except('_token');
        $csv = $params['csv'];

        dd($params);
        
        $rows = Excel::toArray([],$request->file('csv'));
        $data = $rows[0];
        
        $purchase_order_id = 15;   
        
        PurchaseOrderBarcode::where('purchase_order_id',$purchase_order_id)->delete();

        // foreach($data as $row){           
            
        //     $pro_name = $row[0];
        //     $barcode_no = $row[1];

        //     $product = Product::where('title', $pro_name)->first();
        //     $product_id = $product->id;
            
        //     $barcodeGenerator = barcodeGenerator($barcode_no);
        //     $code_html = $barcodeGenerator['code_html'];
        //     $code_base64_img = $barcodeGenerator['code_base64_img'];

        //     $barcodeArr = array(
        //         'purchase_order_id' => $purchase_order_id,
        //         'product_id' => $product_id,
        //         'barcode_no' => $barcode_no,
        //         'code_html' => $code_html,
        //         'code_base64_img' => $code_base64_img,
        //         'created_at' => date('Y-m-d H:i:s')
        //     );

        //     // dd($barcodeArr);

        //     PurchaseOrderBarcode::insert($barcodeArr);
            

        // }

        
        Session::flash('message', 'CSV uploaded successfully');
        return redirect()->route('test.upload-csv');
        // dd($data);

        
    }

    // Generate Installation Call Respect Of KHOSLA ORDER DATE

    public function create_khsola_installation(Request $request)
    {
        $bill_date = !empty($request->bill_date)?$request->bill_date:'';
        if(!empty($bill_date)){
            $sales = KgaSalesData::with('product:id,goods_type,is_installable')->where('bill_date', $bill_date);             
            $sales = $sales->whereHas('product', function ($q){
                $q->where('is_installable', 1);
            });
            $sales = $sales->get()->toArray();

            echo '<pre>'; print_r($sales); die;
            
            foreach($sales as $item){
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
                
                    
                }
            }

            

            
        }
        
    }

}
