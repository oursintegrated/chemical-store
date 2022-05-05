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
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">Nota @if(isset($orderHeader)) : {{ $orderHeader->sales_code }} @endif</div>
                            <div class="panel-body">
                                <div class="html-content" id="html-content">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <span>@if(isset($orderHeader)) @if($orderHeader->type == 'tunai') T @else K{{ $orderHeader->due_date }} @endif @endif</span>
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
                                                <th class="font-weight-bold text-center table-bordered"><b>No</b></th>
                                                <th class="text-center table-bordered"><b>Nama Barang</b></th>
                                                <th class="text-center table-bordered"><b>Qty</b></th>
                                                <th class="text-center table-bordered"><b>Harga Satuan</b></th>
                                                <th class="text-center table-bordered"><b>Jumlah (Rp)</b></th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @if(isset($orderDetails))
                                            @for($i=0; $i<count($orderDetails); $i++) <tr>
                                                <td class="text-center">{{ $i+1 }}</td>
                                                <td class="text-center">{{ $orderDetails[$i]->product_name }}</td>
                                                <td class="text-center">{{ $orderDetails[$i]->qty }}</td>
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
                                            <th class="text-center" style="border: none;"><input type="text" readonly id="total" class="form-control text-right" value="{{ number_format($orderHeader->total, 0, ',' , '.') }}"></th>
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
                </div>
            </div>
        </div>
    </div>
</div><!-- /row -->
@endsection
@section('script')
<script>
</script>
@endsection