@extends('layouts.master')
@section('link')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css" />
    <!-- Select2-->
    <link rel="stylesheet" href="{{ url('') }}/assets/libs/adminlte/select2/css/4.1.0/select2.min.css">
    <link href="{{ url('') }}/assets/libs/Datatables/DataTables-1.13.1/css/dataTables.bootstrap5.css" rel="stylesheet"
        type="text/css" />
    <link href="{{ url('') }}/assets/libs/Datatables/Buttons-2.3.3/css/buttons.dataTables.css" rel="stylesheet"
        type="text/css" />
    <link href="{{ url('') }}/assets/libs/Datatables/Responsive-2.4.0/css/responsive.bootstrap.css" rel="stylesheet"
        type="text/css" />
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

        :disabled {
            cursor: not-allowed;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Form Evaluation</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Evaluation</a></li>
                        <li class="breadcrumb-item active">Form</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header align-items-center d-flex justify-content-between">
                    <h3 class="card-title">Evaluation {{ $evaluation->employee->fullname ?? '' }}</h3>
                    <div class="flex-shrink-0">
                        <a href="{{ route('evaluation.index') }}"
                            class="btn btn-primary btn-label waves-effect waves-light"><i
                                class="ri-arrow-left-fill label-icon align-middle fs-16 me-2"></i> Back</a>
                    </div>
                </div><!-- end card header -->
                <div class="card-body">
                    <form class="form" id="evalForm" action="{{ route('evaluation.store') }}" method="post"
                        enctype="multipart/form-data">
                        @csrf
                        @method('POST')
                        <div class="row gy-3">
                            <input type="hidden" name="id" id="id" value="{{ $evaluation->id ?? '' }}">

                            <div class="col-12">
                                <h5 class="text-center">Employee Information</h5>
                            </div>

                            <div class="col-lg-4 col-sm-6 p-2">
                                <label class="required fw-semibold fs-6 mb-2" for="employee_id">Full Name</label>
                                <select class="form-select form-control select2" data-placeholder="Select an option"
                                    name="employee_id" id="employee_id"
                                    data-selected-appraisal="{{ $evaluation->appraisal_id ?? '' }}" required>
                                    <option></option>
                                    @foreach ($employee as $e)
                                        @php
                                            $isPermanent = strtoupper($e->employee->status ?? '') === 'PERMANENT';
                                            $startDate = $isPermanent ? '01/01/' . date('Y') : ($e->employee->contract_startdate ? \Carbon\Carbon::parse($e->employee->contract_startdate)->format('d/m/Y') : '');
                                            $endDate = $isPermanent ? '31/12/' . date('Y') : ($e->employee->enddate ? \Carbon\Carbon::parse($e->employee->enddate)->format('d/m/Y') : '');
                                            $purposeType = $isPermanent ? 'Yearly Evaluation' : 'Employment Status';
                                        @endphp
                                        <option value="{{ $e->employee->id }}" 
                                            data-nik="{{ $e->employee->nik }}"
                                            data-gender="{{ $e->employee->gender }}"
                                            data-dept="{{ $e->employee->department->name ?? '' }}"
                                            data-sect="{{ $e->employee->section->nama ?? '' }}"
                                            data-build="{{ $e->employee->building->nama ?? '' }}"
                                            data-start-date="{{ $startDate }}"
                                            data-end-date="{{ $endDate }}"
                                            data-type="{{ $purposeType }}"
                                            {{ old('employee_id', $evaluation->employee_id ?? '') == $e->employee->id ? 'selected' : '' }}>
                                            {{ $e->employee->fullname }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-lg-4 col-sm-6 p-2">
                                <label class="fw-semibold fs-6 mb-2">Employee ID (NIK)</label>
                                <input disabled type="text" id="nik"
                                    class="form-control form-control-solid mb-3 mb-lg-0" value="" />
                            </div>

                            <div class="col-lg-4 col-sm-6 p-2">
                                <label class="fw-semibold fs-6 mb-2">Gender</label>
                                <input disabled type="text" id="gender"
                                    class="form-control form-control-solid mb-3 mb-lg-0" value="" />
                            </div>

                            <div class="col-lg-4 col-sm-6 p-2">
                                <label class="fw-semibold fs-6 mb-2">Position</label>
                                <input disabled type="text"
                                    id="appraisal_position_display" class="form-control form-control-solid mb-3 mb-lg-0"
                                    value="" />
                                <input type="hidden" name="appraisal_id" id="appraisal_id"
                                    value="{{ $evaluation->appraisal_id ?? '' }}">
                                <input type="hidden" name="appraisal_position_id" id="appraisal_position_id"
                                    value="{{ $evaluation->appraisal_position_id ?? '' }}">
                            </div>

                            <div class="col-lg-4 col-sm-6 p-2">
                                <label class="fw-semibold fs-6 mb-2">Status</label>
                                <input disabled type="text" id="position_status"
                                    class="form-control form-control-solid mb-3 mb-lg-0" value="" />
                                <input type="hidden" name="appraisal_status" id="appraisal_status"
                                    value="{{ $evaluation->appraisal_status ?? '' }}">
                            </div>

                            <div class="col-lg-4 col-sm-6 p-2">
                                <label class="fw-semibold fs-6 mb-2">Department</label>
                                <input disabled type="text" id="department"
                                    class="form-control form-control-solid mb-3 mb-lg-0" 
                                    value="" />
                            </div>

                            <div class="col-lg-4 col-sm-6 p-2">
                                <label class="fw-semibold fs-6 mb-2">Section</label>
                                <input disabled type="text" id="section"
                                    class="form-control form-control-solid mb-3 mb-lg-0"
                                    value="" />
                            </div>

                            <div class="col-lg-4 col-sm-6 p-2">
                                <label class="fw-semibold fs-6 mb-2">Building</label>
                                <input disabled type="text" id="building"
                                    class="form-control form-control-solid mb-3 mb-lg-0"
                                    value="" />
                            </div>

                            <div class="col-12">
                                <hr>
                                <h5 class="text-center">Evaluation Information</h5>
                            </div>

                            <div class="col-lg-4 col-sm-6 p-2">
                                <label class="required fw-semibold fs-6 mb-2" for="start_period">Start Period</label>
                                <div class="input-group">
                                    <input type="text" class="form-control text-sm " id="start_period"
                                        name="eval_start" placeholder="Select Date"
                                        value="{{ old('eval_start', optional($evaluation)->eval_start ? optional($evaluation)->eval_start->format('d/m/Y') : '') }}"
                                        required>
                                    <span class="input-group-text" id="basic-addon2"><i
                                            class="ri-calendar-todo-line"></i></span>
                                </div>
                            </div>

                            <div class="col-lg-4 col-sm-6 p-2">
                                <label class="required fw-semibold fs-6 mb-2" for="end_period">End Period</label>
                                <div class="input-group">
                                    <input type="text" class="form-control text-sm " id="end_period" name="eval_end"
                                        placeholder="Select Date"
                                        value="{{ old('eval_end', optional($evaluation)->eval_end ? optional($evaluation)->eval_end->format('d/m/Y') : '') }}"
                                        required>
                                    <span class="input-group-text" id="basic-addon2"><i
                                            class="ri-calendar-todo-line"></i></span>
                                </div>
                            </div>

                            <div class="col-lg-4 col-sm-6 p-2">
                                <label class="required fw-semibold fs-6 mb-2">Purpose</label>
                                <select required class="form-select select2" data-placeholder="Select an option"
                                    name="purpose" id="purpose">
                                    <option></option>
                                    <option
                                        {{ old('purpose', $evaluation->purpose ?? '') == 'Yearly Evaluation' ? 'selected' : '' }}>
                                        Yearly Evaluation</option>
                                    <option
                                        {{ old('purpose', $evaluation->purpose ?? '') == 'Employment Status' ? 'selected' : '' }}>
                                        Employment Status</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <hr>
                                <h5 class="text-center">Approval Information</h5>
                            </div>

                            <div class="row drafter-group d-none">
                                <div class="col-lg-3 p-2">
                                    <label class="fw-semibold fs-6 mb-2">Drafter <span class="text-danger">(Not Approval)</span></label>
                                    <input disabled type="text" id="drafter_name"
                                        class="form-control form-control-solid mb-3 mb-lg-0"
                                        value="{{ $evaluation->drafter->fullname ?? '' }}" />
                                    <input type="hidden" name="drafter_id" id="drafter_id" 
                                        value="{{ old('drafter_id', $evaluation->drafter_id ?? '') }}">
                                </div>
                                <div class="col-lg-3 p-2">
                                    <label class="fw-semibold fs-6 mb-2">Position</label>
                                    <input disabled type="text" id="drafter_position"
                                        class="form-control form-control-solid mb-3 mb-lg-0"
                                        value="{{ $evaluation->drafter->position->nama ?? '' }}" />
                                </div>
                                <div class="col-lg-3 p-2">
                                    <label class="fw-semibold fs-6 mb-2">Email</label>
                                    <input disabled type="text" id="drafter_email"
                                        class="form-control form-control-solid mb-3 mb-lg-0"
                                        value="{{ $evaluation->drafter->user->email ?? '-' }}" />
                                </div>
                                <div class="col-lg-3 p-2">
                                    <label class="fw-semibold fs-6 mb-2">As <span class="text-danger">(Not Sign)</span></label>
                                    <input disabled type="text"
                                        class="form-control form-control-solid mb-3 mb-lg-0"
                                        value="Drafter" />
                                </div>
                            </div>

                            @php
                                $options = \App\Models\Evaluation::getApprovalOptions();
                            @endphp

                            @for ($i = 1; $i <= 6; $i++)
                                <div class="row approval-group approval-group-{{ $i }}">
                                    <div class="col-lg-3 col-sm-6 p-2">
                                        <label class="required fw-semibold fs-6 mb-2">Line Approval {{ $i }}</label>
                                        <input disabled type="text" id="approval{{ $i }}_name" class="form-control form-control-solid mb-3 mb-lg-0" value="" />
                                        <input type="hidden" name="approval{{ $i }}_id" id="approval{{ $i }}_id" value="{{ old('approval' . $i . '_id', $evaluation->{'approval' . $i . '_id'} ?? '') }}">
                                    </div>
                                    <div class="col-lg-3 col-sm-6 p-2">
                                        <label class="fw-semibold fs-6 mb-2">Position</label>
                                        <input disabled type="text" id="approval{{ $i }}_position" class="form-control form-control-solid mb-3 mb-lg-0" value="" />
                                    </div>
                                    <div class="col-lg-3 col-sm-6 p-2">
                                        <label class="fw-semibold fs-6 mb-2">Email</label>
                                        <input disabled type="text" id="approval{{ $i }}_email" class="form-control form-control-solid mb-3 mb-lg-0" value="" />
                                    </div>
                                    <div class="col-lg-3 col-sm-6 p-2">
                                        <label class="required fw-semibold fs-6 mb-2">Sign {{ $i }} As</label>
                                        <select id="approval{{ $i }}_as" name="approval{{ $i }}_as" class="form-select" required
                                            data-saved-value="{{ old('approval' . $i . '_as', $evaluation->{'approval' . $i . '_as'} ?? '') }}">
                                            <option value="" disabled selected>Select an option</option>  
                                            @php
                                                $approverUser = $evaluation->{'approval' . $i} ?? null;
                                                $positionName = $approverUser->position->nama ?? '';
                                                $defaultValue = \App\Models\Evaluation::getDefaultApprovals($positionName);
                                            @endphp
                                            @foreach ($options as $value)
                                                @php
                                                    $isSelected = old('approval' . $i . '_as', $evaluation->{'approval' . $i . '_as'} ?? $defaultValue) == $value;
                                                @endphp
                                                <option value="{{ $value }}" {{ $isSelected ? 'selected' : '' }}>
                                                    {{ $value }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endfor

                            {{-- Evaluation Aspect Section --}}
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row mb-3">
                                            <div class="col-12 text-center">
                                                <h4 class="text-primary">Evaluation Aspect</h4>
                                                <h6 class="card-subtitle">Decision is a Rights & Authority Board Of
                                                    Directors
                                                </h6>
                                            </div>
                                        </div>
                                        <div class="row">
                                            {{-- 1. KPI --}}
                                            <div class="col-12 p-2 kpi-section d-none">
                                                <div class="card">
                                                    <div class="card-header fw-bold text-center text-uppercase">1. KEY
                                                        PERFORMANCE INDICATOR (KPI)</div>
                                                    <div class="card-body row">
                                                        {{-- Achievement --}}
                                                        <div class="col-md-4 mb-3">
                                                            <label class="required fw-semibold fs-6 mb-2"
                                                                for="kpi_s">Achievement</label>
                                                            <div class="input-group">
                                                                <input type="number" id="kpi_s" name="kpi_s"
                                                                    class="form-control" placeholder="0 - 100"
                                                                    min="0" max="100" step="0.01" 
                                                                    value="{{ old('kpi_s', $evaluation->kpi_s ?? '') }}">
                                                                <span class="input-group-text">%</span>
                                                            </div>
                                                        </div>
                                                        {{-- Score --}}
                                                        <div class="col-md-4 mb-3">
                                                            <label class="fw-semibold fs-6 mb-2"
                                                                for="kpi_score">Score</label>
                                                            <input type="text" id="kpi_score" class="form-control"
                                                                disabled value="{{ $evaluation->kpi_score ?? '' }}">
                                                            <input type="hidden" id="TKPI_score" name="kpi_sc"
                                                                value="{{ old('kpi_sc', $evaluation->kpi_sc ?? '') }}">
                                                            <input type="hidden" name="kpi_w" id="kpi_w"
                                                                value="">
                                                        </div>
                                                        {{-- Comment --}}
                                                        <div class="col-md-4 mb-3">
                                                            <label class="required fw-semibold fs-6 mb-2"
                                                                for="kpi_c">Comment <span class="text-danger">(Max. <span id="count_kpi_c">60</span> Character)</span></label>
                                                            <input type="text" id="kpi_c" name="kpi_c"
                                                                class="form-control" placeholder="Your Comment"
                                                                maxlength="35"
                                                                value="{{ old('kpi_c', $evaluation->kpi_c ?? '') }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- 3. Attendance --}}
                                            <div class="col-12 p-2">
                                                <div class="card">
                                                    <div class="card-header fw-bold text-center text-uppercase">3.
                                                        ATTENDANCE
                                                    </div>
                                                    <div class="card-body row">
                                                        <div class="col-md-4 mb-3">
                                                            <label class="required fw-semibold fs-6 mb-2"
                                                                for="attendance_s">Achievement</label>
                                                            <div class="input-group">
                                                                <input type="number" id="attendance_s"
                                                                    name="attendance_s" class="form-control"
                                                                    placeholder="0 - 100" min="0" max="100"
                                                                    step="any"
                                                                    value="{{ old('attendance_s', $evaluation->attendance_s ?? '') }}">
                                                                <span class="input-group-text">%</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label class="fw-semibold fs-6 mb-2"
                                                                for="attendance_score">Score</label>
                                                            <input type="text" id="attendance_score"
                                                                class="form-control" disabled
                                                                value="{{ $evaluation->attendance_score ?? '' }}">
                                                            <input type="hidden" id="TAT_score" name="attendance_sc"
                                                                value="{{ old('attendance_sc', $evaluation->attendance_sc ?? '') }}">
                                                            <input type="hidden" name="attendance_w" id="attendance_w"
                                                                value="">
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label class="required fw-semibold fs-6 mb-2"
                                                                for="attendance_c">Comment <span class="text-danger">(Max. <span id="count_attendance_c">60</span> Character)</span></label>
                                                            <input type="text" id="attendance_c" name="attendance_c"
                                                                class="form-control" placeholder="Your Comment"
                                                                maxlength="35"
                                                                value="{{ old('attendance_c', $evaluation->attendance_c ?? '') }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- End Evaluation Aspect Section --}}

                            <div class="col-12">
                                <hr>
                                <h5 class="text-center mb-0">Note from HRD</h5>
                                <p class="text-center text-danger mt-0 mb-2">(Max. <span id="count_note_hrd">100</span> Character)</p>
                                <textarea class="form-control" id="note_hrd" name="note_hrd" rows="3" maxlength="100">{{ old('note_hrd', $evaluation->note_hrd ?? '') }}</textarea>
                            </div>

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
                                                <th scope="col" style="width: 45%;">Attachment Name<span
                                                        class="text-danger">*</span></th>
                                                <th scope="col" style="width: 40%;">File<span
                                                        class="text-danger">*</span></th>
                                                <th scope="col" style="width: 10%;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="attachment-list">
                                            @if (isset($evaluation) && $evaluation->attachments && $evaluation->attachments->count() > 0)
                                                @foreach ($evaluation->attachments as $index => $attachment)
                                                    <tr class="existing-attachment-row">
                                                        <th scope="row" class="attachment-id">{{ $index + 1 }}</th>
                                                        <td>
                                                            <div class="mb-2">
                                                                <input type="text" class="form-control"
                                                                    name="existing_attachment_names[{{ $attachment->id }}]"
                                                                    value="{{ $attachment->name }}" required>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="mb-2">
                                                                <a href="{{ asset('storage/' . $attachment->file_path) }}"
                                                                    class="btn btn-primary" target="_blank">View File</a>
                                                            </div>
                                                        </td>
                                                        <td class="attachment-removal">
                                                            <button type="button"
                                                                class="btn btn-danger remove-existing-attachment-btn"
                                                                data-attachment-id="{{ $attachment->id }}">Delete</button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                        <tbody>
                                            <tr>
                                                <td colspan="4">
                                                    <a href="javascript:void(0)" id="add-attachment-item"
                                                        class="btn btn-soft-secondary fw-medium"><i
                                                            class="ri-add-fill me-1 align-bottom"></i> Add Attachment</a>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="col-12">
                                <hr>
                            </div>

                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2 pt-10">
                                    @if (isset($evaluation) && $evaluation->status === 'REVISE' && !is_null($evaluation->release_id))
                                        <button type="button" class="btn btn-dark" data-bs-toggle="modal"
                                            data-bs-target="#rejectModal">
                                            REJECT
                                        </button>
                                        <div class="modal fade" id="rejectModal" tabindex="-1"
                                            aria-labelledby="rejectModalLabel" aria-hidden="true" style="display: none;">
                                            <div class="modal-dialog modal-dialog-top">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="rejectModalLabel">Reject Evaluation
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body text-center p-5">
                                                        <p class="text-muted">Are you sure you want to reject this
                                                            evaluation? This action cannot be undone and the evaluation will
                                                            no longer be editable or deletable.</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" name="status" value="REJECT"
                                                            form="evalForm" class="btn btn-dark">Yes, Reject</button>
                                                        <button type="button" class="btn btn-light"
                                                            data-bs-dismiss="modal">No</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    <button type="submit" name="status" value="DRAFT" class="btn btn-secondary">
                                        DRAFT
                                    </button>
                                    <button type="button" class="btn btn-success" id="releaseButton">
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
        <!--end col-->
    </div>
    <!--end row-->
    @if (isset($evaluation) && $evaluation->evaluationHistories->isNotEmpty())
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header align-items-center d-flex justify-content-between">
                        <h3 class="card-title">Evaluation History</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped bordered display nowrap" style="width:100%"
                            id="table_evaluation_history">
                            <thead>
                                <tr>
                                    <th scope="col" class="text-center">Date Time</th>
                                    <th scope="col" class="text-center">User</th>
                                    <th scope="col" class="text-center">IP Address</th>
                                    <th scope="col" class="text-center">Action</th>
                                    <th scope="col">Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($evaluation->evaluationHistories->sortByDesc('created_at') as $history)
                                    <tr>
                                        <td class="text-center">{{ $history->created_at->format('Y-m-d H:i:s') }}</td>
                                        <td class="text-center">{{ $history->user->name }}</td>
                                        <td class="text-center">{{ $history->ip_address }}</td>
                                        <td class="text-center">
                                            @php
                                                $action = $history->action;
                                                $badges = [
                                                    'RELEASE' => 'success',
                                                    'DRAFT' => 'secondary',
                                                    'REVISE' => 'danger',
                                                    'REJECT' => 'dark',
                                                    'CANCEL' => 'dark',
                                                    '1st Evaluator' => 'success',
                                                    '2nd Evaluator' => 'success',
                                                    '3rd Evaluator' => 'success',
                                                    'HRD Approved' => 'success',
                                                    'Prodir' => 'success',
                                                    'Presdir' => 'success',
                                                    'DONE' => 'success',
                                                ];
                                                $displayText = ($action === 'RELEASE') ? 'HRD' : $action;
                                            @endphp
                                            @if (isset($badges[$action]))
                                                <span class="badge text-bg-{{ $badges[$action] }}">{{ $displayText }}</span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $history->description }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
@section('script')
    <!-- profile-setting init js -->
    <script src="{{ url('') }}/assets/js/pages/profile-setting.init.js"></script>
    <!-- Select2 -->
    <script src="{{ url('') }}/assets/libs/adminlte/select2/js/4.1.0/select2.min.js"></script>
    <script src="{{ url('') }}/assets/libs/flatpickr/flatpickr.min.js"></script>
    <script src="{{ url('') }}/assets/libs/choices.js/public/assets/scripts/choices.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.js"></script>
    <script src="{{ url('') }}/assets/libs/Datatables/DataTables-1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="{{ url('') }}/assets/libs/Datatables/DataTables-1.13.1/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ url('') }}/assets/libs/Datatables/Responsive-2.4.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="{{ url('') }}/assets/js/pages/datatables.init.js"></script>
@endsection
@section('javascript')
    @if (isset($evaluation) && $evaluation->evaluationHistories->isNotEmpty())
        <script type="text/javascript">
            $(document).ready(function() {
                $('#table_evaluation_history').DataTable({
                    stateSave: false,
                    responsive: false,
                    autoWidth: false,
                    scrollX: true,
                    order: [
                        [0, 'desc']
                    ]
                });
            });
        </script>
    @endif
    <script type="text/javascript">
        $(function() {
            $('.select2').select2()
        });
        $(document).ready(function() {
            let lastClickedSubmit;
            $("form button[type='submit']").on("click", function() {
                lastClickedSubmit = $(this);
            });
            $("form").submit(function(e) {
                e.preventDefault();
                const appraisalSelect = $('#appraisal_id');
                if (appraisalSelect.prop('disabled') || !appraisalSelect.val()) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Invalid Appraisal Position',
                        text: 'Please select a valid Appraisal before Submitting',
                    });
                    return;
                }
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
                    let warningMessage = 'Duplicate "Sign As" values found:<br>';
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
                if (!this.reportValidity()) {
                    return;
                }
                swalert = Swal.fire({
                    title: 'Loading!',
                    didOpen: () => {
                        Swal.showLoading()
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
                        swalert.hideLoading();
                        swalert.update({
                            title: "Success",
                            text: response.message,
                            icon: "success",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: {
                                popup: 'swal2-noanimation',
                                confirmButton: "btn btn-primary"
                            }
                        });
                        swalert.then(() => {
                            window.location.href = response.redirect;
                        });
                    },
                    error: function(xhr, status, error) {
                        swalert.hideLoading();
                        $("#loadingSpinner").hide();
                        console.log({
                            xhr,
                            status,
                            error
                        });
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

                swalert.update({
                    title: 'Error',
                    html: errorMessage,
                    icon: 'error',
                    buttonsStyling: false,
                    confirmButtonText: 'Ok',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    }
                });
            }

        });

        $(document).ready(function() {
            $('#employee_id').on('change', function() {
                const selected = $(this).find(':selected');
                const nik = selected.data('nik');
                const gender = selected.data('gender');
                const dept = selected.data('dept');
                const sect = selected.data('sect');
                const build = selected.data('build');
                const startDate = selected.data('start-date');
                const endDate = selected.data('end-date');
                const purposeType = selected.data('type');
                $('#nik').val(nik);
                $('#gender').val(gender);
                $('#department').val(dept);
                $('#section').val(sect);
                $('#building').val(build);
                const startInput = document.getElementById('start_period');
                const endInput = document.getElementById('end_period');
                if (startDate) {
                    startInput.value = startDate;
                    if (startInput._flatpickr) {
                        startInput._flatpickr.setDate(startDate, true, "d/m/Y");
                    }
                } else {
                    startInput.value = '';
                    if (startInput._flatpickr) startInput._flatpickr.clear();
                }
                if (endDate) {
                    endInput.value = endDate;
                    if (endInput._flatpickr) {
                        endInput._flatpickr.setDate(endDate, true, "d/m/Y");
                    }
                } else {
                    endInput.value = '';
                    if (endInput._flatpickr) endInput._flatpickr.clear();
                }
                if (purposeType) {
                    $('#purpose').val(purposeType).trigger('change');
                }
            });
            $('#employee_id').trigger('change');
        });

        $(document).ready(function() {
            let isInitialLoadEvaluator = true;
            $('#employee_id').on('change', function() {
                const employeeId = $(this).val();
                $('#appraisal_position_display').val('');
                $('#appraisal_id').val('');
                $('#appraisal_position_id').val('');
                $('#position_status').val('');
                $('#appraisal_status').val('');
                $('#kpi_w').val('');
                $('#attendance_w').val('');

                $('#drafter_name').val('');
                $('#drafter_id').val('');
                $('#drafter_position').val('');
                $('#drafter_email').val('');
                for (let i = 1; i <= 6; i++) {
                    $(`.approval-group-${i}`).hide();
                    $('#approval' + i + '_name').val('');
                    $('#approval' + i + '_id').val('');
                    $('#approval' + i + '_position').val('');
                    $('#approval' + i + '_email').val('');
                    $(`#approval${i}_as`).prop('required', false);
                }

                if (employeeId) {
                    Swal.fire({
                        title: 'Loading data...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading()
                        }
                    });
                    const appraisalUrl =
                        "{{ route('evaluation.get-appraisals', ['employee_id' => '__ID__']) }}".replace(
                            '__ID__', employeeId);
                    $.ajax({
                        url: appraisalUrl,
                        type: 'GET',
                        success: function(data) {
                            if (data && data.length > 0) {
                                Swal.close();
                                const appraisal = data[0];
                                $('#appraisal_position_display').val(
                                    `${appraisal.position?.nama ?? '-'}`);
                                $('#appraisal_id').val(appraisal.id);
                                $('#appraisal_position_id').val(appraisal.position_id);
                                $('#position_status').val(appraisal.status);
                                $('#appraisal_status').val(appraisal.status);
                                $('#kpi_w').val(appraisal.kpi_weight);
                                $('#attendance_w').val(appraisal.attendance);
                                updateKpiAndAttendance(appraisal.form_type, appraisal.kpi_weight, appraisal.attendance);
                            } else {
                                Swal.close();
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'No Appraisal Position',
                                    text: 'No appraisal data found for this employee.',
                                });
                                $('#appraisal_position_display').val('');
                                $('#appraisal_id').val('');
                                $('#appraisal_position_id').val('');
                                $('#position_status').val('');
                                $('#appraisal_status').val('');
                                $('#kpi_w').val('');
                                $('#attendance_w').val('');
                                resetKpiAndAttendance();
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.close();
                            console.error("AJAX Error:", {
                                xhr,
                                status,
                                error
                            });
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Failed to load position data from the server.',
                            });
                        }
                    });
                    const evaluatorUrl =
                        "{{ route('evaluation.get-evaluators', ['employee_id' => '__ID__']) }}".replace(
                            '__ID__', employeeId);
                    $.ajax({
                        url: evaluatorUrl,
                        type: 'GET',
                        success: function(data) {
                            if (data['drafter']) {
                                $('#drafter_name').val(data['drafter'].fullname);
                                $('#drafter_id').val(data['drafter'].id);
                                $('#drafter_position').val(data['drafter'].position?.nama ?? '-');
                                $('#drafter_email').val(data['drafter'].user?.email ?? '-');
                                $('.drafter-group').removeClass('d-none');
                            } else {
                                $('#drafter_name').val('');
                                $('#drafter_id').val('');
                                $('#drafter_position').val('');
                                $('#drafter_email').val('');
                                $('.drafter-group').addClass('d-none');
                            }
                            for (let i = 1; i <= 6; i++) {
                                const approvalKey = 'approval' + i;
                                const approvalGroup = $(`.approval-group-${i}`);
                                const approvalAsSelect = $(`#approval${i}_as`);
                                if (data[approvalKey]) {
                                    // Data exists, so show the group and fill the fields.
                                    approvalGroup.show();
                                    $('#approval' + i + '_name').val(data[approvalKey]
                                        .fullname);
                                    $('#approval' + i + '_id').val(data[approvalKey].id);
                                    $('#approval' + i + '_position').val(data[approvalKey]
                                        .position?.nama ?? '-');
                                    $('#approval' + i + '_email').val(data[approvalKey].user
                                        ?.email ?? '-');
                                    approvalAsSelect.prop('required', true);
                                    let autoSignAs = data[approvalKey].default_role || '';
                                    let savedSignAs = approvalAsSelect.data('saved-value');
                                    if (isInitialLoadEvaluator && savedSignAs) {
                                        approvalAsSelect.val(savedSignAs).trigger('change');
                                    } else {
                                        approvalAsSelect.val(autoSignAs).trigger('change');
                                    }
                                } else {
                                    // Data does not exist, so hide the group.
                                    approvalGroup.hide();
                                    approvalAsSelect.val('').trigger('change');
                                    approvalAsSelect.prop('required', false);
                                }
                            }
                            isInitialLoadEvaluator = false;
                        },
                        error: function(xhr, status, error) {
                            console.error("AJAX Error (Approvals):", {
                                xhr,
                                status,
                                error
                            });
                        }
                    });
                }
            });
            $('#employee_id').trigger('change');

            var releaseModal = new bootstrap.Modal(document.getElementById('releaseModal'));
            $('#releaseButton').on('click', function() {
                var form = document.getElementById('evalForm');
                if (form.checkValidity() === false) {
                    form.reportValidity();
                    return;
                }
                releaseModal.show();
            });
        });

        $('#start_period').flatpickr({
            allowInput: true,
            altInput: false,
            altFormat: "d F, Y",
            dateFormat: "d/m/Y",
        });
        $('#end_period').flatpickr({
            allowInput: true,
            altInput: false,
            altFormat: "d F, Y",
            dateFormat: "d/m/Y",
        });
        const startInput = document.getElementById("start_period");
        const endInput = document.getElementById("end_period");
        const endPicker = flatpickr("#end_period", {
            allowInput: true,
            dateFormat: "d/m/Y",
        });
        const startPicker = flatpickr("#start_period", {
            allowInput: true,
            dateFormat: "d/m/Y",
            onChange: function(selectedDates) {
                if (selectedDates.length > 0) {
                    const startDate = selectedDates[0];
                    const minEndDate = new Date(startDate.getTime() + 24 * 60 * 60 * 1000);
                    endPicker.set('minDate', minEndDate);
                }
            }
        });
        window.addEventListener("DOMContentLoaded", function() {
            if (startInput.value) {
                const startDate = new Date(startInput.value);
                const minEndDate = new Date(startDate.getTime() + 24 * 60 * 60 * 1000);
                endPicker.set('minDate', minEndDate);
            }
        });
        $(document).ready(function() {
            function addAttachmentRow(defaultName = '') {
                const currentTotalRows = $('#attachment-list tr').length;
                const newCounter = currentTotalRows + 1;
                const newRow = `
                    <tr class="new-attachment-row">
                        <th scope="row" class="attachment-id">${newCounter}</th>
                        <td>
                            <div class="mb-2">
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    name="new_attachment_names[]" 
                                    placeholder="e.g., Attendance List" 
                                    value="${defaultName}" 
                                    required>
                            </div>
                        </td>
                        <td>
                            <div class="mb-2">
                                <input type="file" class="form-control" name="new_attachments[]" required>
                            </div>
                        </td>
                        <td class="attachment-removal">
                            <button type="button" class="btn btn-danger remove-new-attachment-btn">Delete</button>
                        </td>
                    </tr>
                `;
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

            $(document).on('click', '.remove-existing-attachment-btn', function() {
                const attachmentId = $(this).data('attachment-id');
                $(this).closest('tr').remove();
                $('#evalForm').append(
                    `<input type="hidden" name="deleted_attachments[]" value="${attachmentId}">`);
                updateAttachmentNumbers();
            });
            updateAttachmentNumbers();
            const totalExistingRows = $('#attachment-list tr').length;
            if (totalExistingRows === 0) {
                addAttachmentRow('KPI');
                addAttachmentRow('Attendance');
            }

            // Count Character
            function updateCharacterCount(textareaId, countId, maxLength) {
                const textarea = document.getElementById(textareaId);
                const countSpan = document.getElementById(countId);
                if (!textarea || !countSpan) return;
                const updateCount = function() {
                    const currentLength = textarea.value.length;
                    const remaining = maxLength - currentLength;
                    countSpan.textContent = remaining;
                    if (remaining < 0) {
                        countSpan.classList.add('text-warning');
                    } else {
                        countSpan.classList.remove('text-warning');
                    }
                };
                textarea.addEventListener('input', updateCount);
                updateCount(); 
            }

            const inputToMonitor = [
                { id: 'kpi_c', max: 60 },
                { id: 'attendance_c', max: 60 },
                { id: 'note_hrd', max: 100 }, 
            ];
            inputToMonitor.forEach(item => {
                updateCharacterCount(item.id, `count_${item.id}`, item.max); 
            });
        });

        function updateKpiAndAttendance(formType, kpiWeight, attendanceWeight) {
            toggleKpiSection(kpiWeight);
            
            // KPI Calculation
            const inputKpiAch = document.getElementById('kpi_s');
            const inputKpiScore = document.getElementById('kpi_score');
            const hiddenKpiScore = document.getElementById('TKPI_score');

            function hitungKPIScore() {
                let ach = parseFloat(inputKpiAch.value);
                if (isNaN(ach) || ach < 0 || ach > 100) {
                    inputKpiScore.value = '';
                    hiddenKpiScore.value = '';
                } else {
                    let score = (formType === 'A') ?
                        kpiWeight * (ach / 100) :
                        (kpiWeight / 100) * (ach / 100) * 100;
                    inputKpiScore.value = score.toFixed(2);
                    hiddenKpiScore.value = score.toFixed(2);
                }
                hiddenKpiScore.dispatchEvent(new Event('input', {
                    bubbles: true
                }));
            }

            inputKpiAch.removeEventListener('input', hitungKPIScore);
            inputKpiAch.addEventListener('input', hitungKPIScore);
            if (inputKpiAch.value) hitungKPIScore();

            // Attendance Calculation
            const inputAttendanceAch = document.getElementById('attendance_s');
            const inputAttendanceScore = document.getElementById('attendance_score');
            const hiddenAttendanceScore = document.getElementById('TAT_score');

            function hitungAttendanceScore() {
                let ach = parseFloat(inputAttendanceAch.value);
                if (isNaN(ach) || ach < 0 || ach > 100) {
                    inputAttendanceScore.value = '';
                    hiddenAttendanceScore.value = '';
                } else {
                    let score = (formType === 'A') ?
                        attendanceWeight * (ach / 100) :
                        (attendanceWeight / 100) * (ach / 100) * 100;
                    inputAttendanceScore.value = score.toFixed(2);
                    hiddenAttendanceScore.value = score.toFixed(2);
                }
                hiddenAttendanceScore.dispatchEvent(new Event('input', {
                    bubbles: true
                }));
            }

            inputAttendanceAch.removeEventListener('input', hitungAttendanceScore);
            inputAttendanceAch.addEventListener('input', hitungAttendanceScore);
            if (inputAttendanceAch.value) hitungAttendanceScore();
        }

        function resetKpiAndAttendance() {
            document.getElementById('kpi_s').value = '';
            document.getElementById('kpi_score').value = '';
            document.getElementById('TKPI_score').value = '';
            document.getElementById('attendance_s').value = '';
            document.getElementById('attendance_score').value = '';
            document.getElementById('TAT_score').value = '';
        }

        function toggleKpiSection(kpiWeight) {
            const kpiSection = $('.kpi-section');
            const kpiInputs = kpiSection.find('input[required], select[required]');
            if (kpiWeight === 0) {
                kpiSection.addClass('d-none');
                kpiInputs.removeAttr('required');
            } else {
                kpiSection.removeClass('d-none');
                kpiInputs.attr('required', 'required');
            }
        }
    </script>
@endsection
