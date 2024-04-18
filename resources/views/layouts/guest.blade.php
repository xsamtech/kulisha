<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <!-- Meta Tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="author" content="xsamtech.com">
        <meta name="keywords" content="@lang('miscellaneous.keywords')">
        <meta name="dktv-url" content="{{ getWebURL() }}">
        <meta name="dktv-api-url" content="{{ getApiURL() }}">
        <meta name="dktv-visitor" content="{{ !empty(Auth::user()) ? Auth::user()->id : null }}">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="dktv-ref" content="{{ !empty(Auth::user()) ? Auth::user()->api_token : null }}">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="">

        <!-- Favicon -->
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/img/favicon/apple-touch-icon.png') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/img/favicon/favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/img/favicon/favicon-16x16.png') }}">
        <link rel="manifest" href="{{ asset('assets/img/favicon/site.webmanifest') }}">

        <!-- Google Font -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">

        <!-- Plugins CSS -->
        <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

        <!-- Theme CSS -->
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/social/css/style.css') }}">

        <!-- Custom CSS -->
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/style.custom.css') }}">
@if (request()->has('app_id'))
        <style>
            .detect-webview { display: none;!important }
        </style>
@endif
        <title>
@if (!empty($exception))
            {{ $exception->getStatusCode() . ' - ' . __('notifications.' . $exception->getStatusCode() . '_title') }}
@else
    @if (!empty($error_title) || \Session::has('error_message') || \Session::has('error_message_login'))
            {{ !empty($error_title) ? $error_title : (\Session::has('error_message_login') ? preg_match('/~/', \Session::get('error_message_login')) ? explode(', ', explode('~', \Session::get('error_message_login'))[0])[2] : \Session::get('error_message_login') : (\Session::has('error_message') ? (preg_match('/~/', \Session::get('error_message')) ? explode('-', explode('~', \Session::get('error_message'))[0])[2] : \Session::get('error_message')) : '')) }}
    @endif

    @if (empty($error_title) && Session::get('error_message') == null)
        @if (!empty($children))
            @lang('auth.select-your-profile')
        @endif
        @if (Route::is('login'))
            @if (request()->has('check_param'))
                @if (request()->get('check_param') == 'email')
            @lang('auth.verified-email')
                @endif
                @if (request()->get('check_param') == 'phone')
            @lang('auth.verified-phone')
                @endif
            @else
            Se connecter
            {{-- @lang('auth.login') --}}
            @endif
		@endif

		@if (Route::is('register') || !empty($request->temporary_user_id))
            @if (!empty($token_sent))
            @lang('auth.otp-code')
            @else
                @if (!empty($temporary_user))
            @lang('miscellaneous.account.personal_infos.title')
                @else
                    @if (!empty($request->redirect))
                        @if (request()->has('check'))
                            @if (request()->get('check') == 'email')
            @lang('auth.verify-email')
                            @endif
                            @if (request()->get('check') == 'phone')
            @lang('auth.verify-phone')
                            @endif
                        @else
            @lang('auth.reset-password')
                        @endif
                    @else
            @lang('auth.register')
                    @endif
                @endif
            @endif
		@endif

		@if (Route::is('password.request') || !empty($former_password))
            @if (request()->has('check'))
                @if (request()->get('check') == 'email')
                    @lang('auth.verify-email')
                @endif
                @if (request()->get('check') == 'phone')
                    @lang('auth.verify-phone')
                @endif
            @else
                @lang('auth.reset-password')
            @endif
		@endif

        @if (!empty($token_sent))
            @lang('auth.otp-code')
        @endif
    @endif
@endif
        </title>

    </head>

    <body>
        <!-- Pre loader -->
        <div class="preloader">
            <div class="preloader-item">
                <div class="spinner-grow text-primary"></div>
            </div>
        </div>

        <!-- **************** MAIN CONTENT START **************** -->
        <main>
            <!-- Container START -->
            <div class="container">
                <div class="row mt-5 mb-4">
                    <div class="col-lg-3 col-sm-4 col-8 mx-auto">
                        <div class="bg-image">
                            <img src="{{ asset('assets/img/logo-text.png') }}" alt="Kulisha" class="img-fluid">
                            <div class="mask"><a href="{{ route('home') }}"></a></div>
                        </div>
                    </div>
                </div>

@yield('guest-content')

            </div>
            <!-- Container END -->
        </main>
        <!-- **************** MAIN CONTENT END **************** -->

        <!-- =======================Footer START -->
        <footer class="pt-2 pb-sm-4 pb-2 position-relative bg-mode">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-sm-10 col-md-8 col-lg-6">
                        <div class="d-grid d-sm-flex justify-content-center justify-content-sm-between align-items-center mt-3">
                            <!-- Terms -->
                            <ul class="nav">
                                <li class="nav-item"><a class="nav-link fw-bold ps-0 pe-2" href="#">Conditions</a></li>
                                <li class="nav-item"><a class="nav-link fw-bold px-2" href="#">Confidentialité</a></li>
                                <li class="nav-item"><a class="nav-link fw-bold px-2" href="#">Cookies</a></li>
                            </ul>

                            <!-- Theme toggle -->
                            <div class="d-flex justify-content-center">
                                <div role="group" id="themeToggler" class="btn-group shadow-0" aria-label="Theme toggler">
                                    <button type="button" class="btn btn-light light"  data-mdb-ripple-init><i class="bi bi-sun"></i></button>
                                    <button type="button" class="btn btn-light dark"  data-mdb-ripple-init><i class="bi bi-moon-fill"></i></button>
                                    <button type="button" class="btn btn-light auto"  data-mdb-ripple-init><i class="bi bi-circle-half"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <!-- ======================= Footer END -->

        <!-- ======================= JS libraries, plugins and custom scripts -->
        <!-- Bootstrap JS -->
        <script src="{{ asset('assets/addons/social/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
        <!-- Vendors -->
        <script src="{{ asset('assets/addons/social/pswmeter/pswmeter.min.js') }}"></script>
        <!-- Theme Functions -->
        <script src="{{ asset('assets/js/social/functions.js') }}"></script>
    </body>
</html>
