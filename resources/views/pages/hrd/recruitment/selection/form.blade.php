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

        .btn-with-badge {
            overflow: visible;
        }
    </style>
@endsection

@section('content')
    @php
        $employeeData = [];
        foreach ($employees as $emp) {
            $nik = $emp->nik ?? '-';
            $position = $emp->position->nama ?? '-';
            $label = '['.$nik.'] '.$emp->fullname.' ('.$position.')';
            
            $employeeData[] = [
                'id' => $emp->id,
                'text' => $label
            ];
        }
    @endphp
    <input type="hidden" id="master_employee_data" value="{{ json_encode($employeeData) }}">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Form Selection</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Recruitment</a></li>
                        <li class="breadcrumb-item active">Selection</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    
    @php
        $candidateData = [];
        $isEditMode = isset($selection->id) && !empty($selection->id);
        if ($isEditMode && $selection->candidates) {
            foreach ($selection->candidates as $selCandidate) {
                $row = $selCandidate->candidate ?? null; 
                if (!$row) continue;
                $birthDate = $row->birthdate ? \Carbon\Carbon::parse($row->birthdate) : null;
                $age = $birthDate ? $birthDate->diff(\Carbon\Carbon::now())->format('%y Years') : '-';

                $educations = optional($row->educations)->sortByDesc('end_year');
                $eduOutput = '';
                if ($educations && $educations->count() === 1) {
                    $eduOutput = optional($educations->first())->institution_name ?? '-';
                } elseif ($educations && $educations->count() > 1) {
                    foreach ($educations as $index => $education) {
                        $nomor = $index + 1;
                        $institution = optional($education)->institution_name ?? '-';
                        $eduOutput .= "{$nomor}. {$institution}" . ($index < $educations->count() - 1 ? '<br>' : '');
                    }
                }
                $eduOutput = $eduOutput ?: '-';

                $experiences = optional($row->experiences)->sortByDesc('end_date');
                $expYearsOutput = '';
                $expPositionOutput = '';
                $expCompanyOutput = '';
                
                if ($experiences && $experiences->count() === 1) {
                    $experience = $experiences->first();
                    $expYearsOutput = optional($experience)->years ? optional($experience)->years . ' Years' : '-';
                    $expPositionOutput = optional($experience)->position ?? '-';
                    $expCompanyOutput = optional($experience)->company ?? '-';
                } elseif ($experiences && $experiences->count() > 1) {
                     foreach ($experiences as $index => $experience) {
                        $nomor = $index + 1;
                        $year = optional($experience)->years ? optional($experience)->years . ' Years' : '-';
                        $position = optional($experience)->position ?? '-';
                        $company = optional($experience)->company ?? '-';
                        
                        $expYearsOutput .= "{$nomor}. {$year}" . ($index < $experiences->count() - 1 ? '<br>' : '');
                        $expPositionOutput .= "{$nomor}. {$position}" . ($index < $experiences->count() - 1 ? '<br>' : '');
                        $expCompanyOutput .= "{$nomor}. {$company}" . ($index < $experiences->count() - 1 ? '<br>' : '');
                    }
                }
                
                $candidateData[] = [
                    'id' => $row->id,
                    'created_at_ts' => optional($row->created_at)->timestamp ?? time(),
                    'fullname' => $row->fullname ?? '-',
                    'age' => $age,
                    'edu' => $eduOutput,
                    'years_exp' => $expYearsOutput ?: '-',
                    'position' => $expPositionOutput ?: '-',
                    'company' => $expCompanyOutput ?: '-',
                    'skill' => $row->skill ?? '-',
                ];
            }
        }
    @endphp
    
    <input type="hidden" id="candidate_data_json" value="{{ json_encode($candidateData) }}">

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex justify-content-between">
                    <h3 class="card-title">Selection {{ $selection->hiringStep->masterHiring->name ?? '' }}</h3>
                    <div class="flex-shrink-0">
                        <a href="{{ route('selection.index') }}"
                            class="btn btn-primary btn-label waves-effect waves-light"><i
                                class="ri-arrow-left-fill label-icon align-middle fs-16 me-2"></i> Back</a>
                    </div>
                </div>
                <div class="card-body">
                    <form class="form" id="selectionForm" action="{{ route('selection.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        @method('POST')
                        <div class="row gy-3">
                            @php
                                $disabledAttr = $isEditMode ? 'disabled' : '';
                            @endphp
                            <input type="hidden" name="id" id="id" value="{{ $selection->id ?? '' }}">
                            <div class="col-lg-4">
                                <label class="required fw-semibold fs-6 mb-2">Requisition</label>
                                <select required class="form-select select2" data-placeholder="Select an option"
                                    name="requisition_id" id="requisition_id" {{ $disabledAttr }}>
                                    <option></option>
                                    @foreach ($requisitions as $requisition)
                                        <option value="{{ $requisition->id }}"
                                            {{ old('requisition_id', $selection->requisition_id ?? '') == $requisition->id ? 'selected' : '' }}>
                                            {{ optional($requisition->position)->nama }} {{ optional($requisition->section)->nama ?? '' }} ({{ $requisition->no_pengajuan }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-4">
                                <label class="required fw-semibold fs-6 mb-2">Step Selection</label>
                                <select required class="form-select select2" data-placeholder="Select an option" disabled
                                    name="requisition_hiring_step_id" id="requisition_hiring_step_id" {{ $disabledAttr }}>
                                    <option></option>
                                    @if($isEditMode && $selection->requisition_hiring_step_id)
                                        <option value="{{ $selection->requisition_hiring_step_id }}" selected>
                                            {{ $selection->hiringStep->masterHiring->name }}
                                        </option>
                                    @endif
                                </select>
                            </div>
                            <div class="col-lg-4">
                                <label class="required fw-semibold fs-6 mb-2">Noted</label>
                                <input type="text" name="noted" class="form-control form-control-solid mb-3" placeholder="Input your Noted"
                                    value="{{ old('noted', $selection->noted ?? '') }}" />
                            </div>
                            <div class="col-lg-6">
                                <label class="required fw-semibold fs-6 mb-2" for="scheduled_at">Schedule</label>
                                <div class="input-group">
                                    <input type="text" class="form-control text-sm " id="scheduled_at" name="scheduled_at"
                                        placeholder="Select Date"
                                        value="{{ old('scheduled_at', optional(optional($selection)->scheduled_at)->format('d/m/Y H:i') ?? '') }}" 
                                        required>
                                    <span class="input-group-text" id="basic-addon2"><i
                                            class="ri-calendar-todo-line"></i></span>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <label class="required fw-semibold fs-6 mb-2">Location</label>
                                <input type="text" name="location" class="form-control form-control-solid mb-3" placeholder="Input Location for Selection"
                                    value="{{ old('location', $selection->location ?? '') }}" required/>
                            </div>

                            <div id="candidate-section" style="display: none;">
                                <div class="col-12">
                                    <hr>
                                </div>
                                <div class="row">
                                    <div class="col-12 mb-2">
                                        <button id="multi-keep-btn" type="button" title="Keep Selected"
                                            class="btn btn-success btn-sm waves-effect waves-light position-relative btn-with-badge me-2"
                                            style="display:none; z-index: 999;">
                                            <i class="ri-check-line fs-16"></i>
                                            <span
                                                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary">
                                                <span id="keep-count">0</span>
                                                <span class="visually-hidden">keep selected</span>
                                            </span>
                                        </button>
                                        <button id="multi-delete-btn" type="button" title="Delete Selected"
                                            class="btn btn-danger btn-sm waves-effect waves-light position-relative btn-with-badge me-2"
                                            style="display:none; z-index: 999;">
                                            <i class="ri-delete-bin-line fs-16"></i>
                                            <span
                                                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary">
                                                <span id="delete-count">0</span>
                                                <span class="visually-hidden">delete selected</span>
                                            </span>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <table class="table table-striped bordered display nowrap" style="width:100%"
                                        id="table_candidate">
                                        <thead>
                                            <tr>
                                                <th style="text-align:center"><input type="checkbox" id="checkAll"></th>
                                                <th scope="col" class="hidden-column" style="display:none">Created At</th>
                                                <th scope="col" style="text-align:center">Action</th>
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
                                    <input type="hidden" name="candidate_ids" id="candidate_ids">
                                </div>
                            </div>
                            
                            <div class="col-12">
                                <hr>
                                <h5 class="text-center">Invite Employee</h5>
                            </div>

                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-borderless table-nowrap mb-0" id="employee-table">
                                        <thead class="align-middle">
                                            <tr class="table-active">
                                                <th scope="col" style="width: 5%;" class="text-center">#</th>
                                                <th scope="col" style="width: 85%;">Employee Name<span
                                                        class="text-danger">*</span></th>
                                                <th scope="col" style="width: 10%;" class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="employee-list">
                                            @if($isEditMode && $selection->employees)
                                                @foreach($selection->employees as $index => $existingEmp)
                                                    <tr class="existing-employee-row">
                                                        <th scope="row" class="employee-id text-center align-middle">{{ $index + 1 }}</th>
                                                        <td>
                                                            <div class="mb-0">
                                                                <select class="form-select employee-select" name="invited_employees[]" required style="width: 100%;">
                                                                    @foreach($employees as $emp)
                                                                        @if($existingEmp->employee_id == $emp->id)
                                                                            @php
                                                                                $label = '['.($emp->nik??'-').'] '.$emp->fullname.' ('.($emp->position->nama??'-').')';
                                                                            @endphp
                                                                            <option value="{{ $emp->id }}" selected>{{ $label }}</option>
                                                                        @endif
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </td>
                                                        <td class="employee-removal text-center align-middle">
                                                            <button type="button" class="btn btn-danger remove-existing-employee-btn">Delete</button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                        <tbody>
                                            <tr>
                                                <td colspan="3">
                                                    <a href="javascript:void(0)" id="add-employee-item"
                                                        class="btn btn-soft-secondary fw-medium"><i
                                                            class="ri-add-fill me-1 align-bottom"></i> Add Employee</a>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            

                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2 pt-10">
                                    <button type="submit" name="status" value="0" class="btn btn-secondary">DRAFT</button>
                                    <button type="button" class="btn btn-success" id="releaseButton">RELEASE</button>
                                    <div class="modal fade" id="releaseModal" tabindex="-1"
                                        aria-labelledby="releaseModalLabel" aria-hidden="true" style="display: none;">
                                        <div class="modal-dialog modal-dialog-top">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="releaseModalLabel">Release Selection</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body text-center p-5">
                                                    <p class="text-muted">Are you sure you want to release this selection?
                                                    </p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" name="status" value="1"
                                                        form="selectionForm" class="btn btn-success">Yes, Release</button>
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
            let candidateTable;
            let selectedIds = [];

            const isEditMode = $('#id').val().length > 0;
            const initialReqId = $('#requisition_id').val();
            const initialStepId = "{{ $selection->requisition_hiring_step_id ?? '' }}";
            const initialCandidateData = JSON.parse($('#candidate_data_json').val() || '[]');

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
                    candidateTable.rows({
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
                    confirmButtonText: 'Yes, Delete it!',
                    customClass: {
                        confirmButton: "btn btn-danger",
                        cancelButton: "btn btn-secondary"
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        candidateTable.rows(function(idx, data, node) {
                            return selectedIds.includes(data.id.toString());
                        }).remove().draw('full-reset');
                        selectedIds = [];
                        $('.row-checkbox').prop('checked', false);
                        updateMultiButtons();
                        checkAllStatus();
                        Swal.fire({
                            title: 'Deleted!',
                            text: `${count} candidates have been removed from the list.`,
                            icon: 'success',
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        });
                    }
                });
            });

            $(document).on('click', '#multi-keep-btn', function() {
                const count = selectedIds.length;
                if (count === 0) {
                    Swal.fire({
                        title: 'No Items Selected',
                        text: 'Please select at least one item to keep.',
                        icon: 'info'
                    });
                    return;
                }
                Swal.fire({
                    title: 'Are you sure?',
                    text: `You are about to keep ${count} selected items and delete all others.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Keep the Selected!',
                    customClass: {
                        confirmButton: "btn btn-success",
                        cancelButton: "btn btn-secondary"
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const allData = candidateTable.rows().data().toArray();
                        const toKeepData = allData.filter(row => selectedIds.includes(row.id
                            .toString()));
                        candidateTable.clear();
                        candidateTable.rows.add(toKeepData);
                        candidateTable.draw('full-reset');
                        selectedIds = [];
                        $('.row-checkbox').prop('checked', false);
                        updateMultiButtons();
                        checkAllStatus();
                        Swal.fire({
                            title: 'Success!',
                            text: `Only the ${count} selected candidates have been kept. The others are removed.`,
                            icon: 'success',
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        });
                    }
                });
            });

            $('#table_candidate').on('click', '.remove-candidate', function() {
                const row = candidateTable.row($(this).parents('tr'));
                const rowId = row.data().id.toString();
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Delete it!',
                    customClass: {
                        confirmButton: "btn btn-danger",
                        cancelButton: "btn btn-secondary"
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        row.remove().draw('full-reset');
                        selectedIds = selectedIds.filter(val => val !== rowId);
                        updateMultiButtons();
                        checkAllStatus();
                        Swal.fire({
                            title: 'Deleted!',
                            text: 'Candidate has been removed from the list.',
                            icon: 'success',
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        });
                    }
                });
            });

            function initializeDataTable(data) {
                if ($.fn.DataTable.isDataTable('#table_candidate')) {
                    candidateTable.destroy();
                    selectedIds = [];
                    updateMultiButtons();
                    checkAllStatus();
                }
                const tableData = Array.isArray(data) ? data : [];
                candidateTable = $('#table_candidate').DataTable({
                    data: tableData,
                    stateSave: false,
                    responsive: false,
                    autoWidth: false,
                    scrollX: true,
                    order: [1, 'desc'],
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
                            data: 'created_at_ts',
                            name: 'created_at',
                            className: 'hidden-column',
                            visible: false,
                            searchable: false,
                        },
                        {
                            data: 'id',
                            name: 'action',
                            className: "text-center",
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row, meta) {
                                return `<button type="button" class="btn btn-sm btn-danger remove-candidate" data-id="${data}" title="Hapus Kandidat">
                                    <i class="ri-delete-bin-line"></i>
                                </button>`;
                            }
                        },
                        {
                            data: 'fullname',
                            name: 'fullname',
                            defaultContent: '-'
                        },
                        {
                            data: 'age',
                            name: 'age',
                            defaultContent: '-'
                        },
                        {
                            data: 'edu',
                            name: 'edu',
                            defaultContent: '-'
                        },
                        {
                            data: 'years_exp',
                            name: 'years_exp',
                            defaultContent: '-'
                        },
                        {
                            data: 'position',
                            name: 'position',
                            defaultContent: '-'
                        },
                        {
                            data: 'company',
                            name: 'company',
                            defaultContent: '-'
                        },
                        {
                            data: 'skill',
                            name: 'skill',
                            defaultContent: '-'
                        }
                    ],
                });
                candidateTable.on('draw', function() {
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
                const tableContainer = document.querySelector('#table_candidate').closest('.col-12');
                if (tableContainer) {
                    new ResizeObserver(() => {
                        if ($.fn.DataTable.isDataTable('#table_candidate')) {
                            candidateTable.columns.adjust();
                        }
                    }).observe(tableContainer);
                }
            }

            function loadCandidatesForCreation(requisitionId, stepId) {
                const candidateSection = $('#candidate-section');
                if (!requisitionId || !stepId) {
                    candidateSection.hide();
                    if ($.fn.DataTable.isDataTable('#table_candidate')) {
                        candidateTable.clear().draw();
                    }
                    return;
                }

                candidateSection.show();
                initializeDataTable([]);

                Swal.fire({
                    title: 'Loading data...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                const url = '{{ route("selection.getCandidates") }}'; 

                $.ajax({
                    url: url,
                    type: "POST",
                    data: {
                        requisition_id: requisitionId,
                        step_id: stepId,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: "json",
                    success: function(response) {
                        Swal.close();
                        if (response.data && Array.isArray(response.data)) {
                             candidateTable.rows.add(response.data).draw('full-reset');
                        }
                        selectedIds = [];
                        updateMultiButtons();
                        checkAllStatus();
                    },
                    error: function(xhr, status, error) {
                        Swal.close();
                        Swal.fire('Error', 'Failed load candidate.', 'error');
                        candidateTable.clear().draw();
                    }
                });
            }


            function loadHiringSteps(requisitionId, initialStepId = null) {
                const stepSelect = $('#requisition_hiring_step_id');
                if (isEditMode) {
                    $('#candidate-section').show();
                    return; 
                }
                
                stepSelect.empty().append('<option></option>').prop('disabled', true).trigger('change');
                if (!requisitionId) {
                    $('#candidate-section').hide();
                    return;
                }
                
                const url = '{{ route("selection.getSteps", ["requisition" => ":id"]) }}'.replace(':id', requisitionId);
                $.ajax({
                    url: url,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                        stepSelect.empty().append('<option></option>');
                        if (data.length > 0) {
                            $.each(data, function(key, value) {
                                const isSelected = initialStepId && (value.id.toString() === initialStepId.toString());
                                stepSelect.append(`<option value="${value.id}" ${isSelected ? 'selected' : ''}>${value.text}</option>`);
                            });
                            stepSelect.prop('disabled', false).trigger('change');
                            if (!isEditMode && stepSelect.val()) {
                                loadCandidatesForCreation(requisitionId, stepSelect.val());
                            }
                        } else {
                            $('#candidate-section').hide();
                        }
                        stepSelect.select2({ placeholder: "Select an option" });
                    },
                    error: function() {
                        Swal.fire('Error', 'Failed step selection.', 'error');
                        stepSelect.select2({ placeholder: "Select an option" });
                        $('#candidate-section').hide();
                    }
                });
            }

            $('#requisition_id').on('change', function() {
                const reqId = $(this).val();
                if (!isEditMode) {
                    loadHiringSteps(reqId);
                }
            });

            if (!isEditMode) {
                $('#requisition_hiring_step_id').on('change', function() {
                    const reqId = $('#requisition_id').val();
                    const stepId = $(this).val();
                    loadCandidatesForCreation(reqId, stepId);
                });
            }

            if (isEditMode) {
                if (initialReqId && initialStepId) {
                    $('#candidate-section').show();
                    initializeDataTable(initialCandidateData);
                    loadHiringSteps(initialReqId, initialStepId);
                } else {
                    $('#candidate-section').hide();
                    initializeDataTable([]);
                }
            } else {
                $('#candidate-section').hide();
                initializeDataTable([]);
                if (initialReqId) {
                    loadHiringSteps(initialReqId, initialStepId);
                }
            }

            $("form").submit(function(e) {
                e.preventDefault();
                if (!this.reportValidity()) {
                    return;
                }

                if (!candidateTable || candidateTable.rows().count() === 0) {
                    Swal.fire('Error', 'Please select at least one candidate.', 'error');
                    return;
                }

                let candidateIds = candidateTable.rows().data().toArray().map(row => row.id);
                $('#candidate_ids').val(JSON.stringify(candidateIds));
                
                const formData = new FormData(this);
                if (isEditMode) {
                    formData.delete('requisition_id');
                    formData.delete('requisition_hiring_step_id');
                }

                const submitter = e.originalEvent.submitter;
                if (submitter && submitter.name === 'status') {
                    formData.append('status', submitter.value);
                }

                Swal.fire({
                    title: 'Saving data...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: $(this).attr("action"),
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        Swal.close();
                        Swal.fire({
                            title: "Sukses",
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
                if (responseJson && responseJson.message) {
                    errorMessage += `<h4 class="text-danger">${responseJson.message}</h4>`;
                }
                if (responseJson && responseJson.errors) {
                    for (const fieldName in responseJson.errors) {
                        errorMessage += `<p class="text-danger">${responseJson.errors[fieldName][0]}</p>`;
                    }
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

            $('#releaseButton').on('click', function(e) {
                e.preventDefault();
                if (!$('#selectionForm')[0].checkValidity()) {
                    $('#selectionForm')[0].reportValidity();
                    return;
                }
                if (!candidateTable || candidateTable.rows().count() === 0) {
                    Swal.fire('Error', 'Please select at least one candidate.', 'error');
                    return;
                }
                $('#releaseModal').modal('show');
            });
            $('#scheduled_at').flatpickr({
                allowInput: true,
                enableTime: true,
                dateFormat: "d/m/Y H:i",
                minDate: "today",
                time_24hr: true,
            });

        });
    </script>
    <script>
        $(document).ready(function() {
            const rawData = $('#master_employee_data').val();
            const masterEmployees = rawData ? JSON.parse(rawData) : [];
            function renderAllDropdowns() {
                let allSelectedIds = [];
                $('.employee-select').each(function() {
                    const val = $(this).val();
                    if (val) allSelectedIds.push(parseInt(val));
                });

                $('.employee-select').each(function() {
                    const $select = $(this);
                    const currentValue = $select.val() ? parseInt($select.val()) : null;
                    $select.empty();
                    $select.append('<option value=""></option>');
                    masterEmployees.forEach(function(emp) {
                        if (!allSelectedIds.includes(emp.id) || emp.id === currentValue) {
                            const isSelected = (emp.id === currentValue) ? 'selected' : '';
                            const newOption = new Option(emp.text, emp.id, false, emp.id === currentValue);
                            $select.append(newOption);
                        }
                    });
                    $select.trigger('change.select2'); 
                });
            }

            function addEmployeeRow() {
                const $lastRowSelect = $('#employee-list tr:last').find('.employee-select');
                if ($lastRowSelect.length > 0) {
                    const nativeSelect = $lastRowSelect[0];
                    if (!nativeSelect.checkValidity()) {
                        nativeSelect.reportValidity();
                        return;
                    }
                }

                const currentTotalRows = $('#employee-list tr').length;
                const newCounter = currentTotalRows + 1;
                const newRow = `
                    <tr class="new-employee-row">
                        <th scope="row" class="employee-id text-center align-middle">${newCounter}</th>
                        <td>
                            <div class="mb-0">
                                <select class="form-select employee-select" name="invited_employees[]" required style="width: 100%;">
                                    <option value=""></option>
                                </select>
                            </div>
                        </td>
                        <td class="employee-removal text-center align-middle">
                            <button type="button" class="btn btn-danger remove-new-employee-btn">Delete</button>
                        </td>
                    </tr>
                `;
                $('#employee-list').append(newRow);
                
                const $newSelect = $('#employee-list .employee-select').last();
                $newSelect.select2({
                    placeholder: "Select Employee",
                    width: '100%',
                });
                renderAllDropdowns();
                updateEmployeeNumbers();
            }

            function updateEmployeeNumbers() {
                $('#employee-list tr').each(function(index) {
                    $(this).find('.employee-id').text(index + 1);
                });
            }

            $('#add-employee-item').on('click', function() {
                addEmployeeRow();
            });

            $(document).on('click', '.remove-existing-employee-btn, .remove-new-employee-btn', function() {
                $(this).closest('tr').remove();
                updateEmployeeNumbers();
                renderAllDropdowns();
            });

            $(document).on('select2:select select2:unselect', '.employee-select', function(e) {
                renderAllDropdowns();
            });

            $('.employee-select').select2({
                placeholder: "Select Employee",
                width: '100%'
            });

            if ($('.employee-select').length > 0) {
                renderAllDropdowns();
            }            
            updateEmployeeNumbers();

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