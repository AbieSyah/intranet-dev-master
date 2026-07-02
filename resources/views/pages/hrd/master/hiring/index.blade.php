@extends('layouts.master')
@section('link')
<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- Datatables-->
<link href="/assets/libs/Datatables/DataTables-1.13.1/css/dataTables.bootstrap5.css" rel="stylesheet" type="text/css" />
<link href="/assets/libs/Datatables/Buttons-2.3.3/css/buttons.dataTables.css" rel="stylesheet" type="text/css" />
<link href="/assets/libs/Datatables/Responsive-2.4.0/css/responsive.bootstrap.css" rel="stylesheet" type="text/css" />
<!-- Toastr Notifications-->
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endsection
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Hiring</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Master</a></li>
                    <li class="breadcrumb-item active">Hiring</li>
                </ol>
            </div>

        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                @can('hrd.master.hiring.create')
                <button type="button" id="add-hiring" class="btn btn-primary btn-label waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#modal" data-text="Add New Hiring">
                <i class="ri-add-circle-line label-icon align-middle fs-16 me-2"> </i>Add New Hiring
                </button>
                @endcan  
                <div class="flex-shrink-0">
                </div>
            </div>
            <div class="card-body">
                <table class="table table-striped bordered" id="table_hiring">
                <thead>
                    <tr>
                    <th scope="col" style="text-align:center">No</th>
                    <th scope="col" style="text-align:center">Name</th>
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
<div class="modal fade" id="modal" tabindex="-1" aria-labelledby="exampleModalgridLabel" aria-modal="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
          <h5 class="modal-title" id="exampleModalgridLabel">Hiring</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="form" action="{{ route('hiring.store') }}" method="post">
          @csrf
          @method('post')
          <div class="row g-3">
              <input type="hidden" id="id" name="id" value="">
              <div class="col-lg-12">
                <div>
                  <label for="name" class="form-label">Nama</label>
                  <input type="text" class="form-control" name="name" id="name" placeholder="Masukkan Nama Hiring" value="" required>
                </div>
              </div><!--end col-->
              <div class="col-lg-12">
                  <div class="hstack gap-2 justify-content-end">
                      <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                      <button type="submit" id="btn-save" class="btn btn-primary">Save</button>
                  </div>
              </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!--Modal staticbackdrop-->
<div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <img src="{{ url('') }}/assets/images/loading.gif" style="width:120px;height:120px">                    
                <div class="mt-4">
                    <h4 class="mb-3">Please wait...</h4>
                    <h4 class="mb-3">Do not leave this page</h4>
                </div>
            </div>
        </div>
    </div>
</div>
<!--Modal delete-->
<div id="modal-delete" class="modal fade flip" tabindex="-1" aria-labelledby="flipModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-top">
        <div class="modal-content">
            <form id="delete-form" class="form" action="{{ route('hiring.destroy') }}" method="post">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title">Delete Hiring</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-5">
                    <lord-icon
                        src="https://cdn.lordicon.com/gsqxdxog.json"
                        trigger="loop"
                        style="width:120px;height:120px">
                    </lord-icon>
                    <p class="text-muted">Apakah anda yakin?</p>
                    <input type="hidden" name="id" value="">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger">Ya</button>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tidak</button>
                </div>
            </form>
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
<!-- Toastr Notifications-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
@endsection

@section('javascript')
<script>
    $( "#btn-save" ).click(function() {
        $("#form").submit(function () {
            $('#staticBackdrop').modal('show', true);
        });
    });
</script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#table_hiring').DataTable({
        "responsive": true,
        "autoWidth": false,
        stateSave: true,
        processing: true,
        ajax: "{{ route('hiring.index') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex' , "className": "text-center"},
                {data: 'name', name: 'name' , "className": "text-center"},
                {data: 'action', name: 'action', "className": "text-center", orderable: false, searchable: false},
            ]
        });
        $('#add-hiring').on("click", function() {
            $("input[name='id']").val('');
            $("input[name='name']").val('');
        });
        $('#table_hiring').on("click", ".edit-btn", function() {
            var hiringId = $(this).data("id");
            $.ajax({
            url: "{{ route('hiring.edit') }}",
            method: "GET",
            data: {
                id: hiringId
            },
            success: function(result) {
                $("input[name='id']").val(result.id);
                $("input[name='name']").val(result.name);
                $("#modal").modal("show");
            },
            error: function(xhr, status, error) {
                console.log(xhr, status, error);
            }
            });
        });
        $('#table_hiring').on("click", ".delete-btn", function() {
            var hiringId = $(this).data("id");
            $("#modal-delete").find("input[name='id']").val(hiringId);
            $("#modal-delete").modal("show");
        });
        $("#delete-form").on('submit', function() {
            $('#modal-delete').modal('hide');
            $('#staticBackdrop').modal('show');
        });
    });        
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