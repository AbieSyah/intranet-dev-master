@extends('layouts.master')
@section('link')
<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- Required meta tags -->
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css"/>
<!-- Datatables-->
<link href="/assets/libs/Datatables/DataTables-1.13.1/css/dataTables.bootstrap5.css" rel="stylesheet" type="text/css" />
<link href="/assets/libs/Datatables/Buttons-2.3.3/css/buttons.dataTables.css" rel="stylesheet" type="text/css" />
<link href="/assets/libs/Datatables/Responsive-2.4.0/css/responsive.bootstrap.css" rel="stylesheet" type="text/css" />
<!-- Select2-->
<link rel="stylesheet" href="/assets/libs/adminlte/select2/css/4.1.0/select2.min.css">
<!-- Toastr Notifications-->
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<style type="text/css">
    .select2-container--default .select2-selection--multiple .select2-selection__choice__display { color: #000 }
    div.scrollmenu {
        overflow: auto;
        width: 300%;
    }
    /* body{
        background: #f7fbf8; 
    }    */
    img {
        /* display: block; */
        max-width: 100%;
    }
    .preview {
        text-align: center;
        overflow: hidden;
        width: 160px; 
        height: 160px;
        margin: 10px;
        border: 1px solid red;
    }
    .section{
        margin-top:150px;
        background:#fff;
        padding:50px 30px;
    }
    .select2, .select2 option {
    font-size:13px;
    }
</style>
@endsection
@section('content')
<div class="container-fluid">
    <div class="profile-foreground position-relative mx-n4 mt-n4">
        <div class="profile-wid-bg">
            <img src="/assets/images/salonpas-bg.jpg" alt="" class="profile-wid-img" />
        </div>
    </div>
    <div class="pt-4 mb-4 mb-lg-3 pb-lg-4">
        <div class="row g-4">
            <div class="col-auto">                       
                <div class="profile-user position-relative d-inline-block mx-auto">
                    @if(!empty($user->employee->avatar))
                    <div id="avatar-user">
                        <img src="{{ asset('storage/avatars/'.$user->employee->avatar) }}" class="show-image rounded-circle avatar-xl img-thumbnail user-profile-image" alt="user-profile-image">
                    </div>
                    @else
                    <div id="avatar-user">
                        <img src="{{ asset('storage/avatars/user.jpg') }}" class="rounded-circle avatar-xl img-thumbnail user-profile-image" alt="user-profile-image">
                    </div>
                    @endif
                    <div class="avatar-xs p-0 rounded-circle profile-photo-edit">
                        <input onchange="avatarValidation(this);" id="profile-img-file-input" type="file" name="image" class="image profile-img-file-input" accept="image/jpg,image/jpeg,image/png,image/JPG,image/JPEG,image/PNG">
                        <label for="profile-img-file-input" class="profile-photo-edit avatar-xs">
                            <span class="avatar-title rounded-circle bg-light text-body">
                                <i class="ri-camera-fill"></i>
                            </span>
                        </label>
                    </div>
                </div>
            </div>
            <!--end col-->
            <div class="col">
                <div class="p-2">
                    <h3 class="text-white mb-1">{{$user->employee->fullname}}</h3>
                    <p class="text-white-75">{{$user->employee->email}}</p>
                    <div class="hstack text-white-50 gap-1">
                      <div class="me-2"><i class="ri-map-pin-user-line me-1 text-white-75 fs-16 align-middle"></i>
                        {{$user->employee->area->name}}
                      </div>
                      <div><i class="ri-building-line me-1 text-white-75 fs-16 align-middle"></i>
                        {{$user->employee->department->name}}
                      </div>
                    </div>
                    <div class="hstack text-white-50 gap-1">
                      <div class="me-2">
                        @if(!empty($user->employee->level->nama))
                            <i class="ri-contacts-book-2-line me-1 text-white-75 fs-16 align-middle"></i>
                            {{$user->employee->level->nama}}
                        @endif
                      </div>
                      <div>
                        @if(!empty($user->employee->position->nama))
                            <i class="ri-contacts-book-line me-1 text-white-75 fs-16 align-middle"></i>
                            {{$user->employee->position->nama}}
                        @endif
                      </div>
                    </div>
                </div>
            </div>
            <!--end col-->
            <div class="col-12 col-lg-auto order-last order-lg-0">
                <div class="row text text-white-50 text-center">
                    <div class="col-lg-6 col-4">
                        <div class="p-2">
                            <!-- <h4 class="text-white mb-1">{{$user->employee->nik}}</h4>
                            <p class="fs-14 mb-0">NIK</p> -->
                        </div>
                    </div>
                </div>
            </div>
            <!--end col-->

        </div>
        <!--end row-->
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div>
                <div class="d-flex">
                    <!-- Nav tabs -->
                    @include('partials.navbar2')
                    {{-- <ul class="nav nav-pills animation-nav profile-nav gap-2 gap-lg-3 gap-md-3 flex-grow-1" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link fs-14 {{ request()->is('profile-home') ? '' : '' }}" href="{{route('profile.home')}}">
                                Home
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link fs-14 {{ request()->is('profile/internal-rule') ? '' : '' }}" href="{{route('profile.internal.rule')}}">
                                Internal Rule
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link fs-14 {{ request()->is('profile/benefit') ? '' : '' }}" href="{{route('profile.benefit')}}">
                                My Benefit
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link fs-14 {{ request()->is('profile/calendar') ? '' : '' }}" href="{{route('profile.calendar')}}">
                                Calendar
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link fs-14 {{ request()->is('profile/medical') ? '' : '' }}" href="{{route('profile.medical')}}">
                                Medical Checkup
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link fs-14 {{ request()->is('profile/pkb') ? '' : '' }}" href="{{route('profile.pkb')}}">
                               PKB
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link fs-14 {{ request()->is('profile/booking') ? '' : '' }}" href="{{route('profile.booking')}}">
                                Booking Room
                            </a>
                        </li>
                        @can('hrd.menu.profile')
                        <li class="nav-item">
                            <a class="nav-link fs-14 {{ request()->is('profile/training') ? '' : '' }}" href="{{route('profile.training')}}">
                               Training
                            </a>
                        </li>
                        @endcan
                    </ul> --}}
                </div>
                <!-- Navbar -->
                <div class="row pt-4">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">                                        
                                        <div class="row mb-3">
                                            <div class="col-lg-6">
                                            <h4 class="text-primary">Detail Formulir Kebutuhan Training (FKT)</h4>
                                            </div>
                                            <div class="col-lg-6">
                                                @if($fkt->id_penilai == $user->employee_id && $fkt->date_peserta != null)
                                                <a href="{{ route('profile.back.fkt.approve.pti') }}" class="btn btn-primary btn-label waves-effect waves-light float-end"><i class="ri-arrow-left-circle-line label-icon align-middle fs-16 me-2"></i> Back</a>
                                                @else
                                                <a href="{{ route('profile.back.fkt.pti') }}" class="btn btn-primary btn-label waves-effect waves-light float-end"><i class="ri-arrow-left-circle-line label-icon align-middle fs-16 me-2"></i> Back</a>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-lg-7">
                                                <div class="row">
                                                    <div class="col-lg-4">
                                                        <label for="topik" class="form-label col-form-label col-form-label-sm">No Form</label>
                                                    </div>
                                                    <div class="col-lg-8">
                                                        <table class="table table-sm table-nowrap fs-12">
                                                            <tbody>
                                                                <tr>
                                                                    <td>{{$fkt->kode ?? '-'}}</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-4">
                                                        <label for="jenis" class="form-label col-form-label col-form-label-sm">Nama Pemohon</label>
                                                    </div>
                                                    <div class="col-lg-8">
                                                        <table class="table table-sm table-nowrap fs-12">
                                                            <tbody>
                                                                <tr>
                                                                    <td>{{$fkt->pemohon->fullname ?? '-'}}</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-4">
                                                        <label for="jenis" class="form-label col-form-label col-form-label-sm">Departemen</label>
                                                    </div>
                                                    <div class="col-lg-8">
                                                        <table class="table table-sm table-nowrap fs-12">
                                                            <tbody>
                                                                <tr>
                                                                    <td>{{$fkt->pemohon->department->name ?? '-'}}</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-4">
                                                        <label for="jenis" class="form-label col-form-label col-form-label-sm">Tahun Usulan Program</label>
                                                    </div>
                                                    <div class="col-lg-8">
                                                        <table class="table table-sm table-nowrap fs-12">
                                                            <tbody>
                                                                <tr>
                                                                    <td>{{$fkt->tahun_usulan}}</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-4">
                                                        <label for="jenis" class="form-label col-form-label col-form-label-sm">Tahun Rencana Pelaksanaan</label>
                                                    </div>
                                                    <div class="col-lg-8">
                                                        <table class="table table-sm table-nowrap fs-12">
                                                            <tbody>
                                                                <tr>
                                                                    <td>{{$fkt->tahun_pelaksanaan}}</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-4">
                                                        <label for="jenis" class="form-label col-form-label col-form-label-sm">Tujuan Usulan Program</label>
                                                    </div>
                                                    <div class="col-lg-8">
                                                        <table class="table table-sm table-nowrap fs-12">
                                                            <tbody>
                                                                <tr>
                                                                    <td>Program Training Tahunan (PTI)</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-5"></div>
                                        </div>
                                        <!-- Modal Collective -->
                                        @if($fkt->id_pemohon == $user->employee_id && $fkt->date_peserta == null)
                                        <button type="button" class="btn btn-primary " data-bs-toggle="modal" data-bs-target="#collectiveModal">Collective Training</button>
                                        <br>
                                        <br>
                                        @endif
                                        @if($fkt->id_penilai == $user->employee_id && $fkt->date_peserta != null)
                                        <button type="button" class="btn btn-primary " data-bs-toggle="modal" data-bs-target="#collectiveApproveModal">Collective Training</button>
                                        <br>
                                        <br>
                                        @endif
                                        <div id="collectiveModal" class="modal fade" tabindex="-1" aria-labelledby="collectiveModalLabel" aria-hidden="true" style="display: none;">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="collectiveModalLabel">Collective Training</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                                                    </div>
                                                    <form action="{{ route('profile.training.collective.pti', encrypt($fkt->kode)) }}" method="GET">
                                                        @csrf
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <input type="hidden" name="kode_pelatihan" id="kode_pelatihan" value="{{$fkt->kode}}">
                                                                <div class="form-group">
                                                                    <label for="pelatihan" class="col-form-label">Pilih Pelatihan</label>
                                                                    <select class="fs-12 form-control form-control-sm select2 @error('pelatihan') is-invalid @enderror" name="pelatihan" id="pelatihan" data-placeholder="Pilih Pelatihan" required>
                                                                        <option selected="true" disabled="true"></option>
                                                                        @foreach($query_fkt->unique('judul') as $pelatihan)
                                                                            <option value="{{ $pelatihan->judul }}">{{ $pelatihan->judul }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="submit" class="btn btn-primary ">Generate</button>
                                                        </div>
                                                    </form>

                                                </div><!-- /.modal-content -->
                                            </div><!-- /.modal-dialog -->
                                        </div><!-- /.modal -->
                                        <div id="collectiveApproveModal" class="modal fade" tabindex="-1" aria-labelledby="collectiveApproveModalLabel" aria-hidden="true" style="display: none;">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="collectiveApproveModalLabel">Collective Training</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                                                    </div>
                                                    <form action="{{ route('profile.training.collective.approve.pti', encrypt($fkt->kode)) }}" method="GET">
                                                        @csrf
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <input type="hidden" name="kode_judul" id="kode_judul" value="{{$fkt->kode_judul}}">
                                                                <div class="form-group">
                                                                    <label for="pelatihan2" class="col-form-label">Pilih Pelatihan</label>
                                                                    <select class="fs-12 form-control form-control-sm select2 @error('pelatihan2') is-invalid @enderror" name="pelatihan2" id="pelatihan2" data-placeholder="Pilih Pelatihan" required>
                                                                        <option selected="true" disabled="true"></option>
                                                                        @foreach($query_fkt->unique('judul') as $pelatihan)
                                                                            <option value="{{ $pelatihan->judul }}">{{ $pelatihan->judul }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="submit" class="btn btn-primary ">Generate</button>
                                                        </div>
                                                    </form>
                        
                                                </div><!-- /.modal-content -->
                                            </div><!-- /.modal-dialog -->
                                        </div><!-- /.modal --> 
                                        <table class="table table-striped bordered display nowrap" id="table_fkt" style="width:100%;">
                                            <thead>
                                                <tr>
                                                <!-- <th scope="col" style="text-align:center">No</th> -->
                                                <th scope="col" style="text-align:center">Action</th>
                                                <th scope="col" style="text-align:center">Status</th>
                                                <th scope="col" style="text-align:center">Nama</th>
                                                <th scope="col" style="text-align:center">NIK</th>
                                                <th scope="col" style="text-align:center">Jabatan</th>
                                                <th scope="col" style="text-align:center">Nama Pelatihan</th>
                                                <th scope="col" style="text-align:center">Sifat Pelatihan</th>
                                                <th scope="col" style="text-align:center">Alasan</th>
                                                <th scope="col" style="text-align:center">Bulan Pelaksanaan</th>
                                                <th scope="col" style="text-align:center">Provider</th>
                                                <th scope="col" style="text-align:center">Biaya</th>
                                                <th scope="col" style="text-align:center">Akomodasi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($query_fkt as $qry_fkt)
                                                    @php
                                                    $fpkt = \App\Models\Trainingfpkt::where('id_fkt', $qry_fkt->id)->get();
                                                    $status = $fpkt->unique('status')->pluck('status');
                                                    @endphp
                                                    @if($qry_fkt->id_pemohon == $user->employee_id)
                                                        <tr>
                                                            <!-- <td>{{$loop->iteration}}</td> -->
                                                            @if($fpkt->isNotEmpty())
                                                                @if($status[0] == 12)
                                                                <td>
                                                                    <div class="dropdown d-inline-block">
                                                                        <button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                                            <i class="ri-more-fill align-middle"></i>
                                                                        </button>
                                                                        <ul class="dropdown-menu dropdown-menu-end">
                                                                            <li><a href="{{ route('profile.training.fpkt.pti.print', encrypt($qry_fkt->id)) }}" class="dropdown-item" target="_blank"><i class="ri-file-pdf-line align-bottom me-2 text-muted"></i> Print FPKT</a></li>
                                                                        </ul>
                                                                    </div>
                                                                </td>
                                                                @else
                                                                <td>
                                                                    <div class="dropdown d-inline-block">
                                                                        <button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                                            <i class="ri-more-fill align-middle"></i>
                                                                        </button>
                                                                        <ul class="dropdown-menu dropdown-menu-end">
                                                                            <li><a href="{{ route('profile.training.fpkt.pti', encrypt($qry_fkt->id)) }}" class="dropdown-item"><i class="ri-eye-2-line align-bottom me-2 text-muted"></i> Detail FPKT</a></li>
                                                                            <li><a href="{{ route('profile.training.fpkt.pti.print', encrypt($qry_fkt->id)) }}" class="dropdown-item" target="_blank"><i class="ri-file-pdf-line align-bottom me-2 text-muted"></i> Print FPKT</a></li>
                                                                        </ul>
                                                                    </div>
                                                                </td>
                                                                @endif
                                                                @if($fpkt->sum('level_atasan') > 0 && $fpkt->sum('level_peserta') > 0)
                                                                    @if($status[0] == 12)
                                                                    <td><a href="#" data-id="{{encrypt($qry_fkt->id)}}" data-bs-toggle="modal" data-bs-target="#statusModal" <span class="badge text-bg-success view-status">Finished</span></a></td>
                                                                    @else
                                                                    <td><a href="#" data-id="{{encrypt($qry_fkt->id)}}" data-bs-toggle="modal" data-bs-target="#statusModal" <span class="badge text-bg-secondary view-status">Approved</span></a></td>
                                                                    @endif
                                                                @else
                                                                    <td><a href="#" data-id="{{encrypt($qry_fkt->id)}}" data-bs-toggle="modal" data-bs-target="#statusModal" <span class="badge text-bg-warning view-status">Waiting Assessment</span></a></td>
                                                                @endif
                                                            @else
                                                                <td>
                                                                    <div class="dropdown d-inline-block">
                                                                        <button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                                            <i class="ri-more-fill align-middle"></i>
                                                                        </button>
                                                                        <ul class="dropdown-menu dropdown-menu-end">
                                                                            <li><a href="{{ route('profile.training.fpkt.pti', encrypt($qry_fkt->id)) }}" class="dropdown-item"><i class="ri-edit-line align-bottom me-2 text-muted"></i> Isi FPKT</a></li>
                                                                            <li><a href="{{ route('profile.training.fpkt.pti.print', encrypt($qry_fkt->id)) }}" class="dropdown-item" target="_blank"><i class="ri-file-pdf-line align-bottom me-2 text-muted"></i> Print FPKT</a></li>
                                                                        </ul>
                                                                    </div>
                                                                </td>
                                                                <td><a href="#" <span class="badge text-bg-primary">Draft</span></a></td>
                                                            @endif
                                                            <td>{{$qry_fkt->peserta->fullname ?? '-'}}</td>
                                                            <td>{{$qry_fkt->peserta->nik ?? '-'}}</td>
                                                            <td>{{$qry_fkt->peserta->position->nama ?? '-'}}</td>
                                                            <td>{{$qry_fkt->judul ?? '-'}}</td>
                                                            <td>{{$qry_fkt->sifat ?? '-'}}</td>
                                                            <td>{{$qry_fkt->alasan ?? '-'}}</td>
                                                            <td>{{$qry_fkt->bulan_pelaksanaan ?? '-'}}</td>
                                                            <td>{{$qry_fkt->provider->nama ?? '-'}}</td>
                                                            <td>{{$qry_fkt->biaya_fkt ?? '-'}}</td>
                                                            <td>
                                                                a) Menginap : {{$qry_fkt->penginapan ?? '-'}} <br>
                                                                b) Transportasi : {{$qry_fkt->transportasi ?? '-'}}
                                                            </td>
                                                        </tr>
                                                    @else
                                                        @if($qry_fkt->id_peserta == $user->employee_id)
                                                            <tr>
                                                                <!-- <td>{{$loop->iteration}}</td> -->
                                                                @if($fpkt->isNotEmpty())
                                                                    @if($fpkt->sum('level_atasan') > 0 && $fpkt->sum('level_peserta') > 0)
                                                                        <td>
                                                                            <div class="dropdown d-inline-block">
                                                                                <button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                                                    <i class="ri-more-fill align-middle"></i>
                                                                                </button>
                                                                                <ul class="dropdown-menu dropdown-menu-end">
                                                                                    <li><a href="{{ route('profile.training.fpkt.pti.print', encrypt($qry_fkt->id)) }}" class="dropdown-item" target="_blank"><i class="ri-file-pdf-line align-bottom me-2 text-muted"></i> Print FPKT</a></li>
                                                                                </ul>
                                                                            </div>
                                                                        </td>
                                                                        @if($status[0] == 12)
                                                                        <td><a href="#" data-id="{{encrypt($qry_fkt->id)}}" data-bs-toggle="modal" data-bs-target="#statusModal" <span class="badge text-bg-success view-status">Finished</span></a></td>
                                                                        @else
                                                                        <td><a href="#" data-id="{{encrypt($qry_fkt->id)}}" data-bs-toggle="modal" data-bs-target="#statusModal" <span class="badge text-bg-secondary view-status">Approved</span></a></td>
                                                                        @endif
                                                                    @else
                                                                        @if($fpkt->sum('level_peserta') > 0)
                                                                            <td>
                                                                                <div class="dropdown d-inline-block">
                                                                                    <button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                                                        <i class="ri-more-fill align-middle"></i>
                                                                                    </button>
                                                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                                                        <li><a href="{{ route('profile.training.fpkt.pti', encrypt($qry_fkt->id)) }}" class="dropdown-item"><i class="ri-edit-line align-bottom me-2 text-muted"></i> Detail FPKT</a></li>
                                                                                        <li><a href="{{ route('profile.training.fpkt.pti.print', encrypt($qry_fkt->id)) }}" class="dropdown-item" target="_blank"><i class="ri-file-pdf-line align-bottom me-2 text-muted"></i> Print FPKT</a></li>
                                                                                    </ul>
                                                                                </div>
                                                                            </td>
                                                                            <td><a href="#" data-id="{{encrypt($qry_fkt->id)}}" data-bs-toggle="modal" data-bs-target="#statusModal" <span class="badge text-bg-warning view-status">Waiting Assessment</span></a></td>
                                                                        @else
                                                                            <td>
                                                                                <div class="dropdown d-inline-block">
                                                                                    <button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                                                        <i class="ri-more-fill align-middle"></i>
                                                                                    </button>
                                                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                                                        <li><a href="{{ route('profile.training.fpkt.pti', encrypt($qry_fkt->id)) }}" class="dropdown-item"><i class="ri-edit-line align-bottom me-2 text-muted"></i> Isi FPKT</a></li>
                                                                                        <li><a href="{{ route('profile.training.fpkt.pti.print', encrypt($qry_fkt->id)) }}" class="dropdown-item" target="_blank"><i class="ri-file-pdf-line align-bottom me-2 text-muted"></i> Print FPKT</a></li>
                                                                                    </ul>
                                                                                </div>
                                                                            </td>
                                                                            <td><a href="#" data-id="{{encrypt($qry_fkt->id)}}" data-bs-toggle="modal" data-bs-target="#statusModal" <span class="badge text-bg-warning view-status">Waiting Assessment</span></a></td>
                                                                        @endif
                                                                    @endif
                                                                @else
                                                                    <td>
                                                                        <div class="dropdown d-inline-block">
                                                                            <button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                                                <i class="ri-more-fill align-middle"></i>
                                                                            </button>
                                                                            <ul class="dropdown-menu dropdown-menu-end">
                                                                                <li><a href="{{ route('profile.training.fpkt.pti.print', encrypt($qry_fkt->id)) }}" class="dropdown-item" target="_blank"><i class="ri-file-pdf-line align-bottom me-2 text-muted"></i> Print FPKT</a></li>
                                                                            </ul>
                                                                        </div>
                                                                    </td>
                                                                    <td><a href="#" <span class="badge text-bg-primary">Draft</span></a></td>
                                                                @endif
                                                                <td>{{$qry_fkt->peserta->fullname ?? '-'}}</td>
                                                                <td>{{$qry_fkt->peserta->nik ?? '-'}}</td>
                                                                <td>{{$qry_fkt->peserta->position->nama ?? '-'}}</td>
                                                                <td>{{$qry_fkt->judul ?? '-'}}</td>
                                                                <td>{{$qry_fkt->sifat ?? '-'}}</td>
                                                                <td>{{$qry_fkt->alasan ?? '-'}}</td>
                                                                <td>{{$qry_fkt->bulan_pelaksanaan ?? '-'}}</td>
                                                                <td>{{$qry_fkt->provider->nama ?? '-'}}</td>
                                                                <td>{{$qry_fkt->biaya_fkt ?? '-'}}</td>
                                                                <td>
                                                                    a) Menginap : {{$qry_fkt->penginapan ?? '-'}} <br>
                                                                    b) Transportasi : {{$qry_fkt->transportasi ?? '-'}}
                                                                </td>
                                                            </tr>
                                                        @else
                                                            @if($qry_fkt->id_penilai == $user->employee_id)
                                                                <tr>
                                                                    <!-- <td>{{$loop->iteration}}</td> -->
                                                                    @if($fpkt->isNotEmpty())
                                                                        @if($fpkt->sum('level_atasan') > 0 && $fpkt->sum('level_peserta') > 0)
                                                                            <td>
                                                                                <div class="dropdown d-inline-block">
                                                                                    <button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                                                        <i class="ri-more-fill align-middle"></i>
                                                                                    </button>
                                                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                                                        <li><a href="{{ route('profile.training.fpkt.pti.print', encrypt($qry_fkt->id)) }}" class="dropdown-item" target="_blank"><i class="ri-file-pdf-line align-bottom me-2 text-muted"></i> Print FPKT</a></li>
                                                                                    </ul>
                                                                                </div>
                                                                            </td>
                                                                            @if($status[0] == 12)
                                                                            <td><a href="#" data-id="{{encrypt($qry_fkt->id)}}" data-bs-toggle="modal" data-bs-target="#statusModal" <span class="badge text-bg-success view-status">Finished</span></a></td>
                                                                            @else
                                                                            <td><a href="#" data-id="{{encrypt($qry_fkt->id)}}" data-bs-toggle="modal" data-bs-target="#statusModal" <span class="badge text-bg-secondary view-status">Approved</span></a></td>
                                                                            @endif
                                                                        @else
                                                                            @if($fpkt->sum('level_atasan') > 0)
                                                                                <td>
                                                                                    <div class="dropdown d-inline-block">
                                                                                        <button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                                                            <i class="ri-more-fill align-middle"></i>
                                                                                        </button>
                                                                                        <ul class="dropdown-menu dropdown-menu-end">
                                                                                            <li><a href="{{ route('profile.training.fpkt.pti', encrypt($qry_fkt->id)) }}" class="dropdown-item"><i class="ri-edit-line align-bottom me-2 text-muted"></i> Detail FPKT</a></li>
                                                                                            <li><a href="{{ route('profile.training.fpkt.pti.print', encrypt($qry_fkt->id)) }}" class="dropdown-item" target="_blank"><i class="ri-file-pdf-line align-bottom me-2 text-muted"></i> Print FPKT</a></li>
                                                                                        </ul>
                                                                                    </div>
                                                                                </td>
                                                                                <td><a href="#" data-id="{{encrypt($qry_fkt->id)}}" data-bs-toggle="modal" data-bs-target="#statusModal" <span class="badge text-bg-warning view-status">Waiting Assessment</span></a></td>
                                                                            @else
                                                                                <td>
                                                                                    <div class="dropdown d-inline-block">
                                                                                        <button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                                                            <i class="ri-more-fill align-middle"></i>
                                                                                        </button>
                                                                                        <ul class="dropdown-menu dropdown-menu-end">
                                                                                            <li><a href="{{ route('profile.training.fpkt.pti', encrypt($qry_fkt->id)) }}" class="dropdown-item"><i class="ri-edit-line align-bottom me-2 text-muted"></i> Isi FPKT</a></li>
                                                                                            <li><a href="{{ route('profile.training.fpkt.pti.print', encrypt($qry_fkt->id)) }}" class="dropdown-item" target="_blank"><i class="ri-file-pdf-line align-bottom me-2 text-muted"></i> Print FPKT</a></li>
                                                                                        </ul>
                                                                                    </div>
                                                                                </td>
                                                                                <td><a href="#" data-id="{{encrypt($qry_fkt->id)}}" data-bs-toggle="modal" data-bs-target="#statusModal" <span class="badge text-bg-warning view-status">Waiting Assessment</span></a></td>
                                                                            @endif
                                                                        @endif
                                                                    @else
                                                                        <td>
                                                                            <div class="dropdown d-inline-block">
                                                                                <button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                                                    <i class="ri-more-fill align-middle"></i>
                                                                                </button>
                                                                                <ul class="dropdown-menu dropdown-menu-end">
                                                                                    <li><a href="{{ route('profile.training.fpkt.pti.print', encrypt($qry_fkt->id)) }}" class="dropdown-item" target="_blank"><i class="ri-file-pdf-line align-bottom me-2 text-muted"></i> Print FPKT</a></li>
                                                                                </ul>
                                                                            </div>
                                                                        </td>
                                                                        <td><a href="#" <span class="badge text-bg-primary">Draft</span></a></td>
                                                                    @endif
                                                                    <td>{{$qry_fkt->peserta->fullname ?? '-'}}</td>
                                                                    <td>{{$qry_fkt->peserta->nik ?? '-'}}</td>
                                                                    <td>{{$qry_fkt->peserta->position->nama ?? '-'}}</td>
                                                                    <td>{{$qry_fkt->judul ?? '-'}}</td>
                                                                    <td>{{$qry_fkt->sifat ?? '-'}}</td>
                                                                    <td>{{$qry_fkt->alasan ?? '-'}}</td>
                                                                    <td>{{$qry_fkt->bulan_pelaksanaan ?? '-'}}</td>
                                                                    @if($fkt->tipe == 'pti')
                                                                    <td>{{$qry_fkt->provider->nama ?? '-'}}</td>
                                                                    <td>{{$qry_fkt->biaya_fkt ?? '-'}}</td>
                                                                    <td>
                                                                        a) Menginap : {{$qry_fkt->penginapan ?? '-'}} <br>
                                                                        b) Transportasi : {{$qry_fkt->transportasi ?? '-'}}
                                                                    </td>
                                                                    @endif
                                                                </tr>
                                                            @endif
                                                        @endif
                                                    @endif
                                                @endforeach
                                            </tbody>
                                        </table>                                       
                                    </div>
                                </div><!-- end card -->
                            </div>
                            <!--end col-->
                        </div>
                        <!--end row-->
                    </div>
                    <!--end col-->
                </div>
                <!--end row-->
            </div>
        </div>
        <!--end col-->
    </div>
    <!--end row-->
</div><!-- container-fluid -->
<!-- Status Modals -->
<div id="statusModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">            
            <!-- Ribbon Shape -->
            <div class="card ribbon-box shadow-none mb-lg-0">
                <div class="card-body">
                    <div id="status_judul"></div>                  
                    <div class="text-end"><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button></div>
                    
                    <div class="ribbon-content text-muted mt-4">
                    <div id="status_training"></div>                    
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- Modal Validation Extension File Upload Gambar -->
<div class="modal fade" id="secondmodal" aria-hidden="true" aria-labelledby="..." tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
          <div class="modal-body text-center p-5">
              <lord-icon
                  src="https://cdn.lordicon.com/tdrtiskw.json"
                  trigger="loop"
                  colors="primary:#f7b84b,secondary:#405189"
                  style="width:130px;height:130px">
              </lord-icon>
              <div class="mt-4 pt-4">
                  <h4>Whoops, ada yang salah!</h4>
                  <p class="text-muted">Maaf hanya menerima file foto yang bertipe .jpg | .jpeg | .png</p>
                  <!-- Toogle to second dialog -->
                  <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
              </div>
          </div>
      </div>
  </div>
</div>
<!-- Modal Upload foto -->
<div class="modal fade" id="modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg  modal-dialog-scrollable" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="myModalLabel">Upload Foto Profile</h5>
        </div>
        <div class="modal-body">                         
            <div data-simplebar style="max-width: 100%;">                
                <div class="img-container">
                    <div class="row">
                        <div class="col-md-8">
                            <img id="image" src="">
                        </div>
                        <div class="col-md-4">
                            <div class="preview"></div>
                        </div>
                    </div>
                </div>                
            </div>                
            <div class="d-flex gap-2 justify-content-end">
                <button type="button" onclick="cancelAvatar()" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="crop">Crop</button>
            </div>            
        </div>
    </div>
  </div>
</div>
<!--modal konfirmasi upload foto -->
<div class="modal fade" id="konfirmasimodal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <form class="form" action="{{ route('profile.upload') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="mt-4 pt-3">
                        <p class="text-muted mb-4">Apakah Anda Yakin Mengubah Foto Profile Anda?</p>
                        <img src="" style="width: 100px;" class="show-image mb-4">
                        <input type="hidden" name="image_base64"> 
                        <div class="hstack gap-2 justify-content-center">
                            <button type="submit" class="btn btn-primary">Ya</button>
                            <button type="button" onclick="cancelAvatar()" class="btn btn-secondary" data-bs-dismiss="modal">Tidak</button>
                            <!-- <button class="btn btn-secondary" data-bs-dismiss="modal">
                                Tidak
                            </button> -->
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<!-- Datatables -->
<script src="/assets/libs/Datatables/DataTables-1.13.1/js/jquery.dataTables.min.js"></script>
<script src="/assets/libs/Datatables/DataTables-1.13.1/js/dataTables.bootstrap5.min.js"></script>
<script src="/assets/libs/Datatables/Responsive-2.4.0/js/dataTables.responsive.min.js"></script>
<script src="/assets/js/pages/datatables.init.js"></script>
<!-- Select2 -->
<script src="/assets/libs/adminlte/select2/js/4.1.0/select2.min.js"></script>
<script src="/assets/js/pages/select2.init.js"></script>
<!-- profile-setting init js -->
<script src="/assets/js/pages/profile-setting.init.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.js"></script>
<!-- Toastr Notifications-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
@endsection
@section('javascript')
<script>
    $(function () {    
        $('.select2').select2();
        $('#pelatihan').select2({dropdownParent: $('#collectiveModal .modal-content')});
        $('#pelatihan2').select2({dropdownParent: $('#collectiveApproveModal .modal-content')});
    });
</script>
<script>
    var jml = {{ Js::from($total_fkt) }};
    if(jml <= 1){
        let table_fkt = new DataTable('#table_fkt', {
            scrollX: true,
            scrollY: '150px'
        });
    }else if(jml == 2){
        let table_fkt = new DataTable('#table_fkt', {
            scrollX: true,
            scrollY: '200px'
        });
    }else{
        let table_fkt = new DataTable('#table_fkt', {
            scrollX: true
        });
    }

    $('#table_fkt').on("click", ".view-status", function() {
        var id = $(this).data("id");
        $.ajax({
            url: "{{ route('profile.status.fkt.pti') }}",
            type: "POST",
            data: {
                id: id,
                _token: '{{ csrf_token() }}'
            },
            dataType: 'json',
            success: function(result) {
                if(result.status_fpkt == 11){
                    $("#status_judul").html('<div class="ribbon ribbon-secondary ribbon-shape text-uppercase">'+ result.nama_status_fpkt +'</div>');
                }else if(result.status_fpkt == 12){
                    $("#status_judul").html('<div class="ribbon ribbon-success ribbon-shape text-uppercase">'+ result.nama_status_fpkt +'</div>');
                }else if(result.status_fpkt == 10){
                    $("#status_judul").html('<div class="ribbon ribbon-warning ribbon-shape text-uppercase">'+ result.nama_status_fpkt +'</div>');
                }else{
                    $("#status_judul").html('<div class="ribbon ribbon-warning ribbon-shape text-uppercase">'+ result.nama_status_fpkt +'</div>');
                }
                $("#status_training").html('<div class="row">'+
                        '<div class="table-responsive">'+
                        '<table class="table table-borderless table-sm table-nowrap">'+
                            '<tbody>'+
                                '<tr>'+
                                    '<td scope="row">1.</td>'+
                                    '<td scope="row">Approval FPKT</td>'+
                                    '<td>:</td>'+
                                    '<td></td>'+
                                '</tr>'+
                                '<tr>'+
                                    '<td scope="row"></td>'+
                                    '<td scope="row">1. Peserta</td>'+
                                    '<td>:</td>'+
                                    '<td>'+result.nama_peserta+'</td>'+
                                '</tr>'+
                                '<tr>'+
                                    '<td scope="row"></td>'+
                                    '<td scope="row"></td>'+
                                    '<td>:</td>'+
                                    '<td>'+result.date_peserta+'</td>'+
                                '</tr>'+
                                '<tr>'+
                                    '<td scope="row"></td>'+
                                    '<td scope="row">2. Atasan</td>'+
                                    '<td>:</td>'+
                                    '<td>'+result.nama_penilai+'</td>'+
                                '</tr>'+
                                '<tr>'+
                                    '<td scope="row"></td>'+
                                    '<td scope="row"></td>'+
                                    '<td>:</td>'+
                                    '<td>'+result.date_penilai+'</td>'+
                                '</tr>'+
                                '<tr>'+
                                    '<td scope="row"></td>'+
                                    '<td scope="row">3. Verifikasi</td>'+
                                    '<td>:</td>'+
                                    '<td>'+result.nama_hrd+'</td>'+
                                '</tr>'+
                                '<tr>'+
                                    '<td scope="row"></td>'+
                                    '<td scope="row"></td>'+
                                    '<td>:</td>'+
                                    '<td>'+result.date_hrd+'</td>'+
                                '</tr>'+                                
                            '</tbody>'+
                        '</table>'+
                        '</div>'+
                    '</div>'+
                '</div>');
            }
        });
    });
</script>
<script>
    var $modal = $('#modal');
    var image = document.getElementById('image');
    var cropper;

    /*------------------------------------------
    --------------------------------------------
    Image Change Event
    --------------------------------------------
    --------------------------------------------*/
    $("body").on("change", ".image", function(e){
        var files = e.target.files;
        var done = function (url) {
            image.src = url;
            $modal.modal('show');
        };

        var reader;
        var file;
        var url;

        if (files && files.length > 0) {
            file = files[0];

            if (URL) {
                done(URL.createObjectURL(file));
            } else if (FileReader) {
                reader = new FileReader();
                reader.onload = function (e) {
                    done(reader.result);
                };
            reader.readAsDataURL(file);
            }
        }
    });

    /*------------------------------------------
    --------------------------------------------
    Show Model Event
    --------------------------------------------
    --------------------------------------------*/
    $modal.on('shown.bs.modal', function () {
        cropper = new Cropper(image, {
            aspectRatio: 1,
            viewMode: 3,
            preview: '.preview'
        });
    }).on('hidden.bs.modal', function () {
        cropper.destroy();
        cropper = null;
    });

    /*------------------------------------------
    --------------------------------------------
    Crop Button Click Event
    --------------------------------------------
    --------------------------------------------*/
    $("#crop").click(function(){
        canvas = cropper.getCroppedCanvas({
            // width: 160,
            // height: 160,
            width: 200,
            height: 200,
        });

        canvas.toBlob(function(blob) {
            url = URL.createObjectURL(blob);
            var reader = new FileReader();
            reader.readAsDataURL(blob);
            reader.onloadend = function() {
                var base64data = reader.result; 
                $("input[name='image_base64']").val(base64data);
                $(".show-image").show();
                $(".show-image").attr("src",base64data);
                $("#modal").modal('toggle');
            }
        });

        $("#konfirmasimodal").modal("show");
    });        
</script>
<script type="text/javascript">
    function cancelAvatar(){
      var avatar = document.getElementById('profile-img-file-input');
      avatar.value = '';
      var pre_avatar = {{ Js::from($user->employee->avatar ?? '') }};
      if(!pre_avatar){
        document.getElementById("avatar-user").innerHTML = '<img src="/assets/images/users/user-dummy-img.jpg" class="rounded-circle avatar-xl img-thumbnail user-profile-image" alt="user-profile-image">';
      }else{
        document.getElementById("avatar-user").innerHTML = '<img src="/storage/avatars/'+pre_avatar+'" class="rounded-circle avatar-xl img-thumbnail user-profile-image" alt="user-profile-image">';
      }      
    }

    function clearAvatar(){
        var pre_avatar = {{ Js::from($user->employee->avatar ?? '') }};
        if(!pre_avatar){
            document.getElementById("avatar-user").innerHTML = '<img src="/assets/images/users/user-dummy-img.jpg" class="rounded-circle avatar-xl img-thumbnail user-profile-image" alt="user-profile-image">';
        }else{
            document.getElementById("avatar-user").innerHTML = '<img src="/storage/avatars/'+pre_avatar+'" class="rounded-circle avatar-xl img-thumbnail user-profile-image" alt="user-profile-image">';
        }
        var file_avatar = document.getElementById('profile-img-file-input');
        file_avatar.value = '';

        var remove_avatar = document.getElementById('remove_file');
        remove_avatar.value = '1';
    }

    function avatarValidation(){
      //foto profile
      var profile = document.getElementById('profile-img-file-input');             
      var pathProfile = profile.value;

      // tipe file yang diizinkan
      var allowedExtensions =
        /(\.jpg|\.jpeg|\.png|\.JPG|\.JPEG|\.PNG)$/i;
        
      //masalah modal
      if (!allowedExtensions.exec(pathProfile)) {
          $('#secondmodal').modal('show');
          // alert('Invalid file type');
          profile.value = '';
          return false;
      }
    }
</script>
<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });
</script>
<script>
    @if(Session::has('status'))
        toastr.options =
        {
            "closeButton" : true,
            "progressBar" : true,
            "positionClass": "toast-bottom-right"
        }
        toastr.success("{{ session('status') }}");
    @endif
    @if(Session::has('error'))
        toastr.options =
        {
            "closeButton" : true,
            "progressBar" : true,
            "positionClass": "toast-bottom-right"
        }
        toastr.error("{{ session('error') }}");
    @endif
</script>
@endsection