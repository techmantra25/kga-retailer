<?php

namespace App\Http\Controllers;

use App\Models\ServiceCentre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;


class ServiceCentreController extends Controller
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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = !empty($request->search)?$request->search:'';
        $paginate = 20;
        $data = ServiceCentre::select('*');
        $totalResult = ServiceCentre::select('*');

        if(!empty($search)){
            $data = $data->where('name','LIKE','%'.$search.'%')->orWhere('email','LIKE','%'.$search.'%')->orWhere('phone','LIKE','%'.$search.'%');
            $totalResult = $totalResult->where('name','LIKE','%'.$search.'%')->orWhere('email','LIKE','%'.$search.'%')->orWhere('phone','LIKE','%'.$search.'%');
        }

        $data = $data->orderBy('id','desc')->paginate($paginate);
        $totalResult = $totalResult->count();
        return view('service-centre.list', compact('data','totalResult','paginate','search'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('service-centre.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required|max:100',
            'email' => 'regex:/(.+)@(.+)\.(.+)/i|max:100|unique:service_centers,email|nullable|required_without:phone',
            'phone' => 'numeric|digits_between:7,10|unique:service_centers,phone|nullable|required_without:email',
            'address' => 'required'
        ]);

        if(!$validator->fails()){
            $params = $request->except('_token');
            // dd($params);
            $params['created_at'] = date('Y-m-d H:i:s');
            $params['password'] = Hash::make('secret');
            $id = ServiceCentre::insertGetId($params);
            
            if (!empty($id)) {
                Session::flash('message', 'Service Centre created successfully');
                return redirect()->route('service-centre.list');
            } else {
                return redirect()->route('service-centre.add')->withInput($request->all());
            }
        } else {
            return redirect()->route('service-centre.add')->withErrors($validator)->withInput($request->all());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ServiceCentre  $serviceCentre
     * @return \Illuminate\Http\Response
     */
    public function show($idStr,$getQueryString='')
    {
        try {
            $id = Crypt::decrypt($idStr);
            $data = ServiceCentre::find($id);
            return view('service-centre.detail', compact('data','id','getQueryString'));
        } catch ( DecryptException $e) {
            return abort(404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ServiceCentre  $serviceCentre
     * @return \Illuminate\Http\Response
     */
    public function edit($idStr,$getQueryString='')
    {
        try {
            $id = Crypt::decrypt($idStr);
            $data = ServiceCentre::find($id);
            return view('service-centre.edit', compact('data','idStr','getQueryString'));
        } catch ( DecryptException $e) {
            return abort(404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ServiceCentre  $serviceCentre
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$idStr,$getQueryString='')
    {
        try {
            $id = Crypt::decrypt($idStr);
            $params = $request->except('_token');
            $params['updated_at'] = date('Y-m-d H:i:s');
            $data = ServiceCentre::where('id',$id)->update($params);
            // dd($data);
            if (!empty($data)) {
                Session::flash('message', 'Service Centre updated successfully');
                return redirect('/service-centre/list?'.$getQueryString);
            } else {
                return redirect()->route('service-centre.edit')->withInput($request->all());
            }
        } catch ( DecryptException $e) {
            return abort(404);
        }
    }

    /**
     * Toggle Status the specified resource from storage.
     *
     * @param  \App\Models\ServiceCentre  $serviceCentre
     * @return \Illuminate\Http\Response
     */
    public function toggle_status($idStr,$getQueryString='')
    {
        try {
            $id = Crypt::decrypt($idStr);
            $sc = ServiceCentre::find($id);
            $message = "";
            if($sc->status == 1){
                ServiceCentre::where('id',$id)->update(['status'=>0]);
                $message = "Service Centre deactivated successfully";
            } else {
                ServiceCentre::where('id',$id)->update(['status'=>1]);
                $message = "Service Centre activated successfully";
            }

            Session::flash('message', $message);
            if(!empty($getQueryString)){            
                return redirect('/service-centre/list?'.$getQueryString);
            }
            return redirect()->route('service-centre.list');
        } catch ( DecryptException $e) {
            return abort(404);
        }
        
    }

    /**
     * Change Password the specified resource from storage.
     *
     * @param  \App\Models\ServiceCentre  $serviceCentre
     * @return \Illuminate\Http\Response
     */
    public function change_password($idStr,$getQueryString='')
    {
        try {
            $id = Crypt::decrypt($idStr);
            $sc = ServiceCentre::find($id);
            
            return view('service-centre.changepassword', compact('id','idStr','sc','getQueryString'));
        } catch ( DecryptException $e) {
            return abort(404);
        }
        
    }

    /**
     * Save Password the specified resource from storage.
     *
     * @param  \App\Models\ServiceCentre  $serviceCentre
     * @return \Illuminate\Http\Response
     */
    public function save_password(Request $request,$idStr,$getQueryString='')
    {
        # save new password...
        try {
            $id = Crypt::decrypt($idStr);
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
            ServiceCentre::where('id',$id)->update($params);
            Session::flash('message', 'Password changed successfully');
            if(!empty($getQueryString)){            
                return redirect('/service-centre/list?'.$getQueryString);
            }
            return redirect()->route('service-centre.list');

        } catch ( DecryptException $e) {
            return abort(404);
        }
    }

}
