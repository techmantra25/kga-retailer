@extends('layouts.app')
@section('content')
@section('page', 'Employee List -> '. $dealer->name)
<section>
    <div class="search__filter">
        <div class="row align-items-center justify-content-between">
            <div class="col-auto">
                <form action="" id="searchForm">
                <input type="hidden" name="dealer_id" value="{{$dealer->id}}">
                <div class="row g-3 align-items-center">
                    <div class="col-auto">
                        <input type="search" autocomplete="off" name="search" value="" class="form-control select-md" placeholder="Search here..">
                    </div>
                </div>
                </form>
            </div>
            <div class="col-auto">
                @if (Session::has('message'))
                <div class="alert alert-success" role="alert">
                    {{ Session::get('message') }}
                </div>
                @endif
            </div>
             <div class="col-auto">
                <p>{{$total}} Items</p>
            </div>
            <div class="col-auto">
                <a href="{{route('dealers.dealer-employee-add',[Crypt::encrypt($id),Request::getQueryString()])}}" class="btn btn-outline-primary select-md">Add Employee</a>              
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
                <th>Phone</th>
                <th>Working Branch</th>
                <th>Password</th>
                <th>Date</th>
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
                <td class="primary_column">{{$item->name}}</button></td>
                <td  data-colname="Phone">{{$item->phone}}</td>
                <td  data-colname="Phone">{{$item->branchData?$item->branchData->name:"Not-assigned"}}</td>
                <td data-colname="Password">
                <span class="password">{{ $item->password }}</span>
                <button class="btn btn-sm btn-outline-primary toggle-password">Show</button>
                </td>
                <td  data-colname="Created At">{{date('j M Y, l', strtotime($item->created_at))}}</td>
                   <td  data-colname="Status">
                    @if(!empty($item->status))
                    <span class="badge bg-success">Active</span>
                    @else
                    <span class="badge bg-danger">Inactive</span>
                    @endif
                </td>
                <td  data-colname="Action">
                    <a href="{{route('dealers.dealer-employee-edit', [Crypt::encrypt($item->id),Request::getQueryString()])}}" class="btn btn-outline-primary select-md">Edit</a>
                    @if($item->status == 1)
                    <a href="{{route('dealers.employee-toggle-status', [Crypt::encrypt($item->id),Request::getQueryString()])}}" class="btn btn-outline-danger select-md">Inactive</a>
                    @else
                    <a href="{{route('dealers.employee-toggle-status', [Crypt::encrypt($item->id),Request::getQueryString()])}}" class="btn btn-outline-success select-md">Active</a>
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

            {{$data->links()}}
        </tbody>
    </table>

    
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

     $('.toggle-password').on('click', function() {
            var $password = $(this).siblings('.password');
            if ($password.hasClass('visible')) {
                $password.removeClass('visible').text('{{ str_repeat('*', 8) }}'); // Hide password
                $(this).text('Show');
            } else {
                $password.addClass('visible').text($password.data('password')); // Show password
                $(this).text('Hide');
            }
        });

        // Initialize with passwords hidden
        $('.password').each(function() {
            var passwordText = $(this).text();
            $(this).data('password', passwordText).text('{{ str_repeat('*', 8) }}');
        });
</script>  
@endsection 