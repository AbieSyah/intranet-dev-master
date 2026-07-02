@extends('layouts.simple')
@section('link')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="/assets/libs/select2/select2.min.css">
    <link rel="stylesheet" href="/assets/libs/sweetalert2/sweetalert2.min.css">

    <style>
        label {
            font-weight: bold;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <form action="{{ route('guest.form-save') }}" method="POST">
            @csrf
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header text-center">
                        <h2>Form Kunjungan Tamu PT Hisamitsu Pharma Indonesia</h2>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="nama">Nama Tamu <i>(Name of Visitor)</i> <i style="color: red">
                                            *</i></label>
                                    <input type="text" class="form-control" id="nama" name="nama"
                                        style="text-transform:uppercase" required="">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="company">Nama Perusahaan <i>(Company Name)</i> <i style="color: red">
                                            *</i></label>
                                    <input type="text" class="form-control" id="company" name="company"
                                        style="text-transform:uppercase" required="">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="purpose">Tujuan Kunjungan <i>(Visit Purpose)</i> <i style="color: red">
                                            *</i></label>
                                    <input type="text" class="form-control" id="purpose" name="purpose"
                                        style="text-transform:uppercase" required="">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="emp">Bertemu dengan? <i>(Meeting with?)</i> <i style="color: red">
                                            *</i></label>
                                    <input type="text" class="form-control" id="emp" name="emp" required="">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="est">Estimasi Lama Pertemuan? <i>(Estimated meeting time?)</i> <i
                                            style="color: red"> *</i></label>
                                    <input type="text" class="form-control" id="est" name="est" required="">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="suhu">Suhu Tubuh <i>(Body Temperature)</i> <i style="color: red">
                                            *</i></label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="suhu" name="suhu"
                                            step="0.1" required="">
                                        <span class="input-group-text"><sup>o</sup>C</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6>Dalam satu minggu terakhir suhu badan ≥ 37,5<sup>o</sup>C <i style="color: red"> *</i>
                                </h6>
                                <h6 class="sub-title"><i>In the past 1 week, the body temperature ≥ 37,5<sup>o</sup>C</i> <i
                                        style="color: red"> *</i></h6>
                            </div>
                            <div class="card-body">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="q1" id="q1Yes"
                                        value="1" required>
                                    <label class="form-check-label" for="q1Yes">Yes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="q1" id="q1No"
                                        value="0">
                                    <label class="form-check-label" for="q1No">No</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6>Apakah saat ini sedang batuk/pilek/nyeri tenggorokan? <i style="color: red"> *</i></h6>
                                <h6 class="sub-title"><i>Are you currently experiencing cough/flu/sore throat?</i> <i
                                        style="color: red"> *</i></h6>
                            </div>
                            <div class="card-body">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="q2" id="q2Yes"
                                        value="1" required>
                                    <label class="form-check-label" for="q2Yes">Yes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="q2" id="q2No"
                                        value="0">
                                    <label class="form-check-label" for="q2No">No</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-lg-6 col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6>Apakah saat ini sedang pneumonia (sesak nafas) ringan hingga berat? <i
                                        style="color: red">
                                        *</i></h6>
                                <h6 class="sub-title"><i>Are you currently suffering from pneumonia?</i> <i
                                        style="color: red"> *</i></h6>
                            </div>
                            <div class="card-body">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="q3" id="q3Yes"
                                        value="1" required>
                                    <label class="form-check-label" for="q3Yes">Yes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="q3" id="q3No"
                                        value="0">
                                    <label class="form-check-label" for="q3No">No</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6>Apakah dalam 14 hari terakhir memiliki riwayat perjalanan ke negara/wilayah terjangkit
                                    virus
                                    corona? <i style="color: red"> *</i></h6>
                                <h6 class="sub-title"><i>In the last 14 days, have you traveled to a country/region
                                        affected by
                                        the coronavirus?</i> <i style="color: red"> *</i></h6>
                            </div>
                            <div class="card-body">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="q4" id="q4Yes"
                                        value="1" required>
                                    <label class="form-check-label" for="q4Yes">Yes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="q4" id="q4No"
                                        value="0">
                                    <label class="form-check-label" for="q4No">No</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-lg-6 col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6>Apakah dalam 14 hari terakhir mengikuti seminar/workshop/pertemuan dengan banyak orang?
                                    <i style="color: red"> *</i>
                                </h6>
                                <h6 class="sub-title"><i>In the last 14 days, have you attended a seminar/workshop/meeting
                                        with
                                        many people?</i> <i style="color: red"> *</i></h6>
                            </div>
                            <div class="card-body">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="q5" id="q5Yes"
                                        value="1" required>
                                    <label class="form-check-label" for="q5Yes">Yes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="q5" id="q5No"
                                        value="0">
                                    <label class="form-check-label" for="q5No">No</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6>Apakah memiliki kontak langsung dengan keluarga/kerabat dengan kasus corona
                                    terkonfirmasi?
                                    <i style="color: red"> *</i>
                                </h6>
                                <h6 class="sub-title"><i>Do you have direct contact with family/relatives with confirmed
                                        corona
                                        cases?</i> <i style="color: red"> *</i></h6>
                            </div>
                            <div class="card-body">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="q6" id="q6Yes"
                                        value="1" required>
                                    <label class="form-check-label" for="q6Yes">Yes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="q6" id="q6No"
                                        value="0">
                                    <label class="form-check-label" for="q6No">No</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12 mx-auto">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="title">PERATURAN BAGI VISITOR/ TAMU SAAT MEMASUKI PT HISAMITSU PHARMA INDONESIA
                                </h4>
                            </div>
                            <div class="card-body py-2">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <tbody>
                                            <tr>
                                                <td class="text-wrap">Menyerahkan KTP dan mengambil kartu visitor di Pos
                                                    Security</td>
                                                <td class="text-center" style="width: 30%"><img
                                                        src="{{ asset('assets/images/security/tamu1.pn') }}g"
                                                        class="img-fluid" width="80" height="60"></td>
                                            </tr>
                                            <tr>
                                                <td class="text-wrap">Parkir kendaraan pada tempat parkir yang telah
                                                    disediakan
                                                </td>
                                                <td class="text-center"><img
                                                        src="{{ asset('assets/images/security/tamu2.jpg') }}"
                                                        class="img-fluid" width="80" height="60"></td>
                                            </tr>
                                            <tr>
                                                <td class="text-wrap">Menggunakan APD (Alat Pelindung Diri)</td>
                                                <td class="text-center"><img
                                                        src="{{ asset('assets/images/security/tamu3.jpg') }}"
                                                        class="img-fluid" width="80" height="60"></td>
                                            </tr>
                                            <tr>
                                                <td class="text-wrap">Menjaga kebersihan dan membuang sampah pada tempat
                                                    sampah
                                                    yang telah disediakan</td>
                                                <td class="text-center"><img
                                                        src="{{ asset('assets/images/security/tamu4.png') }}"
                                                        class="img-fluid" width="80" height="60"></td>
                                            </tr>
                                            <tr>
                                                <td class="text-wrap">Area terbatas merokok, merokok hanya di Smoking Area
                                                    yang
                                                    telah disediakan</td>
                                                <td class="text-center"><img
                                                        src="{{ asset('assets/images/security/tamu5.png') }}"
                                                        class="img-fluid" width="80" height="60"></td>
                                            </tr>
                                            <tr>
                                                <td class="text-wrap">Dalam keadaan sehat dan tidak terpengaruh obat-obatan
                                                    terlarang serta alkohol</td>
                                                <td class="text-center"><img
                                                        src="{{ asset('assets/images/security/tamu6.png') }}"
                                                        class="img-fluid" width="80" height="60"></td>
                                            </tr>
                                            <tr>
                                                <td class="text-wrap">Menggunakan jalur pedestrian bagi pejalan kaki</td>
                                                <td class="text-center"><img
                                                        src="{{ asset('assets/images/security/tamu7.png') }}"
                                                        class="img-fluid" width="80" height="60"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-lg-12 mx-auto">
                        <div class="card">
                            <div class="card-body">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="deklarasi3" required>
                                    <label class="form-check-label" for="deklarasi3">
                                        Saya telah membaca dan memahami peraturan tamu saat memasuki area PT Hisamitsu
                                        Pharma
                                        Indonesia <br>
                                        <i>have read and understand the safety induction when entering to PT Hisamitsu
                                            Pharma
                                            Indonesia</i>
                                    </label>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="deklarasi1" required>
                                    <label class="form-check-label" for="deklarasi1">
                                        Formulir ini telah saya jawab dengan sebenar-benarnya <br>
                                        <i>I have answered this form truthfully</i>
                                    </label>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="deklarasi2" required>
                                    <label class="form-check-label" for="deklarasi2">
                                        Saya memberikan persetujuan kepada PT Hisamitsu Pharma Indonesia untuk kepentingan
                                        pelacakan terkait COVID-19 <br>
                                        <i>Hereby I give permission to PT Hisamitsu Pharma Indonesia to manage my
                                            information
                                            for tracing purpose of COVID-19</i>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3 mb-5">
                    <div class="col-lg-12 mx-auto">
                        <button type="submit" class="btn btn-primary w-100">Kirim</button>
                    </div>
                </div>
            </div>
        </form>

    </div>
@endsection
@section('script')
    <script src="/assets/libs/sweetalert2/sweetalert2.min.js"></script>
@endsection
@section('javascript')
    <script>
        $("form").submit(function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            Swal.fire({
                title: 'Apakah yakin data yang diisikan sudah benar?',
                icon: 'info',
                showCancelButton: true,
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#405189',
                confirmButtonText: 'Yes',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return $.ajax({
                        url: $(this).attr("action"),
                        method: "POST",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            return response;
                        },
                        error: function(response) {
                            let errors = response.responseJSON?.validator;
                            let errorMessage = response.responseJSON?.message ||
                                'An error occurred';

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
                    Swal.fire({
                        title: 'Pengisian Form Berhasil!',
                        text: 'Silahkan tutup halaman ini',
                        icon: 'success',
                        confirmButtonText: 'OK',
                        allowOutsideClick: false,
                        showConfirmButton: false
                    });
                }
            });
        });
    </script>
@endsection
