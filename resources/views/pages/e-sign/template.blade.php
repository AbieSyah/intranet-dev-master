@extends('layouts.master')
@section('title', $data['title'] . ' - E-Sign')
@section('link')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ url('') }}/assets/libs/adminlte/select2/css/4.1.0/select2.min.css" rel="stylesheet">
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
            border-bottom: 2px solid #1e293b;
            padding-bottom: 20px;
            margin-bottom: 28px;
        }
        .doc-preview .company-header img {
            height: 56px;
            margin-bottom: 8px;
        }
        .doc-preview .company-header .company-name {
            font-size: 15px;
            font-weight: 700;
            color: #1e293b;
            letter-spacing: 0.5px;
        }
        .doc-preview .company-header .company-address {
            font-size: 11px;
            color: #6c757d;
            margin-top: 2px;
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
        .doc-preview .field-label {
            font-weight: 600;
            color: #495057;
            min-width: 140px;
        }
        .doc-preview .field-value {
            color: #212529;
            font-weight: 500;
        }
        .doc-preview .field-value.placeholder {
            color: #adb5bd;
            font-style: italic;
        }
        .doc-preview .signature-area {
            margin-top: 48px;
            display: flex;
            justify-content: space-between;
            gap: 40px;
        }
        .doc-preview .signature-box {
            flex: 1;
            text-align: center;
        }
        .doc-preview .signature-box .sig-title {
            font-weight: 700;
            font-size: 13px;
            margin-bottom: 4px;
            color: #1e293b;
        }
        .doc-preview .signature-box .sig-role {
            font-size: 11px;
            color: #6c757d;
            margin-bottom: 16px;
        }
        .doc-preview .signature-box .sig-line {
            border-top: 1px dashed #212529;
            width: 80%;
            margin: 0 auto 4px;
            padding-top: 8px;
        }
        .doc-preview .signature-box .sig-placeholder {
            width: 120px;
            height: 80px;
            border: 2px dashed #adb5bd;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 8px;
            font-size: 10px;
            color: #adb5bd;
            background: #f8f9fa;
        }
        .doc-preview .signature-box .sig-label {
            font-size: 11px;
            color: #6c757d;
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
                <div class="company-header">
                    <img src="{{ url('') }}/assets/images/hisamitsu.png" alt="Hisamitsu Pharma Indonesia">
                    <div class="company-name">PT HISAMITSU PHARMA INDONESIA</div>
                    <div class="company-address">Jl. Raya Bogor KM. 28, Cibinong, Bogor 16914</div>
                </div>

                <div class="doc-title">{{ $data['title'] }}</div>
                <div class="doc-number" id="docNumberDisplay">Nomor: {{ $data['number'] }}</div>

                <p style="text-align: justify; margin-bottom: 20px;">
                    Yang bertanda tangan di bawah ini:
                </p>

                <table style="width: 100%; margin-bottom: 20px;">
                    <tr>
                        <td class="field-label" style="vertical-align: top; width: 140px;">PT Hisamitsu Pharma Indonesia</td>
                        <td class="field-value">bertindak sebagai <strong>Pemberi Kerja</strong></td>
                    </tr>
                </table>

                <p style="text-align: justify; margin-bottom: 12px;">dengan</p>

                <table style="width: 100%; margin-bottom: 24px;">
                    <tr>
                        <td class="field-label" style="vertical-align: top; width: 140px;">Nama</td>
                        <td class="field-value placeholder" id="previewNama">— Pilih Employee —</td>
                    </tr>
                    <tr>
                        <td class="field-label">NIK</td>
                        <td class="field-value placeholder" id="previewNik">—</td>
                    </tr>
                    <tr>
                        <td class="field-label">Departemen</td>
                        <td class="field-value placeholder" id="previewDept">—</td>
                    </tr>
                    <tr>
                        <td class="field-label">Jabatan</td>
                        <td class="field-value placeholder" id="previewJabatan">—</td>
                    </tr>
                    <tr>
                        <td class="field-label">Tanggal Mulai</td>
                        <td class="field-value placeholder" id="previewTglMulai">—</td>
                    </tr>
                    <tr>
                        <td class="field-label" style="border-bottom: none;">Tanggal Berakhir</td>
                        <td class="field-value placeholder" id="previewTglAkhir">—</td>
                    </tr>
                </table>

                <p style="text-align: justify; margin-bottom: 16px;">
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                </p>
                <p style="text-align: justify; margin-bottom: 16px;">
                    Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit.
                </p>

                <div class="signature-area">
                    <div class="signature-box">
                        <div class="sig-title">HRD</div>
                        <div class="sig-role">Human Resources Department</div>
                        <div class="sig-line"></div>
                        <div class="sig-label">(Digital Signature)</div>
                    </div>
                    <div class="signature-box">
                        <div class="sig-title">Employee</div>
                        <div class="sig-role" id="previewSigRole">— Pilih Employee —</div>
                        <div class="sig-placeholder">
                            <span>QR Code<br>Digital Signature</span>
                        </div>
                        <div class="sig-label">(QR Code akan ditempatkan di sini)</div>
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
                {{-- Pilih Employee --}}
                <div class="mb-3">
                    <label class="form-label">Pilih Employee</label>
                    <select class="form-control" id="selectEmployee" style="width: 100%;">
                        <option value="">— Pilih Employee —</option>
                        @php
                            use App\Models\Employee;
                            $employees = Employee::whereNotNull('fullname')->orderBy('fullname')->limit(50)->get();
                        @endphp
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}"
                                data-nik="{{ $emp->nik ?? '-' }}"
                                data-nama="{{ $emp->fullname }}"
                                data-dept="{{ $emp->department->name ?? '-' }}"
                                data-jabatan="{{ $emp->position->nama ?? '-' }}"
                                data-status="{{ $emp->status ?? '-' }}"
                                data-joindate="{{ $emp->joindate ? date('d M Y', strtotime($emp->joindate)) : '-' }}">
                                {{ $emp->nik }} — {{ $emp->fullname }}
                            </option>
                        @endforeach
                    </select>
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
                        <span class="info-label">Status</span>
                        <span class="info-value" id="infoStatus">—</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Tanggal Masuk</span>
                        <span class="info-value" id="infoJoindate">—</span>
                    </div>
                </div>

                <hr>

                {{-- Field Surat --}}
                <div class="mb-3">
                    <label class="form-label">Nomor Surat</label>
                    <input type="text" class="form-control" id="fieldNomor" value="{{ $data['number'] }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Tanggal Berlaku</label>
                    <input type="date" class="form-control" id="fieldTglMulai" value="{{ date('Y-m-d') }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Tanggal Berakhir</label>
                    <input type="date" class="form-control" id="fieldTglAkhir" value="{{ date('Y-m-d', strtotime('+2 years')) }}">
                </div>
                <div class="mb-4">
                    <label class="form-label">Keterangan</label>
                    <textarea class="form-control" id="fieldKeterangan" rows="3" placeholder="Masukkan keterangan..."></textarea>
                </div>

                {{-- Buttons --}}
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-primary" onclick="previewDocument()">
                        <i class="ri-eye-line me-1"></i> Preview
                    </button>
                    <button type="button" class="btn btn-warning" onclick="simpanDraft()">
                        <i class="ri-save-line me-1"></i> Simpan Draft
                    </button>
                    <a href="{{ route('e-sign.daftar-surat') }}" class="btn btn-light">
                        <i class="ri-arrow-left-line me-1"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script src="{{ url('') }}/assets/libs/adminlte/select2/js/4.1.0/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        $('#selectEmployee').select2({
            placeholder: 'Cari berdasarkan NIK atau Nama...',
            allowClear: true,
            width: '100%',
        });

        $('#selectEmployee').on('change', function() {
            const selected = $(this).find(':selected');
            if (!selected.val()) {
                resetEmployeeInfo();
                resetPreview();
                return;
            }

            const nama = selected.data('nama');
            const nik = selected.data('nik');
            const dept = selected.data('dept');
            const jabatan = selected.data('jabatan');
            const status = selected.data('status');
            const joindate = selected.data('joindate');
            const tglMulai = $('#fieldTglMulai').val();
            const tglAkhir = $('#fieldTglAkhir').val();

            $('#infoNama').text(nama);
            $('#infoNik').text(nik);
            $('#infoDept').text(dept);
            $('#infoJabatan').text(jabatan);
            $('#infoStatus').text(status);
            $('#infoJoindate').text(joindate);

            updatePreview(nama, nik, dept, jabatan, tglMulai, tglAkhir);
        });

        $('#fieldTglMulai, #fieldTglAkhir').on('change', function() {
            const selected = $('#selectEmployee').find(':selected');
            if (selected.val()) {
                updatePreview(
                    selected.data('nama'),
                    selected.data('nik'),
                    selected.data('dept'),
                    selected.data('jabatan'),
                    $('#fieldTglMulai').val(),
                    $('#fieldTglAkhir').val()
                );
            }
        });
    });

    function resetEmployeeInfo() {
        $('#infoNama, #infoNik, #infoDept, #infoJabatan, #infoStatus, #infoJoindate').text('—');
    }

    function resetPreview() {
        $('#previewNama').text('— Pilih Employee —').removeClass('placeholder').addClass('placeholder');
        $('#previewNik, #previewDept, #previewJabatan, #previewTglMulai, #previewTglAkhir').text('—').removeClass('placeholder').addClass('placeholder');
        $('#previewSigRole').text('— Pilih Employee —');
    }

    function updatePreview(nama, nik, dept, jabatan, tglMulai, tglAkhir) {
        const formatDate = (dateStr) => {
            if (!dateStr) return '—';
            const d = new Date(dateStr + 'T00:00:00');
            return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
        };

        $('#previewNama').text(nama).removeClass('placeholder');
        $('#previewNik').text(nik).removeClass('placeholder');
        $('#previewDept').text(dept).removeClass('placeholder');
        $('#previewJabatan').text(jabatan).removeClass('placeholder');
        $('#previewTglMulai').text(formatDate(tglMulai)).removeClass('placeholder');
        $('#previewTglAkhir').text(formatDate(tglAkhir)).removeClass('placeholder');
        $('#previewSigRole').text(nama);
    }

    function previewDocument() {
        document.getElementById('docPreview').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function simpanDraft() {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'Draft berhasil disimpan.',
            confirmButtonColor: '#0ab39c',
            confirmButtonText: 'OK',
        }).then(() => {
            window.location.href = '{{ route('e-sign.daftar-surat') }}';
        });
    }
</script>
@endsection
