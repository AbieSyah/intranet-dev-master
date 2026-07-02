@extends('layouts.general')

@section('link')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ url('') }}/assets/libs/adminlte/select2/css/4.1.0/select2.min.css">
    <style>
        :disabled {
            cursor: not-allowed;
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
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h4 class="text-primary">Formulir Permohonan Permintaan Karyawan</h4>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ route('recruitment.emp.index') }}"
                                class="btn btn-primary btn-label waves-effect waves-light float-end"><i
                                    class="ri-arrow-left-circle-line label-icon align-middle fs-16 me-2"></i> Back</a>
                        </div>
                    </div>
                    <hr>
                    <form id="FormER" action="{{ route('recruitment.emp.er.form.store') }}" method="POST">
                        @csrf
                        @method('POST')
                        <input type="hidden" name="id" id="id" value="{{ $er->id ?? '' }}">
                        <div class="row">
                            <div class="col-12 mb-2">
                                <h5 class="text-center text-primary">Pemohon / Applicant</h5>
                            </div>
                            <div class="col-lg-4 mb-2">
                                <label for="applicant_name" class="form-label col-form-label">Nama Pemohon / Applicant
                                    Name</label>
                                <input type="hidden" class="form-control" id="applicant_id" name="applicant_id"
                                    value="{{ old('applicant_id', $er->applicant_id ?? $user->employee->id) }}" required>
                                <input type="text" class="form-control" id="applicant_name"
                                    value="{{ $er->applicant->fullname ?? $user->employee->fullname }}"
                                    style="Background-color: #eff2f7;" disabled>
                            </div>
                            <div class="col-lg-4 mb-2">
                                <label for="position" class="form-label col-form-label">Jabatan / Position</label>
                                <input type="text" class="form-control" id="position"
                                    value="{{ $er->applicant->position->nama ?? $user->employee->position->nama }}"
                                    style="Background-color: #eff2f7;" disabled>
                            </div>
                            <div class="col-lg-4 mb-2">
                                <label for="department" class="form-label col-form-label">Departemen / Department</label>
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
                                            id="{{ $key }}_name"
                                            class="form-control form-control-solid mb-3 mb-lg-0"
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
                                        <label class="required fw-semibold fs-6 mb-2">Approval {{ $i }}
                                            As</label>
                                        <select id="{{ $approvalAsKey }}" name="{{ $approvalAsKey }}" class="form-select"
                                            required>
                                            <option value="" disabled
                                                {{ is_null($currentApprovalAs) ? 'selected' : '' }}>Select an option
                                            </option>
                                            @foreach ($options as $value)
                                                <option value="{{ $value }}"
                                                    {{ $currentApprovalAs == $value ? 'selected' : '' }}>
                                                    {{ $value }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endfor
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-12 mb-2">
                                <h5 class="text-center text-primary">Penempatan Karyawan / Placement of Employee</h5>
                            </div>
                            <div class="col-lg-4 mb-2">
                                <label for="needs" class="form-label col-form-label">Kebutuhan / Needs (Orang / Persons)</label>
                                <input type="number" min="1" class="form-control" id="needs" name="needs"
                                    value="{{ old('needs', $er->needs ?? '') }}" placeholder="Hanya Angka / Only Number"
                                    required>
                            </div>
                            <div class="col-lg-4 mb-2">
                                <label class="form-label col-form-label">Jabatan / Position</label>
                                <select class="form-select select2 select2-clear" data-placeholder="Select an option"
                                    name="position_id" id="position_id">
                                    <option></option>
                                    @foreach ($positions as $position)
                                        <option value="{{ $position->id }}"
                                            {{ old('position_id', $er->position_id ?? '') == $position->id ? 'selected' : '' }}>
                                            {{ $position->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-4 mb-2">
                                <label class="form-label col-form-label">Departemen / Department</label>
                                <select class="form-select select2 select2-clear" data-placeholder="Select an option"
                                    name="department_id" id="department_id">
                                    <option></option>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->id }}"
                                            {{ old('department_id', $er->department_id ?? '') == $department->id ? 'selected' : '' }}>
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-4 mb-2">
                                <label class="form-label col-form-label">Bagian / Section</label>
                                <select class="form-select select2 select2-clear" data-placeholder="Select an option"
                                    name="section_id" id="section_id">
                                    <option></option>
                                    @foreach ($sections as $section)
                                        <option value="{{ $section->id }}"
                                            {{ old('section_id', $er->section_id ?? '') == $section->id ? 'selected' : '' }}>
                                            {{ $section->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-4 mb-2">
                                <label class="form-label col-form-label">Area / Area</label>
                                <select class="form-select select2 select2-clear" data-placeholder="Select an option"
                                    name="area_id" id="area_id">
                                    <option></option>
                                    @foreach ($areas as $area)
                                        <option value="{{ $area->id }}"
                                            {{ old('area_id', $er->area_id ?? '') == $area->id ? 'selected' : '' }}>
                                            {{ $area->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-lg-4 mb-2">
                                <label class="form-label col-form-label">Alasan Permintaan / Reason of Requisition</label>
                                <select class="form-select select2" data-placeholder="Select an option"
                                    name="reason_requisition" id="reason_requisition" required>
                                    <option></option>
                                    <option value="Tambahan / Additional"
                                        {{ old('reason_requisition', $er->reason_requisition ?? '') == 'Tambahan / Additional' ? 'selected' : '' }}>
                                        Tambahan / Additional</option>
                                    <option value="Penggantian / Replacement"
                                        {{ old('reason_requisition', $er->reason_requisition ?? '') == 'Penggantian / Replacement' ? 'selected' : '' }}>
                                        Penggantian / Replacement</option>
                                </select>
                            </div>
                            <div id="replacement-fields" class="col-lg-8 row m-0 p-0">
                                <div class="col-lg-6 mb-2">
                                    <label class="form-label col-form-label">Nama yang Diganti / Person Replaced</label>
                                    <select class="form-select select2 replacement-required"
                                        data-placeholder="Select an option" name="person_replaced_id"
                                        id="person_replaced_id">
                                        <option></option>
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->id }}"
                                                {{ old('person_replaced_id', $er->person_replaced_id ?? '') == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->fullname }} ({{ $employee->position->nama ?? '-' }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-6 mb-2">
                                    <label class="form-label col-form-label">Alasan Penggantian / Reason of
                                        Replacement</label>
                                    @php
                                        $replacementReasons = [
                                            'Mengundurkan diri / Resign',
                                            'Kontrak Habis / End Contract',
                                            'Pensiun / Pension',
                                            'PHK / Dismissal',
                                            'Lainnya / Others',
                                        ];
                                        $selectedReplacementReason = old(
                                            'reason_replacement',
                                            $er->reason_replacement ?? '',
                                        );
                                    @endphp
                                    <select class="form-select select2 replacement-required"
                                        data-placeholder="Select an option" name="reason_replacement"
                                        id="reason_replacement">
                                        <option></option>
                                        @foreach ($replacementReasons as $reason)
                                            <option value="{{ $reason }}"
                                                {{ $selectedReplacementReason == $reason ? 'selected' : '' }}>
                                                {{ $reason }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <input type="text" class="form-control mt-2" id="reason_replacement_other"
                                        name="reason_replacement_other"
                                        value="{{ old('reason_replacement_other', $er->reason_replacement_other ?? '') }}"
                                        placeholder="Example: Movement from Outsourcing to HPI" style="display: none;">
                                </div>
                            </div>
                            <div class="col-lg-4 mb-2">
                                <label class="form-label col-form-label">Status Karyawan / Employee Status</label>
                                @php
                                    $employeeStatus = [
                                        'Percobaan / Probation',
                                        'Kontrak / Contract',
                                        'Alih Daya / Outsourcing',
                                        'Magang / Internship',
                                    ];
                                    $selectedEmployeeStatus = old('employee_status', $er->employee_status ?? '');
                                @endphp
                                <select class="form-select select2" data-placeholder="Select an option"
                                    name="employee_status" id="employee_status">
                                    <option></option>
                                    @foreach ($employeeStatus as $status)
                                        <option value="{{ $status }}"
                                            {{ $selectedEmployeeStatus == $status ? 'selected' : '' }}>
                                            {{ $status }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-4 mb-2" id="contract-period-field"
                                style="{{ $selectedEmployeeStatus != 'Kontrak / Contract' ? 'display: none;' : '' }}">
                                <label class="form-label col-form-label">Periode Kontrak / Contract Period</label>
                                <select class="form-select select2" data-placeholder="Select an option"
                                    name="contract_period" id="contract_period">
                                    <option></option>
                                    @for ($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}"
                                            {{ old('contract_period', $er->contract_period ?? '') == $i ? 'selected' : '' }}>
                                            {{ $i . ' Bulan / Months' }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-12 mb-2">
                                <h5 class="text-center text-primary">Persyaratan Karyawan / Employee Requirements</h5>
                            </div>
                            <div class="col-12 mb-2">
                                <label class="form-label col-form-label">Pendidikan / Educational Background</label>
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
                                                    <input class="form-check-input education-checkbox" type="checkbox"
                                                        value="{{ $eduName }}" name="education_names[]"
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
                                                    placeholder="Jurusan / Major"
                                                    value="{{ $selectedMajors[$eduName] ?? '' }}" maxlength="20">
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
                                                    <input class="form-check-input gender-checkbox" type="checkbox"
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
                                                    <input type="number" min="1"
                                                        class="form-control gender-needs-count"
                                                        data-name-original="gender_needs[{{ $genderName }}]"
                                                        name="{{ $isChecked ? "gender_needs[{$genderName}]" : '' }}"
                                                        placeholder="Jumlah / Count" value="{{ $needsCount }}">
                                                </div>
                                                <div class="col-lg-8 ps-2">
                                                    <div class="input-group">
                                                        <input type="number" min="1"
                                                            class="form-control gender-age-input"
                                                            data-name-original="gender_start_age[{{ $genderName }}]"
                                                            name="{{ $isChecked ? "gender_start_age[{$genderName}]" : '' }}"
                                                            placeholder="Usia Minimal / Min. Age (Tahun / Years)"
                                                            value="{{ $startAge }}">
                                                        <span class="input-group-text">s/d</span>
                                                        <input type="number" min="1"
                                                            data-name-original="gender_end_age[{{ $genderName }}]"
                                                            name="{{ $isChecked ? "gender_end_age[{$genderName}]" : '' }}"
                                                            class="form-control gender-age-input"
                                                            placeholder="Usia Maksimal / Max. Age (Tahun / Years)"
                                                            value="{{ $endAge }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="col-lg-4 mb-2">
                                <label class="form-label col-form-label">Pengalaman Bekerja / Work Experience</label>
                                @php
                                    $workExperience = [
                                        'Dibutuhkan / Required',
                                        'Tidak dibutuhkan / Not Required (Freshgraduate)',
                                    ];
                                    $selectedExperience = old('work_experience', $er->work_experience ?? '');
                                @endphp
                                <select class="form-select select2" data-placeholder="Select an option"
                                    name="work_experience" id="work_experience">
                                    <option></option>
                                    @foreach ($workExperience as $experience)
                                        <option value="{{ $experience }}"
                                            {{ $selectedExperience == $experience ? 'selected' : '' }}>
                                            {{ $experience }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-4 mb-2" id="duration-experience-field"
                                style="{{ $selectedExperience != 'Dibutuhkan / Required' ? 'display: none;' : '' }}">
                                <label class="form-label col-form-label">Pengalaman / Experience (Tahun / Years)</label>
                                <input type="number" min="1" class="form-control" id="duration_work_experience"
                                    name="duration_work_experience"
                                    value="{{ old('duration_work_experience', $er->duration_work_experience ?? '') }}"
                                    placeholder="Hanya Angka / Only Number">
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-12 mb-2">
                                <h5 class="text-center text-primary">Kualifikasi dan Persyaratan Keterampilan Khusus /
                                    Qualification and Special Skills Requirements<span class="text-danger"><br>(Max. <span id="count_qualification">1000</span> Character)</span></h5>
                            </div>
                            <div class="col-12 mb-2">
                                <textarea class="form-control" id="qualification" name="qualification" rows="7" maxlength="1000"
                                    placeholder="Describe the qualification for new employees..." required>{{ old('qualification', $er->qualification ?? '') }}</textarea>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-lg-4 mb-2">
                                <label class="form-label col-form-label">Tanggal Mulai Bekerja / Employment Date</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="employment_date"
                                        name="employment_date" placeholder="Select Date"
                                        value="{{ old('employment_date', optional($er)->employment_date ? optional($er)->employment_date->format('d/m/Y') : '') }}"
                                        required>
                                    <span class="input-group-text" id="basic-addon2"><i
                                            class="ri-calendar-todo-line"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="row" id="form-submit">
                            <div class="col-12">
                                <div class="hstack gap-2 d-print-none mt-4" style="justify-content: flex-end;">
                                    <button type="button" name="status" value="DRAFT" class="btn btn-secondary" id="draftButton">
                                        DRAFT
                                    </button>
                                    <button type="button" class="btn btn-primary" id="submitButton">
                                        SUBMIT
                                    </button>
                                    <div class="modal fade" id="submitModal" tabindex="-1"
                                        aria-labelledby="submitModalLabel" aria-hidden="true" style="display: none;">
                                        <div class="modal-dialog modal-dialog-top">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="submitModalLabel">Submit Employee
                                                        Requisition</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body text-center p-5">
                                                    <p class="text-muted">Are you sure you want to submit this Employee
                                                        Requisition?
                                                    </p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" name="status" value="SUBMIT" form="FormER"
                                                        class="btn btn-primary">Yes, Submit</button>
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
    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="{{ url('') }}/assets/libs/adminlte/select2/js/4.1.0/select2.min.js"></script>
    <script src="{{ url('') }}/assets/libs/flatpickr/flatpickr.min.js"></script>
@endsection

@section('javascript')
    <script type="text/javascript">
        let swalert;
        $(document).ready(function() {
            $('.select2').not('.select2-clear').select2();
            $('.select2-clear').select2({
                allowClear: true,
                placeholder: "Select an option"
            });

            function toggleReplacementFields() {
                var reason = $('#reason_requisition').val();
                var isReplacement = reason === 'Penggantian / Replacement';
                if (isReplacement) {
                    $('#replacement-fields').show();
                    $('.replacement-required').attr('required', true);
                    toggleOtherReasonField();
                } else {
                    $('#replacement-fields').hide();
                    $('.replacement-required').removeAttr('required');
                    $('#reason_replacement_other').hide().removeAttr('required').val('');
                }
            }

            function toggleOtherReasonField() {
                var replacementReason = $('#reason_replacement').val();
                var reasonOtherInput = $('#reason_replacement_other');
                if (replacementReason === 'Lainnya / Others') {
                    reasonOtherInput.show().attr('required', true);
                } else {
                    reasonOtherInput.hide().removeAttr('required').val('');
                }
            }

            $('#reason_requisition').on('change', toggleReplacementFields);
            $('#reason_replacement').on('change', toggleOtherReasonField);
            toggleReplacementFields();
            toggleOtherReasonField();

            var contractField = $('#contract-period-field');
            var contractSelect = $('#contract_period');

            function toggleContractPeriod() {
                var selectedStatus = $('#employee_status').val();
                if (selectedStatus === 'Kontrak / Contract') {
                    contractField.show();
                    contractSelect.attr('required', true);
                } else {
                    contractField.hide();
                    contractSelect.removeAttr('required');
                    contractSelect.val(null).trigger('change');
                }
            }
            $('#employee_status').on('change', toggleContractPeriod);
            toggleContractPeriod();

            // Education
            function setupEducationCheckboxes() {
                const container = $('#education-requirements-container');
                const otherPlaceholder = 'Kualifikasi Lainnya / Other Qualification';
                container.on('change', '.education-checkbox', function(e) {
                    const checkbox = $(this);
                    const checkboxValue = checkbox.val();
                    const row = checkbox.closest('.education-item');
                    const majorContainer = row.find('.major-input-container');
                    const majorInput = row.find('.major-input');
                    const dynamicPlaceholder = `Jurusan / Major (${checkboxValue})`;
                    if (checkbox.is(':checked')) {
                        majorContainer.show();
                        majorInput.attr('required', true);
                        if (checkboxValue === 'Lainnya / Others') {
                            majorInput.attr('placeholder', otherPlaceholder);
                        } else {
                            majorInput.attr('placeholder', dynamicPlaceholder);
                        }
                    } else {
                        majorContainer.hide();
                        majorInput.val('');
                        majorInput.removeAttr('required');
                        majorInput.attr('placeholder', 'Jurusan / Major');
                    }
                });

                container.find('.education-checkbox').each(function() {
                    const checkbox = $(this);
                    const checkboxValue = checkbox.val();
                    const row = checkbox.closest('.education-item');
                    const majorContainer = row.find('.major-input-container');
                    const majorInput = row.find('.major-input');
                    const dynamicPlaceholder = `Jurusan / Major (${checkboxValue})`;
                    if (checkbox.is(':checked')) {
                        majorContainer.show();
                        majorInput.attr('required', true);
                        if (checkboxValue === 'Lainnya / Others') {
                            majorInput.attr('placeholder', otherPlaceholder);
                        } else {
                            majorInput.attr('placeholder', dynamicPlaceholder);
                        }
                    } else {
                        majorContainer.hide();
                        majorInput.removeAttr('required');
                        majorInput.attr('placeholder', 'Jurusan / Major');
                    }
                });
            }
            setupEducationCheckboxes();
            // End Education

            // Gender
            function setupGenderCheckboxes() {
                const container = $('#gender-requirements-container');
                const defaultCountPlaceholder = 'Jumlah / Count';
                container.on('change', '.gender-checkbox', function() {
                    const checkbox = $(this);
                    const genderName = checkbox.val();
                    const row = checkbox.closest('.gender-item');
                    const detailsContainer = row.find('.gender-details-container');
                    const allInputs = detailsContainer.find('input');
                    const needsCountInput = row.find('.gender-needs-count');
                    const dynamicCountPlaceholder = `${defaultCountPlaceholder} (${genderName})`;
                    if (checkbox.is(':checked')) {
                        detailsContainer.css('display', 'flex');
                        allInputs.attr('required', true).removeAttr('disabled');
                        allInputs.each(function() {
                            const input = $(this);
                            const originalName = input.attr('data-name-original');
                            if (originalName) {
                                input.attr('name', originalName);
                            }
                        });
                        needsCountInput.attr('placeholder', dynamicCountPlaceholder);
                    } else {
                        detailsContainer.hide();
                        allInputs.each(function() {
                            $(this).removeAttr('name');
                        });
                        allInputs.attr('disabled', true).removeAttr('required').val('');
                        needsCountInput.attr('placeholder', defaultCountPlaceholder);
                    }
                    validateTotalNeeds();
                });
            }

            function validateTotalNeeds() {
                const totalNeeds = parseInt($('#needs').val()) || 0;
                let totalGenderCount = 0;
                $('.gender-needs-count').each(function() {
                    const inputElement = $(this);
                    if (inputElement.attr('name')) {
                        totalGenderCount += parseInt(inputElement.val()) || 0;
                    }
                });
                if (totalGenderCount > totalNeeds) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Jumlah Melebihi Kebutuhan\n(Count Needs Exceeded)',
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'OK'
                    });
                    return false;
                }
                return true;
            }

            function validateTotalNeedsForSubmit() {
                const totalNeeds = parseInt($('#needs').val()) || 0;
                let totalGenderCount = 0;
                $('.gender-needs-count').each(function() {
                    if ($(this).attr('name')) {
                        totalGenderCount += parseInt($(this).val()) || 0;
                    }
                });
                if (totalGenderCount !== totalNeeds) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Jumlah Tidak Sama\n(Total Mismatch)',
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        const firstActiveGenderCountInput = $('#gender-requirements-container')
                            .find('.gender-needs-count[name^="gender_needs"]:visible')
                            .first();
                        if (firstActiveGenderCountInput.length) {
                            setTimeout(() => {
                                firstActiveGenderCountInput.focus();
                            }, 500);
                        } else {
                            $('#needs').focus();
                        }
                    });
                    return false;
                }
                return true;
            }

            setupGenderCheckboxes();
            validateTotalNeeds();
            $('#gender-requirements-container').on('input change keyup', '.gender-needs-count', validateTotalNeeds);
            // End Gender

            function checkMinimumSelection() {
                const educationChecked = $('#education-requirements-container').find('.education-checkbox:checked')
                    .length > 0;
                const genderChecked = $('#gender-requirements-container').find('.gender-checkbox:checked').length >
                    0;
                if (!educationChecked) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Pendidikan belum Dipilih\n(Education is Required)',
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        setTimeout(() => {
                            $('#education-requirements-container').find('.education-checkbox')
                                .first().focus();
                        }, 500);
                    });
                    return false;
                }
                if (!genderChecked) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Jenis Kelamin belum Dipilih\n(Gender is Required)',
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        setTimeout(() => {
                            $('#gender-requirements-container').find('.gender-checkbox').first()
                                .focus();
                        }, 500);
                    });
                    return false;
                }
                return true;
            }

            var durationField = $('#duration-experience-field');
            var durationInput = $('#duration_work_experience');

            function toggleExperience() {
                var selectedExperience = $('#work_experience').val();
                if (selectedExperience === 'Dibutuhkan / Required') {
                    durationField.show();
                    durationInput.attr('required', true);
                } else {
                    durationField.hide();
                    durationInput.removeAttr('required');
                    durationInput.val(null).trigger('change');
                }
            }
            $('#work_experience').on('change', toggleExperience);
            toggleExperience();

            var submitModal = new bootstrap.Modal(document.getElementById('submitModal'));
            let submissionType = 'DRAFT';
            let lastClickedSubmit;

            $("form button[type='submit'], #submitButton").on("click", function() {
                const clickedButton = $(this);
                if (clickedButton.attr('name') === 'status') {
                    submissionType = clickedButton.val();
                } else if (clickedButton.attr('id') === 'submitButton') {
                    submissionType = 'SUBMIT';
                }
                lastClickedSubmit = clickedButton;
            });

            $('#draftButton').on('click', function(e) {
                submissionType = 'DRAFT';
                lastClickedSubmit = $(this);
                const form = document.getElementById('FormER');
                $(form).find('[required]').each(function() {
                    $(this).attr('data-temp-required', true).removeAttr('required');
                });
                $(form).trigger('submit');
            });

            $('#submitButton').on('click', function() {
                submissionType = 'SUBMIT';
                var form = document.getElementById('FormER');
                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }
                if (checkMinimumSelection() === false) {
                    return;
                }
                if (validateTotalNeedsForSubmit() === false) {
                    return;
                }
                submitModal.show();
            });

            $("form").submit(function(e) {
                e.preventDefault();
                const form = this;
                const isDraft = (lastClickedSubmit && lastClickedSubmit.attr("value") === 'DRAFT');
                if (isDraft) {
                    $(form).find('[required]').each(function() {
                        $(this).attr('data-temp-required', true).removeAttr('required');
                    });
                }
                if (!isDraft) {
                    if (checkMinimumSelection() === false || validateTotalNeedsForSubmit() === false) {
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
                    if (!form.reportValidity()) {
                        return;
                    }
                }

                swalert = Swal.fire({
                    title: 'Loading!',
                    didOpen: () => {
                        Swal.showLoading()
                    }
                });

                const formData = new FormData(form);
                formData.append('status', isDraft ? 'DRAFT' : 'SUBMIT');

                $.ajax({
                    url: $(this).attr("action"),
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $(form).find('[data-temp-required]').each(function() {
                            $(this).attr('required', true).removeAttr('data-temp-required');
                        });
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
                        $(form).find('[data-temp-required]').each(function() {
                            $(this).attr('required', true).removeAttr('data-temp-required');
                        });
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
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    }
                });
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
                { id: 'qualification', max: 1000 },
            ];
            inputToMonitor.forEach(item => {
                updateCharacterCount(item.id, `count_${item.id}`, item.max); 
            });
        });

        $('#employment_date').flatpickr({
            allowInput: true,
            altInput: false,
            altFormat: "d F, Y",
            dateFormat: "d/m/Y",
            minDate: "today",
        });
    </script>
    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: '{{ session('success') }}',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
            });
        </script>
    @endif
    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '{{ session('error') }}',
                confirmButtonColor: '#d33',
                confirmButtonText: 'OK'
            });
        </script>
    @endif
    @if ($errors->any())
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                html: `{!! implode('<br>', $errors->all()) !!}`,
                confirmButtonColor: '#d33',
                confirmButtonText: 'OK'
            });
        </script>
    @endif
@endsection
