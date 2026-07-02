@extends('layouts.master')
@section('link')
<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- Datatables-->
<link href="/assets/libs/Datatables/DataTables-1.13.1/css/dataTables.bootstrap5.css" rel="stylesheet" type="text/css" />
<link href="/assets/libs/Datatables/Buttons-2.3.3/css/buttons.dataTables.css" rel="stylesheet" type="text/css" />
<link href="/assets/libs/Datatables/Responsive-2.4.0/css/responsive.bootstrap.css" rel="stylesheet" type="text/css" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css">
@endsection
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Employee Leave</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Menu</a></li>
                    <li class="breadcrumb-item active">Employee Leave</li>
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
                    <a class="nav-link active" data-bs-toggle="tab" href="#leave-request">
                        Leave Request History
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#leave-balance">
                        Leave Balance
                    </a>
                </li>
            </ul>
            <!-- CONTENT -->
            <div class="tab-content p-3">
                <!-- ================= REQUEST CUTI ================= -->
                <div class="tab-pane fade show active" id="leave-request">

                    <div class="row align-items-end justify-content-between mb-3">
                        <div class="col-md-3">
                            <label class="form-label">Request Date</label>
                            <div class="input-group">
                                <input type="text" name="request_date" class="form-control bulan request_date" placeholder="Pilih Tanggal">
                                <span class="input-group-text">
                                    <i class="ri-calendar-event-line"></i>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="button" onclick="window.location.href='{{ route('employee-leave.leave-hrd-create') }}'"
                                class="btn btn-primary w-100">
                                Make Leave Request
                            </button>
                        </div>
                    </div>
                    <table class="table table-striped" id="table-leave-history">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NIK</th>
                                <th>Nama</th>
                                <th>Position</th>
                                <th>Area</th>
                                <th>Department</th>
                                <th>leave Type</th>
                                <th>leave Duration</th>
                                <th>Total Days</th>
                                <th>Notes</th>
                                <th>leave balance left</th>
                                <th>status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>

                <!-- ================= SALDO CUTI ================= -->
                <div class="tab-pane fade " id="leave-balance">
                    <div class="row align-items-end justify-content-between mb-3">
                        <div class="col-md-3">
                            <label class="form-label">Balance Years</label>
                            <select class="form-select select2 balance_date" name="year">
                                @for($i = now()->year; $i >= now()->year - 10; $i--)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="button" onclick="window.location.href='{{ route('employee-leave.leave-balance-create') }}'"
                                class="btn btn-primary w-100">
                                Make Leave Balance
                            </button>
                        </div>
                    </div>
                    <table class="table table-striped" id="table-leave-balance">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th class="text-center">NIK</th>
                                <th class="text-center">Name</th>
                                <th class="text-center">Position</th>
                                <th class="text-center">Area</th>
                                <th class="text-center">Department</th>
                                <th class="text-center">Leave Type</th>
                                <th class="text-center">Remaining days Leave</th>
                                <th class="text-center">Valid</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                    </table>
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
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js"></script>
@endsection

@section('javascript')
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
        // ================= MONTH PICKER =================
        $(context).find('.request_date').each(function () {
            if (this._flatpickr) {
                this._flatpickr.destroy();
            }
            flatpickr(this, {
                plugins: [
                    new monthSelectPlugin({
                        shorthand: true,
                        dateFormat: "Y-m",
                        altFormat: "F Y"
                    })
                ],
                altInput: true,
                allowInput: false,
                defaultDate: "today"
            });
        });
    }

    function reloadTable(tableId) {
        if ($.fn.DataTable.isDataTable(tableId)) {
            $(tableId).DataTable().ajax.reload();
        }
    }

    $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        let target = $(e.target).attr("href");

        if (target === '#leave-request') {
            reloadTable('#table-leave-history');
        }

        if (target === '#leave-balance') {
            reloadTable('#table-leave-balance');
        }
    });
    // DATATABLE
    const table = $('#table-leave-balance').DataTable({
        responsive: true,
        autoWidth: false,
        stateSave: true,
        processing: true,
        serverSide: true,
        ajax: {
            url : "{{ route('employee-leave.leave-balance-index') }}",
            data: function (d) {
            d.year = $('.balance_date').val();
            }
        },
        columns: [
            {data: 'DT_RowIndex', className: "text-center", orderable: false, searchable: false},
            {data: 'nik', className: "text-center"},
            {data: 'employee_name', className: "text-center"},
            {data: 'position', className: "text-center"},
            {data: 'area', className: "text-center"},
            {data: 'department', className: "text-center"},
            {data: 'leave_type', className: "text-center"},
            {data: 'remaining_days', className: "text-center"},
            {data: 'valid', className: "text-center"},
            {data: 'action', className: "text-center", orderable: false, searchable: false},
        ]
    });

    const table_approval = $('#table-leave-history').DataTable({
        responsive: true,
        autoWidth: false,
        stateSave: true,
        processing: true,
        serverSide: true,
        ajax: {
            url : "{{ route('employee-leave.leave-hrd-index') }}",
            data: function (d) {
            d.request_date = $('.request_date').val();
            }
        },
        columns: [
            {data: 'DT_RowIndex', className: "text-center", orderable: false, searchable: false},
            {data: 'nik', className: "text-center"},
            {data: 'name', className: "text-center"},
            {data: 'position', className: "text-center"},
            {data: 'area', className: "text-center"},
            {data: 'department', className: "text-center"},
            {data: 'leave_type', className: "text-center"},
            {data: 'duration', className: "text-center"},
            {data: 'total_days', className: "text-center"},
            {data: 'notes', className: "text-center"},
            {data: 'balance_left', className: "text-center"},
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
            {data: 'action', className: "text-center", orderable: false, searchable: false},
        ]
    });

    $('.request_date').on('change', function () {
        $('#table-leave-history').DataTable().ajax.reload();
    });
    $('.balance_date').on('change', function () {
        $('#table-leave-balance').DataTable().ajax.reload();
    });

    $('#table-leave-balance').on("click", ".delete-btn", function () {
        let id = $(this).data("id");
        Swal.fire({
            title: 'Yakin hapus?',
            text: 'Data akan dihapus permanen',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus!'
        }).then((result) => {

            if (result.isConfirmed) {

                $.ajax({
                    url: "{{ route('employee-leave.leave-balance-destroy') }}",
                    type: "DELETE",
                    data: {
                        id: id,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },

                    success: function (res) {
                        Swal.fire('Success', res.message, 'success');

                        $('#table-leave-balance').DataTable().ajax.reload();
                    },

                    error: function (xhr) {
                        let msg = 'Gagal menghapus';

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }

                        Swal.fire('Error', msg, 'error');
                    }
                });

            }
        });
    });

    $('#table-leave-history').on('click', '.delete-btn', function () {
    let id = $(this).data('id');

    Swal.fire({
        title: 'Yakin hapus?',
        text: "Data leave akan dihapus dan saldo dikembalikan",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "{{ route('employee-leave.leave-hrd-destroy') }}",
                method: "DELETE",
                data: { id: id },
                success: function (res) {
                    Swal.fire('Success', res.message, 'success');
                    $('#table-leave-history').DataTable().ajax.reload();
                    $('#table-balance-approval').DataTable().ajax.reload();
                },
                error: function (err) {
                    Swal.fire('Error', err.responseJSON?.message || 'Gagal', 'error');
                }
            });
        }
    });
});

    initPlugins();
});
</script>
@endsection
