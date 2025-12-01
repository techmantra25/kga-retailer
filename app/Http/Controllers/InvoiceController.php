<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
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
        $partner_type = !empty($request->partner_type)?$request->partner_type:'';
        $product_name = !empty($request->product_name)?$request->product_name:'';
        $product_id = !empty($request->product_id)?$request->product_id:'';
        $paginate = 10;
        $data = Invoice::select('*');
        $totalResult = Invoice::select('*');
        if(!empty($search)){
            $data = $data->where('invoice_no', 'LIKE', '%'.$search.'%')->orWhereHas('sales_order',function($order) use ($search){
                $order->where('order_no', 'LIKE', '%'.$search.'%');
            })->orWhereHas('packingslip',function($packingslip) use ($search){
                $packingslip->where('slipno', 'LIKE', '%'.$search.'%');
            });

            $totalResult = $totalResult->where('invoice_no', 'LIKE', '%'.$search.'%')->orWhereHas('sales_order',function($order) use ($search){
                $order->where('order_no', 'LIKE', '%'.$search.'%');
            })->orWhereHas('packingslip',function($packingslip) use ($search){
                $packingslip->where('slipno', 'LIKE', '%'.$search.'%');
            });
        }

        if(!empty($type)){
            $data = $data->where(function($q_type) use ($type) {
                $q_type->orWhereHas('sales_order', function($q_type) use ($type){
                    $q_type->where('type', '=', $type);
                });
            });
            $totalResult = $totalResult->where(function($q_type) use ($type) {
                $q_type->orWhereHas('sales_order', function($q_type) use ($type){
                    $q_type->where('type', '=', $type);
                });
            });
        }
        if(!empty($partner_type)){
            if($partner_type == 'dealer'){
                $data = $data->whereNotNull('dealer_id');
                $totalResult = $totalResult->whereNotNull('dealer_id');
            }else if($partner_type == 'service_partner'){
                $data = $data->whereNotNull('service_partner_id');
                $totalResult = $totalResult->whereNotNull('service_partner_id');
            }
        }

        if(!empty($product_id)){
            $data = $data->whereHas('items', function($items) use($product_id){
                $items->where('product_id', $product_id);
            });
            $totalResult = $totalResult->whereHas('items', function($items) use($product_id){
                $items->where('product_id', $product_id);
            });
        }

        $data = $data->orderBy('id','desc')->paginate($paginate);
        $totalResult = $totalResult->count();

        $data = $data->appends([
            'search'=>$search,
            'page'=>$request->page,
            'type'=>$type,
            'product_name'=>$product_name,
            'product_id'=>$product_id
        ]);
        return view('invoice.list', compact('data','totalResult','search','type','paginate','product_name','product_id','partner_type'));
    }

    public function download($idStr)
    {
        # code...
        try {
            $id = Crypt::decrypt($idStr);
            $invoice = Invoice::find($id);
            $invoice_items = InvoiceItem::where('invoice_id',$id)->get();
            $invoice->invoice_items = $invoice_items;

            $invoicepdfname = "".$invoice->invoice_no."";
            $pdf = Pdf::loadView('invoice.download_2', compact('invoice','id'));
            return $pdf->download($invoicepdfname.'.pdf');

            // dd($invoice);
            // return view('invoice.download', compact('invoice','id'));
        } catch ( DecryptException $e) {
            return abort(404);
        }
    }
}
