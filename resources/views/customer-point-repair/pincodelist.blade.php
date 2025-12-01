@extends('layouts.app')
@section('content')
@section('page', 'PIN Code List(Customer point repair)')
<script>
    var navigator_useragent = '';
    function getBrowserType() {
        const test = regexp => {
            return regexp.test(navigator.userAgent);
        };
        console.log(navigator.userAgent);
        navigator_useragent = navigator.userAgent;
                
        $('#navigator_useragent').val(navigator_useragent);
        if (test(/opr\//i) || !!window.opr) {
            return 'Opera';
        } else if (test(/edg/i)) {
            return 'Microsoft Edge';
        } else if (test(/chrome|chromium|crios/i)) {
            return 'Google Chrome';
        } else if (test(/firefox|fxios/i)) {
            return 'Mozilla Firefox';
        } else if (test(/safari/i)) {
            return 'Apple Safari';
        } else if (test(/trident/i)) {
            return 'Microsoft Internet Explorer';
        } else if (test(/ucbrowser/i)) {
            return 'UC Browser';
        } else if (test(/samsungbrowser/i)) {
            return 'Samsung Browser';
        } else {
            return 'Unknown browser';
        }
    }
    const browserType = getBrowserType();
    console.log(browserType);
    $('#browser_name').val(browserType);
    
    $(document).ready(function(){
        $('#browser_name').val(browserType);
        $('#navigator_useragent').val(navigator_useragent);
        

    })
</script>

@php
    $browser_name = "<script>
        var A = 'Hi there';
    </script>";

    echo $browser_name;
@endphp

<section>
    <ul class="breadcrumb_menu">
        <li><a href="{{ route('customer-point-repair.list') }}">Service Partner</a></li>
        <li>
            <a href="#">
                {{$service_partner->person_name}} - {{$service_partner->company_name}}
            </a>
        </li>
        
    </ul>
    <div class="row">
        <div class="col-lg-6">
            <div class="search__filter">
                <div class="row align-items-center justify-content-between">
                    <div class="col">
                        
                    </div>
                    <div class="col-auto">
                        <form action="" id="searchForm">
                        <div class="row g-3 align-items-center">
                            <div class="col-auto">
                                
                            </div>
                            <div class="col-auto">
                                <select name="product_type" class="form-control" id="product_type">
                                    <option value="">Product Type - All</option>
                                    <option value="general" @if($product_type == 'general') selected @endif>Product Type - General</option>
                                    <option value="chimney" @if($product_type == 'chimney') selected @endif>Product Type - Chimney</option>
                                </select>
                                
                            </div>
                            <div class="col-auto">
                                <input type="search" name="search" value="{{$search}}" class="form-control" placeholder="Search PIN Code ">
                            </div>
                        </div>
                        </form>
                    </div>
                </div>
            </div>        
            <form action="{{ route('customer-point-repair.removepincdoebulk',$service_partner_id) }}" method="POST">
                @csrf
                <input type="hidden" name="browser_name" id="browser_name">
                <input type="hidden" name="navigator_useragent" id="navigator_useragent">
                <div class="filter">
                    <div class="row align-items-center justify-content-between">
                        <div class="col">
                            @if (Session::has('message'))
                            <div class="alert alert-success" role="alert">
                                {{ Session::get('message') }}
                            </div>
                            @endif
                            {{-- <input type="hidden" name="remove_type" value="bulk"> --}}
                            <input type="submit" value="Remove" class="btn btn-outline-danger select-md" id="btnSuspend" onclick="return confirm('Are you sure?');">
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
                            <th class="check-column primary_column" width="10%">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="checkAll">
                                    <label class="form-check-label" for=""></label>
                                </div>
                            </th>
                            <th class="primary_column" width="40%">PIN Code</th>   
                            <th width="40%">Product Type</th> 
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
                            <td data-colname="" class="sr_no">{{$i}}</td>
                            <td data-colname="" class="check-column primary_column">
                                <div class="form-check">
                                    <input name="ids[]" class="data-check" type="checkbox" value="{{$item->id}}">
                                    <label class="form-check-label" for=""></label>
                                </div>
                            </td>
                            <td data-colname="" class="primary_column">
                                {{$item->number}}
                            </td>
                            <td>
                                {{ ucwords($item->product_type) }}
                            </td>
                            
                        </tr>
                        @php
                            $i++;
                        @endphp
                    @empty
                    <tr>
                        <td colspan="3" class="text-center">
                            No data found
                        </td>
                    </tr>
                    @endforelse
                        
                    </tbody>
                </table> 
            </form>
               
            {{$data->links()}}
        </div>
    </div>
    
</section>
<script>
    
    
    
    $(document).ready(function(){
        $('div.alert').delay(3000).slideUp(300);
        $('#btnSuspend').prop('disabled', true);        
        $("#checkAll").change(function () {
            $("input:checkbox").prop('checked', $(this).prop("checked"));
            var checkAllStatus = $("#checkAll:checked").length;
            var total_data_length = "{{ count($data) }}";

            // alert('total_data_length:- '+total_data_length)
            
            // console.log(checkAllStatus)
            if(checkAllStatus == 1 && total_data_length > 0){
                $('#btnSuspend').prop('disabled', false);
            }else{
                $('#btnSuspend').prop('disabled', true);
            }
        });
        
        $('.data-check').change(function () {
            $('#btnSuspend').prop('disabled', false);
            var total_checkbox = $('input:checkbox.data-check').length;
            var total_checked = $('input:checkbox.data-check:checked').length;

            
            if(total_checked == 0 ){
                $('#btnSuspend').prop('disabled', true);
            }
          
            if(total_checkbox == total_checked){
                console.log('All checked')
                $('#checkAll').prop('checked', true);
            }else{
                console.log('Not All checked')
                $('#checkAll').prop('checked', false);
            }
        })
    })

    
    $('input[type=search]').on('search', function () {
        // search logic here
        // this function will be executed on click of X (clear button)
        $('#searchForm').submit();
    });
    $('.toggle_table').click(function(){
		$(this).parents('tr').toggleClass('is-expanded');
	});
    $('#product_type').on('change', function(){
        $('#searchForm').submit();
    })
</script>  
@endsection 