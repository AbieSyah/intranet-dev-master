{{--
-- ============================================================================
-- PARTIAL: _document-pdf.blade.php
-- KHUSUS untuk generate PDF (dipanggil dari ESignController@generatePdf).
-- Struktur HTML dibuat sederhana agar position: fixed footer berfungsi
-- dengan baik di dompdf.
-- ============================================================================
-- Variabel:
--   $doc   – Instance of App\Models\ESign (with employee loaded)
--   $data  – ['title', 'number', 'slug', 'logo']
-- ============================================================================
--}}<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<style>
    @page {
        margin-top: 2cm;
        margin-bottom: 2.5cm;
        margin-left: 2cm;
        margin-right: 2cm;
    }

    body {
        font-family: serif;
        font-size: 12pt;
        line-height: 1.5;
        color: #212529;
        margin: 0;
        padding: 0;
        width: 100%;
    }

    /* Kop surat */
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

    /* Signature area */
    .signature-area {
        text-align: center;
        margin-top: 32px;
    }
    .signature-box {
        display: inline-block;
        text-align: center;
        min-width: 160px;
        margin: 0 16px;
        vertical-align: top;
    }
    .signature-box .sig-label {
        font-weight: 700;
        font-size: 13px;
        margin-bottom: 4px;
        color: #1e293b;
    }
    .signature-box .sig-qr {
        width: 130px;
        height: 90px;
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
    .signature-box .sig-name {
        font-size: 12px;
        font-weight: 600;
        color: #212529;
        margin-bottom: 2px;
    }
    .signature-box .sig-pos {
        font-size: 11px;
        color: #6c757d;
    }

    /* Footer fixed di底部 setiap halaman — ditempatkan di area margin bawah */
    .pdf-footer {
        position: fixed;
        bottom: -2.2cm;
        left: 2cm;
        right: 2cm;
        text-align: center;
        font-size: 9px;
        color: #6c757d;
        line-height: 1.4;
        border-top: 1px solid #dee2e6;
        padding-top: 6px;
    }

    /* Page break */
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
</head>
<body>

{{-- Footer fixed — muncul di setiap halaman PDF --}}
@if(!$doc->isDraft())
<div class="pdf-footer">
    <i class="ri-shield-check-line me-1"></i>
    Dokumen diterbitkan melalui sistem INTRANET E-Sign pada
    <strong>{{ $doc->updated_at ? \Carbon\Carbon::parse($doc->updated_at)->format('d/m/Y H:i') : '-' }}</strong>
    oleh <strong>{{ $doc->creator->name ?? $doc->creator->employee->fullname ?? '-' }}</strong>.
    Sah secara hukum tanpa tanda tangan basah; pindai kode QR untuk verifikasi.
</div>
@endif

{{-- Header (kop surat + judul) --}}
<div class="company-header">
    @if(!empty($data['logo']))
    <img src="{{ $data['logo'] }}" alt="Kop Surat">
    @else
    <img src="{{ url('') }}/assets/images/KOP-terbaru.png" alt="Kop Surat">
    @endif
</div>

<div class="doc-title">{{ $data['title'] }}</div>
<div class="doc-number">Nomor: {{ $doc->nomor_surat ?? $data['number'] }}</div>

{{-- Konten --}}
@if(!empty($doc->content))
    <div class="doc-content">
    {!! $doc->content !!}
    </div>

    {{-- Signature area --}}
    @if(strpos($doc->content, 'signature-box') === false && strpos($doc->content, 'QR Code') === false)
        @php
            $signee1 = $doc->employee1_signee_id ? \App\Models\Employee::with('position')->find($doc->employee1_signee_id) : null;
            $signee2 = $doc->employee2_signee_id ? \App\Models\Employee::with('position')->find($doc->employee2_signee_id) : null;
            $signee3 = $doc->employee3_signee_id ? \App\Models\Employee::with('position')->find($doc->employee3_signee_id) : null;
        @endphp
        <div class="signature-area">
            @if($signee1 || $doc->employee1_signee_id)
            <div class="signature-box">
                <div class="sig-label">Sign 1</div>
                <div class="sig-qr">QR Code<br>Digital Signature</div>
                <div class="sig-name">{{ $signee1->fullname ?? '—' }}</div>
                <div class="sig-pos">{{ $signee1->position->nama ?? '—' }}</div>
            </div>
            @endif
            @if($signee2 || $doc->employee2_signee_id)
            <div class="signature-box">
                <div class="sig-label">Sign 2</div>
                <div class="sig-qr">QR Code<br>Digital Signature</div>
                <div class="sig-name">{{ $signee2->fullname ?? '—' }}</div>
                <div class="sig-pos">{{ $signee2->position->nama ?? '—' }}</div>
            </div>
            @endif
            @if($signee3 || $doc->employee3_signee_id)
            <div class="signature-box">
                <div class="sig-label">Sign 3</div>
                <div class="sig-qr">QR Code<br>Digital Signature</div>
                <div class="sig-name">{{ $signee3->fullname ?? '—' }}</div>
                <div class="sig-pos">{{ $signee3->position->nama ?? '—' }}</div>
            </div>
            @endif
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

</body>
</html>
