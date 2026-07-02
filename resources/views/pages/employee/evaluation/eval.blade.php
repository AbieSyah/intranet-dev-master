@extends('layouts.general')

@section('link')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <style>
        :disabled {
            cursor: not-allowed;
        }

        .modal-body.scrollable {
            overflow-y: auto;
            max-height: 75vh;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        @include('partials.evaluation.profile.eval', [
            'backRoute'   => route('evaluation.emp.index'),
            'reviceRoute' => route('evaluate.emp.revice', ['token' => $token]),
            'storeRoute'  => route('evaluate.emp.store', ['token' => $token]),
        ])
    </div>
@endsection

@section('script')
    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
@endsection

@section('javascript')
    <script>
        $(document).ready(function() {
            function addAttachmentRow(defaultName = '') {
                const currentTotalRows = $('#attachment-list tr').length;
                const newCounter = currentTotalRows + 1;
                const newRow = `
                <tr class="new-attachment-row">
                    <th scope="row" class="attachment-id">${newCounter}</th>
                    <td>
                        <div class="mb-2">
                            <input 
                                type="text" 
                                class="form-control" 
                                name="new_attachment_names[]" 
                                placeholder="e.g., Attendance List" 
                                value="${defaultName}" 
                                required>
                        </div>
                    </td>
                    <td>
                        <div class="mb-2">
                            <input type="file" class="form-control" name="new_attachments[]" required>
                        </div>
                    </td>
                    <td class="attachment-removal">
                        <button type="button" class="btn btn-danger remove-new-attachment-btn">Delete</button>
                    </td>
                </tr>
            `;
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
                $('#evalForm').append(
                    `<input type="hidden" name="deleted_attachments[]" value="${attachmentId}">`);
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
                { id: 'ap_managerial_c', max: 60 },
                { id: 'ap_ability_response_c', max: 60 },
                { id: 'ap_leadership_c', max: 60 },
                { id: 'ap_accuracy_c', max: 60 },
                { id: 'ap_capability_c', max: 60 },
                { id: 'ap_initiative_c', max: 60 },
                { id: 'ap_kaizen_c', max: 60 },
                { id: 'ap_responsibility_c', max: 60 },
                { id: 'ap_discipline_c', max: 60 },
                { id: 'ap_cooperation_c', max: 60 },
                { id: 'kpi_c', max: 60 },
                { id: 'attendance_c', max: 60 },
                { id: 'positive', max: 189 },
                { id: 'weakness', max: 189 }, 
            ];
            inputToMonitor.forEach(item => {
                updateCharacterCount(item.id, `count_${item.id}`, item.max); 
            });
        });
    </script>
    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: '{{ session('success') }}',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '{{ session('error') }}',
                confirmButtonColor: '#d33',
                confirmButtonText: 'OK'
            });
        </script>
    @endif

    @if ($errors->any())
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                html: `{!! implode('<br>', $errors->all()) !!}`,
                confirmButtonColor: '#d33',
                confirmButtonText: 'OK'
            });
        </script>
    @endif
@endsection
