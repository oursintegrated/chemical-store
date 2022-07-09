@extends('layouts.backend')
@section('title', 'Chemical Store | Surat Jalan')
@section('content')
<div class="row row-cards-pf">
    <div class="row-cards-pf card-pf">
        <ol class="breadcrumb">
            <li>
                <span class="pficon pficon-home"></span>
                <a href="{{url('dashboard')}}">Dashboard</a>
            </li>
            <li class="active">
                Surat Jalan
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
                    <span class="fa fa-truck"></span>
                    Surat Jalan
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

                            <div class="form-group">
                                <label class="control-label">Product <span style="color: red;"></span></label>

                                <div class="table-responsive">
                                    <!-- Table HTML -->
                                    <table id="productTable" class="table table-striped table-bordered table-hover" style="width: 100%; font-size: 10pt">
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
                            <a role="button" href="{{ url('/dashboard') }}" class="btn btn-default btn"><i class="fa fa-arrow-circle-left fa-fw"></i> Back</a>

                            <button type="button" id="btnSave" class="btn btn-success btn btn-ml" style="margin-left: 10px"><i class="fa fa-check fa-fw"></i> Print</button>
                        </div>

                        <div class="col-md-7">
                            <div class="panel panel-default">
                                <div class="panel-heading">Surat Jalan</div>
                                <div class="panel-body">
                                    <div class="html-content" id="html-content" style="font-size: 12px;">
                                        <div class="row">
                                            <div class="col-md-6">

                                            </div>
                                            <div class="col-md-6 text-center">
                                                Bandung, {{ date('d M Y') }} <br />
                                                Kepada YTH
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                Surat Jalan No. <br />
                                                Bersama ini kendaraan .............................. No .............................. <br />
                                                Kami kirimkan barang tersebut, di bawah ini <br />
                                                harap terima dengan baik
                                            </div>
                                            <div class="col-md-6">
                                                Bapak/Ibu/Toko <br />
                                                <span id="customerName"></span> <span id="customerNumber"></span> <br />
                                                <span id="customerAddress"></span>
                                            </div>
                                        </div>
                                        <table class="table table-responsive" id="notaTable" style="font-size: 12px; width: 95%; white-space: nowrap; margin-top: 10px">
                                            <thead>
                                                <tr style="background-color: #85c9e9;" class="table-bordered">
                                                    <th class="font-weight-bold text-center table-bordered custom" hidden><b>ID</b></th>
                                                    <th class="font-weight-bold text-center table-bordered custom"><b>Banyaknya</b></th>
                                                    <th class="font-weight-bold text-center table-bordered custom"><b>Sat.</b></th>
                                                    <th class="text-center table-bordered custom"><b>Nama Barang</b></th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                            </tbody>

                                            <tfoot style="border: none !important;">
                                                <th class="text-right" hidden></th>
                                                <th class="text-right" style="border: none;"></th>
                                                <th class="text-right" style="border: none;"></th>
                                                <th class="text-right" style="border: none;"></th>
                                            </tfoot>
                                        </table>
                                        <div class="row" style="margin-top: 1px;">
                                            <div class="col-md-6">
                                                <p class="text-center">Tanda Tangan yang terima,</p>
                                                <br />
                                                <p class="text-center">( ......................................... )</p>
                                            </div>
                                            <div class="col-md-6">
                                                <p class="text-center">Hormat Kami</p>
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
<script src="{{ asset('js/JSPrintManager-master/scripts/JSPrintManager.js') }}"></script>
<script src="{{ asset('js/bluebird.js') }}"></script>

<script type="text/javascript">
    $(document).ready(function() {
        // //WebSocket settings
        // JSPM.JSPrintManager.auto_reconnect = true;
        // JSPM.JSPrintManager.start();
        // JSPM.JSPrintManager.WS.onStatusChanged = function() {
        //     if (jspmWSStatus()) {
        //         // //get client installed printers
        //         // JSPM.JSPrintManager.getPrinters().then(function(myPrinters) {
        //         //     var options = '';
        //         //     for (var i = 0; i < myPrinters.length; i++) {
        //         //         console.log(myPrinters[i]);
        //         //     }
        //         // });
        //     }
        // };

        //Check JSPM WebSocket status
        // function jspmWSStatus() {
        //     if (JSPM.JSPrintManager.websocket_status == JSPM.WSStatus.Open)
        //         return true;
        //     else if (JSPM.JSPrintManager.websocket_status == JSPM.WSStatus.Closed) {
        //         alert('JSPrintManager (JSPM) is not installed or not running! Download JSPM Client App from https://neodynamic.com/downloads/jspm');
        //         return false;
        //     } else if (JSPM.JSPrintManager.websocket_status == JSPM.WSStatus.Blocked) {
        //         alert('JSPM has blocked this website!');
        //         return false;
        //     }
        // }

        var dataProduct = [];

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
                        [3, 'product_name']
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

                        resetNotaTable();
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

        //  ================================= Create Sales

        $("#btnSave").on('click', function() {
            // if (jspmWSStatus()) {
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
                    flag = false;
                }
            }

            if (customerName == "") {
                swal({
                    title: "Oopss!",
                    text: "Customer name is required!",
                    type: "error",
                    timer: 1000,
                    confirmButtonText: 'Ok',
                    closeOnConfirm: false
                });
                flag = false;
            }

            if (dataProduct.length == 0) {
                swal({
                    title: "Oopss!",
                    text: "Please input data product!",
                    type: "error",
                    timer: 1000,
                    confirmButtonText: 'Ok',
                    closeOnConfirm: false
                });
                flag = false;
            }

            if (flag == true) {
                $("#btnSave").prop('disabled', 'true');

                axios.post("/surat-jalan/create", {
                        'customer_id': customerId,
                        'customer_name': customerName,
                        'phone_number': phoneNumber,
                        'address': address,
                        'data_product': dataProduct
                    })
                    .then(function(response) {
                        if (response.data.status == 1) {
                            swal({
                                    title: "Good!",
                                    text: response.data.message,
                                    type: "success",
                                    timer: 1000,
                                    confirmButtonText: 'Ok',
                                    closeOnConfirm: false
                                })
                                .then(function() {
                                    window.location.replace(response.data.intended_url);
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

                // // ============== GENERATE PDF
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
                //     quality: 2,
                //     scale: 3
                // }).then(function(canvas) {
                //     // var imgBase64DataUri = canvas.toDataURL("image/jpeg", 1.0);
                //     var imgBase64DataUri = canvas.toDataURL("image/png", 1.0);

                //     // // ============== PRINT PDF

                //     // //Create a ClientPrintJob
                //     // var cpj = new JSPM.ClientPrintJob();
                //     // //Set Printer type (Refer to the help, there many of them!)
                //     // cpj.clientPrinter = new JSPM.DefaultPrinter();

                //     // //Set content to print... 
                //     // var b64Prefix = "data:image/png;base64,";
                //     // var imgBase64Content = imgBase64DataUri.substring(b64Prefix.length, imgBase64DataUri.length);

                //     // var myImageFile = new JSPM.PrintFile(imgBase64Content, JSPM.FileSourceType.Base64, 'myFileToPrint.png', 1);
                //     // //add file to print job
                //     // cpj.files.push(myImageFile);

                //     // //Send print job to printer!
                //     // cpj.sendToClient();

                //     var pdf = new jsPDF('p', 'pt', [PDF_Width, PDF_Height]);
                //     pdf.addImage(imgBase64DataUri, 'JPG', top_left_margin, top_left_margin, canvas_image_width, canvas_image_height);
                //     for (var i = 1; i <= totalPDFPages; i++) {
                //         pdf.addPage(PDF_Width, PDF_Height);
                //         pdf.addImage(imgBase64DataUri, 'JPG', top_left_margin, -(PDF_Height * i) + (top_left_margin * 4), canvas_image_width, canvas_image_height);
                //     }

                //     var blob = pdf.output('blob');

                //     // var formData = new FormData();
                //     // formData.append('pdf', blob);
                //     // formData.append('name', new Date().getTime());

                //     // // MOVE PDF
                //     // $.ajax('/additional/upload', {
                //     //     method: 'POST',
                //     //     data: formData,
                //     //     processData: false,
                //     //     contentType: false,
                //     //     headers: {
                //     //         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                //     //     },
                //     //     success: function(data) {

                //     //     }
                //     // });
                //     // if (data.status == 1) {
                //     var filename = 'myNota.pdf';

                //     var cpj = new JSPM.ClientPrintJob();
                //     cpj.clientPrinter = new JSPM.DefaultPrinter();
                //     // var my_file = new JSPM.PrintFilePDF('/uploads/' + filename, JSPM.FileSourceType.URL, filename, 1);
                //     var my_file = new JSPM.PrintFilePDF(blob, JSPM.FileSourceType.BLOB, filename, 1);
                //     cpj.files.push(my_file);
                //     cpj.sendToClient();

                //     // ============== INSERT DB
                // axios.post("/sales/create", {
                //         'customer_id': customerId,
                //         'customer_name': customerName,
                //         'phone_number': phoneNumber,
                //         'address': address,
                //         'type': type,
                //         'payment': pembayaran,
                //         'due_date': tenggat,
                //         'data_product': dataProduct,
                //         'total': total
                //     })
                //     .then(function(response) {
                //         if (response.data.status == 1) {
                //             swal({
                //                 title: "Good!",
                //                 text: response.data.message,
                //                 type: "success",
                //                 timer: 1000,
                //                 confirmButtonText: 'Ok',
                //                 closeOnConfirm: false
                //             }).then(function() {
                //                 window.location.replace(response.data.intended_url);
                //             });
                //         } else {
                //             swal({
                //                 title: "Oops!",
                //                 text: response.data.message,
                //                 type: "error",
                //                 closeOnConfirm: false
                //             });
                //         }
                //         $("#btnSave").removeAttr('disabled');
                //     })
                //     .catch(function(error) {
                //         switch (error.response.status) {
                //             case 422:
                //                 swal({
                //                     title: "Oops!",
                //                     text: 'Failed form validation. Please check your input.',
                //                     type: "error"
                //                 });
                //                 break;
                //             case 500:
                //                 swal({
                //                     title: "Oops!",
                //                     text: 'Something went wrong.',
                //                     type: "error"
                //                 });
                //                 break;
                //         }
                //         $("#btnSave").removeAttr('disabled');
                //     });
                //     // } else {
                //     //     swal({
                //     //         title: "Oops!",
                //     //         text: "Failed Move Data...",
                //     //         type: "error",
                //     //         closeOnConfirm: false
                //     //     });
                //     // }
                //     // },
                //     // error: function(data) {
                //     //     swal({
                //     //         title: "Oops!",
                //     //         text: "Failed to print nota...",
                //     //         type: "error",
                //     //         closeOnConfirm: false
                //     //     });
                //     // }
                //     // });
                //     // var customerName = $("#name option:selected").text();
                //     // pdf.save("Nota -" + customerName + ".pdf");

                //     // window.location.replace(response.data.intended_url)
                // });
            }
            // }

        });

    });
</script>

@endsection