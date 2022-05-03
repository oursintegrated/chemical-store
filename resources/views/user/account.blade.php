@extends('layouts.backend')
@section('title', 'Chemical Store | My Account')
@section('content')
<div class="row row-cards-pf">
    <div class="row-cards-pf card-pf">
        <ol class="breadcrumb">
            <li>
                <span class="pficon pficon-home"></span>
                <a href="{{url('dashboard')}}">Dashboard</a>
            </li>
            <li class="active">
                <strong>My Account</strong>
            </li>
        </ol>
    </div>
</div><!-- /row -->

<!-- Toolbar -->
<div class="row row-cards-pf">
    <div class="col-sm-12">
        <div class="card-pf card-pf-view card-pf-accented">
            <div class="card-pf-heading">
                <h1>
                    <span class="pficon pficon-user"></span>
                    My Account
                </h1>
            </div>

            <div class="card-pf-body">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box">
                            <!-- /.box-header -->
                            <div class="box-body">
                                <div class="nav-tabs-custom">
                                    <ul class="nav nav-tabs">
                                        <li class="active"><a aria-expanded="true" href="#profile" data-toggle="tab">Profile</a></li>
                                        <li class=""><a aria-expanded="false" href="#password" data-toggle="tab">Password</a></li>
                                    </ul>
                                    <div class="tab-content">
                                        <div class="tab-pane active" id="profile">
                                            <br>
                                            <form action="{{ url('user/update-profile') }}" method="post">
                                                <?php echo csrf_field(); ?>

                                                <div class="form-group">
                                                    <label>Full Name</label>
                                                    <input class="form-control" autofocus name="full_name" placeholder="Enter full name" type="text" value="{{ Auth::user()->full_name }}">
                                                </div>
                                                <div class="form-group">
                                                    <label>Username</label>
                                                    <div class="form-control-static">
                                                        {{ Auth::user()->username }}
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label>Email</label>
                                                    <input class="form-control" name="email" placeholder="Enter email" type="email" value="{{ Auth::user()->email }}">
                                                </div>
                                                <div class="form-group">
                                                    <input type="submit" value="Save" class="btn btn-success btn">
                                                </div>

                                            </form>

                                        </div>
                                        <!-- /.tab-pane -->
                                        <div class="tab-pane" id="password">
                                            <br>
                                            <form action="{{ url('user/update-password') }}" method="post">
                                                <?php echo csrf_field(); ?>

                                                <input type="hidden" name="user_id" value="{{ $user->id }}">
                                                <div class="form-group">
                                                    <label>Current Password</label>
                                                    <input class="form-control" name="current_password" placeholder="Enter current password" type="password">
                                                </div>
                                                <div class="form-group">
                                                    <label>New Password</label>
                                                    <input class="form-control" name="new_password" placeholder="Enter new password" type="password">
                                                </div>
                                                <div class="form-group">
                                                    <label>Confirm New Password</label>
                                                    <input class="form-control" name="new_password_confirmation" placeholder="Confirm new password" type="password">
                                                </div>
                                                <div class="form-group">
                                                    <input type="submit" value="Save" class="btn btn-success btn">
                                                </div>

                                            </form>

                                        </div>
                                        <!-- /.tab-pane -->
                                    </div>
                                    <!-- /.tab-content -->
                                </div>
                            </div>
                            <!-- /.box-body -->
                        </div>
                        <!-- /.box -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection