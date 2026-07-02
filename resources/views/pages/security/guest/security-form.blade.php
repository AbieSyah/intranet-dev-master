@extends('layouts.master')
@section('link')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="/assets/libs/select2/select2.min.css">
    <link rel="stylesheet" href="/assets/libs/sweetalert2/sweetalert2.min.css">

    </style>
@endsection

@section('content')
    <x-page-title title="Guest Form" :breadcrumbs="['Employee', 'Security Form']" />


    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex justify-content-between">
                    <h3>Form Tamu Baru</h3>

                    <a href="{{ route('guest.index') }}"
                        class="float-end btn btn-primary btn-label waves-effect waves-light"><i
                            class="mdi mdi-arrow-left label-icon align-middle fs-16 me-2"> </i>Back</a>
                </div>
                <div class="card-body">
                    <form id="security_tamu" class="form-horizontal" enctype="multipart/form-data" method="post"
                        action="{{ $guestForm ? route('guest.security-form-store', encrypt($guestForm->id)) : route('guest.security-form-store') }}">

                        @csrf
                        @method('POST')

                        <div class="row">
                            <div class="col-md-4 p-2">
                                <label for="tanggal">Tanggal Kunjungan</label>
                                <input type="date" class="form-control bg-body" id="tanggal" name="tanggal" readonly
                                    value="{{ old('tanggal', isset($guestForm->created_at) ? \Carbon\Carbon::parse($guestForm->created_at)->format('Y-m-d') : now()->format('Y-m-d')) }}">
                            </div>

                            <div class="col-md-4 p-2">
                                <label for="nama">Nama Tamu</label>
                                <input type="text" class="form-control" id="nama" name="nama"
                                    style="text-transform:uppercase" required
                                    value="{{ old('nama', $guestForm->nama ?? '') }}">
                            </div>
                            <div class="col-md-4 p-2">
                                <label for="alamat_pribadi">Alamat Pribadi</label>
                                <input type="text" class="form-control" id="alamat_pribadi" name="alamat_pribadi"
                                    required value="{{ old('alamat_pribadi', $guestForm->alamat_pribadi ?? '') }}">
                            </div>
                            <div class="col-md-4 p-2">
                                <label for="nomor_kartu_identitas">Nomor Kartu Identitas</label>
                                <input type="text" class="form-control" id="nomor_kartu_identitas"
                                    name="nomor_kartu_identitas" required
                                    value="{{ old('nomor_kartu_identitas', $guestForm->nomor_kartu_identitas ?? '') }}">
                            </div>
                            <div class="col-md-4 p-2">
                                <label for="perusahaan">Nama Perusahaan</label>
                                <input type="text" class="form-control" id="perusahaan" name="perusahaan" required
                                    value="{{ old('perusahaan', $guestForm->perusahaan ?? '') }}">
                            </div>

                            <div class="col-md-4 p-2">
                                <label for="tujuan_kunjungan">Tujuan Kunjungan</label>
                                <input type="text" class="form-control" id="tujuan_kunjungan" name="tujuan_kunjungan"
                                    required value="{{ old('tujuan_kunjungan', $guestForm->tujuan_kunjungan ?? '') }}">
                            </div>
                            <div class="col-md-4 p-2">
                                <label for="lama_kunjungan">Lama Kunjungan</label>
                                <input type="text" class="form-control" id="lama_kunjungan" name="lama_kunjungan"
                                    required value="{{ old('lama_kunjungan', $guestForm->lama_kunjungan ?? '') }}">
                            </div>
                            <div class="col-md-4 p-2">
                                <label for="id_employee" id="label-pic">PIC Tujuan</label>
                                <select name="id_employee" id="id_employee" class="form-control select2"
                                    data-placeholder="--Pilih Nama Karyawan--">
                                    <option selected disabled></option>
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}"
                                            {{ old('id_emp', $guestForm->id_employee ?? '') == $employee->id ? 'selected' : '' }}
                                            data-name="{{ $employee->fullname }}">
                                            {{ $employee->fullname }} || {{ $employee->Department->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="text-muted">{{ $guestForm?->nama_pic }}</span>
                            </div>
                            <div class="col-md-4 p-2">
                                <label for="suhu">Suhu Badan</label>
                                <input type="number" step="0.1" class="form-control" id="suhu" name="suhu"
                                    required value="{{ old('suhu', $guestForm->suhu ?? '') }}">
                            </div>
                            <div class="col-md-4 p-2">
                                <label for="resiko_kesehatan" id="label-pic">Resiko Kesehatan</label>
                                <select name="resiko_kesehatan" id="resiko_kesehatan" class="form-control select2"
                                    data-placeholder="Resiko Kesehatan">
                                    <option selected disabled></option>
                                    <option
                                        {{ old('resiko_kesehatan', $guestForm->resiko_kesehatan ?? '') == 'rendah' ? 'selected' : '' }}>
                                        rendah</option>
                                    <option
                                        {{ old('resiko_kesehatan', $guestForm->resiko_kesehatan ?? '') == 'sedang' ? 'selected' : '' }}>
                                        sedang</option>
                                    <option
                                        {{ old('resiko_kesehatan', $guestForm->resiko_kesehatan ?? '') == 'tinggi' ? 'selected' : '' }}>
                                        tinggi</option>
                                </select>
                            </div>
                            <div class="col-md-4 p-2">
                                <label for="nomor_visitor">No Visitor</label>
                                <input type="text" class="form-control" id="nomor_visitor" name="nomor_visitor"
                                    style="text-transform:uppercase"
                                    value="{{ old('nomor_visitor', $guestForm->nomor_visitor ?? '') }}">
                            </div>
                            <div class="col-md-4 p-2">
                                <label for="jenis_kendaraan">Kendaraan</label>
                                <input type="text" class="form-control" id="jenis_kendaraan" name="jenis_kendaraan"
                                    value="{{ old('jenis_kendaraan', $guestForm->jenis_kendaraan ?? '') }}">
                            </div>
                            <div class="col-md-4 p-2">
                                <label for="nomor_polisi">Nopol</label>
                                <input type="text" class="form-control" id="nomor_polisi" name="nomor_polisi"
                                    value="{{ old('nomor_polisi', $guestForm->nomor_polisi ?? '') }}">
                            </div>
                            <div class="col-md-8 p-2">
                                <label for="muatan_kendaraan">Barang yang dibawa keluar / masuk</label>
                                <input type="text" class="form-control" id="muatan_kendaraan" name="muatan_kendaraan"
                                    value="{{ old('muatan_kendaraan', $guestForm->muatan_kendaraan ?? '') }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 p-2">
                                <label for="photo">Foto Kartu Identitas</label>
                                <button id="open-webcam-btn" type="button" onclick="openWebcam()"
                                    class="btn btn-primary w-100">Buka Webcam</button>
                                <div id="my_camera" style="display: none;" class="mt-2"></div>
                                <input type="button" value="Take Foto" onClick="takeSnapshot()"
                                    class="btn btn-primary mt-2 w-100">
                                <input type="hidden" name="photo64" class="image-tag"
                                    value="{{ old('photo64', $guestForm->photo64 ?? '') }}">
                            </div>
                            <div class="col-md-6 p-2">
                                <h6>Tangkapan Webcam akan ditampilkan disini</h6>
                                <div id="results">
                                    @if ($guestForm)
                                        @php
                                            $filePath = 'tamu/' . $guestForm->id . '.jpg'; // Path to the file
                                        @endphp

                                        @if (Storage::disk('public')->exists($filePath))
                                            <img src="{{ Storage::disk('public')->url($filePath) }}"
                                                style="width: 100%;" />
                                        @else
                                            <p>File does not exist.</p>
                                        @endif
                                    @endif

                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="float-end btn btn-primary btn-label waves-effect waves-light">
                                <i class="mdi mdi-content-save-check label-icon align-middle fs-16 me-2"></i>Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="/assets/libs/sweetalert2/sweetalert2.min.js"></script>
    <script src="/assets/libs/select2/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.25/webcam.min.js"></script>
@endsection
@section('javascript')
    <script>
        $(document).ready(function() {
            $('.select2').select2();
            // $('#id_employee').on('change', function() {
            //     var selectedIdEmp = $(this).val(); // Get the selected id_emp
            //     var selectedEmpName = $('option:selected', this).data(
            //         'name'); // Get the data-empname attribute

            //     $('#nama_pic').val(selectedEmpName);
            // });

            $("form").submit(function(e) {
                e.preventDefault();

                const formData = new FormData(this);

                Swal.fire({
                    title: `Apakah anda yakin untuk menyimpan data?`,
                    icon: 'info',
                    showCancelButton: true,
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#405189',
                    confirmButtonText: 'Save',
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        return $.ajax({
                            url: $(this).attr("action"),
                            method: "POST",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                                    'content')
                            },
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                return response;
                            },
                            error: function(response) {
                                let errors = response.responseJSON?.validator;
                                let errorMessage = response.responseJSON?.message;

                                if (errors) {
                                    // Construct the error message
                                    errorMessage += '<ul>';
                                    errors.forEach(error => {
                                        errorMessage += `<li>${error}</li>`;
                                    });
                                    errorMessage += '</ul>';
                                }

                                Swal.fire('Error!', errorMessage, 'error');
                            }
                        });
                    },
                    allowOutsideClick: () => !Swal.isLoading(),
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire('Success!', result.value.message, 'success').then(() => {
                            location.href = result.value.redirect;
                        });
                    }
                });
            });
        });

        var isWebcamOpen = false;

        function openWebcam() {
            if (!isWebcamOpen) {
                // Initialize WebcamJS when the "Buka Webcam" button is clicked
                Webcam.set({
                    width: 490,
                    height: 390,
                    image_format: 'jpeg',
                    jpeg_quality: 100
                });

                Webcam.attach('#my_camera');
                isWebcamOpen = true;
                document.getElementById('my_camera').style.display = 'block';
                document.getElementById('open-webcam-btn').innerText = 'Tutup Webcam';
            } else {
                // Close the webcam if it's already open
                Webcam.reset();
                isWebcamOpen = false;
                document.getElementById('my_camera').style.display = 'none';
                document.getElementById('open-webcam-btn').innerText = 'Buka Webcam';
            }
        }

        function takeSnapshot() {
            if (isWebcamOpen) {
                Webcam.snap(function(data_uri) {
                    $(".image-tag").val(data_uri);
                    document.getElementById('results').innerHTML = '<img src="' + data_uri +
                        '" style="width: 100%"/>';
                });
            } else {
                alert("Buka Webcam terlebih dahulu.");
            }
        }
    </script>
@endsection
