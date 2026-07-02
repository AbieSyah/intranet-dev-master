@extends('layouts.master')
@section('link')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css" />
    <link href="{{ url('') }}/assets/libs/Datatables/DataTables-1.13.1/css/dataTables.bootstrap5.css" rel="stylesheet"
        type="text/css" />
    <link href="{{ url('') }}/assets/libs/Datatables/Buttons-2.3.3/css/buttons.dataTables.css" rel="stylesheet"
        type="text/css" />
    <link href="{{ url('') }}/assets/libs/Datatables/Responsive-2.4.0/css/responsive.bootstrap.css" rel="stylesheet"
        type="text/css" />
    <link rel="stylesheet" href="{{ url('') }}/assets/libs/adminlte/select2/css/4.1.0/select2.min.css">
    <style type="text/css">
        body {
            background: #f7fbf8;
        }

        .preview {
            text-align: center;
            overflow: hidden;
            width: 160px;
            height: 160px;
            margin: 10px;
            border: 1px solid red;
        }

        .section {
            margin-top: 150px;
            background: #fff;
            padding: 50px 30px;
        }

        .modal-lg {
            max-width: 1000px !important;
        }

        .select2-container--default .select2-selection--single {
            height: calc(2.25rem + 2px);
            padding: 0.375rem 0.75rem;
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 1.5rem;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 100%;
        }

        .select2-container--default .select2-selection--multiple {
            min-height: calc(2.25rem + 2px);
            padding: 0.2rem 0.75rem;
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
        }

        div.dataTables_wrapper {
            width: 100%;
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
                <h4 class="mb-sm-0">Form Line Approval</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Line Approval</a></li>
                        <li class="breadcrumb-item active">Form</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex justify-content-between">
                    <h3 class="card-title">Line Approval {{ $lineapproval->approval_type ?? '' }}</h3>
                    <div class="flex-shrink-0">
                        <a href="{{ route('line-approval.index') }}"
                            class="btn btn-primary btn-label waves-effect waves-light"><i
                                class="ri-arrow-left-fill label-icon align-middle fs-16 me-2"></i> Back</a>
                    </div>
                </div>
                <div class="card-body">
                    <form class="form" action="{{ route('line-approval.store') }}" method="post"
                        enctype="multipart/form-data">
                        @csrf
                        @method('POST')
                        <div class="row gy-3">
                            @php
                                $isEditMode = isset($lineapproval->id) && !empty($lineapproval->id);
                                $disabledAttr = $isEditMode ? 'disabled' : '';
                            @endphp
                            <input type="hidden" name="id" id="id" value="{{ $lineapproval->id ?? '' }}">
                            <div class="col-lg-6 p-2">
                                <label class="required fw-semibold fs-6 mb-2">Group Name</label>
                                <input required type="text" name="group_name" class="auto-sum-2 form-control form-control-solid mb-3 mb-lg-0" placeholder="Input Group Name"
                                    value="{{ old('group_name', $lineapproval->group_name ?? '') }}" />
                            </div>
                            <div class="col-lg-6 p-2">
                                <label class="required fw-semibold fs-6 mb-2">Approval Type</label>
                                <select required class="form-select select2" data-placeholder="Select an option"
                                    name="approval_type" id="approval_type" {{ $disabledAttr }}>
                                    <option></option>
                                    <option
                                        {{ old('approval_type', $lineapproval->approval_type ?? '') == 'Evaluation' ? 'selected' : '' }}>
                                        Evaluation</option>
                                    <option
                                        {{ old('approval_type', $lineapproval->approval_type ?? '') == 'Attendance Leave' ? 'selected' : '' }}>
                                        Attendance Leave</option>
                                    <option
                                        {{ old('approval_type', $lineapproval->approval_type ?? '') == 'Attendance Permit' ? 'selected' : '' }}>
                                        Attendance Permit</option>
                                    <option
                                        {{ old('approval_type', $lineapproval->approval_type ?? '') == 'Attendance Overtime' ? 'selected' : '' }}>
                                        Attendance Overtime</option>
                                    <option
                                        {{ old('approval_type', $lineapproval->approval_type ?? '') == 'Business Trip Domestic' ? 'selected' : '' }}>
                                        Business Trip Domestic</option>
                                    <option
                                        {{ old('approval_type', $lineapproval->approval_type ?? '') == 'Business Trip LuarNegeri' ? 'selected' : '' }}>
                                        Business Trip LuarNegeri</option>
                                    <option
                                        {{ old('approval_type', $lineapproval->approval_type ?? '') == 'Report/Claim Business Trip' ? 'selected' : '' }}>
                                        Report/Claim Business Trip</option>
                                    <option
                                        {{ old('approval_type', $lineapproval->approval_type ?? '') == 'Employee Requisition' ? 'selected' : '' }}>
                                        Employee Requisition</option>
                                </select>
                            </div>
                            <div id="employee-filter-and-table">
                                <div class="col-12">
                                    <hr>
                                </div>
                                <div class="row">
                                    <div class="col-lg-4 col-sm-6 p-2">
                                        <label class="required fw-semibold fs-6 mb-2">Department</label>
                                        <select class="form-select select2" data-placeholder="Select an option"
                                            name="department_id" id="department_id" {{ $disabledAttr }}>
                                            <option value="ALL">ALL</option>
                                            @foreach ($departments as $department)
                                                <option value="{{ $department->id }}"
                                                    {{ old('department_id', $lineapproval->department_id ?? '') == $department->id ? 'selected' : '' }}>
                                                    {{ $department->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-lg-4 col-sm-6 p-2">
                                        <label class="required fw-semibold fs-6 mb-2">Area</label>
                                        <select class="form-select select2" data-placeholder="Select an option"
                                            name="area_id" id="area_id" {{ $disabledAttr }}>
                                            <option value="ALL">ALL</option>
                                            @foreach ($areas as $area)
                                                <option value="{{ $area->id }}"
                                                    {{ old('area_id', $lineapproval->area_id ?? '') == $area->id ? 'selected' : '' }}>
                                                    {{ $area->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-lg-4 col-sm-6 p-2">
                                        <label class="required fw-semibold fs-6 mb-2">Building / Placement</label>
                                        <select class="form-select select2" data-placeholder="Select an option"
                                            name="building_id" id="building_id" {{ $disabledAttr }}>
                                            <option value="ALL">ALL</option>
                                            @foreach ($buildings as $building)
                                                <option value="{{ $building->id }}"
                                                    {{ old('building_id', $lineapproval->building_id ?? '') == $building->id ? 'selected' : '' }}>
                                                    {{ $building->nama }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-lg-4 col-sm-6 p-2">
                                        <label class="required fw-semibold fs-6 mb-2">Position</label>
                                        <select class="form-select select2" data-placeholder="Select an option"
                                            name="position_id" id="position_id" {{ $disabledAttr }}>
                                            <option value="ALL">ALL</option>
                                            @foreach ($positions as $position)
                                                <option value="{{ $position->id }}"
                                                    {{ old('position_id', $lineapproval->position_id ?? '') == $position->id ? 'selected' : '' }}>
                                                    {{ $position->nama }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-lg-4 col-sm-6 p-2">
                                        <label class="required fw-semibold fs-6 mb-2">Section</label>
                                        <select class="form-select select2" data-placeholder="Select an option"
                                            name="section_id" id="section_id" {{ $disabledAttr }}>
                                            <option value="ALL">ALL</option>
                                            @foreach ($sections as $section)
                                                <option value="{{ $section->id }}"
                                                    {{ old('section_id', $lineapproval->section_id ?? '') == $section->id ? 'selected' : '' }}>
                                                    {{ $section->nama }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                <div class="col-12 mb-2">
                                    @if ($isEditMode)
                                        <button id="add-employee-btn" class="float-end btn btn-primary btn-label waves-effect waves-light"
                                            data-bs-toggle="modal" data-bs-target="#addEmployeeModal"><i
                                            class="ri-add-circle-line label-icon align-middle fs-16 me-2"></i>Add Employee</button>
                                    @endif
                                    <button id="multi-keep-btn" type="button" title="Keep"
                                        class="float-end btn btn-success btn-sm waves-effect waves-light position-relative btn-with-badge me-2"
                                        style="display:none; z-index: 999;">
                                        <i class="ri-check-line fs-16"></i>
                                        <span
                                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                            <span id="keep-count">0</span>
                                            <span class="visually-hidden">keep selected</span>
                                        </span>
                                    </button>
                                    <button id="multi-delete-btn" type="button" title="Delete"
                                        class="float-end btn btn-danger btn-sm waves-effect waves-light position-relative btn-with-badge me-2"
                                        style="display:none; z-index: 999;">
                                        <i class="ri-delete-bin-line fs-16"></i>
                                        <span
                                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning">
                                            <span id="delete-count">0</span>
                                            <span class="visually-hidden">delete selected</span>
                                        </span>
                                    </button>
                                </div>
                                </div>
                                <div class="col-12">
                                    <table class="table table-striped bordered display nowrap" style="width:100%"
                                        id="table_employee">
                                        <thead>
                                            <tr>
                                                <th style="text-align:center">
                                                    <input type="checkbox" id="checkAll">
                                                </th>
                                                <th scope="col">NIK</th>
                                                <th scope="col">Name</th>
                                                <th scope="col">Position</th>
                                                <th scope="col">Section</th>
                                                <th scope="col" class="text-center">Status</th>
                                                <th scope="col" class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                    <input type="hidden" name="employee_ids" id="employee_ids">
                                </div>
                            </div>
                            @for ($i = 1; $i <= 8; $i++)
                                <div class="approval col-lg-4 col-sm-6 p-2">
                                    <label class="required fw-semibold fs-6 mb-2">Approval {{ $i }}<span class="text-danger d-none info-approval"> (Not Applicant)</span></label>
                                    <select class="form-select select2 select2-approval"
                                        data-placeholder="Select an option" name="approve_{{ $i }}"
                                        id="approve_{{ $i }}" {{ $i == 1 ? 'required' : '' }}>
                                        <option></option>
                                        @foreach ($approveds as $approved)
                                            <option value="{{ $approved->id }}"
                                                {{ old('approve_' . $i, $lineapproval->{'approve_' . $i} ?? '') == $approved->id ? 'selected' : '' }}>
                                                {{ $approved->fullname }} ({{ $approved->position->nama ?? '-' }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endfor
                            <div class="drafter col-lg-4 p-2 d-none">
                                <label class="required fw-semibold fs-6 mb-2">Drafter</label>
                                <select class="form-select select2 select2-approval"
                                    data-placeholder="Select an option"
                                    name="drafter" id="drafter">
                                    <option></option>
                                    @foreach ($approveds as $approved)
                                        <option value="{{ $approved->id }}"
                                            {{ old('drafter', $lineapproval->drafter ?? '') == $approved->id ? 'selected' : '' }}>
                                            {{ $approved->fullname }} ({{ $approved->position->nama ?? '-' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <div class="d-flex justify-content-end">
                                    <div class="text-center pt-10">
                                        <button type="submit" class="btn btn-primary"
                                            data-kt-users-modal-action="submit">
                                            <span class="d-none spinner-border spinner-border-sm" role="status"
                                                aria-hidden="true"></span>
                                            <span class="indicator-label">Save</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="addEmployeeModal" tabindex="-1" aria-labelledby="addEmployeeModalLabel" aria-hidden="true"
        data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addEmployeeModalLabel">Add Employee (Filtered List)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 mb-2">
                            <button type="button" id="add-selected-employees-btn" class="btn btn-primary btn-label waves-effect waves-light"
                            data-text="Add Selected"><i
                                class="ri-add-circle-line label-icon align-middle fs-16 me-2"></i>
                                Add Selected (<span id="modal-selected-count">0</span>)
                            </button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped bordered display nowrap" style="width:100%" id="modal_employee_table">
                            <thead>
                                <tr>
                                    <th style="text-align:center"><input type="checkbox" id="checkAllModal"></th>
                                    <th>NIK</th>
                                    <th>Name</th>
                                    <th>Position</th>
                                    <th>Section</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ url('') }}/assets/js/pages/profile-setting.init.js"></script>
    <script src="{{ url('') }}/assets/libs/Datatables/DataTables-1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="{{ url('') }}/assets/libs/Datatables/DataTables-1.13.1/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ url('') }}/assets/libs/Datatables/Responsive-2.4.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="{{ url('') }}/assets/js/pages/datatables.init.js"></script>
    <script src="{{ url('') }}/assets/libs/adminlte/select2/js/4.1.0/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.js"></script>
@endsection
@section('javascript')
    <script type="text/javascript">
        $(document).ready(function() {

            $('.select2').not('.select2-approval').select2();
            $('.select2-approval').select2({
                allowClear: true,
                placeholder: "Select an option"
            });
            let employeeTable;
            let modalEmployeeTable;
            let selectedIds = [];
            let modalSelectedIds = [];
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
                    employeeTable.rows({
                        page: 'current'
                    }).data().each(function(row) {
                        const rowId = row.id.toString();
                        const checkbox = $('.row-checkbox[value="' + rowId + '"]').not(':disabled');
                        if (checkbox.length) {
                            checkbox.prop('checked', isChecked);
                            if (isChecked) {
                                if (!selectedIds.includes(rowId)) {
                                    selectedIds.push(rowId);
                                }
                            } else {
                                selectedIds = selectedIds.filter(val => val !== rowId);
                            }
                        }
                    });
                    updateMultiButtons();
                }
            });

            function checkAllStatus() {
                const totalEnabledCheckboxes = $('.row-checkbox:not(:disabled)').length;
                const totalCheckedCheckboxes = $('.row-checkbox:not(:disabled):checked').length;
                $('#checkAll').prop('checked', totalEnabledCheckboxes > 0 && totalEnabledCheckboxes ===
                    totalCheckedCheckboxes);
            }

            function toggleApprovalFields() {
                const approvalType = $('#approval_type').val();
                const approvalFields = $('.approval');
                const employeeFilterAndTable = $('#employee-filter-and-table');
                if (!approvalType) {
                    approvalFields.hide();
                    employeeFilterAndTable.hide();
                    return;
                }
                employeeFilterAndTable.show();
                approvalFields.each(function(index) {
                    const approvalNumber = index + 1;
                    let showApproval = true;
                    let showDrafter = false;
                    if (approvalType === 'Evaluation') {
                        if (approvalNumber > 6) {
                            showApproval = false;
                        }
                        $('.info-approval').addClass('d-none');
                        showDrafter = true;
                    } else if (approvalType === 'Employee Requisition') {
                        if (approvalNumber > 4) {
                            showApproval = false;
                        }
                        $('.info-approval').removeClass('d-none');
                        showDrafter = false;
                    } else if (approvalType === 'Attendance Leave') {
                        if (approvalNumber > 8) {
                            showApproval = false;
                        }
                        $('.info-approval').removeClass('d-none');
                        showDrafter = false;
                    } else if (approvalType === 'Attendance Permit') {
                        if (approvalNumber > 8) {
                            showApproval = false;
                        }
                        $('.info-approval').removeClass('d-none');
                        showDrafter = false;
                    } else if (approvalType === 'Attendance Overtime') {
                        if (approvalNumber > 8) {
                            showApproval = false;
                        }
                        $('.info-approval').removeClass('d-none');
                        showDrafter = false;
                    } else if (approvalType === 'Business Trip Dommestic') {
                        if (approvalNumber > 8) {
                            showApproval = false;
                        }
                        $('.info-approval').removeClass('d-none');
                    } else if (approvalType === 'Business Trip LuarNegeri') {
                        if (approvalNumber > 8) {
                            showApproval = false;
                        }
                        $('.info-approval').removeClass('d-none');
                    } else if (approvalType === 'Report/Claim Business Trip') {
                        if (approvalNumber > 8) {
                            showApproval = false;
                        }
                        $('.info-approval').removeClass('d-none');
                    } else {
                        $('#employee-filter-and-table').show()
                    }
                    if (showApproval) {
                        $(this).show();
                    } else {
                        $(`#approve_${approvalNumber}`).val('').trigger('change');
                        $(this).hide();
                    }
                    if (showDrafter) {
                        $('.drafter').removeClass('d-none');
                    } else {
                        $('.drafter').addClass('d-none');
                    }
                });
            }
            toggleApprovalFields();
            $('#approval_type').on('change', function() {
                toggleApprovalFields();
            });

            function updateMultiButtons() {
                const count = selectedIds.length;
                if (count > 0) {
                    $('#multi-delete-btn').show();
                    $('#delete-count').text(count);
                    $('#multi-keep-btn').show();
                    $('#keep-count').text(count);
                } else {
                    $('#multi-delete-btn').hide();
                    $('#multi-keep-btn').hide();
                }
            }
            $(document).on('click', '#multi-delete-btn', function() {
                const count = selectedIds.length;
                if (count === 0) {
                    Swal.fire({
                        title: 'No Items Selected',
                        text: 'Please select at least one item to delete.',
                        icon: 'info'
                    });
                    return;
                }
                Swal.fire({
                    title: 'Are you sure?',
                    text: `You won't be able to revert the deletion of ${count} items!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Yes, Delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        employeeTable.rows(function(idx, data, node) {
                            return selectedIds.includes(data.id.toString());
                        }).remove().draw(false);
                        selectedIds = [];
                        $('.row-checkbox').prop('checked', false);
                        updateMultiButtons();
                        checkAllStatus();
                        Swal.fire(
                            'Deleted!',
                            `${count} employees have been removed from the list.`,
                            'success'
                        );
                    }
                });
            });
            $(document).on('click', '#multi-keep-btn', function() {
                const count = selectedIds.length;
                Swal.fire({
                    title: 'Are you sure?',
                    text: `You are about to keep ${count} selected items and delete all others.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Yes, Keep the Selected!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const allData = employeeTable.rows().data().toArray();
                        const toKeepData = allData.filter(row => selectedIds.includes(row.id
                            .toString()));
                        employeeTable.clear().draw(false);
                        employeeTable.rows.add(toKeepData).draw(false);
                        selectedIds = [];
                        $('.row-checkbox').prop('checked', false);
                        updateMultiButtons();
                        checkAllStatus();
                        Swal.fire(
                            'Success!',
                            `Only the ${count} selected employees have been kept. The others are removed.`,
                            'success'
                        );
                    }
                });
            });
            $('#table_employee').hide();

            function initializeDataTable(data) {
                if ($.fn.DataTable.isDataTable('#table_employee')) {
                    employeeTable.destroy();
                    updateMultiButtons();
                    checkAllStatus();
                }
                employeeTable = $('#table_employee').DataTable({
                    data: data,
                    stateSave: false,
                    responsive: false,
                    autoWidth: false,
                    scrollX: false,
                    order: [
                        [1, 'asc']
                    ],
                    columns: [{
                            data: 'id',
                            name: 'id',
                            orderable: false,
                            searchable: false,
                            className: "text-center",
                            render: function(data, type, row, meta) {
                                return `<input type="checkbox" class="row-checkbox" value="${data}">`;
                            }
                        },
                        {
                            data: 'nik',
                            name: 'nik'
                        },
                        {
                            data: 'fullname',
                            name: 'fullname'
                        },
                        {
                            data: 'position',
                            name: 'position',
                        },
                        {
                            data: 'section',
                            name: 'section',
                        },
                        {
                            data: 'status',
                            name: 'status',
                            orderable: false,
                            searchable: false,
                            className: 'text-center'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false,
                            className: 'text-center'
                        }
                    ],
                });
                employeeTable.on('draw', function() {
                    $('.row-checkbox').each(function() {
                        const id = $(this).val();
                        if (selectedIds.includes(id)) {
                            $(this).prop('checked', true);
                        } else {
                            $(this).prop('checked', false);
                        }
                    });
                    checkAllStatus();
                });
                $('#table_employee').show();
            }

            function initializeModalDataTable(data) {
                if ($.fn.DataTable.isDataTable('#modal_employee_table')) {
                    modalEmployeeTable.destroy();
                }
                modalSelectedIds = [];
                modalEmployeeTable = $('#modal_employee_table').DataTable({
                    data: data,
                    stateSave: false,
                    responsive: true,
                    autoWidth: false,
                    scrollX: true,
                    order: [
                        [1, 'asc']
                    ],
                    columns: [
                        {
                            data: 'id',
                            name: 'id',
                            orderable: false,
                            searchable: false,
                            className: "text-center",
                            render: function(data, type, row, meta) {
                                const isDisabled = employeeTable.rows().data().toArray().some(r => r.id.toString() === data.toString());
                                return `<input type="checkbox" class="modal-row-checkbox" value="${data}" ${isDisabled ? 'disabled' : ''}>`;
                            }
                        },
                        { data: 'nik', name: 'nik' },
                        { data: 'fullname', name: 'fullname' },
                        { data: 'position', name: 'position' },
                        { data: 'section', name: 'section' },
                        { data: 'status', name: 'status', orderable: false, searchable: false, className: 'text-center' },
                        {
                            data: 'id',
                            name: 'action',
                            orderable: false,
                            searchable: false,
                            className: 'text-center',
                            render: function(data, type, row, meta) {
                                return `<button type="button" class="btn btn-danger btn-sm modal-remove-employee" data-id="${data}"><i class="ri-delete-bin-line"></i></button>`;
                            }
                        }
                    ],
                });

                modalEmployeeTable.on('draw', function() {
                    const totalEnabledModalCheckboxes = $('.modal-row-checkbox:not(:disabled)').length;
                    const totalCheckedModalCheckboxes = $('.modal-row-checkbox:not(:disabled):checked').length;
                    if (totalEnabledModalCheckboxes === 0) {
                        $('#checkAllModal').prop('disabled', true).prop('checked', false);
                    } else {
                        $('#checkAllModal').prop('disabled', false);
                        $('#checkAllModal').prop('checked', totalEnabledModalCheckboxes > 0 && totalEnabledModalCheckboxes === totalCheckedModalCheckboxes);
                    }
                    $('.modal-row-checkbox').each(function() {
                        const id = $(this).val();
                        if (modalSelectedIds.includes(id)) {
                            $(this).prop('checked', true);
                        } else {
                            $(this).prop('checked', false);
                        }
                    });
                    updateModalButtons();
                });
            }

            $(document).on('click', '#modal_employee_table .modal-remove-employee', function() {
                const row = modalEmployeeTable.row($(this).parents('tr'));
                const id = $(this).data('id').toString();
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this deletion from the modal list!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Yes, Delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        row.remove().draw(false);
                        modalSelectedIds = modalSelectedIds.filter(val => val !== id);
                        updateModalButtons();
                        Swal.fire('Deleted!', 'Employee removed from modal list.', 'success');
                    }
                });
            });

            $(document).on('change', '.modal-row-checkbox', function() {
                const id = $(this).val();
                if (this.checked) {
                    if (!modalSelectedIds.includes(id)) {
                        modalSelectedIds.push(id);
                    }
                } else {
                    modalSelectedIds = modalSelectedIds.filter(val => val !== id);
                }
                updateModalButtons();
            });

            function updateModalButtons() {
                const count = modalSelectedIds.length;
                $('#modal-selected-count').text(count);
                $('#add-selected-employees-btn').prop('disabled', count === 0);
            }
            updateModalButtons();

            $('#checkAllModal').on('click', function() {
                const isChecked = this.checked;
                modalEmployeeTable.rows({ page: 'current' }).data().each(function(row) {
                    const rowId = row.id.toString();
                    const checkbox = $('.modal-row-checkbox[value="' + rowId + '"]').not(':disabled');
                    if (checkbox.length) {
                        checkbox.prop('checked', isChecked);
                        if (isChecked) {
                            if (!modalSelectedIds.includes(rowId)) {
                                modalSelectedIds.push(rowId);
                            }
                        } else {
                            modalSelectedIds = modalSelectedIds.filter(val => val !== rowId);
                        }
                    }
                });
                updateModalButtons();
            });

            $('#add-selected-employees-btn').on('click', function() {
                if (modalSelectedIds.length === 0) return;
                const selectedData = modalEmployeeTable.rows(function(idx, data) {
                    return modalSelectedIds.includes(data.id.toString());
                }).data().toArray();
                selectedData.forEach(row => {
                    row.action = '<button type="button" class="btn btn-danger btn-sm remove-employee" data-id="' + row.id + '"><i class="ri-delete-bin-line"></i></button>';
                    const idStr = row.id.toString();
                    if (!selectedIds.includes(idStr)) {
                        selectedIds.push(idStr);
                    }
                });
                employeeTable.rows.add(selectedData).draw(false);
                selectedData.forEach(row => {
                    $('.row-checkbox[value="' + row.id + '"]').prop('checked', false);
                });
                selectedIds = selectedIds.filter(id => {
                    return !selectedData.some(row => row.id.toString() === id);
                });
                selectedIds = selectedIds.filter(id => !modalSelectedIds.includes(id));
                modalEmployeeTable.rows(function(idx, data) {
                    return modalSelectedIds.includes(data.id.toString());
                }).remove().draw(false);
                modalSelectedIds = [];
                updateModalButtons();
                updateMultiButtons();
                checkAllStatus();
                $('#addEmployeeModal').modal('hide');
            });

            $('#add-employee-btn').on('click', function(e) {
                e.preventDefault();
                const currentLineApprovalId = $('#id').val();
                const approvalType = $('#approval_type').val();
                const departmentId = $('#department_id').val();
                const areaId = $('#area_id').val();
                const buildingId = $('#building_id').val();
                const positionId = $('#position_id').val();
                const sectionId = $('#section_id').val();
                Swal.fire({
                    title: 'Loading data...',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading() }
                });
                $.ajax({
                    url: "{{ route('line-approval.get-eligible-employees') }}",
                    type: "GET",
                    data: {
                        line_approval_id: currentLineApprovalId,
                        approval_type: approvalType,
                        department_id: departmentId,
                        area_id: areaId,
                        building_id: buildingId,
                        position_id: positionId,
                        section_id: sectionId,
                    },
                    success: function(response) {
                        Swal.close();
                        initializeModalDataTable(response);
                        $('#addEmployeeModal').modal('show');
                    },
                    error: function(xhr) {
                        Swal.close();
                        Swal.fire('Error', 'Failed to load eligible employee data.', 'error');
                        console.error("AJAX Error:", xhr.responseText);
                    }
                });
            });

            function loadEmployees(isInitialLoad = false) {
                const approvalType = $('#approval_type').val();
                const employeeFilterAndTable = $('#employee-filter-and-table');

                if (employeeFilterAndTable.css('display') === 'none' && !approvalType) {
                    if ($.fn.DataTable.isDataTable('#table_employee')) {
                        employeeTable.destroy();
                    }
                    $('#table_employee').hide();
                    return;
                }

                const departmentId = $('#department_id').val();
                const areaId = $('#area_id').val();
                const buildingId = $('#building_id').val();
                const positionId = $('#position_id').val();
                const sectionId = $('#section_id').val();
                const lineApprovalId = $('#id').val();

                const data = {
                    approval_type: approvalType,
                    department_id: departmentId,
                    area_id: areaId,
                    building_id: buildingId,
                    position_id: positionId,
                    section_id: sectionId,
                };

                if (isInitialLoad && lineApprovalId) {
                    data.line_approval_id = lineApprovalId;
                }

                if (approvalType || (isInitialLoad && lineApprovalId)) {
                    Swal.fire({
                        title: 'Loading data...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading()
                        }
                    });
                    $.ajax({
                        url: "{{ route('line-approval.get-employees') }}",
                        type: "GET",
                        data: data,
                        success: function(response) {
                            Swal.close();
                            initializeDataTable(response);
                        },
                        error: function(xhr, status, error) {
                            Swal.close();
                            Swal.fire('Error', 'Failed to load employee data.', 'error');
                            console.error("AJAX Error:", xhr.responseText);
                        }
                    });
                } else {
                    if ($.fn.DataTable.isDataTable('#table_employee')) {
                        employeeTable.destroy();
                    }
                    $('#table_employee').hide();
                }
            }
            const isEditMode = $('#id').val().length > 0;
            if (isEditMode) {
                loadEmployees(true);
                $('#approval_type').on('change', function() {
                    toggleApprovalFields();
                });
            } else {
                $('#approval_type, #department_id, #area_id, #building_id, #position_id, #section_id').on('change',
                    function() {
                        loadEmployees(false);
                    });
                $('#approval_type').on('change', function() {
                    toggleApprovalFields();
                });
            }
            $('#table_employee').on('click', '.remove-employee', function() {
                const row = employeeTable.row($(this).parents('tr'));
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Yes, Delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        row.remove().draw(false);
                        Swal.fire(
                            'Deleted!',
                            'Employee has been removed from the list.',
                            'success'
                        );
                    }
                });
            });

            $("form").submit(function(e) {
                e.preventDefault();
                if (!this.reportValidity()) {
                    return;
                }

                let lastFilledIndex = 0;
                for (let i = 1; i <= 8; i++) {
                    if ($(`#approve_${i}`).val()) {
                        lastFilledIndex = i;
                    }
                }

                let missingApprovals = [];
                for (let i = 1; i <= lastFilledIndex; i++) {
                    if (!$(`#approve_${i}`).val()) {
                        missingApprovals.push(i);
                    }
                }

                if (missingApprovals.length > 0) {
                    let missingList = missingApprovals.map(num => `<li>Approval ${num}</li>`).join('');
                    let htmlContent = `
                        <p>There's a gap in the approval sequence. Please fill out the following approvals before adding subsequent ones:</p>
                        <ul style="text-align: left;">${missingList}</ul>
                    `;
                    Swal.fire({
                        title: 'Incomplete Approval Chain 🔗',
                        html: htmlContent,
                        icon: 'error',
                        buttonsStyling: false,
                        confirmButtonText: "Got it",
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        }
                    });
                    return;
                }

                const approvalType = $('#approval_type').val();
                let employeeIds = [];

                employeeIds = employeeTable.rows().data().toArray().map(row => row.id);
                if (employeeIds.length === 0 && approvalType !== 'Asset Disposal' && approvalType !== 'Change Management') {
                    Swal.fire('Error', 'Please select at least one employee.', 'error');
                    return;
                }

                $('#employee_ids').val(JSON.stringify(employeeIds));

                Swal.fire({
                    title: 'Saving data...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                const formData = new FormData(this);

                $.ajax({
                    url: $(this).attr("action"),
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        Swal.close();
                        Swal.fire({
                            title: "Success",
                            text: response.message,
                            icon: "success",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: {
                                popup: 'swal2-noanimation',
                                confirmButton: "btn btn-primary"
                            }
                        }).then(() => {
                            window.location.href = response.redirect;
                        });
                    },
                    error: function(xhr, status, error) {
                        Swal.close();
                        handleErrorResponse(xhr.responseJSON);
                    }
                });
            });

            function handleErrorResponse(responseJson) {
                let errorMessage = '';
                if (responseJson.message) {
                    errorMessage += `<h4 class="text-danger">${responseJson.message}</h4>`;
                }
                if (responseJson.errors) {
                    for (const fieldName in responseJson.errors) {
                        errorMessage += `<p class="text-danger">${responseJson.errors[fieldName][0]}</p>`;
                    }
                }
                if (responseJson.responseText) {
                    errorMessage += `<p class="text-danger">${responseJson.responseText}</p>`;
                }
                if (errorMessage === '') {
                    errorMessage += '<p class="text-danger">An error occurred.</p>';
                }
                Swal.fire({
                    title: 'Error',
                    html: errorMessage,
                    icon: 'error',
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    }
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
