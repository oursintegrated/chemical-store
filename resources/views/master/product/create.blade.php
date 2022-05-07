@extends('layouts.backend')
@section('title', 'Chemical Store | Product')
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
                <a href="{{url('/data-master/product')}}">Product</a>
            </li>
            <li class="active">
                <strong>Create</strong>
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
                    <span class="pficon pficon-registry"></span>
                    Product
                    <small>Create</small>
                </h1>
            </div>
            <div class="card-pf-body">
                <div class="row">
                    <form id="main_form" autocomplete="off">
                        {{ csrf_field() }}
                        <div class="col-md-5">
                            <div class="form-group required">
                                <label class="control-label">Product Name <span style="color: red;">*</span></label>
                                <!-- <input type="text" required name="name" class="form-control" placeholder="Product Name" value="{{ old('name') }}" autocomplete="off"> -->
                                <select id="name" name="name" class="form-control">
                                    <option></option>
                                    @if(isset($products))
                                    @foreach($products as $product)
                                    <option value="{{ $product->product_name }}"> {{ $product->product_name }}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>

                            <div class="form-group required">
                                <label class="control-label">Stock <span style="color: red;">*</span></label>
                                <input type="number" step="0.1" required id="stock" name="stock" class="form-control" autocomplete="off" placeholder="0" min="0">
                                <small class="form-text text-muted">Stock in Kg (raw material) / Stock in Packet (recipe)</small>
                            </div>

                            <div class="form-group required">
                                <label class="control-label">Type <span style="color: red;"></span></label>
                                <div class="form-inline">
                                    <div class="form-group">
                                        <div class="radio">
                                            <label><input type="radio" name="type" value="raw" checked> Raw material</label>
                                        </div>
                                    </div>
                                    <br />
                                    @if(isset($flag_recipe))
                                    @if($flag_recipe == 1)
                                    <div class="form-group">
                                        <div class="radio">
                                            <label><input type="radio" name="type" value="recipe"> Recipe</label>
                                        </div>
                                    </div>
                                    @endif
                                    @endif
                                </div>
                            </div>

                            <div class="form-group required ingredientForm" hidden>
                                <label class="control-label">Choose Ingredients <span style="color: red;">*</span></label>

                                <div class="table-responsive">
                                    <!-- Table HTML -->
                                    <table id="productTable" class="table table-striped table-bordered table-hover" style="width: 100%; font-size: 8pt">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Code</th>
                                                <th>Product Name</th>
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

                            <div class="form-group required">
                                <label class="control-label">Description <span style="color: red;">*</span></label>
                                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                            </div>

                            <a role="button" href="{{ url('/data-master/product') }}" class="btn btn-default btn"><i class="fa fa-arrow-circle-left fa-fw"></i> Back</a>

                            <button type="button" id="btnSave" class="btn btn-success btn btn-ml" style="margin-left: 10px"><i class="fa fa-check fa-fw"></i> Save</button>
                        </div>

                        <div class="col-md-7">
                            <div class="form-group required ingredientForm" hidden>
                                <label class="control-label">Ingredients <span style="color: red;">*</span></label>
                                <table id="ingredientTable" class="table table-responsive table-bordered">
                                    <thead>
                                        <th>ID</th>
                                        <th class="text-center">No</th>
                                        <th class="text-center">Product Name</th>
                                        <th class="text-center">Req Stock</th>
                                        <th class="text-center">Estimate</th>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                                <small class="form-text text-muted">Ingredient for 1 packet recipe product</small>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<!-- page script -->
<script type="text/javascript">
    $(document).ready(function() {
        var dataIngredients = [];
        // ============================ Additional Function
        $('#name').select2({
            tags: true,
            allowClear: true,
            placeholder: "Input Product",
        });

        $('#stock').keypress(function(evt) {
            return (/^[0-9]*\.?[0-9]*$/).test($(this).val() + evt.key);
        });

        $('#stock').on('keyup change', function(evt) {
            var type = $("input[name='type']:checked").val();
            if (type == 'recipe') {
                var stock = $('#stock').val();
                for (var i = 0; i < dataIngredients.length; i++) {
                    dataIngredients[i].estimate_req = stock * dataIngredients[i].req_stock;
                }
                resetIngredientTable();
            }
        });

        var type = $("input[name='type']:checked").val();
        if (type == 'raw') {
            $('.ingredientForm').hide();
        } else if (type == 'recipe') {
            $('.ingredientForm').show();
        }

        $('input[type=radio][name=type]').change(function() {
            if (this.value == 'raw') {
                $('.ingredientForm').hide();
            } else if (this.value == 'recipe') {
                $('.ingredientForm').show();
            }
            productTable.rows().deselect();
            dataIngredients = [];
            resetIngredientTable();
        });

        // ============================ Initial Datatable
        var ingredientTable = $("#ingredientTable").DataTable({
            processing: false,
            serverSide: false,
            stateSave: false,
            rowCallback: function(row, data, index) {
                for (var i = 0; i < dataIngredients.length; i++) {
                    if (dataIngredients[i].id == data['id']) {
                        var available_stock = dataIngredients[i].stock;
                        if (available_stock < data['estimate_req']) {
                            $('td', row).css('background-color', 'Yellow');
                        }
                    }
                }
            },
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
                data: 'req_stock',
                name: 'req_stock',
                className: 'text-right'
            }, {
                data: 'estimate_req',
                name: 'estimate_req',
                className: 'text-right'
            }]
        });

        $('#ingredientTable').on('draw.dt', function() {
            $('#ingredientTable').Tabledit({
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
                        [3, 'req_stock'],
                    ]
                },
                onSuccess: function(data, textStatus, jqXHR) {
                    if (data.action == 'edit') {
                        var id = data.id;
                        if (data.hasOwnProperty('req_stock')) {
                            for (var i = 0; i < dataIngredients.length; i++) {
                                if (dataIngredients[i].id == id) {
                                    dataIngredients[i].req_stock = data.req_stock
                                    dataIngredients[i].estimate_req = $('#stock').val() * data.req_stock;
                                }
                            }
                        }
                        resetIngredientTable();
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
                for (var i = 0; i < dataIngredients.length; i++) {
                    if (dataIngredients[i].id == data['id']) {
                        $(row).addClass('selected');
                        dataIngredients[i].stock = data['stock'];

                        resetIngredientTable();
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
                    dataIngredients = [];
                    resetIngredientTable();
                },
                className: 'btn btn-default'
            }],
            dom: 'Bfrtip',
            ajax: {
                "url": "/datatable/products/raw-material",
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

                for (var i = 0; i < dataIngredients.length; i++) {
                    if (dataIngredients[i].id == id) {
                        dataIngredients.splice(i, 1);
                    }
                }

                resetIngredientTable();

            } else {
                $(this).addClass('selected');

                var id = productTable.row(this).data().id;
                var no = dataIngredients.length + 1;
                var productName = productTable.row(this).data().product_name;
                var stock = productTable.row(this).data().stock;

                dataIngredients.push({
                    'id': id,
                    'no': no,
                    'product_name': productName,
                    'req_stock': 0,
                    'estimate_req': 0,
                    'stock': stock
                });

                var resetIngredient = ingredientTable
                    .rows()
                    .remove()
                    .draw();

                for (var i = 0; i < dataIngredients.length; i++) {
                    ingredientTable.row.add(dataIngredients[i]).draw()
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

        function resetIngredientTable() {
            var resetIngredient = ingredientTable
                .rows()
                .remove()
                .draw();

            for (var i = 0; i < dataIngredients.length; i++) {
                dataIngredients[i].no = i + 1;
            }

            for (var i = 0; i < dataIngredients.length; i++) {
                ingredientTable.row.add(dataIngredients[i]).draw()
            }
        }

        // ============================ Core Function
        $('#btnSave').on('click', function() {
            $("#btnSave").prop('disabled', 'true');

            var productName = $('#name').val();
            var stock = $('#stock').val();
            var description = $('#description').val();
            var type = $("input[name='type']:checked").val();

            axios.post("/data-master/product/create", {
                    'name': productName,
                    'type': type,
                    'stock': stock,
                    'description': description,
                    'dataIngredients': dataIngredients
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
                            $('form#main_form')[0].reset();
                            $("form#main_form:not(.filter) :input:visible:enabled:first").focus();
                            window.location.replace(response.data.intended_url)
                        });
                    } else {
                        swal({
                            title: "Oops!",
                            text: response.data.message,
                            type: "error",
                            closeOnConfirm: false
                        });

                        productTable.ajax.reload();
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