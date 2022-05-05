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
                    <span class="pficon pficon-user"></span>
                    Customer
                    <small>Edit</small>
                </h1>
            </div>

            <div class="card-pf-body">
                <div class="row">
                    <form id="main_form" autocomplete="off">
                        <div class="col-md-5">
                            {{ csrf_field() }}
                            <input type="hidden" name="customer_id" id="customer_id" value="{{ $customer->id }}">
                            <div class="form-group required">
                                <label class="control-label">Name <span style="color: red;">*</span></label>
                                <input type="text" required name="name" class="form-control" placeholder="Customer Name" value="{{ $customer->name }}" autocomplete="off">
                            </div>

                            <div class="form-group required">
                                <label class="control-label">Telephone <span style="color: red;">*</span></label>
                                @if(isset($telephones))
                                @foreach($telephones as $telephone)
                                <div class="form-group">
                                    <div class="input-group">
                                        <input disabled type="text" value="{{ $telephone->phone }}" id="phone-{{ $telephone->id }}" class="form-control" placeholder="Input phone number" onkeypress="return /[0-9]/i.test(event.key)" maxlength="15">
                                        <span class="input-group-btn">
                                            <button type="button" class="btn btn-default" id="editP-{{ $telephone->id }}" onclick="toogleEditPhone( {{ $telephone->id }})">
                                                <i class="fa fa-pencil"></i>
                                            </button>
                                            <button type="button" class="btn btn-success" id="saveP-{{ $telephone->id }}" onclick="savePhone( {{ $telephone->id }})" disabled>
                                                <i class="fa fa-check"></i>
                                            </button>
                                            <button type="button" class="btn btn-danger" onclick="deletePhone({{ $telephone->id }} )">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </span>
                                    </div>
                                </div>
                                @endforeach
                                @endif

                                <div id="newPhoneRow"></div>
                                <button id="addPhoneRow" type="button" class="btn btn-default" data-toggle="modal" data-target="#myModalPhone"> More &nbsp; <i class="fa fa-phone"></i></button>
                            </div>

                            <div class="form-group required">
                                <label class="control-label">Kontrabon <span style="color: red;">*</span></label>
                                <div class="form-group">
                                    <input type="number" name="kontrabon" class="form-control" placeholder="0" min="0" onkeypress="return /[0-9]/i.test(event.key)" value="{{ $customer->kontrabon }}" @if($flag_kontrabon==0) disabled @endif>
                                </div>
                            </div>

                            <div class="form-group required">
                                <label class="control-label">Address <span style="color: red;"></span></label>
                                <div class="row">
                                    @if(isset($addresses))
                                    @foreach($addresses as $address)
                                    <div class="col-md-6">
                                        <div class="card" style="border: 1px solid #bbbbbb; border-radius: 10px; overflow: hidden;">
                                            <div class="card-header text-right" style="padding-right: 5px; padding-top: 5px">
                                                <button type="button" class="btn btn-default" id="editA-{{ $address->id }}" onclick="toogleEditAddress( {{ $address->id }})">
                                                    <i class="fa fa-pencil"></i>
                                                </button>
                                                <button type="button" class="btn btn-success" id="saveA-{{ $address->id }}" onclick="saveLoc( {{ $address->id }})" disabled>
                                                    <i class="fa fa-check"></i>
                                                </button>
                                                <button type="button" class="btn btn-danger" onclick="deleteLoc({{ $address->id }} )">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                            <div class="card-body">
                                                <blockquote style="padding: 5px 5px !important; margin: 0 0 3px !important">
                                                    <textarea id="loc-{{ $address->id }}" disabled class="form-control" rows="3">{{ $address->location }}</textarea>
                                                </blockquote>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                    @endif
                                    <div class="col-md-12" style="margin-top: 10px;">
                                        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#myModal">More <i class="fa fa-address-card"></i> </button>
                                    </div>
                                </div>

                            </div>

                            <a role="button" href="{{ url('/data-master/customer') }}" class="btn btn-default btn"><i class="fa fa-arrow-circle-left fa-fw"></i> Back</a>

                            <button type="button" id="btnSave" class="btn btn-success btn btn-ml" style="margin-left: 10px"><i class="fa fa-check fa-fw"></i> Save</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    <span class="pficon pficon-close"></span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Add Address</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" autocomplete="off">
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="textInput-modal-markup">Address</label>
                        <div class="col-sm-10">
                            <textarea id="newLoc" aria-label="new address" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="createNewLoc()">Save</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="myModalPhone" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    <span class="pficon pficon-close"></span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Add Phone Number</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" autocomplete="off">
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="textInput-modal-markup">Telephone</label>
                        <div class="col-sm-10">
                            <input id="newPhone" type="text" class="form-control" placeholder="Input new number" onkeypress="return /[0-9]/i.test(event.key)" maxlength="15">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="createNewPhone()">Save</button>
            </div>
        </div>
    </div>
</div>

@endsection
@section('script')
<!-- page script -->
<script type="text/javascript">
    function toogleEditPhone(me) {
        var x = $('#phone-' + me).prop('disabled');
        if (x == true) {
            $('#phone-' + me).prop('disabled', false);
            $('#saveP-' + me).prop('disabled', false);
            $('#editP-' + me).prop('disabled', true);
            $('#btnSave').prop('disabled', true);
        } else {
            $('#phone-' + me).prop('disabled', true);
            $('#saveP-' + me).prop('disabled', true);
            $('#editP-' + me).prop('disabled', false);
            $('#btnSave').prop('disabled', false);
        }
    }

    function toogleEditAddress(me) {
        var x = $('#loc-' + me).prop('disabled');
        if (x == true) {
            $('#loc-' + me).prop('disabled', false);
            $('#saveA-' + me).prop('disabled', false);
            $('#editA-' + me).prop('disabled', true);
            $('#btnSave').prop('disabled', true);
        } else {
            $('#loc-' + me).prop('disabled', true);
            $('#saveA-' + me).prop('disabled', true);
            $('#editA-' + me).prop('disabled', false);
            $('#btnSave').prop('disabled', false);
        }
    }

    function savePhone(me) {
        $('#phone-' + me).prop('disabled', true);
        $('#editP-' + me).prop('disabled', false);
        $('#saveP-' + me).prop('disabled', true);
        $('#btnSave').prop('disabled', false);

        var phone = $('#phone-' + me).val();

        axios.put("/data-master/telephone/" + me + "/edit", {
                'phone': phone
            })
            .then(function(response) {
                if (response.data.status == 1) {
                    swal({
                        title: "Good!",
                        text: response.data.message,
                        type: "success",
                        timer: 1000,
                        confirmButtonText: 'Ok'
                    })
                } else {
                    swal({
                        title: "Oops!",
                        text: response.data.message,
                        type: "error",
                        closeOnConfirm: false
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
    }

    function saveLoc(me) {
        $('#loc-' + me).prop('disabled', true);
        $('#editA-' + me).prop('disabled', false);
        $('#saveA-' + me).prop('disabled', true);
        $('#btnSave').prop('disabled', false);

        var loc = $('#loc-' + me).val();

        axios.put("/data-master/address/" + me + "/edit", {
                'loc': loc
            })
            .then(function(response) {
                if (response.data.status == 1) {
                    swal({
                        title: "Good!",
                        text: response.data.message,
                        type: "success",
                        timer: 1000,
                        confirmButtonText: 'Ok'
                    })
                } else {
                    swal({
                        title: "Oops!",
                        text: response.data.message,
                        type: "error",
                        closeOnConfirm: false
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
    }

    function deletePhone(me) {
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover this record!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            showLoaderOnConfirm: true,
            preConfirm: () => {
                _deletePhone(me)
            }
        })
    }

    var _deletePhone = function(recordID) {
        axios.delete("/data-master/telephone/" + recordID + "/delete")
            .then(function(response) {
                if (response.data.status == 1) {
                    swal({
                        title: "Good!",
                        text: response.data.message,
                        type: "success",
                        confirmButtonText: 'Ok'
                    });

                    location.reload();
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
            })
    };

    function deleteLoc(me) {
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover this record!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            showLoaderOnConfirm: true,
            preConfirm: () => {
                _deleteLoc(me)
            }
        })
    }

    var _deleteLoc = function(recordID) {
        axios.delete("/data-master/address/" + recordID + "/delete")
            .then(function(response) {
                if (response.data.status == 1) {
                    swal({
                        title: "Good!",
                        text: response.data.message,
                        type: "success",
                        confirmButtonText: 'Ok'
                    });

                    location.reload();
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
            })
    };

    var createNewPhone = function() {
        var recordID = $("input[name=customer_id]").val();
        var newPhone = $("#newPhone").val();

        axios.post("/data-master/telephone/create", {
                'customer_id': recordID,
                'newPhone': newPhone
            })
            .then(function(response) {
                if (response.data.status == 1) {
                    swal({
                        title: "Good!",
                        text: response.data.message,
                        type: "success",
                        timer: 1000,
                        confirmButtonText: 'Ok'
                    })
                    location.reload();
                } else {
                    swal({
                        title: "Oops!",
                        text: response.data.message,
                        type: "error",
                        closeOnConfirm: false
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

    var createNewLoc = function() {
        var recordID = $("input[name=customer_id]").val();
        var newLoc = $("#newLoc").val();

        axios.post("/data-master/address/create", {
                'customer_id': recordID,
                'newLoc': newLoc
            })
            .then(function(response) {
                if (response.data.status == 1) {
                    swal({
                        title: "Good!",
                        text: response.data.message,
                        type: "success",
                        timer: 1000,
                        confirmButtonText: 'Ok'
                    })
                    location.reload();
                } else {
                    swal({
                        title: "Oops!",
                        text: response.data.message,
                        type: "error",
                        closeOnConfirm: false
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

    $(function() {
        $("#btnSave").on('click', function() {
            var recordID = $("input[name=customer_id]").val();

            $("#btnSave").prop('disabled', 'true');

            axios.put("/data-master/customer/" + recordID + "/edit", $('#main_form').serialize())
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