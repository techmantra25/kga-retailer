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
    <form action="" id="searchForm">
        <input type="hidden" name="status" value="{{$status}}">
        <input type="hidden" name="type" value="{{$type}}">
    <div class="search__filter">
        <div class="row align-items-end justify-content-between">
            <div class="col">
                <ul>
                    <li @if(!Request::get('status') || (Request::get('status') == 'all')) class="active" @endif><a href="{{route('category.list')}}">All <span class="count">({{$total}})</span></a></li>
                    <li @if(Request::get('status') == 'active' ) class="active" @endif><a href="{{route('category.list',['status'=>'active'])}}">Active <span class="count">({{$totalActive}})</span></a></li>
                    <li @if(Request::get('status') == 'inactive' ) class="active" @endif><a href="{{route('category.list',['status'=>'inactive'])}}">Inactive <span class="count">({{$totlInactive}})</span></a></li>
                </ul>
            </div>
            <div class="col-auto">
                <a href="{{route('category.add',['type'=>$type])}}" class="btn btn-outline-primary select-md">Add New</a>              
            </div>
            
        </div>
    </div>
    
    <div class="search__filter">
        <div class="row align-items-end justify-content-between">
            <div class="col">

            </div>
            <div class="col-auto">
                <select name="product_type" class="form-control select-md" id="product_type">
                    <option value="">All Product Type</option>
                    <option value="fg" @if($product_type == 'fg') selected @endif>Product Type - Finished Goods</option>
                    <option value="sp" @if($product_type == 'sp') selected @endif>Product Type - Spare Parts</option>
                </select>
            </div>
            <div class="col-4">
                <input type="search" name="search" value="{{$search}}" class="form-control select-md" placeholder="Search here..">
            </div>
        
        </div>
    </div>
    </form>
    <div class="filter">
        <div class="row align-items-center justify-content-between">
            <div class="col mb-2 mb-sm-0">
                @if (Session::has('message'))
                <div class="alert alert-success" role="alert">
                    {{ Session::get('message') }}
                    {{ Session::forget('message') }}
                </div>
                @endif
            </div>
            
            <div class="col-auto mb-2 mb-sm-0">
                <p>{{$totalResult}} Items</p>
            </div>
        </div>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th class="sr_no">#</th>
                <th class="sr_no text-center"><i class="fi fi-br-picture"></i></th>
                <th class="primary_column">Created At</th>
                @if ($type == 'child')
                <th>Category</th>
                @endif
                <th>Name</th>  
                <th>Product Type</th>              
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        @php
            if(empty(Request::get('page')) || Request::get('page') == 1){
                $i=1;
            } else {
                $i = (((Request::get('page')-1)*$paginate)+1);
            } 
        @endphp
        @forelse ($data as $item)
            <tr>
                <td class="sr_no">{{$i}}</td>
                <td class="sr_no text-center column-thumb">
                    @if (!empty($item->image))                        
                    <img src="{{ asset($item->image) }}">
                    @else                        
                    <img src="{{url('assets')}}/images/placeholder-image.jpg">
                    @endif
                </td>
                <td class="primary_column"> {{date('j M Y, l', strtotime($item->created_at))}} <button type="button" class="toggle_table"></button></td>
                @if ($type == 'child')
                <td data-colname="Category">
                    <span>{{ $item->child->name }}</span>         
                </td>
                @endif
                <td data-colname="Name">
                    <span>{{ $item->name }}</span>                    
                </td>
                <td>
                    @if ($item->product_type == 'fg')
                        <span class="badge bg-dark">Finished Goods</span>    
                    @else
                        <span class="badge bg-dark">Spare Parts</span>    
                    @endif
                </td>
                <td data-colname="Status">
                    @if(!empty($item->status))
                    <span class="badge bg-success">Active</span>
                    @else
                    <span class="badge bg-danger">Inactive</span>
                    @endif
                </td>
                <td data-colname="Action">
                    <a href="{{route('category.edit', [Crypt::encrypt($item->id),Request::getQueryString()])}}?type={{$type}}" class="btn btn-outline-primary select-md">Edit</a>
                    <a href="{{route('category.show', [Crypt::encrypt($item->id),Request::getQueryString()])}}?type={{$type}}" class="btn btn-outline-primary select-md">View</a>
                    @if(!empty($item->status))
                    <a href="{{route('category.toggle-status', [Crypt::encrypt($item->id),Request::getQueryString()])}}" class="btn btn-outline-danger select-md">Inactive</a>
                    @else
                    <a href="{{route('category.toggle-status', [Crypt::encrypt($item->id),Request::getQueryString()])}}" class="btn btn-outline-success select-md">Active</a>
                    @endif
                </td>
            </tr>
            @php
                $i++;
            @endphp
        @empty
        <tr>
            <td>
                No data found
            </td>
        </tr>
        @endforelse
            
        </tbody>
    </table>
    {{$data->links()}}
    
</section>
<script>
    $(document).ready(function(){
        $('div.alert').delay(3000).slideUp(300);
    })
    $('input[type=search]').on('search', function () {
        // search logic here
        // this function will be executed on click of X (clear button)
        $('#searchForm').submit();
    });
    $('#type').on('change', function(){
        $('#searchForm').submit();
    });
    $('#product_type').on('change', function(){
        $('#searchForm').submit();
    });
    $('.toggle_table').click(function(){
		$(this).parents('tr').toggleClass('is-expanded');
	});
</script>  
@endsection 