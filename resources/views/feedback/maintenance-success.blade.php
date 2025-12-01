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
        background: #f5f5f5;
        padding: 15px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        min-height: 204px;
    }

    .item {
        width: 90px;
        height: 90px;
        display: flex;
        justify-content: center;
        align-items: center;
        user-select: none;
        overflow: hidden;
    }
    .radio {
        display: none;
    }
    .radio ~ span {
        font-size: 3rem;
        filter: grayscale(100);
        cursor: pointer;
        transition: 0.3s;
    }
    .radio ~ span svg {
        width:51px;
    }

    .radio:checked ~ span {
    filter: grayscale(0);
    font-size: 4rem;
    }

    .input-btm-submit{
        background: #da1636;
        color: #fff;
        border: none;
        padding: 14px 28px;
        font-size: 15px;
        text-transform: uppercase;
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

    .bottom-body--small {
        min-height: 70px;
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

</style>
<body>
    <section class="feedback-section">
        <div class="feedback-inner">
            <div class="top-header">
                <figure>
                    <img src="{{url('assets')}}/images/kga_logo.png">
                </figure>
                <h2>Thank you</h2>
            </div>
            <div class="bottom-body bottom-body--small">
                <h3>for your valuable feedback </h3>
                <p>Please visit our website <a href="https://kgaelectronics.com">here</a> </p>
            </div>
        </div>
    </section>
</body>
<script type="text/javascript">
    
</script>
</html>