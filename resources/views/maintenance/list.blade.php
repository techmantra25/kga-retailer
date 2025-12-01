@extends('layouts.app')
@section('content')
@section('page', 'List Chimney Maintenance & Repair Request')
<section>
    <ul class="breadcrumb_menu">        
        <li>Chimney Maintenance & Repair Request </li>
        {{-- <li>{{ ucwords($service_type) }}</li> --}}
        <li>List Bookings</li>
    </ul>
    <div class="search__filter">
        <div class="row align-items-end justify-content-between">
            <div class="col mb-2 mb-sm-0">
                @if (Session::has('message'))
                <div class="alert alert-success" role="alert">
                    {{ Session::get('message') }}
                </div>
                @endif
            </div>
            
            <div class="col-auto mb-2 mb-sm-0">
                {{-- <a href="{{ route('maintenance.add') }}/{{$service_type}}" class="btn btn-outline-success select-md">Book New {{ucwords($service_type)}} Warranty Service</a>  --}}
            </div>
        </div>
    </div>
    <form action="" id="searchForm">
    <div class="search__filter">
        
        <input type="hidden" name="closing_type" value="{{$closing_type}}">
        <div class="row align-items-end justify-content-between">
            <div class="col mb-2 mb-sm-0">
                
            </div>
            <div class="col-auto mb-2 mb-sm-0">
                <div class="row g-3 align-items-center">
                    <div class="col-auto">
                        <select name="service_partner_id" class="form-control select-md" id="service_partner">
                            <option value="" selected hidden>Select Service Partner</option>
                            @forelse ($service_partners as $s)
                            <option value="{{$s->id}}" @if($service_partner_id == $s->id) selected @endif>{{$s->person_name}} - {{$s->company_name}} </option>
                            @empty
                            <option value=""> - No Service Partner Available -</option>
                            @endforelse                            
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-auto mb-2 mb-sm-0">
                <div class="row g-3 align-items-center">
                    <div class="col-auto">
                        <input type="hidden" name="created_at" id="created_at_val" value="{{$created_at}}">
                        <input @if(!empty($created_at)) type="date" @else type="text" onfocus="(this.type='date')" placeholder="Search By Booking Date" @endif  class="form-control select-md" @if(!empty($created_at)) value="{{ $created_at}}" @endif max="{{date('Y-m-d')}}"  id="created_at">
                    </div>
                </div>
            </div>
            <div class="col-auto mb-2 mb-sm-0">
                <div class="row g-3 align-items-center">
                    <div class="col-auto">
                        <input type="search" name="search" value="{{$search}}" class="form-control select-md" placeholder="Search ..">
                    </div>
                </div>
            </div>
            <div class="col-auto mb-2 mb-sm-0">
                <div class="row g-3 align-items-center">
                    <div class="col-auto">
                        <a href="{{ route('maintenance.list') }}" class="btn btn-warning select-md">Reset</a>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
    <div class="search__filter">
        
        <input type="hidden" name="closing_type" value="{{$closing_type}}">
        <div class="row align-items-center justify-content-between">
            <div class="col">
                <ul>
                    <li @if(empty($closing_type)) class="active" @endif><a href="{{ route('maintenance.list') }}">All </a></li>
                    <li @if($closing_type == 'pending') class="active" @endif><a href="{{ route('maintenance.list') }}?closing_type=pending">Pending </a></li>
                    <li @if($closing_type == 'closed') class="active" @endif><a href="{{ route('maintenance.list') }}?closing_type=closed">Closed </a></li>
                    <li @if($closing_type == 'cancelled') class="active" @endif><a href="{{ route('maintenance.list') }}?closing_type=cancelled">Cancelled </a></li>
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
                    <th>Date</th>
                    <th>Dealer</th>
                    <th>Service Partner</th>   
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
                    <td class="primary_column">{{ $item->unique_id }}<br>
                       
                    </td> 
                    <td data-colname="Date">
                        {{ date('d/m/Y', strtotime($item->created_at)) }}<br>
                        @if($item->repeat_call == 1)
                                @php
                                $repeat_mn_id = App\Models\Maintenance::where('id',$item->repeat_id)->pluck('unique_id');   
                                @endphp
                                <br>
                        <span class="badge bg-danger">Repeat Call</span><br>
                        <span class="badge bg-secondary" title="Repeat CPR Id">{{$repeat_mn_id}}</span>
                        @endif 
                    </td>  
                    <td data-colname="Dealer">
                        @if (!empty($item->dealer_id))
                            <span>{{ $item->dealer->name }}</span>
                        @endif
                    </td>                
                    <td data-colname="Service Partner">
                        <span>{{ $item->service_partner?$item->service_partner->company_name:"" }}</span>
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
                            <li>{{ ucwords($item->service_for) }} 
							{{ $item->service_type == 'cleaning' ? 'Normal Cleaning' : ucwords(str_replace("_"," ",$item->service_type)) }}
							</li>
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

                            @else
                                <span class="badge bg-danger">Cancelled</span>
                            @endif
                            
                            <br>

                            @if (empty($item->is_cancelled))
                                
                                <!-- Close Call modal -->
                                <button type="button" class="btn btn-outline-success select-md" data-bs-toggle="modal" data-bs-target="#staticBackdrop{{$item->id}}">
                                    Close Call
                                </button>
                                
                                <!-- Modal Close Call -->
                                <div class="modal fade" id="staticBackdrop{{$item->id}}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="staticBackdropLabel">CLOSE CALL - {{$item->unique_id}}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('maintenance.submit-call-close') }}" method="POST" class="closeCallForm">
                                                @csrf
                                                <input type="hidden" name="maintenance_id" value="{{$item->id}}">
                                                <input type="hidden" name="closed_by" value="{{Auth::user()->id}}">
                                                <input type="hidden" name="request_url" value="{{Request::getQueryString()}}">
                                                <div class="modal-body">
                                                    <div>
                                                        <h5>Q1. Have you called customer before close this call?</h5>
                                                        <div class="row">
                                                            <div class="col-2">
                                                                <input type="radio" name="qa1" value="yes" id="qa1yes" required>
                                                                <label for="qa1yes">Yes</label>
                                                            </div>
                                                            <div class="col-2">
                                                                <input type="radio" name="qa1" value="no" id="qa1no">
                                                                <label for="qa1no">No</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <h5>Q2. Is the {{$item->service_type == 'deep_cleaning' ? 'deep cleaning' : $item->service_type}} completed successfully?</h5>
                                                        <div class="row">
                                                            <div class="col-2">
                                                                <input type="radio" name="qa2" value="yes" id="qa2yes" required>
                                                                <label for="qa2yes">Yes</label>
                                                            </div>
                                                            <div class="col-2">
                                                                <input type="radio" name="qa2" value="no" id="qa2no">
                                                                <label for="qa2no">No</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @if ($item->service_type == 'repairing')
                                                    
                                                    <div>
                                                        <h5>Q3. Any new spares required for repairing the item?</h5>
                                                        <div class="row">
                                                            <div class="col-2">
                                                                <input type="radio" name="is_new_parts_required" value="1" id="qa3yes" required>
                                                                <label for="qa3yes">Yes</label>
                                                            </div>
                                                            <div class="col-2">
                                                                <input type="radio" name="is_new_parts_required" value="0" id="qa3no">
                                                                <label for="qa3no">No</label>
                                                            </div>
                                                        </div>
                                                    </div> 
                                                    @endif
                                                    <div>
                                                        <h5>Comments</h5>
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <textarea name="parts_description" class="form-control" id="parts_description" placeholder="Please add some comments" cols="3" rows="3"></textarea>
                                                            </div>
                                                            
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-success submitCloseBtn">Submit</button>
                                                </div>
                                            </form>
                                            
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif
                    </td>                    
                    <td data-colname="Action">
                        @if (empty($item->is_cancelled) && empty($item->is_closed))
                            <form action="{{ route('maintenance.cancel', [Crypt::encrypt($item->id),Request::getQueryString() ]) }}">
                                <input type="hidden" name="browser_name" class="browser_name">
                                <input type="hidden" name="navigator_useragent" class="navigator_useragent">
                                <button type="submit" onclick="return confirm('Are you sure?');"  class="btn btn-outline-danger select-md">Cancel Call</button>
                            </form>
                        @endif
                        <!-- Details modal -->
                        <button type="button" class="btn btn-outline-success select-md"  data-bs-toggle="modal" data-bs-target="#detailsData{{$item->id}}">
                            Details
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
                                                <span class="text-muted">Service Partner: </span>
                                                <a href="{{ route('service-partner.show', Crypt::encrypt($item->service_partner_id)) }}" target="_blank" class="btn btn-outline-secondary select-md">{{$item->service_partner?$item->service_partner->person_name:""}} - {{$item->service_partner?$item->service_partner->company_name:""}} </a>
                                            </div> 
											@if((empty($item->is_closed) && empty($item->is_cancelled)) || $closing_type == "pending")
												<form action="{{ route('maintenance.service-provider-update') }}" method="POST">
													@csrf <!-- Include CSRF token -->
													<div class="form-group mb-3">
														<select name="update_service_partner" class="form-control select-md" id="update_service_partner">
															<option value="" selected hidden>Select Service Partner</option>
															@foreach ($service_partners as $s)
																<option value="{{$s->id}}">{{$s->person_name}} - {{$s->company_name}}</option>
															@endforeach
														</select>
													</div>
													<input type="hidden" name="id" value="{{$item->id}}"></input>
													<button type="submit" class="btn btn-primary">Update</button>
												</form>
											@endif

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
                                    <form action="{{ route('maintenance.save_remark') }}" method="POST">
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



                        @if (!empty($item->is_spare_added))
                        <!-- View Requisition Spares modal -->
                        <button type="button" class="btn btn-outline-success select-md" data-bs-toggle="modal" data-bs-target="#viewSpares{{$item->id}}" title="{{$item->remarks}}">
                            Requisition Spares
                        </button>                        
                        <!-- View Requisition Spares Remark -->
                        <div class="modal fade" id="viewSpares{{$item->id}}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabelRemark" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="staticBackdropLabelRemark"> Requisition Spares / {{$item->unique_id}}</h5>
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
    function getBrowserType() {
        const test = regexp => {
            return regexp.test(navigator.userAgent);
        };
        console.log(navigator.userAgent);
        var navigator_useragent = navigator.userAgent;
        $('.navigator_useragent').val(navigator_useragent);
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
    $('.browser_name').val(browserType);
    $(document).ready(function(){
        $('div.alert').delay(3000).slideUp(300);
    });
    $("#myForm").submit(function() {
        $('input').attr('readonly', 'readonly');
        $('#submitBtn').attr('disabled', 'disabled');    
        $('#submitBtn').html('<i class="fi fi-br-refresh"></i>').append('   Please wait ...');
        return true;
    });
    $(".closeCallForm").submit(function() {
        $('input').attr('readonly', 'readonly');
        $('.submitCloseBtn').attr('disabled', 'disabled');   
        $('.submitCloseBtn').html('<i class="fi fi-br-refresh"></i>');     
        return true;
    });
    $('input[type=search]').on('search', function () {
        // search logic here
        // this function will be executed on click of X (clear button)
        $('#searchForm').submit();
    });
    $('#created_at').on('change', function(){
        $('#created_at_val').val(this.value);
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