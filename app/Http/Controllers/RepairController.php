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
use App\Models\Repair;
use App\Models\ServicePartnerPincode;
use App\Models\ServicePartner;
use App\Models\ServicePartnerCharge;
use App\Models\CloseRepair;
use App\Models\Ledger;
use App\Models\Product;
use App\Models\RepairSpare;
use App\Models\Changelog;
use App\Models\PurchaseOrderBarcode;
use App\Models\SpareReturn;

class RepairController extends Controller
{
    //

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function list(Request $request)
    {
        # list repair request...
        $paginate = 20;
        $search = !empty($request->search)?$request->search:'';
        $closing_type = !empty($request->closing_type)?$request->closing_type:'';
        $uploaded_at = !empty($request->uploaded_at)?$request->uploaded_at:'';
        $service_partner_id = !empty($request->service_partner_id)?$request->service_partner_id:'';
        $service_partners = ServicePartner::where('is_default', 0)->orderBy('person_name')->get();

        $data = Repair::select('*');
        $totalResult = Repair::select('id');

        if(!empty($search)){
            $data = $data->where(function($query) use ($search){
                $query->where('pincode', 'LIKE','%'.$search.'%')->orWhere('bill_no', 'LIKE','%'.$search.'%')->orWhere('customer_name', 'LIKE', '%'.$search.'%')->orWhere('customer_phone', 'LIKE', '%'.$search.'%')->orWhere('unique_id', 'LIKE', '%'.$search.'%');
            });
            $totalResult = $totalResult->where(function($query) use ($search){
                $query->where('pincode', 'LIKE','%'.$search.'%')->orWhere('bill_no', 'LIKE','%'.$search.'%')->orWhere('customer_name', 'LIKE', '%'.$search.'%')->orWhere('customer_phone', 'LIKE', '%'.$search.'%')->orWhere('unique_id', 'LIKE', '%'.$search.'%');
            });
        }

        if(!empty($service_partner_id)){
            $data = $data->where('service_partner_id',$service_partner_id);
            $totalResult = $totalResult->where('service_partner_id',$service_partner_id);
        }

        if(!empty($uploaded_at)){
            $data = $data->whereRaw(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d') = '".$uploaded_at."'"));
            $totalResult = $totalResult->whereRaw(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d') = '".$uploaded_at."'"));
        } 

        if(!empty($closing_type)){
            if($closing_type == 'cancelled'){
                $data = $data->where('is_cancelled', 1);
                $totalResult = $totalResult->where('is_cancelled', 1);
                $data = $data->orderBy('id','asc')->paginate($paginate);
            } else if($closing_type == 'repeated'){
                $data = $data->where('is_repeated', 1);
                $totalResult = $totalResult->where('is_closed', 1);
                $data = $data->orderBy('id','asc')->paginate($paginate);
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

        // $data = $data->paginate($paginate);
        $totalResult = $totalResult->count();
        $data = $data->appends([
            'search'=>$search,
            'service_partner_id'=>$service_partner_id,
            'uploaded_at'=>$uploaded_at,
            'page'=>$request->page,
            'closing_type'=>$closing_type
        ]);

        return view('repair.list', compact('data','totalResult','closing_type','paginate','service_partners','uploaded_at','search','service_partner_id'));
    }

    public function add(Request $request)
    {
        # add repair request...

        return view('repair.add');
    }

    public function save(Request $request)
    {
        # code...
        $request->validate([
            'dealer_id' => 'required',
            'customer_name' => 'required',
            'customer_phone' => 'required',
            // 'address' => 'required',
            'order_date' => 'required',
            'product_sl_no' => 'required',
            'product_name' => 'required',
            'pincode' => 'required',
            'bill_no' => 'required',
            'filename' => 'nullable|file|mimes:png,jpg,jpeg|max:10000'
        ],[
            'dealer_id.required' => 'Please select dealer user'
        ]);

        $params = $request->except('_token');

        // dd($params);
        $service_partner_id = !empty($params['service_partner_id'])?$params['service_partner_id']:null;
        $service_partner_email = !empty($params['service_partner_email'])?$params['service_partner_email']:null;
        $person_name = !empty($params['service_partner_person_name'])?$params['service_partner_person_name']:null;
        
        $dealer_id = !empty($params['dealer_id'])?$params['dealer_id']:null;
        $pincode = !empty($params['pincode'])?$params['pincode']:null;
        $customer_name = !empty($params['customer_name'])?$params['customer_name']:null;
        $customer_phone = !empty($params['customer_phone'])?$params['customer_phone']:null;
        $address = !empty($params['address'])?$params['address']:null;
        $bill_no = !empty($params['bill_no'])?$params['bill_no']:null;
        $order_date = !empty($params['order_date'])?$params['order_date']:null;
        $delivery_date = !empty($params['delivery_date'])?$params['delivery_date']:null;
        $product_value = !empty($params['product_value'])?$params['product_value']:null;
        $product_id = !empty($params['product_id'])?$params['product_id']:null;
        $product_name = !empty($params['product_name'])?$params['product_name']:null;
        $product_sl_no = !empty($params['product_sl_no'])?$params['product_sl_no']:null;
        $product_id = !empty($params['product_id'])?$params['product_id']:null;
        $remarks = !empty($params['remarks'])?$params['remarks']:null;
        $warranty_status = !empty($params['warranty_status'])?$params['warranty_status']:null;
        $warranty_period = !empty($params['warranty_period'])?$params['warranty_period']:null;
        $warranty_date = !empty($params['warranty_date'])?$params['warranty_date']:null;
        $is_repeated = isset($params['is_repeated'])?$params['is_repeated']:0;

        # Check In A Month Same Bill No Pro Sl No New Repair Request

        $isCallRepeated = Repair::where('bill_no', $bill_no)->where('product_sl_no',$product_sl_no)->where('is_repeated', 1)->orderBy('id', 'desc')->first();

        if(!empty($isCallRepeated)){
            return redirect()->back()->withErrors(['bill_no' => 'Call is already repated', 'repeated' => 'repeated'])->withInput();
        }

        $barcodeExist=PurchaseOrderBarcode::where('barcode_no',$params['product_sl_no'])->first();
        if (!$barcodeExist) {
            // If the barcode is not found, redirect back with an error message
            return redirect()->back()->withErrors(['product_sl_no' => 'This serial number does not exist in our records'])->withInput();
        }
        

        $chRepairInMonth = Repair::where('bill_no', $bill_no)->where('product_sl_no',$product_sl_no)->where('is_repeated', 0)->orderBy('id', 'desc')->first();  //->where('is_repeated', 0) ommit
        if(!empty($chRepairInMonth)){
            $last_created_date = $chRepairInMonth->created_at;
            $date1=date_create($last_created_date);
            $date2=date_create(date('Y-m-d'));
            $diff=date_diff($date1,$date2);
            // $days = $diff->format("%d");
            $days = $diff->days;


            if(($days <= 30) && (empty($is_repeated))){
                
                return redirect()->back()->withErrors(['bill_no' => 'Call is being repated within 30 days', 'repeated' => 'repeated'])->withInput();
            }

            
        }

        


        $service_charge = null;
        if(!empty($product_id)){
            $exist_partner_charge = ServicePartnerCharge::where('service_partner_id',$service_partner_id)->where('product_id', $product_id)->first();

            if(!empty($exist_partner_charge)){
                if(!empty($exist_partner_charge->repair)){
                    $service_charge = $exist_partner_charge->repair;
                }
            }
        }        
        if(!$service_charge){
            return redirect()->back()->withErrors(['service_partner_id' => 'No repair charge added for this product for this service partner'])->withInput();
        }

        $snapshot_file = null;
        $uplaod_base_url_prefix = config('app.uplaod_base_url_prefix');
        if(!empty($params['filename'])){
            $upload_path = $uplaod_base_url_prefix."uploads/repair/";
            $image = $params['filename'];     
            $imageName = time() . "." . $image->getClientOriginalExtension();
            $image->move($upload_path, $imageName);
            $uploadedImage = $imageName;
            $snapshot_file = $upload_path . $uploadedImage;
        }

        $default_service_partner = ServicePartner::find(1);
        
        // $mail_sent_to = $csv_to_email;

        if(empty($params['service_partner_id'])){
            $service_partner_id = 1;
            $service_partner_email = $default_service_partner->email;
            $person_name = 'KGA Admin';
        }

        // die;

        // dd($params);

        $product = Product::find($product_id);
        $repair_charge = $product->repair_charge;
        $in_warranty = ($params['out_of_warranty'] == 'Yes')?0:1;

        
        $repairData = array(
            'unique_id' => 'REP'.genAutoIncreNoYearWise(6,'repairs',date('Y')),
            'service_partner_id' => $service_partner_id,
            'service_partner_email' => $service_partner_email,
            'dealer_id' => $dealer_id,
            'pincode' => $pincode,
            'address' => $address,
            'customer_name' => $customer_name,
            'customer_phone' => $customer_phone,
            'bill_no' => $bill_no,
            'order_date' => $order_date,
            'product_value' => $product_value,
            'product_sl_no' => $product_sl_no,
            'product_name' => $product_name,
            'product_id' => $product_id,
            'warranty_status' => $warranty_status,
            'warranty_period' => $warranty_period,
            'warranty_date' => $warranty_date,
            'remarks' => $remarks,
            'snapshot_file' => $snapshot_file,
            'service_charge' => $service_charge,
            'repair_charge' => $repair_charge,
            'in_warranty' => $in_warranty,
            'is_repeated' => $is_repeated,
            'created_at' => date('Y-m-d H:i:s')
        );
        // dd($repairData);
        Repair::insert($repairData);
       

        $mailData['email'] = $service_partner_email;
        $mailData['name'] = $person_name;
        $mailData['subject'] = "KGA REPAIR NOTIFICATION";
        $mailData['bill_no'] = $bill_no;
        $mailData['order_date'] = date('d/m/Y', strtotime($order_date));
        $mailData['customer_name'] = $customer_name;
        $mailData['customer_phone'] = $customer_phone;
        $mailData['brand'] = 'KGA';
        $mailData['product_value'] = $product_value;
        $mailData['product_sl_no'] = $product_sl_no;
        $mailData['product_name'] = $product_name;
        $mailData['pincode'] = $pincode;
        $this->mailSendData($mailData,$snapshot_file);

        
        Session::flash('message', "Repair booked successfully");
        return redirect()->route('repair.list');

    }


    private function mailSendData($data,$snapshot_file)
    {
        # mail send data...
        // $mailData['email'] = 'arnabm.oneness@gmail.com';
        $mailData['email'] = $data['email'];
        $mailData['name'] = $data['name'];
        $mailData['subject'] = $data['subject'];
        $mailBody = "";
        
        $mailBody .= "<h1>Hi, ".$data['name']."!</h1> <br/>";
        $mailBody .= "<p>You have a new notification for repairing goods.<p>";
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
                    <td style='padding:5px; border: 1px solid #ddd;'>Order Date:<strong>".$data['order_date']."</strong></td>
                    <td style='padding:5px; border: 1px solid #ddd;'>Product Name: <strong>".$data['product_name']."</strong></td>
                    <td style='padding:5px; border: 1px solid #ddd;'>Customer PIN Code: <strong>".$data['pincode']."</strong></td>
                </tr>
                <tr>
                    <td style='padding:5px; border: 1px solid #ddd;'>&nbsp;</td>
                    <td style='padding:5px; border: 1px solid #ddd;'>Brand: <strong>".$data['brand']."</strong> </td>
                    <td style='padding:5px; border: 1px solid #ddd;'>Contact Number: <strong>".$data['customer_phone']."</strong></td>
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
                    'mail_for' => 'repair',
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
                DB::table('mail_send')->insert(['email' => $data['email'] ,'mail_for' => 'repair' , 'bill_no' =>  $data['bill_no'] , 'details' => $details , 'created_at' => date('Y-m-d H:i:s')  ]);        
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

        $repair_id = $params['repair_id'];
        unset($params['request_url']);
        CloseRepair::insert($params);
        Repair::where('id',$repair_id)->update([
            'is_closed' => 1
        ]);

        $data = Repair::find($repair_id);
        $is_repeated = $data->is_repeated;
        $service_partner_id = $data->service_partner_id;
        $service_charge = $data->service_charge;
        $unique_id = $data->unique_id;
        $in_warranty = $data->in_warranty;
        $goods_id = $data->product_id;

        # Ledger Entry Service Partner
        if($service_partner_id != 1){
            # If not master
            if(empty($is_repeated)){
                # If the call is not repeated
                $this->ledgerEntryCallClose($service_partner_id,$service_charge,$unique_id,$repair_id);
            }            
        }

        if(empty($in_warranty)){
            ## If Spares are not added (For Spare Addition This Calculation Already Done)
            ## Ledger Debit Entry Total Service Charge For Out Of Warranty

            $repair_spares =  RepairSpare::where('repair_id',$repair_id)->count();
            $product = Product::find($goods_id);
            $repair_charge = !empty($product->repair_charge)?$product->repair_charge:0;

            if(empty($repair_spares)){
                if(!empty($repair_charge)){
                    $this->ledgerOutOfWarrantyCharge($repair_id,$service_partner_id,$unique_id,$repair_charge);
                }                        
            }            
        }
        

        Session::flash('message', "Call closed successfully.");
        return redirect('/repair/list?'.$request_url);
    }

    private function ledgerEntryCallClose($service_partner_id,$amount,$unique_id,$repair_id){
        $ledgerData = array(
            'type' => 'credit',
            'service_partner_id' => $service_partner_id,
            'amount' => $amount,
            'entry_date' => date('Y-m-d'),
            'user_type' => 'servicepartner',
            'purpose' => 'repair',
            'transaction_id' => $unique_id,
            'repair_id' => $repair_id,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        );

        $existLedger = Ledger::where('repair_id',$repair_id)->first();
        if(empty($existLedger)){
            Ledger::insert($ledgerData);
        }
    }

    private function ledgerOutOfWarrantyCharge($repair_id,$service_partner_id,$unique_id,$amount){

        ## Check exist ledger entry

        $checkExistLedger = Ledger::where('purpose','repair_charges')->where('repair_id',$repair_id)->first();

        if(empty($checkExistLedger)){
            $ledgerArr = array(
                'type' => 'debit',
                'user_type' => 'servicepartner',
                'service_partner_id' => $service_partner_id,
                'repair_id' => $repair_id,
                'amount' => $amount,
                'entry_date' => date('Y-m-d'),
                'purpose' => 'repair_charges',
                'transaction_id' => $unique_id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            );
    
            Ledger::insert($ledgerArr);
        } else {
            $ledgerArr = array(                
                'amount' => $amount,
                'entry_date' => date('Y-m-d'),
                'updated_at' => date('Y-m-d H:i:s')
            );
    
            Ledger::where('id', $checkExistLedger->id)->update($ledgerArr);
        }

        
    }

    public function edit($idStr,$getQueryString='')
    {
        # Edit Repair...
        try {
            $id = Crypt::decrypt($idStr);
            $data = Repair::find($id);
            return view('repair.edit', compact('id','idStr','getQueryString','data'));
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
                'pincode' => 'required',
                'service_partner_id' => 'required',
                'customer_name' => 'required',
                'customer_phone' => 'required',
                'address' => 'required',
                'order_date' => 'required'
            ],[
                'pincode.required' => 'Pincode is required',
                'service_partner_id.required' => 'Please add service partner'
            ]);
            $params = $request->except('_token');

            $service_charge = null;
            if(!empty($params['product_id'])){
                $exist_partner_charge = ServicePartnerCharge::where('service_partner_id',$params['service_partner_id'])->where('product_id', $params['product_id'])->first();

                if(!empty($exist_partner_charge)){
                    if(!empty($exist_partner_charge->repair)){
                        $service_charge = $exist_partner_charge->repair;
                    }
                }
            }  
            
            if(!$service_charge){
                return redirect()->back()->withErrors(['service_partner_id' => 'No repair charge added for this product for this service partner'])->withInput();
            }

            $repair = Repair::find($id);
            $created_at = $repair->created_at;      
            $product_id = $repair->product_id;
            $in_warranty = 1;      
            $product = Product::find($product_id);
            $warranty_period = $product->warranty_period;
            $warranty_date = null;
            
            if(!empty($warranty_period)){
                $warranty_end_date = date('Y-m-d', strtotime($params['order_date']. ' + '.$warranty_period.' months'));
                $warranty_date = date('Y-m-d', strtotime($warranty_end_date.'-1 days'));
                
                // if(date('Y-m-d', strtotime($created_at)) < $warranty_date ){
                //     $in_warranty = 0;
                // }

                if(date('Y-m-d', strtotime($created_at)) > $warranty_date){
                    $in_warranty = 0;
                }
            }

            
            // dd($params);
            $updateArr = array(
                'dealer_id' => $params['dealer_id'],
                'service_partner_id' => $params['service_partner_id'],
                'service_partner_email' => $params['service_partner_email'],
                'pincode' => $params['pincode'],
                'customer_name' => $params['customer_name'],
                'customer_phone' => $params['customer_phone'],
                'address' => $params['address'],
                'order_date' => $params['order_date'],
                'in_warranty' => $in_warranty,
                'warranty_date' => $warranty_date,
                'service_charge' => $service_charge,
                'updated_at' => date('Y-m-d H:i:s')
            );
            // dd($updateArr);
            Repair::where('id',$id)->update($updateArr);

            Session::flash('message', "Call edited successfully.");            
            return redirect('/repair/list?'.$getQueryString);
            
        } catch ( DecryptException $e) {
            return abort(404);
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

        Repair::where('id',$params['repair_id'])->update([
            'remarks' => $params['remarks']
        ]);

        // dd($params);

        Session::flash('message', "Remark added successfully.");
        return redirect('/repair/list?'.$request_url);

    }

    public function cancel(Request $request,$idStr,$getQueryString='')
    {
        # Cancel Call...

        try {
            $id = Crypt::decrypt($idStr);
            $data = Repair::where('id',$id)->update(['is_cancelled'=>1]);

            $repair = Repair::find($id);

            $browser_name = isset($request->browser_name)?$request->browser_name:NULL;
            $navigator_useragent = isset($request->navigator_useragent)?$request->navigator_useragent:NULL;
            $params['unique_id'] = $repair->unique_id;
            addChangeLog(Auth::user()->id,$request->ip(),'repair_cancel',$browser_name,$navigator_useragent,$params);
            

            Session::flash('message', "Repair cancelled successfully.");
            return redirect('/repair/list?'.$getQueryString);

        } catch ( DecryptException $e) {
            return abort(404);
        }
        
    }

    public function add_spares(Request $request,$idStr,$getQueryString='')
    {
        try {
            $id = Crypt::decrypt($idStr);
            // dd($id);
            $repair = Repair::find($id);
            return view('repair.add-spares', compact('id','idStr','getQueryString','repair'));

        } catch ( DecryptException $e) {
            return abort(404);
        }
    }

    public function save_spares(Request $request,$idStr,$getQueryString='')
    {
        try {
            $id = Crypt::decrypt($idStr);
            // dd($id);
            $repair = Repair::find($id);
            $service_partner_id = $repair->service_partner_id;
            $goods_id = $repair->product_id;
            $product = Product::find($goods_id);
            $supplier_warranty_period = $product->supplier_warranty_period;

            if(empty($supplier_warranty_period)){
                return redirect()->back()->withErrors(['goods_id' => 'Please add supplier warranty for the repairing item,  '.$product->title.''])->withInput();
            }

            $request->validate([
                'details.*.product_id' => 'required',
                'details.*.barcodes' => 'required',
            ],[
               'details.*.product_id.required' => 'Please choose spare',
               'details.*.barcodes.required' => 'Please choose barcodes', 
            ]);
            
            $params = $request->except('_token');
            

            $details = $params['details'];
            foreach($details as $item){
                $barcodes = $item['barcodes'];
                foreach($barcodes as $barcode){
                    $spare_id = $item['product_id'];
                    $stock_box = \App\Models\StockBarcode::where('barcode_no', $barcode)->first();
                    $stock_prod = \App\Models\StockProduct::where('stock_id', $stock_box->stock_id)->where('product_id', $spare_id)->first();
                    $cost_price = $stock_prod->cost_price;

                    $is_item_supplier_warranty =  is_item_supplier_warranty($repair->product_id,$repair->order_date);

                    // dd($is_item_supplier_warranty);
                        $spareBarcodeArr = array(
                            'service_partner_id' => $service_partner_id,
                            'spare_id' => $item['product_id'],
                            'barcode_no' => $barcode,
                            'repair_id' => $id,
                            'goods_id' => $goods_id,
                            'rate' => $cost_price,
                            'goods_supplier_warranty_period' => $supplier_warranty_period,
                            'in_warranty' => $is_item_supplier_warranty,
                            'created_at' => date('Y-m-d H:i:s'),
                        );

                        // dd($spareBarcodeArr);

                        SpareReturn::insert($spareBarcodeArr);
                }
            }
            // dd($params);

            // SpareReturn

            Session::flash('message', "Spares added for repair items successfully.");
            return redirect('/repair/list?'.$getQueryString);

        } catch ( DecryptException $e) {
            return abort(404);
        }
    }

    public function remove_spares($idStr,$getQueryString=''){
        try {
            $id = Crypt::decrypt($idStr);
            // dd($id);
            $repair = Repair::find($id);

            SpareReturn::where('repair_id', $id)->delete();

            Session::flash('message', "Spares removed successfully.");
            return redirect('/repair/list?'.$getQueryString);

        } catch ( DecryptException $e) {
            return abort(404);
        }
    }

    
}
