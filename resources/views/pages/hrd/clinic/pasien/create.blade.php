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
      <h4 class="mb-sm-0">Tambah Pasien</h4>

      <div class="page-title-right">
          <ol class="breadcrumb m-0">
              <li class="breadcrumb-item"><a href="javascript: void(0);">Tambah</a></li>
              <li class="breadcrumb-item active">Pasien</li>
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
        <form id="form-patient" method="POST" action="{{route('clinic.patient.store')}}">
            @csrf
            @method('PUT')
            <div class="card-body p-4">
                <div class="row">
                    <div class="col-lg-12 col-sm-6">
                        <a href="{{ url()->previous() }}" class="btn btn-primary btn-label waves-effect waves-light float-end"><i class="ri-arrow-left-circle-line label-icon align-middle fs-16 me-2"></i> Back</a>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-lg-4 col-sm-6" hidden>
                        <div class="form-group">
                            <label for="type">Doctor on duty<span class="text-danger">*</span></label>
                            <input type="text" class="form-control text-sm" id="id_user" name="id_user" value="{{$user->employee_id}}">
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <div class="form-group">
                            <label for="type">Nama Pasien<span class="text-danger">*</span></label>
                            <select class="form-control js-example-basic-single @error('id_employee') is-invalid @enderror" name="id_employee"
                                id="id_employee" data-placeholder="--Pilih Pasien--" required>
                                <option selected="true" disabled="true"></option>
                                @foreach ($employees as $emp)
                                    <option value="{{$emp->id}}">{{$emp->fullname}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6 mb-3">
                        <label for="type">Tanggal Kunjungan<span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" class="form-control text-sm @error('tr_tanggal') is-invalid @enderror" id="tr_tanggal" name="tr_tanggal"
                                placeholder="Masukkan Tanggal" required>
                            <span class="input-group-text" id="basic-addon2"><i
                                class="ri-calendar-todo-line"></i></span>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <div>
                            <label for="keluhan" class="form-label">Gejala<span class="text-danger">*</span></label>
                            <textarea class="form-control" id="keluhan" name="keluhan" rows="3" required></textarea>
                        </div>
                    </div>                    
                </div>
                <div class="row mb-4">
                    <div class="col-lg-4 col-sm-6">
                        <div class="form-group">
                            <label for="type">Diagnosis<span class="text-danger">*</span></label>
                            <input type="text" class="form-control text-sm" id="diagnosa" name="diagnosa" required>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <div class="form-group">
                            <label for="type">Blood Tension<span class="text-danger">*</span></label>
                            <input type="text" class="form-control text-sm" id="tensi" name="tensi" placeholder="___/__" data-slots="_" required>
                        </div>
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
                                                id="id_drug-dropdown" data-placeholder="--Pilih Obat--">
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
                <div class="hstack gap-2 d-print-none mt-2 mb-2" style="justify-content: flex-end;">                        
                    <button type="submit" id="btn-submit" class="btn btn-primary">Submit</button>
                </div>
                <div class="card-body">
                    <ul class="nav nav-pills arrow-navtabs nav-info bg-light mb-3" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#arrow-overview" role="tab">
                                <span class="d-block d-sm-none"><i class="mdi mdi-home-variant"></i></span>
                                <span class="d-none d-sm-block">Riwayat Kunjungan</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#arrow-profile" role="tab">
                                <span class="d-block d-sm-none"><i class="mdi mdi-account"></i></span>
                                <span class="d-none d-sm-block">MCU Tahunan</span>
                            </a>
                        </li>
                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content text-muted">
                        <div class="tab-pane active" id="arrow-overview" role="tabpanel">
                            <table class="table table-striped bordered" id="table_log">
                                <thead>
                                  <tr>
                                    <th scope="col" style="text-align:center">Doctor Name</th>
                                    <th scope="col" style="text-align:center">Date</th>
                                    <th scope="col" style="text-align:center">NIK</th>
                                    <th scope="col" style="text-align:center">Patient Name</th>
                                    <th scope="col" style="text-align:center">Diagnose</th>
                                    <th scope="col" style="text-align:center">Symptoms</th>
                                    <th scope="col" style="text-align:center">Tension</th>
                                    <th scope="col" style="text-align:center">Keterangan</th>
                                    <th scope="col" style="text-align:center"></th>
                                  </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                        <div class="tab-pane" id="arrow-profile" role="tabpanel">
                            <div class="row mb-4">
                                <div class="col-lg-2">
                                    <select class="form-control js-example-basic-single" name="date_mcu" id="date_mcu" data-placeholder="--Pilih Tahun--">
                                        <option selected="true" disabled="true"></option>                             
                                    </select>
                                </div>
                                <div id="preview-mcu" class="col-lg-2">
                                    
                                </div>
                            </div>
                            <ul class="nav nav-tabs mb-3" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#medical" role="tab" aria-selected="false">
                                        Overview
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#hematologi" role="tab" aria-selected="false">
                                        Hematologi
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#urine" role="tab" aria-selected="false">
                                        Urine
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#faal" role="tab" aria-selected="true">
                                        Faal
                                    </a>
                                </li>
                            </ul>
                            <div class="tab-content text-muted">
                                <div class="tab-pane active" id="medical" role="tabpanel">
                                    <h5 class="card-title mb-3">Medical Information</h5>
                                    <div class="profile-timeline">
                                        <div class="accordion accordion-flush" id="todayExample">
                                            <div class="accordion-item border-0">
                                                <div class="accordion-header" id="headingOne">
                                                    <a class="accordion-button p-2 shadow-none" data-bs-toggle="collapse" href="#collapseOne" aria-expanded="true">
                                                        <div class="d-flex">
                                                            <div class="flex-shrink-0 avatar-xs">
                                                                <div class="avatar-title bg-light text-primary rounded-circle">
                                                                    <i class="ri-survey-line"></i>
                                                                </div>
                                                            </div>
                                                            <div class="flex-grow-1 ms-3">
                                                                <h6 class="fs-14 mb-1">
                                                                    Result Laboratorium
                                                                </h6>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                                <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                                    <div id="lab">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="accordion-item border-0">
                                                <div class="accordion-header" id="headingTwo">
                                                    <a class="accordion-button p-2 shadow-none" data-bs-toggle="collapse" href="#collapseTwo" aria-expanded="true">
                                                        <div class="d-flex">
                                                            <div class="flex-shrink-0 avatar-xs">
                                                                <div class="avatar-title bg-light text-primary rounded-circle">
                                                                    <i class="ri-survey-line"></i>
                                                                </div>
                                                            </div>
                                                            <div class="flex-grow-1 ms-3">
                                                                <h6 class="fs-14 mb-1">
                                                                    Result Photo Thorax
                                                                </h6>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                                <div id="collapseTwo" class="accordion-collapse collapse show" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                                                    <div id="foto_thorax">
                                                    </div>
                                                </div> 
                                            </div>
                                            <div class="accordion-item border-0"> 
                                                <div class="accordion-header" id="headingekg">
                                                    <a class="accordion-button p-2 shadow-none" data-bs-toggle="collapse" href="#collapseekg" aria-expanded="true">
                                                        <div class="d-flex">
                                                            <div class="flex-shrink-0 avatar-xs">
                                                                <div class="avatar-title bg-light text-primary rounded-circle">
                                                                    <i class="ri-survey-line"></i>
                                                                </div>
                                                            </div>
                                                            <div class="flex-grow-1 ms-3">
                                                                <h6 class="fs-14 mb-1">
                                                                    Result EKG
                                                                </h6>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                                <div id="collapseekg" class="accordion-collapse collapse show" aria-labelledby="headingekg" data-bs-parent="#accordionExample">
                                                    <div id="ekg">
                                                    </div>
                                                </div> 
                                            </div>
                                            <div class="accordion-item border-0"> 
                                                <div class="accordion-header" id="headingThree">
                                                    <a class="accordion-button p-2 shadow-none" data-bs-toggle="collapse" href="#collapseThree" aria-expanded="true">
                                                        <div class="d-flex">
                                                            <div class="flex-shrink-0 avatar-xs">
                                                                <div class="avatar-title bg-light text-primary rounded-circle">
                                                                    <i class="ri-survey-line"></i>
                                                                </div>
                                                            </div>
                                                            <div class="flex-grow-1 ms-3">
                                                                <h6 class="fs-14 mb-1">
                                                                    Result Audiometri
                                                                </h6>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                                <div id="collapseThree" class="accordion-collapse collapse show" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                                                    <div id="audiometri">
                                                    </div>
                                                </div> 
                                            </div>
                                            <div class="accordion-item border-0"> 
                                                <div class="accordion-header" id="headingFour">
                                                    <a class="accordion-button p-2 shadow-none" data-bs-toggle="collapse" href="#collapseFour" aria-expanded="true">
                                                        <div class="d-flex">
                                                            <div class="flex-shrink-0 avatar-xs">
                                                                <div class="avatar-title bg-light text-primary rounded-circle">
                                                                    <i class="ri-survey-line"></i>
                                                                </div>
                                                            </div>
                                                            <div class="flex-grow-1 ms-3">
                                                                <h6 class="fs-14 mb-1">
                                                                    Result Physical Doctor
                                                                </h6>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                                <div id="collapseFour" class="accordion-collapse collapse show" aria-labelledby="headingFour" data-bs-parent="#accordionExample">
                                                    <div id="fisik_dokter">
                                                    </div>
                                                </div> 
                                            </div> 
                                            <div class="accordion-item border-0"> 
                                                <div class="accordion-header" id="headingFive">
                                                    <a class="accordion-button p-2 shadow-none" data-bs-toggle="collapse" href="#collapseFive" aria-expanded="true">
                                                        <div class="d-flex">
                                                            <div class="flex-shrink-0 avatar-xs">
                                                                <div class="avatar-title bg-light text-primary rounded-circle">
                                                                    <i class="ri-survey-line"></i>
                                                                </div>
                                                            </div>
                                                            <div class="flex-grow-1 ms-3">
                                                                <h6 class="fs-14 mb-1">
                                                                    Conclusion
                                                                </h6>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                                <div id="collapseFive" class="accordion-collapse collapse show" aria-labelledby="headingFive" data-bs-parent="#accordionExample">
                                                    <div id="kesimpulan">
                                                    </div>
                                                </div> 
                                            </div> 
                                            <div class="accordion-item border-0"> 
                                                <div class="accordion-header" id="headingSix">
                                                    <a class="accordion-button p-2 shadow-none" data-bs-toggle="collapse" href="#collapseSix" aria-expanded="true">
                                                        <div class="d-flex">
                                                            <div class="flex-shrink-0 avatar-xs">
                                                                <div class="avatar-title bg-light text-primary rounded-circle">
                                                                    <i class="ri-survey-line"></i>
                                                                </div>
                                                            </div>
                                                            <div class="flex-grow-1 ms-3">
                                                                <h6 class="fs-14 mb-1">                                                                                    
                                                                    Suggestion
                                                                </h6>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                                <div id="collapseSix" class="accordion-collapse collapse show" aria-labelledby="headingSix" data-bs-parent="#accordionExample">
                                                    <div id="saran">
                                                    </div>
                                                </div> 
                                            </div> 
                                        </div>
                                        <!--end accordion-->
                                    </div>
                                </div>
                                <div class="tab-pane" id="hematologi" role="tabpanel">
                                    <h5 class="card-title mb-3">Hematologi Information</h5>
                                    <div class="row">
                                        <div class="col-3">
                                            <div class="d-flex mt-4">
                                            
                                                <div class="flex-grow-1">
                                                    <p class="mb-1">Hemoglobin :</p>
                                                    <div id="hm_hemoglobin">
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_hm_hemoglobin">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end col-->
                                        <div class="col-3">
                                            <div class="d-flex mt-4">            
                                                <div class="flex-grow-1">
                                                    <p class="mb-1">Eritrosit :</p>
                                                    <div id="hm_eritrosit">
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_hm_eritrosit">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end col-->
                                        <div class="col-3">
                                            <div class="d-flex mt-4">
        
                                                <div class="flex-grow-1">
                                                    <p class="mb-1">Hematrokit :</p>
                                                    <div id="hm_hematokrit">
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_hm_hematokrit">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end col-->
                                        <div class="col-3">
                                            <div class="d-flex mt-4">
        
                                                <div class="flex-grow-1">
                                                    <p class="mb-1">MCV :</p>
                                                    <div id="hm_mcv">
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_hm_mcv">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end col-->                                                                                             
                                    </div>
                                    <!--end row-->
                                    <div class="row">
                                        <div class="col-3">
                                            <div class="d-flex mt-4">
        
                                                <div class="flex-grow-1">
                                                    <p class="mb-1">MCH :</p>
                                                    <div id="hm_mch">
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_hm_mch">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end col-->   
                                        <div class="col-3">
                                            <div class="d-flex mt-4">            
                                                <div class="flex-grow-1">
                                                    <p class="mb-1">MCHC :</p>
                                                    <div id="hm_mchc">
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_hm_mchc">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end col-->
                                        <div class="col-3">
                                            <div class="d-flex mt-4">
                                            
                                                <div class="flex-grow-1">
                                                    <p class="mb-1">RDW :</p>
                                                    <div id="hm_rdw">
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_hm_rdw">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end col-->
                                        <div class="col-3">
                                            <div class="d-flex mt-4">
        
                                                <div class="flex-grow-1">
                                                    <p class="mb-1">Leukosit :</p>
                                                    <div id="hm_leukosit">
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_hm_leukosit">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end col-->                                                                                            
                                    </div>
                                    <!--end row-->
                                    <div class="row">
                                        <div class="col-3">
                                            <div class="d-flex mt-4">            
                                                <div class="flex-grow-1">
                                                    <p class="mb-1">EOS :</p>
                                                    <div id="hm_eos">
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_hm_eos">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end col-->
                                        <div class="col-3">
                                            <div class="d-flex mt-4">            
                                                <div class="flex-grow-1">
                                                    <p class="mb-1">BASO :</p>
                                                    <div id="hm_baso">
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_hm_baso">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end col-->    
                                        <div class="col-3">
                                            <div class="d-flex mt-4">            
                                                <div class="flex-grow-1">
                                                    <p class="mb-1">Neutro :</p>
                                                    <div id="hm_neutro">
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_hm_neutro">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end col-->
                                        <div class="col-3">
                                            <div class="d-flex mt-4">            
                                                <div class="flex-grow-1">
                                                    <p class="mb-1">Limfo :</p>
                                                    <div id="hm_limfo">
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_hm_limfo">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end col-->                                                                                            
                                    </div>
                                    <!--end row-->
                                    <div class="row">
                                        <div class="col-3">
                                            <div class="d-flex mt-4">                                                
                                                <div class="flex-grow-1">
                                                    <p class="mb-1">Mono :</p>
                                                    <div id="hm_mono">
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_hm_mono">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end col-->
                                        <div class="col-3">
                                            <div class="d-flex mt-4">            
                                                <div class="flex-grow-1">
                                                    <p class="mb-1">EOS Absolut :</p>
                                                    <div id="hm_eos_absolut">
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_hm_eos_absolut">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end col-->
                                        <div class="col-3">
                                            <div class="d-flex mt-4">            
                                                <div class="flex-grow-1">
                                                    <p class="mb-1">BASO Absolut :</p>
                                                    <div id="hm_baso_absolut">
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_hm_baso_absolut">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end col-->    
                                        <div class="col-3">
                                            <div class="d-flex mt-4">            
                                                <div class="flex-grow-1">
                                                    <p class="mb-1">Neutro Absolut :</p>
                                                    <div id="hm_neutro_absolut">
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_hm_neutro_absolut">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end col-->                                                
                                    </div>
                                    <!--end row-->
                                    <div class="row">
                                        <div class="col-3">
                                            <div class="d-flex mt-4">            
                                                <div class="flex-grow-1">
                                                    <p class="mb-1">Limfo Absolut :</p>
                                                    <div id="hm_limfo_absolut">
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_hm_limfo_absolut">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end col-->
                                        <div class="col-3">
                                            <div class="d-flex mt-4">            
                                                <div class="flex-grow-1">
                                                    <p class="mb-1">Mono Absolut :</p>
                                                    <div id="hm_mono_absolut">
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_hm_mono_absolut">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end col-->
                                        <div class="col-3">
                                            <div class="d-flex mt-4">            
                                                <div class="flex-grow-1">
                                                    <p class="mb-1">Trombosit :</p>
                                                    <div id="hm_trombosit">
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_hm_trombosit">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end col-->
                                        <div class="col-3">
                                            <div class="d-flex mt-4">
        
                                                <div class="flex-grow-1">
                                                    <p class="mb-1">LED :</p>
                                                    <div id="hm_led">
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_hm_led">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end col-->
                                    </div>
                                    <!--end row-->
                                </div>
                                <div class="tab-pane" id="urine" role="tabpanel">
                                    <h5 class="card-title mb-3">Urine Information</h5>
                                    <div class="row">
                                        <div class="col-3">
                                            <div class="d-flex mt-4">
                                            
                                                <div class="flex-grow-1">
                                                    <p class="mb-1">Warna :</p>
                                                    <div id="u_warna">
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_u_warna">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end col-->
                                        <div class="col-3">
                                            <div class="d-flex mt-4">            
                                                <div class="flex-grow-1">
                                                    <p class="mb-1">Kejernihan :</p>
                                                    <div id="u_kejernihan">
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_u_kejernihan">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end col-->
                                        <div class="col-3">
                                            <div class="d-flex mt-4">            
                                                <div class="flex-grow-1">
                                                    <p class="mb-1">Berat Jenis :</p>
                                                    <div id="u_berat_jenis">
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_u_berat_jenis">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end col-->
                                        <div class="col-3">
                                            <div class="d-flex mt-4">            
                                                <div class="flex-grow-1">
                                                    <p class="mb-1">PH :</p>
                                                    <div id="u_ph">
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_u_ph">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end col-->                                                                                             
                                    </div>
                                    <!--end row-->
                                    <div class="row">
                                        <div class="col-3">
                                            <div class="d-flex mt-4">            
                                                <div class="flex-grow-1">
                                                    <p class="mb-1">Protein Albumin :</p>
                                                    <div id="u_protein_albumin">
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_u_protein_albumin">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end col-->   
                                        <div class="col-3">
                                            <div class="d-flex mt-4">            
                                                <div class="flex-grow-1">
                                                    <p class="mb-1">Glukosa :</p>
                                                    <div id="u_glukosa">
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_u_glukosa">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end col-->
                                        <div class="col-3">
                                            <div class="d-flex mt-4">                                                
                                                <div class="flex-grow-1">
                                                    <p class="mb-1">Keton :</p>
                                                    <div id="u_keton">
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_u_keton">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end col-->
                                        <div class="col-3">
                                            <div class="d-flex mt-4">            
                                                <div class="flex-grow-1">
                                                    <p class="mb-1">Bilirubin :</p>
                                                    <div id="u_bilirubin">
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_u_bilirubin">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end col-->                                                                                            
                                    </div>
                                    <!--end row-->
                                    <div class="row">
                                        <div class="col-3">
                                            <div class="d-flex mt-4">            
                                                <div class="flex-grow-1">
                                                    <p class="mb-1">Urobilinogen :</p>
                                                    <div id="u_urobilinogen">
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_u_urobilinogen">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end col-->
                                        <div class="col-3">
                                            <div class="d-flex mt-4">            
                                                <div class="flex-grow-1">
                                                    <p class="mb-1">Nitrit :</p>
                                                    <div id="u_nitrit">
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_u_nitrit">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end col-->    
                                        <div class="col-3">
                                            <div class="d-flex mt-4">            
                                                <div class="flex-grow-1">
                                                    <p class="mb-1">Leukosit Esterase :</p>
                                                    <div id="u_leukosit_esterase">
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_u_leukosit_esterase">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end col-->
                                        <div class="col-3">
                                            <div class="d-flex mt-4">            
                                                <div class="flex-grow-1">
                                                    <p class="mb-1">Darah Haem :</p>
                                                    <div id="u_darah_haem">
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_u_darah_haem">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end col-->                                                                                            
                                    </div>
                                    <!--end row-->
                                    <div class="row">
                                        <div class="col-3">
                                            <div class="d-flex mt-4">                                                
                                                <div class="flex-grow-1">
                                                    <p class="mb-1">Eri :</p>
                                                    <div id="u_eri">
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_u_eri">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end col-->
                                        <div class="col-3">
                                            <div class="d-flex mt-4">            
                                                <div class="flex-grow-1">
                                                    <p class="mb-1">Leuko :</p>
                                                    <div id="u_leuko">
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_u_leuko">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end col-->
                                        <div class="col-3">
                                            <div class="d-flex mt-4">            
                                                <div class="flex-grow-1">
                                                    <p class="mb-1">Epithel :</p>
                                                    <div id="u_epithel">
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_u_epithel">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end col-->    
                                        <div class="col-3">
                                            <div class="d-flex mt-4">            
                                                <div class="flex-grow-1">
                                                    <p class="mb-1">Silinder :</p>
                                                    <div id="u_silinder">
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_u_silinder">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end col-->                                                
                                    </div>
                                    <!--end row-->
                                    <div class="row">
                                        <div class="col-3">
                                            <div class="d-flex mt-4">            
                                                <div class="flex-grow-1">
                                                    <p class="mb-1">Kristal :</p>
                                                    <div id="u_kristal">
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_u_kristal">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end col-->
                                        <div class="col-3">
                                            <div class="d-flex mt-4">            
                                                <div class="flex-grow-1">
                                                    <p class="mb-1">Lain-lain :</p>
                                                    <div id="u_lain">
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_u_lain">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end col-->
                                    </div>
                                    <!--end row-->
                                </div>
                                <div class="tab-pane" id="faal" role="tabpanel">
                                    <h5 class="card-title mb-3">Faal Information</h5>
                                    <div class="row">
                                        <div class="col-3">
                                            <div class="d-flex mt-4">                                                
                                                <div class="flex-grow-1">
                                                    <p class="mb-1">SGOT :</p>
                                                    <div id="fh_sgot">
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_fh_sgot">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end col-->
                                        <div class="col-3">
                                            <div class="d-flex mt-4">            
                                                <div class="flex-grow-1">
                                                    <p class="mb-1">SGPT :</p>
                                                    <div id="fh_sgpt">
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_fh_sgpt">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end col-->
                                        <div class="col-3">
                                            <div class="d-flex mt-4">            
                                                <div class="flex-grow-1">
                                                    <p class="mb-1">Kolesterol Total :</p>
                                                    <div id="fl_kolesterol_total">
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_fl_kolesterol_total">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end col-->
                                        <div class="col-3">
                                            <div class="d-flex mt-4">            
                                                <div class="flex-grow-1">
                                                    <p class="mb-1">HDL Kolesterol :</p>
                                                    <div id="fl_hdl_kolesterol">
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_fl_hdl_kolesterol">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end col-->                                                                                             
                                    </div>
                                    <!--end row-->
                                    <div class="row">
                                        <div class="col-3">
                                            <div class="d-flex mt-4">            
                                                <div class="flex-grow-1">
                                                    <p class="mb-1">LDL Kolesterol :</p>
                                                    <div id="fl_ldl_kolesterol">
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_fl_ldl_kolesterol">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end col-->   
                                        <div class="col-3">
                                            <div class="d-flex mt-4">            
                                                <div class="flex-grow-1">
                                                    <p class="mb-1">Trigliserida :</p>
                                                    <div id="fl_trigliserida">
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_fl_trigliserida">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end col-->
                                        <div class="col-3">
                                            <div class="d-flex mt-4">                                                
                                                <div class="flex-grow-1">
                                                    <p class="mb-1">Glukosa Puasa :</p>
                                                    <div id="gd_glukosa_puasa">
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_gd_glukosa_puasa">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end col-->
                                        <div class="col-3">
                                            <div class="d-flex mt-4">            
                                                <div class="flex-grow-1">
                                                    <p class="mb-1">JPP :</p>
                                                    <div id="gd_jpp">
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_gd_jpp">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end col-->                                                                                            
                                    </div>
                                    <!--end row-->
                                    <div class="row">
                                        <div class="col-3">
                                            <div class="d-flex mt-4">            
                                                <div class="flex-grow-1">
                                                    <p class="mb-1">BUN :</p>
                                                    <div id="fg_bun">
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_fg_bun">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end col-->
                                        <div class="col-3">
                                            <div class="d-flex mt-4">            
                                                <div class="flex-grow-1">
                                                    <p class="mb-1">Ureum :</p>
                                                    <div id="fg_ureum">
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_fg_ureum">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end col-->    
                                        <div class="col-3">
                                            <div class="d-flex mt-4">            
                                                <div class="flex-grow-1">
                                                    <p class="mb-1">Kreatinin :</p>
                                                    <div id="fg_kreatinin">
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_fg_kreatinin">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end col-->
                                        <div class="col-3">
                                            <div class="d-flex mt-4">            
                                                <div class="flex-grow-1">
                                                    <p class="mb-1">EGFR :</p>
                                                    <div id="fg_egfr">
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_fg_egfr">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end col-->                                                                                            
                                    </div>
                                    <!--end row-->
                                    <div class="row">
                                        <div class="col-3">
                                            <div class="d-flex mt-4">                                                
                                                <div class="flex-grow-1">
                                                    <p class="mb-1">Asam Urat :</p>
                                                    <div id="asam_urat">
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_asam_urat">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end col-->
                                        <div class="col-3">
                                            <div class="d-flex mt-4">            
                                                <div class="flex-grow-1">
                                                    <p class="mb-1">HBSAG :</p>
                                                    <div id="hbsag">
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_hbsag">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end col-->                                                                                            
                                    </div>
                                    <!--end row-->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>                
            </div>
        </form>
      </div>
    </div>
  </div>
  <!--end col-->
</div>
<!--modal preview mcu-->
<div class="modal flip" id="modal-preview" tabindex="-1" aria-labelledby="exampleModalgridLabel" aria-modal="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-judul"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <embed id="show-preview" src="" frameborder="0" width="100%" height="450px">
            </div>
            <div class="modal-footer">
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
         $("#date_mcu").on('change', function() {
            var date_mcu = this.value;
            var id_employee = $('#id_employee').val();
            $.ajax({
                url: "{{ route('clinic.patient.medical.mcu.pdf') }}",
                type: "POST",
                data: {
                    date_mcu: date_mcu,
                    id_employee: id_employee,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(result) {
                    $("#preview-mcu").html('');
                    if(result.url_mcu == null || result.url_mcu == ''){
                        $("#preview-mcu").html('');
                    }else{
                        $("#preview-mcu").append('<button type="button" data-bs-toggle="modal" data-bs-target="#modal-preview" class="btn btn-info btn-sm"><i class="ri-zoom-in-line align-bottom"></i> Document MCU</button>');
                        $("#show-preview").attr("src", result.url_mcu);
                    }
                }
            });
         });
    </script>
    <script>
        // This code empowers all input tags having a placeholder and data-slots attribute
        document.addEventListener('DOMContentLoaded', () => {
            for (const el of document.querySelectorAll("[placeholder][data-slots]")) {
                const pattern = el.getAttribute("placeholder"),
                    slots = new Set(el.dataset.slots || "_"),
                    prev = (j => Array.from(pattern, (c,i) => slots.has(c)? j=i+1: j))(0),
                    first = [...pattern].findIndex(c => slots.has(c)),
                    accept = new RegExp(el.dataset.accept || "\\d", "g"),
                    clean = input => {
                        input = input.match(accept) || [];
                        return Array.from(pattern, c =>
                            input[0] === c || slots.has(c) ? input.shift() || c : c
                        );
                    },
                    format = () => {
                        const [i, j] = [el.selectionStart, el.selectionEnd].map(i => {
                            i = clean(el.value.slice(0, i)).findIndex(c => slots.has(c));
                            return i<0? prev.at(-1) : back ? prev[i-1] || first : i;
                        });
                        el.value = clean(el.value).join("");
                        el.setSelectionRange(i, j);
                        back = false;
                    };
                let back = false;
                el.addEventListener("keydown", (e) => back = e.key === "Backspace");
                el.addEventListener("input", format);
                el.addEventListener("focus", format);
                el.addEventListener("blur", () => el.value === pattern && (el.value=""));
            }
        });
    </script>
    <script>
        $('#id_employee').on("change", function() {
            var id_emp = this.value;
            $.ajax({
            url: "{{ route('clinic.patient.medical.year') }}",
            method: "POST",
            data: {
                "_token": "{{ csrf_token() }}",
                id_emp: id_emp
            },
            success: function(result) {                
                $("#date_mcu").html('');
                $("#date_mcu").html('<option selected="true" disabled="true"></option>');
                $.each(result, function (key, val) {
                    $("#date_mcu").append('<option value="'+val+'">'+val+'</option>');
                    $("#date_mcu").trigger('change');
                });

                //medical checkup
                $("#lab").html('');                                                       
                    $("#foto_thorax").html('');     
                    $("#audiometri").html('');                                                                                      
                    $("#ekg").html('');                                                       
                    $("#fisik_dokter").html('');                                                       
                    $("#kesimpulan").html('');                                                       
                    $("#saran").html('');
                    //hematologi
                    $("#hm_hemoglobin").html('');
                    $("#hm_eritrosit").html('');
                    $("#hm_hematokrit").html('');
                    $("#hm_mcv").html('');
                    $("#hm_mch").html('');
                    $("#hm_mchc").html('');
                    $("#hm_rdw").html('');
                    $("#hm_leukosit").html('');
                    $("#hm_eos").html('');
                    $("#hm_baso").html('');
                    $("#hm_neutro").html('');
                    $("#hm_limfo").html('');
                    $("#hm_mono").html('');
                    $("#hm_eos_absolut").html('');
                    $("#hm_baso_absolut").html('');
                    $("#hm_neutro_absolut").html('');
                    $("#hm_limfo_absolut").html('');
                    $("#hm_mono_absolut").html('');
                    $("#hm_trombosit").html('');
                    $("#hm_led").html('');
                    //urine
                    $("#u_warna").html('');
                    $("#u_kejernihan").html('');
                    $("#u_berat_jenis").html('');
                    $("#u_ph").html('');
                    $("#u_protein_albumin").html('');
                    $("#u_glukosa").html('');
                    $("#u_keton").html('');
                    $("#u_bilirubin").html('');
                    $("#u_urobilinogen").html('');
                    $("#u_nitrit").html('');
                    $("#u_leukosit_esterase").html('');
                    $("#u_darah_haem").html('');
                    $("#u_eri").html('');
                    $("#u_leuko").html('');
                    $("#u_epithel").html('');
                    $("#u_silinder").html('');
                    $("#u_kristal").html('');
                    $("#u_lain").html('');
                    //faal
                    $("#fh_sgot").html('');
                    $("#fh_sgpt").html('');
                    $("#fl_kolesterol_total").html('');
                    $("#fl_hdl_kolesterol").html('');
                    $("#fl_ldl_kolesterol").html('');
                    $("#fl_trigliserida").html('');
                    $("#gd_glukosa_puasa").html('');
                    $("#gd_jpp").html('');
                    $("#fg_bun").html('');
                    $("#fg_ureum").html('');
                    $("#fg_kreatinin").html('');
                    $("#fg_egfr").html('');
                    $("#asam_urat").html('');
                    $("#hbsag").html('');
                    //nilai rujukan
                    $("#nr_hm_hemoglobin").html('');
                    $("#nr_hm_eritrosit").html('');
                    $("#nr_hm_hematokrit").html('');
                    $("#nr_hm_mcv").html('');
                    $("#nr_hm_mch").html('');
                    $("#nr_hm_mchc").html('');
                    $("#nr_hm_rdw").html('');
                    $("#nr_hm_leukosit").html('');
                    $("#nr_hm_eos").html('');
                    $("#nr_hm_baso").html('');
                    $("#nr_hm_neutro").html('');
                    $("#nr_hm_limfo").html('');
                    $("#nr_hm_mono").html('');
                    $("#nr_hm_eos_absolut").html('');
                    $("#nr_hm_baso_absolut").html('');
                    $("#nr_hm_neutro_absolut").html('');
                    $("#nr_hm_limfo_absolut").html('');
                    $("#nr_hm_mono_absolut").html('');
                    $("#nr_hm_trombosit").html('');
                    $("#nr_hm_led").html('');
                    $("#nr_u_warna").html('');
                    $("#nr_u_kejernihan").html('');
                    $("#nr_u_berat_jenis").html('');
                    $("#nr_u_ph").html('');
                    $("#nr_u_protein_albumin").html('');
                    $("#nr_u_glukosa").html('');
                    $("#nr_u_keton").html('');
                    $("#nr_u_bilirubin").html('');
                    $("#nr_u_urobilinogen").html('');
                    $("#nr_u_nitrit").html('');
                    $("#nr_u_leukosit_esterase").html('');
                    $("#nr_u_darah_haem").html('');
                    $("#nr_u_eri").html('');
                    $("#nr_u_leuko").html('');
                    $("#nr_u_epithel").html('');
                    $("#nr_u_silinder").html('');
                    $("#nr_u_kristal").html('');
                    $("#nr_u_lain").html('');
                    $("#nr_fh_sgot").html('');
                    $("#nr_fh_sgpt").html('');
                    $("#nr_fl_kolesterol_total").html('');
                    $("#nr_fl_hdl_kolesterol").html('');
                    $("#nr_fl_ldl_kolesterol").html('');
                    $("#nr_fl_trigliserida").html('');
                    $("#nr_gd_glukosa_puasa").html('');
                    $("#nr_gd_jpp").html('');
                    $("#nr_fg_bun").html('');
                    $("#nr_fg_ureum").html('');
                    $("#nr_fg_kreatinin").html('');
                    $("#nr_fg_egfr").html('');
                    $("#nr_asam_urat").html('');
                    $("#nr_hbsag").html('');
            },
            error: function(xhr, status, error) {
                console.log(xhr, status, error);
            }
            });
        });
    </script>
    <script>
        $('#date_mcu').on("change", function() {
            var id_employee = $('#id_employee').val();
            var year_mcu = this.value;
            $.ajax({
            url: "{{ route('clinic.patient.medical') }}",
            method: "POST",
            data: {
                "_token": "{{ csrf_token() }}",
                id_employee: id_employee,
                year_mcu: year_mcu
            },
            success: function(result) {        
                if(result != ''){
                    //medical information
                    $("#lab").html('<div class="accordion-body ms-2 ps-5">'+result.lab+'</div>');                                                       
                    $("#foto_thorax").html('<div class="accordion-body ms-2 ps-5">'+result.foto_thorax+'</div>');
                    if(!result.audiometri){
                        $("#audiometri").html('<div class="accordion-body ms-2 ps-5">-</div>');                                                       
                    }else{
                        $("#audiometri").html('<div class="accordion-body ms-2 ps-5">'+result.audiometri+'</div>');                                                       
                    }                                 
                    if(!result.ekg){
                        $("#ekg").html('<div class="accordion-body ms-2 ps-5">-</div>');                                                       
                    }else{
                        $("#ekg").html('<div class="accordion-body ms-2 ps-5">'+result.ekg+'</div>');                                                       
                    }                                 
                    $("#fisik_dokter").html('<div class="accordion-body ms-2 ps-5">'+result.fisik_dokter+'</div>');                                                       
                    $("#kesimpulan").html('<div class="accordion-body ms-2 ps-5">'+result.kesimpulan+'</div>');                                                       
                    $("#saran").html('<div class="accordion-body ms-2 ps-5">'+result.saran+'</div>');
                    //hematologi
                    $("#hm_hemoglobin").html('<h6>'+result.hm_hemoglobin+'</h6>');
                    $("#hm_eritrosit").html('<h6>'+result.hm_eritrosit+'</h6>');
                    $("#hm_hematokrit").html('<h6>'+result.hm_hematokrit+'</h6>');
                    $("#hm_mcv").html('<h6>'+result.hm_mcv+'</h6>');
                    $("#hm_mch").html('<h6>'+result.hm_mch+'</h6>');
                    $("#hm_mchc").html('<h6>'+result.hm_mchc+'</h6>');
                    $("#hm_rdw").html('<h6>'+result.hm_rdw+'</h6>');
                    $("#hm_leukosit").html('<h6>'+result.hm_leukosit+'</h6>');
                    $("#hm_eos").html('<h6>'+result.hm_eos+'</h6>');
                    $("#hm_baso").html('<h6>'+result.hm_baso+'</h6>');
                    $("#hm_neutro").html('<h6>'+result.hm_neutro+'</h6>');
                    $("#hm_limfo").html('<h6>'+result.hm_limfo+'</h6>');
                    $("#hm_mono").html('<h6>'+result.hm_mono+'</h6>');
                    $("#hm_eos_absolut").html('<h6>'+result.hm_eos_absolut+'</h6>');
                    $("#hm_baso_absolut").html('<h6>'+result.hm_baso_absolut+'</h6>');
                    $("#hm_neutro_absolut").html('<h6>'+result.hm_neutro_absolut+'</h6>');
                    $("#hm_limfo_absolut").html('<h6>'+result.hm_limfo_absolut+'</h6>');
                    $("#hm_mono_absolut").html('<h6>'+result.hm_mono_absolut+'</h6>');
                    $("#hm_trombosit").html('<h6>'+result.hm_trombosit+'</h6>');
                    $("#hm_led").html('<h6>'+result.hm_led+'</h6>');
                    //urine
                    $("#u_warna").html('<h6>'+result.u_warna+'</h6>');
                    $("#u_kejernihan").html('<h6>'+result.u_kejernihan+'</h6>');
                    $("#u_berat_jenis").html('<h6>'+result.u_berat_jenis+'</h6>');
                    $("#u_ph").html('<h6>'+result.u_ph+'</h6>');
                    $("#u_protein_albumin").html('<h6>'+result.u_protein_albumin+'</h6>');
                    $("#u_glukosa").html('<h6>'+result.u_glukosa+'</h6>');
                    $("#u_keton").html('<h6>'+result.u_keton+'</h6>');
                    $("#u_bilirubin").html('<h6>'+result.u_bilirubin+'</h6>');
                    $("#u_urobilinogen").html('<h6>'+result.u_urobilinogen+'</h6>');
                    $("#u_nitrit").html('<h6>'+result.u_nitrit+'</h6>');
                    $("#u_leukosit_esterase").html('<h6>'+result.u_leukosit_esterase+'</h6>');
                    $("#u_darah_haem").html('<h6>'+result.u_darah_haem+'</h6>');
                    $("#u_eri").html('<h6>'+result.u_eri+'</h6>');
                    $("#u_leuko").html('<h6>'+result.u_leuko+'</h6>');
                    $("#u_epithel").html('<h6>'+result.u_epithel+'</h6>');
                    $("#u_silinder").html('<h6>'+result.u_silinder+'</h6>');
                    $("#u_kristal").html('<h6>'+result.u_kristal+'</h6>');
                    $("#u_lain").html('<h6>'+result.u_lain+'</h6>');
                    //faal
                    $("#fh_sgot").html('<h6>'+result.fh_sgot+'</h6>');
                    $("#fh_sgpt").html('<h6>'+result.fh_sgpt+'</h6>');
                    $("#fl_kolesterol_total").html('<h6>'+result.fl_kolesterol_total+'</h6>');
                    $("#fl_hdl_kolesterol").html('<h6>'+result.fl_hdl_kolesterol+'</h6>');
                    $("#fl_ldl_kolesterol").html('<h6>'+result.fl_ldl_kolesterol+'</h6>');
                    $("#fl_trigliserida").html('<h6>'+result.fl_trigliserida+'</h6>');
                    $("#gd_glukosa_puasa").html('<h6>'+result.gd_glukosa_puasa+'</h6>');
                    $("#gd_jpp").html('<h6>'+result.gd_jpp+'</h6>');
                    $("#fg_bun").html('<h6>'+result.fg_bun+'</h6>');
                    $("#fg_ureum").html('<h6>'+result.fg_ureum+'</h6>');
                    $("#fg_kreatinin").html('<h6>'+result.fg_kreatinin+'</h6>');
                    $("#fg_egfr").html('<h6>'+result.fg_egfr+'</h6>');
                    $("#asam_urat").html('<h6>'+result.asam_urat+'</h6>');
                    $("#hbsag").html('<h6>'+result.hbsag+'</h6>');
                    //nilai rujukan
                    $.each(result.master_lab, function(key, value) {
                        //hematologi
                        if(key == 'hm_hemoglobin'){
                            $("#nr_hm_hemoglobin").html('<h6>'+value+'</h6>');
                        }
                        if(key == 'hm_eritrosit'){
                            $("#nr_hm_eritrosit").html('<h6>'+value+'</h6>');
                        }
                        if(key == 'hm_hematokrit'){
                            $("#nr_hm_hematokrit").html('<h6>'+value+'</h6>');
                        }
                        if(key == 'hm_mcv'){
                            $("#nr_hm_mcv").html('<h6>'+value+'</h6>');
                        }
                        if(key == 'hm_mch'){
                            $("#nr_hm_mch").html('<h6>'+value+'</h6>');
                        }
                        if(key == 'hm_mchc'){
                            $("#nr_hm_mchc").html('<h6>'+value+'</h6>');
                        }
                        if(key == 'hm_rdw'){
                            $("#nr_hm_rdw").html('<h6>'+value+'</h6>');
                        }
                        if(key == 'hm_leukosit'){
                            $("#nr_hm_leukosit").html('<h6>'+value+'</h6>');
                        }
                        if(key == 'hm_eos'){
                            $("#nr_hm_eos").html('<h6>'+value+'</h6>');
                        }
                        if(key == 'hm_baso'){
                            $("#nr_hm_baso").html('<h6>'+value+'</h6>');
                        }
                        if(key == 'hm_neutro'){
                            $("#nr_hm_neutro").html('<h6>'+value+'</h6>');
                        }
                        if(key == 'hm_limfo'){
                            $("#nr_hm_limfo").html('<h6>'+value+'</h6>');
                        }
                        if(key == 'hm_mono'){
                            $("#nr_hm_mono").html('<h6>'+value+'</h6>');
                        }
                        if(key == 'hm_eos_absolut'){
                            $("#nr_hm_eos_absolut").html('<h6>'+value+'</h6>');
                        }
                        if(key == 'hm_baso_absolut'){
                            $("#nr_hm_baso_absolut").html('<h6>'+value+'</h6>');
                        }
                        if(key == 'hm_neutro_absolut'){
                            $("#nr_hm_neutro_absolut").html('<h6>'+value+'</h6>');
                        }
                        if(key == 'hm_limfo_absolut'){
                            $("#nr_hm_limfo_absolut").html('<h6>'+value+'</h6>');
                        }
                        if(key == 'hm_mono_absolut'){
                            $("#nr_hm_mono_absolut").html('<h6>'+value+'</h6>');
                        }
                        if(key == 'hm_trombosit'){
                            $("#nr_hm_trombosit").html('<h6>'+value+'</h6>');
                        }
                        if(key == 'hm_led'){
                            $("#nr_hm_led").html('<h6>'+value+'</h6>');
                        }
                        //urine
                        if(key == 'u_warna'){
                            $("#nr_u_warna").html('<h6>'+value+'</h6>');
                        }
                        if(key == 'u_kejernihan'){
                            $("#nr_u_kejernihan").html('<h6>'+value+'</h6>');
                        }
                        if(key == 'u_berat_jenis'){
                            $("#nr_u_berat_jenis").html('<h6>'+value+'</h6>');
                        }
                        if(key == 'u_ph'){
                            $("#nr_u_ph").html('<h6>'+value+'</h6>');
                        }
                        if(key == 'u_protein_albumin'){
                            $("#nr_u_protein_albumin").html('<h6>'+value+'</h6>');
                        }
                        if(key == 'u_glukosa'){
                            $("#nr_u_glukosa").html('<h6>'+value+'</h6>');
                        }
                        if(key == 'u_keton'){
                            $("#nr_u_keton").html('<h6>'+value+'</h6>');
                        }
                        if(key == 'u_bilirubin'){
                            $("#nr_u_bilirubin").html('<h6>'+value+'</h6>');
                        }
                        if(key == 'u_urobilinogen'){
                            $("#nr_u_urobilinogen").html('<h6>'+value+'</h6>');
                        }
                        if(key == 'u_nitrit'){
                            $("#nr_u_nitrit").html('<h6>'+value+'</h6>');
                        }
                        if(key == 'u_leukosit_esterase'){
                            $("#nr_u_leukosit_esterase").html('<h6>'+value+'</h6>');
                        }
                        if(key == 'u_darah_haem'){
                            $("#nr_u_darah_haem").html('<h6>'+value+'</h6>');
                        }
                        if(key == 'u_eri'){
                            $("#nr_u_eri").html('<h6>'+value+'</h6>');
                        }
                        if(key == 'u_leuko'){
                            $("#nr_u_leuko").html('<h6>'+value+'</h6>');
                        }
                        if(key == 'u_epithel'){
                            $("#nr_u_epithel").html('<h6>'+value+'</h6>');
                        }
                        if(key == 'u_silinder'){
                            $("#nr_u_silinder").html('<h6>'+value+'</h6>');
                        }
                        if(key == 'u_kristal'){
                            $("#nr_u_kristal").html('<h6>'+value+'</h6>');
                        }
                        if(key == 'u_lain'){
                            $("#nr_u_lain").html('<h6>'+value+'</h6>');
                        }
                        //faal
                        if(key == 'fh_sgot'){
                            $("#nr_fh_sgot").html('<h6>'+value+'</h6>');
                        }
                        if(key == 'fh_sgpt'){
                            $("#nr_fh_sgpt").html('<h6>'+value+'</h6>');
                        }
                        if(key == 'fl_kolesterol_total'){
                            $("#nr_fl_kolesterol_total").html('<h6>'+value+'</h6>');
                        }
                        if(key == 'fl_hdl_kolesterol'){
                            $("#nr_fl_hdl_kolesterol").html('<h6>'+value+'</h6>');
                        }
                        if(key == 'fl_ldl_kolesterol'){
                            $("#nr_fl_ldl_kolesterol").html('<h6>'+value+'</h6>');
                        }
                        if(key == 'fl_trigliserida'){
                            $("#nr_fl_trigliserida").html('<h6>'+value+'</h6>');
                        }
                        if(key == 'gd_glukosa_puasa'){
                            $("#nr_gd_glukosa_puasa").html('<h6>'+value+'</h6>');
                        }
                        if(key == 'gd_jpp'){
                            $("#nr_gd_jpp").html('<h6>'+value+'</h6>');
                        }
                        if(key == 'fg_bun'){
                            $("#nr_fg_bun").html('<h6>'+value+'</h6>');
                        }
                        if(key == 'fg_ureum'){
                            $("#nr_fg_ureum").html('<h6>'+value+'</h6>');
                        }
                        if(key == 'fg_kreatinin'){
                            $("#nr_fg_kreatinin").html('<h6>'+value+'</h6>');
                        }
                        if(key == 'fg_egfr'){
                            $("#nr_fg_egfr").html('<h6>'+value+'</h6>');
                        }
                        if(key == 'asam_urat'){
                            $("#nr_asam_urat").html('<h6>'+value+'</h6>');
                        }
                        if(key == 'hbsag'){
                            $("#nr_hbsag").html('<h6>'+value+'</h6>');
                        }
                    }); 
                }else{
                    $("#lab").html('');                                                       
                    $("#foto_thorax").html('');     
                    $("#audiometri").html('');                                                                                      
                    $("#ekg").html('');                                                       
                    $("#fisik_dokter").html('');                                                       
                    $("#kesimpulan").html('');                                                       
                    $("#saran").html('');
                    //hematologi
                    $("#hm_hemoglobin").html('');
                    $("#hm_eritrosit").html('');
                    $("#hm_hematokrit").html('');
                    $("#hm_mcv").html('');
                    $("#hm_mch").html('');
                    $("#hm_mchc").html('');
                    $("#hm_rdw").html('');
                    $("#hm_leukosit").html('');
                    $("#hm_eos").html('');
                    $("#hm_baso").html('');
                    $("#hm_neutro").html('');
                    $("#hm_limfo").html('');
                    $("#hm_mono").html('');
                    $("#hm_eos_absolut").html('');
                    $("#hm_baso_absolut").html('');
                    $("#hm_neutro_absolut").html('');
                    $("#hm_limfo_absolut").html('');
                    $("#hm_mono_absolut").html('');
                    $("#hm_trombosit").html('');
                    $("#hm_led").html('');
                    //urine
                    $("#u_warna").html('');
                    $("#u_kejernihan").html('');
                    $("#u_berat_jenis").html('');
                    $("#u_ph").html('');
                    $("#u_protein_albumin").html('');
                    $("#u_glukosa").html('');
                    $("#u_keton").html('');
                    $("#u_bilirubin").html('');
                    $("#u_urobilinogen").html('');
                    $("#u_nitrit").html('');
                    $("#u_leukosit_esterase").html('');
                    $("#u_darah_haem").html('');
                    $("#u_eri").html('');
                    $("#u_leuko").html('');
                    $("#u_epithel").html('');
                    $("#u_silinder").html('');
                    $("#u_kristal").html('');
                    $("#u_lain").html('');
                    //faal
                    $("#fh_sgot").html('');
                    $("#fh_sgpt").html('');
                    $("#fl_kolesterol_total").html('');
                    $("#fl_hdl_kolesterol").html('');
                    $("#fl_ldl_kolesterol").html('');
                    $("#fl_trigliserida").html('');
                    $("#gd_glukosa_puasa").html('');
                    $("#gd_jpp").html('');
                    $("#fg_bun").html('');
                    $("#fg_ureum").html('');
                    $("#fg_kreatinin").html('');
                    $("#fg_egfr").html('');
                    $("#asam_urat").html('');
                    $("#hbsag").html('');
                    //nilai rujukan
                    $("#nr_hm_hemoglobin").html('');
                    $("#nr_hm_eritrosit").html('');
                    $("#nr_hm_hematokrit").html('');
                    $("#nr_hm_mcv").html('');
                    $("#nr_hm_mch").html('');
                    $("#nr_hm_mchc").html('');
                    $("#nr_hm_rdw").html('');
                    $("#nr_hm_leukosit").html('');
                    $("#nr_hm_eos").html('');
                    $("#nr_hm_baso").html('');
                    $("#nr_hm_neutro").html('');
                    $("#nr_hm_limfo").html('');
                    $("#nr_hm_mono").html('');
                    $("#nr_hm_eos_absolut").html('');
                    $("#nr_hm_baso_absolut").html('');
                    $("#nr_hm_neutro_absolut").html('');
                    $("#nr_hm_limfo_absolut").html('');
                    $("#nr_hm_mono_absolut").html('');
                    $("#nr_hm_trombosit").html('');
                    $("#nr_hm_led").html('');
                    $("#nr_u_warna").html('');
                    $("#nr_u_kejernihan").html('');
                    $("#nr_u_berat_jenis").html('');
                    $("#nr_u_ph").html('');
                    $("#nr_u_protein_albumin").html('');
                    $("#nr_u_glukosa").html('');
                    $("#nr_u_keton").html('');
                    $("#nr_u_bilirubin").html('');
                    $("#nr_u_urobilinogen").html('');
                    $("#nr_u_nitrit").html('');
                    $("#nr_u_leukosit_esterase").html('');
                    $("#nr_u_darah_haem").html('');
                    $("#nr_u_eri").html('');
                    $("#nr_u_leuko").html('');
                    $("#nr_u_epithel").html('');
                    $("#nr_u_silinder").html('');
                    $("#nr_u_kristal").html('');
                    $("#nr_u_lain").html('');
                    $("#nr_fh_sgot").html('');
                    $("#nr_fh_sgpt").html('');
                    $("#nr_fl_kolesterol_total").html('');
                    $("#nr_fl_hdl_kolesterol").html('');
                    $("#nr_fl_ldl_kolesterol").html('');
                    $("#nr_fl_trigliserida").html('');
                    $("#nr_gd_glukosa_puasa").html('');
                    $("#nr_gd_jpp").html('');
                    $("#nr_fg_bun").html('');
                    $("#nr_fg_ureum").html('');
                    $("#nr_fg_kreatinin").html('');
                    $("#nr_fg_egfr").html('');
                    $("#nr_asam_urat").html('');
                    $("#nr_hbsag").html('');
                }               
            },
            error: function(xhr, status, error) {
                console.log(xhr, status, error);
            }
            });
        });
    </script>
    <script type="text/javascript">
    $(document).ready(function() {            
        load_data();
        function load_data(employee_id = ''){
            let table = $('#table_log').DataTable({
            stateSave: true,
            responsive: true,
            autoWidth: false,
            processing: true,
            serverSide: true,
            ajax: {
                url:"{{ route('clinic.patient.log') }}",
                data:{employee_id:employee_id}
            },
            columns: [{
                data: 'id_dokter',
                name: 'id_dokter',
                className: "text-center"
                },
                {
                data: 'visit_date',
                name: 'visit_date',
                className: "text-center"
                },
                {
                data: 'nik',
                name: 'nik',
                className: "text-center"
                },
                {
                data: 'id_employee',
                name: 'id_employee',
                className: "text-center"
                },           
                {
                data: 'diagnosa',
                name: 'diagnosa',
                className: "text-center"
                },
                {
                data: 'keluhan',
                name: 'keluhan',
                className: "text-center"
                },
                {
                data: 'tensi',
                name: 'tensi',
                className: "text-center"
                },
                {
                data: 'keterangan',
                name: 'keterangan',
                className: "text-center"
                },
                {
                data: 'medicine',
                name: 'medicine',
                className: "none text-center"
                },
            ]
            });
        }
        $("#id_employee").change(function(){
            var employee_id = this.value;
            $('#table_log').DataTable().destroy();
            load_data(employee_id);
        });
    });
    </script>
    <script>
        $('.js-example-basic-single').select2();

        $('#tr_tanggal').flatpickr({
            allowInput: true,
            altInput: false,
            altFormat: "d F, Y",
            dateFormat: "Y-m-d",
        });   

        $( "#btn-submit" ).click(function() {
            $("#form-patient").submit(function () {
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
