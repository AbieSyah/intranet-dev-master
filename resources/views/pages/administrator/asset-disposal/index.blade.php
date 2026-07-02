@extends('layouts.master')

@section('link')
   <meta name="csrf-token" content="{{ csrf_token() }}">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css"/>
   <link href="{{ url('') }}/assets/libs/Datatables/DataTables-1.13.1/css/dataTables.bootstrap5.css" rel="stylesheet" type="text/css" />
   <link href="{{ url('') }}/assets/libs/Datatables/Buttons-2.3.3/css/buttons.dataTables.css" rel="stylesheet" type="text/css" />
   <link href="{{ url('') }}/assets/libs/Datatables/Responsive-2.4.0/css/responsive.bootstrap.css" rel="stylesheet" type="text/css" />
   <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endsection

@section('content') 
   <div class="row">
      <div class="col-12">
         <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">List Disposal IT Asset</h4>

            <div class="page-title-right">
               <ol class="breadcrumb m-0">
                  <li class="breadcrumb-item"><a href="javascript: void(0);">IT Asset</a></li>
                  <li class="breadcrumb-item"><a href="javascript: void(0);">Disposal</a></li>
                  <li class="breadcrumb-item active">List</li>
               </ol>
            </div>

         </div>
      </div>
   </div>

   <div class="row mt-4 mb-2">   
      @if ($approvalList->count())         
         <div class="col-md-6 col-12">
            <div class="card border-start border-primary border-4 shadow-sm">
               <div class="card-body">
                  <div class="d-flex mb-3">
                     <div class="flex-shrink-0">
                        <i class="ri-file-search-fill text-primary fs-1"></i>
                     </div>
                     <div class="flex-grow-1 ms-3">
                        <h5 class="mb-1 fw-bold">Approval Queue: Action Pending</h5>
                        <p class="mb-0 text-muted small">You have 1 new disposal request awaiting your technical evaluation.</p>
                     </div>
                  </div>
                  <div class="d-flex flex-column gap-2">
                     @foreach ($approvalList as $approval)
                        <x-asset-disposal.approval-card 
                           transaction_number="{{ $approval->transaction_number }}" 
                           requester='{{ $approval->requester->fullname }}' 
                           division="{{ $approval->requester->department?->name?? 'N/A' }}" 
                           url="{{ route('asset-disposal.review', [encrypt($approval->id)]) }}" 
                           days="{{ $approval->updated_at->diffForHumans() }}" />
                     @endforeach
                  </div>
               </div>
            </div>
         </div>
      @endif

      @if ($revisionList->count())         
         <div class="col-md-6 col-12">
            <div class="card border-start border-warning border-4 shadow-sm">
               <div class="card-body">
                  <div class="d-flex mb-3">
                     <div class="flex-shrink">
                        <i class="ri-error-warning-fill text-warning fs-1"></i>
                     </div>
                     <div class="flex-grow-1 w-100 ms-3">
                        <h5 class="mb-1 fw-bold">Action Required: Requests Needing Revision</h5>
                        <p class="mb-0 text-muted small">You have 1 request that requires updates based on evaluator feedback.</p>
                     </div>
                  </div>

                  <div class="d-flex flex-column gap-2">
                     @foreach ($revisionList as $revision)
                        <x-asset-disposal.revision-card 
                           transaction_number="{{ $revision->transaction_number }}" 
                           comments="{{ Str::limit($revision->logs->sortByDesc('id')->first()->comments, 180) }}"  
                           days="{{ $revision->updated_at->diffForHumans() }}"
                           url="{{ route('asset-disposal.revision', encrypt($revision->id)) }}"/>
                     @endforeach
                  </div>
               </div>
            </div>
         </div>
      @endif
   </div>

   <div class="card">
      <div class="d-flex justify-content-between">
         <ul class="nav nav-tabs nav-tabs-custom nav-success" role="tablist">
            <li class="nav-item">
               <a class="nav-link py-3 active" id="tab-rule" data-bs-toggle="tab"
                  href="#my-disposal-request" role="tab">
                  <i class="ri-survey-line me-1 align-bottom"></i> My Disposal Request
               </a>
            </li>
            <li class="nav-item">
               <a class="nav-link py-3" id="tab-benefit" data-bs-toggle="tab" href="#all-disposal-request"
                  role="tab">
                  <i class="ri-survey-line me-1 align-bottom"></i> All Disposal Request
               </a>
            </li>
         </ul>
         <div class="d-flex align-items-center justify-content-center pe-3 gap-2">
            <a href="{{ route('it_asset.index') }}" class="btn btn-primary btn-label waves-effect waves-light"><i class="ri-arrow-left-fill label-icon align-middle fs-16 me-2"></i> Back</a>
            <a class="btn btn-outline-secondary" href="{{ route('it_asset.disposed') }}">Asset Disposed</a>
         </div>
      </div>
      <div class="tab-content">
         <div class="tab-pane active" id="my-disposal-request" role="tabpanel">
            <div class="card-body">
               <table class="table table-striped" id="my-table-asset-disposal">
                  <thead>
                     <tr>
                        <th>Transaction No.</th>
                        <th>Submitted At</th>
                        <th>Total Sale Price</th>
                        <th>Status</th>
                        <th>Current Approver</th>
                        <th>Action</th>
                     </tr>
                  </thead>
                  <tbody>
                  </tbody>
               </table>
            </div>
         </div>
         <div class="tab-pane" id="all-disposal-request" role="tabpanel">
            <div class="card-body">
               <table class="table table-striped" id="table-asset-disposal">
                  <thead>
                     <tr>
                        <th>Transaction No.</th>
                        <th>Submitted At</th>
                        <th>Requester</th>
                        <th>Total Sale Price</th>
                        <th>Status</th>
                        <th>Current Approver</th>
                        <th>Action</th>
                     </tr>
                  </thead>
                  <tbody>
                  </tbody>
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
      function loadDataTable(tableName, my = false) {
         let columns = [
            {
               data: 'transaction_number',
               className: "text-center fw-bold"
            }, 
            {
               data: 'created_at',
               className: "text-center",
               render: function(data) {
                  return flatpickr.formatDate(new Date(data), "d-M-Y H:i");
               }
            }
         ];

         if (!my) {
            columns.push({
               data: 'requester.fullname',
               defaultContent: "N/A"
            });
         }

         columns.push(
            {
               data: 'total_price',
               render: $.fn.dataTable.render.number(',', '.', 2, 'Rp ')
            }, 
            {
               data: 'current_status',
               className: "text-center",
               render: function(data) {
                  const statuses = {
                     'waiting': '<span class="badge bg-secondary">Pending</span>',
                     'approved': '<span class="badge bg-success">Approved</span>',
                     'rejected': '<span class="badge bg-danger text-light">Rejected</span>',
                     'revision': '<span class="badge bg-warning text-dark">Revision</span>',
                     'revised': '<span class="badge bg-info">Revised</span>',
                     'canceled': '<span class="badge bg-primary">Canceled</span>',
                     'complete': '<span class="badge bg-success">Complete</span>',
                  };
                  return statuses[data] || '<span class="badge bg-dark">Unknown</span>';
               }
            }, 
            {
               data: 'current_approver',
               className: "text-center",
               render: function(data) {
                  return `<small class="text-muted">${data}</small>`;
               }
            }, 
            {
               data: null,
               className: "text-center",
               orderable: false,
               searchable: false,
               render: function(data, type, row) {
                  return `
                     <div class="d-flex gap-1 justify-content-center">
                        ${row.revision_url? `
                           <a href="${row.revision_url}" class="btn btn-warning btn-sm" title="Fix Revision">
                              <i class="ri-edit-box-line"></i>
                           </a>
                        ` : ''}
                        ${row.document_url? `
                           <a href="${row.document_url}" class="btn btn-success btn-sm" title="Document" target="_BLANK">
                              <i class="ri-file-line"></i>
                           </a>
                        ` : ''}
                        <a href="${row.view_url}" class="btn btn-info btn-sm" title="View History">
                           <i class="ri-history-line"></i>
                        </a>
                        ${row.cancel_url? `
                           <form action="${row.cancel_url}" method="POST" style="display:inline;" class="cancel-form">
                              @csrf
                              @method('DELETE')
                              <input type="hidden" name="reason" class="reason-input">
                              <button type="button" class="btn btn-danger btn-sm btn-trigger-swal" title="Delete">
                                    <i class="ri-delete-bin-line"></i>
                              </button>
                           </form>
                        `: ''}
                     </div>
                  `;
               }
            }
         );

         return $(tableName).DataTable({
            processing: true,
            serverSide: true,
            stateSave: true,
            responsive: true,
            autoWidth: false,
            ajax: {
               url: `{{ route('asset-disposal.get-disposal') }}?${my? 'my=true' : ''}`,
            },
            columns: columns,
            order: [3, 'desc']
         })
      }

      $('body').on('click', '.btn-trigger-swal', function(e) {
         e.preventDefault();
         
         const form = $(this).closest('form');
         
         Swal.fire({
            title: 'Cancel Disposal?',
            text: 'Please provide a reason for cancelling this request.',
            input: 'textarea',
            inputPlaceholder: 'Type your reason here...',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Confirm Cancellation',
            preConfirm: (reason) => {
               if (!reason) {
                  Swal.showValidationMessage('The reason field is required');
                  return false;
               }
               return reason;
            }
         }).then((result) => {
            if (result.isConfirmed) {
               // Put the Swal value into the hidden input
               form.find('.reason-input').val(result.value);
               
               // Submit the form normally (causes page reload)
               form.submit();
            }
         });
      });

      loadDataTable('#my-table-asset-disposal', true);
      loadDataTable('#table-asset-disposal');
      
      $('.cancel-form').on('click', '.cancel', function (e) {
         e.preventDefault();
         
         let rowData = table.row($(this).parents('tr')).data();
         let transactionId = rowData.encrypted_id; 

         Swal.fire({
            title: 'Confirm Disposal?',
            text: `Transaction: ${rowData.transaction_number}`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, Submit',
            cancelButtonText: 'Review Again',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                  // This part handles the Ajax submission inside the SweetAlert
                  return $.ajax({
                     url: `/it-asset/disposal/submit/${transactionId}`,
                     method: 'POST',
                     data: {
                        _token: $('meta[name="csrf-token"]').attr('content') // Get CSRF from meta tag
                     }
                  }).catch(error => {
                     Swal.showValidationMessage(`Request failed: ${error.statusText}`);
                  });
            },
            allowOutsideClick: () => !Swal.isLoading()
         }).then((result) => {
            if (result.isConfirmed) {
                  Swal.fire('Success!', 'The request has been submitted.', 'success');
                  table.ajax.reload(null, false); // Reload table without resetting paging
            }
         });
      });

      // Configure Toastr options
      toastr.options = {
         "closeButton": true,
         "progressBar": true,
         "positionClass": "toast-top-right",
         "timeOut": "5000"
      };
      
      @if(Session::has('success'))
         toastr.options = {
            "closeButton" : true,
            "progressBar" : true,
            "positionClass": "toast-bottom-right"
         }
         toastr.success("{{ session('success') }}");
      @endif
      @if(Session::has('error'))
         toastr.options = {
            "closeButton" : true,
            "progressBar" : true,
            "positionClass": "toast-bottom-right"
         }
         toastr.error("{{ session('error') }}");
      @endif
   </script>
@endsection