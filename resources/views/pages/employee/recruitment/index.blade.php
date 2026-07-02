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
                        <a class="nav-link py-3 active" id="tab-er" data-bs-toggle="tab" href="#pill-er-process"
                            role="tab">
                            <i class="ri-user-add-line me-1 align-bottom"></i> Employee Requisition
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link py-3" id="tab-selection" data-bs-toggle="tab" href="#pill-selection-process"
                            role="tab">
                            <i class="ri-user-search-line me-1 align-bottom"></i> Selection
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link py-3" id="tab-result" data-bs-toggle="tab" href="#pill-result"
                            role="tab">
                            <i class="ri-user-follow-line me-1 align-bottom"></i> Result
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="pill-er-process" role="tabpanel">
                        <div class="px-3 mt-4 mb-2 align-items-center d-flex">
                            <ul class="nav nav-pills gap-2 mb-2" id="myTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button type="button" id="tab-process-requisition"
                                        class="btn btn-primary border shadow list-group-item-primary active btn-with-badge"
                                        data-bs-toggle="tab" href="#pill-tab-process-requisition-content" role="tab"
                                        aria-controls="pill-tab-process-requisition-content"
                                        aria-selected="true"><strong>Process</strong>
                                        <span id="approve-badge-container"
                                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                            style="display: none;"> <span id="approve-count">0</span>
                                            <span class="visually-hidden">approve selected</span>
                                        </span>
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button type="button" id="tab-done-requisition"
                                        class="btn btn-primary border shadow list-group-item-primary" data-bs-toggle="tab"
                                        href="#pill-tab-done-requisition" role="tab"
                                        aria-controls="pill-tab-done-requisition" aria-selected="false"><strong>Done</strong>
                                    </button>
                                </li>
                            </ul>
                        </div>
                        <div class="tab-content">
                            <div class="tab-pane active" id="pill-tab-process-requisition-content" role="tabpanel">
                                <div class="px-3 align-items-center d-flex row">
                                    <div class="col-md-6">
                                        <button type="button" name="reset" id="reset-process-er"
                                            class="btn btn-soft-danger waves-effect waves-light btn-sm me-2"><i
                                                class="ri-refresh-line me-1 align-bottom"></i> Refresh</button>
                                        <button id="multi-delete-btn" type="button" title="Delete Selected"
                                            class="btn btn-danger btn-sm waves-effect waves-light position-relative btn-with-badge me-2"
                                            style="display: none">
                                            <i class="ri-delete-bin-line"></i>
                                            <span
                                                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning">
                                                <span id="delete-count-process">0</span>
                                                <span class="visually-hidden">delete selected</span>
                                            </span>
                                        </button>
                                        <button id="multi-approve-btn-process" type="button" title="Approve"
                                            class="btn btn-success btn-sm waves-effect waves-light position-relative btn-with-badge me-2"
                                            style="display: none">
                                            <i class="ri-check-line"></i>
                                            <span
                                                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                                <span id="approve-count-process">0</span>
                                                <span class="visually-hidden">approve selected</span>
                                            </span>
                                        </button>
                                    </div>
                                    <div class="col-md-6">
                                        @if ($hasLineApproval)
                                            <a href="{{ route('recruitment.emp.er.form') }}"
                                                class="btn btn-primary btn-label waves-effect waves-light float-end"
                                                data-text="Buat Pengajuan">
                                                <i class="ri-add-circle-line label-icon align-middle fs-16 me-2"> </i>Buat
                                                Pengajuan
                                            </a>
                                        @endif
                                    </div>
                                </div>
                                <div class="card-body">
                                    <table class="table table-striped bordered display nowrap" style="width:100%"
                                        id="table_process">
                                        <thead>
                                            <tr>
                                                <th style="text-align:center"><input type="checkbox"
                                                        id="checkAllProcessER">
                                                </th>
                                                <th scope="col" class="hidden-column" style="display:none">Created At
                                                </th>
                                                <th scope="col" style="text-align:center">Action</th>
                                                <th scope="col" style="text-align:center">Status</th>
                                                <th scope="col">Applicant</th>
                                                <th scope="col">Needs</th>
                                                <th scope="col">Position</th>
                                                <th scope="col">Employee Status</th>
                                                <th scope="col">Department</th>
                                                <th scope="col">Section</th>
                                                <th scope="col">Area</th>
                                                <th scope="col">Reason</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="tab-pane" id="pill-tab-done-requisition" role="tabpanel">
                                <div class="px-3 align-items-center d-flex">
                                    <button type="button" name="reset" id="reset-done-er"
                                        class="btn btn-soft-danger waves-effect waves-light btn-sm"><i
                                            class="ri-refresh-line me-1 align-bottom"></i> Refresh</button>
                                </div>
                                <div class="card-body">
                                    <table class="table table-striped bordered display nowrap" style="width:100%"
                                        id="table_done">
                                        <thead>
                                            <tr>
                                                <th scope="col" class="hidden-column" style="display:none">Created At
                                                </th>
                                                <th scope="col" style="text-align:center">Action</th>
                                                <th scope="col" style="text-align:center">Status</th>
                                                <th scope="col">Applicant</th>
                                                <th scope="col">Needs</th>
                                                <th scope="col">Position</th>
                                                <th scope="col">Employee Status</th>
                                                <th scope="col">Department</th>
                                                <th scope="col">Section</th>
                                                <th scope="col">Area</th>
                                                <th scope="col">Reason</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Selection --}}
                    <div class="tab-pane" id="pill-selection-process" role="tabpanel">
                        <div class="px-3 mt-4 mb-2 align-items-center d-flex">
                            <ul class="nav nav-pills gap-2 mb-2" id="myTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button type="button" id="tab-process-selection"
                                        class="btn btn-primary border shadow list-group-item-primary active btn-with-badge"
                                        data-bs-toggle="tab" href="#pill-tab-process-selection-content" role="tab"
                                        aria-controls="pill-tab-process-selection-content"
                                        aria-selected="true"><strong>Process</strong>
                                        <span id="review-badge-container"
                                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                            style="display: none;"> <span id="review-count">0</span>
                                            <span class="visually-hidden">review selected</span>
                                        </span>
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button type="button" id="tab-done-selection"
                                        class="btn btn-primary border shadow list-group-item-primary" data-bs-toggle="tab"
                                        href="#pill-tab-done-selection" role="tab"
                                        aria-controls="pill-tab-done-selection" aria-selected="false"><strong>Done</strong>
                                    </button>
                                </li>
                            </ul>
                        </div>
                        <div class="tab-content">
                            <div class="tab-pane active" id="pill-tab-process-selection-content" role="tabpanel">
                                <div class="px-3 align-items-center d-flex">
                                    <button type="button" name="reset" id="reset-process-selection"
                                        class="btn btn-soft-danger waves-effect waves-light btn-sm"><i
                                            class="ri-refresh-line me-1 align-bottom"></i> Refresh</button>
                                </div>
                                <div class="card-body">
                                    <table class="table table-striped bordered display nowrap" style="width:100%"
                                        id="table_process_selection">
                                        <thead>
                                            <tr>
                                                <th scope="col" class="hidden-column" style="display:none">Created At</th>
                                                <th scope="col" style="text-align:center">Action</th>
                                                <th scope="col" style="text-align:center">Status</th>
                                                <th scope="col">Requisition</th>
                                                <th scope="col">Selection</th>
                                                <th scope="col">Noted</th>
                                                <th scope="col">Participant</th>
                                                <th scope="col">Schedule</th>
                                                <th scope="col">Location</th>
                                                <th scope="col">Passed</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="tab-pane" id="pill-tab-done-selection" role="tabpanel">
                                <div class="px-3 align-items-center d-flex">
                                    <button type="button" name="reset" id="reset-done-selection"
                                        class="btn btn-soft-danger waves-effect waves-light btn-sm"><i
                                            class="ri-refresh-line me-1 align-bottom"></i> Refresh</button>
                                </div>
                                <div class="card-body">
                                    <table class="table table-striped bordered display nowrap" style="width:100%"
                                        id="table_done_selection">
                                        <thead>
                                            <tr>
                                                <th scope="col" class="hidden-column" style="display:none">Created At</th>
                                                <th scope="col" style="text-align:center">Action</th>
                                                <th scope="col">Requisition</th>
                                                <th scope="col">Selection</th>
                                                <th scope="col">Noted</th>
                                                <th scope="col">Participant</th>
                                                <th scope="col">Schedule</th>
                                                <th scope="col">Location</th>
                                                <th scope="col">Passed</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Result --}}
                    <div class="tab-pane" id="pill-result" role="tabpanel">
                        <div class="px-3 mt-4 align-items-center d-flex">
                            <button type="button" name="reset" id="reset-result"
                                class="btn btn-soft-danger waves-effect waves-light btn-sm"><i
                                    class="ri-refresh-line me-1 align-bottom"></i> Refresh</button>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped bordered display nowrap" style="width:100%" id="table_result">
                                <thead>
                                    <tr>
                                        <th scope="col" class="hidden-column" style="display:none">Created At</th>
                                        <th scope="col" style="text-align:center">Action</th>
                                        <th scope="col" style="text-align:center">Status</th>
                                        <th scope="col">Requisition</th>
                                        <th scope="col">Needs</th>
                                        <th scope="col">Fulfilled</th>
                                        <th scope="col">Employee Status</th>
                                        <th scope="col">Reason</th>
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

    <div class="modal fade" id="trackingModal" tabindex="-1" aria-labelledby="trackingModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="trackingModalLabel">Employee Requisition Approve</h5>
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
    <div id="deleteConfirmationModal" class="modal fade flip" tabindex="-1"
        aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-dialog-top">
            <div class="modal-content">
                <form class="form" id="universalDeleteForm" action="{{ route('recruitment.emp.er.my-er.destroy') }}"
                    method="post">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteConfirmationModalLabel">Delete Employee Requisition</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center p-5">
                        <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop"
                            style="width:120px;height:120px"></lord-icon>
                        <p class="text-muted" id="deleteMessage">Are you sure you want to delete this requisition?</p>
                        <div id="delete-id-container"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-danger">Yes, Delete</button>
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">No</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div id="approveConfirmationModal" class="modal fade flip" tabindex="-1"
        aria-labelledby="approveConfirmationModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-dialog-top">
            <div class="modal-content">
                <form class="form" id="approveForm" action="{{ route('recruitment.emp.er.approveMultiple') }}"
                    method="post">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="approveConfirmationModalLabel">Approve Employee Requisitions</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center p-5">
                        <p class="text-muted" id="approveMessage">Are you sure you want to approve these selected
                            Employee Requisitions?</p>
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
    <div class="modal fade" id="reasonModal" tabindex="-1" aria-labelledby="reasonModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reasonModalLabel">Reason for Revision</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="decisionReasonContent" class="text-muted">Loading...</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!--Modal staticbackdrop-->
    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body text-center p-5">
                    <img src="{{ url('') }}/assets/images/loading.gif" style="width:120px;height:120px">                    
                    <div class="mt-4">
                        <h4 class="mb-3">Please wait...</h4>
                        <h4 class="mb-3">Do not leave this page</h4>
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
    <script src="{{ url('') }}/assets/js/pages/datatables.init.js"></script>
    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
@endsection

@section('javascript')
    <script>
        $(document).ready(function() {
            // Employee Requisition
            let selectedIds = [];
            const tableProcessId = '#table_process';
            let tableProcess = null;
            const tableDoneId = '#table_done';
            let tableDone = null;
            
            // Selection
            const tableProcessSelectionId = '#table_process_selection';
            let tableProcessSelection = null;
            const tableDoneSelectionId = '#table_done_selection';
            let tableDoneSelection = null;

            // Result
            const tableResultId = '#table_result';
            let tableResult = null;

            let currentTabId = '#pill-tab-process-requisition-content';

            let defaultTabId = '#pill-tab-process-requisition-content';
            @if (Session::has('tab_approve') || request()->has('tab_approve'))
                defaultTabId = '#pill-tab-process-requisition-content';
                currentTabId = defaultTabId;
            @elseif (Session::has('tab_done') || request()->has('tab_done'))
                defaultTabId = '#pill-tab-done-requisition';
                currentTabId = defaultTabId;
            @elseif (Session::has('tab_process_selection') || request()->has('tab_process_selection'))
                defaultTabId = '#pill-tab-process-selection-content';
                currentTabId = defaultTabId;
            @elseif (Session::has('tab_done_selection') || request()->has('tab_done_selection'))
                defaultTabId = '#pill-tab-done-selection';
                currentTabId = defaultTabId;
            @elseif (Session::has('tab_result') || request()->has('tab_result'))
                defaultTabId = '#pill-result';
                currentTabId = defaultTabId;
            @endif

            function initializeDefaultTab() {
                if (defaultTabId === '#pill-tab-done-requisition') {
                    $('#tab-process-requisition').removeClass('active');
                    $('#pill-tab-process-requisition-content').removeClass('active');
                    $('#tab-done-requisition').addClass('active');
                    $('#pill-tab-done-requisition').addClass('active');
                    tableDone = initializeDoneDataTable();
                } else if (defaultTabId === '#pill-tab-process-selection-content') {
                    $('#tab-er').removeClass('active');
                    $('#pill-er-process').removeClass('active');
                    $('#tab-selection').addClass('active');
                    $('#pill-selection-process').addClass('active');
                    $('#tab-done-selection').removeClass('active');
                    $('#pill-tab-done-selection').removeClass('active');
                    $('#tab-process-selection').addClass('active');
                    $('#pill-tab-process-selection-content').addClass('active');
                    tableProcessSelection = initializeProcessSelectionDataTable();
                } else if (defaultTabId === '#pill-tab-done-selection') {
                    $('#tab-er').removeClass('active');
                    $('#pill-er-process').removeClass('active');
                    $('#tab-selection').addClass('active');
                    $('#pill-selection-process').addClass('active');
                    $('#tab-process-selection').removeClass('active');
                    $('#pill-tab-process-selection-content').removeClass('active');
                    $('#tab-done-selection').addClass('active');
                    $('#pill-tab-done-selection').addClass('active');
                    tableDoneSelection = initializeDoneSelectionDataTable();
                } else if (defaultTabId === '#pill-result') {
                    $('#tab-er').removeClass('active');
                    $('#pill-er-process').removeClass('active');
                    $('#tab-selection').removeClass('active');
                    $('#pill-selection-process').removeClass('active');
                    $('#tab-result').addClass('active');
                    $('#pill-result').addClass('active');
                    tableResult = initializeResultDataTable();
                } else {
                    tableProcess = initializeProcessDataTable();
                }
            }

            function updateApproveBadge() {
                $.ajax({
                    url: "{{ route('recruitment.emp.er.approve-er.count') }}",
                    method: 'GET',
                    success: function(response) {
                        const count = response.jml_approve || 0;
                        const $badgeContainer = $('#approve-badge-container');
                        $('#approve-count').text(count);
                        if (count > 0) {
                            $badgeContainer.show();
                        } else {
                            $badgeContainer.hide();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Failed to update approve count:", error);
                        $('#approve-badge-container').hide();
                    }
                });
            }
            updateApproveBadge();

            function updateMultiButtons() {
                const count = selectedIds.length;
                let allMyReq = true;
                $('#multi-delete-btn').hide();
                $('#multi-approve-btn-process').hide();
                if (count > 0) {
                    const allData = $('#table_process').DataTable().data().toArray();
                    for (let i = 0; i < allData.length; i++) {
                        const rowData = allData[i];
                        if (selectedIds.includes(rowData.id) && !rowData.is_my_er) {
                            allMyReq = false;
                            break;
                        }
                    }
                    if (allMyReq) {
                        $('#multi-delete-btn').show();
                        $('#delete-count-process').text(count);
                    } else {
                        $('#multi-approve-btn-process').show();
                        $('#approve-count-process').text(count);
                    }
                }
            }

            function checkSelectAllStatus() {
                const dt = $(tableProcessId).DataTable();
                const enabledCheckboxes = dt.rows({ page: 'current', search: 'applied' }).nodes().to$().find('.row-checkbox:not(:disabled)');
                const totalEnabled = enabledCheckboxes.length;
                const totalChecked = enabledCheckboxes.filter(':checked').length;
                const checkAll = $('#checkAllProcessER');
                if (totalEnabled === 0) {
                    checkAll.prop('checked', false);
                    checkAll.prop('disabled', true);
                } else {
                    checkAll.prop('disabled', false);
                    checkAll.prop('checked', totalEnabled > 0 && totalEnabled === totalChecked);
                }
            }

            $(document).on('change', `${tableProcessId} .row-checkbox`, function() {
                const id = $(this).val();
                if (this.checked) {
                    if (!selectedIds.includes(id)) selectedIds.push(id);
                } else {
                    selectedIds = selectedIds.filter(val => val !== id);
                }
                checkSelectAllStatus();
                updateMultiButtons();
            });

            $('#checkAllProcessER').on('click', function() {
                if (!$(this).is(':disabled')) {
                    const isChecked = this.checked;
                    const dt = $(tableProcessId).DataTable();
                    dt.rows({ page: 'current', search: 'applied' }).nodes().to$().each(function() {
                        const checkbox = $(this).find('.row-checkbox');
                        const id = checkbox.val();
                        if (!checkbox.is(':disabled')) {
                            checkbox.prop('checked', isChecked);
                            if (isChecked) {
                                if (!selectedIds.includes(id)) {
                                    selectedIds.push(id);
                                }
                            } else {
                                selectedIds = selectedIds.filter(val => val !== id);
                            }
                        }
                    });
                    updateMultiButtons();
                }
            });

            $('#multi-approve-btn-process').click(function() {
                let firstSelectedRowData = tableProcess.rows().data().toArray().find(row => selectedIds.includes(String(row.id)));
                let role = firstSelectedRowData ? firstSelectedRowData.role : '';
                const approveForm = $('#approveForm');
                approveForm.attr('action', "{{ route('recruitment.emp.er.approveMultiple') }}");
                const message =
                    `Are you sure you want to approve ${selectedIds.length} selected Employee Requisitions?`;
                $('#approveConfirmationModalLabel').text('Approve Employee Requisitions');
                $('#approveMessage').text(message);
                const idContainer = $('#approve-id-container');
                idContainer.empty();
                selectedIds.forEach(id => {
                    idContainer.append(`<input type="hidden" name="ids[]" value="${id}">`);
                });
                $('#current-role').val(role);
                $('#approveConfirmationModal').modal('show');
            });

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            function initializeProcessDataTable() {
                if ($.fn.DataTable.isDataTable(tableProcessId)) {
                    $(tableProcessId).DataTable().destroy();
                }

                let columnsToUse = [{
                        data: 'id',
                        name: 'id',
                        orderable: false,
                        searchable: false,
                        className: "text-center",
                        render: function(data, type, row, meta) {
                            let isDisabled = '';
                            if (!(row.status && ((row.status.includes('DRAFT') || row.status.includes('REJECT')) || row.has_action))) {
                                isDisabled = 'disabled';
                            }
                            let isChecked = selectedIds.includes(String(data)) ? 'checked' : '';
                            return `<input type="checkbox" class="row-checkbox" value="${data}" ${isDisabled} ${isChecked}>`;
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        className: 'hidden-column',
                        visible: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        className: "text-center",
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'status',
                        name: 'status',
                        className: "text-center",
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'applicantName',
                        name: 'applicantName',
                        defaultContent: '-'
                    },
                    {
                        data: 'needs',
                        name: 'needs',
                        className: "text-center",
                        defaultContent: '-'
                    },
                    {
                        data: 'position',
                        name: 'position',
                        defaultContent: '-'
                    },
                    {
                        data: 'employee_status',
                        name: 'employee_status',
                        defaultContent: '-'
                    },
                    {
                        data: 'department',
                        name: 'department',
                        defaultContent: '-'
                    },
                    {
                        data: 'section',
                        name: 'section',
                        defaultContent: '-'
                    },
                    {
                        data: 'area',
                        name: 'area',
                        defaultContent: '-'
                    },
                    {
                        data: 'reason',
                        name: 'reason',
                        defaultContent: '-'
                    },
                ];
                let orderIndex = 1;

                return $(tableProcessId).DataTable({
                    destroy: true,
                    stateSave: false,
                    responsive: false,
                    autoWidth: false,
                    processing: true,
                    serverSide: false,
                    scrollX: true,
                    order: [
                        [orderIndex, 'desc']
                    ],
                    ajax: {
                        url: "{{ route('recruitment.emp.er.process-combined') }}", 
                        error: function(xhr, error, thrown) {
                            console.error("DataTables AJAX Error (Process Combined ER):", xhr.responseText);
                        }
                    },
                    columns: columnsToUse,
                    "drawCallback": function(settings) {
                        checkSelectAllStatus();
                        updateMultiButtons();
                        updateApproveBadge();
                        const api = this.api();
                        api.rows({ page: 'current' }).nodes().to$().each(function() {
                            const checkbox = $(this).find('.row-checkbox');
                            const id = checkbox.val();
                            if (selectedIds.includes(id)) {
                                checkbox.prop('checked', true);
                            }
                        });
                    }
                });
            }

            function initializeDoneDataTable() {
                if ($.fn.DataTable.isDataTable(tableDoneId)) {
                    $(tableDoneId).DataTable().destroy();
                }
                let columnsToUse = [{
                        data: 'created_at',
                        name: 'created_at',
                        className: 'hidden-column',
                        visible: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        className: "text-center",
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'status',
                        name: 'status',
                        className: "text-center",
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'applicantName',
                        name: 'applicantName',
                        defaultContent: '-'
                    },
                    {
                        data: 'needs',
                        name: 'needs',
                        className: "text-center",
                        defaultContent: '-'
                    },
                    {
                        data: 'position',
                        name: 'position',
                        defaultContent: '-'
                    },
                    {
                        data: 'employee_status',
                        name: 'employee_status',
                        defaultContent: '-'
                    },
                    {
                        data: 'department',
                        name: 'department',
                        defaultContent: '-'
                    },
                    {
                        data: 'section',
                        name: 'section',
                        defaultContent: '-'
                    },
                    {
                        data: 'area',
                        name: 'area',
                        defaultContent: '-'
                    },
                    {
                        data: 'reason',
                        name: 'reason',
                        defaultContent: '-'
                    },
                ];
                let orderIndex = 0;

                return $(tableDoneId).DataTable({
                    destroy: true,
                    stateSave: false,
                    responsive: false,
                    autoWidth: false,
                    processing: true,
                    serverSide: false,
                    scrollX: true,
                    order: [
                        [orderIndex, 'desc']
                    ],
                    ajax: {
                        url: "{{ route('recruitment.emp.er.done-er') }}",
                        error: function(xhr, error, thrown) {
                            console.error("DataTables AJAX Error (Done ER):", xhr.responseText);
                        }
                    },
                    columns: columnsToUse,
                    "drawCallback": function(settings) {
                        updateApproveBadge(); 
                    }
                });
            }

            initializeDefaultTab();

            $('#reset-process-er').on('click', function() {
                updateApproveBadge();
                if (tableProcess) {
                    selectedIds = [];
                    $('#checkAllProcessER').prop('checked', false);
                    updateMultiButtons();
                    tableProcess.ajax.reload(null, false);
                }
            });

            $('#reset-done-er').on('click', function() {
                if (tableDone) {
                    tableDone.ajax.reload(null, false);
                }
            });

            $.fn.dataTable.ext.errMode = 'none';
            $(document).on('error.dt', function(e, settings, techNote, message) {
                console.error('DataTables Error:', message);
            });

            let resizeTimer;
            $(window).on('resize', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust().draw();
                }, 300);
            });

            $('a[data-bs-toggle="tab"], button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
                let targetTab = $(e.target).attr('href');
                if (targetTab === '#pill-er-process') {
                    targetTab = '#pill-tab-process-requisition-content';
                } else if (targetTab === '#pill-selection-process') {
                    targetTab = '#pill-tab-process-selection-content';
                }
                currentTabId = targetTab;
                if (targetTab === '#pill-tab-process-requisition-content') {
                    if (!tableProcess) {
                        tableProcess = initializeProcessDataTable();
                    } else {
                        tableProcess.columns.adjust().draw();
                        tableProcess.ajax.reload(null, false);
                    }
                } else if (targetTab === '#pill-tab-done-requisition') {
                    if (!tableDone) {
                        tableDone = initializeDoneDataTable();
                    } else {
                        tableDone.columns.adjust().draw();
                        tableDone.ajax.reload(null, false);
                    }
                    selectedIds = [];
                    $('#multi-delete-btn').hide();
                } else if (targetTab === '#pill-tab-process-selection-content') {
                    if (!tableProcessSelection) {
                        tableProcessSelection = initializeProcessSelectionDataTable();
                    } else {
                        tableProcessSelection.columns.adjust().draw();
                        tableProcessSelection.ajax.reload(null, false);
                    }
                } else if (targetTab === '#pill-tab-done-selection') {
                    if (!tableDoneSelection) {
                        tableDoneSelection = initializeDoneSelectionDataTable();
                    } else {
                        tableDoneSelection.columns.adjust().draw();
                        tableDoneSelection.ajax.reload(null, false);
                    }
                } else if (targetTab === '#pill-result') {
                    if (!tableResult) {
                        tableResult = initializeResultDataTable();
                    } else {
                        tableResult.columns.adjust().draw();
                        tableResult.ajax.reload(null, false);
                    }
                }
            });

            const erStepsUrl = '{{ route('recruitment.emp.er.my-er.steps', ':id') }}';
            $(document).on('click', '.btn-view-steps', function(e) {
                e.preventDefault();
                var encryptedId = $(this).data('id');
                const url = erStepsUrl.replace(':id', encryptedId);
                $('#trackingModalLabel').text('Employee Requisition Approve');
                $('#trackingModal .modal-body .timeline').html(`
                            <div class="text-center py-5">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        `);
                $('#trackingModal').modal('show');
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        var timelineHtml = '';
                        $.each(response.steps, function(index, step) {
                            var completedClass = step.completed ? 'completed' : '';
                            var dateDisplay = step.date ?
                                `<small class="text-muted">${step.date}</small>` :
                                '<small class="text-muted">-</small>';
                            timelineHtml += `
                                    <div class="timeline-item ${completedClass}">
                                        <span class="timeline-line"></span>
                                        <div class="timeline-marker"></div>
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <h6 class="mb-0">${step.name}${step.approval}</h6>
                                                ${dateDisplay}
                                            </div>
                                        </div>
                                    </div>
                                `;
                        });
                        $('#trackingModal .modal-body .timeline').html(timelineHtml);
                    },
                    error: function(xhr) {
                        console.error('Error fetching data:', xhr.responseText);
                        $('#trackingModal .modal-body .timeline').html(
                            '<div class="alert alert-danger">Failed to load data. Please try again.</div>'
                        );
                    }
                });
            });

            $('#multi-delete-btn').click(function() {
                const message =
                    `Are you sure you want to delete ${selectedIds.length} selected requisitions?`;
                $('#deleteMessage').text(message);
                const idContainer = $('#delete-id-container');
                idContainer.empty();
                selectedIds.forEach(id => {
                    idContainer.append(`<input type="hidden" name="ids[]" value="${id}">`);
                });
                $('#deleteConfirmationModal').modal('show');
            });

            $(document).on("click", ".delete-btn", function() {
                var evalId = $(this).data("id");
                $('#deleteMessage').text('Are you sure you want to delete this requisition?');
                const idContainer = $('#delete-id-container');
                idContainer.empty();
                idContainer.append(`<input type="hidden" name="ids[]" value="${evalId}">`);
                $('#deleteConfirmationModal').modal("show");
            });

            $("#universalDeleteForm").on('submit', function() {
                $('#deleteConfirmationModal').modal('hide');
                $('#staticBackdrop').modal('show');
            });

            const reasonUrl = '{{ route('recruitment.emp.er.reason', ':id') }}';
            $(document).on('click', '.btn-reason', function(e) {
                e.preventDefault();
                var encryptedId = $(this).data('id');
                const url = reasonUrl.replace(':id', encryptedId);
                const modal = $('#reasonModal');
                const contentContainer = $('#decisionReasonContent');
                contentContainer.html(`
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                `);
                modal.modal('show');
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        if (response.reason) {
                            contentContainer.html(response.reason);
                        } else {
                            contentContainer.text('Reason not available.');
                        }
                    },
                    error: function(xhr) {
                        contentContainer.html('<span class="text-danger">Failed to load reason.</span>');
                        console.error('AJAX Error:', xhr.responseText);
                    }
                });
            });

            // Selection
            function updateReviewBadge() {
                $.ajax({
                    url: "{{ route('recruitment.emp.selection.process.count') }}",
                    method: 'GET',
                    success: function(response) {
                        const count = response.jml_review || 0;
                        const $badgeContainer = $('#review-badge-container');
                        $('#review-count').text(count);
                        if (count > 0) {
                            $badgeContainer.show();
                        } else {
                            $badgeContainer.hide();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Failed to update review count:", error);
                        $('#review-badge-container').hide();
                    }
                });
            }
            updateReviewBadge();

            function initializeProcessSelectionDataTable() {
                if ($.fn.DataTable.isDataTable(tableProcessSelectionId)) {
                    $(tableProcessSelectionId).DataTable().destroy();
                }

                let columnsToUse = [
                    {data: 'created_at',name: 'created_at',className: 'hidden-column',visible: false,searchable: false,},
                    {data: 'action',name: 'action',className: "text-center",orderable: false,searchable: false,defaultContent: '-'},
                    {data: 'status',name: 'status',className: "text-center",orderable: false,searchable: false,defaultContent: '-'},
                    {data: 'requisition',name: 'requisition',defaultContent: '-'}, 
                    {data: 'selection',name: 'selection',defaultContent: '-'}, 
                    {data: 'noted',name: 'noted',defaultContent: '-'},
                    {data: 'participant',name: 'participant',defaultContent: '-'},
                    {data: 'schedule',name: 'schedule',defaultContent: '-'},
                    {data: 'location',name: 'location',defaultContent: '-'},
                    {data: 'passed',name: 'passed',defaultContent: '-'},
                ];
                let orderIndex = 0;

                return $(tableProcessSelectionId).DataTable({
                    destroy: true,
                    stateSave: false,
                    responsive: false,
                    autoWidth: false,
                    processing: true,
                    serverSide: false,
                    scrollX: true,
                    order: [
                        [orderIndex, 'desc']
                    ],
                    ajax: {
                        url: "{{ route('recruitment.emp.selection.process') }}", 
                        error: function(xhr, error, thrown) {
                            console.error("DataTables AJAX Error (Process Selection):", xhr.responseText);
                        }
                    },
                    columns: columnsToUse,
                    "drawCallback": function(settings) {
                        updateReviewBadge(); 
                    }
                });
            }

            function initializeDoneSelectionDataTable() {
                if ($.fn.DataTable.isDataTable(tableDoneSelectionId)) {
                    $(tableDoneSelectionId).DataTable().destroy();
                }
                let columnsToUse = [
                    {data: 'created_at',name: 'created_at',className: 'hidden-column',visible: false,searchable: false,},
                    {data: 'action',name: 'action',className: "text-center",orderable: false,searchable: false,defaultContent: '-'},
                    {data: 'requisition',name: 'requisition',defaultContent: '-'}, 
                    {data: 'selection',name: 'selection',defaultContent: '-'}, 
                    {data: 'noted',name: 'noted',defaultContent: '-'},
                    {data: 'participant',name: 'participant',defaultContent: '-'},
                    {data: 'schedule',name: 'schedule',defaultContent: '-'},
                    {data: 'location',name: 'location',defaultContent: '-'},
                    {data: 'passed',name: 'passed',defaultContent: '-'},
                ];
                let orderIndex = 0;

                return $(tableDoneSelectionId).DataTable({
                    destroy: true,
                    stateSave: false,
                    responsive: false,
                    autoWidth: false,
                    processing: true,
                    serverSide: false,
                    scrollX: true,
                    order: [
                        [orderIndex, 'desc']
                    ],
                    ajax: {
                        url: "{{ route('recruitment.emp.selection.done') }}",
                        error: function(xhr, error, thrown) {
                            console.error("DataTables AJAX Error (Done Selection):", xhr.responseText);
                        }
                    },
                    columns: columnsToUse,
                    "drawCallback": function(settings) {
                        updateReviewBadge(); 
                    }
                });
            }

            $('#reset-process-selection').on('click', function() {
                updateReviewBadge();
                if (tableProcessSelection) {
                    tableProcessSelection.ajax.reload(null, false);
                }
            });

            $('#reset-done-selection').on('click', function() {
                if (tableDoneSelection) {
                    tableDoneSelection.ajax.reload(null, false);
                }
            });

            function initializeResultDataTable() {
                if ($.fn.DataTable.isDataTable(tableResultId)) {
                    $(tableResultId).DataTable().destroy();
                }
                let columnsToUse = [
                    { data: 'created_at', name: 'created_at', className: 'hidden-column', visible: false },
                    { data: 'action', name: 'action', className: "text-center", orderable: false, searchable: false },
                    { data: 'status', name: 'status', className: "text-center", orderable: false, searchable: false },
                    { data: 'requisition', name: 'requisition', defaultContent: '-' },
                    { data: 'needs', name: 'needs', defaultContent: '-' },
                    { data: 'fulfilled', name: 'fulfilled', defaultContent: '-' },
                    { data: 'employee_status', name: 'employee_status', defaultContent: '-' },
                    { data: 'reason', name: 'reason', defaultContent: '-' }
                ];
                let orderIndex = 0;

                return $(tableResultId).DataTable({
                    destroy: true,
                    stateSave: false,
                    responsive: false,
                    autoWidth: false,
                    processing: true,
                    serverSide: false,
                    scrollX: true,
                    order: [
                        [orderIndex, 'desc']
                    ],
                    ajax: {
                        url: "{{ route('recruitment.emp.result') }}",
                        error: function(xhr, error, thrown) {
                            console.error("DataTables AJAX Error (Done Selection):", xhr.responseText);
                        }
                    },
                    columns: columnsToUse,
                });
            }

            $('#reset-result').on('click', function() {
                if (tableResult) {
                    tableResult.ajax.reload(null, false);
                }
            });
        });
    </script>
    <script>
        @if (Session::has('success'))
            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-bottom-right"
            }
            toastr.success("{{ session('success') }}");
        @endif
        @if (Session::has('error'))
            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-bottom-right"
            }
            toastr.error("{{ session('error') }}");
        @endif
        @if (Session::has('status'))
            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-bottom-right"
            }
            toastr.success("{{ session('status') }}");
        @endif
    </script>
@endsection