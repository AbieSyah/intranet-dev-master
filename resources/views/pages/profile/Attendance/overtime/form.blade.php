@extends('layouts.master')

@section('link')
   <meta name="csrf-token" content="{{ csrf_token() }}">
   <!-- Required meta tags -->
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
   <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css" />
   <!-- Datatables-->
   <link href="{{ url('') }}/assets/libs/Datatables/DataTables-1.13.1/css/dataTables.bootstrap5.css" rel="stylesheet"
      type="text/css" />
   <link href="{{ url('') }}/assets/libs/Datatables/Buttons-2.3.3/css/buttons.dataTables.css" rel="stylesheet"
      type="text/css" />
   <link href="{{ url('') }}/assets/libs/Datatables/Responsive-2.4.0/css/responsive.bootstrap.css" rel="stylesheet"
      type="text/css" />
   <!-- Toastr Notifications-->
   <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
   {{-- checkbox styling --}}
   <style>
      .catalog-card {cursor: pointer;}
      .catalog-card .card {transition: 0.2s ease-in-out;border: 2px solid #e5e5e5;}
      .catalog-card.active .card {border: 2px solid #0d6efd;background-color: #eef4ff;}
   </style>
   {{-- end checkbox styling --}}
@endsection

@section('content')
   <div class="container-fluid">
      <div class="profile-foreground position-relative mx-n4 mt-n4">
         <div class="profile-wid-bg">
            <img src="/assets/images/salonpas-bg.jpg" alt="" class="profile-wid-img" />
         </div>
      </div>
      <div class="pt-4 mb-4 mb-lg-3 pb-lg-4">
         <div class="row g-4">
            <div class="col-auto">
               <div class="profile-user position-relative d-inline-block mx-auto">
                  @if (!empty($user->employee->avatar))
                     <div id="avatar-user">
                        <img src="{{ asset('storage/avatars/' . $user->employee->avatar) }}"
                           class="show-image rounded-circle avatar-xl img-thumbnail user-profile-image"
                           alt="user-profile-image">
                     </div>
                  @else
                     <div id="avatar-user">
                        <img src="{{ asset('storage/avatars/user.jpg') }}"
                           class="rounded-circle avatar-xl img-thumbnail user-profile-image"
                           alt="user-profile-image">
                     </div>
                  @endif
                  <div class="avatar-xs p-0 rounded-circle profile-photo-edit">
                     <input onchange="avatarValidation(this);" id="profile-img-file-input" type="file"
                        name="image" class="image profile-img-file-input"
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
            <div class="col">
               <div class="p-2">
                  <h3 class="text-white mb-1">{{ $user->employee->fullname }}</h3>
                  <p class="text-white-75">{{ $user->employee->email }}</p>
                  <div class="hstack text-white-50 gap-1">
                     <div class="me-2"><i class="ri-map-pin-user-line me-1 text-white-75 fs-16 align-middle"></i>
                        {{ $user->employee->area->name }}
                     </div>
                     <div><i class="ri-building-line me-1 text-white-75 fs-16 align-middle"></i>
                        {{ $user->employee->department->name }}
                     </div>
                  </div>
                  <div class="hstack text-white-50 gap-1">
                     <div class="me-2">
                        @if (!empty($user->employee->level->nama))
                           <i class="ri-contacts-book-2-line me-1 text-white-75 fs-16 align-middle"></i>
                           {{ $user->employee->level->nama }}
                        @endif
                     </div>
                     <div>
                        @if (!empty($user->employee->position->nama))
                           <i class="ri-contacts-book-line me-1 text-white-75 fs-16 align-middle"></i>
                           {{ $user->employee->position->nama }}
                        @endif
                     </div>
                  </div>
               </div>
            </div>
            <!--end col-->
            <div class="col-12 col-lg-auto order-last order-lg-0">
               <div class="row text text-white-50 text-center">
                  <div class="col-lg-6 col-4">
                     <div class="p-2">
                        <!-- <h4 class="text-white mb-1">{{ $user->employee->nik }}</h4>
                                 <p class="fs-14 mb-0">NIK</p> -->
                     </div>
                  </div>
               </div>
            </div>
            <!--end col-->
         </div>
         <!--end row-->
      </div>

      <div class="row">
         <div class="col-lg-12">
            <div>
               <div class="d-flex">
                  <!-- Nav tabs -->
                  @include('partials.navbar2')
               </div>
               <!-- Navbar -->
               <div class="row pt-4">
                  <div class="col-12">
                     <div class="card">
                        <div class="card-body">
                           <form method="POST" action="{{ route('attendance-permit.profile-store') }}" id="attendance-overtime">
                              @csrf
                                <div class="card shadow-sm mb-4">
                                    <!-- HEADER -->
                                    <div class="card-header bg-white border-bottom">
                                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                            <h4 class="mb-0">
                                                <i class="ri-time-line me-1 text-primary"></i>
                                                Formulir Pengajuan Lembur
                                            </h4>

                                            <a href="{{ route('attendance-permit.profile-index') }}"
                                                class="btn btn-primary btn-label waves-effect waves-light">
                                                <i class="ri-arrow-left-fill label-icon align-middle fs-16 me-2"></i>
                                                Back
                                            </a>
                                        </div>
                                    </div>

                                    <div class="card-body">
                                        <!-- DETAIL JAM KERJA -->
                                        <div class="row mb-4">
                                            <div class="col-md-3">
                                                <div class="card border shadow-sm h-100">
                                                    <div class="card-body text-center">
                                                        <i class="ri-group-line fs-2 text-primary"></i>
                                                        <h6 class="mt-2 mb-1">Group Shift</h6>
                                                        <p class="text-muted mb-0">{{$groupName}}</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="card border shadow-sm h-100">
                                                    <div class="card-body text-center">
                                                        {{-- <i class="ri-calendar-line fs-2 text-info"></i> --}}
                                                        {{-- <i class="ri-time-line fs-2 text-info"></i> --}}
                                                        {{-- <i class="ri-briefcase-line fs-2 text-info"></i> --}}
                                                        <i class="ri-file-list-3-line fs-2 text-info"></i>
                                                        <h6 class="mt-2 mb-1">Jam Kerja</h6>
                                                        <p class="text-muted mb-0">{{$workName}}</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="card border shadow-sm h-100">
                                                    <div class="card-body text-center">
                                                        <i class="ri-login-box-line fs-2 text-success"></i>
                                                        <h6 class="mt-2 mb-1">Jam Masuk</h6>
                                                        <p class="text-muted mb-0">{{$workIn}}</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="card border shadow-sm h-100">
                                                    <div class="card-body text-center">
                                                        <i class="ri-logout-box-r-line fs-2 text-danger"></i>
                                                        <h6 class="mt-2 mb-1">Jam Keluar</h6>
                                                        <p class="text-muted mb-0">{{$workOut}}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- TANGGAL PENGAJUAN -->
                                        {{-- <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-semibold">Tanggal Pengajuan</label>
                                                <div class="input-group">
                                                    <input type="text" name="propose_date"
                                                        class="form-control bulan propose_date"
                                                        placeholder="Pilih Tanggal">
                                                    <span class="input-group-text">
                                                        <i class="ri-calendar-event-line"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div> --}}

                                        <!-- TANGGAL MULAI & SELESAI -->
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-semibold">Tanggal Mulai</label>
                                                <div class="input-group">
                                                    <input type="text" name="start_date"
                                                        class="form-control bulan start_date"
                                                        placeholder="Pilih Tanggal">
                                                    <span class="input-group-text">
                                                        <i class="ri-calendar-event-line"></i>
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-semibold">Tanggal Selesai</label>
                                                <div class="input-group">
                                                    <input type="text" name="end_date"
                                                        class="form-control bulan end_date"
                                                        placeholder="Pilih Tanggal">
                                                    <span class="input-group-text">
                                                        <i class="ri-calendar-event-line"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- JAM MULAI & SELESAI -->
                                        <div class="row align-items-end">
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label fw-semibold">Jam Mulai</label>
                                                <div class="input-group">
                                                    <input type="text" name="start_time"
                                                        class="form-control start_time"
                                                        placeholder="Pilih Jam">
                                                    <span class="input-group-text">
                                                        <i class="ri-time-line"></i>
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <label class="form-label fw-semibold">Jam Selesai</label>
                                                <div class="input-group">
                                                    <input type="text" name="end_time"
                                                        class="form-control end_time"
                                                        placeholder="Pilih Jam">
                                                    <span class="input-group-text">
                                                        <i class="ri-time-line"></i>
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <label class="form-label fw-semibold">Total Jam Kerja</label>
                                                <div class="input-group">
                                                    <input type="text" id="total" class="form-control bg-light" readonly>
                                                    <span class="input-group-text">
                                                        <i class="ri-calculator-line"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- ALASAN -->
                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label fw-semibold">Alasan Lembur</label>
                                                <textarea name="reason" rows="4" class="form-control"
                                                    placeholder="Masukkan alasan pengajuan lembur"></textarea>
                                            </div>
                                        </div>

                                    </div>

                                    <!-- FOOTER -->
                                    <div class="card-footer bg-white text-end">
                                        <button class="btn btn-success px-4">
                                            <i class="ri-send-plane-fill me-1"></i>
                                            Ajukan Lembur
                                        </button>
                                    </div>
                                </div>
                           </form>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <!--end col-->
      </div>
      <!--end row-->
   </div><!-- container-fluid -->
@endsection

@section('script')
   <!-- Datatables -->
   <script src="{{ url('') }}/assets/libs/Datatables/DataTables-1.13.1/js/jquery.dataTables.min.js"></script>
   <script src="{{ url('') }}/assets/libs/Datatables/DataTables-1.13.1/js/dataTables.bootstrap5.min.js"></script>
   <script src="{{ url('') }}/assets/libs/Datatables/Responsive-2.4.0/js/dataTables.responsive.min.js"></script>
   <script src="{{ url('') }}/assets/js/pages/datatables.init.js"></script>
   <!-- profile-setting init js -->
   <script src="{{ url('') }}/assets/js/pages/profile-setting.init.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.js"></script>
   <!-- Sweetalert -->
   <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection

@section('javascript')
<script>
$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    function initPlugins(context = document) {
        // DATE
        $(context).find('.start_date').each(function () {
            if (!this._flatpickr) {
                flatpickr(this, {
                    dateFormat: "Y-m-d"
                });
            }
        });

        $(context).find('.end_date').each(function () {
            if (!this._flatpickr) {
                flatpickr(this, {
                    dateFormat: "Y-m-d"
                });
            }
        });

        // TIME ONLY
        $(context).find('.start_time').each(function () {
            if (!this._flatpickr) {
                flatpickr(this, {
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: "H:i",
                    time_24hr: true
                });
            }
        });

        $(context).find('.end_time').each(function () {
            if (!this._flatpickr) {
                flatpickr(this, {
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: "H:i",
                    time_24hr: true
                });
            }
        });
    }
    initPlugins();
    // ================= TANGGAL PENGAJUAN OTOMATIS =================
    // let today = new Date().toISOString().split('T')[0];
    //     $('input[name="propose_date"]').val(today);
    //     // jika pakai flatpickr
    //     if ($('.propose_date')[0]._flatpickr) {
    //         $('.propose_date')[0]._flatpickr.setDate(today);
    //     }
    //     // ================= FLATPICKR JAM =================
    //     $('.start_time, .end_time').flatpickr({
    //         enableTime: true,
    //         noCalendar: true,
    //         dateFormat: "H:i",
    //         time_24hr: true
    //     });
        // ================= HITUNG TOTAL JAM =================
        function calculateTotalHours() {
            let start = $('.start_time').val();
            let end = $('.end_time').val();
            if (start && end) {
                let startTime = new Date("2000-01-01 " + start);
                let endTime = new Date("2000-01-01 " + end);
                let diffMs = endTime - startTime;
                if (diffMs < 0) {
                    diffMs += 24 * 60 * 60 * 1000; // jika lewat tengah malam
                }
                let hours = Math.floor(diffMs / (1000 * 60 * 60));
                let minutes = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));
                $('#total').val(hours + ' Jam ' + minutes + ' Menit');
            } else {
                $('#total').val('');
            }
        }
        $('.start_time, .end_time').on('change', function () {
            calculateTotalHours();
        });

    function validateForm() {
        let type = $('#type').val();
        let startDate = $('[name="start_date"]').val();
        let endDate = $('[name="end_date"]').val();
        let startTime = $('[name="start_time"]').val();
        let endTime = $('[name="end_time"]').val();
        let reason = $('[name="reason"]').val();
        let attachment = $('[name="attachment"]').val();

        // alasan wajib
        if (!reason) {
            Swal.fire('Warning', 'Alasan wajib diisi', 'warning');
            return false;
        }

        // start date wajib
        if (!startDate) {
            Swal.fire('Warning', 'Tanggal mulai wajib diisi', 'warning');
            return false;
        }

        // jika end date kosong = start date
        if (!endDate) {
            endDate = startDate;
        }

        // validasi jam
        if (startTime && endTime) {
            // kalau tanggal sama
            if (startDate === endDate) {
                if (endTime < startTime) {
                    Swal.fire(
                        'Warning',
                        'Jam selesai tidak boleh kurang dari jam mulai pada hari yang sama',
                        'warning'
                    );
                    return false;
                }
            }
        }

        return true;
    }

    $('#attendance-overtime').on('submit', function (e) {
        e.preventDefault();
        if (!validateForm()) return;
        let formData = new FormData(this);
        Swal.fire({
            title: 'Yakin?',
            text: "Data permit akan dikirim",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('overtime.store') }}",
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (res) {
                        Swal.fire('Success', res.message, 'success')
                            .then(() => window.location.href = "{{ route('overtime.index') }}");
                    },
                    error: function (xhr) {
                        let msg = xhr.responseJSON?.message ?? 'Terjadi kesalahan';
                        Swal.fire('Error', msg, 'error');
                    }
                });
            }
        });
    });
});
</script>
@endsection
