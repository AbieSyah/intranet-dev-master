@extends('layouts.master')
@section('title', 'Buat Surat Baru - E-Sign')
@section('link')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ url('') }}/assets/libs/adminlte/select2/css/4.1.0/select2.min.css" rel="stylesheet">
    <style>
        .select-card {
            border: 2px solid #e9ecef;
            border-radius: 14px;
            cursor: pointer;
            transition: all 0.2s ease;
            height: 100%;
            padding: 1rem;
            word-break: break-word;
            overflow-wrap: break-word;
        }
        .select-card:hover {
            border-color: #405189;
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(64,81,137,0.12);
        }
        .select-card.selected {
            border-color: #405189;
            background: #f0f4ff;
        }
        .select-card .card-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }
        .select-card .card-title {
            font-size: 14px;
            font-weight: 600;
            color: #1e293b;
            line-height: 1.3;
            word-break: break-word;
        }
        .select-card .card-sub {
            font-size: 12px;
            color: #6c757d;
            line-height: 1.4;
            word-break: break-word;
        }
        .select-card .template-badge {
            font-size: 11px;
            padding: 2px 8px;
            border-radius: 20px;
            white-space: normal;
            word-break: break-word;
            max-width: 100%;
            display: inline-block;
        }
        .select-card .card-text-wrap {
            min-width: 0;
            overflow: hidden;
        }
        .summary-box {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 1.25rem;
            min-height: 120px;
        }
        .summary-box .summary-empty {
            color: #adb5bd;
        }
        .summary-box .summary-item {
            padding: 0.5rem 0;
            border-bottom: 1px solid #e9ecef;
            word-break: break-word;
            overflow-wrap: break-word;
        }
        .summary-box .summary-item:last-child {
            border-bottom: none;
        }
        .summary-box .summary-item .ri-user-line {
            flex-shrink: 0;
        }
        /* Fix Select2 dropdown agar tidak overflow */
        .select2-container {
            width: 100% !important;
        }
        .select2-container .select2-selection--single {
            height: 38px;
            display: flex;
            align-items: center;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            word-break: break-word;
            white-space: normal;
            padding-right: 30px;
        }
        .select2-results__option {
            word-break: break-word;
            white-space: normal;
            padding: 6px 12px;
        }
        @media (max-width: 576px) {
            .select-card {
                padding: 0.75rem;
            }
            .select-card .card-icon {
                width: 36px;
                height: 36px;
                font-size: 16px;
            }
            .select-card .card-title {
                font-size: 13px;
            }
            .create-step-title {
                font-size: 15px !important;
            }
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
                    <h4 class="mb-sm-0">Buat Surat Baru</h4>
                    <small class="text-muted">Digital Signature Management System</small>
                </div>
            </div>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('e-sign.daftar-surat') }}">E-Sign Management</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('e-sign.daftar-surat') }}">Daftar Surat</a></li>
                    <li class="breadcrumb-item active">Buat Surat</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="alert alert-soft-info d-flex align-items-center gap-2 mb-4 py-2 px-3" role="alert">
            <i class="ri-information-line"></i>
            <span>Pilih jenis surat, lalu pilih template yang akan digunakan.</span>
        </div>
    </div>
</div>

<form action="{{ route('e-sign.store') }}" method="POST" id="formCreate">
    @csrf
    <input type="hidden" name="jenis_surat_slug" id="inputJenisSuratSlug" value="">
    <input type="hidden" name="letter_type_id" id="inputLetterTypeId" value="">
    <input type="hidden" name="template_id" id="inputTemplateId" value="">
    <input type="hidden" name="employee_id" id="inputEmployeeId" value="">
    <input type="hidden" name="title" id="inputTitle" value="">
    <input type="hidden" name="content" id="inputContent" value="">

    <div class="row">
        {{-- LEFT: Steps --}}
        <div class="col-lg-8 col-sm-12">
            {{-- STEP 1: Pilih Jenis Surat --}}
            <div class="card" style="border:none;border-radius:14px;">
                <div class="card-body" style="padding:1.5rem;">
                    @if($preselectedTypeId)
                        @php $selectedType = $letterTypes->firstWhere('id', $preselectedTypeId); @endphp
                        <h5 class="card-title mb-3 create-step-title">
                            <span class="badge bg-primary me-1">1</span> Jenis Surat
                        </h5>
                        <div class="select-card selected" style="cursor:default;max-width:400px;" id="preselectedType" data-type-id="{{ $selectedType->id }}" data-slug="{{ $selectedType->slug }}" data-name="{{ $selectedType->name }}">
                            <div class="d-flex align-items-start gap-2">
                                <div class="card-icon bg-info bg-opacity-10 text-info">
                                    <i class="{{ $selectedType->icon ?? 'ri-file-text-line' }}"></i>
                                </div>
                                <div class="card-text-wrap flex-grow-1">
                                    <div class="card-title">{{ $selectedType->name ?? 'Unknown' }}</div>
                                    @if($selectedType && $selectedType->description)
                                    <div class="card-sub mt-1">{{ $selectedType->description }}</div>
                                    @endif
                                </div>
                                <a href="{{ route('e-sign.create-select') }}" class="btn btn-sm btn-outline-secondary ms-2 flex-shrink-0">
                                    Ganti
                                </a>
                            </div>
                        </div>
                    @else
                        <h5 class="card-title mb-3 create-step-title">
                            <span class="badge bg-primary me-1">1</span> Pilih Jenis Surat
                        </h5>
                        <div class="row g-2 g-md-3">
                            @foreach($letterTypes as $type)
                            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12">
                                <div class="select-card select-type" data-type-id="{{ $type->id }}" data-slug="{{ $type->slug }}" data-name="{{ $type->name }}">
                                    <div class="d-flex align-items-start gap-2">
                                        <div class="card-icon bg-{{ $type->color }} bg-opacity-10 text-{{ $type->color }}">
                                            <i class="{{ $type->icon }}"></i>
                                        </div>
                                        <div class="card-text-wrap flex-grow-1">
                                            <div class="card-title">{{ $type->name }}</div>
                                            @if($type->description)
                                            <div class="card-sub mt-1">{{ $type->description }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- STEP 2: Pilih Template (muncul setelah pilih jenis surat) --}}
            <div class="card" style="border:none;border-radius:14px;display:none;" id="stepTemplate">
                <div class="card-body" style="padding:1.5rem;">
                    <h5 class="card-title mb-3 create-step-title">
                        <span class="badge bg-primary me-1">2</span> Pilih Template
                    </h5>
                    <div id="templateList" class="row g-2 g-md-3">
                        {{-- Akan diisi oleh JavaScript --}}
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT: Ringkasan & Aksi --}}
        <div class="col-lg-4 col-sm-12">
            <div class="card" style="border:none;border-radius:14px;position:sticky;top:1rem;">
                <div class="card-body" style="padding:1.5rem;">
                    <h5 class="card-title mb-3 d-flex align-items-center gap-2">
                        <i class="ri-survey-line"></i> Ringkasan
                    </h5>
                    <div class="summary-box" id="summaryContent">
                        <div class="summary-empty text-center py-4">
                            <i class="ri-file-text-line fs-1 text-muted"></i>
                            <p class="text-muted mt-2 mb-0">Belum ada yang dipilih.</p>
                            <small class="text-muted">Pilih jenis surat terlebih dahulu.</small>
                        </div>
                    </div>
                    <hr class="my-3">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary" id="btnLanjutkan" disabled>
                            <i class="ri-arrow-right-line me-1"></i> Lanjutkan ke Editor
                        </button>
                        <a href="{{ route('e-sign.daftar-surat') }}" class="btn btn-outline-secondary">
                            <i class="ri-arrow-left-line me-1"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

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
                    <i class="ri-information-line me-1"></i> Tanda @{{variable}} akan diganti dengan data real saat generate surat.
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
        margin: 0 auto;
        display: block;
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

    /* Preview button di kartu template */
    .preview-template-btn {
        opacity: 0.6;
        transition: all 0.15s ease;
    }
    .select-card:hover .preview-template-btn {
        opacity: 1;
    }
    .preview-template-btn:hover {
        background: #e9ecef !important;
        border-color: #adb5bd !important;
    }
</style>
@endsection

@section('javascript')
<script>
    // Templates data from server
    const templatesData = @json($templatesData);
    const preselectedTypeId = @json($preselectedTypeId ?? null);

    $(document).ready(function() {
        // State
        let selectedTypeId = null;
        let selectedSlug = null;
        let selectedName = null;
        let selectedTemplateId = null;
        let selectedTemplateName = null;

        // Auto-select letter type if preselectedTypeId is set
        if (preselectedTypeId) {
            const $card = $(`#preselectedType`);
            if ($card.length) {
                selectLetterType($card);
            }
        }

        // Click letter type card
        $('.select-type').on('click', function() {
            selectLetterType($(this));
        });

        function selectLetterType($card) {
            $('.select-type').removeClass('selected');
            $card.addClass('selected');

            selectedTypeId = $card.data('type-id');
            selectedSlug = $card.data('slug');
            selectedName = $card.data('name');
            selectedTemplateId = null;
            selectedTemplateName = null;

            // Reset template selection
            $('#inputTemplateId').val('');
            $('#inputEmployeeId').val('');

            // Set hidden inputs
            $('#inputJenisSuratSlug').val(selectedSlug);
            $('#inputLetterTypeId').val(selectedTypeId);
            $('#inputTitle').val(selectedName);

            // Show & populate template list
            renderTemplates(selectedTypeId);
            updateSummary();
        }

        // Click template card (delegated)
        $(document).on('click', '.select-template', function() {
            $('.select-template').removeClass('selected');
            $(this).addClass('selected');

            selectedTemplateId = $(this).data('id');
            selectedTemplateName = $(this).data('title');

            $('#inputTemplateId').val(selectedTemplateId);

            updateSummary();
        });

        function renderTemplates(typeId) {
            const templates = templatesData[typeId] || [];
            const $list = $('#templateList');
            const $step = $('#stepTemplate');

            if (templates.length === 0) {
                $list.html(`
                    <div class="col-12">
                        <div class="text-center py-4">
                            <i class="ri-file-text-line fs-1 text-muted"></i>
                            <p class="text-muted mt-2 mb-0">Tidak ada template untuk jenis surat ini.</p>
                        </div>
                    </div>
                `);
            } else {
                let html = '';
                templates.forEach(function(tpl) {
                    html += `
                        <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12">
                            <div class="select-card select-template ${tpl.is_active ? 'selected' : ''}" data-id="${tpl.id}" data-title="${tpl.title}">
                                <div class="d-flex align-items-start gap-2">
                                    <div class="card-icon bg-info bg-opacity-10 text-info">
                                        <i class="ri-file-copy-2-line"></i>
                                    </div>
                                    <div class="card-text-wrap flex-grow-1">
                                        <div class="card-title">${tpl.title}</div>
                                        <div class="card-sub mt-1">${tpl.created_at}</div>
                                        ${tpl.is_active ? '<span class="badge bg-success mt-1">Aktif</span>' : ''}
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-secondary preview-template-btn" data-id="${tpl.id}" title="Preview Template" style="font-size:11px;padding:2px 6px;flex-shrink:0;">
                                        <i class="ri-eye-line"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                });
                $list.html(html);

                // Auto-select active template
                const activeTpl = templates.find(t => t.is_active);
                if (activeTpl) {
                    selectedTemplateId = activeTpl.id;
                    selectedTemplateName = activeTpl.title;
                    $('#inputTemplateId').val(activeTpl.id);
                }
            }

            $step.show();
            // Scroll ke step template
            $('html, body').animate({
                scrollTop: $step.offset().top - 100
            }, 300);
        }

        function updateSummary() {
            if (selectedTypeId && selectedSlug && selectedName) {
                let html = `
                    <div class="summary-item d-flex align-items-center gap-2">
                        <i class="ri-grid-line text-primary fs-5"></i>
                        <div>
                            <small class="text-muted d-block">Jenis Surat</small>
                            <span class="fw-medium">${selectedName}</span>
                        </div>
                    </div>
                `;
                if (selectedTemplateId && selectedTemplateName) {
                    html += `
                        <div class="summary-item d-flex align-items-center gap-2">
                            <i class="ri-file-copy-2-line text-info fs-5"></i>
                            <div>
                                <small class="text-muted d-block">Template</small>
                                <span class="fw-medium">${selectedTemplateName}</span>
                            </div>
                        </div>
                    `;
                }
                $('#summaryContent').html(html);
                $('#btnLanjutkan').prop('disabled', !selectedTemplateId);
            } else {
                $('#summaryContent').html(`
                    <div class="summary-empty text-center py-4">
                        <i class="ri-file-text-line fs-1 text-muted"></i>
                        <p class="text-muted mt-2 mb-0">Belum ada yang dipilih.</p>
                        <small class="text-muted">Pilih jenis surat terlebih dahulu.</small>
                    </div>
                `);
                $('#btnLanjutkan').prop('disabled', true);
            }
        }

        // Preview template
        $(document).on('click', '.preview-template-btn', function(e) {
            e.stopPropagation();
            const tplId = $(this).data('id');
            let tplData = null;
            Object.values(templatesData).some(function(arr) {
                const found = arr.find(t => t.id == tplId);
                if (found) { tplData = found; return true; }
                return false;
            });
            if (tplData) {
                showPreview(tplData);
            }
        });

        function showPreview(tpl) {
            let content = tpl.content || '<p class="text-muted">Tidak ada konten template.</p>';

            // Kop surat (letterhead) — gambar utuh
            let fullHtml = `
                <div class="preview-kop">
                    <img src="{{ url('') }}/assets/images/KOP-terbaru.png" alt="Kop Surat" class="kop-img-full">
                </div>
            ` + content;

            // Highlight placeholders — escape Blade with @
            fullHtml = fullHtml.replace(/\{\{(\w+)\}\}/g, '<span class="preview-placeholder">@{{$1}}</span>');

            $('#previewModalLabel').text(tpl.title);
            renderPreviewPages(fullHtml);
            $('#previewModal').modal('show');
        }

        function renderPreviewPages(html) {
            var wrapper = document.querySelector('#previewModalBody');
            if (!wrapper) return;

            var wrapperParent = wrapper.parentNode;
            var pages = wrapperParent.querySelectorAll('.preview-a4-page');
            for (var i = 1; i < pages.length; i++) pages[i].remove();

            wrapper.innerHTML = html;

            // Tunggu gambar selesai loading
            var imgs = wrapper.querySelectorAll('img');
            if (imgs.length === 0) {
                doPagination(wrapper);
            } else {
                var total = imgs.length, loaded = 0;
                imgs.forEach(function(img) {
                    if (img.complete) { loaded++; if (loaded >= total) doPagination(wrapper); }
                    else { img.onload = function() { loaded++; if (loaded >= total) doPagination(wrapper); }; }
                });
            }
        }

        function doPagination(firstPage) {
            var maxH = 297 * 3.78;
            var allPages = [firstPage];
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
        }

        // Form submit — redirect ke editor dengan membawa template_id
        $('#formCreate').on('submit', function(e) {
            if (!selectedTypeId || !selectedTemplateId) {
                e.preventDefault();
                return;
            }
            e.preventDefault();
            if (selectedSlug && selectedTemplateId) {
                window.location.href = '{{ url("e-sign/create") }}/' + selectedSlug + '?template_id=' + selectedTemplateId;
            }
        });
    });
</script>
@endsection
