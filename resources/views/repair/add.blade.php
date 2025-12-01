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
        <form id="myForm" action="{{ route('repair.save') }}" enctype="multipart/form-data" method="POST">
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
                                <input type="text" autocomplete="off" class="form-control" id="dealer_name" onkeyup="searchDealerUser(this.value);" placeholder="Search dealer user ... " name="dealer_name" value="{{ old('dealer_name') }}" >
                                <input type="hidden" name="dealer_id" id="dealer_id" class="" value="{{ old('dealer_id') }}">
                                <input type="hidden" name="dealer_type" id="dealer_type" class="" value="{{ old('dealer_type') }}">
                                
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
                <ul class="pincodeclass">
                    <li>Bill</li>
                </ul>  
                <div class="card shadow-sm">
					<div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Bill No<span class="text-danger">*</span></label>
                                <input type="text" autocomplete="off" placeholder="Enter Bill No" name="bill_no" class="form-control" maxlength="100" id="" value="{{ old('bill_no') }}">        
                                @error('bill_no') <p class="small text-danger">{{ $message }}</p> @enderror                           
                            </div>
                        </div> 
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Order Date<span class="text-danger">*</span></label>
                                <input type="date" max="{{date('Y-m-d')}}" name="order_date" class="form-control" id="order_date" value="{{ old('order_date') }}">  
                                @error('order_date') <p class="small text-danger">{{ $message }}</p> @enderror                                   
                            </div>
                        </div>  
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Bill Value </label>
                                <input type="text" autocomplete="off" placeholder="Enter Bill Value" name="product_value" class="form-control" id="" value="{{ old('product_value') }}"> 
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
                                <input type="hidden" name="product_id" id="product_id" value="{{old('product_id')}}">
                                <input type="text" autocomplete="off" placeholder="Enter Product Name" onkeyup="searchProduct(this.value);"  name="product_name" maxlength="200" class="form-control" id="product_name" value="{{ old('product_name') }}">
                                <div class="respDropProduct" id="respDropProduct" style="position: relative;"></div>
                                @error('product_name') <p class="small text-danger">{{ $message }}</p> @enderror   
                            </div>
                        </div>  
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Serial No<span class="text-danger">*</span></label>
                                <input type="text" autocomplete="off" placeholder="Enter Product Serial No" name="product_sl_no" maxlength="100" class="form-control" id="" value="{{ old('product_sl_no') }}"> 
                                @error('product_sl_no') <p class="small text-danger">{{ $message }}</p> @enderror                                 
                            </div>
                        </div>                                  
                    </div>     
					</div>
                </div>
                <div class="card shadow-sm" id="div_warranty">
					<div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Warranty Status</label>
                                <input type="text" autocomplete="off" name="warranty_status" class="form-control" readonly id="warranty_status" value="{{ old('warranty_status') }}">  
                            </div>
                        </div>  
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Warranty Period (in month)</label>
                                <input type="text" autocomplete="off" name="warranty_period" class="form-control" readonly id="warranty_period" value="{{ old('warranty_period') }}">  
                            </div>
                        </div> 
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Warranty Date</label>
                                <input type="date" name="warranty_date" class="form-control" readonly id="warranty_date" value="{{ old('warranty_date') }}">  
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Out of Warranty</label>
                                <span class="" id="out_of_warranty_span"></span>
                                <input type="hidden" name="out_of_warranty" class="form-control" readonly id="out_of_warranty" value="{{ old('out_of_warranty') }}">  
                            </div>
                        </div>                               
                    </div>     
					</div>
                </div>
                <div class="card shadow-sm">
					<div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Upload Snapshot (If Any) </label>
                                <input type="file" name="filename" class="form-control" id="" accept="image/*">
                                @error('filename') <p class="small text-danger">{{ $message }}</p> @enderror                                 
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

                
                @if ( $errors->has('repeated'))
                    
                
                <div class="card shadow-sm">
					<div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" id="is_repeated" name="is_repeated" title="Set As Test Product">
                                    <label class="form-check-label" for="is_repeated">
                                        Set As Repeated Call
                                    </label>
                                </div>
                            </div> 
                                                                                
                        </div>   
					</div>
                </div>   
               
                @endif
                            
                <div class="card shadow-sm">
                    <div class="card-body text-end">
                        <a href="{{route('repair.list')}}" class="btn btn-sm btn-danger">Back</a>
                        <button type="submit" id="submitBtn" class="btn btn-sm btn-success">Submit </button>
                    </div>
                </div>                                            
            </div>              
        </div>
                 
        </form>             
    </div>    
</section>
<script>
    $(document).ready(function(){
        $('#div_warranty').hide();
        var old_warranty_status = "{{ old('warranty_status') }}";
        if(old_warranty_status != ''){
            $('#div_warranty').show();
        } else {
            $('#div_warranty').hide();
        }
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
          
    }

    function searchProduct(search){
        if(search.length > 0) {
            $.ajax({
                url: "{{ route('ajax.search-product-by-type') }}",
                method: 'post',
                data: {
                    '_token': '{{ csrf_token() }}',
                    search: search,
                    type: 'fg',
                    goods_type: ['general','gas_stove','ac','gieger']
                },
                success: function(result) {
                    var content = '';
                    if (result.length > 0) {
                        content += `<div class="dropdown-menu show  product-dropdown select-md" aria-labelledby="dropdownMenuButton" style="width: 100%;">`;

                        $.each(result, (key, value) => {                            
                            content += `<a class="dropdown-item" href="javascript: void(0)" onclick="fetchProduct(${value.id})">${value.title}</a>`;
                        })
                        content += `</div>`;
                        // $($this).parent().after(content);
                    } else {
                        content += `<div class="dropdown-menu show  product-dropdown select-md" aria-labelledby="dropdownMenuButton"><li class="dropdown-item">No product found</li></div>`;
                    }
                    $('#respDropProduct').html(content);
                }
            });
        } else {
            $('.product-dropdown').hide()
        }
        
    }

    function fetchProduct(id) {
        $('.product-dropdown').hide();
        $('#div_warranty').show();

        var order_date = $('#order_date').val();
        var dealer_type = $('#dealer_type').val();

        $.ajax({
            url: "{{ route('ajax.get-single-product') }}",
            method: 'post',
            data: {
                '_token': '{{ csrf_token() }}',
                id:id,
                order_date:order_date,
                dealer_type: dealer_type
            },
            success: function(result) {
                var title = result.title;
                var unique_id = result.unique_id;
                var warranty_status = result.warranty_status;
                var warranty_period = result.warranty_period;
                var warranty_date = result.warranty_date;
                var out_of_warranty = result.out_of_warranty;
                
                
                $('#product_id').val(id);
                $('#product_name').val(title);
                $('#warranty_status').val(warranty_status);
                $('#warranty_period').val(warranty_period);
                $('#warranty_date').val(warranty_date);
                $('#out_of_warranty').val(out_of_warranty);
                $('#out_of_warranty_span').text(out_of_warranty);
                if(out_of_warranty == 'Yes'){
                    $('#out_of_warranty_span').addClass('badge bg-danger');
                } else {
                    $('#out_of_warranty_span').addClass('badge bg-success');
                }
                
            }
        });   
          
    }

    function getServicePartners(evt){        
        $.ajax({
            url: "{{ route('ajax.get-service-partner-by-pincode') }}",
            method: 'post',
            data: {
                '_token': '{{ csrf_token() }}',
                pincode:evt
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
</script>
@endsection