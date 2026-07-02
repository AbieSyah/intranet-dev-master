<!doctype html>
<html lang="en" data-layout="horizontal" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg"
  data-sidebar-image="none" data-preloader="disable">

<head>
  <meta charset="utf-8" />
  <title>Intranet - Hisamitsu Pharma Indonesia</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <!-- App favicon -->
  {{--
  <link rel="shortcut icon" href="{{  url('') }}/assets/images/favicon.ico"> --}}
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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css" />
  <style type="text/css">
    body {
      background: #f7fbf8;
    }

    img {
      /* display: block; */
      max-width: 100%;
    }

    .preview {
      text-align: center;
      overflow: hidden;
      width: 160px;
      height: 160px;
      margin: 10px;
      border: 1px solid red;
    }

    .section {
      margin-top: 150px;
      background: #fff;
      padding: 50px 30px;
    }
  </style>
  <!-- add link-->
  @yield('link')
  @stack('styles')
</head>

<body>
  <!-- Begin page -->
  <div id="layout-wrapper">
    @php
      \Carbon\Carbon::setLocale('id');
      $now = \Carbon\Carbon::now();
      $alertStart = \Carbon\Carbon::parse(env('MAINTENANCE_ALERT_START'));
      $eventStart = \Carbon\Carbon::parse(env('MAINTENANCE_EVENT_START'));
      $eventEnd = \Carbon\Carbon::parse(env('MAINTENANCE_EVENT_END'));
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
          <div class="alert alert-dismissible bg-danger text-white alert-label-icon rounded-label fade show mb-xl-0"
            role="alert">
            <i class="ri-error-warning-line label-icon"></i>
            <marquee class="mt-1 fw-bold">
              Untuk meningkatkan kualitas layanan dan performa sistem, kami akan melakukan pemeliharaan pada hari
              {{ $timeInfo }}. Selama periode ini, sistem tidak dapat diakses untuk sementara waktu. Kami Mohon Maaf atas
              ketidaknyamanan yang ditimbulkan.
            </marquee>
            <button type="button" class="btn-close text-white" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        </div>
      @elseif($isMaintenanceFinished)
        <div class="maintenance-alert">
          <div class="alert alert-dismissible bg-success text-white alert-label-icon rounded-label fade show mb-xl-0"
            role="alert">
            <i class="ri-check-line label-icon"></i>
            <marquee class="mt-1 fw-bold">
              Kami informasikan bahwa proses pemeliharaan telah selesai dilakukan. Saat ini, seluruh layanan sudah kembali
              beroperasi secara normal. Kami mengucapkan Terima Kasih atas kesabaran dan kerja sama Anda selama proses ini
              berlangsung. Selamat kembali beraktivitas.
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

    <!-- ========== App Menu ========== -->
    <!-- Left Sidebar End -->
    <!-- Vertical Overlay-->

    <!-- ============================================================== -->
    <!-- Start right Content here -->
    <!-- ============================================================== -->
    <header>
      <div class="container-fluid">
        <div class="profile-foreground position-relative mx-n4 mt-n1">
          <div class="profile-wid-bg">
            <img src="{{  url('') }}/assets/images/salonpas-bg.jpg" alt="" class="profile-wid-img" />
          </div>
        </div>
        <div class="pt-4 mb-4 mb-lg-3 pb-lg-4">
          <div class="row">
            <div class="d-flex justify-content-end bd-highlight">
              <div class="row text text-center">
                <div class="col-lg-12 col-md-6 flex">
                  @php
                    $tamu = App\Models\Security\Guest::where('id_employee', $user->employee_id)
                      ->whereNull('waktu_keluar')
                      ->whereNull('waktu_bertemu')
                      ->latest()
                      ->first();
                  @endphp
                  @if ($tamu)
                    <div class="dropdown mb-lg-3">
                      <button class="btn" type="button" id="dropdownTamu" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="d-flex align-items-center">
                          <span class="text-start text-white-75">
                            <i class="mdi mdi-account-tie align-middle me-1"></i> Tamu
                          </span>
                        </span>
                      </button>
                      <ul class="dropdown-menu w-100" aria-labelledby="dropdownTamu">
                        <li class="dropdown-item">
                          <div class="d-flex mb-3">
                            <div class="flex-grow-1">
                              <i class="mdi mdi-checkbox-blank-circle me-2 text-warning"></i>
                              <span class="fw-medium">{{ $tamu->nama }}</span>
                            </div>
                            <div class="flex-shrink-0"></div>
                          </div>
                          <h6 class="card-title fs-14">{{ $tamu->tujuan_kunjungan }}</h6>
                          <button class="btn btn-warning btn-sm w-100" id="button-tamu"
                            data-tamu-id="{{ $tamu->id }}">Selesai!</button>
                        </li>
                      </ul>
                    </div>

                    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                    <script>
                      document.addEventListener('DOMContentLoaded', function () {
                        document.getElementById('button-tamu').addEventListener('click', function () {
                          const guestId = this.getAttribute('data-tamu-id'); // Get the tamu ID
                          const requestUrl = "{{ route('guest.set-waktu-keluar', ':id') }}".replace(':id',
                            guestId); // Your route for patch

                          Swal.fire({
                            title: `Apakah anda yakin untuk menyelesaikan kunjungan dengan tamu?`,
                            icon: 'info',
                            showCancelButton: true,
                            cancelButtonText: 'Batal',
                            confirmButtonColor: 'success',
                            confirmButtonText: 'Selesai',
                            showLoaderOnConfirm: true,
                            preConfirm: () => {
                              return fetch(requestUrl, {
                                method: 'PATCH',
                                headers: {
                                  'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content')
                                }
                              }).then(response => {
                                if (!response.ok) {
                                  return response.json().then(error => {
                                    throw new Error(error.message || 'Error occurred!');
                                  });
                                }
                              }).catch(error => {
                                Swal.fire('Error!', error.message, 'error');
                              });
                            },
                            allowOutsideClick: () => !Swal.isLoading(),
                          }).then((result) => {
                            if (result.isConfirmed) {
                              Swal.fire('Success!', 'Selesai.', 'success').then(() => location.reload());
                            }
                          });
                        });
                      });
                    </script>
                  @endif
                </div>
              </div>
              <div class="row text text-center">
                <div class="col-lg-12 col-md-6 flex">
                  <div class="dropdown">
                    <button type="button" class="btn btn-gost" data-bs-toggle="dropdown" aria-haspopup="true"
                      aria-expanded="false">
                      <span class="d-flex align-items-center">
                        <span class="text-start text-white-75">
                          <i class="ri-settings-line align-middle me-1"></i> Settings
                        </span>
                      </span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                      <!-- item-->
                      <h6 class="dropdown-header">Welcome {{ Auth::user()->name }}</h6>
                      @can('emp.menu')
                        <a class="dropdown-item" href="{{ route('emp.profile') }}"><i
                            class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i>
                          <span class="align-middle">My Profile</span></a>
                      @else
                        <a class="dropdown-item" href="{{ route('profile') }}"><i
                            class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i>
                          <span class="align-middle">My Profile</span></a>
                      @endcan
                      <a class="dropdown-item" href="{{ route('user.password.index') }}">
                        <i class="mdi mdi-lock text-muted fs-16 align-middle me-1"></i>
                        <span class="align-middle">Reset Password</span>
                      </a>
                      <a class="dropdown-item" href="{{ route('disclaimer') }}"><i
                          class="ri-shield-flash-fill text-muted fs-16 align-middle me-1"></i>
                        <span class="align-middle">Privacy Policy</span></a>
                      <!-- <div class="dropdown-divider"></div> -->
                      <a class="dropdown-item" href="{{ route('logout') }}"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i
                          class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i> <span class="align-middle"
                          data-key="t-logout">Logout</span></a>
                      <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                      </form>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!--end col-->
          </div>
          <div class="row g-4">
            <div class="col-auto">
              {{-- <div class="avatar-lg">
                @if (!empty($user->employee->avatar))
                <img src="{{ asset('storage/avatars/'.$user->employee->avatar) }}" alt="user-img"
                  class="img-thumbnail rounded-circle" />
                @else
                <img src="{{  url('') }}/assets/images/users/user-dummy-img.jpg" alt="user-img"
                  class="img-thumbnail rounded-circle" />
                @endif
              </div> --}}
              <div class="profile-user position-relative d-inline-block mx-auto">
                @if ($user->employee && !empty($user->employee->avatar))
                  <div id="avatar-user">
                    <img src="{{ asset('storage/avatars/' . $user->employee->avatar) }}"
                      class="show-image rounded-circle avatar-xl img-thumbnail user-profile-image"
                      alt="user-profile-image">
                  </div>
                @else
                  <div id="avatar-user">
                    <img src="{{ asset('storage/avatars/user.jpg') }}"
                      class="rounded-circle avatar-xl img-thumbnail user-profile-image" alt="user-profile-image">
                  </div>
                @endif
                <div class="avatar-xs p-0 rounded-circle profile-photo-edit">
                  <input onchange="avatarValidation(this);" id="profile-img-file-input" type="file" name="image"
                    class="image profile-img-file-input"
                    accept="image/jpg,image/jpeg,image/png,image/JPG,image/JPEG,image/PNG">
                  <label for="profile-img-file-input" class="profile-photo-edit avatar-xs">
                    <span class="avatar-title rounded-circle bg-light text-body">
                      <i class="ri-camera-fill"></i>
                    </span>
                  </label>
                </div>
              </div>
            </div>
            <!--end col-->
            <div class="col-auto">
              <div class="p-2">
                <h3 class="text-white mb-1">{{ $user->employee?->fullname ?? $user->name }}</h3>
                <p class="text-white-75">{{ $user->employee?->email ?? $user->email }}</p>
                <div class="hstack text-white-50 gap-1">
                  <div class="me-2"><i class="ri-map-pin-user-line me-1 text-white-75 fs-16 align-middle"></i>
                    {{ $user->employee?->area?->name ?? 'N/A' }}
                  </div>
                  <div><i class="ri-building-line me-1 text-white-75 fs-16 align-middle"></i>
                    {{ $user->employee?->department?->name ?? 'N/A' }}
                  </div>
                </div>
                <div class="hstack text-white-50 gap-1">
                  <div class="me-2">
                    @if (!empty($user->employee?->level?->nama))
                      <i class="ri-contacts-book-2-line me-1 text-white-75 fs-16 align-middle"></i>
                      {{ $user->employee->level->nama }}
                    @endif
                  </div>
                  <div>
                    @if (!empty($user->employee?->position?->nama))
                      <i class="ri-contacts-book-line me-1 text-white-75 fs-16 align-middle"></i>
                      {{ $user->employee->position->nama }}
                    @endif
                  </div>
                </div>
              </div>
            </div>
            <!--end col-->
          </div>
          <!--end row-->
        </div>
        <!-- navbars -->
        @include('partials.navbar')
        <!-- end navbars-->
        <!-- main content-->
        @yield('content')
        <!-- end main content-->
        <!-- Modal Validation Extension File Upload Gambar -->
        <div class="modal fade" id="secondmodal" aria-hidden="true" aria-labelledby="..." tabindex="-1">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-body text-center p-5">
                <lord-icon src="https://cdn.lordicon.com/tdrtiskw.json" trigger="loop"
                  colors="primary:#f7b84b,secondary:#405189" style="width:130px;height:130px">
                </lord-icon>
                <div class="mt-4 pt-4">
                  <h4>Whoops, ada yang salah!</h4>
                  <p class="text-muted">Maaf hanya menerima file foto yang bertipe .jpg | .jpeg |
                    .png</p>
                  <!-- Toogle to second dialog -->
                  <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- Modal Upload foto -->
        <div class="modal fade" id="modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
          role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg  modal-dialog-scrollable" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Upload Foto Profile</h5>
              </div>
              <div class="modal-body">
                <div data-simplebar style="max-width: 100%;">
                  <div class="img-container">
                    <div class="row">
                      <div class="col-md-8">
                        <img id="image" src="">
                      </div>
                      <div class="col-md-4">
                        <div class="preview"></div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="d-flex gap-2 justify-content-end">
                  <button type="button" onclick="cancelAvatar()" class="btn btn-secondary"
                    data-bs-dismiss="modal">Cancel</button>
                  <button type="button" class="btn btn-primary" id="crop">Crop</button>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!--modal konfirmasi upload foto -->
        <div class="modal fade" id="konfirmasimodal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
          role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-body text-center p-5">
                <form class="form" action="{{ route('profile.upload') }}" method="post" enctype="multipart/form-data">
                  @csrf
                  <div class="mt-4 pt-3">
                    <p class="text-muted mb-4">Apakah Anda Yakin Mengubah Foto Profile Anda?</p>
                    <img src="" style="width: 100px;" class="show-image mb-4">
                    <input type="hidden" name="image_base64">
                    <div class="hstack gap-2 justify-content-center">
                      <button type="submit" class="btn btn-primary">Ya</button>
                      <button type="button" onclick="cancelAvatar()" class="btn btn-secondary"
                        data-bs-dismiss="modal">Tidak</button>
                      <!-- <button class="btn btn-secondary" data-bs-dismiss="modal">
                                                Tidak
                                            </button> -->
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div><!-- container-fluid -->
    </header>
    <div class="main-content">
      <div class="page-content">
      </div>
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
  <!-- profile-setting init js -->
  <script src="{{  url('') }}/assets/js/pages/profile-setting.init.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.js"></script>
  <!-- Add js -->
  @yield('script')
  @yield('javascript')
  <script>
    var $modal = $('#modal');
    var image = document.getElementById('image');
    var cropper;

    /*------------------------------------------
    --------------------------------------------
    Image Change Event
    --------------------------------------------
    --------------------------------------------*/
    $("body").on("change", ".image", function (e) {
      var files = e.target.files;
      var done = function (url) {
        image.src = url;
        $modal.modal('show');
      };

      var reader;
      var file;
      var url;

      if (files && files.length > 0) {
        file = files[0];

        if (URL) {
          done(URL.createObjectURL(file));
        } else if (FileReader) {
          reader = new FileReader();
          reader.onload = function (e) {
            done(reader.result);
          };
          reader.readAsDataURL(file);
        }
      }
    });

    /*------------------------------------------
    --------------------------------------------
    Show Model Event
    --------------------------------------------
    --------------------------------------------*/
    $modal.on('shown.bs.modal', function () {
      cropper = new Cropper(image, {
        aspectRatio: 1,
        viewMode: 3,
        preview: '.preview'
      });
    }).on('hidden.bs.modal', function () {
      cropper.destroy();
      cropper = null;
    });

    /*------------------------------------------
    --------------------------------------------
    Crop Button Click Event
    --------------------------------------------
    --------------------------------------------*/
    $("#crop").click(function () {
      canvas = cropper.getCroppedCanvas({
        // width: 160,
        // height: 160,
        width: 200,
        height: 200,
      });

      canvas.toBlob(function (blob) {
        url = URL.createObjectURL(blob);
        var reader = new FileReader();
        reader.readAsDataURL(blob);
        reader.onloadend = function () {
          var base64data = reader.result;
          $("input[name='image_base64']").val(base64data);
          $(".show-image").show();
          $(".show-image").attr("src", base64data);
          $("#modal").modal('toggle');
        }
      });

      $("#konfirmasimodal").modal("show");
    });
  </script>
  <script type="text/javascript">
    function cancelAvatar() {
      var avatar = document.getElementById('profile-img-file-input');
      avatar.value = '';
      var pre_avatar = {{ Js::from($user->employee?->avatar ?? '') }};
      if (!pre_avatar) {
        document.getElementById("avatar-user").innerHTML =
          '<img src="{{  url('') }}/assets/images/users/user-dummy-img.jpg" class="rounded-circle avatar-xl img-thumbnail user-profile-image" alt="user-profile-image">';
      } else {
        document.getElementById("avatar-user").innerHTML = '<img src="/storage/avatars/' + pre_avatar +
          '" class="rounded-circle avatar-xl img-thumbnail user-profile-image" alt="user-profile-image">';
      }
    }

    function clearAvatar() {
      var pre_avatar = {{ Js::from($user->employee?->avatar ?? '') }};
      if (!pre_avatar) {
        document.getElementById("avatar-user").innerHTML =
          '<img src="{{  url('') }}/assets/images/users/user-dummy-img.jpg" class="rounded-circle avatar-xl img-thumbnail user-profile-image" alt="user-profile-image">';
      } else {
        document.getElementById("avatar-user").innerHTML = '<img src="/storage/avatars/' + pre_avatar +
          '" class="rounded-circle avatar-xl img-thumbnail user-profile-image" alt="user-profile-image">';
      }
      var file_avatar = document.getElementById('profile-img-file-input');
      file_avatar.value = '';

      var remove_avatar = document.getElementById('remove_file');
      remove_avatar.value = '1';
    }

    function avatarValidation() {
      //foto profile
      var profile = document.getElementById('profile-img-file-input');
      var pathProfile = profile.value;

      // tipe file yang diizinkan
      var allowedExtensions =
        /(\.jpg|\.jpeg|\.png|\.JPG|\.JPEG|\.PNG)$/i;

      //masalah modal
      if (!allowedExtensions.exec(pathProfile)) {
        $('#secondmodal').modal('show');
        // alert('Invalid file type');
        profile.value = '';
        return false;
      }
    }
  </script>
  @stack('scripts')
</body>

</html>