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
            @if($title == 'hrd')
            <h4 class="mb-sm-0">VERIFICATION TRAINING</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">HRD</a></li>
                    <li class="breadcrumb-item">Training</li>
                    <li class="breadcrumb-item active">Verification</li>
                </ol>
            </div>
            @endif
            <!-- view mr.mizukami -->
            @if($title == 'direktur')
            <h4 class="mb-sm-0">APPROVAL TRAINING</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">HRD</a></li>
                    <li class="breadcrumb-item">Training</li>
                    <li class="breadcrumb-item active">Approval</li>
                </ol>
            </div>
            @endif
            <!-- view mr.sakurai -->
            @if($title == 'presiden')
            <h4 class="mb-sm-0">APPROVAL TRAINING</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">HRD</a></li>
                    <li class="breadcrumb-item">Training</li>
                    <li class="breadcrumb-item active">Approval</li>
                </ol>
            </div>
            @endif

        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">                                        
                <div class="row mb-3">
                    <div class="col-lg-6">
                        <!-- view hrd -->
                        @if($title == 'hrd')
                            <h4 class="text-primary">Verification Formulir Kebutuhan Training</h4>
                            @endif
                        <!-- view mr.mizukami -->
                        @if($title == 'direktur')
                            <h4 class="text-primary">Approval Formulir Kebutuhan Training</h4>
                        @endif
                        <!-- view mr.sakurai -->
                        @if($title == 'presiden')
                            <h4 class="text-primary">Approval Formulir Kebutuhan Training</h4>
                        @endif
                    </div>
                    <div class="col-lg-6">
                        <a href="{{ route('training.ptt.index') }}" class="btn btn-primary btn-label waves-effect waves-light float-end"><i class="ri-arrow-left-circle-line label-icon align-middle fs-16 me-2"></i> Back</a>
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
                            @if($title == 'direktur')
                                @if($fkt->status == 'VERIFIED 1')
                                    {{-- mr. mizukami --}}
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
                                            @if($fkt->status == 'PROPOSED')
                                                <td style="text-align: center;"><span class="badge text-bg-warning">Waiting Verification HRD</span></td>
                                                <td style="text-align: center;">
                                                    <div class="dropdown d-inline-block">
                                                        <button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="ri-more-fill align-middle"></i></button>
                                                        <ul class="dropdown-menu dropdown-menu-end">
                                                            <li><a href="#" data-id="{{encrypt($fkt->kode_judul)}}" data-bs-toggle="modal" data-bs-target=".bs-example-modal-center" class="dropdown-item view-status"><i class="ri-checkbox-line align-bottom me-2 text-muted"></i> Verification</a></li>
                                                            <li><a href="{{ route('training.ptt.fkt.pdf', encrypt($fkt->kode_judul)) }}" class="dropdown-item" target="_blank"><i class="ri-file-pdf-line align-bottom me-2 text-muted"></i> Document FKT</a></li>
                                                            <li><a href="{{ route('training.ptt.fpkt.pdf', encrypt($fkt->kode_judul)) }}" class="dropdown-item" target="_blank"><i class="ri-file-pdf-line align-bottom me-2 text-muted"></i> Document FPKT</a></li>
                                                        </ul>
                                                    </div>
                                                </td>
                                            @else
                                                @if($user->employee_id == $fkt->id_verified)
                                                    @if(empty($fkt->date_verified))
                                                    <td style="text-align: center;"><span class="badge text-bg-warning">Waiting Approval Direktur Produksi</span></td>
                                                    <td style="text-align: center;">
                                                        <div class="dropdown d-inline-block">
                                                            <button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="ri-more-fill align-middle"></i></button>
                                                            <ul class="dropdown-menu dropdown-menu-end">
                                                                <li><a href="#" data-id="{{encrypt($fkt->kode_judul)}}" data-bs-toggle="modal" data-bs-target=".bs-verified-modal-center" class="dropdown-item verified-status"><i class="ri-checkbox-line align-bottom me-2 text-muted"></i> Approval</a></li>
                                                                <li><a href="{{ route('training.ptt.fkt.pdf', encrypt($fkt->kode_judul)) }}" class="dropdown-item" target="_blank"><i class="ri-file-pdf-line align-bottom me-2 text-muted"></i> Document FKT</a></li>
                                                                <li><a href="{{ route('training.ptt.fpkt.pdf', encrypt($fkt->kode_judul)) }}" class="dropdown-item" target="_blank"><i class="ri-file-pdf-line align-bottom me-2 text-muted"></i> Document FPKT</a></li>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                    @else
                                                    <td style="text-align: center;"><span class="badge text-bg-success">Approved Direktur Produksi</span></td>
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
                                                @elseif($user->employee_id == $fkt->id_approval)
                                                    @if(empty($fkt->date_approval))
                                                    <td style="text-align: center;"><span class="badge text-bg-warning">Waiting Approval Presiden Direktur</span></td>
                                                    <td style="text-align: center;">
                                                        <div class="dropdown d-inline-block">
                                                            <button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="ri-more-fill align-middle"></i></button>
                                                            <ul class="dropdown-menu dropdown-menu-end">
                                                                <li><a href="#" data-id="{{encrypt($fkt->kode_judul)}}" data-bs-toggle="modal" data-bs-target=".bs-approved-modal-center" class="dropdown-item approved-status"><i class="ri-checkbox-line align-bottom me-2 text-muted"></i> Approval</a></li>
                                                                <li><a href="{{ route('training.ptt.fkt.pdf', encrypt($fkt->kode_judul)) }}" class="dropdown-item" target="_blank"><i class="ri-file-pdf-line align-bottom me-2 text-muted"></i> Document FKT</a></li>
                                                                <li><a href="{{ route('training.ptt.fpkt.pdf', encrypt($fkt->kode_judul)) }}" class="dropdown-item" target="_blank"><i class="ri-file-pdf-line align-bottom me-2 text-muted"></i> Document FPKT</a></li>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                    @else
                                                    <td style="text-align: center;"><span class="badge text-bg-primary">Finished</span></td>
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
                                                @else
                                                @if(!empty($fkt->date_approval))
                                                <td style="text-align: center;"><span class="badge text-bg-primary">Finished</span></td>
                                                @else
                                                @if(!empty($fkt->date_verified))
                                                <td style="text-align: center;"><span class="badge text-bg-success">Approved Direktur Produksi</span></td>
                                                @else
                                                <td style="text-align: center;"><span class="badge text-bg-success">Verified HRD</span></td>
                                                @endif
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
                                                @endif
                                            @endif
                                        @endif
                                    </tr>
                                @endif
                            @endif
                            @if($title == 'presiden')
                                @if($fkt->status == 'APPROVED')
                                    {{-- mr. sakurai --}}
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
                                            @if($fkt->status == 'PROPOSED')
                                                <td style="text-align: center;"><span class="badge text-bg-warning">Waiting Verification HRD</span></td>
                                                <td style="text-align: center;">
                                                    <div class="dropdown d-inline-block">
                                                        <button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="ri-more-fill align-middle"></i></button>
                                                        <ul class="dropdown-menu dropdown-menu-end">
                                                            <li><a href="#" data-id="{{encrypt($fkt->kode_judul)}}" data-bs-toggle="modal" data-bs-target=".bs-example-modal-center" class="dropdown-item view-status"><i class="ri-checkbox-line align-bottom me-2 text-muted"></i> Verification</a></li>
                                                            <li><a href="{{ route('training.ptt.fkt.pdf', encrypt($fkt->kode_judul)) }}" class="dropdown-item" target="_blank"><i class="ri-file-pdf-line align-bottom me-2 text-muted"></i> Document FKT</a></li>
                                                            <li><a href="{{ route('training.ptt.fpkt.pdf', encrypt($fkt->kode_judul)) }}" class="dropdown-item" target="_blank"><i class="ri-file-pdf-line align-bottom me-2 text-muted"></i> Document FPKT</a></li>
                                                        </ul>
                                                    </div>
                                                </td>
                                            @else
                                                @if($user->roles()->pluck('id')->first() == '51')
                                                    @if(empty($fkt->date_verified))
                                                        <td style="text-align: center;"><span class="badge text-bg-warning">Waiting Approval Direktur Produksi</span></td>
                                                        <td style="text-align: center;">
                                                            <div class="dropdown d-inline-block">
                                                                <button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="ri-more-fill align-middle"></i></button>
                                                                <ul class="dropdown-menu dropdown-menu-end">
                                                                    <li><a href="#" data-id="{{encrypt($fkt->kode_judul)}}" data-bs-toggle="modal" data-bs-target=".bs-verified-modal-center" class="dropdown-item verified-status"><i class="ri-checkbox-line align-bottom me-2 text-muted"></i> Approval</a></li>
                                                                    <li><a href="{{ route('training.ptt.fkt.pdf', encrypt($fkt->kode_judul)) }}" class="dropdown-item" target="_blank"><i class="ri-file-pdf-line align-bottom me-2 text-muted"></i> Document FKT</a></li>
                                                                    <li><a href="{{ route('training.ptt.fpkt.pdf', encrypt($fkt->kode_judul)) }}" class="dropdown-item" target="_blank"><i class="ri-file-pdf-line align-bottom me-2 text-muted"></i> Document FPKT</a></li>
                                                                </ul>
                                                            </div>
                                                        </td>
                                                    @else
                                                    <td style="text-align: center;"><span class="badge text-bg-success">Approved Direktur Produksi</span></td>
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
                                                @elseif($user->roles()->pluck('id')->first() == '49')
                                                    @if(empty($fkt->date_approval))
                                                    <td style="text-align: center;"><span class="badge text-bg-warning">Waiting Approval Presiden Direktur</span></td>
                                                    <td style="text-align: center;">
                                                        <div class="dropdown d-inline-block">
                                                            <button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="ri-more-fill align-middle"></i></button>
                                                            <ul class="dropdown-menu dropdown-menu-end">
                                                                <li><a href="#" data-id="{{encrypt($fkt->kode_judul)}}" data-bs-toggle="modal" data-bs-target=".bs-approved-modal-center" class="dropdown-item approved-status"><i class="ri-checkbox-line align-bottom me-2 text-muted"></i> Approval</a></li>
                                                                <li><a href="{{ route('training.ptt.fkt.pdf', encrypt($fkt->kode_judul)) }}" class="dropdown-item" target="_blank"><i class="ri-file-pdf-line align-bottom me-2 text-muted"></i> Document FKT</a></li>
                                                                <li><a href="{{ route('training.ptt.fpkt.pdf', encrypt($fkt->kode_judul)) }}" class="dropdown-item" target="_blank"><i class="ri-file-pdf-line align-bottom me-2 text-muted"></i> Document FPKT</a></li>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                    @else
                                                    <td style="text-align: center;"><span class="badge text-bg-primary">Finished</span></td>
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
                                                @else
                                                @if(!empty($fkt->date_approval))
                                                <td style="text-align: center;"><span class="badge text-bg-primary">Finished</span></td>
                                                @else
                                                    @if(!empty($fkt->date_verified))
                                                    <td style="text-align: center;"><span class="badge text-bg-success">Approved Direktur Produksi</span></td>
                                                    @else
                                                    <td style="text-align: center;"><span class="badge text-bg-success">Verified HRD</span></td>
                                                    @endif
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
                                                @endif
                                            @endif
                                        @endif
                                    </tr>
                                @endif
                            @endif
                            @if($title == 'hrd')
                                {{-- role HRD --}}
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
                                        @if($fkt->status == 'PROPOSED')
                                            <td style="text-align: center;"><span class="badge text-bg-warning">Waiting Verification HRD</span></td>
                                            <td style="text-align: center;">
                                                <div class="dropdown d-inline-block">
                                                    <button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="ri-more-fill align-middle"></i></button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li><a href="#" data-id="{{encrypt($fkt->kode_judul)}}" data-bs-toggle="modal" data-bs-target=".bs-example-modal-center" class="dropdown-item view-status"><i class="ri-checkbox-line align-bottom me-2 text-muted"></i> Verification</a></li>
                                                        <li><a href="{{ route('training.ptt.fkt.pdf', encrypt($fkt->kode_judul)) }}" class="dropdown-item" target="_blank"><i class="ri-file-pdf-line align-bottom me-2 text-muted"></i> Document FKT</a></li>
                                                        <li><a href="{{ route('training.ptt.fpkt.pdf', encrypt($fkt->kode_judul)) }}" class="dropdown-item" target="_blank"><i class="ri-file-pdf-line align-bottom me-2 text-muted"></i> Document FPKT</a></li>
                                                    </ul>
                                                </div>
                                            </td>
                                        @else
                                            @if($user->roles()->pluck('id')->first() == '51')
                                                @if(empty($fkt->date_verified))
                                                <td style="text-align: center;"><span class="badge text-bg-warning">Waiting Approval Direktur Produksi</span></td>
                                                <td style="text-align: center;">
                                                    <div class="dropdown d-inline-block">
                                                        <button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="ri-more-fill align-middle"></i></button>
                                                        <ul class="dropdown-menu dropdown-menu-end">
                                                            <li><a href="#" data-id="{{encrypt($fkt->kode_judul)}}" data-bs-toggle="modal" data-bs-target=".bs-verified-modal-center" class="dropdown-item verified-status"><i class="ri-checkbox-line align-bottom me-2 text-muted"></i> Approval</a></li>
                                                            <li><a href="{{ route('training.ptt.fkt.pdf', encrypt($fkt->kode_judul)) }}" class="dropdown-item" target="_blank"><i class="ri-file-pdf-line align-bottom me-2 text-muted"></i> Document FKT</a></li>
                                                            <li><a href="{{ route('training.ptt.fpkt.pdf', encrypt($fkt->kode_judul)) }}" class="dropdown-item" target="_blank"><i class="ri-file-pdf-line align-bottom me-2 text-muted"></i> Document FPKT</a></li>
                                                        </ul>
                                                    </div>
                                                </td>
                                                @else
                                                <td style="text-align: center;"><span class="badge text-bg-success">Approved Direktur Produksi</span></td>
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
                                            @elseif($user->roles()->pluck('id')->first() == '49')
                                                @if(empty($fkt->date_approval))
                                                <td style="text-align: center;"><span class="badge text-bg-warning">Waiting Approval Presiden Direktur</span></td>
                                                <td style="text-align: center;">
                                                    <div class="dropdown d-inline-block">
                                                        <button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="ri-more-fill align-middle"></i></button>
                                                        <ul class="dropdown-menu dropdown-menu-end">
                                                            <li><a href="#" data-id="{{encrypt($fkt->kode_judul)}}" data-bs-toggle="modal" data-bs-target=".bs-approved-modal-center" class="dropdown-item approved-status"><i class="ri-checkbox-line align-bottom me-2 text-muted"></i> Approval</a></li>
                                                            <li><a href="{{ route('training.ptt.fkt.pdf', encrypt($fkt->kode_judul)) }}" class="dropdown-item" target="_blank"><i class="ri-file-pdf-line align-bottom me-2 text-muted"></i> Document FKT</a></li>
                                                            <li><a href="{{ route('training.ptt.fpkt.pdf', encrypt($fkt->kode_judul)) }}" class="dropdown-item" target="_blank"><i class="ri-file-pdf-line align-bottom me-2 text-muted"></i> Document FPKT</a></li>
                                                        </ul>
                                                    </div>
                                                </td>
                                                @else
                                                <td style="text-align: center;"><span class="badge text-bg-primary">Finished</span></td>
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
                                            @else
                                            @if(!empty($fkt->date_approval))
                                            <td style="text-align: center;"><span class="badge text-bg-primary">Finished</span></td>
                                            @elseif(!empty($fkt->date_verified))
                                            <td style="text-align: center;"><span class="badge text-bg-success">Approved Direktur Produksi</span></td>
                                            @else
                                            <td style="text-align: center;"><span class="badge text-bg-success">Verified HRD</span></td>
                                            @endif
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
                                        @endif
                                    @endif
                                </tr>
                            @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- Approve HRD Modal -->
                <div class="modal fade bs-example-modal-center" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-top">
                        <div class="modal-content">
                            <div class="modal-body text-center p-5">
                                <div class="mt-4">
                                    <h4 class="mb-3">Apakah Anda Yakin ?</h4>
                                    <form id="form-approve" method="POST" action="{{ route('training.ptt.store') }}">
                                        @csrf
                                        @method('put')
                                        <input type="hidden" name="tipe_submit" id="tipe_submit" value="hrd">
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

                <!-- Approve Mr.Mizukami Modal -->
                <div class="modal fade bs-verified-modal-center" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-top">
                        <div class="modal-content">
                            <div class="modal-body text-center p-5">
                                <div class="mt-4">
                                    <h4 class="mb-3">Apakah Anda Yakin ?</h4>
                                    <form id="form-approve2" method="POST" action="{{ route('training.ptt.store') }}">
                                        @csrf
                                        @method('put')
                                        <input type="hidden" name="tipe_submit" id="tipe_submit" value="verified">
                                        <input type="hidden" name="kode_judul2" id="kode_judul2" value="">
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

                <!-- Approve Mr.Sakurai Modal -->
                <div class="modal fade bs-approved-modal-center" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-top">
                        <div class="modal-content">
                            <div class="modal-body text-center p-5">
                                <div class="mt-4">
                                    <h4 class="mb-3">Apakah Anda Yakin ?</h4>
                                    <form id="form-approve3" method="POST" action="{{ route('training.ptt.store') }}">
                                        @csrf
                                        @method('put')
                                        <input type="hidden" name="tipe_submit" id="tipe_submit" value="approved">
                                        <input type="hidden" name="kode_judul3" id="kode_judul3" value="">
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
    $('#table_fkt').on("click", ".verified-status", function() {
        var kode_judul = $(this).data("id");
        $('#kode_judul2').val(kode_judul);
    });
    $('#table_fkt').on("click", ".approved-status", function() {
        var kode_judul = $(this).data("id");
        $('#kode_judul3').val(kode_judul);
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
    //submit form approve mr.mizukami
    $("#form-approve2").submit(function(e) {
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
    //submit form approve mr.sakurai
    $("#form-approve3").submit(function(e) {
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