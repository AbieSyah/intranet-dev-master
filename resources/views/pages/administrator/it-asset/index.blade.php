@extends('layouts.master')

@section('link')
   <meta name="csrf-token" content="{{ csrf_token() }}">
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css"/>
   <link rel="stylesheet" href="{{  url('') }}/assets/libs/adminlte/select2/css/4.1.0/select2.min.css">
   <link href="{{ url('') }}/assets/libs/Datatables/DataTables-1.13.1/css/dataTables.bootstrap5.css" rel="stylesheet" type="text/css" />
   <link href="{{ url('') }}/assets/libs/Datatables/Buttons-2.3.3/css/buttons.dataTables.css" rel="stylesheet" type="text/css" />
   <link href="{{ url('') }}/assets/libs/Datatables/Responsive-2.4.0/css/responsive.bootstrap.css" rel="stylesheet" type="text/css" />
   <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
   <style>
      .select2-container--default .select2-selection--single {
         height: calc(2.25rem + 2px);
         padding: 0.375rem 0.75rem;
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
@endsection

@section('content') 
   {{-- -------------------- Create Modal -------------------- --}}
   <div class="modal fade modal-lg" tabindex="-1" id="create-modal">
      <div class="modal-dialog">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title">Create IT Asset</h5>
               <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
               <form id="create-form" action="{{ route('it_asset.store') }}" method="post">
                  @csrf
                  <div class="row g-3">
                     <div class="col-md-6">
                        <label class="form-label">Asset Code</label>
                        <input type="text" class="form-control" name="asset_code" placeholder="XX.XX.XX.XXX" required>
                     </div>

                     <div class="col-md-6">
                        <label class="form-label">Brand / Model</label>
                        <input type="text" class="form-control" name="brand" placeholder="e.g. Lenovo ThinkCentre" required>
                     </div>

                     <div class="col-md-6">
                        <label class="form-label">Owner</label>
                        <select class="form-select select2" name="pic" data-dropdown-parent="#create-modal" data-placeholder="Select Owner" required>
                           <option value=""></option>
                           @foreach ($employees as $employee)
                              <option 
                                 value="{{ encrypt($employee->id) }}">
                                    {{ $employee->fullname }} - {{ ($employee->department->name?? "N/A")."(".($employee->position->nama?? 'N/A').")" }}
                              </option>
                           @endforeach
                        </select>
                     </div>

                     <div class="col-md-6">
                        <label class="form-label">Asset Type</label>
                        <select class="form-select select2" data-dropdown-parent="#create-modal" data-placeholder="Select Asset Type" name="asset_type_id" required>
                           <option value=""></option>
                           @foreach ($assetTypes as $assetType)
                              <option value="{{ $assetType->id }}" {{ old('asset_type_id') == $assetType->id? 'selected' : '' }} class="text-capitalize">{{ $assetType->name }}-{{ ' (' . intval((int) $assetType->estimated_lifespan / 12) . ' years)' }}</option>
                           @endforeach
                        </select>
                     </div>

                     <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status" required>
                           @foreach ($statuses as $status)
                              <option value="{{ $status['value'] }}">{{ $status['label'] }}</option>
                           @endforeach
                           {{-- <option value="3">Disposed</option> --}}
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
                           <input type="text" class="form-control" name="price" placeholder="0.00" required>
                        </div>
                     </div>
                  </div>
                  <div class="row mt-2 g-3">
                     <div class="col-md-6">
                        <label class="form-label">Hardware Specification</label>
                        <textarea class="form-control" name="specification" rows="3" placeholder="Enter asset specification..." required></textarea>
                     </div>
                     <div class="col-md-6">
                        <label class="form-label">Software Specification</label>
                        <textarea class="form-control" name="software" rows="3" placeholder="Enter asset software list..."></textarea>
                     </div>
                  </div>
               </form>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
               <button type="submit" class="btn btn-primary" form="create-form">Save changes</button>
            </div>
         </div>
      </div>
   </div>
   {{-- -------------------- End Create Modal -------------------- --}}

   {{-- -------------------- Import Modal -------------------- --}}
   <div class="modal fade" tabindex="-1" id="import-modal">
      <div class="modal-dialog">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title">Import IT Asset</h5>
               <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
               <form id="import-form" action="{{ route('it_asset.preview') }}" method="post" enctype="multipart/form-data">
                  @csrf
                  @method('POST')
                  <div class="row g-3">
                     <div class="col-12">
                        <div>
                           <a href="{{ route('it_asset.download') }}" target="blank" class="btn mb-2 btn-success btn-label waves-effect waves-light">
                              <i class="ri-download-2-fill label-icon align-middle fs-16 me-2"></i>
                              Download Format
                           </a>
                           <input type="file" class="form-control" name="file" id="file"
                                 accept=".xls, .xlsx" required>
                        </div>
                     </div>
                  </div>
               </form>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
               <button type="submit" class="btn btn-primary" form="import-form">Import File</button>
            </div>
         </div>
      </div>
   </div>
   {{-- -------------------- End Import Modal -------------------- --}}

   <div class="row">
      <div class="col-12">
         <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">List IT Asset</h4>

            <div class="page-title-right">
               <ol class="breadcrumb m-0">
                  <li class="breadcrumb-item"><a href="javascript: void(0);">IT Asset</a></li>
                  <li class="breadcrumb-item active">List</li>
               </ol>
            </div>

         </div>
      </div>
   </div>

   <div class="col-lg-12">
      <div class="card">
         <div class="card-header align-items-center d-flex justify-content-between gap-1">
            <div class="d-flex gap-1">
               <button type="button" id="add_user" class="btn btn-primary btn-label waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#create-modal" data-text="Add New User">
                  <i class="ri-add-circle-line label-icon align-middle fs-16 me-2"> </i>Add New Asset
               </button>
               <button data-bs-target="#import-modal" data-bs-toggle="modal" data-text="Import File" class="btn mb-0 btn-primary btn-label waves-effect waves-light">
                  <i class="ri-add-circle-line label-icon align-middle fs-16 me-2"> </i> Import .xls File
               </button>
            </div>
            <div class="d-flex gap-1">
               @canany(['itsm.asset-type.read', 'itsm.asset-disposal.read'])                        
                  <div class="dropdown">
                     <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        More Menu
                     </button>
                     <ul class="dropdown-menu">
                        @can('itsm.asset-type.read')
                           <li><a class="dropdown-item" href="{{ route('asset-type.index') }}">Asset Types</a></li>
                        @endcan
                        @can('itsm.asset-disposal.read')
                           <li><a class="dropdown-item" href="{{ route('asset-disposal.index') }}">Asset Disposals</a></li>
                        @endcan
                        <li><a class="dropdown-item" href="{{ route('maintenance.index') }}">Asset Maintenance</a></li>
                     </ul>
                  </div>
               @endcanany
            </div>
         </div>
         <div class="card-body">
            <div class="d-flex gap-2 mb-2">
               <form action="{{ route('asset-disposal.preview') }}" method="post" id="dispose-form" class="d-none">
                  @csrf
                  <button data-text="Import File" class="btn mb-2 btn-danger btn-label ght" id="dispose-button">
                     <i class="ri-file-reduce-line label-icon align-middle fs-16 me-2"> </i> <span class="text">Dispose</span>
                  </button>
               </form>
               <form action="{{ route('it_asset.print-preview') }}" method="post" id="print-form" class="d-none">
                  @csrf
                  <button data-text="Import File" class="btn mb-2 btn-secondary btn-label ght" id="print-button">
                     <i class="ri-printer-fill label-icon align-middle fs-16 me-2"> </i> <span class="text">Print</span>
                  </button>
               </form>
            </div>
            <div class="d-flex gap-2 mb-2">
               <select id="filter-asset-type" class="form-select select2">
                  <option value="">All Asset Types</option>
                  @foreach ($assetTypes as $item)
                     <option value="{{ $item->id }}">{{ $item->name }}</option>
                  @endforeach
               </select>
               <select id="filter-area" class="form-select select2">
                  <option value="">All Areas</option>
                  @foreach ($area as $item)
                     <option value="{{ $item->name }}">{{ $item->name }}</option>
                  @endforeach
               </select>
               <select id="filter-department" class="form-select select2">
                  <option value="">All Departments</option>
                  @foreach ($departments as $item)
                     <option value="{{ $item->name }}">{{ $item->name }}</option>
                  @endforeach
               </select>
            </div>
            <table class="table table-hover align-middle w-100" id="table_it_asset">
               <thead>
                  <tr>
                     <th style="text-align:center"><input type="checkbox" id="check-all"></th>
                     <th style="text-align:center">Code</th>
                     <th style="text-align:center">Brand</th>
                     <th style="text-align:center">Asset Type</th>
                     <th style="text-align:center">Status</th>
                     <th style="text-align:center">Registered On</th>
                     <th style="text-align:center; min-width: 100px">Age</th>
                     <th style="text-align:center">Owner</th>
                     <th style="text-align:center">Area</th>
                     <th style="text-align:center">Department</th>
                     <th style="text-align:center">Price</th>
                     <th style="text-align:center">Hardware</th>
                     <th style="text-align:center">Software</th>
                     <th style="text-align:center; min-width: 150px">Action</th>
                  </tr>
               </thead>
               <tbody>
               </tbody>
            </table>
         </div>
      </div>
   </div>

@endsection

@section('script')
   <!-- Datatables -->
   <script src="{{ url('') }}/assets/js/pages/datatables.init.js"></script>
   <script src="{{ url('') }}/assets/libs/Datatables/DataTables-1.13.1/js/jquery.dataTables.min.js"></script>
   <script src="{{ url('') }}/assets/libs/Datatables/DataTables-1.13.1/js/dataTables.bootstrap5.min.js"></script>
   <script src="{{ url('') }}/assets/libs/Datatables/Responsive-2.4.0/js/dataTables.responsive.min.js"></script>
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
      // ------------- DataTable -------------
      let selectedAssets = [];

      $(document).ready(function() {
         function loadDataTable(tableId = '#table_it_asset') {
            let table = $(tableId).DataTable({
               processing: true,
               responsive: false,
               serverSide: false,
               scrollX: true,
               stateSave: true,
               ajax: {
                     url: "{{ route('it_asset.get_assets') }}",
               },
               // 2. The Re-indexing Hook
               // This runs every time the table is drawn (pagination, search, sort)
               drawCallback: function() {
                  syncCheckboxes();
               },
               columns: [
                  {
                     data: null,
                     orderable: false,
                     sortable: false,
                     searchable: false,
                     render: function(data, type, row) {
                        return `<input type="checkbox" title="${row.disabled_message?? ''}" class="${!row.is_disabled? 'row-checkbox' : ''}" ${row.is_disabled? 'disabled' : ''} value="${!row.is_disabled? row.asset_code : ''}">`;
                     }
                  }, {
                     data: 'asset_code',
                     className: "text-center",
                     defaultContent: "N/A"
                  }, {
                     data: 'brand',
                  }, {
                     data: 'asset_type.name',
                     "className": "text-center"
                  }, {
                     data: 'status',
                     "className": "text-center",
                     render: function name(data, type, row) {
                        if (data == 'active') {
                           return '<span class="badge bg-success">Active</span>';
                        } else if (data == 'broken') {
                           return '<span class="badge bg-warning">Broken</span>';
                        } else if (data == 'disposed') {
                           return '<span class="badge bg-danger">Disposed</span>';
                        } else if (data == 'on_disposal') {
                           return '<span class="badge bg-danger">On Disposal</span>';
                        } else if (data == 'backup') {
                           return '<span class="badge bg-primary">Backup</span>';
                        } else {
                           return '<span class="badge bg-secondary">Unknown</span>';
                        }
                     }
                  }, {
                     data: 'year_registered',
                     "className": "text-center",
                     render: function(data) {
                        if (data) {
                           const dateObj = new Date(data);
                           return flatpickr.formatDate(dateObj, "Y-M-d");
                        }
                        return 'N/A';
                     }
                  }, {
                     data: null,
                     "className": "text-center",
                     render: function(data, type, row) {
                        const registeredDate = new Date(row.year_registered);
                        const currentDate = new Date();
                        let years = currentDate.getFullYear() - registeredDate.getFullYear();
                        let months = currentDate.getMonth() - registeredDate.getMonth();
                        if (months < 0 || (months === 0 && currentDate.getDate() < registeredDate.getDate())) {
                        years--;
                        months += 12;
                        }
                        if (currentDate.getDate() < registeredDate.getDate()) {
                        months--;
                        }
                        return (years < 0? 0 : years) + ' years<br>' + (years < 0? 0 : months) + ' months';
                     }
                  }, {
                     data: 'employee.fullname',
                     "className": "text-center",
                     defaultContent: "N/A"
                  }, {
                     data: 'employee_area',
                     defaultContent: "N/A"
                  }, {
                     data: 'employee_department',
                     defaultContent: "N/A"
                  }, {
                     data: 'price',
                     // "className": "text-center",
                     render: function(data, type, row) {
                        if (data) {
                           return '<span style="white-space: nowrap;">Rp ' + parseInt(data).toLocaleString('id-ID') + '</span>';
                        }
                        return 'N/A';
                     }
                  }, {
                     data: 'specification',
                     render: function(data, type, row) {
                        return data? `<span class="d-inline-block text-truncate" style="max-width: 200px;" title="${data}">${data}</span>` : 'N/A';
                     }
                  }, {
                     data: 'software',
                     defaultContent: 'N/A',
                     render: function(data, type, row) {
                        return data? `<span class="d-inline-block text-truncate" style="max-width: 200px;" title="${data}">${data}</span>` : 'N/A';
                     }
                  }, {
                     data: null,
                     "className": "text-center",
                     orderable: false,
                     sortable: false,
                     searchable: false,
                     render: function(data, type, row) {
                        return `
                           <div class="d-flex gap-1 flex-wrap justify-content-center">
                              <a href="`+row.show_url+`" class="btn btn-success btn-sm" title="History">
                                 <i class="ri-file-history-line"></i>
                              </a>` +
                              (row.disposal_url !== null? `<a href="`+row.disposal_url+`" class="btn btn-primary btn-sm" title="Disposal Proposal">
                                 <i class="ri-file-search-line"></i>
                              </a>` : '') +
                              `<a href="`+row.edit_url+`" class="btn btn-warning btn-sm" title="Edit">
                                 <i class="ri-edit-line"></i>
                              </a>
                              ${row.status !== 'on_disposal'? `<a href="`+row.movement_url+`" class="btn btn-secondary btn-sm" title="Movement">
                                 <i class="ri-exchange-line"></i>
                              </a>` : ''}
                              ${row.delete_url ? `<button data-url="` + row.delete_url + `" class="btn btn-danger btn-sm delete-btn" title="Delete">
                                 <i class="ri-delete-bin-line"></i>
                              </button>` : ''}
                           </div>
                        `;
                     }
                  },
               ],
               order: [[5, 'desc']],
            });
         }

         $('input[name="price"]').on("input", function() {
            let value = $(this).val();
            value = value.replace(/[^0-9]/g, ''); // Remove non-numeric characters
            if (value) {
               value = parseInt(value).toLocaleString('id-ID'); // Format as Indonesian Rupiah
            }
            $(this).val(value);
            $(this).attr('data-raw-value', value.replace(/[^0-9]/g, '')); // Store raw numeric value in data attribute
         });


         loadDataTable();


         // ------------- Filter Handler -------------
         $('#filter-asset-type, #filter-area, #filter-department').on('change', function() {
            const assetType = $('#filter-asset-type').val();
            const area = $('#filter-area').val();
            const department = $('#filter-department').val();

            let queryParams = [];
            if (assetType) queryParams.push('asset_type=' + encodeURIComponent(assetType));
            if (area) queryParams.push('area=' + encodeURIComponent(area));
            if (department) queryParams.push('department=' + encodeURIComponent(department));

            const newUrl = "{{ route('it_asset.get_assets') }}" + (queryParams.length > 0 ? '?' + queryParams.join('&') : '');
            
            $('#table_it_asset').DataTable().ajax.url(newUrl).load();
         });
         // ------------ End Filter Handler -------------


         const disposeForm = $('#dispose-form')
         const disposeButton = $('#dispose-button')

         const printForm = $('#print-form')
         const printButton = $('#print-button')
         $('#table_it_asset tbody').on('change', '.row-checkbox', function() {
            const id = $(this).val();
            
            if ($(this).is(':checked')) {
               // Add ID if not already in array
               if (!selectedAssets.includes(id)) {
                  selectedAssets.push(id);
               }
            } else {
               // Remove ID from array
               selectedAssets = selectedAssets.filter(item => item !== id);
            }

            
            
            updateCheckAllState();
            formDisplayState()
         });

         disposeForm.submit(function(e) {
            e.preventDefault();
            // Remove old generated inputs (optional but recommended)
            $('.generated-asset').remove();

            selectedAssets.forEach((asset, index) => {
               disposeForm.append(
                  `<input type="hidden" 
                     class="generated-asset"
                     name="asset_codes[${index}][name]" 
                     value="${asset}">`
               );
            });

            // Now submit normally
            this.submit();
         });

         printForm.submit(function(e) {
            e.preventDefault();
            // Remove old generated inputs (optional but recommended)
            $('.generated-asset').remove();

            selectedAssets.forEach((asset, index) => {
               printForm.append(
                  `<input type="hidden" 
                     class="generated-asset"
                     name="asset_codes[${index}]name" 
                     value="${asset}">`
               );
            });

            // Now submit normally
            this.submit();
         });
            
         // End Bulk update 

         // 4. "Select All" Logic
         $('#check-all').on('change', function() {
            const isChecked = $(this).is(':checked');
            
            $('.row-checkbox').each(function() {
               const id = $(this).val();
               // if(!this.disabled) {
                  $(this).prop('checked', isChecked);
                  if (isChecked) {
                     if (!selectedAssets.includes(id)) selectedAssets.push(id);
                  } else {
                     selectedAssets = selectedAssets.filter(item => item !== id);
                  }
               // }
               
            });
            formDisplayState()
         });


         // ----------- delete confirmation -----------
         $(document).on('click', '.delete-btn', function(e) {
            e.preventDefault();
            deleteHandler(e, this);
         });

         function formDisplayState() {
            if (selectedAssets.length > 0) {
               disposeForm.removeClass('d-none')
               printForm.removeClass('d-none')
               
               disposeButton.find('.text').html('Dispose '+selectedAssets.length+' unit')
               printButton.find('.text').html('Print '+selectedAssets.length+' unit')
            } else {
               disposeForm.addClass('d-none')
               printForm.addClass('d-none')
            }
         }

         // 5. Syncing UI with Array Data
         function syncCheckboxes() {
            $('.row-checkbox').each(function() {
               if (selectedAssets.includes($(this).val())) {
                  $(this).prop('checked', true);
               }
            });
            updateCheckAllState();
         }

         // 6. Master Checkbox Visual State
         function updateCheckAllState() {
            const pageCheckboxes = $('.row-checkbox').length;
            const pageChecked = $('.row-checkbox:checked').length;
            
            $('#check-all').prop('checked', pageCheckboxes > 0 && pageCheckboxes === pageChecked);
         }

         
         function deleteHandler(event, element) {
            Swal.fire({
               title: 'Are you sure?',
               text: "This action cannot be undone!",
               icon: 'warning',
               showCancelButton: true,
               confirmButtonColor: '#d33',
               cancelButtonColor: '#3085d6',
               confirmButtonText: 'Yes, delete it!',
               buttonsStyling: false,
               reverseButtons: true,
               customClass: {
                  popup: 'swal2-noanimation',
                  confirmButton: "btn btn-danger ms-2",
                  cancelButton: "btn btn-secondary"
               },
               preConfirm: () => {
                  return new Promise((resolve) => {
                     $.ajax({
                        url: $(element).data('url'),
                        method: 'DELETE',
                        data: {
                           _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                           if (response.status == 'success') {
                              Swal.fire({
                                 icon: 'success',
                                 title: "Deleted!",
                                 text: response.message,
                                 confirmButtonText: "Close",
                              }).then(() => {
                                 table.ajax.reload(null, false); // Reload datatable without resetting pagination
                              });
                           } else {
                              Swal.fire({
                                 icon: 'error',
                                 title: "Error",
                                 text: response.message || 'An error occurred while processing your request.',
                                 confirmButtonText: "Ok, got it!",
                              });
                           }
                        },
                        error: function(xhr, status, error) {
                           Swal.fire({
                              icon: 'error',
                              title: "Error",
                              text: xhr.responseJSON.message || 'An error occurred while processing your request.',
                              buttonsStyling: false,
                              confirmButtonText: "Ok, got it!",
                              customClass: {
                                 popup: 'swal2-noanimation',
                                 confirmButton: "btn btn-primary"
                              }
                           });
                        }
                     });
                  });
               }
            });
         }
      });
      // ------------- DataTable -------------
   

      // ----------------- create form handler -----------------
      $('#create-form').submit(function(e) {
         let swal
         e.preventDefault()

         swal = Swal.fire({
            title: 'Loading!',
            didOpen: () => {
               Swal.showLoading()
            }
         })

         const formData = new FormData(this)

         formData.entries().forEach(element => {
            const [key, value] = element;
            
            if (key.includes('price')) {
               const cleanNumber = value.replace(/[^0-9]/g, ''); 
               formData.set(key, cleanNumber);
            }
         });

         $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            showLoaderOnConfirm: true,
            success: function(response) {
               swal.hideLoading()
               if (response.status == 'success') {
                  swal.update({
                     icon: 'success',
                     title: "Success",
                     text: response.message,
                     buttonsStyling: false,
                     confirmButtonText: "Close",
                     customClass: {
                        popup: 'swal2-noanimation',
                        confirmButton: "btn btn-primary"
                     }
                  })
                  swal.then(function() {
                     window.location.href = response.redirect;
                  })
               } else if(response.status == 'info') {
                  swal.update({
                     icon: 'info',
                     title: "Info",
                     text: response.message,
                     buttonsStyling: false,
                     confirmButtonText: "Ok, got it!",
                     customClass: {
                        popup: 'swal2-noanimation',
                        confirmButton: "btn btn-primary"
                     }
                  })
               } 
            },
            error: function(xhr, status, error) {
               swal.hideLoading();
               
               swal.update({
                  icon: 'error',
                  title: "Error",
                  text: error || 'An error occurred while processing your request.',
                  buttonsStyling: false,
                  confirmButtonText: "Ok, got it!",
                  customClass: {
                     popup: 'swal2-noanimation',
                     confirmButton: "btn btn-primary"
                  }
               })
            }
         })
      })
      // ----------------- end create form handler -----------------
   </script>

   {{-- import form handler --}}
   <script>
      $(document).ready(function() {
         // const importModal = $('#import-modal');
         // const bsModal = new bootstrap.Modal(importModal[0]); // Ambil elemen DOM asli

         // importModal.on('hidden.bs.modal', function() {
         //    $('#import-form')[0].reset()
         // })

         // $('#import-xls').on('change', function(e) {
         //    const file = e.target.files[0];
            
         //    if (file) {
         //       const fileName = file.name;
         //       importModal.find('.modal-body').text('Import file ' + fileName);
         //       bsModal.show();
         //    }
         // });

         // $('#import-form').submit(function(e) {
            // e.preventDefault()
            // const formData = new FormData(this)
            // console.log(formData);

            // $.ajax({
            //    url: $(this).attr('action'),
            //    method: 'POST',
            //    data: formData,
            //    processData: false,
            //    contentType: false,
            //    success: function(response) {
            //       console.log(response);
            //    },
            //    error: function(response) {
            //       console.log(response);
            //    }
            // })
         // })
      });
   </script>
   {{-- end import form handler --}}
   
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