@extends('layouts.master')
@section('link')
<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- Toastr Notifications-->
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Upload</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Medical Checkup</a></li>
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Reguler</a></li>
                    <li class="breadcrumb-item active">Upload</li>
                </ol>
            </div>

        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <form id="Form" action="{{ route('medicals.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-header align-items-center d-flex justify-content-between"> 
                <div class="col-md-7">
                        <!-- <input class="form-control" type="file" name="file"> -->
                        <input type="hidden" name="id_temp" value="{{$kode}}">
                    <div class="input-group">
                        <input onchange="uploadValidation(this);" type="file" class="form-control form-control text-sm col-sm-6" name="file" id="file" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" required>
                        <button type="button" class="btn btn-soft-danger waves-effect waves-light" onclick="clearUpload()">Remove</button>
                        <button type="button" id="preview" class="btn btn-soft-warning waves-effect waves-light">Preview</button>
                    </div>
                    <span class="form-text">hanya menerima file bertipe .xlsx | .xls | .xlsm dan pastikan ukuran file tidak lebih dari 5MB.</span>
                </div>
                <div class="col-md-5">
                <a href="{{ route('reguler.detail', $id) }}" class="btn btn-primary btn-label waves-effect waves-light float-end"><i class="ri-arrow-left-fill label-icon align-middle fs-16 me-2"></i> Back</a>
                </div>
                    
                
            </div><!-- end card header -->
            <div class="card-body">  
                <div id="cek_preview" hidden>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">NO</th>
                                    <th scope="col">NO LAB</th>
                                    <th scope="col">NAMA</th>
                                    <th scope="col">L/P</th>
                                    <th scope="col">UMUR</th>
                                    <th scope="col">DEPARTMENT</th>
                                    <th scope="col">LAB</th>
                                    <th scope="col">FOTO THORAX</th>
                                    <th scope="col">AUDIOMETRI</th>
                                    <th scope="col">FISIK DOKTER</th>
                                    <th scope="col">KESIMPULAN</th>
                                    <th scope="col">SARAN</th>
                                    <th scope="col">SKOR FRAMIGHAM</th>
                                    <th scope="col">KRITERIA SEHAT</th>
                                </tr>
                            </thead>
                            <tbody id="table_preview">
                            </tbody>
                        </table>
                        <!-- end table -->
                    </div>
                    <!-- end table responsive -->                    
                </div>
                <button type="submit" id="btn-save" class="float-end btn btn-primary btn-label waves-effect waves-light mb-4"><i class="ri-upload-2-line label-icon align-middle fs-16 me-2"></i> Upload</button>
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
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
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
<!-- Toastr Notifications-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
@endsection
@section('javascript')
<script>
    $( "#btn-save" ).click(function() {
        $("#Form").submit(function () {
            $('#staticBackdrop').modal('show', true);
        });
    });
</script>
<script>            
    $("#preview").click(function () {        
        var cek_file = document.getElementById('file');
        var cekUpload= cek_file.value;
        if(cekUpload == ''){
            // alert('No file chosen...');
            document.getElementById(
                'info-validation').innerHTML =
                '<p class="text-muted fs-12">Maaf file belum di upload</p>';
            $('#validationmodal').modal('show');
        }else{
            $('#cek_preview').prop('hidden',false);
            var formData = new FormData();
            var file = $('#file').prop('files')[0];
    
            formData.append('file', file);
            // formData.append('other_variable', $('#other_variable').val());
            var no = 1;
            $.ajax({
                url: '{{ route("api.reguler.upload")}}',
                method: 'post',
                data: formData,
                contentType : false,
                processData : false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response){
                    $.each(response, function(key, value) {
                        var index = no++;
                        $("#table_preview").append(
                            '<tr>'+
                                '<td>'+index+'</td>'+
                                '<td>'+value['no_lab']+'</td>'+
                                '<td>'+value['nama']+'</td>'+
                                '<td>'+value['jk']+'</td>'+
                                '<td>'+value['umur']+'</td>'+
                                '<td>-</td>'+
                                '<td>'+value['lab']+'</td>'+
                                '<td>'+value['foto_thorax']+'</td>'+
                                '<td>'+value['audiometri']+'</td>'+
                                '<td>'+value['fisik_dokter']+'</td>'+
                                '<td>'+value['kesimpulan']+'</td>'+
                                '<td>'+value['saran']+'</td>'+
                                '<td>'+value['skor_framigham']+'</td>'+
                                '<td>'+value['kriteria_sehat']+'</td>'+
                            '</tr>'
                        );
                    })
                }
            });            
        }
    });

    function clearUpload(){
        $('#cek_preview').prop('hidden',true);
        var upload = document.getElementById('file');
        upload.value = '';
    }

    function uploadValidation(){
        var upload = document.getElementById('file');
        var pathUpload= upload.value;

        // tipe file yang diizinkan
        var allowedExtensions = /(\.xlsx|\.xls|\.xlsm)$/i;

        if (!allowedExtensions.exec(pathUpload)) {
            document.getElementById(
                'info-validation').innerHTML =
                '<p class="text-muted fs-12">Maaf hanya menerima file document yang bertipe .xlsx | .xls | .xlsm</p>';
            $('#validationmodal').modal('show');
            // alert('Invalid file type');
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
    $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
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