@extends('layouts.master')
@section('link')
<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- Select2-->
<link rel="stylesheet" href="/assets/libs/adminlte/select2/css/4.1.0/select2.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css"/>
<style type="text/css">
    /* body{
        background: #f7fbf8; 
    } */
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
                            <a class="nav-link fs-14 {{ request()->is('profile/medical') ? '' : 'active' }}" href="{{route('profile.medical')}}">
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
                        @endcan --}}
                        <!-- <li class="nav-item">
                            <a class="nav-link fs-14" data-bs-toggle="tab" href="#projects" role="tab">
                                <i class="ri-price-tag-line d-inline-block d-md-none"></i> <span class="d-none d-md-inline-block">Allowances</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link fs-14" data-bs-toggle="tab" href="#documents" role="tab">
                                <i class="ri-folder-4-line d-inline-block d-md-none"></i> <span class="d-none d-md-inline-block">Internal Rules</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link fs-14" id="calendarButton" data-bs-toggle="tab" href="#test" role="tab">
                                <i class="ri-folder-4-line d-inline-block d-md-none"></i> <span class="d-none d-md-inline-block">Calendar</span>
                            </a>
                        </li> -->
                    {{-- </ul> --}}
                    <!-- <div class="flex-shrink-0">
                        <a href="pages-profile-settings.html" class="btn btn-success"><i class="ri-edit-box-line align-bottom"></i> Edit Profile</a>
                    </div> -->
                </div>
                <!-- Navbar -->
                @if(!empty($medical))
                <div class="row pt-4">
                    <div class="col-lg-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-3">Medical Checkup</h5>
                                <select class="form-control select2" name="date_mcu" id="date_mcu">
                                    @foreach($arr_tanggal as $key_tgl => $tanggal)
                                        @if(!empty($latest_mcu))
                                            @if($latest_mcu == $tanggal)
                                                <option value="{{$key_tgl}}" selected>{{$tanggal}}</option>
                                            @else           
                                                <option value="{{$key_tgl}}">{{$tanggal}}</option>
                                            @endif  
                                        @else         
                                            <option value="{{$key_tgl}}">{{$tanggal}}</option> 
                                        @endif
                                    @endforeach                             
                                </select>
                                <div id="profile-mcu"> 
                                    <div data-simplebar style="max-width: 453px;">
                                        <table class="table table-borderless table-nowrap mb-0">
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1 text-muted">Tipe MCU :</p>
                                                            <div id="jenis_mcu">
                                                                @if(!empty($medical->paket))
                                                                    @if($medical->paket == 'mcu tahunan')
                                                                        <h6 class="text-truncate mb-0">Tahunan</h6>
                                                                    @elseif($medical->paket == 'calon karyawan')
                                                                        <h6 class="text-truncate mb-0">Calon Karyawan</h6>
                                                                    @else
                                                                        <h6 class="text-truncate mb-0">Penetapan</h6>
                                                                    @endif
                                                                @else
                                                                    <h6 class="text-truncate mb-0">Tahunan</h6>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1 text-muted">No Lab :</p>
                                                            <div id="no_lab">
                                                                <h6 class="text-truncate mb-0">{{$medical->no_lab}}</h6>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1 text-muted">Full Name :</p>
                                                            <div id="fullname">
                                                                <h6 class="text-truncate mb-0">{{$medical->employee->fullname}}</h6>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1 text-muted">Gender :</p>
                                                            <div id="gender">
                                                                <h6 class="text-truncate mb-0">{{$medical->employee->gender}}</h6>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1 text-muted">Age :</p>
                                                            <div id="umur">
                                                                <h6 class="text-truncate mb-0">{{$medical->umur}}</h6>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1 text-muted">Score Framigham :</p>
                                                            <div id="skor">
                                                                <h6 class="text-truncate mb-0">{{$medical->skor_framigham}}</h6>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1 text-muted">Kriteria :</p>
                                                            <div id="kriteria">
                                                                <h6 class="text-truncate mb-0">{{$medical->kriteria_sehat}}</h6>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1 text-muted">Tanggal Pemeriksaan :</p>
                                                            <div id="tgl_mcu">
                                                                @if(!empty($medical->tanggal_mcu))
                                                                <h6 class="text-truncate mb-0">{{\Carbon\Carbon::parse($medical->tanggal_mcu)->format('d F Y')}}</h6>
                                                                @else
                                                                <h6 class="text-truncate mb-0">-</h6>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <br>
                                    <button type="button" data-bs-toggle="modal" data-bs-target="#modal-lampiran-mcu" class="btn btn-danger"><i class="ri-file-pdf-line me-1 align-bottom"></i> Show MCU</button>
                                    <div id="unduh_mcu" class="mt-2">
                                    </div>
                                </div>
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div>
                    <!--end col-->
                    <div class="col-lg-9">
                        <div class="card">
                            <div class="card-body">
                                <div id="medical-mcu">
                                    <ul class="nav nav-tabs mb-3" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" data-bs-toggle="tab" href="#medical" role="tab" aria-selected="false">
                                                Overview
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" href="#hematologi" role="tab" aria-selected="false">
                                                Hematologi
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" href="#urine" role="tab" aria-selected="false">
                                                Urine
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" href="#faal" role="tab" aria-selected="true">
                                                Faal
                                            </a>
                                        </li>
                                    </ul>
                                    <div class="tab-content text-muted">
                                        <div class="tab-pane active" id="medical" role="tabpanel">
                                            <h5 class="card-title mb-3">Medical Information</h5>
                                            <div class="profile-timeline">
                                                <div class="accordion accordion-flush" id="todayExample">
                                                    <div class="accordion-item border-0">
                                                        <div class="accordion-header" id="headingOne">
                                                            <a class="accordion-button p-2 shadow-none" data-bs-toggle="collapse" href="#collapseOne" aria-expanded="true">
                                                                <div class="d-flex">
                                                                    <div class="flex-shrink-0 avatar-xs">
                                                                        <div class="avatar-title bg-light text-primary rounded-circle">
                                                                            <i class="ri-survey-line"></i>
                                                                        </div>
                                                                    </div>
                                                                    <div class="flex-grow-1 ms-3">
                                                                        <h6 class="fs-14 mb-1">
                                                                            Result Laboratorium
                                                                        </h6>
                                                                    </div>
                                                                </div>
                                                            </a>
                                                        </div>
                                                        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                                            <div id="lab">
                                                                <div class="accordion-body ms-2 ps-5">
                                                                    {{$medical->lab}}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="accordion-item border-0">
                                                        <div class="accordion-header" id="headingTwo">
                                                            <a class="accordion-button p-2 shadow-none" data-bs-toggle="collapse" href="#collapseTwo" aria-expanded="true">
                                                                <div class="d-flex">
                                                                    <div class="flex-shrink-0 avatar-xs">
                                                                        <div class="avatar-title bg-light text-primary rounded-circle">
                                                                            <i class="ri-survey-line"></i>
                                                                        </div>
                                                                    </div>
                                                                    <div class="flex-grow-1 ms-3">
                                                                        <h6 class="fs-14 mb-1">
                                                                            Result Photo Thorax
                                                                        </h6>
                                                                    </div>
                                                                </div>
                                                            </a>
                                                        </div>
                                                        <div id="collapseTwo" class="accordion-collapse collapse show" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                                                            <div id="foto_thorax">
                                                                <div class="accordion-body ms-2 ps-5">
                                                                    {{$medical->foto_thorax}}
                                                                </div>
                                                            </div>
                                                        </div> 
                                                    </div>
                                                    @if(!empty($medical->ekg))
                                                    <div class="accordion-item border-0">
                                                        <div class="accordion-header" id="headingTwo">
                                                            <a class="accordion-button p-2 shadow-none" data-bs-toggle="collapse" href="#collapseSeven" aria-expanded="true">
                                                                <div class="d-flex">
                                                                    <div class="flex-shrink-0 avatar-xs">
                                                                        <div class="avatar-title bg-light text-primary rounded-circle">
                                                                            <i class="ri-survey-line"></i>
                                                                        </div>
                                                                    </div>
                                                                    <div class="flex-grow-1 ms-3">
                                                                        <h6 class="fs-14 mb-1">
                                                                            Result EKG
                                                                        </h6>
                                                                    </div>
                                                                </div>
                                                            </a>
                                                        </div>
                                                        <div id="collapseSeven" class="accordion-collapse collapse show" aria-labelledby="headingSeven" data-bs-parent="#accordionExample">
                                                            <div class="accordion-body ms-2 ps-5">
                                                                {{$medical->ekg}}
                                                            </div>
                                                        </div> 
                                                    </div>
                                                    @endif
                                                    <div class="accordion-item border-0"> 
                                                        <div class="accordion-header" id="headingThree">
                                                            <a class="accordion-button p-2 shadow-none" data-bs-toggle="collapse" href="#collapseThree" aria-expanded="true">
                                                                <div class="d-flex">
                                                                    <div class="flex-shrink-0 avatar-xs">
                                                                        <div class="avatar-title bg-light text-primary rounded-circle">
                                                                            <i class="ri-survey-line"></i>
                                                                        </div>
                                                                    </div>
                                                                    <div class="flex-grow-1 ms-3">
                                                                        <h6 class="fs-14 mb-1">
                                                                            Result Audiometri
                                                                        </h6>
                                                                    </div>
                                                                </div>
                                                            </a>
                                                        </div>
                                                        <div id="collapseThree" class="accordion-collapse collapse show" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                                                            <div id="audiometri">
                                                                <div class="accordion-body ms-2 ps-5">
                                                                    {{$medical->audiometri}}
                                                                </div>
                                                            </div>
                                                        </div> 
                                                    </div>
                                                    <div class="accordion-item border-0"> 
                                                        <div class="accordion-header" id="headingFour">
                                                            <a class="accordion-button p-2 shadow-none" data-bs-toggle="collapse" href="#collapseFour" aria-expanded="true">
                                                                <div class="d-flex">
                                                                    <div class="flex-shrink-0 avatar-xs">
                                                                        <div class="avatar-title bg-light text-primary rounded-circle">
                                                                            <i class="ri-survey-line"></i>
                                                                        </div>
                                                                    </div>
                                                                    <div class="flex-grow-1 ms-3">
                                                                        <h6 class="fs-14 mb-1">
                                                                            Result Physical Doctor
                                                                        </h6>
                                                                    </div>
                                                                </div>
                                                            </a>
                                                        </div>
                                                        <div id="collapseFour" class="accordion-collapse collapse show" aria-labelledby="headingFour" data-bs-parent="#accordionExample">
                                                            <div id="fisik_dokter">
                                                                <div class="accordion-body ms-2 ps-5">
                                                                    {{$medical->fisik_dokter}}
                                                                </div>
                                                            </div>
                                                        </div> 
                                                    </div> 
                                                    <div class="accordion-item border-0"> 
                                                        <div class="accordion-header" id="headingFive">
                                                            <a class="accordion-button p-2 shadow-none" data-bs-toggle="collapse" href="#collapseFive" aria-expanded="true">
                                                                <div class="d-flex">
                                                                    <div class="flex-shrink-0 avatar-xs">
                                                                        <div class="avatar-title bg-light text-primary rounded-circle">
                                                                            <i class="ri-survey-line"></i>
                                                                        </div>
                                                                    </div>
                                                                    <div class="flex-grow-1 ms-3">
                                                                        <h6 class="fs-14 mb-1">
                                                                            Conclusion
                                                                        </h6>
                                                                    </div>
                                                                </div>
                                                            </a>
                                                        </div>
                                                        <div id="collapseFive" class="accordion-collapse collapse show" aria-labelledby="headingFive" data-bs-parent="#accordionExample">
                                                            <div id="kesimpulan">
                                                                <div class="accordion-body ms-2 ps-5">
                                                                    {{$medical->kesimpulan}}
                                                                </div>
                                                            </div>
                                                        </div> 
                                                    </div> 
                                                    <div class="accordion-item border-0"> 
                                                        <div class="accordion-header" id="headingSix">
                                                            <a class="accordion-button p-2 shadow-none" data-bs-toggle="collapse" href="#collapseSix" aria-expanded="true">
                                                                <div class="d-flex">
                                                                    <div class="flex-shrink-0 avatar-xs">
                                                                        <div class="avatar-title bg-light text-primary rounded-circle">
                                                                            <i class="ri-survey-line"></i>
                                                                        </div>
                                                                    </div>
                                                                    <div class="flex-grow-1 ms-3">
                                                                        <h6 class="fs-14 mb-1">                                                                                    
                                                                            Suggestion
                                                                        </h6>
                                                                    </div>
                                                                </div>
                                                            </a>
                                                        </div>
                                                        <div id="collapseSix" class="accordion-collapse collapse show" aria-labelledby="headingSix" data-bs-parent="#accordionExample">
                                                            <div id="saran">
                                                                <div class="accordion-body ms-2 ps-5">
                                                                    {{$medical->saran}}
                                                                </div>
                                                            </div>
                                                        </div> 
                                                    </div> 
                                                </div>
                                                <!--end accordion-->
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="hematologi" role="tabpanel">
                                            <h5 class="card-title mb-3">Hematologi Information</h5>
                                            <div class="row">
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">
                                                    
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Hemoglobin :</p>
                                                            <div id="hm_hemoglobin">
                                                                <h6>{{$medical->hm_hemoglobin}}</h6>
                                                            </div>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            <div id="nr_hm_hemoglobin">
                                                            @foreach($lab as $key => $value)
                                                                @if($key == 'hm_hemoglobin')
                                                                <h6>{{$value}}</h6>
                                                                @endif
                                                            @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">            
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Eritrosit :</p>
                                                            <div id="hm_eritrosit">
                                                                <h6>{{$medical->hm_eritrosit}}</h6>
                                                            </div>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            <div id="nr_hm_eritrosit">
                                                                @foreach($lab as $key => $value)
                                                                    @if($key == 'hm_eritrosit')
                                                                    <h6>{{$value}}</h6>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">
                
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Hematrokit :</p>
                                                            <div id="hm_hematokrit">
                                                                <h6>{{$medical->hm_hematokrit}}</h6>
                                                            </div>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            <div id="nr_hm_hematokrit">
                                                                @foreach($lab as $key => $value)
                                                                    @if($key == 'hm_hematokrit')
                                                                    <h6>{{$value}}</h6>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">
                
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">MCV :</p>
                                                            <div id="hm_mcv">
                                                                <h6>{{$medical->hm_mcv}}</h6>
                                                            </div>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            <div id="nr_hm_mcv">
                                                                @foreach($lab as $key => $value)
                                                                    @if($key == 'hm_mcv')
                                                                    <h6>{{$value}}</h6>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->                                                                                             
                                            </div>
                                            <!--end row-->
                                            <div class="row">
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">
                
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">MCH :</p>
                                                            <div id="hm_mch">
                                                                <h6>{{$medical->hm_mch}}</h6>
                                                            </div>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            <div id="nr_hm_mch">
                                                                @foreach($lab as $key => $value)
                                                                    @if($key == 'hm_mch')
                                                                    <h6>{{$value}}</h6>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->   
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">            
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">MCHC :</p>
                                                            <div id="hm_mchc">
                                                                <h6>{{$medical->hm_mchc}}</h6>
                                                            </div>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            <div id="nr_hm_mchc">
                                                                @foreach($lab as $key => $value)
                                                                    @if($key == 'hm_mchc')
                                                                    <h6>{{$value}}</h6>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">
                                                    
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">RDW :</p>
                                                            <div id="hm_rdw">
                                                                <h6>{{$medical->hm_rdw}}</h6>
                                                            </div>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            <div id="nr_hm_rdw">
                                                                @foreach($lab as $key => $value)
                                                                    @if($key == 'hm_rdw')
                                                                    <h6>{{$value}}</h6>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">
                
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Leukosit :</p>
                                                            <div id="hm_leukosit">
                                                                <h6>{{$medical->hm_leukosit}}</h6>
                                                            </div>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            <div id="nr_hm_leukosit">
                                                                @foreach($lab as $key => $value)
                                                                    @if($key == 'hm_leukosit')
                                                                    <h6>{{$value}}</h6>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->                                                                                            
                                            </div>
                                            <!--end row-->
                                            <div class="row">
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">            
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">EOS :</p>
                                                            <div id="hm_eos">
                                                                <h6>{{$medical->hm_eos}}</h6>
                                                            </div>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            <div id="nr_hm_eos">
                                                                @foreach($lab as $key => $value)
                                                                    @if($key == 'hm_eos')
                                                                    <h6>{{$value}}</h6>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">            
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">BASO :</p>
                                                            <div id="hm_baso">
                                                                <h6>{{$medical->hm_baso}}</h6>
                                                            </div>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            <div id="nr_hm_baso">
                                                                @foreach($lab as $key => $value)
                                                                    @if($key == 'hm_baso')
                                                                    <h6>{{$value}}</h6>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->    
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">            
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Neutro :</p>
                                                            <div id="hm_neutro">
                                                                <h6>{{$medical->hm_neutro}}</h6>
                                                            </div>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            <div id="nr_hm_neutro">
                                                                @foreach($lab as $key => $value)
                                                                    @if($key == 'hm_neutro')
                                                                    <h6>{{$value}}</h6>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">            
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Limfo :</p>
                                                            <div id="hm_limfo">
                                                                <h6>{{$medical->hm_limfo}}</h6>
                                                            </div>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            <div id="nr_hm_limfo">
                                                                @foreach($lab as $key => $value)
                                                                    @if($key == 'hm_limfo')
                                                                    <h6>{{$value}}</h6>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->                                                                                            
                                            </div>
                                            <!--end row-->
                                            <div class="row">
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">                                                
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Mono :</p>
                                                            <div id="hm_mono">
                                                                <h6>{{$medical->hm_mono}}</h6>
                                                            </div>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            <div id="nr_hm_mono">
                                                                @foreach($lab as $key => $value)
                                                                    @if($key == 'hm_mono')
                                                                    <h6>{{$value}}</h6>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">            
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">EOS Absolut :</p>
                                                            <div id="hm_eos_absolut">
                                                                <h6>{{$medical->hm_eos_absolut}}</h6>
                                                            </div>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            <div id="nr_hm_eos_absolut">
                                                                @foreach($lab as $key => $value)
                                                                    @if($key == 'hm_eos_absolut')
                                                                    <h6>{{$value}}</h6>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">            
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">BASO Absolut :</p>
                                                            <div id="hm_baso_absolut">
                                                                <h6>{{$medical->hm_baso_absolut}}</h6>
                                                            </div>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            <div id="nr_hm_baso_absolut">
                                                                @foreach($lab as $key => $value)
                                                                    @if($key == 'hm_baso_absolut')
                                                                    <h6>{{$value}}</h6>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->    
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">            
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Neutro Absolut :</p>
                                                            <div id="hm_neutro_absolut">
                                                                <h6>{{$medical->hm_neutro_absolut}}</h6>
                                                            </div>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            <div id="nr_hm_neutro_absolut">
                                                                @foreach($lab as $key => $value)
                                                                    @if($key == 'hm_neutro_absolut')
                                                                    <h6>{{$value}}</h6>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->                                                
                                            </div>
                                            <!--end row-->
                                            <div class="row">
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">            
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Limfo Absolut :</p>
                                                            <div id="hm_limfo_absolut">
                                                                <h6>{{$medical->hm_limfo_absolut}}</h6>
                                                            </div>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            <div id="nr_hm_limfo_absolut">
                                                                @foreach($lab as $key => $value)
                                                                    @if($key == 'hm_limfo_absolut')
                                                                    <h6>{{$value}}</h6>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">            
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Mono Absolut :</p>
                                                            <div id="hm_mono_absolut">
                                                                <h6>{{$medical->hm_mono_absolut}}</h6>
                                                            </div>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            <div id="nr_hm_mono_absolut">
                                                                @foreach($lab as $key => $value)
                                                                    @if($key == 'hm_mono_absolut')
                                                                    <h6>{{$value}}</h6>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">            
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Trombosit :</p>
                                                            <div id="hm_trombosit">
                                                                <h6>{{$medical->hm_trombosit}}</h6>
                                                            </div>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            <div id="nr_hm_trombosit">
                                                                @foreach($lab as $key => $value)
                                                                    @if($key == 'hm_trombosit')
                                                                    <h6>{{$value}}</h6>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">
                
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">LED :</p>
                                                            <div id="hm_led">
                                                                <h6>{{$medical->hm_led}}</h6>
                                                            </div>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            <div id="nr_hm_led">
                                                                @foreach($lab as $key => $value)
                                                                    @if($key == 'hm_led')
                                                                    <h6>{{$value}}</h6>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                            </div>
                                            <!--end row-->
                                        </div>
                                        <div class="tab-pane" id="urine" role="tabpanel">
                                            <h5 class="card-title mb-3">Urine Information</h5>
                                            <div class="row">
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">
                                                    
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Warna :</p>
                                                            <div id="u_warna">
                                                                <h6>{{$medical->u_warna}}</h6>
                                                            </div>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            <div id="nr_u_warna">
                                                                @foreach($lab as $key => $value)
                                                                    @if($key == 'u_warna')
                                                                    <h6>{{$value}}</h6>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">            
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Kejernihan :</p>
                                                            <div id="u_kejernihan">
                                                                <h6>{{$medical->u_kejernihan}}</h6>
                                                            </div>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            <div id="nr_u_kejernihan">
                                                                @foreach($lab as $key => $value)
                                                                    @if($key == 'u_kejernihan')
                                                                    <h6>{{$value}}</h6>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">            
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Berat Jenis :</p>
                                                            <div id="u_berat_jenis">
                                                                <h6>{{$medical->u_berat_jenis}}</h6>
                                                            </div>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            <div id="nr_u_berat_jenis">
                                                                @foreach($lab as $key => $value)
                                                                    @if($key == 'u_berat_jenis')
                                                                    <h6>{{$value}}</h6>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">            
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">PH :</p>
                                                            <div id="u_ph">
                                                                <h6>{{$medical->u_ph}}</h6>
                                                            </div>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            <div id="nr_u_ph">
                                                                @foreach($lab as $key => $value)
                                                                    @if($key == 'u_ph')
                                                                    <h6>{{$value}}</h6>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->                                                                                             
                                            </div>
                                            <!--end row-->
                                            <div class="row">
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">            
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Protein Albumin :</p>
                                                            <div id="u_protein_albumin">
                                                                <h6>{{$medical->u_protein_albumin}}</h6>
                                                            </div>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            <div id="nr_u_protein_albumin">
                                                                @foreach($lab as $key => $value)
                                                                    @if($key == 'u_protein_albumin')
                                                                    <h6>{{$value}}</h6>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->   
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">            
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Glukosa :</p>
                                                            <div id="u_glukosa">
                                                                <h6>{{$medical->u_glukosa}}</h6>
                                                            </div>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            <div id="nr_u_glukosa">
                                                                @foreach($lab as $key => $value)
                                                                    @if($key == 'u_glukosa')
                                                                    <h6>{{$value}}</h6>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">                                                
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Keton :</p>
                                                            <div id="u_keton">
                                                                <h6>{{$medical->u_keton}}</h6>
                                                            </div>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            <div id="nr_u_keton">
                                                                @foreach($lab as $key => $value)
                                                                    @if($key == 'u_keton')
                                                                    <h6>{{$value}}</h6>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">            
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Bilirubin :</p>
                                                            <div id="u_bilirubin">
                                                                <h6>{{$medical->u_bilirubin}}</h6>
                                                            </div>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            <div id="nr_u_bilirubin">
                                                                @foreach($lab as $key => $value)
                                                                    @if($key == 'u_bilirubin')
                                                                    <h6>{{$value}}</h6>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->                                                                                            
                                            </div>
                                            <!--end row-->
                                            <div class="row">
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">            
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Urobilinogen :</p>
                                                            <div id="u_urobilinogen">
                                                                <h6>{{$medical->u_urobilinogen}}</h6>
                                                            </div>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            <div id="nr_u_urobilinogen">
                                                                @foreach($lab as $key => $value)
                                                                    @if($key == 'u_urobilinogen')
                                                                    <h6>{{$value}}</h6>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">            
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Nitrit :</p>
                                                            <div id="u_nitrit">
                                                                <h6>{{$medical->u_nitrit}}</h6>
                                                            </div>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            <div id="nr_u_nitrit">
                                                                @foreach($lab as $key => $value)
                                                                    @if($key == 'u_nitrit')
                                                                    <h6>{{$value}}</h6>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->    
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">            
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Leukosit Esterase :</p>
                                                            <div id="u_leukosit_esterase">
                                                                <h6>{{$medical->u_leukosit_esterase}}</h6>
                                                            </div>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            <div id="nr_u_leukosit_esterase">
                                                                @foreach($lab as $key => $value)
                                                                    @if($key == 'u_leukosit_esterase')
                                                                    <h6>{{$value}}</h6>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">            
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Darah Haem :</p>
                                                            <div id="u_darah_haem">
                                                                <h6>{{$medical->u_darah_haem}}</h6>
                                                            </div>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            <div id="nr_u_darah_haem">
                                                                @foreach($lab as $key => $value)
                                                                    @if($key == 'u_darah_haem')
                                                                    <h6>{{$value}}</h6>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->                                                                                            
                                            </div>
                                            <!--end row-->
                                            <div class="row">
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">                                                
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Eri :</p>
                                                            <div id="u_eri">
                                                                <h6>{{$medical->u_eri}}</h6>
                                                            </div>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            <div id="nr_u_eri">
                                                                @foreach($lab as $key => $value)
                                                                    @if($key == 'u_eri')
                                                                    <h6>{{$value}}</h6>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">            
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Leuko :</p>
                                                            <div id="u_leuko">
                                                                <h6>{{$medical->u_leuko}}</h6>
                                                            </div>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            <div id="nr_u_leuko">
                                                                @foreach($lab as $key => $value)
                                                                    @if($key == 'u_leuko')
                                                                    <h6>{{$value}}</h6>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">            
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Epithel :</p>
                                                            <div id="u_epithel">
                                                                <h6>{{$medical->u_epithel}}</h6>
                                                            </div>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            <div id="nr_u_epithel">
                                                                @foreach($lab as $key => $value)
                                                                    @if($key == 'u_epithel')
                                                                    <h6>{{$value}}</h6>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->    
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">            
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Silinder :</p>
                                                            <div id="u_silinder">
                                                                <h6>{{$medical->u_silinder}}</h6>
                                                            </div>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            <div id="nr_u_silinder">
                                                                @foreach($lab as $key => $value)
                                                                    @if($key == 'u_silinder')
                                                                    <h6>{{$value}}</h6>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->                                                
                                            </div>
                                            <!--end row-->
                                            <div class="row">
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">            
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Kristal :</p>
                                                            <div id="u_kristal">
                                                                <h6>{{$medical->u_kristal}}</h6>
                                                            </div>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            <div id="nr_u_kristal">
                                                                @foreach($lab as $key => $value)
                                                                    @if($key == 'u_kristal')
                                                                    <h6>{{$value}}</h6>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">            
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Lain-lain :</p>
                                                            <div id="u_lain">
                                                                <h6>{{$medical->u_lain}}</h6>
                                                            </div>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            <div id="nr_u_lain">
                                                                @foreach($lab as $key => $value)
                                                                    @if($key == 'u_lain')
                                                                    <h6>{{$value}}</h6>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                            </div>
                                            <!--end row-->
                                        </div>
                                        <div class="tab-pane" id="faal" role="tabpanel">
                                            <h5 class="card-title mb-3">Faal Information</h5>
                                            <div class="row">
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">                                                
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">SGOT :</p>
                                                            <div id="fh_sgot">
                                                                <h6>{{$medical->fh_sgot}}</h6>
                                                            </div>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            <div id="nr_fh_sgot">
                                                                @foreach($lab as $key => $value)
                                                                    @if($key == 'fh_sgot')
                                                                    <h6>{{$value}}</h6>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">            
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">SGPT :</p>
                                                            <div id="fh_sgpt">
                                                                <h6>{{$medical->fh_sgpt}}</h6>
                                                            </div>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            <div id="nr_fh_sgpt">
                                                                @foreach($lab as $key => $value)
                                                                    @if($key == 'fh_sgpt')
                                                                    <h6>{{$value}}</h6>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">            
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Kolesterol Total :</p>
                                                            <div id="fl_kolesterol_total">
                                                                <h6>{{$medical->fl_kolesterol_total}}</h6>
                                                            </div>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            <div id="nr_fl_kolesterol_total">
                                                                @foreach($lab as $key => $value)
                                                                    @if($key == 'fl_kolesterol_total')
                                                                    <h6>{{$value}}</h6>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">            
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">HDL Kolesterol :</p>
                                                            <div id="fl_hdl_kolesterol">
                                                                <h6>{{$medical->fl_hdl_kolesterol}}</h6>
                                                            </div>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            <div id="nr_fl_hdl_kolesterol">
                                                                @foreach($lab as $key => $value)
                                                                    @if($key == 'fl_hdl_kolesterol')
                                                                    <h6>{{$value}}</h6>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->                                                                                             
                                            </div>
                                            <!--end row-->
                                            <div class="row">
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">            
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">LDL Kolesterol :</p>
                                                            <div id="fl_ldl_kolesterol">
                                                                <h6>{{$medical->fl_ldl_kolesterol}}</h6>
                                                            </div>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            <div id="nr_fl_ldl_kolesterol">
                                                                @foreach($lab as $key => $value)
                                                                    @if($key == 'fl_ldl_kolesterol')
                                                                    <h6>{{$value}}</h6>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->   
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">            
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Trigliserida :</p>
                                                            <div id="fl_trigliserida">
                                                                <h6>{{$medical->fl_trigliserida}}</h6>
                                                            </div>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            <div id="nr_fl_trigliserida">
                                                                @foreach($lab as $key => $value)
                                                                    @if($key == 'fl_trigliserida')
                                                                    <h6>{{$value}}</h6>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">                                                
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Glukosa Puasa :</p>
                                                            <div id="gd_glukosa_puasa">
                                                                <h6>{{$medical->gd_glukosa_puasa}}</h6>
                                                            </div>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            <div id="nr_gd_glukosa_puasa">
                                                                @foreach($lab as $key => $value)
                                                                    @if($key == 'gd_glukosa_puasa')
                                                                    <h6>{{$value}}</h6>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">            
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">JPP :</p>
                                                            <div id="gd_jpp">
                                                                <h6>{{$medical->gd_jpp}}</h6>
                                                            </div>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            <div id="nr_gd_jpp">
                                                                @foreach($lab as $key => $value)
                                                                    @if($key == 'gd_jpp')
                                                                    <h6>{{$value}}</h6>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->                                                                                            
                                            </div>
                                            <!--end row-->
                                            <div class="row">
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">            
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">BUN :</p>
                                                            <div id="fg_bun">
                                                                <h6>{{$medical->fg_bun}}</h6>
                                                            </div>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            <div id="nr_fg_bun">
                                                                @foreach($lab as $key => $value)
                                                                    @if($key == 'fg_bun')
                                                                    <h6>{{$value}}</h6>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">            
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Ureum :</p>
                                                            <div id="fg_ureum">
                                                                <h6>{{$medical->fg_ureum}}</h6>
                                                            </div>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            <div id="nr_fg_ureum">
                                                                @foreach($lab as $key => $value)
                                                                    @if($key == 'fg_ureum')
                                                                    <h6>{{$value}}</h6>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->    
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">            
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Kreatinin :</p>
                                                            <div id="fg_kreatinin">
                                                                <h6>{{$medical->fg_kreatinin}}</h6>
                                                            </div>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            <div id="nr_fg_kreatinin">
                                                                @foreach($lab as $key => $value)
                                                                    @if($key == 'fg_kreatinin')
                                                                    <h6>{{$value}}</h6>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">            
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">EGFR :</p>
                                                            <div id="fg_egfr">
                                                                <h6>{{$medical->fg_egfr}}</h6>
                                                            </div>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            <div id="nr_fg_egfr">
                                                                @foreach($lab as $key => $value)
                                                                    @if($key == 'fg_egfr')
                                                                    <h6>{{$value}}</h6>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->                                                                                            
                                            </div>
                                            <!--end row-->
                                            <div class="row">
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">                                                
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Asam Urat :</p>
                                                            <div id="asam_urat">
                                                                <h6>{{$medical->asam_urat}}</h6>
                                                            </div>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            <div id="nr_asam_urat">
                                                                @foreach($lab as $key => $value)
                                                                    @if($key == 'asam_urat')
                                                                    <h6>{{$value}}</h6>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">            
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">HBSAG :</p>
                                                            <div id="hbsag">
                                                                <h6>{{$medical->hbsag}}</h6>
                                                            </div>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            <div id="nr_hbsag">
                                                                @foreach($lab as $key => $value)
                                                                    @if($key == 'hbsag')
                                                                    <h6>{{$value}}</h6>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->                                                                                            
                                            </div>
                                            <!--end row-->
                                        </div>
                                    </div>
                                </div>
                                <div id="medical-view"></div>
                            </div>
                            <!--end card-body-->
                        </div><!-- end card -->
                    </div>
                    <!--end col-->
                </div>                      
                @endif                        
            </div>
        </div>
        <!--end col-->
    </div>
    <!--end row-->
</div><!-- container-fluid -->
<!--modal lampiran mcu-->
<div class="modal flip" id="modal-lampiran-mcu" tabindex="-1" aria-labelledby="exampleModalgridLabel" aria-modal="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-judul"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="profile-lampiran">
                </div>                
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>
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
<!-- profile-setting init js -->
<script src="/assets/js/pages/profile-setting.init.js"></script>
<!-- Select2 -->
<script src="/assets/libs/adminlte/select2/js/4.1.0/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.js"></script>
@endsection
@section('javascript')
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
    $(function () {
        $('.select2').select2()        
    });

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
    var date_mcu = $('#date_mcu').val();    
    $.ajax({
        url: "{{ route('profile.lampiran.pdf') }}",
        type: "POST",
        data: {
            date_mcu: date_mcu,
            _token: '{{ csrf_token() }}'
        },
        dataType: 'json',
        success: function(result) {
            // $("#profile-lampiran").html('<embed src="'+result.pdf_mcu+'" frameborder="0" width="100%" height="550px">');            
            if(result.pdf_mcu == 0) {
                $("#profile-lampiran").html('<center><div class="text-center"><lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop" colors="primary:#405189,secondary:#0ab39c" style="width:150px;height:150px"></lord-icon><h5 class="text-center mt-2">data not available...</h5></div></center>');
            }else{
                $("#profile-lampiran").html('<iframe src="'+result.pdf_mcu+'" frameborder="0" style="height:500px; width:100%;"></iframe>');            
            }
            if(result.unduh_mcu == 0) {
                $("#unduh_mcu").html('');            
            }else{
                $("#unduh_mcu").html('<a href="'+result.unduh_mcu+'" class="btn btn-success"><i class="ri-file-pdf-line me-1 align-bottom"></i> Download MCU</a>');
            }
            if(result.paket == 'mcu tahunan'){
                $("#medical-mcu").show();
                $("#medical-view").hide();
            }else{
                $("#medical-mcu").hide();
                $("#medical-view").show();
                $("#medical-view").html('<iframe src="'+result.pdf_mcu+'" frameborder="0" style="height:700px; width:100%;"></iframe>');
            }
        }
    });
    $('#date_mcu').on('change', function(){
        $("#profile-lampiran").html(''); 
        var date_mcu = this.value;
        $.ajax({
            url: "{{ route('profile.lampiran.pdf') }}",
            type: "POST",
            data: {
                date_mcu: date_mcu,
                _token: '{{ csrf_token() }}'
            },
            dataType: 'json',
            success: function(result) {
                // console.log(result);
                if(result.unduh_mcu == 0) {
                    $("#unduh_mcu").html('');            
                }else{
                    $("#unduh_mcu").html('<a href="'+result.unduh_mcu+'" class="btn btn-success"><i class="ri-file-pdf-line me-1 align-bottom"></i> Download MCU</a>');
                }
                if(result.pdf_mcu == 0) {
                    $("#profile-lampiran").html('<center><div class="text-center"><lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop" colors="primary:#405189,secondary:#0ab39c" style="width:150px;height:150px"></lord-icon><h5 class="text-center mt-2">data not available...</h5></div></center>');
                }else{                        
                    $("#profile-lampiran").html('<iframe src="'+result.pdf_mcu+'" frameborder="0" style="height:500px; width:100%;"></iframe>');                                                       
                     //profile information                                                       
                     $("#no_lab").html('<h6 class="text-truncate mb-0">'+result.no_lab+'</h6>');                                                       
                    $("#fullname").html('<h6 class="text-truncate mb-0">'+result.fullname+'</h6>');                                                       
                    $("#gender").html('<h6 class="text-truncate mb-0">'+result.gender+'</h6>');                                                       
                    $("#umur").html('<h6 class="text-truncate mb-0">'+result.umur+'</h6>');                                                       
                    $("#skor").html('<h6 class="text-truncate mb-0">'+result.skor_framigham+'</h6>');                                                       
                    $("#kriteria").html('<h6 class="text-truncate mb-0">'+result.kriteria_sehat+'</h6>');                                                       
                    $("#tgl_mcu").html('<h6 class="text-truncate mb-0">'+result.tgl_mcu+'</h6>');                                                       
                    //medical information
                    if(result.paket == 'mcu tahunan'){
                        $("#medical-mcu").show();
                        $("#medical-view").hide();
                        $("#lab").html('<div class="accordion-body ms-2 ps-5">'+result.lab+'</div>');                                                       
                        $("#foto_thorax").html('<div class="accordion-body ms-2 ps-5">'+result.foto_thorax+'</div>');
                        if(!result.audiometri){
                            $("#audiometri").html('<div class="accordion-body ms-2 ps-5">-</div>');                                                       
                        }else{
                            $("#audiometri").html('<div class="accordion-body ms-2 ps-5">'+result.audiometri+'</div>');                                                       
                        }                                 
                        $("#fisik_dokter").html('<div class="accordion-body ms-2 ps-5">'+result.fisik_dokter+'</div>');                                                       
                        $("#kesimpulan").html('<div class="accordion-body ms-2 ps-5">'+result.kesimpulan+'</div>');                                                       
                        $("#saran").html('<div class="accordion-body ms-2 ps-5">'+result.saran+'</div>');
                        //hematologi
                        $("#hm_hemoglobin").html('<h6>'+result.hm_hemoglobin+'</h6>');
                        $("#hm_eritrosit").html('<h6>'+result.hm_eritrosit+'</h6>');
                        $("#hm_hematokrit").html('<h6>'+result.hm_hematokrit+'</h6>');
                        $("#hm_mcv").html('<h6>'+result.hm_mcv+'</h6>');
                        $("#hm_mch").html('<h6>'+result.hm_mch+'</h6>');
                        $("#hm_mchc").html('<h6>'+result.hm_mchc+'</h6>');
                        $("#hm_rdw").html('<h6>'+result.hm_rdw+'</h6>');
                        $("#hm_leukosit").html('<h6>'+result.hm_leukosit+'</h6>');
                        $("#hm_eos").html('<h6>'+result.hm_eos+'</h6>');
                        $("#hm_baso").html('<h6>'+result.hm_baso+'</h6>');
                        $("#hm_neutro").html('<h6>'+result.hm_neutro+'</h6>');
                        $("#hm_limfo").html('<h6>'+result.hm_limfo+'</h6>');
                        $("#hm_mono").html('<h6>'+result.hm_mono+'</h6>');
                        $("#hm_eos_absolut").html('<h6>'+result.hm_eos_absolut+'</h6>');
                        $("#hm_baso_absolut").html('<h6>'+result.hm_baso_absolut+'</h6>');
                        $("#hm_neutro_absolut").html('<h6>'+result.hm_neutro_absolut+'</h6>');
                        $("#hm_limfo_absolut").html('<h6>'+result.hm_limfo_absolut+'</h6>');
                        $("#hm_mono_absolut").html('<h6>'+result.hm_mono_absolut+'</h6>');
                        $("#hm_trombosit").html('<h6>'+result.hm_trombosit+'</h6>');
                        $("#hm_led").html('<h6>'+result.hm_led+'</h6>');
                        //urine
                        $("#u_warna").html('<h6>'+result.u_warna+'</h6>');
                        $("#u_kejernihan").html('<h6>'+result.u_kejernihan+'</h6>');
                        $("#u_berat_jenis").html('<h6>'+result.u_berat_jenis+'</h6>');
                        $("#u_ph").html('<h6>'+result.u_ph+'</h6>');
                        $("#u_protein_albumin").html('<h6>'+result.u_protein_albumin+'</h6>');
                        $("#u_glukosa").html('<h6>'+result.u_glukosa+'</h6>');
                        $("#u_keton").html('<h6>'+result.u_keton+'</h6>');
                        $("#u_bilirubin").html('<h6>'+result.u_bilirubin+'</h6>');
                        $("#u_urobilinogen").html('<h6>'+result.u_urobilinogen+'</h6>');
                        $("#u_nitrit").html('<h6>'+result.u_nitrit+'</h6>');
                        $("#u_leukosit_esterase").html('<h6>'+result.u_leukosit_esterase+'</h6>');
                        $("#u_darah_haem").html('<h6>'+result.u_darah_haem+'</h6>');
                        $("#u_eri").html('<h6>'+result.u_eri+'</h6>');
                        $("#u_leuko").html('<h6>'+result.u_leuko+'</h6>');
                        $("#u_epithel").html('<h6>'+result.u_epithel+'</h6>');
                        $("#u_silinder").html('<h6>'+result.u_silinder+'</h6>');
                        $("#u_kristal").html('<h6>'+result.u_kristal+'</h6>');
                        $("#u_lain").html('<h6>'+result.u_lain+'</h6>');
                        //faal
                        $("#fh_sgot").html('<h6>'+result.fh_sgot+'</h6>');
                        $("#fh_sgpt").html('<h6>'+result.fh_sgpt+'</h6>');
                        $("#fl_kolesterol_total").html('<h6>'+result.fl_kolesterol_total+'</h6>');
                        $("#fl_hdl_kolesterol").html('<h6>'+result.fl_hdl_kolesterol+'</h6>');
                        $("#fl_ldl_kolesterol").html('<h6>'+result.fl_ldl_kolesterol+'</h6>');
                        $("#fl_trigliserida").html('<h6>'+result.fl_trigliserida+'</h6>');
                        $("#gd_glukosa_puasa").html('<h6>'+result.gd_glukosa_puasa+'</h6>');
                        $("#gd_jpp").html('<h6>'+result.gd_jpp+'</h6>');
                        $("#fg_bun").html('<h6>'+result.fg_bun+'</h6>');
                        $("#fg_ureum").html('<h6>'+result.fg_ureum+'</h6>');
                        $("#fg_kreatinin").html('<h6>'+result.fg_kreatinin+'</h6>');
                        $("#fg_egfr").html('<h6>'+result.fg_egfr+'</h6>');
                        $("#asam_urat").html('<h6>'+result.asam_urat+'</h6>');
                        $("#hbsag").html('<h6>'+result.hbsag+'</h6>');
                        //nilai rujukan
                        $.each(result.master_lab, function(key, value) {
                            //hematologi
                            if(key == 'hm_hemoglobin'){
                                $("#nr_hm_hemoglobin").html('<h6>'+value+'</h6>');
                            }
                            if(key == 'hm_eritrosit'){
                                $("#nr_hm_eritrosit").html('<h6>'+value+'</h6>');
                            }
                            if(key == 'hm_hematokrit'){
                                $("#nr_hm_hematokrit").html('<h6>'+value+'</h6>');
                            }
                            if(key == 'hm_mcv'){
                                $("#nr_hm_mcv").html('<h6>'+value+'</h6>');
                            }
                            if(key == 'hm_mch'){
                                $("#nr_hm_mch").html('<h6>'+value+'</h6>');
                            }
                            if(key == 'hm_mchc'){
                                $("#nr_hm_mchc").html('<h6>'+value+'</h6>');
                            }
                            if(key == 'hm_rdw'){
                                $("#nr_hm_rdw").html('<h6>'+value+'</h6>');
                            }
                            if(key == 'hm_leukosit'){
                                $("#nr_hm_leukosit").html('<h6>'+value+'</h6>');
                            }
                            if(key == 'hm_eos'){
                                $("#nr_hm_eos").html('<h6>'+value+'</h6>');
                            }
                            if(key == 'hm_baso'){
                                $("#nr_hm_baso").html('<h6>'+value+'</h6>');
                            }
                            if(key == 'hm_neutro'){
                                $("#nr_hm_neutro").html('<h6>'+value+'</h6>');
                            }
                            if(key == 'hm_limfo'){
                                $("#nr_hm_limfo").html('<h6>'+value+'</h6>');
                            }
                            if(key == 'hm_mono'){
                                $("#nr_hm_mono").html('<h6>'+value+'</h6>');
                            }
                            if(key == 'hm_eos_absolut'){
                                $("#nr_hm_eos_absolut").html('<h6>'+value+'</h6>');
                            }
                            if(key == 'hm_baso_absolut'){
                                $("#nr_hm_baso_absolut").html('<h6>'+value+'</h6>');
                            }
                            if(key == 'hm_neutro_absolut'){
                                $("#nr_hm_neutro_absolut").html('<h6>'+value+'</h6>');
                            }
                            if(key == 'hm_limfo_absolut'){
                                $("#nr_hm_limfo_absolut").html('<h6>'+value+'</h6>');
                            }
                            if(key == 'hm_mono_absolut'){
                                $("#nr_hm_mono_absolut").html('<h6>'+value+'</h6>');
                            }
                            if(key == 'hm_trombosit'){
                                $("#nr_hm_trombosit").html('<h6>'+value+'</h6>');
                            }
                            if(key == 'hm_led'){
                                $("#nr_hm_led").html('<h6>'+value+'</h6>');
                            }
                            //urine
                            if(key == 'u_warna'){
                                $("#nr_u_warna").html('<h6>'+value+'</h6>');
                            }
                            if(key == 'u_kejernihan'){
                                $("#nr_u_kejernihan").html('<h6>'+value+'</h6>');
                            }
                            if(key == 'u_berat_jenis'){
                                $("#nr_u_berat_jenis").html('<h6>'+value+'</h6>');
                            }
                            if(key == 'u_ph'){
                                $("#nr_u_ph").html('<h6>'+value+'</h6>');
                            }
                            if(key == 'u_protein_albumin'){
                                $("#nr_u_protein_albumin").html('<h6>'+value+'</h6>');
                            }
                            if(key == 'u_glukosa'){
                                $("#nr_u_glukosa").html('<h6>'+value+'</h6>');
                            }
                            if(key == 'u_keton'){
                                $("#nr_u_keton").html('<h6>'+value+'</h6>');
                            }
                            if(key == 'u_bilirubin'){
                                $("#nr_u_bilirubin").html('<h6>'+value+'</h6>');
                            }
                            if(key == 'u_urobilinogen'){
                                $("#nr_u_urobilinogen").html('<h6>'+value+'</h6>');
                            }
                            if(key == 'u_nitrit'){
                                $("#nr_u_nitrit").html('<h6>'+value+'</h6>');
                            }
                            if(key == 'u_leukosit_esterase'){
                                $("#nr_u_leukosit_esterase").html('<h6>'+value+'</h6>');
                            }
                            if(key == 'u_darah_haem'){
                                $("#nr_u_darah_haem").html('<h6>'+value+'</h6>');
                            }
                            if(key == 'u_eri'){
                                $("#nr_u_eri").html('<h6>'+value+'</h6>');
                            }
                            if(key == 'u_leuko'){
                                $("#nr_u_leuko").html('<h6>'+value+'</h6>');
                            }
                            if(key == 'u_epithel'){
                                $("#nr_u_epithel").html('<h6>'+value+'</h6>');
                            }
                            if(key == 'u_silinder'){
                                $("#nr_u_silinder").html('<h6>'+value+'</h6>');
                            }
                            if(key == 'u_kristal'){
                                $("#nr_u_kristal").html('<h6>'+value+'</h6>');
                            }
                            if(key == 'u_lain'){
                                $("#nr_u_lain").html('<h6>'+value+'</h6>');
                            }
                            //faal
                            if(key == 'fh_sgot'){
                                $("#nr_fh_sgot").html('<h6>'+value+'</h6>');
                            }
                            if(key == 'fh_sgpt'){
                                $("#nr_fh_sgpt").html('<h6>'+value+'</h6>');
                            }
                            if(key == 'fl_kolesterol_total'){
                                $("#nr_fl_kolesterol_total").html('<h6>'+value+'</h6>');
                            }
                            if(key == 'fl_hdl_kolesterol'){
                                $("#nr_fl_hdl_kolesterol").html('<h6>'+value+'</h6>');
                            }
                            if(key == 'fl_ldl_kolesterol'){
                                $("#nr_fl_ldl_kolesterol").html('<h6>'+value+'</h6>');
                            }
                            if(key == 'fl_trigliserida'){
                                $("#nr_fl_trigliserida").html('<h6>'+value+'</h6>');
                            }
                            if(key == 'gd_glukosa_puasa'){
                                $("#nr_gd_glukosa_puasa").html('<h6>'+value+'</h6>');
                            }
                            if(key == 'gd_jpp'){
                                $("#nr_gd_jpp").html('<h6>'+value+'</h6>');
                            }
                            if(key == 'fg_bun'){
                                $("#nr_fg_bun").html('<h6>'+value+'</h6>');
                            }
                            if(key == 'fg_ureum'){
                                $("#nr_fg_ureum").html('<h6>'+value+'</h6>');
                            }
                            if(key == 'fg_kreatinin'){
                                $("#nr_fg_kreatinin").html('<h6>'+value+'</h6>');
                            }
                            if(key == 'fg_egfr'){
                                $("#nr_fg_egfr").html('<h6>'+value+'</h6>');
                            }
                            if(key == 'asam_urat'){
                                $("#nr_asam_urat").html('<h6>'+value+'</h6>');
                            }
                            if(key == 'hbsag'){
                                $("#nr_hbsag").html('<h6>'+value+'</h6>');
                            }
                        });  
                    }else{
                        $("#medical-mcu").hide();
                        $("#medical-view").show();
                        $("#medical-view").html('<iframe src="'+result.pdf_mcu+'" frameborder="0" style="height:500px; width:100%;"></iframe>');
                    }
                }
            }
        });
    });
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