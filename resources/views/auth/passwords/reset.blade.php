@extends('layouts.login')
@section('title', 'Reset Password')
@section('content')
{{ Auth::logout() }}

@php
$action = request() -> get('action');
$action_text = [
    'register' => 'Create Account',
    'reset' => 'Reset Password'
][$action];
@endphp

<div class="bg-primary login-page">

    <div class="container-1100 login-container">

        <div class="row">

            <div class="col-12 col-sm-7 mx-auto">

                <div class="d-flex justify-content-around align-items-center login-div w-100">

                    <div class="w-100">

                        <div class="d-flex justify-content-around my-4 animate__animated animate__zoomIn">
                            <img src="/images/logo/logos.png" class="login-logo">
                        </div>

                        <form id="reset_password_form" class="w-100">

                            <input type="hidden" name="token" value="{{ $token }}">

                            <div class="card w-100 shadow animate__animated animate__zoomIn">

                                <div class="card-header bg-primary text-white shadow font-12 mb-n2">{{ $action_text }}</div>

                                <div class="card-body text-primary">

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="alert alert-danger hidden font-9" id="reset_error_div">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">

                                        <div class="col-12 col-md-7">

                                            <div class="pt-2 reset-inputs">

                                                <input type="text" class="custom-form-element form-large form-input" id="email" name="email" value="{{ $email ?? old('email') }}" data-label="Email Address" readonly autocomplete="email">

                                                <input type="password" class="custom-form-element form-large form-input required" id="password" name="password" data-label="Password" required autocomplete="new-password">

                                                <input type="password" class="custom-form-element form-input form-large required" id="password_confirmation" name="password_confirmation" data-label="Confirm Password" required autocomplete="new-password">


                                            </div>

                                        </div>

                                        <div class="col-12 col-md-5">
                                            <div class="d-flex justify-content-start align-items-center font-9 h-100">
                                                <div>
                                                    <strong>Password Requirements:</strong><br>
                                                    <i class="fal fa-arrow-right fa-xs mr-1"></i> One uppercase letter<br>
                                                    <i class="fal fa-arrow-right fa-xs mr-1"></i> One lower case letter<br>
                                                    <i class="fal fa-arrow-right fa-xs mr-1"></i> One numeric value<br>
                                                    <i class="fal fa-arrow-right fa-xs mr-1"></i> One special character<br>
                                                    <i class="fal fa-arrow-right fa-xs mr-1"></i> Must be at least 8 characters
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                </div>

                                <div class="card-footer bg-white">
                                    <div class="d-flex justify-content-around">
                                        <a href="javascript:void(0)" class="btn btn-primary" id="reset_password_button">{{ $action_text }} <i class="fal fa-check ml-2"></i></a>
                                    </div>

                                    <div class="mt-3 text-white w-100 text-center"><a href="/login">Return to Login</div>

                                </div>

                            </div>

                        </form>

                        <div class="h5 mt-5 text-white w-100 text-center animate__animated animate__zoomIn">Taylor Properties Document Management</div>



                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


{{-- <div class="modal fade draggable" id="forgot_password_modal" tabindex="-1" role="dialog" aria-labelledby="forgot_password_modal_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header draggable-handle">
                <h4 class="modal-title" id="forgot_password_modal_title">Forgot Password?</h4>
                <button type="button" class="close text-danger" data-dismiss="modal" aria-label="Close">
                    <i class="fal fa-times mt-2"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="forgot_password_form">
                    @csrf
                    <div class="px-3">
                        <div class="alert alert-danger hidden" id="reset_error_div"><i class="fa fa-exclamation-triangle mr-2"></i> <span id="reset_error_message"></span></div>
                        <div class="text-gray d-flex justify-content-around">
                            <div>
                                Enter your email address to reset your password
                                <input type="text" class="custom-form-element form-large form-input" id="forgot_email" name="email" data-label="Email Address">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer d-flex justify-content-around">
                <a class="btn btn-primary" id="save_forgot_password_button" data-dismiss"modal">Reset Password <i class="fal fa-arrow-right ml-2"></i></a>
            </div>
        </div>
    </div>
</div> --}}
@endsection
