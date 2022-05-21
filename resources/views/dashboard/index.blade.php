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
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <!-- Table HTML -->
                    <table id="salesTable" class="table table-striped table-bordered table-hover" style="width: 100%;">
                        <thead>
                            <tr>
                                <th class="text-center">Action</th>
                                <th class="text-center">Sales Code</th>
                                <th class="text-center">Customer Name</th>
                                <th class="text-center">Address</th>
                                <th class="text-center">Type</th>
                                <th class="text-center">Total</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Credit</th>
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
                                <th>Total</th>
                                <th>Status</th>
                                <th>Credit</th>
                                <th>Transaction Date</th>
                                <th>Due Date</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div><!-- /row -->
@endsection

@section('script')
<script>
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
            "url": "/datatable/dashboard",
            "type": "POST"
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
            data: 'total',
            name: 'total',
            className: 'text-right'
        }, {
            data: 'status',
            name: 'status',
            className: 'align-middle'
        }, {
            data: 'credit',
            name: 'credit',
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

    function updateStatus(id, payment) {
        axios.post("/sales/" + id + "/update-status", {
                'payment': payment
            })
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

    // function deleteStatus(id) {
    //     axios.post("/sales/" + id + "/delete-status", {})
    //         .then(function(response) {
    //             if (response.data.status == 1) {
    //                 swal({
    //                     title: "Good!",
    //                     text: response.data.message,
    //                     type: "success",
    //                     timer: 1000,
    //                     confirmButtonText: 'Ok'
    //                 }).then(function() {
    //                     location.reload();
    //                 });
    //             } else {
    //                 swal({
    //                     title: "Oops!",
    //                     text: response.data.message,
    //                     type: "error",
    //                     closeOnConfirm: false
    //                 });
    //             }
    //         })
    //         .catch(function(error) {
    //             switch (error.response.status) {
    //                 case 422:
    //                     swal({
    //                         title: "Oops!",
    //                         text: 'Failed form validation. Please check your input.',
    //                         type: "error"
    //                     });
    //                     break;
    //                 case 500:
    //                     swal({
    //                         title: "Oops!",
    //                         text: 'Something went wrong.',
    //                         type: "error"
    //                     });
    //                     break;
    //             }
    //         });
    // }

    // ========================= Complete Sales
    var completeSales = function(me) {
        var type = me.data('type');
        if (type == "tunai" || type == "kredit") {
            swal({
                title: "Are you sure?",
                text: "You will not be able to recover this record!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "lime",
                confirmButtonText: "Yes, complete it!",
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    _completeSales(me, null)
                }
            })
        } else {
            (async () => {

                const {
                    value: payment
                } = await swal.fire({
                    title: 'Select payment method',
                    input: 'select',
                    inputOptions: {
                        tunai: 'Tunai',
                        transfer: 'Transfer',
                        gyro: 'Gyro'
                    },
                    inputPlaceholder: 'Select a payment',
                    showCancelButton: true,
                    inputValidator: (value) => {
                        _completeSales(me, value)
                    }
                })

            })()
        }

    };

    var _completeSales = function(me, payment) {
        var recordID = me.data('record-id');
        updateStatus(recordID, payment);
    };


    // =========================== Credit
    var credit = function(me) {
        var sales_id = me.data('record-id');
        window.location.replace('/credit/' + sales_id);
    };
</script>
@endsection