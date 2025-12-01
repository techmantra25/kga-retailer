<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use App\Models\ServicePartner;
use App\Models\Changelog;
use App\User;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('guest:servicepartner')->except('logout');
    }

    public function masterLogin(Request $request)
    {
        $this->validate($request, [
            'email'   => 'required|email',
            'password' => 'required|min:6'
        ]);

        $checkUserActive = User::where('email',$request->email)->first();

        if(!empty($checkUserActive)){
            if(empty($checkUserActive->status)){
                return back()->withInput($request->only('email', 'remember'))->withErrors(['email' => 'User is inactive']);
            }
        }

        if (Auth::attempt($request->only(['email','password']), $request->get('remember'))) {
            

            $browser_name = isset($request->browser_name)?$request->browser_name:NULL;
            $navigator_useragent = isset($request->navigator_useragent)?$request->navigator_useragent:NULL;

            $params['role_name'] = $checkUserActive->role->name;
            $params['name'] = Auth::user()->name;
            $params['email'] = $request->email;
            addChangeLog(Auth::user()->id,$request->ip(),'user_login',$browser_name,$navigator_useragent,$params);
            
            
            return redirect()->intended('/home');
        }

        return back()->withInput($request->only('email', 'remember'))->withErrors(['email' => 'Wrong Login Credential.']);
    }



    /* +++++++++++++++++ Service Partner Authentication ++++++++++++++++++++ */

    public function showServicePartnerLoginForm()
    {
        // dd('Hi');
        return view('auth.login', ['route' => route('servicepartnerweb.login'), 'title'=>'Service Partner']);
    }

    public function servicepartnerLogin(Request $request)
    {
        $this->validate($request, [
            'email'   => 'required|email',
            'password' => 'required|min:6'
        ]);

        $checkUserActive = ServicePartner::where('email',$request->email)->first();

        if(!empty($checkUserActive)){
            if(empty($checkUserActive->status)){
                return back()->withInput($request->only('email', 'remember'))->withErrors(['email' => 'User is inactive']);
            }
        }

        if (Auth::guard('servicepartner')->attempt($request->only(['email','password']), $request->get('remember'))) {
            // dd('Hii');
            return redirect()->intended('/servicepartnerweb/dashboard');
        }

        return back()->withInput($request->only('email', 'remember'))->withErrors(['email' => 'Wrong Login Credential.']);
    }

}
