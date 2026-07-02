@extends('layouts.master')

@section('link')
   <meta name="csrf-token" content="{{ csrf_token() }}">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css"/>
   <link rel="stylesheet" href="{{  url('') }}/assets/libs/adminlte/select2/css/4.1.0/select2.min.css">
   <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endsection

@section('content')
   <div class="col">
      <div class="page-title-box d-sm-flex align-items-center justify-content-between">
         <h4 class="mb-sm-0">Edit IT Asset</h4>

         <div class="page-title-right">
            <ol class="breadcrumb m-0">
               <li class="breadcrumb-item"><a href="javascript: void(0);">IT Asset</a></li>
               <li class="breadcrumb-item active">Edit</li>
            </ol>
         </div>
      </div>
      
      <div class="card">
         <div class="card-header justify-content-end d-flex">
            <a href="{{ route('it_asset.index') }}" class="btn btn-primary btn-label waves-effect waves-light"><i class="ri-arrow-left-fill label-icon align-middle fs-16 me-2"></i> Back</a>
         </div>
         <div class="card-body">
            <form id="edit-form" action="{{ route('it_asset.update', ['itAsset' => $encryptedId, 'role' => $role ?? null]) }}" method="post">
               @csrf
               @method("PUT")
               <div class="row g-3">
                  <div class="col-md-6">
                     <label class="form-label">Asset Code</label>
                     <input type="text" class="form-control" name="asset_code" placeholder="XXXXXX-XXXX" required value="{{ $itAsset->asset_code }}">
                  </div>

                  <div class="col-md-6">
                     <label class="form-label">Brand / Model</label>
                     <input type="text" class="form-control" name="brand" placeholder="e.g. Lenovo ThinkCentre" required value="{{ $itAsset->brand }}">
                  </div>

                  <div class="col-md-6">
                     <label class="form-label">Owner</label>
                     <select class="form-select select2" name="pic" disabled data-placeholder="Select PIC" required>
                        <option value=""></option>
                        <option selected>
                              {{ $itAsset->employee->fullname }} - {{ $itAsset->employee->nik?? 'N/A' }}
                        </option>
                        {{-- @foreach ($employees as $employee)
                           <option 
                              value="{{ encrypt($employee->id) }}"
                              {{ $itAsset->employee_id == $employee->id? 'selected' : '' }}>
                                 {{ $employee->fullname }} - {{ $employee->department_name?? 'N/A' }}
                           </option>
                        @endforeach --}}
                     </select>
                  </div>

                  <div class="col-md-6">
                     <label class="form-label">Asset Type</label>
                     <select class="form-select select2" data-placeholder="Select Asset Type" name="asset_type_id" required>
                        <option value=""></option>
                        @foreach ($assetTypes as $assetType)
                           <option value="{{ $assetType->id }}" {{ $itAsset->asset_type_id == $assetType->id? 'selected' : '' }} class="text-capitalize">{{ $assetType->name }}-{{ ' (' . intval((int) $assetType->estimated_lifespan / 12) . ' years)' }}</option>
                        @endforeach
                     </select>
                  </div>

                  <div class="col-md-6">
                     <label class="form-label">Status</label>
                     <select class="form-select" name="status" required>
                        @if ($itAsset->status == App\Models\ITAsset::STATUS_ON_DISPOSAL)
                           <option value="{{ App\Models\ITAsset::STATUS_ON_DISPOSAL }}" selected>On Disposal</option>
                        @else
                           <option value="{{ App\Models\ITAsset::STATUS_ACTIVE }}" {{ strtolower($itAsset->status) == strtolower(App\Models\ITAsset::STATUS_ACTIVE)? 'selected' : '' }}>Active</option>
                           <option value="{{ App\Models\ITAsset::STATUS_BACKUP }}" {{ strtolower($itAsset->status) == strtolower(App\Models\ITAsset::STATUS_BACKUP)? 'selected' : '' }}>Backup</option>
                           {{-- @if ($role == App\Models\ITAsset::ROLE_SERVICE_DESK)
                              <option value="{{ App\Models\ITAsset::STATUS_BROKEN }}" {{ strtolower($itAsset->status) == strtolower(App\Models\ITAsset::STATUS_BROKEN)? 'selected' : '' }}>Broken</option>
                           @endif --}}
                        @endif
                     </select>
                  </div>

                  <div class="col-md-6">
                     <label class="form-label">Date Registered</label>
                     <input type="text" id="reg-date" class="form-control" name="year_registered" placeholder="Select Date" required>
                  </div>

                  <div class="col-md-6"></div>

                  <div class="col-md-6">
                     <label class="form-label">Price</label>
                     <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="text" class="form-control sale-price-form" name="price" placeholder="0.00" required value="{{ number_format($itAsset->price, 0, '.', '.') }}">
                     </div>
                  </div>

                  <div class="row mt-2 g-3">
                     <div class="col-md-6">
                        <label class="form-label">Hardware</label>
                        <textarea class="form-control" name="specification" rows="3" placeholder="Enter asset specification..." required>{{ $itAsset->specification }}</textarea>
                     </div>
                     <div class="col-md-6">
                        <label class="form-label">Software</label>
                        <textarea class="form-control" name="software" rows="3" placeholder="Enter asset software list...">{{ $itAsset->software }}</textarea>
                     </div>
                  </div>
               </div>
            </form>
         </div>
         <div class="card-footer">
            <button type="submit" form="edit-form" class="btn btn-primary float-end">Update Asset</button>
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
         $('.select2').select2()
      });

      // ------------- Initialize Flatpickr (Matching the YYYY-MM-DD format in your table) -------------
      $("#reg-date").flatpickr({
         allowInput: true,
         altInput: false,
         dateFormat: "Y-M-d",
         defaultDate: new Date("{{ $itAsset->year_registered }}"),
      });

      $(document).ready(function() {
         $(".sale-price-form").on("input", function() {
            let value = $(this).val();
            value = value.replace(/[^0-9]/g, ''); // Remove non-numeric characters
            if (value) {
               value = parseInt(value).toLocaleString('id-ID'); // Format as Indonesian Rupiah
            }
            $(this).val(value);
            $(this).attr('data-raw-value', value.replace(/[^0-9]/g, '')); // Store raw numeric value in data attribute
            $("#sale-price-total").text(calculateTotalSalePrice()); // Update total sale price
         });


         $('#edit-form').submit(function(e) {
            e.preventDefault()
            let swal

            // swal = Swal.fire({
            //    title: 'Loading!',
            //    didOpen: () => {
            //       Swal.showLoading()
            //    }
            // })

            const formData = new FormData(this)

            formData.set('price', formData.get('price').replace(/[^0-9]/g, ''))
            
            Swal.fire({
               title: 'Are you sure?',
               text: "You are about to update this IT Asset's data.",
               icon: 'warning',
               showCancelButton: true,
               confirmButtonColor: '#3085d6',
               cancelButtonColor: '#d33',
               confirmButtonText: 'Yes, update it!',
               reverseButtons: true,
               preConfirm: () => {
                  $.ajax({
                     url: $(this).attr('action'),
                     method: 'POST',
                     data: formData,
                     processData: false,
                     contentType: false,
                     success: function(response) {
                        Swal.showLoading()
                        if(response.status == 'success') {
                           Swal.fire({
                              icon: 'success',
                              title: 'IT Asset updated successfully!',
                              showConfirmButton: false,
                              timer: 1500
                           }).then(() => {
                              window.location.href = "{{ route('it_asset.index') }}"
                           })
                        } else {
                           Swal.fire({
                              icon: 'error',
                              title: 'Failed to update IT Asset',
                              text: response.message || 'An error occurred while updating the IT Asset.'
                           })
                        }
                     },
                     error: function(xhr) {
                        swal.close()
                        toastr.error('An error occurred while updating the IT Asset.')
                     }
                  })
               }
            })
         })
      })
   </script>
@endsection