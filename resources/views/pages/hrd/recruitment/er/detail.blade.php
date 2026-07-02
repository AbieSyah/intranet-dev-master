@extends('layouts.master')
@section('link')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css" />
    <link rel="stylesheet" href="{{ url('') }}/assets/libs/adminlte/select2/css/4.1.0/select2.min.css">
    <link href="{{ url('') }}/assets/libs/Datatables/DataTables-1.13.1/css/dataTables.bootstrap5.css" rel="stylesheet"
        type="text/css" />
    <link href="{{ url('') }}/assets/libs/Datatables/Buttons-2.3.3/css/buttons.dataTables.css" rel="stylesheet"
        type="text/css" />
    <link href="{{ url('') }}/assets/libs/Datatables/Responsive-2.4.0/css/responsive.bootstrap.css" rel="stylesheet"
        type="text/css" />
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
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

        div.dataTables_wrapper {
            width: 100%;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Detail Employee Requisition</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Recruitment</a></li>
                        <li class="breadcrumb-item active">Employee Requisition</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header align-items-center d-flex justify-content-between">
                    <h3 class="card-title">Detail Employee Requisition {{ $er->no_pengajuan ?? '' }}</h3>
                    <div class="flex-shrink-0">
                        <a href="{{ route('employee-requisition.index') }}" class="btn btn-primary btn-label waves-effect waves-light">
                            <i class="ri-arrow-left-fill label-icon align-middle fs-16 me-2"></i> Back
                        </a>
                        @php
                            $forbiddenStatuses = ['PROPOSE', 'REVISE', 'DRAFT', 'REJECT'];
                        @endphp
                        @if (!in_array($er->status, $forbiddenStatuses))
                            <a href="{{ route('employee-requisition.print', encrypt($er->id)) }}" target="_blank"
                                class="btn btn-success btn-label waves-effect waves-light">
                                <i class="ri-printer-fill label-icon align-middle fs-16 me-2"></i> Print
                            </a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 mb-2">
                            <h5 class="text-center text-primary">Pemohon / Applicant</h5>
                        </div>
                        <div class="col-lg-4 mb-2">
                            <label for="applicant_name" class="form-label col-form-label">Nama Pemohon /
                                Applicant
                                Name</label>
                            <input type="hidden" class="form-control" id="applicant_id" name="applicant_id"
                                value="{{ old('applicant_id', $er->applicant_id ?? $user->employee->id) }}" required>
                            <input type="text" class="form-control" id="applicant_name"
                                value="{{ $er->applicant->fullname ?? $user->employee->fullname }}"
                                style="Background-color: #eff2f7;" disabled>
                        </div>
                        <div class="col-lg-4 mb-2">
                            <label for="position" class="form-label col-form-label">Jabatan /
                                Position</label>
                            <input type="text" class="form-control" id="position"
                                value="{{ $er->applicant->position->nama ?? $user->employee->position->nama }}"
                                style="Background-color: #eff2f7;" disabled>
                        </div>
                        <div class="col-lg-4 mb-2">
                            <label for="department" class="form-label col-form-label">Departemen /
                                Department</label>
                            <input type="text" class="form-control" id="department"
                                value="{{ $er->applicant->department->name ?? $user->employee->department->name }}"
                                style="Background-color: #eff2f7;" disabled>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-12">
                            <h5 class="text-center text-primary">Approval Information</h5>
                        </div>
                        @php
                            $options = ['Checker', 'Approval', 'Director', 'President Director'];
                            $maxApprovals = count($approvals);
                            $approvalKeys = array_keys($approvals);
                        @endphp
                        @for ($i = 1; $i <= $maxApprovals; $i++)
                            @php
                                $key = $approvalKeys[$i - 1];
                                $approvalIdKey = $key . '_id';
                                $approvalAsKey = $key . '_as';
                                $currentApprovalObject = $approvals[$key];
                                $currentApprovalId = optional($currentApprovalObject)->id ?? null;
                                $defaultApprovalAsText = $options[$i - 1] ?? null;
                                $storedApprovalAs = optional($er)->{$approvalAsKey} ?? $defaultApprovalAsText;
                                $currentApprovalAs = old($approvalAsKey, $storedApprovalAs);
                            @endphp
                            <div class="row approval-group approval-group-{{ $i }}">
                                <div class="col-lg-3 col-sm-6 p-2">
                                    <label class="required fw-semibold fs-6 mb-2">Line Approval
                                        {{ $i }}</label>
                                    <input disabled style="cursor: not-allowed" type="text"
                                        id="{{ $key }}_name" class="form-control form-control-solid mb-3 mb-lg-0"
                                        value="{{ optional($currentApprovalObject)->fullname ?? '-' }}" />
                                    <input type="hidden" name="{{ $approvalIdKey }}" id="{{ $approvalIdKey }}"
                                        value="{{ $currentApprovalId }}">
                                </div>
                                <div class="col-lg-3 col-sm-6 p-2">
                                    <label class="fw-semibold fs-6 mb-2">Position</label>
                                    <input disabled style="cursor: not-allowed" type="text"
                                        id="{{ $key }}_position"
                                        class="form-control form-control-solid mb-3 mb-lg-0"
                                        value="{{ optional(optional($currentApprovalObject)->position)->nama ?? '-' }}" />
                                </div>
                                <div class="col-lg-3 col-sm-6 p-2">
                                    <label class="fw-semibold fs-6 mb-2">Email</label>
                                    <input disabled style="cursor: not-allowed" type="text"
                                        id="{{ $key }}_email"
                                        class="form-control form-control-solid mb-3 mb-lg-0"
                                        value="{{ optional(optional($currentApprovalObject)->user)->email ?? '-' }}" />
                                </div>
                                <div class="col-lg-3 col-sm-6 p-2">
                                    <label class="required fw-semibold fs-6 mb-2">Approval
                                        {{ $i }}
                                        As</label>
                                    <input disabled style="cursor: not-allowed" type="text"
                                        id="{{ $key }}_email"
                                        class="form-control form-control-solid mb-3 mb-lg-0"
                                        value="{{ $currentApprovalAs }}" />
                                </div>
                            </div>
                        @endfor
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-12 mb-2">
                            <h5 class="text-center text-primary">Penempatan Karyawan / Placement of
                                Employee</h5>
                        </div>
                        <div class="col-lg-4 mb-2">
                            <label for="needs" class="form-label col-form-label">Kebutuhan /
                                Needs</label>
                            <input type="text" class="form-control" value="{{ $er->needs ?? '0' }} Orang / Person"
                                disabled>
                        </div>
                        <div class="col-lg-4 mb-2">
                            <label class="form-label col-form-label">Jabatan / Position</label>
                            <input type="text" class="form-control" value="{{ $er->position->nama ?? 'NA' }}" disabled>
                        </div>
                        <div class="col-lg-4 mb-2">
                            <label class="form-label col-form-label">Departemen / Department</label>
                            <input type="text" class="form-control" value="{{ $er->department->name ?? 'NA' }}" disabled>
                        </div>
                        <div class="col-lg-4 mb-2">
                            <label class="form-label col-form-label">Bagian / Section</label>
                            <input type="text" class="form-control" value="{{ $er->section->nama ?? 'NA' }}" disabled>
                        </div>
                        <div class="col-lg-4 mb-2">
                            <label class="form-label col-form-label">Area / Area</label>
                            <input type="text" class="form-control" value="{{ $er->area->name ?? 'NA' }}" disabled>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-lg-4 mb-2">
                            <label class="form-label col-form-label">Alasan Permintaan / Reason of
                                Requisition</label>
                            <input type="text" class="form-control" value="{{ $er->reason_requisition }}" disabled>
                        </div>
                        @if (!empty($er->person_replace->fullname) && !empty($er->reason_replacement))
                            <div id="replacement-fields" class="col-lg-8 row m-0 p-0">
                                <div class="col-lg-6 mb-2">
                                    <label class="form-label col-form-label">Nama yang Diganti / Person
                                        Replaced</label>
                                    <input type="text" class="form-control"
                                        value="{{ optional($er->person_replace)->fullname ? $er->person_replace->fullname . ' (' . $er->person_replace->nik . ')' : '' }}"
                                        disabled>
                                </div>
                                <div class="col-lg-6 mb-2">
                                    <label class="form-label col-form-label">Alasan Penggantian / Reason of
                                        Replacement</label>
                                    <input type="text" class="form-control"
                                        value="{{ optional($er)->reason_replacement }}" disabled>
                                    @if (!empty($er->reason_replacement_other))
                                        <input type="text" class="form-control mt-2"
                                            value="{{ optional($er)->reason_replacement_other }}" disabled>
                                    @endif
                                </div>
                            </div>
                        @endif
                        <div class="col-lg-4 mb-2">
                            <label class="form-label col-form-label">Status Karyawan / Employee
                                Status</label>
                            <input type="text" class="form-control" value="{{ optional($er)->employee_status }}"
                                disabled>
                        </div>
                        @if (!empty($er->contract_period))
                            <div class="col-lg-4 mb-2" id="contract-period-field">
                                <label class="form-label col-form-label">Periode Kontrak / Contract
                                    Period</label>
                                <input type="text" class="form-control"
                                    value="{{ optional($er)->contract_period }} Bulan / Months" disabled>
                            </div>
                        @endif
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-12 mb-2">
                            <h5 class="text-center text-primary">Persyaratan Karyawan / Employee
                                Requirements</h5>
                        </div>
                        <div class="col-12 mb-2">
                            <label class="form-label col-form-label">Pendidikan / Educational
                                Background</label>
                            @php
                                $educationRequirement = [
                                    'SMA / MA / SMK',
                                    'Diploma / Diploma Degree',
                                    'Sarjana / Bachelor Degree',
                                    'Profesi / Profession Program',
                                    'Lainnya / Others',
                                ];
                                $educationalRequirements = $er->educationalRequirements ?? collect();
                                $selectedEducationNames = old(
                                    'education_names',
                                    $educationalRequirements->pluck('name')->toArray(),
                                );
                                $selectedMajors = old(
                                    'major_requirements',
                                    $educationalRequirements->pluck('pivot.major', 'name')->toArray(),
                                );
                            @endphp
                            <div id="education-requirements-container">
                                @foreach ($educationRequirement as $eduName)
                                    <div class="row align-items-center mb-1 education-item">
                                        <div class="col-lg-4">
                                            <div class="form-check">
                                                <input disabled class="form-check-input education-checkbox"
                                                    type="checkbox" value="{{ $eduName }}" name="education_names[]"
                                                    id="edu_{{ Str::slug($eduName) }}"
                                                    {{ in_array($eduName, $selectedEducationNames) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="edu_{{ Str::slug($eduName) }}">
                                                    {{ $eduName }}
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-lg-8 major-input-container"
                                            style="{{ in_array($eduName, $selectedEducationNames) ? '' : 'display:none;' }}">
                                            <input type="text" class="form-control major-input"
                                                name="major_requirements[{{ $eduName }}]"
                                                value="{{ $selectedMajors[$eduName] ?? '' }}" disabled>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="col-12 mb-2">
                            <label class="form-label col-form-label">Jenis Kelamin / Gender</label>
                            @php
                                $genders = ['Pria / Male', 'Wanita / Female'];
                                $selectedGenders = $er->genderRequirements ?? collect();
                                $selectedGenderDetails = $selectedGenders
                                    ->keyBy('gender_name')
                                    ->map(function ($item) {
                                        return [
                                            'needs_count' => $item->needs_count,
                                            'start_age' => $item->start_age,
                                            'end_age' => $item->end_age,
                                        ];
                                    })
                                    ->toArray();
                            @endphp
                            <div id="gender-requirements-container">
                                @foreach ($genders as $genderName)
                                    @php
                                        $details = $selectedGenderDetails[$genderName] ?? [];
                                        $needsCount = old(
                                            "gender_needs.{$genderName}",
                                            $details['needs_count'] ?? null,
                                        );
                                        $startAge = old(
                                            "gender_start_age.{$genderName}",
                                            $details['start_age'] ?? null,
                                        );
                                        $endAge = old("gender_end_age.{$genderName}", $details['end_age'] ?? null);
                                        $isChecked = !is_null($needsCount) || old("gender_select.{$genderName}");
                                        $nameAttr = $isChecked ? 'name' : 'data-name-original';
                                    @endphp
                                    <div class="row align-items-center mb-2 gender-item">
                                        <div class="col-lg-2">
                                            <div class="form-check">
                                                <input disabled class="form-check-input gender-checkbox" type="checkbox"
                                                    value="{{ $genderName }}"
                                                    name="gender_select[{{ $genderName }}]"
                                                    id="gender_{{ Str::slug($genderName) }}"
                                                    {{ $isChecked ? 'checked' : '' }}>
                                                <label class="form-check-label"
                                                    for="gender_{{ Str::slug($genderName) }}">
                                                    {{ $genderName }}
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-lg-10 gender-details-container"
                                            style="display: {{ $isChecked ? 'flex' : 'none' }}; align-items: center;">
                                            <div class="col-lg-4">
                                                <input type="text" class="form-control gender-needs-count"
                                                    data-name-original="gender_needs[{{ $genderName }}]"
                                                    name="{{ $isChecked ? "gender_needs[{$genderName}]" : '' }}"
                                                    value="{{ $needsCount }} Orang / Person" disabled>
                                            </div>
                                            <div class="col-lg-8 ps-2">
                                                <div class="input-group">
                                                    <input type="text" class="form-control gender-age-input"
                                                        data-name-original="gender_start_age[{{ $genderName }}]"
                                                        name="{{ $isChecked ? "gender_start_age[{$genderName}]" : '' }}"
                                                        disabled value="Usia / Age : {{ $startAge }} Tahun / Years">
                                                    <span class="input-group-text">s/d</span>
                                                    <input type="text"
                                                        data-name-original="gender_end_age[{{ $genderName }}]"
                                                        name="{{ $isChecked ? "gender_end_age[{$genderName}]" : '' }}"
                                                        class="form-control gender-age-input" disabled
                                                        value="Usia / Age : {{ $endAge }} Tahun / Years">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-lg-6 mb-2">
                            <label class="form-label col-form-label">Pengalaman Bekerja / Work
                                Experience</label>
                            <input type="text" class="form-control" value="{{ optional($er)->work_experience }}"
                                disabled>
                        </div>
                        @if (!empty($er->duration_work_experience))
                            <div class="col-lg-6 mb-2" id="duration-experience-field">
                                <label class="form-label col-form-label">Pengalaman / Experience (Tahun /
                                    Years)</label>
                                <input type="text" class="form-control" id="duration_work_experience"
                                    name="duration_work_experience"
                                    value="{{ $er->duration_work_experience }} Tahun / Years" disabled>
                            </div>
                        @endif
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-12 mb-2">
                            <h5 class="text-center text-primary">Kualifikasi dan Persyaratan
                                Keterampilan Khusus /
                                Qualification and Special Skills Requirements</h5>
                        </div>
                        <div class="col-12 mb-2">
                            <textarea class="form-control" id="qualification" name="qualification" rows="7" maxlength="1000"
                                placeholder="Describe the qualification for new employees..." disabled>{{ old('qualification', $er->qualification ?? '') }}</textarea>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-lg-4 mb-2">
                            <label class="form-label col-form-label">Tanggal Mulai Bekerja / Employment
                                Date</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="employment_date" name="employment_date"
                                    placeholder="Select Date"
                                    value="{{ old('employment_date', optional($er)->employment_date ? optional($er)->employment_date->format('d/m/Y') : '') }}"
                                    disabled>
                                <span class="input-group-text" id="basic-addon2"><i
                                        class="ri-calendar-todo-line"></i></span>
                            </div>
                        </div>
                    </div>
                    <hr>
                    @if (!empty($er->decision))
                    <div class="row">
                        <div class="col-12 mb-2">
                            <h5 class="text-center text-primary">Informasi Tindak Lanjut / Follow-Up Information</h5>
                        </div>
                        <div class="col-lg-6 mb-2">
                            <form id="RecruitmentSourceForm" action="{{ route('employee-requisition.detail.recruitment.source', encrypt($er->id)) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <label class="form-label col-form-label">Sumber Rekrutmen / Recruitment Source</label>
                                @php
                                    $recSourceOptions = [
                                        'Manual Source / Job Submission',
                                        'Internet / Job Posting',
                                        'Head Hunter / Talent Search',
                                        'Others',
                                    ];
                                    $recruitSource = $er->recruitmentSources ?? collect();
                                    $selectedRecSource = $recruitSource->pluck('name')->toArray(); 
                                    $selectedOtherDetail = $recruitSource->where('name', 'Others')->first()->pivot->other_detail ?? '';
                                @endphp
                                <div id="recruitment-source-container">
                                    @foreach ($recSourceOptions as $rs)
                                        @php
                                            $isChecked = in_array($rs, $selectedRecSource);
                                        @endphp
                                        <div class="row align-items-center mb-1 recsource-item">
                                            <div class="col-12">
                                                <div class="form-check">
                                                    <input class="form-check-input recsource-checkbox" type="checkbox"
                                                        value="{{ $rs }}" name="recruitment_source[]"
                                                        id="rs_{{ Str::slug($rs) }}"
                                                        {{ $isChecked ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="rs_{{ Str::slug($rs) }}">
                                                        {{ $rs }}
                                                    </label>
                                                </div>
                                            </div>
                                            @if ($rs === 'Others')
                                                <div class="col-12 recsource-input-container" id="other-source-input-container"
                                                    style="{{ $isChecked ? '' : 'display:none;' }}">
                                                    <input type="text" class="form-control recsource-input mt-1"
                                                        name="other_source[Others]"
                                                        placeholder="Example: Recruitment from Outsourcing and experience 2+ years in HPI"
                                                        value="{{ $selectedOtherDetail }}" maxlength="60">
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                                <button type="submit" id="saveRecruitmentSourceBtn" class="btn btn-primary mt-2" style="display:none;">Save</button>
                            </form>
                        </div>
        
                        @if (!empty($er->decision))
                            <div class="col-lg-6 mb-2">
                                <label class="form-label col-form-label">Keputusan / Decision</label>
                                <br>
                                @php
                                    $decision = strtoupper($er->decision);
                                    $class = '';
                                    if ($decision == 'APPROVED') {
                                        $class = 'text-bg-success';
                                    } elseif ($decision == 'DISAPPROVED') {
                                        $class = 'text-bg-danger';
                                    } elseif ($decision == 'PENDING') {
                                        $class = 'text-bg-warning';
                                    } else {
                                        $class = 'text-bg-dark';
                                    }
                                @endphp
                                <h3 class="badge {{ $class }} fw-bold" style="font-size: 1em !important;">
                                    {{ $decision }}
                                </h3>
                                @if (!empty($er->decision_comment))
                                    <br>
                                    <label class="form-label col-form-label">Komentar / Comment</label>
                                    <input type="text" class="form-control"
                                        value="{{ $er->decision_comment ?? '' }}" disabled>
                                @endif
                            </div>
                        @endif
                    </div>
                    @endif

                    @if (!in_array($er->status, $forbiddenStatuses) && $er->decision === 'APPROVED')
                        <hr>
                        <div class="row">
                            <div class="col-12">
                                <h5 class="text-center text-primary mb-4">Selection Steps</h5>
                            <div class="col-12">
                                <form id="SelectionStepForm" action="{{ route('employee-requisition.store.steps', encrypt($er->id)) }}" method="POST">
                                    @csrf
                                    <div class="table-responsive">
                                        <table class="table table-borderless table-nowrap mb-0" id="selection-steps-table">
                                            <thead class="align-middle">
                                                <tr class="table-active">
                                                    <th scope="col" style="width: 10%;" class="text-center">Step</th>
                                                    <th scope="col" style="width: 60%;">Selection Type <span class="text-danger">*</span></th>
                                                    <th scope="col" style="width: 20%;" class="text-center">Status</th>
                                                    <th scope="col" style="width: 10%;" class="text-center">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="selection-steps-list">
                                                @foreach ($er->hiringSteps()->withCount('selectionProcesses')->orderBy('step_order')->get() as $step)
                                                    <tr class="step-item" data-step-id="{{ $step->id }}">
                                                        <th scope="row" class="step-order-number text-center">{{ $step->step_order }}</th>
                                                        <td>
                                                            <div class="mb-0">
                                                                <input type="hidden" name="steps[{{ $step->id }}][id]" value="{{ $step->id }}">
                                                                <input type="hidden" class="step-order-value" name="steps[{{ $step->id }}][order]" value="{{ $step->step_order }}">
                                                                @php
                                                                    $isLocked = $step->selection_processes_count > 0 || !is_null($er->fulfilled_date);
                                                                @endphp
                                                                <select class="form-select step-select" 
                                                                    name="steps[{{ $step->id }}][master_hiring_id]" required
                                                                    {{ $isLocked ? 'disabled' : '' }}>
                                                                    <option value="" disabled selected>Select an option</option> 
                                                                    @foreach ($masterHiring as $master)
                                                                        <option value="{{ $master->id }}" {{ $step->master_hiring_id == $master->id ? 'selected' : '' }}>
                                                                            {{ $master->name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="badge bg-success">SAVED</span>
                                                        </td>
                                                        <td class="text-center">
                                                            @if($step->selection_processes_count == 0 && is_null($er->fulfilled_date))
                                                                <button type="button" class="btn btn-danger btn-sm remove-step-btn" title="Delete" data-step-id="{{ $step->id }}">
                                                                    <i class="ri-delete-bin-line"></i>
                                                                </button>
                                                            @else
                                                                @if($step->selection_processes_count > 0)
                                                                    <span class="badge bg-primary">SELECTION</span>
                                                                @else
                                                                    <span class="badge bg-danger">LOCKED</span>
                                                                @endif
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            @if(is_null($er->fulfilled_date))
                                                <tbody>
                                                    <tr>
                                                        <td colspan="4">
                                                            <a href="javascript:void(0)" id="add-step-item"
                                                                class="btn btn-soft-primary fw-medium"><i
                                                                    class="ri-add-fill me-1 align-bottom"></i> Add </a>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            @endif
                                        </table>
                                    </div>
                                    @if(is_null($er->fulfilled_date))
                                        <button type="submit" id="saveStepsBtn" class="btn btn-primary" style="display: none">Save</button>
                                    @endif
                                </form>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <input type="hidden" id="final_candidate_data" value="{{ json_encode($finalCandidates) }}">
        @if(count($finalCandidates) > 0)
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header align-items-center d-flex justify-content-between">
                        <h3 class="card-title">Final Candidate for Employee Requisition</h3>
                    </div>
                    <div class="card-body">
                        <div class="row gy-3">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-striped bordered display nowrap" style="width:100%" id="table_final_candidate">
                                        <thead>
                                            <tr>
                                                <th scope="col" class="text-center">
                                                    <input type="checkbox" id="checkAll">
                                                </th>
                                                <th scope="col" class="text-center">Action</th>
                                                <th scope="col" class="text-center">Attachment</th>
                                                <th scope="col">Name</th>
                                                <th scope="col">ID</th>
                                                <th scope="col" class="text-center">Age</th>
                                                <th scope="col" class="text-center">Gender</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-flex justify-content-start gap-2">
                                    @if(is_null($er->fulfilled_date))
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#closingRequisitionModal">
                                            <i class="ri-lock-2-fill align-bottom me-1"></i> FULFILLED
                                        </button>
                                    @else
                                        <button type="button" title="Add to Employee" class="btn btn-success" id="btnHireSelected"
                                            style="display: none;">
                                            <i class="ri-user-add-line align-bottom me-1"></i> HIRE
                                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary">
                                                <span id="hire-count">0</span>
                                                <span class="visually-hidden">hire selected</span>
                                            </span>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="closingRequisitionModal" tabindex="-1" aria-labelledby="closingModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="ClosingRequisitionForm" action="{{ route('employee-requisition.detail.recruitment.close', encrypt($er->id)) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="modal-header">
                            <h5 class="modal-title" id="closingModalLabel">Close Employee Requisition</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="fulfilled_reason" class="form-label required">Reason</label>
                                <input type="text" class="form-control" id="fulfilled_reason" name="fulfilled_reason" placeholder="Input your reason for closing" required/>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade" id="hireCandidatesModal" tabindex="-1" aria-labelledby="hireModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="hireModalLabel">Confirm Hiring</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted text-center">
                            Are you sure you want to hire <span class="fw-bold text-primary" id="modal-hire-count">0</span> selected candidate(s)?
                        <br>
                            This action will create new Employee data for the selected candidates.
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-success" id="confirmHireBtn">Yes, Hire</button>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
@endsection
@section('script')
    <script src="{{ url('') }}/assets/js/pages/profile-setting.init.js"></script>
    <script src="{{ url('') }}/assets/libs/adminlte/select2/js/4.1.0/select2.min.js"></script>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script type="text/javascript">
        let swalert;
        let initialFormState;
        let selectedIds = [];
        let allCandidates = [];
        function getFormState() {
            const $form = $('#RecruitmentSourceForm');
            const $checkboxes = $form.find('.recsource-checkbox');
            const $otherInput = $form.find('#other-source-input-container .recsource-input');
            const state = {};
            $checkboxes.each(function() {
                state[$(this).val()] = $(this).is(':checked');
            });
            state['OthersDetail'] = $otherInput.val() ? $otherInput.val().trim() : ''; 
            return JSON.stringify(state);
        }

        function checkForChanges() {
            const currentFormState = getFormState();
            const $saveBtn = $('#saveRecruitmentSourceBtn');
            
            if (currentFormState !== initialFormState) {
                $saveBtn.slideDown(200);
            } else {
                $saveBtn.slideUp(200);
            }
        }

        function handleErrorResponse(responseJson) {
            let errorMessage = '';
            let isValidationError = responseJson && responseJson.errors;
            if (responseJson && responseJson.message) {
                if (isValidationError) {
                    errorMessage += `<h4>${responseJson.message}</h4>`;
                } else {
                    errorMessage += `<h4>${responseJson.message}</h4>`;
                }
            }
            if (isValidationError) {
                for (const fieldName in responseJson.errors) {
                    errorMessage += `<p>${responseJson.errors[fieldName]}</p>`;
                }
            }
            if (errorMessage === '') {
                errorMessage = 'An Error occured.';
            }
            Swal.fire({
                title: 'Error!',
                html: errorMessage,
                icon: 'error',
                buttonsStyling: false,
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'btn btn-danger'
                }
            });
        }

        $(document).ready(function() {
            const $form = $('#RecruitmentSourceForm');
            const $otherInput = $form.find('#other-source-input-container .recsource-input');
            initialFormState = getFormState();
            checkForChanges();
            $('.recsource-checkbox').on('change', function() {
                const checkbox = $(this);
                const value = checkbox.val();
                if (value === 'Others') {
                    const inputContainer = $('#other-source-input-container');
                    if (checkbox.is(':checked')) {
                        inputContainer.slideDown(200);
                        $otherInput.prop('required', true);
                    } else {
                        inputContainer.slideUp(200, function() {
                            $otherInput.val(''); 
                            $otherInput.prop('required', false);
                            checkForChanges();
                        });
                    }
                }
                checkForChanges();
            });

            $otherInput.on('input', checkForChanges);
            $('#other-source-input-container').each(function() {
                const isChecked = $('#rs_{{ Str::slug('Others') }}').is(':checked');
                const inputField = $(this).find('.recsource-input');                
                if (isChecked) {
                    $(this).show();
                    inputField.prop('required', true);
                } else {
                    $(this).hide();
                    inputField.prop('required', false);
                }
            });

            $form.submit(function(e) { 
                e.preventDefault();
                if (!this.reportValidity()) {
                    return;
                }
                swalert = Swal.fire({
                    title: 'Saving data...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading()
                    }
                });
                const formData = new FormData(this);
                $.ajax({
                    url: $form.attr("action"),
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        swalert.hideLoading();
                        initialFormState = getFormState();
                        checkForChanges();
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
                    },
                    error: function(xhr, status, error) {
                        swalert.close();
                        console.error({ xhr, status, error });
                        handleErrorResponse(xhr.responseJSON || { message: 'Server connection error.' });
                    }
                });
            });
        });
    </script>
    <script type="text/javascript">
        let stepCounter = 0;
        const $stepsList = $('#selection-steps-list');
        const $saveStepsBtn = $('#saveStepsBtn');
        const $addStepBtn = $('#add-step-item');
        const masterHiringOptions = @json($masterHiring ?? []);
        let initialStepsState;

        function getUsedMasterIds() {
            const usedIds = [];
            $stepsList.find('.step-select').each(function() {
                const masterId = $(this).val();
                if (masterId) {
                    usedIds.push(masterId.toString());
                }
            });
            return usedIds;
        }

        function getMasterOptionsHtml(currentSelectedValue = '') {
            const usedIds = getUsedMasterIds();
            const filteredUsedIds = usedIds.filter(id => id !== currentSelectedValue);
            let optionsHtml = '<option value="" disabled selected>Select an option</option>';
            masterHiringOptions.forEach(master => {
                const masterIdStr = master.id.toString();
                if (!filteredUsedIds.includes(masterIdStr)) {
                    const isSelected = currentSelectedValue === masterIdStr ? 'selected' : '';
                    optionsHtml += `<option value="${master.id}" ${isSelected}>${master.name}</option>`;
                }
            });
            return optionsHtml;
        }

        function updateStepNumbers() {
            $stepsList.find('tr.step-item').each(function(index) {
                const newOrder = index + 1;
                $(this).find('.step-order-number').text(newOrder);
                $(this).find('.step-order-value').val(newOrder);
            });
        }

        function addStepRow() {
            stepCounter++;
            const newRow = `
                <tr class="step-item new-step-row" data-new-id="${stepCounter}">
                    <th scope="row" class="step-order-number text-center"></th>
                    <td>
                        <div class="mb-0">
                            <input type="hidden" class="step-order-value" name="new_steps[${stepCounter}][order]" value="">
                            <select class="form-select step-select" 
                                name="new_steps[${stepCounter}][master_hiring_id]" required>
                                ${getMasterOptionsHtml()}
                            </select>
                        </div>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-primary">NEW</span>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-danger btn-sm remove-step-btn" title="Delete">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    </td>
                </tr>
            `;
            $stepsList.append(newRow);
            updateStepNumbers();
        }
        
        function refreshAllStepOptions() {
            $stepsList.find('tr.step-item').each(function() {
                const $select = $(this).find('.step-select');
                const currentSelectedValue = $select.val();
                const newOptionsHtml = getMasterOptionsHtml(currentSelectedValue);
                $select.html(newOptionsHtml);
                if (currentSelectedValue) {
                    $select.val(currentSelectedValue);
                }
            });
        }

        function checkStepIntegrity() {
            let isIncomplete = false;
            $stepsList.find('.step-select').each(function() {
                if (!$(this).val()) { 
                    isIncomplete = true;
                    return false;
                }
            });
            return !isIncomplete; 
        }

        function toggleAddButton() {
            const usedCount = getUsedMasterIds().length;
            const totalMaster = masterHiringOptions.length;
            if (usedCount >= totalMaster) {
                $addStepBtn.slideUp(200); 
            } else {
                $addStepBtn.slideDown(200);
            }
        }

        function getStepsState() {
            const state = [];
            $stepsList.find('tr.step-item').each(function() {
                const masterId = $(this).find('.step-select').val();
                const order = $(this).find('.step-order-value').val();
                const dbId = $(this).data('step-id') || 'NEW';
                state.push(`${dbId}:${masterId}:${order}`);
            });
            return state.sort().join('|');
        }

        function checkAndToggleSaveButton() {
            const isIntegrityOK = checkStepIntegrity();
            const currentState = getStepsState();
            if (isIntegrityOK && currentState !== initialStepsState) {
                $saveStepsBtn.slideDown(200); 
            } else {
                $saveStepsBtn.slideUp(200);
            }
            toggleAddButton();
        }


        $(document).ready(function() {
            initialStepsState = getStepsState();
            if ($stepsList.find('tr.step-item').length === 0) {
                addStepRow();
            }
            refreshAllStepOptions(); 
            checkAndToggleSaveButton();

            $('#add-step-item').on('click', function() {
                let formIsInvalid = false;
                $stepsList.find('.step-select').each(function() {
                    if (!this.reportValidity()) {
                        formIsInvalid = true;
                        return false; 
                    }
                });
                if (formIsInvalid) {
                    return;
                }
                addStepRow();
                refreshAllStepOptions();
                checkAndToggleSaveButton();
            });

            $stepsList.on('click', '.remove-step-btn', function() {
                const $row = $(this).closest('tr');
                const stepId = $row.data('step-id');
                if (stepId) {
                    $('#SelectionStepForm').append(
                        `<input type="hidden" name="deleted_steps[]" value="${stepId}">`
                    );
                }
                $row.remove();
                setTimeout(() => {
                    updateStepNumbers();
                    refreshAllStepOptions(); 
                    checkAndToggleSaveButton();
                }, 50);
            });
            
            $stepsList.on('change', '.step-select', function() {
                refreshAllStepOptions(); 
                checkAndToggleSaveButton(); 
            });

            $('#SelectionStepForm').submit(function(e) {
                e.preventDefault();                                
                if (!checkStepIntegrity()) {
                    Swal.fire({
                        title: 'Validation Failed!',
                        text: 'Please review the selection steps. Ensure all steps are selected and complete.',
                        icon: 'error',
                        buttonsStyling: false,
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'btn btn-danger'
                        }
                    });
                    return; 
                }
                updateStepNumbers(); 
                let swalert = Swal.fire({
                    title: 'Saving data...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading()
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
                        swalert.close();
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
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload(); 
                            }
                        });
                    },
                    error: function(xhr, status, error) {
                        swalert.close();
                        handleErrorResponse(xhr.responseJSON || { message: 'Server connection error.' });
                    }
                });
            });

            @if(count($finalCandidates) > 0)
            allCandidates = JSON.parse($('#final_candidate_data').val() || '[]');
            const finalCandidatesData = JSON.parse($('#final_candidate_data').val() || '[]');
            if (finalCandidatesData.length > 0) {
                $('#table_final_candidate').DataTable({
                    data: finalCandidatesData,
                    destroy: true,
                    stateSave: false,
                    responsive: true,
                    scrollX: false,
                    autoWidth: false,
                    order: [[3, 'asc']],
                    columns: [
                        {
                            data: null,
                            className: "text-center",
                            orderable: false,
                            render: function (data, type, row) {
                                let isDisabled = row.is_hired ? 'disabled' : '';
                                let isChecked = selectedIds.includes(String(row.candidate_id)) ? 'checked' : '';
                                return `<input class="row-checkbox" type="checkbox" value="${row.candidate_id}" ${isChecked} ${isDisabled}>`;
                            }
                        },
                        {
                            data: 'action',
                            className: "text-center",
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'attachment',
                            className: "text-center",
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row) {
                                if (data) {
                                    let fileUrl = "{{ asset('storage/candidates/selection') }}/" + data;
                                    return `<a href="${fileUrl}" target="_blank" class="btn btn-sm btn-success" title="View Attachment">
                                                <i class="ri-file-text-line"></i>
                                            </a>`;
                                }
                                return '-';
                            }
                        },
                        { data: 'fullname', defaultContent: '-' },
                        { data: 'no_ktp', defaultContent: '-' },
                        { data: 'age', className: "text-center", defaultContent: '-' },
                        { data: 'gender', className: "text-center", defaultContent: '-' },
                    ],
                    "drawCallback": function(settings) {
                        $('[data-toggle="tooltip"]').tooltip();
                        checkAllStatus();
                    }
                });
            }

            $('#ClosingRequisitionForm').submit(function(e) {
                e.preventDefault();
                if (!this.checkValidity()) {
                    this.reportValidity();
                    return;
                }
                let swalert = Swal.fire({
                    title: 'Closing Requisition...',
                    text: 'Please wait while we process your request.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading()
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
                        swalert.close();
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
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        });
                    },
                    error: function(xhr, status, error) {
                        swalert.close();
                        handleErrorResponse(xhr.responseJSON || { message: 'Server connection error.' });
                    }
                });
            });

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
                    const dt = $('#table_final_candidate').DataTable();
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
                const dt = $('#table_final_candidate').DataTable();
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
            checkAllStatus();

            function updateMultiButtons() {
                const count = selectedIds.length;
                $('#btnHireSelected').hide();
                if (count > 0) {
                    $('#btnHireSelected').show();
                    $('#hire-count').text(count);
                }
            }

            $('#btnHireSelected').on('click', function() {
                if (selectedIds.length === 0) {
                    Swal.fire({
                        title: 'Please select at least one candidate to hire.',
                        icon: 'warning',
                        confirmButtonClass: 'btn btn-danger',
                        buttonsStyling: false
                    });
                    return;
                }
                $('#modal-hire-count').text(selectedIds.length);
                const hireModal = new bootstrap.Modal(document.getElementById('hireCandidatesModal'));
                hireModal.show();
            });

            $('#confirmHireBtn').on('click', function() {
                $('#hireCandidatesModal').modal('hide');
                let swalert = Swal.fire({
                    title: 'Processing Hiring...',
                    text: 'Creating employee data...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading()
                    }
                });

                $.ajax({
                    url: "{{ route('employee-requisition.detail.recruitment.hire', encrypt($er->id)) }}", // Pastikan route ini sesuai
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        candidate_ids: selectedIds
                    },
                    success: function(response) {
                        swalert.close();
                        Swal.fire({
                            title: "Success!",
                            text: response.message,
                            icon: "success",
                            confirmButtonText: "OK",
                            buttonsStyling: false,
                            customClass: {
                                confirmButton: 'btn btn-success'
                            },
                            allowOutsideClick: false
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        swalert.close();
                        let errorMessage = xhr.responseJSON?.message || 'Failed to hire candidates.';
                        Swal.fire({
                            title: 'Error', 
                            text: errorMessage, 
                            icon: 'error',
                            buttonsStyling: false,
                            confirmButtonText: "Close",
                            customClass: {
                                confirmButton: 'btn btn-danger'
                            }
                        });
                    }
                });
            });
            @endif
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
    @if(count($finalCandidates) > 0)
    @include('pages.hrd.recruitment.selection.partials.candidate')
    @endif
@endsection
