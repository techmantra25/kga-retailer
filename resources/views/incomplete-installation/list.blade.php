@extends('layouts.app')
@section('content')
@section('page', 'Incomplete Installation Log')
<section>
    <ul class="breadcrumb_menu">        
        {{-- <li><a href="{{ route('service-partner.list') }}">Service Partner</a> </li> --}}
        <li>Incomplete Installation</li>
        <li>Log</li>
    </ul>
    <div class="row">
        <div class="col mb-2 mb-sm-0">
            @if (Session::has('message'))
            <div class="alert alert-success" role="alert">
                {{ Session::get('message') }}
            </div>
            @endif
        </div>
              
    </div>  
    <div class="search__filter">
        <form action="" id="searchForm">
        <div class="row align-items-center justify-content-between">
            <div class="col mb-2 mb-sm-0">                
                                              
            </div>     
            <div class="col-auto mb-2 mb-sm-0">
                <select name="service_partner_id" class="form-control select-md" id="service_partner">
                    <option value="" selected hidden>Select Service Partner</option>
                    @foreach ($service_partner as $sp)
                        <option value="{{ $sp->id }}" @if($service_partner_id == $sp->id) selected @endif >{{ $sp->person_name }} - {{ $sp->company_name }}</option>
                    @endforeach
                </select>   
            </div>  
            <div class="col-auto mb-2 mb-sm-0">
                <a href="{{ route('incomplete-installation.list') }}?search={{$search}}" class="btn btn-outline-warning select-md">Reset Service Partner</a>   
            </div>        
            <div class="col-auto mb-2 mb-sm-0">
                <div class="row g-3 align-items-center">
                    <div class="col-auto">
                        <input type="search" name="search" value="{{$search}}" class="form-control select-md" placeholder="Search ..">
                    </div>
                </div>
            </div>            
        </div>
        </form>
    </div>
    <div class="search__filter">
        <form action="" id="searchForm">
        <div class="row align-items-center justify-content-between">
            <div class="col mb-2 mb-sm-0">                
                                           
            </div>            
            <div class="col-auto mb-2 mb-sm-0">
                {{-- <select name="" class="form-control select-md" id="">
                    <option value="">Select Service Partner</option>
                </select>    --}}
            </div> 
            <div class="col-auto mb-2 mb-sm-0">
                {{-- <a href="" class="btn btn-outline-success select-md">Set</a>    --}}
            </div>            
        </div>
        </form>
    </div>
    <div class="filter">
        <div class="row align-items-center justify-content-between">
            <div class="col">
                
            </div>            
            <div class="col-auto">
                <p>{{$totalData}} Items</p>
            </div>
        </div>
    </div>
    
        <table class="table">
            <thead>
                <tr>
                    <th class="sr_no">#</th>
					<th class="primary_column">Service Partner</th>   
                    <th>Date</th>
                    <th>Pincode</th>  
                    <th>Order Detail</th>
                    <th>Product Detail</th>    
                    <th>Customer Detail</th>
                    <th>Clearing Status</th>
                    {{-- <th>Action</th> --}}
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
                        
                        <p class="small text-muted mb-1">
                            @if (!empty($item->service_partner->company_name))
                            <span>Company Name: <strong>{{$item->service_partner->company_name}} </strong></span> <br/>
                            @endif
                            @if (!empty($item->service_partner->person_name))
                            <span>Person Name: <strong>{{$item->service_partner->person_name}} </strong></span> <br/>
                            @endif
                            @if (!empty($item->service_partner->email))
                            <span>Email: <strong>{{$item->service_partner_email}}</strong></span> <br/>
                            @endif
                            @if (!empty($item->service_partner->phone))
                            <span>Phone: <strong>{{$item->service_partner->phone}}</strong></span> <br/>
                            @endif
                        </p>   
						
						<button type="button" class="toggle_table"></button>
                       
                                             
                    </td>
					<td data-colname="Date">
                        {{ date('j M Y, l', strtotime($item->bill_date)) }}
                    </td>
                    <td data-colname="Pincode">
                        <strong>{{$item->pincode}}</strong>
                    </td>
                    <td data-colname="Order Detail">
                        <p class="small text-muted mb-1">
                            <span>Bill No: <strong>{{$item->bill_no}}</strong></span> <br/>
                            <span>Bill Date: <strong>{{ date('d/m/Y', strtotime($item->bill_date))}}</strong></span> <br/>
                        </p>
                    </td>
                    <td data-colname="Product Detail">
                        <p class="small text-muted mb-1">
                            <span>Product Sl No: <strong>{{$item->serial}}</strong></span> <br/>
                            <span>Product Name: <strong>{{$item->item}}</strong></span> <br/>
                            <span>Class: <strong>{{$item->class_name}}</strong></span> <br/>
                        </p>
                    </td>
                    <td data-colname="Customer Detail">
                        <p class="small text-muted mb-1">
                            <span>Customer Name: <strong>{{$item->customer_name}}</strong></span> <br/>
                            <span>Address: <strong>{{$item->address}}</strong></span> <br/>
                            <span>Mobile No: <strong>{{$item->mobile}}</strong></span> <br/>
                        </p>
                    </td>
                    <td data-colname="Clearing Status">
                        @if (!empty($item->installation_id))
                            <span class="badge bg-success">CLEARED</span>
                            <span class="badge bg-success">CHARGES ADDED</span>
                            <span class="badge bg-success">NOTIFICATION SENT</span>
                        @else
                            <span class="badge bg-danger">YET TO ADD CHARGE</span> 
                            <span class="badge bg-danger">YET TO CLEAR</span>
                        @endif
                    </td>
                    {{-- <td>
                        <a href="" class="btn btn-outline-success select-md">Set Charges</a>
                    </td> --}}
                </tr>
                @php
                    $i++;
                @endphp
                @empty
                <tr>
                    <td colspan="">No record found</td>
                </tr>  
                @endforelse
            </tbody>
        </table>
        {{$data->links()}}
    
</section>
<script>
    $(document).ready(function(){
        $('div.alert').delay(3000).slideUp(300);
    });
    $("#myForm").submit(function() {
        $('input').attr('readonly', 'readonly');
        $('#submitBtn').attr('disabled', 'disabled');    
        $('#submitBtn').html('<i class="fi fi-br-refresh"></i>').append('   Please wait ...');
        return true;
    });
    $('input[type=search]').on('search', function () {
        // search logic here
        // this function will be executed on click of X (clear button)
        $('#searchForm').submit();
    });
    $('#uploaded_at').on('change', function(){
        $('#uploaded_at_val').val(this.value);
        $('#searchForm').submit();
    })
    $('#service_partner').on('change', function(){
        $('#searchForm').submit();
    });
	$('.toggle_table').click(function(){
		$(this).parents('tr').toggleClass('is-expanded');
	});
</script>
@endsection