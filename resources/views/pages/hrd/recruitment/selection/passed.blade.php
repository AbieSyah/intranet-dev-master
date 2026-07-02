@extends('layouts.master')
@section('link')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- CSS Libraries --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css" />
    <link href="{{ url('') }}/assets/libs/Datatables/DataTables-1.13.1/css/dataTables.bootstrap5.css" rel="stylesheet" type="text/css" />
    <link href="{{ url('') }}/assets/libs/Datatables/Buttons-2.3.3/css/buttons.dataTables.css" rel="stylesheet" type="text/css" />
    <link href="{{ url('') }}/assets/libs/Datatables/Responsive-2.4.0/css/responsive.bootstrap.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ url('') }}/assets/libs/adminlte/select2/css/4.1.0/select2.min.css">
    <style type="text/css">
        body { background: #f7fbf8; }
        .section { margin-top: 150px; background: #fff; padding: 50px 30px; }
        div.dataTables_wrapper { width: 100%; }
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
    @php
        $candidateData = [];
        if ($selection->candidates) {
            foreach ($selection->candidates as $selCandidate) {
                $row = $selCandidate->candidate ?? null; 
                if (!$row) continue;

                $empPassList = '';
                $candidateName = $row->fullname ?? '-';
                // EMPLOYEE
                if ($selCandidate->assessments) {
                    foreach ($selCandidate->assessments as $assessment) {
                        $assessor = $assessment->employee;
                        if (!$assessor) continue;
                        $selectionEmployee = $selection->employees->where('employee_id', $assessor->id)->first();
                        if (!$selectionEmployee || is_null($selectionEmployee->completed_at)) {
                            continue;
                        }
                        $empName = $assessor->fullname ?? '-';
                        $btnClass = '';
                        if ($assessment->result_status == 1) {
                            $btnClass = 'btn-success';
                        } else {
                            $btnClass = 'btn-danger';
                        }
                        $safeComment = htmlspecialchars($assessment->comment ?? '-', ENT_QUOTES);
                        $empPassList .= '<div class="mb-1">
                            <button type="button" class="btn '.$btnClass.' btn-sm w-100 fw-bold btn-view-comment" 
                                data-candidate="'.$candidateName.'" 
                                data-name="'.$empName.'" 
                                data-status="'.$assessment->result_status.'" 
                                data-comment="'.$safeComment.'">
                                '.$empName.'
                            </button></div>';
                    }
                }
                $empPassList = $empPassList ?: '-';

                $isPassed = $selCandidate->result_status == 1;
                $age = $row->birthdate ? \Carbon\Carbon::parse($row->birthdate)->diff(\Carbon\Carbon::now())->format('%y Years') : '-';
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

                $rawEducations = optional($row->educations)->sortByDesc('end_year')->values();
                $rawExperiences = optional($row->experiences)->sortByDesc('end_date')->values();

                $candidateData[] = [
                    'candidate_id' => $row->id,
                    'created_at_ts' => optional($row->created_at)->timestamp ?? time(),
                    'no_ktp' => $row->no_ktp ?? '-',
                    'fullname' => $row->fullname ?? '-',
                    'age' => $age,
                    'gender' => $row->gender ?? '-',
                    'edu' => $eduOutput,
                    'years_exp' => $expYearsOutput ?: '-',
                    'position' => $expPositionOutput ?: '-',
                    'company' => $expCompanyOutput ?: '-',
                    'skill' => $row->skill ?? '-',
                    'is_passed' => $isPassed,
                    'is_present' => $selCandidate->is_present,
                    'comment' => $selCandidate->comment ?? '',
                    'action' => '<div class="d-flex justify-content-center gap-1">
                                    <button type="button" title="Detail" class="btn btn-info btn-sm view-detail" data-id="' . $row->id . '"><i class="ri-eye-2-line"></i></button>
                                    <button type="button" title="Delete" class="btn btn-danger btn-sm remove-candidate" data-id="' . $row->id . '"><i class="ri-delete-bin-line"></i></button>
                                 </div>',
                    'emp_pass_html'    => $empPassList,
                    'attachment' => $selCandidate->attachment ?? null,

                    'raw_data' => [
                        'photo' => $row->photo,
                        'nickname' => $row->nickname,
                        'ktp_address' => $row->ktp_address,
                        'domicile_address' => $row->domicile_address,
                        'birthplace' => $row->birthplace,
                        'birthdate' => $row->birthdate ? \Carbon\Carbon::parse($row->birthdate)->format('d/m/Y') : '-',
                        'age' => $age,
                        'gender' => $row->gender,
                        'religion' => $row->religion,
                        'marital' => $row->marital,
                        'height' => $row->height,
                        'weight' => $row->weight,
                        'phone' => $row->phone,
                        'email' => $row->email,
                        'posting_title' => optional($row->posting)->title ?? '-',
                        'submit_date' => $row->created_at ? \Carbon\Carbon::parse($row->created_at)->format('d F Y - H:i:s') : '-',
                        'pos_name' => optional($row->position)->nama ?? '-',
                        'sect_name' => optional($row->section)->nama ?? '-',
                        'dept_name' => optional($row->department)->name ?? '-',
                        'area_name' => optional($row->area)->name ?? '-',
                    ],
                    'raw_educations' => $rawEducations,
                    'raw_experiences' => $rawExperiences
                ];
            }
        }
    @endphp
    
    <input type="hidden" id="candidate_data_json" value="{{ json_encode($candidateData) }}">

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header align-items-center d-flex justify-content-between">
                    <h3 class="card-title">Selection {{ $selection->hiringStep->masterHiring->name ?? '' }}</h3>
                    <div class="flex-shrink-0">
                        <a href="{{ route('selection.index') }}" class="btn btn-primary btn-label"><i class="ri-arrow-left-fill label-icon align-middle fs-16 me-2"></i> Back</a>
                    </div>
                </div>
                <div class="card-body">
                    <form class="form" id="selectionForm" action="{{ route('selection.passed.store') }}">
                        @csrf
                        <div class="row gy-3">
                            <input type="hidden" name="id" id="id" value="{{ $selection->id ?? '' }}">
                            <div class="col-lg-4">
                                <label class="fw-semibold fs-6 mb-2">Requisition</label>
                                <input type="text" class="form-control" 
                                    value="{{ optional($selection->requisition->position)->nama }} {{ optional($selection->requisition->section)->nama ?? '' }} ({{ $selection->requisition->no_pengajuan ?? '-' }})" 
                                    disabled/>
                            </div>
                            <div class="col-lg-4">
                                <label class="fw-semibold fs-6 mb-2">Step Selection
                                    @php
                                        $currentOrder = $selection->hiringStep->step_order ?? 0;
                                        $lastOrder = $selection->requisition->hiringSteps()->max('step_order');
                                    @endphp
                                    @if($currentOrder > 0 && $currentOrder == $lastOrder) 
                                        <span class="text-success">(Last)</span>
                                    @endif
                                </label>
                                <input type="text" class="form-control" value="{{ $selection->hiringStep->masterHiring->name ?? '-' }}" disabled/>
                            </div>
                            <div class="col-lg-4">
                                <label class="fw-semibold fs-6 mb-2">Noted</label>
                                <input type="text" class="form-control" value="{{ $selection->noted ?? '' }}" disabled/>
                            </div>
                            <div class="col-lg-6">
                                <label class="fw-semibold fs-6 mb-2">Schedule</label>
                                <input type="text" class="form-control" value="{{ optional($selection->scheduled_at)->format('d/m/Y H:i') ?? '' }}" disabled>
                            </div>
                            <div class="col-lg-6">
                                <label class="fw-semibold fs-6 mb-2">Location</label>
                                <input type="text" class="form-control" value="{{ $selection->location ?? '' }}" disabled/>
                            </div>

                            @if($selection->employees->isNotEmpty())
                            <div class="col-12">
                                <hr>
                                <h5 class="text-center mb-3">Assessment Team</h5>
                                <div class="table-responsive">
                                    <table class="table table-borderless table-nowrap mb-0">
                                        <thead class="align-middle table-active">
                                            <tr>
                                                <th class="text-center">#</th>
                                                <th class="text-center">NIK</th>
                                                <th>Fullname</th>
                                                <th>Position</th>
                                                <th class="text-center">Assessment</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($selection->employees as $index => $existingEmp)
                                                <tr>
                                                    <th class="text-center">{{ $index + 1 }}</th>
                                                    <td class="text-center">{{ $existingEmp->employee->nik ?? '-' }}</td>
                                                    <td>{{ $existingEmp->employee->fullname ?? '-' }}</td>
                                                    <td>{{ $existingEmp->employee->position->nama ?? '-' }}</td>
                                                    <td class="text-center">
                                                        @if($existingEmp->completed_at)
                                                            <span class="badge text-bg-success">DONE</span>
                                                        @else
                                                            <span class="badge text-bg-primary">PROCESS</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @endif

                            <div class="col-12">
                                <hr>
                                <button type="button" class="btn btn-success btn-label mb-3" data-bs-toggle="modal" data-bs-target="#attendanceModal">
                                    <i class="ri-checkbox-multiple-line label-icon align-middle fs-16 me-2"></i> Manage Attendance
                                </button>
                                <div class="alert alert-primary alert-dismissible alert-label-icon rounded-label fade show" role="alert">
                                    <i class="ri-error-warning-line label-icon"></i>
                                    <strong>Use the checkbox </strong>
                                    <i class="ri-checkbox-fill text-success fs-17" style="vertical-align: -3px;"></i> 
                                    <strong> to mark candidates as PASSED. Result Information : </strong>
                                    <span class="badge text-bg-success">PASSED</span>
                                    <span class="badge text-bg-danger">REJECT</span>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-striped bordered display nowrap" style="width:100%" id="table_candidate">
                                        <thead>
                                            <tr>
                                                <th scope="col" class="text-center">Action</th>
                                                <th scope="col" class="text-center">Comment for Candidate (Final)</th>
                                                <th scope="col" class="text-center">Pass (Final)</th>
                                                <th scope="col" class="text-center">Attachment</th>
                                                <th scope="col">Name</th>
                                                <th scope="col">ID</th>
                                                <th scope="col" class="text-center">Result (Employee)</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                    <input type="hidden" name="candidates_grading" id="candidates_grading">
                                    <input type="hidden" name="process_status" id="process_status">
                                </div>
                                <hr>
                            </div>
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2 pt-10">
                                    <button type="button" onclick="submitSelection(1)" class="btn btn-primary">Save</button>
                                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#releaseModal">Done</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="attendanceModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Manage Candidate Attendance</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="modal-loader" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <h5 class="mt-3 text-muted">Loading candidates data...</h5>
                    </div>
                    <div id="modal-content-wrapper" style="display: none;">
                        <p class="text-muted mb-3">Please Check <i class="ri-checkbox-fill text-success fs-17" style="vertical-align: -3px;"></i> the candidates who are <b>Present</b> for this selection process.</p>
                        <div class="table-responsive">
                            <table class="table table-striped bordered display nowrap" style="width:100%" id="modal_attendance_table">
                                <thead>
                                    <tr>
                                        <th class="text-center">
                                            <input type="checkbox" id="checkAllAttendance" style="transform: scale(1.2);">
                                        </th>
                                        <th scope="col">Name</th>
                                        <th scope="col">ID</th>
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
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="d-flex justify-content-between w-100">
                        <span class="text-muted my-auto">Selected : <b id="count-present">0</b> Candidates</span>
                        <div>
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-success" id="btnUpdateAttendance">Update Attendance List</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="releaseModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-top">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Closing Selection</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-5">
                    <p class="text-muted">Are you sure you want to closing this selection process?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="submitSelection(2)" class="btn btn-success">Yes, Closing</button>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">No</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="attachmentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Manage Attachment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="uploadForm" enctype="multipart/form-data">
                        <input type="hidden" id="attach_candidate_id" name="candidate_id">
                        <input type="hidden" id="attach_selection_id" name="selection_id" value="{{ $selection->id }}">
                        <h5><span class="fw-semibold" id="cName"></span><span class="fw-semibold" id="cId"></span></h5>
                        <label for="attachmentFile" class="fw-semibold fs-6 mb-2">Upload (PDF/Image, Max. 10 MB)</label>
                        <div class="d-flex align-items-start gap-2">
                            <div class="flex-grow-1">
                                <input class="form-control" type="file" id="attachmentFile" name="attachment" accept=".pdf,.jpg,.jpeg,.png">
                                <div class="text-muted fs-11 mt-1 existingFileArea">
                                    Uploading a new file will replace the existing one.
                                </div>
                            </div>
                            <div class="existingFileArea">
                                <a href="#" id="viewFileBtn" target="_blank" class="btn btn-primary text-nowrap">View</a>
                                <button type="button" id="deleteFileBtn" class="btn btn-danger text-nowrap" title="Delete">
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="btnSaveAttachment">Save</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ url('') }}/assets/libs/Datatables/DataTables-1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="{{ url('') }}/assets/libs/Datatables/DataTables-1.13.1/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ url('') }}/assets/libs/Datatables/Responsive-2.4.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ url('') }}/assets/libs/adminlte/select2/js/4.1.0/select2.min.js"></script>
@endsection

@section('javascript')
    <script type="text/javascript">
        let candidateTable;       
        let attendanceTable;      
        let allCandidates = [];
        let modalSelectedIds = []; 
        $(document).ready(function() {
            allCandidates = JSON.parse($('#candidate_data_json').val() || '[]');
            initMainTable();
            function saveAttendanceToDB(candidatesArray) {
                const selectionId = $('#id').val();
                return $.ajax({
                    url: "{{ route('selection.passed.updateAttendance') }}",
                    method: "POST",
                    data: {
                        selection_id: selectionId,
                        candidates: candidatesArray
                    },
                });
            }
            $('#btnUpdateAttendance').on('click', function() {
                if (modalSelectedIds.length === 0) {
                    Swal.fire('No candidates selected.', '', 'error');
                    return;
                }
                let payload = [];
                let countUpdated = 0;
                modalSelectedIds.forEach(idStr => {
                    const index = allCandidates.findIndex(c => c.candidate_id == idStr);
                    if(index !== -1) {
                        allCandidates[index].is_present = 1;
                        countUpdated++;
                        payload.push({ candidate_id: idStr, is_present: 1 });
                    }
                });
                Swal.fire({ title: 'Updating...', didOpen: () => Swal.showLoading() });
                saveAttendanceToDB(payload)
                    .then(() => {
                        reloadMainTable();
                        initAttendanceTable();
                        modalSelectedIds = []; 
                        $('#count-present').text('0');
                        $('#checkAllAttendance').prop('checked', false);
                        $('#attendanceModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Attendance Updated',
                            text: `${countUpdated} candidates marked present.`,
                            timer: 1500,
                            showConfirmButton: false
                        });
                    })
                    .catch((xhr) => {
                        let errorMessage = 'Failed to update database.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error Occurred',
                            text: errorMessage
                        });
                    });
            });

            $('#table_candidate tbody').on('click', '.remove-candidate', function() {
                const id = $(this).data('id');
                Swal.fire({
                    title: 'Delete from Present List?',
                    text: "Candidate status will be updated to Absent immediately.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Yes, Delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({ title: 'Updating...', didOpen: () => Swal.showLoading() });
                        saveAttendanceToDB([{ candidate_id: id, is_present: 0 }])
                            .then(() => {
                                const index = allCandidates.findIndex(c => c.candidate_id == id);
                                if(index !== -1) {
                                    allCandidates[index].is_present = 0;
                                    allCandidates[index].is_passed = 0;
                                }
                                reloadMainTable();
                                initAttendanceTable();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: 'Candidate removed from present list.',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            })
                            .catch(() => {
                                let errorMessage = 'Failed to update database.';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                }
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error Occurred',
                                    text: errorMessage
                                });
                            });
                    }
                });
            });

            $('#attendanceModal').on('show.bs.modal', function () {
                $('#modal-loader').show();
                $('#modal-content-wrapper').hide();
                modalSelectedIds = [];
                $('#count-present').text('0');
                $('#checkAllAttendance').prop('checked', false);
            });

            $('#attendanceModal').on('shown.bs.modal', function () {
                initAttendanceTable();
                var $wrapper = $('#modal-content-wrapper');
                $wrapper.css('opacity', 0).show();
                requestAnimationFrame(function() {
                    attendanceTable.columns.adjust().responsive.recalc();
                    $('#modal-loader').hide();
                    $wrapper.animate({ opacity: 1 }, 200);
                });
            });

            $('#checkAllAttendance').on('click', function() {
                const isChecked = this.checked;
                const rows = attendanceTable.rows({ page: 'current' }).nodes();
                $('input.attendance-checkbox', rows).prop('checked', isChecked).trigger('change');
            });

            $('#modal_attendance_table tbody').on('change', '.attendance-checkbox', function() {
                const id = $(this).val().toString();
                if (this.checked) {
                    if (!modalSelectedIds.includes(id)) modalSelectedIds.push(id);
                } else {
                    modalSelectedIds = modalSelectedIds.filter(val => val !== id);
                }
                $('#count-present').text(modalSelectedIds.length);
                updateCheckAllStateModal(); 
            });

            // attachment
            $('#table_candidate tbody').on('click', '.btn-attachment', function() {
                const candidateId = $(this).data('id');
                const rowData = allCandidates.find(c => c.candidate_id == candidateId);
                if (!rowData) return;
                $('#uploadForm')[0].reset();
                $('#attach_candidate_id').val(candidateId);
                $('#cName').text(rowData.fullname); 
                $('#cId').text(' (' + rowData.no_ktp + ')');
                if (rowData.attachment) {
                    $('.existingFileArea').show();
                    let fileUrl = "{{ asset('storage/candidates/selection') }}/" + rowData.attachment;
                    $('#viewFileBtn').attr('href', fileUrl);
                } else {
                    $('.existingFileArea').hide();
                    $('#viewFileBtn').attr('href', '#');
                }
                $('#attachmentModal').modal('show');
            });

            $('#btnSaveAttachment').on('click', function() {
                let fileInput = $('#attachmentFile')[0].files[0];
                if (!fileInput) {
                    Swal.fire('Warning', 'Please select a file to upload.', 'warning');
                    return;
                }
                let formData = new FormData($('#uploadForm')[0]);
                Swal.fire({
                    title: 'Uploading...',
                    text: 'Please wait while we save the file.',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });
                $.ajax({
                    url: "{{ route('selection.upload.attachment') }}",
                    method: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        Swal.close();
                        $('#attachmentModal').modal('hide');
                        const candidateId = $('#attach_candidate_id').val();
                        const index = allCandidates.findIndex(c => c.candidate_id == candidateId);
                        if (index !== -1) {
                            allCandidates[index].attachment = response.filename;
                        }
                        reloadMainTable();
                        Swal.fire({
                            icon: 'success',
                            title: 'Uploaded!',
                            text: 'Attachment has been saved successfully.',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    },
                    error: function(xhr) {
                        Swal.close();
                        let msg = xhr.responseJSON?.message || 'Upload failed.';
                        Swal.fire('Error', msg, 'error');
                    }
                });
            });

            $('#deleteFileBtn').on('click', function() {
                const candidateId = $('#attach_candidate_id').val();
                const selectionId = $('#attach_selection_id').val();
                Swal.fire({
                    title: 'Delete Attachment?',
                    text: "Are you sure you want to remove this file? This action cannot be undone.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Deleting...',
                            allowOutsideClick: false,
                            didOpen: () => Swal.showLoading()
                        });
                        $.ajax({
                            url: "{{ route('selection.delete.attachment') }}",
                            method: "POST",
                            data: {
                                candidate_id: candidateId,
                                selection_id: selectionId,
                            },
                            success: function(response) {
                                Swal.close();
                                const index = allCandidates.findIndex(c => c.candidate_id == candidateId);
                                if (index !== -1) {
                                    allCandidates[index].attachment = null;
                                }
                                $('.existingFileArea').hide();
                                $('#attachmentFile').val('');
                                reloadMainTable();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: response.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            },
                            error: function(xhr) {
                                Swal.close();
                                let msg = xhr.responseJSON?.message || 'Delete failed.';
                                Swal.fire('Error', msg, 'error');
                            }
                        });
                    }
                });
            });

            const sidebarToggleBtn = $('#topnav-hamburger-icon'); 
            if (sidebarToggleBtn.length) {
                sidebarToggleBtn.on('click', function() {
                    setTimeout(function() {
                        $('#table_candidate').DataTable().columns.adjust().draw();
                    }, 300);
                });
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        });

        function initMainTable() {
            const presentData = allCandidates.filter(item => item.is_present == true || item.is_present == 1);
            candidateTable = $('#table_candidate').DataTable({
                data: presentData,
                destroy: true,
                stateSave: false,
                responsive: false,
                scrollX: true,
                order: [[4, 'asc']], 
                columns: [
                    {
                        data: 'action',
                        className: "text-center",
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'comment',
                        className: "text-center",
                        orderable: false,
                        render: function(data, type, row) {
                            const val = data ? data : '';
                            return `<textarea class="form-control form-control-sm comment-input" 
                                    rows="5" 
                                    maxlength="300" 
                                    placeholder="(Max 300 Character)" 
                                    style="min-width: 200px; resize: vertical;">${val}</textarea>`;
                        }
                    },
                    {
                        data: 'candidate_id',
                        className: "text-center",
                        orderable: false,
                        render: function(data, type, row) {
                            const checked = row.is_passed ? 'checked' : '';
                            return `<input type="checkbox" class="pass-checkbox mt-2" value="${data}" ${checked} style="transform: scale(1.2);">`;
                        }
                    },
                    {
                        data: 'attachment',
                        className: "text-center",
                        orderable: false,
                        render: function(data, type, row) {
                            let btnClass = data ? 'btn-success' : 'btn-primary';
                            let icon = data ? 'ri-file-text-line' : 'ri-file-upload-line';
                            let tooltip = data ? 'Manage File' : 'Upload File';
                            return `<button type="button" class="btn btn-sm ${btnClass} btn-attachment" 
                                        data-id="${row.candidate_id}" 
                                        title="${tooltip}">
                                        <i class="${icon}"></i>
                                    </button>`;
                        }
                    },
                    { data: 'fullname', defaultContent: '-' },
                    { data: 'no_ktp', defaultContent: '-' },
                    {
                        data: 'emp_pass_html', 
                        className: "text-center",
                        orderable: false 
                    }
                ],
                language: {
                    emptyTable: "No candidates present yet. Please click 'Manage Attendance' to check them in."
                }
            });

            candidateTable.on('draw', function() {
                updateCheckAllStateMain();
            });

            $('#table_candidate tbody').off('change', '.pass-checkbox').on('change', '.pass-checkbox', function() {
                const id = $(this).val();
                const found = allCandidates.find(c => c.candidate_id == id);
                if(found) found.is_passed = this.checked;
                updateCheckAllStateMain();
            });

            $('#table_candidate tbody').off('keyup change', '.comment-input').on('keyup change', '.comment-input', function() {
                const row = candidateTable.row($(this).closest('tr'));
                const data = row.data();
                const found = allCandidates.find(c => c.candidate_id == data.candidate_id);
                if(found) found.comment = $(this).val();
            });
        }

        function updateCheckAllStateMain() {
            const rows = candidateTable.rows({ page: 'current' }).nodes();
            const total = $('input.pass-checkbox', rows).length;
            const checked = $('input.pass-checkbox:checked', rows).length;
            $('#checkAll').prop('checked', total > 0 && total === checked);
        }

        function reloadMainTable() {
            const presentData = allCandidates.filter(item => item.is_present == true || item.is_present == 1);
            candidateTable.clear().rows.add(presentData).draw();
        }

        function initAttendanceTable() {
            const absentData = allCandidates.filter(item => !item.is_present || item.is_present == 0);
            if (absentData.length === 0) {
                $('#checkAllAttendance').prop('checked', false).prop('disabled', true);
            }
            if ($.fn.DataTable.isDataTable('#modal_attendance_table')) {
                attendanceTable.clear().rows.add(absentData).draw();
                updateCheckAllStateModal();
                return;
            }

            attendanceTable = $('#modal_attendance_table').DataTable({
                data: absentData, 
                destroy: true,
                stateSave: false,
                responsive: false,
                scrollX: true,
                order: [[1, 'asc']], 
                columns: [
                    {
                        data: 'candidate_id',
                        className: "text-center",
                        orderable: false,
                        render: function(data, type, row) {
                            const isChecked = modalSelectedIds.includes(data.toString()) ? 'checked' : '';
                            return `<input type="checkbox" class="attendance-checkbox mt-1" value="${data}" ${isChecked} style="transform: scale(1.2);">`;
                        }
                    },
                    { data: 'fullname', defaultContent: '-' },
                    { data: 'no_ktp', defaultContent: '-' },
                    { data: 'age', defaultContent: '-' },
                    { data: 'edu', defaultContent: '-' },
                    { data: 'years_exp', defaultContent: '-' },
                    { data: 'position', defaultContent: '-' },
                    { data: 'company', defaultContent: '-' },
                    { data: 'skill', defaultContent: '-' }
                ],
                language: {
                    emptyTable: "Great! All candidates are already marked as present."
                }
            });
            attendanceTable.on('draw', function() {
                updateCheckAllStateModal();
            });
        }

        function updateCheckAllStateModal() {
            if (!$.fn.DataTable.isDataTable('#modal_attendance_table')) return;
            const table = $('#modal_attendance_table').DataTable();
            const totalRecords = table.rows().count();
            const rows = table.rows({ page: 'current' }).nodes();
            const totalCheckbox = $('input.attendance-checkbox', rows).length;
            const checkedCheckbox = $('input.attendance-checkbox:checked', rows).length;
            if (totalRecords === 0) {
                $('#checkAllAttendance').prop('checked', false);
                $('#checkAllAttendance').prop('disabled', true);
            } else {
                $('#checkAllAttendance').prop('disabled', false);
                if (totalCheckbox > 0 && totalCheckbox === checkedCheckbox) {
                    $('#checkAllAttendance').prop('checked', true);
                } else {
                    $('#checkAllAttendance').prop('checked', false);
                }
            }
        }

        function submitSelection(statusValue) {
            const form = document.getElementById('selectionForm');    
            const dataToSubmit = allCandidates.filter(c => c.is_present).map(item => {
                return {
                    candidate_id: item.candidate_id,
                    is_passed: item.is_passed ? 1 : 0,
                    comment: item.comment,
                    is_present: 1 
                };
            });

            if (dataToSubmit.length === 0) {
                Swal.fire('Warning', 'No candidates present yet.', 'warning');
                return;
            }

            $('#candidates_grading').val(JSON.stringify(dataToSubmit));
            $('#process_status').val(statusValue);
            const formData = new FormData(form);
            Swal.fire({
                title: 'Saving data...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            $.ajax({
                url: form.action,
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.close();
                    $('#releaseModal').modal('hide');
                    Swal.fire({
                        title: "Success",
                        text: response.message,
                        icon: "success",
                        confirmButtonText: "Ok, got it!",
                        customClass: { confirmButton: "btn btn-primary" }
                    }).then(() => {
                        window.location.href = response.redirect;
                    });
                },
                error: function(xhr) {
                    Swal.close();
                    let errorMsg = xhr.responseJSON?.message || 'An error occurred.';
                    Swal.fire('Error', errorMsg, 'error');
                }
            });
        }
    </script>
    @include('pages.hrd.recruitment.selection.partials.candidate')
@endsection