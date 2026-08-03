{{--
-- ============================================================================
-- PARTIAL: header.blade.php
-- Document header: company identity + title + document number.
-- Shared by all letter type templates.
--
-- Variables expected:
--   $doc   – Instance of App\Models\ESign
--   $data  – ['title' => string, 'number' => string, 'slug' => string]
--             Optional: $data['logo'] – base64 data URI for PDF generation
-- ============================================================================
--}}
<div class="company-header">
    @if(!empty($data['logo']))
    <img src="{{ $data['logo'] }}" alt="Kop Surat">
    @else
    <img src="{{ url('') }}/assets/images/KOP-terbaru.png" alt="Kop Surat">
    @endif
</div>

<div class="doc-title">{{ $data['title'] }}</div>
<div class="doc-number">Nomor: {{ $doc->nomor_surat ?? $data['number'] }}</div>
