@extends('layouts.simple')

@section('content')
<style>
   body { background-color: #f8fafc; }
   .glass-card {
      background: #ffffff;
      border: 1px solid rgba(0,0,0,0.05);
      border-radius: 1.25rem;
   }
   .status-badge {
      padding: 0.5rem 1rem;
      border-radius: 50rem;
      font-size: 0.875rem;
      font-weight: 600;
   }
   .info-label {
      font-size: 0.75rem;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      color: #64748b;
      font-weight: 700;
      margin-bottom: 0.25rem;
      display: block;
   }
   .preview-section {
      background-color: #f1f5f9;
      border-radius: 1rem;
      padding: 1.5rem;
   }
   .btn-modern {
      padding: 0.8rem 1.5rem;
      border-radius: 0.75rem;
      font-weight: 600;
      transition: all 0.2s;
   }
   .btn-modern:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
</style>

<div class="container py-5">
   <div class="row justify-content-center">
      <div class="col-lg-9 col-xl-8">
         
         @if(session('success'))
            <div class="glass-card shadow-sm p-5 text-center">
               <div class="mb-4">
                  <span class="display-1 text-success"><i class="bi bi-check2-circle"></i></span>
               </div>
               <h2 class="fw-bold">Process Completed</h2>
               <p class="text-secondary mb-4">The change management request has been successfully <strong>{{ session('success') }}</strong>.</p>
               <small class="d-block">Approved by: {{ $serviceChange->approver->fullname }} - {{ $serviceChange->approver->position->nama }}({{ $serviceChange->approver->department->name }})</small>
               <small class="d-block mb-3">Approval Time: {{ $serviceChange->approved_at->format('d M Y, H:i') }}</small>
               <button class="btn btn-primary w-100" onclick="window.location.href='{{ route('service-change.index') }}'">
                  Back to Change Requests
               </button>
            </div>


         @elseif($serviceChange->status !== 'proposed')
            <div class="glass-card shadow-sm p-5 text-center">
               <div class="mb-4">
                  <span class="display-4 text-info"><i class="bi bi-info-circle"></i></span>
               </div>
               <h4 class="fw-bold">Request Already Approved</h4>
               <h6>By: {{ $serviceChange->approver->fullname }} - {{ $serviceChange->approver->position->nama }}({{ $serviceChange->approver->department->name }})</h6>
               <p class="text-secondary mb-4">
                  This request was marked as <span class="badge bg-soft-info text-info status-badge">{{ strtoupper($serviceChange->status) }}</span> 
                  <br>on {{ $serviceChange->approved_at->format('d M Y, H:i') }}
               </p>
               <button class="btn btn-primary w-100" onclick="window.location.href='{{ route('service-change.index') }}'">
                  Back to Change Requests
               </button>
            </div>

         @else
            <div class="glass-card shadow-sm p-4 p-md-5">
               <div class="d-flex justify-content-between align-items-start mb-4">
                  <div>
                     <span class="info-label">Document Number</span>
                     <h3 class="fw-bold text-dark">{{ $serviceChange->change_no }}</h3>
                  </div>
                  <span class="status-badge bg-warning bg-opacity-10 text-warning">
                     Pending Review
                  </span>
               </div>

               <div class="row g-4 mb-5">
                  <div class="col-md-6">
                     <span class="info-label">Proposer</span>
                     <div class="d-flex align-items-center">
                        <div class="avatar-sm bg-primary text-white rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.8rem;">
                           {{ substr($serviceChange->proposer->fullname, 0, 2) }}
                        </div>
                        <div>
                           <p class="mb-0 fw-bold">{{ $serviceChange->proposer->fullname }}</p>
                           <small class="text-muted">{{ $serviceChange->proposer->position->nama }}</small>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <span class="info-label">Department</span>
                     <p class="mb-0 fw-bold text-dark">{{ $serviceChange->proposer->department->name }}</p>
                  </div>
                  <div class="col-md-12">
                     <span class="info-label">Execution Plan</span>
                     <p class="mb-0 fw-bold text-dark">{{ $serviceChange->planned_start->format('d M Y, H:i') }} to {{ $serviceChange->planned_end->format('d M Y, H:i') }}</p>
                  </div>
                  <div class="col-md-12">
                     <span class="info-label">Change Priority</span>
                     <p class="mb-0 fw-bold text-dark">{{ strtoupper($serviceChange->change_type) }}</p>
                  </div>
               </div>

               <div class="preview-section mb-4">
                  <div class="d-flex align-items-center mb-3">
                     <span class="fw-bold text-primary" style="font-size: 0.9rem;">Service Ticket Reference</span>
                  </div>
                  <h5 class="fw-bold mb-2">{{ $serviceChange->ticket->no_ticket }}</h5>
                  <div class="text-secondary" style="font-size: 0.95rem; line-height: 1.6;">
                     {!! str_replace('&nbsp;', ' ', strip_tags($serviceChange->ticket->description)) !!}
                  </div>
               </div>

               <hr class="my-4 opacity-25">

               <div class="preview-section mb-4">
                  <div class="d-flex align-items-center mb-3">
                     <span class="fw-bold text-primary" style="font-size: 0.9rem;">Change Management</span>
                  </div>
                  <div class="text-secondary mb-4" style="font-size: 0.95rem; line-height: 1.6;">
                     {!! str_replace('&nbsp;', ' ', strip_tags($serviceChange->it_notice)) !!}
                  </div>
               </div>

               <form action="{{ URL::signedRoute('service-change.public.approve', ['id' => encrypt($serviceChange->id)]) }}" method="POST" id="approvalForm">
                  @csrf
                  
                  {{-- <div class="mb-4">
                     <label class="info-label">Add Note (Optional)</label>
                     <textarea name="note" class="form-control border-0 bg-light" rows="3" placeholder="Write your feedback here..." style="border-radius: 0.75rem;"></textarea>
                  </div> --}}

                  <div class="mb-4 p-3 border rounded-3 bg-light bg-opacity-50">
                     <div class="form-check d-flex align-items-center">
                        <input class="form-check-input me-3" type="checkbox" id="confirmCheck" style="width: 1.2rem; height: 1.2rem; cursor: pointer;">
                        <label class="form-check-label text-secondary small fw-medium" for="confirmCheck" style="cursor: pointer;">
                           I hereby declare that I have reviewed the change request details and the associated service ticket, and I fully authorize this action.
                        </label>
                     </div>
                  </div>

                  <div class="row g-3">
                     <button type="submit" name="action" value="approved" id="approveBtn" class="btn btn-primary btn-modern w-100" disabled>
                        <i class="bi bi-check-lg me-2"></i>Approve
                     </button>
                  </div>
               </form>
            </div>
         @endif

         <p class="text-center mt-4 text-muted" style="font-size: 0.8rem;">
            &copy; {{ date('Y') }} Service Desk System &bull; Secure Approval Portal
         </p>
      </div>
   </div>
</div>
@endsection

@section('javascript')
   <script>
      document.addEventListener('DOMContentLoaded', function() {
         const checkbox = document.getElementById('confirmCheck');
         const approveBtn = document.getElementById('approveBtn');

         checkbox.addEventListener('change', function() {
               approveBtn.disabled = !this.checked;
               
               // Opsional: Tambahkan efek visual saat aktif
               if(this.checked) {
                  approveBtn.classList.remove('opacity-50');
               } else {
                  approveBtn.classList.add('opacity-50');
               }
         });
      });
   </script>
@endsection