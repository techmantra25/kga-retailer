@extends('servicepartnerweb.layouts.app')
@section('content')
@section('page', 'Chimney Servicing')
@section('small', '(Cleaning & Repairing)')
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
            <input type="hidden" name="closing_type" value="{{$closing_type}}">
        <div class="row align-items-center justify-content-between">
            <div class="col">
                <ul>
                    <li @if(!Request::get('closing_type') || (Request::get('closing_type') == 'all')) class="active" @endif><a href="{{route('servicepartnerweb.maintenance.list')}}">All </a></li>
                    <li @if(Request::get('closing_type') == 'pending' ) class="active" @endif><a href="{{route('servicepartnerweb.maintenance.list',['closing_type'=>'pending'])}}">Pending </a></li>
                    <li @if(Request::get('closing_type') == 'closed' ) class="active" @endif><a href="{{route('servicepartnerweb.maintenance.list',['closing_type'=>'closed'])}}">Closed </a></li>
                    <li @if($closing_type == 'cancelled') class="active" @endif><a href="{{ route('servicepartnerweb.maintenance.list') }}?closing_type=cancelled">Cancelled </a></li>
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
                                <input type="hidden" name="created_at" id="uploaded_at_val" value="{{$created_at}}">
                                <input @if(!empty($created_at)) type="date" @else type="text" onfocus="(this.type='date')" placeholder="Search Notification Date" @endif  class="form-control select-md" @if(!empty($created_at)) value="{{ $created_at}}" @endif max="{{date('Y-m-d')}}"  id="created_at">
                                
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
                        <a href="{{ route('servicepartnerweb.maintenance.list') }}" class="btn btn-warning select-md">Reset</a>
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
                    <th class="sr_no">#</th>
                    <th class="primary_column">ID</th>
                    <th>Date</th>
                    <th>Dealer</th>
                    <th>Pincode</th>
                    <th>Item Name</th>   
                    <th>Warranty Status</th> 
                    <th>Maintenence & Service Type</th>
                    <th>Customer Detail</th>
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
                    <td class="primary_column">{{ $item->unique_id }}</td>  
                    <td data-colname="Date">
                        {{ date('d/m/Y', strtotime($item->created_at)) }}    
                    </td> 
                    <td data-colname="Dealer">
                        @if (!empty($item->dealer_id))
                            <span>{{ $item->dealer->name }}</span>
                        @endif
                    </td> 
                    <td data-colname="Pincode">
                        <span>{{$item->pincode}}</span>
                    </td>
                    
                    <td data-colname="Item Name">
                        <span>{{$item->product_name}}</span>
                    </td>
                    <td data-colname="Warranty Status">
                        @if (!empty($item->out_of_warranty))
                            <span class="badge bg-danger">Out Of Warranty</span> <br/>
                        @else
                        <span class="badge bg-success">In Warranty</span> <br/>
                        @endif

                        
                        @if (!empty($item->is_spare_chargeable))
                            <span class="badge bg-info">Spare Chargeable</span> <br/>
                        @endif
                        @if (!empty($item->is_repair_chargeable))
                            <span class="badge bg-info">Repair Chargeable</span> <br/>
                        @endif
                        
                    </td>
                    <td>
                        <ul class="pincodeclass">
                            <li>{{ ucwords(str_replace("_"," ",$item->maintenance_type)) }}</li>
                            <li>{{ ucwords($item->service_for) }} {{ ucwords($item->service_type) }}</li>
                        </ul>
                    </td>
                    <td data-colname="Customer Details">
                        <span>{{$item->customer_name}} ({{$item->customer_phone}})</span>
                    </td>
                    <td data-colname="Closing Status">
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
                                <br>

                            @else
                                <span class="badge bg-danger">Cancelled</span>
                            @endif

                            
                        @endif
                    </td>                    
                    <td data-colname="Action">

                        <!-- Details modal -->
                        <button type="button" class="btn btn-outline-success select-md"  data-bs-toggle="modal" data-bs-target="#detailsData{{$item->id}}">
                            View Details
                        </button>
                        
                        <!-- Modal Details -->
                        <div class="modal fade" id="detailsData{{$item->id}}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabelDetails" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="staticBackdropLabelDetails">
                                            {{$item->unique_id}} 
                                            @if (!empty($item->out_of_warranty))
                                                <span class="badge bg-danger">Out Of Warranty</span> <br/>
                                            @else
                                                <span class="badge bg-success">In Warranty</span> <br/>
                                            @endif
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    
                                    <div class="modal-body">                                        
                                        <div class="row">
                                            <div class="form-group mb-3">
                                                <span class="text-muted">Booking Date : </span>{{date('m/d/Y', strtotime($item->created_at))}}
                                            </div>  
                                            <div class="form-group mb-3">
                                                <span class="text-muted">Service : </span>
                                                <span class="badge bg-info">{{ucwords($item->service_for)}} {{ucwords($item->service_type)}}</span> 
                                            </div>                                           
                                            
                                            <div class="form-group mb-3">
                                                <span class="text-muted">Dealer : </span>{{$item->dealer->name}} 
                                            </div> 
                                            <div class="form-group mb-3">
                                                <span class="text-muted">Pincode : </span>{{$item->pincode}} 
                                            </div> 
                                            <div class="form-group mb-3">
                                                <span class="text-muted">Service Partner : </span>
                                                <a href="{{ route('service-partner.show', Crypt::encrypt($item->service_partner_id)) }}" target="_blank" class="btn btn-outline-secondary select-md">{{$item->service_partner->person_name}} - {{$item->service_partner->company_name}} </a>
                                            </div> 
                                            <div class="form-group mb-3">
                                                <span class="text-muted">Order Date : </span>{{date('m/d/Y', strtotime($item->order_date))}}
                                            </div>
                                            <div class="form-group mb-3">
                                                <span class="text-muted">Bill No : </span>{{$item->bill_no}} 
                                            </div> 
                                            <div class="form-group mb-3">
                                                <span class="text-muted">Product Serial No : </span>{{$item->product_sl_no}} 
                                            </div> 
                                            <div class="form-group mb-3">
                                                <span class="text-muted">Product Name : </span>
                                                <a href="{{ route('product.show', Crypt::encrypt($item->product_id)) }}" class="btn btn-outline-secondary select-md" target="_blank">{{$item->product_name}}</a>
                                            </div> 
                                            <div class="form-group mb-3">
                                                <span class="text-muted">Customer : </span>{{$item->customer_name}}  ({{$item->customer_phone}})
                                            </div> 

                                            

                                            
                                            @if ($item->service_type == 'repairing')
                                            <div class="form-group mb-3">
                                                <span class="text-muted">Spare Chargeable : </span>
                                                @if (!empty($item->is_spare_chargeable))
                                                <span class="badge bg-success">Yes</span> <br/>
                                                @else
                                                <span class="badge bg-danger">No</span> <br/>
                                                @endif
                                            </div>                                            
                                            <div class="form-group mb-3">
                                                <span class="text-muted">Repair Chargeable : </span>
                                                @if (!empty($item->is_repair_chargeable))
                                                <span class="badge bg-success">Yes</span> <br/>
                                                @else
                                                <span class="badge bg-danger">No</span> <br/>
                                                @endif
                                            </div>
                                            @endif
                                            
                                            

                                            <div class="form-group mb-3">
                                                <span class="text-muted">Address : </span>{{$item->address}} 
                                            </div> 
                                            <div class="form-group mb-3">
                                                <span class="text-muted">Remarks : </span>{{$item->remarks}}
                                            </div> 
                                        </div>                                   
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        
                                    </div>
                                   
                                </div>
                            </div>
                        </div>



                        @if (empty($item->is_closed) && empty($item->is_cancelled))

                            @if($item->service_type == 'repairing')
                            <a href="{{ route('servicepartnerweb.maintenance.add-spare-parts',[Crypt::encrypt($item->id),Request::getQueryString()]) }}" class="btn btn-outline-success select-md">Request Spares & Close Call</a>
                            @endif
                                                        
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
                                                <a  href="{{ route('servicepartnerweb.maintenance.close-otp-request', [Crypt::encrypt($item->id),Request::getQueryString()]) }}" class="btn btn-success">Send  OTP</a>
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
                                            <li> {{$item->unique_id}}</li>
                                        </ul>
                                        <form action="{{ route('servicepartnerweb.maintenance.submit-closing-otp', [Request::getQueryString()]) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="maintenance_id" value="{{$item->id}}">                                        
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


                        <!-- Remark modal -->
                        <button type="button" class="btn btn-outline-success select-md" title="{{$item->remarks}}"  data-bs-toggle="modal" data-bs-target="#remarkData{{$item->id}}">
                            Remark
                        </button>
                        
                        <!-- Modal Remark -->
                        <div class="modal fade" id="remarkData{{$item->id}}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabelRemark" aria-hidden="true">
                            <div class="modal-dialog modal-md">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="staticBackdropLabelRemark"> {{$item->unique_id}}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('servicepartnerweb.maintenance.save_remark') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="request_url" value="{{Request::getQueryString()}}">
                                        <input type="hidden" name="maintenance_id" value="{{ $item->id }}">
                                        <div class="modal-body">
                                            <div>
                                                <h5>Remarks</h5>
                                                <div class="row">
                                                    <div class="col-12">
                                                        <textarea name="remarks" class="form-control" id="" cols="30" rows="10" placeholder="Please add remarks" required>{{ $item->remarks }}</textarea>
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
                            Requisition Spares
                        </button>                        
                        <!-- Spares REMARK -->
                        <div class="modal fade" id="staticBackdropSpares{{$item->id}}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabelRemark" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="staticBackdropLabelRemark">Requisition Spares / {{$item->unique_id}}  </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>                                    
                                        
                                    <div class="modal-body">
                                        <div class="table-responsive">
                                            <table class="table" id="prodHistTable">
                                                <thead>
                                                    <th>#</th>
                                                    <th>Product</th>
                                                    <th>Quantity</th>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $j=1;
                                                        // dd($item->spares1);
                                                    @endphp
                                                    @foreach ($item->spares1 as $spares)
                                                        <tr>
                                                            <td>{{$j}}</td>
                                                            <td>{{$spares->spares->title}}</td>
                                                            <td>{{$spares->quantity}}</td>
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

                    </td>
                </tr>
                @php
                    $i++;
                @endphp
                @empty
                <tr>
                    <td colspan="12" style="text-align: center;">No record found</td>
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
    });
    
    $('input[type=search]').on('search', function () {
        // search logic here
        // this function will be executed on click of X (clear button)
        $('#searchForm').submit();
    });
    $('#created_at').on('change', function(){
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