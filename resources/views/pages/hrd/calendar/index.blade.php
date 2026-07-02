@extends('layouts.master')
@section('link')
<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- Datatables-->
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
            <h4 class="mb-sm-0">Template Calendar</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Template</a></li>
                    <li class="breadcrumb-item active">Calendar</li>
                </ol>
            </div>

        </div>
    </div>
</div>

<div class="row">
  <div class="col-lg-12">
      <div class="card">
          <div class="card-header align-items-center d-flex">
            @can('hrd.calendar.template.create')
              <button type="button" class="btn btn-primary btn-label waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#modal" data-text="Create New Template">
                  <i class="ri-add-circle-line label-icon align-middle fs-16 me-2"> </i>Create New Template
              </button>
            @endcan
          </div><!-- end card header -->
          <div class="card-body">
              <table class="table table-striped bordered" id="table_calendar">
                  <thead>
                      <tr>
                      <th scope="col" style="text-align:center">NO</th>
                      <th scope="col" style="text-align:center">TAHUN</th>
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
<!--Modal add/edit-->
<div class="modal fade" id="modal" tabindex="-1" aria-labelledby="exampleModalgridLabel" aria-modal="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
          <h5 class="modal-title" id="exampleModalgridLabel">Create/Update Template</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="form" action="{{ route('calendar.store') }}" method="post">
          @csrf
          @method('POST')
          <div class="row g-3">
            <input type="hidden" id="id" name="id" value="">
            <div class="col-lg-12">
                <div>
                  <label for="tahun" class="form-label">Tahun</label>
                  <select class="form-control select2 @error('tahun') is-invalid @enderror" name="tahun" id="tahun" data-placeholder="Pilih Tahun" required>
                    @for( $i=$min_year; $i<=$max_year; $i++)
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                  </select>
                </div>
            </div><!--end col-->
            <div class="col-lg-12">
                <div class="hstack gap-2 justify-content-end">
                    <button type="submit" class="btn btn-primary">Generate</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div><!--end col-->
          </div><!--end row-->
        </form>
      </div>
    </div>
  </div>
</div>
<!--Modal upload-->
<div class="modal fade" id="upload-calendar" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form id="form-upload" action="{{ route('calendar.upload') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-header">
            <h5 class="modal-title" id="myModalLabel">Upload File</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
        </div>
        <div class="modal-body">
            <input type="hidden" class="form-control" id="id_calendar" name="id_calendar" value=""/>
            <div class="row">
                <div class="col-lg-12">
                    <div class="mb-3">
                        <label class="form-label">Tahun</label>
                        <input type="text" class="form-control" name="upload_tahun" id="upload_tahun" style="Background-color: #eff2f7;" readonly>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <label class="form-label">Upload</label>
                    <div class="input-group">
                        <input onchange="uploadValidation(this);" type="file" class="form-control form-control text-sm col-sm-6" name="file" id="file" accept="application/pdf,application/PDF" required>
                        <button type="button" class="btn btn-soft-danger waves-effect waves-light" onclick="clearUpload()">Remove</button>
                    </div>
                    <span class="form-text">hanya menerima file bertipe .pdf | .PDF</span>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" id="btn-save" class="btn btn-primary ">Submit</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- Modal Validation Extension File Upload -->
<div class="modal fade" id="validationmodal" aria-hidden="true" aria-labelledby="..." tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-body text-center p-5">
        <lord-icon
            src="https://cdn.lordicon.com/tdrtiskw.json"
            trigger="loop"
            colors="primary:#f7b84b,secondary:#405189"
            style="width:130px;height:130px">
        </lord-icon>
        <div class="mt-0 pt-4">
            <h4>Whoops, ada yang salah!</h4>
            <div id="info-validation"></div>
            <!-- Toogle to second dialog -->
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</div>
<!--modal preview mcu-->
<div class="modal flip" id="modal-preview" tabindex="-1" aria-labelledby="exampleModalgridLabel" aria-modal="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="modal-judul"></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <iframe src="" id="show-preview" frameborder="0" style="height:500px; width:100%;"></iframe>          
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
<!-- Select2 -->
<script src="/assets/libs/adminlte/select2/js/4.1.0/select2.min.js"></script>
<!-- Sweetalert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection

@section('javascript')
<script>
    $('#tahun').select2({dropdownParent: $('#modal .modal-content')});

    function clearUpload(){
      var upload = document.getElementById('file');
      upload.value = '';
    }

    function uploadValidation(){
      var upload = document.getElementById('file');
      var pathUpload= upload.value;

      // tipe file yang diizinkan
      var allowedExtensions = /(\.pdf|\.PDF)$/i;

      if (!allowedExtensions.exec(pathUpload)) {
          document.getElementById(
              'info-validation').innerHTML =
              '<p class="text-muted fs-12">Maaf hanya menerima file document yang bertipe .pdf | .PDF</p>';
          $('#validationmodal').modal('show');
          upload.value = '';
          return false;
      }
      else
      {             
          // dijalankan
      }        
    }
</script>
<script>
    //submit upload calendar
    $("#form-upload").submit(function(e) {
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
    $(document).ready(function () {
      $('#tahun').val(null).trigger('change');
        let swalert;
        $('#table_calendar').DataTable({
        responsive: true,
        autoWidth: false,
        stateSave: true,
        processing: true,
        serverSide: true,
        ajax: "{{ route('calendar.index') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex' , "className": "text-center"},
                {data: 'tahun', name: 'tahun' , "className": "text-center"},
                {data: 'action', name: 'action', "className": "text-center", orderable: false, searchable: false},
            ]
        });

        $("#modal").on("hidden.bs.modal", function() {
          $("#id").val(null);
          $('#tahun').val(null).trigger('change');
          // $("#show-preview").html('');
        });

        $("#upload-calendar").on("hidden.bs.modal", function() {
          $("#file").val(null);
          $("#upload_tahun").val(null);
        });

        $(document).on("click", ".edit-btn", function(){
          var tempId = $(this).data("id");
          $.ajax({
            url: "{{ route('calendar.edit') }}",
            method: "GET",
            data: {
              id: tempId
            },
            success: function(response){
              $("input[name='id']").val(response.id);
              // $("input[name='tahun']").val(response.tahun);
              // $('#tahun').val(null).trigger('change');

              $('#tahun').find('option[value="' + response.tahun + '"]').prop(
                'selected', true);

              $('#tahun').trigger('change');

              $("#modal").modal("show");
            },
            error: function(xhr, status, error) {
              console.log(xhr, status, error);
            }
          })
        });

        $('#table_calendar tbody').on('click', 'tr', function () {
            //get id calendar
            var id_calendar = $(this).closest('tr').find('#calendar_id').val();
            $("#id_calendar").val(id_calendar);
            var upload_tahun = $(this).closest('tr').find('#tahun_upload').val();
            $("#upload_tahun").val(upload_tahun);
            //get id preview
            var id_preview = $(this).closest('tr').find('#id_preview').val();
            $("#show-preview").attr('src', id_preview);

        }); 

        $('#form').submit(function(e) {
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
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });
</script>
@endsection