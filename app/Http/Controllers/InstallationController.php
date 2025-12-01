<?php

namespace App\Http\Controllers;

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
use App\Models\ServicePartner;
use App\Models\Pincode;
use App\Models\ServicePartnerPincode;
use App\Models\Installation;
use App\Models\Settings;
use App\Models\CloseInstallation;
use App\Models\ServicePartnerCharge;
use App\Models\Product;
use App\Models\Payment;
use App\Models\Ledger;
use App\Models\Changelog;

class InstallationController extends Controller
{
    //

    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function list(Request $request)
    {
        # list installation...
        
        $paginate = 20;
        $search = !empty($request->search)?$request->search:'';
        $uploaded_at = !empty($request->uploaded_at)?$request->uploaded_at:'';
        $bill_date = !empty($request->bill_date)?$request->bill_date:'';
        $service_partner = !empty($request->service_partner)?$request->service_partner:'';

        $closing_type = !empty($request->closing_type)?$request->closing_type:'';

        $service_partners = ServicePartner::get();
        $data = Installation::with('service_partner');
        $totalResult = Installation::select('id');

        if(!empty($search)){
            $data = $data->where(function($query) use ($search){
                $query->where('pincode', 'LIKE','%'.$search.'%')->orWhere('bill_no', 'LIKE','%'.$search.'%')->orWhere('mobile_no', 'LIKE', '%'.$search.'%')->orWhere('phone_no', 'LIKE', '%'.$search.'%')->orWhere('customer_name', 'LIKE', '%'.$search.'%')->orWhere('unique_id', 'LIKE', '%'.$search.'%')->orWhere('product_sl_no', 'LIKE', '%'.$search.'%')->orWhereHas('product', function($p) use($search){
                    $p->where('title','LIKE','%'.$search.'%');
                });
            });
            $totalResult = $totalResult->where(function($query) use ($search){
                $query->where('pincode', 'LIKE','%'.$search.'%')->orWhere('bill_no', 'LIKE','%'.$search.'%')->orWhere('mobile_no', 'LIKE', '%'.$search.'%')->orWhere('phone_no', 'LIKE', '%'.$search.'%')->orWhere('customer_name', 'LIKE', '%'.$search.'%')->orWhere('unique_id', 'LIKE', '%'.$search.'%')->orWhere('product_sl_no', 'LIKE', '%'.$search.'%')->orWhereHas('product', function($p) use($search){
                    $p->where('title','LIKE','%'.$search.'%');
                });
            });
        }

        if(!empty($service_partner)){
            $data = $data->where('service_partner_id',$service_partner);
            $totalResult = $totalResult->where('service_partner_id',$service_partner);
        }

        if(!empty($uploaded_at)){
            $data = $data->whereRaw(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d') = '".$uploaded_at."'"));
            $totalResult = $totalResult->whereRaw(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d') = '".$uploaded_at."'"));
        } 
        if(!empty($bill_date)){
            $data = $data->where('delivery_date', $bill_date);
            $totalResult = $totalResult->where('delivery_date', $bill_date);
        } 
        
        if(!empty($closing_type)){
            if($closing_type == 'cancelled'){
                $data = $data->where('is_cancelled', 1);
                $totalResult = $totalResult->where('is_cancelled', 1);
                $data = $data->orderBy('id','desc')->paginate($paginate);
            } else if($closing_type == 'pending'){
                $data = $data->where('is_closed', 0)->where('is_cancelled', 0);
                $totalResult = $totalResult->where('is_closed', 0)->where('is_cancelled', 0);
                $data = $data->orderBy('id','asc')->paginate($paginate);
            } else {
                $data = $data->where('is_closed', 1);
                $totalResult = $totalResult->where('is_closed', 1);
                $data = $data->orderBy('id','desc')->paginate($paginate);
            }
            
        } else {
            # Default id desc
            $data = $data->orderBy('id','desc')->paginate($paginate);
        }
        
        $totalResult = $totalResult->count();

        $data = $data->appends([
            'search'=>$search,
            'service_partner'=>$service_partner,
            'uploaded_at'=>$uploaded_at,
            'bill_date'=>$bill_date,
            'page'=>$request->page,
            'closing_type'=>$closing_type
        ]);
                
        return view('installation.list', compact('data','totalResult','paginate', 'search','uploaded_at','service_partners','service_partner','closing_type','bill_date'));
    }

    public function add(Request $request)
    {
        # add installation...      
        return view('installation.add');
    }

    public function save(Request $request)
    {
        # save installation...
        $request->validate([
            // 'dealer_user_id' => 'required',
            'dealer_id' => 'required',
            'customer_name' => 'required',
            'address' => 'required',
            'mobile_no' => 'required',
            'delivery_date' => 'required',
            // 'salesman' => 'required',
            // 'salesman_mobile_no' => 'required',
            'product_sl_no' => 'required|exists:purchase_order_barcodes,barcode_no',
            'product_name' => 'required',
            'pincode' => 'required',
            'bill_no' => 'required|unique:installations,bill_no',
            'filename' => 'nullable|file|mimes:png,jpg,jpeg|max:10000'
        ],[
            'dealer_id.required' => 'Please select dealer',
            'product_sl_no.exists' => 'This product serial number does not exist in our record.'
        ]);

        $params = $request->except('_token');

        $uplaod_base_url_prefix = config('app.uplaod_base_url_prefix');
        
        // $dealer_user_id = $params['dealer_user_id'];
        $dealer_id = $params['dealer_id'];
        
        $branch = !empty($params['branch'])?$params['branch']:null;
        $entry_date = !empty($params['entry_date'])?$params['entry_date']:null;
        $bill_no = !empty($params['bill_no'])?$params['bill_no']:null;
        $customer_name = !empty($params['customer_name'])?$params['customer_name']:null;
        $address = !empty($params['address'])?$params['address']:null;
        $district = !empty($params['district'])?$params['district']:null;
        $mobile_no = !empty($params['mobile_no'])?$params['mobile_no']:null;
        $phone_no = !empty($params['phone_no'])?$params['phone_no']:null;
        $delivery_date = !empty($params['delivery_date'])?$params['delivery_date']:null;
        $brand = !empty($params['brand'])?$params['brand']:'KGA';
        $class = null;
        $salesman = !empty($params['salesman'])?$params['salesman']:null;
        $salesman_mobile_no = !empty($params['salesman_mobile_no'])?$params['salesman_mobile_no']:null;
        $product_value = !empty($params['product_value'])?$params['product_value']:null;
        $product_sl_no = !empty($params['product_sl_no'])?$params['product_sl_no']:null;
        $product_name = !empty($params['product_name'])?$params['product_name']:null;
        $product_id = !empty($params['product_id'])?$params['product_id']:null;

        $service_partner_id = !empty($params['service_partner_id'])?$params['service_partner_id']:null;
        $service_partner_email = !empty($params['service_partner_email'])?$params['service_partner_email']:null;
        $service_partner_company_name = !empty($params['service_partner_company_name'])?$params['service_partner_company_name']:null;
        $service_partner_person_name = !empty($params['service_partner_person_name'])?$params['service_partner_person_name']:null;

        $service_charge = null;
        if(!empty($product_id)){
            $exist_partner_charge = ServicePartnerCharge::where('service_partner_id',$service_partner_id)->where('product_id', $product_id)->first();

            if(!empty($exist_partner_charge)){
                if(!empty($exist_partner_charge->installation)){
                    $service_charge = $exist_partner_charge->installation;
                }
            }
        }  
        
        if(!$service_charge){
            return redirect()->back()->withErrors(['service_partner_id' => 'No installation charge added for this product for this service partner'])->withInput();
        }

        $existSame = DB::table('installations')->where('entry_date',$entry_date)->where('bill_no',$bill_no)->where('product_sl_no', $product_sl_no)->first();

        if(!empty($existSame)){
            return redirect()->back()->withErrors(['bill_no' => ' Already exists the record '])->withInput();
        }


        $snapshot_file = null;
        if(!empty($params['filename'])){
            $upload_path = $uplaod_base_url_prefix."uploads/service-snapshot/";
            $image = $params['filename'];
            // $imageName = time() . "." . $image->getClientOriginalName();            
            $imageName = time() . "." . $image->getClientOriginalExtension();
            $image->move($upload_path, $imageName);
            $uploadedImage = $imageName;
            $snapshot_file = $upload_path . $uploadedImage;
        }
          
        $default_service_partner = ServicePartner::find(1);
        $csv_to_email = $default_service_partner->email;
        $person_name = "KGA Admin";

        if(!empty($service_partner_id)){
            $csv_to_email = $service_partner_email;
            $person_name = $service_partner_person_name;
        }
        
        

        $installationData = array(
            'unique_id' => 'INSTAL'.genAutoIncreNoYearWise(6,'installations',date('Y')),
            'dealer_id' => $dealer_id,
            'service_partner_id' => $service_partner_id,
            'service_partner_email' => $csv_to_email,
            'pincode' => $params['pincode'],
            'mail_send' => 1,
            'branch' => $branch,
            'entry_date' => $entry_date,
            'bill_no' => $bill_no,
            'customer_name' => $customer_name,
            'address' => $address,
            'district' => $district,
            'mobile_no' => $mobile_no,
            'phone_no' => $phone_no,
            'delivery_date' => $delivery_date,
            'brand' => $brand,
            'salesman' => $salesman,
            'salesman_mobile_no' => $salesman_mobile_no,
            'product_value' => $product_value,
            'product_sl_no' => $product_sl_no,
            'product_name' => $product_name,
            'product_id' => $product_id,
            'service_charge' => $service_charge,
            'snapshot_file' => $snapshot_file,
            'created_at' => date('Y-m-d H:i:s') 
        );
        // echo '<pre> Service Partner installationData:- '; print_r($installationData); die;
        Installation::insert($installationData);
        /* Mail Send Service Partner */
        $mailData['email'] = $csv_to_email;
        $mailData['name'] = $person_name;
        $mailData['subject'] = "KGA SERVICE NOTIFICATION";
        $mailData['bill_no'] = $bill_no;
        $mailData['customer_name'] = $customer_name;
        $mailData['branch'] = $branch;
        $mailData['address'] = $address;
        $mailData['district'] = $district;
        $mailData['mobile_no'] = $mobile_no;
        $mailData['phone_no'] = $phone_no;
        $mailData['delivery_date'] = $delivery_date;
        $mailData['brand'] = $brand;
        $mailData['class'] = $class;
        $mailData['salesman'] = $salesman;
        $mailData['salesman_mobile_no'] = $salesman_mobile_no;
        $mailData['product_value'] = $product_value;
        $mailData['product_sl_no'] = $product_sl_no;
        $mailData['product_name'] = $product_name;
        $mailData['pincode'] = $params['pincode'];
        // echo '<pre>Service Partner mailData:- '; print_r($mailData);
        $this->mailSendData($mailData,$snapshot_file); 


        Session::flash('message', "Details Submitted Successfully To Service Partner.");
        return redirect()->route('installation.list');
    }

    public function edit($idStr,$getQueryString='')
    {
        # Edit Call (pincode & service partner)...
        try {
            $id = Crypt::decrypt($idStr);
            $data = Installation::find($id);
            return view('installation.edit', compact('id','idStr','getQueryString','data'));
        } catch ( DecryptException $e) {
            return abort(404);
        }
        
    }

    public function update(Request $request,$idStr,$getQueryString='')
    {
        # code...
        try {
            $id = Crypt::decrypt($idStr);
            $request->validate([
                'dealer_id' => 'nullable|exists:dealers,id',
                'pincode' => 'required',
                'service_partner_id' => 'required',
                'delivery_date' => 'required',
                'customer_name' => 'required',
                'mobile_no' => 'required',
                'address' => 'required'
            ],[
                // 'dealer_id.required' => 'Dealer is required',
                'pincode.required' => 'Pincode is required',
                'service_partner_id.required' => 'Please add service partner'
            ]);
            $params = $request->except('_token');
            // dd($params);

            $service_charge = null;
            if(!empty($params['product_id'])){
                $exist_partner_charge = ServicePartnerCharge::where('service_partner_id',$params['service_partner_id'])->where('product_id', $params['product_id'])->first();

                if(!empty($exist_partner_charge)){
                    if(!empty($exist_partner_charge->installation)){
                        $service_charge = $exist_partner_charge->installation;
                    }
                }
            }  
            
            if(!$service_charge){
                return redirect()->back()->withErrors(['service_partner_id' => 'No installation charge added for this product for this service partner'])->withInput();
            }

            
            // dd($params);
            $updateArr = array(
                'dealer_id' => $params['dealer_id'],
                'service_partner_id' => $params['service_partner_id'],
                'service_partner_email' => $params['service_partner_email'],
                'pincode' => $params['pincode'],
                'delivery_date' => $params['delivery_date'],
                'customer_name' => $params['customer_name'],
                'mobile_no' => $params['mobile_no'],
                'address' => $params['address'],
                'service_charge' => $service_charge,
                'updated_at' => date('Y-m-d H:i:s')
            );

            $browser_name = isset($params['browser_name'])?($params['browser_name']):NULL;
            $navigator_useragent = isset($params['navigator_useragent'])?$params['navigator_useragent']:NULL;
            unset($params['browser_name']);
            unset($params['navigator_useragent']);

            Installation::where('id',$id)->update($updateArr);
            $installation = Installation::find($id);
            $params['unique_id'] = $installation->unique_id;
            addChangeLog(Auth::user()->id,$request->ip(),'installation_edit_call',$browser_name,$navigator_useragent,$params);
            
            Session::flash('message', "Call edited successfully.");            
            return redirect('/installation/list?'.$getQueryString);
            
        } catch ( DecryptException $e) {
            return abort(404);
        }
    }

    public function upload_csv(Request $request)
    {
        # upload installation ... csv mapped with service partners ... mail send

        $request->validate([
            'csv' => 'required'
        ]);
        $params = $request->except('_token');
        $csv = $params['csv'];
        $csvFileName = $csv->getClientOriginalName();
        $csvFileExt = $csv->getClientOriginalExtension();
        
        $rows = Excel::toArray([],$request->file('csv'));
        $snapshot_file = '';
        
        /* CSV Master To Email */
        $default_service_partner = ServicePartner::find(1);
        $csv_to_email = $default_service_partner->email;
        
        $data = $rows[0];
        /* Column validaton */
        $columns = $rows[0][0];        
        $myReqColumns = [
                            'Sl',
                            'Branch',
                            'Date',
                            'Billno',
                            'Productname',
                            'Customername',
                            'Address',
                            'Pincode',
                            'District',
                            'Mobileno',
                            'Phoneno',
                            'Deliverydate',
                            'Remarks',
                            'Brand',
                            'Class',
                            'Salesman',
                            'Salesmanmobileno',
                            'Productvalue',
                            'Productserialno'
                        ];
        $reqColumnErr = false;
        foreach($columns as $col){
            if(!in_array($col,$myReqColumns)){
                $reqColumnErr = true;
            }
        }
        if($reqColumnErr){
            return  redirect()->back()->withErrors(['csv'=> "Missing column in file"])->withInput();
        }

        // dd($data);
               
        foreach($data as $key => $item){            
            if($key != 0){
                // dd(getDateValue($item[11]));
                $pincode = $item[7];
                $pincodes = Pincode::where('number',$pincode)->first();
                $mail_send = 0;
                $service_partner_id = 0;

                /* Sync Product */
                $product_id = null;
                $goods_type = 'general';
                $exist_pro = Product::where('title', 'LIKE', $item[4])->first();
                if(!empty($exist_pro)){
                    $product_id = $exist_pro->id;
                    $goods_type = $exist_pro->goods_type;
                }

                $exist_service_partner_csv = Installation::where('bill_no', $item[3])->where('entry_date', getDateValue($item[2]))->where('product_sl_no', $item[18])->first();

                if(empty($exist_service_partner_csv)){
                    if(!empty($pincodes)){
                        $pincode_id = $pincodes->id;
                        $getpartnerpincode = ServicePartnerPincode::with('service_partner')->where('pincode_id',$pincode_id)->where('product_type',$goods_type)->orderBy('id','desc')->first();
                        
                        // echo '<pre>'; print_r($getpartnerpincode);
                        if(!empty($getpartnerpincode)){
                            $email = $getpartnerpincode->service_partner->email; 
                            $person_name = $getpartnerpincode->service_partner->person_name; 
                            if(!empty($email)){

                                $mail_send = 1;
                                $service_partner_id = $getpartnerpincode->service_partner_id;

                                $service_charge = null;
                                if(!empty($product_id)){
                                    $exist_partner_charge = ServicePartnerCharge::where('service_partner_id',$service_partner_id)->where('product_id', $product_id)->first();

                                    if(!empty($exist_partner_charge)){
                                        if(!empty($exist_partner_charge->installation)){
                                            $service_charge = $exist_partner_charge->installation;
                                        }
                                    }
                                }
                                
                                if($service_charge){
                                    $notificationData = array(
                                        'unique_id' => 'INSTAL'.genAutoIncreNoYearWise(6,'installations',date('Y')),
                                        'csv_file_name' => $csvFileName,
                                        'service_partner_id' => $service_partner_id,
                                        'service_partner_email' => $email,
                                        'pincode' => $pincode,
                                        'mail_send' => $mail_send,
                                        'branch' => $item[1],
                                        'entry_date' => getDateValue($item[2]),
                                        'bill_no' => $item[3],
                                        'customer_name' => $item[5],
                                        'address' => $item[6],
                                        'district' => $item[8],
                                        'mobile_no' => $item[9],
                                        'phone_no' => $item[10],
                                        'delivery_date' => getDateValue($item[11]),
                                        'brand' => $item[13],
                                        'class' => $item[14],
                                        'salesman' => $item[15],
                                        'salesman_mobile_no' => $item[16],
                                        'product_value' => $item[17],
                                        'product_sl_no' => $item[18],
                                        'product_name' => $item[4],
                                        'product_id' => $product_id,
                                        'service_charge' => $service_charge,
                                        'created_at' => date('Y-m-d H:i:s') 
                                    );
                                    Installation::insert($notificationData);
                                    /* Mail Send Service Partner */
                                    $mailData['email'] = $email;
                                    $mailData['name'] = $person_name;
                                    $mailData['subject'] = "KGA SERVICE NOTIFICATION";
                                    $mailData['bill_no'] = $item[3];
                                    $mailData['customer_name'] = $item[5];
                                    $mailData['branch'] = $item[1];
                                    $mailData['address'] = $item[6];
                                    $mailData['district'] = $item[8];
                                    $mailData['mobile_no'] = $item[9];
                                    $mailData['phone_no'] = $item[10];
                                    $mailData['delivery_date'] = $item[11];
                                    $mailData['brand'] = $item[13];
                                    $mailData['class'] = $item[14];
                                    $mailData['salesman'] = $item[15];
                                    $mailData['salesman_mobile_no'] = $item[16];
                                    $mailData['product_value'] = $item[17];
                                    $mailData['product_sl_no'] = $item[18];
                                    $mailData['product_name'] = $item[4];
                                    $mailData['pincode'] = $item[7];
                                    $this->mailSendData($mailData,$snapshot_file); 
                                }
                                 
                            }                            
                        } else {
                            /* Mail Send Master */
                            $mailAdminData['email'] = $csv_to_email;
                            $mailAdminData['name'] = "KGA Admin";
                            $mailAdminData['subject'] = "KGA SERVICE NOTIFICATION";
                            $mailAdminData['bill_no'] = $item[3];
                            $mailAdminData['customer_name'] = $item[5];
                            $mailAdminData['branch'] = $item[1];
                            $mailAdminData['address'] = $item[6];
                            $mailAdminData['district'] = $item[8];
                            $mailAdminData['mobile_no'] = $item[9];
                            $mailAdminData['phone_no'] = $item[10];
                            $mailAdminData['delivery_date'] = $item[11];
                            $mailAdminData['brand'] = $item[13];
                            $mailAdminData['class'] = $item[14];
                            $mailAdminData['salesman'] = $item[15];
                            $mailAdminData['salesman_mobile_no'] = $item[16];
                            $mailAdminData['product_value'] = $item[17];
                            $mailAdminData['product_sl_no'] = $item[18];
                            $mailAdminData['product_name'] = $item[4];
                            $mailAdminData['pincode'] = $item[7];
                            $this->mailSendData($mailAdminData,$snapshot_file);
                            $mail_send = 1;
                            $notificationData = array(
                                'unique_id' => 'INSTAL'.genAutoIncreNoYearWise(6,'installations',date('Y')),
                                'csv_file_name' => $csvFileName,
                                'service_partner_id' => 1,
                                'service_partner_email' => $csv_to_email,
                                'pincode' => $pincode,
                                'mail_send' => $mail_send,
                                'branch' => $item[1],
                                'entry_date' => getDateValue($item[2]),
                                'bill_no' => $item[3],
                                'customer_name' => $item[5],
                                'address' => $item[6],
                                'district' => $item[8],
                                'mobile_no' => $item[9],
                                'phone_no' => $item[10],
                                'delivery_date' => getDateValue($item[11]),
                                'brand' => $item[13],
                                'class' => $item[14],
                                'salesman' => $item[15],
                                'salesman_mobile_no' => $item[16],
                                'product_value' => $item[17],
                                'product_sl_no' => $item[18],
                                'product_name' => $item[4],
                                'product_id' => $product_id,
                                'created_at' => date('Y-m-d H:i:s') 
                            );
                            Installation::insert($notificationData);
                        }     
                    } else {
                        /* Mail Send Master */
                        $mailAdminData['email'] = $csv_to_email;
                        $mailAdminData['name'] = "KGA Admin";
                        $mailAdminData['subject'] = "KGA SERVICE NOTIFICATION";
                        $mailAdminData['bill_no'] = $item[3];
                        $mailAdminData['customer_name'] = $item[5];
                        $mailAdminData['branch'] = $item[1];
                        $mailAdminData['address'] = $item[6];
                        $mailAdminData['district'] = $item[8];
                        $mailAdminData['mobile_no'] = $item[9];
                        $mailAdminData['phone_no'] = $item[10];
                        $mailAdminData['delivery_date'] = $item[11];
                        $mailAdminData['brand'] = $item[13];
                        $mailAdminData['class'] = $item[14];
                        $mailAdminData['salesman'] = $item[15];
                        $mailAdminData['salesman_mobile_no'] = $item[16];
                        $mailAdminData['product_value'] = $item[17];
                        $mailAdminData['product_sl_no'] = $item[18];
                        $mailAdminData['product_name'] = $item[4];
                        $mailAdminData['pincode'] = $item[7];
                        $this->mailSendData($mailAdminData,$snapshot_file);
                        $mail_send = 1;
                        $notificationData = array(
                            'unique_id' => 'INSTAL'.genAutoIncreNoYearWise(6,'installations',date('Y')),
                            'csv_file_name' => $csvFileName,
                            'service_partner_id' => 1,
                            'service_partner_email' => $csv_to_email,
                            'pincode' => $pincode,
                            'mail_send' => $mail_send,
                            'branch' => $item[1],
                            'entry_date' => getDateValue($item[2]),
                            'bill_no' => $item[3],
                            'customer_name' => $item[5],
                            'address' => $item[6],
                            'district' => $item[8],
                            'mobile_no' => $item[9],
                            'phone_no' => $item[10],
                            'delivery_date' => getDateValue($item[11]),
                            'brand' => $item[13],
                            'class' => $item[14],
                            'salesman' => $item[15],
                            'salesman_mobile_no' => $item[16],
                            'product_value' => $item[17],
                            'product_sl_no' => $item[18],
                            'product_name' => $item[4],
                            'product_id' => $product_id,
                            'created_at' => date('Y-m-d H:i:s') 
                        );
                        Installation::insert($notificationData);
    
                    }
                }
            }                        
        }
        // dd($data);
        // echo '<pre>'; print_r($data); die;
        Session::flash('message', "Service Partner notified successfully from csv for installation");
        return redirect()->route('installation.list');
    }

    private function mailSendData($data,$snapshot_file)
    {
        # mail send data...
        $mailData['email'] = $data['email'];
        $mailData['name'] = $data['name'];
        $mailData['subject'] = $data['subject'];
        $mailBody = "";
        
        $mailBody .= "<h1>Hi, ".$data['name']."!</h1> <br/>";
        $mailBody .= "<p>You have a new notification for installing goods.<p>";
        $mailBody .= "Please find the details below , <br/>";
        
        
        $mailBody .= "
        <table cellspacing='0' cellpadding='0' style='border: 1px solid #ddd;'>
            <thead>
                <tr>
                    <th style='padding:5px; border: 1px solid #ddd;'>Order Detail</th>
                    <th style='padding:5px; border: 1px solid #ddd;'>Product Detail</th>
                    <th style='padding:5px; border: 1px solid #ddd;'>Customer Detail</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style='padding:5px; border: 1px solid #ddd;'>Bill No: <strong>".$data['bill_no']."</strong> </td>
                    <td style='padding:5px; border: 1px solid #ddd;'>Product Sl No: <strong>".$data['product_sl_no']."</strong> </td>
                    <td style='padding:5px; border: 1px solid #ddd;'>Customer Name: <strong>".$data['customer_name']."</strong> </td>
                </tr>
                <tr>
                    <td style='padding:5px; border: 1px solid #ddd;'>Delivery Date:<strong>".$data['delivery_date']."</strong></td>
                    <td style='padding:5px; border: 1px solid #ddd;'>Product Name: <strong>".$data['product_name']."</strong></td>
                    <td style='padding:5px; border: 1px solid #ddd;'>Address: <strong>".$data['address']." </strong></td>
                </tr>
                <tr>
                    <td style='padding:5px; border: 1px solid #ddd;'>Branch: <strong>".$data['branch']."</strong></td>
                    <td style='padding:5px; border: 1px solid #ddd;'>Brand: <strong>".$data['brand']."</strong> </td>
                    <td style='padding:5px; border: 1px solid #ddd;'>District: <strong>".$data['district']."<strong></strong></td>
                </tr>
                <tr>
                    <td style='padding:5px; border: 1px solid #ddd;'>&nbsp;</td>
                    <td style='padding:5px; border: 1px solid #ddd;'>Class: <strong>".$data['class']."</strong></td>
                    <td style='padding:5px; border: 1px solid #ddd;'>Customer PIN Code: <strong>".$data['pincode']."</strong></td>
                </tr>            
                <tr>
                    <td style='padding:5px; border: 1px solid #ddd;'>&nbsp;</td>
                    <td style='padding:5px; border: 1px solid #ddd;'>&nbsp;</td>
                    <td style='padding:5px; border: 1px solid #ddd;'>Contact Number: <strong>".$data['mobile_no']." / ".$data['phone_no']."</strong></td>
                </tr>
            </tbody>
        </table>
        ";


        $mailData['body'] = $mailBody;
        if(!empty($snapshot_file)){
            $files = array(
                public_path().'/'.$snapshot_file
            );
            $mail = mailSendAttachments($mailData,$files);
            if($mail) {
                $details = json_encode($data);
                DB::table('mail_send')->insert([
                    'email' => $data['email'],
                    'bill_no' =>  $data['bill_no'], 
                    'details' => $details,
                    'is_attachment' => 1,
                    'attachement_files' => json_encode($files),
                    'created_at' => date('Y-m-d H:i:s')  
                ]);        
            }
        } else {
            $mail = sendMail($mailData);
            if($mail) {
                $details = json_encode($data);
                DB::table('mail_send')->insert([
                    'email' => $data['email'],
                    'bill_no' =>  $data['bill_no'],
                    'details' => $details,
                    'created_at' => date('Y-m-d H:i:s')
                ]);        
            }
        }

        

    }

    public function submit_call_close(Request $request)
    {
        # close the call...
        
        // dd($request->all());
        $params = $request->except('_token');
        $params['created_at'] = date('Y-m-d H:i:s');
        $request_url = $params['request_url'];
        // dd($params);

        $installation_id = $params['installation_id'];
        unset($params['request_url']);
        CloseInstallation::insert($params);
        Installation::where('id',$installation_id)->update([
            'is_closed' => 1
        ]);

        $data = Installation::find($installation_id);
        $service_partner_id = $data->service_partner_id;
        $service_charge = $data->service_charge;
        $product_id = $data->product_id;
        $unique_id = $data->unique_id;

        # Ledger Entry Service Partner
        if($service_partner_id != 1){
            # If not master
            $this->ledgerEntryCallClose($service_partner_id,$service_charge,$unique_id,$installation_id);
            $this->ledgerDebitEntryCallClose($service_partner_id,$product_id,$installation_id);
        }
        

        Session::flash('message', "Call closed successfully.");
        return redirect('/installation/list?'.$request_url);
    }

    public function set_urgent($idStr,$getQueryString='')
    {
        # set call urgent...
        // echo "Set Urgent";
        try {
            $id = Crypt::decrypt($idStr);
            Installation::where('id',$id)->update([
                'is_urgent' => 1 , 'set_urgent_at' => date('Y-m-d H:i:s')
            ]);

            Session::flash('message', "Call marked as urgent successfully");
            return redirect('installation/list?'.$getQueryString);
        } catch ( DecryptException $e) {
            return abort(404);
        }
    }

    private function ledgerEntryCallClose($service_partner_id,$amount,$unique_id,$installation_id){
        $ledgerData = array(
            'type' => 'credit',
            'service_partner_id' => $service_partner_id,
            'amount' => $amount,
            'entry_date' => date('Y-m-d'),
            'user_type' => 'servicepartner',
            'purpose' => 'installation',
            'transaction_id' => $unique_id,
            'installation_id' => $installation_id,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        );

        $existLedger = Ledger::where('installation_id',$installation_id)->first();
        if(empty($existLedger)){
            Ledger::insert($ledgerData);
        }
    }
    private function ledgerDebitEntryCallClose($service_partner_id,$product_id,$installation_id){
        $unique_id = '';
        $product = Product::findOrFail($product_id);
        if(isset($product)){
            $amount = $product->installable_amount;
            if($amount>0){
                $params =[];
                unset($params['bank_name_hidden']);
                $params['service_partner_id'] = $service_partner_id;
                $params['amount'] = $amount;
                $params['user_type'] = "servicepartner";
                $params['payment_mode'] = "cash";
                $params['voucher_no'] = 'PAYT'.genAutoIncreNoYearWiseOrder(5,'payments',date('Y'),date('m'));
                $params['entry_date'] = date('Y-m-d');
                $params['created_at'] = date('Y-m-d H:i:s');
                $params['updated_at'] = date('Y-m-d H:i:s');
                
                $payment_id = Payment::insertGetId($params);
                $ledgerData = array(
                    'type' => 'debit',
                    'amount' => $amount,
                    'entry_date' => $params['entry_date'],
                    'user_type' => 'servicepartner',
                    'service_partner_id' => $service_partner_id,
                    'payment_id' => $payment_id,
                    'purpose' => 'payment',
                    'transaction_id' => $params['voucher_no'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                );
                $existLedger = Ledger::where('installation_id',$installation_id)->where('type', 'debit')->first();
                if(empty($existLedger)){
                    Ledger::insert($ledgerData);
                }
            }
        }
    }

    public function save_remark(Request $request)
    {
        # save remark ...
        $request->validate([
            'remarks' => 'required'
        ]);
        $params = $request->except('_token');
        $request_url = $params['request_url'];

        Installation::where('id',$params['installation_id'])->update([
            'remarks' => $params['remarks']
        ]);

        // dd($params);

        Session::flash('message', "Remark added successfully.");
        return redirect('/installation/list?'.$request_url);

    }

    public function cancel(Request $request,$idStr,$getQueryString='')
    {
        # Cancel Call...

        try {
            $id = Crypt::decrypt($idStr);
            $data = Installation::where('id',$id)->update(['is_cancelled'=>1]);

            $installation = Installation::find($id);
            $params['unique_id'] = $installation->unique_id;

            $browser_name = isset($request->browser_name)?$request->browser_name:NULL;
            $navigator_useragent = isset($request->navigator_useragent)?$request->navigator_useragent:NULL;
            
            addChangeLog(Auth::user()->id,$request->ip(),'installation_cancel',$browser_name,$navigator_useragent,$params);
            
            Session::flash('message', "Installation cancelled successfully.");
            return redirect('/installation/list?'.$getQueryString);

        } catch ( DecryptException $e) {
            return abort(404);
        }
        
    }

    
}
