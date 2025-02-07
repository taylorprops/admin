@extends('layouts.login')

@section('content')
{{ Auth::logout() }}

<div class="vh-100 vw-100 bg-primary">

    <div class="container-1000 login-container">

        <div class="row">

            <div class="col-12 col-sm-8 col-lg-6 mx-auto">

                <div class="d-flex justify-content-around align-items-center vh-90 w-100">

                    <form id="login_form" class="w-100">

                        <div class="d-flex justify-content-around mb-4">
                            <img src="/images/logo/logos.png">
                        </div>
                        <div class="card w-100 shadow">

                            <div class="card-header bg-primary text-white">Login</div>

                            <div class="card-body text-primary">

                                <div class="alert alert-danger hidden" id="error_div"><i class="fa fa-exclamation-triangle mr-2"></i> <span id="error_message"></span></div>

                                <div class="px-5 pt-2">

                                    <input type="text" class="custom-form-element form-input required" id="email" name="email" value="{{ old('email') }}" data-label="Email Address" required autocomplete="email" autofocus>

                                    <input type="password" class="custom-form-element form-input required" id="password" name="password" data-label="Password" required autocomplete="current-password">

                                    <input class="custom-form-element form-checkbox" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }} data-label="Remember Me">

                                </div>

                            </div>

                            <div class="card-footer bg-white">

                                <div class="d-flex justify-content-around">
                                    <a href="javascript:void(0)" class="btn btn-primary" id="login_button">Login <i class="fal fa-arrow-right ml-2"></i></a>
                                </div>
                                <div class="d-flex justify-content-around mt-3">
                                    <a href="javascript:void(0)" id="forgot_password_button">Forgot Your Password?</a>
                                </div>

                            </div>

                            <input type="hidden" id="previous_url" name="previous_url" value="{{ url() -> previous() }}">

                        </div>

                    </form>

                </div>

            </div>

        </div>


        {{-- <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Login') }}</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <div class="form-group row">
                                <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                                <div class="col-md-6">
                                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                                <div class="col-md-6">
                                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-6 offset-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                        <label class="form-check-label" for="remember">
                                            {{ __('Remember Me') }}
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-8 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Login') }}
                                    </button>

                                    @if (Route::has('password.request'))
                                        <a class="btn btn-link" href="{{ route('password.request') }}">
                                            {{ __('Forgot Your Password?') }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                            <input type="hidden" id="previous_url" name="previous_url" value="{{ url() -> previous() }}">
                        </form>
                    </div>
                </div>
            </div>
        </div> --}}
    </div>

</div>

<div class="modal fade draggable" id="forgot_password_modal" tabindex="-1" role="dialog" aria-labelledby="forgot_password_modal_title" aria-hidden="true">
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
                                <input type="text" class="custom-form-element form-input" id="forgot_email" name="email" data-label="Email Address">
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
</div>
@endsection
