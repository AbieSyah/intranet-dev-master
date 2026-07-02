@extends('layouts.master')
@section('link')
<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- multi.js css -->
<!-- <link rel="stylesheet" type="text/css" href="/assets/libs/multi.js/multi.min.css" /> -->
<!-- Datatables-->
<link href="/assets/libs/Datatables/DataTables-1.13.1/css/dataTables.bootstrap5.css" rel="stylesheet" type="text/css" />
<link href="/assets/libs/Datatables/Buttons-2.3.3/css/buttons.dataTables.css" rel="stylesheet" type="text/css" />
<link href="/assets/libs/Datatables/Responsive-2.4.0/css/responsive.bootstrap.css" rel="stylesheet" type="text/css" />
<link href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css" rel="stylesheet" />
<!-- Select2-->
<link rel="stylesheet" href="/assets/libs/adminlte/select2/css/4.1.0/select2.min.css">
<!-- Toastr Notifications-->
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Medical Checkup Tahunan {{$year}}</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Medical Check Up</a></li>
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Reguler</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </div>

        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="row">
            <div class="col-lg-3">
                <div class="card card-animate">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="text-uppercase fw-medium text-muted mb-0">Total Employee</p>
                                <h2 class="mt-4 ff-secondary fw-semibold"><span class="counter-value"
                                        data-target="{{$total_emp['jml_medical']}}">0</span></h2>
                                <p class="mb-0 text-muted">
                                    @if($total_emp['selisih'] >= 0)
                                        <span class="badge bg-light text-success mb-2">
                                            <i class="ri-arrow-up-line align-middle"></i> {{$total_emp['persentase']}} %
                                        </span>
                                    @else
                                        <span class="badge bg-light text-danger mb-2">
                                            <i class="ri-arrow-down-line align-middle"></i> {{abs($total_emp['persentase'])}} %
                                        </span>
                                    @endif
                                    @if($total_emp['selisih'] > 0)
                                        ({{$total_emp['selisih']}})
                                    @else
                                        ({{abs($total_emp['selisih'])}})
                                    @endif
                                </p>
                                <p class="mb-0 text-muted">Dari tahun lalu ({{$total_emp['pre_jml_medical']}})</p>
                            </div>
                            <div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-soft-primary rounded-circle fs-2">
                                        <i data-feather="users" class="text-primary"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div><!-- end card body -->
                </div> <!-- end card-->
            </div> <!-- end col-->
            <div class="col-lg-3">
                <div class="card card-animate">
                    <div class="card-body">
                        <a href="#" data-bs-toggle="modal" data-bs-target="#modal-rs">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p class="text-uppercase fw-medium text-muted mb-0">Resiko Tinggi</p>
                                    <h2 class="mt-4 ff-secondary fw-semibold"><span class="counter-value"
                                            data-target="{{$total_rt['jml_medical']}}">0</span></h2>
                                            <p class="mb-0 text-muted">
                                        @if($total_rt['selisih'] >= 0)
                                            <span class="badge bg-light text-success mb-2">
                                                <i class="ri-arrow-up-line align-middle"></i> {{$total_rt['persentase']}} %
                                            </span>
                                        @else
                                            <span class="badge bg-light text-danger mb-2">
                                                <i class="ri-arrow-down-line align-middle"></i> {{abs($total_rt['persentase'])}} %
                                            </span>
                                        @endif
                                        @if($total_rt['selisih'] > 0)
                                            ({{$total_rt['selisih']}})
                                        @else
                                            ({{abs($total_rt['selisih'])}})
                                        @endif
                                    </p>
                                    <p class="mb-0 text-muted">Dari tahun lalu ({{$total_rt['pre_jml_medical']}})</p>
                                </div>
                                <div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-soft-danger rounded-circle fs-2">
                                            <i class="lab las la-heartbeat text-danger"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div><!-- end card body -->
                </div> <!-- end card-->
            </div> <!-- end col-->
            <div class="col-lg-3">
                <div class="card card-animate">
                    <div class="card-body">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#modal-sdr">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="text-uppercase fw-medium text-muted mb-0">Sehat Dengan Resiko</p>
                                <h2 class="mt-4 ff-secondary fw-semibold"><span class="counter-value"
                                        data-target="{{$total_sr['jml_medical']}}">0</span></h2>
                                        <p class="mb-0 text-muted">
                                    @if($total_sr['selisih'] >= 0)
                                        <span class="badge bg-light text-success mb-2">
                                            <i class="ri-arrow-up-line align-middle"></i> {{$total_sr['persentase']}} %
                                        </span>
                                    @else
                                        <span class="badge bg-light text-danger mb-2">
                                            <i class="ri-arrow-down-line align-middle"></i> {{abs($total_sr['persentase'])}} %
                                        </span>
                                    @endif
                                    @if($total_sr['selisih'] > 0)
                                        ({{$total_sr['selisih']}})
                                    @else
                                        ({{abs($total_sr['selisih'])}})
                                    @endif
                                </p>
                                <p class="mb-0 text-muted">Dari tahun lalu ({{$total_sr['pre_jml_medical']}})</p>
                            </div>
                            <div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-soft-warning rounded-circle fs-2">
                                        <i class="lab las la-heartbeat text-warning"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                    </div><!-- end card body -->
                </div> <!-- end card-->
            </div> <!-- end col-->
            <div class="col-lg-3">
                <div class="card card-animate">
                    <div class="card-body">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#modal-s">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="text-uppercase fw-medium text-muted mb-0">Sehat</p>
                                <h2 class="mt-4 ff-secondary fw-semibold"><span class="counter-value"
                                        data-target="{{$total_sehat['jml_medical']}}">0</span></h2>
                                        <p class="mb-0 text-muted">
                                    @if($total_sehat['selisih'] >= 0)
                                        <span class="badge bg-light text-success mb-2">
                                            <i class="ri-arrow-up-line align-middle"></i> {{$total_sehat['persentase']}} %
                                        </span>
                                    @else
                                        <span class="badge bg-light text-danger mb-2">
                                            <i class="ri-arrow-down-line align-middle"></i> {{abs($total_sehat['persentase'])}} %
                                        </span>
                                    @endif
                                    @if($total_sehat['selisih'] > 0)
                                        ({{$total_sehat['selisih']}})
                                    @else
                                        ({{abs($total_sehat['selisih'])}})
                                    @endif
                                </p>
                                <p class="mb-0 text-muted">Dari tahun lalu ({{$total_sehat['pre_jml_medical']}})</p>
                            </div>
                            <div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-soft-success rounded-circle fs-2">
                                        <i class="lab las la-heartbeat text-success"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                    </div><!-- end card body -->
                </div> <!-- end card-->
            </div> <!-- end col-->
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">      
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-lg-3">
                        @can('hrd.medical-record.reguler.upload.excel')
                        <a href="{{ route('reguler.upload', $kode) }}" class="btn btn-primary btn-label waves-effect waves-light" data-text="Upload Medical Checkup">
                            <i class="ri-file-upload-line label-icon align-middle fs-16 me-2"></i>Upload Rekapitulasi MCU
                        </a> 
                        @endcan                      
                    </div>
                    <div class="col-lg-3">
                        @can('hrd.medical-record.reguler.high-risk.excel')
                        <a href="{{ route('reguler.export', $kode) }}" target="_blank" class="btn btn-success btn-label waves-effect waves-light" data-text="Resume High Risk">
                            <i class="ri-file-excel-2-line label-icon align-middle fs-16 me-2"></i>Resume High Risk
                        </a>
                        @endcan
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group p-2">
                            <select class="form-control select2" id="kriteria_sehat" name="kriteria_sehat">
                                <option value="" selected>All</option>
                                <option value="SEHAT">Sehat</option>
                                <option value="SEHAT DENGAN RESIKO">Sehat dengan resiko</option>
                                <option value="RESIKO TINGGI">Resiko tinggi</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-2 p-2">
                        <button type="button" name="filter" id="filter" class="btn btn-soft-primary waves-effect waves-light btn-sm"><i class="ri-filter-2-line me-1 align-bottom"></i> Filters</button>
                        <button type="button" name="refresh" id="refresh" class="btn btn-soft-danger waves-effect waves-light btn-sm"><i class="ri-refresh-line me-1 align-bottom"></i> Reset</button>
                    </div>
                    <div class="col-lg-2">
                        <a href="{{ route('reguler.index') }}" class="btn btn-primary btn-label waves-effect waves-light float-end"><i class="ri-arrow-left-fill label-icon align-middle fs-16 me-2"></i> Back</a>
                    </div>  
                </div>                
                <table class="table table-striped bordered" id="table_medical">
                    <thead>
                        <tr>
                        <th scope="col" style="text-align:center">NO</th>
                        <!-- <th scope="col" style="text-align:center">NO LAB</th> -->
                        <th scope="col" style="text-align:center">NIK</th>
                        <th scope="col" style="text-align:center">NAMA</th>
                        <!-- <th scope="col" style="text-align:center">L/P</th> -->
                        <!-- <th scope="col" style="text-align:center">USIA</th> -->
                        <th scope="col" style="text-align:center">AREA</th>
                        <th scope="col" style="text-align:center">DEPARTEMEN</th>
                        <th scope="col" style="text-align:center">LOKASI KERJA</th>
                        <th scope="col" style="text-align:center">TGL. PEMERIKSAAN</th>
                        <th scope="col" style="text-align:center">KRITERIA SEHAT</th>
                        <th scope="col" style="text-align:center">ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!--Modal edit-->
<div class="modal fade" id="edit-reguler" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="Form-edit" action="{{ route('reguler.api.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Edit Detail</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" class="form-control" id="id" name="id" value=""/>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="mb-3">
                                <label class="form-label">No Lab</label>
                                <input type="text" class="form-control text-uppercase" name="ajx_no_lab" id="ajx_no_lab" placeholder="Masukkan No Lab">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-control" name="ajx_status" id="ajx_status" data-placeholder="--Pilih Kriteria--">                                       
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">                            
                            <div class="mb-3">
                                <label>Tanggal Periksa</label>
                                <div class="input-group">
                                    <input type="text" name="ajx_tanggal_mcu" id="ajx_tanggal_mcu"
                                        class="form-control @error('ajx_tanggal_mcu') is-invalid @enderror"
                                        placeholder="Pilih Tanggal" value="">
                                    <span class="input-group-text"><i class="ri-calendar-event-line"></i></span>
                                </div>
                            </div>
                        </div>                    
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <label class="form-label">Upload</label>
                            <div class="input-group">
                                <input onchange="ajxuploadValidation(this);" type="file" class="form-control form-control text-sm col-sm-6" name="ajx_file" id="ajx_file" accept="application/pdf,application/PDF">
                                <button type="button" class="btn btn-soft-danger waves-effect waves-light" onclick="ajxclearUpload()">Remove</button>
                                <button type="button" data-bs-toggle="modal" data-bs-target="#modal-preview" class="btn btn-soft-warning waves-effect waves-light">Preview</button>
                            </div>
                            <span class="form-text">hanya menerima file bertipe .pdf | .PDF</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="btn-edit-save" class="btn btn-primary ">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--Modal upload-->
<div class="modal fade" id="upload-reguler" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="Form" action="{{ route('reguler.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Upload File</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" class="form-control" id="id_medical" name="id_medical" value=""/>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="mb-3">
                                <label class="form-label">No Lab</label>
                                <input type="text" class="form-control text-uppercase" name="no_lab" id="no_lab" placeholder="Masukkan No Lab" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-control" name="status" id="status" data-placeholder="--Pilih Kriteria--" required>
                                    <option selected="true" disabled="true"></option>
                                    <option value="SEHAT">Sehat</option>
                                    <option value="SEHAT DENGAN RESIKO">Sehat dengan resiko</option>
                                    <option value="RESIKO TINGGI">Resiko tinggi</option>                                        
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">                            
                            <div class="mb-3">
                                <label>Tanggal Periksa</label>
                                <div class="input-group">
                                    <input type="text" name="tanggal_mcu" id="tanggal_mcu"
                                        class="form-control @error('tanggal_mcu') is-invalid @enderror"
                                        placeholder="Pilih Tanggal" value="" required>
                                    <span class="input-group-text"><i class="ri-calendar-event-line"></i></span>
                                </div>
                            </div>
                        </div>                    
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <label class="form-label">Upload</label>
                            <div class="input-group">
                                <input onchange="uploadValidation(this);" type="file" class="form-control form-control text-sm col-sm-6" name="file" id="file" accept="application/pdf,application/PDF" required>
                                <button type="button" class="btn btn-soft-danger waves-effect waves-light" onclick="clearUpload()">Remove</button>
                            </div>
                            <span class="form-text">hanya menerima file bertipe .pdf | .PDF</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="btn-save" class="btn btn-primary ">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Modal Validation Extension File Upload -->
<div class="modal fade" id="validationmodal" aria-hidden="true" aria-labelledby="..." tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <lord-icon
                    src="https://cdn.lordicon.com/tdrtiskw.json"
                    trigger="loop"
                    colors="primary:#f7b84b,secondary:#405189"
                    style="width:130px;height:130px">
                </lord-icon>
                <div class="mt-0 pt-4">
                    <h4>Whoops, ada yang salah!</h4>
                    <div id="info-validation"></div>
                    <!-- Toogle to second dialog -->
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!--modal preview mcu-->
<div class="modal flip" id="modal-preview" tabindex="-1" aria-labelledby="exampleModalgridLabel" aria-modal="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-judul"></h5>
                <!-- <button class="btn-close" data-bs-target="#firstmodal" data-bs-toggle="modal" data-bs-dismiss="modal"> -->
                <button type="button" class="btn-close" data-bs-target="#edit-reguler" data-bs-toggle="modal" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <embed id="show-preview" src="" frameborder="0" width="100%" height="450px">
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>
<!-- MULAI MODAL KONFIRMASI DELETE-->
<div class="modal flip" tabindex="-1" role="dialog" id="delete-reguler" data-backdrop="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">WARNING</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('reguler.destroy')}}" method="post">
                @csrf
                @method('put')
                    <input type="hidden" id="del_medical" name="del_medical" value="">
                    <center>
                        <lord-icon
                            src="https://cdn.lordicon.com/gsqxdxog.json"
                            trigger="loop"
                            delay="1000"
                            colors="primary:#121331,secondary:#08a88a"
                            style="width:100px;height:100px">
                        </lord-icon>
                        <p><b>Are You Sure ?</b></p>
                        <p>You will not be able to recover this data</p>
                    </center>
                    <div class="hstack gap-2 justify-content-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Card Resiko Tinggi Modal -->
<div id="modal-rs" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">RESIKO TINGGI</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-between border-bottom border-bottom-dashed py-2">
                    <p class="fw-medium mb-0"><i class="ri-checkbox-blank-circle-fill text-success align-middle me-2"></i> {{$ket['rt_sehat'] ?? '0'}} Karyawan Tahun Lalu "<span class="text-success">SEHAT</span>" Sekarang "<span class="text-danger">RESIKO TINGGI</span>"</p>
                </div><!-- end -->
                <div class="d-flex justify-content-between border-bottom border-bottom-dashed py-2">
                    <p class="fw-medium mb-0"><i class="ri-checkbox-blank-circle-fill text-warning align-middle me-2"></i> {{$ket['rt_sdr'] ?? '0'}} Karyawan Tahun Lalu "<span class="text-warning">SEHAT DENGAN RESIKO</span>" Sekarang "<span class="text-danger">RESIKO TINGGI</span>"</p>
                </div><!-- end -->
                <div class="d-flex justify-content-between border-bottom border-bottom-dashed py-2">
                    <p class="fw-medium mb-0"><i class="ri-checkbox-blank-circle-fill text-danger align-middle me-2"></i> {{$ket['rt_rt'] ?? '0'}} Karyawan Masih Tetap "<span class="text-danger">RESIKO TINGGI</span>"</p>
                </div><!-- end -->
            </div>
            <div class="modal-footer">
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- Card Sehat Dengan Resiko Modal -->
<div id="modal-sdr" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">SEHAT DENGAN RESIKO</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-between border-bottom border-bottom-dashed py-2">
                    <p class="fw-medium mb-0"><i class="ri-checkbox-blank-circle-fill text-success align-middle me-2"></i> {{$ket['sr_sehat'] ?? '0'}} Karyawan Tahun Lalu "<span class="text-success">SEHAT</span>" Sekarang "<span class="text-warning">SEHAT DENGAN RESIKO</span>"</p>
                </div><!-- end -->
                <div class="d-flex justify-content-between border-bottom border-bottom-dashed py-2">
                    <p class="fw-medium mb-0"><i class="ri-checkbox-blank-circle-fill text-warning align-middle me-2"></i> {{$ket['sr_sdr'] ?? '0'}} Karyawan Masih Tetap "<span class="text-warning">SEHAT DENGAN RESIKO</span>"</p>
                </div><!-- end -->
                <div class="d-flex justify-content-between border-bottom border-bottom-dashed py-2">
                    <p class="fw-medium mb-0"><i class="ri-checkbox-blank-circle-fill text-danger align-middle me-2"></i> {{$ket['sr_rt'] ?? '0'}} Karyawan Tahun Lalu "<span class="text-danger">RESIKO TINGGI</span>" Sekarang "<span class="text-warning">SEHAT DENGAN RESIKO</span>"</p>
                </div><!-- end -->
            </div>
            <div class="modal-footer">
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- Card Sehat Modal -->
<div id="modal-s" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">SEHAT</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-between border-bottom border-bottom-dashed py-2">
                    <p class="fw-medium mb-0"><i class="ri-checkbox-blank-circle-fill text-success align-middle me-2"></i> {{$ket['s_sehat'] ?? '0'}} Karyawan Masih Tetap "<span class="text-success">SEHAT</span>"</p>
                </div><!-- end -->
                <div class="d-flex justify-content-between border-bottom border-bottom-dashed py-2">
                    <p class="fw-medium mb-0"><i class="ri-checkbox-blank-circle-fill text-warning align-middle me-2"></i> {{$ket['s_sdr'] ?? '0'}} Karyawan Tahun Lalu "<span class="text-warning">SEHAT DENGAN RESIKO</span>" Sekarang "<span class="text-success">SEHAT</span>"</p>
                </div><!-- end -->
                <div class="d-flex justify-content-between border-bottom border-bottom-dashed py-2">
                    <p class="fw-medium mb-0"><i class="ri-checkbox-blank-circle-fill text-danger align-middle me-2"></i> {{$ket['s_rt'] ?? '0'}} Karyawan Tahun Lalu "<span class="text-danger">RESIKO TINGGI</span>" Sekarang "<span class="text-success">SEHAT</span>"</p>
                </div><!-- end -->
            </div>
            <div class="modal-footer">
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<!-- Select2 -->
<script src="/assets/libs/adminlte/select2/js/4.1.0/select2.min.js"></script>
<!-- Toastr Notifications-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
@endsection

@section('javascript')
<script>
    $('#tanggal_mcu').flatpickr({
        allowInput: true,
        altInput: false,
        altFormat: "d F, Y",
        dateFormat: "Y-m-d",
    }); 
    $('#ajx_tanggal_mcu').flatpickr({
        allowInput: true,
        altInput: false,
        altFormat: "d F, Y",
        dateFormat: "Y-m-d",
    }); 
    $(function () {
        $('#kriteria_sehat').select2();
    });
</script>
<script>
    $(function () {
        $('#status').select2({dropdownParent: $('#upload-reguler .modal-content')});
        $('#ajx_status').select2({dropdownParent: $('#edit-reguler .modal-content')});
    });

    function clearUpload(){
        var upload = document.getElementById('file');
        upload.value = '';
    }

    function ajxclearUpload(){
        var upload = document.getElementById('ajx_file');
        upload.value = '';
    }

    function uploadValidation(){
        var upload = document.getElementById('file');
        var pathUpload= upload.value;

        // tipe file yang diizinkan
        var allowedExtensions = /(\.pdf|\.PDF)$/i;

        if (!allowedExtensions.exec(pathUpload)) {
            document.getElementById(
                'info-validation').innerHTML =
                '<p class="text-muted fs-12">Maaf hanya menerima file document yang bertipe .pdf | .PDF</p>';
            $('#validationmodal').modal('show');
            upload.value = '';
            return false;
        }
        else
        {             
            // dijalankan
        }      
    }
    function ajxuploadValidation(){
        var upload = document.getElementById('ajx_file');
        var pathUpload= upload.value;

        // tipe file yang diizinkan
        var allowedExtensions = /(\.pdf|\.PDF)$/i;

        if (!allowedExtensions.exec(pathUpload)) {
            document.getElementById(
                'info-validation').innerHTML =
                '<p class="text-muted fs-12">Maaf hanya menerima file document yang bertipe .pdf | .PDF</p>';
            $('#validationmodal').modal('show');
            upload.value = '';
            return false;
        }
        else
        {             
            // dijalankan
        }      
    }
</script>
<script>
    $( "#btn-save" ).click(function() {
        $("#Form").submit(function () {
            $('#staticBackdrop').modal('show', true);
        });
    });
    $( "#btn-edit-save" ).click(function() {
        $("#Form-edit").submit(function () {
            $('#staticBackdrop').modal('show', true);
        });
    });
</script>
<script>
    $(document).ready(function () {
        load_data();
        function load_data(kriteria_sehat = ''){
            $('#table_medical').DataTable({
                responsive: true,
                autoWidth: false,
                stateSave: true,
                processing: true,
                serverSide: true,
                // dom: 'Bfrtip',
                // buttons: [
                //     'copyHtml5',
                //     'excelHtml5',
                //     'csvHtml5',
                //     'pdfHtml5'
                // ],
                ajax: {
                    url: "{{route('reguler.detail', $kode)}}",
                    data:{kriteria_sehat:kriteria_sehat}
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex' , "className": "text-center"},
                    // {data: 'no_lab', name: 'no_lab' , "className": "text-center"},
                    {data: 'nik', name: 'nik' , "className": "text-center"},
                    {data: 'fullname', name: 'fullname' , "className": "text-center"},
                    // {data: 'gender', name: 'gender' , "className": "text-center"},
                    // {data: 'umur', name: 'umur' , "className": "text-center"},
                    {data: 'area', name: 'area' , "className": "text-center"},
                    {data: 'department', name: 'department' , "className": "text-center"},
                    {data: 'work_location', name: 'work_location' , "className": "text-center"},
                    {data: 'tanggal_mcu', name: 'tanggal_mcu' , "className": "text-center"},
                    {data: 'kriteria_sehat', name: 'kriteria_sehat' , "className": "text-center"},
                    {data: 'action', name: 'action', "className": "text-center", orderable: false, searchable: false},
                ]
            });
            $('#table_medical tbody').on('click', 'tr', function () {
                //get id medical
                var id_medical = $(this).closest('tr').find('#medical_id').val();
                $("#id_medical").val(id_medical);
                $("#del_medical").val(id_medical);
                //reset modal form
                $('#status').val(null).trigger('change');
                $('#no_lab').val('');
                $('#tanggal_mcu').val(null);
                $('#file').val('');

                //edit
                var id = $(this).closest('tr').find('#id_detail_medical').val();
                var no_lab = $(this).closest('tr').find('#id_no_lab').val();
                var status = $(this).closest('tr').find('#id_status').val();
                var tgl = $(this).closest('tr').find('#id_tgl').val();
                var preview = $(this).closest('tr').find('#preview').val();
                $("#id").val(id);
                $("#ajx_no_lab").val(no_lab); 
                $("#ajx_tanggal_mcu").val(tgl); 
                if(status == 'SEHAT'){
                    $('#ajx_status').html('<option value="none">None</option><option value="SEHAT" selected>Sehat</option><option value="SEHAT DENGAN RESIKO">Sehat dengan resiko</option><option value="RESIKO TINGGI">Resiko tinggi</option>');                               
                }else if(status == 'SEHAT DENGAN RESIKO'){
                    $('#ajx_status').html('<option value="none">None</option><option value="SEHAT">Sehat</option><option value="SEHAT DENGAN RESIKO" selected>Sehat dengan resiko</option><option value="RESIKO TINGGI">Resiko tinggi</option>');   
                }else{
                    $('#ajx_status').html('<option value="none">None</option><option value="SEHAT">Sehat</option><option value="SEHAT DENGAN RESIKO">Sehat dengan resiko</option><option value="RESIKO TINGGI" selected>Resiko tinggi</option>');    
                }
                $("#show-preview").attr("src", preview);
                //reset modal edit
                $('#ajx_file').val('');
            });
        }
        $('#filter').click(function(){
            var kriteria_sehat = $('#kriteria_sehat').val();
            if(kriteria_sehat != '')
            {
                $('#table_medical').DataTable().destroy();
                load_data(kriteria_sehat);
            }else{
                $('#kriteria_sehat').val(null).trigger('change');
                $('#table_medical').DataTable().destroy();
                load_data();
            }
        });
        $('#refresh').click(function(){
            $('#kriteria_sehat').val(null).trigger('change');
            $('#table_medical').DataTable().destroy();
            load_data();
        });
    });
</script>
<script>
    @if(Session::has('success'))
        toastr.options =
        {
            "closeButton" : true,
            "progressBar" : true,
            "positionClass": "toast-bottom-right"
        }
        toastr.success("{{ session('success') }}");
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