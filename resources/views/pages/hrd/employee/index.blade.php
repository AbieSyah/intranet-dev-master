@extends('layouts.master')
@section('link')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ url('') }}/assets/libs/Datatables/DataTables-1.13.1/css/dataTables.bootstrap5.css" rel="stylesheet"
        type="text/css" />
    <link href="{{ url('') }}/assets/libs/Datatables/Buttons-2.3.3/css/buttons.dataTables.css" rel="stylesheet"
        type="text/css" />
    <link href="{{ url('') }}/assets/libs/Datatables/Responsive-2.4.0/css/responsive.bootstrap.css" rel="stylesheet"
        type="text/css" />
    <!-- Select2-->
    <link rel="stylesheet" href="{{ url('') }}/assets/libs/adminlte/select2/css/4.1.0/select2.min.css">
    <style>
        div.dataTables_wrapper {
            width: 100%;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">List Employee</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Employee</a></li>
                        <li class="breadcrumb-item active">List</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>
    <!--end row-->

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex justify-content-between">
                    <div class="col-md-2 p-2">
                        <div class="form-group">
                            <select class="form-control select2" id="form_status" name="form_status">
                                <option value="ALL" selected>All</option>
                                <option value="PERMANENT">Permanent</option>
                                <option value="CONTRACT">Contract</option>
                                <option value="PROBATION">Probation</option>
                                <option value="OUTSOURCING">Outsourcing</option>
                                <option value="TERMINATED">Terminated</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <button type="button" name="filter" id="filter"
                            class="btn btn-soft-primary waves-effect waves-light btn-sm"><i
                                class="ri-filter-2-line me-1 align-bottom"></i> Filters</button>
                        <button type="button" name="refresh" id="refresh"
                            class="btn btn-soft-danger waves-effect waves-light btn-sm"><i
                                class="ri-refresh-line me-1 align-bottom"></i> Reset</button>
                    </div>
                    <div class="col-md-5">
                        @can('hrd.employee.create')
                            <a href="{{ route('employee.form') }}"
                                class="float-end btn btn-primary btn-label waves-effect waves-light"
                                data-text="Add Employee"><i class="ri-add-circle-line label-icon align-middle fs-16 me-2">
                                </i>Add Employee</a>
                        @endcan
                        <button type="button" class="float-end btn btn-success btn-label waves-effect waves-light me-2"
                            data-text="Export" data-bs-toggle="modal" data-bs-target="#modal"><i
                                class="ri-file-excel-2-line label-icon align-middle fs-16 me-2"></i>Import</a>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-striped bordered display nowrap" style="width:100%" id="table">
                        <thead>
                            <tr>
                                <th scope="col"></th>
                                <th scope="col">NIK</th>
                                <th scope="col">FULL NAME</th>
                                <th scope="col">AREA</th>
                                <th scope="col">DEPT</th>
                                <th scope="col">SECTION</th>
                                <th scope="col">POSITION</th>
                                <th scope="col">SERVICE YEAR</th>
                                <th scope="col">STATUS</th>
                                <th scope="col">ACTION</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!--end row-->

    {{-- Modal Export --}}
    <div class="modal fade" id="modal-export-status" tabindex="-1" aria-labelledby="exportStatusLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exportStatusLabel">Export Employee Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="fw-semibold text-muted">Select employee statuses to export :</p>
                    <form id="form-export-status">
                        <div class="row g-2">
                            <div class="col-12">
                                <div class="form-check card-radio">
                                    <input class="form-check-input status-export-checkbox" type="checkbox" value="PERMANENT" id="exp_permanent" checked>
                                    <label class="form-check-label" for="exp_permanent">Permanent</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-check card-radio">
                                    <input class="form-check-input status-export-checkbox" type="checkbox" value="CONTRACT" id="exp_contract" checked>
                                    <label class="form-check-label" for="exp_contract">Contract</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-check card-radio">
                                    <input class="form-check-input status-export-checkbox" type="checkbox" value="PROBATION" id="exp_probation" checked>
                                    <label class="form-check-label" for="exp_probation">Probation</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-check card-radio">
                                    <input class="form-check-input status-export-checkbox" type="checkbox" value="OUTSOURCING" id="exp_outsourcing" checked>
                                    <label class="form-check-label" for="exp_outsourcing">Outsourcing</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-check card-radio">
                                    <input class="form-check-input status-export-checkbox" type="checkbox" value="TERMINATED" id="exp_terminated">
                                    <label class="form-check-label" for="exp_terminated">Terminated</label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="btn-execute-export" class="btn btn-success">
                        <i class="ri-file-excel-2-line me-1 align-bottom"></i> Download Excel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!--Modal Import -->
    <div class="modal fade" id="modal" tabindex="-1" aria-labelledby="exampleModalgridLabel" aria-modal="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalgridLabel">Import Data from Excel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="form" action="{{ route('employee.import') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        @method('POST')
                        <div class="row g-3">
                            <div class="col-12">
                                <div>
                                    <input type="file" class="form-control" name="file_xls" id="file_xls"
                                        accept=".xls, .xlsx" required>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="hstack gap-2 justify-content-end">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" id="btn-save" class="btn btn-success">Import</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!--Modal staticbackdrop-->
    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body text-center p-5">
                    <img src="/assets/images/loading.gif" style="width:120px;height:120px">
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
    <!-- Datatables -->
    <script src="{{ url('') }}/assets/libs/Datatables/DataTables-1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="{{ url('') }}/assets/libs/Datatables/DataTables-1.13.1/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ url('') }}/assets/libs/Datatables/Responsive-2.4.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="{{ url('') }}/assets/js/pages/datatables.init.js"></script>
    <!-- Select2 -->
    <script src="{{ url('') }}/assets/libs/adminlte/select2/js/4.1.0/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection

@section('javascript')
    <script type="text/javascript">
        $(function() {
            $('.select2').select2()
        });
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            load_data();
            function load_data(form_status = '') {
                $('#table').DataTable({
                    stateSave: true,
                    responsive: false,
                    autoWidth: false,
                    processing: true,
                    serverSide: true,
                    scrollX: true,
                    dom: '<"row"<"col-12 mb-2"B><"col-12 d-flex justify-content-between"lf>>rtip',
                    buttons: [
                        {
                            extend: 'excel',
                            text: '<i class="ri-file-excel-2-line me-1"></i> Export',
                            action: function (e, dt, node, config) {
                                $('#modal-export-status').modal('show');
                            }
                        }
                    ],
                    ajax: {
                        url: "{{ route('employee.index') }}",
                        data: {
                            form_status: form_status
                        }
                    },
                    columns: [
                        {
                            data: 'avatar',
                            name: 'avatar',
                            className: "text-center",
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'nik',
                            name: 'nik',
                            defaultContent: '-'
                        },
                        {
                            data: 'fullname',
                            name: 'fullname',
                            defaultContent: '-'
                        },
                        {
                            data: 'area_kode',
                            name: 'area.kode',
                            defaultContent: '-'
                        },
                        {
                            data: 'department_name',
                            name: 'department.name',
                            defaultContent: '-'
                        },
                        {
                            data: 'section_nama',
                            name: 'section.nama',
                            defaultContent: '-'
                        },
                        {
                            data: 'position_nama',
                            name: 'position.nama',
                            defaultContent: '-'
                        },
                        {
                            data: 'service_year',
                            name: 'service_year',
                        },
                        {
                            data: 'status',
                            name: 'status',
                            className: "text-center",
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'action',
                            name: 'action',
                            className: "text-center",
                            orderable: false,
                            searchable: false
                        },
                    ],
                    order: [[1, 'asc']]
                });
            }
            $.fn.dataTable.ext.errMode = 'none';
            $(document).on('error.dt', function(e, settings, techNote, message) {
                console.error('DataTables Error:', message);
            });
            $('#filter').click(function() {
                var form_status = $('#form_status').val();
                if (form_status != '') {
                    $('#table').DataTable().destroy();
                    load_data(form_status);
                }
            });
            $('#refresh').click(function() {
                $('#form_status').val('ALL').trigger('change');
                var table = $('#table').DataTable();
                table.state.clear();
                table.destroy();
                load_data();
            });
            const sidebarToggleBtn = $('#topnav-hamburger-icon'); 
            if (sidebarToggleBtn.length) {
                sidebarToggleBtn.on('click', function() {
                    setTimeout(function() {
                        $('#table').DataTable().columns.adjust().draw();
                    }, 300);
                });
            }

            // Export
            $('#btn-execute-export').click(function() {
                let selectedStatuses = [];
                $('.status-export-checkbox:checked').each(function() {
                    selectedStatuses.push($(this).val());
                });
                if (selectedStatuses.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Selection Required',
                        text: 'Please select at least one employee status to export!',
                        buttonsStyling: false,
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        }
                    });
                    return;
                }
                let statusQueryString = selectedStatuses.join(',');
                $('#modal-export-status').modal('hide');
                window.location.href = "{{ route('employee.export') }}?form_status=" + encodeURIComponent(statusQueryString);
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        });
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            let swalert;
            $("form").submit(function(e) {
                e.preventDefault();
                swalert = Swal.fire({
                    title: 'Please wait...',
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
                    if (Array.isArray(responseJson.responseText)) {
                        errorMessage += `<ul class="text-danger text-start">`;
                        responseJson.responseText.forEach(errorText => {
                            errorMessage += `<li>${errorText}</li>`;
                        });
                        errorMessage += `</ul>`;
                    } 
                    else if (typeof responseJson.responseText === 'string') {
                        errorMessage += `<p class="text-danger">${responseJson.responseText}</p>`;
                    }
                }
                if (errorMessage === '') {
                    errorMessage += '<p class="text-danger">An error occurred.</p>';
                }
                swalert.update({
                    title: 'Error',
                    html: errorMessage,
                    icon: 'error',
                    buttonsStyling: false,
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    }
                });
            }
        });
    </script>
@endsection
