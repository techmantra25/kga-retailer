@extends('layouts.app')
@section('content')
@section('page', 'Supplier')
<section>
    <div class="search__filter">
        <div class="row align-items-center justify-content-between">
            <div class="col mb-2 mb-sm-0">
                <ul>
                    <li @if(!Request::get('status') || (Request::get('status') == 'all')) class="active" @endif><a href="{{route('supplier.list')}}">All <span class="count">({{$total}})</span></a></li>
                    <li @if(Request::get('status') == 'active' ) class="active" @endif><a href="{{route('supplier.list',['status'=>'active'])}}">Active <span class="count">({{$totalActive}})</span></a></li>
                    <li @if(Request::get('status') == 'inactive' ) class="active" @endif><a href="{{route('supplier.list',['status'=>'inactive'])}}">Inactive <span class="count">({{$totlInactive}})</span></a></li>
                </ul>
            </div>
            <div class="col-auto mb-2 mb-sm-0">
                <a href="{{route('supplier.add')}}" class="btn btn-outline-primary select-md">Add New</a>              
            </div>
            <div class="col-auto mb-2 mb-sm-0">
                <form action="" id="searchForm">
                <input type="hidden" name="status" value="{{$status}}">
                <div class="row g-3 align-items-center">
                    <div class="col-auto">
                        <input type="search" autocomplete="off" name="search" value="{{$search}}" class="form-control select-md" placeholder="Search here..">
                    </div>
                    <div class="col-auto">
                        {{-- <button type="submit" class="btn btn-outline-primary btn-sm">Search </button> --}}
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
    <div class="filter">
        <div class="row align-items-center justify-content-between">
            <div class="col mb-2 mb-sm-0">
                @if (Session::has('message'))
                <div class="alert alert-success" role="alert">
                    {{ Session::get('message') }}
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
                <th class="primary_column">Created At</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>    
                <th>Inside / Outside</th>            
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
                <td class="primary_column">
                {{date('j M Y, l', strtotime($item->created_at))}}
                <div class="row__action">
                    
                </div>
                <button type="button" class="toggle_table"></button> 
                </td>
                <td data-colname="Name">{{$item->public_name}}</td>
                <td data-colname="Email">{{$item->email}}</td>
                <td data-colname="Phone">{{$item->phone}}</td>
                <td data-colname="Inside / Outside">
                    @if (!empty($item->is_inside))
                    <span class="badge bg-dark">Inside</span>
                    @else
                    <span class="badge bg-dark">Outside</span>
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
                    <a href="{{route('supplier.edit', [Crypt::encrypt($item->id),Request::getQueryString()])}}" class="btn btn-outline-primary select-md">Edit</a>
                    <a href="{{route('supplier.show', [Crypt::encrypt($item->id),Request::getQueryString()])}}" class="btn btn-outline-primary select-md">View</a>
                    @if(!empty($item->status))
                    <a href="{{route('supplier.toggle-status', [Crypt::encrypt($item->id),Request::getQueryString()])}}" class="btn btn-outline-danger select-md">Inactive</a>
                    @else
                    <a href="{{route('supplier.toggle-status', [Crypt::encrypt($item->id),Request::getQueryString()])}}" class="btn btn-outline-success select-md">Active</a>
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
    $('.toggle_table').click(function(){
		$(this).parents('tr').toggleClass('is-expanded');
	});
</script>  
@endsection 