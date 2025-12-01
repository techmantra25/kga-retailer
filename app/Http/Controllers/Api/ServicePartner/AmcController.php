<?php

namespace App\Http\Controllers\Api\ServicePartner;

use App\Http\Controllers\Controller;
use App\Models\AmcPlanType;
use App\Models\DapDiscountRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\DB;
use App\Models\ServicePartner;
use App\Models\DapService;
use App\Models\PurchaseOrderBarcode;
use App\Models\PurchaseOrderProduct;
use App\Models\Maintenance;
use App\Models\Ledger;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Packingslip;
use App\Models\PackingslipProduct;
use App\Models\DapServicePayment;
use App\Models\MaintenanceSpare;
use App\Models\GoodsWarranty;
use App\Models\ProductWarranty;
use App\Models\Product;
use App\Models\SalesOrderProduct;
use App\Models\PackingslipBarcode;
use App\Models\SpareGoods;
use App\Models\StockBarcode;
use App\Models\SalesOrder;
use App\Models\DapSpearPartOrder;
use App\Models\DapSpearPartFinalOrder;
use App\Models\ProductAmc;
use App\Models\KgaSalesData;
use App\Models\AmcSubscription;
use App\Models\BeforeAmcSubscription;
use App\Models\ServicePartnerCharge;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;



class AmcController extends Controller
{
    // private $service_partner_id;
    // public function __construct(Request $request)
    // {
    //     # pass bearer token in Authorizations key...
    //     if (! $request->hasHeader('Authorizations')) {
    //         response()->json(["status"=>false,"message"=>"Unauthorized"],401)->send();
    //         exit();
    //     } else {
    //         $bearer_token = $request->header('Authorizations');
    //         $token = str_replace("Bearer ","",$bearer_token);            
    //         try {
    //             $this->service_partner_id = Crypt::decrypt($token);
    //             $staff = ServicePartner::find($this->service_partner_id);           
    //         } catch (DecryptException $e) {
    //             response()->json(["status"=>false,"message"=>"Mismatched token"],400)->send();
    //             exit();
    //         }
    //     }
    // }

    public $service_partner_id = 40;

    public function amc_plan_type()
    {   
        $count = 0;
        $data = AmcPlanType::where('deleted_at',1)->get()->toArray();

        if (!empty($data)) {
            $count = count($data);
            return Response::json(['status' => true, 'count'=>$count, 'data' => $data], 200);
        } else {
            return Response::json(['status' => false, 'message' => "No record found"], 200);
        }
       
    }
    public function fetch_product(Request $request)
    {   
        $search = $request->search?$request->search:null;
        
        $data = Product::where('type','fg')->select('*');
        $count = 0;
        if($search){
            $data = $data->where(function($query) use ($search){
                $query->Where('title', 'LIKE','%'.$search.'%')
                ->orWhere('public_name', 'LIKE','%'.$search.'%');
            });
        }

        $data = $data->orderBy('title','ASC')->take(25)->get()->toArray();
        if(!empty($data)){
            $count = count($data);
            return Response::json(['status' => true,'count'=>$count,'data' => $data], 200);
        }else{
            return Response::json(['status' => false,'count'=>$count,'data' => $data], 200);
        }
       
    }
    public function product_list()
    {   
        $count = 0;
        $data = Product::where('type','fg')->get()->toArray();

        if (!empty($data)) {
            $count = count($data);
            return Response::json(['status' => true, 'count'=>$count, 'data' => $data], 200);
        } else {
            return Response::json(['status' => false, 'message' => "No product found"], 200);
        }
       
    }
    public function fetch_amc_plan(Request $request,$product_id)
    {   
        $type = $request->type?$request->type:null;
        $count = 0;
        if($type){
            
            $data = ProductAmc::where('product_id',$product_id)->where('plan_id',$type)->orderBy('duration','ASC')->get()->toArray();
            if(!empty($data)){
                $count = count($data);
                return Response::json(['status' => true, 'count'=>$count, 'data' => $data], 200);
            }else{
                return Response::json(['status' => false, 'message' =>"No plan found for this product in this type"], 200);
            }
            
        }else{
            
            $data = ProductAmc::where('product_id',$product_id)->orderBy('plan_id','ASC')->orderBy('duration','ASC')->orderBy('amount','ASC')->get()->toArray();
            if(!empty($data)){
                $count = count($data);
                return Response::json(['status' => true, 'count'=>$count, 'data' => $data], 200);
            }else{
                return Response::json(['status' => false, 'message' => $type."plan not found for this product"], 200);
            }
        }
        
        
    }
    public function fetch_customer(Request $request)
    {  
        $search = !empty($request->search)?$request->search:'';
        $paginate = 25;

        $AmcSubscriptionSerials = AmcSubscription::groupBy('serial')->pluck('serial')->toArray();

        $data = KgaSalesData::whereNotIn('serial',$AmcSubscriptionSerials)->select('*');
        $totalResult =KgaSalesData::whereNotIn('serial',$AmcSubscriptionSerials)->select('*');


        if(!empty($search)){
            $data = $data->where(function($query) use ($search){
                $query->Where('bill_no', 'LIKE','%'.$search.'%')
                ->orWhere('customer_name', 'LIKE','%'.$search.'%')
                ->orWhere('mobile', 'LIKE','%'.$search.'%')
                ->orWhere('phone', 'LIKE','%'.$search.'%')
                ->orWhere('barcode', 'LIKE','%'.$search.'%')
                ->orWhere('serial', 'LIKE','%'.$search.'%');
            });
            $totalResult = $totalResult->where(function($query) use ($search){
                $query->Where('bill_no', 'LIKE','%'.$search.'%')
                ->orWhere('customer_name', 'LIKE','%'.$search.'%')
                ->orWhere('mobile', 'LIKE','%'.$search.'%')
                ->orWhere('phone', 'LIKE','%'.$search.'%')
                ->orWhere('barcode', 'LIKE','%'.$search.'%')
                ->orWhere('serial', 'LIKE','%'.$search.'%');
            });
        }


        $data = $data->orderBy('id','DESC')->paginate($paginate);
        // dd($data);
        $totalResult = $totalResult->count();

        if(!empty($data)){
            return Response::json(['status' => true, 'data' => $data], 200);
        }else{
            return Response::json(['status' => false, 'message' =>"No record found!"], 200);
        }
       
    }

    public function select_customer(Request $request,$kga_sales_id)
    {  
        $data = KgaSalesData::find($kga_sales_id);
        $product_id = $data->product_id;
        
        $product_comprehensive_warranty =ProductWarranty::where('goods_id',$product_id)->where('dealer_type','khosla')->where('warranty_type','comprehensive')->value('warranty_period');
        $data->product_comprehensive_warranty = $product_comprehensive_warranty?$product_comprehensive_warranty:0;
        
        if(!empty($data)){
            return Response::json(['status' => true, 'data' => $data], 200);
        }else{
            return Response::json(['status' => false, 'message' =>"No record found!"], 200);
        }
    }



    public function discount_request(Request $request)
    { 

        // dd($request->all());
        $kga_sales_id = $request->kga_sales_id;
        $amc_unique_number = getAmcUniqueNumber();
        $product_id = $request->product_id;
        $serial = $request->serial;
        $amc_id = $request->amc_id;
        $product_comprehensive_warranty = $request->product_comprehensive_warranty;
        $actual_amount = $request->actual_amount;
        $discount_request = $request->discount_request;
        $purchase_amount = $request->purchase_amount;
        // $sell_by = $service_partner_id;
        $sell_by = 40;
        $type = 'servicepartner';


        // Find existing record with status 2
        $data = BeforeAmcSubscription::where('kga_sales_id', $kga_sales_id)
        ->where(function ($query) {
            $query->where('status', 2)
                ->orWhere('status', 3);
        })->orderBy('id','DESC')
        ->first();
        if ($data) {
            // Update existing record
            // $data->amc_unique_number = $amc_unique_number;
            $data->amc_id = $amc_id;
            $data->actual_amount = $actual_amount;
            $data->discount = 0;
            $data->discount_request = $discount_request;
            $data->purchase_amount = $purchase_amount;
            $data->status = 2;
            $saved = $data->save();
        } else {
            // Create a new record
            $new_data = new BeforeAmcSubscription();
            $new_data->kga_sales_id = $kga_sales_id;
            $new_data->amc_unique_number = $amc_unique_number;
            $new_data->product_id = $product_id;
            $new_data->serial = $serial;
            $new_data->amc_id = $amc_id;
            $new_data->comprehensive_warranty = $product_comprehensive_warranty;
            $new_data->actual_amount = $actual_amount;
            $new_data->discount_request = $discount_request;
            $new_data->purchase_amount = $purchase_amount;
            $new_data->status = 2; // Pending for admin approval
            $new_data->type = $type;
            $new_data->sell_by = $sell_by;
            $saved = $new_data->save();
        }
    
        // Return appropriate response
        if ($saved) {
            return response()->json([
                'status' => true,
                'message' => "Discount request sent, waiting for Admin Approval!"
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => "Something went wrong! Try Again"
            ], 200);
        }

    }

    public function send_payment_link(Request $request)
    {  

        // dd($request->all());
        // $actual_amount = $request->actual_amount;
        // $discount = $request->discount;
        // $purchase_amount = $request->purchase_amount;
        // $customer_name = $request->customer_name;
        // $mobile = $request->phone;
        // $kga_sales_id = $request->kga_sales_id;
        // $amc_unique_number = $request->amc_unique_number;
        // $product_id = $request->product_id;
        // $serial = $request->serial;
        // $amc_id = $request->amc_id;
        // $product_comprehensive_warranty = $request->product_comprehensive_warranty;

            $kga_sales_id = $request->kga_sales_id;
            $amc_unique_number = getAmcUniqueNumber();
            $product_id = $request->product_id;
            $serial = $request->serial;
            $amc_id = $request->amc_id;
            $product_comprehensive_warranty = $request->product_comprehensive_warranty;
            $actual_amount = $request->actual_amount;
            $discount = $request->discount;
            $purchase_amount = $request->purchase_amount;
            // $sell_by = $service_partner_id;
            $sell_by = 40;
            $type ='servicepartner';
            $mobile = $request->mobile;
            $discount = $request->discount;
            $customer_name = $request->customer_name;

           // Begin a database transaction
           DB::beginTransaction();

           try {

               $url = env('CASHFREE_BASE_URL')."/pg/orders";

               $headers = array(
                   "Content-Type: application/json",
                   "x-api-version: ".env('CASHFREE_API_VERSION'),
                   "x-client-id: ".env('CASHFREE_API_KEY'),
                   "x-client-secret: ".env('CASHFREE_API_SECRET')
               );
               $return_url = route('amc_payment_success');
              
               $data = json_encode([
                    'order_id' =>  'order_'.time().'_'.rand(11111,99999),
                    'order_amount' => $purchase_amount,
                    "order_currency" => "INR",
                    "customer_details" => [
                         "customer_id" => 'customer_'.time().'_'.rand(11111,99999),
                         "customer_name" => $customer_name,
                         "customer_phone" => $mobile,
                    ],
                    "order_meta" => [
                         'return_url' => $return_url . '/?order_id={order_id}&order_token={order_token}&kga_sales_id=' . $kga_sales_id . '&amc_unique_number=' . $amc_unique_number .'&product_comprehensive_warranty=' . $product_comprehensive_warranty .'&amc_id='. $amc_id . '&type=' . $type .'&auth_id=' . $sell_by
                         ]
               ]);
              
               $curl = curl_init($url);
               
               curl_setopt($curl, CURLOPT_URL, $url);
               curl_setopt($curl, CURLOPT_POST, true);
               curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
               curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
               curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
             
               $resp = curl_exec($curl);
               curl_close($curl);
               $link = json_decode($resp)->payment_link;
       
               DB::table('amc_payment_links')->updateOrInsert(
                   ['kga_sales_id' => $kga_sales_id,
                    'amc_unique_number' => $amc_unique_number],  // The condition to check for existing record
                   [
                       'link' => $link,            // The values to update or insert
                       'status' => 0,            // pending
                       'created_at' => date('Y-m-d H:i:s'),
                       'updated_at' => date('Y-m-d H:i:s'),

                   ]
               );

               DB::table('before_amc_subscription')->insert([
                   'kga_sales_id' => $kga_sales_id,
                   'amc_unique_number' => $amc_unique_number,
                   'product_id' => $product_id,
                   'serial' => $serial,
                   'comprehensive_warranty' => $product_comprehensive_warranty,
                   'amc_id' => $amc_id,
                   'actual_amount' => $actual_amount,
                   'discount' => $discount,
                   'purchase_amount' => $purchase_amount,
                   'status' => 0, // pending
                   'type' => $type,
                   'sell_by' => $sell_by,
                   'created_at' => date('Y-m-d H:i:s'),
                   'updated_at' => date('Y-m-d H:i:s'),
               ]);
               $record = DB::table('amc_payment_links')->where('kga_sales_id', $kga_sales_id)->where('amc_unique_number',$amc_unique_number)->first();
               if($record){
                   DB::commit();
                   $url_link = route('AMC_payment_link',['d'=>$kga_sales_id, 'amc_serial'=>$amc_unique_number]);  
                   $query_calling_number = "6291117317";
               
                   $sms_entity_id = getSingleAttributeTable('settings','id',1,'sms_entity_id');
                   $sms_template_id = "1707172234124956959";
                   
                   $myMessage = urlencode('We are pleased to inform you that your product repair charge is now ready for payment. Kindly use the following link to complete the transaction: '.$url_link.' .AMMR TECHNOLOGY LLP');

                   $sms_url = 'https://sms.bluwaves.in/sendsms/bulk.php?username=ammrllp&password=123456789&type=TEXT&sender=AMMRTL&mobile='.$mobile.'&message='.$myMessage.'&entityId='.$sms_entity_id.'&templateId='.$sms_template_id;
                
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
                   
                   return Response::json(['status'=>true, 'message' =>"Payment link send to this phone number, wating for payment!"],200);
               }else{
                   throw new Exception('Record not found.');;
               }
               
           } catch (\Exception $e) {
               DB::rollBack();
            //    dd($e->getMessage());
               return Response::json(['status'=>false, 'message' =>'Something went wrong. Please try again later!'],200);
           }


        
    }

}