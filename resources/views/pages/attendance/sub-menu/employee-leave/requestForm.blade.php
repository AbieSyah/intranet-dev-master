@extends('layouts.master')

@section('link')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link href="/assets/libs/Datatables/DataTables-1.13.1/css/dataTables.bootstrap5.css" rel="stylesheet" type="text/css" />
<link href="/assets/libs/Datatables/Buttons-2.3.3/css/buttons.dataTables.css" rel="stylesheet" type="text/css" />
<link href="/assets/libs/Datatables/Responsive-2.4.0/css/responsive.bootstrap.css" rel="stylesheet" type="text/css" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4>Form Leave</h4>
        </div>
    </div>
</div>

<form id="leaveFormHRD" action="{{ route('employee-leave.leave-hrd-store') }}" method="POST">
@csrf
<div class="row">
    <input type="hidden" name="employees" id="employees_input">
    <div class="card">
        <div class="container-fluid">
            <!-- HEADER -->
            <div class="card mb-3">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('attendance-permit.index') }}" class="btn btn-primary btn-label waves-effect waves-light">
                            <i class="ri-arrow-left-fill label-icon align-middle fs-16 me-2"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">

                        <div class="col-md-4">
                            <label>Leave Type</label>
                            <select name="leave_type" id="leave_type" class="form-select">
                                <option value="pribadi" selected>Pribadi</option>
                                <option value="normatif">Normatif</option>
                            </select>
                        </div>

                    </div>
                </div>
            </div>
            <!-- ================= GLOBAL TABLE ================= -->
            <div class="card mb-3">
                <div class="card-body">
                    <!-- FILTER -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <select class="form-select select2" id="department_id">
                                <option value="ALL">All Department</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department }}">{{ $department }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select class="form-select select2" id="area_id">
                                <option value="ALL">All Area</option>
                                @foreach($areas as $area)
                                    <option value="{{ $area }}">{{ $area }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select class="form-select select2" id="position_id">
                                <option value="ALL">All Position</option>
                                @foreach($positions as $position)
                                    <option value="{{ $position }}">{{ $position }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <!-- TABLE -->
                    <div class="table-responsive">
                        <table class="table table-bordered" id="table_employee">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%"><input type="checkbox" id="select_all"></th>
                                    <th class="text-center">NIK</th>
                                    <th class="text-center">Name</th>
                                    <th class="text-center">Position</th>
                                    <th class="text-center">Area</th>
                                    <th class="text-center">Department</th>
                                    <th class="text-center">Leave Balance</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    <div class="mt-3">
                        <p id="selected_count">0 Employee selected</p>
                    </div>
                </div>

                <!-- ============================= SECTION PRIBADI ============================== -->
                <div id="section-pribadi" >
                    <div class="card-body row">
                        <div class="col-md-6">
                            <label>Start Date</label>
                            <div class="input-group">
                                <input type="text" name="start_date_pribadi" class="form-control bulan start_date_pribadi" placeholder="Pilih Tanggal">
                                <span class="input-group-text"><i class="ri-calendar-event-line"></i></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label>End Date</label>
                            <div class="input-group">
                                <input type="text" name="end_date_pribadi" class="form-control bulan end_date_pribadi" placeholder="Pilih Tanggal">
                                <span class="input-group-text"><i class="ri-calendar-event-line"></i></span>
                            </div>
                        </div>
                        <div class="mt-3">
                            <label>Notes</label>
                            <textarea name="notes_pribadi" class="form-control" placeholder="optional"></textarea>
                        </div>
                    </div>
                </div>

                <!-- ============================= SECTION NORMATIF ============================== -->
                <div id="section-normatif" style="display:none;">
                    <div class=" card-body row">
                        <div class="col-md-12 mb-3">
                            <label>Leave Normatif Type</label>
                            <select class="form-select select2" id="type" name="type">
                                <option value="all">All type</option>
                                @foreach ($leaves as $leave )
                                    <option value="{{ $leave->id }}"
                                        data-days="{{ $leave->number_of_days }}">
                                        {{ $leave->type }} - {{ $leave->description }} - {{ $leave->number_of_days }} days
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Start Date</label>
                            <div class="input-group">
                                <input type="text" name="start_date_normatif" class="form-control bulan start_date_normatif" placeholder="Pilih Tanggal">
                                <span class="input-group-text"><i class="ri-calendar-event-line"></i></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label>End Date</label>
                            <div class="input-group">
                                <input type="text" name="end_date_normatif" class="form-control bulan end_date_normatif" readonly placeholder="automatically filled when already select type and start date">
                                <span class="input-group-text"><i class="ri-calendar-event-line"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- ================= LAMPIRAN ================= -->
            <div id="section-lampiran" class="card mb-3" >
                <div class="card-header"><h5>Lampiran</h5></div>
                <div class="card-body">
                    <div class="mb-3">
                        <label>Upload Foto</label>
                        <input type="file" name="attachment" class="form-control">
                    </div>
                </div>
            </div>

            <div class="card-footer text-end">
                <button class="btn btn-success">Save</button>
            </div>
        </div>
    </div>
</div>
</form>
@endsection

@section('script')
   <!-- Datatables -->
   <script src="{{ url('') }}/assets/libs/Datatables/DataTables-1.13.1/js/jquery.dataTables.min.js"></script>
   <script src="{{ url('') }}/assets/libs/Datatables/DataTables-1.13.1/js/dataTables.bootstrap5.min.js"></script>
   <script src="{{ url('') }}/assets/libs/Datatables/Responsive-2.4.0/js/dataTables.responsive.min.js"></script>
   <script src="{{ url('') }}/assets/js/pages/datatables.init.js"></script>
   <!-- profile-setting init js -->
   <script src="{{ url('') }}/assets/js/pages/profile-setting.init.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.js"></script>
   <!-- Sweetalert -->
   <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
   <!-- Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endsection

@section('javascript')
<script>
    $(document).ready(function () {
        let employeeTable;
        let selectedEmployees = [];

        // ================= INIT TABLE =================
        function initTable() {
            if (!employeeTable) {
                employeeTable = $('#table_employee').DataTable({
                    scrollY: "350px",
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    ajax: {
                        url: "{{ route('employee-leave.leave-hrd-create') }}",
                        data: function (d) {
                            d.position = $('#position_id').val();
                            d.area = $('#area_id').val();
                            d.department = $('#department_id').val();
                        }
                    },
                    columns: [
                        {
                            data: 'id',
                            render: function (d) {
                                let checked = selectedEmployees.includes(d.toString()) ? 'checked' : '';
                                return `<input type="checkbox" class="employee_checkbox" value="${d}" ${checked}>`;
                            },
                            orderable: false,
                            searchable: false
                        },
                        { data: 'nik', className: "text-center" },
                        { data: 'fullname', className: "text-center" },
                        { data: 'position', className: "text-center" },
                        { data: 'area', className: "text-center" },
                        { data: 'department', className: "text-center" },
                        { data: 'leave_balance', className: "text-center" },
                    ]
                });
                // WAJIB: bind setelah init
                employeeTable.on('draw', function () {
                    $('.employee_checkbox').each(function () {
                        let id = $(this).val().toString();
                        $(this).prop('checked', selectedEmployees.includes(id));
                    });
                    // sync select all
                    let total = $('.employee_checkbox').length;
                    let checked = $('.employee_checkbox:checked').length;

                    $('#select_all').prop('checked', total > 0 && total === checked);

                    updateSelectedCount();
                });
            }
        }
        // ================= COUNT =================
        function updateSelectedCount() {
            $('#selected_count').text(selectedEmployees.length + " employee selected");
        }
        // ================= PLUGINS =================
        function initPlugins(context = document) {

            $(context).find('.select2').each(function () {
                if ($(this).hasClass("select2-hidden-accessible")) {
                    $(this).select2('destroy');
                }
                $(this).select2({ width: '100%' });
            });
            $(context).find('.start_date_pribadi, .end_date_pribadi, .start_date_normatif')
                .each(function () {
                    if (!this._flatpickr) {
                        flatpickr(this, {
                            allowInput: true,
                            dateFormat: "Y-m-d"
                        });
                    }
                });
        }
        // ================= VIEW =================
        function handleView() {
            let type = $('#leave_type').val();
            $('#section-pribadi').hide();
            $('#section-normatif').hide();

            if (!employeeTable) {
                initTable();
            }
            if (type === 'pribadi') {
                $('#section-pribadi').show();
                initPlugins('#section-pribadi');
            } else {
                $('#section-normatif').show();
                initPlugins('#section-normatif');
            }
        }
        handleView();
        initPlugins();
        $('#leave_type').on('change', handleView);
        // ================= FILTER =================
        $(document).on('change', '#department_id, #area_id, #position_id', function () {
            if (employeeTable) {
                employeeTable.ajax.reload();
            }
        });
        // ================= CHECKBOX =================
        $(document).on('change', '.employee_checkbox', function () {
            let id = $(this).val().toString();
            if ($(this).is(':checked')) {
                if (!selectedEmployees.includes(id)) {
                    selectedEmployees.push(id);
                }
            } else {
                selectedEmployees = selectedEmployees.filter(e => e !== id);
            }
            updateSelectedCount();
        });
        // ================= SELECT ALL =================
        $('#select_all').on('change', function () {
            let checked = $(this).prop('checked');
            $('.employee_checkbox').each(function () {
                let id = $(this).val().toString();
                $(this).prop('checked', checked);
                if (checked) {
                    if (!selectedEmployees.includes(id)) {
                        selectedEmployees.push(id);
                    }
                } else {
                    selectedEmployees = selectedEmployees.filter(e => e !== id);
                }
            });
            updateSelectedCount();
        });
        // ================= NORMATIF =================
        function calculateNormatif() {
        let start = $('.start_date_normatif').val();
        let selected = $('#type option:selected');
        let days = parseInt(selected.data('days'));
        let typeId = selected.val();

        if (!start || !days || !typeId) return;

        $.ajax({
            url: "{{ route('employee-leave.calculate-normatif') }}",
            method: "GET",
            data: {
                start_date: start,
                leave_setting_id: typeId
            },
            success: function (res) {
                $('.end_date_normatif').val(res.end_date);

                if (res.excluded.length > 0) {
                    let notes = '<div class="row row-cols-3 row-cols-md-6 g-1">';

                    res.excluded.forEach((e, index) => {
                        notes += `
                            <div class="col">
                                <small>• ${e.date} (${e.type})</small>
                            </div>
                        `;
                    });

                    notes += '</div>';

                    $('#leave_normatif_notes').html(notes);
                } else {
                    $('#leave_normatif_notes').html('-');
                }
            },
            error: function(xhr) {
                console.log(xhr.responseText);
            }
        });
    }
    $('#type').on('change', calculateNormatif);
    $('.start_date_normatif').on('change', calculateNormatif);

        // ================= STORE =================
        $('#leaveFormHRD').on('submit', function (e) {
            e.preventDefault();
            if (selectedEmployees.length === 0) {
                Swal.fire('Warning', 'Pilih minimal 1 karyawan', 'warning');
                return;
            }
            if ($('#leave_type').val() === 'normatif') {
                if (!$('input[name="attachment"]').val()) {
                    Swal.fire('Warning', 'Lampiran wajib untuk cuti normatif', 'warning');
                    return;
                }
            }

            let formData = new FormData(this);
            formData.append('employees', JSON.stringify(selectedEmployees));
            formData.append('notes', $('textarea[name="notes"]').val());

            Swal.fire({
                title: 'Yakin?',
                text: "Data cuti akan disimpan",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya'
            }).then((result) => {

                if (result.isConfirmed) {

                    $.ajax({
                        url: "{{ route('employee-leave.leave-hrd-store') }}",
                        method: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,

                        success: function (res) {
                            Swal.fire('Success', res.message, 'success')
                                .then(() => window.location.href = "{{ route('attendance-permit.index') }}#employee-leave");
                        },

                        error: function (xhr) {
                            let msg = xhr.responseJSON?.message ?? 'Terjadi kesalahan';
                            Swal.fire('Error', msg, 'error');
                        }
                    });
                }
            });
        });
    });
</script>
@endsection
