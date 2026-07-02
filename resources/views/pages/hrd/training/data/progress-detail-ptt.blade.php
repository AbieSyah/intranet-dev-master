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
            <!-- view hrd -->
            <h4 class="mb-sm-0">DETAIL TRAINING</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">HRD</a></li>
                    <li class="breadcrumb-item">Training</li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </div>

        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">                                        
                <div class="row mb-3">
                    <div class="col-lg-6">
                        <h4 class="text-primary">Detail Formulir Kebutuhan Training</h4>
                    </div>
                    <div class="col-lg-6">
                        <a href="{{ route('training.data.proggress.ptt.detail.back') }}" class="btn btn-primary btn-label waves-effect waves-light float-end"><i class="ri-arrow-left-circle-line label-icon align-middle fs-16 me-2"></i> Back</a>
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
                                            <td>Program Training Tahunan (PTT)</td>
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
                            <th scope="col" style="text-align:center">No</th>
                            <th scope="col" style="text-align:center">Periode</th>
                            <th scope="col" style="text-align:center">Pemohon</th>
                            <th scope="col" style="text-align:center">Topic</th>
                            <th scope="col" style="text-align:center">Jenis</th>
                            <th scope="col" style="text-align:center">Jumlah Peserta</th>
                            <th scope="col" style="text-align:center">Status</th>
                            <th scope="col" style="text-align:center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($query_fkt as $fkt)
                            @php
                            $jml_peserta = \App\Models\Trainingfkt::where('kode_judul', $fkt->kode_judul)->count();
                            $cek_jml = \App\Models\Trainingfkt::where('kode_judul', $fkt->kode_judul)->whereNull('date_checker')->count();
                            @endphp
                            <tr>
                                <td style="text-align: center;">{{$loop->iteration}}</td>
                                <td style="text-align: center;">{{$fkt->bulan_pelaksanaan}}</td>
                                <td style="text-align: center;">{{$fkt->pemohon->fullname}}</td>
                                <td style="text-align: center;">{{$fkt->judul}}</td>
                                <td style="text-align: center;">{{$fkt->sifat}}</td>
                                <td style="text-align: center;">{{$jml_peserta}}</td>
                                @if($cek_jml > 0)
                                    <td style="text-align: center;"><span class="badge text-bg-warning">On Progress</span></td>
                                    <td style="text-align: center;">
                                        <div class="dropdown d-inline-block">
                                            <button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="ri-more-fill align-middle"></i></button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a href="{{ route('training.ptt.fkt.pdf', encrypt($fkt->kode_judul)) }}" class="dropdown-item" target="_blank"><i class="ri-file-pdf-line align-bottom me-2 text-muted"></i> Document FKT</a></li>
                                                <li><a href="{{ route('training.ptt.fpkt.pdf', encrypt($fkt->kode_judul)) }}" class="dropdown-item" target="_blank"><i class="ri-file-pdf-line align-bottom me-2 text-muted"></i> Document FPKT</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                @else
                                <td style="text-align: center;"><span class="badge text-bg-warning">{{$fkt->training_status->name}}</span></td>
                                <td style="text-align: center;">
                                    <div class="dropdown d-inline-block">
                                        <button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="ri-more-fill align-middle"></i></button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a href="{{ route('training.ptt.fkt.pdf', encrypt($fkt->kode_judul)) }}" class="dropdown-item" target="_blank"><i class="ri-file-pdf-line align-bottom me-2 text-muted"></i> Document FKT</a></li>
                                            <li><a href="{{ route('training.ptt.fpkt.pdf', encrypt($fkt->kode_judul)) }}" class="dropdown-item" target="_blank"><i class="ri-file-pdf-line align-bottom me-2 text-muted"></i> Document FPKT</a></li>
                                        </ul>
                                    </div>
                                </td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
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
<!-- Sweetalert -->
<link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
@endsection
@section('javascript')
<script>
    let table_fkt = new DataTable('#table_fkt', {
        stateSave: true
    });
    $('#table_fkt').on("click", ".view-status", function() {
        var kode_judul = $(this).data("id");
        $('#kode_judul').val(kode_judul);
    });
</script>
<script>
    //submit form approve hrd
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
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });
</script>
@endsection