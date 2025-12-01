<?php

namespace App\Http\Controllers\ServicePartner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use File; 
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Models\ServicePartner;
use App\Models\Pincode;
use App\Models\ServicePartnerPincode;


class PincodeController extends Controller
{
    
    public function __construct(Request $request)
    {
        $this->middleware('auth:servicepartner');
    }

    public function index(Request $request)
    {
        # my pincodes...

        // echo 'My Pincodes';
        $search = !empty($request->search)?$request->search:'';
        $paginate = 20;
        $service_partner_id = Auth::user()->id;
        $data = ServicePartnerPincode::where('service_partner_id',$service_partner_id);
        $totalResult = ServicePartnerPincode::where('service_partner_id',$service_partner_id);

        if(!empty($search)){
            $data = $data->where('number', 'LIKE', '%'.$search.'%');
            $totalResult = $totalResult->where('number', 'LIKE', '%'.$search.'%');
        }

        $data = $data->orderBy('number','asc')->get();
        $totalResult = $totalResult->count();

        $service_partner = ServicePartner::find($service_partner_id);
        
        return view('servicepartnerweb.pincode.list', compact('data','totalResult','service_partner_id','service_partner','search'));
    }
}
