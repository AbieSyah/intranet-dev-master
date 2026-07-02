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
            <h4 class="mb-sm-0">RECORD TRAINING</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Record</a></li>
                    <li class="breadcrumb-item active">Training</li>
                </ol>
            </div>

        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
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
                        <th scope="col" style="text-align:center">NIK</th>
                        <th scope="col" style="text-align:center">Nama</th>
                        <th scope="col" style="text-align:center">Departemen/Bagian</th>
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
                    url: "{{ route('training.data.index') }}",
                    data:{from_year:from_year}
                },
                columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    "className": "text-center"
                },
                {
                    data: 'nik',
                    name: 'nik',
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