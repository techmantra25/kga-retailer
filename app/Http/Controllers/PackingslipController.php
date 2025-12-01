<?php

namespace App\Http\Controllers;

use App\Models\Packingslip;
use App\Models\PackingslipBarcode;
use App\Models\PackingslipProduct;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\StockBarcode;
use App\Models\CustomerPointService;
use App\Models\SalesOrder;
use App\Models\Ledger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Barryvdh\DomPDF\Facade\Pdf;

class PackingslipController extends Controller
{
    public function __construct(Request $request)
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            
            if(Auth::user()->id == 8){
                abort(404);
            }            

            return $next($request);
        });
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = !empty($request->search)?$request->search:'';
        $type = !empty($request->type)?$request->type:'';
        $product_name = !empty($request->product_name)?$request->product_name:'';
        $product_id = !empty($request->product_id)?$request->product_id:'';
        $paginate = 10;
        $data = Packingslip::select('*')->with([
            'sales_order' => function($q){
                $q->select('id','order_no','created_at','order_amount','dealer_id','service_partner_id','type')->with('dealer:id,name,email,phone')->with('service_partner:id,person_name,company_name,email,phone');
            }
        ]);
        $totalResult = Packingslip::select('*');

        if(!empty($search)){
            $data = $data->where(function($query) use ($search){
                $query->where('slipno', 'LIKE', '%'.$search.'%')->orWhere('details', 'LIKE', '%'.$search.'%')->orWhereHas('sales_order', function ($q) use ($search) {
                    $q->where('order_no', 'LIKE','%'.$search.'%');
                });
            });
            $totalResult = $totalResult->where(function($query) use ($search){
                $query->where('slipno', 'LIKE', '%'.$search.'%')->orWhere('details', 'LIKE', '%'.$search.'%')->orWhereHas('sales_order', function ($q) use ($search) {
                    $q->where('order_no', 'LIKE','%'.$search.'%');
                });
            });
        }

        if(!empty($type)){
            $data = $data->whereHas('sales_order', function($querytype) use ($type){
                $querytype->where('type', $type);
            });
            $totalResult = $totalResult->whereHas('sales_order', function($querytype) use ($type){
                $querytype->where('type', $type);
            });
        }

        if(!empty($product_id)){
            $data = $data->whereHas('packingslip_products', function($ps) use($product_id){
                $ps->where('product_id', $product_id);
            });
            $totalResult = $totalResult->whereHas('packingslip_products', function($ps) use($product_id){
                $ps->where('product_id', $product_id);
            });
        }

        $data = $data->orderBy('id','desc')->paginate($paginate);
        $totalResult = $totalResult->count();

        $data = $data->appends([
            'search'=>$search,
            'page'=>$request->page,
            'type' => $type,
            'product_name' => $product_name,
            'product_id' => $product_id
        ]);
        return view('packingslip.list', compact('data','totalResult','search','type','paginate','product_name','product_id'));
    }

    public function show(Request $request,$idStr)
    {
        # code...

        try {
            $id = Crypt::decrypt($idStr);
            $data = Packingslip::find($id);
            // dd($data);
            return view('packingslip.detail', compact('id','idStr','data'));
        } catch (DecryptException $e) {
            return abort(404);            
        }
    }

    public function download($idStr)
    {
        # download...
        try {
            $id = Crypt::decrypt($idStr);
            $packingslips = Packingslip::find($id); 
            // dd($packingslips);
            $sales_orders = SalesOrder::find($packingslips->sales_order_id);
            $customer = Customer::find($sales_orders->customer_id);

            $pspdfname = "".$packingslips->slipno."";
            $pdf = Pdf::loadView('packingslip.download', compact('id','idStr','packingslips','customer'));
            return $pdf->download($pspdfname.'.pdf');

            // return view('packingslip.download', compact('id','idStr','packingslips','customer'));
        } catch ( DecryptException $e) {
            return abort(404);
        }
    }

    /*
    ** Raise Invoice Preview
    */
    public function raise_invoice(Request $request,$packingslip_idStr,$getQueryString='')
    {
        try {
            $packingslip_id = Crypt::decrypt($packingslip_idStr);
            $packingslip = Packingslip::find($packingslip_id);
            if(empty($packingslip->invoice_no)){
                $data = PackingslipProduct::where('packingslip_id',$packingslip_id)->get();
                $customer = Customer::find($packingslip->sales_order->customer_id);
                return view('packingslip.raise-invoice', compact('packingslip','packingslip_id','packingslip_idStr','data','customer'));
            } else {
                Session::flash('message', 'Invoice exists already');
                return redirect()->route('packingslip.list');
            }            
        } catch ( DecryptException $e) {
            return abort(404);
        }
    }

    /*
    ** Save Invoice 
    */
    public function save_invoice(Request $request)
    {
        # save invoice...
        
        $params = $request->except('_token');
        // dd($params);
        $invoice_no = 'KGA'.genAutoIncreNoYearWiseOrder(4,'invoices',date('Y'),date('m'));
        
        $items = $params['items'];
        $invoiceData = array(
            'invoice_no' => $invoice_no,
            'sales_order_id' => $params['sales_order_id'],
            'dealer_id' => $params['dealer_id'],
            'service_partner_id' => $params['service_partner_id'],
            'packingslip_id' => $params['packingslip_id'],
            'total_amount' => $params['total_amount'],
            // 'customer_details' => json_encode($params['customer_details']),
            'item_details' => json_encode($items),
            'created_at' => date('Y-m-d H:i:s')
        );
        $invoice_id = Invoice::insertGetId($invoiceData);
        
        foreach($items as $item){
            $invoiceitemData = array(
                'invoice_id' => $invoice_id,
                'product_id' => $item['product_id'],
                'product_title' => $item['product_title'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'total_price' => $item['total_price'],
                'price_exc_tax' => $item['price_exc_tax'],
                'total_price_exc_tax' => $item['total_price_exc_tax'],
                'tax' => $item['tax'],
                'hsn_code' => $item['hsn_code'],
                'created_at' => date('Y-m-d H:i:s')
            );
            InvoiceItem::insert($invoiceitemData);
        }

        Packingslip::where('id', $params['packingslip_id'])->update(['invoice_no'=>$invoice_no]);

        /* Ledger Entry */
        if(!empty($params['dealer_id'])){
            $this->ledgerEntry('dealer',$params['dealer_id'],$params['total_amount'],$invoice_no);
        }
        if(!empty($params['service_partner_id'])){
            $this->ledgerEntry('servicepartner',$params['service_partner_id'],$params['total_amount'],$invoice_no);
        }
        $sales_orders = SalesOrder::find($params['sales_order_id']);
        if($sales_orders){
            $dap_data = CustomerPointService::where('id', $sales_orders->crp_id)->first();
            if($dap_data){
                $dap_data->service_partner_invoice = $invoice_no;
                $dap_data->status = 3;//Generate Invoice
                $dap_data->save();
            }
        }
        
        Session::flash('message', 'Invoice Generated Successfully');
        if($sales_orders->crp_id){
            return redirect()->route('customer-point-repair.list-booking');
        }else{
            return redirect()->route('invoice.list');
        }
        
    }

    /* 
    ** View Goods Scan Out  
    */
    public function goods_scan_out(Request $request,$packingslip_idStr,$getQueryString='')
    {
        // return abort(404);
        # view goods scan out...
        try {
            $search = !empty($request->search)?$request->search:'';
            $packingslip_id = Crypt::decrypt($packingslip_idStr);
            $packingslip = Packingslip::find($packingslip_id);
            $proIds = array();
            $packingslip_products = PackingslipProduct::where('packingslip_id', $packingslip_id)->get();
            foreach($packingslip_products as $product){
                $proIds[] = $product->product_id;
            }
            $data = StockBarcode::with('product')->where('is_stock_out', 0)->whereIn('product_id', $proIds);        
            if(!empty($search)){
                $data = $data->where(function($q) use ($search){
                    $q->where('barcode_no','LIKE', '%'.$search.'%')->orWhereHas('product', function ($product) use ($search) {
                        $product->where('title', 'LIKE','%'.$search.'%');
                    });
                });
            }
            $data = $data->get()->sortBy('product_id')->groupBy('product.id');
            $total_products = PackingslipProduct::where('packingslip_id', $packingslip_id)->sum('quantity');
            // dd($total_products);
            return view('packingslip.goods-out', compact('packingslip_id','packingslip_idStr','packingslip','getQueryString','search','data','total_products'));
        } catch ( DecryptException $e) {
            return abort(404);
        }
    }

    public function save_scan_out(Request $request,$id)
    {
        # save scan out...
        // return abort(503);
        // dd($request->all());
        $params = $request->except('_token');
        $barcodes = $request->barcodes;
        foreach($barcodes as $barcode_no){    
            
            $stock_barcodes = StockBarcode::where('barcode_no',$barcode_no)->first();
            $product_id = $stock_barcodes->product_id;
            $code_html = $stock_barcodes->code_html;
            $code_base64_img = $stock_barcodes->code_base64_img;

            $packingslip_barcode_arr = array(
                'packingslip_id' => $id,
                'product_id' => $product_id,
                'barcode_no' => $barcode_no,
                'code_html' => $code_html,
                'code_base64_img' => $code_base64_img,
                'created_at' => date('Y-m-d H:i:s')
            );
            // dd($packingslip_barcode_arr);            
            PackingslipBarcode::insert($packingslip_barcode_arr);
            StockBarcode::where('barcode_no',$barcode_no)->update([
                'is_stock_out' => 1
            ]);
        }
        PackingSlip::where('id',$id)->update(['is_goods_out' => 1]);
        Session::flash('message', 'All scanned goods out successfully');
        return redirect()->route('packingslip.list');
        // dd($barcodes);
        
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
        } else if ($user_type == 'servicepartner'){
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

    /*
    ** View Barcode
    */

    public function barcodes(Request $request,$idStr)
    {
        # View Barcode ...
        try{
            $search = !empty($request->search)?$request->search:'';
            $id = Crypt::decrypt($idStr);
            $data = PackingslipBarcode::with('product')->where('packingslip_id',$id);
            $totalData = PackingslipBarcode::with('product')->where('packingslip_id',$id);
            
            if(!empty($search)){
                $data = $data->where(function($q) use ($search){
                    $q->where('barcode_no','LIKE', '%'.$search.'%')->orWhereHas('product', function ($product) use ($search) {
                        $product->where('title', 'LIKE','%'.$search.'%');
                    });
                });
                $totalData = $totalData->where(function($q) use ($search){
                    $q->where('barcode_no','LIKE', '%'.$search.'%')->orWhereHas('product', function ($product) use ($search) {
                        $product->where('title', 'LIKE','%'.$search.'%');
                    });
                });
            }
            
            $data = $data->get();
            $totalData = $totalData->count();
            $ps = Packingslip::find($id);
            
            $slipno = $ps->slipno;
            return view('packingslip.barcodes', compact('data','totalData','id','slipno','search'));
        } catch (DecryptException $e) {
            return abort(404);
        }
        
    }

    /*
    ** Download Barcodes
    */

    public function barcode_csv(Request $request,$idStr)
    {
        # Download Barcodes ...
        try{
            $search = !empty($request->search)?$request->search:'';
            $id = Crypt::decrypt($idStr);
            $data = StockBarcode::where('packingslip_id',$id);
            
            if(!empty($search)){
                $data = $data->where(function($q) use ($search){
                    $q->where('barcode_no','LIKE', '%'.$search.'%')->orWhereHas('product', function ($product) use ($search) {
                        $product->where('title', 'LIKE','%'.$search.'%');
                    });
                });
            }

            $data = $data->pluck('barcode_no')->toArray();
            // dd($data);
            $ps = Packingslip::find($id);
            $slipno = $ps->slipno;

            $fileName = "Barcodes-Purchase-Order-".$slipno.".csv";
            // dd($data);
            $headers = array(
                "Content-type"        => "text/csv",
                "Content-Disposition" => "attachment; filename=$fileName",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            );

            $columns = array('Barcodes');

            $callback = function() use($data, $columns) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);
                $net_value = 0;
                
                foreach ($data as $key => $value) {                    
                    $row['Barcodes'] = $value;

                    fputcsv($file, array($row['Barcodes']));                
                }
                fclose($file);
            };
            return response()->stream($callback, 200, $headers);

            
        } catch (DecryptException $e) {
            return abort(404);
        }
        
    }
    
}
