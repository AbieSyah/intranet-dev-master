@extends('layouts.general')

@section('link')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">

    <link href="{{ url('') }}/assets/libs/Datatables/DataTables-1.13.1/css/dataTables.bootstrap5.css" rel="stylesheet"
        type="text/css" />

    <link href="{{ url('') }}/assets/libs/Datatables/Buttons-2.3.3/css/buttons.dataTables.css" rel="stylesheet"
        type="text/css" />

    <link href="{{ url('') }}/assets/libs/Datatables/Responsive-2.4.0/css/responsive.bootstrap.css" rel="stylesheet"
        type="text/css" />

    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <!-- Select2-->
    <link rel="stylesheet" href="{{ url('') }}/assets/libs/adminlte/select2/css/4.1.0/select2.min.css">
    <style>
        .table-responsive {
            overflow: visible;
        }

        .centered {
            text-align: center;
        }

        div.dataTables_wrapper {
            width: 100%;
        }

        .hidden-column {
            display: none;
        }

        .btn-with-badge {
            overflow: visible;
        }

        /* Custom styles for the timeline */
        .timeline {
            position: relative;
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .timeline-item {
            width: 100%;
            position: relative;
            padding-left: 50px;
            padding-top: 6px;
            padding-bottom: 6px;
        }

        .timeline-item:last-child {
            padding-bottom: 0;
        }

        .timeline-marker {
            position: absolute;
            top: 15px;
            left: 14px;
            width: 15px;
            height: 15px;
            border-radius: 50%;
            background-color: #ddd;
            border: 2px solid #ddd;
        }

        .timeline-item.completed .timeline-marker {
            background-color: #0ab39c;
            border-color: #0ab39c;
        }

        .timeline-item.completed .timeline-line {
            background-color: #0ab39c;
        }

        .timeline-line {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            left: 20px;
            width: 2px;
            background-color: #ddd;
        }
        
        .timeline::after {
            background: none !important;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <ul class="nav nav-tabs nav-tabs-custom nav-success" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link py-3 active" id="tab-rule" data-bs-toggle="tab" href="#pill-process"
                            role="tab">
                            <i class="ri-survey-line me-1 align-bottom"></i> On Process
                            @if ($jml_process > 0)
                                <span class="badge bg-danger">{{ $jml_process }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link py-3" id="tab-benefit" data-bs-toggle="tab" href="#pill-done" role="tab">
                            <i class="bi bi-clipboard-check me-1 align-bottom"></i> Done
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="pill-process" role="tabpanel">
                        <div class="px-3 mt-4 mb-2 align-items-center d-flex">
                            <button type="button" name="reset" id="reset-process"
                                class="btn btn-soft-danger waves-effect waves-light btn-sm"><i
                                    class="ri-refresh-line me-1 align-bottom"></i> Refresh</button>
                            <button id="multi-approve-btn-process" type="button" title="Approve"
                                class="btn btn-success btn-sm waves-effect waves-light position-relative btn-with-badge ms-2"
                                style="display: none">
                                <i class="ri-check-line"></i>
                                <span
                                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    <span id="approve-count-process">0</span>
                                    <span class="visually-hidden">approve selected</span>
                                </span>
                            </button>
                            <button id="multi-print-btn-process" type="button" title="Resume Evaluation"
                                class="btn btn-success btn-sm waves-effect waves-light position-relative btn-with-badge ms-2"
                                style="display: none">
                                <i class="ri-file-text-line"></i>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    <span id="print-count-process">0</span>
                                    <span class="visually-hidden">print selected</span>
                                </span>
                            </button>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped bordered display nowrap" style="width:100%"
                                id="table_process">
                                <thead>
                                    <tr>
                                        <th style="text-align:center"><input type="checkbox" id="checkAllProcess"></th>
                                        <th scope="col" style="text-align:center">Action</th>
                                        <th scope="col" style="text-align:center">NIK</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Department</th>
                                        <th scope="col">Period</th>
                                        <th scope="col">Purpose</th>
                                        <th scope="col" style="text-align:center">Status</th>
                                        <th scope="col">KPI</th>
                                        <th scope="col">Attitude & Performance</th>
                                        <th scope="col">Attendance</th>
                                        <th scope="col">Total</th>
                                        <th scope="col">Grade</th>
                                        <th scope="col">Decision</th>
                                        <th scope="col" class="hidden-column" style="display:none">Created At</th>
                                        <th scope="col" class="hidden-column" style="display:none">Has Action</th>
                                        <th scope="col" class="hidden-column" style="display:none">Role</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane" id="pill-done" role="tabpanel">
                        <div class="px-3 mt-4 mb-2 align-items-center d-flex row">
                            <div class="col-md-2 mb-2">
                                <select class="form-control js-example-basic-single" name="tahun" id="tahun" required>
                                    @foreach($years as $year)
                                        <option value="{{ $year }}" @if($year == date('Y')) selected @endif>{{ $year }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-2">
                                <button type="button" name="filter" id="filter-done" class="btn btn-soft-secondary waves-effect waves-light btn-sm me-2"><i class="ri-filter-2-line me-1 align-bottom"></i> Filters</button>
                                <button type="button" name="refresh" id="reset-done" class="btn btn-soft-danger waves-effect waves-light btn-sm me-2"><i class="ri-refresh-line me-1 align-bottom"></i> Reset</button>
                                <button id="multi-print-btn-done" type="button" title="Resume Evaluation"
                                    class="btn btn-success btn-sm waves-effect waves-light position-relative btn-with-badge"
                                    style="display: none">
                                    <i class="ri-file-text-line"></i>
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                        <span id="print-count-done">0</span>
                                        <span class="visually-hidden">print selected</span>
                                    </span>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped bordered display nowrap" style="width:100%" id="table_done">
                                <thead>
                                    <tr>
                                        <th style="text-align:center"><input type="checkbox" id="checkAllDone"></th>
                                        <th scope="col" style="text-align:center">Action</th>
                                        <th scope="col" style="text-align:center">NIK</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Department</th>
                                        <th scope="col">Period</th>
                                        <th scope="col">Purpose</th>
                                        <th scope="col" style="text-align:center">Status</th>
                                        <th scope="col">KPI</th>
                                        <th scope="col">Attitude & Performance</th>
                                        <th scope="col">Attendance</th>
                                        <th scope="col">Total</th>
                                        <th scope="col">Grade</th>
                                        <th scope="col">Decision</th>
                                        <th scope="col" class="hidden-column" style="display:none">Created At</th>
                                        <th scope="col" class="hidden-column" style="display:none">Has Action</th>
                                        <th scope="col" class="hidden-column" style="display:none">Role</th>
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
    <div id="approveConfirmationModal" class="modal fade flip" tabindex="-1"
        aria-labelledby="approveConfirmationModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-dialog-top">
            <div class="modal-content">
                <form class="form" id="approveForm" action="{{ route('evaluation.emp.approveMultiple') }}"
                    method="post">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="approveConfirmationModalLabel">Approve Evaluations</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center p-5">
                        <p class="text-muted" id="approveMessage">Are you sure you want to approve these selected
                            evaluations?</p>
                        <div id="approve-id-container"></div>
                        <input type="hidden" name="role" id="current-role">
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Yes, Approve</button>
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">No</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="trackingModal" tabindex="-1" aria-labelledby="trackingModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="trackingModalLabel">Evaluation Step</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="timeline">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ url('') }}/assets/libs/Datatables/DataTables-1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="{{ url('') }}/assets/libs/Datatables/DataTables-1.13.1/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ url('') }}/assets/libs/Datatables/Responsive-2.4.0/js/dataTables.responsive.min.js"></script>
    <script src="{{ url('') }}/assets/js/pages/datatables.init.js"></script>
    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <!-- Select2 -->
    <script src="{{ url('') }}/assets/libs/adminlte/select2/js/4.1.0/select2.min.js"></script>
@endsection

@section('javascript')
    @include('partials.evaluation.profile.index', [
        'stepsRoute'          => route('evaluation.emp.steps', ':id'),
        'processRoute'        => route('evaluation.emp.process'),
        'doneRoute'           => route('evaluation.emp.done'),
        'countProcessRoute'   => route('evaluation.emp.countprocess'),
        'approveMultipleRoute'=> route('evaluation.emp.approveMultiple'),
        'printTokenRoute'     => route('evaluation.emp.approveMultiple.print.token'),
        'printRoute'          => route('evaluation.emp.approveMultiple.print', ['token' => ':token']),
    ])
    {{-- Toastr session --}}
    <script>
        @if (Session::has('status'))
            toastr.options = { "closeButton": true, "progressBar": true, "positionClass": "toast-bottom-right" }
            toastr.success("{{ session('status') }}");
        @endif
        @if (Session::has('error'))
            toastr.options = { "closeButton": true, "progressBar": true, "positionClass": "toast-bottom-right" }
            toastr.error("{{ session('error') }}");
        @endif
    </script>
@endsection
