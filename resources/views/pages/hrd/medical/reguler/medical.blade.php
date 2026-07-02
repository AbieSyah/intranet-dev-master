@extends('layouts.master')
@section('link')
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <!-- costume css -->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/flip/css/flipbook.style.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/flip/css/font-awesome.css') }}">
    <!-- <link rel="stylesheet" type="text/css" href="{{ asset('assets/flip/css/footer.css') }}"> -->
    <style>
        body {
            background-color: #f6f6f6;
        }

        #author {
            font-size: 15px;
            font-weight: bold;
            color: #0186c9;
        }

        #date {
            margin-left: 10px;
            font-size: 15px;
            color: #819196;
        }

        #size {
            font-size: 15px;
            color: #819196;
        }

        #description {
            margin-top: 20px;
            font-weight: lighter;
        }
    </style>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="profile-foreground position-relative mx-n4 mt-n4">
            <div class="profile-wid-bg">
                <img src="/assets/images/salonpas-bg.jpg" alt="" class="profile-wid-img" />
            </div>
        </div>
        <div class="pt-4 mb-4 mb-lg-3 pb-lg-4">
            <div class="row g-4">
                <div class="col-auto">
                    <div class="avatar-lg">
                        @if (!empty($medical->employee->avatar))
                            <img src="{{ asset('storage/avatars/' . $medical->employee->avatar) }}" alt="user-img"
                                class="img-thumbnail rounded-circle" />
                        @else
                            <img src="/assets/images/users/user-dummy-img.jpg" alt="user-img"
                                class="img-thumbnail rounded-circle" />
                        @endif
                    </div>
                </div>
                <!--end col-->
                <div class="col">
                    <div class="p-2">
                        <h3 class="text-white mb-1">{{ $medical->employee->fullname }}</h3>
                        <p class="text-white-75">NIK : {{ $medical->employee->nik }}</p>
                        <div class="hstack text-white-50 gap-1">
                            <div class="me-2"><i class="ri-map-pin-user-line me-1 text-white-75 fs-16 align-bottom"></i>
                                {{ $medical->employee->area->name }}
                            </div>
                            <div class="text-uppercase"><i
                                    class="ri-building-line me-1 text-white-75 fs-16 align-bottom"></i>
                                {{ $medical->employee->department->name }}
                            </div>
                        </div>
                        <div class="hstack text-white-50 gap-1">
                            <div class="me-2">
                                @if (!empty($medical->employee->level->nama))
                                    <i class="ri-contacts-book-2-line me-1 text-white-75 fs-16 align-middle"></i>
                                    {{ $medical->employee->level->nama }}
                                @endif
                            </div>
                            <div>
                                @if (!empty($medical->employee->position->nama))
                                    <i class="ri-contacts-book-line me-1 text-white-75 fs-16 align-middle"></i>
                                    {{ $medical->employee->position->nama }}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <!--end col-->
                <div class="col-12 col-lg-auto order-last order-lg-0">
                    <div class="row text text-white-50 text-center">
                        <div class="col-lg-12 col-6">
                            <div class="p-2">
                                <h4 class="text-white"><i class="las la-heartbeat"></i> {{ $medical->kriteria_sehat }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end col-->

            </div>
            <!--end row-->
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div>
                    <div class="d-flex" style="justify-content: flex-end;">
                        <!-- Nav tabs -->
                        <!-- <ul class="nav nav-pills animation-nav profile-nav gap-2 gap-lg-3 flex-grow-1" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link fs-14 active" data-bs-toggle="tab" href="#overview-tab" role="tab">
                                        <i class="ri-airplay-fill d-inline-block d-md-none"></i> <span class="d-none d-md-inline-block">Overview</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link fs-14" data-bs-toggle="tab" href="#hematologi" role="tab">
                                        <i class="ri-price-tag-line d-inline-block d-md-none"></i> <span class="d-none d-md-inline-block">Hematologi</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link fs-14" data-bs-toggle="tab" href="#urine" role="tab">
                                        <i class="ri-price-tag-line d-inline-block d-md-none"></i> <span class="d-none d-md-inline-block">Urine</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link fs-14" data-bs-toggle="tab" href="#faal" role="tab">
                                        <i class="ri-price-tag-line d-inline-block d-md-none"></i> <span class="d-none d-md-inline-block">Faal</span>
                                    </a>
                                </li>
                            </ul> -->
                        <div class="d-flex gap-3">
                            <!-- <button type="button" data-bs-toggle="modal" data-bs-target="#modal-lampiran-mcu" class="btn btn-danger"><i class="ri-file-pdf-line align-bottom"></i> View PDF</button> -->
                            <a id="read" class="btn btn-danger"><i class="ri-file-pdf-line align-bottom"></i> Read
                                PDF</a>
                            <a href="{{ url()->previous() }}" class="btn btn-success"><i
                                    class="ri-logout-box-line align-bottom"></i> Back</a>
                        </div>
                    </div>
                    <div class="row pt-4">
                        <div class="col-lg-3">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title mb-3">Profile Information</h5>
                                    <div data-simplebar style="max-width: 453px;">
                                        <table class="table table-borderless table-nowrap mb-0">
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1 text-muted">No Lab :</p>
                                                            <h6 class="text-truncate mb-0">{{ $medical->no_lab }}</h6>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1 text-muted">Full Name :</p>
                                                            <h6 class="text-truncate mb-0">
                                                                {{ $medical->employee->fullname }}</h6>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1 text-muted">Gender :</p>
                                                            <h6 class="text-truncate mb-0">
                                                                {{ $medical->employee->gender }}</h6>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1 text-muted">Age :</p>
                                                            <h6 class="text-truncate mb-0">{{ $medical->umur }}</h6>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1 text-muted">Score Framigham :</p>
                                                            <h6 class="text-truncate mb-0">{{ $medical->skor_framigham }}
                                                            </h6>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1 text-muted">Tanggal Pemeriksaan :</p>
                                                            @if (!empty($medical->tanggal_mcu))
                                                                <h6 class="text-truncate mb-0">
                                                                    {{ \Carbon\Carbon::parse($medical->tanggal_mcu)->format('d F Y') }}
                                                                </h6>
                                                            @else
                                                                <h6 class="text-truncate mb-0">-</h6>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div><!-- end card body -->
                            </div><!-- end card -->
                        </div>
                        <!--end col-->
                        <div class="col-lg-9">
                            <div class="card">
                                <div class="card-body">
                                    <ul class="nav nav-tabs mb-3" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" data-bs-toggle="tab" href="#medical"
                                                role="tab" aria-selected="false">
                                                Overview
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" href="#hematologi" role="tab"
                                                aria-selected="false">
                                                Hematologi
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" href="#urine" role="tab"
                                                aria-selected="false">
                                                Urine
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" href="#faal" role="tab"
                                                aria-selected="true">
                                                Faal
                                            </a>
                                        </li>
                                    </ul>
                                    <div class="tab-content  text-muted">
                                        <div class="tab-pane active" id="medical" role="tabpanel">
                                            <h5 class="card-title mb-3">Medical Information</h5>
                                            <div class="profile-timeline">
                                                <div class="accordion accordion-flush" id="todayExample">
                                                    <div class="accordion-item border-0">
                                                        <div class="accordion-header" id="headingOne">
                                                            <a class="accordion-button p-2 shadow-none"
                                                                data-bs-toggle="collapse" href="#collapseOne"
                                                                aria-expanded="true">
                                                                <div class="d-flex">
                                                                    <div class="flex-shrink-0 avatar-xs">
                                                                        <div
                                                                            class="avatar-title bg-light text-primary rounded-circle">
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
                                                        <div id="collapseOne" class="accordion-collapse collapse show"
                                                            aria-labelledby="headingOne"
                                                            data-bs-parent="#accordionExample">
                                                            <div class="accordion-body ms-2 ps-5">
                                                                {{ $medical->lab }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="accordion-item border-0">
                                                        <div class="accordion-header" id="headingTwo">
                                                            <a class="accordion-button p-2 shadow-none"
                                                                data-bs-toggle="collapse" href="#collapseTwo"
                                                                aria-expanded="true">
                                                                <div class="d-flex">
                                                                    <div class="flex-shrink-0 avatar-xs">
                                                                        <div
                                                                            class="avatar-title bg-light text-primary rounded-circle">
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
                                                        <div id="collapseTwo" class="accordion-collapse collapse show"
                                                            aria-labelledby="headingTwo"
                                                            data-bs-parent="#accordionExample">
                                                            <div class="accordion-body ms-2 ps-5">
                                                                {{ $medical->foto_thorax }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @if (!empty($medical->ekg))
                                                        <div class="accordion-item border-0">
                                                            <div class="accordion-header" id="headingTwo">
                                                                <a class="accordion-button p-2 shadow-none"
                                                                    data-bs-toggle="collapse" href="#collapseSeven"
                                                                    aria-expanded="true">
                                                                    <div class="d-flex">
                                                                        <div class="flex-shrink-0 avatar-xs">
                                                                            <div
                                                                                class="avatar-title bg-light text-primary rounded-circle">
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
                                                            <div id="collapseSeven"
                                                                class="accordion-collapse collapse show"
                                                                aria-labelledby="headingSeven"
                                                                data-bs-parent="#accordionExample">
                                                                <div class="accordion-body ms-2 ps-5">
                                                                    {{ $medical->ekg }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    <div class="accordion-item border-0">
                                                        <div class="accordion-header" id="headingThree">
                                                            <a class="accordion-button p-2 shadow-none"
                                                                data-bs-toggle="collapse" href="#collapseThree"
                                                                aria-expanded="true">
                                                                <div class="d-flex">
                                                                    <div class="flex-shrink-0 avatar-xs">
                                                                        <div
                                                                            class="avatar-title bg-light text-primary rounded-circle">
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
                                                        <div id="collapseThree" class="accordion-collapse collapse show"
                                                            aria-labelledby="headingThree"
                                                            data-bs-parent="#accordionExample">
                                                            <div class="accordion-body ms-2 ps-5">
                                                                {{ $medical->audiometri }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="accordion-item border-0">
                                                        <div class="accordion-header" id="headingFour">
                                                            <a class="accordion-button p-2 shadow-none"
                                                                data-bs-toggle="collapse" href="#collapseFour"
                                                                aria-expanded="true">
                                                                <div class="d-flex">
                                                                    <div class="flex-shrink-0 avatar-xs">
                                                                        <div
                                                                            class="avatar-title bg-light text-primary rounded-circle">
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
                                                        <div id="collapseFour" class="accordion-collapse collapse show"
                                                            aria-labelledby="headingFour"
                                                            data-bs-parent="#accordionExample">
                                                            <div class="accordion-body ms-2 ps-5">
                                                                {{ $medical->fisik_dokter }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="accordion-item border-0">
                                                        <div class="accordion-header" id="headingFive">
                                                            <a class="accordion-button p-2 shadow-none"
                                                                data-bs-toggle="collapse" href="#collapseFive"
                                                                aria-expanded="true">
                                                                <div class="d-flex">
                                                                    <div class="flex-shrink-0 avatar-xs">
                                                                        <div
                                                                            class="avatar-title bg-light text-primary rounded-circle">
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
                                                        <div id="collapseFive" class="accordion-collapse collapse show"
                                                            aria-labelledby="headingFive"
                                                            data-bs-parent="#accordionExample">
                                                            <div class="accordion-body ms-2 ps-5">
                                                                {{ $medical->kesimpulan }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="accordion-item border-0">
                                                        <div class="accordion-header" id="headingSix">
                                                            <a class="accordion-button p-2 shadow-none"
                                                                data-bs-toggle="collapse" href="#collapseSix"
                                                                aria-expanded="true">
                                                                <div class="d-flex">
                                                                    <div class="flex-shrink-0 avatar-xs">
                                                                        <div
                                                                            class="avatar-title bg-light text-primary rounded-circle">
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
                                                        <div id="collapseSix" class="accordion-collapse collapse show"
                                                            aria-labelledby="headingSix"
                                                            data-bs-parent="#accordionExample">
                                                            <div class="accordion-body ms-2 ps-5">
                                                                {{ $medical->saran }}
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
                                                            <h6>{{ $medical->hm_hemoglobin }}</h6>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            @foreach ($lab as $key => $value)
                                                                @if ($key == 'hm_hemoglobin')
                                                                    <h6>{{ $value }}</h6>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">

                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Eritrosit :</p>
                                                            <h6>{{ $medical->hm_eritrosit }}</h6>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            @foreach ($lab as $key => $value)
                                                                @if ($key == 'hm_eritrosit')
                                                                    <h6>{{ $value }}</h6>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">

                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Hematrokit :</p>
                                                            <h6>{{ $medical->hm_hematokrit }}</h6>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            @foreach ($lab as $key => $value)
                                                                @if ($key == 'hm_hematokrit')
                                                                    <h6>{{ $value }}</h6>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">

                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">MCV :</p>
                                                            <h6>{{ $medical->hm_mcv }}</h6>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            @foreach ($lab as $key => $value)
                                                                @if ($key == 'hm_mcv')
                                                                    <h6>{{ $value }}</h6>
                                                                @endif
                                                            @endforeach
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
                                                            <h6>{{ $medical->hm_mch }}</h6>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            @foreach ($lab as $key => $value)
                                                                @if ($key == 'hm_mch')
                                                                    <h6>{{ $value }}</h6>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">

                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">MCHC :</p>
                                                            <h6>{{ $medical->hm_mchc }}</h6>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            @foreach ($lab as $key => $value)
                                                                @if ($key == 'hm_mchc')
                                                                    <h6>{{ $value }}</h6>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">

                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">RDW :</p>
                                                            <h6>{{ $medical->hm_rdw }}</h6>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            @foreach ($lab as $key => $value)
                                                                @if ($key == 'hm_rdw')
                                                                    <h6>{{ $value }}</h6>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">

                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Leukosit :</p>
                                                            <h6>{{ $medical->hm_leukosit }}</h6>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            @foreach ($lab as $key => $value)
                                                                @if ($key == 'hm_leukosit')
                                                                    <h6>{{ $value }}</h6>
                                                                @endif
                                                            @endforeach
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
                                                            <h6>{{ $medical->hm_eos }}</h6>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            @foreach ($lab as $key => $value)
                                                                @if ($key == 'hm_eos')
                                                                    <h6>{{ $value }}</h6>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">

                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">BASO :</p>
                                                            <h6>{{ $medical->hm_baso }}</h6>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            @foreach ($lab as $key => $value)
                                                                @if ($key == 'hm_baso')
                                                                    <h6>{{ $value }}</h6>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">

                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Neutro :</p>
                                                            <h6>{{ $medical->hm_neutro }}</h6>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            @foreach ($lab as $key => $value)
                                                                @if ($key == 'hm_neutro')
                                                                    <h6>{{ $value }}</h6>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">

                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Limfo :</p>
                                                            <h6>{{ $medical->hm_limfo }}</h6>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            @foreach ($lab as $key => $value)
                                                                @if ($key == 'hm_limfo')
                                                                    <h6>{{ $value }}</h6>
                                                                @endif
                                                            @endforeach
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
                                                            <h6>{{ $medical->hm_mono }}</h6>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            @foreach ($lab as $key => $value)
                                                                @if ($key == 'hm_mono')
                                                                    <h6>{{ $value }}</h6>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">

                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">EOS Absolut :</p>
                                                            <h6>{{ $medical->hm_eos_absolut }}</h6>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            @foreach ($lab as $key => $value)
                                                                @if ($key == 'hm_eos_absolut')
                                                                    <h6>{{ $value }}</h6>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">

                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">BASO Absolut :</p>
                                                            <h6>{{ $medical->hm_baso_absolut }}</h6>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            @foreach ($lab as $key => $value)
                                                                @if ($key == 'hm_baso_absolut')
                                                                    <h6>{{ $value }}</h6>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">

                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Neutro Absolut :</p>
                                                            <h6>{{ $medical->hm_neutro_absolut }}</h6>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            @foreach ($lab as $key => $value)
                                                                @if ($key == 'hm_neutro_absolut')
                                                                    <h6>{{ $value }}</h6>
                                                                @endif
                                                            @endforeach
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
                                                            <h6>{{ $medical->hm_limfo_absolut }}</h6>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            @foreach ($lab as $key => $value)
                                                                @if ($key == 'hm_limfo_absolut')
                                                                    <h6>{{ $value }}</h6>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">

                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Mono Absolut :</p>
                                                            <h6>{{ $medical->hm_mono_absolut }}</h6>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            @foreach ($lab as $key => $value)
                                                                @if ($key == 'hm_mono_absolut')
                                                                    <h6>{{ $value }}</h6>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">

                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Trombosit :</p>
                                                            <h6>{{ $medical->hm_trombosit }}</h6>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            @foreach ($lab as $key => $value)
                                                                @if ($key == 'hm_trombosit')
                                                                    <h6>{{ $value }}</h6>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">

                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">LED :</p>
                                                            <h6>{{ $medical->hm_led }}</h6>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            @foreach ($lab as $key => $value)
                                                                @if ($key == 'hm_led')
                                                                    <h6>{{ $value }}</h6>
                                                                @endif
                                                            @endforeach
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
                                                            <h6>{{ $medical->u_warna }}</h6>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            @foreach ($lab as $key => $value)
                                                                @if ($key == 'u_warna')
                                                                    <h6>{{ $value }}</h6>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">

                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Kejernihan :</p>
                                                            <h6>{{ $medical->u_kejernihan }}</h6>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            @foreach ($lab as $key => $value)
                                                                @if ($key == 'u_kejernihan')
                                                                    <h6>{{ $value }}</h6>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">

                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Berat Jenis :</p>
                                                            <h6>{{ $medical->u_berat_jenis }}</h6>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            @foreach ($lab as $key => $value)
                                                                @if ($key == 'u_berat_jenis')
                                                                    <h6>{{ $value }}</h6>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">

                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">PH :</p>
                                                            <h6>{{ $medical->u_ph }}</h6>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            @foreach ($lab as $key => $value)
                                                                @if ($key == 'u_ph')
                                                                    <h6>{{ $value }}</h6>
                                                                @endif
                                                            @endforeach
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
                                                            <h6>{{ $medical->u_protein_albumin }}</h6>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            @foreach ($lab as $key => $value)
                                                                @if ($key == 'u_protein_albumin')
                                                                    <h6>{{ $value }}</h6>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">

                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Glukosa :</p>
                                                            <h6>{{ $medical->u_glukosa }}</h6>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            @foreach ($lab as $key => $value)
                                                                @if ($key == 'u_glukosa')
                                                                    <h6>{{ $value }}</h6>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">

                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Keton :</p>
                                                            <h6>{{ $medical->u_keton }}</h6>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            @foreach ($lab as $key => $value)
                                                                @if ($key == 'u_keton')
                                                                    <h6>{{ $value }}</h6>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">

                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Bilirubin :</p>
                                                            <h6>{{ $medical->u_bilirubin }}</h6>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            @foreach ($lab as $key => $value)
                                                                @if ($key == 'u_bilirubin')
                                                                    <h6>{{ $value }}</h6>
                                                                @endif
                                                            @endforeach
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
                                                            <h6>{{ $medical->u_urobilinogen }}</h6>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            @foreach ($lab as $key => $value)
                                                                @if ($key == 'u_urobilinogen')
                                                                    <h6>{{ $value }}</h6>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">

                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Nitrit :</p>
                                                            <h6>{{ $medical->u_nitrit }}</h6>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            @foreach ($lab as $key => $value)
                                                                @if ($key == 'u_nitrit')
                                                                    <h6>{{ $value }}</h6>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">

                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Leukosit Esterase :</p>
                                                            <h6>{{ $medical->u_leukosit_esterase }}</h6>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            @foreach ($lab as $key => $value)
                                                                @if ($key == 'u_leukosit_esterase')
                                                                    <h6>{{ $value }}</h6>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">

                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Darah Haem :</p>
                                                            <h6>{{ $medical->u_darah_haem }}</h6>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            @foreach ($lab as $key => $value)
                                                                @if ($key == 'u_darah_haem')
                                                                    <h6>{{ $value }}</h6>
                                                                @endif
                                                            @endforeach
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
                                                            <h6>{{ $medical->u_eri }}</h6>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            @foreach ($lab as $key => $value)
                                                                @if ($key == 'u_eri')
                                                                    <h6>{{ $value }}</h6>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">

                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Leuko :</p>
                                                            <h6>{{ $medical->u_leuko }}</h6>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            @foreach ($lab as $key => $value)
                                                                @if ($key == 'u_leuko')
                                                                    <h6>{{ $value }}</h6>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">

                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Epithel :</p>
                                                            <h6>{{ $medical->u_epithel }}</h6>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            @foreach ($lab as $key => $value)
                                                                @if ($key == 'u_epithel')
                                                                    <h6>{{ $value }}</h6>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">

                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Silinder :</p>
                                                            <h6>{{ $medical->u_silinder }}</h6>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            @foreach ($lab as $key => $value)
                                                                @if ($key == 'u_silinder')
                                                                    <h6>{{ $value }}</h6>
                                                                @endif
                                                            @endforeach
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
                                                            <h6>{{ $medical->u_kristal }}</h6>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            @foreach ($lab as $key => $value)
                                                                @if ($key == 'u_kristal')
                                                                    <h6>{{ $value }}</h6>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">

                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Lain-lain :</p>
                                                            <h6>{{ $medical->u_lain }}</h6>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            @foreach ($lab as $key => $value)
                                                                @if ($key == 'u_lain')
                                                                    <h6>{{ $value }}</h6>
                                                                @endif
                                                            @endforeach
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
                                                            <h6>{{ $medical->fh_sgot }}</h6>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            @foreach ($lab as $key => $value)
                                                                @if ($key == 'fh_sgot')
                                                                    <h6>{{ $value }}</h6>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">

                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">SGPT :</p>
                                                            <h6>{{ $medical->fh_sgpt }}</h6>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            @foreach ($lab as $key => $value)
                                                                @if ($key == 'fh_sgpt')
                                                                    <h6>{{ $value }}</h6>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">

                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Kolesterol Total :</p>
                                                            <h6>{{ $medical->fl_kolesterol_total }}</h6>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            @foreach ($lab as $key => $value)
                                                                @if ($key == 'fl_kolesterol_total')
                                                                    <h6>{{ $value }}</h6>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">

                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">HDL Kolesterol :</p>
                                                            <h6>{{ $medical->fl_hdl_kolesterol }}</h6>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            @foreach ($lab as $key => $value)
                                                                @if ($key == 'fl_hdl_kolesterol')
                                                                    <h6>{{ $value }}</h6>
                                                                @endif
                                                            @endforeach
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
                                                            <h6>{{ $medical->fl_ldl_kolesterol }}</h6>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            @foreach ($lab as $key => $value)
                                                                @if ($key == 'fl_ldl_kolesterol')
                                                                    <h6>{{ $value }}</h6>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">

                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Trigliserida :</p>
                                                            <h6>{{ $medical->fl_trigliserida }}</h6>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            @foreach ($lab as $key => $value)
                                                                @if ($key == 'fl_trigliserida')
                                                                    <h6>{{ $value }}</h6>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">

                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Glukosa Puasa :</p>
                                                            <h6>{{ $medical->gd_glukosa_puasa }}</h6>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            @foreach ($lab as $key => $value)
                                                                @if ($key == 'gd_glukosa_puasa')
                                                                    <h6>{{ $value }}</h6>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">

                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">JPP :</p>
                                                            <h6>{{ $medical->gd_jpp }}</h6>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            @foreach ($lab as $key => $value)
                                                                @if ($key == 'gd_jpp')
                                                                    <h6>{{ $value }}</h6>
                                                                @endif
                                                            @endforeach
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
                                                            <h6>{{ $medical->fg_bun }}</h6>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            @foreach ($lab as $key => $value)
                                                                @if ($key == 'fg_bun')
                                                                    <h6>{{ $value }}</h6>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">

                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Ureum :</p>
                                                            <h6>{{ $medical->fg_ureum }}</h6>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            @foreach ($lab as $key => $value)
                                                                @if ($key == 'fg_ureum')
                                                                    <h6>{{ $value }}</h6>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">

                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Kreatinin :</p>
                                                            <h6>{{ $medical->fg_kreatinin }}</h6>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            @foreach ($lab as $key => $value)
                                                                @if ($key == 'fg_kreatinin')
                                                                    <h6>{{ $value }}</h6>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">

                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">EGFR :</p>
                                                            <h6>{{ $medical->fg_egfr }}</h6>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            @foreach ($lab as $key => $value)
                                                                @if ($key == 'fg_egfr')
                                                                    <h6>{{ $value }}</h6>
                                                                @endif
                                                            @endforeach
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
                                                            <h6>{{ $medical->asam_urat }}</h6>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            @foreach ($lab as $key => $value)
                                                                @if ($key == 'asam_urat')
                                                                    <h6>{{ $value }}</h6>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">

                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">HBSAG :</p>
                                                            <h6>{{ $medical->hbsag }}</h6>
                                                            <p class="mb-1">Nilai Rujukan :</p>
                                                            @foreach ($lab as $key => $value)
                                                                @if ($key == 'hbsag')
                                                                    <h6>{{ $value }}</h6>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                            </div>
                                            <!--end row-->
                                        </div>
                                    </div>
                                </div>
                                <!--end card-body-->
                            </div><!-- end card -->
                        </div>
                        <!--end col-->
                    </div>
                    <!--end row-->
                    <!-- Tab panes -->
                    {{-- <div class="tab-content pt-4 text-muted">
                        <div class="tab-pane active" id="overview-tab" role="tabpanel">
                        </div>
                        <div class="tab-pane fade" id="hematologi" role="tabpanel">
                            <div class="row">
                                <div class="col-lg-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title mb-3">Profile Information</h5>
                                            <!-- <div class="table-responsive"> -->
                                            <div data-simplebar style="max-width: 453px;">
                                                <table class="table table-borderless table-nowrap mb-0">
                                                    <tbody>
                                                        <tr>
                                                            <td>
                                                                <div class="flex-grow-1">
                                                                    <p class="mb-1 text-muted">No Lab :</p>
                                                                    <h6 class="text-truncate mb-0">{{$medical->no_lab}}</h6>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <div class="flex-grow-1">
                                                                    <p class="mb-1 text-muted">Full Name :</p>
                                                                    <h6 class="text-truncate mb-0">{{$medical->employee->fullname}}</h6>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <div class="flex-grow-1">
                                                                    <p class="mb-1 text-muted">Gender :</p>
                                                                    <h6 class="text-truncate mb-0">{{$medical->employee->gender}}</h6>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <div class="flex-grow-1">
                                                                    <p class="mb-1 text-muted">Age :</p>
                                                                    <h6 class="text-truncate mb-0">{{$medical->umur}}</h6>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <div class="flex-grow-1">
                                                                    <p class="mb-1 text-muted">Score Framigham :</p>
                                                                    <h6 class="text-truncate mb-0">{{$medical->skor_framigham}}</h6>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <div class="flex-grow-1">
                                                                    <p class="mb-1 text-muted">Location :</p>
                                                                    @if (!empty($medical->employee->work_location))
                                                                    <h6 class="text-truncate mb-0">{{$medical->employee->work_location}}</h6>
                                                                    @else
                                                                    <h6 class="text-truncate mb-0">-</h6>
                                                                    @endif
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <!-- </div> -->
                                        </div><!-- end card body -->
                                    </div><!-- end card -->
                                </div>
                                <!--end col-->
                                <div class="col-lg-9">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title mb-3">Hematologi Information</h5>
                                            <div class="row">
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">
                                                       
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Hemoglobin :</p>
                                                            <h6>{{$medical->hm_hemoglobin}}</h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">
                  
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Eritrosit :</p>
                                                            <h6>{{$medical->hm_eritrosit}}</h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">
                  
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Hematrokit :</p>
                                                            <h6>{{$medical->hm_hematokrit}}</h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">
                  
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">MCV :</p>
                                                            <h6>{{$medical->hm_mcv}}</h6>
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
                                                            <h6>{{$medical->hm_mch}}</h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->   
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">
                  
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">MCHC :</p>
                                                            <h6>{{$medical->hm_mch}}</h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">
                                                       
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">RDW :</p>
                                                            <h6>{{$medical->hm_rdw}}</h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">
                  
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Leukosit :</p>
                                                            <h6>{{$medical->hm_leukosit}}</h6>
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
                                                            <h6>{{$medical->hm_eos}}</h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">
                  
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">BASO :</p>
                                                            <h6>{{$medical->hm_baso}}</h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->    
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">
                  
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Neutro :</p>
                                                            <h6>{{$medical->hm_neutro}}</h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">
                  
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Limfo :</p>
                                                            <h6>{{$medical->hm_limfo}}</h6>
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
                                                            <h6>{{$medical->hm_mono}}</h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">
                  
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">EOS Absolut :</p>
                                                            <h6>{{$medical->hm_eos_absolut}}</h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">
                  
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">BASO Absolut :</p>
                                                            <h6>{{$medical->hm_baso_absolut}}</h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->    
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">
                  
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Neutro Absolut :</p>
                                                            <h6>{{$medical->hm_neutro_absolut}}</h6>
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
                                                            <h6>{{$medical->hm_limfo_absolut}}</h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">
                  
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Mono Absolut :</p>
                                                            <h6>{{$medical->hm_mono_absolut}}</h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">
                  
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Trombosit :</p>
                                                            <h6>{{$medical->hm_trombosit}}</h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">
                  
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">LED :</p>
                                                            <h6>{{$medical->hm_led}}</h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                            </div>
                                            <!--end row-->
                                        </div>
                                        <!--end card-body-->
                                    </div><!-- end card -->
                                </div>
                                <!--end col-->
                            </div>
                            <!--end row-->
                        </div>
                        <!--end tab-pane-->
                        <div class="tab-pane fade" id="urine" role="tabpanel">
                            <div class="row">
                                <div class="col-lg-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title mb-3">Profile Information</h5>
                                            <!-- <div class="table-responsive"> -->
                                            <div data-simplebar style="max-width: 453px;">
                                                <table class="table table-borderless table-nowrap mb-0">
                                                    <tbody>
                                                        <tr>
                                                            <td>
                                                                <div class="flex-grow-1">
                                                                    <p class="mb-1 text-muted">No Lab :</p>
                                                                    <h6 class="text-truncate mb-0">{{$medical->no_lab}}</h6>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <div class="flex-grow-1">
                                                                    <p class="mb-1 text-muted">Full Name :</p>
                                                                    <h6 class="text-truncate mb-0">{{$medical->employee->fullname}}</h6>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <div class="flex-grow-1">
                                                                    <p class="mb-1 text-muted">Gender :</p>
                                                                    <h6 class="text-truncate mb-0">{{$medical->employee->gender}}</h6>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <div class="flex-grow-1">
                                                                    <p class="mb-1 text-muted">Age :</p>
                                                                    <h6 class="text-truncate mb-0">{{$medical->umur}}</h6>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <div class="flex-grow-1">
                                                                    <p class="mb-1 text-muted">Score Framigham :</p>
                                                                    <h6 class="text-truncate mb-0">{{$medical->skor_framigham}}</h6>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <div class="flex-grow-1">
                                                                    <p class="mb-1 text-muted">Location :</p>
                                                                    @if (!empty($medical->employee->work_location))
                                                                    <h6 class="text-truncate mb-0">{{$medical->employee->work_location}}</h6>
                                                                    @else
                                                                    <h6 class="text-truncate mb-0">-</h6>
                                                                    @endif
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <!-- </div> -->
                                        </div><!-- end card body -->
                                    </div><!-- end card -->
                                </div>
                                <!--end col-->
                                <div class="col-lg-9">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title mb-3">Urine Information</h5>
                                            <div class="row">
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">
                                                       
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Warna :</p>
                                                            <h6>{{$medical->u_warna}}</h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">
                  
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Kejernihan :</p>
                                                            <h6>{{$medical->u_kejernihan}}</h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">
                  
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Berat Jenis :</p>
                                                            <h6>{{$medical->u_berat_jenis}}</h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">
                  
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">PH :</p>
                                                            <h6>{{$medical->u_ph}}</h6>
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
                                                            <h6>{{$medical->u_protein_albumin}}</h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->   
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">
                  
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Glukosa :</p>
                                                            <h6>{{$medical->u_glukosa}}</h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">
                                                       
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Keton :</p>
                                                            <h6>{{$medical->u_keton}}</h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">
                  
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Bilirubin :</p>
                                                            <h6>{{$medical->u_bilirubin}}</h6>
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
                                                            <h6>{{$medical->u_urobilinogen}}</h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">
                  
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Nitrit :</p>
                                                            <h6>{{$medical->u_nitrit}}</h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->    
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">
                  
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Leukosit Esterase :</p>
                                                            <h6>{{$medical->u_leukosit_esterase}}</h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">
                  
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Darah Haem :</p>
                                                            <h6>{{$medical->u_darah_haem}}</h6>
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
                                                            <h6>{{$medical->u_eri}}</h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">
                  
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Leuko :</p>
                                                            <h6>{{$medical->u_leuko}}</h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">
                  
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Epithel :</p>
                                                            <h6>{{$medical->u_epithel}}</h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->    
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">
                  
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Silinder :</p>
                                                            <h6>{{$medical->u_silinder}}</h6>
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
                                                            <h6>{{$medical->u_kristal}}</h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">
                  
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Lain-lain :</p>
                                                            <h6>{{$medical->u_lain}}</h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                            </div>
                                            <!--end row-->
                                        </div>
                                        <!--end card-body-->
                                    </div><!-- end card -->
                                </div>
                                <!--end col-->
                            </div>
                            <!--end row-->
                        </div>
                        <!--end tab-pane-->
                        <div class="tab-pane fade" id="faal" role="tabpanel">
                            <div class="row">
                                <div class="col-lg-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title mb-3">Profile Information</h5>
                                            <!-- <div class="table-responsive"> -->
                                            <div data-simplebar style="max-width: 453px;">
                                                <table class="table table-borderless table-nowrap mb-0">
                                                    <tbody>
                                                        <tr>
                                                            <td>
                                                                <div class="flex-grow-1">
                                                                    <p class="mb-1 text-muted">No Lab :</p>
                                                                    <h6 class="text-truncate mb-0">{{$medical->no_lab}}</h6>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <div class="flex-grow-1">
                                                                    <p class="mb-1 text-muted">Full Name :</p>
                                                                    <h6 class="text-truncate mb-0">{{$medical->employee->fullname}}</h6>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <div class="flex-grow-1">
                                                                    <p class="mb-1 text-muted">Gender :</p>
                                                                    <h6 class="text-truncate mb-0">{{$medical->employee->gender}}</h6>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <div class="flex-grow-1">
                                                                    <p class="mb-1 text-muted">Age :</p>
                                                                    <h6 class="text-truncate mb-0">{{$medical->umur}}</h6>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <div class="flex-grow-1">
                                                                    <p class="mb-1 text-muted">Score Framigham :</p>
                                                                    <h6 class="text-truncate mb-0">{{$medical->skor_framigham}}</h6>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <div class="flex-grow-1">
                                                                    <p class="mb-1 text-muted">Location :</p>
                                                                    @if (!empty($medical->employee->work_location))
                                                                    <h6 class="text-truncate mb-0">{{$medical->employee->work_location}}</h6>
                                                                    @else
                                                                    <h6 class="text-truncate mb-0">-</h6>
                                                                    @endif
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <!-- </div> -->
                                        </div><!-- end card body -->
                                    </div><!-- end card -->
                                </div>
                                <!--end col-->
                                <div class="col-lg-9">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title mb-3">Faal Information</h5>
                                            <div class="row">
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">
                                                       
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">SGOT :</p>
                                                            <h6>{{$medical->fh_sgot}}</h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">
                  
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">SGPT :</p>
                                                            <h6>{{$medical->fh_sgpt}}</h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">
                  
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Kolesterol Total :</p>
                                                            <h6>{{$medical->fl_kolesterol_total}}</h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">
                  
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">HDL Kolesterol :</p>
                                                            <h6>{{$medical->fl_hdl_kolesterol}}</h6>
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
                                                            <h6>{{$medical->fl_ldl_kolesterol}}</h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->   
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">
                  
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Trigliserida :</p>
                                                            <h6>{{$medical->fl_trigliserida}}</h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">
                                                       
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Glukosa Puasa :</p>
                                                            <h6>{{$medical->gd_glukosa_puasa}}</h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">
                  
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">JPP :</p>
                                                            <h6>{{$medical->gd_jpp}}</h6>
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
                                                            <h6>{{$medical->fg_bun}}</h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">
                  
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Ureum :</p>
                                                            <h6>{{$medical->fg_ureum}}</h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->    
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">
                  
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">Kreatinin :</p>
                                                            <h6>{{$medical->fg_kreatinin}}</h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">
                  
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">EGFR :</p>
                                                            <h6>{{$medical->fg_egfr}}</h6>
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
                                                            <h6>{{$medical->asam_urat}}</h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-3">
                                                    <div class="d-flex mt-4">
                  
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">HBSAG :</p>
                                                            <h6>{{$medical->hbsag}}</h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end col-->                                                                                            
                                            </div>
                                            <!--end row-->
                                        </div>
                                        <!--end card-body-->
                                    </div><!-- end card -->
                                </div>
                                <!--end col-->
                            </div>
                            <!--end row-->
                        </div>
                        <!--end tab-pane-->
                    </div> --}}
                    <!--end tab-content-->
                </div>
            </div>
            <!--end col-->
        </div>
        <!--end row-->
    </div><!-- container-fluid -->
    <!--modal lampiran mcu-->
    <div class="modal flip" id="modal-lampiran-mcu" tabindex="-1" aria-labelledby="exampleModalgridLabel"
        aria-modal="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-judul"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if ($cek_pdf == 1)
                        <embed src="{{ route('lampiran.mcu', $id) }}" frameborder="0" width="100%" height="550px">
                    @else
                        <center>
                            <lord-icon src="https://cdn.lordicon.com/tdrtiskw.json" trigger="loop"
                                colors="primary:#f7b84b,secondary:#405189" style="width:130px;height:130px">
                            </lord-icon>
                            <div class="mt-4 pt-4">
                                <h4>No data available...!</h4>
                            </div>
                        </center>
                    @endif
                    <div class="modal-footer">
                    </div>
                </div>
            </div>
        </div>
    @endsection
    @section('script')
        <!-- AJAX -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script>
        <!-- Include JS -->
        <script src="{{ asset('assets/flip/js/flipbook.min.js') }}"></script>
    @endsection
    @section('javascript')
        <script type="text/javascript">
            $(document).ready(function() {
                $("#read").flipBook({
                    //Layout Setting
                    pdfUrl: "{{ route('lampiran.mcu', $id) }}",
                    lightBox: true,
                    layout: 3,
                    currentPage: {
                        vAlign: "bottom",
                        hAlign: "left"
                    },
                    // BTN SETTING
                    btnShare: {
                        enabled: false
                    },
                    btnPrint: {
                        hideOnMobile: true
                    },
                    btnDownloadPages: {
                        enabled: true,
                        title: "Download pages",
                        icon: "fa-download",
                        icon2: "file_download",
                        url: "{{ route('lampiran.mcu', $id) }}",
                        name: "allPages.zip",
                        hideOnMobile: false
                    },
                    btnColor: 'rgb(255,120,60)',
                    sideBtnColor: 'rgb(255,120,60)',
                    sideBtnSize: 60,
                    sideBtnBackground: "rgba(0,0,0,.7)",
                    sideBtnRadius: 60,
                    btnSound: {
                        vAlign: "top",
                        hAlign: "left"
                    },
                    btnAutoplay: {
                        vAlign: "top",
                        hAlign: "left"
                    },
                    // SHARING
                    btnShare: {
                        enabled: false,
                        title: "Share",
                        icon: "fa-share-alt"
                    },
                    facebook: {
                        enabled: false,
                        url: "ismanyan.github.io/Pdf_flipbook.demo.github.io/pdf/pdf.pdf"
                    },
                    google_plus: {
                        enabled: false
                    },
                    email: {
                        enabled: false,
                        url: "https://ismanyan.github.io/Pdf_flipbook.demo.github.io/pdf/pdf.pdf",
                        title: "PDF KPK",
                        description: "Silahkan click link di bawah untuk melihat / mengunduf pdf"
                    },
                    twitter: {
                        enabled: false,
                        url: "https://ismanyan.github.io/Pdf_flipbook.demo.github.io/pdf/pdf.pdf"
                    },
                    pinterest: {
                        enabled: false,
                        url: "https://ismanyan.github.io/Pdf_flipbook.demo.github.io/pdf/pdf.pdf"
                    }
                });
            })
        </script>
    @endsection
