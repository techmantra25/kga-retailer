@extends('layouts.app')
@section('content')
@section('page', 'Service Centre')
@section('small', '(For Inhouse DAP Servicing)')
<section>
    <div class="search__filter">
        <div class="row align-items-center justify-content-between">
            <div class="col">
                
            </div>
            <div class="col-auto">
                <a href="{{route('service-centre.add')}}" class="btn btn-outline-primary select-md">Add New</a>              
            </div>
            <div class="col-auto">
                <form action="" id="searchForm">
                {{-- <input type="hidden" name="status" value="{{$status}}"> --}}
                <div class="row g-3 align-items-center">
                    <div class="col-auto">
                        <input type="search" autocomplete="off" name="search" value="{{$search}}" class="form-control select-md" placeholder="Search here..">
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
    <div class="filter">
        <div class="row align-items-center justify-content-between">
            <div class="col">
                @if (Session::has('message'))
                <div class="alert alert-success" role="alert">
                    {{ Session::get('message') }}
                </div>
                @endif
            </div>
            
            <div class="col-auto">
                <p>{{$totalResult}} Items</p>
            </div>
        </div>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th class="sr_no">#</th>
                <th class="primary_column">Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Address</th>
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
                <td class="primary_column">{{$item->name}} <button type="button" class="toggle_table"></button></td>
                <td  data-colname="Email">{{$item->email}}</td>
                <td  data-colname="Phone">{{$item->phone}}</td>
                <td  data-colname="Address">{{$item->address}}</td>
                <td  data-colname="Status">
                    @if(!empty($item->status))
                    <span class="badge bg-success">Active</span>
                    @else
                    <span class="badge bg-danger">Inactive</span>
                    @endif
                </td>
                <td  data-colname="Action">
                    <a href="{{route('service-centre.edit', [Crypt::encrypt($item->id),Request::getQueryString()])}}" class="btn btn-outline-primary select-md">Edit</a>
                    <a href="{{route('service-centre.show', [Crypt::encrypt($item->id),Request::getQueryString()])}}" class="btn btn-outline-primary select-md">View</a>
                    <a href="{{ route('service-centre.change-password', [Crypt::encrypt($item->id),Request::getQueryString()]) }}" class="btn btn-outline-primary select-md">Change Password</a>
                    @if(!empty($item->status))
                    <a href="{{route('service-centre.toggle-status', [Crypt::encrypt($item->id),Request::getQueryString()])}}" class="btn btn-outline-danger select-md">Inactive</a>
                    @else
                    <a href="{{route('service-centre.toggle-status', [Crypt::encrypt($item->id),Request::getQueryString()])}}" class="btn btn-outline-success select-md">Active</a>
                    @endif
                </td>
            </tr>
            @php
                $i++;
            @endphp
        @empty
        <tr>
            <td colspan="11" style="text-align: center;">
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