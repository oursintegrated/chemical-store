@extends('layouts.backend')
@section('title', 'Chemical Store | Sales')
@section('content')
<div class="row row-cards-pf">
    <div class="row-cards-pf card-pf">
        <ol class="breadcrumb">
            <li>
                <span class="pficon pficon-home"></span>
                <a href="{{url('dashboard')}}">Dashboard</a>
            </li>
            <li>
                <span class="pficon pficon-user"></span>
                <a href="{{url('/sales')}}">Sales</a>
            </li>
            <li class="active">
                <strong>List Sales</strong>
            </li>
        </ol>
    </div>
</div><!-- /row -->

<div class="row row-cards-pf">
    <!-- Important:  if you need to nest additional .row within a .row.row-cards-pf, do *not* use .row-cards-pf on the nested .row  -->
    <div class="col-xs-12">
        <div class="card-pf card-pf-accented card-pf-view">
            <div class="card-pf-heading">
                <h1>
                    <span class="pficon pficon-orders"></span>
                    Sales
                    <small>List</small>
                </h1>
            </div>
            <div class="card-pf-body">
                <div class="row">
                    <div class="col-md-12">
                        <a href="{{url('/sales/create')}}" class="btn btn-default btn">
                            <li class="fa fa-plus-square"></li> &nbsp; Create Sales
                        </a>
                    </div>
                </div>
                <br />
                <div class="row">
                    <form id="main_form" class="col-md-3" autocomplete="off">
                        {{ csrf_field() }}
                        <div class="form-group required">
                            <label for="fullname">Transaction Date <span style="color: red;">*</span></label>
                            <input id="date" placeholder="Input date" class="form-control datepicker" name="datetimerange" value="">
                        </div>

                        <div class="form-group">
                            <button type="button" class="btn btn-default" id="searchBtn"> Search </button>
                        </div>
                    </form>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <!-- Table HTML -->
                            <table id="salesTable" class="table table-striped table-bordered table-hover" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>Actions</th>
                                        <th class="text-center">Sales Code</th>
                                        <th class="text-center">Customer Name</th>
                                        <th class="text-center">Address</th>
                                        <th class="text-center">Type</th>
                                        <th class="text-center">Payment</th>
                                        <th class="text-center">Total</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Transaction Date</th>
                                        <th class="text-center">Due Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th></th>
                                        <th>Sales Code</th>
                                        <th>Customer Name</th>
                                        <th>Address</th>
                                        <th>Type</th>
                                        <th>Payment</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Transaction Date</th>
                                        <th>Due Date</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div><!-- /row -->
@endsection
@section('script')
<script>
    $(document).ready(function() {

        var startDate = '';
        var endDate = '';
        $('#date').daterangepicker({
            autoApply: true,
            autoUpdateInput: false,
        }, function(start, end, label) {
            $('#date').val(start.format('MM/DD/YYYY') + ' - ' + end.format('MM/DD/YYYY'));

            startDate = start.format('YYYY-MM-DD');
            endDate = end.format('YYYY-MM-DD');
        });

        $('#searchBtn').on('click', function() {
            if ($('#date').val() == '') {
                startDate = '';
                endDate = '';
            }
            table.ajax.reload();
        });

        $('#salesTable tfoot th').each(function() {
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
        var table = $("#salesTable").DataTable({
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
                "url": "/datatable/sales",
                "type": "POST",
                "data": function(d) {
                    d.start_date = startDate,
                        d.end_date = endDate
                }
            },
            language: {
                "decimal": ",",
                "thousands": "."
            },
            columns: [{
                data: 'action',
                name: 'action',
                className: "table-view-pf-actions",
                orderable: false,
                searchable: false
            }, {
                data: 'sales_code',
                name: 'sales_code'
            }, {
                data: 'customer_name',
                name: 'customer_name'
            }, {
                data: 'address',
                name: 'address'
            }, {
                data: 'type',
                name: 'type',
                className: 'align-middle'
            }, {
                data: 'payment',
                name: 'payment',
                className: 'align-middle'
            }, {
                data: 'total',
                name: 'total',
                className: 'text-right'
            }, {
                data: 'status',
                name: 'status',
                className: 'align-middle'
            }, {
                data: 'transaction_date',
                name: 'transaction_date',
                className: 'text-center'
            }, {
                data: 'due_date',
                name: 'due_date',
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
    });

    var deleteSales = function(me) {
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover this record!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            showLoaderOnConfirm: true,
            preConfirm: () => {
                _deleteSales(me)
            }
        })
    };

    var _deleteSales = function(me) {
        var recordID = me.data('record-id');

        axios.delete("/sales/" + recordID + "/delete")
            .then(function(response) {
                if (response.data.status == 1) {
                    swal({
                        title: "Good!",
                        text: response.data.message,
                        type: "success",
                        confirmButtonText: 'Ok'
                    });

                    $('#salesTable').DataTable().ajax.reload(null, false);
                } else {
                    swal({
                        title: "Oops!",
                        text: response.data.message,
                        type: "error",
                        confirmButtonText: 'Ok'
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
    };
</script>
@endsection