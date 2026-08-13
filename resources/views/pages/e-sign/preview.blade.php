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
            width: 100%;
            max-width: 800px;
            padding: 0;
        }
        /* Setiap halaman A4 pada preview (dibuat otomatis via JS pagination) */
        .doc-preview-page {
            background: #fff;
            width: 100%;
            height: 1050px;
            padding: 60px 56px 70px 56px;
            box-sizing: border-box;
            box-shadow: 0 4px 24px rgba(0,0,0,0.10);
            border: 1px solid #dee2e6;
            border-radius: 4px;
            font-family: Calibri, Arial, sans-serif;
            font-size: 12pt;
            line-height: 1.5;
            color: #212529;
            position: relative;
            margin-bottom: 24px;
            overflow: hidden;
        }
        .doc-preview-page .page-inner {
            height: 100%;
            overflow: hidden;
        }
        .doc-preview-page .page-number {
            position: absolute;
            bottom: 24px;
            right: 56px;
            font-size: 10px;
            color: #6c757d;
            line-height: 1;
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
                {{-- Catatan penerbitan — masuk sebagai konten & ikut ter-pagination --}}
                @if(!$doc->isDraft())
                <div class="preview-footer-note">
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
    // ===== Pagination Dokumen Preview =====
    // Membagi isi surat menjadi beberapa halaman A4 dan menampilkan nomor halaman
    // dengan format "X / Y" (mengikuti total halaman).
    (function() {
        function paginateDocument() {
            var container = document.getElementById('docPreview');
            if (!container) return;

            // Dimensi halaman A4 preview (harus sinkron dengan CSS .doc-preview-page)
            var CONTENT_H = 1050 - 60 - 70; // 920 = tinggi area isi (.page-inner)

            // Snapshot seluruh node teratas lalu kosongkan container
            var source = document.createElement('div');
            while (container.firstChild) source.appendChild(container.firstChild);

            // Buka bungkus .doc-content agar paragraf jadi node sejajar dengan header,
            // sehingga konten bisa dipindah per-paragraf ke halaman berikutnya.
            var wrappers = source.querySelectorAll('.doc-content');
            for (var w = 0; w < wrappers.length; w++) {
                var dc = wrappers[w];
                while (dc.firstChild) source.insertBefore(dc.firstChild, dc);
                dc.remove();
            }

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
            var sigEl = source.querySelector('.esign-signature-area');
            if (sigEl) sigEl.parentNode.removeChild(sigEl);

            function makePage(withTitle) {
                var page = document.createElement('div');
                page.className = 'doc-preview-page';
                var inner = document.createElement('div');
                inner.className = 'page-inner';
                page.appendChild(inner);
                var num = document.createElement('div');
                num.className = 'page-number';
                page.appendChild(num);
                container.appendChild(page);
                // Setiap halaman diawali kop surat (logo); judul & nomor hanya halaman pertama
                if (hasKop) inner.appendChild(kopEl.cloneNode(true));
                if (withTitle) {
                    if (titleEl) inner.appendChild(titleEl);
                    if (numEl) inner.appendChild(numEl);
                }
                return { el: page, inner: inner };
            }

            var pages = [];
            var current = makePage(true);
            pages.push(current);

            // Tinggi isi halaman saat ini. scrollHeight mencerminkan tinggi isi sebenarnya
            // walau halaman berukuran tetap (height:100%), jadi halaman bertambah otomatis
            // sesuai banyaknya konten.
            function pageH() {
                return current.inner.scrollHeight;
            }

            function place(node) {
                if (node.nodeType === 3) {
                    if (node.textContent.trim() === '') return;
                    current.inner.appendChild(node);
                    return;
                }
                current.inner.appendChild(node);
                if (pageH() > CONTENT_H) {
                    current.inner.removeChild(node);
                    current = makePage(false);
                    pages.push(current);
                    current.inner.appendChild(node);
                }
            }

            var nodes = Array.prototype.slice.call(source.childNodes);
            for (var i = 0; i < nodes.length; i++) place(nodes[i]);

            // Tempel area tanda tangan di halaman TERAKHIR (di bawah konten).
            // Jika tidak muat, pindah ke halaman baru agar tetap di akhir dokumen.
            if (sigEl) {
                var lastInner = pages[pages.length - 1].inner;
                lastInner.appendChild(sigEl);
                if (lastInner.scrollHeight > CONTENT_H) {
                    lastInner.removeChild(sigEl);
                    var fresh = makePage(false);
                    pages.push(fresh);
                    fresh.inner.appendChild(sigEl);
                }
            }

            // Tulis nomor halaman "X / Y"
            for (var p = 0; p < pages.length; p++) {
                var num = pages[p].el.querySelector('.page-number');
                if (num) num.textContent = (p + 1) + ' / ' + pages.length;
            }
        }

        // Jalankan setelah seluruh konten (termasuk gambar kop surat) termuat
        if (document.readyState === 'complete') {
            paginateDocument();
        } else {
            window.addEventListener('load', paginateDocument);
        }
    })();
</script>
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
