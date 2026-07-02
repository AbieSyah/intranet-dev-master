@extends('layouts.master')
@section('link')
   <meta name="csrf-token" content="{{ csrf_token() }}">
   <link rel="stylesheet" href="{{  url('') }}/assets/libs/adminlte/select2/css/4.1.0/select2.min.css">
   <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endsection

@section('content') 
   <div class="row">
      <div class="col-12">
         <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Form Knowledge Base</h4>

            <div class="page-title-right">
               <ol class="breadcrumb m-0">
                  <li class="breadcrumb-item"><a href="javascript: void(0);">Knowledge Base</a></li>
                  <li class="breadcrumb-item active">Form</li>
               </ol>
            </div>

         </div>
      </div>
   </div>

   <div class="container-fluid py-4">
      <form action="" id="kb-form">
         @csrf
         {{-- @if(isset($knowledgeBase)) @method('PUT') @endif --}}

         <div class="row">
            <div class="col-lg-8">
               <div class="card border-0 shadow-sm p-4 mb-4">
                  <div class="mb-4">
                     <label class="form-label fw-bold">Title</label>
                     <input type="text" name="title" class="form-control form-control-lg" 
                        placeholder="Enter title here..." value="{{ $knowledgeBase->title ?? '' }}" required>
                  </div>

                  <div class="mb-4">
                     <label class="form-label fw-bold">Content</label>
                     <textarea name="content" id="editor" required class="form-control">{{ $knowledgeBase->content ?? '' }}</textarea>
                  </div>

                  <div class="mb-3">
                     <label class="form-label fw-bold">Attachments(optional)</label>
                     <input type="file" id="tumbnail" name="attachments[]" 
                        onchange="validateSize(this); tumbnailValidation();" 
                        accept=".jpg,.jpeg,.png,.pdf" multiple class="form-control">
                     @error('message') <small class="text-danger">{{ $message }}</small> @enderror
                  </div>
                  <div id="image_tumbnail">
                     {{-- @dd($knowledgeBase->id, $knowledgeBase->media) --}}
                  </div>
                  @if (isset($knowledgeBase) && $knowledgeBase->media->isNotEmpty())
                     <div class="mt-3">
                        <label class="form-label fw-bold">Uploaded Attachments</label>
                        <div class="d-flex gap-2">
                           @foreach ($knowledgeBase->media as $media)
                              <div style="width: 100px; height: 100px" class="position-relative d-flex align-items-center justify-content-center text-center overflow-hidden">
                                 <button type="button" data-media-id="{{ encrypt($media->id) }}" class="delete-attachment-btn btn btn-sm btn-danger position-absolute top-0 end-0" style="z-index: 10">
                                    <i class="ri-delete-bin-line"></i>
                                 </button>
                                 <a href="{{ asset('storage/'.$media->path) }}" target="_blank" class="d-block">
                                    @if ($media->type == 'image')
                                       <img src="{{ asset('storage/'.$media->path) }}" alt="Attachment" class="img-fluid">
                                    @else
                                       {{ $media->name }}</a>
                                    @endif
                                 </a>
                              </div>
                           @endforeach
                        </div>
                     </div>
                  @endif
               </div>
            </div>

            <div class="col-lg-4">
               <div class="card border-0 shadow-sm p-4 mb-4">
                  <h5 class="fw-bold mb-3">Publishing</h5>
                  <hr>

                  <div class="mb-4">
                     <label class="form-label d-block text-muted small fw-bold text-uppercase">Access Level</label>
                     <select name="level" class="form-select border-light bg-light">
                        <option value="private" {{ (isset($knowledgeBase) && $knowledgeBase->level == App\Models\KnowledgeBase::LEVEL_PRIVATE) ? 'selected' : '' }}>Super Admin Only</option>
                        <option value="some_employees" {{ (isset($knowledgeBase) && $knowledgeBase->level == App\Models\KnowledgeBase::LEVEL_SOME_EMPLOYEES) ? 'selected' : '' }}>Specific Employees</option>
                        <option value="all_employees" {{ (isset($knowledgeBase) && $knowledgeBase->level == App\Models\KnowledgeBase::LEVEL_ALL_EMPLOYEES) ? 'selected' : '' }}>All Employees</option>
                     </select>
                  </div>

                  <div class="d-grid gap-2">
                     {{-- <button type="button" href="{{ isset($knowledgeBase)? route('knowledge-base.preview') : '' }}" target="_blank" class="btn btn-outline-secondary border-dashed" id="preview-btn" style="{{ isset($knowledgeBase) ? 'display: block;' : 'display: none;' }}">
                        <i class="fas fa-eye me-2"></i>Preview
                     </button> --}}
                     <div class="d-flex gap-1">
                        <button type="submit" name="action" value="release" class="flex-1 btn btn-secondary btn-label waves-effect waves-light"><i class="ri-global-line label-icon align-middle fs-16 me-2"></i> <span style="white-space: nowrap">Release</span></button>
                        &nbsp;
                        <button type="submit" name="action" value="draft" class="flex-1 btn btn-primary btn-label waves-effect waves-light"><i class="ri-save-line label-icon align-middle fs-16 me-2"></i> <span style="white-space: nowrap">Draft</span></button>
                     </div>
                  </div>
               </div>

               <div class="card" id="specific-employee-container" style="{{ isset($knowledgeBase) && $knowledgeBase->level == App\Models\KnowledgeBase::LEVEL_SOME_EMPLOYEES? '' : 'display: none;' }}">
                  <div class="card-header">
                     <h5 class="fw-bold mb-0">Selected Employees</h5>
                  </div>
                  <div id="selected-employee-list" class="card-body">
                     @if (isset($knowledgeBase) && $knowledgeBase->employees)
                        @foreach ($knowledgeBase->employees as $employee)
                           <div>
                              <input type="hidden" name="selected_employees[]" value="{{ $employees->find($employee->id)->encrypted_id }}" data-name="{{ $employee->fullname }}" data-department="{{ $employee->department?->name?? "N/A" }}" data-position="{{ $employee->position?->nama?? "N/A" }}">
                              <label>{{ $loop->iteration }}. {{ $employee->fullname }} - {{ $employee->position?->nama?? "N/A" }} ({{ $employee->department?->name?? "N/A" }})</label>
                           </div>
                        @endforeach
                     @endif
                  </div>
                  <div class="card-footer">
                     <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#employeeModal">
                        <i class="ri-edit-line me-1"></i> Edit Selection
                     </button>
                  </div>
               </div>

               <div class="card border-0 shadow-sm p-4">
                  <h5 class="fw-bold mb-3">Meta Info</h5>
                  <p class="text-muted small">Created by: <strong>{{ auth()->user()->name }}</strong></p>
                  <p class="text-muted small">Last Modified: <strong>{{ isset($knowledgeBase) ? $knowledgeBase->updated_at->diffForHumans() : 'Now' }}</strong></p>
               </div>
            </div>
         </div>
      </form>
   </div>


   <div class="modal fade" id="employeeModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg shadow-lg">
         <div class="modal-content border-0">
            <div class="modal-header bg-light">
               <h5 class="modal-title">Select Specific Employees</h5>
               <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
               <div class="mb-3">
                  <input type="text" id="search-employee" class="form-control" placeholder="Search name or department...">
               </div>
               <div class="table-responsive" style="max-height: 400px;">
                  <table class="table table-hover">
                     <thead class="table-light sticky-top">
                        <tr>
                           <th width="50">Select</th>
                           <th>Name</th>
                           <th>Position</th>
                           <th>Department</th>
                        </tr>
                     </thead>
                     <tbody id="employee-list">
                        @foreach($employees as $employee)
                        <tr>
                           <td>
                              <input type="checkbox" class="emp-checkbox form-check-input" style="pointer-events: none"
                                    value="{{ $employee->encrypted_id }}" {{ isset($knowledgeBase) && $knowledgeBase->employees->find($employee->id)? 'checked' : '' }} data-name="{{ $employee->fullname }}" data-department="{{ $employee->department?->name?? "N/A" }}" data-position="{{ $employee->position?->nama?? "N/A" }}" data-name="{{ $employee->fullname }}" data-department="{{ $employee->department?->name?? "N/A" }}" data-position="{{ $employee->position?->nama?? "N/A" }}">
                           </td>
                           <td>{{ $employee->fullname }}</td>
                           <td>{{ $employee->position?->nama?? "N/A" }}</td>
                           <td>{{ $employee->department?->name?? "N/A" }}</td>
                        </tr>
                        @endforeach
                     </tbody>
                  </table>
               </div>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
               <button type="button" id="save-selection" class="btn btn-primary px-4">Apply Selection</button>
            </div>
         </div>
      </div>
   </div>
@endsection

@section('script')
   <script src="/assets/libs/adminlte/select2/js/4.1.0/select2.min.js"></script>
   <!-- Sweetalert -->
   <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
   
   <script src="/assets/ckeditor/ckeditor.js"></script>
   
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

            if (!isValid) {
               $('#firstmodal').modal('show');
               $(input).val(''); // reset input
               document.getElementById('image_tumbnail').innerHTML = ''; 
            }
         }
      }

      function tumbnailValidation() {
         var tumbnail = document.getElementById('tumbnail');
         var previewContainer = document.getElementById('image_tumbnail');
         
         // Reset container setiap kali ada perubahan file
         previewContainer.innerHTML = '<div class="row" id="preview-row"></div>';
         var previewRow = document.getElementById('preview-row');

         var allowedExtensions = /(\.jpg|\.jpeg|\.png|\.pdf)$/i;

         if (tumbnail.files) {
            Array.from(tumbnail.files).forEach(file => {
               // Validasi Ekstensi
               if (!allowedExtensions.exec(file.name)) {
                  toastr.error('Invalid file type: ' + file.name);
                  tumbnail.value = '';
                  previewContainer.innerHTML = '';
                  return false;
               }

               // Reader untuk Preview
               var reader = new FileReader();
               reader.onload = function(e) {
                  let content = '';
                  if (file.type === "application/pdf") {
                     content = `
                        <div class="col-md-4 mb-2">
                           <div class="card p-2 border-primary text-center">
                              <i class="ri-file-pdf-fill ri-3x text-danger"></i>
                              <small class="d-block text-truncate">${file.name}</small>
                           </div>
                        </div>`;
                  } else {
                     content = `
                        <div class="col-md-4 mb-2">
                           <div class="card p-1">
                              <img src="${e.target.result}" class="img-fluid rounded" style="height: 100px; object-fit: cover;"/>
                              <small class="d-block text-truncate text-center mt-1">${file.name}</small>
                           </div>
                        </div>`;
                  }
                  previewRow.innerHTML += content;
               };
               reader.readAsDataURL(file);
            });
         }
      }
   </script>


   <script>
      $(document).ready(function() {
         $('.select2').select2()
      });

      // ------------- Initialize Flatpickr (Matching the YYYY-MM-DD format in your table) -------------
      $("#reg-date").flatpickr({
         allowInput: true,
         altInput: false,
         dateFormat: "Y-M-d",
         defaultDate: new Date(),
      });
   </script>

   <script>
      $(document).ready(function() {
         const editor = CKEDITOR.replace('editor', { 
            extraPlugins: 'notification',
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
               {
               "name": "insert",
               "groups": ["insert"]
               },
               {
               "name": "styles",
               "groups": ["styles"]
               }
            ],
         });

         // ------------- Handle form submission -------------
         $('#kb-form').submit(function(e) {
            e.preventDefault();

            editor.updateElement(); 

            if ($('#editor').val() === '') {
               editor.showNotification('Required!', 'warning');
               return false;
            }

            // Get form data
            let formData = new FormData(this);

            formData.set('action', $(e.originalEvent.submitter).val());

            formData.set('content', CKEDITOR.instances['editor'].getData());

            // Determine the URL and method based on whether we're creating or updating
            let url = "{{ isset($knowledgeBase) ? route('knowledge-base.upsert', encrypt($knowledgeBase->id)) : route('knowledge-base.upsert') }}";
            let method = 'POST';


            Swal.fire({
               title: 'Are you sure?',
               text: "You are about to save this knowledgeBase.",
               icon: 'warning',
               showCancelButton: true,
               confirmButtonColor: '#3085d6',
               cancelButtonColor: '#d33',
               confirmButtonText: 'Yes, save it!',
               preConfirm: () => {
                  Swal.showLoading();
                  $.ajax({
                     url: url,
                     method: method,
                     data: formData,
                     processData: false,
                     contentType: false,
                     success: function(response) {
                        toastr.success(response.message);
                        window.location.href = "{{ route('knowledge-base.index') }}";
                     },
                     error: function(xhr) {
                        let errorMsg = 'An error occurred while saving the knowledgeBase.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                           errorMsg = xhr.responseJSON.message;
                        }
                        toastr.error(errorMsg);
                     }
                  });
               }
            }).then((result) => {
               console.log(result);
            })
         });
         // ------------- End Handle form submission -------------
      })
   </script>



   <script>
      // ---------------- Data Bridge Select Employee Options ------------------
      $(document).ready(function() {
         // 1. Tampilkan/Sembunyikan kontainer berdasarkan Access Level
         $('select[name="level"]').on('change', function() {
            if ($(this).val() === 'some_employees') {
               $('#specific-employee-container').fadeIn();
               $('#employeeModal').modal('show'); // Otomatis buka modal
            } else {
               $('#specific-employee-container').fadeOut();
            }
         });

         let selectedEmployees = [];
         @if (isset($knowledgeBase))
            const selectedEmployeeList = $("#selected-employee-list input[name='selected_employees[]']").map(function() {
               return {
                  id: $(this).val(),
                  name: $(this).data('name'),
                  department: $(this).data('department'),
                  position: $(this).data('position')
               };
            }).get();

            selectedEmployees = selectedEmployeeList.map(emp => emp.id);
         @endif

         // 2. Logika Simpan dari Modal ke Form Utama
         $('#save-selection').on('click', function() {
            selectedEmployees = []; // Reset array setiap kali simpan
            let selectedNamesHtml = '';

            $('#kb-form #selected-employee-list').empty();

            $('.emp-checkbox:checked').each(function() {
               const id = $(this).val();
               const name = $(this).data('name');
               const department = $(this).data('department');
               const position = $(this).data('position');
               
               selectedEmployees.push({
                  id: id,
                  name: name,
                  department: department,
                  position: position
               });
               selectedNamesHtml += `
                  <span class="badge bg-info text-dark p-2 rounded-pill">
                     ${name} <i class="fas fa-user ms-1"></i>
                  </span>`;
            });

            // Masukkan ke Hidden Input (dalam format string/array)
            // $('#selected-employees-ids').val(JSON.stringify(selectedEmployees));
            count = 1;
            selectedEmployees.forEach(emp => {
               inputTemplate = `<div>
                     <input type="hidden" name="selected_employees[]" value="${emp.id}">
                     <label>${count}. ${emp.name} - ${emp.position} (${emp.department})</label>
                  </div>`

               $('#kb-form #selected-employee-list').append(inputTemplate);
               count++;
            });
            
            $('#specific-employee-container').fadeIn();

            $('#employee-pills').html(selectedNamesHtml || '<span class="text-muted small italic">No employees selected</span>');
            
            $('#employeeModal').modal('hide');
         });

         // 3. Fitur Filter Sederhana di dalam Modal
         $("#search-employee").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#employee-list tr").filter(function() {
               $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
         });

         $('#employee-list tr').on('click', function() {
            let checkbox = $(this).find('.emp-checkbox');
            checkbox.prop('checked', !checkbox.prop('checked'));
         });
      });
      // ---------------- End Data Bridge Select Employee Options ------------------
   </script>

   <script>
      // ---------------- Remove Attachment ------------------
      $('.delete-attachment-btn').on('click', function(e) {
         e.preventDefault();
         const button = $(this);
         const mediaId = button.data('media-id');

         Swal.fire({
            title: 'Are you sure?',
            text: "This attachment will be permanently deleted.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!',
            preConfirm: () => {
               return $.ajax({
                  url: "{{ url('administrator/knowledge-base/media') }}/" + mediaId,
                  type: 'POST',
                  data: {
                     _method: 'DELETE',
                     _token: $('meta[name="csrf-token"]').attr('content')
                  },
                  success: function(response) {
                     toastr.success(response.message);
                     button.parent().remove();
                  },
                  error: function(xhr) {
                     let errorMsg = 'An error occurred while deleting the attachment.';
                     if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                     }
                     toastr.error(errorMsg);
                  }
               });
            }
         });
      });
      // ---------------- End Remove Attachment ------------------
   </script>
@endsection