<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Models\ServicePartner;
use App\Models\Payment;
use App\Models\Ledger;
use App\Models\Installation;
use App\Models\Repair;
use App\Models\CreditNote;
use App\Models\User;
use App\Models\AmcSubscription;
class AccountingController extends Controller
{
    public function __construct(Request $request)
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            
            if(Auth::user()->id == 8){
                abort(404);
            }            

            return $next($request);
        });
    }

    public function payment_list(Request $request)
    {
        # Payment List...
        $paginate = 20;
        $search = !empty($request->search)?$request->search:'';

        $data = Payment::select('*');
        $totalResult = Payment::select('*');

        if(!empty($search)){
            $data = $data->where('voucher_no', 'LIKE', '%'.$search.'%')->orWhereHas('service_partner', function($sp) use($search){
                $sp->where('person_name','LIKE','%'.$search.'%')->orWhere('company_name','LIKE','%'.$search.'%')->orWhere('email','LIKE','%'.$search.'%')->orWhere('phone','LIKE','%'.$search.'%');
            });
            $totalResult = $totalResult->where('voucher_no', 'LIKE', '%'.$search.'%')->orWhereHas('service_partner', function($sp) use($search){
                $sp->where('person_name','LIKE','%'.$search.'%')->orWhere('company_name','LIKE','%'.$search.'%')->orWhere('email','LIKE','%'.$search.'%')->orWhere('phone','LIKE','%'.$search.'%');
            });
        }

        $totalResult = $totalResult->count();
        $data = $data->orderBy('id','desc')->paginate($paginate);


        return view('accounting.payment-list', compact('data','totalResult','paginate','search'));
    }

    public function payment_add(Request $request)
    {
        # Payment Add...
        $service_partners = ServicePartner::where('is_default', 0)->where('status', 1)->orderBy('person_name')->get();
		$ho_sales = User::where('role_id',6)->orderBy('name')->get();
        return view('accounting.payment-add', compact('service_partners','ho_sales'));
    }

    public function payment_save(Request $request)
    {
		//dd($request->all());
        # Payment Save...
       /* $request->validate([
            'service_partner_id' => 'required',
            'amount' => 'required',
            'payment_mode' => 'required',
            'bank_name' => 'required_unless:payment_mode,cash',
            'chq_utr_no' => 'required_unless:payment_mode,cash'
        ],[
            'service_partner_id.required' => 'Please choose service partner',
            'amount' => 'Please add amount',
            'payment_mode.required' => 'Please add payment mode',
            'bank_name.required_unless' => 'Please add bank where entry is not cash',
            'chq_utr_no.required_unless' => 'Please add cheque or UTR number where entry is not cash'
        ]); */
		
		 // Common validation rules
		$rules = [
			'amount' => 'required|numeric',
			'payment_mode' => 'required',
			'bank_name' => 'required_unless:payment_mode,cash',
			'chq_utr_no' => 'required_unless:payment_mode,cash',
		];

		$messages = [
			'amount.required' => 'Please add amount',
			'payment_mode.required' => 'Please select payment mode',
			'bank_name.required_unless' => 'Please add bank name where entry is not cash',
			'chq_utr_no.required_unless' => 'Please add cheque or UTR number where entry is not cash',
		];

		// Conditional validation based on user_type
		if ($request->user_type == 'servicepartner') {
			$rules['service_partner_id'] = 'required';
			$messages['service_partner_id.required'] = 'Please choose a Service Partner';
		} elseif ($request->user_type == 'ho_sale') {
			$rules['ho_sale_id'] = 'required';
			$messages['ho_sale_id.required'] = 'Please choose a Ho Sale';
		} else {
			return back()->withErrors(['user_type' => 'Please select a valid user type'])->withInput();
		}
		
		
        $request->validate($rules, $messages);
		
        $params = $request->except('_token');
         //dd($params);

        unset($params['bank_name_hidden']);
        $params['created_at'] = date('Y-m-d H:i:s');
        $params['updated_at'] = date('Y-m-d H:i:s');
        $payment_id = Payment::insertGetId($params);
        $ledgerData = array(
            'type' => 'debit',
            'amount' => $params['amount'],
            'entry_date' => $params['entry_date'],
            'user_type' => 'servicepartner',
            'service_partner_id' => $params['service_partner_id'],
            'payment_id' => $payment_id,
            'purpose' => 'payment',
            'transaction_id' => $params['voucher_no'],
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        );
		
		  if ($request->user_type == 'servicepartner') {
            $ledgerData['user_type'] = 'servicepartner';
            $ledgerData['service_partner_id'] = $params['service_partner_id'];
        } else {
            $ledgerData['user_type'] = 'ho_sale';
            $ledgerData['user_id'] = $params['ho_sale_id'];
        }
		
        Ledger::insert($ledgerData);

        $message = "Payment Added Successfully. Please check ".$params['user_type']."'s ledger. ";
        Session::flash('message', $message);
        return redirect()->route('accounting.payment-list');

    }

    public function list_credit_note(Request $request)
    {
        $data = CreditNote::with('ho_sale')->orderBy('id', 'desc')->paginate(25);
        return view('accounting.creditnote-list', compact('data'));
    }

    public function add_credit_note(Request $request)
    {
        $service_partners = ServicePartner::where('is_default', 0)->where('status', 1)->orderBy('person_name')->get();
		$ho_sales = User::where('role_id',6)->orderBy('name')->get();
        return view('accounting.creditnote-add', compact('service_partners','ho_sales'));
    }

   /* public function save_credit_note(Request $request)
    {
        $request->validate([
            'service_partner_id' => 'required|exists:service_partners,id',
            'amount' => 'required',
            'call_type' => 'required',
            'call_no' => 'required'
        ]);
        $params = $request->except('_token');
        $installation_id = $repair_id = null;
        if($params['call_type'] == 'installation'){
            $checkInstallation = Installation::where('unique_id', $params['call_no'])->first();
            if(!empty($checkInstallation)){
                if($checkInstallation->service_partner_id == $params['service_partner_id']){
                    $installation_id = $checkInstallation->id;
                } else {
                    return redirect()->back()->withErrors(['call_no' => 'Wrong installation call no for the service partner'])->withInput();
                }
            } else {
                return redirect()->back()->withErrors(['call_no' => 'Invalid installation call no'])->withInput();
            }
        } else if($params['call_type'] == 'repair'){
            $checkRepair = Repair::where('unique_id', $params['call_no'])->first();
            if(!empty($checkRepair)){
                if($checkRepair->service_partner_id == $params['service_partner_id']){
                    $repair_id = $checkRepair->id;
                } else {
                    return redirect()->back()->withErrors(['call_no' => 'Wrong repair call no for the service partner'])->withInput();
                }
            } else {
                return redirect()->back()->withErrors(['call_no' => 'Invalid repair call no'])->withInput();
            }
        }

        $params['installation_id'] = $installation_id;
        $params['repair_id'] = $repair_id;
        $params['created_by'] = Auth::user()->id;
        $params['created_at'] = date('Y-m-d H:i:s');
        // dd($params);
        $id = CreditNote::insertGetId($params);
        
        $ledgerArr = array(
            'type' => 'credit',
            'amount' => $params['amount'],
            'entry_date' => $params['entry_date'],
            'user_type' => 'servicepartner',
            'service_partner_id' => $params['service_partner_id'],
            'transaction_id' => $params['transaction_id'],
            'purpose' => 'credit_note',
            'credit_note_id' => $id,
            'created_at' => date('Y-m-d H:i:s')
        );
        Ledger::insert($ledgerArr);
        // dd($ledgerArr);

        $message = "Credit Note Added Successfully. Please check it on ledger also. ";
        Session::flash('message', $message);
        return redirect()->route('accounting.payment-list');
    }  */
	
	
		public function save_credit_note(Request $request)
{
    try {
        //  Common validation
        $rules = [
            'amount' => 'required|numeric|min:1',
            'entry_date' => 'required|date',
            'user_type' => 'required|in:service_partner,ho_sale',
        ];

        //  Dynamic validation rules
        if ($request->user_type == 'service_partner') {
            $rules = array_merge($rules, [
                'service_partner_id' => 'required|exists:service_partners,id',
                'call_type' => 'required|in:installation,repair',
                'call_no' => 'required',
            ]);
        } elseif ($request->user_type == 'ho_sale') {
            $rules = array_merge($rules, [
                'ho_sale_id' => 'required|exists:users,id',
                'call_type' => 'required|in:amc',
                'amc_unique_number' => 'required',
            ]);
        }

        $request->validate($rules);

        $params = $request->except('_token');
        $installation_id = $repair_id = $amc_id = null;

        //  INSTALLATION
        if ($params['call_type'] == 'installation') {
            $checkInstallation = Installation::where('unique_id', $params['call_no'])->first();

            if ($checkInstallation) {
                if ($checkInstallation->service_partner_id == $params['service_partner_id']) {
                    $installation_id = $checkInstallation->id;
                } else {
                    return back()->withErrors(['call_no' => 'Wrong installation call no for the selected service partner'])->withInput();
                }
            } else {
                return back()->withErrors(['call_no' => 'Invalid installation call number'])->withInput();
            }
        }

        //  REPAIR
        elseif ($params['call_type'] == 'repair') {
            $checkRepair = Repair::where('unique_id', $params['call_no'])->first();

            if ($checkRepair) {
                if ($checkRepair->service_partner_id == $params['service_partner_id']) {
                    $repair_id = $checkRepair->id;
                } else {
                    return back()->withErrors(['call_no' => 'Wrong repair call no for the selected service partner'])->withInput();
                }
            } else {
                return back()->withErrors(['call_no' => 'Invalid repair call number'])->withInput();
            }
        }

        //  AMC — only for Ho Sale (role_id = 6)
        elseif ($params['call_type'] == 'amc') {

            if ($request->user_type == 'service_partner') {
                return back()->withErrors(['call_type' => 'AMC Credit Notes are allowed only for Ho Sale users.'])->withInput();
            }

            // AMC check
            $checkAmc = AmcSubscription::where('amc_unique_number', $params['amc_unique_number'])->first();

            if (!$checkAmc) {
                return back()->withErrors(['amc_unique_number' => 'Invalid AMC Unique Number'])->withInput();
            }

            // Ho Sale user check
            $hoSale = User::where('id', $params['ho_sale_id'])->where('role_id', 6)->first();

            if (!$hoSale) {
                return back()->withErrors(['ho_sale_id' => 'Selected user is not authorized as Ho Sale'])->withInput();
            }

            $amc_id = $checkAmc->id;
        }

        //  Save Credit Note
        $params['installation_id'] = $installation_id;
        $params['repair_id'] = $repair_id;
        $params['amc_id'] = $amc_id;
        $params['created_by'] = Auth::id();
        $params['created_at'] = now();

        $creditNoteId = CreditNote::insertGetId($params);

        //  Ledger Entry
        $ledgerArr = [
            'type' => 'credit',
            'amount' => $params['amount'],
            'entry_date' => $params['entry_date'],
            'transaction_id' => $params['transaction_id'],
            'purpose' => 'credit_note',
            'credit_note_id' => $creditNoteId,
            'created_at' => now(),
        ];

        if ($request->user_type == 'service_partner') {
            $ledgerArr['user_type'] = 'servicepartner';
            $ledgerArr['service_partner_id'] = $params['service_partner_id'];
        } else {
            $ledgerArr['user_type'] = 'ho_sale';
            $ledgerArr['user_id'] = $params['ho_sale_id'];
        }

        Ledger::insert($ledgerArr);

        //  Success Message
        Session::flash('message', 'Credit Note Added Successfully. Please check it on ledger also.');
        return redirect()->route('accounting.payment-list');
    }

    //  Catch any exception
    catch (\Exception $e) {
        \Log::error('Error saving Credit Note: ' . $e->getMessage());
		//dd($e->getMessage());
        return back()->withErrors(['error' => 'Something went wrong while saving Credit Note. Please try again.'])->withInput();
    }
}


    
}
