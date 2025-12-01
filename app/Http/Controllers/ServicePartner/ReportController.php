<?php

namespace App\Http\Controllers\ServicePartner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Models\Ledger;

class ReportController extends Controller
{
    public function __construct(Request $request)
    {
        $this->middleware('auth:servicepartner');
    }

    public function ledger(Request $request)
    {
        # code...
	
        $ob_amount = 0;
        $is_transaction = array();
        $service_partner_id = Auth::user()->id;
        $from_date = !empty($request->from_date)?$request->from_date:date('Y-m-01', strtotime(date('Y-m-d')));
        $to_date = !empty($request->to_date)?$request->to_date:date('Y-m-d');
		$filter_type = $request->filter_type ?? 'all';
        $data = Ledger::where('user_type', 'servicepartner')->where('service_partner_id', $service_partner_id);
        $data = $data->whereBetween(DB::raw('DATE(created_at)'), [$from_date,$to_date]);
		
		// Apply AMC Incentive filter
		if($filter_type == 'amc'){
			$data = $data->whereNotNull('amc_id')->where('amount','>',0);
		}
		
        $data = $data->get();
        $ob_cred = Ledger::where('user_type','servicepartner')->where('service_partner_id',$service_partner_id)->where('type','credit')->where('entry_date', '<', $from_date)->sum('amount');
        $ob_deb = Ledger::where('user_type','servicepartner')->where('service_partner_id',$service_partner_id)->where('type','debit')->where('entry_date', '<', $from_date)->sum('amount');
        $ob_amount = ($ob_cred - $ob_deb);

        $is_transaction = Ledger::where('user_type','servicepartner')->where('service_partner_id',$service_partner_id)->first();

        // dd($data);

        return view('servicepartnerweb.report.ledger', compact('data','to_date','from_date','ob_amount','is_transaction','filter_type'));
    }

    public function ledger_csv(Request $request)
    {
        # ledger csv...

        $user_type = 'servicepartner';
        $service_partner_id = Auth::user()->id;
        $from_date = !empty($request->from_date)?$request->from_date:date('Y-m-01', strtotime(date('Y-m-d')));
        $to_date = !empty($request->to_date)?$request->to_date:date('Y-m-d');

        
        $person_name = Auth::user()->person_name;
        $company_name = Auth::user()->company_name;

        $data = Ledger::select('*');        
        $data = $data->where('user_type',$user_type)->where('service_partner_id',$service_partner_id);
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
    }
}
