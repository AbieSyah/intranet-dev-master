{{--
-- ============================================================================
-- TEMPLATE: default.blade.php
-- Default/fallback letter template.
-- Used when no specific template exists for a letter type.
--
-- Variables expected:
--   $doc   – Instance of App\Models\ESign (with employee loaded)
--   $data  – ['title' => string, 'number' => string, 'slug' => string]
-- ============================================================================
--}}
@include('pages.e-sign.partials.header')

<p style="text-align: justify; margin-bottom: 20px;">
    Yang bertanda tangan di bawah ini:
</p>

<table style="width: 100%; margin-bottom: 20px;">
    <tr>
        <td class="field-label" style="vertical-align: top; width: 140px;">PT Hisamitsu Pharma Indonesia</td>
        <td class="field-value">bertindak sebagai <strong>Pemberi Kerja</strong></td>
    </tr>
</table>

<p style="text-align: justify; margin-bottom: 12px;">dengan</p>

<table style="width: 100%; margin-bottom: 24px;">
    <tr>
        <td class="field-label" style="vertical-align: top; width: 140px;">Nama</td>
        <td class="field-value">{{ $doc->employee->fullname ?? '—' }}</td>
    </tr>
    <tr>
        <td class="field-label">NIK</td>
        <td class="field-value">{{ $doc->employee->nik ?? '—' }}</td>
    </tr>
    <tr>
        <td class="field-label">Departemen</td>
        <td class="field-value">{{ $doc->employee->department->name ?? '—' }}</td>
    </tr>
    <tr>
        <td class="field-label">Jabatan</td>
        <td class="field-value">{{ $doc->employee->position->nama ?? '—' }}</td>
    </tr>
    @if($doc->tanggal_mulai)
    <tr>
        <td class="field-label">Tanggal Berlaku</td>
        <td class="field-value">{{ $doc->tanggal_mulai_formatted }}</td>
    </tr>
    @endif
    @if($doc->tanggal_akhir)
    <tr>
        <td class="field-label">Tanggal Berakhir</td>
        <td class="field-value">{{ $doc->tanggal_akhir_formatted }}</td>
    </tr>
    @endif
</table>

@if($doc->description)
<p style="text-align: justify; margin-bottom: 16px;">
    {{ $doc->description }}
</p>
@else
<p style="text-align: justify; margin-bottom: 16px;">
    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
</p>
@endif

@include('pages.e-sign.partials.signature')
