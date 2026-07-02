<script>
    $(document).ready(function() {
        const evaluationStepsUrl = '{{ $stepsRoute }}';
        $('#tahun').select2();
        let selectedIds = new Set();
        let currentTabId = '#pill-process';

        $(document).on('change', '.row-checkbox', function() {
            updateSelectedIds();
            updateMultiButtons();
        });

        $('#checkAllProcess').on('click', function() {
            if (currentTabId === '#pill-process' && !$(this).is(':disabled')) {
                $('#table_process .row-checkbox:not(:disabled)').prop('checked', this.checked);
                updateSelectedIds();
                updateMultiButtons();
            }
        });

        $('#checkAllDone').on('click', function() {
            if (currentTabId === '#pill-done' && !$(this).is(':disabled')) {
                $('#table_done .row-checkbox:not(:disabled)').prop('checked', this.checked);
                updateSelectedIds();
                updateMultiButtons();
            }
        });

        function updateSelectedIds() {
            const tableSelector = currentTabId === '#pill-process' ? '#table_process' : '#table_done';
            const checkAllId = currentTabId === '#pill-process' ? '#checkAllProcess' : '#checkAllDone';
            $(`${tableSelector} .row-checkbox`).each(function() {
                const val = $(this).val();
                if ($(this).is(':checked')) {
                    selectedIds.add(val);
                } else {
                    selectedIds.delete(val);
                }
            });
            const checkedInView = $(`${tableSelector} .row-checkbox:not(:disabled):checked`).length;
            const totalInView   = $(`${tableSelector} .row-checkbox:not(:disabled)`).length;
            $(checkAllId).prop('checked', totalInView > 0 && checkedInView === totalInView);
        }

        function updateMultiButtons() {
            const count = selectedIds.size;
            $('#multi-approve-btn-process').hide();
            $('#multi-print-btn-process').hide();
            $('#multi-print-btn-done').hide();
            if (count > 0) {
                if (currentTabId === '#pill-process') {
                    $('#multi-approve-btn-process').show();
                    $('#approve-count-process').text(count);
                    $('#multi-print-btn-process').show();
                    $('#print-count-process').text(count);
                } else if (currentTabId === '#pill-done') {
                    $('#multi-print-btn-done').show();
                    $('#print-count-done').text(count);
                }
            }
        }

        $('#multi-approve-btn-process').click(function() {
            let firstSelectedRowData = tableProcess.rows().data().toArray()
                .find(row => selectedIds.has(String(row.id)));
            let role = firstSelectedRowData ? firstSelectedRowData.role : '';
            $('#approveForm').attr('action', '{{ $approveMultipleRoute }}');
            $('#approveMessage').text(`Are you sure you want to approve ${selectedIds.size} selected evaluations?`);
            const idContainer = $('#approve-id-container');
            idContainer.empty();
            selectedIds.forEach(id => {
                idContainer.append(`<input type="hidden" name="ids[]" value="${id}">`);
            });
            $('#current-role').val(role);
            $('#approveConfirmationModal').modal('show');
        });

        function handleMultiPrint(e) {
            e.preventDefault();
            if (selectedIds.size === 0) {
                Swal.fire('Error!', 'Please select at least one evaluation to print.', 'error');
                return;
            }
            Swal.fire({ title: 'Loading data...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
            $.ajax({
                url: '{{ $printTokenRoute }}',
                method: 'POST',
                data: {
                    ids: [...selectedIds],
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    Swal.close();
                    if (response.token) {
                        let url = '{{ $printRoute }}'.replace(':token', response.token);
                        window.open(url, '_blank');
                    } else {
                        Swal.fire('Error!', 'Failed to generate token.', 'error');
                    }
                },
                error: function(xhr) {
                    Swal.close();
                    Swal.fire('Error!', 'Failed to generate token. ' + xhr.responseJSON.message, 'error');
                }
            });
        }

        $('#multi-print-btn-process').click(handleMultiPrint);
        $('#multi-print-btn-done').click(handleMultiPrint);

        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

        let tableProcess = null;
        let tableDone = null;

        @if (Session::has('tab_done') || request()->has('tab_done'))
            currentTabId = '#pill-done';
            tableDone = initializeDataTable('#table_done', '{{ $doneRoute }}');
            $('a[href="#pill-done"]').tab('show');
        @elseif (Session::has('tab_process'))
            currentTabId = '#pill-process';
            tableProcess = initializeDataTable('#table_process', '{{ $processRoute }}');
            $('a[href="#pill-process"]').tab('show');
        @else
            currentTabId = '#pill-process';
            tableProcess = initializeDataTable('#table_process', '{{ $processRoute }}');
        @endif

        function updateProcessBadge() {
            $.ajax({
                url: '{{ $countProcessRoute }}',
                method: 'GET',
                success: function(response) {
                    let badge = $('a[href="#pill-process"] .badge');
                    if (badge.length === 0) {
                        const link = $('a[href="#pill-process"]');
                        if (link.length > 0) {
                            badge = $('<span class="badge bg-danger"></span>').appendTo(link);
                        } else { return; }
                    }
                    if (response.jml_process > 0) {
                        badge.text(response.jml_process).show();
                    } else {
                        badge.hide();
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Failed to update badge count:", error);
                }
            });
        }

        updateProcessBadge();

        $('#reset-process').on('click', function() {
            if (tableProcess) {
                selectedIds = new Set();
                $('#checkAllProcess').prop('checked', false);
                updateMultiButtons();
                tableProcess.ajax.reload(function() { updateProcessBadge(); }, false);
            }
        });

        $(document).on('click', '#filter-done', function() {
            if (tableDone) tableDone.ajax.reload(null, false);
        });

        $('#reset-done').on('click', function() {
            if (tableDone) {
                var currentYear = '{{ date('Y') }}';
                if ($('#tahun option[value="' + currentYear + '"]').length > 0) {
                    $('#tahun').val(currentYear).trigger('change');
                }
                tableDone.ajax.reload(null, false);
            }
        });

        $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
            let targetTab = $(e.target).attr('href');
            currentTabId = targetTab;
            selectedIds = new Set();
            if (targetTab === '#pill-process') {
                $('#checkAllProcess').prop('checked', false);
            } else if (targetTab === '#pill-done') {
                $('#checkAllDone').prop('checked', false);
            }
            updateMultiButtons();
            if (targetTab === '#pill-process') {
                tableProcess = initializeDataTable('#table_process', '{{ $processRoute }}');
                updateProcessBadge();
            } else if (targetTab === '#pill-done') {
                tableDone = initializeDataTable('#table_done', '{{ $doneRoute }}');
            }
        });

        function initializeDataTable(tableId, ajaxUrl) {
            if ($.fn.DataTable.isDataTable(tableId)) {
                $(tableId).DataTable().destroy();
            }
            let columnsToUse;
            let orderIndex = 14;
            let ajaxData = {};

            const checkboxColumnProcess = {
                data: 'id', name: 'id', orderable: false, searchable: false, className: "text-center",
                render: function(data, type, row) {
                    const isEnabled = row.total_score > 0 && row.has_action && row.ap_s > 0 && !row.role.includes('drafter');
                    return `<input type="checkbox" class="row-checkbox" value="${data}" ${isEnabled ? '' : 'disabled'}>`;
                }
            };

            const checkboxColumnDone = {
                data: 'id', name: 'id', orderable: false, searchable: false, className: "text-center",
                render: function(data, type, row) {
                    const enabled = row.total_score !== null && row.total_score > 0;
                    return `<input type="checkbox" class="row-checkbox" value="${data}" ${enabled ? '' : 'disabled'}>`;
                }
            };

            // Kolom yang sama untuk kedua tabel
            const sharedColumns = [
                { data: 'action',           name: 'action',           className: "text-center", orderable: false, searchable: false },
                { data: 'nik',              name: 'nik',              className: "text-center" },
                { data: 'name',             name: 'name' },
                { data: 'department',       name: 'department' },
                { data: 'period',           name: 'period' },
                { data: 'purpose',          name: 'purpose' },
                { data: 'status',           name: 'status',           className: "text-center", orderable: false, searchable: false },
                { data: 'kpi_score',        name: 'kpi_score',        className: "text-center" },
                { data: 'ap_s',             name: 'ap_s',             className: "text-center" },
                { data: 'attendance_score', name: 'attendance_score', className: "text-center" },
                { data: 'total_score',      name: 'total_score',      className: "text-center" },
                { data: 'grade',            name: 'grade',            className: "text-center" },
                { data: 'decision',         name: 'decision',         className: "text-center" },
                { data: 'created_at',       name: 'created_at',       className: 'hidden-column', visible: false },
                { data: 'has_action',       name: 'has_action',       className: 'hidden-column', visible: false },
                { data: 'role',             name: 'role',             className: 'hidden-column', visible: false },
            ];

            if (tableId === '#table_process') {
                columnsToUse = [checkboxColumnProcess, ...sharedColumns];
            } else {
                columnsToUse = [checkboxColumnDone, ...sharedColumns];
                ajaxData = function(d) { d.year = $('#tahun').val(); };
            }

            return $(tableId).DataTable({
                destroy: true,
                stateSave: false,
                responsive: false,
                autoWidth: false,
                processing: true,
                serverSide: false,
                scrollX: true,
                order: [[orderIndex, 'desc']],
                ajax: {
                    url: ajaxUrl,
                    data: ajaxData,
                    error: function(xhr, error, thrown) {
                        console.error("DataTables AJAX Error:", xhr.responseText);
                    }
                },
                columns: columnsToUse,
                drawCallback: function(settings) {
                    const tableSelector = tableId === '#table_process' ? '#table_process' : '#table_done';
                    const checkAllId    = tableId === '#table_process' ? '#checkAllProcess' : '#checkAllDone';

                    $(`${tableSelector} .row-checkbox`).each(function() {
                        if (selectedIds.has($(this).val())) {
                            $(this).prop('checked', true);
                        }
                    });

                    const checkedInView = $(`${tableSelector} .row-checkbox:not(:disabled):checked`).length;
                    const totalInView   = $(`${tableSelector} .row-checkbox:not(:disabled)`).length;

                    if (totalInView === 0) {
                        $(checkAllId).prop('checked', false).prop('disabled', true);
                    } else {
                        $(checkAllId).prop('disabled', false);
                        $(checkAllId).prop('checked', checkedInView === totalInView);
                    }

                    updateMultiButtons();

                    if (tableId === '#table_process') {
                        var api = this.api();
                        var rowData = api.rows().data();
                        if (rowData.length > 0) {
                            var userRole = rowData[0].role;
                            if (userRole === 'drafter') {
                                $('a[href="#pill-done"]').closest('li').hide();
                                if ($('#pill-done').hasClass('active')) {
                                    $('a[href="#pill-process"]').tab('show');
                                }
                            } else {
                                $('a[href="#pill-done"]').closest('li').show();
                            }
                        }
                    }
                }
            });
        }

        $.fn.dataTable.ext.errMode = 'none';
        $(document).on('error.dt', function(e, settings, techNote, message) {
            console.error('DataTables Error:', message);
        });

        $(document).on('click', '.btn-view-steps', function(e) {
            e.preventDefault();
            var encryptedId = $(this).data('id');
            const url = evaluationStepsUrl.replace(':id', encryptedId);
            $('#trackingModalLabel').text('Evaluation Steps');
            $('#trackingModal .modal-body .timeline').html(`
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            `);
            $('#trackingModal').modal('show');
            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    var timelineHtml = '';
                    $.each(response.steps, function(index, step) {
                        var completedClass = step.completed ? 'completed' : '';
                        var dateDisplay = step.date
                            ? `<small class="text-muted">${step.date}</small>`
                            : '<small class="text-muted">-</small>';
                        timelineHtml += `
                            <div class="timeline-item ${completedClass}">
                                <span class="timeline-line"></span>
                                <div class="timeline-marker"></div>
                                <div class="d-flex align-items-center">
                                    <div>
                                        <h6 class="mb-0">${step.name}${step.approval}</h6>
                                        ${dateDisplay}
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    $('#trackingModal .modal-body .timeline').html(timelineHtml);
                },
                error: function(xhr) {
                    console.error('Error fetching data:', xhr.responseText);
                    $('#trackingModal .modal-body .timeline').html(
                        '<div class="alert alert-danger">Failed to load data. Please try again.</div>'
                    );
                }
            });
        });
    });
</script>