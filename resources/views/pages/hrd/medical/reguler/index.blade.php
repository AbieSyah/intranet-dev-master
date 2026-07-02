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
            <h4 class="mb-sm-0">Reguler</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Medical Check Up</a></li>
                    <li class="breadcrumb-item active">Reguler</li>
                </ol>
            </div>

        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                @can('hrd.medical-record.reguler.create')
                    <a href="{{ route('reguler.form') }}" class="btn btn-primary btn-label waves-effect waves-light" data-text="Create New Medical Check Up"><i class=" ri-add-circle-line label-icon align-middle fs-16 me-2"></i>Create New Medical Check Up</a>
                @endcan
                {{-- <a href="{{ route('reguler.upload') }}" class="btn btn-primary btn-label waves-effect waves-light" data-text="Upload Medical Record">
                    <i class="ri-file-upload-line label-icon align-middle fs-16 me-2"></i>Upload Medical Record
                </a> --}}
            </div><!-- end card header -->
            <div class="card-body">
                <table class="table table-striped bordered" id="table_medical">
                    <thead>
                        <tr>
                        <th scope="col" style="text-align:center">NO</th>
                        <th scope="col" style="text-align:center">TAHUN</th>
                        <th scope="col" style="text-align:center">LABORATORIUM</th>
                        <th scope="col" style="text-align:center">TOTAL KARYAWAN</th>
                        <th scope="col" style="text-align:center">TANGGAL</th>
                        <th scope="col" style="text-align:center">PROGRESS</th>
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
<!--modal edit template-->
<div class="modal flip" id="modal-edit" tabindex="-1" aria-labelledby="exampleModalgridLabel" aria-modal="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-judul">Edit Template MCU</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('reguler.edit') }}" method="post">
            @csrf
            <div class="modal-body">
                <div class="row">
                    <input type="hidden" id="id" name="id" value=""/>
                    <div class="col-lg-12">                            
                        <div class="mb-3">
                            <label>Tanggal Periksa</label>
                            <div class="input-group">
                                <input type="text" name="date_range" id="date_range"
                                    class="form-control @error('date_range') is-invalid @enderror"
                                    placeholder="Pilih Tanggal" value="" required>
                                <span class="input-group-text"><i class="ri-calendar-event-line"></i></span>
                            </div>
                        </div>
                    </div>                    
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
            </form>
        </div>
    </div>
</div>
<!--Modal staticbackdrop-->
<!-- <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
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
</div> -->
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
    $('#date_range').flatpickr({
        mode: "range",
        allowInput: true,
        altInput: false,
        altFormat: "d F, Y",
        dateFormat: "Y-m-d",
    });   
</script>
<script>
    $( "#btn-save" ).click(function() {
        $("#form").submit(function () {
            $('#staticBackdrop').modal('show', true);
        });
    });

    $(function () {
        $('#vendor').select2({dropdownParent: $('#flipModalCreateNewMedical .modal-content')});
        $('#employee').select2({dropdownParent: $('#flipModalCreateNewMedical .modal-content')});
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
        ajax: "{{ route('reguler.index') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex' , "className": "text-center"},
                {data: 'tahun', name: 'tahun' , "className": "text-center"},
                {data: 'vendor', name: 'vendor' , "className": "text-center"},
                {data: 'total', name: 'total' , "className": "text-center"},
                {data: 'date_range', name: 'date_range' , "className": "text-center"},
                {data: 'progress', name: 'progress' , "className": "text-center"},
                {data: 'action', name: 'action', "className": "text-center", orderable: false, searchable: false},
            ]
        });
        $('#table_medical tbody').on('click', 'tr', function () {
            //get id template medical
            var data_id = $(this).closest('tr').find('#edit').data('id');
            $("#id").val(data_id);
            //get tgl periksa
            var tgl_periksa = $(this).closest('tr').find('#tgl_periksa').val();
            $("#date_range").val(tgl_periksa);
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