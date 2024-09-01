<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Kulisha OTP</title>
    </head>

    <body style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif">
        <img src="{{ asset('assets/img/brand.png') }}" alt="Kulisha" width="100">

        <p style="margin-top: 3rem; margin-bottom: 1rem;">@lang('notifications.token_label')</p>
        <h1 style="font-weight: 900; color: black; background-color: #c3c3c3; padding: 20px;">{{ $token }}</h1>
    </body>
</html>