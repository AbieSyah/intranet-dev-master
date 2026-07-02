@extends('layouts.master')
@section('link')
<meta name="csrf-token" content="{{ csrf_token() }}">
   <style>
    .limit-text{
        max-width: 200px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
   </style>
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
            <h4 class="mb-sm-0">Form Perizinan</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Menu</a></li>
                    <li class="breadcrumb-item active">Form Perizinan</li>
                </ol>
            </div>

        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="card">

            <!-- TAB UTAMA -->
            <ul class="nav nav-tabs mb-3 px-3 pt-3" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#attendance-permit">
                        Izin Karyawan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#overtime">
                        Lembur
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#business-trip">
                        Perjalanan Bisnis
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" data-bs-target="#employee-leave" href="#employee-leave">
                        Cuti Karyawan
                    </a>
                </li>
            </ul>
            <!-- CONTENT TAB UTAMA -->
            <div class="tab-content p-3">
                <!-- ================= IZIN ================= -->
                <div class="tab-pane fade show active" id="attendance-permit">
                    <div class="row align-items-end justify-content-between mb-3">
                        <!-- FILTER TYPE -->
                        <div class="col-md-3">
                            <label class="form-label">Permit Type</label>
                            <select id="filter_type" class="form-select select2">
                                <option value="">All Type</option>
                                <option value="sick">Izin Dokter</option>
                                <option value="earlyout">Pulang Cepat</option>
                                <option value="late">Terlambat</option>
                                <option value="temporary_out">Keluar Sementara</option>
                                <option value="other">Lainnya</option>
                            </select>
                        </div>
                        <!-- FILTER DATE -->
                        <div class="col-md-3">
                            <label class="form-label">Request Date</label>
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
                    <table class="table table-striped dt-responsive nowrap w-100" id="table-permit">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NIK</th>
                                <th>Nama</th>
                                <th>Position</th>
                                <th>Area</th>
                                <th>Department</th>
                                <th>Tipe Izin</th>
                                <th>Tanggal Izin</th>
                                <th>Waktu Izin</th>
                                <th>Status</th>
                                <th>Mengetahui</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>

                <!-- ================= LEMBUR ================= -->
                <div class="tab-pane fade" id="overtime">
                    <div class="row align-items-end justify-content-between mb-3">
                        <!-- FILTER DATE -->
                        <div class="col-md-3">
                            <label class="form-label">Claim Overtime Date</label>
                            <div class="input-group">
                                <input type="text" name="overtime_date"
                                    class="form-control overtime_date"
                                    placeholder="Pilih Tanggal">
                                <span class="input-group-text">
                                    <i class="ri-calendar-event-line"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <table class="table table-striped dt-responsive nowrap w-100" id="table-overtime">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NIK</th>
                                <th>Nama</th>
                                <th>Position</th>
                                <th>Area</th>
                                <th>Department</th>
                                <th>WorkHour</th>
                                <th>Tanggal Lembur</th>
                                <th>Jam Lembur</th>
                                <th>Kesepakatan Jam Lembur</th>
                                <th>Total jam Lembur</th>
                                <th>Note</th>
                                <th>Mengetahui</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <!-- ================= PERJALANAN BISNIS ================= -->
                <div class="tab-pane fade" id="business-trip">
                    <div class="d-flex gap-2 mb-3" role="tablist">
                        <a class="btn btn-outline-primary active"
                            data-bs-toggle="tab"
                            href="#trip-onprocess"
                            role="tab">
                            ON PROCESS
                        </a>
                        <a class="btn btn-outline-success"
                            data-bs-toggle="tab"
                            href="#trip-completed"
                            role="tab">
                            REPORTED / COMPLETED
                        </a>
                        <a class="btn btn-outline-danger"
                            data-bs-toggle="tab"
                            href="#trip-cancelled"
                            role="tab">
                            CANCELLED
                        </a>
                    </div>
                    <!-- FILTER GLOBAL -->
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">
                                        Bulan Pengajuan
                                    </label>
                                    <div class="input-group">
                                        <input type="text"
                                            id="filter_business_trip_month"
                                            class="form-control filter_business_trip_month"
                                            placeholder="Pilih Tanggal">
                                        <span class="input-group-text">
                                            <i class="ri-calendar-event-line"></i>
                                        </span>
                                    </div>
                                </div>
                                {{-- <div class="col-md-3">
                                    <label class="form-label">
                                        Employee
                                    </label>
                                    <input type="text"
                                        id="filter_employee"
                                        class="form-control"
                                        placeholder="Cari nama / NIK">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">
                                        Type
                                    </label>
                                    <select id="filter_trip_type"
                                        class="form-select">
                                        <option value="">
                                            Semua
                                        </option>
                                        <option value="domestic">
                                            Domestic
                                        </option>
                                        <option value="overseas">
                                            Overseas
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="button"
                                        class="btn btn-primary w-100"
                                        id="btn-search-trip">
                                        Search
                                    </button>
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="button"
                                        class="btn btn-light w-100"
                                        id="btn-reset-trip">
                                        Reset
                                    </button>
                                </div> --}}
                            </div>
                        </div>
                    </div>
                    <!-- TAB CONTENT -->
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="trip-onprocess">
                            <table class="table table-striped dt-responsive nowrap w-100" id="table-businessTrip-onprocess">
                                <thead>
                                    <tr>
                                        <th class="text-center">No</th>
                                        <th class="text-center">No Document</th>
                                        <th class="text-center">NIK</th>
                                        <th class="text-center">Nama</th>
                                        <th class="text-center">Area</th>
                                        <th class="text-center">Department</th>
                                        <th class="text-center">Position</th>
                                        <th class="text-center">Tipe Perjalanan Dinas</th>
                                        <th class="text-center">Tanggal Perjalanan Dinas</th>
                                        <th class="text-center">Jam Berangkat dan Tiba</th>
                                        <th class="text-center">Berangkat Dari</th>
                                        <th class="text-center">Tujuan</th>
                                        <th class="text-center" style="width: 200px;"> Keperluan </th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                        <div class="tab-pane fade" id="trip-completed">
                            <table class="table table-striped dt-responsive nowrap w-100" id="table-businessTrip-report">
                                <thead>
                                    <tr>
                                        <th class="text-center">No</th>
                                        <th class="text-center">No Document</th>
                                        <th class="text-center">NIK</th>
                                        <th class="text-center">Nama</th>
                                        <th class="text-center">Area</th>
                                        <th class="text-center">Department</th>
                                        <th class="text-center">Position</th>
                                        <th class="text-center">Tipe Perjalanan Dinas</th>
                                        <th class="text-center">Tanggal Perjalanan Dinas</th>
                                        <th class="text-center">Tujuan</th>
                                        <th class="text-center" style="width: 200px;"> Keperluan </th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                        <div class="tab-pane fade" id="trip-cancelled">
                            <table class="table table-striped dt-responsive nowrap w-100" id="table-business-trip-cancel">
                                <thead>
                                    <tr>
                                        <th class="text-center">No</th>
                                        <th class="text-center">No Document</th>
                                        <th class="text-center">NIK</th>
                                        <th class="text-center">Nama</th>
                                        <th class="text-center">Area</th>
                                        <th class="text-center">Department</th>
                                        <th class="text-center">Position</th>
                                        <th class="text-center">Tipe Perjalanan Dinas</th>
                                        <th class="text-center">Tanggal Perjalanan Dinas</th>
                                        <th class="text-center">Total Kerugian</th>
                                        <th class="text-center">Kerugian Biaya Yang di Tanggung</th>
                                        <th class="text-center">Alasan Pembatalan</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>

                </div>

                <!-- ================= CUTI ================= -->
                <div class="tab-pane fade" id="employee-leave">
                    <!-- TAB DALAM CUTI -->
                    <div class="d-flex gap-2 mb-3" role="tablist">
                        <a class="btn btn-outline-primary active"
                        data-bs-toggle="tab"
                        href="#leave-request"
                        role="tab">
                            <i class="ri-survey-line me-1 align-bottom"></i>
                            Leave History
                        </a>

                        <a class="btn btn-outline-primary"
                        data-bs-toggle="tab"
                        href="#leave-balance"
                        role="tab">
                            <i class="bi bi-clipboard-check me-1 align-bottom"></i>
                            Leave Balance
                        </a>
                    </div>

                    <!-- CONTENT TAB CUTI -->
                    <div class="tab-content">
                        <!-- LEAVE APPROVAL -->
                        <div class="tab-pane fade show active" id="leave-request">
                            <div class="row align-items-end justify-content-between mb-3">
                                <div class="col-md-3">
                                    <label class="form-label">Request Date</label>
                                    <div class="input-group">
                                        <input type="text" name="request_date"
                                            class="form-control bulan request_date"
                                            placeholder="Pilih Tanggal">
                                        <span class="input-group-text">
                                            <i class="ri-calendar-event-line"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button type="button"
                                        onclick="window.location.href='{{ route('employee-leave.leave-hrd-create') }}'"
                                        class="btn btn-primary w-100">
                                        Make Leave Request
                                    </button>
                                </div>
                            </div>

                            <table class="table table-striped dt-responsive nowrap w-100" id="table-leave-history">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>NIK</th>
                                        <th>Nama</th>
                                        <th>Position</th>
                                        <th>Area</th>
                                        <th>Department</th>
                                        <th>Leave Type</th>
                                        <th>Leave Duration</th>
                                        <th>Total Days</th>
                                        <th>Notes</th>
                                        <th>Leave Balance Left</th>
                                        <th>Lampiran</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>

                        <!-- LEAVE BALANCE -->
                        <div class="tab-pane fade" id="leave-balance">
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
                                    <button type="button"
                                        onclick="window.location.href='{{ route('employee-leave.leave-balance-create') }}'"
                                        class="btn btn-primary w-100">
                                        Make Leave Balance
                                    </button>
                                </div>
                            </div>

                            <table class="table table-striped dt-responsive nowrap w-100" id="table-leave-balance">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>NIK</th>
                                        <th>Name</th>
                                        <th>Position</th>
                                        <th>Area</th>
                                        <th>Department</th>
                                        <th>Leave Type</th>
                                        <th>Remaining Days Leave</th>
                                        <th>Valid</th>
                                        <th>Action</th>
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

<div class="modal fade" id="modalDetail" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Detail Permit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body" id="detailContent">
                Loading...
            </div>
            {{-- @if ($know) --}}
            <div class="modal-footer">
                <button class="btn btn-success" id="btn-know">Mengetahui</button>
            </div>
            {{-- @endif --}}
        </div>
    </div>
</div>
{{-- ========================================== MODAL DETAIL FOR BUSINESS TRIP  ========================================== --}}
<div class="modal fade" id="proposeDetailModal" data-bs-focus="false" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <input type="hidden" id="approval_id">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            {{-- ================= HEADER ================= --}}
            <div class="modal-header bg-light border-0 pb-0">
                <div>
                    <h4 class="mb-1 fw-bold text-black">
                        Business Trip Detail

                    </h4>
                    <div class="">
                        <span id="detail_document_number"></span>
                        •
                        <span id="detail_trip_type_badge"></span>
                    </div>
                </div>
                <div class="justify-content-end">
                    <button type="button" class="btn-close " data-bs-dismiss="modal"></button>
                </div>
            </div>
            <div class="modal-body">
                {{-- ================= TOP SUMMARY ================= --}}
                <div class="row g-3 mb-2">
                    <div class="col-md-3">
                        <div class="border rounded-3 p-3 h-100">
                            <small class="text-muted d-block mb-1">
                                Employee
                            </small>
                            <div class="fw-semibold"
                                 id="detail_employee">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded-3 p-3 h-100">
                            <small class="text-muted d-block mb-1">
                                Date
                            </small>
                            <div class="fw-semibold"
                                 id="detail_date">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded-3 p-3 h-100">
                            <small class="text-muted d-block mb-1">
                                Route
                            </small>
                            <div class="fw-semibold"
                                 id="detail_route">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded-3 p-3 h-100">
                            <small class="text-muted d-block mb-1">
                                Total Estimation
                            </small>
                            <div class="fw-bold text-primary fs-5"
                                 id="detail_total_cost">
                            </div>
                        </div>
                    </div>
                </div>
                {{-- ================= PURPOSE ================= --}}
                <div class="border rounded-3 p-3 mb-4">
                    <div class="fw-semibold mb-2">
                        Purpose
                    </div>
                    <div class="text-muted"
                         id="detail_purpose">
                    </div>
                </div>
                {{-- ================= COST ================= --}}
                <div class="mb-2">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar-xs me-2">
                            <span class="avatar-title bg-primary-subtle text-white rounded">
                                <i class="ri-wallet-3-line"></i>
                            </span>
                        </div>
                        <div class="fw-semibold">
                            Estimasi Biaya
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Kategori</th>
                                    <th>Unit</th>
                                    <th>Total</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody id="detail-cost-wrapper">
                            </tbody>
                        </table>
                    </div>
                </div>
                {{-- ================= TRANSPORT & HOTEL ================= --}}
                <div class="row g-3 mb-4">
                    {{-- TRANSPORT --}}
                    <div class="col-md-6">
                        <div class="border rounded-3 p-3 h-100">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar-xs me-2">
                                    <span class="avatar-title bg-info-subtle text-white rounded">
                                        <i class="ri-car-line"></i>
                                    </span>
                                </div>
                                <div class="fw-semibold">
                                    Transportasi
                                </div>
                            </div>
                            <div id="detail-transport-content">
                            </div>
                        </div>
                    </div>
                    {{-- HOTEL --}}
                    <div class="col-md-6">
                        <div class="border rounded-3 p-3 h-100">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-xs me-2">
                                        <span class="avatar-title bg-warning-subtle text-white rounded">
                                            <i class="ri-hotel-line"></i>
                                        </span>
                                    </div>
                                    <div class="fw-semibold">
                                        Hotel
                                    </div>
                                </div>
                                <div id="hotel-reservation-badge">
                                </div>
                            </div>
                            <div id="detail-hotel-content">
                            </div>
                        </div>
                    </div>
                </div>
                {{-- ================= APPROVAL ================= --}}
                <div class="mb-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar-xs me-2">
                            <span class="avatar-title bg-success-subtle text-white rounded">
                                <i class="ri-check-double-line"></i>
                            </span>
                        </div>
                        <div class="fw-semibold">
                            Approval Flow
                        </div>
                    </div>
                    <div id="approval-wrapper"
                         class="d-flex flex-column gap-2">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- ========================================== MODAL DETAIL FOR BUSINESS TRIP REPORT ========================================== --}}
<div class="modal fade" id="reportClaimDetailModal" data-bs-focus="false" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <input type="hidden" id="approval_report_id">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            {{-- ================= HEADER ================= --}}
            <div class="modal-header bg-light border-0 pb-0">
                <div>
                    <h4 class="mb-1 fw-bold text-black">
                        Business Report Detail •
                        <span id="report-status"></span>
                    </h4>
                    <div class="">
                        <span id="detail_report_document_number"></span>
                        •
                        <span id="detail_report_trip_type_badge"></span>
                    </div>
                </div>
                <div class="justify-content-end">
                    <button type="button" class="btn-close " data-bs-dismiss="modal"></button>
                </div>
            </div>
            <div class="modal-body">
                {{-- ================= TOP SUMMARY ================= --}}
                <div class="row g-3 mb-2">
                    <div class="col-md-3">
                        <div class="border rounded-3 p-3 h-100">
                            <small class="text-muted d-block mb-1">
                                Employee
                            </small>
                            <div class="fw-semibold"
                                 id="detail_report_employee">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded-3 p-3 h-100">
                            <small class="text-muted d-block mb-1">
                                Date
                            </small>
                            <div class="fw-semibold"
                                 id="detail_report_date">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded-3 p-3 h-100">
                            <small class="text-muted d-block mb-1">
                                Tujuan
                            </small>
                            <div class="fw-semibold"
                                 id="detail_report_route">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded-3 p-3 h-100">
                            <small class="text-muted d-block mb-1">
                                Total Estimation
                            </small>
                            <div class="fw-bold text-primary fs-5"
                                 id="detail_report_total_cost">
                            </div>
                        </div>
                    </div>
                </div>
                {{-- ================= PURPOSE ================= --}}
                <div class="border rounded-3 p-3 mb-4">
                    <div class="fw-semibold mb-2">
                        Purpose
                    </div>
                    <div class="text-muted"
                         id="detail_report_purpose">
                    </div>
                </div>
                {{-- ================= COST ================= --}}
                <div class="mb-2">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar-xs me-2">
                            <span class="avatar-title bg-primary-subtle text-white rounded">
                                <i class="ri-wallet-3-line"></i>
                            </span>
                        </div>
                        <div class="fw-semibold">
                            Estimasi Biaya Makan
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>hari</th>
                                    <th>Kategori</th>
                                    <th>Nominal</th>
                                    <th>Attachment/Nota</th>
                                </tr>
                            </thead>
                            <tbody id="detail-meal-wrapper">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar-xs me-2">
                            <span class="avatar-title bg-primary-subtle text-white rounded">
                                <i class="ri-wallet-3-line"></i>
                            </span>
                        </div>
                        <div class="fw-semibold">
                            Estimasi Biaya Lainnya
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Kategori</th>
                                    <th>Nominal</th>
                                    <th>Jumlah</th>
                                    <th>Total</th>
                                    <th>Keterangan</th>
                                    <th>Attachment/Nota</th>
                                </tr>
                            </thead>
                            <tbody id="detail-expense-wrapper">
                            </tbody>
                        </table>
                    </div>
                </div>
                {{-- ================= APPROVAL ================= --}}
                <div class="mb-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar-xs me-2">
                            <span class="avatar-title bg-success-subtle text-white rounded">
                                <i class="ri-check-double-line"></i>
                            </span>
                        </div>
                        <div class="fw-semibold">
                            Approval Flow
                        </div>
                    </div>
                    <div id="approval-report-wrapper"
                         class="d-flex flex-column gap-2">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- ========================================== MODAL DETAIL FOR BUSINESS TRIP CANCELLATION ========================================== --}}
<div class="modal fade" id="cancellationDetailModal" data-bs-focus="false" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <input type="hidden" id="approval_cancel_id">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            {{-- ================= HEADER ================= --}}
            <div class="modal-header bg-light border-0 pb-0">
                <div>
                    <h4 class="mb-1 fw-bold text-black">
                        Cancel Business Trip •
                        <span id="cancel-status"></span>
                    </h4>
                    <div class="">
                        <span id="detail_trip_document_number"></span>
                        •
                        <span id="detail_cancel_type_badge"></span>
                    </div>
                </div>
                <div class="justify-content-end">
                    <button type="button" class="btn-close " data-bs-dismiss="modal"></button>
                </div>
            </div>
            <div class="modal-body">
                {{-- ================= TOP SUMMARY ================= --}}
                <div class="row g-3 mb-2">
                    <div class="col-md-3">
                        <div class="border rounded-3 p-3 h-100">
                            <small class="text-muted d-block mb-1">
                                Employee
                            </small>
                            <div class="fw-semibold"
                                id="detail_trip_employee">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="border rounded-3 p-3 h-100">
                            <small class="text-muted d-block mb-1">
                                Date
                            </small>
                            <div class="fw-semibold"
                                id="detail_trip_date">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="border rounded-3 p-3 h-100">
                            <small class="text-muted d-block mb-1">
                                Tujuan
                            </small>
                            <div class="fw-semibold"
                                id="detail_trip_route">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="border rounded-3 p-3 h-100">
                            <small class="text-muted d-block mb-1">
                                Total Kerugian
                            </small>
                            <div class="fw-bold text-danger fs-5"
                                id="detail_cancel_loss_amount">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="border rounded-3 p-3 h-100 bg-success-subtle">
                            <small class="text-muted d-block mb-1">
                                Ditanggung Perusahaan
                            </small>
                            <div class="fw-bold text-success fs-5"
                                id="detail_company_covered_amount">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="border rounded-3 p-3 h-100 bg-warning-subtle">
                            <small class="text-muted d-block mb-1">
                                Ditanggung Karyawan
                            </small>
                            <div class="fw-bold text-warning fs-5"
                                id="detail_employee_covered_amount">
                            </div>
                        </div>
                    </div>

                </div>
                {{-- ================= PURPOSE ================= --}}
                <div class="border rounded-3 p-3 mb-4">
                    <div class="fw-semibold mb-2">
                        Alasan Pembatalan
                    </div>
                    <div class="text-muted"
                         id="detail_reason_cancel">
                    </div>
                </div>
                {{-- ================= COST ================= --}}
                <div class="mb-2">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar-xs me-2">
                            <span class="avatar-title bg-primary-subtle text-white rounded">
                                <i class="ri-wallet-3-line"></i>
                            </span>
                        </div>
                        <div class="fw-semibold">
                            List Biaya Kerugian
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Kategori</th>
                                    <th>Nominal</th>
                                    <th>Jumlah</th>
                                    <th>Total</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody id="detail-list-loss">
                            </tbody>
                        </table>
                    </div>
                </div>
                {{-- ================= APPROVAL ================= --}}
                <div class="mb-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar-xs me-2">
                            <span class="avatar-title bg-success-subtle text-white rounded">
                                <i class="ri-check-double-line"></i>
                            </span>
                        </div>
                        <div class="fw-semibold">
                            Approval Flow
                        </div>
                    </div>
                    <div id="approval-cancel-wrapper"
                         class="d-flex flex-column gap-2">
                    </div>
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
    const PERMIT_DETAIL = "{{ route('attendance-permit.detail', ':id') }}";
    const PERMIT_KNOWLEDGE = "{{ route('attendance-permit.hrd_knowledge', ':id') }}";
    const OVERTIME_DETAIL = "{{ route('attendance-permit.overtime_detail', ':id') }}";
    const OVERTIME_KNOWLEDGE = "{{ route('attendance-permit.overtime_knowledge', ':id') }}";
</script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
    let hash = window.location.hash;
    if (hash) {
        let triggerEl = document.querySelector(`[data-bs-target="${hash}"]`);
        if (triggerEl) {
            let tab = new bootstrap.Tab(triggerEl);
            tab.show();
        }
    }
});
</script>
<script>
$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    // ===================================================== // FILTER EVENTS // =====================================================
    $('#filter_type, .filter_date').on('change', function () {
        tablePermit.ajax.reload();
    });
    $('.request_date').on('change', function () {
        tableLeaveHistory.ajax.reload();
    });
    $('.balance_date').on('change', function () {
        tableLeaveBalance.ajax.reload();
    });
    $('.overtime_date').on('change', function () {
        tableOvertime.ajax.reload();
    });
    // Note: Flatpickr event listener dipindahkan ke dalam callback onChange di initPlugins()
    // ===================================================== INIT PLUGINS =====================================================
    function initPlugins(context = document) {
        // SELECT2
        $(context).find('.select2').each(function () {
            if ($(this).hasClass("select2-hidden-accessible")) {
                $(this).select2('destroy');
            }
            $(this).select2({ width: '100%' });
        });
        // FILTER DATE
        $(context).find('.filter_date').each(function () {if (!this._flatpickr) {flatpickr(this, {dateFormat: "Y-m-d",altInput: true,altFormat: "d M Y",allowInput: true,defaultDate: "today"});}});
        // REQUEST DATE
        $(context).find('.request_date').each(function () {if (this._flatpickr) { this._flatpickr.destroy(); }flatpickr(this, {plugins: [new monthSelectPlugin({shorthand: true, dateFormat: "Y-m", altFormat: "F Y"})],altInput: true, allowInput: false, defaultDate: "today"});});

        $(context).find('.filter_business_trip_month').each(function () {if (this._flatpickr) { this._flatpickr.destroy(); }flatpickr(this, {
            plugins: [new monthSelectPlugin({shorthand: true, dateFormat: "Y-m", altFormat: "F Y"})],altInput: true, allowInput: false, defaultDate: "today",
                onChange: function(selectedDates, dateStr, instance) { if (typeof tableBusinessTrip !== 'undefined') { tableBusinessTrip.ajax.reload(), tableBusinessReport.ajax.reload(), tablebusinessTripCancel.ajax.reload(); } }});

            });

        $(context).find('.overtime_date').each(function () {if (!this._flatpickr) {flatpickr(this, {dateFormat: "Y-m-d",altInput: true,altFormat: "d M Y",allowInput: true,defaultDate: "today"});}});
    }
    initPlugins();
    // =====================================================  TAB EVENT  =====================================================
    $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        let target = $(e.target).attr("href"); // ambil id tab
        if (target === '#attendance-permit') {
            tablePermit.ajax.reload();
        }
        if (target === '#overtime') {
            tableOvertime.ajax.reload();
        }
        if (target === '#business-trip') {
            tableBusinessTrip.ajax.reload();
        }
        if (target === '#trip-onprocess') {
            tableBusinessTrip.ajax.reload();
        }
        if (target === '#trip-completed') {
            tableBusinessReport.ajax.reload();
        }
        if (target === '#trip-cancelled') {
            tablebusinessTripCancel.ajax.reload();
        }
        if (target === '#employee-leave') {
            tableLeaveHistory.ajax.reload();
            tableLeaveBalance.ajax.reload();
        }
        if (target === '#leave-request') {
            tableLeaveHistory.ajax.reload();
        }
        if (target === '#leave-balance') {
            tableLeaveBalance.ajax.reload();
        }
    });
    // =====================================================  BUSINESS TRIP SECTION  =====================================================
    let tableBusinessTrip = $('#table-businessTrip-onprocess').DataTable({
        processing: true,
        responsive: false,
        serverSide: false,
        scrollX: true,
        ajax: {
            url : "{{ route('index.business-trip') }}",
            data: function (d) {
                d.request_month = $('.filter_business_trip_month').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', className: 'text-center', orderable: false, searchable: false},
            { data: 'no_document', className: 'text-center' },
            { data: 'nik', className:"text-center" },
            { data: 'employee_name', className:"text-center" },
            { data: 'area', className:"text-center"},
            { data: 'department', className:"text-center" },
            { data: 'position', className:"text-center" },
            { data: 'tipe', className: 'text-center' },
            { data: 'date_and_day', className: 'text-center' },
            { data: 'dept_and_arr_times', className: 'text-center' },
            { data: 'depart_from', className: 'text-center' },
            { data: 'arrival_to', className: 'text-center' },
            {
                data: 'needs',
                className: 'text-center',
                render: function(data){
                    return `
                        <div class="limit-text"
                            title="${data}">
                            ${data}
                        </div>
                    `;
                }
            },
            {   data: 'status',
                className: 'text-center',
                render: function (data) {
                    const map = {
                        draft: `
                            <span class="badge bg-secondary">
                                Draft
                            </span>
                        `,
                        revised: `
                            <span class="badge bg-info">
                                Revised
                            </span>
                        `,
                        waiting: `
                            <span class="badge bg-warning text-white">
                                Waiting Approval
                            </span>
                        `,
                        approved: `
                            <span class="badge bg-success">
                                Approved
                            </span>
                        `,
                        ongoing: `
                            <span class="badge bg-warning">
                                Ongoing
                            </span>
                        `,
                        rejected: `
                            <span class="badge bg-danger">
                                Rejected
                            </span>
                        `,
                        cancel_waiting: `
                            <span class="badge bg-warning">
                                Cancel Waiting
                            </span>
                        `,
                    };
                    return map[data] ?? `
                        <span class="badge bg-light text-dark">
                            ${data}
                        </span>
                    `;
                }
            },
            { data: 'action', className: 'text-center', orderable: false, searchable: false}
        ]
    });

    let tableBusinessReport = $('#table-businessTrip-report').DataTable({
        processing: true,
        responsive: false,
        serverSide: false,
        scrollX: true,
        ajax: {
            url : "{{ route('index.business-trip-report') }}",
            data: function (d) {
                d.request_month = $('.filter_business_trip_month').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', className: 'text-center', orderable: false, searchable: false},
            { data: 'no_document', className: 'text-center' },
            { data: 'nik', className:"text-center" },
            { data: 'employee_name', className:"text-center" },
            { data: 'area', className:"text-center"},
            { data: 'department', className:"text-center" },
            { data: 'position', className:"text-center" },
            { data: 'type', className: 'text-center' },
            { data: 'date_and_day', className: 'text-center' },
            { data: 'arrival_to', className: 'text-center' },
            {
                data: 'needs',
                className: 'text-center',
                render: function(data){
                    return `
                        <div class="limit-text"
                            title="${data}">
                            ${data}
                        </div>
                    `;
                }
            },
            {   data: 'status',
                className: 'text-center',
                render: function (data) {
                    const map = {
                        draft: `
                            <span class="badge bg-secondary text-white">
                                Reported
                            </span>
                        `,
                        revised: `
                            <span class="badge bg-info">
                                Reported Revised
                            </span>
                        `,
                        waiting: `
                            <span class="badge bg-secondary text-white">
                                Reported
                            </span>
                        `,
                        approved: `
                            <span class="badge bg-success">
                                Completed
                            </span>
                        `,
                        rejected: `
                            <span class="badge bg-danger">
                                Rejected
                            </span>
                        `,
                    };
                    return map[data] ?? `
                        <span class="badge bg-light text-dark">
                            ${data}
                        </span>
                    `;
                }
            },
            { data: 'action', className: 'text-center', orderable: false, searchable: false}
        ]
    });

    let tablebusinessTripCancel = $('#table-business-trip-cancel').DataTable({
        processing: true,
        responsive: false,
        serverSide: false,
        scrollX: true,
        ajax: "{{ route('index.business-trip-cancel') }}",
        columns: [
            { data: 'DT_RowIndex', className: 'text-center', orderable: false, searchable: false},
            { data: 'no_document', className: 'text-center' },
            { data: 'nik', className:"text-center" },
            { data: 'employee_name', className:"text-center" },
            { data: 'area', className:"text-center"},
            { data: 'department', className:"text-center" },
            { data: 'position', className:"text-center" },
            { data: 'type', className: 'text-center' },
            { data: 'date_and_day', className: 'text-center' },
            {
                data: 'reason_cancel',
                className: 'text-center',
                render: function(data){
                    const map = {
                        emergency:`
                            <strong> Kondisi Darurat atau Alasan Kesehatan </strong>
                        `,
                        company_decision:`
                            <strong> Perubahan Keputusan Perusahaan </strong>
                        `,
                        force_majeure:`
                            <strong> Force Majeure </strong>
                        `,
                        personal_reasons:`
                            <strong> Alasan Pribadi </strong>
                        `,
                        other:`
                            <strong> Lainnya </strong>
                        `,
                    };
                    let reasonText =
                        map[data.reason_cancel]
                        ??
                        data.reason_cancel;
                    return `
                        <div>
                            <div>
                                ${reasonText}
                            </div>
                            ${
                                data.reason_other
                                ?
                                `
                                <small class="text-muted d-block mt-1">
                                    ${data.reason_other}

                                </small>
                                `
                                :
                                ''
                            }
                        </div>
                    `;
                }
            },
            {
                data: 'total_cost_lost',
                className: 'text-center',
                render: function(data){
                    if(!data){
                        return 'IDR 0';
                    }
                    return 'IDR ' +
                        Number(data)
                        .toLocaleString('id-ID');
                }
            },
            {
                data: 'lost_costs_incurred',
                className: 'text-center',
                render: function(data){
                    let employee =
                        Number(
                            data.employee ?? 0
                        ).toLocaleString(
                            'id-ID'
                        );
                    let company =
                        Number(
                            data.company ?? 0
                        ).toLocaleString(
                            'id-ID'
                        );
                    return `
                        <div class="text-start">
                            <div>
                                <strong>
                                    Employee :
                                </strong>
                                IDR ${employee}
                            </div>
                            <div>
                                <strong>
                                    Company :
                                </strong>
                                IDR ${company}
                            </div>
                        </div>
                    `;
                }
            },
            {   data: 'status',
                className: 'text-center',
                render: function (data) {
                    const map = {
                        draft: `
                            <span class="badge bg-warning text-white">
                                Menunggu Pembatalan
                            </span>
                        `,
                        submitted: `
                            <span class="badge bg-warning text-white">
                                Menunggu Pembatalan
                            </span>
                        `,
                        approved: `
                            <span class="badge bg-info">
                                Dibatalkan
                            </span>
                        `,
                        rejected: `
                            <span class="badge bg-danger">
                                Pembatalan Ditolak
                            </span>
                        `,
                    };
                    return map[data] ?? `
                        <span class="badge bg-light text-white">
                            ${data}
                        </span>
                    `;
                }
            },
            { data: 'action', className: 'text-center', orderable: false, searchable: false}
        ]
    });
    $(document).on('click', '.btn-business-trip-detail', function () {
        let id = $(this).data('id');
        $.ajax({
            url: "{{ route('detail.business-trip', ':id') }}"
                .replace(':id', id),
            type: 'GET',
            success: function(res) {
                $('#approval_id').val(res.approval_id);
                fillProposeDetailModal(res);
                $('#proposeDetailModal').modal('show');
            }
        });
    });
    $(document).on('click', '.btn-detail-reportClaim', function () {
        let id = $(this).data('id');
        $.ajax({
            url: "{{ route('detail.business-trip-report', ':id') }}"
                .replace(':id', id),
            type: 'GET',
            success: function(res) {
                $('#approval_id').val(res.approval_id);
                fillReportDetailModal(res);
                $('#reportClaimDetailModal').modal('show');
            }
        });
    });
    $(document).on('click', '.btn-detail-cancellation', function () {
        let id = $(this).data('id');
        $.ajax({
            url: "{{ route('detail.business-trip-cancel', ':id') }}"
                .replace(':id', id),
            type: 'GET',
            success: function(res) {
                console.log(res.trip_type);
                $('#approval_id').val(res.approval_id);
                fillCancellationDetailModal(res);
                $('#cancellationDetailModal').modal('show');
            }
        });
    });
    // =====================================================  OVERTIME SECTION  =====================================================
    const tableOvertime = $('#table-overtime').DataTable({
        processing: true,
        responsive: false,
        serverSide: false,
        scrollX: true,
        ajax: {
            url : "{{ route('attendance-permit.index-overtime') }}",
            data: function (d) {
                d.date = $('.overtime_date').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', className: "text-center", orderable: false, searchable: false},
            { data: 'nik', className: "text-center"},
            { data: 'employee_name', className: "text-center"},
            { data: 'position', className: "text-center"},
            { data: 'area', className: "text-center"},
            { data: 'department', className: "text-center"},
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
            { data: 'overtime_date', className: "text-center"},
            { data: 'overtime_work', className: "text-center"},
            { data: 'agreed_work', className: "text-center"},
            { data: 'total_work', className: "text-center"},
            { data: 'note', className: "text-center"},
            {
                data: 'hrd_knowledge',
                className: "text-center",
                render: function (data) {
                    return data == 1
                        ? `<i class="bi bi-check-circle-fill text-success fs-5"></i>`
                        : `<i class="bi bi-x-circle-fill text-danger fs-5"></i>`;
                }
            },
            {
                data: 'status',
                className: "text-center",
                render: function (data) {
                    const map = {
                        approved: '<span class="badge bg-success">Approved</span>',
                        rejected: '<span class="badge bg-danger">Rejected</span>',
                        waiting: '<span class="badge bg-warning">Waiting</span>',
                    };
                    return map[data] ?? '<span class="badge bg-secondary">Unknown</span>';
                }
            },
            {data: 'action', orderable: false, searchable: false, className: "text-center"},
        ]
    });

    $(document).on('click', '.overtime_knowledge-btn', function () {
        let id = $(this).data('id');
        let url = OVERTIME_DETAIL.replace(':id', id);
        let notesSection = '';

        $('#modalDetail').modal('show');
        $('#detailContent').html('Loading...');

        $.get(url, function (res) {
            const sourceMap = {
                'bf': 'Lembur Sebelum Jam Kerja',
                'af': 'Lembur Setelah Jam Kerja',
                'hl': 'Lembur di Hari Libur',
                'bf|af': 'Lembur Sebelum & Setelah Jam Kerja',
            };
            const sources = (res.source || '').split('|');
            const sourceLabel = sources.map(s => sourceMap[s.trim()] || s.trim()).join(' | ');

            const approvalRows = (res.approvals || []).map(function (approval) {
                const statusMap = {
                    approved: '<span class="badge bg-success">Approved</span>',
                    waiting: '<span class="badge bg-warning">Waiting</span>',
                    rejected: '<span class="badge bg-danger">Rejected</span>',
                    pending: '<span class="badge bg-secondary">Pending</span>',
                };
                return `
                    <tr>
                        <td>${approval.level ?? '-'}</td>
                        <td>${approval.employee?.fullname ?? approval.employee?.nik ?? '-'}</td>
                        <td>${approval.position ?? approval.department ?? '-'}</td>
                        <td>${statusMap[approval.status] ?? approval.status ?? '-'}</td>
                    </tr>
                `;
            }).join('');

            if (parseInt(res.hrd_knowledge) === 0) {
                notesSection = `
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="border rounded p-3">
                                <small class="text-muted">Notes</small>
                                <input type="text"
                                    id="notes"
                                    name="notes"
                                    class="form-control">
                            </div>
                        </div>
                    </div>
                `;
            }

            let html = `
                <div class="container-fluid">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                <small class="text-muted">Nama Karyawan</small>
                                <div class="fw-semibold">${res.employee?.fullname ?? '-'}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                <small class="text-muted">NIK</small>
                                <div class="fw-semibold">${res.employee?.nik ?? '-'}</div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="border rounded p-3">
                                <small class="text-muted">Sumber Lembur</small>
                                <div>${sourceLabel || '-'}</div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="border rounded p-3">
                                <small class="text-muted">Alasan</small>
                                <div>${res.reason ?? '-'}</div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="border rounded p-3">
                                <small class="text-muted">Daftar Approval</small>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered mb-0">
                                        <thead>
                                            <tr>
                                                <th>Level</th>
                                                <th>Approver</th>
                                                <th>Position</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${approvalRows || '<tr><td colspan="6" class="text-center">Tidak ada approval</td></tr>'}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    ${notesSection}
                </div>
            `;

            $('#detailContent').html(html);
            if (parseInt(res.hrd_knowledge) === 1 || res.status !== 'approved') {
                $('#btn-know').hide();
            } else {
                $('#btn-know')
                    .show()
                    .data('id', id)
                    .data('url', OVERTIME_KNOWLEDGE.replace(':id', id))
                    .data('table', 'overtime');
            }
        });
    });
    // ===================================================== PERMIT SECTION =====================================================
    const tablePermit = $('#table-permit').DataTable({
        processing: true,
        responsive: false,
        serverSide: false,
        scrollX: true,
        ajax: {
            url : "{{ route('attendance-permit.index') }}",
            data: function (d) {
                d.filter_date = $('.filter_date').val();
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
            {data: 'type', className: "text-center"},
            {data: 'date_permit', className: "text-center"},
            {data: 'time_permit', className: "text-center"},
            {
                data: 'status',
                className: "text-center",
                render: function (data) {
                    const map = {
                        approved: '<span class="badge bg-success">Approved</span>',
                        rejected: '<span class="badge bg-danger">Rejected</span>',
                        waiting: '<span class="badge bg-warning">Waiting</span>',
                    };
                    return map[data] ?? '<span class="badge bg-secondary">Unknown</span>';
                }
            },
            {
                data: 'hrd_knowledge',
                className: "text-center",
                render: function (data) {
                    return data == 1
                        ? `<i class="bi bi-check-circle-fill text-success fs-5"></i>`
                        : `<i class="bi bi-x-circle-fill text-danger fs-5"></i>`;
                }
            },
            {data: 'action', orderable: false, searchable: false, className: "text-center"},
        ]
    });

    $(document).on('click', '.detail-btn', function () {
        let id = $(this).data('id');
        let url = PERMIT_DETAIL.replace(':id', id);

        $('#modalDetail').modal('show');
        $('#detailContent').html('Loading...');

        $.get(url, function (res) {

            let html = `
                <div class="container-fluid">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                <small class="text-muted">Name</small>
                                <div class="fw-semibold">${res.employee_name}</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                <small class="text-muted">Type</small>
                                <div>
                                    <span class="badge bg-primary">${res.type}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="border rounded p-3">
                                <small class="text-muted">Reason</small>
                                <div>${res.reason ?? '-'}</div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row mb-2">
                        <div class="col-md-4">
                            <small class="text-muted">Approved By</small>
                            <div class="fw-semibold">${res.approved_by_name ?? '-'}</div>
                        </div>

                        <div class="col-md-4">
                            <small class="text-muted">Position</small>
                            <div>${res.approved_by_position ?? '-'}</div>
                        </div>

                        <div class="col-md-4">
                            <small class="text-muted">Approved At</small>
                            <div>${res.approved_by_at ?? '-'}</div>
                        </div>
                    </div>

                </div>
            `;

            $('#detailContent').html(html);

            if (parseInt(res.hrd_knowledge) === 1) {
                $('#btn-know').hide();
            } else {
                $('#btn-know')
                    .show()
                    .data('id', id)
                    .data('url', PERMIT_KNOWLEDGE.replace(':id', id))
                    .data('table', 'permit');
            }
        });
    });
    // ===================================================== KNOW BUTTON =====================================================
    $('#btn-know').on('click', function () {
        let url = $(this).data('url');
        let table = $(this).data('table');
        let notes = $('input[name="notes"]').val();

        if (!url) {
            return;
        }

        $.post(url, {
            notes: notes
        }, function () {
            $('#modalDetail').modal('hide');

            if (table === 'overtime') {
                tableOvertime.ajax.reload();
                Swal.fire(
                    'Success',
                    'Berhasil, Lembur Karyawan Telah Diketahui',
                    'success'
                );
            } else {
                tablePermit.ajax.reload();
                Swal.fire(
                    'Success',
                    'Berhasil, Data Perizinan akan diteruskan ke Security',
                    'success'
                );
            }
        });
    });

    // ===================================================== LEAVE SECTION =====================================================
    const tableLeaveBalance = $('#table-leave-balance').DataTable({
        processing: true,
        responsive: false,
        serverSide: true,
        scrollX: true,
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
    const tableLeaveHistory = $('#table-leave-history').DataTable({
        processing: true,
        responsive: false,
        serverSide: false,
        scrollX: true,
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
            {data: 'attachment', className: "text-center"},
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
});
</script>
<script>
function formatCurrency(value){
    return 'IDR ' + new Intl.NumberFormat('id-ID').format(value ?? 0);
}
function fillProposeDetailModal(res)
{
    // ================= HEADER =================
    $('#detail_document_number').text(
        res.no_document ?? '-'
    );
    let tripBadge = '';
    if (res.trip_type === 'domestic') {
        tripBadge =
            `<span class="badge bg-primary">
                Domestic
            </span>`;
    }
    else {
        tripBadge =
            `<span class="badge bg-info">
                Overseas
            </span>`;
    }
    $('#detail_trip_type_badge').html(
        tripBadge
    );
    // ================= SUMMARY =================
    $('#detail_employee').html(`
        <div class="fw-semibold">
            ${res.employee?.fullname ?? '-'}
        </div>
        <small class="text-muted">
            ${res.level ?? '-'}
            •
            ${res.position ?? '-'}
        </small>
    `);
    $('#detail_date').html(`
        <div class="d-flex align-items-center justify-content-between">

            <div>
                <div class="fw-semibold">
                    ${res.start_date ?? '-'}
                </div>

                <small class="text-muted">
                    Berangkat
                </small>
            </div>

            <div class="px-2">
                <i class="ri-arrow-right-line text-primary fs-5"></i>
            </div>

            <div class="text-end">
                <div class="fw-semibold">
                    ${res.end_date ?? '-'}
                </div>

                <small class="text-muted">
                    Berakhir
                </small>
            </div>

        </div>

        <div class="mt-2 text-center">
            <span class="badge bg-primary-subtle text-primary">
                ${res.total_days ?? 0} Hari
            </span>
        </div>
    `);

    $('#detail_route').html(`
        <div class="d-flex align-items-center justify-content-between gap-3">

            <div>
                <div class="fw-semibold">
                    ${res.departure_from ?? '-'}
                </div>

                <small class="text-muted">
                    Berangkat
                </small>
            </div>

            <div class="px-2">
                <i class="ri-arrow-right-line text-primary fs-5"></i>
            </div>

            <div class="text-end">
                <div class="fw-semibold">
                    ${res.arrival_to ?? '-'}
                </div>

                <small class="text-muted">
                    Tujuan
                </small>
            </div>
        </div>
    `);
    let totalCost = 0;
    res.costs.forEach(item => {
        totalCost += Number(item.total_amount ?? 0);
    });

    $('#detail_total_cost').text(
        'IDR ' + totalCost.toLocaleString()
    );

    $('#detail_purpose').text(
        res.purpose ?? '-'
    );
    // ================= COST =================
    let costHtml = '';
    if (res.costs.length > 0) {
        res.costs.forEach(item => {
            costHtml += `
                <tr>
                    <td class="text-capitalize">
                        ${item.category ?? '-'}
                    </td>
                    <td>
                        ${item.currency ?? 'IDR'}
                        ${Number(item.unit_amount ?? 0).toLocaleString()}
                    </td>
                    <td class="fw-semibold text-primary">
                        ${item.currency ?? 'IDR'}
                        ${Number(item.total_amount ?? 0).toLocaleString()}
                    </td>
                    <td>
                        ${item.notes ?? '-'}
                    </td>
                </tr>
            `;
        });
    } else {
        costHtml = `
            <tr>
                <td colspan="4"
                    class="text-center text-muted py-4">
                    Tidak ada estimasi biaya
                </td>
            </tr>
        `;
    }
    $('#detail-cost-wrapper').html(
        costHtml
    );
    // ================= TRANSPORT =================
    let transportHtml = `
        <div class="text-muted">
            Tidak ada data transportasi
        </div>
    `;
    let transport = res.transportations;
    if (transport) {
        transportHtml = `
            <div class="d-flex align-items-center justify-content-start mb-1 gap-1">
                <small class="text-muted">
                    Transport :
                </small>
                <div class="fw-semibold text-capitalize">
                    ${transport.transport_type ?? '-'}
                    ${transport.public_transport_type
                        ? ' • ' + transport.public_transport_type
                        : ''
                    }
                </div>
            </div>
            ${(transport.vehicle_number || transport.driver_name) ? `
                <div class="d-flex align-items-center justify-content-start mb-1 gap-1">
                    <small class="text-muted">
                        Vehicle / Driver
                    </small>
                    <div class="fw-semibold text-end">
                        ${transport.vehicle_number ?? '-'}
                        ${transport.driver_name
                            ? ' • ' + transport.driver_name
                            : ''
                        }
                    </div>
                </div>
            ` : ''}
            ${(transport.departure_date || transport.arrival_date) ? `
                <div class="d-flex align-items-center justify-content-start mb-1 gap-1">
                    <small class="text-muted">
                        Schedule :
                    </small>
                    <div class="fw-semibold small">
                        ${transport.departure_date ?? '-'}
                        ${transport.departure_time ?? ''}
                        <i class="ri-arrow-right-line mx-1"></i>
                        ${transport.arrival_date ?? '-'}
                        ${transport.arrival_time ?? ''}
                    </div>
                </div>
            ` : ''}

            ${transport.notes ? `
                <div class="d-flex align-items-start justify-content-between">
                    <small class="text-muted">
                        Notes
                    </small>
                    <div class="fw-semibold text-end ms-3">
                        ${transport.notes}
                    </div>
                </div>
            ` : ''}
        `;
    }
    $('#detail-transport-content').html(
        transportHtml
    );
    $('#detail-transport-content').html(
        transportHtml
    );
    // ================= HOTEL =================
    let hotel = res.hotels;
    let reservationBadge = '';
    if (hotel) {
        reservationBadge = `
            <span class="badge ${
                hotel.reservation_by_ga
                    ? 'bg-primary-subtle text-primary'
                    : 'bg-secondary-subtle text-secondary'
            }">

                ${
                    hotel.reservation_by_ga
                        ? 'Reservation by GA'
                        : 'Mandiri'
                }
            </span>
        `;
    }
    $('#hotel-reservation-badge').html(
        reservationBadge
    );
    let hotelHtml = `
        <div class="text-muted">
            Tidak ada hotel
        </div>
    `;
    if (hotel) {
        hotelHtml = `
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                {{-- HOTEL --}}
                <div class="d-flex align-items-center gap-2">
                    <div>
                        <small class="text-muted d-block">
                            Hotel
                        </small>

                        <div class="fw-semibold">
                            ${hotel.hotel_name ?? '-'}
                        </div>
                    </div>
                </div>
                {{-- DATE --}}
                <div class="d-flex align-items-center gap-2">
                    <div>
                        <small class="text-muted d-block">
                            Check In
                        </small>
                        <div class="fw-semibold small">
                            ${hotel.check_in ?? '-'}
                        </div>
                    </div>
                    <i class="ri-arrow-right-line text-muted mt-3"></i>
                    <div>
                        <small class="text-muted d-block">
                            Check Out
                        </small>
                        <div class="fw-semibold small">
                            ${hotel.check_out ?? '-'}
                        </div>
                    </div>
                </div>
                {{-- DURATION --}}
                <div class="text-end">
                    <small class="text-muted text-center d-block">
                        Duration
                    </small>
                    <div class="fw-bold text-primary">
                        ${hotel.total_days ?? 0} Hari
                        /
                        ${hotel.total_nights ?? 0} Malam
                    </div>
                </div>
            </div>
        `;
    }
    $('#detail-hotel-content').html(
        hotelHtml
    );
    // ================= APPROVAL =================
    let approvalHtml = '';
    if (res.approvals.length > 0) {
        res.approvals.forEach(item => {
            let badge = '';
            if (item.status === 'approved') {
                badge = `
                    <span class="badge bg-success">
                        Approved
                    </span>
                `;
            } else if (item.status === 'rejected') {
                badge = `
                    <span class="badge bg-danger">
                        Rejected
                    </span>
                `;
            } else if (item.status === 'waiting') {
                badge = `
                    <span class="badge bg-warning">
                        Waiting
                    </span>
                `;
            } else if (item.status === 'revised') {
                badge = `
                    <span class="badge bg-info">
                        Revised
                    </span>
                `;
            } else {
                badge = `
                    <span class="badge bg-secondary">
                        Pending
                    </span>
                `;
            }
            approvalHtml += `
                <div class="border rounded-3 px-3 py-2 mb-2">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-start align-items-center">
                                <div class="fw-semibold">
                                    ${item.approver?.fullname ?? '-'}
                                </div>
                                <span class="badge bg-light text-dark">
                                    ${item.position ?? '-'}
                                </span>
                            </div>
                            <small class="text-muted">
                                Approval ${item.level}
                            </small>
                            ${
                                item.logs?.length
                                ? `
                                    <div class="mt-1">
                                        ${item.logs.map(log => {
                                            let logBadge = '';
                                            if (log.status === 'approved') {
                                                logBadge =
                                                    `<span class="text-success">
                                                        Approved
                                                    </span>`;
                                            }
                                            else if (log.status === 'rejected') {
                                                logBadge =
                                                    `<span class="text-danger">
                                                        Rejected
                                                    </span>`;
                                            }
                                            else if (log.status === 'revised') {
                                                logBadge =
                                                    `<span class="text-warning">
                                                        Revised
                                                    </span>`;
                                            }
                                            else {
                                                logBadge =
                                                    `<span class="text-secondary">
                                                        ${log.status}
                                                    </span>`;
                                            }
                                            return `
                                                <div class="small text-muted d-flex flex-wrap align-items-center gap-1">
                                                    <span>
                                                        <i class="ri-time-line me-1"></i>
                                                        ${log.action_at ?? '-'}
                                                    </span>
                                                    <span>•</span>
                                                    <span class="fw-semibold">
                                                        ${logBadge}
                                                    </span>
                                                    ${
                                                        log.reason
                                                        ? `
                                                            <>
                                                                <span>•</span>
                                                                <span>
                                                                    ${log.reason}
                                                                </span>
                                                            </>
                                                        `
                                                        : ''
                                                    }
                                                </div>
                                            `;
                                        }).join('')}
                                    </div>
                                `
                                : ''
                            }
                        </div>
                        <div>
                            ${badge}
                        </div>
                    </div>
                </div>
            `;
        });
    } else {
        approvalHtml = `
            <div class="text-muted">
                Tidak ada approval
            </div>
        `;
    }
    $('#approval-wrapper').html(approvalHtml);
    $('#btn-cancel').attr('data-id',res.id);
    // ================= SHOW MODAL =================
    $('#proposeDetailModal').modal('show');
}
function fillReportDetailModal(res)
{
    // ================= HEADER =================
    $('#detail_report_document_number').text(
        res.no_document ?? '-'
    );
    $('#detail_report_trip_type_badge').html(
        res.trip_type === 'domestic'
        ?
        `<span class="badge bg-primary">
            Domestic
        </span>`
        :
        `<span class="badge bg-success">
            Overseas
        </span>`
    );

    let badge = '';
    if (res.status === 'approved') {

        badge = `
            <span class="badge bg-success">
                Approved
            </span>
        `;

    } else if (res.status === 'rejected') {

        badge = `
            <span class="badge bg-danger">
                Rejected
            </span>
        `;

    } else if (res.status === 'revised') {

        badge = `
            <span class="badge bg-info">
                Revised
            </span>
        `;

    } else if (res.status === 'waiting') {

        badge = `
            <span class="badge bg-warning text-white">
                Waiting Approval
            </span>
        `;

    } else if (res.status === 'draft') {

        badge = `
            <span class="badge bg-secondary">
                Draft
            </span>
        `;

    } else {

        badge = `
            <span class="badge bg-light text-dark">
                ${res.status ?? '-'}
            </span>
        `;
    }

    $('#report-status').html(badge);

    // ================= SUMMARY =================
    $('#detail_report_employee').text(
        res.employee_name ?? '-'
    );
    $('#detail_report_date').html(`
        <div class="d-flex align-items-center justify-content-between">

            <div>
                <div class="fw-semibold">
                    ${res.start_date ?? '-'}
                </div>

                <small class="text-muted">
                    Berangkat
                </small>
            </div>

            <div class="px-2">
                <i class="ri-arrow-right-line text-primary fs-5"></i>
            </div>

            <div class="text-end">
                <div class="fw-semibold">
                    ${res.end_date ?? '-'}
                </div>

                <small class="text-muted">
                    Berakhir
                </small>
            </div>

        </div>

        <div class="mt-2 text-center">
            <span class="badge bg-primary-subtle text-primary">
                ${res.total_days ?? 0} Hari
            </span>
        </div>
    `);
    $('#detail_report_route').text(
        res.arrival_to ?? '-'
    );
    $('#detail_report_total_cost').html(
        'IDR ' + new Intl.NumberFormat('id-ID').format(
            res.total_cost ?? 0
        )
    );
    $('#detail_report_purpose').text(
        res.purpose ?? '-'
    );
    // ================= MEAL =================
    $('#detail-meal-wrapper').html('');
    (res.meals ?? []).forEach(item=>{
        let attachmentHtml='-';
        if(item.attachments?.length){
            attachmentHtml = item.attachments.map(file=>{
                return `
                    <a href="/${file.file_path}"
                    target="_blank">
                    ${file.file_name}
                    </a>
                `;
            }).join('<br>');
        }
        $('#detail-meal-wrapper').append(`
            <tr>
                <td>
                    ${item.expense_date}
                </td>
                <td> Meal </td>
                <td> IDR ${new Intl.NumberFormat('id-ID').format(
                        item.unit_amount ?? 0
                    )}
                </td>
                <td>
                    ${attachmentHtml}
                </td>
            </tr>
        `);
    });
    // ================= EXPENSE =================
    $('#detail-expense-wrapper').html('');
    (res.expenses ?? []).forEach(item=>{
        let attachmentHtml='-';
        if(item.attachments?.length){
            attachmentHtml = item.attachments.map(file=>{
                return `
                    <a
                    href="/${file.file_path}"
                    target="_blank">
                    ${file.file_name}
                    </a>
                `;
            }).join('<br>');
        }

        $('#detail-expense-wrapper').append(`
            <tr>
                <td>
                    ${item.category ?? '-'}
                </td>
                <td> IDR ${new Intl.NumberFormat('id-ID').format(
                        item.unit_amount ?? 0
                    )}
                </td>
                <td> ${item.qty ?? 0} </td>
                <td> IDR ${new Intl.NumberFormat('id-ID').format(
                        item.unit_total ?? 0
                    )}
                </td>
                <td> ${item.notes ?? '-'} </td>
                <td> ${attachmentHtml} </td>
            </tr>
        `);
    });
    // ================= APPROVAL =================
    $('#approval-report-wrapper').html('');
    (res.approvals ?? []).forEach(item=>{
        let badge='';
        // console.log(res.approvals);
        if(item.status==='approved')
        {
            badge=
            `<span class="badge bg-success">
                Approved
            </span>`;
        }
        else if(item.status==='rejected')
        {
            badge=
            `<span class="badge bg-danger">
                Rejected
            </span>`;
        }
        else if(item.status==='revised')
        {
            badge=
            `<span class="badge bg-info">
                Revised
            </span>`;
        }
        else if(item.status==='waiting')
        {
            badge=
            `<span class="badge bg-warning">
                Waiting
            </span>`;
        }
        else
        {
            badge=
            `<span class="badge bg-secondary">
                Pending
            </span>`;
        }

        $('#approval-report-wrapper').append(`
            <div class="border rounded-3 px-3 py-2 mb-2">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-start align-items-center">
                                <div class="fw-semibold">
                                    ${item.approver?.fullname ?? '-'}
                                </div>
                                <span class="badge bg-light text-dark">
                                    ${item.position ?? '-'}
                                </span>
                            </div>
                        <small class="text-muted">
                            Approval ${item.level}
                        </small>
                        ${item.logs?.length ?`
                            <div class="mt-2">
                                ${item.logs.map(log=>{
                                    let logBadge='';
                                    if(log.status==='approved'){
                                        logBadge=
                                        `
                                            <span class="text-success fw-semibold">
                                                Approved
                                            </span>
                                        `;
                                    }
                                    else if(log.status==='rejected'){
                                        logBadge=
                                        `
                                            <span class="text-danger fw-semibold">
                                                Rejected
                                            </span>
                                        `;
                                    }
                                    else if(log.status==='revised'){
                                        logBadge=
                                        `
                                            <span class="text-secondary fw-semibold">
                                                Revised
                                            </span>
                                        `;
                                    }
                                    else{
                                        logBadge=
                                        `
                                            <span class="text-warning fw-semibold">

                                                ${log.status}

                                            </span>
                                        `;
                                    }
                                    return `
                                        <div class="small text-muted mb-1">
                                            <div class="d-flex flex-wrap align-items-center gap-1">
                                                <span>
                                                    <i class="ri-time-line me-1"></i>
                                                    ${log.action_at ?? '-'}
                                                </span>
                                                <span>•</span>
                                                ${logBadge}
                                                ${
                                                    log.reason
                                                    ?
                                                    `
                                                        <span>•</span>
                                                        <span>
                                                            ${log.reason}

                                                        </span>
                                                    `
                                                    :
                                                    ''
                                                }
                                            </div>
                                        </div>
                                    `;
                                }).join('')}
                            </div>
                            `
                            :
                            ''
                        }
                    </div>
                    <div>
                        ${badge}
                    </div>
                </div>
            </div>
        `);
    });
}
function fillCancellationDetailModal(res)
{
    // ================= HEADER =================
    $('#detail_trip_document_number').text(
        res.no_document ?? '-'
    );
    let tripBadge = '';
    if (res.trip_type === 'domestic') {
        tripBadge =
            `<span class="badge bg-primary">
                Domestic
            </span>`;
    }
    else {
        tripBadge =
            `<span class="badge bg-info">
                Overseas
            </span>`;
    }
    $('#detail_cancel_type_badge').html(
        tripBadge
    );
    let badge = '';
    if (res.status === 'approved') {

        badge = `
            <span class="badge bg-success">
                Approved
            </span>
        `;

    } else if (res.status === 'rejected') {

        badge = `
            <span class="badge bg-danger">
                Rejected
            </span>
        `;

    } else if (res.status === 'revised') {

        badge = `
            <span class="badge bg-info">
                Revised
            </span>
        `;

    } else if (res.status === 'waiting') {

        badge = `
            <span class="badge bg-warning text-white">
                Waiting Approval
            </span>
        `;

    } else if (res.status === 'draft') {

        badge = `
            <span class="badge bg-secondary">
                Draft
            </span>
        `;

    } else {

        badge = `
            <span class="badge bg-light text-dark">
                ${res.status ?? '-'}
            </span>
        `;
    }

    $('#cancel-status').html(badge);
    // ================= SUMMARY =================
    $('#detail_trip_employee').html(`
        <div class="fw-semibold">
            ${res.employee_name ?? '-'}
        </div>
        <small class="text-muted">
            ${res.position ?? '-'}
        </small>
    `);
    $('#detail_trip_date').html(`
        <div class="d-flex align-items-center justify-content-between">

            <div>
                <div class="fw-semibold">
                    ${res.start_date ?? '-'}
                </div>

                <small class="text-muted">
                    Berangkat
                </small>
            </div>

            <div class="px-2">
                <i class="ri-arrow-right-line text-primary fs-5"></i>
            </div>

            <div class="text-end">
                <div class="fw-semibold">
                    ${res.end_date ?? '-'}
                </div>

                <small class="text-muted">
                    Berakhir
                </small>
            </div>

        </div>

        <div class="mt-2 text-center">
            <span class="badge bg-primary-subtle text-primary">
                ${res.total_days ?? 0} Hari
            </span>
        </div>
    `);
    $('#detail_trip_route').text(
        res.arrival_to ?? '-'
    );
    $('#detail_cancel_loss_amount').html(
        'IDR ' + new Intl.NumberFormat('id-ID').format(
            res.total_cost ?? 0
        )
    );
    $('#detail_company_covered_amount').text(
        formatCurrency(res.company_amount)
    );

    $('#detail_employee_covered_amount').text(
        formatCurrency(res.employee_amount)
    );

    $('#detail_cancel_loss_amount').text(
        formatCurrency(res.total_loss_amount)
    );
    const reasonMap = {
        emergency: 'Kondisi Darurat atau Alasan Kesehatan',
        company_decision: 'Perubahan Keputusan Perusahaan',
        force_majeure: 'Force Majeure',
        personal_reasons: 'Alasan Pribadi',
        other: 'Lainnya'
    };

    let reasonHtml = `
        <div class="fw-semibold">
            ${reasonMap[res.reason_cancel] ?? res.reason_cancel ?? '-'}
        </div>
    `;

    if (res.reason_other) {
        reasonHtml += `
            <div class="mt-2 text-muted">
                ${res.reason_other}
            </div>
        `;
    }

    $('#detail_reason_cancel').html(reasonHtml);

    $('#detail-list-loss').empty();

    (res.items || []).forEach(item => {

        $('#detail-list-loss').append(`
            <tr>
                <td>
                    ${item.category ?? '-'}
                </td>

                <td class="text-start">
                    IDR ${new Intl.NumberFormat('id-ID').format(
                        item.unit_amount ?? 0
                    )}
                </td>

                <td class="text-start">
                    ${item.qty ?? 0}
                </td>

                <td class="text-start">
                    IDR ${new Intl.NumberFormat('id-ID').format(
                        item.unit_total ?? 0
                    )}
                </td>

                <td>
                    ${item.notes ?? '-'}
                </td>
            </tr>
        `);

    });
    // ================= APPROVAL =================
    $('#approval-cancel-wrapper').html('');
    (res.approvals ?? []).forEach(item=>{
        let badge='';
        // console.log(res.approvals);
        if(item.status==='approved')
        {
            badge=
            `<span class="badge bg-success">
                Approved
            </span>`;
        }
        else if(item.status==='rejected')
        {
            badge=
            `<span class="badge bg-danger">
                Rejected
            </span>`;
        }
        else if(item.status==='revised')
        {
            badge=
            `<span class="badge bg-warning">
                Revised
            </span>`;
        }
        else if(item.status==='waiting')
        {
            badge=
            `<span class="badge bg-info">
                Waiting
            </span>`;
        }
        else
        {
            badge=
            `<span class="badge bg-secondary">
                Pending
            </span>`;
        }

        $('#approval-cancel-wrapper').append(`
            <div class="border rounded-3 px-3 py-2 mb-2">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-start align-items-center">
                                <div class="fw-semibold">
                                    ${item.approver?.fullname ?? '-'}
                                </div>
                                <span class="badge bg-light text-dark">
                                    ${item.position ?? '-'}
                                </span>
                            </div>
                        <small class="text-muted">
                            Approval ${item.level}
                        </small>
                        ${item.logs?.length ?`
                            <div class="mt-2">
                                ${item.logs.map(log=>{
                                    let logBadge='';
                                    if(log.status==='approved'){
                                        logBadge=
                                        `
                                            <span class="text-success fw-semibold">
                                                Approved
                                            </span>
                                        `;
                                    }
                                    else if(log.status==='rejected'){
                                        logBadge=
                                        `
                                            <span class="text-danger fw-semibold">
                                                Rejected
                                            </span>
                                        `;
                                    }
                                    else if(log.status==='revised'){
                                        logBadge=
                                        `
                                            <span class="text-warning fw-semibold">
                                                Revised
                                            </span>
                                        `;
                                    }
                                    else{
                                        logBadge=
                                        `
                                            <span class="text-secondary fw-semibold">

                                                ${log.status}

                                            </span>
                                        `;
                                    }
                                    return `
                                        <div class="small text-muted mb-1">
                                            <div class="d-flex flex-wrap align-items-center gap-1">
                                                <span>
                                                    <i class="ri-time-line me-1"></i>
                                                    ${log.action_at ?? '-'}
                                                </span>
                                                <span>•</span>
                                                ${logBadge}
                                                ${
                                                    log.reason
                                                    ?
                                                    `
                                                        <span>•</span>
                                                        <span>
                                                            ${log.reason}

                                                        </span>
                                                    `
                                                    :
                                                    ''
                                                }
                                            </div>
                                        </div>
                                    `;
                                }).join('')}
                            </div>
                            `
                            :
                            ''
                        }
                    </div>
                    <div>
                        ${badge}
                    </div>
                </div>
            </div>
        `);
    });
}
</script>
@endsection
