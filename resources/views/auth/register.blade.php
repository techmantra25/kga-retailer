<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="{{url('assets')}}/css/bootstrap.min.css" rel="stylesheet">
        <link href="{{url('assets')}}/css/style.css" rel="stylesheet">

        <title>KGA | Master Login</title>
    </head>
    <body>
        <main class="login">
            <div class="login__left">
                <img src="{{url('assets')}}/images/kga_big_logo.jpg">
            </div>
            <div class="login__right">
                <div class="login__block">
                    <div class="logo__block">
                        <img src="{{url('assets')}}/images/kga_logo.png">
                    </div>
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
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
                            {{-- <div class="col-6 text-end">
                                <a href="#">Forgot Password?</a>
                            </div> --}}
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-lg btn-theme">{{ __('Login') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
        <script src="{{url('assets')}}/js/bootstrap.bundle.min.js"></script>
    </body>
</html>