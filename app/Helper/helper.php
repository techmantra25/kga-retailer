<?php
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Models\StockLog;
use App\Models\CustomerPointService;
use App\Models\Changelog;
use App\Models\CustomerPointServiceLog;
use App\Models\DapServiceLog;
use App\Models\DapService;
use App\Models\DapServicePayment;
use App\Models\Maintenance;
use App\Models\CrpServicePayment;
use App\Models\AmcServicePayment;
use App\Models\KgaSalesData;
use App\Models\ProductWarranty;
use App\Models\ProductAmc;
use App\Models\AmcSubscription;
use Carbon\Carbon;

function test(){
    return "Test";
}

function barcodeGenerator($barcode_no){
    $length = 12;    
    $min = str_repeat(0, $length-1) . 1;
    $max = str_repeat(9, $length);
    // $barcode_no =  mt_rand($min, $max);   

    $generator = new Picqer\Barcode\BarcodeGeneratorHTML();
    $generatorSVG = new Picqer\Barcode\BarcodeGeneratorSVG(); // Vector based SVG
    $generatorPNG = new Picqer\Barcode\BarcodeGeneratorPNG(); // Pixel based PNG
    $generatorJPG = new Picqer\Barcode\BarcodeGeneratorJPG(); // Pixel based JPG
    $generatorHTML = new Picqer\Barcode\BarcodeGeneratorHTML(); // Pixel based HTML
    $generatorDynamicHTML = new Picqer\Barcode\BarcodeGeneratorDynamicHTML(); // Vector based HTML

    $code_html = $generator->getBarcode($barcode_no, $generator::TYPE_CODE_128);
    $code_base64_img = base64_encode($generatorPNG->getBarcode($barcode_no, $generatorPNG::TYPE_CODE_128));

    return array('barcode_no'=>$barcode_no,'code_html'=>$code_html,'code_base64_img'=>$code_base64_img);
}

function barcodeGeneratorWithNo($no){
    $generator = new Picqer\Barcode\BarcodeGeneratorHTML();
    $generatorSVG = new Picqer\Barcode\BarcodeGeneratorSVG(); // Vector based SVG
    $generatorPNG = new Picqer\Barcode\BarcodeGeneratorPNG(); // Pixel based PNG
    $generatorJPG = new Picqer\Barcode\BarcodeGeneratorJPG(); // Pixel based JPG
    $generatorHTML = new Picqer\Barcode\BarcodeGeneratorHTML(); // Pixel based HTML
    $generatorDynamicHTML = new Picqer\Barcode\BarcodeGeneratorDynamicHTML(); // Vector based HTML

    $code_html = $generator->getBarcode($no, $generator::TYPE_CODE_128);
    $code_base64_img = base64_encode($generatorPNG->getBarcode($no, $generatorPNG::TYPE_CODE_128));
    return array('barcode_no'=>$no,'code_html'=>$code_html,'code_base64_img'=>$code_base64_img);
}

function genAutoIncreNo($length=5,$table='products'){
    $val = 1;    
    $data = DB::table($table)->select('id')->orderBy('id','desc')->first();
    if(empty($data)){
        $val = 1;
    } else {
        $val = $data->id + 1;
    }    
    $number = str_pad($val,$length,"0",STR_PAD_LEFT);
    return $number;
}

function genAutoIncreNoYearWise($length=5,$table='sales_orders',$year = null){
    $val = 1;    
    $data = DB::table($table)->whereRaw("DATE_FORMAT(created_at, '%Y') = '".$year."'")->count();

    if(!empty($data)){
        $val = ($data + 1);
    }
    // $number = str_pad($val,$length,"0",STR_PAD_LEFT);
    do {
        $number = str_pad($val, $length, "0", STR_PAD_LEFT);
        $unique_id = $year.$number;
        $exists = DB::table($table)->where('unique_id', $unique_id)->exists();
        $val++;
    } while ($exists);
    return $unique_id;
}

function genAutoIncreNoYearWiseOrder($length=4,$table='sales_orders',$year = null,$month = null){
    # PO , GRN, SALES ORDER , RETURN ORDER
    $val = 1;    
    $data = DB::table($table)->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = '".$year."-".$month."'  ")->count();

    if(!empty($data)){
        $val = ($data + 1);
    }

    $number = str_pad($val,$length,"0",STR_PAD_LEFT);
    
    return $year.''.$month.''.$number;
}
function genAutoIncreNoYearWiseCallBook($length=4,$table = null,$year = null,$month = null ,$prefix = ''){
    # PO , GRN, SALES ORDER , RETURN ORDER
    $val = 1;    
    $data = DB::table($table)->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = '".$year."-".$month."'  ")->count();
    if(!empty($data)){
        $val = ($data + 1);
    }
    // $number = str_pad($val,$length,"0",STR_PAD_LEFT);
    do {
        $number = str_pad($val, $length, "0", STR_PAD_LEFT);
        $unique_id = $prefix.$year.$month.$number;
        $exists = DB::table($table)->where('unique_id', $unique_id)->exists();
        $val++;
    } while ($exists);
    
    return $unique_id;
}

function getSingleAttributeTable($tableName,$idColumn,$idValue,$attribute){    
    $data = DB::table($tableName)->select($attribute)->where($idColumn,$idValue)->first();
    return $data->$attribute;
}

function isBulkScanned($purchase_order_id,$product_id){
    $data = DB::table('purchase_order_barcodes')->where('purchase_order_id',$purchase_order_id)->where('product_id',$product_id)->where('is_bulk_scanned', 1)->first();

    if(!empty($data)){
        return true;
    } else {
        return false;
    }
}

function isBulkScannedReturnSpare($return_spare_id,$product_id){
    $data = DB::table('return_spare_barcodes')->where('return_spare_id',$return_spare_id)->where('product_id',$product_id)->where('is_bulk_scanned', 1)->first();

    if(!empty($data)){
        return true;
    } else {
        return false;
    }
}

function sendMail($data){
    // $mail = Mail::send(['text'=>'mailview'] , $data, function ($message) use ($data) {
    //     $message->to($data['email'], $data['name'])->subject($data['subject']);
    // });
    try{        
        $mail = Mail::send([], [], function ($message) use ($data)  {
            $message->to($data['email'],$data['name'])
              ->subject($data['subject'])
              // here comes what you want
            //   ->setBody('Hi, welcome user!'); // assuming text/plain
              // or:
              ->setBody($data['body'], 'text/html'); // for HTML rich messages
          });
    
        return true;
    } catch(Exception $e){
        return $e;
    }
    
}

function mailSendAttachments($data,$files){
    try{        
        $mail = Mail::send([], [], function ($message) use ($data, $files)  {
            $message->to($data['email'],$data['name'])->subject($data['subject'])->setBody($data['body'], 'text/html'); // for HTML rich messages
            foreach ($files as $file){
                $message->attach($file);
            } 
        });
    
        return true;
    } catch(Exception $e){
        return $e;
    }
}

function getDateValue($string){
    // die($excelDateTime);
    $date = str_replace('/', '-', $string);
    return  date('Y-m-d', strtotime($date));
}

function getStockProduct($product_id){
    $data = DB::table('stock_barcodes')->where('product_id',$product_id)->where('is_scanned', 0)->count();
    return $data;
}

function getStockInventoryProduct($product_id){
    $data = DB::table('stock_inventory')->where('product_id',$product_id)->first();
    if(!empty($data)){
        return $data->quantity;
    } else {
        return 0;
    }
}

function updateStockInvetory($product_id,$quantity,$type='in',$data_id = null,$data_type = ''){
    # $type is 'in/out'
    $data = DB::table('stock_inventory')->where('product_id',$product_id)->first();
    $stock_quantity = 0;
    if(!empty($data)){
        $stock_quantity = $data->quantity;
        if($type == 'in'){
            $net_quantity = ($stock_quantity + $quantity);
            DB::table('stock_inventory')->where('product_id',$product_id)->update(['quantity'=>$net_quantity,'updated_at' => date('Y-m-d H:i:s')]);
        } else if ($type == 'out'){
            $net_quantity = ($stock_quantity - $quantity);
            DB::table('stock_inventory')->where('product_id',$product_id)->update(['quantity'=>$net_quantity,'updated_at' => date('Y-m-d H:i:s')]);
        }
        
    } else {
        DB::table('stock_inventory')->insert([
            'product_id' => $product_id,
            'quantity' => $quantity,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    $purchase_order_id = null;
    $packingslip_id = null;
    $return_spare_id = null;
    $dealer_purchase_order_id = null;
    if($type == 'in'){
        $entry_type = 'grn';
        
        if($data_type == 'packingslip'){
            $packingslip_id = $data;
        } else if ($data_type == 'purchase_order'){
            $purchase_order_id = $data_id;
        } else if ($data_type == 'return_spares'){
            $return_spare_id = $data_id;
        } else if ($data_type == 'dealer_purchase_order'){
            $dealer_purchase_order_id = $data_id;
        }        
        
    } else if ($type == 'out'){
        $entry_type = 'ps';
        $packingslip_id = $data_id;
    }
    
    StockLog::insert([
        'product_id' =>$product_id,
        'quantity' => $quantity,
        'type' => $type,
        'data_id' => $data_id,
        'entry_type' => $entry_type,
        'purchase_order_id' => $purchase_order_id,
        'packingslip_id' => $packingslip_id,
        'return_spare_id' => $return_spare_id,
        'dealer_purchase_order_id' => $dealer_purchase_order_id,
        'created_at' => date('Y-m-d H:i:s')
    ]);
    
}

function getSalesOrderProduct($sales_orders_id,$product_id){
    $data = DB::table('sales_order_products')->where('sales_orders_id',$sales_orders_id)->where('product_id',$product_id)->first();

    return $data;
}

function updateSalesOrderStatusPS($sales_order_id){
    $order_status = 'pending';
    $sales_order_products = DB::table('sales_order_products')->where('sales_orders_id', $sales_order_id)->get();
    $isAllCompleted = 0;
    $isCompleteArr = array();
    foreach($sales_order_products as $pro){
        if($pro->quantity == $pro->delivered_quantity){
            $isAllCompleted = 1;            
        } else {
            $isAllCompleted = 0;
        }
        $pro->is_all_completed = $isAllCompleted;
        $isCompleteArr[] = $isAllCompleted;
    }

    if(in_array(0,$isCompleteArr)){
        $order_status = 'ongoing';
    } else {
        $order_status = 'completed';
    }
    DB::table('sales_orders')->where('id',$sales_order_id)->update([
        'status' => $order_status
    ]);
}

function getAmountAlphabetically($amount)
{
    $number = $amount;
    $no = floor($number);
    $point = round($number - $no, 2) * 100;
    $hundred = null;
    $digits_1 = strlen($no);
    $i = 0;
    $str = array();
    $words = array('0' => '', '1' => 'one', '2' => 'two',
    '3' => 'three', '4' => 'four', '5' => 'five', '6' => 'six',
    '7' => 'seven', '8' => 'eight', '9' => 'nine',
    '10' => 'ten', '11' => 'eleven', '12' => 'twelve',
    '13' => 'thirteen', '14' => 'fourteen',
    '15' => 'fifteen', '16' => 'sixteen', '17' => 'seventeen',
    '18' => 'eighteen', '19' =>'nineteen', '20' => 'twenty',
    '30' => 'thirty', '40' => 'forty', '50' => 'fifty',
    '60' => 'sixty', '70' => 'seventy',
    '80' => 'eighty', '90' => 'ninety');
    $digits = array('', 'hundred', 'thousand', 'lakh', 'crore');
    while ($i < $digits_1) {
        $divider = ($i == 2) ? 10 : 100;
        $number = floor($no % $divider);
        $no = floor($no / $divider);
        $i += ($divider == 10) ? 1 : 2;
        if ($number) {
        $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
        $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
        $str [] = ($number < 21) ? $words[$number] .
            " " . $digits[$counter] . $plural . " " . $hundred
            :
            $words[floor($number / 10) * 10]
            . " " . $words[$number % 10] . " "
            . $digits[$counter] . $plural . " " . $hundred;
        } else $str[] = null;
    }
    $str = array_reverse($str);
    $result = implode('', $str);
    $points = ($point) ? "" . $words[$point / 10] . " " . $words[$point = $point % 10] : 'Zero';
    return  ucwords($result) . " Rupees  and " . $points . " Paise";
}

function getPercentageVal($percent,$number)
{
    return ($percent / 100) * $number;
}

function getGSTAmount($price,$gst_val){
    $gst_amount = $price - ( $price * ( 100 / ( 100 + (getPercentageVal($gst_val,100)) ) ) );
    $net_price = ($price - $gst_amount);

    return array('gst_amount' => $gst_amount , 'net_price' => $net_price);
}

function getPSProductQuantity($packingslip_id,$product_id){
    $data = DB::table('packingslip_products')->where('packingslip_id',$packingslip_id)->where('product_id',$product_id)->first();
    return $data->quantity;
}

function checkStockProductScanned($barcode_no){
    $data = DB::table('stock_barcodes')->select('id','stock_id','product_id','barcode_no','is_scanned','is_stock_out','packingslip_id')->where('barcode_no',$barcode_no)->first();
    // dd($data);
    if(!empty($data)){
        if(!empty($data->is_scanned) && !empty($data->packingslip_id)){
            return 1;
        } else {
            return 0;
        }
    } else {
        return 0;
    }
}

function genAutoIncreNoBarcode($product_id,$year,$type=''){
    $val = 1;    
    if(empty($type)){
        $data = DB::table('purchase_order_barcodes')->where('product_id',$product_id)->whereRaw("DATE_FORMAT(created_at, '%Y') = '".$year."'")->count();
    } else {
        if($type == 'return_spare')
        $data = DB::table('return_spare_barcodes')->where('product_id',$product_id)->whereRaw("DATE_FORMAT(created_at, '%Y') = '".$year."'")->count();
    }
    

    if(!empty($data)){
        $val = ($data + 1);
    }

    $product_unique_id = getSingleAttributeTable('products','id',$product_id,'unique_id');

    // dd($data);
    $prefix = 'K'.$product_unique_id.''.$year.'';
    $suffix = str_pad($val,5,"0",STR_PAD_LEFT);
    $number = $prefix.''.$suffix;

    $barcode_no = $number;
    if($type == 'return_spare'){
        $barcode_no = 'RE'.$number;
    }
    $generator = new Picqer\Barcode\BarcodeGeneratorHTML();
    $generatorPNG = new Picqer\Barcode\BarcodeGeneratorPNG();

    $code_html = $generator->getBarcode($barcode_no, $generator::TYPE_CODE_128);

    $code_base64_img = base64_encode($generatorPNG->getBarcode($barcode_no, $generatorPNG::TYPE_CODE_128));

    return array('barcode_no'=>$barcode_no,'code_html'=>$code_html,'code_base64_img'=>$code_base64_img);
    
}

function checkPhoneNumberValid($number){
    $res = false;
    if(is_numeric($number) && (strlen($number) == 10)){
        $res = true;
    }
    return $res;
}

function getServicePartnerProductCharges($service_partner_id,$product_id){
    $data = DB::table('service_partner_charges')->where('service_partner_id',$service_partner_id)->where('product_id',$product_id)->first();

    return $data;
}

function checkServicePartnerProductCharge($type='',$service_partner_id = '',$product_id = ''){
    $data = DB::table('service_partner_charges')->where('service_partner_id',$service_partner_id)->where('product_id',$product_id)->first();
    $amount = '';
    if(!empty($data)){
        if($type == 'installation'){
            if(!empty($data->installation)){
                $amount = $data->installation;
            }
        } else if($type == 'repair'){
            if(!empty($data->repair)){
                $amount = $data->repair;
            }
        }
    }
    return $amount;
}

function getCrDr($amount){
    if($amount > 0){
        return "Cr"; # if postive +
    } else if($amount < 0) {
        return "Dr"; # if negative -
    } else {
        return "";
    }
}

function replaceMinusSign($number){
    return str_replace("-","",$number);
}

function getRepairSpares($repair_id,$product_id){
    $data = DB::table('repair_spares')->where('repair_id',$repair_id)->where('product_id',$product_id)->first();

    return $data;
}

function getMaintenanceSpares($maintenance_id,$product_id){
    $data = DB::table('maintenance_spares')->where('maintenance_id',$maintenance_id)->where('product_id',$product_id)->first();

    return $data;
}

function get_spare_goods_names($spare_id){
    $data = DB::select("SELECT GROUP_CONCAT(products.title) AS goods_name FROM spare_goods LEFT JOIN products ON products.id = spare_goods.goods_id WHERE spare_id = ".$spare_id." ");

    return $data[0]->goods_name;

    
}

function getBarcodeDetails($barcode_no){
    $data = DB::table('purchase_order_barcodes')->where('barcode_no',$barcode_no)->first();
    return array(
        'code_html'=>$data->code_html,
        'code_base64_img'=>$data->code_base64_img,
        'product_id'=>$data->product_id
    );
}

function dates_month($month, $year) {
    $num = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $month_names = array();
    $date_values = array();

    for ($i = 1; $i <= $num; $i++) {
        $mktime = mktime(0, 0, 0, $month, $i, $year);
        $date = date("d (D)", $mktime);
        $month_names[$i] = $date;
        $date_values[$i] = date("Y-m-d", $mktime);
    }

    return ['month_names'=>$month_names,'date_values'=>$date_values];
}

function getGoodsWarrantyEndDate($product_id,$bill_date){
    $product = \App\Models\Product::find($product_id);
    if(!empty($product)){

        if($product->goods_type == 'chimney'){
            $comprehensive_warranty = $product->comprehensive_warranty;

            $warranty_date = date('Y-m-d', strtotime($bill_date. ' + '.$comprehensive_warranty.' months'));
            $warranty_end_date = date('Y-m-d', strtotime($warranty_date.'-1 days'));
            return array('status' => true,'warranty_end_date'=>$warranty_end_date);

        } else {
            ## For General 
            $warranty_status = $product->warranty_status;
            $warranty_period = $product->warranty_period;

            if($warranty_status == 'yes' && !empty($warranty_period)){
                $warranty_date = date('Y-m-d', strtotime($bill_date. ' + '.$warranty_period.' months'));
                $warranty_end_date = date('Y-m-d', strtotime($warranty_date.'-1 days'));
                return array('status' => true,'warranty_end_date'=>$warranty_end_date);
            } else {
                return array('status' => false);
            }
        }

        return array('status' => true);
    } else {
        return array('status' => false);
    }
}

function getAmcDuration($product_id,$month_val){
    $data = DB::table('product_amcs')->where('product_id',$product_id)->where('month_val',$month_val)->first();
    return $data;
}

function addChangeLog($user_id,$ip_address,$action_type,$browser_name,$navigator_useragent,$params){
    Changelog::insert([
        'user_id' => $user_id,
        'ip_address' => $ip_address,
        'action_type' => $action_type,
        'browser_name' => $browser_name,
        'navigator_useragent' => $navigator_useragent,
        'details' => json_encode($params),
        'created_at' => date('Y-m-d H:i:s')
    ]);
}
function AddCustomerPointLog($service_id, $purpose){
    $store = new CustomerPointServiceLog;
    $store->service_id = $service_id;
    $store->purpose = $purpose;
    $store->created_at = now();
    $store->save();
}
function AddDapServiceLog($service_id, $purpose){
    $store = new DapServiceLog;
    $store->service_id = $service_id;
    $store->purpose = $purpose;
    $store->created_at = now();
    $store->save();
}

function userAccess($role_id,$module_id){
    $role_module_restictions = DB::table('role_module_restictions')->where('role_id',$role_id)->where('module_id',$module_id)->first();

    if(!empty($role_module_restictions)){
        return false;
    } else {
        return true;
    }
}

function is_item_supplier_warranty($goods_id,$order_date){
    # Check the spare for goods within supplier warranty period or not
    
    $goods = \App\Models\Product::find($goods_id);
    $supplier_warranty_period = $goods->supplier_warranty_period;
    
    $warranty_end_date = date('Y-m-d', strtotime($order_date. ' + '.$supplier_warranty_period.' months'));
    $warranty_date = date('Y-m-d', strtotime($warranty_end_date.'-1 days'));
    
    $in_warranty = 1;
    if(date('Y-m-d') > $warranty_date){
        $in_warranty = 0;
    } else {
        $in_warranty = 1;
    }
    return $in_warranty;
    
    
}
function GetCleaningWarranty($serial){
    $used_cleaning = Maintenance::where('product_sl_no',$serial)->where('service_type','cleaning')->where('is_closed',1)->where('is_amc',0)->count();
    return $used_cleaning;
} 

function GetDeepCleaningWarranty($serial){
   $used_deep_cleaning = Maintenance::where('product_sl_no',$serial)->where('service_type','deep_cleaning')->where('is_closed',1)->where('is_amc',0)->count();
	return $used_deep_cleaning;
}

function GetAmcCleaningWarranty($serial){
    $used_amc_cleaning = Maintenance::where('product_sl_no',$serial)->where('service_type','cleaning')->where('is_closed',1)->where('is_amc',1)->count();
    return $used_amc_cleaning;
} 

function GetAmcDeepCleaningWarranty($serial){
   $used_amc_deep_cleaning = Maintenance::where('product_sl_no',$serial)->where('service_type','deep_cleaning')->where('is_closed',1)->where('is_amc',1)->count();
	return $used_amc_deep_cleaning;
}

function ActualAmcCleaningWarranty($serial,$amc_start_date){
    $used_amc_cleaning = Maintenance::where('product_sl_no',$serial)->where('service_type','cleaning')->where('is_closed',1)->where('is_amc',1)->where('created_at','>=', $amc_start_date.' 01:01:01')->count();
    return $used_amc_cleaning;
} 

function ActuallAmcDeepCleaningWarranty($serial,$amc_start_date){
   $used_amc_deep_cleaning = Maintenance::where('product_sl_no',$serial)->where('service_type','deep_cleaning')->where('is_closed',1)->where('is_amc',1)->where('created_at','>=', $amc_start_date.' 01:01:01')->count();
	return $used_amc_deep_cleaning;
}

function GetAmcDetails($serial) {
    $subscription = AmcSubscription::with('AmcData.AmcDurationData', 'AmcData.AmcPlanData')
        ->where('serial', $serial)
        ->first();

    if (!$subscription) return null;

    return [
        'amc_number' => $subscription->amc_unique_number,
        'amc_plan_name' => optional(optional($subscription->AmcData)->AmcPlanData)->name ?? "N/A",
        'amc_start_date' => date('d-m-Y', strtotime($subscription->amc_start_date)),
        'amc_end_date' => date('d-m-Y', strtotime($subscription->amc_end_date)),
        'warranty_status' => ($subscription->amc_end_date >= now()) ? "YES" : "NO",
        'amc_actual_normal_cleaning' => optional(optional($subscription->AmcData)->AmcDurationData)->normal_cleaning ?? 0,
        'amc_actual_deep_cleaning' => optional(optional($subscription->AmcData)->AmcDurationData)->deep_cleaning ?? 0,
        'amc_used_normal_cleaning' => GetAmcCleaningWarranty($serial),
        'amc_used_deep_cleaning' => GetAmcDeepCleaningWarranty($serial),
        'amc_remaining_normal_cleaning' => 
            (optional(optional($subscription->AmcData)->AmcDurationData)->normal_cleaning ?? 0) - GetAmcCleaningWarranty($serial),
        'amc_remaining_deep_cleaning' => 
            (optional(optional($subscription->AmcData)->AmcDurationData)->deep_cleaning ?? 0) - GetAmcDeepCleaningWarranty($serial)
    ];
}
    

function get_spare_warranty($crp_id, $sp_id){      //pending for additional warranty
    $crp_data = \App\Models\CustomerPointService::find($crp_id);
    $product_id =$crp_data->product_id;
    $warranty_status = 0;
    $data = ['purchase_date'=>$crp_data->bill_date,'booking_date'=>$crp_data->entry_date, 'warranty_end_date'=>'','warranty_status'=>0];
   
    $get_comprehensive_warranty = \App\Models\ProductWarranty::where('goods_id',$product_id)->where('dealer_type',$crp_data->dealer_type)->where('warranty_type', 'comprehensive')->first();
   
    if($get_comprehensive_warranty){
        // For Part Chargeable
        $get_additional_part_chargeable__warranty = \App\Models\ProductWarranty::where('goods_id',$product_id)->where('dealer_type',$crp_data->dealer_type)->where('warranty_type', 'additional')->where('additional_warranty_type', 1)->first();

        $part_chargeable__warranty = $get_additional_part_chargeable__warranty?$get_additional_part_chargeable__warranty->warranty_period:0;
        
        $warranty_period = $get_comprehensive_warranty->warranty_period+$part_chargeable__warranty;

         // For Service Chargeable
        $get_additional_service_chargeable_warranty = \App\Models\ProductWarranty::where('goods_id',$product_id)->where('dealer_type',$crp_data->dealer_type)->where('warranty_type', 'additional')->where('additional_warranty_type', 2)->first();

        $service_chargeable_warranty = $get_additional_service_chargeable_warranty?$get_additional_service_chargeable_warranty->warranty_period:0;
        $service_chargeable_warranty_period = $get_comprehensive_warranty->warranty_period+$service_chargeable_warranty;
        
        // dd($service_chargeable_warranty_period);

        $warranty_end_date = date('Y-m-d', strtotime($crp_data->bill_date. ' + '.$warranty_period.' months'));
        $warranty_end_date = date('Y-m-d', strtotime($warranty_end_date . ' -1 days'));

        // For Service Chargeable warranty
        $service_chargeable_warranty_end_date = date('Y-m-d', strtotime($crp_data->bill_date. ' + '.$service_chargeable_warranty_period.' months'));
        $service_chargeable_warranty_end_date = date('Y-m-d', strtotime($service_chargeable_warranty_end_date . ' -1 days'));


        
        if ($crp_data->entry_date < $warranty_end_date) {
            $warranty_status = 1; //Yes;
            $data['purchase_date'] = $crp_data->bill_date;
            $data['booking_date'] = $crp_data->entry_date;
            $data['warranty_end_date'] = $warranty_end_date;
            $data['warranty_status'] = $warranty_status;
            $data['warranty_preiod'] = $warranty_period;
            $data['service_chargeable_warranty'] = 1;//Yes
        }else{
            $get_parts_warranty = \App\Models\ProductWarranty::where('goods_id',$product_id)->where('dealer_type',$crp_data->dealer_type)->where('warranty_type', 'parts')->where('spear_id', $sp_id)->first();
            if($get_parts_warranty){
                $part_warranty_end_date = date('Y-m-d', strtotime($crp_data->bill_date. ' + '.$get_parts_warranty->warranty_period .' months'));
                $part_warranty_end_date = date('Y-m-d', strtotime($part_warranty_end_date . ' -1 days'));
                if ($crp_data->entry_date < $part_warranty_end_date) {
                    $warranty_status = 1; //Yes;
                    $data['purchase_date'] = $crp_data->bill_date;
                    $data['booking_date'] = $crp_data->entry_date;
                    $data['warranty_end_date'] = $part_warranty_end_date;
                    $data['warranty_status'] = $warranty_status;
                    $data['service_chargeable_warranty'] = 0;//No
                    $data['warranty_preiod'] = $get_parts_warranty->warranty_period;
                }
            }
        }
        if ($crp_data->entry_date < $service_chargeable_warranty_end_date) {
            $data['service_chargeable_warranty'] = 1;//Yes
        }
    }else{
        $get_parts_warranty = \App\Models\ProductWarranty::where('goods_id',$product_id)->where('dealer_type',$crp_data->dealer_type)->where('warranty_type', 'parts')->where('spear_id', $sp_id)->first();
            if($get_parts_warranty){
                $part_warranty_end_date = date('Y-m-d', strtotime($crp_data->bill_date. ' + '.$get_parts_warranty->warranty_period .' months'));
                $part_warranty_end_date = date('Y-m-d', strtotime($part_warranty_end_date . ' -1 days'));
                if ($crp_data->entry_date < $part_warranty_end_date) {
                    $warranty_status = 1; //Yes;
                    $data['purchase_date'] = $crp_data->bill_date;
                    $data['booking_date'] = $crp_data->entry_date;
                    $data['warranty_end_date'] = $part_warranty_end_date;
                    $data['warranty_status'] = $warranty_status;
                    $data['service_chargeable_warranty'] = 0;//No
                    $data['warranty_preiod'] = $get_parts_warranty->warranty_period;
                }
            }
    }
    return $data;
}


function get_dap_spare_warranty($dap_id, $sp_id){   //pending for additional warranty
    $crp_data = \App\Models\DapService::find($dap_id);
    $product_id =$crp_data->product_id;
    $warranty_status = 0;
    $crp_data->dealer_type = "khosla";
    
    $data = ['purchase_date'=>$crp_data->bill_date,'booking_date'=>$crp_data->entry_date, 'warranty_end_date'=>'','warranty_status'=>0];
    $get_comprehensive_warranty = \App\Models\ProductWarranty::where('goods_id',$product_id)->where('dealer_type',$crp_data->dealer_type)->where('warranty_type', 'comprehensive')->first();
    if($get_comprehensive_warranty){
        $get_additional_warranty = \App\Models\ProductWarranty::where('goods_id',$product_id)->where('dealer_type',$crp_data->dealer_type)->where('warranty_type', 'additional')->where('additional_warranty_type', 1)->first();
        $additional_warranty_period = $get_additional_warranty ? $get_additional_warranty->warranty_period : 0;
        $warranty_period = $get_comprehensive_warranty->warranty_period+$additional_warranty_period;
        $warranty_end_date = date('Y-m-d', strtotime($crp_data->bill_date. ' + '.$warranty_period.' months'));
        $warranty_end_date = date('Y-m-d', strtotime($warranty_end_date . ' -1 days'));
        if ($crp_data->entry_date < $warranty_end_date) {
            $warranty_status = 1; //Yes;
            $data['purchase_date'] = $crp_data->bill_date;
            $data['booking_date'] = $crp_data->entry_date;
            $data['warranty_end_date'] = $warranty_end_date;
            $data['warranty_status'] = $warranty_status;
            $data['warranty_preiod'] = $warranty_period;
        }else{
            $get_parts_warranty = \App\Models\ProductWarranty::where('goods_id',$product_id)->where('dealer_type',$crp_data->dealer_type)->where('warranty_type', 'parts')->where('spear_id', $sp_id)->first();
            if($get_parts_warranty){
                $part_warranty_end_date = date('Y-m-d', strtotime($crp_data->bill_date. ' + '.$get_parts_warranty->warranty_period .' months'));
                $part_warranty_end_date = date('Y-m-d', strtotime($part_warranty_end_date . ' -1 days'));
                if ($crp_data->entry_date < $part_warranty_end_date) {
                    $warranty_status = 1; //Yes;
                    $data['purchase_date'] = $crp_data->bill_date;
                    $data['booking_date'] = $crp_data->entry_date;
                    $data['warranty_end_date'] = $part_warranty_end_date;
                    $data['warranty_status'] = $warranty_status;
                    $data['warranty_preiod'] = $get_parts_warranty->warranty_period;
                }
            }
        }
    }else{
        $get_parts_warranty = \App\Models\ProductWarranty::where('goods_id',$product_id)->where('dealer_type',$crp_data->dealer_type)->where('warranty_type', 'parts')->where('spear_id', $sp_id)->first();
            if($get_parts_warranty){
                $part_warranty_end_date = date('Y-m-d', strtotime($crp_data->bill_date. ' + '.$get_parts_warranty->warranty_period .' months'));
                $part_warranty_end_date = date('Y-m-d', strtotime($part_warranty_end_date . ' -1 days'));
                if ($crp_data->entry_date < $part_warranty_end_date) {
                    $warranty_status = 1; //Yes;
                    $data['purchase_date'] = $crp_data->bill_date;
                    $data['booking_date'] = $crp_data->entry_date;
                    $data['warranty_end_date'] = $part_warranty_end_date;
                    $data['warranty_status'] = $warranty_status;
                    $data['warranty_preiod'] = $get_parts_warranty->warranty_period;
                }
            }
    }
    return $data;
   
}

// function getComprehensiveWarranty($toDate,$remaining_days){
//     $getMaxComMonth = ProductWarranty::where('warranty_type', 'comprehensive')
//     ->where('dealer_type', 'khosla')
//     ->max('warranty_period');
//     $getMaxComMonth = $getMaxComMonth?$getMaxComMonth:12;
//    // Calculate the start date for the 48-month range
//    $startDate = \Carbon\Carbon::parse($toDate)->subMonths(12);

//    // Query KgaSalesData with bill_date between startDate and toDate
//    $data = KgaSalesData::select('product_id', 'bill_date')
//        ->whereBetween('bill_date', [$startDate, $toDate])
//        ->get()
//        ->toArray();
//        dd($data);
// }

function getAmcUniqueNumber(){
    $val = 1;    
    $year = now()->year;
    $month = now()->format('m');
    $data = DB::table('before_amc_subscription')->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = '".$year."-".$month."'  ")->count();
    if(!empty($data)){
        $val = ($data + 1);
    }
    // $number = str_pad($val,$length,"0",STR_PAD_LEFT);
    do {
        $number = str_pad($val, 6, "0", STR_PAD_LEFT);
        $unique_id = 'AMC'.$year.$month.$number;
        $exists = DB::table('before_amc_subscription')->where('amc_unique_number', $unique_id)->exists();
        $val++;
    } while ($exists);
    
    return $unique_id;

}

/* function generateAmcInvoiceId()   //for AMC INVOICE
    {
        // Get today's date in the format yyyy/mm/dd
        $up_year = date('y') + 1;
        $current_year = date('y');
        $current_month = date('m');
        $today = 'AMC'.$current_year.$up_year.'/'.$current_month;
        // Find the latest invoice for today
        $lastInvoice = AmcServicePayment::whereDate('created_at', Carbon::today())
            ->orderBy('invoice_id', 'desc')
            ->first();
        // Get the next sequence number
        if ($lastInvoice) {
            // Extract the last part of the invoice ID (sequence number)
            $lastInvoiceNumber = (int)substr($lastInvoice->invoice_id, -7);
            // Increment the sequence number
            $newInvoiceNumber = $lastInvoiceNumber + 1;
        } else {
            // Start from 1 if no invoice exists for today
            $newInvoiceNumber = 1;
        }   
        // Format the invoice ID as yyyy/mm/dd/01
        return $today . '/' . str_pad($newInvoiceNumber, 7, '0', STR_PAD_LEFT);
    } */

  function generateAmcInvoiceId() // for AMC INVOICE
{
    return DB::transaction(function () {
        $current_year = date('y');
        $up_year = $current_year + 1;
        $current_month = date('m');
        $todayPrefix = 'AMC' . $current_year . $up_year . '/' . $current_month;

        // Lock the relevant rows to avoid race condition
        $lastInvoice = AmcServicePayment::where('invoice_id', 'like', $todayPrefix . '/%')
            ->lockForUpdate()
            ->orderBy('invoice_id', 'desc')
            ->first();

        if ($lastInvoice) {
            // Extract last sequence
            $lastNumber = (int)substr($lastInvoice->invoice_id, -7);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $todayPrefix . '/' . str_pad($newNumber, 7, '0', STR_PAD_LEFT);
    });
}


function generateInvoiceId()  //for CRP INVOICE
    {
        // Get today's date in the format yyyy/mm/dd
        $up_year = date('y') + 1;
        $current_year = date('y');
        $current_month = date('m');
        $today = 'CRP'.$current_year.$up_year.'/'.$current_month;
        // Find the latest invoice for today
        $lastInvoice = CrpServicePayment::whereDate('created_at', Carbon::today())
            ->orderBy('invoice_id', 'desc')
            ->first();
        // Get the next sequence number
        if ($lastInvoice) {
            // Extract the last part of the invoice ID (sequence number)
            $lastInvoiceNumber = (int)substr($lastInvoice->invoice_id, -7);
            // Increment the sequence number
            $newInvoiceNumber = $lastInvoiceNumber + 1;
        } else {
            // Start from 1 if no invoice exists for today
            $newInvoiceNumber = 1;
        }   
        // Format the invoice ID as yyyy/mm/dd/01
        return $today . '/' . str_pad($newInvoiceNumber, 7, '0', STR_PAD_LEFT);
    }

function generateDapInvoiceId()  //for DAP INVOICE
    {
        // Get today's date in the format yyyy/mm/dd
        $up_year = date('y') + 1;
        $current_year = date('y');
        $current_month = date('m');
        $today = 'DAP'.$current_year.$up_year.'/'.$current_month;
        // Find the latest invoice for today
        $lastInvoice = DapServicePayment::whereDate('created_at', Carbon::today())
            ->orderBy('invoice_id', 'desc')
            ->first();
        // Get the next sequence number
        if ($lastInvoice) {
            // Extract the last part of the invoice ID (sequence number)
            $lastInvoiceNumber = (int)substr($lastInvoice->invoice_id, -7);
            // Increment the sequence number
            $newInvoiceNumber = $lastInvoiceNumber + 1;
        } else {
            // Start from 1 if no invoice exists for today
            $newInvoiceNumber = 1;
        }   
        // Format the invoice ID as yyyy/mm/dd/01
        return $today . '/' . str_pad($newInvoiceNumber, 7, '0', STR_PAD_LEFT);
    }
// number to word
function amountInWords(float $number)
{
    $decimal = round($number - ($no = floor($number)), 2) * 100;
    $hundred = null;
    $digits_length = strlen($no);
    $i = 0;
    $str = array();
    $words = array(0 => '', 1 => 'one', 2 => 'two',
        3 => 'three', 4 => 'four', 5 => 'five', 6 => 'six',
        7 => 'seven', 8 => 'eight', 9 => 'nine',
        10 => 'ten', 11 => 'eleven', 12 => 'twelve',
        13 => 'thirteen', 14 => 'fourteen', 15 => 'fifteen',
        16 => 'sixteen', 17 => 'seventeen', 18 => 'eighteen',
        19 => 'nineteen', 20 => 'twenty', 30 => 'thirty',
        40 => 'forty', 50 => 'fifty', 60 => 'sixty',
        70 => 'seventy', 80 => 'eighty', 90 => 'ninety');
    $digits = array('', 'hundred','thousand','lakh', 'crore');
    while( $i < $digits_length ) {
        $divider = ($i == 2) ? 10 : 100;
        $number = floor($no % $divider);
        $no = floor($no / $divider);
        $i += $divider == 10 ? 1 : 2;
        if ($number) {
            $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
            $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
            $str [] = ($number < 21) ? $words[$number].' '. $digits[$counter]. $plural.' '.$hundred:$words[floor($number / 10) * 10].' '.$words[$number % 10]. ' '.$digits[$counter].$plural.' '.$hundred;
        } else $str[] = null;
    }
    $Rupees = implode('', array_reverse($str));
    $paise = ($decimal > 0) ? "." . ($words[$decimal / 10] . " " . $words[$decimal % 10]) . ' Paise' : '';
    return ($Rupees ? $Rupees . 'Rupees ' : '') . $paise;
}
function sendAMCPaymentLink($mobile, $amount, $UrlParams, $customer_name = '')
{
    $apiDomainUrl  = config('whatsapp.api_domain_url');
    $apiVersion    = config('whatsapp.api_version');
    $channelNumber = config('whatsapp.channel_number');
    $apiKey        = config('whatsapp.api_key');


    // Format mobile
    $recipientPhone = "91" . $mobile;

    // Format amount
    $amountText = number_format($amount, 2);

    /**
     *  WhatsApp Template: send_amc_payment_link
     *  {{1}} = Amount
     *  {{2}} = Payment URL (display inside message body)
     *  Button → URL (parameter: {{url}})
     */

    $data = [
        "messaging_product" => "whatsapp",
        "recipient_type"    => "individual",
        "to"                => $recipientPhone,
        "type"              => "template",
        "template" => [
            "name" => "send_amc_payment_link",
            "language" => ["code" => "en"],
            "components" => [
                [
                    "type" => "body",
                    "parameters" => [
                        ["type" => "text", "text" => $customer_name],    // {{1}}
                        ["type" => "text", "text" => $amountText]     // {{2}}
                    ]
                ],
                [
                    "type" => "button",
                    "sub_type" => "url",
                    "index" => "0",
                    "parameters" => [
                        ["type" => "text", "text" => $UrlParams]     // {{url}}
                    ]
                ]
            ]
        ]
    ];

    $apiUrl = "$apiDomainUrl/$apiVersion/$channelNumber/messages";

    // CURL CALL
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $apiUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer $apiKey",
            "Content-Type: application/json"
        ],
        CURLOPT_POSTFIELDS => json_encode($data),
    ]);
    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    // Log to DB
    DB::table('whatsapp_send_responses')->insert([
        'mobile'        => $mobile,
        'template_name' => 'send_amc_payment_link',
        'request_json'  => json_encode($data),
        'response_json' => $response,
        'error_message' => $error,
        'created_at'    => now(),
        'updated_at'    => now(),
    ]);

    return [
        "response" => $response,
        "error"    => $error
    ];
}
