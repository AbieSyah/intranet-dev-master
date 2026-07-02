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
            <h4 class="mb-sm-0">TRAINING</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">HRD</a></li>
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
                    <a class="nav-link py-3 active" id="data-training" data-bs-toggle="tab" href="#pill-data" role="tab">
                        <i class="ri-file-user-line me-1 align-bottom"></i> Data Training
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link py-3" id="verified-training" data-bs-toggle="tab" href="#pill-verified" role="tab">
                        <i class="ri-task-line me-1 align-bottom"></i> Verification Training
                    </a>
                </li>                                                                       
                <li class="nav-item">
                    <a class="nav-link py-3" id="approved-training" data-bs-toggle="tab" href="#pill-approved" role="tab">
                        <i class="ri-task-line me-1 align-bottom"></i> Approved Training
                    </a>
                </li>                                                                       
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="pill-data" role="tabpanel">
                    <div class="px-3 mt-4 mb-2 row">
                        <!-- <div class="col-lg-9">                           
                        </div> -->
                        <div class="col-lg-1 mt-2">
                            <label class="form-label">Periode :</label>
                        </div>
                        <div class="col-lg-2 mt-2">
                            <div class="form-group">
                                <select class="form-control js-example-basic-single" name="from_year" id="from_year">
                                    @for( $i=$max; $i>=$min; $i--)
                                        @if($i == $year_now)
                                        <option value="{{ $i }}" selected>{{ $i }}</option>
                                        @else
                                        <option value="{{ $i }}">{{ $i }}</option>
                                        @endif
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div><!-- end card header -->
                    <div class="card-body">            
                        <table class="table table-striped bordered" id="table_training">
                            <thead>
                                <tr>
                                <th scope="col" style="text-align:center">No</th>
                                <th scope="col" style="text-align:center">Nama</th>
                                <th scope="col" style="text-align:center">Bagian</th>
                                <th scope="col" style="text-align:center">Jumlah Training</th>
                                <!-- <th scope="col" style="text-align:center">STATUS</th> -->
                                <th scope="col" style="text-align:center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane" id="pill-verified" role="tabpanel">
                    <div class="card-body">            
                        <table class="table table-striped bordered" id="table_verified">
                            <thead>
                                <tr>
                                <th scope="col" style="text-align:center">No</th>
                                <th scope="col" style="text-align:center">Nama Pemohon</th>
                                <th scope="col" style="text-align:center">Topik Training</th>
                                <th scope="col" style="text-align:center">Status</th>
                                <th scope="col" style="text-align:center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane" id="pill-approved" role="tabpanel">
                    <div class="card-body">
                        <ul class="nav nav-pills gap-2 mb-4" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button type="button" id="unscheduled" class="btn btn-primary border shadow list-group-item-primary active"
                                data-bs-toggle="tab" type="button" href="#pill-unscheduled"
                                role="tab" aria-controls="pill-unscheduled" aria-selected="true"><i class="ri-calendar-line align-bottom me-1"></i> Unscheduled
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button type="button" id="scheduled" class="btn btn-primary border shadow list-group-item-primary"
                                data-bs-toggle="tab" type="button" href="#pill-scheduled"
                                role="tab" aria-controls="pill-scheduled" aria-selected="false"><i class="ri-calendar-check-line align-bottom me-1"></i> Scheduled
                                </button>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="pill-unscheduled" role="tabpanel">            
                                <table class="table table-striped bordered" id="table_approved">
                                    <thead>
                                        <tr>
                                        <th scope="col" style="text-align:center">No</th>
                                        <th scope="col" style="text-align:center"></th>
                                        <th scope="col" style="text-align:center">Topik Training</th>
                                        <th scope="col" style="text-align:center">Status</th>
                                        <th scope="col" style="text-align:center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                            <div class="tab-pane" id="pill-scheduled" role="tabpanel">            
                                <table class="table table-striped bordered" id="table_schedule">
                                    <thead>
                                        <tr>
                                            <th scope="col" style="text-align:center">No</th>
                                            <th scope="col" style="text-align:center">Topik Training</th>
                                            <th scope="col" style="text-align:center">Jumlah Peserta</th>
                                            <th scope="col" style="text-align:center">Tanggal Mulai</th>
                                            <th scope="col" style="text-align:center">Tanggal Akhir</th>
                                            <th scope="col" style="text-align:center">Provider</th>
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
<!-- Second modal dialog -->
<div class="modal fade" id="secondmodal" aria-hidden="true" aria-labelledby="..." tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="flipModalLabel">Create Schedule Training</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">                
                <form id="Form-training" action="{{ route('training.store') }}" method="POST">
                @csrf
                    <div class="row g-3">
                        <input type="hidden" id="kode_fkt" name="kode_fkt">
                        <input type="hidden" id="judul_fkt" name="judul_fkt">
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
                            <div>
                                <label for="lokasi" class="form-label">Lokasi</label>
                                <input type="text" class="form-control" name="lokasi" id="lokasi" placeholder="Masukkan Nama Lokasi" value="" required>
                            </div>
                        </div><!--end col-->    
                        <div class="col-lg-12">
                            <div>
                                <label for="biaya" class="form-label">Biaya</label>
                                <input type="text" class="form-control" name="biaya" id="biaya" placeholder="Masukkan Nama Biaya" value="" required>
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
                            <div class="hstack gap-2 justify-content-end mt-4">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                <button type="submit" id="submit-jadwal" class="btn btn-primary">Submit</button>
                            </div>
                        </div><!--end col-->
                    </div><!--end row-->
                </form>
            </div>
        </div><!-- /.modal-content -->
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
<!-- Toastr Notifications-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
@endsection

@section('javascript')
@if(Session::has('scheduled'))
<script>
    $('#data-training').removeClass('active');
    $('#pill-data').removeClass('active');
    $('#verified-training').removeClass('active');
    $('#pill-verified').removeClass('active');
    $('#approved-training').addClass('active');
    $('#pill-approved').addClass('active');
</script>
@endif
<script>
    $('#secondmodal').on('hidden.bs.modal', function () {
        $(this).find('form').trigger('reset');
    });
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
    //convert currency
    var rupiah = document.getElementById('biaya');
        rupiah.addEventListener("keyup", function(e) {
        // tambahkan 'Rp.' pada saat form di ketik
        // gunakan fungsi formatRupiah() untuk mengubah angka yang di ketik menjadi format angka
        rupiah.value = formatRupiah(this.value);
        });
        /* Fungsi formatRupiah */
        function formatRupiah(angka, prefix) {
        var number_string = angka.replace(/[^,\d]/g, "").toString(),
            split = number_string.split(","),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        // tambahkan titik jika yang di input sudah menjadi angka ribuan
        if (ribuan) {
            separator = sisa ? "." : "";
            rupiah += separator + ribuan.join(".");
        }

        rupiah = split[1] != undefined ? rupiah + "," + split[1] : rupiah;
        return prefix == undefined ? rupiah : rupiah ? rupiah : "";
        }
</script>
<script type="text/javascript">
    $(document).ready(function() {
        load_data();
        function load_data(from_year = ''){
            let table = $('#table_training').DataTable({
                stateSave: true,
                responsive: true,
                autoWidth: false,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('training.index') }}",
                    data:{from_year:from_year}
                },
                columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    "className": "text-center"
                },
                {
                    data: 'nama',
                    name: 'nama',
                    "className": "text-center"
                },
                {
                    data: 'bagian',
                    name: 'bagian',
                    "className": "text-center"
                },
                {
                    data: 'total',
                    name: 'total',
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
        }
        $("#from_year").change(function(){
            var from_year = this.value;
            $('#table_training').DataTable().destroy();
            load_data(from_year);
        });

        //tabel verified
        let table_verified = $('#table_verified').DataTable({
            stateSave: true,
            responsive: true,
            autoWidth: false,
            processing: true,
            serverSide: true,
            ajax: "{{ route('training.hrd.verified') }}",
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
        //tabel approved
        let table_approved = $('#table_approved').DataTable({
            stateSave: true,
            responsive: true,
            autoWidth: false,
            processing: true,
            serverSide: true,
            ajax: "{{ route('training.hrd.approved') }}",
            columns: [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                "className": "text-center"
            },
            {
                data: 'kode',
                name: 'kode',
                "className": "text-center"
            },
            {
                data: 'judul',
                name: 'judul',
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
            {
                data: 'peserta',
                name: 'peserta',
                "className": "none text-center"
            },
            ]
        });
        table_approved.column(1).visible(false);
        $('#table_approved').on('click', '.btn-schedule', function () {
            var kode_fkt = $(this).closest('tr').find('#btn-kode').val();
            var judul_fkt = $(this).closest('tr').find('#btn-judul').val();
            $('#kode_fkt').val(kode_fkt);
            $('#judul_fkt').val(judul_fkt);
        });
        //tabel table_schedule
        let table_schedule = $('#table_schedule').DataTable({
            stateSave: true,
            responsive: true,
            autoWidth: false,
            processing: true,
            serverSide: true,
            ajax: "{{ route('training.hrd.schedule') }}",
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
                data: 'jml_peserta',
                name: 'jml_peserta',
                "className": "text-center"
            },
            {
                data: 'tgl_mulai',
                name: 'tgl_mulai',
                "className": "text-center"
            },
            {
                data: 'tgl_akhir',
                name: 'tgl_akhir',
                "className": "text-center"
            },
            {
                data: 'vendor',
                name: 'vendor',
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
            },
            ]
        });
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