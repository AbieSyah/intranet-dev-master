@extends('layouts.master')
@section('link')
<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- Required meta tags -->
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css"/>
<!-- Select2-->
<link rel="stylesheet" href="/assets/libs/adminlte/select2/css/4.1.0/select2.min.css">
<style type="text/css">
    .select2-container--default .select2-selection--multiple .select2-selection__choice__display { color: #000 }
    .select2-container.select2-container--default.select2-container--open  {
        z-index: 5000;
    }
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
<style>
    .wizard-content-left {
    background-blend-mode: darken;
    background-color: rgba(0, 0, 0, 0.45);
    background-image: url("https://i.ibb.co/X292hJF/form-wizard-bg-2.jpg");
    background-position: center center;
    background-size: cover;
    height: 100vh;
    padding: 30px;
    }
    .wizard-content-left h1 {
    color: #ffffff;
    font-size: 38px;
    font-weight: 600;
    padding: 12px 20px;
    text-align: center;
    }

    .form-wizard {
    color: #888888;
    padding: 30px;
    }
    .form-wizard .wizard-form-radio {
    display: inline-block;
    margin-left: 5px;
    position: relative;
    }
    .form-wizard .wizard-form-radio input[type="radio"] {
    -webkit-appearance: none;
    -moz-appearance: none;
    -ms-appearance: none;
    -o-appearance: none;
    appearance: none;
    background-color: #dddddd;
    height: 25px;
    width: 25px;
    display: inline-block;
    vertical-align: middle;
    border-radius: 50%;
    position: relative;
    cursor: pointer;
    }
    .form-wizard .wizard-form-radio input[type="radio"]:focus {
    outline: 0;
    }
    .form-wizard .wizard-form-radio input[type="radio"]:checked {
    background-color: #fb1647;
    }
    .form-wizard .wizard-form-radio input[type="radio"]:checked::before {
    content: "";
    position: absolute;
    width: 10px;
    height: 10px;
    display: inline-block;
    background-color: #ffffff;
    border-radius: 50%;
    left: 1px;
    right: 0;
    margin: 0 auto;
    top: 8px;
    }
    .form-wizard .wizard-form-radio input[type="radio"]:checked::after {
    content: "";
    display: inline-block;
    webkit-animation: click-radio-wave 0.65s;
    -moz-animation: click-radio-wave 0.65s;
    animation: click-radio-wave 0.65s;
    background: #000000;
    content: '';
    display: block;
    position: relative;
    z-index: 100;
    border-radius: 50%;
    }
    .form-wizard .wizard-form-radio input[type="radio"] ~ label {
    padding-left: 10px;
    cursor: pointer;
    }
    .form-wizard .form-wizard-header {
    text-align: center;
    }
    .form-wizard .form-wizard-next-btn, .form-wizard .form-wizard-previous-btn, .form-wizard .form-wizard-submit {
    background-color: #0b5394;
    color: #ffffff;
    display: inline-block;
    min-width: 100px;
    min-width: 120px;
    padding: 10px;
    text-align: center;
    }
    .form-wizard .form-wizard-next-btn:hover, .form-wizard .form-wizard-next-btn:focus, .form-wizard .form-wizard-previous-btn:hover, .form-wizard .form-wizard-previous-btn:focus, .form-wizard .form-wizard-submit:hover, .form-wizard .form-wizard-submit:focus {
    color: #ffffff;
    opacity: 0.6;
    text-decoration: none;
    }
    .form-wizard .wizard-fieldset {
    display: none;
    }
    .form-wizard .wizard-fieldset.show {
    display: block;
    }
    .form-wizard .wizard-form-error {
    display: none;
    background-color: #d70b0b;
    position: absolute;
    left: 0;
    right: 0;
    bottom: 0;
    height: 2px;
    width: 100%;
    }
    .form-wizard .form-wizard-previous-btn {
    background-color: #fb1647;
    }
    /* .form-wizard .form-control {
    font-weight: 300;
    height: auto !important;
    padding: 15px;
    color: #888888;
    background-color: #f1f1f1;
    border: none;
    }
    .form-wizard .form-control:focus {
    box-shadow: none;
    } */
    .form-wizard .form-group {
    position: relative;
    margin: 25px 0;
    }
    .form-wizard .wizard-form-text-label {
    position: absolute;
    left: 10px;
    top: 16px;
    transition: 0.2s linear all;
    }
    .form-wizard .focus-input .wizard-form-text-label {
    color: #0b5394;
    top: -18px;
    transition: 0.2s linear all;
    font-size: 12px;
    }
    .form-wizard .form-wizard-steps {
    margin: 30px 0;
    }
    .form-wizard .form-wizard-steps li {
    width: 25%;
    float: left;
    position: relative;
    }
    .form-wizard .form-wizard-steps li::after {
    background-color: #f3f3f3;
    content: "";
    height: 5px;
    left: 0;
    position: absolute;
    right: 0;
    top: 50%;
    transform: translateY(-50%);
    width: 100%;
    border-bottom: 1px solid #dddddd;
    border-top: 1px solid #dddddd;
    }
    .form-wizard .form-wizard-steps li span {
    background-color: #dddddd;
    border-radius: 50%;
    display: inline-block;
    height: 40px;
    line-height: 40px;
    position: relative;
    text-align: center;
    width: 40px;
    z-index: 1;
    }
    .form-wizard .form-wizard-steps li:last-child::after {
    width: 50%;
    }
    .form-wizard .form-wizard-steps li.active span, .form-wizard .form-wizard-steps li.activated span {
    background-color: #0b5394;
    color: #ffffff;
    }
    .form-wizard .form-wizard-steps li.active::after, .form-wizard .form-wizard-steps li.activated::after {
    background-color: #0b5394;
    left: 50%;
    width: 50%;
    border-color: #0b5394;
    }
    .form-wizard .form-wizard-steps li.activated::after {
    width: 100%;
    border-color: #0b5394;
    }
    .form-wizard .form-wizard-steps li:last-child::after {
    left: 0;
    }
    .form-wizard .wizard-password-eye {
    position: absolute;
    right: 32px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    }
    @keyframes click-radio-wave {
    0% {
        width: 25px;
        height: 25px;
        opacity: 0.35;
        position: relative;
    }
    100% {
        width: 60px;
        height: 60px;
        margin-left: -15px;
        margin-top: -15px;
        opacity: 0.0;
    }
    }
    @media screen and (max-width: 767px) {
    .wizard-content-left {
        height: auto;
    }
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
                                        <form id="Formfkt" action="{{route('profile.training.fkt.pti.store')}}" method="POST">
                                            @csrf
                                            @method('put')
                                            <div class="row mb-3">
                                                <div class="col-lg-6">
                                                    <h4 class="text-primary">Formulir Kebutuhan Training (FKT)</h4>
                                                </div>
                                                <div class="col-lg-6">
                                                    <a href="{{ route('profile.back.fkt.pti') }}" class="btn btn-primary btn-label waves-effect waves-light float-end"><i class="ri-arrow-left-circle-line label-icon align-middle fs-16 me-2"></i> Back</a>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-lg-3">
                                                    <label for="pemohon" class="form-label col-form-label col-form-label-sm">Nama Pemohon</label>
                                                </div>
                                                <div class="col-lg-6">
                                                    <input type="hidden" class="form-control form-control-sm" id="id_pemohon" name="id_pemohon" placeholder="Masukkan id" value="{{$user->employee->id}}">
                                                    <input type="text" class="form-control form-control-sm" id="nama_pemohon" name="nama_pemohon" placeholder="Masukkan Nama" value="{{$user->employee->fullname}}" style="Background-color: #eff2f7;" readonly>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-lg-3">
                                                    <label for="department" class="form-label col-form-label col-form-label-sm">Departemen</label>
                                                </div>
                                                <div class="col-lg-6">
                                                    <input type="text" class="form-control form-control-sm" id="department" placeholder="Masukkan Departemen" value="{{$user->employee->department->name}}" style="Background-color: #eff2f7;" readonly>
                                                </div>
                                                <div class="col-lg-3">                                        
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-lg-3">
                                                    <label for="tahun_usulan" class="form-label col-form-label col-form-label-sm">Tahun Usulan Program</label>
                                                </div>
                                                <div class="col-lg-2">
                                                    <div class="form-group">
                                                        <select class="form-control select2" id="tahun_usulan" name="tahun_usulan" required>
                                                            <option value="{{ $year_now }}" selected="true">{{ $year_now }}</option>
                                                            <option value="{{ $next_year }}">{{ $next_year }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-7">                                        
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-lg-3">
                                                    <label for="tahun_pelaksanaan" class="form-label col-form-label col-form-label-sm">Tahun Rencana Pelaksanaan</label>
                                                </div>
                                                <div class="col-lg-2">
                                                    <div class="form-group">
                                                        <select class="form-control select2" id="tahun_pelaksanaan" name="tahun_pelaksanaan" required>
                                                            <option value="{{ $year_now }}" selected="true">{{ $year_now }}</option>
                                                            <option value="{{ $next_year }}">{{ $next_year }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-7">                                        
                                                </div>
                                            </div>
                                            <div class="row mb-3" >
                                                <div class="col-lg-3">
                                                    <label for="tipe" class="form-label col-form-label col-form-label-sm">Tujuan Usulan Program</label>
                                                </div>
                                                <div class="col-lg-3">
                                                    <input type="text" class="form-control form-control-sm" value="Program Training Insidentil" style="Background-color: #eff2f7;" readonly>
                                                    <input type="hidden" class="form-control form-control-sm" name="tipe" id="tipe" value="pti" required>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-lg-3">
                                                    <label for="check" class="form-label col-form-label col-form-label-sm">Kepala Departemen</label>
                                                </div>
                                                <div class="col-lg-6">
                                                    <select class="form-control form-control-sm select2 fs-12" name="id_checker"
                                                        id="id_checker" data-placeholder="--Pilih Kepala Departemen--" required>
                                                        <option selected="true" disabled="true"></option>
                                                        @foreach ($employees as $emp)
                                                            @if(!empty($emp->level->nama))
                                                            <option value="{{ $emp->id }}">{{ $emp->fullname }} -- {{$emp->department->name}} -- {{$emp->level->nama}}</option>
                                                            @else
                                                            <option value="{{ $emp->id }}">{{ $emp->fullname }} -- {{$emp->department->name}}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-lg-3">
                                                    <label for="check" class="form-label col-form-label col-form-label-sm">Alasan Insidentil</label>
                                                </div>
                                                <div class="col-lg-6">
                                                    <textarea class="form-control" id="alasan_pti" name="alasan_pti" rows="3" required></textarea>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="card-body p-4 border-top border-top-dashed">
                                                    <div data-simplebar data-simplebar-auto-hide="false" style="max-width: 100%;">
                                                        <table class="table table-borderless fs-12" style="table-layout: fixed; width: 300%;">
                                                            <thead class="align-middle">
                                                                <tr class="table-active">
                                                                    <th scope="col" style="width: 2%;">#</th>
                                                                    <th scope="col" style="width: 10%;">
                                                                        Nama Peserta
                                                                    </th>
                                                                    <th scope="col" style="width: 10%;">
                                                                        Nama Atasan Peserta
                                                                    </th>
                                                                    <th scope="col" style="width: 15%;">
                                                                        Pelatihan
                                                                    </th>
                                                                    <th scope="col" style="width: 10%;">
                                                                        Jenis Pelatihan
                                                                    </th>
                                                                    <th scope="col" style="width: 10%">
                                                                        Sifat Pelatihan
                                                                    </th>
                                                                    <th scope="col" style="width: 15%">Alasan</th>
                                                                    <!-- <th scope="col" style="width: 10%">Periode</th> -->
                                                                    <th scope="col" style="width: 10%">Bulan Pelaksanaan</th>
                                                                    <th scope="col" id="h-provider" style="width: 10%">Provider</th>
                                                                    <th scope="col" id="h-biaya" style="width: 10%">Biaya</th>
                                                                    <th scope="col" id="h-akomodasi" style="width: 10%">Akomodasi</th>
                                                                    <th scope="col" style="width: 2%"></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="newlink2">           
                                                                    
                                                            </tbody>
                                                            <tbody>
                                                                <tr id="newForm" style="display: none;">
                                                                    <td class="d-none" colspan="5">
                                                                        <p>Add New Form</p>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="5">
                                                                        <a href="javascript:new_link2()" id="add-item"
                                                                            class="btn btn-soft-success btn-sm"><i
                                                                                class="ri-add-fill me-1 align-bottom"></i> Tambah Peserta</a>
                                                                    </td>
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
                                            <div class="row" id="form-submit">
                                                <div class="col-lg-12">
                                                    <div class="hstack gap-2 d-print-none mt-4" style="justify-content: flex-end;">
                                                        <button class="btn btn-secondary" id="btn-draft" name="action" value="draft" type="submit">Draft</button>
                                                        <button class="btn btn-primary" id="btn-submit" name="action" value="submit" type="submit">Submit</button>
                                                    </div>
                                                </div>
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

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                </div>
                <div class="modal-body ">
                    <div class="form-wizard">
                        <form id="inputs">
                            <div class="form-wizard-header">
                                <!-- <p>Fill all form field to go next step</p> -->
                                <img src="/assets/images/task.png" alt="">
                                <ul class="list-unstyled form-wizard-steps clearfix">
                                    
                                </ul>
                            </div>
                            <fieldset id="set-1" class="wizard-fieldset show">
                                <h5>Training Participant Information</h5>
                                <div class="form-group">
                                    <label for="md_peserta" class="form-label">Nama Peserta</label>
                                    <select class="fs-12 wizard-required form-control select2" name="md_peserta" id="md_peserta" data-placeholder="Pilih Peserta" multiple="multiple">
                                        @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}">{{ $employee->fullname }}</option>
                                        @endforeach
                                    </select>
                                    <div class="wizard-form-error"></div>
                                </div>
                                <div class="form-group">
                                    <label for="md_penilai" class="form-label">Nama Atasan Peserta</label>                                        
                                        <select class="fs-12 wizard-required form-control select2" name="md_penilai" id="md_penilai" data-placeholder="Pilih Atasan">
                                            <option selected="true" value="" hidden></option>
                                            @foreach ($employees as $emp)
                                                @if(!empty($emp->level->nama))
                                                <option value="{{ $emp->id }}">{{ $emp->fullname }} -- {{$emp->department->name}} -- {{$emp->level->nama}}</option>
                                                @else
                                                <option value="{{ $emp->id }}">{{ $emp->fullname }} -- {{$emp->department->name}}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    <div class="wizard-form-error"></div>
                                </div>                                
                                <div class="form-group">
                                    <label for="md_pelatihan" class="form-label">Nama Pelatihan</label>
                                    <input type="text" class="form-control wizard-required" name="md_pelatihan" id="md_pelatihan" placeholder="Masukkan Nama Pelatihan">
                                    <div class="wizard-form-error"></div>
                                </div>
                                <div class="form-group">
                                    Metode :
                                    <div class="wizard-form-radio">
                                        <input name="md_jenis" class="wizard-required" id="md_online" type="radio" value="Online">
                                        <label for="md_online">Online</label>
                                    </div>
                                    <div class="wizard-form-radio">
                                        <input name="md_jenis" class="wizard-required" id="md_offline" type="radio" value="Offline">
                                        <label for="md_offline">Offline</label>
                                    </div>
                                </div>
                                <div class="form-group clearfix">
                                    <a href="javascript:;" class="form-wizard-next-btn float-right">Next</a>
                                </div>
                            </fieldset> 
                            <fieldset id="set-2" class="wizard-fieldset">
                                <h5>Fill all form field to go next step</h5>
                                <div class="form-group">
                                <label for="md_sifat" class="form-label">Jenis</label>
                                    <select class="form-select wizard-required" id="md_sifat" name="md_sifat">
                                        <option selected value="">Open this select menu</option>
                                        <option value="Skill Training">Skill Training</option>
                                        <option value="Re-Training">Re-Training</option>
                                        <option value="Cross Functional Training">Cross Functional Training</option>
                                        <option value="Team Training">Team Training</option>
                                    </select>
                                    <div class="wizard-form-error"></div>
                                </div>
                                <div class="form-group">
                                <label for="md_alasan" class="form-label">Alasan</label>
                                    <textarea style="white-space: pre-line;" class="form-control wizard-required" id="md_alasan" name="md_alasan" placeholder="Masukkan Alasan" rows="3"></textarea>
                                    <div class="wizard-form-error"></div>
                                </div>
                                <div class="form-group">
                                <label for="md_bulan" class="form-label">Bulan Pelaksanaan</label>
                                    <select class="form-select wizard-required" id="md_bulan" name="md_bulan">
                                        <option selected value="">Open this select menu</option>
                                        <option value="Fleksibel">Fleksibel</option>
                                        <option value="Januari">Januari</option>
                                        <option value="Februari">Februari</option>
                                        <option value="Maret">Maret</option>
                                        <option value="April">April</option>
                                        <option value="Mei">Mei</option>
                                        <option value="Juni">Juni</option>
                                        <option value="Juli">Juli</option>
                                        <option value="Agustus">Agustus</option>
                                        <option value="September">September</option>
                                        <option value="Oktober">Oktober</option>
                                        <option value="November">November</option>
                                        <option value="Desember">Desember</option>
                                    </select>
                                    <div class="wizard-form-error"></div>
                                </div>
                                <div class="form-group clearfix">
                                    <a href="javascript:;" class="form-wizard-previous-btn float-left">Previous</a>
                                    <a href="javascript:;" class="form-wizard-next-btn float-right">Next</a>
                                </div>
                            </fieldset> 
                            <fieldset id="set-3" class="wizard-fieldset">
                                <div id="md-ptt">
                                    <h5>Fill all form field to go next step</h5>
                                    <div class="form-group">
                                        <label for="md_provider" class="form-label">Provider</label>
                                        <select class="fs-12 form-control select2" name="md_provider" id="md_provider" data-placeholder="Pilih Vendor">
                                            <option selected="true" value=""></option>
                                            @foreach($vendors as $vendor)
                                                <option value="{{ $vendor->id }}">{{ $vendor->nama }}</option>
                                            @endforeach
                                                <option value="other">Other</option>
                                        </select>
                                        <div class="wizard-form-error"></div>
                                    </div>
                                    <div id="md_other">
                                        
                                    </div>
                                    <label for="md_biaya" class="form-label">Biaya</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span><input type="text" class="form-control wizard-required" id="md_biaya" name="md_biaya" placeholder="Masukkan Biaya">
                                        <div class="wizard-form-error"></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="md_penginapan" class="form-label">a) Menginap: </label>
                                        <select class="form-select wizard-required" id="md_penginapan" name="md_penginapan">
                                            <option selected value="">Open this select menu</option>
                                            <option value="Ya">Ya</option>
                                            <option value="Tidak">Tidak</option>
                                        </select>
                                        <div class="wizard-form-error"></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="md_transportasi" class="form-label">b) Transportasi: </label>
                                        <select class="form-select wizard-required" id="md_transportasi" name="md_transportasi">
                                            <option selected value="">Open this select menu</option>
                                            <option value="Ya">Ya</option>
                                            <option value="Tidak">Tidak</option>
                                        </select>
                                        <div class="wizard-form-error"></div>
                                    </div>
                                    <div class="form-group clearfix">
                                        <a href="javascript:;" class="form-wizard-previous-btn float-left">Previous</a>
                                        <a href="javascript:;" class="form-wizard-next-btn float-right">Next</a>
                                    </div>
                                </div>
                            </fieldset> 
                            <fieldset id="set-4" class="wizard-fieldset">
                                <div class="text-center">
                                    <div class="avatar-md mt-5 mb-4 mx-auto">
                                        <div class="avatar-title bg-light text-success display-4 rounded-circle">
                                            <i class="ri-checkbox-circle-fill"></i>
                                        </div>
                                    </div>
                                    <h5>Well Done !</h5>
                                    <p class="text-muted">
                                        You have successfully filled in the registration form
                                    </p>
                                    <div class="form-group clearfix">
                                        <a href="javascript:;" class="form-wizard-previous-btn float-left">Previous</a>
                                        <a href="javascript:;" id="submit-wizard" class="form-wizard-next-btn float-right" data-bs-dismiss="modal" aria-label="Close">Finish</a>
                                        <!-- <button type="button" id="submit-wizard" class="form-wizard-submit float-right" data-bs-dismiss="modal" aria-label="Close">Finish</button> -->
                                    </div>
                                </div>
                            </fieldset> 
                        </form>                            
                    </div>  
                </div>  
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
<!-- Select2 -->
<script src="/assets/libs/adminlte/select2/js/4.1.0/select2.min.js"></script>
<script src="/assets/js/pages/select2.init.js"></script>
<!-- profile-setting init js -->
<script src="/assets/js/pages/profile-setting.init.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.js"></script>
<!-- Sweetalert -->
<script src="{{asset('assets/libs/sweetalert2/11/sweetalert2.min.js')}}"></script>
@endsection
@section('javascript')
<script>
    $(document).ready(function() {
        $("#btn-submit").click(function() {
            $("#Formfkt").submit(function () {
                $('#staticBackdrop').modal('show', true);
            });
        });
        $("#btn-draft").click(function() {
            $("#Formfkt").submit(function () {
                $('#staticBackdrop').modal('show', true);
            });
        });
    });
</script>
<script>
    $(document).ready(function () {
        $('#myModal').modal({
            backdrop: 'static',
            keyboard: false
        });
        $("#md_other").html('');
        $('#md_provider').on('change', function() {
            var vendor = this.value;
            if(vendor == 'other'){
                $("#md_other").append('<div class="form-group">'+
                    '<input type="text" class="form-control wizard-required" name="md_provider_other" id="md_provider_other" placeholder="Masukkan Nama Provider">'+
                    '<div class="wizard-form-error"></div>'+
                    '</div>');
            }else{
                $("#md_other").html('');
            }
        });
        $('#myModal').on('hidden.bs.modal', function () {
            $("#md_other").html('');
            $(this).find('form').trigger('reset');
            // wizard-fieldset
            $("#step-1").addClass('active');
            $("#step-1").removeClass('activated');
            $("#step-2").removeClass('activated');
            $("#step-3").removeClass('activated');
            $("#step-3").removeClass('active');
            $("#step-4").removeClass('activated');
            $("#step-4").removeClass('active');

            $("#set-1").addClass('show');
            $("#set-2").removeClass('show');
            $("#set-3").removeClass('show');
            $("#set-4").removeClass('show');
            $(this).removeData('bs.modal');
            $(".form-wizard-steps").trigger('reset');            
        });
    });
</script>
<script>
    //convert currency
    var rupiah = document.getElementById('md_biaya');
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
    jQuery(document).ready(function() {
  // click on next button
  jQuery('.form-wizard-next-btn').click(function() {
      var parentFieldset = jQuery(this).parents('.wizard-fieldset');
      var currentActiveStep = jQuery(this).parents('.form-wizard').find('.form-wizard-steps .active');
      var next = jQuery(this);
      var nextWizardStep = true;
      if (document.querySelectorAll('input[name="md_jenis"]:checked').length > 0) {
          nextWizardStep = true;
      } else {
          alert('Metode tidak boleh kosong.');
          nextWizardStep = false;
      }
    parentFieldset.find('.wizard-required').each(function(){
      var thisValue = jQuery(this).val();

      if( thisValue == "") {
        jQuery(this).siblings(".wizard-form-error").slideDown();
        nextWizardStep = false;
      }
      else {
        jQuery(this).siblings(".wizard-form-error").slideUp();
      }
    });
    if( nextWizardStep) {
      next.parents('.wizard-fieldset').removeClass("show","400");
      currentActiveStep.removeClass('active').addClass('activated').next().addClass('active',"400");
      next.parents('.wizard-fieldset').next('.wizard-fieldset').addClass("show","400");
      jQuery(document).find('.wizard-fieldset').each(function(){
        if(jQuery(this).hasClass('show')){
          var formAtrr = jQuery(this).attr('data-tab-content');
          jQuery(document).find('.form-wizard-steps .form-wizard-step-item').each(function(){
            if(jQuery(this).attr('data-attr') == formAtrr){
              jQuery(this).addClass('active');
              var innerWidth = jQuery(this).innerWidth();
              var position = jQuery(this).position();
              jQuery(document).find('.form-wizard-step-move').css({"left": position.left, "width": innerWidth});
            }else{
              jQuery(this).removeClass('active');
            }
          });
        }
      });
    }
  });
  //click on previous button
  jQuery('.form-wizard-previous-btn').click(function() {
    var counter = parseInt(jQuery(".wizard-counter").text());;
    var prev =jQuery(this);
    var currentActiveStep = jQuery(this).parents('.form-wizard').find('.form-wizard-steps .active');
    prev.parents('.wizard-fieldset').removeClass("show","400");
    prev.parents('.wizard-fieldset').prev('.wizard-fieldset').addClass("show","400");
    currentActiveStep.removeClass('active').prev().removeClass('activated').addClass('active',"400");
    jQuery(document).find('.wizard-fieldset').each(function(){
      if(jQuery(this).hasClass('show')){
        var formAtrr = jQuery(this).attr('data-tab-content');
        jQuery(document).find('.form-wizard-steps .form-wizard-step-item').each(function(){
          if(jQuery(this).attr('data-attr') == formAtrr){
            jQuery(this).addClass('active');
            var innerWidth = jQuery(this).innerWidth();
            var position = jQuery(this).position();
            jQuery(document).find('.form-wizard-step-move').css({"left": position.left, "width": innerWidth});
          }else{
            jQuery(this).removeClass('active');
          }
        });
      }
    });
  });
  // focus on input field check empty or not
  jQuery(".form-control").on('focus', function(){
    var tmpThis = jQuery(this).val();
    if(tmpThis == '' ) {
      jQuery(this).parent().addClass("focus-input");
    }
    else if(tmpThis !='' ){
      jQuery(this).parent().addClass("focus-input");
    }
  }).on('blur', function(){
    var tmpThis = jQuery(this).val();
    if(tmpThis == '' ) {
      jQuery(this).parent().removeClass("focus-input");
      jQuery(this).siblings('.wizard-form-error').slideDown("3000");
    }
    else if(tmpThis !='' ){
      jQuery(this).parent().addClass("focus-input");
      jQuery(this).siblings('.wizard-form-error').slideUp("3000");
    }
  });
});
</script>
<script>
    $(function () {    
        $('.select2').select2();
        // $('#md_peserta').select2({dropdownParent: $('#myModal .modal-content')});

        $('#myModal').on('shown.bs.modal', function (e) {
            $(this).find('.select2').select2({
                dropdownParent: $(this).find('.modal-content')
            });
        })

        $('#form-submit').hide();
        $('#add-item').click(function(){
            $('#form-submit').show();
        });
    });
</script>
<script>
        $(".form-wizard-steps").html("");
        $('#h-provider').show();
        $('#h-biaya').show();
        $('#h-akomodasi').show();

        $("td.provider").prop('hidden',false);
        $("td.biaya").prop('hidden',false);
        $("td.akomodasi").prop('hidden',false);
        $(':input.vendor').prop('required',false);
        $(':input.nominal').prop('required',true);
        $(':input.inap').prop('required',true);
        $(':input.transport').prop('required',true);
        //modal training
        $(".form-wizard-steps").append(
            '<li id="step-1" class="active"><span>1</span></li>'+
            '<li id="step-2"><span>2</span></li>'+
            '<li id="step-3"><span>3</span></li>'+
            '<li id="step-4"><span>4</span></li>'
        );
        $("#md-ptt").prop('hidden',false);
</script>
<script>
    var count = 0;
    function new_link2() {
        $('#myModal').modal('show'); 
        count++;        
        var e = document.createElement("tr"),
            t = (e.id = count, e.className = "produk", 
            '<tr>'+
                '<th scope="row" class="produk-id">' + count + '</th>'+
                '<td class="text-start">'+
                    '<input type="hidden" id="nomor" name="no_urut[]" value="'+count+'">'+
                    '<div class="mb-2">'+
                        '<div class="form-group">'+
                            '<select class="fs-12 form-control form-control-sm select2 @error("id_peserta") is-invalid @enderror" name="id_peserta-'+count+'[]" id="peserta-'+count+'" data-placeholder="Select Employee" multiple="multiple" required>@foreach($employees as $employee)<option value="{{ $employee->id }}">{{ $employee->fullname }}</option>@endforeach</select>'+
                        '</div>'+
                    '</div>'+
                '</td>'+
                '<td>'+
                    '<div class="form-group">'+
                        '<select class="form-control form-control-sm select2 @error("id_penilai") is-invalid @enderror" name="id_penilai-'+count+'[]" id="id_penilai-dropdown-' +count +'" data-placeholder="Pilih Atasan" required>'+
                            '<option selected="true" disabled="true"></option>'+
                            '@foreach($employees as $employee)'+
                                '@if(!empty($employee->level->nama))'+
                                    '<option value="{{ $employee->id }}">{{ $employee->fullname }} -- {{$employee->department->name}} -- {{$employee->level->nama}}</option>'+
                                '@else'+
                                    '<option value="{{ $employee->id }}">{{ $employee->fullname }} -- {{$employee->department->name}}</option>'+
                                '@endif'+
                            '@endforeach'+
                        '</select>'+
                    '</div>'+
                '</td>'+
                '<td>'+
                    '<div class="input-group mb-2">'+
                        '<input type="text" class="form-control form-control-sm" id="judul-' +count +'" name="judul-'+count+'[]" placeholder="Masukkan Pelatihan" value="" required>'+
                    '</div>'+
                    // '<div>'+
                    //     '<textarea style="white-space: pre-line;" class="form-control form-control-sm" id="detail-' +count +'" name="detail-'+count+'[]" placeholder="Masukkan Detail" value="" rows="3" required></textarea>'+
                    // '</div>'+
                '</td>'+
                '<td>'+
                    '<select class="form-select form-select-sm mb-2" id="jenis_pelatihan-' +count +'" name="jenis_pelatihan-'+count+'[]" required>'+
                        '<option selected value="">Open this select menu</option>'+
                        '<option value="Online">Online</option>'+
                        '<option value="Offline">Offline</option>'+
                    '</select>'+
                '</td>'+
                '<td>'+
                    // '<div class="input-group">'+
                    //     '<input type="text" class="form-control form-control-sm" id="sifat-' +count +'" name="sifat-'+count+'[]" placeholder="Masukkan Sifat" value="" required>'+
                    // '</div>'+
                    '<select class="form-select form-select-sm mb-2" id="sifat-' +count +'" name="sifat-'+count+'[]" required>'+
                        '<option selected value="">Open this select menu</option>'+
                        '<option value="Skill Training">Skill Training</option>'+
                        '<option value="Re-Training">Re-Training</option>'+
                        '<option value="Cross Functional Training">Cross Functional Training</option>'+
                        '<option value="Team Training">Team Training</option>'+
                    '</select>'+
                '</td>'+
                '<td>'+
                    '<div>'+
                        '<textarea style="white-space: pre-line;" class="form-control form-control-sm" id="alasan-' +count +'" name="alasan-'+count+'[]" placeholder="Masukkan Alasan" value="" rows="3" required></textarea>'+
                    '</div>'+
                '</td>'+
                // '<td>'+
                //     '<div class="input-group input-group-sm">'+
                //         '<input type="text" name="periode-' +count +'[]" id="periode-'+count+'" class="form-control @error("periode") is-invalid @enderror" placeholder="Pilih Tanggal" value="" required>'+
                //         '<span class="input-group-text"><i class="ri-calendar-event-line"></i></span>'+
                //     '</div>'+
                // '</td>'+
                '<td>'+
                    '<select class="form-select form-select-sm mb-2" id="bulan_pelaksanaan-' +count +'" name="bulan_pelaksanaan-'+count+'[]" required>'+
                        '<option selected value="">Open this select menu</option>'+
                        '<option value="Fleksibel">Fleksibel</option>'+
                        '<option value="Januari">Januari</option>'+
                        '<option value="Februari">Februari</option>'+
                        '<option value="Maret">Maret</option>'+
                        '<option value="April">April</option>'+
                        '<option value="Mei">Mei</option>'+
                        '<option value="Juni">Juni</option>'+
                        '<option value="Juli">Juli</option>'+
                        '<option value="Agustus">Agustus</option>'+
                        '<option value="September">September</option>'+
                        '<option value="Oktober">Oktober</option>'+
                        '<option value="November">November</option>'+
                        '<option value="Desember">Desember</option>'+
                    '</select>'+
                '</td>'+
                '<td class="provider">'+
                    '<div class="form-group">'+
                        '<select class="form-control form-control-sm select2 vendor" name="id_vendor-'+count+'[]" id="id_vendor-dropdown-' +count +'" data-placeholder="Pilih Vendor">'+
                            '<option selected="true" value=""></option>'+
                            '@foreach($vendors as $vendor)'+
                                '<option value="{{ $vendor->id }}">{{ $vendor->nama }}</option>'+
                            '@endforeach'+
                                '<option value="other">Other</option>'+
                        '</select>'+
                    '</div>'+
                    '<div id="cek_provider-'+count+'">'+
                        '<div class="form-group mt-3">'+
                            '<input type="text" class="form-control form-control-sm" id="vendor_other-' +count +'" name="vendor_other-'+count+'[]" placeholder="Masukkan Provider" value="">'+
                        '</div>'+
                    '</div>'+
                '</td>'+
                '<td class="biaya">'+
                    '<div class="input-group input-group-sm">'+
                        '<span class="input-group-text">Rp</span><input type="text" class="form-control form-control-sm nominal" id="biaya_fkt-' +count +'" name="biaya_fkt-'+count+'[]" placeholder="Masukkan Biaya" value="">'+
                    '</div>'+
                '</td>'+
                '<td class="akomodasi">'+
                    '<label for="penginapan" class="form-label">a) Menginap: </label>'+
                    // '<div class="input-group mb-2">'+
                    //     '<input type="text" class="form-control form-control-sm inap" id="penginapan-' +count +'" name="penginapan-'+count+'[]" placeholder="Masukkan Penginapan" value="">'+
                    // '</div>'+
                    '<select class="form-select form-select-sm mb-2 inap" id="penginapan-' +count +'" name="penginapan-'+count+'[]" required>'+
                        '<option selected value="">Open this select menu</option>'+
                        '<option value="Ya">Ya</option>'+
                        '<option value="Tidak">Tidak</option>'+
                    '</select>'+
                    '<label for="penginapan" class="form-label">b) Transportasi: </label>'+
                    // '<div class="input-group">'+
                    //     '<input type="text" class="form-control form-control-sm transport" id="transportasi-' +count +'" name="transportasi-'+count+'[]" placeholder="Masukkan Transportasi" value="">'+
                    // '</div>'+
                    '<select class="form-select form-select-sm mb-2 transport" id="transportasi-' +count +'" name="transportasi-'+count+'[]" required>'+
                        '<option selected value="">Open this select menu</option>'+
                        '<option value="Ya">Ya</option>'+
                        '<option value="Tidak">Tidak</option>'+
                    '</select>'+
                '</td>'+
                '<td class="produk-removal">'+
                    '<a href="javascript:avoid(0);" onclick="remove();" class="btn btn-soft-danger"><i class="ri-delete-bin-line"></i></a>'+
                '</td>'+
            '</tr>'
            ),
            t = (e.innerHTML = document.getElementById("newForm").innerHTML + t, document.getElementById("newlink2")
                .appendChild(e), document.querySelectorAll("[data-trigger]"));
        Array.from(t).forEach(function(e) {
            new Choices(e, {
                placeholderValue: "This is a placeholder set in the config",
                searchPlaceholderValue: "This is a search placeholder"
            })
        }), remove(), resetRow()
        //reinitialize the new select box
        $('.select2').select2();
        $('#cek_provider-'+count+'').hide();
        $('#id_vendor-dropdown-'+count+'').on('change', function() {
            var vendor = this.value;
            if(vendor == 'other'){
                $('#cek_provider-'+count+'').show();
                $('#vendor_other-'+count+'').val(input_vendor_other);
            }else{
                $('#cek_provider-'+count+'').hide();
                $('#vendor_other-'+count+'').val('');
            }
        });

        $("#submit-wizard").click(function() {
            //Declare and initialize variable for display inputs in div
            var input_peserta = "";
            var input_penilai = "";
            var input_pelatihan = "";
            var input_jenis = "";
            var input_sifat = "";
            var input_alasan = "";
            var input_bulan = "";
            var input_vendor = "";
            var input_vendor_other = "";
            var input_biaya = "";
            var input_penginapan = "";
            var input_transportasi = "";
            $("#inputs").each(function() {
                var md_peserta = $(this).find("#md_peserta").val();
                var md_penilai = $(this).find("#md_penilai").val();
                var md_pelatihan = $(this).find("#md_pelatihan").val();
                var md_jenis = $(this).find("input[name='md_jenis']:checked").val();
                var md_sifat = $(this).find("#md_sifat").val();
                var md_alasan = $(this).find("#md_alasan").val();
                var md_bulan = $(this).find("#md_bulan").val();
                var md_provider = $(this).find("#md_provider").val();
                var md_provider_other = $(this).find("#md_provider_other").val();
                var md_biaya = $(this).find("#md_biaya").val();
                var md_penginapan = $(this).find("#md_penginapan").val();
                var md_transportasi = $(this).find("#md_transportasi").val();
                input_peserta += md_peserta;
                input_penilai += md_penilai;
                input_pelatihan += md_pelatihan;
                input_jenis += md_jenis;
                input_sifat += md_sifat;
                input_alasan += md_alasan;
                input_bulan += md_bulan;
                input_vendor += md_provider;
                input_vendor_other += md_provider_other;
                input_biaya += md_biaya;
                input_penginapan += md_penginapan;
                input_transportasi += md_transportasi;
            });

            var arr_peserta = input_peserta.split(',');
            $.each(arr_peserta, function(i,e){
                $('#peserta-'+count+'').find('option[value="'+e+'"]').prop('selected', true);
            });
            $('#peserta-'+count+'').trigger('change.select2');
            $('#id_penilai-dropdown-' +count +'').find('option[value="'+input_penilai+'"]').prop('selected', true);
            $('#id_penilai-dropdown-'+count+'').trigger('change.select2');
            $('#judul-'+count+'').val(input_pelatihan);
            $('#jenis_pelatihan-' +count +'').find('option[value="'+input_jenis+'"]').prop('selected', true);
            $('#sifat-' +count +'').find('option[value="'+input_sifat+'"]').prop('selected', true);
            $('#alasan-'+count+'').val(input_alasan);
            $('#bulan_pelaksanaan-' +count +'').find('option[value="'+input_bulan+'"]').prop('selected', true);
            $('#id_vendor-dropdown-' +count +'').find('option[value="'+input_vendor+'"]').prop('selected', true);
            $('#id_vendor-dropdown-'+count+'').trigger('change.select2');
            if(input_vendor == 'other'){
                $('#cek_provider-'+count+'').show();
                $('#vendor_other-'+count+'').val(input_vendor_other);
            }else{
                $('#cek_provider-'+count+'').hide();
                $('#vendor_other-'+count+'').val('');
            }
            $('#biaya_fkt-'+count+'').val(input_biaya);
            $('#penginapan-' +count +'').find('option[value="'+input_penginapan+'"]').prop('selected', true);
            $('#transportasi-' +count +'').find('option[value="'+input_transportasi+'"]').prop('selected', true);
        });
        //convert currency
        var rupiah = document.getElementById('biaya_fkt-'+count+'');
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
        // return prefix == undefined ? rupiah : rupiah ? "Rp. " + rupiah : "";
        }

        // $("#periode-"+count+"").flatpickr({
        //     mode: "range",
        //     allowInput: true,
        //     altInput: false,
        //     altFormat: "d F, Y",
        //     dateFormat: "Y-m-d",
        // });  

        $("td.provider").prop('hidden',false);
        $("td.biaya").prop('hidden',false);
        $("td.akomodasi").prop('hidden',false);
        $(':input.vendor').prop('required',false);
        $(':input.nominal').prop('required',true);
        $(':input.inap').prop('required',true);
        $(':input.transport').prop('required',true);
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
        Array.from(document.getElementById("newlink2").querySelectorAll("tr")).forEach(function(e, t) {
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