<?php

namespace App\Http\Controllers\Api\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use App\User;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderProduct;
use App\Models\PurchaseOrderBarcode;
use App\Models\StockBarcode;
use App\Models\Packingslip;
use App\Models\PackingslipBarcode;
use App\Models\PackingslipProduct;
use App\Models\ReturnSpare;
use App\Models\ReturnSpareItem;
use App\Models\ReturnSpareBarcode;

class ScanController extends Controller
{
    private $staff_id;
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
                $this->staff_id = Crypt::decrypt($token);  
                $staff = User::find($this->staff_id);           
            } catch (DecryptException $e) {
                response()->json(["status"=>false,"message"=>"Mismatched token"],400)->send();
                exit();
            }
        }

    }

    public function stockin(Request $request)
    {
        # scan for stock in ...
        $validator = Validator::make($request->all(),[
            'barcode_no' => 'required|exists:purchase_order_barcodes,barcode_no',
            'purchase_order_id' => 'required|numeric|exists:purchase_orders,id',
            'product_id' => 'required|numeric|exists:products,id'
        ]);

        if(!$validator->fails()){
            $params = $request->except('_token');
            $purchase_order_id = $params['purchase_order_id'];
            $product_id = $params['product_id'];

            $purchaseorder =  PurchaseOrder::find($purchase_order_id);
            $type = $purchaseorder->type;
            if($purchaseorder->goods_out_in == 'bulk'){
                return Response::json(['status' => false, 'message' => "This PO items are for bulk out" ],200);
            }
            $purchase_order_products = PurchaseOrderProduct::where('purchase_order_id',$purchase_order_id)->where('product_id',$product_id)->first();

            

            if(!empty($purchase_order_products)){
                if($type == 'fg'){
                    $quantity = $purchase_order_products->quantity;
                } else {
                    $quantity = $purchase_order_products->pack_of;
                }
                $scan_product_quantity = $quantity;
                $po_product_id = $purchase_order_products->id;

                $count_scanned = PurchaseOrderBarcode::where('purchase_order_id',$purchase_order_id)->where('product_id',$product_id)->where('is_stock_in',1)->where('is_scanned',1)->count();

                if($count_scanned < $scan_product_quantity){
                    // echo "More scan required"; die;
                    $barcode_no = $params['barcode_no'];
                    $exist = PurchaseOrderBarcode::where('barcode_no', $barcode_no)->first();
                    
                    if(!empty($exist)){
                        if(empty($exist->is_stock_in)){
                            if(empty($exist->is_scanned)){                            
                                PurchaseOrderBarcode::where('barcode_no',$barcode_no)->update([
                                    'is_scanned' => 1,
                                    'scanned_by' => $this->staff_id
                                ]);
    
                                $count_product_scanned = PurchaseOrderBarcode::where('purchase_order_id',$purchase_order_id)->where('product_id',$product_id)->where('is_scanned',1)->count();
    
                                return Response::json(['status' => true, 'message' => "Scanned successfully", 'data' => array(
                                    'required_product_scan' => $scan_product_quantity,
                                    'count_product_scanned' => $count_product_scanned,
                                    'else_product_scan' => ($scan_product_quantity - $count_product_scanned)
                                ) ],200);
                            } else {
                                return Response::json(['status' => false, 'message' => "Already scanned" ],200);
                            }
                        } else {
                            return Response::json(['status' => false, 'message' => "Already stock in " ],200);
                        }
                        
                    } else {
                        return Response::json(['status' => false, 'message' => "No barcode found" ],200);
                    } 
                } else {
                    // echo "All required are scanned"; die;
                    return Response::json(['status' => false, 'message' => "".$count_scanned." items scanning are completed for this PO product" ],200);
                }
                                
            } else {
                return Response::json(['status' => false, 'message' => "Product not found in this PO" ],200);
            }
                        
        } else {
            return Response::json(['status' => false, 'message' => $validator->errors()->first() , 'data' => array( $validator->errors() ) ],400);
        } 
    }

    public function stockout(Request $request)
    {
        # scan for stock out ...
        $validator = Validator::make($request->all(),[
            'barcode_no' => 'required|exists:stock_barcodes,barcode_no',
            'packingslip_id' => 'required|numeric|exists:packingslips,id',
            'product_id' => 'required|numeric|exists:products,id'
        ]);

        if(!$validator->fails()){
            $params = $request->except('_token');
            $packingslip_id = $params['packingslip_id'];
            $product_id = $params['product_id'];

            $packingslips =  Packingslip::find($packingslip_id);
            if($packingslips->goods_out_type == 'bulk'){
                return Response::json(['status' => false, 'message' => "This packing slip items are for bulk out" ],200);
            }
            $packingslip_products = PackingslipProduct::where('packingslip_id',$packingslip_id)->where('product_id',$product_id)->first();

            

            if(!empty($packingslip_products)){
                $scan_product_quantity = $packingslip_products->quantity;
                $packingslip_product_id = $packingslip_products->id;

                $count_scanned = StockBarcode::where('packingslip_id',$packingslip_id)->where('product_id',$product_id)->count();

                if($count_scanned < $scan_product_quantity){
                    // echo "More scan required"; die;
                    $barcode_no = $params['barcode_no'];
                    $exist = StockBarcode::where('barcode_no', $barcode_no)->first();
                    
                    if(!empty($exist)){
                        
                        if(empty($exist->is_scanned) && empty($exist->is_stock_out) && empty($exist->packingslip_id)){
                            
                            StockBarcode::where('barcode_no',$barcode_no)->update([
                                'is_scanned' => 1, 
                                'packingslip_id' => $packingslip_id,
                                'scanned_by' => $this->staff_id
                            ]);

                            $count_product_scanned = StockBarcode::where('packingslip_id',$packingslip_id)->where('product_id',$product_id)->where('is_scanned',1)->count();

                            return Response::json(['status' => true, 'message' => "Scanned successfully", 'data' => array(
                                'required_product_scan' => $scan_product_quantity,
                                'count_product_scanned' => $count_product_scanned,
                                'else_product_scan' => ($scan_product_quantity - $count_product_scanned)
                            ) ],200);
                        } else {
                            return Response::json(['status' => false, 'message' => "Already scanned" ],200);
                        }
                    } else {
                        return Response::json(['status' => false, 'message' => "No barcode found" ],200);
                    } 
                } else {
                    // echo "All required are scanned"; die;
                    return Response::json(['status' => false, 'message' => "".$count_scanned." items scanning are completed for this packing slip product" ],200);
                }
                                
            } else {
                return Response::json(['status' => false, 'message' => "Product not found in this packing slip" ],200);
            }
                        
        } else {
            return Response::json(['status' => false, 'message' => $validator->errors()->first() , 'data' => array( $validator->errors() ) ],400);
        }
    }

    public function return_spares_stockin(Request $request)
    {
        # Return Spare Scan In...
        $validator = Validator::make($request->all(),[
            'barcode_no' => 'required|exists:return_spare_barcodes,barcode_no',
            'return_spare_id' => 'required|numeric|exists:return_spare,id',
            'product_id' => 'required|numeric|exists:products,id'
        ]);

        if(!$validator->fails()){
            $params = $request->except('_token');
            $return_spare_id = $params['return_spare_id'];
            $product_id = $params['product_id'];

            $retun_spare =  ReturnSpare::find($return_spare_id);
            $type = $retun_spare->type;
            if($retun_spare->goods_out_in == 'bulk'){
                return Response::json(['status' => false, 'message' => "This Return Spare items are for bulk out" ],200);
            }
            $return_spare_items = ReturnSpareItem::where('return_spare_id',$return_spare_id)->where('product_id',$product_id)->first();            

            if(!empty($return_spare_items)){                
                $quantity = $return_spare_items->quantity;
                
                $scan_product_quantity = $quantity;

                $count_scanned = ReturnSpareBarcode::where('return_spare_id',$return_spare_id)->where('product_id',$product_id)->where('is_stock_in',1)->where('is_scanned',1)->count();

                if($count_scanned < $scan_product_quantity){
                    // echo "More scan required"; die;
                    $barcode_no = $params['barcode_no'];
                    $exist = ReturnSpareBarcode::where('barcode_no', $barcode_no)->first();
                    
                    if(!empty($exist)){                        
                        if(empty($exist->is_scanned) && empty($exist->is_stock_in) ){
                            ReturnSpareBarcode::where('barcode_no',$barcode_no)->update([
                                'is_scanned' => 1, 
                                'is_stock_in' => 1,
                                'scanned_by' => $this->staff_id
                            ]);
                            $count_product_scanned = ReturnSpareBarcode::where('return_spare_id',$return_spare_id)->where('product_id',$product_id)->where('is_scanned',1)->count();

                            return Response::json(['status' => true, 'message' => "Scanned successfully", 'data' => array(
                                'required_product_scan' => $scan_product_quantity,
                                'count_product_scanned' => $count_product_scanned,
                                'else_product_scan' => ($scan_product_quantity - $count_product_scanned)
                            ) ],200);
                        } else {
                            return Response::json(['status' => false, 'message' => "Already scanned" ],200);
                        }
                    } else {
                        return Response::json(['status' => false, 'message' => "No barcode found" ],200);
                    } 
                } else {
                    // echo "All required are scanned"; die;
                    return Response::json(['status' => false, 'message' => "".$count_scanned." items scanning are completed for this PO product" ],200);
                }                                
            } else {
                return Response::json(['status' => false, 'message' => "Product not found in this PO" ],200);
            }                        
        } else {
            return Response::json(['status' => false, 'message' => $validator->errors()->first() , 'data' => array( $validator->errors() ) ],400);
        }
    }
}
