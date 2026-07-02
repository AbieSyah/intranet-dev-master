@extends('layouts.general')
@section('link')
<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- Required meta tags -->
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
<!-- Datatables-->
<link href="/assets/libs/Datatables/DataTables-1.13.1/css/dataTables.bootstrap5.css" rel="stylesheet" type="text/css" />
<link href="/assets/libs/Datatables/Buttons-2.3.3/css/buttons.dataTables.css" rel="stylesheet" type="text/css" />
<link href="/assets/libs/Datatables/Responsive-2.4.0/css/responsive.bootstrap.css" rel="stylesheet" type="text/css" />
<!-- Toastr Notifications-->
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<style>
    .table-responsive{
        overflow: visible;
    }
    .centered {
        text-align: center; /* Center text inside the div */
    }
</style>
@endsection
@section('content')
<!-- start page -->
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs nav-border-top nav-border-top-primary mb-3" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="data-training" data-bs-toggle="tab" href="#nav-border-top-data" role="tab" aria-selected="false">
                            Record Training @if($jml_record > 0 || $jml_approve > 0) <span class="badge bg-danger">!</span>@endif
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="ptt" data-bs-toggle="tab" href="#nav-border-top-ptt" role="tab" aria-selected="false">
                            Pengajuan Rencana Pelatihan Tahunan @if($count_jml_approve_ptt > 0) <span class="badge bg-danger">!</span>@endif
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="pti" data-bs-toggle="tab" href="#nav-border-top-pti" role="tab" aria-selected="false">
                            Pengajuan Pelaksanaan Pelatihan @if($count_jml_approve_pti > 0) <span class="badge bg-danger">!</span>@endif
                        </a>
                    </li>
                </ul>
                <div class="tab-content text-muted">
                    <div class="tab-pane active" id="nav-border-top-data" role="tabpanel">
                        <!-- Nav tabs -->
                        <ul class="nav nav-pills nav-custom-outline nav-primary mb-3" role="tablist">
                            <li class="nav-item waves-effect waves-light">
                                <a class="nav-link active" id="tab-my-training" data-bs-toggle="tab" href="#border-nav-tab-my-training" role="tab">My Training @if($jml_record > 0) <span class="badge bg-danger">{{$jml_record}}</span>@endif</a>
                            </li>
                            <li class="nav-item waves-effect waves-light">
                                <a class="nav-link" id="tab-approved-laporan" data-bs-toggle="tab" href="#border-nav-tab-approved-laporan" role="tab">Approve Laporan @if($jml_approve > 0) <span class="badge bg-danger">{{$jml_approve}}</span>@endif</a>
                            </li>
                        </ul>
                        <!-- Tab panes -->
                        <div class="tab-content text-muted">
                            <div class="tab-pane active" id="border-nav-tab-my-training" role="tabpanel">
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
                            </div>
                            <div class="tab-pane" id="border-nav-tab-approved-laporan" role="tabpanel">
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
                            </div>
                        </div>

                        <!--Modal Status-->
                        <div class="modal fade" id="modal-jadwal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content"> 
                                    <div class="card-header">
                                        <h5 class="card-subtitle text-muted mb-0" style="text-align: center">Jadwal Pelaksanaan Pelatihan</h5>
                                    </div>  
                                    <div class="card-body">
                                        <form id="form-jadwal" action="{{ route('training.emp.jadwal.store') }}" method="POST">
                                            @csrf
                                            @method('put')
                                            <input type="hidden" class="form-control" name="id_record" id="id_record">
                                            <div class="row mb-3">
                                                <div class="col-lg-12">
                                                    <label for="start_date" class="form-label col-form-label">Tanggal Mulai Pelaksanaan</label>
                                                    <div class="input-group">
                                                        <input type="text" name="start_date" id="start_date"
                                                            class="form-control @error('start_date') is-invalid @enderror"
                                                            placeholder="Pilih Tanggal" data-provider="flatpickr" required>
                                                        <span class="input-group-text"><i class="ri-calendar-event-line"></i></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-lg-12">
                                                    <label for="end_date" class="form-label col-form-label">Tanggal Akhir Pelaksanaan</label>
                                                    <div class="input-group">
                                                        <input type="text" name="end_date" id="end_date"
                                                            class="form-control @error('end_date') is-invalid @enderror"
                                                            placeholder="Pilih Tanggal" data-provider="flatpickr" required>
                                                        <span class="input-group-text"><i class="ri-calendar-event-line"></i></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-lg-12">
                                                    <label for="lokasi" class="form-label col-form-label">Lokasi Pelaksanaan</label>
                                                    <input type="text" class="form-control" name="lokasi" id="lokasi" placeholder="Masukkan Lokasi" required>
                                                </div>
                                            </div>
                                            <div class="d-flex float-end gap-2 mt-4">
                                                <button type="submit" class="btn btn-primary btn-animation waves-effect waves-light" data-text="Submit">Submit</button>
                                                <a href="javascript:void(0);" data-bs-dismiss="modal" class="btn btn-primary btn-animation waves-effect waves-light" data-text="Back">Back</a>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="nav-border-top-ptt" role="tabpanel">
                        <!-- Nav tabs -->
                        <ul class="nav nav-pills nav-custom-outline nav-primary mb-3" role="tablist">
                            <li class="nav-item waves-effect waves-light">
                                <a class="nav-link active" id="tab-pengajuan-ptt" data-bs-toggle="tab" href="#border-nav-tab-pengajuan-ptt" role="tab">Pengajuan Pelatihan</a>
                            </li>
                            <li class="nav-item waves-effect waves-light">
                                <a class="nav-link" id="tab-approved-ptt" data-bs-toggle="tab" href="#border-nav-tab-approved-ptt" role="tab">Approve Pelatihan @if($count_jml_approve_ptt > 0)<span class="badge bg-danger">{{$count_jml_approve_ptt}}</span>@endif</a>
                            </li>
                        </ul>
                        <!-- Tab panes -->
                        <div class="tab-content text-muted">
                            <div class="tab-pane active" id="border-nav-tab-pengajuan-ptt" role="tabpanel">
                                <div class="px-3 mb-4 align-items-center d-flex">
                                    <a href="{{ route('training.emp.fkt.ptt.create') }}" class="btn btn-primary btn-label waves-effect waves-light" data-text="Buat Pengajuan">
                                        <i class="ri-add-circle-line label-icon align-middle fs-16 me-2"> </i>Buat Pengajuan
                                    </a>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-striped bordered" id="table_ptt" style="width:100%;">
                                        <thead>
                                            <tr>
                                            <th scope="col" style="text-align:center">Tanggal Pengajuan</th>
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
                                <!--Modal Status FKP-->
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
                                                            {{-- <li class="nav-item">
                                                                <a class="nav-link" data-bs-toggle="tab" href="#pill-justified-profile-1" role="tab">
                                                                    Status FPKP
                                                                </a>
                                                            </li> --}}
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
                                                            {{-- <div class="tab-pane" id="pill-justified-profile-1" role="tabpanel">
                                                                <div class="profile-timeline">
                                                                    <div class="accordion accordion-flush" id="todayExample">
                                                                        <div class="text-center p-2">
                                                                            <img src="{{asset('assets/images/no-data.png')}}" style="width:80px;height:80px;" />
                                                                            <div class="mt-3">
                                                                                <h5 class="mb-3">Tidak ada status...</h5>
                                                                            </div>
                                                                        </div>                                        
                                                                    </div>
                                                                    <!--end accordion-->
                                                                </div>
                                                            </div> --}}
                                                        </div>
                                                        <ul class="nav nav-tabs nav-justified nav-border-top nav-border-top-primary  mb-3" role="tablist">
                                                            <li class="nav-item">
                                                                <a class="nav-link active" data-bs-toggle="tab" href="#pill-justified-home-2" role="tab">
                                                                    Catatan FKP
                                                                </a>
                                                            </li>
                                                            {{-- <li class="nav-item">
                                                                <a class="nav-link" data-bs-toggle="tab" href="#pill-justified-profile-2" role="tab">
                                                                    Catatan FPKP
                                                                </a>
                                                            </li> --}}
                                                        </ul>
                                                        <div class="tab-content text-muted">
                                                            <div class="tab-pane active" id="pill-justified-home-2" role="tabpanel">
                                                                <div class="profile-timeline">                                                                                            
                                                                    <div id="ctt-fkp" class="accordion accordion-flush">                                        
                                                                        
                                                                    </div>
                                                                    <!--end accordion-->
                                                                </div>
                                                            </div>
                                                            {{-- <div class="tab-pane" id="pill-justified-profile-2" role="tabpanel">
                                                                <div class="profile-timeline">
                                                                    <div class="accordion accordion-flush" id="todayExample">
                                                                        <div class="text-center p-2">
                                                                            <img src="{{asset('assets/images/no-data.png')}}" style="width:80px;height:80px;" />
                                                                            <div class="mt-3">
                                                                                <h5 class="mb-3">Tidak ada catatan...</h5>
                                                                            </div>
                                                                        </div>
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
                                                            </div> --}}
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
                            <div class="tab-pane" id="border-nav-tab-approved-ptt" role="tabpanel">
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
                    <div class="tab-pane" id="nav-border-top-pti" role="tabpanel">
                        <!-- Nav tabs -->
                        <ul class="nav nav-pills nav-custom-outline nav-primary mb-3" role="tablist">
                            <li class="nav-item waves-effect waves-light">
                                <a class="nav-link active" id="tab-pengajuan-pti" data-bs-toggle="tab" href="#border-nav-tab-pengajuan-pti" role="tab">Pengajuan Pelatihan</a>
                            </li>
                            <li class="nav-item waves-effect waves-light">
                                <a class="nav-link" id="tab-approved-pti" data-bs-toggle="tab" href="#border-nav-tab-approved-pti" role="tab">Approve Pelatihan @if($count_jml_approve_pti > 0)<span class="badge bg-danger">{{$count_jml_approve_pti}}</span>@endif</a>
                            </li>
                        </ul>
                        <!-- Tab panes -->
                        <div class="tab-content text-muted">
                            <div class="tab-pane active" id="border-nav-tab-pengajuan-pti" role="tabpanel">
                                <div class="px-3 mb-4 align-items-center d-flex">
                                    {{-- <a href="{{ route('training.emp.fkt.pti.create') }}" class="btn btn-primary btn-label waves-effect waves-light" data-text="Buat Pengajuan">
                                        <i class="ri-add-circle-line label-icon align-middle fs-16 me-2"> </i>Buat Pengajuan
                                    </a> --}}
                                    <button type="button" class="btn btn-primary btn-label waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#myModal"><i class="ri-add-circle-line label-icon align-middle fs-16 me-2"> </i>Buat Pengajuan</button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-striped bordered" id="table_pti" style="width:100%;">
                                        <thead>
                                            <tr>
                                            <th scope="col" style="text-align:center">Pelatihan</th>
                                            <th scope="col" style="text-align:center">Provider</th>
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
                                <!-- Default Modals -->                               
                                <div id="myModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{route('training.emp.fkt.pti.select.create')}}" method="POST">
                                                @csrf
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="myModalLabel">Pilih anda sebagai apa?</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row px-5">
                                                        <div class="col-lg-6">
                                                            <center>
                                                                <img src="{{ asset('assets/images/peserta.png') }}" alt="" class="avatar-xl">
                                                            </center>
                                                            <!-- Base Radios -->
                                                            <div class="centered mt-2 mb-2">
                                                                <input class="form-check-input" type="radio" name="cek_radio" id="input_peserta" value="peserta">
                                                                <label class="form-check-label" for="label-peserta">
                                                                    Peserta
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <center>
                                                                <img src="{{ asset('assets/images/pemohon.png') }}" alt="" class="avatar-xl">
                                                            </center>
                                                            <!-- Base Radios -->
                                                            <div class="centered mt-2 mb-2">
                                                                <input class="form-check-input" type="radio" name="cek_radio" id="input_pemohon" value="pemohon">
                                                                <label class="form-check-label" for="label-pemohon">
                                                                    Pemohon
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- Radio Buttons -->
                                                </div>
                                                <div class="modal-footer">
                                                        <button type="submit" id="buat-formulir" class="btn btn-primary float-end">Buat Formulir</button>
                                                </div>
                                            </form>
                                        </div><!-- /.modal-content -->
                                    </div><!-- /.modal-dialog -->
                                </div><!-- /.modal --> 
                                <!--Modal Status-->
                                {{--<div class="modal fade" id="modal-status-fpkt" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content"> 
                                            <div class="card-header">
                                                <h5 class="card-subtitle text-muted mb-0" style="text-align: center"><span id="judul_fpkt"></span></h5>
                                            </div>  
                                            <div class="card-body">
                                                <div class="row">
                                                    <table class="table table-borderless">
                                                        <tbody>
                                                            <tr>
                                                                <th scope="row"><a href="#" class="fw-semibold">Tujuan Usulan Program</a></th>
                                                                <td>:</td>
                                                                <td><span id="tujuan_usulan_fpkt"></span></td>
                                                            </tr>
                                                            <tr>
                                                                <th scope="row"><a href="#" class="fw-semibold">Pemohon</a></th>
                                                                <td>:</td>
                                                                <td><span id="emp_pemohon_fpkt"></span></td>
                                                            </tr>
                                                            <tr>
                                                                <th scope="row"><a href="#" class="fw-semibold">Tanggal</a></th>
                                                                <td>:</td>
                                                                <td><span id="tanggal_pemohon_fpkt"></span></td>
                                                            </tr>
                                                            <tr>
                                                                <th scope="row"><a href="#" class="fw-semibold">Pelaksanaan</a></th>
                                                                <td>:</td>
                                                                <td><span id="date_pelaksanaan_fpkt"></span></td>
                                                            </tr>
                                                            <tr>
                                                                <th scope="row"><a href="#" class="fw-semibold">Status</a></th>
                                                                <td>:</td>
                                                                <td id="nama_status_fpkt"></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="row">
                                                    <ul class="nav nav-tabs nav-justified nav-border-top nav-border-top-primary mb-3" role="tablist">
                                                        <li class="nav-item">
                                                            <a class="nav-link active" data-bs-toggle="tab" href="#pill-justified-home-1" role="tab">
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
                                                                                <div id="status-peserta-fpkt" class="d-flex">
                                                                                    
                                                                                </div>
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                    <div class="accordion-item border-0">
                                                                        <div class="accordion-header" id="headingTwo">
                                                                            <a class="accordion-button p-2 shadow-none" data-bs-toggle="collapse" href="#collapseTwo" aria-expanded="false">
                                                                                <div id="status-atasan-fpkt" class="d-flex">
                                                                                    
                                                                                </div>
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                    <div class="accordion-item border-0">
                                                                        <div class="accordion-header" id="headingTwo">
                                                                            <a class="accordion-button p-2 shadow-none" data-bs-toggle="collapse" href="#collapseTwo" aria-expanded="false">
                                                                                <div id="status-atasan-dept-fpkt" class="d-flex">
                                                                                    
                                                                                </div>
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                    <div class="accordion-item border-0">
                                                                        <div class="accordion-header" id="headingTwo">
                                                                            <a class="accordion-button p-2 shadow-none" data-bs-toggle="collapse" href="#collapseTwo" aria-expanded="false">
                                                                                <div id="status-verified-hrd-fpkt" class="d-flex">
                                                                                    
                                                                                </div>
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                    <div class="accordion-item border-0">
                                                                        <div class="accordion-header" id="headingTwo">
                                                                            <a class="accordion-button p-2 shadow-none" data-bs-toggle="collapse" href="#collapseTwo" aria-expanded="false">
                                                                                <div id="status-bod1-fpkt" class="d-flex">
                                                                                    
                                                                                </div>
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                    <div class="accordion-item border-0">
                                                                        <div class="accordion-header" id="headingTwo">
                                                                            <a class="accordion-button p-2 shadow-none" data-bs-toggle="collapse" href="#collapseTwo" aria-expanded="false">
                                                                                <div id="status-bod2-fpkt" class="d-flex">
                                                                                    
                                                                                </div>
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <!--end accordion-->
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <ul class="nav nav-tabs nav-justified nav-border-top nav-border-top-primary  mb-3" role="tablist">
                                                        <li class="nav-item">
                                                            <a class="nav-link active" data-bs-toggle="tab" href="#pill-justified-home-2" role="tab">
                                                                Catatan FPKP
                                                            </a>
                                                        </li>
                                                    </ul>
                                                    <div class="tab-content text-muted">
                                                        <div class="tab-pane active" id="pill-justified-home-2" role="tabpanel">
                                                            <div class="profile-timeline">                                                                                            
                                                                <div id="ctt-fpkt" class="accordion accordion-flush">                                        
                                                                    
                                                                </div>
                                                                <!--end accordion-->
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
                                </div>--}}
                            </div>
                            <div class="tab-pane" id="border-nav-tab-approved-pti" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-striped bordered" id="table_pti_approved" style="width:100%;">
                                        <thead>
                                            <tr>
                                                <th scope="col" style="text-align:center">No</th>
                                                <th scope="col" style="text-align:center">Pelatihan</th>
                                                <th scope="col" style="text-align:center">Peserta</th>
                                                <th scope="col" style="text-align:center">Provider</th>
                                                <th scope="col" style="text-align:center">Biaya</th>
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
                                                    <form id="form-approve" method="POST" action="{{ route('training.emp.fpkt.pti.approved.store') }}">
                                                        @csrf
                                                        @method('put')
                                                        <input type="hidden" name="id_fpkt" id="id_fpkt" value="">
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
                                <!--Modal Status-->
                                <div class="modal fade" id="modal-status-fpkt" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content"> 
                                            <div class="card-header">
                                                <h5 class="card-subtitle text-muted mb-0" style="text-align: center"><span id="judul_fpkt"></span></h5>
                                            </div>  
                                            <div class="card-body">
                                                <div class="row">
                                                    <table class="table table-borderless">
                                                        <tbody>
                                                            <tr>
                                                                <th scope="row"><a href="#" class="fw-semibold">Tujuan Usulan Program</a></th>
                                                                <td>:</td>
                                                                <td><span id="tujuan_usulan_fpkt"></span></td>
                                                            </tr>
                                                            <tr>
                                                                <th scope="row"><a href="#" class="fw-semibold">Pemohon</a></th>
                                                                <td>:</td>
                                                                <td><span id="emp_pemohon_fpkt"></span></td>
                                                            </tr>
                                                            <tr>
                                                                <th scope="row"><a href="#" class="fw-semibold">Tanggal</a></th>
                                                                <td>:</td>
                                                                <td><span id="tanggal_pemohon_fpkt"></span></td>
                                                            </tr>
                                                            <tr>
                                                                <th scope="row"><a href="#" class="fw-semibold">Pelaksanaan</a></th>
                                                                <td>:</td>
                                                                <td><span id="date_pelaksanaan_fpkt"></span></td>
                                                            </tr>
                                                            <tr>
                                                                <th scope="row"><a href="#" class="fw-semibold">Status</a></th>
                                                                <td>:</td>
                                                                <td id="nama_status_fpkt"></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="row">
                                                    <ul class="nav nav-tabs nav-justified nav-border-top nav-border-top-primary mb-3" role="tablist">
                                                        <li class="nav-item">
                                                            <a class="nav-link active" data-bs-toggle="tab" href="#pill-justified-home-1" role="tab">
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
                                                                                <div id="status-peserta-fpkt" class="d-flex">
                                                                                    
                                                                                </div>
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                    <div class="accordion-item border-0">
                                                                        <div class="accordion-header" id="headingTwo">
                                                                            <a class="accordion-button p-2 shadow-none" data-bs-toggle="collapse" href="#collapseTwo" aria-expanded="false">
                                                                                <div id="status-atasan-fpkt" class="d-flex">
                                                                                    
                                                                                </div>
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                    <div class="accordion-item border-0">
                                                                        <div class="accordion-header" id="headingTwo">
                                                                            <a class="accordion-button p-2 shadow-none" data-bs-toggle="collapse" href="#collapseTwo" aria-expanded="false">
                                                                                <div id="status-atasan-dept-fpkt" class="d-flex">
                                                                                    
                                                                                </div>
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                    <div class="accordion-item border-0">
                                                                        <div class="accordion-header" id="headingTwo">
                                                                            <a class="accordion-button p-2 shadow-none" data-bs-toggle="collapse" href="#collapseTwo" aria-expanded="false">
                                                                                <div id="status-verified-hrd-fpkt" class="d-flex">
                                                                                    
                                                                                </div>
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                    <div class="accordion-item border-0">
                                                                        <div class="accordion-header" id="headingTwo">
                                                                            <a class="accordion-button p-2 shadow-none" data-bs-toggle="collapse" href="#collapseTwo" aria-expanded="false">
                                                                                <div id="status-bod1-fpkt" class="d-flex">
                                                                                    
                                                                                </div>
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                    <div class="accordion-item border-0">
                                                                        <div class="accordion-header" id="headingTwo">
                                                                            <a class="accordion-button p-2 shadow-none" data-bs-toggle="collapse" href="#collapseTwo" aria-expanded="false">
                                                                                <div id="status-bod2-fpkt" class="d-flex">
                                                                                    
                                                                                </div>
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <!--end accordion-->
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <ul class="nav nav-tabs nav-justified nav-border-top nav-border-top-primary  mb-3" role="tablist">
                                                        <li class="nav-item">
                                                            <a class="nav-link active" data-bs-toggle="tab" href="#pill-justified-home-2" role="tab">
                                                                Catatan FPKP
                                                            </a>
                                                        </li>
                                                    </ul>
                                                    <div class="tab-content text-muted">
                                                        <div class="tab-pane active" id="pill-justified-home-2" role="tabpanel">
                                                            <div class="profile-timeline">                                                                                            
                                                                <div id="ctt-fpkt" class="accordion accordion-flush">                                        
                                                                    
                                                                </div>
                                                                <!--end accordion-->
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
                        </div>
                    </div>
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
<!-- Sweetalert -->
<link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<!-- Toastr Notifications-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
@endsection
@section('javascript')
@if(Session::has('tab_approval'))
<script>
    $('#data-training').addClass('active');
    $('#nav-border-top-data').addClass('active');
    $('#tab-approved-laporan').addClass('active');
    $('#border-nav-tab-approved-laporan').addClass('active');
    $('#tab-my-training').removeClass('active');
    $('#border-nav-tab-my-training').removeClass('active');
    $('#ptt').removeClass('active');
    $('#nav-border-top-ptt').removeClass('active');
    $('#pti').removeClass('active');
    $('#nav-border-top-pti').removeClass('active');
</script>
@endif
@if(Session::has('tab_ptt'))
<script>
    $('#ptt').addClass('active');
    $('#nav-border-top-ptt').addClass('active');
    $('#tab-pengajuan-ptt').addClass('active');
    $('#border-nav-tab-pengajuan-ptt').addClass('active');
    $('#tab-approved-ptt').removeClass('active');
    $('#border-nav-tab-approved-ptt').removeClass('active');
    $('#data-training').removeClass('active');
    $('#nav-border-top-data').removeClass('active');
    $('#pti').removeClass('active');
    $('#nav-border-top-pti').removeClass('active');
    $('#tab-pengajuan-pti').addClass('active');
    $('#border-nav-tab-pengajuan-pti').addClass('active');
    $('#tab-approved-pti').removeClass('active');
    $('#border-nav-tab-approved-pti').removeClass('active');
</script>
@endif
@if(Session::has('tab_approve_ptt'))
<script>
    $('#ptt').addClass('active');
    $('#nav-border-top-ptt').addClass('active');
    $('#tab-pengajuan-ptt').removeClass('active');
    $('#border-nav-tab-pengajuan-ptt').removeClass('active');
    $('#tab-approved-ptt').addClass('active');
    $('#border-nav-tab-approved-ptt').addClass('active');
    $('#data-training').removeClass('active');
    $('#nav-border-top-data').removeClass('active');
    $('#pti').removeClass('active');
    $('#nav-border-top-pti').removeClass('active');
    $('#tab-pengajuan-pti').removeClass('active');
    $('#border-nav-tab-pengajuan-pti').removeClass('active');
    $('#tab-approved-pti').removeClass('active');
    $('#border-nav-tab-approved-pti').removeClass('active');
</script>
@endif
@if(Session::has('tab_pti'))
<script>
    $('#pti').addClass('active');
    $('#nav-border-top-pti').addClass('active');
    $('#tab-pengajuan-pti').addClass('active');
    $('#border-nav-tab-pengajuan-pti').addClass('active');
    $('#tab-approved-pti').removeClass('active');
    $('#border-nav-tab-approved-pti').removeClass('active');
    $('#data-training').removeClass('active');
    $('#nav-border-top-data').removeClass('active');
    $('#ptt').removeClass('active');
    $('#nav-border-top-ptt').removeClass('active');
    $('#tab-pengajuan-ptt').addClass('active');
    $('#border-nav-tab-pengajuan-ptt').addClass('active');
    $('#tab-approved-ptt').removeClass('active');
    $('#border-nav-tab-approved-ptt').removeClass('active');
</script>
@endif
@if(Session::has('tab_approve_pti'))
<script>
    $('#pti').addClass('active');
    $('#nav-border-top-pti').addClass('active');
    $('#tab-pengajuan-pti').removeClass('active');
    $('#border-nav-tab-pengajuan-pti').removeClass('active');
    $('#tab-approved-pti').addClass('active');
    $('#border-nav-tab-approved-pti').addClass('active');
    $('#data-training').removeClass('active');
    $('#nav-border-top-data').removeClass('active');
    $('#ptt').removeClass('active');
    $('#nav-border-top-ptt').removeClass('active');
    $('#tab-pengajuan-ptt').addClass('active');
    $('#border-nav-tab-pengajuan-ptt').addClass('active');
    $('#tab-approved-ptt').removeClass('active');
    $('#border-nav-tab-approved-ptt').removeClass('active');
</script>
@endif
<script>
    $('#buat-formulir').prop('disabled', true);
    $("#myModal").on('hide.bs.modal', function(){
        $('#buat-formulir').prop('disabled', true);
        if($('#input_peserta').is(':checked')){
            $('#input_peserta').prop('checked', false);
        }
        if($('#input_pemohon').is(':checked')){
            $('#input_pemohon').prop('checked', false);
        }
    });
    
    $("input[name='cek_radio']").click(function() {
        $('#buat-formulir').prop('disabled', false);
    });
</script>
<script>
    $(document).ready(function() {
        $('#start_date').flatpickr({
            allowInput: true,
            altInput: true,
            altFormat: "d F, Y",
            dateFormat: "Y-m-d",
        }); 
        $('#end_date').flatpickr({
            allowInput: true,
            altInput: true,
            altFormat: "d F, Y",
            dateFormat: "Y-m-d",
        }); 
    });
</script>
<script>
    $(document).ready(function() {
        //table my training
        let table_data = $('#table_data').DataTable({
            stateSave: true,
            responsive: true,
            autoWidth: false,
            processing: true,
            serverSide: true,
            ajax: "{{ route('training.emp.index') }}",
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
        $('#table_data').on("click", ".jadwal-btn", function() {
            var id_record = $(this).data("id");
            $('#id_record').val(id_record);
        });
        //table approve laporan
        let table_laporan = $('#table_laporan').DataTable({
            stateSave: true,
            responsive: true,
            autoWidth: false,
            processing: true,
            serverSide: true,
            ajax: "{{ route('training.emp.index.laporan') }}",
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
            ajax: "{{ route('training.emp.index.fkt.ptt') }}",
            columns: [{
                data: 'date_pemohon',
                name: 'date_pemohon',
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
                url: "{{ route('training.emp.fkt.ptt.status') }}",
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
            ajax: "{{ route('training.emp.fkt.ptt.approved') }}",
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
            ajax: "{{ route('training.emp.index.fkt.pti') }}",
            columns: [{
                data: 'judul_fpkt',
                name: 'judul_fpkt',
                "className": "text-center"
            },
            {
                data: 'vendor',
                name: 'vendor',
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

                    
        //table pti approved
        let table_pti_approved = $('#table_pti_approved').DataTable({
            stateSave: true,
            responsive: true,
            autoWidth: false,
            processing: true,
            serverSide: true,
            ajax: "{{ route('training.emp.fkt.pti.approved') }}",
            columns: [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                "className": "text-center"
            },
            {
                data: 'pelatihan',
                name: 'pelatihan',
                "className": "text-center"
            },
            {
                data: 'peserta',
                name: 'peserta',
                "className": "text-center"
            },
            {
                data: 'provider',
                name: 'provider',
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
                data: 'action',
                name: 'action',
                "className": "text-center",
                orderable: false,
                searchable: false
            }
            ]
        });

        $('#table_pti_approved').on("click", ".view-approve", function() {
            var id_fpkt = $(this).data("id");
            $('#id_fpkt').val(id_fpkt);
        });

        $('#table_pti_approved').on("click", ".view-status-fpkt", function() {
            var kode = $(this).data("id");
            $.ajax({
                url: "{{ route('training.emp.fkt.pti.status') }}",
                type: "POST",
                data: {
                    kode: kode,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(result) {
                   //status fkp
                   $("#judul_fpkt").html(result.judul_fpkt);
                    $("#tujuan_usulan_fpkt").html(result.tujuan_usulan_fpkt);
                    $("#emp_pemohon_fpkt").html(result.nama_pemohon_fpkt);
                    $("#tanggal_pemohon_fpkt").html(result.date_pemohon_fpkt);
                    $("#date_pelaksanaan_fpkt").html(result.date_pelaksanaan_fpkt);
                    if(result.id_status_fpkt == 9){
                        $("#nama_status_fpkt").html('<span class="badge badge-outline-warning">'+result.nama_status_fpkt+'</span>');
                    }
                    if(result.id_status_fpkt == 10){
                        $("#nama_status_fpkt").html('<span class="badge badge-outline-secondary">'+result.nama_status_fpkt+'</span>');
                    }
                    if(result.date_peserta_fpkt == null){
                        $("#status-peserta-fpkt").html('<div class="flex-shrink-0 avatar-xs">'+
                            '<div class="avatar-title bg-light text-success rounded-circle">'+
                                '<i class="ri-subtract-line"></i>'+
                            '</div>'+
                        '</div>'+
                        '<div class="flex-grow-1 ms-3">'+
                            '<h6 class="fs-14 mb-1">'+
                                'Menunggu Penilaian : '+result.id_peserta_fpkt+                                
                            '</h6>'+
                            '<small class="text-muted"></small>'+
                        '</div>');  
                    }else{
                        $("#status-peserta-fpkt").html('<div class="flex-shrink-0 avatar-xs">'+
                            '<div class="avatar-title bg-light text-success rounded-circle">'+
                                '<i class="ri-check-line"></i>'+
                            '</div>'+
                        '</div>'+
                        '<div class="flex-grow-1 ms-3">'+
                            '<h6 class="fs-14 mb-1">'+
                                'Penilaian Oleh : '+result.id_peserta_fpkt+
                            '</h6>'+
                            '<small class="text-muted">'+result.date_peserta_fpkt+'</small>'+
                        '</div>');
                    }

                    if(result.date_atasan_fpkt == null){
                        $("#status-atasan-fpkt").html('<div class="flex-shrink-0 avatar-xs">'+
                            '<div class="avatar-title bg-light text-success rounded-circle">'+
                                '<i class="ri-subtract-line"></i>'+
                            '</div>'+
                        '</div>'+
                        '<div class="flex-grow-1 ms-3">'+
                            '<h6 class="fs-14 mb-1">'+
                                'Menunggu Penilaian Atasan Langsung'+                                
                            '</h6>'+
                            '<small class="text-muted"></small>'+
                        '</div>');  
                    }else{
                        $("#status-atasan-fpkt").html('<div class="flex-shrink-0 avatar-xs">'+
                            '<div class="avatar-title bg-light text-success rounded-circle">'+
                                '<i class="ri-check-line"></i>'+
                            '</div>'+
                        '</div>'+
                        '<div class="flex-grow-1 ms-3">'+
                            '<h6 class="fs-14 mb-1">'+
                                'Penilaian Oleh : '+result.id_atasan_fpkt+
                            '</h6>'+
                            '<small class="text-muted">'+result.date_atasan_fpkt+'</small>'+
                        '</div>');
                    }

                    if(result.date_atasan_dept_fpkt == null){
                        $("#status-atasan-dept-fpkt").html('<div class="flex-shrink-0 avatar-xs">'+
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
                    }else{
                        $("#status-atasan-dept-fpkt").html('<div class="flex-shrink-0 avatar-xs">'+
                            '<div class="avatar-title bg-light text-success rounded-circle">'+
                                '<i class="ri-check-line"></i>'+
                            '</div>'+
                        '</div>'+
                        '<div class="flex-grow-1 ms-3">'+
                            '<h6 class="fs-14 mb-1">'+
                                'Disetujui Oleh : '+result.atasan_dept_fpkt+
                            '</h6>'+
                            '<small class="text-muted">'+result.date_atasan_dept_fpkt+'</small>'+
                        '</div>');
                    }

                    if(result.date_verified_hrd_fpkt == null){
                        $("#status-verified-hrd-fpkt").html('<div class="flex-shrink-0 avatar-xs">'+
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
                    }else{
                        $("#status-verified-hrd-fpkt").html('<div class="flex-shrink-0 avatar-xs">'+
                            '<div class="avatar-title bg-light text-success rounded-circle">'+
                                '<i class="ri-check-line"></i>'+
                            '</div>'+
                        '</div>'+
                        '<div class="flex-grow-1 ms-3">'+
                            '<h6 class="fs-14 mb-1">'+
                                'Diverifikasi Oleh : '+result.verified_hrd_fpkt+
                            '</h6>'+
                            '<small class="text-muted">'+result.date_verified_hrd_fpkt+'</small>'+
                        '</div>');
                    }

                    if(result.date_bod1_fpkt == null){
                        $("#status-bod1-fpkt").html('<div class="flex-shrink-0 avatar-xs">'+
                            '<div class="avatar-title bg-light text-success rounded-circle">'+
                                '<i class="ri-subtract-line"></i>'+
                            '</div>'+
                        '</div>'+
                        '<div class="flex-grow-1 ms-3">'+
                            '<h6 class="fs-14 mb-1">'+
                                'Menunggu Persetujuan BOD 1'+
                            '</h6>'+
                            '<small class="text-muted"></small>'+
                        '</div>');
                    }else{
                        $("#status-bod1-fpkt").html('<div class="flex-shrink-0 avatar-xs">'+
                            '<div class="avatar-title bg-light text-success rounded-circle">'+
                                '<i class="ri-check-line"></i>'+
                            '</div>'+
                        '</div>'+
                        '<div class="flex-grow-1 ms-3">'+
                            '<h6 class="fs-14 mb-1">'+
                                'Disetujui Oleh : '+result.bod1_fpkt+
                            '</h6>'+
                            '<small class="text-muted">'+result.date_bod1_fpkt+'</small>'+
                        '</div>');
                    }
                    if(result.date_bod2_fpkt == null){
                        $("#status-bod2-fpkt").html('<div class="flex-shrink-0 avatar-xs">'+
                            '<div class="avatar-title bg-light text-success rounded-circle">'+
                                '<i class="ri-subtract-line"></i>'+
                            '</div>'+
                        '</div>'+
                        '<div class="flex-grow-1 ms-3">'+
                            '<h6 class="fs-14 mb-1">'+
                                'Menunggu Persetujuan BOD 2'+
                            '</h6>'+
                            '<small class="text-muted"></small>'+
                        '</div>');
                    }else{
                        $("#status-bod2-fpkt").html('<div class="flex-shrink-0 avatar-xs">'+
                            '<div class="avatar-title bg-light text-success rounded-circle">'+
                                '<i class="ri-check-line"></i>'+
                            '</div>'+
                        '</div>'+
                        '<div class="flex-grow-1 ms-3">'+
                            '<h6 class="fs-14 mb-1">'+
                                'Disetujui Oleh : '+result.bod2_fpkt+
                            '</h6>'+
                            '<small class="text-muted">'+result.date_bod2_fpkt+'</small>'+
                        '</div>');
                    }
                    //catatan fkp
                    $("#ctt-fpkt").html('');
                    if(result.ctt_fpkt == null){
                        $("#ctt-fpkt").html('<div class="text-center p-2">'+
                            '<img src="{{asset('assets/images/no-data.png')}}" style="width:80px;height:80px;" />'+
                            '<div class="mt-3">'+
                                '<h5 class="mb-3">Tidak ada catatan...</h5>'+
                            '</div>'+
                        '</div>');
                    }else{                        
                        $.each(result.ctt_fpkt, function(key,val) {
                            $("#ctt-fpkt").append('<div class="accordion-item border-0">'+
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
    });
</script>
<script>
    $(document).ready(function () {
        $("#offcanvasRight").on("show.bs.offcanvas", function (e) {
            var data_id = $(e.relatedTarget).data('id');
            $.ajax({
                url: "{{route('profile.training.status.laporan')}}",
                type: "POST",
                data: {
                    id: data_id,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(result) {
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
                    if(result.cek_direktur == 'ada'){
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
    $("#form-jadwal").submit(function(e) {
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
            swalert.then(() => window.location.href = "{{ route('training.emp.index') }}")
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
            swalert.then(() => window.location.href = "{{ route('training.emp.fkt.pti.approve.back') }}")
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
