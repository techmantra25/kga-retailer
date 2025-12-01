<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Customer;
use App\Models\Dealer;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\ServicePartner;
use App\Models\Settings;
use App\Models\PurchaseOrder;
use App\Models\SalesOrder;
use App\Models\Invoice;
use App\Models\Stock;
use App\Models\Branch;
use App\Models\Changelog;
use App\Models\KgaSalesData;
use App\User;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $countCustomers = Customer::count();
        $countDealers = Dealer::count();
        $countSuppliers = Supplier::count();
        $countServicePartners = ServicePartner::where('is_default', 0)->count();
        $countSpares = Product::where('type','sp')->count();
        $countGoods = Product::where('type','fg')->count();
        $countPO = PurchaseOrder::count();
        $countGRN = Stock::count();
        $countSales = SalesOrder::count();
        $countInvoice = Invoice::count();
        
        return view('home', compact('countCustomers','countDealers','countSuppliers','countServicePartners','countSpares','countGoods','countPO','countGRN','countSales','countInvoice'));
    }

    public function myprofile(Request $request)
    {
        return view('profile');
    }

    public function saveprofile(Request $request)
    {
        # code...
        $request->validate([
            'name' => 'required|max:100'
        ]);

        $params = $request->except('_token');
        User::where('id', Auth::user()->id)->update($params);

        Session::flash('message', 'Profile updated successfully');
        return redirect()->route('myprofile'); 
    }

    public function changepassword(Request $request)
    {
        return view('password');
    }

    public function savepassword(Request $request)
    {
        # code...

        $request->validate([
            'password' => 'min:6|required_with:password_confirmation|same:password_confirmation',
            'password_confirmation' => 'min:6'
        ]);

        $params = $request->except('_token');
        
        if(!empty($params['password'])){
            $params['password'] = Hash::make($params['password']);            
        } else {
            unset($params['password']);
        }
        
        unset($params['password_confirmation']);
        // dd($params);
        $data = User::where('id', Auth::user()->id)->update($params);
        Session::flash('message', 'Password changed successfully');
        return redirect()->route('changepassword'); 
    }

    public function settings(Request $request)
    {
        # view settings...
        $settings = Settings::find(1);
        return view('settings', compact('settings'));
    }

    public function savesettings(Request $request)
    {
        # save settings...
        $params = $request->except('_token');
        
        Settings::where('id',1)->update($params);

        ServicePartner::where('id', 1)->update([
            'email' => $params['csv_to_email']
        ]);

        Session::flash('message', 'Settings saved successfully');
        return redirect()->route('settings'); 
    }

    public function kga_daily_stock(Request $request)
    {

        $paginate = !empty($request->paginate)?$request->paginate:25;
        $stock_date = !empty($request->stock_date)?$request->stock_date:date('Y-m-d',strtotime("-1 day"));
        $branch_id = !empty($request->branch_id)?$request->branch_id:'';
        $branch_name = !empty($request->branch_name)?$request->branch_name:'';

        $product_id = !empty($request->product_id)?$request->product_id:'';
        $product_name = !empty($request->product_name)?$request->product_name:'';

        $data = DB::table('kga_stock_data')->select('*');
        $totalResult = DB::table('kga_stock_data');

        if(!empty($branch_id)){
            $data = $data->where('branch_id',$branch_id);
            $totalResult = $totalResult->where('branch_id',$branch_id);
        }

        if(!empty($product_id)){
            $data = $data->where('product_id',$product_id);
            $totalResult = $totalResult->where('product_id',$product_id);
        }

        $data = $data->where('stock_date',$stock_date)->paginate($paginate);
        $totalResult = $totalResult->where('stock_date',$stock_date)->count();

        $data = $data->appends([
            'stock_date' => $stock_date,
            'branch_id' => $branch_id,
            'branch_name' => $branch_name,
            'product_id' => $product_id,
            'product_name' => $product_name,
            'page' => $request->page,
            'paginate' => $paginate
        ]);

        return view('kga-daily-stock', compact('paginate','stock_date','branch_id','branch_name','product_id','product_name','data','totalResult'));
    }

    public function csv_daily_stock(Request $request)
    {
        $stock_date = !empty($request->stock_date)?$request->stock_date:date('Y-m-d',strtotime("-1 day"));
        
        $product_id = !empty($request->product_id)?$request->product_id:'';
        $product_name = !empty($request->product_name)?$request->product_name:'';

        $branch_id = !empty($request->branch_id)?$request->branch_id:'';
        $branch_name = !empty($request->branch_name)?$request->branch_name:'';

        $data = DB::table('kga_stock_data')->select('*');        
        if(!empty($product_id)){
            $data = $data->where('product_id',$product_id);
        }
        if(!empty($branch_id)){
            $data = $data->where('branch_id',$branch_id);
        }

        $data = $data->where('stock_date',$stock_date)->get();
        $myArr = array();
        if(!empty($data)){
            foreach($data as $item){
                $myArr[] = array(
                    'itemcode' => $item->itemcode,
                    'sitecode_info' => $item->sitecode_info,
                    'opening' => $item->opening,
                    'received' => $item->received,
                    'issued' => $item->issued,
                    'closing' => $item->closing,
                    'available' => $item->available,
                    'defective' => $item->defective,
                    'display' => $item->display,
                    'transit' => $item->transit,
                    'defective_transit' => $item->defective_transit
                ); 
            }
        }


        $fileName = date('Ymd', strtotime($stock_date))."-kgastock";
        if(!empty($product_id)){
            $fileName .= "-".urlencode($product_name)."";
        }
        if(!empty($branch_id)){
            $fileName .= "-".urlencode($branch_name)."";
        }
        
        $fileName .= ".csv";
        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $dateColumn = array('','Date:- '.date('j M Y, l', strtotime($stock_date)));
        $proColumn = array('',''.$product_name.'');
        $branchColumn = array('',''.$branch_name.'');
        $spaceColumn1 = array('','','','','','','','','','');
        $columns = array('#','Product','Showroom','Opening','Received','Issued','Closing','Available','Defective','Display','Transit','Defective Transit');
        
        $callback = function() use($myArr, $dateColumn,$proColumn,$branchColumn,$spaceColumn1,$columns) {
            $file = fopen('php://output', 'w');            
            fputcsv($file, $dateColumn);
            fputcsv($file, $proColumn);
            fputcsv($file, $branchColumn);
            fputcsv($file, $spaceColumn1);
            fputcsv($file, $columns);
            
            $i = 1;

            foreach ($myArr as $item) {     
                $row['#'] = $i;      
                // if(!empty($product_id)){
                //     unset($row['Product']);
                // } else {
                    $row['Product'] = $item['itemcode'];     
                // }

                // if(!empty($branch_id)){
                //     unset($row['Showroom']);
                // } else {
                    $row['Showroom'] = $item['sitecode_info']; 
                // }
                
                
                $row['Opening'] = $item['opening'];
                $row['Received'] = $item['received'];
                $row['Issued'] = $item['issued'];
                $row['Closing'] = $item['closing'];
                $row['Available'] = $item['available'];
                $row['Defective'] = $item['defective'];
                $row['Display'] = $item['display'];
                $row['Transit'] = $item['transit'];
                $row['Defective Transit'] = $item['defective_transit'];

                $fputcsvRowArr = array(
                    $row['#'],
                    $row['Product'],
                    $row['Showroom'],
                    $row['Opening'],
                    $row['Received'],
                    $row['Issued'],
                    $row['Closing'],
                    $row['Available'],
                    $row['Defective'],
                    $row['Display'],
                    $row['Transit'],
                    $row['Defective Transit']
                );
                
                fputcsv($file, $fputcsvRowArr );  
                $i++;              
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);

    }
	
	public function updateEmail(Request $request){
	   $request->validate([
        'id' => 'required|exists:kga_sales_data,id',
        'email' => 'required|email',
    ]);

		$data = KgaSalesData::find($request->id);
		$data->email = $request->email;
		$data->save();
		 return redirect()->back()->with('message', 'Email updated successfully.');
	}

    public function kga_daily_sales(Request $request)
    {

        $paginate = !empty($request->paginate)?$request->paginate:25;
        $bill_date = !empty($request->bill_date)?$request->bill_date:date('Y-m-d',strtotime("-1 day"));
        $branch_id = !empty($request->branch_id)?$request->branch_id:'';
        $branch_name = !empty($request->branch_name)?$request->branch_name:'';

        $product_id = !empty($request->product_id)?$request->product_id:'';
        $product_name = !empty($request->product_name)?$request->product_name:'';

        $data = DB::table('kga_sales_data')->select('*');
        $totalResult = DB::table('kga_sales_data');

        if(!empty($branch_id)){
            $data = $data->where('branch_id',$branch_id);
            $totalResult = $totalResult->where('branch_id',$branch_id);
        }

        if(!empty($product_id)){
            $data = $data->where('product_id',$product_id);
            $totalResult = $totalResult->where('product_id',$product_id);
        }

        $data = $data->where('bill_date',$bill_date)->paginate($paginate);
        $totalResult = $totalResult->where('bill_date',$bill_date)->count();

        // dd($data);

        $data = $data->appends([
            'bill_date' => $bill_date,
            'branch_id' => $branch_id,
            'branch_name' => $branch_name,
            'product_id' => $product_id,
            'product_name' => $product_name,
            'page' => $request->page,
            'paginate' => $paginate
        ]);

        return view('kga-daily-sales', compact('paginate','bill_date','branch_id','branch_name','product_id','product_name','data','totalResult'));
    }

    public function csv_daily_sales(Request $request)
    {

        $bill_date = !empty($request->bill_date)?$request->bill_date:date('Y-m-d',strtotime("-1 day"));
        
        $product_id = !empty($request->product_id)?$request->product_id:'';
        $product_name = !empty($request->product_name)?$request->product_name:'';

        $branch_id = !empty($request->branch_id)?$request->branch_id:'';
        $branch_name = !empty($request->branch_name)?$request->branch_name:'';

        $data = DB::table('kga_sales_data')->select('*');        
        if(!empty($product_id)){
            $data = $data->where('product_id',$product_id);
        }
        if(!empty($branch_id)){
            $data = $data->where('branch_id',$branch_id);
        }

        $data = $data->where('bill_date',$bill_date)->get();

        $fileName = date('Ymd', strtotime($bill_date))."-kgasales";
        if(!empty($product_id)){
            $fileName .= "-".urlencode($product_name)."";
        }
        if(!empty($branch_id)){
            $fileName .= "-".urlencode($branch_name)."";
        }
        $fileName .= ".csv";
        
        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $dateColumn = array('','Date:- '.date('j M Y, l', strtotime($bill_date)));
        $proColumn = !empty($product_id)?array('',$product_name):array('','');
        $branchColumn = !empty($branch_id)?array('',$branch_name):array('','');
        $spaceColumn1 = array('','','','','','','','','','');

        $columns = array('#','Bill No','Product','Class','Customer Name','Mobile / Phone','Address','Near Location','Barcode','Serial','Branch');
        
        $myArr = array();
        if(!empty($data)){
            foreach($data as $item){
                $myArr[] = array(
                    'product' => $item->item,
                    'bill_no' => $item->bill_no,
                    'class_name' => $item->class_name,
                    'customer_name' => $item->customer_name,
                    'address' => $item->address,
                    'near_location' => $item->near_location,
                    'pincode' => $item->pincode,
                    'mobile' => $item->mobile,
                    'phone' => $item->phone,
                    'barcode' => $item->barcode,
                    'serial' => $item->serial,
                    'branch' => $item->branch
                ); 
            }
        }

        $callback = function() use($myArr, $dateColumn,$proColumn,$branchColumn,$spaceColumn1,$columns) {
            $file = fopen('php://output', 'w');            
            fputcsv($file, $dateColumn);
            fputcsv($file, $proColumn);
            fputcsv($file, $branchColumn);
            fputcsv($file, $spaceColumn1);
            fputcsv($file, $columns);
            
            $i = 1;
            
            foreach ($myArr as $item) {                  
                $row['#'] = $i;             
                $row['Bill No'] = $item['bill_no'];
                $row['Product'] = $item['product'];
                $row['Class'] = $item['class_name'];
                $row['Customer Name'] = $item['customer_name'];
                $row['Mobile / Phone'] = $item['mobile'].' / '.$item['phone'];
                $row['Address'] = $item['address'];
                $row['Near Location'] = $item['near_location'];
                $row['Barcode'] = $item['barcode'];
                $row['Serial'] = $item['serial'];
                $row['Branch'] = $item['branch'];
            
            
                fputcsv($file, array(
                        $row['#'],
                        $row['Bill No'],  
                        $row['Product'],                                             
                        $row['Class'],
                        $row['Customer Name'],
                        $row['Mobile / Phone'],
                        $row['Address'],
                        $row['Near Location'],
                        $row['Barcode'],
                        $row['Serial'],
                        $row['Branch']
                    )
                );  
                
                
                $i++;              
            }
            
            
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);

    }


    public function export_csv_changelog(Request $request)
    {
        $log_date = !empty($request->log_date)?$request->log_date:date('Y-m-d');
        $data = Changelog::with('user:id,role_id,name,email')->where( DB::raw("(DATE_FORMAT(created_at,'%Y-%m-%d'))") , $log_date )->get()->toArray();

        // dd($data);
        $myArr = array();

        if(!empty($data)){
            foreach($data as $item){
                $myArr[] = array(
                    'user_name' => $item['user']['name'],
                    'user_email' => $item['user']['email'],
                    'action_type' => $item['action_type'],
                    'ip_address' => $item['ip_address'],
                    'browser_name' => $item['browser_name'],
                    'navigator_useragent' => $item['navigator_useragent'],
                    'created_at' => $item['created_at'],
                    'details' => $item['details']
                ); 
            }
        }


        $fileName = date('Y-m-d', strtotime($log_date))."-kgamaster-changelog.csv";
        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $dateColumn = array('','Log Date:- '.date('j M Y, l', strtotime($log_date)));
        
        $spaceColumn1 = array('','CSV Export Time:- '.date('j M Y, H:i A'),'','','','','','Note: PLEASE PASTE THE JSON DATA INTO JSON FORMATTER TO BEAUTIFY');
        $spaceColumn2 = array('','','','','','','','Suggested Json Beautifier Link: https://jsonformatter.org/');
        $columns = array('#','USER','ACTION','TIMESTAMP','IP ADDRESS','BROWSER','NAVIGATOR USER AGENT','RAW DATA PARAMETER');

        $callback = function() use($myArr, $dateColumn,$spaceColumn1,$spaceColumn2,$columns) {
            $file = fopen('php://output', 'w');            
            fputcsv($file, $dateColumn);
            fputcsv($file, $spaceColumn1);
            fputcsv($file, $spaceColumn2);
            fputcsv($file, $columns);
            
            
            $i=1;
            foreach ($myArr as $arr) {  
                $row['#'] = $i;              
                $row['USER'] = $arr['user_name'].' ('.$arr['user_email'].')';
                $row['ACTION'] = ucwords(str_replace("_"," ",$arr['action_type']));
                $row['TIMESTAMP'] = date('j M Y h:i:s A',strtotime($arr['created_at'])).' (IST)';
                $row['IP ADDRESS'] = $arr['ip_address'];
                $row['BROWSER'] = $arr['browser_name'];
                $row['NAVIGATOR USER AGENT'] = $arr['navigator_useragent'];
                $row['RAW DATA PARAMETER'] = $arr['details'];
                
                
                fputcsv($file, array(   
                        $row['#'],                     
                        $row['USER'],
                        $row['ACTION'],
                        $row['TIMESTAMP'],
                        $row['IP ADDRESS'],
                        $row['BROWSER'],
                        $row['NAVIGATOR USER AGENT'],
                        $row['RAW DATA PARAMETER']
                    )
                ); 
                $i++;               
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);

        // dd($data);
        
    }
}
