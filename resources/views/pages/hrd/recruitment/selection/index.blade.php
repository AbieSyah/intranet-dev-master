@extends('layouts.master')

@section('link')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="{{ url('') }}/assets/libs/Datatables/DataTables-1.13.1/css/dataTables.bootstrap5.css" rel="stylesheet"
        type="text/css" />

    <link href="{{ url('') }}/assets/libs/Datatables/Responsive-2.4.0/css/responsive.bootstrap.css" rel="stylesheet"
        type="text/css" />
    <link rel="stylesheet" href="{{ url('') }}/assets/libs/adminlte/select2/css/4.1.0/select2.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <style>
        div.dataTables_wrapper {
            width: 100%;
        }
        .hidden-column {
            display: none;
        }

        /* Custom styles for the timeline */
        .timeline {
            position: relative;
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .timeline-item {
            width: 100%;
            position: relative;
            padding-left: 50px;
            padding-top: 6px;
            padding-bottom: 6px;
        }
        .timeline-item:last-child {
            padding-bottom: 0;
        }
        .timeline-marker {
            position: absolute;
            top: 15px;
            left: 14px;
            width: 15px;
            height: 15px;
            border-radius: 50%;
            background-color: #ddd;
            border: 2px solid #ddd;
        }
        .timeline-item.completed .timeline-marker {
            background-color: #0ab39c;
            border-color: #0ab39c;
        }
        .timeline-item.completed .timeline-line {
            background-color: #0ab39c;
        }
        .timeline-line {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            left: 20px;
            width: 2px;
            background-color: #ddd;
        }
        .timeline::after {
            background: none !important;
        }
        .btn-with-badge {
            overflow: visible;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">List Selection</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Recruitment</a></li>
                        <li class="breadcrumb-item active">Selection</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <select class="form-control js-example-basic-single select2" name="status" id="status" required>
                                <option value="ALL" selected>ALL SELECTION</option>
                                <option value="0">DRAFT</option>
                                <option value="1">RELEASE</option>
                                <option value="2">DONE</option>
                            </select>
                        </div>
                        <div class="col-md-8">
                            <a href="{{ route('selection.form') }}"
                                class="float-end btn btn-primary btn-label waves-effect waves-light"
                                data-text="Add Selection"><i class="ri-add-circle-line label-icon align-middle fs-16 me-2">
                                </i>Add Selection</a>
                            <button type="button" name="filter" id="filter"
                                class="btn btn-soft-primary waves-effect waves-light btn-sm me-1"><i
                                    class="ri-filter-2-line me-1 align-bottom"></i> Filters</button>
                            <button type="button" name="reset" id="reset"
                                class="btn btn-soft-danger waves-effect waves-light btn-sm"><i
                                    class="ri-refresh-line me-1 align-bottom"></i> Reset</button>
                            @can('hrd.selection.delete')
                                <button id="multi-delete-btn" type="button" title="Delete"
                                    class="btn btn-danger btn-sm waves-effect waves-light position-relative btn-with-badge ms-1"
                                    style="display: none">
                                    <i class="ri-delete-bin-line"></i>
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary">
                                        <span id="delete-count">0</span>
                                        <span class="visually-hidden">delete selected</span>
                                    </span>
                                </button>
                            @endcan
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-striped bordered display nowrap" style="width:100%" id="table_selection">
                        <thead>
                            <tr>
                                <th style="text-align:center"><input type="checkbox" id="checkAll"></th>
                                <th scope="col" class="hidden-column" style="display:none">Created At</th>
                                <th scope="col" style="text-align:center">Action</th>
                                <th scope="col" style="text-align:center">Status</th>
                                <th scope="col">Requisition</th>
                                <th scope="col">Selection</th>
                                <th scope="col">Noted</th>
                                <th scope="col">Participant</th>
                                <th scope="col">Schedule</th>
                                <th scope="col">Location</th>
                                <th scope="col">Passed</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @can('hrd.selection.delete')
        <div id="deleteConfirmationModal" class="modal fade flip" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel"
            aria-hidden="true" style="display: none;">
            <div class="modal-dialog modal-dialog-top">
                <div class="modal-content">
                    <form class="form" id="universalDeleteForm" action="{{ route('selection.delete') }}"
                        method="post">
                        @csrf
                        @method('DELETE')
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteConfirmationModalLabel">Delete Selection</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center p-5">
                            <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop"
                                style="width:120px;height:120px"></lord-icon>
                            <p class="text-muted" id="deleteMessage">Are you sure you want to delete this selection?</p>
                            <div id="delete-id-container"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-danger">Yes, Delete</button>
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">No</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan
    <!--Modal staticbackdrop-->
    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
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
@endsection

@section('script')
    <script src="{{ url('') }}/assets/libs/Datatables/DataTables-1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="{{ url('') }}/assets/libs/Datatables/DataTables-1.13.1/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ url('') }}/assets/libs/Datatables/Responsive-2.4.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="{{ url('') }}/assets/libs/adminlte/select2/js/4.1.0/select2.min.js"></script>
    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
@endsection

@section('javascript')
    <script type="text/javascript">
        $(document).ready(function() {
            $('.select2').select2();
            let orderColumnIndex = 1;
            let selectedIds = [];
            $(document).on('change', '.row-checkbox', function() {
                const id = $(this).val();
                if (this.checked) {
                    if (!selectedIds.includes(id)) {
                        selectedIds.push(id);
                    }
                } else {
                    selectedIds = selectedIds.filter(val => val !== id);
                }
                updateMultiButtons();
                checkAllStatus();
            });

            $('#checkAll').on('click', function() {
                if (!$(this).is(':disabled')) {
                    const isChecked = this.checked;
                    const table = $('#table_selection').DataTable();
                    table.rows({ page: 'current', search: 'applied' }).nodes().to$().each(function() {
                        const checkbox = $(this).find('.row-checkbox');
                        const id = checkbox.val();
                        if (!checkbox.is(':disabled')) {
                            checkbox.prop('checked', isChecked);
                            if (isChecked) {
                                if (!selectedIds.includes(id)) {
                                    selectedIds.push(id);
                                }
                            } else {
                                selectedIds = selectedIds.filter(val => val !== id); 
                            }
                        }
                    });
                    updateMultiButtons();
                }
            });

            function checkAllStatus() {
                const dt = $('#table_selection').DataTable();
                const currentRows = dt.rows({ page: 'current', search: 'applied' }).nodes().to$();
                const allCheckboxes = currentRows.find('.row-checkbox');
                const enabledCheckboxes = currentRows.find('.row-checkbox:not(:disabled)');
                const checkedCheckboxes = enabledCheckboxes.filter(':checked');
                const checkAll = $('#checkAll');
                if (allCheckboxes.length === 0 || enabledCheckboxes.length === 0) {
                    checkAll.prop('disabled', true);
                    checkAll.prop('checked', false);
                } else {
                    checkAll.prop('disabled', false);
                    const isAllChecked = (enabledCheckboxes.length > 0) && (enabledCheckboxes.length === checkedCheckboxes.length);
                    checkAll.prop('checked', isAllChecked);
                }
            }

            function updateMultiButtons() {
                const count = selectedIds.length;
                $('#multi-delete-btn').hide();
                if (count > 0) {
                    @can('hrd.selection.delete')
                        $('#multi-delete-btn').show();
                        $('#delete-count').text(count);
                    @endcan
                }
            }

            var table_selection = $('#table_selection').DataTable({
                destroy: true,
                stateSave: true,
                responsive: false,
                autoWidth: false,
                processing: true,
                serverSide: false,
                scrollX: true,
                order: [
                    [orderColumnIndex, 'desc']
                ],
                ajax: {
                    url: "{{ route('selection.index') }}",
                    type: 'GET',
                    data: function (d) {
                        d.status = $('#status').val();
                    },
                    dataSrc: "data",
                    error: function(xhr, error, thrown) {
                        console.error("DataTables AJAX Error:", xhr.responseText);
                    }
                },
                columns: [
                    {data: 'id',name: 'id',className: "text-center",orderable: false,searchable: false,defaultContent: '-'},
                    {data: 'created_at',name: 'created_at',className: 'hidden-column',visible: false,searchable: false,},
                    {data: 'action',name: 'action',className: "text-center",orderable: false,searchable: false,defaultContent: '-'},
                    {data: 'status',name: 'status',className: "text-center",orderable: false,searchable: false,defaultContent: '-'},
                    {data: 'requisition',name: 'requisition',defaultContent: '-'}, 
                    { 
                        data: 'selection', 
                        name: 'selection',
                        render: function(data, type, row) {
                            if (data && data.includes('(Last)')) {
                                return data.replace('(Last)', '<span class="fw-bold text-success">(Last)</span>');
                            }
                            return data;
                        }
                    }, 
                    {data: 'noted',name: 'noted',defaultContent: '-'},
                    {data: 'participant',name: 'participant',defaultContent: '-'},
                    {data: 'schedule',name: 'schedule',defaultContent: '-'},
                    {data: 'location',name: 'location',defaultContent: '-'},
                    {data: 'passed',name: 'passed',defaultContent: '-'},
                ],
                "drawCallback": function(settings) {
                    $('[data-toggle="tooltip"]').tooltip();
                    checkAllStatus();
                    const api = this.api();
                    api.rows({ page: 'current' }).nodes().to$().each(function() {
                        const checkbox = $(this).find('.row-checkbox');
                        const id = checkbox.val();
                        if (selectedIds.includes(id)) {
                            checkbox.prop('checked', true);
                        }
                    });
                }
            });
            $('#checkAll').prop('checked', false);

            if (!table_selection.state.loaded()) {
                $('#status').val('ALL').trigger('change');
            }

            $('#filter').on('click', function() {
                table_selection.ajax.reload();
                $('#checkAll').prop('checked', false);
                selectedIds = [];
                updateMultiButtons();
            });

            $('#reset').on('click', function() {
                $('#status').val('ALL').trigger('change');
                table_selection.state.clear();
                table_selection.order([orderColumnIndex, 'desc']).draw();
                table_selection.ajax.reload();
                $('#checkAll').prop('checked', false);
                selectedIds = [];
                updateMultiButtons();
            });

            const sidebarToggleBtn = $('#topnav-hamburger-icon'); 
            if (sidebarToggleBtn.length) {
                sidebarToggleBtn.on('click', function() {
                    setTimeout(function() {
                        $('#table_selection').DataTable().columns.adjust().draw();
                    }, 300);
                });
            }

            $.fn.dataTable.ext.errMode = 'none';
            $(document).on('error.dt', function(e, settings, techNote, message) {
                console.error('DataTables Error:', message);
            });

            @can('hrd.selection.delete')
                $('#multi-delete-btn').click(function() {
                    const message =
                        `Are you sure you want to Delete ${selectedIds.length} selected selections?`;
                    $('#deleteMessage').text(message);
                    const idContainer = $('#delete-id-container');
                    idContainer.empty();
                    selectedIds.forEach(id => {
                        idContainer.append(`<input type="hidden" name="ids[]" value="${id}">`);
                    });
                    $('#deleteConfirmationModal').modal('show');
                });
                $(document).on("click", ".delete-btn", function() {
                    var erId = $(this).data("id");
                    $('#deleteMessage').text('Are you sure you want to Delete this selection?');
                    const idContainer = $('#delete-id-container');
                    idContainer.empty();
                    idContainer.append(`<input type="hidden" name="ids[]" value="${erId}">`);
                    $('#deleteConfirmationModal').modal("show");
                });
                $("#universalDeleteForm").on('submit', function() {
                    $('#deleteConfirmationModal').modal('hide');
                    $('#staticBackdrop').modal('show');
                });
            @endcan
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