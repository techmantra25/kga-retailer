@extends('layouts.app')
@section('content')
@section('page', 'Stock Log Details')
<section>
    <ul class="breadcrumb_menu"> 
        <li>
            <a href="{{ route('stock.list', $getQueryString) }}">
                <i class="fi-br-arrow-alt-circle-left"></i>
                Back To List
            </a>
        </li>
        
    </ul>
    <ul class="pincodeclass">
        <li>
            <a href="{{ route('product.show', Crypt::encrypt($product_id)) }}?backtomodule=stock_logs&backtodestination={{Request::fullUrl()}}">{{ $product->title }}</a>
        </li>
        <li>
            >
        </li>
        <li>
            <span>{{$count}}</span>
        </li>
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
            
            <div class="col-auto">

            </div>
        </div>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th>#</th>     
                <th>Date</th>    
                <th>In / Out</th>
                <th>Quantity</th>
                <th>Source</th>
            </tr>
        </thead>
        <tbody>
        @php
            if(empty(Request::get('page')) || Request::get('page') == 1){
                $i=1;
            } else {
                $i = (((Request::get('page')-1)*10)+1);
            } 
        @endphp
        @forelse ($data as $item)
            <tr>
                <td>{{$i}}</td>
                <td>{{ date('j M Y, l', strtotime($item->created_at)) }}</td>
                <td>
                    @if ($item->type == 'in')
                        <span class="badge bg-success">{{ ucwords($item->type) }}</span>
                    @else
                        <span class="badge bg-danger">{{ ucwords($item->type) }}</span>
                    @endif
                </td>
                <td>
                    @if ($item->type == 'in')
                        <span class="">{{ $item->quantity }} pcs</span>
                    @else
                        <span class="">{{ $item->quantity }} pcs</span>
                    @endif
                    {{-- {{$item->type}} --}}
                </td>
                <td>
                    @if ($item->entry_type == 'grn')
                        @if (!empty($item->purchase_order_id))
                            <a href="{{ route('purchase-order.show',Crypt::encrypt($item->purchaseorder->id)) }}?backtomodule=stock_logs&backtodestination={{Request::fullUrl()}}" class="btn btn-outline-success select-md">{{$item->purchaseorder->order_no}}</a>
                        @elseif (!empty($item->return_spare_id))
                            <a href="{{ route('return-spares.show',Crypt::encrypt($item->return_spare_id)) }}?backtomodule=stock_logs&backtodestination={{Request::fullUrl()}}" class="btn btn-outline-success select-md">{{$item->returnspares->transaction_id}}</a>
                        @endif
                        
                    @else
                        <a href="{{ route('packingslip.show', Crypt::encrypt($item->packingslip_id)) }}?backtomodule=stock_logs&backtodestination={{Request::fullUrl()}}" class="btn btn-outline-danger select-md">{{$item->packingslip->slipno}}</a>
                    @endif
                </td>
            </tr>
            @php
                $i++;
            @endphp
        @empty
            <tr>
                <td>
                    No data found
                </td>
            </tr>
        @endforelse
            
        </tbody>
    </table>
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
</script>  
@endsection 