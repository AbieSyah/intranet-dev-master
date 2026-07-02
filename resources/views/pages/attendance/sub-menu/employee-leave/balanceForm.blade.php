@extends('layouts.master')
@section('link')
<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- Datatables-->
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
            <h4>Add Leave Balance</h4>
        </div>
    </div>
</div>

<form action="{{ route('employee-leave.leave-balance-store') }}" method="POST">
@csrf

<div class="row">
    <div class="col-lg-10">
    <input type="hidden" name="employees" id="employees_input">
        {{-- ===================== TABLE ATAS FILTER ===================== --}}
        <div class="card mb-3">
            <div class="card-header">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('attendance-permit.index') }}" class="btn btn-primary btn-label waves-effect waves-light">
                        <i class="ri-arrow-left-fill label-icon align-middle fs-16 me-2"></i> Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-md-4">
                        <label>Position</label>
                        <select id="filter_position" class="form-select select2">
                            <option value="">All Position</option>
                            @foreach($positions as $position)
                                <option value="{{ $position }}">{{ $position }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>Area</label>
                        <select id="filter_area" class="form-select select2">
                            <option value="">All Area</option>
                            @foreach($areas as $area)
                                <option value="{{ $area }}">{{ $area }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>Department</label>
                        <select id="filter_department" class="form-select select2">
                            <option value="">All Department</option>
                            @foreach($departments as $department)
                                <option value="{{ $department }}">{{ $department }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="table-employee">
                        <thead class="table-light">
                            <tr>
                                <th width="5%"><input type="checkbox" id="select_all"></th>
                                <th class="text-center">NIK</th>
                                <th class="text-center">Name</th>
                                <th class="text-center">Position</th>
                                <th class="text-center">Area</th>
                                <th class="text-center">Department</th>
                                <th class="text-center">Join Date</th>
                                <th class="text-center">Years Of Service</th>
                            </tr>
                        </thead>
                    </table>
                </div>

                <div class="d-flex justify-content-between mt-3">
                    <p id="selected_count">0 Employee selected</p>

                    <div>
                        <button type="button" id="btn-reset" class="btn btn-secondary">Reset</button>
                        <button type="button" id="btn-select" class="btn btn-primary">Select</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===================== TABLE BAWAH ===================== --}}
        <div class="card mb-3">
            <div class="card-header">
                <h5>Leave Saldo Balance</h5>
            </div>

            <div class="card-body">
                <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                    <table class="table table-bordered" id="table-selected">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center">No</th>
                                <th class="text-center">Nik</th>
                                <th class="text-center">Name</th>
                                <th class="text-center">Years of Service</th>
                                <th class="text-center">Leave Balance</th>
                                <th class="text-center">Remaining Leave</th>
                                <th class="text-center">Total Leave Days</th>
                                <th class="text-center" width="10%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- diisi via JS --}}
                        </tbody>
                    </table>
                </div>
            </div>                    {{-- ===================== VALID DATE ===================== --}}
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                            <label class="form-label">Start From</label>
                            <div class="input-group">
                                <input type="text" name="valid_from" class="form-control bulan start_date" placeholder="Pilih Tanggal">
                                <span class="input-group-text"><i class="ri-calendar-event-line"></i></span>
                            </div>
                        </div>
                    <div class="col-md-6">
                        <label>Expire</label>
                        <input type="text" id="valid_until" class="form-control" readonly>
                    </div>
                </div>
                <div class="text-end mt-3">
                    <button class="btn btn-success">Save</button>
                </div>
            </div>
        </div>
    </div>
    {{-- ===================== INFO PANEL ===================== --}}
    <div class="col-lg-2">
        <div class="card">
            <div class="card-header">
                <h5>Informasi</h5>
            </div>
            <div class="card-body">
                <ul>
                    <li>Data Karyawan Yang Tampil Hanyalah Karyawan Yang Sudah Bekerja Selama 1 Tahun</li>
                    <li>Saldo cuti berlaku selama 2 tahun</li>
                    <li>Tipe Cuti Akan Terisi Otomatis Berdasarkan Berapa Lama Karyawan Sudah Bergabung</li>
                    <li>Bila Terdapat Tanda Seru ⚠️ Itu Menandakan Tanggal Join Karyawan Tidak Ada Yang Sesuai Dengan Master Leave</li>
                </ul>
            </div>
        </div>
    </div>

</div>
</form>
@endsection

@section('script')
<!-- Datatables -->
<script src="/assets/libs/Datatables/DataTables-1.13.1/js/jquery.dataTables.min.js"></script>
<script src="/assets/libs/Datatables/DataTables-1.13.1/js/dataTables.bootstrap5.min.js"></script>
<script src="/assets/libs/Datatables/Responsive-2.4.0/js/dataTables.responsive.min.js"></script>
<script src="/assets/js/pages/datatables.init.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
@endsection

@section('javascript')
<script>
// ================= INIT PLUGINS =================
function initPlugins(context = document) {
    // SELECT2
    $(context).find('.select2').each(function () {if (!$(this).hasClass("select2-hidden-accessible")) {$(this).select2({width: '100%',dropdownParent: $('body')});}});
    // FLATPICKR DATE
    $(context).find('.start_date').each(function () {if (!this._flatpickr) {flatpickr(this, {allowInput: true,dateFormat: "Y-m-d"});}});
}
// ================= MAIN =================
$(document).ready(function () {
    initPlugins(); // 🔥 WAJIB
    // CSRF
    let leaveSettings = @json($leaves);
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    let selectedEmployees = [];
    let selectedEmployeeData = [];

    function updateSelectedCount() {
        $('#selected_count').text(selectedEmployees.length + " employee selected");
    }

    // ================= TABLE EMPLOYEE =================
    let employeeTable = $('#table-employee').DataTable({
        scrollY: "350px",
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('employee-leave.leave-balance-create') }}",
            data: function (d) {
                d.position = $('#filter_position').val();
                d.area = $('#filter_area').val();
                d.department = $('#filter_department').val();
            }
        },
        columns: [
            {
                data: 'id',
                render: function (d) {
                    return `<input type="checkbox" class="employee_checkbox" value="${d}">`;
                },
                orderable: false,
                searchable: false
            },
            { data: 'nik', className: "text-center" },
            { data: 'fullname', className: "text-center" },
            { data: 'position', className: "text-center" },
            { data: 'area', className: "text-center" },
            { data: 'department', className: "text-center" },
            { data: 'joindate', className: "text-center" },
            { data: 'total_years', className: "text-center" }
        ]
    });

    // FILTER
    $('#filter_area, #filter_department, #filter_position, #filter_join_year')
    .on('change', function () {
        employeeTable.ajax.reload();
    });

    // ================= CHECKBOX =================
    employeeTable.on('draw', function () {
        $('.employee_checkbox').each(function () {
            $(this).prop('checked', selectedEmployees.includes($(this).val()));
        });

        let total = employeeTable.rows({ search: 'applied' }).nodes().length;
        let checked = employeeTable.rows({ search: 'applied' }).nodes().to$().find('.employee_checkbox:checked').length;

        $('#select_all').prop('checked', total > 0 && total === checked);
    });

    $('#select_all').on('change', function () {
        let checked = $(this).prop('checked');

        employeeTable.rows({ search: 'applied' }).nodes().to$().find('.employee_checkbox').each(function () {
            let id = $(this).val();
            $(this).prop('checked', checked);

            if (checked) {
                if (!selectedEmployees.includes(id)) selectedEmployees.push(id);
            } else {
                selectedEmployees = selectedEmployees.filter(e => e != id);
            }
        });

        updateSelectedCount();
    });

    $(document).on('change', '.employee_checkbox', function () {
        let id = $(this).val();

        if ($(this).is(':checked')) {
            if (!selectedEmployees.includes(id)) selectedEmployees.push(id);
        } else {
            selectedEmployees = selectedEmployees.filter(e => e != id);
        }

        updateSelectedCount();
    });

    // ================= SELECT BUTTON =================
    $('#btn-select').on('click', function () {
        if (selectedEmployees.length === 0) {
            Swal.fire('Warning', 'Pilih minimal 1 karyawan!', 'warning');
            return;
        }
        Swal.fire({
            title: 'Memuat data...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });
        $.ajax({
            url: "{{ route('employee-leave.get-selected-employees') }}", // 🔥 endpoint baru
            method: "POST",
            data: {
                ids: selectedEmployees,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (res) {
                selectedEmployeeData = res.data;
                renderSelectedTable();
                Swal.close();
            },
            error: function () {
                Swal.fire('Error', 'Gagal mengambil data karyawan', 'error');
            }
        });
    });

    // ================= RESET =================
    $('#btn-reset').on('click', function () {
        Swal.fire({
            title: 'Yakin reset?',
            icon: 'warning',
            showCancelButton: true
        }).then(result => {
            if (result.isConfirmed) {
                selectedEmployeeData = [];
                selectedEmployees = [];
                employeeTable.ajax.reload();
                renderSelectedTable();
                updateSelectedCount();
            }
        });
    });

    // ================= RENDER TABLE =================
    async function renderSelectedTable() {
        let tbody = $('#table-selected tbody');
        tbody.empty();
        if (selectedEmployeeData.length === 0) {
            tbody.append(`<tr><td colspan="8" class="text-center text-muted">Belum ada karyawan dipilih</td></tr>`);
            return;
        }
        let no = 1; // 🔥 nomor urut
        for (const emp of selectedEmployeeData) {
            let setting = getLeaveSetting(emp.total_years);

            if (!setting) {
                let balance = 0;
                let leaveTypeId = '';
                let leaveType = '';
                let remaining = emp.remaining_last_year ?? 0;
                let expiredDays = emp.expired_days ?? 0;
                let total = balance + remaining;
                tbody.append(`
                <tr data-id="${emp.id}" style="background-color: #fff3cd;">
                    <td class="text-center">${no++}</td> <!--  NO -->
                    <td class="text-center">${emp.nik ?? '-'}</td>
                    <td class="text-center">${emp.fullname}</td>
                    <td class="text-center">${emp.total_years ?? 0} Tahun</td>
                    <td class="text-center text-danger" style="font-weight: bold;">
                        ⚠️ Leave type tidak terdeteksi
                        <input type="hidden" class="leave-type-id" value="">
                        <input type="hidden" class="leave-type" value="">
                    </td>
                    <td class="text-center">
                        ${remaining > 0
                        ? `${remaining} ${
                            expiredDays !== null && expiredDays !== undefined
                                ? `<small class="text-muted">(expired in ${expiredDays} days)</small>`
                                : ''
                        }`
                        : '-'}
                    </td>
                    <td class="text-center total-cell">0</td>
                    <td class="text-center">
                        <button class="btn btn-danger btn-sm btn-remove" data-id="${emp.id}">Hapus</button>
                    </td>
                </tr>
                `);
                continue;
            }

            let balance = parseInt(setting.number_of_days) || 0;
            let leaveTypeId = setting.id;
            let leaveType = setting.type;

            let remaining = parseInt(emp.remaining_last_year) || 0;
            let expiredDays = emp.expired_days;
            let total = balance + remaining;

            tbody.append(`
            <tr data-id="${emp.id}">
                <td class="text-center">${no++}</td> <!-- 🔥 NO -->
                <td class="text-center">${emp.nik ?? '-'}</td>
                <td class="text-center">${emp.fullname}</td>
                <td class="text-center">${emp.total_years ?? 0} Tahun</td>
                <td class="text-center">
                    <input type="number" class="form-control form-control-sm balance-input"
                        value="${balance}" min="0">
                    <input type="hidden" class="leave-type-id" value="${leaveTypeId}">
                    <input type="hidden" class="leave-type" value="${leaveType}">
                </td>
                <td class="text-center">
                    ${remaining > 0
                    ? `${remaining} ${
                        expiredDays !== null && expiredDays !== undefined
                            ? `<small class="text-muted">(expired in ${expiredDays} days)</small>`
                            : ''
                    }`
                    : '-'}
                </td>
                <td class="text-center total-cell">
                    ${total}
                </td>
                <td class="text-center">
                    <button class="btn btn-danger btn-sm btn-remove" data-id="${emp.id}">Hapus</button>
                </td>
            </tr>
            `);
        }

        no = 1;
        $('#table-selected tbody tr').each(function(){
            $(this).find('td:first').text(no++);
        });
    }
    //================== LOGIC HITUNG-HITUNGAN =================
    $(document).on('input', '.balance-input', function () {
        let row = $(this).closest('tr');
        let balance = parseInt($(this).val()) || 0;
        // ambil angka pertama dari remaining (sebelum text expired)
        let remainingText = row.find('td:eq(4)').text();
        let remaining = parseInt(remainingText) || 0;
        let total = balance + remaining;
        row.find('.total-cell').text(total);
    });
    // ================= REMOVE =================
    $(document).on('click', '.btn-remove', function () {
        let id = $(this).data('id');

        selectedEmployeeData = selectedEmployeeData.filter(e => e.id != id);
        selectedEmployees = selectedEmployees.filter(e => e != id.toString());
        employeeTable.ajax.reload();
        renderSelectedTable();
        updateSelectedCount();

        no = 1;
        $('#table-selected tbody tr').each(function(){
            $(this).find('td:first').text(no++);
        });
    });
    // ================= LOGIC =================
    function getLeaveSetting(totalYears) {
        let year = parseInt(totalYears) || 0;

        // Cari setting yang cocok dengan range tahun
        let matched = leaveSettings.find(v => {
            let minCheck = v.min_years === null || year >= v.min_years;
            let maxCheck = v.max_years === null || year <= v.max_years;
            return minCheck && maxCheck;
        });
        // Jika tidak ada yang cocok, gunakan setting dengan min_years terdekat
        if (!matched) {
            matched = leaveSettings
                .filter(v => v.min_years !== null && year >= v.min_years)
                .sort((a, b) => b.min_years - a.min_years)[0];
        }

        return matched;
    }
    // ================= SUBMIT =================
    $('form').on('submit', function (e) {
        e.preventDefault();

        if (selectedEmployeeData.length === 0) {
            Swal.fire('Warning', 'Belum ada karyawan dipilih!', 'warning');
            return;
        }
        let data = [];
        let hasError = false;
        let errorEmps = [];

        $('#table-selected tbody tr').each(function () {
            let id = $(this).data('id');
            let balance = $(this).find('.balance-input').val();
            let leaveTypeId = $(this).find('.leave-type-id').val();
            let leaveType = $(this).find('.leave-type').val();

            // ❌ VALIDASI: leave_type_id tidak boleh kosong, null, atau "null"
            if (!leaveTypeId || leaveTypeId === 'null' || leaveTypeId === '') {
                let empName = $(this).find('td:eq(1)').text();
                errorEmps.push(empName);
                hasError = true;
                return;
            }

            data.push({
                id: id,
                leave_type_id: leaveTypeId,
                type: leaveType,
                leave_balance: balance
            });
        });

        if (hasError) {
            let empList = errorEmps.join('<br>');
            Swal.fire({
                title: 'Error',
                html: `Karyawan berikut tidak memiliki leave type yang valid:<br><br>${empList}`,
                icon: 'error'
            });
            return;
        }

        let payload = {
            employees: JSON.stringify(data),
            valid_from: $('[name="valid_from"]').val(),
            valid_to: $('#valid_until').val()
        };
        Swal.fire({
            title: 'Menyimpan...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });
        $.ajax({
            url: "{{ route('employee-leave.leave-balance-store') }}",
            method: "POST",
            data: payload,
            success: function (res) {
                Swal.fire('Success', res.message, 'success')
                    .then(() => window.location.href = "{{ route('attendance-permit.index') }}#employee-leave");
            },
            error: function (err) {
                let message = err.responseJSON?.message || 'Terjadi kesalahan';
                if (err.responseJSON?.data?.employees) {
                    message += '<br><br>Karyawan duplicate:<br>' + err.responseJSON.data.employees.join('<br>');
                }
                Swal.fire('Error', message, 'error');
            }
        });
    });

    // ================= VALID DATE =================
    $(document).on('change', '.start_date', function () {
        let date = new Date($(this).val());

        if (!isNaN(date)) {
            date.setFullYear(date.getFullYear() + 2);
            date.setDate(date.getDate() - 1);

            $('#valid_until').val(date.toISOString().split('T')[0]);
        }
    });

});
</script>
@endsection
