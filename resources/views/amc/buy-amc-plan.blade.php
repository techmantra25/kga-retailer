@extends('layouts.app')
@section('content')
@section('page', 'Buy AMC Plan')
<section>
    <ul class="breadcrumb_menu d-flex justify-content-between">
    <li>Product Name: <strong>{{$product_amc_data->title}}</strong></li>
        <a href="{{ route('amc.amc-by-product',[$kga_sale_id , Crypt::encrypt($kga_sales_data->product_id)])  }}" class="btn btn-outline-danger select-md" title="Back">Back</a>
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
    @php
    $product_comprehensive_warranty = App\Models\ProductWarranty::where('dealer_type', 'khosla')->where('goods_id', $kga_sales_data->product_id)->where('warranty_type','comprehensive')->pluck('warranty_period')->first();
    @endphp

    <div class="card shadow-sm">
        <div class="container-fluid">
        <div class="card shadow-sm">
        <span>Amc unique serial number : <strong class="bg-danger"> {{ $amc_unique_number }}</strong></span>

        </div>
            <div class="row">
                <div class="col-sm-6 pt-2">
                    <div class="card shadow-sm">
                        <p class="small text-muted mb-1">
                            <span class="text-center"><strong>Customer Details</strong></span></br></br>
                            <span>Bill date: <strong>{{ date('j M Y, l', strtotime($kga_sales_data->bill_date)) }}</strong></span> <br/>
                            <span>Name: <strong>{{ $kga_sales_data->customer_name }}</strong></span> <br/>
                            <span>Mobile: <strong>{{ $kga_sales_data->mobile }}</strong></span> <br/>
                            <span>Phone: <strong>{{ $kga_sales_data->phone }}</strong></span> <br/>
                            <span>Address: <strong>{{ $kga_sales_data->address }}</strong></span> <br/>
                            <span>Pin: <strong>{{ $kga_sales_data->pincode }}</strong></span> <br/>
                        </p>
                    </div>
                </div>
                <div class="col-sm-6 pt-2">
                    <div class="card shadow-sm">
                        <p class="small text-muted mb-1">
                            <span class="text-center"><strong>Product Details</strong></span></br></br>
                            <span>Name: <strong>{{ $kga_sales_data->item }}</strong></span> <br/>
                            <span>Comprehensive Warranty: <strong>{{$product_comprehensive_warranty?$product_comprehensive_warranty . ' Months':'Yet Not Set' }}</strong></span> <br/>
                            <span>Serial: <strong>{{ $kga_sales_data->serial }}</strong></span> <br/>
                            <span>Barcode: <strong>{{ $kga_sales_data->barcode }}</strong></span> <br/>
                            <span>Branch: <strong>{{ $kga_sales_data->branch }}</strong></span> <br/>
                            <span>Comprehensive Warranty End Date: <strong>
                                @if ($product_comprehensive_warranty)
                                    {{ \Carbon\Carbon::parse($kga_sales_data->bill_date)
                                        ->addMonths($product_comprehensive_warranty)
                                        ->format('j M Y, l') }}
                                @else
                                    N/A
                                @endif
                            </strong></span> <br/>

                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card shadow-sm p-2">
        <div class="row">
            <div class="col">
                <form method="POST" action="{{ route('amc.send-payment-link') }}">
                    @csrf
                    <label>AMC Type:</label>
                    <input type="text" name="plan_name" value="{{$product_amc_data->AmcPlanData?$product_amc_data->AmcPlanData->name:'No Plan Found'}}" class="form-control" readonly>
                </div>
                <div class="col">
                    <label>Duration( In Days ):</label>
                    <input type="text" name="plan_duration" value="{{ $product_amc_data->duration }}" class="form-control" readonly>
                </div>
                <div class="col">
                    <label>Actual Amount:</label>
                    <input type="number" name="actual_amount" id="actual_amount" value="{{$product_amc_data->amount}}" class="form-control" readonly>
                </div>
                <div class="col">
                    <label>Purchase Amount:</label>
                    <input type="number" name="purchase_amount" value="{{$product_amc_data->amount}}" class="form-control" id="purchase_amount" readonly>
                </div>
                <!-- Discount Type Toggle -->
                <div class="col">
                    <label>Discount Type:</label>
                    <select id="discount_type" name="discount_type" class="form-control" onchange="updateDiscountLabel({{$amc_discount}})">
                        <option value="percent">%</option>
                        <option value="flat">Flat</option>
                    </select>
                </div>

                <!-- Discount Input -->
                <div class="col">
                    <label id="discount_label">Discount (Max {{$amc_discount}}%):</label>
                    <input
                        type="number"
                        name="discount"
                        value="0"
                        class="form-control"
                        id="discount"
                        min="0"
                        step="0.0001"
                        oninput="applyDiscount({{$amc_discount}})"
                    >
                    <small id="max_flat_hint" class="text-muted d-none"></small>
                </div>

                <div class="col">
                    <label>Mobile:</label>
                    <input type="number" name="phone"  class="form-control" id="phone" value="{{$kga_sales_data->mobile}}" oninput="validatePhone()">
                </div>
            </div>
            
            <input type="hidden" name="customer_name" value="{{$kga_sales_data->customer_name}}"/>
            <input type="hidden" name="kga_sales_id" value="{{$kga_sales_data->id}}"/>
            <input type="hidden" name="product_id" value="{{$kga_sales_data->product_id}}"/>
            <input type="hidden" name="serial" value="{{$kga_sales_data->serial}}"/>
            <input type="hidden" name="amc_id" value="{{$id}}"/>
            <input type="hidden" name="product_comprehensive_warranty" value="{{$product_comprehensive_warranty}}"/>
            <input type="hidden" name="amc_unique_number" value="{{$amc_unique_number}}"/>
            
            <div class="text-end pt-3">
                <button type="submit" class="btn btn-outline-primary select-md" id="send_payment">Send payment link</button>
            </div>
        </form>
    </div>
   
    @if(count($before_amc_subscription_data)>0)
    <div class="card shadow-sm">
        <table class="table">
            <thead>
                <tr>
                    <th class="sr_no">#</th>
                    <th class="primary_column">Kga Sales Id</th>
                    <th>Amc Unique Id</th>
                    <th>Serial</th>
                    <th>Link</th>
                    <th>Actual Amount</th>
                    <th>Discount (%)</th>
                    <th>Purchase Amount</th>
                    <th>Payment Time</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($before_amc_subscription_data as $item)
                <tr>
                    <td class="sr_no">{{ $loop->iteration }}</td>
                    <td class="sr_no">{{$item->kga_sales_id}}</td>
                    <td class="sr_no">{{$item->amc_unique_number}}</td>
                    <td class="sr_no">{{$item->serial}}</td>
                    <!-- <td class="sr_no">
                    <span id="link-{{ $loop->iteration }}">{{ $item->AmcLinkData ? $item->AmcLinkData->link : "" }}</span>
                    <button class="btn btn-outline-primary select-md" onclick="copyText('link-{{ $loop->iteration }}')">Copy link</button>
                    </td> -->
                    <td class="sr_no">
                        <button title="Copy this link" class="btn btn-outline-primary select-md" onclick="copyText('{{ $item->AmcLinkData ? $item->AmcLinkData->link : '' }}')">Copy link</button>
                    </td>
                    <td class="sr_no">{{number_format($item->actual_amount,2)}}</td>
                    <td class="sr_no">{{$item->discount}}</td>
                    <td class="sr_no">{{number_format($item->purchase_amount,2)}}</td>
                    <td class="sr_no">{{$item->payment_time}}</td>
                    <td class="sr_no">
                        {!! $item->status == 0 
                            ? '<button class="btn btn-outline-warning select-md">Pending</button>' 
                            : '<button class="btn btn-outline-success select-md">Success</button>' !!}
                    </td>
                   <!--  <td>
                        @if($item->status == 1 )
                        <a href="" class="btn btn-outline-primary select-md">Go</a>
                        @endif
                    </td> -->
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
    </div>
    @endif

    
   
</section>
<script>
    // function copyText(elementId) {
    //     const textToCopy = document.getElementById(elementId).textContent;
    //     navigator.clipboard.writeText(textToCopy).then(() => {
    //         alert("Copied to clipboard: " + textToCopy);
    //     }).catch(err => {
    //         console.error("Failed to copy text: ", err);
    //     });
    // }

    function copyText(link) {
        if (link) {
            navigator.clipboard.writeText(link).then(() => {
                alert("Link copied.");
            }).catch(err => {
                console.error("Failed to copy text: ", err);
            });
        } else {
            alert("No link available to copy.");
        }
    }

function validatePhone() {
    let phoneInput = document.getElementById('phone');
    let sendPayment = document.getElementById('send_payment');
    
    // Check if the input is exactly 10 digits and contains only numbers
    if (phoneInput.value.length === 10 && /^[0-9]+$/.test(phoneInput.value)) {
        sendPayment.style.display = "inline-block"; // Show the button
    } else {
        sendPayment.style.display = "none"; // Hide the button
    }
}

function updateDiscountLabel(maxDiscount) {
    const discountType = document.getElementById('discount_type').value;
    const actualAmount = parseFloat(document.getElementById('actual_amount').value);
    const label = document.getElementById('discount_label');
    const hint = document.getElementById('max_flat_hint');

    if (discountType === 'flat') {
        const maxFlat = (actualAmount * maxDiscount / 100).toFixed(2);
        label.textContent = `Discount (Max Flat ₹${maxFlat}):`;
        hint.textContent = `Max allowed flat discount: ₹${maxFlat}`;
        hint.classList.remove('d-none');
    } else {
        label.textContent = `Discount (Max ${maxDiscount}%):`;
        hint.classList.add('d-none');
    }

    // Reset the discount and recalculate
    document.getElementById('discount').value = 0;
    applyDiscount(maxDiscount);
}


function applyDiscount(maxDiscount) {
    const discountInput = document.getElementById('discount');
    const amountInput = document.getElementById('purchase_amount');
    const actualAmount = parseFloat(document.getElementById('actual_amount').value);
    const sendPayment = document.getElementById('send_payment');
    const discountType = document.getElementById('discount_type').value;

    const discountValue = parseFloat(discountInput.value);

    let isValid = false;
    let discountedAmount = actualAmount;

    if (!isNaN(discountValue) && discountValue >= 0) {
        if (discountType === 'percent') {
            if (discountValue <= maxDiscount) {
                discountedAmount = actualAmount - (actualAmount * (discountValue / 100));
                isValid = true;
            }
        } else if (discountType === 'flat') {
            const maxFlat = actualAmount * (maxDiscount / 100);
            if (discountValue <= maxFlat) {
                discountedAmount = actualAmount - discountValue;
                isValid = true;
            }
        }
    }

    if (isValid) {
        amountInput.value = discountedAmount.toFixed(2);
        discountInput.classList.remove("is-invalid");
        discountInput.classList.add("is-valid");
        sendPayment.style.display = "inline-block";
    } else {
        amountInput.value = actualAmount.toFixed(2);
        discountInput.classList.remove("is-valid");
        discountInput.classList.add("is-invalid");
        sendPayment.style.display = "none";
    }
}

</script>  
@endsection 