@extends('layouts.app')
@section('content')
@section('page', 'Branch List -> ' . $dealer->name)
<section>
    <div class="filter">
        <div class="row align-items-center justify-content-between">
            <div class="col-auto">
                <form action="" id="searchForm">
                <div class="row g-3 align-items-center">
                    <div class="col-auto">
                        <input type="search" autocomplete="off" name="search" value="{{$search}}" class="form-control select-md" placeholder="Search here..">
                    </div>
                </div>
                </form>
            </div>
            <div class="col-auto">
                <p>{{$total}} Items</p>
            </div>
            <div class="col-auto">
                <a href="{{route('dealers.list')}}" class="btn btn-outline-primary select-md">Back</a>              
            </div>
            
        </div>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th class="sr_no">#</th>
                <th class="primary_column">Name</th>
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
                <td class="primary_column">{{$item->name}}</td>
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