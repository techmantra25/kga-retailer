<?php

namespace App\Http\Controllers\ServicePartner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\ServicePartner;
use App\Models\Installation;
use App\Models\CustomerPointService;
use App\Models\Repair;
use App\Models\Maintenance;

class HomeController extends Controller
{
    //

    public function __construct(Request $request)
    {
        # code...
        $this->middleware('auth:servicepartner');
    }

    public function index(Request $request)
    {
        # code...
        $count_pending_installation = Installation::where('service_partner_id', Auth::user()->id)->where('is_closed', 0)->where('is_cancelled', 0)->count();
        $count_pending_repair = Repair::where('service_partner_id', Auth::user()->id)->where('is_closed', 0)->where('is_cancelled', 0)->count();
        $count_pending_customer_repair_point =CustomerPointService::where('assign_service_perter_id',Auth::user()->id)->where('status',0)->count();
		$count_pending_chimney_cleaning = Maintenance::where('service_partner_id',Auth::user()->id)
														->where('is_closed',0)
														->where('is_cancelled',0)
														->count();
        return view('servicepartnerweb.home', compact('count_pending_installation','count_pending_repair','count_pending_customer_repair_point','count_pending_chimney_cleaning'));
    }

    public function my_profile(Request $request)
    {
        # my profile...
        return view('servicepartnerweb.profile');
    }

    public function save_profile(Request $request)
    {
        # code...
        $request->validate([
            'company_name' => 'required|max:100',
            'person_name' => 'required|max:100'
        ]);

        $params = $request->except('_token');
        ServicePartner::where('id', Auth::user()->id)->update($params);

        Session::flash('message', 'Profile updated successfully');
        return redirect()->route('servicepartnerweb.myprofile'); 
    }

    public function change_password(Request $request)
    {
        # change password...
        return view('servicepartnerweb.password');
    }

    public function save_password(Request $request)
    {
        # code...

        $request->validate([
            'password' => 'min:6|required_with:password_confirmation|same:password_confirmation',
            'password_confirmation' => 'min:6'
        ]);

        $params = $request->except('_token');
        
        if(!empty($params['password'])){
            $params['password'] = Hash::make($params['password']);            
        } else {
            unset($params['password']);
        }
        
        unset($params['password_confirmation']);
        // dd($params);
        $data = ServicePartner::where('id', Auth::user()->id)->update($params);
        Session::flash('message', 'Password changed successfully');
        return redirect()->route('servicepartnerweb.changepassword'); 
    }
}
