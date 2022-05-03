@extends('layouts.backend')
@section('title', 'My Account')
@section('content')
<div class="row row-cards-pf">
    <div class="row-cards-pf card-pf">
        <ol class="breadcrumb">
            <li>
                <span class="pficon pficon-home"></span>
                <a href="{{url('dashboard')}}">Dashboard</a>
            </li>
            <li class="active">
                <strong>Reset Password</strong>
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
                    Reset Password
                </h1>
            </div>

            <div class="card-pf-body">
                <div class="row">
                    <div class="col-xs-12">
                        <form action="{{ url('/configuration/user/update-password') }}" method="post">
                            {{ csrf_field() }}
                            <input type="hidden" name="user_id" value="{{ $user->id }}">
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
                </div>
            </div>
        </div>
    </div>
</div>
@endsection