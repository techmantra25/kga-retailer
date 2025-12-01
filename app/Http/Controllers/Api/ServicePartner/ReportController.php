<?php

namespace App\Http\Controllers\Api\ServicePartner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\DB;
use File;
use App\Models\ServicePartner;
use App\Models\Ledger;


class ReportController extends Controller
{
    private $service_partner_id;
    private $person_name;
    public function __construct(Request $request)
    {
        # pass bearer token in Authorizations key...
        if (! $request->hasHeader('Authorizations')) {
            response()->json(["status"=>false,"message"=>"Unauthorized"],401)->send();
            exit();
        } else {
            $bearer_token = $request->header('Authorizations');
            $token = str_replace("Bearer ","",$bearer_token);            
            try {
                $this->service_partner_id = Crypt::decrypt($token);
                $sp = ServicePartner::find($this->service_partner_id);
                $this->person_name = $sp->person_name;
            } catch (DecryptException $e) {
                response()->json(["status"=>false,"message"=>"Mismatched token"],400)->send();
                exit();
            }
        }
    }

    public function ledger(Request $request)
    {
        # ledger...
        $opening_balance = 0;
        $service_partner_id = $this->service_partner_id;
        $from_date = !empty($request->from_date)?$request->from_date:date('Y-m-01', strtotime(date('Y-m-d')));
        $to_date = !empty($request->to_date)?$request->to_date:date('Y-m-d');

        $data = Ledger::select('id','type','amount','entry_date','purpose','transaction_id')->where('user_type', 'servicepartner')->where('service_partner_id', $service_partner_id);
        $data = $data->whereBetween(DB::raw('DATE(created_at)'), [$from_date,$to_date]);
        $data = $data->get();

        $ob_cred = Ledger::where('user_type','servicepartner')->where('service_partner_id',$service_partner_id)->where('type','credit')->where('entry_date', '<', $from_date)->sum('amount');
        $ob_deb = Ledger::where('user_type','servicepartner')->where('service_partner_id',$service_partner_id)->where('type','debit')->where('entry_date', '<', $from_date)->sum('amount');
        $opening_balance = ($ob_cred - $ob_deb);


        return Response::json([
            'status' => true,
            'message' => "My Ledger",
            'data' => array(
                'from_date' => $from_date,
                'to_date' => $to_date,
                'opening_balance' => $opening_balance,
                'list' => $data
            )
        ]);

    }

    public function ledger_csv(Request $request)
    {
        # download csv ledger...

        $user_type = 'servicepartner';
        $service_partner_id = $this->service_partner_id;
        $from_date = !empty($request->from_date)?$request->from_date:date('Y-m-01', strtotime(date('Y-m-d')));
        $to_date = !empty($request->to_date)?$request->to_date:date('Y-m-d');

        
        $person_name = $this->person_name;

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
            'entry_date' => $from_date
        );

        

        // dd($data);        

        $myArr = array();
        foreach($data  as  $item){
            $myArr[] = array(
                'type' => $item->type,
                'purpose' => $item->purpose,
                'transaction_id' => $item->transaction_id,
                'amount' => $item->amount,
                'entry_date' => $item->entry_date
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

        $columns = array('Date','Transaction Id', 'Purpose', 'Debit', 'Credit',  'Closing');

        $callback = function() use($myArr, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            $net_value = 0;
            
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
                
                
                $row['Date']  = date('d/m/Y', strtotime($item['entry_date']));
                $row['Transaction Id'] = $item['transaction_id'];
                $row['Purpose'] = ucwords(str_replace("_"," ",$item['purpose']));                
                $row['Debit']  = replaceMinusSign($debitAmt);
                $row['Credit']    = $creditAmt;
                $row['Closing']  =  replaceMinusSign($net_value)." ".getCrDr($net_value);

                fputcsv($file, array($row['Date'], $row['Transaction Id'],$row['Purpose'], $row['Debit'], $row['Credit'], $row['Closing']));                
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);

    }


}
