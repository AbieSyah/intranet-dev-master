@extends('layouts.general')

@section('link')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
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
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h4 class="text-primary">Detail Seleksi Calon Karyawan</h4>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ route('recruitment.emp.index', ['tab_done_selection']) }}"
                                class="btn btn-primary btn-label waves-effect waves-light float-end"><i
                                    class="ri-arrow-left-circle-line label-icon align-middle fs-16 me-2"></i> Back</a>
                        </div>
                    </div>
                    <hr>
                    @php
                        $candidateData = [];
                        if ($selection->candidates) {
                            foreach ($selection->candidates as $selCandidate) {
                                // Present Only
                                if (!$selCandidate->is_present) {
                                    continue;
                                }
                                
                                $row = $selCandidate->candidate ?? null; 
                                if (!$row) continue;

                                $empPassList = '';
                                $candidateName = $row->fullname ?? '-';
                                // HRD
                                if ($selCandidate->result_status != 0) { 
                                    $hrdLabel = 'HRD';
                                    if ($selection->status == 2) {
                                        $hrdLabel = 'HRD (Final)';
                                    }
                                    $btnClass = '';
                                    if ($selCandidate->result_status == 1) {
                                        $btnClass = 'btn-success';
                                    } elseif ($selCandidate->result_status == 2) {
                                        $btnClass = 'btn-danger';
                                    }
                                    $safeComment = htmlspecialchars($selCandidate->comment ?? '-', ENT_QUOTES);
                                    $empPassList .= '<div class="mb-1">
                                        <button type="button" class="btn '.$btnClass.' btn-sm w-100 fw-bold btn-view-comment" 
                                            data-candidate="'.$candidateName.'" 
                                            data-name="'.$hrdLabel.'" 
                                            data-status="'.$selCandidate->result_status.'" 
                                            data-comment="'.$safeComment.'">
                                            '.$hrdLabel.'
                                        </button></div>';
                                }
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
                                
                                $myAssessment = $selCandidate->assessments
                                                ->where('employee_id', $loggedInEmployeeId)
                                                ->first();
                                $isPassed = false;
                                $comment  = '';
                                if ($myAssessment) {
                                    $isPassed = $myAssessment->result_status == 1; 
                                    $comment  = $myAssessment->comment;
                                }

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
                                    'comment' => $selCandidate->comment ?? '',
                                    'emp_pass_html'    => $empPassList,
                                    'action' => '<button type="button" title="Detail" class="btn btn-info btn-sm view-detail" data-id="' . $row->id . '"><i class="ri-eye-2-line"></i></button>',
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
                    <div class="row gy-3">
                        <input type="hidden" name="id" id="id" value="{{ $selection->id ?? '' }}">
                        <div class="col-lg-4">
                            <label class="fw-semibold fs-6 mb-2">Requisition</label>
                            <input type="text" class="form-control" 
                                value="{{ optional($selection->requisition->position)->nama }} {{ optional($selection->requisition->section)->nama ?? '' }} ({{ $selection->requisition->no_pengajuan ?? '-' }})" 
                                disabled/>
                        </div>
                        <div class="col-lg-4">
                            <label class="fw-semibold fs-6 mb-2">Step Selection</label>
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
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($selection->employees)
                                            @foreach($selection->employees as $index => $existingEmp)
                                                <tr>
                                                    <th class="text-center">{{ $index + 1 }}</th>
                                                    <td class="text-center">{{ $existingEmp->employee->nik ?? '-' }}</td>
                                                    <td>{{ $existingEmp->employee->fullname ?? '-' }}
                                                        @if($loggedInEmployeeId == $existingEmp->employee_id)
                                                            <span class="text-success fw-bold">(You)</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $existingEmp->employee->position->nama ?? '-' }}</td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-12">
                            <hr>
                            <div class="alert alert-primary alert-dismissible alert-label-icon rounded-label fade show" role="alert">
                                <i class="ri-error-warning-line label-icon"></i>
                                <strong>Result Information : </strong>
                                <span class="badge text-bg-success">PASSED</span>
                                <span class="badge text-bg-danger">REJECT</span>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped bordered display nowrap" style="width:100%" id="table_candidate">
                                    <thead>
                                        <tr>
                                            <th scope="col" class="text-center">Action</th>
                                            <th scope="col">Name</th>
                                            <th scope="col">ID</th>
                                            <th scope="col">Age</th>
                                            <th scope="col" class="text-center">Result</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
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
        let allCandidates = [];
        $(document).ready(function() {
            allCandidates = JSON.parse($('#candidate_data_json').val() || '[]');
            const initialCandidateData = JSON.parse($('#candidate_data_json').val() || '[]');
            candidateTable = $('#table_candidate').DataTable({
                data: initialCandidateData,
                stateSave: false,
                responsive: false,
                scrollX: true,
                order: [[1, 'asc']], 
                columns: [
                    {
                        data: 'action',
                        className: "text-center",
                        orderable: false,
                        searchable: false
                    },
                    { data: 'fullname', defaultContent: '-' },
                    { data: 'no_ktp', defaultContent: '-' },
                    { data: 'age', defaultContent: '-' },
                    {
                        data: 'emp_pass_html', 
                        className: "text-center",
                        orderable: false 
                    }
                ]
            });
        });   
    </script>
    @include('pages.hrd.recruitment.selection.partials.candidate')
@endsection
