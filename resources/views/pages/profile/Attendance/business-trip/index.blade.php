{{-- @extends('layouts.master') --}}
@extends(Auth::user()->can('emp.menu') ? 'layouts.general' : 'layouts.master')

@section('link')
   <meta name="csrf-token" content="{{ csrf_token() }}">
   <style>
    .limit-text{
        max-width: 200px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
   </style>
   <!-- Required meta tags -->
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
   <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css" />
   <!-- Datatables-->
   <link href="{{ url('') }}/assets/libs/Datatables/DataTables-1.13.1/css/dataTables.bootstrap5.css" rel="stylesheet"
      type="text/css" />
   <link href="{{ url('') }}/assets/libs/Datatables/Buttons-2.3.3/css/buttons.dataTables.css" rel="stylesheet"
      type="text/css" />
   <link href="{{ url('') }}/assets/libs/Datatables/Responsive-2.4.0/css/responsive.bootstrap.css" rel="stylesheet"
      type="text/css" />
   <!-- Toastr Notifications-->
   <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
   <!-- Select2-->
   <link rel="stylesheet" href="{{ url('') }}/assets/libs/adminlte/select2/css/4.1.0/select2.min.css">
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
@endsection

@section('content')
   <div class="container-fluid">
    @if (!Auth::user()->can('emp.menu'))
      <div class="profile-foreground position-relative mx-n4 mt-n4">
         <div class="profile-wid-bg">
            <img src="/assets/images/salonpas-bg.jpg" alt="" class="profile-wid-img" />
         </div>
      </div>
      <div class="pt-4 mb-4 mb-lg-3 pb-lg-4">
         <div class="row g-4">
            <div class="col-auto">
               <div class="profile-user position-relative d-inline-block mx-auto">
                  @if (!empty($user->employee->avatar))
                     <div id="avatar-user">
                        <img src="{{ asset('storage/avatars/' . $user->employee->avatar) }}"
                           class="show-image rounded-circle avatar-xl img-thumbnail user-profile-image"
                           alt="user-profile-image">
                     </div>
                  @else
                     <div id="avatar-user">
                        <img src="{{ asset('storage/avatars/user.jpg') }}"
                           class="rounded-circle avatar-xl img-thumbnail user-profile-image"
                           alt="user-profile-image">
                     </div>
                  @endif
                  <div class="avatar-xs p-0 rounded-circle profile-photo-edit">
                     <input onchange="avatarValidation(this);" id="profile-img-file-input" type="file"
                        name="image" class="image profile-img-file-input"
                        accept="image/jpg,image/jpeg,image/png,image/JPG,image/JPEG,image/PNG">
                     <label for="profile-img-file-input" class="profile-photo-edit avatar-xs">
                        <span class="avatar-title rounded-circle bg-light text-body">
                           <i class="ri-camera-fill"></i>
                        </span>
                     </label>
                  </div>
               </div>
            </div>
            <!--end col-->
            <div class="col">
               <div class="p-2">
                  <h3 class="text-white mb-1">{{ $user->employee->fullname }}</h3>
                  <p class="text-white-75">{{ $user->employee->email }}</p>
                  <div class="hstack text-white-50 gap-1">
                     <div class="me-2"><i class="ri-map-pin-user-line me-1 text-white-75 fs-16 align-middle"></i>
                        {{ $user->employee->area->name }}
                     </div>
                     <div><i class="ri-building-line me-1 text-white-75 fs-16 align-middle"></i>
                        {{ $user->employee->department->name }}
                     </div>
                  </div>
                  <div class="hstack text-white-50 gap-1">
                     <div class="me-2">
                        @if (!empty($user->employee->level->nama))
                           <i class="ri-contacts-book-2-line me-1 text-white-75 fs-16 align-middle"></i>
                           {{ $user->employee->level->nama }}
                        @endif
                     </div>
                     <div>
                        @if (!empty($user->employee->position->nama))
                           <i class="ri-contacts-book-line me-1 text-white-75 fs-16 align-middle"></i>
                           {{ $user->employee->position->nama }}
                        @endif
                     </div>
                  </div>
               </div>
            </div>
            <!--end col-->
            <div class="col-12 col-lg-auto order-last order-lg-0">
               <div class="row text text-white-50 text-center">
                  <div class="col-lg-6 col-4">
                     <div class="p-2">
                        <!-- <h4 class="text-white mb-1">{{ $user->employee->nik }}</h4>
                                 <p class="fs-14 mb-0">NIK</p> -->
                     </div>
                  </div>
               </div>
            </div>
            <!--end col-->
         </div>
         <!--end row-->
      </div>
      @endif

      <div class="row">
         <div class="col-lg-12">
                <div class="d-flex">
                    @if (!Auth::user()->can('emp.menu'))
                        @include('partials.navbar2')
                    @endif
                </div>
               <div class="row pt-4">
                  <div class="col-12">
                     <div class="row">
                        @if ($isApprover)
                           <div class="col-md-12 col-12">
                              <div class="card border-start border-primary border-4 shadow-sm">
                                 <div class="card-body">
                                    <div class="mb-3">
                                        <div class="flex-shrink-0 d-flex align-items-center">
                                            <i class="ri-file-search-fill text-primary fs-1 me-2"></i>
                                            <div>
                                                <h5 class="mb-0 fw-bold">
                                                    Waiting Status
                                                </h5>
                                                <small class="text-muted">
                                                    Approval Queue
                                                </small>
                                            </div>
                                        </div>
                                        <div class="row g-3">
                                            <!-- BUSINESS TRIP -->
                                            <div class="col-lg-4 col-md-6">
                                                <div class="border rounded p-3 h-100">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar-sm me-3">
                                                                <span class="avatar-title bg-primary-subtle text-white rounded">
                                                                    <i class="ri-briefcase-line fs-4"></i>
                                                                </span>
                                                            </div>
                                                            <div>
                                                                <div class="fw-semibold">
                                                                    Pengajuan Perjalanan Dinas
                                                                </div>
                                                                <small class="text-muted" id="business-trip-text">
                                                                    Menunggu Approval
                                                                </small>
                                                            </div>
                                                        </div>
                                                        <span class="badge bg-warning fs-6 px-3" id="business-trip-count">
                                                            0
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- CLAIM -->
                                            <div class="col-lg-4 col-md-6">
                                                <div class="border rounded p-3 h-100">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar-sm me-3">
                                                                <span class="avatar-title bg-success-subtle text-white rounded">
                                                                    <i class="ri-file-list-3-line fs-4"></i>
                                                                </span>
                                                            </div>
                                                            <div>
                                                                <div class="fw-semibold" >
                                                                    Report atau Pengajuan Claim
                                                                </div>
                                                                <small class="text-muted" id="claim-text">
                                                                    Menunggu Review
                                                                </small>
                                                            </div>
                                                        </div>
                                                        <span class="badge bg-info fs-6 px-3" id="claim-count">
                                                            0
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- CANCEL -->
                                            <div class="col-lg-4 col-md-6">
                                                <div class="border rounded p-3 h-100">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar-sm me-3">
                                                                <span class="avatar-title bg-danger-subtle text-white rounded">
                                                                    <i class="ri-close-circle-line fs-4"></i>
                                                                </span>
                                                            </div>
                                                            <div>
                                                                <div class="fw-semibold" >
                                                                    Pembatalan Perjalanan Dinas
                                                                </div>
                                                                <small class="text-muted" id="cancel-text">
                                                                    Menunggu Approval
                                                                </small>
                                                            </div>
                                                        </div>
                                                        <span class="badge bg-danger fs-6 px-3" id="cancel-count">
                                                            0
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        @endif
                     </div>

                     <div class="card">
                        <ul class="nav nav-tabs nav-tabs-custom nav-success" role="tablist">
                           <li class="nav-item">
                              <a class="nav-link py-3 active" id="tab-rule" data-bs-toggle="tab" href="#pill-myData"
                              role="tab">
                                 <i class="ri-survey-line me-1 align-bottom"></i> Data Perjalanan Dinas
                              </a>
                           </li>
                           <li class="nav-item">
                              <a class="nav-link py-3 " id="tab-benefit" data-bs-toggle="tab" href="#pill-my-report-claim"
                              role="tab">
                                 <i class="ri-survey-line me-1 align-bottom"></i> Data Report/Claim Perjalanan Dinas
                              </a>
                           </li>
                           <li class="nav-item">
                              <a class="nav-link py-3 " id="tab-benefit" data-bs-toggle="tab" href="#pill-my-cancellation"
                              role="tab">
                                 <i class="ri-survey-line me-1 align-bottom"></i> Data Pembatalan Perjalanan Dinas
                              </a>
                           </li>

                           {{-- ======================================== Merupakan Approval ========================================  --}}
                            @if ($isApprover)
                           <li class="nav-item">
                                <a class="nav-link py-3" id="tab-benefit" data-bs-toggle="tab" href="#pill-approval"
                                role="tab">
                                <i class="bi bi-clipboard-check me-1 align-bottom"></i> Persetujuan Perjalanan Dinas
                                </a>
                           </li>
                           <li class="nav-item">
                                <a class="nav-link py-3" id="tab-benefit" data-bs-toggle="tab" href="#pill-report-claim-approval"
                                role="tab">
                                <i class="bi bi-clipboard-check me-1 align-bottom"></i> Report / Persetujuan Claim
                                </a>
                           </li>
                           <li class="nav-item">
                                <a class="nav-link py-3" id="tab-benefit" data-bs-toggle="tab" href="#pill-cancellation-approval"
                                role="tab">
                                <i class="bi bi-clipboard-check me-1 align-bottom"></i> Pembatalan Perjalanan Dinas
                                </a>
                           </li>
                            @endif
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="pill-myData" role="tabpanel">
                                <div class="card-body">
                                    <div class="d-flex justify-content-start mb-3 gap-3">
                                        <div class="mb-3">
                                        <a class="btn btn-primary" href="{{ route('business-trip.propose-create') }}">
                                            Pengusulan Perjalanan Dinas
                                        </a>
                                    </div>
                                    </div>
                                    <table class="table table-striped dt-responsive nowrap w-100" id="table-my-business_trip">
                                        <thead>
                                            <tr>
                                                <th class="text-center">No</th>
                                                <th class="text-center">No Document</th>
                                                <th class="text-center">Tanggal Pengajuan</th>
                                                <th class="text-center">Tipe Perjalanan Dinas</th>
                                                <th class="text-center">Tanggal Perjalanan Dinas</th>
                                                <th class="text-center">Jam Berangkat dan Tiba</th>
                                                <th class="text-center">Berangkat Dari</th>
                                                <th class="text-center">Tujuan</th>
                                                <th class="text-center" style="width: 200px;"> Keperluan </th>
                                                <th class="text-center">Status</th>
                                                <th class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                    </table>

                                </div>
                            </div>
                            <div class="tab-pane fade" id="pill-my-report-claim" role="tabpanel">
                                <div class="card-body">
                                    <div class="mb-3">
                                        <a class="btn btn-primary" href="{{ route('business-trip-report.create') }}">
                                            Claim / Report Perjalanan Dinas
                                        </a>
                                    </div>
                                    <table class="table table-striped dt-responsive nowrap w-100" id="table-my-report-claim">
                                        <thead>
                                            <tr>
                                                <th class="text-center">No</th>
                                                <th class="text-center">No Document</th>
                                                <th class="text-center">Tanggal Pengajuan</th>
                                                <th class="text-center">Tipe Perjalanan Dinas</th>
                                                <th class="text-center">Tanggal Perjalanan Dinas</th>
                                                <th class="text-center">Tujuan</th>
                                                <th class="text-center" style="width: 200px;"> Keperluan </th>
                                                <th class="text-center">Status</th>
                                                <th class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                    </table>

                                </div>
                            </div>
                            <div class="tab-pane fade" id="pill-my-cancellation" role="tabpanel">
                                <div class="card-body">
                                    <table class="table table-striped dt-responsive nowrap w-100" id="table-my-cancellation">
                                        <thead>
                                            <tr>
                                                <th class="text-center">No</th>
                                                <th class="text-center">No Document</th>
                                                <th class="text-center">Tanggal Pengajuan Pembatalan</th>
                                                <th class="text-center">Tipe Perjalanan Dinas</th>
                                                <th class="text-center">Tanggal Perjalanan Dinas</th>
                                                <th class="text-center">Alasan Pembatalan</th>
                                                <th class="text-center">Total Kerugian Biaya</th>
                                                <th class="text-center">Kerugian Biaya Yang Ditanggung</th>
                                                <th class="text-center">Status</th>
                                                <th class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                    </table>

                                </div>
                            </div>
                            {{-- <div class="d-flex justify-content-start mb-3 gap-2">
                                            <button id="btn-bulk-mode" type="button" class="btn btn-primary">
                                                <i class="ri-checkbox-multiple-line"></i> Bulk Approve
                                            </button>

                                            <button id="btn-approved-selected" class="btn btn-success d-none">
                                                <i class="ri-check-double-line"></i> Approved Selected
                                            </button>
                                            <button id="btn-revised-selected" class="btn btn-info d-none">
                                                <i class="ri-check-double-line"></i> Revised Selected
                                            </button>
                                            <button id="btn-rejected-selected" class="btn btn-danger d-none">
                                                <i class="ri-check-double-line"></i> Rejected Selected
                                            </button>

                                            <button id="btn-cancel-bulk" type="button" class="btn btn-dark d-none">
                                                <i class="ri-close-line"></i> Cancel
                                            </button>
                                        </div> --}}

                            <!-- ================= APPROVAL (WAITING) ================= -->
                            <div class="tab-pane fade" id="pill-approval" role="tabpanel">
                                <div class="card-body">
                                    <div class="flex gap-2 mb-3" role="tablist">
                                        <a class="btn btn-outline-primary active" id="tab-rule" data-bs-toggle="tab"
                                        href="#approval-process" role="tab">
                                        <i class="ri-survey-line me-1 align-bottom"></i> Approval
                                        </a>
                                        <a class="btn btn-outline-primary" id="tab-benefit" data-bs-toggle="tab"
                                        href="#approval-history"
                                        role="tab">
                                        <i class="bi bi-clipboard-check me-1 align-bottom"></i> History
                                        </a>
                                    </div>
                                    <div class="tab-content">
                                        <div class="tab-pane active" id="approval-process" role="tabpanel">
                                            <table class="table table-striped dt-responsive nowrap w-100" id="table-approval-business_trip">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center">No</th>
                                                        <th class="text-center">No Document</th>
                                                        <th class="text-center">NIK</th>
                                                        <th class="text-center">Nama</th>
                                                        <th class="text-center">Area</th>
                                                        <th class="text-center">Department</th>
                                                        <th class="text-center">Position</th>
                                                        <th class="text-center">Tanggal Pengajuan</th>
                                                        <th class="text-center">Tipe Perjalanan Dinas</th>
                                                        <th class="text-center">Tanggal Perjalanan Dinas</th>
                                                        <th class="text-center">Jam Berangkat dan Tiba</th>
                                                        <th class="text-center">Berangkat Dari</th>
                                                        <th class="text-center">Tujuan</th>
                                                        <th class="text-center" style="width: 200px;"> Keperluan </th>
                                                        <th class="text-center">Status</th>
                                                        <th class="text-center">Action</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                        <div class="tab-pane" id="approval-history" role="tabpanel">
                                            <table class="table table-striped dt-responsive nowrap w-100" id="table-approval-history">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center">No</th>
                                                        <th class="text-center">No Document</th>
                                                        <th class="text-center">NIK</th>
                                                        <th class="text-center">Nama</th>
                                                        <th class="text-center">Area</th>
                                                        <th class="text-center">Department</th>
                                                        <th class="text-center">Position</th>
                                                        <th class="text-center">Tanggal Pengajuan</th>
                                                        <th class="text-center">Tipe Perjalanan Dinas</th>
                                                        <th class="text-center">Tanggal Perjalanan Dinas</th>
                                                        <th class="text-center">Jam Berangkat dan Tiba</th>
                                                        <th class="text-center">Berangkat Dari</th>
                                                        <th class="text-center">Tujuan</th>
                                                        <th class="text-center" style="width: 200px;"> Keperluan </th>
                                                        <th class="text-center">Status</th>
                                                        <th class="text-center">Action</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="pill-report-claim-approval" role="tabpanel">
                                <div class="card-body">
                                    <div class="flex gap-2 mb-3" role="tablist">
                                        <a class="btn btn-outline-primary active" id="tab-rule" data-bs-toggle="tab"
                                        href="#report-claim-process" role="tab">
                                        <i class="ri-survey-line me-1 align-bottom"></i> Approval
                                        </a>
                                        <a class="btn btn-outline-primary" id="tab-benefit" data-bs-toggle="tab"
                                        href="#report-claim-history"
                                        role="tab">
                                        <i class="bi bi-clipboard-check me-1 align-bottom"></i> History
                                        </a>
                                    </div>
                                    <div class="tab-content">
                                        <div class="tab-pane active" id="report-claim-process" role="tabpanel">
                                            <table class="table table-striped dt-responsive nowrap w-100" id="table-report-claim-approval">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center">No</th>
                                                        <th class="text-center">No Document</th>
                                                        <th class="text-center">NIK</th>
                                                        <th class="text-center">Nama</th>
                                                        <th class="text-center">Area</th>
                                                        <th class="text-center">Department</th>
                                                        <th class="text-center">Position</th>
                                                        <th class="text-center">Tanggal Pengajuan</th>
                                                        <th class="text-center">Tipe Perjalanan Dinas</th>
                                                        <th class="text-center">Tanggal Perjalanan Dinas</th>
                                                        <th class="text-center">Tujuan</th>
                                                        <th class="text-center" style="width: 200px;"> Keperluan </th>
                                                        <th class="text-center">Status</th>
                                                        <th class="text-center">Action</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                        <div class="tab-pane" id="report-claim-history" role="tabpanel">
                                            <table class="table table-striped dt-responsive nowrap w-100" id="table-report-claim-history">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center">No</th>
                                                        <th class="text-center">No Document</th>
                                                        <th class="text-center">NIK</th>
                                                        <th class="text-center">Nama</th>
                                                        <th class="text-center">Area</th>
                                                        <th class="text-center">Department</th>
                                                        <th class="text-center">Position</th>
                                                        <th class="text-center">Tanggal Pengajuan</th>
                                                        <th class="text-center">Tipe Perjalanan Dinas</th>
                                                        <th class="text-center">Tanggal Perjalanan Dinas</th>
                                                        <th class="text-center">Tujuan</th>
                                                        <th class="text-center" style="width: 200px;"> Keperluan </th>
                                                        <th class="text-center">Status</th>
                                                        <th class="text-center">Action</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="pill-cancellation-approval" role="tabpanel">
                                <div class="card-body">
                                    <div class="flex gap-2 mb-3" role="tablist">
                                        <a class="btn btn-outline-primary active" id="tab-rule" data-bs-toggle="tab"
                                        href="#cancellation-process" role="tab">
                                        <i class="ri-survey-line me-1 align-bottom"></i> Approval
                                        </a>
                                        <a class="btn btn-outline-primary" id="tab-benefit" data-bs-toggle="tab"
                                        href="#cancellation-history"
                                        role="tab">
                                        <i class="bi bi-clipboard-check me-1 align-bottom"></i> History
                                        </a>
                                    </div>

                                    <div class="tab-content">
                                        <div class="tab-pane active" id="cancellation-process" role="tabpanel">
                                            <table class="table table-striped dt-responsive nowrap w-100" id="table-cancellation-approval">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center">No</th>
                                                        <th class="text-center">No Document</th>
                                                        <th class="text-center">NIK</th>
                                                        <th class="text-center">Nama</th>
                                                        <th class="text-center">Area</th>
                                                        <th class="text-center">Department</th>
                                                        <th class="text-center">Position</th>
                                                        <th class="text-center">Tanggal Pengajuan Pembatalan</th>
                                                        <th class="text-center">Tipe Perjalanan Dinas</th>
                                                        <th class="text-center">Tanggal Perjalanan Dinas</th>
                                                        <th class="text-center">Total Kerugian</th>
                                                        <th class="text-center">Kerugian Biaya Yang di Tanggung</th>
                                                        <th class="text-center">Alasan Pembatalan</th>
                                                        <th class="text-center">Status</th>
                                                        <th class="text-center">Action</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                        <div class="tab-pane" id="cancellation-history" role="tabpanel">
                                            <table class="table table-striped dt-responsive nowrap w-100" id="table-cancellation-history">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center">No</th>
                                                        <th class="text-center">No Document</th>
                                                        <th class="text-center">NIK</th>
                                                        <th class="text-center">Nama</th>
                                                        <th class="text-center">Area</th>
                                                        <th class="text-center">Department</th>
                                                        <th class="text-center">Position</th>
                                                        <th class="text-center">Tanggal Pengajuan Pembatalan</th>
                                                        <th class="text-center">Tipe Perjalanan Dinas</th>
                                                        <th class="text-center">Tanggal Perjalanan Dinas</th>
                                                        <th class="text-center">Total Kerugian</th>
                                                        <th class="text-center">Kerugian Biaya Yang di Tanggung</th>
                                                        <th class="text-center">Alasan Pembatalan</th>
                                                        <th class="text-center">Status</th>
                                                        <th class="text-center">Action</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                     </div>
                  </div>
               </div>
         </div>
         <!--end col-->
      </div>
      <!--end row-->
   </div><!-- container-fluid -->

{{-- ================================= MODAL DETAIL FOR PROPOSE BUSINESS TRIP ================================= --}}
<div class="modal fade" id="proposeDetailModal" data-bs-focus="false" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <input type="hidden" id="approval_id">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            {{-- ================= HEADER ================= --}}
            <div class="modal-header bg-light border-0 pb-0">
                <div>
                    <h4 class="mb-1 fw-bold text-black">
                        Business Trip Detail
                    </h4>
                    <div class="">
                        <span id="detail_document_number"></span>
                        •
                        <span id="detail_trip_type_badge"></span>
                    </div>
                </div>
                <div class="justify-content-end">
                    <button type="button" class="btn-close " data-bs-dismiss="modal"></button>
                </div>
            </div>
            <div class="modal-body">
                {{-- ================= TOP SUMMARY ================= --}}
                <div class="row g-3 mb-2">
                    <div class="col-md-3">
                        <div class="border rounded-3 p-3 h-100">
                            <small class="text-muted d-block mb-1">
                                Employee
                            </small>
                            <div class="fw-semibold"
                                 id="detail_employee">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded-3 p-3 h-100">
                            <small class="text-muted d-block mb-1">
                                Date
                            </small>
                            <div class="fw-semibold"
                                 id="detail_date">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded-3 p-3 h-100">
                            <small class="text-muted d-block mb-1">
                                Route
                            </small>
                            <div class="fw-semibold"
                                 id="detail_route">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded-3 p-3 h-100">
                            <small class="text-muted d-block mb-1">
                                Total Estimation
                            </small>
                            <div class="fw-bold text-primary fs-5"
                                 id="detail_total_cost">
                            </div>
                        </div>
                    </div>
                </div>
                {{-- ================= PURPOSE ================= --}}
                <div class="border rounded-3 p-3 mb-4">
                    <div class="fw-semibold mb-2">
                        Purpose
                    </div>
                    <div class="text-muted"
                         id="detail_purpose">
                    </div>
                </div>
                {{-- ================= COST ================= --}}
                <div class="mb-2">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar-xs me-2">
                            <span class="avatar-title bg-primary-subtle text-white rounded">
                                <i class="ri-wallet-3-line"></i>
                            </span>
                        </div>
                        <div class="fw-semibold">
                            Estimasi Biaya
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Kategori</th>
                                    <th>Unit</th>
                                    <th>Total</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody id="detail-cost-wrapper">
                            </tbody>
                        </table>
                    </div>
                </div>
                {{-- ================= TRANSPORT & HOTEL ================= --}}
                <div class="row g-3 mb-4">
                    {{-- TRANSPORT --}}
                    <div class="col-md-6">
                        <div class="border rounded-3 p-3 h-100">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar-xs me-2">
                                    <span class="avatar-title bg-info-subtle text-white rounded">
                                        <i class="ri-car-line"></i>
                                    </span>
                                </div>
                                <div class="fw-semibold">
                                    Transportasi
                                </div>
                            </div>
                            <div id="detail-transport-content">
                            </div>
                        </div>
                    </div>
                    {{-- HOTEL --}}
                    <div class="col-md-6">
                        <div class="border rounded-3 p-3 h-100">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-xs me-2">
                                        <span class="avatar-title bg-warning-subtle text-white rounded">
                                            <i class="ri-hotel-line"></i>
                                        </span>
                                    </div>
                                    <div class="fw-semibold">
                                        Hotel
                                    </div>
                                </div>
                                <div id="hotel-reservation-badge">
                                </div>
                            </div>
                            <div id="detail-hotel-content">
                            </div>
                        </div>
                    </div>
                </div>
                {{-- ================= APPROVAL ================= --}}
                <div class="mb-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar-xs me-2">
                            <span class="avatar-title bg-success-subtle text-white rounded">
                                <i class="ri-check-double-line"></i>
                            </span>
                        </div>
                        <div class="fw-semibold">
                            Approval Flow
                        </div>
                    </div>
                    <div id="approval-wrapper"
                         class="d-flex flex-column gap-2">
                    </div>
                </div>
                {{-- ================= FOOTER ACTION ================= --}}
                <div class="modal-footer border-0 pt-0">
                    <div class="w-100 d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <!-- LEFT INFO -->
                        <div>
                            <small class="text-muted">

                            </small>
                        </div>
                        <!-- RIGHT ACTION -->
                        <div class="d-flex flex-wrap gap-2">
                            <!-- CANCEL -->
                            <div id="cancel-propose-section">
                                <button type="button"
                                {{-- data-id="{{ encrypt($businessTrip->id) }}" --}}
                                        class="btn btn-secondary btn-label waves-effect waves-light"
                                        id="btn-cancel">
                                    <i class="ri-forbid-2-line label-icon align-middle fs-16 me-2"></i>
                                    Pembatalan Perjalanan Dinas
                                </button>
                            </div>
                            <!-- APPROVAL -->
                            <div id="approval-propose-section"class="d-flex gap-2">
                                <!-- REVISED -->
                                <button type="button"
                                        class="btn btn-warning btn-label waves-effect waves-light"
                                        id="btn-revised">
                                    <i class="ri-edit-2-line label-icon align-middle fs-16 me-2"></i>
                                    Revised
                                </button>
                                <!-- REJECT -->
                                <button type="button"
                                        class="btn btn-danger btn-label waves-effect waves-light"
                                        id="btn-rejected">
                                    <i class="ri-close-circle-line label-icon align-middle fs-16 me-2"></i>
                                    Reject
                                </button>
                                <!-- APPROVE -->
                                <button type="button"
                                        class="btn btn-success btn-label waves-effect waves-light"
                                        id="btn-approved">
                                    <i class="ri-check-double-line label-icon align-middle fs-16 me-2"></i>
                                    Approve
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ================================= MODAL DETAIL FOR REPORT/CLAIM BUSINESS TRIP ================================= --}}
<div class="modal fade" id="reportClaimDetailModal" data-bs-focus="false" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <input type="hidden" id="approval_report_id">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            {{-- ================= HEADER ================= --}}
            <div class="modal-header bg-light border-0 pb-0">
                <div>
                    <h4 class="mb-1 fw-bold text-black">
                        Business Report Detail
                    </h4>
                    <div class="">
                        <span id="detail_report_document_number"></span>
                        •
                        <span id="detail_report_trip_type_badge"></span>
                    </div>
                </div>
                <div class="justify-content-end">
                    <button type="button" class="btn-close " data-bs-dismiss="modal"></button>
                </div>
            </div>
            <div class="modal-body">
                {{-- ================= TOP SUMMARY ================= --}}
                <div class="row g-3 mb-2">
                    <div class="col-md-3">
                        <div class="border rounded-3 p-3 h-100">
                            <small class="text-muted d-block mb-1">
                                Employee
                            </small>
                            <div class="fw-semibold"
                                 id="detail_report_employee">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded-3 p-3 h-100">
                            <small class="text-muted d-block mb-1">
                                Date
                            </small>
                            <div class="fw-semibold"
                                 id="detail_report_date">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded-3 p-3 h-100">
                            <small class="text-muted d-block mb-1">
                                Tujuan
                            </small>
                            <div class="fw-semibold"
                                 id="detail_report_route">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded-3 p-3 h-100">
                            <small class="text-muted d-block mb-1">
                                Total Estimation
                            </small>
                            <div class="fw-bold text-primary fs-5"
                                 id="detail_report_total_cost">
                            </div>
                        </div>
                    </div>
                </div>
                {{-- ================= PURPOSE ================= --}}
                <div class="border rounded-3 p-3 mb-4">
                    <div class="fw-semibold mb-2">
                        Purpose
                    </div>
                    <div class="text-muted"
                         id="detail_report_purpose">
                    </div>
                </div>
                {{-- ================= COST ================= --}}
                <div class="mb-2">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar-xs me-2">
                            <span class="avatar-title bg-primary-subtle text-white rounded">
                                <i class="ri-wallet-3-line"></i>
                            </span>
                        </div>
                        <div class="fw-semibold">
                            Estimasi Biaya Makan
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>hari</th>
                                    <th>Kategori</th>
                                    <th>Nominal</th>
                                    <th>Attachment/Nota</th>
                                </tr>
                            </thead>
                            <tbody id="detail-meal-wrapper">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar-xs me-2">
                            <span class="avatar-title bg-primary-subtle text-white rounded">
                                <i class="ri-wallet-3-line"></i>
                            </span>
                        </div>
                        <div class="fw-semibold">
                            Estimasi Biaya Lainnya
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Kategori</th>
                                    <th>Nominal</th>
                                    <th>Jumlah</th>
                                    <th>Total</th>
                                    <th>Keterangan</th>
                                    <th>Attachment/Nota</th>
                                </tr>
                            </thead>
                            <tbody id="detail-expense-wrapper">
                            </tbody>
                        </table>
                    </div>
                </div>
                {{-- ================= APPROVAL ================= --}}
                <div class="mb-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar-xs me-2">
                            <span class="avatar-title bg-success-subtle text-white rounded">
                                <i class="ri-check-double-line"></i>
                            </span>
                        </div>
                        <div class="fw-semibold">
                            Approval Flow
                        </div>
                    </div>
                    <div id="approval-report-wrapper"
                         class="d-flex flex-column gap-2">
                    </div>
                </div>
                {{-- ================= FOOTER ACTION ================= --}}
                <div class="modal-footer border-0 pt-0">
                    <div class="w-100 d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <!-- LEFT INFO -->
                        <div>
                            <small class="text-muted">

                            </small>
                        </div>
                        <!-- RIGHT ACTION -->
                        <div class="d-flex flex-wrap gap-2">
                            <!-- APPROVAL -->
                            <div id="approval-report-section"class="d-flex gap-2">
                                <!-- REVISED -->
                                <button type="button"
                                        class="btn btn-warning btn-label waves-effect waves-light"
                                        id="btn-report-revised">
                                    <i class="ri-edit-2-line label-icon align-middle fs-16 me-2"></i>
                                    Revised
                                </button>
                                <!-- REJECT -->
                                <button type="button"
                                        class="btn btn-danger btn-label waves-effect waves-light"
                                        id="btn-report-rejected">
                                    <i class="ri-close-circle-line label-icon align-middle fs-16 me-2"></i>
                                    Reject
                                </button>
                                <!-- APPROVE -->
                                <button type="button"
                                        class="btn btn-success btn-label waves-effect waves-light"
                                        id="btn-report-approved">
                                    <i class="ri-check-double-line label-icon align-middle fs-16 me-2"></i>
                                    Approve
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ========================================== MODAL DETAIL FOR BUSINESS TRIP CANCELLATION ========================================== --}}
<div class="modal fade" id="cancellationDetailModal" data-bs-focus="false" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <input type="hidden" id="approval_cancel_id">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            {{-- ================= HEADER ================= --}}
            <div class="modal-header bg-light border-0 pb-0">
                <div>
                    <h4 class="mb-1 fw-bold text-black">
                        Cancel Business Trip
                    </h4>
                    <div class="">
                        <span id="detail_trip_document_number"></span>
                        •
                        <span id="detail_cancel_type_badge"></span>
                    </div>
                </div>
                <div class="justify-content-end">
                    <button type="button" class="btn-close " data-bs-dismiss="modal"></button>
                </div>
            </div>
            <div class="modal-body">
                {{-- ================= TOP SUMMARY ================= --}}
                <div class="row g-3 mb-2">
                    <div class="col-md-3">
                        <div class="border rounded-3 p-3 h-100">
                            <small class="text-muted d-block mb-1">
                                Employee
                            </small>
                            <div class="fw-semibold"
                                id="detail_trip_employee">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="border rounded-3 p-3 h-100">
                            <small class="text-muted d-block mb-1">
                                Date
                            </small>
                            <div class="fw-semibold"
                                id="detail_trip_date">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="border rounded-3 p-3 h-100">
                            <small class="text-muted d-block mb-1">
                                Tujuan
                            </small>
                            <div class="fw-semibold"
                                id="detail_trip_route">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="border rounded-3 p-3 h-100">
                            <small class="text-muted d-block mb-1">
                                Total Kerugian
                            </small>
                            <div class="fw-bold text-danger fs-5"
                                id="detail_cancel_loss_amount">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="border rounded-3 p-3 h-100 bg-success-subtle">
                            <small class="text-muted d-block mb-1">
                                Ditanggung Perusahaan
                            </small>
                            <div class="fw-bold text-success fs-5"
                                id="detail_company_covered_amount">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="border rounded-3 p-3 h-100 bg-warning-subtle">
                            <small class="text-muted d-block mb-1">
                                Ditanggung Karyawan
                            </small>
                            <div class="fw-bold text-warning fs-5"
                                id="detail_employee_covered_amount">
                            </div>
                        </div>
                    </div>

                </div>
                {{-- ================= PURPOSE ================= --}}
                <div class="border rounded-3 p-3 mb-4">
                    <div class="fw-semibold mb-2">
                        Alasan Pembatalan
                    </div>
                    <div class="text-muted"
                         id="detail_reason_cancel">
                    </div>
                </div>
                {{-- ================= COST ================= --}}
                <div class="mb-2">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar-xs me-2">
                            <span class="avatar-title bg-primary-subtle text-white rounded">
                                <i class="ri-wallet-3-line"></i>
                            </span>
                        </div>
                        <div class="fw-semibold">
                            List Biaya Kerugian
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Kategori</th>
                                    <th>Nominal</th>
                                    <th>Jumlah</th>
                                    <th>Total</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody id="detail-list-loss">
                            </tbody>
                        </table>
                    </div>
                </div>
                {{-- ================= APPROVAL ================= --}}
                <div class="mb-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar-xs me-2">
                            <span class="avatar-title bg-success-subtle text-white rounded">
                                <i class="ri-check-double-line"></i>
                            </span>
                        </div>
                        <div class="fw-semibold">
                            Approval Flow
                        </div>
                    </div>
                    <div id="approval-cancel-wrapper"
                         class="d-flex flex-column gap-2">
                    </div>
                </div>
                {{-- ================= FOOTER ACTION ================= --}}
                <div class="modal-footer border-0 pt-0">
                    <div class="w-100 d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <!-- LEFT INFO -->
                        <div>
                            <small class="text-muted">

                            </small>
                        </div>
                        <!-- RIGHT ACTION -->
                        <div class="d-flex flex-wrap gap-2">
                            <!-- APPROVAL -->
                            <div id="approval-cancellation-section"class="d-flex gap-2">
                                <!-- REJECT -->
                                <button type="button"
                                        class="btn btn-danger btn-label waves-effect waves-light"
                                        id="btn-cancel-rejected">
                                    <i class="ri-close-circle-line label-icon align-middle fs-16 me-2"></i>
                                    Reject
                                </button>
                                <!-- APPROVE -->
                                <button type="button"
                                        class="btn btn-success btn-label waves-effect waves-light"
                                        id="btn-cancel-approved">
                                    <i class="ri-check-double-line label-icon align-middle fs-16 me-2"></i>
                                    Approve
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
   <!-- Datatables -->
   <script src="{{ url('') }}/assets/libs/Datatables/DataTables-1.13.1/js/jquery.dataTables.min.js"></script>
   <script src="{{ url('') }}/assets/libs/Datatables/DataTables-1.13.1/js/dataTables.bootstrap5.min.js"></script>
   <script src="{{ url('') }}/assets/libs/Datatables/Responsive-2.4.0/js/dataTables.responsive.min.js"></script>
   <script src="{{ url('') }}/assets/js/pages/datatables.init.js"></script>
   <!-- profile-setting init js -->
   <script src="{{ url('') }}/assets/js/pages/profile-setting.init.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.js"></script>
   <!-- Sweetalert -->
   <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
   <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
   <!-- Toastr Notifications-->
   <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
   <!-- Select2 -->
   <script src="{{ url('') }}/assets/libs/adminlte/select2/js/4.1.0/select2.min.js"></script>
@endsection

@section('javascript')
<script>
    $(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    loadApprovalCountWaiting();
    let hash = window.location.hash;
    if(hash === '#pill-approval'){
        new bootstrap.Tab(
            $('a[href="#pill-approval"]')[0]
        ).show();
        new bootstrap.Tab(
            $('a[href="#approval-process"]')[0]
        ).show();
    }
    if(hash === '#pill-report-claim'){
        new bootstrap.Tab(
            $('a[href="#pill-report-claim-approval"]')[0]
        ).show();
        new bootstrap.Tab(
            $('a[href="#report-claim-process"]')[0]
        ).show();
    }
    if(hash === '#pill-cancellation'){
        new bootstrap.Tab(
            $('a[href="#pill-cancellation-approval"]')[0]
        ).show();
        new bootstrap.Tab(
            $('a[href="#cancellation-process"]')[0]
        ).show();
    }

    $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
    activeTab = $(e.target).attr("href");
    if (activeTab === '#pill-myData') {
        tableMyData.ajax.reload();
        loadApprovalCountWaiting();
    }
    if (activeTab === '#pill-my-cancellation') {
        tableMyCancellation.ajax.reload();
        loadApprovalCountWaiting();
    }
    if (activeTab === '#pill-my-report-claim') {
        tableMyReportClaim.ajax.reload();
        loadApprovalCountWaiting();
    }
    // isApprover
    if (activeTab === '#pill-approval') {
        tableApprovalData.ajax.reload();
        loadApprovalCountWaiting();
    }
    if (activeTab === '#approval-process') {
        tableApprovalData.ajax.reload();
        loadApprovalCountWaiting();
    }
    if (activeTab === '#approval-history') {
        tableApprovalHistory.ajax.reload();
        loadApprovalCountWaiting();
    }
        if (activeTab === '#pill-report-claim-approval') {
        tableReportClaimData.ajax.reload();
        loadApprovalCountWaiting();
    }
    if (activeTab === '#report-claim-process') {
        tableReportClaimData.ajax.reload();
        loadApprovalCountWaiting();
    }
    if (activeTab === '#report-claim-history') {
        tableReportClaimHistory.ajax.reload();
        loadApprovalCountWaiting();
    }
    if (activeTab === '#pill-cancellation-approval') {
        tableCancellationApproval.ajax.reload();
        loadApprovalCountWaiting();
    }
    if (activeTab === '#cancellation-process') {
        tableCancellationApproval.ajax.reload();
        loadApprovalCountWaiting();
    }
    if (activeTab === '#cancellation-history') {
        tableCancellationHistory.ajax.reload();
        loadApprovalCountWaiting();
    }
});

    // ================================================================== Busines Trip Section ==================================================================
    let tableMyData = $('#table-my-business_trip').DataTable({
        processing: true,
        responsive: false,
        serverSide: false,
        scrollX: true,
        ajax: "{{ route('business-trip.my-business-trip-data') }}",
        columns: [
            { data: 'DT_RowIndex', className: 'text-center', orderable: false, searchable: false},
            { data: 'no_document', className: 'text-center' },
            { data: 'request_date', className: 'text-center' },
            { data: 'tipe', className: 'text-center' },
            { data: 'date_and_day', className: 'text-center' },
            { data: 'dept_and_arr_times', className: 'text-center' },
            { data: 'depart_from', className: 'text-center' },
            { data: 'arrival_to', className: 'text-center' },
            {
                data: 'needs',
                className: 'text-center',
                render: function(data){
                    return `
                        <div class="limit-text"
                            title="${data}">
                            ${data}
                        </div>
                    `;
                }
            },
            {   data: 'status',
                className: 'text-center',
                render: function (data) {
                    const map = {
                        draft: `
                            <span class="badge bg-secondary">
                                Draft
                            </span>
                        `,
                        revised: `
                            <span class="badge bg-info">
                                Revised
                            </span>
                        `,
                        waiting: `
                            <span class="badge bg-warning text-white">
                                Waiting Approval
                            </span>
                        `,
                        approved: `
                            <span class="badge bg-success">
                                Approved
                            </span>
                        `,
                        ongoing: `
                            <span class="badge bg-warning">
                                Ongoing
                            </span>
                        `,
                        reported: `
                            <span class="badge bg-dark">
                                Reported
                            </span>
                        `,
                        completed: `
                            <span class="badge bg-success text-white">
                                Completed
                            </span>
                        `,
                        cancelled: `
                            <span class="badge bg-danger text-white ">
                                Cancelled
                            </span>
                        `,
                        rejected: `
                            <span class="badge bg-danger">
                                Rejected
                            </span>
                        `,
                        cancel_waiting: `
                            <span class="badge bg-warning">
                                Cancellation Waiting
                            </span>
                        `,
                    };
                    return map[data] ?? `
                        <span class="badge bg-light text-dark">
                            ${data}
                        </span>
                    `;
                }
            },
            { data: 'action', className: "text-center", orderable: false, searchable: false},
        ]
    });

    let tableApprovalData = $('#table-approval-business_trip').DataTable({
        processing: true,
        responsive: false,
        serverSide: false,
        scrollX: true,
        ajax: "{{ route('business-trip.approval-data') }}",
        columns: [
            { data: 'DT_RowIndex', className: 'text-center', orderable: false, searchable: false},
            { data: 'no_document', className: 'text-center' },
            { data: 'nik', className:"text-center" },
            { data: 'employee_name', className:"text-center" },
            { data: 'area', className:"text-center"},
            { data: 'department', className:"text-center" },
            { data: 'position', className:"text-center" },
            { data: 'request_date', className: 'text-center' },
            { data: 'tipe', className: 'text-center' },
            { data: 'date_and_day', className: 'text-center' },
            { data: 'dept_and_arr_times', className: 'text-center' },
            { data: 'depart_from', className: 'text-center' },
            { data: 'arrival_to', className: 'text-center' },
            {
                data: 'needs',
                className: 'text-center',
                render: function(data){
                    return `
                        <div class="limit-text"
                            title="${data}">
                            ${data}
                        </div>
                    `;
                }
            },
            {   data: 'status',
                className: 'text-center',
                render: function (data) {
                    const map = {
                        waiting: `
                            <span class="badge bg-warning text-white">
                                Waiting Approval
                            </span>
                        `,
                        revised: `
                            <span class="badge bg-info">
                                Revised
                            </span>
                        `,
                    };
                    return map[data] ?? `
                        <span class="badge bg-light text-white">
                            ${data}
                        </span>
                    `;
                }
            },
            { data: 'action', className: 'text-center', orderable: false, searchable: false}
        ]
    });
    let tableApprovalHistory = $('#table-approval-history').DataTable({
        processing: true,
        responsive: false,
        serverSide: false,
        scrollX: true,
        ajax: "{{ route('business-trip.approval-history') }}",
        columns: [
            { data: 'DT_RowIndex', className: 'text-center', orderable: false, searchable: false},
            { data: 'no_document', className: 'text-center' },
            { data: 'nik', className:"text-center" },
            { data: 'employee_name', className:"text-center" },
            { data: 'area', className:"text-center"},
            { data: 'department', className:"text-center" },
            { data: 'position', className:"text-center" },
            { data: 'request_date', className: 'text-center' },
            { data: 'tipe', className: 'text-center' },
            { data: 'date_and_day', className: 'text-center' },
            { data: 'dept_and_arr_times', className: 'text-center' },
            { data: 'depart_from', className: 'text-center' },
            { data: 'arrival_to', className: 'text-center' },
            {
                data: 'needs',
                className: 'text-center',
                render: function(data){
                    return `
                        <div class="limit-text"
                            title="${data}">
                            ${data}
                        </div>
                    `;
                }
            },
            {   data: 'status',
                className: 'text-center',
                render: function (data) {
                    const map = {
                        approved: `
                            <span class="badge bg-success">
                                Approved
                            </span>
                        `,
                        rejected: `
                            <span class="badge bg-danger">
                                Rejected
                            </span>
                        `,
                    };
                    return map[data] ?? `
                        <span class="badge bg-light text-white">
                            ${data}
                        </span>
                    `;
                }
            },
            { data: 'action', className: 'text-center', orderable: false, searchable: false}
        ]
    });
    $(document).on('click', '.btn-detail-myData', function () {
        let id = $(this).data('id');
        $.ajax({
            url: "{{ route('business-trip.my-business-trip-detail', ':id') }}"
                .replace(':id', id),
            type: 'GET',
            success: function(res) {
                // hide approval action
                if (res.can_action) {
                    $('#cancel-propose-section').show();
                } else {
                    $('#cancel-propose-section').hide();
                }
                $('#approval-propose-section').addClass('d-none');
                fillProposeDetailModal(res);
                $('#proposeDetailModal').modal('show');
            }
        });
    });

    $(document).on('click', '.btn-detail-approval', function () {
        let id = $(this).data('id');
        $.ajax({
            url: "{{ route('business-trip.approval-detail', ':id') }}"
                .replace(':id', id),
            type: 'GET',
            success: function(res) {
                // show approval action
                $('#approval-propose-section').removeClass('d-none');
                // simpan approval id
                $('#approval_id').val(res.approval_id);
                if (res.can_action) {
                    $('#approval-propose-section').show();
                    $('#cancel-propose-section').hide();
                } else {
                    $('#approval-propose-section').hide();
                    $('#cancel-propose-section').hide();

                }
                fillProposeDetailModal(res);
                $('#proposeDetailModal').modal('show');
            }
        });
    });
    $(document).on('click', '.btn-detail-approval-history', function () {
        let id = $(this).data('id');
        // console.log('clicked');
        $.ajax({
            url: "{{ route('business-trip.approval-detail', ':id') }}"
                .replace(':id', id),
            type: 'GET',
            success: function(res) {
                // show approval action
                $('#approval-propose-section').addClass('d-none');
                $('#cancel-propose-section').addClass('d-none');
                fillProposeDetailModal(res);
                $('#proposeDetailModal').modal('show');
            }
        });
    });
    // =============================== SINGLE APPROVE AND REJECT =================================
    $('#btn-approved').on('click', function () {
        processApproval('approved');
    });
    // ================= REVISED =================
    $('#btn-revised').on('click', function () {
        processApproval('revised');
    });
    // ================= REJECTED =================
    $('#btn-rejected').on('click', function () {
        processApproval('rejected');
    });

    function processApproval(action)
    {
        let approvalId = $('#approval_id').val();
        let title = '';
        let text = '';
        let icon = '';
        if (action === 'approved') {
            title = 'Approve Pengajuan?';
            text = 'Anda dapat memberikan catatan approval (opsional)';
            icon = 'success';
        }
        else if (action === 'revised') {
            title = 'Revisi Pengajuan?';
            text = 'Berikan alasan revisi';
            icon = 'warning';
        }
        else if (action === 'rejected') {
            title = 'Reject Pengajuan?';
            text = 'Berikan alasan penolakan';
            icon = 'error';
        }
        // $('#detailModal').modal('hide');
        Swal.fire({
            // target: document.getElementById('proposeDetailModal'),
            title: title,
            text: text,
            icon: icon,
            input: 'textarea',
            backdrop: true,
            allowOutsideClick: false,
            allowEscapeKey: false,
            showCancelButton: true,
            cancelButtonText: 'Batal',
            didOpen: () => {
                setTimeout(() => {
                    $('.swal2-textarea').focus();
                }, 100);
            },
            inputPlaceholder:
                action === 'approved'
                    ? 'Tambahkan catatan approval (opsional)'
                    : action === 'revised'
                        ? 'Masukkan alasan revisi...'
                        : 'Masukkan alasan penolakan...',
            inputAttributes: {
                autocapitalize: 'off'
            },
            showCancelButton: true,
            confirmButtonText:
                action === 'approved'
                    ? 'Ya, Approve'
                    : action === 'revised'
                        ? 'Ya, Revised'
                        : 'Ya, Reject',
            cancelButtonText: 'Batal',
            confirmButtonColor:
                action === 'approved'
                    ? '#0ab39c'
                    : action === 'revised'
                        ? '#f7b84b'
                        : '#f06548',
            preConfirm: (reason) => {
                // wajib isi untuk revised & rejected
                if (
                    (action === 'revised' ||
                    action === 'rejected') &&
                    !reason
                ) {
                    Swal.showValidationMessage(
                        'Alasan wajib diisi'
                    );
                    return false;
                }
                return reason;
            }
        }).then((result) => {
            if (!result.isConfirmed) {
                return;
            }
            $.ajax({
                url: "{{ route('business-trip.single-process-approval') }}",
                type: "POST",
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    id: approvalId,
                    action: action,
                    reason: result.value
                },
                success: function(res) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: res.message,
                        timer: 1800,
                        showConfirmButton: false
                    });
                    $('#proposeDetailModal').modal('hide');
                    tableApprovalData.ajax.reload();
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text:
                            xhr.responseJSON?.message ??
                            'Terjadi kesalahan'
                    });
                }
            });
        });
    }

    $(document).on('click', '.edit-btn', function () {
        let id = $(this).data('id');
        window.location.href =
            "{{ route('business-trip.propose-edit', ':id') }}"
                .replace(':id', id);
    });

    // =========================================================== REPORT / CLAIM ===========================================================

    let tableMyReportClaim = $('#table-my-report-claim').DataTable({
        processing: true,
        responsive: false,
        serverSide: false,
        scrollX: true,
        ajax: "{{ route('business-trip.my-report-claim-data') }}",
        columns: [
            { data: 'DT_RowIndex', className: 'text-center', orderable: false, searchable: false},
            { data: 'no_document', className: 'text-center' },
            { data: 'propose_date', className: 'text-center' },
            { data: 'type', className: 'text-center' },
            { data: 'date_and_day', className: 'text-center' },
            { data: 'arrival_to', className: 'text-center' },
            {
                data: 'needs',
                className: 'text-center',
                render: function(data){
                    return `
                        <div class="limit-text"
                            title="${data}">
                            ${data}
                        </div>
                    `;
                }
            },
            {   data: 'status',
                className: 'text-center',
                render: function (data) {
                    const map = {
                        draft: `
                            <span class="badge bg-secondary">
                                Draft
                            </span>
                        `,
                        revised: `
                            <span class="badge bg-info">
                                Revised
                            </span>
                        `,
                        waiting: `
                            <span class="badge bg-warning text-white">
                                Waiting Approval
                            </span>
                        `,
                        approved: `
                            <span class="badge bg-success">
                                Approved
                            </span>
                        `,
                        ongoing: `
                            <span class="badge bg-warning">
                                Ongoing
                            </span>
                        `,
                        reported: `
                            <span class="badge bg-dark">
                                Reported
                            </span>
                        `,
                        completed: `
                            <span class="badge bg-success">
                                Completed
                            </span>
                        `,
                        cancelled: `
                            <span class="badge bg-light text-dark border">
                                Cancelled
                            </span>
                        `,
                        rejected: `
                            <span class="badge bg-danger">
                                Rejected
                            </span>
                        `,
                    };
                    return map[data] ?? `
                        <span class="badge bg-light text-dark">
                            ${data}
                        </span>
                    `;
                }
            },
            { data: 'action', className: "text-center", orderable: false, searchable: false},
        ]
    });

    let tableReportClaimData = $('#table-report-claim-approval').DataTable({
        processing: true,
        responsive: false,
        serverSide: false,
        scrollX: true,
        ajax: "{{ route('business-trip.report-claim-data') }}",
        columns: [
            { data: 'DT_RowIndex', className: 'text-center', orderable: false, searchable: false},
            { data: 'no_document', className: 'text-center' },
            { data: 'nik', className:"text-center" },
            { data: 'employee_name', className:"text-center" },
            { data: 'area', className:"text-center"},
            { data: 'department', className:"text-center" },
            { data: 'position', className:"text-center" },
            { data: 'request_date', className: 'text-center' },
            { data: 'type', className: 'text-center' },
            { data: 'date_and_day', className: 'text-center' },
            { data: 'arrival_to', className: 'text-center' },
            {
                data: 'needs',
                className: 'text-center',
                render: function(data){
                    return `
                        <div class="limit-text"
                            title="${data}">
                            ${data}
                        </div>
                    `;
                }
            },
            {   data: 'status',
                className: 'text-center',
                render: function (data) {
                    const map = {
                        waiting: `
                            <span class="badge bg-warning text-white">
                                Waiting Approval
                            </span>
                        `,
                        revised: `
                            <span class="badge bg-info">
                                Revised
                            </span>
                        `,
                    };
                    return map[data] ?? `
                        <span class="badge bg-light text-white">
                            ${data}
                        </span>
                    `;
                }
            },
            { data: 'action', className: 'text-center', orderable: false, searchable: false}
        ]
    });
    let tableReportClaimHistory = $('#table-report-claim-history').DataTable({
        processing: true,
        responsive: false,
        serverSide: false,
        scrollX: true,
        ajax: "{{ route('business-trip.report-claim-history') }}",
        columns: [
            { data: 'DT_RowIndex', className: 'text-center', orderable: false, searchable: false},
            { data: 'no_document', className: 'text-center' },
            { data: 'nik', className:"text-center" },
            { data: 'employee_name', className:"text-center" },
            { data: 'area', className:"text-center"},
            { data: 'department', className:"text-center" },
            { data: 'position', className:"text-center" },
            { data: 'request_date', className: 'text-center' },
            { data: 'type', className: 'text-center' },
            { data: 'date_and_day', className: 'text-center' },
            { data: 'arrival_to', className: 'text-center' },
            {
                data: 'needs',
                className: 'text-center',
                render: function(data){
                    return `
                        <div class="limit-text"
                            title="${data}">
                            ${data}
                        </div>
                    `;
                }
            },
            {   data: 'status',
                className: 'text-center',
                render: function (data) {
                    const map = {
                        approved: `
                            <span class="badge bg-success">
                                Approved
                            </span>
                        `,
                        rejected: `
                            <span class="badge bg-danger">
                                Rejected
                            </span>
                        `,
                    };
                    return map[data] ?? `
                        <span class="badge bg-light text-white">
                            ${data}
                        </span>
                    `;
                }
            },
            { data: 'action', className: 'text-center', orderable: false, searchable: false}
        ]
    });

    $(document).on('click', '.btn-detail-myReportClaim', function () {
        let id = $(this).data('id');
        $.ajax({
            url: "{{ route('business-trip.my-report-claim-detail', ':id') }}"
                .replace(':id', id),
            type: 'GET',
            success: function(res) {
                // hide approval action
                $('#approval-report-section').addClass('d-none');
                fillReportDetailModal(res);
                $('#reportClaimDetailModal').modal('show');
            }
        });
    });
    $(document).on('click', '.btn-detail-reportClaim', function () {
        let id = $(this).data('id');
        // console.log('clicked');
        $.ajax({
            url: "{{ route('business-trip.report-claim-detail', ':id') }}"
                .replace(':id', id),
            type: 'GET',
            success: function(res) {
                console.log(res.approval_id);
                // show approval action
                $('#approval-report-section').removeClass('d-none');
                // simpan approval id
                $('#approval_report_id').val(res.approval_id);
                if (res.can_action) {
                    $('#approval-report-section').show();
                } else {
                    $('#approval-report-section').hide();
                }
                fillReportDetailModal(res);
                $('#reportClaimDetailModal').modal('show');
            }
        });
    });
    $(document).on('click', '.btn-detail-reportClaimHistory', function () {
        let id = $(this).data('id');
        // console.log('clicked');
        $.ajax({
            url: "{{ route('business-trip.report-claim-detail', ':id') }}"
                .replace(':id', id),
            type: 'GET',
            success: function(res) {
                // show approval action
                $('#approval-report-section').addClass('d-none');
                fillReportDetailModal(res);
                $('#reportClaimDetailModal').modal('show');
            }
        });
    });

    $('#btn-report-approved').on('click', function () {
        processReportApproval('approved');
    });
    // ================= REVISED =================
    $('#btn-report-revised').on('click', function () {
        processReportApproval('revised');
    });
    // ================= REJECTED =================
    $('#btn-report-rejected').on('click', function () {
        processReportApproval('rejected');
    });
    function processReportApproval(action)
    {
        let approvalId = $('#approval_report_id').val();
        let title = '';
        let text = '';
        let icon = '';
        if (action === 'approved') {
            title = 'Approve Pengajuan?';
            text = 'Anda dapat memberikan catatan approval (opsional)';
            icon = 'success';
        }
        else if (action === 'revised') {
            title = 'Revisi Pengajuan?';
            text = 'Berikan alasan revisi';
            icon = 'warning';
        }
        else if (action === 'rejected') {
            title = 'Reject Pengajuan?';
            text = 'Berikan alasan penolakan';
            icon = 'error';
        }
        // $('#detailModal').modal('hide');
        Swal.fire({
            // target: document.getElementById('proposeDetailModal'),
            title: title,
            text: text,
            icon: icon,
            input: 'textarea',
            backdrop: true,
            allowOutsideClick: false,
            allowEscapeKey: false,
            showCancelButton: true,
            cancelButtonText: 'Batal',
            didOpen: () => {
                setTimeout(() => {
                    $('.swal2-textarea').focus();
                }, 100);
            },
            inputPlaceholder:
                action === 'approved'
                    ? 'Tambahkan catatan approval (opsional)'
                    : action === 'revised'
                        ? 'Masukkan alasan revisi...'
                        : 'Masukkan alasan penolakan...',
            inputAttributes: {
                autocapitalize: 'off'
            },
            showCancelButton: true,
            confirmButtonText:
                action === 'approved'
                    ? 'Ya, Approve'
                    : action === 'revised'
                        ? 'Ya, Revised'
                        : 'Ya, Reject',
            cancelButtonText: 'Batal',
            confirmButtonColor:
                action === 'approved'
                    ? '#0ab39c'
                    : action === 'revised'
                        ? '#f7b84b'
                        : '#f06548',
            preConfirm: (reason) => {
                // wajib isi untuk revised & rejected
                if (
                    (action === 'revised' ||
                    action === 'rejected') &&
                    !reason
                ) {
                    Swal.showValidationMessage(
                        'Alasan wajib diisi'
                    );
                    return false;
                }
                return reason;
            }
        }).then((result) => {
            if (!result.isConfirmed) {
                return;
            }
            $.ajax({
                url: "{{ route('business-trip.single-process-Report') }}",
                type: "POST",
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    id: approvalId,
                    action: action,
                    reason: result.value
                },
                success: function(res) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: res.message,
                        timer: 1800,
                        showConfirmButton: false
                    });
                    $('#reportClaimDetailModal').modal('hide');
                    tableReportClaimData.ajax.reload();
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text:
                            xhr.responseJSON?.message ??
                            'Terjadi kesalahan'
                    });
                }
            });
        });
    }

    $(document).on('click', '.editReport-btn', function () {
        let id = $(this).data('id');
        window.location.href =
            "{{ route('business-trip-report.edit', ':id') }}"
                .replace(':id', id);
    });

    // ============================================================= CANCELLATION SECTION =============================================================
    let tableMyCancellation = $('#table-my-cancellation').DataTable({
        processing: true,
        responsive: false,
        serverSide: false,
        scrollX: true,
        ajax: "{{ route('business-trip.my-cancellation') }}",
        columns: [
            { data: 'DT_RowIndex', className: 'text-center', orderable: false, searchable: false},
            { data: 'no_document', className: 'text-center' },
            { data: 'propose_date', className: 'text-center' },
            { data: 'type', className: 'text-center' },
            { data: 'date_and_day', className: 'text-center' },
            {
                data: 'reason_cancel',
                className: 'text-center',
                render: function(data){
                    const map = {
                        emergency:`
                            <strong> Kondisi Darurat atau Alasan Kesehatan </strong>
                        `,
                        company_decision:`
                            <strong> Perubahan Keputusan Perusahaan </strong>
                        `,
                        force_majeure:`
                            <strong> Force Majeure </strong>
                        `,
                        personal_reasons:`
                            <strong> Alasan Pribadi </strong>
                        `,
                        other:`
                            <strong> Lainnya </strong>
                        `,
                    };
                    let reasonText =
                        map[data.reason_cancel]
                        ??
                        data.reason_cancel;
                    return `
                        <div>
                            <div>
                                ${reasonText}
                            </div>
                            ${
                                data.reason_other
                                ?
                                `
                                <small class="text-muted d-block mt-1">
                                    ${data.reason_other}

                                </small>
                                `
                                :
                                ''
                            }
                        </div>
                    `;
                }
            },
            {
                data: 'total_cost_lost',
                className: 'text-center',
                render: function(data){

                    if(!data){
                        return 'IDR 0';
                    }

                    return 'IDR ' +
                        Number(data)
                        .toLocaleString('id-ID');

                }
            },
            {
                data: 'lost_costs_incurred',
                className: 'text-center',
                render: function(data){
                    let employee =
                        Number(
                            data.employee ?? 0
                        ).toLocaleString(
                            'id-ID'
                        );
                    let company =
                        Number(
                            data.company ?? 0
                        ).toLocaleString(
                            'id-ID'
                        );
                    return `
                        <div class="text-start">
                            <div>
                                <strong>
                                    Employee :
                                </strong>
                                IDR ${employee}
                            </div>
                            <div>
                                <strong>
                                    Company :
                                </strong>
                                IDR ${company}
                            </div>
                        </div>
                    `;
                }
            },
            {   data: 'status',
                className: 'text-center',
                render: function (data) {
                    const map = {
                        draft: `
                            <span class="badge bg-secondary">
                                Draft
                            </span>
                        `,
                        revised: `
                            <span class="badge bg-info">
                                Revised
                            </span>
                        `,
                        approved: `
                            <span class="badge bg-success">
                                Approved
                            </span>
                        `,
                        rejected: `
                            <span class="badge bg-danger">
                                Rejected
                            </span>
                        `,
                    };
                    return map[data] ?? `
                        <span class="badge bg-light text-dark">
                            ${data}
                        </span>
                    `;
                }
            },
            { data: 'action', className: "text-center", orderable: false, searchable: false},
        ]
    });
    let tableCancellationApproval = $('#table-cancellation-approval').DataTable({
        processing: true,
        responsive: false,
        serverSide: false,
        scrollX: true,
        ajax: "{{ route('business-trip.cancellation-approval') }}",
        columns: [
            { data: 'DT_RowIndex', className: 'text-center', orderable: false, searchable: false},
            { data: 'no_document', className: 'text-center' },
            { data: 'nik', className:"text-center" },
            { data: 'employee_name', className:"text-center" },
            { data: 'area', className:"text-center"},
            { data: 'department', className:"text-center" },
            { data: 'position', className:"text-center" },
            { data: 'propose_date', className: 'text-center' },
            { data: 'type', className: 'text-center' },
            { data: 'date_and_day', className: 'text-center' },
            {
                data: 'reason_cancel',
                className: 'text-center',
                render: function(data){
                    const map = {
                        emergency:`
                            <strong> Kondisi Darurat atau Alasan Kesehatan </strong>
                        `,
                        company_decision:`
                            <strong> Perubahan Keputusan Perusahaan </strong>
                        `,
                        force_majeure:`
                            <strong> Force Majeure </strong>
                        `,
                        personal_reasons:`
                            <strong> Alasan Pribadi </strong>
                        `,
                        other:`
                            <strong> Lainnya </strong>
                        `,
                    };
                    let reasonText =
                        map[data.reason_cancel]
                        ??
                        data.reason_cancel;
                    return `
                        <div>
                            <div>
                                ${reasonText}
                            </div>
                            ${
                                data.reason_other
                                ?
                                `
                                <small class="text-muted d-block mt-1">
                                    ${data.reason_other}

                                </small>
                                `
                                :
                                ''
                            }
                        </div>
                    `;
                }
            },
            {
                data: 'total_cost_lost',
                className: 'text-center',
                render: function(data){
                    if(!data){
                        return 'IDR 0';
                    }
                    return 'IDR ' +
                        Number(data)
                        .toLocaleString('id-ID');
                }
            },
            {
                data: 'lost_costs_incurred',
                className: 'text-center',
                render: function(data){
                    let employee =
                        Number(
                            data.employee ?? 0
                        ).toLocaleString(
                            'id-ID'
                        );
                    let company =
                        Number(
                            data.company ?? 0
                        ).toLocaleString(
                            'id-ID'
                        );
                    return `
                        <div class="text-start">
                            <div>
                                <strong>
                                    Employee :
                                </strong>
                                IDR ${employee}
                            </div>
                            <div>
                                <strong>
                                    Company :
                                </strong>
                                IDR ${company}
                            </div>
                        </div>
                    `;
                }
            },
            {   data: 'status',
                className: 'text-center',
                render: function (data) {
                    const map = {
                        waiting: `
                            <span class="badge bg-warning text-white">
                                Waiting Approval
                            </span>
                        `,
                        revised: `
                            <span class="badge bg-info">
                                Revised
                            </span>
                        `,
                    };
                    return map[data] ?? `
                        <span class="badge bg-light text-white">
                            ${data}
                        </span>
                    `;
                }
            },
            { data: 'action', className: 'text-center', orderable: false, searchable: false}
        ]
    });
    let tableCancellationHistory = $('#table-cancellation-history').DataTable({
        processing: true,
        responsive: false,
        serverSide: false,
        scrollX: true,
        ajax: "{{ route('business-trip.cancellation-history') }}",
        columns: [
            { data: 'DT_RowIndex', className: 'text-center', orderable: false, searchable: false},
            { data: 'no_document', className: 'text-center' },
            { data: 'nik', className:"text-center" },
            { data: 'employee_name', className:"text-center" },
            { data: 'area', className:"text-center"},
            { data: 'department', className:"text-center" },
            { data: 'position', className:"text-center" },
            { data: 'propose_date', className: 'text-center' },
            { data: 'type', className: 'text-center' },
            { data: 'date_and_day', className: 'text-center' },
            {
                data: 'reason_cancel',
                className: 'text-center',
                render: function(data){
                    const map = {
                        emergency:`
                            <strong> Kondisi Darurat atau Alasan Kesehatan </strong>
                        `,
                        company_decision:`
                            <strong> Perubahan Keputusan Perusahaan </strong>
                        `,
                        force_majeure:`
                            <strong> Force Majeure </strong>
                        `,
                        personal_reasons:`
                            <strong> Alasan Pribadi </strong>
                        `,
                        other:`
                            <strong> Lainnya </strong>
                        `,
                    };
                    let reasonText =
                        map[data.reason_cancel]
                        ??
                        data.reason_cancel;
                    return `
                        <div>
                            <div>
                                ${reasonText}
                            </div>
                            ${
                                data.reason_other
                                ?
                                `
                                <small class="text-muted d-block mt-1">
                                    ${data.reason_other}

                                </small>
                                `
                                :
                                ''
                            }
                        </div>
                    `;
                }
            },
            {
                data: 'total_cost_lost',
                className: 'text-center',
                render: function(data){
                    if(!data){
                        return 'IDR 0';
                    }
                    return 'IDR ' +
                        Number(data)
                        .toLocaleString('id-ID');
                }
            },
            {
                data: 'lost_costs_incurred',
                className: 'text-center',
                render: function(data){
                    let employee =
                        Number(
                            data.employee ?? 0
                        ).toLocaleString(
                            'id-ID'
                        );
                    let company =
                        Number(
                            data.company ?? 0
                        ).toLocaleString(
                            'id-ID'
                        );
                    return `
                        <div class="text-start">
                            <div>
                                <strong>
                                    Employee :
                                </strong>
                                IDR ${employee}
                            </div>
                            <div>
                                <strong>
                                    Company :
                                </strong>
                                IDR ${company}
                            </div>
                        </div>
                    `;
                }
            },
            {   data: 'status',
                className: 'text-center',
                render: function (data) {
                    const map = {
                        approved: `
                            <span class="badge bg-success">
                                Approved
                            </span>
                        `,
                        rejected: `
                            <span class="badge bg-danger">
                                Rejected
                            </span>
                        `,
                    };
                    return map[data] ?? `
                        <span class="badge bg-light text-white">
                            ${data}
                        </span>
                    `;
                }
            },
            { data: 'action', className: 'text-center', orderable: false, searchable: false}
        ]
    });
    $(document).on('click', '.btn-detail-myCancellation', function () {
        let id = $(this).data('id');
        $.ajax({
            url: "{{ route('business-trip.my-cancellation-detail', ':id') }}"
                .replace(':id', id),
            type: 'GET',
            success: function(res) {
                // hide approval action
                console.log(res.approvals);
                $('#approval-cancellation-section').addClass('d-none');
                fillCancellationDetailModal(res);
                $('#cancellationDetailModal').modal('show');
            }
        });
    });
    $(document).on('click', '.btn-detail-cancellation', function () {
        let id = $(this).data('id');
        // console.log('clicked cancel');
        $.ajax({
            url: "{{ route('business-trip.cancellation-detail', ':id') }}"
                .replace(':id', id),
            type: 'GET',
            success: function(res) {
                console.log(res);
                // show approval action
                $('#approval-cancellation-section').removeClass('d-none');
                // simpan approval id
                $('#approval_cancel_id').val(res.approval_id);
                if (res.can_action) {
                    $('#approval-cancellation-section').show();
                } else {
                    $('#approval-cancellation-section').hide();
                }
                fillCancellationDetailModal(res);
                $('#cancellationDetailModal').modal('show');
            }
        });
    });
    $(document).on('click', '.btn-detail-cancellation-history', function () {
        let id = $(this).data('id');
        // console.log('clicked');
        $.ajax({
            url: "{{ route('business-trip.cancellation-detail', ':id') }}"
                .replace(':id', id),
            type: 'GET',
            success: function(res) {
                // show approval action
                $('#approval-cancellation-section').addClass('d-none');
                fillCancellationDetailModal(res);
                $('#cancellationDetailModal').modal('show');
            }
        });
    });

    $('#btn-cancel-approved').on('click', function () {
        processCancelApproval('approved');
    });
    // ================= REVISED =================
    $('#btn-cancel-revised').on('click', function () {
        processCancelApproval('revised');
    });
    // ================= REJECTED =================
    $('#btn-cancel-rejected').on('click', function () {
        processCancelApproval('rejected');
    });
    function processCancelApproval(action)
    {
        let approvalId = $('#approval_cancel_id').val();
        let title = '';
        let text = '';
        let icon = '';
        if (action === 'approved') {
            title = 'Approve Pengajuan?';
            text = 'Anda dapat memberikan catatan approval (opsional)';
            icon = 'success';
        }
        else if (action === 'revised') {
            title = 'Revisi Pengajuan?';
            text = 'Berikan alasan revisi';
            icon = 'warning';
        }
        else if (action === 'rejected') {
            title = 'Reject Pengajuan?';
            text = 'Berikan alasan penolakan';
            icon = 'error';
        }
        // $('#detailModal').modal('hide');
        Swal.fire({
            // target: document.getElementById('proposeDetailModal'),
            title: title,
            text: text,
            icon: icon,
            input: 'textarea',
            backdrop: true,
            allowOutsideClick: false,
            allowEscapeKey: false,
            showCancelButton: true,
            cancelButtonText: 'Batal',
            didOpen: () => {
                setTimeout(() => {
                    $('.swal2-textarea').focus();
                }, 100);
            },
            inputPlaceholder:
                action === 'approved'
                    ? 'Tambahkan catatan approval (opsional)'
                    : action === 'revised'
                        ? 'Masukkan alasan revisi...'
                        : 'Masukkan alasan penolakan...',
            inputAttributes: {
                autocapitalize: 'off'
            },
            showCancelButton: true,
            confirmButtonText:
                action === 'approved'
                    ? 'Ya, Approve'
                    : action === 'revised'
                        ? 'Ya, Revised'
                        : 'Ya, Reject',
            cancelButtonText: 'Batal',
            confirmButtonColor:
                action === 'approved'
                    ? '#0ab39c'
                    : action === 'revised'
                        ? '#f7b84b'
                        : '#f06548',
            preConfirm: (reason) => {
                // wajib isi untuk revised & rejected
                if (
                    (action === 'revised' ||
                    action === 'rejected') &&
                    !reason
                ) {
                    Swal.showValidationMessage(
                        'Alasan wajib diisi'
                    );
                    return false;
                }
                return reason;
            }
        }).then((result) => {
            if (!result.isConfirmed) {
                return;
            }
            $.ajax({
                url: "{{ route('business-trip.single-process-cancel') }}",
                type: "POST",
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    id: approvalId,
                    action: action,
                    reason: result.value
                },
                success: function(res) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: res.message,
                        timer: 1800,
                        showConfirmButton: false
                    });
                    $('#cancellationDetailModal').modal('hide');
                    tableCancellationApproval.ajax.reload();
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text:
                            xhr.responseJSON?.message ??
                            'Terjadi kesalahan'
                    });
                }
            });
        });
    }


    $(document).on('click','#btn-cancel',function(){
        let id = $(this).data('id');
        window.location.href = "{{ route('business-trip-cancellation.create', ':id') }}"
            .replace(':id', id);
    });
});
</script>
<script>
function loadApprovalCountWaiting()
{
    $.ajax({
        url: "{{ route('business-trip.waiting-pending-count') }}",
        type:'GET',
        success:function(res)
        {
            const businessTrip = res.business_trip ?? 0;
            const claim = res.claim ?? 0;
            const cancellation = res.cancellation ?? 0;
            $('#business-trip-count').text(businessTrip);
            $('#claim-count').text(claim);
            $('#cancel-count').text(cancellation);
            $('#business-trip-text').text(businessTrip > 0 ? 'Menunggu Approval' : 'Tidak Ada Request');
            $('#claim-text').text(claim > 0 ? 'Menunggu Review' : 'Tidak Ada Request');
            $('#cancel-text').text(cancellation > 0 ? 'Menunggu Approval' : 'Tidak Ada Request');
        },
        error:function(xhr){console.error( 'Failed Load Approval Count', xhr);}
    });
}
function formatCurrency(value){
    return 'IDR ' + new Intl.NumberFormat('id-ID').format(value ?? 0);
}
function fillProposeDetailModal(res)
{
    // ================= HEADER =================
    $('#detail_document_number').text(
        res.no_document ?? '-'
    );
    let tripBadge = '';
    if (res.trip_type === 'domestic') {
        tripBadge =
            `<span class="badge bg-primary">
                Domestic
            </span>`;
    }
    else {
        tripBadge =
            `<span class="badge bg-info">
                Overseas
            </span>`;
    }
    $('#detail_trip_type_badge').html(
        tripBadge
    );
    // ================= SUMMARY =================
    $('#detail_employee').html(`
        <div class="fw-semibold">
            ${res.employee?.fullname ?? '-'}
        </div>
        <small class="text-muted">
            ${res.level ?? '-'}
            •
            ${res.position ?? '-'}
        </small>
    `);
    $('#detail_date').html(`
        <div class="d-flex align-items-center justify-content-between">

            <div>
                <div class="fw-semibold">
                    ${res.start_date ?? '-'}
                </div>

                <small class="text-muted">
                    Berangkat
                </small>
            </div>

            <div class="px-2">
                <i class="ri-arrow-right-line text-primary fs-5"></i>
            </div>

            <div class="text-end">
                <div class="fw-semibold">
                    ${res.end_date ?? '-'}
                </div>

                <small class="text-muted">
                    Berakhir
                </small>
            </div>

        </div>

        <div class="mt-2 text-center">
            <span class="badge bg-primary-subtle text-primary">
                ${res.total_days ?? 0} Hari
            </span>
        </div>
    `);

    $('#detail_route').html(`
        <div class="d-flex align-items-center justify-content-between gap-3">

            <div>
                <div class="fw-semibold">
                    ${res.departure_from ?? '-'}
                </div>

                <small class="text-muted">
                    Berangkat
                </small>
            </div>

            <div class="px-2">
                <i class="ri-arrow-right-line text-primary fs-5"></i>
            </div>

            <div class="text-end">
                <div class="fw-semibold">
                    ${res.arrival_to ?? '-'}
                </div>

                <small class="text-muted">
                    Tujuan
                </small>
            </div>
        </div>
    `);
    let totalCost = 0;
    res.costs.forEach(item => {
        totalCost += Number(item.total_amount ?? 0);
    });

    const expenseMethodMap = {
        advance: 'Advance',
        reimburse: 'Reimburse',
        operation_cost: 'Operating Cost'
    };

    let expenseMethod =
        expenseMethodMap[res.expense_method]
        ?? res.expense_method
        ?? '-';

    let advanceHtml = '';

    if (
        res.expense_method === 'advance'
        &&
        res.advance_amount > 0
    ) {

        advanceHtml = `
            <small class="d-block text-muted">
                Advance Amount :
                IDR ${Number(res.advance_amount)
                    .toLocaleString('id-ID')}
            </small>
        `;

    }

    $('#detail_total_cost').html(`
        <div class="fw-bold text-primary fs-5">
            IDR ${Number(totalCost)
                .toLocaleString('id-ID')}
        </div>

        <span class="badge bg-info mt-1">
            ${expenseMethod}
        </span>

        ${
            res.expense_method === 'advance'
            ? `
                <div class="mt-1">
                    <span class="fw-semibold">
                        IDR ${Number(res.advance_amount)
                            .toLocaleString('id-ID')}
                    </span>
                </div>
            `
            : ''
        }
    `);

    $('#detail_purpose').text(
        res.purpose ?? '-'
    );

    // ================= COST =================

    let costHtml = '';

    if (res.costs.length > 0) {

        res.costs.forEach(item => {

            costHtml += `
                <tr>
                    <td class="text-capitalize">
                        ${item.category ?? '-'}
                    </td>

                    <td>
                        ${item.currency ?? 'IDR'}
                        ${Number(item.unit_amount ?? 0).toLocaleString()}
                    </td>

                    <td class="fw-semibold text-primary">
                        ${item.currency ?? 'IDR'}
                        ${Number(item.total_amount ?? 0).toLocaleString()}
                    </td>

                    <td>
                        ${item.notes ?? '-'}
                    </td>
                </tr>
            `;
        });

    } else {

        costHtml = `
            <tr>
                <td colspan="4"
                    class="text-center text-muted py-4">

                    Tidak ada estimasi biaya

                </td>
            </tr>
        `;
    }
    $('#detail-cost-wrapper').html(
        costHtml
    );

    // ================= TRANSPORT =================

    let transportHtml = `
        <div class="text-muted">
            Tidak ada data transportasi
        </div>
    `;

    let transport = res.transportations;

    if (transport) {

        transportHtml = `

            <div class="d-flex align-items-center justify-content-start mb-1 gap-1">

                <small class="text-muted">
                    Transport :
                </small>

                <div class="fw-semibold text-capitalize">
                    ${transport.transport_type ?? '-'}
                    ${transport.public_transport_type
                        ? ' • ' + transport.public_transport_type
                        : ''
                    }
                </div>

            </div>

            ${(transport.vehicle_number || transport.driver_name) ? `
                <div class="d-flex align-items-center justify-content-start mb-1 gap-1">

                    <small class="text-muted">
                        Vehicle / Driver
                    </small>

                    <div class="fw-semibold text-end">
                        ${transport.vehicle_number ?? '-'}
                        ${transport.driver_name
                            ? ' • ' + transport.driver_name
                            : ''
                        }
                    </div>

                </div>
            ` : ''}

            ${(transport.departure_date || transport.arrival_date) ? `
                <div class="d-flex align-items-center justify-content-start mb-1 gap-1">

                    <small class="text-muted">
                        Schedule :
                    </small>

                    <div class="fw-semibold small">

                        ${transport.departure_date ?? '-'}
                        ${transport.departure_time ?? ''}

                        <i class="ri-arrow-right-line mx-1"></i>

                        ${transport.arrival_date ?? '-'}
                        ${transport.arrival_time ?? ''}

                    </div>

                </div>
            ` : ''}

            ${transport.notes ? `
                <div class="d-flex align-items-start justify-content-between">

                    <small class="text-muted">
                        Notes
                    </small>

                    <div class="fw-semibold text-end ms-3">
                        ${transport.notes}
                    </div>

                </div>
            ` : ''}
        `;
    }

    $('#detail-transport-content').html(
        transportHtml
    );

    $('#detail-transport-content').html(
        transportHtml
    );

    // ================= HOTEL =================

    let hotel = res.hotels;
    let reservationBadge = '';

    if (hotel) {

        reservationBadge = `
            <span class="badge ${
                hotel.reservation_by_ga
                    ? 'bg-primary-subtle text-primary'
                    : 'bg-secondary-subtle text-secondary'
            }">

                ${
                    hotel.reservation_by_ga
                        ? 'Reservation by GA'
                        : 'Mandiri'
                }

            </span>
        `;
    }

    $('#hotel-reservation-badge').html(
        reservationBadge
    );
    let hotelHtml = `
        <div class="text-muted">
            Tidak ada hotel
        </div>
    `;

    if (hotel) {

        hotelHtml = `

            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                {{-- HOTEL --}}
                <div class="d-flex align-items-center gap-2">
                    <div>
                        <small class="text-muted d-block">
                            Hotel
                        </small>

                        <div class="fw-semibold">
                            ${hotel.hotel_name ?? '-'}
                        </div>
                    </div>

                </div>

                {{-- DATE --}}
                <div class="d-flex align-items-center gap-2">

                    <div>
                        <small class="text-muted d-block">
                            Check In
                        </small>

                        <div class="fw-semibold small">
                            ${hotel.check_in ?? '-'}
                        </div>
                    </div>

                    <i class="ri-arrow-right-line text-muted mt-3"></i>

                    <div>
                        <small class="text-muted d-block">
                            Check Out
                        </small>

                        <div class="fw-semibold small">
                            ${hotel.check_out ?? '-'}
                        </div>
                    </div>

                </div>

                {{-- DURATION --}}
                <div class="text-end">

                    <small class="text-muted text-center d-block">
                        Duration
                    </small>

                    <div class="fw-bold text-primary">
                        ${hotel.total_days ?? 0} Hari
                        /
                        ${hotel.total_nights ?? 0} Malam
                    </div>

                </div>

            </div>
        `;
    }

    $('#detail-hotel-content').html(
        hotelHtml
    );
    // ================= APPROVAL =================
    let approvalHtml = '';

    if (res.approvals.length > 0) {

        res.approvals.forEach(item => {

            let badge = '';

            if (item.status === 'approved') {

                badge = `
                    <span class="badge bg-success">
                        Approved
                    </span>
                `;

            } else if (item.status === 'rejected') {

                badge = `
                    <span class="badge bg-danger">
                        Rejected
                    </span>
                `;

            } else if (item.status === 'waiting') {

                badge = `
                    <span class="badge bg-warning">
                        Waiting
                    </span>
                `;

            } else if (item.status === 'revised') {

                badge = `
                    <span class="badge bg-info">
                        Revised
                    </span>
                `;

            } else {

                badge = `
                    <span class="badge bg-secondary">
                        Pending
                    </span>
                `;
            }

            approvalHtml += `

                <div class="border rounded-3 px-3 py-2 mb-2">

                    <div class="d-flex justify-content-between align-items-start">

                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-start align-items-center">
                                <div class="fw-semibold">
                                    ${item.approver?.fullname ?? '-'}
                                </div>
                                <span class="badge bg-light text-dark">
                                    ${item.position ?? '-'}
                                </span>
                            </div>

                            <small class="text-muted">
                                Approval ${item.level}
                            </small>

                            ${
                                item.logs?.length
                                ? `
                                    <div class="mt-1">

                                        ${item.logs.map(log => {

                                            let logBadge = '';

                                            if (log.status === 'approved') {
                                                logBadge =
                                                    `<span class="text-success">
                                                        Approved
                                                    </span>`;
                                            }
                                            else if (log.status === 'rejected') {
                                                logBadge =
                                                    `<span class="text-danger">
                                                        Rejected
                                                    </span>`;
                                            }
                                            else if (log.status === 'revised') {
                                                logBadge =
                                                    `<span class="text-warning">
                                                        Revised
                                                    </span>`;
                                            }
                                            else {
                                                logBadge =
                                                    `<span class="text-secondary">
                                                        ${log.status}
                                                    </span>`;
                                            }

                                            return `
                                                <div class="small text-muted d-flex flex-wrap align-items-center gap-1">

                                                    <span>
                                                        <i class="ri-time-line me-1"></i>
                                                        ${log.action_at ?? '-'}
                                                    </span>

                                                    <span>•</span>

                                                    <span class="fw-semibold">
                                                        ${logBadge}
                                                    </span>

                                                    ${
                                                        log.reason
                                                        ? `
                                                            <>
                                                                <span>•</span>

                                                                <span>
                                                                    ${log.reason}
                                                                </span>
                                                            </>
                                                        `
                                                        : ''
                                                    }

                                                </div>
                                            `;
                                        }).join('')}

                                    </div>
                                `
                                : ''
                            }

                        </div>

                        <div>
                            ${badge}
                        </div>

                    </div>

                </div>
            `;
        });
    } else {
        approvalHtml = `
            <div class="text-muted">
                Tidak ada approval
            </div>
        `;
    }
    $('#approval-wrapper').html(approvalHtml);
    $('#btn-cancel').attr('data-id',res.id);

    // ================= SHOW MODAL =================

    $('#proposeDetailModal').modal('show');
}

function fillReportDetailModal(res)
{
    // ================= HEADER =================
    $('#detail_report_document_number').text(
        res.no_document ?? '-'
    );
    $('#detail_report_trip_type_badge').html(
        res.trip_type === 'domestic'
        ?
        `<span class="badge bg-primary">
            Domestic
        </span>`
        :
        `<span class="badge bg-success">
            Overseas
        </span>`
    );
    // ================= SUMMARY =================
    $('#detail_report_employee').text(
        res.employee_name ?? '-'
    );
    $('#detail_report_date').html(`
        <div class="d-flex align-items-center justify-content-between">

            <div>
                <div class="fw-semibold">
                    ${res.start_date ?? '-'}
                </div>

                <small class="text-muted">
                    Berangkat
                </small>
            </div>

            <div class="px-2">
                <i class="ri-arrow-right-line text-primary fs-5"></i>
            </div>

            <div class="text-end">
                <div class="fw-semibold">
                    ${res.end_date ?? '-'}
                </div>

                <small class="text-muted">
                    Berakhir
                </small>
            </div>

        </div>

        <div class="mt-2 text-center">
            <span class="badge bg-primary-subtle text-primary">
                ${res.total_days ?? 0} Hari
            </span>
        </div>
    `);
    $('#detail_report_route').text(
        res.arrival_to ?? '-'
    );
    $('#detail_report_total_cost').html(
        'IDR ' + new Intl.NumberFormat('id-ID').format(
            res.total_cost ?? 0
        )
    );
    $('#detail_report_purpose').text(
        res.purpose ?? '-'
    );
    // ================= MEAL =================
    $('#detail-meal-wrapper').html('');
    (res.meals ?? []).forEach(item=>{
        let attachmentHtml='-';
        if(item.attachments?.length){
            attachmentHtml = item.attachments.map(file=>{
                return `
                    <a href="/${file.file_path}"
                    target="_blank">
                    ${file.file_name}
                    </a>
                `;
            }).join('<br>');
        }
        $('#detail-meal-wrapper').append(`
            <tr>
                <td>
                    ${item.expense_date}
                </td>
                <td> Meal </td>
                <td> IDR ${new Intl.NumberFormat('id-ID').format(
                        item.unit_amount ?? 0
                    )}
                </td>
                <td>
                    ${attachmentHtml}
                </td>
            </tr>
        `);
    });
    // ================= EXPENSE =================
    $('#detail-expense-wrapper').html('');
    (res.expenses ?? []).forEach(item=>{
        let attachmentHtml='-';
        if(item.attachments?.length){
            attachmentHtml = item.attachments.map(file=>{
                return `
                    <a
                    href="/${file.file_path}"
                    target="_blank">
                    ${file.file_name}
                    </a>
                `;
            }).join('<br>');
        }

        $('#detail-expense-wrapper').append(`
            <tr>
                <td>
                    ${item.category ?? '-'}
                </td>
                <td> IDR ${new Intl.NumberFormat('id-ID').format(
                        item.unit_amount ?? 0
                    )}
                </td>
                <td> ${item.qty ?? 0} </td>
                <td> IDR ${new Intl.NumberFormat('id-ID').format(
                        item.unit_total ?? 0
                    )}
                </td>
                <td> ${item.notes ?? '-'} </td>
                <td> ${attachmentHtml} </td>
            </tr>
        `);
    });
    // ================= APPROVAL =================
    $('#approval-report-wrapper').html('');
    (res.approvals ?? []).forEach(item=>{
        let badge='';
        // console.log(res.approvals);
        if(item.status==='approved')
        {
            badge=
            `<span class="badge bg-success">
                Approved
            </span>`;
        }
        else if(item.status==='rejected')
        {
            badge=
            `<span class="badge bg-danger">
                Rejected
            </span>`;
        }
        else if(item.status==='revised')
        {
            badge=
            `<span class="badge bg-warning">
                Revised
            </span>`;
        }
        else if(item.status==='waiting')
        {
            badge=
            `<span class="badge bg-info">
                Waiting
            </span>`;
        }
        else
        {
            badge=
            `<span class="badge bg-secondary">
                Pending
            </span>`;
        }

        $('#approval-report-wrapper').append(`
            <div class="border rounded-3 px-3 py-2 mb-2">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-start align-items-center">
                                <div class="fw-semibold">
                                    ${item.approver?.fullname ?? '-'}
                                </div>
                                <span class="badge bg-light text-dark">
                                    ${item.position ?? '-'}
                                </span>
                            </div>
                        <small class="text-muted">
                            Approval ${item.level}
                        </small>
                        ${item.logs?.length ?`
                            <div class="mt-2">
                                ${item.logs.map(log=>{
                                    let logBadge='';
                                    if(log.status==='approved'){
                                        logBadge=
                                        `
                                            <span class="text-success fw-semibold">
                                                Approved
                                            </span>
                                        `;
                                    }
                                    else if(log.status==='rejected'){
                                        logBadge=
                                        `
                                            <span class="text-danger fw-semibold">
                                                Rejected
                                            </span>
                                        `;
                                    }
                                    else if(log.status==='revised'){
                                        logBadge=
                                        `
                                            <span class="text-warning fw-semibold">
                                                Revised
                                            </span>
                                        `;
                                    }
                                    else{
                                        logBadge=
                                        `
                                            <span class="text-secondary fw-semibold">

                                                ${log.status}

                                            </span>
                                        `;
                                    }
                                    return `
                                        <div class="small text-muted mb-1">
                                            <div class="d-flex flex-wrap align-items-center gap-1">
                                                <span>
                                                    <i class="ri-time-line me-1"></i>
                                                    ${log.action_at ?? '-'}
                                                </span>
                                                <span>•</span>
                                                ${logBadge}
                                                ${
                                                    log.reason
                                                    ?
                                                    `
                                                        <span>•</span>
                                                        <span>
                                                            ${log.reason}

                                                        </span>
                                                    `
                                                    :
                                                    ''
                                                }
                                            </div>
                                        </div>
                                    `;
                                }).join('')}
                            </div>
                            `
                            :
                            ''
                        }
                    </div>
                    <div>
                        ${badge}
                    </div>
                </div>
            </div>
        `);
    });
}
function fillCancellationDetailModal(res)
{
    // ================= HEADER =================
    $('#detail_trip_document_number').text(
        res.no_document ?? '-'
    );
    $('#detail_cancel_type_badge').html(
        res.trip_type === 'domestic'
        ?
        `<span class="badge bg-primary">
            Domestic
        </span>`
        :
        `<span class="badge bg-success">
            Overseas
        </span>`
    );
    // ================= SUMMARY =================
    $('#detail_trip_employee').html(`
        <div class="fw-semibold">
            ${res.employee_name ?? '-'}
        </div>
        <small class="text-muted">
            ${res.position ?? '-'}
        </small>
    `);
    $('#detail_trip_date').html(`
        <div class="d-flex align-items-center justify-content-between">

            <div>
                <div class="fw-semibold">
                    ${res.start_date ?? '-'}
                </div>

                <small class="text-muted">
                    Berangkat
                </small>
            </div>

            <div class="px-2">
                <i class="ri-arrow-right-line text-primary fs-5"></i>
            </div>

            <div class="text-end">
                <div class="fw-semibold">
                    ${res.end_date ?? '-'}
                </div>

                <small class="text-muted">
                    Berakhir
                </small>
            </div>

        </div>

        <div class="mt-2 text-center">
            <span class="badge bg-primary-subtle text-primary">
                ${res.total_days ?? 0} Hari
            </span>
        </div>
    `);
    $('#detail_trip_route').text(
        res.arrival_to ?? '-'
    );
    $('#detail_cancel_loss_amount').html(
        'IDR ' + new Intl.NumberFormat('id-ID').format(
            res.total_cost ?? 0
        )
    );
    $('#detail_company_covered_amount').text(
        formatCurrency(res.company_amount)
    );

    $('#detail_employee_covered_amount').text(
        formatCurrency(res.employee_amount)
    );

    $('#detail_cancel_loss_amount').text(
        formatCurrency(res.total_loss_amount)
    );
    const reasonMap = {
        emergency: 'Kondisi Darurat atau Alasan Kesehatan',
        company_decision: 'Perubahan Keputusan Perusahaan',
        force_majeure: 'Force Majeure',
        personal_reasons: 'Alasan Pribadi',
        other: 'Lainnya'
    };

    let reasonHtml = `
        <div class="fw-semibold">
            ${reasonMap[res.reason_cancel] ?? res.reason_cancel ?? '-'}
        </div>
    `;

    if (res.reason_other) {
        reasonHtml += `
            <div class="mt-2 text-muted">
                ${res.reason_other}
            </div>
        `;
    }

    $('#detail_reason_cancel').html(reasonHtml);

    $('#detail-list-loss').empty();

    (res.items || []).forEach(item => {

        $('#detail-list-loss').append(`
            <tr>
                <td>
                    ${item.category ?? '-'}
                </td>

                <td class="text-start">
                    IDR ${new Intl.NumberFormat('id-ID').format(
                        item.unit_amount ?? 0
                    )}
                </td>

                <td class="text-start">
                    ${item.qty ?? 0}
                </td>

                <td class="text-start">
                    IDR ${new Intl.NumberFormat('id-ID').format(
                        item.unit_total ?? 0
                    )}
                </td>

                <td>
                    ${item.notes ?? '-'}
                </td>
            </tr>
        `);

    });
    // ================= APPROVAL =================
    $('#approval-cancel-wrapper').html('');
    (res.approvals ?? []).forEach(item=>{
        let badge='';
        // console.log(res.approvals);
        if(item.status==='approved')
        {
            badge=
            `<span class="badge bg-success">
                Approved
            </span>`;
        }
        else if(item.status==='rejected')
        {
            badge=
            `<span class="badge bg-danger">
                Rejected
            </span>`;
        }
        else if(item.status==='revised')
        {
            badge=
            `<span class="badge bg-warning">
                Revised
            </span>`;
        }
        else if(item.status==='waiting')
        {
            badge=
            `<span class="badge bg-info">
                Waiting
            </span>`;
        }
        else
        {
            badge=
            `<span class="badge bg-secondary">
                Pending
            </span>`;
        }

        $('#approval-cancel-wrapper').append(`
            <div class="border rounded-3 px-3 py-2 mb-2">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-start align-items-center">
                                <div class="fw-semibold">
                                    ${item.approver?.fullname ?? '-'}
                                </div>
                                <span class="badge bg-light text-dark">
                                    ${item.position ?? '-'}
                                </span>
                            </div>
                        <small class="text-muted">
                            Approval ${item.level}
                        </small>
                        ${item.logs?.length ?`
                            <div class="mt-2">
                                ${item.logs.map(log=>{
                                    let logBadge='';
                                    if(log.status==='approved'){
                                        logBadge=
                                        `
                                            <span class="text-success fw-semibold">
                                                Approved
                                            </span>
                                        `;
                                    }
                                    else if(log.status==='rejected'){
                                        logBadge=
                                        `
                                            <span class="text-danger fw-semibold">
                                                Rejected
                                            </span>
                                        `;
                                    }
                                    else if(log.status==='revised'){
                                        logBadge=
                                        `
                                            <span class="text-warning fw-semibold">
                                                Revised
                                            </span>
                                        `;
                                    }
                                    else{
                                        logBadge=
                                        `
                                            <span class="text-secondary fw-semibold">

                                                ${log.status}

                                            </span>
                                        `;
                                    }
                                    return `
                                        <div class="small text-muted mb-1">
                                            <div class="d-flex flex-wrap align-items-center gap-1">
                                                <span>
                                                    <i class="ri-time-line me-1"></i>
                                                    ${log.action_at ?? '-'}
                                                </span>
                                                <span>•</span>
                                                ${logBadge}
                                                ${
                                                    log.reason
                                                    ?
                                                    `
                                                        <span>•</span>
                                                        <span>
                                                            ${log.reason}

                                                        </span>
                                                    `
                                                    :
                                                    ''
                                                }
                                            </div>
                                        </div>
                                    `;
                                }).join('')}
                            </div>
                            `
                            :
                            ''
                        }
                    </div>
                    <div>
                        ${badge}
                    </div>
                </div>
            </div>
        `);
    });
}
</script>
@endsection
