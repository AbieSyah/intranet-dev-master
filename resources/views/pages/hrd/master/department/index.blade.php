@extends('layouts.master')
@section('link')
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link href="/assets/libs/Datatables/DataTables-1.13.1/css/dataTables.bootstrap5.css" rel="stylesheet" type="text/css" />
  <link href="/assets/libs/Datatables/Buttons-2.3.3/css/buttons.dataTables.css" rel="stylesheet" type="text/css" />
  <link href="/assets/libs/Datatables/Responsive-2.4.0/css/responsive.bootstrap.css" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<div class="row">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <h4 class="mb-sm-0">List Department</h4>

      <div class="page-title-right">
          <ol class="breadcrumb m-0">
              <li class="breadcrumb-item"><a href="javascript: void(0);">Department</a></li>
              <li class="breadcrumb-item active">List</li>
          </ol>
      </div>

    </div>
  </div>
</div>
<!--end row-->

<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-header align-items-center d-flex">
        @can('hrd.master.department.update')
        <button type="button" class="btn btn-primary btn-label waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#modal" data-text="Add New Department">
          <i class="ri-add-circle-line label-icon align-middle fs-16 me-2"> </i>Add New Department
        </button>
        @endcan  
        <div class="flex-shrink-0">
        </div>
      </div><!-- end card header -->
      <div class="card-body">
        <table class="table table-striped bordered" id="table_dept">
          <thead>
            <tr>
              <th scope="col" style="text-align:center">Id</th>
              <th scope="col" style="text-align:center">Department Name</th>
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
          <h5 class="modal-title" id="exampleModalgridLabel">Department</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form class="form" action="{{ route('department.store') }}" method="post">
          @csrf
          @method('POST')
          <div class="row g-3">
              <input type="hidden" id="id" name="id" value="">
              <div class="col-xxl-6">
                <div>
                  <label for="name" class="form-label">Name</label>
                  <input type="text" class="form-control" name="name" id="name" placeholder="Masukkan Nama Department " value="" required>
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
@endsection

@section('script')
<!-- Datatables -->
<script src="/assets/libs/Datatables/DataTables-1.13.1/js/jquery.dataTables.min.js"></script>
<script src="/assets/libs/Datatables/DataTables-1.13.1/js/dataTables.bootstrap5.min.js"></script>
<script src="/assets/libs/Datatables/Responsive-2.4.0/js/dataTables.responsive.min.js"></script>
<script src="/assets/js/pages/datatables.init.js"></script>
@endsection

@section('javascript')
  <script type="text/javascript">
    $(document).ready(function() {
      let table = $('#table_dept').DataTable({
        stateSave: true,
        responsive: true,
        autoWidth: false,
        processing: true,
        serverSide: true,
        ajax: "{{ route('department.index') }}",
        columns: [{
          data: 'id',
          name: 'id',
          "className": "text-center"
        }, {
          data: 'nama',
          name: 'nama',
          "className": "text-center",
        }, {
          data: 'action',
          name: 'action',
          "className": "text-center",
          orderable: false,
          searchable: false
        }, ]
      });
      $("#modal").on("hidden.bs.modal", function() {
        $("#resultContent").html("");
        $('#name').val(null).trigger('change');
        $("#submit-btn").prop("disabled", false);
      });

      $(document).on("click", ".edit-btn", function() {
        var areaId = $(this).data("id");
        $.ajax({
          url: "{{ route('department.edit') }}",
          method: "GET",
          data: {
            id: areaId
          },
          success: function(response) {
            $("input[name='id']").val(response.id);
            $("input[name='name']").val(response.name);


            $("#modal").modal("show");
          },
          error: function(xhr, status, error) {
            console.log(xhr, status, error);
          }
        });
      });

      $("form").submit(function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const submitButton = $(this).find("button[type='submit']");

        submitButton.prop("disabled", true);

        submitButton.find(".spinner-border").removeClass("d-none");
        submitButton.find(".indicator-label").addClass("d-none");

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
            const message = response.message;

            // Display the success message
            $("#resultContent").html(
              `<h4 class="text-success">${message}</h4>`).removeClass(
              "d-none");

            // Close the modal after 2 seconds
            setTimeout(function() {
              $("#modal").modal("hide");
            }, 1500);

            // Reload your data table (if needed)
            table.ajax.reload(null, false);
          },
          error: function(xhr, status, error) {
            console.log({
              xhr,
              status,
              error
            });
            handleErrorResponse(xhr.responseJSON);
          },
          complete: function() {
            submitButton.find(".spinner-border").addClass("d-none");
            submitButton.find(".indicator-label").removeClass("d-none");
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
            errorMessage += `<h4 class="text-danger">${responseJson.errors[fieldName][0]}</h4>`;
          }
        }

        if (responseJson.responseText) {
          errorMessage += `<h4 class="text-danger">${responseJson.responseText}</h4>`;

        }

        if (errorMessage === '') {
          errorMessage += '<h4 class="text-danger">An error occurred.</h4>';
        }

        $("#resultContent").html(errorMessage).removeClass("d-none");
      }
    });
  </script>
@endsection
