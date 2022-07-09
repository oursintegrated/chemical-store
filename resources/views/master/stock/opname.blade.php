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
                <strong>Stock Opname</strong>
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
                    <small>Opname</small>
                </h1>
            </div>
            <div class="card-pf-body">
                <div class="row">
                    <p>&nbsp;</p>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-default" onclick="printContent()">Save to PDF</button>
                        <br />
                        <br />
                        <div class="table-responsive" id="print">
                            <!-- Table HTML -->
                            <table id="productTable" class="table table-striped table-bordered" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th class="text-center">No</th>
                                        <th class="text-center">Product Name</th>
                                        <th class="text-center">Stock {{ date('M Y') }}</th>
                                        <th class="text-center">Stock Opname</th>
                                        <th class="text-center">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>ID</th>
                                        <th class="text-center">No</th>
                                        <th class="text-center">Product Name</th>
                                        <th class="text-center">Current Stock</th>
                                        <th class="text-center">Stock Opname</th>
                                        <th class="text-center">Keterangan</th>
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
    function printContent() {
        var restorepage = document.body.innerHTML;
        var printcontent = document.getElementById('print').innerHTML;
        document.body.innerHTML = printcontent;
        window.print();
        document.body.innerHTML = restorepage;
    }

    $(document).ready(function() {
        // $('#productTable tfoot th').each(function() {
        //     var title = $(this).text();
        //     if (title != '') {
        //         if (title == 'Add Stock') {

        //         } else {
        //             $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" style="width: 100%;" />');
        //         }
        //     }
        //     if (title == 'Created At') {
        //         $(this).html('<input type="text" class="datepicker form-control" placeholder="Search ' + title + '" style="width: 100%;" />');
        //     }
        //     if (title == 'Updated At') {
        //         $(this).html('<input type="text" class="datepicker form-control" placeholder="Search ' + title + '" style="width: 100%;" />');
        //     }
        // });

        // DataTable Config
        var no = 0;
        var table = $("#productTable").DataTable({
            processing: true,
            serverSide: false,
            stateSave: false,
            stateDuration: 0,
            columnDefs: [{
                searchable: false,
                orderable: false,
                targets: 0,
            }, ],
            order: [
                [2, 'asc']
            ],
            lengthMenu: [
                [-1],
                ["All"]
            ],
            pageLength: -1,
            dom: '',
            ajax: {
                "url": "/datatable/stocks/opname",
                "type": "POST"
            },
            language: {
                "decimal": ",",
                "thousands": "."
            },
            columns: [{
                data: 'id',
                name: 'id',
            }, {
                data: 'no',
                name: 'no'
            }, {
                data: 'product_name',
                name: 'product_name'
            }, {
                data: 'stock',
                name: 'stock',
                className: 'text-right'
            }, {
                data: 'stock',
                name: 'stock',
                className: 'text-right'
            }, {
                data: 'keterangan',
                name: 'keterangan',
                className: 'text-right'
            }]
        });

        table.on('order.dt search.dt', function() {
            let i = 1;

            table.cells(null, 1, {
                search: 'applied',
                order: 'applied'
            }).every(function(cell) {
                this.data(i++);
            });
        }).draw();

        /* Ketika Value pada Input di TFOOT berubah, Maka Search Sesuai Kolom */
        // table.columns().every(function() {
        //     var that = this;
        //     $('input', this.footer()).on('keyup change', function() {

        //         // Cancel the default action, if needed
        //         event.preventDefault();

        //         var keyword = this.value;

        //         if (this.placeholder == 'Search Published') {
        //             keyword = keyword.toUpperCase();
        //             if (keyword == 'TRUE' || keyword == 'YA' || keyword == 'YES' || keyword == 'Y' || keyword == '1') {
        //                 keyword = 1;
        //             } else {
        //                 keyword = 0;
        //             }
        //         }

        //         if (that.search() !== keyword) {
        //             that
        //                 .search(keyword)
        //                 .draw();
        //         }
        //     });
        // });

        // $("tfoot .datepicker").datepicker({
        //     autoclose: true,
        //     endDate: "0d",
        //     format: "dd MM yyyy",
        //     todayHighlight: true,
        //     weekStart: 1,
        // });

        $('#productTable').on('draw.dt', function() {
            $('#productTable').Tabledit({
                url: '/datatable/stock/tabledit',
                dataType: 'json',
                inputClass: 'form-control input-sm',
                toolbarClass: 'btn-toolbar',
                groupClass: 'btn-group btn-group-sm',
                dangerClass: 'danger',
                warningClass: 'warning',
                mutedClass: 'text-muted',
                eventType: 'dblclick',
                rowIdentifier: 'id',
                autoFocus: true,
                hideIdentifier: true,
                editButton: false,
                deleteButton: false,
                saveButton: false,
                restoreButton: true,
                columns: {
                    identifier: [0, 'id'],
                    editable: [
                        [4, 'stock'],
                        [5, 'keterangan']
                    ]
                },
                onSuccess: function(data, textStatus, jqXHR) {

                }
            });
        });
    });
</script>
@endsection