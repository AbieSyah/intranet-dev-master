<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="light" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable">

<head>
    <meta charset="utf-8" />
    <title>Intranet | Hisamitsu Pharma Indonesia</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesbrand" name="author" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- App favicon -->
    <link rel="shortcut icon" href="/assets/images/favicon.ico">

    <!-- Layout config Js -->
    <script src="/assets/js/layout.js"></script>
    <!-- Bootstrap Css -->
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="/assets/css/app.min.css" rel="stylesheet" type="text/css" />
    <!-- custom Css-->
    <link href="/assets/css/custom.min.css" rel="stylesheet" type="text/css" />
    <!-- Toastr Notifications-->
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
</head>

<body>
    <!-- auth-page wrapper -->
    <div class="auth-page-wrapper auth-bg-cover py-5 d-flex justify-content-center align-items-center min-vh-100">
        <div class="bg-overlay"></div>
        <!-- auth-page content -->
        <div class="auth-page-content overflow-hidden pt-lg-12">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-8">
                        <!-- Warning Alert -->
                        <div class="alert alert-warning alert-border-left alert-dismissible fade show" role="alert">
                            <i class="ri-alert-line me-3 align-middle"></i> Dalam upaya meningkatkan keamanan akun anda, silahkan melakukan perubahan password
                            <!-- <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button> -->
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="card overflow-hidden">
                            <div class="card-body">
                                <div class="text-center">
                                    <h4 class="text-uppercase">Silahkan Buat Kata Sandi Baru</h4>
                                    <div class="row justify-content-center mt-4">
                                        <div class="col-lg-12">
                                            <form method="POST" action="{{ route('password.update.year') }}">
                                                    @method('patch')
                                                    @csrf
                                                    <div class="mb-3">
                                                        <!-- <label class="form-label" for="password-input">Password</label> -->
                                                        <div class="position-relative auth-pass-inputgroup">
                                                            <input type="password" class="form-control pe-5 password-input" onpaste="return false" placeholder="Enter password" id="password-input" name="password" aria-describedby="passwordInput" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" required autocomplete="new-password">
                                                            <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon" type="button" id="password-addon"><i class="ri-eye-fill align-middle"></i></button>
                                                        </div>
                                                        <div id="passwordInput" class="form-text float-start mb-4">Must be at least 8 characters.</div>
                                                    </div>
                                                    <div class="col-lg-12 float-start">
                                                    <div id="password-contain" class="p-3 bg-light mb-2 rounded">
                                                        <h5 class="fs-13">Password must contain:</h5>
                                                        <p id="pass-length" class="invalid fs-12 mb-2">Minimum <b>8 characters</b></p>
                                                        <p id="pass-lower" class="invalid fs-12 mb-2">At <b>lowercase</b> letter (a-z)</p>
                                                        <p id="pass-upper" class="invalid fs-12 mb-2">At least <b>uppercase</b> letter (A-Z)</p>
                                                        <p id="pass-number" class="invalid fs-12 mb-0">A least <b>number</b> (0-9)</p>
                                                    </div>
                                                    </div>

                                                    <div class="mt-4">
                                                        <button class="btn btn-success w-100" type="submit">Submit</button>
                                                    </div>
                                            </form><!-- end form -->
                                            <div class="mt-2">
                                                <a href="{{ route('logout') }}" class="btn btn-secondary w-100" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Back</a>
                                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                                    @csrf
                                                </form>                                            
                                            </div>
                                        </div>
                                    </div>                                    
                                </div>
                            </div>
                        </div>
                        <!-- end card -->
                        
                    </div>
                    <!-- end col -->
                </div>
                <!-- end row -->
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        
                    </div>
                </div>
            </div>
            <!-- end container -->
        </div>
        <!-- end auth page content -->
    </div>
    <!-- end auth-page-wrapper -->

    <!-- JAVASCRIPT -->
    <script src="/assets/libs/Jquery/jquery-3.6.3.min.js"></script>
    <script src="/assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/libs/simplebar/simplebar.min.js"></script>
    <script src="/assets/libs/node-waves/waves.min.js"></script>
    <script src="/assets/libs/feather-icons/feather.min.js"></script>
    <script src="/assets/js/pages/plugins/lord-icon-2.1.0.js"></script>
    <script src="/assets/js/plugins.js"></script>
    <!-- Toastr Notifications-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <!-- password-addon init -->
    <script src="/assets/js/pages/passowrd-create.init.js"></script>
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

</body>

</html>