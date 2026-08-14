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
        margin-top: 5.0cm;
        margin-bottom: 2.2cm;
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

    /* Kop surat — berulang di SETIAP halaman (position:fixed seperti footer) */
    .pdf-repeating-header {
        position: fixed;
        top: -5.0cm;
        left: 2cm;
        right: 2cm;
        text-align: center;
        z-index: 10;
    }
    .pdf-repeating-header .company-header {
        text-align: center;
        margin: 0;
        padding-top: 0;
    }
    .pdf-repeating-header .company-header img {
        width: 85%;
        height: auto;
        display: block;
        margin: 0 auto;
    }
    .pdf-repeating-header .doc-title {
        text-align: center;
        font-size: 16px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin: 4px 0 2px 0;
        color: #1e293b;
    }
    .pdf-repeating-header .doc-number {
        text-align: center;
        font-size: 12px;
        color: #6c757d;
        margin: 0 0 6px 0;
        font-weight: 500;
    }

    /* Judul & nomor surat — hanya di halaman pertama (flow) */
    .doc-title {
        text-align: center;
        font-size: 16px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin: 8px 0 4px 0;
        color: #1e293b;
    }
    .doc-number {
        text-align: center;
        font-size: 12px;
        color: #6c757d;
        margin-bottom: 24px;
        font-weight: 500;
    }

    /* Signature area */
    .signature-area {
        width: 100%;
        margin-top: 18px;
    }
    .sign-table {
        width: 100%;
        table-layout: fixed;
        border-collapse: collapse;
    }
    .sign-cell {
        width: 33.33%;
        vertical-align: top;
        text-align: center;
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
        bottom: -1.9cm;
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
    .doc-title, .doc-number {
        page-break-inside: avoid;
    }
    .signature-area {
        page-break-inside: avoid;
    }
    .doc-content {
        page-break-inside: auto;
    }

    /* DomPDF tidak mendukung flexbox dengan baik. Ubah area tanda tangan
       yang tertanam di konten (layout flex editor) menjadi tabel agar
       tampil rapi & tidak terlempar ke halaman berikutnya. */
    .esign-signature-area {
        display: table !important;
        width: 100% !important;
        table-layout: fixed !important;
        page-break-inside: avoid !important;
    }
    .esign-signature-area > div {
        display: table-cell !important;
        width: 33.33% !important;
        vertical-align: top !important;
    }
    .esign-signature-area > div:first-child {
        text-align: center !important;
    }
    .esign-signature-area > div:first-child > div {
        display: inline-block !important;
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

{{-- Header (kop surat/logo) — berulang di setiap halaman via position:fixed --}}
<div class="pdf-repeating-header">
    <div class="company-header">
        @if(!empty($data['logo']))
        <img src="{{ $data['logo'] }}" alt="Kop Surat">
        @else
        <img src="{{ url('') }}/assets/images/KOP-terbaru.png" alt="Kop Surat">
        @endif
    </div>
</div>

{{-- Judul & nomor surat — hanya di halaman pertama --}}
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

            // Hanya tampilkan slot yang aktif di template (default: semua)
            $activeTemplate = $doc->template ?? \App\Models\ESignTemplate::where('jenis_surat_slug', $doc->jenis_surat_slug ?? $data['slug'] ?? null)
                ->where('is_active', true)
                ->first();
            $signActive = $activeTemplate ? $activeTemplate->sign_slots : [1, 2, 3];

            $signHas = [
                1 => in_array(1, $signActive) && ($signee1 || $doc->employee1_signee_id),
                2 => in_array(2, $signActive) && ($signee2 || $doc->employee2_signee_id),
                3 => in_array(3, $signActive) && ($signee3 || $doc->employee3_signee_id),
            ];
            $signCount = 0;
            foreach ($signHas as $h) { if ($h) $signCount++; }

            // Posisi otomatis sesuai jumlah sign: 1=kanan, 2=kiri+kanan, 3=kiri+tengah+kanan
            $signCells = ['left' => null, 'center' => null, 'right' => null];
            if ($signCount === 1)     { $signCells['right'] = 1; }
            elseif ($signCount === 2) { $signCells['left'] = 1; $signCells['right'] = 2; }
            else                      { $signCells['left'] = 1; $signCells['center'] = 2; $signCells['right'] = 3; }
        @endphp
        <div class="signature-area">
            <table class="sign-table"><tr>
            @foreach(['left','center','right'] as $cell)
                <td class="sign-cell">
                @if($signCells[$cell])
                    @php
                        $n = $signCells[$cell];
                        $s = ${'signee'.$n};
                    @endphp
                    <div class="signature-box">
                        <div class="sig-label">Sign {{ $n }}</div>
                        <div class="sig-qr">QR Code<br>Digital Signature</div>
                        <div class="sig-name">{{ $s->fullname ?? '—' }}</div>
                        <div class="sig-pos">{{ $s->position->nama ?? '—' }}</div>
                    </div>
                @endif
                </td>
            @endforeach
            </tr></table>
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
