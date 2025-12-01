@extends('layouts.app')
@section('content')
@section('page', 'AMC Plan Master')
<section>
    <ul class="breadcrumb_menu d-flex justify-content-between">        
        <li>Plan Name: <strong class="badge rounded-pill bg-secondary">
		<!--	{{$amc_plan_data->name}}({{implode(', ', $amc_plan_data->plan_asset_names)}}) -->
			@php
				// Get specific duration from relation collection matching the current id
				$duration = $amc_plan_data->AmcDurationData->firstWhere('id', $amc_duration_data->id);
			@endphp

			{{$amc_plan_data->name}} (
				@foreach($amc_plan_data->plan_asset_names as $index => $asset)
					@if($asset == 'Cleaning')
						{{$asset}} 
						@if($duration)
							(Deep Cleaning:{{$duration->deep_cleaning}}, Normal Cleaning:{{$duration->normal_cleaning}})
						@endif
					@else
						{{$asset}}
					@endif

					@if(!$loop->last), @endif
				@endforeach
			)


			</strong>  
			||  Plan Duration :  <strong  class="badge rounded-pill bg-secondary">{{$amc_duration_data->duration}} days
			</strong>
		</li>
    </ul>
    <ul class="breadcrumb_menu">
        <li>
            <a href="{{ route('amc.plan-duration' ,  Crypt::encrypt($amc_plan_data->id)) }}">
                <i class="fi-br-arrow-alt-circle-left"></i>
                Back To AMC Plans
            </a>
        </li>        
        <li>
            Total Product: {{$product_count}}
        </li>               
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
    <form action="{{ route('amc.save-product-amc') }}" method="POST">
            @csrf
            <input type="hidden" name="browser_name" id="browser_name">
            <input type="hidden" name="navigator_useragent" id="navigator_useragent">
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="">Product Name <span class="text-danger">*</span></label>
                        <input type="text" autocomplete="off" placeholder="Search Product by name" onkeyup="searchProduct(this.value);"  name="product_name" maxlength="200" class="form-control" id="product_name" value="{{ old('product_name') }}">
                        <input type="hidden" name="product_id" id="product_id" class="" value="">
                        <input type="hidden" name="plan_id" value="{{$amc_plan_data->id}}">
                        <input type="hidden" name="duration" value="{{$amc_duration_data->duration}}">
                        <input type="hidden" name="duration_id" value="{{$id}}">
                        <div class="respDropProduct" id="respDropProduct" style="position: relative;"></div>
                    </div>                                      
                </div>            
                <div class="col-sm-2" id="amc_amount">
                    <label for="">AMC Amount <span class="text-danger">*</span></label>
                    <input type="number" autocomplete="off" class="form-control" placeholder="Enter amount " name="amc_amount" required />
                </div>
                <div class="col-sm-2 mt-4" id="amc_save_button">
                    <button type="submit" class="btn btn-success">Save</button>
                </div>
            </div> 
    </form> 

    <div id="">
        <!-- <form action="" method="POST"> -->
            <!-- @csrf -->
            <input type="hidden" name="browser_name" id="browser_name">
            <input type="hidden" name="navigator_useragent" id="navigator_useragent">
            <div class="row" id="">
                <ul class="goods_class">               
                    @if (!empty($data))
                    @forelse ($data as $goods)
                        <li id="goodsli{{$goods->id}}"> 
                            {{ $goods->productData?$goods->productData->title:"" }} - (₹ {{number_format($goods->amount,2)}})
                            <a href="javascript:void(0);" onclick="removeStore({{$goods->id}});"><i class="fi fi-br-cross-small"></i>
                        </a> 
                        <input type="hidden" class="goods_ids" name="goods_ids[]" value="{{ $goods->product_id }}" >
                        </li>
                    @empty
                        <li class="bg-danger"> No Product assign </li>
                        
                    @endforelse
                    
                    @endif
                </ul>                
            </div> 
            <div class="row" id="goodsDiv">
                <div class="col-md-6">
                    <a href="{{ route('amc.plan-duration',  Crypt::encrypt($amc_plan_data->id)) }}" class="btn btn-danger select-md">Back</a>
                    <!-- <button type="submit" class="btn btn-success">Save</button> -->
                </div>
            </div>
        <!-- </form> -->
        
    </div>
    
</section>
<script>
    function getBrowserType() {
        const test = regexp => {
            return regexp.test(navigator.userAgent);
        };
        console.log(navigator.userAgent);
        var navigator_useragent = navigator.userAgent;
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
    $('#browser_name').val(browserType);
        

    $(document).ready(function(){
        $('#amc_amount').hide();
        $('#amc_save_button').hide();
	});


    function searchProduct(search){
        if(search.length > 0) {
            let goodsIds = $("input[name='goods_ids[]']").map(function () {
                return $(this).val(); // Get the value of each input
            }).get();
            $.ajax({
                url: "{{ route('ajax.search-product-for-amc') }}",
                method: 'post',
                data: {
                    '_token': '{{ csrf_token() }}',
                    search: search,
                    idnotin:goodsIds,
                    type: 'fg'
                },
                success: function(result) {
                    // console.log(result);
                    var content = '';
                    if (result.length > 0) {
                        content += `<div class="dropdown-menu show  product-dropdown select-md" aria-labelledby="dropdownMenuButton" style="width: 100%;">`;

                        $.each(result, (key, value) => {                            
                            content += `<a class="dropdown-item" href="javascript: void(0)" onclick="fetchProduct(${value.id})">${value.title}</a>`;
                        })
                        content += `</div>`;
                        // $($this).parent().after(content);
                    } else {
                        content += `<div class="dropdown-menu show  product-dropdown select-md" aria-labelledby="dropdownMenuButton" style="width: 100%; text-align:center;"><li class="dropdown-item">No product found</li></div>`;
                    }
                    $('#respDropProduct').html(content);
                }
            });
        } else {
            $('.product-dropdown').hide()
        }
        
    }

    function fetchProduct(id) {
        $('.product-dropdown').hide();
        $('#amc_amount').show();
        // $('#amc_save_button').show();
        // Show the save button when typing in the input field
        $('#amc_amount input[name="amc_amount"]').on('input', function () {
            if ($(this).val().trim() !== '') {
                $('#amc_save_button').show(); // Show the button if the input has a value
            } else {
                $('#amc_save_button').hide(); // Hide the button if the input is empty
            }
        });

        $.ajax({
            url: "{{ route('ajax.get-single-product') }}",
            method: 'post',
            data: {
                '_token': '{{ csrf_token() }}',
                id:id
            },
            success: function(result) {
                console.log(result);
                var title = result.title;

                $('#product_name').val(title);
                $('#product_id').val(id);          
                // proIdArr.push(id);
    
                // $('.goods_class').append(`<li id="goodsli`+id+`">`+title+` <a href="javascript:void(0);"  onclick="removeStore('`+id+`');"><i class="fi fi-br-cross-small"></i></a><input type="hidden" class="goods_ids" name="goods_ids[]" value="`+id+`" ></li>`);
                // $('.product-dropdown').hide();
                // $('#product').val('');

                // if(proIdArr.length == 0){
                //     $('#goodsDiv').hide();
                // } else {
                //     $('#goodsDiv').show();
                // }

                // // alert(proIdArr) 
                
            }
        });             
    } 


    function removeStore(id){
        // console.log(id);
        const navigator_useragent = navigator.userAgent;
        const browserType = getBrowserType();

        $.ajax({
                url: "{{ route('ajax.amc-product-delete') }}",
                method: 'post',
                data: {
                    '_token': '{{ csrf_token() }}',
                    id: id,
                    navigator_useragent: navigator_useragent,
                    browser_name: browserType
                },
                success: function(result) {
                    // console.log(result);
                    if(result.status == 200){
                        location.reload();
                    }
                }
            });
    
    }
          
</script>  
@endsection 