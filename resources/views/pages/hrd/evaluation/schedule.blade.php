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
                <h4 class="mb-sm-0">List Evaluation Schedule</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Evaluation</a></li>
                        <li class="breadcrumb-item active">Schedule</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- Info Employment -->
    <div class="row d-none" id="employment-info-alert">
        <div class="col-12">
            <div class="alert alert-primary alert-dismissible alert-label-icon rounded-label fade show" role="alert">
                <i class="ri-error-warning-line label-icon"></i>
                <strong>The Employment Status list will appear automatically starting 60 days before End Period.</strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    </div>
    <!-- Info Yearly -->
    <div class="row d-none" id="yearly-info-alert">
        <div class="col-12">
            <div class="alert alert-primary alert-dismissible alert-label-icon rounded-label fade show" role="alert">
                <i class="ri-error-warning-line label-icon"></i>
                <strong>The Yearly Evaluation list will appear automatically starting <span
                        id="yearly-remain-period">30</span> days before <span id="yearly-end-period">31 December
                        {{ date('Y') }}</span>.</strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <ul class="nav nav-tabs nav-tabs-custom nav-primary" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link py-3 active" id="tab-employment-status" data-bs-toggle="tab"
                            href="#pill-employment-status" role="tab">
                            <i class="ri-user-2-line me-1 align-bottom"></i> Employment Status
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link py-3" id="tab-yearly-evaluation" data-bs-toggle="tab"
                            href="#pill-yearly-evaluation" role="tab">
                            <i class="ri-calendar-2-line me-1 align-bottom"></i> Yearly Evaluation
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="pill-employment-status" role="tabpanel">
                        <div class="row align-items-center p-3 g-3">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <select class="form-control select2" name="department_id" id="department_id">
                                        <option value="ALL">ALL DEPARTMENT</option>
                                        @foreach ($departments as $department)
                                            <option value="{{ $department->id }}"
                                                {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                                {{ $department->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <select class="form-control select2" name="building_id" id="building_id">
                                        <option value="ALL">ALL ORGANIZATION</option>
                                        @foreach ($buildings as $building)
                                            <option value="{{ $building->id }}"
                                                {{ old('building_id') == $building->id ? 'selected' : '' }}>
                                                {{ $building->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex gap-1">
                                    <button type="button" name="filter" id="filter-employment"
                                        class="btn btn-soft-primary waves-effect waves-light btn-sm">
                                        <i class="ri-filter-2-line me-1 align-bottom"></i> Filters
                                    </button>
                                    <button type="button" name="reset" id="reset-employment" class="btn btn-soft-danger waves-effect waves-light btn-sm">
                                        <i class="ri-refresh-line me-1 align-bottom"></i> Reset
                                    </button>
                                    <button id="release-btn-employment" type="button" title="Release Form" class="btn btn-soft-success btn-sm waves-effect waves-light position-relative btn-with-badge" style="display: none;">
                                        <i class="ri-send-plane-line me-1 align-bottom"></i> Release Form
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                            <span class="release-count">0</span>
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped bordered display nowrap" style="width:100%"
                                id="table_employment_status">
                                <thead>
                                    <tr>
                                        <th style="text-align:center"><input type="checkbox" id="checkAllEmployment"></th>
                                        <th scope="col" class="text-center">NIK</th>
                                        <th scope="col">Full Name</th>
                                        <th scope="col" class="text-center">Join Date</th>
                                        <th scope="col" class="text-center">Status</th>
                                        <th scope="col" class="text-center">Start Date</th>
                                        <th scope="col" class="text-center">End Date</th>
                                        <th scope="col" class="text-center">Service Year</th>
                                        <th scope="col" class="text-center">Area</th>
                                        <th scope="col" class="text-center">Department</th>
                                        <th scope="col" class="text-center">Section</th>
                                        <th scope="col" class="text-center">Position</th>
                                        <th scope="col" class="text-center">Organization</th>
                                        <th scope="col" class="text-center">Remaining</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane" id="pill-yearly-evaluation" role="tabpanel">
                        <div class="row align-items-center p-3 g-3">
                            <div class="col-md-3">
                                <select class="form-control select2" name="yearly_department_id"
                                    id="yearly_department_id">
                                    <option value="ALL">ALL DEPARTMENT</option>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select class="form-control select2" name="yearly_building_id" id="yearly_building_id">
                                    <option value="ALL">ALL ORGANIZATION</option>
                                    @foreach ($buildings as $building)
                                        <option value="{{ $building->id }}">{{ $building->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex gap-1">
                                    <button type="button" id="filter-yearly"
                                        class="btn btn-soft-primary waves-effect waves-light btn-sm">
                                        <i class="ri-filter-2-line me-1 align-bottom"></i> Filters</button>
                                    <button type="button" id="reset-yearly" class="btn btn-soft-danger waves-effect waves-light btn-sm">
                                        <i class="ri-refresh-line me-1 align-bottom"></i> Reset
                                    </button>
                                    <button id="release-btn-yearly" type="button" title="Release Form" class="btn btn-soft-success btn-sm waves-effect waves-light position-relative btn-with-badge" style="display: none;">
                                        <i class="ri-send-plane-line me-1 align-bottom"></i> Release Form
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                            <span class="release-count">0</span>
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped bordered display nowrap" style="width:100%"
                                id="table_yearly_evaluation">
                                <thead>
                                    <tr>
                                        <th style="text-align:center"><input type="checkbox" id="checkAllYearly"></th>
                                        <th scope="col" class="text-center">NIK</th>
                                        <th scope="col">Full Name</th>
                                        <th scope="col" class="text-center">Join Date</th>
                                        <th scope="col" class="text-center">Status</th>
                                        <th scope="col" class="text-center">Start Date</th>
                                        <th scope="col" class="text-center">End Date</th>
                                        <th scope="col" class="text-center">Service Year</th>
                                        <th scope="col" class="text-center">Area</th>
                                        <th scope="col" class="text-center">Department</th>
                                        <th scope="col" class="text-center">Section</th>
                                        <th scope="col" class="text-center">Position</th>
                                        <th scope="col" class="text-center">Organization</th>
                                        <th scope="col" class="text-center">Remaining</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
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
            let currentTabId = '#pill-employment-status';
            let tableEmployment = null;
            let tableYearly = null;
            let selectedIds = [];

            const EMPLOYMENT_COLUMNS = [
                { 
                    data: 'id', 
                    name: 'id', 
                    orderable: false, 
                    searchable: false, 
                    className: "text-center",
                    render: function(data, type, row, meta) {
                        if (row.is_draft) {
                            return `<input type="checkbox" class="row-checkbox" value="${data}" disabled title="DRAFT Evaluation">`;
                        }
                        return `<input type="checkbox" class="row-checkbox" value="${data}">`;
                    }
                },
                {
                    data: 'nik',
                    name: 'nik',
                    defaultContent: '-'
                },
                {
                    data: 'fullname',
                    name: 'fullname',
                    defaultContent: '-'
                },
                {
                    data: 'joindate',
                    name: 'joindate',
                    className: "text-center",
                    render: function(data, type, row) {
                        if (type === 'sort' || type === 'type') {
                            return data.timestamp || 0;
                        }
                        return data.display || '-';
                    }
                },
                {
                    data: 'contract_number',
                    name: 'contract_number',
                    className: "text-center"
                },
                {
                    data: 'start_date',
                    name: 'start_date',
                    className: "text-center",
                    render: function(data, type, row) {
                        if (type === 'sort' || type === 'type') {
                            return data.timestamp || 0;
                        }
                        return data.display || '-';
                    }
                },
                {
                    data: 'end_date',
                    name: 'end_date',
                    className: "text-center",
                    render: function(data, type, row) {
                        if (type === 'sort' || type === 'type') {
                            return data.timestamp || 0;
                        }
                        return data.display || '-';
                    }
                },
                {
                    data: 'service_year',
                    name: 'service_year',
                    className: "text-center",
                    defaultContent: '-'
                },
                {
                    data: 'area',
                    name: 'area',
                    className: "text-center",
                    defaultContent: '-'
                },
                {
                    data: 'department',
                    name: 'department',
                    className: "text-center",
                    defaultContent: '-'
                },
                {
                    data: 'section',
                    name: 'section',
                    className: "text-center",
                    defaultContent: '-'
                },
                {
                    data: 'position',
                    name: 'position',
                    className: "text-center",
                    defaultContent: '-'
                },
                {
                    data: 'building',
                    name: 'building',
                    className: "text-center",
                    defaultContent: '-'
                },
                {
                    data: 'remaining',
                    name: 'remaining',
                    className: "text-center",
                    orderable: false,
                    searchable: false
                },
            ];

            const YEARLY_COLUMNS = [
                { 
                    data: 'id', 
                    name: 'id', 
                    orderable: false, 
                    searchable: false, 
                    className: "text-center",
                    render: function(data, type, row, meta) {
                        if (row.is_draft) {
                            return `<input type="checkbox" class="row-checkbox" value="${data}" disabled title="DRAFT Evaluation">`;
                        }
                        return `<input type="checkbox" class="row-checkbox" value="${data}">`;
                    }
                },
                {
                    data: 'nik',
                    name: 'nik',
                    defaultContent: '-'
                },
                {
                    data: 'fullname',
                    name: 'fullname',
                    defaultContent: '-'
                },
                {
                    data: 'joindate',
                    name: 'joindate',
                    className: "text-center",
                    render: function(data, type, row) {
                        if (type === 'sort' || type === 'type') {
                            return data.timestamp || 0;
                        }
                        return data.display || '-';
                    }
                },
                {
                    data: 'status',
                    name: 'status',
                    className: "text-center",
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'start_date',
                    name: 'start_date',
                    className: "text-center",
                    render: function(data, type, row) {
                        if (type === 'sort' || type === 'type') {
                            return data.timestamp || 0;
                        }
                        return data.display || '-';
                    }
                },
                {
                    data: 'end_date',
                    name: 'end_date',
                    className: "text-center",
                    render: function(data, type, row) {
                        if (type === 'sort' || type === 'type') {
                            return data.timestamp || 0;
                        }
                        return data.display || '-';
                    }
                },
                {
                    data: 'service_year',
                    name: 'service_year',
                    className: "text-center",
                    defaultContent: '-'
                },
                {
                    data: 'area',
                    name: 'area',
                    className: "text-center",
                    defaultContent: '-'
                },
                {
                    data: 'department',
                    name: 'department',
                    className: "text-center",
                    defaultContent: '-'
                },
                {
                    data: 'section',
                    name: 'section',
                    className: "text-center",
                    defaultContent: '-'
                },
                {
                    data: 'position',
                    name: 'position',
                    className: "text-center",
                    defaultContent: '-'
                },
                {
                    data: 'building',
                    name: 'building',
                    className: "text-center",
                    defaultContent: '-'
                },
                {
                    data: 'remaining',
                    name: 'remaining',
                    className: "text-center",
                    orderable: false,
                    searchable: false
                },
            ];

            let defaultTab = '#pill-employment-status';
            let routeEmployment = "{{ route('evaluation.schedule.index') }}";
            let routeYearly = "{{ route('evaluation.schedule.getYearly') }}";
            const yearlyAlert = $('#yearly-info-alert');
            const employmentAlert = $('#employment-info-alert');

            @if (Session::has('tab_yearly') || request()->has('tab_yearly'))
                defaultTab = '#pill-yearly-evaluation';
            @elseif (Session::has('tab_employment') || request()->has('tab_employment'))
                defaultTab = '#pill-employment-status';
            @endif

            if (defaultTab === '#pill-yearly-evaluation') {
                currentTabId = defaultTab;
                tableYearly = initializeDataTable('#table_yearly_evaluation', routeYearly);
                $('a[href="#pill-yearly-evaluation"]').tab('show');
                yearlyAlert.removeClass('d-none');
                employmentAlert.addClass('d-none');
            } else {
                currentTabId = defaultTab;
                tableEmployment = initializeDataTable('#table_employment_status', routeEmployment);
                yearlyAlert.addClass('d-none');
                employmentAlert.removeClass('d-none');
            }

            $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
                let targetTab = $(e.target).attr('href');
                currentTabId = targetTab;
                selectedIds = []; 
                $('#checkAllEmployment, #checkAllYearly').prop('checked', false);
                updateMultiButtons();
                if (targetTab === '#pill-employment-status') {
                    tableEmployment = initializeDataTable('#table_employment_status',
                        "{{ route('evaluation.schedule.index') }}");
                    yearlyAlert.addClass('d-none');
                    employmentAlert.removeClass('d-none');
                } else if (targetTab === '#pill-yearly-evaluation') {
                    tableYearly = initializeDataTable('#table_yearly_evaluation',
                        "{{ route('evaluation.schedule.getYearly') }}");
                    yearlyAlert.removeClass('d-none');
                    employmentAlert.addClass('d-none');
                }
            });

            function initializeDataTable(tableId, ajaxUrl) {
                if ($.fn.DataTable.isDataTable(tableId)) {
                    $(tableId).DataTable().destroy();
                }
                let columnsToUse;
                if (tableId === '#table_yearly_evaluation') {
                    columnsToUse = YEARLY_COLUMNS;
                } else {
                    columnsToUse = EMPLOYMENT_COLUMNS;
                }
                return $(tableId).DataTable({
                    destroy: true,
                    stateSave: false,
                    responsive: false,
                    autoWidth: false,
                    processing: true,
                    serverSide: false,
                    scrollX: true,
                    order: [],
                    ajax: {
                        url: ajaxUrl,
                        error: function(xhr, error, thrown) {
                            console.error("DataTables AJAX Error:", xhr.responseText);
                        },
                        data: function(d) {
                            if (tableId === '#table_yearly_evaluation') {
                                d.department_id = $('#yearly_department_id').val();
                                d.building_id = $('#yearly_building_id').val();
                            } else {
                                d.department_id = $('#department_id').val();
                                d.building_id = $('#building_id').val();
                            }
                            return d;
                        },
                        dataSrc: function(json) {
                            if (tableId === '#table_yearly_evaluation') {
                                const yearlyAlert = $('#yearly-info-alert');
                                if (json.yearly_end_date) {
                                    const endDate = json.yearly_end_date;
                                    const remain = json.remain;
                                    $('#yearly-end-period').text(endDate);
                                    $('#yearly-remain-period').text(remain);
                                }
                            }
                            return json.data;
                        }
                    },
                    columns: columnsToUse,
                    drawCallback: function(settings) {
                        let table = this.api();
                        let checkAllId = (tableId === '#table_employment_status') ? '#checkAllEmployment' : '#checkAllYearly';                       
                        table.rows({ page: 'current' }).nodes().to$().each(function() {
                            const rowData = table.row(this).data();
                            const id = String(rowData.id);
                            const checkbox = $(this).find('.row-checkbox');
                            if (selectedIds.includes(id)) {
                                checkbox.prop('checked', true);
                            } else {
                                checkbox.prop('checked', false);
                            }
                        });
                        checkAllStatus();
                    }
                });
            }
            $.fn.dataTable.ext.errMode = 'none';
            $(document).on('error.dt', function(e, settings, techNote, message) {
                console.error('DataTables Error:', message);
            });

            $('#filter-employment').on('click', function() {
                if (tableEmployment) {
                    selectedIds = [];
                    $('#checkAllEmployment').prop('checked', false);
                    updateMultiButtons();
                    tableEmployment.ajax.reload();
                }
            });
            $('#reset-employment').on('click', function() {
                $('#department_id').val('ALL').trigger('change');
                $('#building_id').val('ALL').trigger('change');
                if (tableEmployment) {
                    selectedIds = [];
                    $('#checkAllEmployment').prop('checked', false);
                    updateMultiButtons();
                    tableEmployment.ajax.reload();
                }
            });

            $('#filter-yearly').on('click', function() {
                if (tableYearly) {
                    selectedIds = [];
                    $('#checkAllYearly').prop('checked', false);
                    updateMultiButtons();
                    tableYearly.ajax.reload();
                }
            });
            $('#reset-yearly').on('click', function() {
                $('#yearly_department_id').val('ALL').trigger('change');
                $('#yearly_building_id').val('ALL').trigger('change');
                if (tableYearly) {
                    selectedIds = [];
                    $('#checkAllYearly').prop('checked', false);
                    updateMultiButtons();
                    tableYearly.ajax.reload();
                }
            });

            $(document).on('change', '.row-checkbox', function() {
                const id = String($(this).val());
                if (this.checked) {
                    if (!selectedIds.includes(id)) {
                        selectedIds.push(id);
                    }
                } else {
                    selectedIds = selectedIds.filter(val => String(val) !== id);
                }
                checkAllStatus();
                updateMultiButtons();
            });

            $('#checkAllEmployment, #checkAllYearly').on('click', function() {
                if (!$(this).is(':disabled')) {
                    const isChecked = this.checked;
                    const currentTable = (currentTabId === '#pill-employment-status') ? tableEmployment : tableYearly;
                    currentTable.rows({ page: 'current', search: 'applied' }).nodes().to$().each(function() {
                        const rowData = currentTable.row($(this)).data();
                        const id = String(rowData.id);
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
                            checkAllStatus();
                            updateMultiButtons();
                        }
                    });
                }
            });

            // RELEASE FORM
            $('#release-btn-employment, #release-btn-yearly').on('click', function() {
                const currentTable = (currentTabId === '#pill-employment-status') ? tableEmployment : tableYearly;
                const purpose = (currentTabId === '#pill-employment-status') ? 'Employment Status' : 'Yearly Evaluation';
                
                let startDates = new Set();
                let endDates = new Set();
                
                currentTable.rows().data().each(function(row) {
                    if (selectedIds.includes(String(row.id))) {
                        let rStartDate = (row.start_date && row.start_date.display) ? row.start_date.display : row.start_date;
                        let rEndDate = (row.end_date && row.end_date.display) ? row.end_date.display : row.end_date;
                        if(rStartDate && rStartDate !== '-') startDates.add(rStartDate);
                        if(rEndDate && rEndDate !== '-') endDates.add(rEndDate);
                    }
                });

                if (startDates.size > 1 || endDates.size > 1) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed!',
                        text: 'Start & End Date Period not SAME',
                        confirmButtonText: 'Ok, Got it!',
                        buttonsStyling: false,
                        customClass: {
                            confirmButton: "btn btn-primary"
                        }
                    });
                    return;
                }

                const startDate = [...startDates][0] || '';
                const endDate = [...endDates][0] || ''

                Swal.fire({
                    title: 'Validating Data...',
                    text: 'Please wait while we check the master setup.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: '{{ route("evaluation.schedule.validate-multiple") }}',
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        employee_ids: JSON.stringify(selectedIds)
                    },
                    success: function(response) {
                        const form = $('<form>', {
                            method: 'POST',
                            action: '{{ route("evaluation.schedule.create-multiple") }}'
                        });
                        form.append($('<input>', { type: 'hidden', name: '_token', value: $('meta[name="csrf-token"]').attr('content') }));
                        form.append($('<input>', { type: 'hidden', name: 'employee_ids', value: JSON.stringify(selectedIds) }));
                        form.append($('<input>', { type: 'hidden', name: 'purpose', value: purpose }));
                        form.append($('<input>', { type: 'hidden', name: 'start_date', value: startDate }));
                        form.append($('<input>', { type: 'hidden', name: 'end_date', value: endDate }));
                        
                        $('body').append(form);
                        form.submit();
                    },
                    error: function(xhr) {
                        Swal.close();
                        if (xhr.status === 422 && xhr.responseJSON.missing_data) {
                            const missingData = xhr.responseJSON.missing_data;
                            let warningMessage = 'The following employees are missing setup :<br><br>';
                            warningMessage += `<ul style="text-align: left; font-size: 0.9em;">`;
                            missingData.forEach(function(emp) {
                                warningMessage += `<li>${emp}</li>`;
                            });
                            warningMessage += `</ul>`;
                            
                            Swal.fire({
                                icon: 'error',
                                title: 'Missing Master Setup',
                                html: warningMessage,
                                confirmButtonText: 'Ok, Got it!',
                                buttonsStyling: false,
                                customClass: {
                                    confirmButton: "btn btn-primary"
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'An unexpected error occurred during validation',
                                confirmButtonText: 'Ok, Got it!',
                                buttonsStyling: false,
                                customClass: {
                                    confirmButton: "btn btn-primary"
                                }
                            });
                        }
                    }
                });
            });

            function checkAllStatus() {
                const tableId = (currentTabId === '#pill-employment-status') ? '#table_employment_status' : '#table_yearly_evaluation';
                const checkAllId = (currentTabId === '#pill-employment-status') ? '#checkAllEmployment' : '#checkAllYearly';
                const totalEnabledCheckboxes = $(tableId + ' .row-checkbox:not(:disabled)').length;
                const totalCheckedCheckboxes = $(tableId + ' .row-checkbox:not(:disabled):checked').length;
                const $checkAllBtn = $(checkAllId);
                if (totalEnabledCheckboxes === 0) {
                    $checkAllBtn.prop('checked', false);
                    $checkAllBtn.prop('disabled', true);
                } else {
                    $checkAllBtn.prop('disabled', false);
                    $checkAllBtn.prop('checked', totalEnabledCheckboxes === totalCheckedCheckboxes);
                }
            }

            function updateMultiButtons() {
                const count = selectedIds.length;
                $('.release-count').text(count);
                if (count > 0) {
                    $('#release-btn-employment, #release-btn-yearly').show();
                } else {
                    $('#release-btn-employment, #release-btn-yearly').hide();
                }
            }

            const sidebarToggleBtn = $('#topnav-hamburger-icon');
            if (sidebarToggleBtn.length) {
                sidebarToggleBtn.on('click', function() {
                    setTimeout(function() {
                        if (currentTabId == '#pill-employment-status') {
                            $('#table_employment_status').DataTable().columns.adjust().draw();
                        } else if (currentTabId == '#pill-yearly-evaluation') {
                            $('#table_yearly_evaluation').DataTable().columns.adjust().draw();
                        }
                    }, 300);
                });
            }
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
