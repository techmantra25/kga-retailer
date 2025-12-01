<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\User;
use App\Models\Role;
use App\Models\Changelog;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class ManagerController extends Controller
{
    public function __construct(Request $request)
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {            
            $this->type = Auth::user()->type;            
            // dd($this->type);
            if($this->type != 'admin'){                
                abort(401);                
            }

            return $next($request);
        });
    }
    /**
     * Display a listing of the manager.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = !empty($request->search)?$request->search:'';
        $status = !empty($request->status)?$request->status:'all';
        $paginate = 10;
        $total = User::where('type','manager')->count();
        
        $totalActive = User::where('type','manager')->where('status', 1)->count();
        $totlInactive = User::where('type','manager')->where('status', 0)->count();
        $data = User::select('*')->where('type','manager');
        $totalResult = User::where('type','manager');
        // if(!empty($search)){
            $data = $data->where(function($query) use ($search){
                $query->where('name', 'LIKE','%'.$search.'%')->orWhere('email','LIKE','%'.$search.'%')->orWhere('phone', 'LIKE', '%'.$search.'%');
            });
            $totalResult = $totalResult->where(function($query) use ($search){
                $query->where('name', 'LIKE','%'.$search.'%')->orWhere('email','LIKE','%'.$search.'%')->orWhere('phone', 'LIKE', '%'.$search.'%');
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
        return view('manager.list', compact('data','totalResult','total','totalActive','totlInactive','status','search','paginate'));
    }

    /**
     * Show the form for creating a new manager.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {        
        $roles = Role::whereNotIn('id',[1,3])->get();
        return view('manager.add', compact('roles'));
    }

    /**
     * Store a newly created manager in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'name' => 'required|string|max:100',            
            'email' => 'regex:/(.+)@(.+)\.(.+)/i|max:100|unique:users,email|required',
            // 'phone' => 'numeric|digits_between:7,10|unique:users,phone|nullable|required_without:email',    
            'password' => 'min:6|required_with:password_confirmation|same:password_confirmation',
            'password_confirmation' => 'min:6'
        ],
        [
            'role_id.required' => 'Please select role'
        ]);

        $params = $request->except('_token');
        $browser_name = isset($params['browser_name'])?$params['browser_name']:NULL;
        $navigator_useragent = isset($params['navigator_useragent'])?$params['navigator_useragent']:NULL;
        unset($params['password_confirmation']);
        unset($params['browser_name']);
        unset($params['navigator_useragent']);
        $params['password'] = Hash::make($params['password']);
        $params['created_at'] = date('Y-m-d H:i:s');
        $amcIncentive = User::where('role_id', $params['role_id'])
            ->where('amc_incentive', '>', 0)
            ->value('amc_incentive') ?? 0;
        $params['amc_incentive'] = $amcIncentive;
        $id = User::insertGetId($params);

        $role_id = $params['role_id'];

        $role_name = getSingleAttributeTable('roles','id',$role_id,'name');
        $params['role_name'] = $role_name;
        $action_type = $role_name.' create';
        addChangeLog(Auth::user()->id,$request->ip(),$action_type,$browser_name,$navigator_useragent,$params);
        
        
        if (!empty($id)) {
            Session::flash('message', 'Manager created successfully');
            return redirect()->route('manager.list');
        } else {
            return redirect()->route('manager.add')->withInput($request->all());
        }
    }

    /**
     * Display the specified manager.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($idStr,$getQueryString='')
    {
        try {
            $id = Crypt::decrypt($idStr);
            $data = User::find($id);
            return view('manager.detail', compact('data','id','getQueryString'));
        } catch ( DecryptException $e) {
            return abort(404);
        }
    }

    /**
     * Show the form for editing the specified manager.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($idStr,$getQueryString='')
    {
        try {
            $id = Crypt::decrypt($idStr);
            $data = User::find($id);
            $roles = Role::whereNotIn('id',[1,3])->get();
            return view('manager.edit', compact('data','id','idStr','getQueryString','roles'));
        } catch ( DecryptException $e) {
            return abort(404);
        }
    }

    /**
     * Update the specified manager in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$idStr,$getQueryString='')
    {
        try {
            $id = Crypt::decrypt($idStr);
            $request->validate([
                'name' => 'required|string|max:100',
                'email' => 'regex:/(.+)@(.+)\.(.+)/i|max:100|unique:users,email,'.$id.'|required',
                // 'phone' => 'numeric|unique:users,phone,'.$id.'|nullable|required_without:email' ,
                'password' => 'nullable|min:6|required_with:password_confirmation|same:password_confirmation',
                'password_confirmation' => 'nullable|min:6'           
            ]);
    
            $params = $request->except('_token');
            
            if(!empty($params['password'])){
                $params['password'] = Hash::make($params['password']);            
            } else {
                unset($params['password']);
            }

            $browser_name = isset($params['browser_name'])?$params['browser_name']:NULL;
            $navigator_useragent = isset($params['navigator_useragent'])?$params['navigator_useragent']:NULL;
            unset($params['browser_name']);
            unset($params['navigator_useragent']);
            
            unset($params['password_confirmation']);
            $params['updated_at'] = date('Y-m-d H:i:s');
            
            $amcIncentive = User::where('role_id', $params['role_id'])
            ->where('amc_incentive', '>', 0)
            ->value('amc_incentive') ?? 0;
            $params['amc_incentive'] = $amcIncentive;
            $data = User::where('id',$id)->update($params);

            $role_id = $params['role_id'];
            $role_name = getSingleAttributeTable('roles','id',$role_id,'name');
            $params['role_name'] = $role_name;
            $action_type = $role_name.' update';
            addChangeLog(Auth::user()->id,$request->ip(),$action_type,$browser_name,$navigator_useragent,$params);
            
            if (!empty($data)) {
                Session::flash('message', 'Manager updated successfully');
                return redirect('/manager/list?'.$getQueryString);
                // return redirect()->route('manager.list');
            } else {
                return redirect()->route('manager.edit',$id)->withInput($request->all());
            }
        } catch ( DecryptException $e) {
            return abort(404);
        }
    }

    /**
     * Toggle Status the specified manager from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function toggle_status($idStr,$getQueryString='')
    {
        try {
            $id = Crypt::decrypt($idStr);
            $customer = User::find($id);
            $message = "";
            if($customer->status == 1){
                User::where('id',$id)->update(['status'=>0]);
                $message = "Manager deactivated successfully";
            } else {
                User::where('id',$id)->update(['status'=>1]);
                $message = "Manager activated successfully";
            }
    
            Session::flash('message', $message);
            if(!empty($getQueryString)){            
                return redirect('/manager/list?'.$getQueryString);
            }
            return redirect()->route('manager.list');
        } catch ( DecryptException $e) {
            return abort(404);
        }
    }
}
