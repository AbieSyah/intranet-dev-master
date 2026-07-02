{{-- @extends('layouts.master') --}}
@extends(Auth::user()->can('emp.menu') ? 'layouts.general' : 'layouts.master')

@section('link')
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css"/>
   {{-- checkbox styling --}}
   <style>
      .catalog-card {cursor: pointer;}
      .catalog-card .card {transition: 0.2s ease-in-out;border: 2px solid #e5e5e5;}
      .catalog-card.active .card {border: 2px solid #0d6efd;background-color: #eef4ff;}
   </style>
   {{-- end checkbox styling --}}
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

    {{-- ================= CONTENT ================= --}}
    <div class="row">
        <div class="col-12">
            @if (!Auth::user()->can('emp.menu'))
                <div class="mb-3">
                    @include('partials.navbar2')
                </div>
            @endif
            <form
                id="businessTripForm"
                action="{{ isset($businessTrip)
                    ? route('business-trip.propose-update', encrypt($businessTrip->id))
                    : route('business-trip.propose-store')
                }}"
                method="POST"
            >
                @csrf
                @if(isset($businessTrip))
                    @method('PUT')
                @endif
                {{-- ================= HEADER ================= --}}
                <div class="card mb-3">
                    <div class="card-header border-0 bg-light">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm me-3">
                                    <span class="avatar-title bg-primary-subtle text-primary rounded-3 fs-3 shadow-sm">
                                        <i class="ri-briefcase-line text-white"></i>
                                    </span>
                                </div>
                                <div>
                                    <h4 class="mb-1 fw-semibold text-dark">
                                        Formulir Pengajuan Perjalanan Dinas
                                        {{-- @if(isset($businessTrip))
                                            {{ $businessTrip->id }}
                                        @endif --}}
                                    </h4>
                                    <small class="text-muted">
                                        Pengajuan business trip karyawan
                                    </small>
                                </div>
                            </div>

                            <a href="{{ route('business-trip.profile-index') }}"
                            class="btn btn-primary btn-label waves-effect waves-light shadow-sm">
                                <i class="ri-arrow-left-line label-icon align-middle fs-16 me-2"></i>
                                Kembali
                            </a>
                        </div>
                    </div>
                    <div class="card-body">

                        <div class="row g-3">

                            <div class="col-md-12">
                                <label class="form-label">
                                    Nomor Document
                                </label>
                                <input type="text" id="document_number" class="form-control"
                                value="{{ old('no_document', $businessTrip->no_document ?? '') }}"
                                readonly>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">
                                    Tanggal Pengajuan
                                </label>
                                <input type="date" class="form-control" name="propose_date" value="{{ now()->format('Y-m-d') }}" readonly>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">
                                    Tipe Perjalanan Dinas
                                </label>
                                <select class="form-select" id="trip_type" name="trip_type">
                                    <option value="domestic" {{ old('trip_type', $businessTrip->trip_type ?? '') == 'domestic' ? 'selected' : '' }}>
                                        Domestic
                                    </option>
                                    <option value="overseas" {{ old('trip_type', $businessTrip->trip_type ?? '') == 'overseas' ? 'selected' : '' }}>
                                        Overseas
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">
                                    Metode Pengeluaran
                                </label>
                                <select class="form-select" id="expense_method" name="expense_method">
                                    <option value="reimbursement" {{ old('expense_method', $businessTrip->expense_method ?? '') == 'reimbursement' ? 'selected' : '' }}>
                                        Reimbursement
                                    </option>
                                    <option value="advance" {{ old('expense_method', $businessTrip->expense_method ?? '') == 'advance' ? 'selected' : '' }}>
                                        Advance / Kasbon
                                    </option>
                                    <option value="operating_cost" {{ old('expense_method', $businessTrip->expense_method ?? '') == 'operating_cost' ? 'selected' : '' }}>
                                        Operating Cost
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">
                                    Membutuhkan Hotel?
                                </label>
                                <select class="form-select"
                                        id="need_hotel"
                                        name="need_hotel">
                                    <option value="0" {{ old('need_hotel', $businessTrip->need_hotel ?? '0') == '0' ? 'selected' : '' }}>Tidak</option>
                                    <option value="1" {{ old('need_hotel', $businessTrip->need_hotel ?? '0') == '1' ? 'selected' : '' }}>Ya</option>

                                </select>
                            </div>

                        </div>

                    </div>
                </div>

                {{-- ================= BUSINESS TRIP DETAIL ================= --}}
                <div class="card mb-3">

                    <div class="card-header">
                        <h5 class="mb-0">
                            Detail Perjalanan Dinas
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label"> Tanggal Berangkat </label>
                                <div class="input-group">
                                    <input type="text" id="start_date" name="start_date" class="form-control bulan start_date" placeholder="Pilih Tanggal Berakhir" value="{{ old('start_date', $businessTrip->start_date ?? '') }}">
                                    <span class="input-group-text"><i class="ri-calendar-event-line"></i></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label"> Tanggal Kembali </label>
                                <div class="input-group">
                                    <input type="text" id="end_date" name="end_date" class="form-control bulan end_date" placeholder="Pilih Tanggal Berakhir" value="{{ old('end_date', $businessTrip->end_date ?? '') }}">
                                    <span class="input-group-text"><i class="ri-calendar-event-line"></i></span>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">
                                    Jam Keberangkatan
                                </label>
                                <div class="input-group">
                                    <input type="time"
                                        id="departure_time"
                                        name="departure_time"
                                        class="form-control timepicker"
                                        placeholder="pilih jam Keberangkatan"
                                        value="{{ old('departure_time', $businessTrip->departure_time ?? '') }}">
                                    <span class="input-group-text">
                                        <i class="ri-time-line"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">
                                    Perkiraan Jam Tiba
                                </label>
                                <div class="input-group">
                                    <input type="time"
                                        id="arrival_time"
                                        name="arrival_time"
                                        class="form-control timepicker"
                                        placeholder="pilih perkiraan jam tiba"
                                        value="{{ old('arrival_time', $businessTrip->arrival_time ?? '') }}">
                                    <span class="input-group-text">
                                        <i class="ri-time-line"></i>
                                    </span>
                                </div>
                            </div>
                            <!-- TOTAL HARI -->
                            <div class="col-md-2">
                                <label class="form-label">
                                    Total Hari
                                </label>
                                <input type="text"
                                    id="total_days"
                                    name="total_days"
                                    class="form-control bg-light"
                                    placeholder="Auto"
                                    readonly
                                    value="{{ old('total_days', $businessTrip->total_days ?? '') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">
                                    Berangkat Dari
                                </label>
                                <select class="form-select" id="departure_from" name="departure_from">
                                    <option value="house" {{ old('departure_from', $businessTrip->departure_from ?? '') == 'house' ? 'selected' : '' }}>
                                        Rumah
                                    </option>
                                    <option value="company" {{ old('departure_from', $businessTrip->departure_from ?? '') == 'company' ? 'selected' : '' }}>
                                        PT. HPI
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">
                                    Tujuan
                                </label>
                                <input type="text"
                                       id="arrival_to"
                                       name="arrival_to"
                                       class="form-control"
                                       placeholder="Perusahaan atau tempat tujuan"
                                       value="{{ old('arrival_to', $businessTrip->arrival_to ?? '') }}">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">
                                    Keperluan Perjalanan Dinas
                                </label>
                                <textarea class="form-control"
                                          rows="4"
                                          id="purpose"
                                          name="purpose"
                                          placeholder="Jelaskan tujuan perjalanan dinas">{{ old('purpose', $businessTrip->purpose ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- ================= ADVANCE ================= --}}
                <div class="card mb-3 d-none" id="advance-section">

                    <div class="card-header">
                        <h5 class="mb-0">
                            Advance / Kasbon
                        </h5>
                    </div>

                    <div class="card-body">

                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label">
                                    Currency
                                </label>

                                <select class="form-select"
                                        name="advance_currency">
                                    <option value="IDR" {{ old('advance_currency', $businessTrip->advance_currency ?? 'IDR') == 'IDR' ? 'selected' : '' }}>IDR</option>
                                    {{-- <option value="USD">USD</option> --}}

                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">
                                    Jumlah Kasbon
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        Rp
                                    </span>
                                    <input type="text"
                                        class="form-control currency-format"
                                        id="advance_amount"
                                        name="advance_amount"
                                        placeholder="0"
                                        value="{{ old(
                                            'advance_amount',
                                            isset($businessTrip->advance_amount)
                                                ? number_format((float) $businessTrip->advance_amount, 0, ',', '.')
                                                : ''
                                        ) }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

               {{-- ================= BUSINESS TRIP ALLOWANCE ================= --}}
                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h5 class="mb-0">
                                Estimasi Biaya Perjalanan Dinas
                            </h5>

                            <small class="text-muted">
                                Daily allowance dihitung otomatis berdasarkan level karyawan
                            </small>
                        </div>
                    </div>

                    <div class="card-body">

                        {{-- AUTO GENERATED --}}
                        <div class="mb-4">

                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar-xs me-2">
                                    <span class="avatar-title bg-success-subtle text-success rounded fs-16">
                                        <i class="ri-settings-3-line"></i>
                                    </span>
                                </div>
                                <div>
                                    <h6 class="mb-0">
                                        Uang saku Harian dan Laundry
                                    </h6>

                                    <small class="text-muted">
                                        Dibuat otomatis berdasarkan master allowance
                                    </small>
                                </div>
                            </div>

                            <div class="table-responsive">

                                <table class="table table-bordered align-middle">

                                    <thead class="table-light">
                                        <tr>
                                            <th>Kategori</th>
                                            <th>Trip Type</th>
                                            <th>Currency</th>
                                            <th>Nominal / Hari</th>
                                            <th>Total Hari</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>

                                    <tbody id="allowance-wrapper">
                                    </tbody>

                                </table>

                            </div>

                        </div>
                        {{-- ========================================== MANUAL INPUT ========================================== --}}
                        <div>
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-xs me-2">
                                        <span class="avatar-title bg-warning-subtle text-warning rounded fs-16">
                                            <i class="ri-edit-2-line"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 ">
                                            Biaya
                                        </h6>
                                        <small class="text-muted">
                                            Transportasi, hotel, dan biaya lainnya
                                        </small>
                                    </div>
                                </div>
                                <!-- RIGHT -->
                                <div>
                                    <button type="button"
                                            class="btn btn-primary btn-label waves-effect waves-light shadow-sm"
                                            id="btn-add-expense">
                                        <i class="ri-add-line label-icon align-middle fs-16 me-2"></i>
                                        Tambah Biaya
                                    </button>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Kategori</th>
                                            <th>Keterangan</th>
                                            <th>Currency</th>
                                            <th>Nominal/Hari</th>
                                            <th>Total Hari/Malam</th>
                                            <th>Jumlah Total</th>
                                            <th width="10%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="manual-expense-wrapper">

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <div class="alert alert-info mb-0">
                                    <div class="fw-semibold mb-1">
                                        Informasi
                                    </div>
                                    <ul class="mb-0 ps-3">
                                        <li>Claim Uang Makan dilakukan saat report perjalanan dinas</li>
                                        <li>Uang Laundry hanya berlaku untuk overseas ≥ 7 hari</li>
                                        <li>Maksimal Uang Laundry adalah IDR 200.000 per day</li>
                                        <li>Transport, Hotel dan kebutuhan pengeluaran lain yang perlu diajukan diinput manual</li>
                                        <li>Untuk Hotel Harinya dihitung Per Malam</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                                <small class="text-muted d-block">
                                    Total Estimasi
                                </small>
                                <h3 class="fw-bold text-primary mb-0"
                                    id="grand-total">

                                    IDR 0
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- ================= TRANSPORTATION ================= --}}
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0">
                            Transportasi Perjalanan Dinas
                        </h5>
                        <small class="text-muted">
                            Pilih jenis transportasi yang digunakan
                        </small>
                    </div>
                    <div class="card-body">
                        {{-- ================= MAIN TYPE ================= --}}
                        <div class="mb-4">

                            <label class="form-label fw-semibold">
                                Jenis Transportasi
                            </label>

                            <div class="row g-3">

                                <div class="col-md-4">
                                    <div class="form-check card-radio">
                                        <input class="form-check-input transportation-main"
                                            type="radio"
                                            name="transport_type"
                                            id="transport_private"
                                            value="private"
                                            {{ old('transport_type', $businessTrip->transportations->transport_type ?? '') == 'private' ? 'checked' : '' }}>

                                        <label class="form-check-label w-100"
                                            for="transport_private">

                                            <span class="d-block fw-semibold">
                                                Kendaraan Pribadi
                                            </span>

                                            <small class="text-muted">
                                                Menggunakan kendaraan pribadi
                                            </small>

                                        </label>

                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-check card-radio">

                                        <input class="form-check-input transportation-main"
                                            type="radio"
                                            name="transport_type"
                                            id="transport_company"
                                            value="company_car"
                                            {{ old('transport_type', $businessTrip->transportations->transport_type ?? '') == 'company_car' ? 'checked' : '' }}>

                                        <label class="form-check-label w-100"
                                            for="transport_company">

                                            <span class="d-block fw-semibold">
                                                Kendaraan Operasional
                                            </span>

                                            <small class="text-muted">
                                                Mobil operasional perusahaan
                                            </small>

                                        </label>

                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-check card-radio">

                                        <input class="form-check-input transportation-main"
                                            type="radio"
                                            name="transport_type"
                                            id="transport_public"
                                            value="public_transport"
                                            {{ old('transport_type', $businessTrip->transportations->transport_type ?? '') == 'public_transport' ? 'checked' : '' }}>

                                        <label class="form-check-label w-100"
                                            for="transport_public">

                                            <span class="d-block fw-semibold">
                                                Transportasi Umum
                                            </span>

                                            <small class="text-muted">
                                                Taxi, plane, train, dan lainnya
                                            </small>

                                        </label>

                                    </div>
                                </div>

                            </div>

                        </div>

                        {{-- ================= COMPANY CAR ================= --}}
                        <div id="company-car-section"
                            class="d-none">

                            <div class="row g-3">

                                <div class="col-md-6">

                                    <label class="form-label">
                                        Nomor Polisi
                                    </label>

                                    <input type="text"
                                        class="form-control"
                                        id="vehicle_number"
                                        name="vehicle_number"
                                        placeholder="Contoh : L 1234 ABC"
                                        value="{{ old('vehicle_number', $businessTrip->transportations->vehicle_number ?? '') }}">

                                </div>

                                <div class="col-md-6">

                                    <label class="form-label">
                                        Nama Driver
                                    </label>

                                    <input type="text"
                                        class="form-control"
                                        id="driver_name"
                                        name="driver_name"
                                        placeholder="Masukkan nama driver"
                                        value="{{ old('driver_name', $businessTrip->transportations->driver_name ?? '') }}">

                                </div>

                            </div>

                        </div>

                        {{-- ================= PUBLIC TRANSPORT ================= --}}
                        <div id="public-transport-section"
                            class="d-none">

                            <div class="mb-3">

                                <label class="form-label">
                                    Jenis Transportasi Umum
                                </label>

                                <select class="form-select"
                                        id="public_transport_type"
                                        name="public_transport_type">

                                    <option value="" {{ old('public_transport_type', $businessTrip->transportations->public_transport_type ?? '') == '' ? 'selected' : '' }}>
                                        Pilih
                                    </option>

                                    <option value="taxi" {{ old('public_transport_type', $businessTrip->transportations->public_transport_type ?? '') == 'taxi' ? 'selected' : '' }}>
                                        Taxi
                                    </option>

                                    <option value="plane" {{ old('public_transport_type', $businessTrip->transportations->public_transport_type ?? '') == 'plane' ? 'selected' : '' }}>
                                        Plane
                                    </option>

                                    <option value="train" {{ old('public_transport_type', $businessTrip->transportations->public_transport_type ?? '') == 'train' ? 'selected' : '' }}>
                                        Train
                                    </option>

                                    <option value="other" {{ old('public_transport_type', $businessTrip->transportations->public_transport_type ?? '') == 'other' ? 'selected' : '' }}>
                                        Other
                                    </option>

                                </select>

                            </div>

                            {{-- ================= PLANE / TRAIN ================= --}}
                            <div id="schedule-section"
                                class="d-none">

                                <div class="row g-3">

                                    <div class="col-md-4">
                                        <label class="form-label"> Tanggal Keberangkatan </label>
                                        <div class="input-group">
                                            <input type="text" id="transport_start_date" name="transport_start_date" class="form-control bulan start_date" placeholder="Pilih Tanggal Berakhir" value="{{ old('transport_start_date', $businessTrip->transportations->departure_date ?? '') }}">
                                            <span class="input-group-text"><i class="ri-calendar-event-line"></i></span>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">
                                            Perkiraan Jam Keberangkatan
                                        </label>
                                        <div class="input-group">
                                            <input type="time"
                                                id="transport_departure_time"
                                                name="transport_departure_time"
                                                class="form-control timepicker"
                                                placeholder="pilih perkiraan jam tiba"
                                                value="{{ old('transport_departure_time', $businessTrip->transportations->departure_time ?? '') }}">
                                            <span class="input-group-text">
                                                <i class="ri-time-line"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label"> Tanggal Kedatangan </label>
                                        <div class="input-group">
                                            <input type="text" id="transport_end_date" name="transport_end_date" class="form-control bulan end_date" placeholder="Pilih Tanggal Berakhir" value="{{ old('transport_end_date', $businessTrip->transportations->arrival_date ?? '') }}">
                                            <span class="input-group-text"><i class="ri-calendar-event-line"></i></span>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">
                                            Perkiraan Jam Tiba
                                        </label>
                                        <div class="input-group">
                                            <input type="time"
                                                id="transport_arrival_time"
                                                name="transport_arrival_time"
                                                class="form-control timepicker"
                                                placeholder="pilih perkiraan jam tiba"
                                                value="{{ old('transport_arrival_time', $businessTrip->transportations->arrival_time ?? '') }}">
                                            <span class="input-group-text">
                                                <i class="ri-time-line"></i>
                                            </span>
                                        </div>
                                    </div>

                                </div>

                            </div>

                            {{-- ================= OTHER ================= --}}
                            <div id="other-transport-section"
                                class="d-none">

                                <label class="form-label">
                                    Keterangan Transportasi
                                </label>

                                <textarea class="form-control"
                                        rows="3"
                                        name="transport_notes"
                                        placeholder="Jelaskan jenis transportasi">{{ old('transport_notes', $businessTrip->transportations->notes ?? '') }}</textarea>

                            </div>

                        </div>

                    </div>

                </div>

                {{-- ================= HOTEL ================= --}}
                <div class="card mb-3 d-none" id="hotel-section">
                    <div class="card-header">
                        <h5 class="mb-0">
                            Informasi Hotel
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">
                                    Reservasi Oleh GA?
                                </label>
                                <select class="form-select"
                                        id="reservation_by_ga"
                                        name="reservation_by_ga">
                                    <option value="0" {{ old('reservation_by_ga', $businessTrip->hotels->reservation_by_ga ?? '0') == '0' ? 'selected' : '' }}>Tidak</option>
                                    <option value="1" {{ old('reservation_by_ga', $businessTrip->hotels->reservation_by_ga ?? '0') == '1' ? 'selected' : '' }}>Ya</option>
                                </select>
                            </div>
                            <div id="hotel-input-wrapper">

                                <div class="row g-3">

                                    <div class="col-md-6">
                                        <label class="form-label">
                                            Nama Hotel
                                        </label>

                                        <input type="text"
                                            class="form-control"
                                            id="hotel_name"
                                            name="hotel_name"
                                            placeholder="Contoh : Hotel Asylum Jakarta "
                                            value="{{ old('hotel_name', $businessTrip->hotels->hotel_name ?? '') }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">
                                            Jumlah Hari
                                        </label>

                                        <input type="number"
                                            class="form-control"
                                            id="Days_checkIn"
                                            name="Days_checkIn"
                                            value="{{ old('Days_checkIn', $businessTrip->hotels->total_days ?? '') }}">
                                    </div>

                                    <div class="col-md-2">
                                        <label class="form-label">
                                            Jumlah Malam
                                        </label>

                                        <input type="number"
                                            class="form-control"
                                            id="Night_checkIn"
                                            name="Night_checkIn"
                                            value="{{ old('Night_checkIn', $businessTrip->hotels->total_nights ?? '') }}">
                                    </div>

                                    <div class="row g-2">
                                        <div class="col-md-6">
                                            <label class="form-label"> Tanggal Check In </label>
                                            <div class="input-group">
                                                <input type="text" id="check_in" name="check_in" class="form-control bulan start_date" placeholder="Pilih Tanggal Berakhir" value="{{ old('check_in', $businessTrip->hotels->check_in ?? '') }}">
                                                <span class="input-group-text"><i class="ri-calendar-event-line"></i></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label"> Tanggal Check Out </label>
                                            <div class="input-group">
                                                <input type="text" id="check_out" name="check_out" class="form-control bulan end_date" placeholder="Pilih Tanggal Berakhir" value="{{ old('check_out', $businessTrip->hotels->check_out ?? '') }}">
                                                <span class="input-group-text"><i class="ri-calendar-event-line"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ================= ATTACHMENT ================= --}}
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0">
                            Lampiran
                        </h5>
                    </div>
                    <div class="card-body">
                        {{-- <div class="mb-3">
                            <label class="form-label">
                                Upload Lampiran
                            </label>
                            <input type="file"
                                   class="form-control"
                                   name="attachment">
                        </div> --}}
                        <div>
                            <label class="form-label">
                                Catatan Tambahan
                            </label>
                            <textarea class="form-control"
                                      rows="3"
                                      name="notes"
                                      placeholder="Jika Ada catatan Tambahan Untuk Di Sampaikan">{{ old('notes', $businessTrip->notes ?? '') }}</textarea>
                        </div>
                    </div>
                    {{-- ================= FOOTER ================= --}}
                    <div class="card-footer text-end mt-3">
                        <button type="submit"
                                class="btn btn-success px-5">
                            <i class="ri-send-plane-fill me-1"></i>
                            Ajukan Business Trip
                        </button>
                    </div>
                </div>

                {{-- ================= APPROVAL ================= --}}
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0">
                            Approval Yang Dibutuhkan
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            @for ($i = 1; $i <= 8; $i++)
                                <div class="col-md-4">
                                    <label class="form-label">
                                        Approval {{ $i }}
                                    </label>
                                    <input type="text"
                                        class="form-control bg-light"
                                        id="approval_{{ $i }}"
                                        value="-"
                                        readonly>
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>
            </form>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
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
    let transportationIndex = 1;
    let manualExpenseIndex = 0;
    let existingManualExpenses =
        @json($manualExpenses->values() ?? []);
    // console.log(existingManualExpenses);

    initPlugins();
    generateDocumentNumber();
    toggleAdvanceSection();
    toggleHotelSection();
    loadAllowanceByTripType();
    loadApprover();
    toggleHotelReservation();



    // Ensure all initialization functions run
    updateAllowanceTotal();

    $('#trip_type,#start_date,#end_date').on('change', function () {
        loadApprover();
        loadAllowanceByTripType();
    });
    $('#expense_method').on('change', function () {
        toggleAdvanceSection();
    });
    $('#need_hotel').on('change', function () {
        toggleHotelSection();
    });
    $('#reservation_by_ga').on('change', function () {
        toggleHotelReservation();
    });
     $('#trip_type').on('change', function () {
        generateDocumentNumber();
    });
    $(document).on('input', '.currency-format', function () {
        let value = $(this).val();
        value = value.replace(/\D/g, '');
        value = new Intl.NumberFormat('id-ID').format(value);
        $(this).val(value);
    });

    // ================= ADD MANUAL EXPENSE =================
    function formatNumber(number)
    {
        return Number(number).toLocaleString('id-ID');
    }
    function appendManualExpenseRow(data = null)
    {
        let row = `
            <tr class="manual-expense-row">

                <td>
                    <select class="form-select category-select manual-category"
                            name="manual_expenses[${manualExpenseIndex}][category]">

                        <option value="">Pilih</option>

                        <option value="hotel"
                            ${data?.category == 'hotel' ? 'selected' : ''}>
                            Hotel
                        </option>

                        <option value="transport"
                            ${data?.category == 'transport' ? 'selected' : ''}>
                            Transport
                        </option>

                        <option value="parking"
                            ${data?.category == 'parking' ? 'selected' : ''}>
                            Parking
                        </option>

                        <option value="tol"
                            ${data?.category == 'tol' ? 'selected' : ''}>
                            Tol
                        </option>

                        <option value="other"
                            ${data?.category == 'other' ? 'selected' : ''}>
                            Other
                        </option>

                    </select>
                </td>

                <td>
                    <textarea
                        class="form-control manual-notes"
                        rows="1"
                        name="manual_expenses[${manualExpenseIndex}][notes]"
                        placeholder="Keterangan"
                    >${data?.notes ?? ''}</textarea>
                </td>

                <td width="10%">
                    <select class="form-select currency-input manual-currency"
                            name="manual_expenses[${manualExpenseIndex}][currency]">

                        <option value="IDR"
                            ${data?.currency == 'IDR' ? 'selected' : ''}>
                            IDR
                        </option>

                    </select>
                </td>

                <td width="15%">
                    <div class="input-group">

                        <span class="input-group-text">
                            Rp
                        </span>

                        <input type="text"
                            class="form-control amount-input currency-format manual-amount"
                            name="manual_expenses[${manualExpenseIndex}][amount]"
                            value="${formatNumber(data?.unit_amount ?? 0)}"
                            placeholder="0">

                    </div>
                </td>

                <td width="10%">
                    <input type="number"
                        class="form-control qty-input manual-total-unit"
                        name="manual_expenses[${manualExpenseIndex}][qty]"
                        value="${data?.qty ?? 1}"
                        min="1">
                </td>

                <td width="15%">
                    <div class="fw-bold text-primary total-text">
                        ${data?.currency ?? 'IDR'}
                        ${formatNumber(data?.total_amount ?? 0)}
                    </div>
                </td>

                <td class="text-center">

                    <button type="button"
                            class="btn btn-danger btn-sm remove-manual-expense">

                        <i class="ri-delete-bin-line"></i>

                    </button>

                </td>

            </tr>
        `;

        $('#manual-expense-wrapper').append(row);

        manualExpenseIndex++;
    }
//     console.log(existingManualExpenses);
// console.log(typeof existingManualExpenses);
// console.log(Array.isArray(existingManualExpenses));
    // console.log('before foreach');
    if (existingManualExpenses.length > 0) {
        // console.log('masuk foreach');

        existingManualExpenses.forEach(item => {
            // console.log(item);
            appendManualExpenseRow(item);
        });
    }

    $('#btn-add-expense').on('click', function () {

        appendManualExpenseRow();

    });

    $(document).on('click', '.remove-manual-expense', function () {
        $(this).closest('tr').remove();
        calculateGrandTotal();
    });

    $(document).on(
        'keyup change',
        '.amount-input, .qty-input, .currency-input',
        function () {
            let row = $(this).closest('tr');
            let amount = parseInt(
                row.find('.amount-input')
                    .val()
                    .replace(/[^\d]/g, '')
            ) || 0;
            let qty = parseInt(
                row.find('.qty-input')
                    .val()
                    .replace(/[^\d]/g, '')
            ) || 0;
            let currency = row.find('.currency-input').val();
            let total = amount * qty;
            row.find('.total-text').html(
                currency + ' ' + total.toLocaleString('id-ID')
            );
            calculateGrandTotal();
        }
    );
    $('.transportation-main').on('change', function () {
        let value = $(this).val();
        $('#company-car-section').addClass('d-none');
        $('#public-transport-section').addClass('d-none');
        if (value === 'company_car') {
            $('#company-car-section')
                .removeClass('d-none');
        }
        if (value === 'public_transport') {
            $('#public-transport-section')
                .removeClass('d-none');
        }
    });
    $('#public_transport_type').on('change', function () {
        let value = $(this).val();
        $('#schedule-section').addClass('d-none');
        $('#other-transport-section').addClass('d-none');
        if (value === 'plane' || value === 'train') {
            $('#schedule-section')
                .removeClass('d-none');
        }
        if (value === 'other') {
            $('#other-transport-section')
                .removeClass('d-none');
        }
    });
     // ================= INITIALIZE VISIBILITY BASED ON EXISTING DATA =================
    // Trigger transportation-main change to show/hide company-car or public-transport section
    let selectedTransport =
    $('input[name="transport_type"]:checked').val();
    if (selectedTransport) {

        // trigger radio transport
        $('input[name="transport_type"][value="' +
            selectedTransport + '"]')
            .trigger('change');

        // jika public transport
        if (selectedTransport === 'public_transport') {

            let publicType =
                $('#public_transport_type').val();

            if (publicType) {

                $('#public_transport_type')
                    .trigger('change');
            }
        }
    }
        //============================== AJAX SUBMIT ===============================
    $('#businessTripForm').on('submit', function (e) {
        e.preventDefault();
        if (!validateForm()) {
            return;
        }
        let form = $(this);
        let formData = new FormData(this);
        // bersihkan hanya data yang dikirim
        $('.currency-format').each(function () {
            let inputName = $(this).attr('name');
            let cleanValue = $(this)
                .val()
                .replace(/\./g, '');
            formData.set(inputName, cleanValue);
        });
        let actionUrl = form.attr('action');
        let method = form.find('input[name="_method"]').val() ?? 'POST';
        // title swal dinamis
        let isEdit = method === 'PUT';
        Swal.fire({
            title: isEdit
                ? 'Update Business Trip?'
                : 'Ajukan Business Trip?',
            text: isEdit
                ? 'Perubahan data akan disimpan'
                : 'Pengajuan Business Trip akan dikirim',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: isEdit
                ? 'Ya, Update'
                : 'Ya, Submit',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (!result.isConfirmed) {
                return;
            }
            $.ajax({
                url: actionUrl,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (res) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: res.message,
                        timer: 1800,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href =
                            "{{ route('business-trip.profile-index') }}";
                    });
                },
                error: function (xhr) {
                    let msg =
                        xhr.responseJSON?.message ??
                        'Terjadi kesalahan';
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: msg
                    });
                }
            });
        });
    });
});
</script>
<script>
function initPlugins(context = document) {
        // SELECT2
        $(context).find('.select2').each(function () {if ($(this).hasClass("select2-hidden-accessible")) {$(this).select2('destroy');}$(this).select2({width: '100%',placeholder: "Select option"});});
        // FLATPICKR DATE
        $(context).find('.start_date').each(function () {
            if (!this._flatpickr) {
                flatpickr(this, {
                    allowInput: true,
                    dateFormat: "Y-m-d",
                    onChange: function () {
                        updateAllowanceTotal();
                    }
                });
            }
        });
        $(context).find('.end_date').each(function () {
            if (!this._flatpickr) {
                flatpickr(this, {
                    allowInput: true,
                    dateFormat: "Y-m-d",
                    onChange: function () {
                        updateAllowanceTotal();
                    }
                });
            }
        });
        $('.timepicker').flatpickr({
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            time_24hr: true
        });
    }

    let isEdit = @json(isset($businessTrip));
    function generateDocumentNumber()
    {
        // stop jika edit
        if (isEdit) {
            return;
        }

        let tripType = $('#trip_type').val();

        $.ajax({
            url: "{{ route('business-trip.generate-document-number') }}",
            type: "GET",
            data: {
                trip_type: tripType
            },
            success: function(response) {

                $('#document_number').val(
                    response.no_document
                );
            }
        });
    }

    function getTotalDays() {
        let start = $('#start_date').val();
        let end = $('#end_date').val();
        // console.log(start,end);
        // jika belum lengkap, tidak ada total hari
        if (!start || !end) {
            return 0;
        }
        let startDate = new Date(start);
        let endDate = new Date(end);
        // selisih milisecond
        let diffTime = endDate.getTime() - startDate.getTime();
        // convert ke hari
        let diffDays = Math.floor(
            diffTime / (1000 * 60 * 60 * 24)
        ) + 1;
        // minimal 1 hari
        if (diffDays <= 0) {
            return 1;
        }
        return diffDays;
    }

    function updateAllowanceTotal() {
        let totalDays = getTotalDays();
        // console.log(totalDays);
        $('#total_days').val(totalDays > 0 ? totalDays : '');
        $('#allowance-wrapper .allowance-row').each(function () {
            let amount = parseInt(
                $(this)
                    .find('.amount-input')
                    .val()
                    .replace(/\./g, '')
            ) || 0;
            let currency = $(this)
                .find('.currency-input')
                .val();
            let total = amount * totalDays;
            // badge hari
            $(this).find('.total-day-badge')
                .text(totalDays > 0 ? totalDays + ' Hari' : '0 Hari');
            // total amount
            $(this).find('.total-amount-text')
                .text(
                    currency + ' ' + total.toLocaleString()
                );
        });
        calculateGrandTotal();
    }

    function calculateGrandTotal() {
        let grandTotal = 0;
        // ================= AUTO ALLOWANCE =================
        $('#allowance-wrapper .allowance-row').each(function () {
            let amount = parseCurrency(
                $(this).find('.amount-input').val()
            );
            let totalDaysText = $(this)
                .find('.total-day-badge')
                .text();
            let totalDays = parseInt(totalDaysText) || 0;
            grandTotal += amount * totalDays;
        });
        // ================= MANUAL EXPENSE =================
        $('#manual-expense-wrapper .manual-expense-row').each(function () {
            let amount = parseCurrency(
                $(this).find('.amount-input').val()
            );
            let qty = parseInt(
                $(this)
                    .find('.qty-input')
                    .val()
                    .replace(/\./g, '')
            ) || 0;
            grandTotal += amount * qty;
        });
        $('#grand-total').html(
            'IDR ' +
            new Intl.NumberFormat('id-ID')
                .format(grandTotal)
        );
    }

    function fetchAllowances(tripType, startDate, endDate) {
        if (!tripType) {
            $('#allowance-wrapper').html('');
            calculateGrandTotal();
            return;
        }
        $.ajax({
            url: "{{ route('business-trip.get-allowance') }}",
            type: "GET",
            data: {
                trip_type: tripType,
                start_date: startDate,
                end_date: endDate
            },
            success: function (response) {
                let html = '';
                // console.log(response);
                response.data.forEach((item, index) => {
                    html += `
                        <tr class="allowance-row">
                            <td>
                                <input type="hidden"
                                    name="allowances[${index}][business_trip_allowance_id]"
                                    value="${item.id}">

                                <input type="text"
                                    class="form-control"
                                    value="${item.category}"
                                    readonly>

                                <input type="hidden"
                                    name="allowances[${index}][category]"
                                    value="${item.category}">
                            </td>

                            <td>
                                <input type="text"
                                    class="form-control"
                                    value="${item.trip_type}"
                                    readonly>

                                <input type="hidden"
                                    name="allowances[${index}][trip_type]"
                                    value="${item.trip_type}">
                            </td>
                            <td>

                                <input type="text"
                                    class="form-control currency-input"
                                    name="allowances[${index}][currency]"
                                    value="${item.currency}"
                                    readonly>
                            </td>
                            <td>
                                <div class="input-group">
                                     <span class="input-group-text">
                                        Rp
                                    </span>
                                    <input type="text"
                                        class="form-control amount-input currency-format"
                                        name="allowances[${index}][amount]"
                                        value="${Number(item.amount).toLocaleString('id-ID')}"
                                        readonly>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success total-day-badge">
                                    1 Hari
                                </span>
                            </td>
                           <td class="text-center">
                                <span class="fw-bold text-primary total-amount-text ">
                                    ${item.currency}
                                    ${Number(item.amount).toLocaleString('id-ID')}
                                </span>
                            </td>
                        </tr>
                    `;
                });
                $('#allowance-wrapper').html(html);
                calculateGrandTotal();
                updateAllowanceTotal();
            }
        });
    }

    function loadAllowanceByTripType() {
        let tripType = $('#trip_type').val();
        let startDate = $('#start_date').val();
        let endDate = $('#end_date').val();
        fetchAllowances(
            tripType,
            startDate,
            endDate
        );
    }
    function parseCurrency(value) {
        if (!value) {
            return 0;
        }
        return parseInt(
            value.toString().replace(/\./g, '')
        ) || 0;
    }

    function toggleAdvanceSection() {
        let method = $('#expense_method').val();
        if (method === 'advance') {
            $('#advance-section').removeClass('d-none');
        } else {
            $('#advance-section').addClass('d-none');
        }
    }

    function toggleHotelSection() {
        let hotel = $('#need_hotel').val();
        if (hotel == '1') {
            $('#hotel-section').removeClass('d-none');
        } else {
            $('#hotel-section').addClass('d-none');
        }
    }
    function toggleHotelReservation() {
        let value = $('#reservation_by_ga').val();
        if (value == '1') {
            $('#hotel-input-wrapper')
                .find('input, textarea, select')
                .prop('disabled', true);
        } else {
            $('#hotel-input-wrapper')
                .find('input, textarea, select')
                .prop('disabled', false);
        }
    }
    function loadApprover() {
        let tripType = $('#trip_type').val();
        $.ajax({
            url: "{{ route('business-trip.get-approver') }}",
            type: "GET",
            data: {
                trip_type: tripType
            },
            success: function (response) {
                for (let i = 1; i <= 8; i++) {
                    $('#approval_' + i).val(
                        response['approve_' + i] ?? '-'
                    );
                }
            }
        });
    }
    function validateForm() {

    if (!$('#trip_type').val()) {
        Swal.fire('Warning', 'Trip type wajib dipilih', 'warning');
        return false;
    }

    if (!$('#start_date').val()) {
        Swal.fire('Warning', 'Tanggal mulai wajib diisi', 'warning');
        return false;
    }

    if (!$('#end_date').val()) {
        Swal.fire('Warning', 'Tanggal selesai wajib diisi', 'warning');
        return false;
    }
    if (!$('#departure_time').val()) {
        Swal.fire('Warning', 'Jam Keberangkatan Wajib Diisi', 'warning');
        return false;
    }
    if (!$('#arrival_time').val()) {
        Swal.fire('Warning', 'Perkiraan Jam Tiba Wajib Diisi', 'warning');
        return false;
    }
    if (!$('#arrival_to').val()) {
        Swal.fire('Warning', 'Tujuan Perjalanan Dinas Wajib Diisi', 'warning');
        return false;
    }

    if (!$('#purpose').val()) {
        Swal.fire('Warning', 'Keperluan  perjalanan wajib diisi', 'warning');
        return false;
    }

    let expenseMethod = $('#expense_method').val();

    if (
        expenseMethod === 'advance' &&
        !$('#advance_amount').val()
    ) {
        Swal.fire(
            'Warning',
            'Nominal advance wajib diisi',
            'warning'
        );

        return false;
    }


    let needHotel = $('#need_hotel').val();
    let reservationByGA = $('#reservation_by_ga').val();

    if (needHotel == '1') {

        if (
            reservationByGA == '0'
        ) {

            if (!$('#hotel_name').val()) {
                Swal.fire(
                    'Warning',
                    'Nama hotel wajib diisi',
                    'warning'
                );

                return false;
            }

            if (!$('#check_in').val()) {
                Swal.fire(
                    'Warning',
                    'Check in wajib diisi',
                    'warning'
                );

                return false;
            }

            if (!$('#check_out').val()) {
                Swal.fire(
                    'Warning',
                    'Check out wajib diisi',
                    'warning'
                );

                return false;
            }
            if (!$('#Night_checkIn').val()) {
                Swal.fire(
                    'Warning',
                    'Jumlah Malam Wajib Diisi',
                    'warning'
                );

                return false;
            }
            if (!$('#Days_checkIn').val()) {
                Swal.fire(
                    'Warning',
                    'Jumlah Hari Wajib Diisi',
                    'warning'
                );

                return false;
            }

        }
    }

    let manualValid = true;

    $('#manual-expense-wrapper tr').each(function () {

        let category = $(this)
            .find('.manual-category')
            .val();

        let notes = $(this)
            .find('.manual-notes')
            .val();

        let currency = $(this)
            .find('.manual-currency')
            .val();

        let amount = $(this)
            .find('.manual-amount')
            .val();

        let totalUnit = $(this)
            .find('.manual-total-unit')
            .val();

        if (
            !category ||
            !notes ||
            !currency ||
            !amount ||
            !totalUnit
        ) {

            manualValid = false;
            return false;
        }
    });

    if (!manualValid) {

        Swal.fire(
            'Warning',
            'Semua Kolom Biaya wajib diisi',
            'warning'
        );

        return false;
    }

    let transportationType =
        $('input[name="transport_type"]:checked').val();

    if (!transportationType) {

        Swal.fire(
            'Warning',
            'Jenis transportasi wajib dipilih',
            'warning'
        );

        return false;
    }

    if (transportationType === 'company_car') {

        if (!$('#vehicle_number').val()) {

            Swal.fire(
                'Warning',
                'Nomor kendaraan wajib diisi',
                'warning'
            );

            return false;
        }

        if (!$('#driver_name').val()) {

            Swal.fire(
                'Warning',
                'Nama driver wajib diisi',
                'warning'
            );

            return false;
        }
    }

    if (transportationType === 'public_transport') {

        let publicType =
            $('#public_transport_type').val();

        if (!publicType) {

            Swal.fire(
                'Warning',
                'Jenis transportasi umum wajib dipilih',
                'warning'
            );

            return false;
        }

        if (
            publicType === 'plane' ||
            publicType === 'train'
        ) {

            if (!$('#transport_start_date').val()) {

                Swal.fire(
                    'Warning',
                    'Tanggal keberangkatan wajib diisi',
                    'warning'
                );

                return false;
            }

            if (!$('#transport_departure_time').val()) {

                Swal.fire(
                    'Warning',
                    'Perkiraan Jam keberangkatan wajib diisi',
                    'warning'
                );

                return false;
            }

            if (!$('#transport_end_date').val()) {

                Swal.fire(
                    'Warning',
                    'Tanggal kedatangan wajib diisi',
                    'warning'
                );

                return false;
            }

            if (!$('#transport_arrival_time').val()) {

                Swal.fire(
                    'Warning',
                    'Perkiraan Jam Tiba wajib diisi',
                    'warning'
                );

                return false;
            }
        }

        if (
            publicType === 'other' &&
            !$('#transport_notes').val()
        ) {

            Swal.fire(
                'Warning',
                'Keterangan transportasi wajib diisi',
                'warning'
            );

            return false;
        }
    }

    return true;
}


    // $('#btn-add-expense').on('click', function () {

    //     let row = `
    //         <tr class="manual-expense-row">

    //             <td>
    //                 <select class="form-select category-select manual-category"
    //                         name="manual_expenses[${manualExpenseIndex}][category]">

    //                     <option value="">Pilih</option>
    //                     <option value="hotel">Hotel</option>
    //                     <option value="transport">Transport</option>
    //                     <option value="parking">Parking</option>
    //                     <option value="tol">Tol</option>
    //                     <option value="laundry">Laundry</option>
    //                     <option value="other">Other</option>

    //                 </select>
    //             </td>

    //             <td>
    //                 <textarea class="form-control manual-notes"
    //                         rows="1"
    //                         name="manual_expenses[${manualExpenseIndex}][notes]"
    //                         placeholder="Keterangan"></textarea>
    //             </td>

    //             <td width="10%">
    //                 <select class="form-select currency-input manual-currency"
    //                         name="manual_expenses[${manualExpenseIndex}][currency]">

    //                     <option value="IDR">IDR</option>

    //                 </select>
    //             </td>

    //             <td width="15%">
    //                 <div class="input-group">
    //                     <span class="input-group-text">
    //                         Rp
    //                     </span>
    //                     <input type="text"
    //                         class="form-control amount-input currency-format manual-amount"
    //                         name="manual_expenses[${manualExpenseIndex}][amount]"
    //                         placeholder="0">
    //                 </div>
    //             </td>

    //             <td width="10%">
    //                 <input type="number"
    //                     class="form-control qty-input manual-total-unit"
    //                     name="manual_expenses[${manualExpenseIndex}][qty]"
    //                     value="1"
    //                     min="1">
    //             </td>

    //             <td width="15%">
    //                 <div class="fw-bold text-primary total-text">
    //                     IDR 0
    //                 </div>
    //             </td>

    //             <td class="text-center">
    //                 <button type="button"
    //                         class="btn btn-danger btn-sm remove-manual-expense">
    //                     <i class="ri-delete-bin-line"></i>
    //                 </button>
    //             </td>
    //         </tr>
    //     `;
    //     $('#manual-expense-wrapper').append(row);
    //     manualExpenseIndex++;
    // });
</script>
@endsection
