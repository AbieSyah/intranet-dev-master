@extends('layouts.master')

@section('content') 
   <div class="row">
      <div class="col-12">
         <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">List Change Management</h4>

            <div class="page-title-right">
               <ol class="breadcrumb m-0">
                  <li class="breadcrumb-item"><a href="{{ route('service-change.index') }}">Change Management</a></li>
                  <li class="breadcrumb-item active">Show</li>
               </ol>
            </div>

         </div>
      </div>
   </div>

   <div class="container-fluid py-4">
      <div class="row">
         <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
               <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-3">
                  <h6 class="mb-0 fw-bold text-light"><i class="ri-git-merge-line me-2"></i>Change Management Details</h6>
                  <span class="badge bg-light text-primary text-uppercase">
                     {{ $serviceChange->status ?? 'Pending' }}
                  </span>
               </div>
               <div class="card-body p-4">
                  <h5 class="fw-bold text-dark mb-1">{{ $serviceChange->subject }}</h5>
                  <p class="text-muted small mb-4">Requested on {{ $serviceChange->created_at->format('d M Y, H:i') }}</p>
                  
                  <div>
                     <label class="text-muted small fw-bold d-block text-uppercase mb-1">Change Number</label>
                     <p class="text-dark">{{ $serviceChange->change_no }}</p>
                  </div>
                  <div>
                     <label class="text-muted small fw-bold d-block text-uppercase mb-1">Subject</label>
                     <p class="text-dark">{{ $serviceChange->ticket->subject }}</p>
                  </div>
                  <div>
                     <label class="text-muted small fw-bold d-block text-uppercase mb-1">IT Notice</label>
                     <p class="text-dark">{{ $serviceChange->it_notice ?? 'No reason provided' }}</p>
                  </div>
                  <div>
                     <label class="text-muted small fw-bold d-block text-uppercase mb-1">Execution Plan</label>
                     <p class="text-dark">Starting from <strong>{{ $serviceChange->planned_start ? $serviceChange->planned_start->format('d M Y, H:i') : 'N/A' }}</strong> to <strong>{{ $serviceChange->planned_end ? $serviceChange->planned_end->format('d M Y, H:i') : 'N/A' }}</strong></p>
                  </div>
                  {{-- <div class="col-md-6">
                     <label class="text-muted small fw-bold d-block text-uppercase mb-1">Priority Result</label>
                     <p class="text-dark">{{ strtoupper($serviceChange->ticket->priority->level) ?? 'N/A' }}</p>
                  </div> --}}
                  <div>
                     <label class="text-muted small fw-bold d-block text-uppercase mb-1">Description</label>
                     <div class="bg-light p-3 rounded shadow-sm border-start border-4 border-info">
                        {!! $serviceChange->ticket->description !!}
                     </div>
                  </div>
               </div>
            </div>

            @if ($serviceChange->status == App\Models\ServiceChange::STATUS_APPROVED || $serviceChange->status == App\Models\ServiceChange::STATUS_DONE)
               <div class="card shadow-sm border-0 mb-4">
                  <div class="card-header bg-white fw-bold py-3 d-flex justify-content-between align-items-center">
                     <span>
                        <i class="ri-attachment-line me-2"></i>Actual Execution Evidence
                     </span>

                     <button class="btn btn-sm btn-outline-primary" onclick="toggleActualExecutionForm(this)">
                        <i class="ri-edit-line me-1"></i><span class="text">Edit</span>
                     </button>
                  </div>
                  <div class="card-body">
                     @if ($serviceChange->status == App\Models\ServiceChange::STATUS_APPROVED || $serviceChange->status == App\Models\ServiceChange::STATUS_DONE)
                        <div id="actual-execution-form-container" class="{{ $serviceChange->status == App\Models\ServiceChange::STATUS_DONE? 'd-none' : '' }}">
                           <x-service-management.message
                              :isEdit="$serviceChange->status == App\Models\ServiceChange::STATUS_DONE"
                              :ticketMessage="$serviceChangeMessage"
                              :ticket="$serviceChange->ticket"
                              :role="App\Models\ServiceTicketMessage::ROLE_SERVICE_CHANGE"
                              :isInternal="true"
                           />
                        </div>
                     @endif
                     @if($serviceChange->status == App\Models\ServiceChange::STATUS_DONE)
                        <div id="actual-execution-container">
                           <div> 
                              <label class="text-muted small fw-bold d-block text-uppercase mb-1">Actual execution has been completed: </label>
                              <p class="text-dark">Starting from <strong>{{ $serviceChange->actual_start ? $serviceChange->actual_start->format('d M Y, H:i') : 'N/A' }}</strong> to <strong>{{ $serviceChange->actual_end ? $serviceChange->actual_end->format('d M Y, H:i') : 'N/A' }}</strong></p>
                           </div>
                           <div>
                              <label class="text-muted small fw-bold d-block text-uppercase mb-1">Executor: </label>
                              <p class="text-dark">{{ $serviceChangeMessage ? $serviceChangeMessage->sender->fullname.' - '.$serviceChangeMessage->sender->nik : 'N/A' }}</p>
                           </div>
                           <div class="mb-4">
                              <label class="text-muted small fw-bold d-block text-uppercase mb-1">Notes: </label>
                              <div class="bg-light p-3 rounded shadow-sm border-start border-4 border-info">
                                 {!! $serviceChangeMessage ? $serviceChangeMessage->message : 'No description available.' !!}
                              </div>
                           </div>
                           @if ($serviceChangeMessage->media->count() == 0)
                              <p class="text-muted">No evidence files uploaded.</p>
                           @else
                              <div>
                                 <label class="text-muted small fw-bold d-block text-uppercase mb-1">Attachments: </label>
                                 <div class="d-flex flex-wrap gap-3">
                                    @if($serviceChangeMessage->media->count() > 0)
                                       <div class="mt-2 pt-2 border-t">
                                          {{-- Files List --}}
                                          @php
                                             $files = $serviceChangeMessage->media->filter(fn($m) => !in_array($m->extension, ['jpg', 'jpeg', 'png']));
                                          @endphp
                                          @if($files->count() > 0)
                                             <div class="mb-3">
                                                <ul class="list-unstyled mb-0">
                                                   @foreach($files as $media)
                                                      <li>
                                                         <a href="{{ asset('storage/'.$media->path) }}" target="_blank" 
                                                            class="flex items-center rounded text-xs {{ $serviceChangeMessage->role == 'it' ? 'bg-blue-700 text-white' : 'bg-gray-50 text-blue-600' }}">
                                                            <i class="ri-file-pdf-line mr-1 text-lg"></i>
                                                            <span class="truncate max-w-[100px]">{{ $media->name }}</span>
                                                         </a>
                                                      </li>
                                                   @endforeach
                                                </ul>
                                             </div>
                                          @endif

                                          {{-- Images at Bottom --}}
                                          @php
                                             $images = $serviceChangeMessage->media->filter(fn($m) => in_array($m->extension, ['jpg', 'jpeg', 'png']));
                                          @endphp
                                          @if($images->count() > 0)
                                             <div class="d-flex flex-wrap gap-2 justify-content-{{ $serviceChangeMessage->role == 'it' ? 'end' : 'start' }}">
                                                @foreach($images as $media)
                                                   <a href="{{ asset('storage/'.$media->path) }}" class="d-block" target="_blank">
                                                      <img src="{{ asset('storage/'.$media->path) }}" class="rounded shadow-sm hover:opacity-90 transition-opacity" style="width: 80px; height: 80px; object-fit: cover">
                                                   </a>
                                                @endforeach
                                             </div>
                                          @endif
                                       </div>
                                    @endif
                                 </div>
                              </div>
                           @endif
                        </div>
                     @endif
                  </div>
               </div>
            @endif
         </div>

         <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-4">
               <div class="card-header bg-dark text-white fw-bold py-3">
                  <i class="ri-ticket-2-line me-2"></i>Related Ticket
               </div>
               <div class="card-body">
                  <div class="mb-3">
                     <label class="text-muted small d-block">Ticket Number</label>
                     <a href="{{ route('service-management.workspace', ['id' => encrypt($serviceChange->ticket->id), 'role' => encrypt('viewer')]) }}" class="fw-bold text-primary">#{{ $serviceChange->ticket->no_ticket }}</a>
                  </div>
                  <div class="mb-3">
                     <label class="text-muted small d-block">Category</label>
                     <span class="badge bg-soft-info text-info" style="font-size: .75rem">{{ strtoupper(str_replace('_', ' ', $serviceChange->ticket->category)) }}</span>
                  </div>
                  <div class="mb-3">
                     <label class="text-muted small d-block">Catalog</label>
                     <span class="badge bg-soft-primary text-primary" style="font-size: .8rem">{{ strtoupper(str_replace('_', ' ', $serviceChange->ticket->catalog)) }}</span>
                  </div>
                  <div class="mb-3">
                     <label class="text-muted small d-block">Source</label>
                     <span class="badge bg-soft-primary text-primary" style="font-size: .8rem">{{ strtoupper(str_replace('_', ' ', $serviceChange->ticket->type)) }}</span>
                  </div>
                  <div class="mb-0">
                     <label class="text-muted small d-block">Priority</label>
                     <div class="d-flex align-items-center">
                        <i class="ri-flashlight-fill text-warning me-1"></i>
                        <h6 class="mb-0">{{ strtoupper($serviceChange->ticket->priority->level) }} (Total Score: {{ $serviceChange->ticket->total_score }})</h6>
                     </div>
                     <span class="badge bg-soft-primary text-primary mb-1" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-content="{{ $impactMetric->where('score', $serviceChange->ticket->impact)->first()->definition ?? 'N/A' }}">
                        Impact : {{ $serviceChange->ticket->impact ?? 'N/A' }}({{ $impactMetric->where('score', $serviceChange->ticket->impact)->first()->definition ?? 'N/A' }})
                     </span>
                     <span class="badge bg-soft-primary text-primary mb-1" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-content="{{ $urgencyMetric->where('score', $serviceChange->ticket->urgency)->first()->definition ?? 'N/A' }}">
                        Urgency : {{ $serviceChange->ticket->urgency ?? 'N/A' }}({{ $urgencyMetric->where('score', $serviceChange->ticket->urgency)->first()->definition ?? 'N/A' }})
                     </span>
                     <span class="badge bg-soft-primary text-primary mb-1" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-content="{{ $scopeMetric->where('score', $serviceChange->ticket->scope)->first()->definition ?? 'N/A' }}">
                        Scope : {{ $serviceChange->ticket->scope ?? 'N/A' }}({{ $scopeMetric->where('score', $serviceChange->ticket->scope)->first()->definition ?? 'N/A' }})
                     </span>
                     <span class="badge bg-soft-primary text-primary mb-1" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-content="{{ $serviceChange->ticket->riskRegister->name?? 'N/A' }}">
                        Risk Register : {{ $serviceChange->ticket->risk_register_score ?? 'N/A' }}({{ $serviceChange->ticket->riskRegister->name?? 'N/A' }})
                     </span>
                     <span class="badge bg-soft-primary text-primary mb-1" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-content="{{ $serviceChange->ticket->riskRegister->name?? 'N/A' }}">
                        SLA : {{ $serviceChange->ticket->priority->formated_sla ?? 'N/A' }}
                     </span>
                  </div>
               </div>
            </div>

            <x-service-management.service-change :ticket="$serviceChange->ticket" role="service_change" class="mb-4" />

            <div class="card shadow-sm border-0">
               <div class="card-header bg-white fw-bold py-3">
                  <i class="ri-user-settings-line me-2"></i>Related Users
               </div>
               <ul class="list-group list-group-flush">
                  <li class="list-group-item d-flex align-items-center py-3">
                     <div class="avatar-sm d-flex align-items-center justify-content-center bg-soft-primary text-primary rounded-circle me-3 p-2">
                        {!! isset($serviceChange->approver->avatar)? "<img src='" . asset('storage/avatars/' . $serviceChange->approver->avatar) . "' alt='Avatar' class='img-fluid rounded-circle'>" : "A" !!}
                     </div>
                     <div>
                        <span class="d-block fw-bold small">Service Change Approved By</span>
                        <span class="text-muted small">{{ $serviceChange->approver->fullname? "{$serviceChange->approver->fullname} - {$serviceChange->approver->position->nama}({$serviceChange->approver->department->name})" : 'N/A' }}</span>
                     </div>
                  </li>
                  <li class="list-group-item d-flex align-items-center py-3">
                     <div class="avatar-sm d-flex align-items-center justify-content-center bg-soft-secondary text-secondary rounded-circle me-3 p-2">
                        {!! isset($serviceChange->proposer->avatar)? "<img src='" . asset('storage/avatars/' . $serviceChange->proposer->avatar) . "' alt='Avatar' class='img-fluid rounded-circle'>" : "P" !!}
                     </div>
                     <div>
                        <span class="d-block fw-bold small">Service Change Proposed By</span>
                        <span class="text-muted small">{{ $serviceChange->proposer->fullname? "{$serviceChange->proposer->fullname} - {$serviceChange->proposer->position->nama}({$serviceChange->proposer->department->name})" : 'N/A' }}</span>
                     </div>
                  </li>
                  <li class="list-group-item d-flex align-items-center py-3">
                     <div class="avatar-sm d-flex align-items-center justify-content-center bg-soft-secondary text-secondary rounded-circle me-3 p-2">
                        {!! isset($serviceChange->ticket->submitter->avatar)? "<img src='" . asset('storage/avatars/' . $serviceChange->ticket->submitter->avatar) . "' alt='Avatar' class='img-fluid rounded-circle'>" : "TP" !!}
                     </div>
                     <div>
                        <span class="d-block fw-bold small">Ticket Proposed By</span>
                        <span class="text-muted small">{{ $serviceChange->ticket->submitter->fullname? "{$serviceChange->ticket->submitter->fullname} - {$serviceChange->ticket->submitter->position->nama}({$serviceChange->ticket->submitter->department->name})" : 'N/A' }}</span>
                     </div>
                  </li>
               </ul>
            </div>
         </div>
      </div>
   </div>

   <script>
      function toggleActualExecutionForm(e) {
         console.log(e);
         
         const formContainer = document.getElementById('actual-execution-form-container');
         const executionContainer = document.getElementById('actual-execution-container');
         if (formContainer && executionContainer) {
            formContainer.classList.toggle('d-none');
            executionContainer.classList.toggle('d-none');
            e.querySelector('.text').textContent = formContainer.classList.contains('d-none') ? 'Edit' : 'Back';
         }
      }
   </script>
@endsection