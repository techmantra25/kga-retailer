@extends('layouts.app')
@section('content')
@section('page', 'HO-SALE')
{{-- @section('small', '(AMC Plan List In Days With Products)') --}}

<section>
    <div class="col mb-2 mb-sm-0">
        @if (Session::has('message'))
        <div class="alert alert-success" role="alert">
            <strong>{{ Session::get('message') }}</strong>
        </div>
        @endif
        @if (Session::has('error'))
        <div class="alert alert-danger" role="alert">
            {{ Session::get('error') }}
        </div>
        @endif
    </div>
   
    <form action="" id="searchForm">
        <div class="search__filter">
            <div class="row align-items-center justify-content-between d-flex">
               <!-- <div class="col">
                </div>  -->
                <div class="col-2" >
                    <!-- <input type="number" value="{{$remaining_days}}" class="form-control select-md" name="remaining_days" placeholder="Search for remaining days" /> -->
                     <select class="form-control select-md" id="remaining_days" name="remaining_days">
                        <option selected hidden>--Choose Remaining Days--</option>
                       <!-- <option value="1" @if(request('remaining_days') == '1') selected @endif>Tomorrow</option>
                        <option value="2" @if(request('remaining_days') == '2') selected @endif>2 Days</option>
                        <option value="3" @if(request('remaining_days') == '3') selected @endif>3 Days</option>
                        <option value="4" @if(request('remaining_days') == '4') selected @endif>4 Days</option>
                        <option value="5" @if(request('remaining_days') == '5') selected @endif>5 Days</option>
                        <option value="6" @if(request('remaining_days') == '6') selected @endif>6 Days</option>  -->
						 <option value="hot_leads" 
							@if(request('remaining_days', 'hot_leads') == 'hot_leads') selected @endif>
							Hot Leads
						</option>
                        <option value="7" @if(request('remaining_days') == '7') selected @endif>7 Days</option>
                        <option value="15" @if(request('remaining_days') == '15') selected @endif>15 Days</option>
                        <option value="30" @if(request('remaining_days') == '30') selected @endif>30 Days</option>
						 <option value="60" @if(request('remaining_days') == '60') selected @endif>60 Days</option>
						  <option value="above_60" @if(request('remaining_days') == 'above_60') selected @endif>60 Days Above</option>
						<option value="-7" @if(request('remaining_days') == '-7') selected @endif>-7 Days</option>
						<option value="-15" @if(request('remaining_days') == '-15') selected @endif>-15 Days</option>
						<option value="-30" @if(request('remaining_days') == '-30') selected @endif>-30 Days</option>
						<option value="-60" @if(request('remaining_days') == '-60') selected @endif>-60 Days</option>
						  <option value="below_60" @if(request('remaining_days') == 'below_60') selected @endif>60 Days Below</option>
                     </select>
                </div>
				 <div class="col-2" id="calls_filter">
                    <select class="form-control select-md"  name="calls_filter">
                        <option selected hidden>--Choose Calls Filter--</option>
                        <option value="old_pending" @if(request('calls_filter') == 'old_pending') selected @endif>Old Pending</option>
                        <option value="today_due" @if(request('calls_filter') == 'today_due') selected @endif>Today Due</option>
						<option value="call_back" @if(request('calls_filter') == 'call_back') selected @endif>Call Back</option>
                    </select>
                </div>
                <div class="col-2" id="amc_subscription">
                    <select class="form-control select-md" name="amc_subscription">
                        <option value="unsubscription" @if(request('amc_subscription') == 'unsubscription') selected @endif>Not Subscribed</option>
                        <option value="subscription" @if(request('amc_subscription') == 'subscription') selected @endif>Subscribed</option>
                        <option value="pending_request" @if(request('amc_subscription') == 'pending_request') selected @endif>Discount Approval Pending</option>
						<option value="pending_payment" @if(request('amc_subscription') == 'pending_payment') selected @endif>Pending Payment</option>
						<option value="refused" @if(request('amc_subscription') == 'refused') selected @endif>Refused</option>
                    </select>
                </div>
              
 				<!-- Replace the existing date input -->
				<div class="col-2" id="search_date">
					<input type="date" value="{{ $from_date }}" class="form-control select-md" name="from_date" placeholder="From date">
					<input type="date" value="{{ $to_date }}" class="form-control select-md mt-1" name="to_date" placeholder="To date">
				</div>
                <div class="col-3">
                    <input type="search" name="search" value="{{$search}}" class="form-control select-md" placeholder="Search by product,serial,barcode,mobile,phone,branch etc..">
                    
                </div>
                <div class="col-auto">
                    <a href="{{ route('amc.ho-sale') }}" class="btn btn-outline-warning select-md">Reset Page</a>   
                </div>
                
            </div>
        </div>
        <div class="filter">
        <div class="row align-items-center justify-content-between">
            <div class="col">
                <span class="small filter-waiting-text" id=""></span>                
            </div>
            <div class="col-auto">
                Number of rows:
            </div><div class="col-auto p-0">
                <select name="paginate" id="paginate" class="form-control select-md" id="">
                    <option value="25" @if($paginate == 25) selected @endif>25</option>
                    <option value="50" @if($paginate == 50) selected @endif>50</option>
                    <option value="100" @if($paginate == 100) selected @endif>100</option>
                    <option value="200" @if($paginate == 200) selected @endif>200</option>
                </select>
            </div>
            <div class="col-auto">
                <p>Total {{$totalResult}} Records</p>
            </div>
        </div>
    </div>
    </form>

    <table class="table">
        <thead>
            <tr>
                <th class="sr_no">#</th>
                <th class="sr_no">KGA-Sales-Id</th>
                <th class="primary_column">Bill Date</th>
                <th>Customer Details</th>
				<th>Warranty Details</th>
                <th>Item Details</th>
                @if(request('amc_subscription') == 'unsubscription')
                <th>Action</th>
                @else
                <th>Subscription Details</th>
                @endif

            </tr>
        </thead>
        <tbody>
        @php
            // echo $request->page; die;
            // $page = Request::get('page')?Request::get('page'):1;   
                  
            if(empty($page) || $page == 1){                
                $i=1;
            } else {
                $i = ((($page-1)*$paginate)+1);
            } 
        @endphp
        @forelse ($data as $item)

            @php
                $warrantyCount = App\Models\ProductWarranty::where('dealer_type', 'khosla')->where('goods_id', $item->product_id)->count(); 
                $amcCount = App\Models\ProductAmc::where('product_id', $item->product_id)->count(); 
                $last_call_history_data = App\Models\AmcCallHistory::where('kga_sale_id', $item->id)->orderBy('id','DESC')->first();
                $reminder_days = null; 
                if ($last_call_history_data && $last_call_history_data->reminder_date) {
                    $reminder_date = \Carbon\Carbon::parse($last_call_history_data->reminder_date);
                    $current_date = \Carbon\Carbon::now()->format('Y/m/d');
                    $reminder_days = $reminder_date->diffInDays($current_date, false);
                }

                $discount_request =  App\Models\BeforeAmcSubscription::where('kga_sales_id', $item->id)->orderby('id','DESC')->get();
            
            @endphp
            <tr>
                
                <td class="sr_no">{{$i}}</td>
                <td class="sr_no">{{$item->id}}</br>
                @if($last_call_history_data)
                    <a href="{{ route('amc.call-history-track' , Crypt::encrypt($item->id)) }}" class="btn btn-outline-primary select-md">Call History</a> 
                @endif
                </td>
                <td>
                    {{ date('j M Y, l', strtotime($item->bill_date)) }}</br>
                    <span>Bill No: <strong>{{ $item->bill_no }}</strong></span></br>
                    <span>Branch: <strong>{{ $item->branch }}</strong></span></br>
                    @if($last_call_history_data && $last_call_history_data->type !== 2)
                    <span class="bg-danger">Call date: <strong>{{ date('j M Y, l', strtotime($last_call_history_data->created_at))}}</strong></span></br>
                    <span class="bg-danger">Call back date: <strong>{{ date('j M Y, l', strtotime($last_call_history_data->reminder_date))}}</strong></span></br>
                        @if($reminder_days <= 0)
                        <span class="bg-danger">Reminder in: <strong>{{ abs($reminder_days) }} Days</strong></span></br>
                        @endif
                    @endif
                    
                </td>
                <td>
                    <p class="small text-muted mb-1">
                        <span>Name: <strong>{{ $item->customer_name }}</strong></span> <br/>
						 <span>Email: <strong>{{ $item->email }}</strong></span> <br/>
                        <span>Mobile: <strong>{{ $item->mobile }}</strong></span> <br/>
                        <span>Phone: <strong>{{ $item->phone }}</strong></span> <br/>
                        <span>Address: <strong>{{ $item->address }}</strong></span> <br/>
                        <span>Pin: <strong>{{ $item->pincode }}</strong></span> <br/>
                    </p>
                </td>
                <td>
						@php
							// Get warranties for this product
							$warranties = App\Models\ProductWarranty::where('dealer_type', 'khosla')
								->where('goods_id', $item->product_id)
					            ->where('warranty_type','comprehensive')
								->get();

							$currentDate = \Carbon\Carbon::now();
						@endphp

						@forelse($warranties as $warranty)
							@php
								// Calculate dates
								$billDate = \Carbon\Carbon::parse($item->bill_date)->startOfDay();
								$currentDate = \Carbon\Carbon::now()->startOfDay();
								$warrantyEndDate = $billDate->copy()->addMonths($warranty->warranty_period)->subDay();

								$daysRemaining = $currentDate->diffInDays($warrantyEndDate, false);

								$status = $daysRemaining >= 0 ? 'Active' : 'Expired';
							@endphp

							<div class="warranty-info mb-2 p-2">
								<p class="small text-muted mb-1">
									<span>Type: <strong class="text-primary">{{ ucfirst($warranty->warranty_type) }}</strong></span><br>
									<span>Period: <strong>{{ $warranty->warranty_period }} months</strong></span><br>
									<span>End Date: <strong>{{ $warrantyEndDate->format('j M Y') }}</strong></span><br>
									<span>Remaining: 
										<strong class="text-{{ $status == 'Active' ? 'success' : 'danger' }}">
											{{ abs($daysRemaining) }} days
										</strong>
									</span><br>
									<span>Status: 
										<span class="badge bg-{{ $status == 'Active' ? 'success' : 'danger' }}">
											{{ $status }}
										</span>
									</span>

									@if($warranty->warranty_type === 'additional')
										<br><span class="text-muted small">
											@if($warranty->additional_warranty_type == 1)
												(Parts Chargeable)
											@else
												(Service Chargeable)
											@endif
										</span>
									@endif

									@if($warranty->parts)
										<br><span class="text-muted small">
											Parts: {{ $warranty->parts }}
										</span>
									@endif
								</p>
							</div>
						@empty
							<span class="text-muted small">No warranty registered</span>
						@endforelse
					</td>

                <td>
                    
                    <p class="small text-muted mb-1">
                        <span>Serial: <strong>{{ $item->serial }}</strong></span> <br/>
                        <span>Item: <strong>{{ $item->item }}</strong></span> <br/>
						<span>Class: <strong>{{ optional($item->product->category)->name }}</strong></span> <br/>
                        <span>Barcode: <button class="showdetails" title="Download Barcode" onclick="downloadImage('{{$item->barcode}}')">{{ $item->barcode }}</button></span> <br/>
                    </p>
                </td>
                <td>
              @if(request('amc_subscription') == 'unsubscription' || request('amc_subscription') == 'refused' || !request('amc_subscription'))
                <button type="button" class="btn btn-outline-{{ $warrantyCount > 0 ? 'primary' : 'danger' }} select-md" data-bs-toggle="modal" data-bs-target="#Warranty_status_data" onclick="getWarrantyData({{$item->product_id}}, 'khosla', '{{$item->bill_date}}', this)">View warranty</button>
                <a href="{{ route('amc.amc-by-product',[$item->id, Crypt::encrypt($item->product_id)]) }}" class="btn btn-outline-{{ $amcCount > 0 ? 'primary' : 'danger' }} select-md">AMC Plans</a>
                <button type="button" class="btn btn-outline-primary select-md" data-bs-toggle="modal" data-bs-target="#call_back_date{{$item->id}}" > Call Back Date </button>
                @if($last_call_history_data && $last_call_history_data->type === 2)
                <button type="button" class="btn btn-danger select-md" > Refused </button>
                @else
                <button type="button" class="btn btn-outline-primary select-md" data-bs-toggle="modal" data-bs-target="#refuse{{$item->id}}" > Refuse? </button>
                @endif


                @else
            
                   <!--subscription data  -->
                    @php
                        $amcSubscription = App\Models\AmcSubscription::where('serial',$item->serial)->orderBy('id','DESC')->first(); 
                        $amc_data = $amcSubscription ? App\Models\ProductAmc::where('id',$amcSubscription->amc_id)->first() : null;
                        $plan_data = $amc_data ? App\Models\AmcPlanType::find($amc_data->plan_id) : null;
                    @endphp
                    <p class="small text-muted mb-1">
                        <span>Purchase Date: <strong>{{ $amcSubscription ->purchase_date }}</strong></span> <br/>
                        <span>Actual amount: <strong>{{ $amcSubscription->actual_amount }}</strong></span> <br/>
                        <span>Discount: <strong>{{ $amcSubscription->discount }}</strong></span> <br/>
                        <span>Purchase amount: {{ $amcSubscription->purchase_amount }}<br/>
                        <span>Plan name: {{ $plan_data->name ?? "N/A" }}<br/>
                        <span>Plan duration: {{ $amc_data->duration ?? "N/A"}}<br/>
                    </p>
                @endif   
                </td>

            </tr>

            <div class="modal fade" id="call_back_date{{$item->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Reminder Form</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="post" action="{{ route('amc.call-back-date') }}">
                        @csrf
                    <div class="modal-body">
                        <input type="number" name="reminder_days" class="form-control" placeholder="Reminder in days" required></br>
                        <textarea class="form-control" name="remark" placeholder="Remarks here.."></textarea>
                        <input type="hidden" name="kga_sale_id" value="{{$item->id}}">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </form>
                    </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="refuse{{$item->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Reminder Form</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="post" action="{{ route('amc.call-refuse') }}">
                        @csrf
                    <div class="modal-body">
                        <textarea class="form-control" name="remark" placeholder="Remarks here.."></textarea>
                        <input type="hidden" name="kga_sale_id" value="{{$item->id}}">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </form>
                    </div>
                    </div>
                </div>
            </div>
            @php
                $i++;
            @endphp
        @empty
            <tr>
                <td colspan="12" style="text-align: center;">
                    No data found
                </td>
            </tr>
        @endforelse
            
        </tbody>
    </table>
    {{$data->links()}}


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
    var navigator_useragent = '';
    function getBrowserType() {
        const test = regexp => {
            return regexp.test(navigator.userAgent);
        };
        console.log(navigator.userAgent);
        navigator_useragent = navigator.userAgent;
                
        $('#navigator_useragent').val(navigator_useragent);
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
        $('.browser_name').val(browserType);
        $('.navigator_useragent').val(navigator_useragent);
        

    })


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


    function getWarrantyData(product_id, dealer_type, bill_date, element){  
        var currentDate = new Date();
        var entry_date = currentDate.toISOString().split('T')[0]; // Format to 'YYYY-MM-DD'
		  //var remaining_days = $('#remaining_days').val() || '';
        $.ajax({
            url: "{{ route('ajax.get-product-warranty-status') }}",
            method: 'post',
            data: {
                '_token': '{{ csrf_token() }}',
                id:product_id,
                order_date:bill_date,
                dealer_type: dealer_type,
                to_date: entry_date,
				//remaining_days: remaining_days
            },
            success: function(result) {
                // console.log(result.data);
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
                                            <th>Remaining Days</th>
                                            <th>Warranty Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>`;
                    if(result.status === true && result.data.length > 0) {

                        $.each(result.data, function(key, item) {
                            console.log(item.days_remaining)
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
                                    <td> <span class="badge bg-${item.warranty_status==="YES"?"success":"danger"}">${item.days_remaining}</span></td>
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



    $('#amc_subscription').on('change', function(){
        $('#searchForm').submit();
    })
    $('#plan_type').on('change', function(){
        $('#searchForm').submit();
    })
   $('#duration_type').on('change', function(){
        $('#searchForm').submit();
    })
   $('#search_date').on('change', function(){
        $('#searchForm').submit();
    })
  
    $('#paginate').on('change', function(){
        $('#searchForm').submit();
    })
	$('#calls_filter').on('change',function(){
		$('#searchForm').submit();
	})
	
	$(document).ready(function () {
		$("#remaining_days").change(function(){
			var selectedDays = $(this).val();
			window.location.href = "{{ route('amc.ho-sale') }}?remaining_days=" + selectedDays;
		});
	});


</script>  
@endsection 