@extends('layouts.general')
@section('link')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Toastr Notifications-->
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endsection

@section('content')
    <!-- <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Reset Password</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Password</a></li>
                        <li class="breadcrumb-item active">Reset</li>
                    </ol>
                </div>

            </div>
        </div>
    </div> -->

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header border-0">
                    <div class="text-center mt-2">
                        <h5 class="text-primary">Create New Password</h5>
                        <p class="text-muted">Silahkan Buat Kata Sandi Baru</p>
                    </div>
                </div><!-- end card header -->
                <div class="card-body">                    
                    <div class="p-2">
                        <form method="POST" action="{{ route('user.password.update') }}">
                            @method('patch')
                            @csrf
                            <div class="mb-3">
                                <label class="form-label" for="password-input">Password</label>
                                <div class="position-relative auth-pass-inputgroup">
                                    <input type="password" class="form-control pe-5 password-input" onpaste="return false" placeholder="Enter password" id="password-input" name="password" aria-describedby="passwordInput" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" required autocomplete="new-password">
                                    <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon" type="button" id="password-addon"><i class="ri-eye-fill align-middle"></i></button>
                                </div>
                                <div id="passwordInput" class="form-text">Must be at least 8 characters.</div>
                            </div>

                            <!-- <div class="mb-3">
                                <label class="form-label" for="password-confirmation">Confirm Password</label>
                                <div class="position-relative auth-pass-inputgroup mb-3">
                                    <input id="password-confirm" type="password" class="form-control pe-5 password-input" placeholder="Confirm password" name="password_confirmation" required autocomplete="new-password">
                                    <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon" type="button" id="confirm-password-input"><i class="ri-eye-fill align-middle"></i></button>
                                </div>
                            </div> -->

                            <div id="password-contain" class="p-3 bg-light mb-2 rounded">
                                <h5 class="fs-13">Password must contain:</h5>
                                <p id="pass-length" class="invalid fs-12 mb-2">Minimum <b>8 characters</b></p>
                                <p id="pass-lower" class="invalid fs-12 mb-2">At <b>lowercase</b> letter (a-z)</p>
                                <p id="pass-upper" class="invalid fs-12 mb-2">At least <b>uppercase</b> letter (A-Z)</p>
                                <p id="pass-number" class="invalid fs-12 mb-0">A least <b>number</b> (0-9)</p>
                            </div>

                            <div class="mt-4">
                                <button class="btn btn-success w-100" type="submit">Reset Password</button>
                            </div>
                        </form><!-- end form -->
                    </div>
                </div><!-- end card-body -->
            </div><!-- end card -->
        </div>
        <!--end col-->
    </div>
@endsection
@section('script')
    <!-- <script src="{{asset('assets/libs/Datatables/jQuery-3.6.0/jquery-3.6.0.min.js')}}"></script> -->
    <!-- Toastr Notifications-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <!-- password-addon init -->
    <script src="/assets/js/pages/passowrd-create.init.js"></script>
@endsection
@section('javascript')
    <script>
       @if(Session::has('status'))
            toastr.options =
            {
                "closeButton" : true,
                "progressBar" : true,
                "positionClass": "toast-bottom-right"
            }
            toastr.success("{{ session('status') }}");
        @endif
    </script>
@endsection
