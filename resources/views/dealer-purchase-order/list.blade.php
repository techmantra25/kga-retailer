@extends('layouts.app')
@section('content')
@section('page', 'Dealer Purchase Order Management')
<section>
    <ul class="breadcrumb_menu">    
        <li>Dealer Purchase Order Management</li>    
        <li>List Orders</li>
    </ul>
    <div class="search__filter">
        <div class="row align-items-center justify-content-between">
            <div class="col">
                
            </div>
            <div class="col-auto">
                <a href="{{ route('dealer-purchase-order.add') }}" class="btn btn-outline-primary select-md">Add New</a>  
            </div>
            <div class="col-auto">
                <form action="" id="searchForm">
                <div class="row g-3 align-items-center">                    
                    <div class="col-auto">
                        <input type="search" autocomplete="off" name="search" value="{{$search}}" class="form-control select-md" placeholder="Search here..">
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
            
            <div class="col-auto">
                <p>{{$totalResult}} Items</p>
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>ID</th>
                    <th>Date</th>
                    <th>Dealer</th>
                    <th>Items</th>
                    <th>Amount</th>       
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                {{-- {{Request::getRequestUri()}} --}}
                {{-- {{Request::fullUrl()}} --}}
            @php
                if(empty(Request::get('page')) || Request::get('page') == 1){
                    $i=1;
                } else {
                    $i = (((Request::get('page')-1)*$paginate)+1);
                } 
    
                
            @endphp
            @forelse ($data as $item)
            @php
                $details = json_decode($item->details);            
            @endphp
                <tr>
                    <td>{{$i}}</td>
                    <td>{{$item->order_no}}</td>
                    <td>{{date('j M Y, l', strtotime($item->created_at))}}</td>
                    <td>
                        <span> Name: <strong>{{$item->dealer->name}}</strong> 
                            </span> <br>
                    </td>                    
                    <td>                        
                        <button type="button" class="btn btn-outline-success select-md" data-bs-toggle="modal" data-bs-target="#exampleModal{{$item->id}}"> View Items ({{ count($item->products) }}) </button>
                        <!-- Modal -->
                        <div class="modal fade" id="exampleModal{{$item->id}}" tabindex="-1" aria-labelledby="" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="prodTitle">
                                            {{$item->order_no}} / 
                                            
                                                {{$item->dealer->name}}
                                            
                                        </h5>
                                        
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="table-responsive">
                                            <table class="table" id="prodHistTable">
                                                <thead>
                                                    <th>#</th>
                                                    <th>Product</th>
                                                    <th>Total Pcs</th>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $j=1;
                                                        $details = $item->products;
                                                    @endphp
                                                    @foreach ($details as $detail)
                                                        <tr>
                                                            <td>{{$j}}</td>
                                                            <td>
                                                                {{$detail->product->title}}
                                                            </td>
                                                            <td>{{$detail->quantity}}</td>
                                                        </tr>
                                                    @php
                                                        $j++;
                                                    @endphp
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Ok</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        Rs. {{ number_format((float)$item->amount, 2, '.', '') }}
                    </td>
                    <td>
                        @if (empty($item->is_cancelled))
                            @if(empty($item->is_goods_in))
                            <span class="badge bg-warning">Pending</span>            
                            @else
                            <span class="badge bg-success">Goods In</span>
                            @endif   
                        @else
                            <span class="badge bg-danger">Cancelled</span>  
                        @endif
                                                     
                    </td>
                    <td>
                        @if (empty($item->is_cancelled) && empty($item->is_goods_in))
                        <a href="{{ route('dealer-purchase-order.generate-grn', [Crypt::encrypt($item->id),Request::getQueryString()] ) }}" class="btn btn-outline-success select-md">Generate GRN</a>
                        @endif  
                        @if (empty($item->is_cancelled) && empty($item->is_goods_in))
                        <a href="{{route('dealer-purchase-order.cancel', [Crypt::encrypt($item->id),Request::getQueryString()] )}}" onclick="return confirm('Are you sure want to cancel the order?');" class="btn btn-outline-danger select-md">Cancel Order</a>
                        @endif                          
                        <a href="{{ route('dealer-purchase-order.show',  [Crypt::encrypt($item->id),Request::getQueryString()] ) }}" class="btn btn-outline-primary select-md">Details</a> 
                        <a href="{{ route('dealer-purchase-order.barcodes', Crypt::encrypt($item->id)) }}" class="btn btn-outline-primary select-md">Barcodes</a>
                    </td>
                </tr>
                @php
                    $i++;
                @endphp
            @empty
                <tr>
                    <td colspan="8" style="text-align: center;">
                        No data found
                    </td>
                </tr>
            @endforelse
                
            </tbody>
        </table>
    </div>
    
    {{$data->links()}}
    
</section>
<script>
    $(document).ready(function(){
        $('div.alert').delay(3000).slideUp(300);
    })
    $('input[type=search]').on('search', function () {
        // search logic here
        // this function will be executed on click of X (clear button)
        $('#searchForm').submit();
    });
    $('#type').on('change', function(){
        $('#searchForm').submit();
    })
    $('#status').on('change', function(){
        $('#searchForm').submit();
    })
</script>  
@endsection 