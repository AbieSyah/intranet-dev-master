@extends('layouts.general')
@section('link')
<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- Required meta tags -->
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
<!-- Select2-->
<link rel="stylesheet" href="/assets/libs/adminlte/select2/css/4.1.0/select2.min.css">
@endsection
@section('content')
<!-- start page -->
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <form id="Formlaporan" action="{{route('training.emp.store.laporan')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('put')
                    <div class="row mb-3">
                        <!-- Info Validation -->
                        <div class="col-lg-5">
                            <div class="alert alert-info alert-dismissible alert-label-icon rounded-label fade show" role="alert">
                                <i class="ri-error-warning-line label-icon"></i><strong>Kotak input yang diberi tanda <span class="text-danger">*</span> wajib diisi.</strong>
                            </div>
                        </div>
                        <div class="col-lg-7">
                            <a href="{{ url()->previous() }}" class="btn btn-primary btn-label waves-effect waves-light float-end"><i class="ri-arrow-left-circle-line label-icon align-middle fs-16 me-2"></i> Back</a>
                        </div>
                    </div> 
                    <div class="row mb-3">
                        <h4 class="text-primary">Formulir Laporan Pelaksanaan Training</h4>
                    </div>
                    <div class="row mb-3">
                        <div class="col-lg-3">
                            <label for="tgl_laporan" class="form-label col-form-label">Tanggal Laporan<span class="text-danger">*</span></label>
                        </div>
                        <div class="col-lg-3">
                            <div class="input-group">
                                <input type="text" name="tgl_laporan" id="tgl_laporan" class="form-control @error("tgl_laporan") is-invalid @enderror" placeholder="Pilih Tanggal" value="" required>
                                <span class="input-group-text"><i class="ri-calendar-event-line"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-lg-3">
                            <label for="nama_peserta" class="form-label col-form-label">Nama</label>
                        </div>
                        <div class="col-lg-6">
                            <input type="hidden" class="form-control" id="id_record" name="id_record" placeholder="Masukkan Id Record" value="{{$training_record->id}}">
                            <input type="hidden" class="form-control" id="id_peserta" name="id_peserta" placeholder="Masukkan Id" value="{{$training_record->id_employee}}">
                            <input type="text" class="form-control" id="nama_peserta" name="nama_peserta" placeholder="Masukkan Nama" value="{{$training_record->employee->fullname}}" style="Background-color: #eff2f7;" readonly>
                        </div>
                        <div class="col-lg-3">                                        
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-lg-3">
                            <label for="bagian" class="form-label col-form-label">Bagian</label>
                        </div>
                        <div class="col-lg-6">
                            <input type="text" class="form-control" id="bagian" name="bagian" placeholder="Masukkan Bagian" value="{{$training_record->employee->section->nama ?? '-'}}" style="Background-color: #eff2f7;" readonly>
                        </div>
                        <div class="col-lg-3">                                        
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-lg-3">
                            <label for="departement" class="form-label col-form-label">Departemen</label>
                        </div>
                        <div class="col-lg-6">
                            <input type="text" class="form-control" id="departement" name="departement" placeholder="Masukkan Departemen" value="{{$training_record->employee->department->name ?? '-'}}" style="Background-color: #eff2f7;" readonly>
                        </div>
                        <div class="col-lg-3">                                        
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-lg-3">
                            <label for="judul" class="form-label col-form-label">Nama Program Pelatihan</label>
                        </div>
                        <div class="col-lg-6">
                            <input type="text" class="form-control" id="judul" name="judul" placeholder="Masukkan Program Pelatihan" value="{{$training_record->judul}}" style="Background-color: #eff2f7;" readonly>
                        </div>
                        <div class="col-lg-3">                                        
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-lg-3">
                            <label for="tgl_pelaksanaan" class="form-label col-form-label">Tanggal Pelaksanaan</label>
                        </div>
                        <div class="col-lg-6">
                            <input type="hidden" class="form-control" id="tgl_pelaksanaan" name="tgl_pelaksanaan" placeholder="Masukkan Tanggal" value="{{$training_record->start_date}} to {{$training_record->end_date}}" style="Background-color: #eff2f7;" readonly>
                            <input type="text" class="form-control" id="nama_tgl_pelaksanaan" name="nama_tgl_pelaksanaan" placeholder="Masukkan Tanggal" value="{{date('d, M Y', strtotime($training_record->start_date))}} to {{date('d, M Y', strtotime($training_record->end_date))}}" style="Background-color: #eff2f7;" readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="card-body p-4 border-top border-top-dashed">
                            <div class="mb-4">
                                <label for="no_1" class="form-label">1. Isi Pelatihan?<span class="text-danger">*</span></label>
                                <div class="col-lg-10">
                                    <textarea class="form-control" id="isi_pelatihan" name="isi_pelatihan" rows="3" required></textarea>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label for="no_2" class="form-label">2. Apa yang dipelajari?<span class="text-danger">*</span></label>
                                <div class="col-lg-10">
                                    <textarea class="form-control" id="dipelajari" name="dipelajari" rows="3" required></textarea>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label for="no_3" class="form-label">3. Bagaimana anda mengimplementasikan materi training dalam pekerjaan?<span class="text-danger">*</span></label>
                                <div class="col-lg-10">
                                    <textarea class="form-control" id="implementasi" name="implementasi" rows="3" required></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="card-body p-4 border-top border-top-dashed">
                            <div class="mb-4">
                                <div class="col-lg-6">
                                    <label class="form-label">Upload Sertifikat</label>
                                    <div class="input-group">
                                        <input onchange="uploadSertifikatValidation(this);" type="file" class="form-control form-control text-sm col-sm-6" name="file_sertifikat" id="file_sertifikat">
                                        <button type="button" class="btn btn-outline-danger waves-effect waves-light" onclick="clearSertifikatUpload()">Remove</button>
                                    </div>
                                    <span class="form-text">hanya menerima file bertipe .pdf | .pptx | .jpg | .jpeg | .png | .xlsx | .docx</span>
                                </div>
                            </div>
                            <div class="mb-4">
                                <div class="col-lg-6">
                                    <label class="form-label">Upload Materi</label>
                                    <div class="input-group">
                                        <input type="file" class="form-control form-control text-sm col-sm-6" name="file_materi" id="file_materi">
                                        <button type="button" class="btn btn-outline-danger waves-effect waves-light" onclick="clearMateriUpload()">Remove</button>
                                    </div>
                                    <span class="form-text">menerima all file</span>
                                </div>
                            </div>
                            <div class="mb-4">
                                <div class="col-lg-6">
                                    <label class="form-label">Silahkan Isi Evaluasi di bawah ini!<span class="text-danger">*</span></label>
                                    <br>
                                    <div>
                                        <input type="hidden" class="form-control border-dashed" id="cek_evaluasi" name="cek_evaluasi" placeholder="-----" value="" required>
                                    </div>
                                    @error('cek_evaluasi')
                                        <div id="info-evaluasi" class="alert alert-danger">Anda Belum Mengisi Evaluasi.</div>
                                    @enderror
                                    <br>
                                    <a id="btn-evaluasi" href="{{route('training.emp.evaluasi.laporan', encrypt($training_record->id))}}"
                                        class="btn btn-soft-danger mt-2" target="_blank">
                                        <i class="ri-survey-line me-1 align-bottom"></i> 
                                        Evaluasi Training
                                    </a>
                                    <button id="cek_btn-evaluasi" type="button" class="btn btn-outline-info waves-effect waves-light mt-2">Check</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="card-body p-4 border-top border-top-dashed">
                            <div class="row">
                                <div class="col-lg-12">
                                    <table class="table table-bordered border-primary fs-10" >
                                        <thead>
                                            <tr>
                                                <th scope="col" style="text-align: center;">President Director</th>
                                                @if($training_record->employee->department->id == 3 || $training_record->employee->department->id == 4 || $training_record->employee->department->id == 5 || $training_record->employee->department->id == 6 || $training_record->employee->department->id == 8 || $training_record->employee->department->id == 9 || $training_record->employee->department->id == 10)
                                                <th scope="col" style="text-align: center;">Production Director / Jr. Director</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <div class="form-group">
                                                        <select class="form-control select2" id="ttd_presiden" name="ttd_presiden" required>
                                                            <option value="1122" selected="true">SATO, Ryo</option>
                                                        </select>
                                                    </div>
                                                </td>
                                                @if($training_record->employee->department->id == 3 || $training_record->employee->department->id == 4 || $training_record->employee->department->id == 5 || $training_record->employee->department->id == 6 || $training_record->employee->department->id == 8 || $training_record->employee->department->id == 9 || $training_record->employee->department->id == 10)
                                                <td>
                                                    <div class="form-group">
                                                        <select class="form-control select2" id="ttd_direktur" name="ttd_direktur" required>
                                                            <option value="1159" selected="true">MIZUKAMI, Tatsuhiro</option>
                                                        </select>
                                                    </div>
                                                </td>
                                                @else
                                                    <div hidden class="form-group">
                                                        <select class="form-control select2" id="ttd_direktur" name="ttd_direktur" required>
                                                            <option value="1159" selected="true">MIZUKAMI, Tatsuhiro</option>
                                                        </select>
                                                    </div>
                                                @endif
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>                                                        
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <table class="table table-bordered border-primary fs-10" >
                                        <thead>
                                            <tr>
                                                <th scope="col" style="text-align: center;">General Manager</th>
                                                <th scope="col" style="text-align: center;">Manager</th>
                                                <th scope="col" style="text-align: center;">Atasan Langsung</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <div class="form-group">
                                                        <select class="form-control select2" id="ttd_general_manager" name="ttd_general_manager" data-placeholder="Pilih Employee" required>
                                                            <option selected="true" disabled="true"></option>
                                                            @foreach ($employees as $emp)
                                                                <option value="{{$emp->id}}">{{$emp->nik}} -- {{$emp->fullname}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group">
                                                        <select class="form-control select2" id="ttd_manager" name="ttd_manager" data-placeholder="Pilih Employee" required>
                                                            <option selected="true" disabled="true"></option>
                                                            @foreach ($employees as $emp)
                                                                <option value="{{$emp->id}}">{{$emp->nik}} -- {{$emp->fullname}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group">
                                                        <select class="form-control select2" id="ttd_atasan" name="ttd_atasan" data-placeholder="Pilih Employee" required>
                                                            <option selected="true" disabled="true"></option>
                                                            @foreach ($employees as $emp)
                                                                <option value="{{$emp->id}}">{{$emp->nik}} -- {{$emp->fullname}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>                                                        
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <table class="table table-bordered border-primary fs-10">
                                        <thead>
                                            <tr>
                                                <th scope="col" style="text-align: center;">HRD & GA General Manager</th>
                                                <th scope="col" style="text-align: center;">HRD PIC Pelatihan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <div class="form-group">
                                                        <select class="form-control select2" id="ttd_hrd_ga" name="ttd_hrd_ga" data-placeholder="Pilih Employee" required>
                                                            {{--<option value="634" selected="true">WAWAN SUPRIYANTO</option>--}}
                                                            @foreach ($employees as $emp)
                                                                <option value="{{$emp->id}}">{{$emp->nik}} -- {{$emp->fullname}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group">
                                                        <select class="form-control select2" id="ttd_pic" name="ttd_pic" data-placeholder="Pilih Employee" required>
                                                            <option selected="true" value="181">SEPTALIA META KARINA</option>
                                                        </select>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>                                            
                    <div class="row" id="form-submit">
                        <div class="col-lg-12">
                            <div class="hstack gap-2 d-print-none mt-4" style="justify-content: flex-end;">
                                <button type="submit" id="btn-submit" class="btn btn-primary">Submit</button>
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
<!-- Select2 -->
<script src="/assets/libs/adminlte/select2/js/4.1.0/select2.min.js"></script>
<script src="/assets/js/pages/select2.init.js"></script>
@endsection
@section('javascript')
<script>
    var id_record = {{ Js::from(encrypt($training_record->id)) }};
    $("#cek_btn-evaluasi").click(function() {              
        $.ajax({
            url: "{{route('training.emp.check.evaluasi.laporan')}}",
            type: "POST",
            data: {
                id_record: id_record,
                _token: '{{ csrf_token() }}'
            },
            dataType: 'json',
            success: function(result){
                if(result == 'ya'){
                    alert("Anda Sudah Mengisi Evaluasi");
                    $("#info-evaluasi").html("");
                    $("#info-evaluasi").html("Anda Sudah Mengisi Evaluasi");
                    $('#cek_evaluasi').val(result);
                    $("#btn-evaluasi").removeClass("btn-soft-danger");  
                    $("#btn-evaluasi").addClass("btn-soft-success");
                    $("#info-evaluasi").removeClass("alert-danger");  
                    $("#info-evaluasi").addClass("alert-success");
                }else{
                    $('#cek_evaluasi').val('');
                    alert("Anda Belum Mengisi Evaluasi");
                    $("#btn-evaluasi").removeClass("btn-soft-danger");  
                    $("#btn-evaluasi").removeClass("btn-soft-success");  
                    $("#btn-evaluasi").addClass("btn-soft-danger");
                }
            }
        });
    });
</script>
<script>
    $("#tgl_laporan").flatpickr({
        allowInput: true,
        altInput: true,
        altFormat: "d F, Y",
        dateFormat: "Y-m-d",
    });

    $(".select2").select2();
    
    $(document).ready(function() {
        $("#btn-submit").click(function() {
            $("#Formlaporan").submit(function () {
                $('#staticBackdrop').modal('show', true);
            });
        });
    });
</script>
<script>
    function uploadSertifikatValidation(){
        var upload = document.getElementById('file_sertifikat');
        var pathUpload= upload.value;

        // tipe file yang diizinkan
        var allowedExtensions = /(\.pdf|\.PDF|\.pptx|\.PPTX|\.jpg|\.JPG|\.jpeg|\.JPEG|\.png|\.PNG|\.xlsx|\.XLSX|\.docx|\.DOCX)$/i;

        if (!allowedExtensions.exec(pathUpload)) {
            document.getElementById(
                'info-validation').innerHTML =
                '<p class="text-muted fs-12">Maaf hanya menerima file document yang bertipe .pdf | .pptx | .jpg | .jpeg | .png | .xlsx | .docx</p>';
            $('#modalTraining').modal('toggle');
            $('#validationmodal').modal('show');
            upload.value = '';
            return false;
        }
        else
        {             
            // dijalankan
        }      
    }

    function clearSertifikatUpload(){
        var upload = document.getElementById('file_sertifikat');
        upload.value = '';
    }
    function clearMateriUpload(){
        var upload_materi = document.getElementById('file_materi');
        upload_materi.value = '';
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
