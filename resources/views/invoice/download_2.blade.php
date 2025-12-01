<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KGA | {{$invoice->invoice_no}}</title>
</head>
<body style="font-family: Arial, sans-serif; margin: 0; padding: 0; display: flex; justify-content: center; align-items: center; background-color: #ffffff;">
<div style="max-width: 700px;">
    <div style="max-width: 700px; margin: 20px auto; border: 1px solid #000; padding: 0px; background-color: #ffffff;">
        <!-- Header Section -->
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 0px; font-size: 12px;">
            <tr>
                <!-- Logo and Company Details -->
                <td style="width: 12%; text-align: left; vertical-align: top; padding: 5px; ">
                    <img src="{{ asset('assets/images/kga_logo_inv.png') }}" alt="Logo" style="max-width: 80px;">
                </td>
                <td style="width: 50%; text-align: left; vertical-align: top; padding: 5px; line-height: 1.6;">
                    <strong style="font-size: 14px; ">AMMR TECHNOLOGY LLP</strong><br>
                    W-1015, 10TH FLOOR, DIAMOND HERITAGE,<br>
                    16, STRAND ROAD, KOLKATA-700001, WEST BENGAL<br>
                    <strong>GSTIN/UIN:</strong> 19AADFA0703E1ZO<br>
                    <strong>State Name:</strong> West Bengal, <strong>Code:</strong> 19<br>
                    <strong>Email:</strong> kgaelectronics@gmail.com
                </td>
                <td style="border-left:#000 1px solid">
                    <table style="width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 12px; padding-bottom: 15px;">
                        <tr>
                            <td style="padding-left: 10px;"><span style="display:block">Invoice No:</span> {{$invoice->invoice_no}}</td>
                            <td><span style="display:block">Invoice Date:
                            </span> {{ date('d-M-Y',strtotime($invoice->created_at)) }}</td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <table style="width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 12px;">
                                <tr>
                                    <td style="text-align:left;width:100%;border-top:1px solid #000;padding-top:15px; ">
                                        <div>
                                            <span style="">
                                            Order No: 
                                            </span> {{$invoice->sales_order->order_no}}
                                        </div>
                                        <div>
                                            <span style="">
                                            Order Date:
                                            </span>{{date('d-M-Y', strtotime($invoice->sales_order->created_at))}}
                                        </div>
                                        
                                    </td>
                                </tr>
                                </table>
                            </td>
                        </tr>

                    </table>
                </td>
            </tr>
        </table>

        <!-- Bill To / Ship To Section -->
        <table style="width: 100%; border-collapse: collapse; margin-top:0px; font-size: 12px;">
            <tr>
            @if (!empty($invoice->dealer))
                <td style="width: 50%; border: 1px solid #000; padding: 8px; vertical-align: top; line-height: 1.6;border-left: 0;border-bottom: 0;">
                    <strong style="">Bill To:</strong><br>
                    {{$invoice->dealer->name}}<br>
                    <strong style="">Phone:</strong> {{$invoice->dealer->phone}}<br>
                    <strong style="">GSTIN/UIN:</strong> {{$invoice->dealer->gst_no}}<br>
                    <strong style="">PAN/IT No:</strong> {{$invoice->dealer->pan_no}}<br>
                    <span style="">Address:</span> {{$invoice->dealer->address}}
                </td>
                <td style="width: 50%; border: 1px solid #000; padding: 8px; vertical-align: top; line-height: 1.6;border-right: 0;    border-bottom: 0;">
                    <strong style="">Ship To:</strong><br>
                    {{$invoice->dealer->name}}<br>
                    <strong style="">Phone:</strong> {{$invoice->dealer->phone}}<br>
                    <strong style="">GSTIN/UIN:</strong> {{$invoice->dealer->gst_no}}<br>
                    <strong style="">PAN/IT No:</strong> {{$invoice->dealer->pan_no}}<br>
                    <span style="">Address:</span> {{$invoice->dealer->address}}
                </td>
            @else
                <td style="width: 50%; border: 1px solid #000; padding: 8px; vertical-align: top; line-height: 1.6;border-left: 0;border-bottom: 0;">
                    <div> <span style="">Bill To: </span>
                        <strong>{{$invoice->service_partner->company_name}}</strong>
                        <p style="margin: 0;"><span style="">Phone :</span> {{$invoice->service_partner->phone}}</p>	
                        <p style="margin: 0;">
                        <span style="">Address:</span>
                            {{$invoice->service_partner->address}}
                        </p>
                    </div>							
                </td>
                <td style="width: 50%; border: 1px solid #000; padding: 8px; vertical-align: top; line-height: 1.6;border-right: 0;    border-bottom: 0;">
                    <div style="margin: 0;"><span style="">Ship To: </span>
                        <strong>{{$invoice->service_partner->company_name}}</strong>
                        <p style="margin: 0;"><span style="">Phone :</span> {{$invoice->service_partner->phone}}</p>
                        <p style="margin: 0;">
                            <span style="">Address:</span>
                            {{$invoice->service_partner->address}}
                        </p>
                    </div>							
                </td>
            @endif
            </tr>
        </table>


        <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
            <thead>
                <tr>
                    <th style="border: 1px solid #000; border-left: 0; padding: 8px; text-align: center; background-color: #f2f2f2;">Sl No.</th>
                    <th style="border: 1px solid #000; border-left: 0; padding: 8px; text-align: center; background-color: #f2f2f2;">Descriptions of Goods</th>
                    <th style="border: 1px solid #000; border-left: 0; padding: 8px; text-align: center; background-color: #f2f2f2;">Total Pcs</th>
                    <th style="border: 1px solid #000; border-left: 0; padding: 8px; text-align: center; background-color: #f2f2f2;">Price per Piece (Exc. Tax)</th>
                    <th style="border: 1px solid #000; border-left: 0; padding: 8px; text-align: center; background-color: #f2f2f2;border-left: 0">Total Amount (Exc. Tax)</th>
                    <th style="border: 1px solid #000; border-left: 0; padding: 8px; text-align: center; background-color: #f2f2f2;">HSN Code</th>
                    <th style="border: 1px solid #000; border-left: 0; padding: 8px; text-align: center; background-color: #f2f2f2;">GST</th>
                    <th style="border: 1px solid #000; border-left: 0; padding: 8px; text-align: center; background-color: #f2f2f2;border-right: 0">Total Amount (Inc. Tax)</th>
                </tr>
            </thead>
            <tbody>
                    @php
                        $item_details = json_decode($invoice->item_details);
						$total_invoice_price = 0;
						$total_pcs = 0;
                    @endphp
                    @forelse ($item_details as $key => $items)
                        @php      
                            $exc_tax_pro_price = ($items->price - $items->tax);
                            $count_exc_tax_pro_price = ($items->quantity * $exc_tax_pro_price);  
                            
                            $getGSTAmount = getGSTAmount($items->price,$items->tax);
                            $gst_amount = $getGSTAmount['gst_amount'];
                            $net_price = $getGSTAmount['net_price'];
                            $count_price = ($items->quantity * $net_price);
                            // echo $net_price;
                            $total_invoice_price += $items->total_price;
                            $total_pcs += $items->quantity;
                        @endphp
                        <tr>
                            <td style="border: 1px solid #000; border-left: 0; padding: 8px; text-align: center;">{{$key}}</td>
                            <td style="border: 1px solid #000; padding: 8px; text-align: center;">{{ $items->product_title }}</td>
                            <td style="border: 1px solid #000; padding: 8px; text-align: center;">{{ $items->quantity }}</td>
                            <td style="border: 1px solid #000; padding: 8px; text-align: center;">Rs. {{ $items->price_exc_tax }}</td>
                            <td style="border: 1px solid #000; padding: 8px; text-align: center;">Rs. {{ $items->total_price_exc_tax }}</td>
                            <td style="border: 1px solid #000; padding: 8px; text-align: center;">{{ $items->hsn_code }}</td>
                            <td style="border: 1px solid #000; padding: 8px; text-align: center;">{{ $items->tax }} %</td>
                            <td style="border: 1px solid #000; border-right: 0; padding: 8px; text-align: center;">Rs. {{ number_format((float)$items->total_price, 2, '.', '') }}</td>
                        </tr>
                    @empty
                      
                    @endforelse
                <tr>
                    <td colspan="2" style="border: 1px solid #000; border-left: 0; padding: 8px; text-align: left; font-weight: bold;"><span style="">Total Items:</span> {{count((array)$item_details)}}</td>
                    <td style="border: 1px solid #000; padding: 8px; text-align: center; font-weight: bold;">{{ $total_pcs }}</td>
                    <td colspan="3" style="border: 1px solid #000; padding: 8px;"></td>
                    <td style="border: 1px solid #000; padding: 8px; text-align: center; font-weight: bold;">Total Invoice Amount (Inc. Tax):</td>
                    <td style="border: 1px solid #000; border-right: 0; padding: 8px; text-align: right; font-weight: bold;">Rs. {{ number_format((float)$invoice->total_amount, 2, '.', '') }}</td>
                </tr>
            </tbody>
      </table>

      <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
            <tr>
                <td style="width: 60%; padding: 10px; vertical-align: top;">
                    <p style="margin: 0; font-weight: bold;">Amount Chargeable (in words)</p>
                    <p style="margin: 5px 0;color:#000"><span style="">INR </span>{{getAmountAlphabetically($invoice->total_amount)}}</p>
                    <p style="margin: 0;">Company’s PAN: <span style="font-weight: bold;">ABOFA0730E</span></p>
                    <p style="margin: 5px 0; font-weight: bold;text-decoration:underline">Declaration</p>
                    <p style="margin: 0;">We declare that this invoice shows the actual price of the goods described and that all particulars are true and correct.</p>
                </td>
                <td style="width: 40%; padding: 10px; vertical-align: top; text-align: left;">
                    <p style="margin: 0; font-weight: bold; text-align:right;">E. & O.E</p>
                    <p style="margin: 5px 0; font-weight: bold;">Company’s Bank Details</p>
                    <p style="margin: 5px 0;">A/c Holder’s Name: <span style="font-weight: bold;">AMMR TECHNOLOGY LLP</span></p>
                    <p style="margin: 5px 0;">Bank Name: <span style="font-weight: bold;">HDFC BANK</span></p>
                    <p style="margin: 5px 0;">A/c No.: <span style="font-weight: bold;">50200037992770</span></p>
                    <p style="margin: 5px 0;">Branch & IFS Code: <span style="font-weight: bold;">South Calcutta Girls College Branch & HDFC0009536</span></p>
                </td>
            </tr>
            <tr>
            <td align="top" style="border:#000 1px solid;padding:0 0 0 15px; vertical-align: baseline; border-bottom: 0;border-left: 0;">
                
                <table align="top">
                    <tr>
                        <td>
                            <p class="red" style="margin:0;font-size:10px">Customer’s Seal & Signature</p> 
                        </td>
                    </tr>
                </table>
            </td>
            <td style="border:#000 1px solid;padding: 15px; border-bottom: 0;border-right: 0;"><h4 style="text-align:center;">AMMR TECHNOLOGY LLP</h4></td>
            </tr>
        </table>
    
    </div>
    <p style="text-align:center; font-size:12px;">This is Computer Generated Invoice (No Signature Required)</p>
</div>
</body>
</html>

