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
            <h4 class="mb-sm-0">Group Employee Work Hour</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Master</a></li>
                    <li class="breadcrumb-item active">Group Employee</li>
                </ol>
            </div>

        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header align-items-center d-flex gap-4">
                <div>
                    <a href="{{ route('group-employee-workhour.create') }}"
                    class="btn btn-primary btn-label waves-effect waves-light">
                        <i class="ri-add-circle-line label-icon align-middle fs-16 me-2"></i>
                        Add New Group
                </a>
                </div>
                <div>
                    <button type="button" id="btn-open-find-employee-modal" class="btn btn-primary btn-label waves-effect waves-light">
                        <i class="ri-add-circle-line label-icon align-middle fs-16 me-2"></i>
                        Find Employee
                    </button>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-striped bordered" id="table-group-employee-workhour">
                <thead>
                    <tr>
                    <th scope="col" style="text-align:center">No</th>
                    <th scope="col" style="text-align:center">Group</th>
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

<!-- FIND EMPLOYEE MODAL -->
<div class="modal fade" id="findEmployeeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-between align-items-center">
                <h5 class="modal-title">Find Employee</h5>
                <div>
                    <button type="button" id="btn-transfer-to" class="btn btn-success me-2">Transfer To</button>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
                <div class="modal-body">
                    <div class="table-responsive">
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
                    <table class="table table-bordered" id="table-find-employee">
                        <thead class="table-light">
                            <tr>
                                <th style="width:40px;"><input type="checkbox" id="modal_select_all"></th>
                                <th>Name</th>
                                <th>Area</th>
                                <th>Department</th>
                                <th>Section</th>
                                <th>Building</th>
                                <th>Group</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <p class="text-muted mt-2" id="modal_selected_count">0 Employee selected</p>
            </div>
        </div>
    </div>
</div>

<!-- TRANSFER EMPLOYEE MODAL -->
<div class="modal fade" id="transferEmployeeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Transfer Employee</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="select-group" class="form-label">Select Target Group</label>
                    <select id="select-group" class="form-select select2">
                        <option value="">Choose Group</option>
                        <!-- Options will be loaded via AJAX -->
                    </select>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered" id="table-selected-employees">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Area</th>
                                <th>Department</th>
                                <th>Section</th>
                                <th>Building</th>
                                <th>Current Group</th>
                            </tr>
                        </thead>
                        <tbody id="selected-employees-body">
                            <!-- Selected employees will be populated here -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="btn-confirm-transfer" class="btn btn-primary">Confirm Transfer</button>
            </div>
        </div>
    </div>
</div>



<!--Modal staticbackdrop-->
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
        //DATATABLE GROUP EMPLOYEE
        let groupEmployeeTable = $('#table-group-employee-workhour').DataTable({
        processing:true,
        serverSide:true,
        responsive:true,
        autoWidth: false,
        ajax:"{{ route('group-employee-workhour.index') }}",
        columns:[
            {data:'DT_RowIndex', name:'DT_RowIndex', className : "text-center", orderable:false, searchable:false},
            {data:'group_name', name:'name', className: "text-center"},
            {data:'total_employee', name:'total_employee', className: "text-center"},
            {data:'action', name:'action', className:"text-center", orderable:false, searchable:false}
        ]
    });
        //EDIT EMPLOYEE WORK HOUR
        $('#table-group-employee-workhour').on("click", ".edit-btn", function() {
            let id = $(this).data("id");
            let url = "{{ route('group-employee-workhour.edit', ':id') }}";
            url = url.replace(':id', id);
            window.location.href = url;
        });
        //DELETE EMPLOYEE WORKHOUR
        $('#table-group-employee-workhour').on("click", ".delete-btn", function () {
            let id = $(this).data("id");
            Swal.fire({
                title: "Are you sure?",
                text: "Group Employee Work Hour will be deleted!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#6c757d",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('group-employee-workhour.destroy', ':id') }}".replace(':id', id),
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
                            $('#table-group-employee-workhour').DataTable().ajax.reload(null,false);
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
        // FIND EMPLOYEE MODAL TABLE
        let selectedEmployees = [];
        let findEmployeeTable = $('#table-find-employee').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            scrollY: "450px",
            ajax: {
                url: "{{ route('group-employee-workhour.findEmployee') }}",
                data: function(d) {
                    d.area = $('#filter_area').val();
                    d.department = $('#filter_department').val();
                    d.section = $('#filter_section').val();
                    d.building = $('#filter_building').val();
                }
            },
            columns: [
                {data:'id', render:function(d){ return `<input type="checkbox" class="employee_checkbox_modal" value="${d}">`; }, orderable:false, searchable:false},
                {data:'fullname'},
                {data:'area'},
                {data:'department'},
                {data:'section'},
                {data:'building'},
                {data:'group_name'}
            ]
        });

        $('#filter_area, #filter_department, #filter_section, #filter_building').on('change', function(){
            findEmployeeTable.ajax.reload();
        });

        function refreshSelectedCount(){
            $('#modal_selected_count').text(`${selectedEmployees.length} employee selected`);
        }
        findEmployeeTable.on('draw', function(){
            // Sync checkbox states with selectedEmployees store
            findEmployeeTable.rows({search:'applied'}).nodes().to$().find('.employee_checkbox_modal').each(function(){
                let id = $(this).val();
                $(this).prop('checked', selectedEmployees.includes(id));
            });

            let totalVisible = findEmployeeTable.rows({search:'applied'}).nodes().length;
            let checkedVisible = findEmployeeTable.rows({search:'applied'}).nodes().to$().find('.employee_checkbox_modal:checked').length;
            $('#modal_select_all').prop('checked', totalVisible > 0 && checkedVisible === totalVisible);
            refreshSelectedCount();
        });

        $('#modal_select_all').on('change', function(){
            let checked = $(this).prop('checked');
            findEmployeeTable.rows({search:'applied'}).nodes().to$().find('.employee_checkbox_modal').each(function(){
                let id = $(this).val();
                $(this).prop('checked', checked);

                if(checked && !selectedEmployees.includes(id)) {
                    selectedEmployees.push(id);
                }
                if(!checked) {
                    selectedEmployees = selectedEmployees.filter(e => e != id);
                }
            });
            refreshSelectedCount();
        });

        $(document).on('change', '.employee_checkbox_modal', function(){
            let id = $(this).val();
            if($(this).is(':checked')) {
                if(!selectedEmployees.includes(id)) selectedEmployees.push(id);
            } else {
                selectedEmployees = selectedEmployees.filter(e => e != id);
            }

            let totalVisible = findEmployeeTable.rows({search:'applied'}).nodes().length;
            let checkedVisible = findEmployeeTable.rows({search:'applied'}).nodes().to$().find('.employee_checkbox_modal:checked').length;
            $('#modal_select_all').prop(totalVisible > 0 && checkedVisible === totalVisible);
            refreshSelectedCount();
        });

        $('#btn-open-find-employee-modal').on('click', function(){
            $('#findEmployeeModal').modal('show');
        });

        $('#findEmployeeModal').on('shown.bs.modal', function(){
            findEmployeeTable.ajax.reload(null, false);
            findEmployeeTable.columns.adjust().responsive.recalc();
        });


        $('#btn-transfer-to').on('click', function(){
            let selectedData = [];
            if(selectedEmployees.length === 0){
                Swal.fire({ icon:'warning', title:'Please select at least one employee.' });
                return;
            }
            $('#table-find-employee .employee_checkbox_modal').each(function(){
                let id = $(this).val();
                if(selectedEmployees.includes(id)){
                    let row = $(this).closest('tr');
                    selectedData.push({
                        id: id,
                        fullname: row.find('td').eq(1).text(),
                        area: row.find('td').eq(2).text(),
                        department: row.find('td').eq(3).text(),
                        section: row.find('td').eq(4).text(),
                        building: row.find('td').eq(5).text(),
                        group_name: row.find('td').eq(6).text()
                    });
                }
            });
            let selected = selectedEmployees.slice();
            $('#findEmployeeModal').modal('hide');
            $('#selected-employees-body').empty();
            selectedData.forEach(function(emp){
                $('#selected-employees-body').append(`
                    <tr>
                        <td>${emp.fullname}</td>
                        <td>${emp.area}</td>
                        <td>${emp.department}</td>
                        <td>${emp.section}</td>
                        <td>${emp.building}</td>
                        <td>${emp.group_name}</td>
                    </tr>
                `);
            });

            // Load group options via AJAX
            $.ajax({
                url: "{{ route('group-employee-workhour.getGroups') }}",
                type: 'GET',
                success: function(response){
                    $('#select-group').empty().append('<option value="">Choose Group</option>');
                    response.forEach(function(group){
                        $('#select-group').append(`<option value="${group.id}">${group.name}</option>`);
                    });
                    // Initialize Select2 after options are loaded
                    $('#select-group').select2({
                        dropdownParent: $('#transferEmployeeModal')
                    });
                }
            });
            $('#transferEmployeeModal').modal('show');
            $('#transferEmployeeModal').data('selectedEmployees', selected);
        });
        $('#btn-confirm-transfer').on('click', function(){
            let selectedEmployees = $('#transferEmployeeModal').data('selectedEmployees');
            let targetGroup = $('#select-group').val();

            if (!targetGroup) {
                Swal.fire({ icon:'warning', title:'Please select a target group.' });
                return;
            }
            Swal.fire({
                title: 'Confirm Transfer',
                text: `Transfer ${selectedEmployees.length} employees to selected group?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, Transfer'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('group-employee-workhour.transferTo') }}", // Assume route exists
                        type: 'POST',
                        data: {
                            employees: selectedEmployees,
                            target_group: targetGroup
                        },
                        success: function(response){
                            Swal.fire({
                                icon: 'success',
                                title: 'Transfer Successful',
                                text: response.message
                            });
                            $('#transferEmployeeModal').modal('hide');
                            // Reload tables if needed
                            groupEmployeeTable.ajax.reload();
                        },
                        error: function(xhr){
                            Swal.fire({
                                icon: 'error',
                                title: 'Transfer Failed',
                                text: xhr.responseJSON?.message || 'Something went wrong'
                            });
                        }
                    });
                }
            });
        });
        // Initialize flatpickr and select2
        $('#date_start').flatpickr({
            allowInput: true,
            altInput: false,
            altFormat: "d F, Y",
            dateFormat: "Y-m-d",
        });
        $('#date_end').flatpickr({
            allowInput: true,
            altInput: false,
            altFormat: "d F, Y",
            dateFormat: "Y-m-d",
        });
        $('#filter_area, #filter_department, #filter_section, #filter_building').select2({
            dropdownParent: $('#findEmployeeModal')
        });
        $('#select-group').select2({
            dropdownParent: $('#transferEmployeeModal')
        });
    });
</script>
@endsection
