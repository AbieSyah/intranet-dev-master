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
                     <div class="row">
                        @if ($isApprover)
                           <div class="col-md-6 col-12">
                              <div class="card border-start border-primary border-4 shadow-sm">
                                 <div class="card-body">
                                    <div class="d-flex mb-3">
                                       <div class="flex-shrink-0">
                                          <i class="ri-file-search-fill text-primary fs-1"></i>
                                       </div>
                                       <div class="flex-grow-1 ms-3">
                                          <h5 class="mb-1 fw-bold">Approval Queue: Action Pending</h5>
                                            <p class="mb-0 medium" id="approval-text"></p>
                                            {{-- <p>Data Terlambat Harus DiKetahui Hingga HRD agar dapat terhitung di Hari Tersebut</p> --}}
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        @endif
                     </div>

                     <div class="card">
                        <ul class="nav nav-tabs nav-tabs-custom nav-success" role="tablist">
                           <li class="nav-item">
                              <a class="nav-link py-3 active" id="tab-rule" data-bs-toggle="tab"
                                 href="#pill-process" role="tab">
                                 <i class="ri-survey-line me-1 align-bottom"></i> Data Terlambat
                              </a>
                           </li>
                            @if ($isApprover)
                           <li class="nav-item">
                                <a class="nav-link py-3" id="tab-benefit" data-bs-toggle="tab" href="#pill-approval"
                                role="tab">
                                <i class="bi bi-clipboard-check me-1 align-bottom"></i> Mengetahui Terlambat Bawahan
                                </a>
                           </li>
                           <li class="nav-item">
                                <a class="nav-link py-3" id="tab-benefit" data-bs-toggle="tab" href="#pill-history"
                                role="tab">
                                <i class="bi bi-clipboard-check me-1 align-bottom"></i> Riwayat Terlambat Bawahan
                                </a>
                           </li>
                            @endif
                        </ul>
                        <div class="tab-content">
                            <!-- ================= MY PERMIT ================= -->
                            <div class="tab-pane fade show active" id="pill-process" role="tabpanel">
                                <div class="card-body">
                                    <table class="table table-striped dt-responsive nowrap w-100" id="table-my-late">
                                        <thead>
                                            <tr>
                                                <th class="text-center">No</th>
                                                <th class="text-center">Tanggal</th>
                                                <th class="text-center">Jam Aktual Masuk(Employee)</th>
                                                <th class="text-center">Jam Aktual Masuk(Security)</th>
                                                <th class="text-center">Alasan Terlambat</th>
                                                <th class="text-center">Diketahui Security</th>
                                                <th class="text-center">Diketahui Atasan</th>
                                                <th class="text-center">Diketahui HRD</th>
                                            </tr>
                                        </thead>
                                    </table>

                                </div>
                            </div>

                            <!-- ================= KNOWLEDGE ================= -->
                            <div class="tab-pane fade" id="pill-approval" role="tabpanel">
                                <div class="card-body">

                                    <table class="table table-striped dt-responsive nowrap w-100" id="table-head-knowledge">
                                        <thead>
                                            <tr>
                                                <th class="text-center">NIK</th>
                                                <th class="text-center">Nama</th>
                                                <th class="text-center">Area</th>
                                                <th class="text-center">Department</th>
                                                <th class="text-center">Position</th>
                                                <th class="text-center">Date</th>
                                                <th class="text-center">Jam Kerja</th>
                                                <th class="text-center">Jam Masuk(Karyawan)</th>
                                                <th class="text-center">Jam Aktual(Security)</th>
                                                <th class="text-center">Alasan Terlambat</th>
                                                <th class="text-center">Diketahui Security</th>
                                                <th class="text-center">Diketahui Head</th>
                                                <th class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                    </table>

                                </div>
                            </div>

                            <!-- ================= HISTORIES ================= -->
                            <div class="tab-pane fade" id="pill-history" role="tabpanel">
                                <div class="card-body">

                                    <table class="table table-striped dt-responsive nowrap w-100" id="table-head-history">
                                        <thead>
                                            <tr>
                                                <th class="text-center">NIK</th>
                                                <th class="text-center">Nama</th>
                                                <th class="text-center">Area</th>
                                                <th class="text-center">Department</th>
                                                <th class="text-center">Position</th>
                                                <th class="text-center">Jam Kerja</th>
                                                <th class="text-center">Date</th>
                                                <th class="text-center">Jam Masuk(Karyawan)</th>
                                                <th class="text-center">Jam Aktual(Security)</th>
                                                <th class="text-center">Alasan Terlambat</th>
                                                <th class="text-center">Diketahui Security</th>
                                                <th class="text-center">Diketahui Atasan</th>
                                                <th class="text-center">Diketahui HRD</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
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
   <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
   <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
   <!-- Toastr Notifications-->
   <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
   <!-- Select2 -->
   <script src="{{ url('') }}/assets/libs/adminlte/select2/js/4.1.0/select2.min.js"></script>
@endsection

@section('javascript')
<script>
    const KNOWLEDGE = "{{ route('attendance-late.knowledge', ':id') }}";
</script>
<script>
    $(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    loadApprovalCount();
    $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        let target = $(e.target).attr("href"); // ambil id tab
        if (target === '#pill-process') {
            $('#table-my-late').DataTable().ajax.reload();
            loadApprovalCount();
        }
        if (target === '#pill-approval') {
            $('#table-head-knowledge').DataTable().ajax.reload();
            loadApprovalCount();
        }
        if (target === '#pill-history') {
            $('#table-head-history').DataTable().ajax.reload();
            loadApprovalCount();
        }
    });
    // ==================== Count ===========================
    function loadApprovalCount() {
        $.get("{{ route('attendance-late.pending-count') }}", function(res) {
            let html = '';
            if (res.total > 0) {
                html = `You have <b>${res.total}</b> Employee Late awaiting your Confirmation`;
            } else {
                html = `No pending Confirmation 🎉`;
            }
            $('#approval-text').html(html);
        });
    }

    // ======================= TABLE MY EMPLOYEE =====================
    let tableHistory = $('#table-my-late').DataTable({
        processing: true,
        responsive: false,
        serverSide: false,
        scrollX: true,
        ajax: {
            url: "{{ route('attendance-late.data-my') }}",
            data: function (d) {
                d.date = $('#filter_date_history').val();
            }
        },
        order: [[1, 'desc']],
        columns: [
            {data: 'DT_RowIndex', className: "text-center", orderable: false, searchable: false},
            {data: 'date',className: "text-center"},
            {data: 'actual_in_employee',className: "text-center"},
            {data: 'actual_in_security',className: "text-center"},
            {data: 'reason',className: "text-center"},
            {
            data: 'security', className: "text-center"},
            {
            data: 'head', className: "text-center"},
            {
            data: 'hrd', className: "text-center"},
        ]
    });
    // =============================== TABLE HEAD KNOWLEDGE =============================
    $('#table-head-knowledge').DataTable({
        processing: true,
        responsive: false,
        serverSide: false,
        scrollX: true,
        ajax: "{{ route('attendance-late.data-head-knowledge') }}",
        columns: [
            { data: 'nik', className:"text-center" },
            { data: 'employee_name', className:"text-center" },
            { data: 'area', className:"text-center"},
            { data: 'department', className:"text-center" },
            { data: 'position', className:"text-center" },
            { data: 'date', className:"text-center" },
            {
                data: null,
                className: "text-center",
                render: function(data){
                    return `
                        <div class="d-flex flex-column">
                            <small><b>${data.group_workhours ?? '-'}</b></small>
                            <small class="">
                                ${data.work_in ?? '-'} - ${data.work_out ?? '-'}
                            </small>
                        </div>
                    `;
                }
            },
            { data: 'actual_in_employee', className:"text-center" },
            { data: 'actual_in_security', className:"text-center" },
            { data: 'reason', className:"text-center" },
            { data: 'security', className:"text-center" },
            { data: 'head', className:"text-center" },
            { data: 'action', orderable: false, searchable: false, className: 'text-center'}
        ]
    });
    $('#table-head-history').DataTable({
        processing: true,
        responsive: false,
        serverSide: false,
        scrollX: true,
        ajax: "{{ route('attendance-late.data-head-history') }}",
        columns: [
            { data: 'nik', className:"text-center" },
            { data: 'employee_name', className:"text-center" },
            { data: 'area', className:"text-center"},
            { data: 'department', className:"text-center" },
            { data: 'position', className:"text-center" },
            { data: 'date', className:"text-center" },
            {
                data: null,
                className: "text-center",
                render: function(data){
                    return `
                        <div class="d-flex flex-column">
                            <small><b>${data.group_workhours ?? '-'}</b></small>
                            <small class="">
                                ${data.work_in ?? '-'} - ${data.work_out ?? '-'}
                            </small>
                        </div>
                    `;
                }
            },
            { data: 'actual_in_employee', className:"text-center" },
            { data: 'actual_in_security', className:"text-center" },
            { data: 'reason', className:"text-center" },
            { data: 'security', className:"text-center" },
            { data: 'head', className:"text-center" },
            { data: 'hrd', className:"text-center" },
        ]
    });
    $(document).on('click', '.knowledge-btn', function () {
        let id = $(this).data('id');
        let url = KNOWLEDGE.replace(':id', id);

        Swal.fire({
            title: 'Konfirmasi',
            text: `Konfirmasi Karyawan Yang Terlambat?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Simpan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post(url, {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    id: id,
                }, function (res) {
                    $('#table-head-knowledge').DataTable().ajax.reload();

                    Swal.fire('Berhasil', res.message ?? 'Berhasil diproses', 'success');
                }).fail(function (xhr) {
                    Swal.fire(
                        'Error',
                        xhr.responseJSON?.message ?? 'Terjadi kesalahan',
                        'error'
                    );
                });
            }
        });
    });
});
</script>
@endsection
