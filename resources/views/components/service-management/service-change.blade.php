@props([
   'ticket',
   'role',
])

@php
   $changeApprovers = App\Models\User::role('Super User')->with('employee', 'employee.department', 'employee.position')->get()->pluck('employee')->filter(fn($emp) => $emp != null);
   // dd($changeApprovers);
@endphp

@pushOnce('styles')
   <link rel="stylesheet" href="{{  url('') }}/assets/libs/adminlte/select2/css/4.1.0/select2.min.css">
   <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endPushOnce

<div class="card" id="change-management-card">
   <div class="card-header bg-white fw-bold">Change Management</div>
   <div class="card-body">
      <div>
         @if (($role == 'it' || $role == 'service_change') && $ticket->serviceChange)
            <div class="mb-3">
               <label class="text-muted small d-block">Proposed By:</label>
               <h6>{{ $ticket->serviceChange->proposer->fullname }} at {{ $ticket->serviceChange->proposed_at->format('d M Y H:i') }}</h6>
            </div>

            <div class="mb-3">
               <label class="text-muted small d-block">Execution Plan:</label>
               <h6>{{ $ticket->serviceChange->planned_start->format('d M Y H:i') }} to {{ $ticket->serviceChange->planned_end->format('d M Y H:i') }}</h6>
            </div>
            
            @if($ticket->serviceChange->status == 'approved')
               <div class="mb-3">
                  <label class="text-muted small d-block">Actual Execution:</label>
                  <h6>
                     @if ($ticket->serviceChange->status == 'done')
                        {{ $ticket->serviceChange->actual_start->format('d M Y H:i') }} to {{ $ticket->serviceChange->actual_end->format('d M Y H:i') }}
                     @else
                        <span class="text-primary">Please update the actual execution time after the work is completed.</span>
                     @endif
                  </h6>
               </div>
            @endif

            <div class="d-flex align-items-center justify-content-between mb-2">
               <h6 class="small fw-bold text-uppercase mb-0">{{ $ticket->serviceChange->status == "proposed"? "Waiting For Approval: " : "Approved By: " }}: {{ $ticket->serviceChange->approver->fullname }}</h6>
                
               @if($ticket->serviceChange->status == 'approved')
                  <span class="badge bg-primary">Approved</span>
               @elseif($ticket->serviceChange->status == 'done')
                  <span class="badge bg-success">Done</span>
               @else
                  <span class="badge bg-warning text-dark">Pending</span>
               @endif
            </div>

            <div class="progress" style="height: 8px;">
               @php
                  $progress = 0;
                  $color = 'bg-warning';
                  
                  if($ticket->serviceChange->status == 'done') {
                     $progress = 100;
                     $color = 'bg-success';
                  } elseif($ticket->serviceChange->status == 'approved') {
                     $progress = 50;
                     $color = 'bg-primary';
                  } else {
                     $progress = 10; // Sedang menunggu
                  }
               @endphp
               <div class="progress-bar {{ $color }} progress-bar-striped progress-bar-animated" role="progressbar" style="width: {{ $progress }}%"></div>
            </div>
            {{-- Tambahkan Tombol Edit/Update di bawah Progress Bar --}}
            @if ($ticket->serviceChange->status == 'proposed' && $role == 'service_change')
               <button class="btn btn-sm btn-outline-secondary mt-3 w-100" data-bs-toggle="modal" data-bs-target="#changeModal">
                  Edit Change Request
                  {{-- {{ $ticket->serviceChange->status == 'approved' ? 'Update Actual Execution' : 'Edit Change Request' }} --}}
               </button>
            @elseif($role == 'it')
               <a href="{{ route('service-change.show', encrypt($ticket->serviceChange->id)) }}" class="btn btn-sm btn-outline-secondary mt-3 w-100">
                  View Change Request
               </a>
               @endif
               
            @if ($ticket->serviceChange->approver_id == Auth::user()->employee_id && $ticket->serviceChange->status == 'proposed' && request()->is('administrator/change-management/*') )
               <a href="{{ URL::signedRoute('service-change.public.index', ['id' => encrypt($ticket->serviceChange->id), 'approverId' => encrypt(Auth::user()->employee_id)]) }}" class="btn btn-sm btn-primary mt-3 w-100">
                  Approve Service Change
               </a>
            @endif

            @if ($ticket->serviceChange->approver_id == Auth::id() && $ticket->serviceChange->status == 'pending' && $role == 'service_change')
               <a href="{{ URL::signedRoute('service-change.approve', encrypt($ticket->serviceChange->id)) }}" class="btn btn-sm btn-success w-100 mb-1">Approve Change</a>
            @endif
         @elseif ($ticket->current_status == 'process' || $ticket->current_status == 'hold')
            <button class="w-100 btn btn-primary" data-bs-toggle="modal" data-bs-target="#changeModal">
               Create Change Management
            </button>
         @endif
      </div>
   </div>

   <div class="modal" id="changeModal" tabindex="-1">
      <div class="modal-dialog">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title">Select Change Approver</h5>
               <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
               <form action="#" method="POST" id="changeForm">
                  @csrf
                  @if ($ticket->serviceChange)
                     @method('PUT')
                  @endif
                  <input type="hidden" name="ticket_id" value="{{ encrypt($ticket->id) }}">

                  <div class="mb-3">
                     <label class="form-label">Subject</label>
                     <input type="text" value="{{ $ticket->subject }}" class="form-control" disabled>
                  </div>

                  <div class="mb-3">
                     <label class="form-label">Description</label>
                     <div name="description" cols="30" rows="10" class="form-control" style="background: var(--vz-input-disabled-bg)">
                        {!! $ticket->description !!}
                     </div>
                  </div>
                  
                  <div class="mb-3">
                     <label for="it_notice" class="form-label">IT Notice</label>
                     <textarea name="it_notice" class="form-control" 
                        {{ $ticket->serviceChange && $ticket->serviceChange->status == 'approved' ? 'disabled' : '' }}>{{ $ticket->serviceChange->it_notice ?? '' }}</textarea>
                  </div>
                  
                  <div class="mb-3">
                     <label for="execution_plan" class="form-label">Execution Plan</label>
                     <input type="text" name="execution_plan" class="form-control date-range" 
                        value="{{ $ticket->serviceChange ? $ticket->serviceChange->planned_start->format('Y-m-d H:i') . ' to ' . $ticket->serviceChange->planned_end->format('Y-m-d H:i') : '' }}"
                        {{ $ticket->serviceChange && $ticket->serviceChange->status == 'approved' ? 'disabled' : 'required' }}
                        style="background: {{ $ticket->serviceChange && $ticket->serviceChange->status == 'approved' ? 'var(--vz-input-disabled-bg)' : 'white' }}">
                  </div>

                  @if($ticket->serviceChange && $ticket->serviceChange->status == 'approved')
                     <div class="mb-3">
                        <label for="actual_execution" class="form-label text-primary fw-bold">Actual Execution (Realization)</label>
                        <input type="text" name="actual_execution" id="actual_execution" 
                           class="form-control date-range-actual" 
                           value="{{ $ticket->serviceChange->actual_start ? $ticket->serviceChange->actual_start->format('Y-m-d H:i') . ' to ' . $ticket->serviceChange->actual_end->format('Y-m-d H:i') : '' }}"
                           required>
                        <small class="text-muted">Fill this after the work is completed.</small>
                     </div>
                  @endif

                  {{-- <div class="mb-3">
                     <label class="form-label">CM Type</label>
                     <select name="change_type" class="form-select" required>
                        <option value="">Select CM Type</option>
                        <option value="{{ App\Models\ServiceChange::TYPE_STANDARD }}">{{ strtoupper(App\Models\ServiceChange::TYPE_STANDARD) }}</option>
                        <option value="{{ App\Models\ServiceChange::TYPE_NORMAL }}">{{ strtoupper(App\Models\ServiceChange::TYPE_NORMAL) }}</option>
                        <option value="{{ App\Models\ServiceChange::TYPE_EMERGENCY }}">{{ strtoupper(App\Models\ServiceChange::TYPE_EMERGENCY) }}</option>
                     </select>
                  </div> --}}

                  <div class="mb-3">
                     <label class="form-label">Approver</label>
                     <select name="approver" class="form-select select2-service-change" required data-dropdown-parent="#changeModal" {{ $ticket->serviceChange && $ticket->serviceChange->status == 'approved' ? 'disabled' : '' }}>
                        <option value="">Select Approver</option>
                        @foreach($changeApprovers as $employee)
                           <option value="{{ encrypt($employee->id) }}" {{ $ticket->serviceChange && $ticket->serviceChange->approver_id == $employee->id ? 'selected' : '' }}>
                              {{ strtoupper($employee->fullname) }} - {{ strtoupper($employee->position->nama) }} ({{ strtoupper($employee->department->name) }})
                           </option>
                        @endforeach
                     </select>
                  </div>
               </form>
            </div>
            <div class="modal-footer">
               <button class="w-100 btn btn-primary" type="submit" form="changeForm">
                  @if ($ticket->serviceChange)
                     {{ $ticket->serviceChange->status == 'approved' ? 'Save Actual Execution(Done)' : 'Save Changes' }}
                  @else
                     Create Change Management
                  @endif
               </button>
            </div>
         </div>
      </div>
   </div>
</div>

@pushOnce('scripts')
   <!-- Select2 -->
   {{-- <script src="/assets/libs/adminlte/select2/js/4.1.0/select2.min.js"></script> --}}
   <!-- Sweetalert -->
   <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

   {{-- the import select2 and flatpickr script and css are at the parent of this page(service-management/show.blade.php)  --}}
   <script>
      // $('.select2-service-change').select2();

      // --------------------------------- SERVICE CHANGE MANAGEMENT HANDLER ---------------------------------
      flatpickr(".date-range", {
         mode: "range",
         enableTime: true,
         time_24hr: true,
         dateFormat: "Y-m-d H:i",
         minDate: "today", // Opsional: mencegah pilih tanggal masa lalu
         // onChange: function(selectedDates, dateStr, instance) {
         //    // Jika butuh memisahkan value untuk dikirim ke backend
         //    if (selectedDates.length === 2) {
         //          console.log("Start:", selectedDates[0]);
         //          console.log("End:", selectedDates[1]);
         //    }
         // }
      });

      // Di dalam script tag
      flatpickr(".date-range-actual", {
         mode: "range",
         enableTime: true,
         time_24hr: true,
         dateFormat: "Y-m-d H:i",
      });

      $(document).ready(function() {
         $('#changeForm').submit(function(e) {
            e.preventDefault()

            const formData = new FormData(this);

            $('#changeModal button[type="submit"]').prop('disabled', true).text('Processing...');

            $('#change-management-card .text-danger').not('.static-label').remove();
            $('.is-invalid').removeClass('is-invalid');

            $.ajax({
               url: "{{ $ticket->serviceChange? route('service-change.update', encrypt($ticket->serviceChange->id)) : route('service-change.store') }}",
               method: "POST",
               data: formData,
               processData: false,
               contentType: false,
               success: function(response) {
                  $('#changeModal button[type="submit"]').prop('disabled', false).text('Create Change Management');
                  
                  Swal.fire({
                     title: 'Success!',
                     text: response.message,
                     icon: 'success',

                     // its nonactive because CM details are not implemented yet, so we just refresh the page to update CM status
                     // showCancelButton: true,
                     // confirmButtonText: 'Go to Change Management',
                     // cancelButtonText: 'Stay'


                     confirmButtonText: 'OK',
                  }).then((result) => {
                     $('#changeModal').modal('hide');
                     if (result.isConfirmed) {
                        window.location.href = window.location.href; // Refresh halaman untuk update status CM
                        // window.location.href = '#'; // Ganti dengan URL detail CM jika sudah ada
                     } else {
                        // window.location.href = window.location.href; // Refresh halaman untuk update status CM meskipun user klik cancel atau close modal, karena detail CM belum ada jadi kita buat agar tetap refresh untuk update status CM di UI
                     }
                  });
               },
               error: function(xhr) {
                  // console.log(xhr);                  
                  // return

                  const errorMessage = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'An error occurred while processing your request.';
                  const statusInfo = ' (Status: ' + xhr.status + ' ' + xhr.statusText + ')';

                  $('#changeModal button[type="submit"]').prop('disabled', false).text('Create Change Management');

                  if (xhr.status === 422) {
                     const errors = xhr.responseJSON.errors;
                     
                     // Notifikasi umum dengan Swal
                     Swal.fire({
                           icon: 'error',
                           title: 'Validation Error',
                           text: 'Please check the required fields.',
                     });

                     // Tampilkan error spesifik di bawah tiap input
                     $.each(errors, function(key, value) {
                           // Cari element berdasarkan name atribut
                           let input = $('[name="' + key + '"]');
                           
                           // Handle khusus untuk select2
                           if (input.hasClass('select2-hidden-accessible')) {
                              input.next('.select2-container').addClass('is-invalid');
                              input.closest('div').append('<small class="text-danger">' + value[0] + '</small>');
                           } else {
                              input.addClass('is-invalid');
                              input.after('<small class="text-danger">' + value[0] + '</small>');
                           }
                     });
                     
                     // Jika error ada di Step 1 (Catalog) tapi user di Step 2
                     if(errors.catalog && $('#step1').is(':hidden')) {
                           $('#step2').hide();
                           $('#step1').show();
                     }
                  } else {
                     Swal.fire('Error', 'Something went wrong on the server.', 'error');
                  }
               }
            })
         });
      })
      // --------------------------------- END SERVICE CHANGE MANAGEMENT HANDLER ---------------------------------
   </script>
@endPushOnce