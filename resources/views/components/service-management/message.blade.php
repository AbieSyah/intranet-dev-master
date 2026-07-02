{{-- accept type = edit/null --}}
<form id="message-form" action="{{ route('service-desk.send-message', ['id' => encrypt($ticket->id), 'role' => encrypt($role), 'messageId' => isset($isEdit) && isset($ticketMessage) ? encrypt($ticketMessage->id) : null]) }}" method="post" enctype="multipart/form-data">
   @csrf
   @if (isset($isInternal))
      <input type="hidden" name="is_internal" value="1">
   @endif

   {{-- <input id='chatInput' type="text" class="form-control border-0 bg-light" placeholder="Type a message..."> --}}
   @if ($role == App\Models\ServiceTicketMessage::ROLE_SERVICE_CHANGE)
      <div class="mt-3">
         <label>Actual Execution <span class="text-danger">*</span></label><br>
         {{-- <textarea name="actual_execution" rows="5" class="form-control" minlength="5">{{ old('actual_execution') }}</textarea> --}}
         <input type="text" name="actual_execution" id="doc_actual_execution" required  class="form-control date-range-picker" value="{{ $isEdit? $ticket->serviceChange->actual_start->format('Y-m-d H:i').' to '.$ticket->serviceChange->actual_end->format('Y-m-d H:i') : '' }}"/>
         @error('actual_execution') <small class="text-danger">{{ $actual_execution }}</small> @enderror
      </div>
   @endif

   <div class="mt-3">
      <label>Message <span class="text-danger">*</span></label><br>
      {{-- <textarea name="message" rows="5" class="form-control" minlength="5">{{ old('message') }}</textarea> --}}
      <textarea name="message" id="doc_message" required row="5">{{ old('message')?? (isset($ticketMessage)? $ticketMessage->message : '') }}</textarea>
      @error('message') <small class="text-danger">{{ $message }}</small> @enderror
   </div>

   
   <div class="mt-3">
      <label>Attachments</label><span>(optional)</span> <br>
      <input type="file" id="tumbnail" name="attachments[]" 
         onchange="validateSize(this); tumbnailValidation();" 
         accept=".jpg, .jpeg, .png, .pdf, .zip, .docx, .doc, .xlsx, .xls, .pptx, .ppt, .csv, .txt" multiple class="form-control">
      @error('message') <small class="text-danger">{{ $message }}</small> @enderror
   </div>
   <div id="image_tumbnail"></div>

   @if(isset($isEdit) && $isEdit == true)
      <style>
         .media-hover {
            display: none;
            opacity: 0;
            z-index: 99;
         }
         .position-relative:hover .media-hover {
            display: flex;
            opacity: 1;
            transition: display 0.3s ease-in-out;
         }
      </style>
      <div class="mt-4">
         <label>Existing Attachments</label><br>
         <div class="d-flex flex-wrap gap-3">
            @if ($ticketMessage)
               @if ($ticketMessage->media->count() == 0)
                  <p class="text-muted">No evidence files uploaded.</p>
               @else
                  <div class="mt-2 pt-2 border-t">
                     {{-- Files List --}}
                     @php
                        $files = $ticketMessage->media->filter(fn($m) => !in_array($m->extension, ['jpg', 'jpeg', 'png']));
                     @endphp
                     @if($files->count() > 0)
                        <div class="mb-3">
                           <ul class="list-unstyled mb-0">
                              @foreach($files as $media)
                                 <li>
                                    <a href="{{ asset('storage/'.$media->path) }}" target="_blank" 
                                       class="flex items-center rounded text-xs {{ $ticketMessage->role == 'it' ? 'bg-blue-700 text-white' : 'bg-gray-50 text-blue-600' }}">
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
                        $images = $ticketMessage->media->filter(fn($m) => in_array($m->extension, ['jpg', 'jpeg', 'png']));
                     @endphp
                     @if($images->count() > 0)
                        <div class="d-flex flex-wrap gap-2 justify-content-{{ $ticketMessage->role == 'it' ? 'end' : 'start' }}">
                           @foreach($images as $media)
                              <a href="{{ asset('storage/'.$media->path) }}" class="d-block" target="_blank">
                                 <img src="{{ asset('storage/'.$media->path) }}" class="rounded shadow-sm hover:opacity-90 transition-opacity" style="width: 80px; height: 80px; object-fit: cover">
                              </a>
                           @endforeach
                        </div>
                     @endif
                  </div>
               @endif
            @endif
         </div>
      </div>
   @endif

   <div class="d-flex gap-3 mt-3">
      <button type="submit" class="btn btn-secondary w-100">Send <i class="ri-send-plane-fill"></i></button>
   </div>
</form>

@pushOnce('scripts')
   {{-- ckeditor --}}
   <script src="/assets/ckeditor/ckeditor.js"></script>
   {{-- dropzone --}}
   <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.js"></script>

   <script>
      flatpickr('.date-range-picker', {
         enableTime: true,
         mode: 'range',
         time_24hr: true,
         dateFormat: "Y-m-d H:i",
      });

      // --------------------------------- MESSAGE FORM ---------------------------------
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

         var allowedExtensions = /(\.jpg|\.jpeg|\.png|\.pdf|\.zip|\.docx|\.doc|\.xlsx|\.xls|\.pptx|\.ppt|\.csv|\.txt)$/i;

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
                  if (file.type === "image/jpeg" || file.type === "image/png" || file.type === "image/jpg") {
                     content = `
                        <div class="col-md-4 mb-2">
                           <div class="card p-1">
                              <img src="${e.target.result}" class="img-fluid rounded" style="height: 100px; object-fit: cover;"/>
                              <small class="d-block text-truncate text-center mt-1">${file.name}</small>
                           </div>
                        </div>`
                  } else {
                     content = `
                        <div class="col-md-4 mb-2">
                           <div class="card p-2 border-primary text-center">
                              <i class="ri-file-fill ri-3x text-primary"></i>
                              <small class="d-block text-truncate">${file.name}</small>
                           </div>
                        </div>`;
                  }
                  previewRow.innerHTML += content;
               };
               reader.readAsDataURL(file);
            });
         }
      }

      $(document).ready(function() {
         const editor = CKEDITOR.replace( 'doc_message', { 
            // toolbar :[['Undo','Redo','RemoveFormat'],['Bold', 'Italic', '-', 'NumberedList', 'BulletedList']]
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

         editor.on( 'required', function( evt ) {
            editor.showNotification( 'This field is required.', 'warning' );
            evt.cancel();
         } );

         $('.delete-media-btn').click(function() {
            const mediaId = $(this).data('media-id');
            const mediaName = $(this).data('media-name');
            Swal.fire({
               title: 'Are you sure?',
               text: `You are about to delete the attachment "${mediaName}". This action cannot be undone.`,
               icon: 'warning',
               showCancelButton: true,
               confirmButtonColor: '#d33',
               cancelButtonColor: '#6c757d',
               confirmButtonText: 'Yes, delete it!',
               cancelButtonText: 'No, keep it',
               showLoaderOnConfirm: true,
               reverseButtons: true,
               preConfirm: () => {
                  return $.ajax({
                     url: "/service-desk/delete-message-media/" + mediaId,
                     method: "POST",
                     data: {
                        _token: "{{ csrf_token() }}",
                        media_id: mediaId
                     }
                  }).done(function(response) {
                     if (response.success) {
                        toastr.success(response.message);
                        // $(`button[data-media-id="${mediaId}"]`).closest('.position-relative').remove();
                        window.location.href = window.location.href; // Refresh page to reflect changes
                     } else {
                        toastr.error(response.message);
                     }
                  }).fail(function() {
                     toastr.error('An error occurred while deleting the attachment.');
                  });
               }
            });
         });
      })
      // --------------------------------- END MESSAGE FORM ---------------------------------
   </script>
@endPushOnce