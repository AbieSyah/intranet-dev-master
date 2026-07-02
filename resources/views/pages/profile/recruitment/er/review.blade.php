@extends('layouts.master')
@section('link')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css" />
    <!-- Datatables-->
    <link href="{{ url('') }}/assets/libs/Datatables/DataTables-1.13.1/css/dataTables.bootstrap5.css" rel="stylesheet"
        type="text/css" />
    <link href="{{ url('') }}/assets/libs/Datatables/Buttons-2.3.3/css/buttons.dataTables.css" rel="stylesheet"
        type="text/css" />
    <link href="{{ url('') }}/assets/libs/Datatables/Responsive-2.4.0/css/responsive.bootstrap.css" rel="stylesheet"
        type="text/css" />
    <!-- Toastr Notifications-->
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="{{ url('') }}/assets/libs/adminlte/select2/css/4.1.0/select2.min.css">
    <style type="text/css">
        img {
            /* display: block; */
            max-width: 100%;
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

        .table-responsive {
            overflow: visible;
        }

        div.dataTables_wrapper {
            width: 100%;
        }

        .hidden-column {
            display: none;
        }

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
    <div class="container-fluid">
        <div class="profile-foreground position-relative mx-n4 mt-n4">
            <div class="profile-wid-bg">
                <img src="/assets/images/salonpas-bg.jpg" alt="" class="profile-wid-img" />
            </div>
        </div>
        <div class="pt-4 mb-4 mb-lg-3 pb-lg-4">
            <div class="row g-4">
                <div class="col-auto">
                    <div class="profile-user position-relative d-inline-block mx-auto">
                        @if (!empty($user->employee->avatar))
                            <div id="avatar-user">
                                <img src="{{ asset('storage/avatars/' . $user->employee->avatar) }}"
                                    class="show-image rounded-circle avatar-xl img-thumbnail user-profile-image"
                                    alt="user-profile-image">
                            </div>
                        @else
                            <div id="avatar-user">
                                <img src="{{ asset('storage/avatars/user.jpg') }}"
                                    class="rounded-circle avatar-xl img-thumbnail user-profile-image"
                                    alt="user-profile-image">
                            </div>
                        @endif
                        <div class="avatar-xs p-0 rounded-circle profile-photo-edit">
                            <input onchange="avatarValidation(this);" id="profile-img-file-input" type="file"
                                name="image" class="image profile-img-file-input"
                                accept="image/jpg,image/jpeg,image/png,image/JPG,image/JPEG,image/PNG">
                            <label for="profile-img-file-input" class="profile-photo-edit avatar-xs">
                                <span class="avatar-title rounded-circle bg-light text-body">
                                    <i class="ri-camera-fill"></i>
                                </span>
                            </label>
                        </div>
                    </div>
                </div>
                <!--end col-->
                <div class="col">
                    <div class="p-2">
                        <h3 class="text-white mb-1">{{ $user->employee->fullname }}</h3>
                        <p class="text-white-75">{{ $user->employee->email }}</p>
                        <div class="hstack text-white-50 gap-1">
                            <div class="me-2"><i class="ri-map-pin-user-line me-1 text-white-75 fs-16 align-middle"></i>
                                {{ $user->employee->area->name }}
                            </div>
                            <div><i class="ri-building-line me-1 text-white-75 fs-16 align-middle"></i>
                                {{ $user->employee->department->name }}
                            </div>
                        </div>
                        <div class="hstack text-white-50 gap-1">
                            <div class="me-2">
                                @if (!empty($user->employee->level->nama))
                                    <i class="ri-contacts-book-2-line me-1 text-white-75 fs-16 align-middle"></i>
                                    {{ $user->employee->level->nama }}
                                @endif
                            </div>
                            <div>
                                @if (!empty($user->employee->position->nama))
                                    <i class="ri-contacts-book-line me-1 text-white-75 fs-16 align-middle"></i>
                                    {{ $user->employee->position->nama }}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <!--end col-->
                <div class="col-12 col-lg-auto order-last order-lg-0">
                    <div class="row text text-white-50 text-center">
                        <div class="col-lg-6 col-4">
                            <div class="p-2">
                                <!-- <h4 class="text-white mb-1">{{ $user->employee->nik }}</h4>
                                                                                                            <p class="fs-14 mb-0">NIK</p> -->
                            </div>
                        </div>
                    </div>
                </div>
                <!--end col-->

            </div>
            <!--end row-->
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div>
                    <div class="d-flex">
                        <!-- Nav tabs -->
                        @include('partials.navbar2')
                    </div>
                    <!-- Navbar -->
                    <div class="row pt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <h4 class="text-primary">Review Permohonan Permintaan Karyawan</h4>
                                        </div>
                                        <div class="col-md-6">
                                            <a href="{{ route('recruitment.profile.index') }}"
                                                class="btn btn-primary btn-label waves-effect waves-light float-end"><i
                                                    class="ri-arrow-left-circle-line label-icon align-middle fs-16 me-2"></i>
                                                Back</a>
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
                                            foreach ($approvalSteps as $step) {
                                                if (isset($er->{$step . '_id'})) {
                                                    $totalApprovedSteps++;
                                                }
                                            }
                                        @endphp
                                        @foreach ($approvalSteps as $step)
                                            @if (isset($er->{$step . '_id'}))
                                                <div class="col-md-4">
                                                    <p><strong>As
                                                            <span class="d-none d-sm-inline"> : </span></strong>
                                                        <span class="d-sm-none"><br></span>
                                                        {{ $er->{$step . '_as'} }}
                                                        @if ($role == $step)
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
                                                        {{ isset($er->{$step}->section->nama) ? ' / ' . strtoupper($er->{$step}->section->nama) : ' / NA' }}
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
                                    <form id="FormER"
                                        action="{{ route('recruitment.profile.er.approve.form.store', ['token' => $token]) }}"
                                        method="POST">
                                        @csrf
                                        @method('POST')
                                        <input type="hidden" name="id" id="id"
                                            value="{{ $er->id ?? '' }}">
                                        <div class="row">
                                            <div class="col-12 mb-2">
                                                <h5 class="text-center text-primary">Pemohon / Applicant</h5>
                                            </div>
                                            <div class="col-lg-4 mb-2">
                                                <label for="applicant_name" class="form-label col-form-label">Nama Pemohon
                                                    /
                                                    Applicant
                                                    Name</label>
                                                <input type="hidden" class="form-control" id="applicant_id"
                                                    name="applicant_id"
                                                    value="{{ old('applicant_id', $er->applicant_id ?? $user->employee->id) }}"
                                                    required>
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
                                                <h5 class="text-center text-primary">Penempatan Karyawan / Placement of Employee</h5>
                                            </div>
                                            <div class="col-lg-4 mb-2">
                                                <label for="needs" class="form-label col-form-label">Kebutuhan / Needs (Orang / Persons)</label>
                                                <input type="text" class="form-control" value="{{ $er->needs ?? '0' }} Orang / Persons" disabled>
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
                                                <input type="text" class="form-control"
                                                    value="{{ $er->reason_requisition }}" disabled>
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
                                                        <label class="form-label col-form-label">Alasan Penggantian /
                                                            Reason of
                                                            Replacement</label>
                                                        <input type="text" class="form-control"
                                                            value="{{ optional($er)->reason_replacement }}" disabled>
                                                        @if (!empty($er->reason_replacement_other))
                                                            <input type="text" class="form-control mt-2"
                                                                value="{{ optional($er)->reason_replacement_other }}"
                                                                disabled>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="col-lg-4 mb-2">
                                                <label class="form-label col-form-label">Status Karyawan / Employee
                                                    Status</label>
                                                <input type="text" class="form-control"
                                                    value="{{ optional($er)->employee_status }}" disabled>
                                            </div>
                                            @if (!empty($er->contract_period))
                                                <div class="col-lg-4 mb-2" id="contract-period-field">
                                                    <label class="form-label col-form-label">Periode Kontrak / Contract
                                                        Period</label>
                                                    <input type="text" class="form-control"
                                                        value="{{ optional($er)->contract_period }} Bulan / Months"
                                                        disabled>
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
                                                    $educationalRequirements =
                                                        $er->educationalRequirements ?? collect();
                                                    $selectedEducationNames = old(
                                                        'education_names',
                                                        $educationalRequirements->pluck('name')->toArray(),
                                                    );
                                                    $selectedMajors = old(
                                                        'major_requirements',
                                                        $educationalRequirements
                                                            ->pluck('pivot.major', 'name')
                                                            ->toArray(),
                                                    );
                                                @endphp
                                                <div id="education-requirements-container">
                                                    @foreach ($educationRequirement as $eduName)
                                                        <div class="row align-items-center mb-1 education-item">
                                                            <div class="col-lg-4">
                                                                <div class="form-check">
                                                                    <input disabled
                                                                        class="form-check-input education-checkbox"
                                                                        type="checkbox" value="{{ $eduName }}"
                                                                        name="education_names[]"
                                                                        id="edu_{{ Str::slug($eduName) }}"
                                                                        {{ in_array($eduName, $selectedEducationNames) ? 'checked' : '' }}>
                                                                    <label class="form-check-label"
                                                                        for="edu_{{ Str::slug($eduName) }}">
                                                                        {{ $eduName }}
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-8 major-input-container"
                                                                style="{{ in_array($eduName, $selectedEducationNames) ? '' : 'display:none;' }}">
                                                                <input type="text" class="form-control major-input"
                                                                    name="major_requirements[{{ $eduName }}]"
                                                                    value="{{ $selectedMajors[$eduName] ?? '' }}"
                                                                    disabled>
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
                                                            $endAge = old(
                                                                "gender_end_age.{$genderName}",
                                                                $details['end_age'] ?? null,
                                                            );
                                                            $isChecked =
                                                                !is_null($needsCount) ||
                                                                old("gender_select.{$genderName}");
                                                            $nameAttr = $isChecked ? 'name' : 'data-name-original';
                                                        @endphp
                                                        <div class="row align-items-center mb-2 gender-item">
                                                            <div class="col-lg-2">
                                                                <div class="form-check">
                                                                    <input disabled
                                                                        class="form-check-input gender-checkbox"
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
                                                                    <input type="text"
                                                                        class="form-control gender-needs-count"
                                                                        data-name-original="gender_needs[{{ $genderName }}]"
                                                                        name="{{ $isChecked ? "gender_needs[{$genderName}]" : '' }}"
                                                                        value="{{ $needsCount }} Orang / Persons"
                                                                        disabled>
                                                                </div>
                                                                <div class="col-lg-8 ps-2">
                                                                    <div class="input-group">
                                                                        <input type="text"
                                                                            class="form-control gender-age-input"
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
                                                <input type="text" class="form-control"
                                                    value="{{ optional($er)->work_experience }}" disabled>
                                            </div>
                                            @if (!empty($er->duration_work_experience))
                                                <div class="col-lg-6 mb-2" id="duration-experience-field">
                                                    <label class="form-label col-form-label">Pengalaman / Experience (Tahun
                                                        /
                                                        Years)</label>
                                                    <input type="text" class="form-control"
                                                        id="duration_work_experience" name="duration_work_experience"
                                                        value="{{ $er->duration_work_experience }} Tahun / Years"
                                                        disabled>
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
                                                <form id="FormRevise" action="{{ route('recruitment.profile.er.approve.reject', ['token' => $token]) }}"
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
                </div>
            </div>
            <!--end col-->
        </div>
        <!--end row-->
    </div><!-- container-fluid -->
    <!--Modal Sertifikat-->
    <div class="modal fade" id="modalSertifikat" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalgridLabel">Preview certificate</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="show-preview-sertifikat">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Status Modals -->
    <div id="statusModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true"
        style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Ribbon Shape -->
                <div class="card ribbon-box shadow-none mb-lg-0">
                    <div class="card-body">
                        <div id="status_judul"></div>
                        <div class="text-end"><button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"> </button></div>

                        <div class="ribbon-content text-muted mt-4">
                            <div id="status_training"></div>
                        </div>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        <!-- Modal Validation Extension File Upload Gambar -->
        <div class="modal fade" id="secondmodal" aria-hidden="true" aria-labelledby="..." tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body text-center p-5">
                        <lord-icon src="https://cdn.lordicon.com/tdrtiskw.json" trigger="loop"
                            colors="primary:#f7b84b,secondary:#405189" style="width:130px;height:130px">
                        </lord-icon>
                        <div class="mt-4 pt-4">
                            <h4>Whoops, ada yang salah!</h4>
                            <p class="text-muted">Maaf hanya menerima file foto yang bertipe .jpg | .jpeg | .png</p>
                            <!-- Toogle to second dialog -->
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal Upload foto -->
        <div class="modal fade" id="modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
            role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg  modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="myModalLabel">Upload Foto Profile</h5>
                    </div>
                    <div class="modal-body">
                        <div data-simplebar style="max-width: 100%;">
                            <div class="img-container">
                                <div class="row">
                                    <div class="col-md-8">
                                        <img id="image" src="">
                                    </div>
                                    <div class="col-md-4">
                                        <div class="preview"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex gap-2 justify-content-end">
                            <button type="button" onclick="cancelAvatar()" class="btn btn-secondary"
                                data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" id="crop">Crop</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--modal konfirmasi upload foto -->
        <div class="modal fade" id="konfirmasimodal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
            role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body text-center p-5">
                        <form class="form" action="{{ route('profile.upload') }}" method="post"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="mt-4 pt-3">
                                <p class="text-muted mb-4">Apakah Anda Yakin Mengubah Foto Profile Anda?</p>
                                <img src="" style="width: 100px;" class="show-image mb-4">
                                <input type="hidden" name="image_base64">
                                <div class="hstack gap-2 justify-content-center">
                                    <button type="submit" class="btn btn-primary">Ya</button>
                                    <button type="button" onclick="cancelAvatar()" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Tidak</button>
                                    <!-- <button class="btn btn-secondary" data-bs-dismiss="modal">
                                                                                                                Tidak
                                                                                                            </button> -->
                                </div>
                            </div>
                        </form>
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
