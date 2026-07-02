@extends('layouts.master')
@section('link')
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link href="/assets/libs/Datatables/DataTables-1.13.1/css/dataTables.bootstrap5.css" rel="stylesheet" type="text/css" />
  <link href="/assets/libs/Datatables/Buttons-2.3.3/css/buttons.dataTables.css" rel="stylesheet" type="text/css" />
  <link href="/assets/libs/Datatables/Responsive-2.4.0/css/responsive.bootstrap.css" rel="stylesheet" type="text/css" />
  <!-- Select2-->
  <link rel="stylesheet" href="/assets/libs/adminlte/select2/css/4.1.0/select2.min.css">
  <!-- Toastr Notifications-->
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endsection

@section('content')
<div class="row">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <h4 class="mb-sm-0">News and Event</h4>

      <div class="page-title-right">
          <ol class="breadcrumb m-0">
              <li class="breadcrumb-item"><a href="javascript: void(0);">News and Event</a></li>
              <li class="breadcrumb-item active">index</li>
          </ol>
      </div>

    </div>
  </div>
</div>
<!--end row-->

<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-header align-items-center d-flex justify-content-between">
        <div class="col-md-2 p-2">
        </div>
        <div class="col-md-7">
        </div>
        <div class="col-md-3">
          @can('hrd.news-and-event.create')
          <a href="{{ route('news-and-event.form') }}" class="float-end btn btn-primary btn-label waves-effect waves-light" data-text="Add News or Event"><i class="ri-add-circle-line label-icon align-middle fs-16 me-2"> </i>Add News or Event</a>  
          @endcan  
        </div>        
      </div><!-- end card header -->
      <div class="card-body">
        <table class="table table-striped bordered" id="table">
          <thead>
            <tr>
              <th scope="col">NO</th>
              <th scope="col">TUMBNAIL</th>
              <th scope="col">JUDUL</th>
              <th scope="col">TANGGAL</th>
              <th scope="col">STATUS</th>
              <th scope="col">ACTION</th>
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
<!--Modal delete-->
<div id="modal" class="modal fade flip" tabindex="-1" aria-labelledby="flipModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-top">
        <div class="modal-content">
            <form class="form" action="{{ route('news-and-event.destroy') }}" method="post">
              @csrf
              @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Delete News and Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-5">
                    <lord-icon
                        src="https://cdn.lordicon.com/gsqxdxog.json"
                        trigger="loop"
                        style="width:120px;height:120px">
                    </lord-icon>
                    <p class="text-muted">Apakah anda yakin?</p>
                    <input type="hidden" id="id" name="id" value="">
                </div>
                <div class="modal-footer">
                  <button type="submit" class="btn btn-primary">Ya</button>
                  <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tidak</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
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
<!-- Toastr Notifications-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
@endsection

@section('javascript')
  <script type="text/javascript">
    $(document).ready(function() {
        $('#table').DataTable({
            stateSave: true,
            responsive: true,
            autoWidth: false,
            processing: true,
            serverSide: true,
            ajax: "{{ route('news-and-event.index') }}",
            "columnDefs": [
                { "width": "10%", "targets": 0 },
                { "width": "10%", "targets": 1 },
                { "width": "30%", "targets": 2 },
                { "width": "10%", "targets": 3 },
                { "width": "10%", "targets": 4 },
                { "width": "10%", "targets": 5 }
            ],
            columns: [
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    "className": "text-center"
                },
                {
                    data: 'tumbnail',
                    name: 'tumbnail',
                    "className": "text-center"
                },
                {
                    data: 'judul',
                    name: 'judul',
                    "className": "text-center"
                },
                {
                    data: 'tanggal',
                    name: 'tanggal',
                    "className": "text-center"
                },
                {
                    data: 'status',
                    name: 'status',
                    "className": "text-center"
                },
                {
                    data: 'action',
                    name: 'action',
                    "className": "text-center",
                    orderable: false,
                    searchable: false
                }, 
            ]
        });
        $(document).on("click", ".delete-btn", function() {
          var newsId = $(this).data("id");
          $("input[name='id']").val(newsId);
          $("#modal").modal("show");
        });
    });
  </script>
  <script>
      @if(Session::has('status'))
          toastr.options =
          {
              "closeButton" : true,
              "progressBar" : true,
              "positionClass": "toast-bottom-right"
          }
          toastr.success("{{ session('status') }}");
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
