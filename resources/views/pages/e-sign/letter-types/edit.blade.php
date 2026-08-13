@extends('layouts.master')
@section('title', 'Edit Jenis Surat - E-Sign')
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
    .preview-color-box {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-block;
        vertical-align: middle;
        border: 2px solid #e9ecef;
    }
    .nav-tabs .nav-link {
        font-weight: 500;
        color: #6c757d;
        padding: 0.75rem 1.25rem;
        border: none;
        border-bottom: 2px solid transparent;
    }
    .nav-tabs .nav-link.active {
        color: #405189;
        border-bottom-color: #405189;
        background: transparent;
    }
    .nav-tabs .nav-link:hover:not(.active) {
        border-bottom-color: #dee2e6;
    }
    .template-card {
        border: 1px solid #e9ecef;
        border-radius: 12px;
        padding: 1.25rem;
        transition: box-shadow 0.2s;
    }
    .template-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.06);
    }
    .template-card.active-template {
        border-color: #0ab39c;
        background: #f0fdf9;
    }
    .file-upload-area {
        border: 2px dashed #d1d5db;
        border-radius: 12px;
        padding: 2.5rem 1.5rem;
        text-align: center;
        transition: border-color 0.2s, background 0.2s;
        cursor: pointer;
    }
    .file-upload-area:hover {
        border-color: #405189;
        background: #f8f9ff;
    }
    .file-upload-area.has-file {
        border-color: #0ab39c;
        background: #f0fdf9;
    }
    .file-info {
        display: none;
    }
    .file-info.show {
        display: block;
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
                    <h4 class="mb-sm-0">Edit Jenis Surat</h4>
                    <small class="text-muted">{{ $type->name }}</small>
                </div>
            </div>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('e-sign.jenis-surat') }}">E-Sign Management</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('e-sign.jenis-surat') }}">Jenis Surat</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Tabs -->

<form action="{{ route('e-sign.jenis-surat.update', $type->id) }}" method="POST">
    @csrf
    @method('PUT')
            <div class="row">
                <div class="col-lg-8">
                    <div class="card form-card">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Informasi Jenis Surat</h5>
                            <div class="row mb-3">
                                <label class="col-md-3 col-form-label">Nama Jenis Surat <span class="text-danger">*</span></label>
                                <div class="col-md-9">
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $type->name) }}" required>
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-md-3 col-form-label">Prefix Nomor Surat <span class="text-danger">*</span></label>
                                <div class="col-md-9">
                                    <input type="text" name="prefix" class="form-control @error('prefix') is-invalid @enderror" value="{{ old('prefix', $type->prefix) }}" placeholder="Contoh: PKWT" required>
                                    <small class="text-muted">Contoh: PKWT/HRD/2026/001</small>
                                    @error('prefix')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-md-3 col-form-label">Deskripsi</label>
                                <div class="col-md-9">
                                    <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description', $type->description) }}</textarea>
                                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card form-card">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Tampilan</h5>
                            <div class="mb-3">
                                <label class="form-label">Ikon</label>
                                <select name="icon" class="form-select @error('icon') is-invalid @enderror">
                                    <option value="ri-file-text-line" {{ old('icon',$type->icon)=='ri-file-text-line' ? 'selected' : '' }}>📄 File Text</option>
                                    <option value="ri-file-paper-2-line" {{ old('icon',$type->icon)=='ri-file-paper-2-line' ? 'selected' : '' }}>📋 File Paper</option>
                                    <option value="ri-file-list-3-line" {{ old('icon',$type->icon)=='ri-file-list-3-line' ? 'selected' : '' }}>📑 File List</option>
                                    <option value="ri-file-copy-2-line" {{ old('icon',$type->icon)=='ri-file-copy-2-line' ? 'selected' : '' }}>📝 File Copy</option>
                                    <option value="ri-contract-line" {{ old('icon',$type->icon)=='ri-contract-line' ? 'selected' : '' }}>📜 Contract</option>
                                    <option value="ri-draft-line" {{ old('icon',$type->icon)=='ri-draft-line' ? 'selected' : '' }}>✏️ Draft</option>
                                    <option value="ri-sticky-note-line" {{ old('icon',$type->icon)=='ri-sticky-note-line' ? 'selected' : '' }}>📌 Sticky Note</option>
                                    <option value="ri-bookmark-line" {{ old('icon',$type->icon)=='ri-bookmark-line' ? 'selected' : '' }}>🔖 Bookmark</option>
                                    <option value="ri-survey-line" {{ old('icon',$type->icon)=='ri-survey-line' ? 'selected' : '' }}>📊 Survey</option>
                                    <option value="ri-mail-send-line" {{ old('icon',$type->icon)=='ri-mail-send-line' ? 'selected' : '' }}>📧 Mail Send</option>
                                </select>
                                @error('icon')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Warna</label>
                                <select name="color" class="form-select @error('color') is-invalid @enderror">
                                    <option value="primary" {{ old('color',$type->color)=='primary' ? 'selected' : '' }}>🔵 Blue</option>
                                    <option value="success" {{ old('color',$type->color)=='success' ? 'selected' : '' }}>🟢 Green</option>
                                    <option value="warning" {{ old('color',$type->color)=='warning' ? 'selected' : '' }}>🟡 Yellow</option>
                                    <option value="danger" {{ old('color',$type->color)=='danger' ? 'selected' : '' }}>🔴 Red</option>
                                    <option value="info" {{ old('color',$type->color)=='info' ? 'selected' : '' }}>🔵 Info</option>
                                    <option value="secondary" {{ old('color',$type->color)=='secondary' ? 'selected' : '' }}>⚪ Secondary</option>
                                    <option value="dark" {{ old('color',$type->color)=='dark' ? 'selected' : '' }}>⚫ Black</option>
                                </select>
                                @error('color')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <div class="mt-2 d-flex align-items-center gap-2">
                                    <span class="preview-color-box" id="colorPreview"></span>
                                    <small class="text-muted">Pratinjau warna</small>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input type="checkbox" name="is_active" class="form-check-input" value="1" id="isActive" {{ old('is_active', $type->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="isActive">Aktif</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('e-sign.jenis-surat') }}" class="btn btn-secondary w-50"><i class="ri-arrow-left-line me-1"></i> Kembali</a>
                        <button type="submit" class="btn btn-primary w-50"><i class="ri-save-line me-1"></i> Simpan</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@section('javascript')
<script>
    // Back browser → kembali ke tampilan Jenis Surat
    (function() {
        if (window.history && window.history.pushState) {
            history.pushState(null, '', location.href);
            window.addEventListener('popstate', function() {
                window.location.replace('{{ route("e-sign.jenis-surat") }}');
            });
        }
    })();
</script>
<script>
function updateColorPreview(){const c=$('select[name="color"]').val();const m={'primary':'#405189','success':'#0ab39c','warning':'#f7b84b','danger':'#f06548','info':'#299cdb','secondary':'#adb5bd','dark':'#1e293b'};$('#colorPreview').css('background-color',m[c]||'#405189');}
$('select[name="color"]').on('change',updateColorPreview);updateColorPreview();
</script>
@endsection
