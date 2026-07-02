@extends('layouts.general')
@section('link')
<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- Datatables-->
<link href="/assets/libs/Datatables/DataTables-1.13.1/css/dataTables.bootstrap5.css" rel="stylesheet" type="text/css" />
<link href="/assets/libs/Datatables/Buttons-2.3.3/css/buttons.dataTables.css" rel="stylesheet" type="text/css" />
<link href="/assets/libs/Datatables/Responsive-2.4.0/css/responsive.bootstrap.css" rel="stylesheet" type="text/css" />
<style type="text/css">
    div.dataTables_wrapper {
    width: 100%;
    /* margin: 0 auto; */
    }
</style>
@endsection
@section('content')
<!-- start page -->
<div class="row">
    <div class="col-lg-12">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Internal Rule</h5>
                        <table class="table table-striped bordered" id="table_rule">
                            <thead>
                                <tr>
                                <th scope="col" style="text-align:center">NO</th>
                                <th scope="col" style="text-align:center">RULE</th>
                                <th scope="col" style="text-align:center">BERLAKU MULAI</th>
                                <th scope="col" style="text-align:center">ISI</th>
                                <th scope="col" style="text-align:center">VIEW</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div><!-- end card body -->
                </div><!-- end card -->
            </div>
            <!--end col-->
        </div>
        <!--end row-->
    </div>
    <!--end col-->
</div>
<!--end row-->
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
        responsive: false,
        autoWidth: false,
        stateSave: true,
        processing: true,
        serverSide: true,
        scrollX: true,
        ajax: "{{ route('internal-rule.emp.index') }}",
            "columnDefs": [
                { "width": "5%", "targets": 0 },
                { "width": "20%", "targets": 1 },
                { "width": "15%", "targets": 2 },
                { "width": "30%", "targets": 3 },
                { "width": "20%", "targets": 4 }
            ],
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex' , "className": "text-center"},
                {data: 'nama', name: 'nama' , "className": "text-center"},
                {data: 'tgl_berlaku', name: 'tgl_berlaku' , "className": "text-center"},
                {data: 'isi', name: 'isi' , "className": "text-left"},
                {data: 'action', name: 'action', "className": "text-center", orderable: false, searchable: false},
            ]
        });

        $("#modal-preview").on("hidden.bs.modal", function() {
          $("#show-preview").html('');
        });

        $('#table_rule').on("click", ".preview-btn", function() {
            var preview = $(this).data("id");
            if(preview == 0){
                $("#show-preview").html('<center><div class="text-center"><lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop" colors="primary:#405189,secondary:#0ab39c" style="width:150px;height:150px"></lord-icon><h5 class="text-center mt-2">data not available...</h5></div></center>');
            }else{
                $("#show-preview").html('<iframe src="'+preview+'" frameborder="0" style="height:500px; width:100%;"></iframe>');
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
@endsection
