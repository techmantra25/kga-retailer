@extends('layouts.app')
@section('content')
@section('page', 'AMC Plan')
<style>
  .error {
    color: red;
    font-size: 0.85em;
    margin-top: 4px;
}

</style>
<section>
    <ul class="breadcrumb_menu d-flex justify-content-between">        
        <li>Set Different AMC Plan </li>
        <button class="btn btn-outline-primary select-md" data-bs-toggle="modal" data-bs-target="#add" title="Add">Add Plan</button>
    </ul>
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
     <!-- add Modal-->
    <div class="modal fade" id="add" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('amc.plan-name-create') }}" method="post" id="createPlanForm">
                @csrf
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add AMC Plan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
					<label>Name :</label>
                     <input type="text" class="form-control" placeholder="Enter new plane name" name="plan_name" required/>
					<div class="error" id="plan_name_error"></div>
					<div class="mt-3">
						<label> Type:</label><br/>
						@foreach($plan_asset as $index => $plan_assets)
						<div class="form-check">
							<input type="checkbox" class="form-check-input" id="plan_asset_{{ $index }}" name="plan_asset[]"  value="{{$plan_assets->id}}" >
							<label class="form-check-label" for="plan_asset_{{ $index }}">{{$plan_assets->name}}</label>
						</div> 
						@endforeach
						<div class="error" id="plan_type_error"></div>
					 </div>
					  
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" id="create_plan"  class="btn btn-primary">Create</button>
                </div>
			</div>
            </form>
        </div>
    </div>
   <!-- <div class="col mb-2 mb-sm-0">
                @if (Session::has('message'))
                <div class="alert alert-success" role="alert">
                    {{ Session::get('message') }}
                </div>
                @endif
    </div>  -->
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th class="primary_column">Name</th>
                <th>Action</th>
                <th>Add Details</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $item)
            <tr>
                <td>{{$loop->iteration}}</td>
                <td><strong>{{$item->name}}</strong></td>
                <td>
                    <a class="btn btn-outline-primary select-md" href=" {{ route('amc.plan-duration' , Crypt::encrypt($item->id)) }} " title="View"> View </a>
                    <button type="button" class="btn btn-outline-primary select-md" data-bs-toggle="modal" data-bs-target="#edit{{$item->id}}" title="Edit"> Edit </button>
                    <button type="button" class="btn btn-outline-primary select-md" data-bs-toggle="modal" data-bs-target="#delete{{$item->id}}" title="Delete"> Delete </button>
                </td>
                <td>
                    <button class="btn btn-outline-primary select-md" data-bs-toggle="modal" data-bs-target="#add_days{{$item->id}}" title="Add Days">Add Days</button>
                    <button class="btn btn-outline-primary select-md" data-bs-toggle="modal" data-bs-target="#add_products{{$item->id}}" title="Add Products">Add Products</button>
                </td>
            </tr>   
             <!-- edit Modal-->
                <div class="modal fade" id="edit{{$item->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <form action="{{ route('amc.plan-name-edit') }}" method="post" id="editPlanForm{{ $item->id }}">
                            @csrf
                            <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Edit Plan Name</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                Name : <input type="text" class="form-control" name="plan_name" id="edit_plan_name_{{ $item->id }}" value="{{ $item->name}}" />
								<div class="error" id="edit_plan_name_error_{{ $item->id }}"></div>
                                <input type="hidden" name="id" value="{{$item->id}}">
								<div class="mt-3">
									<label>Assets Type:</label><br/>
									@php
									   $selectedPlanAssetsIds = explode(',',$item->plan_asset_id);
									@endphp
								@foreach($plan_asset as $index => $plan_assets)
								<div class="form-check">
									<input type="checkbox" class="form-check-input" id="plan_asset_{{ $item->id }}_{{ $index }}" name="plan_asset[]"  value="{{$plan_assets->id}}" {{in_array($plan_assets->id , $selectedPlanAssetsIds) ? 'checked' : ''}}>
									<label class="form-check-label" for="plan_asset_{{ $item->id }}_{{ $index }}">{{$plan_assets->name}}</label>
								</div> 
								@endforeach
									<div class="error" id="edit_plan_type_error_{{ $item->id }}"></div>
								</div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary" onclick="editPlan(this, {{ $item->id }})">Update</button>
                            </div>
						</div>
                        </form>
                    </div>
                </div>
             <!-- edit Modal-->
                <div class="modal fade" id="delete{{$item->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <form action="{{ route('amc.plan-name-delete') }}" method="post">
                            @csrf
                            <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Delete Plan</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <strong>Are you sure you want to delete it?</strong>
                                <input type="hidden" name="id" value="{{$item->id}}">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                                <button type="submit" class="btn btn-primary">Yes</button>
                            </div>
							</div>
                        </form>
                    </div>
                </div>

                <!-- add days Modal-->
                <div class="modal fade" id="add_days{{$item->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <form action="{{ route('amc.plan-duration-create') }}" method="post" id="add_duration">
                            @csrf
                            <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Add Days</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
								
                                Plan Name : <strong>{{ $item->name}}({{implode(',',$item->plan_asset_names)}})</strong>
                                <input type="hidden" name="plan_name" value="{{$item->id}}" />
                                <input type="number" class="form-control" id="plan_dureation" name="plan_dureation" placeholder="Enter plan duration in days" />
								<div class="error" id="plan_duration_error"></div>
								
								@php
								  $planAssetIds = explode(',',$item->plan_asset_id);
								@endphp
								@if(in_array( 1, $planAssetIds))
								
								<label>Cleaning Type:</label>
								<!-- Deep Cleaning Checkbox -->
								<div class="form-check mb-2 mt-2">
									<input class="form-check-input" type="checkbox" id="deep_cleaning_{{ $item->id }}" name="deep_cleaning_checkbox">
									<label class="form-check-label" for="deep_cleaning_{{ $item->id }}">Deep Cleaning</label>
								</div>
								<div id="deep_cleaning_input_{{ $item->id }}" class="mb-3" style="display: none;">
									<input type="number" class="form-control" id="deep_cleaning_days" name="deep_cleaning_days" placeholder="Enter deep cleaning">								 
									<div class="error" id="deep_cleaning_error"></div>
								</div>

								<!-- Normal Cleaning Checkbox -->
								<div class="form-check mb-2">
									<input class="form-check-input" type="checkbox" id="normal_cleaning_{{ $item->id }}" name="normal_cleaning_checkbox">
									<label class="form-check-label" for="normal_cleaning_{{ $item->id }}">Normal Cleaning</label>
								</div>
								<div id="normal_cleaning_input_{{ $item->id }}" class="mb-3" style="display: none;">
									<input type="number" class="form-control" id="normal_cleaning_days" name="normal_cleaning_days" placeholder="Enter normal cleaning">								  
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
                <!-- add Products Modal-->
                <div class="modal fade" id="add_products{{$item->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <form action="{{ route('amc.plan-duration-create') }}" method="post">
                            @csrf
                            <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Select Duration (in days)</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                @forelse($item->AmcDurationData as $days)
                                <a href="{{ route('amc.plan-master' , Crypt::encrypt($days->id)) }}" class="btn btn-outline-primary select-md">{{$days->duration}}</a>
                                @empty
                                <span class="bg-danger">No record found</span>
                                @endforelse
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </form>
                        </div>
                    </div>
                </div>
            @empty
            <tr>
                <td colspan="4" class="text-center">No data available.</td> <!-- Fallback message if no data -->
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
        //console.log('nh');
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
	
	//create plan type script
	 $(document).ready(function () {
    $('#create_plan').on('click', function (e) {
        const modal = $(this).closest('.modal');
        const form = modal.find('form#createPlanForm');
        let hasError = false;

        // Clear previous errors
        modal.find('.error').text('');

        // Validate plan name
        const planName = form.find('input[name="plan_name"]').val().trim();
        if (planName === '') {
            $('#plan_name_error').text('Plan name is required.');
            hasError = true;
        }

        // Validate at least one checkbox is selected
        const checkedAssets = form.find('input[name="plan_asset[]"]:checked');
        if (checkedAssets.length === 0) {
            $('#plan_type_error').text('Please select at least one type.');
            hasError = true;
        }

        // Submit the form if no errors
        if (!hasError) {
            form.submit();
        }
    });
});

//Edit plan script
	function editPlan(button, planId) {
    const modal = $(button).closest('.modal');
    const form = modal.find(`#editPlanForm${planId}`);

    let hasError = false;

    // Clear previous errors
    modal.find(`#edit_plan_name_error_${planId}`).text('');
    modal.find(`#edit_plan_type_error_${planId}`).text('');

    // Validate plan_name
    const planNameInput = form.find(`#edit_plan_name_${planId}`);
    const planName = planNameInput.length ? planNameInput.val().trim() : '';

    if (planName === '') {
        modal.find(`#edit_plan_name_error_${planId}`).text('Plan name is required.');
        hasError = true;
    }

    // Validate checked assets
    const checkedAssets = form.find('input[name="plan_asset[]"]:checked');
    if (checkedAssets.length === 0) {
        modal.find(`#edit_plan_type_error_${planId}`).text('At least one asset must be selected.');
        hasError = true;
    }

    // Submit form if no errors
    if (!hasError) {
        form.submit();
    }
}
	 
</script>  
<script>
function editPlan(button, id) {
    let isValid = true;
    const nameInput = document.getElementById(`edit_plan_name_${id}`);
    const name = nameInput.value.trim();
    const errorName = document.getElementById(`edit_plan_name_error_${id}`);
    const errorType = document.getElementById(`edit_plan_type_error_${id}`);
    
    errorName.innerText = "";
    errorType.innerText = "";

    const assetCheckboxes = document.querySelectorAll(`#edit${id} input[name='plan_asset[]']:checked`);

    if (name === "") {
        errorName.innerText = "Plan name is required";
        isValid = false;
    }

    if (assetCheckboxes.length === 0) {
        errorType.innerText = "Select at least one asset type";
        isValid = false;
    }

    if (isValid) {
        document.getElementById(`editPlanForm${id}`).submit();
    }
}
</script>

@endsection 