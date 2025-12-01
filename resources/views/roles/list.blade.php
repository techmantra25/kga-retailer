@extends('layouts.app')
@section('content')
@section('page', 'Roles')
<section>
    @if (Session::has('message'))
    <div class="alert alert-success" role="alert">
        {{ Session::get('message') }}
        {{ Session::forget('message') }}
    </div>
    @endif
    <form action="" id="searchForm">
    <div class="search__filter">
        <div class="row align-items-center justify-content-between">
            <div class="col">
                
            </div>
            <div class="col-auto">
                {{-- <a href="" class="btn btn-outline-primary select-md">Add New</a>     --}}
                
            </div>
            
        </div>
    </div>
    <div class="search__filter">
        <div class="row align-items-center justify-content-between">
            <div class="col">
            </div>
            <div class="col-auto">
               
            </div>            
        </div>
    </div>
    <div class="filter">
        <div class="row align-items-center justify-content-between">
            <div class="col">
                <span class="small filter-waiting-text" id=""></span>                
            </div>
            
            <div class="col-auto">
                
            </div>
        </div>
    </div>
    </form>
    <table class="table">
        <thead>
            <tr>
                <th class="sr_no">#</th>
                <th>Name</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        @php                      
            $i=1;            
        @endphp
        @forelse ($data as $item)
            <tr>
                <td class="sr_no">{{$i}}</td>
                <td data-colname="Name">
                    {{$item->name}} &nbsp;
                    ({{count($item->users)}})
                </td>
                <td data-colname="Action">
                    <a href="{{ route('role-management.restricted-modules', Crypt::encrypt($item->id)) }}" class="btn btn-outline-primary select-md">Restricted Modules ({{ count($item->modules) }})</a>
                    
                </td>
            </tr>
            @php
                $i++;
            @endphp
        @empty
            <tr>
                <td colspan="12" style="text-align: center;">
                    No data found
                </td>
            </tr>
        @endforelse
            
        </tbody>
    </table>
    
</section>
<script>
    $(document).ready(function(){
        $('div.alert').delay(3000).slideUp(300);
    })
   
    $('.toggle_table').click(function(){
		$(this).parents('tr').toggleClass('is-expanded');
	});
</script>  
@endsection 