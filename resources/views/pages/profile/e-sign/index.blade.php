@extends(Auth::user()->can('emp.menu') ? 'layouts.general' : 'layouts.master')
@section('title', 'E-Sign')
@section('link')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .table-esign th { font-size: 12px; text-transform: uppercase; letter-spacing: 0.3px; }
        .table-esign td { font-size: 13px; vertical-align: middle; }
        .pagination { margin-bottom: 0; justify-content: center; }
        .page-link { font-size: 13px; }
    </style>
@endsection

@section('content')
<div class="container-fluid">
    @if (!Auth::user()->can('emp.menu'))
    <div class="profile-foreground position-relative mx-n4 mt-n4">
        <div class="profile-wid-bg">
            <img src="/assets/images/salonpas-bg.jpg" alt="" class="profile-wid-img" />
        </div>
    </div>
    <div class="pt-3 mb-3">
        <div class="row">
            <div class="col-12">
                <div class="d-flex">
                    @include('partials.navbar2')
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">E-Sign Saya</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('profile.home') }}">My Profile</a></li>
                        <li class="breadcrumb-item active">E-Sign</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link {{ $tab === 'sign' ? 'active' : '' }}"
                            href="{{ route('e-sign.profile-index', ['tab' => 'sign']) }}">
                            <i class="ri-pen-nib-line me-1"></i> Sign
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $tab === 'done' ? 'active' : '' }}"
                            href="{{ route('e-sign.profile-index', ['tab' => 'done']) }}">
                            <i class="ri-check-double-line me-1"></i> Done
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                @if($documents->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover table-nowrap mb-0 table-esign">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Nomor Surat</th>
                                <th>Jenis Surat</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th>action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($documents as $i => $doc)
                            @php
                                $badge = 'secondary';
                                if (in_array($doc->status, ['sign_1', 'sign_2', 'sign_3'])) $badge = 'info';
                                elseif ($doc->status === 'completed') $badge = 'success';
                                elseif ($doc->status === 'rejected_employee') $badge = 'danger';
                            @endphp
                            <tr>
                                <td>{{ $documents->firstItem() + $i }}</td>
                                <td><span class="fw-medium">{{ $doc->nomor_surat }}</span></td>
                                <td>{{ $doc->jenis_surat_label }}</td>
                                <td>{{ $doc->tanggal_mulai_formatted }}</td>
                                <td>
                                    <span class="badge bg-{{ $badge }}">{{ $doc->status_label }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('e-sign.preview', $doc->id) }}" class="btn btn-sm btn-soft-primary">
                                        <i class="ri-eye-line me-1"></i>Preview
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $documents->links() }}
                </div>
                @else
                <div class="text-center py-5">
                    <i class="ri-inbox-2-line fs-1 text-muted"></i>
                    <p class="text-muted mt-2 mb-0">Belum ada surat untuk Anda.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
</div>
@endsection
