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
                            <h4 class="text-primary">Review Permohonan Permintaan Karyawan</h4>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ route('recruitment.emp.index') }}"
                                class="btn btn-primary btn-label waves-effect waves-light float-end"><i
                                    class="ri-arrow-left-circle-line label-icon align-middle fs-16 me-2"></i> Back</a>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-12">
                            <h5 class="text-center text-primary">Approval Information</h5>
                        </div>
                        @php
                            $approvalSteps = ['approval1', 'approval2', 'approval3', 'approval4'];
                            $count = 0;
                            $totalApprovedSteps = 0;
                            $lastApprovalRole = null;
                            foreach($approvalSteps as $step) {
                                if(isset($er->{$step . '_id'})) {
                                    $totalApprovedSteps++;
                                    $lastApprovalRole = $step; 
                                }
                            }
                            $isLastApproval = ($role == $lastApprovalRole);
                        @endphp
                        @foreach($approvalSteps as $step)
                            @if(isset($er->{$step . '_id'}))
                                <div class="col-md-4">
                                    <p><strong>As
                                            <span class="d-none d-sm-inline"> : </span></strong>
                                        <span class="d-sm-none"><br></span>
                                        {{ $er->{$step . '_as'} }}
                                        @if($role == $step)
                                            <span class="fw-bold text-success">(You)</span>
                                        @endif
                                    </p>
                                    <p><strong>Name
                                            <span class="d-none d-sm-inline"> : </span></strong>
                                        <span class="d-sm-none"><br></span>
                                        {{ strtoupper($er->{$step}->fullname ?? '') }}
                                    </p>
                                    <p><strong>Position
                                            <span class="d-none d-sm-inline"> : </span></strong>
                                        <span class="d-sm-none"><br></span>
                                        {{ strtoupper($er->{$step}->position->nama ?? '') }}
                                    </p>
                                    <p><strong>Department / Section
                                            <span class="d-none d-sm-inline"> : </span></strong>
                                        <span class="d-sm-none"><br></span>
                                        {{ strtoupper($er->{$step}->department->name) }}
                                        {{ isset($er->{$step}->section->nama) 
                                            ? ' / ' . strtoupper($er->{$step}->section->nama) 
                                            : ' / NA' }}
                                    </p>
                                </div> 
                                @php
                                    $count++;
                                @endphp
                                @if ($count % 3 == 0 && $count < $totalApprovedSteps)
                                    <div class="col-12">
                                        <hr>
                                    </div>
                                @endif
                            @endif
                        @endforeach
                    </div>
                    <hr>
                    <form id="FormER" action="{{ route('recruitment.emp.er.approve.form.store', ['token' => $token]) }}"
                        method="POST">
                        @csrf
                        @method('POST')
                        <input type="hidden" name="id" id="id" value="{{ $er->id ?? '' }}">
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
                                <input type="text" class="form-control" value="{{ $er->reason_requisition }}"
                                    disabled>
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
                                                        type="checkbox" value="{{ $eduName }}"
                                                        name="education_names[]" id="edu_{{ Str::slug($eduName) }}"
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
                                                    <input disabled class="form-check-input gender-checkbox"
                                                        type="checkbox" value="{{ $genderName }}"
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
                                                            disabled
                                                            value="Usia / Age : {{ $startAge }} Tahun / Years">
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
                                    <input type="text" class="form-control" id="employment_date"
                                        name="employment_date" placeholder="Select Date"
                                        value="{{ old('employment_date', optional($er)->employment_date ? optional($er)->employment_date->format('d/m/Y') : '') }}"
                                        disabled>
                                    <span class="input-group-text" id="basic-addon2"><i
                                            class="ri-calendar-todo-line"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="row" id="form-submit">
                            <div class="col-12">
                                <div class="hstack gap-2 d-print-none mt-4" style="justify-content: flex-end;">
                                    @if ($isLastApproval)
                                        <button type="button" class="btn btn-danger approval-action-button" name="status"
                                            value="DISAPPROVED" data-action="DISAPPROVED" title="Ditolak / Disapproved">
                                            DISAPPROVED
                                        </button>
                                        <button type="button" class="btn btn-warning approval-action-button" name="status"
                                            value="PENDING" data-action="PENDING" title="Ditunda / Pending">
                                            PENDING
                                        </button>
                                        <button type="button" class="btn btn-success approval-action-button" name="status"
                                            value="APPROVED" data-action="APPROVED" title="Disetujui / Approved">
                                            APPROVED
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-primary approval-action-button" name="status"
                                            value="SUBMIT_NON_LAST" data-action="SUBMIT" title="Kirim / Submit"> 
                                            SUBMIT
                                        </button>
                                        <button type="button" class="btn btn-dark"
                                            data-bs-toggle="modal" data-bs-target="#reviceModal">
                                            REJECT
                                        </button>
                                    @endif
                                    <div class="modal fade" id="submitModal" tabindex="-1"
                                        aria-labelledby="submitModalLabel" aria-hidden="true" style="display: none;">
                                        <div class="modal-dialog modal-dialog-top">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="submitModalLabel">
                                                        <span id="modal-title-action"></span> Employee Requisition
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p class="text-muted mb-1">Are you sure you want to
                                                        <span id="modal-body-action"
                                                            class="fw-bold"></span>
                                                        this Employee Requisition?
                                                    </p>
                                                    @if ($isLastApproval)
                                                        <div id="comment-section">
                                                            <input type="text" class="form-control" name="decision_comment" id="decision_comment_modal"
                                                                placeholder="Tambahkan Komentar / Input your Comment" maxlength="60">
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" name="status" id="modalSubmitButton"
                                                        value="" form="FormER" class="btn">Yes, <span
                                                            id="modal-button-text"></span></button>
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
                    <div class="modal fade" id="reviceModal" tabindex="-1" aria-labelledby="reviceModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header text-white">
                                    <h5 class="modal-title" id="reviceModalLabel">Reject Employee Requisition</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <form id="FormRevise" action="{{ route('recruitment.emp.er.approve.reject', ['token' => $token]) }}"
                                    method="POST">
                                    @csrf
                                    <div class="modal-body">
                                        <label for="revice_reason" class="form-label">Please provide a reason for reject :</label>
                                        <input type="text" class="form-control" name="reason_comment" id="reason_comment_revice"
                                            placeholder="Masukkan Alasan / Input your Reason" maxlength="60" required>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-dark">Reject</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
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
        function toTitleCase(str) {
            return str.toLowerCase().split(' ').map(function(word) {
                return (word.charAt(0).toUpperCase() + word.slice(1));
            }).join(' ');
        }
        $(document).ready(function() {
            $('.select2').not('.select2-clear').select2();
            $('.select2-clear').select2({
                allowClear: true,
                placeholder: "Select an option"
            });

            var submitModal = new bootstrap.Modal(document.getElementById('submitModal'));
            let submissionType = 'SUBMIT';
            let lastClickedSubmit;

            $(".approval-action-button").on("click", function() {
                const clickedButton = $(this);
                if (clickedButton.attr('name') === 'status') {
                    submissionType = clickedButton.val();
                } else if (clickedButton.attr('id') === 'submitButtonOnly') {
                    submissionType = 'SUBMIT';
                }
                lastClickedSubmit = clickedButton;
            });

            $(".approval-action-button").on("click", function(e) {
                const clickedButton = $(this);
                submissionType = clickedButton.val();
                if (submissionType !== 'SUBMIT_NON_LAST' && submissionType !== 'DRAFT') {
                    const form = document.getElementById('FormER');
                    if (!form.reportValidity()) {
                        if (e && e.stopImmediatePropagation) {
                            e.stopImmediatePropagation();
                        }
                        return;
                    }
                }

                let actionText = submissionType;
                if (submissionType === 'SUBMIT_NON_LAST') {
                    actionText = 'SUBMIT';
                    submissionType = 'SUBMIT';
                }
                
                const actionTitle = toTitleCase(actionText); 
                let modalClass = 'btn-primary';

                if (actionText === 'APPROVED') {
                    modalClass = 'btn-success';
                } else if (actionText === 'DISAPPROVED') {
                    modalClass = 'btn-danger';
                } else if (actionText === 'PENDING') {
                    modalClass = 'btn-warning';
                }

                $('#modal-title-action').text(actionTitle);
                $('#modal-body-action').text(actionTitle);
                $('#modal-button-text').text(actionTitle);
                
                const modalSubmitButton = $('#modalSubmitButton');
                modalSubmitButton.removeClass().addClass('btn ' + modalClass);
                modalSubmitButton.val(submissionType); 
                submitModal.show();
            });

            $('#submitModal').on('shown.bs.modal', function () {
                const commentField = $('#decision_comment_modal');
                if (submissionType === 'DISAPPROVED' || submissionType === 'PENDING') {
                    commentField.prop('required', true);
                } else {
                    commentField.prop('required', false);
                }
            });

            $('#submitModal').on('hidden.bs.modal', function () {
                const commentField = $('#decision_comment_modal');
                commentField.prop('required', false); 
            });

            $("#FormER").submit(function(e) { 
                e.preventDefault();
                const form = this;
                const actionToSend = submissionType;
                const isDraft = (actionToSend === 'DRAFT');

                if (isDraft) {
                    $(form).find('[required]').each(function() {
                        $(this).attr('data-temp-required', true).removeAttr('required');
                    });
                }

                if (!isDraft && actionToSend !== 'SUBMIT') {
                    if (!form.reportValidity()) {
                        if (isDraft) {
                            $(form).find('[data-temp-required]').each(function() {
                                $(this).attr('required', true).removeAttr('data-temp-required');
                            });
                        }
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
                formData.append('status', actionToSend);
                submitModal.hide();

                $.ajax({
                    url: $(this).attr("action"),
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $(form).find('[data-temp-required]').each(function() {
                            $(this).attr('required', true).removeAttr(
                                'data-temp-required');
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
                            $(this).attr('required', true).removeAttr(
                                'data-temp-required');
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

            $("#FormRevise").submit(function(e) { 
                e.preventDefault();
                const form = this;
                if (!form.reportValidity()) {
                    return; 
                }
                swalert = Swal.fire({
                    title: 'Loading!',
                    didOpen: () => {
                        Swal.showLoading()
                    }
                });
                const formData = new FormData(form);
                const reviseModal = bootstrap.Modal.getInstance(document.getElementById('reviceModal'));
                reviseModal.hide();

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
