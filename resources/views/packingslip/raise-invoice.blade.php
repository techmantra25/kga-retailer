@extends('layouts.app')
@section('content')
@section('page', 'Raise Invoice')
<section>
    <ul class="breadcrumb_menu">    
        <li>Order Management</li>    
        <li><a href="{{ route('sales-order.show',Crypt::encrypt($packingslip->sales_order_id)) }}">{{$packingslip->sales_order->order_no}}</a> </li>
        <li><a href="{{ route('packingslip.list') }}?search={{ $packingslip->slipno }}">{{ $packingslip->slipno }}</a></li>
        <li>Raise Invoice</li>
    </ul>
    <div class="search__filter">
        <div class="row align-items-center justify-content-between">
            <div class="col">
                
            </div>
            <div class="col-auto">
                             
            </div>
            <div class="col-auto">
                <form action="" id="searchForm">
                <div class="row g-3 align-items-center">                    
                    <div class="col-auto">
                       
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
    <div class="filter">
        <div class="row align-items-center justify-content-between">
            <div class="col">
                @if (Session::has('message'))
                <div class="alert alert-success" role="alert">
                    {{ Session::get('message') }}
                    {{ Session::forget('message') }}
                </div>
                @endif
            </div>
            
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <h6>Order Details</h6>
            <div class="card shadow-sm">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="form-group">
                            <p><span class="text-muted">Order No : {{$packingslip->sales_order->order_no}} </span> </p>
                        </div> 
                        <div class="form-group">
                            <p><span class="text-muted">Order Date : {{ date('d/m/Y', strtotime($packingslip->sales_order->created_at)) }} </span> </p>
                        </div> 
                    </div>
                </div>  
            </div>                                      
        </div>  
        <div class="col-sm-6">
            @if (!empty($packingslip->sales_order->dealer))
            <h5>Dealer Details</h5>
            <div class="card shadow-sm">
                <div class="card shadow-sm">
                    <div class="card-body">                        
                        <div class="form-group mb-3">
                            <p><span class="text-muted">Person Name : {{$packingslip->sales_order->dealer->name}} </span> </p>
                        </div> 
                        <div class="form-group mb-3">
                            <p><span class="text-muted">Email : {{$packingslip->sales_order->dealer->email}} </span> </p>
                        </div> 
                        <div class="form-group mb-3">
                            <p><span class="text-muted">Phone : {{$packingslip->sales_order->dealer->phone}} </span> </p>
                        </div> 
                    </div>
                </div>  
            </div>    
            @else 
            <h5>Dealer Details</h5>
            <div class="card shadow-sm">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <p><span class="text-muted">Person Name : {{$packingslip->sales_order->service_partner->person_name}} </span> </p>
                        </div> 
                        <div class="form-group mb-3">
                            <p><span class="text-muted">Company Name : {{$packingslip->sales_order->service_partner->company_name}} </span> </p>
                        </div> 
                        <div class="form-group mb-3">
                            <p><span class="text-muted">Email : {{$packingslip->sales_order->service_partner->email}} </span> </p>
                        </div> 
                        <div class="form-group mb-3">
                            <p><span class="text-muted">Phone : {{$packingslip->sales_order->service_partner->phone}} </span> </p>
                        </div> 
                    </div>
                </div>
            </div>
            @endif                                     
        </div>   
    </div>
    <div class="row">
        <div class="col-md-12">
            <form id="myForm" action="{{ route('packingslip.save-invoice') }}" method="POST">
                @csrf
                <input type="hidden" name="packingslip_id" value="{{$packingslip_id}}">
                <input type="hidden" name="sales_order_id" value="{{$packingslip->sales_order_id}}">
                <input type="hidden" name="dealer_id" value="{{ $packingslip->sales_order->dealer_id }}">
                <input type="hidden" name="service_partner_id" value="{{ $packingslip->sales_order->service_partner_id }}">
                
                @php                    
                    $total_amount = 0;
                @endphp
            <h6>Item Details</h6>
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Item Name</th>
                        <th>Quantity</th>
                        <th>Price per Piece (Exc.Tax)</th>
                        <th>Total Amount (Exc.Tax)</th>
                        <th>HSN CODE</th>
                        <th>GST</th>
                        <th>Total Amount (Inc.Tax)</th>
                    </tr>
                </thead>
                <tbody>
                @php
                    $i = 1;
                @endphp
                @forelse ($data as $item)
                    @php
                        $details = json_decode($item->details);
                        $getSalesOrderProduct = getSalesOrderProduct($packingslip->sales_order_id,$item->product_id);
                        $product_price = $getSalesOrderProduct->product_price;
                        $hsn_code = $getSalesOrderProduct->hsn_code;
                        $tax = $getSalesOrderProduct->tax;
                        $total_price = ($item->quantity * $product_price);
                        $total_amount += $total_price;


                        $exc_tax_pro_price = ($product_price - $tax);
                        $count_exc_tax_pro_price = ($item->quantity * $exc_tax_pro_price);
                        $getGSTAmount = getGSTAmount($product_price,$tax);
                        $gst_amount = $getGSTAmount['gst_amount'];
                        $net_price = $getGSTAmount['net_price'];
                        $count_price = ($item->quantity * $net_price);
                    @endphp
                    <tr>
                        <td>{{ $i }}</td>
                        <td>{{ $item->product->unique_id }} | {{ $item->product->title }}</td>
                        <td>{{ $item->quantity }} pcs</td>
                        <td>Rs. {{ number_format((float)$net_price, 2, '.', '') }}</td>
                        <td>Rs. {{ number_format((float)$count_price, 2, '.', '') }}</td>
                        <td>{{ $hsn_code }}</td>
                        <td>{{ $tax }} %</td>
                        <td>Rs. {{ number_format((float)$total_price, 2, '.', '') }}</td>
                    </tr>
                    <input type="hidden" name="items[{{$i}}][product_id]" value="{{ $item->product_id }}">
                    <input type="hidden" name="items[{{$i}}][product_title]" value="{{ $item->product->title }}">
                    <input type="hidden" name="items[{{$i}}][quantity]" value="{{ $item->quantity }}">
                    <input type="hidden" name="items[{{$i}}][price]" value="{{ number_format((float)$product_price, 2, '.', '') }}">
                    <input type="hidden" name="items[{{$i}}][tax]" value="{{ $tax }}">
                    <input type="hidden" name="items[{{$i}}][hsn_code]" value="{{ $hsn_code }}">
                    <input type="hidden" name="items[{{$i}}][total_price]" value="{{ number_format((float)$total_price, 2, '.', '') }}">
                    <input type="hidden" name="items[{{$i}}][price_exc_tax]" value="{{ number_format((float)$net_price, 2, '.', '') }}">
                    <input type="hidden" name="items[{{$i}}][total_price_exc_tax]" value="{{ number_format((float)$count_price, 2, '.', '') }}">
                    @php
                        $i++;
                    @endphp
                @empty
                    
                @endforelse
                
                <input type="hidden" name="total_amount" value="{{$total_amount}}">
                    
                </tbody>
            </table>
            <div class="card shadow-sm">
                <div class="card shadow-sm">
                    <div class="card-body"> 
                        <div class="row mb-3 justify-content-end">
                            <div class="col-md-8">
                                <h6 class="text-muted mb-2">Total Invoice Amount (Inc.Tax)</h6>
                            </div>
                            <div class="col-md-4 text-end">
                                <table class="w-100">            
                                    <tbody><tr class="border-top">
                                        <td>
                                            <h6 class="text-muted mb-2"> Rs. {{ number_format((float)$total_amount, 2, '.', '') }} </h6>
                                        </td>
                                    </tr>
                                </tbody></table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="form-check">
                                <input type="checkbox" name="" class="form-check-input" id="acknowledgement" value="1">
                                <label for="acknowledgement" class="form-check-label">
                                    As per the Indian IT Act, an electronic document requires an electronic signature as prescribed by the Act, to gain legal sanctity in the court of law. Hence saying that the printed document in the subject is produced electronically and therefore does not require a signature is not acceptable.
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body text-end">
                    <a href="{{ route('packingslip.list') }}" class="btn btn-sm btn-danger">Back</a>
                    <button type="submit" id="submitBtn" class="btn btn-sm btn-success">Generate Invoice</button>
                </div>
            </div>
            </form>
        </div>
    </div>
</section>
<script>
    $(document).ready(function(){
        $('#submitBtn').prop('disabled', true);
    });
    $("#myForm").submit(function() {
        $('input').attr('readonly', 'readonly');
        $('#submitBtn').attr('disabled', 'disabled');   
        $('#submitBtn').html('<i class="fi fi-br-refresh"></i>');     
        return true;
    });
    $('#acknowledgement').change(function(){
        var isCheck = $("#acknowledgement:checked").length;
        // alert(isCheck)
        if(isCheck == 1) {
            $('#submitBtn').prop('disabled', false);
        } else {
            $('#submitBtn').prop('disabled', true);
        }
    });
</script>  
@endsection 