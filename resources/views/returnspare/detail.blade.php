@extends('layouts.app')
@section('content')
@section('page', 'Service Partner Returned Spare Details')
<section>
    <ul class="breadcrumb_menu"> 
        <li>Returned Spare Management</li>
        <li><a href="{{ route('return-spares.list') }}">Service Partner Returned Spares</a> </li>
        <li>{{ $data->transaction_id }}</li>
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
                            <p><span class="text-muted">Order No : {{$data->transaction_id}} </span> </p>
                        </div> 
                        <div class="form-group mb-3">
                            <p><span class="text-muted">Order Date : {{ date('d/m/Y h:i A', strtotime($data->created_at)) }} </span> </p>
                            {{-- <p><span class="text-muted">Order Date : {{ date('jS M y h:i A', strtotime($order->created_at)) }} </span> </p> --}}
                        </div> 
                        <div class="form-group mb-3">
                            <p><span class="text-muted">Order Amount : Rs. {{ number_format((float)$data->amount, 2, '.', '') }} </span> </p>
                        </div> 
                        <div class="form-group mb-3">
                            <p><span class="text-muted">Status : 

                                @if($data->status == 1)
                                    <span class="badge bg-warning">Pending</span>
                                @elseif ($data->status == 2)
                                    <span class="badge bg-success">Received</span>
                                @elseif ($data->status == 3)
                                    <span class="badge bg-danger">Cancelled</span>
                                @endif  
                            </span> </p>
                        </div> 
                        
                    </div>
                </div>  
            </div>                                      
        </div>  
        
        <div class="col-sm-6">
            <h5>Service Partner Details</h5>
            <div class="card shadow-sm">
                <div class="card shadow-sm">
                    <div class="card-body">
                        
                        <div class="form-group mb-3">
                            <p><span class="text-muted">Person Name : {{$data->service_partner->person_name}} </span> </p>
                        </div> 
                        <div class="form-group mb-3">
                            <p><span class="text-muted">Company Name : {{$data->service_partner->company_name}} </span> </p>
                        </div> 
                        <div class="form-group mb-3">
                            <p><span class="text-muted">Email : {{$data->service_partner->email}} </span> </p>
                        </div> 
                        <div class="form-group mb-3">
                            <p><span class="text-muted">Phone : {{$data->service_partner->phone}} </span> </p>
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
                        <th>Tax(%)</th>
                        <th>Total Price</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $i=1;
                    @endphp
                    @forelse ($data->items as $item)
                    <tr>
                        <td>{{$i}}</td>
                        <td>
                            <a href="{{ route('product.show', Crypt::encrypt($item->product_id)) }}" class="showdetails">
                                {{ $item->product->unique_id }} | {{ $item->product->title }}
                            </a>
                        </td>
                        <td>{{$item->quantity}} pcs</td>
                        <td>Rs. {{ number_format((float)$item->product_price, 2, '.', '') }}</td>
                        <td>{{$item->tax}}</td>
                        <td>Rs. {{ number_format((float)$item->product_total_price, 2, '.', '') }}</td>
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