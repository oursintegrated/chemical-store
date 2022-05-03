<!doctype html>
<html lang="{{ app()->getLocale() }}" class="layout-pf layout-pf-fixed">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title')</title>
    <link rel="icon" href="{{ parse_url(asset('favicon_c.png'), PHP_URL_PATH) }}"/>

    <link rel="stylesheet" type="text/css" href="{{ parse_url(asset('patternfly/patternfly.min.css'), PHP_URL_PATH) }}">
    <link rel="stylesheet" type="text/css" href="{{ parse_url(asset('patternfly/patternfly-additions.min.css'), PHP_URL_PATH) }}">

    <!--select2 css-->
    <link rel="stylesheet" type="text/css" href="{{ parse_url(asset('patternfly/select2.min.css'), PHP_URL_PATH) }}">

    <!--sweetalert css-->
    <link rel="stylesheet" type="text/css" href="{{ parse_url(asset('patternfly/sweetalert2.min.css'), PHP_URL_PATH) }}">

    <!--datatable css-->
    <link rel="stylesheet" type="text/css" href="{{ parse_url(asset('css/dataTables.jqueryui.min.css'), PHP_URL_PATH) }}">

    <!--datetime picker-->
    <link rel="stylesheet" type="text/css" href="{{ parse_url(asset('css/bootstrap-datetimepicker.min.css'), PHP_URL_PATH) }}">

    <!--token-->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        .backgroundBody {
            -webkit-print-color-adjust: exact;

            background-color: #f5f5f5;
        }

        html {
            height: 100%;
        }

        body {
            position: relative;
            /*padding-bottom: 6rem;*/
            padding: 0 0 !important;
        }

        .font_mhd_form{
            font-size: 10pt;
        }

        .voucher {
            background-color: yellow;
            font-size: 7pt;
            text-align: right;
            margin-top: 5px;
        }

        @media print {
            .backgroundBody {
                -webkit-print-color-adjust: exact;
            }

            .tableInvoiceInternalDocument {
                font-size: 5pt;
            }

            .tableOrderList {
                font-size: 9pt;
            }

            .tableVoucher {
                margin-top: 2cm;
                width: 18cm;
                height: 7cm;
                font-size: 10pt;
            }

            .voucher {
                background-color: yellow !important;
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
</head>
<body class="backgroundBody">
<div class="container-fluid">
    @yield('content')
</div>

<script src="{{ parse_url(asset('patternfly/patternfly.js'), PHP_URL_PATH) }}"></script>

<script>
    $(document).ready(function () {
        $().setupVerticalNavigation(true);
    });
</script>
@yield('script')
</body>
</html>