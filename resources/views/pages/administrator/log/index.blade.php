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
            <h4 class="mb-sm-0">Log User Activity</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Log</a></li>
                    <li class="breadcrumb-item active">List</li>
                </ol>
            </div>

        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <div class="row g-3">
                    <div class="col-md-2">
                        <div class="form-group">
                            <select class="form-control js-example-basic-single" name="bulan" id="bulan" required>
                                @if($month == '01')
                                <option value="01" selected> Januari</option>
                                @else
                                <option value="01"> Januari</option>
                                @endif
                                @if($month == '02')
                                <option value="02" selected> Februari</option>
                                @else
                                <option value="02"> Februari</option>
                                @endif
                                @if($month == '03')
                                <option value="03" selected> Maret</option>
                                @else
                                <option value="03"> Maret</option>
                                @endif
                                @if($month == '04')
                                <option value="04" selected> April</option>
                                @else
                                <option value="04"> April</option>
                                @endif
                                @if($month == '05')
                                <option value="05" selected> Mei</option>
                                @else
                                <option value="05"> Mei</option>
                                @endif
                                @if($month == '06')
                                <option value="06" selected> Juni</option>
                                @else
                                <option value="06"> Juni</option>
                                @endif
                                @if($month == '07')
                                <option value="07" selected> Juli</option>
                                @else
                                <option value="07"> Juli</option>
                                @endif
                                @if($month == '08')
                                <option value="08" selected> Agustus</option>
                                @else
                                <option value="08"> Agustus</option>
                                @endif
                                @if($month == '09')
                                <option value="09" selected> September</option>
                                @else
                                <option value="09"> September</option>
                                @endif
                                @if($month == '10')
                                <option value="10" selected> Oktober</option>
                                @else
                                <option value="10"> Oktober</option>
                                @endif
                                @if($month == '11')
                                <option value="11" selected> November</option>
                                @else
                                <option value="11"> November</option>
                                @endif
                                @if($month == '12')
                                <option value="12" selected> Desember</option>
                                @else
                                <option value="12"> Desember</option>
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <select class="form-control js-example-basic-single" name="tahun" id="tahun" required>
                                @for( $i=$max; $i>=$min; $i--)
                                    @if($i == $max)
                                    <option value="{{ $i }}" selected>{{ $i }}</option>
                                    @else
                                    <option value="{{ $i }}">{{ $i }}</option>
                                    @endif
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <button type="button" name="filter" id="filter" class="btn btn-soft-secondary waves-effect waves-light btn-sm"><i class="ri-filter-2-line me-1 align-bottom"></i> Filters</button>
                        <button type="button" name="refresh" id="refresh" class="btn btn-soft-danger waves-effect waves-light btn-sm"><i class="ri-refresh-line me-1 align-bottom"></i> Reset</button>
                    </div>
                </div>
            </div><!-- end card header -->
            <div class="card-body">
                <table class="table table-striped bordered" id="table_log">
                    <thead>
                        <tr>
                            <th scope="col" style="text-align:center">No</th>
                            <th scope="col" style="text-align:center">Date Time</th>
                            <th scope="col" style="text-align:center">User</th>
                            <th scope="col" style="text-align:center">Ip Address</th>
                            <th scope="col" style="text-align:center">Action</th>
                            <th scope="col" style="text-align:center">Description</th>
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
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script src="/assets/js/pages/datatables.init.js"></script>
<!-- Select2 -->
<script src="/assets/libs/adminlte/select2/js/4.1.0/select2.min.js"></script>
@endsection

@section('javascript')
<script>
    $(document).ready(function() {
        $('#bulan').select2();
        $('#tahun').select2();
    });
</script>
<script type="text/javascript">
    $(document).ready(function () {
        load_data();
        function load_data(bulan = '', tahun = ''){
            $('#table_log').DataTable({
                "responsive": true,
                "autoWidth": false,
                stateSave: true,
                processing: true,
                // serverSide: true,
                // "language": {
                //     "loadingRecords": "No data available in table"
                // },
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                ajax: {
                    url:"{{ route('log.index') }}",
                    data:{bulan:bulan, tahun:tahun}
                },
                    columns: [
                        {data: 'DT_RowIndex', name: 'DT_RowIndex' , "className": "text-center"},
                        {data: 'datetime', name: 'datetime' , "className": "text-center"},
                        {data: 'name', name: 'name' , "className": "text-center"},
                        {data: 'address', name: 'address' , "className": "text-center"},
                        {data: 'action', name: 'action' , "className": "text-center"},
                        {data: 'description', name: 'description' , "className": "text-center"},
                    ]
            });
        }
        $('#filter').click(function(){
            var bulan = $('#bulan').val();
            var tahun = $('#tahun').val();
            if(bulan != '' &&  tahun != '')
            {
                $('#table_log').DataTable().destroy();
                load_data(bulan, tahun);
            }else{
                alert('bulan dan tahun tidak boleh kosong');
            }
        });
        $('#refresh').click(function(){
            var bulan =  {{ Js::from($month) }};
            var tahun =  {{ Js::from($year) }};
            $('#bulan').val(bulan).trigger('change');
            $('#tahun').val(tahun).trigger('change');
            $('#table_log').DataTable().destroy();
            load_data();
        });
    });        
</script>
<script>
    $(document).ready(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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