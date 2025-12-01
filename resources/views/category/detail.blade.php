@extends('layouts.app')
@section('content')
@if ($type == 'parent')
    @section('page', 'Category')
    @section('small', '(Class)')
@else
    @section('page', 'Sub Category')
    @section('small', '(Group)')
@endif
<section>
    <ul class="breadcrumb_menu">     
        <li>Product Management</li>     
        @if ($type == 'parent')
        <li><a href="{{ route('category.list') }}?{{$getQueryString}}">Category</a> </li>
        @else
        <li><a href="{{ route('category.list') }}?{{$getQueryString}}">Sub Category</a> </li>
        @endif  
        <li>Details</li>
    </ul> 
    <div class="row">
        <div class="col-sm-12">
            <div class="card shadow-sm">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <p>
                                <span class="text-muted">Name : </span>
                                @if ($data->product_type == 'fg')
                                    Finished Goods
                                @else
                                    Spare Parts
                                @endif
                            </p>
                        </div> 
                        <div class="form-group mb-3">
                            <p><span class="text-muted">Name : </span>{{$data->name}} </p>
                        </div> 
                        @if (!empty($data->parent_id))
                        <div class="form-group mb-3">
                            <p><span class="text-muted">Parent : </span>{{$data->child->name}} </p>
                        </div> 
                        @endif                        
                        <div class="form-group mb-3">
                            <p><span class="text-muted">Description : </span>{{$data->description}} </p>
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