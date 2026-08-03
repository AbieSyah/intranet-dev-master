@extends('layouts.master')
@section('title', 'Detail Jenis Surat - E-Sign')
@section('link')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    .form-card {
        border: none;
        border-radius: 14px;
    }
    .form-card .card-body {
        padding: 2rem;
    }
    .letter-icon-lg {
        width: 64px;
        height: 64px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        flex-shrink: 0;
    }
    .info-label {
        font-size: 12px;
        color: #6c757d;
        width: 120px;
    }
    .info-value {
        font-size: 14px;
        font-weight: 500;
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
                    <h4 class="mb-sm-0">Detail Jenis Surat</h4>
                    <small class="text-muted">{{ $type->name }}</small>
                </div>
            </div>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('e-sign.jenis-surat') }}">E-Sign Management</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('e-sign.jenis-surat') }}">Jenis Surat</a></li>
                    <li class="breadcrumb-item active">{{ $type->name }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card form-card">
            <div class="card-body">
                <div class="d-flex align-items-start gap-3 mb-3">
                    <div class="letter-icon-lg bg-{{ $type->color }} bg-opacity-10 text-{{ $type->color }}">
                        <i class="{{ $type->icon }}"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="mb-1">{{ $type->name }}</h5>
                        {!! $type->status_badge !!}
                    </div>
                </div>

                <hr>

                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="info-label ps-0">Kode Surat</td>
                        <td class="info-value">
                            <span class="badge bg-{{ $type->color }}">{{ $type->prefix }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="info-label ps-0">Deskripsi</td>
                        <td class="info-value">{{ $type->description ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="info-label ps-0">Template</td>
                        <td class="info-value">{!! $type->template_status_label !!}</td>
                    </tr>
                    <tr>
                        <td class="info-label ps-0">Dokumen</td>
                        <td class="info-value">{{ $type->document_count }} surat</td>
                    </tr>
                    <tr>
                        <td class="info-label ps-0">Total Template</td>
                        <td class="info-value">{{ $type->template_count }} versi</td>
                    </tr>
                    <tr>
                        <td class="info-label ps-0">Dibuat</td>
                        <td class="info-value">{{ $type->created_at ? $type->created_at->format('d M Y') : '-' }}</td>
                    </tr>
                </table>

                <hr>

                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('e-sign.jenis-surat') }}" class="btn btn-secondary">
                        <i class="ri-arrow-left-line me-1"></i> Kembali
                    </a>
                    <a href="{{ route('e-sign.templates.create', ['letter_type_id' => $type->id]) }}" class="btn btn-outline-info">
                        <i class="ri-file-copy-2-line me-1"></i> Template Surat
                    </a>
                    <a href="{{ route('e-sign.jenis-surat.edit', $type->id) }}" class="btn btn-outline-primary">
                        <i class="ri-pencil-line me-1"></i> Edit Info
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
