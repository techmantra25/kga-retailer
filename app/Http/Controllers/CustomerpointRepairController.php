<?php

namespace App\Http\Controllers;

use App\Models\ServicePartner;
use App\Models\Pincode;
use App\Models\CustomerPointServicePartnerPincode;
use App\Models\ServicePartnerPincode;
use App\Models\Installation;
use App\Models\CustomerPointService;
use App\Models\CustomerPointServiceSpare;
use App\Models\PurchaseOrderProduct;
use App\Models\Repair;
use App\Models\Settings;
use App\Models\Product;
use App\Models\KgaSalesData;
use App\Models\CRPFinalSpare;
use App\Models\CloseInstallation;
use App\Models\ServicePartnerCharge;
use App\Models\PurchaseOrderBarcode;
use App\Models\PurchaseOrder;
use App\Models\Ledger;
use App\Models\ReplacementRequest;
use App\Models\Changelog;
use App\Models\ReplacementChallan;
use App\Models\ProductWarranty;
use App\Models\ReplacementDispatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Barryvdh\DomPDF\Facade\Pdf;
use File; 
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Auth;

class CustomerpointRepairController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
   
    public function index(Request $request)
    {
       // if(Auth::user()->id == 8){
          //  abort(404);
        //}  else {

            $search = !empty($request->search)?$request->search:'';
            $status = !empty($request->status)?$request->status:'';
            $type = !empty($request->type)?$request->type:'';
            $paginate = 20;
            $total = ServicePartner::where('is_default', 0)->count();
            
            $totalActive = ServicePartner::where('is_default', 0)->where('status', 1)->count();
            $totlInactive = ServicePartner::where('is_default', 0)->where('status', 0)->count();
            $data = ServicePartner::select('*')->with('pincodes');
            $totalResult = ServicePartner::select('id');
            if(!empty($status)){
                if($status == 'active'){
                    $data = $data->where('status', 1);
                    $totalResult = $totalResult->where('status', 1);
                } else if ($status == 'inactive'){
                    $data = $data->where('status', 0);
                    $totalResult = $totalResult->where('status', 0);
                }
            }
            
            if(!empty($search)){
                $data = $data->where(function($query) use ($search){
                    $query->where('person_name', 'LIKE','%'.$search.'%')->orWhere('company_name','LIKE','%'.$search.'%')->orWhere('email','LIKE','%'.$search.'%')->orWhere('phone', 'LIKE', '%'.$search.'%')->orWhereHas('pincodes', function ($q) use ($search) {
                        $q->where('number', 'LIKE','%'.$search.'%');
                    });
                });
                $totalResult = $totalResult->where(function($query) use ($search){
                    $query->where('person_name', 'LIKE','%'.$search.'%')->orWhere('company_name','LIKE','%'.$search.'%')->orWhere('email','LIKE','%'.$search.'%')->orWhere('phone', 'LIKE', '%'.$search.'%')->orWhereHas('pincodes', function ($q) use ($search) {
                        $q->where('number', 'LIKE','%'.$search.'%');
                    });
                });
            }

            if(!empty($type)){
                $data = $data->where('type', $type);
                $totalResult = $totalResult->where('type', $type);
            }
            
            $data = $data->where('is_default', 0)->orderBy('id','desc')->paginate($paginate);
            $totalResult = $totalResult->where('is_default', 0)->count();

            $data = $data->appends([
                'search'=>$search,
                'type' => $type,
                'status'=>$status,
                'page'=>$request->page
            ]);

            // dd($data);
            return view('customer-point-repair.list', compact('data','totalResult','total','totalActive','totlInactive','status','search','type','paginate'));

        //}
        
    }

    public function list_booking(Request $request){
        $search = !empty($request->search)?$request->search:'';
        $entry_date = !empty($request->entry_date)?$request->entry_date:'';
        $reaching_status = !empty($request->reaching_status)?$request->reaching_status:'';
        $service_partner_id = !empty($request->service_partner_id)?$request->service_partner_id:'';
        $service_partner_name = !empty($request->service_partner_name)?$request->service_partner_name:'';
     
        $paginate = 20;
        $data = CustomerPointService::with('replacementRequest','servicePartner', 'SpareData')->select('*');
        $totalResult = CustomerPointService::select('*');
        $servicePartners = ServicePartner::where('status',1)->orderBy('company_name')->get();
        // $servicePartners = CustomerPointServicePartnerPincode::with('service_partner')->groupBy('service_partner_id')->get();
        // $servicePartners = ServicePartnerPincode::with('service_partner')->groupBy('service_partner_id')->get();

        if(!empty($search)){
            $data = $data->where('unique_id', 'LIKE', $search)->orWhere('customer_name','LIKE','%'.$search.'%')->orWhere('mobile','LIKE','%'.$search.'%')->orWhere('phone','LIKE','%'.$search.'%')->orWhere('item','LIKE','%'.$search.'%');
            $totalResult = $totalResult->where('unique_id', 'LIKE', $search)->orWhere('customer_name','LIKE','%'.$search.'%')->orWhere('mobile','LIKE','%'.$search.'%')->orWhere('phone','LIKE','%'.$search.'%')->orWhere('item','LIKE','%'.$search.'%');
        }
        if(!empty($entry_date)){
            $data = $data->where('entry_date', $entry_date);
            $totalResult = $totalResult->where('entry_date', $entry_date);
        }
        if(!empty($service_partner_id)){
            $data = $data->where('assign_service_perter_id', $service_partner_id);
            $totalResult = $totalResult->where('assign_service_perter_id', $service_partner_id);
        }
        if(!empty($reaching_status)){
            if($reaching_status == 'pending'){
                $data = $data->where('status', 0);
                $totalResult = $totalResult->where('status', 0);
            } else if ($reaching_status == 'repairing'){
                $data = $data->where('status', 3);
                $totalResult = $totalResult->where('status', 3);
            } else if ($reaching_status == 'pending-approval'){
                $data = $data->where('status', 4);
                $totalResult = $totalResult->where('status', 4);
            } else if ($reaching_status == 'success'){
                $data = $data->where('status', 7);
                $totalResult = $totalResult->where('status', 7);
            } else if ($reaching_status == 'closed'){
                $data = $data->where('status', 8);
                $totalResult = $totalResult->where('status', 8);
            } else if ($reaching_status == 'cancelled'){
                $data = $data->where('status', 9);
                $totalResult = $totalResult->where('status', 9);
            }
        }
        $data = $data->orderBy('id', 'desc')->paginate($paginate);
        // dd($data);
		
		foreach($data as $item){
		// bill_date from CustomerPointService
			$billDate = \Carbon\Carbon::parse($item->bill_date);
			$today = \Carbon\Carbon::now();
			$oneMonthAgo = $today->copy()->subMonth();
            $oneYearAgo = $today->copy()->subYear();
			
			// Default disabled
            $item->replace_button_enable = false;
			// CASE 3 → Bill date older than 1 year
			if ($billDate->lt($oneYearAgo)) {
				$item->replace_button_enable = false;
			}else{
				// CASE 1 → Within 1 month
				if ($billDate->gte($oneMonthAgo)) {
					$item->replace_button_enable = true;
				}else {
					// CASE 2 → Older than 1 month → Check repair count in 1 year
					$repairCount = CustomerPointService::where('serial', $item->serial)
						->where('entry_date', '>=', $oneYearAgo)
						->count();

					if ($repairCount >= 2) {
						$item->replace_button_enable = true;
					}
				} 
			}
		}
		
        $totalResult = $totalResult->count();

        $data = $data->appends([
            'page' => $request->page,
            'search' => $search,
            'service_partner_id' => $service_partner_id,
            'service_partner_name' => $service_partner_name,
            'entry_date' => $entry_date,
            'reaching_status' => $reaching_status
        ]);
        // dd($data);
        
        return view('customer-point-repair.list-booking', compact('data','search','paginate','totalResult','entry_date','reaching_status','service_partner_id','service_partner_name','servicePartners'));
    }
	
	//Replacement Methods
	public function replacementList(){
		$data = ReplacementRequest::with('crp_data','approval_1','approval_2')->orderBy('id','desc')->get();
		return view('customer-point-repair.replacement-list',compact('data'));
	}
	
	public function createReplacementRequest($crp_id)
	{
		$deadline = now()->addDays(2);

		ReplacementRequest::create([
			'crp_id' => $crp_id,
			'status' => 'pending',
			'report_required_till' => $deadline,
			'report_uploaded' => 0,
		]);

		return redirect()->back()->with('success', 'Replacement Report must be submitted before '.$deadline->format('d-m-Y H:i'));
	}

	
	public function uploadReplacementReport(Request $request)
	{
		$request->validate([
			'report_file' => 'required|mimes:pdf,jpg,jpeg,png|max:2048',
		]);

		try {
			$replacement = ReplacementRequest::where('crp_id', $request->id)->first();
			// Store the file
			 if ($request->hasFile('report_file')) {

				$file = $request->file('report_file');

				// Get original extension
				$extension = $file->getClientOriginalExtension();

				// Create custom filename
				$fileName = 'replacement_report_' . time() . '.' . $extension;

				// Destination path (storage/app/public/replacement_reports)
				  $destinationPath = public_path('uploads/replacement_reports');

				// Create directory if not exists
				if (!file_exists($destinationPath)) {
					mkdir($destinationPath, 0755, true);
				}

				// Move file
				$file->move($destinationPath, $fileName);

				// Save relative path (same format as store())
				$path = 'uploads/replacement_reports/' . $fileName;
			}

			// Create replacement request + deadline
			$deadLine = now()->addDays(2);

			$replacement->update([
				'report_file' => $path,
				'report_uploaded' => 1,
				'report_required_till' => $deadLine,
				'status' => 'report_uploaded',
			]);

			return redirect()->back()->with('success', 'Report uploaded successfully.');
		} catch (\Exception $e) {
			\Log::error('Replacement Report Upload Error: ' . $e->getMessage());
			return redirect()->back()->with('error', 'Something went wrong. Please try again.');
		}
	}
	
	 // Level 1 approval
    public function approveLevel1(Request $request)
	{
		$r = ReplacementRequest::findOrFail($request->id);

		$r->status = 'level_approval_1';
		$r->approval1_by = auth()->id();
		$r->approval1_at = now();
		$r->save();

		return response()->json(['success' => true]);
	}
	
	  public function approveLevel2(Request $request)
	{
		$r = ReplacementRequest::findOrFail($request->id);

		$r->status = 'completed';
		$r->approval2_by = auth()->id();
		$r->approval2_at = now();
		$r->save();

		return response()->json(['success' => true]);
	}
	
	public function generateChallanFullFlow($replacement_request_id)
{
    try {
        $replacementRequest = ReplacementRequest::findOrFail($replacement_request_id);

        //  Generate Challan
        $lastChallan = ReplacementChallan::latest()->first();
        $challan_no = 'CH-' . (optional($lastChallan)->id + 1 ?? 1);
        $oldProduct = Product::find($replacementRequest->crp_data->product_id);
		
        $productWarranty = ProductWarranty::where('goods_id', $oldProduct->id)->first();
        $challan = ReplacementChallan::create([
            'replacement_request_id' => $replacement_request_id,
            'challan_no' => $challan_no,
            // 'customer_details' =>json_encode([
			// 	"dealer_type" => optional($replacementRequest->crp_data)->dealer_type,
			// 	"customer_name" => optional($replacementRequest->crp_data)->customer_name,
			// 	"mobile"  => optional($replacementRequest->crp_data)->mobile,
			// 	"phone"   => optional($replacementRequest->crp_data)->phone,
			// 	"alternate_no" => optional($replacementRequest->crp_data)->alternate_no,
			// 	"address"    => optional($replacementRequest->crp_data)->address,
			// 	"pincode"    => optional($replacementRequest->crp_data)->pincode,
			// ]), 
            'customer_details' => $replacementRequest->crp_data,
            'product_details' => json_encode([
                'product_id' => $oldProduct->id,
                'product_name' => $oldProduct->name,
                'warranty_type' => $productWarranty->warranty_type ?? null,
                'warranty_period' => $productWarranty->warranty_period ?? null,
            ]),
        ]);
        //  Transfer Warranty
        $newProductId = $replacementRequest->crp_data->product_id; // you should set this in your form/model
		
        if ($productWarranty && $newProductId) {
           ProductWarranty::create([
                'goods_id' => $newProductId,
                'dealer_type' => $productWarranty->dealer_type,
                'warranty_type' => $productWarranty->warranty_type,
                'additional_warranty_type' => $productWarranty->additional_warranty_type,
                'warranty_period' => $productWarranty->warranty_period,
                'number_of_cleaning' => $productWarranty->number_of_cleaning,
                'number_of_deep_cleaning' => $productWarranty->number_of_deep_cleaning,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);
        }
		
		  //  Create Replacement Invoice (Bill to Service Center)
        $invoice_no = 'KGA'.genAutoIncreNoYearWiseOrder(4,'invoices',date('Y'),date('m'));
		$user = auth()->user();
		if($user->role_id == 1){
		   $serviceCenterId = $user->id;
		}else{
		  $serviceCenterId = $user->id;
		}

        $invoice = \DB::table('invoices')->insertGetId([
            'invoice_no' => $invoice_no,
            'sales_order_id' => null,
            'packingslip_id' => null,
            'dealer_id' => null,
            'service_partner_id' => null,
			'service_centre_id' => $serviceCenterId,
            'total_amount' => $oldProduct->mop ?? 0,
            'paid_amount' => 0,
            //  'customer_details' =>json_encode([
			// 	"dealer_type" => optional($replacementRequest->crp_data)->dealer_type,
			// 	"customer_name" => optional($replacementRequest->crp_data)->customer_name,
			// 	"mobile"  => optional($replacementRequest->crp_data)->mobile,
			// 	"phone"   => optional($replacementRequest->crp_data)->phone,
			// 	"alternate_no" => optional($replacementRequest->crp_data)->alternate_no,
			// 	"address"    => optional($replacementRequest->crp_data)->address,
			// 	"pincode"    => optional($replacementRequest->crp_data)->pincode,
			// ]), 
            'customer_details' => $replacementRequest->crp_data,
            'item_details' => json_encode([
                $newProductId => [
                    'product_id' => $newProductId,
                    'product_title' => $oldProduct->title,
                    'quantity' => 1,
                    'price' => $oldProduct->mop ?? 0,
                    'total_price' => $oldProduct->mop ?? 0
                ]
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Dispatch Replacement
        $dispatch = ReplacementDispatch::create([
            'replacement_request_id' => $replacement_request_id,
            'courier_name' => 'Auto-Dispatch',
            'tracking_no' => 'TRACK' . time(),
            'shipped_at' => now(),
        ]);

        //  Update Status
        $replacementRequest->status = 'dispatched';
        $replacementRequest->save();

        //  Generate OTP
        $mobile = optional($replacementRequest->crp_data)->alternate_no;
        $otp = rand(1000, 9999);
        $checkPhoneNumberValid = checkPhoneNumberValid($mobile);
        if($checkPhoneNumberValid){
                $query_calling_number = "6291117317";
                $sms_entity_id = getSingleAttributeTable('settings','id',1,'sms_entity_id');
                $sms_template_id = "1707172847011954423";

                
                $myMessage = urlencode('Your OTP for confirming the closure of your recent call is: '.$otp.'. If you did not request this, please contact '.$query_calling_number.'.AMMR TECHNOLOGY LLP');

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

                 \DB::table('replacement_otps')->insert([
                    'replacement_request_id' => $replacement_request_id,
                    'otp' => $otp,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

        }
       

        //  Return success response
        return response()->json(['success' => true, 'message' => 'Challan generated, warranty transferred, dispatched & OTP: '.$otp]);

    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}
	
	
	public function verifyOtp(Request $request)
	{
		$request->validate([
			'otp' => 'required'
		]);

		$replacement = ReplacementRequest::findOrFail($request->id);

          // ✅ Fetch latest OTP
        $otpRow = \DB::table('replacement_otps')
            ->where('replacement_request_id', $replacement->id)
            ->latest()
            ->first();

		if (!$otpRow || $otpRow->otp != $request->otp) {
        return response()->json([
                'message' => 'Invalid OTP'
            ], 422);
        }


		$replacement->status = 'closed';
		$replacement->save();

         //INCENTIVE RELEASE TO SERVICE CENTRE
        //  User who is closing the call
        $closingUser = Auth::guard('web')->user();
        
        // Only Admin (1) or Service Centre (4) can get incentive
        if(in_array($closingUser->role_id,[1,4]) && $closingUser->amc_incentive > 0){
            // 🔹 Item total price
          $productId =  $replacement->crp_data->product_id;
          $product   = Product::find($productId);
          $itemTotal = $product->mop ?? 0;
          // AMC Incentive calculation
            $incentiveAmount = round(
                ($itemTotal * $closingUser->amc_incentive) /100 ,2
            );

            $year = date('Y');

            // Get the last transaction ID for the year
            $lastTransaction = Ledger::whereYear('created_at', $year)
                ->latest('id')
                ->first();

            if ($lastTransaction) {
                // Extract the numeric part after the year
                $lastNumber = (int) substr($lastTransaction->transaction_id, strrpos($lastTransaction->transaction_id, $year) + 4);
                $newNumber = $lastNumber + 1;
            } else {
                $newNumber = 1;
            }

            $transactionId = 'REPLACE' . $year . str_pad($newNumber, 4, '0', STR_PAD_LEFT);

            if($incentiveAmount > 0){
                Ledger::create([
                    'type'  => 'credit',
                    'amount'=> $incentiveAmount,
                    'entry_date' => now()->toDateString(),
                    'user_type' => $closingUser->role_id == 1 ? 'admin' : 'service_centre',
                    'user_id'  => $closingUser->id,
                    'crp_id'   => $replacement->crp_id,
                    'purpose'    => 'Replacement Product',
                    'transaction_id' => $transactionId,
                ]);
            }
        }

		return response()->json([
			'message' => 'Call closed successfully'
		]);
	}




  

    public function add_call_request(Request $request)
    {
        // dd($request->all());
        if (!isset($request->product_name)) {
            return redirect()->back()->with(['serial' => 'Please select product details first!']);
        }
        // dd($request->all());

        $serial = $request->serial;
        $repeat_call = 0 ;
        $checkExistSerial = CustomerPointService::where('serial', $serial)->where('is_closed',1)->orderBy('id','DESC')->first();
        // dd($checkExistSerial);

        if(!empty($checkExistSerial)){
            $last_entry_date = $checkExistSerial->entry_date;
            $date1=date_create($last_entry_date);
            $date2=date_create(date('Y-m-d'));
            $diff=date_diff($date1,$date2);
            // $days = $diff->format("%d");
            $days = $diff->days;

            if($days <= 30){                
                // return redirect()->back()->withErrors(['serial' => 'You cannot add same item within 30 days'])->withInput();
                $repeat_call = 1;
            }
        }
        // dd($repeat_call);
        return view('customer-point-repair.add' , compact('repeat_call'));
    }

    public function store_call_request(Request $request)
    {

        // dd($request->all());
        $request->validate([
            'dealer_id' => 'required',
            'pincode' => 'required',
            'service_partner_id' => 'required',
            'customer_name' => 'required',
            'customer_phone' => 'required',
            'customer_alternate_phone' => 'required|digits:10',
            'address' => 'required',
            'bill_no' => 'required',
            'order_date' => 'required',
            'product_value' => 'nullable',
            'remarks' => 'required',
            'filename' => $request->dealer_type === 'nonkhosla' 
            ? 'required|file|mimes:png,jpg,jpeg|max:10000' 
            : 'nullable|file|mimes:png,jpg,jpeg|max:10000',

        ],[
            'dealer_id.required' => 'Please select dealer user',
            'address.required' => 'Address is required',
            'customer_alternate_phone.required' => 'Alternate number is required',
            'customer_alternate_phone.digits' => 'Alternate number must be exactly 10 digits',
            'remarks.required' => 'Please specify the issue',
            'filename.required' => 'Please upload the invoice in png,jpg,jpeg'
        ]);
       
        $params = $request->except('_token');

        $service_partner_id = !empty($params['service_partner_id'])?$params['service_partner_id']:null;
        $service_partner_email = !empty($params['service_partner_email'])?$params['service_partner_email']:null;
        $person_name = !empty($params['service_partner_person_name'])?$params['service_partner_person_name']:null;
        
        $dealer_id = !empty($params['dealer_id'])?$params['dealer_id']:null;
        $dealer_type = !empty($params['dealer_type'])?$params['dealer_type']:null;
        $pincode = !empty($params['pincode'])?$params['pincode']:null;
        $customer_name = !empty($params['customer_name'])?$params['customer_name']:null;
        $customer_alternate_phone = !empty($params['customer_alternate_phone'])?$params['customer_alternate_phone']:null;
        $customer_phone = !empty($params['customer_phone'])?$params['customer_phone']:null;
        $address = !empty($params['address'])?$params['address']:null;
        $bill_no = !empty($params['bill_no'])?$params['bill_no']:null;
        $order_date = !empty($params['order_date'])?$params['order_date']:null;
        $delivery_date = !empty($params['delivery_date'])?$params['delivery_date']:null;
        $product_value = !empty($params['product_value'])?$params['product_value']:null;
        $product_id = !empty($params['product_id'])?$params['product_id']:null;
        $product_name = !empty($params['product_name'])?$params['product_name']:null;
        $product_sl_no = !empty($params['product_sl_no'])?$params['product_sl_no']:null;
        $product_id = !empty($params['product_id'])?$params['product_id']:null;
        $remarks = !empty($params['remarks'])?$params['remarks']:null;
        $warranty_status = !empty($params['warranty_status'])?$params['warranty_status']:null;
        $warranty_period = !empty($params['warranty_period'])?$params['warranty_period']:null;
        $warranty_date = !empty($params['warranty_date'])?$params['warranty_date']:null;
        $is_repeated = isset($params['is_repeated'])?$params['is_repeated']:0;
       

        $snapshot_file = null;
        // $uplaod_base_url_prefix = config('app.uplaod_base_url_prefix');
        if(!empty($params['filename'])){
            $upload_path = public_path("uploads/CPR/");
            $image = $params['filename'];     
            $imageName = time() . "." . $image->getClientOriginalExtension();
            $image->move($upload_path, $imageName);
            $uploadedImage = $imageName;
            $snapshot_file = 'uploads/CPR/' . $uploadedImage;
        }

        $uniue_id = genAutoIncreNoYearWiseCallBook(3,'customer_point_services',date('Y'),date('m'),'CPR');
        $entry_date = date('Y-m-d');
        $created_at = date('Y-m-d H:i:s');

        $barcodeGeneratorWithNo = barcodeGeneratorWithNo($params['product_sl_no']);
        $code_html = $barcodeGeneratorWithNo['code_html'];
        $code_base64_img = $barcodeGeneratorWithNo['code_base64_img'];

        $repeat_call = 0;       //if the crp_call is not repeat in 30 days
        $repeat_crp_id = NULL;       //if the crp_call is not repeat in 30 days

        $checkExistSerial = CustomerPointService::where('serial', $params['product_sl_no'])->where('is_closed',1)->orderBy('id','DESC')->first();
        if(!empty($checkExistSerial)){
            $last_entry_date = $checkExistSerial->entry_date;
          
            $date1=date_create($last_entry_date);
            $date2=date_create(date('Y-m-d'));
            $diff=date_diff($date1,$date2);
            // $days = $diff->format("%d");
            $days = $diff->days;

            if($days <= 30){                
                // return redirect()->back()->with(['serial' => 'You cannot add same item within 30 days'])->withInput();
                $repeat_call = 1;
                $repeat_crp_id = $checkExistSerial->id;
            }
        }

        $entry_date = date('Y-m-d');
        $created_by = Auth::user()->name;
        $repairData = array(
            'unique_id' => $uniue_id,
            'entry_date' => $entry_date,
            'assign_service_perter_id' => $service_partner_id,
            'dealer_type' => $dealer_type,
            'dealer_id' => $dealer_id,
            'pincode' => $pincode,
            'address' => $address,
            'customer_name' => $customer_name,
            'mobile' => $customer_phone,
            'alternate_no' => $customer_alternate_phone,
            'bill_no' => $bill_no,
            'bill_date' => $order_date,
            'code_html' => $code_html,
            'code_base64_img' => $code_base64_img,
            'price' => $product_value,
            'serial' => $product_sl_no,
            'repeat_call' => $repeat_call,
            'repeat_crp_id' => $repeat_crp_id,
            'barcode' => $product_sl_no,
            'item' => $product_name,
            'product_id' => $product_id,
            'issue' => $remarks,
            'snapshot_file' => $snapshot_file,
            'created_at' => date('Y-m-d H:i:s')
        );
        try{
            CustomerPointService::insert($repairData);
            $get_data = CustomerPointService::where('unique_id', $uniue_id)->first();

            if($get_data){
                $servicePartner = $get_data->servicePartner?$get_data->servicePartner->person_name:"";
                $purpose = "Call has been successfully booked.(".$uniue_id.") & assigned to ".$servicePartner." service partner by admin ".ucwords($created_by)."";
                AddCustomerPointLog($get_data->id, $purpose);
            }
            // sms send to alternate no of user

            $call_id = $get_data->unique_id;
            $mobile = $get_data->alternate_no;
            $item = $get_data->item ?? "";
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

                // Insert SMS API response into the database
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

            Session::flash('message', 'Call Booked Successfully');
            return redirect()->route('customer-point-repair.list-booking');
        }catch (\Exception $e) {
            // Handle any errors during the database transaction
            return redirect()->back()->with('error', 'Error occurred while booking the call: ' . $e->getMessage())->withInput();
        }
    }

    public function show($idStr,$getQueryString='')
    {
        if(Auth::user()->id == 8){
            abort(404);
        } else {
            try {
                $id = Crypt::decrypt($idStr);
                $data = ServicePartner::findOrFail($id);
                return view('customer-point-repair.detail', compact('data','id','getQueryString'));
            } catch ( DecryptException $e) {
                return abort(404);
            }
        }
        
        
    }

    
   
    public function upload_pincode_csv($idStr,Request $request)
    {
        if(Auth::user()->id == 8){
            abort(404);
        } else {
            try {
                $id = Crypt::decrypt($idStr);
                $service_partner = ServicePartner::find($id);
                $service_partner_pincodes_general =CustomerPointServicePartnerPincode::where('service_partner_id',$id)->where('product_type', 'general')->orderBy('number')->get();
                $service_partner_pincodes_chimney =CustomerPointServicePartnerPincode::where('service_partner_id',$id)->where('product_type', 'chimney')->orderBy('number')->get();
                return view('customer-point-repair.csvpin', compact('id','service_partner','service_partner_pincodes_general','service_partner_pincodes_chimney'));
            } catch ( DecryptException $e) {
                return abort(404);
            }
        }
        
        
    }

    public function assign_pincode_csv(Request $request)
    {
        # csv for pincodes...
        $request->validate([
            'csv' => 'required'
        ]);
        $params = $request->except('_token');
        $csv = $params['csv'];
        $service_partner_id = $params['service_partner_id'];
        $product_type = $params['product_type'];

        // dd($params);
        
        $rows = Excel::toArray([],$request->file('csv'));
        $data = $rows[0];        
        // $columns = $rows[0][0];       
        // dd($columns);

        foreach($data as $item){
            ## Checking Existing ... ###
            $pincode = $item[0];
            $pincode_id = 0;
           
            $exist_pincode = Pincode::where('number',$pincode)->first();
            if($exist_pincode){
                $pincode_id = $exist_pincode->id;   
            }else{
                $pincode_id = Pincode::insertGetId(['number'=>$pincode,'is_csv_uploaded'=>1,'created_at' => date('Y-m-d H:i:s') ]);
            }
            # Check pincode for other SP assigned

            $check_pincode_others = CustomerPointServicePartnerPincode::where('pincode_id',$pincode_id)->where('product_type',$product_type)->where('service_partner_id', '!=', $service_partner_id)->first();

            if(!empty($check_pincode_others)){
                $another_sp_person_name = $check_pincode_others->service_partner->person_name;
                $another_sp_company_name = $check_pincode_others->service_partner->company_name;
                $another_sp_name = $another_sp_person_name.' - '.$another_sp_company_name;
                
                $errorMsg = $pincode.' already assigned to '.$another_sp_name.' for '.$product_type.' goods type';
                return redirect()->route('service-partner.upload-pincode-csv', Crypt::encrypt($service_partner_id))->withErrors(['csv'=> $errorMsg])->withInput($request->all()); 
            }
        }

        foreach($data as $item){
            # Entry In Table .... ##
            $pincode = trim($item[0]); // Remove leading and trailing whitespace
            $pincode = str_replace('�', '', $pincode); // Remove any special characters
            $pincode = substr($pincode, 0, 6); // Get only the first 6 digits
            // dd($pincode);
            $pincode_id = 0;
            $exist_pincode = Pincode::where('number',$pincode)->first();
            if($exist_pincode){
                $pincode_id = $exist_pincode->id;   
            }else{
                $pincode_id = Pincode::insertGetId(['number'=>$pincode,'is_csv_uploaded'=>1,'created_at' => date('Y-m-d H:i:s') ]);
            }
            $exist_service_partner_pincodes = CustomerPointServicePartnerPincode::where('service_partner_id',$service_partner_id)->where('pincode_id',$pincode_id)->where('product_type', $product_type)->first();
        //   dd($data);
            if(!$exist_service_partner_pincodes){
                CustomerPointServicePartnerPincode::insert([
                    'service_partner_id' => $service_partner_id,
                    'pincode_id' => $pincode_id,
                    'number' => $pincode,
                    'product_type' => $product_type,
                    'is_from_csv' => 1,
                    'created_at' => date('Y-m-d H:i:s') 
                ]);
            }
            // else{
            //     CustomerPointServicePartnerPincode::insert([
            //         'service_partner_id' => $service_partner_id,
            //         'pincode_id' => $pincode_id,
            //         'number' => $pincode,
            //         'product_type' => $product_type,
            //         'is_from_csv' => 1,
            //         'created_at' => date('Y-m-d H:i:s') 
            //     ]);
            // }
        }

        Session::flash('message', "Pin codes has been assigned to service partner successfully"); 
        return redirect()->route('customer-point-repair.upload-pincode-csv',Crypt::encrypt($service_partner_id));        
    }

    public function pincodelist($service_partner_idStr,Request $request)
    {
        # code...
        if(Auth::user()->id == 8){
            abort(404);
        } else {
            try {
                $search = !empty($request->search)?$request->search:'';
                $product_type = !empty($request->product_type)?$request->product_type:'';
                $paginate = 25;
                $service_partner_id = Crypt::decrypt($service_partner_idStr);
                $data = CustomerPointServicePartnerPincode::where('service_partner_id',$service_partner_id);
                $totalResult = CustomerPointServicePartnerPincode::where('service_partner_id',$service_partner_id);
    
                if(!empty($search)){
                    $data = $data->where('number', 'LIKE', '%'.$search.'%');
                    $totalResult = $totalResult->where('number', 'LIKE', '%'.$search.'%');
                }
                if(!empty($product_type)){
                    $data = $data->where('product_type',$product_type);
                    $totalResult = $totalResult->where('product_type',$product_type);
                }
    
                $data = $data->orderBy('number','asc')->paginate($paginate);
                $totalResult = $totalResult->count();
    
                $data = $data->appends([
                    'page' => $request->page,
                    'search' => $search,
                    'product_type' => $product_type
                ]);
                $service_partner = ServicePartner::find($service_partner_id);
                return view('customer-point-repair.pincodelist', compact('data','totalResult','service_partner_id','service_partner','search','paginate','product_type'));
            } catch ( DecryptException $e) {
                return abort(404);
            } 
        }
               
    }


    public function removepincdoebulk($service_partner_id,Request $request)
    {
        # remove pin codes...

        // dd($request->all());
        // dd($service_partner_id);

        $ids = !empty($request->ids)?$request->ids:array();
        $pincodeArr = array();
        if(!empty($ids)){
            foreach($ids as $id){
                $pincode = CustomerPointServicePartnerPincode::find($id);
                $pincodeArr[] = $pincode->number;
                CustomerPointServicePartnerPincode::where('id',$id)->delete();
            }
        }

        ### Changelog ###
        $browser_name = isset($request->browser_name)?$request->browser_name:NULL;
        $navigator_useragent = isset($request->navigator_useragent)?$request->navigator_useragent:NULL;
        
        

        $sp = ServicePartner::find($service_partner_id);
        $params['service_partner_person_name'] = $sp->person_name;
        $params['service_partner_company_name'] = $sp->company_name;
        $params['pincodeArr'] = $pincodeArr;

        addChangeLog(Auth::user()->id,$request->ip(),'service_partner_remove_pincode',$browser_name,$navigator_useragent,$params);
        

        Session::flash('message', "PIN Codes removed successfully");
        return redirect()->route('customer-point-repair.pincodelist',Crypt::encrypt($service_partner_id));

    }

    
    public function call_logs($service_partner_idStr,$type,Request $request)
    {
        # call logs...
        try {
            $id = Crypt::decrypt($service_partner_idStr);
            $company_name = getSingleAttributeTable('service_partners','id',$id,'company_name');
            $person_name = getSingleAttributeTable('service_partners','id',$id,'person_name');
            $from_date = !empty($request->from_date)?$request->from_date:date('Y-m-01', strtotime(date('Y-m-d')));
            $to_date = !empty($request->to_date)?$request->to_date:date('Y-m-d');
            // echo "Call Logs :- ".$id;
            $totalResult = 0;
            if($type == 'installation'){
                $data = Installation::where('service_partner_id',$id)->where('is_closed', 1)->whereBetween(DB::raw('DATE(created_at)'), [$from_date,$to_date]);
                $totalResult = Installation::where('service_partner_id',$id)->where('is_closed', 1)->whereBetween(DB::raw('DATE(created_at)'), [$from_date,$to_date]);
            } else if ($type == 'repair'){
                $data = Repair::where('service_partner_id',$id)->where('is_closed', 1)->whereBetween(DB::raw('DATE(created_at)'), [$from_date,$to_date]);
                $totalResult = Repair::where('service_partner_id',$id)->where('is_closed', 1)->whereBetween(DB::raw('DATE(created_at)'), [$from_date,$to_date]);
            }
            

            $data = $data->paginate(20);
            $totalResult = $totalResult->count();
            return view('servicepartner.call-logs', compact('id','service_partner_idStr','data','type','totalResult','from_date','to_date','company_name','person_name'));
        } catch ( DecryptException $e) {
            return abort(404);
        }
        
    }

    public function checkProductDetails(Request $request){
        $contact_type = $request->input('contact_type', 'mobile');
        $mobile = $request->input('mobile', '');
        $phone = $request->input('phone', '');
        $bill_no = $request->input('bill_no', '');
        $serial = $request->input('serial', '');
        $non_khosla_serial = $request->input('non_khosla_serial', '');
        $type = $request->input('type', 'khosla');
        $data = null;
        $khosla_data = [];
        $installation = null;
        // Check if the serial number is provided
        if($type == 'non-khosla'){
            if (!empty($non_khosla_serial)) {
                // Use Eloquent's query builder to filter the records
                $data = PurchaseOrderBarcode::with('product','productWarranty', 'goodsWarranty')->where('barcode_no', $non_khosla_serial)->first();
                $installation = Installation::where('product_sl_no',$non_khosla_serial)->first();
               
            }
        }else if($type == 'khosla'){
            if (!empty($mobile) || !empty($phone) || !empty($bill_no) || !empty($serial)) {
                $khosla_data = KgaSalesData::with('product')->whereNotNull('product_id');

                $khosla_data->when(!empty($mobile) && $contact_type == 'mobile', function ($query) use ($mobile) {
                    return $query->where('mobile', $mobile);
                });
                
                $khosla_data->when(!empty($phone) && $contact_type == 'phone', function ($query) use ($phone) {
                    return $query->where('phone', $phone);
                });
                
                $khosla_data->when(!empty($bill_no), function ($query) use ($bill_no) {
                    return $query->where('bill_no', $bill_no);
                });
                
                $khosla_data->when(!empty($serial), function ($query) use ($serial) {
                    return $query->where('serial', $serial);
                });
                
                $khosla_data = $khosla_data->get();
            }
            
        }

        return view('customer-point-repair.checkitemstatus', compact('data','non_khosla_serial','khosla_data','contact_type', 'mobile', 'phone', 'bill_no','serial','type','installation'));
    }
    public function barcode($idStr)
    {
        $id = Crypt::decrypt($idStr);
        $data = CustomerPointService::find($id);
        return view('customer-point-repair.crp-barcode', compact('data'));
    }
    public function add_spare($idStr)
    {
        $id = Crypt::decrypt($idStr);
        $data = CustomerPointService::find($id);
        $final_spare_data = CRPFinalSpare::where('crp_id',$id)->get();
        $spare_data =CustomerPointServiceSpare::where('crp_id',$id)->orderBy('created_at','desc')->get();
        return view('customer-point-repair.add-spare', compact('id','data','spare_data','final_spare_data'));
    }
    public function save_spare(Request $request)
    {
        // dd($request->all());
        // CustomerPointServiceSpare::where('crp_id',$request->crp_id)->delete();
        $filteredArray = array_filter($request->product_id, function($value) {
            return !is_null($value);
        });
        if(count($filteredArray)>0){
            foreach ($filteredArray as $index => $productId) {
                $product = Product::find($productId);
                $product_name = $product->title;
                // $mop = $product->mop?$product->mop:0;
                $profit_percentage = $product->profit_percentage?$product->profit_percentage:0;
                $last_po_cost_price = $product->last_po_cost_price?$product->last_po_cost_price:0;
                $mop = $last_po_cost_price *(1+($profit_percentage/100));
                
                $purchase_order_ids = PurchaseOrderProduct::where('product_id',$productId)->pluck('purchase_order_id')->toArray();
                if (!empty($purchase_order_ids)) {
                    // Get the latest purchase order ID based on the condition
                    $latest_purchase_order_id = PurchaseOrder::whereIn('id', $purchase_order_ids)
                    ->where('is_goods_in', 1)
                    ->orderBy('created_at', 'desc') // Adjust if needed, e.g., 'updated_at'
                    ->pluck('id')
                    ->first(); // Get the latest (first) ID
                    if($latest_purchase_order_id){
                        
                        $last_po_cost_price = PurchaseOrderProduct::where('purchase_order_id',$latest_purchase_order_id)->pluck('cost_price')->first();
                        // dd($last_po_cost_price);
                        
                        // Get the quantity for the current product from the request, default to 1 if not provided
                        $quantity = isset($request->product_qty[$index]) && $request->product_qty[$index] > 0 
                        ? $request->product_qty[$index] 
                        : 1;
                        
                        // Calculate final amount
                        $final_amount = $quantity * $mop;
                         // Calculate profit percentage
                         $profit_percentage = 0; // Default to 0 if last_po_cost_price is 0 to avoid division by zero
                         if ($last_po_cost_price > 0) {
                             $profit_percentage = (($mop - $last_po_cost_price) / $last_po_cost_price) * 100;
                             $profit_percentage = round($profit_percentage, 2); // Round to 2 decimal places
                         }
    
                        
                        $data = CustomerPointServiceSpare::where('crp_id', $request->crp_id)->where('sp_id', $productId)->first();
                        if(!$data){
                            $data = new CustomerPointServiceSpare();
                            $data->quantity = $quantity;

                        }else{
                            $data->quantity = $data->quantity+$quantity; 
                        }
                        $data->crp_id = $request->crp_id;  // Assuming `crp_id` is needed for every record
                        $data->generate_by = $request->generate_by; // If applicable, set other fields accordingly
                        $data->sp_id = $productId;  // Set the current product ID
                        $data->sp_name = $product_name;  
                        $data->mop = $mop;  
                        $data->last_po_cost_price = $last_po_cost_price;  
                        $data->profit_percentage = $profit_percentage;  
                        $data->final_amount = $data->quantity * $mop;  
                        $data->save();
                        
                    }else{
                        return redirect()->back()->with('error', 'this ('.$product_name.') is out of stock.');
                    }
                }else{

                    return redirect()->back()->with('error', 'This product has no purchase order record.');
                }
            }
            return redirect()->back()->with('success', 'Spares added successfully.');
        }else{
            return redirect()->back()->with('error', 'Please select a product first.');
        }
        
        
        
    }
    public function delete_spare(Request $request)
    {
        $data = CustomerPointServiceSpare::find($request->id);
        $data->delete();
        // Return a JSON response indicating success
        return response()->json([
            'status' => 'success',
            'message' => 'Spare deleted successfully.'
        ]);
    }
    public function admin_approval(Request $request)
    {
        // Begin a transaction
        DB::beginTransaction();
        try {
            $data = CustomerPointService::find($request->id);
            if($request->approval == "2"){
                $data->admin_approval = 2;
                $data->status = 8;
                $data->approved_by = Auth::user()->id;
                $data->save();
                DB::commit();
                return redirect()->back()->with('warning', 'Admin approval Rejected successfully.');
            }else{
                $data->admin_approval = 1;
                $data->status = 8;
                $data->approved_by = Auth::user()->id;
                $service_charge = ServicePartnerCharge::select('repair')->where('service_partner_id',$data->assign_service_perter_id)->where('product_id',$data->product_id)->first();

                if (!$service_charge) {
                    DB::rollBack(); // Rollback the transaction on error
                    return redirect()->back()->with('warning', 'Service charge not found for this product.');
                }
                    // if repeat_call === 1 and $data->repeat_crp_id found
                    if($data->repeat_call === 1 && $data->repeat_crp_id != NULL){
                        //debit ledger
                        $pre_crp_data = CustomerPointService::find($data->repeat_crp_id);
                        $pre_service_partner = $pre_crp_data->assign_service_perter_id;
                        // $pre_service_partner_service_charge = Ledger::where('service_partner_id',$pre_service_partner)->where('crp_id',$data->repeat_crp_id)->where('purpose','Customer Point Repair')->where('type','credit')->pluck('amount');
                        $pre_service_partner_service_charge = Ledger::where('service_partner_id',$pre_service_partner)->where('crp_id',$data->repeat_crp_id)->where('transaction_id',$pre_crp_data->unique_id)->where('type','credit')->pluck('amount');


                        $ledgerData = [
                            'type' => 'debit',
                            'service_partner_id' => $pre_service_partner,
                            'amount' => $pre_service_partner_service_charge,
                            'entry_date' => date('Y-m-d'),
                            'user_type' => 'servicepartner',
                            'purpose' => 'Customer Point Repair(repeat call)',
                            'transaction_id' => $pre_crp_data->unique_id,
                            'crp_id' => $data->repeat_crp_id,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s')
                        ];
                        Ledger::insert($ledgerData);
                    }

                        // Ledger Entry
                        $ledgerData = [
                            'type' => 'credit',
                            'service_partner_id' => $data->assign_service_perter_id,
                            'amount' => $service_charge->repair,
                            'entry_date' => date('Y-m-d'),
                            'user_type' => 'servicepartner',
                            'purpose' => 'Customer Point Repair',
                            'transaction_id' => $data->unique_id,
                            'crp_id' => $data->id,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s')
                        ];
        
                        $existLedger = Ledger::where('crp_id', $data->id)->where('type','credit')->first();
                        if (empty($existLedger)) {
                            Ledger::insert($ledgerData);
                        }
                    }

                $data->save();
                 // Commit the transaction
                DB::commit();
                return redirect()->back()->with('success', 'Admin approval updated successfully.');
            } catch (\Exception $e) {
                // Rollback the transaction on error
                DB::rollBack();
                
                return response()->json(['status' => false, 'message' => 'Failed to update admin approval', 'error' => $e->getMessage()], 500);
            }
            
            
        }
        
        public function cancell(Request $request){
            // dd($request->all());
            $request->validate([
                'id' => 'required|exists:customer_point_services,id',
                'cancelled_reason' => 'required|string'
            ]);
            $data = CustomerPointService::find($request->id);
            $data->status = 9;
            $data->cancelled_reason = $request->cancelled_reason;
            $data->cancelled_by = 1;
            $data->save();
            if($data){   
                return redirect()->back()->with('success', 'Request cancell successfully.');
            }else{
                return redirect()->back()->with('error', 'Something wrong happen, please try again.');
            }
        }
        public function reassign_engineer(Request $request){
            $update =  CustomerPointService::with('servicePartner')->find($request->id);
            $previous_engg_name = $update->servicePartner?$update->servicePartner->person_name:"";
            $update->assign_service_perter_id =$request->service_partner;
            $update->save();
            // Reload the model with the updated service partner relationship
            $update->load('servicePartner');
            if($update){
                $Auth_name = Auth::user()->name;
                $new_engg_name = $update->servicePartner?$update->servicePartner->person_name:"";
                $purpose = "Service partner has been changed (".$previous_engg_name.") & assigned to  new service partner (".$new_engg_name.") by (".$Auth_name.").";
                AddCustomerPointLog($update->id, $purpose);

                Session::flash('message', 'Engineer re-assign Successfully');
                return response()->json(['status'=>200]);
            }else{
                Session::flash('message', 'Something went worng.');
                return response()->json(['status'=>400]);
            }
        }
 

        public function download_customer_invoice($idStr)
        {
            $id = Crypt::decrypt($idStr);
            $crp_data = CustomerPointService::with('servicePartner','paymentData')->find($id);
            // dd($crp_data);
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
        }
        public function send_user_invoice_link($idStr)
        {
            try {
                // Decrypt the ID
                $id = Crypt::decrypt($idStr);
        
                // Retrieve the customer record
                $crp = CustomerPointService::find($id);
        
                if (!$crp) {
                    return redirect()->back()->with('error', 'Customer record not found.');
                }     
                // Extract the required information
                $product_name = $crp->item;
                if (strlen($product_name) > 30) {
                    $product_name = substr($product_name, 0, 28) . '..';
                }
                $mobile = $crp->alternate_no;
                $call_id = $crp->unique_id;
                // $download_url = "kgaerp.in/test-retailer/cin?d=".$crp->id;
                $download_url = route('c_invoice',['d'=>$crp->id]);

                $query_calling_number = "6291117317";
        
                // Get SMS details from settings
                $sms_entity_id = getSingleAttributeTable('settings', 'id', 1, 'sms_entity_id');
                $sms_template_id = "1707172846606238636";
        
                // Prepare the message
                // $myMessage = urlencode('Your product ' . $product_name . ' Call ID ' . $call_id . ' has been repaired. '
                //     . 'Please collect from ' . $product_name . '. Click to download bill ' . $download_url . '. '
                //     . 'For assistance call ' . $query_calling_number . '.AMMRTL');
                                     // Your product {#var#} Call ID {#var#} has been repaired. Click to download bill {#var#}. For assistance call 6291117317.AMMRTL
                $myMessage = urlencode('Your product ' . $product_name . ' Call ID ' . $call_id . ' has been repaired. Click to download bill '.$download_url.'. For assistance call 6291117317.AMMRTL');


        
                // Construct the SMS API URL
                $sms_url = 'https://sms.bluwaves.in/sendsms/bulk.php?username=ammrllp&password=123456789&type=TEXT'
                    . '&sender=AMMRTL&mobile=' . $mobile
                    . '&message=' . $myMessage
                    . '&entityId=' . $sms_entity_id
                    . '&templateId=' . $sms_template_id;
        
                // Initialize cURL
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => $sms_url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_TIMEOUT => 10, // Timeout after 10 seconds
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                ));
        
                // Execute cURL and capture response
                $response = curl_exec($curl);
                // dd($response);
        
                // Check if the cURL request was successful
                if (curl_errno($curl)) {
                    $error_msg = curl_error($curl);
                    curl_close($curl);
                    return redirect()->back()->with('error', 'Failed to send SMS: ' . $error_msg);
                }
        
                curl_close($curl);
        
                // Log the SMS API response
                DB::table('sms_api_response')->insert([
                    'sms_template_id' => $sms_template_id,
                    'sms_entity_id' => $sms_entity_id,
                    'phone' => $mobile,
                    'message_body' => $myMessage,
                    'response_body' => $response,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
        
                return redirect()->back()->with('success', 'Invoice link has been successfully sent.');
        
            } catch (Exception $e) {
                // Handle exceptions like decryption errors or DB errors
                return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
            }
        }

        public function DeadBarcodes($idStr,$getQueryString='')
        {
            # barcodes...
            try{
                $id = Crypt::decrypt($idStr);
                $crp_data = CustomerPointService::find($id);         
                $data = CRPFinalSpare::with('ProductData')->where('crp_id',$id)->get();
                $totalData = count($data);
                return view('customer-point-repair.return-spare-barcodes', compact('idStr','getQueryString','data','crp_data','totalData','id'));
            } catch (DecryptException $e){
                return abort(404);
            }
        }
            
    }
    

