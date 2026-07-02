@extends('layouts.master')

@section('link')
   <meta name="csrf-token" content="{{ csrf_token() }}">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css"/>
   <link rel="stylesheet" href="{{  url('') }}/assets/libs/adminlte/select2/css/4.1.0/select2.min.css">
   <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endsection

@section('content') 
   <div class="row">
      <div class="col-12">
         <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Movement IT Asset</h4>

            <div class="page-title-right">
               <ol class="breadcrumb m-0">
                  <li class="breadcrumb-item"><a href="javascript: void(0);">IT Asset</a></li>
                  <li class="breadcrumb-item active">Movement</li>
               </ol>
            </div>
         </div>
      </div>
   </div>

   <div class="d-flex justify-content-center">
      <div class="col-lg-7">
         <div class="card">
            <div class="card-header">
               <div class="d-flex justify-content-between">
                  <div>
                     
                  </div>
                  <div>
                     <a href="{{ route('it_asset.index') }}" class="btn btn-primary btn-label waves-effect waves-light"><i class="ri-arrow-left-fill label-icon align-middle fs-16 me-2"></i> Back</a>
                  </div>
               </div>
            </div>
            <div class="card-body">
               <form method="post" action="{{ route('it_asset.movement.update', encrypt($itAsset->id)) }}" id="movement-form">
                  @csrf
                  <div class="d-flex gap-2 align-items-center flex-wrap justify-content-center mb-3">
                     <div class="flex-1">
                        <label class="form-label">From Owner</label>
                        <select class="form-select select2" name="from_pic" data-placeholder="Select Current Person in Charge" style="Background-color: #eff2f7;" disabled>
                           <option value=""></option>
                           @foreach ($employees as $employee)
                              <option value="{{ encrypt(mt_rand(100, 1000)) }}" {{ $employee->id == $itAsset->employee_id? 'selected' : '' }}>
                                    {{ $employee->fullname }} - {{ $employee->position?->nama }}({{ $employee->department?->name }})
                              </option>
                           @endforeach
                        </select>
                     </div>
      
                     <div class="flex-1">
                        <label class="form-label">To Owner</label>
                        <select class="form-select select2" name="to_pic" data-placeholder="Select Next Person in Charge" required>
                           <option value=""></option>
                           @foreach ($employees as $employee)
                              <option 
                                 value="{{ encrypt($employee->id) }}">
                                    {{ $employee->fullname }} - {{ $employee->position?->nama }}({{ $employee->department?->name }})
                              </option>
                           @endforeach
                        </select>
                     </div>
                  </div>

                  <div class="mb-3">
                     <label for="reason">Reason</label>
                     <textarea name="reason" id="reason" cols="30" rows="10" class="form-control"></textarea>
                  </div>

                  <button class="btn btn-primary w-100" type="submit">Transfer PIC</button>
               </form>
            </div>
            {{-- <div class="card-footer"></div> --}}
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
   <script>
      $(document).ready(function() {
         $('.select2').each(function() {
            $(this).select2()
         })

         const form = $('#movement-form')
         form.submit(function(e) {
            e.preventDefault()

            let swal

            const formData = new FormData(this)

            Swal.fire({
               title: 'Confirm Movement?',
               text: "Are you sure you want to transfer this asset?",
               icon: 'warning',
               showCancelButton: true,
               confirmButtonColor: '#3085d6',
               cancelButtonColor: '#d33',
               confirmButtonText: 'Yes, move it!',
               showLoaderOnConfirm: true, // Enables the loading state
               reverseButtons: true,  
               preConfirm: () => {
                  return $.ajax({
                     url: $(this).attr('action'),
                     type: 'POST',
                     processData: false,
                     contentType: false,
                     data: formData,
                     dataType: 'json',
                  })
                  .done(response => {
                     return response; // Pass response to the next .then()
                  })
                  .fail(xhr => {
                     let errorMsg = xhr.responseJSON?.message || 'Something went wrong';
                     Swal.hideLoading(); // Hide loading state on error
                     // Swal.showValidationMessage(`Request failed: ${errorMsg}`);
                     Swal.fire('Error!', errorMsg + " Error: " + xhr.responseJSON?.error, 'error');
                  });
               },
               allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
               if (result.isConfirmed) {
                  const res = result.value;
                  if (res.status === 'success') {
                     Swal.fire('Success!', res.message, 'success').then(() => {
                        window.location.href = "{{ route('it_asset.index') }}";
                     });
                  } else if(res.status === 'info') {
                     Swal.fire('No Updates', res.message, 'info');
                  } else {
                     Swal.fire('Error!', res.message, 'error');
                  }
               }
            });
         })
      })
   </script>
@endsection