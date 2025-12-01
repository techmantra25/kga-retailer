@extends('layouts.app')
@section('content')
@section('page', 'Product Dap-Quotation ')
<section>
    <ul class="breadcrumb_menu">        
        <li>DAP QUATITION</li>
    </ul>    

            <div class="col-auto">
                <div class="row g-3 align-items-center">  
                    <div class="col-auto">
                        <!-- <a onclick='printResultHandler()' class="btn btn-outline-primary select-md">Download PDF</a> -->
                        <a href="{{ route('dap-services.list') }}" class="btn btn-outline-danger select-md">Back</a>
                        
                    </div>                  
                </div>
            </div>  

            <div class="row">        
                <table class="table">
                    <thead>
                        <tr>
                            <th class="primary_column">Dap Unique Id</th>
                            <th>Item</th> 
                            <th width="60%">Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td rowspan="{{ count($parts_data) + 4 + ($discount_request_data ? 1 : 0) }}">{{ $data->unique_id }}</td>
                            <td rowspan="{{ count($parts_data) + 4 + ($discount_request_data ? 1 : 0) }}">{{ $data->item }}</td>
                            <td>
                                <table class="w-100 table table-striped table-hover p-2">
                                    <tr>
                                        <th width="80%">Spare Parts</th>
                                        <th class="text-end">Amount</th>
                                    </tr>
                                    @foreach ($parts_data as $key => $item)
                                    <tr>
                                        <td>{{ $item->title }}</td>
                                        <td class="text-end">₹ {{ number_format($item->final_amount, 2) }}</td>
                                    </tr>
                                    @endforeach
                                    <tr>
                                        <td>Amount:</td>
                                        <td id="total_amount" class="text-end">₹ {{ number_format($data->total_amount, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Service charge:</td>
                                        <td class="text-end">₹ {{ number_format($data->total_service_charge, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Discount:</td>
                                        <td class="text-end">₹ {{ number_format($data->discount_amount, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Total Amount:</td>
                                        <td  class="text-end">₹ {{ number_format($data->total_service_charge + $data->total_amount - $data->discount_amount, 2) }}</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        @if(($discount_request_data && $data->otp_verified == 0)|| $data->is_closed == 0)
                        <tr style="background-color: yellow;">
                            <form action="{{route('dap-services.dap-discount-amount-request-approved')}}" method="post"  onsubmit="return validateApprovalAmount()">
                                @csrf
                                <td>Discount request:</td>
                                <td><input type="text" name="approval_amount" id="approval_amount" value="{{ $discount_request_data ? number_format($discount_request_data->approval_amount, 2) : '' }}"></td>
                                <td>
                                   @if($discount_request_data && $discount_request_data->status == 0)
										<button type="submit">Approve ?</button>
									@elseif($discount_request_data && $discount_request_data->status != 0)
										<button type="submit">Approved</button>
									@else
										<button type="submit" disabled>No Request Found</button>
									@endif

                                </td>
                                <input type="hidden" name="dap_id" value="{{$discount_request_data->dap_id ?? ''}}">
                            </form>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>

         
</section>
<script>
  

    function printResultHandler() {
        //Get the HTML of div
        var print_header = '';
        var divElements = document.getElementById("print_div").innerHTML;
        var print_footer = '';

        //Get the HTML of whole page
        var oldPage = document.body.innerHTML;
        //Reset the page's HTML with div's HTML only
        document.body.innerHTML =
                "<html><head><title></title></head><body><font size='2'>" +
                divElements + "</font>" + print_footer + "</body>";
        //Print Page
        window.print();
        //Restore orignal HTML
        document.body.innerHTML = oldPage;
        //bindUnbind();
    }
    function validateApprovalAmount() {
    var approvalAmount = parseFloat(document.getElementById('approval_amount').value.replace(/,/g, ''));
    var totalAmount = parseFloat(document.getElementById('total_amount').innerText.replace(/[^0-9.-]+/g,""));
    
    if (approvalAmount >= totalAmount) {
        alert('Approval amount must be less than the total amount.');
        return false;
    }
    return true;
}
    
</script>
@endsection