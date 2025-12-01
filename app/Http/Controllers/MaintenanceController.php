<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Models\Maintenance;
use App\Models\ServicePartner;
use App\Models\ServicePartnerCharge;
use App\Models\CloseMaintenance;
use App\Models\Ledger;
use App\Models\PurchaseOrderBarcode;
use App\Models\KgaSalesData;
use App\Models\Product;
use App\Models\ProductAmc;
use App\Models\AmcSubscription;
use App\Models\ProductWarranty;
use DateTime;
use Illuminate\Support\Facades\Auth;

class MaintenanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function list(Request $request,$service_type='chimney')
    {
        # List ...
        
        $paginate = 20;
        $search = !empty($request->search)?$request->search:'';
        $service_partner_id = !empty($request->service_partner_id)?$request->service_partner_id:'';
        $created_at = !empty($request->created_at)?$request->created_at:'';
        $closing_type = !empty($request->closing_type)?$request->closing_type:'';
        // $service_type = !empty($request->service_type)?$request->service_type:'';

        $service_partners = ServicePartner::where('is_default', 0)->orderBy('person_name')->get();
        $data = Maintenance::select('*');
        $totalResult = Maintenance::select('*');

        // if(!empty($service_type)){
        //     $data = $data->where('service_type',$service_type);
        //     $totalResult = $totalResult->where('service_type',$service_type);
        // }

        if(!empty($search)){
            $data = $data->where('unique_id', 'LIKE', '%'.$search.'%')->orWhere('product_name', 'LIKE', '%'.$search.'%')->orWhere('customer_name', 'LIKE', '%'.$search.'%')->orWhere('customer_phone', 'LIKE', '%'.$search.'%');
            $totalResult = $totalResult->where('unique_id', 'LIKE', '%'.$search.'%')->orWhere('product_name', 'LIKE', '%'.$search.'%')->orWhere('customer_name', 'LIKE', '%'.$search.'%')->orWhere('customer_phone', 'LIKE', '%'.$search.'%');
        }

        if(!empty($service_partner_id)){
            $data = $data->where('service_partner_id', $service_partner_id);
            $totalResult = $totalResult->where('service_partner_id', $service_partner_id);
        }

        if(!empty($created_at)){
            $data = $data->whereRaw(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d') = '".$created_at."'"));
            $totalResult = $totalResult->whereRaw(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d') = '".$created_at."'"));
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

        // dd($data);

        $data = $data->appends([
            'search'=>$search,
            'created_at'=>$created_at,
            'page'=>$request->page,
            'closing_type'=>$closing_type,
            // 'service_type' => $service_type,
            'service_partner_id'=>$service_partner_id
        ]);

        return view('maintenance.list', compact('search','service_partner_id','paginate','data','totalResult','service_partners','created_at','closing_type'));



    }

    public function add(Request $request )
    {
        # Add New Maintenance Form ...
        // dd($request->all());
        $serial = $request->get('serial');
		$productId = $request->get('product_id');
        $GetCleaningWarranty = GetCleaningWarranty($serial);
		$GetDeepCleaningWarranty = GetDeepCleaningWarranty($serial);
		$amcCleaningUsed = GetAmcCleaningWarranty($serial);
        $amcDeepCleaningUsed = GetAmcDeepCleaningWarranty($serial);
         //dd($GetCleaningWarranty);
		 // dd($GetDeepCleaningWarranty);
        $repeat_call = 0 ;

        $checkExistSerial = Maintenance::where('product_sl_no', $serial)->where('is_closed',1)->orderBy('id','DESC')->first(); 
        
        
        if(!empty($checkExistSerial)){
            $last_entry_date = $checkExistSerial->created_at;
            $date1=date_create($last_entry_date);
            $date2=date_create(date('Y-m-d'));
            $diff=date_diff($date1,$date2);
            // $days = $diff->format("%d");
            $days = $diff->days;
            // dd($days);

            if($days <= 30){     
                $repeat_call = 1;
            }
        }


        return view('maintenance.add',compact('GetCleaningWarranty','GetDeepCleaningWarranty','amcCleaningUsed','amcDeepCleaningUsed','repeat_call'));
    }

    public function add_call_request(Request $request )
    {
        # Add New Maintenance Form ...
        
        return view('maintenance.add');
    }
	public function ServiceProvderUpdate(Request $request){
		$data= Maintenance::findOrFail($request->id);
        if($data){
            $service_type = $data->service_type;    
            $service_charge = false;
            $checkServiceCharge = ServicePartnerCharge::where('service_partner_id', $request->update_service_partner)->where('product_id', $data->product_id)->first();
          
            if(!empty($checkServiceCharge)){
                if($service_type == 'repairing'){
                    $service_charge = $checkServiceCharge->repair;
                } else if ($service_type == 'cleaning'){
                    $service_charge = $checkServiceCharge->cleaning;
                }else if ($service_type == 'deep_cleaning'){
                    $service_charge = $checkServiceCharge->deep_cleaning;
				}
                
            }
            if(!$service_charge){
                Session::flash('message', "No ".$service_type." charge added for this product for this service partner ");
                return redirect()->back();
            }
            $browser_name = isset($request->browser_name)?$request->browser_name:NULL;
            $navigator_useragent = isset($request->navigator_useragent)?$request->navigator_useragent:NULL;
            addChangeLog(Auth::user()->id,$request->ip(),'maintenance_service_partner_update',$browser_name,$navigator_useragent,$data);
            $data->service_partner_id = $request->update_service_partner;
            $data->service_charge = $service_charge;
            $data->save();

            
            Session::flash('message', 'Service partner has been successfully updated');
            return redirect()->back();
        }else{
            Session::flash('message', 'Data not found.');
            return redirect()->back();
        }		
	}

    public function checkitemstatus(Request $request)
    {
      //dd($request->all());
        $contact_type = $request->input('contact_type', 'mobile');
        $mobile = $request->input('mobile', '');
        $phone = $request->input('phone', '');
        $bill_no = $request->input('bill_no', '');
        $serial = $request->input('serial', '');
		
		$GetCleaningWarranty = GetCleaningWarranty($serial);
		$GetDeepCleaningWarranty = GetDeepCleaningWarranty($serial);
		$amcCleaningUsed = GetAmcCleaningWarranty($serial);
        $amcDeepCleaningUsed = GetAmcDeepCleaningWarranty($serial);
		
        $non_khosla_serial = $request->input('non_khosla_serial', '');
        $type = $request->input('type', 'khosla');
        $data = null;
        $khosla_data = [];
		$amc_warranty = null;
        // Check if the serial number is provided
        if($type == 'non-khosla'){
            if (!empty($non_khosla_serial)) {
                // Use Eloquent's query builder to filter the records
                $data = PurchaseOrderBarcode::with('product','productWarranty', 'goodsWarranty')->where('barcode_no', $non_khosla_serial)->first();
                // dd($data);
				$data->amc_subscription = AmcSubscription::with('AmcData.AmcDurationData','AmcData.AmcPlanData')
															->where('serial', $data->barcode_no)
															->first();
				//if($data->amc_subscription){
				   // $amcSubscription = $data->amc_subscription;
					
				//}
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
				
				if(count($khosla_data)>0){
				   $first_item = $khosla_data->first();
					$product_id = $first_item->product_id;
					$bill_date = $first_item->bill_date;
					$warranty = ProductWarranty::where('goods_id', $product_id)
												->where('dealer_type', $type)
												->get();
					$warranty_period = 0;
					foreach ($warranty as $item) {
						if ($item->warranty_type === 'additional') {
							$comprehensive_period = ProductWarranty::where('goods_id', $product_id)
								->where('dealer_type', $type)
								->where('warranty_type', 'comprehensive')
								->pluck('warranty_period')->first() ?? 0;
							$warranty_period += $item->warranty_period + $comprehensive_period;
						} else {
							$warranty_period += $item->warranty_period;
						}
					}
					// Calculate warranty and AMC start dates
					 $warranty_end_date = date('Y-m-d', strtotime($bill_date . ' + ' . $warranty_period . ' months'));
					 $amc_start_date = date('Y-m-d', strtotime($warranty_end_date . '+1 days'));
					 $first_item->amc_subscription = AmcSubscription::with('AmcData.AmcDurationData','AmcData.AmcPlanData')->where('serial', $first_item->serial)->first();
					$getAmcSubscription = $first_item->amc_subscription;
					if($getAmcSubscription){
					$amc_actual_normal_cleaning = optional(optional($getAmcSubscription->AmcData)->AmcDurationData)->normal_cleaning?? "0";
                    $amc_actual_deep_cleaning = optional(optional($getAmcSubscription->AmcData)->AmcDurationData)->deep_cleaning?? "0";
					$amc_used_normal_cleaning = ActualAmcCleaningWarranty($request->serial,$amc_start_date);
					$amc_used_deep_cleaning = 	ActuallAmcDeepCleaningWarranty($request->serial,$amc_start_date);
						$amc_warranty = [
							'amc_actual_normal_cleaning' => $amc_actual_normal_cleaning,
							'amc_actual_deep_cleaning'  => $amc_actual_deep_cleaning,
							'amc_used_normal_cleaning' => $amc_used_normal_cleaning,
							'amc_used_deep_cleaning' => $amc_used_deep_cleaning,
							'amc_remaining_normal_cleaning' => $amc_actual_normal_cleaning - $amc_used_normal_cleaning,
						   'amc_remaining_deep_cleaning' => $amc_actual_deep_cleaning - $amc_used_deep_cleaning,
						];
					}
				}
			
            }
            
        }
        // dd($khosla_data);
        return view('maintenance.checkitemstatus', 
                    compact('data','non_khosla_serial','khosla_data','contact_type', 'mobile', 'phone', 'bill_no','serial','type','GetCleaningWarranty','GetDeepCleaningWarranty','amcCleaningUsed','amcDeepCleaningUsed','amc_warranty')
                );
    }
    
    

    public function save(Request $request)
    {
        # Save...
       // dd($request->all());
		$amc_cleaning_status = "No";
		$start_date = null;
		if (isset($request->amc_cleaning_status)) {
			$amc_cleaning_status = $request->amc_cleaning_status ?? null;
		}
		if (isset($request->$start_date)) {
			$start_date = $request->$start_date ?? null;
		}
		
		
        $request->validate([
            'dealer_id' => 'required',
            'pincode' => 'required',
            'service_partner_id' => 'required',
            'customer_name' => 'required',
            'customer_phone' => 'required',
            'product_value' => 'nullable',
            'address' => 'required',
            'bill_no' => 'required',
            'order_date' => 'required',
            'product_id' => 'required',
            'service_type' => 'required',
        ],[
            'dealer_id.required' => 'Please select dealer',
            'pincode.required' => 'Please add pincode',
            'service_partner_id.required' => 'Please select service partner',
            'customer_name.required' => 'Please enter customer name',
            'customer_phone.required' => 'Please enter customer phone number',
            'bill_no.required' => 'Please enter bill number',
            'order_date.required' => 'Please choose order date',
            'product_id.required' => 'Please select product',
            'service_type.required' => 'Please select service type',
        ]);

        $params = $request->except('_token');
		 
		// Get warranty info using serial number
		$GetCleaningWarranty = GetCleaningWarranty($request->serial);
		$GetDeepCleaningWarranty = GetDeepCleaningWarranty($request->serial);
        // dd($params);    
        $service_type = $params['service_type'];    
        $service_charge = false;
		$is_amc_used = 0;
        $cleaning_status = strtolower($request->cleaning_status ?? '');
		if(isset($amc_cleaning_status)){
			 $amc_cleaning_status = strtolower($request->amc_cleaning_status ?? '');
		}
       
		if ( $service_type == 'cleaning') {
            if ($cleaning_status === 'no' && $amc_cleaning_status === 'no') {
                return redirect()->back()
				->withErrors([
					'serial' => 'No cleaning warranty (Comprehensive or AMC) available for this product.',
				])
				->withInput($request->all());
            }elseif ($cleaning_status === 'no' && $amc_cleaning_status === 'yes') {
				$checkServiceCharge = ServicePartnerCharge::where('service_partner_id', $params['service_partner_id'])->where('product_id', $params['product_id'])->first();
				if(!empty($checkServiceCharge)){
					if($request->amc_remaining_normal_cleaning>0){
					 	//$service_charge = 0;
						 $service_charge = $checkServiceCharge->cleaning;
						 $is_amc_used = 1;
					}else{
						  $service_charge = $checkServiceCharge->cleaning;
					}
                }else{
                     $service_charge = 0;
                }
            }elseif ($cleaning_status === 'yes') {
				 $is_amc_used = 0;
                $checkServiceCharge = ServicePartnerCharge::where('service_partner_id', $params['service_partner_id'])->where('product_id', $params['product_id'])->first();
                if(!empty($checkServiceCharge)){
                    $service_charge = $checkServiceCharge->cleaning;
                }else{
                     $service_charge = 0;
                }
            }else{
				 $is_amc_used = 0;
                $service_charge = 0;
            }
			
		} elseif ( $service_type == 'deep_cleaning') {
			// if ($cleaning_status === 'no' && $amc_cleaning_status === 'no') {
			if ($amc_cleaning_status === 'no') {
                return redirect()->back()
				->withErrors([
					'serial' => 'No cleaning warranty (Comprehensive or AMC) available for this product.',
				])
				->withInput($request->all());
            //}elseif ($cleaning_status === 'no' && $amc_cleaning_status === 'yes') {
			}elseif ($amc_cleaning_status === 'yes') {
				$checkServiceCharge = ServicePartnerCharge::where('service_partner_id', $params['service_partner_id'])->where('product_id', $params['product_id'])->first();
				 if(!empty($checkServiceCharge)){
					if($request->amc_remaining_deep_cleaning >0 ){
					 	//$service_charge = 0;
						$service_charge = $checkServiceCharge->deep_cleaning;
						 $is_amc_used = 1;
					}else{
						  $service_charge = $checkServiceCharge->deep_cleaning;
					}
                }else{
                     $service_charge = 0;
                }
			}
			/* elseif ($cleaning_status === 'yes') {
				 $is_amc_used = 0;
                $checkServiceCharge = ServicePartnerCharge::where('service_partner_id', $params['service_partner_id'])->where('product_id', $params['product_id'])->first();
                if(!empty($checkServiceCharge)){
                    $service_charge = $checkServiceCharge->deep_cleaning;
                }else{
                     $service_charge = 0;
                }
            }else{
				 $is_amc_used = 0;
                $service_charge = 0;
            } */
		}else{
			 $checkServiceCharge = ServicePartnerCharge::where('service_partner_id', $params['service_partner_id'])->where('product_id', $params['product_id'])->first();

			if(!empty($checkServiceCharge)){
				if($service_type == 'repairing'){
					$service_charge = $checkServiceCharge->repair;
				} else if ($service_type == 'cleaning'){
					$service_charge = $checkServiceCharge->cleaning;
				} else if ($service_type == 'deep_cleaning') {
					$service_charge = $checkServiceCharge->deep_cleaning; // Deep cleaning

				}

			}
		}
		
        $params['repeat_call'] = 0;       //if the dap_call is not repeat in 30 days
        $params['repeat_id'] = NULL;       //if the dap_call is not repeat in 30 days
        if($service_type === 'repairing'){
            $checkExistSerial = Maintenance::where('product_sl_no', $params['product_sl_no'])->where('service_type','repairing')->where('is_closed',1)->orderBy('id','DESC')->first();
            // dd($checkExistSerial);


            if (!empty($checkExistSerial)) {
                // Extract the date part only (ignoring the time)
                $last_entry_date = $checkExistSerial->created_at->format('Y-m-d');
                // Create DateTime objects for comparison
                $date1 = date_create($last_entry_date);
                $date2 = date_create(date('Y-m-d'));
                
                // Calculate the difference between the two dates
                $diff = date_diff($date1, $date2);
                // $days = $diff->format("%a"); // %a gives total number of days
                $days = $diff->days;
            
                if ($days <= 30) {
                    // return redirect()->back()->withErrors(['product_sl_no' => 'You cannot add the same item within 30 days'])->withInput($request->all());

                    $params['repeat_call'] = 1;       //if the dap_call is not repeat in 30 days
                    $params['repeat_id'] = $checkExistSerial->id;       //if the dap_call is not repeat in 30 days
                }
            }
        }


        if($service_charge === false || $service_charge === null){
            return redirect()->back()->withErrors(['service_partner_id' =>  'No '.$service_type.' charge added for this product for this service partner ' ])->withInput($request->all());
        }
        
        $uniue_id = 'MNT'.genAutoIncreNoYearWise(5,'maintenances',date('Y'));
        $mainenanceArr = array(
            'product_id' => $params['product_id'],
            'service_partner_id' => $params['service_partner_id'],
            'dealer_id' => $params['dealer_id'],
            'unique_id' => $uniue_id,
            'pincode' => $params['pincode'],
            'bill_no' => $params['bill_no'],
            'order_date' => $params['order_date'],
            'customer_name' => $params['customer_name'],
            'customer_phone' => $params['customer_phone'],
            'address' => $params['address'],
            'product_value' => $params['product_value'],
            'product_sl_no' => $params['product_sl_no'],
            'repeat_call' => $params['repeat_call'],
            'repeat_id' => $params['repeat_id'],
            'product_name' => $params['product_name'],
            'service_for' => $params['service_for'],
            'service_type' => $params['service_type'],
            'maintenance_type' => $params['maintenance_type'],
			'is_amc' => ($params['maintenance_type'] == 'amc') ? 1 : 0,
            'remarks' => $params['remarks'],
            'service_charge' => $service_charge,
            'is_spare_chargeable' => $params['is_spare_chargeable'],
            'is_repair_chargeable' => $params['is_repair_chargeable'],
            'out_of_warranty' => $params['out_of_warranty'],
			'is_amc'  => $is_amc_used,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        );
        //dd($mainenanceArr);
        Maintenance::insert($mainenanceArr);

        $successMsg = " Request call booked successfully ";

        Session::flash('message', $successMsg);
        return redirect('/maintenance/list');
    }

    
    public function save_remark(Request $request)
    {
        # save remark ...
        $request->validate([
            'remarks' => 'required'
        ]);
        $params = $request->except('_token');
        // dd($params);
        $request_url = $params['request_url'];

        Maintenance::where('id',$params['maintenance_id'])->update([
            'remarks' => $params['remarks']
        ]);

        // dd($params);

        Session::flash('message', "Remark added successfully.");
        return redirect('/maintenance/list?'.$request_url);

    }

    public function submit_call_close(Request $request)
    {
        # close the call...
        
       // dd($request->all());
        $params = $request->except('_token');
        $params['created_at'] = date('Y-m-d H:i:s');
        $request_url = $params['request_url'];
        // dd($params);

        $maintenance_id = $params['maintenance_id'];
        unset($params['request_url']);
        CloseMaintenance::insert($params);
        Maintenance::where('id',$maintenance_id)->update([
            'is_closed' => 1,
        ]);

        $data = Maintenance::find($maintenance_id);
        $service_partner_id = $data->service_partner_id;
        $service_charge = $data->service_charge;
        $unique_id = $data->unique_id;
		//dd($service_charge);
        # Ledger Entry Service Partner        
        $this->ledgerEntryCallClose($service_partner_id,$service_charge,$unique_id,$maintenance_id);
        
        Session::flash('message', "Call closed successfully.");
        return redirect('/maintenance/list/?'.$request_url);
    }

    private function ledgerEntryCallClose($service_partner_id,$amount,$unique_id,$maintenance_id){
        $data = Maintenance::find($maintenance_id);
        if($data->repeat_call === 1 && $data->repeat_id !== NULL){
            $pre_data = Ledger::where('maintenance_id',$data->repeat_id)->where('type','credit')->orderBy('id','DESC')->first();
            // dd($pre_data);
            $ledgerData = array(
                'type' => 'debit',
                'service_partner_id' => $pre_data->service_partner_id,
                'amount' => $pre_data->amount,
                'entry_date' => date('Y-m-d'),
                'user_type' => 'servicepartner',
                'purpose' => 'maintenance(repeat call)',
                'transaction_id' => $pre_data->transaction_id,
                'maintenance_id' => $pre_data->maintenance_id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            );
    
            Ledger::insert($ledgerData);
        }
        $ledgerData = array(
            'type' => 'credit',
            'service_partner_id' => $service_partner_id,
            'amount' => $amount,
            'entry_date' => date('Y-m-d'),
            'user_type' => 'servicepartner',
            'purpose' => 'maintenance',
            'transaction_id' => $unique_id,
            'maintenance_id' => $maintenance_id,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        );

        Ledger::insert($ledgerData);
    }

    public function cancel(Request $request,$idStr,$getQueryString='')
    {
        # Cancel Call...

        try {
            $id = Crypt::decrypt($idStr);
            $data = Maintenance::where('id',$id)->update(['is_cancelled'=>1]);

            $maintenance = Maintenance::find($id);
            $params['unique_id'] = $maintenance->unique_id;

            $browser_name = isset($request->browser_name)?$request->browser_name:NULL;
            $navigator_useragent = isset($request->navigator_useragent)?$request->navigator_useragent:NULL;
            
            addChangeLog(Auth::user()->id,$request->ip(),'chimney_maintenance_cancel',$browser_name,$navigator_useragent,$params);
            
            Session::flash('message', "Call cancelled successfully.");
            return redirect('/maintenance/list?'.$getQueryString);

        } catch ( DecryptException $e) {
            return abort(404);
        }
        
    }



}
