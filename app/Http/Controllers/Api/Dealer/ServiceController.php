<?php

namespace App\Http\Controllers\Api\Dealer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\DB;
use File;
use App\Models\Dealer;
use App\Models\Pincode;
use App\Models\ServicePartnerPincode;
use App\Models\ServicePartner;
use App\Models\Installation;
use App\Models\Product;

class ServiceController extends Controller
{
    private $user_id;
    public function __construct(Request $request)
    {
        # pass bearer token in Authorizations key...
        if (! $request->hasHeader('Authorizations')) {
            response()->json(["status"=>false,"message"=>"Unauthorized"],401)->send();
            exit();
        } else {
            $bearer_token = $request->header('Authorizations');
            $token = str_replace("Bearer ","",$bearer_token);            
            try {
                $this->user_id = Crypt::decrypt($token);
                $dealer = Dealer::find($this->user_id);           
            } catch (DecryptException $e) {
                response()->json(["status"=>false,"message"=>"Mismatched token"],400)->send();
                exit();
            }
        }

    }

    public function create(Request $request)
    {
        # create service...

        $validator = Validator::make($request->all(),[
            'customer_name' => 'required',
            'address' => 'required',
            // 'district' => 'required',
            'mobile_no' => 'required',
            'delivery_date' => 'required|date_format:Y-m-d',            
            'salesman' => 'required',
            'salesman_mobile_no' => 'nullable',
            'product_sl_no' => 'required',
            'product_name' => 'required',
            'remarks' => 'nullable',
            'pincode' => 'required|numeric',
            'bill_no' => 'required|unique:installations,bill_no'
        ]);

        $dealer_id = $this->user_id;
        // dd($dealer_user_id);

        if(!$validator->fails()){
            $params = $request->except('_token');
            $snapshot_file = !empty($params['snapshot_file'])?$params['snapshot_file']:'';

            $branch = !empty($params['branch'])?$params['branch']:'';
            $entry_date = !empty($params['entry_date'])?$params['entry_date']:date('Y-m-d');
            $bill_no = !empty($params['bill_no'])?$params['bill_no']:'';
            $customer_name = !empty($params['customer_name'])?$params['customer_name']:'';
            $address = !empty($params['address'])?$params['address']:'';
            $district = !empty($params['district'])?$params['district']:'';
            $mobile_no = !empty($params['mobile_no'])?$params['mobile_no']:'';
            $phone_no = !empty($params['phone_no'])?$params['phone_no']:'';
            $delivery_date = !empty($params['delivery_date'])?$params['delivery_date']:'';
            $brand = !empty($params['brand'])?$params['brand']:'KGA';
            $class = '';
            $salesman = !empty($params['salesman'])?$params['salesman']:'';
            $salesman_mobile_no = !empty($params['salesman_mobile_no'])?$params['salesman_mobile_no']:'';
            $product_value = !empty($params['product_value'])?$params['product_value']:'';
            $product_sl_no = !empty($params['product_sl_no'])?$params['product_sl_no']:'';
            $product_name = !empty($params['product_name'])?$params['product_name']:'';
            
            $pincode = Pincode::where('number',$params['pincode'])->first();
            // echo '<pre>'; print_r($pincode);

            $default_service_partner = ServicePartner::find(1);
            $csv_to_email = $default_service_partner->email;

            $mail_sent_to = $csv_to_email;

            if(!empty($pincode)){
                $pincode_id = $pincode->id;
                $getpartnerpincode = ServicePartnerPincode::with('service_partner')->where('pincode_id',$pincode_id)->orderBy('id','desc')->first();
                // echo '<pre>'; print_r($getpartnerpincode);
                if(!empty($getpartnerpincode)){
                    $email = $getpartnerpincode->service_partner->email; 
                    $person_name = $getpartnerpincode->service_partner->person_name; 

                    $mail_sent_to = $email;
                    
                    if(!empty($email)){
                        $mail_send = 1;
                        $service_partner_id = $getpartnerpincode->service_partner_id;
                        // dd($person_name);
                        $notificationData = array(
                            'unique_id' => 'INSTAL'.genAutoIncreNoYearWise(6,'installations',date('Y')),
                            'dealer_id' => $dealer_id,
                            'service_partner_id' => $service_partner_id,
                            'service_partner_email' => $email,
                            'pincode' => $params['pincode'],
                            'mail_send' => $mail_send,
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
                            'snapshot_file' => $snapshot_file
                        );
                        // echo '<pre> Service Partner notificationData:- '; print_r($notificationData);
                        Installation::insert($notificationData);
                        /* Mail Send Service Partner */
                        $mailData['email'] = $email;
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
                        $mailData['pincode'] = $pincode;
                        // echo '<pre>Service Partner mailData:- '; print_r($mailData);
                        $this->mailSendData($mailData,$snapshot_file);  
                    }                            
                } else {
                    $mail_send = 1;
                    $notificationData = array(
                        'unique_id' => 'INSTAL'.genAutoIncreNoYearWise(6,'installations',date('Y')),
                        'dealer_id' => $dealer_id,
                        'service_partner_id' => 1,
                        'service_partner_email' => $csv_to_email,
                        'pincode' => $params['pincode'],
                        'mail_send' => $mail_send,
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
                        'snapshot_file' => $snapshot_file
                    );
                    // echo '<pre> Master notificationData:- '; print_r($notificationData);
                    Installation::insert($notificationData);
                    /* Mail Send Master */
                    $mailAdminData['email'] = $csv_to_email;
                    $mailAdminData['name'] = "KGA Admin";
                    $mailAdminData['subject'] = "KGA SERVICE NOTIFICATION";
                    $mailAdminData['bill_no'] = $bill_no;
                    $mailAdminData['customer_name'] = $customer_name;
                    $mailAdminData['branch'] = $branch;
                    $mailAdminData['address'] = $address;
                    $mailAdminData['district'] = $district;
                    $mailAdminData['mobile_no'] = $mobile_no;
                    $mailAdminData['phone_no'] = $phone_no;
                    $mailAdminData['delivery_date'] = $delivery_date;
                    $mailAdminData['brand'] = $brand;
                    $mailAdminData['class'] = $class;
                    $mailAdminData['salesman'] = $salesman;
                    $mailAdminData['salesman_mobile_no'] = $salesman_mobile_no;
                    $mailAdminData['product_value'] = $product_value;
                    $mailAdminData['product_sl_no'] = $product_sl_no;
                    $mailAdminData['product_name'] = $product_name;
                    $mailAdminData['pincode'] = $pincode;
                    // echo '<pre> Master mailAdminData:- '; print_r($mailAdminData);
                    $this->mailSendData($mailAdminData,$snapshot_file);
                } 
                

            } else {
                $mail_send = 1;
                $notificationData = array(
                    'unique_id' => 'INSTAL'.genAutoIncreNoYearWise(6,'installations',date('Y')),
                    'dealer_id' => $dealer_id,
                    'service_partner_id' => 1,
                    'service_partner_email' => $csv_to_email,
                    'pincode' => $params['pincode'],
                    'mail_send' => $mail_send,
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
                    'snapshot_file' => $snapshot_file
                );
                // echo '<pre> Master notificationData:- '; print_r($notificationData);
                Installation::insert($notificationData);
                /* Mail Send Master */
                $mailAdminData['email'] = $csv_to_email;
                $mailAdminData['name'] = "KGA Admin";
                $mailAdminData['subject'] = "KGA SERVICE NOTIFICATION";
                $mailAdminData['bill_no'] = $bill_no;
                $mailAdminData['customer_name'] = $customer_name;
                $mailAdminData['branch'] = $branch;
                $mailAdminData['address'] = $address;
                $mailAdminData['district'] = $district;
                $mailAdminData['mobile_no'] = $mobile_no;
                $mailAdminData['phone_no'] = $phone_no;
                $mailAdminData['delivery_date'] = $delivery_date;
                $mailAdminData['brand'] = $brand;
                $mailAdminData['class'] = $class;
                $mailAdminData['salesman'] = $salesman;
                $mailAdminData['salesman_mobile_no'] = $salesman_mobile_no;
                $mailAdminData['product_value'] = $product_value;
                $mailAdminData['product_sl_no'] = $product_sl_no;
                $mailAdminData['product_name'] = $product_name;
                $mailAdminData['pincode'] = $pincode;
                // echo '<pre> Master mailAdminData:- '; print_r($mailAdminData);
                $this->mailSendData($mailAdminData,$snapshot_file);
            }

            // echo '<pre>'.$mail_sent_to; die;
            return Response::json([
                'status' => true,
                'message' => "Details Submitted Successfully To Service Partner. ",
                'data' => array(
                    'mail_sent_to' => $mail_sent_to,
                    'params' => $params
                    
                )
            ],200);
            // echo '<pre>'; print_r($params); die;
        } else {
            return Response::json(['status' => false, 'message' => $validator->errors()->first() , 'data' => array( $validator->errors() ) ],400);
        }
        
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
            # snapshot_file is full image file path ... no need to concat
            $files = array(
                $snapshot_file
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

    public function upload_snapshot(Request $request)
    {
        # upload snapshot for adding service details...
        
        $validator = Validator::make($request->all(),[
            'filename' => 'required|file|mimes:png,jpg,jpeg|max:10000'
        ]);

        $uplaod_base_url_prefix = config('app.uplaod_base_url_prefix');

        if(!$validator->fails()){
            $params = $request->except('_token');
            // echo '<pre>'; print_r($params); die;
                
            $upload_path = $uplaod_base_url_prefix."uploads/service-snapshot/";
            $image = $params['filename'];
            // $imageName = time() . "." . $image->getClientOriginalName();            
            $imageName = time() . "." . $image->getClientOriginalExtension();
            $image->move($upload_path, $imageName);
            $uploadedImage = $imageName;
            $fileurl = $upload_path . $uploadedImage;
            // dd($fileurl);
            return Response::json([
                'status' => true,
                'message' => "Image uploaded successfully",
                'data' => array(
                    'fileurl' => $fileurl
                )
            ]);
        } else {
            return Response::json(['status' => false, 'message' => $validator->errors()->first() , 'data' => array( $validator->errors() ) ],400);
        }
    }

    
    public function search_product(Request $request)
    {
        # code...
        $params = $request->except('_token');
        $search = !empty($params['search'])?$params['search']:'';
        $data = Product::select('id','title');
        if(!empty($search)){
            $data = $data->where('title', 'LIKE', '%'.$search.'%');
        }
        $data = $data->orderBy('title')->get();

        return Response::json([
            'status' => true,
            'message' => "Search product result ",
            'data' => array(
                'products' => $data
                
            )
        ],200);
    }
}
