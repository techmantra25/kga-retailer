@extends('layouts.app')
@section('content')
@section('page', 'Dealer')
<section>
    <ul class="breadcrumb_menu">        
        <li><a href="{{ route('dealers.list') }}?{{$getQueryString}}">Dealer</a> </li>
        <li>Details</li>
    </ul> 
    <div class="row">
        <div class="col-sm-12">
            <div class="card shadow-sm">
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
                        <div class="form-group mb-3">
                            <p><span class="text-muted">PAN : </span>{{$data->pan_no}} </p>
                        </div> 
                        <div class="form-group mb-3">
                            <p><span class="text-muted">GST No : </span>{{$data->gst_no}} </p>
                        </div> 
                        <div class="form-group mb-3">
                            <p><span class="text-muted">License No : </span>{{$data->license_no}} </p>
                        </div> 
                        <div class="form-group mb-3">
                            <p><span class="text-muted">Address : </span>{{$data->address}} </p>
                        </div> 
                        <div class="form-group mb-3">
                            <p>
                                <span class="text-muted">Type : </span>
                                <span class="badge bg-success">{{ ucwords($data->dealer_type)}} </span>
                                
                            </p>
                        </div> 
                    </div>
                </div>   
                
            </div>                                      
        </div>            
    </div>    
</section>
<script>
    
</script>  
@endsection 