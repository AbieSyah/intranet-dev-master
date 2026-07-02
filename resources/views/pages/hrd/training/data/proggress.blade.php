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
            <h4 class="mb-sm-0">PROGRESS TRAINING</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Progress</a></li>
                    <li class="breadcrumb-item active">Training</li>
                </ol>
            </div>

        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <ul class="nav nav-tabs nav-tabs-custom nav-success" role="tablist">
                <li class="nav-item">
                    <a class="nav-link py-3 active" id="pti-training" data-bs-toggle="tab" href="#pill-pti" role="tab">
                        <i class="ri-file-text-line me-1 align-bottom"></i> Program Training Insidentil
                    </a>
                </li>                                                                 
                <li class="nav-item">
                    <a class="nav-link py-3" id="ptt-training" data-bs-toggle="tab" href="#pill-ptt" role="tab">
                        <i class="ri-file-text-line me-1 align-bottom"></i> Program Training Tahunan
                    </a>
                </li>      
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="pill-pti" role="tabpanel">
                    <div class="px-3 mt-4 mb-2 align-items-center d-flex">
                        <ul class="nav nav-pills gap-2 mb-4" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button type="button" id="tab-pti-all" class="btn btn-primary border shadow list-group-item-primary active"
                                data-bs-toggle="tab" type="button" href="#pti-tab-all"
                                role="tab" aria-controls="pti-tab-all" aria-selected="true"><strong>All Training</strong>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button type="button" id="tab-pti-verified" class="btn btn-primary border shadow list-group-item-primary"
                                data-bs-toggle="tab" type="button" href="#pti-tab-verified"
                                role="tab" aria-controls="pti-tab-verified" aria-selected="false"><strong>Verification Training @if($count_jml_verified > 0)<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">{{$count_jml_verified}} <span class="visually-hidden">unread messages</span></span>@endif</strong>
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="tab-content">
                        <div class="tab-pane active" id="pti-tab-all" role="tabpanel">
                            <div class="card-body">            
                                <table class="table table-striped bordered" id="table_pti">
                                    <thead>
                                        <tr>
                                        <th scope="col" style="text-align:center">No</th>
                                        <th scope="col" style="text-align:center">Propose By</th>
                                        <th scope="col" style="text-align:center">Topic Training</th>
                                        <th scope="col" style="text-align:center">Total Participant</th>
                                        <th scope="col" style="text-align:center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane" id="pti-tab-verified" role="tabpanel">
                            <div class="card-body">            
                                <table class="table table-striped bordered" id="table_verified_pti">
                                    <thead>
                                        <tr>
                                            <th scope="col" style="text-align:center">No</th>
                                            <th scope="col" style="text-align:center">Propose By</th>
                                            <th scope="col" style="text-align:center">Topic Training</th>
                                            <th scope="col" style="text-align:center">Total Participant</th>
                                            <th scope="col" style="text-align:center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                            <!-- Approve Modal -->
                            <div class="modal fade bs-example-modal-center" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-top">
                                    <div class="modal-content">
                                        <div class="modal-body text-center p-5">
                                            <div class="mt-4">
                                                <h4 class="mb-3">Apakah Anda Yakin ?</h4>
                                                <form id="form-approve" method="POST" action="{{ route('training.data.verification.proggress.store') }}">
                                                    @csrf
                                                    @method('put')
                                                    <input type="hidden" name="kode_judul" id="kode_judul" value="">
                                                    <div class="hstack gap-2 justify-content-center">
                                                        <button type="submit" class="btn btn-primary">Ya</button>
                                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tidak</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div><!-- /.modal-content -->
                                </div><!-- /.modal-dialog -->
                            </div><!-- /.modal -->
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="pill-ptt" role="tabpanel">
                    <div class="px-3 mt-4 mb-2 align-items-center d-flex">
                        <ul class="nav nav-pills gap-2 mb-4" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button type="button" id="tab-ptt-all" class="btn btn-primary border shadow list-group-item-primary active"
                                data-bs-toggle="tab" type="button" href="#ptt-tab-all"
                                role="tab" aria-controls="ptt-tab-all" aria-selected="true"><strong>All Training</strong>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button type="button" id="tab-ptt-verified" class="btn btn-primary border shadow list-group-item-primary"
                                data-bs-toggle="tab" type="button" href="#ptt-tab-verified"
                                role="tab" aria-controls="ptt-tab-verified" aria-selected="false"><strong>Verification Training @if($count_jml_verified_ptt > 0)<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">{{$count_jml_verified_ptt}} <span class="visually-hidden">unread messages</span></span>@endif</strong>
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="tab-content">
                        <div class="tab-pane active" id="ptt-tab-all" role="tabpanel">
                            <div class="card-body">            
                                <table class="table table-striped bordered" id="table_ptt">
                                    <thead>
                                        <tr>
                                        <th scope="col" style="text-align:center">No</th>
                                        <th scope="col" style="text-align:center">Tahun</th>
                                        <th scope="col" style="text-align:center">Jumlah Usulan Topic</th>
                                        <th scope="col" style="text-align:center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane" id="ptt-tab-verified" role="tabpanel">
                            <div class="card-body">            
                                <table class="table table-striped bordered" id="table_verified_ptt">
                                    <thead>
                                        <tr>
                                        <th scope="col" style="text-align:center">No</th>
                                        <th scope="col" style="text-align:center">Tahun</th>
                                        <th scope="col" style="text-align:center">Jumlah Usulan Topic</th>
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
<script src="https://cdn.datatables.net/select/1.2.7/js/dataTables.select.min.js"></script>
<script src="/assets/js/pages/datatables.init.js"></script>
<!-- Select2 -->
<script src="/assets/libs/adminlte/select2/js/4.1.0/select2.min.js"></script>
<script src="/assets/js/pages/select2.init.js"></script>
<!-- Sweetalert -->
<link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<!-- Toastr Notifications-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
@endsection

@section('javascript')
@if(Session::has('tab_pti_all'))
<script>
    $('#pti-training').addClass('active');
    $('#pill-pti').addClass('active');
    $('#pti-tab-all').addClass('active');
    $('#pti-tab-verified').removeClass('active');
    $('#tab-pti-all').addClass('active');
    $('#tab-pti-verified').removeClass('active');
    $('#ptt-training').removeClass('active');
    $('#pill-ptt').removeClass('active');
    $('#ptt-tab-all').removeClass('active');
    $('#ptt-tab-verified').removeClass('active');
    $('#tab-ptt-all').removeClass('active');
    $('#tab-ptt-verified').removeClass('active');
</script>
@endif
@if(Session::has('tab_ptt_all'))
<script>
    $('#pti-training').removeClass('active');
    $('#pill-pti').removeClass('active');
    $('#pti-tab-all').removeClass('active');
    $('#pti-tab-verified').removeClass('active');
    $('#tab-pti-all').removeClass('active');
    $('#tab-pti-verified').removeClass('active');
    $('#ptt-training').addClass('active');
    $('#pill-ptt').addClass('active');
    $('#ptt-tab-all').addClass('active');
    $('#ptt-tab-verified').removeClass('active');
    $('#tab-ptt-all').addClass('active');
    $('#tab-ptt-verified').removeClass('active');
</script>
@endif
@if(Session::has('pti-tab-verified'))
<script>
    $('#pti-training').addClass('active');
    $('#pill-pti').addClass('active');
    $('#pti-tab-all').addClass('active');
    $('#pti-tab-verified').addClass('active');
    $('#tab-pti-all').addClass('active');
    $('#tab-pti-verified').removeClass('active');
    $('#ptt-training').removeClass('active');
    $('#pill-ptt').removeClass('active');
    $('#ptt-tab-all').removeClass('active');
    $('#ptt-tab-verified').removeClass('active');
    $('#tab-ptt-all').removeClass('active');
    $('#tab-ptt-verified').removeClass('active');
</script>
@endif
@if(Session::has('ptt-tab-verified'))
<script>
    $('#pti-training').removeClass('active');
    $('#pill-pti').removeClass('active');
    $('#pti-tab-all').removeClass('active');
    $('#pti-tab-verified').removeClass('active');
    $('#tab-pti-all').removeClass('active');
    $('#tab-pti-verified').removeClass('active');
    $('#ptt-training').addClass('active');
    $('#pill-ptt').addClass('active');
    $('#ptt-tab-all').removeClass('active');
    $('#ptt-tab-verified').addClass('active');
    $('#tab-ptt-all').removeClass('active');
    $('#tab-ptt-verified').addClass('active');
</script>
@endif
<script type="text/javascript">
    $(document).ready(function() {
        //pti
        let table_pti = $('#table_pti').DataTable({
            stateSave: true,
            responsive: true,
            autoWidth: false,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('training.data.proggress') }}"
            },
            columns: [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                "className": "text-center"
            },
            {
                data: 'pemohon',
                name: 'pemohon',
                "className": "text-center"
            },
            {
                data: 'judul',
                name: 'judul',
                "className": "text-center"
            },
            {
                data: 'jml_peserta',
                name: 'jml_peserta',
                "className": "text-center"
            },            
            {
                data: 'action',
                name: 'action',
                "className": "text-center",
                orderable: false,
                searchable: false
            },
            {
                data: 'peserta',
                name: 'peserta',
                "className": "none text-center"
            }
            ]
        });

        let table_verified_pti = $('#table_verified_pti').DataTable({
            stateSave: true,
            responsive: true,
            autoWidth: false,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('training.data.verification.proggress') }}"
            },
            columns: [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                "className": "text-center"
            },
            {
                data: 'pemohon',
                name: 'pemohon',
                "className": "text-center"
            },
            {
                data: 'judul',
                name: 'judul',
                "className": "text-center"
            },
            {
                data: 'jml_peserta',
                name: 'jml_peserta',
                "className": "text-center"
            },            
            {
                data: 'action',
                name: 'action',
                "className": "text-center",
                orderable: false,
                searchable: false
            },
            {
                data: 'peserta',
                name: 'peserta',
                "className": "none text-center"
            }
            ]
        });
        $('#table_verified_pti').on("click", ".view-approve", function() {
            var kode_judul = $(this).data("id");
            $('#kode_judul').val(kode_judul);
        });

        //ptt
        let table_ptt = $('#table_ptt').DataTable({
            stateSave: true,
            responsive: true,
            autoWidth: false,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('training.data.proggress.ptt') }}"
            },
            columns: [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                "className": "text-center"
            },
            {
                data: 'tahun_usulan',
                name: 'tahun_usulan',
                "className": "text-center"
            },
            {
                data: 'jumlah_usulan',
                name: 'jumlah_usulan',
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

        let table_verified_ptt = $('#table_verified_ptt').DataTable({
            stateSave: true,
            responsive: true,
            autoWidth: false,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('training.data.proggress.ptt.verified') }}"
            },
            columns: [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                "className": "text-center"
            },
            {
                data: 'tahun_usulan',
                name: 'tahun_usulan',
                "className": "text-center"
            },
            {
                data: 'jumlah_usulan',
                name: 'jumlah_usulan',
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
    });
</script>
<script>
    $("#form-approve").submit(function(e) {
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
            swalert.then(() => window.location.href = "{{ route('profile.back.fkt.approve.pti') }}")
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