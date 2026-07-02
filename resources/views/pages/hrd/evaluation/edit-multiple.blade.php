@extends('layouts.master')
@section('link')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css" />
    <link href="{{ url('') }}/assets/libs/Datatables/DataTables-1.13.1/css/dataTables.bootstrap5.css" rel="stylesheet" type="text/css" />
    <link href="{{ url('') }}/assets/libs/Datatables/Buttons-2.3.3/css/buttons.dataTables.css" rel="stylesheet" type="text/css" />
    <link href="{{ url('') }}/assets/libs/Datatables/Responsive-2.4.0/css/responsive.bootstrap.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ url('') }}/assets/libs/adminlte/select2/css/4.1.0/select2.min.css">
    <style type="text/css">
        body { background: #f7fbf8; }
        .preview { text-align: center; overflow: hidden; width: 160px; height: 160px; margin: 10px; border: 1px solid red; }
        .section { margin-top: 150px; background: #fff; padding: 50px 30px; }
        .modal-lg { max-width: 1000px !important; }
        .select2-container--default .select2-selection--single { height: calc(2.25rem + 2px); padding: 0.375rem 0.75rem; border: 1px solid #ced4da; border-radius: 0.375rem; }
        .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 1.5rem; }
        .select2-container--default .select2-selection--single .select2-selection__arrow { height: 100%; }
        div.dataTables_wrapper { width: 100%; }
        .btn-with-badge { overflow: visible; }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Edit Multiple Evaluation</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Multiple Evaluation</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex justify-content-between">
                    <h3 class="card-title">Evaluation</h3>
                    <div class="flex-shrink-0">
                        <a href="{{ route('evaluation.index') }}" class="btn btn-primary btn-label waves-effect waves-light">
                            <i class="ri-arrow-left-fill label-icon align-middle fs-16 me-2"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form class="form" action="{{ route('evaluation.edit-multiple.store') }}" method="post" enctype="multipart/form-data" id="evalForm">
                        @csrf
                        <div class="row gy-3">
                            <div class="col-12">
                                <button id="multi-keep-btn" type="button" title="Keep" class="float-end btn btn-success btn-sm waves-effect waves-light position-relative btn-with-badge me-2" style="display:none;">
                                    <i class="ri-check-line fs-16"></i>
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                        <span id="keep-count">0</span>
                                        <span class="visually-hidden">keep selected</span>
                                    </span>
                                </button>
                                <button id="multi-delete-btn" type="button" title="Delete" class="float-end btn btn-danger btn-sm waves-effect waves-light position-relative btn-with-badge me-2" style="display:none;">
                                    <i class="ri-delete-bin-line fs-16"></i>
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning">
                                        <span id="delete-count">0</span>
                                        <span class="visually-hidden">delete selected</span>
                                    </span>
                                </button>
                            </div>
                            <div class="col-12">
                                <table class="table table-striped bordered display nowrap" style="width:100%" id="table_evaluation">
                                    <thead>
                                        <tr>
                                            <th style="text-align:center">
                                                <input type="checkbox" id="checkAll">
                                            </th>
                                            <th scope="col">No. Evaluation</th>
                                            <th scope="col">Name</th>
                                            <th scope="col">Department</th>
                                            <th scope="col">Period</th>
                                            <th scope="col">Purpose</th>
                                            <th scope="col" class="text-center">Status</th>
                                            <th scope="col" class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                                <input type="hidden" name="evaluation_ids" id="evaluation_ids">
                            </div>
                            <div class="col-12">
                                <hr>
                                <h5 class="text-center mb-0">Note from HRD</h5>
                                <p class="text-center text-danger mt-0 mb-2">(Max. <span id="count_note_hrd">100</span> Character)</p>
                                <textarea class="form-control" id="note_hrd" name="note_hrd" rows="3" maxlength="100">{{ old('note_hrd', $noteHrd) }}</textarea>
                            </div>
                            <div class="col-12">
                                <hr>
                                <h5 class="text-center">Attachments</h5>
                            </div>
                            <div class="col-12 p-2">
                                <div class="table-responsive">
                                    <table class="table table-borderless table-nowrap mb-0" id="attachment-table">
                                        <thead class="align-middle">
                                            <tr class="table-active">
                                                <th scope="col" style="width: 5%;">#</th>
                                                <th scope="col" style="width: 45%;">Attachment Name<span class="text-danger">*</span></th>
                                                <th scope="col" style="width: 40%;">File<span class="text-danger">*</span></th>
                                                <th scope="col" style="width: 10%;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="attachment-list">
                                            @if ($attachments->isNotEmpty())
                                                @foreach ($attachments as $index => $attachment)
                                                    <tr class="existing-attachment-row">
                                                        <th scope="row" class="attachment-id">{{ $index + 1 }}</th>
                                                        <td><div class="mb-2"><input type="text" class="form-control" name="existing_attachment_names[{{ $attachment->id }}]" value="{{ $attachment->name }}" required></div></td>
                                                        <td><div class="mb-2"><a href="{{ asset('storage/' . $attachment->file_path) }}" class="btn btn-primary" target="_blank">View File</a></div></td>
                                                        <td class="attachment-removal"><button type="button" class="btn btn-danger remove-existing-attachment-btn" data-attachment-id="{{ $attachment->id }}">Delete</button></td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                        <tbody>
                                            <tr>
                                                <td colspan="4"><a href="javascript:void(0)" id="add-attachment-item" class="btn btn-soft-secondary fw-medium"><i class="ri-add-fill me-1 align-bottom"></i> Add Attachment</a></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-flex justify-content-end">
                                    <div class="text-center pt-10">
                                        <button type="submit" class="btn btn-primary" data-kt-users-modal-action="submit">
                                            <span class="d-none spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                            <span class="indicator-label">Save</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ url('') }}/assets/js/pages/profile-setting.init.js"></script>
    <script src="{{ url('') }}/assets/libs/Datatables/DataTables-1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="{{ url('') }}/assets/libs/Datatables/DataTables-1.13.1/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ url('') }}/assets/libs/Datatables/Responsive-2.4.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="{{ url('') }}/assets/js/pages/datatables.init.js"></script>
    <script src="{{ url('') }}/assets/libs/adminlte/select2/js/4.1.0/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.js"></script>
@endsection

@section('javascript')
    <script type="text/javascript">
        $(document).ready(function() {
            let evaluationTable;
            let selectedIds = [];

            const statusBadges = {
                'RELEASE': 'success', 'DRAFT': 'secondary', 'REVISE': 'danger', 'REJECT': 'dark',
                '1st Evaluator': 'success', '2nd Evaluator': 'success', '3rd Evaluator': 'success',
                'HRD Approved': 'success', 'Prodir': 'success', 'Presdir': 'success', 'DONE': 'success',
            };

            $(document).on('change', '.row-checkbox', function() {
                const id = $(this).val();
                if (this.checked) {
                    if (!selectedIds.includes(id)) {
                        selectedIds.push(id);
                    }
                } else {
                    selectedIds = selectedIds.filter(val => val !== id);
                }
                updateMultiButtons();
                checkAllStatus();
            });

            $('#checkAll').on('click', function() {
                if (!$(this).is(':disabled')) {
                    const isChecked = this.checked;
                    evaluationTable.rows({ page: 'current' }).nodes().to$().find('.row-checkbox').each(function() {
                        const rowId = $(this).val();
                        if (!$(this).is(':disabled')) {
                            $(this).prop('checked', isChecked);
                            if (isChecked) {
                                if (!selectedIds.includes(rowId)) {
                                    selectedIds.push(rowId);
                                }
                            } else {
                                selectedIds = selectedIds.filter(val => val !== rowId);
                            }
                        }
                    });
                    updateMultiButtons();
                }
            });

            function checkAllStatus() {
                const totalEnabledCheckboxes = $('.row-checkbox:not(:disabled)').length;
                const totalCheckedCheckboxes = $('.row-checkbox:not(:disabled):checked').length;
                $('#checkAll').prop('checked', totalEnabledCheckboxes > 0 && totalEnabledCheckboxes === totalCheckedCheckboxes);
            }

            function updateMultiButtons() {
                const count = selectedIds.length;
                if (count > 1) {
                    $('#multi-delete-btn').show();
                    $('#delete-count').text(count);
                    $('#multi-keep-btn').show();
                    $('#keep-count').text(count);
                } else {
                    $('#multi-delete-btn').hide();
                    $('#multi-keep-btn').hide();
                }
            }

            $(document).on('click', '#multi-delete-btn', function() {
                const count = selectedIds.length;
                if (count === 0) {
                    Swal.fire({ title: 'No Items Selected', text: 'Please select at least one item to delete.', icon: 'info' });
                    return;
                }
                Swal.fire({
                    title: 'Are you sure?',
                    text: `You won't be able to revert the deletion of ${count} items!`,
                    icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'Yes, Delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        evaluationTable.rows(function(idx, data, node) {
                            return selectedIds.includes(data.id.toString());
                        }).remove().draw(false);
                        selectedIds = [];
                        $('.row-checkbox').prop('checked', false);
                        updateMultiButtons();
                        checkAllStatus();
                        Swal.fire('Deleted!', `${count} evaluations have been removed from the list.`, 'success');
                    }
                });
            });

            $(document).on('click', '#multi-keep-btn', function() {
                const count = selectedIds.length;
                Swal.fire({
                    title: 'Are you sure?',
                    text: `You are about to keep ${count} selected items and delete all others.`,
                    icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'Yes, Keep the Selected!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const allData = evaluationTable.rows().data().toArray();
                        const toKeepData = allData.filter(row => selectedIds.includes(row.id.toString()));
                        evaluationTable.clear().draw(false);
                        evaluationTable.rows.add(toKeepData).draw(false);
                        selectedIds = [];
                        $('.row-checkbox').prop('checked', false);
                        updateMultiButtons();
                        checkAllStatus();
                        Swal.fire('Success!', `Only the ${count} selected evaluations have been kept. The others are removed.`, 'success');
                    }
                });
            });

            function initializeDataTable(data) {
                if ($.fn.DataTable.isDataTable('#table_evaluation')) {
                    evaluationTable.destroy();
                }
                evaluationTable = $('#table_evaluation').DataTable({
                    data: data,
                    stateSave: false, responsive: false, autoWidth: false, scrollX: false,
                    order: [[1, 'asc']],
                    columns: [
                        { data: 'id', name: 'id', orderable: false, searchable: false, className: "text-center", render: function(data, type, row, meta) {
                            const isChecked = selectedIds.includes(data.toString());
                            const checkedAttr = isChecked ? 'checked' : '';
                            return `<input type="checkbox" class="row-checkbox" value="${data}" ${checkedAttr}>`;
                        }},
                        { data: 'release_id', name: 'release_id' },
                        { data: 'fullname', name: 'fullname' },
                        { data: 'department', name: 'department' },
                        { data: 'period', name: 'period' },
                        { data: 'purpose', name: 'purpose' },
                        { data: 'status', name: 'status', orderable: false, searchable: false, className: 'text-center', render: function(data, type, row) {
                            const badgeClass = statusBadges[data] || 'secondary';
                            return `<span class="badge bg-${badgeClass}">${data}</span>`;
                        }},
                        { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center', render: function(data, type, row) {
                            return `<button type="button" class="btn btn-danger btn-sm remove-evaluation" data-id="${row.id}"><i class="ri-delete-bin-line"></i></button>`;
                        }}
                    ],
                });
                
                selectedIds = [];
                updateMultiButtons();
                checkAllStatus();

                evaluationTable.on('draw', function() {
                    $('.row-checkbox').each(function() {
                        const id = $(this).val();
                        if (selectedIds.includes(id)) {
                            $(this).prop('checked', true);
                        } else {
                            $(this).prop('checked', false);
                        }
                    });
                    checkAllStatus();
                });
            }

            const initialData = @json($formattedEvaluations);
            initializeDataTable(initialData);

            $(document).on('click', '.remove-evaluation', function() {
                const row = evaluationTable.row($(this).parents('tr'));
                Swal.fire({
                    title: 'Are you sure?', text: "You won't be able to revert this!", icon: 'warning',
                    showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'Yes, Delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const rowId = row.data().id.toString();
                        row.remove().draw(false);
                        selectedIds = selectedIds.filter(val => val !== rowId);
                        updateMultiButtons();
                        checkAllStatus();
                        Swal.fire('Deleted!', 'Evaluation has been removed from the list.', 'success');
                    }
                });
            });

            $("form").submit(function(e) {
                e.preventDefault();
                const evaluationIds = evaluationTable.rows().data().toArray().map(row => row.id);
                if (evaluationIds.length === 0) {
                    Swal.fire('Error', 'Please select at least one evaluation.', 'error');
                    return;
                }
                $('#evaluation_ids').val(JSON.stringify(evaluationIds));
                Swal.fire({ title: 'Saving data...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); }});
                const formData = new FormData(this);
                $.ajax({
                    url: $(this).attr("action"), method: "POST", data: formData, processData: false, contentType: false,
                    success: function(response) {
                        Swal.close();
                        Swal.fire({ title: "Success", text: response.message, icon: "success", buttonsStyling: false, confirmButtonText: "Ok, got it!", customClass: { popup: 'swal2-noanimation', confirmButton: "btn btn-primary" }
                        }).then(() => { window.location.href = response.redirect; });
                    },
                    error: function(xhr, status, error) {
                        Swal.close();
                        handleErrorResponse(xhr.responseJSON);
                    }
                });
            });

            function handleErrorResponse(responseJson) {
                let errorMessage = '';
                if (responseJson.message) { errorMessage += `<h4 class="text-danger">${responseJson.message}</h4>`; }
                if (responseJson.errors) { for (const fieldName in responseJson.errors) { errorMessage += `<p class="text-danger">${responseJson.errors[fieldName][0]}</p>`; }}
                if (responseJson.responseText) { errorMessage += `<p class="text-danger">${responseJson.responseText}</p>`; }
                if (errorMessage === '') { errorMessage += '<p class="text-danger">An error occurred.</p>'; }
                Swal.fire({
                    title: 'Error', html: errorMessage, icon: 'error', buttonsStyling: false,
                    customClass: { confirmButton: 'btn btn-primary' }
                });
            }
        });

        $(document).ready(function() {
            function addAttachmentRow(defaultName = '') {
                const currentTotalRows = $('#attachment-list tr').length;
                const newCounter = currentTotalRows + 1;
                const newRow = `
                <tr class="new-attachment-row">
                    <th scope="row" class="attachment-id">${newCounter}</th>
                    <td><div class="mb-2">
                    <input 
                        type="text" 
                        class="form-control" 
                        name="new_attachment_names[]" 
                        placeholder="e.g., Attendance List" 
                        value="${defaultName}" 
                        required>
                    </div></td>
                    <td><div class="mb-2"><input type="file" class="form-control" name="new_attachments[]" required></div></td>
                    <td class="attachment-removal"><button type="button" class="btn btn-danger remove-new-attachment-btn">Delete</button></td>
                </tr>`;
                $('#attachment-list').append(newRow);
                updateAttachmentNumbers();
            }
            function updateAttachmentNumbers() {
                $('#attachment-list tr').each(function(index) {
                    $(this).find('.attachment-id').text(index + 1);
                });
            }
            $('#add-attachment-item').on('click', function() {
                addAttachmentRow();
            });
            $(document).on('click', '.remove-new-attachment-btn', function() {
                $(this).closest('tr').remove();
                updateAttachmentNumbers();
            });
            $(document).on('click', '.remove-existing-attachment-btn', function() {
                const attachmentId = $(this).data('attachment-id');
                $(this).closest('tr').remove();
                $('#evalForm').append(`<input type="hidden" name="deleted_attachments[]" value="${attachmentId}">`);
                updateAttachmentNumbers();
            });
            updateAttachmentNumbers();
            const totalExistingRows = $('#attachment-list tr').length;
            if (totalExistingRows === 0) {
                addAttachmentRow('KPI');
                addAttachmentRow('Attendance');
            }

            // Count Character
            function updateCharacterCount(textareaId, countId, maxLength) {
                const textarea = document.getElementById(textareaId);
                const countSpan = document.getElementById(countId);
                if (!textarea || !countSpan) return;
                const updateCount = function() {
                    const currentLength = textarea.value.length;
                    const remaining = maxLength - currentLength;
                    countSpan.textContent = remaining;
                    if (remaining < 0) {
                        countSpan.classList.add('text-warning');
                    } else {
                        countSpan.classList.remove('text-warning');
                    }
                };
                textarea.addEventListener('input', updateCount);
                updateCount(); 
            }

            const inputToMonitor = [
                { id: 'note_hrd', max: 100 }, 
            ];
            inputToMonitor.forEach(item => {
                updateCharacterCount(item.id, `count_${item.id}`, item.max); 
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }});
        });
    </script>
    <script>
        @if (Session::has('success'))
            toastr.options = {"closeButton": true,"progressBar": true,"positionClass": "toast-bottom-right"}
            toastr.success("{{ session('success') }}");
        @endif
        @if (Session::has('error'))
            toastr.options = {"closeButton": true,"progressBar": true,"positionClass": "toast-bottom-right"}
            toastr.error("{{ session('error') }}");
        @endif
    </script>
@endsection