<!doctype html>
<html lang="{{ app()->getLocale() }}" class="layout-pf layout-pf-fixed">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>
        @yield('title')
    </title>
    <link rel="icon" href="{{ asset('images/logo.png') }}" />

    <link rel="stylesheet" type="text/css" href="{{ asset('css/all.css') }}">

    <link rel="stylesheet" type="text/css" href="{{ asset('css/daterangepicker.css') }}">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        .backgroundBody {
            background-color: #f5f5f5;
        }

        html {
            height: 100%;
        }

        body {
            position: relative;
            padding-bottom: 6rem;
        }

        .footer {
            position: absolute;
            right: 0;
            bottom: 0;
            left: 0;
            padding: 1rem;
            background-color: #ffffff;
            text-align: left;
        }

        .swal2-popup {
            font-size: 1.6rem !important;
        }

        .btn-ml {
            margin-left: 10px;
        }

        .align-middle {
            text-align: center;
            vertical-align: top !important;
        }
    </style>

    <link rel="stylesheet" type="text/css" href="{{ asset('jstree/dist/themes/default/style.css') }}" />

</head>

<body class="backgroundBody">
    <nav class="navbar navbar-pf-vertical">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a href="/" class="navbar-brand">
                {{--Please change <p> tag to your Project Logo in white monochrome version with uncomment the <img> tag--}}
                <!-- <h2 style="margin-top: 4px; margin-bottom: 0px"><b>[Chemical Store]</h2> -->
                {{--<img class="navbar-brand-icon" src="{{ asset('/images/logo.png') }}" alt=""/>--}}
            </a>
        </div>
        <nav class="collapse navbar-collapse">
            <ul class="nav navbar-nav navbar-right navbar-iconic">
                <li class="dropdown">
                    <a class="dropdown-toggle nav-item-iconic" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                        <span title="Username" class="pficon pficon-user"></span>
                        <span class="dropdown-title">
                            {{ Auth::user()->full_name }} - {{ Auth::user()->role->display_name }}
                            <b class="caret"></b>
                        </span>

                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="{{ url('user/profile') }}">My Account</a>
                        </li>
                        <li>
                            <a href="{{ route('logout') }}" onclick="event.preventDefault();
                        document.getElementById('logout-form').submit();">
                                Logout
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
    </nav>
    <!--/.navbar-->

    <div class="nav-pf-vertical nav-pf-vertical-with-sub-menus">
        @include('layouts.sidebar.menu')
    </div>

    <div class="container-pf-nav-pf-vertical hide-nav-pf">
        @include('common.error')
        @include('flash::message')
    </div>

    <div class="container-fluid container-pf-nav-pf-vertical hide-nav-pf">
        @yield('content')
    </div>

    <div class="footer container-fluid container-pf-nav-pf-vertical hide-nav-pf">
        <div class="row row-cards-pf">
            <div class="col-md-6">
                <strong>Copyright &copy; {{ config('example.year_created') }}{{(date('Y') > config('example.year_created') ? ' - '.date('Y') : '')}}. <a href="#">Chemical Store</a>.</strong>
            </div>
            <div class="col-md-6 text-right hidden-xs hidden-sm">
                Version <strong>{{ config('example.app_version') }}</strong>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/patternfly.js') }}"></script>

    <script src="{{ asset('jstree/dist/jstree.js') }}"></script>

    <script src="{{ asset('js/datatable/dataTable.select.js') }}"></script>

    <script src="{{ asset('js/datatable/buttons.js') }}"></script>

    <script src="{{ asset('js/tabledit.js') }}"></script>

    <script src="{{ asset('js/jspdf.js') }}"></script>

    <script src="{{ asset('js/html2canvas.js') }}"></script>

    <script src="{{ asset('js/moment.js') }}"></script>

    <script src="{{ asset('js/daterangepicker.js') }}"></script>

    <script>
        $(document).ready(function() {
            $().setupVerticalNavigation(true);
        });
    </script>
    @yield('script')
</body>

</html>