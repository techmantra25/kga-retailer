<!DOCTYPE html>
<html lang="en">
<head>
  <title>Invoice</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
</head>
<style>
    .table {
        
        border-collapse: collapse;
        font-family: "Montserrat", sans-serif;
    }
    .table td {
        padding: 10px;
    }
    .table, tr, td {
        font-family: "Montserrat", sans-serif;
    }
  
    .outer-part {
        border:1px solid #000;
        min-height: 90vh;
    }
    table{

        width:100%;
    }
    p{
        font-family: "Montserrat", sans-serif;
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
                            <td style="width: 30%; font-weight: bold; padding: 5px;">Receiver / Billed to</td>
                            <td style="width: 70%; padding: 5px;">Invoice No. : {{$invoice_no}}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold; padding: 5px;">Name</td>
                            <td style="padding: 5px;">: {{$sale->customer_name}}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold; padding: 5px;">Address</td>
                            <td style="padding: 5px;">: {{$sale->address}}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold; padding: 5px;">Contact No</td>
                            <td style="padding: 5px;">: {{$sale->mobile}}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold; padding: 5px;">State</td>
                            <td style="padding: 5px;">:</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold; padding: 5px;">GSTIN</td>
                            <td style="padding: 5px;">: </td>
                        </tr>
                    </table>
                </td>
                <td style="width: 50%; vertical-align: top; padding: 5px;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="font-weight: bold; padding: 5px;">Date</td>
                            <td style="padding: 5px;">: 07-05-2025</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold; padding: 5px;">Product</td>
                            <td style="padding: 5px;">: {{$sale->item}}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold; padding: 5px;">Product Sl. No.</td>
                            <td style="padding: 5px;">: {{$sale->serial}}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <tr>
                <td>
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="font-weight: bold; padding: 5px;">AMC SCHEME NAME : 
								 @if(!empty($planAssets))
                                 ( {{ implode(' + ', $planAssets) }} )
								@else
									N/A
								@endif
							</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold; padding: 5px;">Scheme Description : 
							({{ $normal_clean }} Normal Cleaning + 
							{{ $deep_clean }} Deep Cleaning)
							</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold; padding: 5px;">Time Prieod : 
							 {{$duration}} days
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
                <tr>
                    <td style="border: 1px solid #e0e0e0; padding: 8px;">AMC CHARGES</td>
                    <td style="border: 1px solid #e0e0e0; padding: 8px;">998715</td>
                    <td style="border: 1px solid #e0e0e0; padding: 8px; text-align: center;">1</td>
                    <td style="text-align: right;">{{ number_format($taxableValue, 2) }}</td>
					<td style="text-align: right;"></td>
					<td style="text-align: right;">{{ number_format($cgst, 2) }}</td>
					<td style="text-align: right;">{{ number_format($sgst, 2) }}</td>
					<td style="text-align: right;">{{ number_format($totalAmount, 2) }}</td>
                </tr>
                <tr style="font-weight: bold;">
                    <td style="border: 1px solid #e0e0e0; padding: 8px;" colspan="2">Total</td>
                    <td style="border: 1px solid #e0e0e0; padding: 8px; text-align: center;">1</td>
                    <td style="text-align: right;">{{ number_format($taxableValue, 2) }}</td>
					<td style="text-align: right;"></td>
					<td style="text-align: right;">{{ number_format($cgst, 2) }}</td>
					<td style="text-align: right;">{{ number_format($sgst, 2) }}</td>
					<td style="text-align: right;">{{ number_format($totalAmount, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <table style="width: 100%; border-collapse: collapse; margin-bottom: 35px; border: 1px solid #000000;">
            <tr>
                <td style="font-weight: bold; padding: 5px;">PAYMENT Ref. No. : XXXXXXXXXXXXXXXX</td>
                <td style="font-weight: bold; padding: 5px;">PAYMENT Date : XX/XX/XXXX</td>
            </tr>
        </table>

        <table style="vertical-align: bottom;">
            <tbody>
                <tr>
                    <td style="font-weight: bold; padding: 5px;">
                        <p>Rupees : Two Thousand Three Hundred Sixty Only</p>
                        <p>Remarks : </p>
                    </td>
                    <td style="font-weight: bold; padding: 5px; vertical-align: bottom; text-align: center;"> 

                        <p>Digital Signature
                            & Stamp</p>
                        <p>For AMMR TECHNOLOGY LLP</p>
                    </td>
                </tr>
            </tbody>
        </table>

    </div>

    <table class="outer-table">
        <tbody>
            <tr>
                <td style="vertical-align: top; font-size:13px; font-weight:600; color:#000;">User: <span>Sukanta_Brhs</span></td>
                <td style="text-align: center; vertical-align: top;">
                    <p style="font-size:14px; font-weight:600; margin-top: 0; margin-bottom: 3px;">Subject to Kolkata Jurisdiction </p>
                    <p style="font-size:14px; font-weight:600; margin-top: 2px; margin-bottom: 3px;">Computer Generated Invioce & Needs No Signature</p>
                </td>
            </tr>
        </tbody>
    </table>
</body>
</html>