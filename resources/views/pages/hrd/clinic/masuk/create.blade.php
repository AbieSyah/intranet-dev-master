@extends('layouts.master')
@section('link')
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link href="/assets/libs/Datatables/DataTables-1.13.1/css/dataTables.bootstrap5.css" rel="stylesheet" type="text/css" />
  <link href="/assets/libs/Datatables/Buttons-2.3.3/css/buttons.dataTables.css" rel="stylesheet" type="text/css" />
  <link href="/assets/libs/Datatables/Responsive-2.4.0/css/responsive.bootstrap.css" rel="stylesheet" type="text/css" />
  <!-- Select2-->
  <link rel="stylesheet" href="/assets/libs/adminlte/select2/css/4.1.0/select2.min.css">
  <style>
    .select2-container--default .select2-selection--multiple .select2-selection__choice__display { color: #000 }
  </style>
@endsection

@section('content')

<div class="row">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <h4 class="mb-sm-0">Medicine Transaction In</h4>

      <div class="page-title-right">
          <ol class="breadcrumb m-0">
              <li class="breadcrumb-item"><a href="javascript: void(0);">Medicine</a></li>
              <li class="breadcrumb-item"><a href="javascript: void(0);">Transaction</a></li>
              <li class="breadcrumb-item active">In</li>
          </ol>
      </div>

    </div>
  </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <!-- Info Validation -->
        <div class="alert alert-primary alert-dismissible alert-label-icon rounded-label fade show" role="alert">
        <i class="ri-error-warning-line label-icon"></i>
        <strong>Kotak input yang diberi tanda <span class="text-danger">*</span> wajib diisi.</strong>
        </div>
    </div>
</div>

<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-body">
        <form id="form-in" method="POST" action="{{route('clinic.masuk.store')}}">
            @csrf
            @method('PUT')
            <div class="card-body p-4">
                <div class="row">
                    <div class="col-lg-4 col-sm-6" hidden>
                        <div class="form-group">
                            <label for="type">Doctor on duty<span class="text-danger">*</span></label>
                            <input type="text" class="form-control text-sm" id="id_user" name="id_user" value="{{$user->employee_id}}">
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6 mb-3">
                        <label for="type">Tanggal Transaksi<span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" class="form-control text-sm @error('tr_tanggal') is-invalid @enderror" id="tr_tanggal" name="tr_tanggal"
                                placeholder="Masukkan Tanggal" required>
                            <span class="input-group-text" id="basic-addon2"><i
                                class="ri-calendar-todo-line"></i></span>
                        </div>
                    </div>
                    <div class="col-lg-8 col-sm-6">
                        <a href="{{ url()->previous() }}" class="btn btn-primary btn-label waves-effect waves-light float-end"><i class="ri-arrow-left-circle-line label-icon align-middle fs-16 me-2"></i> Back</a>
                    </div>
                </div>
            </div>
            <div class="card-body p-4 border-top border-top-dashed">
                <div class="table-responsive">
                    <table class="invoice-table table table-borderless table-nowrap mb-0">
                        <thead class="align-middle">
                            <tr class="table-active">
                                <th scope="col" style="width: 50px;">#</th>
                                <th scope="col">
                                    Medicine<span class="text-danger">*</span>
                                </th>
                                <th scope="col" style="width: 150px;">Quantity<span class="text-danger">*</span></th>
                                <th scope="col" class="text-end" style="width: 105px;"></th>
                            </tr>
                        </thead>
                        <tbody id="newlink">
                            <tr id="1" class="product">
                                <th scope="row" class="product-id">1</th>
                                <td class="text-start">
                                    <div class="mb-2">
                                        <div class="form-group">
                                            <select class="form-control js-example-basic-single @error('id_drug') is-invalid @enderror" name="id_drug[]"
                                                id="id_drug-dropdown" data-placeholder="--Pilih Obat--" required>
                                                <option selected="true" disabled="true"></option>
                                                @foreach ($drugs as $drug)
                                                    <option value="{{$drug->id}}">{{$drug->nama}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>                                                                                   
                                </td>
                                <td>
                                    <div class="mb-2">
                                        <div class="input-group">
                                            <input type="number" class="form-control text-sm" id="jml_drug"
                                                name="jml_drug[]" value="0">
                                            <span class="input-group-text" id="basic-addon2"><i class="las la-capsules"></i></span>
                                        </div>
                                    </div>                                           
                                </td>
                                <td class="product-removal">
                                    <a href="javascript:void(0)" class="btn btn-danger">Delete</a>
                                </td>
                            </tr>
                        </tbody>
                        <tbody>
                            <tr id="newForm" style="display: none;">
                                <td class="d-none" colspan="5">
                                    <p>Add New Form</p>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="5">
                                    <a href="javascript:new_link()" id="add-item"
                                        class="btn btn-soft-secondary fw-medium"><i
                                            class="ri-add-fill me-1 align-bottom"></i> Add Item</a>
                                </td>
                            </tr>
                            <tr class="border-top border-top-dashed mt-2">
                                <td colspan="3"></td>
                                <td colspan="2" class="p-0"></td>
                            </tr>
                        </tbody>
                    </table>
                    <!--end table-->
                </div>
                    <div class="hstack gap-2 d-print-none mt-4" style="justify-content: flex-end;">                        
                        <button type="submit" id="btn-submit" class="btn btn-primary">Submit</button>
                    </div>
            </div>
        </form>
      </div>
    </div>
  </div>
  <!--end col-->
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
<!--end row-->
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
@endsection

@section('javascript')
  <script>
    $('.js-example-basic-single').select2();

    $('#tr_tanggal').flatpickr({
        allowInput: true,
        altInput: false,
        altFormat: "d F, Y",
        dateFormat: "Y-m-d",
    });   

    $( "#btn-submit" ).click(function() {
        $("#form-in").submit(function () {
            $('#staticBackdrop').modal('show', true);
        });
    });
  </script>
    <script>
        var count = 1;

        function new_link() {
            count++;
            var e = document.createElement("tr"),
                t = (e.id = count, e.className = "product", '<tr>'+
                    '<th scope="row" class="product-id">' + count +'</th>'+
                        '<td class="text-start">'+
                            '<div class="mb-2">'+
                                '<div class"form-group">'+
                                    '<select class="form-control js-example-basic-single @error("id_drug") is-invalid @enderror" name="id_drug[]" id="id_drug-dropdown-'+ count +'" data-placeholder="--Pilih Obat--" required>'+
                                        '<option selected="true" disabled="true"></option>'+
                                        '@foreach ($drugs as $drug)'+
                                            '<option value="{{ $drug->id }}" >{{ $drug->nama }}</option>'+
                                        '@endforeach'+
                                    '</select>'+
                                '</div>'+
                            '</div>'+
                        '</td>'+
                        '<td>'+
                            '<div class="input-group">'+
                                '<input type="number" class="form-control text-sm" id="jml_drug" name="jml_drug[]" value="0">'+
                                    '<span class="input-group-text" id="basic-addon2">'+
                                        '<i class="las la-capsules"></i>'+
                                    '</span>'+
                            '</div>'+
                        '</td><td class="product-removal"><a class="btn btn-danger">Delete</a></td></tr>'
                ),
                t = (e.innerHTML = document.getElementById("newForm").innerHTML + t, document.getElementById("newlink")
                    .appendChild(e), document.querySelectorAll("[data-trigger]"));
            Array.from(t).forEach(function(e) {
                new Choices(e, {
                    placeholderValue: "This is a placeholder set in the config",
                    searchPlaceholderValue: "This is a search placeholder"
                })
            }), remove(), resetRow()
            
            //reinitialize the new select box
            $('.js-example-basic-single').select2({
                //configuration
                // tags: true
            });
        }
        remove();

        function remove() {
            Array.from(document.querySelectorAll(".product-removal a")).forEach(function(e) {
                e.addEventListener("click", function(e) {
                    removeItem(e), resetRow()
                })
            })
        }

        function resetRow() {
            Array.from(document.getElementById("newlink").querySelectorAll("tr")).forEach(function(e, t) {
                t += 1;
                e.querySelector(".product-id").innerHTML = t
            })
        }

        function removeItem(e) {
            e.target.closest("tr").remove()
        }
    </script>
@endsection
