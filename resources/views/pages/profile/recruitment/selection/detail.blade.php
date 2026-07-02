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
                                            <h4 class="text-primary">Detail Seleksi Calon Karyawan</h4>
                                        </div>
                                        <div class="col-md-6">
                                            <a href="{{ route('recruitment.profile.index', ['tab_done_selection']) }}"
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

            const sidebarToggleBtn = $('#topnav-hamburger-icon'); 
            if (sidebarToggleBtn.length) {
                sidebarToggleBtn.on('click', function() {
                    setTimeout(function() {
                        $('#table_candidate').DataTable().columns.adjust().draw();
                    }, 300);
                });
            }
        });
    </script>
    @include('pages.hrd.recruitment.selection.partials.candidate')
@endsection
