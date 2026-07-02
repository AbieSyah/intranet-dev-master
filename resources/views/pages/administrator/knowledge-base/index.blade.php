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
                <h4 class="mb-sm-0">List Knowledge Base</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Knowledge Base</a></li>
                        <li class="breadcrumb-item active">List</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">Ticket Submission List</h5>
                    <div class="d-flex gap-2">
                        <a class="btn btn-primary px-3" href="{{ route('knowledge-base.create') }}">
                            <i class="ri-add-line me-1"></i> Create New Knowledge Base
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-hover w-100 align-middle" id="knowledgeBase">
                        <thead class="table-light">
                            <tr>
                                <th>No.</th>
                                <th>Title</th>
                                <th>Author</th>
                                <th>Status</th>
                                <th>Level</th>
                                <th>Created At</th>
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
        function loadDatatable() {
            $('#knowledgeBase').DataTable({
                processing: true,
                responsive: false,
                serverSide: false,
                scrollX: true,
                stateSave: true,
                ajax: {
                    url: "{{ route('knowledge-base.data', 'all') }}",
                    data: function(d) {
                        d.my = $('#filter-my-ticket').is(':checked'); // Contoh parameter tambahan
                        d.filter = $('#status-filter').val();
                    }
                },
                columns: [{
                        data: null,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'title',
                    },
                    {
                        data: 'author_name',
                    },
                    {
                        data: 'status',
                        render: function(data, type, row) {
                            let badgeClass = '';
                            switch (data) {
                                case 'draft':
                                    badgeClass = 'bg-secondary';
                                    break;
                                case 'published':
                                    badgeClass = 'bg-success';
                                    break;
                                case 'archived':
                                    badgeClass = 'bg-danger';
                                    break;
                            }
                            return `<span class="badge ${badgeClass}">${data.charAt(0).toUpperCase() + data.slice(1)}</span>`;
                        }
                    },
                    {
                        data: 'level',
                        render: function(data, type, row) {
                            let badgeClass = '';
                            switch (data) {
                                case 'private':
                                    badgeClass = 'bg-secondary';
                                    break;
                                case 'some_employees':
                                    badgeClass = 'bg-warning';
                                    break;
                                case 'all_employees':
                                    badgeClass = 'bg-success';
                                    break;
                            }
                            return `<span class="badge ${badgeClass}">${data.replace('_', ' ').toUpperCase()}</span>`;
                        }
                    },
                    {
                        data: 'formated_created_at',
                    },
                    {
                        data: null,
                        orderable: false,
                        render: function(data, type, row) {
                            actionButtons = `
                        <div class="dropdown text-center">
                           <button class="btn btn-sm btn-secondary" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                              <i class="ri-more-2-line"></i>
                           </button>
                           <ul class="dropdown-menu">
                              <li><a class="dropdown-item" target="_blank" href="${row.view_url}?preview=true"><i class="ri-eye-line me-2"></i>${row.status == "draft"? 'Preview' : 'View'}</a></li>
                              <li><a class="dropdown-item" href="${row.edit_url}"><i class="ri-edit-line me-2"></i>Edit Knowledge Base</a></li>
                              <li><hr class="dropdown-divider"></li>
                              <li><button class="dropdown-item text-danger delete-btn" data-url="${row.delete_url}"><i class="ri-delete-bin-line me-2"></i>Delete</button></li>
                           </ul>
                        </div>
                     `

                            return actionButtons
                        }
                    }
                ]
            });
        }

        $(document).ready(function() {
            loadDatatable();

            $(document).on('click', '.delete-btn', function() {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: $(this).data('url'),
                            type: 'DELETE',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                $('#knowledgeBase').DataTable().ajax.reload();
                                Swal.fire(
                                    'Deleted!',
                                    response.message,
                                    'success'
                                )
                            },
                            error: function(xhr) {
                                Swal.fire(
                                    'Error!',
                                    'An error occurred while deleting the Knowledge Base.',
                                    'error'
                                )
                            }
                        });
                    }
                })
            });
        });
    </script>
@endsection
