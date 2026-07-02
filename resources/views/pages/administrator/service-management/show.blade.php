@extends(
   request()->routeIs('service-management.workspace') || request()->routeIs('myservice-desk.workspace') ? 
      'layouts.master' : (
         request()->routeIs('service-desk.workspace') ? 
            'layouts.general' : 
         'layouts.simple'
      )
)

@section('link')
   <meta name="csrf-token" content="{{ csrf_token() }}">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css"/>
   <link rel="stylesheet" href="{{  url('') }}/assets/libs/adminlte/select2/css/4.1.0/select2.min.css">
   <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
   <link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.css" rel="stylesheet" />
@endsection

@section('content')
      @if (request()->routeIs('service-management.workspace'))
         <div class="row">
            <div class="col-12">
               <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                  <h4 class="mb-sm-0">IT Service Desk</h4>

                  <div class="page-title-right">
                     <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ $role == App\Models\ServiceTicket::ROLE_IT? route('service-management.index') : route('service-desk.index') }}">IT Service Desk</a></li>
                        <li class="breadcrumb-item active">{{ strtoupper($role) }}</li>
                     </ol>
                  </div>

               </div>
            </div>
         </div>
      @elseif(request()->routeIs('myservice-desk.workspace'))
         <div class="container-fluid">
            <div class="profile-foreground position-relative mx-n4 mt-n4">
               <div class="profile-wid-bg">
                  <img src="/assets/images/salonpas-bg.jpg" alt="" class="profile-wid-img" />
               </div>
            </div>
            <div class="pt-4 mb-4 mb-lg-3 pb-lg-4">
               <div class="row g-4">
                  <div class="col-auto">
                     <div class="profile-user position-relative d-inline-block mx-auto">
                        @if ($user->employee && !empty($user->employee->avatar))
                           <div id="avatar-user">
                              <img src="{{ asset('storage/avatars/' . $user->employee->avatar) }}"
                                 class="show-image rounded-circle avatar-xl img-thumbnail user-profile-image"
                                 alt="user-profile-image">
                           </div>
                        @else
                           <div id="avatar-user">
                              <img src="{{ asset('storage/avatars/user.jpg') }}"
                                 class="rounded-circle avatar-xl img-thumbnail user-profile-image"
                                 alt="user-profile-image">
                           </div>
                        @endif
                        <div class="avatar-xs p-0 rounded-circle profile-photo-edit">
                           <input onchange="avatarValidation(this);" id="profile-img-file-input" type="file"
                              name="image" class="image profile-img-file-input"
                              accept="image/jpg,image/jpeg,image/png,image/JPG,image/JPEG,image/PNG">
                           <label for="profile-img-file-input" class="profile-photo-edit avatar-xs">
                              <span class="avatar-title rounded-circle bg-light text-body">
                                 <i class="ri-camera-fill"></i>
                              </span>
                           </label>
                        </div>
                     </div>
                  </div>
                  <!--end col-->
                  <div class="col">
                     <div class="p-2">
                        <h3 class="text-white mb-1">{{ $user->employee?->fullname ?? $user->name }}</h3>
                        <p class="text-white-75">{{ $user->employee?->email ?? $user->email }}</p>
                        <div class="hstack text-white-50 gap-1">
                           <div class="me-2"><i class="ri-map-pin-user-line me-1 text-white-75 fs-16 align-middle"></i>
                              {{ $user->employee?->area?->name ?? 'N/A' }}
                           </div>
                           <div><i class="ri-building-line me-1 text-white-75 fs-16 align-middle"></i>
                              {{ $user->employee?->department?->name ?? 'N/A' }}
                           </div>
                        </div>
                        <div class="hstack text-white-50 gap-1">
                           <div class="me-2">
                              @if (!empty($user->employee?->level?->nama))
                                 <i class="ri-contacts-book-2-line me-1 text-white-75 fs-16 align-middle"></i>
                                 {{ $user->employee->level->nama }}
                              @endif
                           </div>
                           <div>
                              @if (!empty($user->employee?->position?->nama))
                                 <i class="ri-contacts-book-line me-1 text-white-75 fs-16 align-middle"></i>
                                 {{ $user->employee->position->nama }}
                              @endif
                           </div>
                        </div>
                     </div>
                  </div>
                  <!--end col-->
                  <div class="col-12 col-lg-auto order-last order-lg-0">
                     <div class="row text text-white-50 text-center">
                        <div class="col-lg-6 col-4">
                           <div class="p-2">
                              <!-- <h4 class="text-white mb-1">{{ $user->employee->nik }}</h4>
                                 <p class="fs-14 mb-0">NIK</p> -->
                           </div>
                        </div>
                     </div>
                  </div>
                  <!--end col-->

               </div>
               <!--end row-->
            </div>

            <div class="row">
               <div class="col-lg-12">
                  <div>
                     @include('partials.navbar2')
                  </div>
               </div>
            </div>
      @endif

         <div class="row @if (request()->is('myservice-desk*')) pt-4 @endif">
            <div class="col-lg-8">

               @if ($role == App\Models\ServiceTicket::ROLE_USER || $role == App\Models\ServiceTicket::ROLE_IT || $role == App\Models\ServiceTicket::ROLE_CC)
                  <div class="card bg-white p-3">
                     <div class="text-center">
                        <div class="alert fs-5 px-3 py-2 text-light fw-semibold mb-0" id="statusBadge" style="background-color: {{ $ticketPriority['color'] ?? '#aaa' }}">
                           Priority: <span class="text-uppercase">{{ $ticketPriority['label'] ?? 'unsigned' }}</span>
                        </div>
                     </div>
                     @if($ticket->current_status !== 'closed' && $ticket->current_status !== 'cancelled')
                        <x-service-management.message :ticket="$ticket" :role="$role" />
                     @else
                        <div class="alert alert-secondary mb-0 text-center py-2">
                           <i class="ri-lock-line me-1"></i> This ticket is {{ $ticket->current_status }}. Chat history is read-only.
                        </div>
                     @endif
                  </div>
               @endif

               <div class="card bg-transparent mb-4">
                  <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                     <div>
                        <span class="badge bg-soft-primary text-primary mb-1">TICKET #{{ $ticket->no_ticket }}</span>
                        <div id="ticketSubject" class="d-flex align-items-center gap-2">
                           <h4 class="mb-0">{{ $ticket->subject }}</h4>
                           @if ($role == App\Models\ServiceTicket::ROLE_IT && $ticket->current_status !== 'closed' && $ticket->current_status !== 'cancelled')
                              <button class="btn btn-sm btn-rounded-pill" id="editSubjectBtn"><i class="ri-edit-line"></i></button>
                           @endif
                        </div>
                        @if ($role == App\Models\ServiceTicket::ROLE_IT && $ticket->current_status !== 'closed' && $ticket->current_status !== 'cancelled')
                           <div id="editableTicketSubject" class="d-none">
                              <form id="editSubjectForm" class="d-flex gap-2 align-items-center" method="POST">
                                 @csrf
                                 <input type="text" name="subject" class="fs-4" value="{{ $ticket->subject }}" required>
                                 <button type="submit" class="btn btn-sm btn-primary"><i class="ri-check-line"></i></button>
                                 <button type="button" id="cancelEditSubject" class="btn btn-sm btn-secondary"><i class="ri-close-line"></i></button>
                              </form>
                           </div>
                        @endif
                     </div>
                     <div class="text-end">
                        <span class="badge bg-light text-dark px-3 py-2 fs-6 text-capitalize" id="statusBadge">{{ $ticket->current_status }} {{ $isReopen? '(Reopen)' : '' }}</span>
                     </div>
                  </div>
                  
               <div class="message-container p-4 overflow-y-auto" style="display: flex; flex-direction: column-reverse;">
                  {{-- sortByDesc + flex-direction: column-reverse memastikan pesan terbaru di bawah dan scroll otomatis terjaga --}}
                  @foreach($ticket->messages->sortBy('created_at') as $msg)
                     @if ($msg->role !== App\Models\ServiceTicketMessage::ROLE_SERVICE_CHANGE)
                        @if($msg->role == 'system')
                           @if ($role !== App\Models\ServiceTicket::ROLE_IT && !$msg->is_internal)
                              <div class="text-center mt-3 mb-5">
                                 <span class="bg-gray-100 d-inline-block border shadow px-3 py-1 fs-6 text-uppercase" style="border-radius: 999px">
                                    <i class="ri-settings-3-line mr-1"></i> {{ $msg->message }} - {{ $msg->created_at->format('d M Y H:i') }}
                                 </span>
                              </div>
                           @elseif($role == App\Models\ServiceTicket::ROLE_IT)
                              <div class="text-center mt-3 mb-5">
                                 <span class="bg-gray-100 d-inline-block border shadow px-3 py-1 fs-6 text-uppercase" style="border-radius: 999px">
                                    <i class="ri-settings-3-line mr-1"></i> {{ $msg->message }} - {{ $msg->created_at->format('d M Y H:i') }}{{ $role == 'it' ? "(" . ($msg->sender ? "{$msg->sender->fullname} - NIK: {$msg->sender->nik}" : "SYSTEM") . ")" : '' }}
                                 </span>
                              </div>
                           @endif
                        @else
                           <div class="d-flex gap-3 position-relative {{ $msg->role == 'it' ? 'flex-row-reverse' : 'flex-row' }} mb-4 items-end">
                              <div class="flex-shrink-0 {{ $msg->role == 'it' ? 'ml-3' : 'mr-3' }} mb-2">
                                 <div class="relative">
                                    <span class="inline-flex items-center justify-center rounded border-2 border-white shadow-sm px-2" 
                                       style="width: 35px; height: 35px; background-color: {{ $msg->role == 'it' ? '#2563eb' : '#64748b' }};">
                                       
                                       <span class="text-white font-bold text-xs">
                                          {{ strtoupper(substr($msg->sender->fullname?? 'U', 0, 1)) }}
                                       </span>
                                    </span>
                                 </div>
                              </div>

                              <div class="max-w-[75%] {{ $msg->role == 'it' ? 'text-right' : 'text-left' }} pb-0">
                                 <div class="relative {{ $msg->role == 'it' ? 'bg-primary text-white rounded-2 rounded-tr-none ms-auto' : 'bg-white border text-gray-800 rounded-2 rounded-tl-none' }} p-3 pb-1 shadow-sm" style="word-break: break-word; max-width: 100%;">
                                    <style>
                                       .it-message a {
                                          color: #bfdbfe;
                                          text-underline-position: under;
                                          text-decoration: underline;
                                       }
                                    </style>
                                    <div class="text-sm {{ $msg->role == 'it'? 'it-message' : '' }}" style="text-align: justify;">
                                       {!! str_replace('&nbsp;', ' ', $msg->message) !!}
                                    </div>
                                    
                                    @if($msg->media->count() > 0)
                                       <div class="mt-2 pt-2 border-t">
                                          {{-- Files List --}}
                                          @php
                                             $files = $msg->media->filter(fn($m) => !in_array($m->extension, ['jpg', 'jpeg', 'png']));
                                          @endphp
                                          @if($files->count() > 0)
                                             <div class="mb-3">
                                                <ul class="list-unstyled mb-0">
                                                   @foreach($files as $media)
                                                      <li>
                                                         <a href="{{ asset('storage/'.$media->path) }}" target="_blank" 
                                                            class="flex items-center rounded text-xs {{ $msg->role == 'it' ? 'bg-blue-700 text-white' : 'bg-gray-50 text-blue-600' }}">
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
                                             $images = $msg->media->filter(fn($m) => in_array($m->extension, ['jpg', 'jpeg', 'png']));
                                          @endphp
                                          @if($images->count() > 0)
                                             <div class="d-flex flex-wrap gap-2 justify-content-{{ $msg->role == 'it' ? 'end' : 'start' }}">
                                                @foreach($images as $media)
                                                   <a href="{{ asset('storage/'.$media->path) }}" class="d-block" target="_blank">
                                                      <img src="{{ asset('storage/'.$media->path) }}" class="rounded shadow-sm hover:opacity-90 transition-opacity" style="width: 80px; height: 80px; object-fit: cover">
                                                   </a>
                                                @endforeach
                                             </div>
                                          @endif
                                       </div>
                                    @endif
                                    <div class="text-end mt-3" style="{{ $msg->role == 'it' ? 'left-0' : 'right: 1rem' }}; bottom: 1.5rem; white-space: nowrap;">
                                       {{ $msg->created_at->format('d M Y H:i') }}{{ ' - '.$msg->sender->fullname }}
                                    </div>
                                 </div>
                              </div>
                           </div>
                        @endif
                     @endif
                  @endforeach
               </div>
               </div>
            </div>
            
            <div class="col-lg-4">
               @php
                  $isClosed = $ticket->current_status === App\Models\ServiceStatusHistory::STATUS_CLOSED || $ticket->current_status === App\Models\ServiceStatusHistory::STATUS_CANCELLED;
                  $hasRequestApproval = 
                        $ticket->dept_head_approval === App\Models\ServiceTicket::APPROVAL_STATUS_PENDING || 
                        $ticket->supervisor_approval === App\Models\ServiceTicket::APPROVAL_STATUS_PENDING;
                  
                  // Status Approval
                  $requestApproved = $ticket->dept_head_approval === App\Models\ServiceTicket::APPROVAL_STATUS_APPROVED && $ticket->supervisor_approval === App\Models\ServiceTicket::APPROVAL_STATUS_APPROVED;
                  $changeApproved = !$ticket->serviceChange || ($ticket->serviceChange && $ticket->serviceChange->status === App\Models\ServiceChange::STATUS_APPROVED);
                  $changeDone = $ticket->serviceChange && $ticket->serviceChange->status === App\Models\ServiceChange::STATUS_DONE;

                  // Default Values
                  $buttonText = 'Close Ticket';
                  $buttonClass = 'btn btn-warning text-dark';
                  $disabled = '';
                  $id = 'closeTicket';

                  // 1. Cek Jika Sudah Closed
                  if ($isClosed) {
                     $buttonText = 'Ticket Closed';
                     $buttonClass = 'btn btn-success';
                     $disabled = 'disabled';
                     $id = '';

                     if($ticket->current_status === App\Models\ServiceStatusHistory::STATUS_CANCELLED) {
                        $buttonText = 'Ticket Cancelled';
                        $buttonClass = 'btn btn-danger';
                     }
                  } 
                  // 2. Cek Jika Ada Hambatan (Waiting)
                  else {
                     $waitingReasons = [];

                     if ($hasRequestApproval && !$requestApproved) {
                        $waitingReasons[] = "Request Approval";
                     }

                     if($ticket->serviceChange) {
                        if (!$changeDone && !$changeApproved) {
                           $waitingReasons[] = "Change Approval";
                        } elseif (!$changeDone) {
                           $waitingReasons[] = "Change Execution";
                        }
                     }

                     // Jika ada alasan menunggu, ubah state tombol
                     if (!empty($waitingReasons)) {
                        $reasonText = implode(' and ', $waitingReasons);
                        $buttonText = "Close Ticket (Waiting for {$reasonText})";
                        $buttonClass = 'btn btn-secondary';
                        $disabled = 'disabled';
                        $id = '';
                     }
                  }
               @endphp

               <div class="d-flex flex-column">
                  @if ($ticket->current_status !== App\Models\ServiceStatusHistory::STATUS_OPEN && $role == App\Models\ServiceTicket::ROLE_IT)
                     <div class="mb-2 flex-1">
                        <button class="{{ $buttonClass }} w-100" {{ $disabled }} id="{{ $id }}">
                           <i class="bi bi-lock-fill me-1 {{ $disabled ? '' : 'd-none' }}"></i>
                           {{ $buttonText }}
                        </button>
                     </div>
                  @endif

                  @if ($ticket->current_status == App\Models\ServiceStatusHistory::STATUS_CLOSED && $role == App\Models\ServiceTicket::ROLE_IT)
                     <div class="mb-2">
                        <button class="btn btn-outline-primary w-100" id="reopenTicket">
                           <i class="bi bi-lock-open-fill me-1"></i>
                           Reopen Ticket 
                        </button>
                     </div>
                  @endif
               </div>

               @php
                  // Tentukan apakah approver saat ini sudah menyetujui atau belum
                  $isPending = false;
                  if ($role === App\Models\ServiceTicket::ROLE_SUPERVISOR) {
                     $isPending = ($ticket->supervisor_approval == App\Models\ServiceTicket::APPROVAL_STATUS_PENDING);
                  } elseif ($role === App\Models\ServiceTicket::ROLE_DEPT_HEAD) {
                     $isPending = ($ticket->dept_head_approval == App\Models\ServiceTicket::APPROVAL_STATUS_PENDING);
                  }
               @endphp

               {{-- Hanya tampilkan Card jika role adalah Approver DAN statusnya masih Pending --}}
               @if (in_array($role, [App\Models\ServiceTicket::ROLE_SUPERVISOR, App\Models\ServiceTicket::ROLE_DEPT_HEAD]) && $isPending)
                  @if ($role == App\Models\ServiceTicket::ROLE_DEPT_HEAD && $ticket->supervisor_approval != App\Models\ServiceTicket::APPROVAL_STATUS_APPROVED)
                     <div class="alert alert-warning">
                        <i class="ri-error-warning-line me-1"></i>
                        Waiting for Supervisor Approval
                     </div>
                  @else
                     <div class="card shadow-sm mb-3">
                        <div class="card-header bg-white fw-bold">Action Required: Approval</div>
                        <div class="card-body">
                           <form id="approverForm">
                              @csrf
                              <button type="submit" class="btn btn-success w-100">
                                 <i class="fas fa-check-circle me-1"></i>
                                 Approve As 
                                 {{ $role === App\Models\ServiceTicket::ROLE_SUPERVISOR ? 'Supervisor' : 'Department Head' }}
                                 ({{ $role === App\Models\ServiceTicket::ROLE_SUPERVISOR ? ($ticket->supervisor->fullname ?? 'N/A') : ($ticket->deptHead->fullname ?? 'N/A') }})
                              </button>
                           </form>
                        </div>
                     </div>
                  @endif
               @endif

               <div class="card shadow-sm mb-4">
                  <div class="card-header bg-white fw-bold d-flex justify-content-between align-items-center">
                     Mapping Information
                     @if ($role == App\Models\ServiceTicket::ROLE_IT && $ticket->current_status !== 'open')
                        <button
                           type="button"
                           class="btn btn-sm btn-outline-primary rounded-pill"
                           onclick="navigator.clipboard.writeText('{{ URL::temporarySignedRoute('service-ticket.approve-workspace', now()->addHours(24), ['id' => encrypt($ticket->id), 'role' => encrypt('viewer')]) }}').then(() => toastr.success('Link copied to clipboard'))"
                           title="Public link only lasts for 24 hours"
                        >Copy Public Link</button>
                     @endif
                  </div>
                  <div class="card-body">
                     <div class="mb-3">
                        <label class="text-muted small d-block">Current Status</label>
                        @if ($role == App\Models\ServiceTicket::ROLE_IT && $ticket->current_status !== 'open' && $ticket->current_status !== 'closed' && $ticket->current_status !== 'cancelled')
                           {{-- <form id="status-update-form-{{ $ticket->no_ticket }}" action="{{ route('service-ticket.change-status', encrypt($ticket->id)) }}" method="POST">
                              @csrf
                              <input type="hidden" name="current_status" value="{{ \App\Models\ServiceStatusHistory::STATUS_PROCESS }}">
                           </form> --}}
                           <div class="d-flex justify-content-between align-items-center">
                              <span class="badge bg-light text-dark px-3 py-2 fs-6 text-capitalize">{{ $ticket->current_status }}</span>

                              <button type="button" 
                                    onclick="confirmStatusUpdate('{{ $ticket->no_ticket }}')"
                                    title="Change Status to {{ $ticket->current_status == "process"? 'HOLD' : "PROCESS" }}"
                                    class="btn btn-outline-{{ $ticket->current_status == "process"? 'dark' : "primary" }} rounded-pill hover:shadow-lg transition-all">
                                 <i class="ri-arrow-left-right-line"></i>
                              </button>
                           </div>
                        @else
                           <span class="badge bg-light text-dark px-3 py-2 fs-6 text-capitalize">{{ $ticket->current_status }}</span>
                        @endif
                     </div>
                     <div class="mb-3">
                        <label class="text-muted small d-block">Submitter</label>
                        <h6>{{ $ticket->submitter->fullname }} (NIK: {{ $ticket->submitter->nik }})</h6>
                     </div>
                     <div class="mb-3">
                        <label class="text-muted small d-block">Department</label>
                        <h6>{{ $ticket->employee_department ?? $ticket->submitter->department?->name ?? "N/A" }}</h6>
                     </div>
                     <div class="mb-3">
                        <label class="text-muted small d-block">Position</label>
                        <h6>{{ $ticket->employee_position ?? $ticket->submitter->position?->nama ?? "N/A" }}</h6>
                     </div>
                     <div class="mb-3">
                        <label class="text-muted small d-block">Proposed Date</label>
                        <h6>{{ $ticket->created_at->format('d M Y h:i') }}</h6>
                     </div>
                     <div class="mb-3">
                        <label class="text-muted small d-block">CC Recipients</label>
                        @if($ticket->ccs->count() > 0)
                           <div class="d-flex flex-wrap gap-1">
                              @foreach($ticket->ccs as $cc)
                                 <div class="badge bg-soft-primary text-primary border border-primary-subtle d-flex align-items-center p-1 px-2 rounded-pill">
                                    <span class="me-2 avatar-title rounded-circle bg-primary text-white text-[10px]" style="width: 20px; height: 20px; display: flex; align-items: center; justify-content: center;">
                                       {{ substr($cc->employee->fullname, 0, 1) }}
                                    </span>
                                    <span class="text-xs">{{ $cc->employee->fullname }}</span>
                                 </div>
                              @endforeach
                           </div>
                        @else
                           <p class="text-muted text-xs text-center my-2 italic">No CC recipients added.</p>
                        @endif
                     </div>

                  </div>
               </div>

               {{-- ----------------------- ANALYSIS SECTION ----------------------- --}}

               @if($ticket->current_status !== 'open')
                  <div class="card shadow-sm mb-4 border-start border-4 border-primary">
                     <div class="card-header bg-white fw-bold">Analysis Result</div>
                     <div class="card-body">
                        @if ($ticket->type == App\Models\ServiceTicket::TYPE_IT_INITIATIVE)
                           <div class="mb-3">
                              <label class="text-muted small d-block">Report For:</label>
                              <h6>{{ $ticket->reportFor->nik }} - {{ strtoupper($ticket->reportFor->fullname).' - '.strtoupper($ticket->reportFor->position?->nama ?? "N/A") }} - ({{ strtoupper($ticket->reportFor->department?->name ?? "N/A") }})</h6>
                           </div>
                        @endif
                        <div class="mb-3">
                           <label class="text-muted small d-block">Category:</label>
                           <h6>{{ strtoupper(str_replace('_', ' ', $ticket->category?? "N/A")) }}</h6>
                        </div>
                        <div class="mb-3">
                           <label class="text-muted small d-block">Catalog:</label>
                           <h6>{{ strtoupper($ticket->catalog?? "N/A") }}</h6>
                        </div>
                        <div class="mb-3">
                           <label class="text-muted small d-block">Type:</label>
                           <h6>{{ strtoupper(str_replace('_', ' ', $ticket->type?? "N/A")) }}</h6>
                        </div>
                        <div class="mb-3">
                           <label class="text-muted small d-block">Priority:</label>
                           <h6 class="mb-0" title="{{ 'Total Score = (Impact x Urgency) + Scope + Risk Register Score' }}">
                              {{ isset($ticketPriority['label'])? strtoupper($ticketPriority['label'] . "(Total Score: " . $ticket->total_score . ")") : 'N/A' }}
                              <i class="ri-information-line"></i>
                           </h6>
                           @if ($role == App\Models\ServiceTicket::ROLE_IT)
                              <span class="badge bg-soft-primary text-primary mb-1" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-content="{{ $impactMetric->where('score', $ticket->impact)->first()->definition ?? 'N/A' }}">
                                 Impact : {{ $ticket->impact ?? 'N/A' }}({{ $impactMetric->where('score', $ticket->impact)->first()->definition ?? 'N/A' }})
                              </span>
                              <span class="badge bg-soft-primary text-primary mb-1" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-content="{{ $urgencyMetric->where('score', $ticket->urgency)->first()->definition ?? 'N/A' }}">
                                 Urgency : {{ $ticket->urgency ?? 'N/A' }}({{ $urgencyMetric->where('score', $ticket->urgency)->first()->definition ?? 'N/A' }})
                              </span>
                              <span class="badge bg-soft-primary text-primary mb-1" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-content="{{ $scopeMetric->where('score', $ticket->scope)->first()->definition ?? 'N/A' }}">
                                 Scope : {{ $ticket->scope ?? 'N/A' }}({{ $scopeMetric->where('score', $ticket->scope)->first()->definition ?? 'N/A' }})
                              </span>
                              <span class="badge bg-soft-primary text-primary mb-1" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-content="{{ $ticket->riskRegister->name?? 'N/A' }}">
                                 Risk Register : {{ $ticket->risk_register_score ?? 'N/A' }}({{ $ticket->riskRegister->name?? 'N/A' }})
                              </span>
                              <span class="badge bg-soft-primary text-primary mb-1" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-content="{{ $ticket->riskRegister->name?? 'N/A' }}">
                                 SLA : {{ $ticket->priority->formated_sla ?? 'N/A' }}
                              </span>
                              @endif
                           <div>
                           </div>
                        </div>
                        <div class="mb-3">
                           @if($ticket->itAssets->count() > 0)
                              <label class="text-muted small d-block">Assigned Asset(s):</label>
                              <ul class="ps-4"> 
                                 @foreach($ticket->itAssets as $asset)
                                    <li class="mb-2">
                                       {{ strtoupper($asset->employee->fullname).' - '.strtoupper($asset->employee->position?->nama?? "N/A") }} ({{ strtoupper($asset->employee->department?->name?? "N/A") }}) <br>
                                       IT Asset: {{ $asset->asset_code }} - {{ $asset->brand ?? 'N/A' }}({{ $asset->status }}) 
                                       @if ($role == App\Models\ServiceTicket::ROLE_IT && $ticket->current_status !== 'closed' && $ticket->current_status !== 'cancelled')
                                          <button class="btn btn-outline-secondary btn-sm btn-rounded edit-status-asset-btn" data-it-asset="{{ encrypt($asset->id) }}" data-status="{{ $asset->status }}" data-asset-code="{{ $asset->asset_code }}"><i class="ri-edit-line"></i></button>
                                          <button class="btn btn-outline-warning btn-sm btn-rounded remove-asset-btn" data-it-asset="{{ encrypt($asset->id) }}" title="Remove Asset"><i class="ri-delete-bin-line"></i></button>
                                       @endif
                                    </li>
                                 @endforeach
                              </ul>
                           @else
                              <p><em>No Assets assigned.</em></p>
                           @endif

                           @if ($role == 'it' && $ticket->current_status !== 'closed' && $ticket->current_status !== 'cancelled')
                              <button class="btn btn-outline-secondary add-asset-btn w-100" data-bs-toggle="modal" data-bs-target="#addAssetModal"><i class="ri-add-line"></i> Add IT Asset</button>
                           @endif
                        </div>
                     </div>
                  </div>

                  @if ($role == App\Models\ServiceTicket::ROLE_IT)
                     <div class="modal fade" id="addAssetModal">
                        <div class="modal-dialog">
                           <div class="modal-content">
                              <div class="modal-header">
                                 <h5 class="modal-title">Add IT Asset</h5>
                                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                              </div>
                              <div class="modal-body">
                                 <form action="{{ route('service-management.add-asset', ['id' => encrypt($ticket->id)]) }}" method="POST" id="addAssetForm">
                                    @csrf
                                    <input type="hidden" name="ticket_id" value="{{ encrypt($ticket->id) }}">
                                    <div class="mb-3">
                                       <select class="form-control select2" id="itAssetSelect" name="asset_id" required data-dropdown-parent="#addAssetModal">
                                          <option value="">-- Select IT Asset --</option>
                                          @foreach($assets as $asset)
                                             <option value="{{ encrypt($asset->id) }}">
                                                {{ $asset->asset_code }} - {{ $asset->brand ?? 'N/A' }} ({{ $asset->employee->fullname }})
                                             </option>
                                          @endforeach
                                       </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100">Add Asset</button>
                                 </form>
                              </div>
                           </div>
                        </div>
                     </div>

                     <div class="modal fade" id="updateItAssetStatusModal">
                        <div class="modal-dialog">
                           <div class="modal-content">
                              <div class="modal-header">
                                 <h5 class="modal-title">Update IT Asset Status</h5>
                                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                              </div>
                              <div class="modal-body">
                                 <form id="updateItAssetStatusForm">
                                    @csrf
                                    <input type="hidden" name="it_asset_id" id="itAssetId">
                                    <div class="mb-3">
                                       <label for="assetCode" class="form-label">Asset Code</label>
                                       <input type="text" class="form-control" id="assetCode" name="asset_code" readonly>
                                    </div>
                                    <div class="mb-3">
                                       <label for="assetCode" class="form-label">Current Status</label>
                                       <input type="text" class="form-control" id="currentStatus" name="current_status" readonly>
                                    </div>
                                    <div class="mb-3">
                                       <label for="assetStatus" class="form-label">Status</label>
                                       <select class="form-select" id="assetStatus" name="status" required>
                                          <option value="">Select Status</option>
                                          <option value="{{ App\Models\ITAsset::STATUS_ACTIVE }}">Active</option>
                                          <option value="{{ App\Models\ITAsset::STATUS_BACKUP }}">Backup</option>
                                          <option value="{{ App\Models\ITAsset::STATUS_BROKEN }}">Broken</option>
                                       </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Update Status</button>
                                 </form>
                              </div>
                           </div>
                        </div>
                     </div>

                     @if ($ticket->current_status !== 'closed' && $ticket->current_status !== 'cancelled')
                        <button data-bs-toggle="collapse" data-bs-target="#analysisSection" class="btn btn-outline-primary w-100 mb-4">
                           <i class="ri-shield-check-line me-1 pe-none"></i> Re-analyze Ticket
                        </button>
                        <div class="collapse" id="analysisSection">
                           <div>
                              <x-service-management.analysis :ticket="$ticket" class="mb-4" :employees="$employees" :risk_registers="$riskRegisters"/>
                           </div>
                        </div>
                     @endif
                  @endif
               @elseif($ticket->current_status == 'open' && $role == App\Models\ServiceTicket::ROLE_IT)
                  <x-service-management.analysis :ticket="$ticket" class="mb-4" :employees="$employees" :risk_registers="$riskRegisters"/>
               @endif

               @if (
                  ($ticket->current_status == 'process' || $ticket->current_status == 'hold') && $role == 'it' &&
                  (!$ticket->supervisor_id || !$ticket->dept_head_id)
               )
                  <x-service-management.request-approval :ticket="$ticket" :role="$role" :employees="$employees"/>
               @endif

               @if ($ticket->supervisor && $ticket->deptHead)
                  <div class="d-flex mb-4 gap-3 flex-wrap justify-content-center">
                     <div class="flex-1">
                        <div class="mb-3 gap-2 bg-white rounded">
                           <x-service-desk.approval-progress class="mb-0" name="Direct Supervisor" :ticket="$ticket" />
                           @if ($role == App\Models\ServiceTicket::ROLE_IT)
                              @if ($ticket->supervisor_approval == App\Models\ServiceTicket::APPROVAL_STATUS_PENDING)
                                 <div class="px-3 pb-3 d-flex gap-2">
                                    <button class="btn w-100 btn-outline-secondary resend-email-btn" data-role="supervisor" title="Resend Email">
                                       <i class="ri-mail-send-line"></i> Resend Email
                                    </button>
                                    <button type="button" class="btn btn-outline-warning copy-link-btn" data-role="{{ encrypt('supervisor') }}" title="Copy Public Link">
                                       <i class="ri-clipboard-line"></i>
                                    </button>
                                 </div>
                              @endif
                           @endif
                        </div>
                        <div class="gap-2 bg-white rounded">
                           <x-service-desk.approval-progress class="mb-0" name="Dept Head" for_dept_head :ticket="$ticket" />
                           @if ($role == App\Models\ServiceTicket::ROLE_IT)
                              @if ($ticket->supervisor_approval == App\Models\ServiceTicket::APPROVAL_STATUS_APPROVED && $ticket->dept_head_approval == App\Models\ServiceTicket::APPROVAL_STATUS_PENDING)
                                 <div class="px-3 pb-3 d-flex gap-2">
                                    <button class="btn w-100 btn-outline-secondary resend-email-btn" data-role="dept_head" title="Resend Email">
                                       <i class="ri-mail-send-line"></i> Resend Email
                                    </button>
                                    <button type="button" class="btn btn-outline-warning copy-link-btn" data-role="{{ encrypt('dept_head') }}" title="Copy Public Link">
                                       <i class="ri-clipboard-line"></i>
                                    </button>
                                 </div>
                              @endif
                           @endif
                        </div>
                     </div>
                     <div class="bg-white rounded shadow p-2 flex-fill flex-col d-flex flex-column justify-content-center align-items-center" style="max-width: 100px">
                        <a target="_blank" href="{{ URL::signedRoute('service-desk.request-approval.report', ['id' => encrypt($ticket->id)]) }}">
                           <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M5 4H15V8H19V20H5V4ZM3.9985 2C3.44749 2 3 2.44405 3 2.9918V21.0082C3 21.5447 3.44476 22 3.9934 22H20.0066C20.5551 22 21 21.5489 21 20.9925L20.9997 7L16 2H3.9985ZM10.4999 7.5C10.4999 9.07749 10.0442 10.9373 9.27493 12.6534C8.50287 14.3757 7.46143 15.8502 6.37524 16.7191L7.55464 18.3321C10.4821 16.3804 13.7233 15.0421 16.8585 15.49L17.3162 13.5513C14.6435 12.6604 12.4999 9.98994 12.4999 7.5H10.4999ZM11.0999 13.4716C11.3673 12.8752 11.6042 12.2563 11.8037 11.6285C12.2753 12.3531 12.8553 13.0182 13.5101 13.5953C12.5283 13.7711 11.5665 14.0596 10.6352 14.4276C10.7999 14.1143 10.9551 13.7948 11.0999 13.4716Z"></path></svg>
                           <span class="fs-11 text-center">
                              Approval <br> Document
                           </span>
                        </a>
                     </div>
                  </div>
               @endif

               {{-- Service Change Management Section --}}
               @if (($role == App\Models\ServiceTicket::ROLE_IT || $role == App\Models\ServiceTicket::ROLE_SERVICE_CHANGE) && $ticket->current_status !== 'open' && $ticket->current_status !== 'cancelled' && ($ticket->serviceChange || $ticket->current_status !== 'closed'))
                  <x-service-management.service-change :ticket="$ticket" :role="$role" class="mb-4" />
               @endif
               {{-- END Service Change Management Section --}}

               @if ($ticket->current_status == 'open' && ($role == 'it' || ($role == 'user' && $ticket->submitter_id == Auth::user()->employee_id)))
                  <div class="text-center">
                     <x-service-desk.cancel-form :ticket="$ticket" :role="$role == 'it' ? 'it' : 'user'" />
                  </div>
               @endif
               {{-- ----------------------- END TRIAGE SECTION ----------------------- --}}
            </div>
         </div>
         @if (request()->is('myservice-desk*'))
         </div> {{-- Close Container Fluid --}}
         @endif

      <div class="modal fade" id="replaceModal" tabindex="-1">
         <div class="modal-dialog">
            <div class="modal-content">
               <div class="modal-header">
                  <h5 class="modal-title">Swap IT Asset</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
               </div>
               <div class="modal-body">
                  <div class="alert alert-info py-2 small">
                     <i class="ri-information-line"></i> Swapping will unlink the current asset and assign a new one to John Doe.
                  </div>
                  <div class="mb-3">
                     <label class="form-label">Old Asset</label>
                     <input type="text" class="form-control bg-light" value="LENOVO-THINKPAD-X1" readonly>
                  </div>
                  <div class="mb-3">
                     <label class="form-label">Choose Replacement Asset</label>
                     <select class="form-select">
                        <option value="">-- Select Available Inventory --</option>
                        <option value="102">DELL-LATITUDE-5420 (#IT-2024-001)</option>
                        <option value="105">MACBOOK-AIR-M2 (#IT-2024-005)</option>
                     </select>
                  </div>
               </div>
               <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                  <button type="button" class="btn btn-primary">Process Swap</button>
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
   {{-- add asset handler --}}
   <script>
      $(document).ready(function() {
         $('.select2').select2()
         
         $('#addAssetForm').on('submit', function(e) {
            e.preventDefault()

            const formData = $(this).serialize()

            $.ajax({
               url: $(this).attr('action'),
               method: 'POST',
               data: formData,
               success: function(response) {
                  toastr.success('IT Asset assigned successfully')
                  $('#itAssetSelect').val('')
                  $('#addAssetForm').addClass('d-none')
                  location.reload()
               },
               error: function(xhr) {
                  Swal.fire({
                     title: 'Error',
                     text: xhr.responseJSON?.message || 'Failed to assign IT asset.',
                     icon: 'error'
                  });
               }
            })
         })
      })
   </script>
   {{-- end asset handler --}}

   <script>
      // edit it asset status handler
      $(document).ready(function() {
         const assetModal = new bootstrap.Modal(document.getElementById('updateItAssetStatusModal'));
         $('.edit-status-asset-btn').on('click', function() {
            const assetId = $(this).data('it-asset');
            const assetCode = $(this).data('asset-code');
            const currentStatus = $(this).data('status');

            $.ajax({
               url: `/administrator/it-asset/${assetId}/get`,
               method: 'GET',
               success: function(response) {
                  $('#itAssetId').val(assetId);
                  $('#assetCode').val(assetCode);
                  $('#currentStatus').val(currentStatus.charAt(0).toUpperCase() + currentStatus.slice(1));
               }
            })

            assetModal.show()
         })

         $(".remove-asset-btn").on("click", function() {
            const assetId = $(this).data("it-asset");

            Swal.fire({
               title: 'Remove IT Asset?',
               text: "This will unassign the asset from the ticket.",
               icon: 'warning',
               showCancelButton: true,
               confirmButtonColor: '#d33',
               cancelButtonColor: '#3085d6',
               confirmButtonText: 'Yes, Remove it!'
            }).then((result) => {
               if (result.isConfirmed) {
                  $.ajax({
                     url: `/administrator/service-desk/${{{ encrypt($ticket->id) }}}/remove-asset`,
                     method: 'POST',
                     data: {
                        _token: '{{ csrf_token() }}',
                        asset_id: assetId
                     },
                     success: function(response) {
                        Swal.fire({
                           title: 'Removed!',
                           text: 'IT Asset has been removed from the ticket.',
                           icon: 'success',
                           timer: 1500,
                           showConfirmButton: false
                        }).then(() => location.reload())
                     },
                     error: function(xhr) {
                        Swal.fire({
                           title: 'Error',
                           text: xhr.responseJSON?.message || 'Failed to remove IT asset.',
                           icon: 'error'
                        });
                     }
                  })
               }
            })
         })

         $('.copy-link-btn').click(function() {
            const btn = $(this)
            const role = btn.data('role')
            const encryptedTicketId = `{{ encrypt($ticket->id) }}`;
            const baseUrl = `/administrator/service-desk/get-public-link/${encryptedTicketId}/${role}`;
            
            $.ajax({
               url: baseUrl,
               method: 'POST',
               data: {
                  _token: '{{ csrf_token() }}',
               },
               success: function(response) {
                  navigator.clipboard.writeText(response.link).then(() => toastr.success('Link copied to clipboard'))
               },
               error: function(xhr) {
                  Swal.fire({
                     title: 'Error',
                     text: xhr.responseJSON?.message || 'Failed to generate link.',
                     icon: 'error'
                  });
               }
            })
         })

         $('#updateItAssetStatusForm').on('submit', function(e) {
            e.preventDefault();

            const formData = $(this).serialize();

            Swal.fire({
               title: 'Update IT Asset Status?',
               text: "This will update the status of the assigned IT asset.",
               icon: 'warning',
               showCancelButton: true,
               confirmButtonColor: '#3085d6',
               cancelButtonColor: '#d33',
               confirmButtonText: 'Yes, Update it!',
               showLoaderOnConfirm: true,
               preConfirm: () => {
                  $.ajax({
                     url: '{{ route("it_asset.update-status", ":id") }}'.replace(':id', $('#itAssetId').val()),
                     method: 'POST',
                     data: formData,
                     success: function(response) {
                        Swal.fire({
                           title: 'Success!',
                           text: 'IT Asset status has been updated.',
                           icon: 'success',
                           timer: 1500,
                           showConfirmButton: false
                        }).then(() => {
                           assetModal.hide();
                           location.reload();
                        });
                     },
                     error: function(xhr) {
                        Swal.fire({
                           title: 'Error',
                           text: xhr.responseJSON?.message || 'Failed to update asset status.',
                           icon: 'error'
                        });
                     }
                  })
               }
            })
         })
      })
      // end edit it asset status handler
   </script>

   <script>
      // --------------------------------- APPROVER FORM HANDLER ---------------------------------
      function confirmStatusUpdate(ticketNo) {
         Swal.fire({
            title: 'Change Status Confirmation',
            text: "You are about to change the ticket status from {{ $ticket->current_status }} to {{ $ticket->current_status == 'process'? 'hold' : 'process' }}. Please provide a reason for this change:",
            input: 'text',
            inputPlaceholder: 'Enter reason here...',
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: 'Yes, Update Status',
            cancelButtonText: 'Cancel',
            showLoaderOnConfirm: true, // Menampilkan loading di tombol
            reverseButtons: true,
            inputValidator: (value) => {
                  if (!value) return 'Reason is required!'
            },
            preConfirm: (reason) => {
                  // Bagian AJAX
                  // const form = $('#status-update-form-' + ticketNo);
                  return $.ajax({
                     url: '{{ route('service-ticket.change-status', encrypt($ticket->id)) }}',
                     method: 'POST',
                     data: {
                        _token: '{{ csrf_token() }}',
                        reason: reason
                     },
                     success: function(response) {
                        return response;
                     },
                     error: function(xhr) {
                        Swal.showValidationMessage(`Request failed: ${xhr.responseJSON.message || xhr.statusText}`);
                     }
                  });
            },
            allowOutsideClick: () => !Swal.isLoading()
         }).then((result) => {
            if (result.isConfirmed) {
                  Swal.fire({
                     title: 'Success!',
                     text: 'Ticket status has been updated.',
                     icon: 'success',
                     timer: 1500,
                     showConfirmButton: false
                  }).then(() => {
                     // Refresh halaman setelah user melihat pesan sukses
                     location.reload();
                  });
            }
         });
      }
      // --------------------------------- APPROVER FORM HANDLER ---------------------------------
   </script>


   <script>
      // --------------------------------- APPROVER FORM HANDLER ---------------------------------
      @if ($role == App\Models\ServiceTicket::ROLE_SUPERVISOR || $role == App\Models\ServiceTicket::ROLE_DEPT_HEAD)
         $("#approverForm").submit(function(e) {
            e.preventDefault()

            const formData = new FormData(this)
            
            formData.append('approver', "{{ encrypt($approverId?? $employee->id) }}");
            formData.append('role', "{{ encrypt($role) }}");

            Swal.fire({
               title: 'Are you sure?',
               text: "You are about to approve this request.",
               icon: 'warning',
               showCancelButton: true,
               confirmButtonColor: '#28a745',
               cancelButtonColor: '#d33',
               confirmButtonText: 'Yes, Approve it!',
               input: 'textarea',
               reverseButtons: true,
               inputPlaceholder: 'Enter note here...',
               inputValidator: (value) => {
                  if (!value) {
                     return 'Note is required to approve!';
                  }
                  if (value.length > 250) {
                     return 'Note cannot exceed 250 characters!';
                  }
               },
               showLoaderOnConfirm: true,
               preConfirm: (note) => {
                  formData.append('note', note);
                  return $.ajax({
                     url: "{{ URL::signedRoute('service-management.approve', encrypt($ticket->id)) }}",
                     method: "POST",
                     data: formData,
                     processData: false,
                     contentType: false,
                  }).done(response => {
                     return response;
                  }).fail(xhr => {
                     const message = xhr.responseJSON?.message || 'An error occurred while processing your request.';
                     Swal.showValidationMessage(message); 
                     
                     Swal.hideLoading(); 
                  });;
               },
               allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
               if (result.isConfirmed) {
                  Swal.fire(
                     'Approved!',
                     'The request has been approved.',
                     'success'
                  ).then(() => {
                     location.reload();
                  });
               }
            });
         })
      @endif
      // --------------------------------- END APPROVER FORM HANDLER ---------------------------------
   </script>

   
   <script>
      $(document).ready(function() {
         // --------------------------------- CLOSE TICKET HANDLER ---------------------------------
         $('#closeTicket').on('click', function(e) {
            e.preventDefault();

            Swal.fire({
               title: "Close this ticket?",
               text: "Once closed, this ticket cannot be reopened for further discussion.",
               icon: "warning",
               showCancelButton: true,
               confirmButtonColor: "#34c38f", // Green (success)
               cancelButtonColor: "#f46a6a", // Red (danger)
               confirmButtonText: "Yes, Close Ticket",
               cancelButtonText: "Cancel",
               showLoaderOnConfirm: true,
               reverseButtons: true,
               preConfirm: () => {
                  return $.ajax({
                     url: "{{ route('service-ticket.close', encrypt($ticket->id)) }}",
                     method: "POST",
                     data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        _method: 'PUT' // Menggunakan PUT untuk update status
                     },
                     success: function(response) {
                        return response;
                     },
                     error: function(xhr) {
                        Swal.showValidationMessage(
                           `Request failed: ${xhr.responseJSON.message || 'Server Error'}`
                        );
                     }
                  });
               },
               allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
               if (result.isConfirmed) {
                  Swal.fire({
                     title: "Closed!",
                     text: "The ticket status has been updated to Closed.",
                     icon: "success",
                     timer: 1500,
                     showConfirmButton: false
                  }).then(() => {
                     location.reload(); // Refresh untuk update UI (menghilangkan input chat, dll)
                  });
               }
            });
         });

         $('.resend-email-btn').on('click', function() {
            const role = $(this).data('role');
            const url = "{{ route('service-management.resend-notification', ['id' => encrypt($ticket->id), 'role' => '']) }}/" + role;

            $(this).prop('disabled', true).html('<i class="ri-loader-4-line spin"></i> Resending...');

            let countdown = 30;
            const $btn = $(this);
            $btn.prop('disabled', true).html(`<i class="ri-timer-line"></i> Resend in ${countdown}s`);
            
            let timer;

            $.ajax({
               url: url,
               method: "POST",
               data: {
                  _token: $('meta[name="csrf-token"]').attr('content')
               },
               success: function(response) {
                  toastr.options = {
                     "closeButton" : true,
                     "progressBar" : true,
                     "positionClass": "toast-bottom-right"
                  }
                  toastr.success("Email has been resent to the " + role + ".");

                  timer = setInterval(() => {
                     countdown--;
                     $btn.html(`<i class="ri-timer-line"></i> Resend in ${countdown}s`);
                     
                     if (countdown === 0) {
                        clearInterval(timer);
                        $btn.prop('disabled', false).html('<i class="ri-mail-send-line"></i> Resend Email');
                     }
                  }, 1000)
               },
               error: function(xhr) {
                  toastr.options = {
                     "closeButton" : true,
                     "progressBar" : true,
                     "positionClass": "toast-bottom-right"
                  }
                  toastr.error("Failed to resend email: " + (xhr.responseJSON.message || 'Server Error'));
               }
            });
         });
         // --------------------------------- END CLOSE TICKET HANDLER ---------------------------------
      })
   </script>

   <script>
      // Editable Ticket Subject Handler
      const ticketSubject = $('#ticketSubject');
      const editableTicketSubject = $('#editableTicketSubject');
      const editSubjectForm = $('#editSubjectForm');
      const editBtn = $('#editSubjectBtn');
      const cancelBtn = $('#cancelEditSubject');

      const submitFunction = () => {
         const newSubject = editSubjectForm.find('input[name="subject"]').val().trim();
         if (newSubject.length === 0) {
            toastr.options = {
               "closeButton" : true,
               "progressBar" : true,
               "positionClass": "toast-bottom-right"
            }
            toastr.error("Subject cannot be empty.");
            return;
         }

         $.ajax({
            url: "{{ route('service-management.update-subject', encrypt($ticket->id)) }}",
            method: "POST",
            data: {
               _token: $('meta[name="csrf-token"]').attr('content'),
               subject: newSubject,
            },
            success: function(response) {
               ticketSubject.removeClass('d-none');
               ticketSubject.find('h4').text(newSubject);
               editableTicketSubject.addClass('d-none').val(newSubject);
               toastr.options = {
                  "closeButton" : true,
                  "progressBar" : true,
                  "positionClass": "toast-bottom-right"
               }
               toastr.success("Ticket subject updated successfully.");
            },
            error: function(xhr) {
               toastr.options = {
                  "closeButton" : true,
                  "progressBar" : true,
                  "positionClass": "toast-bottom-right"
               }
               toastr.error("Failed to update subject: " + (xhr.responseJSON.message || 'Server Error'));
            }
         });
      };

      editBtn.on('click', function() {
         ticketSubject.addClass('d-none');
         editableTicketSubject.removeClass('d-none');
         editableTicketSubject.focus();
      });

      cancelBtn.on('click', function() {
         ticketSubject.removeClass('d-none');
         editableTicketSubject.addClass('d-none');
      });

      editSubjectForm.on('blur', function() {
         submitFunction();
      });

      editSubjectForm.on('submit', function(e) {
         e.preventDefault();
         submitFunction();
      });

      // End editable ticket subject handler
   </script>

   {{-- Reopen Ticket Handler --}}
   <script>
      $("#reopenTicket").on('click', function(e) {
         e.preventDefault();

         Swal.fire({
            title: "Reopen this ticket?",
            text: "This will change the ticket status back to Open and allow further updates.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#34c38f", // Green (success)
            cancelButtonColor: "#f46a6a", // Red (danger)
            confirmButtonText: "Yes, Reopen Ticket",
            cancelButtonText: "Cancel",
            showLoaderOnConfirm: true,
            reverseButtons: true,
            input: 'textarea',
            inputPlaceholder: "Enter reason for reopening...",
            inputValidator: (value) => {
               if (!value) {
                  return 'Please enter a reason for reopening the ticket!'; // Error message displayed if empty
               }
            },
            preConfirm: (result) => {
               return $.ajax({
                  url: "{{ route('service-ticket.reopen', encrypt($ticket->id)) }}",
                  method: "POST",
                  data: {
                     _token: $('meta[name="csrf-token"]').attr('content'),
                     _method: 'PUT', // Menggunakan PUT untuk update status
                     note: result
                  },
                  success: function(response) {
                     return response;
                  },
                  error: function(xhr) {
                     Swal.showValidationMessage(
                        `Request failed: ${xhr.responseJSON.message || 'Server Error'}`
                     );
                  }
               });
            },
            allowOutsideClick: () => !Swal.isLoading()
         }).then((result) => {
            if (result.isConfirmed) {
               Swal.fire({
                  title: "Reopened!",
                  text: "The ticket status has been updated to Open.",
                  icon: "success",
                  timer: 1500,
                  showConfirmButton: false
               }).then(() => {
                  location.reload(); // Refresh untuk update UI
               });
            }
         });
      });
   </script>
   {{-- End Reopen Ticket Handler --}}

   <script>
      @if(Session::has('success'))
         toastr.options = {
            "closeButton" : true,
            "progressBar" : true,
            "positionClass": "toast-bottom-right"
         }
         toastr.success("{{ session('success') }}");
      @endif
   </script>
@endsection