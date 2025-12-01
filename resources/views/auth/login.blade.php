<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="{{url('assets')}}/css/bootstrap.min.css" rel="stylesheet">
        <link href="{{url('assets')}}/css/style.css" rel="stylesheet">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <title>KGA | {{ $title ?? "Master" }} Login</title>
    </head>
    <body oncontextmenu="return false">
        <main class="login">
            <div class="login__left">
                <img src="{{url('assets')}}/images/kga_big_logo.jpg">
            </div>
            <div class="login__right">
                <div class="login__block">
                    <div class="logo__block">
                        <img src="{{url('assets')}}/images/kga_logo.png">
                    </div>
                    
                    <div class="radio-wrapper">
                        <label for="master" class="custom-radio">
                            <input type="radio" name="logintype" id="master" value="master" onclick="location.href='{{ route('login') }}'" @if(!isset($route)) checked @endif >
                            <span>Master Login</span>
                        </label>
                        <label for="servicepartner" class="custom-radio">
                            <input type="radio" name="logintype" id="servicepartner" onclick="location.href='{{ route('servicepartnerweb.login-view') }}'" value="servicepartner"  @isset($route) checked   @endisset >
                            <span>Service Partner Login</span>
                        </label>
                    </div>
                    @if (Session::has('message'))
                    <div class="alert alert-info" role="alert">
                        {{ Session::get('message') }}
                    </div>
                    @endif
                    @isset($route)
                        <form method="POST" action="{{ $route }}">
                    @else
                    <form action="{{ route('masterLogin') }}" method="POST">
                    @endisset
                        @csrf
                        <input type="hidden" name="browser_name" class="browser_name">
                        <input type="hidden" name="navigator_useragent" class="navigator_useragent">
                        <div class="form-floating mb-3">
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" id="" required autocomplete="email" placeholder="{{ __('Email Address') }}" autofocus>
                            <label for="">{{ __('Email Address') }}</label>
                        </div>
                        @error('email')
                        <span class="invalid-feedback d-block" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                        <div class="form-floating mb-3">
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="" name="password" required autocomplete="current-password" placeholder="{{ __('Password') }}">
                            <label for="">{{ __('Password') }}</label>
                        </div>
                        @error('password')
                        <span class="invalid-feedback d-block" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror

                        <div class="row mb-3">
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>
                            
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-lg btn-theme">{{ __('Login') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
        <script src="{{url('assets')}}/js/bootstrap.bundle.min.js"></script>
        <script>
            function getBrowserType() {
                const test = regexp => {
                    return regexp.test(navigator.userAgent);
                };
                console.log(navigator.userAgent);
                var navigator_useragent = navigator.userAgent;
                $('.navigator_useragent').val(navigator_useragent);
                if (test(/opr\//i) || !!window.opr) {
                    return 'Opera';
                } else if (test(/edg/i)) {
                    return 'Microsoft Edge';
                } else if (test(/chrome|chromium|crios/i)) {
                    return 'Google Chrome';
                } else if (test(/firefox|fxios/i)) {
                    return 'Mozilla Firefox';
                } else if (test(/safari/i)) {
                    return 'Apple Safari';
                } else if (test(/trident/i)) {
                    return 'Microsoft Internet Explorer';
                } else if (test(/ucbrowser/i)) {
                    return 'UC Browser';
                } else if (test(/samsungbrowser/i)) {
                    return 'Samsung Browser';
                } else {
                    return 'Unknown browser';
                }
            }
            const browserType = getBrowserType();
            console.log(browserType);
            $('.browser_name').val(browserType);

            $(document).ready(function(){
                $('div.alert').delay(10000).slideUp(300);


                // Reload page for avoiding csrf token expiration
                const reloadVal = 119000; // 119 seconds 
                let idleTimer = null;
                idleTimer = setTimeout(function () {
                    location.reload(true);
                    console.log('Reloading ...');
                }, reloadVal);


            });

            // Restrict Right Click
            document.onkeydown = function(e) {
                if(e.keyCode == 123) {
                    return false;
                }
                if(e.ctrlKey && e.shiftKey && e.keyCode == 'I'.charCodeAt(0)){
                    return false;
                }
                if(e.ctrlKey && e.shiftKey && e.keyCode == 'J'.charCodeAt(0)){
                    return false;
                }
                if(e.ctrlKey && e.keyCode == 'U'.charCodeAt(0)){
                    return false;
                }
                if(e.ctrlKey && e.shiftKey && e.keyCode == 'C'.charCodeAt(0)){
                    return false;
                }
            }
        </script>
        
    </body>
</html>