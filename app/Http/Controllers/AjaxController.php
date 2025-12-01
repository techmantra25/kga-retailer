<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\PurchaseOrderBarcode;
use App\Models\PurchaseOrderProduct;
use App\Models\StockInventory;
use App\Models\Category;
use App\Models\StockBarcode;
use App\Models\Dealer;
use App\Models\ServicePartnerPincode;
use App\Models\ServicePartner;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\ProductAmc;
use App\Models\GoodsWarranty;
use App\Models\SpareGoods;
use App\Models\Maintenance;
use App\Models\ProductWarranty;
use App\Models\Branch;
use App\Models\AmcSubscription;
use App\Models\CustomerPointServicePartnerPincode;
use App\Models\SalesOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;



class AjaxController extends Controller
{
    public function __construct(Request $request)
    {
        # code...
    }

    public function subcategory_by_category(Request $request)
    {
        # Get Subcategory By Category Id...
        
        $cat_id = !empty($request->cat_id)?$request->cat_id:'';        
        $category = Category::find($cat_id);

        $subcats = Category::where('status', 1)->where('parent_id',$cat_id)->orderBy('name','asc')->get();

        
        return ['subcats'=>$subcats];
    }

    public function category_by_product_type(Request $request)
    {
        $product_type = !empty($request->product_type)?$request->product_type:'';
        $category = Category::where('product_type',$product_type)->whereNull('parent_id')->orderBy('name')->get();
        return ['category'=>$category];
    }

 
    public function get_chimnney_repairing_repeat_call(Request $request)
    {
        $repeat_call = 0;
        $serial_no = $request->serial_no;
        $selectedServiceType = $request->selectedServiceType;
        $checkExistSerial = Maintenance::where('product_sl_no', $serial_no)->where('service_type',$selectedServiceType)->where('is_closed',1)->orderBy('id','DESC')->first();
        

        if (!empty($checkExistSerial)) {
            // Extract the date part only (ignoring the time)
            $last_entry_date = $checkExistSerial->created_at->format('Y-m-d');
            // Create DateTime objects for comparison
            $date1 = date_create($last_entry_date);
            $date2 = date_create(date('Y-m-d'));
            
            // Calculate the difference between the two dates
            $diff = date_diff($date1, $date2);
            // $days = $diff->format("%a"); // %a gives total number of days
            $days = $diff->days;
        
            if ($days <= 30) {
                $repeat_call = 1;       //if the dap_call is not repeat in 30 days
            }
        }

        return response()->json(['repeat_call' => $repeat_call]);

    }
    public function search_product_for_amc(Request $request)
    {
        // dd($request->all());
        # search product by title for AMC...
        $search = !empty($request->search)?$request->search:'';
        $type = !empty($request->type)?$request->type:'';
        $idnotin = !empty($request->idnotin)?$request->idnotin:array();

        
        $data = Product::select('id','cat_id','subcat_id','unique_id','title','public_name','mop','hsn_code','gst')->with(['category:id,name','subcategory:id,name'])->where('status',1);        
        if(!empty($type)){
            $data = $data->where('type',$type);
        }
        if(!empty($idnotin)){
            $data = $data->whereNotIn('id', $idnotin);
        }
        if(!empty($search)){
            $data = $data->where(function($q) use ($search){
                $q->where('title', 'LIKE','%'.$search.'%')
                ->orWhere('public_name', 'LIKE', '%' . $search . '%');
            });
        }
        
        $data = $data->get();
        return $data;        
    }
    public function search_product_by_type(Request $request)
    {
        // dd($request->all());
        # search product by title...
        $search = !empty($request->search)?$request->search:'';
        $type = !empty($request->type)?$request->type:'';
        $cw = !empty($request->cw)?$request->cw:'';
        $spare_type = !empty($request->spare_type)?$request->spare_type:'';
        $goods_type = !empty($request->goods_type)?$request->goods_type:'';
        $idnotin = !empty($request->idnotin)?$request->idnotin:array();
        
        $data = Product::select('id','cat_id','subcat_id','unique_id','title','public_name','mop','hsn_code','gst','last_po_cost_price','profit_percentage')->with(['category:id,name','subcategory:id,name'])->where('status',1);        
        if(!empty($type)){
            $data = $data->where('type',$type);
        }
        if(!empty($spare_type)){
            $data = $data->where('spare_type',$spare_type);
        }
        if(!empty($goods_type)){
            if (is_array($goods_type)) {
                $data->whereIn('goods_type', $goods_type); // Adjusted to handle arrays
            } else {
                $data->where('goods_type', $goods_type);
            }
        }
        if(!empty($idnotin)){
            $data = $data->whereNotIn('id', $idnotin);
        }

        if(!empty($search)){
            $data = $data->where(function($q) use ($search){
                $q->where('title', 'LIKE','%'.$search.'%');
            });
        }

        if(!empty($cw)){
            $data = $data->whereNotNull('comprehensive_warranty');
        }  
        
        $data = $data->get();
        // dd($data);
        return $data;        
    }

    public function search_product_for_return(Request $request){
        // dd($request->all());
        $search = !empty($request->search)?$request->search:'';
        $type = !empty($request->type)?$request->type:'';
        $service_partner_id = !empty($request->service_partner_id)?$request->service_partner_id:'';
        $dealer_id = !empty($request->dealer_id)?$request->dealer_id:'';
        $idnotin = !empty($request->idnotin)?$request->idnotin:array();

        if(!empty($dealer_id)){
            $data = SalesOrder::where('type',$type)->where('dealer_id',$dealer_id)->get();
            dd($data);
        }else{
            $data = SalesOrder::where('type',$type)->where('service_partner_id',$service_partner_id)->get();
            dd($data);

        }

    }

    public function get_spare_part(Request $request)
    {
        $id = !empty($request->goods_id)?$request->goods_id:'';
        $search = !empty($request->search)?$request->search:'';
        $data = array();
        if(!empty($id)){
            $spareIds = SpareGoods::where('goods_id',$id)->pluck('spare_id')->toArray();
            if (!empty($spareIds)) {
                // Filter the spare IDs that exist in the purchase_order_products table
                // $validSpareIds = StockInventory::whereIn('product_id', $spareIds)
                //     ->where('quantity', '>', 0)
                //     ->pluck('product_id')
                //     ->toArray();

                // $data = Product::whereIn('products.id', $spareIds)
                //     ->where(function ($q) use ($search) {
                //         $q->where('products.title', 'LIKE', '%' . $search . '%');
                //     })
                //     ->join('stock_inventory', 'products.id', '=', 'stock_inventory.product_id')
                //     ->select('products.title', 'products.id', 'stock_inventory.quantity')
                //     ->orderBy('products.title')
                //     ->get();

                $data = Product::whereIn('id', $spareIds)
                ->where(function ($q) use ($search) {
                    $q->where('title', 'LIKE', '%' . $search . '%');
                })
                ->select('title', 'id')
                ->orderBy('title')
                ->get();
                return $data;
            }else{
                // Return an empty response or message if no spare IDs found in purchase_order_products
                //   return response()->json([
                //     'status' => false,
                //     'message' => 'No spare parts found in purchase orders.'
                // ], 404);
                return (object) array();

            }
            
        }else{
            return (object) array();
        }
    }
    public function get_single_product_amc(Request $request)
    {
        $id = !empty($request->id)?$request->id:'';
        // dd($id);
        if(!empty($id)){
            $title = Product::select('title')->find($id);
            $data = ProductAmc::where('product_id',$id)->get();
            $data['title'] = $title;
            return $data;
        } else {
            return (object) array();
        }


    }
    public function get_single_product(Request $request)
    {
        # single product...
        $id = !empty($request->id)?$request->id:'';
        $order_date = !empty($request->order_date)?$request->order_date:'';
        $dealer_type = !empty($request->dealer_type)?$request->dealer_type:'nonkhosla';
        
        if(!empty($id)){
            
            $data = Product::find($id);
            $warranty_period = $data->warranty_period;
            $data->warranty_date = null;
            $data->out_of_warranty = 'No';
            // if(!empty($order_date) && !empty($warranty_period)){
            //     $warranty_end_date = date('Y-m-d', strtotime($order_date. ' + '.$warranty_period.' months'));
            //     $warranty_date = date('Y-m-d', strtotime($warranty_end_date.'-1 days'));
            //     $data->warranty_date = $warranty_date;

            //     if(date('Y-m-d') > $warranty_date){
            //         $data->out_of_warranty = 'Yes';
            //     }
            // }
           
            if(!empty($order_date) && !empty($dealer_type)){
                $goods_warranty = GoodsWarranty::where('goods_id', $id)->where('dealer_type', $dealer_type)->first();
                $extra_warranty =$goods_warranty->extra_warranty ?? 0;
                if(!empty($goods_warranty)){
                    $warranty_period = 0;
                    $motor_warranty_period = 0;
                    if($goods_warranty->warranty_type == 'general'){
                        $warranty_period = $goods_warranty->general_warranty;
                    } else {
                        $warranty_period = $goods_warranty->comprehensive_warranty+$extra_warranty;
                        $motor_warranty_period = $goods_warranty->motor_warranty ?? 0;
                        
                    }
                    $warranty_end_date = date('Y-m-d', strtotime($order_date. ' + '.$warranty_period.' months'));
                    $warranty_date = date('Y-m-d', strtotime($warranty_end_date.'-1 days'));
                    $motor_warranty_end_date = date('Y-m-d', strtotime($order_date. ' + '.$motor_warranty_period.' months')); //for motor
                    $motor_warranty_date = date('Y-m-d', strtotime($motor_warranty_end_date.'-1 days')); //for motor

                    $data->warranty_date = $warranty_date;
                    $data->motor_warranty_date = $motor_warranty_date;
                    $data->warranty_type = $goods_warranty->warranty_type;
                    if(date('Y-m-d') > $warranty_date){
                        $data->out_of_warranty = 'Yes';
                        $data->warranty_status = 'no';
                    }else{
                        $data->out_of_warranty = 'No';
                        $data->warranty_status = 'yes';

                    }
                } else {
                    $data->warranty_status = 'no';
                    $data->warranty_period = null;
                }
            }
            // dd($data);
            return $data;
        } else {
            return (object) array();
        }
    }
    public function get_product_warranty_status(Request $request){
        $id = !empty($request->id)?$request->id:'';
        $order_date = !empty($request->order_date)?$request->order_date:'';
        $dealer_type = !empty($request->dealer_type)?$request->dealer_type:'nonkhosla';
        $to_date = !empty($request->to_date) ? $request->to_date : date('Y-m-d'); // Default to today if not provided
        $data = ProductWarranty::with('spear_goods')->where('goods_id', $id)->where('dealer_type', $dealer_type)->get();
        if(empty($id)){
            return response()->json(['status'=>false, 'message'=>'No product found!']);
        }else{
            $khosla_warranty = [];
            foreach($data as $key =>$item){
                $array = [];
                $array['warranty_type']=$item->warranty_type;
                $array['additional_warranty_type']=$item->additional_warranty_type;
                $array['number_of_cleaning']=$item->number_of_cleaning;
                $array['parts']=$item->spear_goods?$item->spear_goods->title:null;
                if($item->warranty_type === 'additional'){
                    $comprehensive_warranty_preiod = ProductWarranty::where('goods_id',$item->goods_id)->where('dealer_type',$dealer_type)->where('warranty_type','comprehensive')->pluck('warranty_period')->first();
                    $comprehensive_warranty_preiod = $comprehensive_warranty_preiod ? $comprehensive_warranty_preiod : 0; // Default to 0 if not found
                    $array['warranty_period']=$item->warranty_period+$comprehensive_warranty_preiod;
                }else{
                    $array['warranty_period']=$item->warranty_period;
                }
                $array['dealer_type']=$item->dealer_type;
                
                // For Warranty checking
                $warranty_period = $array['warranty_period'];
                $warranty_end_date = date('Y-m-d', strtotime($order_date. ' + '.$warranty_period.' months'));
                $warranty_date = date('Y-m-d', strtotime($warranty_end_date.'-1 days'));
                $array['warranty_end_date']=date('d-m-Y',strtotime($warranty_date));

              $days_difference = (strtotime($warranty_date) - strtotime($to_date)) / (60 * 60 * 24); // Difference in days
                if($to_date < $warranty_date){
                    $array['warranty_status']="YES";
                    $array['days_remaining'] = $days_difference; // Positive days remaining
                }else{
                    $array['warranty_status']="NO";
                    $array['days_remaining'] = $days_difference; // Negative days (expired warranty)
                }
                $khosla_warranty[]= $array;
            }
			
			$comprehensive_warranty = collect($khosla_warranty)
				->firstWhere('warranty_type', 'comprehensive');

			$comprehensive_end_date = $comprehensive_warranty 
				? date('Y-m-d', strtotime(str_replace('-', '/', $comprehensive_warranty['warranty_end_date'])))
				: null;

           
            $getAMCSubscription = AmcSubscription::with('AmcData.AmcDurationData','AmcData.AmcPlanData')->where('serial', $request->serial_no)->first();
			
            $amc_warranty = null;
            if($getAMCSubscription){
				// AMC purchase date
				//$amc_purchase_date = date('Y-m-d', strtotime($getAMCSubscription->purchase_date));
				// If AMC is purchased before or on warranty end, AMC starts next day after warranty
				//if ($amc_purchase_date <= $warranty_end_date) {
				//	$amc_start_date = date('Y-m-d', strtotime($warranty_end_date . '+1 days'));
				//} else {
					// If AMC is purchased after warranty, start from purchase date
				//	$amc_start_date = $amc_purchase_date;
				//}
                //$getAMCSubscription->amc_start_date = date('d-m-Y', strtotime($getAMCSubscription->amc_start_date));
                $getAMCSubscription->amc_end_date = date('d-m-Y', strtotime($getAMCSubscription->amc_end_date));
                $today_date = date('Y-m-d',strtotime($to_date));
                //$amc_start_date = date('Y-m-d', strtotime($array['warranty_end_date'].'+1 days'));
				if ($comprehensive_end_date && $getAMCSubscription->purchase_date <= $comprehensive_end_date) {
					$amc_start_date = date('Y-m-d', strtotime($comprehensive_end_date . '+1 days'));
				} else {
					$amc_start_date = date('Y-m-d', strtotime($getAMCSubscription->purchase_date));
				}

                $amc_end_date = date('Y-m-d', strtotime($amc_start_date. ' + '.$getAMCSubscription->AmcData->duration.' days'));

                $days_difference = (strtotime($amc_end_date) - strtotime($today_date)) / (60 * 60 * 24); // Difference in days
                if($today_date < $amc_end_date){
                    $warranty_status="YES";
                    $days_remaining = $days_difference; 
                }else{
                    $warranty_status="No";
                    $days_remaining = $days_difference;
                }
                $amc_actual_normal_cleaning = optional(optional($getAMCSubscription->AmcData)->AmcDurationData)->normal_cleaning?? "0";
                $amc_actual_deep_cleaning = optional(optional($getAMCSubscription->AmcData)->AmcDurationData)->deep_cleaning?? "0";
                $amc_warranty = [
                    'amc_id'=>$getAMCSubscription->id,
                    'amc_number'=>$getAMCSubscription->amc_unique_number,
                    'amc_start_date'=>$amc_start_date,
                    'amc_end_date'=>$amc_end_date,
                    'warranty_status'=>$warranty_status,
                    'days_difference'=>$days_difference,
                    'amc_plan_name' => optional(optional($getAMCSubscription->AmcData)->AmcPlanData)->name ?? "N/A",
					'amc_duration' => optional($getAMCSubscription->AmcData)->duration ?? "0",
                    'amc_actual_normal_cleaning' => $amc_actual_normal_cleaning,
                    'amc_actual_deep_cleaning' => $amc_actual_deep_cleaning,
                    'amc_used_normal_cleaning' => ActualAmcCleaningWarranty($request->serial_no,$amc_start_date),
                    'amc_used_deep_cleaning' => ActuallAmcDeepCleaningWarranty($request->serial_no,$amc_start_date),
                    'amc_remaining_normal_cleaning' => $amc_actual_normal_cleaning-ActualAmcCleaningWarranty($request->serial_no,$amc_start_date),
                    'amc_remaining_deep_cleaning' => $amc_actual_deep_cleaning-ActuallAmcDeepCleaningWarranty($request->serial_no,$amc_start_date),
                    ];
            }
            return response()->json(['status'=>true, 'data'=>$khosla_warranty, 'amc_subscription'=>$amc_warranty]);
        }
    }

    public function pobulkscan(Request $request)
    {
        # PO Bulk Scan...

        $purchase_order_id = !empty($request->purchase_order_id)?$request->purchase_order_id:'';
        $product_id = !empty($request->product_id)?$request->product_id:'';
        $is_bulk_scanned = $request->is_bulk_scanned;
        $data = PurchaseOrderBarcode::where('purchase_order_id',$purchase_order_id)->where('product_id',$product_id)->where('is_scanned', 0)->get();

        // dd($data);
        
        if(!empty($data)){
            foreach($data as $item){
                PurchaseOrderBarcode::where('id',$item->id)->update(['is_bulk_scanned'=>$is_bulk_scanned]);
            }
        }

        return true;
        

        // if(!empty($data)){
        //     return true;
        // } else {
        //     return false;
        // }
    }
    public function posinglescan(Request $request){
        # PO Single Scan...
        $barcode_id = !empty($request->barcode_id)?$request->barcode_id:'';
        $is_bulk_scanned = $request->is_bulk_scanned;
        $data = PurchaseOrderBarcode::findOrFail($barcode_id);
        $data->is_bulk_scanned = $is_bulk_scanned;
        $data->save();
        // return true;
        if(!empty($data)){
            return true;
        } else {
            return false;
        }
    }

    public function checkPOScannedboxes(Request $request)
    {
        # Ajax ... 
        $data = array();
        $purchase_order_id = $request->purchase_order_id;
        $data = PurchaseOrderBarcode::where('purchase_order_id',$purchase_order_id)->where('is_scanned', 1)->get();

        return $data;
    }

    public function checkPSScannedboxes(Request $request)
    {
        # code...
        $data = array();
        $packingslip_id = $request->packingslip_id;
        $data = StockBarcode::where('packingslip_id',$packingslip_id)->where('is_scanned', 1)->get();

        return $data;
    }

    public function searchDealerUser(Request $request)
    {
        # search dealer ...
        $search = !empty($request->search)?$request->search:'';
        $data = Dealer::where('name', 'LIKE', '%'.$search.'%')->orderBy('name')->get();
        return $data;
    }

    public function get_service_partner_by_pincode(Request $request)
    {

        # get service partner by pincode...
        $pincode = !empty($request->pincode)?$request->pincode:'';
        $product_type = !empty($request->product_type)?$request->product_type:'';
        $data = (object) array();
        $data = ServicePartnerPincode::with('service_partner:id,email,person_name,company_name');
        
        if(!empty($product_type)){
            $data = $data->where('product_type', $product_type);
        } else {
            $data = $data->where('product_type', 'general');
        }

        $data = $data->where('number', '=', $pincode)->first();
        
            return $data;
        
        
    }
    public function get_customer_point_service_partner_by_pincode(Request $request)
    {
        # get service partner by pincode...
        $pincode = !empty($request->pincode)?$request->pincode:'';
        $product_type = !empty($request->product_type)?$request->product_type:'';
        $data = (object) array();
        $data = CustomerPointServicePartnerPincode::with('service_partner:id,email,person_name,company_name');
        
        if(!empty($product_type)){
            $data = $data->where('product_type', $product_type);
        } else {
            $data = $data->where('product_type', 'general');
        }

        $data = $data->where('number', '=', $pincode)->first();
        return $data;
        
    }

    public function searchServicePartner(Request $request)
    {
        # search service partner...
        $search = $request->search;
        $is_active = !empty($request->is_active)?$request->is_active:'';
        $data = ServicePartner::where('is_default',0);
        
        if(!empty($is_active)){
            $data = $data->where('status', 1);
        }
        
        $data = $data->where(function($q) use($search){
            $q->where('company_name', 'LIKE','%'.$search.'%')->orWhere('person_name','LIKE','%'.$search.'%');
        })->orderBy('company_name')->get();
        return $data;
    }

    public function servicepartner_returnable_spares(Request $request)
    {
        # Service Partner Return Available Goods...
        $service_partner_id = $request->service_partner_id;
        $search = !empty($request->search)?$request->search:'';
        $idnotin = !empty($request->idnotin)?$request->idnotin:array();
        $data = Invoice::where('service_partner_id',$service_partner_id)->get()->toArray();
        $ids = $products = $proIds = $proArr = [];
        foreach($data as $d){
            $ids[] = $d['id'];
        }
        if(!empty($ids)){
            $products = InvoiceItem::with('product:id,title,unique_id')->select('product_id',DB::raw('MAX(price) as price , MAX(quantity) as quantity , MAX(hsn_code) as hsn_code , MAX(tax) as tax'))->whereHas('invoice', function($inv) use($service_partner_id){
                $inv->where('service_partner_id',$service_partner_id);
            });
            // $products = InvoiceItem::with('product:id,title,unique_id')->whereIn('invoice_id',$ids);
            if(!empty($idnotin)){
                $products = $products->whereNotIn('product_id', $idnotin);
            }
            $products = $products->whereHas('product',function($query) use ($search){
                $query->where('title', 'LIKE','%'.$search.'%');
            });

            $products = $products->groupBy('product_id');
            
            $products = $products->orderBy('id','desc')->get();
            
        }
        
        return $products;
    }

   
    public function getBankList(Request $request)
    {
        # Get Available Indian Banks...
        $search = !empty($request->search)?$request->search:'';

        $data = DB::table('bank_lists')->where('name','LIKE','%'.$search.'%')->orderBy('name')->get();

        return $data;
    }

    public function search_branches(Request $request)
    {
        # Search branch / showroom...

        $search = !empty($request->search)?$request->search:'';
        $data = Branch::select('id','name');
        if(!empty($search)){
            $data = $data->where('name','LIKE','%'.$search.'%');
        }
        $data = $data->orderBy('name')->get();
        return $data;
    }

    public function get_goods_spare(Request $request)
    {
        # Search Spare List For A Goods...
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
        return $spare_goods;
        
        
        // dd($spare_goods);
        // return $spare_goods;



    }

    public function toggle_status(Request $request)
    {
        # Ajax change status...
        $id = !empty($request->id)?$request->id:'';
        $table_name = !empty($request->table_name)?$request->table_name:'';
        $data = DB::table($table_name)->where('id',$id)->first();
        $message = $status = "";
        if($data->status == 1){
            DB::table($table_name)->where('id',$id)->update(['status'=>0]);
            $message = "Deactivated successfully";
            $status = "inactive";
        } else {
            DB::table($table_name)->where('id',$id)->update(['status'=>1]);
            $message = "Activated successfully";
            $status = "active";
        }
        return ['status'=>$status,'message'=>$message];

    }


    public function dealer_returnable_goods(Request $request)
    {
        # Dealer Return Available Goods...
        $dealer_id = $request->dealer_id;
        $search = !empty($request->search)?$request->search:'';
        $idnotin = !empty($request->idnotin)?$request->idnotin:array();
        $data = Invoice::where('dealer_id',$dealer_id)->get()->toArray();
        $ids = $products = $proIds = $proArr = [];
        foreach($data as $d){
            $ids[] = $d['id'];
        }
        if(!empty($ids)){
            $products = InvoiceItem::with('product:id,title,unique_id')->select('product_id',DB::raw('MAX(price) as price , SUM(quantity) as quantity '))->whereHas('invoice', function($inv) use($dealer_id){
                $inv->where('dealer_id',$dealer_id);
            });
            
            if(!empty($idnotin)){
                $products = $products->whereNotIn('product_id', $idnotin);
            }
            $products = $products->whereHas('product',function($query) use ($search){
                $query->where('title', 'LIKE','%'.$search.'%');
            });

            $products = $products->groupBy('product_id');
            
            $products = $products->orderBy('id','desc')->get();
            
        }
        
        return $products;
    }

    public function service_partner_barcodes(Request $request)
    {
        $service_partner_id = !empty($request->service_partner_id)?$request->service_partner_id:'';
        $product_id = !empty($request->product_id)?$request->product_id:'';
        $search = !empty($request->search)?$request->search:'';
        $barcodenotin = !empty($request->barcodenotin)?$request->barcodenotin:array();

        $spare_returns = \App\Models\SpareReturn::where('service_partner_id', $service_partner_id)->pluck('barcode_no')->toArray();


        $data = StockBarcode::select('id','product_id','barcode_no')->where('barcode_no','LIKE','%'.$search.'%')->where('product_id', $product_id)->whereHas('packingslip', function($ps) use($service_partner_id){
            $ps->whereHas('sales_order', function($so) use ($service_partner_id){
                $so->where('service_partner_id', $service_partner_id);
            });
        })->whereNotIn('barcode_no', $spare_returns);

        $data = $data->whereNotIn('barcode_no', $barcodenotin);
        
        $data = $data->get();

        return $data;
    }
    
    function get_cleaning_warranty_by_product($product_id){
        $used_cleaning = Maintenance::where('product_id',$product_id)->where('service_type','cleaning')->count();
        return $used_cleaning;
    }
    public function amc_product_delete(Request $request) {
        // dd($request->all());
        $browser_name = $request->input('browser_name', null);
        $navigator_useragent = $request->input('navigator_useragent', null);
        $id = !empty($request->id) ? $request->id : '';
        $data = ProductAmc::find($id);
    
        if ($data) {
            $data->delete();
            addChangeLog(Auth::user()->id,$request->ip(),'Product Deleted in AMC Plan',$browser_name,$navigator_useragent,$data);
            // Store a success message in the session
            Session::flash('message', 'Product deleted successfully!');
        } else {
            // Store an error message in case the product is not found
            Session::flash('error', 'Product not found!');
        }
    
        return response()->json(['status' => 200]);
    }


}
