@props([
   'ticket',
   'employees',
   'it_initiative' => false, // Tambahan props untuk membedakan form IT Initiative
   'dropdown_parent' => null,
])

@php
   $catalogData = App\Models\ServiceCatalog::get();
   $categories = [];

   foreach ($catalogData as $catalog) {
      $categories[$catalog->category][] = $catalog->service_catalog;
   }

   $employees = App\Models\Employee::with('department', 'position')->get();

   $employees->each(function($employee) {
      $employee->encrypted_id = encrypt($employee->id);
   });
@endphp

@pushOnce('styles')
   <link rel="stylesheet" href="{{ url('') }}/assets/libs/adminlte/select2/css/4.1.0/select2.min.css">
   <link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.css" rel="stylesheet" />

   {{-- checkbox styling --}}
   <style>
      .categories-card {
         cursor: pointer;
      }

      .categories-card .card {
         transition: 0.2s ease-in-out;
         border: 2px solid #e5e5e5;
      }

      .categories-card.active .card {
         border: 2px solid #0d6efd;
         background-color: #eef4ff;
      }

      .select2-container--default .select2-selection--single {
         height: calc(2.25rem + 2px);
         padding: 0.375rem;
         border: 1px solid #ced4da;
         border-radius: 0.375rem;
      }
      .select2-container--default .select2-selection--single .select2-selection__rendered {
         line-height: 1.5rem;
      }
      .select2-container--default .select2-selection--single .select2-selection__arrow {
         height: 100%;
      }
   </style>
   {{-- end checkbox styling --}}
@endPushOnce

<form method="POST" action="{{ route('service-ticket.store', $it_initiative? ['it_initiative' => encrypt($it_initiative)] : null) }}" id="ticketForm" class="row" enctype="multipart/form-data">
   @csrf

   @if (request()->is('myservice-desk*'))
      <input type="hidden" name="red" value="{{ encrypt('myservice-desk') }}">
   @endif

   <div class="col-12 text-center">
      <h4 class="mb-4">Report Form</h4>
   </div>

   <div id="step1">
      @if ($it_initiative)
         <div class="mb-3">
            <label data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-title="Report For" data-bs-content="Select the employee for whom you are reporting the issue.">Report For <span class="text-danger">*</span></label>
            <select name="report_for_id" class="form-select select2" data-dropdown-parent="{{ $dropdown_parent }}" data-placeholder="Select Employee">
               <option value=""></option>
               @foreach ($employees as $employee)
                  <option value="{{ $employee->encrypted_id }}">{{ $employee->nik }} - {{ $employee->fullname.' ('.($employee->department?->name?? 'N/A').' - '.($employee->position?->nama?? 'N/A').')' }}</option>
               @endforeach
            </select>
         </div>

         <div class="row">
            <div class="mb-3 col-6">
               <label>Categories <span class="text-danger">*</span></label>
               <select name="category" id="category" class="form-select select2" data-dropdown-parent="{{ $dropdown_parent }}" 
                     data-placeholder="Select Category" required>
                  <option value=""></option>
                  @foreach($categories as $category => $catalog)
                     <option value="{{ $category }}" class="text-capitalize" required>
                        {{ ucfirst(str_replace('_', ' ', $category)) }}
                     </option>
                  @endforeach
               </select>
            </div>

            <div class="mb-3 col-6">
               <label>Catalogs <span class="text-danger">*</span></label>
               <select name="catalog" id="catalog" class="form-select select2" data-dropdown-parent="{{ $dropdown_parent }}" 
                     data-placeholder="Select Catalog" required>
                  <option value=""></option>
               </select>
            </div>
         </div>
      @endif

      <div class="mb-3">
         <label>CC(Carbon Copy)</label>
         <select name="ccs[]" id="" class="form-select select2" data-dropdown-parent="{{ $dropdown_parent }}" data-placeholder="Select Employee" multiple>
            @foreach ($employees as $employee)
               <option value="{{ $employee->encrypted_id }}">{{ $employee->fullname.' ('.($employee->department?->name?? 'N/A').' - '.($employee->position?->nama?? 'N/A').')' }}</option>
            @endforeach
         </select>
         @error('ccs') <small class="text-danger">{{ $message }}</small> @enderror
      </div>

      @if (!$it_initiative)
         <label>Category <span class="text-danger">*</span></label>
         <div class="d-flex flex-wrap gap-3 justify-center" id="categoriesWrapper">
            @foreach ($categories as $key => $catalog)
               <div class="flex-1" style="min-width: 230px">
                  <div class="categories-card {{ old('category') == $key ? 'active' : '' }}" data-value="{{ $key }}">
                     <input type="radio" name="category" value="{{ $key }}" {{ old('category') == $key ? 'checked' : '' }} hidden>
                     <div class="card p-4 mb-0 text-center h-100 shadow-sm transition-all border-2 clickable-card">
                        <h6 class="mb-0 text-capitalize">{{ ucfirst(str_replace('_', ' ', $key)) }}<i class="bi bi-info-circle ms-2" data-bs-toggle="popover" data-bs-title="List Catalog Service" data-bs-content="{{ implode('<br>', $catalog) }}"></i></h6>
                     </div>
                  </div>
               </div>
            @endforeach
         </div>
      @endif

      @error('category') <small class="text-danger">{{ $message }}</small> @enderror
   </div>

   <div id="step2">
      <div class="mt-3">
         <label>Subject <span class="text-danger">*</span></label>
         <input type="text" name="subject" class="form-control" value="{{ old('subject') }}" min="5">
         @error('subject') <small class="text-danger">{{ $message }}</small> @enderror
      </div>

      <div class="mt-3">
         <label>Description <span class="text-danger">*</span></label><br>
         {{-- <textarea name="description" rows="5" class="form-control" minlength="5">{{ old('description') }}</textarea> --}}
         <textarea name="description" id="doc_description">{{ old('description') }}</textarea>
         @error('description') <small class="text-danger">{{ $message }}</small> @enderror
      </div>

      <div class="mt-3">
         <label>Attachments (Images/PDF) <span class="text-muted fs-12">(Optional)</span></label>
         <div class="form-group">
            <div class="needsclick dropzone" id="document-dropzone"></div>
         </div>
      </div>
   </div>
   <div class="d-flex justify-content-center gap-2 mt-4 col-12">
      <button type="submit" class="btn btn-success">Submit Ticket</button>
   </div>
</form>

@pushOnce('scripts')
   <!-- CKEditor 4-->  
   {{-- <script src="/assets/ckeditor/jquery.min.js"></script> --}}
   <script src="/assets/ckeditor/ckeditor.js"></script>
   <!-- Select2 -->
   <script src="/assets/libs/adminlte/select2/js/4.1.0/select2.min.js"></script>
   {{-- dropzone --}}
   <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.js"></script>
   <!-- Sweetalert -->
   <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
   <script type="text/javascript">
    $(function () {
      $('.select2').select2()
    });
  </script>
   <script>
      const serviceCategories = @json($categories);
      function inputCatalogOptions(el) {
         const val = $(el).val();
         console.log(val);
         
         $("#catalog").empty()
         $('#catalog').append(new Option('', ''));
         serviceCategories[val].forEach(catalog => {
            $('#catalog').append(new Option(catalog.toUpperCase(), catalog));
         });
         $('#catalog').append(new Option('Lain-lain', 'lain-lain'));
      }
      $('#category').change(function(e) {
         inputCatalogOptions(this);
      })
   
      $(document).ready(function() {
         $('#category').trigger('change');
      })
   </script>

   <script>
      var uploadedDocumentMap = {}
      Dropzone.options.documentDropzone = {
         url: "{{ route('service-ticket.uploads') }}",
         maxFilesize: 5, // MB
         addRemoveLinks: true,
         acceptedFiles: '.jpg,.jpeg,.png,.pdf,.zip,.docx,.doc,.xlsx,.xls,.pptx,.ppt,.csv,.txt',
         headers: {
               'X-CSRF-TOKEN': "{{ csrf_token() }}"
         },
         success: function (file, response) {
            // 1. Simpan input hidden dengan index [path] agar rapi di Laravel
            $('form').append('<input type="hidden" name="attachments[' + response.path + '][path]" value="' + response.path + '">')
            $('form').append('<input type="hidden" name="attachments[' + response.path + '][original_name]" value="' + response.original_name + '">')
            $('form').append('<input type="hidden" name="attachments[' + response.path + '][extension]" value="' + response.extension + '">')
            
            // 2. Perbaikan Naming: Gunakan response.path sebagai value di map
            uploadedDocumentMap[file.name] = response.path
            file.original_name = response.original_name
         },
         removedfile: function (file) {
            // Jika file dari server (init), gunakan file.file_name. Jika baru upload, gunakan map.
            var path = (typeof file.file_name !== 'undefined') ? file.file_name : uploadedDocumentMap[file.name];

            if (path) {
               $.ajax({
                  url: "{{ route('service-ticket.delete-upload') }}",
                  type: 'POST',
                  data: {
                     path: path, // Kirim sebagai 'path' agar sesuai dengan identifier kita
                     '_token': "{{ csrf_token() }}"
                  },
                  success: function (response) {
                     toastr.success(response.message);
                     // Hapus semua input hidden yang memiliki index path tersebut
                     $('form').find('input[name="attachments[' + path + '][path]"]').remove();
                     $('form').find('input[name="attachments[' + path + '][original_name]"]').remove();
                     $('form').find('input[name="attachments[' + path + '][extension]"]').remove();
                  }
               });
            }
            
            // Hapus preview dari UI
            if (file.previewElement != null && file.previewElement.parentNode != null) {
               file.previewElement.parentNode.removeChild(file.previewElement);
            }
         },
         init: function () {
            @if(isset($ticket) && $ticket->media)
               var files = {!! json_encode($ticket->media) !!}
               for (var i in files) {
                  var file = files[i];
                  // file_name di sini digunakan oleh logic removedfile (existing files)
                  var mockFile = { name: file.name, size: 12345, file_name: file.path }; 
                  
                  this.options.addedfile.call(this, mockFile);
                  this.options.thumbnail.call(this, mockFile, "/storage/" + file.path);
                  mockFile.previewElement.classList.add('dz-complete');
                  
                  $('form').append('<input type="hidden" name="attachments[' + file.path + '][path]" value="' + file.path + '">');
                  $('form').append('<input type="hidden" name="attachments[' + file.path + '][original_name]" value="' + file.name + '">');
                  $('form').append('<input type="hidden" name="attachments[' + file.path + '][extension]" value="' + file.extension + '">');
               }
            @endif
         }
      }
   </script>

   <script>
      $(document).ready(function(){
         var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
         var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl, {
                  html: true, // WAJIB: Agar tag <br> berfungsi
                  trigger: 'hover', // Opsional: Agar muncul saat kursor di atas icon
                  placement: 'top'
            })
         })

         $('.categories-card').click(function(){
            // remove active from all
            $('.categories-card').removeClass('active');

            // add active to clicked
            $(this).addClass('active');

            // check the hidden radio
            $(this).find('input[type="radio"]').prop('checked', true);
         });

         $('.select2-service-desk').select2({
            placeholder: 'Select Employee'
         });
      });
   </script>
   <script>
      $(document).ready(function() {
         CKEDITOR.replace( 'doc_description',
         { 
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
         
         // Toggle Report Type
         $('#report_type').on('change', function() {
            if ($(this).val() === 'self') {
                  $('#self_section').show();
                  $('#other_section').hide();
            } else {
                  $('#self_section').hide();
                  $('#other_section').show();
            }
         });

         // Toggle PIC Method (Search vs Manual)
         $('.pic-method').on('change', function() {
            if ($(this).val() === 'search') {
                  $('#pic_search_wrapper').show();
                  $('#pic_manual_wrapper').hide();
            } else {
                  $('#pic_search_wrapper').hide();
                  $('#pic_manual_wrapper').show();
            }
         });

         // Step Navigation
         $('#nextBtn').click(function() {
            $('#step1').hide();
            $('#step2').show();
         });

         $('#backBtn').click(function() {
            $('#step2').hide();
            $('#step1').show();
         });



         $('#ticketForm').on('submit', function(e) {
            e.preventDefault();
            
            for (instance in CKEDITOR.instances) {
               CKEDITOR.instances[instance].updateElement();
            }

            // Loading state
            const submitBtn = $(this).find('button[type="submit"]');
            submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Processing...');

            // Clear previous errors
            $('.text-danger').not('.static-label').remove();
            $('.is-invalid').removeClass('is-invalid');

            $.ajax({
               url: $(this).attr('action'),
               method: "POST",
               data: new FormData(this),
               processData: false,
               contentType: false,
               headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
               },
               success: function(response) {
                  Swal.fire({
                     icon: 'success',
                     title: 'Success!',
                     text: response.message,
                     timer: 2000,
                     showConfirmButton: false
                  }).then(() => {
                     window.location.href = response.redirect_url;
                  });
               },
               error: function(xhr) {
                  submitBtn.prop('disabled', false).html('Submit Ticket');
                  
                  if (xhr.status === 422) {
                     const errors = xhr.responseJSON.errors;

                     // console.log(errors);
                     
                     
                     // Notifikasi umum dengan Swal
                     Swal.fire({
                           icon: 'error',
                           title: 'Validation Error',
                           text: 'Please check the required fields. Error: ' + errors[Object.keys(errors)[0]][0], // Tampilkan error pertama sebagai contoh
                     });

                     // Tampilkan error spesifik di bawah tiap input
                     $.each(errors, function(key, value) {
                           // Cari element berdasarkan name atribut
                           let input = $('[name="' + key + '"]');
                           
                           // Handle khusus untuk select2
                           if (input.hasClass('select2-hidden-accessible')) {
                              input.next('.select2-container').addClass('is-invalid');
                              input.closest('div').append('<small class="text-danger">' + value[0] + '</small>');
                           } else {
                              input.addClass('is-invalid');
                              input.after('<small class="text-danger">' + value[0] + '</small>');
                           }
                     });
                     
                     // Jika error ada di Step 1 (Catalog) tapi user di Step 2
                     if(errors.catalog && $('#step1').is(':hidden')) {
                           $('#step2').hide();
                           $('#step1').show();
                     }
                  } else {
                     Swal.fire('Error', 'Something went wrong on the server.', 'error');
                  }
               }
            });
         });
      });
   </script>

   <script>
      function validateSize(input) {
         const fileSize = input.files[0].size / 1024 / 1024; // in MiB
            if (fileSize > 5) {
               $('#firstmodal').modal('show');
               // alert('Maaf ukuran file lebih dari 5MB');
               $(input).val(''); //direset
            } else {
               // dijalankan
            }
      }

      function tumbnailValidation() {
         //foto tumbnail
         var tumbnail = document.getElementById('tumbnail');             
         var pathtumbnail = tumbnail.value;

         // tipe file yang diizinkan
         var allowedExtensions =
               /(\.jpg|\.jpeg|\.png|\.pdf|\.zip|\.docx|\.doc|\.xlsx|\.xls|\.pptx|\.ppt|\.csv|\.txt)$/i;
         
         //tumbnail modal
         if (!allowedExtensions.exec(pathtumbnail)) {
            $('#secondmodal').modal('show');
            // alert('Invalid file type');
            tumbnail.value = '';
            return false;
         }
         else
         {             
            // image preview
            if (tumbnail.files && tumbnail.files[0]) {
               var reader = new FileReader();
               reader.onload = function(e) {
                     //image
                     document.getElementById(
                        'image_tumbnail').innerHTML =
                        '<img src="' + e.target.result
                        + '" class="img-thumbnail" alt="230x230" width="230px"/>';
                     //reset
                     // document.getElementById(
                     //     'reset').innerHTML =
                     //     '<input type="button" value="Reset" onclick="clearResult()"/>';
               };
                  
               reader.readAsDataURL(tumbnail.files[0]);
            }                
         }            
      }
      //remove image tumbnail
      function clearTumbnail(){
         //reset image tumbnail
         document.getElementById("image_tumbnail").innerHTML = '';
         var tumbnail = document.getElementById('tumbnail');
         tumbnail.value = '';

      }
   </script>
@endPushOnce