@extends('layouts.master')
@section('title', $data['title'] . ' - Preview - E-Sign')
@section('link')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        /* ===== Document Preview Styles ===== */
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
            padding: 60px 56px 70px 56px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.10);
            border: 1px solid #dee2e6;
            border-radius: 4px;
            font-size: 13px;
            line-height: 1.7;
            color: #212529;
            position: relative;
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
        .doc-preview .field-label {
            font-weight: 600;
            color: #495057;
            min-width: 140px;
        }
        .doc-preview .field-value {
            color: #212529;
            font-weight: 500;
        }

        /* Preview footer — fixed di bottom halaman A4 preview */
        .doc-preview .preview-footer {
            position: absolute;
            bottom: 0;
            left: 56px;
            right: 56px;
            text-align: center;
            font-size: 10px;
            color: #6c757d;
            line-height: 1.5;
            border-top: 1px solid #dee2e6;
            padding-top: 12px;
            padding-bottom: 12px;
        }
        /* Sembunyikan footer dari _document-content agar tidak double */
        .doc-preview .preview-footer-note-inline {
            display: none !important;
        }

        /* Preview footer — muncul di bagian bawah dokumen */
        .doc-preview .preview-footer-note {
            margin-top: 48px;
            padding-top: 16px;
            border-top: 1px solid #dee2e6;
            font-size: 10px;
            color: #6c757d;
            line-height: 1.5;
            text-align: center;
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
                    <h4 class="mb-sm-0">Preview — {{ $data['title'] }}</h4>
                    <small class="text-muted">Digital Signature Management System</small>
                </div>
            </div>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">E-Sign Management</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('e-sign.daftar-surat') }}">Daftar Surat</a></li>
                    <li class="breadcrumb-item active">Preview</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- LEFT: Document Preview --}}
    <div class="col-xl-9 col-lg-8">
        <div class="doc-preview-wrapper">
            <div class="doc-preview" id="docPreview">
                @include('pages.e-sign.partials._document-content')
                {{-- Footer preview — absolute bottom --}}
                @if(!$doc->isDraft())
                <div class="preview-footer">
                    <i class="ri-shield-check-line me-1"></i>
                    Dokumen diterbitkan melalui sistem INTRANET E-Sign pada
                    <strong>{{ $doc->updated_at ? \Carbon\Carbon::parse($doc->updated_at)->format('d/m/Y H:i') : '-' }}</strong>
                    oleh <strong>{{ $doc->creator->name ?? $doc->creator->employee->fullname ?? '-' }}</strong>.
                    Sah secara hukum tanpa tanda tangan basah; pindai kode QR untuk verifikasi.
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- RIGHT: Action Panel --}}
    <div class="col-xl-3 col-lg-4">
        <div class="card">
            <div class="card-header">
                <i class="ri-file-info-line me-1"></i> Informasi Dokumen
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted d-block">Status</small>
                    <span class="badge bg-{{ 
                        ($doc->status === 'draft' ? 'warning' : 
                        ($doc->status === 'sign_1' || $doc->status === 'sign_2' || $doc->status === 'sign_3' ? 'info' : 
                        ($doc->status === 'completed' ? 'success' : 
                        ($doc->status === 'rejected_employee' ? 'danger' : 'secondary'))))
                    }} fs-12 mt-1">{{ $doc->status_label }}</span>
                </div>

                <div class="employee-info-grid mb-3">
                    <div class="info-row">
                        <span class="info-label">Employee</span>
                        <span class="info-value">{{ $doc->employee->fullname ?? '—' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">NIK</span>
                        <span class="info-value">{{ $doc->employee->nik ?? '—' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Departemen</span>
                        <span class="info-value">{{ $doc->employee->department->name ?? '—' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Nomor Surat</span>
                        <span class="info-value">{{ $doc->nomor_surat }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Tanggal Dibuat</span>
                        <span class="info-value">{{ $doc->upload_date ? \Carbon\Carbon::parse($doc->upload_date)->format('d M Y') : '—' }}</span>
                    </div>
                </div>

                <hr>

                <div class="d-grid gap-2">
                    @php
                        $employeeId = Auth::user()->employee_id;
                        $isRecipient = $doc->canBeResponded($employeeId);
                    @endphp

                    @if($isRecipient)
                    {{-- Employee: Sign / Approve --}}
                    <form method="POST" action="{{ route('e-sign.approve', $doc->id) }}" style="display:inline;">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn btn-success w-100 mb-2" onclick="return confirm('Tandatangani surat ini?')">
                            <i class="ri-check-double-line me-1"></i> Tanda Tangan
                        </button>
                    </form>
                    @endif

                    @if($doc->isDraft())
                    <a href="{{ route('e-sign.edit', $doc->id) }}" class="btn btn-primary">
                        <i class="ri-edit-line me-1"></i> Edit Surat
                    </a>
                    @endif

                    @if($doc->canBeSent())
                    <form method="POST" action="{{ route('e-sign.send', $doc->id) }}" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-info w-100 mb-2" onclick="return confirm('Kirim surat ini ke Employee?')">
                            <i class="ri-send-plane-line me-1"></i> Kirim ke Employee
                        </button>
                    </form>
                    @endif

                    @if(!$doc->isDraft())
                    <a href="{{ route('e-sign.pdf', $doc->id) }}" class="btn btn-success">
                        <i class="ri-download-2-line me-1"></i> Download PDF
                    </a>
                    @endif

                    @if($doc->isDraft())
                    <button type="button" class="btn btn-outline-danger" id="btnHapusSurat"
                        data-id="{{ $doc->id }}" data-judul="{{ $data['title'] }}">
                        <i class="ri-delete-bin-line me-1"></i> Hapus Surat
                    </button>
                    @endif

                    <a href="{{ $isRecipient ? route('e-sign.profile-index') : route('e-sign.daftar-surat') }}" class="btn btn-light">
                        <i class="ri-arrow-left-line me-1"></i> Kembali ke Daftar
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        $('#btnHapusSurat').on('click', function() {
            var id = $(this).data('id');
            var judul = $(this).data('judul');

            Swal.fire({
                title: 'Hapus Surat?',
                text: 'Yakin ingin menghapus surat "' + judul + '"? Data yang sudah dihapus tidak dapat dikembalikan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
            }).then(function(result) {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ url("e-sign") }}/' + id,
                        type: 'POST',
                        data: {
                            _method: 'DELETE',
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(res) {
                            if (res.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: res.message,
                                    timer: 2000,
                                    showConfirmButton: false,
                                }).then(function() {
                                    window.location.href = '{{ route("e-sign.daftar-surat") }}';
                                });
                            }
                        },
                        error: function(xhr) {
                            var msg = 'Gagal menghapus surat.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                msg = xhr.responseJSON.message;
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: msg,
                            });
                        }
                    });
                }
            });
        });
    });
</script>
@endsection
