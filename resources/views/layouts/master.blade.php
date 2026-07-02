<!doctype html>
@if (Auth::user()->can('emp.menu'))
  <html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="sm"
    data-sidebar-image="none" data-preloader="disable">
@else
  <html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg"
    data-sidebar-image="none" data-preloader="disable">
@endif

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
  <!-- add link-->
  @stack('styles')
  <style>
    .select2-selection__choice__display {
      color: black !important;
    }
  </style>
  @yield('link')
</head>

<body>
  <!-- Begin page -->
  <div id="layout-wrapper">
    @include('partials.header')
    <!-- ========== App Menu ========== -->
    @include('partials.menu')
    <!-- Left Sidebar End -->
    <!-- Vertical Overlay-->
    <div class="vertical-overlay"></div>

    <!-- ============================================================== -->
    <!-- Start right Content here -->
    <!-- ============================================================== -->
    <div class="main-content">
      <div class="page-content">
        <div class="container-fluid">
          <!-- start page title -->
          @yield('content')
          <!-- end page title -->
        </div>
        <!-- container-fluid -->
      </div>
      <!-- End Page-content -->

      @php
          \Carbon\Carbon::setLocale('id');
          $now = \Carbon\Carbon::now();
          $alertStart = \Carbon\Carbon::parse(env('MAINTENANCE_ALERT_START'));
          $eventStart = \Carbon\Carbon::parse(env('MAINTENANCE_EVENT_START'));
          $eventEnd   = \Carbon\Carbon::parse(env('MAINTENANCE_EVENT_END'));
          $afterEventEnd = $eventEnd->copy()->addHour();
          $showAlert = $now->greaterThanOrEqualTo($alertStart) && $now->lessThanOrEqualTo($afterEventEnd);
          $isUnderMaintenance = $now->lessThan($eventEnd);
          $isMaintenanceFinished = $now->greaterThanOrEqualTo($eventEnd) && $now->lessThanOrEqualTo($afterEventEnd);
          $isSameDay = $eventStart->isSameDay($eventEnd);
          if ($isSameDay) {
              $timeInfo = $eventStart->isoFormat('dddd, D MMMM YYYY') . " Pukul " . $eventStart->format('H:i') . " - " . $eventEnd->format('H:i') . " WIB";
          } else {
              $timeInfo = $eventStart->isoFormat('dddd, D MMMM YYYY') . " Pukul " . $eventStart->format('H:i') . " WIB - " . 
                          $eventEnd->isoFormat('dddd, D MMMM YYYY') . " Pukul " . $eventEnd->format('H:i') . " WIB";
          }
      @endphp
      @if($showAlert)
        @if($isUnderMaintenance)
          <div class="maintenance-alert">
              <div class="alert alert-dismissible bg-danger text-white alert-label-icon rounded-label fade show mb-xl-0" role="alert">
                <i class="ri-error-warning-line label-icon"></i>
                <marquee class="mt-1 fw-bold">
                    Untuk meningkatkan kualitas layanan dan performa sistem, kami akan melakukan pemeliharaan pada hari {{ $timeInfo }}. Selama periode ini, sistem tidak dapat diakses untuk sementara waktu. Kami Mohon Maaf atas ketidaknyamanan yang ditimbulkan.
                </marquee>
                <button type="button" class="btn-close text-white" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
          </div>
        @elseif($isMaintenanceFinished)
          <div class="maintenance-alert">
              <div class="alert alert-dismissible bg-success text-white alert-label-icon rounded-label fade show mb-xl-0" role="alert">
                <i class="ri-check-line label-icon"></i>
                <marquee class="mt-1 fw-bold">
                    Kami informasikan bahwa proses pemeliharaan telah selesai dilakukan. Saat ini, seluruh layanan sudah kembali beroperasi secara normal. Kami mengucapkan Terima Kasih atas kesabaran dan kerja sama Anda selama proses ini berlangsung. Selamat kembali beraktivitas.
                </marquee>
                <button type="button" class="btn-close text-white" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
          </div>
        @endif
        <style>
            .maintenance-alert {
                position: fixed;
                bottom: 10px;
                right: 20px;
                left: 20px;
                z-index: 9999;
                max-width: 100%;
            }
            @media (max-width: 576px) {
                .maintenance-alert {
                    right: 10px;
                    left: 10px;
                    min-width: unset;
                    max-width: unset;
                }
            }
        </style>
      @endif

      <footer class="footer">
        <div class="container-fluid">
          <div class="row">
            <div class="col-sm-6">
              <script>
                document.write(new Date().getFullYear())
              </script> © INTRANET.
            </div>
            <div class="col-sm-6">
              <div class="text-sm-end d-none d-sm-block">
                Design & Develop by Information Technology Hisamitsu Pharma Indonesia
              </div>
            </div>
          </div>
        </div>
      </footer>
    </div>
    <!-- end main content-->

  </div>
  <!-- END layout-wrapper -->

  <!-- JAVASCRIPT -->
  <script src="{{  url('') }}/assets/libs/Jquery/jquery-3.6.3.min.js"></script>
  <script src="{{  url('') }}/assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="{{  url('') }}/assets/libs/simplebar/simplebar.min.js"></script>
  <script src="{{  url('') }}/assets/libs/node-waves/waves.min.js"></script>
  <script src="{{  url('') }}/assets/libs/feather-icons/feather.min.js"></script>
  <script src="{{  url('') }}/assets/js/pages/plugins/lord-icon-2.1.0.js"></script>
  <script src="{{  url('') }}/assets/js/plugins.js"></script>

  <!-- notifications init -->
  <script src="{{  url('') }}/assets/js/pages/notifications.init.js"></script>

  <!-- App js -->
  <script src="{{  url('') }}/assets/js/app.js"></script>
  <!-- Add js -->
  @yield('script')
  @yield('javascript')

  <script>
    $(document).on('select2:open', () => {
      document.querySelector('.select2-search__field').focus();
    });
  </script>
  @stack('scripts')
</body>

</html>
