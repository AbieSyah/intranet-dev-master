@extends('layouts.master')
@section('title', 'Edit Template - E-Sign')
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

    /* ============================================================
       A4 Document Editor — tampilan halaman kertas di layar
       ============================================================ */

    /* Wrapper latar abu-abu di luar halaman */
    .editor-a4-wrapper {
        background: #e8eaed;
        border-radius: 12px;
        padding: 2rem 1rem;
        display: flex;
        justify-content: center;
    }

    /* Toolbar sticky */
    .editor-a4-wrapper .ck-editor__top {
        position: sticky;
        top: 0;
        z-index: 50;
    }

    /* Area editable — seperti halaman A4 */
    /* Padding default 2cm 2.5cm, akan di-override JS jika ada margin */
    .editor-a4-wrapper .ck-editor__editable {
        background: #ffffff !important;
        width: 210mm !important;
        min-height: 700px !important;
        max-height: none !important;
        padding: 2cm 2.5cm !important;
        margin: 0 auto !important;
        box-shadow: 0 2px 12px rgba(0,0,0,0.10), 0 1px 3px rgba(0,0,0,0.06) !important;
        border: none !important;
        font-family: Calibri, Arial, sans-serif !important;
        font-size: 12pt !important;
        line-height: 1.5 !important;
    }

    /* Paragraph spacing untuk surat resmi — seperti Word */
    .editor-a4-wrapper .ck-editor__editable p {
        margin: 0 0 0.35em 0 !important;
        padding: 0 !important;
        line-height: 1.5 !important;
    }

    /* Heading dalam surat — spacing rapat seperti Word */
    .editor-a4-wrapper .ck-editor__editable h1,
    .editor-a4-wrapper .ck-editor__editable h2,
    .editor-a4-wrapper .ck-editor__editable h3,
    .editor-a4-wrapper .ck-editor__editable h4,
    .editor-a4-wrapper .ck-editor__editable h5 {
        font-family: Calibri, Arial, sans-serif !important;
        margin: 0.5em 0 0.25em 0 !important;
        line-height: 1.3 !important;
    }

    /* Hilangkan border default CKEditor */
    .editor-a4-wrapper .ck.ck-editor__editable_inline {
        border: none !important;
    }

    /* Table styling — seperti Word */
    .editor-a4-wrapper .ck-editor__editable figure.table {
        width: 100% !important;
        margin: 12px 0 !important;
    }
    .editor-a4-wrapper .ck-editor__editable figure.table table {
        width: 100% !important;
        border-collapse: collapse !important;
        font-size: 11pt !important;
    }
    .editor-a4-wrapper .ck-editor__editable figure.table table td,
    .editor-a4-wrapper .ck-editor__editable figure.table table th {
        border: 1px solid #212529 !important;
        padding: 6px 8px !important;
        vertical-align: top !important;
    }
    .editor-a4-wrapper .ck-editor__editable figure.table table th {
        background: #f0f0f0 !important;
        font-weight: 700 !important;
    }

    /* Pastikan toolbar rapi */
    .editor-a4-wrapper .ck.ck-toolbar {
        border: none !important;
        border-bottom: 1px solid #dadce0 !important;
        border-radius: 8px 8px 0 0 !important;
        background: #f8f9fa !important;
    }

    /* Responsive: jika layar kurang dari 210mm, gunakan lebar penuh */
    @media (max-width: 900px) {
        .editor-a4-wrapper .ck-editor__editable {
            width: 100% !important;
            padding: 1.5cm 1.5cm !important;
        }
    }

    /* ============================================================
       A4 Preview — tampilan seperti kertas di layar
       ============================================================ */
    .preview-a4-page {
        background: #ffffff;
        width: 210mm;
        min-height: 297mm;
        overflow: hidden;
        position: relative;
        margin-bottom: 1.5em;
        padding: 1cm 2.5cm 2cm 2.5cm;
        box-shadow: 0 2px 12px rgba(0,0,0,0.10), 0 1px 3px rgba(0,0,0,0.06);
        font-family: Calibri, Arial, sans-serif;
        font-size: 12pt;
        line-height: 1.5;
        color: #212529;
    }
    .preview-a4-page p {
        margin: 0 0 0.75em 0;
        padding: 0;
    }
    .preview-a4-page h1,
    .preview-a4-page h2,
    .preview-a4-page h3,
    .preview-a4-page h4,
    .preview-a4-page h5 {
        font-family: Calibri, Arial, sans-serif;
    }
    .preview-a4-page figure.table {
        width: 100%;
        margin: 12px 0;
    }
    .preview-a4-page figure.table table {
        width: 100%;
        border-collapse: collapse;
        font-size: 11pt;
    }
    .preview-a4-page figure.table table td,
    .preview-a4-page figure.table table th {
        border: 1px solid #212529 !important;
        padding: 6px 8px !important;
        vertical-align: top !important;
    }
    .preview-a4-page figure.table table th {
        background: #f0f0f0 !important;
        font-weight: 700 !important;
    }

    /* Kop surat (letterhead) — gambar penuh */
    .preview-kop {
        margin-left: -0.8cm;
        margin-right: -0.8cm;
        margin-bottom: 0.3em;
        margin-top: -0.6cm;
    }
    .kop-img-full {
        width: 85%;
        height: auto;
        display: block;
        margin: 0 auto;
    }

    /* Placeholder highlight */
    .preview-placeholder {
        display: inline-block;
        background: #fff3cd;
        color: #856404;
        padding: 0 6px;
        border-radius: 3px;
        font-family: "Courier New", monospace;
        font-size: 11pt;
        border: 1px dashed #ffc107;
    }

    /* ============================================================
       Placeholder Panel
       ============================================================ */
    .placeholder-panel {
        border: none;
        border-radius: 14px;
        position: sticky;
        top: 1rem;
    }
    .placeholder-panel .card-body {
        padding: 1.25rem;
    }
    .placeholder-group-title {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6c757d;
        margin-bottom: 0.5rem;
        padding-bottom: 0.25rem;
        border-bottom: 1px solid #e9ecef;
    }
    .placeholder-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.375rem 0.75rem;
        font-size: 12px;
        font-weight: 500;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        background: #fff;
        color: #405189;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .placeholder-btn:hover {
        background: #f0f4ff;
        border-color: #405189;
        transform: translateY(-1px);
    }
    .placeholder-btn:active {
        transform: translateY(0);
    }
    .placeholder-btn i {
        font-size: 14px;
        opacity: 0.7;
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <div class="flex-shrink-0" style="width:38px;height:38px;background:linear-gradient(135deg,#0ab39c,#405189);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                    <i class="ri-file-copy-2-line text-white fs-18"></i>
                </div>
                <div>
                    <h4 class="mb-sm-0">Edit Template</h4>
                    <small class="text-muted">{{ $template->title }}</small>
                </div>
            </div>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">E-Sign Management</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('e-sign.templates', ['letter_type_id' => $template->letter_type_id]) }}">Template Surat</a></li>
                    <li class="breadcrumb-item active">Edit Template</li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<form action="{{ route('e-sign.templates.update', $template->id) }}" method="POST">
    @csrf
    @method('PUT')
    <!-- Baris 1: Informasi Template -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card form-card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Informasi Template</h5>

                    <div class="row mb-3">
                        <label class="col-md-3 col-form-label">Jenis Surat <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" value="{{ $template->letterType->name ?? $template->jenis_surat_label }}" readonly>
                            <small class="text-muted">Jenis surat tidak dapat diubah. Buat template baru jika diperlukan.</small>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-md-3 col-form-label">Nama Template <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                value="{{ old('title', $template->title) }}" required>
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-md-3 col-form-label">Aktif?</label>
                        <div class="col-md-9">
                            <div class="form-check form-switch mt-1">
                                <input type="checkbox" name="is_active" class="form-check-input" value="1" id="isActive"
                                    {{ old('is_active', $template->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="isActive">Jadikan template aktif</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card form-card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Informasi Template</h5>
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted ps-0" style="width:90px;">Jenis Surat</td>
                            <td class="fw-semibold">{{ $template->letterType->name ?? $template->jenis_surat_label }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0">Status</td>
                            <td>{!! $template->status_badge !!}</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0">Dibuat</td>
                            <td>{{ $template->created_at ? $template->created_at->format('d M Y H:i') : '-' }}</td>
                        </tr>
                        @if($template->creator)
                        <tr>
                            <td class="text-muted ps-0">Oleh</td>
                            <td>{{ $template->creator->name }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            {{-- Hidden fields untuk Page Layout — diisi dari modal CKEditor --}}
            <input type="hidden" name="page_size" id="inputPageSize" value="{{ $template->page_size ?? 'A4' }}">
            <input type="hidden" name="page_margin_top" id="inputMarginTop" value="{{ $template->page_margin_top ?? 25 }}">
            <input type="hidden" name="page_margin_bottom" id="inputMarginBottom" value="{{ $template->page_margin_bottom ?? 25 }}">
            <input type="hidden" name="page_margin_left" id="inputMarginLeft" value="{{ $template->page_margin_left ?? 25 }}">
            <input type="hidden" name="page_margin_right" id="inputMarginRight" value="{{ $template->page_margin_right ?? 25 }}">

            <div class="d-flex gap-2">
                <a href="{{ route('e-sign.templates', ['letter_type_id' => $template->letter_type_id]) }}" class="btn btn-secondary w-50">
                    <i class="ri-arrow-left-line me-1"></i> Kembali
                </a>
                <button type="submit" class="btn btn-primary w-50">
                    <i class="ri-save-line me-1"></i> Simpan
                </button>
            </div>
        </div>
    </div>

    <!-- Baris 2: CKEditor 5 + Placeholder Panel -->
    <div class="row">
        <div class="col-lg-9">
            <div class="editor-a4-wrapper">
                <textarea name="content" id="templateContent">{{ old('content', $template->content) }}</textarea>
            </div>
            @error('content')<div class="invalid-feedback d-block mt-2">{{ $message }}</div>@enderror
        </div>

        <div class="col-lg-3">
            @php $placeholders = config('esign.placeholders', []); @endphp
            <div class="card placeholder-panel">
                <div class="card-body">
                    <h6 class="fw-semibold mb-3" style="font-size:14px;">
                        <i class="ri-puzzle-2-line me-1"></i> Placeholder
                    </h6>
                    <p class="text-muted small mb-3">
                        Klik untuk menyisipkan placeholder pada posisi kursor.
                    </p>

                    {{-- Employee (pilih nomor + dropdown field) --}}
                    @php
                        $allEmployeeFields = [
                            'nik' => 'NIK',
                            'no_ktp' => 'No. KTP',
                            'fullname' => 'Nama Lengkap',
                            'email' => 'Email',
                            'addressktp' => 'Alamat KTP',
                            'birthplace' => 'Tempat Lahir',
                            'birthdate' => 'Tanggal Lahir',
                            'gender' => 'Jenis Kelamin',
                            'religion' => 'Agama',
                            'marital' => 'Status Perkawinan',
                            'hp' => 'No. HP',
                            'joindate' => 'Tanggal Masuk',
                            'enddate' => 'Tanggal Keluar',
                            'status' => 'Status Karyawan',
                            'work_location' => 'Lokasi Kerja',
                            'domicile_address' => 'Alamat Domisili',
                            'emergency_contact' => 'Kontak Darurat',
                            'emergency_contact_relation' => 'Hubungan Kontak Darurat',
                            'emergency_contact_handphone' => 'HP Kontak Darurat',
                            'emergency_contact_address' => 'Alamat Kontak Darurat',
                            'permanent_startdate' => 'Tanggal Jadi Tetap',
                            'iso_position' => 'Posisi ISO',
                            'cost_center' => 'Cost Center',
                            'last_education' => 'Pendidikan Terakhir',
                            'major_last_education' => 'Jurusan',
                            'last_education_institutional' => 'Institusi Pendidikan',
                            'tax_dependents' => 'PTKP',
                            'npwp' => 'NPWP',
                            'outsourcing_vendor' => 'Vendor Outsourcing',
                            'bpjs_kesehatan' => 'BPJS Kesehatan',
                            'bpjs_ketenagakerjaan' => 'BPJS Ketenagakerjaan',
                            'latest_agreement_number' => 'No. Perjanjian Terbaru',
                            'active_agreement_number' => 'No. Perjanjian Aktif',
                            'bank_name' => 'Nama Bank',
                            'bank_account' => 'No. Rekening',
                            'bank_account_holder' => 'Pemilik Rekening',
                            'blood_type' => 'Golongan Darah',
                            'contract_startdate' => 'Tanggal Mulai Kontrak',
                            'contract_number' => 'No. Kontrak',
                            'department' => 'Departemen',
                            'position' => 'Jabatan',
                            'section' => 'Section',
                            'level' => 'Level',
                            'area' => 'Area',
                            'building' => 'Building',
                        ];
                    @endphp
                    <div class="mb-3">
                        <div class="placeholder-group-title mb-2"><i class="ri-user-line me-1"></i> Employee</div>
                        <div class="d-flex align-items-start gap-1 mb-2" style="flex-wrap:wrap;">
                            <span style="font-size:11px;font-weight:500;color:#6c757d;line-height:24px;white-space:nowrap;">Nomor:</span>
                            <div class="d-flex gap-1 flex-wrap" id="empNumberGroup">
                                <button type="button" class="btn btn-outline-primary active" data-emp="1" style="font-size:11px;padding:1px 10px;">1</button>
                                <button type="button" class="btn btn-outline-primary" data-emp="2" style="font-size:11px;padding:1px 10px;">2</button>
                                <button type="button" class="btn btn-outline-primary" data-emp="3" style="font-size:11px;padding:1px 10px;">3</button>
                            </div>
                            <div class="d-flex gap-1" style="white-space:nowrap;">
                                <button type="button" class="btn btn-sm btn-outline-primary" id="btnAddEmpNumber" style="font-size:11px;padding:1px 8px;" title="Tambah nomor employee">
                                    <i class="ri-add-line"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger" id="btnRemoveEmpNumber" style="font-size:11px;padding:1px 8px;display:none;" title="Hapus nomor terakhir">
                                    <i class="ri-subtract-line"></i>
                                </button>
                            </div>
                        </div>
                        <div class="input-group input-group-sm mb-1">
                            <select class="form-select" id="selectEmpField" style="font-size:11px;">
                                <option value="">-- Pilih Field --</option>
                                @foreach($allEmployeeFields as $col => $label)
                                <option value="{{ $col }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            <button type="button" class="btn btn-primary" id="btnInsertEmpField" style="font-size:11px;">
                                <i class="ri-add-line"></i> Tambah
                            </button>
                        </div>
                    </div>

                    {{-- Surat: Tanggal 1, Tanggal 2, ... --}}
                    <div class="mb-3">
                        <div class="placeholder-group-title"><i class="ri-file-text-line me-1"></i> Surat</div>
                        <div class="d-flex align-items-start gap-1 mb-1" style="flex-wrap:wrap;">
                            <span style="font-size:11px;font-weight:500;color:#6c757d;line-height:24px;white-space:nowrap;">Tanggal:</span>
                            <div class="d-flex gap-1 flex-wrap" id="tanggalGroup">
                                @php $openT = '{' . '{'; $closeT = '}' . '}'; @endphp
                                <button type="button" class="btn btn-outline-primary" style="font-size:11px;padding:1px 10px;" onclick="insertPlaceholder('{{$openT}}tanggal_1{{$closeT}}')">
                                    <i class="ri-calendar-line"></i> Tanggal 1
                                </button>
                                <button type="button" class="btn btn-outline-primary" style="font-size:11px;padding:1px 10px;" onclick="insertPlaceholder('{{$openT}}tanggal_2{{$closeT}}')">
                                    <i class="ri-calendar-line"></i> Tanggal 2
                                </button>
                            </div>
                            <div class="d-flex gap-1" style="white-space:nowrap;">
                                <button type="button" class="btn btn-sm btn-outline-primary" id="btnAddTanggal" style="font-size:11px;padding:1px 8px;" title="Tambah tanggal">
                                    <i class="ri-add-line"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger" id="btnRemoveTanggal" style="font-size:11px;padding:1px 8px;display:none;" title="Hapus tanggal terakhir">
                                    <i class="ri-subtract-line"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Tanda Tangan: Sign 1, Sign 2, Sign 3 --}}
                    <div class="mb-3">
                        <div class="placeholder-group-title"><i class="ri-pencil-line me-1"></i> Tanda Tangan</div>
                        <div class="d-flex flex-wrap gap-1">
                            <button type="button" class="btn btn-outline-primary" style="font-size:11px;padding:1px 10px;" onclick="insertPH('sign_employee1')">
                                <i class="ri-pencil-line"></i> Sign 1
                            </button>
                            <button type="button" class="btn btn-outline-primary" style="font-size:11px;padding:1px 10px;" onclick="insertPH('sign_employee2')">
                                <i class="ri-pencil-line"></i> Sign 2
                            </button>
                            <button type="button" class="btn btn-outline-primary" style="font-size:11px;padding:1px 10px;" onclick="insertPH('sign_employee3')">
                                <i class="ri-pencil-line"></i> Sign 3
                            </button>
                        </div>
                    </div>

                    {{-- Preview --}}
                    <hr class="my-3">
                    <h6 class="fw-semibold mb-2" style="font-size:13px;">
                        <i class="ri-eye-line me-1"></i> Preview
                    </h6>
                    <button type="button" class="btn btn-info w-100" id="btnPreviewTemplate" style="font-size:12px;">
                        <i class="ri-eye-line me-1"></i> Lihat Preview
                    </button>

                    <hr class="my-3">
                    <div class="small text-muted">
                        <i class="ri-information-line me-1"></i> Placeholder akan diganti dengan data real saat Generate Surat.
                    </div>
                </div>
            </div>

            {{-- Modal Preview --}}
            <div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><i class="ri-eye-line me-1"></i> Preview Template</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body" style="background:#e8eaed;padding:2rem;">
                            <div style="display:flex;justify-content:center;">
                                <div class="preview-a4-page" id="previewModalBody">
                                    <div class="text-center text-muted py-5">
                                        <i class="ri-loader-2-line fs-1"></i>
                                        <p class="mt-2">Memuat preview...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <small class="text-muted me-auto">
                                <i class="ri-information-line me-1"></i> Tanda @{{variable}} akan diganti dengan data real saat generate surat.
                            </small>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

{{-- ================================================================ --}}
{{-- MODAL: Page Layout — diakses dari toolbar CKEditor atau tombol sidebar --}}
{{-- ================================================================ --}}
<div class="modal fade" id="pageLayoutModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:480px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ri-layout-line me-1"></i> Page Layout
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-3">
                    Atur margin dan ukuran kertas. Hasilnya akan sama seperti saat generate PDF nanti.
                </p>

                {{-- Ukuran Kertas --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Ukuran Kertas</label>
                    <select class="form-select" id="modalPageSize">
                        <option value="A4" {{ ($template->page_size ?? 'A4') == 'A4' ? 'selected' : '' }}>A4 (210 × 297 mm)</option>
                        <option value="Letter" {{ ($template->page_size ?? '') == 'Letter' ? 'selected' : '' }}>Letter (216 × 279 mm)</option>
                        <option value="Legal" {{ ($template->page_size ?? '') == 'Legal' ? 'selected' : '' }}>Legal (216 × 356 mm)</option>
                    </select>
                </div>

                {{-- Margin Grid --}}
                <label class="form-label fw-semibold">Margin (mm)</label>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label" style="font-size:11px;">Atas</label>
                        <input type="number" class="form-control modal-margin" id="modalMarginTop"
                            value="{{ $template->page_margin_top ?? 25 }}" min="5" max="100" step="1">
                    </div>
                    <div class="col-6">
                        <label class="form-label" style="font-size:11px;">Bawah</label>
                        <input type="number" class="form-control modal-margin" id="modalMarginBottom"
                            value="{{ $template->page_margin_bottom ?? 25 }}" min="5" max="100" step="1">
                    </div>
                    <div class="col-6">
                        <label class="form-label" style="font-size:11px;">Kiri</label>
                        <input type="number" class="form-control modal-margin" id="modalMarginLeft"
                            value="{{ $template->page_margin_left ?? 25 }}" min="5" max="100" step="1">
                    </div>
                    <div class="col-6">
                        <label class="form-label" style="font-size:11px;">Kanan</label>
                        <input type="number" class="form-control modal-margin" id="modalMarginRight"
                            value="{{ $template->page_margin_right ?? 25 }}" min="5" max="100" step="1">
                    </div>
                </div>

                {{-- Preview ikonik: visual A4 dengan margin --}}
                <div class="border rounded p-3 bg-light">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width:60px;height:80px;background:#fff;border:2px solid #405189;border-radius:3px;position:relative;flex-shrink:0;">
                            <div id="visualMarginTop" style="position:absolute;top:0;left:0;right:0;height:4px;background:#0ab39c;opacity:0.5;"></div>
                            <div id="visualMarginBottom" style="position:absolute;bottom:0;left:0;right:0;height:4px;background:#0ab39c;opacity:0.5;"></div>
                            <div id="visualMarginLeft" style="position:absolute;top:0;bottom:0;left:0;width:4px;background:#0ab39c;opacity:0.5;"></div>
                            <div id="visualMarginRight" style="position:absolute;top:0;bottom:0;right:0;width:4px;background:#0ab39c;opacity:0.5;"></div>
                            <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);font-size:8px;font-weight:700;color:#405189;white-space:nowrap;" id="visualPageSizeLabel">A4</div>
                        </div>
                        <div class="small text-muted">
                            <span id="visualMarginInfo">Atas: 25 | Bawah: 25 | Kiri: 25 | Kanan: 25</span><br>
                            <span id="visualSizeInfo">A4 (210 × 297 mm)</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnApplyPageLayout">
                    <i class="ri-check-line me-1"></i> Terapkan
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
@vite('resources/js/ckeditor.js')
<script>
    const empDbFields = @json($allEmployeeFields);
    let currentEmpNum = 1;

    // ================================================================
    // PAGE LAYOUT MODAL — update visual preview & sync ke hidden fields
    // ================================================================
    function updatePageLayoutVisual() {
        var top = parseInt(document.getElementById('modalMarginTop').value) || 25;
        var bottom = parseInt(document.getElementById('modalMarginBottom').value) || 25;
        var left = parseInt(document.getElementById('modalMarginLeft').value) || 25;
        var right = parseInt(document.getElementById('modalMarginRight').value) || 25;
        var size = document.getElementById('modalPageSize').value;

        // Update visual margin bars (proporsional)
        var maxVal = 100;
        document.getElementById('visualMarginTop').style.height = Math.min(top / maxVal * 20, 16) + 'px';
        document.getElementById('visualMarginBottom').style.height = Math.min(bottom / maxVal * 20, 16) + 'px';
        document.getElementById('visualMarginLeft').style.width = Math.min(left / maxVal * 14, 12) + 'px';
        document.getElementById('visualMarginRight').style.width = Math.min(right / maxVal * 14, 12) + 'px';

        // Update label
        var sizeLabels = { 'A4': 'A4 (210 × 297 mm)', 'Letter': 'Letter (216 × 279 mm)', 'Legal': 'Legal (216 × 356 mm)' };
        document.getElementById('visualPageSizeLabel').textContent = size;
        document.getElementById('visualSizeInfo').textContent = sizeLabels[size] || 'A4 (210 × 297 mm)';
        document.getElementById('visualMarginInfo').textContent =
            'Atas: ' + top + ' | Bawah: ' + bottom + ' | Kiri: ' + left + ' | Kanan: ' + right;

        // Update CKEditor editable padding jika editor sudah siap
        if (window.ckEditorInstance) {
            var editable = window.ckEditorInstance.editing.view.getDomRoot();
            if (editable) {
                editable.style.padding = top + 'mm ' + right + 'mm ' + bottom + 'mm ' + left + 'mm';
            }
        }
    }

    // Event listeners untuk modal
    document.addEventListener('DOMContentLoaded', function() {
        // Update visual saat input berubah
        document.querySelectorAll('.modal-margin').forEach(function(el) {
            el.addEventListener('input', updatePageLayoutVisual);
        });
        document.getElementById('modalPageSize').addEventListener('change', updatePageLayoutVisual);

        // Tombol Terapkan: simpan ke hidden fields
        document.getElementById('btnApplyPageLayout').addEventListener('click', function() {
            var top = document.getElementById('modalMarginTop').value;
            var bottom = document.getElementById('modalMarginBottom').value;
            var left = document.getElementById('modalMarginLeft').value;
            var right = document.getElementById('modalMarginRight').value;
            var size = document.getElementById('modalPageSize').value;

            document.getElementById('inputPageSize').value = size;
            document.getElementById('inputMarginTop').value = top;
            document.getElementById('inputMarginBottom').value = bottom;
            document.getElementById('inputMarginLeft').value = left;
            document.getElementById('inputMarginRight').value = right;

            // Tutup modal
            var modal = bootstrap.Modal.getInstance(document.getElementById('pageLayoutModal'));
            if (modal) modal.hide();
        });

        // Reset visual saat modal dibuka
        document.getElementById('pageLayoutModal').addEventListener('shown.bs.modal', function() {
            updatePageLayoutVisual();
        });
        document.querySelectorAll('#empNumberGroup .btn[data-emp]').forEach(function(btn) {
            btn.addEventListener('click', function() {
                document.querySelectorAll('#empNumberGroup .btn[data-emp]').forEach(function(b) {
                    b.classList.remove('active');
                });
                this.classList.add('active');
                currentEmpNum = parseInt(this.dataset.emp);
            });
        });

        document.getElementById('btnAddEmpNumber').addEventListener('click', function() {
            var btns = document.querySelectorAll('#empNumberGroup .btn[data-emp]');
            var maxNum = 1;
            btns.forEach(function(b) { var n = parseInt(b.dataset.emp); if (n > maxNum) maxNum = n; });
            var newNum = maxNum + 1;
            var newBtn = document.createElement('button');
            newBtn.type = 'button';
            newBtn.className = 'btn btn-outline-primary';
            newBtn.style.cssText = 'font-size:11px;padding:1px 10px;';
            newBtn.dataset.emp = newNum;
            newBtn.textContent = newNum;
            newBtn.addEventListener('click', function() {
                document.querySelectorAll('#empNumberGroup .btn[data-emp]').forEach(function(b) { b.classList.remove('active'); });
                this.classList.add('active');
                currentEmpNum = parseInt(this.dataset.emp);
            });
            document.getElementById('empNumberGroup').appendChild(newBtn);
            newBtn.click();
            document.getElementById('btnRemoveEmpNumber').style.display = '';
        });

        document.getElementById('btnRemoveEmpNumber').addEventListener('click', function() {
            var btns = document.querySelectorAll('#empNumberGroup .btn[data-emp]');
            if (btns.length <= 3) return;
            var lastBtn = btns[btns.length - 1];
            var lastNum = parseInt(lastBtn.dataset.emp);
            lastBtn.remove();
            if (currentEmpNum === lastNum) {
                var btn1 = document.querySelector('#empNumberGroup .btn[data-emp="1"]');
                if (btn1) btn1.click();
            }
            if (document.querySelectorAll('#empNumberGroup .btn[data-emp]').length <= 3) {
                document.getElementById('btnRemoveEmpNumber').style.display = 'none';
            }
        });

        document.getElementById('btnInsertEmpField').addEventListener('click', function() {
            var sel = document.getElementById('selectEmpField');
            var col = sel.value;
            if (!col) { alert('Silakan pilih field terlebih dahulu.'); return; }
            var key = 'employee' + currentEmpNum + '_' + col;
            insertPH(key);
            sel.value = '';
        });

        document.getElementById('selectEmpField').addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('btnInsertEmpField').click();
            }
        });

        document.getElementById('btnAddTanggal').addEventListener('click', function() {
            var btns = document.querySelectorAll('#tanggalGroup .placeholder-btn-sm');
            var maxNum = 2;
            btns.forEach(function(b) {
                var match = b.textContent.trim().match(/Tanggal (\d+)/);
                if (match) { var n = parseInt(match[1]); if (n > maxNum) maxNum = n; }
            });
            var newNum = maxNum + 1;
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'placeholder-btn-sm';
            var openB = '{' + '{';
            var closeB = '}' + '}';
            btn.className = 'btn btn-outline-primary';
            btn.style.cssText = 'font-size:11px;padding:1px 10px;';
            btn.setAttribute('onclick', "insertPlaceholder('" + openB + "tanggal_" + newNum + closeB + "')");
            btn.innerHTML = '<i class="ri-calendar-line"></i> Tanggal ' + newNum;
            document.getElementById('tanggalGroup').appendChild(btn);
            document.getElementById('btnRemoveTanggal').style.display = '';
        });

        document.getElementById('btnRemoveTanggal').addEventListener('click', function() {
            var btns = document.querySelectorAll('#tanggalGroup .btn');
            if (btns.length <= 2) return;
            btns[btns.length - 1].remove();
            if (document.querySelectorAll('#tanggalGroup .btn').length <= 2) {
                document.getElementById('btnRemoveTanggal').style.display = 'none';
            }
        });

    });

    function insertPH(key) {
        var a = '{' + '{', b = '}' + '}';
        insertPlaceholder(a + key + b);
    }

    // Preview Template
    document.getElementById('btnPreviewTemplate').addEventListener('click', function() {
        var content = '';
        if (window.ckEditorInstance) {
            content = window.ckEditorInstance.getData();
        } else {
            content = document.getElementById('templateContent').value;
        }
        if (!content.trim()) { alert('Template masih kosong.'); return; }

        // Ambil nilai margin dari hidden fields (diisi dari modal Page Layout)
        var marginTop = document.getElementById('inputMarginTop')?.value || 25;
        var marginBottom = document.getElementById('inputMarginBottom')?.value || 25;
        var marginLeft = document.getElementById('inputMarginLeft')?.value || 25;
        var marginRight = document.getElementById('inputMarginRight')?.value || 25;
        var pageSize = document.getElementById('inputPageSize')?.value || 'A4';

        // Ukuran kertas dalam mm
        var pageWidth = 210, pageHeight = 297;
        if (pageSize === 'Letter') { pageWidth = 216; pageHeight = 279; }
        else if (pageSize === 'Legal') { pageWidth = 216; pageHeight = 356; }

        var preview = content;
        var openB = '{' + '{';
        var closeB = '}' + '}';

        function signPreviewHtml(signLabel, name, position) {
            if (!name) return '[Tanda Tangan ' + signLabel + ']';
            var posText = position || '_______________';
            return '<div style="text-align:center;margin-top:16px;">' +
                '<div style="font-weight:700;font-size:13px;margin-bottom:4px;color:#1e293b;">' + signLabel + '</div>' +
                '<div style="width:120px;height:80px;border:2px dashed #adb5bd;border-radius:8px;display:flex;align-items:center;justify-content:center;margin:0 auto 8px;font-size:10px;color:#adb5bd;background:#f8f9fa;">' +
                '<span>QR Code<br>Digital Signature</span></div>' +
                '<div style="font-size:12px;font-weight:600;color:#212529;margin-bottom:2px;">' + name + '</div>' +
                '<div style="font-size:11px;color:#6c757d;">' + posText + '</div></div>';
        }

        var repl = [
            ['employee1_fullname', 'John Doe'], ['employee1_nik', 'NIK. 123456789'],
            ['employee1_position', 'Manager'], ['employee1_department', 'HRD'],
            ['employee_name', 'John Doe'], ['employee_nik', 'NIK. 123456789'],
            ['employee_position', 'Manager'], ['employee_department', 'HRD'],
            ['tanggal_1', '01 Januari 2026'], ['tanggal_2', '01 Januari 2026'],
            ['tanggal_mulai', '01 Januari 2026'], ['tanggal_akhir', '31 Desember 2026'],
            ['today', '23 Juli 2026'], ['judul_surat', 'Contoh Judul Surat'],
            ['nomor_surat', 'XXX/2026/001'],
            ['sign_employee1', '[Tanda Tangan]'], ['sign_employee2', '[Tanda Tangan]'], ['sign_employee3', '[Tanda Tangan]'],
        ];
        for (var r = 0; r < repl.length; r++) {
            var re = new RegExp(openB + repl[r][0] + closeB, 'g');
            preview = preview.replace(re, repl[r][1]);
        }
        var reCatch = new RegExp(openB + '[a-z0-9_]+' + closeB, 'g');
        preview = preview.replace(reCatch, '[Data]');

        var logoUrl = '{{ url('') }}/assets/images/KOP-terbaru.png';

        // Highlight sisa placeholder yang belum diganti
        var reCatch = new RegExp('(' + openB + '[a-z0-9_]+' + closeB + ')', 'g');
        preview = preview.replace(reCatch, '<span class="preview-placeholder">$1</span>');

        var fullHtml =
            '<div class="preview-kop"><img src="' + logoUrl + '" alt="Kop Surat" class="kop-img-full"></div>' +
            '<div style="text-align:center;font-size:16px;font-weight:700;text-transform:uppercase;letter-spacing:1px;margin-bottom:4px;color:#1e293b;">{{ $template->letterType->name ?? 'Judul Surat' }}</div>' +
            '<div style="text-align:center;font-size:12px;color:#6c757d;margin-bottom:28px;font-weight:500;">Nomor: _______________</div>' +
            preview;

        var wrapper = document.getElementById('previewModalBody');
        // Terapkan margin dari form ke preview
        wrapper.style.padding = marginTop + 'mm ' + marginRight + 'mm ' + marginBottom + 'mm ' + marginLeft + 'mm';
        wrapper.style.width = pageWidth + 'mm';
        wrapper.style.minHeight = pageHeight + 'mm';
        wrapper.innerHTML = fullHtml;

        // Pagination jika konten lebih dari 1 halaman A4
        var maxH = pageHeight * 3.78;
        var allPages = [wrapper];
        var pageIndex = 0;
        while (pageIndex < allPages.length) {
            var curr = allPages[pageIndex];
            var safety = 0;
            while (curr.scrollHeight > maxH + 5 && safety < 50) {
                safety++;
                var kids = curr.children;
                if (kids.length <= 1) break;
                var last = kids[kids.length - 1];
                var next = allPages[pageIndex + 1];
                if (!next) {
                    next = document.createElement('div');
                    next.className = 'preview-a4-page';
                    next.style.marginTop = '0';
                    curr.parentNode.insertBefore(next, curr.nextSibling);
                    allPages.push(next);
                }
                next.insertBefore(last, next.firstChild);
            }
            pageIndex++;
        }

        var modal = new bootstrap.Modal(document.getElementById('previewModal'));
        modal.show();
    });
</script>
@endsection
