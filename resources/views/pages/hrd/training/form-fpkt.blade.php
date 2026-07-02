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
            <h4 class="mb-sm-0">TRAINING</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">HRD</a></li>
                    <li class="breadcrumb-item">Training</li>
                    <li class="breadcrumb-item active">Formulir</li>
                </ol>
            </div>

        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">  
                <form action="{{ route('training.fpkt.form.store') }}" method="post">
                    @csrf
                    @method('PUT')                                      
                    <div class="row mb-3">
                        <div class="col-lg-6">
                        <h4 class="text-primary">Formulir Penilaian Kebutuhan Training</h4>
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
                                    <table class="table table-sm table-nowrap fs-12">
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
                                    <table class="table table-sm table-nowrap fs-12">
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
                                    <table class="table table-sm table-nowrap fs-12">
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
                                    <table class="table table-sm table-nowrap fs-12">
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
                                    <label for="jenis" class="form-label col-form-label col-form-label-sm">Dokumen</label>
                                </div>
                                <div class="col-lg-7">
                                    <table class="table table-sm table-nowrap fs-12">
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <a href="{{ route('profile.training.fkt.pdf', encrypt($fkt->kode)) }}" target="_blank" class="btn btn-danger btn-sm">
                                                        <i class="ri-file-pdf-line align-bottom me-1"></i> Dokumen formulir kebutuhan training
                                                    </a>
                                                    <a href="{{ route('profile.training.fpkt.pdf', encrypt($fkt->kode)) }}" target="_blank" class="btn btn-danger btn-sm">
                                                        <i class="ri-file-pdf-line align-bottom me-1"></i> Dokumen formulir penilaian kebutuhan training
                                                    </a>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2"></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-lg-10">
                            <!-- Tables Border Colors -->
                            <div class="table-responsive">
                                <table class="table table-bordered border-secondary fs-12 table-nowrap">
                                    <thead>
                                        <tr>
                                            <th scope="col">
                                                Latar Belakang Usulan Training : <br>
                                                <p class="text-muted"><i>(Penjelasan mengenai keterkaitan antara usulan topik training dengan pekerjaan saat ini).</i></p>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th scope="row">
                                                <div>
                                                    <textarea class="form-control" id="latar_belakang" name="latar_belakang" rows="3" style="Background-color: #eff2f7;" readonly>{{ old('latar_belakang', $fpkt->latar_belakang ?? '') }}</textarea>
                                                </div>
                                            </th>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="card-body p-4 border-top border-top-dashed">
                            <div data-simplebar data-simplebar-auto-hide="false" style="max-width: 100%;">
                                <table class="table table-borderless fs-12" style="table-layout: fixed; width: 300%;">
                                    <thead class="align-middle">
                                        <tr class="table-active">
                                            <!-- <th scope="col" style="width: 2%;">#</th> -->
                                            <th scope="col" style="width: 10%; text-align: center;">
                                                Tujuan Training
                                            </th>
                                            <th scope="col" style="width: 10%; text-align: center;">
                                                Kompetensi yang Diharapkan
                                            </th>
                                            <th scope="col" style="width: 15%; text-align: center;">
                                                Skill / Knowledge
                                            </th>
                                            <th scope="col" style="width: 15%; text-align: center;">Level Skill / Knowledge (diisi oleh peserta)</th>
                                            <th scope="col" style="width: 10%; text-align: center;">Level Skill / Knowledge (diisi oleh atasan langsung)</th>
                                            <th scope="col" id="h-provider" style="width: 10%; text-align: center;">Rata - rata Level Skill /Knowledge</th>
                                            <th scope="col" id="h-biaya" style="width: 10%; text-align: center;">Kebutuhan Training</th>
                                            <th scope="col" style="width: 2%; text-align: center;"></th>
                                        </tr>
                                        <tr>
                                            <!-- <th scope="col" style="width: 2%;"></th> -->
                                            <th scope="col" style="width: 10%; text-align: center;">
                                                <p class="text-muted"><i>Tuliskan Tujuan yang ingin dicapai setelah mengikuti training!</i></p>
                                            </th>
                                            <th scope="col" style="width: 10%; text-align: center;">
                                                <p class="text-muted"><i>Tuliskan Kompetensi apa saja yang dapat menunjang dalam mencapai tujuan training ini!</i></p>
                                            </th>
                                            <th scope="col" style="width: 15%; text-align: center;">
                                                <p class="text-muted"><i>Sebutkan minimal 3 komponen Skill / Knowledge yang saat ini dimiliki oleh karyawan dan diperlukan untuk merepresentasikan kompetensi yang diharapkan!</i></p>
                                            </th>
                                            <th scope="col" style="width: 10%; text-align: center;">
                                                <p class="text-muted"><i>Tingkat Skill / Knowledge menurut penilaian diri sendiri (skala 1 - 5).</i></p>
                                            </th>
                                            <th scope="col" style="width: 15%; text-align: center;">
                                                <p class="text-muted"><i>Tingkat Skill / Knowledge menurut penilaian atasan langsung (skala 1 - 5).</i></p>
                                            </th>
                                            <th scope="col" style="width: 10%; text-align: center;">
                                                <p class="text-muted"><i>Rata - rata tingkat Skill / Knowledge menurut penilaian diri sendiri dan atasan langsung.</i></p>
                                            </th>
                                            <th scope="col" id="h-provider" style="width: 10%; text-align: center;">
                                                <p class="text-muted"><i>Tingkat kebutuhan training</i></p>
                                            </th>
                                            <th scope="col" style="width: 2%;"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="newlink">
                                        @if($arr_fpkt->isNotEmpty())
                                            @php($i = 0)
                                            @foreach($arr_fpkt as $data_fpkt)
                                                <tr id="1" class="produk">
                                                    <th scope="row" class="produk-id" hidden>{{$loop->iteration}}</th>
                                                    <td class="text-start">
                                                        <input type="hidden" id="nomor" name="no_urut[]" value="{{$loop->iteration}}">
                                                        <div class="input-group mb-2">
                                                            <input type="text" class="form-control form-control-sm" id="tujuan-{{$loop->iteration}}" name="tujuan-{{$loop->iteration}}[]" value="{{$data_fpkt->tujuan}}" style="Background-color: #eff2f7;" readonly>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="input-group mb-2">
                                                            <input type="text" class="form-control form-control-sm" id="kompetensi-{{$loop->iteration}}" name="kompetensi-{{$loop->iteration}}[]" value="{{$data_fpkt->kompetensi}}" style="Background-color: #eff2f7;" readonly>
                                                        </div>
                                                    </td>             
                                                    <td>
                                                        <div class="input-group mb-2">
                                                            <input type="text" class="form-control form-control-sm" id="skill-{{$loop->iteration}}" name="skill-{{$loop->iteration}}[]" value="{{$data_fpkt->skill}}" style="Background-color: #eff2f7;" readonly>
                                                        </div>
                                                    </td>               
                                                    <td>
                                                        <div class="input-group mb-2">
                                                            <input type="number" class="form-control form-control-sm peserta" id="level_peserta-{{$loop->iteration}}" name="level_peserta-{{$loop->iteration}}[]" value="{{$data_fpkt->level_peserta}}" style="Background-color: #eff2f7;" readonly>
                                                        </div>
                                                    </td>              
                                                    <td>
                                                        <div class="input-group mb-2">
                                                            <input type="number" class="form-control form-control-sm atasan" id="level_atasan-{{$loop->iteration}}" name="level_atasan-{{$loop->iteration}}[]" value="{{$data_fpkt->level_atasan}}" style="Background-color: #eff2f7;" readonly>
                                                        </div>
                                                    </td>                
                                                    <td>
                                                        <div class="input-group mb-2">
                                                            <input type="number" class="form-control form-control-sm" id="level_rata-{{$loop->iteration}}" name="level_rata-{{$loop->iteration}}[]" value="{{$data_fpkt->level_rata}}" style="Background-color: #eff2f7;" readonly>
                                                        </div>
                                                    </td>               
                                                    <td>
                                                        <div class="input-group mb-2">
                                                            <input type="text" class="form-control form-control-sm" id="level_kebutuhan-{{$loop->iteration}}" name="level_kebutuhan-{{$loop->iteration}}[]" value="{{$data_fpkt->level_kebutuhan}}" style="Background-color: #eff2f7;" readonly>
                                                        </div>
                                                    </td>                
                                                    <td>
                                                        <!-- <a href="#" class="btn btn-soft-danger" disabled><i class="ri-delete-bin-line"></i></a> -->
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                    <tbody>
                                        <tr id="newForm" style="display: none;">
                                            <td class="d-none" colspan="5">
                                                <p>Add New Form</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            @if($arr_fpkt->isNotEmpty())
                                                @if(!empty($cek_peserta))
                                                    @if($arr_fpkt->sum('level_peserta') == 0)
                                                    <td colspan="5">
                                                        <a href="#modal-penilaian-peserta"
                                                            class="btn btn-soft-success"
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#modalPeserta"><i
                                                                class="ri-add-fill me-1 align-bottom"></i> Update Nilai</a>
                                                    </td>                                                                            
                                                    @endif
                                                @endif
                                                @if(!empty($cek_atasan))
                                                    @if($arr_fpkt->sum('level_atasan') == 0)
                                                    <td colspan="5">
                                                        <a href="#modal-penilaian-atasan"
                                                            class="btn btn-soft-success"
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#modalAtasan">
                                                            <i class="ri-add-fill me-1 align-bottom"></i> 
                                                            Update Nilai
                                                        </a>
                                                    </td>
                                                    @endif
                                                @endif
                                            @else
                                            <td colspan="5">
                                                <a href="javascript:new_link()" id="add-item"
                                                    class="btn btn-soft-success"><i
                                                        class="ri-add-fill me-1 align-bottom"></i> Add New</a>
                                            </td>
                                            @endif
                                        </tr>
                                        <tr class="border-top border-top-dashed mt-2">
                                            <td colspan="3"></td>
                                            <td colspan="2" class="p-0"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-lg-10">
                            <!-- Tables Border Colors -->
                            <div class="table-responsive">
                                <table class="table table-bordered border-secondary fs-12 table-nowrap">
                                    <thead>
                                        <tr>
                                            <th scope="col">
                                                Catatan Dari Atasan : <br>
                                                <p class="text-muted"><i>(diisi jika skor kebutuhan training 5 atau 4).</i></p>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th scope="row">
                                                <div>
                                                    <textarea class="form-control" id="catatan" name="catatan" rows="3" style="Background-color: #eff2f7;" readonly>{{ old('catatan', $fpkt->catatan ?? '') }}</textarea>
                                                </div>
                                            </th>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="form-submit">
                        <div class="col-lg-12">
                            <button class="btn btn-primary float-end" name="action" value="pemohon" type="submit">Submit</button>
                        </div>
                    </div>
                </form>
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
@endsection
@section('javascript')
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