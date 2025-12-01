@extends('servicepartnerweb.layouts.app')
@section('content')
@section('page', 'Buy AMC Plan')
<section>
    <ul class="breadcrumb_menu d-flex justify-content-between">
    <li>Product Name: <strong>{{$product_amc_data->title}}</strong></li>
        <a href="{{ route('servicepartnerweb.amc.amc-by-product',[$kga_sale_id , Crypt::encrypt($kga_sales_data->product_id)])  }}" class="btn btn-outline-danger select-md" title="Back">Back</a>
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
                            <span>Comprehensive Warranty End date: <strong>
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
    <div class="card shadow-sm">
        <div class="row">
            <div class="col">
                <form method="POST" action="{{ route('servicepartnerweb.amc.send-payment-link') }}">
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
                <div class="col">
                    <label>Discount Request:</label></br>

                    <input type="radio" name="discount_request" value="yes" id="discount_yes">
                    <label for="discount_yes">Yes</label>
                    
                    <input type="radio" name="discount_request" value="no" id="discount_no" checked>
                    <label for="discount_no">No</label>

                    <input type="number" class="form-control" placeholder="Put your discount request in (%)" name="discount_request_percentage"  id="discount_request_percentage" style="display: none;">
                    <span id="discount_error" style="color: red; display: none;">Discount percentage must be between 1 and 99.</span>
                </div>
                <div class="col">
                    <label>Mobile:</label>
                    <input type="number" name="phone"  class="form-control" id="phone" value="{{$kga_sales_data->mobile}}" oninput="validatePhone()">
                </div>
            </div>
            
            <input type="hidden" id="customer_name" name="customer_name" value="{{$kga_sales_data->customer_name}}"/>
            <input type="hidden" id="kga_sales_id" name="kga_sales_id" value="{{$kga_sales_data->id}}"/>
            <input type="hidden" id="product_id" name="product_id" value="{{$kga_sales_data->product_id}}"/>
            <input type="hidden" id="serial" name="serial" value="{{$kga_sales_data->serial}}"/>
            <input type="hidden" id="amc_id" name="amc_id" value="{{$id}}"/>
            <input type="hidden" id="product_comprehensive_warranty" name="product_comprehensive_warranty" value="{{$product_comprehensive_warranty}}"/>
            <input type="hidden" id="amc_unique_number" name="amc_unique_number" value="{{$amc_unique_number}}"/>
            
            <div class="text-end pt-4 mb-3">
                <button type="button" class="btn btn-outline-primary select-md" id="approval_for_discount" style="display: none;">Approval for discount</button>
                <button type="submit" class="btn btn-outline-primary select-md" id="send_payment">Send payment link</button>
            </div>
        </form>


        <!-- Separate form for Approval for Discount -->
        <form id="discountForm" method="POST" action="{{ route('servicepartnerweb.amc.discount-request') }}" style="display: none;">
            @csrf
            <input type="hidden" id="customer_name" name="customer_name" value="{{$kga_sales_data->customer_name}}"/>
            <input type="hidden" id="kga_sales_id" name="kga_sales_id" value="{{$kga_sales_data->id}}"/>
            <input type="hidden" id="product_id" name="product_id" value="{{$kga_sales_data->product_id}}"/>
            <input type="hidden" id="serial" name="serial" value="{{$kga_sales_data->serial}}"/>
            <input type="hidden" id="amc_id" name="amc_id" value="{{$id}}"/>
            <input type="hidden" id="product_comprehensive_warranty" name="product_comprehensive_warranty" value="{{$product_comprehensive_warranty}}"/>
            <input type="hidden" id="amc_unique_number" name="amc_unique_number" value="{{$amc_unique_number}}"/>
            <input type="hidden" name="discount_request_percentage" id="discount_request_percentage_hidden"/>
            <input type="hidden" name="actual_amount" id="actual_amount_hidden"/>
            <input type="hidden" name="purchase_amount" id="purchase_amount_hidden"/>
        </form>
    </div>

            <!-- Modal -->
        <!-- <div class="modal fade" id="discountRequest" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Discount Request in (%) </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="number" class="form-control" name="discount_request" placeholder="Put your discount request in percentage (%)"/>
                <p class="text-muted">
                    Note: Your discount request will be reviewed and requires approval from the admin. Once approved, the final discount amount will be provided to you. 
                    Thank you for your patience.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
            </div>
        </div>
        </div> -->



   
    @if(count($before_amc_subscription_data)>0)
    <div class="card shadow-sm">
        <table class="table">
            <thead>
                <tr>
                    <th class="sr_no">#</th>
                    <th class="primary_column">Kga sales id</th>
                    <th>Amc unique id</th>
                    <th>Serial</th>
                    <th>Link</th>
                    <th>Actual Amount</th>
                    <th>Discount (%)</th>
                    <th>Purchase Amount</th>
                    <th>Payment time</th>
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
                    <td>
                        @if($item->status == 1 )
                        <a href="" class="btn btn-outline-primary select-md">Go</a>
                        @endif
                    </td>
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
    document.addEventListener('DOMContentLoaded', function () {
        const discountYes = document.getElementById('discount_yes');
        const discountNo = document.getElementById('discount_no');
        const discountPercentage = document.getElementById('discount_request_percentage');
        const approvalForDiscount = document.getElementById('approval_for_discount');
        const sendPayment = document.getElementById('send_payment');
        const actualAmount = document.getElementById('actual_amount');
        const purchaseAmount = document.getElementById('purchase_amount');

        // Event listener for discount request radio buttons
        discountYes.addEventListener('change', function () {
            if (this.checked) {
                discountPercentage.style.display = 'block';
                approvalForDiscount.style.display = 'inline-block';
                sendPayment.style.display = 'none';
            }
        });

        discountNo.addEventListener('change', function () {
            if (this.checked) {
                discountPercentage.style.display = 'none';
                approvalForDiscount.style.display = 'none';
                sendPayment.style.display = 'inline-block';
                // Reset purchase amount to actual amount
                purchaseAmount.value = actualAmount.value;
            }
        });

        discountPercentage.addEventListener('input', function () {
            let percentage = parseFloat(this.value);
            const discountError = document.getElementById('discount_error');

            if (percentage >= 1 && percentage <= 99) {
                discountError.style.display = 'none';
                let actualValue = parseFloat(actualAmount.value);
                let discountedValue = actualValue - (actualValue * (percentage / 100));
                purchaseAmount.value = discountedValue.toFixed(2);
                
            } else {
                discountError.style.display = 'block';
                this.value = ''; // Clear invalid input
                purchaseAmount.value = actualAmount.value;
            }
        });

        approvalForDiscount.addEventListener('click', function () {
            const discountRequestPercentage = discountPercentage.value;
            const actualAmountValue = actualAmount.value;
            const purchaseAmountValue = purchaseAmount.value;

            // Populate the hidden inputs in the discountForm
            discountForm.querySelector('[name="discount_request_percentage"]').value = discountRequestPercentage;
            discountForm.querySelector('[name="actual_amount"]').value = actualAmountValue;
            discountForm.querySelector('[name="purchase_amount"]').value = purchaseAmountValue;
            discountForm.submit();
        });

    });




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


function applyDiscount(maxDiscount) {
    const discountInput = document.getElementById('discount');
    const amountInput = document.getElementById('purchase_amount');
    const actualAmount = parseFloat(document.getElementById('actual_amount').value);
    let sendPayment = document.getElementById('send_payment');


    const discountValue = parseFloat(discountInput.value);

    if (!isNaN(discountValue) && discountValue >= 0 && discountValue <= maxDiscount) {
        const discountedAmount = actualAmount - (actualAmount * (discountValue / 100));
        amountInput.value = discountedAmount.toFixed(2); // Update purchase amount field
        discountInput.classList.remove("is-invalid");
        discountInput.classList.add("is-valid");
        sendPayment.style.display = "inline-block"; // Show the button

    } else {
        discountInput.classList.remove("is-valid");
        discountInput.classList.add("is-invalid");
        amountInput.value = actualAmount.toFixed(2); // Reset to original amount
        sendPayment.style.display = "none"; // Hide the button


    }
}

</script>  
@endsection 