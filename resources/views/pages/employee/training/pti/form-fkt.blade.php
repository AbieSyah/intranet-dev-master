@extends('layouts.general')
@section('link')
<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- Required meta tags -->
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
<!-- Select2-->
<link rel="stylesheet" href="/assets/libs/adminlte/select2/css/4.1.0/select2.min.css">
<style type="text/css">
    .select2-container--default .select2-selection--multiple .select2-selection__choice__display { color: #000 }
    .select2-container.select2-container--default.select2-container--open  {
        z-index: 5000;
    }
    .select2, .select2 option {
    font-size:13px;
    }
</style>
<style>
    .wizard-content-left {
    background-blend-mode: darken;
    background-color: rgba(0, 0, 0, 0.45);
    background-image: url("https://i.ibb.co/X292hJF/form-wizard-bg-2.jpg");
    background-position: center center;
    background-size: cover;
    height: 100vh;
    padding: 30px;
    }
    .wizard-content-left h1 {
    color: #ffffff;
    font-size: 38px;
    font-weight: 600;
    padding: 12px 20px;
    text-align: center;
    }

    .form-wizard {
    color: #888888;
    padding: 30px;
    }
    .form-wizard .wizard-form-radio {
    display: inline-block;
    margin-left: 5px;
    position: relative;
    }
    .form-wizard .wizard-form-radio input[type="radio"] {
    -webkit-appearance: none;
    -moz-appearance: none;
    -ms-appearance: none;
    -o-appearance: none;
    appearance: none;
    background-color: #dddddd;
    height: 25px;
    width: 25px;
    display: inline-block;
    vertical-align: middle;
    border-radius: 50%;
    position: relative;
    cursor: pointer;
    }
    .form-wizard .wizard-form-radio input[type="radio"]:focus {
    outline: 0;
    }
    .form-wizard .wizard-form-radio input[type="radio"]:checked {
    background-color: #fb1647;
    }
    .form-wizard .wizard-form-radio input[type="radio"]:checked::before {
    content: "";
    position: absolute;
    width: 10px;
    height: 10px;
    display: inline-block;
    background-color: #ffffff;
    border-radius: 50%;
    left: 1px;
    right: 0;
    margin: 0 auto;
    top: 8px;
    }
    .form-wizard .wizard-form-radio input[type="radio"]:checked::after {
    content: "";
    display: inline-block;
    webkit-animation: click-radio-wave 0.65s;
    -moz-animation: click-radio-wave 0.65s;
    animation: click-radio-wave 0.65s;
    background: #000000;
    content: '';
    display: block;
    position: relative;
    z-index: 100;
    border-radius: 50%;
    }
    .form-wizard .wizard-form-radio input[type="radio"] ~ label {
    padding-left: 10px;
    cursor: pointer;
    }
    .form-wizard .form-wizard-header {
    text-align: center;
    }
    .form-wizard .form-wizard-next-btn, .form-wizard .form-wizard-previous-btn, .form-wizard .form-wizard-submit {
    background-color: #0b5394;
    color: #ffffff;
    display: inline-block;
    min-width: 100px;
    min-width: 120px;
    padding: 10px;
    text-align: center;
    }
    .form-wizard .form-wizard-next-btn:hover, .form-wizard .form-wizard-next-btn:focus, .form-wizard .form-wizard-previous-btn:hover, .form-wizard .form-wizard-previous-btn:focus, .form-wizard .form-wizard-submit:hover, .form-wizard .form-wizard-submit:focus {
    color: #ffffff;
    opacity: 0.6;
    text-decoration: none;
    }
    .form-wizard .wizard-fieldset {
    display: none;
    }
    .form-wizard .wizard-fieldset.show {
    display: block;
    }
    .form-wizard .wizard-form-error {
    display: none;
    background-color: #d70b0b;
    position: absolute;
    left: 0;
    right: 0;
    bottom: 0;
    height: 2px;
    width: 100%;
    }
    .form-wizard .form-wizard-previous-btn {
    background-color: #fb1647;
    }
    /* .form-wizard .form-control {
    font-weight: 300;
    height: auto !important;
    padding: 15px;
    color: #888888;
    background-color: #f1f1f1;
    border: none;
    }
    .form-wizard .form-control:focus {
    box-shadow: none;
    } */
    .form-wizard .form-group {
    position: relative;
    margin: 25px 0;
    }
    .form-wizard .wizard-form-text-label {
    position: absolute;
    left: 10px;
    top: 16px;
    transition: 0.2s linear all;
    }
    .form-wizard .focus-input .wizard-form-text-label {
    color: #0b5394;
    top: -18px;
    transition: 0.2s linear all;
    font-size: 12px;
    }
    .form-wizard .form-wizard-steps {
    margin: 30px 0;
    }
    .form-wizard .form-wizard-steps li {
    width: 25%;
    float: left;
    position: relative;
    }
    .form-wizard .form-wizard-steps li::after {
    background-color: #f3f3f3;
    content: "";
    height: 5px;
    left: 0;
    position: absolute;
    right: 0;
    top: 50%;
    transform: translateY(-50%);
    width: 100%;
    border-bottom: 1px solid #dddddd;
    border-top: 1px solid #dddddd;
    }
    .form-wizard .form-wizard-steps li span {
    background-color: #dddddd;
    border-radius: 50%;
    display: inline-block;
    height: 40px;
    line-height: 40px;
    position: relative;
    text-align: center;
    width: 40px;
    z-index: 1;
    }
    .form-wizard .form-wizard-steps li:last-child::after {
    width: 50%;
    }
    .form-wizard .form-wizard-steps li.active span, .form-wizard .form-wizard-steps li.activated span {
    background-color: #0b5394;
    color: #ffffff;
    }
    .form-wizard .form-wizard-steps li.active::after, .form-wizard .form-wizard-steps li.activated::after {
    background-color: #0b5394;
    left: 50%;
    width: 50%;
    border-color: #0b5394;
    }
    .form-wizard .form-wizard-steps li.activated::after {
    width: 100%;
    border-color: #0b5394;
    }
    .form-wizard .form-wizard-steps li:last-child::after {
    left: 0;
    }
    .form-wizard .wizard-password-eye {
    position: absolute;
    right: 32px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    }
    @keyframes click-radio-wave {
    0% {
        width: 25px;
        height: 25px;
        opacity: 0.35;
        position: relative;
    }
    100% {
        width: 60px;
        height: 60px;
        margin-left: -15px;
        margin-top: -15px;
        opacity: 0.0;
    }
    }
    @media screen and (max-width: 767px) {
    .wizard-content-left {
        height: auto;
    }
    }
</style>
@endsection
@section('content')
<!-- start page -->
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">                                        
                <form id="Formfkt" action="{{route('training.emp.fkt.pti.store')}}" method="POST">
                    @csrf
                    @method('put')
                    <div class="row mb-3">
                        <div class="col-lg-6">
                            <h4 class="text-primary">Formulir Pelaksanaan Pelatihan</h4>
                        </div>
                        <div class="col-lg-6">
                            <a href="{{ route('training.emp.fkt.pti.back') }}" class="btn btn-primary btn-label waves-effect waves-light float-end"><i class="ri-arrow-left-circle-line label-icon align-middle fs-16 me-2"></i> Back</a>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-lg-3">
                            <label for="usulan_program" class="form-label col-form-label">Usulan Program</label>
                        </div>
                        <div class="col-lg-3">
                            <input type="hidden" id="cek_radio" name="cek_radio" value="{{$cek_radio}}">
                            <div class="form-group">
                                <select class="form-control select2" id="usulan_program" name="usulan_program" data-placeholder="--Pilih Program--" required>
                                    <option disabled="true" selected="true"></option>
                                    <option value="ptt">Program Pelatihan Tahunan</option>
                                    <option value="pti">Program Pelatihan Insidentil</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6">                                        
                        </div>
                    </div>
                    <div id="topik-pelatihan" class="row mb-3">
                        <div class="col-lg-3">
                            <label for="judul_pelatihan" class="form-label col-form-label">Topik Pelatihan</label>
                        </div>
                        <div class="col-lg-5">
                            <div id="select-pelatihan">
                                <div class="form-group">
                                    <select class="form-control select2" id="judul_pelatihan" name="judul_pelatihan" data-placeholder="--Pilih Pelatihan--" required>
    
                                    </select>
                                </div>
                            </div>
                            <div id="input-pelatihan">
                                <input type="text" class="form-control" id="judul_fpkt" name="judul_fpkt" placeholder="Masukkan Pelatihan" value="" required>
                            </div>
                        </div>
                        <div class="col-lg-4">                                        
                        </div>
                    </div>
                    <div id="jenis_insidentil" class="row mb-3">
                        <div class="col-lg-3">
                            <label for="jenis_pelatihan" class="form-label col-form-label">Jenis pelatihan</label>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <select class="form-control select2" id="jenis_pelatihan" name="jenis_pelatihan" data-placeholder="--Jenis Pelatihan--">
                                    <option disabled="true" selected="true"></option>
                                    <option value="Online">Online</option>
                                    <option value="Offline">Offline</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6">                                        
                        </div>
                    </div>
                    <div id="peserta_tahunan" class="row mb-3">
                        <div class="col-lg-3">
                            <label for="peserta" class="form-label col-form-label">Peserta Pelatihan</label>
                        </div>
                        <div class="col-lg-6">
                            <input type="hidden" id="id_peserta" name="id_peserta" value="">
                            <textarea class="form-control" id="nama_peserta" name="nama_peserta" rows="3"></textarea>
                        </div>
                    </div>                    
                    <div id="peserta_insidentil" class="row mb-3">
                        @if($cek_radio == 'pemohon')
                            <div class="col-lg-3">
                                <label for="emp" class="form-label col-form-label">Peserta Pelatihan</label>
                            </div>
                            <div class="col-lg-6">                            
                                <div class="form-group">
                                    <select class="fs-12 form-control form-control-sm select2 @error("id_emp") is-invalid @enderror" name="id_emp[]" id="id_emp" data-placeholder="Select Employee" multiple="multiple">
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}">{{ $employee->fullname }}</option>
                                        @endforeach
                                    </select>
                                </div>                           
                            </div>
                        @else
                            <input type="hidden" id="id_emp" name="id_emp[]" value="{{$user->employee_id}}">
                        @endif
                    </div>
                    <div class="row mb-3">
                        <div class="col-lg-3">
                            <label for="id_vendor" class="form-label col-form-label">Provider</label>
                        </div>
                        <div class="col-lg-6">
                            <select class="fs-12 form-control select2" name="id_vendor" id="id_vendor" data-placeholder="Pilih Provider" required>
                                <option selected="true" value=""></option>
                                @foreach($vendors as $vendor)
                                    <option value="{{ $vendor->id }}">{{ $vendor->nama }}</option>
                                @endforeach
                                    <option value="other">Other</option>
                            </select>
                            <div id="other">

                            </div>
                        </div>
                        <div class="col-lg-3">                                        
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-lg-3">
                            <label for="biaya_fpkt" class="form-label col-form-label">Biaya Pelatihan</label>
                        </div>
                        <div class="col-lg-6">
                            <div class="input-group">
                                <span class="input-group-text">Rp</span><input type="text" class="form-control" id="biaya_fpkt" name="biaya_fpkt" placeholder="Masukkan Biaya" value="" required>
                            </div>
                        </div>
                        <div class="col-lg-3">                                        
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-lg-3">
                            <label for="jml_qty" class="form-label col-form-label">Quantity</label>
                        </div>
                        <div class="col-lg-6">
                            <div class="input-step step-primary">
                                <button type="button" class="minus">–</button>
                                <input type="number" id="jml_qty" name="jml_qty" class="product-quantity" value="1" min="0"
                                    max="100" readonly>
                                <button type="button" class="plus">+</button>
                            </div>
                        </div>
                        <div class="col-lg-3">                                        
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-lg-3">
                            <label for="biaya_fpkt" class="form-label col-form-label">Total Biaya</label>
                        </div>
                        <div class="col-lg-6">
                            <div class="input-group">
                                <span class="input-group-text">Rp</span><input type="text" class="form-control" id="total_biaya" name="total_biaya" placeholder="Total Biaya" value="" style="Background-color: #eff2f7;" readonly>
                            </div>
                        </div>
                        <div class="col-lg-3">                                        
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-lg-3">
                            <label for="date_pelaksanaan" class="form-label col-form-label">Tanggal Pelaksanaan</label>
                        </div>
                        <div class="col-lg-6">
                            <div class="input-group">
                                <input type="text" name="date_pelaksanaan" id="date_pelaksanaan"
                                    class="form-control @error('date_pelaksanaan') is-invalid @enderror"
                                    placeholder="Pilih Tanggal" data-provider="flatpickr" value="" required>
                                <span class="input-group-text"><i class="ri-calendar-event-line"></i></span>
                            </div>
                        </div>
                        <div class="col-lg-3">                                        
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-lg-3">
                            <label for="check" class="form-label col-form-label">Kepala Departemen</label>
                        </div>
                        <div class="col-lg-6">
                            <select class="form-control select2 fs-12" name="id_dept_head"
                                id="id_dept_head" data-placeholder="--Pilih Kepala Departemen--" required>
                                <option selected="true" disabled="true"></option>
                                @foreach ($employees as $emp)
                                    @if(!empty($emp->level->nama))
                                    <option value="{{ $emp->id }}">{{ $emp->fullname }} -- {{$emp->department->name}} -- {{$emp->level->nama}}</option>
                                    @else
                                    <option value="{{ $emp->id }}">{{ $emp->fullname }} -- {{$emp->department->name}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @if($cek_radio == 'pemohon')
                        <input type="hidden" id="id_atasan" name="id_atasan" value="{{$user->employee_id}}">
                    @else
                        <div class="row mb-3">
                            <div class="col-lg-3">
                                <label for="check" class="form-label col-form-label">Atasan Langsung</label>
                            </div>
                            <div class="col-lg-6">
                                <select class="form-control select2 fs-12" name="id_atasan"
                                    id="id_atasan" data-placeholder="--Pilih Atasan langsung--" required>
                                    <option selected="true" disabled="true"></option>
                                    @foreach ($employees as $emp)
                                        @if(!empty($emp->level->nama))
                                        <option value="{{ $emp->id }}">{{ $emp->fullname }} -- {{$emp->department->name}} -- {{$emp->level->nama}}</option>
                                        @else
                                        <option value="{{ $emp->id }}">{{ $emp->fullname }} -- {{$emp->department->name}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @endif
                    <div id="alasan-pti" class="row mb-3">
                        <div class="col-lg-3">
                            <label for="check" class="form-label col-form-label">Alasan Insidentil</label>
                        </div>
                        <div class="col-lg-6">
                            <textarea class="form-control" id="alasan_pti" name="alasan_pti" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="row mt-4 mb-3">
                        <div class="col-lg-10">
                            <!-- Tables Border Colors -->
                            <div class="table-responsive">
                                <table class="table table-bordered border-secondary fs-12 table-nowrap">
                                    <thead>
                                        <tr>
                                            <th scope="col">
                                                Latar Belakang Usulan Training : <br>
                                                <p class="text-muted"><i>(Penjelasan mengenai keterkaitan antara usulan topik training dengan pekerjaan saat ini).</i></p>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th scope="row">
                                                <div>
                                                    <textarea class="form-control" id="latar_belakang" name="latar_belakang" rows="3" required></textarea>
                                                </div>
                                            </th>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-lg-2">

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="hstack gap-2 d-print-none mt-4" style="justify-content: flex-end;">
                                {{-- <button class="btn btn-secondary" id="btn-draft" name="action" value="draft" type="submit">Draft</button> --}}
                                <button class="btn btn-primary" id="btn-submit" name="action" value="submit" type="submit">Submit</button>
                            </div>
                        </div>
                    </div>
                </form>              
            </div>
        </div><!-- end card -->
    </div>
    <!--end col-->
</div>
<!--end row-->
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
<!-- Select2 -->
<script src="/assets/libs/adminlte/select2/js/4.1.0/select2.min.js"></script>
<script src="/assets/js/pages/select2.init.js"></script>
<!-- input spin init -->
<script src="/assets/js/pages/form-input-spin.init.js"></script>
@endsection
@section('javascript')
<script>
    $(document).ready(function() {
        $("#btn-submit").click(function() {
            $("#Formfkt").submit(function () {
                $('#staticBackdrop').modal('show', true);
            });
        });
        $("#btn-draft").click(function() {
            $("#Formfkt").submit(function () {
                $('#staticBackdrop').modal('show', true);
            });
        });

        $('#date_pelaksanaan').flatpickr({
            allowInput: false,
            altInput: true,
            altFormat: "d F, Y",
            dateFormat: "Y-m-d",
        }); 
    });
</script>
<script>
    $('#topik-pelatihan').hide();
    $('#alasan-pti').hide();
    $("#peserta_tahunan").hide();
    $("#peserta_insidentil").hide();
    $("#jenis_insidentil").hide();
    $("#other").html('');
    $('#usulan_program').on('change', function() {
        $('#id_peserta').val('');
        $('#nama_peserta').val('');
        var tipe = this.value;
        var id_user = {{ Js::from($user->employee_id) }};
        var cek_radio = {{ Js::from($cek_radio) }};
        $.ajax({
            url: "{{ route('training.emp.select.usulan.pti') }}",
            type: "POST",
            data: {
                id_user: id_user,
                tipe: tipe,
                cek_radio: cek_radio,
                _token: '{{ csrf_token() }}'
            },
            dataType: 'json',
            success: function(result) {
                $('#topik-pelatihan').show();
                if(tipe == 'ptt'){
                    $('#alasan-pti').hide();
                    $('#select-pelatihan').show();
                    $('#input-pelatihan').hide();
                    $("#peserta_tahunan").show();
                    $("#peserta_insidentil").hide();
                    $("#jenis_insidentil").hide();
                    $("#judul_pelatihan").prop("required", true);
                    $("#judul_fpkt").prop("required", false);
                    $("#alasan_pti").prop("required", false);
                    $("#peserta_insidentil").prop("required", false);
                    $("#jenis_insidentil").prop("required", false);
                    $('#judul_pelatihan').html(
                        '<option selected="true" disabled="true"></option>'
                    );
                    $.each(result, function(key, value) {
                        $("#judul_pelatihan").append('<option value="' + key + '">' +
                            value + 
                        '</option>');                      
                    });

                    $('#judul_pelatihan').on('change', function() {
                        var judul = this.value;
                        var cek_radio = {{ Js::from($cek_radio) }};
                        $.ajax({
                            url: "{{ route('training.emp.select.pelatihan.pti') }}",
                            type: "POST",
                            data: {
                                id_user: id_user,
                                judul: judul,
                                cek_radio: cek_radio,
                                _token: '{{ csrf_token() }}'
                            },
                            dataType: 'json',
                            success: function(result) {
                                $('#id_peserta').val(result.id_peserta);
                                $('#nama_peserta').val(result.nama_emp);
                                var style ="background-color:#eff2f7;";
                                $("#nama_peserta").attr("style", style);
                                $("#nama_peserta").prop("readonly", true);
                            }
                        });
                    });
                }else{
                    $('#alasan-pti').show();
                    $('#select-pelatihan').hide();
                    $('#input-pelatihan').show();
                    $("#peserta_tahunan").hide();
                    $("#peserta_insidentil").show();
                    $("#jenis_insidentil").show();
                    $("#judul_pelatihan").prop("required", false);
                    $("#judul_fpkt").prop("required", true);
                    $("#alasan_pti").prop("required", true);
                    $("#peserta_insidentil").prop("required", true);
                    $("#jenis_insidentil").prop("required", true);
                }   
            }
        });
    });
    $('#id_vendor').on('change', function() {
        var vendor = this.value;
        if(vendor == 'other'){
            $("#other").append('<div class="form-group mt-4">'+
                '<input type="text" class="form-control" name="nama_vendor" id="nama_vendor" placeholder="Masukkan Nama Provider" required>'+
            '</div>');
        }else{
            $("#other").html('');
        }
    })
</script>
<script>
    //convert Quantity
    $('.plus').on('click', function() {
        var jml_plus = document.getElementById('jml_qty').value;
        var jml_biaya = document.getElementById('biaya_fpkt').value;
        const total_biaya = jml_biaya.replace(/\./g, '')*jml_plus;
        var total_rupiah = `${total_biaya.toLocaleString("id-ID")}`;
        $("#total_biaya").val(total_rupiah);        
    });
    $('.minus').on('click', function() {
        var jml_minus = document.getElementById('jml_qty').value;
        var jml_biaya = document.getElementById('biaya_fpkt').value;
        const total_biaya = jml_biaya.replace(/\./g, '')*jml_minus;
        var total_rupiah = `${total_biaya.toLocaleString("id-ID")}`;
        $("#total_biaya").val(total_rupiah);
    });
    //convert currency
    var rupiah = document.getElementById('biaya_fpkt');
        rupiah.addEventListener("keyup", function(e) {
        // tambahkan 'Rp.' pada saat form di ketik
        // gunakan fungsi formatRupiah() untuk mengubah angka yang di ketik menjadi format angka
        rupiah.value = formatRupiah(this.value);
       
        //Quantity
        var jumlah_rupiah = rupiah.value.replace(/\./g, '');
        var jml_qty = $("#jml_qty").val();
        const total_rupiah = jml_qty*jumlah_rupiah;
        var jml_rupiah = `${total_rupiah.toLocaleString("id-ID")}`;
        $("#total_biaya").val(jml_rupiah);
        });
        /* Fungsi formatRupiah */
        function formatRupiah(angka, prefix) {
        var number_string = angka.replace(/[^,\d]/g, "").toString(),
            split = number_string.split(","),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        // tambahkan titik jika yang di input sudah menjadi angka ribuan
        if (ribuan) {
            separator = sisa ? "." : "";
            rupiah += separator + ribuan.join(".");
        }

        rupiah = split[1] != undefined ? rupiah + "," + split[1] : rupiah;
        return prefix == undefined ? rupiah : rupiah ? rupiah : "";
        }
</script>
<script>
    $(function () {    
        $('.select2').select2();
    });
</script>
<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });
</script>
@endsection
