@extends('layouts.master')
@section('link')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css" />
    <!-- Select2-->
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
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Form Job Posting</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Recruitment</a></li>
                        <li class="breadcrumb-item active">Job Posting</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header align-items-center d-flex justify-content-between">
                    <h3 class="card-title">Job Posting {{ $jp->title ?? '' }}</h3>
                    <div class="flex-shrink-0">
                        <a href="{{ route('job-posting.index') }}"
                            class="btn btn-primary btn-label waves-effect waves-light"><i
                                class="ri-arrow-left-fill label-icon align-middle fs-16 me-2"></i> Back</a>
                    </div>
                </div>
                <div class="card-body">
                    <form class="form" id="jpForm" action="{{ route('job-posting.store') }}" method="post"
                        enctype="multipart/form-data">
                        @csrf
                        @method('POST')
                        <div class="row gy-3">
                            <input type="hidden" name="id" id="id" value="{{ $jp->id ?? '' }}">

                            <div class="col-12">
                                <h5 class="text-center">Employee Requisition</h5>
                            </div>

                            <div class="col-lg-4 col-sm-6">
                                <label class="required fw-semibold fs-6 mb-2" for="requisition_id">Requisition</label>
                                <select class="form-select form-control select2" data-placeholder="Select an option"
                                    name="requisition_id" id="requisition_id" required>
                                    <option></option>
                                    @foreach ($er as $e)
                                        <option value="{{ $e->id }}" 
                                            {{ old('requisition_id', $jp->requisition_id ?? '') == $e->id ? 'selected' : '' }}>
                                            {{ $e->no_pengajuan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-lg-4 col-sm-6">
                                <label class="fw-semibold fs-6 mb-2">Position</label>
                                <input disabled style="cursor: not-allowed" type="text"
                                    id="position" class="form-control form-control-solid mb-3 mb-lg-0"
                                    value="" />
                                <input type="hidden" name="position_id" id="position_id"
                                    value="{{ $jp->position_id ?? '' }}">
                            </div>

                            <div class="col-lg-4 col-sm-6">
                                <label class="fw-semibold fs-6 mb-2">Section</label>
                                <input disabled style="cursor: not-allowed" type="text"
                                    id="section" class="form-control form-control-solid mb-3 mb-lg-0"
                                    value="" />
                                <input type="hidden" name="section_id" id="section_id"
                                    value="{{ $jp->section_id ?? '' }}">
                            </div>

                            <div class="col-lg-4 col-sm-6">
                                <label class="fw-semibold fs-6 mb-2">Needs</label>
                                <input disabled style="cursor: not-allowed" type="text"
                                    id="needs_display" class="form-control form-control-solid mb-3 mb-lg-0"
                                    value="" />
                                <input type="hidden" name="needs" id="needs"
                                    value="{{ $jp->needs ?? '' }}">
                            </div>

                            <div class="col-lg-4 col-sm-6">
                                <label class="fw-semibold fs-6 mb-2">Status</label>
                                <input disabled style="cursor: not-allowed" type="text" id="employee_status_display"
                                    class="form-control form-control-solid mb-3 mb-lg-0" value="" />
                                <input type="hidden" name="employee_status" id="employee_status"
                                    value="{{ $jp->employee_status ?? '' }}">
                            </div>

                            <div class="col-lg-4 col-sm-6">
                                <label class="fw-semibold fs-6 mb-2">Department</label>
                                <input disabled style="cursor: not-allowed" type="text" id="department"
                                    class="form-control form-control-solid mb-3 mb-lg-0" value="" />
                                <input type="hidden" name="department_id" id="department_id"
                                    value="{{ $jp->department_id ?? '' }}">
                                <input type="hidden" name="area_id" id="area_id"
                                    value="{{ $jp->area_id ?? '' }}">
                            </div>

                            <div class="col-12">
                                <hr>
                                <h5 class="text-center">More Information</h5>
                            </div>

                            <div class="col-12">
                                <label class="fw-semibold fs-6 mb-2">Job Title</label>
                                <input required type="text" name="title" id="title" 
                                    class="form-control form-control-solid mb-3 mb-lg-0" 
                                    value="{{ old('title', $jp->title ?? '') }}"
                                    placeholder="Input Job Title for Needs" style="text-transform: uppercase"/>
                            </div>

                            <div class="col-lg-4 col-sm-6">
                                <label class="required fw-semibold fs-6 mb-2" for="apply_start">Start Apply</label>
                                <div class="input-group">
                                    <input type="text" class="form-control text-sm" id="apply_start"
                                        name="apply_start" placeholder="Select Date"
                                        value="{{ old('apply_start', optional($jp)->apply_start ? optional($jp)->apply_start->format('d/m/Y') : '') }}"
                                        required>
                                    <span class="input-group-text" id="basic-addon2"><i
                                            class="ri-calendar-todo-line"></i></span>
                                </div>
                            </div>

                            <div class="col-lg-4 col-sm-6">
                                <label class="required fw-semibold fs-6 mb-2" for="apply_end">End Apply</label>
                                <div class="input-group">
                                    <input type="text" class="form-control text-sm" id="apply_end" name="apply_end"
                                        placeholder="Select Date"
                                        value="{{ old('apply_end', optional($jp)->apply_end ? optional($jp)->apply_end->format('d/m/Y') : '') }}"
                                        required>
                                    <span class="input-group-text" id="basic-addon2"><i
                                            class="ri-calendar-todo-line"></i></span>
                                </div>
                            </div>

                            <div class="col-lg-4 col-sm-6">
                                <label class="fw-semibold fs-6 mb-2">Noted</label>
                                <input type="text" name="noted" id="noted" 
                                    class="form-control form-control-solid mb-3 mb-lg-0" 
                                    value="{{ old('noted', $jp->noted ?? '') }}"
                                    placeholder="Input your Noted"/>
                            </div>
                            
                            <div class="col-12">
                                <h5 class="text-center text-primary">Qualification<span class="text-danger"><br>(Max. <span id="count_qualification">1000</span> Character)</span></h5>
                                <textarea class="form-control" id="qualification" name="qualification" rows="7" maxlength="5000"
                                    placeholder="Describe the qualification for job posting..." required>{{ old('qualification', $jp->qualification ?? '') }}</textarea>
                            </div>

                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2 pt-10">
                                    <button type="submit" name="status" value="DRAFT" class="btn btn-secondary">
                                        DRAFT
                                    </button>
                                    <button type="button" class="btn btn-success" id="publishButton">
                                        PUBLISH
                                    </button>
                                    <div class="modal fade" id="publishModal" tabindex="-1"
                                        aria-labelledby="publishModalLabel" aria-hidden="true" style="display: none;">
                                        <div class="modal-dialog modal-dialog-top">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="publishModalLabel">Publish Job Posting</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body text-center p-5">
                                                    <p class="text-muted">Are you sure you want to publish this job posting?
                                                    </p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" name="status" value="PUBLISH"
                                                        form="jpForm" class="btn btn-success">Yes, Publish</button>
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
@endsection
@section('javascript')
    <script type="text/javascript">
        $(function() {
            $('.select2').select2();
        });
        $(document).ready(function() {
            const jobId = $('#id').val();
            let lastClickedSubmit;
            $("form button[type='submit']").on("click", function() {
                lastClickedSubmit = $(this);
            });
            $("form").submit(function(e) {
                e.preventDefault();
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
            
            var publishModal = new bootstrap.Modal(document.getElementById('publishModal'));
            $('#publishButton').on('click', function() {
                var form = document.getElementById('jpForm');
                if (form.checkValidity() === false) {
                    form.reportValidity();
                    return;
                }
                publishModal.show();
            });

            const requisitionChangeHandler = function() {
                const erId = $(this).val();
                $('#needs').val('');
                $('#needs_display').val('');
                $('#position').val('');
                $('#position_id').val('');
                $('#department').val('');
                $('#department_id').val('');
                $('#area_id').val('');
                $('#section').val('');
                $('#section_id').val('');
                $('#employee_status').val('');
                $('#employee_status_display').val('');
                if (erId) {
                    Swal.fire({
                        title: 'Loading data...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading()
                        }
                    });
                    const reqUrl =
                        "{{ route('job-posting.get-requisition', ['requisition_id' => '__ID__']) }}".replace(
                            '__ID__', erId);
                    $.ajax({
                        url: reqUrl,
                        type: 'GET',
                        success: function(data) {
                            if (data && data.length > 0) {
                                Swal.close();
                                const requisition = data[0];
                                $('#needs').val(`${requisition.needs ?? '0'}`);
                                $('#needs_display').val(`${requisition.needs ?? '0'} Person`);
                                $('#position').val(`${requisition.position?.nama ?? 'NA'}`);
                                $('#position_id').val(`${requisition.position_id ?? ''}`);
                                $('#department').val(`${requisition.department?.name ?? 'NA'}`);
                                $('#department_id').val(`${requisition.department_id ?? ''}`);
                                $('#area_id').val(`${requisition.area_id ?? ''}`);
                                $('#section').val(`${requisition.section?.nama ?? 'NA'}`);
                                $('#section_id').val(`${requisition.section_id ?? ''}`);
                                $('#employee_status').val(`${requisition.employee_status ?? ''}`);
                                $('#employee_status_display').val(`${requisition.employee_status ?? 'NA'}`);
                                const positionName = requisition.position?.nama ?? '';
                                const sectionName = requisition.section?.nama ?? '';
                                let fullTitle = '';
                                if (positionName) {
                                    fullTitle += positionName;
                                    if (sectionName) {
                                        fullTitle += ` ${sectionName}`;
                                    }
                                }
                                if (!jobId || $('#title').val() === '') {
                                     $('#title').val(fullTitle.toUpperCase());
                                }
                                let qualificationContent = requisition.qualification ?? '';
                                if (!jobId) {
                                    $('#qualification').val(qualificationContent.trim()); 
                                }
                                updateCharacterCount('qualification', 'count_qualification', 5000);
                            } else {
                                Swal.close();
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'No Requisition',
                                    text: 'No Employee Requisition Found.',
                                });
                                $('#needs').val('');
                                $('#needs_display').val('');
                                $('#position').val('');
                                $('#position_id').val('');
                                $('#department').val('');
                                $('#department_id').val('');
                                $('#area_id').val('');
                                $('#section').val('');
                                $('#section_id').val('');
                                $('#employee_status').val('');
                                $('#employee_status_display').val('');
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
                                text: 'Failed to load requisition data from the server.',
                            });
                        }
                    });
                }
            };
            
            $('#requisition_id').on('change', requisitionChangeHandler);
            if (jobId && $('#requisition_id').val()) {
                requisitionChangeHandler.call($('#requisition_id')[0]); 
                $('#requisition_id').attr('disabled', true);
            }

            const startInput = document.getElementById("apply_start");
            const endInput = document.getElementById("apply_end");
            const endPicker = flatpickr("#apply_end", {
                allowInput: true,
                dateFormat: "d/m/Y",
                minDate: "today",
            });
            const startPicker = flatpickr("#apply_start", {
                allowInput: true,
                dateFormat: "d/m/Y",
                minDate: "today",
                onChange: function(selectedDates, dateStr, instance) {
                    if (selectedDates.length > 0) {
                        const startDate = selectedDates[0];
                        endPicker.set('minDate', startDate);
                        if (endPicker.selectedDates.length > 0 && startDate > endPicker.selectedDates[0]) {
                            endPicker.clear();
                        }
                    } else {
                        endPicker.set('minDate', 'today');
                    }
                }
            });
            window.addEventListener("DOMContentLoaded", function() {
                if (startInput.value) {
                    const dateParts = startInput.value.split('/');
                    const startDate = new Date(dateParts[2], dateParts[1] - 1, dateParts[0]);
                    endPicker.set('minDate', startDate);
                }
            });

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
                { id: 'qualification', max: 5000 },
            ];
            inputToMonitor.forEach(item => {
                updateCharacterCount(item.id, `count_${item.id}`, item.max); 
            });
        });
    </script>
@endsection
