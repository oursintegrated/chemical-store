@extends('layouts.backend')
@section('title', 'Chemical Store | Role')
@section('content')
<div class="row row-cards-pf">
    <div class="row-cards-pf card-pf">
        <ol class="breadcrumb">
            <li>
                <span class="pficon pficon-home"></span>
                <a href="{{url('dashboard')}}">Dashboard</a>
            </li>
            <li>
                <span class="pficon pficon-users"></span>
                <a href="{{url('/configuration/role')}}">Role</a>
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
                    <span class="pficon pficon-users"></span>
                    Role
                    <small>Edit</small>
                </h1>
            </div>

            <div class="card-pf-body">
                <div class="row">
                    <form id="main_form">
                        <div class="col-md-5">
                            {{ csrf_field() }}
                            <input type="hidden" name="role_id" value="{{ $role->id }}">
                            <input type="hidden" name="menu_id" id="menu_id" value="">
                            <div class="form-group required">
                                <label class="control-label">Name <span style="color: red;">*</span></label>
                                <input type="text" required name="name" class="form-control" placeholder="Name" value="{{ $role->name }}" autocomplete="off">
                            </div>
                            <div class="form-group required">
                                <label class="control-label">Display Name <span style="color: red;">*</span></label>
                                <input type="text" required name="display_name" class="form-control" placeholder="Display Name" value="{{ $role->display_name }}" autocomplete="off">
                            </div>
                            <div class="form-group required">
                                <label class="control-label">Description <span style="color: red;">*</span></label>
                                <input type="text" required name="description" class="form-control" placeholder="Description" value="{{ $role->description }}" autocomplete="off">
                            </div>

                            <a role="button" href="{{ url('/configuration/role') }}" class="btn btn-default btn"><i class="fa fa-arrow-circle-left fa-fw"></i> Back</a>

                            <button type="button" id="btnSave" class="btn btn-success btn btn-ml" style="margin-left: 10px"><i class="fa fa-check fa-fw"></i> Save</button>
                        </div>
                        <div class="col-md-5">
                            <label class="control-label">Select Menu <span style="color: red;">*</span></label>
                            <div id="menuTree">
                            </div>
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
            var recordID = $("input[name=role_id]").val();

            var checked_ids = [];
            var selectedNodes = $('#menuTree').jstree("get_selected", true);
            $.each(selectedNodes, function() {
                checked_ids.push(this.id);
            });

            // $("#menuTree").find(".jstree-undetermined").each(function(i, element) {
            //     var nodeId = $(element).closest('.jstree-node').attr("id");
            //     checked_ids.push( nodeId );
            // });

            $('#menu_id').val(checked_ids);

            $("#btnSave").prop('disabled', 'true');

            axios.put("/configuration/role/" + recordID + "/edit", $('#main_form').serialize())
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

        // ===================== MENU TREE
        $("#btnSave").prop('disabled', 'true');

        var recordID = $("input[name=role_id]").val();
        $('#menuTree').jstree({
                'core': {
                    'data': {
                        'url': "/jstree/menu",
                        'data': function(node) {
                            return {
                                'id': node.id,
                                'type': 'edit',
                                'role_id': recordID
                            };
                        }
                    },
                },
                "types": {
                    "default": {
                        "icon": "fa fa-folder fa-fw"
                    },
                    'f-open': {
                        'icon': 'fa fa-folder-open fa-fw'
                    },
                    'f-closed': {
                        'icon': 'fa fa-folder fa-fw'
                    }
                },
                'plugins': [
                    'checkbox',
                    'types'
                ],
                'checkbox': {
                    three_state: false, // to avoid that fact that checking a node also check others
                    whole_node: true, // to avoid checking the box just clicking the node
                    tie_selection: true, // for checking without selecting and selecting without checking
                },
            })

            .bind("ready.jstree", function(e, data) {
                $("#btnSave").removeAttr('disabled');
            })

        $("#menuTree").on('open_node.jstree', function(event, data) {
            data.instance.set_type(data.node, 'f-open');
        });

        $("#menuTree").on('close_node.jstree', function(event, data) {
            data.instance.set_type(data.node, 'f-closed');
        });
    });
</script>
@endsection