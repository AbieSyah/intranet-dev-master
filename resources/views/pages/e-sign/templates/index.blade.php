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
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewModalLabel">
                    <i class="ri-eye-line me-1"></i> Preview Template
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="background:#e8eaed;padding:2rem;overflow-y:auto;max-height:calc(100vh - 10rem);">
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
        flex-direction: column;
        align-items: center;
        width: 100%;
    }
    /* Pemisah antar kertas (page break) */
    .preview-page-break {
        width: 70%;
        max-width: 420px;
        margin: 4rem auto 4.5rem auto;
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
    .preview-a4-page {
        background: #ffffff;
        width: 210mm;
        box-sizing: border-box;
        min-height: 297mm;
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
    /* Kop + judul + nomor pada preview — disamakan dengan preview editor template */
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
                    // Kop + judul + nomor + tanda tangan — disamakan dengan preview editor template.
                    // Kop berulang di tiap halaman; judul & nomor hanya di halaman pertama.
                    let fullHtml = `
                        <div class="company-header">
                            <img src="{{ url('') }}/assets/images/KOP-terbaru.png" alt="Kop Surat" class="kop-img-full">
                        </div>
                        <div class="doc-title">Judul Surat</div>
                        <div class="doc-number">Nomor: _______________</div>
                    ` + content + buildPreviewSignatureHtml(res);
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

            // Hapus semua page & page break kecuali yang pertama
            var wrapperParent = wrapper.parentNode;
            var pages = wrapperParent.querySelectorAll('.preview-a4-page');
            for (var i = 1; i < pages.length; i++) pages[i].remove();
            wrapperParent.querySelectorAll('.preview-page-break').forEach(function(b) { b.remove(); });

            wrapper.innerHTML = html;

            // Paginasi SETELAH modal tampil penuh (agar ukuran terukur benar), menunggu gambar.
            // Kalau konten tiba sebelum modal tampil (via AJAX), tunggu dulu event shown.
            function run() {
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
            if ($('#previewModal').hasClass('show')) {
                run();
            } else {
                $('#previewModal').one('shown.bs.modal', run);
            }
        }

                // Gambaran area tanda tangan — mengikuti setting sign template (sign_1/2/3)
        function buildPreviewSignatureHtml(tpl) {
            var s1 = (tpl && tpl.sign_1) ? 1 : 0;
            var s2 = (tpl && tpl.sign_2) ? 1 : 0;
            var s3 = (tpl && tpl.sign_3) ? 1 : 0;
            function box(label) {
                return '<div class="editor-sign-box">' +
                    '<div class="editor-sign-box-label">' + label + '</div>' +
                    '<div class="editor-sign-box-qr">QR Code<br>Digital Signature</div>' +
                    '</div>';
            }
            var left = s2 ? box('Sign 2') : '';
            var center = s3 ? box('Sign 3') : '';
            var right = s1 ? box('Sign 1') : '';
            return '<div class="preview-sign-placeholder">' +
                '<div class="editor-sign-slot">' + left + '</div>' +
                '<div class="editor-sign-slot">' + center + '</div>' +
                '<div class="editor-sign-slot">' + right + '</div>' +
                '</div>';
        }

        // Paginasi preview — disamakan dengan preview editor template:
        // kop berulang di tiap halaman, judul/nomor halaman 1 saja, tanda tangan di akhir.
        function doPagination(wrapper) {
            var source = document.createElement('div');
            while (wrapper.firstChild) source.appendChild(wrapper.firstChild);

            // Pisahkan kop (berulang), judul & nomor (halaman 1), dan area tanda tangan (akhir)
            var kopEl = null, hasKop = false;
            var k = source.querySelector('.company-header');
            if (k) { kopEl = k; k.parentNode.removeChild(k); hasKop = true; }
            var titleEl = source.querySelector('.doc-title');
            var numEl = source.querySelector('.doc-number');
            var sig = source.querySelector('.preview-sign-placeholder');
            if (sig) sig.parentNode.removeChild(sig);

            // Halaman pertama = wrapper
            wrapper.innerHTML = '';
            if (hasKop) wrapper.appendChild(kopEl.cloneNode(true));
            if (titleEl) wrapper.appendChild(titleEl);
            if (numEl) wrapper.appendChild(numEl);

            var maxH = 297 * 3.78; // 297mm ke px
            var allPages = [wrapper];
            var pageIndex = 0;

            var nodes = Array.prototype.slice.call(source.childNodes);
            for (var i = 0; i < nodes.length; i++) wrapper.appendChild(nodes[i]);

            function makeNext(curr) {
                var np = document.createElement('div');
                np.className = 'preview-a4-page';
                curr.parentNode.insertBefore(np, curr.nextSibling);
                // Page break visual di antara kertas
                var brk = document.createElement('div');
                brk.className = 'preview-page-break';
                curr.parentNode.insertBefore(brk, np);
                // Halaman berikutnya diawali kop surat
                if (hasKop) np.appendChild(kopEl.cloneNode(true));
                allPages.push(np);
                return np;
            }

            while (pageIndex < allPages.length) {
                var currentPage = allPages[pageIndex];
                var safety = 0;
                while (currentPage.scrollHeight > maxH + 5 && safety < 50) {
                    safety++;
                    var children = currentPage.children;
                    if (children.length <= 1) break; // sisakan minimal 1 elemen

                    var lastEl = children[children.length - 1];
                    var nextPage = allPages[pageIndex + 1] || makeNext(currentPage);
                    // Sisipkan di awal konten (setelah kop) agar urutan tetap benar
                    var ib = nextPage.children[hasKop ? 1 : 0];
                    if (ib) nextPage.insertBefore(lastEl, ib); else nextPage.appendChild(lastEl);
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
    });
</script>
@endsection
