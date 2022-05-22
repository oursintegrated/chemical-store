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
                <strong>List Low Stock</strong>
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
                    Low Stock
                    <small>List</small>
                </h1>
            </div>
            <div class="card-pf-body">
                <!-- <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-default btn" id="adjustmentBtn">
                            <li class="fa fa-plus-square"></li> &nbsp; Add Stock
                        </button>
                    </div>
                </div> -->
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
                                        <th class="text-center">ID</th>
                                        <th class="text-center">Code</th>
                                        <th class="text-center">Product Name</th>
                                        <th class="text-center">Type</th>
                                        <th class="text-center">Stock</th>
                                        <th class="text-center">Min Stock</th>
                                        <!-- <th class="text-center">Adjustment Stock</th> -->
                                        <th class="text-center">Description</th>
                                        <th class="text-center">Created At</th>
                                        <th class="text-center">Updated At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>ID</th>
                                        <th>Code</th>
                                        <th>Product Name</th>
                                        <th>Type</th>
                                        <th>Stock</th>
                                        <th>Min Stock</th>
                                        <!-- <th></th> -->
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
                if (title == 'Add Stock') {

                } else {
                    $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" style="width: 100%;" />');
                }
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
                [-1],
                ["All"]
            ],
            columnDefs: [{
                "defaultContent": "-",
                "targets": "_all",
            }],
            pageLength: -1,
            order: [
                [2, 'asc']
            ],
            dom: '',
            ajax: {
                "url": "/datatable/stock-low",
                "type": "POST"
            },
            language: {
                "decimal": ",",
                "thousands": "."
            },
            columns: [{
                data: 'id',
                name: 'id',
                visible: false
            }, {
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

        // $('#adjustmentBtn').click(function() {
        //     $("#adjustmentBtn").prop('disabled', 'true');

        //     var totalRow = table.data().count();
        //     var temp = [];
        //     for (var i = 0; i < totalRow; i++) {
        //         var product_id = table.cell(i, 0).data();
        //         var adj_stock = table.cell(i, 6).nodes().to$().find('input').val();
        //         var type = table.cell(i, 3).data();
        //         var product_name = table.cell(i, 2).data();

        //         if (adj_stock != 0) {
        //             var obj = {
        //                 'product_id': product_id,
        //                 'adj_stock': adj_stock,
        //                 'type': type,
        //                 'product_name': product_name
        //             }
        //             temp.push(obj);
        //         }
        //     }

        //     axios.post("/data-master/stock-low/adjust", {
        //             'adjustment': temp,
        //         })
        //         .then(function(response) {
        //             if (response.data.status == 1) {
        //                 var m = '';
        //                 if (response.data.cant.length > 0) {
        //                     m = "Bahan tidak memadai <br />"
        //                 }
        //                 swal({
        //                     title: "Good!",
        //                     html: response.data.message + '<br/>' + m + '<b>' +
        //                         response.data.cant + '</b>',
        //                     type: "success",
        //                     confirmButtonText: 'Ok'
        //                 }).then((result) => {
        //                     window.location.replace(response.data.intended_url)
        //                 })
        //             } else {
        //                 swal({
        //                     title: "Oops!",
        //                     text: response.data.message,
        //                     type: "error",
        //                     closeOnConfirm: false
        //                 });

        //                 table.ajax.reload();
        //             }
        //             $("#adjustmentBtn").removeAttr('disabled');
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
        //             $("#adjustmentBtn").removeAttr('disabled');
        //         });
        // });
    });
</script>
@endsection