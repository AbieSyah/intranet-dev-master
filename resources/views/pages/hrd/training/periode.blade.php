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
<link rel="stylesheet" href="https://cdn.datatables.net/select/1.2.7/css/select.dataTables.min.css">
<!-- Select2-->
<link rel="stylesheet" href="/assets/libs/adminlte/select2/css/4.1.0/select2.min.css">
<!-- Toastr Notifications-->
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">PERIODE TRAINING</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Periode</a></li>
                    <li class="breadcrumb-item active">Training</li>
                </ol>
            </div>

        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="px-3 mt-4 mb-2 align-items-center d-flex">
                <button type="button" class="btn btn-primary btn-label waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#addmodal" data-text="Add New Periode Training">
                    <i class="ri-add-circle-line label-icon align-middle fs-16 me-2"> </i>Add New Periode Training
                </button>
            </div><!-- end card header -->
            <div class="card-body">            
                <table class="table table-striped bordered" id="table_training">
                    <thead>
                        <tr>
                        <th scope="col" style="text-align:center">NO</th>
                        <th scope="col" style="text-align:center">PERIODE</th>
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
<!-- Second modal dialog -->
<div class="modal fade" id="addmodal" aria-hidden="true" aria-labelledby="..." tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="flipModalLabel">Create/Update Periode Training</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">                
                <form id="Form-training" action="{{ route('training.periode.store') }}" method="POST">
                @csrf
                    <div class="row g-3">
                        <input type="hidden" id="id" name="id" value="">
                        <div class="col-lg-12">
                            <div>
                                <label for="tahun" class="form-label">Periode</label>
                                <input type="number" class="form-control" name="tahun" id="tahun" placeholder="Masukkan Tahun" value="" required>
                            </div>
                        </div><!--end col-->    
                        <div class="col-lg-12">                            
                            <label for="status" class="form-label">Status</label>
                            <select class="form-control" id="status" name="status" data-placeholder="Pilih Status" required>
                                <option selected="true" disabled="true"></option>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div><!--end col-->
                        <div class="col-lg-12">
                            <div class="hstack gap-2 justify-content-end mt-4">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                <button type="submit" id="submit-jadwal" class="btn btn-primary">Submit</button>
                            </div>
                        </div><!--end col-->
                    </div><!--end row-->
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div>
</div>
@endsection

@section('script')
<!-- Datatables -->
<script src="/assets/libs/Datatables/DataTables-1.13.1/js/jquery.dataTables.min.js"></script>
<script src="/assets/libs/Datatables/DataTables-1.13.1/js/dataTables.bootstrap5.min.js"></script>
<script src="/assets/libs/Datatables/Responsive-2.4.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/select/1.2.7/js/dataTables.select.min.js"></script>
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
        $('#status').select2({dropdownParent: $('#addmodal .modal-content')});
    });
    $('#addmodal').on('hidden.bs.modal', function () {
        $(this).find('form').trigger('reset');
        $('#status').val('').trigger('change');
    });
    $('#start_date').flatpickr({
        allowInput: true,
        altInput: false,
        altFormat: "d F, Y",
        dateFormat: "Y-m-d",
    }); 

    $('#end_date').flatpickr({
        allowInput: true,
        altInput: false,
        altFormat: "d F, Y",
        dateFormat: "Y-m-d",
    });

    $('#exp_date').flatpickr({
        allowInput: true,
        altInput: false,
        altFormat: "d F, Y",
        dateFormat: "Y-m-d",
    });  
</script>
<script type="text/javascript">
    let table = $('#table_training').DataTable({
        stateSave: true,
        responsive: true,
        autoWidth: false,
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('training.periode') }}"
        },
        columns: [{
            data: 'DT_RowIndex',
            name: 'DT_RowIndex',
            "className": "text-center"
        },
        {
            data: 'periode',
            name: 'periode',
            "className": "text-center"
        },
        {
            data: 'status',
            name: 'status',
            "className": "text-center"
        },
        {
            data: 'action',
            name: 'action',
            "className": "text-center",
            orderable: false,
            searchable: false
        },
        ]
    });

    $('#table_training').on("click", ".edit-btn", function() {
        var id = $(this).data("id");
            $.ajax({
            url: "{{ route('training.periode.edit') }}",
            method: "GET",
            data: {
                id: id
            },
            success: function(result) {                
                //send to add/edit modal
                $("input[name='id']").val(result.id);
                $("input[name='tahun']").val(result.periode);
                $('#status').val(result.status).change();
                $("#addmodal").modal("show");
            },
            error: function(xhr, status, error) {
                console.log(xhr, status, error);
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