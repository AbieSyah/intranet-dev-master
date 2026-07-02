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
<!-- Select2-->
<link rel="stylesheet" href="/assets/libs/adminlte/select2/css/4.1.0/select2.min.css">
<!-- Toastr Notifications-->
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">RECORD TRAINING</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Record</a></li>
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Training</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </div>

        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-xxl-9">
        <div class="card" id="demo">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card-header border-bottom-dashed p-4">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                @if(!empty($employee->avatar))
                                <img src="{{ asset('storage/avatars/'.$employee->avatar) }}" class="show-image rounded-circle avatar-xl img-thumbnail user-profile-image" alt="user-profile-image">
                                @else
                                <img src="{{ asset('storage/avatars/user.jpg') }}" class="rounded-circle avatar-xl img-thumbnail user-profile-image" alt="user-profile-image">
                                @endif
                                <div class="mt-sm-3 mt-3">
                                    <h6 class="text-muted text-uppercase fw-semibold">{{$employee->fullname}}</h6>
                                    <p class="text-muted mb-1">{{$employee->area->name}}</p>
                                    <p class="text-muted mb-0">{{$employee->department->name}}</p>
                                </div>
                            </div>
                            <div class="flex-shrink-0 mt-sm-0 mt-3">
                            <a href="{{ route('training.data.index') }}" class="btn btn-primary mb-4 btn-label waves-effect waves-light float-end"><i class="ri-arrow-left-circle-line label-icon align-middle fs-16 me-2"></i> Back</a>
                            </div>
                        </div>
                    </div>
                    <!--end card-header-->
                </div><!--end col-->
                <div class="col-lg-12">
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-lg-3 col-6">
                                <p class="text-muted mb-2 text-uppercase fw-semibold">NA</p>
                                <h5 class="fs-14 mb-0">NA</h5>
                            </div>
                            <!--end col-->
                            <div class="col-lg-3 col-6">
                                <p class="text-muted mb-2 text-uppercase fw-semibold">NA</p>
                                <h5 class="fs-14 mb-0">NA</h5>
                            </div>
                            <!--end col-->
                            <div class="col-lg-3 col-6">
                                <p class="text-muted mb-2 text-uppercase fw-semibold">NA</p>
                                <span class="badge badge-soft-success fs-11" id="payment-status">NA</span>
                            </div>
                            <!--end col-->
                            <div class="col-lg-3 col-6">
                                <p class="text-muted mb-2 text-uppercase fw-semibold">Total Training</p>
                                <h5 class="fs-14 mb-0"><span id="total-amount">{{$total_training}}</span></h5>
                            </div>
                            <!--end col-->
                        </div>
                        <!--end row-->
                    </div>
                    <!--end card-body-->
                </div><!--end col-->
                <div class="col-lg-12">
                    <div class="card-body p-4 border-top border-top-dashed">
                        <div class="table-responsive">
                            <table class="table table-striped bordered" id="table_training">
                                <thead>
                                    <tr>
                                    <th scope="col" style="text-align:center">#</th>
                                    <th scope="col" style="text-align:center">Training</th>
                                    <th scope="col" style="text-align:center">Tanggal Mulai</th>
                                    <th scope="col" style="text-align:center">Tanggal Akhir</th>
                                    <th scope="col" style="text-align:center">Lokasi</th>
                                    <th scope="col" style="text-align:center">Biaya</th>
                                    <th scope="col" style="text-align:center">Status</th>
                                    <th scope="col" style="text-align:center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!--end card-body-->
                </div><!--end col-->
            </div><!--end row-->
        </div>
        <!--end card-->
    </div>
    <!--end col-->
</div>
<!--Modal Training edit-->
<div class="modal fade" id="modalTraining" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
          <h5 class="modal-title" id="exampleModalgridLabel">Update Training</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="Form-training" action="{{ route('training.data.update') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="row g-3">
            <input type="hidden" id="id_training" name="id_training" value="">
            <div class="col-lg-12">
                <div>
                    <label for="judul" class="form-label">Nama Training</label>
                    <input type="text" class="form-control" name="judul" id="judul" placeholder="Masukkan Nama Training" value="" required>
                </div>
            </div><!--end col-->
            <div class="col-lg-12">
                <div>
                    <label for="detail" class="form-label">Detail</label>
                    <textarea class="form-control" name="detail" id="detail" rows="3"></textarea>
                </div>
            </div><!--end col-->
            <div class="col-lg-12">                            
                <div>
                    <label for="start_date" class="form-label">Tanggal Mulai</label>
                    <div class="input-group">
                        <input type="text" name="start_date" id="start_date"
                            class="form-control @error('start_date') is-invalid @enderror"
                            placeholder="Pilih Tanggal" value="" required>
                        <span class="input-group-text"><i class="ri-calendar-event-line"></i></span>
                    </div>
                </div>
            </div><!--end col-->    
            <div class="col-lg-12">                            
                <div>
                    <label for="end_date" class="form-label">Tanggal Akhir</label>
                    <div class="input-group">
                        <input type="text" name="end_date" id="end_date"
                            class="form-control @error('end_date') is-invalid @enderror"
                            placeholder="Pilih Tanggal" value="" required>
                        <span class="input-group-text"><i class="ri-calendar-event-line"></i></span>
                    </div>
                </div>
            </div><!--end col-->
            <div class="col-lg-12">                            
                <label for="id_vendor" class="form-label">Vendor</label>
                <select class="form-control" id="id_vendor" name="id_vendor" data-placeholder="Pilih Vendor" required>
                    <option selected="true" disabled="true"></option>
                    @foreach($vendors as $vendor)
                        <option value="{{$vendor->id}}">{{$vendor->nama}}</option>
                    @endforeach
                </select>
            </div><!--end col-->
            <div class="col-lg-12">
                <div>
                    <label for="lokasi" class="form-label">Lokasi</label>
                    <input type="text" class="form-control" name="lokasi" id="lokasi" placeholder="Masukkan Nama Lokasi" value="" required>
                </div>
            </div><!--end col-->    
            <div class="col-lg-12">
                <div>
                    <label for="biaya" class="form-label">Biaya</label>
                    <input type="number" class="form-control" name="biaya" id="biaya" placeholder="Masukkan Nama Biaya" value="" required>
                </div>
            </div><!--end col-->
            <div class="col-lg-12">                            
                <div>
                    <label for="exp_date" class="form-label">Tanggal Kadaluarsa</label>
                    <div class="input-group">
                        <input type="text" name="exp_date" id="exp_date"
                            class="form-control @error('exp_date') is-invalid @enderror"
                            placeholder="Pilih Tanggal" value="">
                        <span class="input-group-text"><i class="ri-calendar-event-line"></i></span>
                    </div>
                </div>
            </div><!--end col-->    
            <div class="col-lg-12">
                <div>
                    <label class="form-label">Upload Sertifikat</label>
                    <div class="input-group">
                        <input onchange="uploadSertifikatValidation(this);" type="file" class="form-control form-control text-sm col-sm-6" name="file_sertifikat" id="file_sertifikat" accept="application/pdf,application/PDF">
                        <button type="button" class="btn btn-soft-danger waves-effect waves-light" onclick="clearSertifikatUpload()">Remove</button>
                    </div>
                    <span class="form-text">hanya menerima file bertipe .pdf | .PDF</span>
                </div>
            </div><!--end col-->
            <div class="col-lg-12">                            
                <label for="status" class="form-label">Status</label>
                <select class="form-control" id="status" name="status" data-placeholder="Pilih Status" required>
                    <option selected="true" disabled="true"></option>
                    <option value="ON PROGRESS">ON PROGRESS</option>
                    <option value="FINISHED">FINISHED</option>
                </select>
            </div><!--end col-->
            <div class="col-lg-12">
                <div class="hstack gap-2 justify-content-end mt-4">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" id="btn-save" class="btn btn-primary">Save</button>
                </div>
            </div><!--end col-->
          </div><!--end row-->
        </form>
      </div>
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
<!--Modal staticbackdrop-->
<div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <img src="/assets/images/loading.gif" style="width:120px;height:120px">                    
                <div class="mt-4">
                    <h4 class="mb-3">Please wait...</h4>
                    <h4 class="mb-3">Do not leave this page</h4>
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
<script src="/assets/js/pages/datatables.init.js"></script>
<!-- Select2 -->
<script src="/assets/libs/adminlte/select2/js/select2.min.js"></script>
<script src="/assets/js/pages/select2.init.js"></script>
<!-- Toastr Notifications-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
@endsection

@section('javascript')
<script>
    $(function () {
        $('#id_vendor').select2({dropdownParent: $('#modalTraining .modal-content')});
        $('#status').select2({dropdownParent: $('#modalTraining .modal-content')});
    });

    $( "#btn-save" ).click(function() {
        $("#Form-training").submit(function () {
            $('#modalTraining').modal('toggle');
            $('#staticBackdrop').modal('show', true);
        });
    });

    function uploadSertifikatValidation(){
        var upload = document.getElementById('file_sertifikat');
        var pathUpload= upload.value;

        // tipe file yang diizinkan
        var allowedExtensions = /(\.pdf|\.PDF)$/i;

        if (!allowedExtensions.exec(pathUpload)) {
            document.getElementById(
                'info-validation').innerHTML =
                '<p class="text-muted fs-12">Maaf hanya menerima file document yang bertipe .pdf | .PDF</p>';
            $('#modalTraining').modal('toggle');
            $('#validationmodal').modal('show');
            upload.value = '';
            return false;
        }
        else
        {             
            // dijalankan
        }      
    }

    function clearSertifikatUpload(){
        var upload = document.getElementById('file_sertifikat');
        upload.value = '';
    } 
</script>
<script type="text/javascript">
    $(document).ready(function() {
        let table = $('#table_training').DataTable({
            stateSave: true,
            responsive: true,
            autoWidth: false,
            processing: true,
            serverSide: true,
            ajax: "{{ route('training.data.detail', $kode) }}",
            columns: [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                "className": "text-center"
            },
            {
                data: 'judul',
                name: 'judul',
                "className": "text-center"
            },
            {
                data: 'start_date',
                name: 'start_date',
                "className": "text-center"
            },
            {
                data: 'end_date',
                name: 'end_date',
                "className": "text-center"
            },
            {
                data: 'lokasi',
                name: 'lokasi',
                "className": "text-center"
            },
            {
                data: 'biaya',
                name: 'biaya',
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
    });
</script>
<script>
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
<script>
    $('#table_training').on("click", ".edit-btn", function() {
        var training_id = $(this).data("id");
        $.ajax({
            url: "{{ route('training.edit') }}",
            method: "GET",
            data: {
                id: training_id
            },
            success: function(result) {    
                // console.log(result)            
                //send to edit modal
                $("input[name='id_training']").val(result.id);
                $("input[name='judul']").val(result.judul);
                document.getElementsByName('detail')[0].value = result.detail;
                $("input[name='start_date']").val(result.start_date);
                $("input[name='end_date']").val(result.end_date);
                $("input[name='exp_date']").val(result.exp_date);
                $('#id_vendor').val(result.id_vendor).trigger('change');
                $("input[name='lokasi']").val(result.lokasi);
                $("input[name='biaya']").val(result.biaya);
                $('#status').val(result.status).trigger('change');
                $("#modalTraining").modal("show");
            },
            error: function(xhr, status, error) {
                console.log(xhr, status, error);
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
</script>
@endsection