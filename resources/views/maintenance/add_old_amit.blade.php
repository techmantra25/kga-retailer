@extends('layouts.app')
@section('content')
@section('page', 'Add New Chimney Maintenance & Repair Request')
<section>   
    <ul class="breadcrumb_menu">  
        <li>Chimney Maintenance & Repair Request</li>
        <li>Add New Request</li>
        <li>{{ ucwords(Request::get('service_type')) }} Service</li>
    </ul>
    <div class="row">
        @if (Session::has('message'))
        <div class="alert alert-success" role="alert">
            {{ Session::get('message') }}
        </div>
        @endif        
        <div class="row">
            <div class="col-sm-12"> 
                <div id="form2">   
                    <form id="myForm" action="{{ route('maintenance.save') }}"  method="POST">
                    @csrf
                    {{-- <input type="hidden" name="service_type" value="{{ Request::get('service_type') }}"> --}}
                    <input type="hidden" name="is_spare_chargeable" value="{{ Request::get('is_spare_chargeable') }}">
                    <input type="hidden" name="is_repair_chargeable" value="{{ Request::get('is_repair_chargeable') }}">
                    <input type="hidden" name="out_of_warranty" value="{{ Request::get('out_of_warranty') }}">
                    <input type="hidden" name="maintenance_type" value="{{ Request::get('maintenance_type') }}">
                    
                    <ul class="pincodeclass">
                        <li>Bill</li>
                    </ul>  
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">Bill No </label>
                                        <input type="text" placeholder="Enter Bill No" name="bill_no" class="form-control" maxlength="100" id="" readonly value="{{ Request::get('bill_no') }}">        
                                                            
                                    </div>
                                </div> 
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">Bill Date </label>
                                        <input type="date"  readonly name="order_date" class="form-control" id="order_date" value="{{ Request::get('order_date') }}">  
                                        @error('order_date') <p class="small text-danger">{{ $message }}</p> @enderror                                   
                                    </div>
                                </div>  
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">Bill Value </label>
                                        <input type="text" placeholder="Enter Bill Value" name="product_value" class="form-control" id="" readonly value="{{ Request::get('product_value') }}"> 
                                    </div>
                                </div>                                                                       
                            </div>     
                        </div>                 
                    </div>  
                    <ul class="pincodeclass">
                        <li>Product</li>
                    </ul>  
                    <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="">Name </label>
                                    <input type="hidden" name="product_id" id="product_id" value="{{ Request::get('product_id') }}">
                                    <input type="hidden" name="comprehensive_warranty" id="comprehensive_warranty" value="{{ Request::get('comprehensive_warranty') }}">
                                    <input type="hidden" name="comprehensive_warranty_free_services" id="comprehensive_warranty_free_services" value="{{ Request::get('comprehensive_warranty_free_services') }}">
                                    <input type="hidden" name="extra_warranty" id="extra_warranty" value="{{ Request::get('extra_warranty') }}">
                                    <input type="hidden" name="motor_warranty" id="motor_warranty" value="{{ Request::get('motor_warranty') }}">
                                    <input type="text" readonly name="product_name" maxlength="200" class="form-control" id="product_name" value="{{ Request::get('product_name') }}">
                                    
                                    
                                </div>
                            </div>  
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Serial No </label>
                                    <input type="text" autocomplete="off" placeholder="Enter Product Serial No" name="product_sl_no" maxlength="100" class="form-control" id="" value="{{ Request::get('product_sl_no') }}" readonly> 
                                    @error('product_sl_no') <p class="small text-danger">{{ $message }}</p> @enderror                         
                                </div>
                            </div>                                  
                        </div> 
                    </div>                     
                    </div>
                                      
                    <ul class="pincodeclass">
                        <li>Dealer</li>
                    </ul>
                    <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Dealer <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="dealer_name" onkeyup="searchDealerUser(this.value);" placeholder="Search dealer user ... " name="dealer_name" value="{{ old('dealer_name') }}" autocomplete="off">
                                    <input type="hidden" name="dealer_id" id="dealer_id" class="" value="{{ old('dealer_id') }}">
                                    
                                    <div class="respDropDealer" id="respDropDealer" style="position: relative;"></div>
                                    @error('dealer_id') <p class="small text-danger">{{ $message }}</p> @enderror                                
                                </div>
                            </div> 
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">PIN Code<span class="text-danger">*</span></label>
                                    <input type="text" autocomplete="off" placeholder="Enter PIN Code" maxlength="6" name="pincode" class="form-control" onkeyup="getServicePartners(this.value);" id="pincode" onkeypress="validateNum(event)" value="{{ old('pincode') }}">   
                                    @error('pincode') <p class="small text-danger">{{ $message }}</p> @enderror                              
                                </div>
                            </div>  
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Assigned Service Partner <span class="text-danger">*</span></label>
                                    <input type="hidden" name="service_partner_id" id="service_partner_id" value="{{old('service_partner_id')}}">
                                    <input type="hidden" name="service_partner_email" id="service_partner_email" value="{{old('service_partner_email')}}">
                                    <input type="hidden" name="service_partner_person_name" id="service_partner_person_name" value="{{old('service_partner_person_name')}}">
                                    <input type="hidden" name="service_partner_company_name" id="service_partner_company_name" value="{{old('service_partner_company_name')}}">
                                    <input type="text" name="service_partner_name" class="form-control" id="service_partner_name" value="{{old('service_partner_name')}}" onkeyup="searchServicePartner(this.value);" placeholder="Search service partner ... " autocomplete="off"> 

                                    <div class="respDropServicePartner" id="respDropServicePartner" style="position: relative;"></div>


                                    @error('service_partner_id') <p class="small text-danger">{{ $message }}</p> @enderror   
                                                            
                                </div>
                            </div>                                              
                        </div>
                    </div>
                    </div>
                    <ul class="pincodeclass">
                        <li>End Customer</li>
                    </ul>  
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Name <span class="text-danger">*</span></label>
                                        <input type="text" autocomplete="off" placeholder="Enter Customer Full Name" name="customer_name" class="form-control" maxlength="250" id="" value="{{ old('customer_name') }}">  
                                        @error('customer_name') <p class="small text-danger">{{ $message }}</p> @enderror                                  
                                    </div>
                                </div> 
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Mobile No <span class="text-danger">*</span></label>
                                        <input type="text" autocomplete="off" placeholder="Enter Customer Mobile No" name="customer_phone" class="form-control" maxlength="10" id="" value="{{ old('customer_phone') }}">    
                                        @error('customer_phone') <p class="small text-danger">{{ $message }}</p> @enderror                                
                                    </div>
                                </div>                                             
                            </div>
                        </div>                      
                    </div>
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="">Address </label>
                                        <textarea name="address" placeholder="Enter Address" class="form-control" id="" cols="1" rows="1">{{ old('address') }}</textarea>    
                                        @error('address') <p class="small text-danger">{{ $message }}</p> @enderror                            
                                    </div>
                                </div> 
                            </div>                                    
                        </div>                      
                    </div>
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="row">
                                 
                            </div>                                    
                        </div>                      
                    </div>
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="row">    
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">Service For </label>
                                        <select name="service_for" class="form-control" id="service_for">
                                            <option value="chimney">Chimney</option>    
                                            <option value="motor">Motor</option>
                                        </select> 
                                        @error('service_for') <p class="small text-danger">{{ $message }}</p> @enderror                            
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">Service Type </label>
                                        <select name="service_type" class="form-control" id="service_type">   
                                            <option value="repairing" title="For Both Chminey And Motor">Repairing</option>
                                            <option value="cleaning" title="Only For Chimney" @if(old('service_for') == 'motor') disabled @endif>Cleaning</option> 
                                        </select> 
                                        @error('service_type') <p class="small text-danger">{{ $message }}</p> @enderror                            
                                    </div>
                                </div>                    
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Remarks </label>
                                        <textarea name="remarks" class="form-control" id="" cols="3" rows="5">{{ old('remarks') }}</textarea>
                                        @error('remarks') <p class="small text-danger">{{ $message }}</p> @enderror                                 
                                    </div>
                                </div>                                                         
                            </div>     
                        </div>                 
                    </div>                
                                    
                    <div class="card shadow-sm">
                        <div class="card-body text-end">
                            <a href="{{ route('maintenance.checkitemstatus') }}?{{ Request::getQueryString() }}" class="btn btn-danger">Back</a>
                            <button type="submit" id="submitBtn" class="btn btn-success">Submit </button>
                        </div>
                    </div>       
                    </form>               
                </div>                                                    
            </div>              
        </div>                      
    </div>    
</section>
<script>
    $(document).ready(function(){
        
    });
    $("#myForm").submit(function() {
        $('input').attr('readonly', 'readonly');
        $('#submitBtn').attr('disabled', 'disabled');   
        $('#submitBtn').html('<i class="fi fi-br-refresh"></i>');     
        return true;
    });

    $('#order_date').on('change', function(){
        var order_date = $('#order_date').val();
        var product_id = $('#product_id').val();

        if(product_id != ''){
            fetchProduct(product_id);
        }
    });

    $('#service_for').on('change', function(){
        var service_for = this.value;
        if(service_for == 'motor'){
            
            // $("#service_type option:contains('Value cleaning')").attr("disabled","disabled");
            $("#service_type option[value='cleaning']").attr('disabled', true); 
        }
    })
   
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
        var regex = /[0-9]/; // only number
        // var regex = /[0-9]|\./; // number with point
        if( !regex.test(key) ) {
            theEvent.returnValue = false;
            if(theEvent.preventDefault) theEvent.preventDefault();
        }
    }

    function searchDealerUser(search){
        if(search.length > 0) {
            $.ajax({
                url: "{{ route('ajax.search-dealer-user') }}",
                method: 'post',
                data: {
                    '_token': '{{ csrf_token() }}',
                    search: search
                },
                success: function(result) {
                    console.log(result);
                    var content = '';
                    if (result.length > 0) {
                        content += `<div class="dropdown-menu show  dealer-dropdown select-md" aria-labelledby="dropdownMenuButton" style="width: 100%;">`;

                        $.each(result, (key, value) => {                            
                            content += `<a class="dropdown-item" href="javascript: void(0)" onclick="fetchDealer(${value.id},'${value.name}')">${value.name} </a>`;
                        })
                        content += `</div>`;
                        // $($this).parent().after(content);
                    } else {
                        content += `<div class="dropdown-menu show  dealer-dropdown select-md" aria-labelledby="dropdownMenuButton"><li class="dropdown-item">No product found</li></div>`;
                    }
                    $('#respDropDealer').html(content);
                }
            });
        } else {
            $('.dealer-dropdown').hide()
        }
        
    }

    function fetchDealer(id,name) {
        $('.dealer-dropdown').hide()
        $('#dealer_id').val(id);
        $('#dealer_name').val(name);
          
    }



    function getServicePartners(evt){
        console.log(evt);
        $.ajax({
            url: "{{ route('ajax.get-service-partner-by-pincode') }}",
            method: 'post',
            data: {
                '_token': '{{ csrf_token() }}',
                pincode:evt,
                product_type: 'chimney'
            },
            success: function(result) {
                $('#service_partner_id').val('');
                $('#service_partner_name').val('');
                $('#service_partner_email').val('');
                if(result != ''){
                    var service_partner_id = result.service_partner_id;
                    var service_partner_email = result.service_partner.email;
                    var company_name = result.service_partner.company_name;
                    var person_name = result.service_partner.person_name;
                    console.log('service_partner_id:- '+service_partner_id)
                    console.log('service_partner_email:- '+service_partner_email)
                    console.log('company_name:- '+company_name);
                    console.log('person_name:- '+person_name);
                    $('#service_partner_id').val(service_partner_id);
                    $('#service_partner_name').val(person_name+' | '+company_name);
                    $('#service_partner_email').val(service_partner_email);
                    $('#service_partner_company_name').val(company_name);
                    $('#service_partner_person_name').val(person_name);
                }
            }
        });   
    }

    function searchServicePartner(search){
        if(search.length > 0) {
            $.ajax({
                url: "{{ route('ajax.searchServicePartner') }}",
                method: 'post',
                data: {
                    '_token': '{{ csrf_token() }}',
                    search: search,
                    is_active: 'active'
                },
                success: function(result) {
                    console.log(result);
                    var content = '';
                    if (result.length > 0) {
                        content += `<div class="dropdown-menu show  servicepartner-dropdown select-md" aria-labelledby="dropdownMenuButton" style="width: 100%;">`;

                        $.each(result, (key, value) => {                            
                            content += `<a class="dropdown-item" href="javascript: void(0)" onclick="fetchServicePartner(${value.id},'${value.company_name}','${value.person_name}','${value.email}')">${value.person_name} |  ${value.company_name}</a>`;
                        })
                        content += `</div>`;
                        // $($this).parent().after(content);
                    } else {
                        content += `<div class="dropdown-menu show  servicepartner-dropdown select-md" aria-labelledby="dropdownMenuButton"><li class="dropdown-item">No service partner found</li></div>`;
                    }
                    $('#respDropServicePartner').html(content);
                }
            });
        } else {
            $('.servicepartner-dropdown').hide()
        }
    }

    function fetchServicePartner(i,c,p,e){
        $('.servicepartner-dropdown').hide();

        $('#service_partner_id').val(i);
        $('#service_partner_email').val(e);
        $('#service_partner_company_name').val(c);
        $('#service_partner_person_name').val(p);
        $('#service_partner_name').val(p+' | '+c);


    }
</script>
@endsection