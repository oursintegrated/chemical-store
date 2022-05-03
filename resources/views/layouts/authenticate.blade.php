<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title')</title>
    <link rel="icon" href="{{ asset('images/yogyalogo(Y).png') }}"/>

    <link rel="stylesheet" type="text/css" href="{{ asset('css/all.css') }}">

    <style>
        @font-face {
            font-family: Open Sans;
        }
    </style>

    <style type="text/css">
        :root {
            --background: #D2D6DF;
        }

        .login-page {
            background: var(--background);
            background-size: cover;
        }

        .swal2-popup {
            font-size: 1.6rem !important;
        }
    </style>
</head>
<body class="login-page">
<div class="login-pf-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3 col-lg-6 col-lg-offset-3">
                <header class="login-pf-page-header">

                </header>
                @yield('content')
            </div><!-- col -->
        </div><!-- row -->
    </div><!-- container -->
</div><!-- login-pf-page -->

<script src="{{ asset('js/patternfly.js') }}"></script>
<script>
    // Random image cover
    var image = Math.floor((Math.random() * 6) + 1); // 6 indicates total images to randomize
    document.documentElement.style.setProperty("--background", "url('/images/"+ image + ".jpg')");
</script>
@yield('script')
</body>
</html>