@extends('layouts.master')
@section('link')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    .flatpickr-calendar{
        z-index: 999999 !important;
    }
</style>
<!-- Datatables-->
<link href="/assets/libs/Datatables/DataTables-1.13.1/css/dataTables.bootstrap5.css" rel="stylesheet" type="text/css" />
<link href="/assets/libs/Datatables/Buttons-2.3.3/css/buttons.dataTables.css" rel="stylesheet" type="text/css" />
<link href="/assets/libs/Datatables/Responsive-2.4.0/css/responsive.bootstrap.css" rel="stylesheet" type="text/css" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Employee Attendance</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Master</a></li>
                    <li class="breadcrumb-item active">Employee Attendance</li>
                </ol>
            </div>

        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">

                <!-- TAB HEADER -->
                <ul class="nav nav-tabs nav-tabs-custom nav-success mb-3" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#tab-attendance" role="tab">
                            <i class="ri-calendar-check-line me-1"></i> Employee Records
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#tab-late" role="tab">
                            <i class="ri-calendar-check-line me-1"></i> Late Records
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#tab-view" role="tab">
                            <i class="ri-team-line me-1"></i> View Employee
                        </a>
                    </li>
                </ul>
                <!-- FILTER GLOBAL -->
                <div class="row mb-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Start Date</label>
                        <div class="input-group">
                            <input type="text" name="filter_date" id="filter_date"
                                class="form-control bulan filter_date" placeholder="Pilih Tanggal">
                            <span class="input-group-text">
                                <i class="ri-calendar-event-line"></i>
                            </span>
                        </div>
                    </div>

                    <div class="col-md-9 text-end">
                        <div class="btn-group" role="group">
                            <button class="btn btn-primary active filter-location" data-value="1">HQ</button>
                            <button class="btn btn-outline-primary filter-location" data-value="0">HO</button>
                        </div>
                    </div>
                </div>
                <!-- TAB CONTENT -->
                <div class="tab-content">
                    <!-- ATTENDANCE EMPLOYEE -->
                    <div class="tab-pane fade show active" id="tab-attendance" role="tabpanel">
                        <table class="table table-striped bordered" id="table_employee_attendance">
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
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    <div class="tab-pane fade show " id="tab-late" role="tabpanel">
                        <table class="table table-striped dt-responsive nowrap w-100" id="table_employee_late">
                            <thead>
                                <tr>
                                    <th class="text-center">No</th>
                                    <th class="text-center">NIK</th>
                                    <th class="text-center">Name</th>
                                    <th class="text-center">Position</th>
                                    <th class="text-center">Area</th>
                                    <th class="text-center">Department</th>
                                    <th class="text-center">WorkHour</th>
                                    <th class="text-center">Actual In (Employee)</th>
                                    <th class="text-center">Actual In (Security)</th>
                                    <th class="text-center">Reason Late</th>
                                    <th class="text-center">Diketahui Satpam</th>
                                    <th class="text-center">Diketahui Atasan</th>
                                    <th class="text-center">Diketahui HRD</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    <!-- VIEW EMPLOYEE -->
                    <div class="tab-pane fade" id="tab-view" role="tabpanel">
                        <table class="table table-striped bordered" id="table_employee_view">
                            <thead>
                                <tr>
                                    <th class="text-center">No</th>
                                    <th class="text-center">NIK</th>
                                    <th class="text-center">Name</th>
                                    <th class="text-center">Position</th>
                                    <th class="text-center">Area</th>
                                    <th class="text-center">Department</th>
                                    <th class="text-center">Group WorkHours</th>
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
<script src="{{ asset('assets/js/tz.js') }}"></script>
{{-- <script src="https://cdn.jsdelivr.net/npm/tz-lookup@6.1.25/tz.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment-timezone/builds/moment-timezone-with-data.min.js"></script> --}}
@endsection

@section('javascript')
<script>
    const KNOW_URL = "{{ route('employee-attendance-late.knowledge', ':id') }}";
    const UPDATE_URL = "{{ route('employee-attendance.update', ['id' => ':id']) }}";
</script>
<script type="text/javascript">
$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    // console.log(tzlookup);

    function initPlugins(context = document) {
        $(context).find('.select2').each(function () {
            if ($(this).hasClass("select2-hidden-accessible")) {
                $(this).select2('destroy');
            }
            $(this).select2({ width: '100%' });
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
    // ===================== DEFAULT VALUE =====================
    let today = new Date().toISOString().split('T')[0];
    $('#filter_date').val(today);
    let selectedLocation = 1; // default HQ

    $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        let target = $(e.target).attr("href");
        if (target === '#tab-view') {
            tableView.ajax.reload(null, false);
        }
        if (target === '#tab-late') {
            tableLate.ajax.reload(null, false);
        }
        if (target === '#tab-attendance') {
            table.ajax.reload(null, false);
        }
    });

    // ===================== FILTER DATE =====================
    $('#filter_date').change(function () {
        tableView.ajax.reload();
        table.ajax.reload();
        tableLate.ajax.reload();
    });
    // ===================== FILTER HQ / HO =====================
    $('.filter-location').click(function () {
        $('.filter-location')
            .removeClass('btn-primary active')
            .addClass('btn-outline-primary');
        $(this)
            .removeClass('btn-outline-primary')
            .addClass('btn-primary active');
        selectedLocation = $(this).data('value');
        tableView.ajax.reload();
        table.ajax.reload();
        tableLate.ajax.reload();
    });

    // ===================== DATATABLE ATTENDANCE =====================
    let table = $('#table_employee_attendance').DataTable({
        responsive: true,
        autoWidth: false,
        stateSave: true,
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('employee-attendance.index') }}",
            data: function (d) {
                d.date = $('#filter_date').val();
                d.location = selectedLocation;
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
                                        ? 'Check In - Terlambat'
                                        : 'Check In - Lembur',
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
                            data.status_check_out === 'early' ||
                            data.status_check_out === 'overtime'
                        ){

                            badges.push(
                                badge(
                                    data.status_check_out === 'early'
                                        ? 'Check Out - Pulang Cepat'
                                        : 'Check Out - Lembur',
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
            {data: 'action', orderable: false, searchable: false, className: "text-center"},
        ]
    });

    // ============================== TABLE LATE ==================================
    let tableLate = $('#table_employee_late').DataTable({
        processing: true,
        responsive: false,
        serverSide: true,
        scrollX: true,
        ajax: {
            url: "{{ route('employee-attendance.late') }}",
            data: function (d) {
                d.date = $('#filter_date').val();
                d.location = selectedLocation;
            }
        },
        // order: [[1, 'desc']],
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
            { data: 'actual_in_employee', className: "text-center"},
            { data: 'actual_in_security', className: "text-center"},
            { data: 'reason', className: "text-center"},
            { data: 'security', className: "text-center"},
            { data: 'head', className: "text-center"},
            { data: 'hrd', className: "text-center"},
            {data: 'action', orderable: false, searchable: false, className: "text-center"},
        ]
    });

    // ============================ VIEW ===============================
    let tableView = $('#table_employee_view').DataTable({
        responsive: true,
        autoWidth: false,
        stateSave: true,
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('employee-attendance.view') }}",
            data: function (d) {
                d.date = $('#filter_date').val();
                d.location = selectedLocation;
            }
        },
        columns: [
            {data: 'DT_RowIndex', className: "text-center", orderable: false, searchable: false},
            {data: 'nik', className: "text-center"},
            {data: 'employee_name', className: "text-center"},
            {data: 'position_name', className: "text-center"},
            {data: 'area_name', className: "text-center"},
            {data: 'department_name', className: "text-center"},
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
                data: 'status',
                className: "text-center",
                render: function () {
                    return '<span class="badge bg-warning">No Records</span>';
                }
            },
        ]
    });
    $(document).on('click', '.knowledge-btn', function () {
        let id = $(this).data('id');
        let url = KNOW_URL.replace(':id', id);
        Swal.fire({
            title: 'Konfirmasi',
            text: `Konfirmasi Karyawan Yang terlambat?`,
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
                    $('#table_employee_late').DataTable().ajax.reload();
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

    $(document).on('click','.edit-btn',function(){

    let id = $(this).data('id');
    let checkin = $(this).data('checkin') ?? '';
    let checkout = $(this).data('checkout') ?? '';

    Swal.fire({

        title: 'Edit Attendance',

        html: `

            <div class="text-start mb-3">

                <label class="form-label">
                    Check In
                </label>

                <input
                    id="swal-checkin"
                    type="text"
                    class="form-control">

            </div>

            <div class="text-start">

                <label class="form-label">
                    Check Out
                </label>

                <input
                    id="swal-checkout"
                    type="text"
                    class="form-control">

            </div>
        `,

        showCancelButton:true,

        confirmButtonText:'Save',

        didOpen: function(){

            flatpickr("#swal-checkin",{

                enableTime:true,

                dateFormat:"Y-m-d H:i",

                time_24hr:true,
                allowOutsideClick: false,

                defaultDate: checkin

            });

            flatpickr("#swal-checkout",{

                enableTime:true,
                dateFormat:"Y-m-d H:i",
                time_24hr:true,
                allowOutsideClick: false,
                defaultDate: checkout

            });

        },

        preConfirm:()=>{

            return {

                check_in:
                    $('#swal-checkin').val(),

                check_out:
                    $('#swal-checkout').val()

            }

        }

    }).then((result)=>{

        if(!result.isConfirmed) return;

        $.ajax({

            url: UPDATE_URL.replace(':id', id),

            method:'PUT',

            data:{
                _token:"{{ csrf_token() }}",
                check_in:result.value.check_in,
                check_out:result.value.check_out
            },

            success:function(res){

                Swal.fire(
                    'Success',
                    res.message,
                    'success'
                );

                table.ajax.reload();

            },

            error:function(xhr){

                Swal.fire(
                    'Error',
                    xhr.responseJSON?.message ??
                    'Failed',
                    'error'
                );

            }

        });

    });

});
    function formatDateTime(value){
        if(!value) return '';
        let date = new Date(value);
        return date.toISOString()
            .slice(0,16);

    }
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
