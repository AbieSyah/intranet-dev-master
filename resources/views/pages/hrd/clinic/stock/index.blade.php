@extends('layouts.master')
@section('link')
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link href="/assets/libs/Datatables/DataTables-1.13.1/css/dataTables.bootstrap5.css" rel="stylesheet" type="text/css" />
  <link href="/assets/libs/Datatables/Buttons-2.3.3/css/buttons.dataTables.css" rel="stylesheet" type="text/css" />
  <link href="/assets/libs/Datatables/Responsive-2.4.0/css/responsive.bootstrap.css" rel="stylesheet" type="text/css" />
  <!-- Toastr Notifications-->
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endsection

@section('content')

<div class="row">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <h4 class="mb-sm-0">Medicine Stock</h4>

      <div class="page-title-right">
          <ol class="breadcrumb m-0">
              <li class="breadcrumb-item"><a href="javascript: void(0);">Medicine</a></li>
              <li class="breadcrumb-item active">Stock</li>
          </ol>
      </div>

    </div>
  </div>
</div>

<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-header align-items-center d-flex">
      </div><!-- end card header -->
      <div class="card-body">
        <table class="table table-striped bordered" id="table_stock">
          <thead>
            <tr>
              <th scope="col" style="text-align:center">No</th>
              <th scope="col" style="text-align:left">Medicine Name</th>
              <th scope="col" style="text-align:center">Prestock</th>
              <th scope="col" style="text-align:center">In</th>
              <th scope="col" style="text-align:center">Out</th>
              <th scope="col" style="text-align:center">Ending Stock</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <!--end col-->
</div>
<!--end row-->
@endsection

@section('script')
<!-- Datatables -->
<script src="/assets/libs/Datatables/DataTables-1.13.1/js/jquery.dataTables.min.js"></script>
<script src="/assets/libs/Datatables/DataTables-1.13.1/js/dataTables.bootstrap5.min.js"></script>
<script src="/assets/libs/Datatables/Responsive-2.4.0/js/dataTables.responsive.min.js"></script>
<script src="/assets/js/pages/datatables.init.js"></script>
<!-- Toastr Notifications-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
@endsection

@section('javascript')
  <script type="text/javascript">
    $(document).ready(function() {
      let table = $('#table_stock').DataTable({
        stateSave: true,
        responsive: true,
        autoWidth: true,
        processing: true,
        serverSide: true,
        ajax: "{{ route('clinic.stock.index') }}",
        columns: [{
            data: 'DT_RowIndex',
            name: 'DT_RowIndex',
            className: "text-center"
          },
          {
            data: 'drug',
            name: 'drug',
            className: "text-left"
          },
          {
            data: 'prestock',
            name: 'prestock',
            className: "text-center"
          },
          {
            data: 'in',
            name: 'in',
            className: "text-center"
          },
          {
            data: 'out',
            name: 'out',
            className: "text-center"
          },
          {
            data: 'ending',
            name: 'ending',
            className: "text-center"
          }
        ]
      });
    });
  </script>
  <script>
    @if(Session::has('success'))
        toastr.options =
        {
            "closeButton" : true,
            "progressBar" : true,
            "positionClass": "toast-bottom-right"
        }
        toastr.success("{{ session('success') }}");
    @endif

    @if(Session::has('error'))
        toastr.options =
        {
            "closeButton" : true,
            "progressBar" : true,
            "positionClass": "toast-bottom-right"
        }
        toastr.error("{{ session('error') }}");
    @endif
  </script>
@endsection
