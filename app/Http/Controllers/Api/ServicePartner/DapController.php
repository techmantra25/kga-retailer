<?php

namespace App\Http\Controllers\Api\ServicePartner;

use App\Http\Controllers\Controller;
use App\Models\DapDiscountRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\DB;
use App\Models\ServicePartner;
use App\Models\DapService;
use App\Models\PurchaseOrderBarcode;
use App\Models\PurchaseOrderProduct;
use App\Models\Maintenance;
use App\Models\Ledger;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Packingslip;
use App\Models\PackingslipProduct;
use App\Models\DapServicePayment;
use App\Models\MaintenanceSpare;
use App\Models\GoodsWarranty;
use App\Models\ProductWarranty;
use App\Models\Product;
use App\Models\SalesOrderProduct;
use App\Models\PackingslipBarcode;
use App\Models\SpareGoods;
use App\Models\StockBarcode;
use App\Models\SalesOrder;
use App\Models\DapSpearPartOrder;
use App\Models\DapSpearPartFinalOrder;
use App\Models\ServicePartnerCharge;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;




class DapController extends Controller
{
    // private $service_partner_id;
    // public function __construct(Request $request)
    // {
    //     # pass bearer token in Authorizations key...
    //     if (! $request->hasHeader('Authorizations')) {
    //         response()->json(["status"=>false,"message"=>"Unauthorized"],401)->send();
    //         exit();
    //     } else {
    //         $bearer_token = $request->header('Authorizations');
    //         $token = str_replace("Bearer ","",$bearer_token);            
    //         try {
    //             $this->service_partner_id = Crypt::decrypt($token);
    //             $staff = ServicePartner::find($this->service_partner_id);           
    //         } catch (DecryptException $e) {
    //             response()->json(["status"=>false,"message"=>"Mismatched token"],400)->send();
    //             exit();
    //         }
    //     }
    // }

    public function list($id)
    {
        $dap_product = DapService::where('assign_service_perter_id', $id)->where('is_closed', 0)->where('quotation_status', 0)->orderBy('id', 'DESC')->get()->toArray();
        if (!empty($dap_product)) {
            return Response::json(['status' => true, 'data' => $dap_product], 200);
        } else {
            return Response::json(['status' => false, 'message' => "No product found"], 200);
        }
    }
    // public function engg_global_dap_product_search($id, $barcode) //old
    // {
    //     $dap_product = DapService::where('unique_id', $barcode)
    //         ->where('assign_service_perter_id', $id)
    //         ->get();

    //     if ($dap_product->isNotEmpty()) { 
    //         if($dap_product[0]->quotation_status == 1){
    //             return response()->json(['status' => true, 'message' => "Quation list already generated for this product"], 200);
    //         }
    //         $dap_product = $dap_product->map(function ($item) {
    //             $general_warranty = $comprehensive_warranty = $extra_warranty = $motor_warranty = 0;
    //             $GoodsWarranty = GoodsWarranty::where('dealer_type', 'khosla')
    //                 ->where('goods_id', $item->product_id)
    //                 ->first(); // For only khosla product

    //             if ($GoodsWarranty) {
    //                 $general_warranty = $GoodsWarranty->general_warranty ?: 0;
    //                 $comprehensive_warranty = $GoodsWarranty->comprehensive_warranty ?: 0;
    //                 $extra_warranty = $GoodsWarranty->extra_warranty ?: 0;
    //                 $motor_warranty = $GoodsWarranty->motor_warranty ?: 0;
    //             }

    //             $warranty_period = $general_warranty + $extra_warranty;
    //             $warranty_end_date = date('Y-m-d', strtotime($item->bill_date . ' + ' . $warranty_period . ' months'));
    //             $warranty_date = date('Y-m-d', strtotime($warranty_end_date . ' -1 days'));
    //             $current_date = date('Y-m-d');

    //             $item->general_warranty = false;
    //             $item->general_expiry_date = $warranty_date;
    //             $item->comprehensive_warranty = false;
    //             $item->comprehensive_expiry_date = null;
    //             $item->motor_warranty = false;
    //             $item->motor_expiry_date = null;
    //             $item->in_warranty = false;

    //             if ($current_date < $warranty_date) {
    //                 $item->general_warranty = true;
    //                 $item->comprehensive_warranty = true;
    //                 $item->motor_warranty = true;
    //                 $item->in_warranty = true;
    //             } else {
    //                 if ($comprehensive_warranty > 0) {
    //                     $warranty_end_date = date('Y-m-d', strtotime($item->bill_date . ' + ' . $comprehensive_warranty . ' months'));
    //                     $warranty_date = date('Y-m-d', strtotime($warranty_end_date . ' -1 days'));
    //                     if ($current_date < $warranty_date) {
    //                         $item->in_warranty = true;
    //                         $item->comprehensive_warranty = true;
    //                         $item->comprehensive_expiry_date = $warranty_date;
    //                     }
    //                 } 
    //                 if ($motor_warranty > 0) {
    //                     $warranty_end_date = date('Y-m-d', strtotime($item->bill_date . ' + ' . $motor_warranty . ' months'));
    //                     $warranty_date = date('Y-m-d', strtotime($warranty_end_date . ' -1 days'));
    //                     if ($current_date < $warranty_date) {
    //                         $item->in_warranty = true;
    //                         $item->motor_warranty = true;
    //                         $item->motor_expiry_date = $warranty_date;
    //                     }
    //                 }
    //             }

    //             return $item;
    //         })->toArray();

    //         return response()->json(['status' => true, 'data' => $dap_product], 200);
    //     } else {
    //         return response()->json(['status' => false, 'message' => "This product is not assigned to you"], 200);
    //     }
    // }

    public function engg_global_dap_product_search($id, $barcode)  // newly update
    {
        $dap_product = DapService::where('unique_id', $barcode)
            ->where('assign_service_perter_id', $id)
            ->get();

        if ($dap_product->isNotEmpty()) {
            if ($dap_product[0]->quotation_status == 1) {
                return response()->json(['status' => true, 'message' => "Quation list already generated for this product"], 200);
            }
            $dap_product = $dap_product->map(function ($item) {
                $GoodsWarranty = ProductWarranty::where('dealer_type', 'khosla')->where('goods_id', $item->product_id)->get(); // for only khosla product
                $khosla_warranty = [];
                if ($GoodsWarranty) {
                    foreach ($GoodsWarranty as $key => $value) {
                        $array = [];
                        $array['warranty_type'] = $value->warranty_type;
                        $array['additional_warranty_type'] = $value->additional_warranty_type;
                        $array['number_of_cleaning'] = $value->number_of_cleaning;
                        $array['parts'] = $value->spear_goods ? $value->spear_goods->title : null;
                        // $array['warranty_period'] = $value->warranty_period;
                        // Calculate warranty period, including additional warranty if applicable
                        if ($value->warranty_type === 'additional') {
                            // Get comprehensive warranty period if available
                            $comprehensive_warranty_period = ProductWarranty::where('goods_id', $value->goods_id)
                                ->where('dealer_type', 'khosla')
                                ->where('warranty_type', 'comprehensive')
                                ->pluck('warranty_period')
                                ->first();
                            $comprehensive_warranty_period = $comprehensive_warranty_period ? $comprehensive_warranty_period : 0;

                            // Add comprehensive period to additional warranty period
                            $array['warranty_period'] = $value->warranty_period + $comprehensive_warranty_period;
                        } else {
                            $array['warranty_period'] = $value->warranty_period;
                        }
                        $array['dealer_type'] = $value->dealer_type;
                        $warranty_period = $array['warranty_period'];
                        // $array['dealer_type'] = $value->dealer_type;
                        // $warranty_period = $value->warranty_period;
                        $warranty_end_date = date('Y-m-d', strtotime($item->bill_date . ' + ' . $warranty_period . ' months'));
                        $warranty_date = date('Y-m-d', strtotime($warranty_end_date . ' -1 days'));
                        $array['warranty_end_date'] = date('d-m-Y', strtotime($warranty_date));
                        if (date('Y-m-d') < $warranty_date) {
                            $array['warranty_status'] = "YES";
                        } else {
                            $array['warranty_status'] = "NO";
                        }
                        $khosla_warranty[] = $array;
                    }
                }

                // Add the warranty details to the item
                $item->khosla_warranty = $khosla_warranty;
                return $item;
            })->toArray();

            return response()->json(['status' => true, 'data' => $dap_product], 200);
        } else {
            return response()->json(['status' => false, 'message' => "This product is not assigned to you"], 200);
        }
    }

    // public function engg_scan_dap_barcode($id, $dap_barcode) ///old
    // {
    //     $dap_product = DapService::where('unique_id', $dap_barcode)
    //         ->where('assign_service_perter_id', $id)
    //         ->first();

    //     if ($dap_product) {
    //         // Convert $dap_product to an object
    //         $general_warranty = $comprehensive_warranty = $extra_warranty = $motor_warranty = 0;
    //         $GoodsWarranty = GoodsWarranty::where('dealer_type', 'khosla')
    //             ->where('goods_id', $dap_product->product_id)
    //             ->first(); // For only khosla product

    //         if ($GoodsWarranty) {
    //             $general_warranty = $GoodsWarranty->general_warranty ?: 0;
    //             $comprehensive_warranty = $GoodsWarranty->comprehensive_warranty ?: 0;
    //             $extra_warranty = $GoodsWarranty->extra_warranty ?: 0;
    //             $motor_warranty = $GoodsWarranty->motor_warranty ?: 0;
    //         }

    //         $warranty_period = $general_warranty + $extra_warranty;
    //         $warranty_end_date = date('Y-m-d', strtotime($dap_product->bill_date . ' + ' . $warranty_period . ' months'));
    //         $warranty_date = date('Y-m-d', strtotime($warranty_end_date . ' -1 days'));
    //         $current_date = date('Y-m-d');

    //         $dap_product->general_warranty = false;
    //         $dap_product->general_expiry_date = $warranty_date;
    //         $dap_product->comprehensive_warranty = false;
    //         $dap_product->comprehensive_expiry_date = null;
    //         $dap_product->motor_warranty = false;
    //         $dap_product->motor_expiry_date = null;
    //         $dap_product->in_warranty = false;

    //         if ($current_date < $warranty_date) {
    //             $dap_product->general_warranty = true;
    //             $dap_product->comprehensive_warranty = true;
    //             $dap_product->motor_warranty = true;
    //             $dap_product->in_warranty = true;
    //         } else {
    //             if ($comprehensive_warranty > 0) {
    //                 $warranty_end_date = date('Y-m-d', strtotime($dap_product->bill_date . ' + ' . $comprehensive_warranty . ' months'));
    //                 $warranty_date = date('Y-m-d', strtotime($warranty_end_date . ' -1 days'));
    //                 if ($current_date < $warranty_date) {
    //                     $dap_product->in_warranty = true;
    //                     $dap_product->comprehensive_warranty = true;
    //                     $dap_product->comprehensive_expiry_date = $warranty_date;
    //                 }
    //             } 
    //             if ($motor_warranty > 0) {
    //                 $warranty_end_date = date('Y-m-d', strtotime($dap_product->bill_date . ' + ' . $motor_warranty . ' months'));
    //                 $warranty_date = date('Y-m-d', strtotime($warranty_end_date . ' -1 days'));
    //                 if ($current_date < $warranty_date) {
    //                     $dap_product->in_warranty = true;
    //                     $dap_product->motor_warranty = true;
    //                     $dap_product->motor_expiry_date = $warranty_date;
    //                 }
    //             }
    //         }

    //         // Convert the item to an object
    //         $dap_product = (object) $dap_product->toArray();

    //         return Response::json(['status' => true, 'message' => "Product matched!", 'data' => $dap_product], 200);
    //     } else {
    //         return Response::json(['status' => false, 'message' => "Product miss-matched!"], 200);
    //     }
    // }

    public function engg_scan_dap_barcode($id, $dap_barcode)  // newly update
    {
        $dap_product = DapService::where('unique_id', $dap_barcode)
            ->where('assign_service_perter_id', $id)
            ->first();

        if ($dap_product) {
            // Convert $dap_product to an object
            $GoodsWarranty = ProductWarranty::where('dealer_type', 'khosla')->where('goods_id', $dap_product->product_id)->get(); // for only khosla product
            $khosla_warranty = [];
            if ($GoodsWarranty) {
                foreach ($GoodsWarranty as $key => $value) {
                    $array = [];
                    $array['warranty_type'] = $value->warranty_type;
                    $array['additional_warranty_type'] = $value->additional_warranty_type;
                    $array['number_of_cleaning'] = $value->number_of_cleaning;
                    $array['parts'] = $value->spear_goods ? $value->spear_goods->title : null;
                    // $array['warranty_period'] = $value->warranty_period;
                      // Calculate warranty period, including additional warranty if applicable
                    if ($value->warranty_type === 'additional') {
                        // Get comprehensive warranty period if available
                        $comprehensive_warranty_period = ProductWarranty::where('goods_id', $value->goods_id)
                            ->where('dealer_type', 'khosla')
                            ->where('warranty_type', 'comprehensive')
                            ->pluck('warranty_period')
                            ->first();
                        $comprehensive_warranty_period = $comprehensive_warranty_period ? $comprehensive_warranty_period : 0;

                        // Add comprehensive period to additional warranty period
                        $array['warranty_period'] = $value->warranty_period + $comprehensive_warranty_period;
                    } else {
                        $array['warranty_period'] = $value->warranty_period;
                    }
                    $array['dealer_type'] = $value->dealer_type;
                    $warranty_period = $array['warranty_period'];
                    $warranty_end_date = date('Y-m-d', strtotime($dap_product->bill_date . ' + ' . $warranty_period . ' months'));
                    $warranty_date = date('Y-m-d', strtotime($warranty_end_date . ' -1 days'));
                    $array['warranty_end_date'] = date('d-m-Y', strtotime($warranty_date));
                    if (date('Y-m-d') < $warranty_date) {
                        $array['warranty_status'] = "YES";
                    } else {
                        $array['warranty_status'] = "NO";
                    }
                    $khosla_warranty[] = $array;
                }
            }
            // Attach the warranty details to the product object
            $dap_product->khosla_warranty = $khosla_warranty;

            return Response::json(['status' => true, 'message' => "Product matched!", 'data' => $dap_product], 200);
        } else {
            return Response::json(['status' => false, 'message' => "Product miss-matched!"], 200);
        }
    }

    public function engg_scan_spear_part_barcode($spear_barcode, $dap_product_id, $dap_id)
    {
        $spearBarcodeDetails = StockBarcode::where('barcode_no', $spear_barcode)->whereNull('packingslip_id')
            ->where('is_damage', 0)
            ->where('is_stock_out', 0)
            ->first(); // for checking the barcode is availabe or not
        if (!empty($spearBarcodeDetails)) {
            $spare_id = $spearBarcodeDetails->product_id;
            $good_ids = SpareGoods::where('spare_id', $spare_id)->pluck('goods_id')->toArray();
            // Check if $dap_product_id exists in $good_ids array
            if (in_array($dap_product_id, $good_ids)) {
                $product_id = $spearBarcodeDetails->product_id;
                // $product_details = Product::select('id', 'cat_id', 'title', 'unique_id', 'spare_type', 'profit_percentage')
                // ->find($product_id);
                $product_details = Product::find($product_id);
                // if ($product_details->last_po_cost_price > 0) {
                //     $spear_price = $product_details->last_po_cost_price;
                // } else {
                //     $spear_price = $product_details->mop;
                // }
               // if ($product_details->last_po_cost_price === null || $product_details->last_po_cost_price >= 0) {
                 //   return Response::json(['status' => false, 'message' => "This product does not have a Last cost price in our records!"], 200);
               // }
              //  if ($product_details->profit_percentage === null || $product_details->profit_percentage >= 0) {
               //     return Response::json(['status' => false, 'message' => "This product does not have a Profit % in our records!"], 200);
              //  }
                $spear_price = $product_details->last_po_cost_price * (1 + ($product_details->profit_percentage / 100));

                if ($product_details) { 
                    // Set Warranty Data
                    
                    $get_dap_warranty = get_dap_spare_warranty($dap_id, $product_details->id);                   
                    $product_details->profit_percentage = $product_details->profit_percentage ?? 0;
                    $product_details = $product_details->toArray();
                    $product_details['spear_price'] = $spear_price;
                    $product_details['in_warranty'] = $get_dap_warranty['warranty_status'];
                    return Response::json(['status' => true, 'message' => "Spear parts details found", 'product_details' => $product_details], 200);
                }
            } else {
                return Response::json(['status' => false, 'message' => "This spare part is not compatible with the selected Dap product"], 200);
            }
        } else {
            return Response::json(['status' => false, 'message' => "Spear parts not found in our record"], 200);
        }
    }

    public function engg_scan_spear_part_barcode_final($spear_barcode, $dap_product_id, $dap_id)
    {
        
        $spearBarcodeDetails = StockBarcode::where('barcode_no', $spear_barcode)->whereNull('packingslip_id')
        ->where('is_damage', 0)
        ->where('is_stock_out', 0)
        ->first(); // for checking the barcode is availabe or not

        if (!empty($spearBarcodeDetails)) {

            $spare_id = $spearBarcodeDetails->product_id;
            $good_ids = SpareGoods::where('spare_id', $spare_id)->pluck('goods_id')->toArray();
            if (in_array($dap_product_id, $good_ids)) {
                // $spearBarcodeDetails->is_stock_in = 0;
                // $spearBarcodeDetails->is_archived = 1;
                // $spearBarcodeDetails->save();

                // $purchase_order_id = PurchaseOrderBarcode::where('barcode_no',$spearBarcodeDetails->barcode_no)->pluck('purchase_order_id')->first();
                // $product_id = PurchaseOrderBarcode::where('barcode_no',$spearBarcodeDetails->barcode_no)->pluck('product_id')->first();
                $product_id = $spearBarcodeDetails->product_id;

                // if(!empty($purchase_order_id) && !empty($product_id)){
                // $spear_price = PurchaseOrderProduct::where('purchase_order_id',$purchase_order_id)->where('product_id',$product_id)->pluck('cost_price')->first();
                $product_details = Product::select('id', 'cat_id', 'title', 'unique_id', 'spare_type', 'profit_percentage')->find($product_id);
                $product = Product::find($product_id);
                // if ($product->last_po_cost_price > 0) {
                //     $spear_price = $product->last_po_cost_price;
                // } else {
                //     $spear_price = $product->mop;
                // }

               // if ($product->last_po_cost_price === null || $product->last_po_cost_price >= 0) {
                  //  return Response::json(['status' => false, 'message' => "This product does not have a Last cost price in our records!"], 200);
               // }
               // if ($product->profit_percentage === null || $product->profit_percentage >= 0) {
               //     return Response::json(['status' => false, 'message' => "This product does not have a Profit % in our records!"], 200);
               // }
                $spear_price = $product->last_po_cost_price * (1 + ($product->profit_percentage / 100));

                if ($product_details) {
                    $get_dap_warranty = get_dap_spare_warranty($dap_id, $product_details->id);
                    $product_details->profit_percentage = $product_details->profit_percentage ?? 0;
                    $product_details = $product_details->toArray();
                    $product_details['spear_price'] = $spear_price;
                    $product_details['in_warranty'] = $get_dap_warranty['warranty_status'];
                    $product_details['sp_barcode'] = $spear_barcode;


                    $uniue_id = 'OLD-SP' . genAutoIncreNoYearWiseOrder(3, 'dap_spear_part_final_orders', date('Y'), date('m'));
                    $barcodeGeneratorWithNo = barcodeGeneratorWithNo($uniue_id);
                    $code_html = $barcodeGeneratorWithNo['code_html'];
                    $code_base64_img = $barcodeGeneratorWithNo['code_base64_img'];
                    $old_spare_parts_data = [
                        'uniue_id' => $uniue_id,
                        'code_html' => $code_html,
                        'code_base64_img' => $code_base64_img,
                    ];
                    return Response::json(['status' => true, 'message' => "Spear parts used", 'product_details' => $product_details, 'old_spare_parts_data' => $old_spare_parts_data], 200);
                }
            } else {
                return Response::json(['status' => false, 'message' => "This spare part is not fitted for the selected product"], 200);
            }
        } else {
            return Response::json(['status' => false, 'message' => "Spear parts not found",], 200);
        }
    }
    public function repaire_product_dispatched_from_wearhouse(Request $request) {}
    public function spear_parts_order_final(Request $request)
    {
        DB::beginTransaction();
        try {
            $data = json_decode($request->data);
            $dapId = $request->dap_id;
            // Cache the DapService data to reduce database queries
            $cacheKey = "dap_service_final_{$dapId}";
            $dap_data = Cache::remember($cacheKey, 60, function () use ($dapId) {
                return DapService::with('branch', 'return_branch')->where('unique_id', $dapId)->first();
            });
            
            if (!$dap_data) {
                DB::rollBack();
                return response()->json(['status' => false, 'message' => "Dap Service data not found"], 404);     
            }

            $service_partner_ladger_amount = $dap_data->total_service_charge ? $dap_data->total_service_charge : 0;
            $payable_amount = ($dap_data->total_amount + $dap_data->total_service_charge) - $dap_data->discount_amount;
            $new_spear_part_qty = count($data);
            
            if (count($data) > 0) {
                $new_total_amount = 0; // Initialize total amount
                $details  = [];
                foreach ($data as $item) {
                    $product_items =[];
                    $new_barcode_no = "RETSPR" . $item->sp_barcode;
                    $barcodeGeneratorWithNo = barcodeGeneratorWithNo($new_barcode_no);
                    // Generate Old Barcode
                    $code_html = $barcodeGeneratorWithNo['code_html'];
                    $code_base64_img = $barcodeGeneratorWithNo['code_base64_img'];
                    
                    $dap_parts = new DapSpearPartFinalOrder();
                    $dap_parts->dap_id = $dap_data->id;
                    $dap_parts->product_id = $item->sp_id;
                    $dap_parts->price = $item->sp_price;
                    $dap_parts->title = $item->title;
                    $dap_parts->profit_percentage = $item->profit_percentage;
                    $dap_parts->final_amount = $item->final_amount;
                    $dap_parts->warranty_status = filter_var($item->warranty_status, FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
                    $dap_parts->old_spare_part_barcode = $item->sp_barcode;
                    $dap_parts->new_spare_barcode = $new_barcode_no;
                    $dap_parts->code_html = $code_html;
                    $dap_parts->code_base64_img = $code_base64_img;
                    $dap_parts->save();
                    // Add the final_amount to total_amount
                    $new_total_amount += $item->final_amount;
                    
                    $products = Product::find($item->sp_id);
                    $product_items['product']=$products->title;
                    $product_items['product_id']=$item->sp_id;
                    $product_items['quantity']=1;
                    $product_items['product_price']=$item->final_amount;
                    $product_items['tax']=$products->gst;
                    $product_items['hsn_code']=$products->hsn_code;
                    $product_items['product_total_price']=$item->final_amount;
                    $details[]= $product_items;
                }
                
                $new_auctual_total_amount = ($new_total_amount + $dap_data->total_service_charge) - $dap_data->discount_amount;
                
                if ($new_auctual_total_amount > $payable_amount) {
                    $difference = $new_auctual_total_amount - $payable_amount;
                    $dap_data->total_amount = $new_total_amount;
                    $dap_data->final_amount = $new_total_amount;
                    $dap_data->discount_amount += $difference;
                } elseif ($new_auctual_total_amount < $payable_amount) {
                    $difference = $payable_amount - $new_auctual_total_amount;
                    $dap_data->total_amount = $new_total_amount;
                    $dap_data->final_amount = $new_total_amount;
                    $dap_data->total_service_charge += $difference;
                } else {
                    $dap_data->total_amount = $new_total_amount;
                    $dap_data->final_amount = $new_total_amount;
                }
                
                $dap_data->spear_part_qty = $new_spear_part_qty;
                $dap_data->is_closed = 1;
                $dap_data->save();
                
                // Packing Slip And Sales Order and Invoice Generate
                $order_no = 'ORD'.genAutoIncreNoYearWiseOrder(4,'sales_orders',date('Y'),date('m'));
                // dd($order_no); 
                $service_partner_id = $dap_data->assign_service_perter_id;
                $salesOrderData = array(
                    'order_no' => $order_no,
                    'dap_id' => $dap_data->id,
                    'service_partner_id' => $service_partner_id,
                    'type' => 'sp',
                    'details' => json_encode($details),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')

                );
                // echo '<pre>'; print_r($salesOrderData); die;
                $id = SalesOrder::insertGetId($salesOrderData);
                $total_amount = 0;
                
                foreach($details as $detail){                  
                    $product_total_price = ($detail['quantity'] * $detail['product_price']);
                    $salesOrderProductData = array(
                        'sales_orders_id' => $id,
                        'product_id' => $detail['product_id'],
                        'quantity' => $detail['quantity'],
                        'delivered_quantity' => 1,
                        'product_price' => $detail['product_price'],
                        'product_total_price' => $product_total_price,
                        'tax' => $detail['tax'],
                        'hsn_code' => $detail['hsn_code'],
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    );
                     // Insert each product data into SalesOrderProduct
                    SalesOrderProduct::insert($salesOrderProductData);
                    $total_amount += $product_total_price;
                }
                SalesOrder::where('id',$id)->update(['order_amount'=>$total_amount]);        
                ## Check Barcode
                foreach ($data as $item) {
                    // $checkBarcode = StockBarcode::where('barcode_no',$item->sp_barcode)->first();
                    $sales_orders_id = SalesOrder::find($id);
                    $barcodeArr[] = $item->sp_barcode;
                }
                $stock_products = StockBarcode::selectRaw('product_id, count(product_id) AS count_product')->whereIn('barcode_no',$barcodeArr)->groupBy('product_id')->get()->toArray();

                $slipno = 'PS'.genAutoIncreNoYearWiseOrder(4,'packingslips',date('Y'),date('m'));

                if(!empty($stock_products)){
                    foreach($stock_products as $pro){
                        $sales_order_product = SalesOrderProduct::where('sales_orders_id',$id)->where('product_id',$pro['product_id'])->first();
        
                        if(!empty($sales_orders_id->dap_id)){
                            $dap_data = DapService::where('id', $sales_orders_id->dap_id)->first();
                            if($dap_data){
                                $dap_data->packing_slip = $slipno;
                                $dap_data->packing_slip_status = 1;
                                $dap_data->save();
                            }
                        }
                        
                    }
                }    
                ## Packing Slip Creation
                $packingslip_id = Packingslip::insertGetId([
                    'sales_order_id' => $sales_orders_id->id,
                    'goods_out_type' => 'bulk',
                    'slipno' => $slipno,
                    'is_goods_out' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')

                ]);       

                foreach($stock_products as $pro){
                PackingslipProduct::insert([
                    'packingslip_id' => $packingslip_id,
                    'product_id' => $pro['product_id'],
                    'quantity' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')

                ]);     
        
                //     ## Update quantity of stock inventory
                    updateStockInvetory($pro['product_id'],$pro['count_product'],'out',$packingslip_id,'packingslip');      
                }

                // ## Set As Disbursed ... 
                StockBarcode::whereIn('barcode_no',$barcodeArr)->update([
                    'packingslip_id' => $packingslip_id,
                    'is_stock_out' => 1,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
        
                // ## Sales Order Make Completed
        
                SalesOrder::where('id', $sales_orders_id->id)->update([
                    'status' => 'completed'
                ]);
        
                $this->savePackingslipBarcodes($packingslip_id,$barcodeArr);
                // dd($packingslip_id);
                //invoice
                $invoice_no = 'INV'.genAutoIncreNoYearWiseOrder(4,'invoices',date('Y'),date('m'));

                $invoiceData = array(
                    'invoice_no' => $invoice_no,
                    'sales_order_id' => $sales_orders_id->id,
                    'dealer_id' => NULL,
                    'service_partner_id' => $service_partner_id,
                    'packingslip_id' => $packingslip_id,
                    'total_amount' => $total_amount,
                    // 'customer_details' => json_encode($params['customer_details']),
                    'item_details' => json_encode($data),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                );
                $invoice_id = Invoice::insertGetId($invoiceData);
                foreach($data as $item){
                    $products = Product::find($item->sp_id);

                     // Calculate price excluding tax
                    $gst_rate = $products->gst;
                    $price_exc_tax = $item->final_amount / (1 + ($gst_rate / 100));

                    $invoiceitemData = array(
                        'invoice_id' => $invoice_id,
                        'product_id' => $item->sp_id,
                        'product_title' => $item->title,
                        'quantity' => 1,
                        'price' => $item->final_amount,
                        'total_price' => $item->final_amount,
                        'price_exc_tax' => round($price_exc_tax,2),
                        'total_price_exc_tax' =>round($price_exc_tax,2),
                        'tax' => $gst_rate,
                        'hsn_code' => $products->hsn_code,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                        
                    );
                    InvoiceItem::insert($invoiceitemData);
                    // Ledger Debit form each parts required in dap_product
                    if(!empty($service_partner_id)){
                        $this->ledgerEntry('servicepartner',$service_partner_id,$item->final_amount,$invoice_no);
                    }
                }
                    Packingslip::where('id', $packingslip_id)->update(['invoice_no' => $invoice_no]);

                // Ledger Notification
                if ($dap_data) {
                    //if it is repeat_call( call generated in 30 dys ) , then now debit the previous engg. service charge amount form his ledger 
                    //old service partner ledger debit

                    if($dap_data->repeat_call === 1 && $dap_data->repeat_dap_id != NULL){
                        //debit ledger
                        $pre_dap_data = DapService::find($dap_data->repeat_dap_id);
                        $pre_service_partner = $pre_dap_data->assign_service_perter_id;
                        // $pre_service_partner_service_charge = Ledger::where('service_partner_id',$pre_service_partner)->where('dap_id',$dap_data->repeat_dap_id)->where('purpose','Dap Repair')->where('type','credit')->pluck('amount');
                        $pre_service_partner_service_charge = Ledger::where('service_partner_id',$pre_service_partner)->where('dap_id',$dap_data->repeat_dap_id)->where('transaction_id',$pre_dap_data->unique_id)->where('type','credit')->pluck('amount');


                        $ledgerData = [
                            'type' => 'debit',
                            'service_partner_id' => $pre_service_partner,
                            'amount' => $pre_service_partner_service_charge,
                            'entry_date' => date('Y-m-d'),
                            'user_type' => 'servicepartner',
                            'purpose' => 'Dap Repair(repeat call)',
                            'transaction_id' => $pre_dap_data->unique_id,
                            'dap_id' => $dap_data->repeat_dap_id,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s')
                        ];
                        Ledger::insert($ledgerData);
                    }
                        // Ledger Entry
                        $ledgerData = [
                            'type' => 'credit',
                            'service_partner_id' => $dap_data->assign_service_perter_id,
                            'amount' => $service_partner_ladger_amount,
                            'entry_date' => date('Y-m-d'),
                            'user_type' => 'servicepartner',
                            'purpose' => 'Dap Repair',
                            'transaction_id' => $dap_data->unique_id,
                            'dap_id' => $dap_data->id,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s')
                        ];

                        $existLedger = Ledger::where('dap_id', $dap_data->id)->first();
                        if (empty($existLedger)) {
                            Ledger::insert($ledgerData);
                        }
                    }
                DB::commit();
                return response()->json(['status' => true, 'message' => "Dap Product repaired & Spear parts order finalized successfully"], 200);
            } else {
                DB::rollBack();
                return response()->json(['status' => false, 'message' => "No spear parts data provided"], 400);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
    public function spear_parts_order(Request $request)
    {
        try {
            $data = json_decode($request->data);
            $dap_data = DapService::where('unique_id', $request->dap_id)->first();
            $product_id = $dap_data->product_id;
            $service_partner_id = $dap_data->assign_service_perter_id;
            $service_charge = ServicePartnerCharge::select('repair')->where('service_partner_id', $service_partner_id)->where('product_id', $product_id)->first();
            if (!$service_charge) {
                return Response::json(['status' => false, 'message' => "service charge not found of this product",], 500);
            }
            DapSpearPartOrder::where('dap_id', $dap_data->id)->delete();
            $spear_part_qty = count($data);
            if ($dap_data) {
                if (count($data) > 0) {
                    $total_amount = 0; // Initialize total amount
                    foreach ($data as $item) {
                        $dap_parts = new DapSpearPartOrder();
                        $dap_parts->dap_id = $dap_data->id;
                        $dap_parts->product_id = $item->sp_id;
                        $dap_parts->title = $item->title;
                        $dap_parts->price = $item->sp_price;
                        $dap_parts->profit_percentage = $item->profit_percentage;
                        $dap_parts->final_amount = $item->final_amount;

                        // Convert warranty_status to 1 or 0 based on its boolean value
                        $dap_parts->warranty_status = filter_var($item->warranty_status, FILTER_VALIDATE_BOOLEAN) ? 1 : 0;

                        $dap_parts->save();
                        // Add the final_amount to total_amount
                        $total_amount += $item->final_amount;
                    }
                    if ($service_charge->repair === null) {
                        $service_charge->repair = 0; // Default to 0 if no service charge is found
                    }

                    $dap_data->spear_part_qty = $spear_part_qty;
                    $dap_data->total_amount = $total_amount;
                    $dap_data->final_amount = $total_amount;
                    $dap_data->total_service_charge = $service_charge->repair;
                    $dap_data->quotation_status = 1;
                    $dap_data->save();


                    $parts_data = DapSpearPartOrder::where('dap_id', $dap_data->id)->get()->toArray();

                    $data = [
                        'dap_id' => $dap_data->id,
                        'dap_barcode' => $dap_data->unique_id,
                        'mobile' => $dap_data->alternate_no,
                        'dap_product_name' => $dap_data->item,
                        'total_parts_amount' => $dap_data->total_amount,
                        'discount_amount' => $dap_data->discount_amount,
                        'spear_part_qty' => $dap_data->spear_part_qty,
                        'service_charge' => $dap_data->total_service_charge
                    ];

                    return Response::json(['status' => true, 'parts_data' => $parts_data, 'data' => $data, 'message' => "Data saved successfully",], 200);
                } else {
                    return Response::json(['status' => false, 'message' => "item not found!",], 200);
                }
            } else {
                return Response::json(['status' => false, 'message' => "DAP id  not found!",], 200);
            }
        } catch (\Exception $e) {
            // Handle any exceptions and return a generic error response
            return Response::json(['status' => false, 'message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }



    public function dap_quotation_list($id)
    {
        $data = DapService::select(
            'id',
            'product_id',
            'unique_id',
            'bill_date',
            'product_id',
            'item',
            'spear_part_qty',
            'alternate_no',
            'discount_amount',
            'final_amount',
            'total_service_charge',
            'is_paid',
            'is_cancelled',
            'quotation_status',
            'in_warranty',
            'send_otp',
            'send_otp_time',
            'otp_verified',
            'assign_service_perter_id',
            'class_name',
            'entry_date',
            'serial',
            'customer_name',
            'mobile',
            'address',
            'updated_at',
            'branch_id',
            'is_closed'
        )
            ->with('EstimateSpares', 'DiscoundData', 'branch')
            ->where('quotation_status', 1)
            ->where('is_closed', 0)
            ->where('assign_service_perter_id', $id)
            ->orderBy('updated_at', 'desc')
            ->get();
        if ($data->isNotEmpty()) {
            $dataArray = $data->toArray();
            $dataArray = collect($dataArray);
            $final_data = [];
            foreach ($dataArray as $item) {
                $general_warranty = $comprehensive_warranty = $extra_warranty = $motor_warranty = 0;

                $GoodsWarranty = GoodsWarranty::where('dealer_type', 'khosla')
                    ->where('goods_id', $item['product_id'])
                    ->first();

                if ($GoodsWarranty) {
                    $general_warranty = $GoodsWarranty->general_warranty ?: 0;
                    $comprehensive_warranty = $GoodsWarranty->comprehensive_warranty ?: 0;
                    $extra_warranty = $GoodsWarranty->extra_warranty ?: 0;
                    $motor_warranty = $GoodsWarranty->motor_warranty ?: 0;
                }

                $warranty_period = $general_warranty + $extra_warranty;
                $warranty_end_date = date('Y-m-d', strtotime($item['bill_date'] . ' + ' . $warranty_period . ' months'));
                $warranty_date = date('Y-m-d', strtotime($warranty_end_date . ' -1 days'));
                $current_date = date('Y-m-d');

                $item['general_warranty'] = false;
                $item['general_expiry_date'] = $warranty_date;
                $item['comprehensive_warranty'] = false;
                $item['comprehensive_expiry_date'] = null;
                $item['motor_warranty'] = false;
                $item['motor_expiry_date'] = null;
                $item['in_warranty'] = false;

                if ($current_date < $warranty_date) {
                    $item['general_warranty'] = true;
                    $item['comprehensive_warranty'] = true;
                    $item['motor_warranty'] = true;
                    $item['in_warranty'] = true;
                } else {
                    if ($comprehensive_warranty > 0) {
                        $warranty_end_date = date('Y-m-d', strtotime($item['bill_date'] . ' + ' . $comprehensive_warranty . ' months'));
                        $warranty_date = date('Y-m-d', strtotime($warranty_end_date . ' -1 days'));
                        if ($current_date < $warranty_date) {
                            $item['in_warranty'] = true;
                            $item['comprehensive_warranty'] = true;
                            $item['comprehensive_expiry_date'] = $warranty_date;
                        }
                    }
                    if ($motor_warranty > 0) {
                        $warranty_end_date = date('Y-m-d', strtotime($item['bill_date'] . ' + ' . $motor_warranty . ' months'));
                        $warranty_date = date('Y-m-d', strtotime($warranty_end_date . ' -1 days'));
                        if ($current_date < $warranty_date) {
                            $item['in_warranty'] = true;
                            $item['motor_warranty'] = true;
                            $item['motor_expiry_date'] = $warranty_date;
                        }
                    }
                }
                $final_data[] = $item;
            }
            return response()->json(['status' => true, 'data' => $final_data, 'message' => "Data fetched successfully"], 200);
        } else {
            return response()->json(['status' => false, 'message' => "No data found"], 200);
        }
    }

    public function dap_discount_request(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dap_id' => 'required',
            'discount_amount' => 'required',
        ]);
        DapDiscountRequest::where('dap_id', $request->dap_id)->delete();

        if (!$validator->fails()) {
            $discount  = new DapDiscountRequest();
            $discount->dap_id = $request->dap_id;
            $discount->discount_amount = $request->discount_amount;
            $discount->approval_amount = $request->discount_amount;
            $discount->status = 0;
            $discount->save();
            return Response::json(['status' => true, 'message' => 'Request has been send successfully'], 200);
        } else {
            return Response::json(['status' => false, 'message' => $validator->errors()->first(), 'data' => array($validator->errors())], 400);
        }
    }

    public function dap_payment_cancelled_is_closed(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dap_id' => 'required',
        ]);
        if (!$validator->fails()) {
            $dap_data = DapService::find($request->dap_id);
            if ($dap_data) {
                $dap_data->is_cancelled = 1; // only cancelled
                // $dap_data->is_closed = 1;
                $dap_data->save();

                return Response::json(['status' => true, 'message' => "The service for your product has been successfully closed due to the declined payment"], 200);
            } else {
                return Response::json(['status' => false, 'message' => "No record found for the provided Dap ID "], 200);
            }
        } else {
            return Response::json(['status' => false, 'message' => $validator->errors()->first(), 'data' => array($validator->errors())], 400);
        }
    }
    public function quotation_send_customer(Request $request)
    {

        $data = json_decode($request->data);

        $product_name = $data[0]->dap_product_name;
        if (strlen($product_name) > 30) {
            $product_name = substr($product_name, 0, 28) . '..';
        }
        $mobile = $data[0]->mobile;

        // $mobile = 9804503949;
        $call_id = $data[0]->dap_barcode;
        $dap_id = $data[0]->dap_id;
        $final_amount = ($data[0]->total_parts_amount + $data[0]->service_charge) - $data[0]->discount_amount;
        $otp = rand(1000, 9999);

        $checkPhoneNumberValid = checkPhoneNumberValid($mobile);
        if ($checkPhoneNumberValid) {
            $query_calling_number = "6291117317";

            $sms_entity_id = getSingleAttributeTable('settings', 'id', 1, 'sms_entity_id');
            $sms_template_id = "1707172110563286721";


            $myMessage = urlencode('Your ' . $product_name . ' Call ID ' . $call_id . ' repair charge is ' . $final_amount . '. To accept the repair please share OTP ' . $otp . ' with customer care ' . $query_calling_number . ' AMMRTL');


            $sms_url = 'https://sms.bluwaves.in/sendsms/bulk.php?username=ammrllp&password=123456789&type=TEXT&sender=AMMRTL&mobile=' . $mobile . '&message=' . $myMessage . '&entityId=' . $sms_entity_id . '&templateId=' . $sms_template_id;

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
                'phone' => $mobile,
                'message_body' => $myMessage,
                'response_body' => $response,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            $DapService = DapService::find($dap_id);
            $DapService->send_otp = $otp;
            $DapService->send_otp_time = date('Y-m-d H:i:s');
            $DapService->otp_verified = 0;
            $DapService->save();
            return response()->json(['status' => true, 'message' => "OTP has been sent successfully"], 200);
        } else {
            return response()->json(['status' => false, 'message' => "Mobile number must be 10 digits"], 500);
        }
    }


    public function quotation_otp_verify(Request $request)
    {
        DB::beginTransaction();
        try {
            $DapService = DapService::find($request->dap_id);
            if (!$DapService) {
                DB::rollBack();
                return Response::json(['status' => false, 'message' => 'Service not found'], 404);
            }
            $mobile = $DapService->alternate_no ?? 'N/A';
            // $mobile = "8617207525";
            $mobile2 = strval($mobile); // mobile no string
            $checkPhoneNumberValid = checkPhoneNumberValid($mobile);
            if (!$checkPhoneNumberValid) {
                DB::rollBack();
                return Response::json(['status' => false, 'message' => 'Mobile number must be 10 digits'], 404);
            }

            $existing_otp = $DapService->send_otp;
            $amount = ($DapService->final_amount + $DapService->total_service_charge) - $DapService->discount_amount;
            $name = $DapService->customer_name ?? 'N/A';


            if ($existing_otp != $request->otp) {
                DB::rollBack();
                return Response::json(['status' => false, 'message' => 'The OTP you entered does not match our records'], 400);
            } else {
                $dap_id = $request->dap_id;

                $DapServicePayment = DapServicePayment::where('dap_service_id', $dap_id)->get();
                // if(count($DapServicePayment)>0){
                //     DB::rollBack();
                //     return Response::json(['status' => false, 'message' => 'Payment already completed']);
                // }

                // Your success logic here
                // For example, updating the record to mark OTP as verified
                $url = env('CASHFREE_BASE_URL')."/pg/orders";

                $headers = array(
                    "Content-Type: application/json",
                    "x-api-version: ".env('CASHFREE_API_VERSION'),
                    "x-client-id: " . env('CASHFREE_API_KEY'),
                    "x-client-secret: " . env('CASHFREE_API_SECRET')
                );
                $return_url = route('dap_payment_success');

                $data = json_encode([
                    'order_id' =>  'order_' . time() . '_' . rand(11111, 99999),
                    'order_amount' => $amount,
                    "order_currency" => "INR",
                    "customer_details" => [
                        "customer_id" => 'customer_' . time() . '_' . rand(11111, 99999),
                        "customer_name" => $name,
                        "customer_phone" => $mobile2,
                    ],
                    "order_meta" => [
                        'return_url' => $return_url . '/?order_id={order_id}&order_token={order_token}&dap_id=' . $dap_id
                    ]
                ]);

                $curl = curl_init($url);

                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

                $resp = curl_exec($curl);
                curl_close($curl);
                $link = json_decode($resp)->payment_link;

                $DapService->otp_verified = 1;
                $DapService->is_paid = 2; //Pending Or Link Send
                $DapService->save();
                DB::table('dap_payment_links')->updateOrInsert(
                    ['dap_id' => $DapService->id],  // The condition to check for existing record
                    [
                        'link' => $link,            // The values to update or insert
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]
                );
                $record = DB::table('dap_payment_links')->where('dap_id', $DapService->id)->first();
                if ($link) {
                    DB::commit();
                    $url_link = route('dap_payment_link',['d'=>$record->id]);
                    // return $url_link;
                    $query_calling_number = "6291117317";

                    $sms_entity_id = getSingleAttributeTable('settings', 'id', 1, 'sms_entity_id');
                    $sms_template_id = "1707172234124956959";


                    $myMessage = urlencode('We are pleased to inform you that your product repair charge is now ready for payment. Kindly use the following link to complete the transaction: ' . $url_link . ' .AMMR TECHNOLOGY LLP');


                    $sms_url = 'https://sms.bluwaves.in/sendsms/bulk.php?username=ammrllp&password=123456789&type=TEXT&sender=AMMRTL&mobile=' . $mobile . '&message=' . $myMessage . '&entityId=' . $sms_entity_id . '&templateId=' . $sms_template_id;

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
                    return Response::json(['status' => true, 'message' => 'OTP verified successfully']);
                } else {
                    DB::rollBack();
                    return Response::json(['status' => false, 'message' => 'An error occurred while verifying OTP']);
                }
            }
        } catch (\Exception $e) {
            // dd($e->getMessage());
            DB::rollBack();
            return Response::json(['status' => false, 'message' => 'An error occurred while verifying OTP', 'error' => $e->getMessage()], 500);
        }
    }

    public function dap_service_list_cancelled($id)
    {
        $data = DapService::where('quotation_status', 1)
            ->where('assign_service_perter_id', $id)->where('is_paid', 0)->where('is_closed', 1)
            ->orderBy('id', 'desc')
            ->get()->toArray();

        if ($data) {
            return response()->json(['status' => true, 'data' => $data], 200);
        } else {
            return response()->json(['status' => false, 'message' => 'No data found'], 200);
        }
    }
    public function dap_service_list_successed($id)
    {
        $data = DapService::where('quotation_status', 1)
            ->where('assign_service_perter_id', $id)->where('is_paid', 1)->where('is_closed', 1)
            ->orderBy('id', 'desc')
            ->get()->toArray();

        if ($data) {
            return response()->json(['status' => true, 'data' => $data], 200);
        } else {
            return response()->json(['status' => false, 'message' => 'No data found'], 200);
        }
    }

    public function dap_product_return_showroom($id)
    {
        $dap_product = DapService::find($id);
        if ($dap_product->service_centre_dispatch == 1) {
            return response()->json(['status' => true, 'message' => 'Product already dispatched from service center to showrrom'], 200);
        } else {
            $dap_product->service_centre_dispatch = 1;
            $dap_product->service_centre_dispatch_date = now();
            $dap_product->save();
            if ($dap_product) {

                $product_name = $dap_product->item;
                if (strlen($product_name) > 30) {
                    $product_name = substr($product_name, 0, 28) . '..';
                }
                $mobile = $dap_product->alternate_no;
                $branch = $dap_product->return_branch ? $dap_product->return_branch->name : "";
                $call_id = $dap_product->unique_id;
                $download_url = route('invoice',['d'=>$dap_product->id]);

                $final_amount = ($dap_product->total_amount + $dap_product->total_service_charge) - $dap_product->discount_amount;
                $query_calling_number = "6291117317";
                $sms_entity_id = getSingleAttributeTable('settings', 'id', 1, 'sms_entity_id');
                $sms_template_id = "1707172110576165514";

                $myMessage = urlencode('Your product ' . $product_name . ' Call ID ' . $call_id . ' has been repaired. Please collect from ' . $branch . '. Click to download bill ' . $download_url . '. For assistance call ' . $query_calling_number . '.AMMRTL');

                $sms_url = 'https://sms.bluwaves.in/sendsms/bulk.php?username=ammrllp&password=123456789&type=TEXT&sender=AMMRTL&mobile=' . $mobile . '&message=' . $myMessage . '&entityId=' . $sms_entity_id . '&templateId=' . $sms_template_id;

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

                DB::table('sms_api_response')->insert([
                    'sms_template_id' => $sms_template_id,
                    'sms_entity_id' => $sms_entity_id,
                    'phone' => $mobile,
                    'message_body' => $myMessage,
                    'response_body' => $response,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

                return response()->json(['status' => true, 'message' => 'Successfully to dispatched from service center to showrrom'], 200);
            }
            return response()->json(['status' => false, 'message' => 'Failed to dispatched from service center to showrrom'], 200);
        }
    }
    public function display_all_detais_before_dispatched_from_showrrom($barcode)
    {
        $dap_product = DapService::with('branch', 'return_branch', 'serviceCentre', 'product')->where('unique_id', $barcode)->first();
        if ($dap_product) {
            return response()->json(['status' => true, 'data' => $dap_product], 200);
        } else {
            return response()->json(['status' => false, 'message' => 'No data found'], 400);
        }
    }
    public function download_return_road_challan($id)
    {
        $dap_data = DapService::with('branch', 'return_branch', 'serviceCentre', 'product')->where('id', $id)->first();
        if ($dap_data) {
            // Convert the record to an array
            $data = $dap_data->toArray();
            $date = date('d-M-y');
            $pdf = Pdf::loadView('dap-services.download-return-road-challan', compact('data', 'date'));
            return $pdf->download('Road-Challan.pdf');
        }
    }
    public function return_road_challan_generate(Request $request)
    {
        //$request->all();

        // $validator = Validator::make($data, [
        //     'dap_id' => 'required',
        //     'return_branch_id' => 'required',
        //     'return_type' => 'required',
        // ]);

        // if ($validator->fails()) {
        //     return response()->json(['status' => false, 'message' => $validator->errors()->first(), 'data' => $validator->errors()], 400);
        // }
        if (empty($request->dap_id)) {
            return response()->json(['status' => false, 'message' => 'Dap id not found'], 404);
        }
        if (empty($request->return_branch_id)) {
            return response()->json(['status' => false, 'message' => 'Return branch  id not found'], 404);
        }
        if (empty($request->return_type)) {
            return response()->json(['status' => false, 'message' => 'Return type not found'], 404);
        }

        $dap_data = DapService::where('id', $request->dap_id)->first();
        if (!$dap_data) {
            return response()->json(['status' => false, 'message' => 'DapService not found'], 404);
        }
        $dap_data->return_road_challan = 1;
        $dap_data->return_type = $request->return_type;
        if ($request->return_type == "by_vehicle") {
            $dap_data->return_vehicle_number = $request->return_vehicle_number;
            $dap_data->return_transport_file = NULL;
        } elseif ($request->return_type == "by_transport") {

            $dap_data->return_vehicle_number = NULL;
            if (!empty($request->return_transport_file)) {
                $upload_path = public_path("uploads/DAP/Transport");
                $image = $request->return_transport_file;
                $imageName = time() . "." . $image->getClientOriginalExtension();
                $image->move($upload_path, $imageName);
                $uploadedImage = $imageName;
                $dap_data->return_transport_file = 'uploads/DAP/Transport/' . $uploadedImage;
            } else {
                return response()->json(['status' => false, 'message' => 'Return Transport file not found'], 404);
            }
        }
        $dap_data->return_branch_id = $request->return_branch_id;
        $dap_data->save();

        return response()->json(['status' => true, 'message' => 'Return road challan generated successfully'], 200);
    }

    private function savePackingslipBarcodes($packingslip_id,$barcodeArr){
        foreach($barcodeArr as $barcode_no){

            $stock_barcode = StockBarcode::select('id', 'product_id')->where('barcode_no', $barcode_no)->first();
            $product_id = $stock_barcode->product_id;
            $psBarcodeArr = array(
                'packingslip_id' => $packingslip_id,
                'product_id' => $product_id,
                'barcode_no' => $barcode_no,
                'created_at' => date('Y-m-d H:i:s')
            );
            PackingslipBarcode::insert($psBarcodeArr);
        }
    }

    private function ledgerEntry($user_type,$ledger_user_id,$amount,$invoice_no){
        if($user_type == 'dealer'){
            Ledger::insert([
                'type' => 'debit',
                'dealer_id' => $ledger_user_id,
                'amount' => $amount,
                'entry_date' => date('Y-m-d'),
                'user_type' => 'dealer',
                'purpose' => 'invoice',
                'transaction_id' => $invoice_no,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        } else if ($user_type == 'servicepartner'){;
            Ledger::insert([
                'type' => 'debit',
                'service_partner_id' => $ledger_user_id,
                'amount' => $amount,
                'entry_date' => date('Y-m-d'),
                'user_type' => 'servicepartner',
                'purpose' => 'invoice',
                'transaction_id' => $invoice_no,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
    }
}
