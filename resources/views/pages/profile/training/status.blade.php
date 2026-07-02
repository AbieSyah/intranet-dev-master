@extends('layouts.master')
@section('link')
<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- Required meta tags -->
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css"/>
<!-- costume css -->
<link rel="stylesheet" type="text/css" href="{{asset('assets/flip/css/flipbook.style.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('assets/flip/css/font-awesome.css')}}">
<style type="text/css">
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
    @sizeContainer: 400px;
@sizeInput: 240px;
@sizeCounter: 60px;
@sizeJump: 60px;
@sizeClose: 20px;

.centered {
  top: 50%;
  transform: translate(0, -50%);
}
.activeJump {
  cursor: pointer;
  color: #424242;
}
.activeSearch {
  opacity: 1 !important;
  transform: translateY(3px) !important;
  transition: all 150ms linear;
}
#searchBar___container {
  height: 37px;
  width: @sizeContainer;
  border: 1px solid #ccc;
  position: absolute;
  top: 0;
  right: 10%;
  opacity: 0;
  transform: translateY(-45px);
  transition: all 150ms linear;
  border-radius: 2px;
  .searchBar___inputContainer {
    display: inline-block;
    position: absolute;
    height: 24px;
    width: 96%;
    padding: 0 2% 0 2%;
    .searchBar___input {
      padding: 0;
      display: inline-block;
      height: 24px;
      border: none;
      font-size: 16px;
      width: @sizeInput;
      &:focus {
        outline-width: 0;
      }
    }
    .searchBar___counter {
      display: inline-block;
      text-align: center;
      border-right: 1px solid #aaa;
      width: @sizeCounter;
      color: #aaa;
    }
    .searchBar___jump {
      display: inline-block;
      text-align: center;
      width: @sizeJump;
      color: #aaa;
      right: 0;
      text-align: center;
      span {
        padding-left: 5px;
        padding-right: 5px;
      }
    }
  }
  .searchBar___close {
    cursor: pointer;
    display: inline-block;
    position: absolute;
    width: @sizeClose;
    right: 5px;
    color: black;
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
                            <a class="nav-link fs-14 {{ request()->is('profile/training') ? '' : 'active' }}" href="{{route('profile.training')}}">
                               Training
                            </a>
                        </li>
                        @endcan
                    </ul> --}}
                </div>
                <!-- Navbar -->
                <!-- start page title -->        
                <div class="row justify-content-center pt-4">
                    <div class="col-sm-6 mx-auto">
                        <div class="card ribbon-box">  
                            <div class="card-header">
                                <h5 class="card-subtitle text-muted mb-0" style="text-align: center">hgvfyugy</h5>
                            </div>  
                            <div class="card-body">
                                <div class="row">
                                    <table class="table table-borderless">
                                        <tbody>
                                            <tr>
                                                <th scope="row"><a href="#" class="fw-semibold">Tanggal Usulan</a></th>
                                                <td>:</td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th scope="row"><a href="#" class="fw-semibold">Tanggal Disetujui</a></th>
                                                <td>:</td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th scope="row"><a href="#" class="fw-semibold">Nilai</a></th>
                                                <td>:</td>
                                                <td></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="row">
                                    <ul class="nav nav-justified mb-3" role="tablist">
                                        <li class="nav-item waves-effect waves-light">
                                            <a class="nav-link active" data-bs-toggle="tab" href="#pill-justified-home-1" role="tab">
                                                Status Approval
                                            </a>
                                        </li>
                                        <li class="nav-item waves-effect waves-light">
                                            <a class="nav-link" data-bs-toggle="tab" href="#pill-justified-profile-1" role="tab">
                                                Status Support
                                            </a>
                                        </li>
                                    </ul>
                                    <div class="tab-content text-muted">
                                        <div class="tab-pane active" id="pill-justified-home-1" role="tabpanel">
                                            <div class="profile-timeline">
                                                <div class="accordion accordion-flush" id="todayExample">                                        
                                                    <div class="accordion-item border-0">
                                                        <div class="accordion-header" id="headingTwo">
                                                            <a class="accordion-button p-2 shadow-none" data-bs-toggle="collapse" href="#collapseTwo" aria-expanded="false">
                                                                <div class="d-flex">
                                                                    <div class="flex-shrink-0 avatar-xs">
                                                                        <div class="avatar-title bg-light text-danger rounded-circle">
                                                                            <i class="ri-close-line"></i>
                                                                        </div>
                                                                    </div>
                                                                    <div class="flex-grow-1 ms-3">
                                                                        <h6 class="fs-14 mb-1">
                                                                            Direvisi Atasan : -
                                                                        </h6>
                                                                        <small class="text-muted"></small>
                                                                    </div>
                                                                </div>
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <div class="accordion-item border-0">
                                                        <div class="accordion-header" id="headingTwo">
                                                            <a class="accordion-button p-2 shadow-none" data-bs-toggle="collapse" href="#collapseTwo" aria-expanded="false">
                                                                <div class="d-flex">
                                                                    <div class="flex-shrink-0 avatar-xs">
                                                                        <div class="avatar-title bg-light text-danger rounded-circle">
                                                                            <i class="ri-close-line"></i>
                                                                        </div>
                                                                    </div>
                                                                    <div class="flex-grow-1 ms-3">
                                                                        <h6 class="fs-14 mb-1">
                                                                            Direvisi oleh Kaizen 1
                                                                        </h6>
                                                                        <small class="text-muted"></small>
                                                                    </div>
                                                                </div>
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <div class="accordion-item border-0">
                                                        <div class="accordion-header" id="headingTwo">
                                                            <a class="accordion-button p-2 shadow-none" data-bs-toggle="collapse" href="#collapseTwo" aria-expanded="false">
                                                                <div class="d-flex">
                                                                    <div class="flex-shrink-0 avatar-xs">
                                                                        <div class="avatar-title bg-light text-danger rounded-circle">
                                                                            <i class="ri-close-line"></i>
                                                                        </div>
                                                                    </div>
                                                                    <div class="flex-grow-1 ms-3">
                                                                        <h6 class="fs-14 mb-1">
                                                                            Direvisi oleh Kaizen 2
                                                                        </h6>
                                                                        <small class="text-muted"></small>
                                                                    </div>
                                                                </div>
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <div class="accordion-item border-0">
                                                    <div class="accordion-header" id="headingTwo">
                                                        <a class="accordion-button p-2 shadow-none" data-bs-toggle="collapse" href="#collapseTwo" aria-expanded="false">
                                                            <div class="d-flex">                                                       
                                                                    <div class="flex-shrink-0 avatar-xs">
                                                                        <div class="avatar-title bg-light text-success rounded-circle">
                                                                            <i class="ri-subtract-line"></i>
                                                                        </div>
                                                                    </div>
                                                                    <div class="flex-grow-1 ms-3">
                                                                        <h6 class="fs-14 mb-1">
                                                                            Menunggu Persetujuan BOD 
                                                                        </h6>
                                                                        <small class="text-muted"></small>
                                                                    </div>
                                                                </div>
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <div class="accordion-item border-0">
                                                        <div class="accordion-header" id="headingTwo">
                                                            <a class="accordion-button p-2 shadow-none" data-bs-toggle="collapse" href="#collapseTwo" aria-expanded="false">
                                                                <div class="d-flex">
                                                                    <div class="flex-shrink-0 avatar-xs">
                                                                        <div class="avatar-title bg-light text-danger rounded-circle">
                                                                            <i class="ri-close-line"></i>
                                                                        </div>
                                                                    </div>
                                                                    <div class="flex-grow-1 ms-3">
                                                                        <h6 class="fs-14 mb-1">
                                                                            Direvisi oleh BOD
                                                                        </h6>
                                                                        <small class="text-muted"></small>
                                                                    </div>
                                                                </div>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end accordion-->
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="pill-justified-profile-1" role="tabpanel">
                                            <div class="profile-timeline">
                                                <div class="accordion accordion-flush" id="todayExample">                                        
                                                    <div class="accordion-item border-0">
                                                        <div class="accordion-header" id="headingTwo">
                                                            <a class="accordion-button p-2 shadow-none" data-bs-toggle="collapse" href="#collapseTwo" aria-expanded="false">
                                                                <div class="d-flex">
                                                                    @if(empty($post->kaizenapproval->date_sup_approval))
                                                                        <div class="flex-shrink-0 avatar-xs">
                                                                            <div class="avatar-title bg-light text-success rounded-circle">
                                                                                <i class="ri-subtract-line"></i>
                                                                            </div>
                                                                        </div>
                                                                        <div class="flex-grow-1 ms-3">
                                                                            <h6 class="fs-14 mb-1">
                                                                                Menunggu Persetujuan Kepala Bagian Support : {{ $post->kaizenapproval->supapproval->name ?? '' }}
                                                                            </h6>
                                                                            <small class="text-muted"></small>
                                                                        </div>
                                                                    @else
                                                                        <div class="flex-shrink-0 avatar-xs">
                                                                            <div class="avatar-title bg-light text-success rounded-circle">
                                                                                <i class="ri-check-line"></i>
                                                                            </div>
                                                                        </div>
                                                                        <div class="flex-grow-1 ms-3">
                                                                            <h6 class="fs-14 mb-1">
                                                                                Disetujui Oleh Kepala Bagian Support : {{ $post->kaizenapproval->supapproval->name }}
                                                                            </h6>
                                                                            <small class="text-muted">{{ date('d F Y, H:m', strtotime($post->kaizenapproval->date_sup_approval)) }}</small>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </a>
                                                        </div>
                                                    </div>
                                                
                                                    <div class="accordion-item border-0">
                                                        <div class="accordion-header" id="headingTwo">
                                                            <a class="accordion-button p-2 shadow-none" data-bs-toggle="collapse" href="#collapseTwo" aria-expanded="false">
                                                                <div class="d-flex">
                                                                    @if(empty($post->kaizenapproval->date_sup_del))
                                                                        <div class="flex-shrink-0 avatar-xs">
                                                                            <div class="avatar-title bg-light text-success rounded-circle">
                                                                                <i class="ri-subtract-line"></i>
                                                                            </div>
                                                                        </div>
                                                                        <div class="flex-grow-1 ms-3">
                                                                            <h6 class="fs-14 mb-1">
                                                                                Menunggu Penyelesaian Oleh Bagian Support : {{ $post->kaizenapproval->delapproval->name ?? '' }}
                                                                            </h6>
                                                                            <small class="text-muted"></small>
                                                                        </div>
                                                                    @else
                                                                        <div class="flex-shrink-0 avatar-xs">
                                                                            <div class="avatar-title bg-light text-success rounded-circle">
                                                                                <i class="ri-check-line"></i>
                                                                            </div>
                                                                        </div>
                                                                        <div class="flex-grow-1 ms-3">
                                                                            <h6 class="fs-14 mb-1">
                                                                                Diselesaikan Oleh Bagian Support : {{ $post->empsupport->name }}
                                                                            </h6>
                                                                            <small class="text-muted">{{ date('d F Y, H:m', strtotime($post->kaizenapproval->date_sup_del)) }}</small>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end accordion-->
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <ul class="nav nav-justified mb-3" role="tablist">
                                            <li class="nav-item waves-effect waves-light">
                                                <a class="nav-link active" data-bs-toggle="tab" href="#pill-justified-home-2" role="tab">
                                                    Catatan
                                                </a>
                                            </li>
                                            <li class="nav-item waves-effect waves-light">
                                                <a class="nav-link" data-bs-toggle="tab" href="#pill-justified-profile-2" role="tab">
                                                    Log Approval
                                                </a>
                                            </li>
                                        </ul>
                                        <div class="tab-content text-muted">
                                            <div class="tab-pane active" id="pill-justified-home-2" role="tabpanel">
                                                <div class="profile-timeline">
                                                    <div class="accordion accordion-flush" id="todayExample">                                        
                                                        <div class="accordion-item border-0">
                                                            <div class="accordion-header" id="headingTwo">
                                                                <a class="accordion-button p-2 shadow-none" data-bs-toggle="collapse" href="#collapseTwo" aria-expanded="false">
                                                                    <div class="d-flex">
                                                                        <div class="flex-shrink-0 avatar-xs">
                                                                            <div class="avatar-title bg-light text-success rounded-circle">
                                                                                <i class="ri-sticky-note-fill"></i>
                                                                            </div>
                                                                        </div>
                                                                        <div class="flex-grow-1 ms-3">
                                                                            <h6 class="fs-14 mb-1">
                                                                                -
                                                                            </h6>
                                                                            <small class="text-muted">-</small>
                                                                        </div>
                                                                    </div>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!--end accordion-->
                                                </div>
                                            </div>
                                            <div class="tab-pane" id="pill-justified-profile-2" role="tabpanel">
                                                <div class="profile-timeline">
                                                    <div class="accordion accordion-flush" id="todayExample">
                                                        <div class="accordion-item border-0">
                                                            <div class="accordion-header" id="headingTwo">
                                                                <a class="accordion-button p-2 shadow-none" data-bs-toggle="collapse" href="#collapseTwo" aria-expanded="false">
                                                                    <div class="d-flex">
                                                                        <div class="flex-shrink-0 avatar-xs">
                                                                            <div class="avatar-title bg-light text-success rounded-circle">
                                                                                <i class="ri-check-line"></i>
                                                                            </div>
                                                                        </div>
                                                                        <div class="flex-grow-1 ms-3">
                                                                            <h6 class="fs-14 mb-1">
                                                                                -
                                                                            </h6>
                                                                    
                                                                            <h6 class="fs-12 mb-1">
                                                                                -
                                                                            </h6>
                                                                            <small class="text-muted">-</small>
                                                                        </div>
                                                                    </div>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!--end accordion-->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div><!-- end card-body -->
                                <div class="card-footer">
                                    <a href="{{ url()->previous() }}" class="btn btn-primary btn-animation waves-effect waves-light float-end" data-text="Back">Back</a>
                                </div>
                            </div>
                            <!--end card-->
                        </div>
                        <!--end col-->
                    </div>
                    <!--end row-->
                </div>
                <!-- end page title -->
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
<!-- profile-setting init js -->
<script src="/assets/js/pages/profile-setting.init.js"></script>
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