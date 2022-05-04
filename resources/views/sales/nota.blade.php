@extends('layouts.print_backend')
@section('title', 'Print Nota')
@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="card-pf card-pf-view">
            <div class="card-pf-body" style="margin: 0 0 0 !important; padding: 0 0 0 !important;">
                <div class="col-md-7">
                    <div class="panel panel-default">
                        <div class="panel-heading">Nota</div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-7"></div>
                                <div class="col-md-5 text-center">
                                    Bandung, {{ date('d M Y') }} <br />
                                    Kepada YTH
                                </div>
                            </div>
                            <br />
                            <div class="row">
                                <div class="col-md-8"></div>
                                <div class="col-md-4">
                                    Bapak/Ibu/Toko <br />
                                    <span id="customerName"></span> <span id="customerNumber"></span> <br />
                                    <span id="customerAddress"></span>
                                </div>
                            </div>
                            <br />
                            <table class="table table-responsive table-bordered" id="notaTable">
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
                                    <br />
                                    <br />
                                </div>
                                <div class="col-md-6">
                                    <p class="text-center">Hormat Kami</p>
                                    <br />
                                    <br />
                                    <br />
                                    <br />
                                    <br />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 text-right">
                                    <button id="printNota" class="btn btn-default" type="button">
                                        Print Nota
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script type="text/javascript">
</script>
@endsection