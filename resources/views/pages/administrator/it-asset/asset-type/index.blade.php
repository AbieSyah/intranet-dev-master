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
         <form id="formAsset">
            @csrf
            <input type="hidden" name="_method" id="method">
            <input type="hidden" name="id" id="id">
            <div class="modal-content">
               <div class="modal-header">
                  <h5 class="modal-title" id="modalTitle">Form Asset Type</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
               </div>
               <div class="modal-body">
                  <div class="mb-3">
                     <label>Name</label>
                     <input type="text" name="name" id="name" class="form-control" required>
                  </div>
                  <div class="mb-3">
                     <label>Estimated Lifetime (Months)</label>
                     <input type="number" name="estimated_lifespan" id="estimated_lifespan" class="form-control" required>
                     <div id="lifespan-preview" class="form-text text-muted mt-1" style="font-style: italic;">
                        Preview: 0 years, 0 months
                     </div>
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
               <li class="breadcrumb-item"><a href="javascript: void(0);">IT Asset</a></li>
               <li class="breadcrumb-item"><a href="javascript: void(0);">AssetType</a></li>
               <li class="breadcrumb-item active">List</li>
            </ol>
         </div>
      </div>
      
      <div class="card">
         <div class="card-header d-flex justify-content-between">
            <button class="btn btn-primary" onclick="addForm()">+ Add Asset Type</button>
            <a href="{{ route('it_asset.index') }}" class="btn btn-primary btn-label waves-effect waves-light"><i class="ri-arrow-left-fill label-icon align-middle fs-16 me-2"></i> Back</a>
         </div>
         <div class="card-body">
            <div class="col">
               <table id="assetTable" class="table table-striped dt-responsive nowrap w-100">
                  <thead>
                     <tr>
                        <th>Name</th>
                        <th>Lifetime (Months)</th>
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
         serverSide: true,
         ajax: "{{ route('asset-type.data') }}",
         columns: [
            {data: 'name', name: 'name'},
            {data: 'estimated_lifespan', name: 'estimated_lifespan'},
            {
               data: null,
               name: 'action',
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
         $('#formAsset')[0].reset();
         $('#id').val('');
         $('#method').val('POST');
         $('#modalTitle').text('Add Asset Type');
         $('#modalForm').modal('show');
      }

      $('#estimated_lifespan').on('input', function() {
         let totalMonths = $(this).val();
         let previewElement = $('#lifespan-preview');

         if (totalMonths && totalMonths > 0) {
            let years = Math.floor(totalMonths / 12);
            let months = totalMonths % 12;

            let result = "Preview: ";
            if (years > 0) {
               result += years + " year" + (years > 1 ? "s" : "") + ", ";
            }
            result += months + " month" + (months > 1 ? "s" : "");

            previewElement.text(result).removeClass('text-danger').addClass('text-muted');
         } else {
            previewElement.text("Preview: 0 years, 0 months");
         }
      });

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
            const data = await $.get(`/administrator/asset-type/${id}`);

            $('#id').val(data.encrypted_id);
            $('#name').val(data.name);
            $('#estimated_lifespan').val(data.estimated_lifespan);
            $('#method').val('PUT');
            $('#modalTitle').text('Edit Asset Type');
            
            Swal.close(); 
            $('#modalForm').modal('show');
            $('#estimated_lifespan').trigger('input');

         } catch (error) {
            Swal.close();
            toastr.error('Failed to fetch data: ' + (error.responseJSON?.message || error.statusText));
         }
      });

      // Submit Create/Update
      $('#formAsset').on('submit', function(e) {
         e.preventDefault();
         let encryptedId = $('#id').val();
         let url = encryptedId ? "/administrator/asset-type/" + encryptedId : "{{ route('asset-type.store') }}";
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
            error: function(xhr) { toastr.error(xhr.responseJSON?.message || xhr.statusText, 'Something went wrong!',); }
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
                  url: "/administrator/asset-type/" + id,
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