<!DOCTYPE html>
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>KGA</title>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<style>
    .feedback-section {
        background: rgb(255,255,255);
        background: -moz-linear-gradient(top, rgba(255,255,255,1) 47%, rgba(218,22,54,1) 47%);
        background: -webkit-linear-gradient(top, rgba(255,255,255,1) 47%,rgba(218,22,54,1) 47%);
        background: linear-gradient(to bottom, rgba(255,255,255,1) 47%,rgba(218,22,54,1) 47%);
        filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#da1636',GradientType=0 );
        height: 100vh;
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        align-items: center;
    }
    body{
        padding: 0;
        margin: 0;
        font-family: 'Rubik', sans-serif;
    }
    .feedback-inner {
        max-width: 500px;
        margin: auto;
        flex: 1;
        background: #fff;
        
    }

    .top-header figure {
        text-align: center;
        display: block;
    }
    .top-header figure img {
        height: 79px;
    }
    .top-header h2 {
        text-align: center;
        font-size: 30px;
        margin: 0;
    }
    .top-header h2 span {
        color: #da1636;
    }

    .feedback-inner h3 {
        font-size: 15px;
        font-weight: 500;
        text-align: center;
        padding: 0;
        margin: 0 0 11px;
    }
    .feedback-wrap {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        align-items: center;
        margin: 18px 0 18px;
    }
    .top-header {
        /* background: #f5f5f5; */
        padding: 15px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        min-height: 140px;
    }

    .item {
        width: 90px;
        height: 90px;
        display: flex;
        justify-content: center;
        align-items: center;
        user-select: none;
        /* overflow: hidden; */
    }
    .item label strong {
        font-size: 12px;
        color: #000000;
    }
    

    
    .bottom-body {
        padding: 15px;
        min-height:247px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
    }
    .bottom-body h3 {
        margin-bottom: 24px;
    }
    .price_card {
        display: block;
        padding: 16px;
        background: #f5f7fe;
        border-radius: 6px;
        width: 100%;
        box-sizing: border-box;
        margin-bottom: 10px;
    }
    .price_card:last-child {
        margin-bottom: 0;
    }
    .price_card h2 {
        font-size: 40px;
        display: flex;
        justify-content: center;
        margin: 0;
    }
    .price_card h2 sub {
        align-self: flex-end;
        display: inline-block;
        font-size: 20px;
        font-weight: normal;
        top: -6px;
        position: relative;
        margin-right: 5px;
    }
    .price_card h2 span {
        align-self: flex-end;
        display: inline-block;
        font-size: 20px;
        font-weight: normal;
        top: -6px;
        position: relative;
        margin-left: 5px;
    }
    .price_card p {
        margin: 0 0 10px;
        font-size: 12px;
    }
    .price_card a {
        display: inline-block;
        padding: 6px 24px;
        border: 1px solid #ddd;
        border-radius: 20px;
        font-size: 14px;
        color: #000;
        text-decoration: none;
    }
    form {
        width: 100%;
        padding: 0 30px;
        display: block;
        box-sizing: border-box;
    }
    .table {
        border-collapse: collapse;
        width: 100%;
    }
    .table th {
        text-align: left;
        font-size: 14px;
        padding: 10px 10px 10px 0;
    }
    .table td {
        text-align: right;
        font-size: 14px;
        padding: 10px 0 10px 10px;
    }
    .table th, .table td {
        border-bottom: 1px solid #eee;
    }
    .table tr:last-child th, .table tr:last-child td {
        border: none;
    }
    .table tr:last-child td {
        padding: 10px 0;
    }
    .back_btn {
        display: inline-block;
        padding: 6px 24px;
        border: 1px solid #ddd;
        border-radius: 20px;
        font-size: 14px;
        color: #000;
        text-decoration: none;
    }
    .btn {
        display: inline-block;
        padding: 6px 24px;
        border: 1px solid #ddd;
        border-radius: 20px;
        font-size: 14px;
        color: #000;
        text-decoration: none;
        cursor: pointer;
    }
    .btn-red {
        background: #d60d38;
        color: #fff;
        border-color: #d60d38;
    }

    @media(max-width:498px){
        .feedback-inner {
            margin-left: 15px;
            margin-right: 15px;
        }
        .item {
            width: 78px;
            height: 78px;
        }
        .top-header h2 {
            font-size: 25px;
        }
        form {
            width: 100%;
            padding: 0;
        }
    }


    @media(max-width:448px){
        
        .item {
            width: 62px;
            height: 62px;
        }

    }
    @media(max-width:400px){
        .item {
            width:50px;
            height:50px;
        }
        
        .item {
            width: 59px;
            height: auto;
        }
        .item label strong {
            font-size: 10px;
        }
    }

</style>
<body>
    <section class="feedback-section">
        <div class="feedback-inner">
            <div class="top-header">
                <figure>
                    <img src="{{url('assets')}}/images/kga_logo.png">
                </figure>
                <h2>Hello, <span>{{$amc_request->customer_name}}</span></h2>
            </div>
            <div class="bottom-body">
                <h3>Package Preview </h3>
               
                <form id="myForm" method="POST" action="{{ route('customer-payment.submit-amc') }}">
                    @csrf
                    <input type="hidden" name="customer_name" value="{{$amc_request->customer_name}}">
                    <input type="hidden" name="customer_phone" value="{{$amc_request->customer_phone}}">
                    <input type="hidden" name="amount" value="{{$amc->amount}}">
                    <input type="hidden" name="product_id" value="{{$amc_request->product_id}}">
                    <input type="hidden" name="amc_request_id" value="{{$amc_request_id}}">
                    <input type="hidden" name="month_val" value="{{$amc->month_val}}">
                    
                    <table class="table">
                        <tbody>
                            <tr>
                                <th>Item</th>
                                <td>{{$amc_request->product->title}}</td>
                            </tr>
                            <tr>
                                <th>Item Purchase Date</th>
                                <td>{{ date('d M, Y', strtotime($amc_request->bill_date)) }}</td>
                            </tr>
                            <tr>
                                <th>Package Amount</th>
                                <td>₹ {{$amc->amount}}</td>
                            </tr>
                            @if (!empty($amc->description))
                            <tr>
                                <th>Package Description</th>
                                <td>{{$amc->description}}</td>
                            </tr>
                            @endif                            
                            <tr>
                                <th>Validity</th>
                                <td>{{($amc->month_val/12)}} years</td>
                            </tr>
                            <tr>
                                @php
                                    

                                    $getGoodsWarrantyEndDate = getGoodsWarrantyEndDate($amc_request->product_id,$amc_request->bill_date);

                                    $warranty_end_date = $amc_request->bill_date;
                                    if($getGoodsWarrantyEndDate['status']){
                                        $warranty_end_date = $getGoodsWarrantyEndDate['warranty_end_date'];
                                    }

                                    $amc_expired = date('Y-m-d', strtotime($warranty_end_date. ' + '.$amc->month_val.' months'));
                                    $amc_expired_val = date('Y-m-d', strtotime($amc_expired.'-1 days'));
                                    $amc_expired_text = date('d M, Y', strtotime($amc_expired.'-1 days'));
                                @endphp
                                <input type="hidden" name="expiry_date" value="{{$amc_expired_val}}">
                                <th>Expiry Date</th>
                                <td>{{ $amc_expired_text }}</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th align="left">
                                    <a href="{{ route('customer-payment.amc-offer',$amc_request_id) }}" class="back_btn">Back</a>
                                </th>
                                <td align="right">
                                    <button type="submit" id="submitBtn" class="btn btn-red">Proceed To Pay</button>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </form>

                
                  
                
            </div>
            
        </div>
    </section>
    <script type="text/javascript">
        $("#myForm").submit(function() {
            $('input').attr('readonly', 'readonly');
            $('#submitBtn').attr('disabled', 'disabled');  
            $('#submitBtn').html('<i class="fi fi-br-refresh"></i>').append('   Please wait ...'); 
            return true;
        }); 
    </script>
</body>
</html>
