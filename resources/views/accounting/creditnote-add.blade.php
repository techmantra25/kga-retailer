@extends('layouts.app')
@section('content')
@section('page', 'Add Credit Note')
<section>   
    <ul class="breadcrumb_menu">  
        <li>Accounting</li>      
        <li><a href="{{ route('accounting.payment-list') }}">Credit Note</a> </li>
        <li>Add</li>    
    </ul>
    <div class="row">
        @if (Session::has('message'))
        <div class="alert alert-success" role="alert">
            {{ Session::get('message') }}
            {{ Session::forget('message') }}
        </div>
        @endif
        <form id="myForm" action="{{ route('accounting.save-credit-note') }}" method="POST">
            @csrf
        <div class="row">
            <div class="col-sm-12">            
                <div class="card shadow-sm">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">ID <span class="text-danger">*</span></label>
                                <input type="text" name="transaction_id" class="form-control" autocomplete="off" value="{{ 'CREDITNOTE'.genAutoIncreNoYearWiseOrder(5,'credit_note',date('Y'),date('m')) }}" readonly >
                            </div>
                        </div> 
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="entry_date">Date <span class="text-danger">*</span></label>
                                <input type="date" name="entry_date" class="form-control" id="entry_date" value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}">
                            </div>
                        </div> 
						
				<div class="col-md-3">
					<label for="">User Type</label>
					<div>
						<label class="me-3">
							<input type="radio" name="user_type" value="service_partner" {{ old('user_type') == 'service_partner' ? 'checked' : '' }}> Service Partner
						</label>
						<label>
							<input type="radio" name="user_type" value="ho_sale" {{ old('user_type') == 'ho_sale' ? 'checked' : '' }}> Ho Sale
						</label>
					</div>
				</div>

                        
                        <div class="col-md-6 service-partner-group">
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
						<div class="col-md-6 ho-sale-group" style="display:none">
                          <div class="form-group">
                              <label for="">Ho Sale <span class="text-danger">*</span></label>
                              <select name="ho_sale_id" class="form-control" id="">
                                  <option value="" hidden selected>Choose one</option>
                                  @foreach ($ho_sales as $ho)
                                      <option value="{{ $ho->id }}" @if((old('ho_sale_id')) == $ho->id) selected @endif>{{ $ho->name }}</option>
                                  @endforeach
                              </select>
                              @error('ho_sale_id') <p class="small text-danger">{{ $message }}</p> @enderror
                          </div>
                        </div>  
                    </div>                      
                </div>

                <div class="card shadow-sm">
                    <div class="row">
                      <div class="col-md-4">
                        <div class="form-group">
                            <label for="">Call Type <span class="text-danger">*</span> </label>
                            <select name="call_type" class="form-control" id="call_type">
                                <option value="" hidden selected>Choose one</option>
                                <option value="installation" @if(old('call_type') == 'installation') selected @endif>Installation</option>
                                <option value="repair" @if(old('call_type') == 'repair') selected @endif>Repair</option>
								<option value="amc" @if(old('call_type') == 'amc') selected @endif>Amc</option>
                            </select>
                            @error('call_type') <p class="small text-danger">{{ $message }}</p> @enderror
                        </div>
                      </div>
                      <div class="col-md-4 call-no-group">
                        <div class="form-group">
                            <label for="">Call No <span class="text-danger">*</span> </label>
                            <input type="text" name="call_no" class="form-control" placeholder="Please enter call number" value="{{ old('call_no') }}">
                            @error('call_no') <p class="small text-danger">{{ $message }}</p> @enderror
                        </div>
                      </div>
						<!--Amc Unique No-->
					  <div class="col-md-4 amc-group" style="display:none;">
                        <div class="form-group">
                            <label for="">Amc Unique No <span class="text-danger">*</span> </label>
                            <input type="text" name="amc_unique_number" class="form-control" placeholder="Please enter amc unique no" value="{{ old('amc_unique_number') }}">
                            @error('amc_unique_number') <p class="small text-danger">{{ $message }}</p> @enderror
                        </div>
                      </div>
						
                      <div class="col-md-4">
                        <div class="form-group">
                            <label for="amount">Amount <span class="text-danger">*</span></label>
                            <input type="amount" name="amount" class="form-control" id="amount" placeholder="Enter amount" value="{{ old('amount') }}" onkeypress="validateNum(event)" maxlength="10" autocomplete="off">
                            @error('amount') <p class="small text-danger">{{ $message }}</p> @enderror
                        </div>
                      </div> 
                                                                               
                    </div>                      
                </div>

                <div class="card shadow-sm">
                    <div class="row">                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="remarks">Remarks </label>
                                <textarea name="remarks" class="form-control" id="" cols="3" rows="3" placeholder="Enter some remarks">{{old('remarks')}}</textarea>
                                
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
        var regex = /[0-9]|\./;
        if( !regex.test(key) ) {
            theEvent.returnValue = false;
            if(theEvent.preventDefault) theEvent.preventDefault();
        }
    }

</script>

<script>
$(document).ready(function() {
    
    toggleUserTypeFields();

    $('input[name="user_type"]').change(function() {
        toggleUserTypeFields();
    });

    function toggleUserTypeFields() {
        const selected = $('input[name="user_type"]:checked').val();

        if (selected === 'service_partner') {
            $('.service-partner-group').show();
            $('.ho-sale-group').hide();
			
			// reset ho sale field
            $('select[name="ho_sale_id"]').val('');
			
            $('.call-type-group').show();
            $('.call-no-group').show();
            $('.amc-group').hide();
			$('input[name="amc_unique_number"]').val('');
        } 
        else if (selected === 'ho_sale') {
            $('.service-partner-group').hide();
            $('.ho-sale-group').show();
			
			 // reset service partner field
            $('select[name="service_partner_id"]').val('');
			
            $('.call-type-group').hide();
            $('.call-no-group').hide();
            $('.amc-group').show();
			 $('input[name="call_no"]').val('');
        } 
        else {
            $('.service-partner-group, .ho-sale-group, .call-type-group, .call-no-group, .amc-group').hide();
        }
    }

    toggleUserTypeFields();
});
</script>



@endsection