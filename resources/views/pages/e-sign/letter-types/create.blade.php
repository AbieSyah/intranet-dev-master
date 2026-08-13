@extends('layouts.master')
@section('title', 'Tambah Jenis Surat - E-Sign')
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
                    <h4 class="mb-sm-0">Tambah Jenis Surat</h4>
                    <small class="text-muted">Digital Signature Management System</small>
                </div>
            </div>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('e-sign.jenis-surat') }}">E-Sign Management</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('e-sign.jenis-surat') }}">Jenis Surat</a></li>
                    <li class="breadcrumb-item active">Tambah</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<form action="{{ route('e-sign.jenis-surat.store') }}" method="POST">
    @csrf
    <div class="row">
        <div class="col-lg-8">
            <div class="card form-card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Informasi Jenis Surat</h5>

                    <div class="row mb-3">
                        <label class="col-md-3 col-form-label">Nama Jenis Surat <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name') }}" placeholder="Contoh: Surat PKWT" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-md-3 col-form-label">Prefix Nomor Surat <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <input type="text" name="prefix" class="form-control @error('prefix') is-invalid @enderror"
                                value="{{ old('prefix') }}" placeholder="Contoh: PKWT" required>
                            <small class="text-muted">Contoh: <code>PKWT</code> akan menghasilkan nomor surat <code>PKWT/2026/001</code></small>
                            @error('prefix')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-md-3 col-form-label">Deskripsi</label>
                        <div class="col-md-9">
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                rows="3" placeholder="Deskripsi jenis surat...">{{ old('description') }}</textarea>
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
                        <select name="icon" class="form-select @error('icon') is-invalid @enderror" id="iconSelect">
                            <option value="ri-file-text-line" {{ old('icon')=='ri-file-text-line' ? 'selected' : '' }}>📄 File Text</option>
                            <option value="ri-file-paper-2-line" {{ old('icon')=='ri-file-paper-2-line' ? 'selected' : '' }}>📋 File Paper</option>
                            <option value="ri-file-list-3-line" {{ old('icon')=='ri-file-list-3-line' ? 'selected' : '' }}>📑 File List</option>
                            <option value="ri-file-copy-2-line" {{ old('icon')=='ri-file-copy-2-line' ? 'selected' : '' }}>📝 File Copy</option>
                            <option value="ri-contract-line" {{ old('icon')=='ri-contract-line' ? 'selected' : '' }}>📜 Contract</option>
                            <option value="ri-draft-line" {{ old('icon')=='ri-draft-line' ? 'selected' : '' }}>✏️ Draft</option>
                            <option value="ri-sticky-note-line" {{ old('icon')=='ri-sticky-note-line' ? 'selected' : '' }}>📌 Sticky Note</option>
                            <option value="ri-bookmark-line" {{ old('icon')=='ri-bookmark-line' ? 'selected' : '' }}>🔖 Bookmark</option>
                            <option value="ri-survey-line" {{ old('icon')=='ri-survey-line' ? 'selected' : '' }}>📊 Survey</option>
                            <option value="ri-mail-send-line" {{ old('icon')=='ri-mail-send-line' ? 'selected' : '' }}>📧 Mail Send</option>
                        </select>
                        <small class="text-muted">Pilih ikon dari Remix Icon</small>
                        @error('icon')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Warna</label>
                        <select name="color" class="form-select @error('color') is-invalid @enderror">
                            <option value="primary" {{ old('color')=='primary' ? 'selected' : '' }}>🔵 Primary (Biru)</option>
                            <option value="success" {{ old('color')=='success' ? 'selected' : '' }}>🟢 Success (Hijau)</option>
                            <option value="warning" {{ old('color')=='warning' ? 'selected' : '' }}>🟡 Warning (Kuning)</option>
                            <option value="danger" {{ old('color')=='danger' ? 'selected' : '' }}>🔴 Danger (Merah)</option>
                            <option value="info" {{ old('color')=='info' ? 'selected' : '' }}>🔵 Info (Biru Muda)</option>
                            <option value="secondary" {{ old('color')=='secondary' ? 'selected' : '' }}>⚪ Secondary (Abu)</option>
                            <option value="dark" {{ old('color')=='dark' ? 'selected' : '' }}>⚫ Dark (Hitam)</option>
                        </select>
                        @error('color')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="mt-2 d-flex align-items-center gap-2">
                            <span class="preview-color-box" id="colorPreview"></span>
                            <small class="text-muted">Pratinjau warna</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="is_active" class="form-check-input" value="1" id="isActive" {{ old('is_active', '1') ? 'checked' : '' }}>
                            <label class="form-check-label" for="isActive">Aktif</label>
                        </div>
                        <small class="text-muted">Nonaktifkan jika jenis surat tidak digunakan</small>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="multi_enabled" class="form-check-input" value="1" id="multiEnabled" {{ old('multi_enabled', '1') ? 'checked' : '' }}>
                            <label class="form-check-label" for="multiEnabled">Aktifkan Multi-Surat</label>
                        </div>
                        <small class="text-muted">Izinkan jenis surat ini dikirim ke banyak penerima sekaligus</small>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <a href="{{ route('e-sign.jenis-surat') }}" class="btn btn-secondary w-50">
                    <i class="ri-arrow-left-line me-1"></i> Kembali
                </a>
                <button type="submit" class="btn btn-primary w-50">
                    <i class="ri-save-line me-1"></i> Simpan
                </button>
            </div>
        </div>
    </div>
</form>
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
    $(document).ready(function() {
        // Color preview
        function updateColorPreview() {
            const color = $('select[name="color"]').val();
            const colorMap = {
                'primary': '#405189',
                'success': '#0ab39c',
                'warning': '#f7b84b',
                'danger': '#f06548',
                'info': '#299cdb',
                'secondary': '#adb5bd',
                'dark': '#1e293b',
            };
            $('#colorPreview').css('background-color', colorMap[color] || '#405189');
        }
        $('select[name="color"]').on('change', updateColorPreview);
        updateColorPreview();
    });
</script>
@endsection
