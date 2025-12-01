@extends('layouts.app')
@section('content')
@section('page', 'Create PO')
<section>   
    <ul class="breadcrumb_menu">       
        <li>Purchase Order</li> 
        <li><a href="{{ route('purchase-order.list', ['po_type'=>'po']) }}">PO</a> </li>
        <li>Create</li>
    </ul>
    <div class="row">
        @if (!empty($supplier_id) && !empty($type))
        <form id="myForm" action="{{ route('purchase-order.store') }}" enctype="multipart/form-data" method="POST">
            @csrf
        @else
        <form id="myForm" action="{{ route('purchase-order.add') }}" enctype="multipart/form-data" method="GET">        
        @endif
        
        
        <div class="row">
            <div class="col-sm-12">     
                <div class="card shadow-sm">
                    @if (!empty($supplier_id) && !empty($type))
                    <div class="row">                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Supplier <span class="text-danger">*</span></label>
                                <select name="supplier_id" class="form-control"  id="supplier_id" disabled >
                                    <option value="" hidden selected>Select an option</option>
                                    @forelse ($supplier as $s)
                                        <option value="{{$s->id}}" @if($supplier_id == $s->id) selected @endif>
                                            {{$s->public_name}} 
                                        </option>
                                    @empty
                                        <option value="" disabled>No supplier found ...</option>
                                    @endforelse
                                </select>
                                <input type="hidden" name="supplier_id" value="{{$supplier_id}}">
                            </div>
                        </div> 
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Type <span class="text-danger">*</span></label>
                                <select name="type" class="form-control" id="" disabled>
                                    <option value="" hidden selected>Select an option</option>
                                    <option value="fg" @if($type == 'fg') selected @endif>Finished Goods</option>
                                    <option value="sp" @if($type == 'sp') selected @endif>Spare Parts</option>
                                </select>
                                <input type="hidden" name="type" id="type" value="{{$type}}">
                            </div>
                        </div> 
                                                                        
                    </div>   
                    @else
                    <div class="row">                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Supplier <span class="text-danger">*</span></label>
                                <select name="supplier_id" class="form-control"  id="supplier_id" >
                                    <option value="" hidden selected>Select an option</option>
                                    @forelse ($supplier as $s)
                                        <option value="{{$s->id}}">
                                            {{$s->public_name}} 
                                        </option>
                                    @empty
                                        <option value="" disabled>No supplier found ...</option>
                                    @endforelse
                                </select>
                                @error('supplier_id') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div> 
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Type <span class="text-danger">*</span></label>
                                <select name="type" class="form-control" id="">
                                    <option value="" hidden selected>Select an option</option>
                                    <option value="fg">Finished Goods</option>
                                    <option value="sp">Spare Parts</option>
                                </select>  
                                @error('type') <p class="small text-danger">{{ $message }}</p> @enderror                              
                            </div>
                        </div>                                      
                    </div>   
                    @endif                                       
                </div>                
                @if (empty($type) || empty($supplier_id))
                <div class="card shadow-sm">
                    <div class="card-body text-end">
                        <a href="{{route('purchase-order.list', ['po_type'=>'po'])}}" class="btn btn-sm btn-danger">Back</a>
                        <button type="submit" class="btn btn-sm btn-success">Next </button>
                    </div>
                </div>  
                @else
                <div class="card shadow-sm">
                    <h6>Item Details</h6> 
                    <div class="table-responsive order-addmore">
                        <table class="table" id="timePriceTable">
                            <thead>
                                <tr>
                                    <th>Product<span class="text-danger">*</span></th>
                                    <th> MOP </th>  
                                    @if ($type == 'sp')
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
                                @if(old('details'))
                                @php
                                    $old_details = old('details');
                                @endphp
                                @foreach ($old_details as $key=>$details)
                                <tr id="tr_{{$key}}" class="tr_pro">
                                    <td class="f-12">
                                        <input type="text" autocomplete="off" class="form-control" id="product{{$key}}" onkeyup="getProducts(this.value,{{$key}},'{{$type}}');" placeholder="Search product ... " name="details[{{$key}}][product]" value="{{ old('details.'.$key.'.product') }}" style="width: 200px">
                                        <input type="hidden" name="details[{{$key}}][product_id]" id="product_id{{$key}}" class="productids" value="{{ old('details.'.$key.'.product_id') }}">
                                        <input type="hidden" name="details[{{$key}}][product_unique_id]" id="product_unique_id{{$key}}" value="{{ old('details.'.$key.'.product_id') }}">
                                        <div class="respDrop" id="respDrop{{$key}}"></div>
                                        @error('details.'.$key.'.product_id') <p class="small text-danger">{{ $message }}</p> @enderror
                                    </td>
                                    <td>
                                        <div class="input-group ">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    Rs.
                                                </div>
                                            </div>
                                            <input type="text" autocomplete="off" name="details[{{$key}}][mop]" class="form-control" readonly id="mop{{$key}}" value="{{ old('details.'.$key.'.mop') }}" style="width: 90px;">
                                        </div>
                                    </td>
                                    @if ($type == 'sp')
                                    <td>                                    
                                        <input type="number" min="1" oninput="this.value = Math.abs(this.value)" class="form-control" id="pack_of{{$key}}" placeholder="" name="details[{{$key}}][pack_of]"  value="{{ old('details.'.$key.'.pack_of') }}" onkeyup="calculatePrice({{$key}})" onchange="calculatePrice({{$key}})">
                                        @error('details.'.$key.'.pack_of') <p class="small text-danger">{{ $message }}</p> @enderror
                                    </td>
                                    <td>                                    
                                        <input type="number" min="1" oninput="this.value = Math.abs(this.value)" class="form-control" id="quantity_in_pack{{$key}}" placeholder="" name="details[{{$key}}][quantity_in_pack]"  value="{{ old('details.'.$key.'.quantity_in_pack') }}" onkeyup="calculatePrice({{$key}})" onchange="calculatePrice({{$key}})">
                                        @error('details.'.$key.'.quantity_in_pack') <p class="small text-danger">{{ $message }}</p> @enderror
                                    </td>
                                    @endif                                
                                    <td>
                                        @if ($type == 'fg')
                                            <input type="number" min="1" oninput="this.value = Math.abs(this.value)" class="form-control" id="quantity{{$key}}" placeholder="" name="details[{{$key}}][quantity]" onkeyup="calculatePrice({{$key}})" onchange="calculatePrice({{$key}})"  value="{{ old('details.'.$key.'.quantity') }}">
                                            @error('details.'.$key.'.quantity') <p class="small text-danger">{{ $message }}</p> @enderror
                                        @elseif ($type == 'sp')                                        
                                            <input type="number" min="1" oninput="this.value = Math.abs(this.value)" class="form-control" id="quantity{{$key}}" placeholder="" name="details[{{$key}}][quantity]"  value="{{ old('details.'.$key.'.quantity') }}" readonly>
                                            @error('details.'.$key.'.quantity') <p class="small text-danger">{{ $message }}</p> @enderror
                                        @endif                                    
                                    </td> 
                                    <td>
                                        <div class="input-group ">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    Rs.
                                                </div>
                                            </div>
                                            <input type="text" autocomplete="off" onkeypress="validateNum(event)" class="form-control" id="cost_price{{$key}}" placeholder="" name="details[{{$key}}][cost_price]" onkeyup="calculatePrice({{$key}})" value="{{ old('details.'.$key.'.cost_price') }}">
                                        </div>
                                        @error('details.'.$key.'.cost_price') <p class="small text-danger">{{ $message }}</p> @enderror
                                    </td>                                
                                    <td>
                                        <input type="text" autocomplete="off" name="details[{{$key}}][hsn_code]" class="form-control" id="hsn_code{{$key}}" maxlength="20" value="{{ old('details.'.$key.'.hsn_code') }}" style="width: 90px;">
                                        @error('details.'.$key.'.hsn_code') <p class="small text-danger">{{ $message }}</p> @enderror
                                    </td>                                
                                    <td>
                                        <div class="input-group ">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    Rs.
                                                </div>
                                            </div>
                                            <input type="text" autocomplete="off" onkeypress="validateNum(event)" class="form-control" id="mrp{{$key}}" placeholder="" name="details[{{$key}}][mrp]" value="{{ old('details.'.$key.'.mrp') }}" style="width: 90px;">
                                        </div>
                                        @error('details.'.$key.'.mrp') <p class="small text-danger">{{ $message }}</p> @enderror
                                    </td>
                                    <td>
                                        <div class="input-group ">
                                            <input type="text" autocomplete="off" onkeypress="validateNum(event)" class="form-control" id="tax{{$key}}" placeholder="" name="details[{{$key}}][tax]" value="{{ old('details.'.$key.'.tax') }}" maxlength="2" style="width:90px;">
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
                                            <input type="text" autocomplete="off" name="details[{{$key}}][total_price]" class="form-control total_price" id="total_price{{$key}}" value="{{ old('details.'.$key.'.total_price') }}" readonly style="width: 90px;">
                                        </div>
                                        @error('details.'.$key.'.total_price') <p class="small text-danger">{{ $message }}</p> @enderror
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
                                        <input type="text" autocomplete="off" class="form-control" id="product1" onkeyup="getProducts(this.value,1,'{{$type}}');" placeholder="Search product ... " name="details[1][product]" style="width: 200px">
                                        <input type="hidden" name="details[1][product_id]" id="product_id1" class="productids">
                                        <input type="hidden" name="details[1][product_unique_id]" id="product_unique_id1" >
                                        <div class="respDrop" id="respDrop1"></div>
                                    </td>
                                    <td>
                                        <div class="input-group ">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    Rs.
                                                </div>
                                            </div>
                                            <input type="text" autocomplete="off" name="details[1][mop]" class="form-control" readonly id="mop1" style="width: 90px;">
                                        </div>
                                    </td>
                                    @if ($type == 'sp')
                                    <td>
                                        <input type="number" name="details[1][pack_of]" class="form-control" min="1" oninput="this.value = Math.abs(this.value)" class="form-control" id="pack_of1" value="1" onkeyup="calculatePrice(1)" onchange="calculatePrice(1)">
                                    </td>
                                    <td>
                                        <input type="number" name="details[1][quantity_in_pack]" id="quantity_in_pack1" class="form-control" min="1" oninput="this.value = Math.abs(this.value)" value="1" onkeyup="calculatePrice(1)" onchange="calculatePrice(1)" >
                                    </td>
                                    @endif                                
                                    <td>
                                        @if ($type == 'fg')
                                        <input type="number" min="1" oninput="this.value = Math.abs(this.value)" class="form-control" id="quantity1" name="details[1][quantity]" onkeyup="calculatePrice(1)" onchange="calculatePrice(1)" value="1">
                                        @elseif ($type == 'sp')
                                        <input type="number" class="form-control" id="quantity1" readonly name="details[1][quantity]" value="1">
                                        @endif                                    
                                    </td>  
                                    <td>                                    
                                        <div class="input-group ">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    Rs.
                                                </div>
                                            </div>
                                            <input type="text" autocomplete="off" onkeypress="validateNum(event)" class="form-control" id="cost_price1" placeholder="" name="details[1][cost_price]" onkeyup="calculatePrice(1)">
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" autocomplete="off" name="details[1][hsn_code]" class="form-control" id="hsn_code1" maxlength="20" style="width: 90px;">
                                    </td>
                                    <td>                                    
                                        <div class="input-group ">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    Rs.
                                                </div>
                                            </div>
                                            <input type="text" autocomplete="off" onkeypress="validateNum(event)" class="form-control" id="mrp1" placeholder="" name="details[1][mrp]" style="width: 90px;">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group ">
                                            <input type="text" autocomplete="off" onkeypress="validateNum(event)" class="form-control" id="tax1" placeholder="" name="details[1][tax]" maxlength="2" style="width:90px;">
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
                                            <input type="text" autocomplete="off" name="details[1][total_price]" class="form-control total_price" id="total_price1" readonly style="width: 90px;">
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
                            <div class="row mb-3 justify-content-end">
                                <div class="col-md-8">
                                    <h6 class="text-muted mb-2">Total Amount (Inc.Tax)</h6>
                                </div>
                                <div class="col-md-4 text-end">
                                    <table class="w-100">            
                                        <tbody>
                                            <tr class="border-top">
                                                <td>
                                                    <h6 class="text-dark mb-0 text-end"> Rs. <span id="order_amount_text">0</span></h6>
                                                    <input type="hidden" name="order_amount_val" id="order_amount_val" value="{{old('order_amount')}}">
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
                        <a href="{{route('purchase-order.list', ['po_type'=>'po'])}}" class="btn btn-sm btn-danger">Back</a>
                        <a href="{{route('purchase-order.add')}}" class="btn btn-sm btn-warning">Reset Form</a>
                        <a href="{{route('purchase-order.add', ['supplier_id'=>$supplier_id,'type'=>$type])}}" class="btn btn-sm btn-warning">Reset Items</a>
                        <button type="submit" id="submitBtn" class="btn btn-sm btn-success">Create </button>
                        <p class="small filter-waiting-text"></p>
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

        // alert(proIdArr)
        
    })

    $("#myForm").submit(function() {
        $('#submitBtn').attr('disabled', 'disabled');
        $('#submitBtn').html('<i class="fi fi-br-refresh"></i>').append('   Please wait ...');
        $('.filter-waiting-text').text('Please Wait ... This Process Will Take A Few Minutes .');
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
        var type = $('#type').val();        
        var toAppend = ``;     
        if(type == 'fg'){
            toAppend += `
            <tr id="tr_`+i+`" class="tr_pro">
                <td class="f-12">
                    <input type="text" autocomplete="off" class="form-control" id="product`+i+`" placeholder="Search product ... " onkeyup="getProducts(this.value,`+i+`,'{{$type}}');" name="details[`+i+`][product]" style="width: 200px">
                    <input type="hidden" name="details[`+i+`][product_id]" id="product_id`+i+`" class="productids">
                    <input type="hidden" name="details[`+i+`][product_unique_id]" id="product_unique_id`+i+`" >
                    <div class="respDrop" id="respDrop`+i+`"></div>
                </td>
                <td>                
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                Rs.
                            </div>
                        </div>
                        <input type="text" autocomplete="off" name="details[`+i+`][mop]" class="form-control" readonly id="mop`+i+`" style="width: 90px;">
                    </div>
                </td>
                <td>
                    <input type="number" min="1" value="1" oninput="this.value = Math.abs(this.value)"  class="form-control" id="quantity`+i+`" placeholder="" name="details[`+i+`][quantity]" onkeyup="calculatePrice(`+i+`)" onchange="calculatePrice(`+i+`)">
                </td>  
                <td>                
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                Rs.
                            </div>
                        </div>
                        <input type="text" autocomplete="off" onkeypress="validateNum(event)" class="form-control" id="cost_price`+i+`" placeholder="" name="details[`+i+`][cost_price]" onkeyup="calculatePrice(`+i+`)">
                    </div>
                </td>
                <td>
                    <input type="text" autocomplete="off" name="details[`+i+`][hsn_code]" class="form-control" id="hsn_code`+i+`" maxlength="20" style="width: 90px;">
                </td>
                <td>                
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                Rs.
                            </div>
                        </div>
                        <input type="text" autocomplete="off" onkeypress="validateNum(event)" class="form-control" id="mrp`+i+`" placeholder="" name="details[`+i+`][mrp]" style="width: 90px;">
                    </div>
                </td>
                <td>
                    <div class="input-group">
                        <input type="text" autocomplete="off" class="form-control" name="details[`+i+`][tax]" id="tax`+i+`" value="" maxlength="2" style="width:90px;">
                        <div class="input-group-prepend">
                            <div class="input-group-text" >
                                %
                            </div>
                        </div>
                    </div>
                </td>
                <td>                
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                Rs.
                            </div>
                        </div>
                        <input type="text" autocomplete="off" name="details[`+i+`][total_price]" class="form-control total_price" id="total_price`+i+`" readonly style="width: 90px;">
                    </div>
                </td>
                <td>
                    <a class="btn btn-sm btn-success actionTimebtn addNewTime" id="addNew`+i+`">+</a>
                    <a class="btn btn-sm btn-danger actionTimebtn removeTimePrice" id="removeNew`+i+`" onclick="removeRow(`+i+`)">X</a>
                </td>
            </tr>
            `;
        }  else if (type == 'sp'){
            toAppend += `
            <tr id="tr_`+i+`" class="tr_pro">
                <td class="f-12">
                    <input type="text" autocomplete="off" class="form-control" id="product`+i+`" placeholder="Search product ... " onkeyup="getProducts(this.value,`+i+`,'{{$type}}');" name="details[`+i+`][product]">
                    <input type="hidden" name="details[`+i+`][product_id]" id="product_id`+i+`" class="productids">
                    <input type="hidden" name="details[`+i+`][product_unique_id]" id="product_unique_id`+i+`" >
                    <div class="respDrop" id="respDrop`+i+`"></div>
                </td>
                <td>                
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                Rs.
                            </div>
                        </div>
                        <input type="text" autocomplete="off" name="details[`+i+`][mop]" class="form-control" readonly id="mop`+i+`" style="width: 70px;">
                    </div>
                </td>
                <td>
                    <input type="number" name="details[`+i+`][pack_of]" class="form-control" min="1" oninput="this.value = Math.abs(this.value)" id="pack_of`+i+`" value="1" onkeyup="calculatePrice(`+i+`)" onchange="calculatePrice(`+i+`)">
                </td>
                <td>
                    <input type="number" name="details[`+i+`][quantity_in_pack]" id="quantity_in_pack`+i+`" class="form-control" min="1" oninput="this.value = Math.abs(this.value)" value="1" onkeyup="calculatePrice(`+i+`)" onchange="calculatePrice(`+i+`)">
                </td>  
                <td>
                    <input type="number" class="form-control" id="quantity`+i+`" readonly name="details[`+i+`][quantity]" value="1">
                </td>
                <td>                
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                Rs.
                            </div>
                        </div>
                        <input type="text" autocomplete="off" onkeypress="validateNum(event)" class="form-control" id="cost_price`+i+`" placeholder="" name="details[`+i+`][cost_price]" onkeyup="calculatePrice(`+i+`)">
                    </div>
                </td>
                <td>
                    <input type="text" autocomplete="off" name="details[`+i+`][hsn_code]" class="form-control" id="hsn_code`+i+`" maxlength="20" style="width: 90px;">
                </td>
                <td>                
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                Rs.
                            </div>
                        </div>
                        <input type="text" onkeypress="validateNum(event)" class="form-control" id="mrp`+i+`" placeholder="" name="details[`+i+`][mrp]">
                    </div>
                </td>
                <td>
                    <div class="input-group mb-3">
                        <input type="text" autocomplete="off" class="form-control" name="details[`+i+`][tax]" id="tax`+i+`" value="" maxlength="2" style="width:90px;">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                %
                            </div>
                        </div>
                    </div>
                </td>
                <td>                
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                Rs.
                            </div>
                        </div>
                        <input type="text" name="details[`+i+`][total_price]" class="form-control total_price" id="total_price`+i+`" readonly>
                    </div>
                </td>
                <td>
                    <a class="btn btn-sm btn-success actionTimebtn addNewTime" id="addNew`+i+`">+</a>
                    <a class="btn btn-sm btn-danger actionTimebtn removeTimePrice" id="removeNew`+i+`" onclick="removeRow(`+i+`)">X</a>
                </td>
            </tr>
            `;
        }
        

        $('#timePriceTable tbody').append(toAppend);
        i++;
    });
    
    function removeRow(i){
        var count_tr_pro = $('.tr_pro').length; 
        console.log(count_tr_pro);  
        if(count_tr_pro > 1){    
            var proId = $('#product_id'+i).val();                        
            proIdArr =  proIdArr.filter(e => e!=proId)
            // alert(proIdArr)        
            $('#tr_'+i).remove();
        }        
    }

    function getProducts(search,index,type){
        if(search.length > 0) {
            $.ajax({
                url: "{{ route('ajax.search-product-by-type') }}",
                method: 'post',
                data: {
                    '_token': '{{ csrf_token() }}',
                    search: search,
                    type: type,
                    idnotin: proIdArr
                },
                success: function(result) {
                    console.log(result);
                    var content = '';
                    if (result.length > 0) {
                        content += `<div class="dropdown-menu show  product-dropdown select-md" aria-labelledby="dropdownMenuButton">`;

                        $.each(result, (key, value) => {                            
                            content += `<a class="dropdown-item" href="javascript: void(0)" onclick="fetchProduct('${index}',${value.id})">${value.title}</a>`;
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

    function fetchProduct(count,id) {
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
                var unique_id = result.unique_id;
                var mop = result.mop;
                var hsn_code = result.hsn_code;

                $('#product'+count).val(title);
                $('#product_unique_id'+count).val(unique_id);
                $('#product_id'+count).val(id);
                $('#mop'+count).val(mop);
                $('#hsn_code'+count).val(hsn_code);
                    
                $('#removeNew'+count).show();  
                proIdArr.push(id);
                // alert(proIdArr) 
                
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

        var type = $('#type').val();
        if(type == 'fg'){
            var quantity = $('#quantity'+number).val();
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
        } else {
            // alert('Hi')
            var pack_of = $('#pack_of'+number).val();
            var quantity_in_pack = $('#quantity_in_pack'+number).val();

            var quantity = (quantity_in_pack * pack_of);
            $('#quantity'+number).val(quantity);
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