@extends('layouts.app')
@section('content')
@section('page', 'Product')
<section>   
    <ul class="breadcrumb_menu">  
        <li>Product Management</li>      
        <li><a href="{{ route('product.list') }}">Product</a> </li>
        <li>Create</li>
    </ul>
    {{-- @if($errors->any())
    {{ implode('', $errors->all('<div>:message</div>')) }}
    @endif --}}
    <div class="row">
        <form id="myForm" action="{{ route('product.store') }}" enctype="multipart/form-data" method="POST">
            @csrf
        <div class="row">
            <div class="col-sm-12">            
                <div class="card shadow-sm">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">ID <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" value="{{genAutoIncreNo()}}" readonly disabled>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Type <span class="text-danger">*</span></label>
                                <select name="type" class="form-control" id="type">
                                    <option value="" hidden selected>Select an option</option>
                                    <option value="fg" @if(old('type') == 'fg') selected @endif>Finished Goods</option>
                                    <option value="sp" @if(old('type') == 'sp') selected @endif>Spare Parts</option>
                                </select>
                                @error('type') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div> 
                        <div class="col-md-3" id="cat_div">
                            <div class="form-group">
                                <label for="">Category (Class) <span class="text-danger">*</span></label>
                                <select name="cat_id" class="form-control" id="cat_id">
                                    <option value="" hidden selected>Select an option</option>
                                    @forelse ($category as $item)
                                    <option value="{{$item->id}}" @if(old('cat_id') == $item->id) selected @endif>{{$item->name}}</option>
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
                                <select name="subcat_id" class="form-control" id="subcat_id">
                                    <option value="" hidden selected>Select an option</option>
                                    @if (!empty(old('subcat_id')))
                                    <option value="{{old('subcat_id')}}" selected data-name="{{old('subcat_name')}}">{{old('subcat_name')}}</option>   
                                    @endif
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
                                <input class="form-check-input" type="checkbox" value="1" id="is_test_product" name="is_test_product" title="Set As Test Product" @if(old('is_test_product') == 1) checked @endif>
                                <label class="form-check-label" for="is_test_product">
                                    Test Product
                                </label>
                            </div>
                        </div>                     
                        <div class="col-md-10">
                            <div class="form-group">
                                <label for="">Title <span class="text-danger">*</span></label>
                                <div class="input-group mb-3">
                                    <input type="text" autocomplete="off" class="form-control" name="title" id="name" placeholder="Enter title" value="{{ old('title') }}" maxlength="100">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text" style="height: 37px;">
                                            <input type="checkbox" id="flexCheckDefault" title="Check same as product title and public name">
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
                                <input type="text" autocomplete="off" name="public_name" id="public_name" placeholder="Please Enter Public Name" class="form-control" maxlength="100" value="{{old('public_name')}}">
                                @error('public_name') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">HSN Code </label>
                                <input type="text" autocomplete="off" name="hsn_code" id="hsn_code" placeholder="Please Enter HSN Code" class="form-control" maxlength="10" value="{{old('hsn_code')}}">
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
                                    <textarea name="description" class="form-control" id="" cols="3" rows="3" placeholder="Enter product description">{{old('description')}}</textarea>
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
                                        <input type="text" autocomplete="off" class="form-control" name="mop" id="mop" placeholder="Enter Market Operating Price" value="{{ old('mop') }}" onkeypress="validateNum(event)" maxlength="8">
                                    </div>
                                    @error('mop') <p class="small text-danger">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
				<!-- Amc Applicable-->
				
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row">  
                            <div class="col-md-3 div_goods_type">
                                <div class="form-group">
                                    <label for="goods_type">Goods Type <span class="text-danger">*</span></label>
                                    <select name="goods_type" class="form-control" id="goods_type">
                                        <option value="general" @if(old('goods_type') == 'general') selected @endif>General</option>
                                        <option value="chimney" @if(old('goods_type') == 'chimney') selected @endif>Chimney</option>
                                        <option value="gas_stove" @if(old('goods_type') == 'gas_stove') selected @endif>Gas Stove</option>
                                        <option value="ac" @if(old('goods_type') == 'ac') selected @endif>Ac</option>
										 <option value="gieger" @if(old('goods_type') == 'gieger') selected @endif>Geyser</option>
                                    </select>
                                    @error('goods_type') <p class="small text-danger">{{ $message }}</p> @enderror
                                </div>
                            </div> 
                            
                            <div class="col-md-3 div_spare_spec1" id="">
                                <div class="form-group">
                                    <label for="">Set of Pieces <span class="text-danger">*</span></label>
                                    <input type="number" value="{{ old('set_of_pcs') }}" name="set_of_pcs" class="form-control" min="1" id="">
                                    @error('set_of_pcs') <p class="small text-danger">{{ $message }}</p> @enderror
                                </div>
                            </div> 
                            <div class="col-md-3 div_spare_spec1" id="">
                                <div class="form-group">
                                    <label for="">Spare Type <span class="text-danger">*</span></label>
                                    <select name="spare_type" class="form-control" id="">
                                        <option value="general" @if(old('spare_type') == 'general') selected @endif>General</option>
                                        <option value="motor" @if(old('spare_type') == 'motor') selected @endif>Motor</option>
                                    </select>
                                    @error('spare_type') <p class="small text-danger">{{ $message }}</p> @enderror
                                </div>
                            </div> 
                            <div class="col-md-3 div_spare_spec1" id="">
                                <div class="form-group">
                                    <label for="">Profit Percentage <span class="text-danger">*</span></label>
                                    <div class="input-group ">
                                        <input type="text" autocomplete="off" onkeypress="validateNum(event)" class="form-control" id="profit_percentage" placeholder="" name="profit_percentage" maxlength="5" style="width:90px;" value="{{ old('profit_percentage') }}">
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
				
                <div class="card shadow-sm" id="div_service_level">
                <div class="card-body">
                    <div class="row">     
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Service Level <span class="text-danger">*</span></label>
                                <select name="service_level" class="form-control" id="">
                                    <option value="" selected hidden>Select an option</option>
                                    <option value="customer" @if(old('service_level') == 'customer') selected @endif>Customer</option>
                                    <option value="dealer" @if(old('service_level') == 'dealer') selected @endif>Dealer</option>
                                </select>
                                
                                @error('service_level') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div> 
                        
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="is_installable" name="is_installable" @if(old('is_installable') == 1) checked @endif>
                                <label class="form-check-label" for="is_installable">
                                    Installation Applicable
                                </label>
                            </div>
                            @error('is_installable') <p class="small text-danger">{{ $message }}</p> @enderror  
                            <div class="form-check" id="installable_amount_div" 
								 style="display: {{ $errors->has('installable_amount') || old('installable_amount') ? 'block' : 'none' }}">   
                                <input type="text" class="form-control" name="installable_amount" value="{{old('installable_amount')}}" placeholder="Installation amount">  
                                @error('installable_amount') <p class="small text-danger">{{ $message }}</p> @enderror                     
                            </div> 
                        </div> 
                                                 
                    </div>  
                </div>                    
                </div>
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
                                        <input type="text" autocomplete="off" class="form-control" name="repair_charge" id="repair_charge" placeholder="Enter Repair Charge" value="{{ old('repair_charge') }}" onkeypress="validateNum(event)" maxlength="8">
                                    </div>
                                    
                                    @error('repair_charge') <p class="small text-danger">{{ $message }}</p> @enderror
                                </div>
                            </div> 
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Supplier Warranty <br/> Period (In month) </label>
                                    <input type="number" autocomplete="off" name="supplier_warranty_period" class="form-control" value="{{ old('supplier_warranty_period') }}"  min="1" max="99909" pattern="[0-9]" id="supplier_warranty_period"   placeholder="Enter Supplier Warranty Period">
                                    @error('supplier_warranty_period') <p class="small text-danger">{{ $message }}</p> @enderror
                                </div>
                            </div>                                                              
                        </div>  
                    </div>                    
                </div>
                
                <!-- <div class="card shadow-sm" id="div_warranty_level1">
                    <div class="card-body">
                        <div class="row">  
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">Warranty Status <span class="text-danger">*</span></label>
                                    <select name="warranty_status" class="form-control" id="warranty_status">
                                        <option value="" selected hidden>Select an option</option>
                                        <option value="yes" @if(old('warranty_status') == 'yes') selected @endif>Yes</option>
                                        <option value="no" @if(old('warranty_status') == 'no') selected @endif>No</option>
                                    </select>
                                    @error('warranty_status') <p class="small text-danger">{{ $message }}</p> @enderror
                                </div>
                            </div>  
                            <div class="col-md-3" id="warranty_div">
                                <div class="form-group">
                                    <label for="">Warranty Period (In month)  <span class="text-danger">*</span></label>
                                    <input type="text" autocomplete="off" name="warranty_period" class="form-control" value="{{ old('warranty_period') }}" id="warranty_period" onkeypress="validateNum(event)" placeholder="Enter warranty period">                                
                                    @error('warranty_period') <p class="small text-danger">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card shadow-sm" id="div_warranty_level2">
                    <div class="card-body">
                        <h6> Chimney Warranty Label </h6>
                        <div class="row align-items-end">  
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="comprehensive_warranty">Free Service Tenure (In month) <span class="text-danger">*</span></label>
                                    <input type="text" autocomplete="off" name="comprehensive_warranty" value="{{ old('comprehensive_warranty') }}" class="form-control"  id="comprehensive_warranty" onkeypress="validateNum(event)" maxlength="3">
                                    @error('comprehensive_warranty') <p class="small text-danger">{{ $message }}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="comprehensive_warranty_free_services">No of Free Maintenances <span class="text-danger">*</span></label>
                                    <input type="text" autocomplete="off" name="comprehensive_warranty_free_services" value="{{ old('comprehensive_warranty_free_services') }}" class="form-control"  id="comprehensive_warranty_free_services" onkeypress="validateNum(event)" maxlength="1">
                                    @error('comprehensive_warranty_free_services') <p class="small text-danger">{{ $message }}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="extra_warranty">Additional Warranty (In month)  <span class="text-danger">*</span>
                                    <br/> <i style="font-style: italic">i.e. This Is Above Standard One Year Warranty That You Are Given</i> </label>
                                    <input type="text" autocomplete="off" name="extra_warranty" value="{{ old('extra_warranty') }}" class="form-control"  id="extra_warranty" onkeypress="validateNum(event)" maxlength="3">
                                    @error('extra_warranty') <p class="small text-danger">{{ $message }}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="motor_warranty">Motor Warranty (In month) <span class="text-danger">*</span></label>
                                    <input type="text" autocomplete="off" name="motor_warranty" value="{{ old('motor_warranty') }}" class="form-control"  id="motor_warranty" onkeypress="validateNum(event)" maxlength="3">
                                    @error('motor_warranty') <p class="small text-danger">{{ $message }}</p> @enderror
                                </div>
                            </div>                        
                        </div>
                    </div>
                </div> -->
                                
                <div class="card shadow-sm">
                    <div class="card-body text-end">
                        <a href="{{route('product.list')}}" class="btn btn-sm btn-danger">Back</a>
                        <button type="submit" id="submitBtn" class="btn btn-sm btn-success">Create </button>
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
    $(document).ready(function(){
        var old_comprehensive_warranty = "{{ old('comprehensive_warranty') }}";
        var old_comprehensive_warranty_free_services = "{{ old('comprehensive_warranty_free_services') }}";
        var old_extra_warranty = "{{ old('extra_warranty') }}";
        var old_motor_warranty = "{{ old('motor_warranty') }}";

        if(old_comprehensive_warranty != '' || old_comprehensive_warranty_free_services != '' || old_extra_warranty != '' || old_motor_warranty != '') {
            $('#div_warranty_level2').show();
        } else {
            $('#div_warranty_level2').hide();
        }

        
        var old_type = "{{ old('type') }}";
        if(old_type != ''){
            $('#cat_div').show();
            if(old_type == 'fg'){
                $('#div_service_level').show();
                $('#div_warranty_level1').show();
                $('.div_spare_spec1').hide();
                $('.div_goods_type').show();
                $('#subcat_div').hide();
                $('#div_repair_charge').show();
                $('.div_mop').show();
            }else{
                $('#div_service_level').hide();
                $('#div_warranty_level1').hide();
                $('.div_spare_spec1').show();
                $('.div_goods_type').hide();
                $('#subcat_div').show();
                $('#div_repair_charge').hide();
                $('.div_mop').hide();
            }
        }else {
            $('#div_service_level').hide();
            $('#div_warranty_level1').hide();
            $('.div_spare_spec1').hide();
            $('.div_goods_type').hide();
            $('#subcat_div').hide();
            $('#cat_div').hide();
            $('#div_repair_charge').hide();
            $('.div_mop').hide();
        }  

        $('#div_warranty_level2').hide();
        var old_goods_type = "{{ old('goods_type') }}";
        if(old_goods_type != ''){
            if(old_goods_type == 'general'){
                $('#div_warranty_level2').hide();
                $('#div_warranty_level1').show();
            } else {
                $('#div_warranty_level2').show();
                $('#div_warranty_level1').hide();
            }
        }
        $('#warranty_div').hide();
        var old_warranty_status = "{{ old('warranty_status') }}";
        if(old_warranty_status != ''){
            if(old_warranty_status == 'yes'){
                $('#warranty_div').show();
            } else {
                $('#warranty_div').hide();
            }
        }
        
        
        
                
    });
    $("#myForm").submit(function() {
        $('input').attr('readonly', 'readonly');
        $('#submitBtn').attr('disabled', 'disabled');   
        $('#submitBtn').html('<i class="fi fi-br-refresh"></i>');     
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
        $('#cat_div').show();
        if(this.value == 'fg'){
            $('#div_service_level').show();
            $('#div_warranty_level1').show();
            $('.div_spare_spec1').hide();
            $('.div_goods_type').show();
            $('#subcat_div').hide();
            $('#div_repair_charge').show();
            $('.div_mop').show();
        } else {
            $('#div_service_level').hide();
            $('#div_warranty_level1').hide();
            $('.div_spare_spec1').show();
            $('.div_goods_type').hide();
            $('#subcat_div').show();
            $('#div_repair_charge').hide();
            $('.div_mop').hide();
        }

        $.ajax({
            url: "{{ route('ajax.category-by-product-type') }}",
            dataType: 'json',
            type: 'post',
            data: {
                "_token": "{{ csrf_token() }}",
                "product_type": this.value
            },
            success: function(data){
                var category = data.category;
                console.log(data);
                var catHTML = ``;
                if(category.length == 0){
                    catHTML += `<option value="" disabled>No category available ... </option>`;
                }
                catHTML += `<option value="" hidden selected>Select an option</option>`;
                for(var i=0; i < category.length; i++){
                    catHTML += `<option value="`+category[i].id+`" data-name="`+category[i].name+`">`+category[i].name+`</option>`;
                }

                $('#cat_id').html(catHTML);

                
                
            }
        });
    });
    $('#goods_type').on('change', function(){
        if(this.value == 'general'){
            $('#div_warranty_level2').hide();
            $('#div_warranty_level1').show();
        } else {
            $('#div_warranty_level2').show();
            $('#div_warranty_level1').hide();
        }
    })
    $('#is_installable').on('change', function(){
        if (this.checked) {
            $('#installable_amount_div').show();
        } else {
            $('#installable_amount_div').hide();
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