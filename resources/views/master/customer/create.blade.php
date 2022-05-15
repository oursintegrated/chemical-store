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
                <span class="pficon pficon-user"></span>
                <a href="{{url('/data-master/customer')}}">Customer</a>
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
                    <span class="pficon pficon-user"></span>
                    Customer
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
                                    <option value="{{ $customer->name }}"> {{ $customer->name }}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>

                            <div class="form-group required">
                                <label class="control-label">Code</label>
                                <div class="form-group">
                                    <input type="text" name="code" class="form-control">
                                </div>
                            </div>

                            <div class="form-group required">
                                <label class="control-label">Telephone</label>
                                <div id="phoneFormRow">
                                    <div class="form-group">
                                        <input type="text" name="telephone[]" class="form-control" placeholder="Input phone number" maxlength="15" onkeypress="return /[0-9]/i.test(event.key)">
                                    </div>
                                </div>
                                <div id="newPhoneRow"></div>
                                <button id="addPhoneRow" type="button" class="btn btn-default"> More &nbsp; <i class="fa fa-phone"></i></button>
                            </div>

                            <div class="form-group required">
                                <label class="control-label">Kontrabon <span style="color: red;">*</span></label>
                                <div class="form-group">
                                    <input type="number" name="kontrabon" class="form-control" placeholder="0" min="0" onkeypress="return /[0-9]/i.test(event.key)">
                                </div>
                            </div>

                            <div class="form-group required">
                                <label class="control-label">Address <span style="color: red;">*</span></label>
                                <div id="addressFormRow">
                                    <div class="form-group">
                                        <textarea name="address[]" class="form-control" rows="2"></textarea>
                                    </div>
                                </div>
                                <div id="newAddressRow"></div>
                                <button id="addAddressRow" type="button" class="btn btn-default"> More &nbsp; <i class="fa fa-address-card"></i></button>
                            </div>

                            <a role="button" href="{{ url('/data-master/customer') }}" class="btn btn-default btn"><i class="fa fa-arrow-circle-left fa-fw"></i> Back</a>

                            <button type="button" id="btnSave" class="btn btn-success btn btn-ml" onclick="create_customer()" style="margin-left: 10px"><i class="fa fa-check fa-fw"></i> Save</button>
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
    $("#addPhoneRow").click(function() {
        var html = '';
        html += '<div id="phoneFormRow">';
        html += '<div class="form-group">';
        html += '<div class="input-group">';
        html += '<input type="text" name="telephone[]" class="form-control" placeholder="Input phone number"  onkeypress="return /[0-9]/i.test(event.key)" maxlength="15">';
        html += '<span class="input-group-btn">';
        html += '<button id="removePhoneRow" class="btn btn-danger" type="button"> <i class="fa fa-trash"></i> </button>';
        html += '</span>';
        html += '</div>';
        html += '</div>';
        html += '</div>';

        $('#newPhoneRow').append(html);
    });

    // remove row
    $(document).on('click', '#removePhoneRow', function() {
        $(this).closest('#phoneFormRow').remove();
    });

    $("#addAddressRow").click(function() {
        var html = '';
        html += '<div id="addressFormRow">';
        html += '<div class="form-group">';
        html += '<div class="input-group">';
        html += '<textarea name="address[]" class="form-control" rows="2"></textarea>';
        html += '<span class="input-group-btn">';
        html += '<button id="removeAddressRow" class="btn btn-danger" type="button"> <i class="fa fa-trash"></i> </button>';
        html += '</span>';
        html += '</div>';
        html += '</div>';
        html += '</div>';

        $('#newAddressRow').append(html);
    });

    // remove row
    $(document).on('click', '#removeAddressRow', function() {
        $(this).closest('#addressFormRow').remove();
    });

    $('#name').select2({
        tags: true,
        allowClear: true,
        placeholder: "Input Customer Name",
    });

    var create_customer = function() {
        $("#btnSave").prop('disabled', 'true');

        axios.post("/data-master/customer/create", $('#main_form').serialize())
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