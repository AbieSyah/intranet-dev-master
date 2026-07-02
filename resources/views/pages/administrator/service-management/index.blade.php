@extends('layouts.master')

@section('link')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ url('') }}/assets/libs/Datatables/DataTables-1.13.1/css/dataTables.bootstrap5.css" rel="stylesheet"
        type="text/css" />
    <link href="{{ url('') }}/assets/libs/Datatables/Buttons-2.3.3/css/buttons.dataTables.css" rel="stylesheet"
        type="text/css" />
    <link href="{{ url('') }}/assets/libs/Datatables/Responsive-2.4.0/css/responsive.bootstrap.css" rel="stylesheet"
        type="text/css" />
    <link rel="stylesheet" href="{{ url('') }}/assets/libs/adminlte/select2/css/4.1.0/select2.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">List IT Service Management</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">IT Service Management</a></li>
                        <li class="breadcrumb-item active">List</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-primary p-3">
                <div class="d-flex align-items-center">
                    <i class="ri-ticket-2-line fs-1 text-white opacity-50"></i>
                    <div class="ms-3">
                        <h6 class="mb-0 text-white">New Tickets(Today)</h6>
                        <h3 class="mb-0 text-white">{{ $newTicketsCount }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-warning text-dark p-3">
                <div class="d-flex align-items-center">
                    <i class="ri-time-line fs-1 text-white opacity-50"></i>
                    <div class="ms-3">
                        <h6 class="mb-0 text-white">Open Tickets</h6>
                        <h3 class="mb-0 text-white">{{ $openTicketsCount }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-info p-3">
                <div class="d-flex align-items-center">
                    <i class="ri-lightbulb-line fs-1 opacity-50 text-white"></i>
                    <div class="ms-3">
                        <h6 class="mb-0 text-white">In Progress</h6>
                        <h3 class="mb-0 text-white">{{ $inProgressTicketsCount }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-success p-3">
                <div class="d-flex align-items-center">
                    <i class="ri-checkbox-circle-line fs-1 opacity-50 text-white"></i>
                    <div class="ms-3">
                        <h6 class="mb-0 text-white">Closed(Today)</h6>
                        <h3 class="mb-0 text-white">{{ $closedTodayTicketsCount }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">Ticket Submission List</h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('service-management.closed') }}" class="btn btn-outline-danger px-3">
                            Closed Tickets
                        </a>
                        <a href="{{ route('service-management.initiate') }}"
                            class="float-end btn btn-primary btn-label waves-effect waves-light"
                            data-text="Create IT Initiative">
                            <i class="ri-add-circle-line label-icon align-middle fs-16 me-2"></i>
                            Create IT Initiative
                        </a>
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                More Menu
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('service-catalog.index') }}">Service
                                        Catalog</a>
                                </li>
                                <li><a class="dropdown-item" href="{{ route('priority.index') }}">ITSM Priority</a></li>
                                <li><a class="dropdown-item" href="{{ route('priority-metric.index') }}">Priority
                                        Metrics</a>
                                </li>
                                <li><a class="dropdown-item" href="{{ route('risk-register.index') }}">Risk Register</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-hover align-middle w-100" id="ticketTable">
                        <thead class="table-light">
                            <tr>
                                <th>Ticket ID</th>
                                <th>Subject</th>
                                <th>User</th>
                                <th>Type</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <!-- Datatables -->
    <script src="/assets/libs/Datatables/DataTables-1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="/assets/libs/Datatables/DataTables-1.13.1/js/dataTables.bootstrap5.min.js"></script>
    <script src="/assets/libs/Datatables/Responsive-2.4.0/js/dataTables.responsive.min.js"></script>
    <script src="/assets/js/pages/datatables.init.js"></script>
    <!-- Select2 -->
    <script src="/assets/libs/adminlte/select2/js/4.1.0/select2.min.js"></script>
    <!-- Sweetalert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
@endsection

@section('javascript')
    <script>
        $(document).ready(function() {
            $('.select2').select2()
        });

        // ------------- Initialize Flatpickr (Matching the YYYY-MM-DD format in your table) -------------
        $("#reg-date").flatpickr({
            allowInput: true,
            altInput: false,
            dateFormat: "Y-M-d",
            defaultDate: new Date(),
        });
    </script>

    <script>
        $(document).ready(function() {
            const priorityColorMap = @json($priorityColorMap);

            $('#ticketTable').DataTable({
                processing: true,
                responsive: false,
                serverSide: false,
                scrollX: true,
                stateSave: true,
                ajax: {
                    url: "{{ route('service-ticket.data') }}",
                    data: function(d) {
                        d.my = $('#filter-my-ticket').is(':checked'); // Contoh parameter tambahan
                        // d.filter = $('#status-filter').val();
                        d.filter = 'open';
                        d.all = true;
                    }
                },
                columns: [{
                        data: 'no_ticket',
                        render: function(data, type, row) {
                            return `<span class="fw-bold text-primary text-nowrap">#${data}</span><br>
                              <small class="text-muted">${row.created_at_formatted.display}</small>`;
                        }
                    },
                    {
                        data: 'subject'
                    },
                    {
                        data: 'submitter',
                        render: function(data, type, row) {
                            return `${data.fullname} (${data.nik})`;
                        }
                    },
                    {
                        data: 'type',
                        render: function(data) {
                            let color = 'bg-secondary';
                            if (data === 'incident') color = 'bg-danger';
                            else if (data === 'request') color = 'bg-info';
                            else if (data === 'change') color = 'bg-warning text-dark';
                            else if (data === 'it_initiative') color = 'bg-success';

                            return `<span class="badge ${color} text-capitalize">${data?? "Unassigned"}</span>`;
                        }
                    },
                    {
                        data: 'total_score',
                        type: 'num',
                        render: function(data, type, row) {
                            let color = null;
                            let priority = 'Unassigned';

                            Object.entries(priorityColorMap).forEach(function([key, value]) {
                                if (data >= value.min_score && data <= value.max_score) {
                                    color = value.color;
                                    priority = key;
                                }
                            });

                            if (type === 'sort') {
                                return parseInt(data);
                            }

                            return `<span class="badge text-capitalize" style="background-color: ${color || '#3577f1'};">${data >= 99999999 ? 'N/A' : data} (${priority})</span>`;
                        }
                    },
                    {
                        data: 'current_status',
                        render: function(data) {
                            let color = 'bg-secondary';
                            let status = data ? data.toLowerCase() : '';

                            if (status === 'closed') color = 'bg-success';
                            else if (status === 'open') color = 'bg-warning text-dark';
                            else if (status === 'process') color = 'bg-primary';
                            else if (status === 'hold') color = 'bg-dark';

                            return `<span class="badge ${color} text-uppercase">${status}</span>`;
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        render: function(data, type, row) {
                            let actionItem = '';

                            if (row.main_action) {
                                actionItem = `
                           <div class="text-center">
                              <a class="btn btn-sm ${row.main_action.class}" href="${row.main_action.url?? `/administrator/service-desk/${row.encrypted_id}/{{ encrypt('it') }}`}">
                                 <i class="${row.main_action.icon} me-2"></i>${row.main_action.label}
                              </a>
                           </div>`;
                            }

                            return actionItem;
                            // <li>
                            //    <a class="dropdown-item" href="${row.view_url}">
                            //       <i class="ri-eye-line me-2"></i>View Details
                            //    </a>
                            // </li>
                        }
                    }
                ],
                order: [
                    [4, 'desc']
                ],
            });

            // Toggle Replace Asset button visibility based on Category selection
            $('#ticketType').on('change', function() {
                if ($(this).val() === 'change') {
                    $('#replaceAssetBtn').fadeIn();
                } else {
                    $('#replaceAssetBtn').fadeOut();
                }
            });

            // Quick Fix Logic
            function sendQuickFix() {
                Swal.fire({
                    title: 'Confirm Quick Fix?',
                    text: 'This will send a "Fixed" notification to the reporter and close this ticket.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, resolve it',
                    confirmButtonColor: '#198754',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Logic to send AJAX message "item is fixed, please check accordingly"
                        Swal.fire('Closed!', 'Message sent and ticket resolved.', 'success');
                    }
                });
            }
        })
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
    </script>
@endsection
