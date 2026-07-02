@props([
   'ticket', 
   'role'// 'user' or 'it'
])

<div>
   <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#cancelTicketModal">
      Cancel Ticket
   </button>
   <div class="modal fade" id="cancelTicketModal" tabindex="-1" aria-labelledby="cancelTicketModalLabel" aria-hidden="true">
      <div class="modal-dialog">
         <div class="modal-content">
            <form method="POST" action="{{ route('service-ticket.cancel', ['id' => encrypt($ticket->id), 'role' => encrypt($role)]) }}">
               @csrf
               <div class="modal-header">
                  <h5 class="modal-title" id="cancelTicketModalLabel">Cancel Service Ticket</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
               </div>
               <div class="modal-body">
                  <div class="mb-3">
                     <label for="reason" class="form-label">Reason for Cancellation</label>
                     <textarea class="form-control" id="reason" name="reason" rows="4" required maxlength="500"></textarea>
                  </div>
               </div>
               <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                  <button type="submit" class="btn btn-danger">Confirm Cancellation</button>
               </div>
            </form>
         </div>
      </div>
   </div>
</div>


@pushOnce('scripts')
   <script>
      <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
   </script>

   <script>
      $('#cancelTicketModal').on('shown.bs.modal', function () {
         $('#cancellation_reason').trigger('focus');
      });

      $('#cancelTicketModal form').on('submit', function(e) {
         e.preventDefault();
         Swal.fire({
            title: 'Are you sure?',
            text: "You are about to cancel this ticket. This action cannot be undone.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, cancel it!',
            cancelButtonText: 'No, keep it',
            showLoaderOnConfirm: true,
            reverseButtons: true,
            preConfirm: () => {
               return $.ajax({
                  url: $(this).attr('action'),
                  method: 'POST',
                  data: $(this).serialize(),
               }).then(response => {
                  if(response.status === 'success') {
                     window.location.href = window.location.href;
                     toastr.success(response.message);
                  } else {
                     throw new Error(response.message || 'An error occurred while cancelling the ticket.');
                  }
               }).catch(error => {
                  Swal.showValidationMessage(
                     `Request failed: ${error}`
                  );
               });
            },
         })
      });
   </script>
@endPushOnce