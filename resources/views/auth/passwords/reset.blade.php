@extends('layouts.auth')
@section('content')
<div class="col-lg-12">
    <div class="card overflow-hidden border-0">
        <div class="row justify-content-center g-0">
            <div class="col-lg-6">
                <div class="p-lg-5 p-4 auth-one-bg h-100">
                    
                </div>
            </div>
            <!-- end col -->

            <div class="col-lg-6">
                <div class="p-lg-5 p-4">
                    <h5 class="text-primary">Forgot Password?</h5>
                    <p class="text-muted">Silahkan Buat Kata Sandi Baru</p>
                    <div class="p-2">
                        <form method="POST" action="{{ route('password.update') }}">
                            @csrf
                            <input type="hidden" name="token" value="{{ $token }}">
                            <div class="mb-4">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control  @error('email') is-invalid @enderror" id="email" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label class="form-label" for="password-input">Password</label>
                                <div class="position-relative auth-pass-inputgroup">
                                    <input type="password" class="form-control pe-5 password-input" onpaste="return false" placeholder="Enter password" id="password-input" name="password" aria-describedby="passwordInput" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" required autocomplete="new-password">
                                    <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon" type="button" id="password-addon"><i class="ri-eye-fill align-middle"></i></button>
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div id="passwordInput" class="form-text">Must be at least 8 characters.</div>
                            </div>
                            <div id="password-contain" class="p-3 bg-light mb-2 rounded">
                                <h5 class="fs-13">Password must contain:</h5>
                                <p id="pass-length" class="invalid fs-12 mb-2">Minimum <b>8 characters</b></p>
                                <p id="pass-lower" class="invalid fs-12 mb-2">At <b>lowercase</b> letter (a-z)</p>
                                <p id="pass-upper" class="invalid fs-12 mb-2">At least <b>uppercase</b> letter (A-Z)</p>
                                <p id="pass-number" class="invalid fs-12 mb-0">A least <b>number</b> (0-9)</p>
                            </div>
                            <div class="mb-4">
                                <label class="form-label" for="confirm-password-input">Confirm Password</label>
                                <div class="position-relative auth-pass-inputgroup mb-3">
                                    <input id="password-confirm" type="password" class="form-control pe-5 password-input" name="password_confirmation" required autocomplete="new-password">
                                    <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon" type="button" id="confirm-password-input"><i class="ri-eye-fill align-middle"></i></button>
                                </div>
                            </div>
                            <div class="text-center mt-4">
                                <button class="btn btn-success w-100" type="submit">Reset Password</button>
                            </div>
                        </form><!-- end form -->
                    </div>

                    <div class="mt-5 text-center">
                        <p class="mb-0">Wait, I remember my password... <a href="{{ route('login') }}" class="fw-semibold text-primary text-decoration-underline"> Click here </a> </p>
                    </div>
                </div>
            </div>
            <!-- end col -->
        </div>
        <!-- end row -->
    </div>
    <!-- end card -->
</div>
<!-- end col -->
@endsection

{{--
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Reset Password') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf

                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-end">{{ __('Confirm Password') }}</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Reset Password') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
--}}
