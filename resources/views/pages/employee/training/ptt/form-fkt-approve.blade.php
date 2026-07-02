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
<!-- start page -->
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">                                        
                <div class="row mb-3">
                    <div class="col-lg-6">
                    <h4 class="text-primary">Approval Formulir Kebutuhan Pelatihan (FKP)</h4>
                    </div>
                    <div class="col-lg-6">
                        <a href="{{ route('training.emp.fkt.ptt.approve.back') }}" class="btn btn-primary btn-label waves-effect waves-light float-end"><i class="ri-arrow-left-circle-line label-icon align-middle fs-16 me-2"></i> Back</a>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-lg-7">
                        <div class="row">
                            <div class="col-lg-4">
                                <label for="jenis" class="form-label col-form-label col-form-label-sm">Tahun Usulan Program</label>
                            </div>
                            <div class="col-lg-8">
                                <table class="table table-sm table-nowrap fs-12">
                                    <tbody>
                                        <tr>
                                            <td>{{$tahun_usulan}}</td>
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
                                            <td>Program Pelatihan Tahunan</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5"></div>
                </div>         
                <div class="table-responsive">                         
                    <table class="table table-striped bordered" id="table_fkt" style="width:100%;">
                        <thead>
                            <tr>
                                <th scope="col" style="text-align:center">No Document</th>
                                <th scope="col" style="text-align:center">Pemohon</th>
                                <th scope="col" style="text-align:center">Peserta</th>
                                <th scope="col" style="text-align:center">Status</th>
                                <th scope="col" style="text-align:center">Action</th>
                                <th scope="col" style="text-align:center"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($query_fkt))
                                @foreach($query_fkt as $fkt)
                                    @php
                                        $peserta = \App\Models\Trainingfkt::where('kode', $fkt->kode)->get();
                                        $jml_peserta = $peserta->count();
                                    @endphp
                                    @if(empty($fkt->date_checker))
                                        <tr>                                                                    
                                            <td style="text-align: center;">{{$fkt->kode}}</td>
                                            <td style="text-align: center;">{{$fkt->pemohon->fullname}}</td>
                                            <td style="text-align: center;">{{$jml_peserta}}</td>
                                            <td style="text-align: center;"><span class="badge text-bg-info">Waiting Approval</span></td>
                                            <td style="text-align: center;">
                                                <div class="dropdown d-inline-block">
                                                    <button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="ri-more-fill align-middle"></i></button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li><a href="#" data-id="{{encrypt($fkt->kode)}}" data-bs-toggle="modal" data-bs-target=".bs-example-modal-approve" class="dropdown-item view-approve"><i class="ri-checkbox-line align-bottom me-2 text-muted"></i> Approve</a></li>
                                                        <li><a href="#" data-id="{{encrypt($fkt->kode)}}" data-bs-toggle="modal" data-bs-target=".bs-example-modal-revise" class="dropdown-item view-revise"><i class="ri-error-warning-line align-bottom me-2 text-muted"></i> Revise</a></li>
                                                        <li><a href="#" data-id="{{encrypt($fkt->kode)}}" data-bs-toggle="modal" data-bs-target=".bs-example-modal-reject" class="dropdown-item view-reject"><i class="ri-close-circle-line align-bottom me-2 text-muted"></i> Reject</a></li>
                                                        <li><a href="{{ route('profile.training.fkt.ptt.pdf', encrypt($fkt->kode)) }}" class="dropdown-item" target="_blank"><i class="ri-file-pdf-line align-bottom me-2 text-muted"></i> Document FKP</a></li>
                                                        {{-- <li><a href="{{ route('profile.training.fpkt.ptt.pdf', encrypt($fkt->kode_judul)) }}" class="dropdown-item" target="_blank"><i class="ri-file-pdf-line align-bottom me-2 text-muted"></i> Document FPKT</a></li> --}}
                                                    </ul>
                                                </div>
                                            </td>
                                            <td style="text-align: center;">
                                                <div class="col-lg-12">
                                                    <table class="table table-bordered" style="table-layout: fixed; width:100%;">
                                                        <thead>
                                                            <tr>
                                                                <th colspan="3" style="text-align: center;">Pengajuan Program Pelatihan</th>
                                                            </tr>
                                                            <tr>
                                                                <th style="text-align: center;">Pelatihan</th>
                                                                <th style="text-align: center;">Peserta</th>
                                                                <th style="text-align: center;">Pelaksanaan</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($peserta as $psa)
                                                                @php($bulan = \Carbon\Carbon::create()->month($psa->bulan_pelaksanaan)->format('F'))
                                                                <tr>
                                                                    <td style="text-align: center;">{{$psa->judul}}</td>
                                                                    <td style="text-align: center;">{{$psa->peserta->fullname}}</td>
                                                                    <td style="text-align: center;">{{$bulan}} {{$psa->tahun_pelaksanaan}}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif                                                           
                                @endforeach
                            @endif
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
                                    <form id="form-approve" class="mt-4" method="POST" action="{{ route('training.emp.fpkt.ptt.approved.store') }}">
                                        @csrf
                                        @method('put')
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
                                    <form id="form-revise" class="mt-4" method="POST" action="{{ route('training.emp.fpkt.ptt.revised.store') }}">
                                        @csrf
                                        @method('put')
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
                                    <form id="form-reject" class="mt-4" method="POST" action="{{ route('training.emp.fpkt.ptt.rejected.store') }}">
                                        @csrf
                                        @method('put')
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
            </div>
        </div><!-- end card -->
    </div>
    <!--end col-->
</div>
<!--end row-->
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
<!-- Sweetalert -->
<link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<!-- Toastr Notifications-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
@endsection
@section('javascript')
<script>
    $(function () {    
        $('.select2').select2();
    });
</script>
<script>
    $(".bs-example-modal-revise").on("hidden.bs.modal", function(){
        $("#catatan_revise").val('');
    });
    $(".bs-example-modal-reject").on("hidden.bs.modal", function(){
        $("#catatan_reject").val('');
    });
</script>
<script>
    let table_fkt = new DataTable('#table_fkt', {
        stateSave: true,
        responsive: true,
            columnDefs: [
            {
                className: 'none',
                orderable: false,
                targets: 5
            }
        ],
    });
    $('#table_fkt').on("click", ".view-approve", function() {
        var kode_approve = $(this).data("id");
        $('#kode_approve').val(kode_approve);
    });
    $('#table_fkt').on("click", ".view-revise", function() {
        var kode_revise = $(this).data("id");
        $('#kode_revise').val(kode_revise);
    });
    $('#table_fkt').on("click", ".view-reject", function() {
        var kode_reject = $(this).data("id");
        $('#kode_reject').val(kode_reject);
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
