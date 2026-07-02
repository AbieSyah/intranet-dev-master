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

        div.dataTables_wrapper {
            width: 100%;
        }

        tr {
            cursor: pointer;
        }

        tr.selected-row {
            font-weight: bold;
            cursor: default;
        }

        .btn-with-badge {
            overflow: visible;
        }

        :disabled {
            cursor: not-allowed;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Create Multiple Evaluation</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Multiple Evaluation</a></li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex justify-content-between">
                    <h3 class="card-title">Evaluation</h3>
                    <div class="flex-shrink-0">
                        <a href="{{ url()->previous() }}"
                            class="btn btn-primary btn-label waves-effect waves-light"><i
                                class="ri-arrow-left-fill label-icon align-middle fs-16 me-2"></i> Back</a>
                    </div>
                </div>
                <div class="card-body">
                    <form class="form" action="{{ route('evaluation.create-multiple.store') }}" method="post"
                        enctype="multipart/form-data" id="evalForm">
                        @csrf
                        @method('POST')
                        <div class="row gy-3">
                            <input type="hidden" name="id" id="id" value="{{ $lineapproval->id ?? '' }}">
                            <div class="col-12">
                                <h5 class="text-center">Evaluation Information</h5>
                            </div>

                            <div class="col-lg-4 col-sm-6 p-2">
                                <label class="required fw-semibold fs-6 mb-2" for="start_period">Start Period</label>
                                <div class="input-group">
                                    <input type="text" class="form-control text-sm" id="start_period" name="eval_start"
                                        placeholder="Select Date" value="{{ $start_date ?? old('eval_start') }}"
                                        {{ isset($is_from_schedule) ? 'readonly style=background-color:#e9ecef;' : 'required' }}>
                                    <span class="input-group-text" id="basic-addon2"><i
                                            class="ri-calendar-todo-line"></i></span>
                                </div>
                            </div>

                            <div class="col-lg-4 col-sm-6 p-2">
                                <label class="required fw-semibold fs-6 mb-2" for="end_period">End Period</label>
                                <div class="input-group">
                                    <input type="text" class="form-control text-sm" id="end_period" name="eval_end"
                                        placeholder="Select Date" value="{{ $end_date ?? old('eval_end') }}"
                                        {{ isset($is_from_schedule) ? 'readonly style=background-color:#e9ecef;' : 'required' }}>
                                    <span class="input-group-text" id="basic-addon2"><i
                                            class="ri-calendar-todo-line"></i></span>
                                </div>
                            </div>

                            <div class="col-lg-4 col-sm-6 p-2">
                                <label class="required fw-semibold fs-6 mb-2">Purpose</label>
                                @if(isset($is_from_schedule))
                                    <input type="hidden" name="purpose" value="{{ $purpose }}">
                                    <select class="form-select select2" disabled>
                                        <option selected>{{ $purpose }}</option>
                                    </select>
                                @else
                                    <select required class="form-select select2" data-placeholder="Select an option" name="purpose" id="purpose">
                                        <option></option>
                                        <option {{ old('purpose') == 'Yearly Evaluation' ? 'selected' : '' }}>Yearly Evaluation</option>
                                        <option {{ old('purpose') == 'Employment Status' ? 'selected' : '' }}>Employment Status</option>
                                    </select>
                                @endif
                            </div>
                            @if(!isset($is_from_schedule))
                            <div class="col-lg-12">
                                <hr>
                            </div>
                            <div class="col-lg-4 col-sm-6 p-2">
                                <label class="required fw-semibold fs-6 mb-2">Department</label>
                                <select class="form-select select2" data-placeholder="Select an option" name="department_id"
                                    id="department_id" required>
                                    <option></option>
                                    <option value="ALL">ALL</option>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->id }}"
                                            {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-4 col-sm-6 p-2">
                                <label class="required fw-semibold fs-6 mb-2">Area</label>
                                <select class="form-select select2" data-placeholder="Select an option" name="area_id"
                                    id="area_id">
                                    <option value="ALL">ALL</option>
                                    @foreach ($areas as $area)
                                        <option value="{{ $area->id }}"
                                            {{ old('area_id') == $area->id ? 'selected' : '' }}>
                                            {{ $area->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-4 col-sm-6 p-2">
                                <label class="required fw-semibold fs-6 mb-2">Building / Placement</label>
                                <select class="form-select select2" data-placeholder="Select an option" name="building_id"
                                    id="building_id">
                                    <option value="ALL">ALL</option>
                                    @foreach ($buildings as $building)
                                        <option value="{{ $building->id }}"
                                            {{ old('building_id') == $building->id ? 'selected' : '' }}>
                                            {{ $building->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-4 col-sm-6 p-2">
                                <label class="required fw-semibold fs-6 mb-2">Position</label>
                                <select class="form-select select2" data-placeholder="Select an option"
                                    name="position_id" id="position_id">
                                    <option value="ALL">ALL</option>
                                    @foreach ($positions as $position)
                                        <option value="{{ $position->id }}"
                                            {{ old('position_id') == $position->id ? 'selected' : '' }}>
                                            {{ $position->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-4 col-sm-6 p-2">
                                <label class="required fw-semibold fs-6 mb-2">Section</label>
                                <select class="form-select select2" data-placeholder="Select an option" name="section_id"
                                    id="section_id">
                                    <option value="ALL">ALL</option>
                                    @foreach ($sections as $section)
                                        <option value="{{ $section->id }}"
                                            {{ old('section_id') == $section->id ? 'selected' : '' }}>
                                            {{ $section->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                            <div class="col-12">
                                <button id="multi-keep-btn" type="button" title="Keep"
                                    class="float-end btn btn-success btn-sm waves-effect waves-light position-relative btn-with-badge me-2"
                                    style="display:none;">
                                    <i class="ri-check-line fs-16"></i>
                                    <span
                                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                        <span id="keep-count">0</span>
                                        <span class="visually-hidden">keep selected</span>
                                    </span>
                                </button>
                                <button id="multi-delete-btn" type="button" title="Delete"
                                    class="float-end btn btn-danger btn-sm waves-effect waves-light position-relative btn-with-badge me-2"
                                    style="display:none;">
                                    <i class="ri-delete-bin-line fs-16"></i>
                                    <span
                                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning">
                                        <span id="delete-count">0</span>
                                        <span class="visually-hidden">delete selected</span>
                                    </span>
                                </button>
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

                            {{-- ATTACHMENT --}}
                            <div class="col-12">
                                <hr>
                                <h5 class="text-center">Attachments</h5>
                            </div>
                            <div class="col-12 p-2">
                                <div class="table-responsive">
                                    <table class="table table-borderless table-nowrap mb-0" id="attachment-table">
                                        <thead class="align-middle">
                                            <tr class="table-active">
                                                <th scope="col" style="width: 5%;">#</th>
                                                <th scope="col" style="width: 45%;">Attachment Name<span class="text-danger">*</span></th>
                                                <th scope="col" style="width: 40%;">File<span class="text-danger">*</span></th>
                                                <th scope="col" style="width: 10%;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="attachment-list">
                                            </tbody>
                                        <tbody>
                                            <tr>
                                                <td colspan="4"><a href="javascript:void(0)" id="add-attachment-item" class="btn btn-soft-secondary fw-medium"><i class="ri-add-fill me-1 align-bottom"></i> Add Attachment</a></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="col-12">
                                <hr>
                                <h5 class="text-center">Approval Information</h5>
                            </div>

                            {{-- Drafter --}}
                            <div class="row drafter-group d-none">
                                <div class="col-lg-3 p-2">
                                    <label class="fw-semibold fs-6 mb-2">Drafter <span class="text-danger">(Not Approval)</span></label>
                                    <input disabled style="cursor: not-allowed" type="text" id="drafter_name"
                                        class="form-control form-control-solid mb-3 mb-lg-0" value="" />
                                    <input type="hidden" name="drafter_id" id="drafter_id" value="">
                                </div>
                                <div class="col-lg-3 p-2">
                                    <label class="fw-semibold fs-6 mb-2">Position</label>
                                    <input disabled style="cursor: not-allowed" type="text" id="drafter_position"
                                        class="form-control form-control-solid mb-3 mb-lg-0" value="" />
                                </div>
                                <div class="col-lg-3 p-2">
                                    <label class="fw-semibold fs-6 mb-2">Email</label>
                                    <input disabled style="cursor: not-allowed" type="text" id="drafter_email"
                                        class="form-control form-control-solid mb-3 mb-lg-0" value="" />
                                </div>
                                <div class="col-lg-3 p-2">
                                    <label class="fw-semibold fs-6 mb-2">As <span class="text-danger">(Not Sign)</span></label>
                                    <input disabled style="cursor: not-allowed" type="text"
                                        class="form-control form-control-solid mb-3 mb-lg-0" value="Drafter" />
                                </div>
                            </div>

                            @php
                                $options = \App\Models\Evaluation::getApprovalOptions();
                            @endphp

                            @for ($i = 1; $i <= 6; $i++)
                                <div class="row approval-group approval-group-{{ $i }}">
                                    <div class="col-lg-3 col-sm-6 p-2">
                                        <label class="required fw-semibold fs-6 mb-2">Line Approval {{ $i }}</label>
                                        <input disabled style="cursor: not-allowed" type="text" id="approval{{ $i }}_name" class="form-control form-control-solid mb-3 mb-lg-0" value="" />
                                        <input type="hidden" name="approval{{ $i }}_id" id="approval{{ $i }}_id" value="">
                                    </div>
                                    <div class="col-lg-3 col-sm-6 p-2">
                                        <label class="fw-semibold fs-6 mb-2">Position</label>
                                        <input disabled style="cursor: not-allowed" type="text" id="approval{{ $i }}_position" class="form-control form-control-solid mb-3 mb-lg-0" value="" />
                                    </div>
                                    <div class="col-lg-3 col-sm-6 p-2">
                                        <label class="fw-semibold fs-6 mb-2">Email</label>
                                        <input disabled style="cursor: not-allowed" type="text" id="approval{{ $i }}_email" class="form-control form-control-solid mb-3 mb-lg-0" value="" />
                                    </div>
                                    <div class="col-lg-3 col-sm-6 p-2">
                                        <label class="required fw-semibold fs-6 mb-2">Sign {{ $i }} As</label>
                                        <select id="approval{{ $i }}_as" name="approval{{ $i }}_as" class="form-select" required>
                                            <option value="" disabled selected>Select an option</option>  
                                            @foreach ($options as $value)
                                                <option value="{{ $value }}">{{ $value }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endfor
                            
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2 pt-10">
                                    <button type="submit" name="status" value="DRAFT" class="btn btn-secondary">
                                        DRAFT
                                    </button>
                                    <button type="button" id="btn-release-modal" class="btn btn-success">
                                        RELEASE
                                    </button>
                                    <div class="modal fade" id="releaseModal" tabindex="-1"
                                        aria-labelledby="releaseModalLabel" aria-hidden="true" style="display: none;">
                                        <div class="modal-dialog modal-dialog-top">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="releaseModalLabel">Release Evaluation</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body text-center p-5">
                                                    <p class="text-muted">Are you sure you want to release this evaluation?
                                                    </p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" name="status" value="RELEASE"
                                                        form="evalForm" class="btn btn-success">Yes, Release</button>
                                                    <button type="button" class="btn btn-light"
                                                        data-bs-dismiss="modal">No</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
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
    <script src="{{ url('') }}/assets/libs/flatpickr/flatpickr.min.js"></script>
@endsection
@section('javascript')
    <script type="text/javascript">
        $(document).ready(function() {
            $('.select2').select2();
            let employeeTable;

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

            function updateMultiButtons() {
                const count = selectedIds.length;
                if (count > 1) {
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
                    selectedIds = [];
                    updateMultiButtons();
                    checkAllStatus();
                }
                employeeTable = $('#table_employee').DataTable({
                    data: data,
                    stateSave: false,
                    responsive: false,
                    autoWidth: false,
                    scrollX: false,
                    order: [],
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
                    const firstRow = employeeTable.row(0).node();
                    if (firstRow) {
                        $('#table_employee tbody tr').removeClass('selected-row');
                        $(firstRow).addClass('selected-row');
                        $(firstRow).trigger('click');
                    }
                });
                employeeTable.draw();
                $('#table_employee').show();
            }

            function loadEmployees() {
                const departmentId = $('#department_id').val();
                const areaId = $('#area_id').val();
                const buildingId = $('#building_id').val();
                const positionId = $('#position_id').val();
                const sectionId = $('#section_id').val();
                const lineApprovalId = $('#id').val();
                if (departmentId || lineApprovalId) {
                    Swal.fire({
                        title: 'Loading data...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading()
                        }
                    });
                    $.ajax({
                        url: "{{ route('evaluation.create-multiple.getEmployee') }}",
                        type: "GET",
                        data: {
                            department_id: departmentId,
                            area_id: areaId,
                            building_id: buildingId,
                            position_id: positionId,
                            section_id: sectionId,
                            line_approval_id: lineApprovalId
                        },
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
            
            $('#department_id, #area_id, #building_id, #position_id, #section_id').on('change', function() {
                loadEmployees();
            });
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

            let lastClickedSubmit;
            $("form button[type='submit']").on("click", function() {
                lastClickedSubmit = $(this);
                if (validateAttachments()) {
                    return false;
                }
            });

            $("form").submit(function(e) {
                e.preventDefault();

                // Check Approval As
                const approvalAsValues = {};
                let approvalAsHasDuplicate = false;
                for (let i = 1; i <= 6; i++) {
                    const approvalAsSelect = $(`#approval${i}_as`);
                    const approvalGroup = $(`.approval-group-${i}`);
                    if (approvalGroup.is(':visible') && approvalAsSelect.val()) {
                        const value = approvalAsSelect.val();
                        if (approvalAsValues[value]) {
                            approvalAsValues[value].push(`Approval ${i}`);
                            approvalAsHasDuplicate = true;
                        } else {
                            approvalAsValues[value] = [`Approval ${i}`];
                        }
                    }
                }
                if (approvalAsHasDuplicate) {
                    let warningMessage = 'Duplicate "Approval As" values found:<br>';
                    for (const value in approvalAsValues) {
                        if (approvalAsValues[value].length > 1) {
                            warningMessage +=
                                `<br><strong>${value}</strong> is used multiple times on lines: ${approvalAsValues[value].join(', ')}.`;
                        }
                    }
                    Swal.fire({
                        icon: 'warning',
                        title: 'Duplicate Approval Roles',
                        html: warningMessage,
                    });
                    return;
                }

                // Check Line Approval (CLA)
                const CLAselectedRow = employeeTable.row('.selected-row');
                if (!CLAselectedRow.any()) {
                    Swal.fire('Error', 'Please select a row to set the approval line.', 'error');
                    return;
                }
                const CLAselectedRowData = CLAselectedRow.data();
                const CLAselectedLineApprovalId = CLAselectedRowData.line_approval.id.toString();
                const CLAemployeeData = employeeTable.rows().data().toArray();
                const CLAemployeesWithDifferentApproval = [];
                for (let i = 0; i < CLAemployeeData.length; i++) {
                    const employee = CLAemployeeData[i];
                    if (!employee.line_approval || employee.line_approval.id.toString() !==
                        CLAselectedLineApprovalId) {
                        CLAemployeesWithDifferentApproval.push({
                            fullname: employee.fullname,
                            nik: employee.nik
                        });
                    }
                }
                if (CLAemployeesWithDifferentApproval.length > 0) {
                    let warningMessage = 'The following employees have different approval lines:<br><br>';
                    warningMessage +=
                        `<ul>${CLAemployeesWithDifferentApproval.map(employee => `<li style="text-align: left;">${employee.fullname} (${employee.nik})</li>`).join('')}</ul>`;
                    Swal.fire({
                        icon: 'warning',
                        title: 'Mismatched Approval Lines',
                        html: warningMessage,
                    });
                    return;
                }

                if (!this.reportValidity()) {
                    return;
                }

                const employeeIds = employeeTable.rows().data().toArray().map(row => row.id);

                if (employeeIds.length === 0) {
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
                if (lastClickedSubmit && lastClickedSubmit.attr("name") && lastClickedSubmit.attr(
                        "value")) {
                    formData.append(lastClickedSubmit.attr("name"), lastClickedSubmit.attr("value"));
                }

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
            $('#table_employee tbody').on('click', 'tr', function() {
                $('#table_employee tbody tr').removeClass('selected-row');
                $(this).addClass('selected-row');
                const rowData = employeeTable.row(this).data();
                
                if (rowData && rowData.line_approval && rowData.line_approval.drafter) {
                    const drafter = rowData.line_approval.drafter;
                    $('#drafter_name').val(drafter.fullname || '-');
                    $('#drafter_id').val(drafter.id || '');
                    $('#drafter_position').val(drafter.position?.nama || '-');
                    $('#drafter_email').val(drafter.user?.email || '-');
                    $('.drafter-group').removeClass('d-none');
                } else {
                    $('#drafter_name').val('');
                    $('#drafter_id').val('');
                    $('#drafter_position').val('');
                    $('#drafter_email').val('');
                    $('.drafter-group').addClass('d-none');
                }
                
                for (let i = 1; i <= 6; i++) {
                    const approvalKey = `approval${i}`;
                    const approvalGroup = $(`.approval-group-${i}`);
                    const approvalAsSelect = $(`#approval${i}_as`);
                    const approvalNameInput = $(`#approval${i}_name`);
                    const approvalIdInput = $(`#approval${i}_id`);
                    const approvalPositionInput = $(`#approval${i}_position`);
                    const approvalEmailInput = $(`#approval${i}_email`);
                    if (rowData && rowData.line_approval && rowData.line_approval[approvalKey]) {
                        const approvalData = rowData.line_approval[approvalKey];
                        approvalGroup.show();
                        approvalNameInput.val(approvalData.fullname || '-');
                        approvalIdInput.val(approvalData.id || '');
                        approvalPositionInput.val(approvalData.position?.nama || '-');
                        approvalEmailInput.val(approvalData.user?.email || '-');
                        approvalAsSelect.prop('required', true);
                        let autoSignAs = approvalData.default_role || '';
                        approvalAsSelect.val(autoSignAs).trigger('change');
                    } else {
                        approvalGroup.hide();
                        approvalNameInput.val('');
                        approvalIdInput.val('');
                        approvalPositionInput.val('');
                        approvalEmailInput.val('');
                        approvalAsSelect.prop('required', false);
                        approvalAsSelect.val('').trigger('change');
                    }
                }
            });

            var releaseModal = new bootstrap.Modal(document.getElementById('releaseModal'));
            $('#btn-release-modal').on('click', function() {
                if (validateAttachments()) return;
                var form = document.getElementById('evalForm');
                if (form.checkValidity() === false) {
                    form.reportValidity();
                    return;
                }
                releaseModal.show();
            });

            // FROM SCHEDULE
            @if(isset($is_from_schedule))
                startPicker.set('clickOpens', false);
                endPicker.set('clickOpens', false);
                const scheduleData = {!! $schedule_data !!};
                selectedIds = [];
                initializeDataTable(scheduleData);
            @else
                if ($('#id').val()) {
                    loadEmployees();
                }
            @endif

            // ATTACHMENT
            function addAttachmentRow(defaultName = '') {
                const currentTotalRows = $('#attachment-list tr').length;
                const newCounter = currentTotalRows + 1;
                const newRow = `
                <tr class="new-attachment-row">
                    <th scope="row" class="attachment-id">${newCounter}</th>
                    <td><div class="mb-2">
                    <input 
                        type="text" 
                        class="form-control" 
                        name="new_attachment_names[]" 
                        placeholder="e.g., Attendance List" 
                        value="${defaultName}" 
                        required>
                    </div></td>
                    <td><div class="mb-2"><input type="file" class="form-control" name="new_attachments[]" required></div></td>
                    <td class="attachment-removal"><button type="button" class="btn btn-danger remove-new-attachment-btn">Delete</button></td>
                </tr>`;
                $('#attachment-list').append(newRow);
                updateAttachmentNumbers();
            }
            function updateAttachmentNumbers() {
                $('#attachment-list tr').each(function(index) {
                    $(this).find('.attachment-id').text(index + 1);
                });
            }
            $('#add-attachment-item').on('click', function() {
                addAttachmentRow();
            });
            $(document).on('click', '.remove-new-attachment-btn', function() {
                $(this).closest('tr').remove();
                updateAttachmentNumbers();
            });
            addAttachmentRow('KPI');
            addAttachmentRow('Attendance');

            function showWarning(message) {
                Swal.fire({
                    icon: 'warning',
                    title: message,
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    }
                });
            }

            function validateAttachments() {
                let stopSubmit = false;
                $('#attachment-list tr').each(function() {
                    const nameInput = $(this).find('input[type="text"]');
                    const fileInput = $(this).find('input[type="file"]');
                    if (nameInput.length === 0 || fileInput.length === 0) return;
                    const nameValue = nameInput.val().trim();
                    const fileEmpty = fileInput[0].files.length === 0;
                    if (nameValue === '') {
                        stopSubmit = false;
                        return false;
                    }
                    if (fileEmpty) {
                        fileInput[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
                        fileInput[0].focus();
                        showWarning(`Please upload the file for "${nameValue}".`);
                        stopSubmit = true;
                        return false;
                    }
                });
                return stopSubmit;
            }
        });
        
        const endPicker = flatpickr("#end_period", {
            allowInput: true,
            altInput: false,
            dateFormat: "d/m/Y",
        });
        const startPicker = flatpickr("#start_period", {
            allowInput: true,
            altInput: false,
            dateFormat: "d/m/Y",
            onChange: function(selectedDates) {
                if (selectedDates.length > 0) {
                    const startDate = selectedDates[0];
                    const minEndDate = new Date(startDate.getTime() + 24 * 60 * 60 * 1000);
                    endPicker.set('minDate', minEndDate);
                }
            }
        });

        const startInput = document.getElementById("start_period");
        if (startInput && startInput.value) {
            const dateParts = startInput.value.split("/");
            if (dateParts.length === 3) {
                const startDate = new Date(+dateParts[2], dateParts[1] - 1, +dateParts[0]);
                const minEndDate = new Date(startDate.getTime() + 24 * 60 * 60 * 1000);
                endPicker.set('minDate', minEndDate);
            }
        }

        @if(isset($is_from_schedule))
            startPicker.set('clickOpens', false);
            endPicker.set('clickOpens', false);
        @endif
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
