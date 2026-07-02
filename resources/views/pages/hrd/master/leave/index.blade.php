@extends('layouts.master')
@section('link')
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link href="/assets/libs/Datatables/DataTables-1.13.1/css/dataTables.bootstrap5.css" rel="stylesheet" type="text/css" />
  <link href="/assets/libs/Datatables/Buttons-2.3.3/css/buttons.dataTables.css" rel="stylesheet" type="text/css" />
  <link href="/assets/libs/Datatables/Responsive-2.4.0/css/responsive.bootstrap.css" rel="stylesheet" type="text/css" />
  <!-- Select2-->
  <link rel="stylesheet" href="/assets/libs/adminlte/select2/css/4.1.0/select2.min.css">
  <style>
    .select2-container--default .select2-selection--multiple .select2-selection__choice__display { color: #000 }
  </style>
@endsection

@section('content')

<div class="row">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <h4 class="mb-sm-0">Collective Leave</h4>

      <div class="page-title-right">
          <ol class="breadcrumb m-0">
              <li class="breadcrumb-item"><a href="javascript: void(0);">Collective</a></li>
              <li class="breadcrumb-item active">Leave</li>
          </ol>
      </div>

    </div>
  </div>
</div>

<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-header align-items-center d-flex">
        <button type="button" class="btn btn-primary btn-label waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#modal" data-text="Add New Leave">
          <i class="ri-add-circle-line label-icon align-middle fs-16 me-2"> </i>Add New Leave
        </button>  
        <div class="flex-shrink-0">
        </div>
      </div><!-- end card header -->
      <div class="card-body">
        <table class="table table-striped bordered" id="table_leave">
          <thead>
            <tr>
              <th scope="col" style="text-align:center">No</th>
              <th scope="col">Name</th>
              <th scope="col" style="text-align:center">Action</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <!--end col-->
</div>
<!--end row-->

<!--Modal add/edit-->
<div class="modal fade" id="modal" tabindex="-1" aria-labelledby="exampleModalgridLabel" aria-modal="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
          <h5 class="modal-title" id="exampleModalgridLabel">Add/Edit Leave</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form class="form" action="{{ route('leave.store') }}" method="post">
          @csrf
          @method('POST')
          <div class="row">
            <input type="hidden" id="id" name="id" value="">
            <div class="col-lg-12">
              <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Masukkan Nama Leave" value="" required>
              </div>
            </div><!--end col-->
            <div class="col-lg-12">
                <div class="hstack gap-2 justify-content-end">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div><!--end col-->
          </div><!--end row-->
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
<!-- Select2 -->
<script src="/assets/libs/adminlte/select2/js/4.1.0/select2.min.js"></script>
<!-- Sweetalert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection

@section('javascript')
  <script type="text/javascript">
    $(document).ready(function() {
      let swalert;
      let table = $('#table_leave').DataTable({
        stateSave: true,
        responsive: true,
        autoWidth: true,
        processing: true,
        serverSide: true,
        ajax: "{{ route('leave.index') }}",
        columns: [{
            data: 'id',
            name: 'id',
            className: "text-center"
          }, {
            data: 'nama',
            name: 'nama',
          }, {
            data: 'action',
            name: 'action',
            className: "text-center",
            orderable: false,
            searchable: false
          },
        ]
      });

      $("#modal").on("hidden.bs.modal", function() {
        $("#name").val(null);
      });

      $(document).on("click", ".edit-btn", function() {
        var leaveId = $(this).data("id");
        $.ajax({
          url: "{{ route('leave.edit') }}",
          method: "GET",
          data: {
            id: leaveId
          },
          success: function(response) {
            $("input[name='id']").val(response.id);
            $("input[name='name']").val(response.nama);

            $("#modal").modal("show");
          },
          error: function(xhr, status, error) {
            console.log(xhr, status, error);
          }
        });
      });

      $("form").submit(function(e) {
        e.preventDefault();

        swalert = Swal.fire({
          title: 'Loading!',
          didOpen: () => {
            Swal.showLoading()
          }
        });

        const formData = new FormData(this);

        $.ajax({
          url: $(this).attr("action"),
          method: "POST",
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          data: formData,
          processData: false,
          contentType: false,
          success: function(response) {
            swalert.hideLoading();
            swalert.update({
              title: "Success",
              text: response.message,
              icon: "success",
              buttonsStyling: false,
              confirmButtonText: "Ok, got it!",
              customClass: {
                popup: 'swal2-noanimation',
                confirmButton: "btn btn-primary"
              }
            });
            swalert.then(() => window.location.reload() = response.redirect)
          },
          error: function(xhr, status, error) {
            console.log({
              xhr,
              status,
              error
            });
            handleErrorResponse(xhr.responseJSON);
          }
        });
      });

      function handleErrorResponse(responseJson) {
        let errorMessage = '';

        if (responseJson.message) {
          errorMessage += `<h4 class="text-danger">${responseJson.message}</h4>`;
        }

        if (responseJson.errors) {
          for (const fieldName in responseJson.errors) {
            errorMessage += `<p class="text-danger">${responseJson.errors[fieldName][0]}</p>`;
          }
        }

        if (responseJson.responseText) {
          errorMessage += `<p class="text-danger">${responseJson.responseText}</p>`;

        }

        if (errorMessage === '') {
          errorMessage += '<p class="text-danger">An error occurred.</p>';
        }

        // Display error message using SweetAlert
        swalert.update({
          title: 'Error',
          html: errorMessage,
          icon: 'error',
          buttonsStyling: false,
          confirmButtonText: 'Ok',
          customClass: {
            confirmButton: 'btn btn-primary'
          }
        });
      }
    });
  </script>
@endsection
