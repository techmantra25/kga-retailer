@extends('layouts.app')
@section('content')
@section('page', 'Supplier')
<section>
    <ul class="breadcrumb_menu">        
        <li><a href="{{ route('supplier.list') }}?{{$getQueryString}}">Supplier</a> </li>
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
                            <p><span class="text-muted">Public Name : </span>{{$data->public_name}} </p>
                        </div> 
                        <div class="form-group mb-3">
                            <p><span class="text-muted">Email : </span>{{$data->email}} </p>
                        </div> 
                        <div class="form-group mb-3">
                            <p><span class="text-muted">Phone : </span>{{$data->phone}} </p>
                        </div>
                        <div class="form-group mb-3">
                            <p>
                                <span class="text-muted">Address : </span>{{$data->address}} 
                                @if (!empty($data->is_inside))
                                <span class="text-muted">(India)</span>   
                                @else
                                <span class="text-muted">(Abroad)</span>   
                                @endif
                            </p>
                        </div>
                        <div class="form-group mb-3">
                            <span class="text-dark">{{$data->state}} | {{$data->city}} | {{$data->pin}} </span>                             
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