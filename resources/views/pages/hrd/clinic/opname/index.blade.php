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
  <!-- Toastr Notifications-->
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endsection

@section('content')

<div class="row">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <h4 class="mb-sm-0">Medicine Opname</h4>

      <div class="page-title-right">
          <ol class="breadcrumb m-0">
              <li class="breadcrumb-item"><a href="javascript: void(0);">Medicine</a></li>
              <li class="breadcrumb-item active">Opname</li>
          </ol>
      </div>

    </div>
  </div>
</div>

<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-header align-items-center d-flex">
        @can('hrd.clinic.opname.create')
          <a href="{{ route('clinic.opname.create') }}" class="btn btn-primary btn-label waves-effect waves-light" data-text="Add New Transaction">
            <i class="ri-add-circle-line label-icon align-middle fs-16 me-2"> </i>Add New Transaction
          </a>  
        @endcan
        <div class="flex-shrink-0">
        </div>
      </div><!-- end card header -->
      <div class="card-body">
        <table class="table table-striped bordered" id="table_opname">
          <thead>
            <tr>
              <th scope="col" style="text-align:center">No</th>
              <th scope="col" style="text-align:center">Kategori</th>
              <th scope="col" style="text-align:center">Tanggal</th>
              <th scope="col" style="text-align:center">Nama Obat</th>
              <th scope="col" style="text-align:center">Jumlah</th>
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

<!--Modal delete-->
<div id="modal-delete" class="modal fade flip" tabindex="-1" aria-labelledby="flipModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-top">
        <div class="modal-content">
            <form class="form-delete" action="{{ route('clinic.masuk.destroy') }}" method="post">
              @csrf
              @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Delete Transaction</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-5">
                    <lord-icon
                        src="https://cdn.lordicon.com/gsqxdxog.json"
                        trigger="loop"
                        style="width:120px;height:120px">
                    </lord-icon>
                    <p class="text-muted">Apakah anda yakin?</p>
                    <input type="hidden" id="id" name="id" value="">
                </div>
                <div class="modal-footer">
                  <button type="submit" class="btn btn-primary">Ya</button>
                  <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tidak</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
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
<!-- Select2 -->
<script src="/assets/libs/adminlte/select2/js/4.1.0/select2.min.js"></script>
<!-- Sweetalert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection

@section('javascript')
  <script>
    $('#tr_tanggal').flatpickr({
        allowInput: true,
        altInput: false,
        altFormat: "d F, Y",
        dateFormat: "Y-m-d",
    });   

    $('#id_drug').select2({dropdownParent: $('#modal .modal-content')});
  </script>
  <script type="text/javascript">
    $(document).ready(function() {
      let swalert;
      let table = $('#table_opname').DataTable({
        stateSave: true,
        responsive: true,
        autoWidth: true,
        processing: true,
        serverSide: true,
        ajax: "{{ route('clinic.masuk.index') }}",
        columns: [{
            data: 'DT_RowIndex',
            name: 'DT_RowIndex',
            className: "text-center"
          },
          {
            data: 'kategori',
            name: 'kategori',
            className: "text-center"
          },
          {
            data: 'tr_tanggal',
            name: 'tr_tanggal',
            className: "text-center"
          },
          {
            data: 'id_drug',
            name: 'id_drug',
            className: "text-center"
          },
          {
            data: 'jml_drug',
            name: 'jml_drug',
            className: "text-center"
          },
          {
            data: 'action',
            name: 'action',
            className: "text-center",
            orderable: false,
            searchable: false
          },
        ]
      });

      $("#modal").on("hidden.bs.modal", function() {
        $("#tr_tanggal").val(null);
        $('#id_drug').val(null).trigger('change');
        $("#jml_drug").val(null);
      });

      $(document).on("click", ".delete-btn", function() {
        var trdrugId = $(this).data("id");
        $("input[name='id']").val(trdrugId);
        $("#modal-delete").modal("show");
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
  <script>
    $("form-delete").submit(function(e) {
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
