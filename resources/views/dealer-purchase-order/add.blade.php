@extends('layouts.app')
@section('content')
@section('page', 'Create New Order')
<section>   
    <ul class="breadcrumb_menu"> 
        <li>Dealer Purchase Order Management</li>
        <li><a href="{{ route('sales-order.list') }}">List Orders</a> </li>
        <li>Create New Order</li>
    </ul>
    <div class="row">
        
        @if (empty($dealer_id))
        <form id="myForm" action="{{ route('dealer-purchase-order.add') }}"  method="GET">
        @else
        <form id="myForm" action="{{ route('dealer-purchase-order.store') }}"  method="POST">
            @csrf
        @endif
        
        
        
        <div class="row">
            <div class="col-sm-12">  
                @if (empty($dealer_id))
                <div class="card shadow-sm">                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="">Dealer <span class="text-danger">*</span></label>
                            <select name="dealer_id" class="form-control"  id="dealer_id"  >
                                <option value="" hidden selected>Select an option</option>
                                @forelse ($dealer as $d)
                                    <option value="{{$d->id}}" @if(old('dealer_id') == $d->id) selected @endif>{{$d->name}}</option>
                                @empty
                                    <option value="" disabled>No dealer found ...</option>
                                @endforelse
                            </select>
                        </div>
                        @error('dealer_id') <p class="small text-danger">{{ $message }}</p> @enderror
                    </div>            
                </div>  

                <div class="card shadow-sm">
                    <div class="card-body text-end">
                        <a href="{{route('dealer-purchase-order.list')}}" class="btn btn-sm btn-danger">Back</a>
                        
                        <button type="submit" id="submitBtn" class="btn btn-sm btn-success">Next </button>
                    </div>
                </div>  

                @else

                <div class="card shadow-sm">                    
                    <div class="col-md-6">
                        <input type="hidden" name="dealer_id" value="{{$dealer_id}}">
                        <div class="form-group">
                            <label for="">Dealer <span class="text-danger">*</span></label>
                            <select name="dealer_id" class="form-control" disabled id="dealer_id"  >
                                <option value="" hidden selected>Select an option</option>
                                @forelse ($dealer as $d)
                                    <option value="{{$d->id}}" @if($dealer_id == $d->id) selected @endif>{{$d->name}}</option>
                                @empty
                                    <option value="" disabled>No dealer found ...</option>
                                @endforelse
                            </select>
                        </div>
                        @error('dealer_id') <p class="small text-danger">{{ $message }}</p> @enderror
                    </div>            
                </div>  
                 
                <div class="card shadow-sm">
                    <h6>Item Details</h6>
                    <div class="table-responsive order-addmore">
                        <table class="table" id="timePriceTable">
                            <thead>
                                <tr>
                                    <th>Product<span class="text-danger">*</span></th>
                                    <th>Quantity <i class="fi fi-br-info" title="Total Sold Quantity"></i> <span class="text-danger">*</span></th>
                                    <th>Price <i class="fi fi-br-info" title="Max Sold Price"></i><span class="text-danger">*</span> </th>
                                    <th>Total Price</th>  
                                </tr>
                            </thead>
                            <tbody>    
                                @if(old('details'))
                                @php
                                    $old_details = old('details');
                                @endphp
                                @foreach ($old_details as $key=>$details)
                                <tr id="tr_{{$key}}" class="tr_pro">
                                    <td class="f-12">
                                        <input type="text" autocomplete="off" class="form-control" id="product{{$key}}" onkeyup="getProducts(this.value,{{$key}},'fg');" placeholder="Search product ... " name="details[{{$key}}][product]" value="{{ old('details.'.$key.'.product') }}" style="width: 350px">
                                        <input type="hidden" name="details[{{$key}}][product_id]" id="product_id{{$key}}" class="productids" value="{{ old('details.'.$key.'.product_id') }}">
                                        <div class="respDrop" id="respDrop{{$key}}"></div>
                                        @error('details.'.$key.'.product_id') <p class="small text-danger">{{ $message }}</p> @enderror
                                    </td>
                                    <td>
                                        <input type="number" min="1" oninput="this.value = Math.abs(this.value)" class="form-control" id="quantity{{$key}}" placeholder="" name="details[{{$key}}][quantity]" onkeyup="calculatePrice({{$key}})" onchange="calculatePrice({{$key}})"  value="{{ old('details.'.$key.'.quantity') }}" style="width: 75px;">
                                        @error('details.'.$key.'.quantity') <p class="small text-danger">{{ $message }}</p> @enderror
                                    </td> 
                                    <td>                                    
                                        <div class="input-group ">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    Rs.
                                                </div>
                                            </div>
                                            <input type="text" autocomplete="off" name="details[{{$key}}][product_price]" class="form-control" id="product_price{{$key}}" onkeyup="calculatePrice({{$key}})" value="{{ old('details.'.$key.'.product_price') }}">
                                        </div>
                                        @error('details.'.$key.'.product_price') <p class="small text-danger">{{ $message }}</p> @enderror
                                    </td>
                                    <td>                                    
                                        <div class="input-group ">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    Rs.
                                                </div>
                                            </div>
                                            <input type="text" autocomplete="off" name="details[{{$key}}][product_total_price]" class="form-control total_price" readonly id="product_total_price{{$key}}" value="{{ old('details.'.$key.'.product_total_price') }}" style="width:200px">
                                        </div>
                                        @error('details.'.$key.'.product_total_price') <p class="small text-danger">{{ $message }}</p> @enderror
                                    </td>       
                                    <td>
                                        <a class="btn btn-sm btn-success actionTimebtn addNewTime" id="addNew{{$key}}">+</a>
                                        <a class="btn btn-sm btn-danger actionTimebtn removeTimePrice" id="removeNew{{$key}}" onclick="removeRow({{$key}})">X</a>
                                    </td>
                                </tr>  
                                @endforeach
                                @else
                                <tr id="tr_1" class="tr_pro">
                                    <td class="f-12">
                                        <input type="text" autocomplete="off" class="form-control" id="product1" onkeyup="getProducts(this.value,1,'fg');" placeholder="Search product ... " name="details[1][product]" style="width: 350px">
                                        <input type="hidden" name="details[1][product_id]" id="product_id1" class="productids">
                                        <div class="respDrop" id="respDrop1"></div>
                                    </td>
                                    <td>
                                        <input type="number" min="1" oninput="this.value = Math.abs(this.value)" class="form-control" id="quantity1" placeholder="" name="details[1][quantity]" onkeyup="calculatePrice(1)" onchange="calculatePrice(1)" value="1" style="width: 75px;">
                                    </td>     
                                    <td>                                    
                                        <div class="input-group ">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    Rs.
                                                </div>
                                            </div>
                                            <input type="text" autocomplete="off" name="details[1][product_price]" class="form-control" id="product_price1" onkeyup="calculatePrice(1)">
                                        </div>
                                    </td>
                                    <td>                                    
                                        <div class="input-group ">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    Rs.
                                                </div>
                                            </div>
                                            <input type="text" name="details[1][product_total_price]" class="form-control total_price" readonly id="product_total_price1" style="width:200px">
                                        </div>
                                    </td>                            
                                    <td>
                                        <a class="btn btn-sm btn-success actionTimebtn addNewTime" id="addNew1">+</a>
                                        <a class="btn btn-sm btn-danger actionTimebtn removeTimePrice" id="removeNew1" onclick="removeRow(1)">X</a>
                                    </td>
                                </tr> 
                                @endif                          
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card shadow-sm">
                    <div class="card shadow-sm">
                        <div class="card-body"> 
                            <div class="row  justify-content-end">
                                <div class="col-md-8">
                                    <h6 class="text-muted mb-2">Total Amount</h6>
                                </div>
                                <div class="col-md-4 text-end">
                                    <table class="w-100">            
                                        <tbody><tr class="border-top">
                                            <td>
                                                <h6 class="text-dark mb-0 text-end"> Rs <span id="order_amount_text">0</span></h6>
                                                <input type="hidden" name="order_amount_val" id="order_amount_val" value="{{old('order_amount')}}">
                                            </td>
                                        </tr>
                                    </tbody></table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card shadow-sm">
                    <div class="card-body text-end">
                        <a href="{{route('dealer-purchase-order.list')}}" class="btn btn-sm btn-danger">Back</a>
                        <a href="{{route('dealer-purchase-order.add')}}" class="btn btn-sm btn-warning">Reset Form</a>
                        <a href="{{route('dealer-purchase-order.add', ['dealer_id'=>$dealer_id])}}" class="btn btn-sm btn-warning">Reset Items</a>
                        <button type="submit" id="submitBtn" class="btn btn-sm btn-success">Create </button>
                    </div>
                </div>     
                @endif          
                              
                                             
            </div>              
        </div>
                 
        </form>             
    </div>    
</section>
<script>
    var rowCount = $('#timePriceTable tbody tr').length;
    var proIdArr = [];
    $(document).ready(function(){  
        // alert(rowCount)
        if(rowCount == 1){
            $('#removeNew1').hide();
        }

        @if(old('details'))
        var order_amount = 0;
        $('.total_price').each(function(){
            if($(this).val() != ''){
                order_amount += parseFloat($(this).val());
            }
        });
        $('#order_amount_text').text(order_amount);
        $('#order_amount_val').val(order_amount);

        $('.productids').each(function(){ 
            if($(this).val() != ''){
                proIdArr.push($(this).val())
            }
        });
        @endif
        
        // console.log('order_amount:- '+order_amount);
        
    })

    $("#myForm").submit(function() {
        $('#submitBtn').attr('disabled', 'disabled');
        $('#submitBtn').html('<i class="fi fi-br-refresh"></i>').append('   Please wait ...');
        
        return true;
    });

    var i = 2;
    @if (old('details'))
        // {{count(old('details'))}}          
        @foreach($old_details as $key=>$details)
            var totalDetails = "{{$key}}";
        @endforeach        
        // var totalDetails = "{{count(old('details'))}}"; 
        totalDetails = parseInt(totalDetails)    
        console.log('totalDetails:- '+totalDetails);
        i = totalDetails+1;
    @endif

    console.log('index:- '+i);

    $(document).on('click','.addNewTime',function(){
        var thisClickedBtn = $(this);
        // alert(thisClickedBtn)        
        var toAppend = `
        <tr id="tr_`+i+`" class="tr_pro">
            <td class="f-12">
                <input type="text" autocomplete="off" class="form-control" id="product`+i+`" placeholder="Search product ... " onkeyup="getProducts(this.value,`+i+`,'fg');" name="details[`+i+`][product]" style="width: 350px">
                <input type="hidden" name="details[`+i+`][product_id]" id="product_id`+i+`" class="productids">
                <div class="respDrop" id="respDrop`+i+`"></div>
            </td>
            <td>
                <input type="number" min="1" value="1" oninput="this.value = Math.abs(this.value)"  class="form-control" id="quantity`+i+`" placeholder="" name="details[`+i+`][quantity]" onkeyup="calculatePrice(`+i+`)" onchange="calculatePrice(`+i+`)" style="width: 75px;">
            </td>  
            <td>                
                <div class="input-group ">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            Rs.
                        </div>
                    </div>
                    <input type="text" autocomplete="off" name="details[`+i+`][product_price]" class="form-control" id="product_price`+i+`" onkeyup="calculatePrice(`+i+`)">
                </div>
            </td>
            <td>                
                <div class="input-group ">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            Rs.
                        </div>
                    </div>
                    <input type="text" autocomplete="off" name="details[`+i+`][product_total_price]" class="form-control total_price" readonly id="product_total_price`+i+`" style="width: 200px;">
                </div>
            </td>           
            <td>
                <a class="btn btn-sm btn-success actionTimebtn addNewTime" id="addNew`+i+`">+</a>
                <a class="btn btn-sm btn-danger actionTimebtn removeTimePrice" id="removeNew`+i+`" onclick="removeRow(`+i+`)">X</a>
            </td>
        </tr>
        `;

        $('#timePriceTable tbody').append(toAppend);
        i++;
    });
    
    function removeRow(i){
        var count_tr_pro = $('.tr_pro').length;   
        if(count_tr_pro > 1){  
            var proId = $('#product_id'+i).val();                        
            proIdArr =  proIdArr.filter(e => e!=proId)
            // alert(proIdArr)           
            $('#tr_'+i).remove();
        }        
    }

    function getProducts(search,index,type){
        var dealer_id = "{{ $dealer_id }}";
        if(search.length > 0) {
            $.ajax({
                url: "{{ route('ajax.dealer-returnable-goods') }}",
                method: 'post',
                data: {
                    '_token': '{{ csrf_token() }}',
                    search: search,
                    idnotin: proIdArr,
                    dealer_id: dealer_id
                },
                success: function(result) {
                    console.log(result);
                    var content = '';
                    if (result.length > 0) {
                        content += `<div class="dropdown-menu show  product-dropdown select-md" aria-labelledby="dropdownMenuButton">`;

                        $.each(result, (key, value) => {                            
                            content += `<a class="dropdown-item" href="javascript: void(0)" onclick="fetchProduct('${index}',${value.product_id},${value.quantity},'${value.price}','${value.product.unique_id}')">${value.product.title}</a>`;
                        })
                        content += `</div>`;
                        // $($this).parent().after(content);
                    } else {
                        content += `<div class="dropdown-menu show  product-dropdown select-md" aria-labelledby="dropdownMenuButton"><li class="dropdown-item">No product found</li></div>`;
                    }
                    $('#respDrop'+index).html(content);
                }
            });
        } else {
            $('.product-dropdown').hide()
        }
        
    }

    function fetchProduct(count,id,maxquantity,price,unique_id) {
        $('.product-dropdown').hide()
        $.ajax({
            url: "{{ route('ajax.get-single-product') }}",
            method: 'post',
            data: {
                '_token': '{{ csrf_token() }}',
                id:id
            },
            success: function(result) {
                // console.log(result);
                var title = result.title;
                $('#product'+count).val(title);
                $('#product_id'+count).val(id); 
                $('#product_price'+count).val(price);
                $('#quantity'+count).val(maxquantity); 

                var quantity = $('#quantity'+count).val();                
                var product_total_price = (quantity*price);
                console.log('product_total_price:- '+product_total_price);
                $('#product_total_price'+count).val(product_total_price);

                var order_amount = 0;
                $('.total_price').each(function(){
                    if($(this).val() != ''){
                        order_amount += parseFloat($(this).val());
                    }
                });
                $('#order_amount_text').text(order_amount);
                $('#order_amount_val').val(order_amount);
                
                $('#removeNew'+count).show();                  
                proIdArr.push(id);                
            }
        }); 
        
    }


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
        var quantity = $('#quantity'+number).val();
        var product_price = $('#product_price'+number).val();

        console.log('quantity:- '+quantity)
        console.log('product_price:- '+product_price)
        
        var product_total_price = (quantity*product_price);
        console.log('product_total_price:- '+product_total_price);
        $('#product_total_price'+number).val(product_total_price);

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

</script>
@endsection