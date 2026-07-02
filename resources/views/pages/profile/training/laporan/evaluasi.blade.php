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
<!-- App Css-->
<link href="/assets/css/app.min.css" rel="stylesheet" type="text/css" />
<!-- custom Css-->
<link href="/assets/css/custom.min.css" rel="stylesheet" type="text/css" />
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
    /* start desain training */
    #md_rating_dt_1 {
        opacity: 0;
        width: 0;
    }
    #md_rating_dt_2 {
        opacity: 0;
        width: 0;
    }
    #md_rating_dt_3 {
        opacity: 0;
        width: 0;
    }
    #md_rating_dt_4 {
        opacity: 0;
        width: 0;
    }
    #md_rating_dt_5 {
        opacity: 0;
        width: 0;
    }
    /* end desain training */
    /* start fasilitas alat penunjang */
    #md_rating_fap_1 {
        opacity: 0;
        width: 0;
    }
    #md_rating_fap_2 {
        opacity: 0;
        width: 0;
    }
    #md_rating_fap_3 {
        opacity: 0;
        width: 0;
    }
    #md_rating_fap_4 {
        opacity: 0;
        width: 0;
    }
    /* end fasilitas alat penunjang */
    /* start evaluasi trainer */
    #md_rating_et_1 {
        opacity: 0;
        width: 0;
    }
    #md_rating_et_2 {
        opacity: 0;
        width: 0;
    }
    #md_rating_et_3 {
        opacity: 0;
        width: 0;
    }
    #md_rating_et_4 {
        opacity: 0;
        width: 0;
    }
    #md_rating_et_5 {
        opacity: 0;
        width: 0;
    }
    #md_rating_et_6 {
        opacity: 0;
        width: 0;
    }
    #md_rating_et_7 {
        opacity: 0;
        width: 0;
    }
    #md_rating_et_8 {
        opacity: 0;
        width: 0;
    }
    #md_rating_et_9 {
        opacity: 0;
        width: 0;
    }
    #md_rating_et_10 {
        opacity: 0;
        width: 0;
    }
    #md_rating_et_11 {
        opacity: 0;
        width: 0;
    }
    #md_rating_et_12 {
        opacity: 0;
        width: 0;
    }
    /* end evaluasi trainer */
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
                                    <div class="card-body form-steps">                                        
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <!-- <button type="button" class="btn-close mb-4 float-end" data-bs-dismiss="modal" aria-label="Close"> </button> -->
                                                    <form id="form_evaluasi" class="vertical-navs-step needs-validation" action="{{route('profile.training.evaluasi.laporan.store')}}" method="POST">
                                                        @csrf
                                                        @method('put')
                                                        <!-- Info Validation -->
                                                        <div class="alert alert-secondary alert-dismissible alert-label-icon rounded-label fade show" role="alert">
                                                            <i class="ri-error-warning-line label-icon"></i><strong>Kotak input yang diberi tanda <span class="text-danger">*</span> wajib diisi.</strong>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-lg-4">
                                                                <!-- <h5>Formulir Penilaian Kebutuhan Training</h5> -->
                                                                <input type="hidden" name="id" id="id" value="{{$kode}}">
                                                                <img src="/assets/images/verification-img.png" alt="" style="width:100%; height: auto;">
                                                                <div class="nav mt-4 flex-column custom-nav nav-pills" role="tablist" aria-orientation="vertical">
                                                                    <button class="nav-link active" id="v-pills-bill-evaluasi-info1-tab" data-bs-toggle="pill" data-bs-target="#v-pills-bill-evaluasi-1" type="button" role="tab" aria-controls="v-pills-bill-evaluasi-1" aria-selected="true">
                                                                        <span class="step-title me-2">
                                                                            <i class="ri-close-circle-fill step-icon me-2"></i>
                                                                            Desain Training
                                                                        </span>
                                                                    </button>
                                                                    <button class="nav-link" id="v-pills-bill-evaluasi-info2-tab" data-bs-toggle="pill" data-bs-target="#v-pills-bill-evaluasi-2" type="button" role="tab" aria-controls="v-pills-bill-evaluasi-2" aria-selected="false">
                                                                        <span class="step-title me-2">
                                                                            <i class="ri-close-circle-fill step-icon me-2"></i>
                                                                            Fasilitas & Alat Penunjang
                                                                        </span>
                                                                    </button>
                                                                    <button class="nav-link" id="v-pills-bill-evaluasi-info3-tab" data-bs-toggle="pill" data-bs-target="#v-pills-bill-evaluasi-3" type="button" role="tab" aria-controls="v-pills-bill-evaluasi-3" aria-selected="false">
                                                                        <span class="step-title me-2">
                                                                            <i class="ri-close-circle-fill step-icon me-2"></i>
                                                                            Evaluasi Trainer
                                                                        </span>
                                                                    </button>
                                                                </div>
                                                                <!-- end nav -->
                                                            </div> <!-- end col-->
                                                            <div class="col-lg-8 mt-4">
                                                                <div class="px-lg-4">
                                                                    <div class="tab-content">
                                                                        <div class="tab-pane fade show active" id="v-pills-bill-evaluasi-1" role="tabpanel" aria-labelledby="v-pills-bill-evaluasi-info1-tab">
                                                                            <div>
                                                                                <h5>Desain Training</h5>
                                                                                <p class="text-muted">Fill in all the entries below</p>
                                                                            </div>
                            
                                                                            <div class="row g-2">
                                                                                <div class="col-xxl-6">
                                                                                    <!-- Small Tables -->
                                                                                    <table class="table table-sm">
                                                                                        <thead>
                                                                                            <tr>
                                                                                                <th scope="col">Tujuan dari training dapat tersampaikan dengan jelas.<span class="text-danger">*</span></th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody>
                                                                                            <tr>
                                                                                                {{-- <td><input type="number" class="form-control" id="level_skill_md_evaluasi-1" placeholder="(skala 1 - 5)" required=""></td> --}}
                                                                                                <td>
                                                                                                    <div dir="ltr">
                                                                                                        <div id="rater-onhover" class="align-middle"></div>
                                                                                                        <span id="dt_1" class="ratingnum badge bg-info align-middle ms-2"></span>
                                                                                                        <input type="text" id="md_rating_dt_1" name="md_rating_dt_1" value="{{$evaluasi->dt_1 ?? ''}}" required>
                                                                                                    </div>
                                                                                                </td>
                                                                                            </tr>                                                                                    
                                                                                        </tbody>
                                                                                    </table>
                                                                                    <table class="table table-sm">
                                                                                        <thead>
                                                                                            <tr>
                                                                                                <th scope="col">Isi dari materi training sangat sesuai dengan tujuan dari pelaksanaan training.<span class="text-danger">*</span></th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody>                                         
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <div dir="ltr">
                                                                                                        <div id="rater-onhover-dt2" class="align-middle"></div>
                                                                                                        <span id="dt_2" class="ratingnum-dt2 badge bg-info align-middle ms-2"></span>
                                                                                                        <input type="text" id="md_rating_dt_2" name="md_rating_dt_2" value="{{$evaluasi->dt_2 ?? ''}}" required>
                                                                                                    </div>
                                                                                                </td>
                                                                                            </tr>                                           
                                                                                        </tbody>
                                                                                    </table>
                                                                                    <table class="table table-sm">
                                                                                        <thead>
                                                                                            <tr>
                                                                                                <th scope="col">Proses pembelajaran di kelas serta aktivitas role play pada saat training dapat memudahkan saya dalam memahami materi training.<span class="text-danger">*</span></th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody>                                         
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <div dir="ltr">
                                                                                                        <div id="rater-onhover-dt3" class="align-middle"></div>
                                                                                                        <span id="dt_3" class="ratingnum-dt3 badge bg-info align-middle ms-2"></span>
                                                                                                        <input type="text" id="md_rating_dt_3" name="md_rating_dt_3" value="{{$evaluasi->dt_3 ?? ''}}" required>
                                                                                                    </div>
                                                                                                </td>
                                                                                            </tr>                                           
                                                                                        </tbody>
                                                                                    </table>
                                                                                    <table class="table table-sm">
                                                                                        <thead>
                                                                                            <tr>
                                                                                                <th scope="col">Topik training tersusun dan disajikan dengan baik.<span class="text-danger">*</span></th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody>                                         
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <div dir="ltr">
                                                                                                        <div id="rater-onhover-dt4" class="align-middle"></div>
                                                                                                        <span id="dt_4" class="ratingnum-dt4 badge bg-info align-middle ms-2"></span>
                                                                                                        <input type="text" id="md_rating_dt_4" name="md_rating_dt_4" value="{{$evaluasi->dt_4 ?? ''}}" required>
                                                                                                    </div>
                                                                                                </td>
                                                                                            </tr>                                           
                                                                                        </tbody>
                                                                                    </table>
                                                                                    <table class="table table-sm">
                                                                                        <thead>
                                                                                            <tr>
                                                                                                <th scope="col">Durasi pelaksanaan training sesuai dengan materi yang disampaikan.<span class="text-danger">*</span></th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody>                                         
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <div dir="ltr">
                                                                                                        <div id="rater-onhover-dt5" class="align-middle"></div>
                                                                                                        <span id="dt_5" class="ratingnum-dt5 badge bg-info align-middle ms-2"></span>
                                                                                                        <input type="text" id="md_rating_dt_5" name="md_rating_dt_5" value="{{$evaluasi->dt_5 ?? ''}}" required>
                                                                                                    </div>
                                                                                                </td>
                                                                                            </tr>                                           
                                                                                        </tbody>
                                                                                    </table>
                                                                                </div>
                                                                            </div><!--end row-->
                            
                                                                            <div class="d-flex align-items-start gap-3 mt-4">
                                                                                <button type="button" id="cek_pill-evaluasi-2" class="btn btn-success btn-label right ms-auto nexttab nexttab" data-nexttab="v-pills-bill-evaluasi-info2-tab"><i class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i>Next</button>
                                                                            </div>
                                                                        </div>                                                                                    
                                                                        <div class="tab-pane fade" id="v-pills-bill-evaluasi-2" role="tabpanel" aria-labelledby="v-pills-bill-evaluasi-info2-tab">
                                                                            <div>
                                                                                <h5>Fasilitas & Alat Penunjang</h5>
                                                                                <p class="text-muted">Fill in all the entries below</p>
                                                                            </div>
                            
                                                                            <div class="row g-2">
                                                                                <div class="col-xxl-6">
                                                                                    <!-- Small Tables -->
                                                                                    <table class="table table-sm">
                                                                                        <thead>
                                                                                            <tr>
                                                                                                <th scope="col">Penyampaian training dilengkapi dengan alat peraga yang cukup memadai.<span class="text-danger">*</span></th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody>                                         
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <div dir="ltr">
                                                                                                        <div id="rater-onhover-fap" class="align-middle"></div>
                                                                                                        <span id="fap_1" class="ratingnum-fap badge bg-info align-middle ms-2"></span>
                                                                                                        <input type="text" id="md_rating_fap_1" name="md_rating_fap_1" value="{{$evaluasi->fap_1 ?? ''}}" required>
                                                                                                    </div>
                                                                                                </td>
                                                                                            </tr>                                           
                                                                                        </tbody>
                                                                                    </table>
                                                                                    <table class="table table-sm">
                                                                                        <thead>
                                                                                            <tr>
                                                                                                <th scope="col">Materi training disampaikan dan divisualisasikan dalam powerpoint yang menarik.<span class="text-danger">*</span></th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody>                                         
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <div dir="ltr">
                                                                                                        <div id="rater-onhover-fap2" class="align-middle"></div>
                                                                                                        <span id="fap_2" class="ratingnum-fap2 badge bg-info align-middle ms-2"></span>
                                                                                                        <input type="text" id="md_rating_fap_2" name="md_rating_fap_2" value="{{$evaluasi->fap_2 ?? ''}}" required>
                                                                                                    </div>
                                                                                                </td>
                                                                                            </tr>                                           
                                                                                        </tbody>
                                                                                    </table>
                                                                                    <table class="table table-sm">
                                                                                        <thead>
                                                                                            <tr>
                                                                                                <th scope="col">Proyektor dan audio yang dipergunakan pada saat training berfungsi sebagaimana mestinya.<span class="text-danger">*</span></th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody>                                         
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <div dir="ltr">
                                                                                                        <div id="rater-onhover-fap3" class="align-middle"></div>
                                                                                                        <span id="fap_3" class="ratingnum-fap3 badge bg-info align-middle ms-2"></span>
                                                                                                        <input type="text" id="md_rating_fap_3" name="md_rating_fap_3" value="{{$evaluasi->fap_3 ?? ''}}" required>
                                                                                                    </div>
                                                                                                </td>
                                                                                            </tr>                                           
                                                                                        </tbody>
                                                                                    </table>
                                                                                    <table class="table table-sm">
                                                                                        <thead>
                                                                                            <tr>
                                                                                                <th scope="col">Pengaturan venue dan tempat duduk para peserta sangat strategis.<span class="text-danger">*</span></th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody>                                         
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <div dir="ltr">
                                                                                                        <div id="rater-onhover-fap4" class="align-middle"></div>
                                                                                                        <span id="fap_4" class="ratingnum-fap4 badge bg-info align-middle ms-2"></span>
                                                                                                        <input type="text" id="md_rating_fap_4" name="md_rating_fap_4" value="{{$evaluasi->fap_4 ?? ''}}" required>
                                                                                                    </div>
                                                                                                </td>
                                                                                            </tr>                                           
                                                                                        </tbody>
                                                                                    </table>
                                                                                </div>
                                                                            </div><!--end row-->
                            
                                                                            <div class="d-flex align-items-start gap-3 mt-4">
                                                                                <button type="button" class="btn btn-light btn-label previestab" data-previous="v-pills-bill-evaluasi-info1-tab"><i class="ri-arrow-left-line label-icon align-middle fs-16 me-2"></i> Back</button>
                                                                                <button type="button" id="cek_pill-evaluasi-3" class="btn btn-success btn-label right ms-auto nexttab nexttab" data-nexttab="v-pills-bill-evaluasi-info3-tab"><i class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i>Next</button>
                                                                            </div>
                                                                        </div>
                                                                        <div class="tab-pane fade" id="v-pills-bill-evaluasi-3" role="tabpanel" aria-labelledby="v-pills-bill-evaluasi-info3-tab">
                                                                            <div>
                                                                                <h5>Evaluasi Trainer</h5>
                                                                                <p class="text-muted">Fill in all the entries below</p>
                                                                            </div>
                            
                                                                            <div class="row g-2">
                                                                                <div class="col-xxl-6">
                                                                                    <!-- Small Tables -->
                                                                                    <!-- trainer pertama -->
                                                                                    <table class="table table-sm">
                                                                                        <thead>
                                                                                            <tr>
                                                                                                <th scope="col">Nama Trainer Pertama.<span class="text-danger">*</span></th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody>                                         
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <input type="text" class="form-control" id="trainer_1" name="trainer_1" placeholder="Jawaban Anda" value="{{$evaluasi->trainer_1 ?? ''}}" required>
                                                                                                </td>
                                                                                            </tr>                                           
                                                                                        </tbody>
                                                                                    </table>
                                                                                    <table class="table table-sm">
                                                                                        <thead>
                                                                                            <tr>
                                                                                                <th scope="col">Trainer dapat menciptakan suasana belajar yang kondusif dan interaktif.<span class="text-danger">*</span></th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody>                                         
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <div dir="ltr">
                                                                                                        <div id="rater-onhover-et" class="align-middle"></div>
                                                                                                        <span id="et_1" class="ratingnum-et badge bg-info align-middle ms-2"></span>
                                                                                                        <input type="text" id="md_rating_et_1" name="md_rating_et_1" value="{{$evaluasi->et_1 ?? ''}}" required>
                                                                                                    </div>
                                                                                                </td>
                                                                                            </tr>                                           
                                                                                        </tbody>
                                                                                    </table>
                                                                                    <table class="table table-sm">
                                                                                        <thead>
                                                                                            <tr>
                                                                                                <th scope="col">Trainer menunjukkan kepercayaan diri dalam menyampaikan materi yang dikuasainya.<span class="text-danger">*</span></th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody>                                         
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <div dir="ltr">
                                                                                                        <div id="rater-onhover-et2" class="align-middle"></div>
                                                                                                        <span id="et_2" class="ratingnum-et2 badge bg-info align-middle ms-2"></span>
                                                                                                        <input type="text" id="md_rating_et_2" name="md_rating_et_2" value="{{$evaluasi->et_2 ?? ''}}" required>
                                                                                                    </div>
                                                                                                </td>
                                                                                            </tr>                                           
                                                                                        </tbody>
                                                                                    </table>
                                                                                    <table class="table table-sm">
                                                                                        <thead>
                                                                                            <tr>
                                                                                                <th scope="col">Trainer dapat menstimulus critical thinking dan motivasi belajar dari para peserta.<span class="text-danger">*</span></th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody>                                         
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <div dir="ltr">
                                                                                                        <div id="rater-onhover-et3" class="align-middle"></div>
                                                                                                        <span id="et_3" class="ratingnum-et3 badge bg-info align-middle ms-2"></span>
                                                                                                        <input type="text" id="md_rating_et_3" name="md_rating_et_3" value="{{$evaluasi->et_3 ?? ''}}" required>
                                                                                                    </div>
                                                                                                </td>
                                                                                            </tr>                                           
                                                                                        </tbody>
                                                                                    </table>
                                                                                    <table class="table table-sm">
                                                                                        <thead>
                                                                                            <tr>
                                                                                                <th scope="col">Trainer dapat menghandle dan memastikan suasana kelas yang nyaman dan profesional.<span class="text-danger">*</span></th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody>                                         
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <div dir="ltr">
                                                                                                        <div id="rater-onhover-et4" class="align-middle"></div>
                                                                                                        <span id="et_4" class="ratingnum-et4 badge bg-info align-middle ms-2"></span>
                                                                                                        <input type="text" id="md_rating_et_4" name="md_rating_et_4" value="{{$evaluasi->et_4 ?? ''}}" required>
                                                                                                    </div>
                                                                                                </td>
                                                                                            </tr>                                           
                                                                                        </tbody>
                                                                                    </table>
                                                                                    <!-- trainer kedua -->
                                                                                    <table class="table table-sm">
                                                                                        <thead>
                                                                                            <tr>
                                                                                                <th scope="col">Nama Trainer Kedua.</th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody>                                         
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <input type="text" class="form-control" id="trainer_2" name="trainer_2" placeholder="Jawaban Anda" value="{{$evaluasi->trainer_2 ?? ''}}">
                                                                                                </td>
                                                                                            </tr>                                           
                                                                                        </tbody>
                                                                                    </table>
                                                                                    <table class="table table-sm">
                                                                                        <thead>
                                                                                            <tr>
                                                                                                <th scope="col">Trainer dapat menciptakan suasana belajar yang kondusif dan interaktif.</th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody>                                         
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <div dir="ltr">
                                                                                                        <div id="rater-onhover-et5" class="align-middle"></div>
                                                                                                        <span id="et_5" class="ratingnum-et5 badge bg-info align-middle ms-2"></span>
                                                                                                        <input type="text" id="md_rating_et_5" name="md_rating_et_5" value="{{$evaluasi->et_5 ?? ''}}">
                                                                                                    </div>
                                                                                                </td>
                                                                                            </tr>                                           
                                                                                        </tbody>
                                                                                    </table>
                                                                                    <table class="table table-sm">
                                                                                        <thead>
                                                                                            <tr>
                                                                                                <th scope="col">Trainer menunjukkan kepercayaan diri dalam menyampaikan materi yang dikuasainya.</th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody>                                         
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <div dir="ltr">
                                                                                                        <div id="rater-onhover-et6" class="align-middle"></div>
                                                                                                        <span id="et_6" class="ratingnum-et6 badge bg-info align-middle ms-2"></span>
                                                                                                        <input type="text" id="md_rating_et_6" name="md_rating_et_6" value="{{$evaluasi->et_6 ?? ''}}">
                                                                                                    </div>
                                                                                                </td>
                                                                                            </tr>                                           
                                                                                        </tbody>
                                                                                    </table>
                                                                                    <table class="table table-sm">
                                                                                        <thead>
                                                                                            <tr>
                                                                                                <th scope="col">Trainer dapat menstimulus critical thinking dan motivasi belajar dari para peserta.</th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody>                                         
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <div dir="ltr">
                                                                                                        <div id="rater-onhover-et7" class="align-middle"></div>
                                                                                                        <span id="et_7" class="ratingnum-et7 badge bg-info align-middle ms-2"></span>
                                                                                                        <input type="text" id="md_rating_et_7" name="md_rating_et_7" value="{{$evaluasi->et_7 ?? ''}}">
                                                                                                    </div>
                                                                                                </td>
                                                                                            </tr>                                           
                                                                                        </tbody>
                                                                                    </table>
                                                                                    <table class="table table-sm">
                                                                                        <thead>
                                                                                            <tr>
                                                                                                <th scope="col">Trainer dapat menghandle dan memastikan suasana kelas yang nyaman dan profesional.</th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody>                                         
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <div dir="ltr">
                                                                                                        <div id="rater-onhover-et8" class="align-middle"></div>
                                                                                                        <span id="et_8" class="ratingnum-et8 badge bg-info align-middle ms-2"></span>
                                                                                                        <input type="text" id="md_rating_et_8" name="md_rating_et_8" value="{{$evaluasi->et_8 ?? ''}}">
                                                                                                    </div>
                                                                                                </td>
                                                                                            </tr>                                           
                                                                                        </tbody>
                                                                                    </table>
                                                                                    <!-- trainer ketiga -->
                                                                                    <table class="table table-sm">
                                                                                        <thead>
                                                                                            <tr>
                                                                                                <th scope="col">Nama Trainer Ketiga.</th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody>                                         
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <input type="text" class="form-control" id="trainer_3" name="trainer_3" placeholder="Jawaban Anda" value="{{$evaluasi->trainer_3 ?? ''}}">
                                                                                                </td>
                                                                                            </tr>                                           
                                                                                        </tbody>
                                                                                    </table>
                                                                                    <table class="table table-sm">
                                                                                        <thead>
                                                                                            <tr>
                                                                                                <th scope="col">Trainer dapat menciptakan suasana belajar yang kondusif dan interaktif.</th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody>                                         
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <div dir="ltr">
                                                                                                        <div id="rater-onhover-et9" class="align-middle"></div>
                                                                                                        <span id="et_9" class="ratingnum-et9 badge bg-info align-middle ms-2"></span>
                                                                                                        <input type="text" id="md_rating_et_9" name="md_rating_et_9" value="{{$evaluasi->et_9 ?? ''}}">
                                                                                                    </div>
                                                                                                </td>
                                                                                            </tr>                                           
                                                                                        </tbody>
                                                                                    </table>
                                                                                    <table class="table table-sm">
                                                                                        <thead>
                                                                                            <tr>
                                                                                                <th scope="col">Trainer menunjukkan kepercayaan diri dalam menyampaikan materi yang dikuasainya.</th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody>                                         
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <div dir="ltr">
                                                                                                        <div id="rater-onhover-et10" class="align-middle"></div>
                                                                                                        <span id="et_10" class="ratingnum-et10 badge bg-info align-middle ms-2"></span>
                                                                                                        <input type="text" id="md_rating_et_10" name="md_rating_et_10" value="{{$evaluasi->et_10 ?? ''}}">
                                                                                                    </div>
                                                                                                </td>
                                                                                            </tr>                                           
                                                                                        </tbody>
                                                                                    </table>
                                                                                    <table class="table table-sm">
                                                                                        <thead>
                                                                                            <tr>
                                                                                                <th scope="col">Trainer dapat menstimulus critical thinking dan motivasi belajar dari para peserta.</th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody>                                         
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <div dir="ltr">
                                                                                                        <div id="rater-onhover-et11" class="align-middle"></div>
                                                                                                        <span id="et_11" class="ratingnum-et11 badge bg-info align-middle ms-2"></span>
                                                                                                        <input type="text" id="md_rating_et_11" name="md_rating_et_11" value="{{$evaluasi->et_11 ?? ''}}">
                                                                                                    </div>
                                                                                                </td>
                                                                                            </tr>                                           
                                                                                        </tbody>
                                                                                    </table>
                                                                                    <table class="table table-sm">
                                                                                        <thead>
                                                                                            <tr>
                                                                                                <th scope="col">Trainer dapat menghandle dan memastikan suasana kelas yang nyaman dan profesional.</th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody>                                         
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <div dir="ltr">
                                                                                                        <div id="rater-onhover-et12" class="align-middle"></div>
                                                                                                        <span id="et_12" class="ratingnum-et12 badge bg-info align-middle ms-2"></span>
                                                                                                        <input type="text" id="md_rating_et_12" name="md_rating_et_12" value="{{$evaluasi->et_12 ?? ''}}">
                                                                                                    </div>
                                                                                                </td>
                                                                                            </tr>                                           
                                                                                        </tbody>
                                                                                    </table>
                                                                                </div>
                                                                            </div><!--end row-->
                            
                                                                            <div class="d-flex align-items-start gap-3 mt-4">
                                                                                <button type="button" class="btn btn-light btn-label previestab" data-previous="v-pills-bill-evaluasi-info2-tab"><i class="ri-arrow-left-line label-icon align-middle fs-16 me-2"></i> Back</button>
                                                                                <button type="submit" class="btn btn-primary btn-label right ms-auto"><i class="ri-save-line label-icon align-middle fs-16 ms-2"></i>Save</button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <!-- end tab content -->
                                                                </div>
                                                            </div>
                                                            <!-- end col -->
                                                        </div>   
                                                    </form>
                                                </div>
                                                <!-- <div class="vr"></div> -->
                                            </div>
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
@endsection

@section('script')
<!-- Select2 -->
<script src="/assets/libs/adminlte/select2/js/4.1.0/select2.min.js"></script>
<script src="/assets/js/pages/select2.init.js"></script>
<!-- profile-setting init js -->
<script src="/assets/js/pages/profile-setting.init.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.js"></script>
<script src="/assets/libs/simplebar/simplebar.min.js"></script>
<script src="/assets/libs/node-waves/waves.min.js"></script>
<!-- form wizard init -->
<script src="/assets/js/pages/form-wizard.init.js"></script>
<!-- Sweetalert -->
<script src="{{asset('assets/libs/sweetalert2/11/sweetalert2.min.js')}}"></script>
<!-- rater-js plugin -->
<script src="/assets/libs/rater-js/index.js"></script>
<!-- rating init -->
{{-- <script src="/assets/js/pages/rating.init.js"></script> --}}
@endsection
@section('javascript')
<script>
    var evaluasi = {{ Js::from($evaluasi) }};
    if(evaluasi){
        //desain training
        var dt_1 = evaluasi['dt_1'];
        var dt_2 = evaluasi['dt_2'];
        var dt_3 = evaluasi['dt_3'];
        var dt_4 = evaluasi['dt_4'];
        var dt_5 = evaluasi['dt_5'];
        //fasilitas alat penunjang
        var fap_1 = evaluasi['fap_1'];
        var fap_2 = evaluasi['fap_2'];
        var fap_3 = evaluasi['fap_3'];
        var fap_4 = evaluasi['fap_4'];
        //evaluasi training
        var et_1 = evaluasi['et_1'];
        var et_2 = evaluasi['et_2'];
        var et_3 = evaluasi['et_3'];
        var et_4 = evaluasi['et_4'];
        var et_5 = evaluasi['et_5'];
        var et_6 = evaluasi['et_6'];
        var et_7 = evaluasi['et_7'];
        var et_8 = evaluasi['et_8'];
        var et_9 = evaluasi['et_9'];
        var et_10 = evaluasi['et_10'];
        var et_11 = evaluasi['et_11'];
        var et_12 = evaluasi['et_12'];
    }else{
        //desain training
        var dt_1 = 0;
        var dt_2 = 0;
        var dt_3 = 0;
        var dt_4 = 0;
        var dt_5 = 0;
        //fasilitas alat penunjang
        var fap_1 = 0;
        var fap_2 = 0;
        var fap_3 = 0;
        var fap_4 = 0;
        //evaluasi training
        var et_1 = 0;
        var et_2 = 0;
        var et_3 = 0;
        var et_4 = 0;
        var et_5 = 0;
        var et_6 = 0;
        var et_7 = 0;
        var et_8 = 0;
        var et_9 = 0;
        var et_10 = 0;
        var et_11 = 0;
        var et_12 = 0;
    }
    document.querySelector("#rater-onhover") && (starRatinghover = raterJs({
    starSize: 22,
    rating: dt_1,
    element: document.querySelector("#rater-onhover"),
    rateCallback: function(e, t) {
        this.setRating(e), t()
    },
    onHover: function(e, t) {
        document.querySelector(".ratingnum").textContent = e
    },
    onLeave: function(e, t) {
        document.querySelector(".ratingnum").textContent = t
    }
})), document.querySelector("#rater-onhover-dt2") && (starRatinghover = raterJs({
    starSize: 22,
    rating: dt_2,
    element: document.querySelector("#rater-onhover-dt2"),
    rateCallback: function(e, t) {
        this.setRating(e), t()
    },
    onHover: function(e, t) {
        document.querySelector(".ratingnum-dt2").textContent = e
    },
    onLeave: function(e, t) {
        document.querySelector(".ratingnum-dt2").textContent = t
    }
})), document.querySelector("#rater-onhover-dt3") && (starRatinghover = raterJs({
    starSize: 22,
    rating: dt_3,
    element: document.querySelector("#rater-onhover-dt3"),
    rateCallback: function(e, t) {
        this.setRating(e), t()
    },
    onHover: function(e, t) {
        document.querySelector(".ratingnum-dt3").textContent = e
    },
    onLeave: function(e, t) {
        document.querySelector(".ratingnum-dt3").textContent = t
    }
})), document.querySelector("#rater-onhover-dt4") && (starRatinghover = raterJs({
    starSize: 22,
    rating: dt_4,
    element: document.querySelector("#rater-onhover-dt4"),
    rateCallback: function(e, t) {
        this.setRating(e), t()
    },
    onHover: function(e, t) {
        document.querySelector(".ratingnum-dt4").textContent = e
    },
    onLeave: function(e, t) {
        document.querySelector(".ratingnum-dt4").textContent = t
    }
})), document.querySelector("#rater-onhover-dt5") && (starRatinghover = raterJs({
    starSize: 22,
    rating: dt_5,
    element: document.querySelector("#rater-onhover-dt5"),
    rateCallback: function(e, t) {
        this.setRating(e), t()
    },
    onHover: function(e, t) {
        document.querySelector(".ratingnum-dt5").textContent = e
    },
    onLeave: function(e, t) {
        document.querySelector(".ratingnum-dt5").textContent = t
    }
})), document.querySelector("#rater-onhover-fap") && (starRatinghover = raterJs({
    starSize: 22,
    rating: fap_1,
    element: document.querySelector("#rater-onhover-fap"),
    rateCallback: function(e, t) {
        this.setRating(e), t()
    },
    onHover: function(e, t) {
        document.querySelector(".ratingnum-fap").textContent = e
    },
    onLeave: function(e, t) {
        document.querySelector(".ratingnum-fap").textContent = t
    }
})), document.querySelector("#rater-onhover-fap2") && (starRatinghover = raterJs({
    starSize: 22,
    rating: fap_2,
    element: document.querySelector("#rater-onhover-fap2"),
    rateCallback: function(e, t) {
        this.setRating(e), t()
    },
    onHover: function(e, t) {
        document.querySelector(".ratingnum-fap2").textContent = e
    },
    onLeave: function(e, t) {
        document.querySelector(".ratingnum-fap2").textContent = t
    }
})), document.querySelector("#rater-onhover-fap3") && (starRatinghover = raterJs({
    starSize: 22,
    rating: fap_3,
    element: document.querySelector("#rater-onhover-fap3"),
    rateCallback: function(e, t) {
        this.setRating(e), t()
    },
    onHover: function(e, t) {
        document.querySelector(".ratingnum-fap3").textContent = e
    },
    onLeave: function(e, t) {
        document.querySelector(".ratingnum-fap3").textContent = t
    }
})), document.querySelector("#rater-onhover-fap4") && (starRatinghover = raterJs({
    starSize: 22,
    rating: fap_4,
    element: document.querySelector("#rater-onhover-fap4"),
    rateCallback: function(e, t) {
        this.setRating(e), t()
    },
    onHover: function(e, t) {
        document.querySelector(".ratingnum-fap4").textContent = e
    },
    onLeave: function(e, t) {
        document.querySelector(".ratingnum-fap4").textContent = t
    }
})), document.querySelector("#rater-onhover-et") && (starRatinghover = raterJs({
    starSize: 22,
    rating: et_1,
    element: document.querySelector("#rater-onhover-et"),
    rateCallback: function(e, t) {
        this.setRating(e), t()
    },
    onHover: function(e, t) {
        document.querySelector(".ratingnum-et").textContent = e
    },
    onLeave: function(e, t) {
        document.querySelector(".ratingnum-et").textContent = t
    }
})), document.querySelector("#rater-onhover-et2") && (starRatinghover = raterJs({
    starSize: 22,
    rating: et_2,
    element: document.querySelector("#rater-onhover-et2"),
    rateCallback: function(e, t) {
        this.setRating(e), t()
    },
    onHover: function(e, t) {
        document.querySelector(".ratingnum-et2").textContent = e
    },
    onLeave: function(e, t) {
        document.querySelector(".ratingnum-et2").textContent = t
    }
})), document.querySelector("#rater-onhover-et3") && (starRatinghover = raterJs({
    starSize: 22,
    rating: et_3,
    element: document.querySelector("#rater-onhover-et3"),
    rateCallback: function(e, t) {
        this.setRating(e), t()
    },
    onHover: function(e, t) {
        document.querySelector(".ratingnum-et3").textContent = e
    },
    onLeave: function(e, t) {
        document.querySelector(".ratingnum-et3").textContent = t
    }
})), document.querySelector("#rater-onhover-et4") && (starRatinghover = raterJs({
    starSize: 22,
    rating: et_4,
    element: document.querySelector("#rater-onhover-et4"),
    rateCallback: function(e, t) {
        this.setRating(e), t()
    },
    onHover: function(e, t) {
        document.querySelector(".ratingnum-et4").textContent = e
    },
    onLeave: function(e, t) {
        document.querySelector(".ratingnum-et4").textContent = t
    }
})), document.querySelector("#rater-onhover-et5") && (starRatinghover = raterJs({
    starSize: 22,
    rating: et_5,
    element: document.querySelector("#rater-onhover-et5"),
    rateCallback: function(e, t) {
        this.setRating(e), t()
    },
    onHover: function(e, t) {
        document.querySelector(".ratingnum-et5").textContent = e
    },
    onLeave: function(e, t) {
        document.querySelector(".ratingnum-et5").textContent = t
    }
})), document.querySelector("#rater-onhover-et6") && (starRatinghover = raterJs({
    starSize: 22,
    rating: et_6,
    element: document.querySelector("#rater-onhover-et6"),
    rateCallback: function(e, t) {
        this.setRating(e), t()
    },
    onHover: function(e, t) {
        document.querySelector(".ratingnum-et6").textContent = e
    },
    onLeave: function(e, t) {
        document.querySelector(".ratingnum-et6").textContent = t
    }
})), document.querySelector("#rater-onhover-et7") && (starRatinghover = raterJs({
    starSize: 22,
    rating: et_7,
    element: document.querySelector("#rater-onhover-et7"),
    rateCallback: function(e, t) {
        this.setRating(e), t()
    },
    onHover: function(e, t) {
        document.querySelector(".ratingnum-et7").textContent = e
    },
    onLeave: function(e, t) {
        document.querySelector(".ratingnum-et7").textContent = t
    }
})), document.querySelector("#rater-onhover-et8") && (starRatinghover = raterJs({
    starSize: 22,
    rating: et_8,
    element: document.querySelector("#rater-onhover-et8"),
    rateCallback: function(e, t) {
        this.setRating(e), t()
    },
    onHover: function(e, t) {
        document.querySelector(".ratingnum-et8").textContent = e
    },
    onLeave: function(e, t) {
        document.querySelector(".ratingnum-et8").textContent = t
    }
})), document.querySelector("#rater-onhover-et9") && (starRatinghover = raterJs({
    starSize: 22,
    rating: et_9,
    element: document.querySelector("#rater-onhover-et9"),
    rateCallback: function(e, t) {
        this.setRating(e), t()
    },
    onHover: function(e, t) {
        document.querySelector(".ratingnum-et9").textContent = e
    },
    onLeave: function(e, t) {
        document.querySelector(".ratingnum-et9").textContent = t
    }
})), document.querySelector("#rater-onhover-et10") && (starRatinghover = raterJs({
    starSize: 22,
    rating: et_10,
    element: document.querySelector("#rater-onhover-et10"),
    rateCallback: function(e, t) {
        this.setRating(e), t()
    },
    onHover: function(e, t) {
        document.querySelector(".ratingnum-et10").textContent = e
    },
    onLeave: function(e, t) {
        document.querySelector(".ratingnum-et10").textContent = t
    }
})), document.querySelector("#rater-onhover-et11") && (starRatinghover = raterJs({
    starSize: 22,
    rating: et_11,
    element: document.querySelector("#rater-onhover-et11"),
    rateCallback: function(e, t) {
        this.setRating(e), t()
    },
    onHover: function(e, t) {
        document.querySelector(".ratingnum-et11").textContent = e
    },
    onLeave: function(e, t) {
        document.querySelector(".ratingnum-et11").textContent = t
    }
})), document.querySelector("#rater-onhover-et12") && (starRatinghover = raterJs({
    starSize: 22,
    rating: et_12,
    element: document.querySelector("#rater-onhover-et12"),
    rateCallback: function(e, t) {
        this.setRating(e), t()
    },
    onHover: function(e, t) {
        document.querySelector(".ratingnum-et12").textContent = e
    },
    onLeave: function(e, t) {
        document.querySelector(".ratingnum-et12").textContent = t
    }
}));
</script>
<script>
    // start desain training
    $("#rater-onhover").click(function() {
        var dt_1 = $('#dt_1').text();
        $('#md_rating_dt_1').val(dt_1);
    });
    $("#rater-onhover-dt2").click(function() {
        var dt_2 = $('#dt_2').text();
        $('#md_rating_dt_2').val(dt_2);
    });
    $("#rater-onhover-dt3").click(function() {
        var dt_3 = $('#dt_3').text();
        $('#md_rating_dt_3').val(dt_3);
    });
    $("#rater-onhover-dt4").click(function() {
        var dt_4 = $('#dt_4').text();
        $('#md_rating_dt_4').val(dt_4);
    });
    $("#rater-onhover-dt5").click(function() {
        var dt_5 = $('#dt_5').text();
        $('#md_rating_dt_5').val(dt_5);
    });
    //end desain training
    //start fasilitas alat penunjang
    $("#rater-onhover-fap").click(function() {
        var fap_1 = $('#fap_1').text();
        $('#md_rating_fap_1').val(fap_1);
    });
    $("#rater-onhover-fap2").click(function() {
        var fap_2 = $('#fap_2').text();
        $('#md_rating_fap_2').val(fap_2);
    });
    $("#rater-onhover-fap3").click(function() {
        var fap_3 = $('#fap_3').text();
        $('#md_rating_fap_3').val(fap_3);
    });
    $("#rater-onhover-fap4").click(function() {
        var fap_4 = $('#fap_4').text();
        $('#md_rating_fap_4').val(fap_4);
    });
    //end fasilitas alat penunjang
    //start evaluasi trainer
    $("#rater-onhover-et").click(function() {
        var et_1 = $('#et_1').text();
        $('#md_rating_et_1').val(et_1);
    });
    $("#rater-onhover-et2").click(function() {
        var et_2 = $('#et_2').text();
        $('#md_rating_et_2').val(et_2);
    });
    $("#rater-onhover-et3").click(function() {
        var et_3 = $('#et_3').text();
        $('#md_rating_et_3').val(et_3);
    });
    $("#rater-onhover-et4").click(function() {
        var et_4 = $('#et_4').text();
        $('#md_rating_et_4').val(et_4);
    });
    $("#rater-onhover-et5").click(function() {
        var et_5 = $('#et_5').text();
        $('#md_rating_et_5').val(et_5);
    });
    $("#rater-onhover-et6").click(function() {
        var et_6 = $('#et_6').text();
        $('#md_rating_et_6').val(et_6);
    });
    $("#rater-onhover-et7").click(function() {
        var et_7 = $('#et_7').text();
        $('#md_rating_et_7').val(et_7);
    });
    $("#rater-onhover-et8").click(function() {
        var et_8 = $('#et_8').text();
        $('#md_rating_et_8').val(et_8);
    });
    $("#rater-onhover-et9").click(function() {
        var et_9 = $('#et_9').text();
        $('#md_rating_et_9').val(et_9);
    });
    $("#rater-onhover-et10").click(function() {
        var et_10 = $('#et_10').text();
        $('#md_rating_et_10').val(et_10);
    });
    $("#rater-onhover-et11").click(function() {
        var et_11 = $('#et_11').text();
        $('#md_rating_et_11').val(et_11);
    });
    $("#rater-onhover-et12").click(function() {
        var et_12 = $('#et_12').text();
        $('#md_rating_et_12').val(et_12);
    });
    //end evaluasi trainer
</script>
<script>
    $("#tgl_laporan").flatpickr({
        allowInput: true,
        altInput: true,
        altFormat: "d F, Y",
        dateFormat: "Y-m-d",
    });

    $(".select2").select2();
</script>
<script>
    //submit update event
    $("#form_evaluasi").submit(function(e) {
        e.preventDefault();

        swalert = Swal.fire({
          title: 'Loading!',
          didOpen: () => {
            Swal.showLoading()
          }
        });

        const formData = new FormData(this);

        $.ajax({
          url: $(this).attr("action"),
          method: "POST",
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          data: formData,
          processData: false,
          contentType: false,
          success: function(response) {
            swalert.hideLoading();
            swalert.update({
              title: "Success",
              text: response.message,
              icon: "success",
              buttonsStyling: false,
              confirmButtonText: "Ok, got it!",
              customClass: {
                popup: 'swal2-noanimation',
                confirmButton: "btn btn-primary"
              }
            });
            swalert.then(() => window.open(window.location, '_self').close())
          },
          error: function(xhr, status, error) {
            console.log({
              xhr,
              status,
              error
            });
            handleErrorResponse(xhr.responseJSON);
          }
        });
      });

    function handleErrorResponse(responseJson) {
    let errorMessage = '';

    if (responseJson.message) {
        errorMessage += `<p class="text-danger">${responseJson.message}</p>`;
    }

    if (responseJson.errors) {
        for (const fieldName in responseJson.errors) {
        errorMessage += `<p class="text-danger">${responseJson.errors[fieldName][0]}</p>`;
        }
    }

    if (responseJson.responseText) {
        errorMessage += `<p class="text-danger">${responseJson.responseText}</p>`;

    }

    if (errorMessage === '') {
        errorMessage += '<p class="text-danger">An error occurred.</p>';
    }

    // Display error message using SweetAlert
    swalert.update({
        title: 'Error',
        html: errorMessage,
        icon: 'error',
        buttonsStyling: false,
        confirmButtonText: 'Ok',
        customClass: {
        confirmButton: 'btn btn-primary'
        }
    });
    }
</script>
<script>
    function uploadSertifikatValidation(){
        var upload = document.getElementById('file_sertifikat');
        var pathUpload= upload.value;

        // tipe file yang diizinkan
        var allowedExtensions = /(\.pdf|\.PDF)$/i;

        if (!allowedExtensions.exec(pathUpload)) {
            document.getElementById(
                'info-validation').innerHTML =
                '<p class="text-muted fs-12">Maaf hanya menerima file document yang bertipe .pdf | .PDF</p>';
            $('#modalTraining').modal('toggle');
            $('#validationmodal').modal('show');
            upload.value = '';
            return false;
        }
        else
        {             
            // dijalankan
        }      
    }

    function clearSertifikatUpload(){
        var upload = document.getElementById('file_sertifikat');
        upload.value = '';
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