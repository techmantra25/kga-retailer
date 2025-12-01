  <style>
    p, th,td{
      font-size: 12px;
    }
  </style>
  <table style="border: 1px solid black; width: 700px; margin: auto; font-family: Arial, sans-serif; border-collapse: collapse;">
    <tr>
      <td colspan="5" style="padding: 10px; vertical-align: top;">
        <h2 style="margin: 0;">AMMR TECHNOLOGY LLP</h2>
        <p style="margin: 5px 0;"><b>DIAMOND HERITAGE 16, STRAND ROAD 10TH FLOOR, ROOM NO - #N 1015<b></p>
        <p style="margin: 5px 0;"><b>Phone: +91-6291117317</b></p>
        <p style="margin: 5px 0;"><b>E-mail: <a href="mailto:kgaelectronics@gmail.com">kgaelectronics@gmail.com
            <b></a></p>
        <p style="margin: 5px 0;">Website: <a href="http://www.kgaelectronics.com" target="_blank">www.kgaelectronics.com</a></p>
      </td>
      <td colspan="4" style="padding: 10px; vertical-align: top; text-align: right;">
        <p style="margin: 5px 0;text-align: left; padding: 8px 0;"><b>GSTIN: 19ABOFA0730E1ZO</b></p>
        <p style="margin: 5px 0;text-align: left;"><b>PAN No: ABOFA0730E</b></p>
      </td>
    </tr>
    <tr>
      <td colspan="9" style="padding: 10px;">
        <p style="margin: 0;"><b>SERVICE PARTNER:</b> {{ $crp_data->servicePartner?$crp_data->servicePartner->person_name:"" }} </p>

        <p style="margin: 10px 0;"><b>Phone:</b> {{ $crp_data->servicePartner?$crp_data->servicePartner->phone:"" }}</p>
      </td>
    </tr>
    <tr>
      <td colspan="9" style="text-align: center; padding: 10px;">
        <h3 style="margin: 0;">TAX INVOICE</h3>
      </td>
    </tr>

    <tr>
      <td colspan="4" style="border: 1px solid black; padding: 10px; vertical-align: top;">
        <strong>Billed to</strong>
        <p><b>Name :</b> {{ $crp_data->customer_name?$crp_data->customer_name:"" }}</p>
        <p><b>Address :</b> {{ $crp_data->address?$crp_data->address:"" }}</p>
        <p><b>Contact No :</b> {{ $crp_data->alternate_no?$crp_data->alternate_no:"" }}</p>
        <p><b>State :</b>  WEST BENGAL</p>
        <p><b>GSTIN :</b> UN-REGISTERED / GST No</p>
      </td>
      <td colspan="5" style="border: 1px solid black; padding: 10px; vertical-align: top;">
        <p><b>Invoice No :</b> {{ $crp_data->paymentData?$crp_data->paymentData->invoice_id:"" }}</p>
        <p><b>Date :</b> {{$Todate}}</p>
        <p><b>Call No :</b> {{ $crp_data->unique_id?$crp_data->unique_id:"" }}</p>
        <p><b>Call Date :</b> {{ $crp_data->entry_date?$crp_data->entry_date:"" }}</p>
        <p><b>Product :</b>{{ $crp_data->item?$crp_data->item:"" }} </p>
        <p><b>Product Sl. No :</b> {{ $crp_data->serial?$crp_data->serial:"" }}</p>
      </td>
    </tr>

    <!-- Goods Section -->
    <tr>
      <td colspan="9" style="border: 1px solid black; padding: 10px; font-weight: bold; text-align: left;">GOODS</td>
    </tr>
    <tr style="text-align: center; font-weight: bold;">
      <td style="border: 1px solid black; padding: 3px;">Spares Description</td>
      <td style="border: 1px solid black;padding: 3px;">HSN/SAC</td>
      <td style="border: 1px solid black;padding: 3px;">Qty</td>
      <td style="border: 1px solid black;padding: 3px;">Rate Rs.</td>
      <td style="border: 1px solid black;padding: 3px;">Taxable Value</td>
      <td style="border: 1px solid black;padding: 3px;">IGST</td>
      <td style="border: 1px solid black;padding: 3px;">CGST</td>
      <td style="border: 1px solid black;padding: 3px;">SGST</td>
      <td style="border: 1px solid black;padding: 3px;">Amount</td>
    </tr>
    @php
    $totalQty = 0;
    $rate_amount = 0;
    $final_amount = 0;
    $gst_value = 0;
    $cgst = 0;
    $sgst = 0;
    $taxable_value = 0;
    $total_taxable_value = 0;
    $total_rate_amount = 0;
    $total_final_amount = 0;
    $total_cgst = 0;
    $total_sgst = 0;
    @endphp
    @foreach($sp_final_data as $item)
    @php
        $totalQty += $item->qty;
        $rate_amount = $item->selling_price ? $item->selling_price : 0;
        $total_rate_amount += $rate_amount;
        $final_amount = $item->selling_price ? $item->selling_price : 0;
        $total_final_amount += $final_amount;
        $gst_value = $item->productData? $item->productData->gst : 0;
        $cgst = ($rate_amount * (($gst_value/2)/100));
        $sgst = ($rate_amount * (($gst_value/2)/100));
        $taxable_value = $final_amount-$cgst-$sgst;
        $total_taxable_value += $taxable_value;
        $total_cgst +=$cgst;
        $total_sgst +=$sgst;
       
    @endphp
    <tr style="text-align: center;">
      <td style="border: 1px solid black;padding: 3px;">{{ $item->productData?$item->productData->title:""}} (As per scan)</td>
      <td style="border: 1px solid black;padding: 3px;">{{ $item->productData?$item->productData->hsn_code:""}}</td>
      <td style="border: 1px solid black;padding: 3px;">1</td>
      <td style="border: 1px solid black;padding: 3px;">{{number_format($rate_amount,2)}}</td>
      <td style="border: 1px solid black;padding: 3px;">{{number_format($taxable_value,2)}}</td>
      <td style="border: 1px solid black;padding: 3px;"></td>
      <td style="border: 1px solid black;padding: 3px;">{{number_format($cgst,2)}}</td>
      <td style="border: 1px solid black;padding: 3px;">{{number_format($sgst,2)}}</td>
      <td style="border: 1px solid black;padding: 3px;">{{number_format($final_amount,2)}}</td>
    </tr>
    @endforeach
    <tr style="text-align: center; font-weight: bold;">
      <td colspan="" style="border: 1px solid black;text-align: center;padding: 3px;">Total</td>
      <td style="border: 1px solid black;padding: 3px;"></td>
       <td style="border: 1px solid black;padding: 3px;">{{$totalQty}}</td>
      <td style="border: 1px solid black;padding: 3px;">{{number_format($total_rate_amount,2)}}</td>
      <td style="border: 1px solid black;padding: 3px;">{{number_format($total_taxable_value,2)}}</td>
      <td style="border: 1px solid black;padding: 3px;"></td>
      <td style="border: 1px solid black;padding: 3px;">{{number_format($total_cgst,2)}}</td>
      <td style="border: 1px solid black;padding: 3px;">{{number_format($total_sgst,2)}}</td>
      <td style="border: 1px solid black;padding: 3px;">{{number_format($total_final_amount,2)}}</td>
      
    </tr>
    <!-- Services Section -->
     @php
     $service_cgst = ($crp_data->repair_charge *(($gst_value/2)/100));
     $service_sgst = ($crp_data->repair_charge *(($gst_value/2)/100));
     $service_taxable_vale = $crp_data->repair_charge-$service_cgst-$service_sgst;
     $grand_total_amount=$total_final_amount+$crp_data->repair_charge;
     @endphp
    <tr>
      <td colspan="9" style="border: 1px solid black; padding: 10px; font-weight: bold; text-align: left;">SERVICES</td>
    </tr>
    <tr style="text-align: center; font-weight: bold;">
      <td style="border: 1px solid black;padding: 3px;">Service Description</td>
      <td colspan="2" style="border: 1px solid black;padding: 3px;">HSN/SAC</td>
      <td colspan="2" style="border: 1px solid black;padding: 3px;">Taxable Value</td>
      <td style="border: 1px solid black;padding: 3px;">IGST</td>
      <td style="border: 1px solid black;padding: 3px;">CGST</td>
      <td style="border: 1px solid black;padding: 3px;">SGST</td>
      <td style="border: 1px solid black;padding: 3px;">Amount</td>
    </tr>
    <tr style="text-align: center;">
      <td style="border: 1px solid black;padding: 3px;">SERVICE CHARGES</td>
      <td colspan="2" style="border: 1px solid black;padding: 3px;">998715</td>
      <td colspan="2" style="border: 1px solid black;padding: 3px;">{{number_format($service_taxable_vale,2)}}</td>
      <td style="border: 1px solid black;padding: 3px;"></td>
      <td style="border: 1px solid black;padding: 3px;">{{number_format($service_cgst,2)}}</td>
      <td style="border: 1px solid black;padding: 3px;">{{number_format($service_sgst,2)}}</td>
      <td style="border: 1px solid black;padding: 3px;">{{number_format($crp_data->repair_charge,2)}}</td>
    </tr>
    <!-- <tr style="text-align: center; font-weight: bold;">
      <td colspan="" style="border: 1px solid black;text-align: center;padding: 3px;">Total</td>
      <td colspan="2" style="border: 1px solid black;padding: 3px;"></td>
      <td colspan="2" style="border: 1px solid black;padding: 3px;">500.00</td>
      <td style="border: 1px solid black;padding: 3px;"></td>
      <td style="border: 1px solid black;padding: 3px;">45.00</td>
      <td style="border: 1px solid black;padding: 3px;">45.00</td>
      <td style="border: 1px solid black;padding: 3px;">590.00</td>
    </tr> -->
    <tr style="text-align: center;">
        <td style="height: 80px;"></td>
    </tr>

    <!-- Total Amount Section -->
    <tr style="font-weight: bold; text-align: left;padding: 3px;">
      <td colspan="1" style="border: 1px solid black;padding: 8px;border-right:0">Total Taxable Amount</td>
      <td colspan="1" style="border: 1px solid black;padding: 3px;border-left: 0;">{{number_format(($total_taxable_value+$service_taxable_vale),2)}}</td>
      <td colspan="2" style="border: 1px solid black;padding: 3px;text-align: center;border-right:0">Total Tax </td>
      <td colspan="1" style="border: 1px solid black;padding: 3px;border-left: 0;">{{number_format(($total_cgst+$total_sgst+$service_cgst+$service_sgst),2)}}</td>
      <td colspan="3" style="border: 1px solid black;padding: 3px;">Total :( Incl. all Taxes ) ₹</td>
      <td colspan="1" style="border: 1px solid black;padding: 3px;">{{number_format(($grand_total_amount),2)}}</td>
    </tr>
    <tr style="font-weight: bold; text-align: left;padding: 3px;">
      <td colspan="1" style="border: 1px solid black;padding: 8px;border-right:0">PAYMENT Ref. No. :</td>
      <td colspan="1" style="border: 1px solid black;padding: 3px;border-left: 0;">{{ $crp_data->paymentData?$crp_data->paymentData->payment_id:""}}</td>
      <td colspan="4" style="border: 1px solid black;padding: 8px;border-right:0;text-align:right;">PAYMENT Date :</td>
      <td colspan="3" style="border: 1px solid black;padding: 8px;border-left:0;text-align:right;">{{ $crp_data->paymentData?$crp_data->paymentData->payment_date:"" }}</td>
    </tr>

    <tr style="font-weight: bold; text-align: left;">
        <td style="height: 80px;"></td>
    </tr>
    
    @php
    $rupees = amountInWords($grand_total_amount);
    @endphp
    <!-- Final Section -->
    <tr>
      <td colspan="9" style="border: 1px solid black;padding: 10px;">
        <p style="margin: 0; font-weight: bold;">Total Amount: <span style="padding: 3px;">{{$rupees}}</span></p>
        <!-- <p style="margin: 0; font-weight: bold;">Remarks: <span style="color: red;padding: 3px;">JOB DONE - CUSTOMER HAPPY</span></p> -->
      </td>
    </tr>

     <tr>
      <td colspan="4" style="border: 1px solid black;padding: 10px;border-right: 0;">
        <p style="margin: 0; font-weight: bold;">Remarks: <span style="padding: 3px;">JOB DONE - CUSTOMER HAPPY</span></p>
      </td>
      <td valign="bottom" colspan="5">
          <p style="text-align:right;">
              <img style="width:150px" src="https://kgaerp.in/test-retailer/assets/images/crp_sign.png">
          </p>
          <p style="text-align:right;padding:8px;font-size: 12px;">
              <b>For AMMR TECHNOLOGY LLP</b>
          </p>
      </td>
    </tr>

  </table>

  <div style="margin:0px auto; width:900px; text-align: center; padding-right:120px;">
   <!--  <span style="float:left"><b>User: <span style="color:red">Sukanta_Brhs</span></b></span> -->
      <p>
        <b>Subject to Kolkata Jurisdiction</b>
      </p>
  </div>
