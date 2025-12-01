@extends('layouts.app')
@section('content')
@section('page', 'Book a Repair')

<section>   
    <ul class="breadcrumb_menu">  
        <li>Service Partner Management</li>      
        <li><a href="{{ route('repair.list') }}">Repair Request</a> </li>
        <li>Book a Repair</li>
    </ul>
    <div class="row">
        @if (Session::has('message'))
        <div class="alert alert-success" role="alert">
            {{ Session::get('message') }}
        </div>
        @endif
        @if (Session::has('serial'))
        <div class="alert alert-danger" role="alert">
            {{ Session::get('serial') }}
        </div>
        @endif
        <form id="myForm" action="{{ route('customer-point-repair.store-call-request') }}" enctype="multipart/form-data" method="POST">
            @csrf
        <div class="row">
            <div class="col-sm-12">  
                <ul class="pincodeclass">
                    <li>Dealer</li>
                </ul>
                <div class="card shadow-sm">
					<div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Dealer <span class="text-danger">*</span></label>
                                @if(Request::get('dealer_id')==2)
                                <input type="text" autocomplete="off" class="form-control" id="dealer_name" name="dealer_name" value="{{ 'Khosla Electronics Pvt. Ltd.', old('dealer_name') }}" >
                                <input type="hidden" name="dealer_id" id="dealer_id" class="" value="{{Request::get('dealer_id'), old('dealer_id') }}">
                                @else
                                <input type="text" autocomplete="off" class="form-control" id="dealer_name" onkeyup="searchDealerUser(this.value);" placeholder="Search dealer user ... " name="dealer_name" value="{{ old('dealer_name') }}" >
                                <input type="hidden" name="dealer_id" id="dealer_id" class="" value="{{Request::get('dealer_id'), old('dealer_id') }}">
                                @endif

                                <input type="hidden" name="dealer_type" id="dealer_type" class="" value="{{Request::get('dealer_type'), old('dealer_type') }}">
                                
                                <div class="respDropDealer" id="respDropDealer" style="position: relative;"></div>
                                @error('dealer_id') <p class="small text-danger">{{ $message }}</p> @enderror                                
                            </div>
                        </div> 
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">PIN Code<span class="text-danger">*</span></label>
                                <input type="text" autocomplete="off" placeholder="Enter PIN Code" maxlength="6" name="pincode" class="form-control" onkeyup="getServicePartners(this.value);" id="pincode" onkeypress="validateNum(event)" value="{{Request::get('pincode'), old('pincode') }}">   
                                @error('pincode') <p class="small text-danger">{{ $message }}</p> @enderror                              
                            </div>
                        </div>  
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Assigned Service Partner </label>
                                <input type="hidden" name="service_partner_id" id="service_partner_id" value="{{old('service_partner_id')}}">
                                <input type="hidden" name="service_partner_email" id="service_partner_email" value="{{old('service_partner_email')}}">
                                <input type="hidden" name="service_partner_person_name" id="service_partner_person_name" value="{{old('service_partner_person_name')}}">
                                <input type="hidden" name="service_partner_company_name" id="service_partner_company_name" value="{{old('service_partner_company_name')}}">
                                <input type="text" autocomplete="off" name="service_partner_name" class="form-control" id="service_partner_name" value="{{old('service_partner_name')}}" onkeyup="searchServicePartner(this.value);" placeholder="Search service partner ... "> 

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
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Name <span class="text-danger">*</span></label>
                                <input type="text" autocomplete="off" placeholder="Enter Customer Full Name" name="customer_name" class="form-control" maxlength="250" id="" value="{{Request::get('customer_name'), old('customer_name') }}">  
                                @error('customer_name') <p class="small text-danger">{{ $message }}</p> @enderror                                  
                            </div>
                        </div> 
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Mobile No <span class="text-danger">*</span></label>
                                <input type="text" autocomplete="off" placeholder="Enter Customer Mobile No" name="customer_phone" class="form-control" maxlength="10" id="customer_phone" value="{{Request::get('mobile'), old('customer_phone') }}">    
                                @error('customer_phone') <p class="small text-danger">{{ $message }}</p> @enderror                                
                            </div>
                        </div>                                                                                      
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Alternate Mobile No <span class="text-danger">*</span></label>
                                <input type="text" autocomplete="off" placeholder="Enter Customer Alternate Mobile No" name="customer_alternate_phone" class="form-control" maxlength="10" id="customer_alternate_phone" value="{{ old('customer_alternate_phone') }}">    
                                @error('customer_alternate_phone') <p class="small text-danger">{{ $message }}</p> @enderror                                
                            </div>
                        </div>    
                        <div class="col-md-1">
                            <div class="form-group">
                                <label for="sameAsPhone">Same as Mobile</label><br/>
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" id="sameAsPhone" onchange="copyPhone()">                              
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
                                <textarea name="address" placeholder="Enter Address" class="form-control" id="" cols="1" rows="1">{{Request::get('address'), old('address') }}</textarea>    
                                @error('address') <p class="small text-danger">{{ $message }}</p> @enderror                            
                            </div>
                        </div> 
						</div>                                    
                    </div>                      
                </div>
                <ul class="pincodeclass">
                    <li>Bill</li>
                </ul>  
                <div class="card shadow-sm">
					<div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Bill No<span class="text-danger">*</span></label>
                                <input type="text" autocomplete="off" placeholder="Enter Bill No" name="bill_no" class="form-control" maxlength="100" id="" value="{{Request::get('bill_no'), old('bill_no') }}">        
                                @error('bill_no') <p class="small text-danger">{{ $message }}</p> @enderror                           
                            </div>
                        </div> 
                      
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Invoice/Bill Date<span class="text-danger">*</span></label>
                                <input type="date" max="{{date('Y-m-d')}}" name="order_date" class="form-control" id="order_date" value="{{Request::get('bill_date'), old('order_date') }}" @if(Request::get('bill_date')) readonly @endif>  
                                @error('order_date') <p class="small text-danger">{{ $message }}</p> @enderror                                   
                            </div>
                        </div>  
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Bill Value </label>        
                                <input type="text" autocomplete="off" placeholder="Enter Bill Value" name="product_value" class="form-control" id="" value="{{ old('product_value') }}" > 
                                @error('product_value') <p class="small text-danger">{{ $message }}</p> @enderror                                   
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
                        <div class="col-md-9">
                            <div class="form-group">
                                <label for="">Name <span class="text-danger">*</span></label>
                                <input type="hidden" name="product_id" id="product_id" value="{{Request::get('product_id')}}">
                                <input type="hidden" name="product_type" id="product_type" value="{{Request::get('product_type')}}">
                                <input type="text" autocomplete="off" placeholder="Enter Product Name"  name="product_name" maxlength="200" class="form-control" id="product_name" value="{{ Request::get('product_name') }}" readonly>
                                <div class="respDropProduct" id="respDropProduct" style="position: relative;"></div>
                                @error('product_name') <p class="small text-danger">{{ $message }}</p> @enderror   
                            </div>
                        </div>  
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Serial No<span class="text-danger">*</span></label>
                                <input type="text" autocomplete="off" placeholder="Enter Product Serial No" name="product_sl_no" maxlength="100" class="form-control" id="" value="{{ Request::get('serial') }}" readonly> 
                                @error('product_sl_no') <p class="small text-danger">{{ $message }}</p> @enderror  
                                @if($repeat_call === 1)  <p class="small text-danger">Repeat Call</p> @endif
                            </div>
                        </div>                                  
                    </div>     
					</div>
                </div>
                <div class="card shadow-sm">
					<div class="card-body" id="div_warranty">

					</div>
                </div>
                <div class="card shadow-sm">
					<div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Upload Snapshot (If Any) @if (!Request::has('dealer_type')) <span class="text-danger">*</span> @endif </label>
                                <input type="file" name="filename" class="form-control" id="" accept="image/*">
                                @error('filename') <p class="small text-danger">{{ $message }}</p> @enderror                                 
                            </div>
                        </div> 
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Remarks <span class="text-danger">*</span></label>
                                <textarea name="remarks" class="form-control" id="" cols="3" rows="5">{{ old('remarks') }}</textarea>
                                @error('remarks') <p class="small text-danger">{{ $message }}</p> @enderror                                 
                            </div>
                        </div>                                                         
                    </div>   
					</div>
                </div>
                            
                <div class="card shadow-sm">
                    <div class="card-body text-end">
                        <a href="{{route('customer-point-repair.check-product-details')}}" class="btn btn-sm btn-danger">Back</a>
                        <button type="submit" id="submitBtn" class="btn btn-sm btn-success">Submit </button>
                    </div>
                </div>                                            
            </div>              
        </div>
                 
        </form>             
    </div>    
</section>
<script>
    $("#myForm").submit(function() {
        $('input').attr('readonly', 'readonly');
        $('#submitBtn').attr('disabled', 'disabled');   
        $('#submitBtn').html('<i class="fi fi-br-refresh"></i>');     
        return true;
    });

    $('#order_date').on('change', function(){
        var product_id = $('#product_id').val();
        var order_date = $(this).val();
        if(product_id != ''){
            fetchProduct(product_id, order_date);
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
                    var content = '';
                    if (result.length > 0) {
                        content += `<div class="dropdown-menu show  dealer-dropdown select-md" aria-labelledby="dropdownMenuButton" style="width: 100%;">`;

                        $.each(result, (key, value) => {                            
                            content += `<a class="dropdown-item" href="javascript: void(0)" onclick="fetchDealer(${value.id},'${value.name}','${value.dealer_type}')">${value.name} </a>`;
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

    function fetchDealer(id,name,dealer_type) {
        $('.dealer-dropdown').hide()
        $('#dealer_id').val(id);
        $('#dealer_name').val(name);
        $('#dealer_type').val(dealer_type);
        $('#order_date').val('');
        $('#div_warranty').html('');
    }

    function fetchProduct(id,order_date) {
        $('.product-dropdown').hide();
        $('#div_warranty').show();

        
        var dealer_type = $('#dealer_type').val();

        $.ajax({
            url: "{{ route('ajax.get-product-warranty-status') }}",
            method: 'post',
            data: {
                '_token': '{{ csrf_token() }}',
                id:id,
                order_date:order_date,
                dealer_type: dealer_type,
                to_date: "{{date('Y-m-d')}}"
            },
            success: function(result) {
                var html = "";
                var dealer_text_type = dealer_type=='khosla' ? "Khosla" : "Non Khosla";
                if(result.status === true && result.data.length > 0) {
                    html += `<div class="card shadow-sm">
                                <div class="card-header bg-light">
                                    <span class="badge bg-secondary">Dealer Type: <span id="dealer_text_type">${dealer_text_type}<span></span>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm table-borderless">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Warranty Type</th>
                                                <th>Warranty Period (Months)</th>
                                                <th>Warranty End Date</th>
                                                <th>Warranty Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>`;

                    $.each(result.data, function(key, item) {
                        html += `<tr>
                                    <td> ${item.warranty_type.charAt(0).toUpperCase() + item.warranty_type.slice(1)}`;

                        // Checking if warranty_type is "additional"
                        if(item.warranty_type === "additional") {
                            html += ` <span class="badge bg-danger" style="cursor: pointer; font-size: 9px;">
                                        ${item.additional_warranty_type == 1 ? "Parts Chargeable" : "Service Chargeable"}
                                    </span>`;
                        }

                        // Checking if warranty_type is "cleaning"
                        if(item.warranty_type === "cleaning") {
                            html += ` <span class="badge bg-danger" style="cursor: pointer; font-size: 9px;" title="Number of cleaning">
                                        ${item.number_of_cleaning}
                                    </span>`;
                        }

                        // Adding spear_goods if available
                        if(item.parts) {
                            html += ` <span class="badge bg-success"> ${item.parts}</span>`;
                        }

                        html += `</td>
                                <td> <span class="badge bg-success">${item.warranty_period}</span></td>
                                <td> <span class="badge bg-${item.warranty_status==="YES"?"success":"danger"}">${item.warranty_end_date}</span></td>
                                <td> <span class="badge bg-${item.warranty_status==="YES"?"success":"danger"}">${item.warranty_status}</span></td>
                                </tr>`;
                    });

                    html += `</tbody>
                            </table>
                            </div>
                            </div>`;
                    
                    // Inject the generated HTML into the DOM element
                    $('#div_warranty').html(html);
                }else{
                    $('#div_warranty').html('');
                }
            }
        });   
          
    }

    function getServicePartners(evt){      
        var product_type = $('#product_type').val();  
        $.ajax({
            url: "{{ route('ajax.get-service-partner-by-pincode') }}",
            method: 'post',
            data: {
                '_token': '{{ csrf_token() }}',
                product_type:product_type,
                pincode:evt,
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
                    
                    $('#service_partner_id').val(service_partner_id);
                    $('#service_partner_name').val(person_name+' | '+company_name);
                    $('#service_partner_email').val(service_partner_email);
                    $('#service_partner_company_name').val(company_name);
                    $('#service_partner_person_name').val(person_name);
                    $('#respDropServicePartner').html("");
                } else {
                    content = `<div class="dropdown-menu show  servicepartner-dropdown select-md" aria-labelledby="dropdownMenuButton"><li class="dropdown-item">No service partner found</li></div>`;
                    $('#respDropServicePartner').html(content);
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

    $("input:checkbox#is_repeated").change(function() {
        var ischecked= $(this).is(':checked');
        
        if(ischecked){
            $('input').attr('readonly', 'readonly');
        }else{
            $('input').removeAttr('readonly');
           
        }       
    });

    function copyPhone() {
        var checkbox = document.getElementById('sameAsPhone');
        var phoneField = document.getElementById('customer_phone');
        var alternatePhoneField = document.getElementById('customer_alternate_phone');
        
        if (checkbox.checked) {
            alternatePhoneField.value = phoneField.value;
            $('#customer_alternate_phone').attr('readonly', 'readonly');
        } else {
            alternatePhoneField.value = ''; // Clear the alternate phone field if unchecked
            $('#customer_alternate_phone').removeAttr('readonly');
        }
    }

    window.onload = function() {
        const pincode = document.getElementById('pincode').value;
        if (pincode) {
            getServicePartners(pincode);
        }
        const product_id = $('#product_id').val();
        var order_date = $('#order_date').val();
        if(product_id != '') {
            fetchProduct(product_id, order_date);
        }
    };

</script>
@endsection