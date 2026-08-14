@extends('layouts.master')
@section('title', $data['title'] . ' - E-Sign')
@section('link')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ url('') }}/assets/libs/adminlte/select2/css/4.1.0/select2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <style>
        .doc-preview-wrapper {
            background: #e9ecef;
            border-radius: 12px;
            padding: 24px;
            min-height: 80vh;
            display: flex;
            justify-content: center;
        }
        .doc-preview {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 0;
        }
        /* Halaman A4 pada preview editor — disamakan persis dengan ikon preview surat */
        .doc-preview-page {
            background: #ffffff;
            width: 210mm;
            min-height: 297mm;
            position: relative;
            margin: 0 auto 1.5em auto;
            padding: 1cm 2.5cm 2cm 2.5cm;
            box-shadow: 0 2px 12px rgba(0,0,0,0.10), 0 1px 3px rgba(0,0,0,0.06);
            border: 1px solid #dee2e6;
            border-radius: 4px;
            font-family: Calibri, Arial, sans-serif;
            font-size: 12pt;
            line-height: 1.5;
            color: #212529;
        }
        /* Pemisah antar kertas */
        .preview-page-break {
            width: 70%;
            max-width: 420px;
            margin: 4rem auto 4.5rem auto;
            border-top: 2px dashed #cbd5e1;
            flex-shrink: 0;
        }
        /* Nomor halaman "X / Y" di pojok kanan bawah tiap kertas */
        .preview-page-number {
            position: absolute;
            bottom: 0.35cm;
            right: 1cm;
            font-size: 10pt;
            font-weight: 600;
            color: #6b7280;
            font-family: Calibri, Arial, sans-serif;
        }
        .doc-preview p {
            margin: 0 0 0.75em 0;
            padding: 0;
        }
        .doc-preview h1,
        .doc-preview h2,
        .doc-preview h3,
        .doc-preview h4,
        .doc-preview h5 {
            font-family: Calibri, Arial, sans-serif;
        }
        .doc-preview table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11pt;
        }
        .doc-preview table td,
        .doc-preview table th {
            border: 1px solid #212529;
            padding: 6px 8px;
            vertical-align: top;
        }
        .doc-preview table th {
            background: #f0f0f0;
            font-weight: 700;
        }
        .doc-preview .company-header {
            text-align: center;
            margin-left: -0.8cm;
            margin-right: -0.8cm;
            margin-bottom: 6px;
            margin-top: -0.6cm;
        }
        .doc-preview .company-header img {
            width: 85%;
            height: auto;
            display: block;
            margin: 0 auto;
        }
        .doc-preview .doc-title {
            text-align: center;
            font-size: 16px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 4px;
            color: #1e293b;
        }
        .doc-preview .doc-number {
            text-align: center;
            font-size: 12px;
            color: #6c757d;
            margin-bottom: 28px;
            font-weight: 500;
        }
        /* Right panel */
        .side-panel {
            position: sticky;
            top: 88px;
            align-self: flex-start;
            max-height: calc(100vh - 100px);
            overflow-y: auto;
        }
        .side-panel .card {
            border-radius: 12px;
            border: none;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        }
        .side-panel .card-header {
            background: transparent;
            border-bottom: 1px solid #e9ecef;
            padding: 1rem 1.25rem;
            font-weight: 600;
            font-size: 15px;
        }
        .side-panel .form-label {
            font-size: 12px;
            font-weight: 600;
            color: #495057;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-bottom: 4px;
        }
        .side-panel .form-control, .side-panel .form-select {
            font-size: 13px;
            border-radius: 8px;
        }
        .side-panel .form-control:read-only {
            background: #f8f9fa;
            cursor: default;
        }
        .employee-info-grid {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 12px 16px;
        }
        .employee-info-grid .info-row {
            display: flex;
            justify-content: space-between;
            padding: 4px 0;
            font-size: 12px;
        }
        .employee-info-grid .info-row .info-label {
            color: #6c757d;
            font-weight: 500;
        }
        .employee-info-grid .info-row .info-value {
            color: #212529;
            font-weight: 600;
            text-align: right;
        }
        .esign-signature-area {
            display: flex !important;
            align-items: flex-start;
            justify-content: space-between;
            page-break-inside: avoid;
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
                    <h4 class="mb-sm-0">{{ $data['title'] }}</h4>
                    <small class="text-muted">Digital Signature Management System</small>
                </div>
            </div>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">E-Sign Management</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('e-sign.jenis-surat') }}">Jenis Surat</a></li>
                    <li class="breadcrumb-item active">{{ $data['title'] }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- LEFT: Document Preview (70%) --}}
    <div class="col-xl-8 col-lg-7">
        <div class="doc-preview-wrapper">
            <div class="doc-preview" id="docPreview">
                <div id="renderedContent">
                    <div class="text-center py-5 text-muted">
                        <i class="ri-file-text-line fs-1"></i>
                        <p class="mt-2">Pilih employee untuk melihat preview surat.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- RIGHT: Data Surat Panel (30%) --}}
    <div class="col-xl-4 col-lg-5 side-panel">
        <div class="card">
            <div class="card-header">
                <i class="ri-file-info-line me-1"></i> Data Surat
            </div>
            <div class="card-body">
                <form method="POST" action="@if($mode === 'edit') {{ route('e-sign.update', $doc->id) }} @elseif($mode === 'edit-batch') {{ route('e-sign.batch.update', $batch->id) }} @else {{ route('e-sign.store') }} @endif" id="formDraft">
                    @csrf
                    @if($mode === 'edit' || $mode === 'edit-batch')
                    @method('PUT')
                    @endif
                    <input type="hidden" name="jenis_surat_slug" value="{{ $data['slug'] }}">
                    <input type="hidden" name="document_name" value="{{ $data['title'] }}">
                    <input type="hidden" name="letter_type_id" value="{{ $type->id ?? '' }}">
                    <input type="hidden" name="template_id" id="inputTemplateId" value="{{ $activeTemplate->id ?? '' }}">
                    @if($mode === 'edit-batch')
                    <input type="hidden" name="batch_id" id="inputBatchId" value="{{ $batch->id }}">
                    @endif
                    <textarea name="content" style="display:none;" id="inputContent"></textarea>

                    {{-- Hidden inputs untuk employee_id per sign --}}
                    <input type="hidden" name="employee_id" id="inputEmployeeId" value="">
                    <input type="hidden" name="employee1_signee_id" id="inputEmployee1Id" value="">
                    <input type="hidden" name="employee2_signee_id" id="inputEmployee2Id" value="">
                    <input type="hidden" name="employee3_signee_id" id="inputEmployee3Id" value="">
                    <input type="hidden" name="multi_surat" id="inputMultiSurat" value="{{ $mode === 'edit-batch' ? '1' : '0' }}">
                    <input type="hidden" name="send_now" id="inputSendNow" value="0">

                    {{-- Toggle Multisurat --}}
                    <div class="mb-3 p-2 rounded border {{ in_array($mode, ['create','edit-batch']) ? '' : 'bg-light' }}" style="{{ in_array($mode, ['create','edit-batch']) ? 'border-color:#dee2e6;' : '' }}">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="toggleMultiSurat" @if($mode === 'create' || $mode === 'edit-batch') {{ $mode === 'edit-batch' ? 'checked' : '' }} @else disabled @endif>
                            <label class="form-check-label fw-semibold" for="toggleMultiSurat">Multisurat</label>
                        </div>
                        <small class="text-muted d-block mt-1">
                            {{ ($mode === 'create' || $mode === 'edit-batch') ? 'Kirim 1 surat ke banyak karyawan sekaligus (tiap karyawan mendapat salinan dengan datanya sendiri).' : 'Mode multisurat hanya tersedia saat membuat surat baru.' }}
                        </small>
                        <div class="alert alert-info py-2 mt-2 mb-0" id="multiHint" style="{{ $mode === 'edit-batch' ? '' : 'display:none;' }}font-size:12px;">
                            <i class="ri-stack-line me-1"></i>
                            Mode multisurat aktif. Pilih beberapa karyawan sebagai penerima — preview akan menampilkan surat untuk tiap karyawan.
                        </div>
                        <div class="form-check form-switch mt-2" style="display:none;">
                            <input class="form-check-input" type="checkbox" id="toggleSign2Recipient" checked>
                            <label class="form-check-label fw-semibold" for="toggleSign2Recipient">Sign 2 = Penerima (multisurat)</label>
                        </div>
                        <small class="text-muted d-block mt-1" style="display:none;font-size:12px;">
                            Saat aktif, pada multisurat tiap salinan menampilkan penerimanya sebagai Sign 2.
                        </small>
                    </div>

                    {{-- Pilih Employee (multiple) --}}
                    <div class="mb-3" id="singleEmployeeWrap">
                        <label class="form-label">Pilih Employee</label>
                        <select name="employee_id" class="form-control" id="selectEmployee" style="width: 100%;" multiple>
                            @foreach($employees as $emp)
                            @php
                                $empJson = [
                                    'nik' => $emp->nik, 'no_ktp' => $emp->no_ktp,
                                    'fullname' => $emp->fullname, 'email' => $emp->email,
                                    'addressktp' => $emp->addressktp, 'birthplace' => $emp->birthplace,
                                    'birthdate' => $emp->birthdate, 'gender' => $emp->gender,
                                    'religion' => $emp->religion, 'marital' => $emp->marital,
                                    'hp' => $emp->hp, 'joindate' => $emp->joindate,
                                    'enddate' => $emp->enddate, 'status' => $emp->status,
                                    'work_location' => $emp->work_location,
                                    'domicile_address' => $emp->domicile_address,
                                    'emergency_contact' => $emp->emergency_contact,
                                    'emergency_contact_relation' => $emp->emergency_contact_relation,
                                    'emergency_contact_handphone' => $emp->emergency_contact_handphone,
                                    'emergency_contact_address' => $emp->emergency_contact_address,
                                    'permanent_startdate' => $emp->permanent_startdate,
                                    'iso_position' => $emp->iso_position, 'cost_center' => $emp->cost_center,
                                    'last_education' => $emp->last_education,
                                    'major_last_education' => $emp->major_last_education,
                                    'last_education_institutional' => $emp->last_education_institutional,
                                    'tax_dependents' => $emp->tax_dependents, 'npwp' => $emp->npwp,
                                    'outsourcing_vendor' => $emp->outsourcing_vendor,
                                    'bpjs_kesehatan' => $emp->bpjs_kesehatan,
                                    'bpjs_ketenagakerjaan' => $emp->bpjs_ketenagakerjaan,
                                    'latest_agreement_number' => $emp->latest_agreement_number,
                                    'active_agreement_number' => $emp->active_agreement_number,
                                    'bank_name' => $emp->bank_name, 'bank_account' => $emp->bank_account,
                                    'bank_account_holder' => $emp->bank_account_holder,
                                    'blood_type' => $emp->blood_type,
                                    'contract_startdate' => $emp->contract_startdate,
                                    'contract_number' => $emp->contract_number,
                                    'department' => $emp->department->name ?? null,
                                    'position' => $emp->position->nama ?? null,
                                    'section' => $emp->section->nama ?? null,
                                    'level' => $emp->level->nama ?? null,
                                    'area' => $emp->area->name ?? null,
                                    'building' => $emp->building->nama ?? null,
                                ];
                            @endphp
                            <option value="{{ $emp->id }}"
                                data-emp='{!! htmlspecialchars(json_encode($empJson), ENT_QUOTES) !!}'
                                data-nik="{{ $emp->nik ?? '-' }}"
                                data-nama="{{ $emp->fullname }}"
                                data-dept="{{ $emp->department->name ?? '-' }}"
                                data-jabatan="{{ $emp->position->nama ?? '-' }}"
                                data-birthplace="{{ $emp->birthplace ?? '-' }}"
                                data-birthdate="{{ $emp->birthdate ?? '-' }}"
                                data-gender="{{ $emp->gender ?? '-' }}"
                                data-religion="{{ $emp->religion ?? '-' }}"
                                data-marital="{{ $emp->marital ?? '-' }}"
                                data-hp="{{ $emp->hp ?? '-' }}"
                                data-email="{{ $emp->email ?? '-' }}">
                                {{ $emp->nik }} — {{ $emp->fullname }}
                            </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Urutan pemilihan menentukan peran: karyawan pertama = pihak pertama (Sign 1), karyawan kedua = pihak kedua (Sign 2).</small>
                    </div>

                    {{-- Pihak Pertama & Penerima (mode multisurat) --}}
                    @php
                        $multiRecSlot = $activeTemplate ? $activeTemplate->recipient_sign : null;
                        $multiFixedSlots = $activeTemplate ? $activeTemplate->fixed_sign_slots : [];
                    @endphp
                    <div id="multiEmployeeWrap" style="display:none;">
                        @foreach($multiFixedSlots as $fs)
                        <div class="mb-3">
                            <label class="form-label">Penandatangan Tetap (Sign {{ $fs }}) <small class="text-muted">(sama untuk semua salinan)</small></label>
                            <select class="form-control fixed-sign-select" id="selectFixedSign{{ $fs }}" data-sign="{{ $fs }}" style="width: 100%;">
                                <option value="">— Pilih Penandatangan —</option>
                            </select>
                        </div>
                        @endforeach
                        <div class="mb-3">
                            <label class="form-label">Penerima <small class="text-muted">(bisa lebih dari satu)</small></label>
                            <select class="form-control" id="selectPenerima" style="width: 100%;" multiple>
                            </select>
                            <small class="text-muted">Tiap penerima diberi nomor urut dan menjadi penandatangan "Penerima" pada salinannya masing-masing.</small>
                        </div>
                        <div class="alert alert-light py-2 mb-0" style="font-size:11px;">
                            <i class="ri-information-line me-1"></i>
                            Slot Penerima: {{ $multiRecSlot ? ('Sign ' . $multiRecSlot) : '— (tidak ada; penerima tidak menandatangani)' }}.
                        </div>
                    </div>

                    {{-- Info Employee --}}
                    <div class="employee-info-grid mb-3" id="employeeInfo">
                        <div class="info-row">
                            <span class="info-label">Nama</span>
                            <span class="info-value" id="infoNama">—</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">NIK</span>
                            <span class="info-value" id="infoNik">—</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Departemen</span>
                            <span class="info-value" id="infoDept">—</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Jabatan</span>
                            <span class="info-value" id="infoJabatan">—</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Tempat Lahir</span>
                            <span class="info-value" id="infoBirthplace">—</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Tgl Lahir</span>
                            <span class="info-value" id="infoBirthdate">—</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Gender</span>
                            <span class="info-value" id="infoGender">—</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Agama</span>
                            <span class="info-value" id="infoReligion">—</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Status</span>
                            <span class="info-value" id="infoMarital">—</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">No. HP</span>
                            <span class="info-value" id="infoHp">—</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Email</span>
                            <span class="info-value" id="infoEmail">—</span>
                        </div>
                    </div>

                    <hr>

                    {{-- Judul Surat --}}
                    <div class="mb-3">
                        <label class="form-label">Judul Surat</label>
                        <input type="text" name="title" class="form-control" id="fieldTitle"
                            value="@if($mode === 'edit' && $doc) {{ $doc->title }} @elseif($mode === 'edit-batch' && isset($batchDocs) && $batchDocs->isNotEmpty()) {{ $batchDocs->first()->title }} @else {{ $data['title'] }} @endif"
                            placeholder="Masukkan judul surat">
                    </div>

                    {{-- Tanggal Dinamis (otomatis dari placeholder di template) --}}
                    <div id="dynamicDatesContainer">
                        <label class="form-label">Tanggal</label>
                        <div id="dynamicDatesList"></div>
                    </div>

                    {{-- Penandatangan (Sign 1, 2, 3) --}}
                    <hr>
                    @php $signSlots = $activeTemplate ? $activeTemplate->sign_slots : [1]; @endphp
                    <div id="signEmployeeFields">
                        <label class="form-label mb-2">Penandatangan</label>
                        @if(in_array(1, $signSlots))
                        <div class="mb-3 sign-field" id="signField1">
                            <label class="form-label" style="font-size:11px;text-transform:none;letter-spacing:0;">Sign 1 — Pilih Employee</label>
                            <select class="form-control sign-employee-select" id="selectEmployee1" style="width: 100%;">
                                <option value="">— Pilih Employee —</option>
                                @foreach($employees as $emp)
                                <option value="{{ $emp->id }}"
                                    data-nik="{{ $emp->nik ?? '-' }}"
                                    data-nama="{{ $emp->fullname }}"
                                    data-dept="{{ $emp->department->name ?? '-' }}"
                                    data-jabatan="{{ $emp->position->nama ?? '-' }}"
                                    data-birthplace="{{ $emp->birthplace ?? '-' }}"
                                    data-birthdate="{{ $emp->birthdate ?? '-' }}"
                                    data-gender="{{ $emp->gender ?? '-' }}"
                                    data-religion="{{ $emp->religion ?? '-' }}"
                                    data-marital="{{ $emp->marital ?? '-' }}"
                                    data-hp="{{ $emp->hp ?? '-' }}"
                                    data-email="{{ $emp->email ?? '-' }}">
                                    {{ $emp->nik }} — {{ $emp->fullname }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        @if(in_array(2, $signSlots))
                        <div class="mb-3 sign-field" id="signField2">
                            <label class="form-label" style="font-size:11px;text-transform:none;letter-spacing:0;">Sign 2 — Pilih Employee</label>
                            <select class="form-control sign-employee-select" id="selectEmployee2" style="width: 100%;">
                                <option value="">— Pilih Employee —</option>
                                @foreach($employees as $emp)
                                <option value="{{ $emp->id }}"
                                    data-nik="{{ $emp->nik ?? '-' }}"
                                    data-nama="{{ $emp->fullname }}"
                                    data-dept="{{ $emp->department->name ?? '-' }}"
                                    data-jabatan="{{ $emp->position->nama ?? '-' }}"
                                    data-birthplace="{{ $emp->birthplace ?? '-' }}"
                                    data-birthdate="{{ $emp->birthdate ?? '-' }}"
                                    data-gender="{{ $emp->gender ?? '-' }}"
                                    data-religion="{{ $emp->religion ?? '-' }}"
                                    data-marital="{{ $emp->marital ?? '-' }}"
                                    data-hp="{{ $emp->hp ?? '-' }}"
                                    data-email="{{ $emp->email ?? '-' }}">
                                    {{ $emp->nik }} — {{ $emp->fullname }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        @if(in_array(3, $signSlots))
                        <div class="mb-3 sign-field" id="signField3">
                            <label class="form-label" style="font-size:11px;text-transform:none;letter-spacing:0;">Sign 3 — Pilih Employee</label>
                            <select class="form-control sign-employee-select" id="selectEmployee3" style="width: 100%;">
                                <option value="">— Pilih Employee —</option>
                                @foreach($employees as $emp)
                                <option value="{{ $emp->id }}"
                                    data-nik="{{ $emp->nik ?? '-' }}"
                                    data-nama="{{ $emp->fullname }}"
                                    data-dept="{{ $emp->department->name ?? '-' }}"
                                    data-jabatan="{{ $emp->position->nama ?? '-' }}"
                                    data-birthplace="{{ $emp->birthplace ?? '-' }}"
                                    data-birthdate="{{ $emp->birthdate ?? '-' }}"
                                    data-gender="{{ $emp->gender ?? '-' }}"
                                    data-religion="{{ $emp->religion ?? '-' }}"
                                    data-marital="{{ $emp->marital ?? '-' }}"
                                    data-hp="{{ $emp->hp ?? '-' }}"
                                    data-email="{{ $emp->email ?? '-' }}">
                                    {{ $emp->nik }} — {{ $emp->fullname }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                    </div>

                    {{-- Keterangan --}}
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="Opsional">{{ $mode === 'edit' && $doc ? $doc->description : '' }}</textarea>                    </div>

                    {{-- Buttons --}}
                    <div class="d-grid gap-2 mt-3">
                        <button type="submit" class="btn btn-{{ ($mode === 'edit' || $mode === 'edit-batch') ? 'primary' : 'warning' }}" id="btnSimpanDraft">
                            <i class="ri-{{ ($mode === 'edit' || $mode === 'edit-batch') ? 'refresh' : 'save' }}-line me-1"></i>
                            {{ ($mode === 'edit' || $mode === 'edit-batch') ? 'Update Draft' : 'Simpan Draft' }}
                        </button>
                        <button type="submit" class="btn btn-success" id="btnLangsungKirim">
                            <i class="ri-send-plane-line me-1"></i> Langsung Kirim ke Employee
                        </button>
                        <a href="{{ route('e-sign.daftar-surat') }}" class="btn btn-light">
                            <i class="ri-arrow-left-line me-1"></i> Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script src="{{ url('') }}/assets/libs/adminlte/select2/js/4.1.0/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.id.min.js"></script>
<script>
    // Back browser → kembali ke tampilan Daftar Surat
    (function() {
        if (window.history && window.history.pushState) {
            history.pushState(null, '', location.href);
            window.addEventListener('popstate', function() {
                window.location.replace('{{ route("e-sign.daftar-surat") }}');
            });
        }
    })();
</script>
<script>
    // Store raw template content (with placeholders)
    var rawContent = '';
    var openBrace = String.fromCharCode(123, 123);
    var closeBrace = String.fromCharCode(125, 125);

    // Slot tanda tangan aktif dari template (1=kanan, 2=kiri, 3=tengah)
    window.activeSigns = @json($activeTemplate ? $activeTemplate->sign_slots : [1]);
    window.multiRecSlot = @json($multiRecSlot);

    // Daftar field employee untuk placeholder (employee_/employee1_/2_/3_)
    var empFields = @json($employeePlaceholderFields);

    $(document).ready(function() {
        // ===== Inisialisasi select2 karyawan =====
        // Search select2 mencocokkan SELURUH data karyawan: NIK, nama, departemen,
        // jabatan, dan semua kolom lain di tabel employees (via data-emp JSON) —
        // tanpa field terpisah.
        function employeeMatcher(params, data) {
            if ($.trim(params.term) === '') return data;
            if (typeof data.text === 'undefined') return null;
            var term = params.term.toLowerCase();

            var haystack = (data.text || '').toLowerCase();
            if (data.element) {
                var $el = $(data.element);
                // Semua atribut data-* (nik, nama, dept, jabatan, email, dsb.)
                $.each($el.data(), function(k, v) {
                    if (v != null) haystack += ' ' + String(v).toLowerCase();
                });
                // data-emp = JSON lengkap seluruh kolom employee
                var empRaw = $el.attr('data-emp');
                if (empRaw) {
                    try {
                        var emp = JSON.parse(empRaw);
                        $.each(emp, function(k, v) {
                            if (v != null) haystack += ' ' + String(v).toLowerCase();
                        });
                    } catch(e) {}
                }
            }
            return haystack.indexOf(term) > -1 ? data : null;
        }

        // Init Select2 multiple untuk employee (mode single / non-multisurat)
        // Urutan pemilihan menentukan peran: pertama = pihak pertama (Sign 1), kedua = pihak kedua (Sign 2).
        function initEmployeeSelect() {
            $('#selectEmployee').select2({
                placeholder: 'Cari berdasarkan NIK, Nama, atau Departemen...',
                allowClear: true,
                width: '100%',
                matcher: employeeMatcher,
                templateSelection: function(data, container) {
                    if (!data.id) return data.text;
                    var values = $('#selectEmployee').val() || [];
                    var idx = values.indexOf(String(data.id));
                    if (idx === -1) return data.text;
                    var roleLabel = idx === 0 ? 'Pihak Pertama' : (idx === 1 ? 'Pihak Kedua' : 'Pihak ' + (idx + 1));
                    return $('<span title="' + roleLabel + '"><span class="badge bg-primary me-1" style="font-size:10px;">' + (idx + 1) + '</span>' + data.text + '</span>');
                },
            });
        }

        function initFixedSignSelects() {
            $('.fixed-sign-select').select2({
                placeholder: 'Cari berdasarkan NIK, Nama, atau Departemen...',
                allowClear: true,
                width: '100%',
                matcher: employeeMatcher,
            });
        }

        function initPenerimaSelect() {
            $('#selectPenerima').select2({
                placeholder: 'Cari berdasarkan NIK, Nama, atau Departemen...',
                allowClear: true,
                width: '100%',
                matcher: employeeMatcher,
                templateSelection: function(data, container) {
                    if (!data.id) return data.text;
                    var values = $('#selectPenerima').val() || [];
                    var idx = values.indexOf(String(data.id));
                    if (idx === -1) return data.text;
                    return $('<span><span class="badge bg-info me-1" style="font-size:10px;">P' + (idx + 1) + '</span>' + data.text + '</span>');
                },
            });
        }

        // Init awal semua select2
        initEmployeeSelect();

        // Inisialisasi field multisurat: Penandatangan Tetap (per slot) & Penerima (multiple, bernomor)
        // Opsi di-clone dari selectEmployee agar data atribut (data-nik, dsb.) ikut terbawa.
        $('.fixed-sign-select').each(function() {
            $(this).append($('#selectEmployee option').clone());
        });
        $('#selectPenerima').append($('#selectEmployee option').clone());

        initFixedSignSelects();
        initPenerimaSelect();

        // Init Select2 untuk sign employee
        $('.sign-employee-select').each(function() {
            $(this).select2({
                placeholder: 'Cari berdasarkan NIK atau Nama...',
                allowClear: true,
                width: '100%',
            });
        });

        // Load initial template content
        // Mode edit: gunakan konten draft yang sudah tersimpan (sudah berisi data employee)
        // agar variabel tidak tampil kosong. Mode create: gunakan konten template mentah.
        @if(($mode === 'edit' || $mode === 'edit-batch') && isset($doc) && $doc && $doc->content)
        rawContent = {!! json_encode($doc->content) !!};
        @elseif($activeTemplate && $activeTemplate->content)
        rawContent = {!! json_encode($activeTemplate->content) !!};
        @elseif($templates && $templates->count() > 0)
        rawContent = {!! json_encode($templates->first()->content ?? '') !!};
        @endif

        // Employee change (multi)
        $('#selectEmployee').on('change', function() {
            var selected = $(this).find(':selected');
            if (!selected.length || !selected.val()) {
                $('#infoNama, #infoNik, #infoDept, #infoJabatan, #infoBirthplace, #infoBirthdate, #infoGender, #infoReligion, #infoMarital, #infoHp, #infoEmail').text('—');
                renderPreview();
                return;
            }
            // Show info from first selected employee
            var first = selected.first();
            $('#infoNama').text(first.data('nama'));
            $('#infoNik').text(first.data('nik'));
            $('#infoDept').text(first.data('dept'));
            $('#infoJabatan').text(first.data('jabatan'));
            $('#infoBirthplace').text(first.data('birthplace') || '-');
            $('#infoBirthdate').text(first.data('birthdate') || '-');
            $('#infoGender').text(first.data('gender') || '-');
            $('#infoReligion').text(first.data('religion') || '-');
            $('#infoMarital').text(first.data('marital') || '-');
            $('#infoHp').text(first.data('hp') || '-');
            $('#infoEmail').text(first.data('email') || '-');
            // Auto-fill Penandatangan dari karyawan bernomor (Sign 1 = karyawan 1, dst)
            // Hanya isi jika dropdown belum diisi manual, agar tidak menimpa pilihan user.
            selected.each(function(idx) {
                var signIdx = idx + 1;
                var sel = $('#selectEmployee' + signIdx);
                if (sel.length && !sel.val()) {
                    sel.val($(this).val()).trigger('change');
                }
            });
            renderPreview();
        });

        // Employee change for each sign
        $(document).on('change', '.sign-employee-select', function() {
            syncEmployeeHidden();
            renderPreview();
        });

        // Toggle Multisurat
        $('#toggleMultiSurat').on('change', function() {
            var on = $(this).is(':checked');
            $('#inputMultiSurat').val(on ? '1' : '0');
            $('#multiHint').toggle(on);
            $('#singleEmployeeWrap').toggle(!on);
            $('#multiEmployeeWrap').toggle(on);
            if (!on) {
                $('#inputEmployee1Id').val('');
            }
            renderPreview();
        });

        // Pilih Penandatangan Tetap (multisurat) per slot → employee<slot>_signee_id
        $(document).on('change', '.fixed-sign-select', function() {
            var slot = $(this).data('sign');
            $('#inputEmployee' + slot + 'Id').val($(this).val() || '');
            renderPreview();
        });

        // Pilih Penerima (multisurat, multiple)
        $('#selectPenerima').on('change', function() {
            renderPreview();
        });

        // Toggle Sign 2 = Penerima (multisurat) — sementara disembunyikan
        $('#toggleSign2Recipient').on('change', function() {
            renderPreview();
        });

        // Date changes — pakai event delegation untuk input dinamis
        $(document).on('input', '.dynamic-date-input', function() {
            renderPreview();
        });

        // Init datepicker untuk input tanggal dinamis (setelah di-generate)
        function initDatepicker() {
            if (typeof $.fn.datepicker !== 'undefined') {
                $('.dynamic-date-input').each(function() {
                    if (!$(this).data('datepicker')) {
                        $(this).datepicker({
                            format: 'dd MM yyyy',
                            autoclose: true,
                            todayHighlight: true,
                            orientation: 'top auto',
                            language: 'id',
                        }).on('changeDate', function() {
                            renderPreview();
                        });
                    }
                });
                // Klik pada input atau icon kalender → tampilkan datepicker
                $(document).off('click', '.dynamic-date-input, .btn-date-trigger');
                $(document).on('click', '.dynamic-date-input', function(e) {
                    e.preventDefault();
                    $(this).datepicker('show');
                });
                $(document).on('click', '.btn-date-trigger', function(e) {
                    e.preventDefault();
                    $(this).closest('.date-group').find('.dynamic-date-input').datepicker('show');
                });
            }
        }

        // Placeholder field changes
        $(document).on('input', '.placeholder-field', function() {
            renderPreview();
        });

        // Title changes
        $('#fieldTitle').on('input', function() {
            renderPreview();
        });

        // Initial render
        detectSignFields();
        generateDateFields();
        initDatepicker();
        safeRenderPreview();

        // EDIT-BATCH: pre-select penerima batch & aktifkan mode multi
        @if($mode === 'edit-batch')
        (function() {
            var recipientIds = @json($batchDocs->pluck('employee_id')->toArray());
            recipientIds.forEach(function(id) {
                var opt = $('#selectPenerima option[value="' + id + '"]');
                if (opt.length) opt.prop('selected', true);
            });
            @foreach($multiFixedSlots as $fs)
            var f{{ $fs }}Id = @json($batchDocs->first()->{'employee'.$fs.'_signee_id'} ?? null);
            if (f{{ $fs }}Id) {
                var optF{{ $fs }} = $('#selectFixedSign{{ $fs }} option[value="' + f{{ $fs }}Id + '"]');
                if (optF{{ $fs }}.length) optF{{ $fs }}.prop('selected', true);
            }
            @endforeach
            $('#selectPenerima').trigger('change');
            $('#toggleMultiSurat').prop('checked', true).trigger('change');
        })();
        @endif

        // EDIT (surat tunggal): pre-select employee & signee dari draft tersimpan
        @if($mode === 'edit' && $doc)
        (function() {
            var empId = @json($doc->employee_id ?? null);
            if (empId) {
                var opt = $('#selectEmployee option[value="' + empId + '"]');
                if (opt.length) opt.prop('selected', true);
                $('#selectEmployee').trigger('change');
            }
            [
                [1, '#selectEmployee1', '#inputEmployee1Id'],
                [2, '#selectEmployee2', '#inputEmployee2Id'],
                [3, '#selectEmployee3', '#inputEmployee3Id']
            ].forEach(function(pair) {
                var signeeId = @json($doc->employee1_signee_id ?? null);
                if (pair[0] === 2) signeeId = @json($doc->employee2_signee_id ?? null);
                if (pair[0] === 3) signeeId = @json($doc->employee3_signee_id ?? null);
                if (signeeId) {
                    var sel = $(pair[1]);
                    var sopt = sel.find('option[value="' + signeeId + '"]');
                    if (sopt.length) sel.val(signeeId).trigger('change');
                    $(pair[2]).val(signeeId);
                }
            });
            syncEmployeeHidden();
            safeRenderPreview();
        })();
        @endif

        // Re-render setelah semua gambar (kop surat) termuat agar pagination akurat
        $(window).on('load', function() {
            safeRenderPreview();
        });

        // Before submit, save rendered content
        $('#formDraft').on('submit', function(e) {
            var form = this;
            var $form = $(form);
            var multi = $('#inputMultiSurat').val() === '1';
            var selected = $('#selectEmployee').find(':selected');
            var recipients = $('#selectPenerima').find(':selected');

            // Tentukan jenis aksi dari tombol yang ditekan
            var submitter = e && e.originalEvent && e.originalEvent.submitter;
            if (submitter) {
                if (submitter.id === 'btnSimpanDraft') {
                    $('#inputSendNow').val('0');
                } else if (submitter.id === 'btnLangsungKirim') {
                    $('#inputSendNow').val('1');
                }
            }

            // Mode MULTISURAT: kirim array recipients[] (employee_id + content per penerima)
            if (multi && recipients.length > 0) {
                // Hapus input recipients lama jika ada (mis. saat re-submit)
                $form.find('input[name^="recipients["]').remove();
                $form.find('textarea[name^="recipients["]').remove();

                // Sync penandatangan tetap (per slot) ke employee<slot>_signee_id
                $('.fixed-sign-select').each(function() {
                    var slot = $(this).data('sign');
                    $('#inputEmployee' + slot + 'Id').val($(this).val() || '');
                });

                var base = buildBaseContent();
                var submitted = 0;
                recipients.each(function(i) {
                    var empData = getEmployeeDataByIndex(i, '#selectPenerima');
                    var content = applyRecipientPlaceholders(base, empData);
                    if (content.indexOf('esign-signature-area') === -1 && content.indexOf('signature-box') === -1) {
                        content += multiSignAreaHtml(empData, window.multiRecSlot);
                    }
                    $form.append('<input type="hidden" name="recipients[' + submitted + '][employee_id]" value="' + $(this).val() + '">');
                    // Simpan konten HTML mentah via textarea tersembunyi (TIDAK di-escape),
                    // sehingga preview merender HTML dengan benar.
                    var $contentBox = $('<textarea style="display:none;" name="recipients[' + submitted + '][content]"></textarea>').text(content);
                    $form.append($contentBox);
                    submitted++;
                });

                // Tanggal tersembunyi (sama seperti single)
                if (!$('#hiddenTglMulai').length) {
                    $form.append('<input type="hidden" name="tanggal_mulai" id="hiddenTglMulai" value="">');
                    $form.append('<input type="hidden" name="tanggal_akhir" id="hiddenTglAkhir" value="">');
                }
                $('#hiddenTglMulai').val($('#dateField_tanggal_mulai').val() || $('#dynamicDatesList .dynamic-date-input:first').val() || '');
                $('#hiddenTglAkhir').val($('#dateField_tanggal_akhir').val() || '');

                return true;
            }

            // Mode SINGLE
            var rendered = safeRenderPreview();
            $('#inputContent').val(rendered || rawContent);

            // Sync hidden inputs for sign employees
            syncEmployeeHidden();

            // Set hidden tanggal_mulai & tanggal_akhir dari dynamic date fields (jika ada)
            if (!$('#hiddenTglMulai').length) {
                $(this).append('<input type="hidden" name="tanggal_mulai" id="hiddenTglMulai" value="">');
                $(this).append('<input type="hidden" name="tanggal_akhir" id="hiddenTglAkhir" value="">');
            }
            $('#hiddenTglMulai').val($('#dateField_tanggal_mulai').val() || $('#dynamicDatesList .dynamic-date-input:first').val() || '');
            $('#hiddenTglAkhir').val($('#dateField_tanggal_akhir').val() || '');

            // Multi-select: disable all except first option so only first employee_id is submitted
            if (selected.length > 1) {
                selected.each(function(index) {
                    if (index > 0) $(this).prop('selected', false);
                });
            }
        });
    });

    function getEmployeeData() {
        var sel = $('#selectEmployee').find(':selected').first();
        if (!sel.length || !sel.val()) return null;
        return {
            name: sel.data('nama') || '-',
            nik: sel.data('nik') || '-',
            dept: sel.data('dept') || '-',
            position: sel.data('jabatan') || '-',
            birthplace: sel.data('birthplace') || '-',
            birthdate: sel.data('birthdate') || '-',
            gender: sel.data('gender') || '-',
            religion: sel.data('religion') || '-',
            marital: sel.data('marital') || '-',
            hp: sel.data('hp') || '-',
            email: sel.data('email') || '-',
        };
    }

    function getEmployeeDataByIndex(index, selector) {
        var $source = selector ? $(selector).find(':selected') : $('#selectEmployee').find(':selected');
        if ($source.length <= index || !$source.eq(index).val()) return null;
        var s = $source.eq(index);
        var parsed = null;
        try { parsed = JSON.parse(s.attr('data-emp') || ''); } catch(e) { parsed = null; }
        if (parsed && typeof parsed === 'object') {
            parsed.name = parsed.fullname || s.data('nama') || '-';
            parsed.dept = parsed.department || s.data('dept') || '-';
            parsed.fullname = parsed.fullname || parsed.name || '-';
            return parsed;
        }
        return {
            name: s.data('nama') || '-',
            nik: s.data('nik') || '-',
            position: s.data('jabatan') || '-',
            dept: s.data('dept') || '-',
            birthplace: s.data('birthplace') || '-',
            birthdate: s.data('birthdate') || '-',
            gender: s.data('gender') || '-',
            religion: s.data('religion') || '-',
            marital: s.data('marital') || '-',
            hp: s.data('hp') || '-',
            email: s.data('email') || '-',
            fullname: s.data('nama') || '-',
        };
    }

    function getSignEmployeeData(signIndex) {
        var sel = $('#selectEmployee' + signIndex).find(':selected');
        if (!sel.length || !sel.val()) return null;
        return {
            name: sel.data('nama') || '-',
            nik: sel.data('nik') || '-',
            position: sel.data('jabatan') || '-',
            dept: sel.data('dept') || '-',
            fullname: sel.data('nama') || '-',
        };
    }

    function syncEmployeeHidden() {
        var emp1 = $('#selectEmployee1').val() || '';
        var emp2 = $('#selectEmployee2').val() || '';
        var emp3 = $('#selectEmployee3').val() || '';
        $('#inputEmployee1Id').val(emp1);
        $('#inputEmployee2Id').val(emp2);
        $('#inputEmployee3Id').val(emp3);
        // Set employee_id ke sign 1 untuk backward compatibility
        $('#inputEmployeeId').val(emp1);
    }

    function detectSignFields() {
        // Sign fields always visible — user can assign signees freely
    }

    function formatDate(dateStr) {
        if (!dateStr) return '';
        var parts = dateStr.split('-');
        var d = new Date(parts[0], parts[1] - 1, parts[2]);
        return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
    }

    function decodeContent(encoded) {
        if (!encoded) return '';
        try {
            return atob(encoded);
        } catch(e) {
            return encoded;
        }
    }

    function generateDateFields() {
        if (!rawContent) return;

        // Cari semua placeholder tanggal di template: @{{tanggal_...}} atau @{{tgl_...}}
        var excludeList = [
            'employee_name','employee_nik','employee_position','employee_department',
            'employee_birthplace','employee_birthdate','employee_gender','employee_religion',
            'employee_marital','employee_hp','employee_email',
            'employee2_name','employee2_nik','employee2_position','employee2_department',
            'employee2_birthplace','employee2_birthdate','employee2_gender','employee2_religion',
            'employee2_marital','employee2_hp','employee2_email',
            'employee3_name','employee3_nik','employee3_position','employee3_department',
            'employee3_birthplace','employee3_birthdate','employee3_gender','employee3_religion',
            'employee3_marital','employee3_hp','employee3_email',
            'nomor_surat','today',
            'sign_employee1','sign_employee2','sign_employee3',
        ];

        var dateKeys = [];
        var regex = /\{\{([a-zA-Z0-9_]+)\}\}/g;
        var match;
        while ((match = regex.exec(rawContent)) !== null) {
            var key = match[1];
            // Ambil yang namanya diawali "tanggal" atau "tgl" dan tidak di-exclude
            if ((key.indexOf('tanggal') === 0 || key.indexOf('tgl') === 0) && excludeList.indexOf(key) === -1) {
                if (dateKeys.indexOf(key) === -1) dateKeys.push(key);
            }
        }

        var container = $('#dynamicDatesList');
        container.empty();

        if (dateKeys.length === 0) {
            container.html('<small class="text-muted">Tidak ada placeholder tanggal di template.</small>');
            return;
        }

        var labels = {
            'tanggal_mulai': 'Tanggal Mulai',
            'tanggal_akhir': 'Tanggal Berakhir',
        };

        var todayStr = new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });

        $.each(dateKeys, function(i, key) {
            var label = labels[key] || 'Tanggal ' + key.replace('tanggal_', '').replace('tgl_', '');
            var isFirst = (i === 0);
            var defaultValue = isFirst ? todayStr : '';
            var placeholder = isFirst ? 'Pilih atau ketik tanggal' : 'Kosongkan jika tidak perlu';

            var html = '<div class="mb-2">' +
                '<label class="form-label" style="font-size:11px;">' + label + '</label>' +
                '<div class="input-group input-group-sm date-group">' +
                '<span class="input-group-text bg-light btn-date-trigger" style="cursor:pointer;">' +
                '<i class="ri-calendar-line"></i></span>' +
                '<input type="text" class="form-control dynamic-date-input" id="dateField_' + key + '"' +
                ' data-date-key="' + key + '" placeholder="' + placeholder + '" value="' + defaultValue + '"' +
                ' style="cursor:pointer;background:#fff;" autocomplete="off">' +
                '</div></div>';
            container.append(html);
        });
    }

    function escapeRegex(str) {
        return str.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }

    function renderPreview() {
        if (!rawContent) {
            $('#renderedContent').html(
                '<div class="text-center py-5 text-muted">' +
                '<i class="ri-file-text-line fs-1"></i>' +
                '<p class="mt-2">Template belum tersedia. Silakan buat template terlebih dahulu.</p>' +
                '</div>'
            ).show();
            var containerEl = document.getElementById('docPreview');
            if (containerEl) {
                var oldPages = containerEl.querySelectorAll('.doc-preview-page');
                for (var op = 0; op < oldPages.length; op++) oldPages[op].remove();
            }
            return '';
        }

        var empFields = @json($employeePlaceholderFields);

        // ---- base: placeholder yang TIDAK terkait employee (sama untuk semua salinan) ----
        var base = buildBaseContent();

        var title = $('#fieldTitle').val() || '{{ $data["title"] }}';
        var logoUrl = '{{ url("") }}/assets/images/KOP-terbaru.png';
        function headerHtml() {
            return '<div class="company-header"><img src="' + logoUrl + '" alt="Kop Surat"></div>' +
                '<div class="doc-title">' + title + '</div>';
        }

        function isMultiSurat() {
            return $('#toggleMultiSurat').length && $('#toggleMultiSurat').is(':checked');
        }

        var selected = $('#selectEmployee').find(':selected');
        var recipients = $('#selectPenerima').find(':selected');
        var multi = isMultiSurat() && recipients.length > 0;

        // ---- MULTI-SURAT: render N salinan, tiap salinan data penerima sendiri ----
        if (multi) {
            var pages = [];
            recipients.each(function(i) {
                var empData = getEmployeeDataByIndex(i, '#selectPenerima');
                var content = applyRecipientPlaceholders(base, empData);
                if (content.indexOf('esign-signature-area') === -1 && content.indexOf('signature-box') === -1) {
                    content += multiSignAreaHtml(empData, window.multiRecSlot);
                }
                var page = '<div class="doc-preview-page">' + headerHtml() + content + '</div>';
                if (i < recipients.length - 1) page += '<div class="preview-page-break"></div>';
                pages.push(page);
            });
            $('#renderedContent').html(pages.join('')).show();
            // Bersihkan halaman pagination hasil render tunggal sebelumnya (jika ada)
            var cEl = document.getElementById('docPreview');
            if (cEl) {
                var leftovers = cEl.querySelectorAll('.doc-preview-page');
                for (var lx = 0; lx < leftovers.length; lx++) {
                    if (leftovers[lx].parentNode !== document.getElementById('renderedContent')) leftovers[lx].remove();
                }
            }
            return base;
        }

        // ---- SINGLE mode (existing) ----
        var content = base;
        // placeholder employeeX_name → fullname sesuai index (employee_/1_=0, 2_=1, 3_=2)
        content = content.replace(/\{\{employee_name\}\}|\{\{employee1_name\}\}/g, function() {
            var e = getEmployeeDataByIndex(0); return (e && (e.name || e.fullname)) || '_______________';
        });
        content = content.replace(/\{\{employee2_name\}\}/g, function() {
            var e = getEmployeeDataByIndex(1); return (e && (e.name || e.fullname)) || '_______________';
        });
        content = content.replace(/\{\{employee3_name\}\}/g, function() {
            var e = getEmployeeDataByIndex(2); return (e && (e.name || e.fullname)) || '_______________';
        });
        [['employee_', 0], ['employee1_', 0], ['employee2_', 1], ['employee3_', 2]].forEach(function(pair) {
            empFields.forEach(function(f) {
                var empData = getEmployeeDataByIndex(pair[1]);
                var v = empData ? (empData[f] || '_______________') : '_______________';
                content = content.replace(new RegExp('\\{\\{' + escRegex(pair[0] + f) + '\\}\\}', 'g'), v);
            });
        });
        content = content.replace(/\{\{sign_employee1\}\}/g, '');
        content = content.replace(/\{\{sign_employee2\}\}/g, '');
        content = content.replace(/\{\{sign_employee3\}\}/g, '');

        if (content.indexOf('esign-signature-area') === -1 && content.indexOf('signature-box') === -1 && window.activeSigns && window.activeSigns.length) {
            content += buildSignAreaHtml();
        }
        var htmlSingle = headerHtml() + content;
        $('#renderedContent').html(htmlSingle).show();
        paginateDocument();
        return content;
    }

    // Render preview yang AMAN: error saat render/pagination (mis. DOM) TIDAK boleh
    // menghentikan proses simpan draft. Bila error, dikembalikan konten dasar agar
    // form tetap terisi dan bisa disimpan.
    function safeRenderPreview() {
        try {
            return renderPreview();
        } catch (err) {
            console.error('Preview render error:', err);
            try {
                return buildBaseContent();
            } catch (e2) {
                console.error('buildBaseContent fallback error:', e2);
                return rawContent || '';
            }
        }
    }

    function escRegex(str) {
        return String(str || '').replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }

    // Kotak tanda tangan generik (label + nama + jabatan) — dipakai multi-surat
    function signBoxHtml(label, name, position) {
        return '<div style="width:140px;text-align:center;">' +
            '<div style="font-weight:700;font-size:13px;margin-bottom:4px;color:#1e293b;">' + label + '</div>' +
            '<div style="width:120px;height:80px;border:2px dashed #adb5bd;border-radius:8px;display:flex;align-items:center;justify-content:center;margin:0 auto 8px;font-size:10px;color:#adb5bd;background:#f8f9fa;">' +
            '<span>QR Code<br>Digital Signature</span></div>' +
            '<div style="font-size:12px;font-weight:600;color:#212529;margin-bottom:2px;">' + (name || '_______________') + '</div>' +
            '<div style="font-size:11px;color:#6c757d;">' + (position || '_______________') + '</div></div>';
    }

    // Ambil data penandatangan tetap untuk slot tertentu (dari select fixed-sign).
    function getFixedSignData(slot) {
        var sel = $('#selectFixedSign' + slot).find(':selected');
        if (!sel.length || !sel.val()) return null;
        return getEmployeeDataByIndex(0, '#selectFixedSign' + slot);
    }

    // Ambil data yang akan mengisi slot tanda tangan pada satu salinan multi-surat.
    // Slot penerima (window.multiRecSlot) → data penerima salinan ini; slot lain → penandatangan tetap.
    function getSlotData(slot, empData) {
        if (slot === window.multiRecSlot) {
            return empData || null;
        }
        var fixed = getFixedSignData(slot);
        if (fixed) return fixed;
        // Fallback HR untuk slot 1 (pihak perusahaan) bila belum dipilih.
        if (slot === 1) {
            return { fullname: '{{ optional(Auth::user()->employee)->fullname ?? "HR / Admin" }}', position: '{{ optional(Auth::user()->employee?->position)->nama ?? "HR / Admin" }}' };
        }
        return null;
    }

    // Replace placeholder employee pada satu salinan multi-surat.
    // Pemetaan placeholder → slot tanda tangan:
    //   employee_ / employee1_  → slot 1
    //   employee2_              → slot 2
    //   employee3_              → slot 3
    function applyRecipientPlaceholders(content, empData) {
        [['employee_', 1, /^\{\{(?:employee|employee1)_/], ['employee1_', 1, /^\{\{(?:employee|employee1)_/], ['employee2_', 2, /^\{\{employee2_/], ['employee3_', 3, /^\{\{employee3_/]].forEach(function(def) {
            var prefix = def[0], slot = def[1];
            // Tanpa slot penerima (penerima tidak menandatangani, mis. surat pengumuman):
            // seluruh placeholder employee = data penerima salinan ini (bukan penandatangan tetap).
            // Dengan slot penerima: placeholder yang menunjuk slot penerima = data penerima,
            // slot lain = penandatangan tetap.
            var data;
            if (window.multiRecSlot === null || window.multiRecSlot === undefined) {
                data = empData || null;
            } else {
                data = getSlotData(slot, empData);
            }
            var name = data ? (data.fullname || data.name || '_______________') : '_______________';
            var nameKey = prefix === 'employee_' ? 'employee_name' : prefix + 'name';
            content = content.replace(new RegExp('\\{\\{' + nameKey + '\\}\\}', 'g'), name);
            empFields.forEach(function(f) {
                var v = data ? (data[f] || '_______________') : '_______________';
                content = content.replace(new RegExp('\\{\\{' + prefix + escRegex(f) + '\\}\\}', 'g'), v);
            });
        });
        return content;
    }

    // Area tanda tangan per salinan multi-surat — hanya slot aktif, terposisi (kiri=Sign2, tengah=Sign3, kanan=Sign1).
    function multiSignAreaHtml(empData, recSlot) {
        var active = window.activeSigns || [];
        function cell(slot) {
            if (active.indexOf(slot) === -1) return '<div style="flex:1;"></div>';
            var data = getSlotData(slot, empData);
            var name = data ? (data.fullname || data.name || '') : '';
            var pos = data ? (data.position || '') : '';
            return '<div style="flex:1;display:flex;justify-content:center;">' + signBoxHtml('Sign ' + slot, name, pos) + '</div>';
        }
        return '<div class="esign-signature-area" style="display:flex;align-items:flex-start;justify-content:space-between;margin-top:18px;page-break-inside:avoid;">' +
            cell(2) + cell(3) + cell(1) +
            '</div>';
    }

    // Bangun konten dasar (placeholder non-employee direplace) — dipakai single & multi
    function buildBaseContent() {
        var content = rawContent;
        $('.dynamic-date-input').each(function() {
            var key = $(this).data('date-key');
            var val = $(this).val() || '_______________';
            content = content.replace(new RegExp('\\{\\{' + escRegex(key) + '\\}\\}', 'g'), val);
        });
        content = content.replace(/\{\{today\}\}/g, new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }));
        content = content.replace(/\{\{nomor_surat\}\}/g, '_______________');
        content = content.replace(/\{\{judul_surat\}\}/g, $('#fieldTitle').val() || '_______________');
        $('.placeholder-field').each(function() {
            var placeholder = $(this).data('placeholder');
            var val = $(this).val() || '_______________';
            content = content.replace(new RegExp(escRegex(placeholder), 'g'), val);
        });
        return content;
    }

    // Kotak tanda tangan (single mode) — dibaca dari select sign 1/2/3
    function signEmpHtml(label, name, idx) {
        var empSign = getSignEmployeeData(idx);
        var displayName = empSign ? empSign.name : '_______________';
        var displayPosition = empSign ? empSign.position : '_______________';
        return '<div class="signature-box" style="text-align:center;margin-top:16px;">' +
            '<div style="font-weight:700;font-size:13px;margin-bottom:4px;color:#1e293b;">' + label + '</div>' +
            '<div style="width:120px;height:80px;border:2px dashed #adb5bd;border-radius:8px;display:flex;align-items:center;justify-content:center;margin:0 auto 8px;font-size:10px;color:#adb5bd;background:#f8f9fa;">' +
            '<span>QR Code<br>Digital Signature</span></div>' +
            '<div style="font-size:12px;font-weight:600;color:#212529;margin-bottom:2px;">' + displayName + '</div>' +
            '<div style="font-size:11px;color:#6c757d;">' + displayPosition + '</div></div>';
    }

    // Bangun area tanda tangan pada preview Buat Surat sesuai slot aktif template
    // Posisi tetap: kiri=Sign2, tengah=Sign3, kanan=Sign1
    function buildSignAreaHtml() {
        var active = window.activeSigns || [];
        function box(label, idx) {
            var empSign = getSignEmployeeData(idx);
            var displayName = empSign ? empSign.name : '_______________';
            var displayPosition = empSign ? empSign.position : '_______________';
            return signBoxHtml(label, displayName, displayPosition);
        }
        function cell(sign) {
            if (active.indexOf(sign) === -1) return '<div style="flex:1;"></div>';
            return '<div style="flex:1;display:flex;justify-content:center;">' + box('Sign ' + sign, sign) + '</div>';
        }
        return '<div class="esign-signature-area" style="display:flex;align-items:flex-start;justify-content:space-between;margin-top:32px;page-break-inside:avoid;">' +
            cell(2) + cell(3) + cell(1) + '</div>';
    }

    // ===== Pagination Dokumen Preview Editor =====
    // Menggunakan algoritma & ukuran A4 yang SAMA dengan ikon preview surat
    // (create.blade.php) agar jumlah halaman antara keduanya selalu konsisten.
    function paginateDocument() {
        var container = document.getElementById('docPreview');
        var source = document.getElementById('renderedContent');
        if (!container || !source) return;

        // Bersihkan halaman & page break hasil pagination sebelumnya
        var oldPages = container.querySelectorAll('.doc-preview-page');
        for (var op = 0; op < oldPages.length; op++) oldPages[op].remove();
        container.querySelectorAll('.preview-page-break').forEach(function(b) { b.remove(); });

        // Pastikan source kembali jadi anak langsung container (tersembunyi sebagai sumber)
        if (source.parentNode !== container) container.appendChild(source);
        source.style.display = 'none';

        // Pisahkan kop surat (logo) agar diulang di SETIAP halaman,
        // sedangkan judul & nomor surat hanya muncul di halaman pertama.
        var kopEl = null, titleEl = null, numEl = null, hasKop = false;
        var kopNode = source.querySelector('.company-header');
        if (kopNode) { kopEl = kopNode; kopNode.parentNode.removeChild(kopNode); hasKop = true; }
        var titleNode = source.querySelector('.doc-title');
        if (titleNode) { titleEl = titleNode; }
        var numNode = source.querySelector('.doc-number');
        if (numNode) { numEl = numNode; }

        // Pisahkan area tanda tangan agar selalu berada di AKHIR dokumen (di bawah),
        // dan tidak ikut ter-paginasi ke atas halaman.
        var sigEl = null;
        var sigNode = source.querySelector('.esign-signature-area');
        if (sigNode) { sigEl = sigNode; sigNode.parentNode.removeChild(sigNode); }

        // Buat halaman pertama dan tuangkan seluruh isi konten ke dalamnya
        var firstPage = document.createElement('div');
        firstPage.className = 'doc-preview-page';
        container.appendChild(firstPage);
        if (hasKop) firstPage.appendChild(kopEl.cloneNode(true));
        if (titleEl) firstPage.appendChild(titleEl);
        if (numEl) firstPage.appendChild(numEl);

        var nodes = Array.prototype.slice.call(source.childNodes);
        for (var i = 0; i < nodes.length; i++) {
            firstPage.appendChild(nodes[i]);
        }

        // Sama persis dengan ikon preview: pecah per elemen blok hingga muat dalam 297mm
        var maxH = 297 * 3.78; // 297mm ke px
        var allPages = [firstPage];
        var pageIndex = 0;

        // Ukur tinggi ISI sebenarnya dari sebuah halaman (bukan scrollHeight).
        // scrollHeight selalu di-klamp ke min-height kertas (297mm), sehingga tidak
        // cocok untuk mengecek apakah area tanda tangan masih muat di halaman.
        function contentHeight(page) {
            var baseTop = page.getBoundingClientRect().top;
            var maxBottom = 0;
            var kids = page.children;
            for (var i = 0; i < kids.length; i++) {
                var el = kids[i];
                var r = el.getBoundingClientRect();
                var cs = window.getComputedStyle(el);
                var bottom = r.bottom + parseFloat(cs.marginBottom || 0);
                if (bottom - baseTop > maxBottom) maxBottom = bottom - baseTop;
            }
            return maxBottom;
        }

        function makeNextPage(currentPage) {
            var np = document.createElement('div');
            np.className = 'doc-preview-page';
            currentPage.parentNode.insertBefore(np, currentPage.nextSibling);
            // Page break visual di antara kertas
            var brk = document.createElement('div');
            brk.className = 'preview-page-break';
            currentPage.parentNode.insertBefore(brk, np);
            // Halaman berikutnya hanya kop surat (tanpa judul/nomor)
            if (hasKop) np.appendChild(kopEl.cloneNode(true));
            allPages.push(np);
            return np;
        }

        while (pageIndex < allPages.length) {
            var currentPage = allPages[pageIndex];
            var safety = 0;

            while (contentHeight(currentPage) > maxH + 5 && safety < 50) {
                safety++;
                var children = currentPage.children;
                if (children.length <= 1) break; // sisakan minimal 1 elemen (mis. hanya kop)

                var lastEl = children[children.length - 1];

                var nextPage = allPages[pageIndex + 1] || makeNextPage(currentPage);
                // Sisipkan ke AWAL konten halaman berikutnya (setelah kop surat).
                // Elemen dipindah satu per satu dari AKHIR halaman sebelumnya, jadi jika
                // ditambahkan ke akhir akan membalik urutan; insert di awal membuat urutan tetap benar.
                var insertBefore = nextPage.children[hasKop ? 1 : 0];
                if (insertBefore) nextPage.insertBefore(lastEl, insertBefore);
                else nextPage.appendChild(lastEl);
            }
            pageIndex++;
        }

        // Tempel area tanda tangan di halaman TERAKHIR (di bawah konten).
        // Jika tidak muat, letakkan di halaman baru agar tetap di akhir dokumen.
        if (sigEl) {
            var lastPage = allPages[allPages.length - 1];
            lastPage.appendChild(sigEl);
            if (contentHeight(lastPage) > maxH + 5) {
                lastPage.removeChild(sigEl);
                var lastNext = makeNextPage(lastPage);
                lastNext.appendChild(sigEl);
            }
        }

        // Nomor halaman "X / Y" di pojok kanan bawah tiap kertas
        allPages.forEach(function(pg, idx) {
            var existing = pg.querySelector('.preview-page-number');
            if (existing) existing.remove();
            var num = document.createElement('div');
            num.className = 'preview-page-number';
            num.textContent = (idx + 1) + ' / ' + allPages.length;
            pg.appendChild(num);
        });
    }
</script>
@endsection
