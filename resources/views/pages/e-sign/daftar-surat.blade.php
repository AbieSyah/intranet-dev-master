@extends('layouts.master')
@section('title', 'Daftar Surat - E-Sign')
@section('link')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ url('') }}/assets/libs/Datatables/DataTables-1.13.1/css/dataTables.bootstrap5.css" rel="stylesheet" type="text/css" />
    <link href="{{ url('') }}/assets/libs/Datatables/Buttons-2.3.3/css/buttons.dataTables.css" rel="stylesheet" type="text/css" />
    <link href="{{ url('') }}/assets/libs/Datatables/Responsive-2.4.0/css/responsive.bootstrap.css" rel="stylesheet" type="text/css" />
    <style>
        div.dataTables_wrapper { width: 100%; }
        .table-esign th { font-size: 12px; text-transform: uppercase; letter-spacing: 0.3px; }
        .table-esign td { font-size: 13px; vertical-align: middle; }
        .expand-row { display: none; background-color: #f8f9fa !important; }
        .expand-row td { padding: 16px 24px !important; }
        .expand-row.show { display: table-row; }
        .btn-expand { cursor: pointer; transition: transform 0.2s; }
        .btn-expand.expanded { transform: rotate(90deg); }
        .detail-grid { display: grid; grid-template-columns: 140px 1fr; gap: 6px 16px; font-size: 13px; }
        .detail-label { color: #6c757d; font-weight: 500; }
        .detail-value { color: #212529; }
        .signee-list { list-style: none; padding: 0; margin: 0; }
        .signee-list li { padding: 2px 0; }
        .signee-list li::before { content: "•"; color: #0ab39c; font-weight: bold; display: inline-block; width: 14px; }
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
                    <h4 class="mb-sm-0">Daftar Surat</h4>
                    <small class="text-muted">Digital Signature Management System</small>
                </div>
            </div>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">E-Sign Management</a></li>
                    <li class="breadcrumb-item active">Daftar Surat</li>
                </ol>
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

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="ri-error-warning-line me-1"></i> {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if($currentStatus)
<div class="row mb-3">
    <div class="col-12">
        <div class="alert alert-soft-info d-flex align-items-center gap-2 mb-0 py-2 px-3" role="alert">
            <i class="ri-filter-line"></i>
            <span>Menampilkan dokumen dengan status: <strong>{{ $currentStatus }}</strong></span>
            <a href="{{ route('e-sign.daftar-surat') }}" class="btn btn-sm btn-light ms-auto">Reset Filter</a>
        </div>
    </div>
</div>
@endif

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between gap-2 flex-wrap">
                <div>
                    <h5 class="card-title mb-0">Semua Dokumen</h5>
                    <small class="text-muted">Total {{ $documents->total() }} dokumen</small>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <form method="GET" action="{{ route('e-sign.daftar-surat') }}" class="d-flex align-items-center gap-2">
                        <select name="status" class="form-select form-select-sm" style="min-width:150px;">
                            <option value=""> Semua Status </option>
                            @foreach($statusFilterOptions as $val => $label)
                            <option value="{{ $val }}" {{ ($currentStatus ?? '') == $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                            @endforeach
                        </select>
                        <select name="jenis_surat" class="form-select form-select-sm" style="min-width:140px;">
                            <option value=""> Semua Jenis Surat </option>
                            @foreach($letterTypes as $lt)
                            <option value="{{ $lt->slug }}" {{ ($currentJenisSurat ?? '') == $lt->slug ? 'selected' : '' }}>
                                {{ $lt->name }}
                            </option>
                            @endforeach
                        </select>
                        <div class="input-group input-group-sm" style="min-width:200px;">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="ri-search-line text-muted"></i>
                            </span>
                            <input type="text" name="search" class="form-control" placeholder="Cari nomor, NIK, nama..." value="{{ $search ?? '' }}">
                            <button class="btn btn-primary" type="submit">
                                <i class="ri-filter-2-line"></i>
                            </button>
                            @if($search || $currentJenisSurat)
                            <a href="{{ route('e-sign.daftar-surat') }}" class="btn btn-outline-secondary">
                                <i class="ri-close-line"></i>
                            </a>
                            @endif
                        </div>
                    </form>
                    <a href="{{ route('e-sign.create-select') }}" class="btn btn-primary">
                        <i class="ri-add-line me-1"></i> Buat Surat Baru
                    </a>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-striped dt-responsive nowrap w-100 table-esign" id="tableDaftarSurat">
                    <thead>
                        <tr>
                            <th style="width:36px;text-align:center;"><input type="checkbox" id="checkAllDraft" title="Pilih semua draft di halaman ini"></th>
                            <th style="width:40px;"></th>
                            <th>No</th>
                            <th>Nomor Surat</th>
                            <th>Jenis Surat</th>
                            <th>Template</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($documents as $i => $doc)
                        @php
                            $slugMap = [
                                'PKWT' => 'pkwt',
                                'Promosi' => 'promosi',
                                'Mutasi' => 'mutasi',
                                'Demosi' => 'demosi',
                                'Perpanjangan PKWT' => 'perpanjangan-pkwt',
                                'Pengangkatan Karyawan Tetap' => 'pengangkatan',
                                'Surat Peringatan' => 'surat-peringatan',
                            ];
                            $slug = $slugMap[$doc['jenis_surat']] ?? 'pkwt';
$badge = 'secondary';
                                if ($doc['status'] === 'Completed') $badge = 'success';
                                elseif ($doc['status'] === 'Dikonfirmasi') $badge = 'success';
                                elseif ($doc['status'] === 'Menunggu Konfirmasi') $badge = 'warning';
                                elseif (in_array($doc['status'], ['Menunggu Sign 1', 'Menunggu Sign 2', 'Menunggu Sign 3'])) $badge = 'info';
                                elseif ($doc['status'] === 'Draft') $badge = 'warning';
                                elseif ($doc['status'] === 'Rejected') $badge = 'danger';
                            $rowId = 'detail-' . $doc['id'];
                        @endphp
                        <tr class="main-row" data-target="{{ $rowId }}">
                            <td class="text-center">
                                <input type="checkbox" class="draft-check" value="{{ $doc['id'] }}" {{ $doc['status_raw'] === 'draft' ? '' : 'disabled' }}>
                            </td>
                            <td class="text-center">
                                <i class="ri-arrow-right-s-line btn-expand fs-18" data-target="{{ $rowId }}"></i>
                            </td>
                            <td>{{ $i + 1 }}</td>
                            <td><span class="fw-medium">{{ $doc['nomor_surat'] ?? '—' }}</span>
                                @if(!empty($doc['is_batch']))
                                <span class="badge bg-dark ms-1" title="Bagian dari multi-surat (batch #{{ $doc['batch_id'] }})">
                                    <i class="ri-stack-line me-1"></i>{{ $doc['batch_id'] }}-{{ str_pad((string)($doc['nomor_sub'] ?? '-'), 3, '0', STR_PAD_LEFT) }}
                                </span>
                                @endif
                            </td>
                            <td>{{ $doc['jenis_surat'] }}</td>
                            <td>{{ $doc['template_name'] }}</td>
                            <td>{{ $doc['tanggal'] }}</td>
                            <td><span class="badge bg-{{ $badge }}">{{ $doc['status'] }}</span></td>
                            <td>
                                <a href="{{ route('e-sign.preview', $doc['id']) }}" class="btn btn-sm btn-soft-primary">
                                    <i class="ri-eye-line me-1"></i>Preview
                                </a>
                            </td>
                        </tr>
                        <tr class="expand-row" id="{{ $rowId }}">
                            <td colspan="9">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="fw-semibold mb-3"><i class="ri-file-info-line me-1"></i> Informasi Employee</h6>
                                        <div class="detail-grid">
                                            <span class="detail-label">NIK</span>
                                            <span class="detail-value">{{ $doc['nik'] }}</span>
                                            <span class="detail-label">Nama</span>
                                            <span class="detail-value">{{ $doc['nama'] }}</span>
                                            <span class="detail-label">Departemen</span>
                                            <span class="detail-value">{{ $doc['departemen'] }}</span>
                                            <span class="detail-label">Jabatan</span>
                                            <span class="detail-value">{{ $doc['jabatan'] }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="fw-semibold mb-3"><i class="ri-signature-line me-1"></i> Status Tanda Tangan ({{ count($doc['signees']) }} Sign)</h6>
                                        <ul class="signee-list">
                                            @foreach($doc['signees'] as $signee)
                                            <li>
                                                <strong>Sign {{ $signee['level'] }}:</strong> {{ $signee['name'] }}
                                                @if(!empty($signee['signed_at']))
                                                <span class="badge bg-success-subtle text-success ms-1">
                                                    <i class="ri-check-line"></i> Sudah ttd {{ \Carbon\Carbon::parse($signee['signed_at'])->format('d M Y, H:i') }}
                                                </span>
                                                @else
                                                <span class="badge bg-light text-muted ms-1">
                                                    <i class="ri-time-line"></i> Belum
                                                </span>
                                                @endif
                                            </li>
                                            @endforeach
                                        </ul>
                                        <div class="mt-3">
                                            @if($doc['status_raw'] === 'draft')
                                            @if(!empty($doc['is_batch']))
                                            <a href="{{ route('e-sign.batch.edit', $doc['batch_id']) }}" class="btn btn-sm btn-warning">
                                                <i class="ri-pencil-line me-1"></i> Edit Batch
                                            </a>
                                            <form method="POST" action="{{ route('e-sign.batch.send', $doc['batch_id']) }}" class="d-inline" onsubmit="return confirm('Kirim multi-surat ini ke semua penerima?');">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-info">
                                                    <i class="ri-send-plane-line me-1"></i> Kirim Batch
                                                </button>
                                            </form>
                                            @else
                                            <a href="{{ route('e-sign.edit', $doc['id']) }}" class="btn btn-sm btn-warning">
                                                <i class="ri-pencil-line me-1"></i> Edit Surat
                                            </a>
                                            @endif
                                            @endif
                                            <a href="{{ route('e-sign.pdf', $doc['id']) }}" class="btn btn-sm btn-success">
                                                <i class="ri-download-2-line me-1"></i> Download PDF
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-3">
                    {{ $documents->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<form method="POST" action="{{ route('e-sign.send-bulk') }}" id="formSendBulk">
    @csrf
    <div class="bulk-bar" id="bulkBar" style="display:none;position:fixed;left:0;right:0;bottom:0;z-index:1050;background:#ffffff;box-shadow:0 -4px 16px rgba(0,0,0,.12);border-top:2px solid #0ab39c;padding:10px 24px;">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="text-muted">
                <i class="ri-checkbox-multiple-line me-1 text-primary"></i>
                <strong id="bulkCount">0</strong> surat draft terpilih
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-sm btn-light" id="btnClearSelection">
                    <i class="ri-close-line me-1"></i>Batal
                </button>
                <button type="submit" class="btn btn-sm btn-info" onclick="return confirm('Kirim surat draft terpilih ke employee masing-masing?');">
                    <i class="ri-send-plane-line me-1"></i> Kirim ke Employee
                </button>
            </div>
        </div>
    </div>
</form>
@endsection

@section('javascript')
<script>
    $(document).ready(function() {
        // Expand/collapse on arrow click
        $(document).on('click', '.btn-expand', function(e) {
            e.stopPropagation();
            var targetId = $(this).data('target');
            var $target = $('#' + targetId);
            var $icon = $(this);

            $target.toggleClass('show');
            $icon.toggleClass('expanded');
        });

        // Expand/collapse on row click (except action column)
        $(document).on('click', '.main-row td', function(e) {
            if ($(e.target).closest('a, button, .btn-expand, input').length) return;
            var $icon = $(this).closest('tr').find('.btn-expand');
            $icon.trigger('click');
        });

        // Bulk send draft selection
        function updateBulkSelection() {
            var checked = $('.draft-check:checked');
            $('#bulkCount').text(checked.length);
            $('#formSendBulk input[name="ids[]"]').remove();
            checked.each(function() {
                $('<input>').attr({
                    type: 'hidden',
                    name: 'ids[]',
                    value: $(this).val()
                }).appendTo('#formSendBulk');
            });
            $('#bulkBar').toggle(checked.length > 0);
        }

        $('#checkAllDraft').on('change', function() {
            $('.draft-check:not(:disabled)').prop('checked', this.checked);
            updateBulkSelection();
        });

        $(document).on('change', '.draft-check', function() {
            var all = $('.draft-check:not(:disabled)').length > 0 &&
                      $('.draft-check:checked').length === $('.draft-check:not(:disabled)').length;
            $('#checkAllDraft').prop('checked', all);
            updateBulkSelection();
        });

        $('#btnClearSelection').on('click', function() {
            $('.draft-check').prop('checked', false);
            $('#checkAllDraft').prop('checked', false);
            updateBulkSelection();
        });
    });
</script>
@endsection