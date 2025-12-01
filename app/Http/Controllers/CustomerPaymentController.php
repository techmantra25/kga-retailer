<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
use App\Models\Settings;
use App\Models\Installation;
use App\Models\Product;
use App\Models\ProductAmc;
use App\Models\CustomerAmcPackage;
use App\Models\CustomerAmcRequest;
use App\Models\DapService;
use App\Models\DapServiceSpare;
use App\Models\DapServicePayment;

class CustomerPaymentController extends Controller
{
    //
    private $cf_app_id_test;
    private $cf_secret_key_test;
    private $cf_app_id_live;
    private $cf_secret_key_live;

    public function __construct()
    {
        $settings = Settings::find(1);
        $this->cf_app_id_test = $settings->cf_app_id_test;
        $this->cf_secret_key_test = $settings->cf_secret_key_test;
        $this->cf_app_id_live = $settings->cf_app_id_live;
        $this->cf_secret_key_live = $settings->cf_secret_key_live;
    }

    /* ++++++++++++ Inhouse DAP Servicing +++++++++++++++++ */

    public function amc_offer(Request $request,$amc_request_id)
    {
        // dd($this->cf_secret_key_test);
        // $installation_id = 0;
        $amc_request = CustomerAmcRequest::find($amc_request_id);
        if(!empty($amc_request)){
            $errMsg = "";
            if(!empty($amc_request->is_availed)){
                $errMsg = "You are already availing the service.";
            }
            $product_id = $amc_request->product_id;
            $product_amcs = ProductAmc::where('product_id',$product_id)->get();
            return view('customer-amc.offer', compact('amc_request','amc_request_id','product_amcs','errMsg'));
        } else {
            return view('customer-amc.404');
        }
        
    }

    public function amc_preview(Request $request,$amc_request_id,$amc_id)
    {
        $amc_request = CustomerAmcRequest::find($amc_request_id);
        if(!empty($amc_request)){
            $amc = ProductAmc::find($amc_id);
            if(!empty($amc)){
                return view('customer-amc.preview', compact('amc_request_id','amc_id','amc_request','amc'));
            } else {
                return view('customer-amc.404');
            }            
        } else {
            return view('customer-amc.404');
        }
        
    }

    public function submit_amc(Request $request)
    {
        // dd($request->all());
        $cf_app_id = $this->cf_app_id_test;
        $cf_secret_key = $this->cf_secret_key_test;

        $params = $request->except('_token');
        $amount = $params['amount'];
        $customer_name = $params['customer_name'];
        $customer_phone = $params['customer_phone'];
        $product_id = $params['product_id'];
        $amc_request_id = $params['amc_request_id'];
        $product_title = getSingleAttributeTable('products','id',$product_id,'title');

        $amc_request = CustomerAmcRequest::find($amc_request_id);
        $bill_no = $amc_request->bill_no;
        $barcode = $amc_request->barcode;
        $serial = $amc_request->serial;

        $url = env('CASHFREE_BASE_URL')."/pg/orders";
        $link = ''.URL::to('/').'/'.'customer-payment/amc-return?order_id={order_id}&order_token={order_token}';

        $id = date('YmdHis');



        $data_string = json_encode([
            'order_amount' => $amount,
            'order_id' => "order_".$id,
            "order_currency" => 'INR',
           
            'customer_details' => [
            'customer_id' => "".$id."",
            'customer_name' => $customer_name,
            'customer_email' => "",
            'customer_phone' => $customer_phone
            ],
            'order_meta' =>[
                "return_url" => $link,
                'notify_url' => 'https://test.cashfree.com'
            ],
            'order_note' => 'AMC Purchase For Item Name: '.$product_title.' , Serial No: '.$serial.' , Barcode No: '.$barcode.' , Bill No: '.$bill_no.' '
            
        ]);

        // dd($data_string);

        $headers = array(
            'Content-Type: application/json',
            'x-client-id: '.$cf_app_id,
            'x-client-secret: '.$cf_secret_key,
            'x-api-version: '.env('CASHFREE_API_VERSION')
        );

        // Open connection
        $ch = curl_init();
        // Set the url, number of POST vars, POST data
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_URL, $url);
        //curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST"); 
        curl_setopt($ch, CURLOPT_POST, true);                                                                  
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        // Execute post
        $result = curl_exec($ch);
        $item = json_decode($result);
        
        // echo $result;
        
        curl_close($ch);
        // dd($result);



        $packageArr = array(
            'request_id' => $amc_request_id,
            'customer_name' => $customer_name,
            'customer_phone' => $customer_phone,
            'product_id' => $product_id,
            'amount' => $amount,
            'month_val' => $params['month_val'],
            'expiry_date' => $params['expiry_date'],
            'cashfree_customer_id' => $id,
            'cashfree_order_id' => "order_".$id,
            'json_response1' => $result,
            'created_at' => date('Y-m-d H:i:s')
        );
        // dd($packageArr);
        CustomerAmcPackage::insert($packageArr);



        return redirect()->to($item->payment_link);


    }

    public function amc_return(Request $request)
    {
        $cf_app_id = $this->cf_app_id_test;
        $cf_secret_key = $this->cf_secret_key_test;

        $amc_package = CustomerAmcPackage::where('cashfree_order_id', $request->order_id)->first();

        if(!empty($amc_package)){
            $request_id = $amc_package->request_id;
            $customer_name = $amc_package->customer_name;

            $url = env('CASHFREE_BASE_URL')."/pg/orders/".$request->order_id."/payments";
            $headers = array(
                    'Content-Type: application/json',
                    'x-client-id: '.$cf_app_id,
                    'x-client-secret: '.$cf_secret_key,
                    'x-api-version: '.env('CASHFREE_API_VERSION'),
                    // 'x-request-id: kga_electronics'
                );

            // Open connection
            $ch = curl_init();
            // Set the url, number of POST vars, POST data
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            
            // Execute post
            $result = curl_exec($ch);
            $item = json_decode($result);
            //dd($item[0]->cf_payment_id);
            //echo $result;
            //pr($result);
            curl_close($ch);

            
            
            
            
            if($item[0]->payment_status=='SUCCESS'){
                $message = "AMC Purchased successfully";
                $status = "success";

                CustomerAmcRequest::where('id',$request_id)->update([
                    'is_availed' => 1,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

                return view('customer-amc.complete', compact('message','status','customer_name','amc_package'));
            }else{
                
                ## Delete CustomerAmcPackage For Failure ... 
                CustomerAmcPackage::where('cashfree_order_id', $request->order_id)->delete();

                $message = "Payment is not done. Please click the link for make payment again.";
                $paymentLink = ''.URL::to('/').'/'.'amc-offer/'.$request_id;
                $status = "failure";
                return view('customer-amc.complete', compact('message','status','customer_name','paymentLink'));
            }
        } else {
            return view('customer-amc.404');
        }

        
    }

    /* ++++++++++++++++++++++++++++++++++++++++++++++++++++ */

    /* +++++++++++ Inhouse DAP Servicing ++++++++++++++++++ */

    /**
     * DAP Service View Payment Page
     * GET
    **/

    public function view_dap(Request $request,$service_id)
    {
        $data = DapService::find($service_id);
        if(!empty($data)){  
            $errMsg = '';    
            if(!empty($data->is_paid)){
                $errMsg = "Already Paid For This Service !!!";
            } 
            if(!empty($data->in_warranty)){
                $errMsg = "Your item is in warranty, No payment required !!!";                
            } 
            return view('customer-dap-repair.order', compact('data','service_id','errMsg'));
        } else {
            return view('customer-dap-repair.404');
        }
    }

    /**
     * DAP Service Submit Payment
     * POST
    **/

    public function dap_submit(Request $request)
    {
        $cf_app_id = $this->cf_app_id_test;
        $cf_secret_key = $this->cf_secret_key_test;

        $params = $request->except('_token');
        $service_id = $params['service_id'];
        $data = DapService::find($service_id);
        $customer_name = $data->customer_name;
        $customer_phone = $data->mobile;
        $product_id = $data->product_id;
        $product_title = getSingleAttributeTable('products','id',$product_id,'title');

        $barcode = $data->barcode;
        $serial = $data->serial;
        $amount = $data->total_service_charge;

        if(!empty($amount)){        

            $url = env('CASHFREE_BASE_URL')."/pg/orders";
            $link = ''.URL::to('/').'/'.'customer-payment/dap-return?order_id={order_id}&order_token={order_token}';

            $id = date('YmdHis');



            $data_string = json_encode([
                'order_amount' => $amount,
                'order_id' => "order_".$id,
                "order_currency" => 'INR',
            
                'customer_details' => [
                'customer_id' => "".$id."",
                'customer_name' => $customer_name,
                'customer_email' => "",
                'customer_phone' => $customer_phone
                ],
                'order_meta' =>[
                    "return_url" => $link,
                    'notify_url' => 'https://test.cashfree.com'
                ],
                'order_note' => 'Repairing For Item Name: '.$product_title.' , Serial No: '.$serial.' , Barcode No: '.$barcode.' '
                
            ]);

            // dd($data_string);

            $headers = array(
                'Content-Type: application/json',
                'x-client-id: '.$cf_app_id,
                'x-client-secret: '.$cf_secret_key,
                'x-api-version: '.env('CASHFREE_API_VERSION')
            );

            // Open connection
            $ch = curl_init();
            // Set the url, number of POST vars, POST data
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_URL, $url);
            //curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST"); 
            curl_setopt($ch, CURLOPT_POST, true);                                                                  
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            
            // Execute post
            $result = curl_exec($ch);
            $item = json_decode($result);        
            // echo $result;  die;      
            curl_close($ch);

            $paymentArr = array(
                'dap_service_id' => $service_id,
                'amount' => $amount,
                'cashfree_customer_id' => $id,
                'cashfree_order_id' => 'order_'.$id,
                'customer_name' => $customer_name,
                'customer_phone' => $customer_phone,
                'payment_date' => date('Y-m-d'),
                'created_at' => date('Y-m-d H:i:s')
            );
            DB::table('dap_service_payments')->insert($paymentArr);

            return redirect()->to($item->payment_link);


        } else {

            $errMsg = " No service charge found for this repairing request ";
            return view('customer-dap-repair.order', compact('data','service_id','errMsg'));

        }
    }

    /**
     * DAP Service View Payment Page
     * GET
    **/

    public function dap_return(Request $request)
    {
        $cf_app_id = $this->cf_app_id_test;
        $cf_secret_key = $this->cf_secret_key_test;

        $payments = DapServicePayment::where('cashfree_order_id', $request->order_id)->first();

        if(!empty($payments)){
            $dap_service_id = $payments->dap_service_id;
            $customer_name = $payments->customer_name;

            $url = env('CASHFREE_BASE_URL')."/pg/orders/".$request->order_id."/payments";
            $headers = array(
                    'Content-Type: application/json',
                    'x-client-id: '.$cf_app_id,
                    'x-client-secret: '.$cf_secret_key,
                    'x-api-version: 2022-09-01',
                    // 'x-request-id: kga_electronics'
                );

            // Open connection
            $ch = curl_init();
            // Set the url, number of POST vars, POST data
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            
            // Execute post
            $result = curl_exec($ch);
            $item = json_decode($result);
            //dd($item[0]->cf_payment_id);
            //echo $result;
            //pr($result);
            curl_close($ch);

            
            
            
            
            if($item[0]->payment_status=='SUCCESS'){
                $message = "Payment successfully";
                $status = "success";

                DapService::where('id',$dap_service_id)->update([
                    'payment_method' => 'online',
                    'is_paid' => 1
                ]);

                return view('customer-dap-repair.complete', compact('message','status','customer_name','payments'));
            }else{
                
                ## Delete CustomerAmcPackage For Failure ... 
                DapServicePayment::where('cashfree_order_id', $request->order_id)->delete();

                $message = "Payment is not done. Please click the link for make payment again.";
                $paymentLink = ''.URL::to('/').'/'.'view-dap/'.$request_id;
                $status = "failure";
                return view('customer-dap-repair.complete', compact('message','status','customer_name','paymentLink'));
            }
        } else {
            return view('customer-dap-repair.404');
        }
    }

    /* ++++++++++++++++++++++++++++++++++++++++++++++++++++ */


}
