<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

use App\Models\ServicePartner;
use App\Models\IncompleteInstallation;
use App\Models\ServicePartnerCharge;
use App\Models\Installation;

class IncompleteInstallationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Incomplete Installation Logs
     *
     */
    public function index(Request $request)
    {
        $paginate = 20;
        $search = !empty($request->search)?$request->search:'';
        $service_partner_id = !empty($request->service_partner_id)?$request->service_partner_id:'';
        $service_partner = ServicePartner::where('is_default', 0)->orderBy('person_name')->get();
        $data = IncompleteInstallation::select('*');
        $totalData = IncompleteInstallation::select('id');
        if(!empty($search)){
            $data = $data->where('bill_no','LIKE','%'.$search.'%')->orWhere('item','LIKE','%'.$search.'%')->orWhere('customer_name','LIKE','%'.$search.'%');
            $totalData = $totalData->where('bill_no','LIKE','%'.$search.'%')->orWhere('item','LIKE','%'.$search.'%')->orWhere('customer_name','LIKE','%'.$search.'%');
        }
        if(!empty($service_partner_id)){
            $data = $data->where('service_partner_id',$service_partner_id);
            $totalData = $totalData->where('service_partner_id',$service_partner_id);
        }
        $data = $data->orderBy('id','desc')->paginate($paginate);
        $totalData = $totalData->count();

        $data = $data->appends([
            'page' => $request->page,
            'search' => $search,
            'service_partner_id' => $service_partner_id
        ]);
        return view('incomplete-installation.list', compact('data','paginate','totalData','search','service_partner_id','service_partner'));
    }
    /**
     * Service Partner Wise Clear Installation Form
     *
     */
    public function clear_form(Request $request)
    {
        $service_partner_id = !empty($request->service_partner_id)?$request->service_partner_id:'';
        $service_partner = ServicePartner::where('is_default', 0)->orderBy('person_name')->get();

        $products = array();
        if(!empty($service_partner_id)){
            $products = DB::table('incomplete_installation')->where('service_partner_id',$service_partner_id)->whereNull('installation_id')->groupBy('product_id')->get('product_id');

            // dd($products);
        }
        
        
        return view('incomplete-installation.clear', compact('service_partner_id','service_partner','products'));
        
    }
    /**
     * Save Incomplete Installation - Add Charges - Book Call - Send Email
     *
     */
    public function save_incomplete_installation(Request $request)
    {
        $request->validate([
            'details.*.product_id' => 'required',
            'details.*.installation' => 'required',
            'details.*.repair' => 'required',
        ],[
            'details.*.installation.required' => 'Please add installation charge',
            'details.*.repair.required' => 'Please add repair charge'
        ]);

        $params = $request->except('_token');
        // dd($params);
        $proidArr = array();
        $details = $params['details'];
        foreach($details as $item){
            $proidArr[] = $item['product_id'];
            $chargeData = array(
                'service_partner_id' => $params['service_partner_id'],
                'product_id' => $item['product_id'],
                'installation' => $item['installation'],
                'repair' => $item['repair'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            );
            #1: Add Service Partner Charge
            ServicePartnerCharge::insert($chargeData);
        }

        $incomplete_data = IncompleteInstallation::where('service_partner_id',$params['service_partner_id'])->whereNull('installation_id')->whereIn('product_id',$proidArr)->get()->toArray();

        if(!empty($incomplete_data)){
            foreach($incomplete_data as $items){
                $service_partner = ServicePartner::find($params['service_partner_id']);
                $email = $service_partner->email;
                $person_name = $service_partner->person_name;
                $service_charge = null;
                $service_partner_charge = ServicePartnerCharge::where('product_id',$items['product_id'])->where('service_partner_id',$params['service_partner_id'])->first();
                if(!empty($service_partner_charge)){
                    if(!empty($service_partner_charge->installation)){
                        $service_charge = $service_partner_charge->installation;
                    }
                }
                // dd($items['id']);
                $installationData = array(
                    'unique_id' => 'INSTAL'.genAutoIncreNoYearWise(6,'installations',date('Y')),
                    'service_partner_id' => $params['service_partner_id'],
                    'bill_no' => $items['bill_no'],
                    'mail_send' => 1,
                    'service_partner_email' => $email,
                    'pincode' => $items['pincode'],
                    'branch' => $items['branch'],
                    'entry_date' => $items['bill_date'],
                    'address' => $items['address'],
                    'mobile_no' => $items['mobile'],
                    'phone_no' => $items['phone'],
                    'delivery_date' => $items['bill_date'],
                    'brand' => $items['bill_no'],
                    'class' => $items['class_name'],
                    'service_charge' => $service_charge,
                    'product_name' => $items['item'],
                    'product_id' => $items['product_id'],
                    'product_sl_no' => $items['serial'],
                    'customer_name' => $items['customer_name'],
                    'created_at' => date('Y-m-d H:i:s')
                );

                // dd($installationData);
                #2: Add New Installation
                $installation_id = Installation::insertGetId($installationData);
                #3: Update Installation Id For Those Incomplete Installation Logs
                IncompleteInstallation::where('id',$items['id'])->update(['installation_id'=>$installation_id]);

                /* Mail Send Service Partner */
                $mailData['email'] = $email;
                $mailData['name'] = $person_name;
                $mailData['subject'] = "KGA SERVICE NOTIFICATION";
                $mailData['bill_no'] = $items['bill_no'];
                $mailData['customer_name'] = $items['customer_name'];
                $mailData['branch'] = $items['branch'];
                $mailData['address'] = $items['address'];
                $mailData['mobile_no'] = $items['mobile'];
                $mailData['phone_no'] = $items['phone'];
                $mailData['bill_date'] = $items['bill_date'];
                $mailData['brand'] = 'KGA';
                $mailData['class'] = $items['class_name'];
                $mailData['product_sl_no'] = $items['serial'];
                $mailData['product_name'] = $items['item'];
                $mailData['pincode'] = $items['pincode'];
                // echo '<pre>Service Partner mailData:- '; dd($mailData); 
                // die;
                #4: Mail Send To Service Partner
                $this->mailSendData($mailData); 
            }
        }

        // dd($incomplete_data);
        Session::flash('message', "Service Charges added and installation call booked successfully");
        return redirect()->route('installation.list');
    }

    private function mailSendData($data)
    {
        # mail send data...
        $mailData['email'] = $data['email'];
        // $mailData['email'] = 'arnabm.oneness@gmail.com';
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
                    <td style='padding:5px; border: 1px solid #ddd;'>&nbsp;</td>
                    <td style='padding:5px; border: 1px solid #ddd;'>Customer Name: <strong>".$data['customer_name']."</strong> </td>
                </tr>
                <tr>
                    <td style='padding:5px; border: 1px solid #ddd;'>Bill Date:<strong>".$data['bill_date']."</strong></td>
                    <td style='padding:5px; border: 1px solid #ddd;'>Product Name: <strong>".$data['product_name']."</strong></td>
                    <td style='padding:5px; border: 1px solid #ddd;'>Address: <strong>".$data['address']." </strong></td>
                </tr>
                <tr>
                    <td style='padding:5px; border: 1px solid #ddd;'>Branch: <strong>".$data['branch']."</strong></td>
                    <td style='padding:5px; border: 1px solid #ddd;'>Brand: <strong>".$data['brand']."</strong> </td>
                    <td style='padding:5px; border: 1px solid #ddd;'>&nbsp;</td>
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

        // dd($mailBody);
        
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
