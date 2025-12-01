@extends('layouts.app')
@section('content')
@section('page', 'Change Password')
<section>   
    <ul class="breadcrumb_menu">        
        <li><a href="{{ route('service-partner.list') }}">Service Partner</a> </li>
        <li>{{ $service_partner->person_name }} - {{ $service_partner->company_name }}</li>
        <li>Change Password</li>
    </ul>
    <div class="row">
        <div class="col-sm-12">
            @if (Session::has('message'))
            <div class="alert alert-success" role="alert">
                {{ Session::get('message') }}
            </div>
            @endif
            <form id="myForm" action="{{ route('service-partner.save-password',[$idStr,$getQueryString]) }}" method="POST">
                @csrf
                <input type="hidden" name="browser_name" id="browser_name">
                <input type="hidden" name="navigator_useragent" id="navigator_useragent">
                <div class="card shadow-sm">
					<div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="input-group ">
                                    <input type="password" class="form-control" name="password" id="password" placeholder="New Password" value="" maxlength="20" >
                                    <div class="input-group-prepend">
                                        <div class="input-group-text" style="height: 35px;">
                                            <input type="checkbox" onclick="showPassword('password')" title="Show Password">
                                        </div>
                                    </div>
                                </div>
                                @error('password') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="input-group ">
                                    <input type="password" class="form-control" name="password_confirmation" placeholder="Confirm New Password" id="password_confirmation" value="" maxlength="20" >
                                    <div class="input-group-prepend">
                                        <div class="input-group-text" style="height: 35px;">
                                            <input type="checkbox" onclick="showPassword('password_confirmation')" title="Show Password">
                                        </div>
                                    </div>
                                </div>
                                @error('password_confirmation') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>  
					</div>
                </div>     
                <div class="card shadow-sm">
                    <div class="card-body text-end">
                        <a href="{{route('service-partner.list')}}" class="btn btn-sm btn-danger">Back</a>
                        <button id="submitBtn" type="submit" class="btn btn-sm btn-success">Save </button>
                    </div>
                </div>  
            </form>                              
        </div>            
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
        $('div.alert').delay(3000).slideUp(300);
    });
    $("#myForm").submit(function() {
        $('input').attr('readonly', 'readonly');
        $('#submitBtn').attr('disabled', 'disabled');        
        return true;
    });
    function showPassword(e) {
        var x = document.getElementById(e);
        if (x.type === "password") {
            x.type = "text";
        } else {
            x.type = "password";
        }
    }  
</script>
@endsection