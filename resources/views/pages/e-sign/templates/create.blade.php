@extends('layouts.master')
@section('title', 'Tambah Template - E-Sign')
@section('link')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        padding: 2rem 1rem 0.5rem;
        display: flex;
        justify-content: center;
    }

    /* Container editor RoosterJS — susun toolbar & kertas ke tengah */
    .editor-a4-wrapper .rooster-wrapper {
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 100%;
    }

    /* Toolbar selebar A4 — sticky di bawah header (70px) agar tetap terlihat saat scroll */
    .editor-a4-wrapper .rooster-toolbar {
        width: 210mm;
        max-width: 100%;
        position: sticky;
        top: 70px;
        z-index: 1000;
    }

    /* Area editable — seperti halaman kertas A4 */
    /* Padding default 1cm 2.5cm, akan di-override JS (termasuk area kop & page-break) */
    .editor-a4-wrapper .rooster-editable {
        background: #ffffff !important;
        width: 210mm !important;
        max-width: 100% !important;
        min-height: 297mm !important;
        max-height: none !important;
        padding: 1cm 2.5cm 2cm 2.5cm !important;
        margin: 0 auto !important;
        box-shadow: 0 2px 12px rgba(0,0,0,0.10), 0 1px 3px rgba(0,0,0,0.06) !important;
        border: none !important;
        border-top: none !important;
        font-family: Calibri, Arial, sans-serif !important;
        font-size: 12pt !important;
        line-height: 1.5 !important;
    }

    /* Paragraph spacing untuk surat resmi — seperti Word */
    .editor-a4-wrapper .rooster-editable p {
        margin: 0 0 0.35em 0 !important;
        padding: 0 !important;
        line-height: 1.5 !important;
    }

    /* Heading dalam surat — spacing rapat seperti Word */
    .editor-a4-wrapper .rooster-editable h1,
    .editor-a4-wrapper .rooster-editable h2,
    .editor-a4-wrapper .rooster-editable h3,
    .editor-a4-wrapper .rooster-editable h4,
    .editor-a4-wrapper .rooster-editable h5 {
        font-family: Calibri, Arial, sans-serif !important;
        margin: 0.5em 0 0.25em 0 !important;
        line-height: 1.3 !important;
    }

    /* Table styling — seperti Word */
    .editor-a4-wrapper .rooster-editable table {
        width: 100% !important;
        border-collapse: collapse !important;
        font-size: 11pt !important;
    }
    .editor-a4-wrapper .rooster-editable table td,
    .editor-a4-wrapper .rooster-editable table th {
        border: 1px solid #212529 !important;
        padding: 6px 8px !important;
        vertical-align: top !important;
    }
    .editor-a4-wrapper .rooster-editable table th {
        background: #f0f0f0 !important;
        font-weight: 700 !important;
    }

    /* Responsive: jika layar kurang dari 210mm, gunakan lebar penuh */
    @media (max-width: 900px) {
        .editor-a4-wrapper .rooster-editable {
            width: 100% !important;
            padding: 1.5cm 1.5cm !important;
        }
        .editor-a4-wrapper .rooster-toolbar {
            width: 100% !important;
        }
    }

    /* ============================================================
       A4 Preview — tampilan seperti kertas di layar
       ============================================================ */
    .preview-a4-page {
        background: #ffffff;
        width: 210mm;
        box-sizing: border-box;
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
    /* Pemisah antar kertas (page break) */
    .preview-page-break {
        width: 70%;
        max-width: 420px;
        margin: 3rem auto 3rem auto;
        border-top: 2px dashed #cbd5e1;
        flex-shrink: 0;
    }
    /* Nomor halaman di pojok kanan bawah tiap kertas */
    .preview-page-number {
        position: absolute;
        bottom: 0.35cm;
        right: 1cm;
        font-size: 10pt;
        font-weight: 600;
        color: #6b7280;
        font-family: Calibri, Arial, sans-serif;
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

    /* Kop + judul + nomor pada preview — disamakan dengan preview editor surat */
    .company-header {
        text-align: center;
        margin-bottom: 6px;
    }
    .doc-title {
        text-align: center;
        font-size: 16px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin: 4px 0 2px 0;
        color: #1e293b;
    }
    .doc-number {
        text-align: center;
        font-size: 12px;
        color: #6c757d;
        margin: 0 0 24px 0;
        font-weight: 500;
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

    /* Gambaran area tanda tangan di preview */
    .preview-sign-placeholder {
        margin-top: 40px;
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 16px;
        width: 100%;
        page-break-inside: avoid;
    }
    .editor-sign-slot {
        display: flex;
        justify-content: center;
    }
    .editor-sign-box {
        width: 170px;
        padding: 10px;
        border: 1px dashed #adb5bd;
        border-radius: 8px;
        text-align: center;
        background: #ffffff;
    }
    .editor-sign-box-label {
        font-weight: 700;
        font-size: 11pt;
        color: #1e293b;
        margin-bottom: 6px;
    }
    .editor-sign-box-qr {
        width: 110px;
        height: 70px;
        border: 2px dashed #adb5bd;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 6px;
        font-size: 9px;
        color: #adb5bd;
        background: #ffffff;
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
    .placeholder-preview {
        font-family: "Courier New", monospace;
        font-size: 11px;
        color: #6c757d;
        background: #f8f9fa;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        display: none;
        margin-top: 0.25rem;
    }
    .placeholder-btn:hover + .placeholder-preview {
        display: inline-block;
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
                    <h4 class="mb-sm-0">Tambah Template</h4>
                    <small class="text-muted">Template Surat Digital Signature</small>
                </div>
            </div>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">E-Sign Management</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('e-sign.templates', $preselectedTypeId ? ['letter_type_id' => $preselectedTypeId] : []) }}">Template Surat</a></li>
                    <li class="breadcrumb-item active">Tambah Template</li>
                    <li class="breadcrumb-item active">Tambah</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<form action="{{ route('e-sign.templates.store') }}" method="POST" id="formTemplate">
    @csrf
    <input type="hidden" name="template_type" value="editor">
    <input type="hidden" name="sign_1" id="inputSign1" value="1">
    <input type="hidden" name="sign_2" id="inputSign2" value="0">
    <input type="hidden" name="sign_3" id="inputSign3" value="0">
    <input type="hidden" name="sign_1_is_recipient" id="inputRecipient1" value="0">
    <input type="hidden" name="sign_2_is_recipient" id="inputRecipient2" value="0">
    <input type="hidden" name="sign_3_is_recipient" id="inputRecipient3" value="0">
    <!-- Baris 1: Informasi Template -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card form-card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Informasi Template</h5>

                    @php $selectedType = $letterTypes->firstWhere('id', $preselectedTypeId); @endphp
                    <div class="row mb-3">
                        <label class="col-md-3 col-form-label">Jenis Surat <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <input type="hidden" name="letter_type_id" value="{{ $preselectedTypeId }}">
                            <div class="form-control-plaintext fw-semibold pt-1">
                                {{ $selectedType ? $selectedType->name . ' (' . $selectedType->prefix . ')' : '-' }}
                            </div>
                            @error('letter_type_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-md-3 col-form-label">Nama Template <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                value="{{ old('title') }}" placeholder="Contoh: Template PKWT 2026" required>
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-md-3 col-form-label">Aktif?</label>
                        <div class="col-md-9">
                            <div class="form-check form-switch mt-1">
                                <input type="checkbox" name="is_active" class="form-check-input" value="1" id="isActive" {{ old('is_active', '1') ? 'checked' : '' }}>
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
                    <h5 class="card-title mb-4">Informasi</h5>
                    <div class="text-muted small">
                        <p><i class="ri-information-line me-1"></i> Hanya satu template yang bisa menjadi aktif untuk setiap jenis surat.</p>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <a href="{{ route('e-sign.templates', $preselectedTypeId ? ['letter_type_id' => $preselectedTypeId] : []) }}" class="btn btn-secondary w-50">
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
                <textarea name="content" id="templateContent">{{ old('content') }}</textarea>
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
                            'nik'=>'NIK','no_ktp'=>'No. KTP','fullname'=>'Nama Lengkap','email'=>'Email','addressktp'=>'Alamat KTP',
                            'birthplace'=>'Tempat Lahir','birthdate'=>'Tanggal Lahir','gender'=>'Jenis Kelamin','religion'=>'Agama',
                            'marital'=>'Status Perkawinan','hp'=>'No. HP','joindate'=>'Tanggal Masuk','enddate'=>'Tanggal Keluar',
                            'status'=>'Status Karyawan','work_location'=>'Lokasi Kerja','domicile_address'=>'Alamat Domisili',
                            'emergency_contact'=>'Kontak Darurat','emergency_contact_relation'=>'Hub. Kontak Darurat',
                            'emergency_contact_handphone'=>'HP Kontak Darurat','emergency_contact_address'=>'Alamat Kontak Darurat',
                            'permanent_startdate'=>'Tgl Jadi Tetap','iso_position'=>'Posisi ISO','cost_center'=>'Cost Center',
                            'last_education'=>'Pendidikan','major_last_education'=>'Jurusan','last_education_institutional'=>'Institusi',
                            'tax_dependents'=>'PTKP','npwp'=>'NPWP','outsourcing_vendor'=>'Vendor Outsourcing',
                            'bpjs_kesehatan'=>'BPJS Kesehatan','bpjs_ketenagakerjaan'=>'BPJS Ketenagakerjaan',
                            'latest_agreement_number'=>'No. Perjanjian Terbaru','active_agreement_number'=>'No. Perjanjian Aktif',
                            'bank_name'=>'Nama Bank','bank_account'=>'No. Rekening','bank_account_holder'=>'Pemilik Rekening',
                            'blood_type'=>'Gol. Darah','contract_startdate'=>'Tgl Mulai Kontrak','contract_number'=>'No. Kontrak',
                            'department'=>'Departemen','position'=>'Jabatan','section'=>'Section','level'=>'Level',
                            'area'=>'Area','building'=>'Building',
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

                    {{-- Surat: Nomor Surat + Tanggal --}}
                    @php $openT = '{' . '{'; $closeT = '}' . '}'; @endphp
                    <div class="mb-3">
                        <div class="placeholder-group-title"><i class="ri-file-text-line me-1"></i> Surat</div>
                        <div class="d-flex align-items-start gap-1 mb-1" style="flex-wrap:wrap;">
                            <span style="font-size:11px;font-weight:500;color:#6c757d;line-height:24px;white-space:nowrap;">Nomor:</span>
                            <div class="d-flex gap-1 flex-wrap">
                                <button type="button" class="btn btn-outline-primary" style="font-size:11px;padding:1px 10px;" onclick="insertPlaceholder('{{$openT}}nomor_surat{{$closeT}}')">
                                    <i class="ri-hashtag"></i> Nomor Surat
                                </button>
                            </div>
                        </div>
                        <div class="d-flex align-items-start gap-1 mb-1" style="flex-wrap:wrap;">
                            <span style="font-size:11px;font-weight:500;color:#6c757d;line-height:24px;white-space:nowrap;">Tanggal:</span>
                            <div class="d-flex gap-1 flex-wrap" id="tanggalGroup">
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
                            <button type="button" class="btn btn-outline-primary sign-toggle-btn" data-sign="1" style="font-size:11px;padding:1px 10px;">
                                <i class="ri-pencil-line"></i> Sign 1
                            </button>
                            <button type="button" class="btn btn-outline-primary sign-toggle-btn" data-sign="2" style="font-size:11px;padding:1px 10px;">
                                <i class="ri-pencil-line"></i> Sign 2
                            </button>
                            <button type="button" class="btn btn-outline-primary sign-toggle-btn" data-sign="3" style="font-size:11px;padding:1px 10px;">
                                <i class="ri-pencil-line"></i> Sign 3
                            </button>
                        </div>
                        <div class="text-muted mt-2" style="font-size:11px;">
                            Klik untuk mengaktifkan/nonaktifkan slot tanda tangan.
                        </div>
                        <div class="d-flex flex-wrap gap-3 mt-2 recipient-toggle-group">
                            <div class="form-check form-check-inline mb-0">
                                <input type="checkbox" class="form-check-input recipient-check" data-sign="1" id="recipientSign1">
                                <label class="form-check-label" for="recipientSign1" style="font-size:11px;">Sign 1 = Penerima</label>
                            </div>
                            <div class="form-check form-check-inline mb-0">
                                <input type="checkbox" class="form-check-input recipient-check" data-sign="2" id="recipientSign2">
                                <label class="form-check-label" for="recipientSign2" style="font-size:11px;">Sign 2 = Penerima</label>
                            </div>
                            <div class="form-check form-check-inline mb-0">
                                <input type="checkbox" class="form-check-input recipient-check" data-sign="3" id="recipientSign3">
                                <label class="form-check-label" for="recipientSign3" style="font-size:11px;">Sign 3 = Penerima</label>
                            </div>
                        </div>
                        <div class="text-muted mt-1" style="font-size:11px;">
                            Slot bertanda "Penerima" diisi per penerima pada multi-surat; slot lain tetap. Maksimal satu slot penerima.
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
                            <div style="display:flex;flex-direction:column;align-items:center;">
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
@endsection

@section('javascript')
<script>window.ESIGN_KOP_URL = "{{ url('') }}/assets/images/KOP-terbaru.png";</script>
@vite('resources/js/rooster-editor.js')
<script>
    const empDbFields = @json($allEmployeeFields);
    let currentEmpNum = 1;

    document.addEventListener('DOMContentLoaded', function() {
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

    // ---- Toggle slot tanda tangan ----
    function updateSignButtons() {
        document.querySelectorAll('.sign-toggle-btn').forEach(function(btn) {
            var n = btn.dataset.sign;
            var input = document.getElementById('inputSign' + n);
            var active = input.value === '1';
            btn.classList.toggle('btn-primary', active);
            btn.classList.toggle('btn-outline-primary', !active);
        });
    }
    document.querySelectorAll('.sign-toggle-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var n = btn.dataset.sign;
            var input = document.getElementById('inputSign' + n);
            input.value = input.value === '1' ? '0' : '1';
            // Nonaktif: hapus penanda penerima pada slot tersebut
            if (input.value === '0') {
                var rec = document.getElementById('inputRecipient' + n);
                if (rec) rec.value = '0';
                var chk = document.getElementById('recipientSign' + n);
                if (chk) chk.checked = false;
            }
            updateSignButtons();
            updateRecipientButtons();
            if (window.refreshEditorSignPlaceholder) window.refreshEditorSignPlaceholder();
        });
    });
    updateSignButtons();

    // ---- Penanda slot Penerima (maksimal 1) ----
    function updateRecipientButtons() {
        document.querySelectorAll('.recipient-check').forEach(function(chk) {
            var n = chk.dataset.sign;
            var input = document.getElementById('inputSign' + n);
            var disabled = !input || input.value !== '1';
            chk.disabled = disabled;
            if (disabled && chk.checked) {
                chk.checked = false;
                var rec = document.getElementById('inputRecipient' + n);
                if (rec) rec.value = '0';
            }
        });
    }
    document.querySelectorAll('.recipient-check').forEach(function(chk) {
        chk.addEventListener('change', function() {
            var n = chk.dataset.sign;
            // Hanya satu slot penerima: uncheck lainnya
            document.querySelectorAll('.recipient-check').forEach(function(other) {
                if (other !== chk) other.checked = false;
            });
            document.querySelectorAll('.recipient-check').forEach(function(other) {
                var on = other.dataset.sign;
                document.getElementById('inputRecipient' + on).value = other.checked ? '1' : '0';
            });
        });
    });
    updateRecipientButtons();

    // Bangun HTML slot tanda tangan pada preview sesuai sign aktif
    function buildPreviewSignatureHtml() {
        var active = [];
        if (document.getElementById('inputSign1')?.value === '1') active.push(1);
        if (document.getElementById('inputSign2')?.value === '1') active.push(2);
        if (document.getElementById('inputSign3')?.value === '1') active.push(3);
        function box(label) {
            return '<div class="editor-sign-box">' +
                '<div class="editor-sign-box-label">' + label + '</div>' +
                '<div class="editor-sign-box-qr">QR Code<br>Digital Signature</div>' +
                '</div>';
        }
        var left = active.indexOf(2) !== -1 ? box('Sign 2') : '';
        var center = active.indexOf(3) !== -1 ? box('Sign 3') : '';
        var right = active.indexOf(1) !== -1 ? box('Sign 1') : '';
        return '<div class="preview-sign-placeholder">' +
            '<div class="editor-sign-slot">' + left + '</div>' +
            '<div class="editor-sign-slot">' + center + '</div>' +
            '<div class="editor-sign-slot">' + right + '</div>' +
            '</div>';
    }

    // Preview Template
    document.getElementById('btnPreviewTemplate').addEventListener('click', function() {
        var content = '';
        if (window.getRoosterContent) {
            content = window.getRoosterContent();
        } else if (window.roosterEditor) {
            content = window.getRoosterContent();
        } else {
            content = document.getElementById('templateContent').value;
        }
        if (!content.trim()) { alert('Template masih kosong.'); return; }

        // Kop surat (letterhead). Judul & nomor TIDAK di-generate di sini:
        // nomor surat (@{{nomor_surat}}) ditempatkan bebas oleh user di template,
        // sedangkan judul diisi sendiri oleh user saat membuat surat.
        var logoUrl = '{{ url('') }}/assets/images/KOP-terbaru.png';
        var fullHtml =
            '<div class="company-header"><img src="' + logoUrl + '" alt="Kop Surat" class="kop-img-full"></div>' +
            content +
            buildPreviewSignatureHtml();

        // Highlight placeholder — konsisten dengan preview lain agar page break sama
        fullHtml = fullHtml.replace(/\{\{(\w+)\}\}/g, '<span class="preview-placeholder">@{{$1}}</span>');

        var wrapper = document.getElementById('previewModalBody');
        // Margin sama dengan preview editor surat (1cm 2.5cm 2cm 2.5cm)
        wrapper.style.padding = '1cm 2.5cm 2cm 2.5cm';
        wrapper.innerHTML = fullHtml;

        var modal = new bootstrap.Modal(document.getElementById('previewModal'));
        modal.show();
    });

    // Paginasi preview SETELAH modal tampil (agar tinggi halaman terukur benar),
    // menunggu gambar (KOP) selesai dimuat dulu. Disamakan dengan preview editor surat:
    // kop berulang di tiap halaman, judul/nomor di halaman 1.
    function doTemplatePagination(wrapper) {
        var source = document.createElement('div');
        while (wrapper.firstChild) source.appendChild(wrapper.firstChild);

        // Pisahkan kop (berulang tiap halaman) serta judul & nomor (halaman 1 saja)
        var kopEl = null, titleEl = null, numEl = null, hasKop = false;
        var k = source.querySelector('.company-header');
        if (k) { kopEl = k; k.parentNode.removeChild(k); hasKop = true; }
        var t = source.querySelector('.doc-title'); if (t) titleEl = t;
        var n = source.querySelector('.doc-number'); if (n) numEl = n;
        var sig = source.querySelector('.preview-sign-placeholder');
        if (sig) sig.parentNode.removeChild(sig);

        // Halaman pertama = wrapper
        wrapper.innerHTML = '';
        if (hasKop) wrapper.appendChild(kopEl.cloneNode(true));
        if (titleEl) wrapper.appendChild(titleEl);
        if (numEl) wrapper.appendChild(numEl);

        var maxH = 297 * 3.78;
        var allPages = [wrapper];
        var pageIndex = 0;

        var nodes = Array.prototype.slice.call(source.childNodes);
        for (var i = 0; i < nodes.length; i++) wrapper.appendChild(nodes[i]);

        function makeNext(curr) {
            var np = document.createElement('div');
            np.className = 'preview-a4-page';
            np.style.padding = wrapper.style.padding;
            np.style.marginTop = '0';
            curr.parentNode.insertBefore(np, curr.nextSibling);
            var brk = document.createElement('div');
            brk.className = 'preview-page-break';
            curr.parentNode.insertBefore(brk, np);
            // Halaman berikutnya diawali kop surat
            if (hasKop) np.appendChild(kopEl.cloneNode(true));
            allPages.push(np);
            return np;
        }

        while (pageIndex < allPages.length) {
            var cur = allPages[pageIndex];
            var safety = 0;
            while (cur.scrollHeight > maxH + 5 && safety < 50) {
                safety++;
                var kids = cur.children;
                if (kids.length <= 1) break;
                var last = kids[kids.length - 1];
                var next = allPages[pageIndex + 1] || makeNext(cur);
                // Sisipkan di awal konten (setelah kop) agar urutan tetap benar
                var ib = next.children[hasKop ? 1 : 0];
                if (ib) next.insertBefore(last, ib); else next.appendChild(last);
            }
            pageIndex++;
        }

        // Tempel area tanda tangan di halaman terakhir (bawah)
        if (sig) {
            var lastPage = allPages[allPages.length - 1];
            lastPage.appendChild(sig);
            if (lastPage.scrollHeight > maxH + 5) {
                lastPage.removeChild(sig);
                var fresh = makeNext(lastPage);
                fresh.appendChild(sig);
            }
        }

        // Nomor halaman di pojok kanan bawah tiap kertas
        allPages.forEach(function(pg, idx) {
            var existing = pg.querySelector('.preview-page-number');
            if (existing) existing.remove();
            var num = document.createElement('div');
            num.className = 'preview-page-number';
            num.textContent = (idx + 1) + ' / ' + allPages.length;
            pg.appendChild(num);
        });
    }

    function paginateTemplatePreview() {
        var wrapper = document.getElementById('previewModalBody');
        if (!wrapper) return;
        // Hapus halaman & page break dari preview sebelumnya
        var parent = wrapper.parentNode;
        parent.querySelectorAll('.preview-a4-page').forEach(function(p, i) { if (i > 0) p.remove(); });
        parent.querySelectorAll('.preview-page-break').forEach(function(b) { b.remove(); });
        // Tunggu gambar (KOP) selesai dimuat agar tinggi akurat
        var imgs = wrapper.querySelectorAll('img');
        if (imgs.length === 0) {
            doTemplatePagination(wrapper);
        } else {
            var total = imgs.length, loaded = 0;
            imgs.forEach(function(img) {
                if (img.complete) { loaded++; if (loaded >= total) doTemplatePagination(wrapper); }
                else { img.onload = function() { loaded++; if (loaded >= total) doTemplatePagination(wrapper); }; }
            });
        }
    }

    $('#previewModal').on('shown.bs.modal', paginateTemplatePreview);

    // Cegah simpan bila template tidak menyertakan placeholder nomor surat
    document.getElementById('formTemplate').addEventListener('submit', function(e) {
        var content = window.getRoosterContent
            ? window.getRoosterContent()
            : document.getElementById('templateContent').value;
        if (content.indexOf('@{{nomor_surat}}') === -1) {
            e.preventDefault();
            var msg = 'Template wajib menyertakan placeholder @{{nomor_surat}} pada isi surat. ' +
                      'Gunakan tombol "Nomor Surat" pada panel placeholder untuk menambahkannya.';
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Nomor Surat belum ada',
                    html: 'Template wajib menyertakan placeholder ' +
                          '<code style="font-size:.95em;background:#f5f6f8;border:1px solid #e2e5ea;border-radius:4px;padding:1px 6px;color:#405189;">@{{nomor_surat}}</code> ' +
                          'pada isi surat.<br><small class="text-muted">Gunakan tombol "Nomor Surat" pada panel placeholder untuk menambahkannya.</small>',
                    confirmButtonText: 'Oke, mengerti',
                    confirmButtonColor: '#f0b429',
                    allowOutsideClick: false
                });
            } else {
                alert(msg);
            }
        }
    });
</script>
@endsection
