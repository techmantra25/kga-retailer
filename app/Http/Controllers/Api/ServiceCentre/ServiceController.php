<?php

namespace App\Http\Controllers\Api\ServiceCentre;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\DB;
use App\Models\ServiceCentre;
use App\Models\Branch;
use App\Models\DapService;
use App\Models\DapRequestReceives;
use App\Models\DapRequestReceiveDrop;
use App\Models\DapServiceSpare;
use App\Models\SpareGoods;
use App\Models\PurchaseOrderProduct;
use App\Models\Product;
use App\Models\DapServiceCustomerApproval;
use App\Models\DapRequestReturn;
use App\Models\DapRequestReturnItem;

class ServiceController extends Controller
{
    
    private $service_centre_id;
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
                $this->service_centre_id = Crypt::decrypt($token);
                $staff = ServiceCentre::find($this->service_centre_id);           
            } catch (DecryptException $e) {
                response()->json(["status"=>false,"message"=>"Mismatched token"],400)->send();
                exit();
            }
        }
    }

    /**
     * List Service Calls.
     * GET
    **/

    public function list(Request $request)
    {
        # code...
        $service_centre_id = $this->service_centre_id;

        $validator = Validator::make($request->all(),[
            'branch_id' => 'required|exists:branches,id',
            'receiving_status' => 'nullable|in:yes,no',
            'search' => 'nullable'
        ]);

        if(!$validator->fails()){
            $branch_id = $request->branch_id;
            $receiving_status = !empty($request->receiving_status)?$request->receiving_status:'no';
            $search = !empty($request->search)?$request->search:'';
            $take = !empty($request->take)?$request->take:20;  ## pagination
            $page = isset($request->page)?$request->page:0;  ## pagination
            $skip = ($take*$page);  ## pagination

            $services = DapRequestReceives::select('id','dap_request_receive_drop_id','dap_service_id','service_centre_id','product_id','item','barcode','is_scanned','is_service_centre_received','is_repaired','is_done','is_returned')->with('dap_request:id,unique_id,branch_id,in_warranty,customer_name,mobile,phone','dap_request_receive_drops:id,unique_id,entry_date,amount')->where('service_centre_id',$service_centre_id);
            $countService = DapRequestReceives::where('service_centre_id',$service_centre_id);

            if(!empty($branch_id)){
                $services = $services->whereHas('dap_request', function($branch) use ($branch_id){
                    $branch->where('branch_id', $branch_id);
                });

                $countService = $countService->whereHas('dap_request', function($branch) use ($branch_id){
                    $branch->where('branch_id', $branch_id);
                });
            }

            if($receiving_status == 'no'){
                $services = $services->where('is_service_centre_received', 0);
                $countService = $countService->where('is_service_centre_received', 0);
            } else if ($receiving_status == 'yes'){
                $services = $services->where('is_service_centre_received', 1);
                $countService = $countService->where('is_service_centre_received', 1);
            }

            $services = $services->orderBy('id','desc')->skip($skip)->take($take)->get();
            $countService = $countService->count();

            return Response::json([
                'status' => true, 
                'message' => "Open Call List ",
                'data' => array(
                    'countService' => $countService,
                    'services' => $services
                )
            ],200);
            
        } else {
            return Response::json(['status' => false, 'message' => $validator->errors()->first() , 'data' => array( $validator->errors() ) ],400);
        }
    }

    /**
     * Receive Items. 
     * POST
    **/

    public function receive_items(Request $request)
    {
        # Receive Items...

        $validator = Validator::make($request->all(),[
            'dap_request_receive_drop_id' => 'required|exists:dap_request_receive_drops,id',
            // 'dap_service_ids' => 'required|array'
        ]);

        if(!$validator->fails()){

            $params = $request->except('_token');
            $dap_request_receive_drop_id = $params['dap_request_receive_drop_id'];
            // $dap_service_ids = $params['dap_service_ids'];

            
            DapRequestReceives::where('dap_request_receive_drop_id',$dap_request_receive_drop_id)->update([
                'is_service_centre_received' => 1
            ]);

            return Response::json([
                'status' => true, 
                'message' => "Items Received Successfully",
                'data' => array(
                    'params' => $params
                )
            ],200);


        } else {
            return Response::json(['status' => false, 'message' => $validator->errors()->first() , 'data' => array( $validator->errors() ) ],400);
        }

    }


    /**
     * Search Item Spares.
     * GET
    **/

    public function search_item_spares(Request $request)
    {
        # Search & Get Spare List for DAP Item...

        $validator = Validator::make($request->all(),[
            'goods_id' => 'required|exists:products,id',
            'search' => 'nullable'
        ]);

        if(!$validator->fails()){
            $goods_id = !empty($request->goods_id)?$request->goods_id:'';  # -- product id (type: fg)
            $search = !empty($request->search)?$request->search:'';
            $idnotin = !empty($request->idnotin)?$request->idnotin:array();

            $spare_goods = SpareGoods::select('id','goods_id','spare_id')->with(['spare' => function($spare){
                $spare->select('id','title','type');
            }])->where('goods_id',$goods_id);
            
            if(!empty($search)){
                $spare_goods = $spare_goods->whereHas('spare', function($sp) use($search){
                    $sp->where('title', 'LIKE', '%'.$search.'%');
                });
            }
            if(!empty($idnotin)){
                $spare_goods = $spare_goods->whereNotIn('spare_id', $idnotin);
            }
            $spare_goods = $spare_goods->get()->toArray();

            return Response::json([
                'status' => true, 
                'message' => "Available Spares For Item ",
                'data' => array(
                    'countSpares' => count($spare_goods),
                    'spares' => $spare_goods
                )
            ],200);


        } else {
            return Response::json(['status' => false, 'message' => $validator->errors()->first() , 'data' => array( $validator->errors() ) ],400);
        }

    }

    /**
     * Set Spares For The Repaired Item.
     * POST
    **/

    public function set_spares(Request $request)
    {
        # Set Spare(s) For Repaired Item If Required...
        
        $validator = Validator::make($request->all(),[
            'dap_service_id' => 'required|exists:dap_services,id',
            'details.*.spare_id' => 'required|exists:products,id',
            'details.*.quantity' => 'required|numeric|max:2'
        ]);

        if(!$validator->fails()){
            $params = $request->except('_token');            
            $dap_service_id = $params['dap_service_id'];

            $dap_service = DapService::find($dap_service_id);
            $in_warranty = $dap_service->in_warranty;
            $repair_charge = $dap_service->repair_charge;

            if(!empty($dap_service->is_spare_required)){
                return Response::json([
                    'status' => false, 
                    'message' => "Spares are already added for this item.",
                    'data' => (object) array()
                ],200);
            }
          
            if( empty($repair_charge)){
                return Response::json([
                    'status' => false, 
                    'message' => "Repair charge for the item is not added from product master. Please talk to admin",
                    'data' => (object) array()
                ],200);
            }

            $details = $params['details'];
            $items = json_decode($details);
            // dd($items);

            $total_spare_charge = 0;
            $itemDetails = array();
            foreach($items as $item){
                $spare_id = isset($item->spare_id)?$item->spare_id:NULL;
                $quantity = isset($item->quantity)?$item->quantity:NULL;

                # Check Details Params

                if(empty($spare_id) || empty($quantity)){
                    return Response::json([
                        'status' => false, 
                        'message' => "Please check params for details",
                        'data' => (object) array()
                    ],200);
                }

                # Check Spare ...
                $spare = Product::find($spare_id);
                if(!empty($spare) && ($spare->type != 'sp')){
                    return Response::json([
                        'status' => false, 
                        'message' => "Unknown Spare Parts. Please check spare id :- ".$spare_id,
                        'data' => (object) array()
                    ],200);
                }
                $highest_spare_cost_price_po = PurchaseOrderProduct::where('product_id',$spare_id)->max('cost_price');

                $spare_profit_percentage = getSingleAttributeTable('products','id',$spare_id,'profit_percentage');

                $spare_name = getSingleAttributeTable('products','id',$spare_id,'title');

                if(empty($highest_spare_cost_price_po)){
                    return Response::json([
                        'status' => false, 
                        'message' => "No Stock Price Found For ".$spare_name." . Please talk to System Admin",
                        'data' => (object) array()
                    ],200);
                }

                if(empty($spare_profit_percentage)){
                    return Response::json([
                        'status' => false, 
                        'message' => "No Profit Percentage Added For Spare From Master . Please talk to System Admin",
                        'data' => (object) array()
                    ],200);
                }

                $itemDetails[] = array(
                    'spare_id' => $spare_id,
                    'quantity' => $quantity
                );

                
            }
            // dd($itemDetails);
            foreach($itemDetails as $item){

                $highest_spare_cost_price_po = PurchaseOrderProduct::where('product_id',$item['spare_id'])->max('cost_price');                
                $spare_profit_percentage = getSingleAttributeTable('products','id',$item['spare_id'],'profit_percentage');

                $highest_spare_cost_price_po = !empty($highest_spare_cost_price_po)?$highest_spare_cost_price_po:0;
                $spare_profit_val = getPercentageVal($spare_profit_percentage,$highest_spare_cost_price_po);
                $spare_price = ($highest_spare_cost_price_po + $spare_profit_val);
                $spare_charge = ($item['quantity']*$spare_price);
                $total_spare_charge += $spare_charge;

                $dapServiceSpareArr = array(
                    'dap_service_id' => $dap_service_id,
                    'spare_id' => $item['spare_id'],
                    'quantity' => $item['quantity'],
                    'cost_price' => $highest_spare_cost_price_po,
                    'profit_percentage' => $spare_profit_percentage,
                    'spare_profit_val' => $spare_profit_val,
                    'total_spare_charge' => $spare_charge,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                );

                DapServiceSpare::insert($dapServiceSpareArr);
            }
            $total_service_charge = ($repair_charge+$total_spare_charge);
            $dapServiceUpdateArr = array(
                'is_spare_required' => 1,
                'repair_charge' => $repair_charge,
                'spare_charge' => $total_spare_charge,
                'total_service_charge' => $total_service_charge,
            );
            DapService::where('id',$dap_service_id)->update($dapServiceUpdateArr);


            return Response::json([
                'status' => true, 
                'message' => "Spares added successfully",
                'data' => array(                    
                    'params' => $params
                )
            ],200);


        } else {
            return Response::json(['status' => false, 'message' => $validator->errors()->first() , 'data' => array( $validator->errors() ) ],400);
        }

        
    }

    /**
     * Make Complete Repair Item.
     * POST
    **/

    public function complete_repair(Request $request)
    {
        # Make Complete Repairing Received Item...
        $service_centre_id = $this->service_centre_id;

        $validator = Validator::make($request->all(),[
            'branch_id' => 'required|exists:branches,id',
            'dap_service_id' => 'required|exists:dap_services,id'
        ]);

        if(!$validator->fails()){
            $params = $request->except('_token');
            $branch_id = $params['branch_id'];
            $dap_service_id = $params['dap_service_id'];

            $checkIsClosed = DapRequestReceives::where('dap_service_id',$dap_service_id)->first();

            if(!empty($checkIsClosed)){
                if($checkIsClosed->service_centre_id != $service_centre_id){
                    return Response::json([
                        'status' => false, 
                        'message' => "Unknown Service Centre",
                        'data' => (object) array()
                    ],200);
                }
                if($checkIsClosed->dap_request->branch_id != $branch_id){
                    return Response::json([
                        'status' => false, 
                        'message' => "Unknown Showroom Item",
                        'data' => (object) array()
                    ],200);
                }
                if(empty($checkIsClosed->is_repaired)){
                    DapRequestReceives::where('dap_service_id',$dap_service_id)->update([
                        'is_repaired' => 1,
                        'is_done' => 1,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);

                    
                    $in_warranty = $checkIsClosed->dap_request->in_warranty;
                    if(empty($in_warranty)){
                        $repair_charge = $checkIsClosed->dap_request->repair_charge;
                        $spare_charge = !empty($checkIsClosed->dap_request->spare_charge)?$checkIsClosed->dap_request->spare_charge:0;

                        $total_service_charge = ($repair_charge + $spare_charge);

                        DapService::where('id',$dap_service_id)->update([
                            'repair_charge' => $repair_charge,
                            'total_service_charge' => $total_service_charge,
                        ]);
                        
                    }
                    
                    

                    return Response::json([
                        'status' => true, 
                        'message' => "Item repaired successfully",
                        'data' => array(
                            'total_service_charge' => $total_service_charge
                        )
                    ],200);
                } else {
                    return Response::json([
                        'status' => false, 
                        'message' => "Already repaired",
                        'data' => (object) array()
                    ],200);
                }
            } else {
                return Response::json([
                    'status' => false, 
                    'message' => "Unknown Repair Request",
                    'data' => (object) array()
                ],200);
            }


        } else {
            return Response::json(['status' => false, 'message' => $validator->errors()->first() , 'data' => array( $validator->errors() ) ],400);
        }
        
    }

    /**
     * Repaired Items
     * GET
    **/

    public function repaired_list(Request $request)
    {
        # Repaired / Not Repaired (Refuse To Repair By Customer) List Which Should Be Returned To Showroom ...
        $service_centre_id = $this->service_centre_id;

        $validator = Validator::make($request->all(),[
            'branch_id' => 'required|exists:branches,id',
            'returning_status' => 'nullable|in:yes,no',
            'search' => 'nullable'
        ]);

        if(!$validator->fails()){
            $branch_id = $request->branch_id;
            $search = !empty($request->search)?$request->search:'';
            $returning_status = !empty($request->returning_status)?$request->returning_status:'no';

            $take = !empty($request->take)?$request->take:20;  ## pagination
            $page = isset($request->page)?$request->page:0;  ## pagination
            $skip = ($take*$page);  ## pagination

            $services = DapRequestReceives::select('id','dap_request_receive_drop_id','dap_service_id','service_centre_id','product_id','item','barcode','is_returned')->with('dap_request:id,branch_id')->whereHas('dap_request', function($dap) use ($branch_id){
                $dap->where('branch_id', $branch_id);
            })->where('is_done', 1);

            $countServices = DapRequestReceives::whereHas('dap_request', function($dap) use ($branch_id){
                $dap->where('branch_id', $branch_id);
            })->where('is_done', 1);

            

            if($returning_status == 'no'){
                $services = $services->where('is_returned', 0);
                $countServices = $countServices->where('is_returned', 0);
            } else {
                $services = $services->where('is_returned', 1);
                $countServices = $countServices->where('is_returned', 1);
            }            

            $services = $services->orderBy('updated_at','desc')->skip($skip)->take($take)->get();
            $countServices = $countServices->count();

            return Response::json([
                'status' => true, 
                'message' => "Repaired Call List ",
                'data' => array(
                    'countService' => $countServices,
                    'services' => $services
                )
            ],200);



        } else {
            return Response::json(['status' => false, 'message' => $validator->errors()->first() , 'data' => array( $validator->errors() ) ],400);
        }

        
    }

    /**
     * Make Return Items
     * POST
    **/

    public function return_items(Request $request)
    {
        # Return Repaired Items By Branch (Showroom)...

        $service_centre_id = $this->service_centre_id;

        $validator = Validator::make($request->all(),[
            'branch_id' => 'required|exists:branches,id',
            'amount' => 'required'
            // 'dap_service_ids' => 'required|array'
        ]);

        if(!$validator->fails()){
            $params = $request->except('_token');
            $branch_id = $params['branch_id'];
            $amount = $params['amount'];   ### Challan Amount
            
            $services = DapRequestReceives::where('service_centre_id', $service_centre_id)->whereHas('dap_request', function($dap_request) use($branch_id){
                $dap_request->where('branch_id', $branch_id);
            })->where('is_done', 1)->where('is_returned', 0)->get()->toArray();

            if(!empty($services)){
                $unique_id = 'DAPRET'.genAutoIncreNoYearWiseOrder(4,'dap_request_returns',date('Y'),date('m'));
                // dd($unique_id);
                $dapReqRetArr = array(
                    'unique_id' => $unique_id,
                    'entry_date' => date('Y-m-d'),
                    'branch_id' => $branch_id,
                    'service_centre_id' => $service_centre_id,
                    'amount' => $amount,
                    'created_at' => date('Y-m-d H:i:s')
                );
                $dap_request_return_id = DapRequestReturn::insertGetId($dapReqRetArr);
                
                foreach($services as $service){
                    // dd($service['barcode']);
                    $dapReqRetItemArr = array(
                        'dap_request_return_id' => $dap_request_return_id,
                        'dap_service_id' => $service['dap_service_id'],
                        'service_centre_id' => $service_centre_id,
                        'product_id' => $service['product_id'],
                        'item' => $service['item'],
                        'barcode' => $service['barcode'],
                        'created_at' => date('Y-m-d H:i:s')
                    );
                    DapRequestReturnItem::insert($dapReqRetItemArr);
                    DapRequestReceives::where('dap_service_id',$service['dap_service_id'])->update([
                        'is_returned' => 1
                    ]);
                }

                $message = "Returned Successfully";
            } else {
                $message = "No new items found to return";
            }

            // dd($services);

            return Response::json([
                'status' => true, 
                'message' => $message,
                'data' => array(
                    
                )
            ],200);


        } else {
            return Response::json(['status' => false, 'message' => $validator->errors()->first() , 'data' => array( $validator->errors() ) ],400);
        }


    }

    /**
     * Set Customer Calling Status For OUT OF WARRANTY Item Repairing
     * POST
    **/

    public function set_customer_calling_status(Request $request)
    {
        # If Customer Calling Status not 'answered' And `is_otp_validated` is 0 Set `is_done` => 1 For `dap_service_id` of `dap_request_receives` table   ...
        $service_centre_id = $this->service_centre_id;

        $validator = Validator::make($request->all(),[
            'dap_service_id' => 'required|exists:dap_services,id',
            'cust_calling_status' => 'required|in:not_received,answered,declined,refuse_to_repair'
        ]);

        if(!$validator->fails()){

            $params = $request->except('_token');

            $dap_service_id = $params['dap_service_id'];
            $cust_calling_status = $params['cust_calling_status'];

            $dap_request = DapService::find($dap_service_id);

            $dap_request_receives = DapRequestReceives::where('dap_service_id',$dap_service_id)->first();

            if(empty($dap_request_receives->is_service_centre_received)){
                return Response::json([
                    'status' => false, 
                    'message' => "Item has not received to service centre properly",
                    'data' => (object) array()
                ],200);
            }
            if(!empty($dap_request_receives->is_repaired)){
                return Response::json([
                    'status' => false, 
                    'message' => "Item has been repaired already",
                    'data' => (object) array()
                ],200);
            }
            if(!empty($dap_request_receives->is_done)){
                return Response::json([
                    'status' => false, 
                    'message' => "Item has been marked done already",
                    'data' => (object) array()
                ],200);
            }
            if(!empty($dap_request_receives->is_returned)){
                return Response::json([
                    'status' => false, 
                    'message' => "Item has been returned to branch already",
                    'data' => (object) array()
                ],200);
            }
            if(!empty($dap_request_receives->is_returned)){
                return Response::json([
                    'status' => false, 
                    'message' => "Item has been returned to branch already",
                    'data' => (object) array()
                ],200);
            }
            if(!empty($dap_request->is_cancelled)){
                return Response::json([
                    'status' => false, 
                    'message' => "Request has been cancelled",
                    'data' => (object) array()
                ],200);
            }
            if(!empty($dap_request->is_closed)){
                return Response::json([
                    'status' => false, 
                    'message' => "Request has been closed",
                    'data' => (object) array()
                ],200);
            }



            $dap_service_customer_approvals = DapServiceCustomerApproval::where('dap_service_id',$dap_service_id)->first();

            if(!empty($dap_service_customer_approvals)){
                DapServiceCustomerApproval::where('dap_service_id',$dap_service_id)->update([
                    'cust_calling_status' => $cust_calling_status,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            } else {
                $customerApprovalArr = array(
                    'dap_service_id' => $dap_service_id,
                    'product_id' => $dap_request->product_id,
                    'service_centre_id' => $service_centre_id,
                    'item' => $dap_request->item,
                    'customer_name' => $dap_request->customer_name,
                    'customer_mobile' => $dap_request->mobile,
                    'customer_phone' => $dap_request->phone,
                    'cust_calling_status' => $cust_calling_status,
                    'created_at' => date('Y-m-d H:i:s'),
                );
                DapServiceCustomerApproval::insert($customerApprovalArr);
            }

            # If Customer Calling Status not 'answered' Set Request Status Done

            if($cust_calling_status != 'answered'){
                DapRequestReceives::where('dap_service_id',$dap_service_id)->update([
                    'is_done' => 1
                ]);

                ## This Call don't need to close from master dashboard due to decline , not receive or refuse to repair from customer's end. This call is denoted as cancelled due to this reason. .


                DapService::where('id',$dap_service_id)->update([
                    'is_cancelled' => 1,
                    'is_closed' => 1
                ]);  
                
            }

            return Response::json([
                'status' => true, 
                'message' => "Set Customer Calling Status For Repairing Item Successfully",
                'data' => array(
                    'params' => $params
                )
            ],200);

        } else {
            return Response::json(['status' => false, 'message' => $validator->errors()->first() , 'data' => array( $validator->errors() ) ],400);
        }



    }

    /**
     * Request Customer For Repairing OTP
     * POST
    **/

    public function request_repairing_otp(Request $request)
    {
        # Send OTP To Customer Behalf Of The DAP Service Request ...
        # Get Goods Repair Charge (If Spare Not Added)
        

        $service_centre_id = $this->service_centre_id;

        $validator = Validator::make($request->all(),[
            'dap_service_id' => 'required|exists:dap_services,id',
        ]);

        if(!$validator->fails()){
            $params = $request->except('_token');
            $dap_service_id = $params['dap_service_id'];

            $dap_request = DapService::find($dap_service_id);
            $in_warranty = $dap_request->in_warranty;
            if(!empty($in_warranty)){
                return Response::json([
                    'status' => false, 
                    'message' => "OTP is not required for in warranty service.",
                    'data' => (object) array()
                ],200);
            }
            $repair_charge = !empty($dap_request->repair_charge)?$dap_request->repair_charge:0;
            $spare_charge = !empty($dap_request->spare_charge)?$dap_request->spare_charge:0;
            $total_service_charge = ($repair_charge+$spare_charge);

            $dap_service_customer_approvals = DapServiceCustomerApproval::where('dap_service_id',$dap_service_id)->first();

            $repairing_otp = random_int(100000, 999999);
            $repairing_otp_expired_at = date('Y-m-d H:i', strtotime("+7 days"));

            if(!empty($dap_service_customer_approvals)){

                if(!empty($dap_service_customer_approvals->is_otp_validated)){
                    return Response::json([
                        'status' => false, 
                        'message' => "OTP is already validated",
                        'data' => (object) array()
                    ],200);
                }

                DapServiceCustomerApproval::where('dap_service_id',$dap_service_id)->update([
                    'repairing_otp' => $repairing_otp,
                    'repairing_otp_expired_at' => $repairing_otp_expired_at,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            } else {
                $customerApprovalArr = array(
                    'dap_service_id' => $dap_service_id,
                    'product_id' => $dap_request->product_id,
                    'service_centre_id' => $service_centre_id,
                    'item' => $dap_request->item,
                    'customer_name' => $dap_request->customer_name,
                    'customer_mobile' => $dap_request->mobile,
                    'customer_phone' => $dap_request->phone,
                    'repairing_otp' => $repairing_otp,
                    'repairing_otp_expired_at' => $repairing_otp_expired_at,
                    'created_at' => date('Y-m-d H:i:s'),
                );
                DapServiceCustomerApproval::insert($customerApprovalArr);
            }

            ## Get DAP Item Repair Charge & Spare Charge 

            // $this->sendOTPCustomer($dap_service_id,$otp,$total_service_charge);


            return Response::json([
                'status' => true, 
                'message' => "OTP generated for customer to start repair item. Please validate OTP within 7 days.",
                'data' => array(
                    'repairing_otp' => $repairing_otp,
                    'repairing_otp_expired_at' => $repairing_otp_expired_at
                )
            ],200);

        } else {
            return Response::json(['status' => false, 'message' => $validator->errors()->first() , 'data' => array( $validator->errors() ) ],400);
        }
    }

    /**
     * Validate Repairing OTP
     * POST
    **/

    public function validate_repairing_otp(Request $request)
    {
        # Validate Repairing OTP...

        $service_centre_id = $this->service_centre_id;

        $validator = Validator::make($request->all(),[
            'dap_service_id' => 'required|exists:dap_services,id',
            'repairing_otp' => 'required'
        ]);

        if(!$validator->fails()){

            $params = $request->except('_token');
            $dap_service_id = $params['dap_service_id'];
            $repairing_otp = $params['repairing_otp'];

            $data = DapServiceCustomerApproval::where('dap_service_id',$dap_service_id)->first();

            $now = date('Y-m-d H:i');

            if($now <= $data->repairing_otp_expired_at){
                if($data->repairing_otp == $repairing_otp){
                    DapServiceCustomerApproval::where('dap_service_id',$dap_service_id)->update([
                        'repairing_otp' => null,
                        'repairing_otp_expired_at' => null,
                        'is_otp_validated' => 1
                    ]);

                    return Response::json([
                        'status' => true, 
                        'message' => "OTP validated successfully for initiating the item repairs.",
                        'data' => array(

                        )
                    ],200);

                } else {
                    return Response::json([
                        'status' => false, 
                        'message' => "OTP Mismatched",
                        'data' => (object) array()
                    ],200);
                }
            } else {
                return Response::json([
                    'status' => false, 
                    'message' => "OTP Expired",
                    'data' => (object) array()
                ],200);
            }

        } else {
            return Response::json(['status' => false, 'message' => $validator->errors()->first() , 'data' => array( $validator->errors() ) ],400);
        }


    }

    /**
     * All Repaireable Items. 
     * POST
    **/

    public function repairable_items(Request $request)
    {
        $service_centre_id = $this->service_centre_id;

        $take = !empty($request->take)?$request->take:20;  ## pagination
        $page = isset($request->page)?$request->page:0;  ## pagination
        $skip = ($take*$page);  ## pagination

        $services = DapRequestReceives::select('id','dap_request_receive_drop_id','dap_service_id','product_id','item','barcode')->selectRaw("DATE_FORMAT(created_at,'%Y-%m-%d') AS created_date")->with(['dap_request' => function($d){
            $d->select('id','unique_id','in_warranty','customer_name','mobile','phone','branch_id')->with('branch:id,name');
        }])->where('service_centre_id',$service_centre_id)->where('is_done', 0);
        $countService = DapRequestReceives::where('service_centre_id',$service_centre_id)->where('is_done', 0);
        
        $services = $services->orderBy('id','asc')->skip($skip)->take($take)->get();

        $countService = $countService->count();

        return Response::json([
            'status' => true, 
            'message' => "Repairable All Items",
            'data' => array(
                'countService' => $countService,
                'services' => $services
            )
        ],200);

    }

    private function sendOTPCustomer($dap_service_id,$otp,$total_service_charge){
        ##  Send OTP To Customer With Charges and Online Payment Link

        $dap_request = DapService::find($dap_service_id);
        // $customer_mobile_no = $dap_request->mobile;
        $customer_mobile_no = "8961741161";
        $sms_entity_id = getSingleAttributeTable('settings','id',1,'sms_entity_id');
        $sms_template_id = "1707169579011343699";
        
        
        $payment_link = "https://payu.in/";

        $myMessage = urlencode('"Your KGA Product is out of warranty & has been examined by KGA Engineers. The Total Repair Charge is Rs.'.$total_service_charge.' Your OTP to repair is '.$otp.' Please share OTP to approve this repair charge and begin repair. You can click on this link to make online payment '.$payment_link.' or pay cash while collecting your repaired product."AMMRTL');

        $sms_url = 'https://sms.bluwaves.in/sendsms/bulk.php?username=ammrllp&password=123456789&type=TEXT&sender=AMMRTL&mobile='.$customer_mobile_no.'&message='.$myMessage.'&entityId='.$sms_entity_id.'&templateId='.$sms_template_id;

        // // echo $myMessage; die;

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $sms_url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        // echo '<pre>'; echo $response;

        DB::table('sms_api_response')->insert([
            'sms_template_id' => $sms_template_id,
            'sms_entity_id' => $sms_entity_id,
            'phone' => $customer_mobile_no,
            'message_body' => $myMessage,
            'response_body' => $response,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);



    }



}
