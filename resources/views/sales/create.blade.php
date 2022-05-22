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
                                    <option data-tenggat="{{ $customer->kontrabon }}" value=" {{ $customer->id }}"> {{ $customer->name }}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>

                            <div class="form-group required">
                                <label class="control-label">Telephone</label>
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
                                            <label><input type="radio" name="type" value="tunai" checked> Tunai</label>
                                        </div>
                                    </div>
                                    <br />
                                    <div class="form-group">
                                        <div class="radio">
                                            <label><input type="radio" name="type" value="kontrabon"> Kontrabon</label>
                                        </div>
                                    </div>
                                    <br />
                                </div>
                            </div>

                            <div class="form-group required" id="formTunai">
                                <label class="control-label">Pembayaran <span style="color: red;"></span></label>
                                <div class="form-inline">
                                    <div class="form-group">
                                        <div class="radio">
                                            <label><input type="radio" name="pembayaranTunai" value="cash" checked> Cash</label>
                                        </div>
                                    </div>
                                    <br />
                                    <div class="form-group">
                                        <div class="radio">
                                            <label><input type="radio" name="pembayaranTunai" value="transfer"> Transfer</label>
                                        </div>
                                    </div>
                                    <br />
                                    <div class="form-group">
                                        <div class="radio">
                                            <label><input type="radio" name="pembayaranTunai" value="gyro"> Gyro</label>
                                        </div>
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
                                                <th>Stock</th>
                                                <th>Description</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
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
                                    <div class="form-group required" id="formTunai">
                                        <label class="control-label">Rekening <span style="color: red;"></span></label>
                                        <div class="form-inline">
                                            <div class="form-group">
                                                <div class="radio">
                                                    <label><input type="radio" name="rekening" value="8380113393 a/n Tonny Sutantyo" checked> Tonny Sutantyo - 8380113393</label>
                                                </div>
                                            </div>
                                            <br />
                                            <div class="form-group">
                                                <div class="radio">
                                                    <label><input type="radio" name="rekening" value="4531098298 a/n Tonny Sutantyo"> Tonny Sutantyo - 4531098298</label>
                                                </div>
                                            </div>
                                            <br />
                                            <div class="form-group">
                                                <div class="radio">
                                                    <label><input type="radio" name="rekening" value="6765676511 a/n Felix Chrisanto"> Felix Chrisanto - 6765676511</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="html-content" id="html-content" style="font-size: 8pt;">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <span id="type"></span>
                                            </div>
                                            <div class="col-md-6 text-center">
                                                Bandung, {{ date('d M Y') }} <br />
                                                Kepada YTH
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <br />
                                                <p style="padding-left: 25px;">Nota Faktur _ __</p>
                                            </div>
                                            <div class="col-md-6">
                                                Bapak/Ibu/Toko <br />
                                                <span id="customerName"></span> <span id="customerNumber"></span> <br />
                                                <span id="customerAddress"></span>
                                            </div>
                                        </div>
                                        <table class="table table-responsive table-bordered" id="notaTable" style="font-size: 8pt; width: 95%;">
                                            <thead>
                                                <tr style="background-color: #85c9e9;" class="table-bordered">
                                                    <th class="font-weight-bold text-center table-bordered custom" hidden><b>ID</b></th>
                                                    <th class="font-weight-bold text-center table-bordered custom"><b>Banyaknya</b></th>
                                                    <th class="font-weight-bold text-center table-bordered custom"><b>Sat.</b></th>
                                                    <th class="text-center table-bordered custom"><b>Nama Barang</b></th>
                                                    <th class="text-center table-bordered custom"><b>Harga Satuan</b></th>
                                                    <th class="text-center table-bordered custom"><b>Jumlah (Rp.)</b></th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                            </tbody>

                                            <tfoot style="border: none !important;">
                                                <th class="text-right" hidden></th>
                                                <th class="text-right" style="border: none;"></th>
                                                <th class="text-right" style="border: none;"></th>
                                                <th class="text-right" style="border: none;"></th>
                                                <th class="text-right" style="border: none;"><b>Jumlah (Rp)</b></th>
                                                <th class="text-center" style="border: none; padding: 5px 0 5px 0 !important;"><input type="text" id="total" class="form-control text-right"></th>
                                            </tfoot>
                                        </table>
                                        <div class="row" style="margin-top: 5px;">
                                            <div class="col-md-6">
                                                <p class="text-center">Tanda Terima</p>
                                                <br />
                                                <br />
                                                <p class="text-center">( ......................................... )</p>
                                            </div>
                                            <div class="col-md-6">
                                                <p class="text-center">Hormat Kami</p>
                                                <br />
                                                <br />
                                                <p class="text-center">( ......................................... )</p>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <span style="padding-left: 25px;" id="rek"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- <div class="row">
                                        <div class="col-md-12 text-right">
                                            <button id="printNota" class="btn btn-default" type="button">
                                                <i class="fa fa-print" aria-hidden="true"></i> Nota
                                            </button>
                                        </div>
                                    </div> -->
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
        // $('#printNota').on('click', function() {
        //     var HTML_Width = $(".html-content").width();
        //     var HTML_Height = $(".html-content").height();
        //     var top_left_margin = 15;
        //     var PDF_Width = HTML_Width + (top_left_margin * 2);
        //     var PDF_Height = (PDF_Width * 1.5) + (top_left_margin * 2);
        //     var canvas_image_width = HTML_Width;
        //     var canvas_image_height = HTML_Height;

        //     var totalPDFPages = Math.ceil(HTML_Height / PDF_Height) - 1;

        //     var node = document.getElementById('html-content');
        //     var options = {
        //         quality: 0.95
        //     };

        //     html2canvas($(".html-content")[0], {
        //         quality: 4,
        //         scale: 5
        //     }).then(function(canvas) {
        //         var imgData = canvas.toDataURL("image/jpeg", 1.0);
        //         var pdf = new jsPDF('p', 'pt', [PDF_Width, PDF_Height]);
        //         pdf.addImage(imgData, 'JPG', top_left_margin, top_left_margin, canvas_image_width, canvas_image_height);
        //         for (var i = 1; i <= totalPDFPages; i++) {
        //             pdf.addPage(PDF_Width, PDF_Height);
        //             pdf.addImage(imgData, 'JPG', top_left_margin, -(PDF_Height * i) + (top_left_margin * 4), canvas_image_width, canvas_image_height);
        //         }

        //         var customerName = $("#name option:selected").text();
        //         pdf.save("Nota-" + customerName + ".pdf");
        //     });
        // });

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
                    // "render": $.fn.dataTable.render.number('.', ',', 2, 'Rp ')
                    "render": $.fn.dataTable.render.number('.', ',', 2)
                },
                {
                    "targets": 5,
                    "data": 'total',
                    "render": $.fn.dataTable.render.number('.', ',', 2)
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
                data: 'qty',
                name: 'qty',
                className: 'text-center custom'
            }, {
                data: 'unit',
                name: 'unit',
                className: 'text-center custom'
            }, {
                data: 'product_name',
                name: 'product_name',
                className: 'custom'
            }, {
                data: 'price',
                name: 'price',
                className: 'text-right custom'
            }, {
                data: 'total',
                name: 'total',
                className: 'text-right custom'
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
                        [1, 'qty'],
                        [2, 'unit'],
                        [3, 'product_name'],
                        [4, 'price'],
                        [5, 'total']
                    ]
                },
                onSuccess: function(data, textStatus, jqXHR) {
                    if (data.action == 'edit') {
                        var id = data.id;
                        if (data.hasOwnProperty('qty')) {
                            for (var i = 0; i < dataProduct.length; i++) {
                                if (dataProduct[i].id == id) {
                                    dataProduct[i].qty = data.qty

                                    dataProduct[i].total = parseFloat(dataProduct[i].qty) * parseFloat(dataProduct[i].price)
                                }
                            }
                        }
                        if (data.hasOwnProperty('unit')) {
                            for (var i = 0; i < dataProduct.length; i++) {
                                if (dataProduct[i].id == id) {
                                    dataProduct[i].unit = data.unit
                                }
                            }
                        }
                        if (data.hasOwnProperty('product_name')) {
                            for (var i = 0; i < dataProduct.length; i++) {
                                if (dataProduct[i].id == id) {
                                    dataProduct[i].product_name = data.product_name
                                }
                            }
                        }
                        if (data.hasOwnProperty('price')) {
                            for (var i = 0; i < dataProduct.length; i++) {
                                if (dataProduct[i].id == id) {
                                    dataProduct[i].price = data.price

                                    dataProduct[i].total = parseFloat(dataProduct[i].qty) * parseFloat(dataProduct[i].price)
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
                },
                className: 'btn btn-default'
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
                    'total': 0,
                    'unit': '-'
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
            var sum = 0.00;
            for (var i = 0; i < dataProduct.length; i++) {
                sum = sum + parseFloat(dataProduct[i].total);
            }
            $('#total').val(sum.toLocaleString("es-ES", {
                minimumFractionDigits: 2
            }));
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
            $('#formTunai').show();
        } else {
            $('#formTunai').hide();
        }

        $('input[type=radio][name=type]').change(function() {
            if (this.value == 'tunai') {
                $('#formTunai').show();
                $('#type').text('');
            } else if (this.value == 'kontrabon') {
                var due = $("#name").select2().find(":selected").data("tenggat");

                if (due == undefined) {
                    swal({
                        title: "Oops!",
                        text: "Please input customer name.",
                        type: "error",
                        closeOnConfirm: false
                    });
                    $('input:radio[name=type]').filter('[value=tunai]').prop('checked', true);
                    $('#formTunai').show();
                    $('#type').text('');
                } else {
                    var date = new Date();
                    date.setDate(date.getDate() + due);

                    var mydate = new Date(date);
                    var date = mydate.getDate();
                    var month = ["January", "February", "March", "April", "May", "June",
                        "July", "August", "September", "October", "November", "December"
                    ][mydate.getMonth()];
                    var formatDate = date + ' ' + month + ' ' + mydate.getFullYear();

                    $('#formTunai').hide();
                    $('#type').text('Jatuh Tempo : ' + formatDate);
                }
            }
        });

        var rekening = $("input[name='rekening']:checked").val();
        $('#rek').text('Catatan: untuk pembayaran transfer dapat dikirimkan ke ' + rekening);


        $('input[type=radio][name=rekening]').change(function() {
            $('#rek').text('Catatan: untuk pembayaran transfer dapat dikirimkan ke ' + this.value);
        })
        //  ================================= Create Sales

        $("#btnSave").on('click', function() {
            $("#btnSave").prop('disabled', 'true');

            // // PRINT NOTA
            // var HTML_Width = $(".html-content").width();
            // var HTML_Height = $(".html-content").height();
            // var top_left_margin = 15;
            // var PDF_Width = HTML_Width + (top_left_margin * 2);
            // var PDF_Height = (PDF_Width * 1.5) + (top_left_margin * 2);
            // var canvas_image_width = HTML_Width;
            // var canvas_image_height = HTML_Height;

            // var totalPDFPages = Math.ceil(HTML_Height / PDF_Height) - 1;

            // var node = document.getElementById('html-content');
            // var options = {
            //     quality: 0.95
            // };

            // html2canvas($(".html-content")[0], {
            //     quality: 4,
            //     scale: 5
            // }).then(function(canvas) {
            //     var imgData = canvas.toDataURL("image/jpeg", 1.0);
            //     var pdf = new jsPDF('p', 'pt', [PDF_Width, PDF_Height]);
            //     pdf.addImage(imgData, 'JPG', top_left_margin, top_left_margin, canvas_image_width, canvas_image_height);
            //     for (var i = 1; i <= totalPDFPages; i++) {
            //         pdf.addPage(PDF_Width, PDF_Height);
            //         pdf.addImage(imgData, 'JPG', top_left_margin, -(PDF_Height * i) + (top_left_margin * 4), canvas_image_width, canvas_image_height);
            //     }

            //     var customerName = $("#name option:selected").text();
            //     pdf.save("Nota-" + customerName + ".pdf");
            // });

            // =========================
            var flag = true;
            var customerId = $('#name').val();
            var customerName = $("#name option:selected").text();
            var phoneNumber = $("#telephone option:selected").text();
            var address = $("#address option:selected").text();
            var type = $('input[name="type"]:checked').val();
            var total = $('#total').val();

            var tenggat = 0;
            var pembayaran = '';
            if (type == 'tunai') {
                var pembayaran = $('input[name="pembayaranTunai"]:checked').val();
            }
            if (type == 'kontrabon') {
                var tenggat = $("#name").select2().find(":selected").data("tenggat");
                if (tenggat == undefined) {
                    swal({
                        title: "Oops!",
                        text: "Please input customer name.",
                        type: "error",
                        closeOnConfirm: false
                    });
                    flag == false;
                }
            }

            if (flag == true) {
                axios.post("/sales/create", {
                        'customer_id': customerId,
                        'customer_name': customerName,
                        'phone_number': phoneNumber,
                        'address': address,
                        'type': type,
                        'payment': pembayaran,
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
                                // PRINT NOTA
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

                                    var blob = pdf.output('blob');

                                    var formData = new FormData();
                                    formData.append('pdf', blob);
                                    formData.append('name', new Date().getTime());

                                    $.ajax('/additional/upload', {
                                        method: 'POST',
                                        data: formData,
                                        processData: false,
                                        contentType: false,
                                        headers: {
                                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                        },
                                        success: function(data) {
                                            console.log(data)
                                        },
                                        error: function(data) {
                                            console.log(data)
                                        }
                                    });

                                    // var customerName = $("#name option:selected").text();
                                    // pdf.save("Nota -" + customerName + ".pdf");

                                    // window.location.replace(response.data.intended_url)

                                });
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
            }

        });

    });
</script>

@endsection