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

    <!--Swiper slider css-->
    <link href="{{  url('') }}/assets/libs/swiper/swiper-bundle.min.css" rel="stylesheet" type="text/css" />

    <!-- Layout config Js -->
    <script src="{{  url('') }}/assets/js/layout.js"></script>
    <!-- Bootstrap Css -->
    <link href="{{  url('') }}/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{  url('') }}/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{  url('') }}/assets/css/app.min.css" rel="stylesheet" type="text/css" />
    <!-- custom Css-->
    <link href="{{  url('') }}/assets/css/custom.min.css" rel="stylesheet" type="text/css" />
    @yield('link')
    <!-- Meta -->

</head>

<body>
    <div class="layout-wrapper landing">
        @yield('content')
    </div>

    <!-- Wrapper-->

    <!-- Jquery and Js Plugins -->
    
    <script src="{{  url('') }}/assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="{{  url('') }}/assets/libs/simplebar/simplebar.min.js"></script>
    <script src="{{  url('') }}/assets/libs/node-waves/waves.min.js"></script>
    <script src="{{  url('') }}/assets/libs/feather-icons/feather.min.js"></script>
    <script src="{{  url('') }}/assets/js/pages/plugins/lord-icon-2.1.0.js"></script>
    <script src="{{  url('') }}/assets/js/plugins.js"></script>
    <script src="/adminlte/plugins/jquery/jquery-3.5.1.min.js"></script>

    <!--Swiper slider js-->
    <script src="{{  url('') }}/assets/libs/swiper/swiper-bundle.min.js"></script>
    <!-- landing init -->
    <script src="{{  url('') }}/assets/js/pages/landing.init.js"></script>

    
    @yield('script')
    @yield('javascript')
</body>

</html>
