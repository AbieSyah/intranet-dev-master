@extends('layouts.master')
@section('title', 'E-Sign Dashboard')
@section('link')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ url('') }}/assets/libs/Datatables/DataTables-1.13.1/css/dataTables.bootstrap5.css" rel="stylesheet" type="text/css" />
    <style>
        .e-sign-stat-card {
            border: none;
            border-radius: 12px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            cursor: pointer;
            overflow: hidden;
            text-decoration: none;
            display: block;
        }
        .e-sign-stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.12);
        }
        .e-sign-stat-card .card-body {
            padding: 1.5rem;
        }
        .e-sign-stat-card .stat-icon {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }
        .e-sign-stat-card .stat-value {
            font-size: 28px;
            font-weight: 700;
            line-height: 1.2;
        }
        .e-sign-stat-card .stat-label {
            font-size: 13px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
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
                    <h4 class="mb-sm-0">E-Sign Dashboard</h4>
                    <small class="text-muted">Digital Signature Management System</small>
                </div>
            </div>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">E-Sign Management</a></li>
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-xl-2 col-lg-4 col-md-6">
        <div class="card e-sign-stat-card" onclick="window.location='{{ route('e-sign.daftar-surat') }}'" style="color: inherit;">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                        <i class="ri-file-text-line"></i>
                    </div>
                    <div>
                        <div class="stat-value text-primary">{{ $counts['total'] }}</div>
                        <div class="stat-label text-muted">Total Dokumen</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <a href="{{ route('e-sign.daftar-surat', ['status' => 'Draft']) }}" class="col-xl-2 col-lg-4 col-md-6 e-sign-stat-card text-decoration-none">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                        <i class="ri-draft-line"></i>
                    </div>
                    <div>
                        <div class="stat-value text-warning">{{ $counts['draft'] }}</div>
                        <div class="stat-label text-muted">Draft</div>
                    </div>
                </div>
            </div>
        </div>
    </a>
    <a href="{{ route('e-sign.daftar-surat', ['status' => 'Sign 1']) }}" class="col-xl-2 col-lg-4 col-md-6 e-sign-stat-card text-decoration-none">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon bg-info bg-opacity-10 text-info">
                        <i class="ri-hourglass-line"></i>
                    </div>
                    <div>
                        <div class="stat-value text-info">{{ $counts['waiting'] }}</div>
                        <div class="stat-label text-muted">Menunggu Sign</div>
                    </div>
                </div>
            </div>
        </div>
    </a>
    <a href="{{ route('e-sign.daftar-surat', ['status' => 'Completed']) }}" class="col-xl-2 col-lg-4 col-md-6 e-sign-stat-card text-decoration-none">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon bg-success bg-opacity-10 text-success">
                        <i class="ri-check-double-line"></i>
                    </div>
                    <div>
                        <div class="stat-value text-success">{{ $counts['signed'] }}</div>
                        <div class="stat-label text-muted">Completed</div>
                    </div>
                </div>
            </div>
        </div>
    </a>
    <a href="{{ route('e-sign.daftar-surat', ['status' => 'Rejected']) }}" class="col-xl-2 col-lg-4 col-md-6 e-sign-stat-card text-decoration-none">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon bg-danger bg-opacity-10 text-danger">
                        <i class="ri-close-circle-line"></i>
                    </div>
                    <div>
                        <div class="stat-value text-danger">{{ $counts['rejected'] }}</div>
                        <div class="stat-label text-muted">Rejected</div>
                    </div>
                </div>
            </div>
        </div>
    </a>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">Recent Documents</h5>
                <a href="{{ route('e-sign.daftar-surat') }}" class="btn btn-sm btn-soft-primary">Lihat Semua</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-nowrap mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>No. Dokumen</th>
                                <th>Jenis Surat</th>
                                <th>Employee</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentDocuments as $i => $doc)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td><span class="fw-medium">{{ $doc['nomor_surat'] }}</span></td>
                                <td>{{ $doc['jenis_surat'] }}</td>
                                <td>{{ $doc['nama'] }}</td>
                                <td>
                                    @php
                                        $badge = 'secondary';
                                        if ($doc['status'] === 'Completed' || $doc['status'] === 'Signed') $badge = 'success';
                                        elseif (in_array($doc['status'], ['Sign 1', 'Sign 2', 'Sign 3', 'Waiting Signature'])) $badge = 'info';
                                        elseif ($doc['status'] === 'Draft') $badge = 'warning';
                                        elseif ($doc['status'] === 'Rejected') $badge = 'danger';
                                    @endphp
                                    <span class="badge bg-{{ $badge }}">{{ $doc['status'] }}</span>
                                </td>
                                <td>{{ $doc['tanggal'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
