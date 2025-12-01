@extends('layouts.app')
@section('content')
@section('page', 'AMC Plan Type & Their Duration')
<style>
  .error{
	  color:red;
	  font-size: 0.85em;
      margin-top: 4px;
	}
</style>
<section>
    <ul class="breadcrumb_menu d-flex justify-content-between">        
        <li>Add Product for Different AMC Package</li>
        <div>
        <button class="btn btn-outline-primary select-md" data-bs-toggle="modal" data-bs-target="#add" title="Add">Add Days</button>
        <a href="{{ route('amc.plan-type') }}" class="btn btn-outline-danger select-md" title="Back">Back</a>
        </div>
    </ul>
    
     <!-- add Modal-->
    <div class="modal fade" id="add" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('amc.plan-duration-create') }}" method="post" id="add_duration">
                @csrf
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add AMC Plan With Days</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Plan Name : <strong>{{ $data->name}}({{implode(',',$data->plan_asset_names)}})</strong>
                    <input type="hidden" name="plan_name" value="{{$data->id}}" />
                    <input type="number" class="form-control" name="plan_dureation" placeholder="Enter plan duration in days" />
					<div class="error" id="plan_duration_error"></div>
					@php
					  $planAssetIds = explode(',', $data->plan_asset_id);
					@endphp
					@if(in_array(1,$planAssetIds))
					<label>Cleaning Type:</label>
					
					<!-- Deep Cleaning Checkbox -->
					<div class="form-check mb-2 mt-2">
						<input class="form-check-input" type="checkbox" id="deep_cleaning_{{ $data->id }}" name="deep_cleaning_checkbox">
						<label class="form-check-label" for="deep_cleaning_{{ $data->id }}" style="cursor:pointer;">Deep Cleaning</label>
					</div>
					<div id="deep_cleaning_input_{{ $data->id }}" class="mb-3" style="display: none;">
						<input type="number" class="form-control" name="deep_cleaning_days" placeholder="Enter deep cleaning">
						<div class="error" id="deep_cleaning_error"></div>
					</div>

					<!-- Normal Cleaning Checkbox -->
					<div class="form-check mb-2">
						<input class="form-check-input" type="checkbox" id="normal_cleaning_{{ $data->id }}" name="normal_cleaning_checkbox">
						<label class="form-check-label" for="normal_cleaning_{{ $data->id }}" style="cursor:pointer;">Normal Cleaning</label>
					</div>
					<div id="normal_cleaning_input_{{ $data->id }}" class="mb-3" style="display: none;">
						<input type="number" class="form-control" name="normal_cleaning_days" placeholder="Enter normal cleaning">
						<div class="error" id="normal_cleaning_error"></div>
					</div>
					@endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" id="duration_update" class="btn btn-primary">Update</button>
                </div>
			</div>
            </form>
            
        </div>
    </div>
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
    <div class="mb-3">
        <span class="badge rounded-pill bg-secondary"><strong> {{ $data->name }}({{implode(', ', $data->plan_asset_names)}}):</strong></span>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th>Duration (Days)</th>
				<th>Cleaning Type</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($duration_data as $item)
            @php
            $product_c = App\Models\ProductAmc::where('plan_id', $item->amc_id)
			->where('duration', $item->duration)
			->count();
            @endphp                                                    
                <tr>
                    <td>{{ $item->duration }} Days</td>
					<td>
					  @if($item->deep_cleaning)
						<div>{{$item->deep_cleaning}} Deep Cleaning</div>
					  @endif
					  @if($item->normal_cleaning)
						<div>{{$item->normal_cleaning}} Normal Cleaning</div>
					  @endif
					</td>
                    <td>
                        <a href="{{ route('amc.plan-master' , Crypt::encrypt($item->id)) }}" class="btn btn-outline-primary select-md">Product List ({{$product_c}})</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" class="text-danger text-center">No data found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    
   
</section>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
	
    
    $(document).ready(function(){
        $('div.alert').delay(3000).slideUp(300);
    })
    $('input[type=search]').on('search', function () {
        // search logic here
        // this function will be executed on click of X (clear button)
        $('#searchForm').submit();
    });
	$('.toggle_table').click(function(){
		$(this).parents('tr').toggleClass('is-expanded');
	});

    $('.itemremove').on("click", function() {
        var id = $(this).data('id');
        console.log('nh');
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "badge/delete/" + id;
            } else {
                Swal.fire("Cancelled", "Record is safe", "error");
            }
        });
    });

    function submitForm(formId, duration, planId) {
    const form = document.getElementById(formId);
    // Dynamically update the form's action URL with parameters
    form.action = form.action.replace('__PLAN_ID__', planId).replace('__DURATION__', duration);
    form.submit();
    }
	
		document.addEventListener('DOMContentLoaded', function () {
    $('.modal').each(function () {
        const modal = $(this);
        const form = modal.find('form#add_duration');

        const planId = modal.find('[id^=deep_cleaning_]').attr('id')?.split('_')[2]; // extract dynamic id

        const deepCheckbox = document.getElementById(`deep_cleaning_${planId}`);
        const deepInput = document.getElementById(`deep_cleaning_input_${planId}`);
        const normalCheckbox = document.getElementById(`normal_cleaning_${planId}`);
        const normalInput = document.getElementById(`normal_cleaning_input_${planId}`);

        // Show/hide inputs based on checkbox status (if checkboxes exist)
        if (deepCheckbox) {
            deepCheckbox.addEventListener('change', function () {
                deepInput.style.display = this.checked ? 'block' : 'none';
            });
        }

        if (normalCheckbox) {
            normalCheckbox.addEventListener('change', function () {
                normalInput.style.display = this.checked ? 'block' : 'none';
            });
        }

        modal.find('#duration_update').on('click', function () {
            // Clear previous errors
            modal.find('.error').text('');

            let hasError = false;
            const planDuration = (modal.find('input[name="plan_dureation"]').val() || '').trim();
            const deepDays = (modal.find('input[name="deep_cleaning_days"]').val() || '').trim();
            const normalDays = (modal.find('input[name="normal_cleaning_days"]').val() || '').trim();

            if (!planDuration) {
                modal.find('#plan_duration_error').text('Plan duration is required.');
                hasError = true;
            }

            // Handle deep cleaning validation
            if (deepCheckbox) {
                if (deepCheckbox.checked && !deepDays) {
                    modal.find('#deep_cleaning_error').text('Deep cleaning days are required.');
                    hasError = true;
                }
            } else {
                if (deepInput && deepInput.style.display !== 'none' && !deepDays) {
                    modal.find('#deep_cleaning_error').text('Deep cleaning days are required.');
                    hasError = true;
                }
            }

            // Handle normal cleaning validation
            if (normalCheckbox) {
                if (normalCheckbox.checked && !normalDays) {
                    modal.find('#normal_cleaning_error').text('Normal cleaning days are required.');
                    hasError = true;
                }
            } else {
                if (normalInput && normalInput.style.display !== 'none' && !normalDays) {
                    modal.find('#normal_cleaning_error').text('Normal cleaning days are required.');
                    hasError = true;
                }
            }

            // Show confirmation ONLY if at least one checkbox exists
            if (
                (deepCheckbox || normalCheckbox) &&
                planDuration &&
                ((deepCheckbox && !deepCheckbox.checked) || !deepCheckbox) &&
                ((normalCheckbox && !normalCheckbox.checked) || !normalCheckbox)
            ) {
                Swal.fire({
                    title: 'Proceed without selecting cleaning type?',
                    text: "Are you sure you want to continue without selecting any cleaning type?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Proceed',
                    cancelButtonText: 'No, Go Back',
                }).then((result) => {
                    if (result.isConfirmed && !hasError) {
                        form.submit();
                    }
                });
            } else if (!hasError) {
                form.submit();
            }
        });
    });
});



</script>  
@endsection 