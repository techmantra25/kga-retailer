<?php

namespace App\Http\Controllers;

use App\Models\DapDiscountRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\DapService;
use App\Models\ServiceCentre;
use App\Models\DapSpearPartFinalOrder;
use App\Models\DapRequestReceiveDrop;
use App\Models\DapRequestReceives;
use App\Models\KgaSalesData;
use App\Models\DapRequestReturn;
use App\Models\DapServicePayment;
use App\Models\DapRequestReturnItem;
use App\Models\ServicePartner;
use App\Models\DapSpearPartOrder;
use Barryvdh\DomPDF\Facade\Pdf;


class DAPServiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = !empty($request->search)?$request->search:'';
        $entry_date = !empty($request->entry_date)?$request->entry_date:'';
        $reaching_status = !empty($request->reaching_status)?$request->reaching_status:'';
        $branch_id = !empty($request->branch_id)?$request->branch_id:'';
        $branch_name = !empty($request->branch_name)?$request->branch_name:'';
        $paginate = 20;
        $data = DapService::with('servicePartner')->select('*');
        $totalResult = DapService::select('*');
        $servicePartners = ServicePartner::where('type',2)->where('status',1)->get();
        if(!empty($search)){
            $data = $data->where('unique_id', 'LIKE', $search)->orWhere('serial','LIKE','%'.$search.'%')->orWhere('barcode','LIKE','%'.$search.'%')->orWhere('customer_name','LIKE','%'.$search.'%')->orWhere('mobile','LIKE','%'.$search.'%')->orWhere('phone','LIKE','%'.$search.'%')->orWhere('item','LIKE','%'.$search.'%');
            $totalResult = $totalResult->where('unique_id', 'LIKE', $search)->orWhere('serial','LIKE','%'.$search.'%')->orWhere('barcode','LIKE','%'.$search.'%')->orWhere('customer_name','LIKE','%'.$search.'%')->orWhere('mobile','LIKE','%'.$search.'%')->orWhere('phone','LIKE','%'.$search.'%')->orWhere('item','LIKE','%'.$search.'%');
        }
        if(!empty($entry_date)){
            $data = $data->where('entry_date', $entry_date);
            $totalResult = $totalResult->where('entry_date', $entry_date);
        }
        if(!empty($branch_id)){
            $data = $data->where('branch_id', $branch_id);
            $totalResult = $totalResult->where('branch_id', $branch_id);
        }
        if(!empty($reaching_status)){
            if($reaching_status == 'paid'){
                $data = $data->where('is_paid', 1);
                $totalResult = $totalResult->where('is_paid', 1);
            } else if ($reaching_status == 'closed'){
                $data = $data->where('is_closed', 1);
                $totalResult = $totalResult->where('is_closed', 1);
            }
        }
        
        $data = $data->orderBy('id', 'desc')->paginate($paginate);
        $totalResult = $totalResult->count();

        $data = $data->appends([
            'page' => $request->page,
            'search' => $search,
            'branch_id' => $branch_id,
            'branch_name' => $branch_name,
            'entry_date' => $entry_date,
            'reaching_status' => $reaching_status
        ]);
        // dd($data);
        
        return view('dap-services.list', compact('data','search','paginate','totalResult','entry_date','reaching_status','branch_id','branch_name','servicePartners'));
    }

    /**
     * Check Item Status.
     */

    public function checkdapitemstatus(Request $request)
{
    $contact_type = $request->input('contact_type', 'mobile');
    $mobile = $request->input('mobile', '');
    $phone = $request->input('phone', '');
    $bill_no = $request->input('bill_no', '');
    $serial = $request->input('serial', '');

    $kga_sales_data = [];

    if (!empty($mobile) || !empty($phone) || !empty($bill_no) || !empty($serial)) {
        $kga_sales_data = KgaSalesData::with('product','category')->whereNotNull('product_id');

        if ($contact_type == 'mobile' && !empty($mobile)) {
            $kga_sales_data->where('mobile', $mobile);
        } elseif ($contact_type == 'phone' && !empty($phone)) {
            $kga_sales_data->where('phone', $phone);
        }

        if (!empty($bill_no)) {
            $kga_sales_data->where('bill_no', $bill_no);
        }

        if (!empty($serial)) {
            $kga_sales_data->where('serial', $serial);
        }

        $kga_sales_data = $kga_sales_data->get();
    }
        // dd($kga_sales_data);
    return view('dap-services.checkdapitemstatus', compact('contact_type', 'mobile', 'phone', 'bill_no', 'serial', 'kga_sales_data'));
}

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // dd($request->all());
        $serial = $request->serial;
        $repeat_call = 0 ;
        $checkExistSerial = DapService::where('serial', $serial)->where('is_closed',1)->orderBy('id','DESC')->first();

        if(!empty($checkExistSerial)){
            $last_entry_date = $checkExistSerial->entry_date;
            $date1=date_create($last_entry_date);
            $date2=date_create(date('Y-m-d'));
            $diff=date_diff($date1,$date2);
            // $days = $diff->format("%d");
            $days = $diff->days;

            if($days <= 30){     
                $repeat_call = 1;
            }
        }

        return view('dap-services.add', compact('repeat_call'));
    }

    /**
     * Store a newly created resource in storage.
    **/
    public function store(Request $request)
    {
        $request->validate([
            'branch_id' => 'required',
            'alternate_no' => 'required|digits:10',
            'issue' => 'required'
        ],[
            'branch_id.required' => 'Please select a branch',
            'alternate_no.required' => 'Alternate number is required',
            'alternate_no.digits' => 'Alternate number must be exactly 10 digits',
            'issue.required' => 'Please specify the issue'
        ]);
        $params = $request->except('_token');
        $uniue_id = genAutoIncreNoYearWiseCallBook(3,'dap_services',date('Y'),date('m'),'DAP');
        $params['unique_id'] = $uniue_id;
        $params['is_dispatched_from_branch'] = 0;
        $params['entry_date'] = date('Y-m-d');
        $params['created_at'] = date('Y-m-d H:i:s');
        // dd($params);
        unset($params['branch_name']);

        $barcodeGeneratorWithNo = barcodeGeneratorWithNo($params['barcode']);
        $code_html = $barcodeGeneratorWithNo['code_html'];
        $code_base64_img = $barcodeGeneratorWithNo['code_base64_img'];
        $params['code_html'] = $code_html;
        $params['code_base64_img'] = $code_base64_img;
        $params['repeat_call'] = 0;       //if the dap_call is not repeat in 30 days
        $params['repeat_dap_id'] = NULL;       //if the dap_call is not repeat in 30 days

        $checkExistSerial = DapService::where('serial', $params['serial'])->where('is_closed',1)->orderBy('id','DESC')->first();

        if(!empty($checkExistSerial)){
            $last_entry_date = $checkExistSerial->entry_date;
            $date1=date_create($last_entry_date);
            $date2=date_create(date('Y-m-d'));
            $diff=date_diff($date1,$date2);
            // $days = $diff->format("%d");
            $days = $diff->days;

            if($days <= 30){                
                // return redirect()->back()->withErrors(['serial' => 'You cannot add same item within 30 days'])->withInput();
                $params['repeat_call'] = 1;
                $params['repeat_dap_id'] = $checkExistSerial->id;
            }
        }

        // if($params['in_warranty'] == 0){ //Out OF Warranty
            $product = Product::find($params['product_id']);
            $repair_charge = $product->repair_charge;
            if(empty($repair_charge)){
                return redirect()->back()->withErrors(['item' => 'This goods has no repair charge for out of warranty servicing. Please set from product master'])->withInput();
            }
            $params['repair_charge'] = $repair_charge;
        // }
        $params['created_by'] = Auth::user()->id;

        $dap_data = DapService::create($params);
        $call_id = $dap_data->unique_id;
        $mobile = $dap_data->alternate_no;
        $item = $dap_data->item ?? "";
        if (strlen($item) > 30) {
            $item = substr($item, 0, 28) . '..';
        }
        
        // // Validate phone number
        $checkPhoneNumberValid = checkPhoneNumberValid($mobile);
        if ($checkPhoneNumberValid) {
            $query_calling_number = "6291117317";
            $sms_entity_id = getSingleAttributeTable('settings', 'id', 1, 'sms_entity_id');
            $sms_template_id = "1707172110551659557";
            
            $myMessage = urlencode('Call ID ' . $call_id . ' KGA is happy to help you to repair your product ' . $item . '. For tracking status contact KGA customer care - ' . $query_calling_number . ' AMMRTL');
            $sms_url = 'https://sms.bluwaves.in/sendsms/bulk.php?username=ammrllp&password=123456789&type=TEXT&sender=AMMRTL&mobile=' . $mobile . '&message=' . $myMessage . '&entityId=' . $sms_entity_id . '&templateId=' . $sms_template_id;
            
            $curl = curl_init();
            
            curl_setopt_array($curl, [
                CURLOPT_URL => $sms_url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
            ]);
            
            $response = curl_exec($curl);
            curl_close($curl);

            // Insert SMS API response into the database
            DB::table('sms_api_response')->insert([
                'sms_template_id' => $sms_template_id,
                'sms_entity_id' => $sms_entity_id,
                'phone' => $mobile,
                'message_body' => $myMessage,
                'response_body' => $response,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            
            Session::flash('message', 'Call Booked Successfully');
            return redirect()->route('dap-services.list');
        }else{
            Session::flash('message', 'Some thing went wrong! Please ');

        } 

    }

    /**
     * Generate Road Challan
    **/
    public function generate_road_challan(Request $request)
    {
        $dap_services = array();
        $branch_id = !empty($request->branch_id)?$request->branch_id:'';
        $branch_name = !empty($request->branch_name)?$request->branch_name:'';
        $sc = ServiceCentre::where('status', 1)->orderBy('name')->get()->toArray();
        if(!empty($branch_id)){
            $dap_services = DapService::where('branch_id',$branch_id)->where('is_reached_service_centre', 0)->get()->toArray();
        }
        return view('dap-services.gen-road-challan', compact('branch_id','branch_name','dap_services','sc'));
    }

    /**
     * Save Road Challan
    **/

    public function save_road_challan(Request $request)
    {
        $request->validate([
            'service_centre_id' => 'required',
            'challan_image' => 'required',
            'amount' => 'required'
        ],[
            'service_centre_id.required' => 'Please choose service centre',
            'challan_image.required' => 'Please check challan approved',
            'amount.required' => 'Please add amount'
        ]);
        $params = $request->except('_token');
        // dd($params);

        $dapReqReceiveDropsArr =  array(
            'entry_date' => date('Y-m-d'),
            'branch_id' => $params['branch_id'],
            'challan_image' => $params['challan_image'],
            'unique_id' => 'DAPREQCH'.genAutoIncreNoYearWiseOrder(7,'dap_request_receive_drops',date('Y'),date('m')),
            'amount' => $params['amount'],
            'service_centre_id' => $params['service_centre_id'],
            'created_at' => date('Y-m-d H:i:s')
        );
        $dap_request_receive_drop_id = DapRequestReceiveDrop::insertGetId($dapReqReceiveDropsArr);
        $dap_services = DapService::where('branch_id',$params['branch_id'])->where('is_reached_service_centre', 0)->get()->toArray();
        foreach($dap_services as $servies){

            $dapReqReceiveArr = array(
                'dap_request_receive_drop_id' => $dap_request_receive_drop_id,
                'dap_service_id' => $servies['id'],
                'service_centre_id' => $params['service_centre_id'],
                'product_id' => $servies['product_id'],
                'item' => $servies['item'],
                'barcode' => $servies['barcode'],
                'code_html' => $servies['code_html'],
                'code_base64_img' => $servies['code_base64_img'],
                'created_at' => date('Y-m-d H:i:s')
            );
            DapRequestReceives::insert($dapReqReceiveArr);
            DapService::where('id',$servies['id'])->update([
                'is_reached_service_centre' => 1
            ]);
        }
        $branch_name = getSingleAttributeTable('branches','id',$params['branch_id'],'name');

        Session::flash('message', 'Service Centre Items Reached To Centre Successfully');
        return redirect()->route('dap-services.centre-reached-items',['received_type'=>'notreceived','branch_id'=>$params['branch_id'],'branch_name'=>$branch_name,'service_centre_id'=>$params['service_centre_id']]);      

    }

    /**
     * Centre Reached Items
    **/

    public function centre_reached_items(Request $request)
    {
        # Received Items By Showroom...
        $entry_date = !empty($request->entry_date)?$request->entry_date:'';
        $search = !empty($request->search)?$request->search:'';
        $received_type = !empty($request->received_type)?$request->received_type:'';
        $branch_id = !empty($request->branch_id)?$request->branch_id:'';
        $branch_name = !empty($request->branch_name)?$request->branch_name:'';
        $service_centre_id = !empty($request->service_centre_id)?$request->service_centre_id:1;
        $sc = ServiceCentre::where('status', 1)->get()->toArray();
        $paginate = 20;
        
        $data = DapRequestReceives::select('*');
        $totalResult = DapRequestReceives::select('*');

        if(!empty($branch_id)){
            $data = $data->whereHas('dap_request', function($branch) use($branch_id){
                $branch->where('branch_id', $branch_id);
            });
            $totalResult = $totalResult->whereHas('dap_request', function($branch) use($branch_id){
                $branch->where('branch_id', $branch_id);
            });
        }
        if(!empty($search)){
            $data = $data->where('item', 'LIKE', '%'.$search.'%');
            $totalResult = $totalResult->where('item', 'LIKE', '%'.$search.'%');
        }
        if(!empty($service_centre_id)){
            $data = $data->where('service_centre_id', $service_centre_id);
            $totalResult = $totalResult->where('service_centre_id', $service_centre_id);
        }

        if(!empty($received_type)){
            if($received_type == 'received'){
                $data = $data->where('is_service_centre_received', 1);
                $totalResult = $totalResult->where('is_service_centre_received', 1);
            } else {
                $data = $data->where('is_service_centre_received', 0);
                $totalResult = $totalResult->where('is_service_centre_received', 0);
            }
        }

        $data = $data->orderBy('id','desc')->paginate($paginate);
        $totalResult = $totalResult->count();

        return view('dap-services.centre-reached-items', compact('sc','branch_id','branch_name','service_centre_id','entry_date','search','received_type','data','totalResult'));
    }

    /**
     * Centre Returned Items
    **/

    public function centre_returned_items(Request $request)
    {
        # code...
        $entry_date = !empty($request->entry_date)?$request->entry_date:'';
        $search = !empty($request->search)?$request->search:'';
        $closing_type = !empty($request->closing_type)?$request->closing_type:'';
        $branch_id = !empty($request->branch_id)?$request->branch_id:'';
        $branch_name = !empty($request->branch_name)?$request->branch_name:'';
        $service_centre_id = !empty($request->service_centre_id)?$request->service_centre_id:1;
        $sc = ServiceCentre::where('status', 1)->get()->toArray();
        $paginate = 20;
        
        $data = DapRequestReturnItem::select('*')->with('dap_request');
        $totalResult = DapRequestReturnItem::select('*');

        if(!empty($branch_id)){
            $data = $data->whereHas('return', function($branch) use($branch_id){
                $branch->where('branch_id', $branch_id);
            });
            $totalResult = $totalResult->whereHas('return', function($branch) use($branch_id){
                $branch->where('branch_id', $branch_id);
            });
        }
        if(!empty($search)){
            $data = $data->where('item', 'LIKE', '%'.$search.'%')->orWhereHas('dap_request', function($cust) use($search){
                $cust->where('customer_name','LIKE','%'.$search.'%')->orWhere('mobile','LIKE','%'.$search.'%')->orWhere('phone','LIKE','%'.$search.'%')->orWhere('unique_id','LIKE','%'.$search.'%')->orWhere('class_name','LIKE','%'.$search.'%')->orWhere('barcode','LIKE','%'.$search.'%');
            });
            $totalResult = $totalResult->where('item', 'LIKE', '%'.$search.'%')->orWhereHas('dap_request', function($cust) use($search){
                $cust->where('customer_name','LIKE','%'.$search.'%')->orWhere('mobile','LIKE','%'.$search.'%')->orWhere('phone','LIKE','%'.$search.'%')->orWhere('unique_id','LIKE','%'.$search.'%')->orWhere('class_name','LIKE','%'.$search.'%')->orWhere('barcode','LIKE','%'.$search.'%');
            });
        }
        if(!empty($service_centre_id)){
            $data = $data->where('service_centre_id', $service_centre_id);
            $totalResult = $totalResult->where('service_centre_id', $service_centre_id);
        }

        if(!empty($closing_type)){
            if($closing_type == 'closed'){
                $data = $data->whereHas('dap_request', function($cl){
                    $cl->where('is_closed', 1);
                });
                $totalResult = $totalResult->whereHas('dap_request', function($cl){
                    $cl->where('is_closed', 1);
                });
            } else {
                $data = $data->whereHas('dap_request', function($cl){
                    $cl->where('is_closed', 0);
                });
                $totalResult = $totalResult->whereHas('dap_request', function($cl){
                    $cl->where('is_closed', 0);
                });
            }
        }

        $data = $data->orderBy('id','desc')->paginate($paginate);
        $totalResult = $totalResult->count();

        // dd($data);

        return view('dap-services.centre-returned-items', compact('sc','branch_id','branch_name','service_centre_id','entry_date','search','closing_type','data','totalResult'));
    }

    /**
     * Close Call
    **/

    public function make_close($idStr,$getQueryString='')
    {
        # Close The Call ...
        try {
            $id = Crypt::decrypt($idStr);
            $data = DapService::where('id',$id)->update(['is_closed'=>1]);

            Session::flash('message', "Call closed successfully.");
            return redirect('/dap-services/centre-returned-items?'.$getQueryString);

        } catch ( DecryptException $e) {
            return abort(404);
        }
    }

    /**
     * Make Call Paid (If out of warranty)
    **/

    public function make_paid(Request $request)
    {
        $params = $request->except('_token');
        // dd($params);
        
        DapService::where('id', $params['dap_service_id'])->update([
            'payment_method' => $params['payment_method'],
            'is_paid' => $params['is_paid']
        ]);
        
        Session::flash('message', "Paid successfully.");
        return redirect('/dap-services/centre-returned-items?'.$params['request_url']);
        
        
    }
    
    public function send_service_centre(){
        $paginate = 20;
        $data = DapService::with('servicePartner')->orderBy('id', 'desc')->paginate($paginate);
        $totalResult = DapService::count();
        $serviceCentres = ServiceCentre::get();

        return view('dap-services.call_book_dap', compact('data','totalResult','serviceCentres'));
    }
    public function generate_road_challan_new(Request $request){

        DapService::where('id', $request->dap_id)->update([
            'wearhouse_id' => $request->wearhouse,
            'vehicle_number' => $request->vehicle_number,
            'is_dispatched_from_branch' => 1,
			'is_dispatched_from_branch_date' => now()
        ]);

        return redirect()->back();
    }
    public function download_road_challan_new($barcode){

        $dapService = DapService::with('branch','serviceCentre','product')->where('unique_id', $barcode)->first();
        // Check if the record exists
        if ($dapService) {
            // Convert the record to an array
            $data = $dapService->toArray();
            $date = date('d-M-y');
            $pdf = Pdf::loadView('dap-services.download-road-challan', compact('data','date'));
            return $pdf->download('Road-Challan.pdf');
        }
    }
    public function barcode($idStr)
    {
        $id = Crypt::decrypt($idStr);
        $data = DapService::find($id);
        return view('dap-services.dap-barcode', compact('data'));
    }
    public function dap_quotation($idStr)
    {
        $id = Crypt::decrypt($idStr);
        $data = DapService::find($id);
        $parts_data =DapSpearPartOrder::where('dap_id',$id)->get();
        $discount_request_data =DapDiscountRequest::where('dap_id',$id)->first();
        
        return view('dap-services.dap-quotation', compact('data','parts_data','discount_request_data'));
    }
    public function dap_track($idStr)
    {
        $id = Crypt::decrypt($idStr);
        $data = DapService::with('branch','return_branch','servicePartner','callBookedEmployee','dapProductDispatchedEmployee','serviceCentre','product','FinalSpareParts')->find($id);
        return view('dap-services.dap-track', compact('data'));
    }
    public function dap_discount_amount_request_approved(Request $request){
        $discount_request_data =DapDiscountRequest::where('dap_id',$request->dap_id)->first();
        $discount_request_data->approval_amount =$request->approval_amount;
        $discount_request_data->status =1;
        $discount_request_data->approval_by =Auth::user()->id;
        $discount_request_data->save();
        if($discount_request_data){
            $dap_data = DapService::findOrFail($discount_request_data->dap_id);
            $dap_data->discount_amount=$request->approval_amount;
            $dap_data->save();

            return redirect()->back()->with('success','Discount request amount approved');
        }

   
      
    }
    public function payment_history(Request $request){
    $startDate = $request->start_date ?? '';
    $endDate = $request->end_date ?? '';
    $keyword = $request->keyword ?? '';
    $paginate = 20;

    if (!empty($keyword) || !empty($startDate) || !empty($endDate)) {   
        $query = DapServicePayment::query();

        $query->when($keyword, function ($query) use ($keyword) {
            $query->where('payment_id', 'like', '%' . $keyword . '%')
                ->orWhere('customer_name', 'like', '%' . $keyword . '%')
                ->orWhere('customer_phone', 'like', '%' . $keyword . '%')
                ->orWhere('amount', 'like', '%' . $keyword . '%');
        }); // Move the status condition here

        if (!is_null($startDate) && !is_null($endDate)) {
            $query->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $query->where('created_at', '>=', $startDate . " 00:00:00")
                    ->where('created_at', '<=', date("Y-m-d 23:59:59", strtotime($endDate)));
            });
        }
        
        $data = $query->orderBy('id','DESC')->paginate($paginate);  

    }else{
        $data = DapServicePayment::orderBy('id','DESC')->paginate($paginate);    
    }
        return view('dap-services.payment_history',compact('data','paginate'));
    }
    public function reassign_engineer(Request $request)
    {

        $update =  DapService::findOrFail($request->id);
        $update->assign_service_perter_id =$request->service_partner;
        $update->save();
        if($update){
            Session::flash('message', 'Engineer re-assign Successfully');
            return response()->json(['status'=>200]);
        }else{
            Session::flash('message', 'Something went worng.');
            return response()->json(['status'=>400]);
        }
    }

    public function dap_invoice($idStr){
        $id = crypt::decrypt($idStr);
            // $dap = DapService::find($id);
                $data = DapService::with('branch','return_branch','paymentData')->find($id);
                if($data){
                    $parts_data  = DapSpearPartFinalOrder::with('productData')->where('dap_id',$id)->get();
                    $Todate = date('d-M-y');
                    $pdf = Pdf::loadView('dap-services.dap_invoice_update', compact('data','parts_data','Todate'));
                    return $pdf->download('DAP Invoice.pdf');
                }
    }

}
