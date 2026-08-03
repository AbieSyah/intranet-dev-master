{{--
-- ============================================================================
-- PARTIAL: _document-content.blade.php
--
-- Menampilkan isi surat. Prioritas:
-- 1. $doc->content — konten yang sudah diisi user saat membuat draft
--    (placeholder sudah diganti dengan data dari form).
--    Dibungkus dengan header (kop surat) dan signature area.
-- 2. Fallback ke template Blade berdasarkan jenis_surat_slug
--    (digunakan untuk surat lama yang belum punya content).
--
-- Variabel yang dikirim ke template:
--   $doc   – Instance of App\Models\ESign (with employee loaded)
--   $data  – ['title' => string, 'number' => string, 'slug' => string]
-- ============================================================================
--}}
{{-- CSS untuk web preview --}}
<style>
    body {
        margin: 0;
        padding: 0;
        width: 100%;
    }

    .company-header {
        text-align: center;
        margin-bottom: 8px;
        margin-top: 0;
        margin-left: -0.8cm;
        margin-right: -0.8cm;
        padding-top: 0;
    }
    .company-header img {
        width: 85%;
        height: auto;
        display: block;
        margin: 0 auto;
    }
    .doc-title {
        text-align: center;
        font-size: 16px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 4px;
        color: #1e293b;
    }
    .doc-number {
        text-align: center;
        font-size: 12px;
        color: #6c757d;
        margin-bottom: 28px;
        font-weight: 500;
    }
    .signature-area {
        display: flex;
        flex-wrap: wrap;
        gap: 32px;
        justify-content: center;
        margin-top: 32px;
    }
    .signature-box {
        text-align: center;
        min-width: 160px;
    }

    .company-header, .doc-title, .doc-number {
        page-break-inside: avoid;
    }
    .signature-area {
        page-break-inside: avoid;
    }
    .doc-content {
        page-break-inside: auto;
    }
</style>

@if(!empty($doc->content))
    {{-- Header (kop surat + judul) --}}
    @include('pages.e-sign.partials.header')

    {{-- Isi konten dari form create/edit --}}
    <div class="doc-content">
    {!! $doc->content !!}
    </div>

    {{-- Signature area --}}
    @if(strpos($doc->content, 'signature-box') === false && strpos($doc->content, 'QR Code') === false)
        @include('pages.e-sign.partials.signature')
    @endif

    {{-- Footer note untuk web preview --}}
    @if(!$doc->isDraft())
    <div class="preview-footer-note-inline" style="margin-top:48px;padding-top:16px;border-top:1px solid #dee2e6;font-size:10px;color:#6c757d;line-height:1.5;text-align:center;">
        <i class="ri-shield-check-line me-1"></i>
        Dokumen diterbitkan melalui sistem INTRANET E-Sign pada
        <strong>{{ $doc->updated_at ? \Carbon\Carbon::parse($doc->updated_at)->format('d/m/Y H:i') : '-' }}</strong>
        oleh <strong>{{ $doc->creator->name ?? $doc->creator->employee->fullname ?? '-' }}</strong>.
        Sah secara hukum tanpa tanda tangan basah; pindai kode QR untuk verifikasi.
    </div>
    @endif

@else
    {{-- Fallback template --}}
    @php
        $templateSlug = $doc->jenis_surat_slug ?? $data['slug'] ?? 'default';
        $templateView = 'pages.e-sign.templates.' . $templateSlug;
    @endphp

    @if(view()->exists($templateView))
        @include($templateView)
    @else
        @include('pages.e-sign.templates.default')
    @endif
@endif
