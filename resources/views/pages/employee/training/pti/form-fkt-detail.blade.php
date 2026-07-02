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
                    <h4 class="text-primary">Detail Formulir Penilaian Pelaksanaan Pelatihan</h4>
                    </div>
                    <div class="col-lg-6">
                        @if($fpkt->id_atasan == $user->employee_id && $fpkt->date_peserta != null)
                        <a href="{{ route('training.emp.fkt.pti.approve.back') }}" class="btn btn-primary btn-label waves-effect waves-light float-end"><i class="ri-arrow-left-circle-line label-icon align-middle fs-16 me-2"></i> Back</a>
                        @else
                        <a href="{{ route('training.emp.fkt.pti.back') }}" class="btn btn-primary btn-label waves-effect waves-light float-end"><i class="ri-arrow-left-circle-line label-icon align-middle fs-16 me-2"></i> Back</a>
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
                                            @if(!empty($fpkt->id_fkt))
                                            <td>{{$fpkt->fkt->kode}}</td>
                                            @else
                                            <td>{{$fpkt->kode_fpkt}}</td>
                                            @endif
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
                                            @if(!empty($fpkt->id_fkt))
                                            <td>{{$fpkt->fkt->pemohon->fullname}}</td>
                                            @else
                                            <td>{{$fpkt->pemohon->fullname ?? '-'}}</td>
                                            @endif
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
                                            @if(!empty($fpkt->id_fkt))
                                            <td>{{$fpkt->fkt->pemohon->department->name ?? '-'}}</td>
                                            @else
                                            <td>{{$fpkt->pemohon->department->name ?? '-'}}</td>
                                            @endif
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4">
                                <label for="jenis" class="form-label col-form-label col-form-label-sm">Topik Pelatihan</label>
                            </div>
                            <div class="col-lg-8">
                                <table class="table table-sm table-nowrap fs-12">
                                    <tbody>
                                        <tr>
                                            <td>{{$fpkt->judul_fpkt ?? '-'}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4">
                                <label for="jenis" class="form-label col-form-label col-form-label-sm">Tanggal Pelaksanaan</label>
                            </div>
                            <div class="col-lg-8">
                                <table class="table table-sm table-nowrap fs-12">
                                    <tbody>
                                        <tr>
                                            <td>{{date('d M Y', strtotime($fpkt->date_pelaksanaan))}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4">
                                <label for="jenis" class="form-label col-form-label col-form-label-sm">Atasan Departemen</label>
                            </div>
                            <div class="col-lg-8">
                                <table class="table table-sm table-nowrap fs-12">
                                    <tbody>
                                        <tr>
                                            <td>{{$fpkt->atasan_dept->fullname}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4">
                                <label for="jenis" class="form-label col-form-label col-form-label-sm">Atasan Langsung</label>
                            </div>
                            <div class="col-lg-8">
                                <table class="table table-sm table-nowrap fs-12">
                                    <tbody>
                                        <tr>
                                            <td>{{$fpkt->atasan->fullname}}</td>
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
                                            @if(!empty($fpkt->id_fkt))
                                            <td>Program Pelatihan Tahunan</td>
                                            @else
                                            <td>Program Pelatihan Insidentil</td>
                                            @endif
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5"></div>
                </div>
                <!-- Modal Collective -->
                {{-- @if($fpkt->id_pemohon == $user->employee_id && $fpkt->date_peserta == null)
                <button type="button" class="btn btn-primary " data-bs-toggle="modal" data-bs-target="#collectiveModal">Collective Training</button>
                <br>
                <br>
                @endif
                @if($fpkt->id_atasan == $user->employee_id && $fpkt->date_peserta != null)
                <button type="button" class="btn btn-primary " data-bs-toggle="modal" data-bs-target="#collectiveApproveModal">Collective Training</button>
                <br>
                <br>
                @endif --}}
                {{--<div id="collectiveModal" class="modal fade" tabindex="-1" aria-labelledby="collectiveModalLabel" aria-hidden="true" style="display: none;">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="collectiveModalLabel">Collective Training</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                            </div>
                            <form action="{{ route('training.emp.fpkt.pti.collective', encrypt($fpkt->kode)) }}" method="GET">
                                @csrf
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <input type="hidden" name="kode_pelatihan" id="kode_pelatihan" value="{{$fpkt->kode}}">
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
                            <form action="{{ route('training.emp.fpkt.pti.collective.approve', encrypt($fpkt->kode)) }}" method="GET">
                                @csrf
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <input type="hidden" name="kode_judul" id="kode_judul" value="{{$fpkt->kode_judul}}">
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
                </div><!-- /.modal -->--}} 
                <table class="table table-striped bordered display nowrap" id="table_fkt" style="width:100%;">
                    <thead>
                        <tr>
                            <!-- <th scope="col" style="text-align:center">No</th> -->
                            <th scope="col" class="text-center">Action</th>
                            <th scope="col" class="text-center">Status</th>
                            <th scope="col" class="text-center">Nama</th>
                            <th scope="col" class="text-center">NIK</th>
                            <th scope="col" class="text-center">Jabatan</th>
                            <th scope="col" class="text-center">Nama Pelatihan</th>
                            <th scope="col" class="text-center">Pelaksanaan</th>
                            <th scope="col" class="text-center">Provider</th>
                            <th scope="col" class="text-center">Biaya</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($query_fpkt as $qry_fpkt)
                            @php
                            $fpkt = \App\Models\Trainingfpkt::where('id_fkt', $qry_fpkt->id)->get();
                            $status = $fpkt->unique('status')->pluck('status');
                            @endphp
                            @if(!empty($qry_fpkt->id_fkt)) 
                                {{-- Tahunan --}}
                                <tr>
                                    <td class="text-center">
                                        <div class="dropdown d-inline-block">
                                            <button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ri-more-fill align-middle"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                {{-- <li><a href="{{ route('training.emp.fpkt.pti.form', encrypt($qry_fkt->id)) }}" class="dropdown-item"><i class="ri-eye-2-line align-bottom me-2 text-muted"></i> Detail FPKT</a></li> --}}
                                                @if($qry_fpkt->id_peserta == $user->employee_id)
                                                    @if(!empty($qry_fpkt->date_peserta))
                                                        <li><a href="{{ route('training.emp.fpkt.pti.form', encrypt($qry_fpkt->id)) }}" class="dropdown-item"><i class="ri-eye-2-line align-bottom me-2 text-muted"></i> Detail FPKP</a></li>
                                                    @else
                                                        <li><a href="{{ route('training.emp.fpkt.pti.form', encrypt($qry_fpkt->id)) }}" class="dropdown-item"><i class="ri-edit-line align-bottom me-2 text-muted"></i> Isi FPKP</a></li>
                                                    @endif
                                                @endif
                                                @if($qry_fpkt->id_atasan == $user->employee_id)
                                                    @if(!empty($qry_fpkt->date_atasan))
                                                        <li><a href="{{ route('training.emp.fpkt.pti.form', encrypt($qry_fpkt->id)) }}" class="dropdown-item"><i class="ri-eye-2-line align-bottom me-2 text-muted"></i> Detail FPKP</a></li>
                                                    @else
                                                        <li><a href="{{ route('training.emp.fpkt.pti.form', encrypt($qry_fpkt->id)) }}" class="dropdown-item"><i class="ri-edit-line align-bottom me-2 text-muted"></i> Isi FPKP</a></li>
                                                    @endif
                                                @endif
                                                <li><a href="{{ route('public.training.fpkp.pdf', encrypt($qry_fpkt->id)) }}" class="dropdown-item" target="_blank"><i class="ri-file-pdf-line align-bottom me-2 text-muted"></i> Print FPKP</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @if($qry_fpkt->status == 4)
                                            <a href="javascript:void(0)" data-id="{{encrypt($qry_fpkt->id)}}" data-bs-toggle="modal" data-bs-target="#statusModal" <span class="badge text-bg-info view-status">{{$qry_fpkt->training_status->name}}</span></a>
                                        @endif
                                        @if($qry_fpkt->status == 5)
                                            <a href="javascript:void(0)" data-id="{{encrypt($qry_fpkt->id)}}" data-bs-toggle="modal" data-bs-target="#statusModal" <span class="badge text-bg-secondary view-status">{{$qry_fpkt->training_status->name}}</span></a>
                                        @endif
                                        @if($qry_fpkt->status == 9)
                                            <a href="javascript:void(0)" data-id="{{encrypt($qry_fpkt->id)}}" data-bs-toggle="modal" data-bs-target="#statusModal" <span class="badge text-bg-warning view-status">{{$qry_fpkt->training_status->name}}</span></a>
                                        @endif
                                        @if($qry_fpkt->status == 10)
                                            <a href="javascript:void(0)" data-id="{{encrypt($qry_fpkt->id)}}" data-bs-toggle="modal" data-bs-target="#statusModal" <span class="badge text-bg-secondary view-status">{{$qry_fpkt->training_status->name}}</span></a>
                                        @endif
                                        @if($qry_fpkt->status == 11)
                                            <a href="javascript:void(0)" data-id="{{encrypt($qry_fpkt->id)}}" data-bs-toggle="modal" data-bs-target="#statusModal" <span class="badge text-bg-info view-status">{{$qry_fpkt->training_status->name}}</span></a>
                                        @endif
                                        @if($qry_fpkt->status == 12)
                                            <a href="javascript:void(0)" data-id="{{encrypt($qry_fpkt->id)}}" data-bs-toggle="modal" data-bs-target="#statusModal" <span class="badge text-bg-success view-status">{{$qry_fpkt->training_status->name}}</span></a>
                                        @endif
                                    </td>
                                    <td class="text-center">{{$qry_fpkt->peserta->fullname ?? '-'}}</td>
                                    <td class="text-center">{{$qry_fpkt->peserta->nik ?? '-'}}</td>
                                    <td class="text-center">{{$qry_fpkt->peserta->position->nama ?? '-'}}</td>
                                    <td class="text-center">{{$qry_fpkt->judul_fpkt ?? '-'}}</td>
                                    <td class="text-center">{{date('d M Y', strtotime($qry_fpkt->date_pelaksanaan))}}</td>
                                    <td class="text-center">{{$qry_fpkt->vendor->nama ?? '-'}}</td>
                                    <td class="text-center">Rp. {{number_format($qry_fpkt->biaya_fpkt,'2',',','.') ?? '0'}}</td>                        
                                </tr>
                            @else
                                {{-- Insidentil --}}
                                <tr>
                                    <td class="text-center">
                                        <div class="dropdown d-inline-block">
                                            <button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ri-more-fill align-middle"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                {{-- <li><a href="{{ route('training.emp.fpkt.pti.form', encrypt($qry_fkt->id)) }}" class="dropdown-item"><i class="ri-eye-2-line align-bottom me-2 text-muted"></i> Detail FPKT</a></li> --}}
                                                @if($qry_fpkt->id_peserta == $user->employee_id)
                                                    @if(!empty($qry_fpkt->date_peserta))
                                                        <li><a href="{{ route('training.emp.fpkt.pti.form', encrypt($qry_fpkt->id)) }}" class="dropdown-item"><i class="ri-eye-2-line align-bottom me-2 text-muted"></i> Detail FPKP</a></li>
                                                    @else
                                                        <li><a href="{{ route('training.emp.fpkt.pti.form', encrypt($qry_fpkt->id)) }}" class="dropdown-item"><i class="ri-edit-line align-bottom me-2 text-muted"></i> Isi FPKP</a></li>
                                                    @endif
                                                @endif
                                                @if($qry_fpkt->id_atasan == $user->employee_id)
                                                    @if(!empty($qry_fpkt->date_atasan))
                                                        <li><a href="{{ route('training.emp.fpkt.pti.form', encrypt($qry_fpkt->id)) }}" class="dropdown-item"><i class="ri-eye-2-line align-bottom me-2 text-muted"></i> Detail FPKP</a></li>
                                                    @else
                                                        <li><a href="{{ route('training.emp.fpkt.pti.form', encrypt($qry_fpkt->id)) }}" class="dropdown-item"><i class="ri-edit-line align-bottom me-2 text-muted"></i> Isi FPKP</a></li>
                                                    @endif
                                                @endif
                                                <li><a href="{{ route('public.training.fpkp.pdf', encrypt($qry_fpkt->id)) }}" class="dropdown-item" target="_blank"><i class="ri-file-pdf-line align-bottom me-2 text-muted"></i> Print FPKP</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @if($qry_fpkt->status == 4)
                                            <a href="javascript:void(0)" data-id="{{encrypt($qry_fpkt->id)}}" data-bs-toggle="modal" data-bs-target="#statusModal" <span class="badge text-bg-info view-status">{{$qry_fpkt->training_status->name}}</span></a>
                                        @endif
                                        @if($qry_fpkt->status == 5)
                                            <a href="javascript:void(0)" data-id="{{encrypt($qry_fpkt->id)}}" data-bs-toggle="modal" data-bs-target="#statusModal" <span class="badge text-bg-secondary view-status">{{$qry_fpkt->training_status->name}}</span></a>
                                        @endif
                                        @if($qry_fpkt->status == 9)
                                            <a href="javascript:void(0)" data-id="{{encrypt($qry_fpkt->id)}}" data-bs-toggle="modal" data-bs-target="#statusModal" <span class="badge text-bg-warning view-status">{{$qry_fpkt->training_status->name}}</span></a>
                                        @endif
                                        @if($qry_fpkt->status == 10)
                                            <a href="javascript:void(0)" data-id="{{encrypt($qry_fpkt->id)}}" data-bs-toggle="modal" data-bs-target="#statusModal" <span class="badge text-bg-secondary view-status">{{$qry_fpkt->training_status->name}}</span></a>
                                        @endif
                                        @if($qry_fpkt->status == 11)
                                            <a href="javascript:void(0)" data-id="{{encrypt($qry_fpkt->id)}}" data-bs-toggle="modal" data-bs-target="#statusModal" <span class="badge text-bg-info view-status">{{$qry_fpkt->training_status->name}}</span></a>
                                        @endif
                                        @if($qry_fpkt->status == 12)
                                            <a href="javascript:void(0)" data-id="{{encrypt($qry_fpkt->id)}}" data-bs-toggle="modal" data-bs-target="#statusModal" <span class="badge text-bg-success view-status">{{$qry_fpkt->training_status->name}}</span></a>
                                        @endif
                                    </td>
                                    <td class="text-center">{{$qry_fpkt->peserta->fullname ?? '-'}}</td>
                                    <td class="text-center">{{$qry_fpkt->peserta->nik ?? '-'}}</td>
                                    <td class="text-center">{{$qry_fpkt->peserta->position->nama ?? '-'}}</td>
                                    <td class="text-center">{{$qry_fpkt->judul_fpkt ?? '-'}}</td>
                                    <td class="text-center">{{date('d M Y', strtotime($qry_fpkt->date_pelaksanaan))}}</td>
                                    <td class="text-center">{{$qry_fpkt->vendor->nama ?? '-'}}</td>
                                    <td class="text-center">Rp. {{number_format($qry_fpkt->biaya_fpkt,'2',',','.') ?? '0'}}</td>                        
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table> 
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
                $("#emp_peserta_fpkt").html(result.nama_peserta_fpkt);
                // $("#tanggal_pemohon_fpkt").html(result.date_pemohon_fpkt);
                $("#date_pelaksanaan_fpkt").html(result.date_pelaksanaan_fpkt);
                if(result.id_status_fpkt == 4){
                    $("#nama_status_fpkt").html('<span class="badge badge-outline-info">'+result.nama_status_fpkt+'</span>');
                }
                if(result.id_status_fpkt == 5){
                    $("#nama_status_fpkt").html('<span class="badge badge-outline-secondary">'+result.nama_status_fpkt+'</span>');
                }
                if(result.id_status_fpkt == 9){
                    $("#nama_status_fpkt").html('<span class="badge badge-outline-warning">'+result.nama_status_fpkt+'</span>');
                }
                if(result.id_status_fpkt == 10){
                    $("#nama_status_fpkt").html('<span class="badge badge-outline-secondary">'+result.nama_status_fpkt+'</span>');
                }
                if(result.id_status_fpkt == 11){
                    $("#nama_status_fpkt").html('<span class="badge badge-outline-info">'+result.nama_status_fpkt+'</span>');
                }
                if(result.id_status_fpkt == 12){
                    $("#nama_status_fpkt").html('<span class="badge badge-outline-success">'+result.nama_status_fpkt+'</span>');
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
