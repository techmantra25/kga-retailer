<?php

namespace App\Http\Controllers;

use App\Models\ServicePartner;
use App\Models\Pincode;
use App\Models\ServicePartnerPincode;
use App\Models\Installation;
use App\Models\Repair;
use App\Models\Settings;
use App\Models\CloseInstallation;
use App\Models\ServicePartnerCharge;
use App\Models\Changelog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use File; 
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Auth;

class ServicePartnerController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
   
    public function index(Request $request)
    {
        if(Auth::user()->id == 8){
            abort(404);
        }  else {

            $search = !empty($request->search)?$request->search:'';
            $status = !empty($request->status)?$request->status:'';
            $type = !empty($request->type)?$request->type:'';
            $paginate = 20;
            $total = ServicePartner::where('is_default', 0)->count();
            
            $totalActive = ServicePartner::where('is_default', 0)->where('status', 1)->count();
            $totlInactive = ServicePartner::where('is_default', 0)->where('status', 0)->count();
            $data = ServicePartner::select('*')->with('pincodes');
            $totalResult = ServicePartner::select('id');
            if(!empty($status)){
                if($status == 'active'){
                    $data = $data->where('status', 1);
                    $totalResult = $totalResult->where('status', 1);
                } else if ($status == 'inactive'){
                    $data = $data->where('status', 0);
                    $totalResult = $totalResult->where('status', 0);
                }
            }
            
            if(!empty($search)){
                $data = $data->where(function($query) use ($search){
                    $query->where('person_name', 'LIKE','%'.$search.'%')->orWhere('company_name','LIKE','%'.$search.'%')->orWhere('email','LIKE','%'.$search.'%')->orWhere('phone', 'LIKE', '%'.$search.'%')->orWhereHas('pincodes', function ($q) use ($search) {
                        $q->where('number', 'LIKE','%'.$search.'%');
                    });
                });
                $totalResult = $totalResult->where(function($query) use ($search){
                    $query->where('person_name', 'LIKE','%'.$search.'%')->orWhere('company_name','LIKE','%'.$search.'%')->orWhere('email','LIKE','%'.$search.'%')->orWhere('phone', 'LIKE', '%'.$search.'%')->orWhereHas('pincodes', function ($q) use ($search) {
                        $q->where('number', 'LIKE','%'.$search.'%');
                    });
                });
            }

            if(!empty($type)){
                $data = $data->where('type', $type);
                $totalResult = $totalResult->where('type', $type);
            }
            
            $data = $data->where('is_default', 0)->orderBy('id','desc')->paginate($paginate);
            $totalResult = $totalResult->where('is_default', 0)->count();

            $data = $data->appends([
                'search'=>$search,
                'type' => $type,
                'status'=>$status,
                'page'=>$request->page
            ]);

            // dd($data);
            return view('servicepartner.list', compact('data','totalResult','total','totalActive','totlInactive','status','search','type','paginate'));

        }
        
    }

    public function create()
    {
        if(Auth::user()->id == 8){
            abort(404);
        } else {
            return view('servicepartner.add');
        }
        
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required',
            'company_name' => 'required|max:100',
            'person_name' => 'required|max:100',
            'email' => 'regex:/(.+)@(.+)\.(.+)/i|max:100|unique:service_partners,email|nullable|required_without:phone',
            'phone' => 'numeric|digits_between:7,10|unique:service_partners,phone|nullable|required_without:email',
            // 'pan_no' => 'required',
            // 'aadhaar_no' => 'required',
            // 'gst_no' => 'required',
            // 'license_no' => 'required',
            'address' => 'required',
            'state' => 'required',
            'city' => 'required',
            // 'salary' => 'nullable|required_unless:type,1',
            // 'repair_charge' => 'nullable|required_unless:type,1',
            // 'travelling_allowance' => 'nullable|required_unless:type,1',
        ],[
            // 'salary.required_unless' => 'The salary field is required',
            // 'repair_charge.required_unless' => 'The repair charge is required',
            // 'travelling_allowance.required_unless' => 'The travelling allowance field is required',
        ]);

        $params = $request->except('_token');

        $uplaod_base_url_prefix = config('app.uplaod_base_url_prefix');

        if(!empty($params['image'])){
            $upload_path = $uplaod_base_url_prefix."uploads/service-partner/";
            $image = $params['image'];
            $imageName = time() . "." . $image->getClientOriginalName();
            $image->move($upload_path, $imageName);
            $uploadedImage = $imageName;
            $params['photo'] = $upload_path . $uploadedImage;
            unset($params['image']);
        } else {
            $params['photo'] = null;
        }

        $params['password'] = Hash::make('secret');
        $params['created_at'] = date('Y-m-d H:i:s');

        $browser_name = isset($params['browser_name'])?$params['browser_name']:NULL;
        $navigator_useragent = isset($params['navigator_useragent'])?$params['navigator_useragent']:NULL;
        unset($params['browser_name']);
        unset($params['navigator_useragent']);

        $id = ServicePartner::insertGetId($params);

        ### Changelog ###
        addChangeLog(Auth::user()->id,$request->ip(),'service_partner_create',$browser_name,$navigator_useragent,$params);
        


        if (!empty($id)) {
            Session::flash('message', 'Service Partner Created Successfully');
            return redirect()->route('service-partner.list');
        } else {
            return redirect()->route('service-partner.add')->withInput($request->all());
        }
    }

    public function show($idStr,$getQueryString='')
    {
        if(Auth::user()->id == 8){
            abort(404);
        } else {
            try {
                $id = Crypt::decrypt($idStr);
                $data = ServicePartner::findOrFail($id);
                return view('servicepartner.detail', compact('data','id','getQueryString'));
            } catch ( DecryptException $e) {
                return abort(404);
            }
        }
        
        
    }

    public function edit($idStr,$getQueryString=''){
        if(Auth::user()->id == 8){
            abort(404);
        } else {
            try {
                $id = Crypt::decrypt($idStr);
                $data = ServicePartner::findOrFail($id);
                return view('servicepartner.edit', compact('data','idStr','getQueryString'));
            } catch ( DecryptException $e) {
                return abort(404);
            }
        }
        
        
    }
    
    public function update(Request $request,$idStr,$getQueryString='')
    {
        try {
            $id = Crypt::decrypt($idStr);
            // dd($request->ip());
            $request->validate([
                // 'type' => 'required',
                'company_name' => 'required|max:100',
                'person_name' => 'required|max:100',
                'email' => 'regex:/(.+)@(.+)\.(.+)/i|max:100|unique:service_partners,email,'.$id.'|nullable|required_without:phone',
                'phone' => 'numeric|digits_between:7,10|unique:service_partners,phone,'.$id.'|nullable|required_without:email',
                // 'pan_no' => 'required',
                // 'aadhaar_no' => 'required',
                // 'gst_no' => 'required',
                // 'license_no' => 'required',
                'address' => 'required',
                'state' => 'required',
                'city' => 'required',
                // 'salary' => 'nullable|required_unless:type,1',
                // 'repair_charge' => 'nullable|required_unless:type,1',
                // 'travelling_allowance' => 'nullable|required_unless:type,1',
            ],[
                // 'salary.required_unless' => 'The salary field is required',
                // 'repair_charge.required_unless' => 'The repair charge is required',
                // 'travelling_allowance.required_unless' => 'The travelling allowance field is required',
            ]);
    
            $params = $request->except('_token');
    
            $service_partner = ServicePartner::find($id);

            $uplaod_base_url_prefix = config('app.uplaod_base_url_prefix');
            
            // echo asset($service_partner->photo); die;
    
            if(!empty($params['image'])){
                // if (Storage::exists($service_partner->photo)) {
                //     die($service_partner->photo);
                //     unlink($service_partner->photo);
                // }
    
                File::delete($service_partner->photo);
    
                $upload_path = $uplaod_base_url_prefix."uploads/service-partner/";
                $image = $params['image'];
                $imageName = time() . "." . $image->getClientOriginalName();
                $image->move($upload_path, $imageName);
                $uploadedImage = $imageName;
                $params['photo'] = $upload_path . $uploadedImage;
                unset($params['image']);
                
                
    
            } else {
                $params['photo'] = $service_partner->photo;
            }

            $params['updated_at'] = date('Y-m-d H:i:s');

            $browser_name = isset($params['browser_name'])?$params['browser_name']:NULL;
            $navigator_useragent = isset($params['navigator_useragent'])?$params['navigator_useragent']:NULL;
            unset($params['browser_name']);
            unset($params['navigator_useragent']);
            
            $data = ServicePartner::where('id',$id)->update($params);

            ### Changelog ###
            addChangeLog(Auth::user()->id,$request->ip(),'service_partner_update',$browser_name,$navigator_useragent,$params);
            


            if (!empty($data)) {
                Session::flash('message', 'Service Partner Updated Successfully');
                return redirect('/service-partner/list?'.$getQueryString);
                // return redirect()->route('service-partner.list');
            } else {
                return redirect()->route('service-partner.edit',$id)->withInput($request->all());
            }
        } catch ( DecryptException $e) {
            return abort(404);
        }
        
    }

    public function toggle_status(Request $request,$idStr,$getQueryString='')
    {
        try {
            $id = Crypt::decrypt($idStr);
            $service_partner = ServicePartner::find($id);
            $message = "";
            $params['service_partner_id'] = $id;
            $params['service_partner_person_name'] = $service_partner->person_name;
            $params['service_partner_company_name'] = $service_partner->company_name;
            if($service_partner->status == 1){
                $params['status'] = 0;
                ServicePartner::where('id',$id)->update(['status'=>0]);
                $message = "Service Partner deactivated successfully";
            } else {
                $params['status'] = 1;
                ServicePartner::where('id',$id)->update(['status'=>1]);
                $message = "Service Partner activated successfully";
            }

            ### Changelog ###
            $browser_name = isset($request->browser_name)?$request->browser_name:NULL;
            $navigator_useragent = isset($request->navigator_useragent)?$request->navigator_useragent:NULL;
            addChangeLog(Auth::user()->id,$request->ip(),'service_partner_change_status',$browser_name,$navigator_useragent,$params);
            

            Session::flash('message', $message);
            if(!empty($getQueryString)){            
                return redirect('/service-partner/list?'.$getQueryString);
            }
            return redirect()->route('service-partner.list');
        } catch ( DecryptException $e) {
            return abort(404);
        }
        
    }

    public function change_password($idStr,$getQueryString='')
    {
        # change password...
        if(Auth::user()->id == 8){
            abort(404);
        } else {
            try {
                $id = Crypt::decrypt($idStr);
                $service_partner = ServicePartner::find($id);
                // dd($id);
                return view('servicepartner.changepassword', compact('id','idStr','getQueryString','service_partner'));
            } catch (DecryptException $e) {
                return abort(404);
            }
        }
        
        
    }

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
            $sp = ServicePartner::find($id);
            $details['person_name'] = $sp->person_name;
            $details['company_name'] = $sp->company_name;
            $details['password'] = $params['password'];
            $browser_name = isset($params['browser_name'])?$params['browser_name']:NULL;
            $navigator_useragent = isset($params['navigator_useragent'])?$params['navigator_useragent']:NULL;
            
            if(!empty($params['password'])){
                $params['password'] = Hash::make($params['password']);            
            } else {
                unset($params['password']);
            }            
            unset($params['password_confirmation']);
            unset($params['browser_name']);
            unset($params['navigator_useragent']);
            
            // dd($params);
            ServicePartner::where('id',$id)->update($params);
            Session::flash('message', 'Password changed successfully');

            ### Changelog ###
            addChangeLog(Auth::user()->id,$request->ip(),'service_partner_change_password',$browser_name,$navigator_useragent,$details);
            


            if(!empty($getQueryString)){            
                return redirect('/service-partner/list?'.$getQueryString);
            }
            return redirect()->route('service-partner.list');

        } catch ( DecryptException $e) {
            return abort(404);            
        }
    }
    
    public function upload_pincode_csv($idStr,Request $request)
    {
        if(Auth::user()->id == 8){
            abort(404);
        } else {
            try {
                $id = Crypt::decrypt($idStr);
                $service_partner = ServicePartner::find($id);
                $service_partner_pincodes_general =ServicePartnerPincode::where('service_partner_id',$id)->where('product_type', 'general')->orderBy('number')->get();
                $service_partner_pincodes_chimney =ServicePartnerPincode::where('service_partner_id',$id)->where('product_type', 'chimney')->orderBy('number')->get();
                $service_partner_pincodes_gas_stove =ServicePartnerPincode::where('service_partner_id',$id)->where('product_type', 'gas_stove')->orderBy('number')->get();
                $service_partner_pincodes_ac =ServicePartnerPincode::where('service_partner_id',$id)->where('product_type', 'ac')->orderBy('number')->get();
				$service_partner_pincodes_gieger =ServicePartnerPincode::where('service_partner_id',$id)->where('product_type', 'gieger')->orderBy('number')->get();
                return view('servicepartner.csvpin', compact('id','service_partner','service_partner_pincodes_general','service_partner_pincodes_chimney','service_partner_pincodes_gas_stove','service_partner_pincodes_ac','service_partner_pincodes_gieger'));
            } catch ( DecryptException $e) {
                return abort(404);
            }
        }
    }

    public function assign_pincode_csv(Request $request)
    {
        # csv for pincodes...
        $request->validate([
            'csv' => 'required'
        ]);
        $params = $request->except('_token');
        $csv = $params['csv'];
        $service_partner_id = $params['service_partner_id'];
        $product_type = $params['product_type'];

        // dd($params);
        
        $rows = Excel::toArray([],$request->file('csv'));
        $data = $rows[0];        
        // $columns = $rows[0][0];       
        // dd($columns);

        foreach($data as $item){
            ## Checking Existing ... ###
            $pincode = $item[0];
            $pincode_id = 0;
           
            $exist_pincode = Pincode::where('number',$pincode)->first();
            if($exist_pincode){
                $pincode_id = $exist_pincode->id;   
            }else{
                $pincode_id = Pincode::insertGetId(['number'=>$pincode,'is_csv_uploaded'=>1,'created_at' => date('Y-m-d H:i:s') ]);
            }
            # Check pincode for other SP assigned

            $check_pincode_others = ServicePartnerPincode::where('pincode_id',$pincode_id)->where('product_type',$product_type)->where('service_partner_id', '!=', $service_partner_id)->first();

            if(!empty($check_pincode_others)){
                $another_sp_person_name = $check_pincode_others->service_partner->person_name;
                $another_sp_company_name = $check_pincode_others->service_partner->company_name;
                $another_sp_name = $another_sp_person_name.' - '.$another_sp_company_name;
                
                $errorMsg = $pincode.' already assigned to '.$another_sp_name.' for '.$product_type.' goods type';
                return redirect()->route('service-partner.upload-pincode-csv', Crypt::encrypt($service_partner_id))->withErrors(['csv'=> $errorMsg])->withInput($request->all()); 
            }
        }

        foreach($data as $item){
            # Entry In Table .... ##
            $pincode = trim($item[0]); // Remove leading and trailing whitespace
            $pincode = str_replace('�', '', $pincode); // Remove any special characters
            $pincode = substr($pincode, 0, 6); // Get only the first 6 digits
            // dd($pincode);
            $pincode_id = 0;
            $exist_pincode = Pincode::where('number',$pincode)->first();
            if($exist_pincode){
                $pincode_id = $exist_pincode->id;   
            }else{
                $pincode_id = Pincode::insertGetId(['number'=>$pincode,'is_csv_uploaded'=>1,'created_at' => date('Y-m-d H:i:s') ]);
            }
            $exist_service_partner_pincodes = ServicePartnerPincode::where('service_partner_id',$service_partner_id)->where('pincode_id',$pincode_id)->where('product_type', $product_type)->first();
        //   dd($data);
            if(!$exist_service_partner_pincodes){
                ServicePartnerPincode::insert([
                    'service_partner_id' => $service_partner_id,
                    'pincode_id' => $pincode_id,
                    'number' => $pincode,
                    'product_type' => $product_type,
                    'is_from_csv' => 1,
                    'created_at' => date('Y-m-d H:i:s') 
                ]);
            }
            // else{
            //     ServicePartnerPincode::insert([
            //         'service_partner_id' => $service_partner_id,
            //         'pincode_id' => $pincode_id,
            //         'number' => $pincode,
            //         'product_type' => $product_type,
            //         'is_from_csv' => 1,
            //         'created_at' => date('Y-m-d H:i:s') 
            //     ]);
            // }
        }

        Session::flash('message', "Pin codes has been assigned to service partner successfully"); 
        return redirect()->route('service-partner.upload-pincode-csv',Crypt::encrypt($service_partner_id));        
    }

    public function view_duplicate_pincode_assignee(Request $request)
    {
        if(Auth::user()->id == 8){
            abort(404);
        } else {
            # view duplicate pincdoe assignee...
            $data = DB::select("SELECT number, COUNT(number) AS total_pincode, GROUP_CONCAT(service_partner_id) AS partn_ids, GROUP_CONCAT(id) AS service_partner_pincode_ids , GROUP_CONCAT(product_type) AS service_partner_product_types FROM service_partner_pincodes GROUP BY number HAVING COUNT(number) > 1");

            // dd($data);

            return view('servicepartner.duplicatecsvassignee', compact('data'));
        }
        
    }

    public function remove_duplicate_pincode_assignee(Request $request)
    {
        # code...

        $request->validate([
            'service_partner_pincode_id' => 'required'
        ],[
            'service_partner_pincode_id.required' => 'Please choose at least one'
        ]);

        $service_partner_pincode_id_arr = !empty($request->service_partner_pincode_id)?$request->service_partner_pincode_id:array();
        if(!empty($service_partner_pincode_id_arr)){
            foreach($service_partner_pincode_id_arr as $id){
                ServicePartnerPincode::where('id',$id)->delete();
            }
        }

        Session::flash('message', 'Duplicate pincodes removed successfully');
        return redirect()->route('service-partner.list');        
    }

    public function pincodelist($service_partner_idStr,Request $request)
    {
        # code...

        if(Auth::user()->id == 8){
            abort(404);
        } else {
            try {
                $search = !empty($request->search)?$request->search:'';
                $product_type = !empty($request->product_type)?$request->product_type:'';
                $paginate = 25;
                $service_partner_id = Crypt::decrypt($service_partner_idStr);
                $data = ServicePartnerPincode::where('service_partner_id',$service_partner_id);
                $totalResult = ServicePartnerPincode::where('service_partner_id',$service_partner_id);
    
                if(!empty($search)){
                    $data = $data->where('number', 'LIKE', '%'.$search.'%');
                    $totalResult = $totalResult->where('number', 'LIKE', '%'.$search.'%');
                }
                if(!empty($product_type)){
                    $data = $data->where('product_type',$product_type);
                    $totalResult = $totalResult->where('product_type',$product_type);
                }
    
                $data = $data->orderBy('number','asc')->paginate($paginate);
                $totalResult = $totalResult->count();
    
                $data = $data->appends([
                    'page' => $request->page,
                    'search' => $search,
                    'product_type' => $product_type
                ]);
                $service_partner = ServicePartner::find($service_partner_id);
                return view('servicepartner.pincodelist', compact('data','totalResult','service_partner_id','service_partner','search','paginate','product_type'));
            } catch ( DecryptException $e) {
                return abort(404);
            } 
        }
               
    }

    public function pincodelistcheckbox($service_partner_idStr,Request $request)
    {
        # code...
        try {
            $search = !empty($request->search)?$request->search:'';
            
            $service_partner_id = Crypt::decrypt($service_partner_idStr);
            $data = ServicePartnerPincode::where('service_partner_id',$service_partner_id);
            $totalResult = ServicePartnerPincode::where('service_partner_id',$service_partner_id);

            if(!empty($search)){
                $data = $data->where('number', 'LIKE', '%'.$search.'%');
                $totalResult = $totalResult->where('number', 'LIKE', '%'.$search.'%');
            }

            $data = $data->orderBy('number','asc')->get();
            $totalResult = $totalResult->count();

            
            $service_partner = ServicePartner::find($service_partner_id);
            return view('servicepartner.pincodelistcheckbox', compact('data','totalResult','service_partner_id','service_partner','search'));
        } catch ( DecryptException $e) {
            return abort(404);
        }        
    }

    public function removepincdoebulk($service_partner_id,Request $request)
    {
        # remove pin codes...

        // dd($request->all());
        // dd($service_partner_id);

        $ids = !empty($request->ids)?$request->ids:array();
        $pincodeArr = array();
        if(!empty($ids)){
            foreach($ids as $id){
                $pincode = ServicePartnerPincode::find($id);
                $pincodeArr[] = $pincode->number;
                ServicePartnerPincode::where('id',$id)->delete();
            }
        }

        ### Changelog ###
        $browser_name = isset($request->browser_name)?$request->browser_name:NULL;
        $navigator_useragent = isset($request->navigator_useragent)?$request->navigator_useragent:NULL;
        
        

        $sp = ServicePartner::find($service_partner_id);
        $params['service_partner_person_name'] = $sp->person_name;
        $params['service_partner_company_name'] = $sp->company_name;
        $params['pincodeArr'] = $pincodeArr;

        addChangeLog(Auth::user()->id,$request->ip(),'service_partner_remove_pincode',$browser_name,$navigator_useragent,$params);
        

        Session::flash('message', "PIN Codes removed successfully");
        return redirect()->route('service-partner.pincodelist',Crypt::encrypt($service_partner_id));

    }

    
    public function call_logs($service_partner_idStr,$type,Request $request)
    {
        # call logs...
        try {
            $id = Crypt::decrypt($service_partner_idStr);
            $company_name = getSingleAttributeTable('service_partners','id',$id,'company_name');
            $person_name = getSingleAttributeTable('service_partners','id',$id,'person_name');
            $from_date = !empty($request->from_date)?$request->from_date:date('Y-m-01', strtotime(date('Y-m-d')));
            $to_date = !empty($request->to_date)?$request->to_date:date('Y-m-d');
            // echo "Call Logs :- ".$id;
            $totalResult = 0;
            if($type == 'installation'){
                $data = Installation::where('service_partner_id',$id)->where('is_closed', 1)->whereBetween(DB::raw('DATE(created_at)'), [$from_date,$to_date]);
                $totalResult = Installation::where('service_partner_id',$id)->where('is_closed', 1)->whereBetween(DB::raw('DATE(created_at)'), [$from_date,$to_date]);
            } else if ($type == 'repair'){
                $data = Repair::where('service_partner_id',$id)->where('is_closed', 1)->whereBetween(DB::raw('DATE(created_at)'), [$from_date,$to_date]);
                $totalResult = Repair::where('service_partner_id',$id)->where('is_closed', 1)->whereBetween(DB::raw('DATE(created_at)'), [$from_date,$to_date]);
            }
            

            $data = $data->paginate(20);
            $totalResult = $totalResult->count();
            return view('servicepartner.call-logs', compact('id','service_partner_idStr','data','type','totalResult','from_date','to_date','company_name','person_name'));
        } catch ( DecryptException $e) {
            return abort(404);
        }
        
    }

    public function add_charges(Request $request,$service_partner_idStr,$getQueryString='')
    {
        // dd($request->all());
        # add product charges...
        if(Auth::user()->id == 8){
            abort(404);
        } else {
            try {
                $id = Crypt::decrypt($service_partner_idStr);
                $goods_type = !empty($request->goods_type)?$request->goods_type:'';
                $person_name = getSingleAttributeTable('service_partners','id',$id,'person_name');
    
                $data = ServicePartnerCharge::where('service_partner_id',$id);
                // dd($data);
    
                if(!empty($goods_type)){
                    $data = $data->where('goods_type',$goods_type);
                }            
                
                $data = $data->get()->toarray();
                // dd($data);
    
                // dd($id);
                $proIdArr = array();
                foreach($data as $item){
                    $proIdArr[] = $item['product_id'];
                }
    
                return view('servicepartner.product-charges', compact('id','service_partner_idStr','getQueryString','person_name','data','proIdArr','goods_type'));
            } catch ( \DecryptException $e) {
                return abort(404);
            }
        }
        
        
    }

    public function save_charges(Request $request,$service_partner_idStr,$getQueryString='')
    {
        // dd($request->all()); 
        # save product charges...
        $request->validate([
            'details.*.product_id' => 'required',
            'details.*.installation' => 'nullable|required_without:details.*.repair',
            'details.*.repair' => 'nullable|required_without:details.*.installation',
            'details.*.cleaning' => 'nullable|required_if:goods_type,chimney,gas_stove,ac',
			'details.*.deep_cleaning' => 'nullable|required_if:goods_type,chimney,gas_stove,ac',
        ],[
            'details.*.product_id.required' => 'Please choose product',
            'details.*.installation.required_without' => 'Please add installation charges',
            'details.*.repair.required_without' => 'Please choose repair charges',
            'details.*.cleaning.required_if' => 'Please choose cleaning charges',
			'details.*.deep_cleaning.required_if' => 'Please choose deep cleaning charges',
        ]);

        $params = $request->except('_token');
        
        $service_partner_id = $params['service_partner_id'];
        $details = isset($params['details'])?$params['details']:'';

        $browser_name = isset($params['browser_name'])?$params['browser_name']:NULL;
        $navigator_useragent = isset($params['navigator_useragent'])?$params['navigator_useragent']:NULL;

        $oldProIds = $currentProIds = $removeProIdArr = array();

         //dd($params);

        $all_prev_product = ServicePartnerCharge::where('service_partner_id',$service_partner_id)->where('goods_type', $params['goods_type'])->get();

        foreach($all_prev_product as $pro){
            $oldProIds[] = $pro->product_id;
        }

        foreach($details as $newItem){
            $currentProIds[] = $newItem['product_id'];            
        }
        
        foreach($oldProIds as $value){
            if(!in_array($value,$currentProIds)){
                $removeProIdArr[] = $value;
            }
        }
        
        if(!empty($removeProIdArr)){
            foreach($removeProIdArr as $value){
                ServicePartnerCharge::where('service_partner_id',$service_partner_id)->where('product_id',$value)->delete();
            }
        }

        foreach($details as $items){
            if($items['isNew'] == 0){
                $chargeAddArr = array(
                    'product_id' => $items['product_id'],
                    'service_partner_id' => $service_partner_id,
                    'goods_type' => $params['goods_type'],
                    'installation' => $items['installation'],
                    'repair' => $items['repair'],                    
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                );
                if($params['goods_type'] == 'chimney' || $params['goods_type'] == 'gas_stove' || $params['goods_type'] == 'ac' ||     $params['goods_type'] == 'gieger'){
                    $chineyAddData = array(
						'cleaning' => $items['cleaning'],
						'deep_cleaning' => $items['deep_cleaning']
					);
                    $chargeAddArr = array_merge($chargeAddArr,$chineyAddData);
                }
                ServicePartnerCharge::insert($chargeAddArr);
            }
            if($items['isNew'] == 1){
                $chargeEditArr = array(
                    'goods_type' => $params['goods_type'],
                    'installation' => $items['installation'],
                    'repair' => $items['repair'],
                    'updated_at' => date('Y-m-d H:i:s')
                );
                if($params['goods_type'] == 'chimney' || $params['goods_type'] == 'gas_stove' || $params['goods_type'] == 'ac' || $params['goods_type'] == 'gieger'){
                    $chineyEditData = array(
						'cleaning' => $items['cleaning'],
						'deep_cleaning' => $items['deep_cleaning']
					);
                    $chargeEditArr = array_merge($chargeEditArr,$chineyEditData);
                }
                ServicePartnerCharge::where('service_partner_id',$service_partner_id)->where('product_id', $items['product_id'])->update($chargeEditArr);
            }
        }


        ### Changelog ###
        unset($params['browser_name']);
        unset($params['navigator_useragent']);

        $sp = ServicePartner::find($service_partner_id);
        $params['service_partner_person_name'] = $sp->person_name;
        $params['service_partner_company_name'] = $sp->company_name;
        addChangeLog(Auth::user()->id,$request->ip(),'service_partner_product_charges',$browser_name,$navigator_useragent,$params);
        
        Session::flash('message', 'Charges added successfully');
        return redirect('/service-partner/list');
        

        // dd($params);


    }
    
}
