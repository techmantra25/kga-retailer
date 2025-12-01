<!DOCTYPE html>
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>KGA</title>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<style>
    .feedback-section {
        background: rgb(255,255,255);
        background: -moz-linear-gradient(top, rgba(255,255,255,1) 47%, rgba(218,22,54,1) 47%);
        background: -webkit-linear-gradient(top, rgba(255,255,255,1) 47%,rgba(218,22,54,1) 47%);
        background: linear-gradient(to bottom, rgba(255,255,255,1) 47%,rgba(218,22,54,1) 47%);
        filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#da1636',GradientType=0 );
        min-height: 100vh;
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
        margin: 80px auto;
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
    .feedback-inner h5 {
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
    .radio {
        display: none;
    }
    .radio ~ span {
        /* font-size: 3rem; */
        filter: grayscale(100);
        cursor: pointer;
        transition: 0.3s;
        display: block;

    }
    .radio ~ span svg {
        width:51px;
    }

    .radio:checked ~ span {
        filter: grayscale(0);
        font-size: 3rem;
    }

    .input-btm-submit{
        background: #da1636;
        color: #fff;
        border: none;
        padding: 14px 28px;
        font-size: 15px;
        text-transform: uppercase;
        cursor: pointer;
    }
    .input-btm-submit:hover {
        background: #000;
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
    .bottom-body h3, .bottom-body h5 {
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
        .feedback-inner {
            margin: 20px 15px;
        }
    }


    @media(max-width:448px){
        .radio ~ span svg {
            width: 40px;
        }
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
        .radio ~ span svg {
            width: 36px;
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

                <h5>{{$amc_request->product->title }} </h5>
                <h3>Please select AMC Package </h3>
                @if (!empty($errMsg))
                    <div class="price_card">
                        <h4>{{$errMsg}}</h4>
                        <h3>Please visit our page <a href="http://kgaelectronics.com" class="back_btn">Click here</a> </h3>
                    </div>
                @else
                    @forelse ($product_amcs as $amc)
                    <div class="price_card">
                        <h2><sub>₹</sub> {{$amc->amount}} <span>{{($amc->month_val/12)}} years</span></h2>
                        <p>{{$amc->description}}</p>
                        <h3><a href="{{ route('customer-payment.amc-preview',[$amc_request_id,$amc->id]) }}" class="btn btn-red">Purchase Package</a></h3>
                    </div>
                    @empty
                        <span>No Package Found !!! </span>
                    @endforelse
                @endif
                
            </div>
            
        </div>
    </section>
    <script type="text/javascript">
        // function preventBack() {
        //     window.history.forward(); 
        // }
          
        // setTimeout("preventBack()", 0);
          
        // window.onunload = function () { null };
    </script>
</body>
</html>