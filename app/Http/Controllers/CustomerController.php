<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    public function __construct(Request $request)
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {            
            $this->type = Auth::user()->type;            
            // dd($this->type);
            if($this->type != 'admin'){                
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
        $status = !empty($request->status)?$request->status:'all';
        $paginate = 10;
        $total = Customer::count();
        
        $totalActive = Customer::where('status', 1)->count();
        $totlInactive = Customer::where('status', 0)->count();
        $data = Customer::select('*');
        $totalResult = Customer::select('*');
        // if(!empty($search)){
            $data = $data->where(function($query) use ($search){
                $query->where('name', 'LIKE','%'.$search.'%')->orWhere('email','LIKE','%'.$search.'%')->orWhere('phone', 'LIKE', '%'.$search.'%')->orWhere('pan_no', 'LIKE', '%'.$search.'%')->orWhere('gst_no', 'LIKE', '%'.$search.'%')->orWhere('license_no', 'LIKE', '%'.$search.'%');
            });
            $totalResult = $totalResult->where(function($query) use ($search){
                $query->where('name', 'LIKE','%'.$search.'%')->orWhere('email','LIKE','%'.$search.'%')->orWhere('phone', 'LIKE', '%'.$search.'%')->orWhere('pan_no', 'LIKE', '%'.$search.'%')->orWhere('gst_no', 'LIKE', '%'.$search.'%')->orWhere('license_no', 'LIKE', '%'.$search.'%');
            });
        // }
        if($status == 'active'){
            $data = $data->where('status', 1);
            $totalResult = $totalResult->where('status', 1);
        } else if ($status == 'inactive'){
            $data = $data->where('status', 0);
            $totalResult = $totalResult->where('status', 0);
        }
        $data = $data->orderBy('id','desc')->paginate($paginate);
        $totalResult = $totalResult->count();

        $data = $data->appends([
            'search'=>$search,
            'status'=>$status,
            'page'=>$request->page
        ]);
        return view('customer.list', compact('data','totalResult','total','totalActive','totlInactive','status','search','paginate'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('customer.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {        
        // dd($request->all());
        
        $validator = Validator::make($request->all(),[
            'name' => 'required|max:100',
            'email' => 'regex:/(.+)@(.+)\.(.+)/i|max:100|unique:customers,email|nullable|required_without:phone',
            'phone' => 'numeric|digits_between:7,10|unique:customers,phone|nullable|required_without:email',
            'address' => 'required',
            'gst_no' => 'required|max:20',
            'pan_no' => 'required|max:20',
            'license_no' => 'required'
        ]);

        if(!$validator->fails()){
            $params = $request->except('_token');
            // dd($params);
            $params['created_at'] = date('Y-m-d H:i:s');
            $id = Customer::insertGetId($params);
            
            if (!empty($id)) {
                Session::flash('message', 'Customer created successfully');
                return redirect()->route('customer.list');
            } else {
                return redirect()->route('customer.add')->withInput($request->all());
            }
        } else {
            return redirect()->route('customer.add')->withErrors($validator)->withInput($request->all());
        }

        

        
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function show($idStr,$getQueryString='')
    {        
        try {
            $id = Crypt::decrypt($idStr);
            $data = Customer::find($id);
            return view('customer.detail', compact('data','id','getQueryString'));
        } catch ( DecryptException $e) {
            return abort(404);
        }
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function edit($idStr,$getQueryString='')
    {
        try {
            $id = Crypt::decrypt($idStr);
            $data = Customer::find($id);
            return view('customer.edit', compact('data','idStr','getQueryString'));
        } catch ( DecryptException $e) {
            return abort(404);
        }
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$idStr,$getQueryString='')
    {
        try {
            $id = Crypt::decrypt($idStr);
            $params = $request->except('_token');
            $params['updated_at'] = date('Y-m-d H:i:s');
            $data = Customer::where('id',$id)->update($params);
            // dd($data);
            if (!empty($data)) {
                Session::flash('message', 'Customer updated successfully');
                return redirect('/customer/list?'.$getQueryString);
                // return redirect()->route('customer.list');
            } else {
                return redirect()->route('customer.edit')->withInput($request->all());
            }
        } catch ( DecryptException $e) {
            return abort(404);
        }
        
    }

    /**
     * Change Status the specified customer from storage.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function toggle_status($idStr,$getQueryString='')
    {
        try {
            $id = Crypt::decrypt($idStr);
            $customer = Customer::find($id);
            $message = "";
            if($customer->status == 1){
                Customer::where('id',$id)->update(['status'=>0]);
                $message = "Customer deactivated successfully";
            } else {
                Customer::where('id',$id)->update(['status'=>1]);
                $message = "Customer activated successfully";
            }

            Session::flash('message', $message);
            if(!empty($getQueryString)){            
                return redirect('/customer/list?'.$getQueryString);
            }
            return redirect()->route('customer.list');
        } catch ( DecryptException $e) {
            return abort(404);
        }
        
    }
}
