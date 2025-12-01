@extends('layouts.app')
@section('content')
@section('page', 'Book a Repair')
<section>
    <ul class="breadcrumb_menu">
        <li>Service Partner Management</li>
        <li><a href="{{ route('repair.list') }}">Repair Request</a> </li>
        <li>Book a Repair</li>
    </ul>
    <div class="row">
        @if (Session::has('message'))
        <div class="alert alert-success" role="alert">
            {{ Session::get('message') }}
        </div>
        @endif
        @if (Session::has('serial'))
        <div class="alert alert-danger" role="alert">
            {{ Session::get('serial') }}
        </div>
        @endif
        <form id="myForm" action="{{ route('maintenance.save') }}" enctype="multipart/form-data" method="POST">
            @csrf
            <div class="row">
                <div class="col-sm-12">
                    <ul class="pincodeclass">
                        <li>Dealer</li>
                    </ul>
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">Dealer <span class="text-danger">*</span></label>
                                        @if(Request::get('dealer_id')==2)
                                        <input type="text" autocomplete="off" class="form-control" id="dealer_name"
                                            name="dealer_name"
                                            value="{{ 'Khosla Electronics Pvt. Ltd.', old('dealer_name') }}">
                                        <input type="hidden" name="dealer_id" id="dealer_id" class=""
                                            value="{{Request::get('dealer_id'), old('dealer_id') }}">
                                        @else
                                        <input type="text" autocomplete="off" class="form-control" id="dealer_name"
                                            onkeyup="searchDealerUser(this.value);"
                                            placeholder="Search dealer user ... " name="dealer_name"
                                            value="{{ old('dealer_name') }}">
                                        <input type="hidden" name="dealer_id" id="dealer_id" class=""
                                            value="{{Request::get('dealer_id'), old('dealer_id') }}">
                                        @endif

                                        <input type="hidden" name="dealer_type" id="dealer_type" class=""
                                            value="{{Request::get('dealer_type'), old('dealer_type') }}">

                                        <div class="respDropDealer" id="respDropDealer" style="position: relative;">
                                        </div>
                                        @error('dealer_id') <p class="small text-danger">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">PIN Code<span class="text-danger">*</span></label>
                                        <input type="text" autocomplete="off" placeholder="Enter PIN Code" maxlength="6"
                                            name="pincode" class="form-control"
                                            onkeyup="getServicePartners(this.value);" id="pincode"
                                            onkeypress="validateNum(event)"
                                            value="{{Request::get('pincode'), old('pincode') }}">
                                        @error('pincode') <p class="small text-danger">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">Assigned Service Partner </label>
                                        <input type="hidden" name="service_partner_id" id="service_partner_id"
                                            value="{{old('service_partner_id')}}">
                                        <input type="hidden" name="service_partner_email" id="service_partner_email"
                                            value="{{old('service_partner_email')}}">
                                        <input type="hidden" name="service_partner_person_name"
                                            id="service_partner_person_name"
                                            value="{{old('service_partner_person_name')}}">
                                        <input type="hidden" name="service_partner_company_name"
                                            id="service_partner_company_name"
                                            value="{{old('service_partner_company_name')}}">
                                        <input type="text" autocomplete="off" name="service_partner_name"
                                            class="form-control" id="service_partner_name"
                                            value="{{old('service_partner_name')}}"
                                            onkeyup="searchServicePartner(this.value);"
                                            placeholder="Search service partner ... ">

                                        <div class="respDropServicePartner" id="respDropServicePartner"
                                            style="position: relative;"></div>


                                        @error('service_partner_id') <p class="small text-danger">{{ $message }}</p>
                                        @enderror

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <ul class="pincodeclass">
                        <li>End Customer</li>
                    </ul>
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Name <span class="text-danger">*</span></label>
                                        <input type="text" autocomplete="off" placeholder="Enter Customer Full Name"
                                            name="customer_name" class="form-control" maxlength="250" id=""
                                            value="{{Request::get('customer_name'), old('customer_name') }}">
                                        @error('customer_name') <p class="small text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Mobile No <span class="text-danger">*</span></label>
                                        <input type="text" autocomplete="off" placeholder="Enter Customer Mobile No"
                                            name="customer_phone" class="form-control" maxlength="10"
                                            id="customer_phone"
                                            value="{{Request::get('mobile'), old('customer_phone') }}">
                                        @error('customer_phone') <p class="small text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                {{-- <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Alternate Mobile No <span class="text-danger">*</span></label>
                                <input type="text" autocomplete="off" placeholder="Enter Customer Alternate Mobile No" name="customer_alternate_phone" class="form-control" maxlength="10" id="customer_alternate_phone" value="{{ old('customer_alternate_phone') }}">
                                @error('customer_alternate_phone') <p class="small text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group">
                                <label for="sameAsPhone">Same as Mobile</label><br />
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input
                                    type="checkbox" id="sameAsPhone" onchange="copyPhone()">
                            </div>
                        </div> --}}
                    </div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">Address </label>
                                <textarea name="address" placeholder="Enter Address" class="form-control" id="" cols="1"
                                    rows="1">{{Request::get('address'), old('address') }}</textarea>
                                @error('address') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <ul class="pincodeclass">
                <li>Bill</li>
            </ul>
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Bill No<span class="text-danger">*</span></label>
                                <input type="text" autocomplete="off" placeholder="Enter Bill No" name="bill_no"
                                    class="form-control" maxlength="100" id=""
                                    value="{{Request::get('bill_no'), old('bill_no') }}">
                                @error('bill_no') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Invoice/Bill Date<span class="text-danger">*</span></label>
                                <input type="date" max="{{date('Y-m-d')}}" name="order_date" class="form-control"
                                    id="order_date" value="{{Request::get('bill_date'), old('order_date') }}">
                                @error('order_date') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Bill Value</label>
                                <input type="text" autocomplete="off" placeholder="Enter Bill Value"
                                    name="product_value" class="form-control" id="" value="{{ old('product_value') }}">
                                @error('product_value') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <ul class="pincodeclass">
                <li>Product</li>
            </ul>
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-9">
                            <div class="form-group">
                                <label for="">Name <span class="text-danger">*</span></label>
                                <input type="hidden" name="product_id" id="product_id"
                                    value="{{Request::get('product_id')}}">
                                <input type="hidden" name="product_type" id="product_type"
                                    value="{{Request::get('product_type')}}">
                                <input type="text" autocomplete="off" placeholder="Enter Product Name"
                                    name="product_name" maxlength="200" class="form-control" id="product_name"
                                    value="{{ Request::get('product_name') }}" readonly>
                                <div class="respDropProduct" id="respDropProduct" style="position: relative;"></div>
                                @error('product_name') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Serial No<span class="text-danger">*</span></label>
                                <input type="text" autocomplete="off" placeholder="Enter Product Serial No"
                                    name="product_sl_no" id="product_sl_no" maxlength="100" class="form-control" id=""
                                    value="{{ Request::get('serial') }}" readonly>
                                <input type="hidden" autocomplete="off" placeholder="Enter Product Serial No"
                                    name="GetCleaningWarranty" id="GetCleaningWarranty" maxlength="100"
                                    class="form-control" id="" value="{{ $GetCleaningWarranty }}" readonly>
                                <input type="hidden" autocomplete="off" placeholder="Enter Product Serial No"
                                    name="GetDeepCleaningWarranty" id="GetDeepCleaningWarranty" maxlength="100"
                                    class="form-control" id="" value="{{ $GetDeepCleaningWarranty }}" readonly>
                                <!--Amc Normal & Deep-->
                                <input type="hidden" autocomplete="off" placeholder="Enter Product Serial No"
                                    name="amcCleaningUsed" id="amcCleaningUsed" maxlength="100" class="form-control"
                                    id="" value="{{ $amcCleaningUsed }}" readonly>
                                <input type="hidden" autocomplete="off" placeholder="Enter Product Serial No"
                                    name="amcDeepCleaningUsed" id="amcDeepCleaningUsed" maxlength="100"
                                    class="form-control" id="" value="{{ $amcDeepCleaningUsed }}" readonly>
                                @error('product_sl_no') <p class="small text-danger">{{ $message }}</p> @enderror
                                <span id="repeat_call" style="display: none;">
                                    <p class="small text-danger">Repeat Call</p>
                                </span>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body" id="div_warranty">

                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="">Service For </label>
                                <select name="service_for" class="form-control" id="service_for">
                                    <option value="chimney">Chimney</option>
                                    <!-- <option value="motor">Motor</option> -->
                                </select>
                                @error('service_for') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="">Service Type <span class="text-danger">*</span></label>
                                <select name="service_type" class="form-control" id="service_type">
                                    <option selected hidden value="" title="Cleaning For Chimney">--select--</option>
                                    <option value="cleaning" title="Normal Cleaning For Chimney">Normal Cleaning
                                    </option>
                                    <option value="deep_cleaning" title="Deep Cleaning For Chimney">Deep Cleaning
                                    </option>
                                    <option value="repairing" title="Repairing For Chimney"
                                        @if(old('service_type')=='repairing' ) selected @endif>Repairing</option>
                                </select>
                                @error('service_type') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <!-- <div class="col-md-2">
                            <div class="form-group">
                                <label for="">Service Type </label>
                                <select name="service_type" class="form-control" id="service_type">   
                                    <option value="repairing" title="For Both Chminey And Motor">Repairing</option>
                                    <option value="cleaning" title="Only For Chimney" @if(old('service_for') == 'motor') disabled @endif>Cleaning</option> 
                                </select> 
                                @error('service_type') <p class="small text-danger">{{ $message }}</p> @enderror                            
                            </div>
                        </div>   -->
                        <input type="hidden" name="is_spare_chargeable" id="is_spare_chargeable" value="">
                        <input type="hidden" name="is_repair_chargeable" id="is_repair_chargeable" value="">
                        <input type="hidden" name="out_of_warranty" id="out_of_warranty" value="">
                        <input type="hidden" name="maintenance_type" id="maintenance_type" value="">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Remarks <span class="text-danger">*</span></label>
                                <textarea name="remarks" class="form-control" id="" cols="3"
                                    rows="5">{{ old('remarks') }}</textarea>
                                @error('remarks') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body text-end">
                    <a href="{{route('maintenance.checkitemstatus')}}" class="btn btn-sm btn-danger">Back</a>
                    <button type="submit" id="submitBtn" class="btn btn-sm btn-success">Submit </button>
                </div>
            </div>
    </div>
    </div>
    <!-- Hidden spans for JS -->

    </form>
    </div>
</section>
<script>
    $("#myForm").submit(function () {
        $('input').attr('readonly', 'readonly');
        $('#submitBtn').attr('disabled', 'disabled');
        $('#submitBtn').html('<i class="fi fi-br-refresh"></i>');
        return true;
    });

    $('#order_date').on('change', function () {
        var product_id = $('#product_id').val();
        var order_date = $(this).val();
        if (product_id != '') {
            fetchProduct(product_id, order_date);
        }
    })
    $('#service_type').on('change', function () {
        var selectedServiceType = $(this).val();
        var cleaning_status = $('#cleaning_status').val() || 'NO';
        var amc_remaining_normal_cleaning = $('#amc_remaining_normal_cleaning').val() || 0;
        var amc_remaining_deep_cleaning = $('#amc_remaining_deep_cleaning').val() || 0;
        var amc_cleaning_status = $('#amc_cleaning_status').val() || 'NO';
        var product_id = $('#product_id').val();
        var order_date = $('#order_date').val();
        const warranty_type = "comprehensive";
        if (product_id != '') {
            fetchProductComprehensiveWarranty(product_id, order_date, warranty_type);
        }

        if (selectedServiceType === 'repairing') {
            var serial_no = $('#product_sl_no').val();
            $.ajax({
                url: "{{ route('ajax.get-chimnney-repairing-repeat-call') }}",
                method: 'post',
                data: {
                    '_token': '{{ csrf_token() }}',
                    serial_no: serial_no,
                    selectedServiceType: selectedServiceType
                },
                success: function (result) {
                    if (result['repeat_call'] == 1) {
                        alert('This is a repeat call for service-repair.');
                        document.getElementById('repeat_call').style.display = 'block';
                    } else {
                        document.getElementById('repeat_call').style.display = 'none';
                    };
                    $('#submitBtn').show();
                },
                error: function (xhr, status, error) {
                    console.error('AJAX Error:', error); // Log any errors for debugging
                }
            });
        } else if (selectedServiceType === 'cleaning') {
            if (cleaning_status.trim().toUpperCase() === 'NO' && amc_cleaning_status.trim().toUpperCase() === 'NO') {
                alert('Call book for Normal Cleaning is not applicable');
                $('#submitBtn').hide();
            }else if(cleaning_status.trim().toUpperCase() === 'NO' && amc_cleaning_status.trim().toUpperCase() === 'YES'){
                if(amc_remaining_normal_cleaning==0){
                     alert('Call book for Normal Cleaning is not applicable');
                    $('#submitBtn').hide();
                }else{
                     $('#submitBtn').show();
                }
            }else {
                $('#submitBtn').show();  // Show the submit button otherwise
            }
        }
        else if (selectedServiceType === 'deep_cleaning') {
            //if (cleaning_status.trim().toUpperCase() === 'NO' && amc_cleaning_status.trim().toUpperCase() === 'NO') {
			if (amc_cleaning_status.trim().toUpperCase() === 'NO') {
                alert('Call book for Deep Cleaning is not applicable');
                $('#submitBtn').hide();
            //}else if(cleaning_status.trim().toUpperCase() === 'NO' && amc_cleaning_status.trim().toUpperCase() === 'YES'){
			}else if(amc_cleaning_status.trim().toUpperCase() === 'YES'){
                if(amc_remaining_deep_cleaning==0){
                     alert('Call book for Deep Cleaning is not applicable');
                    $('#submitBtn').hide();
                }else{
                     $('#submitBtn').show();
                }
            }else {
                $('#submitBtn').show();  // Show the submit button otherwise
            }
        } else {
            document.getElementById('repeat_call').style.display = 'none';
            $('#submitBtn').show()
        }
    })


    function fetchProductComprehensiveWarranty(id, order_date, warranty_type) {
        $('.product-dropdown').hide();
        $('#div_warranty').show();


        var GetCleaningWarranty = $('#GetCleaningWarranty').val();
        var serial_no = $('#product_sl_no').val();
        var GetDeepCleaningWarranty = $('#GetDeepCleaningWarranty').val();
        //console.log(GetCleaningWarranty);
        var dealer_type = $('#dealer_type').val();
        // console.log(dealer_type);
        $.ajax({
            url: "{{ route('ajax.get-product-warranty-status') }}",
            method: 'post',
            data: {
                '_token': '{{ csrf_token() }}',
                id: id,
                order_date: order_date,
                serial_no: serial_no,
                dealer_type: dealer_type,

                to_date: "{{date('Y-m-d')}}"
            },
            success: function (result) {
                console.log(result.data);

                // Find the object where warranty_type is 'repaire'
                var comprehensive_warranty = result.data.find(item => item.warranty_type ===
                    'comprehensive');
                var additional_warranty = result.data.find(item => item.warranty_type ===
                "additional"); // additional warranty for repaire charge chargable


                //comprehensive warranty
                if (comprehensive_warranty) {
                    console.log(comprehensive_warranty.warranty_status);
                    if (comprehensive_warranty.warranty_status == 'YES') {
                        var out_of_warranty = 0;
                        var maintenance_type = 'free';
                        var is_spare_chargeable = 0;
                        var is_repair_chargeable = 0;
                        $('#out_of_warranty').val(out_of_warranty);
                        $('#maintenance_type').val(maintenance_type);
                        $('#is_spare_chargeable').val(is_spare_chargeable);
                        $('#is_repair_chargeable').val(is_repair_chargeable);
                    } else {
                        var out_of_warranty = 1;
                        var maintenance_type = 'out_of_warranty';
                        var is_spare_chargeable = 1;
                        var is_repair_chargeable = 1;
                        $('#out_of_warranty').val(out_of_warranty);
                        $('#maintenance_type').val(maintenance_type);
                        $('#is_spare_chargeable').val(is_spare_chargeable);
                        $('#is_repair_chargeable').val(is_repair_chargeable);
                        //now starting addition warrranty when ===>> comprehensive_warranty.warranty_status == 'NO'
                        if (additional_warranty.warranty_status == 'YES') {
                            if (additional_warranty.additional_warranty_type == "2") {
                                var out_of_warranty = 1;
                                var maintenance_type = 'additional';
                                var is_repair_chargeable = 0;
                                var is_spare_chargeable = 1;
                                $('#out_of_warranty').val(out_of_warranty);
                                $('#maintenance_type').val(maintenance_type);
                                $('#is_repair_chargeable').val(is_repair_chargeable);
                                $('#is_spare_chargeable').val(is_spare_chargeable);
                            } else {
                                var out_of_warranty = 1;
                                var maintenance_type = 'additional';
                                var is_repair_chargeable = 1;
                                var is_spare_chargeable = 0;
                                $('#out_of_warranty').val(out_of_warranty);
                                $('#maintenance_type').val(maintenance_type);
                                $('#is_repair_chargeable').val(is_repair_chargeable);
                                $('#is_spare_chargeable').val(is_spare_chargeable);
                            }

                        }

                        console.log("comprehensive Warranty Status:", comprehensive_warranty
                            .warranty_status);
                    }
                } else {
                    var out_of_warranty = 1;
                    var maintenance_type = 'out_of_warranty';
                    var is_spare_chargeable = 1;
                    var is_repair_chargeable = 1;
                    $('#out_of_warranty').val(out_of_warranty);
                    $('#maintenance_type').val(maintenance_type);
                    $('#is_spare_chargeable').val(is_spare_chargeable);
                    $('#is_repair_chargeable').val(is_repair_chargeable);
                }

            }
        });
    }

    function validateNum(evt) {
        var theEvent = evt || window.event;

        // Handle paste
        if (theEvent.type === 'paste') {
            key = event.clipboardData.getData('text/plain');
        } else {
            // Handle key press
            var key = theEvent.keyCode || theEvent.which;
            key = String.fromCharCode(key);
        }
        var regex = /[0-9]/; // only number
        // var regex = /[0-9]|\./; // number with point
        if (!regex.test(key)) {
            theEvent.returnValue = false;
            if (theEvent.preventDefault) theEvent.preventDefault();
        }
    }

    function searchDealerUser(search) {
        if (search.length > 0) {
            $.ajax({
                url: "{{ route('ajax.search-dealer-user') }}",
                method: 'post',
                data: {
                    '_token': '{{ csrf_token() }}',
                    search: search
                },
                success: function (result) {
                    var content = '';
                    if (result.length > 0) {
                        content +=
                            `<div class="dropdown-menu show  dealer-dropdown select-md" aria-labelledby="dropdownMenuButton" style="width: 100%;">`;

                        $.each(result, (key, value) => {
                            content +=
                                `<a class="dropdown-item" href="javascript: void(0)" onclick="fetchDealer(${value.id},'${value.name}','${value.dealer_type}')">${value.name} </a>`;
                        })
                        content += `</div>`;
                        // $($this).parent().after(content);
                    } else {
                        content +=
                            `<div class="dropdown-menu show  dealer-dropdown select-md" aria-labelledby="dropdownMenuButton"><li class="dropdown-item">No product found</li></div>`;
                    }
                    $('#respDropDealer').html(content);
                }
            });
        } else {
            $('.dealer-dropdown').hide()
        }

    }

    function fetchDealer(id, name, dealer_type) {
        console.log(dealer_type);
        $('.dealer-dropdown').hide()
        $('#dealer_id').val(id);
        $('#dealer_name').val(name);
        $('#dealer_type').val(dealer_type);
        $('#order_date').val('');
        $('#div_warranty').html('');
    }

    function fetchProduct(id, order_date) {
        $('.product-dropdown').hide();
        $('#div_warranty').show();


        var GetCleaningWarranty = $('#GetCleaningWarranty').val();
        var serial_no = $('#product_sl_no').val();
        var GetDeepCleaningWarranty = $('#GetDeepCleaningWarranty').val();
        var amcCleaningUsed = parseInt($('#amcCleaningUsed').val()) || 0;
        var amcDeepCleaningUsed = parseInt($('#amcDeepCleaningUsed').val()) || 0;
        //console.log(GetCleaningWarranty);
        var dealer_type = $('#dealer_type').val();
        // console.log(dealer_type);
        $.ajax({
            url: "{{ route('ajax.get-product-warranty-status') }}",
            method: 'post',
            data: {
                '_token': '{{ csrf_token() }}',
                id: id,
                order_date: order_date,
                dealer_type: dealer_type,
                serial_no: serial_no,
                to_date: "{{date('Y-m-d')}}"
            },
            success: function (result) {
                console.warn(result.data);
                // Find the object where warranty_type is 'cleaning'
                var comprehensive_warranty = result.data.find(item => item.warranty_type ===
                    'comprehensive');
                var cleaning_warranty = result.data.find(item => item.warranty_type === 'cleaning');
                if (comprehensive_warranty) {
                    console.log(comprehensive_warranty.warranty_status);
                    if (comprehensive_warranty.warranty_status == 'YES') {
                        var out_of_warranty = 0;
                        $('#out_of_warranty').val(out_of_warranty);
                    } else {
                        var out_of_warranty = 1;
                        $('#out_of_warranty').val(out_of_warranty);
                    }
                } else {
                    var out_of_warranty = 1;
                    $('#out_of_warranty').val(out_of_warranty);
                }


                // If found, access the warranty_status value
                if (cleaning_warranty) {
                    if (cleaning_warranty.warranty_status == 'YES') {
                        // var out_of_warranty = 0;
                        var maintenance_type = 'free';
                        var is_spare_chargeable = 0;
                        var is_repair_chargeable = 0;
                        // $('#out_of_warranty').val(out_of_warranty);
                        $('#maintenance_type').val(maintenance_type);
                        $('#is_spare_chargeable').val(is_spare_chargeable);
                        $('#is_repair_chargeable').val(is_repair_chargeable);
                    } else {
                        // var out_of_warranty = 1;
                        var maintenance_type = 'out_of_warranty';
                        var is_spare_chargeable = 0;
                        var is_repair_chargeable = 0;
                        // $('#out_of_warranty').val(out_of_warranty);
                        $('#maintenance_type').val(maintenance_type);
                        $('#is_spare_chargeable').val(is_spare_chargeable);
                        $('#is_repair_chargeable').val(is_repair_chargeable);
                    }
                    console.log("Cleaning Warranty Status:", cleaning_warranty.warranty_status);
                } else {
                    // var out_of_warranty = 1;   
                    var maintenance_type = 'out_of_warranty';
                    var is_spare_chargeable = 0;
                    var is_repair_chargeable = 0;
                    // $('#out_of_warranty').val(out_of_warranty);
                    $('#maintenance_type').val(maintenance_type);
                    $('#is_spare_chargeable').val(is_spare_chargeable);
                    $('#is_repair_chargeable').val(is_repair_chargeable);
                    console.log("No cleaning warranty found.");
                }


                //comprehensive warranty
                if (comprehensive_warranty) {
                    console.log(comprehensive_warranty.warranty_status);
                    if (comprehensive_warranty.warranty_status == 'YES') {
                        var out_of_warranty = 0;
                        $('#out_of_warranty').val(out_of_warranty);
                    } else {
                        var out_of_warranty = 1;
                        $('#out_of_warranty').val(out_of_warranty);
                        console.log("comprehensive Warranty Status:", comprehensive_warranty
                            .warranty_status);
                    }
                } else {
                    var out_of_warranty = 1;
                    $('#out_of_warranty').val(out_of_warranty);
                    console.log("No cleaning warranty found.");
                }

                var html = "";
                var dealer_text_type = dealer_type == 'khosla' ? "Khosla" : "Non Khosla";
                if (result.status === true && result.data.length > 0) {
                    html += `<div class="card shadow-sm">
                                <div class="card-header bg-light">
                                    <span class="badge bg-secondary">Dealer Type: <span id="dealer_text_type">${dealer_text_type}<span></span>
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
                    let comprehensiveDeepCleaningTotal = 0;
                    let comprehensiveNormalCleaningTotal = 0;
                    $.each(result.data, function (key, item) {
                        var display_type = item.warranty_type === "deep_cleaning" ?
                            "Deep Cleaning" :
                            item.warranty_type === "cleaning" ?
                            "Normal Cleaning" :
                            item.warranty_type.charAt(0).toUpperCase() + item.warranty_type.slice(
                            1);

                        html += `<tr>
									<td> ${display_type}`;
                        //console.log(item);
                        //html += `<tr>
                        //   <td> ${item.warranty_type.charAt(0).toUpperCase() + item.warranty_type.slice(1)}`;

                        // Checking if warranty_type is "additional"
                        if (item.warranty_type === "additional") {
                            html += ` <span class="badge bg-danger" style="cursor: pointer; font-size: 9px;">
                                        ${item.additional_warranty_type == 1 ? "Parts Chargeable" : "Service Chargeable"}
                                    </span>`;
                        }

                        // Checking if warranty_type is "cleaning"

                        if (item.warranty_type === "cleaning") {
                            comprehensiveNormalCleaningTotal = parseInt(item.number_of_cleaning) ||
                                0;
                            const remaining = comprehensiveNormalCleaningTotal -
                            GetCleaningWarranty;
                            html += ` <span class="badge bg-danger" style="cursor: pointer; font-size: 9px;" title="Number of cleaning">
                                        ${item.number_of_cleaning}
                                    </span> Left
                                    <span class="badge bg-success" style="cursor: pointer; font-size: 9px;" title="Number of cleaning">
                                        ${remaining > 0 ? remaining : 0}
                                    </span>`;
                        }
                        //Checking if warranty type is "deep_cleaning"
                       /* if (item.warranty_type === "deep_cleaning") {
                            comprehensiveDeepCleaningTotal = parseInt(item
                                .number_of_deep_cleaning) || 0;
                            const remaining = comprehensiveDeepCleaningTotal -
                                GetDeepCleaningWarranty;
                            html += ` <span class="badge bg-danger" style="cursor: pointer; font-size: 9px;" title="Number of cleaning">
                                        ${item.number_of_deep_cleaning}
                                    </span> Left
									 <span class="badge bg-success" style="cursor: pointer; font-size: 9px;" title="Number of cleaning">
                                       ${remaining > 0 ? remaining : 0}
                                    </span>
									`;

                        } */

                        // Adding spear_goods if available
                        if (item.parts) {
                            html += ` <span class="badge bg-success"> ${item.parts}</span>`;
                        }

                        html += `</td>
                                <td> <span class="badge bg-success">${item.warranty_period}</span></td>
                                <td> <span class="badge bg-${item.warranty_status==="YES"?"success":"danger"}">${item.warranty_end_date}</span></td>
                                <td>`;
                        if (item.warranty_type === "comprehensive") {
                            html +=
                                `<input type="hidden" id="cleaning_status" name="cleaning_status" value="${item.warranty_status}">`;
                        }
                        html += `<span class="badge bg-${item.warranty_status==="YES"?"success":"danger"}">${item.warranty_status}</span>
                                </td>
                                </tr>`;
                    });
                    html +=
                        `<input type="hidden" id="comprehensive_normal_cleaning_total" value="${comprehensiveNormalCleaningTotal}">`;
                    html +=
                        `<input type="hidden" id="comprehensive_deep_cleaning_total" value="${comprehensiveDeepCleaningTotal}">`;

                    html += `</tbody>
                            </table>
                            </div>
                            </div>`;

                    // Add AMC data processing
                    if (result.amc_subscription) {
                        var amc_subscription = result.amc_subscription;
                        $('#service_type').prop('selectedIndex', 0);
                        console.log(amc_subscription);
                        html += `
                        <div class="card shadow-sm mt-3">
                            <div class="card-header bg-light">
                                <span class="badge bg-secondary">AMC Details</span>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm table-borderless">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>AMC Number</th>
                                            <th>Plan</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th>Warranty Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                       <tr>
                                            <td>${amc_subscription.amc_number}</td>
                                            <td>${amc_subscription.amc_plan_name}(${amc_subscription.amc_duration}Days)</td>
                                            <td>
												${amc_subscription.amc_start_date}
											<input type="hidden" id="amc_start_date" name="amc_start_date" value="${amc_subscription.amc_start_date}">
											</td>
                                            <td>${amc_subscription.amc_end_date}</td>
                                             <td>
                                                <input type="hidden" id="amc_cleaning_status" name="amc_cleaning_status" value="${amc_subscription.warranty_status}">
                                             <span class="badge bg-${amc_subscription.warranty_status==="YES"?"success":"danger"}">
                                                ${amc_subscription.warranty_status}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="4">
                                                Normal Cleaning:
                                                <span class="badge bg-primary">Total: ${amc_subscription.amc_actual_normal_cleaning}</span>
                                                <span class="badge bg-success">
                                                    <input type="hidden" id="amc_remaining_normal_cleaning" name="amc_remaining_normal_cleaning" value="${amc_subscription.amc_remaining_normal_cleaning}">
                                                    Remaining: ${amc_subscription.amc_remaining_normal_cleaning}</span>
                                                <span class="badge bg-danger">Used: ${amc_subscription.amc_used_normal_cleaning}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="4">
                                                Deep Cleaning:
                                                <span class="badge bg-primary">Total: ${amc_subscription.amc_actual_deep_cleaning}</span>
                                                <span class="badge bg-success">
                                                     <input type="hidden" id="amc_remaining_deep_cleaning" name="amc_remaining_deep_cleaning" value="${amc_subscription.amc_remaining_deep_cleaning}">
                                                    Remaining: ${amc_subscription.amc_remaining_deep_cleaning}</span>
                                                <span class="badge bg-danger">Used: ${amc_subscription.amc_used_deep_cleaning}</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>`;
                    }else{
                        html += `<div class="card shadow-sm mt-3">
                            <div class="card-header bg-light">
                                <span class="badge bg-secondary">AMC Details</span>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-danger" role="alert">
                                    No AMC subscription is available for this serial number.
                                </div>
                            </div>
                        </div>`
                    }

                    // Inject the generated HTML into the DOM element
                    $('#div_warranty').html(html);
                } else {
                    $('#div_warranty').html('');
                }
            }
        });

    }


    function searchServicePartner(search) {
        if (search.length > 0) {
            $.ajax({
                url: "{{ route('ajax.searchServicePartner') }}",
                method: 'post',
                data: {
                    '_token': '{{ csrf_token() }}',
                    search: search,
                    is_active: 'active'
                },
                success: function (result) {
                    var content = '';
                    if (result.length > 0) {
                        content +=
                            `<div class="dropdown-menu show  servicepartner-dropdown select-md" aria-labelledby="dropdownMenuButton" style="width: 100%;">`;

                        $.each(result, (key, value) => {
                            content +=
                                `<a class="dropdown-item" href="javascript: void(0)" onclick="fetchServicePartner(${value.id},'${value.company_name}','${value.person_name}','${value.email}')">${value.person_name} |  ${value.company_name}</a>`;
                        })
                        content += `</div>`;
                        // $($this).parent().after(content);
                    } else {
                        content +=
                            `<div class="dropdown-menu show  servicepartner-dropdown select-md" aria-labelledby="dropdownMenuButton"><li class="dropdown-item">No service partner found</li></div>`;
                    }
                    $('#respDropServicePartner').html(content);
                }
            });
        } else {
            $('.servicepartner-dropdown').hide()
        }
    }

    function fetchServicePartner(i, c, p, e) {
        $('.servicepartner-dropdown').hide();

        $('#service_partner_id').val(i);
        $('#service_partner_email').val(e);
        $('#service_partner_company_name').val(c);
        $('#service_partner_person_name').val(p);
        $('#service_partner_name').val(p + ' | ' + c);


    }

    $("input:checkbox#is_repeated").change(function () {
        var ischecked = $(this).is(':checked');

        if (ischecked) {
            $('input').attr('readonly', 'readonly');
        } else {
            $('input').removeAttr('readonly');

        }
    });

    function copyPhone() {
        var checkbox = document.getElementById('sameAsPhone');
        var phoneField = document.getElementById('customer_phone');
        var alternatePhoneField = document.getElementById('customer_alternate_phone');

        if (checkbox.checked) {
            alternatePhoneField.value = phoneField.value;
            $('#customer_alternate_phone').attr('readonly', 'readonly');
        } else {
            alternatePhoneField.value = ''; // Clear the alternate phone field if unchecked
            $('#customer_alternate_phone').removeAttr('readonly');
        }
    }

    window.onload = function () {
        const pincode = document.getElementById('pincode').value;
        if (pincode) {
            getServicePartners(pincode);
        }
        const product_id = $('#product_id').val();
        var order_date = $('#order_date').val();
        if (product_id != '') {
            fetchProduct(product_id, order_date);
        }
    };



    function getServicePartners(evt) {
        $.ajax({
            url: "{{ route('ajax.get-service-partner-by-pincode') }}",
            method: 'post',
            data: {
                '_token': '{{ csrf_token() }}',
                pincode: evt,
                product_type: 'chimney',
            },
            success: function (result) {
                $('#service_partner_id').val('');
                $('#service_partner_name').val('');
                $('#service_partner_email').val('');
                if (result != '') {
                    var service_partner_id = result.service_partner_id;
                    var service_partner_email = result.service_partner.email;
                    var company_name = result.service_partner.company_name;
                    var person_name = result.service_partner.person_name;

                    $('#service_partner_id').val(service_partner_id);
                    $('#service_partner_name').val(person_name + ' | ' + company_name);
                    $('#service_partner_email').val(service_partner_email);
                    $('#service_partner_company_name').val(company_name);
                    $('#service_partner_person_name').val(person_name);
                }
            }
        });
    }
</script>
@endsection