@extends('layouts.app')
@section('content')
@section('page', 'Staff')
<section>   
    <ul class="breadcrumb_menu">        
        <li><a href="{{ route('staffs.list') }}?{{$getQueryString}}">Staff</a> </li>
        <li>Update</li>
    </ul> 
    <div class="row">
        <div class="col-sm-12">
            <form id="myForm" action="{{ route('staffs.update', [$idStr,$getQueryString]) }}" method="POST">                
                @csrf
                <div class="card shadow-sm">
					<div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <input type="text" name="name" placeholder="Name" class="form-control" maxlength="100" value="{{$data->name}}">
                                @error('name') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <input type="email" name="email" placeholder="Email" class="form-control" maxlength="100" value="{{$data->email}}">
                                @error('email') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <input type="text" name="phone" placeholder="Phone" class="form-control" maxlength="10" value="{{$data->phone}}">
                                @error('phone') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>  
					</div>
                </div>
                <div class="card shadow-sm">
                    <div class="card-body text-end">
                        <a href="{{route('staffs.list')}}?{{$getQueryString}}" class="btn btn-sm btn-danger">Back</a>
                        <button id="submitBtn" type="submit" class="btn btn-sm btn-success">Update </button>
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
        return true;
    });
</script>
@endsection