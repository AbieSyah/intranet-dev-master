@extends('layouts.master')

@section('link')
   <meta name="csrf-token" content="{{ csrf_token() }}">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css"/>
   <link rel="stylesheet" href="{{  url('') }}/assets/libs/adminlte/select2/css/4.1.0/select2.min.css">
   <link href="{{ url('') }}/assets/libs/Datatables/DataTables-1.13.1/css/dataTables.bootstrap5.css" rel="stylesheet" type="text/css" />
   <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endsection

@section('content')
   <div class="col">
      <div class="page-title-box d-sm-flex align-items-center justify-content-between">
         <h4 class="mb-sm-0">Disposed IT Asset</h4>

         <div class="page-title-right">
            <ol class="breadcrumb m-0">
               <li class="breadcrumb-item"><a href="javascript: void(0);">IT Asset</a></li>
               <li class="breadcrumb-item active">Disposed</li>
            </ol>
         </div>
      </div>
      
      <div class="card">
         <div class="card-header d-flex justify-content-end">
            <a href="{{ route('it_asset.index') }}" class="btn btn-primary btn-label waves-effect waves-light"><i class="ri-arrow-left-fill label-icon align-middle fs-16 me-2"></i> Back</a>
         </div>
         <div class="card-body">
            <table class="table table-stripped table-responsive" id="table_it_assets">
               <thead>
                  <tr>
                     <th scope="col" style="text-align:center">Code</th>
                     <th scope="col" style="text-align:center">Brand</th>
                     <th scope="col" style="text-align:center">Asset Type</th>
                     <th scope="col" style="text-align:center">Status</th>
                     <th scope="col" style="text-align:center">Year Registered</th>
                     <th scope="col" style="text-align:center">Age</th>
                     {{-- <th scope="col" style="text-align:center">Person in Charge</th> --}}
                     {{-- <th scope="col" style="text-align:center">Area</th>
                     <th scope="col" style="text-align:center">Department</th> --}}
                     <th scope="col" style="text-align:center">Price</th>
                     <th scope="col" style="text-align:center">Action</th>
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
         let table = $('#table_it_assets').DataTable({
            processing: true,
            serverSide: true,
            stateSave: true, // Remembers search/pagination on refresh
            responsive: true,
            autoWidth: false,
            ajax: {
               url: "{{ route('it_asset.get_assets', ['status'=>'disposed']) }}",
            },
            // 2. The Re-indexing Hook
            // This runs every time the table is drawn (pagination, search, sort)
            // drawCallback: function() {
            //    syncCheckboxes();
            // },
            responsive: {
               details: {
                     // Set the target to the second column (index 1)
                     target: 1, 
                     type: 'column'
               }
            },
            columns: [
               // {
               //    data: null,
               //    orderable: false,
               //    searchable: false,
               //    render: function(data, type, row) {
               //       return `<input type="checkbox" title="${row.disabled_message?? ''}" class="${!row.is_disabled? 'row-checkbox' : ''}" ${row.is_disabled? 'disabled' : ''} value="${!row.is_disabled? row.asset_code : ''}">`;
               //       // ${row.status != 1? 'disabled' : ''}
               //    }
               // }, 
               {
                  data: 'asset_code',
                  className: "text-center"
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
                     } else if (data == 'disposed' || data == 'on_disposal') {
                        return '<span class="badge bg-danger">Disposed</span>';
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
                     const currentDate = new Date(row.updated_at);
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
                  data: 'price',
                  // "className": "text-center",
                  render: $.fn.dataTable.render.number( ',', '.', 2, 'Rp ' )
               }, {
                  data: null,
                  "className": "text-center",
                  orderable: false,
                  sortable: false,
                  searchable: false,
                  render: function(data, type, row) {
                     return `
                        <div class="d-flex gap-1 flex-wrap justify-content-center">
                           <a href="`+row.show_url+`" class="btn btn-success btn-sm" title="Detail">
                              <i class="ri-eye-line"></i>
                           </a> 
                        </div>
                     `;
                  }
               },
            ]
         });
      })
   </script>
@endsection