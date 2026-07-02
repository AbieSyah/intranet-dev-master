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
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex justify-content-between align-items-center">
            <h4>Form Group Employee Work Hour</h4>
        </div>
    </div>
</div>

<form id="form-create" method="POST" action="{{ route('group-employee-workhour.store') }}">
    @csrf
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
            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Group Name</label>
                    <input type="text" name="group_name" class="form-control" placeholder="Input Group Name" required>
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
        </div>

        {{-- WORKHOUR SCHEDULE --}}
        <div class="card mb-3">
            <div class="card-header"><strong>Workhour Schedule</strong></div>
            <div class="card-body px-4">
                <div id="workhour-wrapper">
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
                            <div class="workhour-detail text-muted medium" style="min-height: 45px;">-</div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Start Date</label>
                            <div class="input-group">
                                <input type="text" name="start_date[]" class="form-control bulan start_date" placeholder="Pilih Tanggal">
                                <span class="input-group-text"><i class="ri-calendar-event-line"></i></span>
                            </div>
                        </div>

                        {{-- <div class="col-md-3">
                            <label class="form-label">End Date</label>
                            <div class="input-group">
                                <input type="text" name="end_date[]" class="form-control bulan end_date" placeholder="Pilih Tanggal">
                                <span class="input-group-text"><i class="ri-calendar-event-line"></i></span>
                            </div>
                        </div> --}}
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
                            <div class="workhour-detail text-muted medium" style="min-height: 45px;">-</div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Start Date</label>
                            <div class="input-group">
                                <input type="text" name="start_date[]" class="form-control bulan start_date" placeholder="Pilih Tanggal">
                                <span class="input-group-text"><i class="ri-calendar-event-line"></i></span>
                            </div>
                        </div>
                        {{-- <div class="col-md-3">
                            <label class="form-label">End Date</label>
                            <div class="input-group">
                                <input type="text" name="end_date[]" class="form-control bulan end_date" placeholder="Pilih Tanggal">
                                <span class="input-group-text"><i class="ri-calendar-event-line"></i></span>
                            </div>
                        </div> --}}
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

    let selectedEmployees = [];

    let workhourDetails = {
        @foreach($workhours as $workhour)
            '{{ $workhour->id }}': @json($workhour->details->map(function($detail){ return ['day'=>$detail->day, 'work_in'=>$detail->work_in, 'work_out'=>$detail->work_out]; })),
        @endforeach
    };

    function formatWorkhourDescription(details){
        if(!details || !details.length) return '-';

        let byTime = {};
        details.forEach(function(item){
            let key = item.work_in + '|' + item.work_out;
            if(!byTime[key]) byTime[key] = [];
            if(!byTime[key].includes(item.day)) byTime[key].push(item.day);
        });

        let order = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
        function daySort(a,b){ return order.indexOf(a) - order.indexOf(b); }

        let parts = [];
        Object.keys(byTime).forEach(function(key){
            let dayList = byTime[key].sort(daySort);
            let daysText = dayList.join(', ');

            // compress range (Senin, Selasa, Rabu, Kamis -> Senin-Kamis)
            if(dayList.length >= 2){
                let start = dayList[0];
                let end = dayList[dayList.length-1];
                let startIdx = order.indexOf(start);
                let endIdx = order.indexOf(end);
                let expected = order.slice(startIdx, endIdx+1);
                if(JSON.stringify(expected) === JSON.stringify(dayList)){
                    daysText = start + ' - ' + end;
                }
            }

            let [workIn, workOut] = key.split('|');
            parts.push(daysText + ' (' + workIn + ' - ' + workOut + ')');
        });

        return parts.join(', ');
    }

    function updateWorkhourDetail(row){
        let selectedId = row.find('.workhour-select').val();
        let details = workhourDetails[selectedId] || [];
        row.find('.workhour-detail').text(formatWorkhourDescription(details));
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

    $(document).on('change', 'select[name="workhour_id[]"]', function() {
        preventDuplicateWorkhour();
        updateWorkhourDetail($(this).closest('.workhour-row'));
    });

    $(document).on('change', '.start_date', function(){
        let row = $(this).closest('.workhour-row');
        let start = $(this).val();
        let today = new Date().toISOString().split('T')[0];
        let isToday = start === today;
        row.find('.toggle-active').prop('checked', isToday);

        if(isToday){
            $('.workhour-row').not(row).find('.toggle-active').prop('checked', false);
        }

        preventDuplicateWorkhour();
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
    });

    // REMOVE ROW
    $(document).on('click', '.remove-row', function () {
        if ($('.workhour-row').length > 1) { $(this).closest('.workhour-row').remove(); updateButtons(); }
        else { Swal.fire({ icon:'warning', title:'Minimal 1 row harus ada' }); }
        preventDuplicateWorkhour();
    });

    // TOGGLE ACTIVE
    $(document).on('change', '.toggle-active', function () {
        if($(this).is(':checked')){
            $('.toggle-active').not(this).prop('checked', false);
            let today = new Date().toISOString().split('T')[0];
            let row = $(this).closest('.workhour-row');
            // paksa start date hari ini
            row.find('.start_date').val(today);
        }
    });

    // DATATABLE
    let groupEmployeeTable = $('#table-group-employee-workhour').DataTable({
        scrollY:"350px", processing:true, serverSide:true, ajax:"{{ route('group-employee-workhour.employeeList') }}",
        columns:[
            {data:'id', render:function(d){ return `<input type="checkbox" class="employee_checkbox" value="${d}">`; }, orderable:false, searchable:false },
            {data:'fullname'},
            {data:'area'},
            {data:'department'},
            {data:'section'},
            {data:'building'}
        ]
    });

    // FILTER
    $('#filter_area').on('change', function(){ groupEmployeeTable.column(2).search(this.value).draw(); });
    $('#filter_department').on('change', function(){ groupEmployeeTable.column(3).search(this.value).draw(); });
    $('#filter_section').on('change', function(){ groupEmployeeTable.column(4).search(this.value).draw(); });
    $('#filter_building').on('change', function(){ groupEmployeeTable.column(5).search(this.value).draw(); });

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
        let today = new Date().toISOString().split('T')[0];
        let workhour_id = [];
        let start_date = [];
        let is_active = [];
        $('#workhour-wrapper .workhour-row').each(function () {

            let wh = $(this).find('select[name="workhour_id[]"]').val();
            let start = $(this).find('input[name="start_date[]"]').val();
            let active = start === today ? 1 : 0;
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
        let activeCount = is_active.filter(function (v){ return v === 1; }).length;
        if(activeCount !== 1){
            Swal.fire('Error','Harus ada tepat 1 workhour yang start hari ini','error');
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
            url:"{{ route('group-employee-workhour.store') }}",
            type:"POST",
            data:{
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
                // let msg = "Something went wrong";
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
});
</script>
@endsection
