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
                    <span class="pficon pficon-registry"></span>
                    Product
                    <small>Create</small>
                </h1>
            </div>
            <div class="card-pf-body">
                <div class="row">
                    <form id="main_form">
                        {{ csrf_field() }}
                        <div class="col-md-5">
                            <div class="form-group required">
                                <label class="control-label">Product Name <span style="color: red;">*</span></label>
                                <!-- <input type="text" required name="name" class="form-control" placeholder="Product Name" value="{{ old('name') }}" autocomplete="off"> -->
                                <select id="name" name="name" class="form-control">
                                    <option></option>
                                    @if(isset($products))
                                    @foreach($products as $product)
                                    <option value="{{ $product->product_name }}"> {{ $product->product_name }}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>

                            <div class="form-group required">
                                <label class="control-label">Stock in Kg <span style="color: red;">*</span></label>
                                <input type="number" step=".1" required name="stock" class="form-control" placeholder="0" value="{{ old('stock') }}" autocomplete="off">
                                <small class="form-text text-muted">use . for decimal number</small>
                            </div>

                            <div class="form-group required">
                                <label class="control-label">Description <span style="color: red;">*</span></label>
                                <textarea class="form-control" name="description" rows="3"></textarea>
                            </div>

                            <a role="button" href="{{ url('/data-master/product') }}" class="btn btn-default btn"><i class="fa fa-arrow-circle-left fa-fw"></i> Back</a>

                            <button type="button" id="btnSave" class="btn btn-success btn btn-ml" onclick="create_product()" style="margin-left: 10px"><i class="fa fa-check fa-fw"></i> Save</button>
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
    $('#name').select2({
        tags: true,
        allowClear: true,
        placeholder: "Input Product",
    });

    var create_product = function() {
        $("#btnSave").prop('disabled', 'true');

        axios.post("/data-master/product/create", $('#main_form').serialize())
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
    };
</script>

@endsection