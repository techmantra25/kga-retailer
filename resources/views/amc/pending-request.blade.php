@extends('layouts.app')
@section('content')
@section('page', 'Pending Discount Request For AMC')

<section>
<ul class="breadcrumb_menu d-flex justify-content-between">
        <a href="{{ route('amc.ho-sale') }}" class="btn btn-outline-danger select-md" title="Back to Ho-sale">Back to Ho-Sale</a>
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

    <form action="" id="searchForm">
        <div class="search__filter">
            <div class="row align-items-center justify-content-between">
                <div class="col">

                </div>
                <div class="col-2">
                   <input type="text" name="search" value="{{$search}}"  class="form-control select-md"/>
                </div>
                <div class="col-auto">
                    <a href="{{ route('amc.pending-request')}}" class="btn btn-outline-warning select-md">Reset</a>   
                </div>
    
            </div>
        </div>
    </form>
<table class="table">
        <thead>
            <tr>
                <th class="sr_no">#</th>
                <th class="sr_no">KGA-Sales-Id</th>
                <th class="sr_no">AMC Unique No.</th>
                <th class="sr_no">Package Details</th>
                <th class="primary_column">Actual Amount</th>
                <th>Approved Discount %</th>
                <th>Discount Request %</th>
                <th>Purchase Amount</th>
                <th>Status</th>
                <th>Action</th>

            </tr>
        </thead>
        <tbody>

        @forelse($data as $item)
            @php
                    $amc_data = App\Models\ProductAmc::find($item->amc_id);  
                    $plan_data = $amc_data? App\Models\AmcPlanType::find($amc_data->plan_id) : null;  
            @endphp        
            <tr>  
                <td class="sr_no">{{$loop->iteration}}</td>
                <td class="sr_no"><a href="{{ route('amc.ho-sale',['search' => $item->kga_sales_id])}}" class="btn btn-outline-dark select-md">{{$item->kga_sales_id}}</a></td>
                <td class="sr_no">{{$item->amc_unique_number}}</td>
                <td>
                    <p class="small text-muted mb-1">
                        <span>Plan Name: <strong>{{optional($plan_data)->name}}</strong></span> <br/>
                        <span>Duration: <strong>{{optional($amc_data)->duration}} Days</strong></span> <br/>
                        
                    </p>
                </td>
                <td class="sr_no">{{$item->actual_amount}}</td>
                <td class="sr_no">{{$item->discount}}</td>
                <td class="sr_no">{{$item->discount_request}}</td>
                <td class="sr_no">{{$item->purchase_amount}}</td>
                @if($item->status === 2)
                <td class="sr_no"><span class="bg-warning">Pending</span></td>
                @elseif($item->status === 1)
                <td class="sr_no"><span class="bg-success">Payment Success</span></td>
                @elseif($item->status === 3)
                <td class="sr_no"><span class="bg-success">Approved & Waiting for payment</span></td>
                @else
                @endif
                <td class="sr_no">
                    <button type="button" class="btn btn-outline-primary select-md" data-bs-toggle="modal" data-bs-target="#request_approval{{$item->id}}">Approval</button>
                    @if($item->status === 3)
                      
                            <form method="post" action="{{ route('amc.after-discount-send-payment-link') }}">
                                @csrf
                                <button type="submit" class="btn btn-outline-primary select-md">Send Payment Link</button>
                                <input type="hidden" name="kga_sales_id" value="{{$item->kga_sales_id}}">
                            </form>
                   
                    @endif
                </td> 
            </tr>

            <div class="modal fade" id="request_approval{{$item->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Request Approval</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="post" action="{{ route('amc.request-approve') }}">
                        @csrf
                    <div class="modal-body">
                        <label>Discount Request (%)</label>
                        <input type="number" name="discount_request" class="form-control" value="{{$item->discount_request}}" readonly></br>
                    </div>
                    <div class="modal-body">
                        <label>Approval Discount Request (%)</label>
                        <input type="number" name="approval_request" class="form-control" id="approval_request{{$item->id}}" value="{{$item->discount}}" oninput="validateDiscount({{$item->id}})" required></br>
                        <div id="approval_error{{$item->id}}" class="text-danger mt-1" style="display: none;">
                            Discount cannot exceed {{$amc_discount}}%.
                        </div>
                    </div>
                    <input type="hidden" name="kga_sales_id" value="{{$item->kga_sales_id}}">
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="save_button{{$item->id}}" style="display: none;">Save</button>
                    </form>
                    </div>
                    </div>
                </div>
            </div>

        @empty
            <tr>
                <td colspan="12" style="text-align: center;">
                    No data found
                </td>
            </tr>
        @endforelse
        <input type="hidden" name="amc_discount" id="amc_discount" value="{{ $amc_discount }}">
        </tbody>
    </table>
    {{$data->links()}}

    
</section>
<script>
   
   function validateDiscount(id) {
    // alert(id);
        const approvalInput = document.getElementById(`approval_request${id}`);
        const saveButton = document.getElementById(`save_button${id}`);
        const errorText = document.getElementById(`approval_error${id}`);
        const amcDiscount = parseFloat(document.getElementById('amc_discount').value); // Fetch amc_discount value
        const discountValue = parseFloat(approvalInput.value);

        if (discountValue > amcDiscount) {
            approvalInput.classList.add('is-invalid'); // Add red border
            errorText.style.display = 'block'; // Show error message
            saveButton.style.display = 'none'; // Hide save button
        } else {
            approvalInput.classList.remove('is-invalid'); // Remove red border
            errorText.style.display = 'none'; // Hide error message
            saveButton.style.display = 'inline-block'; // Show save button
        }
    }
</script>  
@endsection 