@extends('layouts.app')
@section('content')
@section('page', 'Product AMC Plan')

<section>
    <ul class="breadcrumb_menu d-flex justify-content-between">        
        <li>Product Name: <strong>{{$product_name->title}}</strong></li>
        <a href="{{ route('amc.ho-sale') }}" class="btn btn-outline-primary btn-sm">Back</a>
    </ul>
    <form action="" id="searchForm">
        <div class="search__filter">
            <div class="row align-items-center justify-content-between">
                <div class="col">

                </div>
                <div class="col-2">
                    <select name="plan_type" class="form-control select-md" id="plan_type">
                        <option value="">Search by AMC Types</option>
                        @foreach($amc_plan as $item)
                        <option value="{{$item->id}}" @if($plan_type == $item->id) selected @endif >{{$item->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-2">
                    <select name="duration_type" class="form-control select-md" id="duration_type">
                        <option value="">Search by Duration( days )</option>
                        @foreach($amc_duration as $item)
                        <option value="{{$item->duration}}" @if($duration_type == $item->duration) selected @endif >{{$item->duration}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <a href="{{ route('amc.amc-by-product', [$kga_sales_id , Crypt::encrypt($id)]) }}" class="btn btn-outline-warning select-md">Reset</a>   
                </div>
                
            </div>
        </div>
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
    
    <table class="table">
        <thead>
            <tr>
                <th class="sr_no">#</th>
                <th class="primary_column">Name</th>
                <th>Plan Name</th>
                <th>Duration( days )</th>
                <th>Amount</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        @forelse ($data as $item)
            <tr>
                <td class="sr_no">{{ $loop->iteration }}</td>
                <td class="sr_no">{{$item->title}}</td>
                <td class="sr_no">
					@php
						$plan = $item->AmcPlanData;
						$duration = $item->AmcDurationData;
						$assets = $plan ? $plan->plan_asset_names : [];
					@endphp

					{{ $plan->name ?? 'No Plan' }} (

					@foreach($assets as $asset)
						@if($asset === 'Cleaning')
						  {{ $asset }} 
							@if($duration)
							(Deep Cleaning: {{ $duration->deep_cleaning ?? 0 }}, Normal Cleaning: {{ $duration->normal_cleaning ?? 0 }})
							@endif
						@else
						  {{ $asset }}
						@endif

						@if(!$loop->last), @endif
					@endforeach

					)
				</td>

                <td class="sr_no">{{$item->duration}}</td>
                <td class="sr_no">{{number_format($item->amount,2)}}</td>
                <td><a href="{{ route('amc.prepare-for-purchase-amc-plan', [$kga_sales_id , Crypt::encrypt($item->id)]) }}" class="btn btn-outline-primary select-md">Buy</a></td>
            </tr>
           
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


   $('#plan_type').on('change', function(){
        $('#searchForm').submit();
    })
   $('#duration_type').on('change', function(){
        $('#searchForm').submit();
    })
   $('#paginate').on('change', function(){
        $('#searchForm').submit();
    })

function validatePhone(itemId) {
    let phoneInput = document.getElementById(`phone_${itemId}`);
    let buyNowButton = document.getElementById(`buy_now_${itemId}`);
    
    // Check if the input is exactly 10 digits and contains only numbers
    if (phoneInput.value.length === 10 && /^[0-9]+$/.test(phoneInput.value)) {
        buyNowButton.style.display = "inline-block"; // Show the button
    } else {
        buyNowButton.style.display = "none"; // Hide the button
    }
}



    function applyDiscount(itemId, maxDiscount) {
        const discountInput = document.getElementById(`discount_${itemId}`);
        const amountInput = document.getElementById(`amount_${itemId}`);
        const actualAmount = parseFloat(document.querySelector(`#ProductAmcPlanBuy${itemId} input[name="actual_amount"]`).value);

        const discountValue = parseFloat(discountInput.value);

        if (discountValue >= 0 && discountValue <= maxDiscount) {
            const discountedAmount = actualAmount - (actualAmount * (discountValue / 100));
            amountInput.value = discountedAmount.toFixed(2); // Update amount field
            discountInput.classList.remove("is-invalid");
            discountInput.classList.add("is-valid");
        } else {
            discountInput.classList.remove("is-valid");
            discountInput.classList.add("is-invalid");
            amountInput.value = actualAmount.toFixed(2); // Reset to original amount
        }
    }
</script>
@endsection 