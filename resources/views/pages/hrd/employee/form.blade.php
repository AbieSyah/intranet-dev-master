@extends('layouts.master')
@section('link')
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css"/>
  <!-- Select2-->
  <link rel="stylesheet" href="{{  url('') }}/assets/libs/adminlte/select2/css/4.1.0/select2.min.css">
  <style type="text/css">
    body{
        background: #f7fbf8; 
    }
    img {
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
    .section{
        margin-top:150px;
        background:#fff;
        padding:50px 30px;
    }
    .modal-lg{
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
</style>
@endsection

@section('content')
<div class="row">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <h4 class="mb-sm-0">Form Employee</h4>

      <div class="page-title-right">
          <ol class="breadcrumb m-0">
              <li class="breadcrumb-item"><a href="javascript: void(0);">Employee</a></li>
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
        <h3 class="card-title">Employee {{ $employee->name ?? '' }}</h3>  
        <div class="flex-shrink-0">
            <a href="{{ route('employee.index') }}" class="btn btn-primary btn-label waves-effect waves-light"><i class="ri-arrow-left-fill label-icon align-middle fs-16 me-2"></i> Back</a>
        </div>
      </div>
      <div class="card-body">
        <form class="form" action="{{ route('employee.store') }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('POST')
            <h3 class="fw-semibold fs-4 text-center mt-3 mb-4">Personal Information</h3>
            <div class="row gy-3">
              <input type="hidden" name="id" id="id" value="{{ $employee->id ?? '' }}">
              <div class="col-lg-4 col-sm-6 p-2">
                <div class="text-center">
                  <label class="required fw-semibold fs-6 mb-2">Foto Profile</label>
                </div>
                <div class="text-center">
                    <input type="hidden" name="image_base64">
                  <div class="profile-user position-relative d-inline-block mx-auto">
                      @if(!empty($employee->avatar))
                      <div id="avatar-user">
                        <img src="{{ asset('storage/avatars/'.$employee->avatar) }}" class="show-image rounded-circle avatar-xl img-thumbnail user-profile-image" alt="user-profile-image">
                      </div>
                      @else
                      <div id="avatar-user">
                        <img src="{{ asset('storage/avatars/user.jpg') }}" class="rounded-circle avatar-xl img-thumbnail user-profile-image" alt="user-profile-image">
                      </div>
                      @endif
                      <div class="avatar-xs p-0 rounded-circle profile-photo-edit">
                          <input onchange="avatarValidation(this);" id="profile-img-file-input" type="file" name="image" class="image profile-img-file-input" accept="image/jpg,image/jpeg,image/png,image/JPG,image/JPEG,image/PNG">
                          <label for="profile-img-file-input" class="profile-photo-edit avatar-xs">
                              <span class="avatar-title rounded-circle bg-light text-body">
                                  <i class="ri-camera-fill"></i>
                              </span>
                          </label>
                      </div>
                  </div>
                </div>
                <div class="text-center mt-1">
                  <button type="button" onclick="clearAvatar()" class="btn rounded-pill btn-light btn-sm waves-effect"><i class="ri-delete-bin-2-line align-middle me-1"></i> Remove</button>
                  <input type="hidden" name="remove_file" id="remove_file" value="">
                </div>
              </div>
              <div class="col-lg-4 col-sm-6 p-2">
                <label class="fw-semibold fs-6 mb-2">NIK (Nomor Induk Karyawan)</label>
                <input required type="text" name="nik" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="NIK according"
                  value="{{ old('nik', $employee->nik ?? '') }}" />
                  <!-- <small class="text-info">NIK according to ID card</small> -->
              </div>
              <div class="col-lg-4 col-sm-6 p-2">
                <label class="fw-semibold fs-6 mb-2">KTP or Passpord ID</label>
                <input type="text" name="no_ktp" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="KTP according to ID card"
                  value="{{ old('no_ktp', $employee->no_ktp ?? '') }}" />
                  <!-- <small class="text-info">NIK according to ID card</small> -->
              </div>

              <div class="col-lg-4 col-sm-6 p-2">
                <label class="required fw-semibold fs-6 mb-2">Full Name</label>
                <input required type="text" name="fullname" id="fullname" placeholder="Full Name according to ID card" class="form-control form-control-solid mb-3 mb-lg-0"
                  style="text-transform: uppercase" value="{{ old('fullname', $employee->fullname ?? '') }}" />
                <!-- <small class="text-info">Full Name according to ID card</small> -->
              </div>              

              <div class="col-lg-8 col-sm-12 p-2">
                <label class="required fw-semibold fs-6 mb-2">KTP Address</label>
                <input required type="text" name="addressktp" class="form-control form-control-solid mb-3 mb-lg-0"
                  placeholder="Address according to ID card" value="{{ old('addressktp', $employee->addressktp ?? '') }}" />
              </div>

              <div class="col-lg-4 col-sm-6 p-2">
                <label class="required fw-semibold fs-6 mb-2">Birthplace</label>
                <input required type="text" name="birthplace" class="form-control form-control-solid mb-3 mb-lg-0"
                {{-- <input type="text" name="birthplace" class="form-control form-control-solid mb-3 mb-lg-0"   --}}
                value="{{ old('birthplace', $employee->birthplace ?? '') }}" placeholder="Birthplace according to ID card" />
              </div>

              <div class="col-lg-4 col-sm-6 p-2">
                <label class="required fw-semibold fs-6 mb-2">Birth Date</label>
                <input required type="date" name="birthdate" id="birthdate" class="form-control form-control-solid mb-3 mb-lg-0 @error('birthdate') is-invalid @enderror"
                {{-- <input type="date" name="birthdate" id="birthdate" class="form-control form-control-solid mb-3 mb-lg-0 @error('birthdate') is-invalid @enderror"   --}}
                placeholder="Select Date" value="{{ old('birthdate', $employee->birthdate ?? '') }}" />
              </div>

              <div class="col-lg-4 col-sm-6 p-2">
                <label class="required fw-semibold fs-6 mb-2">Gender</label>
                <div class="form-check mb-2">
                  <input class="form-check-input" type="radio" name="gender" value="Male" id="radio-1" {{ old('gender', $employee->gender ?? '') == 'Male' ? 'checked' : '' }}>
                  <label class="form-check-label" for="radio-1">
                    Male
                  </label>
                </div>
                <div class="form-check mb-2">
                  <input class="form-check-input" type="radio" name="gender" value="Female" id="radio-2" {{ old('gender', $employee->gender ?? '') == 'Female' ? 'checked' : '' }}>
                  <label class="form-check-label" for="radio-2">
                    Female
                  </label>
                </div>
              </div>

              <div class="col-lg-4 col-sm-6 p-2">
                  <label class="required fw-semibold fs-6 mb-2">Religion</label>
                  <select required class="form-select select2" data-placeholder="Select Religion" name="religion" id="religion">
                      <option></option>
                      @foreach(\App\Models\Employee::RELIGIONS as $religion)
                          <option value="{{ $religion }}" {{ old('religion', $employee->religion ?? '') == $religion ? 'selected' : '' }}>
                              {{ $religion }}
                          </option>
                      @endforeach
                  </select>
              </div>

              <div class="col-lg-4 col-sm-6 p-2">
                  <label class="required fw-semibold fs-6 mb-2">Marital status</label>
                  <select required class="form-select select2" data-placeholder="Select Marital Status" name="marital" id="marital">
                      <option></option>
                      @foreach(\App\Models\Employee::MARITAL_STATUSES as $value => $data)
                          <option value="{{ $value }}" 
                                  title="{{ $data['title'] }}" 
                                  {{ old('marital', $employee->marital ?? '') == $value ? 'selected' : '' }}>
                              {{ $data['label'] }}
                          </option>
                      @endforeach
                  </select>
              </div>

              <div class="col-lg-4 col-sm-6 p-2">
                <label class="required fw-semibold fs-6 mb-2">Phone</label>
                <input required type="text" name="hp" class="form-control form-control-solid mb-3 mb-lg-0"
                  placeholder="031XXXXXXX" value="{{ old('hp', $employee->hp ?? '') }}" />
              </div>

              <div class="col-lg-4 col-sm-6 p-2">
                <label class="required fw-semibold fs-6 mb-2">Email</label>
                <input type="email" name="email" class="form-control form-control-solid mb-3 mb-lg-0"
                  value="{{ old('email', $employee->email ?? '') }}" placeholder="Your Email address"/>
              </div>

              <div class="col-lg-8 col-sm-12 p-2">
                <label class="required fw-semibold fs-6 mb-2">Domicile Address</label>
                <input required type="text" name="domicile_address" class="form-control form-control-solid mb-3 mb-lg-0"
                  placeholder="Employee Domicile" value="{{ old('domicile_address', $employee->domicile_address ?? '') }}" />
              </div>

              <div class="col-lg-4 col-sm-6 p-2">
                  <label class="fw-semibold fs-6 mb-2">Blood Type</label>
                  <select name="blood_type" class="form-select select2" data-placeholder="Select Blood Type" data-allow-clear="true">
                      <option></option>
                      @foreach(\App\Models\Employee::BLOOD_TYPES as $type)
                          <option value="{{ $type }}" {{ old('blood_type', $employee->blood_type ?? '') == $type ? 'selected' : '' }}>
                              {{ $type }}
                          </option>
                      @endforeach
                  </select>
              </div>

              @if(isset($employee) && $employee->id)
                  <div class="col-lg-4 col-sm-6 p-2">
                      <label class="fw-semibold fs-6 mb-2">Age</label>
                      <input type="text" class="form-control form-control-solid" 
                          value="{{ $employee->age }}" disabled />
                  </div>
                  <div class="col-lg-4 col-sm-6 p-2">
                      <label class="fw-semibold fs-6 mb-2">Service Years</label>
                      <input type="text" class="form-control form-control-solid" 
                          value="{{ $employee->service_years }}" disabled />
                  </div>
              @endif

              <div class="col-lg-12"> 
                <hr>
                <h3 class="fw-semibold fs-4 text-center">Emergency Contact</h3>
              </div>

              <div class="col-lg-12"> 
                <div class="row">
                  <div class="col-lg-4 col-sm-6 p-2">
                    <label class="fw-semibold fs-6 mb-2">Name</label>
                    <input type="text" name="emergency_contact"
                      class="form-control form-control-solid mb-3 mb-lg-0"
                      placeholder="Name"
                      value="{{ old('emergency_contact', $employee->emergency_contact ?? '') }}" />
                  </div>

                  <div class="col-lg-4 col-sm-6 p-2">
                    <label class="fw-semibold fs-6 mb-2">Relation</label>
                    <input type="text" name="emergency_contact_relation"
                      class="form-control form-control-solid mb-3 mb-lg-0"
                      placeholder="Father/Mother/Sibling"
                      value="{{ old('emergency_contact_relation', $employee->emergency_contact_relation ?? '') }}" />
                  </div>

                  <div class="col-lg-4 col-sm-6 p-2">
                    <label class="fw-semibold fs-6 mb-2">Handphone</label>
                    <input type="text" name="emergency_contact_handphone"
                      class="form-control form-control-solid mb-3 mb-lg-0"
                      placeholder="08XXXXXXXXXX"
                      value="{{ old('emergency_contact_handphone', $employee->emergency_contact_handphone ?? '') }}" />
                  </div>

                  <div class="col-lg-4 col-sm-6 p-2">
                    <label class="fw-semibold fs-6 mb-2">Address</label>
                    <input type="text" name="emergency_contact_address"
                      class="form-control form-control-solid mb-3 mb-lg-0"
                      placeholder="Address"
                      value="{{ old('emergency_contact_address', $employee->emergency_contact_address ?? '') }}" />
                  </div>

                </div>
              </div>

              <div class="col-lg-12">
                <hr>
                <h3 class="fw-semibold fs-4 text-center">Employment Detail</h3>
              </div>

              <div class="col-lg-4 col-sm-6 p-2">
                <label class="required fw-semibold fs-6 mb-2">Join Date</label>
                <input required type="date" name="joindate" class="form-control datepicker form-control-solid mb-3 mb-lg-0"
                  value="{{ old('joindate', $employee->joindate ?? '') }}" placeholder="Select Date" />

                {{-- Contract Start Date Input --}}
                <div id="contract-start-date-field" class="d-none">
                  <label class="fw-semibold fs-6 mt-3" id="contract-start-date-label">
                    {{ (old('status', $employee->status ?? '') == 'PROBATION') ? 'Probation Start Date' : 'Contract Start Date' }}
                  </label>
                  <input type="date" name="contract_startdate" class="form-control datepicker form-control-solid mb-3 mb-lg-0"
                    value="{{ old('contract_startdate', $employee->contract_startdate ?? '') }}" placeholder="Select Date" />
                </div>
                {{-- End Contract Start Date Input --}}

                {{-- Permanent Start Date Input --}}
                <div id="permanent-start-date-field" class="d-none">
                  <label class="fw-semibold fs-6 mt-3" id="permanent-start-date-label">
                    Permanent Start Date
                  </label>
                  <input type="date" name="permanent_startdate" class="form-control datepicker form-control-solid mb-3 mb-lg-0"
                    value="{{ old('permanent_startdate', $employee->permanent_startdate ?? '') }}" placeholder="Select Date" />
                </div>
                {{-- End Permanent Start Date Input --}}

                {{-- Outsourcing Vendor Field --}}
                <div id="outsourcing-vendor-field" class="d-none">
                  <label class="fw-semibold fs-6 mt-3">Outsourcing Vendor</label>
                  <input type="text" name="outsourcing_vendor" class="form-control form-control-solid mb-3 mb-lg-0"
                    value="{{ old('outsourcing_vendor', $employee->outsourcing_vendor ?? '') }}" placeholder="Vendor Name" />
                </div>
                {{-- End Outsourcing Vendor Field --}}
              </div>

              <div class="col-lg-4 col-sm-6 p-2">
                <label class=" fw-semibold fs-6 mb-2">End Date</label>
                <input type="date" name="enddate" class="form-control datepicker form-control-solid mb-3 mb-lg-0"
                  value="{{ old('enddate', $employee->enddate ?? '') }}" placeholder="Select Date" />
                <div id="contract-number-field" class="d-none">
                    <label class="fw-semibold fs-6 mt-3">Contract Sequence</label>
                    <select class="form-select select2" data-placeholder="Select Contract Sequence"
                        name="contract_number" id="contract_number">
                        <option value=""></option>
                        @foreach ($contracts as $contract)
                            <option value="{{ $contract->id }}"
                                {{ old('contract_number', $employee->contract_number ?? '') == $contract->id ? 'selected' : '' }}>
                                {{ $contract->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
              </div>

              <div class="col-lg-4 col-sm-6 p-2">
                <label class="required fw-semibold fs-6 mb-2">Employment Status</label>
                <div class="form-check mb-2">
                  <input class="form-check-input" type="radio" name="status" value="PERMANENT" id="radio-3"
                    {{ old('status', $employee->status ?? '') == 'PERMANENT' ? 'checked' : 'checked' }}>
                  <label class="form-check-label" for="radio-3">
                    Permanent
                  </label>
                </div>
                <div class="form-check mb-2">
                  <input class="form-check-input" type="radio" name="status" value="CONTRACT" id="radio-6"
                    {{ old('status', $employee->status ?? '') == 'CONTRACT' ? 'checked' : '' }}>
                  <label class="form-check-label" for="radio-6">
                    Contract
                  </label>
                </div>
                <div class="form-check mb-2">
                  <input class="form-check-input" type="radio" name="status" value="PROBATION" id="radio-6"
                    {{ old('status', $employee->status ?? '') == 'PROBATION' ? 'checked' : '' }}>
                  <label class="form-check-label" for="radio-6">
                    Probation
                  </label>
                </div>
                <div class="form-check mb-2">
                  <input class="form-check-input" type="radio" name="status" value="OUTSOURCING" id="radio-6"
                    {{ old('status', $employee->status ?? '') == 'OUTSOURCING' ? 'checked' : '' }}>
                  <label class="form-check-label" for="radio-6">
                    Outsourcing
                  </label>
                </div>
                <div class="form-check mb-2">
                  <input class="form-check-input" type="radio" name="status" value="TERMINATED" id="radio-6"
                    {{ old('status', $employee->status ?? '') == 'TERMINATED' ? 'checked' : '' }}>
                  <label class="form-check-label" for="radio-6">
                    Terminated
                  </label>
                </div>
                <div id="reason-field" class="d-none">
                  <input type="text" name="reason" class="form-control form-control-solid mb-3 mb-lg-0"
                      value="{{ old('reason', $employee->reason ?? '') }}" placeholder="Reason for Terminated" />
                </div>
              </div>

              <div class="col-lg-4 col-sm-6 p-2">
                <label class="required fw-semibold fs-6 mb-2">Area</label>
                <select class="form-select select2" data-placeholder="Select an option" name="area_id"
                  id="area_id" required>
                  <option></option>
                  @foreach ($areas as $area)
                    <option value="{{ $area->id }}"
                      {{ old('area_id', $employee->area_id ?? '') == $area->id ? 'selected' : '' }}>{{ $area->name }}
                    </option>
                  @endforeach
                </select>
              </div>

              <div class="col-lg-4 col-sm-6 p-2">
                <label class="required fw-semibold fs-6 mb-2">Department</label>
                <select class="form-select select2" data-placeholder="Select an option" name="department_id"
                  id="deparment_id" required>
                  <option></option>
                  @foreach ($departments as $department)
                    <option value="{{ $department->id }}"
                      {{ old('department_id', $employee->department_id ?? '') == $department->id ? 'selected' : '' }}>
                      {{ $department->name }}
                    </option>
                  @endforeach
                </select>
              </div>

              <div class="col-lg-4 col-sm-6 p-2">
                <label class="fw-semibold fs-6 mb-2">Work Location</label>
                <select class="form-select select2" data-placeholder="Select an option" name="work_location"
                  id="work_location" data-allow-clear="true">
                  <option></option>
                  @foreach ($city as $location)
                    <option value="{{ $location['nama'] }}"
                      {{ old('work_location', $employee->work_location ?? '') == $location['nama'] ? 'selected' : '' }}>
                      {{ $location['nama'] }}
                    </option>
                  @endforeach
                </select>
              </div>
              <div class="col-lg-4 col-sm-6 p-2">
                <label class="required fw-semibold fs-6 mb-2">Section</label>
                <select class="form-select select2" data-placeholder="Select an option" name="section_id"
                  id="section_id" data-allow-clear="true">
                  <option></option>
                  @foreach ($sections as $section)
                    <option value="{{ $section->id }}"
                      {{ old('section_id', $employee->section_id ?? '') == $section->id ? 'selected' : '' }}>
                      {{ $section->nama }}
                    </option>
                  @endforeach
                </select>
              </div>
              <div class="col-lg-4 col-sm-6 p-2">
                <label class="required fw-semibold fs-6 mb-2">Position</label>
                <select class="form-select select2" data-placeholder="Select an option" name="position_id"
                  id="position_id" data-allow-clear="true">
                  <option></option>
                  @foreach ($positions as $position)
                    <option value="{{ $position->id }}"
                      {{ old('position_id', $employee->position_id ?? '') == $position->id ? 'selected' : '' }}>
                      {{ $position->nama }}
                    </option>
                  @endforeach
                </select>
              </div>
              <div class="col-lg-4 col-sm-6 p-2">
                <label class="required fw-semibold fs-6 mb-2">Level</label>
                <select class="form-select select2" data-placeholder="Select an option" name="level_id"
                  id="level_id" data-allow-clear="true">
                  <option></option>
                  @foreach ($levels as $level)
                    <option value="{{ $level->id }}"
                      {{ old('level_id', $employee->level_id ?? '') == $level->id ? 'selected' : '' }}>
                      {{ $level->nama }}
                    </option>
                  @endforeach
                </select>
              </div>
              <div class="col-lg-4 col-sm-6 p-2">
                <label class="required fw-semibold fs-6 mb-2">Organization</label>
                <select class="form-select select2" data-placeholder="Select an option" name="building_id"
                  id="building_id" data-allow-clear="true">
                  <option></option>
                  @foreach ($buildings as $building)
                    <option value="{{ $building->id }}"
                      {{ old('building_id', $employee->building_id ?? '') == $building->id ? 'selected' : '' }}>
                      {{ $building->nama }}
                    </option>
                  @endforeach
                </select>
              </div>

              <div class="col-lg-4 col-sm-6 p-2">
                <label class="fw-semibold fs-6 mb-2">ISO Position</label>
                <input type="text" name="iso_position"
                  class="form-control form-control-solid mb-3 mb-lg-0"
                  placeholder="HRD/MANAGER/ETC...."
                  value="{{ old('iso_position', $employee->iso_position ?? '') }}"/>
              </div>

              <div class="col-lg-4 col-sm-6 p-2">
                <label class="fw-semibold fs-6 mb-2">Cost Center</label>
                <input type="number" name="cost_center"
                  class="form-control form-control-solid mb-3 mb-lg-0"
                  placeholder="XXXXX"
                  value="{{ old('cost_center', $employee->cost_center ?? '') }}"/>
              </div>

              <div class="col-sm-6 p-2">
                <label class="fw-semibold fs-6 mb-2">Latest Agreement Number</label>
                <input type="text" 
                  name="latest_agreement_number"
                  id="latest_agreement_number"
                  class="form-control form-control-solid mb-3 mb-lg-0"
                  placeholder="XXXXXXXXXXXXXXXX"
                  value="{{ old('latest_agreement_number', $employee->latest_agreement_number ?? '') }}" />
              </div>

              <div class="col-sm-6 p-2">
                <label class="fw-semibold fs-6 mb-2">Active Agreement Number</label>
                <input type="text" 
                  name="active_agreement_number"
                  id="active_agreement_number"
                  class="form-control form-control-solid mb-3 mb-lg-0"
                  placeholder="XXXXXXXXXXXXXXXX"
                  value="{{ old('active_agreement_number', $employee->active_agreement_number ?? '') }}" />
              </div>

              <div class="col-lg-12">
                <hr>
                <h3 class="fw-semibold fs-4 text-center">Payroll Information</h3>
              </div>

              <div class="col-sm-6 p-2">
                <label class="fw-semibold fs-6 mb-2">PTKP Status</label>
                <select name="tax_dependents" class="form-select select2" 
                  data-placeholder="Select PTKP Status" data-allow-clear="true">
                  <option value="" selected disabled>Select PTKP Status</option>
                  @php
                      $taxOptions = \App\Models\Employee::getTaxDependentsOptions();
                  @endphp
                  @foreach ($taxOptions as $tax)
                      <option value="{{ $tax }}" {{ old('tax_dependents', $employee->tax_dependents ?? '') === $tax ? 'selected' : '' }}>
                          {{ $tax }}
                      </option>
                  @endforeach
                </select>
              </div>

              <div class="col-sm-6 p-2">
                <label class="fw-semibold fs-6 mb-2">No NPWP</label>
                <input type="text" 
                  name="npwp"
                  id="npwp_input"
                  class="form-control form-control-solid mb-3 mb-lg-0"
                  placeholder="XXXXXXXXXXXXXXXX"
                  value="{{ old('npwp', $employee->npwp ?? '') }}"
                  maxlength="16" />
              </div>

              <div class="col-sm-6 p-2">
                <label class="fw-semibold fs-6 mb-2">No BPJS Kesehatan</label>
                <input type="text" 
                  name="bpjs_kesehatan"
                  id="bpjs_kesehatan"
                  class="form-control form-control-solid mb-3 mb-lg-0"
                  placeholder="XXXXXXXXXXXXXXXX"
                  value="{{ old('bpjs_kesehatan', $employee->bpjs_kesehatan ?? '') }}"
                  maxlength="16" />
              </div>

              <div class="col-sm-6 p-2">
                <label class="fw-semibold fs-6 mb-2">No BPJS Ketenagakerjaan</label>
                <input type="text" 
                  name="bpjs_ketenagakerjaan"
                  id="bpjs_ketenagakerjaan"
                  class="form-control form-control-solid mb-3 mb-lg-0"
                  placeholder="XXXXXXXXXXXXXXXX"
                  value="{{ old('bpjs_ketenagakerjaan', $employee->bpjs_ketenagakerjaan ?? '') }}"
                  maxlength="16" />
              </div>

              <div class="col-lg-4 col-sm-6 p-2">
                <label class="fw-semibold fs-6 mb-2">Bank Name</label>
                <input type="text" 
                  name="bank_name"
                  id="bank_name"
                  class="form-control form-control-solid mb-3 mb-lg-0"
                  placeholder="Input Bank Name"
                  value="{{ old('bank_name', $employee->bank_name ?? '') }}" />
              </div>

              <div class="col-lg-4 col-sm-6 p-2">
                <label class="fw-semibold fs-6 mb-2">Bank Account</label>
                <input type="text" 
                  name="bank_account"
                  id="bank_account"
                  class="form-control form-control-solid mb-3 mb-lg-0"
                  placeholder="XXXXXXXXXXXXXXXX"
                  value="{{ old('bank_account', $employee->bank_account ?? '') }}" />
              </div>

              <div class="col-lg-4 col-sm-6 p-2">
                <label class="fw-semibold fs-6 mb-2">Bank Account Holder</label>
                <input type="text" 
                  name="bank_account_holder"
                  id="bank_account_holder"
                  class="form-control form-control-solid mb-3 mb-lg-0"
                  placeholder="Input Bank Account Holder"
                  value="{{ old('bank_account_holder', $employee->bank_account_holder ?? ($employee->fullname ?? '')) }}" />
              </div>

              <div class="col-lg-12">
                <hr>
                <h3 class="fw-semibold fs-4 text-center">Academic Background</h3>
              </div>

              <div class="col-lg-4 col-sm-6 p-2">
                  <label class="fw-semibold fs-6 mb-2">Last Education</label>
                  <select name="last_education" class="form-select select2" 
                      data-placeholder="Select Last Education" data-allow-clear="true">
                      <option></option>
                      @foreach(\App\Models\Employee::LAST_EDUCATIONS as $full => $short)
                          <option value="{{ $full }}"
                              {{ old('last_education', $employee->last_education ?? '') === $full ? 'selected' : '' }}>
                              {{ $full }} ({{ $short }})
                          </option>
                      @endforeach
                  </select>
              </div>
              <div class="col-lg-4 col-sm-6 p-2">
                <label class="fw-semibold fs-6 mb-2">Major</label>
                <input type="text" name="major_last_education"
                  class="form-control form-control-solid mb-3 mb-lg-0"
                  placeholder="Major of Last Education"
                  value="{{ old('major_last_education', $employee->major_last_education ?? '') }}"/>
              </div>
              <div class="col-lg-4 col-sm-6 p-2">
                <label class="fw-semibold fs-6 mb-2">Educational Institution</label>
                <input type="text" name="last_education_institutional"
                  class="form-control form-control-solid mb-3 mb-lg-0"
                  placeholder="Last Educational Institution"
                  value="{{ old('last_education_institutional', $employee->last_education_institutional ?? '') }}"/>
              </div>
              
              <div class="col-lg-12">
                <hr>
              </div>
              
              <div class="col-lg-12">
                  <div class="d-flex justify-content-end">
                      <div class="text-center pt-10">
                          <button type="submit" class="btn btn-primary" data-kt-users-modal-action="submit">
                              <span class="d-none spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
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
  <!--end col-->
</div>
<!--end row-->
<!-- Modal Validation Extension File Upload Gambar -->
<div class="modal fade" id="secondmodal" aria-hidden="true" aria-labelledby="..." tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
          <div class="modal-body text-center p-5">
              <lord-icon
                  src="https://cdn.lordicon.com/tdrtiskw.json"
                  trigger="loop"
                  colors="primary:#f7b84b,secondary:#405189"
                  style="width:130px;height:130px">
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
<div class="modal fade" id="modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="myModalLabel">Upload Foto Profile</h5>
        </div>
        <div class="modal-body">
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
        <div class="modal-footer">
            <button type="button" onclick="cancelAvatar()" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary" id="crop">Crop</button>
        </div>
    </div>
  </div>
</div>
@endsection
@section('script')
  <!-- profile-setting init js -->
  <script src="{{  url('') }}/assets/js/pages/profile-setting.init.js"></script>
  <!-- Select2 -->
  <script src="{{  url('') }}/assets/libs/adminlte/select2/js/4.1.0/select2.min.js"></script>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.js"></script>
@endsection
@section('javascript')
<script>
        var $modal = $('#modal');
        var image = document.getElementById('image');
        var cropper;
  
        /*------------------------------------------
        --------------------------------------------
        Image Change Event
        --------------------------------------------
        --------------------------------------------*/
        $("body").on("change", ".image", function(e){
            var files = e.target.files;
            var done = function (url) {
                image.src = url;
                $modal.modal('show');
            };
  
            var reader;
            var file;
            var url;
  
            if (files && files.length > 0) {
                file = files[0];
  
                if (URL) {
                    done(URL.createObjectURL(file));
                } else if (FileReader) {
                    reader = new FileReader();
                    reader.onload = function (e) {
                        done(reader.result);
                    };
                reader.readAsDataURL(file);
                }
            }
        });
  
        /*------------------------------------------
        --------------------------------------------
        Show Model Event
        --------------------------------------------
        --------------------------------------------*/
        $modal.on('shown.bs.modal', function () {
            cropper = new Cropper(image, {
                aspectRatio: 1,
                viewMode: 3,
                preview: '.preview'
            });
        }).on('hidden.bs.modal', function () {
            cropper.destroy();
            cropper = null;
        });
  
        /*------------------------------------------
        --------------------------------------------
        Crop Button Click Event
        --------------------------------------------
        --------------------------------------------*/
        $("#crop").click(function(){
            canvas = cropper.getCroppedCanvas({
                // width: 160,
                // height: 160,
                width: 200,
                height: 200,
            });
  
            canvas.toBlob(function(blob) {
                url = URL.createObjectURL(blob);
                var reader = new FileReader();
                reader.readAsDataURL(blob);
                reader.onloadend = function() {
                    var base64data = reader.result; 
                    $("input[name='image_base64']").val(base64data);
                    $(".show-image").show();
                    $(".show-image").attr("src",base64data);
                    $("#modal").modal('toggle');
                }
            });
        });
          
    </script>
  <script type="text/javascript">
    $(function () {
      $('.select2').select2()
    });
  </script>
  <script>
    $('#birthdate').flatpickr({
        allowInput: true,
        dateFormat: "Y-m-d",
        maxDate: "today",
    }); 

    function clearAvatar(){
      document.getElementById("avatar-user").innerHTML = '';
      document.getElementById("avatar-user").innerHTML = '<img src="{{  url('') }}/assets/images/users/user-dummy-img.jpg" class="rounded-circle avatar-xl img-thumbnail user-profile-image" alt="user-profile-image">';
      var file_avatar = document.getElementById('profile-img-file-input');
      file_avatar.value = '';

      var remove_avatar = document.getElementById('remove_file');
      remove_avatar.value = '1';
    }

    function cancelAvatar(){
      var avatar = document.getElementById('profile-img-file-input');
      avatar.value = '';
      var pre_avatar = {{ Js::from($employee->avatar ?? '') }};
      if(!pre_avatar){
        document.getElementById("avatar-user").innerHTML = '<img src="{{  url('') }}/assets/images/users/user-dummy-img.jpg" class="rounded-circle avatar-xl img-thumbnail user-profile-image" alt="user-profile-image">';
      }else{
        document.getElementById("avatar-user").innerHTML = '<img src="/storage/avatars/'+pre_avatar+'" class="rounded-circle avatar-xl img-thumbnail user-profile-image" alt="user-profile-image">';
      }      
    }

    function avatarValidation(){
      //cek remove file
      var remove_avatar = document.getElementById('remove_file');
      remove_avatar.value = '';
      //foto profile
      var profile = document.getElementById('profile-img-file-input');             
      var pathProfile = profile.value;

      // tipe file yang diizinkan
      var allowedExtensions =
        /(\.jpg|\.jpeg|\.png|\.JPG|\.JPEG|\.PNG)$/i;
        
      //masalah modal
      if (!allowedExtensions.exec(pathProfile)) {
          $('#secondmodal').modal('show');
          // alert('Invalid file type');
          profile.value = '';
          return false;
      }
    }
  </script>
  <script type="text/javascript">
    $(document).ready(function() {
      let swalert;

      $(".datepicker").flatpickr({
        altInput: true,
        altFormat: "j F, Y",
        dateFormat: "Y-m-d",
      });
      $("#religion").select2({
        tags: true
      })

      $("form").submit(function(e) {
        e.preventDefault();

        swalert = Swal.fire({
          title: 'Loading!',
          didOpen: () => {
            Swal.showLoading()
          }
        });

        const formData = new FormData(this);
        let rawName = formData.get('fullname');
        let cleanName = rawName.replace(/[‘’`´]/g, "'");
        formData.set('fullname', cleanName.toUpperCase());

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
            $("#loadingSpinner").hide();
            console.log({
              xhr,
              status,
              error
            });

            // Handle error response with SweetAlert
            handleErrorResponse(xhr.responseJSON);
          }
        });
      });

      function handleErrorResponse(responseJson) {
        swalert.hideLoading();
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

        // Display error message using SweetAlert
        swalert.update({
          title: 'Error',
          html: errorMessage,
          icon: 'error',
          buttonsStyling: false,
          confirmButtonText: "Ok, got it!",
          customClass: {
            confirmButton: 'btn btn-primary'
          }
        });
      }

      // Trigger Employment Status
      function toggleStatusFields() {
        const selectedStatus = $('input[name="status"]:checked').val();
        const reasonField = $('#reason-field');
        const permanentStartDateField = $('#permanent-start-date-field')
        const contractStartField = $('#contract-start-date-field');
        const contractNumberField = $('#contract-number-field');
        const contractStartLabel = $('#contract-start-date-label');
        const outsourcingVendorField = $('#outsourcing-vendor-field');
        const permanentStartDateInput = permanentStartDateField.find('input[name="permanent_startdate"]')
        const contractStartDateInput = contractStartField.find('input[name="contract_startdate"]');
        const contractNumberSelect = contractNumberField.find('select[name="contract_number"]');
        const outsourcingVendorInput = outsourcingVendorField.find('input[name="outsourcing_vendor"]');
        
        // Reason
        if (selectedStatus === 'TERMINATED') {
            reasonField.removeClass('d-none');
        } else {
            reasonField.addClass('d-none');
            reasonField.find('input[name="reason"]').val('');
        }

        // Contract & Probation
        if (selectedStatus === 'CONTRACT' || selectedStatus === 'PROBATION') {
            contractStartField.removeClass('d-none');
            contractStartDateInput.attr('required', 'required');
            if (selectedStatus === 'PROBATION') {
                contractStartLabel.text('Probation Start Date');
            } else {
                contractStartLabel.text('Contract Start Date');
            }
        } else {
            contractStartField.addClass('d-none');
            contractStartDateInput.removeAttr('required');
            contractStartDateInput.val('');
        }
        if (selectedStatus === 'CONTRACT') {
            contractNumberField.removeClass('d-none');
            contractNumberSelect.attr('required', 'required');
        } else {
            contractNumberField.addClass('d-none');
            contractNumberSelect.removeAttr('required');
            if (selectedStatus !== 'CONTRACT') {
              contractNumberSelect.val('').trigger('change');
            }
        }

        // Permanent
        if (selectedStatus == "PERMANENT") {
          permanentStartDateField.removeClass('d-none')
          permanentStartDateInput.attr('required')
        } else {
          permanentStartDateInput.removeAttr('required')
          permanentStartDateField.addClass('d-none')
        }

        // Outsourcing
        if (selectedStatus === 'OUTSOURCING') {
            outsourcingVendorField.removeClass('d-none');
            outsourcingVendorInput.attr('required', 'required');
        } else {
            outsourcingVendorField.addClass('d-none');
            outsourcingVendorInput.removeAttr('required');
            outsourcingVendorInput.val('');
        }
      }
      toggleStatusFields();
      $('input[name="status"]').on('change', toggleStatusFields);

      // Auto-fill Bank Account Holder with Full Name
      const nameInput = $('#fullname');
      const bankHolderInput = $('#bank_account_holder');
      nameInput.on('input', function() {
          let currentName = $(this).val().toUpperCase();
          let previousName = (nameInput.data('previous-name') || "").toUpperCase();
          let currentBankHolder = bankHolderInput.val().toUpperCase();
          if (currentBankHolder === "" || currentBankHolder === previousName) {
              bankHolderInput.val(currentName);
          }
          nameInput.data('previous-name', currentName);
      });
    });
  </script>
@endsection
