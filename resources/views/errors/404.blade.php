<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" type="text/css" href="{{ asset('patternfly/patternfly.min.css') }}">

    <title>Oops! Not Found :(</title>

    <style>
        html,
        body {
            height: 100%;
        }

        body {
            margin: 0;
            padding: 0;
            width: 100%;
            color: #98a1a6;
            display: table;
            font-weight: 100;
            font-family: 'Open Sans';
        }

        .container {
            text-align: center;
            display: table-cell;
            vertical-align: middle;
        }

        .content {
            text-align: center;
            display: inline-block;
            font-size: 25px;
        }

        .tagline {
            text-align: center;
            display: inline-block;
            font-size: 18px;
        }

        .title {
            font-size: 72px;
            margin-bottom: 40px;
            font-weight: 300;
            margin-top: 30px;
        }

        .error-page {
            background: url('/images/background.jpg');
            background-size: cover;
        }
    </style>
</head>

<body>
    <div class="container error-page">
        <div class="content">
            <img src="{{ asset('images/404.png') }}" width="35%" class="img-responsive center-block">
            <div class="title">Oops!</div>
            <div class="content">Sorry, this page doesn't exist.</div>
            <br>
            <div class="tagline">Back to <a href="/">dashboard.</a></div>
            <br>
        </div>
    </div>
</body>

</html>