@extends('servicepartnerweb.layouts.app')
@section('content')
@section('page', 'Repair')
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
                    <li @if(!Request::get('closing_type') || (Request::get('closing_type') == 'all')) class="active" @endif><a href="{{route('servicepartnerweb.notification.list-repair')}}">All </a></li>
                    <li @if(Request::get('closing_type') == 'pending' ) class="active" @endif><a href="{{route('servicepartnerweb.notification.list-repair',['closing_type'=>'pending'])}}">Pending </a></li>
                    <li @if(Request::get('closing_type') == 'closed' ) class="active" @endif><a href="{{route('servicepartnerweb.notification.list-repair',['closing_type'=>'closed'])}}">Closed </a></li>
                    <li @if(Request::get('closing_type') == 'cancelled' ) class="active" @endif><a href="{{route('servicepartnerweb.notification.list-repair',['closing_type'=>'cancelled'])}}">Cancelled </a></li>
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
                    <td>{{$item->unique_id}}</td>
                    <td>
                        {{$item->pincode}}
                    </td>
                    <td>
                        <span>{{ date('d/m/Y', strtotime($item->created_at)) }} </span> <br/>
                    </td>
                    <td>
                        <p class="small text-muted mb-1">
                            <span>Bill No: <strong>{{$item->bill_no}}</strong></span> <br/>
                            <span>Delivery Date: <strong>{{ date('d/m/Y', strtotime($item->order_date))}}</strong></span> <br/>
                        </p>
                    </td>
                    <td>
                        <p class="small text-muted mb-1">
                            <span>Product Sl No: <strong>{{$item->product_sl_no}}</strong></span> <br/>
                            <span>Product Name: <strong>{{$item->product_name}}</strong></span> <br/>
                        </p>
                    </td>
                    <td>
                        <p class="small text-muted mb-1">
                            <span>Customer Name: <strong>{{$item->customer_name}}</strong></span> <br/>
                            <span>Phone No: <strong>{{$item->customer_phone}}</strong></span> <br/>
                            <span>Address: <strong>{{$item->address}}</strong></span> <br/>
                        </p>
                    </td>
                    <td>
                        @if ($item->warranty_status == 'yes')
                        @if ($item->warranty_date > date('Y-m-d', strtotime($item->created_at)))
                            <span class="badge bg-success">In Warranty</span>
                        @else
                            <span class="badge bg-danger">Out of Warranty</span>
                        @endif
                        <br/>

                        <span>Warranty End Date: <strong>{{ date('d/m/Y', strtotime($item->warranty_date)) }}</strong></span> <br/>
                        @endif
                        
                    </td>
                    <td>
                        
                        @if (!empty($item->is_closed))
                            <span class="badge bg-success">Closed</span>
                        @else

                            @if (empty($item->is_cancelled))
                                
                            
                            
                                @if (date('Y-m-d',strtotime($item->created_at)) == date('Y-m-d'))
                                    <span class="badge bg-success">Pending</span>
                                @elseif (date('Y-m-d',strtotime($item->created_at)) == date('Y-m-d',strtotime("-1 days")))
                                    <span class="badge bg-warning">Pending</span>
                                @elseif (date('Y-m-d',strtotime($item->created_at)) < date('Y-m-d',strtotime("-1 days")))
                                    <span class="badge bg-danger">Pending</span>
                                @endif

                            @else
                                <span class="badge bg-danger">Cancelled</span>
                            @endif
                        @endif
                        
                        
                    </td>
                    
                    <td>
                        @if (!empty($item->snapshot_file))
                            <a href="{{ asset($item->snapshot_file) }}" class="btn btn-outline-success select-md" target="_blank">View</a>
                        @endif

                    </td>
                    <td>
                        @if (empty($item->is_closed))

                            @if (empty($item->is_cancelled))
                             
                                <a href="{{ route('servicepartnerweb.repair-spare.add',[Crypt::encrypt($item->id),Request::getQueryString()]) }}" class="btn btn-outline-success select-md">Request Spares & Close Call</a>

                                <!-- Request OTP Close Call modal -->
                                <button type="button" class="btn btn-outline-success select-md" data-bs-toggle="modal" data-bs-target="#staticReqOTP{{$item->id}}">
                                    Request OTP Close Call
                                </button>
                            
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

                               
                            @endif
                        @endif

                        <!-- REMARK modal -->
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
</script>
@endsection