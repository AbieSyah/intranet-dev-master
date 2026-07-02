@extends('layouts.master')

@section('link')
   <meta name="csrf-token" content="{{ csrf_token() }}">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css"/>
   <link rel="stylesheet" href="{{  url('') }}/assets/libs/adminlte/select2/css/4.1.0/select2.min.css">
   <link href="{{ url('') }}/assets/libs/Datatables/DataTables-1.13.1/css/dataTables.bootstrap5.css" rel="stylesheet" type="text/css" />
   <link href="{{ url('') }}/assets/libs/Datatables/Buttons-2.3.3/css/buttons.dataTables.css" rel="stylesheet" type="text/css" />
   <link href="{{ url('') }}/assets/libs/Datatables/Responsive-2.4.0/css/responsive.bootstrap.css" rel="stylesheet" type="text/css" />
   <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endsection

@section('content')
   <div class="modal fade" id="modalForm" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
         <form id="formCatalog">
            @csrf
            <input type="hidden" name="_method" id="method">
            <input type="hidden" name="id" id="id">
            <div class="modal-content">
               <div class="modal-header">
                  <h5 class="modal-title" id="modalTitle">Form Service Catalog</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
               </div>
               <div class="modal-body">
                  <div class="mb-3">
                     <label>Category</label>
                     <select name="category" id="category" class="form-control">
                        @foreach ($categories as $category)
                           <option value="{{ $category }}">{{ ucfirst(str_replace('_', ' ', $category)) }}</option>
                        @endforeach
                     </select>
                  </div>
                  <div class="mb-3">
                     <label>Catalog</label>
                     <input type="text" name="service_catalog" id="service_catalog" class="form-control" required max="200">
                  </div>
                  <div class="mb-3">
                     <label>Description</label>
                     <textarea name="description" id="description" maxlength="500" class="form-control" required></textarea>
                  </div>
               </div>
               <div class="modal-footer">
                  <button type="submit" class="btn btn-primary">Save Changes</button>
               </div>
            </div>
         </form>
      </div>
   </div>

   <div class="col">
      <div class="page-title-box d-sm-flex align-items-center justify-content-between">
         <h4 class="mb-sm-0">Asset Type</h4>

         <div class="page-title-right">
            <ol class="breadcrumb m-0">
               <li class="breadcrumb-item"><a href="javascript: void(0);">Service Desk</a></li>
               <li class="breadcrumb-item"><a href="javascript: void(0);">Service Catalog</a></li>
               <li class="breadcrumb-item active">List</li>
            </ol>
         </div>
      </div>
      
      <div class="card">
         <div class="card-header d-flex justify-content-between">
            <button class="btn btn-primary" onclick="addForm()">+ Add Service Catalog</button>
            <a href="{{ route('service-management.index') }}" class="btn btn-primary btn-label waves-effect waves-light"><i class="ri-arrow-left-fill label-icon align-middle fs-16 me-2"></i> Back</a>
         </div>
         <div class="card-body">
            <div class="col">
               <table id="assetTable" class="table table-striped dt-responsive nowrap w-100">
                  <thead>
                     <tr>
                        <th style="max-width: 100px">Category</th>
                        <th style="max-width: 300px">Service Catalog</th>
                        <th>Description</th>
                        <th>Action</th>
                     </tr>
                  </thead>
               </table>
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
      let table = $('#assetTable').DataTable({
         processing: true,
         responsive: false,
         serverSide: false,
         scrollX: true,
         ajax: "{{ route('service-catalog.data') }}",
         columns: [
            {data: 'category', name: 'category'},
            {data: 'service_catalog', name: 'service_catalog'},
            {data: 'description', name: 'description'},
            {
               data: null,
               name: 'action',
               searchable: false,
               orderable: false,
               sortable: false,
               render: function(row, type, data) {
                  return `
                     <button class="btn btn-sm btn-warning edit-btn" data-id="${row.encrypted_id}">Edit</button>
                     <button class="btn btn-sm btn-danger delete-btn" data-id="${row.encrypted_id}">Delete</button>
                  `
               }
            }
            // {data: 'action', name: 'action', orderable: false, searchable: false},
         ]
      });

      function addForm() {
         $('#formCatalog')[0].reset();
         $('#id').val('');
         $('#method').val('POST');
         $('#modalTitle').text('Add Service Catalog');
         $('#modalForm').modal('show');
      }

      $(document).on('click', '.edit-btn', async function() {
         let id = $(this).data('id');
         
         Swal.fire({
            title: 'Please Wait',
            text: 'Fetching data...',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                  Swal.showLoading();
            }
         });
         
         try {
            const data = await $.get(`/administrator/service-catalog/${id}`);

            $('#id').val(data.encrypted_id);
            $('#category').val(data.category);
            $('#service_catalog').val(data.service_catalog);
            $('#description').val(data.description);
            $('#method').val('PUT');
            $('#modalTitle').text('Edit Service Catalog');
            
            Swal.close(); 
            $('#modalForm').modal('show');
         } catch (error) {
            Swal.close();
            toastr.error('Failed to fetch data: ' + (error.responseJSON?.message || error.statusText));
         }
      });

      // Submit Create/Update
      $('#formCatalog').on('submit', function(e) {
         e.preventDefault();
         let encryptedId = $('#id').val();
         let url = encryptedId ? "/administrator/service-catalog/" + encryptedId : "{{ route('service-catalog.store') }}";
         let type = encryptedId ? "PUT" : "POST";
         
         // console.log(url);
         // return
         
         $.ajax({
            url: url,
            type: type,
            data: $(this).serialize(),
            success: function(res) {
               $('#modalForm').modal('hide');
               table.ajax.reload();
               toastr.success(res.message);
            },
            error: function() { toastr.error('Something went wrong!'); }
         });
      });

      // Delete Function
      $(document).on('click', '.delete-btn', function() {
         let id = $(this).data('id');
         Swal.fire({
            title: 'Are you sure?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
         }).then((result) => {
            if (result.isConfirmed) {
               $.ajax({
                  url: "/administrator/service-catalog/" + id,
                  type: "DELETE",
                  data: { _token: "{{ csrf_token() }}" },
                  success: function(res) {
                     table.ajax.reload();
                     toastr.success(res.message);
                  }
               });
            }
         });
      });
   </script>
@endsection