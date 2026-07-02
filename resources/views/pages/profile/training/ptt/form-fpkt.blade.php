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
<!-- App Css-->
<link href="/assets/css/app.min.css" rel="stylesheet" type="text/css" />
<!-- custom Css-->
<link href="/assets/css/custom.min.css" rel="stylesheet" type="text/css" />
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
                                        <form id="Form-fpkt" action="{{ route('profile.training.fpkt.ptt.store') }}" method="post">
                                            @csrf
                                            @method('PUT')
                                            <div class="row mb-3">
                                                <!-- Info Validation -->
                                                <div class="alert alert-secondary alert-dismissible alert-label-icon rounded-label fade show" role="alert">
                                                    <i class="ri-error-warning-line label-icon"></i><strong>Kotak input yang diberi tanda <span class="text-danger">*</span> wajib diisi.</strong>
                                                </div>
                                            </div>                                      
                                            <div class="row mb-3">
                                                <div class="col-lg-6">
                                                <h4 class="text-primary">Formulir Penilaian Kebutuhan Training (FPKT)</h4>
                                                </div>
                                                <div class="col-lg-6">
                                                    <a href="{{ url()->previous() }}" class="btn btn-primary btn-label waves-effect waves-light float-end"><i class="ri-arrow-left-circle-line label-icon align-middle fs-16 me-2"></i> Back</a>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-lg-10">
                                                    <div class="row">
                                                        <input type="hidden" name="id_fkt" value="{{$fkt->id}}">
                                                        <div class="col-lg-5">
                                                            <label for="topik" class="form-label col-form-label col-form-label-sm">No Form</label>
                                                        </div>
                                                        <div class="col-lg-7">
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
                                                        <div class="col-lg-5">
                                                            <label for="jenis" class="form-label col-form-label col-form-label-sm">Usulan Topik Training</label>
                                                        </div>
                                                        <div class="col-lg-7">
                                                            <table class="table table-sm table-nowrap fs-12">
                                                                <tbody>
                                                                    <tr>
                                                                        <td>{{$fkt->judul ?? '-'}}</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-lg-5">
                                                            <label for="jenis" class="form-label col-form-label col-form-label-sm">Rekomendasi Jenis Training</label>
                                                        </div>
                                                        <div class="col-lg-7">
                                                            <table class="table table-sm table-nowrap fs-12">
                                                                <tbody>
                                                                    <tr>
                                                                        <td>{{$fkt->jenis_pelatihan ?? '-'}}</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-lg-5">
                                                            <label for="jenis" class="form-label col-form-label col-form-label-sm">Rekomendasi Vendor Training</label>
                                                        </div>
                                                        <div class="col-lg-7">
                                                            <table class="table table-sm table-nowrap fs-12">
                                                                <tbody>
                                                                    <tr>
                                                                        <td>{{$fkt->provider->nama ?? '-'}}</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <br>
                                                    <div class="row">
                                                        <div class="col-lg-5">
                                                            <label for="jenis" class="form-label col-form-label col-form-label-sm">Nama Peserta Training</label>
                                                        </div>
                                                        <div class="col-lg-7">
                                                            <table class="table table-sm table-nowrap fs-12">
                                                                <tbody>
                                                                    <tr>
                                                                        <td>{{$fkt->peserta->fullname ?? '-'}}</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-lg-5">
                                                            <label for="jenis" class="form-label col-form-label col-form-label-sm">Nomor Induk Karyawan (NIK)</label>
                                                        </div>
                                                        <div class="col-lg-7">
                                                            <table class="table table-sm table-nowrap fs-12">
                                                                <tbody>
                                                                    <tr>
                                                                        <td>{{$fkt->peserta->nik ?? '-'}}</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-lg-5">
                                                            <label for="jenis" class="form-label col-form-label col-form-label-sm">Departemen / Bagian</label>
                                                        </div>
                                                        <div class="col-lg-7">
                                                            <table class="table table-sm table-nowrap fs-12">
                                                                <tbody>
                                                                    <tr>
                                                                        <td>{{$fkt->peserta->department->name ?? '-'}}</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-lg-5">
                                                            <label for="jenis" class="form-label col-form-label col-form-label-sm">Jabatan</label>
                                                        </div>
                                                        <div class="col-lg-7">
                                                            <table class="table table-sm table-nowrap fs-12">
                                                                <tbody>
                                                                    <tr>
                                                                        <td>{{$fkt->peserta->position->nama ?? '-'}}</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-lg-5">
                                                            <label for="jenis" class="form-label col-form-label col-form-label-sm">Nama Atasan Langsung<span class="text-danger">*</span></label>
                                                        </div>
                                                        <div class="col-lg-7">
                                                            <table class="table table-sm table-nowrap fs-12">
                                                                <tbody>
                                                                    <tr>
                                                                        <td>
                                                                            <div class="form-group">
                                                                                <select class="form-control select2" name="id_atasan" id="id_atasan" data-placeholder="Pilih Atasan">
                                                                                    <option selected="true" value=""></option>
                                                                                    @foreach($employees as $emp)
                                                                                        @if(!empty($emp->level->nama))
                                                                                            <option value="{{ $emp->id }}">{{ $emp->fullname }} -- {{$emp->department->name}} -- {{$emp->level->nama}}</option>
                                                                                        @else
                                                                                            <option value="{{ $emp->id }}">{{ $emp->fullname }} -- {{$emp->department->name}}</option>
                                                                                        @endif
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-lg-5">
                                                            <label for="biaya_fpkt" class="form-label col-form-label col-form-label-sm">Biaya Pelatihan<span class="text-danger">*</span></label>
                                                        </div>
                                                        <div class="col-lg-7">
                                                            <table class="table table-sm table-nowrap fs-12">
                                                                <tbody>
                                                                    <tr>
                                                                        <td>
                                                                            <div class="input-group">
                                                                                <span class="input-group-text">Rp</span><input type="text" class="form-control nominal" id="biaya_fpkt" name="biaya_fpkt" placeholder="Masukkan Biaya" value="" required>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-lg-5">
                                                            <label for="id_vendor" class="form-label col-form-label col-form-label-sm">Vendor Pelatihan<span class="text-danger">*</span></label>
                                                        </div>
                                                        <div class="col-lg-7">
                                                            <table class="table table-sm table-nowrap fs-12">
                                                                <tbody>
                                                                    <tr>
                                                                        <td>
                                                                            <div class="form-group">
                                                                                <select class="form-control  select2" name="id_vendor" id="id_vendor" data-placeholder="Pilih Vendor">
                                                                                    <option selected="true" value=""></option>
                                                                                    @foreach($vendors as $vendor)
                                                                                        <option value="{{ $vendor->id }}">{{ $vendor->nama }}</option>'+
                                                                                    @endforeach
                                                                                        <option value="other">Other</option>
                                                                                </select>
                                                                            </div>
                                                                            <div id="cek_provider">
                                                                                <div class="form-group mt-3">
                                                                                    <input type="text" class="form-control" id="vendor_other" name="vendor_other" placeholder="Masukkan Vendor" value="">
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-lg-5">
                                                            <label for="date_pelaksanaan" class="form-label col-form-label col-form-label-sm">Tanggal Pelaksanaan<span class="text-danger">*</span></label>
                                                        </div>
                                                        <div class="col-lg-7">
                                                            <table class="table table-sm table-nowrap fs-12">
                                                                <tbody>
                                                                    <tr>
                                                                        <td>
                                                                            <div class="input-group">
                                                                                <input type="text" name="date_pelaksanaan" id="date_pelaksanaan"
                                                                                    class="form-control @error('date_pelaksanaan') is-invalid @enderror"
                                                                                    placeholder="Pilih Tanggal" data-provider="flatpickr" value="" required>
                                                                                <span class="input-group-text"><i class="ri-calendar-event-line"></i></span>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-2"></div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-10">
                                                    <!-- Tables Border Colors -->
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered border-secondary fs-12 table-nowrap">
                                                            <thead>
                                                                <tr>
                                                                    <th scope="col">
                                                                        @if($fkt->id_pemohon == $user->employee_id)
                                                                        Latar Belakang Usulan Training<span class="text-danger">*</span> : <br>
                                                                        @else
                                                                        Latar Belakang Usulan Training : <br>
                                                                        @endif
                                                                        <p class="text-muted"><i>(Penjelasan mengenai keterkaitan antara usulan topik training dengan pekerjaan saat ini).</i></p>
                                                                    </th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <th scope="row">
                                                                        @if($fkt->id_pemohon == $user->employee_id)
                                                                        <div>
                                                                            <textarea class="form-control" id="latar_belakang" name="latar_belakang" rows="3" required>{{ old('latar_belakang', $fpkt->latar_belakang ?? '') }}</textarea>
                                                                        </div>
                                                                        @else
                                                                        <div>
                                                                            <textarea class="form-control" id="latar_belakang" name="latar_belakang" rows="3" style="Background-color: #eff2f7;" readonly>{{ old('latar_belakang', $fpkt->latar_belakang ?? '') }}</textarea>
                                                                        </div>
                                                                        @endif
                                                                    </th>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="card-body p-4 border-top border-top-dashed">
                                                    <div data-simplebar data-simplebar-auto-hide="false" style="max-width: 100%;">
                                                        <table class="table table-borderless fs-12" style="table-layout: fixed; width: 100%;">
                                                            <thead class="align-middle">
                                                                <tr class="table-active">
                                                                    <!-- <th scope="col" style="width: 2%;">#</th> -->
                                                                    <th scope="col" style="width: 13%; text-align: center;">
                                                                        Tujuan Training <i class="ri-information-line" data-bs-toggle="tooltip" data-bs-html="true" title="Tuliskan Tujuan yang ingin dicapai setelah mengikuti training"></i>
                                                                    </th>
                                                                    <th scope="col" style="width: 13%; text-align: center;">
                                                                        Kompetensi yang Diharapkan <i class="ri-information-line" data-bs-toggle="tooltip" data-bs-html="true" title="Tuliskan Kompetensi apa saja yang dapat menunjang dalam mencapai tujuan training ini"></i>
                                                                    </th>
                                                                    <th scope="col" style="width: 13%; text-align: center;">
                                                                        Skill / Knowledge <i class="ri-information-line" data-bs-toggle="tooltip" data-bs-html="true" title="Sebutkan minimal 3 komponen Skill / Knowledge yang saat ini dimiliki oleh karyawan dan diperlukan untuk merepresentasikan kompetensi yang diharapkan"></i>
                                                                    </th>
                                                                    <th scope="col" style="width: 8%; text-align: center;">Level Skill diisi oleh peserta (Skala 1-5) <i class="ri-information-line" data-bs-toggle="tooltip" data-bs-html="true" title="Tingkat Skill / Knowledge menurut penilaian diri sendiri"></i></th>
                                                                    <th scope="col" style="width: 8%; text-align: center;">Level Skill diisi oleh atasan langsung (Skala 1-5) <i class="ri-information-line" data-bs-toggle="tooltip" data-bs-html="true" title="Tingkat Skill / Knowledge menurut penilaian atasan langsung"></i></th>
                                                                    <th scope="col" id="h-provider" style="width: 8%; text-align: center;">Rata - rata Level Skill <i class="ri-information-line" data-bs-toggle="tooltip" data-bs-html="true" title="Rata - rata tingkat Skill / Knowledge menurut penilaian diri sendiri dan atasan langsung"></i></th>
                                                                    <th scope="col" id="h-biaya" style="width: 10%; text-align: center;">Kebutuhan Training</th>
                                                                    <th scope="col" style="width: 5%; text-align: center;"></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="newlink">
                                                                @if($arr_fpkt->isNotEmpty())
                                                                    @php($i = 0)
                                                                    @foreach($arr_fpkt as $data_fpkt)
                                                                    <tr id="1" class="produk">
                                                                        <th scope="row" class="produk-id" hidden>{{$loop->iteration}}</th>
                                                                        <td class="text-start">
                                                                            <input type="hidden" id="nomor" name="no_urut[]" value="{{$loop->iteration}}">
                                                                            @if(!empty($data_fpkt->tujuan))
                                                                            <div class="mb-2">
                                                                                <textarea rows="2" class="form-control" id="tujuan-{{$loop->iteration}}" name="tujuan-{{$loop->iteration}}[]" style="Background-color: #eff2f7;" readonly>{{$data_fpkt->tujuan}}</textarea>
                                                                            </div>
                                                                            @else
                                                                            <div class="mb-2" hidden>
                                                                                <textarea rows="2" class="form-control" id="tujuan-{{$loop->iteration}}" name="tujuan-{{$loop->iteration}}[]" style="Background-color: #eff2f7;" readonly>{{$data_fpkt->tujuan}}</textarea>
                                                                            </div>
                                                                            @endif
                                                                        </td>
                                                                        <td>
                                                                            @if(!empty($data_fpkt->kompetensi))
                                                                            <div class="input-group mb-2">
                                                                                <textarea rows="2" class="form-control" id="kompetensi-{{$loop->iteration}}" name="kompetensi-{{$loop->iteration}}[]" style="Background-color: #eff2f7;" readonly>{{$data_fpkt->kompetensi}}</textarea>
                                                                            </div>
                                                                            @else
                                                                            <div class="input-group mb-2" hidden>
                                                                                <textarea rows="2" class="form-control" id="kompetensi-{{$loop->iteration}}" name="kompetensi-{{$loop->iteration}}[]" style="Background-color: #eff2f7;" readonly>{{$data_fpkt->kompetensi}}</textarea>
                                                                            </div>
                                                                            @endif
                                                                        </td>             
                                                                        <td>
                                                                            <div class="input-group mb-2">
                                                                                <textarea rows="2" class="form-control" id="skill-{{$loop->iteration}}" name="skill-{{$loop->iteration}}[]" style="Background-color: #eff2f7;" readonly>{{$data_fpkt->skill}}</textarea>
                                                                            </div>
                                                                        </td>
                                                                        @if(!empty($cek_peserta))               
                                                                            <td>
                                                                                <div class="input-group mb-2">
                                                                                    <input type="number" class="form-control peserta" id="level_peserta-{{$loop->iteration}}" name="level_peserta-{{$loop->iteration}}[]" value="{{$data_fpkt->level_peserta}}">
                                                                                </div>
                                                                            </td>
                                                                        @else
                                                                            <td>
                                                                                <div class="input-group mb-2">
                                                                                    <input type="number" class="form-control peserta" id="level_peserta-{{$loop->iteration}}" name="level_peserta-{{$loop->iteration}}[]" value="{{$data_fpkt->level_peserta}}" style="Background-color: #eff2f7;" readonly>
                                                                                </div>
                                                                            </td>
                                                                        @endif
                                                                        {{-- @if(!empty($cek_atasan))              
                                                                            <td>
                                                                                <div class="input-group mb-2">
                                                                                    <input type="number" class="form-control atasan" id="level_atasan-{{$loop->iteration}}" name="level_atasan-{{$loop->iteration}}[]" value="{{$data_fpkt->level_atasan}}">
                                                                                </div>
                                                                            </td>
                                                                        @else
                                                                            <td>
                                                                                <div class="input-group mb-2">
                                                                                    <input type="number" class="form-control atasan" id="level_atasan-{{$loop->iteration}}" name="level_atasan-{{$loop->iteration}}[]" value="{{$data_fpkt->level_atasan}}" style="Background-color: #eff2f7;" readonly>
                                                                                </div>
                                                                            </td>
                                                                        @endif                 --}}
                                                                        <td>
                                                                            <div class="input-group mb-2">
                                                                                <input type="number" class="form-control" id="level_rata-{{$loop->iteration}}" name="level_rata-{{$loop->iteration}}[]" value="{{$data_fpkt->level_rata}}" style="Background-color: #eff2f7;" readonly>
                                                                            </div>
                                                                        </td>               
                                                                        <td>
                                                                            <div class="input-group mb-2">
                                                                                <input type="text" class="form-control" id="level_kebutuhan-{{$loop->iteration}}" name="level_kebutuhan-{{$loop->iteration}}[]" value="{{$data_fpkt->level_kebutuhan}}" style="Background-color: #eff2f7;" readonly>
                                                                            </div>
                                                                        </td>                
                                                                        <td>
                                                                            <a href="javascript:void(0)" class="btn btn-soft-danger" disabled><i class="ri-delete-bin-line"></i></a>
                                                                        </td>
                                                                    </tr>
                                                                    @endforeach
                                                                @endif
                                                            </tbody>
                                                            <tbody>
                                                                <tr id="newForm" style="display: none;">
                                                                    <td class="d-none" colspan="5">
                                                                        <p>Add New Form</p>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    @if($arr_fpkt->isNotEmpty())
                                                                    @else
                                                                        <td colspan="5" id="cek-atasan">
                                                                            <a href="javascript:new_link()" 
                                                                                class="btn btn-soft-success"><i
                                                                                    class="ri-add-fill me-1 align-bottom"></i> Add New<span class="text-danger">*</span></a>
                                                                        </td>
                                                                    @endif
                                                                </tr>
                                                                <tr class="border-top border-top-dashed mt-2">
                                                                    <td colspan="3"></td>
                                                                    <td colspan="2" class="p-0"></td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <h5>Analisa Kebutuhan Pelatihan Karyawan<span class="text-danger">*</span></h5>
                                                <div class="row mt-2">
                                                    <label for="no_1" class="form-label">1. Apakah ada keterkaitan antara usulan training yang anda ajukan dengan pekerjaan anda saat ini?</label>
                                                    <div class="col-lg-2">
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="analisa_satu" id="satu_ya" value="satu_ya" required>
                                                            <label class="form-check-label col-form-label-sm" for="satu_ya">
                                                                Ya, sebutkan
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2">
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="analisa_satu" id="satu_tidak" value="satu_tidak" required>
                                                            <label class="form-check-label col-form-label-sm" for="satu_tidak">
                                                                Tidak
                                                            </label>
                                                        </div>                                        
                                                    </div>
                                                </div>
                                                <div id="cek_catatan_satu" class="mt-2">
                                                    <div class="col-lg-10">
                                                        <textarea class="form-control" id="catatan_satu" name="catatan_satu" rows="3"></textarea>
                                                    </div>
                                                </div>
                                                <div id="cek_analisa_dua" class="mt-4">
                                                    <div class="row">
                                                        <label for="no_2" class="form-label">2. Apakah ada permasalahan saat ini sehingga perlu dilakukan training?</label>
                                                        <div class="col-lg-2">
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio" name="analisa_dua" id="dua_ya" value="dua_ya" required>
                                                                <label class="form-check-label col-form-label-sm" for="dua_ya">
                                                                    Ya, sebutkan
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-2">
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio" name="analisa_dua" id="dua_tidak" value="dua_tidak" required>
                                                                <label class="form-check-label col-form-label-sm" for="dua_tidak">
                                                                    Tidak
                                                                </label>
                                                            </div>                                        
                                                        </div>
                                                    </div>
                                                    <div id="cek_catatan_dua" class="mt-2">
                                                        <div class="col-lg-10">
                                                            <textarea class="form-control" id="catatan_dua" name="catatan_dua" rows="3"></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div id="cek_analisa_tiga" class="mt-2">
                                                    <div id="cek_catatan_tiga" class="mt-2">
                                                        <label for="no_3" class="form-label">3. Apa harapan anda terhadap pelatihan yang anda usulkan?</label>
                                                        <div class="col-lg-10">
                                                            <textarea class="form-control" id="catatan_tiga" name="catatan_tiga" rows="3"></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mb-3 mt-4">
                                                <div class="col-lg-10">
                                                    <!-- Tables Border Colors -->
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered border-secondary fs-12 table-nowrap">
                                                            <thead>
                                                                <tr>
                                                                    <th scope="col">
                                                                        Catatan Dari Atasan : <br>
                                                                        <p class="text-muted"><i>(diisi jika skor kebutuhan training 5 atau 4).</i></p>
                                                                    </th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <th scope="row">
                                                                        <div>
                                                                            <textarea class="form-control" id="catatan" name="catatan" rows="3">{{ old('catatan', $fpkt->catatan ?? '') }}</textarea>
                                                                        </div>

                                                                        {{-- <div>
                                                                            <textarea class="form-control" id="catatan" name="catatan" rows="3" style="Background-color: #eff2f7;" readonly>{{ old('catatan', $fpkt->catatan ?? '') }}</textarea>
                                                                        </div> --}}
                                                                    </th>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row" id="form-submit">
                                            @if($arr_fpkt->isNotEmpty())
                                                @if(!empty($cek_peserta))
                                                    @if($arr_fpkt->sum('level_peserta') == 0)
                                                        <div class="col-lg-12">
                                                            <button class="btn btn-primary float-end" id="btn-peserta" name="action" value="peserta" type="submit">Submit</button>
                                                        </div>
                                                    @endif
                                                @endif
                                                {{-- @if(!empty($cek_atasan))
                                                    @if($arr_fpkt->sum('level_atasan') == 0)
                                                        <div class="col-lg-12">
                                                            <button class="btn btn-primary float-end" id="btn-atasan" name="action" value="atasan" type="submit">Submit</button>
                                                        </div>
                                                    @endif
                                                @endif --}}
                                            @else
                                                <div class="col-lg-12">
                                                    <button class="btn btn-primary float-end" id="btn-pemohon" name="action" value="pemohon" type="submit">Submit</button>
                                                </div>
                                            @endif
                                            </div>
                                        </form>
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
<!--Modal staticbackdrop-->
<div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <img src="/assets/images/loading.gif" style="width:120px;height:120px">                    
                <div class="mt-4">
                    <h4 class="mb-3">Please wait...</h4>
                    <h4 class="mb-3">Do not leave this page</h4>
                </div>
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
<script src="/assets/libs/simplebar/simplebar.min.js"></script>
<script src="/assets/libs/node-waves/waves.min.js"></script>
<!-- Toastr Notifications-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
@endsection
@section('javascript')
<script>
    $(document).ready(function() {  
        var id_user = {{ Js::from($user->employee_id) }};      
        $("#cek_provider").hide();
        $("#cek-atasan").hide();
        $('#id_vendor').on('change', function() {
            var vendor = this.value;
            if(vendor == 'other'){
                $("#vendor_other").val('');
                $("#cek_provider").show();
            }else{
                $("#vendor_other").val('');
                $("#cek_provider").hide();
            }
        });
        var style ="background-color:#eff2f7;";           
        var id_atasan = $("#id_atasan").val();          
        if(id_atasan == id_user){
            $("#catatan").removeAttr("style", style);
            $("#catatan").prop("readonly", false);
        }else{
            $("#catatan").attr("style", style);
            $("#catatan").prop("readonly", true);
        }
        $('#id_atasan').on('change', function() {
            $("#newlink").html("");            
            $("#cek-atasan").show();
            var atasan_id = this.value;
            if(atasan_id == id_user){
                $("#catatan").val("");
                $("#catatan").removeAttr("style", style);
                $("#catatan").prop("readonly", false);
            }else{
                $("#catatan").val("");
                $("#catatan").attr("style", style);
                $("#catatan").prop("readonly", true);
            }
        });
    });
</script>
<script>
    //convert currency
    var rupiah = document.getElementById('biaya_fpkt');
        rupiah.addEventListener("keyup", function(e) {
        // tambahkan 'Rp.' pada saat form di ketik
        // gunakan fungsi formatRupiah() untuk mengubah angka yang di ketik menjadi format angka
        rupiah.value = formatRupiah(this.value);
        });
        /* Fungsi formatRupiah */
        function formatRupiah(angka, prefix) {
        var number_string = angka.replace(/[^,\d]/g, "").toString(),
            split = number_string.split(","),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        // tambahkan titik jika yang di input sudah menjadi angka ribuan
        if (ribuan) {
            separator = sisa ? "." : "";
            rupiah += separator + ribuan.join(".");
        }

        rupiah = split[1] != undefined ? rupiah + "," + split[1] : rupiah;
        return prefix == undefined ? rupiah : rupiah ? rupiah : "";
        }
</script>
<script>
    $(document).ready(function() {
        $("#btn-peserta").click(function() {
            $("#Form-fpkt").submit(function () {
                $('#staticBackdrop').modal('show', true);
            });
        });
        $("#btn-atasan").click(function() {
            $("#Form-fpkt").submit(function () {
                $('#staticBackdrop').modal('show', true);
            });
        });
        $("#btn-pemohon").click(function() {
            $("#Form-fpkt").submit(function () {
                $('#staticBackdrop').modal('show', true);
            });
        });
    });
</script>
<script>
    $(document).ready(function () {
        var cek_fpkt = {{ Js::from($fpkt) }};
        if(!cek_fpkt){
            $('#cek_catatan_satu').hide();
            $('#cek_analisa_dua').hide();
            $('#cek_catatan_dua').hide();
            $('#cek_analisa_tiga').hide();
            $('#cek_catatan_tiga').hide();

            $("input[name='analisa_satu']").click(function() {
                var analisa_satu = this.value;
                if(analisa_satu == 'satu_ya'){
                    $('#cek_catatan_satu').show();
                    $('#catatan_satu').prop('required',true);
                    $('#cek_analisa_dua').show();
                    $('#dua_ya').prop('required',true);
                    $('#dua_tidak').prop('required',true);
                    $('#cek_analisa_tiga').hide();
                    $('#cek_catatan_tiga').hide();
                    $('#catatan_tiga').prop('required',false);
                    $('#catatan_tiga').val('');
                }else{
                    $('#cek_catatan_satu').hide();
                    $('#catatan_satu').prop('required',false);
                    $('#catatan_satu').val('');
                    $('#cek_analisa_dua').hide();
                    $('#cek_catatan_dua').hide();
                    $('#catatan_dua').prop('required',false);
                    $('#catatan_dua').val('');
                    $('#dua_ya').prop('checked', false);
                    $('#dua_tidak').prop('checked', false);
                    $('#dua_ya').prop('required',false);
                    $('#dua_tidak').prop('required',false);
                    $('#cek_analisa_tiga').show();
                    $('#cek_catatan_tiga').show();
                    $('#catatan_tiga').prop('required',true);
                }
            });
            $("input[name='analisa_dua']").click(function() {
                var analisa_dua = this.value;
                if(analisa_dua == 'dua_ya'){
                    $('#cek_catatan_dua').show();
                    $('#catatan_dua').prop('required',true);
                    $('#cek_analisa_tiga').show();
                    $('#cek_catatan_tiga').show();
                    $('#catatan_tiga').prop('required',true);
                }else{
                    $('#cek_catatan_dua').hide();
                    $('#catatan_dua').prop('required',false);
                    $('#catatan_dua').val('');
                    $('#cek_analisa_tiga').show();
                    $('#cek_catatan_tiga').show();
                    $('#catatan_tiga').prop('required',true);
                }
            });
        }else{
            //analisa satu
            if(!cek_fpkt.analisa_satu){
                $('#cek_catatan_satu').hide();
                $('#satu_tidak').prop('checked',true);
                $('#satu_tidak').prop('disabled',true);
                $('#satu_ya').prop('disabled',true);
            }else{
                $('#cek_catatan_satu').show();
                $('#satu_ya').prop('checked',true);
                $('#satu_ya').prop('disabled',true);
                $('#satu_tidak').prop('disabled',true);
                $("textarea#catatan_satu").val(cek_fpkt.analisa_satu);
                $("textarea#catatan_satu").attr('style',  'background-color:#eff2f7');
                $("textarea#catatan_satu").prop('readonly', true);
                $('#satu_tidak').prop('disabled',true);
            }
            //analisa dua
            if(!cek_fpkt.analisa_dua){
                $('#cek_analisa_dua').show();
                $('#cek_catatan_dua').hide();
                $('#dua_tidak').prop('checked',true);
                $('#dua_tidak').prop('disabled',true);
                $('#dua_ya').prop('disabled',true);
            }else{
                $('#cek_analisa_dua').show();
                $('#cek_catatan_dua').show();
                $('#dua_ya').prop('checked',true);
                $('#dua_ya').prop('disabled',true);
                $('#dua_tidak').prop('disabled',true);
                $("textarea#catatan_dua").val(cek_fpkt.analisa_dua);
                $("textarea#catatan_dua").attr('style',  'background-color:#eff2f7');
                $("textarea#catatan_dua").prop('readonly', true);
                $('#dua_tidak').prop('disabled',true);
            }
            $('#cek_analisa_tiga').show();
            $('#cek_catatan_tiga').show();
            $("textarea#catatan_tiga").val(cek_fpkt.analisa_tiga);
            $("textarea#catatan_tiga").attr('style',  'background-color:#eff2f7');
            $("textarea#catatan_tiga").prop('readonly', true);
        }
    });
</script>
<script>      
    $(".peserta").on('keyup', function(){
        if(this.value > 5){
            alert('Nilai yang anda masukkan melebihi skala');
            this.value = null;
        }
        let get_id = this.id;
        var urut = get_id.replace("level_peserta-", "");
        //kalkulasi rata rata 
        let nilai_peserta = this.value;
        let nilai_atasan = $("#level_atasan-" +urut +"").val();
        if(nilai_atasan >= 0){
            let rata_rata = (parseInt(nilai_peserta) + parseInt(nilai_atasan))/2;
            $('#level_rata-' +urut +'').val(Math.floor((rata_rata)));
            if(Math.floor((rata_rata)) == '1'){
                $('#level_kebutuhan-'+urut+'').val('Sangat Tinggi');
            }else if(Math.floor((rata_rata)) == '2'){
                $('#level_kebutuhan-'+urut+'').val('Tinggi');
            }else if(Math.floor((rata_rata)) == '3'){
                $('#level_kebutuhan-'+urut+'').val('Sedang');
            }else if(Math.floor((rata_rata)) == '4'){
                $('#level_kebutuhan-'+urut+'').val('Rendah');
            }else if(Math.floor((rata_rata)) == '5'){
                $('#level_kebutuhan-'+urut+'').val('Sangat Rendah');
            }else{
                $('#level_kebutuhan-'+urut+'').val('');
            }
        }
    }); 

    $(".atasan").on('keyup', function(){
        if(this.value > 5){
            alert('Nilai yang anda masukkan melebihi skala');
            this.value = null;
        }
        let get_id = this.id;
        var urut = get_id.replace("level_atasan-", "");
        //kalkulasi rata rata 
        let nilai_peserta = $("#level_peserta-" +urut +"").val();
        let nilai_atasan = this.value;
        if(nilai_peserta >= 0){
            let rata_rata = (parseInt(nilai_peserta) + parseInt(nilai_atasan))/2;
            $('#level_rata-' +urut +'').val(Math.floor((rata_rata)));
            if(Math.floor((rata_rata)) == '1'){
                $('#level_kebutuhan-'+urut+'').val('Sangat Tinggi');
            }else if(Math.floor((rata_rata)) == '2'){
                $('#level_kebutuhan-'+urut+'').val('Tinggi');
            }else if(Math.floor((rata_rata)) == '3'){
                $('#level_kebutuhan-'+urut+'').val('Sedang');
            }else if(Math.floor((rata_rata)) == '4'){
                $('#level_kebutuhan-'+urut+'').val('Rendah');
            }else if(Math.floor((rata_rata)) == '5'){
                $('#level_kebutuhan-'+urut+'').val('Sangat Rendah');
            }else{
                $('#level_kebutuhan-'+urut+'').val('');
            }
        }
    });            
</script>
<script>
    $(function () {    
        $('.select2').select2();
    });
</script>
<script>
    var count = 100;
    function new_link() {    
        count++;        
        var e = document.createElement("tr"),
            t = (e.id = count, e.className = "produk", 
            '<tr>'+
                '<th scope="row" class="produk-id" hidden>' + count + '</th>'+
                '<td class="text-start">'+
                    '<input type="hidden" id="nomor" name="no_urut[]" value="'+count+'">'+
                    '<div class="mb-2">'+
                        '<textarea rows="2" class="form-control" id="tujuan-' +count +'" name="tujuan-'+count+'[]"></textarea>'+
                    '</div>'+
                '</td>'+
                '<td>'+
                    '<div class="input-group mb-2">'+
                        '<textarea rows="2" class="form-control" id="kompetensi-' +count +'" name="kompetensi-'+count+'[]"></textarea>'+
                    '</div>'+
                '</td>'+                
                '<td>'+
                    '<div class="input-group mb-2">'+
                        '<textarea rows="2" class="form-control" id="skill-' +count +'" name="skill-'+count+'[]" required></textarea>'+
                    '</div>'+
                '</td>'+                
                '<td>'+
                    '<div class="input-group mb-2">'+
                        '<input type="number" class="form-control peserta" id="level_peserta-' +count +'" name="level_peserta-'+count+'[]" value="">'+
                    '</div>'+
                '</td>'+                
                '<td>'+
                    '<div class="input-group mb-2">'+
                        '<input type="number" class="form-control atasan" id="level_atasan-' +count +'" name="level_atasan-'+count+'[]" value="">'+
                    '</div>'+
                '</td>'+                
                '<td>'+
                    '<div class="input-group mb-2">'+
                        '<input type="number" class="form-control" id="level_rata-' +count +'" name="level_rata-'+count+'[]" value="" style="Background-color: #eff2f7;" readonly>'+
                    '</div>'+
                '</td>'+                
                '<td>'+
                    '<div class="input-group mb-2">'+
                        '<input text="text" class="form-control" id="level_kebutuhan-' +count +'" name="level_kebutuhan-'+count+'[]" value="" style="Background-color: #eff2f7;" readonly></textarea>'+
                    '</div>'+
                '</td>'+                
                '<td class="produk-removal">'+
                    '<a href="javascript:void(0)" class="btn btn-soft-danger"><i class="ri-delete-bin-line"></i></a>'+
                '</td>'+
            '</tr>'
            ),
            t = (e.innerHTML = document.getElementById("newForm").innerHTML + t, document.getElementById("newlink")
                .appendChild(e), document.querySelectorAll("[data-trigger]"));
        Array.from(t).forEach(function(e) {
            new Choices(e, {
                placeholderValue: "This is a placeholder set in the config",
                searchPlaceholderValue: "This is a search placeholder"
            })
        }), remove(), resetRow()
        //reinitialize the new select box
        $('.select2').select2();

        var id_user = {{ Js::from($user->employee_id) }};

        var peserta = {{ Js::from($cek_peserta)}};
        var style ="background-color:#eff2f7;";           
        if(!peserta){
            $(".peserta").attr("style", style);
            $(".peserta").prop("readonly", true);
            $(".produk-removal").prop("hidden", false);
        }else{
            $(".peserta").removeAttr("style", style);
            $(".peserta").prop("readonly", false);
            $(".produk-removal").prop("hidden", false);            
        }
        
            $("#cek-atasan").show();       
            var id_atasan = $("#id_atasan").val();          
            if(id_atasan == id_user){
                $(".atasan").removeAttr("style", style);
                $(".atasan").prop("readonly", false);
                $(".produk-removal").prop("hidden", false);
            }else{
                $(".atasan").attr("style", style);
                $(".atasan").prop("readonly", true);
                $(".produk-removal").prop("hidden", false);
            }
        // if(!atasan){
        //     $(".atasan").attr("style", style);
        //     $(".atasan").prop("readonly", true);
        //     $(".produk-removal").prop("hidden", false);
        // }else{
        //     $(".atasan").removeAttr("style", style);
        //     $(".atasan").prop("readonly", false);
        //     $(".produk-removal").prop("hidden", true);
        // }
        //limit skala peserta
        $(".peserta").on('keyup', function(){
            if(this.value > 5){
                alert('Nilai yang anda masukkan melebihi skala');
                this.value = null;
            }
            let get_id = this.id;
            var urut = get_id.replace("level_peserta-", "");
            //kalkulasi rata rata 
            let nilai_peserta = this.value;
            let nilai_atasan = $("#level_atasan-" +urut +"").val();
            if(nilai_atasan >= 0){
                let rata_rata = (parseInt(nilai_peserta) + parseInt(nilai_atasan))/2;
                $('#level_rata-' +urut +'').val(Math.floor((rata_rata)));
                if(Math.floor((rata_rata)) == '1'){
                    $('#level_kebutuhan-'+urut+'').val('Sangat Tinggi');
                }else if(Math.floor((rata_rata)) == '2'){
                    $('#level_kebutuhan-'+urut+'').val('Tinggi');
                }else if(Math.floor((rata_rata)) == '3'){
                    $('#level_kebutuhan-'+urut+'').val('Sedang');
                }else if(Math.floor((rata_rata)) == '4'){
                    $('#level_kebutuhan-'+urut+'').val('Rendah');
                }else if(Math.floor((rata_rata)) == '5'){
                    $('#level_kebutuhan-'+urut+'').val('Sangat Rendah');
                }else{
                    $('#level_kebutuhan-'+urut+'').val('');
                }
            }
        }); 
            
        $(".atasan").on('keyup', function(){
            if(this.value > 5){
                alert('Nilai yang anda masukkan melebihi skala');
                this.value = null;
            }
            let get_id = this.id;
            var urut = get_id.replace("level_atasan-", "");
            //kalkulasi rata rata 
            let nilai_peserta = $("#level_peserta-" +urut +"").val();
            let nilai_atasan = this.value;
            if(nilai_peserta >= 0){
                let rata_rata = (parseInt(nilai_peserta) + parseInt(nilai_atasan))/2;
                $('#level_rata-' +urut +'').val(Math.floor((rata_rata)));
                if(Math.floor((rata_rata)) == '1'){
                    $('#level_kebutuhan-'+urut+'').val('Sangat Tinggi');
                }else if(Math.floor((rata_rata)) == '2'){
                    $('#level_kebutuhan-'+urut+'').val('Tinggi');
                }else if(Math.floor((rata_rata)) == '3'){
                    $('#level_kebutuhan-'+urut+'').val('Sedang');
                }else if(Math.floor((rata_rata)) == '4'){
                    $('#level_kebutuhan-'+urut+'').val('Rendah');
                }else if(Math.floor((rata_rata)) == '5'){
                    $('#level_kebutuhan-'+urut+'').val('Sangat Rendah');
                }else{
                    $('#level_kebutuhan-'+urut+'').val('');
                }
            }
        });     
    }
    remove();
    function remove() {
        Array.from(document.querySelectorAll(".produk-removal a")).forEach(function(e) {
            e.addEventListener("click", function(e) {
                removeItem(e), resetRow()
            })
        })
    }

    function resetRow() {
        Array.from(document.getElementById("newlink").querySelectorAll("tr")).forEach(function(e, t) {
            t += 1;
            e.querySelector(".produk-id").innerHTML = t
        })
    }

    function removeItem(e) {
        e.target.closest("tr").remove()
    }
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
@endsection