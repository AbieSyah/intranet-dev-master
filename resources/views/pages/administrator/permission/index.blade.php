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
      <h4 class="mb-sm-0">List Permission</h4>

      <div class="page-title-right">
          <ol class="breadcrumb m-0">
              <li class="breadcrumb-item"><a href="javascript: void(0);">Permission</a></li>
              <li class="breadcrumb-item active">List</li>
          </ol>
      </div>

    </div>
  </div>
</div>

<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-header align-items-center d-flex">
        <button type="button" class="btn btn-primary btn-label waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#modal" data-text="Add New Permission">
          <i class="ri-add-circle-line label-icon align-middle fs-16 me-2"> </i>Add New Permission
        </button>  
        <div class="flex-shrink-0">
        </div>
      </div><!-- end card header -->
      <div class="card-body">
        <table class="table table-striped bordered" id="table_user">
          <thead>
            <tr>
              <th scope="col" style="text-align:center">ID</th>
              <th scope="col">Name</th>
              <th scope="col">Roles</th>
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
          <h5 class="modal-title" id="exampleModalgridLabel">Add/Edit Permission</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form class="form" action="{{ route('permission.store') }}" method="post">
          @csrf
          @method('POST')
          <div class="row g-3">
            <input type="hidden" id="id" name="id" value="">
            <div class="col-xxl-6">
              <div>
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Masukkan Nama Permission" value="" required>
              </div>
            </div><!--end col-->
            <div class="col-xxl-6">
                <div>
                  <label for="role" class="form-label">Roles</label>
                  <select class="form-control select2 @error('roles') is-invalid @enderror" data-dropdown-parent="#modal" name="roles[]" id="roles" multiple="multiple" data-placeholder="Pilih Role" required>
                      @foreach ($roles as $role)
                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                      @endforeach
                  </select>
                </div>
            </div><!--end col-->
            <div class="col-xxl-6">
              <div id="resultContent" class="px-5 px-lg-10">
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


  <!-- <div class="modal fade" id="modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
      <div class="modal-content">
        <div class="modal-header" id="kt_modal_add_user_header">
          <h2 class="fw-bold">Add Permission</h2>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body px-5 my-7">
          <form class="form" action="{{ route('permission.store') }}" method="post">
            @csrf
            @method('POST')
            <div class="d-flex flex-column scroll-y px-5 px-lg-10" id="kt_modal_add_user_scroll" data-kt-scroll="true"
              data-kt-scroll-activate="true" data-kt-scroll-max-height="auto"
              data-kt-scroll-dependencies="#kt_modal_add_user_header" data-kt-scroll-wrappers="#kt_modal_add_user_scroll"
              data-kt-scroll-offset="300px">
              <input type="hidden" name="id">
              <div class="fv-row mb-7">
                <label class="required fw-semibold fs-6 mb-2">Name</label>
                <input type="text" id="name" name="name" class="form-control form-control-solid mb-3 mb-lg-0"
                  placeholder="Permission Name" value="" />
              </div>
              <div class="fv-row mb-7">
                <label class="fw-semibold fs-6 mb-2">Roles</label>
                <select class="form-control js-example-basic-single" data-placeholder="Assign this permission to several Role" id="roles" multiple="multiple" name="roles[]">
                  @foreach ($roles as $role)
                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                  @endforeach
                </select>
                {{--<select class="form-select form-select-solid" data-control="select2" data-close-on-select="false"
                  data-placeholder="Assign this permission to several Role" data-allow-clear="true"
                  data-dropdown-parent="#modal" id="roles" multiple="multiple" name="roles[]">
                  @foreach ($roles as $role)
                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                  @endforeach
                </select>--}}
              </div>
            </div>
            <div id="resultContent" class="px-5 px-lg-10">
            </div>
            <div class="text-center pt-10">
              <button type="submit" class="btn btn-primary" id="submit-btn" data-kt-users-modal-action="submit">
                <span class="d-none spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                <span class="indicator-label">Submit</span>
              </button>
            </div>
          </form>
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
<script src="/assets/js/pages/select2.init.js"></script>
<!-- Sweetalert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection

@section('javascript')
  <script type="text/javascript">
    $('#roles').select2({dropdownParent: $('#modal .modal-content')});

    $(document).ready(function() {
      let swalert;
      let table = $('#table_user').DataTable({
        stateSave: true,
        responsive: true,
        autoWidth: false,
        processing: true,
        serverSide: true,
        ajax: "{{ route('permission.index') }}",
        columns: [{
            data: 'id',
            name: 'id',
            className: "text-center"
          }, {
            data: 'name',
            name: 'name',
          }, {
            data: 'roles',
            name: 'roles',
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
        $("#resultContent").html("");
        $("#name").val(null);
        $("#submit-btn").prop('disabled', false);
        $('#roles').val(null).trigger('change');
      });

      $(document).on("click", ".edit-btn", function() {
        var permissionId = $(this).data("id");
        $.ajax({
          url: "{{ route('permission.edit') }}",
          method: "GET",
          data: {
            id: permissionId
          },
          success: function(response) {
            $("input[name='id']").val(response.id);
            $("input[name='name']").val(response.name);

            // Clear existing selections in the multi-select dropdown
            $('#roles').val(null).trigger('change.select2');

            // Loop through the response.roles array and select options
            response.roles.forEach(function(role) {
              $('#roles').find('option[value="' + role.id + '"]').prop(
                'selected', true);
            });

            // Refresh the Select2 dropdown to display the selected options
            $('#roles').trigger('change.select2');

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
        // const submitButton = $(this).find("button[type='submit']");

        // submitButton.prop("disabled", false);

        // submitButton.find(".spinner-border").removeClass("d-none");
        // submitButton.find(".indicator-label").addClass("d-none");

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
            // const message = response.message;

            // $("#resultContent").html(
            //   `<h4 class="text-success">${message}</h4>`).removeClass(
            //   "d-none");

            // // Close the modal after 2 seconds
            // setTimeout(function() {
            //   $("#modal").modal("hide");
            // }, 1500);

            // // Reload your data table (if needed)
            // table.ajax.reload(null, false);
          },
          error: function(xhr, status, error) {
            console.log({
              xhr,
              status,
              error
            });
            handleErrorResponse(xhr.responseJSON);
          }
          // ,
          // complete: function() {
          //   submitButton.find(".spinner-border").addClass("d-none");
          //   submitButton.find(".indicator-label").removeClass("d-none");
          // }
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

        // $("#resultContent").html(errorMessage).removeClass("d-none");
      }
    });
  </script>
@endsection
