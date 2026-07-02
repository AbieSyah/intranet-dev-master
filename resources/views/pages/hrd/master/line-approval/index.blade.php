@extends('layouts.master')
@section('link')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Datatables-->
    <link href="{{ url('') }}/assets/libs/Datatables/DataTables-1.13.1/css/dataTables.bootstrap5.css" rel="stylesheet"
        type="text/css" />
    <link href="{{ url('') }}/assets/libs/Datatables/Buttons-2.3.3/css/buttons.dataTables.css" rel="stylesheet"
        type="text/css" />
    <link href="{{ url('') }}/assets/libs/Datatables/Responsive-2.4.0/css/responsive.bootstrap.css" rel="stylesheet"
        type="text/css" />
    <!-- Toastr Notifications-->
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <!-- Select2-->
    <link rel="stylesheet" href="{{ url('') }}/assets/libs/adminlte/select2/css/4.1.0/select2.min.css">
    <style>
        div.dataTables_wrapper {
            width: 100%;
        }
    </style>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">List Line Approval</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Line Approval</a></li>
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
                    @can('hrd.master.line-approval.create')
                        <a href="{{ route('line-approval.form') }}" class="btn btn-primary btn-label waves-effect waves-light"
                            data-text="Create Line Approval"><i
                                class="ri-add-circle-line label-icon align-middle fs-16 me-2"></i>Create Line Approval</a>
                    @endcan
                    <div class="flex-shrink-0 ms-auto">
                        @can('hrd.master.line-approval.export_xlsx')
                        <a href="{{ route('line-approval.export_xlsx') }}" class="btn btn-success btn-label waves-effect waves-light"
                            data-text="Export"><i
                                class="ri-file-excel-2-line label-icon align-middle fs-16 me-2"></i>Export</a>
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-striped bordered display nowrap" style="width:100%" id="table_lineapproval">
                        <thead>
                            <tr>
                                <th scope="col" style="text-align:center">No</th>
                                <th scope="col" style="text-align:center">Group Name</th>
                                <th scope="col" style="text-align:center">Approval Type</th>
                                <th scope="col" style="text-align:center">Department</th>
                                <th scope="col" style="text-align:center">Area</th>
                                <th scope="col" style="text-align:center">Organization</th>
                                <th scope="col" style="text-align:center">Employee</th>
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
    <!--Modal staticbackdrop-->
    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body text-center p-5">
                    <img src="{{ url('') }}/assets/images/loading.gif" style="width:120px;height:120px">
                    <div class="mt-4">
                        <h4 class="mb-3">Please wait...</h4>
                        <h4 class="mb-3">Do not leave this page</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--Modal delete-->
    <div id="modal" class="modal fade flip" tabindex="-1" aria-labelledby="flipModalLabel" aria-hidden="true"
        style="display: none;">
        <div class="modal-dialog modal-dialog-top">
            <div class="modal-content">
                <form class="form" action="{{ route('line-approval.destroy') }}" method="post">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Delete Line Approval</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center p-5">
                        <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop"
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
    <script src="{{ url('') }}/assets/libs/Datatables/DataTables-1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="{{ url('') }}/assets/libs/Datatables/DataTables-1.13.1/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ url('') }}/assets/libs/Datatables/Responsive-2.4.0/js/dataTables.responsive.min.js"></script>
    <script src="{{ url('') }}/assets/js/pages/datatables.init.js"></script>
    <!-- Toastr Notifications-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <!-- Select2 -->
    <script src="{{ url('') }}/assets/libs/adminlte/select2/js/select2.min.js"></script>
    <script src="{{ url('') }}/assets/js/pages/select2.init.js"></script>
@endsection

@section('javascript')
    <script>
        $("#btn-save").click(function() {
            $("#form").submit(function() {
                $('#staticBackdrop').modal('show', true);
            });
        });
        $(function() {
            $('#tipe').select2({
                dropdownParent: $('#modal .modal-content')
            });
        });
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#table_lineapproval').DataTable({
                stateSave: false,
                responsive: false,
                autoWidth: false,
                processing: true,
                serverSide: false,
                scrollX: true,
                ajax: "{{ route('line-approval.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        className: "text-center"
                    },
                    {
                        data: 'group_name',
                        name: 'group_name',
                        className: "text-center",
                        defaultContent: '-'
                    },
                    {
                        data: 'approval_type',
                        name: 'approval_type',
                        className: "text-center"
                    },
                    {
                        data: 'department_id',
                        name: 'department_id',
                        className: "text-center"
                    },
                    {
                        data: 'area_id',
                        name: 'area_id',
                        className: "text-center"
                    },
                    {
                        data: 'building_id',
                        name: 'building_id',
                        className: "text-center"
                    },
                    {
                        data: 'total_employees',
                        name: 'total_employees',
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
            $(document).on("click", ".delete-btn", function() {
                var lapproveId = $(this).data("id");
                $("input[name='id']").val(lapproveId);
                $("#modal").modal("show");
            });
            $.fn.dataTable.ext.errMode = 'none';
            $(document).on('error.dt', function(e, settings, techNote, message) {
                console.error('DataTables Error:', message);
            });
            const sidebarToggleBtn = $('#topnav-hamburger-icon'); 
            if (sidebarToggleBtn.length) {
                sidebarToggleBtn.on('click', function() {
                    setTimeout(function() {
                        $('#table_lineapproval').DataTable().columns.adjust().draw();
                    }, 300);
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
    <script>
        @if (Session::has('success'))
            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-bottom-right"
            }
            toastr.success("{{ session('success') }}");
        @endif
        @if (Session::has('error'))
            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-bottom-right"
            }
            toastr.error("{{ session('error') }}");
        @endif
    </script>
@endsection
