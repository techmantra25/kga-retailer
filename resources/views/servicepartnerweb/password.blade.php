@extends('servicepartnerweb.layouts.app')
@section('content')
@section('page', 'Change Password')
<section>   
    <ul class="breadcrumb_menu">        
        <li><a href="{{ route('servicepartnerweb.dashboard') }}">Dashboard</a> </li>
        <li>Change Password</li>
    </ul>
    <div class="row">
        <div class="col-sm-12">
            @if (Session::has('message'))
            <div class="alert alert-success" role="alert">
                {{ Session::get('message') }}
            </div>
            @endif
            <form id="myForm" action="{{ route('servicepartnerweb.savepassword') }}" method="POST">
                @csrf
                <div class="card shadow-sm">
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
                <div class="card shadow-sm">
                    <div class="card-body text-end">
                        <a href="{{route('servicepartnerweb.dashboard')}}" class="btn btn-sm btn-danger">Back To Dasboard</a>
                        <button id="submitBtn" type="submit" class="btn btn-sm btn-success">Save </button>
                    </div>
                </div>  
            </form>                              
        </div>            
    </div>    
</section>
<script>
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