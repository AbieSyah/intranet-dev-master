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
@endsection
@section('content')
<!-- start page -->
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">                                        
                <div class="row mb-3">
                    <div class="col-lg-6">
                    <h4 class="text-primary">Detail Formulir Kebutuhan Training (FKT)</h4>
                    </div>
                    <div class="col-lg-6">
                        @if($fkt->id_penilai == $user->employee_id && $fkt->date_peserta != null)
                        <a href="{{ route('training.emp.fkt.ptt.approve.back') }}" class="btn btn-primary btn-label waves-effect waves-light float-end"><i class="ri-arrow-left-circle-line label-icon align-middle fs-16 me-2"></i> Back</a>
                        @else
                        <a href="{{ route('training.emp.fkt.ptt.back') }}" class="btn btn-primary btn-label waves-effect waves-light float-end"><i class="ri-arrow-left-circle-line label-icon align-middle fs-16 me-2"></i> Back</a>
                        @endif
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-lg-7">
                        <div class="row">
                            <div class="col-lg-4">
                                <label for="topik" class="form-label col-form-label col-form-label-sm">No Form</label>
                            </div>
                            <div class="col-lg-8">
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
                            <div class="col-lg-4">
                                <label for="jenis" class="form-label col-form-label col-form-label-sm">Nama Pemohon</label>
                            </div>
                            <div class="col-lg-8">
                                <table class="table table-sm table-nowrap fs-12">
                                    <tbody>
                                        <tr>
                                            <td>{{$fkt->pemohon->fullname ?? '-'}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4">
                                <label for="jenis" class="form-label col-form-label col-form-label-sm">Departemen</label>
                            </div>
                            <div class="col-lg-8">
                                <table class="table table-sm table-nowrap fs-12">
                                    <tbody>
                                        <tr>
                                            <td>{{$fkt->pemohon->department->name ?? '-'}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4">
                                <label for="jenis" class="form-label col-form-label col-form-label-sm">Tahun Usulan Program</label>
                            </div>
                            <div class="col-lg-8">
                                <table class="table table-sm table-nowrap fs-12">
                                    <tbody>
                                        <tr>
                                            <td>{{$fkt->tahun_usulan}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4">
                                <label for="jenis" class="form-label col-form-label col-form-label-sm">Tahun Rencana Pelaksanaan</label>
                            </div>
                            <div class="col-lg-8">
                                <table class="table table-sm table-nowrap fs-12">
                                    <tbody>
                                        <tr>
                                            <td>{{$fkt->tahun_pelaksanaan}}</td>
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
                <!-- Modal Collective -->
                @if($fkt->id_pemohon == $user->employee_id && $fkt->date_peserta == null)
                <button type="button" class="btn btn-primary " data-bs-toggle="modal" data-bs-target="#collectiveModal">Collective Training</button>
                <br>
                <br>
                @endif
                @if($fkt->id_penilai == $user->employee_id && $fkt->date_peserta != null)
                <button type="button" class="btn btn-primary " data-bs-toggle="modal" data-bs-target="#collectiveApproveModal">Collective Training</button>
                <br>
                <br>
                @endif
                <div id="collectiveModal" class="modal fade" tabindex="-1" aria-labelledby="collectiveModalLabel" aria-hidden="true" style="display: none;">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="collectiveModalLabel">Collective Training</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                            </div>
                            <form action="{{ route('training.emp.fpkt.ptt.collective', encrypt($fkt->kode)) }}" method="GET">
                                @csrf
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <input type="hidden" name="kode_pelatihan" id="kode_pelatihan" value="{{$fkt->kode}}">
                                        <div class="form-group">
                                            <label for="pelatihan" class="col-form-label">Pilih Pelatihan</label>
                                            <select class="fs-12 form-control form-control-sm select2 @error('pelatihan') is-invalid @enderror" name="pelatihan" id="pelatihan" data-placeholder="Pilih Pelatihan" required>
                                                <option selected="true" disabled="true"></option>
                                                @foreach($query_fkt->unique('judul') as $pelatihan)
                                                    <option value="{{ $pelatihan->judul }}">{{ $pelatihan->judul }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary ">Generate</button>
                                </div>
                            </form>

                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div><!-- /.modal -->
                <div id="collectiveApproveModal" class="modal fade" tabindex="-1" aria-labelledby="collectiveApproveModalLabel" aria-hidden="true" style="display: none;">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="collectiveApproveModalLabel">Collective Training</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                            </div>
                            <form action="{{ route('training.emp.fpkt.ptt.collective.approve', encrypt($fkt->kode)) }}" method="GET">
                                @csrf
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <input type="hidden" name="kode_judul" id="kode_judul" value="{{$fkt->kode_judul}}">
                                        <div class="form-group">
                                            <label for="pelatihan2" class="col-form-label">Pilih Pelatihan</label>
                                            <select class="fs-12 form-control form-control-sm select2 @error('pelatihan2') is-invalid @enderror" name="pelatihan2" id="pelatihan2" data-placeholder="Pilih Pelatihan" required>
                                                <option selected="true" disabled="true"></option>
                                                @foreach($query_fkt->unique('judul') as $pelatihan)
                                                    <option value="{{ $pelatihan->judul }}">{{ $pelatihan->judul }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary ">Generate</button>
                                </div>
                            </form>

                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div><!-- /.modal -->                                             
                <table class="table table-striped bordered display nowrap" id="table_fkt" style="width:100%;">
                    <thead>
                        <tr>
                        <!-- <th scope="col" style="text-align:center">No</th> -->
                        <th scope="col" style="text-align:center">Action</th>
                        <th scope="col" style="text-align:center">Status</th>
                        <th scope="col" style="text-align:center">Nama</th>
                        <th scope="col" style="text-align:center">NIK</th>
                        <th scope="col" style="text-align:center">Jabatan</th>
                        <th scope="col" style="text-align:center">Nama Pelatihan</th>
                        <th scope="col" style="text-align:center">Sifat Pelatihan</th>
                        <th scope="col" style="text-align:center">Alasan</th>
                        <th scope="col" style="text-align:center">Bulan Pelaksanaan</th>
                        <th scope="col" style="text-align:center">Provider</th>
                        <th scope="col" style="text-align:center">Biaya</th>
                        <th scope="col" style="text-align:center">Akomodasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($query_fkt as $qry_fkt)
                            @php
                            $fpkt = \App\Models\Trainingfpkt::where('id_fkt', $qry_fkt->id)->get();
                            $status = $fpkt->unique('status')->pluck('status');
                            @endphp
                            @if($qry_fkt->id_pemohon == $user->employee_id)
                                <tr>
                                    <!-- <td>{{$loop->iteration}}</td> -->
                                    @if($fpkt->isNotEmpty())
                                        @if($status[0] == 12)
                                        <td>
                                            <div class="dropdown d-inline-block">
                                                <button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="ri-more-fill align-middle"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li><a href="{{ route('profile.training.fpkt.ptt.print', encrypt($qry_fkt->id)) }}" class="dropdown-item" target="_blank"><i class="ri-file-pdf-line align-bottom me-2 text-muted"></i> Print FPKT</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                        @else
                                        <td>
                                            <div class="dropdown d-inline-block">
                                                <button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="ri-more-fill align-middle"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li><a href="{{ route('training.emp.fpkt.ptt.form', encrypt($qry_fkt->id)) }}" class="dropdown-item"><i class="ri-eye-2-line align-bottom me-2 text-muted"></i> Detail FPKT</a></li>
                                                    <li><a href="{{ route('profile.training.fpkt.ptt.print', encrypt($qry_fkt->id)) }}" class="dropdown-item" target="_blank"><i class="ri-file-pdf-line align-bottom me-2 text-muted"></i> Print FPKT</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                        @endif
                                        @if($fpkt->sum('level_atasan') > 0 && $fpkt->sum('level_peserta') > 0)
                                            @if($status[0] == 12)
                                            <td><a href="#" data-id="{{encrypt($qry_fkt->id)}}" data-bs-toggle="modal" data-bs-target="#statusModal" <span class="badge text-bg-success view-status">Finished</span></a></td>
                                            @else
                                            <td><a href="#" data-id="{{encrypt($qry_fkt->id)}}" data-bs-toggle="modal" data-bs-target="#statusModal" <span class="badge text-bg-secondary view-status">Approved</span></a></td>
                                            @endif
                                        @else
                                            <td><a href="#" data-id="{{encrypt($qry_fkt->id)}}" data-bs-toggle="modal" data-bs-target="#statusModal" <span class="badge text-bg-warning view-status">Waiting Assessment</span></a></td>
                                        @endif
                                    @else
                                        <td>
                                            <div class="dropdown d-inline-block">
                                                <button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="ri-more-fill align-middle"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li><a href="{{ route('training.emp.fpkt.ptt.form', encrypt($qry_fkt->id)) }}" class="dropdown-item"><i class="ri-edit-line align-bottom me-2 text-muted"></i> Isi FPKT</a></li>
                                                    <li><a href="{{ route('profile.training.fpkt.ptt.print', encrypt($qry_fkt->id)) }}" class="dropdown-item" target="_blank"><i class="ri-file-pdf-line align-bottom me-2 text-muted"></i> Print FPKT</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                        <td><a href="#" <span class="badge text-bg-primary">Draft</span></a></td>
                                    @endif
                                    <td>{{$qry_fkt->peserta->fullname ?? '-'}}</td>
                                    <td>{{$qry_fkt->peserta->nik ?? '-'}}</td>
                                    <td>{{$qry_fkt->peserta->position->nama ?? '-'}}</td>
                                    <td>{{$qry_fkt->judul ?? '-'}}</td>
                                    <td>{{$qry_fkt->sifat ?? '-'}}</td>
                                    <td>{{$qry_fkt->alasan ?? '-'}}</td>
                                    <td>{{$qry_fkt->bulan_pelaksanaan ?? '-'}}</td>
                                    <td>{{$qry_fkt->provider->nama ?? '-'}}</td>
                                    <td>{{$qry_fkt->biaya_fkt ?? '-'}}</td>
                                    <td>
                                        a) Menginap : {{$qry_fkt->penginapan ?? '-'}} <br>
                                        b) Transportasi : {{$qry_fkt->transportasi ?? '-'}}
                                    </td>
                                </tr>
                            @else
                                @if($qry_fkt->id_peserta == $user->employee_id)
                                    <tr>
                                        <!-- <td>{{$loop->iteration}}</td> -->
                                        @if($fpkt->isNotEmpty())
                                            @if($fpkt->sum('level_atasan') > 0 && $fpkt->sum('level_peserta') > 0)
                                                <td>
                                                    <div class="dropdown d-inline-block">
                                                        <button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="ri-more-fill align-middle"></i>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end">
                                                            <li><a href="{{ route('profile.training.fpkt.ptt.print', encrypt($qry_fkt->id)) }}" class="dropdown-item" target="_blank"><i class="ri-file-pdf-line align-bottom me-2 text-muted"></i> Print FPKT</a></li>
                                                        </ul>
                                                    </div>
                                                </td>
                                                @if($status[0] == 12)
                                                <td><a href="#" data-id="{{encrypt($qry_fkt->id)}}" data-bs-toggle="modal" data-bs-target="#statusModal" <span class="badge text-bg-success view-status">Finished</span></a></td>
                                                @else
                                                <td><a href="#" data-id="{{encrypt($qry_fkt->id)}}" data-bs-toggle="modal" data-bs-target="#statusModal" <span class="badge text-bg-secondary view-status">Approved</span></a></td>
                                                @endif
                                            @else
                                                @if($fpkt->sum('level_peserta') > 0)
                                                    <td>
                                                        <div class="dropdown d-inline-block">
                                                            <button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                                <i class="ri-more-fill align-middle"></i>
                                                            </button>
                                                            <ul class="dropdown-menu dropdown-menu-end">
                                                                <li><a href="{{ route('training.emp.fpkt.ptt.form', encrypt($qry_fkt->id)) }}" class="dropdown-item"><i class="ri-edit-line align-bottom me-2 text-muted"></i> Detail FPKT</a></li>
                                                                <li><a href="{{ route('profile.training.fpkt.ptt.print', encrypt($qry_fkt->id)) }}" class="dropdown-item" target="_blank"><i class="ri-file-pdf-line align-bottom me-2 text-muted"></i> Print FPKT</a></li>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                    <td><a href="#" data-id="{{encrypt($qry_fkt->id)}}" data-bs-toggle="modal" data-bs-target="#statusModal" <span class="badge text-bg-warning view-status">Waiting Assessment</span></a></td>
                                                @else
                                                    <td>
                                                        <div class="dropdown d-inline-block">
                                                            <button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                                <i class="ri-more-fill align-middle"></i>
                                                            </button>
                                                            <ul class="dropdown-menu dropdown-menu-end">
                                                                <li><a href="{{ route('training.emp.fpkt.ptt.form', encrypt($qry_fkt->id)) }}" class="dropdown-item"><i class="ri-edit-line align-bottom me-2 text-muted"></i> Isi FPKT</a></li>
                                                                <li><a href="{{ route('profile.training.fpkt.ptt.print', encrypt($qry_fkt->id)) }}" class="dropdown-item" target="_blank"><i class="ri-file-pdf-line align-bottom me-2 text-muted"></i> Print FPKT</a></li>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                    <td><a href="#" data-id="{{encrypt($qry_fkt->id)}}" data-bs-toggle="modal" data-bs-target="#statusModal" <span class="badge text-bg-warning view-status">Waiting Assessment</span></a></td>
                                                @endif
                                            @endif
                                        @else
                                            <td>
                                                <div class="dropdown d-inline-block">
                                                    <button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="ri-more-fill align-middle"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li><a href="{{ route('profile.training.fpkt.ptt.print', encrypt($qry_fkt->id)) }}" class="dropdown-item" target="_blank"><i class="ri-file-pdf-line align-bottom me-2 text-muted"></i> Print FPKT</a></li>
                                                    </ul>
                                                </div>
                                            </td>
                                            <td><a href="#" <span class="badge text-bg-primary">Draft</span></a></td>
                                        @endif
                                        <td>{{$qry_fkt->peserta->fullname ?? '-'}}</td>
                                        <td>{{$qry_fkt->peserta->nik ?? '-'}}</td>
                                        <td>{{$qry_fkt->peserta->position->nama ?? '-'}}</td>
                                        <td>{{$qry_fkt->judul ?? '-'}}</td>
                                        <td>{{$qry_fkt->sifat ?? '-'}}</td>
                                        <td>{{$qry_fkt->alasan ?? '-'}}</td>
                                        <td>{{$qry_fkt->bulan_pelaksanaan ?? '-'}}</td>
                                        <td>{{$qry_fkt->provider->nama ?? '-'}}</td>
                                        <td>{{$qry_fkt->biaya_fkt ?? '-'}}</td>
                                        <td>
                                            a) Menginap : {{$qry_fkt->penginapan ?? '-'}} <br>
                                            b) Transportasi : {{$qry_fkt->transportasi ?? '-'}}
                                        </td>
                                    </tr>
                                @else
                                    @if($qry_fkt->id_penilai == $user->employee_id)
                                        <tr>
                                            <!-- <td>{{$loop->iteration}}</td> -->
                                            @if($fpkt->isNotEmpty())
                                                @if($fpkt->sum('level_atasan') > 0 && $fpkt->sum('level_peserta') > 0)
                                                    <td>
                                                        <div class="dropdown d-inline-block">
                                                            <button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                                <i class="ri-more-fill align-middle"></i>
                                                            </button>
                                                            <ul class="dropdown-menu dropdown-menu-end">
                                                                <li><a href="{{ route('profile.training.fpkt.ptt.print', encrypt($qry_fkt->id)) }}" class="dropdown-item" target="_blank"><i class="ri-file-pdf-line align-bottom me-2 text-muted"></i> Print FPKT</a></li>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                    @if($status[0] == 12)
                                                    <td><a href="#" data-id="{{encrypt($qry_fkt->id)}}" data-bs-toggle="modal" data-bs-target="#statusModal" <span class="badge text-bg-success view-status">Finished</span></a></td>
                                                    @else
                                                    <td><a href="#" data-id="{{encrypt($qry_fkt->id)}}" data-bs-toggle="modal" data-bs-target="#statusModal" <span class="badge text-bg-secondary view-status">Approved</span></a></td>
                                                    @endif
                                                @else
                                                    @if($fpkt->sum('level_atasan') > 0)
                                                        <td>
                                                            <div class="dropdown d-inline-block">
                                                                <button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                                    <i class="ri-more-fill align-middle"></i>
                                                                </button>
                                                                <ul class="dropdown-menu dropdown-menu-end">
                                                                    <li><a href="{{ route('training.emp.fpkt.ptt.form', encrypt($qry_fkt->id)) }}" class="dropdown-item"><i class="ri-edit-line align-bottom me-2 text-muted"></i> Detail FPKT</a></li>
                                                                    <li><a href="{{ route('profile.training.fpkt.ptt.print', encrypt($qry_fkt->id)) }}" class="dropdown-item" target="_blank"><i class="ri-file-pdf-line align-bottom me-2 text-muted"></i> Print FPKT</a></li>
                                                                </ul>
                                                            </div>
                                                        </td>
                                                        <td><a href="#" data-id="{{encrypt($qry_fkt->id)}}" data-bs-toggle="modal" data-bs-target="#statusModal" <span class="badge text-bg-warning view-status">Waiting Assessment</span></a></td>
                                                    @else
                                                        <td>
                                                            <div class="dropdown d-inline-block">
                                                                <button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                                    <i class="ri-more-fill align-middle"></i>
                                                                </button>
                                                                <ul class="dropdown-menu dropdown-menu-end">
                                                                    <li><a href="{{ route('training.emp.fpkt.ptt.form', encrypt($qry_fkt->id)) }}" class="dropdown-item"><i class="ri-edit-line align-bottom me-2 text-muted"></i> Isi FPKT</a></li>
                                                                    <li><a href="{{ route('profile.training.fpkt.ptt.print', encrypt($qry_fkt->id)) }}" class="dropdown-item" target="_blank"><i class="ri-file-pdf-line align-bottom me-2 text-muted"></i> Print FPKT</a></li>
                                                                </ul>
                                                            </div>
                                                        </td>
                                                        <td><a href="#" data-id="{{encrypt($qry_fkt->id)}}" data-bs-toggle="modal" data-bs-target="#statusModal" <span class="badge text-bg-warning view-status">Waiting Assessment</span></a></td>
                                                    @endif
                                                @endif
                                            @else
                                                <td>
                                                    <div class="dropdown d-inline-block">
                                                        <button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="ri-more-fill align-middle"></i>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end">
                                                            <li><a href="{{ route('profile.training.fpkt.ptt.print', encrypt($qry_fkt->id)) }}" class="dropdown-item" target="_blank"><i class="ri-file-pdf-line align-bottom me-2 text-muted"></i> Print FPKT</a></li>
                                                        </ul>
                                                    </div>
                                                </td>
                                                <td><a href="#" <span class="badge text-bg-primary">Draft</span></a></td>
                                            @endif
                                            <td>{{$qry_fkt->peserta->fullname ?? '-'}}</td>
                                            <td>{{$qry_fkt->peserta->nik ?? '-'}}</td>
                                            <td>{{$qry_fkt->peserta->position->nama ?? '-'}}</td>
                                            <td>{{$qry_fkt->judul ?? '-'}}</td>
                                            <td>{{$qry_fkt->sifat ?? '-'}}</td>
                                            <td>{{$qry_fkt->alasan ?? '-'}}</td>
                                            <td>{{$qry_fkt->bulan_pelaksanaan ?? '-'}}</td>
                                           
                                            <td>{{$qry_fkt->provider->nama ?? '-'}}</td>
                                            <td>{{$qry_fkt->biaya_fkt ?? '-'}}</td>
                                            <td>
                                                a) Menginap : {{$qry_fkt->penginapan ?? '-'}} <br>
                                                b) Transportasi : {{$qry_fkt->transportasi ?? '-'}}
                                            </td>
                                            
                                        </tr>
                                    @endif
                                @endif
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div><!-- end card -->
    </div>
    <!--end col-->
</div>
<!--end row-->
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
<!-- Toastr Notifications-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
@endsection
@section('javascript')
<script>
    $(function () {    
        $('.select2').select2();
        $('#pelatihan').select2({dropdownParent: $('#collectiveModal .modal-content')});
        $('#pelatihan2').select2({dropdownParent: $('#collectiveApproveModal .modal-content')});
    });
</script>
<script>
    var jml = {{ Js::from($total_fkt) }};
    // console.log(jml)
    if(jml <= 1){
        let table_fkt = new DataTable('#table_fkt', {
            scrollX: true,
            scrollY: '150px'
        });
    }else if(jml == 2){
        let table_fkt = new DataTable('#table_fkt', {
            scrollX: true,
            scrollY: '200px'
        });
    }else{
        let table_fkt = new DataTable('#table_fkt', {
            scrollX: true
        });
    }

    $('#table_fkt').on("click", ".view-status", function() {
        var id = $(this).data("id");
        $.ajax({
            url: "{{ route('training.emp.fkt.ptt.status') }}",
            type: "POST",
            data: {
                id: id,
                _token: '{{ csrf_token() }}'
            },
            dataType: 'json',
            success: function(result) {
                if(result.status_fpkt == 11){
                    $("#status_judul").html('<div class="ribbon ribbon-secondary ribbon-shape text-uppercase">'+ result.status_fpkt +'</div>');
                }else if(result.status_fpkt == 12){
                    $("#status_judul").html('<div class="ribbon ribbon-success ribbon-shape text-uppercase">'+ result.status_fpkt +'</div>');
                }else if(result.status_fpkt == 10){
                    $("#status_judul").html('<div class="ribbon ribbon-warning ribbon-shape text-uppercase">'+ result.status_fpkt +'</div>');
                }else{
                    $("#status_judul").html('<div class="ribbon ribbon-warning ribbon-shape text-uppercase">'+ result.status_fpkt +'</div>');
                }
                $("#status_training").html('<div class="row">'+
                        '<div class="table-responsive">'+
                        '<table class="table table-borderless table-sm table-nowrap">'+
                            '<tbody>'+
                                '<tr>'+
                                    '<td scope="row">1.</td>'+
                                    '<td scope="row">Approval FPKT</td>'+
                                    '<td>:</td>'+
                                    '<td></td>'+
                                '</tr>'+
                                '<tr>'+
                                    '<td scope="row"></td>'+
                                    '<td scope="row">1. Peserta</td>'+
                                    '<td>:</td>'+
                                    '<td>'+result.nama_peserta+'</td>'+
                                '</tr>'+
                                '<tr>'+
                                    '<td scope="row"></td>'+
                                    '<td scope="row"></td>'+
                                    '<td>:</td>'+
                                    '<td>'+result.date_peserta+'</td>'+
                                '</tr>'+
                                '<tr>'+
                                    '<td scope="row"></td>'+
                                    '<td scope="row">2. Atasan</td>'+
                                    '<td>:</td>'+
                                    '<td>'+result.nama_penilai+'</td>'+
                                '</tr>'+
                                '<tr>'+
                                    '<td scope="row"></td>'+
                                    '<td scope="row"></td>'+
                                    '<td>:</td>'+
                                    '<td>'+result.date_penilai+'</td>'+
                                '</tr>'+
                                '<tr>'+
                                    '<td scope="row"></td>'+
                                    '<td scope="row">3. Verifikasi</td>'+
                                    '<td>:</td>'+
                                    '<td>'+result.nama_hrd+'</td>'+
                                '</tr>'+
                                '<tr>'+
                                    '<td scope="row"></td>'+
                                    '<td scope="row"></td>'+
                                    '<td>:</td>'+
                                    '<td>'+result.date_hrd+'</td>'+
                                '</tr>'+                                
                            '</tbody>'+
                        '</table>'+
                        '</div>'+
                    '</div>'+
                '</div>');
            }
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
