@extends('layouts.master')
@section('link')
   <meta name="csrf-token" content="{{ csrf_token() }}">
   <link href="{{ url('') }}/assets/libs/Datatables/DataTables-1.13.1/css/dataTables.bootstrap5.css" rel="stylesheet"
      type="text/css" />
   <link href="{{ url('') }}/assets/libs/Datatables/Buttons-2.3.3/css/buttons.dataTables.css" rel="stylesheet"
      type="text/css" />
   <link href="{{ url('') }}/assets/libs/Datatables/Responsive-2.4.0/css/responsive.bootstrap.css" rel="stylesheet"
      type="text/css" />

   <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

   <!-- Select2-->
   <link rel="stylesheet" href="{{ url('') }}/assets/libs/adminlte/select2/css/4.1.0/select2.min.css">
   <style type="text/css">
      body{
         background: #f7fbf8; 
      }
   </style>
@endsection

@section('content')
   <div class="row">
      <div class="col-12">
         <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Priority Metrics</h4>

            <div class="page-title-right">
               <ol class="breadcrumb m-0">
                  <li class="breadcrumb-item"><a href="javascript: void(0);">Priority Metric</a></li>
                  <li class="breadcrumb-item active">List</li>
               </ol>
            </div>

         </div>
      </div>
   </div>

   <div class="row">

         <div class="card">
            <div class="card-header">
               <div class="d-flex justify-content-between">
                  <div>
                     <button class="btn modal-button btn-primary btn-label waves-effect waves-light"><i class="ri-add-circle-line label-icon align-middle fs-16 me-2"></i> Create New Metric</button>
                  </div>
                  <div>
                     <a href="{{ route('service-management.index') }}" class="btn btn-primary btn-label waves-effect waves-light"><i class="ri-arrow-left-fill label-icon align-middle fs-16 me-2"></i> Back</a>
                  </div>
               </div>
            </div>
            <div class="card-body">
               <ul class="nav nav-pills mb-3">
                  <li class="nav-item" role="presentation">
                     <button class="nav-link filter-button active" data-filter-state = 'all' id="filter-all" type="button">All</button>
                  </li>
                  <li class="nav-item" role="presentation">
                     <button class="nav-link filter-button" data-filter-state = 'impact' id="filter-impact" type="button">Impact</button>
                  </li>
                  <li class="nav-item" role="presentation">
                     <button class="nav-link filter-button" data-filter-state = 'urgency' id="filter-urgency" type="button">Urgency</button>
                  </li>
                  <li class="nav-item" role="presentation">
                     <button class="nav-link filter-button" data-filter-state = 'scope' id="filter-scope" type="button">Scope</button>
                  </li>
               </ul>
               <table class="table table-striped bordered display" id="priority-metric-table">
                  <thead>
                     <tr>
                        <th scope="col">Type</th>
                        <th scope="col">Definition</th>
                        <th scope="col">Description</th>
                        <th scope="col">Score</th>
                        <th scope="col">Actions</th>
                     </tr>
                  </thead>
               </table>
            </div>
         </div>
      </div>
   </div>

   <!-- Modal -->
   <div class="modal fade" id="upsert-modal" tabindex="-1" aria-labelledby="upsert-modal-label" aria-hidden="true">
      <div class="modal-dialog">
         <div class="modal-content">
            <div class="modal-header">
               <h1 class="modal-title fs-5 text-capitalize" id="upsert-modal-label">Add new </h1>
               <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
               <form action="{{ route('priority-metric.upsert') }}" method="post" id="upsert-form">
                  @csrf
                  <input type="hidden" name="is_edit" id="is_edit" value="false">

                  <div class="mb-3">
                     <label class="fw-semibold fs-6 mb-2">Type</label>
                     <select data-placeholder="Select Type" class="form-select" name="type" id="input-type" required>
                        <option value=""></option>
                        <option value="impact">Impact</option>
                        <option value="urgency">Urgency</option>
                        <option value="scope">Scope</option>
                     </select>
                  </div>

                  <div class="mb-3">
                     <label class="fw-semibold fs-6 mb-2">Definition</label>
                     <textarea class="form-control" required name="definition" cols="30" rows="3" maxlength="700" id="input-definition"></textarea>
                  </div>

                  <div class="mb-3">
                     <label class="fw-semibold fs-6 mb-2">Description</label>
                     <textarea class="form-control" required name="description" cols="30" rows="10" maxlength="700" id="input-description"></textarea>
                  </div>

                  <div class="mb-3">
                     <label class="fw-semibold fs-6 mb-2">Score</label>
                     <input type="number" class="form-control" required name="score" min="0" max="100" id="input-score">
                  </div>
               </form>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
               <button type="submit" class="btn btn-primary" form="upsert-form">Save</button>
            </div>
         </div>
      </div>
   </div>
@endsection

@section('script')
   <!-- Datatables -->
   <script src="{{ url('') }}/assets/libs/Datatables/DataTables-1.13.1/js/jquery.dataTables.min.js"></script>
   <script src="{{ url('') }}/assets/libs/Datatables/DataTables-1.13.1/js/dataTables.bootstrap5.min.js"></script>
   <script src="{{ url('') }}/assets/libs/Datatables/Responsive-2.4.0/js/dataTables.responsive.min.js"></script>
   <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
   <script src="{{ url('') }}/assets/js/pages/datatables.init.js"></script>
   <!-- Select2 -->
   <script src="{{ url('') }}/assets/libs/adminlte/select2/js/4.1.0/select2.min.js"></script>
   <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

   <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
@endsection

@section('javascript')
   <script>
      $('.flatpickr').each(function() {
         $(this).flatpickr({
            allowInput: true,
            altInput: false,
            altFormat: "d F, Y",
            dateFormat: "d-M-Y",
         })
      }); 
   </script>

   <script>
      $(document).ready(function() {
         let filterState = 'all';

         // load datatable
         const loadData = function(filterValue) {
            $('#priority-metric-table').DataTable({
               stateSave: true,
               responsive: false,
               autoWidth: false,
               processing: true,
               serverSide: false,
               scrollX: true,
               ajax: {
                  url: "{{ route('priority-metric.data') }}",
                  data: {
                     filter: filterValue
                  },
                  dataSrc: 'data'
               },
               columns: [
                  {
                     data: 'type',
                     render: function(data) {
                           return data.toUpperCase()
                     }
                  }, 
                  {
                     data: 'definition',
                     // render: function(data) {
                     //    return `<div style="max-width: 400px; white-space: pre-wrap;">${data}</div>`
                     // }
                  },
                  {
                     data: 'description',
                     // render: function(data) {
                     //    return `<div style="max-width: 400px; white-space: pre-wrap;">${data}</div>`
                     // }
                  }, 
                  {
                     data: 'score',
                  },
                  {
                     data: null,
                     orderable: false,
                     searchable: false,
                     sortable: false,
                     render: function(data, type, row) {
                        return `
                           <div class="d-flex gap-2">
                              <button type="button" class="btn btn-warning btn-sm edit-btn" 
                                 data-id="${row.encrypted_id}" 
                                 data-type="${row.type}">
                                 <i class="ri-pencil-fill"></i>
                              </button>
                              <button type="button" class="btn btn-danger btn-sm delete-btn" data-id="${row.encrypted_id}">
                                 <i class="ri-delete-bin-line"></i>
                              </button>
                           </div>
                        `
                     }
                  }
                  // {
                  //    data: null,
                  //    render: function(data, type, row) {
                  //       return `<form action="${row.delete_url}" method="post" class="delete-form">
                  //          @csrf
                  //          @method('DELETE')
                  //          <button type="submit" data-toggle="tooltip" title="Delete" data-original-title="Delete" class="btn btn-danger btn-sm delete-btn">
                  //             <i class="ri-delete-bin-line"></i>
                  //          </button>
                  //       </form>`
                  //    }
                  // }
               ],
            });
         }

         loadData(filterState);

         // FORM HANDLER
         let modalState = null

         const modal = new bootstrap.Modal('#upsert-modal')
         const form = $('#upsert-form')
         // const modalButtons = $('.modal-button')
         const modalTitle = $('#upsert-modal-label')
         const inputType = $('input[name = "type"]')
         const inputSelect = $('#input-type')
         const filterButtons = $('.filter-button')
         const dataTable = $('#priority-metric-table')

         filterButtons.each(function() {
            $(this).on('click', function(e) {
               const filterButton = $(this)
               if(filterState !== filterButton.data('filter-state')) {
                  filterState = filterButton.data('filter-state') 
                  filterButtons.each(function() {
                     if ($(this).data('filter-state') !== filterState) {
                        $(this).removeClass('active')
                     } else {
                        $(this).addClass('active')
                     }
                  })
                  dataTable.DataTable().destroy();
                  loadData(filterState)
               }
            })
         })

         $("#upsert-modal").on('hidden.bs.modal', function() {
            form.trigger('reset')
            inputType.val('')
            inputSelect.val('')
            form.attr('action', "{{ route('priority-metric.upsert') }}")
            $('#is_edit').val('false')
         })

         $(document).on('click', '.edit-btn', function() {
            const id = $(this).data('id');
            const type = $(this).data('type');
            
            // Bersihkan error sebelumnya
            $('.is-invalid').removeClass('is-invalid');

            const swalert = Swal.fire({
               title: 'Loading!',
               didOpen: () => {
                  Swal.showLoading()
               }
            });

            $.get(`/administrator/priority-metric/${id}/edit`, function(data) {
               modalTitle.html('Edit ' + type);
               inputType.val(type);
               
               // Ganti URL Form ke Update
               $('#upsert-form').attr('action', `/administrator/priority-metric/upsert/${id}`);
               $('#method-container').html('@method("PUT")');

               // Trigger logika pilihan select berdasarkan kategori
               $('#input-type').val(data.type)
               $('#input-type').prop('disabled', true); // disable type selection on edit to prevent changing type that can cause duplicate entry
               $('#input-description').val(data.description);
               $('#input-definition').val(data.definition);
               $('#input-score').val(data.score);
               $('#input-score').prop('disabled', true);
               $('#is_edit').val('true');

               swalert.close();
               modal.show();
            }).fail(function() {
               swalert.close();
               Swal.fire({
                  title: "Error",
                  text: "Failed to load data",
                  icon: "error",
                  buttonsStyling: false,
                  confirmButtonText: "Ok, got it!",
                  customClass: {
                     confirmButton: "btn btn-primary"
                  }
               });
            });
         });

         // change select option state between categories
         $('.modal-button').on('click', function(e) {
            modalState = $(this).data('modal-state')
            modalTitle.html('Add new '+modalState)
            inputType.val(modalState)
            $('#input-type').prop('disabled', false);
            $('#input-score').prop('disabled', false);


            // Trigger logika pilihan select berdasarkan kategori
            // updateSelectOptions(modalState)

            modal.show()
         })

         function updateSelectOptions(type, selectedValue = null) {
            if (type === 'impact') {
               $('#input-type').val('impact');
            } else if (type === 'urgency') {
               $('#input-type').val('urgency');
            } else if (type === 'scope') {
               $('#input-type').val('scope');
            } else {
               $('#input-type').val('');
            }
         }



         // --------------- upsert handler ---------------
         // create submit handler
         let swalert;
         $('#upsert-form').submit(function(e) {
            e.preventDefault()
            const form = this
            const formData = new FormData(form)
            const isEdit = $('#is_edit').val() === 'true';

            let route = form.action

            const submitData = () => {
               Swal.fire({
                  title: 'Saving...',
                  allowOutsideClick: false,
                  didOpen: () => { Swal.showLoading() }
               });

               $.ajax({
                  url: route,
                  method: 'post',
                  data: formData,
                  processData: false,
                  contentType: false,
                  success: function(response) {
                     if (response.status == 'success') {
                        form.reset();
                        modal.hide();
                        dataTable.DataTable().destroy();
                        loadData(filterState);
                        Swal.close();
                        toastr.success(response.message || 'Priority Metric saved successfully');
                     } else {
                        Swal.fire({
                           title: "Error",
                           text: response.message,
                           icon: "error",
                           confirmButtonText: "Ok, got it!",
                           customClass: { confirmButton: "btn btn-primary" }
                        });
                     }
                  },
                  error: function(xhr) {
                     Swal.fire("Error", `System failure, please try again. Error: ${xhr.responseJSON.message}`, "error");
                  }
               });
            };

            Swal.fire({
               title: 'Are you sure?',
               text: "You won't be able to revert this!",
               icon: 'warning',
               showCancelButton: true,
               confirmButtonText: 'Yes, save it!',
               cancelButtonText: 'No, cancel!',
               reverseButtons: true,
               preConfirm: () => {
                  if (isEdit) {
                     submitData();
                  } else {
                     $.post("{{ route('priority-metric.check-duplicate') }}", {
                        type: formData.get('type'),
                        score: formData.get('score'),
                        _token: $('meta[name="csrf-token"]').attr('content')
                     }).done(function(response) {
                        if (response.exists) {
                           Swal.fire({
                              title: "Duplicate Entry",
                              text: `A priority metric with the same type and score already exists. Description: ${response.data.description}`,
                              icon: "warning",
                              confirmButtonText: "Ok, got it!",
                              customClass: { confirmButton: "btn btn-primary" }
                           });
                        } else {
                           submitData();
                        }
                     });
                  }
                  Swal.showLoading()
               }
            })
         })
         // --------------- end upsert handler ---------------

         $(document).on('click', '.delete-btn', function() {
            const id = $(this).data('id');
            Swal.fire({
               title: 'Are you sure?',
               text: "You won't be able to revert this!",
               icon: 'warning',
               showCancelButton: true,
               confirmButtonText: 'Yes, delete it!',
               cancelButtonText: 'No, cancel!',
               reverseButtons: true,
               preConfirm: () => {
                  Swal.showLoading()
                  $.ajax({
                     url: `/administrator/priority-metric/${id}`,
                     method: 'DELETE',
                     data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                     },
                     success: function(response) {
                        if (response.status == 'success') {
                           dataTable.DataTable().destroy()
                           loadData(filterState)
                           Swal.close()
                           toastr.success(response.message || 'Priority Metric deleted successfully');
                        } else {
                           Swal.fire({
                              title: "Error",
                              text: response.message || 'Failed to delete Priority Metric',
                              icon: "error",
                              buttonsStyling: false,
                              confirmButtonText: "Ok, got it!",
                              customClass: {
                                 confirmButton: "btn btn-primary"
                              }
                           });
                        }
                     }
                  })
               }
            })
         })
      })
   </script>
@endsection
