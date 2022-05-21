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

<div class="row row-cards-pf">
    <div class="row-cards-pf card-pf">
        <ol class="breadcrumb">
            <li>
                <span class="pficon pficon-home"></span>
                <a href="{{url('dashboard')}}">Dashboard</a>
            </li>
        </ol>
    </div>
</div><!-- /row -->

<!-- Toolbar -->
<div class="row row-cards-pf">
    <div class="col-sm-12">
        <div class="card-pf card-pf-accented">
            <div class="card-pf-heading">
                <h1>
                    <span class="pficon pficon-user"></span>
                    Credit
                    <small>List</small>
                </h1>
            </div>
            <div class="card-pf-body">
                <div class="row">
                    <div class="col-xs-6">
                        <label>Sales Code : </label> {{ $sales->sales_code }} - {{ $sales->customer_name }}<br />
                        <label>Total : </label> Rp. {{ number_format($sales->total, 2, ',', '.') }}
                    </div>
                    <div class="col-xs-12">
                        <button type="button" class="btn btn-sm btn-default" data-toggle="collapse" data-target="#toogleForm">
                            <li class="fa fa-plus-square"></li> &nbsp; Pay
                        </button>

                        <form role="form" id="main_form" class="form-inline">
                            {{ csrf_field() }}
                            <input id="id" name="id" class="form-control" type="text" value="{{ $sales->id }}" readonly style="display: none;">

                            <div id="toogleForm" class="collapse">
                                <br />
                                <label for="nominal">Nominal <span style="color: red;">*</span> :</label>
                                <input class="form-control" type="text" id="nominal" placeholder="Enter nominal" name="nominal">
                                &nbsp;&nbsp;&nbsp;
                                <label for="payment">Payment <span style="color: red;">*</span> :</label>
                                <input class="form-control" type="text" id="payment" placeholder="Cash, Transfer, Gyro" name="payment">
                                &nbsp;&nbsp;&nbsp;
                                <label for="notes">Notes :</label>
                                <input class="form-control" type="text" id="notes" placeholder="Notes" name="notes">
                                <button id="btnSave" class="btn btn-default" type="button" onclick="create_credit()">Submit</button>
                            </div>
                        </form>
                    </div>
                    <div class="col-xs-12">
                        <div class="table-responsive">
                            <br />
                            <!-- Table HTML -->
                            <table id="creditTable" class="table table-striped table-bordered table-hover" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <!-- <th class="text-center">Action</th> -->
                                        <th class="text-center">Paid</th>
                                        <th class="text-center">Payment</th>
                                        <th class="text-center">Notes</th>
                                        <th class="text-center">Transaction Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <!-- <th></th> -->
                                        <th>Paid</th>
                                        <th>Payment</th>
                                        <th>Notes</th>
                                        <th>Transaction Date</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $('#nominal').keypress(function(evt) {
        return (/^[0-9]*\.?[0-9]*$/).test($(this).val() + evt.key);
    });

    $('#creditTable tfoot th').each(function() {
        var title = $(this).text();
        if (title != '') {
            $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" style="width: 100%;" />');
        }
        if (title == 'Created At') {
            $(this).html('<input type="text" class="datepicker form-control" placeholder="Search ' + title + '" style="width: 100%;" />');
        }
        if (title == 'Updated At') {
            $(this).html('<input type="text" class="datepicker form-control" placeholder="Search ' + title + '" style="width: 100%;" />');
        }
        if (title == 'Transaction Date') {
            $(this).html('<input type="text" class="datepicker form-control" placeholder="Search ' + title + '" style="width: 100%;" />');
        }
        if (title == 'Due Date') {
            $(this).html('<input type="text" class="datepicker form-control" placeholder="Search ' + title + '" style="width: 100%;" />');
        }
    });

    // DataTable Config
    var table = $("#creditTable").DataTable({
        processing: true,
        serverSide: false,
        stateSave: false,
        stateDuration: 0,
        lengthMenu: [
            [10, 50, 75, -1],
            [10, 50, 75, "All"]
        ],
        pageLength: 10,
        sort: false,
        dom: '<"top"l>rt<"bottom"ip><"clear">',
        ajax: {
            "url": "/datatable/credits",
            "type": "POST",
            "data": {
                id: $('#id').val()
            }
        },
        language: {
            "decimal": ",",
            "thousands": "."
        },
        columns: [{
            data: 'pay',
            name: 'pay',
            className: 'text-right'
        }, {
            data: 'payment',
            name: 'payment',
            className: 'text-center'
        }, {
            data: 'notes',
            name: 'notes'
        }, {
            data: 'created_at',
            name: 'created_at',
            className: 'text-center'
        }]
    });

    /* Ketika Value pada Input di TFOOT berubah, Maka Search Sesuai Kolom */
    table.columns().every(function() {
        var that = this;
        $('input', this.footer()).on('keyup change', function() {

            // Cancel the default action, if needed
            event.preventDefault();

            var keyword = this.value;

            if (this.placeholder == 'Search Published') {
                keyword = keyword.toUpperCase();
                if (keyword == 'TRUE' || keyword == 'YA' || keyword == 'YES' || keyword == 'Y' || keyword == '1') {
                    keyword = 1;
                } else {
                    keyword = 0;
                }
            }

            if (that.search() !== keyword) {
                that
                    .search(keyword)
                    .draw();
            }
        });
    });

    $("tfoot .datepicker").datepicker({
        autoclose: true,
        endDate: "0d",
        format: "dd MM yyyy",
        todayHighlight: true,
        weekStart: 1,
    });

    var create_credit = function() {
        $("#btnSave").prop('disabled', 'true');

        axios.post("/credit/create", $('#main_form').serialize())
            .then(function(response) {
                if (response.data.status == 1) {
                    swal({
                        title: "Good!",
                        text: response.data.message,
                        type: "success",
                        timer: 1000,
                        confirmButtonText: 'Ok'
                    }).then(function() {
                        $('form#main_form')[0].reset();
                        $("form#main_form:not(.filter) :input:visible:enabled:first").focus();
                        table.ajax.reload();
                    });
                } else {
                    swal({
                        title: "Oops!",
                        text: response.data.message,
                        type: "error",
                        closeOnConfirm: false
                    });
                }
                $("#btnSave").removeAttr('disabled');
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
                $("#btnSave").removeAttr('disabled');
            });
    };
</script>
@endsection