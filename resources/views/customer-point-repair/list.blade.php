@extends('layouts.app')
@section('content')
@section('page', 'Service Partner')
<section>
    <ul class="breadcrumb_menu">        
        <li><a href="{{ route('customer-point-repair.list') }}">Customer Point Repair</a> </li>
        <li>Service Partner</li>
    </ul>
    <div class="search__filter">
        <div class="row align-items-center justify-content-between">
            <div class="col-12 col-sm mb-2 mb-sm-0">
                <ul>
                    <li @if(!Request::get('status') || (Request::get('status') == 'all')) class="active" @endif><a href="{{route('customer-point-repair.list')}}">All <span class="count">({{$total}})</span></a></li>
                    <li @if(Request::get('status') == 'active' ) class="active" @endif><a href="{{route('customer-point-repair.list',['status'=>'active'])}}">Active <span class="count">({{$totalActive}})</span></a></li>
                    <li @if(Request::get('status') == 'inactive' ) class="active" @endif><a href="{{route('customer-point-repair.list',['status'=>'inactive'])}}">Inactive <span class="count">({{$totlInactive}})</span></a></li>
                </ul>
            </div>
            <div class="col-auto mb-2 mb-sm-0">
                <form action="" id="searchForm">
                <input type="hidden" name="status" value="{{$status}}">
                
                <div class="row g-3 align-items-center">
                    <div class="col-auto">
                        <select name="type" class="form-control select-md" id="type">
                            <option value="">All Types</option>
                            <option value="1" @if($type == 1) selected @endif>24*7</option>
                            <option value="2" @if($type == 2) selected @endif>Inhouse Technician</option>
                            <option value="3" @if($type == 3) selected @endif>Local Vendors</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <input type="search" name="search" autocomplete="off" value="{{$search}}" class="form-control select-md" placeholder="Search here..">
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
				<th>Created At</th>
                <th>Contact Details</th>
                <th>Type</th>
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
                <td data-colname="" class="sr_no">
                    {{$i}}
                </td>
                <td data-colname="Name" class="primary_column">
                    <p class="small text-muted mb-1">
                        @if (!empty($item->company_name))
                        <span>Company Name: <strong>{{$item->company_name}}</strong></span> <br/>
                        @endif
                        @if (!empty($item->person_name))
                        <span>Person Name: <strong>{{$item->person_name}}</strong></span> <br/>
                        @endif                        
                    </p>
					<button type="button" class="toggle_table"></button>
                </td>
				<td data-colname="Created At">
                    {{date('j M Y, l', strtotime($item->created_at))}}                    
                </td>
                <td data-colname="Contact Details">
                    <p class="small text-muted mb-1">
                        @if (!empty($item->email))
                        <span>Email: <strong>{{$item->email}}</strong></span> <br/>
                        @endif
                        @if (!empty($item->phone))
                        <span>Phone: <strong>{{$item->phone}}</strong></span> <br/>
                        @endif                        
                    </p>                    
                </td> 
                <td data-colname="Type">
                    @if($item->type == 1)
                    <strong >24 * 7</strong>
                    @elseif ($item->type == 2)
                    <strong >Inhouse Technician</strong>
                    @elseif ($item->type == 3)
                    <strong >Local Vendors</strong>
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
                    <a href="{{route('customer-point-repair.show', [Crypt::encrypt($item->id),Request::getQueryString()])}}" class="btn btn-outline-primary select-md">View</a>
                    <a href="{{ route('customer-point-repair.upload-pincode-csv', Crypt::encrypt($item->id)) }}" class="btn btn-outline-primary select-md">Assign PIN Code</a>
                    <a href="{{ route('customer-point-repair.pincodelist', Crypt::encrypt($item->id)) }}" class="btn btn-outline-primary select-md">PIN Codes ({{count($item->customerpointpincodes)}}) </a>
                    <a href="{{ route('service-partner.call-logs', [Crypt::encrypt($item->id),'installation',Request::getQueryString()]) }}" class="btn btn-outline-primary select-md">Call Logs</a>
                    <a href="{{ route('service-partner.add-charges', [Crypt::encrypt($item->id),Request::getQueryString()] ) }}" class="btn btn-outline-primary select-md">Product Charges ({{count($item->products)}})</a>
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
    function getBrowserType() {
        const test = regexp => {
            return regexp.test(navigator.userAgent);
        };
        console.log(navigator.userAgent);
        var navigator_useragent = navigator.userAgent;
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
        $('div.alert').delay(3000).slideUp(300);
    })
    $('input[type=search]').on('search', function () {
        // search logic here
        // this function will be executed on click of X (clear button)
        $('#searchForm').submit();
    });
    $('#type').on('change', function(){
        $('#searchForm').submit();
    })
	$('.toggle_table').click(function(){
		$(this).parents('tr').toggleClass('is-expanded');
	});
</script>  
@endsection 