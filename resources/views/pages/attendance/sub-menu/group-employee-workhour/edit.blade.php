@extends('layouts.master')
@section('link')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    .custom-toggle {
        transform: scale(1.8);
        cursor: pointer;
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
        <div class="page-title-box d-flex justify-content-between align-items-center">
            <h4>Edit Group Employee Work Hour</h4>
        </div>
    </div>
</div>

<form id="form-create" method="POST" action="{{ route('group-employee-workhour.update') }}">
    @csrf
    @method('PUT')
    <input type="hidden" name="group_id" value="{{ encrypt($group->id) }}" />
    <div class="card">
        <div class="card-header align-items-center d-flex justify-content-between">
            <div class="flex-shrink-0">
                <a href="{{ route('group-employee-workhour.index') }}" class="btn btn-primary btn-label waves-effect waves-light">
                    <i class="ri-arrow-left-fill label-icon align-middle fs-16 me-2"></i> Back
                </a>
            </div>
        </div>

        <div class="card-body">
            {{-- GROUP NAME --}}
            <div class="row mb-4 align-items-end">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Group Name</label>
                    <input type="text" name="group_name" class="form-control" placeholder="Input Group Name" value="{{ old('group_name', $group->name) }}" required>
                </div>

                <div class="col-md-6 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-success" id="btn-transfer-in">
                        <i class="ri-logout-box-line"></i> Transfer In
                    </button>

                    <button type="button" class="btn btn-danger" id="btn-transfer-out">
                        <i class="ri-login-box-line"></i> Transfer Out
                    </button>
                </div>
            </div>

            {{-- FILTER --}}
            <div class="row mb-3">
                <div class="col-md-3">
                    <label>Area</label>
                    <select id="filter_area" class="form-select select2">
                        <option value="">All</option>
                        @foreach ($areas as $area)
                            <option value="{{ $area }}">{{ $area }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Department</label>
                    <select id="filter_department" class="form-select select2">
                        <option value="">All</option>
                        @foreach ($departments as $department)
                            <option value="{{ $department }}">{{ $department }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Section</label>
                    <select id="filter_section" class="form-select select2">
                        <option value="">All</option>
                        @foreach ($sections as $section)
                            <option value="{{ $section }}">{{ $section }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Building</label>
                    <select id="filter_building" class="form-select select2">
                        <option value="">All</option>
                        @foreach ($buildings as $building)
                            <option value="{{ $building }}">{{ $building }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- EMPLOYEE TABLE --}}
            <div class="table-responsive">
                <table class="table table-bordered" id="table-group-employee-workhour">
                    <thead class="table-light">
                        <tr>
                            <th width="5%"><input type="checkbox" id="select_all"></th>
                            <th>Name</th>
                            <th>Area</th>
                            <th>Department</th>
                            <th>Section</th>
                            <th>Building</th>
                        </tr>
                    </thead>
                </table>
            </div>
            <p class="text-muted mt-2" id="selected_count">0 Employee selected</p>

            {{-- EMPLOYEES TO BE ADDED --}}
            <div id="employees-to-add-section" class="mt-4" style="display: none;">
                <h5 class="text-primary">Employees to be Added:</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="table-employees-to-add">
                        <thead class="table-success">
                            <tr>
                                <th>Name</th>
                                <th>Area</th>
                                <th>Department</th>
                                <th>Section</th>
                                <th>Building</th>
                                <th>Current Group</th>
                                <th style="width:50px;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="employees-to-add-body"></tbody>
                    </table>
                </div>
                <div class="mt-3">
                    <button type="button" class="btn btn-success" id="btn-save-transfer-in">
                        <i class="ri-save-line"></i> Save Changes
                    </button>
                    <button type="button" class="btn btn-secondary" id="btn-cancel-transfer-in">
                        <i class="ri-close-line"></i> Cancel
                    </button>
                </div>
            </div>
        </div>

        {{-- WORKHOUR SCHEDULE --}}
        <div class="card mb-3">
            <div class="card-header"><strong>Workhour Schedule</strong></div>
            <div class="card-body px-4">
                <div id="workhour-wrapper">
                    @foreach ($group->groupWorkHours as $wh)
                    <div class="row mb-2 workhour-row">
                        <div class="col-md-3">
                            <label class="form-label">Work Name</label>
                            <select name="workhour_id[]" class="form-select select2 workhour-select" >
                                <option value="">Select Work Name</option>
                                @foreach($workhours as $workhour)
                                    <option value="{{ $workhour->id }}" {{ $wh->workhour_id == $workhour->id ? 'selected' : '' }}>
                                        {{ $workhour->work_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Detail Workhour</label>
                            <div class="workhour-detail text-muted medium" style="min-height:45px;">-</div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Start Date</label>
                            <div class="input-group">
                                <input type="text" name="start_date[]" class="form-control bulan start_date"
                                        placeholder="Pilih Tanggal" value="{{ $wh->start_date }}" >
                                <span class="input-group-text"><i class="ri-calendar-event-line"></i></span>
                            </div>
                        </div>

                        <div class="col-md-1 d-flex align-items-end gap-1">
                            <button type="button" class="btn btn-success add-row mb-2"><i class="ri-add-line"></i></button>
                            <button type="button" class="btn btn-danger remove-row mb-2"><i class="ri-subtract-line"></i></button>
                        </div>
                        <div class="col-md-1 d-flex align-items-center justify-content-end mt-3">
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input toggle-active custom-toggle" type="checkbox" >
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- TEMPLATE --}}
                <div id="workhour-row-template" class="d-none">
                    <div class="row mb-3 workhour-row">
                        <div class="col-md-3">
                            <label class="form-label">Work Name</label>
                            <select name="workhour_id[]" class="form-select select2 workhour-select" >
                                <option value="">Select Work Name</option>
                                @foreach($workhours as $workhour)
                                    <option value="{{ $workhour->id }}">{{ $workhour->work_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Detail Workhour</label>
                            <div class="workhour-detail text-muted medium" style="min-height:45px;">-</div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Start Date</label>
                            <div class="input-group">
                                <input type="text" name="start_date[]" class="form-control bulan start_date" placeholder="Pilih Tanggal">
                                <span class="input-group-text"><i class="ri-calendar-event-line"></i></span>
                            </div>
                        </div>

                        <div class="col-md-1 d-flex align-items-end gap-1">
                            <button type="button" class="btn btn-success add-row mb-2"><i class="ri-add-line"></i></button>
                            <button type="button" class="btn btn-danger remove-row mb-2"><i class="ri-subtract-line"></i></button>
                        </div>
                        <div class="col-md-1 d-flex align-items-center justify-content-end mt-3">
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input toggle-active custom-toggle" type="checkbox">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- BUTTON SAVE --}}
        <div class="card-footer text-end">
            <button type="submit" class="btn btn-primary">Save</button>
        </div>
    </div>
</form>

<!-- LOADING MODAL -->
{{-- <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
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
</div> --}}

<!-- TRANSFER IN MODAL -->
<div class="modal fade" id="transferInModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Transfer In - Add Employees to Group</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label>Area</label>
                        <select id="transfer-in-filter-area" class="form-select select2">
                            <option value="">All</option>
                            @foreach ($areas as $area)
                                <option value="{{ $area }}">{{ $area }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Department</label>
                        <select id="transfer-in-filter-department" class="form-select select2">
                            <option value="">All</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department }}">{{ $department }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Section</label>
                        <select id="transfer-in-filter-section" class="form-select select2">
                            <option value="">All</option>
                            @foreach ($sections as $section)
                                <option value="{{ $section }}">{{ $section }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Building</label>
                        <select id="transfer-in-filter-building" class="form-select select2">
                            <option value="">All</option>
                            @foreach ($buildings as $building)
                                <option value="{{ $building }}">{{ $building }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered" id="table-transfer-in">
                        <thead class="table-light">
                            <tr>
                                <th style="width:50px;"><input type="checkbox" id="select-all-transfer-in"></th>
                                <th>Name</th>
                                <th>Area</th>
                                <th>Department</th>
                                <th>Section</th>
                                <th>Building</th>
                                <th>Current Group</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="btn-transfer-in-modal" class="btn btn-success">
                    <i class="ri-logout-box-line"></i> Transfer In
                </button>
            </div>
        </div>
    </div>
</div>

<!-- TRANSFER OUT MODAL -->
<div class="modal fade" id="transferOutModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Transfer Out - Move Employees to Another Group</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="select-target-group" class="form-label">Select Target Group</label>
                    <select id="select-target-group" class="form-select select2">
                        <option value="">Choose Target Group</option>
                        <!-- Options will be loaded via AJAX -->
                    </select>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered" id="table-transfer-out">
                        <thead class="table-light">
                            <tr>
                                <th style="width:50px;"><input type="checkbox" id="select-all-transfer-out"></th>
                                <th>Name</th>
                                <th>Area</th>
                                <th>Department</th>
                                <th>Section</th>
                                <th>Building</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <p class="text-muted mt-2" id="transfer-out-selected-count">0 Employee selected</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="btn-confirm-transfer-out" class="btn btn-danger">
                    <i class="ri-logout-box-line"></i> Transfer Out
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="/assets/libs/Datatables/DataTables-1.13.1/js/jquery.dataTables.min.js"></script>
<script src="/assets/libs/Datatables/DataTables-1.13.1/js/dataTables.bootstrap5.min.js"></script>
<script src="/assets/libs/Datatables/Responsive-2.4.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endsection

@section('javascript')
<script>
$(document).ready(function () {

    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    let existingEmployeeIds = @json($group->groupEmployees->pluck('employee_id')->map(function($id){ return (string)$id; }));
    let selectedEmployees = []; // Start with empty array, not existing employees

    let workhourDetails = {
        @foreach($workhours as $workhour)
            '{{ $workhour->id }}': @json($workhour->details->map(function($detail){ return ['day' => $detail->day, 'work_in' => $detail->work_in, 'work_out' => $detail->work_out]; })),
        @endforeach
    };

    function formatWorkhourDescription(details){
        if(!details || details.length === 0) return '-';

        let groups = {};
        details.forEach(function(item){
            let key = item.work_in + '|' + item.work_out;
            groups[key] = groups[key] || [];
            groups[key].push(item.day);
        });

        let dayOrder = ['Monday','Tuesday','Wednesday','Thursday','Friday','saturday','Sunday'];

        let parts = Object.keys(groups).map(function(key){
            let days = groups[key];
            days.sort(function(a,b){ return dayOrder.indexOf(a) - dayOrder.indexOf(b); });

            let label = days.join(', ');
            if(days.length > 1){
                let start = days[0];
                let end = days[days.length-1];
                let expected = dayOrder.slice(dayOrder.indexOf(start), dayOrder.indexOf(end)+1);
                if(JSON.stringify(expected) === JSON.stringify(days)){
                    label = start + ' - ' + end;
                }
            }

            let [work_in, work_out] = key.split('|');
            return `${label} (${work_in} - ${work_out})`;
        });

        return parts.join(', ');
    }

    function updateWorkhourDetail(row){
        let selectedId = row.find('.workhour-select').val();
        let details = workhourDetails[selectedId] || [];
        row.find('.workhour-detail').text(formatWorkhourDescription(details));
    }

    function parseDateValue(dateStr){
        if(!dateStr) return null;
        let date = new Date(dateStr + 'T00:00:00');
        return isNaN(date.getTime()) ? null : date;
    }

    function updateActiveSchedule(){
        let rows = [];
        $('#workhour-wrapper .workhour-row').each(function(){
            let start = $(this).find('.start_date').val();
            let date = parseDateValue(start);
            if(date){
                rows.push({ row: $(this), startDate: date });
            }
        });

        if(rows.length === 0){
            $('.toggle-active').prop('checked', false);
            return;
        }

        rows.sort(function(a,b){ return a.startDate - b.startDate; });
        let today = new Date();
        today.setHours(0,0,0,0,0);

        let activeRow = null;
        for(let i = 0; i < rows.length; i++){
            if(today >= rows[i].startDate){
                let next = rows[i + 1];
                if(!next || today < next.startDate){
                    activeRow = rows[i];
                    break;
                }
            }
        }

        $('.toggle-active').prop('checked', false);
        if(activeRow){
            activeRow.row.find('.toggle-active').prop('checked', true);
        }
    }

    // INIT PLUGINS
    function initPlugins(context = document) {
        $(context).find('.select2').each(function () {
            if (!$(this).hasClass("select2-hidden-accessible")) {
                $(this).select2({ width: '100%' });
            }
        });
        $(context).find('.start_date').each(function () { if (!this._flatpickr) { flatpickr(this, { allowInput:true, dateFormat:"Y-m-d" }); } });
    }

    // BUTTON LOGIC
    function updateButtons() {
        let rows = $('#workhour-wrapper .workhour-row');
        rows.each(function (index) {
            let addBtn = $(this).find('.add-row');
            let removeBtn = $(this).find('.remove-row');
            if (rows.length === 1) { addBtn.show(); removeBtn.hide(); }
            else { addBtn.toggle(index === rows.length-1); removeBtn.show(); }
        });
    }

    function preventDuplicateWorkhour() {
        let selectedValues = [];
        $('select[name="workhour_id[]"]').each(function(){ let val=$(this).val(); if(val) selectedValues.push(val); });
        $('select[name="workhour_id[]"] option').prop('disabled', false);
        $('select[name="workhour_id[]"]').each(function(){
            let current=$(this).val();
            $(this).find('option').each(function(){
                let val=$(this).val();
                if(val && val!==current && selectedValues.includes(val)) $(this).prop('disabled', true);
            });
        });
        $('.select2').trigger('change.select2');
    }

    $(document).on('change', 'select[name="workhour_id[]"]', function(){
        preventDuplicateWorkhour();
        updateWorkhourDetail($(this).closest('.workhour-row'));
    });

    $(document).on('change', '.start_date', function(){
        let row = $(this).closest('.workhour-row');
        preventDuplicateWorkhour();
        updateWorkhourDetail(row);
        updateActiveSchedule();
    });

    // ADD ROW
    $(document).on('click', '.add-row', function () {
        let newRow = $('#workhour-row-template .workhour-row').clone();
        // RESET SELECT2
        newRow.find('select')
            .val('')
            .removeClass('select2-hidden-accessible')
            .removeAttr('data-select2-id')
            .removeAttr('aria-hidden')
            .removeAttr('tabindex');
        newRow.find('.select2-container').remove();
        // RESET INPUT
        newRow.find('input').val('');
        newRow.find('.toggle-active').prop('checked', false);
        // HAPUS FLATPICKR INSTANCE (jaga-jaga)
        newRow.find('.start_date').each(function(){
            if(this._flatpickr){
                this._flatpickr.destroy();
            }
        });
        // APPEND
        $('#workhour-wrapper').append(newRow);
        // INIT ULANG
        initPlugins(newRow);
        updateButtons();
        preventDuplicateWorkhour();
        updateWorkhourDetail(newRow);
        updateActiveSchedule();
    });

    // REMOVE ROW
    $(document).on('click', '.remove-row', function () {
        if ($('.workhour-row').length > 1) { $(this).closest('.workhour-row').remove(); updateButtons(); }
        else { Swal.fire({ icon:'warning', title:'Minimal 1 row harus ada' }); }
        preventDuplicateWorkhour();
    });

    // TOGGLE ACTIVE
    $(document).on('change', '.toggle-active', function () {
        let row = $(this).closest('.workhour-row');

        if($(this).is(':checked')){
            // Uncheck semua toggle lain
            $('.toggle-active').not(this).prop('checked', false);

            // Set start_date ke besok
            let tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            let tomorrowStr = tomorrow.toISOString().split('T')[0];

            row.find('.start_date').val(tomorrowStr);
            // Trigger flatpickr jika ada
            if(row.find('.start_date')[0]._flatpickr){
                row.find('.start_date')[0]._flatpickr.setDate(tomorrowStr);
            }
        }
    });
    // DATATABLE
    let groupId = "{{ encrypt($group->id) }}";
    let url = "{{ route('group-employee-workhour.employeeByGroup', ':id') }}";
    url = url.replace(':id', groupId);
    let groupEmployeeTable = $('#table-group-employee-workhour').DataTable({
        scrollY : "400px",
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: url,
            data: function(d) {
                d.area = $('#filter_area').val();
                d.department = $('#filter_department').val();
                d.section = $('#filter_section').val();
                d.building = $('#filter_building').val();
            }
        },
        columns: [
            {
                data: 'checkbox',
                orderable: false,
                searchable: false
            },
            { data: 'fullname' },
            { data: 'area' },
            { data: 'department' },
            { data: 'section' },
            { data: 'building' }
        ]
    });

    // FILTER
    $('#filter_area, #filter_department, #filter_section, #filter_building').on('change', function(){
        groupEmployeeTable.ajax.reload();
    });

    // CHECKBOX LOGIC
    groupEmployeeTable.on('draw', function(){
        $('.employee_checkbox').each(function(){ $(this).prop('checked', selectedEmployees.includes($(this).val())); });
        $('#select_all').prop('checked', groupEmployeeTable.rows({search:'applied'}).nodes().to$().find('.employee_checkbox:checked').length === groupEmployeeTable.rows({search:'applied'}).nodes().length);
    });

    $('#select_all').on('change', function(){
        let checked = $(this).prop('checked');
        groupEmployeeTable.rows({search:'applied'}).nodes().to$().find('.employee_checkbox').each(function(){
            $(this).prop('checked', checked);
            let id=$(this).val();
            if(checked && !selectedEmployees.includes(id)) selectedEmployees.push(id);
            if(!checked) selectedEmployees = [];
        });
        $('#selected_count').text(selectedEmployees.length + " employee selected");
    });

    $(document).on('change', '.employee_checkbox', function(){
        let id=$(this).val();
        if($(this).is(':checked')) { if(!selectedEmployees.includes(id)) selectedEmployees.push(id); }
        else { selectedEmployees = selectedEmployees.filter(e=>e!=id); }
        $('#selected_count').text(selectedEmployees.length + " employee selected");
    });

    // SUBMIT FORM
    $('#form-create').on('submit', function(e){
        e.preventDefault();
        let workhour_id = [];
        let start_date = [];
        let is_active = [];
        $('#workhour-wrapper .workhour-row').each(function () {
            let wh = $(this).find('select[name="workhour_id[]"]').val();
            let start = $(this).find('input[name="start_date[]"]').val();
            let isChecked = $(this).find('.toggle-active').is(':checked');
            // is_active hanya berdasarkan toggle state
            let active = isChecked ? 1 : 0;
            if (!wh && !start) return;
            workhour_id.push(wh);
            start_date.push(start);
            is_active.push(active);
        });
        // VALIDASI
        if(workhour_id.length === 0){
            Swal.fire('Error','Minimal 1 workhour harus diisi','error');
            return;
        }
        if(workhour_id.includes("")){
            Swal.fire('Error','Workhour harus dipilih semua','error');
            return;
        }
        if(start_date.includes("")){
            Swal.fire('Error','Semua start date harus diisi','error');
            return;
        }
        // VALIDASI START DATE UNIK
        let uniqueStartDates = [...new Set(start_date)];
        if(uniqueStartDates.length !== start_date.length){
            Swal.fire('Error','Start date harus unik, tidak boleh ada yang sama','error');
            return;
        }
        // VALIDASI: harus ada tepat 1 toggle yang checked
        let activeCount = is_active.filter(function (v){ return v === 1; }).length;
        if(activeCount !== 1){
            Swal.fire('Error','Harus ada tepat 1 workhour yang diaktifkan','error');
            return;
        }
        // if(selectedEmployees.length === 0){
        //     Swal.fire('Error','Pilih minimal 1 employee','error');
        //     return;
        // }
        submitForm(workhour_id, start_date, is_active);
    });

    function submitForm(workhour_id, start_date, is_active){
        $.ajax({
            url:"{{ route('group-employee-workhour.update') }}",
            type:"PUT",
            data:{
                group_id:$('input[name="group_id"]').val(),
                group_name:$('input[name="group_name"]').val(),
                workhour_id, start_date, is_active,
                employee_id:selectedEmployees
            },
            success:function(response){
                Swal.fire("Success", response.message, "success")
                .then(() => {
                    window.location.href = "{{ route('group-employee-workhour.index') }}";
                });
            },
            error:function(xhr){
                let msg = "Something went wrong";
                if(xhr.status === 422){
                    if(xhr.responseJSON.errors){
                        msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                    } else if(xhr.responseJSON.message){
                        msg = xhr.responseJSON.message;
                    }
                }
                Swal.fire({
                    icon: "error",
                    title: "Validation Error",
                    html: msg
                });
            }
        });
    }

    initPlugins();
    $('#workhour-wrapper .workhour-row').each(function(){ updateWorkhourDetail($(this)); });
    updateButtons();
    preventDuplicateWorkhour();
    updateActiveSchedule();

    // Initialize select2 for filter dropdowns
    $('#filter_area, #filter_department, #filter_section, #filter_building').select2({
        width: '100%'
    });
    $('#transfer-in-filter-area, #transfer-in-filter-department, #transfer-in-filter-section, #transfer-in-filter-building').select2({
        dropdownParent: $('#transferInModal')
    });

    // TRANSFER IN FUNCTIONALITY
    let employeesToAdd = [];
    let transferInSelectedEmployees = [];

    // TRANSFER IN MODAL DATATABLE
    let transferInTable = $('#table-transfer-in').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        scrollY: "400px",
        ajax: {
            url: "{{ route('group-employee-workhour.employeeByGroupForTransferIn') }}",
            data: function(d) {
                d.area = $('#transfer-in-filter-area').val();
                d.department = $('#transfer-in-filter-department').val();
                d.section = $('#transfer-in-filter-section').val();
                d.building = $('#transfer-in-filter-building').val();
                d.group_id = "{{ $group->id }}";
            }
        },
        columns: [
            {
                data: null,
                render: function(d, type, row) {
                    return `<input type="checkbox" class="transfer-in-checkbox" value="${row.id}" data-fullname="${row.fullname}" data-area="${row.area}" data-department="${row.department}" data-section="${row.section}" data-building="${row.building}" data-group="${row.group_name || 'No Group'}">`;
                },
                orderable: false,
                searchable: false
            },
            { data: 'fullname' },
            { data: 'area' },
            { data: 'department' },
            { data: 'section' },
            { data: 'building' },
            { data: 'group_name', render: function(d) { return d || 'No Group'; } }
        ],
        drawCallback: function(){
            // Reset select all checkbox state after draw
            let totalCheckboxes = $('.transfer-in-checkbox').length;
            let checkedCheckboxes = $('.transfer-in-checkbox:checked').length;
            $('#select-all-transfer-in').prop('checked', totalCheckboxes === checkedCheckboxes && totalCheckboxes > 0);
        }
    });

    // FILTER TRANSFER IN
    $('#transfer-in-filter-area, #transfer-in-filter-department, #transfer-in-filter-section, #transfer-in-filter-building').on('change', function(){
        transferInTable.ajax.reload();
    });

    // TRANSFER IN BUTTON
    $('#btn-transfer-in').on('click', function(){
        transferInSelectedEmployees = [];
        $('#select-all-transfer-in').prop('checked', false);
        $('#transferInModal').modal('show');
    });

    // RELOAD TRANSFER IN TABLE WHEN MODAL SHOWS
    $('#transferInModal').on('shown.bs.modal', function(){
        transferInTable.ajax.reload(null, false);
        transferInTable.columns.adjust().responsive.recalc();
    });

    // SELECT ALL CHECKBOX FOR TRANSFER IN
    $('#select-all-transfer-in').on('change', function(){
        let checked = $(this).prop('checked');
        $('.transfer-in-checkbox').each(function(){
            $(this).prop('checked', checked);
            let employeeId = $(this).val();
            if(checked && !transferInSelectedEmployees.includes(employeeId)){
                transferInSelectedEmployees.push(employeeId);
            } else if(!checked) {
                transferInSelectedEmployees = [];
            }
        });
    });

    // INDIVIDUAL CHECKBOX FOR TRANSFER IN
    $(document).on('change', '.transfer-in-checkbox', function(){
        let employeeId = $(this).val();
        if($(this).is(':checked')) {
            if(!transferInSelectedEmployees.includes(employeeId)){
                transferInSelectedEmployees.push(employeeId);
            }
        } else {
            transferInSelectedEmployees = transferInSelectedEmployees.filter(id => id != employeeId);
        }
        // Update select all checkbox
        let totalCheckboxes = $('.transfer-in-checkbox').length;
        let checkedCheckboxes = $('.transfer-in-checkbox:checked').length;
        $('#select-all-transfer-in').prop('checked', totalCheckboxes === checkedCheckboxes && totalCheckboxes > 0);
    });

    // TRANSFER IN MODAL BUTTON
    $('#btn-transfer-in-modal').on('click', function(){
        if(transferInSelectedEmployees.length === 0){
            Swal.fire({ icon: 'warning', title: 'Please select employees to transfer' });
            return;
        }

        // Get employee data from checkboxes
        let employeesToTransfer = [];
        $('.transfer-in-checkbox:checked').each(function(){
            let checkbox = $(this);
            employeesToTransfer.push({
                id: checkbox.val(),
                fullname: checkbox.data('fullname'),
                area: checkbox.data('area'),
                department: checkbox.data('department'),
                section: checkbox.data('section'),
                building: checkbox.data('building'),
                group_name: checkbox.data('group')
            });
        });

        // Check for duplicates
        let duplicateFound = false;
        employeesToTransfer.forEach(emp => {
            // Check if already in employeesToAdd list
            if(employeesToAdd.some(existing => existing.id === emp.id)){
                Swal.fire({ icon: 'warning', title: `Employee ${emp.fullname} is already in the list` });
                duplicateFound = true;
                return false;
            }
            // Check if already in current group (existing employees)
            if(existingEmployeeIds.includes(emp.id.toString())){
                Swal.fire({ icon: 'warning', title: `Employee ${emp.fullname} is already in this group` });
                duplicateFound = true;
                return false;
            }
        });

        if(duplicateFound) return;

        // Add all selected employees to the list
        employeesToAdd = employeesToAdd.concat(employeesToTransfer);
        updateEmployeesToAddTable();

        // Reset selections and close modal
        transferInSelectedEmployees = [];
        $('.transfer-in-checkbox').prop('checked', false);
        $('#select-all-transfer-in').prop('checked', false);
        $('#transferInModal').modal('hide');
    });

    // UPDATE EMPLOYEES TO ADD TABLE
    function updateEmployeesToAddTable(){
        let tbody = $('#employees-to-add-body');
        tbody.empty();

        if(employeesToAdd.length > 0){
            $('#employees-to-add-section').show();
            employeesToAdd.forEach(function(emp, index){
                tbody.append(`
                    <tr>
                        <td>${emp.fullname}</td>
                        <td>${emp.area}</td>
                        <td>${emp.department}</td>
                        <td>${emp.section}</td>
                        <td>${emp.building}</td>
                        <td>${emp.group_name}</td>
                        <td> <button type="button" class="btn btn-sm btn-danger btn-remove-employee" data-index="${index}">
                                <i class="ri-delete-bin-line"></i> Remove
                            </button>
                        </td>
                    </tr>
                `);
            });
        } else {
            $('#employees-to-add-section').hide();
        }
    }

    // REMOVE EMPLOYEE FROM LIST
    $(document).on('click', '.btn-remove-employee', function(){
        let index = $(this).data('index');
        employeesToAdd.splice(index, 1);
        updateEmployeesToAddTable();
    });

    // SAVE TRANSFER IN
    $('#btn-save-transfer-in').on('click', function(){
        if(employeesToAdd.length === 0){
            Swal.fire({ icon: 'warning', title: 'No employees to add' });
            return;
        }

        let employeeIds = employeesToAdd.map(emp => emp.id);
        $.ajax({
            url: "{{ route('group-employee-workhour.transferIn') }}",
            type: 'POST',
            data: {
                group_id: "{{ $group->id }}",
                employee_ids: employeeIds
            },
            success: function(response){
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: response.message
                }).then(() => {
                    // Add to selectedEmployees
                    employeeIds.forEach(id => {
                        if(!selectedEmployees.includes(id.toString())){
                            selectedEmployees.push(id.toString());
                        }
                        // Also add to existingEmployeeIds
                        if(!existingEmployeeIds.includes(id.toString())){
                            existingEmployeeIds.push(id.toString());
                        }
                    });
                    // Clear employees to add
                    employeesToAdd = [];
                    updateEmployeesToAddTable();
                    // Reload main table
                    groupEmployeeTable.ajax.reload();
                });
            },
            error: function(xhr){
                let msg = xhr.responseJSON?.message || 'Something went wrong';
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: msg
                });
            }
        });
    });

    // CANCEL TRANSFER IN
    $('#btn-cancel-transfer-in').on('click', function(){
        employeesToAdd = [];
        updateEmployeesToAddTable();
    });

    // TRANSFER OUT FUNCTIONALITY
    let transferOutSelectedEmployees = [];

    // TRANSFER OUT MODAL DATATABLE
    let transferOutTable = $('#table-transfer-out').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        scrollY: "300px",
        ajax: {
            url: "{{ route('group-employee-workhour.employeeByGroup', ':id') }}".replace(':id', "{{ encrypt($group->id) }}"),
            type: 'GET'
        },
        columns: [
            {
                data: 'checkbox',
                orderable: false,
                searchable: false
            },
            { data: 'fullname' },
            { data: 'area' },
            { data: 'department' },
            { data: 'section' },
            { data: 'building' }
        ]
    });

    // TRANSFER OUT BUTTON
    $('#btn-transfer-out').on('click', function(){
        // Load available groups (exclude current group)
        $.ajax({
            url: "{{ route('group-employee-workhour.getGroups') }}",
            type: 'GET',
            success: function(response){
                $('#select-target-group').empty().append('<option value="">Choose Target Group</option>');
                response.forEach(function(group){
                    if(group.id != "{{ $group->id }}") { // Exclude current group
                        $('#select-target-group').append(`<option value="${group.id}">${group.name}</option>`);
                    }
                });
                $('#select-target-group').select2({
                    dropdownParent: $('#transferOutModal')
                });
            }
        });
        $('#transferOutModal').modal('show');
    });

    // RELOAD TRANSFER OUT TABLE WHEN MODAL SHOWS
    $('#transferOutModal').on('shown.bs.modal', function(){
        transferOutTable.ajax.reload(null, false);
        transferOutTable.columns.adjust().responsive.recalc();
        transferOutSelectedEmployees = [];
        $('#transfer-out-selected-count').text('0 Employee selected');
    });

    // TRANSFER OUT CHECKBOX LOGIC
    transferOutTable.on('draw', function(){
        $('.transfer-out-checkbox').each(function(){
            $(this).prop('checked', transferOutSelectedEmployees.includes($(this).val()));
        });
        updateTransferOutSelectAll();
    });
    function updateTransferOutSelectAll(){
        let totalVisible = transferOutTable.rows({search:'applied'}).nodes().length;
        let checkedVisible = transferOutTable.rows({search:'applied'}).nodes().to$().find('.transfer-out-checkbox:checked').length;
        $('#select-all-transfer-out').prop('checked', totalVisible > 0 && checkedVisible === totalVisible);
    }
    $('#select-all-transfer-out').on('change', function(){
        let checked = $(this).prop('checked');
        transferOutTable.rows({search:'applied'}).nodes().to$().find('.transfer-out-checkbox').each(function(){
            let id = $(this).val();
            $(this).prop('checked', checked);

            if(checked && !transferOutSelectedEmployees.includes(id)) {
                transferOutSelectedEmployees.push(id);
            }
            if(!checked) {
                transferOutSelectedEmployees = transferOutSelectedEmployees.filter(e => e != id);
            }
        });
        $('#transfer-out-selected-count').text(`${transferOutSelectedEmployees.length} Employee selected`);
    });

    $(document).on('change', '.transfer-out-checkbox', function(){
        let id = $(this).val();
        if($(this).is(':checked')) {
            if(!transferOutSelectedEmployees.includes(id)) transferOutSelectedEmployees.push(id);
        } else {
            transferOutSelectedEmployees = transferOutSelectedEmployees.filter(e => e != id);
        }

        updateTransferOutSelectAll();
        $('#transfer-out-selected-count').text(`${transferOutSelectedEmployees.length} Employee selected`);
    });

    // CONFIRM TRANSFER OUT
    $('#btn-confirm-transfer-out').on('click', function(){
        let targetGroup = $('#select-target-group').val();

        if (!targetGroup) {
            Swal.fire({ icon:'warning', title:'Please select a target group.' });
            return;
        }

        if (transferOutSelectedEmployees.length === 0) {
            Swal.fire({ icon:'warning', title:'Please select at least one employee.' });
            return;
        }

        Swal.fire({
            title: 'Confirm Transfer Out',
            text: `Move ${transferOutSelectedEmployees.length} employees to selected group?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Transfer'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('group-employee-workhour.transferOut') }}",
                    type: 'POST',
                    data: {
                        current_group_id: "{{ $group->id }}",
                        target_group_id: targetGroup,
                        employee_ids: transferOutSelectedEmployees
                    },
                    success: function(response){
                        Swal.fire({
                            icon: 'success',
                            title: 'Transfer Successful',
                            text: response.message
                        }).then(() => {
                            $('#transferOutModal').modal('hide');
                            // Remove from selectedEmployees
                            transferOutSelectedEmployees.forEach(id => {
                                selectedEmployees = selectedEmployees.filter(e => e != id);
                            });
                            // Reload main table
                            groupEmployeeTable.ajax.reload();
                        });
                    },
                    error: function(xhr){
                        let msg = xhr.responseJSON?.message || 'Something went wrong';
                        Swal.fire({
                            icon: 'error',
                            title: 'Transfer Failed',
                            text: msg
                        });
                    }
                });
            }
        });
    });

});
</script>
@endsection
