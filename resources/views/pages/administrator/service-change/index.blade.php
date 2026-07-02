@extends('layouts.master')

@section('link')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css" />
    <link href="{{ url('') }}/assets/libs/Datatables/DataTables-1.13.1/css/dataTables.bootstrap5.css" rel="stylesheet"
        type="text/css" />
    <link href="{{ url('') }}/assets/libs/Datatables/Buttons-2.3.3/css/buttons.dataTables.css" rel="stylesheet"
        type="text/css" />
    <link href="{{ url('') }}/assets/libs/Datatables/Responsive-2.4.0/css/responsive.bootstrap.css" rel="stylesheet"
        type="text/css" />
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">List Change Management</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Change Management</a></li>
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
                        <h6 class="mb-0 text-white">New Change(Today)</h6>
                        <h3 class="mb-0 text-white">{{ $newServiceChangeCount }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-warning text-dark p-3">
                <div class="d-flex align-items-center">
                    <i class="ri-time-line fs-1 text-white opacity-50"></i>
                    <div class="ms-3">
                        <h6 class="mb-0 text-white">Pending Approval</h6>
                        <h3 class="mb-0 text-white">{{ $proposedServiceChangeCount }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-info p-3">
                <div class="d-flex align-items-center">
                    <i class="ri-lightbulb-line fs-1 opacity-50 text-white"></i>
                    <div class="ms-3">
                        <h6 class="mb-0 text-white">Approved</h6>
                        <h3 class="mb-0 text-white">{{ $inProgressServiceChangeCount }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-success p-3">
                <div class="d-flex align-items-center">
                    <i class="ri-checkbox-circle-line fs-1 opacity-50 text-white"></i>
                    <div class="ms-3">
                        <h6 class="mb-0 text-white">Done(Today)</h6>
                        <h3 class="mb-0 text-white">{{ $doneTodayServiceChangeCount }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <table class="table table-hover align-middle w-100" id="ticketTable">
                        <thead class="table-light">
                            <tr>
                                <th>Change ID</th>
                                <th>Ticket ID</th>
                                <th>Subject</th>
                                <th>Source</th>
                                <th>Change Type</th>
                                <th>Current Status</th>
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
    <!-- Sweetalert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
@endsection

@section('javascript')
    <script>
        $(document).ready(function() {
            $('#ticketTable').DataTable({
                responsive: false,
                processing: true,
                serverSide: false,
                scrollX: true,
                stateSave: true,
                ajax: {
                    url: "{{ route('service-change.data', 'all') }}",
                    data: function(d) {
                        d.my = $('#filter-my-ticket').is(':checked'); // Contoh parameter tambahan
                        d.filter = $('#status-filter').val();
                    },
                },
                columns: [{
                        data: 'change_no',
                        render: function(data, type, row) {
                            return `<span class="fw-bold text-primary text-nowrap">#${data}</span><br>
                                 <small class="text-muted">${row.created_at_formatted.display}</small>`;
                        }
                    },
                    {
                        data: 'ticket.no_ticket',
                        render: function(data, type, row) {
                            return `<span class="fw-bold text-primary text-nowrap">#${data}</span><br>
                                 <small class="text-muted">${row.ticket_created_at_formatted.display}</small>`;
                        }
                    },
                    {
                        data: 'ticket.subject'
                    },
                    {
                        data: 'ticket.type',
                        render: function(data) {
                            let color = 'bg-secondary';
                            if (data === 'incident') color = 'bg-danger';
                            else if (data === 'request') color = 'bg-info';
                            else if (data === 'change') color = 'bg-warning text-dark';
                            else if (data === 'initiative') color = 'bg-success';

                            return `<span class="badge ${color} text-capitalize">${data?? "Unassigned"}</span>`;
                        }
                    },
                    {
                        data: 'change_type',
                        render: function(data) {
                            let color = 'bg-secondary';
                            if (data === 'standard') color = 'bg-primary';
                            else if (data === 'normal') color = 'bg-info';
                            else if (data === 'emergency') color = 'bg-danger';

                            return `<span class="badge ${color} text-capitalize">${data?? "Unassigned"}</span>`;
                        }
                    },
                    {
                        data: 'status',
                        render: function(data) {
                            let color = 'bg-secondary';
                            let status = data ? data.toLowerCase() : '';

                            if (status === 'proposed') color = 'bg-warning text-dark';
                            else if (status === 'rejected') color = 'bg-danger text-white';
                            else if (status === 'approved') color = 'bg-success text-white';

                            return `<span class="badge ${color} text-uppercase">${status}</span>`;
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        render: function(data, type, row) {
                            let actionItem = '';

                            // Cek apakah server mengirimkan main_action
                            // if (row.main_action) {
                            //    actionItem = `
                        //       <div class="text-center">
                        //          <a class="btn btn-sm ${row.main_action.class}" href="${row.main_action.url?? `/administrator/service-desk/${row.encrypted_id}/{{ encrypt('it') }}`}">
                        //             <i class="${row.main_action.icon} me-2"></i>${row.main_action.label}
                        //          </a>
                        //       </div>`;
                            // }

                            // return actionItem;
                            return `
                           <a class="btn btn-sm ${row.detail_label === 'View' ?  'btn-outline-primary' : (row.detail_label === 'Review Change' ? 'btn-success' : 'btn-warning text-dark') }" href="${row.detail_url}">
                              <i class="ri-eye-line me-2"></i>${row.detail_label}
                           </a>
                        `
                        }
                    }
                ],
                order: [
                    [0, 'desc']
                ]
            });
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
