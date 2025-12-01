@extends('layouts.app')
@section('content')
@section('page', 'Edit PO')
<section>   
    <ul class="breadcrumb_menu">       
        <li>Purchase Order</li> 
        <li><a href="{{ route('purchase-order.list') }}">PO</a> </li>
        <li>Edit</li>
        <li>{{$order->order_no}}</li>
    </ul>
    <div class="row">
        <form id="myForm" action="{{ route('purchase-order.update',[$idStr,$getQueryString]) }}" method="POST">
            @csrf

            <input type="hidden" name="browser_name" class="browser_name">
            <input type="hidden" name="navigator_useragent" class="navigator_useragent">
            <input type="hidden" name="" id="type" value="{{$order->type}}">
        <div class="row">
            <div class="col-sm-12">     
                
                <div class="card shadow-sm">
                    <h6>Item Details</h6> 
                    <div class="table-responsive order-addmore">
                        <table class="table" id="timePriceTable">
                            <thead>
                                <tr>
                                    <th>Product<span class="text-danger">*</span></th>
                                    <th> MOP </th>  
                                    @if ($order->type == 'sp')
                                    <th>Pack Of<span class="text-danger">*</span></th>
                                    <th>Quantity in Pack<span class="text-danger">*</span></th>
                                    @endif
                                    <th>Total Quantity<span class="text-danger">*</span></th>
                                    <th>Cost Price (Inc.Tax)<span class="text-danger">*</span></th>  
                                    <th>HSN Code<span class="text-danger">*</span></th>
                                    <th>MRP (Inc.Tax)<span class="text-danger">*</span></th>
                                    <th> Tax<span class="text-danger">*</span></th>
                                    <th>Total Price (Inc.Tax)</th>
                                </tr>
                            </thead>
                            <tbody> 
                                @foreach ($data as $key => $item)
                                @php
                                  $key = $key+1;
                                @endphp
                                  
                                <tr id="tr_{{$key}}" class="tr_pro">
                                    <td class="f-12">
                                        <input type="text" autocomplete="off" class="form-control" id="product1" value="{{$item->product->title}}" readonly name="details[{{$key}}][product]" style="width: 200px">
                                        <input type="hidden" name="details[{{$key}}][product_id]" id="product_id{{$key}}" class="productids" value="{{$item->product_id}}" readonly>
                                       
                                        <div class="respDrop" id="respDrop{{$key}}"></div>
                                    </td>
                                    <td>
                                        <div class="input-group ">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    Rs.
                                                </div>
                                            </div>
                                            <input type="text" autocomplete="off" name="details[{{$key}}][mop]" class="form-control" readonly id="mop{{$key}}" value="{{$item->product->mop}}" style="width: 90px;">
                                        </div>
                                    </td>
                                    @if ($order->type == 'sp')
                                    <td>
                                        <input type="number" name="details[{{$key}}][pack_of]" readonly class="form-control" min="1" oninput="this.value = Math.abs(this.value)" class="form-control" id="pack_of{{$key}}" value="{{$item->pack_of}}" onkeyup="calculatePrice({{$key}})" onchange="calculatePrice({{$key}})">
                                    </td>
                                    <td>
                                        <input type="number" name="details[1][quantity_in_pack]" readonly id="quantity_in_pack{{$key}}" class="form-control" min="1" oninput="this.value = Math.abs(this.value)" value="{{$item->quantity_in_pack}}" onkeyup="calculatePrice({{$key}})" onchange="calculatePrice({{$key}})" >
                                    </td>
                                    @endif                                
                                    <td>
                                        @if ($order->type == 'fg')
                                        <input type="number" min="1" oninput="this.value = Math.abs(this.value)" class="form-control" id="quantity{{$key}}" name="details[{{$key}}][quantity]"  value="{{$item->quantity}}" readonly>
                                        @elseif ($order->type == 'sp')
                                        <input type="number" class="form-control" id="quantity{{$key}}" readonly name="details[{{$key}}][quantity]" value="{{$item->quantity}}" readonly>
                                        @endif                                    
                                    </td>  
                                    <td>                                    
                                        <div class="input-group ">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    Rs.
                                                </div>
                                            </div>
                                            <input type="text" autocomplete="off" onkeypress="validateNum(event)" class="form-control" id="cost_price{{$key}}" placeholder="" name="details[{{$key}}][cost_price]" onkeyup="calculatePrice({{$key}})" value="{{$item->cost_price}}">
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" autocomplete="off" name="details[{{$key}}][hsn_code]" class="form-control" id="hsn_code{{$key}}" value="{{$item->hsn_code}}" maxlength="20" style="width: 90px;" readonly>
                                    </td>
                                    <td>                                    
                                        <div class="input-group ">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    Rs.
                                                </div>
                                            </div>
                                            <input type="text" autocomplete="off" onkeypress="validateNum(event)" class="form-control" id="mrp{{$key}}" placeholder="" name="details[{{$key}}][mrp]" value="{{$item->mrp}}" style="width: 90px;">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group ">
                                            <input type="text" autocomplete="off" onkeypress="validateNum(event)" class="form-control" id="tax{{$key}}" value="{{$item->tax}}" name="details[{{$key}}][tax]" readonly maxlength="2" style="width:90px;">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    %
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>                                    
                                        <div class="input-group ">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    Rs.
                                                </div>
                                            </div>
                                            <input type="text" autocomplete="off" name="details[{{$key}}][total_price]" class="form-control total_price" id="total_price{{$key}}" value="{{$item->total_price}}" readonly style="width: 90px;">
                                        </div>
                                    </td>
                                    
                                </tr>  
                                        
                                @endforeach        
                                                          
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card shadow-sm">
                    <div class="card shadow-sm">
                        <div class="card-body"> 
                            <div class="row mb-3 justify-content-end">
                                <div class="col-md-8">
                                    <h6 class="text-muted mb-2">Total Amount (Inc.Tax)</h6>
                                </div>
                                <div class="col-md-4 text-end">
                                    <table class="w-100">            
                                        <tbody>
                                            <tr class="border-top">
                                                <td>
                                                    <h6 class="text-dark mb-0 text-end"> Rs. <span id="order_amount_text">{{$order->amount}}</span></h6>
                                                    <input type="hidden" name="order_amount_val" id="order_amount_val" value="{{ $order->amount }}">
                                                </td>
                                                
                                            </tr>
                                            <tr>
                                                <td>
                                                    @error('max_qty_validation1') <p class="small text-danger">{{ $message }}</p> @enderror    
                                                    
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    @error('max_qty_validation2') <p class="small text-danger">{{ $message }}</p> @enderror    
                                                    
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card shadow-sm">
                    <div class="card-body text-end">
                        <a href="{{route('purchase-order.list')}}" class="btn btn-sm btn-danger">Back</a>
                        <button type="submit" id="submitBtn" class="btn btn-sm btn-success">Update </button>
                        <p class="small filter-waiting-text"></p>
                    </div>
                </div>  
                                                                        
            </div>              
        </div>                         
        </form>             
    </div>    
</section>
<script>
    function getBrowserType() {
        const test = regexp => {
            return regexp.test(navigator.userAgent);
        };
        console.log(navigator.userAgent);
        var navigator_useragent = navigator.userAgent;
        $('.navigator_useragent').val(navigator_useragent);
        if (test(/opr\//i) || !!window.opr) {
            return 'Opera';
        } else if (test(/edg/i)) {
            return 'Microsoft Edge';
        } else if (test(/chrome|chromium|crios/i)) {
            return 'Google Chrome';
        } else if (test(/firefox|fxios/i)) {
            return 'Mozilla Firefox';
        } else if (test(/safari/i)) {
            return 'Apple Safari';
        } else if (test(/trident/i)) {
            return 'Microsoft Internet Explorer';
        } else if (test(/ucbrowser/i)) {
            return 'UC Browser';
        } else if (test(/samsungbrowser/i)) {
            return 'Samsung Browser';
        } else {
            return 'Unknown browser';
        }
    }
    const browserType = getBrowserType();
    console.log(browserType);
    $('.browser_name').val(browserType);

    $("#myForm").submit(function() {
        $('#submitBtn').attr('disabled', 'disabled');
        $('#submitBtn').html('<i class="fi fi-br-refresh"></i>').append('   Please wait ...');
        $('.filter-waiting-text').text('Please Wait ... This Process Will Take A Few Minutes .');
        return true;
    });

    function validateNum(evt) {
        var theEvent = evt || window.event;

        // Handle paste
        if (theEvent.type === 'paste') {
            key = event.clipboardData.getData('text/plain');
        } else {
        // Handle key press
            var key = theEvent.keyCode || theEvent.which;
            key = String.fromCharCode(key);
        }
        var regex = /[0-9]|\./;
        if( !regex.test(key) ) {
            theEvent.returnValue = false;
            if(theEvent.preventDefault) theEvent.preventDefault();
        }
    }

    function calculatePrice(number)
    {

        var type = $('#type').val();
        // alert(type);
        if(type == 'fg'){
            // alert('hi')
            var quantity = $('#quantity'+number).val();
            var cost_price = $('#cost_price'+number).val();
            
            var total_price = (quantity*cost_price);
            // console.log('total_price:- '+total_price);
            // alert(total_price)
            $('#total_price'+number).val(total_price);

            var order_amount = 0;
            $('.total_price').each(function(){
                if($(this).val() != ''){
                    order_amount += parseFloat($(this).val());
                }            
            });
            $('#order_amount_text').text(order_amount);
            $('#order_amount_val').val(order_amount);
            
            console.log('order_amount:- '+order_amount);
        } else {
            // alert('Hi')
            var pack_of = $('#pack_of'+number).val();
            var quantity_in_pack = $('#quantity_in_pack'+number).val();

            var quantity = (quantity_in_pack * pack_of);
            //$('#quantity'+number).val(quantity);
            var cost_price = $('#cost_price'+number).val();
            
            var total_price = (quantity*cost_price);
            // console.log('total_price:- '+total_price);
            $('#total_price'+number).val(total_price);

            var order_amount = 0;
            $('.total_price').each(function(){
                if($(this).val() != ''){
                    order_amount += parseFloat($(this).val());
                }            
            });
            $('#order_amount_text').text(order_amount);
            $('#order_amount_val').val(order_amount);
            
            console.log('order_amount:- '+order_amount);
        }
        
        
        
    }


</script>
@endsection