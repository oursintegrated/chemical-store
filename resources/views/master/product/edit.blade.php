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
                <strong>Edit</strong>
            </li>
        </ol>
    </div>
</div><!-- /row -->

<!-- Toolbar -->
<div class="row row-cards-pf">
    <div class="col-sm-12">
        <div class="card-pf card-pf-accented card-pf-view">
            <div class="card-pf-heading">
                <h1>
                    <span class="pficon pficon-registry"></span>
                    Product
                    <small>Edit</small>
                </h1>
            </div>

            <div class="card-pf-body">
                <div class="row">
                    <form id="main_form" autocomplete="off">
                        <div class="col-md-5">
                            {{ csrf_field() }}
                            <input type="hidden" name="product_id" id="product_id" value="{{ $product->id }}">
                            <div class="form-group required">
                                <label class="control-label">Product Name <span style="color: red;">*</span></label>
                                <input type="text" required name="name" class="form-control" placeholder="Product Name" value="{{ $product->product_name }}" autocomplete="off">
                            </div>

                            <div class="form-group required">
                                <label class="control-label">Min Stock</label>
                                <input type="number" step="0.1" required name="min_stock" class="form-control" value="{{ number_format($product->min_stock, 2, '.', '') }}" autocomplete="off" placeholder="0" min="0" @if($product->type == 'delivery') readonly @endif>
                            </div>

                            <div class="form-group required">
                                <label class="control-label">Description <span style="color: red;">*</span></label>
                                <textarea class="form-control" name="description" rows="3">{{ $product->description }}</textarea>
                            </div>

                            <a role="button" href="{{ url('/data-master/product') }}" class="btn btn-default btn"><i class="fa fa-arrow-circle-left fa-fw"></i> Back</a>

                            <button type="button" id="btnSave" class="btn btn-success btn btn-ml" style="margin-left: 10px"><i class="fa fa-check fa-fw"></i> Save</button>
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
    $(function() {
        $("#btnSave").on('click', function() {
            var recordID = $("input[name=product_id]").val();

            $("#btnSave").prop('disabled', 'true');

            axios.put("/data-master/product/" + recordID + "/edit", $('#main_form').serialize())
                .then(function(response) {
                    if (response.data.status == 1) {
                        swal({
                            title: "Good!",
                            text: response.data.message,
                            type: "success",
                            timer: 1000,
                            confirmButtonText: 'Ok'
                        }).then(function() {
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