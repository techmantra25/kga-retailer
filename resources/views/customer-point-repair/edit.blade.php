@extends('layouts.app')
@section('content')
@section('page', 'Service Partner')
<section>   
    <ul class="breadcrumb_menu">        
        <li><a href="{{ route('service-partner.list') }}?{{$getQueryString}}">Service Partner</a> </li>
        <li>Update</li>
    </ul>
    <div class="row">
        <form id="myForm" action="{{ route('service-partner.update',[$idStr,$getQueryString]) }}" enctype="multipart/form-data" method="POST">
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
                                <select name="type" class="form-control" id="" disabled>
                                    <option value="" hidden selected>Select an option</option>
                                    <option value="1" @if($data->type == 1) selected @endif>24 * 7</option>
                                    <option value="2" @if($data->type == 2) selected @endif>Inhouse Technician</option>
                                    <option value="3" @if($data->type == 3) selected @endif>Local Vendors</option>
                                </select>
                                @error('type') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div> 
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Company Name <span class="text-danger">*</span></label>
                                <input type="text" autocomplete="off" name="company_name" placeholder="Please Enter Company Name" class="form-control" maxlength="100" value="{{$data->company_name}}">
                                @error('company_name') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Person Name <span class="text-danger">*</span></label>
                                <input type="text" autocomplete="off" name="person_name" placeholder="Please Enter Person Name" class="form-control" maxlength="100" value="{{$data->person_name}}">
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
                                <input type="text" autocomplete="off" name="email" placeholder="Please Enter Email Address" class="form-control" maxlength="100" value="{{$data->email}}">
                                @error('email') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Phone <span class="text-danger">*</span> </label>
                                <input type="text" autocomplete="off" name="phone" placeholder="Please Enter Phone Number" class="form-control" maxlength="10" value="{{$data->phone}}">
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
                                <input type="text" autocomplete="off" name="pan_no" maxlength="11" placeholder="Please Enter PAN Number" class="form-control" maxlength="100" value="{{$data->pan_no}}">
                                @error('pan_no') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Aadhaar No</label>
                                <input type="text" autocomplete="off" name="aadhaar_no" maxlength="20" placeholder="Please Enter Aadhaar Number" class="form-control" maxlength="10" value="{{$data->aadhaar_no}}">
                                @error('aadhaar_no') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">GST No </label>
                                <input type="text" autocomplete="off" name="gst_no" maxlength="20" placeholder="Please Enter GST Number" class="form-control" maxlength="10" value="{{$data->gst_no}}">
                                @error('gst_no') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">License No </label>
                                <input type="text" autocomplete="off" name="license_no" placeholder="Please Enter License Number" class="form-control" maxlength="20" value="{{$data->license_no}}">
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
                                <textarea name="address" class="form-control" id="" placeholder="Please Enter Address" cols="1" rows="1">{{$data->address}}</textarea>
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
                                <input type="text" autocomplete="off" name="state" placeholder="Please Enter State" class="form-control" maxlength="100" value="{{$data->state}}">
                                @error('state') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div> 
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">City <span class="text-danger">*</span></label>
                                <input type="text" autocomplete="off" name="city" placeholder="Please Enter City" class="form-control" maxlength="100" value="{{$data->city}}">
                                @error('city') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div> 
						</div>
                    </div>
                </div>
                <div class="card shadow-sm" id="remuneration_div">
					<div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Salary </label>
                                <input type="text" autocomplete="off" name="salary" placeholder="Please Enter Salary" class="form-control" maxlength="100" value="{{$data->salary}}">
                                @error('salary') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div> 
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Repair Charge </label>
                                <input type="text" autocomplete="off" name="repair_charge" placeholder="Please Enter Repair Charge" class="form-control" maxlength="100" value="{{$data->repair_charge}}">
                                @error('repair_charge') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div> 
                        <div class="col-md-4">  
                            <div class="form-group">
                                <label for="">Travelling Allowance </label>
                                <input type="text" autocomplete="off" name="travelling_allowance" placeholder="Please Enter Travelling Allowance" class="form-control" maxlength="10" value="{{$data->travelling_allowance}}">
                                @error('travelling_allowance') <p class="small text-danger">{{ $message }}</p> @enderror
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
                                <label for="">About </label>
                                <textarea name="about" class="form-control" id="" placeholder="Please Enter About Service Details" cols="1" rows="2">{{$data->about}}</textarea>
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
                            <label for="thumbnail">
                                @if (!empty($data->photo))
                                <img id="output" src="{{ asset($data->photo) }}">
                                @else
                                <img id="output" src="{{url('assets')}}/images/placeholder-image.jpg">  
                                @endif                                
                            </label>
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
                        <a href="{{route('service-partner.list')}}?{{$getQueryString}}" class="btn btn-sm btn-danger">Back</a>
                        <button id="submitBtn" type="submit" class="btn btn-sm btn-success">Update </button>
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
        var type_old = "{{$data->type}}";
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
    });
    $("#myForm").submit(function() {
        $('input').attr('readonly', 'readonly');
        $('#submitBtn').attr('disabled', 'disabled');        
        return true;
    });
</script>
@endsection