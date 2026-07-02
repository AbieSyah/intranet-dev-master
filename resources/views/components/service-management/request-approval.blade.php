@props([
   'ticket',
   'role',
   'employees',
])

{{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" /> --}}


{{-- ----------------------- TRIAGE SECTION ----------------------- --}}
<div class="card shadow-sm mb-4">
   <div class="card-header bg-white fw-bold">Request Approval</div>
   <div class="card-body">
      <form id="triageForm" action="{{ route('service-ticket.request-approval', encrypt($ticket->id)) }}" method="POST" class="d-flex flex-column justify-content-between h-100">
         @csrf
         <div class="mb-2"> 
            <div class="mb-3">
               <label class="fw-bold small text-uppercase text-muted">Direct Supervisor<span class="text-danger">*</span></label>
               <select name="supervisor" data-dropdown-parent="#triageForm" id="supervisor-1" class="form-control select2" data-placeholder="Search Supervisor..." required>
                     <option value=""></option>
                     @foreach($employees as $emp)
                        <option value="{{ encrypt($emp->id) }}" {{ ($ticket->pic_id == $emp->id) ? 'selected' : '' }}>
                           {{ $emp->fullname }} - {{ ($emp->position->nama ?? 'N/A') }} ({{ $emp->department->name ?? 'N/A' }})
                        </option>
                     @endforeach
               </select>
            </div>
            <div class="mb-3">
               <label class="fw-bold small text-uppercase text-muted">Dept Head<span class="text-danger">*</span></label>
               <select name="dept_head" data-dropdown-parent="#triageForm" id="dept-head-1" class="form-control select2" data-placeholder="Search Department Head..." required>
                     <option value=""></option>
                     @foreach($employees as $emp)
                        <option value="{{ encrypt($emp->id) }}" {{ ($ticket->pic_id == $emp->id) ? 'selected' : '' }}>
                           {{ $emp->fullname }} - {{ ($emp->position->nama ?? 'N/A') }} ({{ $emp->department->name ?? 'N/A' }})
                        </option>
                     @endforeach
               </select>
            </div>
         </div>

         <button id="submit-btn" type="submit" form="triageForm" class="btn btn-secondary flex-fill shadow">
            <i class="ri-check-double-line"></i> <span class="text">Make Request Approval</span>
         </button>
      </form>
   </div>
</div>
{{-- ----------------------- END TRIAGE SECTION ----------------------- --}}

@pushOnce('scripts')
   <script>
      $(document).ready(function() {
         // ----------------- form handling -----------------
         $('#triageForm').on('submit', function(e) {
            console.log(true);
            
            e.preventDefault();

            let form = $(this);
            let url = form.attr('action');
            let formData = new FormData(this);

            function clearErrors() {
               $('.is-invalid').removeClass('is-invalid');
               $('.select2-container').removeClass('is-invalid');
               $('.text-danger').remove();
            }

            Swal.fire({
               title: 'Make Request Approval',
               text: "Are you sure you want to make request approval for this ticket? Please make sure the selected approvers are correct.",
               icon: 'question',
               showCancelButton: true,
               confirmButtonColor: '#3085d6',
               cancelButtonColor: '#d33',
               confirmButtonText: 'Yes, Request Approval!',
               showLoaderOnConfirm: true,
               input: 'textarea',
               inputPlaceholder: 'Add a note for the approver',
               reverseButtons: true,
               inputValidator: (value) => {
                  if (value.length == 0 || !value) {
                     return 'Note is required';
                  }
                  if (value.length > 255) {
                     return 'Note cannot exceed 255 characters';
                  }
               },
               preConfirm: (it_note) => {
                  clearErrors();

                  formData.append('it_note', it_note);

                  return $.ajax({
                     url: url,
                     type: 'POST',
                     data: formData,
                     dataType: 'json',
                     contentType: false,
                     processData: false,
                     headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                     }
                  }).then(
                     (response) => {
                        return response;
                     },
                     (jqXHR) => {
                        if (jqXHR.status === 422) {
                           const errors = jqXHR.responseJSON.errors;
                           $.each(errors, function(key, value) {
                              // Handle array inputs (users.0.user -> users[0][user])
                              let nameAttr = key.split('.').map((s, i) => i > 0 ? `[${s}]` : s).join('');
                              let input = $(`[name="${key}"], [name="${nameAttr}"]`);
                              
                              if (input.hasClass('select2-hidden-accessible')) {
                                 input.next('.select2-container').addClass('is-invalid');
                                 input.closest('div').append('<small class="text-danger d-block">' + value[0] + '</small>');
                              } else {
                                 input.addClass('is-invalid');
                                 input.after('<small class="text-danger d-block">' + value[0] + '</small>');
                              }
                           });
                           Swal.showValidationMessage('Please fix the validation errors and try again. Error: ' + jqXHR.message);
                        } else {
                           Swal.showValidationMessage(`Error ${jqXHR.status}: ${jqXHR.responseJSON.message || 'Server Error'}`);
                        }
                        return false; // Mencegah modal tertutup karena error
                     }
                  );
               },
               allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
               if (result.isConfirmed && result.value) {
                  Swal.fire({
                     title: 'Berhasil!',
                     text: 'Tiket telah diverifikasi.',
                     icon: 'success',
                     timer: 1500,
                     showConfirmButton: false
                  }).then(() => {
                     window.location.href = "{{ route('service-management.workspace', ['id' => encrypt($ticket->id), 'role' => encrypt('it')]) }}";
                  });
               }
            });
         });
         // --------------------------------- END STEPPER LOGIC ---------------------------------
      });
   </script>
@endPushOnce