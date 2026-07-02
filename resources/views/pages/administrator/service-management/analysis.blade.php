@extends('layouts.master')

@section('link')
   <meta name="csrf-token" content="{{ csrf_token() }}">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css"/>
   <link rel="stylesheet" href="{{ url('') }}/assets/libs/adminlte/select2/css/4.1.0/select2.min.css">
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
   <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endsection

@section('content') 
   <div class="row">
      <div class="col-12">
         <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">IT Service Desk</h4>
            <div class="page-title-right">
               <ol class="breadcrumb m-0">
                  <li class="breadcrumb-item"><a href="javascript: void(0);">IT Service Desk</a></li>
                  <li class="breadcrumb-item active">Analysis & Triage</li>
               </ol>
            </div>
         </div>
      </div>
   </div>

   <div class="container-fluid py-4">
      <div class="row justify-content-center">
         <div class="col-lg-10">
               <div class="d-flex align-items-center justify-content-between mb-4">
                  <div>
                     <h3 class="fw-bold mb-0">Ticket Analysis & Triage</h3>
                     <p class="text-muted">Validate identity and mapping for Ticket <span class="text-primary">#{{ $ticket->no_ticket }}</span></p>
                  </div>
                  <span class="badge {{ $ticket->self_report ? 'bg-soft-warning text-warning' : 'bg-soft-success text-success' }} px-3 py-2 fs-6">
                     {{ $ticket->self_report ? 'SELF REPORT' : 'REPORT FOR OTHER' }}
                  </span>
               </div>

               <div class="row row-cols-1 row-cols-md-2">
                  <div class="col-md-5">
                     <div class="card shadow-sm border-0 mb-4 h-100">
                        <div class="card-header bg-white fw-bold py-3">Original Description</div>
                        <div class="card-body">
                           <div class="mb-3">
                              <label class="small text-muted d-block">Reporter</label>
                              <h6 class="fw-bold">{{ $ticket->submitter->fullname }} (NIK: {{ $ticket->submitter->nik }})</h6>
                           </div>
                           <div class="mb-3">
                              <label class="small text-muted d-block">Reported Subject</label>
                              <p class="fw-bold text-primary">{{ $ticket->subject }}</p>
                           </div>
                           <div class="mb-3">
                              <label class="small text-muted d-block">User Description</label>
                              <div class="bg-light p-3 rounded border fst-italic">
                                 {!! $ticket->description !!}
                              </div>
                           </div>

                           <div>
                              <label class="small text-muted d-block">User Description</label>
                              <div class="row row-cols-2">
                                 @foreach ($ticket->messages->first()->media as $media)
                                    <div class="col col-6 p-1">
                                       <img src="{{ asset('storage/'.$media->path) }}" alt="Ticket Media" class="img-fluid rounded">
                                    </div>
                                 @endforeach
                              </div>
                           </div>
                           <hr>
                           <div class="small text-muted">
                              <i class="ri-time-line"></i> Submitted {{ $ticket->created_at->diffForHumans() }}
                           </div>
                        </div>
                     </div>
                  </div>

                  {{-- SISI KANAN: FORM TRIAGE --}}
                  <div class="col-md-7">
                     <div class="card h-100 shadow-sm border-0">
                        <form id="triageForm" action="{{ route('service-ticket.verify', encrypt($ticket->id)) }}" method="POST" class="d-flex flex-column justify-content-between h-100" enctype="multipart/form-data">
                           <div class="card-body h-100">
                              @csrf
                              <div id="step1">
                                 <input type="hidden" name="report_for" value="{{ $ticket->report_for }}">
                                 {{-- Identifikasi PIC --}}
                                 <div class="mb-3">
                                    <label class="fw-bold small text-uppercase">CC (Carbon Copy)<span class="text-danger">*</span></label>
                                    <select name="ccs[]" id="verifyCC" class="form-control select2" 
                                          data-placeholder="Select CC..." required multiple>
                                       <option value=""></option>
                                       @foreach($employees as $emp)
                                          <option value="{{ encrypt($emp->id) }}" {{ $ticket->ccs->firstWhere('employee_id', $emp->id) ? 'selected' : '' }}>
                                             {{ $emp->nik }} - {{ $emp->fullname }}
                                          </option>
                                       @endforeach
                                    </select>
                                 </div>

                                 <div class="mb-3">
                                    <label class="fw-bold small text-uppercase">Categories<span class="text-danger">*</span></label>
                                    <select name="category" id="category" class="form-control select2" 
                                          data-placeholder="Select Category..." required>
                                       <option value=""></option>
                                       @foreach($categories as $category => $catalogs)
                                          <option value="{{ $category }}">
                                             {{ ucfirst(str_replace('_', ' ', $category)) }}
                                          </option>
                                       @endforeach
                                    </select>
                                 </div>

                                 <div class="mb-3">
                                    <label class="fw-bold small text-uppercase">Catalogs<span class="text-danger">*</span></label>
                                    <select name="catalog" id="catalog" class="form-control select2" 
                                          data-placeholder="Select Catalog..." required>
                                       <option value=""></option>
                                    </select>
                                 </div>

                                 {{-- Identifikasi Asset --}}
                                 {{-- <div class="mb-3">
                                    <label class="fw-bold small text-uppercase">Identify Asset</label>
                                    <select name="it_asset_id" id="verifyAsset" class="form-control select2" 
                                          data-placeholder="Identify IT Assets">
                                          @if ($ticket->it_asset_id)
                                             <option value="{{ encrypt($ticket->it_asset_id) }}" selected>{{ $ticket->itAsset->asset_code }} - {{ $ticket->itAsset->brand }}</option>
                                          @endif
                                    </select>
                                 </div> --}}

                                 <div class="mb-3">
                                    <label class="fw-bold small text-uppercase">Type<span class="text-danger">*</span></label>
                                    <select name="type" class="form-control select2">
                                       @foreach ($types as $type)
                                          <option value="{{ $type }}" class="text-capitalize" required>{{ ucfirst($type) }}</option>
                                       @endforeach
                                    </select>
                                 </div>

                                 <div class="row g-2 mb-3">
                                    <div class="col-md-6">
                                       <label class="small fw-bold text-uppercase">Update Status<span class="text-danger">*</span></label>
                                       <select name="status" class="form-select select2" required>
                                          <option value="process" selected>PROCESS</option>
                                          <option value="hold">HOLD</option>
                                       </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                       <label class="small fw-bold text-uppercase">Priority Level<span class="text-danger">*</span></label>
                                       <select name="priority" class="form-select select2" required>
                                          @foreach ($priorities as $key => $priority)
                                             <option value="{{ $key }}" {{ $ticket->priority == $key ? 'selected' : '' }}>{{ $priority }}</option>
                                          @endforeach
                                       </select>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                       <label class="small fw-bold text-uppercase">Move to Change Management</label>
                                       <div class="form-check form-switch mt-1">
                                          <input class="form-check-input" type="checkbox" id="changeToggle" name="is_replacement">
                                          <label class="form-check-label small text-danger">Swap Asset with Replacement?</label>
                                       </div>
                                    </div>
                                 </div>

                                 {{-- Box Replacement (Hidden by Default) --}}
                                 <div id="replacementBox" style="display:none;" class="p-3 bg-light rounded border border-info mb-3 shadow-sm">
                                    <div id="changeManagement" class="hidden">
                                       <h5 class="modal-title mb-3">Propose Change Management</h5>
                                       <form id="cmForm">
                                          <div class="modal-body">
                                             <div class="mb-3">
                                                <label class="form-label">Change Type</label>
                                                <select class="form-select" name="change_type">
                                                   <option value="Standard">Standard</option>
                                                   <option value="Normal">Normal</option>
                                                   <option value="Emergency">Emergency</option>
                                                </select>
                                             </div>
                                             <div class="mb-3">
                                                <label class="form-label">Subject</label>
                                                <input type="text" class="form-control" name="subject" value="{{ $ticket->subject }}">
                                             </div>
                                             <div class="mb-3">
                                                <label class="form-label">Description</label>
                                                <textarea class="form-control" name="description" rows="3">{{ $ticket->description }}</textarea>
                                             </div>
                                          </div>
                                       </form>
                                    </div>
                                 </div>
                              </div>

                              <div id="step2" style="display: none">
                              {{-- <div id="step2"> --}}
                                 <div id="pic-container">
                                    <div class="pic-row mb-4" data-index="1">
                                       <div class="d-flex justify-content-between align-items-center">
                                          <label class="fw-bold small text-uppercase text-primary">User 1</label>
                                          {{-- Tombol hapus disembunyikan untuk baris pertama --}}
                                       </div>
                                       <div class="ms-2 ps-4 pb-2 border-start border-primary">
                                          <div class="row mb-3 row-cols-lg-2">
                                             <div>
                                                <label class="fw-bold small text-uppercase text-muted">Direct Supervisor<span class="text-danger">*</span></label>
                                                <select name="users[0][supervisor]" id="supervisor-1" class="form-control select2 user-select" data-placeholder="Search Supervisor..." required>
                                                      <option value=""></option>
                                                      @foreach($employees as $emp)
                                                         <option value="{{ encrypt($emp->id) }}" {{ ($ticket->pic_id == $emp->id) ? 'selected' : '' }}>
                                                            {{ $emp->fullname }} - {{ $emp->department->name."({$emp->position->nama})" }}
                                                         </option>
                                                      @endforeach
                                                </select>
                                             </div>
                                             <div>
                                                <label class="fw-bold small text-uppercase text-muted">Dept Head<span class="text-danger">*</span></label>
                                                <select name="users[0][dept_head]" id="depthead-1" class="form-control select2 user-select" data-placeholder="Search Department Head..." required>
                                                      <option value=""></option>
                                                      @foreach($employees as $emp)
                                                         <option value="{{ encrypt($emp->id) }}" {{ ($ticket->pic_id == $emp->id) ? 'selected' : '' }}>
                                                            {{ $emp->fullname }} - {{ $emp->department->name."({$emp->position->nama})" }}
                                                         </option>
                                                      @endforeach
                                                </select>
                                             </div>
                                          </div>
                                          <div class="mb-3">
                                             <label class="fw-bold small text-uppercase text-muted">User<span class="text-danger">*</span></label>
                                             <select name="users[0][user]" id="user-1" class="form-control select2 user-select" data-placeholder="Search Employee..." required>
                                                   <option value=""></option>
                                                   @foreach($employees as $emp)
                                                      <option value="{{ encrypt($emp->id) }}" {{ ($ticket->pic_id == $emp->id) ? 'selected' : '' }}>
                                                         {{ $emp->fullname }} - {{ $emp->department->name."({$emp->position->nama})" }}
                                                      </option>
                                                   @endforeach
                                             </select>
                                          </div>

                                          <div class="mb-2">
                                             <label class="fw-bold small text-uppercase text-muted">IT Asset</label>
                                             <select name="users[0][asset]" id="asset-1" class="form-control select2 asset-select" data-placeholder="Select Asset...">
                                                   <option value=""></option>
                                                   {{-- Data Asset akan diisi via AJAX atau manual --}}
                                             </select>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                                 
                                 <div class="px-3 text-center mt-3">
                                    <button type="button" id="add-pic-btn" class="btn btn-outline-secondary w-50 shadow-sm">
                                          <i class="ri-user-add-line"></i> Add User +
                                    </button>
                                 </div>
                              </div>

                              <div id="step3">
                                 <div class="mt-3">
                                    <label>IT Message <span class="text-danger">*</span></label><br>
                                    {{-- <textarea name="description" rows="5" class="form-control" minlength="5">{{ old('description') }}</textarea> --}}
                                    <textarea name="it_message" id="it_message">{{ old('it_message') }}</textarea>
                                    @error('it_message') <small class="text-danger">{{ $message }}</small> @enderror
                                 </div>

                                 <div class="mt-3">
                                    <label>Attachments</label><span>(optional)</span> <br>
                                    <input type="file" name="attachments[]" 
                                       onchange="validateSize(this);" 
                                       accept=".jpg,.jpeg,.png,.pdf" multiple class="form-control">
                                    @error('message') <small class="text-danger">{{ $message }}</small> @enderror
                                 </div>
                              </div>

                              <div class="card-footer bg-transparent border-0 d-flex gap-2">
                                 <button type="button" id="back-btn" class="btn btn-light flex-fill d-none">
                                    <i class="ri-arrow-left-line"></i> Back
                                 </button>
                                 <button type="button" id="next-btn" class="btn btn-primary flex-fill">
                                    Next <i class="ri-arrow-right-line"></i>
                                 </button>
                                 <button id="submit-btn" type="submit" class="btn btn-success flex-fill shadow d-none">
                                    <i class="ri-check-double-line"></i> Verify & Open
                                 </button>
                              </div>
                           </div>
                        </form>
                     </div>
                  </div>
               </div>
         </div>
      </div>
   </div>
@endsection

@section('script')
   <!-- CKEditor 4-->  
   <script src="/assets/ckeditor/jquery.min.js"></script>
   <script src="/assets/ckeditor/ckeditor.js"></script>
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
      function validateSize(input) {
         if (input.files) {
            let isValid = true;
            // Loop setiap file untuk cek ukuran
            Array.from(input.files).forEach(file => {
               const fileSize = file.size / 1024 / 1024; // MiB
               if (fileSize > 5) {
                  isValid = false;
                  toastr.error('File "' + file.name + '" exceeds 5MB limit.');
               }
            });
         }
      }

      const serviceCategories = @json($categories);
      $('#category').change(function(e) {
         const val = $(this).val();
         $("#catalog").empty()
         serviceCategories[val].forEach(catalog => {
            $('#catalog').append(new Option(catalog.toUpperCase(), catalog));
         });
         $('#catalog').append(new Option('Lain-lain', 'lain-lain'));
      })



      $(document).ready(function() {
         // end form ui handler
         let currentStep = 1;
         const totalSteps = 3;

         // Sembunyikan semua step kecuali step 1 di awal
         $('#step2, #step3').hide();

         // Fungsi Navigasi Utama
         function updateStepper(direction) {
            // Validasi jika arahnya ke depan (Next)
            if (direction === 'next') {
                  let isValid = true;
                  $(`#step${currentStep} [required]`).each(function() {
                     if ($(this).val() === "" || $(this).val() === null) {
                        isValid = false;
                        $(this).addClass('is-invalid');
                        toastr.info(`Please complete all required fields in Step ${currentStep}`);
                        return false;
                     } else {
                        $(this).removeClass('is-invalid');
                     }
                  });

                  if (!isValid) return; // Stop jika tidak valid
            }

            // Transisi antar Step
            $(`#step${currentStep}`).fadeOut(200, function() {
                  if (direction === 'next') {
                     currentStep++;
                  } else {
                     currentStep--;
                  }

                  $(`#step${currentStep}`).fadeIn(200);
                  updateButtons();
            });
         }

         // Fungsi Mengatur Visibilitas Tombol
         function updateButtons() {
            // Logika Tombol Back (Sembunyi di Step 1)
            if (currentStep === 1) {
                  $('#back-btn').addClass('d-none');
            } else {
                  $('#back-btn').removeClass('d-none');
            }

            // Logika Tombol Next vs Submit
            if (currentStep === totalSteps) {
                  $('#next-btn').addClass('d-none');
                  $('#submit-btn').removeClass('d-none');
            } else {
                  $('#next-btn').removeClass('d-none');
                  $('#submit-btn').addClass('d-none');
            }
            
            // Opsional: Update Progress Bar/Indicator jika ada
            console.log("Current Step: " + currentStep);
         }

         // Event Listeners
         $('#next-btn').on('click', function() {
            updateStepper('next');
         });

         $('#back-btn').on('click', function() {
            updateStepper('back');
         });

         // Toggle Change Management Box
         $('#changeToggle').on('change', function() {
            if($(this).is(':checked')) {
               $('#replacementBox').slideDown();
            } else {
               $('#replacementBox').slideUp();
            }
         });


         // -------- handle step 2 user choices ----------
         let userCount = 1;

         function initSelect2(element) {
            element.select2({
               // theme: 'bootstrap-5', // Sesuaikan dengan tema Anda
               allowClear: true,
            });
         }

         function initAssetAjax(userId, assetId) {
            userId.on('change', function() {
               const picId = $(this).val();
               
               // Reinitialize assetSelect with AJAX
               assetId.select2({
                  allowClear: true,
                  ajax: {
                     url: `/administrator/service-desk/employee/${picId}/assets`,
                     dataType: 'json',
                     processResults: function(data) {
                        return {
                           results: data.map(a => ({
                              id: a.encrypted_id,
                              text: `${a.asset_code} - ${a.brand}`
                           }))
                        };
                     },
                  },
                  placeholder: '-- Choose Asset --'
               });
            }).trigger('change'); 
         }

         // Inisialisasi awal untuk baris pertama
         initSelect2($('.select2'));
         initAssetAjax($('#user-1'), $('#asset-1'))

         // Event Klik Tombol Add +
         $('#add-pic-btn').on('click', function() {
            userCount++;
            
            // Template baris baru
            let newRow = `
            <div class="pic-row mb-4" data-index="${userCount}" style="display:none;">
               <div class="d-flex justify-content-between align-items-center">
                  <label class="fw-bold small text-uppercase text-primary">User ${userCount}</label>
                  <button type="button" class="btn btn-link btn-sm remove-pic-btn" style="color: red">
                     <i class="ri-delete-bin-line"></i> Remove
                  </button>
               </div>
               <div class="ms-2 ps-4 pb-2 border-start border-primary">
                  <div class="row mb-3 row-cols-lg-2">
                     <div>
                        <label class="fw-bold small text-uppercase text-muted">Direct Supervisor<span class="text-danger">*</span></label>
                        <select name="users[${userCount}][supervisor]" id="supervisor-${userCount}" class="form-control select2 user-select" data-placeholder="Search Supervisor..." required>
                              <option value=""></option>
                              @foreach($employees as $emp)
                                 <option value="{{ encrypt($emp->id) }}" {{ ($ticket->pic_id == $emp->id) ? 'selected' : '' }}>
                                    {{ $emp->fullname }} - {{ $emp->department->name."({$emp->position->nama})" }}
                                 </option>
                              @endforeach
                        </select>
                     </div>
                     <div>
                        <label class="fw-bold small text-uppercase text-muted">Dept Head<span class="text-danger">*</span></label>
                        <select name="users[${userCount}][dept_head]" id="depthead-${userCount}" class="form-control select2 user-select" data-placeholder="Search Department Head..." required>
                              <option value=""></option>
                              @foreach($employees as $emp)
                                 <option value="{{ encrypt($emp->id) }}" {{ ($ticket->pic_id == $emp->id) ? 'selected' : '' }}>
                                    {{ $emp->fullname }} - {{ $emp->department->name."({$emp->position->nama})" }}
                                 </option>
                              @endforeach
                        </select>
                     </div>
                  </div>
                  <div class="mb-3">
                     <label class="fw-bold small text-uppercase text-muted">User<span class="text-danger">*</span></label>
                     <select name="users[${userCount}][user]" id="user-${userCount}" class="select2" data-placeholder="Search Employee..." required>
                        <option value=""></option>
                        @foreach($employees as $emp)
                           <option value="{{ encrypt($emp->id) }}">{{ $emp->fullname }} - {{ $emp->department->name."({$emp->position->nama})" }}</option>
                        @endforeach
                     </select>
                  </div>
                  <div class="mb-2">
                     <label class="fw-bold small text-uppercase text-muted">IT Asset</label>
                     <select name="users[${userCount}][asset]" id="asset-${userCount}" class="select2" data-placeholder="Select Asset...">
                        <option value=""></option>
                     </select>
                  </div>
               </div>
            </div>`;

            // Append ke kontainer
            $('#pic-container').append(newRow);
            
            // Animasi muncul dan inisialisasi Select2 baru
            let $newRowElement = $(`.pic-row[data-index="${userCount}"]`);
            $newRowElement.fadeIn(300);
            initSelect2($newRowElement.find('.select2'));
            initAssetAjax($newRowElement.find(`#user-${userCount}`), $newRowElement.find(`#asset-${userCount}`))
         });

         // Event Klik Tombol Remove
         $(document).on('click', '.remove-pic-btn', function() {
            $(this).closest('.pic-row').fadeOut(300, function() {
               $(this).remove();
            });
         });
      });
   </script>

   <script>
      $(document).ready(function() {
         // const picSelect = $('#verifyPic');
         // const assetSelect = $('#verifyAsset');

         // $(document).on('click', function(e) {
         //    console.log(this, e.target);
            
         // })

         // picSelect.on('change', function() {
         //    const picId = $(this).val();
            
         //    // Reinitialize assetSelect with AJAX
         //    assetSelect.select2({
         //       ajax: {
         //          url: `/administrator/service-desk/employee/${picId}/assets`,
         //          dataType: 'json',
         //          processResults: function(data) {
         //             return {
         //                results: data.map(a => ({
         //                   id: a.encrypted_id,
         //                   text: `${a.asset_code} - ${a.brand}`
         //                }))
         //             };
         //          },
         //       },
         //       placeholder: '-- Choose Asset --'
         //    });
         // }).trigger('change'); // Trigger on page load to catch existing data

         
         $('#changeToggle').on('change', function() {
            $('#replacementBox').toggle(this.checked);
            if(this.checked) {
               // Load available inventory only when needed
               $('#replacementAsset').select2({
                  ajax: { url: '/api/assets/available', dataType: 'json' }
               });
            }
         });
      });
   </script>

   <script>
      $(document).ready(function() {
         CKEDITOR.replace( 'it_message', { 
            // toolbar :[['Undo','Redo','RemoveFormat'],['Bold', 'Italic', '-', 'NumberedList', 'BulletedList']]
            toolbarGroups: [{
               "name": "basicstyles",
               "groups": ["basicstyles"]
               },
               {
               "name": "links",
               "groups": ["links"]
               },
               {
               "name": "paragraph",
               "groups": ["list", "blocks"]
               },
               // {
               //   "name": "document",
               //   "groups": ["mode"]
               // },
               {
               "name": "insert",
               "groups": ["insert"]
               },
               {
               "name": "styles",
               "groups": ["styles"]
               }
               // ,
               // {
               //   "name": "about",
               //   "groups": ["about"]
               // }
            ],
            //   removeButtons: 'Underline,Strike,Subscript,Superscript,Anchor,Styles,Specialchar,PasteFromWord'
         });

         
         $('#triageForm').on('submit', function(e) {
            e.preventDefault();

            for (instance in CKEDITOR.instances) {
               CKEDITOR.instances[instance].updateElement();
            }

            let form = $(this);
            let url = form.attr('action');
            let formData = new FormData(this);

            function clearErrors() {
               $('.is-invalid').removeClass('is-invalid');
               $('.select2-container').removeClass('is-invalid');
               $('.text-danger').remove();
            }

            Swal.fire({
               title: 'Konfirmasi Verifikasi',
               text: "Pastikan PIC dan Aset sudah sesuai. Lanjutkan ke Workspace?",
               icon: 'question',
               showCancelButton: true,
               confirmButtonColor: '#3085d6',
               cancelButtonColor: '#d33',
               confirmButtonText: 'Ya, Verifikasi!',
               showLoaderOnConfirm: true,
               preConfirm: () => {
                  clearErrors();
                  // Update CKEditor sebelum kirim jika ada
                  if (typeof CKEDITOR !== 'undefined') {
                     for (instance in CKEDITOR.instances) CKEDITOR.instances[instance].updateElement();
                  }

                  return $.ajax({
                     url: url,
                     type: 'POST',
                     data: formData,
                     dataType: 'json',
                     contentType: false,
                     processData: false,
                     headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                     }
                  }).then(
                     // SUCCESS HANDLER (Ganti .then)
                     (response) => {
                        return response;
                     },
                     // ERROR HANDLER (Ganti .catch)
                     (jqXHR) => {
                        if (jqXHR.status === 422) {
                           const errors = jqXHR.responseJSON.errors;
                           $.each(errors, function(key, value) {
                              // Handle array inputs (users.0.user -> users[0][user])
                              let nameAttr = key.split('.').map((s, i) => i > 0 ? `[${s}]` : s).join('');
                              let input = $(`[name="${key}"], [name="${nameAttr}"]`);
                              
                              if (input.hasClass('select2-hidden-accessible')) {
                                 input.next('.select2-container').addClass('is-invalid');
                                 input.closest('div').append('<small class="text-danger d-block">' + value[0] + '</small>');
                              } else {
                                 input.addClass('is-invalid');
                                 input.after('<small class="text-danger d-block">' + value[0] + '</small>');
                              }
                           });
                           Swal.showValidationMessage('Beberapa field memerlukan perbaikan.');
                        } else {
                           Swal.showValidationMessage(`Error ${jqXHR.status}: ${jqXHR.responseJSON.message || 'Server Error'}`);
                        }
                        return false; // Mencegah modal tertutup karena error
                     }
                  );
               },
               allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
               if (result.isConfirmed && result.value) {
                  Swal.fire({
                     title: 'Berhasil!',
                     text: 'Tiket telah diverifikasi.',
                     icon: 'success',
                     timer: 1500,
                     showConfirmButton: false
                  }).then(() => {
                     window.location.href = "{{ route('service-management.workspace', ['id' => encrypt($ticket->id), 'role' => encrypt('it')]) }}";
                  });
               }
            });
         });
      })
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
   </script>
@endsection
