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
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Pelaksanaan Pelatihan</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pelaksanaan Pelatihan</a></li>
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
                <!-- Approve Modal -->
                <div class="modal fade bs-example-modal-approve" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-top">
                        <div class="modal-content">
                            <div class="modal-body text-center p-5">
                                <div class="mt-2">
                                    <h4 class="mb-3">Apakah Anda Yakin ?</h4>
                                    <img src="{{asset('assets/images/approve.png')}}" style="width:100px;height:100px;" />
                                    <form id="form-approve" class="mt-4" method="POST" action="{{ route('training.pti.store') }}">
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
                                    <form id="form-revise" class="mt-4" method="POST" action="{{ route('training.pti.store') }}">
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
                                    <form id="form-reject" class="mt-4" method="POST" action="{{ route('training.pti.store') }}">
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

                <!-- Status Modals -->
                <div class="modal fade" id="statusModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
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
                                                <th scope="row"><a href="#" class="fw-semibold">Peserta</a></th>
                                                <td>:</td>
                                                <td><span id="emp_peserta_fpkt"></span></td>
                                            </tr>
                                            {{-- <tr>
                                                <th scope="row"><a href="#" class="fw-semibold">Tanggal</a></th>
                                                <td>:</td>
                                                <td><span id="tanggal_pemohon_fpkt"></span></td>
                                            </tr> --}}
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
                url: "{{ route('training.pti.index') }}"
            },
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
        //status
        $('#table_training').on("click", ".view-status", function() {
            var kode = $(this).data("id");
            $.ajax({
                url: "{{ route('training.pti.status') }}",
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
                    $("#emp_peserta_fpkt").html(result.nama_peserta_fpkt);
                    // $("#tanggal_pemohon_fpkt").html(result.date_pemohon_fpkt);
                    $("#date_pelaksanaan_fpkt").html(result.date_pelaksanaan_fpkt);
                    if(result.id_status_fpkt == 9){
                        $("#nama_status_fpkt").html('<span class="badge badge-outline-warning">'+result.nama_status_fpkt+'</span>');
                    }
                    if(result.id_status_fpkt == 10){
                        $("#nama_status_fpkt").html('<span class="badge badge-outline-secondary">'+result.nama_status_fpkt+'</span>');
                    }
                    if(result.id_status_fpkt == 11){
                        $("#nama_status_fpkt").html('<span class="badge badge-outline-info">'+result.nama_status_fpkt+'</span>');
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
                        if(result.cek_dept == 'ada'){
                            $("#status-bod1-fpkt").html('<div class="flex-shrink-0 avatar-xs">'+
                                '<div class="avatar-title bg-light text-success rounded-circle">'+
                                    '<i class="ri-subtract-line"></i>'+
                                '</div>'+
                            '</div>'+
                            '<div class="flex-grow-1 ms-3">'+
                                '<h6 class="fs-14 mb-1">'+
                                    'Menunggu Persetujuan Production Director'+
                                '</h6>'+
                                '<small class="text-muted"></small>'+
                            '</div>');
                        }
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
                                'Menunggu Persetujuan President Director'+
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