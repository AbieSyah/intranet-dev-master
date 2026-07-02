@extends('layouts.master')

@section('link')
   <meta name="csrf-token" content="{{ csrf_token() }}">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css"/>
   <link rel="stylesheet" href="{{  url('') }}/assets/libs/adminlte/select2/css/4.1.0/select2.min.css">
   <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
   <style>
      body { background-color: #f0f2f5; font-size: 0.9rem; }
      .card-header { font-weight: bold; border-bottom: 1px solid #eee; }
      .status-badge { font-size: 0.75rem; padding: 4px 12px; border-radius: 20px; }
      
      /* CTimeline Styles */
      .ctimeline { position: relative; padding-left: 40px; list-style: none; }
      .ctimeline-item { position: relative; width: 100%; padding: 0 0 .75rem 0 !important }
      .ctimeline-icon { 
         position: absolute; left: -32px; top: .5rem; width: 24px; height: 24px; 
         border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white;
      }
   </style>
@endsection

@section('content') 
   {{-- modal section --}}
   @if ($assetDisposal->current_status == 'waiting' && request()->route()->getName() == 'asset-disposal.review' && $assetDisposal->doc_status !== 'approved')
      <div class="modal fade" id="feedbackModal" tabindex="-1" aria-hidden="true">
         <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
               <form action="{{ route('asset-disposal.feedback', encrypt($assetDisposal->id)) }}" method="POST">
                  @csrf
                  <input type="hidden" name="action_type" id="modalActionType" value="">
                  
                  <div class="modal-header">
                     <h5 class="modal-title fw-bold" id="modalTitle">Feedback Required</h5>
                     <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                     <div class="mb-3">
                           <label class="form-label fw-bold small text-muted">REASON / COMMENTS</label>
                           <textarea class="form-control" name="comment" rows="4" required placeholder="Please provide details so the requester can make adjustments..."></textarea>
                     </div>
                     <div class="alert alert-info py-2 px-3 small border-0">
                           <i class="ri-information-line"></i> 
                           <span id="modalHelpText">This will return the request to the submitter.</span>
                     </div>
                  </div>
                  <div class="modal-footer border-0">
                     <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Cancel</button>
                     <button type="submit" id="modalSubmitBtn" class="btn btn-warning fw-bold">Confirm Action</button>
                  </div>
               </form>
            </div>
         </div>
      </div>
   @endif

   <div class="row">
      <div class="col-12">
         <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Disposal IT Asset</h4>

            <div class="page-title-right">
               <ol class="breadcrumb m-0">
                  <li class="breadcrumb-item"><a href="javascript: void(0);">Disposal</a></li>
                  <li class="breadcrumb-item active">Show</li>
               </ol>
            </div>

         </div>
      </div>
   </div>

   <div class="col-lg-12">
      <div class="row">
         <div class="col-12 col-xl-6 mb-4 order-2 order-xl-1">
            <div class="card mb-0 h-100">
               <div class="card-body">
                  <div class="d-flex justify-content-between align-items-start">
                     <div>
                        <h4 class="mb-1">Disposal Request #{{ $assetDisposal->transaction_number }}</h4>
                        @if ($assetDisposal->doc_status !== 'approved')
                           <span class="badge {{ $badgeColors[$assetDisposal->current_status] }} status-badge text-uppercase"><i class="bi bi-clock-fill me-1"></i> {{ $assetDisposal->current_status }}</span>
                        @else
                           <span class="badge bg-success status-badge text-uppercase"><i class="bi bi-clock-fill me-1"></i> {{ $assetDisposal->doc_status }}</span>
                        @endif
                        <p class="text-muted small mt-2 mb-0">Submitted by: {{ $assetDisposal->requester->fullname }}({{ $assetDisposal->created_at->format('d-M-Y H:i') }})</p>
                     </div>
                     <div class="text-end">
                        <h4 class="text-primary mb-0">Total: Rp.{{ number_format($assetDisposal->disposalItems->sum('sale_price')) }}</h4>
                     </div>
                  </div>
               </div>
            </div>
         </div>

         <div class="col-12 col-xl-6 mb-4 order-1 order-xl-2">
            <div class="card mb-0 h-100">
               @if (
                  $assetDisposal->current_status == 'waiting' && 
                  request()->route()->getName() == 'asset-disposal.review' && 
                  $assetDisposal->doc_status !== 'approved' && 
                  $assetDisposal->currentStep()->employee_id == Auth::user()->employee->id
               )
                  <div class="card-body d-flex justify-content-between align-items-center py-3">
                     <div>
                        <span class="text-muted small fw-bold">FINAL ACTION:</span>
                        <p class="mb-2"><strong>Approve As:</strong> {{ $assetDisposal->currentStep()->role_name }}</p>
                        <p class="mb-0 small text-dark">Please review all asset snapshots before deciding.</p>
                     </div>
                     <div class="d-flex gap-2">
                        @if ($assetDisposal->current_status == 'waiting')
                           <button type="button" class="btn btn-outline-warning fw-bold" data-bs-toggle="modal" data-bs-target="#feedbackModal">
                              <i class="ri-chat-history-line"></i> Send for Revision
                           </button>

                           <form id="approveForm" action="{{ route('asset-disposal.feedback', [encrypt($assetDisposal->id), 'origin' => $origin]) }}" method="POST" class="d-inline">
                              @csrf
                              <input type="hidden" name="approve" value="1">
                              <button type="submit" class="btn btn-primary px-4 fw-bold">
                                 <i class="ri-check-double-line"></i> Approve Proposal
                              </button>
                           </form>
                        @endif
                     </div>
                  </div>
               @else
                  @if (request()->route()->getName() !== "asset-disposal.review")
                     <div class="card-body text-end">
                        <a href="{{ route('asset-disposal.index') }}" class="btn btn-primary btn-label waves-effect waves-light"><i class="ri-arrow-left-fill label-icon align-middle fs-16 me-2"></i> Back</a>
                     </div>
                  @elseif($assetDisposal->currentStep()->employee_id !== Auth::user()->employee->id)
                     <div class="card-body d-flex flex-column align-items-center justify-content-center gap-3 py-5">
                        You are not an approver for this request, but you can review the details before approval.
                     </div>
                  @endif
               @endif
            </div>
         </div>
      </div>

      <div class="card">
         <div class="card-header">Buyer Information</div>
         <div class="card-body">
            <div class="row g-3 pb-3">
               <div class="col-12 col-md-6">
                  <div class="form-group">
                     <label class="form-label fw-bold text-muted">BUYER NAME</label>
                     <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="ri-user-line"></i></span>
                        <input type="text" required readonly value="{{ $assetDisposal->buyer_name?? '' }}" name="buyer_name" class="form-control border-start-0" placeholder="e.g. John Doe">
                     </div>
                  </div>
               </div>
               
               <div class="col-12 col-md-6">
                  <div class="form-group">
                     <label class="form-label fw-bold text-muted">PHONE NUMBER</label>
                     <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="ri-phone-line"></i></span>
                        <input type="number" required readonly value="{{ $assetDisposal->buyer_phone?? '' }}" name="buyer_phone" class="form-control border-start-0" placeholder="0812xxxx" inputmode="numeric">
                     </div>
                  </div>
               </div>

               <div class="col-12 col-md-6">
                  <div class="form-group">
                     <label class="form-label fw-bold text-muted">EMAIL ADDRESS</label>
                     <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="ri-mail-line"></i></span>
                        <input type="email" required readonly value="{{ $assetDisposal->buyer_email?? '' }}" name="buyer_email" class="form-control border-start-0" placeholder="buyer@example.com">
                     </div>
                  </div>
               </div>

               <div class="col-12 col-md-6">
                  <div class="form-group">
                        <label class="form-label fw-bold text-muted">OFFICE / HOME ADDRESS</label>
                        <div class="input-group">
                           <span class="input-group-text bg-light border-end-0"><i class="ri-map-pin-line"></i></span>
                           <input type="text" required readonly value="{{ $assetDisposal->buyer_address?? '' }}" name="buyer_address" class="form-control border-start-0" placeholder="Street Name, City...">
                        </div>
                  </div>
               </div>
            </div>
         </div>
      </div>

      <div class="card">
         <div class="card-header">Assets Details</div>
         <div class="card-body p-0">
            <div class="table-responsive">
               <table class="table table-hover mb-0">
                  <thead class="table-light">
                     <tr>
                        <th>Asset Code</th>
                        <th>Brand</th>
                        <th title="State of ITAsset status when disposal is requested">Status</th>
                        <th>Original Price</th>
                        <th>Disposal Price</th>
                        <th>Reason</th>
                     </tr>
                  </thead>
                  <tbody>
                     @foreach ($assetDisposal->disposalItems as $item)
                        <tr>
                           <td>{{ $item->itAsset->asset_code }}</td>
                           <td>{{ $item->itAsset->brand }}</td>
                           <td>{{ $item->current_status }}</td>
                           <td>Rp.{{ number_format($item->buy_price) }}</td>
                           <td>Rp.{{ number_format($item->sale_price) }}</td>
                           <td>{{ $item->reason?? '-' }}</td>
                        </tr>
                     @endforeach
                     <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>Total: Rp. {{ number_format($assetDisposal->disposalItems->sum('sale_price')) }}</td>
                        <td></td>
                     </tr>
                  </tbody>
               </table>
            </div>
         </div>
      </div>

      <div class="row mb-5">
         <div class="col-lg-6">
            <div class="card">
               <div class="card-header">Submitter Details</div>
               <div class="card-body">
                  <h6>Name:</h6>
                  <p class="text-muted">{{ $assetDisposal->requester->fullname }}</p>
                  
                  <h6>Submission Date:</h6>
                  <p class="text-muted">Department: {{ $assetDisposal->requester->department->name }} ({{ $assetDisposal->created_at->format('d-M-Y, h:i A') }})</p>
                  
                  <hr>
                  
                  <h6>Reason for Disposal:</h6>
                  <p class="text-muted">{{ $assetDisposal->reason }}</p>


                  <h6>Attachment:</h6>
                  @if(isset($assetDisposal) && $assetDisposal->file_path)
                     <div id="existing-file">
                        @php $extension = pathinfo($assetDisposal->file_path, PATHINFO_EXTENSION); @endphp
                        
                        @if(in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'pdf']))
                           <a href="{{ asset('storage/' . $assetDisposal->file_path) }}" target="_BLANK">
                              <img src="{{ asset('storage/' . $assetDisposal->file_path) }}" class="img-thumbnail" style="max-height: 100px;">
                           </a>
                        @else
                           <div class="p-3">
                              <i class="ri-file-pdf-fill text-danger" style="font-size: 2rem;"></i>
                              <p class="mb-0 small text-truncate">{{ basename($assetDisposal->file_path) }}</p>
                           </div>
                        @endif
                     </div>
                  @endif
               </div>
            </div>
         </div>

         <div class="col-lg-6">
            <div class="card">
               <div class="card-header">Approval History</div>
               <div class="card-body">
                  <div style="max-height: 400px; overflow-y: auto">
                     <ul class="ctimeline">
                        @foreach ($assetDisposal->logs->sortByDesc('id') as $index => $log)
                           <li class="ctimeline-item" data-bs-toggle="collapse" data-bs-target="#collapseExample{{ $index }}">
                              <div class="ctimeline-icon {{ $badgeColors[$log->status] }}"><i class="bi bi-check-lg"></i></div>
                              <div class="p-2 border rounded bg-light">
                                 <strong>
                                    @if ($log->status == 'waiting')
                                       @if (!$log->for_buyer)
                                          <span class="text-uppercase">{{ $log->status }}</span>
                                          for {{ $log->approvalPath->role_name }}'s({{ $log->approvalPath->employee->fullname?? '-' }}) Response
                                       @else
                                          <span class="text-uppercase">{{ $log->status }}</span>
                                          for {{ $log->assetDisposal->buyer_name }}'s(Buyer) Confirmation
                                       @endif
                                    @elseif($log->status == 'revised')
                                       <span class="text-uppercase">{{ $log->status }}</span>
                                       by {{ $assetDisposal->requester->fullname }}
                                    @elseif ($log->status == 'canceled')
                                       <span class="text-uppercase">Request {{ $log->status }}</span>
                                       by {{ $assetDisposal->requester->fullname }}(Requester)
                                    @else
                                       {{ $log->approvalPath->role_name?? '' }} 
                                       <span class="text-uppercase">{{ $log->status }}</span>
                                    @endif
                                 </strong>
                                 @if ($log->status == 'revision')
                                    <div class="collapse {{ strlen($log->comments) < 100? 'show' : '' }}" id="collapseExample{{ $index }}">
                                       <div class="py-2 text-small">
                                          {{ $log->comments?? 'N/A' }}
                                       </div>
                                    </div>
                                 @endif
                                 <div class="small text-muted">{{ $log->created_at->format('d-M-Y, h:i A') }} ({{ $log->created_at->diffForHumans() }})</div>
                              </div>
                           </li>
                        @endforeach
                        @if ($assetDisposal->doc_status == 'approved')
                           <li class="ctimeline-item">
                              <div class="ctimeline-icon bg-primary"><i class="bi bi-check-lg"></i></div>
                                 <div class="p-2 border rounded bg-light">
                                    <strong>
                                       Proposal Approved 
                                       <span class="text-uppercase">(Finished)</span>
                                    </strong>
                                 <div class="small text-muted">{{ $log->created_at->format('d-m-y, h:i A') }}</div>
                              </div>
                           </li>
                        @endif
                     </ul>
                  </div>
                  {{-- <div class="d-flex justify-content-end gap-2 mt-3">
                     <button class="btn btn-warning btn-sm px-4">Fix & Resubmit</button>
                     <button class="btn btn-primary btn-sm px-4">Cancel</button>
                  </div> --}}
               </div>
            </div>
         </div>
      </div>
   </div>
@endsection

@section('script')
   <!-- Datatables -->
   <script src="/assets/libs/Datatables/DataTables-1.13.1/js/jquery.dataTables.min.js"></script>
   <script src="/assets/libs/Datatables/DataTables-1.13.1/js/dataTables.bootstrap5.min.js"></script>
   <script src="/assets/libs/Datatables/Responsive-2.4.0/js/dataTables.responsive.min.js"></script>
   <script src="/assets/js/pages/datatables.init.js"></script>
   <!-- Select2 -->
   <script src="/assets/libs/adminlte/select2/js/4.1.0/select2.min.js"></script>
   <!-- Sweetalert -->
   <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
@endsection

@section('javascript')
   <script>
      $(document).ready(function() {
         $('#approveForm').on('submit', function(e) {
            e.preventDefault(); // Stop the form from submitting immediately
            
            let form = this;

            Swal.fire({
               title: 'Are you sure?',
               text: "You are about to approve this asset disposal request.",
               icon: 'question',
               showCancelButton: true,
               confirmButtonColor: '#0d6efd', // Matches btn-primary
               cancelButtonColor: '#6c757d',
               confirmButtonText: 'Yes, Approve it!',
               cancelButtonText: 'Cancel'
            }).then((result) => {
               if (result.isConfirmed) {
                  // Show loading state while the database updates
                  Swal.fire({
                     title: 'Processing...',
                     text: 'Updating disposal status',
                     allowOutsideClick: false,
                     didOpen: () => {
                           Swal.showLoading();
                     }
                  });
                  
                  // Submit the form
                  form.submit();
               }
            });
         });
      });
   </script>

   <script>
      @if(Session::has('success'))
         toastr.options = {
            "closeButton" : true,
            "progressBar" : true,
            "positionClass": "toast-bottom-right"
         }
         toastr.success("{{ session('success') }}");
      @endif
      @if(Session::has('error'))
         toastr.options = {
            "closeButton" : true,
            "progressBar" : true,
            "positionClass": "toast-bottom-right"
         }
         toastr.error("{{ session('error') }}");
      @endif
   </script>
@endsection