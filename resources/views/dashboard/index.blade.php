@extends('layouts.backend')
@section('title', 'Chemical Store | Dashboard')
@section('content')
{{-- <div class="row row-cards-pf">--}}
{{-- <div class="row-cards-pf card-pf">--}}
{{-- <ol class="breadcrumb">--}}
{{-- <li class="active">--}}
{{-- <span class="pficon pficon-home"></span>--}}
{{-- <a href="{{url('home')}}">Dashboard</a>--}}
{{-- </li>--}}
{{-- </ol>--}}
{{-- </div>--}}
{{-- </div>--}}

<div class="row text-center">
    <div class="col-xs-12">
        <img width="50" src="{{ asset('images/logo.png') }}" alt=" logo" style="margin-top: 30px" />
        <h2><b>[Chemical Store]</b></h2>
        <p style="font-size: 1.2em;">Hi, <b><span class="text-capitalize">{{ Auth::user()->full_name }}</span></b>! Let's get started.</p><br>
    </div>
</div>

<div class="row row-cards-pf">
    <div class="col-xs-12">
        <div class="panel panel-warning" style="border-radius: 10px;">
            <div class="panel-heading" style="padding: 10px 10px; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                <h3 class="panel-title">Jatuh Tempo</h3>
            </div>
            <div class="panel-body" style="padding: 8px 10px !important;">
                <div class="row">
                    <div class="col-xs-6">
                        Customer Name, no order X jatuh tempo pada Y [No Telpon Customer]
                    </div>
                    <div class="col-xs-6" style="text-align: right;">
                        <button class="btn btn-primary">Action</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div><!-- /row -->
@endsection