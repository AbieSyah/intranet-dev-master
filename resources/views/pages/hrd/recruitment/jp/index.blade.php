@extends('layouts.master')
@section('link')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ url('') }}/assets/libs/Datatables/DataTables-1.13.1/css/dataTables.bootstrap5.css" rel="stylesheet"
        type="text/css" />
    <link href="{{ url('') }}/assets/libs/Datatables/Buttons-2.3.3/css/buttons.dataTables.css" rel="stylesheet"
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
        .btn-with-badge {
            overflow: visible;
        }
    </style>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">List Job Posting</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Recruitment</a></li>
                        <li class="breadcrumb-item active">Job Posting</li>
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
                        <div class="col-md-2">
                            <div class="form-group">
                                @php
                                    $statuses = [
                                        'ALL',
                                        'DRAFT',
                                        'PUBLISH',
                                        'REVISE',
                                        'DONE',
                                    ];
                                @endphp
                                <select class="form-control select2" id="form_status" name="form_status">
                                    @foreach ($statuses as $status)
                                        <option value="{{ $status }}" {{ $status === 'ALL' ? 'selected' : '' }}>
                                            {{ $status }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group">
                                <select class="form-control select2 js-example-basic-single" name="tahun" id="tahun" required>
                                    @foreach($years as $year)
                                        <option value="{{ $year }}" @if($year == date('Y')) selected @endif>{{ $year }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="button" name="filter" id="filter"
                                class="btn btn-soft-primary waves-effect waves-light btn-sm"><i
                                    class="ri-filter-2-line me-1 align-bottom"></i> Filters</button>
                            <button type="button" name="reset" id="reset"
                                class="btn btn-soft-danger waves-effect waves-light btn-sm"><i
                                    class="ri-refresh-line me-1 align-bottom"></i> Reset</button>
                        </div>
                        <div class="col-md-7">
                            <a href="{{ route('job-posting.form') }}"
                                class="float-end btn btn-primary btn-label waves-effect waves-light"
                                data-text="New Job Posting"><i class="ri-add-circle-line label-icon align-middle fs-16 me-2">
                                </i>New Job Posting</a>
                            <button id="multi-revise-btn" type="button" title="Revise"
                                class="float-end btn btn-danger btn-sm waves-effect waves-light position-relative btn-with-badge me-2"
                                style="display:none;">
                                <i class="ri-edit-2-line fs-16"></i>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning">
                                    <span id="revise-count">0</span>
                                    <span class="visually-hidden">revise selected</span>
                                </span>
                            </button>
                            <button id="multi-done-btn" type="button" title="Done"
                                class="float-end btn btn-success btn-sm waves-effect waves-light position-relative btn-with-badge me-2"
                                style="display:none;">
                                <i class="ri-check-line fs-16"></i>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning">
                                    <span id="done-count">0</span>
                                    <span class="visually-hidden">done selected</span>
                                </span>
                            </button>
                            @can('hrd.job-posting.delete')
                                <button id="multi-delete-btn" type="button" title="Delete"
                                    class="float-end btn btn-danger btn-sm waves-effect waves-light position-relative btn-with-badge me-2"
                                    style="display:none;">
                                    <i class="ri-delete-bin-line fs-16"></i>
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning">
                                        <span id="delete-count">0</span>
                                        <span class="visually-hidden">delete selected</span>
                                    </span>
                                </button>
                            @endcan
                            <button id="multi-publish-btn" type="button" title="Publish"
                                class="float-end btn btn-primary btn-sm waves-effect waves-light position-relative btn-with-badge me-2"
                                style="display:none;">
                                <i class="ri-global-line fs-16"></i>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning">
                                    <span id="publish-count">0</span>
                                    <span class="visually-hidden">publish selected</span>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-striped bordered display nowrap" style="width:100%" id="table_jp">
                        <thead>
                            <tr>
                                <th style="text-align:center"><input type="checkbox" id="checkAll"></th>
                                <th scope="col">No. Publish</th>
                                <th scope="col">Job Title</th>
                                <th scope="col">Needs</th>
                                <th scope="col">Period</th>
                                <th scope="col">Employee Status</th>
                                <th scope="col">Area</th>
                                <th scope="col" style="text-align:center">Status</th>
                                <th scope="col" style="text-align:center">Action</th>
                                <th scope="col" class="hidden-column" style="display:none">Created At</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @can('hrd.job-posting.delete')
        <div id="deleteConfirmationModal" class="modal fade flip" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel"
            aria-hidden="true" style="display: none;">
            <div class="modal-dialog modal-dialog-top">
                <div class="modal-content">
                    <form class="form" id="universalDeleteForm" action="{{ route('job-posting.destroy') }}"
                        method="post">
                        @csrf
                        @method('DELETE')
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteConfirmationModalLabel">Delete Job Posting</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center p-5">
                            <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop"
                                style="width:120px;height:120px"></lord-icon>
                            <p class="text-muted" id="deleteMessage">Are you sure you want to delete this job posting?</p>
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
    <div id="publishConfirmationModal" class="modal fade flip" tabindex="-1"
        aria-labelledby="publishConfirmationModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-dialog-top">
            <div class="modal-content">
                <form class="form" id="publishForm" action="{{ route('job-posting.publish-multiple') }}"
                    method="post">
                    @csrf
                    @method('POST')
                    <div class="modal-header">
                        <h5 class="modal-title" id="publishModalLabel">Publish Job Posting</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center p-5">
                        <p class="text-muted" id="publishMessage">Are you sure you want to publish this Job Posting?</p>
                        <div id="publish-id-container"></div> 
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="status" value="PUBLISH"
                            class="btn btn-success">Yes, Publish</button>
                        <button type="button" class="btn btn-light"
                            data-bs-dismiss="modal">No</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div id="reviseConfirmationModal" class="modal fade flip" tabindex="-1" aria-labelledby="reviseConfirmationModalLabel"
        aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-dialog-top">
            <div class="modal-content">
                <form class="form" id="reviseForm" action="{{ route('job-posting.update-status') }}" method="post">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="REVISE">
                    <div id="revise-id-container"></div>
                    <div class="modal-header">
                        <h5 class="modal-title" id="reviseConfirmationModalLabel">Revise Job Posting</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center p-5">
                        <p class="text-muted mt-3">Are you sure you want to Revise this Job Posting?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-danger">Yes, Revise</button>
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">No</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div id="doneConfirmationModal" class="modal fade flip" tabindex="-1" aria-labelledby="doneConfirmationModalLabel"
        aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-dialog-top">
            <div class="modal-content">
                <form class="form" id="doneForm" action="{{ route('job-posting.update-status') }}" method="post">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="DONE">
                    <div id="done-id-container"></div>
                    <div class="modal-header">
                        <h5 class="modal-title" id="doneConfirmationModalLabel">Closing Job Posting</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center p-5">
                        <p class="text-muted mt-3">Are you sure you want to Closing this Job Posting?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Yes, Closing</button>
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">No</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
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
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="{{ url('') }}/assets/js/pages/datatables.init.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ url('') }}/assets/libs/adminlte/select2/js/4.1.0/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
@endsection

@section('javascript')
    <script type="text/javascript">
        $(document).ready(function() {
            $('.select2').select2();
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
                    const dt = $('#table_jp').DataTable();
                    dt.rows({ page: 'current', search: 'applied' }).nodes().to$().each(function() {
                        const checkbox = $(this).find('.row-checkbox');
                        const id = checkbox.val();
                        if (!checkbox.is(':disabled')) {
                            checkbox.prop('checked', isChecked);
                            if (isChecked) {
                                if (!selectedIds.includes(id)) selectedIds.push(id);
                            } else {
                                selectedIds = selectedIds.filter(val => val !== id);
                            }
                        }
                    });
                    updateMultiButtons();
                }
            });

            function checkAllStatus() {
                const dt = $('#table_jp').DataTable();
                const enabledCheckboxes = dt.rows({ page: 'current', search: 'applied' }).nodes().to$().find('.row-checkbox:not(:disabled)');
                const totalEnabledCheckboxes = enabledCheckboxes.length;
                const totalCheckedCheckboxes = enabledCheckboxes.filter(':checked').length;
                const checkAll = $('#checkAll');
                if (totalEnabledCheckboxes === 0) {
                    checkAll.prop('checked', false);
                    checkAll.prop('disabled', true);
                } else {
                    checkAll.prop('disabled', false);
                    checkAll.prop('checked', totalEnabledCheckboxes > 0 && totalEnabledCheckboxes === totalCheckedCheckboxes);
                }
            }

            function updateMultiButtons() {
                const count = selectedIds.length;
                let allDraftOrRevise = true;
                let allPublish = true;
                let allDelete = true;
                $('#multi-delete-btn').hide();
                $('#multi-publish-btn').hide();
                $('#multi-revise-btn').hide();
                $('#multi-done-btn').hide();
                if (count > 0) {
                    const allData = $('#table_jp').DataTable().data().toArray();
                    for (let i = 0; i < allData.length; i++) {
                        const rowData = allData[i];
                        if (selectedIds.includes(rowData.id)) {
                            const statusHtml = rowData.status;
                            if (!statusHtml.includes('DRAFT') && !statusHtml.includes('REVISE')) {
                                allDraftOrRevise = false;
                            }
                            if (!statusHtml.includes('PUBLISH')) {
                                allPublish = false;
                            }
                            if (statusHtml.includes('PUBLISH')) {
                                allDelete = false;
                            }
                        }
                        if (!allDraftOrRevise && !allPublish && !allDelete) {
                            break;
                        }
                    }
                    if (allDraftOrRevise) {
                        $('#multi-publish-btn').show();
                        $('#publish-count').text(count);
                    }
                    if (allPublish) {
                        $('#multi-revise-btn').show();
                        $('#revise-count').text(count);
                        $('#multi-done-btn').show();
                        $('#done-count').text(count);
                    }
                    if (allDelete) {
                        $('#multi-delete-btn').show();
                        $('#delete-count').text(count);
                    }
                }
            }

            @can('hrd.job-posting.delete')
                $('#multi-delete-btn').click(function() {
                    const message =
                        `Are you sure you want to delete ${selectedIds.length} selected job posting?`;
                    $('#deleteMessage').text(message);
                    const idContainer = $('#delete-id-container');
                    idContainer.empty();
                    selectedIds.forEach(id => {
                        idContainer.append(`<input type="hidden" name="ids[]" value="${id}">`);
                    });
                    $('#deleteConfirmationModal').modal('show');
                });
                $(document).on("click", ".delete-btn", function() {
                    var evalId = $(this).data("id");
                    $('#deleteMessage').text('Are you sure you want to delete this job posting?');
                    const idContainer = $('#delete-id-container');
                    idContainer.empty();
                    idContainer.append(`<input type="hidden" name="ids[]" value="${evalId}">`);
                    $('#deleteConfirmationModal').modal("show");
                });
                $("#universalDeleteForm").on('submit', function() {
                    $('#deleteConfirmationModal').modal('hide');
                    $('#staticBackdrop').modal('show');
                });
            @endcan

            // Publish
            $('#multi-publish-btn').click(function() {
                const message = `Are you sure you want to Publish ${selectedIds.length} selected Job Postings?`;
                $('#publishMessage').text(message);
                const idContainer = $('#publish-id-container');
                idContainer.empty();
                selectedIds.forEach(id => {
                    idContainer.append(`<input type="hidden" name="ids[]" value="${id}">`);
                });
                $('#publishConfirmationModal').modal('show');
            });

            // Revise
            $('#multi-revise-btn').click(function() {
                const message = `Are you sure you want to Revise ${selectedIds.length} selected Job Postings?`;
                const idContainer = $('#revise-id-container');
                idContainer.empty();
                selectedIds.forEach(id => {
                    idContainer.append(`<input type="hidden" name="ids[]" value="${id}">`);
                });
                $('#reviseConfirmationModal .modal-body p.text-muted').text(message);
                $('#reviseConfirmationModal').modal('show');
            });
            $(document).on("click", ".revise-btn", function() {
                var jobId = $(this).data("id");
                const idContainer = $('#revise-id-container');
                idContainer.empty();
                idContainer.append(`<input type="hidden" name="id" value="${jobId}">`);
                $('#reviseMessage').text('Are you sure you want to Revise this Job Posting?');
                $('#reviseConfirmationModal').modal("show");
            });

            // Done
            $('#multi-done-btn').click(function() {
                const message = `Are you sure you want to Closing ${selectedIds.length} selected Job Postings?`;
                const idContainer = $('#done-id-container');
                idContainer.empty();
                selectedIds.forEach(id => {
                    idContainer.append(`<input type="hidden" name="ids[]" value="${id}">`);
                });
                $('#doneConfirmationModal .modal-body p.text-muted').text(message);
                $('#doneConfirmationModal').modal('show');
            });
            $(document).on("click", ".done-btn", function() {
                var jobId = $(this).data("id");
                const idContainer = $('#done-id-container');
                idContainer.empty();
                idContainer.append(`<input type="hidden" name="id" value="${jobId}">`);
                $('#doneConfirmationModal .modal-body p.text-muted').text('Are you sure you want to Closing this Job Posting?');
                $('#doneConfirmationModal').modal("show");
            });

            $('#filter').click(function() {
                var form_status = $('#form_status').val();
                load_data(form_status);
                $('#checkAll').prop('checked', false);
                selectedIds = [];
                updateMultiButtons();
            });

            $('#reset').click(function() {
                $('#form_status').val('ALL').trigger('change');
                localStorage.removeItem('form_status');
                var currentYear = '{{ date('Y') }}';
                var optionExists = $('#tahun option[value="' + currentYear + '"]').length > 0;
                if (optionExists) {
                    $('#tahun').val(currentYear).trigger('change');
                }
                const table = $('#table_jp').DataTable();
                table.state.clear();
                load_data('ALL');
                $('#checkAll').prop('checked', false);
                selectedIds = [];
                updateMultiButtons();
            });

            let savedStatus = localStorage.getItem('form_status') || 'ALL';
            $('#form_status').val(savedStatus).trigger('change');
            load_data(savedStatus);

            const sidebarToggleBtn = $('#topnav-hamburger-icon'); 
            if (sidebarToggleBtn.length) {
                sidebarToggleBtn.on('click', function() {
                    setTimeout(function() {
                        $('#table_jp').DataTable().columns.adjust().draw();
                    }, 300);
                });
            }

            function load_data(form_status = '') {
                localStorage.setItem('form_status', form_status);
                const table = $('#table_jp').DataTable({
                    destroy: true,
                    stateSave: true,
                    responsive: false,
                    autoWidth: false,
                    processing: true,
                    serverSide: false,
                    scrollX: true,
                    order: [
                        [9, 'desc']
                    ],
                    ajax: {
                        url: "{{ route('job-posting.index') }}",
                        data: {
                            form_status: form_status,
                            tahun: $('#tahun').val()
                        }
                    },
                    columns: [
                        {
                            data: 'id',
                            name: 'id',
                            orderable: false,
                            searchable: false,
                            className: "text-center",
                            render: function(data, type, row, meta) {
                                let isChecked = selectedIds.includes(data) ? 'checked' : '';
                                return `<input type="checkbox" class="row-checkbox" value="${data}" ${isChecked}>`;
                            }
                        },
                        {
                            data: 'publish_id',
                            name: 'publish_id',
                            defaultContent: '-'
                        },
                        {
                            data: 'title',
                            name: 'title',
                            defaultContent: '-'
                        },
                        {
                            data: 'needs',
                            name: 'needs',
                            defaultContent: '-'
                        },
                        {
                            data: 'period',
                            name: 'period',
                            defaultContent: '-'
                        },
                        {
                            data: 'employee_status',
                            name: 'employee_status',
                            defaultContent: '-'
                        },
                        {
                            data: 'area',
                            name: 'area',
                            defaultContent: '-'
                        },
                        {
                            data: 'status',
                            name: 'status',
                            className: "text-center",
                            orderable: false,
                            searchable: false,
                            defaultContent: '-'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            className: "text-center",
                            orderable: false,
                            searchable: false,
                            defaultContent: '-'
                        },
                        {
                            data: 'created_at',
                            name: 'created_at',
                            className: 'hidden-column',
                            visible: false
                        },
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
            }
            $.fn.dataTable.ext.errMode = 'none';
            $(document).on('error.dt', function(e, settings, techNote, message) {
                console.error('DataTables Error:', message);
            });
        });
    </script>
    @if (session('swal_warning'))
        <script>
            Swal.fire({
                icon: 'warning',
                title: 'Access Denied',
                text: '{{ session('swal_warning') }}',
                confirmButtonText: 'OK'
            });
        </script>
    @endif
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
