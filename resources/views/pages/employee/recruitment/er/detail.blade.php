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
                            <h4 class="text-primary">Detail Permohonan Permintaan Karyawan</h4>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ route('recruitment.emp.index') }}"
                                class="btn btn-primary btn-label waves-effect waves-light float-end"><i
                                    class="ri-arrow-left-circle-line label-icon align-middle fs-16 me-2"></i> Back</a>
                        </div>
                    </div>
                    <hr>
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
                                    <input disabled style="cursor: not-allowed" type="text" id="{{ $key }}_name"
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
                                        id="{{ $key }}_email" class="form-control form-control-solid mb-3 mb-lg-0"
                                        value="{{ optional(optional($currentApprovalObject)->user)->email ?? '-' }}" />
                                </div>
                                <div class="col-lg-3 col-sm-6 p-2">
                                    <label class="required fw-semibold fs-6 mb-2">Approval
                                        {{ $i }}
                                        As</label>
                                    <input disabled style="cursor: not-allowed" type="text"
                                        id="{{ $key }}_email" class="form-control form-control-solid mb-3 mb-lg-0"
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
                                Needs (Orang / Persons)</label>
                            <input type="text" class="form-control" value="{{ $er->needs ?? '0' }} Orang / Persons"
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
                                                    value="{{ $needsCount }} Orang / Persons" disabled>
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
                            <label class="form-label col-form-label">Sumber Rekrutmen / Recruitment Source</label>
                            @php
                                $recSource = [
                                    'Manual Source / Job Submission',
                                    'Internet / Job Posting',
                                    'Head Hunter / Talent Search',
                                    'Others',
                                ];
                                $recruitSource = $er->recruitmentSources ?? collect();
                                $selectedRecSource = old(
                                    'recruitment_source',
                                    $recruitSource->pluck('name')->toArray(),
                                );
                                $selectedOtherDetail = $recruitSource->where('name', 'Others')->first()->pivot->other_detail ?? '';
                            @endphp
                            <div id="recruitment-source-container">
                                @foreach ($recSource as $rs)
                                    @php
                                        $isChecked = in_array($rs, $selectedRecSource);
                                    @endphp
                                    <div class="row align-items-center mb-1 recsource-item">
                                        <div class="col-12">
                                            <div class="form-check">
                                                <input class="form-check-input recsource-checkbox" type="checkbox"
                                                    value="{{ $rs }}" name="recruitment_source[]"
                                                    id="rs_{{ Str::slug($rs) }}"
                                                    {{ $isChecked ? 'checked' : '' }} disabled>
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
                                                    value="{{ old('other_source.Others', $selectedOtherDetail) }}" disabled>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
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
                                <input type="text" class="form-control" value="{{ $er->decision_comment ?? '' }}" disabled>
                            @endif
                        </div>
                        @endif
                    </div>
                    @endif
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
