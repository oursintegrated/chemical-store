@extends('layouts.backend')
@section('title', 'Chemical Store | Customer')
@section('content')
<div class="row row-cards-pf">
    <div class="row-cards-pf card-pf">
        <ol class="breadcrumb">
            <li>
                <span class="pficon pficon-home"></span>
                <a href="{{url('dashboard')}}">Dashboard</a>
            </li>
            <li>
                <span class="pficon pficon-orders"></span>
                <a href="{{url('/sales')}}">Sales</a>
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
                    <span class="pficon pficon-orders"></span>
                    Sales
                    <small>Create</small>
                </h1>
            </div>
            <div class="card-pf-body">
                <div class="row">
                    <form id="main_form" autocomplete="off">
                        {{ csrf_field() }}
                        <div class="col-md-5">
                            <div class="form-group required">
                                <label class="control-label">Name <span style="color: red;">*</span></label>
                                <select id="name" name="name" class="form-control">
                                    <option></option>
                                    @if(isset($customers))
                                    @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}"> {{ $customer->name }}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>

                            <div class="form-group required">
                                <label class="control-label">Telephone <span style="color: red;">*</span></label>
                                <select class="form-control" id="telephone"></select>
                            </div>

                            <div class="form-group required">
                                <label class="control-label">Address <span style="color: red;"></span></label>
                                <select class="form-control" id="address"></select>
                            </div>

                            <div class="form-group required">
                                <label class="control-label">Type <span style="color: red;"></span></label>
                                <div class="form-inline">
                                    <div class="form-group">
                                        <div class="radio">
                                            <label><input type="radio" name="type" value="tunai" checked>Tunai</label>
                                        </div>
                                    </div>
                                    <br />
                                    <div class="form-group">
                                        <div class="radio">
                                            <label><input type="radio" name="type" value="kasbon">Kasbon</label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <input disabled type="number" class="form-control" id="tenggat" name="tenggat" placeholder="Due date">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Product <span style="color: red;"></span></label>

                                <div class="table-responsive">
                                    <!-- Table HTML -->
                                    <table id="productTable" class="table table-striped table-bordered table-hover" style="width: 100%; font-size: 8pt">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Code</th>
                                                <th>Product Name</th>
                                                <th>Stock In Kg</th>
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
                            <a role="button" href="{{ url('/sales') }}" class="btn btn-default btn"><i class="fa fa-arrow-circle-left fa-fw"></i> Back</a>

                            <button type="button" id="btnSave" class="btn btn-success btn btn-ml" style="margin-left: 10px"><i class="fa fa-check fa-fw"></i> Save</button>
                        </div>

                        <div class="col-md-7">
                            <div class="panel panel-default">
                                <div class="panel-heading">Nota</div>
                                <div class="panel-body">
                                    <div class="html-content" id="html-content">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <span id="type"></span>
                                            </div>
                                            <div class="col-md-6 text-center">
                                                Bandung, {{ date('d M Y') }} <br />
                                                Kepada YTH
                                            </div>
                                        </div>
                                        <br />
                                        <div class="row">
                                            <div class="col-md-6"></div>
                                            <div class="col-md-6">
                                                Bapak/Ibu/Toko <br />
                                                <span id="customerName"></span> <span id="customerNumber"></span> <br />
                                                <span id="customerAddress"></span>
                                            </div>
                                        </div>
                                        <br />
                                        <table class="table table-responsive table-bordered" id="notaTable" style="font-size: 8pt;">
                                            <thead>
                                                <tr style="background-color: #85c9e9;" class="table-bordered">
                                                    <th class="font-weight-bold text-center table-bordered" hidden><b>ID</b></th>
                                                    <th class="font-weight-bold text-center table-bordered"><b>No</b></th>
                                                    <th class="text-center table-bordered"><b>Nama Barang</b></th>
                                                    <th class="text-center table-bordered"><b>Qty</b></th>
                                                    <th class="text-center table-bordered"><b>Harga Satuan</b></th>
                                                    <th class="text-center table-bordered"><b>Jumlah (Rp)</b></th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                            </tbody>

                                            <tfoot>
                                                <th class="text-right table-bordered" hidden></th>
                                                <th class="text-right" style="border: none;"></th>
                                                <th class="text-right" style="border: none;"></th>
                                                <th class="text-right" style="border: none;"></th>
                                                <th class="text-right" style="border: none;"><b>Jumlah (Rp)</b></th>
                                                <th class="text-center" style="border: none;"><input type="text" id="total" class="form-control text-right"></th>
                                            </tfoot>
                                        </table>
                                        <br />
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p class="text-center">Tanda Terima</p>
                                                <br />
                                                <br />
                                                <br />
                                            </div>
                                            <div class="col-md-6">
                                                <p class="text-center">Hormat Kami</p>
                                                <br />
                                                <br />
                                                <br />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12 text-right">
                                            <button id="printNota" class="btn btn-default" type="button">
                                                <i class="fa fa-print" aria-hidden="true"></i> Nota
                                            </button>
                                        </div>
                                    </div>
                                </div>
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
        var dataProduct = [];

        // ================================= Create PDf from HTML
        $('#printNota').on('click', function() {
            var HTML_Width = $(".html-content").width();
            var HTML_Height = $(".html-content").height();
            var top_left_margin = 15;
            var PDF_Width = HTML_Width + (top_left_margin * 2);
            var PDF_Height = (PDF_Width * 1.5) + (top_left_margin * 2);
            var canvas_image_width = HTML_Width;
            var canvas_image_height = HTML_Height;

            var totalPDFPages = Math.ceil(HTML_Height / PDF_Height) - 1;

            var node = document.getElementById('html-content');
            var options = {
                quality: 0.95
            };

            html2canvas($(".html-content")[0], {
                quality: 4,
                scale: 5
            }).then(function(canvas) {
                var imgData = canvas.toDataURL("image/jpeg", 1.0);
                var pdf = new jsPDF('p', 'pt', [PDF_Width, PDF_Height]);
                pdf.addImage(imgData, 'JPG', top_left_margin, top_left_margin, canvas_image_width, canvas_image_height);
                for (var i = 1; i <= totalPDFPages; i++) {
                    pdf.addPage(PDF_Width, PDF_Height);
                    pdf.addImage(imgData, 'JPG', top_left_margin, -(PDF_Height * i) + (top_left_margin * 4), canvas_image_width, canvas_image_height);
                }

                var customerName = $("#name option:selected").text();
                pdf.save("Nota-" + customerName + ".pdf");
            });
        });

        // ================================= Initial DataTable

        var notaTable = $("#notaTable").DataTable({
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
                },
                {
                    "targets": 3,
                    "data": 'qty',
                    "render": $.fn.dataTable.render.number('.', ',')
                },
                {
                    "targets": 4,
                    "data": 'price',
                    "render": $.fn.dataTable.render.number('.', ',', 2, 'Rp ')
                },
                {
                    "targets": 5,
                    "data": 'total',
                    "render": $.fn.dataTable.render.number('.', ',', 2, 'Rp ')
                }
            ],
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
            }, {
                data: 'price',
                name: 'price',
                className: 'text-right'
            }, {
                data: 'total',
                name: 'total',
                className: 'text-right'
            }, ]
        });

        $('#notaTable').on('draw.dt', function() {
            $('#notaTable').Tabledit({
                url: '/datatable/sales/tabledit',
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
                        [4, 'price'],
                        [5, 'total'],
                    ]
                },
                onSuccess: function(data, textStatus, jqXHR) {
                    if (data.action == 'edit') {
                        var id = data.id;
                        if (data.hasOwnProperty('qty')) {
                            for (var i = 0; i < dataProduct.length; i++) {
                                if (dataProduct[i].id == id) {
                                    dataProduct[i].qty = data.qty

                                    dataProduct[i].total = parseFloat(dataProduct[i].qty * dataProduct[i].price)
                                }
                            }
                        }
                        if (data.hasOwnProperty('price')) {
                            for (var i = 0; i < dataProduct.length; i++) {
                                if (dataProduct[i].id == id) {
                                    dataProduct[i].price = data.price

                                    dataProduct[i].total = parseFloat(dataProduct[i].qty * dataProduct[i].price)
                                }
                            }

                        }
                        if (data.hasOwnProperty('total')) {
                            for (var i = 0; i < dataProduct.length; i++) {
                                if (dataProduct[i].id == id) {
                                    dataProduct[i].total = data.total
                                }
                            }
                        }

                        resetNotaTable();

                        sumTotal();
                    }
                }
            });
        });

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
                    dataProduct = [];
                    resetNotaTable();
                    sumTotal();
                }
            }],
            dom: 'Bfrtip',
            ajax: {
                "url": "/datatable/products",
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
                data: 'stock_kg',
                name: 'stock_kg'
            }, {
                data: 'description',
                name: 'description'
            }, ]
        });

        $('#productTable tbody').on('click', 'tr', function() {
            if ($(this).hasClass('selected')) {
                $(this).removeClass('selected');

                var id = productTable.row(this).data().id;

                for (var i = 0; i < dataProduct.length; i++) {
                    if (dataProduct[i].id == id) {
                        dataProduct.splice(i, 1);
                    }
                }

                resetNotaTable();

                sumTotal();

            } else {
                $(this).addClass('selected');

                var id = productTable.row(this).data().id;
                var no = dataProduct.length + 1;
                var productName = productTable.row(this).data().product_name;

                dataProduct.push({
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

                for (var i = 0; i < dataProduct.length; i++) {
                    notaTable.row.add(dataProduct[i]).draw()
                }

                sumTotal();
            }
        });

        // ================================= additional function
        function addCommas(nStr) {
            nStr += '';
            var x = nStr.split('.');
            var x1 = x[0];
            var x2 = x.length > 1 ? '.' + x[1] : '';
            var rgx = /(\d+)(\d{3})/;
            while (rgx.test(x1)) {
                x1 = x1.replace(rgx, '$1' + '.' + '$2');
            }
            return x1 + x2;
        }

        function sumTotal() {
            var sum = 0;
            for (var i = 0; i < dataProduct.length; i++) {
                sum = sum + parseInt(dataProduct[i].total);
            }
            $('#total').val(addCommas(sum));
        }

        function resetNotaTable() {
            var resetNota = notaTable
                .rows()
                .remove()
                .draw();

            for (var i = 0; i < dataProduct.length; i++) {
                dataProduct[i].no = i + 1;
            }

            for (var i = 0; i < dataProduct.length; i++) {
                notaTable.row.add(dataProduct[i]).draw()
            }
        }

        $('#name').select2({
            placeholder: "Input Customer Name",
        });

        $('#telephone').on('change', function() {
            var customerNumber = $("#telephone option:selected").text();
            $("#customerNumber").text(' - ' + customerNumber);
        });

        $('#address').on('change', function() {
            var customerAddress = $("#address option:selected").text();
            $("#customerAddress").text(customerAddress);
        });

        $('#name').on('change', function() {
            // reset
            $("#telephone option").each(function() {
                $(this).remove();
            });
            $("#address option").each(function() {
                $(this).remove();
            });

            var customerId = $('#name').val();
            axios.post("/additional/sales/customer", {
                    'customerId': customerId
                })
                .then(function(response) {
                    if (response.data.status == 1) {
                        var telephones = response.data.telephones;
                        var addresses = response.data.addresses;
                        if (telephones.length > 0) {
                            for (var i = 0; i < telephones.length; i++) {
                                optionText = telephones[i].phone;
                                optionValue = telephones[i].id;

                                $('#telephone').append(new Option(optionText, optionValue));
                            }
                        }
                        if (addresses.length > 0) {
                            for (var j = 0; j < addresses.length; j++) {
                                optionText = addresses[j].location;
                                optionValue = addresses[j].id;

                                $('#address').append(new Option(optionText, optionValue));
                            }
                        }

                        var customerName = $("#name option:selected").text();
                        $("#customerName").text(customerName);

                        var customerNumber = $("#telephone option:selected").text();
                        $("#customerNumber").text(' - ' + customerNumber);

                        var customerAddress = $("#address option:selected").text();
                        $("#customerAddress").text(customerAddress);
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

        var type = $("input[name='type']:checked").val();
        if (type == 'tunai') {
            $('#type').text('T');
        } else if (type == 'kasbon') {
            $('#type').text('K');
        }

        $('input[type=radio][name=type]').change(function() {
            if (this.value == 'tunai') {
                $('#tenggat').prop('disabled', true);
                $('#tenggat').val('');
                $('#type').text('T');
            } else if (this.value == 'kasbon') {
                $('#tenggat').prop('disabled', false);
                $('#type').text('K');
            }
        });

        $('#tenggat').on('keyup change', function(event) {
            $(this).val($(this).val().replace(/[^0-9\.]/g, ''));
            if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
                event.preventDefault();
            }

            $('#type').text('K' + $('#tenggat').val());
        });

        //  ================================= Create Sales

        $("#btnSave").on('click', function() {
            $("#btnSave").prop('disabled', 'true');

            var customerId = $('#name').val();
            var customerName = $("#name option:selected").text();
            var phoneNumber = $("#telephone option:selected").text();
            var address = $("#address option:selected").text();
            var type = $('input[name="type"]:checked').val();
            var tenggat = 0;
            if (type == 'kasbon') {
                tenggat = $('#tenggat').val();
            }
            var total = $('#total').val();

            // console.log('data', {
            //     'customer_id': customerId,
            //     'customer_name': customerName,
            //     'phone_id': phoneId,
            //     'phone_number': phoneNumber,
            //     'address_id': addressId,
            //     'address_loc': addressLoc,
            //     'type': type,
            //     'tenggat': tenggat,
            //     'data_product': dataProduct
            // })

            axios.post("/sales/create", {
                    'customer_id': customerId,
                    'customer_name': customerName,
                    'phone_number': phoneNumber,
                    'address': address,
                    'type': type,
                    'due_date': tenggat,
                    'data_product': dataProduct,
                    'total': total
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