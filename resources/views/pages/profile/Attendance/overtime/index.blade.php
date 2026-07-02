@extends(Auth::user()->can('emp.menu') ? 'layouts.general' : 'layouts.master')
{{-- @extends('layouts.master') --}}

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
            <div>
               <div class="d-flex">
                    @if (!Auth::user()->can('emp.menu'))
                        @include('partials.navbar2')
                @endif
               </div>
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
                              <a class="nav-link py-3 active" id="tab-rule" data-bs-toggle="tab" href="#pill-claim-overtime" role="tab">
                                 <i class="ri-survey-line me-1 align-bottom"></i> Lembur
                              </a>
                           </li>
                           <li class="nav-item">
                              <a class="nav-link py-3" id="tab-benefit" data-bs-toggle="tab" href="#pill-claim-history" role="tab">
                                 <i class="ri-survey-line me-1 align-bottom"></i> History Pengajuan Lembur
                              </a>
                           </li>
                            @if ($isApprover)
                           <li class="nav-item">
                                <a class="nav-link py-3" id="tab-benefit" data-bs-toggle="tab" href="#pill-approval" role="tab">
                                <i class="bi bi-clipboard-check me-1 align-bottom"></i> Persetujuan Lembur
                                </a>
                           </li>
                           <li class="nav-item">
                                <a class="nav-link py-3" id="tab-benefit" data-bs-toggle="tab" href="#pill-history" role="tab">
                                <i class="bi bi-clipboard-check me-1 align-bottom"></i> Riwayat
                                </a>
                           </li>
                            @endif
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="pill-claim-overtime" role="tabpanel">
                                <div class="card-body">
                                    <table class="table table-striped dt-responsive nowrap w-100" id="table-claim-overtime">
                                        <thead>
                                            <tr>
                                                <th class="text-center">No</th>
                                                <th class="text-center">Tanggal Lembur</th>
                                                <th class="text-center">Jam Kerja</th>
                                                <th class="text-center">Jam Mulai dan Selesai</th>
                                                <th class="text-center">Total Jam Lembur</th>
                                                <th class="text-center">Alasan</th>
                                                <th class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade " id="pill-claim-history" role="tabpanel">
                                <div class="card-body">
                                    <table class="table table-striped dt-responsive nowrap w-100" id="table-claim-history">
                                        <thead>
                                            <tr>
                                                <th class="text-center">No</th>
                                                <th class="text-center">Tanggal Lembur</th>
                                                <th class="text-center">Tanggal Claim</th>
                                                <th class="text-center">Total Jam Lembur</th>
                                                <th class="text-center">Kesepakatan Jam Lembur (hari libur)</th>
                                                <th class="text-center">Alasan Penolakan</th>
                                                <th class="text-center">Catatan HRD</th>
                                                <th class="text-center">Mengetahui HRD</th>
                                                <th class="text-center">Status</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="pill-approval" role="tabpanel">
                                <div class="card-body">
                                    <div class="d-flex justify-content-start mb-3 gap-2">
                                        <button id="btn-bulk-mode" type="button" class="btn btn-primary">
                                            <i class="ri-checkbox-multiple-line"></i> Bulk Approve
                                        </button>

                                        <button id="btn-approve-selected" class="btn btn-success d-none">
                                            <i class="ri-check-double-line"></i> Approve Selected
                                        </button>

                                        <button id="btn-cancel-bulk" type="button" class="btn btn-danger d-none">
                                            <i class="ri-close-line"></i> Cancel
                                        </button>
                                    </div>
                                    <table class="table table-striped dt-responsive nowrap w-100" id="table-approval-overtime">
                                        <thead>
                                            <tr>
                                                <th width="5%"><input type="checkbox" id="check-all"></th>
                                                <th class="text-center">NIK</th>
                                                <th class="text-center">Nama</th>
                                                <th class="text-center">Position</th>
                                                <th class="text-center">Area</th>
                                                <th class="text-center">Department</th>
                                                <th class="text-center">Jam Kerja</th>
                                                <th class="text-center">Tanggal Lembur</th>
                                                <th class="text-center">Jam Mulai dan Selesai</th>
                                                <th class="text-center">Kesepakatan Jam Lembur</th>
                                                <th class="text-center">Total Jam Lembur</th>
                                                <th class="text-center">Alasan</th>
                                                <th class="text-center">Status</th>
                                                <th class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                            <!-- ================= HISTORY (APPROVED / REJECTED) ================= -->
                            <div class="tab-pane fade" id="pill-history" role="tabpanel">
                                <div class="card-body">
                                    <table class="table table-striped dt-responsive nowrap w-100" id="table-approval-history">
                                        <thead>
                                            <tr>
                                                <th class="text-center">NO</th>
                                                <th class="text-center">NIK</th>
                                                <th class="text-center">Nama</th>
                                                <th class="text-center">Position</th>
                                                <th class="text-center">Area</th>
                                                <th class="text-center">Department</th>
                                                <th class="text-center">jam Kerja</th>
                                                <th class="text-center">Tanggal Lembur</th>
                                                <th class="text-center">Jam Mulai dan Selesai</th>
                                                <th class="text-center">Kesepakatan Jam Lembur</th>
                                                <th class="text-center">Total Jam Lembur</th>
                                                <th class="text-center">Alasan</th>
                                                <th class="text-center">Status</th>
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
    $(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    let hash = window.location.hash;
    if (hash) {
        $('.nav-link').removeClass('active');
        $('.tab-pane').removeClass('show active');

        $(`a[href="${hash}"]`).addClass('active');
        $(hash).addClass('show active');
    }

    $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        let target = $(e.target).attr("href"); // ambil id tab
        if (target === '#pill-claim-overtime') {
            tableClaimOvertime.ajax.reload();
            loadApprovalCount();
        }
        if (target === '#pill-claim-history') {
            tableClaimHistory.ajax.reload();
            loadApprovalCount();
        }
        if (target === '#pill-approval') {
            tableApprovalOvertime.ajax.reload();
            loadApprovalCount();
        }
        if (target === '#pill-history') {
            tableHistoryOvertime.ajax.reload();
            loadApprovalCount();
        }
    });
    function loadApprovalCount() {
        $.get("{{ route('claim-overtime.pending-count') }}", function(res) {
            let html = '';
            if (res.total > 0) {
                html = `You have <b>${res.total}</b> Claim Overtime request(s) awaiting your approval`;
            } else {
                html = `No pending claim overtime requests 🎉`;
            }
            $('#approval-text').html(html);
        });
    }
    loadApprovalCount();
    // ============================= CLAIM OVERTIME SECTION =============================
    let tableClaimOvertime = $('#table-claim-overtime').DataTable({
        processing: true,
        responsive: false,
        serverSide: false,
        scrollX: true,
        ajax: "{{ route('claim-overtime.data-my') }}",
        columns: [
            { data: 'DT_RowIndex', className: 'text-center', orderable: false, searchable: false},
            { data: 'overtime_date', className: 'text-center' },
            {
                data: null,
                className: "text-center",
                render: function(data){
                    return `
                        <div class="d-flex flex-column">
                            <small><b>${data.workhour ?? '-'}</b></small>
                            <small class="">
                                ${data.work_in ?? '-'} - ${data.work_out ?? '-'}
                            </small>
                        </div>
                    `;
                }
            },
            { data: 'work_in_and_out', className: 'text-center' },
            { data: 'total_work', className: 'text-center' },
            { data: 'reason', className: 'text-center'},
            { data: 'action', className: 'text-center', orderable: false, searchable: false }
        ]
    });
    let tableClaimHistory = $('#table-claim-history').DataTable({
        processing: true,
        responsive: false,
        serverSide: false,
        scrollX: true,
        ajax: "{{ route('claim-overtime.data-my-history') }}",
        columns: [
            { data: 'DT_RowIndex', className: 'text-center', orderable: false, searchable: false},
            { data: 'overtime_date', className: 'text-center' },
            { data: 'claim_date', className: 'text-center' },
            { data: 'total_work', className: 'text-center' },
            { data: 'agreed_work', className: 'text-center' },
            { data: 'reason', className: 'text-center'},
            { data: 'hrd_note', className: 'text-center'},
            {
                data: 'hrd_knowledge',
                className: "text-center",
                render: function (data) {
                    if (data == 1) {
                        return `<i class="bi bi-check-circle-fill text-success fs-5"></i>`;
                    } else {
                        return `<i class="bi bi-x-circle-fill text-danger fs-5"></i>`;
                    }
                }
            },
            {
                data: 'status',
                className: 'text-center',
                render: function (data) {
                    const map = {
                        waiting: '<span class="badge bg-warning">Waiting</span>',
                        approved: '<span class="badge bg-success">Approved</span>',
                        rejected: '<span class="badge bg-danger">Rejected</span>',
                    };
                    return map[data] ?? data;
                }
            },
        ]
    });

    $(document).on('click', '.btn-claim', function () {
        let id = $(this).data('id');
        let isHoliday = $(this).data('holiday');
        let agreedWorkHtml = '';
        // tampilkan hanya jika holiday
        if (isHoliday == 1) {
            agreedWorkHtml = `
                <label class="form-label">Agreed Work Start</label>
                <input type="time" id="agreed_work_start" class="form-control mb-3">

                <label class="form-label">Agreed Work End</label>
                <input type="time" id="agreed_work_end" class="form-control mb-3">
            `;
        }
        Swal.fire({
            title: 'Claim Overtime',
            html: `
                <div class="text-start">
                    ${agreedWorkHtml}
                    <label class="form-label">Reason</label>
                    <textarea
                        id="reason"
                        class="form-control"
                        rows="3"
                        placeholder="Masukkan alasan lembur...">
                    </textarea>

                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Ajukan',
            preConfirm: () => {
                let reason = $('#reason').val();
                let agreedWorkStart = $('#agreed_work_start').val();
                let agreedWorkEnd = $('#agreed_work_end').val();
                if (!reason) {
                    Swal.showValidationMessage('Reason wajib diisi');
                    return false;
                }
                // validasi khusus holiday
                if (isHoliday == 1) {
                    if (!agreedWorkStart || !agreedWorkEnd) {
                        Swal.showValidationMessage(
                            'Agreed work wajib diisi'
                        );

                        return false;
                    }
                }
                return {
                    agreed_work_start: agreedWorkStart,
                    agreed_work_end: agreedWorkEnd,
                    reason: reason
                };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('claim-overtime.claimOvertimeNow') }}",
                    type: 'POST',
                    data: {
                        id: id,
                        agreed_work_start: result.value.agreed_work_start,
                        agreed_work_end: result.value.agreed_work_end,
                        reason: result.value.reason,
                    },
                    success: function (res) {
                        Swal.fire(
                            'Success',
                            res.message,
                            'success'
                        );
                        tableClaimOvertime.ajax.reload();
                        loadApprovalCount();
                    },
                    error: function (xhr) {
                        let message = 'Gagal Mengajukan';
                        if (xhr.responseJSON?.message) {
                            message = xhr.responseJSON.message;
                        }
                        Swal.fire(
                            'Error',
                            message,
                            'error'
                        );
                    }
                });
            }
        });
    });
    // ============================= TABLE APPROVAL =============================
    let bulkMode = false;
    let tableApprovalOvertime = $('#table-approval-overtime').DataTable({
        processing: true,
        responsive: false,
        serverSide: false,
        scrollX: true,
        ajax: "{{ route('claim-overtime.data-approval') }}",
        columns: [
            {
                data: 'id',
                className: "text-center",
                visible: false,
                orderable: false,
                searchable: false,
                render: function(data){
                    return bulkMode
                        ? `<input type="checkbox" class="row-checkbox" value="${data}">`
                        : '';
                }
            },
            { data: 'nik', className:"text-center" },
            { data: 'employee_name', className:"text-center" },
            { data: 'position', className:"text-center" },
            { data: 'area', className:"text-center"},
            { data: 'department', className:"text-center" },
            {
                data: null,
                className: "text-center",
                render: function(data){
                    return `
                        <div class="d-flex flex-column">
                            <small><b>${data.workhour ?? '-'}</b></small>
                            <small>
                                ${data.work_in ?? '-'} - ${data.work_out ?? '-'}
                            </small>
                        </div>
                    `;
                }
            },
            { data: 'overtime_date', className:"text-center" },
            { data: 'actual_work', className:"text-center" },
            { data: 'agreed_work', className:"text-center" },
            { data: 'total_work', className:"text-center" },
            { data: 'reason', className:"text-center" },
            {
                data: 'status',
                className: 'text-center',
                render: function (data) {
                    const map = {
                        waiting: '<span class="badge bg-warning">Waiting</span>',
                        approved: '<span class="badge bg-success">Approved</span>',
                        rejected: '<span class="badge bg-danger">Rejected</span>',
                    };
                    return map[data] ?? data;
                }
            },
            {
                data: 'action',
                className: 'text-center',
                render: function(data){
                    return bulkMode ? '' : data;
                }
            }
        ]
    });

    // BULK MODE
    $('#btn-bulk-mode').on('click', function () {
        bulkMode = true;
        tableApprovalOvertime.column(0).visible(true);
        tableApprovalOvertime.column(12).visible(false);
        tableApprovalOvertime.ajax.reload();

        $('#btn-bulk-mode').addClass('d-none');
        $('#btn-approve-selected, #btn-cancel-bulk').removeClass('d-none');
    });

    $('#btn-cancel-bulk').on('click', function () {
        bulkMode = false;

        $('#check-all').prop('checked', false);
        $('.row-checkbox').prop('checked', false);

        tableApprovalOvertime.column(0).visible(false);
        tableApprovalOvertime.column(12).visible(true);
        tableApprovalOvertime.ajax.reload();

        $('#btn-bulk-mode').removeClass('d-none');
        $('#btn-approve-selected, #btn-cancel-bulk').addClass('d-none');
    });
    $(document).on('change', '#check-all', function () {
        $('.row-checkbox').prop('checked', $(this).is(':checked'));
    });

    // ================================== BULK APPROVE AND REJECT SELECTED ===================================
    $('#btn-approve-selected').on('click', function () {
        let ids = [];
        $('.row-checkbox:checked').each(function () {
            ids.push($(this).val());
        });
        if (ids.length === 0) {
            Swal.fire('Warning', 'Pilih minimal satu data', 'warning');
            return;
        }
        Swal.fire({
            title: 'Approve Selected?',
            text: `${ids.length} data akan diapprove`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Approve'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('claim-overtime.bulk-process-approval') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        ids: ids,
                        action: 'approved'
                    },
                    success: function (res) {
                        Swal.fire('Success', res.message, 'success');
                        tableApprovalOvertime.ajax.reload();
                        loadApprovalCount();
                        $('#check-all').prop('checked', false);
                        $('.row-checkbox').prop('checked', false);
                    },
                    error: function (xhr) {
                        Swal.fire('Error', xhr.responseJSON.message ?? 'Terjadi kesalahan', 'error');
                    }
                });
            }
        });
    });

    // ============================= SINGLE APPROVE AND REJECT ==============================
    $(document).on('click', '.btn-approve', function () {
        let id = $(this).data('id');

        Swal.fire({
            title: 'Approve?',
            text: 'Setujui Claim Lembur ini?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Approve'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('claim-overtime.single-process-approval') }}",
                    type: 'POST',
                    data: {
                        id: id,
                        action : 'approved'
                    },
                    success: function (res) {
                        Swal.fire('Success', res.message, 'success');
                        tableApprovalOvertime.ajax.reload();
                        loadApprovalCount();
                    },
                    error: function (xhr) {
                        console.log(xhr.responseText);
                        Swal.fire('Error', 'Gagal approve', 'error');
                    }
                });
            }
        });
    });
    // REJECT
    $(document).on('click','.btn-reject', function () {
        let id = $(this).data('id');

        Swal.fire({
            title: 'Alasan Reject',
            input: 'text',
            inputPlaceholder: 'Masukkan alasan reject...',
            showCancelButton: true,
            confirmButtonText: 'Reject',
            preConfirm: (reason) => {
                if (!reason) {
                    Swal.showValidationMessage('Reason tidak boleh kosong');
                    return false;
                }

                return $.ajax({
                    url: "{{ route('claim-overtime.single-process-approval') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: id,
                        action: 'rejected',
                        reason: reason
                    }
                }).then(response => {
                    Swal.fire('Berhasil', response.message, 'success');
                    tableApprovalOvertime.ajax.reload();
                    loadApprovalCount();
                }).catch(xhr => {
                    Swal.fire('Error', xhr.responseJSON.message ?? 'Terjadi kesalahan', 'error');
                });
            }
        });
    });

    let tableHistoryOvertime = $('#table-approval-history').DataTable({
        processing: true,
        responsive: false,
        serverSide: false,
        scrollX: true,
        ajax: "{{ route('claim-overtime.data-history-overtime') }}",
        columns: [
            { data: 'DT_RowIndex', className: 'text-center', orderable: false, searchable: false},
            { data: 'nik', className:"text-center" },
            { data: 'employee_name', className:"text-center" },
            { data: 'position', className:"text-center" },
            { data: 'area', className:"text-center"},
            { data: 'department', className:"text-center" },
            {
                data: null,
                className: "text-center",
                render: function(data){
                    return `
                        <div class="d-flex flex-column">
                            <small><b>${data.workhour ?? '-'}</b></small>
                            <small>
                                ${data.work_in ?? '-'} - ${data.work_out ?? '-'}
                            </small>
                        </div>
                    `;
                }
            },
            { data: 'overtime_date', className:"text-center" },
            { data: 'actual_work', className:"text-center" },
            { data: 'agreed_work', className:"text-center" },
            { data: 'total_work', className:"text-center" },
            { data: 'reason', className:"text-center" },
            {
                data: 'status',
                className: 'text-center',
                render: function (data) {
                    const map = {
                        waiting: '<span class="badge bg-warning">Waiting</span>',
                        approved: '<span class="badge bg-success">Approved</span>',
                        rejected: '<span class="badge bg-danger">Rejected</span>',
                    };
                    return map[data] ?? data;
                }
            },
        ]
    });
});
</script>
<script>
    $(document).ready(function () {

    let hash = window.location.hash;

    if (hash) {
        $('.nav-link').removeClass('active');
        $('.tab-pane').removeClass('show active');

        $(`a[href="${hash}"]`).addClass('active');
        $(hash).addClass('show active');
    }
});
</script>
@endsection
