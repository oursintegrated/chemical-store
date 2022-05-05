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
                                    <div class="form-group">
                                        <div class="radio">
                                            <label><input type="radio" name="type" value="recipe"> Recipe</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group required">
                                <label class="control-label">Description <span style="color: red;">*</span></label>
                                <textarea class="form-control" name="description" rows="3"></textarea>
                            </div>

                            <a role="button" href="{{ url('/data-master/product') }}" class="btn btn-default btn"><i class="fa fa-arrow-circle-left fa-fw"></i> Back</a>

                            <button type="button" id="btnSave" class="btn btn-success btn btn-ml" onclick="create_product()" style="margin-left: 10px"><i class="fa fa-check fa-fw"></i> Save</button>
                        </div>

                        <div class="col-md-7" id="ingredientForm" hidden>
                            <div class="form-group required">
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
                                <br />
                                <table id="ingredientsTable" class="table table-responsive table-bordered">
                                    <thead>
                                        <th>Product Name</th>
                                        <th>Req Stock</th>
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

        var type = $("input[name='type']:checked").val();
        if (type == 'raw') {
            $('#ingredientForm').hide();
        } else if (type == 'recipe') {
            $('#ingredientForm').show();
        }

        $('input[type=radio][name=type]').change(function() {
            if (this.value == 'raw') {
                $('#ingredientForm').hide();
            } else if (this.value == 'recipe') {
                $('#ingredientForm').show();
            }
        });

        // ============================ Initial Datatable
        var productTable = $("#productTable").DataTable({
            processing: true,
            serverSide: false,
            stateSave: false,
            stateDuration: 0,
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
                }
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

                resetNotaTable();

                sumTotal();

            } else {
                $(this).addClass('selected');

                var id = productTable.row(this).data().id;
                var no = dataIngredients.length + 1;
                var productName = productTable.row(this).data().product_name;

                dataIngredients.push({
                    'id': id,
                    'no': no,
                    'product_name': productName,
                    'qty': 0,
                    'price': 0,
                    'total': 0
                });

                var resetNota = notaTable
                    .rows()
                    .remove()
                    .draw();

                for (var i = 0; i < dataIngredients.length; i++) {
                    notaTable.row.add(dataProduct[i]).draw()
                }

                sumTotal();
            }
        });

        $("tfoot .datepicker").datepicker({
            autoclose: true,
            endDate: "0d",
            format: "dd MM yyyy",
            todayHighlight: true,
            weekStart: 1,
        });
    });


    // ============================ Core Function
    var create_product = function() {
        $("#btnSave").prop('disabled', 'true');

        axios.post("/data-master/product/create", $('#main_form').serialize())
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