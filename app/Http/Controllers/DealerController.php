<?php

namespace App\Http\Controllers;

use App\Models\Dealer;
use App\Models\Branch;
use App\Models\DealerEmployee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class DealerController extends Controller
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
    public function branchList(Request $request, $idStr, $getQueryString = '') 
    {
        $paginate = 25;
        $search = $request->search ?? '';
        
        try {
            $id = Crypt::decrypt($idStr);
            $dealer = Dealer::find($id);
            $query = Branch::where('dealer_id', $id)->orderBy('name', 'asc');

            if (!empty($search)) {
                $query->where('name', 'LIKE', '%' . $search . '%');
            }

            $data = $query->paginate($paginate)->appends([
                'search' => $search,
                'page' => $request->page
            ]);

            $total = Branch::where('dealer_id', $id)->count();

            return view('dealer.branch_list', compact('data', 'id', 'getQueryString', 'total', 'search','dealer'));
        } catch (DecryptException $e) {
            return abort(404);
        }
    }

    public function dealerEmployee(Request $request, $idStr, $getQueryString = '')
    {
        $paginate = 25;
        $search = $request->search ?? '';
        $dealer_id = $request->dealer_id ?? '';

        try {
            $id = Crypt::decrypt($idStr);
            $dealer = Dealer::find($id);
            $query = DealerEmployee::where('dealer_id', $id);

            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'LIKE', '%' . $search . '%')
                      ->orWhere('phone', 'LIKE', '%' . $search . '%');
                });
            }

            $data = $query->paginate($paginate)->appends([
                'search' => $search,
                'page' => $request->page
            ]);

            $total = DealerEmployee::where('dealer_id', $id)->count();

            return view('dealer.dealer_employee_list', compact('dealer', 'id', 'data', 'total', 'search', 'getQueryString'));
        } catch (DecryptException $e) {
            return abort(404);
        }
    }
    public function searchBranch(Request $request)
    {  
        $query = $request->input('query');
        $branches = Branch::where('name', 'LIKE', "%{$query}%")->get();
        return response()->json($branches);
    }

    public function dealerEmployeeAdd($idStr, $getQueryString = ''){
        try {
            $id = Crypt::decrypt($idStr);
            $dealer = Dealer::find($id);
            return view('dealer.add_dealer_employee', compact('id','dealer'));
         } catch (DecryptException $e) {
            return abort(404);
        }
    }
    public function dealerEmployeeStore(Request $request){
         $validator = Validator::make($request->all(),[
                    'name' => 'required|max:100',
                    'phone' => 'numeric|digits_between:10,11|unique:dealer_employee,phone|required',
                    'branch_id' => 'required',
                    'password'=> 'required|min:5|max:10',
                    'cpassword'=>'required|same:password'
                ]);

          if(!$validator->fails()){
            $params = $request->except('_token','cpassword','branch_name');
            $params['created_at'] = date('Y-m-d H:i:s');
            $id = DealerEmployee::insertGetId($params);
            if (!empty($id)) {
                Session::flash('message', 'Employee created successfully');
                return redirect()->route('dealers.dealer-employee-list',Crypt::encrypt($request->dealer_id));
            } else {
                return redirect()->route('dealers.dealer-employee-add',Crypt::encrypt($request->dealer_id))->withInput($request->all());
            }
        } else {
            return redirect()->route('dealers.dealer-employee-add',Crypt::encrypt($request->dealer_id))->withErrors($validator)->withInput($request->all());
        }
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        // dd('Hi');
        $search = !empty($request->search)?$request->search:'';
        $status = !empty($request->status)?$request->status:'all';
        $paginate = 10;
        $total = Dealer::count();
        
        $totalActive = Dealer::where('status', 1)->count();
        $totlInactive = Dealer::where('status', 0)->count();
        $data = Dealer::select('*');
        $totalResult = Dealer::select('*');
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
        return view('dealer.list', compact('data','totalResult','total','totalActive','totlInactive','status','search','paginate'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('dealer.add');
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
            'email' => 'regex:/(.+)@(.+)\.(.+)/i|max:100|unique:dealers,email|nullable|required_without:phone',
            'phone' => 'numeric|digits_between:7,10|unique:dealers,phone|nullable|required_without:email',
            'address' => 'required',
            'gst_no' => 'required|max:20',
            'pan_no' => 'required|max:20',
            'license_no' => 'required'
        ]);

        if(!$validator->fails()){
            $params = $request->except('_token');
            // dd($params);
            $params['created_at'] = date('Y-m-d H:i:s');
            $params['password'] = Hash::make('secret');
            $id = Dealer::insertGetId($params);
            if (!empty($id)) {
                // Create a Employee associated same  with this dealer credentials
                $params['created_at'] = date('Y-m-d H:i:s');
                $params['password'] = 'secret';
                $params['dealer_id'] = $id;
                $params['from_where'] = 1;

                unset($params['email']);
                unset($params['address']);
                unset($params['gst_no']);
                unset($params['pan_no']);
                unset($params['license_no']);
                $emp_id =DealerEmployee::insertGetId($params);
            
                        }
            if (!empty($id) && !empty($emp_id)) {
                Session::flash('message', 'Dealer created successfully');
                return redirect()->route('dealers.list');
            } else {
                return redirect()->route('dealers.add')->withInput($request->all());
            }
        } else {
            return redirect()->route('dealers.add')->withErrors($validator)->withInput($request->all());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Dealer  $dealer
     * @return \Illuminate\Http\Response
     */
    public function show($idStr,$getQueryString='')
    {
        try {
            $id = Crypt::decrypt($idStr);
            $data = Dealer::find($id);
            return view('dealer.detail', compact('data','id','getQueryString'));
        } catch ( DecryptException $e) {
            return abort(404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Dealer  $dealer
     * @return \Illuminate\Http\Response
     */
    public function edit($idStr,$getQueryString='')
    {
        try {
            $id = Crypt::decrypt($idStr);
            $data = Dealer::find($id);
            return view('dealer.edit', compact('data','idStr','getQueryString'));
        } catch ( DecryptException $e) {
            return abort(404);
        }
    }
    public function dealerEmployeeEdit($idStr,$getQueryString='')
    {
        try {
            $id = Crypt::decrypt($idStr);
            $data = DealerEmployee::with('branchData')->find($id);
            return view('dealer.dealer_employee_edit', compact('data','idStr','getQueryString'));
        } catch ( DecryptException $e) {
            return abort(404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Dealer  $dealer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$idStr,$getQueryString='')
    {
        try {
            $id = Crypt::decrypt($idStr);
            $params = $request->except('_token');
            $params['updated_at'] = date('Y-m-d H:i:s');
            $data = Dealer::where('id',$id)->update($params);
            // dd($data);
            if (!empty($data)) {
                Session::flash('message', 'Dealer updated successfully');
                return redirect('/dealers/list?'.$getQueryString);
            } else {
                return redirect()->route('dealers.edit')->withInput($request->all());
            }
        } catch ( DecryptException $e) {
            return abort(404);
        }
    }
    public function dealerEmployeeUpdate(Request $request, $idStr, $getQueryString = '')
    {
        try {
            $id = Crypt::decrypt($idStr);
        } catch (DecryptException $e) {
            return abort(404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|max:100',
            'phone' => 'required|numeric|digits_between:10,11|unique:dealer_employee,phone,' . $id,
            'branch_id' => 'required',
            'password' => 'required|min:5|max:10',
            'cpassword' => 'required|same:password'
        ]);

        if ($validator->fails()) {
            return redirect()->route('dealers.dealer-employee-edit', $idStr)
                             ->withErrors($validator)
                             ->withInput();
        }

        $params = $request->except('_token','cpassword','branch_name');
        $params['updated_at'] = now();

        $data = DealerEmployee::where('id', $id)->update($params);

        if ($data) {
            Session::flash('message', 'Employee updated successfully');
            return redirect()->route('dealers.dealer-employee-list',Crypt::encrypt($request->dealer_id));
        } else {
            Session::flash('error', 'Employee update failed');
            return redirect()->route('dealers.dealer-employee-edit', $idStr)->withInput();
        }
    }



   /**
     * Change Status the specified dealer from storage.
     *
     * @param  \App\Models\Customer  $dealer
     * @return \Illuminate\Http\Response
     */
    public function toggle_status($idStr,$getQueryString='')
    {
        try {
            $id = Crypt::decrypt($idStr);
            $dealer = Dealer::find($id);
            $message = "";
            if($dealer->status == 1){
                Dealer::where('id',$id)->update(['status'=>0]);
                $message = "Dealer deactivated successfully";
            } else {
                Dealer::where('id',$id)->update(['status'=>1]);
                $message = "Dealer activated successfully";
            }

            Session::flash('message', $message);
            if(!empty($getQueryString)){            
                return redirect('/dealers/list?'.$getQueryString);
            }
            return redirect()->route('dealers.list');
        } catch ( DecryptException $e) {
            return abort(404);
        }
        
    }
    public function employee_toggle_status($idStr,$getQueryString='')
    {
        try {
            $id = Crypt::decrypt($idStr);
            $dealer = DealerEmployee::find($id);
            $message = "";
            if($dealer->status == 1){
                DealerEmployee::where('id',$id)->update(['status'=>0]);
                $message = "Employee deactivated successfully";
            } else {
                DealerEmployee::where('id',$id)->update(['status'=>1]);
                $message = "employee activated successfully";
            }

            Session::flash('message', $message);
            return redirect()->route('dealers.dealer-employee-list',Crypt::encrypt($dealer->dealer_id));
        } catch ( DecryptException $e) {
            return abort(404);
        }
        
    }
}
