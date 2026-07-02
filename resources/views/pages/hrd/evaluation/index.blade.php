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
    </style>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">List Evaluation</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Evaluation</a>
                        </li>
                        <li class="breadcrumb-item active">On Process</li>
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
                                        'RELEASE',
                                        'REVISE',
                                        'REJECT',
                                        '1st Evaluator',
                                        '2nd Evaluator',
                                        '3rd Evaluator',
                                        'HRD Approved',
                                        'Prodir',
                                        'Presdir',
                                    ];
                                @endphp
                                <select class="form-control select2" id="form_status" name="form_status">
                                    @foreach ($statuses as $status)
                                        @php
                                            $displayText = ($status === 'RELEASE') ? 'HRD' : $status;
                                        @endphp
                                        <option value="{{ $status }}" {{ $status === 'ALL' ? 'selected' : '' }}>
                                            {{ $displayText }}
                                        </option>
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
                        <div class="col-md-8">
                            @can('hrd.evaluation.read')
                                <a href="{{ route('evaluation.form') }}"
                                    class="float-end btn btn-primary btn-label waves-effect waves-light"
                                    data-text="Add Single"><i class="ri-add-circle-line label-icon align-middle fs-16 me-2">
                                    </i>Add Single</a>
                                <a href="{{ route('evaluation.create-multiple') }}"
                                    class="float-end btn btn-primary btn-label waves-effect waves-light me-2"
                                    data-text="Add Multiple"><i class="ri-add-circle-line label-icon align-middle fs-16 me-2">
                                    </i>Add Multiple</a>
                                <button id="multi-release-btn" type="button" title="Release"
                                    class="float-end btn btn-success btn-sm waves-effect waves-light position-relative btn-with-badge me-2"
                                    style="display:none;">
                                    <i class="ri-check-line fs-16"></i>
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning">
                                        <span id="release-count">0</span>
                                        <span class="visually-hidden">release selected</span>
                                    </span>
                                </button>
                                <button id="multi-edit-btn" type="button" title="Edit"
                                    class="float-end btn btn-warning btn-sm waves-effect waves-light position-relative btn-with-badge me-2"
                                    style="display:none;">
                                    <i class="ri-pencil-line fs-16"></i>
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                        <span id="edit-count">0</span>
                                        <span class="visually-hidden">edit selected</span>
                                    </span>
                                </button>
                                @can('hrd.evaluation.delete')
                                    <button id="multi-delete-btn" type="button" title="Delete"
                                        class="float-end btn btn-danger btn-sm waves-effect waves-light position-relative btn-with-badge me-2"
                                        style="display:none;">
                                        <i class="ri-delete-bin-line fs-16"></i>
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-success">
                                            <span id="delete-count">0</span>
                                            <span class="visually-hidden">delete selected</span>
                                        </span>
                                    </button>
                                @endcan
                            @endcan
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-striped bordered display nowrap" style="width:100%" id="table_evaluation">
                        <thead>
                            <tr>
                                <th style="text-align:center"><input type="checkbox" id="checkAll"></th>
                                <th scope="col" class="hidden-column" style="display:none">Created At</th>
                                <th scope="col" class="text-center">Action</th>
                                <th scope="col" class="text-center">Status</th>
                                <th scope="col">NIK</th>
                                <th scope="col">No. Evaluation</th>
                                <th scope="col">Name</th>
                                <th scope="col" class="text-center">Department</th>
                                <th scope="col" class="text-center">Section</th>
                                <th scope="col" class="text-center">Position</th>
                                <th scope="col" class="text-center">Organization</th>
                                <th scope="col">Start Date</th>
                                <th scope="col">End Date</th>
                                <th scope="col">Purpose</th>
                                <th scope="col">Score</th>
                                <th scope="col">Grade</th>
                                <th scope="col">Decision</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @can('hrd.evaluation.delete')
        <div id="deleteConfirmationModal" class="modal fade flip" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel"
            aria-hidden="true" style="display: none;">
            <div class="modal-dialog modal-dialog-top">
                <div class="modal-content">
                    <form class="form" id="universalDeleteForm" action="{{ route('evaluation.destroy') }}"
                        method="post">
                        @csrf
                        @method('DELETE')
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteConfirmationModalLabel">Delete Evaluations</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center p-5">
                            <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop"
                                style="width:120px;height:120px"></lord-icon>
                            <p class="text-muted" id="deleteMessage">Are you sure you want to delete this evaluation?</p>
                            <div id="delete-id-container"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Yes, Delete</button>
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">No</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan
    <div id="releaseConfirmationModal" class="modal fade flip" tabindex="-1"
        aria-labelledby="releaseConfirmationModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-dialog-top">
            <div class="modal-content">
                <form class="form" id="releaseForm" action="{{ route('evaluation.release-multiple') }}"
                    method="post">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="releaseConfirmationModalLabel">Release Evaluations</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center p-5">
                        <p class="text-muted" id="releaseMessage">Are you sure you want to release these selected
                            evaluations?</p>
                        <div id="release-id-container"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Yes, Release</button>
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">No</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="trackingModal" tabindex="-1" aria-labelledby="trackingModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="trackingModalLabel">Evaluation Step</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="timeline">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="reasonModal" tabindex="-1" aria-labelledby="reasonModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reasonModalLabel">Decision Reason</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="decisionReasonContent" class="text-muted">Loading...</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
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
        const evaluationStepsUrl = '{{ route('evaluation.process.steps', ':id') }}';
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
                    const table = $('#table_evaluation').DataTable();
                    table.rows({ page: 'current', search: 'applied' }).nodes().to$().each(function() {
                        const rowData = table.row($(this)).data();
                        const id = rowData.id;
                        const checkbox = $(this).find('.row-checkbox');
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
                const totalEnabledCheckboxes = $('.row-checkbox:not(:disabled)').length;
                const totalCheckedCheckboxes = $('.row-checkbox:not(:disabled):checked').length;
                $('#checkAll').prop('checked', totalEnabledCheckboxes > 0 && totalEnabledCheckboxes === totalCheckedCheckboxes);
            }

            function updateMultiButtons() {
                const count = selectedIds.length;
                let allDraft = true;
                $('#multi-delete-btn').hide();
                $('#multi-release-btn').hide();
                $('#multi-edit-btn').hide();
                if (count > 1) {
                    $('#multi-delete-btn').show();
                    $('#delete-count').text(count);
                    $('#multi-edit-btn').show();
                    $('#edit-count').text(count);
                    const allData = $('#table_evaluation').DataTable().data().toArray();
                    for (let i = 0; i < allData.length; i++) {
                        const rowData = allData[i];
                        if (selectedIds.includes(rowData.id) && !rowData.status.includes('DRAFT')) {
                            allDraft = false;
                            break;
                        }
                    }
                    if (allDraft) {
                        $('#multi-release-btn').show();
                        $('#release-count').text(count);
                    }
                }
            }

            @can('hrd.evaluation.delete')
                $('#multi-delete-btn').click(function() {
                    const message =
                        `Are you sure you want to delete ${selectedIds.length} selected evaluations?`;
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
                    $('#deleteMessage').text('Are you sure you want to delete this evaluation?');
                    const idContainer = $('#delete-id-container');
                    idContainer.empty();
                    idContainer.append(`<input type="hidden" name="ids[]" value="${evalId}">`);
                    $('#deleteConfirmationModal').modal("show");
                });
            @endcan

            $('#multi-release-btn').click(function() {
                const message =
                    `Are you sure you want to release ${selectedIds.length} selected evaluations?`;
                $('#releaseMessage').text(message);
                const idContainer = $('#release-id-container');
                idContainer.empty();
                selectedIds.forEach(id => {
                    idContainer.append(`<input type="hidden" name="ids[]" value="${id}">`);
                });
                $('#releaseConfirmationModal').modal('show');
            });

            $('#multi-edit-btn').click(function() {
                const count = selectedIds.length;
                if (count === 0) {
                    Swal.fire({
                        title: 'No Items Selected',
                        text: 'Please select at least one item to edit.',
                        icon: 'info'
                    });
                    return;
                }
                Swal.fire({
                    title: 'Loading data...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading()
                    }
                });
                $.ajax({
                    url: "{{ route('evaluation.edit-multiple.token') }}",
                    method: 'POST',
                    data: {
                        ids: selectedIds,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        Swal.close();
                        if (response.token) {
                            let url = "{{ route('evaluation.edit-multiple', ['token' => ':token']) }}";
                            url = url.replace(':token', response.token);
                            window.location.href = url; 
                        } else {
                            Swal.fire('Error!', 'Failed to generate token.', 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.close();
                        Swal.fire('Error!', 'Failed to generate token. ' + xhr.responseJSON.message, 'error');
                    }
                });
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
                const table = $('#table_evaluation').DataTable();
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
                        $('#table_evaluation').DataTable().columns.adjust().draw();
                    }, 300);
                });
            }

            function load_data(form_status = '') {
                localStorage.setItem('form_status', form_status);
                const table = $('#table_evaluation').DataTable({
                    destroy: true,
                    stateSave: true,
                    responsive: false,
                    autoWidth: false,
                    processing: true,
                    serverSide: false,
                    scrollX: true,
                    order: [
                        [1, 'desc']
                    ],
                    dom: '<"row"<"col-12 mb-2"B><"col-12 d-flex justify-content-between"lf>>rtip',
                    buttons: [
                        {
                            extend: 'excel',
                            text: '<i class="ri-file-excel-2-line me-1"></i> Export',
                            action: function (e, dt, node, config) {
                                let currentStatus = $('#form_status').val() || 'ALL';
                                let encodedStatus = encodeURIComponent(currentStatus);
                                window.location.href = "{{ route('evaluation.process.export_xlsx') }}?form_status=" + encodedStatus;
                            }
                        }
                    ],
                    ajax: {
                        url: "{{ route('evaluation.index') }}",
                        data: {
                            form_status: form_status
                        }
                    },
                    columns: [
                        { data: 'id', name: 'id', orderable: false, searchable: false, className: "text-center",
                            render: function(data, type, row, meta) {
                                return `<input type="checkbox" class="row-checkbox" value="${data}">`;
                            }
                        },
                        { data: 'created_at', name: 'created_at', className: 'hidden-column', visible: false, defaultContent: '-' },
                        { data: 'action', name: 'action', className: "text-center", orderable: false, searchable: false, defaultContent: '-' },
                        { data: 'status', name: 'status', className: "text-center", orderable: false, searchable: false, defaultContent: '-' },
                        { data: 'nik', name: 'nik', defaultContent: '-' },
                        { data: 'release_id', name: 'release_id', defaultContent: '-' },
                        { data: 'name', name: 'name', defaultContent: '-' },
                        { data: 'department', name: 'department', defaultContent: '-' },
                        { data: 'section', name: 'section', defaultContent: '-' },
                        { data: 'position', name: 'position', defaultContent: '-' },
                        { data: 'building', name: 'building', className: "text-center", defaultContent: '-' },
                        { 
                            data: 'start', 
                            name: 'start',
                            render: function(data, type, row) {
                                if (type === 'sort' || type === 'type') {
                                    return data.timestamp || 0;
                                }
                                return data.display || '-';
                            }
                        },
                        { 
                            data: 'end', 
                            name: 'end',
                            render: function(data, type, row) {
                                if (type === 'sort' || type === 'type') {
                                    return data.timestamp || 0;
                                }
                                return data.display || '-';
                            }
                        },
                        { data: 'purpose', name: 'purpose', defaultContent: '-' },
                        { data: 'total_score', name: 'total_score', className: "text-center", defaultContent: '-' },
                        { data: 'grade', name: 'grade', className: "text-center", defaultContent: '-' },
                        { data: 'decision', name: 'decision', defaultContent: '-' },
                    ],
                    "drawCallback": function(settings) {
                        checkAllStatus();
                        $('[data-toggle="tooltip"]').tooltip();
                        this.api().rows({ page: 'current' }).nodes().to$().each(function() {
                            const rowData = table.row(this).data();
                            const id = rowData.id;
                            const checkbox = $(this).find('.row-checkbox');
                            if (selectedIds.includes(id)) {
                                checkbox.prop('checked', true);
                            } else {
                                checkbox.prop('checked', false);
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

            // modal view step
            $(document).on('click', '.btn-view-steps', function(e) {
                e.preventDefault();
                var encryptedId = $(this).data('id');
                const url = evaluationStepsUrl.replace(':id', encryptedId);
                $('#trackingModalLabel').text('Evaluation Steps');
                $('#trackingModal .modal-body .timeline').html(`
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    `);
                $('#trackingModal').modal('show');
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        var timelineHtml = '';
                        $.each(response.steps, function(index, step) {
                            var completedClass = step.completed ? 'completed' : '';
                            var dateDisplay = step.date ?
                                `<small class="text-muted">${step.date}</small>` :
                                '<small class="text-muted">-</small>';
                            timelineHtml += `
                                    <div class="timeline-item ${completedClass}">
                                        <span class="timeline-line"></span>
                                        <div class="timeline-marker"></div>
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <h6 class="mb-0">${step.name}${step.approval}</h6>
                                                ${dateDisplay}
                                            </div>
                                        </div>
                                    </div>
                                `;
                        });
                        $('#trackingModal .modal-body .timeline').html(timelineHtml);
                    },
                    error: function(xhr) {
                        console.error('Error fetching data:', xhr.responseText);
                        $('#trackingModal .modal-body .timeline').html(
                            '<div class="alert alert-danger">Failed to load data. Please try again.</div>'
                            );
                    }
                });
            });
        });
        const reasonUrl = '{{ route('evaluation.process.reason', ':id') }}';
        $(document).on('click', '.btn-reason', function(e) {
            e.preventDefault();
            var encryptedId = $(this).data('id');
            const url = reasonUrl.replace(':id', encryptedId);
            const modal = $('#reasonModal');
            const contentContainer = $('#decisionReasonContent');
            contentContainer.html(`
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            `);
            modal.modal('show');
            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    if (response.reason) {
                        contentContainer.html(response.reason);
                    } else {
                        contentContainer.text('Reason not available.');
                    }
                },
                error: function(xhr) {
                    contentContainer.html('<span class="text-danger">Failed to load reason.</span>');
                    console.error('AJAX Error:', xhr.responseText);
                }
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
