@extends('layouts.app')
@section('content')
@section('page', 'Settings')
<section>   
    <ul class="breadcrumb_menu">        
        <li><a href="{{ route('home') }}">Dashboard</a> </li>
        <li>Settings</li>
    </ul>
    <div class="row">
        <div class="col-sm-12">
            @if (Session::has('message'))
            <div class="alert alert-success" role="alert">
                {{ Session::get('message') }}
            </div>
            @endif
            <form id="myForm" action="{{ route('savesettings') }}" method="POST">
                @csrf
                <div class="card shadow-sm">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Master Service Partner Email (for Installation)</label>
                                <input type="email" name="csv_to_email" placeholder="" class="form-control" maxlength="100" value="{{$settings->csv_to_email}}">
                                @error('csv_to_email') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        
                    </div>  
                </div>     
                <div class="card shadow-sm">
                    <div class="card-body text-end">
                        <a href="{{route('home')}}" class="btn btn-sm btn-danger">Back To Dasboard</a>
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
        $('#submitBtn').html('<i class="fi fi-br-refresh"></i>').append('   Please wait ...');    
        return true;
    });  
</script>
@endsection