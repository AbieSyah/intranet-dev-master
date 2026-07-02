@props([
   'transaction_number',
   'requester',
   'division',
   'url',
   'days',
   'text' => "Review",
])

<div {{ $attributes->merge(['class' => 'card mb-0 p-2 bg-light d-inline-block text-start border shadow-none w-100']) }} style="min-width: 300px;">
   <div class="d-flex justify-content-between align-items-center mb-2 gap-2">
      <span class="badge bg-primary">#{{ $transaction_number }}</span>
      <span class="text-primary text-end small fw-bold"><i class="ri-time-line"></i> Awaiting {{ $text }}</span>
   </div>
   <p class="small mb-0 text-dark"><strong>Submitted By:</strong> {{ $requester }} ({!! $division !!})</p>
   <p class="small mb-2 text-dark">{{ $days }}</p>
   <div class="d-flex gap-2">
      <a href="{{ $url }}" class="btn btn-sm btn-outline-primary w-100 fw-bold">
         <i class="ri-eye-line"></i> {{ $text }} 
      </a>
      {{-- <a href="#" class="btn btn-sm btn-primary w-100 fw-bold">
         <i class="ri-checkbox-circle-line"></i> Decide
      </a> --}}
   </div>
</div>