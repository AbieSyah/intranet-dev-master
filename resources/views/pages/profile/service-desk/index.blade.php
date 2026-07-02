@extends(request()->is('myservice-desk*') ? 'layouts.master' : 'layouts.general')

@section('link')
   <meta name="csrf-token" content="{{ csrf_token() }}">
   <!-- Required meta tags -->
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
   <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css" />
   <!-- Datatables-->
   <link href="{{ url('') }}/assets/libs/Datatables/DataTables-1.13.1/css/dataTables.bootstrap5.css" rel="stylesheet"
      type="text/css" />
   <link href="{{ url('') }}/assets/libs/Datatables/Buttons-2.3.3/css/buttons.dataTables.css" rel="stylesheet"
      type="text/css" />
   <link href="{{ url('') }}/assets/libs/Datatables/Responsive-2.4.0/css/responsive.bootstrap.css" rel="stylesheet"
      type="text/css" />
   <!-- Toastr Notifications-->
   <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
   <!-- Select2-->
   <link rel="stylesheet" href="{{ url('') }}/assets/libs/adminlte/select2/css/4.1.0/select2.min.css">
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

   <style>
      .avatar-circle {
         width: 45px; height: 45px; border-radius: 50%;
         display: flex; align-items: center; justify-content: center; font-weight: bold;
      }
   </style>
@endsection

@section('content') 
   <div class="container-fluid">
      @if (request()->is('myservice-desk*'))
         <div class="profile-foreground position-relative mx-n4 mt-n4">
            <div class="profile-wid-bg">
               <img src="/assets/images/salonpas-bg.jpg" alt="" class="profile-wid-img" />
            </div>
         </div>
         <div class="pt-4 mb-4 mb-lg-3 pb-lg-4">
            <div class="row g-4">
               <div class="col-auto">
                  <div class="profile-user position-relative d-inline-block mx-auto">
                     @if (!empty($user->employee->avatar))
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
                     <h3 class="text-white mb-1">{{ $user->employee->fullname }}</h3>
                     <p class="text-white-75">{{ $user->employee->email }}</p>
                     <div class="hstack text-white-50 gap-1">
                        <div class="me-2"><i class="ri-map-pin-user-line me-1 text-white-75 fs-16 align-middle"></i>
                           {{ $user->employee->area->name }}
                        </div>
                        <div><i class="ri-building-line me-1 text-white-75 fs-16 align-middle"></i>
                           {{ $user->employee->department->name }}
                        </div>
                     </div>
                     <div class="hstack text-white-50 gap-1">
                        <div class="me-2">
                           @if (!empty($user->employee->level->nama))
                              <i class="ri-contacts-book-2-line me-1 text-white-75 fs-16 align-middle"></i>
                              {{ $user->employee->level->nama }}
                           @endif
                        </div>
                        <div>
                           @if (!empty($user->employee->position->nama))
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
      @endif

      <div class="row">
         <div class="col-lg-12">
            <div>
               @if (request()->is('myservice-desk'))
                  @include('partials.navbar2')
               @endif

               <div class="row row-cols-2">
                  {{-- @if ($myTicketApprovalList->count())         
                     <div class="col-6">
                        <div class="card border-start border-primary border-4 shadow-sm mt-4">
                           <div class="card-body">
                              <div class="d-flex mb-3">
                                 <div class="flex-shrink-0">
                                    <i class="ri-file-search-fill text-primary fs-1"></i>
                                 </div>
                                 <div class="flex-grow-1 ms-3">
                                    <h5 class="mb-1 fw-bold">Ticket Approval Queue: Action Pending</h5>
                                    <p class="mb-0 text-muted small">You have 1 new ticket awaiting your approval.</p>
                                 </div>
                              </div>
                              <div style="max-height: 600px; overflow-y: auto;">
                                 <div class="d-flex flex-wrap gap-3">
                                    @foreach ($myTicketApprovalList as $approval)
                                       <x-asset-disposal.approval-card class="flex-1"
                                          transaction_number="{{ $approval->no_ticket }}" 
                                          requester='{{ $approval->submitter->fullname }}' 
                                          division="{{ $approval->submitter->department->name }}" 
                                          url="{{ $approval->redirectUrl }}" 
                                          text="{{ $approval->custom_text }}"
                                          days="{{ $approval->updated_at->diffForHumans() }}" />
                                    @endforeach
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  @endif

                  @if ($myChangeApprovalList->count())         
                     <div class="col-6">
                        <div class="card border-start border-warning border-4 shadow-sm mt-4">
                           <div class="card-body">
                              <div class="d-flex mb-3">
                                 <div class="flex-shrink-0">
                                    <i class="ri-file-search-fill text-warning fs-1"></i>
                                 </div>
                                 <div class="flex-grow-1 ms-3">
                                    <h5 class="mb-1 fw-bold">Service Change Approval Queue: Action Pending</h5>
                                    <p class="mb-0 text-muted small">You have 1 new change request awaiting your approval.</p>
                                 </div>
                              </div>
                              <div style="max-height: 600px; overflow-y: auto;">
                                 <div class="d-flex flex-wrap gap-3">
                                    @foreach ($myChangeApprovalList as $approval)
                                       <x-asset-disposal.approval-card class="flex-1"
                                          transaction_number="{{ $approval->change_no }}" 
                                          requester='{{ $approval->proposer->fullname }}' 
                                          division="{{ $approval->proposer->department->name }}" 
                                          url="{{ $approval->redirectUrl }}" 
                                          text="{{ $approval->custom_text }}"
                                          review_as="{{ Auth::user()->employee->fullname }}"
                                          days="{{ $approval->updated_at->diffForHumans() }}" />
                                    @endforeach
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  @endif --}}

                  {{-- @if ($myRelatedTickets->count())
                     <div class="col-6">
                        <div class="card border-start border-info border-4 shadow-sm mt-4">
                           <div class="card-body">
                              <div class="d-flex mb-3">
                                 <div class="flex-shrink-0">
                                    <i class="ri-file-search-fill text-info fs-1"></i>
                                 </div>
                                 <div class="flex-grow-1 ms-3">
                                    <h5 class="mb-1 fw-bold">Related Tickets</h5>
                                    <p class="mb-0 text-muted small">You have {{ $myRelatedTickets->count() }} related tickets.</p>
                                 </div>
                              </div>
                              <div style="overflow-y: auto; max-height: 600px">
                                 <div class="d-flex flex-wrap gap-3">
                                    @foreach ($myRelatedTickets->flatten() as $ticket)
                                       <div class="flex-1">
                                          <x-asset-disposal.approval-card
                                             transaction_number="{{ $ticket->no_ticket }}" 
                                             requester='{{ $ticket->submitter->fullname }}' 
                                             division="{{ $ticket->submitter->department->name }}" 
                                             url="{{ $ticket->redirect_url }}" 
                                             text="{{ $ticket->label }}"
                                             days="{{ $ticket->updated_at->diffForHumans() }}" />
                                       </div>
                                    @endforeach
                                 </div>
                              </div>
                           </div>
                        </div>p
                     </div>
                  @endif --}}
               </div>


               <div class="row pt-4">
                  <div class="col-12">
                     <div class="card">
                        <ul class="nav nav-tabs nav-tabs-custom nav-success" role="tablist">
                           <li class="nav-item">
                              <a class="nav-link py-3 active" data-bs-toggle="tab"
                                 href="#pill-service-desk" role="tab">
                                 <i class="ri-survey-line me-1 align-bottom"></i> Service Desk
                              </a>
                           </li>
                           <li class="nav-item">
                              <a class="nav-link py-3" onclick="if (!historyLoaded) loadData('#historiesTable', 'history')" data-bs-toggle="tab"
                                 href="#pill-histories" role="tab">
                                 <i class="ri-survey-line me-1 align-bottom"></i> Histories
                              </a>
                           </li>
                           <li class="nav-item">
                              <a class="nav-link py-3" data-bs-toggle="tab"
                                 href="#pill-knowledge-base" role="tab">
                                 <i class="ri-survey-line me-1 align-bottom"></i> Knowledge Base
                              </a>
                           </li>
                        </ul>
                        <div class="tab-content">
                           {{-- Service Desk Page --}}
                           <div class="tab-pane active" id="pill-service-desk" role="tabpanel">
                              <div class="card-body">
                                 <a class="btn btn-primary mb-3" href="{{ request()->is('myservice-desk*') ? route('myservice-desk.create') : route('service-desk.create') }}">
                                    Make a New Ticket
                                 </a>
                                 <div class="table-responsive">
                                    <table class="table table-hover align-middle w-100" id="ticketTable">
                                       <thead class="table-light">
                                          <tr>
                                             <th>Ticket ID</th>
                                             <th>Subject</th>
                                             <th>Type</th>
                                             <th>Priority</th>
                                             <th>Status</th>
                                             <th class="text-center">Action</th>
                                          </tr>
                                       </thead>
                                       <tbody>
                                       </tbody>
                                    </table>
                                 </div>
                              </div>
                           </div>
                           <div class="tab-pane" id="pill-histories" role="tabpanel">
                              <div class="card-body">
                                 <table class="table table-hover align-middle w-100" id="historiesTable">
                                    <thead class="table-light">
                                       <tr>
                                          <th>Ticket ID</th>
                                          <th>Subject</th>
                                          <th>Type</th>
                                          <th>Priority</th>
                                          <th>Status</th>
                                          <th class="text-center">Action</th>
                                       </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                 </table>
                              </div>
                           </div>

                           <div class="tab-pane" id="pill-knowledge-base" role="tabpanel">
                              <div class="card-body">
                                 {{-- <div class="row justify-content-center text-center"> --}}
                                    {{-- <div class="col-lg-7">
                                       <h1 class="fw-bold mb-3">Is there anything we can help you with?</h1>
                                       <p class="lead mb-4 opacity-75">Search for knowledge base on your own.</p>
                                    </div> --}}

                                    {{-- <div class="row g-4 mb-5">
                                       @foreach(['Akun & Keamanan', 'Layanan IT', 'Fasilitas Kantor', 'Kebijakan HR'] as $category)
                                          <div class="col-md-3">
                                             <div class="card border-0 shadow-sm text-center p-4 hover-lift transition-all cursor-pointer">
                                                <div class="bg-soft-primary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                                   <i class="fas fa-folder-open text-primary fs-4"></i>
                                                </div>
                                                <h6 class="fw-bold mb-1">{{ $category }}</h6>
                                                <small class="text-muted">12 Artikel</small>
                                             </div>
                                          </div>
                                       @endforeach
                                    </div> --}}

                                    <div class="g-5 justify-content-center">
                                       <div class="list-group list-group-flush rounded-4 gap-3 bg-white p-3">
                                          @if ($knowledgeBases->count())
                                             @foreach($knowledgeBases as $knowledgeBase)
                                                <a href="{{ request()->is('myservice-desk*')? route('myknowledge-base.show', encrypt($knowledgeBase->id)) : route('knowledge-base.show', encrypt($knowledgeBase->id)) }}" class="text-start border-0 py-4 px-3 mb-2 rounded-3 hover-bg-light border-start border-4 border-transparent hover-border-primary transition-all shadow">
                                                   <div class="d-flex">
                                                      {{-- <div class="avatar-circle bg-primary text-white me-3" style="min-width: 3rem;">
                                                         {!! isset($knowledgeBase->author->avatar)? "<img src='" . asset('storage/avatars/' . $knowledgeBase->author->avatar) . "' alt='Avatar' class='img-fluid rounded-circle'>" : substr($knowledgeBase->author->fullname, 0, 1) !!}
                                                      </div> --}}
                                                      <div>
                                                         {{-- <div class="mb-3">
                                                            <p class="mb-1 fw-bold">{{ $knowledgeBase->author->fullname }}</p>
                                                            <div>
                                                               <small class="text-muted">
                                                                  {{ $knowledgeBase->published_at ? "Published on ".$knowledgeBase->published_at->format('d M Y, H:i') : 'Not published yet' }}
                                                                  &bull; <i class="fas fa-{{ $knowledgeBase->level == 'private' ? 'lock' : 'users' }} ms-1"></i> {{ str_replace('_', ' ', ucfirst($knowledgeBase->level)) }}
                                                               </small>
                                                            </div>
                                                         </div> --}}
                                                         <h5 class="fw-bold mb-1 text-dark">{{ $knowledgeBase->title }}</h5>
                                                         <p class="small mb-0" style="line-break: break; white-space: normal">{!! Str::limit(str_replace('&nbsp;', ' ', strip_tags($knowledgeBase->content)), 200) !!}</p>
                                                         <div class="mt-2">
                                                            <span class="badge bg-light text-dark border rounded-pill small fw-normal">
                                                               <i class="ri-arrow-right-s-line"></i> Read More
                                                            </span>
                                                         </div>
                                                      </div>
                                                      <i class="fas fa-chevron-right mt-2"></i>
                                                   </div>
                                                </a>
                                             @endforeach
                                          @else
                                             <div class="text-center py-5">
                                                <i class="fas fa-inbox fs-1 text-muted mb-3"></i>
                                                <p class="text-muted mb-0">No knowledge base found.</p>
                                             </div>
                                          @endif
                                       </div>

                                       <div class="mt-4 d-flex justify-content-center">
                                          {{ $knowledgeBases->links() }}
                                       </div>
                                    </div>

                                    {{-- <div class="col-lg-4">
                                       <div class="card border-0 bg-dark text-white shadow-sm rounded-4 p-4 mt-4">
                                          <h5 class="fw-bold">Masih butuh bantuan?</h5>
                                          <p class="small opacity-75">Jika tidak menemukan jawaban, tim IT Support siap membantu Anda.</p>
                                          <a href="#" class="btn btn-primary w-100 rounded-pill mt-2">Buka Tiket Service</a>
                                       </div>
                                    </div> --}}
                                 {{-- </div> --}}
                              </div>
                           </div>
                           {{-- End Service Desk Page --}}
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <!--end col-->
      </div>
   </div>

@endsection

@section('script')
   <!-- Datatables -->
   <script src="{{ url('') }}/assets/libs/Datatables/DataTables-1.13.1/js/jquery.dataTables.min.js"></script>
   <script src="{{ url('') }}/assets/libs/Datatables/DataTables-1.13.1/js/dataTables.bootstrap5.min.js"></script>
   <script src="{{ url('') }}/assets/libs/Datatables/Responsive-2.4.0/js/dataTables.responsive.min.js"></script>
   <script src="{{ url('') }}/assets/js/pages/datatables.init.js"></script>
   <!-- profile-setting init js -->
   <script src="{{ url('') }}/assets/js/pages/profile-setting.init.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.js"></script>
   <!-- Sweetalert -->
   <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
   <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
   <!-- Toastr Notifications-->
   <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
   <!-- Select2 -->
   <script src="{{ url('') }}/assets/libs/adminlte/select2/js/4.1.0/select2.min.js"></script>
@endsection

@section('javascript')
   <script>
      let historyLoaded = false;
      const priorityColorMap = @json($priorityColorMap);

      const loadData = function(tableElement, filter, onlyMyTicket = false, related = false) {
         $(tableElement).DataTable({
            stateSave: false,
            processing: true,
            responsive: false,
            scrollX: true,
            serverSide: false,
            ajax: {
               url: "{{ route(request()->is('myservice-desk') ? 'myservice-ticket.data' : 'service-ticket.data') }}?filter=" + filter + (onlyMyTicket ? "&my=true" : "") + (related ? "&related=true" : ""),
               dataSrc: 'data'
            },
            columns: [
               {
                  data: 'no_ticket',
                  render: function(data) {
                     return `<span class="fw-bold" style="white-space: nowrap;">#${data}</span>`;
                  }
               },
               {
                  data: 'subject'
               },
               {
                  data: 'type',
                  render: function(data) {
                     let color = 'bg-secondary';
                     if (data === 'incident') color = 'bg-danger';
                     else if (data === 'request') color = 'bg-info';
                     else if (data === 'change') color = 'bg-warning text-dark';
                     else if (data === 'it_initiative') color = 'bg-success';

                     return `<span class="badge ${color} text-capitalize">${data?? "Unassigned"}</span>`;
                  }
               },
               {
                  data: 'total_score',
                  defaultContent: 'Unassigned',
                  type: 'num',
                  render: function(data, type, row) {
                     let color = null;
                     priority = 'Unassigned';
                     Object.entries(priorityColorMap).forEach(function([key, value]) {
                        if (data >= value.min_score && data <= value.max_score) {
                           color = value.color;
                           priority = key;
                        }
                     });

                     if (type == "sort") {
                        return parseInt(data);
                     }

                     return `<span class="badge text-capitalize" style="background-color: ${color || '#3577f1'};">${data >= 99999999 ? 'N/A' : data} (${priority})</span>`;
                  }
               },
               {
                  data: 'current_status',
                  render: function(data) {
                     let color = 'bg-secondary';
                     let status = data ? data.toLowerCase() : '';
                     
                     if (status === 'closed') color = 'bg-success';
                     else if (status === 'open') color = 'bg-warning text-dark';
                     else if (status === 'process') color = 'bg-primary';
                     else if (status === 'hold') color = 'bg-dark';
                     
                     return `<span class="badge ${color} text-uppercase">${status}</span>`;
                  }
               },
               {
                  data: null,
                  className: 'text-center',
                  render: function(data, type, row) {
                     let viewUrl; 
                     let text;
                     if(row.role == 'supervisor' || row.role == 'dept_head' || row.role == 'service_change' || row.role == 'cc') {
                        viewUrl = row.history_action.redirect_url;
                        text = row.history_action.label;
                     } else {
                        viewUrl = `/{{ request()->is('myservice-desk')? 'myservice-desk' : 'service-desk' }}/${row.encrypted_id}/{{ encrypt('user') }}`;
                        text = 'View';
                     }
                     return `
                        <a href="${viewUrl}" target="${row.history_action?.target?? ''}" class="btn btn-sm btn-soft-primary waves-effect waves-light">
                           <i class="ri-eye-fill"></i> ${text}
                        </a>`;
                  }
               }
            ],
            order: [[0, 'desc']],
         });

         if (filter === 'history') {
            historyLoaded = true;
         }
      }

      $(document).ready(function() {
         loadData('#ticketTable', 'open', true, true);
      });
   </script>
@endsection