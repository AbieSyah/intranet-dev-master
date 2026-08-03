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
            background: #fff;
            width: 100%;
            max-width: 800px;
            min-height: 1050px;
            padding: 60px 56px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.10);
            border: 1px solid #dee2e6;
            border-radius: 4px;
            font-size: 13px;
            line-height: 1.7;
            color: #212529;
        }
        .doc-preview .company-header {
            text-align: center;
            margin-left: -56px;
            margin-right: -56px;
            margin-bottom: 6px;
            margin-top: -36px;
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
        {{-- Pilih Template --}}
        @if($templates && $templates->count() > 1)
        <div class="card mb-3">
            <div class="card-header">
                <i class="ri-file-copy-2-line me-1"></i> Pilih Template
            </div>
            <div class="card-body">
                <select class="form-select" id="selectTemplate">
                    @foreach($templates as $tpl)
                    <option value="{{ $tpl->id }}"
                        data-content="{{ base64_encode($tpl->content) }}"
                        {{ $activeTemplate && $activeTemplate->id === $tpl->id ? 'selected' : '' }}>
                        {{ $tpl->title }} {{ $tpl->is_active ? '(Aktif)' : '' }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>
        @endif

        <div class="card">
            <div class="card-header">
                <i class="ri-file-info-line me-1"></i> Data Surat
            </div>
            <div class="card-body">
                <form method="POST" action="{{ $mode === 'edit' ? route('e-sign.update', $doc->id) : route('e-sign.store') }}" id="formDraft">
                    @csrf
                    @if($mode === 'edit')
                    @method('PUT')
                    @endif
                    <input type="hidden" name="jenis_surat_slug" value="{{ $data['slug'] }}">
                    <input type="hidden" name="document_name" value="{{ $data['title'] }}">
                    <input type="hidden" name="letter_type_id" value="{{ $type->id ?? '' }}">
                    <input type="hidden" name="template_id" id="inputTemplateId" value="{{ $activeTemplate->id ?? '' }}">
                    <textarea name="content" style="display:none;" id="inputContent"></textarea>

                    {{-- Hidden inputs untuk employee_id per sign --}}
                    <input type="hidden" name="employee_id" id="inputEmployeeId" value="">
                    <input type="hidden" name="employee1_signee_id" id="inputEmployee1Id" value="">
                    <input type="hidden" name="employee2_signee_id" id="inputEmployee2Id" value="">
                    <input type="hidden" name="employee3_signee_id" id="inputEmployee3Id" value="">

                    {{-- Pilih Employee (multiple) --}}
                    <div class="mb-3">
                        <label class="form-label">Pilih Employee <small class="text-muted">(bisa pilih lebih dari 1)</small></label>
                        <select name="employee_id" class="form-control" id="selectEmployee" style="width: 100%;" multiple>
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
                        <small class="text-muted">Gunakan CTRL/CMD + klik untuk memilih lebih dari satu.</small>
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
                            value="{{ $mode === 'edit' && $doc ? $doc->title : $data['title'] }}"
                            placeholder="Masukkan judul surat">
                    </div>

                    {{-- Tanggal Dinamis (otomatis dari placeholder di template) --}}
                    <div id="dynamicDatesContainer">
                        <label class="form-label">Tanggal</label>
                        <div id="dynamicDatesList"></div>
                    </div>

                    {{-- Penandatangan (Sign 1, 2, 3) --}}
                    <hr>
                    <div id="signEmployeeFields">
                        <label class="form-label mb-2">Penandatangan</label>
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
                    </div>

                    {{-- Placeholder Inputs --}}
                    <hr>
                    <div id="placeholderInputs">
                        <label class="form-label">Data Surat</label>
                        @foreach($placeholders as $key => $desc)
                            @if(in_array($key, $excludedPlaceholders))
                                @continue
                            @endif
                        <div class="mb-2 placeholder-input">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text" style="width:110px;"><code>@{{{{ $key }}}}</code></span>
                                <input type="text" class="form-control placeholder-field"
                                    data-placeholder="@{{{{ $key }}}}"
                                    placeholder="{{ $desc }}">
                            </div>
                        </div>
                        @endforeach
                    </div>

                    {{-- Keterangan --}}
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="Opsional">{{ $mode === 'edit' && $doc ? $doc->description : '' }}</textarea>
                    </div>

                    {{-- Buttons --}}
                    <div class="d-grid gap-2 mt-3">
                        <button type="submit" class="btn btn-{{ $mode === 'edit' ? 'primary' : 'warning' }}" id="btnSimpanDraft">
                            <i class="ri-{{ $mode === 'edit' ? 'refresh' : 'save' }}-line me-1"></i>
                            {{ $mode === 'edit' ? 'Update Draft' : 'Simpan Draft' }}
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
    // Store raw template content (with placeholders)
    var rawContent = '';
    var openBrace = String.fromCharCode(123, 123);
    var closeBrace = String.fromCharCode(125, 125);

    $(document).ready(function() {
        // Init Select2 multiple untuk employee
        $('#selectEmployee').select2({
            placeholder: 'Cari berdasarkan NIK atau Nama...',
            allowClear: true,
            width: '100%',
        });

        // Init Select2 untuk sign employee
        $('.sign-employee-select').each(function() {
            $(this).select2({
                placeholder: 'Cari berdasarkan NIK atau Nama...',
                allowClear: true,
                width: '100%',
            });
        });

        // Load initial template content
        @if($activeTemplate && $activeTemplate->content)
        rawContent = {!! json_encode($activeTemplate->content) !!};
        @elseif($templates && $templates->count() > 0)
        var firstOpt = $('#selectTemplate').length ? $('#selectTemplate').find('option:first') : null;
        rawContent = firstOpt ? decodeContent(firstOpt.data('content')) : '';
        @endif

        // Template change
        $(document).on('change', '#selectTemplate', function() {
            var opt = $(this).find(':selected');
            rawContent = decodeContent(opt.data('content'));
            $('#inputTemplateId').val(opt.val());
            detectSignFields();
            renderPreview();
        });

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
            renderPreview();
        });

        // Employee change for each sign
        $(document).on('change', '.sign-employee-select', function() {
            syncEmployeeHidden();
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
        renderPreview();

        // Re-generate date fields when template changes
        $(document).on('change', '#selectTemplate', function() {
            setTimeout(function() {
                detectSignFields();
                generateDateFields();
                initDatepicker();
                renderPreview();
            }, 100);
        });

        // Before submit, save rendered content
        $('#formDraft').on('submit', function() {
            var rendered = renderPreview();
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
            var selected = $('#selectEmployee').find(':selected');
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

    function getEmployeeDataByIndex(index) {
        var sel = $('#selectEmployee').find(':selected');
        if (sel.length <= index || !sel.eq(index).val()) return null;
        var s = sel.eq(index);
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
            'nomor_surat','judul_surat','today',
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
            );
            return '';
        }

        var content = rawContent;
        var emp = getEmployeeData();

        // ============================================================
        // REPLACE ALL PLACEHOLDERS — otomatis dari data yang tersedia
        // ============================================================

        // 1. Employee data mapping
        var empFieldMap = {
            'employee_name': 'name', 'employee_nik': 'nik', 'employee_position': 'position',
            'employee_department': 'dept', 'employee_birthplace': 'birthplace',
            'employee_birthdate': 'birthdate', 'employee_gender': 'gender',
            'employee_religion': 'religion', 'employee_marital': 'marital',
            'employee_hp': 'hp', 'employee_email': 'email',
            'employee1_nik': 'nik', 'employee1_fullname': 'name', 'employee1_name': 'name',
            'employee1_position': 'position', 'employee1_department': 'dept',
            'employee2_nik': 'nik', 'employee2_fullname': 'name', 'employee2_name': 'name',
            'employee2_position': 'position', 'employee2_department': 'dept',
            'employee3_nik': 'nik', 'employee3_fullname': 'name', 'employee3_name': 'name',
            'employee3_position': 'position', 'employee3_department': 'dept',
        };
        
        // Map placeholder → (employee index, field key)
        var empPlaceholderMap = {};
        for (var p in empFieldMap) {
            var idx = 1;
            if (p.indexOf('employee2') === 0) idx = 2;
            else if (p.indexOf('employee3') === 0) idx = 3;
            empPlaceholderMap[p] = { index: idx - 1, field: empFieldMap[p] };
        }

        // Replace employee placeholders via regex
        for (var placeholder in empPlaceholderMap) {
            var info = empPlaceholderMap[placeholder];
            var empData = getEmployeeDataByIndex(info.index);
            var val = empData ? (empData[info.field] || '_______________') : '_______________';
            var re = new RegExp('\\{\\{' + placeholder.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + '\\}\\}', 'g');
            content = content.replace(re, val);
        }

        // 2. Date placeholders — dinamis dari field yang di-generate
        $('.dynamic-date-input').each(function() {
            var key = $(this).data('date-key');
            var val = $(this).val() || '_______________';
            var regex = new RegExp('\\{\\{' + key.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + '\\}\\}', 'g');
            content = content.replace(regex, val);
        });
        content = content.replace(/\{\{today\}\}/g, new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }));

        // 3. Document fields
        content = content.replace(/\{\{nomor_surat\}\}/g, '_______________');
        content = content.replace(/\{\{judul_surat\}\}/g, $('#fieldTitle').val() || '_______________');

        // 4. Sign placeholders — ambil dari select sign 1,2,3
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
        content = content.replace(/\{\{sign_employee1\}\}/g, signEmpHtml('Sign 1', null, 1));
        content = content.replace(/\{\{sign_employee2\}\}/g, signEmpHtml('Sign 2', null, 2));
        content = content.replace(/\{\{sign_employee3\}\}/g, signEmpHtml('Sign 3', null, 3));

        // 5. Custom placeholder fields (dari form Data Surat)
        $('.placeholder-field').each(function() {
            var placeholder = $(this).data('placeholder');
            var val = $(this).val() || '_______________';
            content = content.replace(new RegExp(escapeRegex(placeholder), 'g'), val);
        });

        // Build full preview with company header
        var title = $('#fieldTitle').val() || '{{ $data["title"] }}';
        var logoUrl = '{{ url("") }}/assets/images/KOP-terbaru.png';

        var html = '<div class="company-header">' +
            '<img src="' + logoUrl + '" alt="Kop Surat">' +
            '</div>' +
            '<div class="doc-title">' + title + '</div>' +
            '<div class="doc-number">Nomor: _______________</div>' +
            content;

        $('#renderedContent').html(html);
        return content;
    }
</script>
@endsection
