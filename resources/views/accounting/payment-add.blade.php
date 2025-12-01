@extends('layouts.app')
@section('content')
@section('page', 'Add Payment')
<section>   
    <ul class="breadcrumb_menu">  
        <li>Accounting</li>      
        <li><a href="{{ route('accounting.payment-list') }}">Payment</a> </li>
        <li>Add</li>    
    </ul>
    <div class="row">
        @if (Session::has('message'))
        <div class="alert alert-success" role="alert">
            {{ Session::get('message') }}
            {{ Session::forget('message') }}
        </div>
        @endif
        <form id="myForm" action="{{ route('accounting.payment-save') }}" method="POST">
            @csrf
        <div class="row">
            <div class="col-sm-12">            
                <div class="card shadow-sm">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">ID <span class="text-danger">*</span></label>
                                <input type="text" name="voucher_no" class="form-control" autocomplete="off" value="{{ 'PAYT'.genAutoIncreNoYearWiseOrder(5,'payments',date('Y'),date('m')) }}" readonly >
                            </div>
                        </div> 
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="entry_date">Date <span class="text-danger">*</span></label>
                                <input type="date" name="entry_date" class="form-control" id="entry_date" value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}">
                            </div>
                        </div> 
                       <!-- <div class="col-md-4">
                            <div class="form-group">
                                <label for="user_type">User Type <span class="text-danger">*</span></label>
                                <select name="user_type" class="form-control" id="user_type">
                                     <option value="">Choose user type</option> 
                                    <option value="servicepartner" selected>Service Partner</option>
									<option value="ho_sale" selected>Ho Sale</option>
                                </select>
                            </div>
                        </div>    -->     
						<div class="col-md-4">
					<label for="">User Type</label>
					<div>
						<label class="me-3">
							<input type="radio" name="user_type" value="servicepartner" {{ old('user_type') == 'servicepartner' ? 'checked' : '' }}> Service Partner
						</label>
						<label>
							<input type="radio" name="user_type" value="ho_sale" {{ old('user_type') == 'ho_sale' ? 'checked' : '' }}> Ho Sale
						</label>
					</div>
				</div>
                    </div>                      
                </div>

                <div class="card shadow-sm">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="">Service Partner <span class="text-danger">*</span></label>
                                <select name="service_partner_id" class="form-control" id="">
                                    <option value="" hidden selected>Choose one</option>
                                    @foreach ($service_partners as $sp)
                                        <option value="{{ $sp->id }}" @if((old('service_partner_id')) == $sp->id) selected @endif>{{ $sp->person_name }} - {{ $sp->company_name }}</option>
                                    @endforeach
                                </select>
                                @error('service_partner_id') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div> 
						<!--Ho Sale-->
						<div class="col-md-3">
                            <div class="form-group">
                                <label for="">Ho Sale <span class="text-danger">*</span></label>
                                <select name="ho_sale_id" class="form-control" id="">
                                    <option value="" hidden selected>Choose one</option>
                                    @foreach ($ho_sales as $ho)
                                        <option value="{{ $ho->id }}" @if((old('ho_sale_id')) == $ho->id) selected @endif>{{ $ho->name }} </option>
                                    @endforeach
                                </select>
                                @error('ho_sale_id') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div> 
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="amount">Amount <span class="text-danger">*</span></label>
                                <input type="amount" name="amount" class="form-control" id="amount" placeholder="Enter amount" value="{{ old('amount') }}" onkeypress="validateNum(event)" maxlength="10" autocomplete="off">
                                @error('amount') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div> 
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="payment_mode">Mode Of Payment <span class="text-danger">*</span></label>
                                <select name="payment_mode" class="form-control" id="payment_mode">
                                    <option value="" hidden selected>Select an option</option>
                                    <option value="cash" @if(old('payment_mode') == 'cash') selected @endif>Cash</option>
                                    <option value="neft" @if(old('payment_mode') == 'neft') selected @endif>NEFT</option>
                                    <option value="cheque" @if(old('payment_mode') == 'cheque') selected @endif>Cheque</option>
                                </select>
                                @error('payment_mode') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>                                                        
                    </div>                      
                </div>

                <div class="card shadow-sm" id="bank_div">
                    <div class="row">                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Bank Name  <span class="text-danger">*</span> </label>
                                <div id="bank_search" style="position: relative;">
                                    <input type="text" id="" autocomplete="off" placeholder="Search Bank" name="bank_name" value="{{ old('bank_name_hidden') }}" onkeyup="getBankList(this.value);" class="form-control bank_name" maxlength="200">
                                    <input type="hidden" class="form-control"  name="bank_name_hidden" value="{{ old('bank_name_hidden') }}"  id="bank_name_hidden">
                                    @error('bank_name') <p class="small text-danger">{{ $message }}</p> @enderror
                                    <div class="respDropBank" id="respDropBank"></div>
                                </div>
                                <div id="bank_custom" style="display: none;">
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control" autocomplete="off" name="bank_name" value="{{ old('bank_name') }}">
                                        <div class="input-group-append">
                                          <a class="btn btn-outline-secondary" id="allbankothers"><i class="fi fi-br-refresh"></i></a>
                                        </div>
                                    </div>
                                </div>                          
                            </div>
                            
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="chq_utr_no">Chqeue / UTR No  <span class="text-danger">*</span> </label>
                                <input type="text" name="chq_utr_no" class="form-control" autocomplete="off" id="chq_utr_no" placeholder="Enter cheque or UTR number" value="{{ old('chq_utr_no') }}">    
                                @error('chq_utr_no') <p class="small text-danger">{{ $message }}</p> @enderror                            
                            </div>
                        </div>                                        
                    </div>                      
                </div>
                
                <div class="card shadow-sm">
                    <div class="row">                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="narration">Narration </label>
                                <textarea name="narration" class="form-control" id="" cols="3" rows="3" placeholder="Enter some narration">{{old('narration')}}</textarea>
                                
                            </div>
                        </div>                                        
                    </div>                      
                </div>                
                <div class="card shadow-sm">
                    <div class="card-body text-end">
                        <a href="{{ route('accounting.payment-list') }}" class="btn btn-sm btn-danger">Back</a>
                        <button type="submit" id="submitBtn" class="btn btn-sm btn-success">Create </button>
                    </div>
                </div>                                            
            </div>              
        </div>
                 
        </form>             
    </div>    
</section>
<script>
    $(document).ready(function(){
		 // Default hide both dropdowns first
    $('select[name="service_partner_id"]').closest('.col-md-5').hide();
    $('select[name="ho_sale_id"]').closest('.col-md-3').hide();

    // On page load, check old selected value
    var old_user_type = "{{ old('user_type') }}";
    if(old_user_type === 'servicepartner'){
        $('select[name="service_partner_id"]').closest('.col-md-5').show();
        $('select[name="ho_sale_id"]').closest('.col-md-3').hide();
    } else if(old_user_type === 'ho_sale'){
        $('select[name="ho_sale_id"]').closest('.col-md-3').show();
        $('select[name="service_partner_id"]').closest('.col-md-5').hide();
    }

    // When user type radio changes
    $('input[name="user_type"]').change(function(){
        var userType = $(this).val();
        if(userType === 'servicepartner'){
            $('select[name="service_partner_id"]').closest('.col-md-5').show();
            $('select[name="ho_sale_id"]').closest('.col-md-3').hide();
        } else if(userType === 'ho_sale'){
            $('select[name="ho_sale_id"]').closest('.col-md-3').show();
            $('select[name="service_partner_id"]').closest('.col-md-5').hide();
        }
    });
		
		
        $('#bank_div').hide();
        var old_payment_mode = "{{ old('payment_mode') }}";
        // alert(old_payment_mode);
        if(old_payment_mode != ''){
            if(old_payment_mode != 'cash'){
                $('#bank_div').show();
            } else{
                $('#bank_div').hide();
            }
        } else {
            $('#bank_div').hide();
        }
        
    });

    $('#payment_mode').change(function(){
        if($('#payment_mode').val() != 'cash'){
            $('#bank_div').show();
        } else{
            $('#bank_div').hide();
        }
    })

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
        var regex = /[0-9]|\./;
        if( !regex.test(key) ) {
            theEvent.returnValue = false;
            if(theEvent.preventDefault) theEvent.preventDefault();
        }
    }

    function getBankList(evt)
    {        
        if(evt.length > 0){
            
            $.ajax({
                url: "{{ route('ajax.get-bank-list') }}",
                method: 'post',
                data: {
                    '_token': '{{ csrf_token() }}',
                    search: evt
                },
                success: function(result) {                    
                    var content = '';
                    if (result.length > 0) {
                        content += `<div class="dropdown-menu show  bankname-dropdown" aria-labelledby="dropdownMenuButton" style="width:100%; padding: 0;">`;

                        $.each(result, (key, value) => {
                            content += `<a class="dropdown-item" href="javascript: void(0)" onclick="fetchBankName('${value.name}')">${value.name}</a>`;
                        })
                        content += `</div>`;
                        // $($this).parent().after(content);
                    } else {
                        content += `<div class="dropdown-menu show  bankname-dropdown" aria-labelledby="dropdownMenuButton"><li class="dropdown-item">No bank found</li></div>`;
                    }
                    $('.respDropBank').html(content);
                }
            });
        }else{
            $('.respDropBank').text('');
            
        }
    }

    function fetchBankName(name)
    {
        if(name != ' - OTHERS -'){
            $('.bankname-dropdown').hide();           
            $('input[name="bank_name"').val(name)
            $('input[name="bank_name_hidden"').val(name)
        }else{
            $('#bank_search').hide();
            $('#bank_custom').show();
        }
        
    }  

    $('#allbankothers').on('click', function(){
        
        $('#bank_custom').hide();
        $('#bank_search').show();
    });
</script>
@endsection