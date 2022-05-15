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
                <strong>History Stock</strong>
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
                    <small>History</small>
                </h1>
            </div>
            <div class="card-pf-body">
                <br />
                <div class="row">
                    <form id="main_form" class="col-md-3" autocomplete="off">
                        {{ csrf_field() }}
                        <div class="form-group required">
                            <label for="fullname">Date <span style="color: red;">*</span></label>
                            <input id="date" placeholder="Input date" class="form-control datepicker" name="datetimerange" value="">
                        </div>
                        <select class="form-control select2" id="product" name="product" data-placeholder="Choose product">
                            <option value="All">ALL PRODUCT</option>
                            @foreach ($products as $product)
                            <option value="{{ $product->id }}">{{ $product->product_name }}</option>
                            @endforeach
                        </select>

                        <div class="form-group">
                            <br />
                            <button type="button" class="btn btn-default" id="searchBtn"> Search </button>
                        </div>
                    </form>

                    <div class="col-md-12"><br /></div>

                    <div class="col-md-12">
                        <div class="table-responsive">
                            <!-- Table HTML -->
                            <table id="historyTable" class="table table-striped table-bordered" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center">Product Name</th>
                                        <th class="text-center">Description</th>
                                        <th class="text-center">Stock Start</th>
                                        <th class="text-center">End of Stock</th>
                                        <th class="text-center">Total</th>
                                        <th class="text-center">Updated At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th class="text-center">Product Name</th>
                                        <th class="text-center">Description</th>
                                        <th class="text-center">Stock Start</th>
                                        <th class="text-center">End of Stock</th>
                                        <th class="text-center">Total</th>
                                        <th class="text-center">Updated At</th>
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
        $('.select2').select2({
            placeholder: "Select a product"
        });

        var startDate = '';
        var endDate = '';
        $('#date').daterangepicker({
            autoApply: true,
            autoUpdateInput: false,
        }, function(start, end, label) {
            $('#date').val(start.format('MM/DD/YYYY') + ' - ' + end.format('MM/DD/YYYY'));

            startDate = start.format('YYYY-MM-DD');
            endDate = end.format('YYYY-MM-DD');
        });

        $('#searchBtn').on('click', function() {
            if ($('#date').val() == '') {
                startDate = '';
                endDate = '';
            }

            table.ajax.reload();
        });

        $('#historyTable tfoot th').each(function() {
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
        var table = $("#historyTable").DataTable({
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
                [4, 'asc']
            ],
            dom: '<"top"l>rt<"bottom"ip><"clear">',
            ajax: {
                "url": "/datatable/stock/history-admin",
                "type": "POST",
                "data": function(d) {
                    d.start_date = startDate,
                        d.end_date = endDate,
                        d.id = $('#product').val()
                }
            },
            language: {
                "decimal": ",",
                "thousands": "."
            },
            columns: [{
                data: 'product_name',
                name: 'product_name'
            }, {
                data: 'description',
                name: 'description',
            }, {
                data: 'from_qty',
                name: 'from_qty',
                className: 'text-right'
            }, {
                data: 'to_qty',
                name: 'to_qty',
                className: 'text-right'
            }, {
                data: 'total',
                name: 'total',
                className: 'text-right'
            }, {
                data: 'updated_at',
                name: 'updated_at',
                className: 'text-center'
            }, ]
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
</script>
@endsection