<!DOCTYPE html>
<html>
<head>
	<title>Invoice No</title>
</head>
<body>
	<table border="1" style="width: 100%; margin: 0 auto; border-collapse: collapse;" cellpadding="0" cellspacing="0" id="invoice_table">
		<tr>
			<td colspan="10">
				<table border="1" style="width: 100%; border-collapse: collapse;" cellpadding="10" cellspacing="0">
					<tr>
						<td rowspan="2" style="font-size: 12px;">
							<p style="margin: 0">Sender</p>
							<p style="margin: 0;"><strong>AGNI</strong><br/>
						</td>
						<td style="font-size: 12px;">							
							<p style="margin: 0;">Invoice No:<br/>
								<strong>INV-12345</strong>
							</p>
						</td>
						<td style="font-size: 12px;">
							<p style="margin: 0;">Invoice Date:<br/><strong>{{$date}}</strong></p>
						</td>
					</tr>
					<tr>
						<td align="center" style="font-size: 12px;">
							<p style="margin: 0;">Order No: ORD-67890</p>
							<p style="margin: 0;">Order Date: 01-Jan-2024</p>
						</td>
						<td style="font-size: 12px;">
							&nbsp;
						</td>
					</tr>
					<tr>
						<td style="font-size: 12px;">
							<p style="margin: 0;">Dap Details:<br/>
								<strong>Unique Id</strong>
								<br/>
								<p style="margin: 0;">{{$data[0]['unique_id']}}</p>
								<strong>Product Name</strong>
								<p style="margin: 0;">{{$data[0]['item']}}</p>								
                            	
                            </p>
							
						</td>
						<td style="font-size: 12px;">
						<p style="margin: 0;">Receive From Showroom:<br/>
								<strong>{{$data[0]['return_branch']['name']}}</strong>
                            </p>
						</td>
						<td style="font-size: 12px;">
							<p style="margin: 0;">Bill To:<br/>
								<strong>{{$data[0]['customer_name']}}</strong>
								<br/>
								<p style="margin: 0;">Mobile : {{$data[0]['mobile']}}</p>
								<p style="margin: 0;">Alternative Number : {{$data[0]['alternate_no']}}</p>								
                            	Billing Address : {{$data[0]['address']}}
								
                            </p>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="10">
				<table class="table"  style="width: 100%; margin: 0 auto; border-collapse: collapse; border-top:0px solid transparent;" cellpadding="0" cellspacing="0">
					
				<tr>
					<th align="center" style="font-size: 12px;">Sl No.</th>
					<th align="center" style="font-size: 12px;">Product Name(Spare Parts)</th>
					<th align="center" style="font-size: 12px;">Total Quantity</th>
					<th align="center" style="font-size: 12px;">Item Amount</th>
					<th align="center" style="font-size: 12px;">Total Amount</th>
				</tr>
				@foreach($parts_data as $key=> $item)
				
				<tr>
					<td align="center" style="font-size: 12px;">{{$key+1}}</td>
					<td align="center" style="font-size: 12px;"><strong>{{$item['title']}}</strong></td>
					<td align="center" style="font-size: 12px;">1</td>			
					<td align="center" style="font-size: 12px;">Rs. {{number_format($item['final_amount'],2)}}</td>
					<td align="center" style="font-size: 12px;">Rs. {{number_format($item['final_amount'],2)}}</td>
				</tr>
				
				@endforeach
				<tr>
					<td></td>
					<td colspan="2" style="font-size: 12px;">&nbsp;</td>
					<td align="right" style="font-size: 12px;"><strong>Service Charge:</strong></td>
					<td align="center" style="font-size: 12px;">Rs. {{ number_format($data[0]['total_service_charge'], 2) }}</td>
				</tr>
				<tr>
					<td></td>
					<td colspan="2" style="font-size: 12px;">&nbsp;</td>
					<td align="right" style="font-size: 12px;"><strong>Discount Amount:</strong></td>
					<td align="center" style="font-size: 12px;">Rs. {{ number_format($data[0]['discount_amount'], 2) }}</td>
				</tr>
				<tr>
					<td></td>
					<td colspan="2" style="font-size: 12px;">&nbsp;</td>
					<td align="right" style="font-size: 12px;"><strong>Invoice Amount:</strong></td>
					<td align="center" style="font-size: 12px;">
						Rs. {{ number_format(($data[0]['total_amount'] + $data[0]['total_service_charge'] - $data[0]['discount_amount']), 2) }}</td>
				</tr>
				@php
				$total_bill = $data[0]['total_amount'] + $data[0]['total_service_charge'] - $data[0]['discount_amount'];
				@endphp
				<tr>
					<td colspan="5">
						<table border="0" style="width: 100%; border-collapse: collapse;" cellpadding="10" cellspacing="0">
							<tr>
								<td valign="top">
									<p style="margin: 0;">Amount Chargeable (in words)<br/><strong>INR {{getAmountAlphabetically($total_bill)}} Only</strong></p>
								</td>
								<td align="right" valign="top"><h4 style="margin: 0;">E. & O.E</h4></td>
							</tr>
							<tr>
								<td style="width: 50%;">
									<p style="margin: 0;"><u>Declaration</u></p>
									<p style="margin: 0;">1. All claims, if any, for shortages or damages must be reported to customer service on the day of delivery through the contact us page on the web store 2. All Disputes are subject to Maharashtra (27) jurisdiction only.</p>
								</td>
								<td align="center" style="width: 50%; border-top: 1px solid #000; border-left: 1px solid #000;">
									<h3>AGNI International</h3>
									<h3>Authorised Signatory</h3>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td colspan="5">
						<table border="0" style="width: 100%; border-collapse: collapse;" cellpadding="10" cellspacing="0">
							<tr>
								<td style="width: 49%;"><p style="margin: 0;"><strong>Bill By</strong></td>
								<td style="width: 2%;" align="center">:</td>
								<td style="width: 49%;"></td>
							</tr>
						</table>
					</td>
				</tr>
					
				</table>
			</td>
		</tr>

	</table>
</body>
</html>


