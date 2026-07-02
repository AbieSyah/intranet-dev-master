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
@endsection
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">PROGRAM TRAINING INSIDENTIL</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">HRD</a></li>
                    <li class="breadcrumb-item">Training</li>
                    <li class="breadcrumb-item">PTI</li>
                    @if($user->roles()->pluck('id')->first() == '2')
                    <li class="breadcrumb-item active">Verification</li>
                    @else
                    <li class="breadcrumb-item active">Approval</li>
                    @endif
                </ol>
            </div>

        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">  
                <form id="form-pti" action="{{ route('training.fpkt.pti.form.store') }}" method="post">
                    @csrf
                    @method('PUT')                                      
                    <div class="row mb-3">
                        <div class="col-lg-6">
                        <h4 class="text-primary">Formulir Penilaian Kebutuhan Training (FPKT)</h4>
                        </div>
                        <div class="col-lg-6">
                            <a href="{{ url()->previous() }}" class="btn btn-primary btn-label waves-effect waves-light float-end"><i class="ri-arrow-left-circle-line label-icon align-middle fs-16 me-2"></i> Back</a>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-lg-10">
                            <div class="row">
                                <input type="hidden" name="id_fkt" value="{{$arr_id}}">
                                <div class="col-lg-5">
                                    <label for="topik" class="form-label col-form-label col-form-label-sm">No Form</label>
                                </div>
                                <div class="col-lg-7">
                                    <table class="table table-sm table-nowrap fs-12">
                                        <tbody>
                                            <tr>
                                                <td>{{$fkt->kode ?? '-'}}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-5">
                                    <label for="jenis" class="form-label col-form-label col-form-label-sm">Usulan Topik Training</label>
                                </div>
                                <div class="col-lg-7">
                                    <table class="table table-sm table-nowrap fs-12">
                                        <tbody>
                                            <tr>
                                                <td>{{$fkt->judul ?? '-'}}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-5">
                                    <label for="jenis" class="form-label col-form-label col-form-label-sm">Rekomendasi Jenis Training</label>
                                </div>
                                <div class="col-lg-7">
                                    <table class="table table-sm table-nowrap fs-12">
                                        <tbody>
                                            <tr>
                                                <td>{{$fkt->jenis_pelatihan ?? '-'}}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-5">
                                    <label for="jenis" class="form-label col-form-label col-form-label-sm">Rekomendasi Vendor Training</label>
                                </div>
                                <div class="col-lg-7">
                                    <table class="table table-sm table-nowrap fs-12">
                                        <tbody>
                                            <tr>
                                                <td>{{$fkt->provider->nama ?? '-'}}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-lg-5">
                                    <label for="jenis" class="form-label col-form-label col-form-label-sm">Nama Peserta Training</label>
                                </div>
                                <div class="col-lg-7">
                                    <table class="table table-sm fs-12">
                                        <tbody>
                                            <tr>
                                                <td>{{implode(', ',$arr_peserta->pluck('fullname')->toArray()) ?? '-'}}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-5">
                                    <label for="jenis" class="form-label col-form-label col-form-label-sm">Nomor Induk Karyawan (NIK)</label>
                                </div>
                                <div class="col-lg-7">
                                    <table class="table table-sm fs-12">
                                        <tbody>
                                            <tr>
                                                <td>{{implode(', ',array_unique($arr_peserta->pluck('nik')->toArray())) ?? '-'}}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-5">
                                    <label for="jenis" class="form-label col-form-label col-form-label-sm">Departemen / Bagian</label>
                                </div>
                                <div class="col-lg-7">
                                    <table class="table table-sm fs-12">
                                        <tbody>
                                            <tr>
                                                <td>{{implode(', ',array_unique($arr_dept)) ?? '-'}}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-5">
                                    <label for="jenis" class="form-label col-form-label col-form-label-sm">Jabatan</label>
                                </div>
                                <div class="col-lg-7">
                                    <table class="table table-sm fs-12">
                                        <tbody>
                                            <tr>
                                                <td>{{implode(', ',array_unique($arr_jabatan)) ?? '-'}}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-5">
                                    <label for="jenis" class="form-label col-form-label col-form-label-sm">Nama Atasan Langsung</label>
                                </div>
                                <div class="col-lg-7">
                                    <table class="table table-sm table-nowrap fs-12">
                                        <tbody>
                                            <tr>
                                                <td>{{$fkt->penilai->fullname ?? '-'}}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-5">
                                    <label for="jenis" class="form-label col-form-label col-form-label-sm">Alasan Insidentil</label>
                                </div>
                                <div class="col-lg-7">
                                    <table class="table table-sm fs-12">
                                        <tbody>
                                            <tr>
                                                <td>{{$fkt->alasan_pti ?? '-'}}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-5">
                                    <label for="jenis" class="form-label col-form-label col-form-label-sm">Dokumen</label>
                                </div>
                                <div class="col-lg-7">
                                    <table class="table table-sm table-nowrap fs-12">
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <a href="{{ route('training.pti.fkt.pdf', encrypt($fkt->kode_judul)) }}" target="_blank" class="btn btn-danger btn-sm">
                                                        <i class="ri-file-pdf-line align-bottom me-1"></i> Dokumen Formulir Kebutuhan Training
                                                    </a>
                                                    @if($user->roles()->pluck('id')->first() == '49' || $user->roles()->pluck('id')->first() == '51')
                                                    
                                                    @else
                                                    <a href="{{ route('training.pti.fpkt.pdf', encrypt($fkt->kode_judul)) }}" target="_blank" class="btn btn-danger btn-sm">
                                                        <i class="ri-file-pdf-line align-bottom me-1"></i> Dokumen Formulir Penilaian Kebutuhan Training
                                                    </a>
                                                    @endif
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2"></div>
                    </div>
                    <div class="row" id="form-submit">
                        <div class="col-lg-12">
                            @if($user->roles()->pluck('id')->first() == '2')
                            <button id="btn-submit" class="btn btn-primary float-end" type="submit">Submit</button>
                            @else
                            <button id="btn-submit" class="btn btn-primary float-end" type="submit">Approve</button>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div><!-- end card -->
    </div>
    <!--end col-->
</div>
<!--end row-->
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
<!-- Datatables -->
<script src="/assets/libs/Datatables/DataTables-1.13.1/js/jquery.dataTables.min.js"></script>
<script src="/assets/libs/Datatables/DataTables-1.13.1/js/dataTables.bootstrap5.min.js"></script>
<script src="/assets/libs/Datatables/Responsive-2.4.0/js/dataTables.responsive.min.js"></script>
<script src="/assets/js/pages/datatables.init.js"></script>
@endsection
@section('javascript')
<script>
    $( "#btn-submit" ).click(function() {
        $("#form-pti").submit(function () {
            $('#staticBackdrop').modal('show', true);
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