@extends('layouts.master')

@section('link')
   <meta name="csrf-token" content="{{ csrf_token() }}">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css"/>
   <link rel="stylesheet" href="{{  url('') }}/assets/libs/adminlte/select2/css/4.1.0/select2.min.css">
   <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endsection

@section('content')

<div class="row">
   <div class="col-lg-12">
      <div class="card">
         <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">Bulk Edit IT Assets</h4>
            <a href="{{ route('it_asset.index') }}" class="btn btn-primary btn-label waves-effect waves-light"><i class="ri-arrow-left-fill label-icon align-middle fs-16 me-2"></i> Back</a>
            {{-- <button type="submit" data-role="all" class="btn submit-button btn-primary">
               <i class="ri-save-line me-1"></i> Save All
            </button> --}}
         </div>
         
         <div class="card-body">
            @foreach (['existingItAssets' => $existingItAssets, 'newItAssets' => $newItAssets] as $index => $item)
               @if (!$item->isEmpty())                  
                  <form data-role="{{ $index }}" class="upsert-form" action="{{ route('it_asset.import.upsert') }}" method="POST">
                     @csrf
                     @if ($index == 'existingItAssets')
                        <div class="alert alert-warning alert-label-icon rounded-label" role="alert">
                           <i class="ri-error-warning-line label-icon"></i>
                           <strong>The imported asset list that already exist will placed here with the new value from imported file</strong>
                        </div>
                     @else
                        <div class="alert alert-success alert-label-icon rounded-label" role="alert">
                           <i class="ri-error-warning-line label-icon"></i>
                           <strong>The new imported asset list</strong>
                        </div>
                     @endif
                     <div class="table-responsive">
                        <table class="table table-stripped table-nowrap align-middle">
                           <thead class="table-light">
                              <tr class="text-center">
                                 <th style="min-width: 150px">Asset Code</th>
                                 <th style="min-width: 200px">Brand</th>
                                 <th style="min-width: 250px">Owner</th>
                                 <th>Type</th>
                                 <th>Year Registered</th>
                                 <th style="min-width: 150px">Price</th>
                                 <th style="min-width: 150px">Status</th>
                                 <th style="min-width: 150px">Specification</th>
                                 <th style="min-width: 150px">Software</th>
                              </tr>
                           </thead>
                           <tbody>
                              @foreach($item as $assetIndex => $asset)
                                 <tr class="asset-row">
                                    <td>
                                       <input type="text" name="it_assets[{{ $assetIndex }}][asset_code]" 
                                          class="form-control form-control-sm text-center asset-code" 
                                          value="{{ $asset['asset_code'] }}">
                                    </td>
                                    <td>
                                       <input type="text" name="it_assets[{{ $assetIndex }}][brand]" 
                                          class="form-control form-control-sm asset-brand" 
                                          value="{{ $asset['brand'] }}" required>
                                    </td>
                                    {{-- <td></td> --}}
                                    <td>
                                       <select name="it_assets[{{ $assetIndex }}][pic]" class="form-select form-select-sm select2 {{ $index == 'newItAssets'? 'owner-select' : '' }} asset-pic" data-placeholder="Find Name/NIK/Position/Department Employee..." required="true" {{ $index == 'existingItAssets' ? 'disabled' : '' }}>
                                          <option></option>
                                          @if ($index == "existingItAssets")
                                             <option selected>
                                                {{ $asset['employee_fullname'] ?? 'N/A' }} - {{ $asset['position'] }} ({{ $asset['department'] ?? 'N/A' }})
                                             </option>
                                          @elseif ($asset['employee'])
                                             <option value="{{ $asset['employee']['encrypted_id'] }}" selected>
                                                {{ $asset['employee']['fullname'] }} - {{ $asset['employee']['position'] }} ({{ $asset['employee']['department'] ?? 'N/A' }})
                                             </option>
                                          @endif
                                       </select>
                                    </td>
                                    <td>
                                       <select name="it_assets[{{ $assetIndex }}][asset_type_id]" class="form-select form-select-sm select2 asset-type" required>
                                          <option></option>
                                          @foreach($assetTypes as $type)
                                             <option value="{{ encrypt($type->id) }}" {{ strtolower($type->name) == strtolower($asset['asset_type'] ?? '') ? 'selected' : '' }}>
                                                {{ $type->name.' - '.floor($type->estimated_lifespan / 12).' years'}}
                                             </option>
                                          @endforeach
                                       </select>
                                    </td>
                                    <td>
                                       <input type="date" name="it_assets[{{ $assetIndex }}][year_registered]" 
                                          class="form-control form-control-sm asset-year-registered" 
                                          value="{{ $asset['year_registered'] }}" required>
                                    </td>
                                    <td>
                                       <div class="input-group input-group-sm">
                                          <span class="input-group-text">Rp</span>
                                          <input type="number" name="it_assets[{{ $assetIndex }}][price]" 
                                                class="form-control asset-price" 
                                                value="{{ (float)$asset['price'] }}" required>
                                       </div>
                                    </td>
                                    <td>
                                       @isset ($asset['old_status'])
                                          <input type="text" disabled class="form-select form-select-sm" value="{{ $asset['old_status'] }}">
                                       @endif
                                       <select name="it_assets[{{ $assetIndex }}][status]" class="form-select form-select-sm asset-status" required>
                                          <option value="active" {{ ($asset['status']?? $asset->status) == 'active' ? 'selected' : '' }}>Active</option>
                                          <option value="backup" {{ ($asset['status']?? $asset->status) == 'backup' ? 'selected' : '' }}>Backup</option>
                                          {{-- <option value="3" {{ $asset['status'] == 3 ? 'selected' : '' }}>Disposed</option> --}}
                                       </select>
                                    </td>
                                    <td>
                                       <textarea name="it_assets[{{ $assetIndex }}][specification]" rows="3" class="form-control form-control-sm asset-specification">{{ $asset['specification']?? '' }}</textarea>
                                    </td>
                                    <td>
                                       <textarea name="it_assets[{{ $assetIndex }}][software]" rows="3" class="form-control form-control-sm asset-software">{{ $asset['software']?? '' }}</textarea>
                                    </td>
                                    @if ($index == 'newItAssets')
                                       <td>
                                          <button type="button" class="btn btn-warning btn-sm" onclick="removeRow(this)">
                                             <i class="ri-delete-bin-line text-dark"></i>
                                          </button>
                                       </td> 
                                    @endif
                                 </tr>
                              @endforeach
                           </tbody>
                        </table>
                     </div>
                     <div class="d-flex justify-content-center">
                        <div class="spinner-border text-primary mt-2 mb-5" role="status">
                           <span class="visually-hidden">Loading...</span>
                        </div>
                        @if ($index == 'existingItAssets')
                           <button type="submit" class="btn submit-button mt-2 btn-warning mb-5 d-none" title="Only update all the existing Assets" disabled>Update Assets</button>
                        @else
                           <button type="submit" class="btn submit-button mt-2 btn-success d-none" title="Create all non existing Assets" disabled>Create New</button>
                        @endif
                     </div>
                  </form>
               @endif
            @endforeach
         </div>
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
         defaultDate: new Date(),
      });
      // ------------- End Initialize Flatpickr (Matching the YYYY-MM-DD format in your table) -------------
   </script>

   <script>
      function removeRow(button) {
         Swal.fire({
            title: 'Are you sure?',
            text: "This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, remove it!',
         }).then((result) => {
            if (result.isConfirmed) {
               $(button).closest('tr').remove();
               toastr.success('Row removed successfully!');
            }
         });
      }

      $(document).ready(function() {
         $('form .spinner-border').hide(); // Hide all loading indicators initially
         $('form button[type="submit"]').prop('disabled', false); // Disable all submit buttons initially
         $('form button[type="submit"]').removeClass('d-none'); // Show all submit buttons initially

         $('.owner-select').select2({
            placeholder: 'Find Name/NIK/Position/Department Employee...',
            allowClear: true,
            minimumInputLength: 2, // User harus mengetik minimal 2 huruf sebelum pencarian dimulai
            ajax: {
               url: "{{ route('it_asset.owners') }}",
               dataType: 'json',
               delay: 250, // Menunda request (debounce) agar tidak memberatkan server
               data: function (params) {
                     return {
                        search: params.term // Parameter yang dikirim ke Controller
                     };
               },
               processResults: function (data) {
                     return {
                        results: data // Select2 mengharapkan format { results: [{id: 1, text: 'abc'}] }
                     };
               },
               cache: true
            }
         });

         // single function to proccess update or insert
         const upsert = async function(rows, actionUrl, actionType) {
            return await $.ajax({
               url: actionUrl + "?type=" + actionType,
               method: 'POST',
               processData: false,
               contentType: "application/json",
               data: JSON.stringify({
                  _token: $('input[name="_token"]').val(),
                  it_assets: rows
               }),
            });
         }

         const forms = $('form.upsert-form')
         
         forms.each(function() {
            $(this).submit(function(e) {
               e.preventDefault() 

               // const formData = new FormData(this)

               const role = $(this).data('role')

               if (role == 'existingItAssets') {
                  Swal.fire({
                     title: 'Are you sure?',
                     text: "You won't be able to revert this!. This wouldn't create any new data.",
                     icon: 'warning',
                     showCancelButton: true,
                     confirmButtonText: 'Yes, update it!',
                     showLoaderOnConfirm: true,
                     preConfirm: () => {
                        const rows = [];
                        $(this).find('.asset-row').each(function() {
                           const row = {
                              'asset_code': $(this).find('.asset-code').val(),
                              'brand': $(this).find('.asset-brand').val(),
                              'pic': $(this).find('.asset-pic').val(),
                              'asset_type_id': $(this).find('.asset-type').val(),
                              'year_registered': $(this).find('.asset-year-registered').val(),
                              'price': $(this).find('.asset-price').val(),
                              'status': $(this).find('.asset-status').val(),
                              'specification': $(this).find('.asset-specification').val(),
                              'software': $(this).find('.asset-software').val(),
                           };
                           rows.push(row);
                        });

                        return upsert(rows, $(this).attr('action'), 'update')
                           .then(result => {
                              if(result.status == 'success') {
                                 toastr.success(role == "existingItAssets"? 'Update complete!' : 'Insert complete')
                                 $(this).hide()
                              } else {
                                 Swal.fire({
                                    icon: 'error',
                                    title: 'Failed to update existing IT Assets',
                                    text: result.message || 'An error occurred while updating the IT Assets.'
                                 })
                              }
                           })
                           .catch(error => {
                              Swal.fire({
                                 icon: 'error',
                                 title: 'Failed to update existing IT Assets',
                                 text: error.responseJSON?.message || 'An error occurred while updating the IT Assets.'
                              })
                           })
                     }
                  })
               } else if (role == 'newItAssets') {
                  Swal.fire({
                     title: 'Are you sure?',
                     text: "You are about to create new assets!. This wouldn't affect already existing data.",
                     icon: 'warning',
                     showCancelButton: true,
                     confirmButtonText: 'Yes, create it!',
                     showLoaderOnConfirm: true,
                     preConfirm: () => {
                        const rows = [];
                        $(this).find('.asset-row').each(function() {
                           const row = {
                              'asset_code': $(this).find('.asset-code').val(),
                              'brand': $(this).find('.asset-brand').val(),
                              'pic': $(this).find('.asset-pic').val(),
                              'asset_type_id': $(this).find('.asset-type').val(),
                              'year_registered': $(this).find('.asset-year-registered').val(),
                              'price': $(this).find('.asset-price').val(),
                              'status': $(this).find('.asset-status').val(),
                              'specification': $(this).find('.asset-specification').val(),
                              'software': $(this).find('.asset-software').val(),
                           };
                           rows.push(row);
                        });

                        return upsert(rows, $(this).attr('action'), 'insert')
                           .then(result => {
                              if(result.status == 'success') {
                                 toastr.success(role == "existingItAssets"? 'Update complete!' : 'Insert complete')
                                 $(this).hide()
                              } else {
                                 Swal.fire({
                                    icon: 'error',
                                    title: 'Failed to create new IT Assets',
                                    text: result.message || 'An error occurred while creating the IT Assets.'
                                 })
                              }
                           })
                           .catch(error => {
                              console.log(error);
                              
                              Swal.fire({
                                 icon: 'error',
                                 title: 'Failed to create new IT Assets',
                                 text: error.responseJSON?.message || 'An error occurred while creating the IT Assets.'
                              })
                           })
                     }
                  })
                  // .then(async (result) => {
                  //    if (result.isConfirmed) {
                  //       let result = await upsert(formData, $(this).attr('action'), 'insert')

                  //       if(result.status == 'success') {
                  //          toastr.success(role == "existingItAssets"? 'Insert complete!' : 'Insert complete')
                  //          $(this).hide()
                  //       }
                  //    }
                  // });
               }
            });
         });
      })
      // ------------- Submit Handler -------------
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