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
   <!-- Select2-->
   <link rel="stylesheet" href="{{ url('') }}/assets/libs/adminlte/select2/css/4.1.0/select2.min.css">
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
         <div class="col-12">
                @if (!Auth::user()->can('emp.menu'))
                    <div class="mb-3">
                        @include('partials.navbar2')
                    </div>
                @endif
                <div class="card-body">
                    <form method="POST" action="{{ route('leave-request.profile-store') }}" id="leaveRequest">
                        @csrf
                            <div class="card mb-3 shadow-sm border-info">
                                <div class="card-header bg-info-subtle">
                                    <h5 class="mb-0">
                                        <i class="ri-calendar-check-line me-1"></i>
                                        Informasi Saldo Cuti
                                    </h5>
                                </div>

                                <div class="card-body">
                                    <div class="row g-3">
                                        {{-- Tahun ini --}}
                                        @if($currentBalance)
                                        @php
                                            $daysLeftCurrent = \Carbon\Carbon::now()->diffInDays($currentBalance->valid_to, false);
                                        @endphp
                                        <div class="col-md-6">
                                            <div class="border rounded p-3 h-100">
                                                <h6 class="fw-bold text-primary">
                                                    <i class="ri-calendar-line"></i>
                                                    Saldo {{ now()->year }}
                                                </h6>
                                                <p class="mb-1">
                                                    <i class="ri-wallet-3-line text-success"></i>
                                                    <strong> {{ $currentBalance->remaining_days }}</strong> hari
                                                </p>
                                                <p class="mb-1">
                                                    Berlaku sampai:
                                                    <strong>{{ \Carbon\Carbon::parse($currentBalance->valid_to)->format('d M Y') }}</strong>
                                                </p>
                                                <small class="text-muted">
                                                    Tersisa {{ $daysLeftCurrent }} hari lagi
                                                </small>
                                            </div>
                                        </div>
                                        @endif
                                        {{-- Tahun lalu --}}
                                        @if($lastBalance)
                                        @php
                                            $daysLeftLast = \Carbon\Carbon::now()->diffInDays($lastBalance->valid_to, false);
                                        @endphp
                                        <div class="col-md-6">
                                            <div class="border rounded p-3 h-100">
                                                <h6 class="fw-bold text-warning">
                                                    <i class="ri-history-line"></i>
                                                    Saldo {{ now()->year - 1 }}
                                                </h6>

                                                <p class="mb-1">
                                                    <i class="ri-wallet-3-line text-success"></i>
                                                    <strong>{{ $lastBalance->remaining_days }}</strong> hari
                                                </p>

                                                <p class="mb-1">
                                                    Berlaku sampai:
                                                    <strong>{{ \Carbon\Carbon::parse($lastBalance->valid_to)->format('d M Y') }}</strong>
                                                </p>

                                                <small class="text-muted">
                                                    Tersisa {{ $daysLeftLast }} hari lagi
                                                </small>
                                            </div>
                                        </div>
                                        @endif

                                    </div>

                                    {{-- <div class="alert alert-light mt-3 mb-0"> --}}
                                    <div class="alert mt-3 mb-0">
                                        <i class="ri-information-line"></i>
                                        Sistem akan otomatis menggunakan saldo yang masa berlakunya paling dekat terlebih dahulu.
                                    </div>
                                </div>
                            </div>
                            <!-- HEADER -->
                            <div class="card mb-3">
                                <div class="card-header border-2">
                                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                            <h4 class="mb-0">
                                                Formulir Pengajuan Cuti
                                            </h4>
                                            <a href="{{ route('leave-request.profile-index') }}"
                                            class="btn btn-primary btn-label waves-effect waves-light">
                                                <i class="ri-arrow-left-fill label-icon align-middle fs-16 me-2"></i>
                                                Kembali
                                            </a>
                                        </div>
                                    </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label>Tipe Cuti</label>
                                            <select id="leave_type" name="leave_type" class="form-select">
                                                <option value="pribadi" selected>Pribadi</option>
                                                <option value="normatif">Normatif</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- ========================= PRIBADI ======================= -->
                            <div id="section-pribadi" class="card mb-3" style="display:none;">
                                <div class="card-body">

                                    <div class="row ">
                                        <div class="col-md-6">
                                            <label>Dimulai Tanggal</label>
                                            <div class="input-group">
                                                <input type="text" name="start_date_pribadi" class="form-control bulan start_date_pribadi" placeholder="Pilih Tanggal di Mulai">
                                                <span class="input-group-text"><i class="ri-calendar-event-line"></i></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label>Berakhir Pada Tanggal</label>
                                            <div class="input-group">
                                                <input type="text" name="end_date_pribadi" class="form-control bulan end_date_pribadi" placeholder="Pilih Tanggal Berakhir">
                                                <span class="input-group-text"><i class="ri-calendar-event-line"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-md-4">
                                            <label>Saldo Hari Terambil</label>
                                            <input type="text" id="taken_days" class="form-control" readonly placeholder="Otomatis Terisi ketika memilih Tanggal di Mulai dan Berakhir">
                                        </div>

                                        <div class="col-md-8">
                                            <label>Keterangan Tanggal</label>
                                            <div id="leave_notes" class="border p-2 rounded" style="min-height: 38px;">
                                                -
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- ========================= NORMATIF ======================= -->
                            <div id="section-normatif" class="card mb-3" style="display:none;">
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label>Cuti Tipe Normatif</label>
                                        <select class="form-select select2" id="type" name="type">
                                            <option value="all">All type</option>
                                            @foreach ($leaves as $leave )
                                                <option value="{{ $leave->id }}"
                                                    data-days="{{ $leave->number_of_days }}">
                                                    {{ $leave->type }} - {{ $leave->description }} - {{ $leave->number_of_days }} days
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <label>Dimulai Pada</label>
                                            <div class="input-group">
                                                <input type="text" name="start_date_normatif" class="form-control bulan start_date_normatif" placeholder="Pilih Tanggal di Mulai">
                                                <span class="input-group-text"><i class="ri-calendar-event-line"></i></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label>Berakhir Pada Tanggal</label>
                                            <div class="input-group">
                                                <input type="text" name="end_date_normatif" class="form-control bulan end_date_normatif" readonly placeholder="Otomatis Terisi Ketika Memilih Tipe Cuti Normatif dan Tanggal di Mulai ">
                                                <span class="input-group-text"><i class="ri-calendar-event-line"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-md-12">
                                            <label>Keterangan Tanggal</label>
                                            <div id="leave_normatif_notes" class="border p-2 rounded" style="min-height: 38px;">
                                                -
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- ================= LAMPIRAN ================= -->
                            <div class="card mb-3">
                                <div class="card-header"><h5>Lampiran</h5></div>
                                <div class="card-body">
                                    <div class="mb-2">
                                        <label>Upload Foto</label>
                                        <input type="file" name="attachment" class="form-control">
                                        <div class="mt-1">
                                        <small class="text-muted "><span class="">* </span>Optional untuk Pribadi | Wajib untuk Normatif</small>
                                        </div>
                                    </div>
                                    <div>
                                        <label>Keterangan</label>
                                        <textarea class="form-control" name="notes" placeholder="optional"></textarea>
                                    </div>
                                </div>
                            </div>
                            <!-- ================= APPROVAL ================= -->
                            <div id="section-approval" class="card mb-3">
                                <div class="card-header"><h5>Persetujuan Yang di Butuhkan</h5></div>
                                <div class="card-body">
                                    <div class="row">
                                        @for ($i = 1; $i <= 8; $i++)
                                            <div class="col-md-4 mb-3">
                                                <label>Approval {{ $i }}</label>
                                                <input type="text"
                                                    class="form-control text-muted"
                                                    value="{{ $approvers['approve_'.$i] ?? '-' }}"
                                                    readonly>
                                            </div>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                        <div class="card-footer text-end mt-3">
                            <button class="btn btn-success">Ajukan Cuti</button>
                        </div>
                    </form>
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
   <!-- Select2 -->
   <script src="{{ url('') }}/assets/libs/adminlte/select2/js/4.1.0/select2.min.js"></script>
@endsection

@section('javascript')
<script>
    $(document).ready(function () {
        $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    //================== PLUGINS ==================
    function initPlugins(context = document) {
        // SELECT2
        $(context).find('.select2').each(function () {if ($(this).hasClass("select2-hidden-accessible")) {$(this).select2('destroy');}$(this).select2({width: '100%',placeholder: "Select option"});});
        // FLATPICKR DATE
        $(context).find('.start_date_pribadi').each(function () {if (!this._flatpickr) {flatpickr(this, {allowInput: true,dateFormat: "Y-m-d"});}});
        $(context).find('.end_date_pribadi').each(function () {if (!this._flatpickr) {flatpickr(this, {allowInput: true,dateFormat: "Y-m-d"});}});
        $(context).find('.start_date_normatif').each(function () {if (!this._flatpickr) {flatpickr(this, {allowInput: true,dateFormat: "Y-m-d"});}});
    }
    initPlugins();
    //================== MODE VIEW HANDLER ==================
    function handleView() {
        let type = $('#leave_type').val();
        // RESET
        $('#section-pribadi').hide();
        $('#section-normatif').show();

        initPlugins('#section-pribadi');
        initPlugins('#section-normatif');

        // TYPE (GLOBAL SECTION)
        if (type === 'pribadi') {
            $('#section-pribadi').show();
            $('#section-normatif').hide();

        } else {
            $('#section-pribadi').hide();
            $('#section-normatif').show();
        }
    }
    handleView();
    $('#leave_type').on('change', function () {
        handleView();
    });

    // ============================= CALCULATE LEAVE DAYS ======================
    function calculateLeaveDays() {
        let start = $('.start_date_pribadi').val();
        let end = $('.end_date_pribadi').val();

        if (!start || !end) return;

        $.ajax({
            url: "{{ route('leave-request.profile-calculate-days') }}",
            method: 'GET',
            data: {
                start_date: start,
                end_date: end
            },
            success: function (res) {
                $('#taken_days').val(res.days);

                if (res.excluded.length > 0) {
                    let notes = '<div class="row row-cols-3 row-cols-md-4 g-1">';
                    res.excluded.forEach((e, index) => {
                        notes += `
                            <div class="col">
                                <small>• ${e.date} (${e.type})</small>
                            </div>
                        `;
                    });
                    notes += '</div>';
                    $('#leave_notes').html(notes);
                } else {
                    $('#leave_notes').html('-');
                }
            }
        });
    }
    $('.start_date_pribadi, .end_date_pribadi').on('change', function () {
        calculateLeaveDays();
    });

    // ============================ CALCULATE NORMATIF ==========================
    function calculateNormatif() {
        let start = $('.start_date_normatif').val();
        let selected = $('#type option:selected');
        let days = parseInt(selected.data('days'));
        let typeId = selected.val();

        if (!start || !days || !typeId) return;

        $.ajax({
            url: "{{ route('leave-request.profile-calculate-normatif') }}",
            method: "GET",
            data: {
                start_date: start,
                leave_setting_id: typeId
            },
            success: function (res) {
                $('.end_date_normatif').val(res.end_date);

                if (res.excluded.length > 0) {
                    let notes = '<div class="row row-cols-3 row-cols-md-6 g-1">';

                    res.excluded.forEach((e, index) => {
                        notes += `
                            <div class="col">
                                <small>• ${e.date} (${e.type})</small>
                            </div>
                        `;
                    });

                    notes += '</div>';

                    $('#leave_normatif_notes').html(notes);
                } else {
                    $('#leave_normatif_notes').html('-');
                }
            },
            error: function(xhr) {
                console.log(xhr.responseText);
            }
        });
    }
    $('#type').on('change', calculateNormatif);
    $('.start_date_normatif').on('change', calculateNormatif);

        //============================== AJAX SUBMIT ===============================
        $('#leaveRequest').on('submit', function (e) {
        e.preventDefault();
        // if (!validateForm()) return;
        let formData = new FormData(this);
        Swal.fire({
            title: 'Yakin?',
            text: "Pengajuan Cuti akan dikirim",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('leave-request.profile-store') }}",
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (res) {
                        Swal.fire('Success', res.message, 'success')
                            .then(() => window.location.href = "{{ route('leave-request.profile-index') }}");
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
