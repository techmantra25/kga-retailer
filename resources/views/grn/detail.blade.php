@extends('layouts.app')
@section('content')
@section('page', 'Details')
<section>
    <ul class="breadcrumb_menu"> 
        <li>Purchase Order</li>
        
        <li><a href="{{ route('grn.index') }}">GRN</a> </li>
                
        {{-- <li>{{$order->order_no}}</li> --}}
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
                            <p><span class="text-muted">Order No : {{$stock->grn_no}} </span> </p>
                        </div> 
                        <div class="form-group mb-3">
                            <p><span class="text-muted">Order Date : {{ date('d/m/Y h:i a', strtotime($stock->created_at)) }} </span> </p>
                        </div> 
                        <div class="form-group mb-3">
                            <p><span class="text-muted">Order Amount : {{ number_format((float)$stock->amount, 2, '.', '') }} </span> </p>
                        </div> 
                    </div>
                </div>  
            </div>                                      
        </div>  
        @if (!empty($stock->purchase_order_id))
        <div class="col-sm-6">
            <h5>Supplier</h5>
            <div class="card shadow-sm">
                <div class="card shadow-sm">
                    <div class="card-body">
                        
                        <div class="form-group mb-3">
                            <p><span class="text-muted">Name : {{$stock->purchase_order->supplier->public_name}} </span> </p>
                        </div> 
                        <div class="form-group mb-3">
                            <p>
                                <span class="text-muted">
                                    Email: 
                                    {{
                                        str_repeat('*', max(0, strlen($stock->purchase_order->supplier->email) - 5)) . 
                                        substr($stock->purchase_order->supplier->email, -5)
                                    }}
                                </span>
                            </p>
                        </div>
                        <div class="form-group mb-3">
                            <p>
                                <span class="text-muted">
                                    Phone: 
                                    {{
                                        str_repeat('*', max(0, strlen($stock->purchase_order->supplier->phone) - 3)) . 
                                        substr($stock->purchase_order->supplier->phone, -3)
                                    }}
                                </span>
                            </p>
                        </div> 
                    </div>
                </div>  
            </div>                                      
        </div>   
        @else
        <div class="col-sm-6">
            <h5>Service Partner</h5>
            <div class="card shadow-sm">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <p><span class="text-muted">Company Name : {{$stock->return_spare->service_partner->company_name}} </span> </p>
                        </div> 
                        <div class="form-group mb-3">
                            <p><span class="text-muted">Person Name : {{$stock->return_spare->service_partner->person_name}} </span> </p>
                        </div> 
                        <div class="form-group mb-3">
                            <p><span class="text-muted">Email : {{$stock->return_spare->service_partner->email}} </span> </p>
                        </div> 
                        <div class="form-group mb-3">
                            <p><span class="text-muted">Phone : {{$stock->return_spare->service_partner->phone}} </span> </p>
                        </div> 
                    </div>
                </div>  
            </div>                                      
        </div>   
        @endif
        
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
                        <th>Quantity</th>  
                        <th>Cost Price</th> 
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
                        <td>{{$item->count}}</td>                        
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