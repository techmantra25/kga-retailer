@extends('layouts.app')
@section('content')
@section('page', 'Ledger')
<section>
    <ul class="breadcrumb_menu">
        <li>Report</li>
        <li>User Ledger</li>
    </ul>
    <div class="search__filter">
        <form action="" id="searchForm">
        <div class="row align-items-center justify-content-between">
            <div class="col">
               
            </div>
            <div class="col-auto">
				<label for="filter_type">Type</label>
				<select name="filter_type" class="form-control" id="filter_type">
					<option value="all" {{ ($filter_type ?? '') == 'all' ? 'selected' : '' }}>All</option>
					<option value="amc" {{ ($filter_type ?? '') == 'amc' ? 'selected' : '' }}>AMC Incentive</option> 
				</select>
			</div>
			@php
			  $role_id = auth()->user()->role_id;
			@endphp
            <div class="col-auto">
                <label for="">User Type</label>
                <select name="user_type" class="form-control" id="user_type">
                   @if($role_id == 1)
					<!--Super admin can see all user types-->
                     <option value="servicepartner" {{ $user_type == 'servicepartner' ? 'selected' : '' }}>Service Partner</option>
					 <option value="service_centre" {{ $user_type == 'service_centre' ? 'selected' : '' }}>Service Centre</option>
					 <option value="ho_sale" {{ $user_type == 'ho_sale' ? 'selected' : '' }}>Ho Sale</option>
					 <option value="admin" {{ $user_type == 'admin' ? 'selected' : '' }}>Admin</option>
					@elseif($role_id == 4)
					<!-- Service Centre sees only Service Partner and self -->
					 <option value="servicepartner" {{ $user_type == 'servicepartner' ? 'selected' : '' }}>Service Partner</option>
					 <option value="service_centre" {{ $user_type == 'service_centre' ? 'selected' : '' }}>Service Centre</option>
					@elseif($role_id == 6)
						<!-- Ho Sale sees only Service Partner and self -->
					 {{-- <option value="servicepartner" {{ $user_type == 'servicepartner' ? 'selected' : '' }}>Service Partner</option> --}}
					  <option value="ho_sale" {{ $user_type == 'ho_sale' ? 'selected' : '' }}>Ho Sale</option>
					
					@endif
                </select>        
            </div>
            <div class="col-auto">
			   <div id="dropdown_servicepartner" class="d-none">
					<label for="service_partner_id">Service Partner</label>
					<select name="service_partner_id" class="form-control" id="service_partner_id">
						<option value="" hidden selected>Select Service Partner</option>
						@foreach ($sp as $p)
						<option value="{{$p->id}}" @if($p->id == $service_partner_id) selected @endif>{{$p->person_name}} - {{$p->company_name}}</option>                        
						@endforeach
					</select>    
				</div>
				
				<div id="dropdown_servicecentre" class="d-none">
					<label for="service_centre_id">Service Centre</label>
					 {{-- Service Centre Dropdown --}}
					<select name="service_centre_id" class="form-control" id="service_centre_id">
						<option value="" hidden selected>Select Service Centre</option>
						@foreach ($service_centres as $sc)
							<option value="{{ $sc->id }}" @if($sc->id == $service_centre_id) selected @endif>
								{{ $sc->name }}
							</option>
						@endforeach
					</select>
				</div>
				
				 {{-- Ho Sale Dropdown --}}
				<div id="dropdown_ho_sale" class="d-none">
					<label for="ho_sale_id">Ho Sale</label>
				<select name="ho_sale_id" class="form-control" id="ho_sale_id">
					<option value="" hidden selected>Select Ho Sale</option>
					@foreach ($ho_sales as $ho)
						<option value="{{ $ho->id }}" @if($ho->id == $ho_sale_id) selected @endif>
							{{ $ho->name }}
						</option>
					@endforeach
				</select>
				</div>
				
				{{-- Admin Dropdown --}}
				<div id="dropdown_admin" class="d-none">
						<label for="admin_id">Ho Sale</label>
					<select name="admin_id" class="form-control" id="admin_id">
						<option value="" hidden selected>Select Admin</option>
						@foreach ($admins as $admin)
							<option value="{{ $admin->id }}" @if($admin->id == $admin_id) selected @endif>
								{{ $admin->name }}
							</option>
						@endforeach
					</select>
				</div>
			</div>
            <div class="col-auto">                
                <div class="row g-3 align-items-center">
                    <div class="col-auto">
                        <label for="">From</label>
                        <input type="date" name="from_date" class="form-control" id="from_date" value="{{ $from_date }}" >
                    </div>
                    <div class="col-auto">
                        <label for="">To</label>
                        <input type="date" name="to_date" value="{{ $to_date }}" class="form-control" id="to_date" max="{{ date('Y-m-d') }}" >
                    </div>
                    <div class="col-auto align-self-end">
                        <input type="submit" value="Search" class="btn btn-success">
                        <a href="{{ route('report.ledger-user') }}" class="btn btn-warning">Reset</a>
                        <a href="javascript:void(0)" onclick="downloadLedger('csv');" class="btn btn-outline-success">CSV</a>
                    </div>
                    
                </div>
                
            </div>
        </div>
			</div>  
        </form>
          
    <table class="table" id="installationTable">
        <thead>
            <tr>
                <th>Date</th>
                <th>Transaction ID</th>
                <th>Purpose</th>
                <th>Debit</th>
                <th>Credit</th>
                <th>Closing</th>
            </tr>
        </thead>
        <tbody>       
        @php
			// Determine if a user is selected based on user_type
			$userSelected = false;
			if (isset($user_type)) {
				if ($user_type == 'servicepartner' && !empty($service_partner_id)) {
					$userSelected = true;
				} elseif ($user_type == 'service_centre' && !empty($service_centre_id)) {
					$userSelected = true;
				} elseif ($user_type == 'ho_sale' && !empty($ho_sale_id)) {
					$userSelected = true;
				} elseif ($user_type == 'admin' && !empty($admin_id)) {
					$userSelected = true;
				}
			}
			
            $i=1;
            $net_value = $cred_value = $deb_value = 0;
            $cred_ob_amount = $deb_ob_amount = $zero_ob_amount = $cr_ob_amount = $dr_ob_amount = $zero_ob_amount = "";
            $net_value += $ob_amount;

            $ob_amount_cr_dr = getCrDr($ob_amount);
            if($ob_amount_cr_dr == 'Cr'){
                $cr_ob_amount = $ob_amount;
            } else if ($ob_amount_cr_dr == 'Dr'){
                $dr_ob_amount = $ob_amount;
            } else if ($ob_amount_cr_dr == ''){
                $zero_ob_amount = '';
            }
            
        @endphp 
        @if ($userSelected && !empty($data) && !empty($is_transaction))
        <tr>
            <td>
                {{ date('d/m/Y', strtotime($from_date)) }}
            </td>
            <td>
                
            </td>
            <td>
                Opening Balance
            </td>
            <td>
                <span class="text-danger">{{ $dr_ob_amount }}</span>
            </td>
            <td>
                <span class="text-success">{{ $cr_ob_amount }}</span>
            </td>
            <td>
                {{ replaceMinusSign($ob_amount) }} 
                        
                {{ getCrDr($ob_amount) }}
            </td>
        </tr>
        @endif
        
        @forelse ($data as $item)
        @php
            $debit_amount = $credit_amount = '';
            if(($item->type == 'credit')){
                $credit_amount = $item->amount;
                $net_value += $item->amount;
                $cred_value += $item->amount;
            }
            if(($item->type == 'debit')){
                $debit_amount = $item->amount;
                $net_value -= $item->amount;
                $deb_value += $item->amount;
            }
            $search_url = "";
            if($item->purpose == 'installation'){
                $search_url = route('installation.list');
            } else if ($item->purpose == 'repair' || $item->purpose == 'repair_charges'){
                $search_url = route('repair.list');
            } else if ($item->purpose == 'invoice'){
                $search_url = route('invoice.list');
            } else if ($item->purpose == 'payment') {
                $search_url = route('accounting.payment-list');
            } else if ($item->purpose == 'maintenance') {
                $service_type = getSingleAttributeTable('maintenances','unique_id',$item->transaction_id,'service_type');
                $search_url = route('maintenance.list',$service_type);
            } else if ($item->purpose == 'spare_return') {
                $search_url = route('spare-return.list');
            } else if ($item->purpose == 'dap-repair') {
                $search_url = route('dap-services.list');
            } else if ($item->purpose == 'customer-point-repair') {
                $search_url = route('customer-point-repair.list');
            }
        @endphp
            <tr>
                <td>
                    {{ date('d/m/Y', strtotime($item->entry_date)) }}
                </td>
                <td>
                    
                    <a href="{{ $search_url }}?search={{$item->transaction_id}}&service_partner_id={{$service_partner_id}}" class="btn btn-outline-dark select-md">{{ $item->transaction_id }}</a>
                        
                </td>
                <td>
                    {{ ucwords(str_replace("_"," ",$item->purpose)) }}
                </td>
                <td>
                    <span class="text-danger">{{ $debit_amount }}</span>
                </td>
                <td>
                    <span class="text-success">{{ $credit_amount }}</span>
                </td>
                <td>
                    {{ replaceMinusSign($net_value) }} 
                            
                    {{ getCrDr($net_value) }}
                </td>
            </tr>
            
            @php
                $i++;
            @endphp
        @empty
            @if (empty($is_transaction))
                <tr>
                    <td colspan="7" style="text-align: center;">
                        No data found
                    </td>
                </tr>
            @endif
        
        @endforelse
        @if ((!empty($user_type)) && !empty($is_transaction))
        <tr class="table-info">
            <td><strong>Closing Amount</strong>  </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>                            
                <strong>                                                               
                    {{ replaceMinusSign($net_value) }} {{ getCrDr($net_value)}}
                </strong>
            </td>
        </tr> 
        @endif   
        </tbody>
    </table>
    
    
    
</section>
<style>
    .table tbody tr.table-info td {
        background: #cff4fc !important;
    }
</style>
<script>
    $(document).ready(function(){
        $('div.alert').delay(3000).slideUp(300);
        var from_date = $('#from_date').val();
        var to_date = $('#to_date').val();
        $('#from_date').attr('max' ,  to_date);
        $('#to_date').attr('min' ,  from_date);
        
    })

    $('input[type=date]').on('change', function(){
        var from_date = $('#from_date').val();
        var to_date = $('#to_date').val();
        $('#from_date').attr('max' ,  to_date);
        $('#to_date').attr('min' ,  from_date);
    })
    $('input[type=search]').on('search', function () {
        // search logic here
        // this function will be executed on click of X (clear button)
        $('#searchForm').submit();
    });

   /* function downloadLedger(e){        
        var from_date = $('#from_date').val();
        var to_date = $('#to_date').val();
        var user_type = $('#user_type').val();
        var service_partner_id = $('#service_partner_id').val();
        var supplier_id = $('#supplier_id').val();
        
        if(user_type == '' || service_partner_id == ''){
            alert("Please get the record first");
            return true;
        }

        var dataString = "from_date="+from_date+"&to_date="+to_date+"&user_type="+user_type+"&service_partner_id="+service_partner_id;
        
        if(e == 'csv'){
            window.location.href = "{{ route('report.ledger-user-csv') }}?"+dataString; 
        }
        
    } */
	
	function downloadLedger(e) {
    const from_date = $('#from_date').val();
    const to_date = $('#to_date').val();
    const user_type = $('#user_type').val();
    const service_partner_id = $('#service_partner_id').val();
    const service_centre_id = $('#service_centre_id').val();
    const ho_sale_id = $('#ho_sale_id').val();
    const admin_id = $('#admin_id').val();

    // Determine which ID is required based on user_type
    let selected_id = '';
    if (user_type === 'servicepartner') {
        selected_id = service_partner_id;
    } else if (user_type === 'service_centre') {
        selected_id = service_centre_id;
    } else if (user_type === 'ho_sale') {
        selected_id = ho_sale_id;
    } else if (user_type === 'admin') {
        selected_id = admin_id;
    }

    if (!user_type || !selected_id) {
        alert("Please select user type and corresponding user before downloading CSV");
        return false;
    }

    const params = new URLSearchParams({
        from_date,
        to_date,
        user_type,
        service_partner_id,
        service_centre_id,
        ho_sale_id,
        admin_id
    }).toString();

    if (e === 'csv') {
        window.location.href = "{{ route('report.ledger-user-csv') }}?" + params;
    }
}

	
	//based on select the user type change the dropdown 
		$(document).ready(function () {
		function toggleDropdowns() {
			const selectedType = $('#user_type').val();

			// Hide all dropdown containers
			$('#dropdown_servicepartner, #dropdown_servicecentre, #dropdown_ho_sale, #dropdown_admin').addClass('d-none');

			// Show only the selected one
			if (selectedType === 'servicepartner') {
				$('#dropdown_servicepartner').removeClass('d-none');
			} else if (selectedType === 'service_centre') {
				$('#dropdown_servicecentre').removeClass('d-none');
			} else if (selectedType === 'ho_sale') {
				$('#dropdown_ho_sale').removeClass('d-none');
			} else if (selectedType === 'admin') {
				$('#dropdown_admin').removeClass('d-none');
			}
		}

		$('#user_type').on('change', toggleDropdowns);

		// Run on page load to reflect current selection
		toggleDropdowns();
	});



    
</script>  
@endsection 