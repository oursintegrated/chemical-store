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
                <strong>List Sales</strong>
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
                    <small>List</small>
                </h1>
            </div>
            <div class="card-pf-body">
                <div class="row">
                    <div class="col-md-12">
                        <a href="{{url('/sales/create')}}" class="btn btn-default btn">
                            <li class="fa fa-plus-square"></li> &nbsp; Create Sales
                        </a>
                    </div>
                </div>
                <div class="row">
                    <p>&nbsp;</p>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <!-- Table HTML -->
                            <table id="customerTable" class="table table-striped table-bordered table-hover" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>Actions</th>
                                        <th>Name</th>
                                        <th>Telephone</th>
                                        <th>Created At</th>
                                        <th>Updated At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th></th>
                                        <th>Name</th>
                                        <th>Telephone</th>
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

        $('#customerTable tfoot th').each(function() {
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
        var table = $("#customerTable").DataTable({
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
                [1, 'asc']
            ],
            dom: '<"top"l>rt<"bottom"ip><"clear">',
            ajax: {
                "url": "/datatable/customers",
                "type": "POST"
            },
            language: {
                "decimal": ",",
                "thousands": "."
            },
            columns: [{
                data: 'action',
                name: 'action',
                className: "table-view-pf-actions",
                orderable: false,
                searchable: false
            }, {
                data: 'name',
                name: 'name'
            }, {
                data: 'telp',
                name: 'telp'
            }, {
                data: 'created_at',
                name: 'created_at'
            }, {
                data: 'updated_at',
                name: 'updated_at'
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
    });

    var deleteCustomer = function(me) {
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover this record!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            showLoaderOnConfirm: true,
            preConfirm: () => {
                _deleteCustomer(me)
            }
        })
    };

    var _deleteCustomer = function(me) {
        var recordID = me.data('record-id');

        axios.delete("/data-master/customer/" + recordID + "/delete")
            .then(function(response) {
                if (response.data.status == 1) {
                    swal({
                        title: "Good!",
                        text: response.data.message,
                        type: "success",
                        confirmButtonText: 'Ok'
                    });

                    $('#customerTable').DataTable().ajax.reload(null, false);
                } else {
                    swal({
                        title: "Oops!",
                        text: response.data.message,
                        type: "error",
                        confirmButtonText: 'Ok'
                    });
                }
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
            });
    };
</script>
@endsection