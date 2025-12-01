<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Response;
use App\Models\ServiceCentre;
use App\Models\ServicePartner;
use App\Models\ProductAmc;
use App\Models\Product;
use App\Models\AmcPlanType;
use App\Models\AmcDuration;
use App\Models\KgaSalesData;
use App\Models\AmcSubscription;
use App\Models\AmcCallHistory;
use App\Models\User;
use App\Models\BeforeAmcSubscription;
use App\Models\ProductWarranty;
use App\Models\PlanAsset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;





class AmcController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }


 /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
	
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
        
        
        
        return view('amc.add',compact('contact_no' ,'bill_no', 'serial','kga_sales_data'));
        
    }
	
	public function plan_assets(Request $request){
		$query = PlanAsset::query();
		if($request->has('search') && !empty('search')){
			$search = $request->search;
			$query->where('name','like','%'.$search.'%');
		}
		$plan_asset = $query->orderBy('name','asc')->get();
		return view('amc.plan-assets',compact('plan_asset'));
	}
	
	public function plan_assets_create(Request $request){
		$request->validate([
		     'name' => 'required|string'
		]);
		
		PlanAsset::create([
		   'name' => $request->name
		]);
		
		return redirect()->back()->with('message','Plan Asset Created Successfully');
	}
	
	public function plan_assets_edit($id){
		$plan_asset_data = PlanAsset::findOrFail($id);
		$plan_asset = PlanAsset::orderBy('name','asc')->get();
		return view('amc.plan-assets',compact('plan_asset_data','plan_asset'));
	}
	
	public function plan_assets_update(Request $request,$id){
		$request->validate([
		   'name' => 'required|string'
		]);
		$plan_asset = PlanAsset::findOrFail($id);
		$plan_asset->update([
		   'name' => $request->name
		]);
		
		return redirect()->route('amc.plan-assets')->with('message','Plan Asset Updated Successfully');
	}
	
	public function plan_assets_delete($id){
		$plan_asset = PlanAsset::findOrFail($id);
		$plan_asset->delete();
		
		return redirect()->back()->with('message','Plan Asset Deleted Successfully');
	}
	
	
    public function plan_type(Request $request)
    {   
        $data = AmcPlanType::with('AmcDurationData')->where('deleted_at',1)->get();
		$plan_asset = PlanAsset::orderBy('name','asc')->get();
        return view('amc.plan-type',compact('data','plan_asset'));
    }
    public function plan_duration(Request $request,$idStr)
    {   
        $id = Crypt::decrypt($idStr);
        $data =AmcPlanType::with('AmcDurationData')->find($id);
        $duration_data = AmcDuration::where('amc_id',$id)->orderBy('duration','ASC')->get();
        return view('amc.plan-duration' ,compact('data','duration_data'));
    }
    public function plan_duration_create(Request $request)
    {
		//dd($request->all());
        try {
            $request->validate([
                'plan_name' => 'required|exists:amc_plan_type,id',
                'plan_dureation' => 'required|integer|min:1',
            ]);
    
            
    
            $amc_plan_name = AmcPlanType::select('name')->find($request->plan_name);
    		// Check only if both cleaning types are present
			if ($request->filled('deep_cleaning_days') && $request->filled('normal_cleaning_days')) {
				$exists = AmcDuration::where('amc_id', $request->plan_name)
					->where('duration', $request->plan_dureation)
					->whereNotNull('deep_cleaning')
					->whereNotNull('normal_cleaning')
					->exists();

				if ($exists) {
					Session::flash(
						'error',
						"{$request->plan_dureation} days with both Deep ({$request->deep_cleaning_days} days) & Normal ({$request->normal_cleaning_days} days) cleaning for {$amc_plan_name->name} already exists."
					);
					return redirect()->back();
				}
			}
            
                $amc_data = new AmcDuration();
                $amc_data->amc_id = $request->plan_name;
                $amc_data->duration = $request->plan_dureation;
				$amc_data->deep_cleaning = $request->has('deep_cleaning_checkbox') ? $request->deep_cleaning_days : null;
				$amc_data->normal_cleaning = $request->has('normal_cleaning_checkbox') ? $request->normal_cleaning_days : null;
                $amc_data->save();
    
               
			Session::flash(
				'message',
				"{$request->plan_dureation} days with the {$amc_plan_name->name} package saved successfully!"
			);
           
        } catch (\Exception $e) {
            Session::flash('error', 'An error occurred: ' . $e->getMessage());
        }
    
        return redirect()->back();
    }
      
    public function plan_name_create(Request $request)
    {    
        $data = new AmcPlanType();
        $data->name = $request->plan_name;
		$data->plan_asset_id = is_array($request->plan_asset)
								? implode(',', $request->plan_asset)
								: $request->plan_asset;
        $data->save();
        if($data){
                $successMsg = "Plan created successfully";
                Session::flash('message', $successMsg);
        }else{
            Session::flash('message', 'Something went wrong.');
        }
        return redirect()->route('amc.plan-type');
    }
    public function plan_name_edit(Request $request)
    {   
        // dd($request->all());
        
        $data = AmcPlanType::find($request->id);
        $data->name = $request->plan_name;
		$data->plan_asset_id = is_array($request->plan_asset)
								? implode(',', $request->plan_asset)
								: $request->plan_asset;
        $data->save();
        if($data){
                $successMsg = "Plan updated successfully";
                Session::flash('message', $successMsg);
        }else{
            Session::flash('message', 'Something went wrong.');
        }
        return redirect()->route('amc.plan-type');
    }
    public function plan_name_delete(Request $request)
    {   
        // dd($request->all());
        $data = AmcPlanType::find($request->id);
        $data->deleted_at = 0;
        $data->save();
        if($data){
            $successMsg = "Plan deleted successfully";
            Session::flash('message', $successMsg);
        }else{
            Session::flash('message', 'Something went wrong.');
        }
        return redirect()->route('amc.plan-type');
    }
    public function plan_master(Request $request, $idStr)
    {   
        $id = Crypt::decrypt($idStr);
        // dd($id);
        $data = ProductAmc::with('productData','AmcPlanData','AmcDurationData')->where('duration_id',$id)->get();
        $amc_duration_data = AmcDuration::find($id);
        // dd($amc_duration_data);
        $amc_plan_data = AmcPlanType::with('AmcDurationData')->find($amc_duration_data->amc_id);
        $product_count = count($data);

        return view('amc.plan-master', compact('data', 'product_count','amc_plan_data','amc_duration_data','id'));
    }
    public function incentive_master(Request $request)
    {
        $service_partner_intensive = ServicePartner::whereNull('service_partner_head')->where('status',1)->value('amc_incentive');
        $service_head_intensive = ServicePartner::where('service_partner_head', 99)->where('status',1)->value('amc_incentive');
        $head_office_intensive = User::where('id',1)->where('type','admin')->where('status',1)->value('amc_incentive');
        $service_centre_incentive = User::where('id',8)->where('type','manager')->where('status',1)->value('amc_incentive');
		$new_head_office_incentive = User::where('role_id',6)->where('type','manager')->where('status',1)->value('amc_incentive');
        return view('amc.incentive-master',            compact('service_partner_intensive','service_head_intensive','head_office_intensive','new_head_office_incentive','service_centre_incentive'));
    }
    public function incentive_update(Request $request)
    {
        // dd($request->all());
        // Validate the input
        

        $request->validate([
            'intensive_for' => 'required|string',
            'service_partner_intensive' => 'required_if:intensive_for,service_partner|numeric|min:0', // Validate for service_prtner
            'service_head_intensive' => 'required_if:intensive_for,service_head|numeric|min:0', // Validate for service_head
            'head_office_intensive' => 'required_if:intensive_for,head_office|numeric|min:0', // Validate for service_head
        ]);
        $browser_name = $request->input('browser_name', null);
        $navigator_useragent = $request->input('navigator_useragent', null);
        
        // Handle the service_partner case
        if ($request->intensive_for === 'service_partner') {
            // Update 'amc_incentive' for all service partners with type = 3
            $updated = ServicePartner::whereNull('service_partner_head')->where('status',1)
                ->update(['amc_incentive' => $request->service_partner_intensive]);
    
            if ($updated) {
                // Success message
                addChangeLog(Auth::user()->id,$request->ip(),'AMC INCENTIVE % CHANGE service partner',$browser_name,$navigator_useragent,$request->all());
                $successMsg = "Service partner Incentives for AMC set successfully.";
                Session::flash('message', $successMsg);
            } else {
                // Error message if no records were updated
                Session::flash('message', 'No service partners found with the specified type.');
            }
    
            return redirect()->route('amc.incentive-master');
        }

        // Handle the service_head case
        if ($request->intensive_for === 'service_head') {
            // Update 'amc_incentive' for all service heads with the specified ID
            $updated = ServicePartner::where('service_partner_head', 99)->where('status',1) // Ensure this is the correct condition
                ->update(['amc_incentive' => $request->service_head_intensive]);
    
            if ($updated) {
                // Success message
                addChangeLog(Auth::user()->id,$request->ip(),'AMC INCENTIVE % CHANGE service head',$browser_name,$navigator_useragent,$request->all());
                $successMsg = "Service partner head Incentives for AMC set successfully.";
                Session::flash('message', $successMsg);
            } else {
                // Error message if no records were updated
                Session::flash('message', 'No service head found with the specified type.');
            }
    
            return redirect()->route('amc.incentive-master');
        }
        // Handle the Head_office case
        if ($request->intensive_for === 'head_office') {
            // dd($request->all());
            // Update 'amc_incentive' for all service heads with the specified ID
            $updated = User::where('id',1)->where('type','admin')->where('status',1) // Ensure this is the correct condition
                ->update(['amc_incentive' => $request->head_office_intensive]);
    
            if ($updated) {
            // Success message
            addChangeLog(Auth::user()->id,$request->ip(),'AMC INCENTIVE % CHANGE head office',$browser_name,$navigator_useragent,$request->all());
                $successMsg = "Head office Incentives for AMC set successfully.";
                Session::flash('message', $successMsg);
            } else {
                // Error message if no records were updated
                Session::flash('message', 'No service head found with the specified type.');
            }
    
            return redirect()->route('amc.incentive-master');
        }
    
        // Return with error if the condition is not met
        return redirect()->back()->withErrors('Invalid request.');
    }

    public function save_product_amc(Request $request){
        // dd($request->all());
        $browser_name = $request->input('browser_name', null);
        $navigator_useragent = $request->input('navigator_useragent', null);
        $data = new ProductAmc();
        $data->product_id = $request->product_id;
        $data->title = $request->product_name;
        $data->plan_id = $request->plan_id;
        $data->duration_id = $request->duration_id;
        $data->duration = $request->duration;
        $data->amount = $request->amc_amount;
        $data->save();
        if($data){
            addChangeLog(Auth::user()->id,$request->ip(),'Product Add in AMC Plan With Amount',$browser_name,$navigator_useragent,$data);
            $successMsg = "Data saved successfully";
            Session::flash('message', $successMsg);
            // Pass duration and plan_id to the next request
        }else{
            Session::flash('error', 'Something went wrong , Try again!');
        }
        
        return redirect()->route('amc.plan-master', Crypt::encrypt($request->duration_id));
        
        
        
    }
    public function search(Request $request){
        $logedInUser = Auth::user();
        $search = !empty($request->search)?$request->search:'';
        $plan_type = !empty($request->plan_type)?$request->plan_type:'';
        $duration_type = !empty($request->duration_type)?$request->duration_type:'';
        $paginate = !empty($request->paginate)?$request->paginate:25;
        $page = $request->page;
        if(!is_numeric($page)){
            $page = 1;
        }
        if(!is_numeric($paginate)){
            $paginate = 25;
        }
        
        $amc_plan = AmcPlanType::get();
        $amc_duration = AmcDuration::groupBy('duration')->orderBy('duration','ASC')->get();
        
        $data = ProductAmc::select('*');
        $totalResult = ProductAmc::select('id');
        
        if(!empty($search)){
            $data = $data->where(function($query) use ($search){
                $query->where('title', 'LIKE','%'.$search.'%');
            });
            $totalResult = $totalResult->where(function($query) use ($search){
                $query->where('title', 'LIKE','%'.$search.'%');
            });
        }
        if(!empty($plan_type)){
            $data = $data->where('plan_id', $plan_type);
            $totalResult = $totalResult->where('plan_id', $plan_type);
        }
        if(!empty($duration_type)){
            $data = $data->where('duration', $duration_type);
            $totalResult = $totalResult->where('duration', $duration_type);
        }
        
        
        $data = $data->with('AmcPlanData')->orderBy('title','ASC')->orderBy('plan_id','ASC')->orderBy('duration','ASC')->paginate($paginate);
        $totalResult = $totalResult->count();
        
        // $data = ProductAmc::with('AmcPlanData')->orderBy('title','ASC')->orderBy('duration','ASC')->orderBy('amount','ASC')->paginate($paginate);
        // $totalResult = count($data);
        $data = $data->appends([
            'page'=>$page,
            'paginate'=>$request->paginate,
            'search'=>$search,
            'plan_type'=>$plan_type,
            'duration_type'=>$duration_type,
            
        ]);
        
        return view('amc.search', compact('amc_plan','amc_duration','data','totalResult','page','paginate','search','plan_type','duration_type','logedInUser'));
        
    }
    public function update_amc_product_amount(Request $request){
        
        // dd($request->all());
        $browser_name = $request->input('browser_name', null);
        $navigator_useragent = $request->input('navigator_useragent', null);
        $id = $request->id;
        $data = ProductAmc::find($id);
        $data->amount = $request->amount;
        $data->save();


        if ($data) {
            // Success message
            addChangeLog(Auth::user()->id,$request->ip(),'AMC Product Amount Update',$browser_name,$navigator_useragent,$data);
            $successMsg = "Product amount updated successfully!";
            Session::flash('message', $successMsg);
        } else {
            // Error message if no records were updated
            Session::flash('message', 'Something went wrong!.');
        }

        return redirect()->back();

    }



    ///HO SALE

    public function ho_sale(Request $request){
        $searchBy = $request->input('search_by', 'default');
        $search = !empty($request->search)?$request->search:'';
        $remaining_days = $request->input('remaining_days', 'hot_leads');
        $paginate = !empty($request->paginate)?$request->paginate:25;
        $amc_subscription = !empty($request->amc_subscription)?$request->amc_subscription:'unsubscription';
        $calls_filter = !empty($request->calls_filter) ? $request->calls_filter : "";
        $page = $request->page;
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        
        $today=date('Y-m-d');

        if($amc_subscription==='subscription'){
            return redirect()->route('amc.subscription-amc-data');
        }elseif($amc_subscription==='pending_request'){
            return redirect()->route('amc.pending-request');
        }elseif($amc_subscription === 'pending_payment'){
            return redirect()->route('amc.pending-payment');
        }

        if(!is_numeric($page)){
            $page = 1;
        }
        if(!is_numeric($paginate)){
            $paginate = 25;
        }


        // Fetch all serials from the AmcSubscription table
        $AmcSubscriptionSerials = AmcSubscription::groupBy('serial')->whereNotNull('serial')->pluck('serial')->toArray();

        if($amc_subscription === 'refused') {
            // Get only refused call history entries
            $data = KgaSalesData::whereHas('CallHistoryData', function($query) {
                $query->where('type', 2);
            })->select('*');
            $totalResult = KgaSalesData::whereHas('CallHistoryData', function($query) {
                $query->where('type', 2);
            })->select('*');
        }
        elseif($amc_subscription == 'unsubscription'){
            $data = KgaSalesData::whereNotIn('serial',$AmcSubscriptionSerials)->select('*');
            $totalResult =KgaSalesData::whereNotIn('serial',$AmcSubscriptionSerials)->select('*');
        }else{
            $data = KgaSalesData::with('AmcSubscription')->whereIn('serial',$AmcSubscriptionSerials)->select('*');
            $totalResult =KgaSalesData::whereNotIn('serial',$AmcSubscriptionSerials)->select('*');
        }


        // Filter by category where amc_applicable = 1
        $data = $data->whereHas('product', function ($q) {
            $q->whereHas('category', function ($query) {
                $query->where('amc_applicable', 1);
            });
        });

        $totalResult = $totalResult->whereHas('product', function ($q) {
            $q->whereHas('category', function ($query) {
                $query->where('amc_applicable', 1);
            });
        });
        //call filter
        if ($calls_filter === 'today_due') {
            $data = $data->where(function($query) use ($today) {
                $query->whereHas('productWarranty', function ($q) use ($today) {
                    $q->whereRaw("DATE_ADD(bill_date, INTERVAL warranty_period MONTH) = ?", [$today]);
                })
                ->orWhereHas('CallHistoryData', function($q) use ($today) {
                    $q->whereDate('reminder_date', $today);
                });
            });
        }elseif ($calls_filter === 'old_pending') {
            $data = $data->where(function($query) use ($today) {
                $query->whereHas('productWarranty', function ($q) use ($today) {
                $q->whereRaw("DATE_ADD(bill_date, INTERVAL warranty_period MONTH) < ? ", [$today]);
            })->orWhereHas('CallHistoryData', function($q) use ($today) {
                        $q->whereDate('reminder_date', '<', $today);
                    });
            });
		}elseif($calls_filter === 'call_back') {
		    $data = $data->whereHas('CallHistoryData' , function($query){
			   $query->where('type',1);
			});
		}  
        
		if (!empty($from_date) || !empty($to_date)) {
            $data->where(function ($query) use ($from_date, $to_date) {
                if (!empty($from_date)) {
                    $query->where('bill_date', '>=', $from_date);
                }
                if (!empty($to_date)) {
                    $query->where('bill_date', '<=', $to_date);
                }
            });
        
            $totalResult->where(function ($query) use ($from_date, $to_date) {
                if (!empty($from_date)) {
                    $query->where('bill_date', '>=', $from_date);
                }
                if (!empty($to_date)) {
                    $query->where('bill_date', '<=', $to_date);
                }
            });
        }
		
		
		if ($remaining_days) {
		    if ($remaining_days === 'hot_leads') {
                // Show warranties expiring ±10 days from today
                
                $data = $data->whereHas('productWarranty', function ($query) use ($today) {
				    $query->whereRaw(" DATE_ADD(bill_date, INTERVAL warranty_period MONTH) BETWEEN DATE_SUB(?, INTERVAL 10 DAY) AND DATE_ADD(?, INTERVAL 10 DAY) ", [$today, $today]);
			    });
				
                // dd($data->limit(10)->get(),$today);
		    } else if($remaining_days === 'above_60'){
				 $data = $data->whereHas('productWarranty', function ($query) use ($today) {
				 	
				    $query->whereRaw(" DATEDIFF(DATE_ADD(bill_date, INTERVAL warranty_period MONTH), ?) < 60 ", [$today]);
				});
			}
			else if($remaining_days === 'below_60'){
				 $data = $data->whereHas('productWarranty', function ($query) use ($today) {
				 	
				    $query->whereRaw(" DATEDIFF(DATE_ADD(bill_date, INTERVAL warranty_period MONTH), ?) < -60 ", [$today]);
				});
			}
			else {
                $remaining_days = (int) $remaining_days;
                if ($remaining_days > 0) {
                    $data = $data->whereHas('productWarranty', function ($query) use ($today, $remaining_days) {
                        $query->whereRaw(" DATE_SUB(DATE_ADD(bill_date, INTERVAL warranty_period MONTH), INTERVAL 1 DAY) BETWEEN ? AND DATE_ADD(?, INTERVAL ? DAY) ", [$today, $today, $remaining_days]);
                    });
                } elseif ($remaining_days < 0) {
                    $absDays = abs($remaining_days);
                    $data = $data->whereHas('productWarranty', function ($query) use ($today, $absDays) {
                        $query->whereRaw(" DATE_SUB(DATE_ADD(bill_date, INTERVAL warranty_period MONTH), INTERVAL 1 DAY) BETWEEN DATE_SUB(?, INTERVAL ? DAY) AND ? ", [$today, $absDays, $today]);
                    });
                }
		    }
	    }

        if(!empty($search)){
            $data = $data->where(function($query) use ($search,$searchBy){
				if ($searchBy === 'id') {
					// Exact match for ID search
					$query->where('id', $search);
				}else{
					$query->where('item', 'LIKE','%'.$search.'%')
					->orWhere('id', 'LIKE','%'.$search.'%')
					->orWhere('bill_no', 'LIKE','%'.$search.'%')
					->orWhere('customer_name', 'LIKE','%'.$search.'%')
					->orWhere('class_name', 'LIKE','%'.$search.'%')
					->orWhere('mobile', 'LIKE','%'.$search.'%')
					// ->orWhere('email', 'LIKE','%'.$search.'%')
					->orWhere('phone', 'LIKE','%'.$search.'%')
					->orWhere('barcode', 'LIKE','%'.$search.'%')
					->orWhere('serial', 'LIKE','%'.$search.'%')
					->orWhere('branch', 'LIKE','%'.$search.'%')
					->orWhere('pincode', 'LIKE','%'.$search.'%')
					->orWhere('address', 'LIKE','%'.$search.'%')
                    ->orWhereHas('product', function ($q) use ($search) {
                    $q->whereHas('category', function ($query) use ($search) {
                        $query->where('name', 'LIKE', '%' . $search . '%');
                    });
                });
				}
                
            });
            $totalResult = $totalResult->where(function($query) use ($search,$searchBy){
				if ($searchBy === 'id') {
					// Exact match for ID search
					$query->where('id', $search);
				}else{
				    $query->where('item', 'LIKE','%'.$search.'%')
					->orWhere('id', 'LIKE','%'.$search.'%')
					->orWhere('bill_no', 'LIKE','%'.$search.'%')
					->orWhere('customer_name', 'LIKE','%'.$search.'%')
					->orWhere('mobile', 'LIKE','%'.$search.'%')
					// ->orWhere('email', 'LIKE','%'.$search.'%')
					->orWhere('phone', 'LIKE','%'.$search.'%')
					->orWhere('barcode', 'LIKE','%'.$search.'%')
					->orWhere('serial', 'LIKE','%'.$search.'%')
					->orWhere('branch', 'LIKE','%'.$search.'%')
					->orWhere('pincode', 'LIKE','%'.$search.'%')
					->orWhere('address', 'LIKE','%'.$search.'%');
				}
                
            });
        }
       

		   $data = $data->with([
				'CallHistoryData',
			   	'product',
				'Before_Amc_Subscription' => function($q) {
					$q->with(['productAmc' => function($q) {
						$q->with('AmcPlanData');
					}]);
				}
			])->orderBy('id','DESC')->paginate($paginate);
       


        // dd($data);
        $totalResult = $totalResult->count();

        $data = $data->appends([
            'page'=>$page,
            'paginate'=>$request->paginate,
            'search'=>$search,
           // 'date'=>$date,
			'from_date' => $from_date,
            'to_date' => $to_date,
            'amc_subscription'=>$amc_subscription,
			'remaining_days' => $remaining_days,
			'calls_filter' => $calls_filter
        ]);
		
        return view('amc.ho-sale', compact('data','totalResult','page','paginate','search','from_date','to_date','remaining_days','amc_subscription'));
    }
	
	
	public function pending_payment(Request $request){
		$search = $request->input('search');
		$from_date = $request->input('from_date');
		$to_date = $request->input('to_date');
		$paginate = $request->input('paginate', 25);

		// Base query for pending payments (status = 0)
		$query = BeforeAmcSubscription::with(['amcLinkData', 'kgaSaleData'])
				 ->where('status', 0)
				 ->orderBy('created_at', 'desc');

		// Apply search filter
		if ($search) {
			$query->where(function($q) use ($search) {
				$q->where('amc_unique_number', 'like', "%$search%")
				  ->orWhereHas('kgaSaleData', function($q) use ($search) {
					  $q->where('customer_name', 'like', "%$search%")
						->orWhere('mobile', 'like', "%$search%")
						->orWhere('serial', 'like', "%$search%");
				  });
			});
		}

		// Date range filter
		if ($from_date || $to_date) {
			$query->where(function($q) use ($from_date, $to_date) {
				if ($from_date) {
					$q->whereDate('created_at', '>=', $from_date);
				}
				if ($to_date) {
					$q->whereDate('created_at', '<=', $to_date);
				}
			});
		}

		$pendingPayments = $query->paginate($paginate);
		$totalResult = $pendingPayments->total();

		return view('amc.pending-payments', [
			'pendingPayments' => $pendingPayments,
			'totalResult' => $totalResult,
			'search' => $search,
			'from_date' => $from_date,
			'to_date' => $to_date,
			'paginate' => $paginate
		]);
	}
	
        public function pending_request(Request $request){
            $search = !empty($request->search)?$request->search:'';
            $data = BeforeAmcSubscription::where('status',2)->orWhere('status',3);
            if(!empty($search)){
                $data = $data->where(function($query) use ($search){
                    $query->where('kga_sales_id', 'LIKE','%'.$search.'%')
                    ->orWhere('amc_unique_number', 'LIKE','%'.$search.'%')
                    ;
                });
            }

            $data = $data->orderBy('id','DESC')->paginate(25);
            $amc_discount = env('AMC_DISCOUNT',0);
            return view('amc.pending-request',compact('data','amc_discount','search'));
            
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

            return view('amc.product-amc-plan', compact('id','kga_sales_id','data','product_name','amc_plan','amc_duration','plan_type','duration_type','kga_sales_data'));
            

        }

        public function request_approve(Request $request)
        { 
            // dd($request->all());
            // Find existing record with status 2,3
            $data = BeforeAmcSubscription::where('kga_sales_id', $request->kga_sales_id)
            ->where(function ($query) {
                $query->where('status', 2)
                    ->orWhere('status', 3);
            })->orderBy('id','DESC')
            ->first();
            // dd($data);

            if($data){
                $old_purchase_amount = $data->actual_amount;
              
                $data->discount = $request->approval_request;
                $data->status = 3; //approval status from admin
                $data->purchase_amount = $old_purchase_amount - ($old_purchase_amount * ($request->approval_request / 100));
                $data->save();
                return redirect()->back()->with('message', 'Request approved successfully');
            }else{
                return redirect()->back()->with('error','Something went wrong! Try again');

            }

        }

        public function call_back_date(Request $request)
        {
            // Validate the incoming request
            $validatedData = $request->validate([
                'kga_sale_id' => 'required|integer',
                'reminder_days' => 'required|integer|min:1',
                'remark' => 'nullable|string|max:255',
            ]);
            
            // Create a new record
            $data = new AmcCallHistory();
            $data->kga_sale_id = $validatedData['kga_sale_id'];
            $data->reminder_days = $validatedData['reminder_days'];
            $data->reminder_date = Carbon::now()->addDays($validatedData['reminder_days']);
            $data->remarks = $validatedData['remark'] ?? null;
            $data->auth_id = Auth::user()->id;
            $data->ip = $request->ip();
            
            // Save the record to the database
            if ($data->save()) {
                return redirect()->back()->with('message', 'Callback date saved successfully');
            } else {
                return redirect()->back()->with('error', 'Failed to save callback date');
            }
        }
        public function call_refuse(Request $request)
        {
            // dd($request->all());

            $validatedData = $request->validate([
                'kga_sale_id' => 'required|integer',
                'remark' => 'nullable|string|max:255',
            ]);

             // Create a new record
             $data = new AmcCallHistory();
             $data->kga_sale_id = $validatedData['kga_sale_id'];
             $data->type = 2; // for refused
             $data->remarks = $validatedData['remark'] ?? null;
             $data->auth_id = Auth::user()->id;
             $data->ip = $request->ip();
             // Save the record to the database
            if ($data->save()) {
                return redirect()->back()->with('message', 'Call Refused Successfully');
            } else {
                return redirect()->back()->with('error', 'Failed to save callback date');
            }
        }

        public function call_history_track(Request $request , $idStr){
            $id = Crypt::decrypt($idStr);
            $data = AmcCallHistory::with('userData')->where('kga_sale_id',$id)->get();

            return view('amc.call-history-track', compact('data','id'));



        }
        public function prepare_for_purchase_amc_plan( Request $request,$kga_sale_id,$idStr){
            // dd($request->all());
            $auth_id = Auth::User()->id;
            $id = Crypt::decrypt($idStr);
            $product_amc_data = ProductAmc::with('AmcPlanData')->find($id);
            $kga_sales_data = KgaSalesData::with('productWarranty')->find($kga_sale_id);
            $amc_unique_number =getAmcUniqueNumber();
            $before_amc_subscription_data = BeforeAmcSubscription::with('AmcLinkData')->where('amc_id', $id)
            ->where('kga_sales_id', $kga_sale_id)->where('type','staff')->where('sell_by',$auth_id)
            ->whereDate('created_at', now()) // Compare only the date part of created_at
            ->orderBy('id', 'DESC')
            ->get();
            
            $amc_discount = env('AMC_DISCOUNT',0);
            return view('amc.buy-amc-plan', compact('id','kga_sale_id','kga_sales_data','product_amc_data','amc_unique_number','before_amc_subscription_data','amc_discount'));
            
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

            $actual_amount = $request->actual_amount;
            $discount_type = $request->discount_type;

            if($discount_type=='flat'){
                $discount = round(($request->discount / $actual_amount) * 100,2);
            }else{
                $discount = $request->discount;
            }
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
            $type = 'staff';
            $auth_id = Auth::User()->id;
            $user = User::where('id', $auth_id)->first();

            if (!$user || empty($user->amc_incentive) || $user->amc_incentive == 0) {
                return redirect()->back()->with('error', 'First create an incentive before sending the payment link.');
            }
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
                   
                    $apiDomainUrl   = config('whatsapp.api_domain_url');
                    $channelNumber  = config('whatsapp.channel_number');
                    $apiKey         = config('whatsapp.api_key');
                    $templateName   = 'otp4';
                    $languageCode   = config('whatsapp.language_code'); // e.g., en_US, hi_IN, etc.

                    $recipientPhone = '91'.$mobile; // Example phone number
                    $url_link = route('AMC_payment_link', [
                        'd'          => $kga_sales_id,
                        'amc_serial' => $amc_unique_number,
                    ]);

                    $amount = "₹".number_format($purchase_amount, 2); 
                    $data = [
                        "MessagingProduct" => "whatsapp",
                        "RecipientType"    => "individual",
                        "to"               => $recipientPhone,
                        "Type"             => "template",
                        "Template" => [
                            "Name"     => $templateName,
                            "Language" => [
                                "Code" => $languageCode
                            ],
                            "components" => [
                                [
                                    "type"       => "body",
                                    "parameters" => [
                                        [
                                            "type" => "text",
                                            "text" => $amount // This is {{1}}
                                        ],
                                        [
                                            "type" => "text",
                                            "text" => $url_link // This is {{2}}
                                        ]
                                    ]
                                ]
                            ]
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
                    // if (curl_errno($ch)) {
                    //     echo "cURL Error: " . curl_error($ch);
                    // } else {
                    //     echo "Response: " . $response;
                    // }

                    curl_close($ch);
                    // For SMS
                    // $query_calling_number = "6291117317";
                    // $sms_entity_id = getSingleAttributeTable('settings','id',1,'sms_entity_id');
                    // $sms_template_id = "1707172234124956959";
                    
                    // $myMessage = urlencode('We are pleased to inform you that your product repair charge is now ready for payment. Kindly use the following link to complete the transaction: '.$url_link.' .AMMR TECHNOLOGY LLP');

                    // $sms_url = 'https://sms.bluwaves.in/sendsms/bulk.php?username=ammrllp&password=123456789&type=TEXT&sender=AMMRTL&mobile='.$mobile.'&message='.$myMessage.'&entityId='.$sms_entity_id.'&templateId='.$sms_template_id;
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

                    return redirect()->back()->with('message','Payment link send to this phone number, wating for payment!');
                }else{
                    throw new Exception('Record not found.');;
                }
                
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Something went wrong. Please try again later! Error: ' . $e->getMessage());
            }
        }

        public function after_discount_send_payment_link(Request $request){
            // dd($request->all());

            $data = BeforeAmcSubscription::where('kga_sales_id',$request->kga_sales_id)->orderBy('id','DESC')->first();
            // dd($data);
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
                    $apiDomainUrl   = config('whatsapp.api_domain_url');
                    $channelNumber  = config('whatsapp.channel_number');
                    $apiKey         = config('whatsapp.api_key');
                    $templateName   = 'otp4';
                    $languageCode   = config('whatsapp.language_code'); // e.g., en_US, hi_IN, etc.

                    $recipientPhone = '91'.$mobile; // Example phone number
                    $url_link = route('AMC_payment_link', [
                        'd'          => $kga_sales_id,
                        'amc_serial' => $amc_unique_number,
                    ]);

                    $amount = "₹".$purchase_amount; 
                    $data = [
                        "MessagingProduct" => "whatsapp",
                        "RecipientType"    => "individual",
                        "to"               => $recipientPhone,
                        "Type"             => "template",
                        "Template" => [
                            "Name"     => $templateName,
                            "Language" => [
                                "Code" => $languageCode
                            ],
                            "components" => [
                                [
                                    "type"       => "body",
                                    "parameters" => [
                                        [
                                            "type" => "text",
                                            "text" => $amount // This is {{1}}
                                        ],
                                        [
                                            "type" => "text",
                                            "text" => $url_link // This is {{2}}
                                        ]
                                    ]
                                ]
                            ]
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
                    // if (curl_errno($ch)) {
                    //     echo "cURL Error: " . curl_error($ch);
                    // } else {
                    //     echo "Response: " . $response;
                    // }

                    curl_close($ch); 
                    
                    // $query_calling_number = "6291117317";
                
                    // $sms_entity_id = getSingleAttributeTable('settings','id',1,'sms_entity_id');
                    // $sms_template_id = "1707172234124956959";
                    
                    // $myMessage = urlencode('We are pleased to inform you that your product repair charge is now ready for payment. Kindly use the following link to complete the transaction: '.$url_link.' .AMMR TECHNOLOGY LLP');

                    // $sms_url = 'https://sms.bluwaves.in/sendsms/bulk.php?username=ammrllp&password=123456789&type=TEXT&sender=AMMRTL&mobile='.$mobile.'&message='.$myMessage.'&entityId='.$sms_entity_id.'&templateId='.$sms_template_id;
                
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
                    
                    return redirect()->back()->with('message','Payment link send to this phone number, wating for payment!');
                }else{
                    throw new Exception('Record not found.');;
                }
                
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Something went wrong. Please try again later! Error: ' . $e->getMessage());
            }

        }



        ///AMC subscriptions data

        public function subscription_amc_data(Request $request){
			$user = auth()->user(); 
			$search = !empty($request->search)? $request->search: "";
            $date = !empty($request->date)?$request->date:'';
            
            
            // $data =AmcSubscription::with('SalesData')->orderBy('id','DESC')->get();
            $data =AmcSubscription::select('*');
            $totalResult =AmcSubscription::select('*');
            
			// 🔐 Apply user-based filter (only if not admin)
			if ($user->id != 1) {
				$data->where('sell_by', $user->id);
				$totalResult->where('sell_by', $user->id);
			}
            
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
            return view('amc.subscription-amc-data',compact('date','data','totalResult','search'));
        }
	
	public function subscription_amc_data_view($id){
	   $subscription = AmcSubscription::with('SalesData','AmcData.AmcPlanData.AmcDurationData')->findOrFail($id);
		return view('amc.subscription-view', compact('subscription'));
	}
	
	public function subscription_amc_data_pdf($id){
		 $subscription = AmcSubscription::with([
		 				'servicePayments',
			              'SalesData',
			 'AmcData.AmcPlanData.AmcDurationData',
		 ])->findOrFail($id);
		 $pdf = PDF::loadView('amc.subscription-pdf',compact('subscription'));
		return $pdf->download("amc-subscription-{$subscription->amc_unique_number}.pdf");
	}
	
	public function subscription_amc_csv(Request $request){
		
		$search = $request->input('search');
		$date   = $request->input('date');
		
		$data = AmcSubscription::with(['SalesData','AmcData'])->orderBy('id','DESC');
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
		$csvFileName = 'amc_subscriptions_' . date('Ymd_his') . '.csv';
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
				'Taxable Amount',
				'IGST',
				'CGST',
				'SGST',
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
				// GST Calculations
				$gst_value = optional(optional($amcData)->productData)->gst ?? 18;
				$actual_amount = $item->actual_amount;

				// Avoid division by zero or negative amount
				$taxable_value = $actual_amount > 0 ? $actual_amount / (1 + ($gst_value / 100)) : 0;
				$cgst = $taxable_value * ($gst_value / 2) / 100;
				$sgst = $taxable_value * ($gst_value / 2) / 100;
					
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
					number_format($taxable_value, 2),
					'0.00', 
					number_format($cgst, 2),
					number_format($sgst, 2),
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
        
        
        // //service ceter module
        // public function service_centre_sell_amc(Request $request){
            
        //     $validatedData = $request->validate([
        //         'contact_no' => ['nullable', 'regex:/^[0-9]{10}$/'], // 10-digit validation
        //         'bill_no' => ['nullable', 'string'],               // Optional validation for bill number
        //         'serial' => ['nullable', 'string'],                // Optional validation for serial
        //     ], [
        //         'contact_no.regex' => 'The contact number must be a valid 10-digit number.',
        //     ]);
            
        //     $contact_no = $request->input('contact_no','');
        //     $bill_no = $request->input('bill_no', '');
        //     $serial = $request->input('serial', '');
            
        //     $kga_sales_data = [];
            
        //     if (!empty($contact_no) || !empty($bill_no) || !empty($serial)) {
        //         $kga_sales_data = KgaSalesData::with('product','category','AmcSubscription')->whereNotNull('product_id');
                
        //         if (!empty($contact_no)) {
        //             $kga_sales_data->where('mobile', $contact_no)->orWhere('phone', $contact_no);
        //         }
                
        //         if (!empty($bill_no)) {
        //             $kga_sales_data->where('bill_no', $bill_no);
        //         }
                
        //         if (!empty($serial)) {
        //             $kga_sales_data->where('serial', $serial);
        //         }
                
        //         $kga_sales_data = $kga_sales_data->get();
        //     }
            
        //     return view ('amc.service-centre-sell-amc',compact('contact_no' ,'bill_no', 'serial','kga_sales_data'));
            
        // }
        
        
        
        // public function view_product_amc_plan(Request $request ,$pid){
        //     // dd($pid);
        //     $amc_plans = ProductAmc::with(['AmcPlanData' => function ($query) {
        //         $query->orderBy('name', 'asc'); // Order AmcPlanData by 'name'
        //     }])
        //     ->where('product_id', $pid)
        //     ->orderBy('duration', 'asc') // Then order by 'duration'
        //     ->orderBy('amount', 'asc') // Order ProductAmc by 'amount'
        //     ->get();
        // //    return view('amc.product-amc-plan-2',compact('amc_plans'));
        // }
    }
    
    