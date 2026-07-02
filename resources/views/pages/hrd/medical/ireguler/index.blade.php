@extends('layouts.master')
@section('link')
<meta name="csrf-token" content="{{ csrf_token() }}">
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
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Ireguler</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Medical Check Up</a></li>
                    <li class="breadcrumb-item active">Ireguler</li>
                </ol>
            </div>

        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header align-items-center d-flex">
            @can('hrd.medical-record.ireguler.create')
                <a href="{{ route('ireguler.form') }}" class="btn btn-primary btn-label waves-effect waves-light" data-text="Create New Medical Check Up"><i class=" ri-add-circle-line label-icon align-middle fs-16 me-2"></i>Create New Medical Check Up</a>
            @endcan 
            </div><!-- end card header -->
            <div class="card-body">
                <table class="table table-striped bordered" id="table_medical">
                    <thead>
                        <tr>
                        <th scope="col" style="text-align:center">NO</th>
                        <th scope="col" style="text-align:center">NAMA</th>
                        <th scope="col" style="text-align:center">USIA</th>
                        <th scope="col" style="text-align:center">L/P</th>
                        <th scope="col" style="text-align:center">TANGGAL PERIKSA</th>
                        <th scope="col" style="text-align:center">LAB</th>
                        <th scope="col" style="text-align:center">PAKET</th>
                        <th scope="col" style="text-align:center">STATUS</th>
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
<!--Modal upload-->
<div class="modal fade" id="upload-ireguler" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="Form" action="{{ route('ireguler.update') }}" method="POST" enctype="multipart/form-data">
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
                                    <option value="FIT">FIT</option>                                        
                                    <option value="UNFIT">UNFIT</option>                                        
                                    <option value="FIT DENGAN CATATAN">FIT DENGAN CATATAN</option>                                        
                                </select>
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
                    <button type="submit" id="btn-save" class="btn btn-primary ">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal flip" id="modal-surat" tabindex="-1" aria-labelledby="exampleModalgridLabel" aria-modal="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-light p-3">
                <h5 class="modal-title" id="modal-judul">NOMOR SURAT</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('ireguler.generate.pdf') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-lg-12">
                            <div>
                                <input type="hidden" class="form-control" name="surat_id" id="surat_id">
                                <input type="text" class="form-control" name="no_surat" id="no_surat" placeholder="Masukkan nomor surat" required>
                            </div>                       
                        </div><!--end col-->
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="hstack gap-2 justify-content-end">
                                <button type="submit" formtarget="_blank" class="btn btn-primary"><i class="ri-printer-line align-bottom me-1"></i> Print</button>
                            </div>
                        </div><!--end col-->
                    </div><!--end row-->
                </form>
            </div>
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
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <embed id="show-preview" src="" frameborder="0" width="100%" height="450px">
            </div>
            <div class="modal-footer">
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
<!-- Datatables -->
<script src="/assets/libs/Datatables/DataTables-1.13.1/js/jquery.dataTables.min.js"></script>
<script src="/assets/libs/Datatables/DataTables-1.13.1/js/dataTables.bootstrap5.min.js"></script>
<script src="/assets/libs/Datatables/Responsive-2.4.0/js/dataTables.responsive.min.js"></script>
<script src="/assets/js/pages/datatables.init.js"></script>
<!-- Select2 -->
<script src="/assets/libs/adminlte/select2/js/4.1.0/select2.min.js"></script>
<!-- Toastr Notifications-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
@endsection

@section('javascript')
<script>
    $(function () {
        $('#status').select2({dropdownParent: $('#upload-ireguler .modal-content')});
    });

    function clearUpload(){
        var upload = document.getElementById('file');
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
</script>
<script>
    $( "#btn-save" ).click(function() {
        $("#Form").submit(function () {
            $('#staticBackdrop').modal('show', true);
        });
    });
</script>
<script>
    $(document).ready(function () {
        $('#table_medical').DataTable({
        responsive: true,
        autoWidth: false,
        stateSave: true,
        processing: true,
        serverSide: true,
        ajax: "{{ route('ireguler.index') }}",
            "columnDefs": [
                { "width": "21%", "targets": 8 }
            ],
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex' , "className": "text-center"},
                {data: 'nama', name: 'nama' , "className": "text-center"},
                {data: 'umur', name: 'umur' , "className": "text-center"},
                {data: 'jk', name: 'jk' , "className": "text-center"},
                {data: 'tanggal_mcu', name: 'tanggal_mcu' , "className": "text-center"},
                {data: 'lab', name: 'lab' , "className": "text-center"},
                {data: 'paket', name: 'paket' , "className": "text-center"},
                {data: 'status', name: 'status' , "className": "text-center"},
                {data: 'action', name: 'action', "className": "text-center", orderable: false, searchable: false},
            ]
        });
        $('#table_medical tbody').on('click', 'tr', function () {
            //get id medical
            var id_medical = $(this).closest('tr').find('#medical_id').val();
            $("#id_medical").val(id_medical);
            //preview medical
            var preview = $(this).closest('tr').find('#id_preview').val();
            $("#show-preview").attr("src", preview);
            //reset modal form
            $('#status').val(null).trigger('change');
            $('#no_lab').val('');
            $('#file').val('');
            //surat mcu
            var id_surat = $(this).closest('tr').find('#id_surat').val();
            $("#surat_id").val(id_surat);
            $("#no_surat").val('');
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