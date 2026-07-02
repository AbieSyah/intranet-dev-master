@extends('layouts.master')
@section('link')
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link href="/assets/libs/Datatables/DataTables-1.13.1/css/dataTables.bootstrap5.css" rel="stylesheet" type="text/css" />
  <link href="/assets/libs/Datatables/Buttons-2.3.3/css/buttons.dataTables.css" rel="stylesheet" type="text/css" />
  <link href="/assets/libs/Datatables/Responsive-2.4.0/css/responsive.bootstrap.css" rel="stylesheet" type="text/css" />
  <!-- Select2-->
  <link rel="stylesheet" href="/assets/libs/adminlte/select2/css/4.1.0/select2.min.css">
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">List User</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">User</a></li>
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
              <button type="button" id="add_user" class="btn btn-primary btn-label waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#modal" data-text="Add New User">
                <i class="ri-add-circle-line label-icon align-middle fs-16 me-2"> </i>Add New User
              </button>
              &nbsp;  
              <a href="{{ route('user.form') }}" class="float-end btn btn-primary btn-label waves-effect waves-light" data-text="Create Multiple Account"><i class="ri-add-circle-line label-icon align-middle fs-16 me-2"> </i>Create Multiple Account</a>  
              <div class="flex-shrink-0">
              </div>
            </div><!-- end card header -->
            <div class="card-body">
              <table class="table table-striped bordered" id="table_user">
                <thead>
                  <tr>
                    <th scope="col" style="text-align:center">ID</th>
                    <th scope="col" style="text-align:center">Nama</th>
                    <th scope="col" style="text-align:center">Roles</th>
                    <th scope="col" style="text-align:center">Area</th>
                    <th scope="col" style="text-align:center">Department</th>
                    <th scope="col" style="text-align:center">Email</th>
                    <th scope="col" style="text-align:center">Status</th>
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
                <h5 class="modal-title" id="exampleModalgridLabel">Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <form class="form" action="{{ route('user.store') }}" method="post">
                @csrf
                @method('POST')
                <div class="row g-3">
                    <input type="hidden" id="id" name="id" value="">
                    <div class="col-xxl-6">
                      <div>
                        <label for="employee" class="form-label">Employee</label>
                        <select class="form-control select2 @error('employee_id') is-invalid @enderror" data-dropdown-parent="#modal" name="employee_id" id="employee_id" data-placeholder="Pilih Nama Anda" required>
                            <option selected="true" disabled="true"></option>
                            @foreach ($employees as $employee)
                              <option value="{{ $employee->id }}" 
                                      data-fullname="{{ $employee->fullname }}" 
                                      data-email="{{ $employee->email }}">
                                  {{ $employee->fullname }} -- {{ $employee->department?->name ?? '-' }}
                              </option>
                            @endforeach
                        </select>
                      </div>
                    </div><!--end col-->
                    <div class="col-xxl-6">
                      <div>
                        <label for="name" class="form-label">Username</label>
                        <input type="text" class="form-control" name="name" placeholder="Masukkan username" value="" required>
                      </div>
                    </div><!--end col-->
                    <div class="col-xxl-6">
                      <div>
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" placeholder="example@hisamitsu.co.id" value="" required>
                      </div>
                    </div><!--end col-->
                    <div class="col-xxl-6" id="cek_password">
                        <div>
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" placeholder="Masukkan password">
                        </div>
                    </div><!--end col-->
                    <div class="col-xxl-6">
                        <div>
                          <label for="role" class="form-label">Roles</label>
                          <select class="form-control select2 @error('role_id') is-invalid @enderror" data-dropdown-parent="#modal" name="role_id" id="role_id" data-placeholder="Pilih Role" required>
                              <option selected="true" disabled="true"></option>
                              @foreach ($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                              @endforeach
                          </select>
                        </div>
                    </div><!--end col-->
                    <div class="col-xxl-6">
                      <label for="status" class="form-label">Status</label>
                        <div>
                          <div class="form-check form-check-inline">
                            <!-- <input class="form-check-input" type="checkbox" value="1" id="cek_status" name="cek_status"> -->
                            <input class="form-check-input" type="radio" name="cek_status" id="cek_status_1" value="1" required>
                            <label class="form-check-label" for="status">
                              Active
                            </label>
                          </div>
                          <div class="form-check form-check-inline">
                            <!-- <input class="form-check-input" type="checkbox" value="1" id="cek_status" name="cek_status"> -->
                            <input class="form-check-input" type="radio" name="cek_status" id="cek_status_2" value="0" required>
                            <label class="form-check-label" for="status">
                              Inactive
                            </label>
                          </div>
                        </div>
                    </div><!--end col-->
                    <div class="col-xxl-6">
                      <label for="send" class="form-label">Send Email Reset Password</label>
                        <div>
                          <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="cek_email" id="cek_email_1" value="yes" required>
                            <label class="form-check-label" for="send">
                              Yes
                            </label>
                          </div>
                          <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="cek_email" id="cek_email_2" value="no" required>
                            <label class="form-check-label" for="send">
                              No
                            </label>
                          </div>
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
  <!-- Sweetalert -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- Select2 -->
  <script src="/assets/libs/adminlte/select2/js/4.1.0/select2.min.js"></script>
@endsection
@section('javascript')
  <script type="text/javascript">
      $(function () {
          $('.select2').select2()
          
      });
  </script>
  <script type="text/javascript">
    $(document).ready(function() {
      let swalert;
      let table = $('#table_user').DataTable({
        stateSave: true,
        responsive: true,
        autoWidth: false,
        processing: true,
        serverSide: true,
        ajax: "{{ route('user.index') }}",
        columns: [{
          data: 'id',
          name: 'id',
          "className": "text-center"
        }, {
          data: 'name',
          name: 'name',
        }, {
          data: 'role',
          name: 'role',
          "className": "text-center"
        }, {
          data: 'area',
          name: 'area',
          "className": "text-center"
        }, {
          data: 'department',
          name: 'department',
          "className": "text-center"
        }, {
          data: 'email',
          name: 'email',
        }, {
          data: 'status',
          name: 'status',
          "className": "text-center"
        }, {
          data: 'action',
          name: 'action',
          "className": "text-center",
          orderable: false,
          searchable: true
        }, ]
      });

      //hide form input password
      $('#cek_password').hide();

      $("#modal").on("hidden.bs.modal", function() {
        $("#resultContent").html("");
        $('#employee_id').val(null).trigger('change');
        $('#role_id').val(null).trigger('change');
        $('#id').val(null);
        $("input[name='password']").val(null);

        const submitButton = $(this).find("button[type='submit']");

        submitButton.prop("disabled", false);
        submitButton.find(".spinner-border").addClass("d-none");
        submitButton.find(".indicator-label").removeClass("d-none");
      });
      
      $('#add_user').on("click", function() {
        $('#cek_password').hide();
        $("input[name='name']").val(null);
        $("input[name='email']").val(null);
        $("input[name='cek_status']").prop('checked', false);
      });

      $(document).on("click", ".edit-btn", function() {
        var userId = $(this).data("id");
        $.ajax({
          url: "{{ route('user.edit') }}",
          method: "GET",
          data: {
            id: userId
          },
          success: function(response) {
            //show form input password
            $('#cek_password').show();
            $("input[name='id']").val(response.id);
            $("input[name='name']").val(response.name);
            $("input[name='email']").val(response.email);

            $('#employee_id').val(response.employee_id).trigger('change');

            if (response.roles.length >= 1) {
              $('#role_id').val(response.roles[0].id).trigger('change');
            }

            $("#modal").modal("show");

            if (response.status == 1) {
              radiobtn = document.getElementById("cek_status_1");
              radiobtn.checked = true;
              // $('#cek_status_1').val(1);
            } else {
              radiobtn = document.getElementById("cek_status_2");
              radiobtn.checked = true;
              // $("input[name='cek_status']").val(0);
            }
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

        // submitButton.prop("disabled", true);

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
            // const message = response.message;

            // $("#resultContent").html(
            //   `<h4 class="text-success">${message}</h4>`).removeClass(
            //   "d-none");

            // table.ajax.reload(null, false);
            // setTimeout(function() {
            //   $("#modal").modal("hide");
            // }, 1500);

            swalert.then(() => window.location.reload() = response.redirect)
          },
          error: function(xhr, status, error) {
            $("#loadingSpinner").hide();
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

      $('#employee_id').on('change', function() {
          let userId = $('#id').val();
          if (!userId) {
              let selectedOption = $(this).find(':selected');
              let fullname = selectedOption.data('fullname');
              let email = selectedOption.data('email');
              if (fullname !== undefined) {
                  $("input[name='name']").val(fullname);
              }
              if (email !== undefined) {
                  $("input[name='email']").val(email);
              }
          }
      });
    });
  </script>
@endsection
