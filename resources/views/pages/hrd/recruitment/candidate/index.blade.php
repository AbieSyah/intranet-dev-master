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
        .timeline-item.failed .timeline-marker {
            background-color: #f06548;
            border-color: #f06548;
        }
        .timeline-item.failed .timeline-line {
            background-color: #f06548;
        }
        .timeline-item.completed h6 i {
            color: #0ab39c;
            font-size: 14px;
        }
        .timeline-item.failed h6 i {
            color: #f06548;
            font-size: 14px;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">List Candidate</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Recruitment</a></li>
                        <li class="breadcrumb-item active">Candidate</li>
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
                            <select class="form-control js-example-basic-single select2" name="posting_id" id="posting_id" required>
                                <option value="ALL" selected>ALL CANDIDATE</option>
                                @foreach($postings as $posting)
                                    <option value="{{ $posting->id }}">{{ $posting->title }} ({{ $posting->publish_id }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-8">
                            <button type="button" name="filter" id="filter"
                                class="btn btn-soft-primary waves-effect waves-light btn-sm me-1"><i
                                    class="ri-filter-2-line me-1 align-bottom"></i> Filters</button>
                            <button type="button" name="reset" id="reset"
                                class="btn btn-soft-danger waves-effect waves-light btn-sm"><i
                                    class="ri-refresh-line me-1 align-bottom"></i> Reset</button>
                            @can('hrd.candidate.delete')
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
                    <table class="table table-striped bordered display nowrap" style="width:100%" id="table_candidate">
                        <thead>
                            <tr>
                                <th style="text-align:center"><input type="checkbox" id="checkAll"></th>
                                <th scope="col" class="hidden-column" style="display:none">Created At</th>
                                <th scope="col" style="text-align:center">Action</th>
                                <th scope="col">Job</th>
                                <th scope="col">Name</th>
                                <th scope="col">Age</th>
                                <th scope="col">Education</th>
                                <th scope="col">Experiences</th>
                                <th scope="col">Position</th>
                                <th scope="col">Company</th>
                                <th scope="col">Skill / Ability</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @can('hrd.candidate.delete')
        <div id="deleteConfirmationModal" class="modal fade flip" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel"
            aria-hidden="true" style="display: none;">
            <div class="modal-dialog modal-dialog-top">
                <div class="modal-content">
                    <form class="form" id="universalDeleteForm" action="{{ route('candidate.delete') }}"
                        method="post">
                        @csrf
                        @method('DELETE')
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteConfirmationModalLabel">Delete Candidate</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center p-5">
                            <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop"
                                style="width:120px;height:120px"></lord-icon>
                            <p class="text-muted" id="deleteMessage">Are you sure you want to delete this candidate?</p>
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
    <div class="modal fade" id="trackingModal" tabindex="-1" aria-labelledby="trackingModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="trackingModalLabel">Selection Step</h5>
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
        const selectionStepsUrl = '{{ route('selection.steps', ':id') }}';
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
                    const table = $('#table_candidate').DataTable();
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
                const table = $('#table_candidate').DataTable();
                const enabledCheckboxes = table.rows({ page: 'current', search: 'applied' }).nodes().to$().find('.row-checkbox:not(:disabled)');
                const totalEnabledCheckboxes = enabledCheckboxes.length;
                const totalCheckedCheckboxes = enabledCheckboxes.filter(':checked').length;
                const checkAll = $('#checkAll');
                checkAll.prop('checked', totalEnabledCheckboxes > 0 && totalEnabledCheckboxes === totalCheckedCheckboxes);
                if (totalEnabledCheckboxes === 0) {
                    checkAll.prop('disabled', true);
                } else {
                    checkAll.prop('disabled', false);
                }
            }

            function updateMultiButtons() {
                const count = selectedIds.length;
                $('#multi-delete-btn').hide();
                if (count > 0) {
                    @can('hrd.candidate.delete')
                        $('#multi-delete-btn').show();
                        $('#delete-count').text(count);
                    @endcan
                }
            }

            var table_candidate = $('#table_candidate').DataTable({
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
                    url: "{{ route('candidate.index') }}",
                    type: 'GET',
                    data: function (d) {
                        d.posting_id = $('#posting_id').val();
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
                    {data: 'job',name: 'job',defaultContent: '-'}, 
                    {data: 'fullname',name: 'fullname',defaultContent: '-'}, 
                    {data: 'age',name: 'age',defaultContent: '-'},
                    {data: 'edu',name: 'edu',defaultContent: '-'},
                    {data: 'years_exp',name: 'years_exp',defaultContent: '-'},
                    {data: 'position',name: 'position',defaultContent: '-'},
                    {data: 'company',name: 'company',defaultContent: '-'},
                    {data: 'skill',name: 'skill',defaultContent: '-'}
                ],
                "drawCallback": function(settings) {
                    $('[data-toggle="tooltip"]').tooltip();
                    checkAllStatus();
                    const api = this.api();
                    api.rows({ page: 'current' }).nodes().to$().each(function() {
                        const checkbox = $(this).find('.row-checkbox');
                        const encryptedId = checkbox.val();
                        if (selectedIds.includes(encryptedId)) {
                            checkbox.prop('checked', true);
                        }
                    });
                }
            });
            $('#checkAll').prop('checked', false);

            if (!table_candidate.state.loaded()) {
                $('#posting_id').val('ALL').trigger('change');
            }

            $('#filter').on('click', function() {
                table_candidate.ajax.reload();
                $('#checkAll').prop('checked', false);
                selectedIds = [];
                updateMultiButtons();
            });

            $('#reset').on('click', function() {
                $('#posting_id').val('ALL').trigger('change');
                table_candidate.state.clear();
                table_candidate.order([orderColumnIndex, 'desc']).draw();
                table_candidate.ajax.reload();
                $('#checkAll').prop('checked', false);
                selectedIds = [];
                updateMultiButtons();
            });

            const sidebarToggleBtn = $('#topnav-hamburger-icon'); 
            if (sidebarToggleBtn.length) {
                sidebarToggleBtn.on('click', function() {
                    setTimeout(function() {
                        $('#table_candidate').DataTable().columns.adjust().draw();
                    }, 300);
                });
            }

            $.fn.dataTable.ext.errMode = 'none';
            $(document).on('error.dt', function(e, settings, techNote, message) {
                console.error('DataTables Error:', message);
            });

            @can('hrd.candidate.delete')
                $('#multi-delete-btn').click(function() {
                    const message =
                        `Are you sure you want to Delete ${selectedIds.length} selected candidates?`;
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
                    $('#deleteMessage').text('Are you sure you want to Delete this candidate?');
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

            // modal view step
            $(document).on('click', '.btn-view-steps', function(e) {
                e.preventDefault();
                var encryptedId = $(this).data('id');
                const url = selectionStepsUrl.replace(':id', encryptedId);
                $('#trackingModalLabel').text('Selection Steps');
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
                            var cssClass = step.status_class ? step.status_class : '';
                            var statusIcon = '';
                            if (cssClass === 'completed') {
                                statusIcon = '<i class="ri-checkbox-circle-fill ms-1 align-middle"></i>'; 
                            } else if (cssClass === 'failed') {
                                statusIcon = '<i class="ri-close-circle-fill ms-1 align-middle"></i>';
                            }
                            var dateDisplay = step.date ? `<small class="text-muted">${step.date}</small>` : '<small class="text-muted">-</small>';
                            timelineHtml += `
                                <div class="timeline-item ${cssClass}">
                                    <span class="timeline-line"></span>
                                    <div class="timeline-marker"></div>
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <h6 class="mb-0 d-flex align-items-center">
                                                ${step.name} 
                                                ${statusIcon}
                                            </h6>
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
                        $('#trackingModal .modal-body .timeline').html('<div class="alert alert-danger">Failed to load data. Please try again.</div>');
                    }
                });
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