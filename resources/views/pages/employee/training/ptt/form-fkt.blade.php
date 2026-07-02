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
                <form id="Formfkt" action="{{route('training.emp.fkt.ptt.store')}}" method="POST">
                    @csrf
                    @method('put')
                    <div class="row mb-3">
                        <div class="col-lg-6">
                            <h4 class="text-primary">Formulir Kebutuhan Pelatihan (FKP)</h4>
                        </div>
                        <div class="col-lg-6">
                            <a href="{{ route('training.emp.fkt.ptt.back') }}" class="btn btn-primary btn-label waves-effect waves-light float-end"><i class="ri-arrow-left-circle-line label-icon align-middle fs-16 me-2"></i> Back</a>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-lg-3">
                            <label for="pemohon" class="form-label col-form-label col-form-label-sm">Nama Pemohon</label>
                        </div>
                        <div class="col-lg-6">
                            <input type="hidden" class="form-control form-control-sm" id="id_pemohon" name="id_pemohon" placeholder="Masukkan id" value="{{$user->employee->id}}">
                            <input type="text" class="form-control form-control-sm" id="nama_pemohon" name="nama_pemohon" placeholder="Masukkan Nama" value="{{$user->employee->fullname}}" style="Background-color: #eff2f7;" readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-lg-3">
                            <label for="department" class="form-label col-form-label col-form-label-sm">Departemen</label>
                        </div>
                        <div class="col-lg-6">
                            <input type="text" class="form-control form-control-sm" id="department" placeholder="Masukkan Departemen" value="{{$user->employee->department->name}}" style="Background-color: #eff2f7;" readonly>
                        </div>
                        <div class="col-lg-3">                                        
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-lg-3">
                            <label for="tahun_usulan" class="form-label col-form-label col-form-label-sm">Tahun Usulan Program</label>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                <select class="form-control select2" id="tahun_usulan" name="tahun_usulan" required>
                                    @if($periode->isNotEmpty())
                                        @foreach($periode as $period)
                                            @if($period->periode == $year_now)
                                            <option value="{{ $period->periode }}" selected>{{ $period->periode }}</option>
                                            @else
                                            <option value="{{ $period->periode }}">{{ $period->periode }}</option>
                                            @endif
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-7">                                        
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-lg-3">
                            <label for="tahun_pelaksanaan" class="form-label col-form-label col-form-label-sm">Tahun Rencana Pelaksanaan</label>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                <select class="form-control select2" id="tahun_pelaksanaan" name="tahun_pelaksanaan" required>
                                    @if($periode->isNotEmpty())
                                        @foreach($periode as $period)
                                            @if($period->periode == $year_now)
                                            <option value="{{ $period->periode }}" selected>{{ $period->periode }}</option>
                                            @else
                                            <option value="{{ $period->periode }}">{{ $period->periode }}</option>
                                            @endif
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-7">                                        
                        </div>
                    </div>
                    <div class="row mb-3" >
                        <div class="col-lg-3">
                            <label for="tipe" class="form-label col-form-label col-form-label-sm">Tujuan Usulan Program</label>
                        </div>
                        <div class="col-lg-3">
                            <input type="text" class="form-control form-control-sm" value="Program Training Tahunan" style="Background-color: #eff2f7;" readonly>
                            <input type="hidden" class="form-control form-control-sm" name="tipe" id="tipe" value="ptt" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-lg-3">
                            <label for="check" class="form-label col-form-label col-form-label-sm">Kepala Departemen</label>
                        </div>
                        <div class="col-lg-6">
                            <select class="form-control form-control-sm select2 fs-12" name="id_checker"
                                id="id_checker" data-placeholder="--Pilih Atasan--" required>
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
                    <div class="row">
                        <div class="card-body p-4 border-top border-top-dashed">
                            <div data-simplebar data-simplebar-auto-hide="false" style="max-width: 100%;">
                                <table class="table table-borderless fs-12" style="table-layout: fixed; width: 300%;">
                                    <thead class="align-middle">
                                        <tr class="table-active">
                                            <th scope="col" style="width: 2%;">#</th>
                                            <th scope="col" style="width: 10%;">
                                                Nama Peserta
                                            </th>
                                            <th scope="col" style="width: 15%;">
                                                Pelatihan
                                            </th>
                                            <th scope="col" style="width: 10%;">
                                                Jenis Pelatihan
                                            </th>
                                            <th scope="col" style="width: 10%">
                                                Sifat Pelatihan
                                            </th>
                                            <th scope="col" style="width: 15%">Alasan</th>
                                            <!-- <th scope="col" style="width: 10%">Periode</th> -->
                                            <th scope="col" style="width: 10%">Bulan Pelaksanaan</th>
                                            <th scope="col" id="h-provider" style="width: 10%">Provider</th>
                                            <th scope="col" id="h-biaya" style="width: 10%">Biaya</th>
                                            <th scope="col" id="h-akomodasi" style="width: 10%">Akomodasi</th>
                                            <th scope="col" style="width: 2%"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="newlink2">           
                                            
                                    </tbody>
                                    <tbody>
                                        <tr id="newForm" style="display: none;">
                                            <td class="d-none" colspan="5">
                                                <p>Add New Form</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="5">
                                                <a href="javascript:new_link2()" id="add-item"
                                                    class="btn btn-soft-success btn-sm"><i
                                                        class="ri-add-fill me-1 align-bottom"></i> Tambah Peserta</a>
                                            </td>
                                        </tr>
                                        <tr class="border-top border-top-dashed mt-2">
                                            <td colspan="3"></td>
                                            <td colspan="2" class="p-0"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="form-submit">
                        <div class="col-lg-12">
                            <div class="hstack gap-2 d-print-none mt-4" style="justify-content: flex-end;">
                                <button class="btn btn-secondary" id="btn-draft" name="action" value="draft" type="submit">Draft</button>
                                <button class="btn btn-primary" id="btn-submit" name="action" value="submit" type="submit">Submit</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div><!-- end card body -->
        </div><!-- end card -->
    </div>
    <!--end col-->
</div>
<!--end row-->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body ">
                <div class="form-wizard">
                    <form id="inputs">
                        <div class="form-wizard-header">
                            <!-- <p>Fill all form field to go next step</p> -->
                            <img src="/assets/images/task.png" alt="">
                            <ul class="list-unstyled form-wizard-steps clearfix">
                                
                            </ul>
                        </div>
                        <fieldset id="set-1" class="wizard-fieldset show">
                            <h5>Training Participant Information</h5>
                            <div class="form-group">
                                <label for="md_peserta" class="form-label">Nama Peserta</label>
                                <select class="fs-12 wizard-required form-control select2" name="md_peserta" id="md_peserta" data-placeholder="Pilih Peserta" multiple="multiple">
                                    @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}">{{ $employee->fullname }}</option>
                                    @endforeach
                                </select>
                                <div class="wizard-form-error"></div>
                            </div>                              
                            <div class="form-group">
                                <label for="md_pelatihan" class="form-label">Nama Pelatihan</label>
                                <input type="text" class="form-control wizard-required" name="md_pelatihan" id="md_pelatihan" placeholder="Masukkan Nama Pelatihan">
                                <div class="wizard-form-error"></div>
                            </div>
                            <div class="form-group">
                                Metode :
                                <div class="wizard-form-radio">
                                    <input name="md_jenis" class="wizard-required" id="md_online" type="radio" value="Online">
                                    <label for="md_online">Online</label>
                                </div>
                                <div class="wizard-form-radio">
                                    <input name="md_jenis" class="wizard-required" id="md_offline" type="radio" value="Offline">
                                    <label for="md_offline">Offline</label>
                                </div>
                            </div>
                            <div class="form-group clearfix">
                                <a href="javascript:;" class="form-wizard-next-btn float-right">Next</a>
                            </div>
                        </fieldset> 
                        <fieldset id="set-2" class="wizard-fieldset">
                            <h5>Fill all form field to go next step</h5>
                            <div class="form-group">
                            <label for="md_sifat" class="form-label">Jenis</label>
                                <select class="form-select wizard-required" id="md_sifat" name="md_sifat">
                                    <option selected value="">Open this select menu</option>
                                    <option value="Skill Training">Skill Training</option>
                                    <option value="Re-Training">Re-Training</option>
                                    <option value="Cross Functional Training">Cross Functional Training</option>
                                    <option value="Team Training">Team Training</option>
                                </select>
                                <div class="wizard-form-error"></div>
                            </div>
                            <div class="form-group">
                            <label for="md_alasan" class="form-label">Alasan</label>
                                <textarea style="white-space: pre-line;" class="form-control wizard-required" id="md_alasan" name="md_alasan" placeholder="Masukkan Alasan" rows="3"></textarea>
                                <div class="wizard-form-error"></div>
                            </div>
                            <div class="form-group">
                            <label for="md_bulan" class="form-label">Bulan Pelaksanaan</label>
                                <select class="form-select wizard-required" id="md_bulan" name="md_bulan">
                                    <option selected value="">Open this select menu</option>
                                    <option value="1">Januari</option>
                                    <option value="2">Februari</option>
                                    <option value="3">Maret</option>
                                    <option value="4">April</option>
                                    <option value="5">Mei</option>
                                    <option value="6">Juni</option>
                                    <option value="7">Juli</option>
                                    <option value="8">Agustus</option>
                                    <option value="9">September</option>
                                    <option value="10">Oktober</option>
                                    <option value="11">November</option>
                                    <option value="12">Desember</option>
                                </select>
                                <div class="wizard-form-error"></div>
                            </div>
                            <div class="form-group clearfix">
                                <a href="javascript:;" class="form-wizard-previous-btn float-left">Previous</a>
                                <a href="javascript:;" class="form-wizard-next-btn float-right">Next</a>
                            </div>
                        </fieldset> 
                        <fieldset id="set-3" class="wizard-fieldset">
                            <div id="md-ptt">
                                <h5>Fill all form field to go next step</h5>
                                <div class="form-group">
                                    <label for="md_provider" class="form-label">Provider</label>
                                    <select class="fs-12 form-control select2" name="md_provider" id="md_provider" data-placeholder="Pilih Vendor">
                                        <option selected="true" value=""></option>
                                        @foreach($vendors as $vendor)
                                            <option value="{{ $vendor->id }}">{{ $vendor->nama }}</option>
                                        @endforeach
                                            <option value="other">Other</option>
                                    </select>
                                    <div class="wizard-form-error"></div>
                                </div>
                                <div id="md_other">
                                    
                                </div>
                                <label for="md_biaya" class="form-label">Biaya</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span><input type="text" class="form-control wizard-required" id="md_biaya" name="md_biaya" placeholder="Masukkan Biaya">
                                    <div class="wizard-form-error"></div>
                                </div>
                                <div class="form-group">
                                    <label for="md_penginapan" class="form-label">a) Menginap: </label>
                                    <select class="form-select wizard-required" id="md_penginapan" name="md_penginapan">
                                        <option selected value="">Open this select menu</option>
                                        <option value="Ya">Ya</option>
                                        <option value="Tidak">Tidak</option>
                                    </select>
                                    <div class="wizard-form-error"></div>
                                </div>
                                <div class="form-group">
                                    <label for="md_transportasi" class="form-label">b) Transportasi: </label>
                                    <select class="form-select wizard-required" id="md_transportasi" name="md_transportasi">
                                        <option selected value="">Open this select menu</option>
                                        <option value="Ya">Ya</option>
                                        <option value="Tidak">Tidak</option>
                                    </select>
                                    <div class="wizard-form-error"></div>
                                </div>
                                <div class="form-group clearfix">
                                    <a href="javascript:;" class="form-wizard-previous-btn float-left">Previous</a>
                                    <a href="javascript:;" class="form-wizard-next-btn float-right">Next</a>
                                </div>
                            </div>
                        </fieldset> 
                        <fieldset id="set-4" class="wizard-fieldset">
                            <div class="text-center">
                                <div class="avatar-md mt-5 mb-4 mx-auto">
                                    <div class="avatar-title bg-light text-success display-4 rounded-circle">
                                        <i class="ri-checkbox-circle-fill"></i>
                                    </div>
                                </div>
                                <h5>Well Done !</h5>
                                <p class="text-muted">
                                    You have successfully filled in the registration form
                                </p>
                                <div class="form-group clearfix">
                                    <a href="javascript:;" class="form-wizard-previous-btn float-left">Previous</a>
                                    <a href="javascript:;" id="submit-wizard" class="form-wizard-next-btn float-right" data-bs-dismiss="modal" aria-label="Close">Finish</a>
                                    <!-- <button type="button" id="submit-wizard" class="form-wizard-submit float-right" data-bs-dismiss="modal" aria-label="Close">Finish</button> -->
                                </div>
                            </div>
                        </fieldset> 
                    </form>                            
                </div>  
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
<!-- Select2 -->
<script src="/assets/libs/adminlte/select2/js/4.1.0/select2.min.js"></script>
<script src="/assets/js/pages/select2.init.js"></script>
@endsection
@section('javascript')
<script>
    $(document).ready(function() {
        $("#btn-draft").click(function() {
            $("#Formfkt").submit(function () {
                $('#staticBackdrop').modal('show', true);
            });
        });
        $("#btn-submit").click(function() {
            $("#Formfkt").submit(function () {
                $('#staticBackdrop').modal('show', true);
            });
        });
    });
</script>
<script>
    $(document).ready(function () {
        $('#myModal').modal({
            backdrop: 'static',
            keyboard: false
        });
        $("#md_other").html('');
        $('#md_provider').on('change', function() {
            var vendor = this.value;
            if(vendor == 'other'){
                $("#md_other").append('<div class="form-group">'+
                    '<input type="text" class="form-control wizard-required" name="md_provider_other" id="md_provider_other" placeholder="Masukkan Nama Provider">'+
                    '<div class="wizard-form-error"></div>'+
                    '</div>');
            }else{
                $("#md_other").html('');
            }
        });
        $('#myModal').on('hidden.bs.modal', function () {
            $("#md_other").html('');
            $(this).find('form').trigger('reset');
            // wizard-fieldset
            $("#step-1").addClass('active');
            $("#step-1").removeClass('activated');
            $("#step-2").removeClass('activated');
            $("#step-3").removeClass('activated');
            $("#step-3").removeClass('active');
            $("#step-4").removeClass('activated');
            $("#step-4").removeClass('active');

            $("#set-1").addClass('show');
            $("#set-2").removeClass('show');
            $("#set-3").removeClass('show');
            $("#set-4").removeClass('show');
            $(this).removeData('bs.modal');
            $(".form-wizard-steps").trigger('reset');            
        });
    });
</script>
<script>
    //convert currency
    var rupiah = document.getElementById('md_biaya');
        rupiah.addEventListener("keyup", function(e) {
        // tambahkan 'Rp.' pada saat form di ketik
        // gunakan fungsi formatRupiah() untuk mengubah angka yang di ketik menjadi format angka
        rupiah.value = formatRupiah(this.value);
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
    jQuery(document).ready(function() {
  // click on next button
  jQuery('.form-wizard-next-btn').click(function() {
      var parentFieldset = jQuery(this).parents('.wizard-fieldset');
      var currentActiveStep = jQuery(this).parents('.form-wizard').find('.form-wizard-steps .active');
      var next = jQuery(this);
      var nextWizardStep = true;
      if(currentActiveStep.closest('li').attr('id') == 'step-1'){
          if (document.querySelectorAll('input[name="md_jenis"]:checked').length > 0) {
              nextWizardStep = true;
          } else {
              alert('Metode tidak boleh kosong.');
              nextWizardStep = false;
          }
      }
      if(currentActiveStep.closest('li').attr('id') == 'step-3'){
          if (document.getElementById("md_biaya").value != 0) {
              nextWizardStep = true;
          } else {
              alert('Biaya tidak boleh 0.');
              nextWizardStep = false;
          }
      }
    parentFieldset.find('.wizard-required').each(function(){
      var thisValue = jQuery(this).val();

      if( thisValue == "") {
        jQuery(this).siblings(".wizard-form-error").slideDown();
        nextWizardStep = false;
      }
      else {
        jQuery(this).siblings(".wizard-form-error").slideUp();
      }
    });
    if( nextWizardStep) {
      next.parents('.wizard-fieldset').removeClass("show","400");
      currentActiveStep.removeClass('active').addClass('activated').next().addClass('active',"400");
      next.parents('.wizard-fieldset').next('.wizard-fieldset').addClass("show","400");
      jQuery(document).find('.wizard-fieldset').each(function(){
        if(jQuery(this).hasClass('show')){
          var formAtrr = jQuery(this).attr('data-tab-content');
          jQuery(document).find('.form-wizard-steps .form-wizard-step-item').each(function(){
            if(jQuery(this).attr('data-attr') == formAtrr){
              jQuery(this).addClass('active');
              var innerWidth = jQuery(this).innerWidth();
              var position = jQuery(this).position();
              jQuery(document).find('.form-wizard-step-move').css({"left": position.left, "width": innerWidth});
            }else{
              jQuery(this).removeClass('active');
            }
          });
        }
      });
    }
  });
  //click on previous button
  jQuery('.form-wizard-previous-btn').click(function() {
    var counter = parseInt(jQuery(".wizard-counter").text());;
    var prev =jQuery(this);
    var currentActiveStep = jQuery(this).parents('.form-wizard').find('.form-wizard-steps .active');
    prev.parents('.wizard-fieldset').removeClass("show","400");
    prev.parents('.wizard-fieldset').prev('.wizard-fieldset').addClass("show","400");
    currentActiveStep.removeClass('active').prev().removeClass('activated').addClass('active',"400");
    jQuery(document).find('.wizard-fieldset').each(function(){
      if(jQuery(this).hasClass('show')){
        var formAtrr = jQuery(this).attr('data-tab-content');
        jQuery(document).find('.form-wizard-steps .form-wizard-step-item').each(function(){
          if(jQuery(this).attr('data-attr') == formAtrr){
            jQuery(this).addClass('active');
            var innerWidth = jQuery(this).innerWidth();
            var position = jQuery(this).position();
            jQuery(document).find('.form-wizard-step-move').css({"left": position.left, "width": innerWidth});
          }else{
            jQuery(this).removeClass('active');
          }
        });
      }
    });
  });
  // focus on input field check empty or not
  jQuery(".form-control").on('focus', function(){
    var tmpThis = jQuery(this).val();
    if(tmpThis == '' ) {
      jQuery(this).parent().addClass("focus-input");
    }
    else if(tmpThis !='' ){
      jQuery(this).parent().addClass("focus-input");
    }
  }).on('blur', function(){
    var tmpThis = jQuery(this).val();
    if(tmpThis == '' ) {
      jQuery(this).parent().removeClass("focus-input");
      jQuery(this).siblings('.wizard-form-error').slideDown("3000");
    }
    else if(tmpThis !='' ){
      jQuery(this).parent().addClass("focus-input");
      jQuery(this).siblings('.wizard-form-error').slideUp("3000");
    }
  });
});
</script>
<script>
    $(function () {    
        $('.select2').select2();
        // $('#md_peserta').select2({dropdownParent: $('#myModal .modal-content')});

        $('#myModal').on('shown.bs.modal', function (e) {
            $(this).find('.select2').select2({
                dropdownParent: $(this).find('.modal-content')
            });
        })

        $('#form-submit').hide();
        $('#add-item').click(function(){
            $('#form-submit').show();
        });
    });
</script>
<script>
    $(".form-wizard-steps").html("");
    $('#h-provider').show();
    $('#h-biaya').show();
    $('#h-akomodasi').show();

    $("td.provider").prop('hidden',false);
    $("td.biaya").prop('hidden',false);
    $("td.akomodasi").prop('hidden',false);
    $(':input.vendor').prop('required',false);
    $(':input.nominal').prop('required',true);
    $(':input.inap').prop('required',true);
    $(':input.transport').prop('required',true);
    //modal training
    $(".form-wizard-steps").append(
        '<li id="step-1" class="active"><span>1</span></li>'+
        '<li id="step-2"><span>2</span></li>'+
        '<li id="step-3"><span>3</span></li>'+
        '<li id="step-4"><span>4</span></li>'
    );
</script>
<script>
    var count = 0;
    function new_link2() {
        $('#myModal').modal('show'); 
        count++;        
        var e = document.createElement("tr"),
            t = (e.id = count, e.className = "produk", 
            '<tr>'+
                '<th scope="row" class="produk-id">' + count + '</th>'+
                '<td class="text-start">'+
                    '<input type="hidden" id="nomor" name="no_urut[]" value="'+count+'">'+
                    '<div class="mb-2">'+
                        '<div class="form-group">'+
                            '<select class="fs-12 form-control form-control-sm select2 @error("id_peserta") is-invalid @enderror" name="id_peserta-'+count+'[]" id="peserta-'+count+'" data-placeholder="Select Employee" multiple="multiple" required>@foreach($employees as $employee)<option value="{{ $employee->id }}">{{ $employee->fullname }}</option>@endforeach</select>'+
                        '</div>'+
                    '</div>'+
                '</td>'+
                '<td>'+
                    '<div class="input-group mb-2">'+
                        '<input type="text" class="form-control form-control-sm" id="judul-' +count +'" name="judul-'+count+'[]" placeholder="Masukkan Pelatihan" value="" required>'+
                    '</div>'+
                '</td>'+
                '<td>'+
                    '<select class="form-select form-select-sm mb-2" id="jenis_pelatihan-' +count +'" name="jenis_pelatihan-'+count+'[]" required>'+
                        '<option selected value="">Open this select menu</option>'+
                        '<option value="Online">Online</option>'+
                        '<option value="Offline">Offline</option>'+
                    '</select>'+
                '</td>'+
                '<td>'+
                    '<select class="form-select form-select-sm mb-2" id="sifat-' +count +'" name="sifat-'+count+'[]" required>'+
                        '<option selected value="">Open this select menu</option>'+
                        '<option value="Skill Training">Skill Training</option>'+
                        '<option value="Re-Training">Re-Training</option>'+
                        '<option value="Cross Functional Training">Cross Functional Training</option>'+
                        '<option value="Team Training">Team Training</option>'+
                    '</select>'+
                '</td>'+
                '<td>'+
                    '<div>'+
                        '<textarea style="white-space: pre-line;" class="form-control form-control-sm" id="alasan-' +count +'" name="alasan-'+count+'[]" placeholder="Masukkan Alasan" value="" rows="3" required></textarea>'+
                    '</div>'+
                '</td>'+
                '<td>'+
                    '<select class="form-select form-select-sm mb-2" id="bulan_pelaksanaan-' +count +'" name="bulan_pelaksanaan-'+count+'[]" required>'+
                        '<option selected value="">Open this select menu</option>'+
                        '<option value="1">Januari</option>'+
                        '<option value="2">Februari</option>'+
                        '<option value="3">Maret</option>'+
                        '<option value="4">April</option>'+
                        '<option value="5">Mei</option>'+
                        '<option value="6">Juni</option>'+
                        '<option value="7">Juli</option>'+
                        '<option value="8">Agustus</option>'+
                        '<option value="9">September</option>'+
                        '<option value="10">Oktober</option>'+
                        '<option value="11">November</option>'+
                        '<option value="12">Desember</option>'+
                    '</select>'+
                '</td>'+
                '<td class="provider">'+
                    '<div class="form-group">'+
                        '<select class="form-control form-control-sm select2 vendor" name="id_vendor-'+count+'[]" id="id_vendor-dropdown-' +count +'" data-placeholder="Pilih Vendor">'+
                            '<option selected="true" value=""></option>'+
                            '@foreach($vendors as $vendor)'+
                                '<option value="{{ $vendor->id }}">{{ $vendor->nama }}</option>'+
                            '@endforeach'+
                                '<option value="other">Other</option>'+
                        '</select>'+
                    '</div>'+
                    '<div id="cek_provider-'+count+'">'+
                        '<div class="form-group mt-3">'+
                            '<input type="text" class="form-control form-control-sm" id="vendor_other-' +count +'" name="vendor_other-'+count+'[]" placeholder="Masukkan Provider" value="">'+
                        '</div>'+
                    '</div>'+
                '</td>'+
                '<td class="biaya">'+
                    '<div class="input-group input-group-sm">'+
                        '<span class="input-group-text">Rp</span><input type="text" class="form-control form-control-sm nominal" id="biaya_fkt-' +count +'" name="biaya_fkt-'+count+'[]" placeholder="Masukkan Biaya" value="">'+
                    '</div>'+
                '</td>'+
                '<td class="akomodasi">'+
                    '<label for="penginapan" class="form-label">a) Menginap: </label>'+
                    '<select class="form-select form-select-sm mb-2 inap" id="penginapan-' +count +'" name="penginapan-'+count+'[]" required>'+
                        '<option selected value="">Open this select menu</option>'+
                        '<option value="Ya">Ya</option>'+
                        '<option value="Tidak">Tidak</option>'+
                    '</select>'+
                    '<label for="penginapan" class="form-label">b) Transportasi: </label>'+
                    '<select class="form-select form-select-sm mb-2 transport" id="transportasi-' +count +'" name="transportasi-'+count+'[]" required>'+
                        '<option selected value="">Open this select menu</option>'+
                        '<option value="Ya">Ya</option>'+
                        '<option value="Tidak">Tidak</option>'+
                    '</select>'+
                '</td>'+
                '<td class="produk-removal">'+
                    '<a href="javascript:avoid(0);" onclick="remove();" class="btn btn-soft-danger"><i class="ri-delete-bin-line"></i></a>'+
                '</td>'+
            '</tr>'
            ),
            t = (e.innerHTML = document.getElementById("newForm").innerHTML + t, document.getElementById("newlink2")
                .appendChild(e), document.querySelectorAll("[data-trigger]"));
        Array.from(t).forEach(function(e) {
            new Choices(e, {
                placeholderValue: "This is a placeholder set in the config",
                searchPlaceholderValue: "This is a search placeholder"
            })
        }), remove(), resetRow()
        //reinitialize the new select box
        $('.select2').select2();
        $('#cek_provider-'+count+'').hide();
        $('#id_vendor-dropdown-'+count+'').on('change', function() {
            var vendor = this.value;
            if(vendor == 'other'){
                $('#cek_provider-'+count+'').show();
                $('#vendor_other-'+count+'').val(input_vendor_other);
            }else{
                $('#cek_provider-'+count+'').hide();
                $('#vendor_other-'+count+'').val('');
            }
        });

        $("#submit-wizard").click(function() {
            //Declare and initialize variable for display inputs in div
            var input_peserta = "";
            var input_penilai = "";
            var input_pelatihan = "";
            var input_jenis = "";
            var input_sifat = "";
            var input_alasan = "";
            var input_bulan = "";
            var input_vendor = "";
            var input_vendor_other = "";
            var input_biaya = "";
            var input_penginapan = "";
            var input_transportasi = "";
            $("#inputs").each(function() {
                var md_peserta = $(this).find("#md_peserta").val();
                var md_penilai = $(this).find("#md_penilai").val();
                var md_pelatihan = $(this).find("#md_pelatihan").val();
                var md_jenis = $(this).find("input[name='md_jenis']:checked").val();
                var md_sifat = $(this).find("#md_sifat").val();
                var md_alasan = $(this).find("#md_alasan").val();
                var md_bulan = $(this).find("#md_bulan").val();
                var md_provider = $(this).find("#md_provider").val();
                var md_provider_other = $(this).find("#md_provider_other").val();
                var md_biaya = $(this).find("#md_biaya").val();
                var md_penginapan = $(this).find("#md_penginapan").val();
                var md_transportasi = $(this).find("#md_transportasi").val();
                input_peserta += md_peserta;
                input_penilai += md_penilai;
                input_pelatihan += md_pelatihan;
                input_jenis += md_jenis;
                input_sifat += md_sifat;
                input_alasan += md_alasan;
                input_bulan += md_bulan;
                input_vendor += md_provider;
                input_vendor_other += md_provider_other;
                input_biaya += md_biaya;
                input_penginapan += md_penginapan;
                input_transportasi += md_transportasi;
            });

            var arr_peserta = input_peserta.split(',');
            $.each(arr_peserta, function(i,e){
                $('#peserta-'+count+'').find('option[value="'+e+'"]').prop('selected', true);
            });
            $('#peserta-'+count+'').trigger('change.select2');
            $('#id_penilai-dropdown-' +count +'').find('option[value="'+input_penilai+'"]').prop('selected', true);
            $('#id_penilai-dropdown-'+count+'').trigger('change.select2');
            $('#judul-'+count+'').val(input_pelatihan);
            $('#jenis_pelatihan-' +count +'').find('option[value="'+input_jenis+'"]').prop('selected', true);
            $('#sifat-' +count +'').find('option[value="'+input_sifat+'"]').prop('selected', true);
            $('#alasan-'+count+'').val(input_alasan);
            $('#bulan_pelaksanaan-' +count +'').find('option[value="'+input_bulan+'"]').prop('selected', true);
            $('#id_vendor-dropdown-' +count +'').find('option[value="'+input_vendor+'"]').prop('selected', true);
            $('#id_vendor-dropdown-'+count+'').trigger('change.select2');
            if(input_vendor == 'other'){
                $('#cek_provider-'+count+'').show();
                $('#vendor_other-'+count+'').val(input_vendor_other);
            }else{
                $('#cek_provider-'+count+'').hide();
                $('#vendor_other-'+count+'').val('');
            }
            $('#biaya_fkt-'+count+'').val(input_biaya);
            $('#penginapan-' +count +'').find('option[value="'+input_penginapan+'"]').prop('selected', true);
            $('#transportasi-' +count +'').find('option[value="'+input_transportasi+'"]').prop('selected', true);
        });
        //convert currency
        var rupiah = document.getElementById('biaya_fkt-'+count+'');
        rupiah.addEventListener("keyup", function(e) {
        // tambahkan 'Rp.' pada saat form di ketik
        // gunakan fungsi formatRupiah() untuk mengubah angka yang di ketik menjadi format angka
        rupiah.value = formatRupiah(this.value);
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
        // return prefix == undefined ? rupiah : rupiah ? "Rp. " + rupiah : "";
        }

        // $("#periode-"+count+"").flatpickr({
        //     mode: "range",
        //     allowInput: true,
        //     altInput: false,
        //     altFormat: "d F, Y",
        //     dateFormat: "Y-m-d",
        // });  

        $("td.provider").prop('hidden',false);
        $("td.biaya").prop('hidden',false);
        $("td.akomodasi").prop('hidden',false);
        $(':input.vendor').prop('required',false);
        $(':input.nominal').prop('required',true);
        $(':input.inap').prop('required',true);
        $(':input.transport').prop('required',true);
    }
    remove();

    function remove() {
        Array.from(document.querySelectorAll(".produk-removal a")).forEach(function(e) {
            e.addEventListener("click", function(e) {
                removeItem(e), resetRow()
            })
        })
    }

    function resetRow() {
        Array.from(document.getElementById("newlink2").querySelectorAll("tr")).forEach(function(e, t) {
            t += 1;
            e.querySelector(".produk-id").innerHTML = t
        })
    }

    function removeItem(e) {
        e.target.closest("tr").remove()
    }
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
