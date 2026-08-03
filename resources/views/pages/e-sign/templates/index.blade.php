@extends('layouts.master')
@section('title', 'Template Surat - E-Sign')
@section('link')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
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
                    <h4 class="mb-sm-0">Template Surat @if($selectedType) — {{ $selectedType->name }} @endif</h4>
                    <small class="text-muted">Digital Signature Management System</small>
                </div>
            </div>
            <div class="page-title-right d-flex align-items-center gap-2">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">E-Sign Management</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('e-sign.jenis-surat') }}">Jenis Surat</a></li>
                    <li class="breadcrumb-item active">Template Surat @if($selectedType) — {{ $selectedType->name }} @endif</li>
                </ol>
                <a href="{{ route('e-sign.templates.create', ['letter_type_id' => $selectedType->id]) }}" class="btn btn-sm btn-primary ms-2">
                    <i class="ri-add-line me-1"></i> Tambah Template
                </a>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="ri-checkbox-circle-line me-1"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- Info Jenis Surat --}}
<div class="row mb-3">
    <div class="col-md-12">
        <div class="d-flex align-items-center justify-content-between bg-light rounded p-3">
            <div class="d-flex align-items-center gap-3">
                <div class="flex-shrink-0" style="width:42px;height:42px;background:linear-gradient(135deg,#0ab39c,#405189);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                    <i class="ri-file-copy-2-line text-white fs-20"></i>
                </div>
                <div>
                    <div class="fw-semibold fs-6">{{ $selectedType->name }}</div>
                    <small class="text-muted">
                        Prefix: <span class="badge bg-primary">{{ $selectedType->prefix }}</span>
                        &bull; {{ $templates->count() }} template
                    </small>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <div class="input-group input-group-sm" style="max-width:250px;">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="ri-search-line text-muted"></i>
                    </span>
                    <input type="text" id="searchTemplate" class="form-control" placeholder="Cari template..." value="{{ $search ?? '' }}">
                    @if($search)
                    <a href="{{ route('e-sign.templates', ['letter_type_id' => $letterTypeId]) }}" class="btn btn-outline-secondary">
                        <i class="ri-close-line"></i>
                    </a>
                    @endif
                </div>
                <a href="{{ route('e-sign.jenis-surat.edit', $selectedType->id) }}" class="btn btn-sm btn-outline-primary">
                    <i class="ri-settings-3-line me-1"></i> Kelola
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    @forelse($templates as $template)
    <div class="col-12 template-item" data-id="{{ $template->id }}">
        <div class="template-card d-flex align-items-center justify-content-between {{ $template->is_active ? 'active-template' : '' }}">
            <div class="d-flex align-items-center gap-3">
                <div>
                    <i class="ri-file-text-line fs-3 text-{{ $template->is_active ? 'success' : 'secondary' }}"></i>
                </div>
                <div>
                    <div class="fw-semibold">{{ $template->title }}</div>
                    <small class="text-muted">
                        <span class="badge bg-light text-dark border">{{ $template->letterType->name ?? $template->jenis_surat_label }}</span>
                        &bull; {{ $template->created_at->diffForHumans() }}
                        @if($template->creator)
                        &bull; oleh {{ $template->creator->name }}
                        @endif
                    </small>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                @if($template->is_active)
                <span class="badge bg-success">Aktif</span>
                @endif
                <button type="button" class="btn btn-sm btn-outline-info btn-preview-template" data-id="{{ $template->id }}" data-title="{{ $template->title }}">
                    <i class="ri-eye-line"></i> Preview
                </button>
                <a href="{{ route('e-sign.templates.edit', $template->id) }}" class="btn btn-sm btn-outline-primary">
                    <i class="ri-pencil-line"></i>
                </a>
                @if(!$template->is_active)
                <button type="button" class="btn btn-sm btn-outline-success btn-set-active" data-id="{{ $template->id }}">
                    <i class="ri-check-double-line"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger btn-delete-template"
                    data-id="{{ $template->id }}" data-title="{{ $template->title }}">
                    <i class="ri-delete-bin-line"></i>
                </button>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="text-center py-5">
            <i class="ri-file-text-line fs-1 text-muted"></i>
            <p class="text-muted mt-2">Belum ada template surat.</p>
            <a href="{{ route('e-sign.templates.create', ['letter_type_id' => $selectedType->id ?? null]) }}" class="btn btn-primary">
                <i class="ri-add-line me-1"></i> Tambah Template
            </a>
        </div>
    </div>
    @endforelse
</div>

{{-- Preview Template Modal --}}
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewModalLabel">
                    <i class="ri-eye-line me-1"></i> Preview Template
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="background:#e8eaed;padding:2rem;">
                <div class="preview-a4-wrapper">
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
                    <i class="ri-information-line me-1"></i> Tanda @{{variable}} adalah placeholder yang akan diganti dengan data real.
                </small>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<style>
    /* ============================================================
       A4 Preview — tampilan seperti kertas di layar
       ============================================================ */
    .preview-a4-wrapper {
        display: flex;
        justify-content: center;
    }
    .preview-a4-page {
        background: #ffffff;
        width: 210mm;
        height: 297mm;
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
    /* Kop surat */
    .preview-kop {
        margin-left: -0.8cm;
        margin-right: -0.8cm;
        margin-bottom: 0.3em;
        margin-top: -0.6cm;
    }
    .preview-kop img {
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
</style>

<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('javascript')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        $('.btn-delete-template').on('click', function() {
            const id = $(this).data('id');
            const title = $(this).data('title');
            Swal.fire({
                title: 'Hapus Template?',
                text: 'Yakin ingin menghapus template "' + title + '"?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ url("e-sign/templates") }}/' + id,
                        type: 'POST',
                        data: {
                            _method: 'DELETE',
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(res) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: res.message || 'Template berhasil dihapus.',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                $('.template-item[data-id="' + id + '"]').fadeOut(300, function() { $(this).remove(); });
                            });
                        },
                        error: function(xhr) {
                            let msg = 'Gagal menghapus template.';
                            if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                            Swal.fire({ icon: 'error', title: 'Gagal!', text: msg });
                        }
                    });
                }
            });
        });

        $('.btn-set-active').on('click', function() {
            const id = $(this).data('id');
            Swal.fire({
                title: 'Jadikan Aktif?',
                text: 'Template ini akan menjadi template aktif.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0ab39c',
                confirmButtonText: 'Ya, Aktifkan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ url("e-sign/templates") }}/' + id + '/set-active',
                        type: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(res) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Template aktif berhasil diperbarui.',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            let msg = 'Gagal mengaktifkan template.';
                            if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                            Swal.fire({ icon: 'error', title: 'Gagal!', text: msg });
                        }
                    });
                }
            });
        });

        // Preview template modal
        $('.btn-preview-template').on('click', function() {
            const id = $(this).data('id');
            const title = $(this).data('title');
            $('#previewModalLabel').text(title);
            $('#previewModalBody').html(`
                <div class="text-center text-muted py-5">
                    <i class="ri-loader-2-line fs-1"></i>
                    <p class="mt-2">Memuat preview...</p>
                </div>
            `);
            $('#previewModal').modal('show');

            $.ajax({
                url: '{{ url("e-sign/templates") }}/' + id + '/preview',
                type: 'GET',
                data: { ajax: 1 },
                success: function(res) {
                    let content = res.rendered || '<p class="text-muted">Tidak ada konten template.</p>';
                    let fullHtml = `
                        <div class="preview-kop">
                            <img src="{{ url('') }}/assets/images/KOP-terbaru.png" alt="Kop Surat" class="kop-img-full">
                        </div>
                    ` + content;
                    fullHtml = fullHtml.replace(/\{\{(\w+)\}\}/g, '<span class="preview-placeholder">@{{$1}}</span>');
                    renderPreviewPages(fullHtml);
                },
                error: function() {
                    $('#previewModalBody').html('<div class="text-center text-muted py-5"><i class="ri-error-warning-line fs-1"></i><p class="mt-2">Gagal memuat preview.</p></div>');
                }
            });
        });

        function renderPreviewPages(html) {
            var wrapper = document.querySelector('#previewModalBody');
            if (!wrapper) return;

            // Hapus semua page kecuali yang pertama
            var wrapperParent = wrapper.parentNode;
            var pages = wrapperParent.querySelectorAll('.preview-a4-page');
            for (var i = 1; i < pages.length; i++) pages[i].remove();

            wrapper.innerHTML = html;

            // Tunggu gambar selesai loading, baru hitung pagination
            var images = wrapper.querySelectorAll('img');
            var pending = images.length;
            if (pending === 0) {
                doPagination(wrapper);
            } else {
                var loaded = 0;
                images.forEach(function(img) {
                    if (img.complete) {
                        loaded++;
                        if (loaded >= pending) doPagination(wrapper);
                    } else {
                        img.onload = function() {
                            loaded++;
                            if (loaded >= pending) doPagination(wrapper);
                        };
                    }
                });
            }
        }

        function doPagination(firstPage) {
            var maxH = 297 * 3.78; // 297mm ke px
            var allPages = [firstPage];
            var pageIndex = 0;

            while (pageIndex < allPages.length) {
                var currentPage = allPages[pageIndex];
                var safety = 0;

                while (currentPage.scrollHeight > maxH + 5 && safety < 50) {
                    safety++;
                    var children = currentPage.children;
                    if (children.length <= 1) break; // sisakan minimal 1 elemen

                    var lastEl = children[children.length - 1];

                    var nextPage = allPages[pageIndex + 1];
                    if (!nextPage) {
                        nextPage = document.createElement('div');
                        nextPage.className = 'preview-a4-page';
                        nextPage.style.marginTop = '0';
                        currentPage.parentNode.insertBefore(nextPage, currentPage.nextSibling);
                        allPages.push(nextPage);
                    }
                    nextPage.insertBefore(lastEl, nextPage.firstChild);
                }
                pageIndex++;
            }
        }
    });
</script>
@endsection
