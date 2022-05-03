@extends('layouts.authenticate')
@section('title', 'Forgot Password')
@section('content')
    <style>
        .login-box-body {
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19); !important;
            background: rgba(255, 255, 255, 0.8);
        }
    </style>
    <div class="row">
        <div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2 col-lg-8 col-lg-offset-2">
            <div class="card-pf login-box-body">
                <header class="login-pf-header text-center">
                    <h2><strong>Input</strong> Your Email</h2>
                </header>
                <form role="form">
                    {{ csrf_field() }}
                    <div id="email_div" class="form-group">
                        <label class="sr-only" for="email">Your Email</label>
                        <input type="text" class="form-control  input-lg" id="email" name="email" placeholder="Your Email">
                    </div>
                    <div id="verification_code_div" class="form-group">
                        <label class="sr-only"  for="verification-code">Verification Code
                        </label>
                        <input type="text" class="form-control input-lg" id="verification_code" name="verification_code" placeholder="Verification Code">
                    </div>
                    <div id="new_password_div" class="form-group">
                        <label class="sr-only"  for="password">New Password
                        </label>
                        <input type="password" class="form-control input-lg" id="new_password" name="new_password" placeholder="New Password">
                    </div>
                    <div id="confirm_new_password_div" class="form-group">
                        <label class="sr-only"  for="new-password">Confirm New Password
                        </label>
                        <input type="password" class="form-control input-lg" id="confirm_new_password" name="confirm_new_password" placeholder="Confirm New Password">
                    </div>
                    <button type="button" class="btn btn-primary btn-block btn-lg" id="check_email" onclick="checkEmail()">Check Email</button>
                    <button type="button" class="btn btn-primary btn-block btn-lg" id="verification_code_btn" onclick="verificationCode()">Verification Code</button>
                    <button type="button" class="btn btn-primary btn-block btn-lg" id="change_password" onclick="changePassword()">Change Password</button>
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
                                <a href="#">YOGYA Group</a>
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
        $(document).ready(function () {
            $("#verification_code_div, #new_password_div, #confirm_new_password_div, #verification_code_btn, #change_password").hide();
        });

        function checkEmail() {
            swal({
                title: "Are you sure?",
                text: "Make sure your email is valid and active.",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Ok",
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    _checkEmail()
                }
            });
        }

        var _checkEmail = function() {
            var email = $("input[name=email]").val();

            axios.post('/forgot/your/password/email', {
                email: email,
                _token: $("#_token").val()
            })
            .then(function(response) {
                if (response.data.status === 1) {
                    swal({
                        title: "Good!",
                        text: response.data.message,
                        timer: 1000,
                        type: "success",
                        confirmButtonText: 'Ok'
                    }).then(function () {
                        $("#verification_code_div").slideDown();
                        $("#check_email").slideUp();
                        $("#verification_code_btn").slideDown();
                        
                        $("#email").attr('disabled', true);
                    });

                } else {
                    swal({
                        title: "Oops!",
                        text: response.data.message,
                        type: "error"
                    }).then(function() {
                        $("input[name=email]").val('');
                    });
                }
            })
            .catch(function(error) {
                swal({
                    title: "Oops!",
                    text: "Something went wrong.",
                    timer: 2000,
                    type: "error"
                });
            });
        }

        function verificationCode(){
            var email = $("input[name=email]").val();
            var verification_code = $("input[name=verification_code]").val();

            axios.post('/forgot/your/password/verification/code', {
                email: email,
                verification_code: verification_code,
                _token: $("#_token").val()
            })
            .then(function(response) {
                if (response.data.status === 1) {
                    swal({
                        title: "Good!",
                        text: response.data.message,
                        timer: 1000,
                        type: "success",
                        confirmButtonText: 'Ok'
                    }).then(function () {
                        $("#new_password_div").slideDown();
                        $("#confirm_new_password_div").slideDown();
                        $("#verification_code_btn").slideUp();
                        $("#change_password").slideDown();
                        
                        $("#verification_code").attr('disabled', true);
                    });

                } else {
                    swal({
                        title: "Oops!",
                        text: response.data.message,
                        type: "error"
                    }).then(function() {
                        $("input[name=verification_code]").val('');
                    });
                }
            })
            .catch(function(error) {
                swal({
                    title: "Oops!",
                    text: "Something went wrong.",
                    timer: 2000,
                    type: "error"
                });
            });
        }

        function changePassword(){
            var email = $("input[name=email]").val();
            var new_password = $("input[name=new_password]").val();
            var confirm_new_password = $("input[name=confirm_new_password]").val();

            if(new_password == confirm_new_password){
                axios.post('/forgot/your/password/change', {
                    email: email,
                    new_password: new_password,
                    _token: $("#_token").val()
                })
                .then(function(response) {
                    if (response.data.status === 1) {
                        swal({
                            title: "Good!",
                            text: response.data.message,
                            timer: 1000,
                            type: "success",
                            confirmButtonText: 'Ok'
                        }).then(function () {
                            window.location.replace('/')
                        });

                    } else {
                        swal({
                            title: "Oops!",
                            text: response.data.message,
                            type: "error"
                        }).then(function() {

                        });
                    }
                })
                .catch(function(error) {
                    swal({
                        title: "Oops!",
                        text: "Something went wrong.",
                        timer: 2000,
                        type: "error"
                    });
                });
            }
            else{
                swal({
                    title: "Oops!",
                    text: 'Password and confirm password not same',
                    type: "error"
                });
            }
        }
    </script>
@endsection