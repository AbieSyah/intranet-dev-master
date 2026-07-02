@extends('layouts.master')

@section('link')
   <meta name="csrf-token" content="{{ csrf_token() }}">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css"/>
   <link href="{{ url('') }}/assets/libs/Datatables/DataTables-1.13.1/css/dataTables.bootstrap5.css" rel="stylesheet" type="text/css" />
   <link href="{{ url('') }}/assets/libs/Datatables/Buttons-2.3.3/css/buttons.dataTables.css" rel="stylesheet" type="text/css" />
   <link href="{{ url('') }}/assets/libs/Datatables/Responsive-2.4.0/css/responsive.bootstrap.css" rel="stylesheet" type="text/css" />
   <link rel="stylesheet" href="{{ url('') }}/assets/css/monthSelectPlugin.css">
@endsection

@section('content')
   <div class="col">
      <div class="page-title-box d-sm-flex align-items-center justify-content-between">
         <h4 class="mb-sm-0">Asset Maintenances</h4>

         <div class="page-title-right">
            <ol class="breadcrumb m-0">
               <li class="breadcrumb-item"><a href="javascript: void(0);">IT Asset</a></li>
               <li class="breadcrumb-item active">Maintenances</li>
            </ol>
         </div>
      </div>

      {{-- <div class="row container-fluid">
         <div class="col-md-4 col-lg-3">
            <div class="card border-0 shadow-sm bg-primary p-3">
               <div class="d-flex align-items-center">
                  <i class="ri-ticket-2-line fs-1 text-white opacity-50"></i>
                  <div class="ms-3">
                     <h6 class="mb-0 text-white">Ongoing</h6>
                     <h3 class="mb-0 text-white">{{ $ongoingMaintenanceCount }}</h3>
                  </div>
               </div>
            </div>
         </div>
         <div class="col-md-4 col-lg-3">
            <div class="card border-0 shadow-sm bg-success p-3">
               <div class="d-flex align-items-center">
                  <i class="ri-ticket-2-line fs-1 text-white opacity-50"></i>
                  <div class="ms-3">
                     <h6 class="mb-0 text-white">Passed</h6>
                     <h3 class="mb-0 text-white">{{ $passedMaintenanceCount }}</h3>
                  </div>
               </div>
            </div>
         </div>
      </div> --}}

      <div class="card">
         <div class="card-header d-flex justify-content-between">
            <button class="btn btn-primary" id="create-maintenance-btn" data-bs-toggle="modal" data-bs-target="#maintenance-modal">
               + Add Maintenance
            </button>
            <a href="{{ route('it_asset.index') }}" class="btn btn-primary btn-label waves-effect waves-light"><i class="ri-arrow-left-fill label-icon align-middle fs-16 me-2"></i> Back</a>
         </div>
         <div class="card-body">
            <div class="mb-3 d-flex gap-2">
               <input type="text" class="form-control" style="width: 300px" name="filter_date" placeholder="Filter maintenance date">
               <button class="btn btn-outline-danger" type="button" id="reset-filter-btn">Reset</button>
            </div>

            <table class="table table-hover align-middle w-100" id="table_asset_maintenance">
               <thead>
                  <tr>
                     <th scope="col" style="text-align:center" width="50px">No</th>
                     <th scope="col">Schedule</th>
                     <th scope="col" style="white-space: nowrap">Asset Code</th>
                     <th scope="col" style="min-width: 200px;">Brand</th>
                     <th scope="col">Owner</th>
                     <th scope="col">Department</th>
                     <th scope="col">Building</th>
                     <th scope="col">Area</th>
                     <th scope="col" style="text-align:center">Action</th>
                  </tr>
               </thead>
               <tbody>
               </tbody>
            </table>
         </div>
      </div>

      <div class="modal fade modal-xl" id="maintenance-modal">
         <div class="modal-dialog">
            <div class="modal-content">
               <div class="modal-header">
                  <h5 class="modal-title" id="maintenance-modal-label">Maintenance Schedule</h5>
               </div>
               <div class="modal-body">
                  <x-maintenance.form modalParent="maintenance-modal" mode="create" />
               </div>
            </div>
         </div>
      </div>

      <div class="modal fade" id="maintenance-edit-modal">
         <div class="modal-dialog">
            <div class="modal-content">
               <div class="modal-header">
                  <h5 class="modal-title" id="maintenance-edit-modal-label">Change Maintenance Schedule</h5>
               </div>
               <div class="modal-body">
                  <form action="" method="post" id="maintenance-edit-form">
                     @csrf
                     @method('put')
                     <input type="hidden" name="maintenance_id" id="edit-maintenance-id">
                     <div class="mb-2">
                        <label for="old_date">Old Date</label>
                        <input type="text" class="form-control" id="old_date" placeholder="Select maintenance date" disabled>
                     </div>
                     <div class="mb-2">
                        <label for="new_date">New Date</label>
                        <input type="text" class="form-control date-select" id="new_date" name="new_date" placeholder="Select maintenance date">
                     </div>

                     <div class="text-center">
                        <button type="submit" class="btn btn-primary text-center">Update Maintenance</button>
                     </div>
                  </form>
               </div>
            </div>
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

   <script src="{{ url('') }}/assets/js/monthSelectPlugin.js"></script>
@endsection

@section('javascript')
   <script>
      $(document).ready(function() {
         const filterInput = flatpickr("input[name='filter_date']", {
            plugins: [
               new monthSelectPlugin({
                  shorthand: true, //defaults to false
               })
            ],
            required: true,
         })

         const maintenanceDateSelection = flatpickr(".date-select", {
            plugins: [
               new monthSelectPlugin({
                  shorthand: true, //defaults to false
               })
            ],
            required: true,
         })

         const loadData = (month, year) => {
            $('#table_asset_maintenance').DataTable({
               processing: true,
               responsive: false,
               serverSide: false,
               scrollX: true,
               ajax: {
                  url: "{{ route('maintenance.data') }}",
                  data: {
                     month: month,
                     year: year
                  }
               },
               columns: [
                  {
                     data: null,
                     className: "text-center",
                     render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                     }
                  }, {
                     data: 'formatted_maintenance_date',
                  }, {
                     data: 'asset_code',
                     render: function(data, type, row) {
                        return `<span class="fw-bold text-primary text-nowrap">${data}</span><br>`;
                     }
                  }, {
                     data: 'brand',
                  }, {
                     data: 'owner.fullname',
                  }, {
                     data: 'department',
                     defaultContent: '-',
                  }, {
                     data: 'building',
                     defaultContent: '-',
                  }, {
                     data: 'area',
                     defaultContent: '-',
                  }, {
                     data: null,
                     "className": "text-center",
                     orderable: false,
                     sortable: false,
                     searchable: false,
                     render: function(data, type, row) {
                        // <a data-maintenance-date="`+row.formatted_maintenance_date+`" class="btn btn-success btn-sm view-btn" data-bs-toggle="modal" data-bs-target="#maintenance-modal" title="Detail">
                        //    <i class="ri-eye-line"></i>
                        // </a> 
                        return `
                           <div class="d-flex gap-1 justify-content-center">
                              
                              <button class="btn btn-primary btn-sm edit-btn" data-bs-toggle="modal" data-bs-target="#maintenance-edit-modal" title="Edit" data-formated-date="`+row.formatted_maintenance_date+`" data-maintenance-id="`+row.encrypted_id+`">
                                 <i class="ri-edit-line"></i>
                              </button> 
                              <button class="btn btn-danger btn-sm delete-btn" data-url="`+row.delete_url+`" title="Delete">
                                 <i class="ri-delete-bin-line"></i>
                              </button>
                           </div>
                        `;
                     }
                  },
               ]
            });
         }

         loadData();

         $('input[name="filter_date"]').on('change', function() {
            const selectedDate = $(this).val();
            if (selectedDate) {
               date = new Date(selectedDate);
               month = date.getMonth() + 1; // Karena getMonth() mengembalikan nilai 0-11
               year = date.getFullYear();
               $('#table_asset_maintenance').DataTable().destroy();
               loadData(month, year);
            } else {
               $('#table_asset_maintenance').DataTable().destroy();
               loadData();
            }
         });

         $('#reset-filter-btn').on('click', function() {
            filterInput.clear();

            $('#table_asset_maintenance').DataTable().destroy();
            loadData();
         });

         $('#create-maintenance-btn').on('click', function() {
            const event = new CustomEvent('maintenance:create', { 
               bubbles: true // Agar event bisa "naik" ke atas DOM
            });
            document.dispatchEvent(event);
         });

         $(document).on('click', '.edit-btn', function() {
            const maintenanceId = $(this).data('maintenance-id');
            $.ajax({
               url: `/administrator/it-asset/maintenance/${maintenanceId}/edit`,
               method: 'GET',
               success: function(response) {
                  $('#edit-maintenance-id').val(maintenanceId);
                  $('#old_date').val(response.formatted_maintenance_date);
                  $('#new_date').val('');
               },
               error: function() {
                  Swal.fire('Error!', 'An error occurred while fetching maintenance data.', 'error');
               }
            });
         })

         $('#maintenance-edit-form').on('submit', function(e) {
            e.preventDefault();
            const maintenanceId = $('#edit-maintenance-id').val();
            const newDate = $('#new_date').val();

            $.ajax({
               url: `/administrator/it-asset/maintenance/${maintenanceId}`,
               method: 'PUT',
               data: {
                  new_date: newDate,
                  _token: $('meta[name="csrf-token"]').attr('content')
               },
               success: function(response) {
                  if (response.status === 'success') {
                     Swal.fire('Updated!', response.message, 'success');
                     $('#maintenance-edit-modal').modal('hide');
                     $('#table_asset_maintenance').DataTable().ajax.reload();
                     $('#old_date').val(response.formatted_maintenance_date);
                     $('#new_date').val('');
                  } else {
                     Swal.fire('Error!', response.message, 'error');
                  }
               },
               error: function() {
                  Swal.fire('Error!', 'An error occurred while updating maintenance data.', 'error');
               }
            });
         });

         $(document).on('click', '.view-btn', function() {
            const event = new CustomEvent('maintenance:view', { 
               detail: {
                  maintenanceDate: $(this).data('maintenance-date')
               },
               bubbles: true // Agar event bisa "naik" ke atas DOM
            });
            document.dispatchEvent(event);
         });
         

         $(document).on('click', '.delete-btn', function() {
            const url = $(this).data('url');
            // console.log(url);

            Swal.fire({
               title: 'Are you sure?',
               text: "This action cannot be undone.",
               icon: 'warning',
               showCancelButton: true,
               confirmButtonColor: '#d33',
               cancelButtonColor: '#3085d6',
               confirmButtonText: 'Yes, delete it!',
               reverseButtons: true,
               preConfirm: () => {
                  Swal.showLoading();
                  return $.ajax({
                     url: url,
                     method: 'DELETE',
                     headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                  }).then(response => {
                     if (response.status === 'success') {
                        Swal.fire('Deleted!', response.message, 'success');
                        $('#table_asset_maintenance').DataTable().ajax.reload();
                     } else {
                        Swal.fire('Error!', response.message, 'error');
                     }
                  }).catch(() => {
                     Swal.fire('Error!', 'An error occurred while deleting the maintenance schedule.', 'error');
                  });
               }
            })
         });
      })
   </script>
@endsection