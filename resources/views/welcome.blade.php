<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="keywords" content="">
        <meta name="author" content="Awaiken Theme">
        <!-- Favicon -->
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/img/favicon/apple-touch-icon.png') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/img/favicon/favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/img/favicon/favicon-16x16.png') }}">
        <link rel="manifest" href="{{ asset('assets/img/favicon/site.webmanifest') }}">
        <!-- Google Fonts css-->
        <link href="https://fonts.googleapis.com/css?family=Roboto+Condensed:300,400,700" rel="stylesheet">
        <!-- Bootstrap css -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" media="screen">
        <!-- Font Awesome icon css-->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet" media="screen">
        <!-- Main custom css -->
        <link href="{{ asset('assets/addons/launch-under-maintenance/css/custom.css') }}" rel="stylesheet" media="screen">
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
            <![endif]-->

        <!-- Page Title -->
        <title>Kulisha</title>
    </head>

    <body class="antialiased">
        <!-- Coming Soon Wrapper starts -->
        <div class="comming-soon">

            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="coming-soon-box">
                            <!-- Logo Start -->
                            <div class="logo">
                                <img src="{{ asset('assets/img/logo-text.png') }}" alt="Kulisha" width="200">
                            </div>
                            <!-- Logo end -->

                            <!-- Types Information start -->
                            <div class="coming-text">
                                <p>Pr&eacute;parez-vous, nous allons</p>
                                <h2><span class="typed-title">Lancer le site dans</span></h2>
                                <div class="typing-title">
                                    <p>Lancer le site dans</p>
                                    <p>Débuter bient&ocirc;t</p>
                                </div>
                            </div>
                            <!-- Types Information end -->

                            <!-- Countdown start -->
                            <div class="countdown-timer-wrapper">
                                <div class="timer" id="countdown"></div>
                            </div>
                            <!-- Countdown end -->

                            <!-- Newsletter Form start -->
                            <div class="newsletter">
                                <p>Laissez-nous votre adresse E-mail pour être informé au Jour-J</p>

                                <div class="newsletter-form">
                                    <form action="#" method="post">
                                        <input type="text" class="new-text" placeholder="Entrer adresse e-mail ..."required />
                                        <button type="submit" class="new-btn">Notifiez-moi</button>
                                    </form>
                                </div>
                            </div>
                            <!-- Newsletter Form end -->

                            <!-- Social Media start -->
                            <div class="social-link">
                                <a href="#"><i class="fa fa-facebook"></i></a>
                                <a href="#"><i class="fa fa-twitter"></i></a>
                                <a href="#"><i class="fa fa-linkedin"></i></a>
                                <a href="#"><i class="fa fa-pinterest"></i></a>
                                <a href="#"><i class="fa fa-instagram"></i></a>
                            </div>
                            <!-- Social Media end -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Coming Soon Wrapper end -->

        <!-- Jquery Library File -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <!-- Timer counter js file -->
        <script src="{{ asset('assets/addons/launch-under-maintenance/js/countdown-timer.js') }}"></script>
        <!-- Typed js file -->
        <script src="{{ asset('assets/addons/launch-under-maintenance/js/typed.js') }}"></script>
        <!-- SmoothScroll -->
        <script src="{{ asset('assets/addons/launch-under-maintenance/js/SmoothScroll.js') }}"></script>
        <!-- Bootstrap js file -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
        <!-- Main Custom js file -->
        <script src="{{ asset('assets/addons/launch-under-maintenance/js/function.js') }}"></script>
        <!-- Timer counter start -->
        <script>
            $(document).ready(function() {
                //var myDate = new Date("08/04/2019");
                var myDate = new Date("15/01/2024");
                myDate.setDate(myDate.getDate());
                $("#countdown").countdown(myDate, function(event) {
                    $(this).html(
                        event.strftime(
                            '<div class="timer-wrapper"><div class="time">%D</div><span class="text">Jours</span></div><div class="timer-wrapper"><div class="time">%H</div><span class="text">Heures</span></div><div class="timer-wrapper"><div class="time">%M</div><span class="text">Minutes</span></div><div class="timer-wrapper"><div class="time">%S</div><span class="text">Secondes</span></div>'
                        )
                    );
                });
            });
        </script>
        <!-- Timer counter end -->
    </body>
</html>
