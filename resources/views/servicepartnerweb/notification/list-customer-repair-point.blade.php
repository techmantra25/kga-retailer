@extends('servicepartnerweb.layouts.app')
@section('content')
@section('page', 'Customer Repair Point')
<section>
    <div class="row">
        <div class="col">
            @if (Session::has('message'))
            <div class="alert alert-success" role="alert">
                {{ Session::get('message') }}
            </div>
            @endif
            @if($errors->has('otp'))
                <div class="alert alert-danger" role="alert">
                    {{ $errors->first('otp') }}
                </div>
            @endif
        </div>                 
    </div>  
    <div class="search__filter">
        <form action="" id="searchForm">
        <div class="row align-items-center justify-content-between">
            <div class="col">
            <ul>
                    <li @if(empty($closing_type)) class="active" @endif><a href="{{ route('servicepartnerweb.notification.list-customer-repair-point') }}">All </a></li>
                    <li @if($closing_type == 'pending') class="active" @endif><a href="{{ route('servicepartnerweb.notification.list-customer-repair-point') }}?closing_type=pending"> Pending </a></li>
                    <li @if($closing_type == 'repairing') class="active" @endif><a href="{{ route('servicepartnerweb.notification.list-customer-repair-point') }}?closing_type=repairing"> Start Repairing </a></li>
                    <li @if($closing_type == 'pending-approval') class="active" @endif><a href="{{ route('servicepartnerweb.notification.list-customer-repair-point') }}?closing_type=pending-approval"> Pending For Admin Approval </a></li>
                    <li @if($closing_type == 'closed') class="active" @endif><a href="{{ route('servicepartnerweb.notification.list-customer-repair-point') }}?closing_type=closed">Closed</a></li>
                    <li @if($closing_type == 'cancelled') class="active" @endif><a href="{{ route('servicepartnerweb.notification.list-customer-repair-point') }}?closing_type=cancelled"> Cancelled </a></li>
                </ul>
            </div>
            <div class="col-auto">
                <div class="row g-3 align-items-center">
                    <div class="col-auto">
                        
                    </div>
                </div>
            </div>
            <div class="col-auto">
                <div class="row g-3 align-items-center">
                    <div class="col-auto">
                        
                        
                    </div>
                </div>
            </div>
            <div class="col-auto">
                <div class="row g-3 align-items-center">
                    <div class="col-auto">
                        <div class="row g-3 align-items-center">
                            <div class="col-auto">
                                <input type="hidden" name="uploaded_at" id="uploaded_at_val" value="{{$uploaded_at}}">
                                <input @if(!empty($uploaded_at)) type="date" @else type="text" onfocus="(this.type='date')" placeholder="Search Notification Date" @endif  class="form-control select-md" @if(!empty($uploaded_at)) value="{{ $uploaded_at}}" @endif max="{{date('Y-m-d')}}"  id="uploaded_at">
                                {{-- <input type="text" onfocus="(this.type='date')" name="" class="form-control select-md" value="@if(!empty($uploaded_at)){{date('d/m/Y', strtotime($uploaded_at))}}@endif" max="{{date('Y-m-d')}}" placeholder="Search Upload Date" id="uploaded_at"> --}}
                            </div>
                        </div>
                    </div>
                    <div class="col-auto">
                        <input type="search" name="search" value="{{$search}}" class="form-control select-md" placeholder="Search ..">
                    </div>
                </div>
            </div>
            <div class="col-auto">
                <div class="row g-3 align-items-center">
                    <div class="col-auto">
                        <a href="{{ route('servicepartnerweb.notification.list-repair') }}" class="btn btn-warning select-md">Reset</a>
                    </div>
                </div>
            </div>
        </div>
        </form>
    </div>
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
                    <th>#</th>
                    <th>ID</th>
                    <th>Pincode</th>  
                    <th>Notified At</th>
                    <th>Order Detail</th>
                    <th>Product Detail</th>    
                    <th>Customer Detail</th>
                    <th>Warranty Details</th>
                    <th>Closing Status</th>
                    <th>View Snapshot</th>
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
                    <td>
                        {{$item->pincode}}
                    </td>
                    <td>
                        <span>{{ date('d/m/Y', strtotime($item->created_at)) }} </span> <br/>
                    </td>
                    <td>
                        <p class="small text-muted mb-1">
                            <span>Bill No: <strong>{{$item->bill_no}}</strong></span> <br/>
                            <span>Delivery Date: <strong>{{ date('d/m/Y', strtotime($item->bill_date))}}</strong></span> <br/>
                        </p>
                    </td>
                    <td>
                        <p class="small text-muted mb-1">
                            <span>Product Sl No: <strong>{{$item->serial}}</strong></span> <br/>
                            <span>Product Name: <strong>{{$item->item}}</strong></span> <br/>
                        </p>
                    </td>
                    <td>
                        <p class="small text-muted mb-1">
                            <span>Customer Name: <strong>{{$item->customer_name}}</strong></span> <br/>
                            <span>Phone No: <strong>{{$item->mobile}}</strong></span> <br/>
                            <span>Alternate No: <strong>{{$item->alternate_no}}</strong></span> <br/>
                            <span>Address: <strong>{{$item->address}}</strong></span> <br/>
                        </p>
                    </td>
                    <td>
                        
                    <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#Warranty_status_data" onclick="getWarrantyData({{$item->product_id}}, '{{$item->dealer_type}}', '{{$item->bill_date}}')">View</button>
                        
                    </td>
                    <td>
                        @if ($item->status == 0)
                            <span class="badge bg-warning">Pending</span>
                        @elseif ($item->status == 1)
                            <span class="badge bg-warning">Warning for Generate Packing Slip for Service Partner</span>
                        @elseif ($item->status == 2)
                            <span class="badge bg-warning">Warning for Generate Invoice for Service Partner</span>
                        @elseif ($item->status == 3)
                            <span class="badge bg-info">Waiting for start repairing</span>
                        @elseif ($item->status == 4)
                            <span class="badge bg-warning">Pending For Admin Approval for Close Call</span>
                        @elseif ($item->status == 5)
                            <span class="badge bg-success">Closed</span>
                        @elseif ($item->status == 6)
                            <span class="badge bg-danger">Cancelled</span>
                        @endif
                    </td>
                    
                    <td>
                        @if (!empty($item->snapshot_file))
                            <a href="{{ asset($item->snapshot_file) }}" class="btn btn-outline-success select-md" target="_blank">View</a>
                        @endif

                    </td>
                    <td>
                                <a href="{{ route('servicepartnerweb.customer-repair-point.add_spare',[Crypt::encrypt($item->id),Request::getQueryString()]) }}" class="btn btn-outline-success select-md">Spares Parts</a>

                                <!-- Request OTP Close Call modal -->
                                <!-- <button type="button" class="btn btn-outline-success select-md" data-bs-toggle="modal" data-bs-target="#staticReqOTP{{$item->id}}">
                                    Request OTP Close Call
                                </button> -->
                            
                                <!-- Modal Request OTP Close Call -->
                                <div class="modal fade" id="staticReqOTP{{$item->id}}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticReqOTPLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-md">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="staticReqOTPLabel">CLOSE CALL  - {{$item->unique_id}} </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                                    
                                                <div class="modal-body">
                                                    <div>                                                
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <label for="">Phone No</label>
                                                                <input type="text" class="form-control" readonly value="{{ $item->customer_phone }}"  >
                                                            </div>
                                                        </div>
                                                    </div>                                            
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    <a  href="{{ route('servicepartnerweb.notification.close-otp-repair', [Crypt::encrypt($item->id),Request::getQueryString()]) }}" class="btn btn-success">Send  OTP</a>
                                                </div>
                                                                            
                                        </div>
                                    </div>
                                </div>



                                @if (!empty($item->closing_otp))
                                    @if (date('Y-m-d H:i') <= $item->closing_otp_expired_at )
                                        <span class="badge bg-info">OTP Generated </span>
                                    @else
                                        <span class="badge bg-warning">OTP Expired. Please Re-generate</span>
                                    @endif
                                    
                                @endif
                                <!-- CLOSE CALL modal -->
                                <button type="button" class="btn btn-outline-success select-md" data-bs-toggle="modal" data-bs-target="#staticBackdrop{{$item->id}}">
                                    Submit OTP Close Call
                                </button>
                            
                                <!-- Modal CLOSE CALL -->
                                <div class="modal fade" id="staticBackdrop{{$item->id}}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-md">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="staticBackdropLabel">CLOSE CALL  </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <ul class="breadcrumb_menu">
                                                <li> {{$item->bill_no}}</li>
                                                <li> {{ $item->product_sl_no }}</li>
                                            </ul>
                                            <form action="{{ route('servicepartnerweb.notification.submit-otp-repair', [Request::getQueryString()]) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="repair_id" value="{{$item->id}}">                                        
                                                <input type="hidden" name="request_url" value="{{Request::getQueryString()}}">
                                                <div class="modal-body">
                                                    <div>                                                
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <input type="text" name="otp" class="form-control" placeholder="Enter OTP" id="" maxlength="6" required onkeypress="validateNum(event)">
                                                            </div>
                                                        </div>
                                                    </div>                                            
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-success">Submit</button>
                                                </div>
                                            </form>                                    
                                        </div>
                                    </div>
                                </div>

                        <button type="button" class="btn btn-outline-success select-md" data-bs-toggle="modal" data-bs-target="#staticBackdropRemark{{$item->id}}" title="{{$item->remarks}}">
                            Remarks
                        </button>                        
                        <!-- Modal REMARK -->
                        <div class="modal fade" id="staticBackdropRemark{{$item->id}}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabelRemark" aria-hidden="true">
                            <div class="modal-dialog modal-md">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="staticBackdropLabelRemark">{{$item->unique_id}}  </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                   
                                    <form action="{{ route('servicepartnerweb.notification.save-remarks-repair') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="repair_id" value="{{$item->id}}">                                        
                                        <input type="hidden" name="request_url" value="{{Request::getQueryString()}}">
                                        <div class="modal-body">
                                            <div>                                                
                                                <div class="row">
                                                    <h5>Add Remarks</h5>
                                                    <div class="col-12">
                                                        <textarea name="remarks" class="form-control" required id="" placeholder="Please add remarks" cols="30" rows="10">{{ $item->remarks }}</textarea>
                                                    </div>
                                                </div>
                                            </div>                                            
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-success">Submit</button>
                                        </div>
                                    </form>
                                    
                                </div>
                            </div>
                        </div>

                        
                        @if (!empty($item->is_spare_added) && !empty($item->is_closed))
                            <!-- Spares modal -->
                            <button type="button" class="btn btn-outline-success select-md" data-bs-toggle="modal" data-bs-target="#staticBackdropSpares{{$item->id}}" title="{{$item->remarks}}">
                                View Spares
                            </button>                        
                            <!-- Spares REMARK -->
                            <div class="modal fade" id="staticBackdropSpares{{$item->id}}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabelRemark" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="staticBackdropLabelRemark">{{$item->unique_id}}  </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>                                    
                                            
                                        <div class="modal-body">
                                            <div class="table-responsive">
                                                <table class="table" id="prodHistTable">
                                                    <thead>
                                                        <th>#</th>
                                                        <th>Product</th>
                                                        <th>Non-Broken / Broken</th>
                                                        <th>Quantity</th>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $j=1;
                                                        @endphp
                                                        @foreach ($item->spares as $items)
                                                            <tr>
                                                                <td>{{$j}}</td>
                                                                <td>{{$items->spares->title}}</td>
                                                                <td>
                                                                    @if (empty($item->is_broken))
                                                                        <span>Non-broken</span>
                                                                    @else
                                                                        <span>Broken</span>
                                                                    @endif
                                                                </td>
                                                                <td>{{$items->quantity}}</td>
                                                            </tr>
                                                        @php
                                                            $j++;
                                                        @endphp
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>                                          
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>
                                                                            
                                    </div>
                                </div>
                            </div>

                            
                        @endif

                        @if (!empty($item->req_note))

                            <!-- View Requision Note modal -->
                            <button type="button" class="btn btn-outline-success select-md" data-bs-toggle="modal" data-bs-target="#viewReqNote{{$item->id}}" title="{{$item->remarks}}">
                                View Requisition Note
                            </button>                        
                            <!-- View Requision Note -->
                            <div class="modal fade" id="viewReqNote{{$item->id}}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabelRemark" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <form action="{{ route('servicepartnerweb.repair-spare.save-requisition-note') }}/{{Request::getQueryString()}}" method="POST">
                                            @csrf
                                            <input type="hidden" name="repair_id" value="{{ $item->id }}">
                                            <input type="hidden" name="service_partner_id" value="{{ Auth::user()->id }}">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="staticBackdropLabelRemark">View Requisition Note - {{$item->unique_id}}  </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>                                    
                                            
                                        <div class="modal-body">
                                            <textarea name="note" class="form-control req_note"  id="" >{{ $item->req_note->note }}</textarea>                                      
                                        </div>
                                        <div class="modal-footer">
                                            <button class="btn btn-success" type="submit">Save</button>
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>
                                        </form>
                                                                            
                                    </div>
                                </div>
                            </div>
                            
                        @endif
                        
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
                </tr>
                @php
                    $i++;
                @endphp
                @empty
                <tr>
                    <td colspan="11" style="text-align: center;">No record found</td>
                </tr>  
                @endforelse
            </tbody>
        </table>
        {{$data->links()}}
    </div>  
</section>

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
<script>
    $(document).ready(function(){
        $('div.alert').delay(7000).slideUp(300);

        ClassicEditor.create( document.querySelector( '.req_note' ) )
        .catch( error => {
            console.error( error );
        });
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
    })
    function validateNum(evt) {
        var theEvent = evt || window.event;

        // Handle paste
        if (theEvent.type === 'paste') {
            key = event.clipboardData.getData('text/plain');
        } else {
        // Handle key press
            var key = theEvent.keyCode || theEvent.which;
            key = String.fromCharCode(key);
        }
        var regex = /[0-9]/;
        if( !regex.test(key) ) {
            theEvent.returnValue = false;
            if(theEvent.preventDefault) theEvent.preventDefault();
        }
    }

    function getWarrantyData(product_id, dealer_type, bill_date){
        $.ajax({
            url: "{{ route('ajax.get-product-warranty-status') }}",
            method: 'post',
            data: {
                '_token': '{{ csrf_token() }}',
                id:product_id,
                order_date:bill_date,
                dealer_type: dealer_type,
                to_date: "{{date('Y-m-d')}}"
            },
            success: function(result) {
                var html = "";
                var dealer_text_type = dealer_type ? "Khosla" : "Non Khosla";
                if(result.status === true && result.data.length > 0) {
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

                    html += `</tbody>
                            </table>
                            </div>
                            </div>`;
                    
                    // Inject the generated HTML into the DOM element
                    $('#div_warranty').html(html);
                }else{
                    $('#div_warranty').html('');
                }
            }
        });  
    }
</script>
@endsection