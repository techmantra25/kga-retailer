<?php
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Models\StockLog;
use App\Models\Changelog;

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

function genAutoIncreNoYearWise($length=5,$table='sales_orders',$year){
    $val = 1;    
    $data = DB::table($table)->whereRaw("DATE_FORMAT(created_at, '%Y') = '".$year."'")->count();

    if(!empty($data)){
        $val = ($data + 1);
    }

    $number = str_pad($val,$length,"0",STR_PAD_LEFT);
    
    return $year.''.$number;
}

function genAutoIncreNoYearWiseOrder($length=4,$table='sales_orders',$year,$month){
    # PO , GRN, SALES ORDER , RETURN ORDER
    $val = 1;    
    $data = DB::table($table)->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = '".$year."-".$month."'  ")->count();

    if(!empty($data)){
        $val = ($data + 1);
    }

    $number = str_pad($val,$length,"0",STR_PAD_LEFT);
    
    return $year.''.$month.''.$number;
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

function updateStockInvetory($product_id,$quantity,$type='in',$data_id,$data_type){
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

function checkServicePartnerProductCharge($type='',$service_partner_id,$product_id){
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
