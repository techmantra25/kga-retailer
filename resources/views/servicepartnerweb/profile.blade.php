@extends('servicepartnerweb.layouts.app')
@section('content')
@section('page', 'My Profile')
<section>   
    <ul class="breadcrumb_menu">        
        <li><a href="{{ route('servicepartnerweb.dashboard') }}">Dashboard</a> </li>
        <li>My Profile</li>
    </ul>
    <div class="row">
        <div class="col-sm-12">
            @if (Session::has('message'))
            <div class="alert alert-success" role="alert">
                {{ Session::get('message') }}
            </div>
            @endif
            <form id="myForm" action="{{ route('servicepartnerweb.saveprofile') }}" method="POST">
                @csrf
                <div class="card shadow-sm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="text" name="company_name" placeholder="Company Name" class="form-control" maxlength="100" value="@if (!empty(old('company_name'))){{old('company_name')}}@else{{Auth::user()->company_name}}@endif">
                                @error('company_name') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="text" name="person_name" placeholder="Person Name" class="form-control" maxlength="100" value="@if (!empty(old('person_name'))){{old('person_name')}}@else{{Auth::user()->person_name}}@endif">
                                @error('person_name') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card shadow-sm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="text" name="email" placeholder="Email" class="form-control" maxlength="100" disabled value="{{Auth::user()->email}}">
                                @error('email') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="text" name="phone" placeholder="Phone" class="form-control" maxlength="10" disabled value="{{Auth::user()->phone}} ">
                                @error('phone') <p class="small text-danger">{{ $message }}</p> @enderror
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
</script>
@endsection