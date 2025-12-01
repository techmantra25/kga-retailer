@extends('layouts.app')
@section('content')
@section('page', 'Staff')
<section>
    <ul class="breadcrumb_menu">        
        <li><a href="{{ route('staffs.list') }}?{{$getQueryString}}">Staff</a> </li>
        <li>Details</li>
    </ul> 
    <div class="row">
        <div class="col-sm-12">
            <div class="card shadow-sm">
                <div class="card-body">
                        <div class="form-group mb-3">
                            <p><span class="text-muted">Name : </span>{{$data->name}} </p>
                        </div> 
                        <div class="form-group mb-3">
                            <p><span class="text-muted">Email : </span>{{$data->email}} </p>
                        </div> 
                        <div class="form-group mb-3">
                            <p><span class="text-muted">Phone : </span>{{$data->phone}} </p>
                        </div> 
                    </div> 
                
            </div>                                      
        </div>            
    </div>    
</section>
<script>
    
</script>  
@endsection 