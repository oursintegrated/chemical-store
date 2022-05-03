@extends('layouts.backend')
@section('title', 'Chemical Store | User')
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
                <a href="{{url('/configuration/user')}}">User</a>
            </li>
            <li class="active">
                <strong>Create</strong>
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
                    <span class="pficon pficon-user"></span>
                    User
                    <small>Create</small>
                </h1>
            </div>
            <div class="card-pf-body">
                <div class="row">
                    <form id="main_form" class="col-md-5">
                        {{ csrf_field() }}
                        <div class="form-group required">
                            <label for="fullname">Full Name <span style="color: red;">*</span></label>
                            <input type="text" class="form-control" id="full_name" name="full_name" placeholder="Full Name" autocomplete="off">
                        </div>
                        <div class="form-group required">
                            <label for="fullname">Username <span style="color: red;">*</span></label>
                            <input type="text" class="form-control" id="username" name="username" placeholder="Username" autocomplete="off">
                        </div>
                        <div class="form-group required">
                            <label for="email">Email <span style="color: red;">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="example@smile.com" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label for="role">Role <span style="color: red;">*</span></label>
                            <select class="form-control" id="role_id" name="role_id">
                                @foreach($roles as $role)
                                <option value="{{$role->id}}">{{$role->display_name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">

                        </div>
                        <div class="form-group required">
                            <label for="password">Password <span style="color: red;">*</span></label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                        </div>
                        <a href="{{url('/configuration/user')}}" class="btn btn-default btn">
                            <li class="fa fa-arrow-circle-left"></li> &nbsp; Back
                        </a>
                        <button type="button" id="btnSave" onclick="create_user()" class="btn btn-success btn btn-ml">
                            <li class="fa fa-check"></li> &nbsp; Save
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script>
    $(document).ready(function() {
        $('#role_id').select2();

        $('#role_id').change(function() {
            var role = $('#role_id').find(":selected").text();
        }).trigger('change');
    });

    var create_user = function() {
        $("#btnSave").prop('disabled', 'true');

        axios.post("/configuration/user/create", $('#main_form').serialize())
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