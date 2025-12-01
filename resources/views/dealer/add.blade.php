@extends('layouts.app')
@section('content')
@section('page', 'Dealer')
<section>   
    <ul class="breadcrumb_menu">        
        <li><a href="{{ route('dealers.list') }}">Dealer</a> </li>
        <li>Create</li>
    </ul> 
    <div class="row">
        <div class="col-sm-12">
            <form id="myForm" action="{{ route('dealers.store') }}" method="POST">
                @csrf
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Name <span class="text-danger">*</span> </label>
                                    <input type="text" autocomplete="off" name="name" placeholder="Name" class="form-control" maxlength="100" value="{{old('name')}}">
                                    @error('name') <p class="small text-danger">{{ $message }}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Email <span class="text-danger">*</span> </label>
                                    <input type="email" autocomplete="off" name="email" placeholder="Email" class="form-control" maxlength="100" value="{{old('email')}}">
                                    @error('email') <p class="small text-danger">{{ $message }}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Phone No <span class="text-danger">*</span> </label>
                                    <input type="text" autocomplete="off" name="phone" placeholder="Phone" class="form-control" maxlength="10" value="{{old('phone')}}" onkeypress="validateNum(event)">
                                    @error('phone') <p class="small text-danger">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>  
                    </div>
                </div>                              
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">PAN No <span class="text-danger">*</span> </label>
                                    <input type="text" autocomplete="off" name="pan_no" placeholder="PAN" class="form-control" maxlength="11" value="{{old('pan_no')}}">
                                    @error('pan_no') <p class="small text-danger">{{ $message }}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">GST No <span class="text-danger">*</span> </label>
                                    <input type="text" autocomplete="off" name="gst_no" placeholder="GST No" class="form-control" maxlength="20" value="{{old('gst_no')}}">
                                    @error('gst_no') <p class="small text-danger">{{ $message }}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">License No <span class="text-danger">*</span> </label>
                                    <input type="text" autocomplete="off" name="license_no" placeholder="License No" class="form-control" maxlength="20" value="{{old('license_no')}}">
                                    @error('license_no') <p class="small text-danger">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="">Address <span class="text-danger">*</span> </label>
                                    <input type="text" autocomplete="off" name="address" placeholder="Address" class="form-control" maxlength="200" value="{{old('address')}}">
                                    @error('address') <p class="small text-danger">{{ $message }}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Type <span class="text-danger">*</span> </label>
                                    <select name="dealer_type" class="form-control" id="dealer_type">
                                        <option value="khosla" @if(old('dealer_type') == 'khosla') selected @endif>Khosla</option>
                                        <option value="nonkhosla" @if(old('dealer_type') == 'nonkhosla') selected @endif>Non Khosla</option>
                                    </select>
                                    
                                </div>
                            </div>                      
                        </div>
                    </div>
                </div>
                <div class="card shadow-sm">
                    <div class="card-body text-end">
                        <div class="row align-items-center">
                            <div class="col"><span>** <strong>secret</strong> is default password for accessing mobile app </span></div>
                            <div class="col-auto">
                            <a href="{{route('dealers.list')}}" class="btn btn-sm btn-danger">Back</a>
                        <button id="submitBtn" type="submit" class="btn btn-sm btn-success">Create </button>
                            </div>
                        </div>
                        
                      
                        
                    </div>
                </div>  
            </form>                              
        </div>            
    </div>    
</section>
<script>
    $("#myForm").submit(function() {
        $('input').attr('readonly', 'readonly');
        $('#submitBtn').attr('disabled', 'disabled');      
        $('#submitBtn').html('<i class="fi fi-br-refresh"></i>').append('   Please wait ...');  
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
</script>
@endsection