@extends(request()->routeIs('myknowledge-base.show')? 'layouts.master' : (request()->has('preview')? 'layouts.master' : 'layouts.general')
)

@section('link')
   <meta name="csrf-token" content="{{ csrf_token() }}">
   <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

   <style>
      .kb-knowledge-base-content {
         font-size: 1rem;
         color: #2d3436;
      }
      /* Memastikan styling dari CKEditor (seperti list & table) tetap muncul */
      .kb-knowledge-base-content ul, .kb-knowledge-base-content ol { padding-left: 1.5rem; margin-bottom: 1.5rem; }
      .kb-knowledge-base-content img { max-width: 100%; height: auto; border-radius: 12px; margin: 2rem 0; }
      .kb-knowledge-base-content table { width: 100%; border-collapse: collapse; margin-bottom: 1.5rem; }
      .kb-knowledge-base-content table td, .kb-knowledge-base-content table th { border: 1px solid #dee2e6; padding: 12px; }

      .avatar-circle {
         width: 45px; height: 45px; border-radius: 50%;
         display: flex; align-items: center; justify-content: center; font-weight: bold;
      }
      .hover-bg-light:hover { background-color: #f8f9fa; cursor: pointer; }
      .transition-all { transition: 0.2s ease; }
      
      @media print {
         .breadcrumb, .alert, .btn, .breadcrumb-item::before { display: none !important; }
         .container { width: 100% !important; max-width: none !important; }
      }
   </style>
@endsection

@section('content') 
   @if (request()->routeIs('knowledge-base.show') && request()->has('preview'))
      <div class="row">
         <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
               <h4 class="mb-sm-0">Knowledge Base</h4>

               <div class="page-title-right">
                  <ol class="breadcrumb m-0">
                     <li class="breadcrumb-item">Knowledge Base</a></li>
                     <li class="breadcrumb-item active">Show</li>
                  </ol>
               </div>

            </div>
         </div>
      </div>
   @endif
   <div class="container-fluid">
      
      @if(request()->routeIs('myknowledge-base.show'))
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

      

      <div class="row justify-content-center py-4">
         <div class="col-lg-9">
            <div class="py-5 px-3 card">
               <div class="card-body">
                  <nav aria-label="breadcrumb" class="mb-4">
                     <ol class="breadcrumb">
                     <li class="breadcrumb-item"><a href="{{ $knowledgeBase->status == 'draft'? route('knowledge-base.index') : route('knowledge-base.show', ['id' => encrypt($knowledgeBase->id)]) }}" class="text-decoration-none">Knowledge Base</a></li>
                        <li class="breadcrumb-item active text-truncate" style="max-width: 200px;">{{ $knowledgeBase->title }}</li>
                     </ol>
                  </nav>

                  @if(request()->has('preview'))
                     <div class="alert {{ $knowledgeBase->status == 'draft'? 'alert-warning' : 'alert-success' }} border-0 shadow-sm d-flex justify-content-between align-items-center mb-5" id="status-alert" data-status="{{ $knowledgeBase->status }}">
                        <span><i class="fas fa-eye me-2"></i> <strong>Preview Mode:</strong> <span id="status-text">{{ $knowledgeBase->status == 'draft'? 'Knowledge base is in draft status.' : 'Knowledge base is published.' }}</span></span>
                        <button id="status-btn" class="btn {{ $knowledgeBase->status == 'draft'? 'btn-success' : 'btn-warning' }}">
                           {{ $knowledgeBase->status == 'draft' ? 'Publish' : 'Draft' }}
                        </button>
                     </div>
                  @endif

                  {{-- <style>
                     * {
                        border: 1px solid red !important;
                     }
                  </style> --}}

                  <header class="mb-5">
                     <h3 class="fw-bold text-dark mb-3">{{ $knowledgeBase->title }}</h3>
                     
                     <div class="d-flex align-items-center justify-content-between border-bottom pb-4">
                        <div class="d-flex align-items-center">
                           <div class="avatar-circle bg-primary text-white me-3">
                              {!! isset($knowledgeBase->author->avatar)? "<img src='" . asset('storage/avatars/' . $knowledgeBase->author->avatar) . "' alt='Avatar' class='img-fluid rounded-circle'>" : substr($knowledgeBase->author->fullname, 0, 1) !!}
                           </div>
                           <div>
                              <p class="mb-1 fw-bold">{{ $knowledgeBase->author->fullname }}</p>
                              <div>
                                 <small class="text-muted">
                                    {{ $knowledgeBase->published_at ? "Published on ".$knowledgeBase->published_at->format('d M Y, H:i') : 'Not published yet' }}
                                    &bull; <i class="fas fa-{{ $knowledgeBase->level == 'private' ? 'lock' : 'users' }} ms-1"></i> {{ str_replace('_', ' ', ucfirst($knowledgeBase->level)) }}
                                 </small>
                              </div>
                           </div>
                        </div>
                        
                        {{-- <div class="action-buttons">
                           <button onclick="window.print()" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                              <i class="fas fa-print me-1"></i> Print
                           </button>
                        </div> --}}
                     </div>
                  </header>

                  <knowledgeBase class="kb-knowledge-base-content lh-lg text-dark">
                     {!! $knowledgeBase->content !!}
                  </knowledgeBase>

                  @if($knowledgeBase->attachments && $knowledgeBase->attachments->count() > 0)
                     <div class="mt-5 pt-5 border-top">
                        <h5 class="fw-bold mb-4">Attachments & Resources</h5>
                        <div class="row g-3">
                           @foreach($knowledgeBase->attachments as $file)
                              <div class="col-md-6">
                                 <div class="card border shadow-none rounded-3 p-3 h-100 hover-bg-light transition-all">
                                    <div class="d-flex align-items-center">
                                       <div class="file-icon me-3">
                                          @if($file->type == 'image')
                                             <i class="fas fa-image fa-2x text-info"></i>
                                          @else
                                             <i class="fas fa-file-pdf fa-2x text-danger"></i>
                                          @endif
                                       </div>
                                       <div class="overflow-hidden">
                                          <p class="mb-0 text-truncate fw-bold">{{ $file->filename }}</p>
                                          <small class="text-muted text-uppercase">{{ $file->type }}</small>
                                       </div>
                                       <a href="{{ asset('storage/'.$file->path) }}" target="_blank" class="ms-auto btn btn-sm btn-link text-primary">
                                          <i class="fas fa-download"></i>
                                       </a>
                                    </div>
                                 </div>
                              </div>
                           @endforeach
                        </div>
                     </div>
                  @endif
               </div>
            </div>   
         </div>
      </div>
   </div>
@endsection

@section('script')
   <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
   
   <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
@endsection

@section('javascript')
   <script>
      // -------------- Toggle Publish/Draft Status -- ------------
      $('#status-btn').on('click', function() {
         let currentStatus = $('#status-alert').data('status');
         let newStatus = currentStatus === 'draft' ? 'published' : 'draft';
         const knowledgeBaseId = "{{ encrypt($knowledgeBase->id) }}";

         Swal.fire({
            title: `Are you sure you want to ${newStatus === 'published' ? 'publish' : 'unpublish'} this article?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: `Yes, ${newStatus === 'published' ? 'Publish' : 'Unpublish'} it!`,
            cancelButtonText: 'Cancel',
            preConfirm: () => {
               return $.ajax({
                  url: `{{ route('knowledge-base.update-status', ['id' => encrypt($knowledgeBase->id)]) }}`,
                  method: 'POST',
                  data: { status: newStatus },
                  headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
               }).then(response => {
                  console.log(response);
                  
                  if (response.status == 'success') {
                     toastr.success("Knowledge Base is " + (newStatus === 'published' ? 'published' : 'moved to draft') + ".");
                     // Update button text and style
                     $('#status-btn').text(newStatus === 'published' ? 'Draft' : 'Publish')
                                    .toggleClass('btn-success btn-warning');

                     $('#status-alert').removeClass('alert-warning alert-success')
                                       .addClass(newStatus === 'published' ? 'alert-success' : 'alert-warning');
                     $('#status-text').text(newStatus === 'published' ? 'Knowledge Base is published.' : 'Knowledge Base is in draft status.');

                     $('#status-alert').data('status', response.data.status)
                  } else {
                     toastr.error(response.message || 'An error occurred while updating status.');
                  }
               }).catch(error => {
                  Swal.showValidationMessage(error.message);
               });
            }
         })

      });
   </script>
@endsection