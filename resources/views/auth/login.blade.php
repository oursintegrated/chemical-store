@extends('layouts.authenticate')
@section('title', 'Login')
@section('content')
<style>
    .login-box-body {
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
         !important;
        background: rgba(255, 255, 255, 0.8);
    }
</style>
<div class="row">
    <div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2 col-lg-8 col-lg-offset-2">
        <div class="card-pf login-box-body">
            <header class="login-pf-header text-center">
                <div class="text-center" style="margin-top: 20px; margin-bottom: 20px">
                    <img width="50" src="{{ asset('images/logo.png') }}" alt=" logo" />
                </div>
                <h4 style="color: gray"><i><strong>Chemical Store</strong></i></h4>
                <h2><strong>Log in</strong> to Your Account</h2>
            </header>
            <form role="form">
                {{ csrf_field() }}
                <div class="form-group">
                    <label class="sr-only" for="email">Username</label>
                    <input type="text" class="form-control  input-lg" id="username" name="username" placeholder="Username">
                </div>
                <div class="form-group">
                    <label class="sr-only" for="password">Password
                    </label>
                    <input type="password" class="form-control input-lg" id="password" name="password" placeholder="Password" onkeyup="login(event)">
                </div>
                <div class="form-group login-pf-settings">
                    <label class="checkbox-label">
                        <input type="checkbox" id="remember" name="remember"> Remember Me
                    </label>
                </div>
                <!-- <div class="form-group">
                    <a href="/forgot/your/password">Forgot Password?</a>
                </div> -->
                <button type="button" class="btn btn-primary btn-block btn-lg" id="btn_login" onclick="_login($(this));">Log In</button>
            </form>
        </div><!-- card -->

        <footer class="login-pf-page-footer">
            <ul class="login-pf-page-footer-links list-unstyled">
                <li>
                    <a class="login-pf-page-footer-link">
                        <strong>
                            <span style="color: black">
                                Copyright &copy; {{ config('example.year_created') }}{{(date('Y') > config('example.year_created') ? ' - '.date('Y') : '')}}.
                            </span>
                            <a href="#"> Chemical Store</a>
                        </strong>
                    </a>
                </li>
            </ul>
        </footer>
    </div><!-- col -->
</div><!-- row -->
@endsection
@section('script')
<script>
    function login(e) {
        if (e.keyCode == 13) {
            _login($("#btn_login"))
        }
    }

    var _login = function(me) {
        me.prop('disabled', true);
        axios.post('/login', {
                username: $("#username").val(),
                password: $("#password").val(),
                remember: $("#remember").val(),
                _token: $("#_token").val()
            })
            .then(function(response) {
                // success = true;
                // alert(response);
                if (response.data.status === 1) {
                    swal({
                        title: "Good!",
                        text: "Logging you in..",
                        timer: 1000,
                        type: "success",
                        confirmButtonText: 'Ok'
                    }).then(function() {
                        window.location.replace(response.data.intended_url)
                    });

                } else {
                    swal({
                        title: "Oops!",
                        text: "Invalid username and/or password.",
                        type: "error"
                    }).then(function() {
                        location.reload();
                    });

                    /* Clear Password */
                    $("#password").val('');
                }
            })
            .then(function(response) {
                /* Enable button and hide overlay */
                me.prop('disabled', false);
            })
            .catch(function(error) {
                swal({
                    title: "Oops!",
                    text: "Something went wrong.",
                    timer: 2000,
                    type: "error"
                });
                /* Enable button  */
                me.prop('disabled', false);
            });
    };
</script>
@endsection