@extends('layouts.master')
@section('link')
   <meta name="csrf-token" content="{{ csrf_token() }}">
   <link href="{{ url('') }}/assets/libs/Datatables/DataTables-1.13.1/css/dataTables.bootstrap5.css" rel="stylesheet"
      type="text/css" />
   <link href="{{ url('') }}/assets/libs/Datatables/Buttons-2.3.3/css/buttons.dataTables.css" rel="stylesheet"
      type="text/css" />
   <link href="{{ url('') }}/assets/libs/Datatables/Responsive-2.4.0/css/responsive.bootstrap.css" rel="stylesheet"
      type="text/css" />
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
            <h4 class="mb-sm-0">Employee's Milestone</h4>

            <div class="page-title-right">
               <ol class="breadcrumb m-0">
                  <li class="breadcrumb-item"><a href="javascript: void(0);">Employee</a></li>
                  <li class="breadcrumb-item active">Milestone</li>
               </ol>
            </div>

         </div>
      </div>
   </div>

   <div class="row">

         <div class="card">
            <div class="card-header">
               <div class="align-items-center d-flex justify-content-between mb-2">
                  <h3 class="card-title">Employee {{ $employee->fullname ?? '' }}({{ $employee->nik?? '' }})</h3>  
                  <div class="flex-shrink-0">
                     <a href="{{ route('employee.index') }}" class="btn btn-primary btn-label waves-effect waves-light"><i class="ri-arrow-left-fill label-icon align-middle fs-16 me-2"></i> Back</a>
                  </div>
               </div>
               <div class="">
                  <ul class="nav nav-pills">
                     <li class="nav-item" role="presentation">
                        <button class="nav-link filter-button active" data-filter-state = 'all' id="filter-all" type="button">All</button>
                     </li>
                     <li class="nav-item" role="presentation">
                        <button class="nav-link filter-button" data-filter-state = 'disciplinary' id="filter-disciplinary" type="button">Disciplinary</button>
                     </li>
                     <li class="nav-item" role="presentation">
                        <button class="nav-link filter-button" data-filter-state = 'reward' id="filter-reward" type="button">Reward</button>
                     </li>
                     <li class="nav-item" role="presentation">
                        <button class="nav-link filter-button" data-filter-state = 'career' id="filter-career" type="button">Career</button>
                     </li>
                  </ul>
               </div>
            </div>
            <div class="card-body">
               <table class="table table-striped bordered display" id="milestone-table">
                  <thead>
                     <tr>
                        <th scope="col">Category</th>
                        <th scope="col">Type</th>
                        <th scope="col">Date</th>
                        <th scope="col">Description</th>
                        <th scope="col">Actions</th>
                     </tr>
                  </thead>
               </table>
            </div>
            <div class="card-footer">
               <div class="d-flex justify-content-center gap-3">
                  <!-- Button trigger modal -->
                  <div class="flex-shrink-0">
                     <button data-modal-state="disciplinary" class="btn modal-button btn-danger btn-label waves-effect waves-light"><i class="ri-add-circle-line label-icon align-middle fs-16 me-2"></i> Disciplinary</button>
                     <button data-modal-state="reward" class="btn modal-button btn-secondary btn-label waves-effect waves-light"><i class="ri-add-circle-line label-icon align-middle fs-16 me-2"></i> Reward</button>
                     <button data-modal-state="career" class="btn modal-button btn-success btn-label waves-effect waves-light"><i class="ri-add-circle-line label-icon align-middle fs-16 me-2"></i> Career</button>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>

   <!-- Modal -->
   <div class="modal fade" id="create-modal" tabindex="-1" aria-labelledby="create-modal-label" aria-hidden="true">
      <div class="modal-dialog">
         <div class="modal-content">
            <div class="modal-header">
               <h1 class="modal-title fs-5 text-capitalize" id="create-modal-label">Add New Milestone</h1>
               <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
               <form action="{{ route('employee.milestone.store', encrypt($employee->id)) }}" method="post" id="create-form">
                  @csrf
                  <div id="method-container"></div> <input type="hidden" name="category">
                  <div class="mb-3">
                     <label class="fw-semibold fs-6 mb-2">Category</label>
                     <select data-placeholder="Select Type" class="form-select select2" name="type" id="input-select" required>
                     </select>
                     <div class="invalid-feedback" id="error-type"></div>
                  </div>

                  <div class="mb-3">
                     <label class="fw-semibold fs-6 mb-2">Date</label>
                     <input type="date" class="form-control flatpickr" name="date" id="input-date" required>
                     <div class="invalid-feedback" id="error-date"></div>
                  </div>

                  <div class="mb-3">
                     <label class="fw-semibold fs-6 mb-2">Description</label>
                     <textarea class="form-control" required name="description" id="input-description" cols="30" rows="5" maxlength="700"></textarea>
                     <div class="invalid-feedback" id="error-description"></div>
                  </div>
               </form>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
               <button type="submit" class="btn btn-primary" form="create-form" id="save-btn">Save Changes</button>
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
            $('#milestone-table').DataTable({
               stateSave: true,
               responsive: false,
               autoWidth: false,
               processing: true,
               serverSide: false,
               scrollX: true,
               // dom: 'Bfrtip',
               ajax: {
                  url: "{{ route('employee.milestone.load', encrypt($employee->id)) }}",
                  data: {
                     filter: filterValue
                  },
                  dataSrc: 'data'
               },
               columns: [
                  {
                     data: 'category',
                     render: function(data) {
                           return data.toUpperCase()
                     }
                  }, 
                  {
                     data: 'type',
                     render: function(data) {
                           return (data?? "REWARD").toUpperCase()
                     }
                  }, 
                  {
                     data: 'formated_date'
                  }, 
                  {
                     data: 'description'
                  },
                  {
                     data: null,
                     render: function(data, type, row) {
                        return `
                           <div class="d-flex gap-2">
                              <button type="button" class="btn btn-warning btn-sm edit-btn" 
                                 data-id="${row.encrypted_id}" 
                                 data-category="${row.category}">
                                 <i class="ri-pencil-fill"></i>
                              </button>
                              <button type="button" class="btn btn-danger btn-sm delete-btn" data-id="${row.encrypted_id}">
                                 <i class="ri-delete-bin-line"></i>
                              </button>
                           </div>`;
                     }
                  }
               ],
               order: [
                  [2, 'asc']
               ]
            });
         }

         loadData(filterState);

         // FORM HANDLER
         let modalState = null

         const modal = new bootstrap.Modal('#create-modal')
         const modalButtons = $('.modal-button')
         const modalTitle = $('#create-modal-label')
         const inputCategory = $('input[name = "category"]')
         const inputSelect = $('#input-select')
         const disciplinarySelect = $('#disciplinary-select')
         const filterButtons = $('.filter-button')
         const dataTable = $('#milestone-table')

         const baseStoreUrl = "{{ route('employee.milestone.store', encrypt($employee->id)) }}";

         const setModalVisibility = function() {
            modalButtons.each(function() {
               if (filterState == "all") {
                  $(this).removeClass('d-none')
               } else if (filterState == $(this).data('modal-state')) {
                  $(this).removeClass('d-none')
               } else {
                  $(this).addClass('d-none')
               }
            })
         }

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
                  setModalVisibility()
                  dataTable.DataTable().destroy();
                  loadData(filterState)
               }
            })
         })

         $(document).on('click', '.edit-btn', function() {
            const id = $(this).data('id');
            const category = $(this).data('category');
            
            // Bersihkan error sebelumnya
            $('.is-invalid').removeClass('is-invalid');

            $.get(`/hrd/employees/milestone/${id}/edit`, function(data) {
               modalTitle.html('Edit ' + category);
               inputCategory.val(category);
               
               // Ganti URL Form ke Update
               $('#create-form').attr('action', `/hrd/employees/milestone/${id}/update`);
               $('#method-container').html('@method("PUT")');

               // Trigger logika pilihan select berdasarkan kategori
               updateSelectOptions(category, data.type);

               // Isi field lainnya
               const dateObj = new Date(data.date);
               const formattedDate = dateObj.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }).replace(/\s/g, '-');
               $('#input-date').val(formattedDate);
               $('#input-description').val(data.description);

               modal.show();
            });
         });

         // change select option state between categories
         modalButtons.on('click', function() {
            const state = $(this).data('modal-state');
            
            // Reset Form ke keadaan semula
            $('#create-form')[0].reset();
            $('#create-form').attr('action', baseStoreUrl);
            $('#method-container').empty();
            $('.is-invalid').removeClass('is-invalid');

            modalTitle.html('Add new ' + state);
            inputCategory.val(state);
            updateSelectOptions(state, null);
            
            modal.show();
         });

         // Fungsi pembantu agar tidak duplikasi kode select
         function updateSelectOptions(state, selectedValue) {
            if (state == 'career') {
               inputSelect.html(`
                  <option value="promotion">Promotion</option>
                  <option value="mutation">Mutation</option>
                  <option value="demotion">Demotion</option>
               `).parent().removeClass('d-none');
               inputSelect.prop('disabled', false);
            } else if (state == 'disciplinary') {
               inputSelect.html(`
                  <option value="warning">Warning</option>
                  <option value="sp1">SP1</option>
                  <option value="sp2">SP2</option>
                  <option value="sp3">SP3</option>
               `).parent().removeClass('d-none');
               inputSelect.prop('disabled', false);
            } else {
               inputSelect.parent().addClass('d-none');
               inputSelect.prop('disabled', true);
            }
            
            if(selectedValue) inputSelect.val(selectedValue);
         }

         // create submit handler
         let swalert;
         $('#create-form').submit(function(e) {
            e.preventDefault()
            const form = this
            const formData = new FormData(form)

            Swal.fire({
               title: 'Are you sure?',
               text: "This action cannot be reverted!",
               icon: 'warning',
               showCancelButton: true,
               confirmButtonColor: '#3085d6',
               cancelButtonColor: '#d33',
               confirmButtonText: 'Yes, save it!',
               cancelButtonText: 'Cancel'
            }).then((result) => {
               swalert = Swal.fire({
                  title: 'Loading!',
                  didOpen: () => {
                     Swal.showLoading()
                  }
               });

               $.ajax({
                  url: $(form).attr('action'),
                  method: 'post',
                  data: formData,
                  processData: false,
                  contentType: false,
                  success: function(response) {
                     swalert.hideLoading()
                     if (response.status == 'success') {
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
                        })
                        swalert.then(() => {
                           form.reset()
                           modal.hide()

                           dataTable.DataTable().destroy()
                           loadData(filterState)
                        })
                     } else if(response.status == 'error') {
                        swalert.update({
                           title: "Error",
                           text: response.message,
                           icon: "error",
                           buttonsStyling: false,
                           confirmButtonText: "Ok, got it!",
                           customClass: {
                              popup: 'swal2-noanimation',
                              confirmButton: "btn btn-primary"
                           }
                        })
                     }
                  }
               })
            })
         })


         // Delete Handler
         $(document).on('click', '.delete-btn', function() {
            const id = $(this).data('id');
            const url = `/hrd/employees/milestone/${id}`; // Sesuaikan dengan route Anda

            Swal.fire({
               title: 'Are you sure?',
               text: "This record will be permanently deleted!",
               icon: 'warning',
               showCancelButton: true,
               confirmButtonColor: '#d33',
               cancelButtonColor: '#3085d6',
               confirmButtonText: 'Yes, delete it!',
               showLoaderOnConfirm: true,
               preConfirm: () => {
                     return $.ajax({
                        url: url,
                        type: 'POST',
                        data: {
                           _token: $('meta[name="csrf-token"]').attr('content'),
                           _method: 'DELETE'
                        },
                        dataType: 'json'
                     })
                     .done(function(response) {
                        return response;
                     })
                     .fail(function(xhr) {
                        Swal.showValidationMessage(
                           `Request failed: ${xhr.responseJSON.message || 'Server Error'}`
                        );
                     });
               },
               allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
               if (result.isConfirmed) {
                     Swal.fire({
                        title: 'Deleted!',
                        text: result.value.message,
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                     }).then(() => {
                        // Reload DataTable agar data terbaru muncul
                        $('#milestone-table').DataTable().ajax.reload();
                     });
               }
            });
         });


         // delete handler
         // const deleteButtons = $('.delete-form')

         // deleteButtons.each(function() {
         //    $(this).submit(function(e) {
         //       e.preventDefault()

         //       const form = $(this)
         //       const formData = new FormData(this)
         //       let deleteSwalert
               
         //       deleteSwAlert = swalert.Fire({
         //          title: "Warning",
         //          text: "Are you sure want to delete ",
         //          icon: "warning",
         //          buttonsStyling: false,
         //          confirmButtonText: "Ok, got it!",
         //          customClass: {
         //             popup: 'swal2-noanimation',
         //             confirmButton: "btn btn-primary"
         //          }
         //       })
         //    })
         // })
      })
   </script>
@endsection
