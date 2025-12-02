<?php

namespace App\Http\Controllers\ServicePartner;

use Illuminate\Support\Facades\Response;
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
use App\Models\KgaSalesData;
use App\Models\AmcPlanType;
use App\Models\Product;
use App\Models\ProductAmc;
use App\Models\AmcDuration;
use App\Models\BeforeAmcSubscription;
use App\Models\AmcSubscription;
use Barryvdh\DomPDF\Facade\Pdf;

class AmcController extends Controller
{
    public function __construct(Request $request)
    {
        $this->middleware('auth:servicepartner');
    }

    public function add(Request $request)
    {
        # List Maintenace...
        
        $validatedData = $request->validate([
            'contact_no' => ['nullable', 'regex:/^[0-9]{10}$/'], // 10-digit validation
            'bill_no' => ['nullable', 'string'],               // Optional validation for bill number
            'serial' => ['nullable', 'string'],                // Optional validation for serial
        ], [
            'contact_no.regex' => 'The contact number must be a valid 10-digit number.',
        ]);
        
        $contact_no = $request->input('contact_no','');
        $bill_no = $request->input('bill_no', '');
        $serial = $request->input('serial', '');
        
        $kga_sales_data = [];
        
        if (!empty($contact_no) || !empty($bill_no) || !empty($serial)) {
            $kga_sales_data = KgaSalesData::with('product','category','AmcSubscription')->whereNotNull('product_id');
            
            if (!empty($contact_no)) {
                $kga_sales_data->where('mobile', $contact_no)->orWhere('phone', $contact_no);
            }
            
            if (!empty($bill_no)) {
                $kga_sales_data->where('bill_no', $bill_no);
            }
            
            if (!empty($serial)) {
                $kga_sales_data->where('serial', $serial);
            }
            
            $kga_sales_data = $kga_sales_data->get();
        }
        
        
        
        return view('servicepartnerweb.amc.add',compact('contact_no' ,'bill_no', 'serial','kga_sales_data'));
        
    }
    public function amc_by_product(Request $request,$kga_sales_id,$idStr){
        $kga_sales_data = KgaSalesData::with('productWarranty')->find($kga_sales_id);
        $plan_type = !empty($request->plan_type)?$request->plan_type:'';
        $duration_type = !empty($request->duration_type)?$request->duration_type:'';
        $id = Crypt::decrypt($idStr);
        // $id=30;
        $amc_plan = AmcPlanType::get();
        $amc_duration = AmcDuration::groupBy('duration')->orderBy('duration','ASC')->get();
        $product_name = Product::select('title')->find($id);
        $data = ProductAmc::with([
            'AmcPlanData' => function ($query) {
                $query->orderBy('name', 'ASC'); // Order the AmcPlanType table by 'name'
            }
        ])
        ->where('product_id', $id) // Apply the product filter
        ->select('*');

        if(!empty($plan_type)){
            $data = $data->where('plan_id', $plan_type);
        }
        if(!empty($duration_type)){
            $data = $data->where('duration', $duration_type);
        }
        $data = $data->paginate(10);

        return view('servicepartnerweb.amc.product-amc-plan', compact('id','kga_sales_id','data','product_name','amc_plan','amc_duration','plan_type','duration_type','kga_sales_data'));
        

    }

    public function prepare_for_purchase_amc_plan( Request $request,$kga_sale_id,$idStr){
        // dd($request->all());
        $auth_id = Auth::User()->id;
        $id = Crypt::decrypt($idStr);
        $product_amc_data = ProductAmc::with('AmcPlanData')->find($id);
        $kga_sales_data = KgaSalesData::with('productWarranty')->find($kga_sale_id);
        $amc_unique_number =getAmcUniqueNumber();
        $before_amc_subscription_data = BeforeAmcSubscription::with('AmcLinkData')->where('amc_id', $id)
        ->where('kga_sales_id', $kga_sale_id)->where('type','servicepartner')->where('sell_by',$auth_id)->whereNotIn('status',[2,3])
        ->whereDate('created_at', now()) // Compare only the date part of created_at
        ->orderBy('id', 'DESC')
        ->get();
        
        $amc_discount = env('AMC_DISCOUNT',0);
        return view('servicepartnerweb.amc.buy-amc-plan', compact('id','kga_sale_id','kga_sales_data','product_amc_data','amc_unique_number','before_amc_subscription_data','amc_discount'));
        
    }

    public function send_payment_link( Request $request)
    {
        $exists = AmcSubscription::where('serial', $request->serial)
            ->where('amc_id', $request->amc_id)
            ->where('amc_end_date', '>', now())
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'This product is already subscribed to an AMC plan that is still active.');
        }
        // dd($request->all());
        $actual_amount = $request->actual_amount;
        $discount = 0;
        $purchase_amount = $request->purchase_amount;
        $customer_name = $request->customer_name;
        $mobile = $request->phone;
        $kga_sales_id = $request->kga_sales_id;
        $amc_unique_number = $request->amc_unique_number;
        $product_id = $request->product_id;
        $serial = $request->serial;
        $amc_id = $request->amc_id;
        $product_comprehensive_warranty = $request->product_comprehensive_warranty?$request->product_comprehensive_warranty:0;

        //for fatching or indentify the user form User table for amc_incentive after selling amc package 
        $type = 'servicepartner';
        $auth_id = Auth::User()->id;
        // dd($auth_id);
        
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
                      'return_url' => $return_url . '/?order_id={order_id}&order_token={order_token}&kga_sales_id=' . $kga_sales_id . '&amc_unique_number=' . $amc_unique_number .'&product_comprehensive_warranty=' . $product_comprehensive_warranty .'&amc_id='. $amc_id . '&type=' . $type .'&auth_id=' . $auth_id
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
                'sell_by' => $auth_id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $record = DB::table('amc_payment_links')->where('kga_sales_id', $kga_sales_id)->where('amc_unique_number',$amc_unique_number)->first();
            if($record){
                DB::commit();
                
                $url_link = route('AMC_payment_link', [
                    'd'          => $kga_sales_id,
                    'amc_serial' => $amc_unique_number,
                ]);
                $link_params = "?d=$kga_sales_id&amc_serial=$amc_unique_number";
                sendAMCPaymentLink($mobile, $purchase_amount, $link_params, $customer_name);
                
                return redirect()->back()->with('message','Payment link send to this phone number, wating for payment!');
            }else{
                throw new Exception('Record not found.');;
            }
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Something went wrong. Please try again later! Error: ' . $e->getMessage());
        }
    }

    public function discount_request(Request $request){
         // dd($request->all());
         $kga_sales_id = $request->kga_sales_id;
         $amc_unique_number = $request->amc_unique_number;
         $product_id = $request->product_id;
         $serial = $request->serial;
         $amc_id = $request->amc_id;
         $product_comprehensive_warranty = $request->product_comprehensive_warranty;
         $actual_amount = $request->actual_amount;
         $discount_request_percentage = $request->discount_request_percentage;
         $purchase_amount = $request->purchase_amount;
         // $sell_by = $service_partner_id;
         $sell_by = Auth::User()->id;
         $type = 'servicepartner';
         try {
 
                $new_data = new BeforeAmcSubscription();
                $new_data->kga_sales_id = $kga_sales_id;
                $new_data->amc_unique_number = $amc_unique_number;
                $new_data->product_id = $product_id;
                $new_data->serial = $serial;
                $new_data->amc_id = $amc_id;
                $new_data->comprehensive_warranty = $product_comprehensive_warranty;
                $new_data->actual_amount = $actual_amount;
                $new_data->discount_request = $discount_request_percentage;
                $new_data->purchase_amount = $purchase_amount;
                $new_data->status = 2; // Pending for admin approval
                $new_data->type = $type;
                $new_data->sell_by = $sell_by;
                $saved = $new_data->save();
            // Return appropriate response
            if ($saved) {
                return redirect()->route('servicepartnerweb.amc.peding-discount-request-list')->with('message','Request send to admin, Waiting for admin approval');
            } else {
            return redirect()->back()->with('error','Something went wrong, Try again');
            }
        } catch (\Exception $e) {
            // Log the error for debugging purposes
            Log::error('Error in discount_request: ' . $e->getMessage());
    
            return redirect()->back()->with('error', 'Something went wrong, please try again');
        }
 
    }
    public function pending_discount_request_list(Request $request){
        // dd('hiiii');
        $search = !empty($request->search)?$request->search:'';
        $data = BeforeAmcSubscription::where('status',2)->orWhere('status',3)->orderBy('id','DESC')->where('type','servicepartner')->where('sell_by',Auth::User()->id);
        if(!empty($search)){
            $data = $data->where(function($query) use ($search){
                $query->where('kga_sales_id', 'LIKE','%'.$search.'%')
                ->orWhere('amc_unique_number', 'LIKE','%'.$search.'%')
                ;
            });
        }
        $data = $data->orderBy('id','DESC')->paginate(25);
        $amc_discount = env('AMC_DISCOUNT',0);
        return view('servicepartnerweb.amc.pending-request',compact('data','amc_discount','search'));
    }

    public function after_discount_send_payment_link(Request $request){
        $data = BeforeAmcSubscription::where('kga_sales_id',$request->kga_sales_id)->orderBy('id','DESC')->first();
        
        $exists = AmcSubscription::where('serial', $request->serial)
            ->where('amc_id', $request->amc_id)
            ->where('amc_end_date', '>', now())
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'This product is already subscribed to an AMC plan that is still active.');
        }
        $type = $data->type;
        $auth_id = $data->sell_by;
        $actual_amount = $data->actual_amount;
        $discount = $data->discount;
        $purchase_amount = $data->purchase_amount;
        $amc_id = $data->amc_id;
        $kga_sales_id = $request->kga_sales_id;
        $serial = $data->serial;
        $amc_unique_number = $data->amc_unique_number;
        $product_comprehensive_warranty = $data->comprehensive_warranty?$data->comprehensive_warranty:0;
        $product_id = $data->product_id;


        $kga_data = KgaSalesData::find($request->kga_sales_id);
        $customer_name = $kga_data->customer_name;
        $mobile = $kga_data->mobile;
        // dd($mobile);


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
                    'return_url' => $return_url . '/?order_id={order_id}&order_token={order_token}&kga_sales_id=' . $kga_sales_id . '&amc_unique_number=' . $amc_unique_number .'&product_comprehensive_warranty=' . $product_comprehensive_warranty .'&amc_id='. $amc_id . '&type=' . $type .'&auth_id=' . $auth_id
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

            // DB::table('before_amc_subscription')->insert([
            //     'kga_sales_id' => $kga_sales_id,
            //     'amc_unique_number' => $amc_unique_number,
            //     'product_id' => $product_id,
            //     'serial' => $serial,
            //     'comprehensive_warranty' => $product_comprehensive_warranty,
            //     'amc_id' => $amc_id,
            //     'actual_amount' => $actual_amount,
            //     'discount' => $discount,
            //     'purchase_amount' => $purchase_amount,
            //     'status' => 0, // pending
            //     'created_at' => date('Y-m-d H:i:s'),
            //     'updated_at' => date('Y-m-d H:i:s'),
            // ]);
            $record = DB::table('amc_payment_links')->where('kga_sales_id', $kga_sales_id)->where('amc_unique_number',$amc_unique_number)->first();
            if($record){
                DB::commit();
                
                $url_link = route('AMC_payment_link', [
                    'd'          => $kga_sales_id,
                    'amc_serial' => $amc_unique_number,
                ]);
                $link_params = "?d=$kga_sales_id&amc_serial=$amc_unique_number";
                sendAMCPaymentLink($mobile, $purchase_amount, $link_params, $customer_name);
                
                return redirect()->back()->with('message','Payment link send to this phone number, wating for payment!');
            }else{
                throw new Exception('Record not found.');
            }
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Something went wrong. Please try again later! Error: ' . $e->getMessage());
        }

    }
	
	public function subscription_amc_data(Request $request){
		$search = !empty($request->search)? $request->search: "";
        $date = !empty($request->date)?$request->date:'';
		$servicePartnerId = auth('servicepartner')->id();
	    $data = AmcSubscription::select('*')->where('sell_by',$servicePartnerId);
		$totalResult = AmcSubscription::select('*')->where('sell_by', $servicePartnerId);
		if(!empty($date)){
                $data = $data->where('purchase_date',$date);
                $totalResult = $totalResult->where('purchase_date',$date);
            }
           
         	if (!empty($search)) {
				$data = $data->where(function($query) use ($search) {
					$query->where('amc_unique_number', 'LIKE', '%' . $search . '%')
						->orWhere('kga_sales_id', 'LIKE', '%' . $search . '%')
						->orWhere('serial', 'LIKE', '%' . $search . '%')
						->orWhereHas('SalesData', function ($q) use ($search) {
						$q->where('item', 'LIKE', '%' . $search . '%') // Search in related SalesData
							->orWhere('customer_name', 'LIKE', '%' . $search . '%')
							->orWhere('mobile', 'LIKE', '%' . $search . '%')
							->orWhere('bill_no', 'LIKE', '%' . $search . '%')
							->orWhere('phone', 'LIKE', '%' . $search . '%');
					});
				});
			}
			 $data = $data->with('SalesData')->orderBy('id','DESC')->paginate(25);
            $totalResult = $totalResult->count();
            return view('amc.service_partner.subscription-amc-data',compact('date','data','totalResult','search'));
	}
	
	public function subscription_amc_data_view($id){
	   $subscription = AmcSubscription::with('SalesData','AmcData')->findOrFail($id);
		return view('amc.service_partner.subscription-view', compact('subscription'));
	}
	
	public function subscription_amc_data_pdf($id){
		 $subscription = AmcSubscription::with('SalesData','AmcData')->findOrFail($id);
		 $pdf = PDF::loadView('amc.service_partner.subscription-pdf',compact('subscription'));
		return $pdf->download("amc-service_partner-subscription-{$subscription->amc_unique_number}.pdf");
	}
	
	public function subscription_amc_csv(Request $request){
		
		$search = $request->input('search');
		$date   = $request->input('date');
		$servicePartnerId = auth('servicepartner')->id();
		$data = AmcSubscription::with(['SalesData','AmcData'])->where('sell_by',$servicePartnerId)->orderBy('id','DESC');
		if($date){
		  $data->where('purchase_date',$date);
		}
		
		if($search){
			  $data->where(function($query) use ($search) {
				$query->where('amc_unique_number', 'LIKE', '%' . $search . '%')
					->orWhere('kga_sales_id', 'LIKE', '%' . $search . '%')
					->orWhere('serial', 'LIKE', '%' . $search . '%')
					->orWhereHas('SalesData', function ($q) use ($search) {
						$q->where('item', 'LIKE', '%' . $search . '%')
							->orWhere('customer_name', 'LIKE', '%' . $search . '%')
							->orWhere('mobile', 'LIKE', '%' . $search . '%')
							->orWhere('bill_no', 'LIKE', '%' . $search . '%')
							->orWhere('phone', 'LIKE', '%' . $search . '%');
					});
			});
		}
		
		 $subscription = $data->get();
		$csvFileName = 'amc_service-partner_subscriptions_' . date('Ymd_his') . '.csv';
		$headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="' . $csvFileName . '"',
       ];
		
		return Response::stream(function() use($subscription){
		   $file = fopen('php://output', 'w');
		    // Add CSV headers
			fputcsv($file, [
				'KGA Sales ID',
				'AMC Unique Number',
				'Serial No',
				'Customer Name',
				'Mobile',
				'Phone',
				'Address',
				'Pincode',
				'Product Name',
				'Bill No',
				'Bill Date',
				'Purchase Date',
				'Actual Amount',
				'Discount',
				'Purchase Amount',
				'Plan Name',
				'Plan Duration (Days)',
				'AMC Start Date',
				'AMC End Date'
			]);
				foreach ($subscription as $item) {
				$salesData = $item->SalesData;
				$amcData = $item->AmcData;
				$plan = AmcPlanType::find(optional($amcData)->plan_id);	
				$planName = optional($plan)->name ?? 'N/A';
				$planAssets = is_array($plan->plan_asset_names ?? null) ? implode(', ', $plan->plan_asset_names) : '';
                $fullPlanName = $planName . ($planAssets ? ' - ' . $planAssets : '');
				$planDuration = optional($item->AmcData)->duration ?? '';
				
				fputcsv($file, [
					$item->kga_sales_id,
					$item->amc_unique_number,
					$item->serial,
					optional($salesData)->customer_name ?? '',
					optional($salesData)->mobile ?? '',
					optional($salesData)->phone ?? '',
					optional($salesData)->address ?? '',
					optional($salesData)->pincode ?? '',
					optional($salesData)->item ?? '',
					optional($salesData)->bill_no ?? '',
					optional($salesData)->bill_date ?? '',
					$item->purchase_date,
					$item->actual_amount,
					$item->discount,
					$item->purchase_amount,
					$fullPlanName,
					$planDuration,
					$item->amc_start_date,
					$item->amc_end_date,
				]);
			}
			 fclose($file);
			
		},200, $headers);
		
		
	}
       



}
