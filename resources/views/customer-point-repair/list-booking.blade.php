@extends('layouts.app')
@section('content')
@section('page', 'List Bookings')
<section>
    <ul class="breadcrumb_menu">        
        <li><a href="{{route('customer-point-repair.list-booking')}}">Customer Point Repair</a></li>
        <li>List Bookings</li>
    </ul>    
    <form action="" id="searchForm">
    <div class="search__filter">
        @if (Session::has('message'))
        <div class="alert alert-success" role="alert">
            {{ Session::get('message') }}
        </div>
        @endif
        @if (Session::has('success'))
        <div class="alert alert-success" role="alert">
            {{ Session::get('success') }}
        </div>
        @endif
        @if (Session::has('error'))
        <div class="alert alert-error" role="alert">
            {{ Session::get('error') }}
        </div>
        @endif
        @if (Session::has('warning'))
        <div class="alert alert-warning" role="alert">
            {{ Session::get('warning') }}
        </div>
        @endif
        <div class="row  justify-content-end">
            <div class="col">
                <a href="{{ route('customer-point-repair.check-product-details') }}" class="btn btn-outline-success ">Book New</a>
            </div>
            <div class="col-md-2">
                <input type="hidden" name="entry_date" id="entry_date_val" value="{{$entry_date}}">
                <input @if(!empty($entry_date)) type="date" @else type="text" onfocus="(this.type='date')" placeholder="Search By Booking Date" @endif  class="form-control " @if(!empty($entry_date)) value="{{ $entry_date}}" @endif max="{{date('Y-m-d')}}"  id="entry_date">
            </div>
            <div class="col-md-3">
                <input type="search" name="search" value="{{$search}}" class="form-control " placeholder="Search ID,Customer,Item etc ...">
            </div>
          
            <div class="col-auto">
                <a href="{{ route('customer-point-repair.list-booking') }}" class="btn btn-warning ">Reset Date & Search</a>
            </div>
          
        </div>
        
        
    </div>
    <div class="search__filter">
        <div class="row  justify-content-end">
            <div class="col-md-4">
                <input type="text" autocomplete="off" name="service_partner_name" class="form-control" id="service_partner_name" value="" onkeyup="searchServicePartner(this.value);" placeholder="Search service partner ... " value={{$service_partner_name}}> 
                <input type="hidden" name="service_partner_id" id="service_partner_id" value="{{$service_partner_id}}">
                <div class="respDropServicePartner" id="respDropServicePartner" style="position: relative;"></div>
            </div>            
            
            <div class="col-auto">
                <a href="{{ route('customer-point-repair.list-booking') }}?entry_date={{$entry_date}}&search={{$search}}&service_partner_name={{$service_partner_name}}" class="btn btn-warning ">Reset Name</a>
            </div>
        </div>
    </div>
    <div class="search__filter">        
        <input type="hidden" name="reaching_status" value="{{$reaching_status}}">
        <div class="row align-items-center justify-content-between">
            <div class="col">
                <ul>
                    <li @if(empty($reaching_status)) class="active" @endif><a href="{{ route('customer-point-repair.list-booking') }}">All </a></li>
                    <li @if($reaching_status == 'pending') class="active" @endif><a href="{{ route('customer-point-repair.list-booking') }}?reaching_status=pending"> Pending </a></li>
                    <li @if($reaching_status == 'repairing') class="active" @endif><a href="{{ route('customer-point-repair.list-booking') }}?reaching_status=repairing"> Start Repairing </a></li>
                    <li @if($reaching_status == 'pending-approval') class="active" @endif><a href="{{ route('customer-point-repair.list-booking') }}?reaching_status=pending-approval"> Pending For Admin Approval </a></li>
                    <li @if($reaching_status == 'success') class="active" @endif><a href="{{ route('customer-point-repair.list-booking') }}?reaching_status=success">Success</a></li>
                    <li @if($reaching_status == 'closed') class="active" @endif><a href="{{ route('customer-point-repair.list-booking') }}?reaching_status=closed">Closed</a></li>
                    <li @if($reaching_status == 'cancelled') class="active" @endif><a href="{{ route('customer-point-repair.list-booking') }}?reaching_status=cancelled"> Cancelled </a></li>
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
                    <th>Customer</th>
                    <th>Item</th>   
                    <th>Warranty Status</th> 
                    <th>Closing Status</th> 
                    <th>File</th> 
                    <th>Assign Engg.</th>
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
                    <button type="button" class="btn btn-outline-danger btn-sm select-md" data-bs-toggle="modal" data-bs-target="#exampleModal{{$item->id}}">
                    Issue
                    </button>
                      
                    </td>
                    <td>
                        {{ date('j M Y, l', strtotime($item->entry_date)) }} <br/>
                        @if ($item->dealer_type == 'khosla')
                            <span class="badge bg-success">Khosla</span>
                        @else
                            <span class="badge bg-danger">Non-Khosla</span>
                        @endif

                        @if($item->repeat_call == 1)
                            @php
                            $repeat_crp_id = App\Models\CustomerPointService::where('id',$item->repeat_crp_id)->pluck('unique_id');   
                            @endphp
                            <br>
                        <span class="badge bg-danger">Repeat Call</span><br>
                        <span class="badge bg-secondary" title="Repeat CPR Id">{{$repeat_crp_id}}</span>
                        @endif
                    </td>
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
                        <button type="button" class="btn btn-outline-primary btn-sm select-md" data-bs-toggle="modal" data-bs-target="#Warranty_status_data" onclick="getWarrantyData({{$item->product_id}}, '{{$item->dealer_type}}', '{{$item->bill_date}}')">View</button>
                        @if ($item->in_warranty == 0 && $item->final_amount>0)
                            <span class="badge bg-danger">Out of Warranty</span>
                        @endif
                        @if ($item->in_warranty == 1)
                            <span class="badge bg-success">In Warranty</span>
                        @endif
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
                            <span class="badge bg-warning">Invoice Generated for customer</span>
                        @elseif ($item->status == 6)
                            <span class="badge bg-warning">Payment link Send and waiting for payment</span>
                        @elseif ($item->status == 7)
                            <span class="badge bg-success">Payment Success</span>
                        @elseif ($item->status == 8)
                            <span class="badge bg-success">Closed</span>
                              <!-- Admin approval logic when status is 8 -->
                            @if ($item->admin_approval == 1)
                                <span class="badge bg-warning">Admin Approved</span>
                            @elseif ($item->admin_approval == 2)
                                <span class="badge bg-warning">Admin Rejected</span>
                            @endif 
                        @elseif ($item->status == 9)
                            <span class="badge bg-danger">Cancelled</span>  
                            <!-- cancel logic when status is 9 -->
                            @if ($item->cancelled_by == 1)
                            <br>
                                <span class="badge bg-warning">Cancelled by admin</span>
                            @elseif ($item->cancelled_by == 2)
                            <br>
                                <span class="badge bg-warning">Cancelled by service-partner</span>
                            @endif 
                            @if(!empty($item->cancelled_reason))
                            <br>
                                <button class="btn btn-outline-danger btn-sm select-md" data-bs-toggle="modal" data-bs-target="#exampleModalCancellReason{{$item->id}}">Reason</button>
                            @endif

                        @endif

                    </td>
                  
                    <td>
                        @if (!empty($item->snapshot_file))
                        <a href="{{ asset($item->snapshot_file) }}" target="_blank" class="btn btn-outline-primary btn-sm select-md">View</a>
                        @else
                        No file Uploaded
                        @endif
                  
                    </td>
                    <td>
                    @if (isset($item->assign_service_perter_id))
                        @if($item->servicePartner)
                        <a href="javascript: void(0)" data-bs-toggle="modal" data-bs-target="#exampleModalReassign{{$item->id}}"><span class="btn btn-success btn-sm select-md">{{$item->servicePartner->person_name}}</span></a>
                        @endif
                    @else
                    <span class="badge bg-danger">Yet not assign</span>
                    @endif
                    </td>    
                    <td> 
                        @if($item->status <=3)
                            <button type="button" data-bs-toggle="modal" data-bs-target="#exampleModalCancell{{$item->id}}" class="btn btn-outline-danger btn-sm select-md">Cancell</button>
                        @endif
                        {{-- <a href="{{route('customer-point-repair.crp-barcode',  Crypt::encrypt($item->id))}}" class="btn btn-outline-primary btn-sm select-md" title="Edit">Barcode</a> --}}
                        <a href="{{route('customer-point-repair.add-spare',  Crypt::encrypt($item->id))}}" class="btn btn-outline-primary btn-sm select-md" title="Edit">{{$item->SpareData && count($item->SpareData)>0?"Show Spare Details":"Add Spare Parts"}}</a>
                        @if($item->status == 4 && !empty($item->remarks) && $item->is_spare_required == 0 && $item->admin_approval == 0)
                            <button type="button" data-bs-toggle="modal" data-bs-target="#exampleModalApprove{{$item->id}}" class="btn btn-outline-primary btn-sm select-md">Approve?</button>
                            @elseif($item->status == 8 && $item->admin_approval == 2)
                            <button class="btn btn-outline-danger btn-sm select-md">Rejected</button>
                            @elseif($item->status == 8 && $item->admin_approval == 1)
                            <button class="btn btn-outline-success btn-sm select-md">Approved</button>
                            @else
                        @endif
                        @php
                            $sales_order_id = App\Models\SalesOrder::where('crp_id', $item->id)->orderBy('id', 'DESC')->value('id');
                        @endphp
                        @if($item->status==1 && $sales_order_id)
                            <a href="{{ route('sales-order.generate-packing-slip', [Crypt::encrypt($sales_order_id),Request::getQueryString()] ) }}" class="btn btn-outline-success select-md">Generate Packing Slip</a>
                        @endif
                        @php
                        $sales_order_id = $sales_order_id?$sales_order_id:null;
                             $Packingslip_id = App\Models\Packingslip::where('sales_order_id', $sales_order_id)->orderBy('id', 'DESC')->value('id');
                        @endphp
                        @if($item->status==2 && $Packingslip_id)
                        <a href="{{ route('packingslip.raise-invoice', [Crypt::encrypt($Packingslip_id),Request::getQueryString()]) }}" class="btn btn-outline-primary select-md">Raise Invoice</a>
                        @endif
                        @if(!empty($item->SpareData) && $item->SpareData->count() > 0   && $item->status >= 7 && $item->return_spare==0)
                            <a href="{{ route('return-spares.store_crp_spare', ['crp_id' => $item->id]) }}" 
                            class="btn btn-outline-warning select-md"
                            onclick="return confirm('Are you sure you want to return this spare?');">
                                Return Spare
                            </a>
                        @endif
                        @if($item->return_spare==1 && is_null($item->return_spare_order))
                            <a href="#" 
                                class="btn btn-warning select-md">
                                    Return Spare Completed
                            </a>
                        @endif
						@if($item->replace_button_enable && !$item->replacementRequest)
							  <button type="button" 
									class="btn btn-outline-primary btn-sm select-md"
									onclick="if(confirm('Are you sure you want to create a replacement request?')) { window.location='{{ route('customer-point-repair.replacement.create', $item->id) }}' }">
								Replace
							</button>
						@endif
						@if($item->replacementRequest && $item->replacementRequest->status == 'pending')
							<button type="button" class="btn btn-outline-primary btn-sm select-md" data-bs-toggle="modal" data-bs-target="#replacementModal{{$item->id}}">
								Upload Report
							</button>
						@endif
						@php
						  $user = Auth::guard('web')->user();
						@endphp
				       {{-- Level 1 Approval --}}
						@if($item->replacementRequest && $item->replacementRequest->status == 'report_uploaded' && in_array($user->role_id,[4,1]))
							 <button type="button"
								class="btn btn-outline-success btn-sm"
								onclick="approveLevel1({{ $item->replacementRequest->id }})">
								Level 1 Approval
							</button>
						@endif

						{{-- Level 2 Approval --}}
						@if($item->replacementRequest && $item->replacementRequest->status == 'level_approval_1' && in_array($user->role_id,[6,1]))
							 <button type="button"
								class="btn btn-outline-success btn-sm"
								onclick="approveLevel2({{ $item->replacementRequest->id }})">
								Level 2 Approval
							</button>
						@endif

						{{-- Completed --}}
						@if($item->replacementRequest && $item->replacementRequest->status == 'completed')
							<span class="badge bg-success">Completed</span>
						@endif

                    </td>

					  


                    <!-- Approval Modal -->
                    <div class="modal fade" id="exampleModalApprove{{$item->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Admin Approval</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form method="POST" action="{{route('customer-point-repair.admin-approval')}}">
                            @csrf
                            <div class="modal-body">
                            <textarea class="form-control" readonly>{{$item->remarks}}</textarea>
                            <h4>Approval for ladger entry for this slected service-partner for closing customer point repaire service with out any spare parts.</h4>
                            <h4>Are you agree?</h4>
                            <h4><input type="radio" name="approval" value="2">No </h4>
                            <h4><input type="radio" name="approval" value="1">Yes </h4>
                            <input type="hidden" name="id" value="{{$item->id}}">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="sumbit" class="btn btn-primary">Save</button>
                            </div>
                            </div>
                            </form>
                        </div>
                    </div>
		
		             <!-- Replacement Modal -->
						<div class="modal fade" id="replacementModal{{$item->id}}" tabindex="-1" aria-labelledby="replacementModalLabel" aria-hidden="true">
						  <div class="modal-dialog">
							<div class="modal-content">
							
							  <form method="POST" action="{{route('customer-point-repair.upload-replacement-report')}}" enctype="multipart/form-data">

								@csrf

								<div class="modal-header">
									<h5 class="modal-title" id="replacementModalLabel">Upload Replacement Report</h5>
									<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
								</div>

								<div class="modal-body">

									<p>Please upload the replacement report.  
									<strong>You must upload within 2 days.</strong></p>

									<div class="mb-3">
										<label>Upload Report (PDF/Image)</label>
										<input type="file" name="report_file" class="form-control" required>
										@error('report_file')
										<p class="text-danger small">{{$message}}</p>
										@enderror
									</div>

									<input type="hidden" name="id" value="{{ $item->id }}">
								</div>

								<div class="modal-footer">
									 <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
									<button  type="submit"  class="btn btn-primary">Submit Report</button>
								</div>

							  </form>

							</div>
						  </div>
						</div>
                    <!-- cancell Modal -->
                    <div class="modal fade" id="exampleModalCancell{{$item->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Cancell Request</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form method="POST" action="{{route('customer-point-repair.cancell')}}">
                            @csrf
                            <div class="modal-body">
                            <textarea class="form-control" placeholder="Remarks for cancell" name="cancelled_reason"></textarea>
                            @error('cancelled_reason')
                                <span class="text-danger">
                                    {{ $message }}
                                </span>
                            @enderror
                            <input type="hidden" name="id" value="{{$item->id}}">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="sumbit" class="btn btn-primary">Save</button>
                            </div>
                            </div>
                            </form>
                        </div>
                    </div>
                    <!-- cancell Reson Modal -->
                    <div class="modal fade" id="exampleModalCancellReason{{$item->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Cancell Reason</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                            <textarea class="form-control" readonly>{{$item->cancelled_reason}}</textarea>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                            </div>
                        </div>
                    </div>
                     

                <!-- Modal -->
                        <div class="modal fade" id="exampleModal{{$item->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-body">
                                        <div class="card shadow-sm mb-0">
                                            <div class="card-header bg-light">
                                                Product issue:
                                            </div>
                                            <div class="card-body">
                                               <div>
                                                    {{$item->issue}}
                                               </div>
                                                <div class="text-end">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                            
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
                                    @if($item->status<3)
                                    <form method="post">
                                        @csrf
                                        <div class="modal-body">
                                        @if (isset($item->assign_service_perter_id))
                                            @if($item->servicePartner)
                                            <p>Current assign engg: <strong>{{$item->servicePartner->person_name?$item->servicePartner->person_name:""}}</strong></p>
                                            @endif
                                        @else
                                        <span class="badge bg-danger">Yet not assign</span>
                                        @endif
                                        <label>Choose Engg:</label>
                                        <select name="reassign_engg" class="form-control" id="reassign_engg{{$item->id}}">
                                            @if($servicePartners)
                                                <option value="" >--select--</option>
                                                @foreach( $servicePartners as $partner)
                                                        <option value="{{ $partner->id }}" {{$partner->id==$item->assign_service_perter_id?"selected":""}}>{{$partner->company_name}} - {{ $partner->person_name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            <button type="button" class="btn btn-primary" onclick="FormSubmit('{{$item->id}}')">Save</button>
                                        </div>
                                    <form>
                                    @else
                                        <div class="modal-body">
                                            <div class="badge bg-danger w-100" style="white-space: unset;">Sorry! You don't have permission to reassign engineer because invoice generated for existing service partner.
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                </tr>
                @if ($errors->has('cancelled_reason') && old('id') == $item->id)
                    <script type="text/javascript">
                        var modalCancell = new bootstrap.Modal(document.getElementById('exampleModalCancell{{$item->id}}'));
                        modalCancell.show();
                    </script>
                @endif
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
    function searchServicePartner(search){
        if(search.length > 0) {
            $.ajax({
                url: "{{ route('ajax.searchServicePartner') }}",
                method: 'post',
                data: {
                    '_token': '{{ csrf_token() }}',
                    search: search,
                    is_active: 'active'
                },
                success: function(result) {
                    console.log(result);
                    var content = '';
                    if (result.length > 0) {
                        content += `<div class="dropdown-menu show  servicepartner-dropdown select-md" aria-labelledby="dropdownMenuButton" style="width: 100%;">`;

                        $.each(result, (key, value) => {                            
                            content += `<a class="dropdown-item" href="javascript: void(0)" onclick="fetchServicePartner(${value.id},'${value.company_name}','${value.person_name}','${value.email}')">${value.person_name} |  ${value.company_name}</a>`;
                        })
                        content += `</div>`;
                        // $($this).parent().after(content);
                    } else {
                        content += `<div class="dropdown-menu show  servicepartner-dropdown select-md" aria-labelledby="dropdownMenuButton"><li class="dropdown-item">No service partner found</li></div>`;
                    }
                    $('#respDropServicePartner').html(content);
                }
            });
        } else {
            $('.servicepartner-dropdown').hide()
        }
    }

    function fetchServicePartner(i,c,p,e){
        $('.servicepartner-dropdown').hide();

        $('#service_partner_id').val(i);
        $('#service_partner_email').val(e);
        $('#service_partner_company_name').val(c);
        $('#service_partner_person_name').val(p);
        $('#service_partner_name').val(p+' | '+c);
        $('#searchForm').submit();


    }
    function GetCleaningWarranty(goods_id) {
        let warrantyValue = 0;
        $.ajax({
            url: '/get_cleaning_warranty_by_product', // Adjust this URL based on your route
            type: 'GET',
            data: { goods_id: goods_id },
            async: false, // Ensures the value is returned before moving on (not recommended for large-scale applications)
            success: function(response) {
                warrantyValue = response.warranty;
            },
            error: function() {
                console.log('Error retrieving warranty data');
            }
        });
        return warrantyValue;
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
                        if (item.warranty_type === "cleaning") {
                            let cleaningWarranty = GetCleaningWarranty(item.goods_id); // Get cleaning warranty from server
                            let numberOfCleaningsLeft = item.number_of_cleaning - cleaningWarranty; // Calculate remaining cleanings
                            
                            html += `<span class="badge bg-danger" style="cursor: pointer; font-size: 9px;" title="Number of cleaning">
                                        ${item.number_of_cleaning}
                                    </span> Left 
                                    <span class="badge bg-success" style="cursor: pointer; font-size: 9px;" title="Number of remaining cleaning">
                                        ${numberOfCleaningsLeft}
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
    function downloadImage(name){
        var url = "https://bwipjs-api.metafloor.com/?bcid=code128&includetext&text="+name;

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
        $.ajax({
            url: "{{ route('customer-point-repair.reassign-engineer') }}",
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
	
	function approveLevel1(id) {

		if (!confirm("Are you sure you want to approve Level 1?")) {
			return;
		}

		$.ajax({
			url: "{{ route('customer-point-repair.replacement.approveLevel1') }}",
			type: "POST",
			data: {
				id: id,
				_token: "{{ csrf_token() }}"
			},
			success: function (response) {
				alert("Level 1 Approved Successfully!");
				location.reload(); // refresh so button changes to Level 2
			},
			error: function (xhr) {
				alert("Error: " + xhr.responseText);
			}
		});
	}

	function approveLevel2(id) {

		if (!confirm("Are you sure you want to approve Level 1?")) {
			return;
		}

		$.ajax({
			url: "{{ route('customer-point-repair.replacement.approveLevel2') }}",
			type: "POST",
			data: {
				id: id,
				_token: "{{ csrf_token() }}"
			},
			success: function (response) {
				alert("Level 2 Approved Successfully!");
				location.reload(); // refresh so button changes to Level 2
			},
			error: function (xhr) {
				alert("Error: " + xhr.responseText);
			}
		});
	}
    
</script>
@endsection