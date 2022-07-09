@extends('layouts.backend')
@section('title', 'Chemical Store | Sales')
@section('content')
<div class="row row-cards-pf">
    <div class="row-cards-pf card-pf">
        <ol class="breadcrumb">
            <li>
                <span class="pficon pficon-home"></span>
                <a href="{{url('dashboard')}}">Dashboard</a>
            </li>
            <li>
                <span class="pficon pficon-user"></span>
                <a href="{{url('/sales')}}">Sales</a>
            </li>
            <li class="active">
                <strong>Detail Sales</strong>
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
                    <span class="pficon pficon-orders"></span>
                    Sales
                    <small>Detail</small>
                </h1>
            </div>
            <div class="card-pf-body">
                <div class="row">
                    <p>&nbsp;</p>
                </div>
                <div class="row">
                    <!-- <div class="col-md-2"></div> -->
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <input type="hidden" id="id" name="id" value="{{ $orderHeader->id }}">
                            <div class="panel-heading">Nota @if(isset($orderHeader)) : {{ $orderHeader->sales_code }} @endif</div>
                            <div class="panel-body">
                                <div class="html-content" id="html-content">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <span>@if(isset($orderHeader)) @if($orderHeader->type == 'kontrabon') Jatuh Tempo : {{ $due_date }} @endif @endif</span>
                                        </div>
                                        <div class="col-md-6 text-center">
                                            Bandung, {{ $transaction_date }} <br />
                                            Kepada YTH
                                        </div>
                                    </div>
                                    <br />
                                    <div class="row">
                                        <div class="col-md-7"></div>
                                        <div class="col-md-5">
                                            Bapak/Ibu/Toko <br />
                                            <span>@if(isset($orderHeader)) {{ $orderHeader->customer_name }} @endif</span> - <span> @if(isset($orderHeader)) {{ $orderHeader->phone_number }} @endif </span> <br />
                                            <span>@if(isset($orderHeader)) {{ $orderHeader->address }} @endif</span>
                                        </div>
                                    </div>
                                    <br />
                                    <table class="table table-responsive table-bordered" id="notaTable" style="font-size: 8pt;">
                                        <thead>
                                            <tr style="background-color: #85c9e9;" class="table-bordered">
                                                <th class="font-weight-bold text-center table-bordered" hidden><b>ID</b></th>
                                                <th class="font-weight-bold text-center table-bordered"><b>Banykanya</b></th>
                                                <th class="text-center table-bordered"><b>Sat.</b></th>
                                                <th class="text-center table-bordered"><b>Nama Barang</b></th>
                                                <th class="text-center table-bordered"><b>Harga Satuan</b></th>
                                                <th class="text-center table-bordered"><b>Jumlah (Rp.)</b></th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @if(isset($orderDetails))
                                            @for($i=0; $i<count($orderDetails); $i++) <tr>
                                                <td class="text-center">{{ $orderDetails[$i]->qty }}</td>
                                                <td class="text-center">{{ $orderDetails[$i]->unit }}</td>
                                                <td class="text-center">{{ $orderDetails[$i]->product_name }}</td>
                                                <td class="text-center">Rp. {{ number_format($orderDetails[$i]->price, 2, ',' ,'.') }}</td>
                                                <td class="text-center">Rp. {{ number_format($orderDetails[$i]->total, 2, ',', '.') }}</td>
                                                </tr>
                                                @endfor
                                                @endif
                                        </tbody>

                                        <tfoot>
                                            <th class="text-right table-bordered" hidden></th>
                                            <th class="text-right" style="border: none;"></th>
                                            <th class="text-right" style="border: none;"></th>
                                            <th class="text-right" style="border: none;"></th>
                                            <th class="text-right" style="border: none;"><b>Jumlah (Rp)</b></th>
                                            <th class="text-center" style="border: none;"><input type="text" readonly id="total" class="form-control text-right" value="{{ number_format($orderHeader->total, 2, ',' , '.') }}"></th>
                                        </tfoot>
                                    </table>
                                    <br />
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p class="text-center">Tanda Terima</p>
                                            <br />
                                            <br />
                                            <br />
                                            <p class="text-center">( ......................................... )</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="text-center">Hormat Kami</p>
                                            <br />
                                            <br />
                                            <br />
                                            <p class="text-center">( ......................................... )</p>
                                        </div>
                                    </div>
                                </div>
                                Credit
                                <br />
                                <!-- Table HTML -->
                                <table id="creditTable" class="table table-striped table-bordered table-hover" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <!-- <th class="text-center">Action</th> -->
                                            <th class="text-center">Paid</th>
                                            <th class="text-center">Payment</th>
                                            <th class="text-center">Notes</th>
                                            <th class="text-center">Transaction Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <!-- <th></th> -->
                                            <th>Paid</th>
                                            <th>Payment</th>
                                            <th>Notes</th>
                                            <th>Transaction Date</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="col-md-2"></div> -->
                </div>
            </div>
        </div>
    </div>
</div><!-- /row -->
@endsection
@section('script')
<script>
    $('#creditTable tfoot th').each(function() {
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
    var table = $("#creditTable").DataTable({
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
            "url": "/datatable/credits",
            "type": "POST",
            "data": {
                id: $('#id').val()
            }
        },
        language: {
            "decimal": ",",
            "thousands": "."
        },
        columns: [{
            data: 'pay',
            name: 'pay',
            className: 'text-right'
        }, {
            data: 'payment',
            name: 'payment',
            className: 'text-center'
        }, {
            data: 'notes',
            name: 'notes'
        }, {
            data: 'created_at',
            name: 'created_at',
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
</script>
@endsection