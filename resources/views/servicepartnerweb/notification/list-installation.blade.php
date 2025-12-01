@extends('servicepartnerweb.layouts.app')
@section('content')
@section('page', 'Installation')
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
                    <li @if(!Request::get('closing_type') || (Request::get('closing_type') == 'all')) class="active" @endif><a href="{{route('servicepartnerweb.notification.list-installation')}}">All </a></li>
                    <li @if(Request::get('closing_type') == 'pending' ) class="active" @endif><a href="{{route('servicepartnerweb.notification.list-installation',['closing_type'=>'pending'])}}">Pending </a></li>
                    <li @if(Request::get('closing_type') == 'closed' ) class="active" @endif><a href="{{route('servicepartnerweb.notification.list-installation',['closing_type'=>'closed'])}}">Closed </a></li>
                    <li @if(Request::get('closing_type') == 'cancelled' ) class="active" @endif><a href="{{route('servicepartnerweb.notification.list-installation',['closing_type'=>'cancelled'])}}">Cancelled </a></li>
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
                        <a href="{{ route('servicepartnerweb.notification.list-installation') }}" class="btn btn-warning select-md">Reset</a>
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
                    <th>Closing Status</th>
                    <th>Urgent Status</th>
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
                            <span>Delivery Date: <strong>{{ date('d/m/Y', strtotime($item->delivery_date))}}</strong></span> <br/>
                            <span>Salesman: <strong>{{$item->salesman}}</strong></span> <br/>
                        </p>
                    </td>
                    <td>
                        <p class="small text-muted mb-1">
                            <span>Product Sl No: <strong>{{$item->product_sl_no}}</strong></span> <br/>
                            <span>Product Name: <strong>{{$item->product_name}}</strong></span> <br/>
                            <span>Brand: <strong>{{$item->brand}}</strong></span> <br/>
                            <span>Class: <strong>{{$item->class}}</strong></span> <br/>
                        </p>
                    </td>
                    <td>
                        <p class="small text-muted mb-1">
                            <span>Customer Name: <strong>{{$item->customer_name}}</strong></span> <br/>
                            <span>Address: <strong>{{$item->address}}</strong></span> <br/>
                            <span>District: <strong>{{$item->district}}</strong></span> <br/>
                            <span>Mobile No: <strong>{{$item->mobile_no}}</strong></span> <br/>
                            <span>Phone No: <strong>{{$item->phone_no}}</strong></span>
                        </p>
                    </td>
                    <td>
                        
                        @if (!empty($item->is_closed))
                            <span class="badge bg-success">Closed</span>
                        @else

                            @if (empty($item->is_cancelled))                            
                            
                                @if (date('Y-m-d',strtotime($item->created_at)) == date('Y-m-d') || date('Y-m-d H:i',strtotime($item->created_at)) == date('Y-m-d 23:10', strtotime("-1 days")))
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
                        @if (!empty($item->is_urgent))
                            <span class="badge bg-success">Urgent</span>
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
                                
                                {{-- <a href="{{ route('servicepartnerweb.notification.close-otp-installation', [Crypt::encrypt($item->id),Request::getQueryString()]) }}" class="btn btn-outline-success select-md">Request OTP Close Call</a> --}}

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
                                                                <input type="text" class="form-control" readonly value="{{ $item->mobile_no }}"  >
                                                            </div>
                                                        </div>
                                                    </div>                                            
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    <a  href="{{ route('servicepartnerweb.notification.close-otp-installation', [Crypt::encrypt($item->id),Request::getQueryString()]) }}" class="btn btn-success">Send  OTP</a>
                                                </div>
                                                                            
                                        </div>
                                    </div>
                                </div>

                                @if (!empty($item->closing_otp))
                                    @if (date('Y-m-d H:i') <= $item->closing_otp_expired_at )
                                        <span class="badge bg-info">OTP Generated</span>
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
                                                <li> {{$item->unique_id}}</li>
                                            </ul>
                                            <form action="{{ route('servicepartnerweb.notification.submit-otp-installation', [Request::getQueryString()]) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="installation_id" value="{{$item->id}}">                                        
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
                                   
                                    <form action="{{ route('servicepartnerweb.notification.save-remarks-installation') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="installation_id" value="{{$item->id}}">                                        
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

                        @if (empty($item->invoice_image))
                            

                        <!-- UPLOAD INVOICE IMAGE modal -->
                        <button type="button" class="btn btn-outline-success select-md" data-bs-toggle="modal" data-bs-target="#uploadInvoiceSnapshot{{$item->id}}" title="{{$item->remarks}}">
                            Upload Invoice Snapshot
                        </button>
                        
                        <!-- Modal UPLOAD INVOICE IMAGE -->
                        <div class="modal fade" id="uploadInvoiceSnapshot{{$item->id}}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="uploadInvoiceSnapshotLabel" aria-hidden="true">
                            <div class="modal-dialog modal-md">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="uploadInvoiceSnapshotLabel">{{$item->unique_id}}  </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                   
                                    <form action="{{ route('servicepartnerweb.notification.submit-invoice-image-installation') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="installation_id" value="{{$item->id}}">                                        
                                        <input type="hidden" name="request_url" value="{{Request::getQueryString()}}">
                                        <div class="modal-body">
                                            <div>                                                
                                                <div class="row">
                                                    <h5>Upload Invoice Image</h5>
                                                    <div class="col-12">
                                                        <input type="file" name="invoice_image" class="form-control" required id="invoice_image" accept="image/*">
                                                    </div>
                                                </div>
                                            </div>                                            
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-success">Upload</button>
                                        </div>
                                    </form>
                                    
                                </div>
                            </div>
                        </div>

                        @else 
                            @if (!empty($item->invoice_image))
                                <a href="{{ asset($item->invoice_image) }}" class="btn btn-outline-success select-md" target="_blank">View Invoice Image</a>
                            @endif
                        
                        @endif
                        
                    </td>
                </tr>
                @php
                    $i++;
                @endphp
                @empty
                <tr>
                    <td colspan="8" style="text-align: center;">No record found</td>
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