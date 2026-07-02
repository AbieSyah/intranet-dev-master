<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.2/dist/sweetalert2.min.css"
    integrity="sha256-XE4NT4UAtULuSdFWQXaaLSOt0/ZqL5xbX/ObUyf2UTI=" crossorigin="anonymous">

  <title>Form Kunjungan Tamu PT Hisamitsu Pharma Indonesia</title>

  <style>
    body,
    html {
      height: 100%;
      margin: 0;
      background: gainsboro;
    }

    .page-content {
      padding: calc(20px + 1.5rem) calc(1.5rem * .5) 60px calc(1.5rem * .5);
      min-height: calc(100vh - 60px);
      max-width: 1000px;
      margin: 0 auto;
    }

    @media (max-width: 576px) {
      .page-content {
        padding: calc(20px + 1.5rem) 0 60px 0;
      }
    }

    .footer {
      width: 100%;
      background-color: #f8f9fa;
      padding: 1rem 0;
      position: fixed;
      bottom: 0;
      left: 0 !important;
    }

    .footer .container-fluid {
      max-width: 1000px;
      margin: 0 auto;
      text-align: between;
    }

    .footer .row {
      margin: 0;
    }

    .footer .col-sm-6 {
      margin-bottom: 0.5rem;
    }

    .footer .text-sm-end {
      text-align: center !important;
      /* Center text on smaller screens */
    }

    label {
      font-weight: bold;
    }
  </style>
</head>

<body>

  <div id="layout-wrapper">
    <div class="page-content">
      <div class="container-fluid">
        <div class="row mt-3">
          <form action="https://intranet.hisamitsu.co.id/form-kunjungan-tamu" method="POST">
            @csrf
            <div class="col-lg-12">
              <div class="card">
                <div class="card-header text-center">
                  <h2>Form Kunjungan Tamu PT Hisamitsu Pharma Indonesia</h2>
                </div>
                <div class="card-body">
                  <div class="row mt-3">
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
                        <label for="est">Estimasi Lama Pertemuan? <i>(Estimated meeting
                            time?)</i> <i style="color: red"> *</i></label>
                        <input type="text" class="form-control" id="est" name="est" required="">
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="row mt-3">
                <div class="col-lg-6 col-md-6">
                  <div class="card">
                    <div class="card-header">
                      <h6>Dalam satu minggu terakhir suhu badan ≥ 37,5<sup>o</sup>C <i style="color: red">
                          *</i>
                      </h6>
                      <h6 class="sub-title"><i>In the past 1 week, the body temperature ≥
                          37,5<sup>o</sup>C</i> <i style="color: red"> *</i></h6>
                    </div>
                    <div class="card-body">
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="q1" id="q1Yes" value="1"
                          required>
                        <label class="form-check-label" for="q1Yes">Yes</label>
                      </div>
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="q1" id="q1No" value="0">
                        <label class="form-check-label" for="q1No">No</label>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-lg-6 col-md-6">
                  <div class="card">
                    <div class="card-header">
                      <h6>Apakah saat ini sedang batuk/pilek/nyeri tenggorokan? <i style="color: red"> *</i>
                      </h6>
                      <h6 class="sub-title"><i>Are you currently experiencing cough/flu/sore
                          throat?</i> <i style="color: red"> *</i></h6>
                    </div>
                    <div class="card-body">
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="q2" id="q2Yes" value="1"
                          required>
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
                      <h6>Apakah saat ini sedang pneumonia (sesak nafas) ringan hingga berat? <i style="color: red">
                          *</i></h6>
                      <h6 class="sub-title"><i>Are you currently suffering from pneumonia?</i> <i style="color: red">
                          *</i></h6>
                    </div>
                    <div class="card-body">
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="q3" id="q3Yes" value="1"
                          required>
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
                      <h6>Apakah dalam 14 hari terakhir memiliki riwayat perjalanan ke
                        negara/wilayah
                        terjangkit
                        virus
                        corona? <i style="color: red"> *</i></h6>
                      <h6 class="sub-title"><i>In the last 14 days, have you traveled to a
                          country/region
                          affected by
                          the coronavirus?</i> <i style="color: red"> *</i></h6>
                    </div>
                    <div class="card-body">
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="q4" id="q4Yes" value="1"
                          required>
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
                      <h6>Apakah dalam 14 hari terakhir mengikuti seminar/workshop/pertemuan
                        dengan banyak
                        orang?
                        <i style="color: red"> *</i>
                      </h6>
                      <h6 class="sub-title"><i>In the last 14 days, have you attended a
                          seminar/workshop/meeting
                          with
                          many people?</i> <i style="color: red"> *</i></h6>
                    </div>
                    <div class="card-body">
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="q5" id="q5Yes" value="1"
                          required>
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
                      <h6>Apakah memiliki kontak langsung dengan keluarga/kerabat dengan kasus
                        corona
                        terkonfirmasi?
                        <i style="color: red"> *</i>
                      </h6>
                      <h6 class="sub-title"><i>Do you have direct contact with family/relatives
                          with
                          confirmed
                          corona
                          cases?</i> <i style="color: red"> *</i></h6>
                    </div>
                    <div class="card-body">
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="q6" id="q6Yes" value="1"
                          required>
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

              <div class="row mt-3">
                <div class="col-lg-12 mx-auto">
                  <div class="card">
                    <div class="card-header">
                      <h4 class="title">PERATURAN BAGI VISITOR/ TAMU SAAT MEMASUKI PT HISAMITSU
                        PHARMA
                        INDONESIA
                      </h4>
                    </div>
                    <div class="card-body py-2">
                      <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                          <tbody>
                            <tr>
                              <td class="text-wrap">
                                Menunjukkan asli KTP/ Identitas diri untuk dapat dicatat<br>
                                <i>Provide original ID card / personal identity to be recorded</i>
                              </td>
                              <td class="text-center" style="width: 30%">
                                <img src="{{ asset('assets/images/security/tamu1.png') }}" class="img-fluid"
                                  width="80" height="60">
                              </td>
                            </tr>
                            <tr>
                              <td class="text-wrap">
                                Parkir kendaraan pada tempat parkir yang telah disediakan<br>
                                <i>Park the vehicle in the parking lot provided</i>
                              </td>
                              <td class="text-center" style="width: 30%">
                                <img src="{{ asset('assets/images/security/tamu2.jpg') }}" class="img-fluid"
                                  width="80" height="60">
                              </td>
                            </tr>
                            <tr>
                              <td class="text-wrap">
                                Menggunakan Alat Pelindung Diri (APD) untuk area tertentu<br>
                                <i>Using Personal Protective Equipment (PPE) for specific areas</i>
                              </td>
                              <td class="text-center" style="width: 30%">
                                <img src="{{ asset('assets/images/security/tamu3.jpg') }}" class="img-fluid"
                                  width="80" height="60">
                              </td>
                            </tr>
                            <tr>
                              <td class="text-wrap">
                                Menjaga kebersihan dan membuang sampah pada tempat sampah yang telah disediakan<br>
                                <i>Maintain cleanliness and dispose of garbage in the trash bins that have been
                                  provided</i>
                              </td>
                              <td class="text-center" style="width: 30%">
                                <img src="{{ asset('assets/images/security/tamu4.png') }}" class="img-fluid"
                                  width="80" height="60">
                              </td>
                            </tr>
                            <tr>
                              <td class="text-wrap">
                                Tidak merokok diluar area merokok yang ditentukan<br>
                                <i>No smoking outside the designated smoking area</i>
                              </td>
                              <td class="text-center" style="width: 30%">
                                <img src="{{ asset('assets/images/security/tamu5.png') }}" class="img-fluid"
                                  width="80" height="60">
                              </td>
                            </tr>
                            <tr>
                              <td class="text-wrap">
                                Dalam keadaan sehat dan tidak terpengaruh obat-obatan terlarang serta alkohol<br>
                                <i>In good health and unaffected by drugs and alcohol</i>
                              </td>
                              <td class="text-center" style="width: 30%">
                                <img src="{{ asset('assets/images/security/tamu6.png') }}" class="img-fluid"
                                  width="80" height="60">
                              </td>
                            </tr>
                            <tr>
                              <td class="text-wrap">
                                Menggunakan jalur pedestrian bagi pejalan kaki<br>
                                <i>Using pedestrian paths for pedestrians</i>
                              </td>
                              <td class="text-center" style="width: 30%">
                                <img src="{{ asset('assets/images/security/tamu7.png') }}" class="img-fluid"
                                  width="80" height="60">
                              </td>
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
                          Saya telah membaca dan memahami peraturan tamu saat memasuki area PT
                          Hisamitsu
                          Pharma
                          Indonesia <br>
                          <i>have read and understand the safety induction when entering to PT
                            Hisamitsu
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
                          Saya memberikan persetujuan kepada PT Hisamitsu Pharma Indonesia untuk mengelola data dan
                          dokumen pribadi saya untuk kepentingan pengelolaan data tamu sesuai dengan kebijakan yang
                          berlaku<br>
                          <i>I provide consent to PT Hisamitsu Pharma Indonesia to manage my personal data and documents
                            for the purpose of guest data management in accordance with applicable policies.</i>
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
      </div>
    </div>
    <footer class="footer">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6">
            <script>
              document.write(new Date().getFullYear())
            </script>© Form Kunjungan Tamu.
          </div>
          <div class="col-sm-6 text-sm-end">
            Hisamitsu Pharma Indonesia
          </div>
        </div>
      </div>
    </footer>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.2/dist/sweetalert2.all.min.js"
    integrity="sha256-4HdbDegPFqVsJaRNvgpTveEgxxl4KHtvqtkZeVsJNI4=" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.querySelectorAll("form").forEach((form) => {
      form.addEventListener("submit", function(e) {
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
            return fetch(this.action, {
                method: "POST",
                body: formData
              })
              .then(response => {
                if (!response.ok) {
                  return response.json().then(res => {
                    const errors = res.validator;
                    let errorMessage = res.message ||
                      'An error occurred';

                    if (errors) {
                      errorMessage += '<ul>';
                      errors.forEach(error => {
                        errorMessage +=
                          `<li>${error}</li>`;
                      });
                      errorMessage += '</ul>';
                    }

                    throw new Error(errorMessage);
                  });
                }
                return response.json();
              })
              .catch(error => {
                Swal.fire('Error!', error.message, 'error');
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
    });
  </script>
</body>

</html>
