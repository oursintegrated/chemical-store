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
                <strong>Manage Stock</strong>
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
                    <small>Manage</small>
                </h1>
            </div>
            <div class="card-pf-body">
                <br />
                <div class="row">
                    <form id="main_form" autocomplete="off">
                        {{ csrf_field() }}
                        <div class="col-md-5">
                            <div class="form-group required">
                                <label class="control-label">Type <span style="color: red;">*</span></label>
                                <select class="form-control" name="type" id="type">
                                    <option value="insert stock">Insert Stock</option>
                                    <!-- <option value="stock opname">Stock Opname</option> -->
                                    <option value="retur">Retur</option>
                                    <option value="product processing">Product Processing</option>
                                    <option value="stock opname">Stock Opname</option>
                                </select>
                            </div>

                            <div class="form-group required ingredientForm">
                                <label class="control-label">Choose Products <span style="color: red;">*</span></label>

                                <div class="table-responsive">
                                    <!-- Table HTML -->
                                    <table id="productTable" class="table table-striped table-bordered table-hover" style="width: 100%; font-size: 8pt">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Code</th>
                                                <th>Product Name</th>
                                                <th>Type</th>
                                                <th>Stock</th>
                                                <th>Description</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                        <!-- <tfoot>
                                            <tr>
                                                <th>Code</th>
                                                <th>Product Name</th>
                                                <th>Stock In Kg</th>
                                                <th>Description</th>
                                            </tr>
                                        </tfoot> -->
                                    </table>
                                </div>
                            </div>

                            <a role="button" href="{{ url('/data-master/stock') }}" class="btn btn-default btn"><i class="fa fa-arrow-circle-left fa-fw"></i> Back</a>

                            <button type="button" id="btnSave" class="btn btn-success btn btn-ml" style="margin-left: 10px"><i class="fa fa-check fa-fw"></i> Save</button>
                        </div>

                        <div class="col-md-7">
                            <div class="form-group required ingredientForm">
                                <label class="control-label">Selected Product <span style="color: red;">*</span></label>
                                <table id="selectedTable" class="table table-responsive table-bordered">
                                    <thead>
                                        <th>ID</th>
                                        <th class="text-center">No</th>
                                        <th class="text-center">Product Name</th>
                                        <th class="text-center">Qty</th>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div><!-- /row -->
@endsection
@section('script')
<script>
    $(document).ready(function() {
        var dataSelected = [];

        // ============================ Initial Datatable
        var selectedTable = $("#selectedTable").DataTable({
            processing: false,
            serverSide: false,
            stateSave: false,
            lengthMenu: [
                [-1],
                ['All']
            ],
            dom: '',
            columnDefs: [{
                "defaultContent": "-",
                "targets": "_all",
            }],
            order: [
                [1, 'asc']
            ],
            sort: false,
            columns: [{
                data: 'id',
                name: 'id'
            }, {
                data: 'no',
                name: 'no'
            }, {
                data: 'product_name',
                name: 'product_name'
            }, {
                data: 'qty',
                name: 'qty',
                className: 'text-right'
            }, ]
        });

        $('#selectedTable').on('draw.dt', function() {
            $('#selectedTable').Tabledit({
                url: '/datatable/product/tabledit',
                dataType: 'json',
                // class for form inputs
                inputClass: 'form-control input-sm',

                // class for toolbar
                toolbarClass: 'btn-toolbar',

                // class for buttons group
                groupClass: 'btn-group btn-group-sm',

                // class for row when ajax request fails
                dangerClass: 'danger',

                // class for row when save changes
                warningClass: 'warning',

                // class for row when is removed
                mutedClass: 'text-muted',

                // trigger to change for edit mode.
                // e.g. 'dblclick'
                eventType: 'dblclick',

                // change the name of attribute in td element for the row identifier
                rowIdentifier: 'id',

                // activate focus on first input of a row when click in save button
                autoFocus: true,

                // hide the column that has the identifier
                hideIdentifier: true,

                // activate edit button instead of spreadsheet style
                editButton: false,

                // activate delete button
                deleteButton: false,

                // activate save button when click on edit button
                saveButton: false,

                // activate restore button to undo delete action
                restoreButton: true,
                columns: {
                    identifier: [0, 'id'],
                    editable: [
                        [3, 'qty'],
                    ]
                },
                onSuccess: function(data, textStatus, jqXHR) {
                    if (data.action == 'edit') {
                        var id = data.id;
                        if (data.hasOwnProperty('qty')) {
                            for (var i = 0; i < dataSelected.length; i++) {
                                if (dataSelected[i].id == id) {
                                    dataSelected[i].qty = data.qty
                                }
                            }
                        }
                        resetSelectedTable();
                    }
                }
            });
        });

        var productTable = $("#productTable").DataTable({
            processing: true,
            serverSide: false,
            stateSave: false,
            stateDuration: 0,
            rowCallback: function(row, data, index) {
                for (var i = 0; i < dataSelected.length; i++) {
                    if (dataSelected[i].id == data['id']) {
                        $(row).addClass('selected');
                        dataSelected[i].stock = data['stock'];

                        resetSelectedTable();
                    }
                }
            },
            lengthMenu: [
                [5],
                [5]
            ],
            pageLength: 5,
            order: [
                [2, 'asc']
            ],
            columnDefs: [{
                "targets": [0],
                "visible": false,
                "searchable": false
            }],
            buttons: [{
                text: 'Reset',
                action: function() {
                    productTable.rows().deselect();
                    dataSelected = [];
                    resetSelectedTable();
                },
                className: 'btn btn-default'
            }],
            dom: 'Bfrtip',
            ajax: {
                "url": "/datatable/stocks/raw-material",
                "type": "POST"
            },
            language: {
                "decimal": ",",
                "thousands": "."
            },
            columns: [{
                data: 'id',
                name: 'id'
            }, {
                data: 'code',
                name: 'code'
            }, {
                data: 'product_name',
                name: 'product_name'
            }, {
                data: 'type',
                name: 'type'
            }, {
                data: 'stock',
                name: 'stock'
            }, {
                data: 'description',
                name: 'description'
            }, ]
        });

        $('#productTable tbody').on('click', 'tr', function() {
            if ($(this).hasClass('selected')) {
                $(this).removeClass('selected');

                var id = productTable.row(this).data().id;

                for (var i = 0; i < dataSelected.length; i++) {
                    if (dataSelected[i].id == id) {
                        dataSelected.splice(i, 1);
                    }
                }

                resetSelectedTable();

            } else {
                $(this).addClass('selected');

                var id = productTable.row(this).data().id;
                var no = dataSelected.length + 1;
                var productName = productTable.row(this).data().product_name;
                var type_product = productTable.row(this).data().type;

                dataSelected.push({
                    'id': id,
                    'no': no,
                    'product_name': productName,
                    'qty': 0,
                    'type': type_product
                });

                var resetSelected = selectedTable
                    .rows()
                    .remove()
                    .draw();

                for (var i = 0; i < dataSelected.length; i++) {
                    selectedTable.row.add(dataSelected[i]).draw()
                }
            }
        });

        $("tfoot .datepicker").datepicker({
            autoclose: true,
            endDate: "0d",
            format: "dd MM yyyy",
            todayHighlight: true,
            weekStart: 1,
        });

        function resetSelectedTable() {
            var resetSelected = selectedTable
                .rows()
                .remove()
                .draw();

            for (var i = 0; i < dataSelected.length; i++) {
                dataSelected[i].no = i + 1;
            }

            for (var i = 0; i < dataSelected.length; i++) {
                selectedTable.row.add(dataSelected[i]).draw()
            }
        }

        // ============================ Core Function
        $('#btnSave').on('click', function() {
            // $("#btnSave").prop('disabled', 'true');

            var type = $("#type").val();
            axios.post("/data-master/stock/manage", {
                    'type': type,
                    'dataSelected': dataSelected
                })
                .then(function(response) {
                    if (response.data.status == 1) {
                        var m = '';
                        if (response.data.cant.length > 0) {
                            m = "Bahan tidak memadai <br />"
                        }
                        swal({
                            title: "Good!",
                            html: response.data.message + '<br/>' + m + '<b>' +
                                response.data.cant + '</b>',
                            type: "success",
                            confirmButtonText: 'Ok'
                        }).then((result) => {
                            window.location.replace(response.data.intended_url)
                        })
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
        });
    });
</script>
@endsection