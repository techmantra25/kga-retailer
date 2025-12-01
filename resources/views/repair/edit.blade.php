@extends('layouts.app')
@section('content')
@section('page', 'Repair - Edit Call')
<section>   
    <ul class="breadcrumb_menu">  
        <li>Service Partner Management</li>      
        <li><a href="{{ route('repair.list') }}">Repair Request</a> </li>
        <li>Edit Call</li>
    </ul>
    <div class="row">
        <form id="myForm" action="{{ route('repair.update', [$idStr,$getQueryString]) }}" enctype="multipart/form-data" method="POST">
            @csrf
            <input type="hidden" name="entry_date" value="{{ date('Y-m-d') }}">
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
                                <label for="">Dealer </label>
                                @if (old('dealer_id'))
                                    <input type="text" class="form-control" id="dealer_name" onkeyup="searchDealerUser(this.value);" placeholder="Search dealer user ... " name="dealer_name" value="{{ old('dealer_name') }}">
                                    <input type="hidden" name="dealer_id" id="dealer_id" value="{{ old('dealer_id') }}">
                                @else
                                    <input type="text" class="form-control" id="dealer_name" onkeyup="searchDealerUser(this.value);" placeholder="Search dealer user ... " name="dealer_name" value="{{ (!empty($data->dealer_id))?getSingleAttributeTable('dealers','id',$data->dealer_id,'name'):'' }}">
                                    <input type="hidden" name="dealer_id" id="dealer_id" value="{{$data->dealer_id}}">
                                @endif
                                
                                
                                <div class="respDropDealer" id="respDropDealer" style="position: relative;"></div>
                                @error('dealer_id') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div> 
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="">PIN Code<span class="text-danger">*</span></label>
                                @if (old('pincode'))
                                    <input type="text" placeholder="Enter PIN Code" maxlength="6" name="pincode" class="form-control" onkeyup="getServicePartners(this.value);" id="pincode" value="{{ old('pincode') }}" onkeypress="validateNum(event)"> 
                                    @error('pincode') <p class="small text-danger">{{ $message }}</p> @enderror  
                                @else
                                    <input type="text" placeholder="Enter PIN Code" maxlength="6" name="pincode" class="form-control" onkeyup="getServicePartners(this.value);" id="pincode" value="{{$data->pincode}}" onkeypress="validateNum(event)"> 
                                @endif
                                  
                                                            
                            </div>
                        </div>  
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Assigned Service Partner <span class="text-danger">*</span></label>
                                @if (old('service_partner_id'))
                                    <input type="hidden" name="service_partner_id" id="service_partner_id" value="{{old('service_partner_id')}}">
                                    <input type="hidden" name="service_partner_email" id="service_partner_email" value="{{old('service_partner_email') }}">
                                    <input type="hidden" name="service_partner_person_name" id="service_partner_person_name" value="{{ old('service_partner_person_name') }}">
                                    <input type="hidden" name="service_partner_company_name" id="service_partner_company_name" value="{{ old('service_partner_company_name') }}">
                                    <input type="text" name="service_partner_name" class="form-control" id="service_partner_name" value="{{ old('service_partner_person_name') }} | {{ old('service_partner_company_name') }}" onkeyup="searchServicePartner(this.value);" placeholder="Search service partner ... ">
                                    @error('service_partner_id') <p class="small text-danger">{{ $message }}</p> @enderror 
                                @else
                                    <input type="hidden" name="service_partner_id" id="service_partner_id" value="{{$data->service_partner_id}}">
                                    <input type="hidden" name="service_partner_email" id="service_partner_email" value="{{$data->service_partner_email  }}">
                                    <input type="hidden" name="service_partner_person_name" id="service_partner_person_name" value="{{ getSingleAttributeTable('service_partners','id',$data->service_partner_id,'person_name')  }}">
                                    <input type="hidden" name="service_partner_company_name" id="service_partner_company_name" value="{{ getSingleAttributeTable('service_partners','id',$data->service_partner_id,'company_name') }}">
                                    <input type="text" name="service_partner_name" class="form-control" id="service_partner_name" value="{{ getSingleAttributeTable('service_partners','id',$data->service_partner_id,'person_name') }} | {{ getSingleAttributeTable('service_partners','id',$data->service_partner_id,'company_name') }}" onkeyup="searchServicePartner(this.value);" placeholder="Search service partner ... ">
                                @endif
                                 

                                <div class="respDropServicePartner" id="respDropServicePartner" style="position: relative;"></div>

                                  
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
                                <input type="text" placeholder="Enter Customer Full Name" name="customer_name" class="form-control" maxlength="250" id="" @if(old('customer_name')) value="{{ old('customer_name') }}"  @else value="{{ $data->customer_name }}" @endif > 
                                @error('customer_name') <p class="small text-danger">{{ $message }}</p> @enderror  
                                                            
                            </div>
                        </div> 
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Mobile No <span class="text-danger">*</span></label>
                                <input type="text" placeholder="Enter Customer Mobile No" name="customer_phone" class="form-control" maxlength="10" id="" @if(old('customer_phone')) value="{{ old('customer_phone') }}"  @else  value="{{ $data->customer_phone }}" @endif > 
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
                                <textarea name="address" placeholder="Enter Address" class="form-control" id="" cols="1" rows="1" >@if(old('address')) {{ old('address') }} @else  {{ $data->address }} @endif</textarea>   
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
                                <input type="text" placeholder="Enter Bill No" name="bill_no" class="form-control" maxlength="100" id="" value="{{ $data->bill_no }}" disabled>        
                                                        
                            </div>
                        </div> 
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Order Date<span class="text-danger">*</span></label>
                                <input type="date" max="{{date('Y-m-d')}}" name="order_date" class="form-control" id="" value="{{ $data->order_date }}" > 
                                @error('order_date') <p class="small text-danger">{{ $message }}</p> @enderror  
                                                               
                            </div>
                        </div>  
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Bill Value </label>
                                <input type="text" placeholder="Enter Bill Value" name="product_value" class="form-control" id="" value="{{ $data->product_value }}" disabled> 
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
                                <input type="hidden" name="product_id" id="product_id" value="{{ $data->product_id }}">
                                <input type="text" placeholder="Enter Product Name" onkeyup="searchProduct(this.value);"  name="product_name" maxlength="200" class="form-control" id="product_name" value="{{ $data->product_name }}" disabled>
                                  
                            </div>
                        </div>  
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Serial No<span class="text-danger">*</span></label>
                                <input type="text" placeholder="Enter Product Serial No" name="product_sl_no" maxlength="100" class="form-control" id="" value="{{ $data->product_sl_no }}" disabled> 
                                                             
                            </div>
                        </div>                                  
                    </div>    
					</div>
                </div>
                <div class="card shadow-sm" id="div_warranty">
					<div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Warranty Status</label>
                                <input type="text" name="warranty_status" class="form-control" disabled id="warranty_status" value="{{ $data->warranty_status }}">  
                            </div>
                        </div>  
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Warranty Period (in month)</label>
                                <input type="text" name="warranty_period" class="form-control" disabled id="warranty_period" value="{{ $data->warranty_period }}">  
                            </div>
                        </div> 
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Warranty Date</label>
                                <input type="date" name="warranty_date" class="form-control" disabled id="warranty_date" value="{{ $data->warranty_date }}">  
                            </div>
                        </div>                               
                    </div>   
					</div>
                </div>
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
            
    });
    $("#myForm").submit(function() {
        $('input').attr('readonly', 'readonly');
        $('#submitBtn').attr('disabled', 'disabled');   
        $('#submitBtn').html('<i class="fi fi-br-refresh"></i>');     
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
                    search: search
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