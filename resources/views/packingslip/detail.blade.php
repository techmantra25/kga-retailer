@extends('layouts.app')
@section('content')
@section('page', 'Details')
<section>
    <ul class="breadcrumb_menu"> 
        <li><a href="{{ route('packingslip.list') }}">Packing Slip</a> </li>
        <li>
            {{$data->slipno}}
        </li>
    </ul>   
    @if (!empty(Request::get('backtomodule')))
    <ul class="breadcrumb_menu">   
            {{-- {{ Request::get('backtodestination') }}  --}}
        <li><a href="{{Request::get('backtodestination')}}">
            <i class="fi fi-br-arrow-alt-circle-left"></i>
            Back To {{ str_replace("_"," ",ucwords(Request::get('backtomodule'))) }}
        </a></li>               
    </ul>
    @endif  
    <div class="row">
        <div class="col-sm-6">
            <h5>Order Details</h5>
            <div class="card shadow-sm">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <p><span class="text-muted">Order No : {{$data->sales_order->order_no}} </span> </p>
                        </div> 
                        <div class="form-group mb-3">
                            <p><span class="text-muted">Order Date : {{ date('d/m/Y h:i a', strtotime($data->sales_order->created_at)) }} </span> </p>
                        </div> 
                        <div class="form-group mb-3">
                            <p><span class="text-muted">Order Amount : Rs. {{ number_format((float)$data->sales_order->order_amount, 2, '.', '') }} </span> </p>
                        </div> 
                    </div>
                </div>  
            </div>                                      
        </div>  
        <div class="col-sm-6">
            @if (!empty($data->sales_order->dealer))
            <h5>Dealer Details</h5>
            <div class="card shadow-sm">
                <div class="card shadow-sm">
                    <div class="card-body">                        
                        <div class="form-group mb-3">
                            <p><span class="text-muted">Person Name : {{$data->sales_order->dealer->name}} </span> </p>
                        </div> 
                        <div class="form-group mb-3">
                            <p><span class="text-muted">Email : {{$data->sales_order->dealer->email}} </span> </p>
                        </div> 
                        <div class="form-group mb-3">
                            <p><span class="text-muted">Phone : {{$data->sales_order->dealer->phone}} </span> </p>
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
                            <p><span class="text-muted">Person Name : {{$data->sales_order->service_partner->person_name}} </span> </p>
                        </div> 
                        <div class="form-group mb-3">
                            <p><span class="text-muted">Company Name : {{$data->sales_order->service_partner->company_name}} </span> </p>
                        </div> 
                        <div class="form-group mb-3">
                            <p><span class="text-muted">Email : {{$data->sales_order->service_partner->email}} </span> </p>
                        </div> 
                        <div class="form-group mb-3">
                            <p><span class="text-muted">Phone : {{$data->sales_order->service_partner->phone}} </span> </p>
                        </div> 
                    </div>
                </div>
            </div>
            @endif
        </div>   
    </div>
    <div class="row">
        <div class="col-md-12">
            <h5>Item Details</h5>
            <table class="table" id="timePriceTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Item Name</th>
                        <th>Piece</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $i=1;
                        $details = $data->packingslip_products;
                    @endphp
                    @forelse ($details as $item)
                    <tr>
                        <td>{{$i}}</td>
                        <td>
                            <a href="{{ route('product.show', Crypt::encrypt($item->product_id)) }}" class="showdetails">
                                 {{ $item->product->title }}
                            </a>
                        </td>
                        <td>
                            {{$item->quantity}} pcs
                        </td>
                    </tr>
                    @php
                        $i++;
                    @endphp
                    @empty
                        
                    @endforelse
                   
                </tbody>
            </table>
        </div>
    </div>

</section>
@endsection