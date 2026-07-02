@extends('layouts.master')
@section('link')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
<div class="row">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <h4 class="mb-sm-0">Assign Permission on Role</h4>

      <div class="page-title-right">
          <ol class="breadcrumb m-0">
              <li class="breadcrumb-item"><a href="javascript: void(0);">Role</a></li>
              <li class="breadcrumb-item active">Assign</li>
          </ol>
      </div>

    </div>
  </div>
</div>

<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-header align-items-center d-flex justify-content-between">
        <h3 class="card-title">Role {{ $role->name ?? '' }}</h3>  
        <div class="flex-shrink-0">
            <a href="{{ URL::previous() }}" class="btn btn-primary btn-label waves-effect waves-light"><i class="ri-arrow-left-circle-line label-icon align-middle fs-16 me-2"></i> Back</a>
        </div>
      </div><!-- end card header -->
      <div class="card-body">
        <form class="form" action="{{ route('role.store') }}" method="post">
            @csrf
            @method('POST')
            <div class="row gy-4">
                <input type="hidden" name="id" id="id" value="{{ old('id', $role->id ?? '') }}">
                <div class="col-xxl-6">
                    <div class="fv-row">
                        <label class="required fw-semibold fs-6 mb-2">Role Name</label>
                        <input type="text" name="name" class="form-control form-control-solid mb-3 mb-lg-0"
                            placeholder="Full name" value="{{ old('name', $role->name ?? '') }}" />
                    </div>
                </div>
                <div class="col-xxl-6">
                    <div class="fv-row form-group">
                        <label class="required fw-semibold fs-6 mb-2">Permissions</label>
                        <div class="accordion" id="accordionExample">
                            @foreach ($permissionGroups as $prefix => $subprefixes)
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapse{{ $prefix }}" aria-expanded="false"
                                            aria-controls="collapse{{ $prefix }}">
                                            {{ $prefix }}
                                        </button>
                                    </h2>
                                    <div id="collapse{{ $prefix }}" class="accordion-collapse collapse"
                                        data-bs-parent="#accordionExample">
                                        <div class="accordion-body">
                                            <div class="row">
                                                @foreach ($subprefixes as $subprefix => $permissions)
                                                    @if ($subprefix)
                                                        <div class="col-md-6">
                                                            <h4 class="subheader" style="padding-top: 1em">
                                                                {{ $subprefix }}</h4>
                                                            <div class="row">
                                                                @if (!$role)
                                                                    @foreach ($permissions as $permission)
                                                                        <div class="col-sm-6 col-md-12 mb-2">
                                                                            <div class="form-check">
                                                                                <input name="permissions[]"
                                                                                    class="form-check-input" type="checkbox"
                                                                                    id="permission{{ $permission->id }}"
                                                                                    value="{{ $permission->id }}">
                                                                                <label class="form-check-label"
                                                                                    for="permission{{ $permission->id }}">
                                                                                    {{ $permission->name }}
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    @endforeach
                                                                @else
                                                                    @foreach ($permissions as $permission)
                                                                        <div class="col-sm-6 col-md-12 mb-2">
                                                                            <div class="form-check">
                                                                                <input name="permissions[]"
                                                                                    class="form-check-input" type="checkbox"
                                                                                    id="permission{{ $permission->id }}"
                                                                                    value="{{ $permission->id }}"
                                                                                    {{ $role->permissions->contains('id', $permission->id) ? 'checked' : '' }}>
                                                                                <label class="form-check-label"
                                                                                    for="permission{{ $permission->id }}">
                                                                                    {{ $permission->name }}
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    @endforeach
                                                                @endif

                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                @foreach ($permissions as $permission)
                                                                    <div class="col-sm-6 col-md-12">
                                                                        <div class="checkbox checkbox-primary">
                                                                            <input name="permissions[]" type="checkbox"
                                                                                value="{{ $permission->id }}"
                                                                                {{ optional($role)->permissions->contains('id', $permission->id) ? 'checked' : '' }}>
                                                                            <label>{{ $permission->name }}</label>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-xxl-6">
                    <div id="resultContent">
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="d-flex justify-content-end">
                        <div class="text-center pt-10">
                            <button type="submit" class="btn btn-primary" data-kt-users-modal-action="submit">
                                <span class="d-none spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                <span class="indicator-label">Save</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
      </div>
    </div>
  </div>
  <!--end col-->
</div>
<!--end row-->
@endsection
@section('script')
<!-- Sweetalert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection
@section('javascript')
    <script type="text/javascript">
        $(document).ready(function() {
            let swalert;
            $("form").submit(function(e) {
                e.preventDefault();

                swalert = Swal.fire({
                    title: 'Loading!',
                    didOpen: () => {
                        Swal.showLoading()
                    }
                });

                const formData = new FormData(this);
                // const submitButton = $(this).find("button[type='submit']");

                // submitButton.prop("disabled", true);

                // submitButton.find(".spinner-border").removeClass("d-none");
                // submitButton.find(".indicator-label").addClass("d-none");

                $.ajax({
                    url: $(this).attr("action"),
                    method: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        swalert.hideLoading();
                        swalert.update({
                            title: "Success",
                            text: response.message,
                            icon: "success",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: {
                                popup: 'swal2-noanimation',
                                confirmButton: "btn btn-primary"
                            }
                        });
                        swalert.then(() => window.location.reload() = response.redirect)
                        // const message = response.message;

                        // // Display the success message
                        // $("#resultContent").html(
                        //     `<h4 class="text-success">${message}</h4>`).removeClass(
                        //     "d-none");

                        // // Close the modal after 2 seconds
                        // setTimeout(function() {
                        //     window.location.replace("{{ route('role.index' ?? '') }}");
                        // }, 2000);

                        // // Reload your data table (if needed)
                        // table.ajax.reload(null, false);
                    },
                    error: function(xhr, status, error) {
                        console.log({
                            xhr,
                            status,
                            error
                        });
                        handleErrorResponse(xhr.responseJSON);
                    }
                    // ,
                    // complete: function() {
                    //     submitButton.prop("disabled", false);
                    //     submitButton.find(".spinner-border").addClass("d-none");
                    //     submitButton.find(".indicator-label").removeClass("d-none");
                    // }
                });
            });

            function handleErrorResponse(responseJson) {
                let errorMessage = '';

                if (responseJson.message) {
                    errorMessage += `<h4 class="text-danger">${responseJson.message}</h4>`;
                }

                if (responseJson.errors) {
                    for (const fieldName in responseJson.errors) {
                        errorMessage += `<p class="text-danger">${responseJson.errors[fieldName][0]}</p>`;
                    }
                }

                if (responseJson.responseText) {
                    errorMessage += `<p class="text-danger">${responseJson.responseText}</p>`;

                }

                if (errorMessage === '') {
                    errorMessage += '<p class="text-danger">An error occurred.</p>';
                }

                // Display error message using SweetAlert
                swalert.update({
                    title: 'Error',
                    html: errorMessage,
                    icon: 'error',
                    buttonsStyling: false,
                    confirmButtonText: 'Ok',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    }
                });

                // $("#resultContent").html(errorMessage).removeClass("d-none");
            }
        });
    </script>
@endsection
