@extends('layouts.app')
@section('content')
@section('page', 'Service Centre Returned Items')
<section>
    <ul class="breadcrumb_menu">        
        <li>Inhouse DAP Servicing</li>
        <li>Service Centre Returned Items</li>
    </ul>    
    <form action="" id="searchForm">
    <div class="search__filter">
        @if (Session::has('message'))
        <div class="alert alert-success" role="alert">
            {{ Session::get('message') }}
        </div>
        @endif
        <div class="row  justify-content-end">
            <div class="col-md-7">
                <div class="input-group">
                    <input type="search" name="search" value="{{$search}}" class="form-control " placeholder="Search Item ...">     
                    <div class="input-group-append">
                        <a href="{{ route('dap-services.centre-returned-items') }}?branch_id={{$branch_id}}&branch_name={{$branch_name}}" class="btn btn-outline-secondary" id="">Reset</a>
                    </div>  
                            
                </div>
            </div>            
        </div>
        
        
    </div>
    <div class="search__filter">
        <div class="row  justify-content-end">
            <div class="col">

            </div>
            <div class="col-md-4">
                <div class="input-group">
                    <input type="text" name="branch_name" class="form-control" placeholder="Search branch where item dropped at..." onkeyup="searchBranch(this.value);" id="branch_name" value="{{ $branch_name }}" autocomplete="off">
                    <input type="hidden" name="branch_id" id="branch_id" value="{{ $branch_id }}">
                
                    
                    <div class="input-group-append">
                        <a href="{{ route('dap-services.centre-returned-items') }}?search={{$search}}" class="btn btn-outline-secondary" id="">Reset</a>
                    </div>                    
                </div>
                <div class="respBranch" id="respBranch" style="position: relative;"></div>

            </div>
            <div class="col-md-3">
                <select name="service_centre_id" class="form-control" id="service_centre_id">
                    <option value="" hidden selected>Select Service Centre</option>
                    @forelse ($sc as $c)
                    <option value="{{$c['id']}}" @if($service_centre_id == $c['id']) selected @endif>{{$c['name']}}</option>
                    @empty
                        
                    @endforelse
                    
                </select>
            </div>            
            
            {{-- <div class="col-auto">
                <a href="{{ route('dap-services.centre-reached-items') }}?search={{$search}}" class="btn btn-warning ">Reset Branch & Centre</a>
            </div> --}}
        </div>
    </div>
    <div class="search__filter">        
        <input type="hidden" name="closing_type" value="{{$closing_type}}">
        <div class="row align-items-center justify-content-between">
            <div class="col">
                <ul>
                    <li @if(empty($closing_type)) class="active" @endif><a href="{{ route('dap-services.centre-returned-items') }}">All </a></li>
                    <li @if($closing_type == 'notclosed') class="active" @endif><a href="{{ route('dap-services.centre-returned-items') }}?closing_type=notclosed">Yet To Close </a></li>
                    <li @if($closing_type == 'closed') class="active" @endif><a href="{{ route('dap-services.centre-returned-items') }}?closing_type=closed"> Closed </a></li>
                </ul>
            </div>            
        </div>        
    </div>
    </form>
    <div class="filter">
        <div class="row align-items-center justify-content-between">
            <div class="col">
                
            </div>            
            <div class="col-auto">
                <p>{{$totalResult}} Items</p>
            </div>
        </div>
    </div>
    <div class="row">        
        <table class="table">
            <thead>
                <tr>
                    <th class="sr_no">#</th>
                    <th class="primary_column">ID</th>
                    <th>Showroom</th>
                    <th>Customer Details</th>
                    <th>Item Details</th>
                    <th>Warranty Status</th>
                    <th>Closing Status</th>
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
                        {{ $item->dap_request->unique_id }}
                    </td data-colname="Showroom">
                    <td data-colname="Customer Details">{{ $item->dap_request->branch->name }}</td>
                    <td data-colname="Item Details">
                        <p class="small text-muted mb-1">
                            <span>Name: <strong>{{ $item->dap_request->customer_name }}</strong></span> <br/>
                            <span>Mobile: <strong>{{ $item->dap_request->mobile }} </strong></span> <br/>
                            <span>Phone: <strong>{{ $item->dap_request->phone }}</strong></span> <br/>
                        </p>
                    </td>
                    <td data-colname="Item Status">
                        <p class="small text-muted mb-1">
                            <span>Bill Date: <strong>{{ date('d/m/Y', strtotime($item->dap_request->bill_date)) }}</strong></span> <br/>
                            <span>Item: <strong>{{ $item->item }}</strong></span> <br/>
                            <span>Class: <strong>{{ $item->dap_request->class_name }}</strong></span> <br/>
                            <span>Barcode: <strong>{{ $item->barcode }}</strong> <br/>
                            
                        </p>
                    </td>
                    <td data-colname="Warranty Status">
                        @if (!empty($item->dap_request->in_warranty))
                            <span class="badge bg-success">In Warranty</span>
                        @else
                            <span class="badge bg-danger">Out Of Warranty</span>
                        @endif
                    </td>                   
                    <td data-colname="Closing Status">
                        @if (!empty($item->dap_request->is_closed))
                            <span class="badge bg-success">Closed</span>
                        @else
                            <span class="badge bg-danger">Yet To Close</span>
                        @endif
                    </td>
                    <td data-colname="Action">
                        

                        @if (empty($item->dap_request->in_warranty))
                            @if (empty($item->dap_request->is_paid))
                                
                                <!-- Make Payment modal -->
                                <button type="button" class="btn btn-outline-success select-md" data-bs-toggle="modal" data-bs-target="#staticMakePayment{{$item->dap_service_id}}">
                                    Make Payment
                                </button>
                                
                                <!-- Modal Make Payment -->
                                <div class="modal fade" id="staticMakePayment{{$item->dap_service_id}}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticMakePaymentLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="staticMakePaymentLabel">CLOSE CALL - {{ $item->dap_request->unique_id }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('dap-services.make-paid') }}" method="POST" class="closeCallForm">
                                                @csrf
                                                <input type="hidden" name="dap_service_id" value="{{$item->dap_service_id}}">
                                                <input type="hidden" name="request_url" value="{{Request::getQueryString()}}">
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-sm-5">
                                                            <div class="form-group">
                                                                <input type="text" name="" class="form-control" id="" placeholder="Total Service Charge: Rs. {{ number_format((float)$item->dap_request->total_service_charge, 2, '.', '') }}" readonly>
                                                            </div>
                                                        </div>
                                                    
                                                        <div class="col-sm-5">
                                                            <div class="form-group">
                                                                
                                                                <select name="payment_method" class="form-control" id="payment_method{{$item->dap_service_id}}" onchange="showPaidDiv({{$item->dap_service_id}},this.value);">
                                                                    <option value="">Choose payment option</option>
                                                                    <option value="cash">Cash</option>
                                                                    <option value="online">Online</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-2" id="paid_div_{{$item->dap_service_id}}" style="display: none;">
                                                            <div class="form-group">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox" value="1" id="is_paid{{$item->dap_service_id}}" name="is_paid" required>
                                                                    <label class="form-check-label" for="is_paid{{$item->dap_service_id}}">
                                                                        Mark As Paid
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-success submitCloseBtn" >Submit</button>
                                                </div>
                                            </form>
                                            
                                        </div>
                                    </div>
                                </div> 


                            @else
                                @if (empty($item->dap_request->is_closed))
                                    <a href="{{ route('dap-services.make-close', [Crypt::encrypt($item->dap_service_id),Request::getQueryString()] ) }}" onclick="return confirm('Are you sure?');" class="btn btn-outline-success select-md">Close Call</a>
                                @endif                                
                            @endif
                        @else
                            @if (empty($item->dap_request->is_closed))
                                <a href="{{ route('dap-services.make-close', [Crypt::encrypt($item->dap_service_id),Request::getQueryString()] ) }}" onclick="return confirm('Are you sure?');" class="btn btn-outline-success select-md">Close Call</a>
                            @endif                            
                        @endif
                    </td>
                </tr>
                @php
                    $i++;
                @endphp
                @empty
                <tr>
                    <td colspan="9" style="text-align: center;">No record found</td>
                </tr>  
                @endforelse
            </tbody>
        </table>
        {{$data->links()}}
    </div>  
</section>
<script>
    $(document).ready(function(){
        $('div.alert').delay(3000).slideUp(300);
    }); 
    
    $('input[type=search]').on('search', function () {
        // search logic here
        // this function will be executed on click of X (clear button)
        $('#searchForm').submit();
    });
    $('#service_centre_id').on('change', function(){
        $('#searchForm').submit();
    })
    function searchBranch(search){
        if(search.length > 0) {
            $.ajax({
                url: "{{ route('ajax.search-branches') }}",
                method: 'post',
                data: {
                    '_token': '{{ csrf_token() }}',
                    search: search
                },
                success: function(result) {
                    console.log(result);
                    var content = '';
                    if (result.length > 0) {
                        content += `<div class="dropdown-menu show  branch-dropdown select-md" aria-labelledby="dropdownMenuButton" style="width: 100%;">`;

                        $.each(result, (key, value) => {                            
                            content += `<a class="dropdown-item" href="javascript: void(0)" onclick="fetchBranch(${value.id},'${value.name}')">${value.name} </a>`;
                        })
                        content += `</div>`;
                        // $($this).parent().after(content);
                    } else {
                        content += `<div class="dropdown-menu show  branch-dropdown select-md" aria-labelledby="dropdownMenuButton"><li class="dropdown-item">No branch found</li></div>`;
                    }
                    $('#respBranch').html(content);
                }
            });
        } else {
            $('.branch-dropdown').hide()
        }
        
    }

    function fetchBranch(id,name) {
        $('.branch-dropdown').hide()
        $('#branch_id').val(id);
        $('#branch_name').val(name);
        $('#searchForm').submit();
    }

    function showPaidDiv(i,val){
        // alert(i+' '+val);
        $('#paid_div_'+i).show();
    }
    
</script>
@endsection