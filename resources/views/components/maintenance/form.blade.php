@props([
   'modalParent' => null,
   'mode' => null, //Optinal, can be "edit", "create", or null(viewOnly)
   'maintenance' => null, //Optional, AssetMaintenance model instance for edit/view mode
   'editBtnClassName' => null, //Optional, additional class for edit button, only used if mode is "view"
])

@php
   if ($mode !== 'view') {
      $assets = App\Models\ITAsset::with('employee.department', 'employee.building', 'employee.position', 'employee.area', 'maintenances')->get();
      $departments = App\Models\Department::get('name');
      $buildings = App\Models\Master\Building::get('nama');
      $areas = App\Models\Area::get('name');

      $assets->each(function($asset) {
         $asset->encrypted_id = encrypt($asset->id);
         $asset->employee->encrypted_id = encrypt($asset->employee->id);
      });
   }
@endphp

@pushOnce('styles')
   <link rel="stylesheet" href="{{ url('') }}/assets/libs/adminlte/select2/css/4.1.0/select2.min.css">
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
   <link rel="stylesheet" href="{{ url('') }}/assets/css/monthSelectPlugin.css">
@endPushOnce

<form method="POST" id="maintenance-form" class="mt-2">
   @csrf

   <div class="row g-3">
      <div class="row">
         <div class="col">
            <label class="fw-bold small">Schedule Maintenance <span class="text-danger">*</span></label>
            <input type="text" name="maintenance_date" id="maintenance-date-selection" class="form-control" required placeholder="Select Month Maintenance" {{ $mode == "view"? 'disabled' : '' }} value="{{ old('maintenance_date', isset($maintenance->maintenance_date) ? $maintenance->maintenance_date->format('F Y') : '') }}">
         </div>
      </div>

      <div class="col-12">
         <hr class="divider">
      </div>

      <div class="col-12">
         <div id="specific-asset-container">
            <label class="form-label fw-bold">Selected Assets</label>
            <table class="table table-striped">
               <thead>
                  <tr>
                     <th>#</th>
                     <th>Asset Code</th>
                     <th>Brand</th>
                     <th>Owner</th>
                     <th>Department</th>
                     <th>Building</th>
                     <th>Area</th>
                     {{-- <th data-bs-toggle="tooltip" title="Maintenance Date" data-bs-trigger="hover" data-bs-content="Can be null">Day</th> --}}
                     @if ($mode !== 'view')
                        <th>Action</th>
                     @endif
                  </tr>
               </thead>
               <tbody id="selected-asset-list">
                  <tr><td colspan="8" class="text-center text-muted">No assets selected</td></tr>
               </tbody>
            </table>
         </div>
         @if ($mode !== 'view')
            <div class="text-center">
               <button type="button" class="btn btn-outline-primary select-assets">Select Assets</button>
            </div>
         @endif
      </div>
   </div>

   <div class="mt-4 pt-3 border-top d-flex justify-content-end" id="maintenance-form-btn">
      <button type="reset" class="btn btn-light me-2">Reset</button>
      <button type="submit" class="btn btn-primary px-4">Save Maintenance Schedule</button>
   </div>
</form>

@if ($mode !== 'view')
   <div class="modal modal-lg fade" id="asset-modal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg shadow-lg">
         <div class="modal-content border-0">
            <div class="modal-header bg-light">
               <h5 class="modal-title">Select Assets</h5>
               <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
               <div class="mb-3">
                  <input type="text" id="search-asset" class="form-control" placeholder="Search assets...">
               </div>
               <div class="row mb-3">
                  <div class="col">
                     <select name="filter-department" id="filter-department" class="form-select" placeholder="Filter by Department">
                        <option value="">All Departments</option>
                        @foreach($departments as $department)
                           <option value="{{ strtolower($department->name) }}">{{ $department->name }}</option>
                        @endforeach
                     </select>
                  </div>
                  <div class="col">
                     <select name="filter-building" id="filter-building" class="form-select" placeholder="Filter by Building">
                        <option value="">All Buildings</option>
                        @foreach($buildings as $building)
                           <option value="{{ strtolower($building->nama) }}">{{ $building->nama }}</option>
                        @endforeach
                     </select>
                  </div>
                  <div class="col">
                     <select name="filter-area" id="filter-area" class="form-select" placeholder="Filter by Area">
                        <option value="">All Areas</option>
                        @foreach($areas as $area)
                           <option value="{{ strtolower($area->name) }}">{{ $area->name }}</option>
                        @endforeach
                     </select>
                  </div>
               </div>
               <div class="table-responsive" style="max-height: 400px;">
                  <table class="table table-hover">
                     <thead class="table-light sticky-top">
                        <tr>
                           <th class="text-center" width="50"></th>
                           <th class="text-center">Asset Code</th>
                           <th class="text-center">Brand</th>
                           <th class="text-center">Name</th>
                           <th class="text-center">Department</th>
                           <th class="text-center">Building</th>
                           <th class="text-center">Area</th>
                        </tr>
                     </thead>
                     <tbody id="asset-list">
                        @foreach($assets as $asset)
                           <tr>
                              <td>
                                 <input 
                                    type="checkbox" 
                                    class="asset-checkbox form-check-input" 
                                    style="pointer-events: none"
                                    value="{{ $asset->encrypted_id }}" 
                                    data-brand="{{ $asset->brand }}" 
                                    data-asset-code="{{ $asset->asset_code }}" 
                                    data-encrypted-employee-id="{{ $asset->employee->encrypted_id }}"
                                    data-employee-name="{{ $asset->employee->fullname }}" 
                                    data-employee-department="{{ $asset->employee->department->name }}" 
                                    data-employee-building="{{ $asset->employee->building?->nama ?? 'N/A' }}"
                                    data-employee-area="{{ $asset->employee->area?->name ?? 'N/A' }}"
                                    data-all-maintenance-date='{{ $asset->all_maintenance_date_json }}'>
                              </td>
                              <td>{{ $asset->asset_code }}</td>
                              <td>{{ $asset->brand }}</td>
                              <td>{{ $asset->employee->fullname }}</td>
                              <td data-column="department">{{ $asset->employee->department->name }}</td>
                              <td data-column="building">{{ $asset->employee->building?->nama ?? 'N/A' }}</td>
                              <td data-column="area">{{ $asset->employee->area?->name ?? 'N/A' }}</td>
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
@endif

@pushOnce('scripts')
   <script src="/assets/libs/adminlte/select2/js/4.1.0/select2.min.js"></script>
   <script src="{{ url('') }}/assets/js/monthSelectPlugin.js"></script>
   <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

   @if ($mode !== "view")
      <script>
         const maintenanceDateSelection = flatpickr("#maintenance-date-selection", {
            plugins: [
               new monthSelectPlugin({
                  shorthand: true, //defaults to false
               })
            ],
            required: true,
         })
         $('.select2').select2({
            theme: 'bootstrap-5',
         });

         $(document).ready(function() {
            const assets = @json($assets);
            let selectedAssets = @json($selectedMaintenanceAssetArray ?? []);
            let filteredAssets = [];
            const modalParent = "{{ $modalParent }}";

            const getAssetsOnSpecificDate = (month, year) => {
               return assets.filter(asset => {
                  return asset.maintenances.some(maintenance => {
                     return maintenance && maintenance.month == month && maintenance.year == year;
                  })
               })
            }

            const updateCheckedAssets = () => {
               $('.asset-checkbox').each(function() {
                  const encryptedId = $(this).val();
                  const isChecked = selectedAssets.some(asset => asset.encryptedId === encryptedId);
                  $(this).prop('checked', isChecked);
               });
            }

            const updateSelectedAssetList = () => {
               const tbody = $('#maintenance-form #selected-asset-list');
               tbody.empty();

               if (selectedAssets.length === 0) {
                  tbody.append('<tr><td colspan="8" class="text-center text-muted">No assets selected</td></tr>');
               } else {
                  selectedAssets.forEach((asset, index) => {
                     tbody.append(`
                        <tr${asset.isExisted? ' class="table-secondary" data-bs-toggle="tooltip" title="' + asset.tooltip + '"': ''}>
                           <td>
                              <input type="hidden" name="assets[${index}][encrypted_id]" value="${asset.encryptedId}">
                              <input type="hidden" name="assets[${index}][encrypted_employee_id]" value="${asset.encryptedEmployeeId}">
                              ${index + 1}
                           </td>
                           <td>${asset.code}</td>
                           <td>${asset.brand}</td>
                           <td>${asset.name}</td>
                           <td>${asset.department}</td>
                           <td>${asset.building}</td>
                           <td>${asset.area}</td>
                           
                           <td>
                              <button type="button" class="btn btn-sm btn-outline-danger btn-remove-asset" data-encrypted-id="${asset.encryptedId}">
                                 <i class="ri-delete-bin-line"></i>
                              </button>
                           </td>
                        </tr$>
                     `);

                     // <td>
                     //    <select name="assets[${index}][day]" class="form-control form-control-sm">
                     //       <option value="">Select day</option>
                     //       ${[...Array(31)].map((_, i) => `
                     //          <option value="${i + 1}" ${asset.maintenanceDay === i + 1 ? 'selected' : ''}>${i + 1}</option>
                     //       `).join('')}
                     //    </select>
                     // </td>
                  });
               }
            };

            const pushSelectedAsset = ({encryptedId, brand, code, encryptedEmployeeId, name, department, building, area, isExisted = false, tooltip = null, date = null, isSelectManual = false, maintenanceDay = null}) => {
               if (!selectedAssets.some(a => a.encryptedId === encryptedId)) {
                  selectedAssets.push({
                     encryptedId: encryptedId,
                     brand: brand,
                     code: code,
                     encryptedEmployeeId: encryptedEmployeeId,
                     name: name,
                     department: department,
                     building: building,
                     area: area,
                     isExisted: isExisted,
                     tooltip: tooltip,
                     date: date,
                     isSelectManual: isSelectManual,
                     maintenanceDay: maintenanceDay,
                  });
               } else {
                  selectedAssets = selectedAssets.map(asset => {
                     if (asset.encryptedId === encryptedId) {
                        if (!asset.isSelectManual) return {
                           ...asset,
                           isExisted: isExisted,
                           tooltip: tooltip,
                           date: date,
                           isSelectManual: isSelectManual,
                           maintenanceDay: maintenanceDay,
                        }
                        else return asset;
                     }
                     return asset;
                  });
               }
            }

            const viewMode = () => {
               $('#maintenance-form input, #maintenance-form select, #maintenance-form textarea').prop('disabled', true);
               $('#maintenance-form .btn-remove-asset').hide();
               $('.select-assets').hide();
               $('#maintenance-form-btn').removeClass('d-flex').addClass('d-none');
               
            }

            const createMode = () => {
               $('#maintenance-form input, #maintenance-form select, #maintenance-form textarea').prop('disabled', false);
               $('#maintenance-form .btn-remove-asset').hide();
               $('.select-assets').show();
               $('#maintenance-form-btn').removeClass('d-none').addClass('d-flex');
            }

            const editMode = () => {
               $('#maintenance-form input, #maintenance-form select, #maintenance-form textarea').prop('disabled', false);
               $('#maintenance-form .btn-remove-asset').show();
               $('.select-assets').show();
               $('#maintenance-form-btn').removeClass('d-none').addClass('d-flex');
            }

            $('.select-assets').on('click', function() {
               if (modalParent) {
                  const modalA = new bootstrap.Modal(document.getElementById(modalParent));
                  modalA.hide();
               }
            
               const modalB = new bootstrap.Modal(document.getElementById('asset-modal'));
               modalB.show();
            });

            $('#save-selection').on('click', function() {
               const checkedItems = $('.asset-checkbox:checked');
               selectedAssets = selectedAssets.filter(asset => !asset.isSelectManual);

               checkedItems.each(function(index) {
                  const encryptedId = $(this).val();
                  const code = $(this).data('asset-code') || '-';
                  const brand = $(this).data('brand') || '-';
                  const encryptedEmployeeId = $(this).data('encrypted-employee-id') || null;
                  const name = $(this).data('employee-name') || '-';
                  const department = $(this).data('employee-department') || '-';
                  const building = $(this).data('employee-building') || '-';
                  const area = $(this).data('employee-area') || '-';
                  selectedAssets.push({
                     encryptedId,
                     code,
                     brand,
                     encryptedEmployeeId,
                     name,
                     department,
                     building,
                     area,
                     isSelectManual: true,
                  });
               });               

               selectedAssets = selectedAssets.filter((asset, index, self) =>
                  index === self.findIndex((a) => a.encryptedId === asset.encryptedId)
               );

               selectedAssets = selectedAssets.filter(asset => {
                  const isChecked = $('.asset-checkbox[value="' + asset.encryptedId + '"]').prop('checked');
                  return isChecked;
               });

               updateSelectedAssetList();

               $('#asset-modal').modal('hide');

               if(selectedAssets.length > 0) {
                  $(".select-assets").text('Edit Selected Assets');
               } else $('.select-assets').text('Select Assets');
            });

            $(document).on('click', '.btn-remove-asset', function() {
               $(this).closest('tr').remove();
               selectedAssets = selectedAssets.filter(asset => asset.encryptedId !== $(this).data('encrypted-id'));
               updateCheckedAssets();
            });

            // Form Reset Button Handler
            $('#maintenance-form').on('reset', function() {
               selectedAssets = [];
               updateSelectedAssetList();
               updateCheckedAssets();
            });

            $('#maintenance-date-selection').on('input', function() {
               const selectedDate = $(this).val();
               const month = new Date(selectedDate).getMonth() + 1
               const year = new Date(selectedDate).getFullYear()
               
               selectedAssets = selectedAssets.filter(asset => asset.isSelectManual);

               const existedAssets = getAssetsOnSpecificDate(month, year);

               existedAssets.forEach(asset => {
                  pushSelectedAsset({
                     encryptedId: asset.encrypted_id,
                     brand: asset.brand,
                     code: asset.asset_code,
                     encryptedEmployeeId: asset.employee.encrypted_id,
                     name: asset.employee.fullname,
                     department: asset.employee.department.name,
                     building: asset.employee.building?.nama ?? 'N/A',
                     area: asset.employee.area?.name ?? 'N/A',
                     isExisted: true,
                     tooltip: `This asset has maintenance scheduled on ${selectedDate}`,
                     date: asset.day,
                     isSelectManual: false,
                     maintenanceDay: asset.maintenances.find(m => m.month == month && m.year == year).day,
                  });
               });
               updateSelectedAssetList();
               updateCheckedAssets();
            });

            // ------------- Form Submit Handler -------------
            @if($mode !== 'view')
               $('#maintenance-form').on('submit', function(e) {
                  e.preventDefault();

                  const formData = $(this).serializeArray();

                  // formData.forEach(item => {
                  //    console.log(`${item.name}: ${item.value}`);
                  // });
                  
                  swal.fire({
                     title: 'Confirm Save',
                     text: 'Are you sure you want to save this maintenance schedule?',
                     icon: 'warning',
                     showCancelButton: true,
                     confirmButtonText: 'Yes, Save it!',
                     cancelButtonText: 'Cancel',
                     reverseButtons: true,
                     preConfirm: () => {
                        return $.ajax({
                           url: '{{ $mode == "edit" ? route('maintenance.update', encrypt($maintenance->id)) : route('maintenance.store') }}',
                           method: '{{ $mode == "edit" ? 'PUT' : 'POST' }}',
                           data: formData,
                           success: function(response) {
                              swal.fire({
                                 title: 'Success',
                                 text: response.message,
                                 icon: 'success',
                                 showConfirmButton: false,
                              }).then(() => {
                                 location.reload();
                              });
                           },
                           error: function(xhr) {
                              let errorMsg = 'An error occurred. Please try again.';
                              if (xhr.responseJSON && xhr.responseJSON.message) {
                                 errorMsg = xhr.responseJSON.message;
                              }
                              swal.fire({
                                 title: 'Error',
                                 text: errorMsg,
                                 icon: 'error',
                              });
                           }
                        });
                     }
                  })
               });
            @endif
            // ------------- End Form Submit Handler -------------            

            $(document).on('maintenance:create', function() {
               selectedAssets = [];
               maintenanceDateSelection.clear();
               updateSelectedAssetList();
               updateCheckedAssets();
               createMode();
            });

            $(document).on('maintenance:edit', function(e) {
               const maintenanceDate = new Date(e.detail.maintenanceDate);
               const month = maintenanceDate.getMonth() + 1, year = maintenanceDate.getFullYear();

               // console.log(maintenanceDate);
               
               selectedAssets = [];

               getAssetsOnSpecificDate(month, year).forEach(asset => {
                  pushSelectedAsset({
                     encryptedId: asset.encrypted_id,
                     brand: asset.brand,
                     code: asset.asset_code,
                     encryptedEmployeeId: asset.employee.encrypted_id,
                     name: asset.employee.fullname,
                     department: asset.employee.department.name,
                        building: asset.employee.building?.nama ?? 'N/A',
                        area: asset.employee.area?.name ?? 'N/A',
                     isExisted: true,
                     tooltip: `This asset has maintenance scheduled on ${e.detail.maintenanceDate}`,
                     date: asset.day,
                     isSelectManual: false,
                     maintenanceDay: asset.maintenances.find(m => m.month == month && m.year == year).day,
                  });
               });

               maintenanceDateSelection.setDate(maintenanceDate);

               updateSelectedAssetList();
               updateCheckedAssets();
               editMode();
            });

            $(document).on('maintenance:view', function(e) {
               const maintenanceDate = new Date(e.detail.maintenanceDate);
               const month = maintenanceDate.getMonth() + 1, year = maintenanceDate.getFullYear();

               selectedAssets = [];

               getAssetsOnSpecificDate(month, year).forEach(asset => {
                  pushSelectedAsset({
                     encryptedId: asset.encrypted_id,
                     brand: asset.brand,
                     code: asset.asset_code,
                     encryptedEmployeeId: asset.employee.encrypted_id,
                     name: asset.employee.fullname,
                     department: asset.employee.department.name,
                     building: asset.employee.building?.nama ?? 'N/A',
                     area: asset.employee.area?.name ?? 'N/A',
                     isExisted: true,
                     tooltip: `This asset has maintenance scheduled on ${e.detail.maintenanceDate}`,
                     date: asset.day,
                     isSelectManual: false,
                     maintenanceDay: asset.maintenances.find(m => m.month == month && m.year == year).day,
                  });
               });

               maintenanceDateSelection.setDate(maintenanceDate);

               updateSelectedAssetList();
               updateCheckedAssets();

               viewMode();
            });




            // filter and search handler
            const applyFilters = () => {
               const selectedArea = $('#filter-area').val().toLowerCase();
               const selectedBuilding = $('#filter-building').val().toLowerCase();
               const selectedDepartment = $('#filter-department').val().toLowerCase();

               $('#asset-list tr').each(function() {
                  const row = $(this);
                  
                  // Ambil teks dari masing-masing kolom
                  const area = row.find('td[data-column="area"]').text().trim().toLowerCase();
                  const building = row.find('td[data-column="building"]').text().trim().toLowerCase();
                  const department = row.find('td[data-column="department"]').text().trim().toLowerCase();

                  // Logika Pengecekan (Satu baris harus lolos SEMUA kriteria)
                  const matchArea = selectedArea === '' || area === selectedArea || (selectedArea === 'unassigned' && (area === 'n/a' || area === ''));
                  
                  const matchBuilding = selectedBuilding === '' || building === selectedBuilding || (selectedBuilding === 'unassigned' && (building === 'n/a' || building === ''));
                  
                  const matchDepartment = selectedDepartment === '' || department === selectedDepartment || (selectedDepartment === 'unassigned' && (department === 'n/a' || department === ''));

                  // Tampilkan hanya jika lolos semua filter
                  if (matchArea && matchBuilding && matchDepartment) {
                        row.show();
                  } else {
                        row.hide();
                  }
               });
            }

            $("#search-asset").on("keyup", function() {
               var value = $(this).val().toLowerCase();
               $("#asset-list tr").filter(function() {
                  $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
               });
            });

            $('#asset-list tr').on('click', function() {
               let checkbox = $(this).find('.asset-checkbox');
               if (checkbox.prop('disabled')) return;
               checkbox.prop('checked', !checkbox.prop('checked'));
            });

            $('#filter-area, #filter-building, #filter-department').on('change', function() {
               applyFilters();
            });
         });
      </script>
   @endif
@endPushOnce   