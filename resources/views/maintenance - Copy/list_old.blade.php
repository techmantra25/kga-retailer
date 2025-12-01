@extends('layouts.app')
@section('content')
@if ($service_type == 'comprehensive')
@section('page', 'Chimney Maintenance Request - Free Maintenance')
@elseif ($service_type == 'extra')
@section('page', 'Chimney Maintenance Request - Additional Warranty')
@else
@section('page', 'Chimney Maintenance Request - Motor Warranty')
@endif

<section>
    <ul class="breadcrumb_menu">        
        <li>Chimney Maintenance Request </li>
        @if ($service_type == 'comprehensive')
        <li>Free Maintenance</li>
        @elseif ($service_type == 'extra')
        <li>Additional Warranty</li>
        @elseif ($service_type == 'motor')
        <li>Motor Warranty</li>
        @endif
        
    </ul>
    <div class="search__filter">
        <div class="row align-items-center justify-content-between">
            <div class="col">
                @if (Session::has('message'))
                <div class="alert alert-success" role="alert">
                    {{ Session::get('message') }}
                </div>
                @endif
            </div>
            
            <div class="col-auto">
                {{-- <a href="{{ route('maintenance.add') }}/{{$service_type}}" class="btn btn-outline-success select-md">Book New {{ucwords($service_type)}} Warranty Service</a>  --}}
            </div>
        </div>
    </div>
    <div class="search__filter">
        <form action="" id="searchForm">
        <input type="hidden" name="closing_type" value="{{$closing_type}}">
        <div class="row align-items-center justify-content-between">
            <div class="col">
                <ul>
                    <li @if(!Request::get('closing_type') || (Request::get('closing_type') == 'all')) class="active" @endif><a href="{{route('maintenance.list',$service_type)}}">All </a></li>
                    <li @if(Request::get('closing_type') == 'pending' ) class="active" @endif><a href="{{route('maintenance.list',$service_type)}}?closing_type=pending">Pending </a></li>
                    <li @if(Request::get('closing_type') == 'closed' ) class="active" @endif><a href="{{route('maintenance.list',$service_type)}}?closing_type=closed">Closed </a></li>
                </ul>
            </div>
            <div class="col-auto">
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
            <div class="col-auto">
                <div class="row g-3 align-items-center">
                    <div class="col-auto">
                        <input type="hidden" name="created_at" id="created_at_val" value="{{$created_at}}">
                        <input @if(!empty($created_at)) type="date" @else type="text" onfocus="(this.type='date')" placeholder="Search By Create Date" @endif  class="form-control select-md" @if(!empty($created_at)) value="{{ $created_at}}" @endif max="{{date('Y-m-d')}}"  id="created_at">
                    </div>
                </div>
            </div>
            <div class="col-auto">
                <div class="row g-3 align-items-center">
                    <div class="col-auto">
                        <input type="search" name="search" value="{{$search}}" class="form-control select-md" placeholder="Search ..">
                    </div>
                </div>
            </div>
            <div class="col-auto">
                <div class="row g-3 align-items-center">
                    <div class="col-auto">
                        <a href="{{ route('maintenance.list',$service_type) }}" class="btn btn-warning select-md">Reset</a>
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
                    <th>Date</th>
                    <th>Dealer</th>
                    <th>Service Partner</th>   
                    <th>Pincode</th>  
                    <th>Order Detail</th>
                    <th>Product Detail</th>    
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
                    <td>{{$i}}</td>
                    <td>{{ $item->unique_id }}</td>  
                    <td>
                        {{ date('d/m/Y', strtotime($item->created_at)) }}    
                    </td>  
                    <td>
                        @if (!empty($item->dealer_id))
                            <span>{{ $item->dealer->name }}</span>
                        @endif
                    </td>                
                    <td>
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
                    </td>
                    <td>
                        <strong>{{$item->pincode}}</strong>
                    </td>
                    <td>
                        <p class="small text-muted mb-1">
                            <span>Bill No: <strong>{{$item->bill_no}}</strong></span> <br/>
                            <span>Order Date: <strong>{{ date('d/m/Y', strtotime($item->order_date))}}</strong></span> <br/>
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
                            <span>Mobile No: <strong>{{$item->customer_phone}}</strong></span> <br/>
                            <span>Address: <strong>{{$item->address}}</strong></span> <br/>
                        </p>
                    </td>
                    <td>
                        @if (!empty($item->is_closed))
                            <span class="badge bg-success">Closed</span>
                        @else
                            
                            @if (date('Y-m-d',strtotime($item->created_at)) == date('Y-m-d'))
                                <span class="badge bg-success">Pending</span>
                            @elseif (date('Y-m-d',strtotime($item->created_at)) == date('Y-m-d',strtotime("-1 days")))
                                <span class="badge bg-warning">Pending</span>
                            @elseif (date('Y-m-d',strtotime($item->created_at)) < date('Y-m-d',strtotime("-1 days")))
                                <span class="badge bg-danger">Pending</span>
                            @endif
                            <br>

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
                                        <form action="{{ route('maintenance.submit-call-close',$service_type) }}" method="POST" class="closeCallForm">
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
                                                    <h5>Q2. Is the repair completed successfully?</h5>
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
                    </td>                    
                    <td>
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
                                    <form action="{{ route('maintenance.save_remark') }}/{{$service_type}}" method="POST">
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
                            View Requisition Spares
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
    })
</script>
@endsection