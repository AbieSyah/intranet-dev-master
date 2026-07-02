@extends('layouts.master')
@section('link')
<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- Select2-->
<link rel="stylesheet" href="/assets/libs/adminlte/select2/css/4.1.0/select2.min.css">
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Ireguler</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Medical Check Up</a></li>
                    <li class="breadcrumb-item active">Ireguler</li>
                </ol>
            </div>

        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <form id="form" action="{{route('ireguler.store')}}" method="POST">
                @csrf            
                <div class="card-header align-items-center">        
                    <div class="row">
                        <div class="d-flex justify-content-between">
                            <div class="mt-2">
                                <h5>Create Medical Checkup</h5>
                                <p class="text-muted">Pilih tombol dibawah ini, untuk membuat data.</p>
                            </div>
                            <div class="mt-2">
                                <a href="{{ url()->previous() }}" class="btn btn-primary btn-label waves-effect waves-light"><i class="ri-arrow-left-fill label-icon align-middle fs-16 me-2"></i> Back</a>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="cek_karyawan" id="karyawan" value="1" required>
                                <label class="form-check-label" for="karyawan">
                                    Karyawan
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="cek_karyawan" id="non_karyawan" value="0" required>
                                <label class="form-check-label" for="non_karyawan">
                                    Non karyawan
                                </label>
                            </div>
                        </div>
                    </div>             
                </div><!-- end card header -->
                <div class="card-body">
                    <input type="hidden" id="cek_employee" name="cek_employee" value=""/>                
                    <div id="form_karyawan">
                        <!-- <div class="row">
                            <div class="col-lg-5">
                                <div class="mb-3">
                                    <label class="form-label">No Lab</label>
                                    <input type="text" class="form-control text-uppercase" name="no_lab_1" id="no_lab_1" placeholder="Masukkan No Lab">
                                </div>
                            </div>
                        </div> -->
                        <div class="row">
                            <div class="col-lg-5">
                                <div class="mb-3">
                                    <label class="form-label">Karyawan</label>
                                    <select class="form-control" name="employee"
                                        id="employee" data-placeholder="--Pilih Karyawan--">
                                        <option selected="true" disabled="true"></option>
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->id }}">{{ $employee->nik }} -- {{ $employee->fullname }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div><!--end col-->
                        </div>
                        <div class="row">
                            <div class="col-lg-5">
                                <div class="mb-3">
                                    <label>Tanggal Pemeriksaan</label>
                                    <div class="input-group">
                                        <input type="text" name="tgl_periksa_1" id="tgl_periksa_1"
                                            class="form-control"
                                            placeholder="Pilih Tanggal">
                                        <span class="input-group-text"><i class="ri-calendar-event-line"></i></span>
                                    </div>
                                </div>
                            </div><!--end col-->
                        </div>
                        <div class="row">
                            <div class="col-lg-5">
                                <div class="mb-3">
                                    <label class="form-label">Paket</label>
                                    <select class="form-control" name="paket_1"
                                        id="paket_1" data-placeholder="--Pilih Paket--">
                                        <option selected="true" disabled="true"></option>
                                        <option value="pria">Karyawan Pria</option>
                                        <option value="wanita">Karyawan Wanita</option>
                                    </select>
                                </div>
                            </div><!--end col-->
                        </div>
                        <div class="row">
                            <div class="col-lg-5">
                                <div class="mb-3">
                                    <label class="form-label">Laboratorium</label>
                                    <select class="form-control" name="vendor_1"
                                        id="vendor_1" data-placeholder="--Pilih Laboratorium--">
                                        <option selected="true" disabled="true"></option>
                                        @foreach ($vendors as $vendor)
                                            <option value="{{ $vendor->id }}">{{ $vendor->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div><!--end col-->
                        </div>
                        <div class="row">
                            <div class="col-lg-5">
                                <div class="mb-3">
                                    <label class="form-label">Area Pelaksanaan MCU</label>
                                    <input type="text" class="form-control" name="apm_1" id="apm_1" placeholder="Masukkan Area MCU">
                                </div>
                            </div><!--end col-->
                        </div>
                        <br>
                        <button type="submit" id="btn-save-1" class="btn btn-primary waves-effect waves-light">Save</button>
                    </div>
                    <div id="form_non_karyawan">
                        <!-- <div class="row">
                            <div class="col-lg-5">
                                <div class="mb-3">
                                    <label class="form-label">No Lab</label>
                                    <input type="text" class="form-control text-uppercase" name="no_lab_2" id="no_lab_2" placeholder="Masukkan No Lab">
                                </div>
                            </div>
                        </div> -->
                        <div class="row">
                            <div class="col-lg-5">
                                <div class="mb-3">
                                    <label class="form-label">Nama Karyawan</label>
                                    <input type="text" class="form-control text-uppercase" name="nama_karyawan" id="nama_karyawan" placeholder="Masukkan nama karyawan">
                                </div>
                            </div><!--end col-->
                        </div>
                        <div class="row">
                            <div class="col-lg-5">
                                <div class="mb-3">
                                    <label class="form-label">Gender</label>
                                    <select class="form-control" name="jk"
                                        id="jk" data-placeholder="--Pilih gender--">
                                        <option selected="true" disabled="true"></option>
                                        <option value="L">Laki-laki</option>                                        
                                        <option value="P">Perempuan</option>                                        
                                    </select>
                                </div>
                            </div><!--end col-->
                        </div>
                        <div class="row">
                            <div class="col-lg-5">
                                <div class="mb-3">
                                    <label class="form-label">No KTP</label>
                                    <input type="number" class="form-control" name="no_ktp" id="no_ktp" placeholder="Masukkan NIK KTP">
                                </div>
                            </div><!--end col-->
                        </div>
                        <div class="row">
                            <div class="col-lg-5">
                                <div class="mb-3">
                                    <label>Tanggal Lahir</label>
                                    <div class="input-group">
                                        <input type="text" name="tgl_lahir" id="tgl_lahir"
                                            class="form-control"
                                            placeholder="Pilih Tanggal">
                                        <span class="input-group-text"><i class="ri-calendar-event-line"></i></span>
                                    </div>
                                </div>
                            </div><!--end col-->
                        </div>
                        <div class="row">
                            <div class="col-lg-5">
                                <div class="mb-3">
                                    <label>Tanggal Pemeriksaan</label>
                                    <div class="input-group">
                                        <input type="text" name="tgl_periksa_2" id="tgl_periksa_2"
                                            class="form-control"
                                            placeholder="Pilih Tanggal">
                                        <span class="input-group-text"><i class="ri-calendar-event-line"></i></span>
                                    </div>
                                </div>
                            </div><!--end col-->
                        </div>
                        <div class="row">
                            <div class="col-lg-5">
                                <div class="mb-3">
                                    <label class="form-label">Paket</label>
                                    <select class="form-control" name="paket_2"
                                        id="paket_2" data-placeholder="--Pilih Paket--">
                                        <option selected="true" disabled="true"></option>
                                        <option value="calon karyawan">Calon Karyawan</option>
                                        <option value="pria">Karyawan Pria</option>
                                        <option value="wanita">Karyawan Wanita</option>
                                    </select>
                                </div>
                            </div><!--end col-->
                        </div>
                        <div class="row">
                            <div class="col-lg-5">
                                <div class="mb-3">
                                    <label class="form-label">Laboratorium</label>
                                    <select class="form-control" name="vendor_2"
                                        id="vendor_2" data-placeholder="--Pilih Laboratorium--">
                                        <option selected="true" disabled="true"></option>
                                        @foreach ($vendors as $vendor)
                                            <option value="{{ $vendor->id }}">{{ $vendor->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div><!--end col-->
                        </div>
                        <div class="row">
                            <div class="col-lg-5">
                                <div class="mb-3">
                                    <label class="form-label">Area Pelaksanaan MCU</label>
                                    <input type="text" class="form-control" name="apm_2" id="apm_2" placeholder="Masukkan Area MCU">
                                </div>
                            </div><!--end col-->
                        </div>
                        <br>
                        <button type="submit" id="btn-save-2" class="btn btn-primary waves-effect waves-light">Save</button>
                    </div>                                    
                </div>
            </form>
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
<!-- multi.js -->
<script src="/assets/libs/multi.js/multi.min.js"></script>
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
<script>
    $('#tgl_periksa_1').flatpickr({
        // mode: "range",
        allowInput: true,
        altInput: false,
        altFormat: "d F, Y",
        dateFormat: "Y-m-d",
    });   
    $('#tgl_periksa_2').flatpickr({
        // mode: "range",
        allowInput: true,
        altInput: false,
        altFormat: "d F, Y",
        dateFormat: "Y-m-d",
    });   
    $('#tgl_lahir').flatpickr({
        // mode: "range",
        allowInput: true,
        altInput: false,
        altFormat: "d F, Y",
        dateFormat: "Y-m-d",
    });   
</script>
<script>
    $('#form_karyawan').hide();
    $('#form_non_karyawan').hide();
    $("input[name='cek_karyawan']").click(function() {
        var cek_karyawan = this.value;
        $('#cek_employee').val(cek_karyawan);
       if(cek_karyawan == 1){
            //show or hide
            $('#form_karyawan').show();
            $('#form_non_karyawan').hide();

            //required
            // $("#no_lab_1").prop('required',true);
            $("#employee").prop('required',true);
            $("#tgl_periksa_1").prop('required',true);
            $("#paket_1").prop('required',true);
            $("#vendor_1").prop('required',true);
            $("#apm_1").prop('required',true);

            //required remove
            // $("#no_lab_2").removeAttr('required');
            $("#nama_karyawan").removeAttr('required');
            $("#jk").removeAttr('required');
            // $("#no_ktp").removeAttr('required');
            $("#tgl_lahir").removeAttr('required');
            $("#tgl_periksa_2").removeAttr('required');
            $("#paket_2").removeAttr('required');
            $("#vendor_2").removeAttr('required');
            $("#apm_2").removeAttr('required');
        }else{
            //show or hide
            $('#form_karyawan').hide();
            $('#form_non_karyawan').show();

            //required
            // $("#no_lab_2").prop('required',true);
            $("#nama_karyawan").prop('required',true);
            $("#jk").prop('required',true);
            // $("#no_ktp").prop('required',true);
            $("#tgl_lahir").prop('required',true);
            $("#tgl_periksa_2").prop('required',true);
            $("#paket_2").prop('required',true);
            $("#vendor_2").prop('required',true);
            $("#apm_2").prop('required',true);

            //required remove
            // $("#no_lab_1").removeAttr('required');
            $("#employee").removeAttr('required');
            $("#tgl_periksa_1").removeAttr('required');
            $("#paket_1").removeAttr('required');
            $("#vendor_1").removeAttr('required');
            $("#apm_1").removeAttr('required');

       }
    //    console.log(test)
    });
</script>
<script>
    $( "#btn-save-1" ).click(function() {
        $("#form").submit(function () {
            $('#staticBackdrop').modal('show', true);
        });
    });
    $( "#btn-save-2" ).click(function() {
        $("#form").submit(function () {
            $('#staticBackdrop').modal('show', true);
        });
    });

    $(function () {
        $('#vendor_1').select2();
        $('#vendor_2').select2();
        $('#employee').select2();
        $('#paket_1').select2();
        $('#paket_2').select2();
        $('#jk').select2();
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
<script>
    $('#select_all').click(function() {
        $('#multiselect-header option').prop('selected', true);
    });
    var multiSelectBasic = document.getElementById("multiselect-basic"),
    multiSelectHeader = (multiSelectBasic && multi(multiSelectBasic, {
        enable_search: !1
    }), document.getElementById("multiselect-header")),
    multiSelectOptGroup = (multiSelectHeader && multi(multiSelectHeader, {
        non_selected_header: "",
        selected_header: ""
    }), document.getElementById("multiselect-optiongroup")),
    autoCompleteFruit = (multiSelectOptGroup && multi(multiSelectOptGroup, {
        enable_search: !0
    }));
</script>
@endsection