@extends('layouts.master')

@section('link')
   <meta name="csrf-token" content="{{ csrf_token() }}">
   <link href="{{ url('') }}/assets/libs/Datatables/DataTables-1.13.1/css/dataTables.bootstrap5.css" rel="stylesheet" type="text/css" />
   <link href="{{ url('') }}/assets/libs/Datatables/Buttons-2.3.3/css/buttons.dataTables.css" rel="stylesheet" type="text/css" />
   <link href="{{ url('') }}/assets/libs/Datatables/Responsive-2.4.0/css/responsive.bootstrap.css" rel="stylesheet" type="text/css" />
   <link rel="stylesheet" href="{{  url('') }}/assets/libs/adminlte/select2/css/4.1.0/select2.min.css">
   <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endsection

@section('content')
   <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <h4 class="mb-sm-0">ITSM Priority Management</h4>

      <div class="page-title-right">
         <ol class="breadcrumb m-0">
            <li class="breadcrumb-item"><a href="javascript: void(0);">Service Desk</a></li>
            <li class="breadcrumb-item"><a href="javascript: void(0);">ITSM Priority Management</a></li>
            <li class="breadcrumb-item active">List</li>
         </ol>
      </div>
   </div>
   <div class="container-fluid py-4">
      <div class="card border-0 shadow-sm">
         <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <button type="button" class="btn btn-primary btn-label waves-effect waves-light" onclick="addPriority()">
               <i class="ri-add-fill label-icon align-middle fs-16 me-2"></i> New Priority
            </button>

            <a href="{{ route('service-management.index') }}" class="btn btn-primary btn-label waves-effect waves-light"><i class="ri-arrow-left-fill label-icon align-middle fs-16 me-2"></i> Back</a>
         </div>
         <div class="card-body">
            <table class="table table-striped w-100" id="priorityTable">
               <thead>
                  <tr>
                        <th>Level</th>
                        <th>Min Score</th>
                        <th>Max Score</th>
                        <th>Min SLA (H)</th>
                        <th>Max SLA (H)</th>
                        <th>Label</th>
                        <th class="text-center">Action</th>
                  </tr>
               </thead>
               <tbody>
                  {{-- Kosongkan, akan diisi oleh DataTables AJAX --}}
               </tbody>
            </table>
         </div>
      </div>
   </div>

   {{-- MODAL CREATE & EDIT --}}
   <div class="modal fade" id="priorityModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
         <div class="modal-content border-0">
            <form id="priorityForm">
               @csrf
               <input type="hidden" name="id" id="priority_url">
               <div class="modal-header border-0">
                  <h5 class="modal-title fw-bold" id="modalTitle">Add New Priority</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
               </div>
               <div class="modal-body py-0">
                  <div class="row g-3">
                     <div class="col-12">
                        <label class="form-label small fw-bold">Level Name</label>
                        <select name="level" id="level" class="form-select">
                           <option value="critical">Critical</option>
                           <option value="high">High</option>
                           <option value="medium">Medium</option>
                           <option value="low">Low</option>
                        </select>
                     </div>
                     <div class="col-6">
                        <label class="form-label small fw-bold">Min Score</label>
                        <input type="number" name="min_score" id="min_score" class="form-control" required>
                     </div>
                     <div class="col-6">
                        <label class="form-label small fw-bold">Max Score</label>
                        <input type="number" name="max_score" id="max_score" class="form-control" required>
                     </div>
                     <div class="row align-items-end g-2">
                        <div class="col-5">
                           <label class="form-label small fw-bold">Min SLA (Hours)</label>
                           <input type="number" name="min_sla_hours" id="min_sla_hours" class="form-control" placeholder="0">
                        </div>
                        <div class="col-2">
                           <div class="mb-0">
                              <label class="form-label small fw-bold d-block text-center">Label</label>
                              <select name="sla_label" id="sla_label" class="form-select text-center fw-bold">
                                 <option value="<"><</option>
                                 <option value=">">></option>
                                 <option value="~">~</option>
                                 <option value="-">-</option>
                              </select>
                           </div>
                        </div>
                        <div class="col-5">
                           <label class="form-label small fw-bold">Max SLA (Hours)</label>
                           <input type="number" name="max_sla_hours" id="max_sla_hours" class="form-control" placeholder="0">
                        </div>
                     </div>
                  </div>
               </div>
               <div class="modal-footer border-0">
                  <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                  <button type="submit" class="btn btn-primary" id="btnSave">Save Changes</button>
               </div>
            </form>
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
      let table;

      $(document).ready(function() {
         // 1. Inisialisasi DataTables
         const priorityColorMap = @json($priorityColorMap);

         table = $('#priorityTable').DataTable({
            processing: true,
            serverSide: true, // Gunakan serverSide jika data ribuan, set false jika sedikit
            ajax: "{{ route('priority.data') }}",
            columns: [
               { 
                  data: 'level',
                  type: 'num',
                  render: function(data, type, row) {
                     let color = null;
                     let priority = 'Unassigned';

                     Object.entries(priorityColorMap).forEach(function([key, value]) {
                        if (data == key) {
                           color = value.color;
                           priority = key;
                        }
                     });

                     if (type === 'sort') {
                        return parseInt(data);
                     }

                     return `<span class="badge text-capitalize" style="background-color: ${color || '#3577f1'};">${row.level}</span>`;
                  }
               },
               { data: 'min_score', name: 'min_score' },
               { data: 'max_score', name: 'max_score' },
               { data: 'min_sla_hours', name: 'min_sla_hours', defaultContent: 'N/A' },
               { data: 'max_sla_hours', name: 'max_sla_hours', defaultContent: 'N/A' },
               { data: 'formated_sla', name: 'formated_sla', 
                  render: function(data) { return `<code>${data}</code>`; }
               },
               { 
                  data: null, 
                  orderable: false, 
                  searchable: false, 
                  class: 'text-center',
                  render: function(data, type, row) {
                     return `
                        <button onclick="editPriority('${row.edit_url}')" class="btn btn-sm btn-info text-white"><i class="ri-pencil-line"></i></button>
                        <button onclick="deletePriority('${row.delete_url}')" class="btn btn-sm btn-danger"><i class="ri-delete-bin-line"></i></button>
                     `;
                  }
               }
            ],
            order: [[1, 'asc']] // Urutkan berdasarkan Min Score secara default
         });

         // 2. Select2 Fix for Modal
         $('.select2-modal').select2({
            dropdownParent: $('#priorityModal'),
            width: '100%'
         });
      });

      // --- CRUD FUNCTIONS ---

      function addPriority() {
         $('#priorityForm')[0].reset();
         $('#priority_url').val('');
         $('#modalTitle').text('Add New Priority');
         $('#priorityModal').modal('show');
      }

      function editPriority(url) {
         $.get(url, function(data) {
            $('#priority_url').val(data.edit_url);
            $('#level').val(data.level).trigger('change');
            $('#min_score').val(data.min_score);
            $('#max_score').val(data.max_score);
            $('#min_sla_hours').val(data.min_sla_hours);
            $('#max_sla_hours').val(data.max_sla_hours);
            $('#sla_label').val(data.sla_label);
            $('#modalTitle').text('Edit Priority');
            $('#priorityModal').modal('show');
         });
      }

      $('#priorityForm').on('submit', function(e) {
         e.preventDefault();
         let editUrl = $('#priority_url').val();
         let url = editUrl?? "/priority";
         let type = editUrl? "PUT" : "POST";

         $.ajax({
            url: url,
            type: type,
            data: $(this).serialize(),
            success: function(res) {
               $('#priorityModal').modal('hide');
               toastr.success(res.message);
               table.ajax.reload(); // Reload table tanpa refresh halaman
            },
            error: function(err) {
               toastr.error(err.responseJSON?.message ?? '', "Please check your input.");
            }
         });
      });

      function deletePriority(url) {
         Swal.fire({
            title: 'Delete this priority?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Delete!',
            confirmButtonColor: '#d33'
         }).then((result) => {
            if (result.isConfirmed) {
               $.ajax({
                  url: url,
                  type: 'DELETE',
                  data: { _token: "{{ csrf_token() }}" },
                  success: function(res) {
                     table.ajax.reload();
                     Swal.fire('Deleted!', res.message, 'success');
                  }
               });
            }
         });
      }

      function handleSlaLogic() {
         const label = $('#sla_label').val();
         const minInput = $('#min_sla_hours');
         const maxInput = $('#max_sla_hours');

         // Reset state
         minInput.prop('disabled', false).prop('required', true);
         maxInput.prop('disabled', false).prop('required', true);

         if (label === '<' || label === '~' || label === '>') {
            // Hanya butuh Max. Contoh: < 4 Jam.
            minInput.val(0).prop('disabled', true).prop('required', false);
         } 
         else if (label === '-') {
            // Butuh keduanya (Range).
            minInput.prop('required', true);
            maxInput.prop('required', true);
         }
      }

      // Jalankan saat label berubah
      $('#sla_label').on('change', handleSlaLogic);

      // Jalankan saat modal dibuka (untuk mode Edit agar langsung menyesuaikan)
      $('#priorityModal').on('shown.bs.modal', function () {
         handleSlaLogic();
      });
   </script>
@endsection