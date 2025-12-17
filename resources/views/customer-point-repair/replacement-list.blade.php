@extends('layouts.app')
@section('content')
@section('page', 'Replacement Request')
<section>
   	 <div class="search__filter">
        <div class="row align-items-center justify-content-end">
            <div class="col-auto mb-2 mb-sm-0">
                <form action="" id="searchForm">
                <div class="row g-3 align-items-center">
                    <div class="col-auto">
                        <input type="search" autocomplete="off" name="search" value="" class="form-control select-md" placeholder="Search here..">
                    </div>
                   
                </div>
                </form>
            </div>
        </div>
    </div>
    <div class="filter">
        <div class="row align-items-center justify-content-between">
            <div class="col mb-2 mb-sm-0">
                @if (Session::has('message'))
                <div class="alert alert-success" role="alert">
                    {{ Session::get('message') }}
                </div>
                @endif
            </div>
            
           
        </div>
    </div>
	@php
	   $user = Auth::guard('web')->user();
	@endphp
    <table class="table">
        <thead>
            <tr>
                <th class="sr_no">#</th>
                <th class="primary_column">Id</th>
                <th>Report</th>
                <th>Approval 1(Biswajit)</th>    
                <th>Approval 1 At</th>            
                <th>Approval 2(Ho Sale)</th>
                <th>Approval 2 At</th>
				<th>Status</th>
				@if(in_array($user->role_id,[1,4]))
				 <th>Action</th>
				@endif
            </tr>
        </thead>
        <tbody>
			@forelse($data as $key => $row)
				<tr>
					<td>{{ $key + 1 }}</td>
					<td>{{ $row->crp_data ? $row->crp_data->unique_id : "" }}</td>

					<td>
						@if($row->report_file)
							<a href="{{ asset($row->report_file) }}" target="_blank">
								View Report
							</a>
						@else
							—
						@endif
					</td>

					<td>{{ optional($row->approval_1)->name }}</td>
					<td>{{ $row->approval1_at ?? '-' }}</td>

					<td>{{ optional($row->approval_2)->name }}</td>
					<td>{{ $row->approval2_at ?? '-' }}</td>

					<td>
						<span class="mb-2 badge 
							{{ $row->status == 'completed' ? 'bg-success' : 'bg-warning' }}">
							{{ ucfirst($row->status) }}
						</span>

					</td>
					
					<td>
					    @if($row->status == 'completed' && in_array($user->role_id,[1,4]) )
								<button type="button" class="btn btn-sm btn-outline-primary" onclick="generateChallan({{$row->id}})">Generate Challan & Auto Process</button>
						@elseif($row->status != 'closed')
						    <button type="button" class="btn btn-sm btn-outline-primary" onclick="openOtpModal({{ $row->id }})">Close Call</button>
						@endif
					</td>
				</tr>
			<!-- Verify OTP Modal -->
		<div class="modal fade" id="verifyOtpModal" tabindex="-1" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Verify OTP</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
					</div>

					<div class="modal-body">
						<input type="hidden" name="replacement_id" id="replacement_id" value="{{$row->id}}">

						<div class="mb-3">
							<label class="form-label">Enter OTP</label>
							<input 
								type="text" 
								class="form-control" 
								name="otp"
								id="otp" 
								placeholder="Enter OTP">
							@error('otp')
							   <p class="small text-danger"> {{$message}}</p>
							@enderror
						</div>
					</div>

					<div class="modal-footer">
						<button class="btn btn-secondary" data-bs-dismiss="modal">
							Cancel
						</button>
						<button type="submit" class="btn btn-primary" onclick="verifyOtp()">
							Verify & Close
						</button>
					</div>
				</div>
			</div>
		</div>
			@empty
				<tr>
					<td colspan="9" class="text-center">No records found</td>
				</tr>
			@endforelse
     </tbody>

    </table>
  	
	

    
</section>
<script>
	
function generateChallan(id){
    if(!confirm('Are you sure you want to generate challan, transfer warranty, dispatch & generate OTP?')){
        return;
    }

    $.ajax({
        url: "{{ route('customer-point-repair.replacement.generate-challan', ':id') }}".replace(':id', id),
        type: "POST",
        data: {
            _token: "{{ csrf_token() }}"
        },
        beforeSend: function(){
            console.log('Processing...');
        },
        success: function(response){
            alert('Success: ' + response.message);
            location.reload(); // reload page to update status
        },
        error: function(xhr, status, error){
            let err = xhr.responseJSON ? xhr.responseJSON.message : 'Something went wrong';
            alert('Error: ' + err);
        }
    });
}
	
	function openOtpModal(id){
	    $('#replacement_id').val(id);
		$('#otp').val('');
		$('#verifyOtpModal').modal('show');
	}
	
	function verifyOtp(){
    let id = $('#replacement_id').val();
    let otp = $('#otp').val();
	console.log(id,otp);
    if(otp === ''){
        alert('Please enter OTP');
        return;
    }

    $.ajax({
        url: "{{ route('customer-point-repair.replacement.verify-otp') }}",
        type: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            id: id,
            otp: otp
        },
        success: function(response){
            alert(response.message);
            $('#verifyOtpModal').modal('hide');
            location.reload();
        },
        error: function(xhr){
            alert(xhr.responseJSON?.message ?? 'Invalid OTP');
        }
    }); 
}
	
	

</script>

@endsection 