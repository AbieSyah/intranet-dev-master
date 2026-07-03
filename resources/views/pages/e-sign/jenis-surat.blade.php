@extends('layouts.master')
@section('title', 'Jenis Surat - E-Sign')
@section('link')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .letter-card {
            border: none;
            border-radius: 14px;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            height: 100%;
            overflow: hidden;
            position: relative;
        }
        .letter-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 32px rgba(0,0,0,0.10);
        }
        .letter-card .card-body {
            padding: 1.75rem;
        }
        .letter-icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            flex-shrink: 0;
        }
        .letter-card .letter-name {
            font-size: 16px;
            font-weight: 600;
            color: #1e293b;
        }
        .letter-card .letter-desc {
            font-size: 13px;
            color: #6c757d;
            line-height: 1.5;
        }
        .letter-card .doc-count-badge {
            font-size: 12px;
            font-weight: 600;
            padding: 4px 12px;
            border-radius: 20px;
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
                    <h4 class="mb-sm-0">Jenis Surat</h4>
                    <small class="text-muted">Digital Signature Management System</small>
                </div>
            </div>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">E-Sign Management</a></li>
                    <li class="breadcrumb-item active">Jenis Surat</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    @foreach($letterTypes as $letter)
    <div class="col-xl-4 col-lg-6 col-md-6">
        <div class="card letter-card">
            <div class="card-body d-flex flex-column">
                <div class="d-flex align-items-start gap-3 mb-3">
                    <div class="letter-icon bg-{{ $letter['color'] }} bg-opacity-10 text-{{ $letter['color'] }}">
                        <i class="{{ $letter['icon'] }}"></i>
                    </div>
                    <div class="flex-grow-1 min-width-0">
                        <div class="letter-name">{{ $letter['name'] }}</div>
                        <div class="letter-desc mt-1">{{ $letter['desc'] }}</div>
                    </div>
                </div>
                <div class="mt-auto d-flex align-items-center justify-content-between pt-3 border-top">
                    <span class="doc-count-badge bg-{{ $letter['color'] }} bg-opacity-10 text-{{ $letter['color'] }}">
                        <i class="ri-file-text-line me-1"></i>{{ $typeCounts[$letter['slug']] ?? 0 }} Dokumen
                    </span>
                    <a href="{{ route('e-sign.template', $letter['slug']) }}" class="btn btn-sm btn-soft-{{ $letter['color'] }}">
                        Lihat Surat <i class="ri-arrow-right-line ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection
