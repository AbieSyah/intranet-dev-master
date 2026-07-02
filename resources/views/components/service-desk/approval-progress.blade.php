@props([
   'class' => null,
   'name',
   'for_dept_head' => false,
   'ticket',
])

{{-- @dd($for_dept_head, $ticket); --}}

@php
   !$for_dept_head? 
      $approval_status = $ticket->supervisor_approval : 
      $approval_status = $ticket->dept_head_approval;

   $rejection_note = !$for_dept_head? $ticket->supervisor_note : $ticket->dept_head_note;
   $approver_name = !$for_dept_head? $ticket->supervisor->fullname : $ticket->deptHead->fullname ?? 'N/A';
@endphp

<div class="card border-0 shadow-sm {{ $class }}">
   <div class="card-body p-3">
      <div class="d-flex align-items-center justify-content-between mb-2">
         <h6 class="small fw-bold text-uppercase mb-0">Approval {{ $name }}({{ $approver_name }})</h6>
         
         {{-- Badge Status Utama --}}
         @if($approval_status == 'rejected')
            <span class="badge bg-danger">Rejected</span>
         @elseif($approval_status == 'approved')
            <span class="badge bg-success">Approved</span>
         @else
            <span class="badge bg-warning text-dark">Pending</span>
         @endif
      </div>

      <div class="progress" style="height: 8px;">
         @php
            $progress = 0;
            $color = 'bg-warning';
            
            if($approval_status == 'approved' || $approval_status == 'done') {
               $progress = 100;
               $color = 'bg-success';
            } elseif($approval_status == 'rejected') {
               $progress = 100;
               $color = 'bg-danger';
            } else {
               $progress = 10; // Sedang menunggu
            }
         @endphp
         <div class="progress-bar {{ $color }} progress-bar-striped progress-bar-animated" 
            role="progressbar" style="width: {{ $progress }}%"></div>
      </div>

      <div class="d-flex justify-content-between mt-2">
         <small class="text-muted" style="font-size: 11px;">Submitted</small>
         <small class="fw-bold {{ $approval_status == 'rejected' ? 'text-danger' : 'text-primary' }}" style="font-size: 11px;">
            @if($approval_status == 'pending')
               Waiting for Review
            @elseif($approval_status == 'approved')
               Finalized
            @else
               Declined
            @endif
         </small>
      </div>
      @if ($approval_status == 'approved')
         <div class="text-end">
            <small class="text-muted" style="font-size: 11px;">{{ !$for_dept_head ? $ticket->supervisor_approval_at->format('d M Y, H:i') : $ticket->dept_head_approval_at->format('d M Y, H:i') }}</small>
         </div>
      @endif

      {{-- Note jika direject --}}
      @if($approval_status == 'rejected' && $rejection_note)
         <div class="mt-2 p-2 bg-light rounded border-start border-danger border-3">
            <small class="d-block fw-bold text-danger" style="font-size: 11px;">Reason:</small>
            <small class="text-muted italic">{{ $rejection_note }}</small>
         </div>
      @endif
   </div>
</div>