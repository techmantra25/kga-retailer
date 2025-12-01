<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KGA-Road-Challan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="
https://cdn.jsdelivr.net/npm/dejavu-sans@1.0.0/css/dejavu-sans.min.css
" rel="stylesheet">
</head>
<style>
    * { font-family: DejaVu Sans, sans-serif; }
    .text-change {
        font-family: DejaVu Sans !important;
    }
</style>

<body>
    <table style="width: 710px;margin: 0 auto;border-collapse: collapse;border: 1px solid #000;" cellpadding="0px"
        cellspacing="0px">
        <tbody>
            <tr>
                <td>
                    <div style="text-align: center;">
                        <h3 style="margin: 0px;font-size: 25px;font-weight: 600;">AMMR TECHNOLOGY LLP</h3>
                        <p style="margin: 0px;font-size: 18px;font-weight: 600;">DIAMOND HERITAGE 16, STRAND ROAD 10TH
                        FLOOR, ROOM NO #N 1015</p>
                        <p style="margin: 0px;font-size: 16px;font-weight: 600;">Email:
                        kgaelectronics@gmail.com<span>Website: www.kgaelectronics.com</span></p>
                        <p style="margin: 0px;font-size: 16px;font-weight: 600;">Issued From Branch: <span
                                style="font-size: 18px;">{{$data['return_branch']['name']}}</span></p>
                        <p style="margin: 5px;font-size: 16px;font-weight: 600;">Issued From Branch Phone No: <span
                                style="font-size: 18px;">9876543210</span></p>
                                <p style="margin: 5px;font-size: 16px;font-weight: 600;">GSTIN: 19ABOFA0730E1ZO</p>
                        <h3 style="text-decoration: underline;">MATERIAL ISSUE FROM SERVICE CENTRE NOTE</h3>
                    </div>
                </td>
            </tr>
            <tr style="border: 1px solid #000; border-bottom: 0;">
                <td style="padding: 5px 10px;">
                    <table style="border-collapse: collapse;width: 100%;" cellpadding="0" cellspacing="0">
                        <tbody>
                            <tr>
                                <td>
                                    <h6 style="margin-bottom: 0;">Service Issue Note No</h6>
                                </td>
                                <td style="width: 10px;">
                                    <span style="margin-right: 10px;margin-left: 10px;">:</span>
                                </td>
                                <td>
                                    <h5 style="font-size: 16px;margin-bottom: 0;">{{$data['unique_id']}}</h5>
                                </td>
                                <td>
                                    <h5 style="font-size: 16px;margin-bottom: 0;">Date of Issue: <span>{{$date}}</span>
                                    </h5>
                                </td>
                            </tr>
                            <tr>
                                <td style="vertical-align: top;">
                                    <h6 style="margin-bottom: 0;vertical-align: top;">Receiver</h6>

                                </td>
                                <td style="vertical-align: top;"><span
                                        style="margin-right: 10px;margin-left: 10px;">:</span></td>
                                <td>
                                    <h6 style="max-width: 300px;margin-bottom: 0;"><strong>KHOSLA ELECTRONICS PVT. PVT.
                                            LTD</strong> 15/28 Dhirendra Nath Ghosh Road, Bhawanipur, Kolkala-700025
                                        <span>GSTIN: 19AABCK9715C1ZK</span></h6>
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td style="vertical-align: top;">
                                    <h6 style="margin-bottom: 0;">Receiving Branch</h6>

                                </td>
                                <td style="vertical-align: top;"><span
                                        style="margin-right: 10px;margin-left:10px;">:</span></td>
                                <td>
                                    <h6 style="margin-bottom: 0;"><strong>{{$data['return_branch']['name']}}</strong></h6>
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td style="vertical-align: top;">
                                    <h6 style="margin-bottom: 0;">Vehicle No</h6>

                                </td>
                                <td style="vertical-align: top;"><span
                                        style="margin-right: 10px;margin-left: 10px;">:</span></td>
                                <td >
                                    <h6 style=" margin-bottom: 0;">{{$data['return_vehicle_number']}}</h6>
                                </td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <table style="border-collapse: collapse; border: 0px;width: 100%;height: 700px;" cellpadding="0"
                        cellspacing="0">
                        <thead>
                            <tr>
                                <th style="border: 1px solid #000;text-align: center; border-left: 0; font-size:13px;">Call No</th>
                                <th style="border: 1px solid #000;text-align: center; min-width:100px; font-size:13px;">Booking Date</th>
                                <th style="border: 1px solid #000;text-align: center; font-size:13px;">Product Description</th>
                                <th style="border: 1px solid #000;text-align: center; font-size:13px;">Customer Mobile</th>
                                <th style="border: 1px solid #000;text-align: center; font-size:13px;">Customer Name</th>
                                <th style="border: 1px solid #000;text-align: center; min-width:150px; font-size:13px;">Product Serial No. & Barcode</th>
                                <th style="max-width: 80px;text-align: center;border: 1px solid #000;border-right: 0; font-size:13px; min-width:80px;">
                                    MRP (Incl. all
                                    Taxes)<span class="text-change" style="font-family: DejaVu Sans, sans-serif;">&#x20A6;</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="font-size:10px;text-align: center;vertical-align: top;padding: 5px;">
                                    {{$data['unique_id']}}</td>
                                <td style="font-size:10px;border: 1px solid #000;text-align: center;vertical-align: top;padding: 5px;">
                                {{ \Carbon\Carbon::parse($data['created_at'])->format('d-M-y') }}</td>
                                <td style="font-size:10px;padding: 5px;border: 1px solid #000;text-align: center;vertical-align: top;">
                                    {{$data['item']}}</td>
                                <td style="font-size:10px;padding: 5px;border: 1px solid #000;text-align: center;vertical-align: top;">
                                    {{$data['alternate_no']}}</td>
                                <td style="font-size:10px;padding: 5px;border: 1px solid #000;text-align: center;vertical-align: top;">
                                    {{$data['customer_name']}}</td>
                                <td style="border: 1px solid #000;text-align: center;vertical-align: top;">
                                    <div class="barcode_image" style="margin: 0 auto 4px">
                                        <img class="" alt="Barcoded value {{$data['unique_id']}}" src="https://bwipjs-api.metafloor.com/?bcid=code128&text={{$data['unique_id']}}&height=6&textsize=14&scale=6&includetext" style="width: 150px; margin-top:5px">
                                        <span style="font-size: 10px;  margin-top:-5px">Repair product</span>
                                    </div>
                                </td>
                                <td style="text-align: center;vertical-align: top;">
                                    {{number_format($data['product']['mop'],2)}}</td>
                            </tr>
                            <tr>
                                <td style="height: 50px;border: 1px solid #000;border-left: 0;border-right: 0;"
                                    colspan="7">
                                    <table style="width: 100%; min-height: 100px;">
                                        <tbody>
                                            <tr>
                                                <td colspan="7">
                                                    <!-- <div
                                                        style="padding: 5px; display: flex; justify-content: space-between;">
                                                        <div style="display: flex; align-items: center;">
                                                            <h6> <strong>Narration:</strong> <span
                                                                    style="margin-left: 20px;">{{$data['issue']}}</span></h6>
                                                        </div>
                                                        <div style="display: flex;">
                                                            <h6 style="max-width: 140px;">Total : ₹ (incl. all Taxes)
                                                            </h6>
                                                            <span>{{$data['product']['mop']}}</span>
                                                        </div>
                                                    </div> -->
                                                    <tr>
                                                        @php
                                                    
                                                            if($data['is_cancelled'] == 1){
                                                            $narration = "Service denied by customer";
                                                            }else{
                                                            $narration = "OK";

                                                            }
                                                        @endphp
                                                        <td style="font-size:13px; min-width:400px;"><strong style="font-size:14px;">Narration:</strong>{{$narration}}</td>
                                                        <td style="font-size:13px;"><strong style="font-size:14px;">Total : <span class="text-change" style="font-family: DejaVu Sans, sans-serif;">&#x20A6;</span> (incl. all Taxes)</strong> {{number_format($data['product']['mop'],2)}} </td>
                                                    </tr>
                                                    
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="7" style="border: 0px solid #000;height: 100px;">
                                    <table style="width: 100%; padding:0 15px; ">
                                        <tbody>
                                            <tr>
                                                <td style="min-width:355px; height: 300px; vertical-align: bottom;">

                                                    <p style="margin: 0; font-size:14px;">Receiver's Signature with Date</p>
                                                    

                                                </td>
                                                <td  style="min-width:355px; height: 300px; vertical-align: bottom;">
                                                <p style="margin: 0; font-size:14px">Manager/In Charge</p>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>