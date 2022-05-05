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
        <!-- <img width="50" src="{{ asset('images/logo.png') }}" alt=" logo" style="margin-top: 30px" /> -->
        <h2><b>[Chemical Store]</b></h2>
        <p style="font-size: 1.2em;">Hi, <b><span class="text-capitalize">{{ Auth::user()->full_name }}</span></b>! Let's get started.</p><br>
    </div>
</div>

<div class="row row-cards-pf">
    <div class="col-xs-12">
        @if(isset($orders))
        @foreach($orders as $order)
        @if($order->day_left > 3 && $order->status == 0)
        <div class="panel panel-warning" style="border-radius: 10px;">
            <div class="panel-heading" style="padding: 10px 10px; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                <h3 class="panel-title">Due Date {{ $order->day_left }} days left</h3>
            </div>
            <div class="panel-body" style="padding: 8px 10px !important;">
                <div class="row">
                    <div class="col-xs-6">
                        <b>{{ $order->customer_name }}</b>, no order <b> <a target="_blank" href="/sales/{{ $order->id }}/detail">{{ $order->sales_code }}</a> </b> jatuh tempo pada <b>{{ $order->due_date_convert }} [{{ $order->phone_number }}] </b>
                    </div>
                    <div class="col-xs-6" style="text-align: right;">
                        <button class="btn btn-success" onclick="updateStatus({{$order->id}})"><i class="fa fa-check"></i></button>
                    </div>
                </div>
            </div>
        </div>
        @elseif($order->day_left < 3 && $order->status == 0)
            <!-- -->
            <div class="panel panel-danger" style="border-radius: 10px;">
                <div class="panel-heading" style="padding: 10px 10px; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                    <h3 class="panel-title">Due Date {{ $order->day_left }} days left</h3>
                </div>
                <div class="panel-body" style="padding: 8px 10px !important;">
                    <div class="row">
                        <div class="col-xs-6">
                            <b>{{ $order->customer_name }}</b>, no order <b> <a target="_blank" href="/sales/{{ $order->id }}/detail">{{ $order->sales_code }}</a> </b> jatuh tempo pada <b>{{ $order->due_date_convert }} [{{ $order->phone_number }}] </b>
                        </div>
                        <div class="col-xs-6" style="text-align: right;">
                            <button class="btn btn-success" onclick="updateStatus({{$order->id}})"><i class="fa fa-check"></i></button>
                        </div>
                    </div>
                </div>
            </div>
            @elseif($order->status == 1)
            <!-- -->
            <div class="panel panel-success" style="border-radius: 10px;">
                <div class="panel-heading" style="padding: 10px 10px; border-top-left-radius: 10px; border-top-right-radius: 10px;text-decoration: line-through;">
                    <h3 class="panel-title">Due Date {{ $order->day_left }} days left</h3>
                </div>
                <div class="panel-body" style="padding: 8px 10px !important;">
                    <div class="row">
                        <div class="col-xs-6" style="text-decoration: line-through;">
                            <b>{{ $order->customer_name }}</b>, no order <b> <a target="_blank" href="/sales/{{ $order->id }}/detail">{{ $order->sales_code }}</a> </b> jatuh tempo pada <b>{{ $order->due_date_convert }} [{{ $order->phone_number }}] </b>
                        </div>
                        <div class="col-xs-6" style="text-align: right;">
                            <button class="btn btn-danger" onclick="deleteStatus({{$order->id}})"><i class="fa fa-close"></i></button>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            @endforeach
            @endif
    </div>
</div><!-- /row -->
@endsection

@section('script')
<script>
    function updateStatus(id) {
        axios.post("/sales/" + id + "/update-status", {})
            .then(function(response) {
                if (response.data.status == 1) {
                    swal({
                        title: "Good!",
                        text: response.data.message,
                        type: "success",
                        timer: 1000,
                        confirmButtonText: 'Ok'
                    }).then(function() {
                        location.reload();
                    });
                } else {
                    swal({
                        title: "Oops!",
                        text: response.data.message,
                        type: "error",
                        closeOnConfirm: false
                    });
                }
            })
            .catch(function(error) {
                switch (error.response.status) {
                    case 422:
                        swal({
                            title: "Oops!",
                            text: 'Failed form validation. Please check your input.',
                            type: "error"
                        });
                        break;
                    case 500:
                        swal({
                            title: "Oops!",
                            text: 'Something went wrong.',
                            type: "error"
                        });
                        break;
                }
            });
    }

    function deleteStatus(id) {
        axios.post("/sales/" + id + "/delete-status", {})
            .then(function(response) {
                if (response.data.status == 1) {
                    swal({
                        title: "Good!",
                        text: response.data.message,
                        type: "success",
                        timer: 1000,
                        confirmButtonText: 'Ok'
                    }).then(function() {
                        location.reload();
                    });
                } else {
                    swal({
                        title: "Oops!",
                        text: response.data.message,
                        type: "error",
                        closeOnConfirm: false
                    });
                }
            })
            .catch(function(error) {
                switch (error.response.status) {
                    case 422:
                        swal({
                            title: "Oops!",
                            text: 'Failed form validation. Please check your input.',
                            type: "error"
                        });
                        break;
                    case 500:
                        swal({
                            title: "Oops!",
                            text: 'Something went wrong.',
                            type: "error"
                        });
                        break;
                }
            });
    }
</script>
@endsection