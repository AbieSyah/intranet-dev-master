<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <base href="" />
  <title>Intranet Hisamitsu Pharma Indonesia</title>
  <meta charset="utf-8" />
  <meta name="description" content=" " />
  <meta name="keywords" content="" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta property="og:locale" content="en_US" />
  <meta property="og:type" content="article" />
  <meta property="og:title" content="INTRANET" />
  <meta property="og:url" content="https://intranet.hisamitsu.co.id" />
  <meta property="og:site_name" content="Intranet | Hisamitsu" />
  {{-- <link rel="shortcut icon" href="{{  url('') }}/assets/media/logos/favicon.ico" /> --}}
  <link rel="shortcut icon" href="{{  url('') }}/assets/images/logo.png">
  <link rel="icon" href="{{  url('') }}/assets/images/logo.png">

  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />

  <link href="{{  url('') }}/assets/plugins/custom/fullcalendar/fullcalendar.bundle.css" rel="stylesheet" type="text/css" />
  <link href="{{  url('') }}/assets/plugins/custom/datatables/datatables.bundle.css" rel="stylesheet" type="text/css" />
  <link href="{{  url('') }}/assets/plugins/Datatables/DataTables-1.13.1/css/dataTables.bootstrap5.css" rel="stylesheet"
    type="text/css" />
  <link href="{{  url('') }}/assets/plugins/Datatables/Buttons-2.3.3/css/buttons.dataTables.css" rel="stylesheet" type="text/css" />
  <link href="{{  url('') }}/assets/plugins/Datatables/Responsive-2.4.0/css/responsive.bootstrap.css" rel="stylesheet"
    type="text/css" />


  <link href="{{  url('') }}/assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
  <link href="{{  url('') }}/assets/css/style.bundle.css" rel="stylesheet" type="text/css" />

  @yield('link')

  <script>
    // Frame-busting to prevent site from being loaded within a frame without permission (click-jacking)
    if (window.top != window.self) {
      window.top.location.replace(window.self.location.href);
    }
  </script>
  @livewireStyles

  <style>
    body {
      font-weight: 500;
    }
  </style>

</head>

<body>

  @yield('content')

  <script src="{{  url('') }}/assets/plugins/global/plugins.bundle.js"></script>
  <script src="{{  url('') }}/assets/js/scripts.bundle.js"></script>
  @livewireScripts

</body>

</html>
