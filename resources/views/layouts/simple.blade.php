<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Intranet - Hisamitsu Pharma Indonesia</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- <link rel="shortcut icon" href="{{  url('') }}/assets/images/favicon.ico"> --}}
    <link rel="shortcut icon" href="{{  url('') }}/assets/images/logo.png">
    <link rel="icon" href="{{  url('') }}/assets/images/logo.png">
    <script src="{{  url('') }}/assets/js/layout.js"></script>
    <link href="{{  url('') }}/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="{{  url('') }}/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="{{  url('') }}/assets/css/app.min.css" rel="stylesheet" type="text/css" />
    @yield('link')

    <style>
        /* Ensure the footer stays at the bottom for mobile devices */
        body,
        html {
            height: 100%;
            margin: 0;
        }

        .page-content {
            padding: calc(20px + 1.5rem) calc(1.5rem * .5) 60px calc(1.5rem * .5);
            min-height: calc(100vh - 60px);
            max-width: 1000px;
            margin: 0 auto;
        }

        @media (max-width: 576px) {
            .page-content {
                padding: calc(20px + 1.5rem) 0 60px 0;
            }
        }

        .footer {
            width: 100%;
            background-color: #f8f9fa;
            padding: 1rem 0;
            position: fixed;
            bottom: 0;
            left: 0 !important;
        }

        .footer .container-fluid {
            max-width: 1000px;
            margin: 0 auto;
            text-align: between;
        }

        .footer .row {
            margin: 0;
        }

        .footer .col-sm-6 {
            margin-bottom: 0.5rem;
        }

        .footer .text-sm-end {
            text-align: center !important;
            /* Center text on smaller screens */
        }
    </style>
</head>

<body>
    <div id="layout-wrapper">
        <div class="page-content">
            <div class="container-fluid">
                @yield('content')
            </div>
        </div>
        <footer class="footer">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <script>
                            document.write(new Date().getFullYear())
                        </script> © INTRANET.
                    </div>
                    <div class="col-sm-6 text-sm-end">
                        Design & Develop by Information Technology Hisamitsu Pharma Indonesia
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <script src="{{  url('') }}/assets/libs/Jquery/jquery-3.6.3.min.js"></script>
    <script src="{{  url('') }}/assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="{{  url('') }}/assets/libs/simplebar/simplebar.min.js"></script>
    <script src="{{  url('') }}/assets/libs/node-waves/waves.min.js"></script>
    <script src="{{  url('') }}/assets/libs/feather-icons/feather.min.js"></script>
    <script src="{{  url('') }}/assets/js/pages/plugins/lord-icon-2.1.0.js"></script>
    <script src="{{  url('') }}/assets/js/plugins.js"></script>
    <script src="{{  url('') }}/assets/js/pages/notifications.init.js"></script>
    <script src="{{  url('') }}/assets/js/app.js"></script>
    @yield('script')
    @yield('javascript')
</body>

</html>
