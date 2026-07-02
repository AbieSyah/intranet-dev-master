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
                    : route('business-trip-report.store')
                }}"
                method="POST"
            >
                @csrf
                @if(isset($businessReport))
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
                                        Formulir Report atau Pengajuan Claim Business Trip
                                        {{-- {{$businessReport->business_trip_id}} --}}
                                    </h4>
                                    <small class="text-muted">
                                        Report atau Claim business trip karyawan
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
                                <select id="select-no-document" name="business_trip_id" class="form-select select2">
                                    <option value=""></option>
                                        @foreach ($datas as $data)
                                            <option value="{{ $data->id }}"
                                                {{ old('business_trip_id',$businessReport->business_trip_id ?? '') == $data->id? 'selected': ''}}>
                                                {{ $data->no_document }}
                                            </option>
                                        @endforeach
                                    </option>
                                </select>
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
                                    <input type="text" id="start_date" name="start_date" class="form-control bulan start_date" placeholder="Pilih Tanggal Berakhir" value="{{ old('start_date', $businessReport->start_date ?? '') }}">
                                    <span class="input-group-text"><i class="ri-calendar-event-line"></i></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">
                                    Sampai Dengan
                                </label>
                                <div class="input-group">
                                    <input type="text" id="end_date" name="end_date" class="form-control bulan end_date" placeholder="Pilih Tanggal Berakhir" value="{{ old('end_date', $businessReport->end_date ?? '') }}">
                                    <span class="input-group-text"><i class="ri-calendar-event-line"></i></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">
                                    Total Hari
                                </label
                                >
                                <input type="text" class="form-control" id="total_days" name="total_days"
                                    value="{{ old('total_days',$businessReport->total_days ?? '') }}" readonly
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
                                    Laporan Perjalanan Dinas
                                </h5>

                                <small class="text-muted">
                                    Detail hasil perjalanan dinas dan laporan aktivitas
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row g-3">
                            <!-- JUMLAH KASBON -->
                            <div class="col-md-12">
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
                                        value="{{ old('advance_amount',isset($businessReport->balance_amount) ? number_format((float) $businessReport->balance_amount, 0, ',', '.'): '') }}"
                                        {{-- readonly --}}
                                        >
                                </div>
                            </div>
                            <!-- TEMPAT TUJUAN -->
                            <div class="col-md-12">
                                <label class="form-label">
                                    Tempat Tujuan
                                </label>

                                <input type="text"
                                    id="arrival_to"
                                    name="arrival_to"
                                    class="form-control"
                                    value="{{ old('arrival_to', $businessReport->arrival_to ?? '') }}">
                            </div>

                            <!-- KEPERLUAN -->
                            <div class="col-md-12">
                                <label class="form-label">
                                    Keperluan Perjalanan Dinas
                                </label>

                                <textarea class="form-control"
                                    rows="3"
                                    id="purpose"
                                    name="purpose">{{ old('purpose', $businessReport->purpose ?? '') }}</textarea>
                            </div>

                            <!-- HASIL PERJALANAN -->
                            <div class="col-md-12">
                                <label class="form-label">
                                    Hasil Perjalanan Dinas
                                </label>

                                <textarea class="form-control"
                                    rows="5"
                                    id="trip_result"
                                    name="trip_result"
                                    placeholder="Jelaskan hasil perjalanan dinas, meeting, training, kunjungan, atau pencapaian selama perjalanan">{{ old('trip_result', $businessReport->trip_result ?? '') }}</textarea>

                                {{-- <small class="text-muted">
                                    Contoh: hasil meeting, progress project, training yang didapatkan, evaluasi vendor, dll.
                                </small> --}}
                            </div>

                            <!-- POIN HASIL -->
                            {{-- <div class="col-md-12">
                                <label class="form-label">
                                    Ringkasan / Poin Penting
                                </label>
                                <div class="border rounded p-3 ">
                                    <!-- WRAPPER -->
                                    <div id="result-point-wrapper">
                                        <!-- ITEM -->
                                        <div class="input-group mb-3 result-point-item">

                                            <span class="input-group-text">
                                                1
                                            </span>
                                            <input type="text"
                                                class="form-control"
                                                name="result_points[]"
                                                placeholder="Masukkan poin hasil perjalanan dinas">

                                            <button type="button"
                                                    class="btn btn-danger remove-point">

                                                <i class="ri-delete-bin-line"></i>
                                            </button>

                                        </div>

                                    </div>

                                    <!-- BUTTON ADD -->
                                    <div class="text-center mt-2">

                                        <button type="button"
                                                class="btn btn-outline-primary btn-md"
                                                id="btn-add-point">

                                            <i class="ri-add-line"></i>
                                            Tambah Poin Ringkasan

                                        </button>

                                    </div>

                                </div>

                            </div> --}}
                        </div>
                    </div>
                </div>

                {{-- ================= BUSINESS TRIP ALLOWANCE ================= --}}
                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h5 class="mb-0">
                                Rincian Biaya Total Perjalanan Dinas Yang Akan Di Ajukan Claim
                            </h5>

                            <small class="text-muted">
                                Uang Makan akan terisi otomatis berdasarkan record absensi
                            </small>
                        </div>
                    </div>

                    <div class="card-body">
                        {{-- AUTO GENERATED --}}
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-xs me-2">
                                        <span class="avatar-title bg-success-subtle text-white rounded fs-16">
                                            <i class="ri-settings-3-line"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">
                                            Uang Makan
                                        </h6>

                                        <small class="text-muted">
                                            Otomatis Berdasarkan Record Absensi
                                        </small>
                                    </div>
                                </div>
                                <div class="gap-3">
                                    <button type="button"
                                            class="btn btn-warning btn-label waves-effect waves-light shadow-sm"
                                            id="btn-reset">
                                        <i class="ri-refresh-line label-icon align-middle fs-16 me-2"></i>
                                        Reset
                                    </button>
                                    <button type="button"
                                            class="btn btn-primary btn-label waves-effect waves-light shadow-sm"
                                            id="btn-add-meal">
                                        <i class="ri-add-line label-icon align-middle fs-16 me-2"></i>
                                        Tambah Uang Makan
                                    </button>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="text-center">Date</th>
                                            <th class="text-center">Kategori</th>
                                            <th class="text-center">Currency</th>
                                            <th class="text-center">Nominal</th>
                                            <th class="text-center">Nota <span class="text-muted">(Optional)</span></th>
                                            <th width="5%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="allowance-wrapper">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="col-md-12 text-md-end mt-3 mb-3 mt-md-0">
                                <small class="text-muted d-block">
                                    Total Estimasi Uang Makan
                                </small>
                                <h3 class="fw-bold text-primary mb-0"
                                    id="meal-total">

                                    IDR 0
                                </h3>
                            </div>
                        </div>
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
                                            Biaya Lainnya
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
                                            <th>Attachment</th>
                                            <th width="5%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="manual-expense-wrapper">
                                         <!-- AUTO DOCUMENT -->
                                        <tr class="auto-expense-row"></tr>

                                        <!-- MANUAL USER -->
                                        <tr class="manual-expense-row"></tr>
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
                                        <li>Nominal Claim untuk Uang Makan Akan Terisi Otomatis Berdasarkan Master Perjalanan Bisnis</li>
                                        <li>Bila Ada Claim Lainnya yang akan di Ajukan wajib Mencantumkan Attachment atau Nota</li>
                                        <li>Jika Claim atau Report memiliki Proposal Perjalanan Bisnis maka Biaya Yang Akan di Ajukan Di proposal akan terisi Otomatis di Bagian Biaya </li>
                                        <li>Biaya Yang Tidak Diperlukan Dapat Dihapus Dan Tidak Akan Ikut Dalam Pengajuan Claim</li>

                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                                <small class="text-muted d-block">
                                    Total Estimasi Keseluruhan Claim
                                </small>
                                <h3 class="fw-bold text-primary mb-0"
                                    id="grand-total">

                                    IDR 0
                                </h3>
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
                            Kirim Report atau Pengajuan Claim Business Trip
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
    let mealItems = @json($mealItems);
    let expenseItems = @json($expenseItems);
</script>
<script>
    $(document).ready(function () {
        $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
     if(mealItems.length){
        $('#allowance-wrapper').html('');
        mealItems.forEach(item=>{
            appendMealRow(
                item,
                false
            );
        });
    }
    expenseItems.forEach(item=>{
        appendManualExpenseRow(
            item
        );
    });
    let transportationIndex = 1;

    initPlugins();
    // appendMealRow();
    loadApprover();
    calculateGrandTotal();

    $('#btn-add-expense').click(function(){
        // console.log('clic')
        appendManualExpenseRow();
    });
    $('#btn-add-meal').click(function(){
        // console.log('clic')
        appendMealRow();
    });
    $(document).on('click', '.remove-manual-expense', function () {
        $(this).closest('tr').remove();
        reindexManualExpenses();
        calculateGrandTotal();
    });
    $(document).on('click', '.remove-meal', function () {
        $(this).closest('tr').remove();
        reindexMealAllowances();
        calculateGrandTotal();
    });
    // existingManualExpenses.forEach(item=>{
    //     appendManualExpenseRow(item);
    // });
    $(document).on('input', '.currency-format', function () {
        let value = $(this).val();
        value = value.replace(/\D/g, '');
        value = new Intl.NumberFormat('id-ID').format(value);
        $(this).val(value);
    });
    $(document).on(
        'click',
        '.remove-existing-file',
        function(){
            // capture attachment id (if present) so backend can delete it on save
            const row = $(this).closest('.existing-file-row');
            const existingInput = row.find('.existing-file-input');
            const fileId = existingInput.length ? existingInput.val() : null;
            if (fileId) {
                // append a hidden field to the form to inform backend
                const input = $(`<input type="hidden" name="deleted_existing_files[]" value="${fileId}">`);
                $('#businessReport-Claim').append(input);
            }
            row.remove();
        }
    );
    $(document).on(
        'input',
        '.amount-input, .qty-input',
        function(){

            calculateGrandTotal();

        }
    );

    // ================================== ADD POINT ====================================
    $('#btn-add-point').on('click', function () {
        let total =
            $('#result-point-wrapper .result-point-item').length + 1;
        let html = `
            <div class="input-group mb-3 result-point-item">
                <span class="input-group-text">
                    ${total}
                </span>
                <input type="text"
                    class="form-control"
                    name="result_points[]"
                    placeholder="Masukkan poin hasil perjalanan dinas">
                <button type="button"
                        class="btn btn-danger remove-point">
                    <i class="ri-delete-bin-line"></i>
                </button>
            </div>
        `;
        $('#result-point-wrapper').append(html);
    });
    // REMOVE
    $(document).on('click', '.remove-point', function () {
        $(this)
            .closest('.result-point-item')
            .remove();
        updatePointNumber();
    });

    function toggleTripFieldReadonly(isReadonly)
    {
        $('#trip_type').prop(
            'readonly',
            isReadonly
        );
        $('#arrival_to').prop(
            'readonly',
            isReadonly
        );
        $('#purpose').prop(
            'readonly',
            isReadonly
        );
        $('#start_date').prop(
            'readonly',
            isReadonly
        );
        $('#end_date').prop(
            'readonly',
            isReadonly
        );
        $('#advance_amount').prop(
            'readonly',
            isReadonly
        );
    }

    $('#select-no-document').on('change', function () {
        let id = $(this).val();
        if(!id ){

            toggleTripFieldReadonly(false);
            $('#btn-add-meal').show();
            $('#trip_type').val('domestic');
            $('#start_date').val('');
            $('#end_date').val('');
            $('#total_days').val('');
            $('#advance_amount').val('');
            $('#arrival_to').val('');
            $('#purpose').val('');

            $('#allowance-wrapper').html('');

            $('#manual-expense-wrapper').find('.auto-expense-row').remove();

            calculateGrandTotal();
            return;
        } else {

            $('#btn-add-meal').hide();

        }
        $.ajax({
            url:
            "{{ route('business-trip-report.document-detail',':id') }}"
            .replace(':id',id),
            type:'GET',
            success:function(res){
                $('#trip_type').val(
                    res.trip.trip_type ?? ''
                );
                $('#arrival_to').val(
                    res.trip.arrival_to ?? ''
                );
                $('#purpose').val(
                    res.trip.purpose ?? ''
                );
                $('#start_date').val(
                    res.trip.start_date ?? ''
                );
                $('#end_date').val(
                    res.trip.end_date ?? ''
                );
                // store numeric value only so it submits reliably
                $('#total_days').val(
                    res.trip.total_days ?? ''
                );
                $('#advance_amount').val(
                    formatNumber(res.trip.advance_amount ?? 0)
                );
                toggleTripFieldReadonly(true);
                loadBusinessTripExpense(
                    res.costs
                );
                loadMealAllowance(
                    id
                );
            }
        });
    });

    $(document).on('keyup change','.amount-input, .qty-input, .currency-input',function () {
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

    $(document).on('change','.attachment-input',function(){
        let previewContainer =
            $(this)
            .closest('td')
            .find('.attachment-preview');

        previewContainer.html('');

        Array
        .from(this.files)
        .forEach(file=>{

            let url =
                URL.createObjectURL(
                    file
                );

            previewContainer.append(`

                <div class="small">

                    <a
                        href="${url}"
                        target="_blank">

                        <i class="ri-attachment-2"></i>

                        ${file.name}

                    </a>

                </div>

            `);

        });

    });

    $('#btn-reset').on('click',function(){
        Swal.fire({
            title:'Reset Data?',
            text:'Ini Akan Mereset Tabel Uang Makan dan Biaya Lainnya. Perubahan yang belum disimpan akan hilang ',
            icon:'warning',
            showCancelButton:true,
            confirmButtonText:'Ya',
            cancelButtonText:'Batal'
        }).then(result=>{
            if(!result.isConfirmed){
                return;
            }
            let documentId =
            $('#select-no-document').val();
            // ================= CLEAR =================
            $('#allowance-wrapper').html('');
            $('#manual-expense-wrapper').html('');
            // ================= RELOAD FROM DOCUMENT =================
            if(documentId){
                $.ajax({
                    url:"{{ route('business-trip-report.document-detail',':id') }}".replace(':id',documentId),
                    type:'GET',
                    success:function(res){
                        loadBusinessTripExpense(
                            res.costs
                        );
                        loadMealAllowance(
                            documentId
                        );
                        calculateGrandTotal();
                        // Swal.fire(
                        //     'Success',
                        //     'Data berhasil direset',
                        //     'success'
                        // );
                    }
                });
            }
            else{
                calculateGrandTotal();
                Swal.fire(
                    'Success',
                    'Data berhasil dikosongkan',
                    'success'
                );
            }
        });
    });
    //============================== AJAX SUBMIT ===============================
    $('#businessReport-Claim').on('submit', function (e) {
        e.preventDefault();
        // ensure derived values are up-to-date before building FormData
        updateAllowanceTotal();
        // if (!validateForm()) {
        //     return;
        // }
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
            url: "{{ route('business-trip.claim-approver') }}",
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
   function updatePointNumber() {
        $('#result-point-wrapper .result-point-item').each(function(index){
            $(this)
                .find('.input-group-text')
                .text(index + 1);
        });
    }

    function formatNumber(num)
    {
        return Number(num).toLocaleString('id-ID');
    }

    let mealExpense = 0;
    function appendMealRow(data = {}, isAuto = false)
    {
        let index = getNextMealIndex();

        let readonlyAttr =
            isAuto
            ? 'readonly'
            : '';

        let disabledAttr =
            isAuto
            ? 'disabled'
            : '';

        let attachmentsHtml = '';

        if(data?.attachments?.length){

            attachmentsHtml =
                data.attachments.map(file => {
                    return `
                        <div class="existing-file-row d-flex align-items-center justify-content-between border rounded px-2 py-1 mb-1">

                            <a href="/${file.file_path}"
                                target="_blank"
                                class="small text-decoration-none">

                                ${file.file_name}

                            </a>

                            <button
                                type="button"
                                class="btn btn-sm btn-danger remove-existing-file">

                                <i class="ri-close-line"></i>

                            </button>

                            <input
                                type="hidden"
                                class="existing-file-input"
                                name="allowances[${index}][existing_files][]"
                                value="${file.id}">
                        </div>
                    `;

                }).join('');
        }

        let row = `
            <tr class="allowance-row">

                <td>

                    <div class="input-group">

                        <input
                            type="text"
                            class="form-control start_date"
                            name="allowances[${index}][date]"
                            value="${data?.expense_date ?? data?.date ?? ''}"
                            ${readonlyAttr}
                        >

                        <span class="input-group-text">

                            <i class="ri-calendar-event-line"></i>

                        </span>

                    </div>

                </td>

                <td class="text-center">

                    Meal

                </td>

                <td>

                    <select
                        class="form-select currency-input"
                        name="allowances[${index}][currency]"
                        ${disabledAttr}
                    >

                        <option
                            value="IDR"
                            ${(data?.currency ?? 'IDR') === 'IDR'
                                ? 'selected'
                                : ''}>

                            IDR

                        </option>

                        <option
                            value="USD"
                            ${data?.currency === 'USD'
                                ? 'selected'
                                : ''}>

                            USD

                        </option>

                    </select>

                </td>

                <td>

                    <input
                        type="text"
                        class="form-control amount-input currency-format"
                        name="allowances[${index}][amount]"
                        value="${formatNumber(
                            data?.unit_amount ??
                            data?.amount ??
                            0
                        )}"
                        ${readonlyAttr}
                    >

                </td>

                <td width="30%">

                    <input
                        type="hidden"
                        name="allowances[${index}][item_id]"
                        value="${data?.id ?? ''}"
                    >

                    <input
                        type="file"
                        multiple
                        class="form-control attachment-input"
                        name="allowances[${index}][attachments][]"
                    >
                    <div class="existing-files mb-2">
                        ${attachmentsHtml}
                    </div>

                    <div class="attachment-preview mt-2"></div>

                </td>

                <td class="text-center">

                    <button
                        type="button"
                        class="btn btn-danger remove-meal">

                        <i class="ri-delete-bin-line"></i>

                    </button>

                </td>

            </tr>
        `;

        $('#allowance-wrapper').append(row);

        initPlugins();

        calculateGrandTotal();
    }
    function getNextMealIndex()
    {
        let max = -1;
        $('input[name^="allowances["]').each(function(){
            let match =
                $(this)
                .attr('name')
                .match(/allowances\[(\d+)\]/);
            if(match){
                max = Math.max(
                    max,
                    parseInt(match[1])
                );
            }
        });
        return max + 1;
    }
    function loadMealAllowance(id)
    {
        $('#allowance-wrapper')
            .html('');
        $.ajax({
            url: "{{ route('business-trip-report.meal-data', ':id') }}"
                .replace(':id', id),
            type: 'GET',
            success: function(res){
                res.forEach(item=>{
                    appendMealRow(item,true);
                });
                calculateGrandTotal();
            },
            error:function(xhr){
                console.log(xhr);
            }
        });
    }

    let manualExpenseIndex = 0;
    function appendManualExpenseRow(data = null)
    {
        // Gunakan index yang unik dari semua rows
        let index = getNextManualExpenseIndex();
        let attachmentsHtml = '';
        // console.log(data);
        // console.log(data.unit_amount);
        if (data?.attachments?.length) {
            attachmentsHtml =
                data.attachments.map(file=>{
                    return `
                    <div class="existing-file-row d-flex align-items-center justify-content-between border rounded px-2 py-1 mb-1">

                        <a
                            href="/${file.file_path}"
                            target="_blank"
                            class="small text-decoration-none">

                            ${file.file_name}

                        </a>

                        <button
                            type="button"
                            class="btn btn-sm btn-danger remove-existing-file">

                            <i class="ri-close-line"></i>

                        </button>

                        <input
                            type="hidden"
                            class="existing-file-input"
                            name="manual_expenses[${index}][existing_files][]"
                            value="${file.id}">

                    </div>

                    `;

                }).join('');
        }
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

                        <option value="USD"
                            ${data?.currency=='USD'?'selected':''}>
                            USD
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
                        IDR ${
                            formatNumber(

                                data?.unit_total
                                ??

                                (
                                    (
                                        data?.amount
                                        ??
                                        data?.unit_amount
                                        ??
                                        0
                                    )

                                    *

                                    (
                                        data?.qty
                                        ??
                                        1
                                    )

                                )

                            )
                        }
                    </div>
                </td>

                <td width="20%">
                    <input
                        type="hidden"
                        name="manual_expenses[${index}][item_id]"
                        value="${data?.id ?? ''}"
                    >
                    <input type="file"
                        multiple
                        class="form-control attachment-input"
                        name="manual_expenses[${index}][attachments][]">

                        <div class="attachment-preview mt-2"> </div>
                        <div class="existing-files mb-2">
                            ${attachmentsHtml}
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
        // manualExpenseIndex++;
        reindexManualExpenses();
    }
    let autoExpenseIndex = 0;
    function appendAutoExpenseRow(data = null)
    {
        let index = getNextManualExpenseIndex();

        let attachmentsHtml = '';

        if(data?.attachments?.length){
            attachmentsHtml =
                data.attachments.map(file=>{
                    return `
                    <div class="existing-file-row d-flex align-items-center justify-content-between border rounded px-2 py-1 mb-1">

                        <a
                            href="/${file.file_path}"
                            target="_blank"
                            class="small text-decoration-none">

                            ${file.file_name}

                        </a>

                        <button
                            type="button"
                            class="btn btn-sm btn-danger remove-existing-file">

                            <i class="ri-close-line"></i>

                        </button>

                        <input
                            type="hidden"
                            class="existing-file-input"
                            name="manual_expenses[${index}][existing_files][]"
                            value="${file.id}">

                    </div>

                    `;

                }).join('');
        }

        let row = `

        <tr class="auto-expense-row">

            <td>

                <select
                    class="form-select manual-category"
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

                <select
                    class="form-select manual-currency currency-input"
                    name="manual_expenses[${index}][currency]">

                    <option
                        value="IDR"
                        ${(data?.currency ?? 'IDR')=='IDR'
                            ? 'selected'
                            : ''}>

                        IDR

                    </option>

                    <option
                        value="USD"
                        ${data?.currency=='USD'
                            ? 'selected'
                            : ''}>

                        USD

                    </option>

                </select>

            </td>

            <td>

                <div class="input-group">

                    <span class="input-group-text">

                        Rp

                    </span>

                    <input
                        type="text"
                        class="form-control amount-input currency-format"
                        name="manual_expenses[${index}][amount]"
                        value="${formatNumber(
                            data?.amount ??
                            data?.unit_amount ??
                            0
                        )}"
                        readonly>

                </div>

            </td>

            <td>

                <input
                    type="number"
                    class="form-control qty-input"
                    name="manual_expenses[${index}][qty]"
                    value="${data?.qty ?? 1}"
                    min="1">

            </td>

            <td>

                <div class="fw-bold total-text">

                    IDR
                    ${formatNumber(
                        data?.unit_total ??
                        (
                            (data?.amount ??
                            data?.unit_amount ??
                            0)
                            *
                            (data?.qty ?? 1)
                        )
                    )}

                </div>

            </td>

            <td width="20%">

                <input
                    type="hidden"
                    name="manual_expenses[${index}][item_id]"
                    value="${data?.id ?? ''}"
                >

                <div class="existing-files mb-2">

                    ${attachmentsHtml}

                </div>

                <input
                    type="file"
                    multiple
                    class="form-control attachment-input"
                    name="manual_expenses[${index}][attachments][]">

                <div class="attachment-preview mt-2"></div>

            </td>

            <td class="text-center">

                <button
                    type="button"
                    class="btn btn-danger remove-manual-expense">

                    <i class="ri-delete-bin-line"></i>

                </button>

            </td>

        </tr>

        `;

        $('#manual-expense-wrapper').append(row);

        calculateGrandTotal();
    }

    function loadBusinessTripExpense(costs)
    {
        // hapus hanya auto expense
        $('#manual-expense-wrapper').find('.auto-expense-row').remove();
        costs.forEach(item => {
            appendAutoExpenseRow({
                category : item.category,
                notes : item.notes,
                currency : item.currency,
                amount : item.unit_amount,
                qty : item.qty,
                id : item.id,
                attachments: item.attachments ?? []
            });
        });
        calculateGrandTotal();
    }

    function calculateGrandTotal()
    {
        let mealTotal = 0;
        let manualTotal = 0;
        // ================= MEAL =================
        $('#allowance-wrapper tr').each(function(){
            let amount =
                parseCurrency(
                    $(this).find('.amount-input').val()
                ) || 0;
            mealTotal += amount;
        });
        // ================= MANUAL EXPENSE =================
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
        let grandTotal =
            mealTotal +
            manualTotal;
        // ================= UPDATE UI =================
        $('#meal-total').html(
            'IDR ' +
            new Intl.NumberFormat(
                'id-ID'
            ).format(
                mealTotal
            )
        );
        $('#grand-total').html(
            'IDR ' +
            new Intl.NumberFormat(
                'id-ID'
            ).format(
                grandTotal
            )
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

    function reindexMealAllowances() {
        $('#allowance-wrapper tr.allowance-row').each(function(index) {
            $(this).find('input[name^="allowances"]').each(function() {
                let name = $(this).attr('name');
                name = name.replace(/allowances\[\d+\]/, `allowances[${index}]`);
                $(this).attr('name', name);
            });
            $(this).find('select[name^="allowances"]').each(function() {
                let name = $(this).attr('name');
                name = name.replace(/allowances\[\d+\]/, `allowances[${index}]`);
                $(this).attr('name', name);
            });
        });
    }

    function getNextManualExpenseIndex()
    {
        let max = -1;

        $('#manual-expense-wrapper tr').each(function(){

            let input =
                $(this)
                    .find(
                        'input[name*="[item_id]"]'
                    )
                    .attr('name');

            if(!input) return;

            let match =
                input.match(
                    /manual_expenses\[(\d+)\]/
                );

            if(match){

                max =
                    Math.max(
                        max,
                        parseInt(match[1])
                    );

            }

        });

        return max + 1;
    }

    function reindexManualExpenses() {
        // Re-index SEMUA rows (auto + manual) berdasarkan urutan DOM
        let index = 0;
        $('#manual-expense-wrapper tr.auto-expense-row, #manual-expense-wrapper tr.manual-expense-row').each(function() {
            $(this).find('input[name^="manual_expenses"]').each(function() {
                let name = $(this).attr('name');
                name = name.replace(/manual_expenses\[\d+\]/, `manual_expenses[${index}]`);
                $(this).attr('name', name);
            });
            $(this).find('select[name^="manual_expenses"]').each(function() {
                let name = $(this).attr('name');
                name = name.replace(/manual_expenses\[\d+\]/, `manual_expenses[${index}]`);
                $(this).attr('name', name);
            });
            $(this).find('textarea[name^="manual_expenses"]').each(function() {
                let name = $(this).attr('name');
                name = name.replace(/manual_expenses\[\d+\]/, `manual_expenses[${index}]`);
                $(this).attr('name', name);
            });
            index++;
        });
    }

</script>
@endsection
