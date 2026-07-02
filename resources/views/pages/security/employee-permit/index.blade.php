@extends('layouts.master')
@section('link')
<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- Datatables-->
<link href="/assets/libs/Datatables/DataTables-1.13.1/css/dataTables.bootstrap5.css" rel="stylesheet" type="text/css" />
<link href="/assets/libs/Datatables/Buttons-2.3.3/css/buttons.dataTables.css" rel="stylesheet" type="text/css" />
<link href="/assets/libs/Datatables/Responsive-2.4.0/css/responsive.bootstrap.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css">
@endsection
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Attendance</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Menu</a></li>
                    <li class="breadcrumb-item active">Attendance</li>
                </ol>
            </div>

        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <!-- TAB -->
            <ul class="nav nav-tabs mb-3 px-3 pt-3" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#tab-attendance-records">
                        Attendance Records
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link " data-bs-toggle="tab" href="#tab-late">
                        Terlambat
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#tab-permit">
                        Izin Karyawan
                    </a>
                </li>
            </ul>
            <!-- CONTENT -->
            <div class="tab-content p-3">
                <div class="tab-pane fade show active" id="tab-attendance-records">
                    <div class="row align-items-end justify-content-between mb-3">
                        <!-- FILTER DATE -->
                        <div class="col-md-3">
                            <label class="form-label">Filter Date</label>
                            <div class="input-group">
                                <input type="text" name="filter_date"
                                    class="form-control filter_date"
                                    placeholder="Pilih Tanggal">
                                <span class="input-group-text">
                                    <i class="ri-calendar-event-line"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <table class="table table-striped dt-responsive nowrap w-100" id="attendance-records">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                    <th class="text-center">NIK</th>
                                    <th class="text-center">Name</th>
                                    <th class="text-center">Position</th>
                                    <th class="text-center">Area</th>
                                    <th class="text-center">Department</th>
                                    <th class="text-center">Group WorkHours</th>
                                    <th class="text-center">Time Check In</th>
                                    <th class="text-center">Time Check Out</th>
                                    {{-- <th class="text-center">Status Entrance</th> --}}
                                    <th class="text-center">Status Presence</th>
                            </tr>
                        </thead>
                    </table>
                </div>

                <div class="tab-pane fade" id="tab-late">
                    <div class="row align-items-end justify-content-between mb-3">
                        <!-- TAB BUTTON -->
                        <div class="d-flex gap-2 mb-3 col-md-4" role="tablist">
                            <a class="btn btn-outline-primary active"
                            data-bs-toggle="tab"
                            href="#tab-late-records"
                            role="tab">
                                <i class="ri-survey-line me-1"></i>
                                Attendance Late
                            </a>
                            <a class="btn btn-outline-primary"
                            data-bs-toggle="tab"
                            href="#tab-late-history"
                            role="tab">
                                <i class="bi bi-clipboard-check me-1"></i>
                                Late History
                            </a>
                        </div>
                        <!-- FILTER -->
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Terlambat</label>
                            <div class="input-group">
                                <input type="text" name="filter_date_late"
                                    class="form-control filter_date_late"
                                    placeholder="Pilih Tanggal">
                                <span class="input-group-text">
                                    <i class="ri-calendar-event-line"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <!-- TAB CONTENT -->
                    <div class="tab-content">
                        <!-- RECORDS -->
                        <div class="tab-pane fade show active" id="tab-late-records">
                            <table class="table table-striped dt-responsive nowrap w-100" id="late-records">
                                <thead>
                                    <tr>
                                        <th class="text-center">No</th>
                                        <th class="text-center">NIK</th>
                                        <th class="text-center">Posisi</th>
                                        <th class="text-center">Name</th>
                                        <th class="text-center">Area</th>
                                        <th class="text-center">Department</th>
                                        <th class="text-center">Group WorkHours</th>
                                        <th class="text-center">Jam Masuk Terlambat</th>
                                        <th class="text-center">Alasan Terlambat</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>

                        <!-- LATE HISTORY -->
                        <div class="tab-pane fade" id="tab-late-history">
                            <table class="table table-striped dt-responsive nowrap w-100" id="late-history">
                                <thead>
                                    <tr>
                                        <th class="text-center">No</th>
                                        <th class="text-center">NIK</th>
                                        <th class="text-center">Name</th>
                                        <th class="text-center">Posisi</th>
                                        <th class="text-center">Area</th>
                                        <th class="text-center">Department</th>
                                        <th class="text-center">Jam Aktual Masuk (Employee)</th>
                                        <th class="text-center">Jam Aktual Masuk (Security)</th>
                                        <th class="text-center">Alasan Terlambat</th>
                                        <th class="text-center">Mengetahui Security</th>
                                        <th class="text-center">Mengetahui Atasan</th>
                                        <th class="text-center">Mengetahui HRD</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade " id="tab-permit">
                    <div class="row align-items-end justify-content-between mb-3">
                        <!-- FILTER TYPE -->
                        <div class="col-md-3">
                            <label class="form-label">Permit Type</label>
                            <select id="filter_type" class="form-select select2">
                                <option value="">All Type</option>
                                <option value="earlyout">Pulang Cepat</option>
                                <option value="temporary_out">Keluar Sementara</option>
                                <option value="other">Lainnya</option>
                            </select>
                        </div>
                        <!-- FILTER DATE -->
                        <div class="col-md-3">
                            <label class="form-label">Propose Date</label>
                            <div class="input-group">
                                <input type="text" name="filter_date_permit"
                                    class="form-control filter_date_permit"
                                    placeholder="Pilih Tanggal">
                                <span class="input-group-text">
                                    <i class="ri-calendar-event-line"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                    <table class="table table-striped dt-responsive nowrap w-100" id="attendance-permit">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NIK</th>
                                <th>Nama</th>
                                <th>Position</th>
                                <th>Area</th>
                                <th>Department</th>
                                <th>Jam Kerja</th>
                                <th>Tipe Izin</th>
                                <th>Waktu Izin</th>
                                <th>Persetujuan Ijin</th>
                                <th>Mengetahui HRD</th>
                                <th>jam Aktual</th>
                                <th>Security</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- <div class="modal fade" id="modalDetail" tabindex="-1">
    <div class="modal-dialog modal-sm ">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Jam Aktual</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body" id="detailContent">
                Loading...
            </div>
            <div class="modal-footer">
                <button class="btn btn-success" id="btn-know">Mengetahui</button>
            </div>
        </div>
    </div>
</div> --}}

<!--Modal staticbackdrop-->
<div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <img src="{{ url('') }}/assets/images/loading.gif" style="width:120px;height:120px">
                <div class="mt-4">
                    <h4 class="mb-3">Please wait...</h4>
                    <h4 class="mb-3">Do not leave this page</h4>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<!-- Datatables -->
<script src="/assets/libs/Datatables/DataTables-1.13.1/js/jquery.dataTables.min.js"></script>
<script src="/assets/libs/Datatables/DataTables-1.13.1/js/dataTables.bootstrap5.min.js"></script>
<script src="/assets/libs/Datatables/Responsive-2.4.0/js/dataTables.responsive.min.js"></script>
<script src="/assets/js/pages/datatables.init.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js"></script>
@endsection

@section('javascript')
<script>
    const KNOWPERMIT_URL = "{{ route('employee-permit.security-permit-knowledge', ':id') }}";
    const KNOWLATE_URL = "{{ route('employee-permit.security-late-knowledge', ':id') }}";
</script>
<script>
$(document).ready(function () {
    // CSRF SETUP
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    function initPlugins(context = document) {
        // ================= SELECT2 =================
        $(context).find('.select2').each(function () {
            if ($(this).hasClass("select2-hidden-accessible")) {
                $(this).select2('destroy');
            }
            $(this).select2({ width: '100%' });
        });
        $(context).find('.filter_date_permit').each(function () {
            if (!this._flatpickr) {
                flatpickr(this, {
                    dateFormat: "Y-m-d",
                    altInput: true,
                    altFormat: "d M Y",
                    allowInput: true,
                    defaultDate: "today"
                });
            }
        });
        $(context).find('.filter_date_late').each(function () {
            if (!this._flatpickr) {
                flatpickr(this, {
                    dateFormat: "Y-m-d",
                    altInput: true,
                    altFormat: "d M Y",
                    allowInput: true,
                    defaultDate: "today"
                });
            }
        });
        $(context).find('.filter_date')
        .each(function () {
            if (!this._flatpickr) {
                flatpickr(this, {
                    allowInput: true,
                    dateFormat: "Y-m-d"
                });
            }
        });
    }
    initPlugins();
    $('#filter_type, .filter_date_permit').on('change', function () {
        tablePermit.ajax.reload();
    });
    $('.filter_date_late').on('change', function () {
        tableLate.ajax.reload();
        tableHistory.ajax.reload();
    });
    $('.filter_date').on('change', function () {
        tableAttendance.ajax.reload();
    });
    $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        let target = $(e.target).attr("href"); // ambil id tab
        if (target === '#tab-attendance-records') {
            tableAttendance.ajax.reload();
        }
        if (target === '#tab-late') {
            tableLate.ajax.reload();
            tableHistory.ajax.reload();
        }
        if (target === '#tab-permit') {
            tablePermit.ajax.reload();
        }
        if (target === '#tab-late-records') {
            tableLate.ajax.reload();
        }
        if (target === '#tab-late-history') {
            tableHistory.ajax.reload();
        }
    });

    let tableAttendance = $('#attendance-records').DataTable({
        responsive: true,
        autoWidth: false,
        stateSave: true,
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('employee-permit.security-records') }}",
            data: function (d) {
                d.date = $('.filter_date').val();
                // d.location = selectedLocation;
            }
        },
        order: [[1, 'desc']],
        columns: [
            {data: 'DT_RowIndex', className: "text-center", orderable: false, searchable: false},
            {data: 'nik', className: "text-center"},
            {data: 'employee_name',className: "text-center"},
            {data: 'position_name',className: "text-center"},
            {data: 'area_name',className: "text-center"},
            {data: 'department_name',className: "text-center"},
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
            {
                data: null,
                className: "text-center",
                render: function(data) {
                    return `
                        <div>

                            <div class="fw-semibold fs-6">
                                ${data.check_in ?? '-'}
                            </div>

                            <div class="mt-1">

                                ${getTimezoneText(data.latlong_ci)}

                            </div>

                        </div>
                    `;
                }
            },
            {
                data: null,
                className: "text-center",
                render: function(data) {
                    return `
                        <div>

                            <div class="fw-semibold fs-6">
                                ${data.check_out ?? '-'}
                            </div>

                            <div class="mt-1">

                                ${getTimezoneText(data.latlong_co)}

                            </div>

                        </div>
                    `;
                }
            },
            {
                data: null,
                className: "text-center",
                render: function(data){

                    function badge(text, type){
                        return `
                            <span class="badge bg-${type} mb-1">
                                ${text}
                            </span>
                        `;
                    }

                    let badges = [];

                    // ================= CHECK IN =================

                    if(data.status_check_in){

                        if(
                            data.status_check_in === 'late' ||
                            data.status_check_in === 'overtime'
                        ){

                            badges.push(
                                badge(
                                    data.status_check_in === 'late'
                                        ? 'Check In - Late'
                                        : 'Check In - Overtime',
                                    data.status_check_in === 'late'
                                        ? 'danger'
                                        : 'info'
                                )
                            );

                        }

                    }

                    // ================= CHECK OUT =================

                    if(data.status_check_out){

                        if(
                            data.status_check_out === 'early_leave' ||
                            data.status_check_out === 'overtime'
                        ){

                            badges.push(
                                badge(
                                    data.status_check_out === 'early'
                                        ? 'Check Out - Early'
                                        : 'Check Out - Overtime',
                                    data.status_check_out === 'early'
                                        ? 'warning'
                                        : 'info'
                                )
                            );

                        }

                    }

                    // ================= ATTENDANCE STATUS =================

                    const attendanceColor = {

                        present : 'success',

                        sick : 'danger',

                        holiday : 'secondary',

                        overtime : 'info',

                        waiting : 'dark',

                        leave : 'warning',

                        absent : 'danger'

                    };

                    badges.push(

                        badge(

                            data.attendance_status ?? '-',

                            attendanceColor[
                                data.attendance_status
                            ] ?? 'secondary'

                        )

                    );

                    // ================= BUSINESS TRIP =================

                    if(data.business_trip_type){

                        badges.push(

                            badge(

                                data.business_trip_type === 'domestic'
                                    ? 'Business Trip Domestic'
                                    : 'Business Trip Overseas',

                                'primary'

                            )

                        );

                    }

                    return `
                        <div class="
                            d-flex
                            flex-column
                            align-items-center
                        ">
                            ${badges.join('')}
                        </div>
                    `;

                }
            },
        ]
    });
    // =============================== TABLE EMPLOYEE PERMIT==============================
    const tablePermit = $('#attendance-permit').DataTable({
        processing: true,
        responsive: false,
        serverSide: false,
        scrollX: true,
        ajax: {
            url : "{{ route('employee-permit.security-index') }}",
            data: function (d) {
            d.filter_date_permit = $('.filter_date_permit').val();
            d.filter_type = $('#filter_type').val();
            }
        },
        columns: [
            {data: 'DT_RowIndex', className: "text-center", orderable: false, searchable: false},
            {data: 'nik', className: "text-center"},
            {data: 'employee_name', className: "text-center"},
            {data: 'position', className: "text-center"},
            {data: 'area', className: "text-center"},
            {data: 'department', className: "text-center"},
            {data: 'workhour', className: "text-center"},
            {data: 'type', className: "text-center"},
            {data: 'time_permit', className: "text-center"},
            {
            data: null,
            className: "text-center",
                render: function (data) {
                    function badge(text, type) {
                        return `<span class="badge bg-${type}">${text}</span>`;
                    }
                    let status = data.status ?? '-';
                    let approver = data.approved_by_name ?? '-';

                    let statusColor = {
                        'approved': 'success',
                        'rejected': 'danger',
                        'waiting': 'warning',
                    };
                    return `
                        <div class="d-flex flex-column align-items-center">
                            ${badge(status, statusColor[status] ?? 'secondary')}
                            <small class=" mt-1">by: ${approver}</small>
                        </div>
                    `;
                }
            },
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
            {data: 'actual_time', className: "text-center"},
            {data: 'security', className: "text-center"},
            {data: 'action', orderable: false, searchable: false, className: "text-center"},
        ]
    });

    $(document).on('click', '.securityPermit-btn', function () {
        let id = $(this).data('id');
        let url = KNOWPERMIT_URL.replace(':id', id);

        // ambil input manual (kalau ada)
        let actualIn  = $('#actual_time_in').val();
        let actualOut = $('#actual_time_out').val();

        let now = new Date();
        let jam = now.toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
        // fallback ke waktu sekarang kalau kosong
        if (!actualIn)  actualIn  = jam;
        if (!actualOut) actualOut = jam;

        Swal.fire({
            title: 'Konfirmasi',
            text: `Jam yang digunakan ${actualIn} - ${actualOut}, lanjutkan?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Simpan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post(url, {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    id: id,
                    actual_time_in: actualIn,
                    actual_time_out: actualOut
                }, function (res) {
                    // $('#modalDetail').modal('hide');
                    $('#attendance-permit').DataTable().ajax.reload();

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

    // ============================== TABLE EMPLOYEE LATE ==================================
    let tableLate = $('#late-records').DataTable({
        processing: true,
        responsive: false,
        serverSide: false,
        scrollX: true,
        ajax: {
            url: "{{ route('employee-permit.security-late') }}",
            data: function (d) {
                d.filter_date_late = $('.filter_date_late').val();
                // tableLate.ajax.reload();
            }
        },
        columns: [
            {data: 'DT_RowIndex', className: "text-center", orderable: false, searchable: false},
            {data: 'nik', className: "text-center"},
            {data: 'employee_name',className: "text-center"},
            {data: 'position_name',className: "text-center"},
            {data: 'area_name',className: "text-center"},
            {data: 'department_name',className: "text-center"},
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
            {
            data: null,
            className: "text-start",
                render: function (data) {
                    function badge(text, type) {
                        return `<span class="badge bg-${type}">${text}</span>`;
                    }
                    let checkInStatus = badge('-', 'secondary');
                    if (data.status_check_in === 'late') {
                        checkInStatus = badge('Terlambat', 'danger');
                    } else if (data.status_check_in === 'on_time') {
                        checkInStatus = badge('Tepat Waktu', 'success');
                    }
                    return `
                        <div>
                            <small>
                                <b>Masuk:</b> ${data.check_in ?? '-'} - ${checkInStatus}
                            </small>
                        </div>
                    `;
                }
            },
            {data: 'reason',className: "text-center"},
            {data: 'action', orderable: false, searchable: false, className: "text-center"},
        ]
    });

    let tableHistory = $('#late-history').DataTable({
        processing: true,
        responsive: false,
        serverSide: false,
        scrollX: true,
        ajax: {
            url: "{{ route('employee-permit.security-late-histories') }}",
            data: function (d) {
                d.filter_date_late = $('.filter_date_late').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', className: "text-center", orderable: false, searchable: false},
            { data: 'nik', className: "text-center"},
            { data: 'employee_name',className: "text-center"},
            { data: 'position_name',className: "text-center"},
            { data: 'area_name',className: "text-center"},
            { data: 'department_name',className: "text-center"},
            { data: 'actual_in_employee',className: "text-center"},
            { data: 'actual_in_security',className: "text-center"},
            { data: 'reason',className: "text-center"},
            { data: 'security', className: "text-center"},
            { data: 'head', className: "text-center"},
            { data: 'hrd', className: "text-center"},
        ]
    });


    // ===================================== SECURITY KNOWLEDGE ========================================
    $(document).on('click', '.securityLate-btn', function () {
        let id = $(this).data('id');
        let url = KNOWLATE_URL.replace(':id', id);

        // ambil input manual (kalau ada)
        let actualIn  = $('#actual_time_in').val();

        let now = new Date();
        let jam = now.toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit',
        });
        // fallback ke waktu sekarang kalau kosong
        if (!actualIn)  actualIn  = jam;

        Swal.fire({
            title: 'Konfirmasi',
            text: `Jam yang digunakan ${actualIn}, lanjutkan?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Simpan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post(url, {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    id: id,
                    actual_time_in: actualIn,
                }, function (res) {
                    // $('#modalDetail').modal('hide');
                    $('#late-records').DataTable().ajax.reload();

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
<script>
    function getTimezoneInfo(timezone) {

    switch (timezone) {

        case 'Asia/Jakarta':
            return {
                label: 'WIB',
                offset: '+07:00'
            };

        case 'Asia/Makassar':
            return {
                label: 'WITA',
                offset: '+08:00'
            };

        case 'Asia/Jayapura':
            return {
                label: 'WIT',
                offset: '+09:00'
            };

        default:
            return {
                label: 'UTC',
                offset: '+00:00'
            };
    }
}
function getTimezoneText(latlong) {

    if (!latlong || latlong === '-') {
        return '-';
    }

    try {

        let coords = latlong
            .split(',')
            .map(item => item.trim());

        let lat = parseFloat(coords[0]);

        let lng = parseFloat(coords[1]);

        if (isNaN(lat) || isNaN(lng)) {
            return 'Invalid Coordinate';
        }

        let timezone = tzlookup(lat, lng);

        let city = timezone
            .split('/')[1]
            ?.replace(/_/g, ' ');

        let tzInfo = getTimezoneInfo(timezone);

        return `
            <span class="small">

                <i class="ri-map-pin-time-line"></i>

                ${city}
                (${tzInfo.label} UTC${tzInfo.offset})

            </span>
        `;

    } catch (e) {

        console.error(e);

        return `
            <span class="text-muted small">
                Unknown Timezone
            </span>
        `;
    }
}
</script>
@endsection
