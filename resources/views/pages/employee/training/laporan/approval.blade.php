@extends('layouts.general')
@section('link')
<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- Required meta tags -->
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
@endsection
@section('content')
<!-- start page -->
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <form id="Formlaporan" action="{{route('training.emp.store.approval.laporan')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('put')
                    <div class="row mb-3">
                        <div class="col-lg-6">
                            <h4 class="text-primary">Formulir Laporan Pelaksanaan Training</h4>
                        </div>
                        <div class="col-lg-6">
                            <a href="{{ route('training.emp.back.approval.laporan') }}" class="btn btn-primary btn-label waves-effect waves-light float-end"><i class="ri-arrow-left-circle-line label-icon align-middle fs-16 me-2"></i> Back</a>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-lg-3">
                            <label for="tgl_laporan" class="form-label col-form-label">Tanggal Laporan</label>
                        </div>
                        <div class="col-lg-3">
                            <div class="input-group">
                                <input type="text" name="tgl_laporan" id="tgl_laporan" class="form-control @error("tgl_laporan") is-invalid @enderror" placeholder="Pilih Tanggal" value="{{date('d F, Y', strtotime($query->tgl_laporan))}}" style="Background-color: #eff2f7;" readonly>
                                <span class="input-group-text"><i class="ri-calendar-event-line"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-lg-3">
                            <label for="nama_peserta" class="form-label col-form-label">Nama</label>
                        </div>
                        <div class="col-lg-6">
                            <input type="hidden" class="form-control" id="id_record" name="id_record" placeholder="Masukkan Id Record" value="{{$query->id}}">
                            <input type="hidden" class="form-control" id="id_peserta" name="id_peserta" placeholder="Masukkan Id" value="{{$query->id_employee}}">
                            <input type="text" class="form-control" id="nama_peserta" name="nama_peserta" placeholder="Masukkan Nama" value="{{$query->employee->fullname}}" style="Background-color: #eff2f7;" readonly>
                        </div>
                        <div class="col-lg-3">                                        
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-lg-3">
                            <label for="bagian" class="form-label col-form-label">Bagian</label>
                        </div>
                        <div class="col-lg-6">
                            <input type="text" class="form-control" id="bagian" name="bagian" placeholder="Masukkan Bagian" value="{{$query->employee->section->nama ?? '-'}}" style="Background-color: #eff2f7;" readonly>
                        </div>
                        <div class="col-lg-3">                                        
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-lg-3">
                            <label for="departement" class="form-label col-form-label">Departemen</label>
                        </div>
                        <div class="col-lg-6">
                            <input type="text" class="form-control" id="departement" name="departement" placeholder="Masukkan Departemen" value="{{$query->employee->department->name ?? '-'}}" style="Background-color: #eff2f7;" readonly>
                        </div>
                        <div class="col-lg-3">                                        
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-lg-3">
                            <label for="judul" class="form-label col-form-label">Nama Program Pelatihan</label>
                        </div>
                        <div class="col-lg-6">
                            <input type="text" class="form-control" id="judul" name="judul" placeholder="Masukkan Program Pelatihan" value="{{$query->judul}}" style="Background-color: #eff2f7;" readonly>
                        </div>
                        <div class="col-lg-3">                                        
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-lg-3">
                            <label for="tgl_pelaksanaan" class="form-label col-form-label">Tanggal Pelaksanaan</label>
                        </div>
                        <div class="col-lg-6">
                            <input type="hidden" class="form-control" id="tgl_pelaksanaan" name="tgl_pelaksanaan" placeholder="Masukkan Tanggal" value="{{$query->start_date}} to {{$query->end_date}}" style="Background-color: #eff2f7;" readonly>
                            <input type="text" class="form-control" id="nama_tgl_pelaksanaan" name="nama_tgl_pelaksanaan" placeholder="Masukkan Tanggal" value="{{date('d, M Y', strtotime($query->start_date))}} to {{date('d, M Y', strtotime($query->end_date))}}" style="Background-color: #eff2f7;" readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="card-body p-4 border-top border-top-dashed">
                            <div class="mb-4">
                                <label for="no_1" class="form-label">1. Isi Pelatihan?</label>
                                <div class="col-lg-10">
                                    <textarea class="form-control" id="isi_pelatihan" name="isi_pelatihan" rows="3" style="Background-color: #eff2f7;" readonly>{{$query->isi_pelatihan}}</textarea>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label for="no_2" class="form-label">2. Apa yang dipelajari?</label>
                                <div class="col-lg-10">
                                    <textarea class="form-control" id="dipelajari" name="dipelajari" rows="3" style="Background-color: #eff2f7;" readonly>{{$query->dipelajari}}</textarea>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label for="no_3" class="form-label">3. Bagaimana anda mengimplementasikan materi training dalam pekerjaan?</label>
                                <div class="col-lg-10">
                                    <textarea class="form-control" id="implementasi" name="implementasi" rows="3" style="Background-color: #eff2f7;" readonly>{{$query->implementasi}}</textarea>
                                </div>
                            </div>  
                            @if($user->employee_id == $query->ttd_atasan)                                                  
                            <div class="mb-4">
                                <label for="no_4" class="form-label">4. Kolom Supervisor (atasan langsung)<span class="text-danger">*</span></label>
                                <label for="no_4" class="form-label">(Setelah menerima laporan hasil pelatihan, harapan atasan terhadap bawahannya untuk menindak lanjuti di masa yang akan datang)</label>
                                <div class="col-lg-10">
                                    <textarea class="form-control" id="hasil" name="hasil" rows="3" required></textarea>
                                </div>
                            </div>
                            @else
                            <div class="mb-4">
                                <label for="no_4" class="form-label">4. Kolom Supervisor (atasan langsung)<span class="text-danger">*</span></label>
                                <label for="no_4" class="form-label">(Setelah menerima laporan hasil pelatihan, harapan atasan terhadap bawahannya untuk menindak lanjuti di masa yang akan datang)</label>
                                <div class="col-lg-10">
                                    <textarea class="form-control" id="hasil" name="hasil" rows="3" style="Background-color: #eff2f7;" readonly>{{$query->hasil ?? ''}}</textarea>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="card-body p-4 border-top border-top-dashed">
                            <div class="row mb-4">
                                @if(!empty($query->sertifikat))
                                <div class="col-lg-2">
                                    <label class="form-label">Dokumen Sertifikat</label>
                                    <br>
                                    <a href="{{route('profile.training.sertifikat',encrypt($query->id))}}" target="_blank" class="btn btn-secondary btn-label waves-effect waves-light"><i class="ri-file-download-line label-icon align-middle fs-16 me-2"></i> Sertifikat</a>
                                </div>
                                @endif
                                @if(!empty($query->materi))
                                <div class="col-lg-2">
                                    <label class="form-label">Dokumen Materi</label>
                                    <br>
                                    <a href="{{route('profile.training.materi',encrypt($query->id))}}" target="_blank" class="btn btn-secondary btn-label waves-effect waves-light"><i class="ri-file-download-line label-icon align-middle fs-16 me-2"></i> Materi</a>
                                </div>
                                @endif
                                <div class="col-lg-3">
                                    <label class="form-label">Preview Laporan</label>
                                    <br>
                                    <a href="{{route('profile.training.laporan.pdf',encrypt($query->id))}}" target="_blank" class="btn btn-secondary btn-label waves-effect waves-light"><i class="ri-file-download-line label-icon align-middle fs-16 me-2"></i> Laporan</a>
                                </div>
                            </div>
                        </div>
                    </div>                                           
                    <div class="row" id="form-submit">
                        <div class="col-lg-12">
                            <div class="hstack gap-2 d-print-none" style="justify-content: flex-end;">
                                <button type="submit" id="btn-submit" class="btn btn-primary">Approve</button>
                            </div>
                        </div>
                    </div>                                            
                </form>
            </div><!-- end card body -->
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
@endsection
@section('javascript')
<script>    
    $(document).ready(function() {
        $("#btn-submit").click(function() {
            $("#Formlaporan").submit(function () {
                $('#staticBackdrop').modal('show', true);
            });
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
