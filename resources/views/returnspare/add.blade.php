@extends('layouts.app')
@section('content')
@section('page', 'Create Return Sales For Goods & Sapre')
<section>   
    <ul class="breadcrumb_menu"> 
        <li>Returned Spare Management</li>
        <li><a href="{{ route('return-spares.list') }}">Returned Goods & Spares List</a> </li>
        <li>Create Return Goods & Spare Order</li>
    </ul>
    <div class="row">
        @if (!empty($service_partner_id) || !empty($dealer_id))
        <form id="myForm" action="{{ route('return-spares.save') }}" enctype="multipart/form-data" method="POST">
            @csrf
        @else
        <form id="myForm" action="" enctype="multipart/form-data" method="GET">
        @endif        
        <div class="row">
            <div class="col-sm-12">                
                <div class="card shadow-sm">                    
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="">Goods Type<span class="text-danger">*</span></label>
                                    <select name="goods_type" class="form-control"  id="goods_type" @if(!empty($goods_type)) disabled @endif>
                                            <option value="" hidden selected>Select an option</option>
                                            <option value="fg" @if($goods_type == 'fg') selected @endif>Finish Goods</option>
                                            <option value="sp" @if($goods_type == 'sp') selected @endif>Spare Parts</option>
                                    </select>
                            </div>
                        </div>                                                
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="">Return For<span class="text-danger">*</span></label>
                                    <select name="return_for" class="form-control"  id="return_for"  onchange="toggleReturnFor()"  @if(!empty($return_for)) disabled @endif>
                                            <option value="" hidden selected>Select an option</option>
                                            <option value="dealer" @if($return_for == 'dealer') selected @endif>Dealer</option>
                                            <option value="service_partner" @if($return_for == 'service_partner') selected @endif>Service Partner</option>
                                    </select>
                            </div>
                        </div>
                        <div class="col-md-6" style="display: none;" id="dealers">
                            <div class="form-group">
                                <label for="">Dealers <span class="text-danger">*</span></label>
                                <select name="dealer_id" class="form-control"  id="dealer_id"  >
                                    <option value="" hidden selected>Select an option</option>
                                    @forelse ($dealers as $d)
                                        <option value="{{$d->id}}" @if( $dealer_id == $d->id) selected @endif>{{ $d->name }}</option>
                                    @empty
                                        <option value="" disabled>No dealer found ...</option>
                                    @endforelse
                                </select>
                            </div>
                            @error('dealer_id') <p class="small text-danger">{{ $message }}</p> @enderror
                        </div>  
                        <div class="col-md-6" style="display: none;" id="service_partners">
                            <div class="form-group">
                                <label for="">Service Partner <span class="text-danger">*</span></label>
                                <select name="service_partner_id" class="form-control"  id="service_partner_id"  >
                                    <option value="" hidden selected>Select an option</option>
                                    @forelse ($service_partners as $sp)
                                        <option value="{{$sp->id}}" @if( $service_partner_id == $sp->id) selected @endif>{{ $sp->person_name }} - {{$sp->company_name}}</option>
                                    @empty
                                        <option value="" disabled>No service Partner found ...</option>
                                    @endforelse
                                </select>
                            </div>
                            @error('service_partner_id') <p class="small text-danger">{{ $message }}</p> @enderror
                        </div>

                        @if($return_for == 'dealer')
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Dealers <span class="text-danger">*</span></label>
                                <select name="dealer_id" class="form-control"  disabled>
                                    <option value="" hidden selected>Select an option</option>
                                    @forelse ($dealers as $d)
                                        <option value="{{$d->id}}" @if( $dealer_id == $d->id) selected @endif>{{ $d->name }}</option>
                                    @empty
                                        <option value="" disabled>No dealer found ...</option>
                                    @endforelse
                                </select>
                            </div>
                            @error('dealer_id') <p class="small text-danger">{{ $message }}</p> @enderror
                        </div>   
                        @elseif($return_for == 'service_partner')
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Service Partner <span class="text-danger">*</span></label>
                                <select name="service_partner_id" class="form-control" disabled>
                                    <option value="" hidden selected>Select an option</option>
                                    @forelse ($service_partners as $sp)
                                        <option value="{{$sp->id}}" @if( $service_partner_id == $sp->id) selected @endif>{{ $sp->person_name }} - {{$sp->company_name}}</option>
                                    @empty
                                        <option value="" disabled>No service Partner found ...</option>
                                    @endforelse
                                </select>
                            </div>
                            @error('service_partner_id') <p class="small text-danger">{{ $message }}</p> @enderror
                        </div>
                        @endif                            
                    </div>   
                    
                                       
                </div>

                @if (!empty($service_partner_id) || !empty($dealer_id))
                <div class="card shadow-sm">
                    <h6>Item Details</h6>
                    <div class="table-responsive order-addmore">
                        <table class="table" id="timePriceTable">
                            <thead>
                                <tr>
                                    <th>Product<span class="text-danger">*</span></th>
                                    <th>Quantity<span class="text-danger">*</span></th>
                                    <th>Last cost price (Inc. Profit  %)<span class="text-danger">*</span> </th>    
                                    <th>HSN Code<span class="text-danger">*</span></th>                        
                                    <th>Tax<span class="text-danger">*</span></th>
                                    <th>Total Price (Inc.Tax)</th>  
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
                                        <input type="text" class="form-control" id="product{{$key}}" onkeyup="getProducts(this.value,{{$key}},'{{$goods_type}}');" placeholder="Search product ... " name="details[{{$key}}][product]" value="{{ old('details.'.$key.'.product') }}" style="width: 350px" autocomplete="off">
                                        <input type="hidden" name="details[{{$key}}][product_id]" id="product_id{{$key}}" class="productids" value="{{ old('details.'.$key.'.product_id') }}">
                                        <input type="hidden" name="details[{{$key}}][product_unique_id]" id="product_unique_id{{$key}}" value="{{ old('details.'.$key.'.product_id') }}">
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
                                            <input type="text" name="details[{{$key}}][product_price]" class="form-control" id="product_price{{$key}}" onkeyup="calculatePrice({{$key}})" value="{{ old('details.'.$key.'.product_price') }}" autocomplete="off">
                                        </div>
                                        @error('details.'.$key.'.product_price') <p class="small text-danger">{{ $message }}</p> @enderror
                                    </td>
                                    <td>
                                        <input type="text" name="details[{{$key}}][hsn_code]" maxlength="6" class="form-control"  id="hsn_code{{$key}}" value="{{ old('details.'.$key.'.hsn_code') }}" style="90px;" autocomplete="off">
                                        @error('details.'.$key.'.hsn_code') <p class="small text-danger">{{ $message }}</p> @enderror
                                    </td>                        
                                    <td>
                                        <div class="input-group ">
                                            <input type="text" class="form-control" name="details[{{$key}}][tax]" id="tax{{$key}}" value="{{ old('details.'.$key.'.tax') }}" maxlength="2" style="width: 80px;" autocomplete="off">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    %
                                                </div>
                                            </div>
                                        </div>
                                        @error('details.'.$key.'.tax') <p class="small text-danger">{{ $message }}</p> @enderror
                                    </td>    
                                    <td>                                    
                                        <div class="input-group ">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    Rs.
                                                </div>
                                            </div>
                                            <input type="text" name="details[{{$key}}][product_total_price]" class="form-control total_price" readonly id="product_total_price{{$key}}" value="{{ old('details.'.$key.'.product_total_price') }}" style="width:200px">
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
                                        <input type="text" class="form-control" id="product1" onkeyup="getProducts(this.value,1,'{{$goods_type}}');" placeholder="Search product ... " name="details[1][product]" style="width: 350px" autocomplete="off">
                                        <input type="hidden" name="details[1][product_id]" id="product_id1" class="productids">
                                        <input type="hidden" name="details[1][product_unique_id]" id="product_unique_id1" >
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
                                            <input type="text" name="details[1][product_price]" class="form-control" id="product_price1"  onkeyup="calculatePrice(1)" autocomplete="off">
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" name="details[1][hsn_code]" maxlength="6" class="form-control"  id="hsn_code1" style="width: 90px;" autocomplete="off">
                                    </td>                        
                                    <td>
                                        <div class="input-group ">
                                            <input type="text" class="form-control" name="details[1][tax]" id="tax1" value="" maxlength="2" style="width: 80px;" autocomplete="off">
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
                                    <h6 class="text-muted mb-2">Total Amount (Inc.Tax)</h6>
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
                        <a href="{{route('return-spares.list')}}" class="btn btn-sm btn-danger">Back</a>
                        <a href="{{route('return-spares.add')}}" class="btn btn-sm btn-warning">Reset Form</a>
                        <a href="{{route('return-spares.add')}}?goods_type={{$goods_type}}&return_for={{$return_for}}&dealer_id={{$dealer_id}}&service_partner_id={{$service_partner_id}}" class="btn btn-sm btn-warning">Reset Items</a>
                        <button type="submit" id="submitBtn" class="btn btn-sm btn-success">Create </button>
                    </div>
                </div>  
                
                @else
                <div class="card shadow-sm">
                    <div class="card-body text-end">
                        <a href="{{route('return-spares.list')}}" class="btn btn-sm btn-danger">Back</a>
                        <button id="next" type="submit" class="btn btn-sm btn-success" style="display: none;" >Next </button>
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
        var goods_type = $('#goods_type').val();
        var thisClickedBtn = $(this);
        // alert(thisClickedBtn)        
        var toAppend = `
        <tr id="tr_`+i+`" class="tr_pro">
            <td class="f-12">
                <input type="text" class="form-control" id="product`+i+`" placeholder="Search product ... " onkeyup="getProducts(this.value,`+i+`,'${goods_type}');" name="details[`+i+`][product]" style="width: 350px" autocomplete="off">
                <input type="hidden" name="details[`+i+`][product_id]" id="product_id`+i+`" class="productids">
                <input type="hidden" name="details[`+i+`][product_unique_id]" id="product_unique_id`+i+`" >
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
                    <input type="text" name="details[`+i+`][product_price]" class="form-control" id="product_price`+i+`" onkeyup="calculatePrice(`+i+`)" autocomplete="off">
                </div>
            </td>
            <td>
                <input type="text" name="details[`+i+`][hsn_code]" maxlength="6" class="form-control"  id="hsn_code`+i+`" style="width: 90px;" autocomplete="off">
            </td>                        
            <td>
                <div class="input-group ">
                    <input type="text" class="form-control" name="details[`+i+`][tax]" id="tax`+i+`" value="" maxlength="2" style="width: 80px;" autocomplete="off">
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
                    <input type="text" name="details[`+i+`][product_total_price]" class="form-control total_price" readonly id="product_total_price`+i+`" style="width: 200px;">
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

            // Recalculate the total order amount after removing the row
            recalculateOrderAmount();
        }        
    }

    function getProducts(search,index,type){
        var service_partner_id = $('#service_partner_id').val();
        var dealer_id = $('#dealer_id').val();
        if(search.length > 0) {
            $.ajax({
                //url: "{{ route('ajax.servicepartner-returnable-spares') }}",
                url: "{{ route('ajax.search-product-by-type') }}",
                method: 'post',
                data: {
                    '_token': '{{ csrf_token() }}',
                    search: search,
                    type: type,
                    service_partner_id: service_partner_id,
                    dealer_id: dealer_id,
                    idnotin: proIdArr
                },
                success: function(result) {
                    console.log(result); 
                    // alert(result);
                    var content = '';
                    if (result.length > 0) {
                        content += `<div class="dropdown-menu show  product-dropdown select-md" aria-labelledby="dropdownMenuButton">`;

                        $.each(result, (key, value) => {                            
                            // content += `<a class="dropdown-item" href="javascript: void(0)" onclick="fetchProduct('${index}',${value.product_id},${value.quantity},'${value.price}','${value.product.unique_id}','${value.hsn_code}','${value.tax}')">${value.product.title}</a>`;


                            content += `<a class="dropdown-item" href="javascript: void(0)" onclick="fetchProduct('${index}',${value.id},1,'${value.mop}','${value.unique_id}','${value.hsn_code}','${value.gst}')">${value.title}</a>`
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

    function fetchProduct(count,id,maxquantity,price,unique_id,hsn_code,tax) {
        $('.product-dropdown').hide()
        $.ajax({
            url: "{{ route('ajax.get-single-product') }}",
            method: 'post',
            data: {
                '_token': '{{ csrf_token() }}',
                id:id
            },
            success: function(result) {
                // console.warn(result);
                var title = result.title;
                var mop = result.mop;
                var last_po_cost_price = result.last_po_cost_price;
                if(!last_po_cost_price || last_po_cost_price <= 0){
                    alert('This product does not have a valid Last Cost Price in our records!');
                    $('#product_price'+count).addClass('is-invalid');
                    // $('#submitBtn').prop('disabled', true);
                    return;
                }
                var profit_percentage = result.profit_percentage;
                if(!profit_percentage || profit_percentage <= 0){
                    alert('This product does not have a profit percentage(%) in our records!');
                }
                var price = last_po_cost_price * (1 + (profit_percentage / 100));
                $('#product'+count).val(title);
                $('#product_id'+count).val(id); 
                $('#hsn_code'+count).val(hsn_code); 
                $('#product_price'+count).val(price);
                $('#tax'+count).val(tax);  
                $('#quantity'+count).val(maxquantity);
                $('#product_unique_id'+count).val(unique_id);
                // $('#quantity'+count).attr('max', maxquantity);

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

    function recalculateOrderAmount() {
        var order_amount = 0;

        // Sum up the total prices from all remaining rows
        $('.total_price').each(function () {
            if ($(this).val() !== '') {
                order_amount += parseFloat($(this).val());
            }
        });

        // Update the displayed total amount
        $('#order_amount_text').text(order_amount.toFixed(2)); // Use 2 decimal places
        $('#order_amount_val').val(order_amount.toFixed(2));

        console.log('Updated order_amount:', order_amount);
    }



    function toggleReturnFor() {
        const returnFor = document.getElementById('return_for').value;
        const dealersDiv = document.getElementById('dealers');
        const servicePartnersDiv = document.getElementById('service_partners');
        const dealerField = document.getElementById('dealer_id');
        const servicePartnerField = document.getElementById('service_partner_id');
        // Reset required attribute for both fields
        dealerField.removeAttribute('required');
        servicePartnerField.removeAttribute('required');
        // Toggle visibility based on selection
        if (returnFor === 'dealer') {
            dealersDiv.style.display = 'block';
            servicePartnersDiv.style.display = 'none';
            dealerField.setAttribute('required', 'required');
            document.getElementById('next').style.display ='inline-block';
        } else if (returnFor === 'service_partner') {
            dealersDiv.style.display = 'none';
            servicePartnersDiv.style.display = 'block';
            servicePartnerField.setAttribute('required', 'required');
            document.getElementById('next').style.display ='inline-block';
        } else {
            dealersDiv.style.display = 'none';
            servicePartnersDiv.style.display = 'none';
            document.getElementById('next').style.display ='none';
        }
    }

</script>
@endsection