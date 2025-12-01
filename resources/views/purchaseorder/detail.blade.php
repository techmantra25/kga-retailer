@extends('layouts.app')
@section('content')
@section('page', 'Details')
<section>
    <ul class="breadcrumb_menu"> 
        <li>Purchase Order</li>
        @if (in_array($order->status,[1,3]))
        <li><a href="{{ route('purchase-order.list') }}">PO</a> </li>
        @else
        <li><a href="{{ route('grn.index') }}">GRN</a> </li>
        @endif
        
        <li>{{$order->order_no}}</li>
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
                            <p><span class="text-muted">Order No : {{$order->order_no}} </span> </p>
                        </div> 
                        <div class="form-group mb-3">
                            <p><span class="text-muted">Order Date : {{ date('d/m/Y h:i a', strtotime($order->created_at)) }} </span> </p>
                        </div> 
                        <div class="form-group mb-3">
                            <p><span class="text-muted">Supplier : {{$order->supplier->public_name}} </span> </p>
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
                        <th>Type</th>
                        <th>MRP</th>
                        @if ($order->type == 'sp')
                        <th>Pack Of</th>
                        <th>Quantity Each Pack</th>
                        @endif
                        <th>Total Quantity</th>
                        <th>Cost Price</th>
                        <th>HSN</th>
                        <th>Tax(%)</th>
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
                        <td>
                            @if ($item->product->type == 'fg')
                                <span class="badge bg-dark">Finished Goods</span>
                            @else
                                <span class="badge bg-dark">Spare Parts</span>
                            @endif
                        </td>
                        <td>{{ number_format((float)$item->mrp, 2, '.', '') }}</td>
                        @if ($order->type == 'sp')
                        <td>{{$item->pack_of}}</td>
                        <td>{{$item->quantity_in_pack}} pcs</td>                            
                        @endif
                        <td>{{$item->quantity}} pcs</td>
                        <td>{{ number_format((float)$item->cost_price, 2, '.', '') }}</td>
                        <td>{{ $item->hsn_code }}</td>
                        <td>{{$item->tax}}</td>
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