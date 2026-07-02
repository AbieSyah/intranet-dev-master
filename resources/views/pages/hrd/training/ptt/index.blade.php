@extends('layouts.master')
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
<link rel="stylesheet" href="https://cdn.datatables.net/select/1.2.7/css/select.dataTables.min.css">
<!-- Select2-->
<link rel="stylesheet" href="/assets/libs/adminlte/select2/css/4.1.0/select2.min.css">
<!-- Toastr Notifications-->
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<style>
    .table-responsive{
        overflow: visible;
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Rencana Pelatihan Tahunan</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Rencana Pelatihan</a></li>
                    <li class="breadcrumb-item active">View</li>
                </ol>
            </div>

        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped bordered" id="table_training" style="width:100%;">
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
                <!-- Approve Modal -->
                <div class="modal fade bs-example-modal-approve" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-top">
                        <div class="modal-content">
                            <div class="modal-body text-center p-5">
                                <div class="mt-2">
                                    <h4 class="mb-3">Apakah Anda Yakin ?</h4>
                                    <img src="{{asset('assets/images/approve.png')}}" style="width:100px;height:100px;" />
                                    <form id="form-approve" class="mt-4" method="POST" action="{{ route('training.ptt.store') }}">
                                        @csrf
                                        @method('put')
                                        <input type="hidden" name="tipe" id="tipe" value="approve">
                                        <input type="hidden" name="kode" id="kode_approve" value="">
                                        <div class="hstack gap-2 justify-content-center">
                                            <button type="submit" class="btn btn-primary">Approve</button>
                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div><!-- /.modal -->    

                <!-- Revise Modal -->
                <div class="modal fade bs-example-modal-revise" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-top">
                        <div class="modal-content">
                            <div class="modal-body text-center p-5">
                                <div class="mt-2">
                                    <h4 class="mb-3">Catatan Revise</h4>
                                    <img src="{{asset('assets/images/revise.png')}}" style="width:100px;height:100px;" />
                                    <form id="form-revise" class="mt-4" method="POST" action="{{ route('training.ptt.store') }}">
                                        @csrf
                                        @method('put')
                                        <input type="hidden" name="tipe" id="tipe" value="revise">
                                        <input type="hidden" name="kode" id="kode_revise" value="">
                                        <div>
                                            <textarea class="form-control mb-4" id="catatan_revise" name="catatan_revise" rows="5" required></textarea>
                                        </div>
                                        <div class="hstack gap-2 justify-content-center">
                                            <button type="submit" class="btn btn-secondary">Revise</button>
                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div><!-- /.modal --> 

                <!-- Reject Modal -->
                <div class="modal fade bs-example-modal-reject" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-top">
                        <div class="modal-content">
                            <div class="modal-body text-center p-5">
                                <div class="mt-2">
                                    <h4 class="mb-3">Catatan Reject</h4>
                                    <img src="{{asset('assets/images/rejected.png')}}" style="width:100px;height:100px;" />
                                    <form id="form-reject" class="mt-4" method="POST" action="{{ route('training.ptt.store') }}">
                                        @csrf
                                        @method('put')
                                        <input type="hidden" name="tipe" id="tipe" value="reject">
                                        <input type="hidden" name="kode" id="kode_reject" value="">
                                        <div>
                                            <textarea class="form-control mb-4" id="catatan_reject" name="catatan_reject" rows="5" required></textarea>
                                        </div>
                                        <div class="hstack gap-2 justify-content-center">
                                            <button type="submit" class="btn btn-danger">Reject</button>
                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div><!-- /.modal --> 

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
        </div>
    </div>
</div>
<!-- Notification Mr.Mizukami Modal -->
{{-- <div class="modal fade bs-notification-modal-center" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-top">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
            <lord-icon src="https://cdn.lordicon.com/rhvddzym.json" trigger="loop" colors="primary:#0ab39c" class="avatar-xl"></lord-icon>
                <div class="mt-1">
                    <h4 class="mb-4">Apakah Anda Yakin ?</h4>
                    <form id="form-notif" method="POST" action="{{ route('training.ptt.notification') }}">
                        @csrf
                        @method('put')
                        <input type="hidden" name="tahun_usulan" id="tahun_usulan" value="">
                        <input type="hidden" name="id_approval" id="id_approval" value="">
                        <div class="hstack gap-2 justify-content-center">
                            <button type="submit" class="btn btn-primary">Ya</button>
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tidak</button>
                        </div>
                    </form>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div> --}}

@endsection

@section('script')
<!-- Datatables -->
<script src="/assets/libs/Datatables/DataTables-1.13.1/js/jquery.dataTables.min.js"></script>
<script src="/assets/libs/Datatables/DataTables-1.13.1/js/dataTables.bootstrap5.min.js"></script>
<script src="/assets/libs/Datatables/Responsive-2.4.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/select/1.2.7/js/dataTables.select.min.js"></script>
<script src="/assets/js/pages/datatables.init.js"></script>
<!-- Select2 -->
<script src="/assets/libs/adminlte/select2/js/4.1.0/select2.min.js"></script>
<script src="/assets/js/pages/select2.init.js"></script>
<!-- Sweetalert -->
<link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<!-- Toastr Notifications-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
@endsection

@section('javascript')
<script type="text/javascript">
    $(document).ready(function() {
        let table = $('#table_training').DataTable({
            stateSave: true,
            responsive: true,
            autoWidth: false,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('training.ptt.index') }}"
            },
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
        //status
        $('#table_training').on("click", ".view-status", function() {
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
        //form approve
        $('#table_training').on("click", ".view-approve", function() {
            var kode_approve = $(this).data("id");
            $('#kode_approve').val(kode_approve);
        });
        $('#table_training').on("click", ".view-revise", function() {
            var kode_revise = $(this).data("id");
            $('#kode_revise').val(kode_revise);
        });
        $('#table_training').on("click", ".view-reject", function() {
            var kode_reject = $(this).data("id");
            $('#kode_reject').val(kode_reject);
        });
    });
</script>
<script>
    //submit form approve
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
            swalert.then(() => window.location.reload() = response.redirect)
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
    //submit form revise
    $("#form-revise").submit(function(e) {
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
            swalert.then(() => window.location.reload() = response.redirect)
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
    //submit form reject
    $("#form-reject").submit(function(e) {
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
            swalert.then(() => window.location.reload() = response.redirect)
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