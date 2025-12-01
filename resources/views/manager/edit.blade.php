@extends('layouts.app')
@section('content')
@section('page', 'Manager')
<section>   
    <ul class="breadcrumb_menu">        
        <li><a href="{{ route('manager.list') }}?{{$getQueryString}}">Manager</a> </li>
        <li>Update</li>
    </ul> 
    <div class="row">
        <div class="col-sm-12">
            <form id="myForm" action="{{ route('manager.update', [$idStr,$getQueryString]) }}" method="POST">                
                @csrf
                <input type="hidden" name="browser_name" id="browser_name">
                <input type="hidden" name="navigator_useragent" id="navigator_useragent">
                <div class="card shadow-sm">
					<div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <input type="hidden" name="role_id" value="{{$data->role_id}}">
                                <label for="">Role <span class="text-danger">*</span> </label>
                                <select name="" class="form-control" id="" disabled>
                                    <option value="" hidden selected>Select One</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}" @if($data->role_id == $role->id) selected @endif>{{ $role->name }}</option>
                                    @endforeach
                                </select>
                                @error('role_id') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Name <span class="text-danger">*</span> </label>
                                <input type="text" autocomplete="off" name="name" placeholder="Name" class="form-control" maxlength="100" value="{{$data->name}}">
                                @error('name') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Email <span class="text-danger">*</span> </label>
                                <input type="email" name="email" placeholder="Email" class="form-control" maxlength="100" value="{{$data->email}}">
                                @error('email') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Phone  </label>
                                <input type="text" autocomplete="off" name="phone" placeholder="Phone" class="form-control" maxlength="10" value="{{$data->phone}}">
                                @error('phone') <p class="small text-danger">{{ $message }}</p> @enderror
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
                                <label for="">Password  </label>
                                <input type="password" name="password" placeholder="Password" class="form-control" maxlength="100" value="">
                                @error('password') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Confirm Password  </label>
                                <input type="password" name="password_confirmation" placeholder="Confirm Password" class="form-control" maxlength="10" value="">
                                @error('password_confirmation') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>  
					</div>
                </div> 
                <div class="card shadow-sm">
                    <div class="card-body text-end">
                        <a href="{{route('manager.list')}}?{{$getQueryString}}" class="btn btn-sm btn-danger">Back</a>
                        <button id="submitBtn" type="submit" class="btn btn-sm btn-success">Update </button>
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

    $("#myForm").submit(function() {
        $('input').attr('readonly', 'readonly');
        $('#submitBtn').attr('disabled', 'disabled');        
        return true;
    }); 
</script>
@endsection