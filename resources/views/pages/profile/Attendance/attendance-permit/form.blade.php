{{-- @extends('layouts.master') --}}
@extends(Auth::user()->can('emp.menu') ? 'layouts.general' : 'layouts.master')



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
    @if (!Auth::user()->can('emp.menu'))
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
      @endif

      <div class="row">
         <div class="col-lg-12">
               <div class="d-flex">
                    @if (!Auth::user()->can('emp.menu'))
                        @include('partials.navbar2')
                    @endif
               </div>
               <!-- Navbar -->
               <div class="row pt-4">
                  <div class="col-12">
                     <div class="card">
                        <div class="card-body">
                           <form method="POST" action="{{ route('attendance-permit.profile-store') }}" id="attendance-permit">
                              @csrf
                                <div class="card mb-3">
                                        <div class="card-body">
                                            <div class="card-header border-2">
                                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                                    <h4 class="mb-0">
                                                        Formulir Pengajuan Izin
                                                    </h4>
                                                    <a href="{{ route('attendance-permit.profile-index') }}"
                                                    class="btn btn-primary btn-label waves-effect waves-light">
                                                        <i class="ri-arrow-left-fill label-icon align-middle fs-16 me-2"></i>
                                                        Back
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="row mt-3">
                                                <div class="mb-3 col-md-6">
                                                    <label>Tipe Izin</label>
                                                    <select id="type" name="type" class="form-select" id="department_id">
                                                        <option value="earlyout">Pulang Cepat</option>
                                                        <option value="temporary_out">Keluar Sementara</option>
                                                        {{-- <option value="pribadi">Izin Pribadi</option> --}}
                                                        <option value="sick">Izin Dokter</option>
                                                        <option value="other">Tertentu atau Lainnya</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 field-start-date">
                                                    <label>Tanggal Mulai</label>
                                                    <div class="input-group">
                                                        <input type="text" name="start_date" class="form-control bulan start_date" placeholder="Pilih Tanggal">
                                                        <span class="input-group-text"><i class="ri-calendar-event-line"></i></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 field-end-date">
                                                    <label>Tanggal Selesai</label>
                                                    <div class="input-group">
                                                        <input type="text" name="end_date" class="form-control bulan end_date" placeholder="Pilih Tanggal">
                                                        <span class="input-group-text"><i class="ri-calendar-event-line"></i></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mt-3">
                                                <div class="col-md-6 field-end-time">
                                                    <label>jam Keluar</label>
                                                    <div class="input-group">
                                                        <input type="text" name="end_time" class="form-control bulan end_time" placeholder="Pilih Jam">
                                                        <span class="input-group-text"><i class="ri-calendar-event-line"></i></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 field-start-time">
                                                    <label>jam Masuk</label>
                                                    <div class="input-group">
                                                        <input type="text" name="start_time" class="form-control bulan start_time" placeholder="Pilih Jam">
                                                        <span class="input-group-text"><i class="ri-calendar-event-line"></i></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="card-header"><h5>Lampiran</h5></div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label>Upload Foto</label>
                                                <input type="file" name="attachment" class="form-control">
                                            </div>
                                            <div>
                                                <label>Alasan</label>
                                                <textarea name="reason" class="form-control" placeholder="Alasan Harus Benar Adanya"></textarea>
                                            </div>
                                        </div>
                                <div class="text-end mt-3 card-footer">
                                    <button class="btn btn-success">Propose Permit</button>
                                </div>
                            </div>
                           </form>
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
    function handleTypeChange() {
        let type = $('#type').val();
        // hide semua dulu
        $('.field-start-date').hide();
        $('.field-end-date').hide();
        $('.field-start-time').hide();
        $('.field-end-time').hide();
        // tampilkan sesuai tipe
        if (type === 'earlyout') {
            $('.field-start-date').show();
            $('.field-end-time').show();
        }
        if (type === 'temporary_out') {
            $('.field-start-date').show();
            $('.field-start-time').show();
            $('.field-end-time').show();
        }
        if (type === 'pribadi') {
            $('.field-start-date').show();
            $('.field-start-time').show();
            $('.field-end-time').show();
        }
        if (type === 'sick') {
            $('.field-start-date').show();
            $('.field-end-date').show();
        }
        if (type === 'other') {
            $('.field-start-date').show();
            $('.field-end-date').show();
            $('.field-start-time').show();
            $('.field-end-time').show();
        }
    }
    function clearHiddenFields() {
        $('.field-start-date:hidden input').val('');
        $('.field-end-date:hidden input').val('');
        $('.field-start-time:hidden input').val('');
        $('.field-end-time:hidden input').val('');
    }
    initPlugins();
    clearHiddenFields();
    handleTypeChange(); // initial
    $('#type').on('change', function () {
        handleTypeChange();
    });

    function validateForm() {
        let type = $('#type').val();
        let startDate = $('[name="start_date"]').val();
        let endDate = $('[name="end_date"]').val();
        let startTime = $('[name="start_time"]').val();
        let endTime = $('[name="end_time"]').val();
        let reason = $('[name="reason"]').val();
        let attachment = $('[name="attachment"]').val();
        // console.log(endDate);

        // alasan wajib semua
        if (!reason) {
            Swal.fire('Warning', 'Alasan wajib diisi', 'warning');
            return false;
        }
        // lampiran wajib semua
        // if (!attachment) {
        //     Swal.fire('Warning', 'Lampiran wajib diupload', 'warning');
        //     return false;
        // }
        // ================= TYPE VALIDATION =================
        if (type === 'earlyout') {
            if (!startDate || !endTime) {
                Swal.fire('Warning', 'Tanggal & Jam Keluar wajib diisi', 'warning');
                return false;
            }
        }
        if (type === 'temporary_out') {
            if (!startDate || !startTime || !endTime) {
                Swal.fire('Warning', 'Tanggal, Jam Masuk, dan Jam Keluar wajib diisi', 'warning');
                return false;
            }
        }
        if (type === 'pribadi') {
            if (!startDate || !startTime || !endTime) {
                Swal.fire('Warning', 'Tanggal, Jam Masuk, dan Jam Keluar wajib diisi', 'warning');
                return false;
            }
        }
        if (type === 'sick') {
            if (!startDate || !endDate || !attachment) {
                Swal.fire('Warning', 'Tanggal Mulai, Tanggal Selesai serta Lampiran wajib diisi', 'warning');
                return false;
            }
        }
        if (type === 'other') {
            if (!startDate || !endDate || !startTime || !endTime || !attachment ) {
                Swal.fire('Warning', 'Semua field wajib diisi untuk tipe ini', 'warning');
                return false;
            }
        }
        return true;
    }

    $('#attendance-permit').on('submit', function (e) {
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
                    url: "{{ route('attendance-permit.profile-store') }}",
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (res) {
                        Swal.fire('Success', res.message, 'success')
                            .then(() => window.location.href = "{{ route('attendance-permit.profile-index') }}");
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
