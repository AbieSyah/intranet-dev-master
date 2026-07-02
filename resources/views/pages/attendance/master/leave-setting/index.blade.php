@extends('layouts.master')
@section('link')
<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- Datatables-->
<link href="/assets/libs/Datatables/DataTables-1.13.1/css/dataTables.bootstrap5.css" rel="stylesheet" type="text/css" />
<link href="/assets/libs/Datatables/Buttons-2.3.3/css/buttons.dataTables.css" rel="stylesheet" type="text/css" />
<link href="/assets/libs/Datatables/Responsive-2.4.0/css/responsive.bootstrap.css" rel="stylesheet" type="text/css" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Leave Setting</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Master</a></li>
                    <li class="breadcrumb-item active">Leave Setting</li>
                </ol>
            </div>

        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <button type="button" id="add-leave-setting" class="btn btn-primary btn-label waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#modal" data-text="Add New Work Hour">
                <i class="ri-add-circle-line label-icon align-middle fs-16 me-2"> </i>Add New Leave Time
                </button>
                <div class="flex-shrink-0">
                </div>
            </div>
            <div class="card-body">
                <table class="table table-striped bordered" id="table_leave-setting">
                <thead>
                    <tr>
                    <th scope="col" style="text-align:center">No</th>
                    <th scope="col" style="text-align:center">Leave Type</th>
                    <th scope="col" style="text-align:center">Description</th>
                    <th scope="col" style="text-align:center">Year Span</th>
                    <th scope="col" style="text-align:center">Number of Days</th>
                    <th scope="col" style="text-align:center">Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!--Modal add/edit-->
<div class="modal fade" id="modal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content shadow border-0 rounded-3">

            <!-- HEADER -->
            <div class="modal-header border-0 pb-2">
                <h5 class="modal-title fw-semibold text-dark">
                    Pengaturan Cuti
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- BODY -->
            <div class="modal-body pt-0">
                <form id="form" action="{{ route('leave-setting.store') }}" method="post">
                    @csrf
                    <input type="hidden" name="id" id="id">

                    <div class="mb-3">
                        <label class="form-label small">Tipe Cuti</label>
                        <select id="type" name="type" class="form-select">
                            <option value="pribadi" selected>Pribadi</option>
                            <option value="normatif">Normatif</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <div class="mb-2">
                            <label class="form-label small">Keterangan</label>
                        <textarea
                            class="form-control" name="description" rows="2" placeholder="description" required>
                        </textarea>
                        </div>
                        <small class="text-muted">* description wajib diisi</small>
                    </div>

                    <div class="row">
                        <div class="mb-2 col-md-3">
                            <label class="form-label small">Tahun Minimal</label>
                            <input type="number" class="form-control" name="min_years" min="0" placeholder="e.g. 1" >
                        </div>
                        <div class="mb-2 col-md-3">
                            <label class="form-label small">Tahun Maksimal</label>
                            <input type="number" class="form-control" name="max_years" min="1" placeholder="e.g. 5" >
                        </div>
                        <div class="mb-2 col-md-6">
                            <label class="form-label small">Jumlah Hari</label>
                            <input type="number" class="form-control" name="number_of_days" min="0" placeholder="e.g. 10" required>
                        </div>
                        <small class="text-muted">* Tahun Minimal dan Maksimal wajib Diisi Untuk Cuti Pribadi</small>
                    </div>

                    <!-- FOOTER -->
                    <div class="d-flex justify-content-end gap-2 mt-4 pt-2 ">
                        <button type="button" class="btn btn-light px-3" data-bs-dismiss="modal">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-primary px-4">
                            Save
                        </button>
                    </div>

                </form>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endsection

@section('javascript')
<script>
$(document).ready(function () {
    // CSRF SETUP
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    // DATATABLE
    const table = $('#table_leave-setting').DataTable({
        responsive: true,
        autoWidth: false,
        stateSave: true,
        processing: true,
        ajax: "{{ route('leave-setting.index') }}",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', className: "text-center"},
            {data: 'type', name: 'type', className: "text-center"},
            {data: 'description', name: 'description', className: "text-center"},
            {data: 'year_span', name: 'year_span', className: "text-center"},
            {data: 'number_of_days', name: 'number_of_days', className: "text-center"},
            {data: 'action', name: 'action', className: "text-center", orderable: false, searchable: false},
        ]
    });

    function validateForm() {
        let type = $('#type').val();
        let description = $('[name="description"]').val();
        let min_year = $('[name="min_years"]').val();
        let max_year = $('[name="max_years"]').val();
        let days = $('[name="number_of_days"]').val();
        if (!days) {
            Swal.fire('Warning', 'Jumlah Hari wajib diisi', 'warning');
            return false;
        }
        if (type === 'pribadi') {
            if (!min_year || !max_year) {
                Swal.fire('Warning', 'Minimal dan Maksimum Tahun Wajib Diisi', 'warning');
                return false;
            }
        }
        return true;
    }
    // FORM SUBMIT (LOADING MODAL)
    $("#form").on("submit", function (e) {
        e.preventDefault();
        // if (!validateForm()) return;
        let form = this;
        let formData = new FormData(form);
        Swal.fire({
            title: 'Saving...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });
        $.ajax({
            url: $(form).attr("action"),
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function () {
                Swal.fire('Success', 'Data berhasil disimpan', 'success');
                $("#modal").modal("hide");
                $('#form')[0].reset();
                table.ajax.reload();
            },
            error: function (xhr) {
                Swal.fire('Error', xhr.responseJSON?.message ?? 'Terjadi kesalahan', 'error');
            }
        });
    });
    // ADD LEAVE SETTING
    $('#add-leave-setting').click(function(){
        $('#form').attr('action',"{{ route('leave-setting.store') }}");

        $('#form')[0].reset();
        $('#id').val('');

        $("#modal").modal("show");
    });

    $('#table_leave-setting').on("click",".edit-btn",function(){
        let id = $(this).data("id");
        $.ajax({
            url: "{{ route('leave-setting.edit') }}",
            method: "GET",
            data: {id: id},
            success:function(res){
                $('#form').attr('action',"{{ route('leave-setting.update') }}");
                $('#id').val(res.id);
                $('input[name="type"]').val(res.type);
                $('textarea[name="description"]').val(res.description);
                $('input[name="min_years"]').val(res.min_years);
                $('input[name="max_years"]').val(res.max_years);
                $('input[name="number_of_days"]').val(res.number_of_days);
                $("#modal").modal("show");
            }
        });
    });

    // DELETE WORKHOUR
    $('#table_leave-setting').on("click",".delete-btn",function(){
        let id = $(this).data("id");
        Swal.fire({
            title: 'Yakin hapus?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus'
        }).then((result)=>{
            if(result.isConfirmed){
                $.ajax({
                    url: "{{ route('leave-setting.destroy') }}",
                    method: "DELETE",
                    data: {id:id},
                    success:function(){
                        Swal.fire('Deleted', 'Data berhasil dihapus', 'success');
                        table.ajax.reload();
                    }
                });
            }
        });
    });
});
</script>
@endsection
