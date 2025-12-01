@extends('layouts.app')
@section('content')
@section('page', 'Service Partner')
<section>   
    <ul class="breadcrumb_menu">        
        <li><a href="{{ route('service-partner.list') }}">Service Partner</a> </li>
        <li>Create</li>
    </ul>
    <div class="row">
        <form id="myForm" action="{{ route('service-partner.store') }}" enctype="multipart/form-data" method="POST">
            @csrf
            <input type="hidden" name="browser_name" id="browser_name">
            <input type="hidden" name="navigator_useragent" id="navigator_useragent">
        <div class="row">
            <div class="col-sm-9">            
                <div class="card shadow-sm">
					<div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Type <span class="text-danger">*</span></label>
                                <select name="type" class="form-control" id="type">
                                    <option value="" hidden selected>Select an option</option>
                                    <option value="1" @if(old('type') == 1) selected @endif>24 * 7</option>
                                    <option value="2" @if(old('type') == 2) selected @endif>Inhouse Technician</option>
                                    <option value="3" @if(old('type') == 3) selected @endif>Local Vendors</option>
                                </select>
                                @error('type') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div> 
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Company Name <span class="text-danger">*</span></label>
                                <input type="text" autocomplete="off" name="company_name" placeholder="Please Enter Company Name" class="form-control" maxlength="100" value="{{old('company_name')}}">
                                @error('company_name') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Person Name <span class="text-danger">*</span></label>
                                <input type="text" autocomplete="off" name="person_name" placeholder="Please Enter Person Name" class="form-control" maxlength="100" value="{{old('person_name')}}">
                                @error('person_name') <p class="small text-danger">{{ $message }}</p> @enderror
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
                                <label for="">Email <span class="text-danger">*</span></label>
                                <input type="text" autocomplete="off" name="email" placeholder="Please Enter Email Address" class="form-control" maxlength="100" value="{{old('email')}}">
                                @error('email') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Phone <span class="text-danger">*</span></label>
                                <input type="text" autocomplete="off" name="phone" placeholder="Please Enter Phone Number" class="form-control" maxlength="10" value="{{old('phone')}}">
                                @error('phone') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
					</div>
                </div>
                <div class="card shadow-sm">
					<div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">PAN No</label>
                                <input type="text" autocomplete="off" name="pan_no" maxlength="11" placeholder="Please Enter PAN Number" class="form-control" maxlength="100" value="{{old('pan_no')}}">
                                @error('pan_no') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Aadhaar No</label>
                                <input type="text" autocomplete="off" name="aadhaar_no" maxlength="20" placeholder="Please Enter Aadhaar Number" class="form-control" maxlength="10" value="{{old('aadhaar_no')}}">
                                @error('aadhaar_no') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">GST No </label>
                                <input type="text" autocomplete="off" name="gst_no" maxlength="20" placeholder="Please Enter GST Number" class="form-control" maxlength="10" value="{{old('gst_no')}}">
                                @error('gst_no') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">License No </label>
                                <input type="text" autocomplete="off" name="license_no" placeholder="Please Enter License Number" class="form-control" maxlength="20" value="{{old('license_no')}}">
                                @error('license_no') <p class="small text-danger">{{ $message }}</p> @enderror
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
                                <label for="">Address <span class="text-danger">*</span></label>
                                <textarea name="address" class="form-control" id="" placeholder="Please Enter Address" cols="1" rows="1">{{old('address')}}</textarea>
                                @error('address') <p class="small text-danger">{{ $message }}</p> @enderror
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
                                <label for="">State <span class="text-danger">*</span></label>
                                <input type="text" autocomplete="off" name="state" placeholder="Please Enter State" class="form-control" maxlength="100" value="{{old('state')}}">
                                @error('state') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div> 
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">City <span class="text-danger">*</span></label>
                                <input type="text" autocomplete="off" name="city" placeholder="Please Enter City" class="form-control" maxlength="100" value="{{old('city')}}">
                                @error('city') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div> 
                        {{-- <div class="col-md-4">  
                            <div class="form-group">
                                <label for="">PIN <span class="text-danger">*</span></label>
                                <input type="text" name="pin" placeholder="Please Enter PIN Code" class="form-control" maxlength="10" value="{{old('pin')}}">
                                @error('pin') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div> --}}
                    </div>
					</div>
                </div>
                <div class="card shadow-sm" id="remuneration_div">
					<div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Salary </label>
                                <input type="text" autocomplete="off" name="salary" placeholder="Please Enter Salary" class="form-control" maxlength="100" value="{{old('salary')}}">
                                @error('salary') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div> 
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Repair Charge </label>
                                <input type="text" autocomplete="off" name="repair_charge" placeholder="Please Enter Repair Charge" class="form-control" maxlength="100" value="{{old('repair_charge')}}">
                                @error('repair_charge') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div> 
                        <div class="col-md-4">  
                            <div class="form-group">
                                <label for="">Travelling Allowance </label>
                                <input type="text" autocomplete="off" name="travelling_allowance" placeholder="Please Enter Travelling Allowance" class="form-control" maxlength="10" value="{{old('travelling_allowance')}}">
                                @error('travelling_allowance') <p class="small text-danger">{{ $message }}</p> @enderror
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
                                <label for="">About </label>
                                <textarea name="about" class="form-control" id="" placeholder="Please Enter About Service Details" cols="1" rows="3">{{old('about')}}</textarea>
                                {{-- @error('about') <p class="small text-danger">{{ $message }}</p> @enderror --}}
                            </div>
                        </div> 
                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="form-check">
                                    <input class="form-check-input" name="is_from_csv" type="checkbox" value="1" id="is_from_csv">
                                    <label class="form-check-label" for="is_from_csv">Mark As FROM CSV</label>
                                </div>
                            </div>
                        </div>                                                
                    </div>
					</div>
                </div>                                          
            </div> 
            <div class="col-sm-3">
                <div class="card shadow-sm">
                    <div class="card-header">
                        Profile Picture
                    </div>
                    <div class="card-body">
                        <div class="w-100 product__thumb">
                            <label for="thumbnail"><img id="output" src="{{url('assets')}}/images/placeholder-image.jpg"></label>
                        </div>
                        <input type="file" name="image" id="thumbnail" accept="image/*" onchange="loadFile(event)">
                        <script>
                            var loadFile = function(event) {
                            var output = document.getElementById('output');
                            output.src = URL.createObjectURL(event.target.files[0]);
                            output.onload = function() {
                                URL.revokeObjectURL(output.src) // free memory
                            }
                            };
                        </script>
                    </div>
                </div>
                <div class="card shadow-sm">
                    <div class="card-body text-end">
                        <span>** <strong>secret</strong> is default password for accessing mobile app </span>
                    </div>
                </div> 
                <div class="card shadow-sm">
                    <div class="card-body text-end">
                        <a href="{{route('service-partner.list')}}" class="btn btn-sm btn-danger">Back</a>
                        <button id="submitBtn" type="submit" class="btn btn-sm btn-success">Create </button>
                    </div>
                </div> 
            </div>   
        </div>                 
        </form>             
    </div>    
</section>
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
        var type_old = "{{old('type')}}";
        console.log(type_old);
        if(type_old == ''){
            $('#remuneration_div').hide();
        }else{
            if(type_old == 1){
                $('#remuneration_div').hide();
                
            }else {
                $('#remuneration_div').show();
            }
        }
        // $('#remuneration_div').hide();
    });
    $("#myForm").submit(function() {
        $('input').attr('readonly', 'readonly');
        $('#submitBtn').attr('disabled', 'disabled');     
        $('#submitBtn').html('<i class="fi fi-br-refresh"></i>').append('   Please wait ...'); 
        return true;
    });
    $('#type').on('change', function(){
        var type = this.value;
        // alert(type);
        if(type == 1){
            $('#remuneration_div').hide();
            
        }else {
            $('#remuneration_div').show();
        }
    });
</script>
@endsection