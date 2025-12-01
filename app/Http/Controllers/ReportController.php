<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Ledger;
use App\Models\ServicePartner;
use App\Models\KgaSalesData;
use App\Models\User;

class ReportController extends Controller
{
    //

    public function __construct(Request $request)
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            
            $accessReport = userAccess(Auth::user()->role_id,12);
            if(!$accessReport){
                abort(404);
            }          

            return $next($request);
        });
    }

   /* public function ledger_user(Request $request)
    {
        # code...
        $user_type = !empty($request->user_type)?$request->user_type:'';
        $service_partner_id = !empty($request->service_partner_id)?$request->service_partner_id:null;
        $from_date = !empty($request->from_date)?$request->from_date:date('Y-m-01', strtotime(date('Y-m-d')));
        $to_date = !empty($request->to_date)?$request->to_date:date('Y-m-d');
        $ob_amount = 0;
        $sp = \App\Models\ServicePartner::where('is_default', 0)->orderBy('person_name')->get();
		$filter_type = $request->filter_type ?? 'all';
        $data = $is_transaction = array();
        if(!empty($user_type) && !empty($service_partner_id)){
            $data = Ledger::select();
        
            if($user_type == 'servicepartner'){
                if(!empty($service_partner_id)){
                    $data = $data->where('user_type',$user_type)->where('service_partner_id',$service_partner_id);
                }
            }

            $data = $data->whereBetween(DB::raw('DATE(entry_date)'), [$from_date,$to_date]);
			if($filter_type == 'amc'){
			    $data = $data->whereNotNull('amc_id')->where('amount','>',0);
			}
            $data = $data->get();

            $ob_cred = Ledger::where('user_type','servicepartner')->where('service_partner_id',$service_partner_id)->where('type','credit')->where('entry_date', '<', $from_date)->sum('amount');
            $ob_deb = Ledger::where('user_type','servicepartner')->where('service_partner_id',$service_partner_id)->where('type','debit')->where('entry_date', '<', $from_date)->sum('amount');
            $ob_amount = ($ob_cred - $ob_deb);
            $is_transaction = Ledger::where('user_type','servicepartner')->where('service_partner_id',$service_partner_id)->first();
            // dd($ob_amount);
        }
        // dd($data);

        return view('report.ledger-user', compact('from_date','to_date','data','sp','user_type','service_partner_id','ob_amount','is_transaction','filter_type'));
    }  */
	
	public function ledger_user(Request $request)
{
    $user_type = $request->user_type ?? '';
    $service_partner_id = $request->service_partner_id ?? null;
    $service_centre_id = $request->service_centre_id ?? null;
    $ho_sale_id = $request->ho_sale_id ?? null;
    $admin_id = $request->admin_id ?? null;
    $from_date = $request->from_date ?? date('Y-m-01');
    $to_date = $request->to_date ?? date('Y-m-d');
    $filter_type = $request->filter_type ?? 'all';

    // Fetch dropdown data
    $sp = ServicePartner::where('is_default', 0)->orderBy('person_name')->get();
    $service_centres = User::where('role_id', 4)->orderBy('name')->get();
    if(Auth::user()->role_id == 6){
        $ho_sales = User::where('role_id', 6)->orderBy('name')->where('id', Auth::user()->id)->get(); 
    }else{
        $ho_sales = User::where('role_id', 6)->orderBy('name')->get();
    }
    
    $admins = User::where('role_id', 1)->orderBy('name')->get();

    $data = $is_transaction = [];
    $ob_amount = 0;

    // Only proceed if user_type and respective ID are properly selected
    if (
        ($user_type === 'servicepartner' && $service_partner_id) ||
        ($user_type === 'service_centre' && $service_centre_id) ||
        ($user_type === 'ho_sale' && $ho_sale_id) ||
        ($user_type === 'admin' && $admin_id)
    ) {
        // Main Ledger Query
        $ledgerQuery = Ledger::query();

        if ($user_type === 'servicepartner') {
            $ledgerQuery->where('user_type', 'servicepartner')
                        ->where('service_partner_id', $service_partner_id);
        } elseif ($user_type === 'service_centre') {
            $ledgerQuery->where('user_type', 'service_centre')
                        ->where('user_id', $service_centre_id);
        } elseif ($user_type === 'ho_sale') {
            $ledgerQuery->where('user_type', 'ho_sale')
                        ->where('user_id', $ho_sale_id);
        } elseif ($user_type === 'admin') {
            $ledgerQuery->where('user_type', 'admin')
                        ->where('user_id', $admin_id);
        }

        $ledgerQuery->whereBetween(DB::raw('DATE(entry_date)'), [$from_date, $to_date]);

        if ($filter_type === 'amc') {
            $ledgerQuery->whereNotNull('amc_id')->where('amount', '>', 0);
        }

        $data = $ledgerQuery->get();

        // Opening Balance Query
        $ob_cred = Ledger::where('user_type', $user_type);
        $ob_deb = Ledger::where('user_type', $user_type);

        if ($user_type === 'servicepartner') {
            $ob_cred->where('service_partner_id', $service_partner_id);
            $ob_deb->where('service_partner_id', $service_partner_id);
        } elseif ($user_type === 'service_centre') {
            $ob_cred->where('user_id', $service_centre_id);
            $ob_deb->where('user_id', $service_centre_id);
        } elseif ($user_type === 'ho_sale') {
            $ob_cred->where('user_id', $ho_sale_id);
            $ob_deb->where('user_id', $ho_sale_id);
        } elseif ($user_type === 'admin') {
            $ob_cred->where('user_id', $admin_id);
            $ob_deb->where('user_id', $admin_id);
        }

        $ob_cred = $ob_cred->where('type', 'credit')->where('entry_date', '<', $from_date)->sum('amount');
        $ob_deb = $ob_deb->where('type', 'debit')->where('entry_date', '<', $from_date)->sum('amount');
        $ob_amount = $ob_cred - $ob_deb;

        // Check if any transactions exist
        $is_transaction = Ledger::where('user_type', $user_type);

        if ($user_type === 'servicepartner') {
            $is_transaction->where('service_partner_id', $service_partner_id);
        } elseif ($user_type === 'service_centre') {
            $is_transaction->where('user_id', $service_centre_id);
        } elseif ($user_type === 'ho_sale') {
            $is_transaction->where('user_id', $ho_sale_id);
        } elseif ($user_type === 'admin') {
            $is_transaction->where('user_id', $admin_id);
        }

        $is_transaction = $is_transaction->first();

        // Merge Admin Entries (if applicable)
       /* if (in_array($user_type, ['service_centre', 'ho_sale'])) {
            $paymentIds = $data->pluck('payment_id')->filter()->unique();

            if ($paymentIds->isNotEmpty()) {
                $adminData = Ledger::where('user_type', 'admin')
                    ->whereIn('payment_id', $paymentIds)
                    ->whereBetween(DB::raw('DATE(entry_date)'), [$from_date, $to_date]);

                if ($filter_type === 'amc') {
                    $adminData->whereNotNull('amc_id')->where('amount', '>', 0);
                }

                $data = $data->merge($adminData->get())->sortBy('entry_date')->values();

                // Opening balance from admin entries linked by payment_id
                $adminObCred = Ledger::where('user_type', 'admin')
                    ->whereIn('payment_id', $paymentIds)
                    ->where('type', 'credit')
                    ->where('entry_date', '<', $from_date)
                    ->sum('amount');

                $adminObDeb = Ledger::where('user_type', 'admin')
                    ->whereIn('payment_id', $paymentIds)
                    ->where('type', 'debit')
                    ->where('entry_date', '<', $from_date)
                    ->sum('amount');

                $ob_amount += ($adminObCred - $adminObDeb);
            }
        } */
    }

    return view('report.ledger-user', compact(
        'from_date', 'to_date', 'data', 'sp', 'user_type',
        'service_partner_id', 'service_centre_id', 'ho_sale_id', 'admin_id',
        'ob_amount', 'is_transaction', 'filter_type',
        'service_centres', 'ho_sales', 'admins'
    ));
}




 /*   public function ledger_user_csv(Request $request)
    {
		//dd($request->all());
        # ledger csv download...
        $user_type = !empty($request->user_type)?$request->user_type:'';
        $service_partner_id = !empty($request->service_partner_id)?$request->service_partner_id:null;
        $from_date = !empty($request->from_date)?$request->from_date:date('Y-m-01', strtotime(date('Y-m-d')));
        $to_date = !empty($request->to_date)?$request->to_date:date('Y-m-d');

        //$sp = ServicePartner::find($service_partner_id);
        //$person_name = $sp->person_name;
        //$company_name = $sp->company_name;
		$person_name = '';
		$company_name = '';

		if ($user_type == 'servicepartner' && !empty($request->service_partner_id)) {
			$sp = ServicePartner::find($request->service_partner_id);
			$person_name = $sp->person_name ?? '';
			$company_name = $sp->company_name ?? '';
		} elseif ($user_type == 'service_centre' && !empty($request->service_centre_id)) {
			$sc = User::find($request->service_centre_id);
			$person_name = $sc->name ?? '';
			$company_name = $sc->address ?? ''; // or another relevant field
		} elseif ($user_type == 'ho_sale' && !empty($request->ho_sale_id)) {
			$ho = User::find($request->ho_sale_id);
			$person_name = $ho->name ?? '';
			$company_name = $ho->email ?? '';
		} elseif ($user_type == 'admin' && !empty($request->admin_id)) {
			$admin = User::find($request->admin_id);
			$person_name = $admin->name ?? '';
			$company_name = 'Admin';
		}
        $data = Ledger::select()->with('installation','repair');
        
        if($user_type == 'servicepartner'){
            if(!empty($service_partner_id)){
                $data = $data->where('user_type',$user_type)->where('service_partner_id',$service_partner_id);
            }
        }

        $data = $data->whereBetween(DB::raw('DATE(created_at)'), [$from_date,$to_date]);

        $data = $data->get();

        $ob_cred = Ledger::where('user_type','servicepartner')->where('service_partner_id',$service_partner_id)->where('type','credit')->where('entry_date', '<', $from_date)->sum('amount');
        $ob_deb = Ledger::where('user_type','servicepartner')->where('service_partner_id',$service_partner_id)->where('type','debit')->where('entry_date', '<', $from_date)->sum('amount');
        $ob_amount = ($ob_cred - $ob_deb);

        $ob_amount_cr_dr = getCrDr($ob_amount);
        if($ob_amount_cr_dr == 'Cr'){
            $tr_type = 'credit';
        } else if ($ob_amount_cr_dr == 'Dr'){
            $tr_type = 'debit';
        } else if ($ob_amount_cr_dr == ''){
            $tr_type = '';
        }
        $myArr = $ob_arr = array();
        $ob_arr = array(
            'type' => $tr_type,
            'purpose' => 'Opening Balance',
            'transaction_id' => '',
            'amount' => $ob_amount,
            'pincode' => '',
            'entry_date' => $from_date,
            'product_name' => '',
            'product_sl_no' => '',
            'customer_name' => '',
            'customer_mobile_no' => '',
            'remarks' => ''
        );
        
        foreach($data  as  $item){
            $product_name = $product_sl_no = $customer_name = $customer_mobile_no = $pincode = $remarks = '';
            if($item->purpose == 'installation'){
                $product_name = $item->installation->product_name;
                $product_sl_no = $item->installation->product_sl_no;
                $customer_name = $item->installation->customer_name;
                $customer_mobile_no = $item->installation->mobile_no;
                $pincode = $item->installation->pincode;

            } else if ($item->purpose == 'repair'){
                $product_name = $item->repair->product_name;
                $product_sl_no = $item->repair->product_sl_no;
                $customer_name = $item->repair->customer_name;
                $customer_mobile_no = $item->repair->customer_phone;
                $pincode = $item->repair->pincode;
            } else if ($item->purpose == 'maintenance'){
                $product_name = $item->maintenance->product_name;
                $product_sl_no = $item->maintenance->product_sl_no;
                $customer_name = $item->maintenance->customer_name;
                $customer_mobile_no = $item->maintenance->customer_phone;
                $pincode = $item->maintenance->pincode;
            } else if ($item->purpose == 'credit_note') {
                $remarks = $item->credit_note->remarks;
            }
            $myArr[] = array(
                'type' => $item->type,
                'purpose' => $item->purpose,
                'transaction_id' => $item->transaction_id,
                'amount' => $item->amount,
                'pincode' => $pincode,
                'entry_date' => $item->entry_date,
                'product_name' => $product_name,
                'product_sl_no' => $product_sl_no,
                'customer_name' => $customer_name,
                'customer_mobile_no' => $customer_mobile_no,
                'remarks' => $remarks
            ); 
            
        }
        array_unshift($myArr,$ob_arr);

        // dd($myArr);
        $fileName = $person_name."-".date('Y-m-d-H-i-s-A').".csv";
        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $fromColumn = array('','From',date('d/m/Y', strtotime($from_date)));
        $toColumn = array('','To',date('d/m/Y', strtotime($to_date)));
        $nameColumn = array('','Service Partner',$person_name.' - '.$company_name);
        $spaceColumn1 = array('','','','','','','','','','','');

        $columns = array('#','Date','Transaction Id', 'Purpose', 'Customer Name', 'Customer Phone', 'Pincode', 'Product Serial No', 'Product Name', 'Remarks', 'Debit', 'Credit',  'Closing');

        $callback = function() use($myArr, $fromColumn,$toColumn,$nameColumn,$spaceColumn1,$columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $fromColumn);
            fputcsv($file, $toColumn);
            fputcsv($file, $nameColumn);
            fputcsv($file, $spaceColumn1);
            fputcsv($file, $columns);
            $net_value = 0;
            $i = 1;
            foreach ($myArr as $item) {
                $creditAmt = $debitAmt = '';
                if($item['type'] == 'credit'){
                    $creditAmt = $item['amount'];
                    $net_value += $item['amount'];
                }
                if($item['type'] == 'debit'){
                    $debitAmt = ($item['amount']);
                    $net_value -= $item['amount'];
                }
                // echo $net_value; die;
                
                $row['#'] = $i;
                $row['Date']  = date('d/m/Y', strtotime($item['entry_date']));
                $row['Transaction Id'] = $item['transaction_id'];
                $row['Purpose'] = ucwords(str_replace("_"," ",$item['purpose']));    
                $row['Customer Name'] = $item['customer_name'];
                $row['Customer Phone'] = $item['customer_mobile_no'];
                $row['Pincode'] = $item['pincode'];
                $row['Product Serial No'] = $item['product_sl_no'];
                $row['Product Name'] = $item['product_name']; 
                $row['Remarks'] = $item['remarks'];            
                $row['Debit']  = replaceMinusSign($debitAmt);
                $row['Credit']    = $creditAmt;
                $row['Closing']  =  replaceMinusSign($net_value)." ".getCrDr($net_value);

                fputcsv($file, array($row['#'] , $row['Date'], $row['Transaction Id'],$row['Purpose'],$row['Customer Name'] , $row['Customer Phone'], $row['Pincode'] ,  $row['Product Serial No'] , $row['Product Name'], $row['Remarks'], $row['Debit'], $row['Credit'], $row['Closing'])); 
                
                $i++;                            
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);

    } */
	
	public function ledger_user_csv(Request $request)
{
    $user_type = $request->user_type ?? '';
    $service_partner_id = $request->service_partner_id ?? null;
    $service_centre_id = $request->service_centre_id ?? null;
    $ho_sale_id = $request->ho_sale_id ?? null;
    $admin_id = $request->admin_id ?? null;
    $from_date = $request->from_date ?? date('Y-m-01');
    $to_date = $request->to_date ?? date('Y-m-d');
    $filter_type = $request->filter_type ?? 'all';

    $person_name = '';
    $company_name = '';

    // Get user details based on user type
    if ($user_type == 'servicepartner' && !empty($service_partner_id)) {
        $sp = ServicePartner::find($service_partner_id);
        $person_name = $sp->person_name ?? '';
        $company_name = $sp->company_name ?? '';
    } elseif ($user_type == 'service_centre' && !empty($service_centre_id)) {
        $sc = User::find($service_centre_id);
        $person_name = $sc->name ?? '';
        $company_name = $sc->email ?? '';
    } elseif ($user_type == 'ho_sale' && !empty($ho_sale_id)) {
        $ho = User::find($ho_sale_id);
        $person_name = $ho->name ?? '';
        $company_name = $ho->email ?? '';
    } elseif ($user_type == 'admin' && !empty($admin_id)) {
        $admin = User::find($admin_id);
        $person_name = $admin->name ?? '';
        $company_name = 'Admin';
    }

    // Build the main ledger query - ADD AMC RELATIONSHIP
    $data = Ledger::query()->with([
        'installation',
        'repair',
        'maintenance',
        'credit_note',
        'amc_subscription',
        'amc_subscription.SalesData'
    ]);

    if ($user_type == 'servicepartner' && !empty($service_partner_id)) {
        $data->where('user_type', 'servicepartner')
             ->where('service_partner_id', $service_partner_id);
    } elseif ($user_type == 'service_centre' && !empty($service_centre_id)) {
        $data->where('user_type', 'service_centre')
             ->where('user_id', $service_centre_id);
    } elseif ($user_type == 'ho_sale' && !empty($ho_sale_id)) {
        $data->where('user_type', 'ho_sale')
             ->where('user_id', $ho_sale_id);
    } elseif ($user_type == 'admin' && !empty($admin_id)) {
        $data->where('user_type', 'admin')
             ->where('user_id', $admin_id);
    }

    // Use entry_date, not created_at
    $data->whereBetween(DB::raw('DATE(entry_date)'), [$from_date, $to_date]);

    // ADD AMC FILTER
    if ($filter_type === 'amc') {
        $data->whereNotNull('amc_id')->where('amount', '>', 0);
    }

    $data = $data->get();

    // Calculate opening balance with proper user type filtering
    $ob_cred = Ledger::where('user_type', $user_type);
    $ob_deb = Ledger::where('user_type', $user_type);

    if ($user_type == 'servicepartner') {
        $ob_cred->where('service_partner_id', $service_partner_id);
        $ob_deb->where('service_partner_id', $service_partner_id);
    } elseif ($user_type == 'service_centre') {
        $ob_cred->where('user_id', $service_centre_id);
        $ob_deb->where('user_id', $service_centre_id);
    } elseif ($user_type == 'ho_sale') {
        $ob_cred->where('user_id', $ho_sale_id);
        $ob_deb->where('user_id', $ho_sale_id);
    } elseif ($user_type == 'admin') {
        $ob_cred->where('user_id', $admin_id);
        $ob_deb->where('user_id', $admin_id);
    }

    $ob_cred = $ob_cred->where('type', 'credit')->where('entry_date', '<', $from_date)->sum('amount');
    $ob_deb = $ob_deb->where('type', 'debit')->where('entry_date', '<', $from_date)->sum('amount');
    $ob_amount = ($ob_cred - $ob_deb);

    $ob_amount_cr_dr = getCrDr($ob_amount);
    if ($ob_amount_cr_dr == 'Cr') {
        $tr_type = 'credit';
    } else if ($ob_amount_cr_dr == 'Dr') {
        $tr_type = 'debit';
    } else {
        $tr_type = '';
    }

    $myArr = [];
    $ob_arr = [
        'type' => $tr_type,
        'purpose' => 'Opening Balance',
        'transaction_id' => '',
        'amount' => $ob_amount,
        'pincode' => '',
        'entry_date' => $from_date,
        'product_name' => '',
        'product_sl_no' => '',
        'customer_name' => '',
        'customer_mobile_no' => '',
        'remarks' => ''
    ];

    foreach ($data as $item) {
        $product_name = $product_sl_no = $customer_name = $customer_mobile_no = $pincode = $remarks = '';
        
        if ($item->purpose == 'installation') {
            $product_name = $item->installation->product_name ?? '';
            $product_sl_no = $item->installation->product_sl_no ?? '';
            $customer_name = $item->installation->customer_name ?? '';
            $customer_mobile_no = $item->installation->mobile_no ?? '';
            $pincode = $item->installation->pincode ?? '';
        } else if ($item->purpose == 'repair') {
            $product_name = $item->repair->product_name ?? '';
            $product_sl_no = $item->repair->product_sl_no ?? '';
            $customer_name = $item->repair->customer_name ?? '';
            $customer_mobile_no = $item->repair->customer_phone ?? '';
            $pincode = $item->repair->pincode ?? '';
        } else if ($item->purpose == 'maintenance') {
            $product_name = $item->maintenance->product_name ?? '';
            $product_sl_no = $item->maintenance->product_sl_no ?? '';
            $customer_name = $item->maintenance->customer_name ?? '';
            $customer_mobile_no = $item->maintenance->customer_phone ?? '';
            $pincode = $item->maintenance->pincode ?? '';
        } else if ($item->purpose == 'credit_note') {
            $remarks = $item->credit_note->remarks ?? '';
        } else if ($item->purpose == 'for_amc_sell' || $item->purpose == 'amc' || !empty($item->amc_id)) {
            // Handle AMC data from amc_subscription and kga_sales_data tables
            if ($item->amc_subscription) {
                $product_sl_no = $item->amc_subscription->amc_unique_number ?? '';
                
                // If data is in kga_sales_data table instead
                if (empty($customer_name) && $item->amc_subscription->SalesData) {
                    $product_name = $item->amc_subscription->SalesData->item ?? $product_name;
                    $product_sl_no = $item->amc_subscription->SalesData->product_sl_no ?? $product_sl_no;
                    $customer_name = $item->amc_subscription->SalesData->customer_name ?? '';
                    $customer_mobile_no = $item->amc_subscription->SalesData->mobile ?? '';
                    $pincode = $item->amc_subscription->SalesData->pincode ?? '';
                }
            }
        }
        
        $myArr[] = [
            'type' => $item->type,
            'purpose' => $item->purpose,
            'transaction_id' => $item->transaction_id,
            'amount' => $item->amount,
            'pincode' => $pincode,
            'entry_date' => $item->entry_date,
            'product_name' => $product_name,
            'product_sl_no' => $product_sl_no,
            'customer_name' => $customer_name,
            'customer_mobile_no' => $customer_mobile_no,
            'remarks' => $remarks
        ];
    }
    
    array_unshift($myArr, $ob_arr);

    $fileName = $person_name . "-" . date('Y-m-d-H-i-s-A') . ".csv";
    $headers = [
        "Content-type" => "text/csv",
        "Content-Disposition" => "attachment; filename=$fileName",
        "Pragma" => "no-cache",
        "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
        "Expires" => "0"
    ];

    $fromColumn = ['', 'From', date('d/m/Y', strtotime($from_date))];
    $toColumn = ['', 'To', date('d/m/Y', strtotime($to_date))];
    $nameColumn = ['', 'Service Partner', $person_name . ' - ' . $company_name];
    $spaceColumn1 = ['', '', '', '', '', '', '', '', '', '', ''];
    $columns = ['#', 'Date', 'Transaction Id', 'Purpose', 'Customer Name', 'Customer Phone', 'Pincode', 'Product Serial No', 'Product Name', 'Remarks', 'Debit', 'Credit', 'Closing'];

    $callback = function() use($myArr, $fromColumn, $toColumn, $nameColumn, $spaceColumn1, $columns) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $fromColumn);
        fputcsv($file, $toColumn);
        fputcsv($file, $nameColumn);
        fputcsv($file, $spaceColumn1);
        fputcsv($file, $columns);
        
        $net_value = 0;
        $i = 1;
        
        foreach ($myArr as $item) {
            $creditAmt = $debitAmt = '';
            if ($item['type'] == 'credit') {
                $creditAmt = $item['amount'];
                $net_value += $item['amount'];
            }
            if ($item['type'] == 'debit') {
                $debitAmt = ($item['amount']);
                $net_value -= $item['amount'];
            }

            $row = [
                '#' => $i,
                'Date' => date('d/m/Y', strtotime($item['entry_date'])),
                'Transaction Id' => $item['transaction_id'],
                'Purpose' => ucwords(str_replace("_", " ", $item['purpose'])),
                'Customer Name' => $item['customer_name'],
                'Customer Phone' => $item['customer_mobile_no'],
                'Pincode' => $item['pincode'],
                'Product Serial No' => $item['product_sl_no'],
                'Product Name' => $item['product_name'],
                'Remarks' => $item['remarks'],
                'Debit' => replaceMinusSign($debitAmt),
                'Credit' => $creditAmt,
                'Closing' => replaceMinusSign($net_value) . " " . getCrDr($net_value)
            ];

            fputcsv($file, [
                $row['#'],
                $row['Date'],
                $row['Transaction Id'],
                $row['Purpose'],
                $row['Customer Name'],
                $row['Customer Phone'],
                $row['Pincode'],
                $row['Product Serial No'],
                $row['Product Name'],
                $row['Remarks'],
                $row['Debit'],
                $row['Credit'],
                $row['Closing']
            ]);

            $i++;
        }
        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}
}
