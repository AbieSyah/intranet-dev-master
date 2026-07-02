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
<!-- Toastr Notifications-->
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
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
    .table-responsive{
        overflow: visible;
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
                                    <ul class="nav nav-tabs nav-tabs-custom nav-success" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link py-3 active" id="data-training" data-bs-toggle="tab" href="#pill-data" role="tab">
                                                <i class="ri-file-user-line me-1 align-bottom"></i> Record Training @if($jml_record > 0 || $jml_approve > 0) <span class="badge bg-danger">!</span>@endif
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link py-3" id="ptt" data-bs-toggle="tab" href="#pill-ptt" role="tab">
                                                @if($count_jml_approve > 0)
                                                <i class="ri-file-list-3-line me-1 align-bottom"></i> Pengajuan Rencana Pelatihan Tahunan<span class="badge bg-danger align-middle ms-1">!</span>
                                                @else
                                                <i class="ri-file-list-3-line me-1 align-bottom"></i> Pengajuan Rencana Pelatihan Tahunan
                                                @endif
                                            </a>
                                        </li>  
                                        <li class="nav-item">
                                            <a class="nav-link py-3" id="pti" data-bs-toggle="tab" href="#pill-pti" role="tab">
                                                @if($count_jml_approve_pti > 0)
                                                <i class="ri-file-list-3-line me-1 align-bottom"></i> Pengajuan Pelaksanaan Pelatihan<span class="badge bg-danger align-middle ms-1">!</span>
                                                @else
                                                <i class="ri-file-list-3-line me-1 align-bottom"></i> Pengajuan Pelaksanaan Pelatihan
                                                @endif
                                            </a>
                                        </li>                                  
                                    </ul>
                                    <div class="tab-content">
                                        <div class="tab-pane active" id="pill-data" role="tabpanel">
                                            <div class="px-3 mt-4 mb-2 align-items-center d-flex">
                                                <ul class="nav nav-pills gap-2 mb-2" id="myTab" role="tablist">
                                                    <li class="nav-item" role="presentation">
                                                        <button type="button" id="tab-my-training" class="btn btn-primary border shadow list-group-item-primary active"
                                                        data-bs-toggle="tab" type="button" href="#pill-tab-my-training"
                                                        role="tab" aria-controls="pill-tab-my-training" aria-selected="true"><strong>My Training @if($jml_record > 0) <span class="badge bg-danger">{{$jml_record}}</span>@endif</strong>
                                                        </button>
                                                    </li>
                                                    <li class="nav-item" role="presentation">
                                                        <button type="button" id="tab-approved-laporan" class="btn btn-primary border shadow list-group-item-primary"
                                                        data-bs-toggle="tab" type="button" href="#pill-tab-approved-laporan"
                                                        role="tab" aria-controls="pill-tab-approved-laporan" aria-selected="false"><strong>Approve Laporan @if($jml_approve > 0) <span class="badge bg-danger">{{$jml_approve}}</span>@endif</strong>
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="tab-content">   
                                                <div class="tab-pane active" id="pill-tab-my-training" role="tabpanel">                                         
                                                    <div class="card-body">                                               
                                                        <div class="table-responsive">
                                                            <table class="table table-striped bordered" id="table_data">
                                                                <thead>
                                                                    <tr>
                                                                    <th scope="col" style="text-align:center">#</th>
                                                                    <th scope="col" style="text-align:center">Training</th>
                                                                    <th scope="col" style="text-align:center">Tanggal Mulai</th>
                                                                    <th scope="col" style="text-align:center">Tanggal Akhir</th>
                                                                    <th scope="col" style="text-align:center">Lokasi</th>
                                                                    <th scope="col" style="text-align:center">Biaya</th>
                                                                    <th scope="col" style="text-align:center">Status</th>
                                                                    <th scope="col" style="text-align:center">Laporan</th>
                                                                    <th scope="col" style="text-align:center">Action</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                </tbody>
                                                            </table>
                                                        </div>   
                                                        <!--modal right offcanvas-->
                                                        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
                                                            <div class="offcanvas-header border-bottom">
                                                                <h5 class="offcanvas-title" id="offcanvasRightLabel">Approval Laporan</h5>
                                                                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                                                            </div>
                                                            <div class="offcanvas-body p-0 overflow-hidden">
                                                                <div data-simplebar style="height: calc(100vh - 112px);">
                                                                    <div class="acitivity-timeline p-4">
                                                                        <div class="acitivity-item d-flex" id="view-atasan">                                                                         
                                                                        </div>
                                                                        <div class="acitivity-item py-3 d-flex" id="view-manager">
                                                                        </div>
                                                                        <div class="acitivity-item py-3 d-flex" id="view-gm">
                                                                        </div>
                                                                        <div class="acitivity-item py-3 d-flex" id="view-direktur">
                                                                        </div>
                                                                        <div class="acitivity-item py-3 d-flex" id="view-presiden">
                                                                        </div>
                                                                        <div class="acitivity-item py-3 d-flex" id="view-pic">
                                                                        </div>
                                                                        <div class="acitivity-item py-3 d-flex" id="view-hrd_ga_gm">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="offcanvas-foorter border p-3 text-center">
                                                                    <a href="javascript:void(0);" class="link-primary">{{date('Y')}} © INTRANET</a>
                                                                </div>
                                                            </div>
                                                        </div>                                             
                                                    </div><!-- end card body -->
                                                </div>
                                                <div class="tab-pane" id="pill-tab-approved-laporan" role="tabpanel">
                                                    <div class="card-body">                                               
                                                        <div class="table-responsive">
                                                            <table class="table table-striped bordered" id="table_laporan">
                                                                <thead>
                                                                    <tr>
                                                                    <th scope="col" style="text-align:center">#</th>
                                                                    <th scope="col" style="text-align:center">Pemohon</th>
                                                                    <th scope="col" style="text-align:center">Training</th>
                                                                    <th scope="col" style="text-align:center">Tanggal Laporan</th>
                                                                    <th scope="col" style="text-align:center">Action</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                </tbody>
                                                            </table>
                                                        </div>                                                
                                                    </div><!-- end card body -->
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="pill-ptt" role="tabpanel">
                                            <div class="px-3 mt-4 mb-2 align-items-center d-flex">
                                                <ul class="nav nav-pills gap-2 mb-4" id="myTab" role="tablist">
                                                    <li class="nav-item" role="presentation">
                                                        <button type="button" id="tab-pengajuan" class="btn btn-primary border shadow list-group-item-primary active"
                                                        data-bs-toggle="tab" type="button" href="#pill-tab-pengajuan"
                                                        role="tab" aria-controls="pill-tab-pengajuan" aria-selected="true"><strong>Pengajuan Pelatihan</strong>
                                                        </button>
                                                    </li>
                                                    <li class="nav-item" role="presentation">
                                                        <button type="button" id="tab-approved" class="btn btn-primary border shadow list-group-item-primary"
                                                        data-bs-toggle="tab" type="button" href="#pill-tab-approved"
                                                        role="tab" aria-controls="pill-tab-approved" aria-selected="false"><strong>Approve Pelatihan @if($count_jml_approve > 0)<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">{{$count_jml_approve}} <span class="visually-hidden">unread messages</span></span>@endif</strong>
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="tab-content">
                                                <div class="tab-pane active" id="pill-tab-pengajuan" role="tabpanel">
                                                    <div class="px-3 mb-2 align-items-center d-flex">
                                                        <a href="{{ route('profile.training.fkt.form.ptt') }}" class="btn btn-primary btn-label waves-effect waves-light" data-text="Buat Pengajuan">
                                                            <i class="ri-add-circle-line label-icon align-middle fs-16 me-2"> </i>Buat Pengajuan
                                                        </a>
                                                    </div>
                                                    <div class="card-body mt-2">
                                                        <div class="table-responsive">
                                                            <table class="table table-striped bordered" id="table_ptt" style="width:100%;">
                                                                <thead>
                                                                    <tr>
                                                                    <th scope="col" style="text-align:center">No Document</th>
                                                                    <th scope="col" style="text-align:center">Pemohon</th>
                                                                    <th scope="col" style="text-align:center">Peserta</th>
                                                                    <th scope="col" style="text-align:center">Total Biaya</th>
                                                                    <th scope="col" style="text-align:center">Status</th>
                                                                    <th scope="col" style="text-align:center">Action</th>
                                                                    <th scope="col" style="text-align:center"></th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <!--Modal Status-->
                                                    <div class="modal fade" id="modal-status" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                            <div class="modal-content"> 
                                                                    <div class="card-header">
                                                                        <h5 class="card-subtitle text-muted mb-0" style="text-align: center"><span id="judul"></span></h5>
                                                                    </div>  
                                                                    <div class="card-body">
                                                                        <div class="row">
                                                                            <table class="table table-borderless">
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <th scope="row"><a href="#" class="fw-semibold">Tujuan Usulan Program</a></th>
                                                                                        <td>:</td>
                                                                                        <td><span id="tujuan_usulan"></span></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <th scope="row"><a href="#" class="fw-semibold">Pemohon</a></th>
                                                                                        <td>:</td>
                                                                                        <td><span id="emp_pemohon"></span></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <th scope="row"><a href="#" class="fw-semibold">Tanggal</a></th>
                                                                                        <td>:</td>
                                                                                        <td><span id="tanggal_pemohon"></span></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <th scope="row"><a href="#" class="fw-semibold">Pelaksanaan</a></th>
                                                                                        <td>:</td>
                                                                                        <td><span id="bulan_pelaksanaan"></span> <span id="tahun_pelaksanaan"></span></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <th scope="row"><a href="#" class="fw-semibold">Status</a></th>
                                                                                        <td>:</td>
                                                                                        <td id="nama_status_fkt"></td>
                                                                                    </tr>
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                        <div class="row">
                                                                            <ul class="nav nav-tabs nav-justified nav-border-top nav-border-top-primary mb-3" role="tablist">
                                                                                <li class="nav-item">
                                                                                    <a class="nav-link active" data-bs-toggle="tab" href="#pill-justified-home-1" role="tab">
                                                                                        Status FKP
                                                                                    </a>
                                                                                </li>
                                                                                <li class="nav-item">
                                                                                    <a class="nav-link" data-bs-toggle="tab" href="#pill-justified-profile-1" role="tab">
                                                                                        Status FPKP
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
                                                                                                        <div id="status-atasan-dept" class="d-flex">
                                                                                                            
                                                                                                        </div>
                                                                                                    </a>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="accordion-item border-0">
                                                                                                <div class="accordion-header" id="headingTwo">
                                                                                                    <a class="accordion-button p-2 shadow-none" data-bs-toggle="collapse" href="#collapseTwo" aria-expanded="false">
                                                                                                        <div id="status-verified-hrd" class="d-flex">
                                                                                                            
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
                                                                                            <div class="text-center p-2">
                                                                                                <img src="{{asset('assets/images/no-data.png')}}" style="width:80px;height:80px;" />
                                                                                                <div class="mt-3">
                                                                                                    <h5 class="mb-3">Tidak ada status...</h5>
                                                                                                </div>
                                                                                            </div>                                        
                                                                                            {{-- <div class="accordion-item border-0">
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
                                                                                                                    Menunggu Persetujuan Kepala Bagian Support :
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
                                                                                                                    Menunggu Penyelesaian Oleh Bagian Support :
                                                                                                                </h6>
                                                                                                                <small class="text-muted"></small>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </a>
                                                                                                </div>
                                                                                            </div> --}}
                                                                                        </div>
                                                                                        <!--end accordion-->
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="row">
                                                                                <ul class="nav nav-tabs nav-justified nav-border-top nav-border-top-primary  mb-3" role="tablist">
                                                                                    <li class="nav-item">
                                                                                        <a class="nav-link active" data-bs-toggle="tab" href="#pill-justified-home-2" role="tab">
                                                                                            Catatan FKP
                                                                                        </a>
                                                                                    </li>
                                                                                    <li class="nav-item">
                                                                                        <a class="nav-link" data-bs-toggle="tab" href="#pill-justified-profile-2" role="tab">
                                                                                            Catatan FPKP
                                                                                        </a>
                                                                                    </li>
                                                                                </ul>
                                                                                <div class="tab-content text-muted">
                                                                                    <div class="tab-pane active" id="pill-justified-home-2" role="tabpanel">
                                                                                        <div class="profile-timeline">                                                                                            
                                                                                            <div id="ctt-fkp" class="accordion accordion-flush">                                        
                                                                                                
                                                                                            </div>
                                                                                            <!--end accordion-->
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="tab-pane" id="pill-justified-profile-2" role="tabpanel">
                                                                                        <div class="profile-timeline">
                                                                                            <div class="accordion accordion-flush" id="todayExample">
                                                                                                <div class="text-center p-2">
                                                                                                    <img src="{{asset('assets/images/no-data.png')}}" style="width:80px;height:80px;" />
                                                                                                    <div class="mt-3">
                                                                                                        <h5 class="mb-3">Tidak ada catatan...</h5>
                                                                                                    </div>
                                                                                                </div>
                                                                                                {{-- <div class="accordion-item border-0">
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
                                                                                                </div> --}}
                                                                                            </div>
                                                                                            <!--end accordion-->
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div><!-- end card-body -->
                                                                        <div class="card-footer">
                                                                            <a href="javascript:void(0);" data-bs-dismiss="modal" class="btn btn-primary btn-animation waves-effect waves-light float-end" data-text="Back">Back</a>
                                                                        </div>
                                                                    </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="tab-pane" id="pill-tab-approved" role="tabpanel">
                                                    <div class="card-body mt-2">
                                                        <div class="table-responsive">
                                                            <table class="table table-striped bordered" id="table_ptt_approved" style="width:100%;">
                                                                <thead>
                                                                    <tr>
                                                                    <th scope="col" style="text-align:center">No</th>
                                                                    <th scope="col" style="text-align:center">Tahun</th>
                                                                    <th scope="col" style="text-align:center">Total Pengajuan</th>
                                                                    <th scope="col" style="text-align:center">Action</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="pill-pti" role="tabpanel">
                                            <div class="px-3 mt-4 mb-2 align-items-center d-flex">
                                                <ul class="nav nav-pills gap-2 mb-4" id="myTab" role="tablist">
                                                    <li class="nav-item" role="presentation">
                                                        <button type="button" id="tab-pengajuan-pti" class="btn btn-primary border shadow list-group-item-primary active"
                                                        data-bs-toggle="tab" type="button" href="#pill-tab-pengajuan-pti"
                                                        role="tab" aria-controls="pill-tab-pengajuan-pti" aria-selected="true"><strong>Pengajuan Pelatihan</strong>
                                                        </button>
                                                    </li>
                                                    <li class="nav-item" role="presentation">
                                                        <button type="button" id="tab-approved-pti" class="btn btn-primary border shadow list-group-item-primary"
                                                        data-bs-toggle="tab" type="button" href="#pill-tab-approved-pti"
                                                        role="tab" aria-controls="pill-tab-approved-pti" aria-selected="false"><strong>Approve Pelatihan @if($count_jml_approve_pti > 0)<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">{{$count_jml_approve_pti}} <span class="visually-hidden">unread messages</span></span>@endif</strong>
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="tab-content">
                                                <div class="tab-pane active" id="pill-tab-pengajuan-pti" role="tabpanel">
                                                    <div class="px-3 mb-2 align-items-center d-flex">
                                                        <a href="{{ route('profile.training.fkt.form.pti') }}" class="btn btn-primary btn-label waves-effect waves-light" data-text="Buat Pengajuan">
                                                            <i class="ri-add-circle-line label-icon align-middle fs-16 me-2"> </i>Buat Pengajuan
                                                        </a>
                                                    </div>
                                                    <div class="card-body mt-2">
                                                        <div class="table-responsive">
                                                            <table class="table table-striped bordered" id="table_pti" style="width:100%;">
                                                                <thead>
                                                                    <tr>
                                                                    <th scope="col" style="text-align:center">Pelatihan</th>
                                                                    <th scope="col" style="text-align:center">Pemohon</th>
                                                                    <th scope="col" style="text-align:center">Peserta</th>
                                                                    <th scope="col" style="text-align:center">Total Biaya</th>
                                                                    <th scope="col" style="text-align:center">Status</th>
                                                                    <th scope="col" style="text-align:center">Action</th>
                                                                    <th scope="col" style="text-align:center"></th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="tab-pane" id="pill-tab-approved-pti" role="tabpanel">
                                                    <div class="card-body mt-2">
                                                        <div class="table-responsive">
                                                            <table class="table table-striped bordered" id="table_pti_approved" style="width:100%;">
                                                                <thead>
                                                                    <tr>
                                                                    <th scope="col" style="text-align:center">No</th>
                                                                    <th scope="col" style="text-align:center">Tahun</th>
                                                                    <th scope="col" style="text-align:center">Pemohon</th>
                                                                    <th scope="col" style="text-align:center">Topic</th>
                                                                    <th scope="col" style="text-align:center">Jenis</th>
                                                                    <th scope="col" style="text-align:center">Peserta</th>
                                                                    <th scope="col" style="text-align:center">Status</th>
                                                                    <th scope="col" style="text-align:center">Action</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <!-- Approve Modal -->
                                                        <div class="modal fade bs-example-modal-center" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-top">
                                                                <div class="modal-content">
                                                                    <div class="modal-body text-center p-5">
                                                                        <div class="mt-4">
                                                                            <h4 class="mb-3">Apakah Anda Yakin ?</h4>
                                                                            <form id="form-approve" method="POST" action="{{ route('profile.training.fkt.pti.approved.store') }}">
                                                                                @csrf
                                                                                @method('put')
                                                                                <input type="hidden" name="kode_judul" id="kode_judul" value="">
                                                                                <div class="hstack gap-2 justify-content-center">
                                                                                    <button type="submit" class="btn btn-primary">Ya</button>
                                                                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tidak</button>
                                                                                </div>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </div><!-- /.modal-content -->
                                                            </div><!-- /.modal-dialog -->
                                                        </div><!-- /.modal -->
                                                    </div>
                                                </div>
                                            </div>
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
<!--Modal Sertifikat-->
<div class="modal fade" id="modalSertifikat" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
          <h5 class="modal-title" id="exampleModalgridLabel">Preview certificate</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
            <div id="show-preview-sertifikat">
            </div>
      </div>
    </div>
  </div>
</div>
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
<!-- profile-setting init js -->
<script src="/assets/js/pages/profile-setting.init.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.js"></script>
<!-- Sweetalert -->
<link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<!-- Toastr Notifications-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
@endsection
@section('javascript')
@if(Session::has('tab_ptt'))
<script>
    $('#data-training').removeClass('active');
    $('#pill-data').removeClass('active');
    $('#ptt').addClass('active');
    $('#pill-ptt').addClass('active');
    $('#pengajuan-training').removeClass('active');
    $('#pill-pengajuan').removeClass('active');
    $('#verified-training').removeClass('active');
    $('#pill-verified').removeClass('active');
</script>
@endif
@if(Session::has('tab_approve_ptt'))
<script>
    $('#data-training').removeClass('active');
    $('#pill-data').removeClass('active');
    $('#ptt').addClass('active');
    $('#pill-ptt').addClass('active');
    $('#tab-pengajuan').removeClass('active');
    $('#pill-tab-pengajuan').removeClass('active');
    $('#tab-approved').addClass('active');
    $('#pill-tab-approved').addClass('active');    
    $('#pengajuan-training').removeClass('active');
    $('#pill-pengajuan').removeClass('active');
    $('#verified-training').removeClass('active');
    $('#pill-verified').removeClass('active');
</script>
@endif
@if(Session::has('tab_pti'))
<script>
    $('#data-training').removeClass('active');
    $('#pill-data').removeClass('active');
    $('#ptt').removeClass('active');
    $('#pill-ptt').removeClass('active');
    $('#pti').addClass('active');
    $('#pill-pti').addClass('active');
    $('#pengajuan-training').removeClass('active');
    $('#pill-pengajuan').removeClass('active');
    $('#verified-training').removeClass('active');
    $('#pill-verified').removeClass('active');
</script>
@endif
@if(Session::has('tab_approve_pti'))
<script>
    $('#data-training').removeClass('active');
    $('#pill-data').removeClass('active');
    $('#pti').addClass('active');
    $('#pill-pti').addClass('active');
    $('#tab-pengajuan-pti').removeClass('active');
    $('#pill-tab-pengajuan-pti').removeClass('active');
    $('#tab-approved-pti').addClass('active');
    $('#pill-tab-approved-pti').addClass('active');    
</script>
@endif
@if(Session::has('tab_approval'))
<script>
    $('#data-training').addClass('active');
    $('#pill-data').addClass('active');
    $('#tab-approved-laporan').addClass('active');
    $('#pill-tab-approved-laporan').addClass('active');
    $('#tab-my-training').removeClass('active');
    $('#pill-tab-my-training').removeClass('active');
    $('#ptt').removeClass('active');
    $('#pill-ptt').removeClass('active');
    $('#pti').removeClass('active');
    $('#pill-pti').removeClass('active');
    $('#pengajuan-training').removeClass('active');
    $('#pill-pengajuan').removeClass('active');
    $('#verified-training').removeClass('active');
    $('#pill-verified').removeClass('active');
</script>
@endif
<script>
    $("#form-approve").submit(function(e) {
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
            swalert.then(() => window.location.href = "{{ route('profile.back.fkt.approve.pti') }}")
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
          errorMessage += `<h4 class="text-danger">${responseJson.message}</h4>`;
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
<script type="text/javascript">
    $(document).ready(function() {
        //table my training
        let table_data = $('#table_data').DataTable({
            stateSave: true,
            responsive: true,
            autoWidth: false,
            processing: true,
            serverSide: true,
            ajax: "{{ route('profile.training') }}",
            columns: [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                "className": "text-center"
            },
            {
                data: 'judul',
                name: 'judul',
                "className": "text-center"
            },
            {
                data: 'start_date',
                name: 'start_date',
                "className": "text-center"
            },
            {
                data: 'end_date',
                name: 'end_date',
                "className": "text-center"
            },
            {
                data: 'lokasi',
                name: 'lokasi',
                "className": "text-center"
            },
            {
                data: 'biaya',
                name: 'biaya',
                "className": "text-center"
            },
            {
                data: 'status',
                name: 'status',
                "className": "text-center"
            },
            {
                data: 'status_laporan',
                name: 'status_laporan',
                "className": "text-center"
            },
            {
                data: 'action',
                name: 'action',
                "className": "text-center",
                orderable: false,
                searchable: false
            },
            ]
        });

        $('#table_data').on("click", ".view-btn", function() {
            var preview = $(this).data("id");
            $("#show-preview-sertifikat").html('<embed src="'+preview+'" frameborder="0" width="100%" height="450px">');
        });

        //table approved laporan
        let table_laporan = $('#table_laporan').DataTable({
            stateSave: true,
            responsive: true,
            autoWidth: false,
            processing: true,
            serverSide: true,
            ajax: "{{ route('profile.training.laporan') }}",
            columns: [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                "className": "text-center"
            },
            {
                data: 'pemohon',
                name: 'pemohon',
                "className": "text-center"
            },
            {
                data: 'judul',
                name: 'judul',
                "className": "text-center"
            },
            {
                data: 'tgl_laporan',
                name: 'tgl_laporan',
                "className": "text-center"
            },
            {
                data: 'action',
                name: 'action',
                "className": "text-center",
                orderable: false,
                searchable: false
            },
            ]
        });

        //table ptt
        let table_ptt = $('#table_ptt').DataTable({
            stateSave: true,
            responsive: true,
            autoWidth: false,
            processing: true,
            serverSide: true,
            ajax: "{{ route('profile.training.fkt.ptt') }}",
            columns: [{
                data: 'kode',
                name: 'kode',
                "className": "text-center"
            },
            {
                data: 'pemohon',
                name: 'pemohon',
                "className": "text-center"
            },
            {
                data: 'jml_peserta',
                name: 'jml_peserta',
                "className": "text-center"
            },
            {
                data: 'total_biaya',
                name: 'total_biaya',
                "className": "text-center"
            },
            {
                data: 'status',
                name: 'status',
                "className": "text-center"
            },
            {
                data: 'action',
                name: 'action',
                "className": "text-center",
                orderable: false,
                searchable: false
            },
            {
                data: 'peserta',
                name: 'peserta',
                "className": "none text-center"
            },
            ]
        });

        $('#table_ptt').on("click", ".view-status", function() {
            var kode = $(this).data("id");
            $("#status-atasan-dept").html('');
            $("#status-verified-hrd").html('');
            $.ajax({
                url: "{{ route('profile.status.fkt.ptt') }}",
                type: "POST",
                data: {
                    kode: kode,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(result) {
                    //status fkp
                    $("#judul").html(result.judul);
                    $("#tujuan_usulan").html(result.tujuan_usulan);
                    $("#emp_pemohon").html(result.nama_pemohon);
                    $("#tanggal_pemohon").html(result.date_pemohon);
                    $("#bulan_pelaksanaan").html(result.bulan_pelaksanaan);
                    $("#tahun_pelaksanaan").html(result.tahun_pelaksanaan);
                    if(result.id_status_fkt == 2){
                        $("#nama_status_fkt").html('<span class="badge badge-outline-warning">'+result.nama_status_fkt+'</span>');
                    }else if(result.id_status_fkt == 15 || result.id_status_fkt == 16){
                        $("#nama_status_fkt").html('<span class="badge badge-outline-secondary">'+result.nama_status_fkt+'</span>');
                    }else if(result.id_status_fkt == 17 || result.id_status_fkt == 18){
                        $("#nama_status_fkt").html('<span class="badge badge-outline-danger">'+result.nama_status_fkt+'</span>');
                    }else{
                        $("#nama_status_fkt").html('<span class="badge badge-outline-secondary">'+result.nama_status_fkt+'</span>');
                    }
                    if(result.date_atasan_dept == null){
                        if(result.id_status_fkt == 15){
                            $("#status-atasan-dept").html('<div class="flex-shrink-0 avatar-xs">'+
                                '<div class="avatar-title bg-light text-success rounded-circle">'+
                                    '<i class="ri-arrow-go-back-line"></i>'+
                                '</div>'+
                            '</div>'+
                            '<div class="flex-grow-1 ms-3">'+
                                '<h6 class="fs-14 mb-1">'+
                                    'Direvisi Oleh : '+result.atasan_revise_ctt.id_user+
                                '</h6>'+
                                '<small class="text-muted">'+result.atasan_revise_ctt.tgl_ctt+'</small>'+
                            '</div>');
                        }else if(result.id_status_fkt == 17){
                            $("#status-atasan-dept").html('<div class="flex-shrink-0 avatar-xs">'+
                                '<div class="avatar-title bg-light text-success rounded-circle">'+
                                    '<i class="ri-close-line"></i>'+
                                '</div>'+
                            '</div>'+
                            '<div class="flex-grow-1 ms-3">'+
                                '<h6 class="fs-14 mb-1">'+
                                    'Direject Oleh : '+result.atasan_reject_ctt.id_user+
                                '</h6>'+
                                '<small class="text-muted">'+result.atasan_reject_ctt.tgl_ctt+'</small>'+
                            '</div>');
                        }else{
                            $("#status-atasan-dept").html('<div class="flex-shrink-0 avatar-xs">'+
                                '<div class="avatar-title bg-light text-success rounded-circle">'+
                                    '<i class="ri-subtract-line"></i>'+
                                '</div>'+
                            '</div>'+
                            '<div class="flex-grow-1 ms-3">'+
                                '<h6 class="fs-14 mb-1">'+
                                    'Menunggu Persetujuan Atasan Departemen'+                                
                                '</h6>'+
                                '<small class="text-muted"></small>'+
                            '</div>');
                        }
                    }else{
                        $("#status-atasan-dept").html('<div class="flex-shrink-0 avatar-xs">'+
                            '<div class="avatar-title bg-light text-success rounded-circle">'+
                                '<i class="ri-check-line"></i>'+
                            '</div>'+
                        '</div>'+
                        '<div class="flex-grow-1 ms-3">'+
                            '<h6 class="fs-14 mb-1">'+
                                'Disetujui Oleh : '+result.atasan_dept+
                            '</h6>'+
                            '<small class="text-muted">'+result.date_atasan_dept+'</small>'+
                        '</div>');
                    }
                    if(result.date_verified_hrd == null){
                        if(result.id_status_fkt == 16){
                            $("#status-verified-hrd").html('<div class="flex-shrink-0 avatar-xs">'+
                                '<div class="avatar-title bg-light text-success rounded-circle">'+
                                    '<i class="ri-arrow-go-back-line"></i>'+
                                '</div>'+
                            '</div>'+
                            '<div class="flex-grow-1 ms-3">'+
                                '<h6 class="fs-14 mb-1">'+
                                    'Direvisi Oleh : '+result.hrd_revise_ctt.id_user+
                                '</h6>'+
                                '<small class="text-muted">'+result.hrd_revise_ctt.tgl_ctt+'</small>'+
                            '</div>');
                        }else if(result.id_status_fkt == 18){
                            $("#status-verified-hrd").html('<div class="flex-shrink-0 avatar-xs">'+
                                '<div class="avatar-title bg-light text-success rounded-circle">'+
                                    '<i class="ri-close-line"></i>'+
                                '</div>'+
                            '</div>'+
                            '<div class="flex-grow-1 ms-3">'+
                                '<h6 class="fs-14 mb-1">'+
                                    'Direject Oleh : '+result.hrd_reject_ctt.id_user+
                                '</h6>'+
                                '<small class="text-muted">'+result.hrd_reject_ctt.tgl_ctt+'</small>'+
                            '</div>');
                        }else{
                            $("#status-verified-hrd").html('<div class="flex-shrink-0 avatar-xs">'+
                                '<div class="avatar-title bg-light text-success rounded-circle">'+
                                    '<i class="ri-subtract-line"></i>'+
                                '</div>'+
                            '</div>'+
                            '<div class="flex-grow-1 ms-3">'+
                                '<h6 class="fs-14 mb-1">'+
                                    'Menunggu Verifikasi HRD'+
                                '</h6>'+
                                '<small class="text-muted"></small>'+
                            '</div>');
                        }
                    }else{
                        $("#status-verified-hrd").html('<div class="flex-shrink-0 avatar-xs">'+
                            '<div class="avatar-title bg-light text-success rounded-circle">'+
                                '<i class="ri-check-line"></i>'+
                            '</div>'+
                        '</div>'+
                        '<div class="flex-grow-1 ms-3">'+
                            '<h6 class="fs-14 mb-1">'+
                                'Diverifikasi Oleh : '+result.verified_hrd+
                            '</h6>'+
                            '<small class="text-muted">'+result.date_verified_hrd+'</small>'+
                        '</div>');
                    }
                    //catatan fkp
                    $("#ctt-fkp").html('');
                    if(result.ctt == null){
                        $("#ctt-fkp").html('<div class="text-center p-2">'+
                            '<img src="{{asset('assets/images/no-data.png')}}" style="width:80px;height:80px;" />'+
                            '<div class="mt-3">'+
                                '<h5 class="mb-3">Tidak ada catatan...</h5>'+
                            '</div>'+
                        '</div>');
                    }else{                        
                        $.each(result.ctt, function(key,val) {
                            $("#ctt-fkp").append('<div class="accordion-item border-0">'+
                                '<div class="accordion-header" id="headingTwo">'+
                                    '<a class="accordion-button p-2 shadow-none" data-bs-toggle="collapse" href="#collapseTwo" aria-expanded="false">'+
                                        '<div class="d-flex">'+
                                            '<div class="flex-shrink-0 avatar-xs">'+
                                                '<div class="avatar-title bg-light text-success rounded-circle">'+
                                                    '<i class="ri-sticky-note-fill"></i>'+
                                                '</div>'+
                                            '</div>'+
                                            '<div class="flex-grow-1 ms-3">'+
                                                '<h6 class="fs-14 mb-1">'+
                                                    val.id_user+
                                                '</h6>'+
                                                '<small class="text-muted"> Note '+val.action+' "'+val.catatan+'". '+val.tgl_ctt+'</small>'+
                                            '</div>'+
                                        '</div>'+
                                    '</a>'+
                                '</div>'+
                            '</div>');
                        });
                    }
                }
            });
        });

        //table ptt approved
        let table_ptt_approved = $('#table_ptt_approved').DataTable({
            stateSave: true,
            responsive: true,
            autoWidth: false,
            processing: true,
            serverSide: true,
            ajax: "{{ route('profile.training.fkt.ptt.approved') }}",
            columns: [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                "className": "text-center"
            },
            {
                data: 'tahun_usulan',
                name: 'tahun_usulan',
                "className": "text-center"
            },
            {
                data: 'total_pengajuan',
                name: 'total_pengajuan',
                "className": "text-center"
            },
            {
                data: 'action',
                name: 'action',
                "className": "text-center",
                orderable: false,
                searchable: false
            },
            ]
        });

        //table pti
        let table_pti = $('#table_pti').DataTable({
            stateSave: true,
            responsive: true,
            autoWidth: false,
            processing: true,
            serverSide: true,
            ajax: "{{ route('profile.training.fkt.pti') }}",
            columns: [{
                data: 'judul_fpkt',
                name: 'judul_fpkt',
                "className": "text-center"
            },
            {
                data: 'pemohon',
                name: 'pemohon',
                "className": "text-center"
            },
            {
                data: 'jml_peserta',
                name: 'jml_peserta',
                "className": "text-center"
            },
            {
                data: 'total_biaya',
                name: 'total_biaya',
                "className": "text-center"
            },
            {
                data: 'status',
                name: 'status',
                "className": "text-center"
            },
            {
                data: 'action',
                name: 'action',
                "className": "text-center",
                orderable: false,
                searchable: false
            },
            {
                data: 'peserta',
                name: 'peserta',
                "className": "none text-center"
            },
            ]
        });

        // $('#table_pti').on("click", ".view-status", function() {
        //     var kode = $(this).data("id");
        //     $.ajax({
        //         url: "{{ route('profile.status.fkt.pti') }}",
        //         type: "POST",
        //         data: {
        //             kode: kode,
        //             _token: '{{ csrf_token() }}'
        //         },
        //         dataType: 'json',
        //         success: function(result) {
        //             var url = '{{route("profile.training.fkt.pti.detail", ":id") }}';
        //             url = url.replace(':id', result.kode);
        //             if(result.status_fkt == 2){
        //                 $("#status_judul").html('<div class="ribbon ribbon-warning ribbon-shape">'+result.nama_status_fkt+'</div>');
        //                 var test = '';
        //             }else if(result.status_fkt == 4){
        //                 $("#status_judul").html('<div class="ribbon ribbon-info ribbon-shape">'+result.nama_status_fkt+'</div>');
        //                 var test = '';
        //                 // var test = '<a href="'+url+'" <span class="badge text-bg-info">Detail</span></a>';
        //             }else if(result.status_fkt == 5){
        //                 $("#status_judul").html('<div class="ribbon ribbon-secondary ribbon-shape">'+result.nama_status_fkt+'</div>');
        //                 var test = '';
        //                 // var test = '<a href="'+url+'" <span class="badge text-bg-secondary">Detail</span></a>';
        //             }else if(result.status_fkt == 6){
        //                 $("#status_judul").html('<div class="ribbon ribbon-success ribbon-shape">'+result.nama_status_fkt+'</div>');
        //                 var test = '<a href="'+url+'" <span class="badge text-bg-success">Detail</span></a>';
        //             }else if(result.status_fkt == 7){
        //                 $("#status_judul").html('<div class="ribbon ribbon-success ribbon-shape">FINISHED</div>');
        //                 var test = '<a href="'+url+'" <span class="badge text-bg-success">Detail</span></a>';
        //             }else if(result.status_fkt == 3){
        //                 $("#status_judul").html('<div class="ribbon ribbon-info ribbon-shape">'+result.nama_status_fkt+'</div>');
        //                 var test = '';
        //                 // var test = '<a href="'+url+'" <span class="badge text-bg-info">Detail</span></a>';
        //             }else{
        //                 $("#status_judul").html('<div class="ribbon ribbon-warning ribbon-shape">NO JUDUL</div>');
        //                 var test = '<a href="'+url+'" <span class="badge text-bg-info">Detail</span></a>';
        //             }
        //             if(result.cek_status > 0){
        //                 $("#status_training").html('<div class="row">'+
        //                         '<div class="table-responsive">'+
        //                         '<table class="table table-borderless table-sm table-nowrap">'+
        //                             '<tbody>'+
        //                                 '<tr>'+
        //                                     '<td scope="row">1.</td>'+
        //                                     '<td scope="row">Pemohon</td>'+
        //                                     '<td>:</td>'+
        //                                     '<td>'+result.nama_pemohon+'</td>'+
        //                                 '</tr>'+
        //                                 '<tr>'+
        //                                     '<td scope="row"></td>'+
        //                                     '<td scope="row"></td>'+
        //                                     '<td>:</td>'+
        //                                     '<td>'+result.date_pemohon+'</td>'+
        //                                 '</tr>'+
        //                                 '<tr>'+
        //                                     '<td scope="row">2.</td>'+
        //                                     '<td scope="row">Approval FPKT</td>'+
        //                                     '<td>:</td>'+
        //                                     '<td>'+ result.cek_fpkt + ' '+test+'</td>'+
        //                                 '</tr>'+
        //                                 '<tr>'+
        //                                     '<td scope="row">3.</td>'+
        //                                     '<td scope="row">Approval Kepala Bagian</td>'+
        //                                     '<td>:</td>'+
        //                                     '<td>'+result.nama_checker+'</td>'+
        //                                 '</tr>'+
        //                                 '<tr>'+
        //                                     '<td scope="row"></td>'+
        //                                     '<td scope="row"></td>'+
        //                                     '<td>:</td>'+
        //                                     '<td>'+result.date_checker+'</td>'+
        //                                 '</tr>'+
        //                             '</tbody>'+
        //                         '</table>'+
        //                         '</div>'+
        //                     '</div>'+
        //                 '</div>');
        //             }else{
        //                 $("#status_training").html('<div class="row">'+
        //                         '<div class="table-responsive">'+
        //                         '<table class="table table-borderless table-sm table-nowrap">'+
        //                             '<tbody>'+
        //                                 '<tr>'+
        //                                     '<td scope="row">1.</td>'+
        //                                     '<td scope="row">Pemohon</td>'+
        //                                     '<td>:</td>'+
        //                                     '<td>'+result.nama_pemohon+'</td>'+
        //                                 '</tr>'+
        //                                 '<tr>'+
        //                                     '<td scope="row"></td>'+
        //                                     '<td scope="row"></td>'+
        //                                     '<td>:</td>'+
        //                                     '<td>'+result.date_pemohon+'</td>'+
        //                                 '</tr>'+
        //                                 '<tr>'+
        //                                     '<td scope="row">2.</td>'+
        //                                     '<td scope="row">Approval FPKT</td>'+
        //                                     '<td>:</td>'+
        //                                     '<td>'+ result.cek_fpkt + ' '+test+'</td>'+
        //                                 '</tr>'+
        //                                 '<tr>'+
        //                                     '<td scope="row">3.</td>'+
        //                                     '<td scope="row">Approval Kepala Bagian</td>'+
        //                                     '<td>:</td>'+
        //                                     '<td>'+result.nama_checker+'</td>'+
        //                                 '</tr>'+
        //                                 '<tr>'+
        //                                     '<td scope="row"></td>'+
        //                                     '<td scope="row"></td>'+
        //                                     '<td>:</td>'+
        //                                     '<td>'+result.date_checker+'</td>'+
        //                                 '</tr>'+
        //                                 '<tr>'+
        //                                     '<td scope="row">4.</td>'+
        //                                     '<td scope="row">Approval Direktur Produksi</td>'+
        //                                     '<td>:</td>'+
        //                                     '<td>'+result.nama_verified+'</td>'+
        //                                 '</tr>'+
        //                                 '<tr>'+
        //                                     '<td scope="row"></td>'+
        //                                     '<td scope="row"></td>'+
        //                                     '<td>:</td>'+
        //                                     '<td>'+result.date_verified+'</td>'+
        //                                 '</tr>'+
        //                                 '<tr>'+
        //                                     '<td scope="row">5.</td>'+
        //                                     '<td scope="row">Approval Presiden Direktur</td>'+
        //                                     '<td>:</td>'+
        //                                     '<td>'+result.nama_approved+'</td>'+
        //                                 '</tr>'+
        //                                 '<tr>'+
        //                                     '<td scope="row"></td>'+
        //                                     '<td scope="row"></td>'+
        //                                     '<td>:</td>'+
        //                                     '<td>'+result.date_approved+'</td>'+
        //                                 '</tr>'+
        //                             '</tbody>'+
        //                         '</table>'+
        //                         '</div>'+
        //                     '</div>'+
        //                 '</div>');
        //             }
        //         }
        //     });
        // });

        // //table pti approved
        // let table_pti_approved = $('#table_pti_approved').DataTable({
        //     stateSave: true,
        //     responsive: true,
        //     autoWidth: false,
        //     processing: true,
        //     serverSide: true,
        //     ajax: "{{ route('profile.training.fkt.pti.approved') }}",
        //     columns: [{
        //         data: 'DT_RowIndex',
        //         name: 'DT_RowIndex',
        //         "className": "text-center"
        //     },
        //     {
        //         data: 'tahun_usulan',
        //         name: 'tahun_usulan',
        //         "className": "text-center"
        //     },
        //     {
        //         data: 'pemohon',
        //         name: 'pemohon',
        //         "className": "text-center"
        //     },
        //     {
        //         data: 'judul',
        //         name: 'judul',
        //         "className": "text-center"
        //     },
        //     {
        //         data: 'jenis',
        //         name: 'jenis',
        //         "className": "text-center"
        //     },
        //     {
        //         data: 'jumlah_peserta',
        //         name: 'jumlah_peserta',
        //         "className": "text-center"
        //     },
        //     {
        //         data: 'status',
        //         name: 'status',
        //         "className": "text-center"
        //     },
        //     {
        //         data: 'action',
        //         name: 'action',
        //         "className": "text-center",
        //         orderable: false,
        //         searchable: false
        //     },
        //     ]
        // });
        
        // $('#table_pti_approved').on("click", ".view-approve", function() {
        //     var kode_judul = $(this).data("id");
        //     $('#kode_judul').val(kode_judul);
        // });
    });
</script>
<script>
    $(document).ready(function () {
        $("#offcanvasRight").on("show.bs.offcanvas", function (e) {
            var data_id = $(e.relatedTarget).data('id');
            console.log(data_id)
            $.ajax({
                url: "{{route('profile.training.status.laporan')}}",
                type: "POST",
                data: {
                    id: data_id,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(result) {
                    console.log(result)
                    //ttd atasan
                    $("#view-atasan").html('');
                    if(result.status_ttd_atasan == 'Approved'){
                        var atasan_ttd = '<span class="badge bg-soft-secondary text-secondary align-middle">Approved</span>';
                    }else{
                        var atasan_ttd = '<span class="badge bg-soft-warning text-warning align-middle">Waiting Approval</span>';
                    }
                    if(result.url_atasan){
                        $("#view-atasan").append('<div class="flex-shrink-0">'+
                            '<img src="'+result.url_atasan+'" alt=""class="avatar-xs rounded-circle acitivity-avatar">'+
                        '</div>'+
                        '<div class="flex-grow-1 ms-3">'+
                            '<h6 class="mb-1">'+result.nama_atasan+' '+atasan_ttd+'</h6>'+
                            '<p class="text-muted mb-2">'+result.area_atasan+'</p>'+
                            '<p class="text-muted mb-2">'+result.departemen_atasan+'</p>'+
                            '<p class="text-muted mb-2">'+result.position_atasan+'</p>'+
                            '<small class="mb-0 text-muted">Tanggal Approve : '+result.tgl_ttd_atasan+'</small>'+
                        '</div>');            
                    }else{                                     
                        $("#view-atasan").append('<div class="flex-shrink-0 avatar-xs acitivity-avatar">'+
                            '<div class="avatar-title bg-soft-success text-success rounded-circle">'+
                                result.nama_atasan.charAt(0)+
                            '</div>'+
                        '</div>'+
                        '<div class="flex-grow-1 ms-3">'+
                            '<h6 class="mb-1">'+result.nama_atasan+' '+atasan_ttd+'</h6>'+
                            '<p class="text-muted mb-2">'+result.area_atasan+'</p>'+
                            '<p class="text-muted mb-2">'+result.departemen_atasan+'</p>'+
                            '<p class="text-muted mb-2">'+result.position_atasan+'</p>'+
                            '<small class="mb-0 text-muted">Tanggal Approve : '+result.tgl_ttd_atasan+'</small>'+
                        '</div>');     
                    }
                    //ttd manager
                    $("#view-manager").html('');
                    if(result.status_ttd_manager == 'Approved'){
                        var manager_ttd = '<span class="badge bg-soft-secondary text-secondary align-middle">Approved</span>';
                    }else{
                        var manager_ttd = '<span class="badge bg-soft-warning text-warning align-middle">Waiting Approval</span>';
                    }
                    if(result.url_manager){
                        $("#view-manager").append('<div class="flex-shrink-0">'+
                            '<img src="'+result.url_manager+'" alt=""class="avatar-xs rounded-circle acitivity-avatar">'+
                        '</div>'+
                        '<div class="flex-grow-1 ms-3">'+
                            '<h6 class="mb-1">'+result.nama_manager+' '+manager_ttd+'</h6>'+
                            '<p class="text-muted mb-2">'+result.area_manager+'</p>'+
                            '<p class="text-muted mb-2">'+result.departemen_manager+'</p>'+
                            '<p class="text-muted mb-2">'+result.position_manager+'</p>'+
                            '<small class="mb-0 text-muted">Tanggal Approve : '+result.tgl_ttd_manager+'</small>'+
                        '</div>');            
                    }else{                                     
                        $("#view-manager").append('<div class="flex-shrink-0 avatar-xs acitivity-avatar">'+
                            '<div class="avatar-title bg-soft-success text-success rounded-circle">'+
                                result.nama_manager.charAt(0)+
                            '</div>'+
                        '</div>'+
                        '<div class="flex-grow-1 ms-3">'+
                            '<h6 class="mb-1">'+result.nama_manager+' '+manager_ttd+'</h6>'+
                            '<p class="text-muted mb-2">'+result.area_manager+'</p>'+
                            '<p class="text-muted mb-2">'+result.departemen_manager+'</p>'+
                            '<p class="text-muted mb-2">'+result.position_manager+'</p>'+
                            '<small class="mb-0 text-muted">Tanggal Approve : '+result.tgl_ttd_manager+'</small>'+
                        '</div>');     
                    }
                    //ttd general manager
                    $("#view-gm").html('');
                    if(result.status_ttd_general_manager == 'Approved'){
                        var gm_ttd = '<span class="badge bg-soft-secondary text-secondary align-middle">Approved</span>';
                    }else{
                        var gm_ttd = '<span class="badge bg-soft-warning text-warning align-middle">Waiting Approval</span>';
                    }
                    if(result.url_gm){
                        $("#view-gm").append('<div class="flex-shrink-0">'+
                            '<img src="'+result.url_gm+'" alt=""class="avatar-xs rounded-circle acitivity-avatar">'+
                        '</div>'+
                        '<div class="flex-grow-1 ms-3">'+
                            '<h6 class="mb-1">'+result.nama_gm+' '+gm_ttd+'</h6>'+
                            '<p class="text-muted mb-2">'+result.area_gm+'</p>'+
                            '<p class="text-muted mb-2">'+result.departemen_gm+'</p>'+
                            '<p class="text-muted mb-2">'+result.position_gm+'</p>'+
                            '<small class="mb-0 text-muted">Tanggal Approve : '+result.tgl_ttd_general_manager+'</small>'+
                        '</div>');            
                    }else{                                     
                        $("#view-gm").append('<div class="flex-shrink-0 avatar-xs acitivity-avatar">'+
                            '<div class="avatar-title bg-soft-success text-success rounded-circle">'+
                                result.nama_gm.charAt(0)+
                            '</div>'+
                        '</div>'+
                        '<div class="flex-grow-1 ms-3">'+
                            '<h6 class="mb-1">'+result.nama_gm+' '+gm_ttd+'</h6>'+
                            '<p class="text-muted mb-2">'+result.area_gm+'</p>'+
                            '<p class="text-muted mb-2">'+result.departemen_gm+'</p>'+
                            '<p class="text-muted mb-2">'+result.position_gm+'</p>'+
                            '<small class="mb-0 text-muted">Tanggal Approve : '+result.tgl_ttd_general_manager+'</small>'+
                        '</div>');     
                    }
                    //ttd direktur
                    $("#view-direktur").html('');
                    if(result.status_ttd_direktur == 'Approved'){
                        var direktur_ttd = '<span class="badge bg-soft-secondary text-secondary align-middle">Approved</span>';
                    }else{
                        var direktur_ttd = '<span class="badge bg-soft-warning text-warning align-middle">Waiting Approval</span>';
                    }
                    if(result.url_direktur){
                        $("#view-direktur").append('<div class="flex-shrink-0">'+
                            '<img src="'+result.url_direktur+'" alt=""class="avatar-xs rounded-circle acitivity-avatar">'+
                        '</div>'+
                        '<div class="flex-grow-1 ms-3">'+
                            '<h6 class="mb-1">'+result.nama_direktur+' '+direktur_ttd+'</h6>'+
                            '<p class="text-muted mb-2">'+result.area_direktur+'</p>'+
                            '<p class="text-muted mb-2">'+result.departemen_direktur+'</p>'+
                            '<p class="text-muted mb-2">'+result.position_direktur+'</p>'+
                            '<small class="mb-0 text-muted">Tanggal Approve : '+result.tgl_ttd_direktur+'</small>'+
                        '</div>');            
                    }else{                                     
                        $("#view-direktur").append('<div class="flex-shrink-0 avatar-xs acitivity-avatar">'+
                            '<div class="avatar-title bg-soft-success text-success rounded-circle">'+
                                result.nama_direktur.charAt(0)+
                            '</div>'+
                        '</div>'+
                        '<div class="flex-grow-1 ms-3">'+
                            '<h6 class="mb-1">'+result.nama_direktur+' '+direktur_ttd+'</h6>'+
                            '<p class="text-muted mb-2">'+result.area_direktur+'</p>'+
                            '<p class="text-muted mb-2">'+result.departemen_direktur+'</p>'+
                            '<p class="text-muted mb-2">'+result.position_direktur+'</p>'+
                            '<small class="mb-0 text-muted">Tanggal Approve : '+result.tgl_ttd_direktur+'</small>'+
                        '</div>');     
                    }
                    //ttd presiden
                    $("#view-presiden").html('');
                    if(result.status_ttd_presiden == 'Approved'){
                        var presiden_ttd = '<span class="badge bg-soft-secondary text-secondary align-middle">Approved</span>';
                    }else{
                        var presiden_ttd = '<span class="badge bg-soft-warning text-warning align-middle">Waiting Approval</span>';
                    }
                    if(result.url_presiden){
                        $("#view-presiden").append('<div class="flex-shrink-0">'+
                            '<img src="'+result.url_presiden+'" alt=""class="avatar-xs rounded-circle acitivity-avatar">'+
                        '</div>'+
                        '<div class="flex-grow-1 ms-3">'+
                            '<h6 class="mb-1">'+result.nama_presiden+' '+presiden_ttd+'</h6>'+
                            '<p class="text-muted mb-2">'+result.area_presiden+'</p>'+
                            '<p class="text-muted mb-2">'+result.departemen_presiden+'</p>'+
                            '<p class="text-muted mb-2">'+result.position_presiden+'</p>'+
                            '<small class="mb-0 text-muted">Tanggal Approve : '+result.tgl_ttd_presiden+'</small>'+
                        '</div>');            
                    }else{                                     
                        $("#view-presiden").append('<div class="flex-shrink-0 avatar-xs acitivity-avatar">'+
                            '<div class="avatar-title bg-soft-success text-success rounded-circle">'+
                                result.nama_presiden.charAt(0)+
                            '</div>'+
                        '</div>'+
                        '<div class="flex-grow-1 ms-3">'+
                            '<h6 class="mb-1">'+result.nama_presiden+' '+presiden_ttd+'</h6>'+
                            '<p class="text-muted mb-2">'+result.area_presiden+'</p>'+
                            '<p class="text-muted mb-2">'+result.departemen_presiden+'</p>'+
                            '<p class="text-muted mb-2">'+result.position_presiden+'</p>'+
                            '<small class="mb-0 text-muted">Tanggal Approve : '+result.tgl_ttd_presiden+'</small>'+
                        '</div>');     
                    }
                    //ttd pic
                    $("#view-pic").html('');
                    if(result.status_ttd_pic == 'Approved'){
                        var pic_ttd = '<span class="badge bg-soft-secondary text-secondary align-middle">Approved</span>';
                    }else{
                        var pic_ttd = '<span class="badge bg-soft-warning text-warning align-middle">Waiting Approval</span>';
                    }
                    if(result.url_pic){
                        $("#view-pic").append('<div class="flex-shrink-0">'+
                            '<img src="'+result.url_pic+'" alt=""class="avatar-xs rounded-circle acitivity-avatar">'+
                        '</div>'+
                        '<div class="flex-grow-1 ms-3">'+
                            '<h6 class="mb-1">'+result.nama_pic+' '+pic_ttd+'</h6>'+
                            '<p class="text-muted mb-2">'+result.area_pic+'</p>'+
                            '<p class="text-muted mb-2">'+result.departemen_pic+'</p>'+
                            '<p class="text-muted mb-2">'+result.position_pic+'</p>'+
                            '<small class="mb-0 text-muted">Tanggal Approve : '+result.tgl_ttd_pic+'</small>'+
                        '</div>');            
                    }else{                                     
                        $("#view-pic").append('<div class="flex-shrink-0 avatar-xs acitivity-avatar">'+
                            '<div class="avatar-title bg-soft-success text-success rounded-circle">'+
                                result.nama_pic.charAt(0)+
                            '</div>'+
                        '</div>'+
                        '<div class="flex-grow-1 ms-3">'+
                            '<h6 class="mb-1">'+result.nama_pic+' '+pic_ttd+'</h6>'+
                            '<p class="text-muted mb-2">'+result.area_pic+'</p>'+
                            '<p class="text-muted mb-2">'+result.departemen_pic+'</p>'+
                            '<p class="text-muted mb-2">'+result.position_pic+'</p>'+
                            '<small class="mb-0 text-muted">Tanggal Approve : '+result.tgl_ttd_pic+'</small>'+
                        '</div>');     
                    }
                    //ttd hrd & ga general manager
                    $("#view-hrd_ga_gm").html('');
                    if(result.status_ttd_hrd_ga_gm == 'Approved'){
                        var hrd_ga_gm_ttd = '<span class="badge bg-soft-secondary text-secondary align-middle">Approved</span>';
                    }else{
                        var hrd_ga_gm_ttd = '<span class="badge bg-soft-warning text-warning align-middle">Waiting Approval</span>';
                    }
                    if(result.url_hrd_ga_gm){
                        $("#view-hrd_ga_gm").append('<div class="flex-shrink-0">'+
                            '<img src="'+result.url_hrd_ga_gm+'" alt=""class="avatar-xs rounded-circle acitivity-avatar">'+
                        '</div>'+
                        '<div class="flex-grow-1 ms-3">'+
                            '<h6 class="mb-1">'+result.nama_hrd_ga_gm+' '+hrd_ga_gm_ttd+'</h6>'+
                            '<p class="text-muted mb-2">'+result.area_hrd_ga_gm+'</p>'+
                            '<p class="text-muted mb-2">'+result.departemen_hrd_ga_gm+'</p>'+
                            '<p class="text-muted mb-2">'+result.position_hrd_ga_gm+'</p>'+
                            '<small class="mb-0 text-muted">Tanggal Approve : '+result.tgl_ttd_hrd_ga_gm+'</small>'+
                        '</div>');            
                    }else{                                     
                        $("#view-hrd_ga_gm").append('<div class="flex-shrink-0 avatar-xs acitivity-avatar">'+
                            '<div class="avatar-title bg-soft-success text-success rounded-circle">'+
                                result.nama_hrd_ga_gm.charAt(0)+
                            '</div>'+
                        '</div>'+
                        '<div class="flex-grow-1 ms-3">'+
                            '<h6 class="mb-1">'+result.nama_hrd_ga_gm+' '+hrd_ga_gm_ttd+'</h6>'+
                            '<p class="text-muted mb-2">'+result.area_hrd_ga_gm+'</p>'+
                            '<p class="text-muted mb-2">'+result.departemen_hrd_ga_gm+'</p>'+
                            '<p class="text-muted mb-2">'+result.position_hrd_ga_gm+'</p>'+
                            '<small class="mb-0 text-muted">Tanggal Approve : '+result.tgl_ttd_hrd_ga_gm+'</small>'+
                        '</div>');     
                    }
                }
            });
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