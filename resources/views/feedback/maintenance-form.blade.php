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
                <h2>Hello, <span>{{$customer_name}}</span></h2>
            </div>
            <div class="bottom-body">
                <h3>Please give your valuable feedback </h3>
                
                <form action="{{route('feedback.submit-maintenance')}}" method="POST">
                    @csrf
                    <input type="hidden" name="maintenance_id" value="{{$id}}">
                    <input type="hidden" name="customer_name" value="{{$customer_name}}">
                    <input type="hidden" name="customer_phone" value="{{$customer_phone}}">
                    <input type="hidden" name="bill_no" value="{{$bill_no}}">
                    <div class="feedback-wrap">
                        <div class="item">
                            <label for="1">
                                <input class="radio" type="radio" name="feedback" id="1" value="1">
                                <span><svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.com/svgjs" width="50" height="50" x="0" y="0" viewBox="0 0 16.933 16.933" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><g transform="translate(0 -280.067)"><circle cx="8.467" cy="288.533" r="7.938" fill="#ffc861" data-original="#ffc861" class=""></circle><path fill="#ffab61" d="M15.469 287.712c.82 5.665-5.937 10.352-10.934 6.649-.386-.227-.863.107-.792.55a7.938 7.938 0 0 0 12.546-7.721.537.537 0 0 0-.82.522z" data-original="#ffab61"></path><path fill="#4d4d4d" d="M6.052 288.08a.678.678 0 0 0-.678.677.678.678 0 0 0 .678.678.678.678 0 0 0 .678-.678.678.678 0 0 0-.678-.678zm4.829 0a.678.678 0 0 0-.678.677.678.678 0 0 0 .678.678.678.678 0 0 0 .678-.678.678.678 0 0 0-.678-.678zM6.008 291.576c-.267.244.113.639.367.38.895-.74 2.276-1.569 3.31-.933.311.201.595-.287.266-.457-1.38-.747-2.806.016-3.943 1.01z" data-original="#4d4d4d"></path><path fill="#4d4d4d" d="M4.661 288.008c-.353 0-.353.53 0 .53h7.54c.353 0 .353-.53 0-.53z" data-original="#4d4d4d"></path><g fill="#ef6356"><path d="M9.589 283.882c.311.25.138.606-.04.787-.25.25.126.625.376.375.534-.535.473-1.238-.037-1.6-.31-.18-.584.232-.3.438zM11.38 282.804c-.246.315-.604.146-.788-.029-.253-.247-.623.133-.37.38.542.527 1.244.457 1.6-.058.176-.31-.24-.58-.442-.293zM11.594 285.623c-.272-.293-.615-.094-.783.095-.231.268-.632-.08-.4-.346.494-.572 1.2-.561 1.597-.079.203.295-.188.599-.414.33zM12.54 283.758c-.297.268-.102.613.085.784.264.234-.088.631-.352.396-.565-.502-.546-1.207-.058-1.599.298-.198.596.196.324.42z" fill="#ef6356" data-original="#ef6356"></path></g></g></g></svg></span>
                                <strong>Angry</strong>
                            </label>
                        </div>
                    
                        <div class="item">
                            <label for="2">
                                <input class="radio" type="radio" name="feedback" id="2" value="2">
                                <span><svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.com/svgjs" width="50" height="50" x="0" y="0" viewBox="0 0 175 175" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><g data-name="Layer 2"><g data-name="52.Pensiv-Face"><circle cx="87.5" cy="87.5" r="87.5" fill="#ffdd67" data-original="#ffdd67" class=""></circle><path fill="#b26d3c" d="M58.5 68c11 0 19.89-16.64 19.89-10.29s-8.9 22.37-19.89 22.37-19.9-16-19.9-22.38S47.51 68 58.5 68zM116.5 68.4c11 0 19.9-16.63 19.9-10.28s-8.91 22.37-19.9 22.37-19.89-16-19.89-22.37 8.9 10.28 19.89 10.28z" data-original="#b26d3c"></path><rect width="60" height="12.11" x="57.49" y="115.22" fill="#b26d3c" rx="6.05" data-original="#b26d3c"></rect></g></g></g></svg></span>
                                <strong>Okay</strong>
                            </label>
                        </div>
                    
                        <div class="item">
                            <label for="3">
                                <input class="radio" type="radio" name="feedback" id="3" value="3">
                                <span><svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.com/svgjs" width="50" height="50" x="0" y="0" viewBox="0 0 175 175" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><g data-name="Layer 2"><g data-name="37.Smile-Beam"><circle cx="87.5" cy="87.5" r="87.5" fill="#ffdd67" data-original="#ffdd67" class=""></circle><g fill="#b26d3c"><path d="M87.5 140.07A50.6 50.6 0 0 1 39.67 106 6 6 0 1 1 51 102.07a38.59 38.59 0 0 0 73 0 6 6 0 1 1 11.33 3.93 50.6 50.6 0 0 1-47.83 34.07zM116.5 58.77c-11 0-19.89 16.63-19.89 10.28s8.9-22.38 19.89-22.38 19.9 16 19.9 22.38-8.91-10.28-19.9-10.28zM58.5 58.35c-11 0-19.9 16.64-19.9 10.29s8.91-22.38 19.9-22.38 19.89 16 19.89 22.38-8.9-10.29-19.89-10.29z" fill="#b26d3c" data-original="#b26d3c"></path></g></g></g></g></svg></span>
                                <strong>Happy</strong>
                            </label>
                        </div>
                    
                        <div class="item">
                            <label for="4">
                                <input class="radio" type="radio" name="feedback" id="4" value="4">
                                <span><svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.com/svgjs" width="50" height="50" x="0" y="0" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M258.781.021V511.98C398.886 510.496 512 396.455 512 256S398.886 1.504 258.781.021z" style="" fill="#ffb751" data-original="#ffb751"></path><path d="M258.781.021C257.854.01 256.927 0 256 0 114.618 0 0 114.618 0 256s114.618 256 256 256c.927 0 1.854-.01 2.781-.021C376.119 510.207 470.793 396.28 470.793 256S376.119 1.793 258.781.021z" style="" fill="#ffd764" data-original="#ffd764" class=""></path><path d="M372.926 225.301h-15.453l-100.165 128h102.905c17.647-22.489 28.165-50.839 28.165-81.642v-30.905c0-8.5-6.953-15.453-15.452-15.453z" style="" fill="#401510" data-original="#401510"></path><path d="M139.074 225.301c-8.499 0-15.453 6.954-15.453 15.453v30.905c0 30.802 10.518 59.153 28.165 81.642l192.695-16.813c8.272-19.161 12.991-41.279 12.991-64.829v-15.453l-202.946-30.905h-15.452z" style="" fill="#901c0f" data-original="#901c0f"></path><path d="m344.482 336.488-87.174 67.539c41.774-.402 78.912-20.15 102.905-50.726a132.643 132.643 0 0 0-15.731-16.813z" style="" fill="#e0230d" data-original="#e0230d"></path><path d="M344.482 336.488c-17.183 39.858-49.737 66.921-87.174 67.539-.433.01-.876.01-1.308.01-42.31 0-79.973-19.841-104.213-50.736 24.24-30.885 61.904-50.736 104.213-50.736 34.017-.001 65.035 12.835 88.482 33.923z" style="" fill="#ff5440" data-original="#ff5440"></path><path d="m326.567 225.301-15.453 46.358h30.905c8.499 0 15.453-6.954 15.453-15.453v-30.905h-30.905z" style="" fill="#ffe7a3" data-original="#ffe7a3"></path><path d="M326.567 225.301v30.905c0 8.499-6.954 15.453-15.453 15.453H169.98c-8.499 0-15.453-6.954-15.453-15.453v-30.905h172.04z" style="" fill="#ffffff" data-original="#ffffff"></path><circle cx="66.961" cy="250.643" r="25.755" style="" fill="#ff804c" data-original="#ff804c"></circle><circle cx="445.038" cy="251.055" r="25.755" style="" fill="#ff804c" data-original="#ff804c"></circle><path d="M185.433 186.672a7.726 7.726 0 0 1-7.726-7.726c0-12.781-10.398-23.179-23.179-23.179-12.781 0-23.179 10.398-23.179 23.179a7.726 7.726 0 1 1-15.452 0c0-21.302 17.33-38.632 38.632-38.632s38.632 17.33 38.632 38.632a7.729 7.729 0 0 1-7.728 7.726zM388.378 186.672a7.725 7.725 0 0 1-7.726-7.726c0-12.781-10.399-23.179-23.179-23.179s-23.179 10.398-23.179 23.179a7.725 7.725 0 0 1-7.726 7.726 7.725 7.725 0 0 1-7.726-7.726c0-21.302 17.33-38.632 38.632-38.632s38.632 17.33 38.632 38.632a7.728 7.728 0 0 1-7.728 7.726z" style="" fill="#401510" data-original="#401510"></path><path d="M263.726 442.672h-15.453a7.726 7.726 0 1 1 0-15.452h15.453a7.725 7.725 0 0 1 7.726 7.726 7.725 7.725 0 0 1-7.726 7.726z" style="" fill="#ffb751" data-original="#ffb751"></path></g></svg></span>
                                <strong>Very Happy</strong>
                            </label>
                        </div>
                    
                        <div class="item">
                            <label for="5">
                                <input class="radio" type="radio" name="feedback" id="5" value="5">
                                <span> 
                                    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.com/svgjs" width="50" height="50" x="0" y="0" viewBox="0 0 64 64" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><circle cx="32" cy="32" r="32" fill="#ffcd00" data-original="#ffcd00"></circle><g fill-rule="evenodd"><path fill="#ffb400" d="M32 64a16 16 0 0 1-16-16 16 16 0 0 1 16-16h32a32 32 0 0 1-32 32z" data-original="#ffb400"></path><path fill="#ffeb00" d="M32 0a16 16 0 0 1 16 16 16 16 0 0 1-16 16H0A32 32 0 0 1 32 0z" data-original="#ffeb00" class=""></path><path fill="#46321e" d="M12 32.002 52 32a19.951 19.951 0 0 1-6.77 15H18.77q-.469-.413-.913-.857A19.998 19.998 0 0 1 12 32.002z" data-original="#46321e"></path><path fill="#c8005a" d="M18.77 47a15.98 15.98 0 0 1 13.228-7h.004a15.981 15.981 0 0 1 13.229 6.999A19.924 19.924 0 0 1 32 52h-.002a19.998 19.998 0 0 1-13.229-5z" data-original="#c8005a"></path></g><path fill="#ff961e" d="M55.217 22.142 52 32.044l-8.423-6.119H33.165l3.218-9.903-3.218-9.901h10.412L52 0l3.217 9.903 8.423 6.119zM8.783 22.142 12 32.044l8.423-6.119h10.413l-3.219-9.903 3.219-9.901H20.423L12 0 8.783 9.903.36 16.022z" data-original="#ff961e" class=""></path></g></svg>
                                </span>
                                <strong>Excellent</strong>
                            </label>
                        </div>
                    </div>
                    @if ($isFeedbackAdded)
                        <p class="small text-danger">Feedback already submitted</p>
                        <p>Please visit our website <a href="https://kgaelectronics.com">here</a> </p>
                    @else
                        <button type="submit" class="input-btm-submit">Submit Feedback</button>    
                    @endif
                    
                    @error('feedback') <p class="small text-danger">{{ $message }}</p> @enderror
                </form>
            </div>
        </div>
    </section>
    <script type="text/javascript">
        function preventBack() {
            window.history.forward(); 
        }
          
        setTimeout("preventBack()", 0);
          
        window.onunload = function () { null };
    </script>
</body>
</html>