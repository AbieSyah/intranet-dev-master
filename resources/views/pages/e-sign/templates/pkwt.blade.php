{{--
-- ============================================================================
-- TEMPLATE: pkwt.blade.php
-- PKWT (Perjanjian Kerja Waktu Tertentu) letter template.
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
        <td class="field-label">Tanggal Mulai</td>
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

<p style="text-align: justify; margin-bottom: 16px;">
    Dengan ini menyatakan bahwa karyawan tersebut di atas telah diangkat sebagai karyawan dengan
    status <strong>Kontrak Waktu Tertentu (PKWT)</strong> untuk jangka waktu sebagaimana tersebut di atas,
    terhitung sejak tanggal <strong>{{ $doc->tanggal_mulai_formatted }}</strong> sampai dengan tanggal
    <strong>{{ $doc->tanggal_akhir_formatted ?? '—' }}</strong>.
</p>

<p style="text-align: justify; margin-bottom: 16px;">
    Selama masa PKWT, karyawan wajib mematuhi seluruh peraturan dan tata tertib yang berlaku
    di PT Hisamitsu Pharma Indonesia. Setelah masa PKWT berakhir, Perjanjian Kerja Waktu Tertentu ini
    dapat diperpanjang atau tidak diperpanjang sesuai dengan ketentuan yang berlaku.
</p>

@if($doc->description)
<p style="text-align: justify; margin-bottom: 16px;">
    Keterangan: {{ $doc->description }}
</p>
@endif

@include('pages.e-sign.partials.signature')
