
<!DOCTYPE html>
<html>
<head>
	<title>PACKING SLIP | {{$packingslips->slipno}}</title>
</head>
<body >
    <table id="packing_table" style="width: 100%; border-collapse: collapse;" border="1" cellpadding="0" cellspacing="0">
        <h3 style="text-align: center;">PACKING SLIP</h3>
        <tr>
            <td>
                <table style="width: 100%; height: 140px; border-collapse: collapse;" cellpadding="0" cellspacing="0">
                    <tr>
                        <td style="padding: 20px; border-right: 1px solid #000;">
                            @if (!empty($packingslips->sales_order->service_partner))
                            {{$packingslips->sales_order->service_partner->person_name}} <br/>
                            @else
                            {{$packingslips->sales_order->dealer->name}} <br/>
                            @endif
                            
                            {{-- {{$customer->phone}} <br/>
                            {{$customer->email}} <br/>
                            {{$customer->address }} <br/> --}}
                        </td>
                        <td style="padding: 20px;">
                            {{$packingslips->slipno}}
                            <br/>
                            {{date('d/m/Y', strtotime($packingslips->created_at))}}
                        </td>
                    </tr>
                </table>
                <table style="width: 100%; border-collapse: collapse;" cellpadding="0" cellspacing="0">
                    <thead>
                        <tr style="border-top: 1px solid #000;">
                            <th style="width:5%; padding: 20px; border-bottom: 1px solid #000; text-align: center;">#</th>
                            <th style="padding: 20px; border-bottom: 1px solid #000; border-left: 1px solid #000; border-right: 1px solid #000; text-align: left">Descriptions of goods</th>
                            <th style="padding: 20px; border-bottom: 1px solid #000; border-left: 1px solid #000; border-right: 1px solid #000;">Total Pieces</th>
                            <th style="padding: 20px; border-bottom: 1px solid #000;">Units</th>
                        </tr>
                    </thead>
                    @php
                        // $details = json_decode($packingslips->details);
                        $details = $packingslips->packingslip_products;
                        $count_pcs = 0;
                    @endphp
                    <tbody style="height: 400px; vertical-align: top;">
                        
                        @forelse ($details as $key => $value)
                        @php
                            $count_pcs += $value->quantity;
                        @endphp
                        <tr>
                            <td style="padding: 20px; text-align: center; border-left: 1px solid #000; border-right: 1px solid #000;">{{$key+1}}</td>
                            <td style="padding: 20px;">{{$value->product->title}}</td>
                            <td style="padding: 20px; text-align: center; border-left: 1px solid #000; border-right: 1px solid #000;">{{$value->quantity}}</td>
                            <td style="padding: 20px; text-align: center;">Pieces</td>
                        </tr> 
                        @empty
                            
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr style="border-top: 1px solid #000;">
                            <td style="padding: 20px; text-align:center;">Total</td>
                            <td style="padding: 20px; text-align: left; border-left: 1px solid #000; border-right: 1px solid #000;"></td>
                            <td style="padding: 20px; text-align: center; border-left: 1px solid #000; border-right: 1px solid #000;">{{$count_pcs}}</td>
                            <td style="padding: 20px; text-align: center; border-left: 1px solid #000; border-right: 1px solid #000;">Pieces</td>
                        </tr>
                    </tfoot>
                </table>
            </td>
        </tr>
    </table>
    <script>
        
        
    </script>
</body>
</html>