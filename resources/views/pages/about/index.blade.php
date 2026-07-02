@extends('layouts.master')
@section('link')
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css"
    integrity="sha256-GzSkJVLJbxDk36qko2cnawOGiqz/Y8GsQv/jMTUrx1Q=" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.22.2/dist/sweetalert2.min.css" integrity="sha256-NWFDlc+2O5YBqGykjREpkSKDMB6yEu5qPecoRrhcsC0=" crossorigin="anonymous">
@endsection
@section('content')
  <div class="row">
    <div class="col-12">
      <div class="page-title-box d-sm-flex align-items-center justify-content-between">
        <h4 class="mb-sm-0">About</h4>

        <div class="page-title-right">
          <ol class="breadcrumb m-0">
            <li class="breadcrumb-item"><a href="javascript: void(0);">Versioning</a></li>
            <li class="breadcrumb-item active">List</li>
          </ol>
        </div>

      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <div id="about-modal-wrapper">

            <!-- Search Form -->
            <div class="row mb-4 g-2 align-items-center">
              <div class="col-md-8">
                <form method="GET" action="{{ route('about.index') }}" class="d-flex">
                  <input type="text" name="search" placeholder="Search by version, description or name..."
                    value="{{ request('search') }}" class="form-control me-2" />
                  <button type="submit" class="btn btn-outline-warning">Search</button>
                  @if (request('search'))
                    <a href="{{ route('about.index') }}" class="btn btn-outline-danger ms-2">
                      Clear
                    </a>
                  @endif
                </form>
              </div>
              @can('about.editor')
                <div class="col-md-4 text-end">
                  <button type="button" class="btn btn-primary" id="create-new-about">Create new</button>
                </div>
              @endcan
            </div>

            <!-- modal -->
            <div class="modal fade" id="about-modal" tabindex="-1" aria-labelledby="aboutModalLabel" aria-hidden="true">
              <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                  <div class="modal-header bg-light">
                    <h5 class="modal-title" id="aboutModalLabel">About Form</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <form method="POST" id="form" action="/about">
                      @csrf
                      <input type="hidden" id="about_id" name="id" value="">
                      <div class="mb-3">
                        <label for="version" class="form-label">Version</label>
                        <input type="text" class="form-control" id="version" name="version" required>
                      </div>
                      <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <div id="description"></div>
                      </div>
                      <div class="mb-3">
                        <label for="release_date" class="form-label">Release Date</label>
                        <input type="text" class="form-control flatpickr" id="release_date" name="release_date"
                          required>
                      </div>
                    </form>
                  </div>
                  <div class="modal-footer">
                    <button type="button" id="discard" class="btn btn-outline-danger"
                      data-bs-dismiss="modal">Discard</button>
                    <button type="button" id="save" class="btn btn-primary">Save</button>
                  </div>
                </div>
              </div>
            </div>
          </div>


          @foreach ($abouts as $about)
            <div class="card mb-3">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-start border-bottom pb-2 mb-2">
                  <div>
                    <h5 class="card-title mb-1">Version <span class="text-primary">{{ $about->version }}</span></h5>
                    <p class="card-subtitle text-muted small">{{ $about->release_date->format('d-m-Y') }}</p>
                  </div>
                  @can('about.editor')
                    <div>
                      <button class="btn btn-sm btn-outline-primary edit-about" data-id="{{ $about->id }}"
                        data-version="{{ $about->version }}" data-release_date="{{ $about->release_date->format('Y-m-d') }}"
                        data-description="{{ $about->description }}">
                        Edit
                      </button>
                      <button class="btn btn-sm btn-outline-danger delete-about" data-id="{{ $about->id }}">
                        Delete
                      </button>
                    </div>
                  @endcan
                </div>
                <div class="ql-editor">{!! $about->description !!}</div>
              </div>
            </div>
          @endforeach

          <div class="mt-4">
            {{ $abouts->appends(request()->query())->links('pagination::bootstrap-5') }}
          </div>

        </div>
      </div>
    </div>
  </div>
@endsection

@section('script')
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"
    integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js"
    integrity="sha256-Huqxy3eUcaCwqqk92RwusapTfWlvAasF6p2rxV6FJaE=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.22.2/dist/sweetalert2.all.min.js" integrity="sha256-Ua8fKA4E1l7RSqT5HOjK0m/PrSwP41XFTs++qmtWey8=" crossorigin="anonymous"></script>
@endsection

@section('javascript')
  <script>
    const quill = new Quill('#description', {
      theme: 'snow'
    });

    $('.flatpickr').flatpickr({
      altInput: true,
      altFormat: 'd-M-Y',
      dateFormat: 'Y-m-d'
    });


    // Bootstrap 5 modal logic
    let aboutModal = null;
    $(function() {
      aboutModal = new bootstrap.Modal(document.getElementById('about-modal'));
    });

    // Open modal for create
    $(document).on('click', '#create-new-about', function() {
      $('#about_id').val('');
      $('#version').val('');
      $('#release_date').flatpickr().clear();
      quill.setContents([]);
      $('#save').text('Save');
      aboutModal.show();
    });

    // Open modal for edit
    $(document).on('click', '.edit-about', function() {
      const aboutId = $(this).data('id');
      const version = $(this).data('version');
      const releaseDate = $(this).data('release_date');
      const description = $(this).data('description');

      $('#about_id').val(aboutId);
      $('#version').val(version);
      $('#release_date').flatpickr().setDate(releaseDate, true);
      quill.root.innerHTML = description;
      $('#save').text('Update');
      aboutModal.show();
    });

    // Discard button closes modal (handled by data-bs-dismiss)

    // ...existing code for save and delete handlers...
    $('#save').on('click', function(event) {
      event.preventDefault();
      let form = $('#form');
      let formData = new FormData(form[0]);
      const descriptionHtml = quill.root.innerHTML;
      formData.append('description', descriptionHtml);

      // Determine if we're updating or creating
      const aboutId = $('#about_id').val();
      const isUpdate = aboutId ? true : false;
      const url = '/about';
      const method = 'POST';

      Swal.fire({
        title: isUpdate ? 'Confirm Update' : 'Confirm Create',
        text: isUpdate ? "Are you sure you want to update this entry?" :
          "Are you sure you want to create a new entry?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Confirm',
        showLoaderOnConfirm: true,
        preConfirm: async () => {
          return $.ajax({
            url: url,
            type: method,
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
              'Accept': 'application/json' // Important for proper error handling
            },
            data: formData,
            processData: false,
            contentType: false,
          }).then(response => {
            return response;
          }).catch(xhr => {
            let errorMessage = 'Something went wrong!';

            if (xhr.status === 422) {
              // Handle validation errors
              const errors = xhr.responseJSON.errors;
              errorMessage = '<ul class="text-left">';

              for (const field in errors) {
                const fieldName = field.replace('_', ' ');
                errorMessage += `<li><strong>${fieldName}:</strong> ${errors[field].join(', ')}</li>`;
              }

              errorMessage += '</ul>';
            } else if (xhr.responseJSON?.message) {
              errorMessage = xhr.responseJSON.message;
            }

            Swal.showValidationMessage(errorMessage);
          });
        },
        allowOutsideClick: () => !Swal.isLoading()
      }).then((result) => {
        if (result.isConfirmed) {
          Swal.fire({
            icon: 'success',
            title: 'Success',
            text: result.value.message,
            showConfirmButton: false,
            timer: 1500
          }).then(() => {
            window.location.reload(); // Or redirect as needed
          });
        }
      }).catch(error => {
        console.error('Error:', error);
        // Additional error handling if needed
      });
    });

    // Delete confirmation and handling
    $(document).on('click', '.delete-about', function() {
      const aboutId = $(this).data('id');
      const aboutElement = $(this).closest('.panel');

      Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: `/about/${aboutId}`,
            type: 'DELETE',
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
              if (response.success) {
                aboutElement.fadeOut(300, function() {
                  $(this).remove();
                });
                Swal.fire(
                  'Deleted!',
                  response.message,
                  'success'
                );
                window.location.reload();
              }
            },
            error: function(xhr) {
              Swal.fire(
                'Error!',
                xhr.responseJSON?.message || 'Failed to delete about entry',
                'error'
              );
            }
          });
        }
      });
    });
  </script>
@endsection
