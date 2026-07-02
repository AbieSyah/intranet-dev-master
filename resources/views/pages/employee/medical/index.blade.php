@extends('layouts.general')
@section('link')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Select2-->
    <link rel="stylesheet" href="/assets/libs/adminlte/select2/css/4.1.0/select2.min.css">
@endsection
@section('content')
<!-- start page -->
<div class="row">
    <div class="col-lg-12">
    @if(!empty($medical))
        <div class="row">
            <div class="col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Medical Checkup</h5>
                        <select class="form-control select2" name="date_mcu" id="date_mcu">
                            @foreach($arr_tanggal as $key_tgl => $tanggal)
                                @if(!empty($latest_mcu))
                                    @if($latest_mcu == $tanggal)
                                        <option value="{{$key_tgl}}" selected>{{$tanggal}}</option>
                                    @else           
                                        <option value="{{$key_tgl}}">{{$tanggal}}</option>
                                    @endif  
                                @else         
                                    <option value="{{$key_tgl}}">{{$tanggal}}</option> 
                                @endif
                            @endforeach                               
                        </select>
                        <div id="profile-mcu"> 
                            <div data-simplebar style="max-width: 453px;">
                                <table class="table table-borderless table-nowrap mb-0">
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div class="flex-grow-1">
                                                    <p class="mb-1 text-muted">Tipe MCU :</p>
                                                    <div id="jenis_mcu">
                                                        @if(!empty($medical->paket))
                                                            @if($medical->paket == 'mcu tahunan')
                                                                <h6 class="text-truncate mb-0">Tahunan</h6>
                                                            @elseif($medical->paket == 'calon karyawan')
                                                                <h6 class="text-truncate mb-0">Calon Karyawan</h6>
                                                            @else
                                                                <h6 class="text-truncate mb-0">Penetapan</h6>
                                                            @endif
                                                        @else
                                                            <h6 class="text-truncate mb-0">Tahunan</h6>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="flex-grow-1">
                                                    <p class="mb-1 text-muted">No Lab :</p>
                                                    <div id="no_lab">
                                                        <h6 class="text-truncate mb-0">{{$medical->no_lab}}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="flex-grow-1">
                                                    <p class="mb-1 text-muted">Full Name :</p>
                                                    <div id="fullname">
                                                        <h6 class="text-truncate mb-0">{{$medical->employee->fullname}}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="flex-grow-1">
                                                    <p class="mb-1 text-muted">Gender :</p>
                                                    <div id="gender">
                                                        <h6 class="text-truncate mb-0">{{$medical->employee->gender}}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="flex-grow-1">
                                                    <p class="mb-1 text-muted">Age :</p>
                                                    <div id="umur">
                                                        <h6 class="text-truncate mb-0">{{$medical->umur}}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="flex-grow-1">
                                                    <p class="mb-1 text-muted">Score Framigham :</p>
                                                    <div id="skor">
                                                        <h6 class="text-truncate mb-0">{{$medical->skor_framigham}}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="flex-grow-1">
                                                    <p class="mb-1 text-muted">Kriteria :</p>
                                                    <div id="kriteria">
                                                        <h6 class="text-truncate mb-0">{{$medical->kriteria_sehat}}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="flex-grow-1">
                                                    <p class="mb-1 text-muted">Tanggal Pemeriksaan :</p>
                                                    <div id="tgl_mcu">
                                                        @if(!empty($medical->tanggal_mcu))
                                                        <h6 class="text-truncate mb-0">{{\Carbon\Carbon::parse($medical->tanggal_mcu)->format('d F Y')}}</h6>
                                                        @else
                                                        <h6 class="text-truncate mb-0">-</h6>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <br>
                            <button type="button" data-bs-toggle="modal" data-bs-target="#modal-lampiran-mcu" class="btn btn-danger"><i class="ri-file-pdf-line me-1 align-bottom"></i> Show MCU</button>
                            <div id="unduh_mcu" class="mt-2">
                            </div>                            
                        </div>
                    </div><!-- end card body -->
                </div><!-- end card -->
            </div>
            <!--end col-->
            <div class="col-lg-9">
                <div class="card">
                    <div class="card-body">
                        <div id="medical-mcu">
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
                                                        <div class="accordion-body ms-2 ps-5">
                                                            {{$medical->lab}}
                                                        </div>
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
                                                        <div class="accordion-body ms-2 ps-5">
                                                            {{$medical->foto_thorax}}
                                                        </div>
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
                                                        <div class="accordion-body ms-2 ps-5">
                                                            @if(!empty($medical->ekg))
                                                                {{$medical->ekg}}
                                                            @else
                                                                -
                                                            @endif
                                                        </div>
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
                                                        <div class="accordion-body ms-2 ps-5">
                                                            {{$medical->audiometri}}
                                                        </div>
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
                                                        <div class="accordion-body ms-2 ps-5">
                                                            {{$medical->fisik_dokter}}
                                                        </div>
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
                                                        <div class="accordion-body ms-2 ps-5">
                                                            {{$medical->kesimpulan}}
                                                        </div>
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
                                                        <div class="accordion-body ms-2 ps-5">
                                                            {{$medical->saran}}
                                                        </div>
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
                                                        <h6>{{$medical->hm_hemoglobin}}</h6>
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_hm_hemoglobin">
                                                    @foreach($lab as $key => $value)
                                                        @if($key == 'hm_hemoglobin')
                                                        <h6>{{$value}}</h6>
                                                        @endif
                                                    @endforeach
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
                                                        <h6>{{$medical->hm_eritrosit}}</h6>
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_hm_eritrosit">
                                                        @foreach($lab as $key => $value)
                                                            @if($key == 'hm_eritrosit')
                                                            <h6>{{$value}}</h6>
                                                            @endif
                                                        @endforeach
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
                                                        <h6>{{$medical->hm_hematokrit}}</h6>
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_hm_hematokrit">
                                                        @foreach($lab as $key => $value)
                                                            @if($key == 'hm_hematokrit')
                                                            <h6>{{$value}}</h6>
                                                            @endif
                                                        @endforeach
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
                                                        <h6>{{$medical->hm_mcv}}</h6>
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_hm_mcv">
                                                        @foreach($lab as $key => $value)
                                                            @if($key == 'hm_mcv')
                                                            <h6>{{$value}}</h6>
                                                            @endif
                                                        @endforeach
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
                                                        <h6>{{$medical->hm_mch}}</h6>
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_hm_mch">
                                                        @foreach($lab as $key => $value)
                                                            @if($key == 'hm_mch')
                                                            <h6>{{$value}}</h6>
                                                            @endif
                                                        @endforeach
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
                                                        <h6>{{$medical->hm_mchc}}</h6>
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_hm_mchc">
                                                        @foreach($lab as $key => $value)
                                                            @if($key == 'hm_mchc')
                                                            <h6>{{$value}}</h6>
                                                            @endif
                                                        @endforeach
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
                                                        <h6>{{$medical->hm_rdw}}</h6>
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_hm_rdw">
                                                        @foreach($lab as $key => $value)
                                                            @if($key == 'hm_rdw')
                                                            <h6>{{$value}}</h6>
                                                            @endif
                                                        @endforeach
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
                                                        <h6>{{$medical->hm_leukosit}}</h6>
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_hm_leukosit">
                                                        @foreach($lab as $key => $value)
                                                            @if($key == 'hm_leukosit')
                                                            <h6>{{$value}}</h6>
                                                            @endif
                                                        @endforeach
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
                                                        <h6>{{$medical->hm_eos}}</h6>
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_hm_eos">
                                                        @foreach($lab as $key => $value)
                                                            @if($key == 'hm_eos')
                                                            <h6>{{$value}}</h6>
                                                            @endif
                                                        @endforeach
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
                                                        <h6>{{$medical->hm_baso}}</h6>
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_hm_baso">
                                                        @foreach($lab as $key => $value)
                                                            @if($key == 'hm_baso')
                                                            <h6>{{$value}}</h6>
                                                            @endif
                                                        @endforeach
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
                                                        <h6>{{$medical->hm_neutro}}</h6>
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_hm_neutro">
                                                        @foreach($lab as $key => $value)
                                                            @if($key == 'hm_neutro')
                                                            <h6>{{$value}}</h6>
                                                            @endif
                                                        @endforeach
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
                                                        <h6>{{$medical->hm_limfo}}</h6>
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_hm_limfo">
                                                        @foreach($lab as $key => $value)
                                                            @if($key == 'hm_limfo')
                                                            <h6>{{$value}}</h6>
                                                            @endif
                                                        @endforeach
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
                                                        <h6>{{$medical->hm_mono}}</h6>
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_hm_mono">
                                                        @foreach($lab as $key => $value)
                                                            @if($key == 'hm_mono')
                                                            <h6>{{$value}}</h6>
                                                            @endif
                                                        @endforeach
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
                                                        <h6>{{$medical->hm_eos_absolut}}</h6>
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_hm_eos_absolut">
                                                        @foreach($lab as $key => $value)
                                                            @if($key == 'hm_eos_absolut')
                                                            <h6>{{$value}}</h6>
                                                            @endif
                                                        @endforeach
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
                                                        <h6>{{$medical->hm_baso_absolut}}</h6>
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_hm_baso_absolut">
                                                        @foreach($lab as $key => $value)
                                                            @if($key == 'hm_baso_absolut')
                                                            <h6>{{$value}}</h6>
                                                            @endif
                                                        @endforeach
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
                                                        <h6>{{$medical->hm_neutro_absolut}}</h6>
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_hm_neutro_absolut">
                                                        @foreach($lab as $key => $value)
                                                            @if($key == 'hm_neutro_absolut')
                                                            <h6>{{$value}}</h6>
                                                            @endif
                                                        @endforeach
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
                                                        <h6>{{$medical->hm_limfo_absolut}}</h6>
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_hm_limfo_absolut">
                                                        @foreach($lab as $key => $value)
                                                            @if($key == 'hm_limfo_absolut')
                                                            <h6>{{$value}}</h6>
                                                            @endif
                                                        @endforeach
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
                                                        <h6>{{$medical->hm_mono_absolut}}</h6>
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_hm_mono_absolut">
                                                        @foreach($lab as $key => $value)
                                                            @if($key == 'hm_mono_absolut')
                                                            <h6>{{$value}}</h6>
                                                            @endif
                                                        @endforeach
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
                                                        <h6>{{$medical->hm_trombosit}}</h6>
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_hm_trombosit">
                                                        @foreach($lab as $key => $value)
                                                            @if($key == 'hm_trombosit')
                                                            <h6>{{$value}}</h6>
                                                            @endif
                                                        @endforeach
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
                                                        <h6>{{$medical->hm_led}}</h6>
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_hm_led">
                                                        @foreach($lab as $key => $value)
                                                            @if($key == 'hm_led')
                                                            <h6>{{$value}}</h6>
                                                            @endif
                                                        @endforeach
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
                                                        <h6>{{$medical->u_warna}}</h6>
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_u_warna">
                                                        @foreach($lab as $key => $value)
                                                            @if($key == 'u_warna')
                                                            <h6>{{$value}}</h6>
                                                            @endif
                                                        @endforeach
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
                                                        <h6>{{$medical->u_kejernihan}}</h6>
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_u_kejernihan">
                                                        @foreach($lab as $key => $value)
                                                            @if($key == 'u_kejernihan')
                                                            <h6>{{$value}}</h6>
                                                            @endif
                                                        @endforeach
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
                                                        <h6>{{$medical->u_berat_jenis}}</h6>
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_u_berat_jenis">
                                                        @foreach($lab as $key => $value)
                                                            @if($key == 'u_berat_jenis')
                                                            <h6>{{$value}}</h6>
                                                            @endif
                                                        @endforeach
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
                                                        <h6>{{$medical->u_ph}}</h6>
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_u_ph">
                                                        @foreach($lab as $key => $value)
                                                            @if($key == 'u_ph')
                                                            <h6>{{$value}}</h6>
                                                            @endif
                                                        @endforeach
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
                                                        <h6>{{$medical->u_protein_albumin}}</h6>
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_u_protein_albumin">
                                                        @foreach($lab as $key => $value)
                                                            @if($key == 'u_protein_albumin')
                                                            <h6>{{$value}}</h6>
                                                            @endif
                                                        @endforeach
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
                                                        <h6>{{$medical->u_glukosa}}</h6>
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_u_glukosa">
                                                        @foreach($lab as $key => $value)
                                                            @if($key == 'u_glukosa')
                                                            <h6>{{$value}}</h6>
                                                            @endif
                                                        @endforeach
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
                                                        <h6>{{$medical->u_keton}}</h6>
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_u_keton">
                                                        @foreach($lab as $key => $value)
                                                            @if($key == 'u_keton')
                                                            <h6>{{$value}}</h6>
                                                            @endif
                                                        @endforeach
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
                                                        <h6>{{$medical->u_bilirubin}}</h6>
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_u_bilirubin">
                                                        @foreach($lab as $key => $value)
                                                            @if($key == 'u_bilirubin')
                                                            <h6>{{$value}}</h6>
                                                            @endif
                                                        @endforeach
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
                                                        <h6>{{$medical->u_urobilinogen}}</h6>
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_u_urobilinogen">
                                                        @foreach($lab as $key => $value)
                                                            @if($key == 'u_urobilinogen')
                                                            <h6>{{$value}}</h6>
                                                            @endif
                                                        @endforeach
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
                                                        <h6>{{$medical->u_nitrit}}</h6>
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_u_nitrit">
                                                        @foreach($lab as $key => $value)
                                                            @if($key == 'u_nitrit')
                                                            <h6>{{$value}}</h6>
                                                            @endif
                                                        @endforeach
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
                                                        <h6>{{$medical->u_leukosit_esterase}}</h6>
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_u_leukosit_esterase">
                                                        @foreach($lab as $key => $value)
                                                            @if($key == 'u_leukosit_esterase')
                                                            <h6>{{$value}}</h6>
                                                            @endif
                                                        @endforeach
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
                                                        <h6>{{$medical->u_darah_haem}}</h6>
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_u_darah_haem">
                                                        @foreach($lab as $key => $value)
                                                            @if($key == 'u_darah_haem')
                                                            <h6>{{$value}}</h6>
                                                            @endif
                                                        @endforeach
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
                                                        <h6>{{$medical->u_eri}}</h6>
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_u_eri">
                                                        @foreach($lab as $key => $value)
                                                            @if($key == 'u_eri')
                                                            <h6>{{$value}}</h6>
                                                            @endif
                                                        @endforeach
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
                                                        <h6>{{$medical->u_leuko}}</h6>
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_u_leuko">
                                                        @foreach($lab as $key => $value)
                                                            @if($key == 'u_leuko')
                                                            <h6>{{$value}}</h6>
                                                            @endif
                                                        @endforeach
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
                                                        <h6>{{$medical->u_epithel}}</h6>
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_u_epithel">
                                                        @foreach($lab as $key => $value)
                                                            @if($key == 'u_epithel')
                                                            <h6>{{$value}}</h6>
                                                            @endif
                                                        @endforeach
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
                                                        <h6>{{$medical->u_silinder}}</h6>
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_u_silinder">
                                                        @foreach($lab as $key => $value)
                                                            @if($key == 'u_silinder')
                                                            <h6>{{$value}}</h6>
                                                            @endif
                                                        @endforeach
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
                                                        <h6>{{$medical->u_kristal}}</h6>
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_u_kristal">
                                                        @foreach($lab as $key => $value)
                                                            @if($key == 'u_kristal')
                                                            <h6>{{$value}}</h6>
                                                            @endif
                                                        @endforeach
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
                                                        <h6>{{$medical->u_lain}}</h6>
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_u_lain">
                                                        @foreach($lab as $key => $value)
                                                            @if($key == 'u_lain')
                                                            <h6>{{$value}}</h6>
                                                            @endif
                                                        @endforeach
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
                                                        <h6>{{$medical->fh_sgot}}</h6>
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_fh_sgot">
                                                        @foreach($lab as $key => $value)
                                                            @if($key == 'fh_sgot')
                                                            <h6>{{$value}}</h6>
                                                            @endif
                                                        @endforeach
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
                                                        <h6>{{$medical->fh_sgpt}}</h6>
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_fh_sgpt">
                                                        @foreach($lab as $key => $value)
                                                            @if($key == 'fh_sgpt')
                                                            <h6>{{$value}}</h6>
                                                            @endif
                                                        @endforeach
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
                                                        <h6>{{$medical->fl_kolesterol_total}}</h6>
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_fl_kolesterol_total">
                                                        @foreach($lab as $key => $value)
                                                            @if($key == 'fl_kolesterol_total')
                                                            <h6>{{$value}}</h6>
                                                            @endif
                                                        @endforeach
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
                                                        <h6>{{$medical->fl_hdl_kolesterol}}</h6>
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_fl_hdl_kolesterol">
                                                        @foreach($lab as $key => $value)
                                                            @if($key == 'fl_hdl_kolesterol')
                                                            <h6>{{$value}}</h6>
                                                            @endif
                                                        @endforeach
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
                                                        <h6>{{$medical->fl_ldl_kolesterol}}</h6>
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_fl_ldl_kolesterol">
                                                        @foreach($lab as $key => $value)
                                                            @if($key == 'fl_ldl_kolesterol')
                                                            <h6>{{$value}}</h6>
                                                            @endif
                                                        @endforeach
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
                                                        <h6>{{$medical->fl_trigliserida}}</h6>
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_fl_trigliserida">
                                                        @foreach($lab as $key => $value)
                                                            @if($key == 'fl_trigliserida')
                                                            <h6>{{$value}}</h6>
                                                            @endif
                                                        @endforeach
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
                                                        <h6>{{$medical->gd_glukosa_puasa}}</h6>
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_gd_glukosa_puasa">
                                                        @foreach($lab as $key => $value)
                                                            @if($key == 'gd_glukosa_puasa')
                                                            <h6>{{$value}}</h6>
                                                            @endif
                                                        @endforeach
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
                                                        <h6>{{$medical->gd_jpp}}</h6>
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_gd_jpp">
                                                        @foreach($lab as $key => $value)
                                                            @if($key == 'gd_jpp')
                                                            <h6>{{$value}}</h6>
                                                            @endif
                                                        @endforeach
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
                                                        <h6>{{$medical->fg_bun}}</h6>
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_fg_bun">
                                                        @foreach($lab as $key => $value)
                                                            @if($key == 'fg_bun')
                                                            <h6>{{$value}}</h6>
                                                            @endif
                                                        @endforeach
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
                                                        <h6>{{$medical->fg_ureum}}</h6>
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_fg_ureum">
                                                        @foreach($lab as $key => $value)
                                                            @if($key == 'fg_ureum')
                                                            <h6>{{$value}}</h6>
                                                            @endif
                                                        @endforeach
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
                                                        <h6>{{$medical->fg_kreatinin}}</h6>
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_fg_kreatinin">
                                                        @foreach($lab as $key => $value)
                                                            @if($key == 'fg_kreatinin')
                                                            <h6>{{$value}}</h6>
                                                            @endif
                                                        @endforeach
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
                                                        <h6>{{$medical->fg_egfr}}</h6>
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_fg_egfr">
                                                        @foreach($lab as $key => $value)
                                                            @if($key == 'fg_egfr')
                                                            <h6>{{$value}}</h6>
                                                            @endif
                                                        @endforeach
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
                                                        <h6>{{$medical->asam_urat}}</h6>
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_asam_urat">
                                                        @foreach($lab as $key => $value)
                                                            @if($key == 'asam_urat')
                                                            <h6>{{$value}}</h6>
                                                            @endif
                                                        @endforeach
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
                                                        <h6>{{$medical->hbsag}}</h6>
                                                    </div>
                                                    <p class="mb-1">Nilai Rujukan :</p>
                                                    <div id="nr_hbsag">
                                                        @foreach($lab as $key => $value)
                                                            @if($key == 'hbsag')
                                                            <h6>{{$value}}</h6>
                                                            @endif
                                                        @endforeach
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
                        <div id="medical-view"></div>
                    </div>
                    <!--end card-body-->
                </div><!-- end card -->
            </div>
            <!--end col-->
        </div>
    @endif
    </div>
    <!--end col-->
</div>
<!--end row-->
<!--modal lampiran mcu-->
<div class="modal flip" id="modal-lampiran-mcu" tabindex="-1" aria-labelledby="exampleModalgridLabel" aria-modal="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-judul"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="profile-lampiran">
                </div>                
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<!-- Select2 -->
<script src="/assets/libs/adminlte/select2/js/4.1.0/select2.min.js"></script>
@endsection
@section('javascript')
<script>
    $(function () {
        $('.select2').select2()        
    });
</script>
<script>
    var date_mcu = $('#date_mcu').val();   
    $.ajax({
        url: "{{ route('profile.lampiran.pdf') }}",
        type: "POST",
        data: {
            date_mcu: date_mcu,
            _token: '{{ csrf_token() }}'
        },
        dataType: 'json',
        success: function(result) {
            // $("#profile-lampiran").html('<embed src="'+result.pdf_mcu+'" frameborder="0" width="100%" height="450px">');
            if(result.pdf_mcu == 0) {
                $("#profile-lampiran").html('<center><div class="text-center"><lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop" colors="primary:#405189,secondary:#0ab39c" style="width:150px;height:150px"></lord-icon><h5 class="text-center mt-2">data not available...</h5></div></center>');
            }else{
                $("#profile-lampiran").html('<iframe src="'+result.pdf_mcu+'" frameborder="0" style="height:500px; width:100%;"></iframe>');            
            }
            if(result.unduh_mcu == 0) {
                $("#unduh_mcu").html('');            
            }else{
                $("#unduh_mcu").html('<a href="'+result.unduh_mcu+'" class="btn btn-success"><i class="ri-file-pdf-line me-1 align-bottom"></i> Download MCU</a>');
            }
            if(result.paket == 'mcu tahunan'){
                $("#medical-mcu").show();
                $("#medical-view").hide();
            }else{
                $("#medical-mcu").hide();
                $("#medical-view").show();
                $("#medical-view").html('<iframe src="'+result.pdf_mcu+'" frameborder="0" style="height:700px; width:100%;"></iframe>');
            }
        }
    });
    $('#date_mcu').on('change', function(){
        $("#profile-lampiran").html(''); 
        var date_mcu = this.value;
        $.ajax({
            url: "{{ route('profile.lampiran.pdf') }}",
            type: "POST",
            data: {
                date_mcu: date_mcu,
                _token: '{{ csrf_token() }}'
            },
            dataType: 'json',
            success: function(result) {
                // console.log(result.no_lab);
                if(result.unduh_mcu == 0) {
                    $("#unduh_mcu").html('');            
                }else{
                    $("#unduh_mcu").html('<a href="'+result.unduh_mcu+'" class="btn btn-success"><i class="ri-file-pdf-line me-1 align-bottom"></i> Download MCU</a>');
                }
                if(result.pdf_mcu == 0) {
                    $("#profile-lampiran").html('<center><div class="text-center"><lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop" colors="primary:#405189,secondary:#0ab39c" style="width:150px;height:150px"></lord-icon><h5 class="text-center mt-2">data not available...</h5></div></center>');
                }else{                        
                    $("#profile-lampiran").html('<iframe src="'+result.pdf_mcu+'" frameborder="0" style="height:500px; width:100%;"></iframe>');
                    //profile information                                                       
                    $("#no_lab").html('<h6 class="text-truncate mb-0">'+result.no_lab+'</h6>');                                                       
                    $("#fullname").html('<h6 class="text-truncate mb-0">'+result.fullname+'</h6>');                                                       
                    $("#gender").html('<h6 class="text-truncate mb-0">'+result.gender+'</h6>');                                                       
                    $("#umur").html('<h6 class="text-truncate mb-0">'+result.umur+'</h6>');                                                       
                    $("#skor").html('<h6 class="text-truncate mb-0">'+result.skor_framigham+'</h6>');                                                       
                    $("#kriteria").html('<h6 class="text-truncate mb-0">'+result.kriteria_sehat+'</h6>');                                                       
                    $("#tgl_mcu").html('<h6 class="text-truncate mb-0">'+result.tgl_mcu+'</h6>');                                                       
                    //medical information
                    if(result.paket == 'mcu tahunan'){
                        $("#medical-mcu").show();
                        $("#medical-view").hide();
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
                        $("#medical-mcu").hide();
                        $("#medical-view").show();
                        $("#medical-view").html('<iframe src="'+result.pdf_mcu+'" frameborder="0" style="height:500px; width:100%;"></iframe>');
                    }
                    
                }
            }
        });
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
