@extends('layouts.app')
@section('content')
@section('page', 'Details')
<section>
    <ul class="breadcrumb_menu"> 
        <li>Dealer Purchase Order Management</li>        
        <li>{{$order->order_no}}</li>
    </ul>
    
    <ul class="breadcrumb_menu">
        <li>
            <a href="{{ route('dealer-purchase-order.list') }}">
                <i class="fi fi-br-arrow-alt-circle-left"></i> Back To List
            </a>
        </li>   
    </ul>
    <div class="row">
        <div class="col-sm-6">
            <h5>Order Details</h5>
            <div class="card shadow-sm">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <p><span class="text-muted">Order No : {{$order->order_no}} </span> </p>
                        </div> 
                        <div class="form-group mb-3">
                            <p><span class="text-muted">Order Date : {{ date('d/m/Y h:i a', strtotime($order->created_at)) }} </span> </p>
                        </div> 
                        <div class="form-group mb-3">
                            <p><span class="text-muted">Dealer : {{$order->dealer->name}} </span> </p>
                        </div> 
                        <div class="form-group mb-3">
                            <p><span class="text-muted">Order Amount : {{ number_format((float)$order->amount, 2, '.', '') }} </span> </p>
                        </div> 
                    </div>
                </div>  
            </div>                                      
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
                        <th>Quantity</th>
                        <th>Rate</th>
                        <th>Total Price</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $i=1;
                    @endphp
                    @forelse ($data as $item)
                    <tr>
                        <td>{{$i}}</td>
                        <td>
                            <a href="{{ route('product.show', Crypt::encrypt($item->product_id)) }}" class="showdetails">{{ $item->product->unique_id }} | {{ $item->product->title }}</a>
                        </td>                        
                        <td>{{$item->quantity}} pcs</td>
                        <td>{{ number_format((float)$item->cost_price, 2, '.', '') }}</td>
                        <td>{{ number_format((float)$item->total_price, 2, '.', '') }}</td>
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