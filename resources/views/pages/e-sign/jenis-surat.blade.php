@extends('layouts.master')
@section('title', 'Jenis Surat - E-Sign')
@section('link')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .letter-card {
            border: none;
            border-radius: 14px;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            height: 100%;
            overflow: hidden;
            position: relative;
        }
        .letter-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 32px rgba(0,0,0,0.10);
        }
        .letter-card .card-body {
            padding: 1.75rem;
        }
        .letter-icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            flex-shrink: 0;
        }
        .letter-card .letter-name {
            font-size: 16px;
            font-weight: 600;
            color: #1e293b;
        }
        .letter-card .letter-desc {
            font-size: 13px;
            color: #6c757d;
            line-height: 1.5;
        }
        .letter-card .doc-count-badge {
            font-size: 12px;
            font-weight: 600;
            padding: 4px 12px;
            border-radius: 20px;
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
                    <h4 class="mb-sm-0">Jenis Surat</h4>
                    <small class="text-muted">Digital Signature Management System</small>
                </div>
            </div>
            <div class="page-title-right d-flex align-items-center gap-2">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">E-Sign Management</a></li>
                    <li class="breadcrumb-item active">Jenis Surat</li>
                </ol>
                <a href="{{ route('e-sign.jenis-surat.create') }}" class="btn btn-sm btn-primary ms-2">
                    <i class="ri-add-line me-1"></i> Tambah Jenis Surat
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    @foreach($letterTypes as $type)
    <div class="col-xl-4 col-lg-6 col-md-6">
        <div class="card letter-card">
            <div class="card-body d-flex flex-column">
                <div class="d-flex align-items-start gap-3 mb-2">
                    <div class="letter-icon bg-{{ $type->color }} bg-opacity-10 text-{{ $type->color }}">
                        <i class="{{ $type->icon }}"></i>
                    </div>
                    <div class="flex-grow-1 min-width-0">
                        <div class="letter-name">{{ $type->name }}</div>
                        <div class="letter-desc mt-1">{{ $type->description ?? '-' }}</div>
                    </div>
                </div>

                <table class="table table-sm table-borderless mb-2">
                    <tr>
                        <td class="text-muted ps-0" style="width:100px;font-size:12px;">Kode Surat</td>
                        <td style="font-size:13px;font-weight:500;letter-spacing:0.5px;">{{ $type->prefix }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-0" style="font-size:12px;">Template</td>
                        <td style="font-size:13px;font-weight:500;">{{ $type->template_count }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-0" style="font-size:12px;">Surat</td>
                        <td style="font-size:13px;font-weight:500;">{{ $type->document_count }} surat</td>
                    </tr>
                </table>

                <div class="mt-auto d-flex gap-1 pt-2 border-top flex-wrap">
                    <a href="{{ route('e-sign.jenis-surat.edit', $type->id) }}" class="btn btn-sm btn-outline-primary">
                        <i class="ri-settings-3-line me-1"></i> Kelola
                    </a>
                    @if($type->template_count > 0)
                    <a href="{{ route('e-sign.templates', ['letter_type_id' => $type->id]) }}" class="btn btn-sm btn-outline-info">
                        <i class="ri-file-copy-2-line me-1"></i> Template
                    </a>
                    @else
                    <a href="{{ route('e-sign.templates.create', ['letter_type_id' => $type->id]) }}" class="btn btn-sm btn-outline-info">
                        <i class="ri-add-line me-1"></i> Buat Template
                    </a>
                    @endif
                    @if($type->has_active_template)
                    <a href="{{ route('e-sign.create-select', ['letter_type_id' => $type->id]) }}" class="btn btn-sm btn-soft-success">
                        Buat Surat <i class="ri-arrow-right-line ms-1"></i>
                    </a>
                    @endif
                    <button type="button" class="btn btn-sm btn-outline-danger btn-delete-letter"
                        data-id="{{ $type->id }}" data-name="{{ $type->name }}"
                        data-template-count="{{ $type->template_count }}"
                        data-document-count="{{ $type->document_count }}">
                        <i class="ri-delete-bin-line me-1"></i> Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endforeach
    @if($letterTypes->isEmpty())
    <div class="col-12">
        <div class="text-center py-5">
            <i class="ri-inbox-2-line fs-1 text-muted"></i>
            <p class="text-muted mt-2">Belum ada jenis surat. Silakan tambah jenis surat baru.</p>
            <a href="{{ route('e-sign.jenis-surat.create') }}" class="btn btn-primary">
                <i class="ri-add-line me-1"></i> Tambah Jenis Surat
            </a>
        </div>
    </div>
    @endif
</div>
@endsection

@section('javascript')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        $('.btn-delete-letter').on('click', function() {
            const id = $(this).data('id');
            const name = $(this).data('name');
            const templateCount = $(this).data('template-count');
            const documentCount = $(this).data('document-count');

            if (documentCount > 0 || templateCount > 0) {
                let reasons = [];
                if (templateCount > 0) reasons.push(`${templateCount} template`);
                if (documentCount > 0) reasons.push(`${documentCount} surat`);
                
                Swal.fire({
                    title: 'Tidak Dapat Dihapus!',
                    text: `Jenis surat "${name}" masih memiliki ${reasons.join(' dan ')}. Hapus ${reasons.join(' dan ')} terlebih dahulu.`,
                    icon: 'error',
                    confirmButtonColor: '#6c757d',
                    confirmButtonText: 'Mengerti',
                });
            } else {
                Swal.fire({
                    title: 'Hapus Jenis Surat?',
                    text: `Yakin ingin menghapus "${name}"?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ url("e-sign/jenis-surat") }}/' + id,
                            type: 'POST',
                            data: {
                                _method: 'DELETE',
                                _token: '{{ csrf_token() }}',
                            },
                            success: function(res) {
                                if (res.success) {
                                    Swal.fire('Berhasil!', res.message, 'success')
                                        .then(() => location.reload());
                                } else {
                                    Swal.fire('Gagal!', res.message, 'error');
                                }
                            },
                            error: function(xhr) {
                                const msg = xhr.responseJSON?.message || 'Terjadi kesalahan.';
                                Swal.fire('Gagal!', msg, 'error');
                            }
                        });
                    }
                });
            }
        });
    });
</script>
@endsection
