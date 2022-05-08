@extends('layouts.backend')
@section('title', 'Chemical Store | Stock')
@section('content')
<div class="row row-cards-pf">
    <div class="row-cards-pf card-pf">
        <ol class="breadcrumb">
            <li>
                <span class="pficon pficon-home"></span>
                <a href="{{url('dashboard')}}">Dashboard</a>
            </li>
            <li>
                <span class="pficon pficon-registry"></span>
                <a href="{{url('/data-master/stock')}}">Stock</a>
            </li>
            <li class="active">
                <strong>List Stock</strong>
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
                    <span class="pficon pficon-registry"></span>
                    Stock
                    <small>List</small>
                </h1>
            </div>
            <div class="card-pf-body">
                <div class="row">
                    <div class="col-md-12">
                        <a href="{{url('/data-master/stock/manage')}}" class="btn btn-default btn">
                            <li class="fa fa-plus-square"></li> &nbsp; Manage Stock
                        </a>
                        <a href="{{url('/data-master/stock/history')}}" class="btn btn-default btn">
                            <li class="fa fa-check-square"></li> &nbsp; History Stock
                        </a>
                    </div>
                </div>
                <br />
                <div class="row">
                    <div class="col-md-2">
                        <div class="panel panel-warning">
                            <div class="panel-heading">
                                <b>Low Stock</b>
                            </div>
                            <div class="panel-body text-center" style="padding: 1px !important;">
                                <h4><b>{{ $totalLowStock }} products</b></h4> <a href="/data-master/stock-low">List Stock</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <p>&nbsp;</p>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <!-- Table HTML -->
                            <table id="productTable" class="table table-striped table-bordered" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center">Code</th>
                                        <th class="text-center">Product Name</th>
                                        <th class="text-center">Type</th>
                                        <th class="text-center">Stock</th>
                                        <th class="text-center">Min Stock</th>
                                        <th class="text-center">Description</th>
                                        <th class="text-center">Created At</th>
                                        <th class="text-center">Updated At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Code</th>
                                        <th>Product Name</th>
                                        <th>Type</th>
                                        <th>Stock</th>
                                        <th>Min Stock</th>
                                        <th>Description</th>
                                        <th>Created At</th>
                                        <th>Updated At</th>
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

        $('#productTable tfoot th').each(function() {
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
        });

        // DataTable Config
        var table = $("#productTable").DataTable({
            processing: true,
            serverSide: false,
            stateSave: false,
            stateDuration: 0,
            lengthMenu: [
                [10, 50, 75, -1],
                [10, 50, 75, "All"]
            ],
            pageLength: 10,
            order: [
                [1, 'asc']
            ],
            dom: '<"top"l>rt<"bottom"ip><"clear">',
            ajax: {
                "url": "/datatable/stocks",
                "type": "POST"
            },
            language: {
                "decimal": ",",
                "thousands": "."
            },
            rowCallback: function(row, data) {
                // check stock
                if (parseFloat(data.stock) < parseFloat(data.min_stock)) {
                    $(row).css('background-color', 'yellow')
                }

                // return satuan
                if (data.type == 'Raw Material') {
                    $('td:eq(3)', row).html(parseFloat(data.stock).toFixed(2) + ' Kg');
                    $('td:eq(4)', row).html(parseFloat(data.min_stock).toFixed(2) + ' Kg');
                } else if (data.type == 'Packaging') {
                    $('td:eq(3)', row).html(parseFloat(data.stock) + ' Pcs');
                    $('td:eq(4)', row).html(parseFloat(data.min_stock) + ' Pcs');
                } else {
                    $('td:eq(3)', row).html(parseFloat(data.stock) + ' Packet');
                    $('td:eq(4)', row).html(parseFloat(data.min_stock) + ' Packet');
                }
            },
            columns: [{
                data: 'code',
                name: 'code'
            }, {
                data: 'product_name',
                name: 'product_name'
            }, {
                data: 'type',
                name: 'type',
                className: 'text-center'
            }, {
                data: 'stock',
                name: 'stock',
                className: 'text-right'
            }, {
                data: 'min_stock',
                name: 'min_stock',
                className: 'text-right'
            }, {
                data: 'description',
                name: 'description'
            }, {
                data: 'created_at',
                name: 'created_at',
                className: 'text-center'
            }, {
                data: 'updated_at',
                name: 'updated_at',
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

    var deleteProduct = function(me) {
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover this record!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            showLoaderOnConfirm: true,
            preConfirm: () => {
                _deleteProduct(me)
            }
        })
    };

    var _deleteProduct = function(me) {
        var recordID = me.data('record-id');

        axios.delete("/data-master/product/" + recordID + "/delete")
            .then(function(response) {
                if (response.data.status == 1) {
                    swal({
                        title: "Good!",
                        text: response.data.message,
                        type: "success",
                        confirmButtonText: 'Ok'
                    });

                    $('#productTable').DataTable().ajax.reload(null, false);
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