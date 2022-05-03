@extends('layouts.backend')
@section('title', 'Import CSV')
@section('content')
    <div class="row row-cards-pf">
        <div class="row-cards-pf card-pf">
            <ol class="breadcrumb">
                <li>
                    <span class="pficon pficon-home"></span>
                    <a href="{{url('home')}}">Dashboard</a>
                </li>
                <li>
                    <span class="fa fa-file"></span>
                    <a href="{{url('importcsv')}}">Import CSV</a>
                </li>
                <li class="active">
                    <strong>Import CSV Example</strong>
                </li>
            </ol>
        </div>
    </div>

    <div class="row row-cards-pf">
        <div class="col-xs-12">
            <div class="card-pf card-pf-accented card-pf-view">
                <div class="card-pf-heading">
                    <h1>
                        <span class="fa fa-file"></span>
                        Import CSV
                        <small>Example</small>
                    </h1>
                </div>
                <div class="card-pf-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-5 form-group">
                                    <input type="file" class="form-control" id="store_csv" name="store_csv" placeholder="Choose CSV file">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <table id="table-label" class="table table-striped table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th>Store Code</th>
                                    <th>Initial</th>
                                    <th>Store Name</th>
                                    <th>Created At</th>
                                    <th>Updated At</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="overlay text-center" id="overlayForm">
                        <i class="fa fa-refresh fa-spin fa-3x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        
        $('#overlayForm').hide();

        $(document).ready(function () {

            $("#store_csv").change(function() {
                /* ketika ada perubahan pada file csv yang di upload maka data tabel akan berubah*/ 
                $("#overlayForm").show();
                var formDataCSV = new FormData();
                var file_csv = $("#store_csv");
                formDataCSV.append('store_csv', file_csv[0].files[0]);

                axios.post('/import-store-csv', formDataCSV, {headers: {'Content-Type': 'multipart/form-data'}})
                    .then(function (response) {
                        if (response.data.length > 0) {
                            var rows = "";
                            var numOfRows = 1;

                            for ($i = 0; $i < response.data.length; $i++) {
                                rows += "<tr>" +
                                    "<td>" + response.data[$i]['store_code'] + "<input type='hidden' name='store_code[]' value='" + response.data[$i]['store_code'] + "'></td>" +
                                    "<td>" + response.data[$i]['initial'] + "<input type='hidden' name='initial[]' value='" + response.data[$i]['initial'] + "'></td>" +
                                    "<td>" + response.data[$i]['store_name'] + "<input type='hidden' name='store_name[]' value='" + response.data[$i]['store_name'] + "'></td></td>" +
                                    "<td>" + response.data[$i]['created_at'] + "<input type='hidden' name='created_at[]' value='" + response.data[$i]['created_at'] + "'></td>" +
                                    "<td>" + response.data[$i]['updated_at'] + "<input type='hidden' name='updated_at[]' value='" + response.data[$i]['updated_at'] + "'></td></td>" +
                                    "<td> <button type='button' class='btn btn-danger' onclick='deleteRow(this)' title='Delete'><i class='fa fa-minus fa-fw'></i></button> </td>" +
                                "</tr>";

                                numOfRows += 1;
                            }
                            
                            $(rows).appendTo("#table-label tbody");
                            $("#overlayForm").hide();
                        } else {
                            $("#overlayForm").hide();
                            swal({
                                title: "Oops!",
                                text: "Not correct csv file or format and/or data not exist.",
                                type: "warning",
                                confirmButtonColor: "#DD6B55",
                                confirmButtonText: "OK",
                                closeOnConfirm: true
                            });
                        }
                    })
                    .catch(function (error) {
                        $("#overlayForm").hide();

                        swal({
                            title: "Oops!",
                            text: "Something went wrong",
                            type: "warning",
                            confirmButtonColor: "#DD6B55",
                            confirmButtonText: "OK",
                            closeOnConfirm: true
                        });
                    });
            });

            function deleteRow(btn) {
                numOfRows -= 1;
                var row = btn.parentNode.parentNode;
                row.parentNode.removeChild(row);
            }
        });
    </script>
@endsection