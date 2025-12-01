@extends('layouts.app')

@section('content')
@section('page', 'AMC Plan Assets')

<section>
	<form action="" method="GET" class="d-flex align-items-center gap-2 mb-0">
		<ul class="breadcrumb_menu d-flex align-items-center gap-2">
			
			<li class="d-flex align-items-center">
				
                <input type="text" name="search" class="form-control form-control-sm me-2" 
                        placeholder="Search by plan asset..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-outline-primary select-md">Search</button>
				
			</li>
			<li><a href="{{route('amc.plan-assets')}}" class="btn btn-outline-warning select-md custom-btm">Reset Page</a></li>
	   </ul>
		</form>


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

    <div class="row">
        <!-- Left Side: Table -->
        <div class="col-md-12">
            <div class="card">
                <div class="card-body p-0">
                    <table class="table mb-0">
                        <thead>
                            <tr class="text-center">
                                <th class="sr_no">#</th>
                                <th class="primary_column">Name</th>
                               <!-- <th>Action</th>  -->
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Loop your AMC records here -->
							@forelse ($plan_asset as $index=> $item)
                            <tr class="text-center">
                                <td>{{$index + 1}}</td>
                                <td>{{$item->name}}</td>
                                <td>
								 <!--  <a href="{{route('amc.plan-assets-edit',$item->id)}}" class="btn btn-outline-primary select-md" title="Edit">Edit</a> -->
                                 <!--  <a href="{{route('amc.plan-assets-delete',$item->id)}}" class="btn btn-outline-primary itemremove select-md" data-id="{{$item->id}}" title="Delete">Delete</a> -->
								</td>
                            </tr>
							@empty
							<tr>
								<td colspan="3" class="text-center">
								    No Data Found
								</td>
							</tr>
							@endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Side: Form -->
       <!-- <div class="col-md-5">
            <div class="card">
                <div class="card-body">
                    <form action="{{isset($plan_asset_data) ? route('amc.plan-assets-update',$plan_asset_data->id) : route('amc.plan-assets-create') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Plan Asset</label>
                            <input type="text" name="name" value="{{old('name',$plan_asset_data->name ?? '')}}" id="name" class="form-control" placeholder="Enter Plan Asset">
							@error('name')
							   <p class="small text-danger">{{$message}}</p>
							@enderror
                        </div> -->
                       
                       <!-- <button type="submit" class="btn btn-outline-primary select-md">{{isset($plan_asset_data) ? 'Update' : 'Add'}}</button>  -->
						
               <!--     </form>
                </div>
            </div>
        </div>  -->
    </div>
</section>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
	 $(document).on("click", ".itemremove", function(e) {
		 e.preventDefault();
        var id = $(this).data('id');
		 var url = $(this).attr('href');
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
                 window.location.href = url;
            } else {
                Swal.fire("Cancelled", "Record is safe", "error");
            }
        });
    });
</script>
@endsection
