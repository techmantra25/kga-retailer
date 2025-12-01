@extends('layouts.app')
@section('content')
@section('page', 'Installation Request')
<section>
    <ul class="breadcrumb_menu">        
        <li><a href="{{ route('service-partner.list') }}">Service Partner</a> </li>
        <li>Installation Request</li>
    </ul>
    <div class="row">
        <div class="col">
            @if (Session::has('message'))
            <div class="alert alert-success" role="alert">
                {{ Session::get('message') }}
            </div>
            @endif
        </div>
        <form id="myForm" action="{{ route('installation.upload-csv') }}" enctype="multipart/form-data" method="POST">
            @csrf
            
        <div class="row">
            <div class="col-sm-12">            
                <div class="card shadow-sm">
					<div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">Upload CSV <span class="text-danger">*</span></label>
                                <input type="file" name="csv" 
                                accept=".csv" 
                                class="form-control" id="">
                                @error('csv') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                                         
                    </div>  
					</div>
                </div>    
                <div class="card shadow-sm">
                    
                    <div class="card-body text-end">
                        
                        <a href="{{ url('/samplecsv/installation/sample-installation.csv') }}" class="btn btn-outline-primary ">Download Sample CSV</a>
                        
                        <button id="submitBtn" type="submit" class="btn  btn-success ">Submit </button>
                    </div>
                </div>                                       
            </div> 
            
        </div>                 
        </form>             
    </div>  
    <form action="" id="searchForm">
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
                    <li @if(!Request::get('closing_type') || (Request::get('closing_type') == 'all')) class="active" @endif><a href="{{route('installation.list')}}">All </a></li>
                    <li @if(Request::get('closing_type') == 'pending' ) class="active" @endif><a href="{{route('installation.list',['closing_type'=>'pending'])}}">Pending </a></li>
                    <li @if(Request::get('closing_type') == 'closed' ) class="active" @endif><a href="{{route('installation.list',['closing_type'=>'closed'])}}">Closed </a></li>
                    <li @if(Request::get('closing_type') == 'cancelled' ) class="active" @endif><a href="{{route('installation.list',['closing_type'=>'cancelled'])}}">Cancelled </a></li>
                </ul>
            </div>
            
            <div class="col-auto">
                <a href="{{ route('installation.add') }}" class="btn btn-outline-success select-md ">Book an Installation</a>
            </div>
        </div>
    </div>
    <div class="search__filter">
        <input type="hidden" name="closing_type" value="{{$closing_type}}">
        <div class="row align-items-center justify-content-between">
            
            <div class="col">
                <select name="service_partner" class="form-control select-md" id="service_partner">
                    <option value="" selected hidden>Select Service Partner</option>
                    @forelse ($service_partners as $s)
                    <option value="{{$s->id}}" @if($service_partner == $s->id) selected @endif>{{$s->person_name}} - {{$s->company_name}} </option>
                    @empty
                    <option value=""> - No Service Partner Available -</option>
                    @endforelse
                </select>
            </div>
            <div class="col-auto">
                <input type="hidden" name="uploaded_at" id="uploaded_at_val" value="{{$uploaded_at}}">
                <input @if(!empty($uploaded_at)) type="date" @else type="text" onfocus="(this.type='date')" placeholder="Search Booking Date" @endif  class="form-control select-md dates" @if(!empty($uploaded_at)) value="{{ $uploaded_at}}" @endif max="{{date('Y-m-d')}}"  id="uploaded_at">
            </div>
            <div class="col-auto">
                <input type="hidden" name="bill_date" id="bill_date_val" value="{{$bill_date}}">
                <input @if(!empty($bill_date)) type="date" @else type="text" onfocus="(this.type='date')" placeholder="Search Bill Date" @endif  class="form-control select-md dates" @if(!empty($bill_date)) value="{{ $bill_date}}" @endif max="{{date('Y-m-d')}}"  id="bill_date">
            </div>
            <div class="col-auto">
                <input type="search" name="search" value="{{$search}}" class="form-control select-md" placeholder="Search ..">
            </div>
            <div class="col-auto">
                <a href="{{ route('installation.list') }}?closing_type={{$closing_type}}" class="btn btn-warning select-md">Reset</a>
            </div>
               
        </div>
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
    </form>
    <table class="table">
        <thead>
            <tr>
                <th class="sr_no">#</th>
                <th class="primary_column">ID</th>
                <th>Booking Time</th>
                <th>Service Partner</th>   
                <th>Pincode</th>  
                <th>Order Detail</th>
                <th>Product Detail</th>    
                <th>Customer Detail</th>
                <th>Closing Status</th>
                <th>Urgent Status</th>
                <th>View Image</th>
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
                    {{ $item->unique_id }} <button type="button" class="toggle_table"></button>
                    <span>Goods Type: </span><span class="badge bg-success">{{ucwords($item->product?$item->product->goods_type:"")}} </span>
                </td>
                <td data-colname="Booking Time">
                    <p class="small text-muted mb-1">
                        @if (empty($item->dealer_user_id))
                            @if(!empty($item->csv_file_name))
                            <span>File Name: <strong>{{$item->csv_file_name}} </strong></span> <br/>
                            @endif
                            <strong>{{ date('j M Y h:i A, l', strtotime($item->created_at)) }} </strong><br/>
                        @else
                            <span>Dealer  </span><br/>
                            <span>
                                Name:- <strong>  {{$item->dealer->name}} </strong>
                            </span> <br/>
                            
                            <strong>{{ date('j M Y h:i A, l', strtotime($item->created_at)) }} </strong> <br/>
                        @endif                            
                    </p>
                </td>
                <td data-colname="Service Partner">
                    @if ($item->service_partner_id == 1)
                        <span class="badge bg-secondary">{{$item->service_partner_email}}</span> 
                        <span class="badge bg-info">Default</span> <br/>
                        <span class="badge bg-danger">No pincode assigned</span>
                    @else
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
                    @endif
                                            
                </td>
                <td data-colname="Pincode">
                    <strong>{{$item->pincode}}</strong>
                </td>
                <td data-colname="Order Detail">
                    <p class="small text-muted mb-1">
                        <span>Bill No: <strong>{{$item->bill_no}}</strong></span> <br/>
                        <span>Bill Date: <strong>{{ date('d/m/Y', strtotime($item->delivery_date))}}</strong></span> <br/>
                        <span>Salesman: <strong>{{$item->salesman}}</strong></span> <br/>
                    </p>
                </td>
                <td data-colname="Product Detail">
                    <p class="small text-muted mb-1">
                        <span>Product Sl No: <strong>{{$item->product_sl_no}}</strong></span> <br/>
                        <span>Product Name: <strong>{{$item->product_name}}</strong></span> <br/>
                        <span>Brand: <strong>{{$item->brand}}</strong></span> <br/>
                        <span>Class: <strong>{{$item->class}}</strong></span> <br/>
                        
                    </p>
                </td>
                <td data-colname="Customer Detail">
                    <p class="small text-muted mb-1">
                        <span>Customer Name: <strong>{{$item->customer_name}}</strong></span> <br/>
                        <span>Address: <strong>{{$item->address}}</strong></span> <br/>
                        <span>District: <strong>{{$item->district}}</strong></span> <br/>
                        <span>Mobile No: <strong>{{$item->mobile_no}}</strong></span> <br/>
                        <span>Phone No: <strong>{{$item->phone_no}}</strong></span>
                    </p>
                </td>
                <td data-colname="Closing Status">
                    @if (!empty($item->is_closed))
                        <span class="badge bg-success">Closed</span>
                    @else
                        
                        @if (empty($item->is_cancelled))
                            @if (date('Y-m-d',strtotime($item->created_at)) == date('Y-m-d') || date('Y-m-d H:i',strtotime($item->created_at)) == date('Y-m-d 23:10', strtotime("-1 days")) )
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
                            
                            <a href="{{ route('installation.edit', [Crypt::encrypt($item->id),Request::getQueryString()]) }}" class="btn btn-outline-success select-md">Edit Call</a> <br/>
                            
                            <!-- Close Call modal -->
                            <button type="button" class="btn btn-outline-success select-md" data-bs-toggle="modal" data-bs-target="#staticBackdrop{{$item->id}}">
                                Close Call
                            </button>
                            
                            <!-- Modal Close Call -->
                            <div class="modal fade" id="staticBackdrop{{$item->id}}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                <div class="modal-dialog modal-md">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="staticBackdropLabel">CLOSE CALL - {{$item->unique_id}}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('installation.submit-call-close') }}" method="POST" class="closeCallForm">
                                            @csrf
                                            <input type="hidden" name="installation_id" value="{{$item->id}}">
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
                                                    <h5>Q2. Is the installation completed successfully?</h5>
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
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-success submitCloseBtn" >Submit</button>
                                            </div>
                                        </form>
                                        
                                    </div>
                                </div>
                            </div>  
                        @endif
                    @endif
                </td>
                <td data-colname="Urgent Status">
                    @if (empty($item->is_closed))
                        @if (!empty($item->is_urgent))
                            <span class="badge bg-success">Urgent</span>   
                        @else
                            @if (empty($item->is_cancelled))
                                <a href="{{ route('installation.set-urgent', [Crypt::encrypt($item->id),Request::getQueryString() ]) }}" onclick="return confirm('Are you sure?');" class="btn btn-outline-success select-md">Set Urgent</a>
                            @endif
                            
                        @endif
                        
                    @else
                        @if (!empty($item->is_urgent))
                            <span class="badge bg-success">Urgent</span>   
                        @endif
                        
                    @endif
                </td>
                <td data-colname="View Image">
                    @if (!empty($item->snapshot_file))
                        <a href="{{ asset($item->snapshot_file) }}" class="btn btn-outline-success select-md" target="_blank">View</a>
                    @endif

                    @if (!empty($item->invoice_image))
                        <a href="{{ asset($item->invoice_image) }}" class="btn btn-outline-success select-md" target="_blank">View Invoice Image</a>
                    @endif

                    <a href="{{ route('feedback.form-installation',$item->id) }}" target="_blank"></a>
                    
                </td>
                <td data-colname="Action">
                    @if (empty($item->is_cancelled) && empty($item->is_closed))
                        <form action="{{ route('installation.cancel', [Crypt::encrypt($item->id),Request::getQueryString() ]) }}">
                            <input type="hidden" name="browser_name" class="browser_name">
                            <input type="hidden" name="navigator_useragent" class="navigator_useragent">
                            <button type="submit" onclick="return confirm('Are you sure?');"  class="btn btn-outline-danger select-md">Cancel Call</button>
                        </form>
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
                                <form action="{{ route('installation.save-remark') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="request_url" value="{{Request::getQueryString()}}">
                                    <input type="hidden" name="installation_id" value="{{ $item->id }}">
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

                    @if(!empty($item->is_closed) && !empty($item->close))
                    <!-- Close Call Remark modal -->
                    <button type="button" class="btn btn-outline-success select-md"  data-bs-toggle="modal" data-bs-target="#closeremarkData{{$item->id}}">
                        View Closing Remark
                    </button>
                    
                    <!-- Modal Close Call Remark -->
                    <div class="modal fade" id="closeremarkData{{$item->id}}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabelRemark" aria-hidden="true">
                        <div class="modal-dialog modal-md">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="staticBackdropLabel">CLOSE CALL - {{$item->unique_id}}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                
                                <div class="modal-body">
                                    <div>
                                        <h5>Q1. Have you called customer before close this call?</h5>
                                        <div class="row">
                                            <div class="col-2">
                                                <strong>{{ucwords($item->close->qa1)}}</strong>
                                            </div>
                                            
                                        </div>
                                    </div>
                                    <div>
                                        <h5>Q2. Is the installation completed successfully?</h5>
                                        <div class="row">
                                            <div class="col-2">
                                                <strong>{{ucwords($item->close->qa2)}}</strong>
                                                
                                            </div>
                                            
                                        </div>
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
        $('.filter-waiting-text').text('Please wait ... ');
        $('#searchForm').submit();
    });
    $('#uploaded_at').on('change', function(){
        $('.filter-waiting-text').text('Please wait ... ');
        $('#uploaded_at_val').val(this.value);
        $('#searchForm').submit();
    });
    $('.dates').on('keydown', function(){
        $('.filter-waiting-text').text('Please select date by clicking on calender icon ... ');
        $('.filter-waiting-text').delay(3000).fadeOut('slow');
        return false;
    });
    $('#bill_date').on('change', function(){
        $('.filter-waiting-text').text('Please wait ... ');
        $('#bill_date_val').val(this.value);
        $('#searchForm').submit();
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