<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable">

<head>
    <meta charset="utf-8" />
    <title>Intranet - Hisamitsu Pharma Indonesia</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- App favicon -->
    {{-- <link rel="shortcut icon" href="{{  url('') }}/assets/images/favicon.ico"> --}}
    <link rel="shortcut icon" href="{{  url('') }}/assets/images/logo.png">
    <link rel="icon" href="{{  url('') }}/assets/images/logo.png">

    <!-- Layout config Js -->
    <script src="{{  url('') }}/assets/js/layout.js"></script>
    <!-- Bootstrap Css -->
    <link href="{{  url('') }}/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{  url('') }}/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{  url('') }}/assets/css/app.min.css" rel="stylesheet" type="text/css" />
    <!-- custom Css-->
    <!-- <link href="{{  url('') }}/assets/css/custom.min.css" rel="stylesheet" type="text/css" /> -->
    <!-- Select2-->
    <link rel="stylesheet" href="{{  url('') }}/assets/libs/adminlte/select2/css/4.1.0/select2.min.css">
</head>

<body>

    <!-- auth-page wrapper -->
    <div class="auth-page-wrapper auth-bg-cover py-0 d-flex justify-content-center align-items-center min-vh-100">
        <div class="bg-overlay"></div>
        <!-- auth-page content -->
        <div class="auth-page-content overflow-hidden pt-lg-3">
            <div class="container">
                <div class="row">
                    @yield('content')
                </div>
                <!-- end row -->
            </div>
            <!-- end container -->
        </div>
        <!-- end auth page content -->

        <!-- footer -->
        <footer class="footer">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center">
                            <p class="mb-0">&copy;
                                <script>document.write(new Date().getFullYear())</script> INTRANET Created By Information Technology Hisamitsu Pharma Indonesia
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <!-- end Footer -->
    </div>
    <!-- end auth-page-wrapper -->

    <!-- JAVASCRIPT -->
    <script src="{{  url('') }}/assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="{{  url('') }}/assets/libs/simplebar/simplebar.min.js"></script>
    <script src="{{  url('') }}/assets/libs/node-waves/waves.min.js"></script>
    <script src="{{  url('') }}/assets/libs/feather-icons/feather.min.js"></script>
    <script src="{{  url('') }}/assets/js/pages/plugins/lord-icon-2.1.0.js"></script>
    <script src="{{  url('') }}/assets/js/plugins.js"></script>
    <script src="{{  url('') }}/assets/js/pages/passowrd-create.init.js"></script>
    <!-- password-addon init -->
    <!-- <script src="{{  url('') }}/assets/js/pages/password-addon.init.js"></script> -->
    <!-- Select2 -->
    <script src="{{  url('') }}/assets/libs/adminlte/jquery/jquery-3.5.1.min.js"></script>
    <script src="{{  url('') }}/assets/libs/adminlte/select2/js/4.1.0/select2.min.js"></script>
    <script type="text/javascript">
        $(function () {
            $('.select2').select2()
        });
    </script>
</body>

</html>