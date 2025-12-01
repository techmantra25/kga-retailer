@extends('layouts.app')
@section('content')
@section('page', 'Repair Request')
<section>
    <ul class="breadcrumb_menu">        
        <li><a href="{{ route('service-partner.list') }}">Service Partner</a> </li>
        <li>Repair Request</li>
    </ul>
    

    <div class="search__filter">
        <div class="row align-items-end justify-content-between">
            
            <div class="col">    
                @if (Session::has('message'))
                <div class="alert alert-success" role="alert">
                    {{ Session::get('message') }}
                </div>
                @endif            
                <h5>List Request</h5>
                <ul>
                    <li @if(!Request::get('closing_type') || (Request::get('closing_type') == 'all')) class="active" @endif><a href="{{route('repair.list')}}">All </a></li>
                    <li @if(Request::get('closing_type') == 'pending' ) class="active" @endif><a href="{{route('repair.list',['closing_type'=>'pending'])}}">Pending </a></li>
                    <li @if(Request::get('closing_type') == 'closed' ) class="active" @endif><a href="{{route('repair.list',['closing_type'=>'closed'])}}">Closed </a></li>
                    <li @if(Request::get('closing_type') == 'cancelled' ) class="active" @endif><a href="{{route('repair.list',['closing_type'=>'cancelled'])}}">Cancelled </a></li>
                    <li @if(Request::get('closing_type') == 'repeated' ) class="active" @endif><a href="{{route('repair.list',['closing_type'=>'repeated'])}}">Repeated </a></li>
                </ul>
            </div>
            
            <div class="col-auto">
                <a href="{{ route('repair.add') }}" class="btn btn-outline-success select-md">Book a Repair</a> 
            </div>
        </div>
    </div>

    <div class="search__filter">
        <form action="" id="searchForm">
        <input type="hidden" name="closing_type" value="{{$closing_type}}">
        <div class="row align-items-center justify-content-between">
            <div class="col">
            </div>
            
            <div class="col-auto">
                <select name="service_partner_id" class="form-control select-md" id="service_partner">
                    <option value="" selected hidden>Select Service Partner</option>
                    @forelse ($service_partners as $s)
                    <option value="{{$s->id}}" @if($s->id == $service_partner_id) selected @endif>{{$s->person_name}} - {{$s->company_name}} </option>
                    @empty
                    <option value=""> - No Service Partner Available -</option>
                    @endforelse
                    
                </select>
            </div>
            <div class="col-auto">
                <input type="hidden" name="uploaded_at" id="uploaded_at_val" value="{{$uploaded_at}}">
                <input @if(!empty($uploaded_at)) type="date" @else type="text" onfocus="(this.type='date')" placeholder="Search Entry Date" @endif  class="form-control select-md" @if(!empty($uploaded_at)) value="{{ $uploaded_at}}" @endif max="{{date('Y-m-d')}}"  id="uploaded_at">
                        
            </div>
            <div class="col-auto">
                <input type="search" name="search" value="{{$search}}" class="form-control select-md" placeholder="Search ..">
                    
            </div>
            <div class="col-auto">
                <a href="{{ route('repair.list') }}?closing_type={{$closing_type}}" class="btn btn-warning select-md">Reset</a>
                    
            </div>
        </div>
        </form>
    </div>
    <div class="filter">
        <div class="row align-items-center justify-content-between">
            <div class="col">                     
                <span class="small filter-waiting-text" id=""></span>
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
                <th class="primary_column">ID</th>
                <th>Created At</th>
                <th>Service Partner</th>
                <th>Pincode</th>
                <th>Dealer Details</th>
                <th>Customer Details</th>
                <th>Product Details</th>
                <th>Warranty Details</th>
                <th>View Snapshot</th>
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
                    {{ $item->unique_id }} <br/>
                    @if (!empty($item->is_repeated))
                        <span class="badge bg-info">Repeated</span>
                    @endif
                    <button type="button" class="toggle_table"></button>
                </td>
                <td data-colname="Created At">
                    {{ date('d/m/Y', strtotime($item->created_at)) }} 
                </td>
                <td data-colname="Service Partner">
                    <p class="small text-muted mb-1">
                        
                        @if (!empty($item->service_partner->company_name))
                        <span>
                            Company Name:- <strong>  {{$item->service_partner->company_name}} </strong>
                        </span> <br/>
                        @endif
                        @if (!empty($item->service_partner->person_name))
                        <span>
                            Person Name:- <strong>  {{$item->service_partner->person_name}} </strong>
                        </span> <br/>
                        @endif
                        @if (!empty($item->service_partner_email))
                        <span>
                            Email:- <strong>  {{$item->service_partner_email}} </strong>
                        </span> <br/>
                        @endif
                        @if (!empty($item->service_partner->phone))
                        <span>
                            Phone:- <strong>  {{$item->service_partner->phone}} </strong>
                        </span> <br/>
                        @endif
                        
                        
                    </p>
                </td>
                <td data-colname="Pincode"><strong>{{$item->pincode}}</strong></td>
                <td data-colname="Dealer Details">
                    <p class="small text-muted mb-1">
                        @if (!empty($item->dealer))
                        <span>
                            {{$item->dealer->name}}
                        </span> <br/>
                        @endif
                        
                        {{-- <span>
                            Person Name:- <strong>  {{$item->dealer_user->name}} </strong>
                        </span> <br/> --}}
                    </p>
                </td>
                <td data-colname="Customer Details">
                    <p class="small text-muted mb-1">
                        <span>Name: <strong>{{$item->customer_name}}</strong></span> <br/>
                        <span>Phone No: <strong>{{$item->customer_phone}}</strong></span> <br/>
                        <span>Address: <strong>{{$item->address}}</strong></span> <br/>
                    </p>
                </td>
                <td data-colname="Product Details">
                    <p class="small text-muted mb-1">
                        <span>Serial No: <strong>{{$item->product_sl_no}}</strong></span> <br/>
                        <span>Name: <strong>{{$item->product_name}}</strong></span> <br/>
                        <span>Bill No: <strong>{{$item->bill_no}}</strong></span> <br/>
                        <span>Value: <strong>{{$item->product_value}}</strong></span> <br/>
                        @if ($item->warranty_status == 'yes')
                            
                            <span>Warranty Period: <strong>{{$item->warranty_period}}</strong></span> <br/>
                            
                        @else
                            <span>Warranty Status: <strong>{{$item->warranty_status}}</strong></span> <br/>
                        @endif
                        
                    </p>
                </td>
                <td data-colname="Warranty Details">
                    @if (!empty($item->in_warranty))                    
                        <span class="badge bg-success">In Warranty</span>
                    @else
                        <span class="badge bg-danger">Out of Warranty</span>
                    @endif
                    <br/>
                    <span>Order Date: <strong>{{ date('d/m/Y', strtotime($item->order_date)) }}</strong></span> <br/>
                    <span>Warranty End Date: <strong>{{ date('d/m/Y', strtotime($item->warranty_date)) }}</strong></span> <br/>
                    
                </td>
                <td data-colname="View Snapshot">
                    @if (!empty($item->snapshot_file))
                        <a href="{{ asset($item->snapshot_file) }}" class="btn btn-outline-success select-md" target="_blank">View</a>
                    @endif
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


                        @if (empty($item->is_cancelled))
                            
                            <a href="{{ route('repair.edit', [Crypt::encrypt($item->id),Request::getQueryString()]) }}" class="btn btn-outline-success select-md">Edit Call</a> <br/>
                            
                            <!-- Button trigger modal -->
                            <button type="button" class="btn btn-outline-success select-md" data-bs-toggle="modal" data-bs-target="#staticBackdrop{{$item->id}}">
                                Close Call
                            </button>
                            
                            <!-- Modal -->
                            <div class="modal fade" id="staticBackdrop{{$item->id}}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="staticBackdropLabel">CLOSE CALL - {{$item->bill_no}}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('repair.submit-call-close') }}" method="POST" class="closeCallForm">
                                            @csrf
                                            <input type="hidden" name="repair_id" value="{{$item->id}}">
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
                        
                    @endif
                </td>
                <td data-colname="Action">
                    @if (empty($item->is_cancelled) && empty($item->is_closed))
                        <form action="{{ route('repair.cancel', [Crypt::encrypt($item->id),Request::getQueryString() ]) }}">
                            <input type="hidden" name="browser_name" class="browser_name">
                            <input type="hidden" name="navigator_useragent" class="navigator_useragent">

                            <button type="submit" onclick="return confirm('Are you sure?');" class="btn btn-outline-danger select-md">Cancel Call</button>
                        </form>
                        {{-- <a href="{{ route('repair.cancel', [Crypt::encrypt($item->id),Request::getQueryString() ]) }}" onclick="return confirm('Are you sure?');" class="btn btn-outline-danger select-md">Cancel Call</a> --}}
                    @endif

                    <!-- Remark modal -->
                    <button type="button" class="btn btn-outline-success select-md" data-bs-toggle="modal" data-bs-target="#remarkData{{$item->id}}" title="{{$item->remarks}}">
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
                                <form action="{{ route('repair.save-remark') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="request_url" value="{{Request::getQueryString()}}">
                                    <input type="hidden" name="repair_id" value="{{ $item->id }}">
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
                                                        <th>Non-broken / Broken</th>
                                                        <th>Quantity</th>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $j=1;
                                                        @endphp
                                                        @foreach ($item->spares as $spares)
                                                            <tr>
                                                                <td>{{$j}}</td>
                                                                <td>{{$spares->spares->title}}</td>
                                                                <td>
                                                                    @if (empty($spares->is_broken))
                                                                        <span>Non-broken</span>
                                                                    @else
                                                                        <span>Broken</span>
                                                                    @endif
                                                                </td>
                                                                <td>{{$spares->quantity}}</td>
                                                            </tr>
                                                        @php
                                                            $j++;
                                                        @endphp
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>  
                                            @if (!empty($item->req_note))
                                                
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <h6> Requisition Note </h6>    
                                                    <textarea name="note" class="form-control req_note"  id="" >{{ $item->req_note->note }}</textarea>   
                                                </div>    
                                            </div> 
                                            @endif                                       
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>                                    
                                </div>
                            </div>
                        </div>
                    @endif


                    @if(!empty($item->is_closed) && !empty($item->close))
                        <!-- Close Call Remark modal -->
                        <button type="button" class="btn btn-outline-success select-md"  data-bs-toggle="modal" data-bs-target="#closeremarkData{{$item->id}}">
                            View Closing Remark
                        </button>
                        
                        <!-- Modal Close Call Remark -->
                        <div class="modal fade" id="closeremarkData{{$item->id}}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabelRemark" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="staticBackdropLabel">CLOSE CALL - {{$item->bill_no}}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('repair.submit-call-close') }}" method="POST" class="closeCallForm">
                                        @csrf
                                        <input type="hidden" name="repair_id" value="{{$item->id}}">
                                        <input type="hidden" name="closed_by" value="{{Auth::user()->id}}">
                                        <input type="hidden" name="request_url" value="{{Request::getQueryString()}}">
                                        <div class="modal-body">
                                            <div>
                                                <h5>Q1. Have you called customer before close this call?</h5>
                                                <div class="row">
                                                    <div class="col-2">
                                                        <strong>{{ ucwords($item->close->qa1) }}</strong>
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <h5>Q2. Is the repair completed successfully?</h5>
                                                <div class="row">
                                                    <div class="col-2">
                                                        <strong><strong>{{ ucwords($item->close->qa2) }}</strong></strong>
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <h5>Q3. Any new spares required for repairing the item?</h5>
                                                <div class="row">
                                                    <div class="col-2">
                                                        <strong>{{ ($item->close->is_new_parts_required == 1) ? 'Yes' : 'No' }}</strong>
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <h5>Comments</h5>
                                                <div class="row">
                                                    <div class="col-12">
                                                        <p>{{ $item->close->parts_description }}</p>
                                                    </div>
                                                    
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </form>
                                    
                                </div>
                            </div>
                        </div>
                    @endif

                    @if (count($item->spare_returns) == 0 && empty($item->is_cancelled))
                        
                    <a href="{{ route('repair.add-spares', [Crypt::encrypt($item->id),Request::getQueryString()]) }}" class="btn btn-outline-success select-md">Add Spares </a>
                    @endif
                        {{-- {{$item->is_not_returnd_spare}} --}}
                    @if (count($item->is_not_returnd_spare) > 0)
                    <a href="{{ route('repair.remove-spares', [Crypt::encrypt($item->id),Request::getQueryString()]) }}" onclick="return confirm('Are you sure want to remove spares?');" class="btn btn-outline-success select-md">Remove Spares ({{count($item->spare_returns)}}) </a>
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

        ClassicEditor.create( document.querySelector( '.req_note' ) )
        .then(editor => { 
            console.log( editor ); 
            editor.isReadOnly = true; // make the editor read-only right after initialization
        } )
        .catch( error => {
            console.error( error );
        }); 
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
        $('.filter-waiting-text').text('Please wait ... ');
        $('#searchForm').submit();
    });
    $('#uploaded_at').on('change', function(){
        $('.filter-waiting-text').text('Please wait ... ');
        $('#uploaded_at_val').val(this.value);
        $('#searchForm').submit();
    });
    $('#uploaded_at').on('keydown', function(){
        $('.filter-waiting-text').text('Please select date by clicking on calender icon ... ');
        $('.filter-waiting-text').delay(3000).fadeOut('slow');
        return false;
    });
    $('#service_partner').on('change', function(){
        $('.filter-waiting-text').text('Please wait ... ');
        $('#searchForm').submit();
    })
	$('.toggle_table').click(function(){
		$(this).parents('tr').toggleClass('is-expanded');
	});
</script>
@endsection