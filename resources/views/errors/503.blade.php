<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" type="text/css" href="{{ asset('patternfly/patternfly.min.css') }}">

    <title>Be right back.</title>

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
            font-size: 30px;
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
            background: url('images/background.jpg');
            background-size: cover;
        }
    </style>
</head>

<body>
    <div class="container error-page">
        <div class="content">
            <img src="{{ asset('images/503.png') }}" width="30%" class="img-responsive center-block">
            <div class="title">Sorry!</div>
            <div class="content">We'll be right back.</div>
            <br>
            <div class="tagline">We are performing some maintenance. We should be back online shortly.</div>
            <br>
        </div>
    </div>
</body>

</html>