<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class SupplierController extends Controller
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
     * Display a listing of the supplier.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = !empty($request->search)?$request->search:'';
        $status = !empty($request->status)?$request->status:'all';
        $paginate = 10;
        $total = Supplier::count();
        
        $totalActive = Supplier::where('status', 1)->count();
        $totlInactive = Supplier::where('status', 0)->count();
        $data = Supplier::select('*');
        $totalResult = Supplier::select('id');
        if(!empty($search)){
            $data = $data->where(function($query) use ($search){
                $query->where('name', 'LIKE','%'.$search.'%')->orWhere('public_name','LIKE','%'.$search.'%')->orWhere('email','LIKE','%'.$search.'%')->orWhere('phone', 'LIKE', '%'.$search.'%');
            });
            $totalResult = $totalResult->where(function($query) use ($search){
                $query->where('name', 'LIKE','%'.$search.'%')->orWhere('public_name','LIKE','%'.$search.'%')->orWhere('email','LIKE','%'.$search.'%')->orWhere('phone', 'LIKE', '%'.$search.'%');
            });
        }
        if($status == 'active'){
            $data = $data->where('status', 1);
            $totalResult = $totalResult->where('status', 1);
        } else if ($status == 'inactive'){
            $data = $data->where('status', 0);
            $totalResult = $totalResult->where('status', 0);
        }
        $data = $data->orderBy('id','desc')->paginate($paginate);
        $totalResult = $totalResult->count();

        // dd($data);

        $data = $data->appends([
            'search'=>$search,
            'status'=>$status,
            'page'=>$request->page
        ]);
        return view('supplier.list', compact('data','totalResult','total','totalActive','totlInactive','status','search','paginate'));
    }

    /**
     * Show the form for creating a new supplier.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('supplier.add');
    }

    /**
     * Store a newly created supplier in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'public_name' => 'required|string|max:100',
            'email' => 'regex:/(.+)@(.+)\.(.+)/i|max:100|unique:suppliers,email|nullable|required_without:phone',
            'phone' => 'numeric|digits_between:7,10|unique:suppliers,phone|nullable|required_without:email',
            'address' => 'required',
            'state' => 'required|max:100',
            'city' => 'required|max:100',
            'pin' => 'required|max:10'         
        ]);

        $params = $request->except('_token');       
        $params['is_inside'] = ((isset($params['is_inside'])) && ($params['is_inside'] == 'true')) ? 1: 0;
        // dd($params);
        $params['created_at'] = date('Y-m-d H:i:s');
        $id = Supplier::insertGetId($params);
        
        if (!empty($id)) {
            Session::flash('message', 'Supplier created successfully');
            return redirect()->route('supplier.list');
        } else {
            return redirect()->route('supplier.add')->withInput($request->all());
        }
    }

    /**
     * Display the specified supplier.
     *
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function show($idStr,$getQueryString='')
    {
        try {
            $id = Crypt::decrypt($idStr);
            $data = Supplier::find($id);
            return view('supplier.detail', compact('data','id','getQueryString'));
        } catch ( DecryptException $e) {
            return abort(404);
        }
    }

    /**
     * Show the form for editing the specified supplier.
     *
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function edit($idStr,$getQueryString='')
    {
        try {
            $id = Crypt::decrypt($idStr);
            $data = Supplier::find($id);
            return view('supplier.edit', compact('data','idStr','getQueryString'));
        } catch ( DecryptException $e) {
            return abort(404);
        }
    }

    /**
     * Update the specified supplier in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,  $idStr,$getQueryString='')
    {
        try {
            $id = Crypt::decrypt($idStr);
            $request->validate([
                'name' => 'required|string|max:100',
                'public_name' => 'required|string|max:100',
                'email' => 'regex:/(.+)@(.+)\.(.+)/i|max:100|unique:users,email,'.$id.'|nullable|required_without:phone',
                'phone' => 'numeric|digits_between:7,10|unique:users,phone,'.$id.'|nullable|required_without:email',
                'address' => 'required',
                'state' => 'required|max:100',
                'city' => 'required|max:100',
                'pin' => 'required|max:10'         
            ]);
    
            $params = $request->except('_token');       
            $params['is_inside'] = ((isset($params['is_inside'])) && ($params['is_inside'] == 'true')) ? 1: 0;
            // dd($params);
            $params['updated_at'] = date('Y-m-d H:i:s');
            $data = Supplier::where('id',$id)->update($params);
            
            if (!empty($data)) {
                Session::flash('message', 'Supplier updated successfully');
                return redirect('/supplier/list?'.$getQueryString);
                // return redirect()->route('supplier.list');
            } else {
                return redirect()->route('supplier.edit',$id)->withInput($request->all());
            }
        } catch ( DecryptException $e) {
            return abort(404);
        }
        
    }

    /**
     * Toggle Status the specified supplier from storage.
     *
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function toggle_status($idStr,$getQueryString='')
    {
        try {
            $id = Crypt::decrypt($idStr);
            $customer = Supplier::find($id);
            $message = "";
            if($customer->status == 1){
                Supplier::where('id',$id)->update(['status'=>0]);
                $message = "Supplier deactivated successfully";
            } else {
                Supplier::where('id',$id)->update(['status'=>1]);
                $message = "Supplier activated successfully";
            }

            Session::flash('message', $message);
            if(!empty($getQueryString)){            
                return redirect('/supplier/list?'.$getQueryString);
            }
            return redirect()->route('supplier.list');
        } catch ( DecryptException $e) {
            return abort(404);
        }
        
    }
}
