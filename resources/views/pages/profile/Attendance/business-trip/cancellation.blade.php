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
                id="businessReport-Claim"
                action="{{ isset($businessReport)
                    ? route('business-trip.report.update', encrypt($businessReport->id))
                    : route('business-trip-cancellation.store')
                }}"
                method="POST"
            >
                @csrf
                @if(isset($businessReport))
                    @method('PUT')
                @endif
                {{-- ================= HEADER ================= --}}
                <div class="card mb-3">
                    <input type="hidden" name="business_trip_id" value="{{ $businessTrip->id }}">
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
                                        Formulir Pembatalan Perjalanan Dinas
                                        {{$businessTrip->id}}
                                    </h4>
                                    <small class="text-muted">
                                        Pengajuan Pembatalan Business Trip
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
                                <input type="text" class="form-control" id="no_document" name="no_document"
                                    value="{{ old('no_document',$businessTrip->no_document ?? '') }}" readonly
                                    >
                                {{-- <select id="select-no-document" name="business_trip_id" class="form-select select2"
                                        value="{{ old('no_document', $businessTrip->no_document ?? '') }}">
                                    <option value=""></option>
                                </select> --}}
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">
                                    Tipe Perjalanan Dinas
                                </label>
                                <select class="form-select" id="trip_type" name="trip_type">
                                    <option value="domestic" {{ old('trip_type', $businessReport->trip_type ?? '') == 'domestic' ? 'selected' : '' }}>
                                        Domestic
                                    </option>
                                    <option value="overseas" {{ old('trip_type', $businessReport->trip_type ?? '') == 'overseas' ? 'selected' : '' }}>
                                        Overseas
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">
                                    Tanggal Perjalanan Dinas
                                </label>
                                <div class="input-group">
                                    <input type="text" id="start_date" name="start_date" class="form-control bulan" placeholder="Pilih Tanggal Berakhir" value="{{ old('start_date', $businessTrip->start_date ?? '') }}" readonly>
                                    <span class="input-group-text"><i class="ri-calendar-event-line"></i></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">
                                    Sampai Dengan
                                </label>
                                <div class="input-group">
                                    <input type="text" id="end_date" name="end_date" class="form-control bulan" placeholder="Pilih Tanggal Berakhir" value="{{ old('end_date', $businessTrip->end_date ?? '') }}" readonly>
                                    <span class="input-group-text"><i class="ri-calendar-event-line"></i></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">
                                    Total Hari
                                </label
                                >
                                <input type="text" class="form-control" id="total_days" name="total_days"
                                    value="{{ old('total_days',$businessTrip->total_days ?? '') }}" readonly
                                    >
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ================= BUSINESS TRIP DETAIL ================= --}}
                <div class="card mb-3 border-0 shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm me-3">
                                <span class="avatar-title bg-primary-subtle text-white rounded-circle fs-4">
                                    <i class="ri-file-text-line"></i>
                                </span>
                            </div>

                            <div>
                                <h5 class="mb-0 fw-semibold">
                                    Alasan Pembatalan Perjalanan Dinas
                                </h5>

                                {{-- <small class="text-muted">
                                    Detail hasil perjalanan dinas dan laporan aktivitas
                                </small> --}}
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row g-3 mb-3">

                                <div class="col-md-4">
                                    <div class="form-check card-radio">
                                        <input class="form-check-input transportation-main"
                                            type="radio"
                                            name="reason"
                                            id="emergency"
                                            value="emergency"
                                            {{ old('transport_type', $businessTrip->transportations->transport_type ?? '') == 'private' ? 'checked' : '' }}>

                                        <label class="form-check-label w-100"
                                            for="emergency">

                                            <span class="d-block fw-semibold">
                                                Kondisi Darurat atau Alasan Kesehatan
                                            </span>

                                            <small class="text-muted">
                                                {{-- Menggunakan kendaraan pribadi --}}
                                            </small>

                                        </label>

                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-check card-radio">

                                        <input class="form-check-input transportation-main"
                                            type="radio"
                                            name="reason"
                                            id="company_decision"
                                            value="company_decision"
                                            {{ old('transport_type', $businessTrip->transportations->transport_type ?? '') == 'company_car' ? 'checked' : '' }}>

                                        <label class="form-check-label w-100"
                                            for="company_decision">

                                            <span class="d-block fw-semibold">
                                                Perubahan Keputusan Perusahaan
                                            </span>

                                            <small class="text-muted">
                                                {{-- Mobil operasional perusahaan --}}
                                            </small>

                                        </label>

                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-check card-radio">

                                        <input class="form-check-input transportation-main"
                                            type="radio"
                                            name="reason"
                                            id="force_majeure"
                                            value="force_majeure"
                                            {{ old('transport_type', $businessTrip->transportations->transport_type ?? '') == 'public_transport' ? 'checked' : '' }}>

                                        <label class="form-check-label w-100"
                                            for="force_majeure">

                                            <span class="d-block fw-semibold">
                                                Force Majeure
                                            </span>

                                            <small class="text-muted">
                                                {{-- Taxi, plane, train, dan lainnya --}}
                                            </small>

                                        </label>

                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check card-radio">

                                        <input class="form-check-input transportation-main"
                                            type="radio"
                                            name="reason"
                                            id="personal_reasons"
                                            value="personal_reasons"
                                            {{ old('transport_type', $businessTrip->transportations->transport_type ?? '') == 'public_transport' ? 'checked' : '' }}>

                                        <label class="form-check-label w-100"
                                            for="personal_reasons">

                                            <span class="d-block fw-semibold">
                                                Alasan Pribadi
                                            </span>

                                            <small class="text-muted">
                                                {{-- Lainnya --}}
                                            </small>

                                        </label>

                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check card-radio">

                                        <input class="form-check-input transportation-main"
                                            type="radio"
                                            name="reason"
                                            id="other"
                                            value="other"
                                            {{ old('transport_type', $businessTrip->transportations->transport_type ?? '') == 'public_transport' ? 'checked' : '' }}>

                                        <label class="form-check-label w-100"
                                            for="other">

                                            <span class="d-block fw-semibold">
                                                Lainnya
                                            </span>

                                            <small class="text-muted">
                                                {{-- Perlu Menyertakan Alasan --}}
                                            </small>

                                        </label>

                                    </div>
                                </div>

                            </div>
                            <div id="other-transport-section"
                                {{-- class="d-none" --}}
                                >
                                <label class="form-label">
                                    Keterangan Pembatalan
                                </label>
                                <textarea class="form-control"
                                        rows="3"
                                        name="reason_other"
                                        placeholder="Keterangan Pembatalan Wajib Jika Alasan Pembatalan Lainnya ">{{ old('transport_notes', $businessTrip->transportations->notes ?? '') }}</textarea>

                            </div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-body">
                        {{-- ========================================== MANUAL INPUT ========================================== --}}
                        <div>
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-xs me-2">
                                        <span class="avatar-title bg-warning-subtle text-white rounded fs-16">
                                            <i class="ri-edit-2-line"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 ">
                                            Kerugian Biaya Yang Timbul
                                        </h6>
                                        <small class="text-muted">
                                            Daily, Transportasi, hotel, dan biaya lainnya yang akan di claim
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
                                            <th>Nominal</th>
                                            <th>Jumlah</th>
                                            <th>Total</th>
                                            <th width="5%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="manual-expense-wrapper">
                                         <!-- AUTO DOCUMENT -->
                                        <tr class="auto-expense-row"></tr>

                                        <!-- MANUAL USER -->
                                        {{-- <tr class="manual-expense-row"></tr> --}}
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
                                        <li>Data Yang Ditampilkan Merupakan Saran Yang Didapatkan dari Perjalanan Dinas Yang Telah Di Ajukan</li>
                                        <li>Saran Data Dapat di Hapus Bila Memang tidak Menimbulkan Kerugian</li>
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

                <div class="card mb-3" id="expense-burden-section">
                    <div class="card-header">
                        <h5 class="mb-0">
                            Kerugian Biaya Yang Timbul Sejumlah Nilai Diatas Akan Ditanggung Oleh :
                        </h5>
                    </div>

                    <div class="card-body">
                        <div class="row align-items-center mb-3">
                            <div class="col-md-3">
                                <label class="fw-semibold">
                                    Perusahaan, sebesar
                                </label>
                            </div>

                            <div class="col-md-5">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        Rp
                                    </span>
                                    <input type="text"
                                        class="form-control currency-format burden-input"
                                        id="company_amount"
                                        name="company_covered_amount"
                                        placeholder="0">
                                </div>
                            </div>
                        </div>

                        <div class="row align-items-center mb-4">
                            <div class="col-md-3">
                                <label class="fw-semibold">
                                    Karyawan, sebesar
                                </label>
                            </div>

                            <div class="col-md-5">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        Rp
                                    </span>

                                    <input type="text"
                                        class="form-control currency-format burden-input"
                                        id="employee_amount"
                                        name="employee_covered_amount"
                                        placeholder="0">

                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row align-items-center g-3">
                            <!-- TOTAL PEMBEBANAN -->
                            <div class="col-md-3">
                                <label class="fw-semibold mb-2 d-block">
                                    Total Pembebanan
                                </label>

                                <div class="input-group">
                                    <span class="input-group-text">
                                        Rp
                                    </span>

                                    <input type="text"
                                        class="form-control fw-bold bg-light"
                                        id="burden_total"
                                        readonly>
                                </div>
                            </div>
                            <!-- GRAND TOTAL CLAIM -->
                            <div class="col-md-3">
                                <label class="fw-semibold mb-2 d-block">
                                    Grand Total Claim
                                </label>

                                <div class="input-group">
                                    <span class="input-group-text">
                                        Rp
                                    </span>

                                    <input type="text"
                                        class="form-control fw-bold bg-light"
                                        id="grand-total-comparator"
                                        readonly>
                                </div>
                            </div>
                            <!-- SELISIH -->
                            <div class="col-md-3">
                                <label class="fw-semibold mb-2 d-block">
                                    Selisih
                                </label>

                                <div class="input-group">
                                    <span class="input-group-text">
                                        Rp
                                    </span>

                                    <input type="text"
                                        class="form-control fw-bold"
                                        id="difference-total"
                                        readonly>

                                </div>
                            </div>
                            <!-- STATUS -->
                            <div class="col-md-3">
                                <label class="fw-semibold mb-2 d-block">
                                    Status
                                </label>

                                <div id="difference-status"
                                    class="border rounded p-2 text-center">
                                    <i class="ri-close-circle-line text-danger"></i>

                                    Belum Valid
                                </div>
                            </div>
                        </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0">
                            Lampiran
                        </h5>
                    </div>
                    <div class="card-body">
                        <div>
                            <label class="form-label">
                                Catatan Tambahan
                            </label>
                            <textarea class="form-control"
                                      rows="3"
                                      name="notes"
                                      placeholder="Jika Ada catatan Tambahan Untuk Di Sampaikan">{{ old('notes', $businessReport->notes ?? '') }}</textarea>
                        </div>
                    </div>
                    {{-- ================= FOOTER ================= --}}
                    <div class="card-footer text-end mt-3">
                        <button type="submit"
                                class="btn btn-success px-5">
                            <i class="ri-send-plane-fill me-1"></i>
                            Kirim Pengajuan Pembatalan
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
    const businessTripCosts = @json($businessTrip->costs);
</script>
<script>
    $(document).ready(function () {
        $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    loadBusinessTripExpense(businessTripCosts);
    initPlugins();
    loadApprover();
    calculateGrandTotal();

    $('#btn-add-expense').click(function(){
        // console.log('clic')
        appendManualExpenseRow();
    });
    $(document).on('click', '.remove-manual-expense', function () {
        $(this).closest('tr').remove();
        calculateGrandTotal();
    });

    $(document).on('input', '.currency-format', function () {
        let value = $(this).val();
        value = value.replace(/\D/g, '');
        value = new Intl.NumberFormat('id-ID').format(value);
        $(this).val(value);
    });

    $(document).on('keyup change','#company_amount,#employee_amount',function(){
        calculateGrandTotal();
    });

    $(document).on('keyup change','.amount-input, .qty-input, .currency-input , .burden-input',function () {
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
    });

    //============================== AJAX SUBMIT ===============================
    $('#businessReport-Claim').on('submit', function (e) {
        e.preventDefault();
        // ensure derived values are up-to-date before building FormData
        updateAllowanceTotal();
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
                beforeSend: function () {
                    Swal.fire({
                        title: 'Menyimpan Data...',
                        text: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
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
    function validateForm()
    {
         let reason = $('input[name="reason"]:checked').val();
        let reasonOther = $('textarea[name="reason_other"]').val()?.trim();

        if (reason === 'other' && !reasonOther) {

            Swal.fire({
                icon: 'warning',
                title: 'Validasi Gagal',
                text: 'Keterangan Pembatalan wajib diisi jika alasan pembatalan adalah Lainnya'
            });

            $('textarea[name="reason_other"]').focus();

            return false;
        }

        return true;
    }
    function initPlugins(context = document) {
        // SELECT2
        $(context).find('.select2').each(function () {if ($(this).hasClass("select2-hidden-accessible")) {$(this).select2('destroy');}$(this).select2({width: '100%',placeholder: "Select option"});});
        $('#select-no-document').select2({placeholder : 'Tanpa Business Trip / Pilih Document', allowClear : true , width : '100%'});
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

    function formatNumber(num)
    {
        return Number(num).toLocaleString('id-ID');
    }

    let manualExpenseIndex = 0;
    function appendManualExpenseRow(data = null)
    {
        let index = manualExpenseIndex;
        let row = `
            <tr class="manual-expense-row">

                <td>

                    <select class="form-select manual-category"
                        name="manual_expenses[${index}][category]">

                        <option value="">Pilih</option>

                        <option value="hotel"
                            ${data?.category=='hotel'?'selected':''}>
                            Hotel
                        </option>

                        <option value="transport"
                            ${data?.category=='transport'?'selected':''}>
                            Transport
                        </option>

                        <option value="parking"
                            ${data?.category=='parking'?'selected':''}>
                            Parking
                        </option>

                        <option value="tol"
                            ${data?.category=='tol'?'selected':''}>
                            Tol
                        </option>

                        <option value="laundry"
                            ${data?.category=='laundry'?'selected':''}>
                            Laundry
                        </option>

                        <option value="other"
                            ${data?.category=='other'?'selected':''}>
                            Other
                        </option>

                        <option value="daily"
                            ${data?.category=='daily'?'selected':''}>
                            Daily
                        </option>

                    </select>

                </td>

                <td>

                    <textarea
                        class="form-control manual-notes"
                        rows="1"
                        name="manual_expenses[${index}][notes]">${data?.notes ?? ''}</textarea>

                </td>

                <td>

                    <select class="form-select manual-currency currency-input"
                        name="manual_expenses[${index}][currency]">

                        <option value="IDR"
                            ${(data?.currency ?? 'IDR')=='IDR'?'selected':''}>
                            IDR
                        </option>

                    </select>

                </td>

                <td>
                    <div class="input-group">
                        <span class="input-group-text"> Rp </span>
                        <input type="text"
                            class="form-control amount-input currency-format"
                            name="manual_expenses[${index}][amount]"
                            value="${formatNumber(data?.amount??data?.unit_amount ?? 0 )}"
                            >
                    </div>
                </td>

                <td>

                    <input type="number"
                        class="form-control qty-input"
                        name="manual_expenses[${index}][qty]"
                        value="${data?.qty ?? 1}"
                        min="1">

                </td>

                <td>
                    <div class="fw-bold total-text">
                        IDR ${formatNumber(data?.unit_total ?? ((data?.amount ?? data?.unit_amount ?? 0) * (data?.qty ?? 1 )))}
                    </div>
                </td>

                <td class="text-center">

                    <button type="button"
                        class="btn btn-danger remove-manual-expense">

                        <i class="ri-delete-bin-line"></i>

                    </button>

                </td>

            </tr>
        `;
        $('#manual-expense-wrapper').append(row);
        manualExpenseIndex++;
    }

    function loadBusinessTripExpense(costs)
    {
        $('#manual-expense-wrapper').empty();
        manualExpenseIndex = 0;
        costs.forEach(item => {
            appendManualExpenseRow({
                category: item.category,
                notes: item.notes,
                currency: item.currency,
                unit_amount: item.unit_amount,
                qty: item.qty,
                unit_total: item.unit_total
            });
        });

        calculateGrandTotal();
    }

    function calculateGrandTotal()
    {
        let manualTotal = 0;

        $('#manual-expense-wrapper tr').each(function(){

            let amount =
                parseCurrency(
                    $(this)
                    .find('.amount-input')
                    .val()
                ) || 0;

            let qty =
                parseInt(
                    $(this)
                    .find('.qty-input')
                    .val()
                ) || 0;

            manualTotal += amount * qty;

        });

        let grandTotal = manualTotal;

        let formatted =
            grandTotal.toLocaleString(
                'id-ID'
            );

        $('#grand-total').html(
            'IDR ' + formatted
        );

        $('#grand-total-comparator')
            .val(formatted);

        // ================= DIFFERENCE =================

        let company =
            parseCurrency(
                $('#company_amount').val()
            );

        let employee =
            parseCurrency(
                $('#employee_amount').val()
            );

        let burdenTotal =
            company + employee;

        $('#burden_total').val(
            burdenTotal.toLocaleString(
                'id-ID'
            )
        );

        let diff =
            burdenTotal - grandTotal;

        $('#difference-total').val(
            Math.abs(diff)
            .toLocaleString('id-ID')
        );

        let status =
            $('#difference-status');

        if(diff === 0){

            status.html(`
                <i class="ri-checkbox-circle-fill text-success fs-5"></i>
                <span class="text-success fw-bold">
                    Valid
                </span>
            `);

        }
        else if(diff > 0){

            status.html(`
                <i class="ri-error-warning-fill text-warning fs-5"></i>
                <span class="text-warning fw-bold">
                    Lebih
                </span>
            `);

        }
        else{

            status.html(`
                <i class="ri-close-circle-fill text-danger fs-5"></i>
                <span class="text-danger fw-bold">
                    Kurang
                </span>
            `);

        }
    }

    function parseCurrency(value) {
        if (!value) {
            return 0;
        }
        return parseInt(
            value.toString().replace(/\./g, '')
        ) || 0;
    }

</script>
@endsection
