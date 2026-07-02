@extends('layouts.master')
@section('link')
<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
.table-danger{
    background:#ff1212 !important;
}
.card-active{
background:#e8f7ee;
border-left:5px solid #28a745;
}

.card-upcoming{
background:#e7f1ff;
border-left:5px solid #e7e42b;
}

.card-expired{
background:#fdeaea;
border-left:5px solid #dc3545;
}

#transferEmployeeModal .modal-dialog {
    max-width: 1200px;
}

#table-selected-employees th,
#table-selected-employees td {
    white-space: nowrap;
}
</style>
<!-- Datatables-->
<link href="/assets/libs/Datatables/DataTables-1.13.1/css/dataTables.bootstrap5.css" rel="stylesheet" type="text/css" />
<link href="/assets/libs/Datatables/Buttons-2.3.3/css/buttons.dataTables.css" rel="stylesheet" type="text/css" />
<link href="/assets/libs/Datatables/Responsive-2.4.0/css/responsive.bootstrap.css" rel="stylesheet" type="text/css" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- Toastr Notifications-->
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endsection
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Business Trip Allowance</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Master</a></li>
                    <li class="breadcrumb-item active">Business Trip Allowance</li>
                </ol>
            </div>

        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header align-items-center d-flex gap-4">
                <button type="button" id="btn-add-allowance" class="btn btn-primary btn-label waves-effect waves-light">
                    <i class="ri-add-circle-line label-icon align-middle fs-16 me-2"></i>
                    Add New Allowance
                </button>
            </div>
            <div class="card-body">
                <table class="table table-striped dt-responsive nowrap w-100" id="table-business-trip-allowance">
                <thead>
                    <tr>
                    <th scope="col" style="text-align:center">No</th>
                    <th scope="col" style="text-align:center">Level</th>
                    <th scope="col" style="text-align:center">Trip Type</th>
                    <th scope="col" style="text-align:center">Category</th>
                    <th scope="col" style="text-align:center">Minimum Hours</th>
                    <th scope="col" style="text-align:center">Amount</th>
                    <th scope="col" style="text-align:center">Total Employee</th>
                    <th scope="col" style="text-align:center">Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- CREATE BUSINESS TRIP MODAL -->
<div class="modal fade"
     id="businessTripModal"
     tabindex="-1"
     aria-hidden="true">

    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">

            <!-- HEADER -->
            <div class="modal-header bg-primary text-white">
                <div>
                    <h5 class="modal-title mb-0 text-white">
                        <i class="ri-briefcase-4-line me-1 text-white "></i>
                        Create Business Trip Allowance
                    </h5>
                    <small class="opacity-75">
                        Pilih employee dan atur allowance business trip
                    </small>
                </div>

                <button type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal">
                </button>
            </div>

            <form id="FormBusinessTrip">

                <div class="modal-body">

                    <!-- FILTER -->
                    <div class="card border shadow-sm mb-4">
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label">
                                        Level
                                    </label>
                                    <select id="select-level" name="select-level" class="form-select select2">
                                        <option value=""> Choose Level </option>
                                            @foreach ($levels as $level)
                                                <option value="{{ $level->id }}">
                                                    {{ $level->nama }}
                                                </option>
                                            @endforeach
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- BUSINESS TRIP CONFIG -->
                    <div class="card border shadow-sm">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">
                                <i class="ri-settings-3-line"></i>
                                Business Trip Configuration
                            </h6>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-label">
                                        Trip Type
                                    </label>

                                    <select id="trip_type" name="trip_type" class="form-select">
                                        <option value="domestic"> Domestic </option>
                                        <option value="overseas"> Overseas </option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label"> Category </label>
                                    <select id="category" name="category" class="form-select">
                                        <option value="daily"> Daily </option>
                                        <option value="meal"> Meal </option>
                                        {{-- <option value="hotel"> Hotel </option>
                                        <option value="transport"> Transport </option> --}}
                                        <option value="laundry"> Laundry </option>
                                    </select>
                                </div>
                                <!-- MINIMUM HOURS -->
                                <div class="col-md-4 d-none" id="minimum-hours-wrapper">
                                    <label class="form-label">
                                        Minimum Hours
                                    </label>
                                    <select id="minimum_hours"
                                            name="minimum_hours"
                                            class="form-select">
                                        <option value="">Select Hours</option>
                                        <option value="4">More Than 4 Hours</option>
                                        <option value="8">More Than 8 Hours</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-2">
                                    <label class="form-label"> Currency </label>
                                    <select id="currency" name="currency"
                                            class="form-select">
                                        <option value="IDR"> IDR </option>
                                    </select>
                                </div>
                                <div class="col-md-10">
                                    <label class="form-label"> Amount </label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            Rp
                                        </span>
                                        <input type="text" id="amount" name="amount" class="form-control currency-format" placeholder="0">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FOOTER -->
                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-light"
                            data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="button"
                            id="btn-save-allowance"
                            class="btn btn-primary">
                        <i class="ri-save-line me-1"></i>
                        Save Business Trip
                    </button>
                </div>
            </form>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endsection

@section('javascript')
<script>
    $(document).ready(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });
</script>
<script type="text/javascript">
$(document).ready(function () {

    initPlugins();

    $('#btn-add-allowance').on('click', function(){
        $('#businessTripModal').modal('show');
        initPlugins($('#businessTripModal'));
    });
    $('#category , #trip_type').on('change', function () {
        let category = $('#category').val();
        let trip_type = $('#trip_type').val();
        if (category  === 'meal' && trip_type === 'domestic') {
            $('#minimum-hours-wrapper')
                .removeClass('d-none');
        } else {
            $('#minimum-hours-wrapper')
                .addClass('d-none');
            $('#minimum_hours').val('');
        }
    });

    $(document).on('input', '.currency-format', function () {
        let cursorPos = this.selectionStart;
        let value = $(this).val()
            .replace(/[^\d]/g, '');
        if (!value) {
            $(this).val('');
            return;
        }
        let formatted = new Intl.NumberFormat('id-ID')
            .format(value);
        $(this).val(formatted);
    });

    let allowanceTable = $('#table-business-trip-allowance').DataTable({
    processing: true,
    responsive: false,
    serverSide: false,
    scrollX: true,
    ajax:"{{ route('business-trip-allowance.index') }}",
    columns:[
        {data:'DT_RowIndex', name:'DT_RowIndex', className : "text-center", orderable:false, searchable:false},
        {data:'level', className: "text-center"},
        {data:'trip_type', className: "text-center"},
        {data:'category', className: "text-center"},
        {data:'minimum_hours', className: "text-center"},
        {data:'amount', className: "text-center"},
        {data:'total_employee', name:'total_employee', className: "text-center"},
        {data:'action', name:'action', className:"text-center", orderable:false, searchable:false}
    ]
});
    $('#btn-save-allowance').on('click', function (e) {
        e.preventDefault();
        let amount = $('#amount')
        .val()
        .replace(/[^\d]/g, '');

        let formData = {
            level_id: $('#select-level').val(),
            category: $('#category').val(),
            minimum_hours: $('#minimum_hours').val(),
            trip_type: $('#trip_type').val(),
            amount: cleanCurrency($('#amount').val()),
            currency: $('#currency').val(),
            _token: $('meta[name="csrf-token"]').attr('content')
        };
        // VALIDASI
        if (
            !formData.level_id ||
            !formData.category ||
            !formData.trip_type ||
            !formData.amount ||
            !formData.currency
        ) {
            Swal.fire({
                icon: 'warning',
                title: 'Semua field wajib diisi'
            });

            return;
        }
        // VALIDASI KHUSUS MEAL DOMESTIC
        if (
            formData.trip_type === 'domestic' &&
            formData.category === 'meal' &&
            !formData.minimum_hours
        ) {
            Swal.fire({
                icon: 'warning',
                title: 'Minimum Hours wajib dipilih',
                text: 'Meal allowance domestic memerlukan minimum hours.'
            });

            return;
        }

        // console.log(amount);
        // CEK EXISTING
        $.post("{{ route('business-trip-allowance.check') }}", formData, function(check){
            if (check.exists) {
                Swal.fire({
                    title: 'Data Sudah Ada',
                    html: `
                        <div class="text-start">
                            <p class="mb-3">
                                Data allowance sudah tersedia dan akan diperbarui:
                            </p>

                            <div class="border rounded p-3 bg-light">

                                <div class="mb-2">
                                    <strong>Level</strong><br>
                                    ${$('#select-level option:selected').text()}
                                </div>

                                <div class="mb-2">
                                    <strong>Category</strong><br>
                                    ${formData.category}
                                </div>

                                <div class="mb-3">
                                    <strong>Trip Type</strong><br>
                                    ${formData.trip_type}
                                </div>

                                <hr>

                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <small class="text-muted">Saat Ini</small>
                                        <div class="fw-bold text-danger">
                                            ${check.data.currency} ${Number(check.data.amount).toLocaleString()}
                                        </div>
                                    </div>

                                    <div class="px-3">
                                        <i class="ri-arrow-right-line fs-3 text-primary"></i>
                                    </div>

                                    <div>
                                        <small class="text-muted">Menjadi</small>
                                        <div class="fw-bold text-success">
                                            ${formData.currency} ${Number(formData.amount).toLocaleString()}
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <p class="mt-3 mb-0 text-warning">
                                Perubahan ini akan menggantikan data sebelumnya.
                            </p>
                        </div>
                    `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Update'
                }).then((result) => {
                    if (result.isConfirmed) {
                        saveAllowance(formData);
                    }
                });
            }
            else {
                Swal.fire({
                    title: 'Simpan Data?',
                    text: 'Business trip allowance akan disimpan',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Simpan'
                }).then((result) => {

                    if (result.isConfirmed) {
                        saveAllowance(formData);
                    }
                });
            }
        });
    });
    function saveAllowance(formData)
    {
        $.ajax({
            url: "{{ route('business-trip-allowance.store') }}",
            type: 'POST',
            data: formData,
            success: function(response){
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: response.message
                });
                $('#businessTripModal').modal('hide');
                allowanceTable.ajax.reload(null, false);
            },
            error: function(xhr){
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: xhr.responseJSON?.message || 'Terjadi kesalahan'
                });
            }
        });
    }

    $(document).on('click', '.edit-btn', function () {
        let id = $(this).data('id');
        $('.currency-format').each(function () {
            let cleanValue = $(this)
                .val()
                .replace(/\./g, '');
            $(this).val(cleanValue);
        });
        $.ajax({
            url: "{{ route('business-trip-allowance.edit', ':id') }}"
                .replace(':id', id),
            type: 'GET',
            success: function(res){
                Swal.fire({
                    title: 'Update Business Trip Allowance',
                    width: 700,
                    html: `
                        <div class="text-start mb-4">
                            <div class="mb-3">
                                <small class="text-muted">
                                    Configuration
                                </small>
                                <div class="fw-semibold">
                                    ${res.level_name} ||
                                    ${res.trip_type} ||
                                    ${res.category}
                                </div>
                            </div>
                            <div class="row align-items-center">
                                <!-- CURRENT -->
                                <div class="col-md-4">
                                    <div class="border rounded p-3 h-100">
                                        <small class="text-muted">
                                            Saat Ini
                                        </small>
                                        <div class="fw-bold text-danger fs-4">
                                            ${res.currency}
                                            ${Number(res.amount).toLocaleString()}
                                        </div>

                                    </div>
                                </div>
                                <!-- ARROW -->
                                <div class="col-md-1 text-center">
                                    <i class="ri-arrow-right-line fs-1 text-primary"></i>
                                </div>
                                <!-- NEW -->
                                <div class="col-md-7">
                                    <div class="border rounded p-3">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                Currency
                                            </label>
                                            <select id="edit_currency" name="currency" class="form-select">
                                                <option value="IDR"> IDR </option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="form-label"> Amount </label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    Rp
                                                </span>
                                                <input type="text" id="edit-amount" name="amount" class="form-control currency-format" placeholder="0">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Update',
                    preConfirm: () => {
                        let currency = $('#edit_currency').val();
                        let amount = cleanCurrency($('#edit-amount').val());
                        if (!currency || !amount) {

                            Swal.showValidationMessage(
                                'Currency dan amount wajib diisi'
                            );
                            return false;
                        }
                        return {
                            currency,
                            amount
                        };
                    }

                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('business-trip-allowance.update', ':id') }}"
                                .replace(':id', id),
                            type: 'PUT',
                            data: {
                                currency: result.value.currency,
                                amount: result.value.amount,
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response){
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: response.message
                                });
                                allowanceTable.ajax.reload(null, false);
                            },
                            error: function(xhr){
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: xhr.responseJSON?.message
                                        || 'Terjadi kesalahan'
                                });
                            }
                        });
                    }
                });
            },
            error: function(xhr){
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: xhr.responseJSON?.message
                        || 'Tidak dapat mengambil data'
                });

            }

        });

    });
    //DELETE EMPLOYEE WORKHOUR
    $('#table-business-trip-allowance').on("click", ".delete-btn", function () {
        let id = $(this).data("id");
        Swal.fire({
            title: "Are you sure?",
            text: "Business Trip Allowance will be deleted!, Make Sure there is no business trip allowance with this data",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('business-trip-allowance.destroy', ':id') }}".replace(':id', id),
                    type: "DELETE",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response){
                        Swal.fire({
                            icon: "success",
                            title: "Deleted!",
                            text: response.message
                        });
                        allowanceTable.ajax.reload(null,false);
                    },
                    error: function(){
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: "Failed to delete data"
                        });
                    }
                });
            }
        });
    });
});
</script>
<script>
    function parseCurrency(value) {
        if (!value) {
            return 0;
        }
        return parseInt(
            value.toString().replace(/\./g, '')
        ) || 0;
    }

    function cleanCurrency(value) {
        return value.replace(/[^\d]/g, '');
    }

    function initPlugins(context = document) {
        // SELECT2
        $(context).find('.select2').each(function () {
            if ($(this).hasClass("select2-hidden-accessible")) {
                $(this).select2('destroy');
            }
            $(this).select2({ width: '100%' });
        });
        $('#select-level').select2({ dropdownParent: $('#businessTripModal')});
        // FILTER DATE
        $(context).find('.filter_date').each(function () {if (!this._flatpickr) {flatpickr(this, {dateFormat: "Y-m-d",altInput: true,altFormat: "d M Y",allowInput: true,defaultDate: "today"});}});
        // REQUEST DATE
        $(context).find('.request_date').each(function () {if (this._flatpickr) { this._flatpickr.destroy(); }flatpickr(this, {
            plugins: [new monthSelectPlugin({shorthand: true, dateFormat: "Y-m", altFormat: "F Y"})],altInput: true, allowInput: false, defaultDate: "today"});});
        $(context).find('.overtime_date').each(function () {if (!this._flatpickr) {flatpickr(this, {dateFormat: "Y-m-d",altInput: true,altFormat: "d M Y",allowInput: true,defaultDate: "today"});}});
    }

</script>
@endsection
