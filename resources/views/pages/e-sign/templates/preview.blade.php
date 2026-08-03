@extends('layouts.master')
@section('title', 'Preview Template - E-Sign')
@section('link')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .preview-content {
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 40px;
            font-size: 14px;
            line-height: 1.8;
            white-space: pre-wrap;
            font-family: 'Courier New', monospace;
        }
    </style>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <div class="flex-shrink-0" style="width:38px;height:38px;background:linear-gradient(135deg,#0ab39c,#405189);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                    <i class="ri-eye-line text-white fs-18"></i>
                </div>
                <div>
                    <h4 class="mb-sm-0">Preview Template</h4>
                    <small class="text-muted">{{ $template->jenis_surat_label }} — v{{ $template->version }}</small>
                </div>
            </div>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">E-Sign Management</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('e-sign.jenis-surat') }}">Jenis Surat</a></li>
                    <li class="breadcrumb-item active">Preview Template</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">{{ $template->title }}</h5>
            </div>
            <div class="card-body">
                <div class="preview-content">{{ $rendered }}</div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <i class="ri-information-line me-1"></i> Informasi
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted d-block">Template</small>
                    <span>{{ $template->jenis_surat_label }} — v{{ $template->version }}</span>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Status</small>
                    {!! $template->status_badge !!}
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Data Dummy yang Digunakan</small>
                    <div class="mt-1">
                        @foreach($placeholders as $key => $desc)
                        <div class="mb-1">
                            <code>@{{{{ $key }}}}</code>
                            <small class="text-muted"> → {{ $desc }}</small>
                        </div>
                        @endforeach
                    </div>
                </div>
                <hr>
                <div class="d-grid gap-2">
                    <a href="{{ route('e-sign.jenis-surat.edit', $template->letter_type_id) }}" class="btn btn-primary">
                        <i class="ri-pencil-line me-1"></i> Kelola Template
                    </a>
                    <a href="{{ route('e-sign.jenis-surat') }}" class="btn btn-light">
                        <i class="ri-arrow-left-line me-1"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
