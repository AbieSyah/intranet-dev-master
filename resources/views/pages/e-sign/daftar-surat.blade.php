@extends('layouts.master')
@section('title', 'Daftar Surat - E-Sign')
@section('link')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ url('') }}/assets/libs/Datatables/DataTables-1.13.1/css/dataTables.bootstrap5.css" rel="stylesheet" type="text/css" />
    <link href="{{ url('') }}/assets/libs/Datatables/Buttons-2.3.3/css/buttons.dataTables.css" rel="stylesheet" type="text/css" />
    <link href="{{ url('') }}/assets/libs/Datatables/Responsive-2.4.0/css/responsive.bootstrap.css" rel="stylesheet" type="text/css" />
    <style>
        div.dataTables_wrapper { width: 100%; }
        .table-esign th { font-size: 12px; text-transform: uppercase; letter-spacing: 0.3px; }
        .table-esign td { font-size: 13px; vertical-align: middle; }
    </style>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <div class="flex-shrink-0" style="width:38px;height:38px;background:linear-gradient(135deg,#0ab39c,#405189);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                    <i class="ri-file-signature-line text-white fs-18"></i>
                </div>
                <div>
                    <h4 class="mb-sm-0">Daftar Surat</h4>
                    <small class="text-muted">Digital Signature Management System</small>
                </div>
            </div>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">E-Sign Management</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('e-sign.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Daftar Surat</li>
                </ol>
            </div>
        </div>
    </div>
</div>

@if($currentStatus)
<div class="row mb-3">
    <div class="col-12">
        <div class="alert alert-soft-info d-flex align-items-center gap-2 mb-0 py-2 px-3" role="alert">
            <i class="ri-filter-line"></i>
            <span>Menampilkan dokumen dengan status: <strong>{{ $currentStatus }}</strong></span>
            <a href="{{ route('e-sign.daftar-surat') }}" class="btn btn-sm btn-light ms-auto">Reset Filter</a>
        </div>
    </div>
</div>
@endif

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="card-title mb-0">Semua Dokumen</h5>
                    <small class="text-muted">Total {{ count($documents) }} dokumen</small>
                </div>
                <a href="{{ route('e-sign.jenis-surat') }}" class="btn btn-sm btn-primary">
                    <i class="ri-add-line me-1"></i>Buat Surat Baru
                </a>
            </div>
            <div class="card-body">
                <table class="table table-striped dt-responsive nowrap w-100 table-esign" id="tableDaftarSurat">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nomor Surat</th>
                            <th>Jenis Surat</th>
                            <th>NIK</th>
                            <th>Nama Employee</th>
                            <th>Departemen</th>
                            <th>Tanggal Dibuat</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($documents as $i => $doc)
                        @php
                            $slugMap = [
                                'PKWT' => 'pkwt',
                                'Promosi' => 'promosi',
                                'Mutasi' => 'mutasi',
                                'Demosi' => 'demosi',
                                'Perpanjangan PKWT' => 'perpanjangan-pkwt',
                                'Pengangkatan Karyawan Tetap' => 'pengangkatan',
                                'Surat Peringatan' => 'surat-peringatan',
                            ];
                            $slug = $slugMap[$doc['jenis_surat']] ?? 'pkwt';
                            $badge = match($doc['status']) {
                                'Signed' => 'success',
                                'Waiting Signature' => 'info',
                                'Draft' => 'warning',
                                'Rejected' => 'danger',
                                default => 'secondary'
                            };
                        @endphp
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td><span class="fw-medium">{{ $doc['nomor_surat'] }}</span></td>
                            <td>{{ $doc['jenis_surat'] }}</td>
                            <td>{{ $doc['nik'] }}</td>
                            <td>{{ $doc['nama'] }}</td>
                            <td>{{ $doc['departemen'] }}</td>
                            <td>{{ $doc['tanggal'] }}</td>
                            <td><span class="badge bg-{{ $badge }}">{{ $doc['status'] }}</span></td>
                            <td>
                                <a href="{{ route('e-sign.template', $slug) }}" class="btn btn-sm btn-soft-primary">
                                    <i class="ri-eye-line me-1"></i>Detail
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script src="{{ url('') }}/assets/libs/Datatables/DataTables-1.13.1/js/jquery.dataTables.min.js"></script>
<script src="{{ url('') }}/assets/libs/Datatables/DataTables-1.13.1/js/dataTables.bootstrap5.min.js"></script>
<script src="{{ url('') }}/assets/libs/Datatables/Buttons-2.3.3/js/dataTables.buttons.min.js"></script>
<script src="{{ url('') }}/assets/libs/Datatables/Responsive-2.4.0/js/dataTables.responsive.min.js"></script>
<script src="{{ url('') }}/assets/libs/Datatables/Responsive-2.4.0/js/responsive.bootstrap.min.js"></script>
<script>
    $(document).ready(function() {
        $('#tableDaftarSurat').DataTable({
            responsive: true,
            pageLength: 25,
            order: [[0, 'asc']],
            columnDefs: [
                { targets: 0, width: '50px' },
            ],
        });
    });
</script>
@endsection