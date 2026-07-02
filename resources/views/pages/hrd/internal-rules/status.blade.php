@extends('layouts.master')
@section('link')
<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- Datatables-->
<link href="/assets/libs/Datatables/DataTables-1.13.1/css/dataTables.bootstrap5.css" rel="stylesheet" type="text/css" />
<link href="/assets/libs/Datatables/Buttons-2.3.3/css/buttons.dataTables.css" rel="stylesheet" type="text/css" />
<link href="/assets/libs/Datatables/Responsive-2.4.0/css/responsive.bootstrap.css" rel="stylesheet" type="text/css" />
<style>
    div.dataTables_wrapper {
      width: 100%;
      /* margin: 0 auto; */
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">History Rules</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">History</a></li>
                    <li class="breadcrumb-item active">Rule</li>
                </ol>
            </div>

        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header align-items-center d-flex justify-content-between">
                <h5 class="mb-sm-0">{{$rule->nama}}</h5>
                <a href="{{ route('internal-rule.index') }}" class="btn btn-primary btn-label waves-effect waves-light"><i class="ri-arrow-left-fill label-icon align-middle fs-16 me-2"></i> Back</a>
            </div><!-- end card header -->
            <div class="card-body">            
                <table class="table table-striped bordered" id="table_rule">
                    <thead>
                        <tr>
                        <th scope="col" style="text-align:center">NO</th>
                        <th scope="col" style="text-align:center">BERLAKU</th>
                        <th scope="col" style="text-align:center">KEDALUWARSA</th>
                        <th scope="col" style="text-align:center">ISI</th>
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
<!--modal preview rules-->
<div class="modal flip" id="modal-preview" tabindex="-1" aria-labelledby="exampleModalgridLabel" aria-modal="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-judul"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="show-preview">
                </div>  
            </div>
            <div class="modal-footer">
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
@endsection

@section('javascript')
<script>
    $(document).ready(function () {
        $('#table_rule').DataTable({
        responsive: true,
        autoWidth: false,
        stateSave: true,
        processing: true,
        serverSide: true,
        scrollX: false,
        ajax: "{{ route('internal-rule.status', $id) }}",
            // "columnDefs": [
            //     { "width": "10%", "targets": 5 }
            // ],
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex' , "className": "text-center"},
                {data: 'tgl_berlaku', name: 'tgl_berlaku' , "className": "text-center"},
                {data: 'tgl_kedaluwarsa', name: 'tgl_kedaluwarsa' , "className": "text-center"},
                {data: 'isi', name: 'isi' , "className": "text-left"},
                {data: 'status', name: 'status' , "className": "text-center"},
                {data: 'action', name: 'action', "className": "text-center", orderable: false, searchable: false},
            ]
        });
        
        $('#table_rule').on("click", ".preview-btn", function() {
            var preview = $(this).data("id");
            $("#show-preview").html('<embed src="'+preview+'" frameborder="0" width="100%" height="450px">');
        });
    });
</script>
@endsection