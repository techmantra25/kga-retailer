@extends('layouts.app')
@section('content')
@section('page', 'Customer')
<section>   
    <ul class="breadcrumb_menu">        
        <li><a href="{{ route('customer.list') }}">Customer</a> </li>
        <li>Create</li>
    </ul> 
    <div class="row">
        <div class="col-sm-12">
            <form id="myForm" action="{{ route('customer.store') }}" method="POST">
                @csrf
                <div class="card shadow-sm">
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
                                <input type="email" name="email" placeholder="Email" class="form-control" maxlength="100" value="{{old('email')}}">
                                @error('email') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Phone No <span class="text-danger">*</span> </label>
                                <input type="text" autocomplete="off" name="phone" placeholder="Phone" class="form-control" maxlength="10" value="{{old('phone')}}">
                                @error('phone') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>  
                </div>                              
                <div class="card shadow-sm">
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
                <div class="card shadow-sm">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">Address <span class="text-danger">*</span> </label>
                                <input type="text" autocomplete="off" name="address" placeholder="Address" class="form-control" maxlength="200" value="{{old('address')}}">
                                @error('address') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>                        
                    </div>
                </div>
                <div class="card shadow-sm">
                    <div class="card-body text-end">
                        <a href="{{route('customer.list')}}" class="btn btn-sm btn-danger">Back</a>
                        <button id="submitBtn" type="submit" class="btn btn-sm btn-success">Create </button>
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
</script>
@endsection