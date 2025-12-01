<!DOCTYPE html>
<html lang="en">
<head>
  <title>Invoice</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  
</head>
<style>
    .table {
        
        border-collapse: collapse;
    }
    .table td {
        padding: 10px;
    }
    .table, tr, td {
    }
  
    .outer-part {
        border:1px solid #000;
    }
    table{

        width:100%;
    }
    p{
        margin-top: 0;
        margin-bottom: 2px;
    }
</style>

<body>
	
    <div class="outer-part">
        <table class="table" style="margin-bottom: 45px;">
            <tbody>
                <tr>
                    <td style="width:60%; vertical-align: top;">
                        <h2 style="font-size:20px; font-weight:600; margin-top: 0; margin-bottom:3px;">AMMR TECHNOLOGY LLP</h2>
                        <p style="font-size: 15px; font-weight: 600;">DIAMOND HERITAGE 16, STRAND ROAD 10TH FLOOR, ROOM NO - #N 1015</p>
                        <p style="font-size: 15px; font-weight: 600;">Phone :  <span>+91-6291117317</span></p>
                        <p style="font-size: 15px; font-weight: 600;">E-mail :  <span>kgaelectronics@gmail</span></p>
                        <p style="font-size: 15px; font-weight: 600;">Website :  <span>www.kgaelectronics.com</span></p>
                    </td>
                    <td style="vertical-align: middle;">
                        <p style="font-size: 15px; font-weight: 600; margin-bottom: 4px;">GSTIN :  <span>19ABOFA0730E1ZO</span></p>
                        <p style="font-size: 15px; font-weight: 600;">PAN No :  <span>ABOFA0730E</span></p>
                    </td>
                </tr>

            </tbody>
        </table>

        <table>
            <tr>
                <td style="text-align:center;" colspan="2"><h2 style="margin-top: 0; margin-bottom: 4px;">TAX INVOICE</h2></td>
            </tr>
        </table>

        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px; border-top:1px solid #000; border-bottom:1px solid #000;">
            <tr>
                <td style="width: 50%; vertical-align: top; padding: 5px; border-right:1px solid #000;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td colspan="2" style="width: 100%; font-weight: bold; padding: 5px;">Receiver / Billed to</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold; padding: 5px;">Name</td>
                            <td style="padding: 5px;">: {{$subscription->SalesData ? $subscription->SalesData->customer_name : ""}}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold; padding: 5px;">Address</td>
                            <td style="padding: 5px;">: {{$subscription->SalesData ? $subscription->SalesData->address : ""}}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold; padding: 5px;">Contact No</td>
                            <td style="padding: 5px;">: {{$subscription->SalesData ? $subscription->SalesData->mobile : ""}}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold; padding: 5px;">State</td>
                            <td style="padding: 5px;">:</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold; padding: 5px;">GSTIN</td>
                            <td style="padding: 5px;">:</td>
                        </tr>
                    </table>
                </td>
                <td style="width: 50%; vertical-align: top; padding: 5px;">
                    <table style="width: 100%; border-collapse: collapse;">
						<tr>
                            <td style="font-weight: bold; padding: 5px;">Invoice No. :</td>
                            <!-- <td style="padding: 5px;">: {{$subscription->servicePayments->first()->invoice_id}}</td> -->
							<td style="padding: 5px;">
								 : {{
									preg_replace('/(.*\/)(0{0,3})(\d+)/', '$1$3', $subscription->servicePayments->first()->invoice_id)
								}} 
								
							</td>

                        </tr>
                        <tr>
                            <td style="font-weight: bold; padding: 5px;">Date</td>
                            <td style="padding: 5px;">: {{\Carbon\Carbon::parse($subscription->purchase_date)->format('d-m-Y')}}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold; padding: 5px;">Product</td>
                            <td style="padding: 5px;">: {{$subscription->SalesData->item ?? ''}}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold; padding: 5px;">Product Sl. No.</td>
                            <td style="padding: 5px;">: {{$subscription->serial}}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
			@php
			  $amc_plan = $subscription->AmcData->AmcPlanData ?? null;
			  $durationData = $amc_plan->AmcDurationData->first() ?? null;
			@endphp
            <tr>
                <td>
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="font-weight: bold; padding: 5px;">AMC SCHEME NAME : {{$amc_plan->name}} </td>
                        </tr>
                        <tr>
                            <td style="padding: 5px;">
								<strong>Scheme Description :</strong>
								<div style="margin-left: 15px;">
								  {{ implode(' + ', $amc_plan->plan_asset_names) }}
								</div>
								@php
									$durationData = $amc_plan->AmcDurationData
										->where('duration', optional($subscription->AmcData)->duration)
										->first();
								@endphp
								<div style="margin-left: 15px;">
									@if($durationData)
										@php
											$description = [];
											if($durationData->deep_cleaning) {
												$description[] = $durationData->deep_cleaning . ' Deep Cleaning';
											}
											if($durationData->normal_cleaning) {
												$description[] = $durationData->normal_cleaning . ' Normal Cleaning';
											}
										@endphp
										({{ implode(' + ', $description) }})
									@else
										N/A
									@endif
								</div>
							</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold; padding: 5px;">
                                Time Period : 
                                @if($subscription->AmcData && $subscription->AmcData->duration)
                                    {{$subscription->AmcData->duration}} Days
                                    @if($subscription->amc_start_date && $subscription->amc_end_date)
                                        ({{ \Carbon\Carbon::parse($subscription->amc_start_date)->format('d M Y') }}
                                        - 
                                        {{ \Carbon\Carbon::parse($subscription->amc_end_date)->format('d M Y') }})
                                    @endif
                                @else
                                    N/A
                                @endif
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
		

        <table style="width: 100%; border-collapse: collapse; margin-bottom: 35px; border: 1px solid #e0e0e0;">
            <thead>
                <tr style="background-color: #f5f5f5;">
                    <th style="border: 1px solid #e0e0e0; padding: 8px; text-align: left;">Description</th>
                    <th style="border: 1px solid #e0e0e0; padding: 8px; text-align: left;">HSN/SAC</th>
                    <th style="border: 1px solid #e0e0e0; padding: 8px; text-align: center;">Qty.</th>
                    <th style="border: 1px solid #e0e0e0; padding: 8px; text-align: right;">Taxable Value</th>
                    <th style="border: 1px solid #e0e0e0; padding: 8px; text-align: right;">IGST</th>
                    <th style="border: 1px solid #e0e0e0; padding: 8px; text-align: right;">CGST</th>
                    <th style="border: 1px solid #e0e0e0; padding: 8px; text-align: right;">SGST</th>
                    <th style="border: 1px solid #e0e0e0; padding: 8px; text-align: right;">Amount</th>
                </tr>
            </thead>
            <tbody>
				@php
					$gst_value = $subscription->AmcData?->productData?->gst ?? 18; // example 18%
					$hsn_code = $subscription->AmcData?->productData?->hsn_code;
					// $actual_amount = $subscription->actual_amount;
					$actual_amount = $subscription->purchase_amount;
					
					// Reverse calculation from actual_amount
					$taxable_value = $actual_amount / (1 + ($gst_value/100));
					$cgst = $taxable_value * ($gst_value/2)/100;
					$sgst = $taxable_value * ($gst_value/2)/100;
				
				   // Total Calculations (for single AMC item) 
				    $total_taxable = $taxable_value;
					$total_cgst = $cgst;
					$total_sgst = $sgst;
					$grand_total = $actual_amount;
		        @endphp
                <tr>
                    <td style="border: 1px solid #e0e0e0; padding: 8px;">AMC CHARGES</td>
                    <td style="border: 1px solid #e0e0e0; padding: 8px;">{{$hsn_code}}</td>
                    <td style="border: 1px solid #e0e0e0; padding: 8px; text-align: center;">1</td>
                    <td style="border: 1px solid #e0e0e0; padding: 8px; text-align: right;">{{number_format($taxable_value,2)}}</td>
                    <td style="border: 1px solid #e0e0e0; padding: 8px; text-align: right;"></td>
                    <td style="border: 1px solid #e0e0e0; padding: 8px; text-align: right;">{{number_format($cgst,2)}}</td>
                    <td style="border: 1px solid #e0e0e0; padding: 8px; text-align: right;">{{number_format($sgst,2)}}</td>
                    <td style="border: 1px solid #e0e0e0; padding: 8px; text-align: right;">{{number_format($actual_amount,2)}}</td>
                </tr>
                <tr style="font-weight: bold;">
                    <td style="border: 1px solid #e0e0e0; padding: 8px;" colspan="2">Total</td>
                    <td style="border: 1px solid #e0e0e0; padding: 8px; text-align: center;">1</td>
                    <td style="border: 1px solid #e0e0e0; padding: 8px; text-align: right;">{{number_format($total_taxable,2)}}</td>
                    <td style="border: 1px solid #e0e0e0; padding: 8px; text-align: right;"></td>
                    <td style="border: 1px solid #e0e0e0; padding: 8px; text-align: right;">{{number_format($total_cgst,2)}}</td>
                    <td style="border: 1px solid #e0e0e0; padding: 8px; text-align: right;">{{number_format($total_sgst,2)}}</td>
                    <td style="border: 1px solid #e0e0e0; padding: 8px; text-align: right;">{{number_format($grand_total,2)}}</td>
                </tr>
            </tbody>
        </table>

        <table style="width: 100%; border-collapse: collapse; margin-bottom: 35px; border: 1px solid #000000;">
            <tr>
                <td style="font-weight: bold; padding: 5px;">PAYMENT Ref. No. : {{$subscription->servicePayments ? $subscription->servicePayments->first()->payment_id : 'N/A'}}</td>
                <td style="font-weight: bold; padding: 5px;">PAYMENT Date : {{\Carbon\Carbon::parse($subscription->purchase_date)->format('d/m/Y')}}</td>
            </tr>
        </table>

        <table style="vertical-align: bottom;">
            <tbody>
				@php
				  $ruppes = amountInWords($grand_total);
				@endphp
                <tr>
                    <td style="font-weight: bold; padding: 5px;">
                        <p>Rupees : {{ucwords($ruppes)}} Only</p>
                        <p>Remarks : </p>
                    </td>
                    <td style="font-weight: bold; padding: 5px; vertical-align: bottom; text-align: center;"> 

                       
                        <p>For AMMR TECHNOLOGY LLP</p>
                    </td>
                </tr>
            </tbody>
        </table>

    </div>

    <table class="outer-table">
        <tbody>
            <tr>
                <td style="vertical-align: top; font-size:12px; font-weight:600; color:#000; width:15% ">User: <span>{{$subscription->Sell_by ? $subscription->Sell_by->name : "" }}</span></td>
                <td style="text-align: center; vertical-align: top;">
                    <p style="font-size:14px; font-weight:600; margin-top: 0; margin-bottom: 3px;">Subject to Kolkata Jurisdiction </p>
                    <p style="font-size:14px; font-weight:600; margin-top: 2px; margin-bottom: 3px;">Computer Generated Invioce & Needs No Signature</p>
                </td>
            </tr>
        </tbody>
    </table>
</body>
</html>