@extends('layouts.app')
@section('content')
@section('page', 'Product')
@if ($data->type == 'fg')
    @section('small', '(Goods)')
@else
    @section('small', '(Spares)')
@endif

<section>   
    <ul class="breadcrumb_menu">   
        <li>Product Management</li>        
        <li><a href="{{ route('product.list') }}?{{$getQueryString}}">Product</a> </li>
        <li>Update</li>
    </ul>
    <ul class="breadcrumb_menu">   
        <li><a href="{{ route('product.list') }}?{{$getQueryString}}"><i class="fi-br-arrow-alt-circle-left"></i> Back To Product</a></li>   
    </ul>
    @if($errors->any())
    {{ implode('', $errors->all('<div>:message</div>')) }}
    @endif
    <div class="row">
        <form id="myForm" action="{{ route('product.update',[$idStr,$getQueryString]) }}" enctype="multipart/form-data" method="POST">
            @csrf
            <input type="hidden" name="browser_name" id="browser_name">
            <input type="hidden" name="navigator_useragent" id="navigator_useragent">

        <div class="row">
            <div class="col-sm-12">            
                <div class="card shadow-sm">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">ID <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" value="{{ $data->unique_id }}" readonly disabled>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <input type="hidden" name="type" value="{{ $data->type }}">
                                <label for="">Type <span class="text-danger">*</span></label>
                                <select name="" disabled class="form-control" id="">
                                    <option value="" hidden selected>Select an option</option>
                                    <option value="fg" @if($data->type == 'fg') selected @endif>Finished Goods</option>
                                    <option value="sp" @if($data->type == 'sp') selected @endif>Spare Parts</option>
                                </select>
                                @error('type') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div> 
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Category (Class) <span class="text-danger">*</span></label>
                                <select name="cat_id" class="form-control" id="cat_id" >
                                    <option value="" hidden selected>Select an option</option>
                                    @forelse ($category as $item)
                                    <option value="{{$item->id}}" @if($data->cat_id == $item->id) selected @endif>{{$item->name}}</option>
                                    @empty                                       
                                    <option value="" disabled>No category available ... </option> 
                                    @endforelse
                                </select>
                                @error('cat_id') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-3" id="subcat_div">
                            <div class="form-group">
                                <label for="">Subcategory (Group) </label>
                                <input type="hidden" name="subcat_name" value="{{old('subcat_name')}}" id="subcat_name">
                                <select name="subcat_id" class="form-control" id="subcat_id" >
                                    @forelse ($subcategory as $item)
                                    <option value="{{$item->id}}" @if($data->subcat_id == $item->id) selected @endif>{{$item->name}}</option>
                                    @empty                                       
                                    <option value="" disabled>No subcategory available ... </option> 
                                    @endforelse
                                </select>
                                @error('subcat_id') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>                                                
                    </div>   
                </div>                   
                </div>
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row"> 
                            <div class="col-md-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" id="is_test_product" name="is_test_product" title="Set As Test Product" @if($data->is_test_product == 1) checked @endif>
                                    <label class="form-check-label" for="is_test_product">
                                        Test Product
                                    </label>
                                </div>
                            </div>                         
                            <div class="col-md-10">
                                <div class="form-group">
                                    <label for="">Title <span class="text-danger">*</span></label>
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control" name="title" id="name" placeholder="Enter title" value="{{ $data->title }}" maxlength="100">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text" style="height: 37px;">
                                                <input type="checkbox" name="is_title_public_name_same" value="1" id="flexCheckDefault" title="Check same as product title and public name" @if(!empty($data->is_title_public_name_same)) checked @endif >
                                                <label for="flexCheckDefault" style="padding-left: 10px;">Set As Public Name </label>
                                            </div>
                                        </div>
                                    </div>
                                    @error('title') <p class="small text-danger">{{ $message }}</p> @enderror
                                </div>
                            </div>                         
                        </div>    
                    </div>                  
                </div>
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-9">
                                <div class="form-group">
                                    <label for="">Public Name <span class="text-danger">*</span></label>
                                    <input type="text" name="public_name" id="public_name" placeholder="Please Enter Public Name" class="form-control" maxlength="100" value="{{$data->public_name}}">
                                    @error('public_name') <p class="small text-danger">{{ $message }}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">HSN Code </label>
                                    <input type="text" name="hsn_code" id="hsn_code" placeholder="Please Enter Public Name" class="form-control" maxlength="100" value="{{$data->hsn_code}}">
                                    @error('hsn_code') <p class="small text-danger">{{ $message }}</p> @enderror
                                </div>
                            </div> 
                        </div>
                    </div>
                </div>
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-9">
                                <div class="form-group">
                                    <label for="">Description </label>
                                    <textarea name="description" class="form-control" id="" cols="3" rows="3">{{$data->description}}</textarea>
                                    
                                </div>                            
                            </div> 
                            <div class="col-md-3 div_mop">
                                <div class="form-group">
                                    <label for="">MOP </label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text" style="height: 34px;">
                                                Rs.
                                            </div>
                                        </div>
                                        <input type="text" class="form-control" name="mop" id="mop" placeholder="Enter Market Operating Price" value="{{ $data->mop }}" onkeypress="validateNum(event)" maxlength="8">
                                    </div>
                                    @error('mop') <p class="small text-danger">{{ $message }}</p> @enderror
                                </div>
                            </div> 
                        </div>
                    </div>
                </div>
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row">                            
                            <div class="col-md-3 div_goods_type">
                                <div class="form-group">
                                    <label for="goods_type">Goods Type <span class="text-danger">*</span></label>
                                    <select name="goods_type" class="form-control" id="goods_type">
                                        <option value="general" @if($data->goods_type == 'general') selected @endif>General</option>
                                        <option value="chimney" @if($data->goods_type == 'chimney') selected @endif>Chimney</option>
                                    </select>
                                    @error('goods_type') <p class="small text-danger">{{ $message }}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-3 div_spare_spec1" id="">
                                <div class="form-group">
                                    <label for="">Set of Pieces <span class="text-danger">*</span></label>
                                    <input type="number" value="{{ $data->set_of_pcs }}" name="set_of_pcs" class="form-control" min="1" id="">
                                    @error('set_of_pcs') <p class="small text-danger">{{ $message }}</p> @enderror
                                </div>
                            </div> 
                            <div class="col-md-3 div_spare_spec1" id="">
                                <div class="form-group">
                                    <label for="">Spare Type <span class="text-danger">*</span></label>
                                    <select name="spare_type" class="form-control" id="">
                                        <option value="general" @if($data->spare_type == 'general') selected @endif>General</option>
                                        <option value="motor" @if($data->spare_type == 'motor') selected @endif>Motor</option>
                                    </select>
                                    @error('spare_type') <p class="small text-danger">{{ $message }}</p> @enderror
                                </div>
                            </div>   
                            <div class="col-md-3 div_spare_spec1" id="">
                                <div class="form-group">
                                    <label for="">Profit Percentage <span class="text-danger">*</span></label>
                                    <div class="input-group ">
                                        <input type="text" onkeypress="validateNum(event)" class="form-control" id="profit_percentage" placeholder="" name="profit_percentage" maxlength="5" style="width:90px;" value="{{$data->profit_percentage}}">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                %
                                            </div>
                                        </div>
                                    </div>
                                    @error('profit_percentage') <p class="small text-danger">{{ $message }}</p> @enderror
                                </div>
                            </div>                                     
                        </div>     
                    </div>                 
                </div>
                @if ($data->type == 'fg')                    
                <div class="card shadow-sm" id="div_service_level">
                    <div class="card-body">
                        <div class="row">     
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Service Level <span class="text-danger">*</span></label>
                                    <select name="service_level" class="form-control" id="">
                                        <option value="" selected hidden>Select an option</option>
                                        <option value="customer" @if($data->service_level == 'customer') selected @endif>Customer</option>
                                        <option value="dealer" @if($data->service_level == 'dealer') selected @endif>Dealer</option>
                                    </select>
                                    
                                    @error('service_level') <p class="small text-danger">{{ $message }}</p> @enderror
                                </div>
                            </div>                        
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" id="is_installable" name="is_installable" @if($data->is_installable == 1) checked @endif>
                                    <label class="form-check-label" for="is_installable">
                                        Installation Applicable
                                    </label>
                                </div>
                                @error('is_installable') <p class="small text-danger">{{ $message }}</p> @enderror                            
                            </div> 
                                                          
                        </div>     
                    </div>                 
                </div>
                @endif
                <div class="card shadow-sm" id="div_repair_charge">
                    <div class="card-body">
                        <div class="row">     
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Repair Charge  <br/> <i style="font-style: italic"> Out Of Warranty</i> </label></label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text" style="height: 34px;">
                                                Rs.
                                            </div>
                                        </div>
                                        <input type="text" class="form-control" name="repair_charge" id="repair_charge" placeholder="Enter Repair Charge" value="{{ $data->repair_charge }}" onkeypress="validateNum(event)" maxlength="8">
                                    </div>
                                    
                                    @error('repair_charge') <p class="small text-danger">{{ $message }}</p> @enderror
                                </div>
                            </div> 
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Supplier Warranty <br/> Period (In month) </label>
                                    <input type="number" autocomplete="off" name="supplier_warranty_period" class="form-control" value="{{ $data->supplier_warranty_period }}"  min="1" max="99909" pattern="[0-9]" id="supplier_warranty_period"   placeholder="Enter Supplier Warranty Period">
                                    @error('supplier_warranty_period') <p class="small text-danger">{{ $message }}</p> @enderror
                                </div>
                            </div>                                                             
                        </div>  
                    </div>                    
                </div>
                @if ($data->type == 'fg' && $data->goods_type == 'general')            
                <div class="card shadow-sm" id="div_warranty_level1">
                    <div class="card-body">
                        <div class="row">  
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">Warranty Status <span class="text-danger">*</span></label>
                                    <select name="warranty_status" class="form-control" id="warranty_status">
                                        <option value="" selected hidden>Select an option</option>
                                        <option value="yes" @if($data->warranty_status == 'yes') selected @endif>Yes</option>
                                        <option value="no" @if($data->warranty_status == 'no') selected @endif>No</option>
                                    </select>
                                    @error('warranty_status') <p class="small text-danger">{{ $message }}</p> @enderror
                                </div>
                            </div>  
                            <div class="col-md-3" id="warranty_div">
                                <div class="form-group">
                                    <label for="">Warranty Period (In month)  <span class="text-danger">*</span></label>
                                    <input type="text" name="warranty_period" class="form-control" value="{{ $data->warranty_period }}" id="warranty_period" onkeypress="validateNum(event)">                                
                                    @error('warranty_period') <p class="small text-danger">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                <div class="card shadow-sm" id="div_warranty_level2">
                    <div class="card-body">
                        <h6> Chimney Warranty Label </h6>
                        <div class="row align-items-end">  
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="comprehensive_warranty">Free Service Tenure (In month) <span class="text-danger">*</span></label>
                                    <input type="text" name="comprehensive_warranty" value="{{ $data->comprehensive_warranty }}" class="form-control"  id="comprehensive_warranty" onkeypress="validateNum(event)" maxlength="3">
                                    @error('comprehensive_warranty') <p class="small text-danger">{{ $message }}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="comprehensive_warranty_free_services">No of Free Maintenances <span class="text-danger">*</span></label>
                                    <input type="text" name="comprehensive_warranty_free_services" value="{{ $data->comprehensive_warranty_free_services }}" class="form-control"  id="comprehensive_warranty_free_services" onkeypress="validateNum(event)" maxlength="1">
                                    @error('comprehensive_warranty_free_services') <p class="small text-danger">{{ $message }}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="extra_warranty">Additional Warranty (In month)  <span class="text-danger">*</span>
                                    <br/> <i style="font-style: italic">i.e. This Is Above Standard One Year Warranty That You Are Given</i> </label>
                                    <input type="text" name="extra_warranty" value="{{ $data->extra_warranty }}" class="form-control"  id="extra_warranty" onkeypress="validateNum(event)" maxlength="3">
                                    @error('extra_warranty') <p class="small text-danger">{{ $message }}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="motor_warranty">Motor Warranty (In month) <span class="text-danger">*</span></label>
                                    <input type="text" name="motor_warranty" value="{{ $data->motor_warranty }}" class="form-control"  id="motor_warranty" onkeypress="validateNum(event)" maxlength="3">
                                    @error('motor_warranty') <p class="small text-danger">{{ $message }}</p> @enderror
                                </div>
                            </div>                        
                        </div>
                    </div>
                </div>  
                                
                <div class="card shadow-sm">
                    <div class="card-body text-end">                        
                        <a href="{{route('product.list')}}?{{$getQueryString}}" class="btn btn-sm btn-danger">Back</a>
                        <button id="submitBtn" type="submit" class="btn btn-sm btn-success">Update </button>
                    </div>
                </div>                                            
            </div>              
        </div>
                 
        </form>             
    </div>    
</section>
<style>
    /* Chrome, Safari, Edge, Opera */
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
    }

    /* Firefox */
    input[type=number] {
    -moz-appearance: textfield;
    }
</style>
<script>

    function getBrowserType() {
        const test = regexp => {
            return regexp.test(navigator.userAgent);
        };
        console.log(navigator.userAgent);
        var navigator_useragent = navigator.userAgent;
        $('#navigator_useragent').val(navigator_useragent);
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
    $('#browser_name').val(browserType);

    $(document).ready(function(){
        var old_comprehensive_warranty = "{{ $data->comprehensive_warranty }}";
        var old_comprehensive_warranty_free_services = "{{ $data->comprehensive_warranty_free_services }}";
        var old_extra_warranty = "{{ $data->extra_warranty }}";
        var old_motor_warranty = "{{ $data->motor_warranty }}";

        if(old_comprehensive_warranty != '' || old_comprehensive_warranty_free_services != '' || old_extra_warranty != '' || old_motor_warranty != '') {
            $('#div_warranty_level2').show();
        } else {
            $('#div_warranty_level2').hide();
        }

        
        var old_type = "{{ $data->type }}";
        if(old_type != ''){
            if(old_type == 'fg'){       
                $('.div_spare_spec1').hide();
                $('.div_goods_type').show();
                $('#subcat_div').hide();
                $('#div_repair_charge').show();
                $('.div_mop').show();
            }else{                
                $('.div_spare_spec1').show();
                $('.div_goods_type').hide();
                $('#subcat_div').show();
                $('#div_repair_charge').hide();
                $('.div_mop').hide();
            }
        }else {            
            $('.div_spare_spec1').hide();
            $('.div_goods_type').hide();
            $('#subcat_div').hide();
            $('#div_repair_charge').hide();
            $('.div_mop').hide();
        } 

        $('#div_warranty_level2').hide();
        var old_goods_type = "{{ $data->goods_type }}";
        if(old_goods_type != ''){
            if(old_goods_type == 'general'){
                $('#div_warranty_level2').hide();
            } else {
                $('#div_warranty_level2').show();
            }
        }

        $('#warranty_div').hide();
        var old_warranty_status = "{{ $data->warranty_status }}";
        if(old_warranty_status != ''){
            if(old_warranty_status == 'yes'){
                $('#warranty_div').show();
            } else {
                $('#warranty_div').hide();
            }
        }

        var old_cat_id = $('#cat_id').val();
        var old_subcat_id = $('#subcat_id').val();
        $.ajax({
            url: "{{ route('ajax.subcategory-by-category') }}",
            dataType: 'json',
            type: 'post',
            data: {
                "_token": "{{ csrf_token() }}",
                "cat_id": old_cat_id
            },
            success: function(data){
                var subcats = data.subcats;
                console.log(data);
                var subcatHTML = ``;
                if(subcats.length == 0){
                    subcatHTML += `<option value="" disabled>No subcategory available ... </option>`;
                }
                
                subcatHTML += `<option value="" hidden selected>Select an option</option>`;
                for(var i=0; i < subcats.length; i++){

                    var subcatSelected = '';
                    if(old_subcat_id == subcats[i].id){
                        subcatSelected = 'selected';
                    }

                    subcatHTML += `<option value="`+subcats[i].id+`" `+subcatSelected+` data-name="`+subcats[i].name+`">`+subcats[i].name+`</option>`;
                }

                $('#subcat_id').html(subcatHTML);

               
            }
        });
             

        


    });
    $("#myForm").submit(function() {
        $('input').attr('readonly', 'readonly');
        $('#submitBtn').attr('disabled', 'disabled');        
        return true;
    });
    $('#cat_id').on('change', function(){        
        $.ajax({
            url: "{{ route('ajax.subcategory-by-category') }}",
            dataType: 'json',
            type: 'post',
            data: {
                "_token": "{{ csrf_token() }}",
                "cat_id": this.value
            },
            success: function(data){
                var subcats = data.subcats;
                console.log(data);
                var subcatHTML = ``;
                if(subcats.length == 0){
                    subcatHTML += `<option value="" disabled>No subcategory available ... </option>`;
                }
                subcatHTML += `<option value="" hidden selected>Select an option</option>`;
                for(var i=0; i < subcats.length; i++){
                    subcatHTML += `<option value="`+subcats[i].id+`" data-name="`+subcats[i].name+`">`+subcats[i].name+`</option>`;
                }

                $('#subcat_id').html(subcatHTML);

                
            }
        });
    });
    $('#subcat_id').on('change', function(){
        var subcat_name = $('option:selected', this).attr('data-name');
        // alert(subcat_name);
        $('#subcat_name').val(subcat_name);
    });
    $('#warranty_status').on('change', function(){
        var warranty_status = this.value;
        if(warranty_status == 'yes'){
            $('#warranty_div').show();
        }else{
            $('#warranty_div').hide();
        }
    });
    $('#type').on('change', function(){
        // alert(this.value);
        if(this.value == 'fg'){
            $('.div_spare_spec1').hide();
            $('.div_goods_type').show();
            $('#subcat_div').hide();
            $('.div_mop').show();
        } else {
            $('.div_spare_spec1').show();
            $('.div_goods_type').hide();
            $('#subcat_div').show();
            $('.div_mop').hide();
        }
    })
    $("input:checkbox#flexCheckDefault").change(function() {
        var ischecked= $(this).is(':checked');
        var name = $('#name').val();
        var public_name = $('#public_name').val();  
        if(ischecked){
            $('#public_name').val(name);  
            $('#public_name').prop('readonly', true);  
            
        }else{
            $('#public_name').val('');  
            $('#public_name').prop('readonly', false);   
           
        }       
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
</script>
@endsection