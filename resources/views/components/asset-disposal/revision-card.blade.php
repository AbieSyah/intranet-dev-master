@props([
   'transaction_number',
   'comments',
   'url' => null,
   'days',
   'class' => null
])

<div class="{{ $class.' card mb-0 p-2 bg-light d-inline-block text-start border shadow-none' }}" style="min-width: 300px;">
   <div class="d-flex justify-content-between align-items-center mb-2">
      <span class="badge bg-warning text-dark">#{{ $transaction_number }}</span>
      <span class="text-danger small fw-bold line-clamp-2"><i class="ri-chat-history-line"></i> Needs Revision</span>
   </div>
   <p class="small mb-0 text-dark"><strong>Last Comment:</strong> "{{ $comments }}"</p>
   <p class="small mb-2 text-dark">{{ $days }}</p>
   @if ($url)
      <a href="{{ $url }}" class="btn btn-sm btn-warning text-dark w-100 fw-bold">
         <i class="ri-edit-box-line"></i> Fix & Resubmit
      </a>
   @endif
</div>