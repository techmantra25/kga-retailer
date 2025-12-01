@extends('layouts.app')
@section('content')
@section('page', 'List Bookings')
<section>
    <ul class="breadcrumb_menu">        
        <li>Inhouse DAP Servicing</li>
        <li>List Bookings</li>
    </ul>    
    <form action="" id="searchForm">
    <div class="search__filter">
        @if (Session::has('message'))
        <div class="alert alert-success" role="alert">
            {{ Session::get('message') }}
        </div>
        @endif
        <div class="row  justify-content-end">
            <div class="col">
                <a href="{{ route('dap-services.checkdapitemstatus') }}" class="btn btn-outline-success ">Book New</a>
            </div>
            <div class="col-md-2">
                <input type="hidden" name="entry_date" id="entry_date_val" value="{{$entry_date}}">
                <input @if(!empty($entry_date)) type="date" @else type="text" onfocus="(this.type='date')" placeholder="Search By Booking Date" @endif  class="form-control " @if(!empty($entry_date)) value="{{ $entry_date}}" @endif max="{{date('Y-m-d')}}"  id="entry_date">
            </div>
            <div class="col-md-3">
                <input type="search" name="search" value="{{$search}}" class="form-control " placeholder="Search ID,Customer,Item etc ...">
            </div>
            <div class="col-md-auto">
                <a href="{{ route('dap-services.list') }}?branch_id={{$branch_id}}&branch_name={{$branch_name}}" class="btn btn-warning ">Reset Date & Search</a>
            </div>
        </div>
        
        
    </div>
    <div class="search__filter">
        <div class="row  justify-content-end">
            <!-- <div class="col">

                <a href="{{ route('dap-services.generate-road-challan') }}" class="btn btn-outline-success ">Generate Road Challan</a>
            </div> -->
            <div class="col-md-4">
                <input type="text" autocomplete="off" name="branch_name" class="form-control" placeholder="Search branch where item dropped at..." onkeyup="searchBranch(this.value);" id="branch_name" value="{{ $branch_name }}">
                <input type="hidden" name="branch_id" id="branch_id" value="{{ $branch_id }}">
                <div class="respBranch" id="respBranch" style="position: relative;"></div>
            </div>            
            
            <div class="col-auto">
                <a href="{{ route('dap-services.list') }}?entry_date={{$entry_date}}&search={{$search}}" class="btn btn-warning ">Reset Banch</a>
            </div>
        </div>
    </div>
    <div class="search__filter">        
        <input type="hidden" name="reaching_status" value="{{$reaching_status}}">
        <div class="row align-items-center justify-content-between">
            <div class="col">
                <ul>
                    <li @if(empty($reaching_status)) class="active" @endif><a href="{{ route('dap-services.list') }}">All </a></li>
                    <li @if($reaching_status == 'paid') class="active" @endif><a href="{{ route('dap-services.list') }}?reaching_status=paid">Paid</a></li>
                    <li @if($reaching_status == 'closed') class="active" @endif><a href="{{ route('dap-services.list') }}?reaching_status=closed"> Closed </a></li>
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
                    <th>Booking Date</th>
                    <th>Showroom</th>
                    <th>Customer</th>
                    <th>Item</th>   
                    <th>Warranty Status</th> 
                    <th>Assign Engg.</th>
                    <th>Payment</th>
                    <th>Repair Status</th>
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
                    <td>{{$i}}</td>
                    <td style="display: flex; flex-direction:column;">{{$item->unique_id}}
                    <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModal{{$item->id}}">
                    Issue
                    </button>
                      
                    </td>
                    <td>{{ date('j M Y, l', strtotime($item->entry_date)) }}</br>
                        @if($item->repeat_call == 1)
                            @php
                            $repeat_dap_id = App\Models\DapService::where('id',$item->repeat_dap_id)->pluck('unique_id');   
                            @endphp
                        <span class="badge bg-danger">Repeat Call</span><br>
                        <span class="badge bg-secondary" title="Repeat DAP Id">{{$repeat_dap_id}}</span>
                        @endif
                    </td>
                    <td>{{ $item->branch->name }}</td>
                    <td>
                        <p class="small text-muted mb-1">
                            <span>Name: <strong>{{ $item->customer_name }}</strong></span> <br/>
                            <span>Mobile: <strong>{{ $item->mobile }}</strong></span> <br/>
                            <span>Phone: <strong>{{ $item->phone }}</strong></span> <br/>
                        </p>
                    </td>
                    <td>
                        <p class="small text-muted mb-1">
                            <span>Serial: <strong>{{ $item->serial }}</strong></span> <br/>
                            <span>Item: <strong>{{ $item->item }}</strong></span> <br/>
                            <span>Class: <strong>{{ $item->class_name }}</strong></span> <br/>
                            <span>Barcode: <button class="showdetails" title="Download Barcode" onclick="downloadImage('{{$item->barcode}}')">{{ $item->barcode }}</button></span> <br/>
                        </p>
                    </td>
                    <td>
                        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#Warranty_status_data" data-entry="{{$item->entry_date}}" onclick="getWarrantyData({{$item->product_id}}, 'khosla', '{{$item->bill_date}}', this)">View</button>
                    </td>
                    <td>
                    @if (isset($item->assign_service_perter_id))
                        @if($item->servicePartner)
                        <button type="button" data-bs-toggle="modal" data-bs-target="#exampleModalReassign{{$item->id}}"><span class="badge bg-success">{{$item->servicePartner->person_name}}</span></button>
                        @endif
                    @else
                    <span class="badge bg-danger">Yet not assign</span>
                    @endif
                    </td>  
                    <td>
                        @if ($item->is_paid == 0)
                            <span class="badge bg-warning">Processing</span>
                        @elseif($item->is_paid == 1)
                            <span class="badge bg-success">Paid</span>
                        @elseif($item->is_paid == 2)
                            <span class="badge bg-info">Pending</span>
                        @endif
                    </td> 
                    <td>    
                        @if ($item->is_closed == 0)
                            <span class="badge bg-warning">Pending</span>
                        @elseif($item->is_closed == 1)
                            <span class="badge bg-success">Closed</span>
                        
                        @endif
                    </td>     
                    <td> 
                         <a href="{{route('dap-services.dap-barcode',  Crypt::encrypt($item->id))}}" class="btn btn-outline-primary btn-sm" title="Edit">Barcode</a>
                    @if ($item->quotation_status == 1)
                        <a href="{{route('dap-services.dap-quotation',  Crypt::encrypt($item->id))}}" class="btn btn-outline-success btn-sm" title="Edit">Quotation</a>
                    @endif
                        <a href="{{route('dap-services.dap-track',  Crypt::encrypt($item->id))}}" class="btn btn-outline-primary btn-sm" title="Edit">Track</a>

                    </td>
                <!-- Modal -->
                        <div class="modal fade" id="exampleModal{{$item->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <h5 class="modal-title text-center text-danger">Product issue:</h5>
                                    <div class="modal-body">
                                    {{$item->issue}}
                                        </div>
                                            <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- reassign modal -->
                        <div class="modal fade" id="exampleModalReassign{{$item->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Reassign Engineer</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form method="post" action="{{route('dap-services.reassign-engineer')}}" id="form_{{ $item->id }}">
                                    @csrf
                                    <div class="modal-body">
                                    @if (isset($item->assign_service_perter_id))
                                        @if($item->servicePartner)
                                        <p>Current assign engg:({{$item->servicePartner->id}}) <strong>{{$item->servicePartner->person_name?$item->servicePartner->person_name:""}}</strong></p>
                                        @endif
                                    @else
                                    <span class="badge bg-danger">Yet not assign</span>
                                    @endif
                                    <label>Choose Engg:</label>
                                    <select name="reassign_engg" id="reassign_engg{{$item->id}}" class="form-control">
                                        @if($servicePartners)
                                            <option value="" >--select--</option>
                                            @foreach( $servicePartners as $partner)
                                                <option value="{{$partner->id}}" {{$partner->id==$item->assign_service_perter_id?"selected":""}}>{{$partner->person_name}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    </div>
                                    <input type="hidden" name="dap_id" value="{{$item->id}}">
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="button" class="btn btn-primary" onclick="FormSubmit('{{$item->id}}')">Save</button>
                                    </div>
                                <form>
                                </div>
                            </div>
                        </div>


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
    <div class="modal fade" id="Warranty_status_data" tabindex="-1" aria-labelledby="Warranty_status_data_by"
    aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg"> <!-- Added modal-lg for large modal -->
            <div class="modal-content">
                <div class="modal-body" id="div_warranty">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
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
    $('#entry_date').on('change', function(){
        $('#entry_date_val').val(this.value);
        $('#searchForm').submit();
    });
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

    function downloadImage(name){
        var url = "https://bwipjs-api.metafloor.com/?bcid=code128&includetext&text="+name;
        console.log('Fetching URL:', url); // Debugging line
        fetch(url)
            .then(resp => resp.blob())
            .then(blob => {
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.style.display = 'none';
                a.href = url;
                // the filename you want
                a.download = name;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
            })
            .catch(() => alert('An error sorry'));
    }
    function FormSubmit(itemId) {
        var service_partner = $('#reassign_engg' + itemId).val();
        console.log(service_partner);
        $.ajax({
            url: "{{ route('dap-services.reassign-engineer') }}",
            method: 'post',
            data: {
                '_token': '{{ csrf_token() }}',
                id:itemId,
                service_partner:service_partner,
            },
            success: function(result) {
                location.reload();
            },
        });
    }
    function getWarrantyData(product_id, dealer_type, bill_date, element){
        var entry_date = $(element).attr('data-entry');
        $.ajax({
            url: "{{ route('ajax.get-product-warranty-status') }}",
            method: 'post',
            data: {
                '_token': '{{ csrf_token() }}',
                id:product_id,
                order_date:bill_date,
                dealer_type: dealer_type,
                to_date: entry_date
            },
            success: function(result) {
                var html = "";
                var dealer_text_type = "Khosla";
                html += `<div class="card shadow-sm">
                            <div class="card-header bg-light">
                                <span class="badge bg-secondary">Dealer Type: <span id="dealer_text_type">${dealer_type}<span></span>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm table-borderless">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Warranty Type</th>
                                            <th>Warranty Period (Months)</th>
                                            <th>Warranty End Date</th>
                                            <th>Warranty Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>`;
                    if(result.status === true && result.data.length > 0) {

                        $.each(result.data, function(key, item) {
                            html += `<tr>
                                        <td> ${item.warranty_type.charAt(0).toUpperCase() + item.warranty_type.slice(1)}`;

                            // Checking if warranty_type is "additional"
                            if(item.warranty_type === "additional") {
                                html += ` <span class="badge bg-danger" style="cursor: pointer; font-size: 9px;">
                                            ${item.additional_warranty_type == 1 ? "Parts Chargeable" : "Service Chargeable"}
                                        </span>`;
                            }

                            // Checking if warranty_type is "cleaning"
                            if(item.warranty_type === "cleaning") {
                                html += ` <span class="badge bg-danger" style="cursor: pointer; font-size: 9px;" title="Number of cleaning">
                                            ${item.number_of_cleaning}
                                        </span>`;
                            }

                            // Adding spear_goods if available
                            if(item.parts) {
                                html += ` <span class="badge bg-success"> ${item.parts}</span>`;
                            }

                            html += `</td>
                                    <td> <span class="badge bg-success">${item.warranty_period}</span></td>
                                    <td> <span class="badge bg-${item.warranty_status==="YES"?"success":"danger"}">${item.warranty_end_date}</span></td>
                                    <td> <span class="badge bg-${item.warranty_status==="YES"?"success":"danger"}">${item.warranty_status}</span></td>
                                    </tr>`;
                        });
                    }else{
                         html += `<tr>
                            <td colspan="4"><span class="badge bg-danger">No Data found!</span></td>
                            </tr>`;
                    }
                    html += `</tbody>
                            </table>
                            </div>
                            </div>`;
                    
                    // Inject the generated HTML into the DOM element
                    $('#div_warranty').html(html);
                
            }
        });  
    }


    
</script>
@endsection