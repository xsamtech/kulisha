<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <!-- Meta Tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="author" content="xsamtech.com">
        <meta name="keywords" content="@lang('miscellaneous.keywords')">
        <meta name="kls-url" content="{{ getWebURL() }}">
        <meta name="kls-api-url" content="{{ getApiURL() }}">
        <meta name="kls-visitor" content="{{ !empty(Auth::user()) ? Auth::user()->id : null }}">
        <meta name="kls-ref" content="{{ (!empty(Auth::user()) ? Auth::user()->api_token : 'nat') . '-' . (request()->has('app_id') ? request()->get('app_id') : 'nai') }}">
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
        {{-- <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"> --}}
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/addons/social/font-awesome/css/all.min.css') }}">
        {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"> --}}
        <link rel="stylesheet" href="{{ asset('assets/addons/social/bootstrap-icons/bootstrap-icons.min.css') }}">

        <!-- Material Design for Boostrap CSS -->
        {{-- <link rel="stylesheet" type="text/css" href="{{ asset('assets/addons/custom/mdb/css/mdb.min.css') }}"> --}}
        <!-- Overlay Scrollbars CSS -->
		<link rel="stylesheet" type="text/css" href="{{ asset('assets/addons/social/OverlayScrollbars-master/css/OverlayScrollbars.min.css') }}">
        <!-- Tiny Slider CSS -->
		<link rel="stylesheet" type="text/css" href="{{ asset('assets/addons/social/tiny-slider/dist/tiny-slider.css') }}">
        <!-- Choices CSS -->
		<link rel="stylesheet" type="text/css" href="{{ asset('assets/addons/social/choices.js/public/assets/styles/choices.min.css') }}">
        <!-- Glightbox CSS -->
		<link rel="stylesheet" type="text/css" href="{{ asset('assets/addons/social/glightbox-master/dist/css/glightbox.min.css') }}">
        <!-- Dropzone CSS -->
		<link rel="stylesheet" type="text/css" href="{{ asset('assets/addons/social/dropzone/dist/min/dropzone.min.css') }}">
        <!-- Flatpickr CSS -->
		<link rel="stylesheet" type="text/css" href="{{ asset('assets/addons/social/flatpickr/dist/flatpickr.min.css') }}">
        <!-- Plyr CSS -->
		<link rel="stylesheet" type="text/css" href="{{ asset('assets/addons/social/plyr/plyr.css') }}">
        <!-- Zuck CSS -->
		<link rel="stylesheet" type="text/css" href="{{ asset('assets/addons/social/zuck.js/dist/zuck.min.css') }}">

        <!-- Theme CSS -->
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/social/css/style.css') }}">

        <!-- Custom CSS -->
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/style.custom.css') }}">
        <style>
            .kls-fs-7 { font-size: 0.7rem; }
            .kls-text-secondary { color: var(--bs-secondary-text-emphasis); }
        </style>

        <title>
@if (Route::is('home'))
            Kulisha
@endif
@if (Route::is('cart.home'))
            @lang('miscellaneous.menu.public.orders.title')
@endif
@if (Route::is('notification.home'))
            @lang('miscellaneous.menu.notifications.title')
@endif
@if (Route::is('community.home'))
            @lang('miscellaneous.menu.public.communities.title')
@endif
@if (Route::is('event.home'))
            @lang('miscellaneous.menu.public.events.title')
@endif
@if (Route::is('message.home'))
            @lang('miscellaneous.menu.messages')
@endif
             / Demo
        </title>

    </head>

    <body>
        <!-- Pre loader -->
        <div class="preloader perfect-scrollbar">
            <div class="preloader-item">
                <div class="spinner-grow text-primary"></div>
            </div>
        </div>

        <!-- Responsive navbar toggler -->
        <button class="close-navbar-toggler navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation"></button>

        <!-- ======================= Header START -->
        <header class="navbar-light bg-mode fixed-top">
            <!-- Logo Nav START -->
            <nav class="navbar navbar-icon navbar-expand-lg">
                <div class="container">
                    <!-- Logo START -->
                    <a class="navbar-brand" href="{{ route('home') }}">
                        <img class="navbar-brand-item" src="{{ asset('assets/img/logo-text.png') }}" alt="Kulisha">
                        {{-- <img class="light-mode-item navbar-brand-item" src="{{ asset('assets/img/brand.png') }}" alt="Kulisha">
                        <img class="dark-mode-item navbar-brand-item" src="{{ asset('assets/img/brand-reverse.png') }}" alt="Kulisha"> --}}
                    </a>
                    <!-- Logo END -->

                    <!-- Responsive navbar toggler -->
                    <button class="navbar-toggler ms-auto icon-md btn btn-light p-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-animation">
                            <span></span>
                            <span></span>
                            <span></span>
                        </span>
                    </button>

                    <!-- Main navbar START -->
                    <div class="collapse navbar-collapse" id="navbarCollapse">
                        <ul class="navbar-nav navbar-nav-scroll mx-auto">
                            <!-- Home -->
                            <li class="nav-item">
                                <a class="nav-link{{ Route::is('home') ? ' active' : '' }}" href="{{ route('home') }}" title="@lang('miscellaneous.menu.home')" data-bs-toggle="tooltip" data-bs-placement="bottom">
@if (!request()->route()->named('home'))
                                    <div class="badge-notif badge-notif-bottom"></div>
@endif
                                    <i class="bi {{ Route::is('home') ? 'bi-house-fill' : 'bi-house' }}"></i> <span class="nav-text">@lang('miscellaneous.menu.home')</span>
                                </a>
                            </li>

                            <!-- Discover -->
                            <li class="nav-item dropdown">
                                <a class="nav-link{{ Route::is('discover.home') ? ' active' : '' }}" href="{{ route('discover.home') }}" title="@lang('miscellaneous.menu.discover')" data-bs-toggle="tooltip" data-bs-placement="bottom">
{{-- @if (!request()->route()->named('cart.home'))
                                    <div class="badge-notif badge-notif-bottom"></div>
@endif --}}
                                    <i class="bi {{ Route::is('discover.home') ? 'bi-compass-fill' : 'bi-compass' }}"></i> <span class="nav-text">@lang('miscellaneous.menu.discover')</span>
                                </a>
                            </li>

                            <!-- Orders -->
                            <li class="nav-item dropdown">
                                <a class="nav-link{{ Route::is('cart.home') ? ' active' : '' }}" href="{{ route('cart.home') }}" title="@lang('miscellaneous.menu.public.orders.title')" data-bs-toggle="tooltip" data-bs-placement="bottom">
{{-- @if (!request()->route()->named('cart.home'))
                                    <div class="badge-notif badge-notif-bottom"></div>
@endif --}}
                                    <i class="bi {{ Route::is('cart.home') ? 'bi-basket3-fill' : 'bi-basket3' }}"></i> <span class="nav-text">@lang('miscellaneous.menu.public.orders.title')</span>
                                </a>
                            </li>

                            <!-- Notifications -->
                            <li class="nav-item dropdown">
                                <a class="nav-link{{ Route::is('notification.home') ? ' active' : '' }}" href="{{ route('notification.home') }}" title="@lang('miscellaneous.menu.notifications.title')" data-bs-toggle="tooltip" data-bs-placement="bottom">
@if (!request()->route()->named('notification.home'))
                                    <div class="badge-notif badge-notif-bottom"></div>
@endif
                                    <i class="bi {{ Route::is('notification.home') ? 'bi-bell-fill' : 'bi-bell' }}"></i> <span class="nav-text">@lang('miscellaneous.menu.notifications.title')</span>
                                </a>
                            </li>

                            <!-- Communties -->
                            <li class="nav-item">
                                <a class="nav-link{{ Route::is('community.home') ? ' active' : '' }}" href="{{ route('community.home') }}" title="@lang('miscellaneous.menu.public.communities.title')" data-bs-toggle="tooltip" data-bs-placement="bottom">
{{-- @if (!request()->route()->named('community.home'))
                                    <div class="badge-notif badge-notif-bottom"></div>
@endif --}}
                                    <i class="bi {{ Route::is('community.home') ? 'bi-people-fill' : 'bi-people' }}"></i> <span class="nav-text">@lang('miscellaneous.menu.public.communities.title')</span>
                                </a>
                            </li>

                            <!-- Events -->
                            <li class="nav-item">
                                <a class="nav-link{{ Route::is('event.home') ? ' active' : '' }}" href="{{ route('event.home') }}" title="@lang('miscellaneous.menu.public.events.title')" data-bs-toggle="tooltip" data-bs-placement="bottom">
{{-- @if (!request()->route()->named('event.home'))
                                    <div class="badge-notif badge-notif-bottom"></div>
@endif --}}
                                    <i class="bi {{ Route::is('event.home') ? 'bi-calendar-event-fill' : 'bi-calendar-event' }}"></i> <span class="nav-text">@lang('miscellaneous.menu.public.events.title')</span>
                                </a>
                            </li>

                            <!-- Messaging -->
                            <li class="nav-item">
                                <a class="nav-link{{ Route::is('message.home') ? ' active' : '' }}" href="{{ route('message.home') }}" title="@lang('miscellaneous.menu.messages')" data-bs-toggle="tooltip" data-bs-placement="bottom">
@if (!request()->route()->named('message.home'))
                                    <div class="badge-notif badge-notif-bottom"></div>
@endif
                                    <i class="bi {{ Route::is('message.home') ? 'bi-chat-quote-fill' : 'bi-chat-quote' }}"></i> <span class="nav-text">@lang('miscellaneous.menu.messages')</span>
                                </a>
                            </li>

                            <li class="nav-item ms-3 opacity-0 d-md-inline-block d-none">
                                <a class="nav-link">
                                    <i class="bi bi-three-dots-vertical"></i> <span class="nav-text"></span>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <!-- Main navbar END -->

                    <!-- Nav right START -->
                    <ul class="nav flex-nowrap align-items-center ms-auto list-unstyled">
                        <li class="nav-item ms-2 dropdown nav-search">
                            <a class="nav-link btn icon-md p-0" href="#" id="searchDropdown" role="button" data-bs-auto-close="outside" data-bs-display="static" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-search fs-5"> </i>
                            </a>

                            <div class="dropdown-menu dropdown-animation dropdown-menu-end p-3 small" aria-labelledby="searchDropdown">
                                <!-- Profile info -->
                                <div class="nav flex-nowrap align-items-center">
                                    <div class="nav-item w-100">
                                        <form class="rounded position-relative">
                                            <input class="form-control ps-5 bg-light" type="search" placeholder="Search..." aria-label="Search">
                                            <button class="btn bg-transparent px-2 py-0 position-absolute top-50 start-0 translate-middle-y" type="submit"><i class="bi bi-search fs-5"></i></button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </li>

                        <li class="nav-item ms-2 dropdown">
                            <a role="button" class="nav-link btn icon-md p-0" id="profileDropdown" role="button" data-bs-auto-close="outside" data-bs-display="static" data-bs-toggle="dropdown" aria-expanded="false">
                                <img class="avatar-img rounded-circle" src="{{ asset('assets/img/template/avatar/07.jpg') }}" alt>
                            </a>

                            <ul class="dropdown-menu dropdown-animation dropdown-menu-end pt-3 small me-md-n3" aria-labelledby="profileDropdown">
                                <!-- Profile info -->
                                <li class="px-3">
                                    <div class="d-flex align-items-center position-relative">
                                        <!-- Avatar -->
                                        <div class="avatar me-3">
                                            <img class="avatar-img rounded-circle" src="assets/img/template/avatar/07.jpg" alt="avatar">
                                        </div>
                                        <div>
                                            <a class="h6 stretched-link" href="{{ route('profile.home', ['username' => 'tonystark']) }}">{{ Str::limit('Robert Downey Jr.', 14, '...') }}</a>
                                            <p class="small m-0">@tonystark</p>
                                        </div>
                                    </div>

                                    <a class="dropdown-item btn btn-primary-soft btn-sm mt-3 mb-2 text-center rounded-pill" href="{{ route('profile.home', ['username' => 'tonystark']) }}">@lang('miscellaneous.menu.public.profile.title')</a>
                                </li>

                                <!-- Links -->
                                <li>
                                    <a class="dropdown-item" href="{{ route('settings.home') }}"><i class="bi bi-gear fa-fw me-2"></i>@lang('miscellaneous.menu.public.settings.title')</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="https://xsamtech.com/messenger" target="_blank">
                                        <i class="fa-fw bi bi-telephone me-2"></i>@lang('miscellaneous.public.home.help')
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="https://xsamtech.com/products/kulisha" target="_blank">
                                        <i class="fa-fw bi bi-question-circle me-2"></i>@lang('miscellaneous.menu.about')
                                    </a>
                                </li>
                                <li class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item bg-danger-soft-hover" href="{{ route('logout') }}">
                                        <i class="bi bi-power fa-fw me-2"></i>@lang('miscellaneous.logout')
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <!-- Dark mode options START -->
                                <li>
                                    <div id="themeToggler" class="modeswitch-item theme-icon-active d-flex justify-content-center gap-2 align-items-center p-3 pb-0">
                                        <span>@lang('miscellaneous.theme')</span>
                                        <button type="button" class="btn btn-light light"  data-mdb-ripple-init><i class="bi bi-sun"></i></button>
                                        <button type="button" class="btn btn-light dark"  data-mdb-ripple-init><i class="bi bi-moon-fill"></i></button>
                                        <button type="button" class="btn btn-light auto"  data-mdb-ripple-init><i class="bi bi-circle-half"></i></button>
                                    </div>
                                </li>
                                <!-- Dark mode options END-->
                            </ul>
                        </li>
                    </ul>
                    <!-- Nav right END -->
                </div>
            </nav>
            <!-- Logo Nav END -->
        </header>
        <!-- ======================= Header END -->

        <!-- **************** MAIN CONTENT START **************** -->
        <main>
            <!-- Container START -->
            <div class="container">
                <div class="row g-4">

@yield('app-content')

                </div>
            </div>
            <!-- Container END -->
        </main>
        <!-- **************** MAIN CONTENT END **************** -->


        <!-- Modal create Feed START -->
        <div class="modal fade" id="modalCreateFeed" tabindex="-1" aria-labelledby="modalLabelCreateFeed" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                <!-- Modal feed header START -->
                <div class="modal-header pb-0 border-bottom-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <!-- Modal feed header END -->

                <!-- Modal feed body START -->
                <div class="modal-body pt-0">
                    <!-- Check One Feed Type -->
                    <div class="d-flex justify-content-center mb-3">
                        <div role="button" class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio1" value="option1" checked>
                            <label class="form-check-label" for="inlineRadio1">Produit</label>
                        </div>
                        <div role="button" class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio2" value="option2">
                            <label class="form-check-label" for="inlineRadio2">Service</label>
                        </div>
                    </div>

                    <!-- Add Feed Text -->
                    <div class="d-flex mb-3">
                        <!-- Avatar -->
                        <div class="avatar avatar-xs me-2">
                            <img class="avatar-img rounded-circle" src="{{ asset('assets/img/template/avatar/07.jpg') }}" alt>
                        </div>
                        <!-- Feed box  -->
                        <form class="w-100">
                            <textarea class="form-control pe-4 fs-3 lh-1 border-0" rows="4" placeholder="Exprimez-vous ..." autofocus></textarea>
                        </form>
                    </div>

                    <!-- Other Feed Data -->
                    <div class="hstack gap-2 justify-content-center">
                        <a class="icon-md bg-success bg-opacity-10 rounded-circle text-success" href="#" data-bs-toggle="tooltip" data-bs-placement="top" title="Image">
                            <i class="bi bi-image"></i>
                        </a>
                        <a class="icon-md bg-info bg-opacity-10 rounded-circle text-info" href="#" data-bs-toggle="tooltip" data-bs-placement="top" title="Document">
                            <i class="bi bi-file-earmark-text"></i>
                        </a>
                        <a class="icon-md bg-danger bg-opacity-10 rounded-circle text-danger" href="#" data-bs-toggle="tooltip" data-bs-placement="top" title="GIF">
                            <i class="bi bi-filetype-gif"></i>
                        </a>
                        <a class="icon-md bg-secondary bg-opacity-10 rounded-circle text-secondary" href="#" data-bs-toggle="tooltip" data-bs-placement="top" title="Localisation">
                            <i class="bi bi-geo-alt-fill"></i>
                        </a>
                        <a class="icon-md bg-warning bg-opacity-10 rounded-circle text-warning" href="#" data-bs-toggle="tooltip" data-bs-placement="top" title="Emoji">
                            <i class="bi bi-emoji-smile-fill"></i>
                        </a>
                    </div>

                    <!-- Select Feed categories -->
                    <div class="mt-3 text-center">
                        <input type="checkbox" class="btn-check" id="btn-check-1" autocomplete="off">
                        <label class="small btn btn-light m-2 rounded-pill" for="btn-check-1">Matériel d’agriculture</label>

                        <input type="checkbox" class="btn-check" id="btn-check-2" autocomplete="off">
                        <label class="small btn btn-light m-2 rounded-pill" for="btn-check-2">Plante et semence</label>

                        <input type="checkbox" class="btn-check" id="btn-check-3" autocomplete="off">
                        <label class="small btn btn-light m-2 rounded-pill" for="btn-check-3">Produit transformé</label>

                        <input type="checkbox" class="btn-check" id="btn-check-4" autocomplete="off">
                        <label class="small btn btn-light m-2 rounded-pill" for="btn-check-4">Produit extrait</label>
                    </div>
                </div>
                <!-- Modal feed body END -->

                <!-- Modal feed footer -->
                <div class="modal-footer px-3 row justify-content-between">
                    <!-- Select -->
                    <div class="col-lg-4">
                        <div class="dropdown">
                            <a href="#" class="text-secondary dropdown-toggle btn btn-secondary-soft py-1 px-2 rounded-pill" id="toggleVisibility" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-earth-africa fs-6"></i>
                            </a>

                            <!-- Visibility dropdown menu -->
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="toggleVisibility">
                                <li>
                                    <a class="dropdown-item 1" href="#"><i class="fa-solid fa-earth-africa me-2"></i>Tout le monde</a>
                                </li>
                                <li>
                                    <a class="dropdown-item 2" href="#"><i class="fa-solid fa-user-secret me-2"></i>Moi uniquement</a>
                                </li>
                                <li>
                                    <a class="dropdown-item 3" href="#"><i class="fa-solid fa-user-gear me-2"></i>Tout le monde, sauf ...</a>
                                </li>
                                <li>
                                    <a class="dropdown-item 4" href="#"><i class="fa-solid fa-user-gear me-2"></i>Personne, sauf …</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <!-- Button -->
                    <div class="col-lg-6 text-sm-end">
                        <button type="button" class="btn btn-success-soft">
                            <i class="bi bi-send me-1"></i> @lang('miscellaneous.post')
                        </button>
                    </div>
                </div>
                <!-- Modal feed footer -->

                </div>
            </div>
        </div>
        <!-- Modal create feed END -->

        <!-- ======================= JS libraries, plugins and custom scripts -->
        <!-- jQuery JS -->
        <script src="{{ asset('assets/addons/custom/jquery/js/jquery.min.js') }}"></script>
        <!-- Bootstrap JS -->
        <script src="{{ asset('assets/addons/social/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
        <!-- Tiny slider -->
        <script src="{{ asset('assets/addons/social/tiny-slider/dist/tiny-slider.js') }}"></script>
        <!-- PswMeter -->
        <script src="{{ asset('assets/addons/social/pswmeter/pswmeter.min.js') }}"></script>
        <!-- Overlay scrollbars -->
        <script src="{{ asset('assets/addons/social/OverlayScrollbars-master/js/OverlayScrollbars.min.js') }}"></script>
        <!-- Choices -->
        <script src="{{ asset('assets/addons/social/choices.js/public/assets/scripts/choices.min.js') }}"></script>
        <!-- Glightbox -->
        <script src="{{ asset('assets/addons/social/glightbox-master/dist/js/glightbox.min.js') }}"></script>
        <!-- Flatpickr -->
        <script src="{{ asset('assets/addons/social/flatpickr/dist/flatpickr.min.js') }}"></script>
        <!-- Plyr -->
        <script src="{{ asset('assets/addons/social/plyr/plyr.js') }}"></script>
        <!-- Dropzone -->
        <script src="{{ asset('assets/addons/social/dropzone/dist/min/dropzone.min.js') }}"></script>
        <!-- Zuck -->
        <script src="{{ asset('assets/addons/social/zuck.js/dist/zuck.min.js') }}"></script>
        <script src="{{ asset('assets/js/social/zuck-stories.js') }}"></script>
        <!-- Theme Functions -->
        <script src="{{ asset('assets/js/social/functions.js') }}"></script>
        <!-- Autoresize textarea -->
        <script src="{{ asset('assets/addons/custom/autosize/js/autosize.min.js') }}"></script>
        <!-- Perfect scrollbar -->
        <script src="{{ asset('assets/addons/custom/perfect-scrollbar/dist/perfect-scrollbar.min.js') }}"></script>
        <!-- Scroll forever -->
        <script src="{{ asset('assets/addons/custom/jquery/scroll4ever/js/jquery.scroll4ever.js') }}"></script>
        <!-- Custom scripts -->
        <script src="{{ asset('assets/js/load-app-scripts.js') }}"></script>
        <script src="{{ asset('assets/js/script.app.js') }}"></script>
    </body>
</html>
