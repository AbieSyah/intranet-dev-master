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
      <h4 class="mb-sm-0">Form Appraisal</h4>

      <div class="page-title-right">
          <ol class="breadcrumb m-0">
              <li class="breadcrumb-item"><a href="javascript: void(0);">Appraisal</a></li>
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
        <h3 class="card-title">Appraisal {{ $appraisal->position->nama ?? '' }}</h3>  
        <div class="flex-shrink-0">
            <a href="{{ route('appraisal.index') }}" class="btn btn-primary btn-label waves-effect waves-light"><i class="ri-arrow-left-fill label-icon align-middle fs-16 me-2"></i> Back</a>
        </div>
      </div><!-- end card header -->
      <div class="card-body">
        <form class="form" action="{{ route('appraisal.store') }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('POST')
            <div class="row gy-3">
              <input type="hidden" name="id" id="id" value="{{ $appraisal->id ?? '' }}">
              <div class="col-lg-4 col-sm-6 p-2">
                <label class="required fw-semibold fs-6 mb-2">Position</label>
                <select class="form-select select2" data-placeholder="Select an option" name="position_id"
                  id="position_id">
                  <option></option>
                  @foreach ($positions as $position)
                    <option value="{{ $position->id }}"
                      {{ old('position_id', $appraisal->position_id ?? '') == $position->id ? 'selected' : '' }}>
                      {{ $position->nama }}
                    </option>
                  @endforeach
                </select>
              </div>

              <div class="col-lg-4 col-sm-6 p-2">
                <label class="required fw-semibold fs-6 mb-2">Status</label>
                <select required class="form-select select2" data-placeholder="Select an option"
                  name="status" id="status">
                  <option></option>
                  <option {{ old('status', $appraisal->status ?? '') == 'Permanent' ? 'selected' : '' }}>Permanent</option>
                  <option {{ old('status', $appraisal->status ?? '') == 'Probation' ? 'selected' : '' }}>Probation</option>
                  <option {{ old('status', $appraisal->status ?? '') == 'Contract' ? 'selected' : '' }}>Contract</option>
                </select>
              </div>

              <div class="col-lg-4 col-sm-6 p-2">
                <label class="required fw-semibold fs-6 mb-2">Department</label>
                <select class="form-select select2" data-placeholder="Select an option" name="department_id"
                  id="deparment_id" required>
                  <option></option>
                  @foreach ($departments as $department)
                    <option value="{{ $department->id }}"
                      {{ old('department_id', $appraisal->department_id ?? '') == $department->id ? 'selected' : '' }}>
                      {{ $department->name }}
                    </option>
                  @endforeach
                </select>
              </div>

              <div class="col-lg-4 col-sm-6 p-2">
                <label class="required fw-semibold fs-6 mb-2">Section</label>
                <select class="form-select select2" data-placeholder="Select an option" name="section_id"
                  id="section_id">
                  <option></option>
                  @foreach ($sections as $section)
                    <option value="{{ $section->id }}"
                      {{ old('section_id', $appraisal->section_id ?? '') == $section->id ? 'selected' : '' }}>
                      {{ $section->nama }}
                    </option>
                  @endforeach
                </select>
              </div>
              
              <div class="col-lg-4 col-sm-6 p-2">
                <label class="required fw-semibold fs-6 mb-2">Form Type</label>
                <div class="form-check mb-2">
                  <input class="form-check-input" type="radio" name="form_type" value="A" id="radio-1" {{ old('form_type', $appraisal->form_type ?? '') == 'A' ? 'checked' : '' }} required>
                  <label class="form-check-label" for="radio-1">
                    Type A
                  </label>
                </div>
                <div class="col-lg-4 form-check mb-2">
                  <input class="form-check-input" type="radio" name="form_type" value="B" id="radio-2" {{ old('form_type', $appraisal->form_type ?? '') == 'B' ? 'checked' : '' }}>
                  <label class="form-check-label" for="radio-2">
                    Type B
                  </label>
                </div>
              </div>

              <div class="col-lg-4 col-sm-6 p-2">
                <label class="fw-semibold fs-6 mb-2">Key Performance Indicator (KPI)</label>
                <input required type="number" max="100" min="0" name="kpi_weight" class="auto-sum-2 form-control form-control-solid mb-3 mb-lg-0" placeholder="0 - 100"
                  value="{{ old('kpi_weight', $appraisal->kpi_weight ?? 0) }}" />
              </div>

              <div class="col-12">
                <hr>
                <h5 class="text-center">Attitude & Performance</h5>
              </div>

              <div class="col-sm-6 p-2">
                <label class="fw-semibold fs-6 mb-2">Weight</label>
                <input required type="number" max="100" min="1" name="ap_weight" class="auto-sum-2 form-control form-control-solid mb-3 mb-lg-0" placeholder="0 - 100"
                  value="{{ old('ap_weight', $appraisal->ap_weight ?? 0) }}" />
              </div>

              <div class="col-sm-6 p-2">
                <label class="fw-semibold fs-6 mb-2">Total</label>
                <input required readonly type="number" max="100" min="1" name="ap_total" class="form-control form-control-solid mb-3 mb-lg-0" style="Background-color: #eff2f7;"
                  value="{{ old('ap_total', $appraisal->ap_total ?? 0) }}" />
              </div>

              <div class="col-lg-4 col-sm-6 p-2">
                <label class="fw-semibold fs-6 mb-2">Managerial</label>
                <input required type="number" max="100" min="0" name="ap_managerial" class="ap-input form-control form-control-solid mb-3 mb-lg-0" placeholder="0 - 100"
                  value="{{ old('ap_managerial', $appraisal->ap_managerial ?? 0) }}" />
              </div>

              <div class="col-lg-4 col-sm-6 p-2">
                <label class="fw-semibold fs-6 mb-2">Ability Response ("HORENSO")</label>
                <input required type="number" max="100" min="0" name="ap_ability_response" class="ap-input form-control form-control-solid mb-3 mb-lg-0" placeholder="0 - 100"
                  value="{{ old('ap_ability_response', $appraisal->ap_ability_response ?? 0) }}" />
              </div>

              <div class="col-lg-4 col-sm-6 p-2">
                <label class="fw-semibold fs-6 mb-2">Leadership</label>
                <input required type="number" max="100" min="0" name="ap_leadership" class="ap-input form-control form-control-solid mb-3 mb-lg-0" placeholder="0 - 100"
                  value="{{ old('ap_leadership', $appraisal->ap_leadership ?? 0) }}" />
              </div>

              <div class="col-lg-4 col-sm-6 p-2">
                <label class="fw-semibold fs-6 mb-2">Accuracy</label>
                <input required type="number" max="100" min="0" name="ap_accuracy" class="ap-input form-control form-control-solid mb-3 mb-lg-0" placeholder="0 - 100"
                  value="{{ old('ap_accuracy', $appraisal->ap_accuracy ?? 0) }}" />
              </div>

              <div class="col-lg-4 col-sm-6 p-2">
                <label class="fw-semibold fs-6 mb-2">Capability</label>
                <input required type="number" max="100" min="0" name="ap_capability" class="ap-input form-control form-control-solid mb-3 mb-lg-0" placeholder="0 - 100"
                  value="{{ old('ap_capability', $appraisal->ap_capability ?? 0) }}" />
              </div>

              <div class="col-lg-4 col-sm-6 p-2">
                <label class="fw-semibold fs-6 mb-2">Initiative</label>
                <input required type="number" max="100" min="0" name="ap_initiative" class="ap-input form-control form-control-solid mb-3 mb-lg-0" placeholder="0 - 100"
                  value="{{ old('ap_initiative', $appraisal->ap_initiative ?? 0) }}" />
              </div>

              <div class="col-lg-4 col-sm-6 p-2">
                <label class="fw-semibold fs-6 mb-2">Kaizen</label>
                <input required type="number" max="100" min="0" name="ap_kaizen" class="ap-input form-control form-control-solid mb-3 mb-lg-0" placeholder="0 - 100"
                  value="{{ old('ap_kaizen', $appraisal->ap_kaizen ?? 0) }}" />
              </div>

              <div class="col-lg-4 col-sm-6 p-2">
                <label class="fw-semibold fs-6 mb-2">Responsibility</label>
                <input required type="number" max="100" min="0" name="ap_responsibility" class="ap-input form-control form-control-solid mb-3 mb-lg-0" placeholder="0 - 100"
                  value="{{ old('ap_responsibility', $appraisal->ap_responsibility ?? 0) }}" />
              </div>

              <div class="col-lg-4 col-sm-6 p-2">
                <label class="fw-semibold fs-6 mb-2">Discipline</label>
                <input required type="number" max="100" min="0" name="ap_discipline" class="ap-input form-control form-control-solid mb-3 mb-lg-0" placeholder="0 - 100"
                  value="{{ old('ap_discipline', $appraisal->ap_discipline ?? 0) }}" />
              </div>

              <div class="col-lg-4 col-sm-6 p-2">
                <label class="fw-semibold fs-6 mb-2">Cooperation</label>
                <input required type="number" max="100" min="0" name="ap_cooperation" class="ap-input form-control form-control-solid mb-3 mb-lg-0" placeholder="0 - 100"
                  value="{{ old('ap_cooperation', $appraisal->ap_cooperation ?? 0) }}" />
              </div>

              <div class="col-lg-12">
                <hr>
              </div>

              <div class="col-sm-6 p-2">
                <label class="fw-semibold fs-6 mb-2">Attendance</label>
                <input required type="number" max="100" min="1" name="attendance" class="auto-sum-2 form-control form-control-solid mb-3 mb-lg-0" placeholder="0 - 100"
                  value="{{ old('attendance', $appraisal->attendance ?? 0) }}" />
              </div>

              <div class="col-sm-6 p-2">
                <label class="fw-semibold fs-6 mb-2">Total</label>
                <input required readonly type="number" max="100" min="1" name="total" class="form-control form-control-solid mb-3 mb-lg-0" style="Background-color: #eff2f7;"
                  value="{{ old('total', $appraisal->total ?? 0) }}" />
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
  <script type="text/javascript">
    $(function () {
      $('.select2').select2()
    });
    $(document).ready(function() {
      $("form").submit(function(e) {
        e.preventDefault();
        const total = parseFloat($('input[name="total"]').val()) || 0;
        const apTotal = parseFloat($('input[name="ap_total"]').val()) || 0;
        if (apTotal <= 0 || apTotal > 100) {
          Swal.fire({
            icon: 'warning',
            title: 'Invalid Total',
            text: 'Attitude & Performance Total is Invalid',
          });
          return;
        }
        if (total < 100 || total > 100) {
          Swal.fire({
            icon: 'warning',
            title: 'Invalid Total',
            text: 'Total must be 100',
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
            swalert.then(() => window.location.reload() = response.redirect)
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
          confirmButtonText: 'Ok',
          customClass: {
            confirmButton: 'btn btn-primary'
          }
        });
      }

    });

    document.addEventListener('DOMContentLoaded', function () {
      const inputs = document.querySelectorAll('.ap-input');
      const totalField = document.querySelector('input[name="ap_total"]');
      function calculateTotal() {
        let total = 0;
        inputs.forEach(input => {
          const value = parseFloat(input.value);
          if (!isNaN(value)) {
            total += value;
          }
        });
        totalField.value = total;
      }
      inputs.forEach(input => {
        input.addEventListener('input', calculateTotal);
      });
      calculateTotal();
    });

    document.addEventListener('DOMContentLoaded', function () {
      const formTypeInputs = document.querySelectorAll('input[name="form_type"]');
      const inputs = {
        kpi: document.querySelector('input[name="kpi_weight"]'),
        ap: document.querySelector('input[name="ap_weight"]'),
        attendance: document.querySelector('input[name="attendance"]'),
      };
      const totalField = document.querySelector('input[name="total"]');
      function calculateTotal2() {
        const formType = document.querySelector('input[name="form_type"]:checked')?.value || 'A';
        const kpi = parseFloat(inputs.kpi.value) || 0;
        const ap = parseFloat(inputs.ap.value) || 0;
        const attendance = parseFloat(inputs.attendance.value) || 0;
        let total = 0;
        if (formType === 'A') {
          total = kpi + ap + attendance;
        } else if (formType === 'B') {
          total = kpi + (ap * attendance / 100);
        }
        totalField.value = Math.round(total);
      }
      Object.values(inputs).forEach(input => {
        input.addEventListener('input', calculateTotal2);
      });
      formTypeInputs.forEach(radio => {
        radio.addEventListener('change', calculateTotal2);
      });
      calculateTotal2();
    });
  </script>
@endsection
