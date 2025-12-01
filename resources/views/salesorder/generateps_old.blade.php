@extends('layouts.app')
@section('content')
@section('page', 'Generate Packing Slip')
<section>
    <ul class="breadcrumb_menu"> 
        <li>Service Partner Spare Order Management</li>
        <li><a href="{{ route('sales-order.list') }}?{{$getQueryString}}">Service Partner Spare Orders</a> </li>
        <li> {{ $data[0]->order->order_no }} </li>
        <li>Generate Spare Packing Slip</li>
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
    <form id="myForm" action="{{ route('sales-order.save-packing-slip', [$idStr,$getQueryString]) }}" method="POST">
        @csrf
        <input type="hidden" name="sales_order_id" value="{{ Crypt::decrypt($idStr) }}">
        
        @php
            
            $slipno = 'PS'.genAutoIncreNoYearWiseOrder(4,'packingslips',date('Y'),date('m'));
        @endphp        
        <input type="hidden" name="slipno" class="form-control" value="{{ $slipno }}" id="">
        <div class="row">
           
            <div class="row">
                <div class="col-md-4">
                    <div class="card shadow-sm">
                        <div class="form-group">
                            <label for="">Goods Out With  </label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="goods_out_type" id="scan" value="scan" @if(old('goods_out_type') == 'scan') checked @endif>
                                <label class="form-check-label" for="scan">Scan</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="goods_out_type" id="bulk" value="bulk" @if(old('goods_out_type') == 'bulk') checked @endif>
                                <label class="form-check-label" for="bulk">Bulk</label>
                            </div>
                            @error('goods_out_type') <p class="small text-danger">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </div>
            <div id="div-item-form">

            <div class="col-md-12">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>                            
                            <th>Product</th>
                            <th>Required Quantity</th>
                            <th>Stock In Quantity</th>
                            <th>Delivered Quantity</th>
                            <th>Update Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                    @php
                        $i=1;
                        $isBtnDisabledArr = array();
                    @endphp
                    @forelse ($data as $item)
                    @php
                        $details = json_decode($item->details);  
                        $stockinqty = getStockInventoryProduct($item->product_id);   
                        $required_quantity = ($item->quantity - $item->delivered_quantity); 
                        // echo 'stockinqty:- '.$stockinqty.'<br/>';
                        // echo 'quantity:- '.$item->quantity.'<br/>';
                        // echo 'delivered_quantity:- '.$item->delivered_quantity.'<br/>';   

                        $isBtnDisabled = 0;
                        if($stockinqty >= $required_quantity){
                            $isBtnDisabled = 1;
                        }
                        // if($required_quantity > $item->delivered_quantity){
                        //     $isBtnDisabled = 1;
                        // }

                        $isBtnDisabledArr[] = $isBtnDisabled;




                    @endphp
                    @if ( $stockinqty >= $required_quantity )
                    @if ($required_quantity > $item->delivered_quantity)

                    <tr>
                        <input type="hidden" name="details[{{$i}}][product_id]" value="{{$item->product_id}}">
                        <input type="hidden" name="details[{{$i}}][product_title]" value="{{$item->product->title}}">
                        <td>{{ $i }}</td>
                        <td>{{ $item->product->title }}</td>
                        <td>{{ $required_quantity }}</td>
                        <td>{{ $stockinqty }}</td>
                        <td>{{ $item->delivered_quantity }}</td>
                        <td>
                            <input type="number" max="{{$item->quantity}}" min="{{$item->quantity}}" name="details[{{$i}}][quantity]" class="form-control quantity" id="quantity{{$i}}" @if(!empty(old('details.'.$i.'.quantity'))) value="{{ old('details.'.$i.'.quantity') }}" @else value="{{$item->quantity}}"  @endif>
                            @error('details.'.$i.'.quantity') <p class="small text-danger">{{ $message }}</p> @enderror
                                                          
                        </td>
                    </tr>
                    @else
                    <tr>
                        <input type="hidden" disabled name="" value="{{$item->product_id}}">
                        <input type="hidden" disabled name="" value="{{$item->product->title}}">
                        <td>{{ $i }}</td>
                        <td>{{ $item->product->title }}</td>
                        <td>{{ $required_quantity }}</td>
                        <td>{{ $stockinqty }}</td>
                        <td>{{ $item->delivered_quantity }}</td>
                        <td>
                            <input type="number" disabled name="" placeholder="DELIVERED" class="form-control quantity" id="quantity{{$i}}" >
                            
                                                          
                        </td>
                    </tr>
                    @endif
                    @else
                    <tr>
                        <td>{{$i}}</td>
                        <td>{{$item->product->title}}</td>
                        <td>{{$item->quantity}}</td>
                        <td>{{ $stockinqty }}</td>
                        <td>{{ $item->delivered_quantity }}</td>
                        <td>
                            <input type="number" disabled name="" placeholder="INSUFFIECIENT QUANTITY" class="form-control" id="">                                                          
                        </td>
                    </tr>                                       
                    @endif  
                        
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
            </div>
            @php
                // print_r($isBtnDisabledArr);
                $submitDisabled = "";
                if(!in_array(0,$isBtnDisabledArr)){
                    $submitDisabled = "disabled";
                } else {
                    $submitDisabled = "";
                }
            @endphp
            <div class="row">
                <div class="col-md-12">
                    <div class="card shadow-sm">
                        <div class="card-body text-end">
                            <a href="{{route('sales-order.list')}}?{{$getQueryString}}" class="btn btn-sm btn-danger">Back</a>
                            <button type="submit" id="submitBtn"  class="btn btn-sm btn-success" >Save </button>
                        </div>
                    </div> 
                </div>
            </div>
            </div>
        </div>        
    </form>    
</section>
<script>
    $(document).ready(function(){
        $('div.alert').delay(3000).slideUp(300);
        $('#div-item-form').hide();
    });   
    $('#type').on('change', function(){
        $('#searchForm').submit();
    });
    $("#myForm").submit(function() {
        $('#submitBtn').attr('disabled', 'disabled');
        $('#submitBtn').html('<i class="fi fi-br-refresh"></i>').append('   Please wait ...');
        
        return true;
    });
    
    $(".quantity").on('keypress keyup keydown', function (evt) {
        evt.preventDefault();
    });
   
    $('input[type=radio]').on('click', function(){
        $('#div-item-form').show();
    })
</script>  
@endsection 