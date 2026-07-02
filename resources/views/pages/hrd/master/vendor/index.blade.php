@extends('layouts.master')
@section('link')
<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- Datatables-->
<link href="/assets/libs/Datatables/DataTables-1.13.1/css/dataTables.bootstrap5.css" rel="stylesheet" type="text/css" />
<link href="/assets/libs/Datatables/Buttons-2.3.3/css/buttons.dataTables.css" rel="stylesheet" type="text/css" />
<link href="/assets/libs/Datatables/Responsive-2.4.0/css/responsive.bootstrap.css" rel="stylesheet" type="text/css" />
<!-- Toastr Notifications-->
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<!-- Select2-->
<link rel="stylesheet" href="/assets/libs/adminlte/select2/css/4.1.0/select2.min.css">
@endsection
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Vendor</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Master</a></li>
                    <li class="breadcrumb-item active">Vendor</li>
                </ol>
            </div>

        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                @can('hrd.master.vendor.create')
                <button type="button" id="add-vendor" class="btn btn-primary btn-label waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#modal" data-text="Add New Vendor">
                <i class="ri-add-circle-line label-icon align-middle fs-16 me-2"> </i>Add New Vendor
                </button>
                @endcan  
                <div class="flex-shrink-0">
                </div>
            </div><!-- end card header -->
            <div class="card-body">
                <table class="table table-striped bordered" id="table_vendor">
                <thead>
                    <tr>
                    <th scope="col" style="text-align:center">No</th>
                    <th scope="col" style="text-align:center">Nama</th>
                    <th scope="col" style="text-align:center">Alamat</th>
                    <th scope="col" style="text-align:center">Tipe</th>
                    <th scope="col" style="text-align:center">Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!--Modal add/edit-->
<div class="modal fade" id="modal" tabindex="-1" aria-labelledby="exampleModalgridLabel" aria-modal="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
          <h5 class="modal-title" id="exampleModalgridLabel">Vendor</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="form" action="{{ route('vendor.store') }}" method="post">
          @csrf
          @method('post')
          <div class="row g-3">
              <input type="hidden" id="id" name="id" value="">
              <div class="col-lg-12">
                <div>
                  <label for="nama" class="form-label">Nama</label>
                  <input type="text" class="form-control" name="nama" id="nama" placeholder="Masukkan Nama Vendor" value="" required>
                </div>
              </div><!--end col-->
              <div class="col-lg-12">
                <div>
                    <label for="alamat" class="form-label">Alamat</label>
                    <textarea class="form-control" name="alamat" id="alamat" rows="3"></textarea>
                  <!-- <input type="text" class="form-control" name="alamat" id="alamat" placeholder="Masukkan Alamat Vendor " value="" required> -->
                </div>
              </div><!--end col-->
              <div class="col-lg-12">                            
                <label for="tipe" class="form-label">Tipe</label>
                <select class="form-control" id="tipe" name="tipe" data-placeholder="Pilih Tipe" required>
                    <option selected="true" disabled="true"></option>
                    <option value="medical">Medical</option>
                    <option value="training">Training</option>
                </select>
            </div><!--end col-->
              <div class="col-lg-12">
                  <div class="hstack gap-2 justify-content-end">
                      <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                      <button type="submit" id="btn-save" class="btn btn-primary">Save</button>
                  </div>
              </div><!--end col-->
          </div><!--end row-->
        </form>
      </div>
    </div>
  </div>
</div>
<!--Modal staticbackdrop-->
<div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <img src="/assets/images/loading.gif" style="width:120px;height:120px">                    
                <div class="mt-4">
                    <h4 class="mb-3">Please wait...</h4>
                    <h4 class="mb-3">Do not leave this page</h4>
                </div>
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
<!-- Toastr Notifications-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<!-- Select2 -->
<script src="/assets/libs/adminlte/select2/js/select2.min.js"></script>
<script src="/assets/js/pages/select2.init.js"></script>
@endsection

@section('javascript')
<script>
    $( "#btn-save" ).click(function() {
        $("#form").submit(function () {
            $('#staticBackdrop').modal('show', true);
        });
    });
    $(function () {
        $('#tipe').select2({dropdownParent: $('#modal .modal-content')});
    });
</script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#table_vendor').DataTable({
        "responsive": true,
        "autoWidth": false,
        stateSave: true,
        processing: true,
        serverSide: true,
        // "language": {
        //     "loadingRecords": "No data available in table"
        // },
        ajax: "{{ route('vendor.index') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex' , "className": "text-center"},
                {data: 'nama', name: 'nama' , "className": "text-center"},
                {data: 'alamat', name: 'alamat' , "className": "text-center"},
                {data: 'tipe', name: 'tipe' , "className": "text-center"},
                {data: 'action', name: 'action', "className": "text-center", orderable: false, searchable: false},
            ]
        });
        // $.fn.dataTable.ext.errMode = 'none';

        $('#add-vendor').on("click", function() {
            $("input[name='id']").val('');
            $("input[name='nama']").val('');
            document.getElementsByName('alamat')[0].value = '';
            $('#tipe').val(null).trigger('change');
        });
        $('#table_vendor').on("click", ".edit-btn", function() {
            var vendorId = $(this).data("id");
            $.ajax({
            url: "{{ route('vendor.edit') }}",
            method: "GET",
            data: {
                id: vendorId
            },
            success: function(result) {
                // console.log(result);
                $("input[name='id']").val(result.id);
                $("input[name='nama']").val(result.nama);
                document.getElementsByName('alamat')[0].value = result.alamat;
                $('#tipe').val(result.tipe).trigger('change');


                $("#modal").modal("show");
            },
            error: function(xhr, status, error) {
                console.log(xhr, status, error);
            }
            });
        });
    });        
</script>
<script>
    $(document).ready(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
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