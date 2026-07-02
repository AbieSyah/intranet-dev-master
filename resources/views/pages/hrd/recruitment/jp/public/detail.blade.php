<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recruitment - Hisamitsu Pharma Indonesia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.2/dist/sweetalert2.min.css"
        integrity="sha256-XE4NT4UAtULuSdFWQXaaLSOt0/ZqL5xbX/ObUyf2UTI=" crossorigin="anonymous">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/css/bootstrap-datepicker3.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css">
    <link rel="preconnect" href="https://challenges.cloudflare.com">
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    <style>
        body,
        html {
            background: gainsboro;
        }

        #preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #fff;
            z-index: 9999;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: opacity 0.3s ease;
        }

        .spinner-border {
            width: 3rem;
            height: 3rem;
        }

        .page-content-wrapper {
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .job-content-style ul,
        .job-content-style ol {
            padding-left: 20px;
        }

        .text-primary {
            color: #003DA7 !important;
        }

        .bg-primary {
            background-color: #003DA7 !important;
            color: white !important;
        }

        .border-primary {
            border-color: #003DA7 !important;
        }

        .form-check-input:checked {
            background-color: #003DA7 !important;
            border-color: #003DA7 !important;
        }

        .preview {
            overflow: hidden;
            width: 160px;
            height: 240px;
            margin: 0 auto;
            border: 1px solid #ccc;
            background: #f7f7f7;
        }

        .datepicker table tr td.active,
        .datepicker table tr td.active.highlighted,
        .datepicker table tr td.active.disabled,
        .datepicker table tr td.active.disabled.highlighted {
            background-color: #003DA7 !important;
            background-image: none;
            color: #fff;
            text-shadow: none;
        }

        .datepicker table tr td.today {
            color: #003DA7 !important;
            font-weight: bold;
            border-color: #003DA7 !important;
        }

        .datepicker table tr td span.active.active,
        .datepicker table tr td span.active:hover {
            background-color: #003DA7 !important;
            background-image: none;
            color: #fff;
        }

        .datepicker table tr td.day:hover,
        .datepicker table tr th.datepicker-switch:hover,
        .datepicker table tr th.prev:hover,
        .datepicker table tr th.next:hover,
        .datepicker table tr td span:hover {
            background: #f0f4ff;
        }

        .datepicker-dropdown .datepicker-switch {
            color: #003DA7;
            font-weight: bold;
        }

        .datepicker table tr td.today {
            color: #003DA7 !important;
            font-weight: bold;
            border-color: #003DA7 !important;
            background-color: #f0f4ff !important;
            background-image: none !important;
        }

        .datepicker table tr td.today.active {
            background-color: #003DA7 !important;
            color: #fff !important;
        }
    </style>
</head>

<body>

    <div id="preloader">
        <div class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="text-primary mt-2">Memuat detail lowongan...</p>
        </div>
    </div>

    <div class="page-content-wrapper">
        <div class="container mt-5 mb-5">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow mb-3 rounded-3">
                        <div class="card-body">
                            <img src="{{ url('') }}/assets/images/logosalonpas.jpg" alt="Logo" height="60"
                                class="mb-2">
                            <h1 class="card-title fs-3 fw-bold text-primary" id="jobTitle">Memuat...</h1>
                            <div class="d-flex flex-column gap-2 mb-4 text-muted">
                                <span class="d-flex align-items-center">
                                    <i class="ri-building-line me-2"></i>
                                    <span>PT. Hisamitsu Pharma Indonesia</span>
                                </span>
                            </div>
                            <hr>
                            <h4 class="fw-bold text-dark mb-3">📋 Deskripsi & Kualifikasi</h4>
                            <div id="jobQualificationContent" class="job-content-style">
                                <p class="text-muted">Memuat deskripsi pekerjaan...</p>
                            </div>
                            <div class="mt-4 p-3 bg-light border border-primary rounded-3">
                                <p class="mb-0 fw-bold text-primary">
                                    <i class="ri-calendar-check-line me-1"></i>
                                    Deadline :
                                    <span id="applicationDeadline" class="text-dark">--/--/----</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card shadow rounded-3">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <span class="badge bg-success-subtle text-success fw-bold" id="applicationStatusBadge">
                                    <i class="ri-timer-line me-1"></i> Status Lowongan
                                </span>
                            </div>
                            <a href="#" id="applyButton" class="btn btn-success btn-lg w-100 fw-bold disabled">
                                <i class="ri-send-plane-fill me-2"></i> LAMAR SEKARANG
                            </a>
                            <hr>
                            <div class="text-start">
                                <h6 class="fw-bold mb-2">Diposting Oleh :</h6>
                                <p class="mb-0" id="posterName"><i class="ri-user-2-line me-2"></i> HR Recruiter</p>
                                <p class="mb-0" id="publishDate"><i class="ri-time-line me-2"></i> Dipublikasi pada --
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="applyJobModal" tabindex="-1" aria-labelledby="applyJobModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white" id="applyJobModalLabel">Recruitment Form</i></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="jobApplicationForm" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="job_id" id="jobIdInput">
                        <input type="hidden" name="traffic_source" id="trafficSourceInput">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="no_ktp" class="form-label">Nomor KTP / <i>KTP Number</i> <i
                                        style="color: red">*</i></label>
                                <input type="text" class="form-control" id="no_ktp" name="no_ktp"
                                    placeholder="3515************" required>
                            </div>
                            <div class="col-md-6">
                                <label for="fullname" class="form-label required">Nama Lengkap / <i>Full Name</i> <i
                                        style="color: red">*</i></label>
                                <input type="text" class="form-control" id="fullname" name="fullname" required
                                    placeholder="Nama Lengkap sesuai KTP">
                            </div>
                            <div class="col-md-6">
                                <label for="nickname" class="form-label">Nama Panggilan / <i>Nickname</i> <i
                                        style="color: red">*</i></label>
                                <input type="text" class="form-control" id="nickname" name="nickname"
                                    placeholder="Masukkan Nama Panggilan" required>
                            </div>
                            <div class="col-12">
                                <label for="ktp_address" class="form-label required">Alamat Sesuai KTP / <i>KTP
                                        Address</i> <i style="color: red">*</i></label>
                                <input type="text" class="form-control" id="ktp_address" name="ktp_address"
                                    required placeholder="Alamat Lengkap sesuai KTP">
                            </div>
                            <div class="col-12">
                                <label for="domicile_address" class="form-label">Alamat Domisili Saat Ini /
                                    <i>Domicile Address</i></label>
                                <input type="text" class="form-control" id="domicile_address"
                                    name="domicile_address" placeholder="Alamat Tinggal saat ini">
                            </div>
                            <div class="col-md-6">
                                <label for="birthplace" class="form-label required">Tempat Lahir / <i>Place of
                                        Birth</i> <i style="color: red">*</i></label>
                                <input type="text" class="form-control" id="birthplace" name="birthplace"
                                    required placeholder="Tempat Lahir sesuai KTP">
                            </div>
                            <div class="col-md-6">
                                <label for="birthdate" class="form-label required">Tanggal Lahir / <i>Date of
                                        Birth</i> <i style="color: red">*</i></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="birthdate" name="birthdate"
                                        placeholder="dd/mm/yyyy" aria-describedby="dateicon"
                                        onkeydown="return false;" required>
                                    <span class="input-group-text" id="dateicon" style="cursor: pointer;">
                                        <i class="ri-calendar-2-line"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label required">Jenis Kelamin / <i>Gender</i> <i
                                        style="color: red">*</i></label>
                                <div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="gender" value="Male"
                                            id="radio-male" required>
                                        <label class="form-check-label" for="radio-male">Pria / Male</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="gender" value="Female"
                                            id="radio-female" required>
                                        <label class="form-check-label" for="radio-female">Wanita / Female</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="religion" class="form-label required">Agama / <i>Religion</i> <i
                                        style="color: red">*</i></label>
                                <select class="form-select" id="religion" name="religion" required>
                                    <option value="" disabled selected>Select an option</option>
                                    <option value="Moslem">Islam / Moslem</option>
                                    <option value="Catholic">Katolik / Catholic</option>
                                    <option value="Christian">Kristen / Christian</option>
                                    <option value="Budhist">Buddha / Budhist</option>
                                    <option value="Hindu">Hindu / Hindu</option>
                                    <option value="None">Lainnya / None</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="marital" class="form-label required">Status Perkawinan / <i>Marital</i>
                                    <i style="color: red">*</i></label>
                                <select class="form-select" id="marital" name="marital" required>
                                    <option value="" disabled selected>Select an option</option>
                                    <option value="Single">Belum Menikah / Single</option>
                                    <option value="Married">Menikah / Married</option>
                                    <option value="Divorced">Cerai / Divorced</option>
                                    <option value="Widow">Janda / Widow</option>
                                    <option value="Widower">Duda / Widower</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="height" class="form-label">Tinggi Badan / <i>Height</i> (cm) <i
                                        style="color: red">*</i></label>
                                <input type="number" class="form-control" id="height" name="height"
                                    placeholder="Ex: 170" min="50" max="300" required>
                            </div>
                            <div class="col-md-6">
                                <label for="weight" class="form-label">Berat Badan / <i>Weight</i> (kg) <i
                                        style="color: red">*</i></label>
                                <input type="number" class="form-control" id="weight" name="weight"
                                    placeholder="Ex: 65" min="20" max="500" required>
                            </div>

                            <div class="col-md-6">
                                <label for="phone" class="form-label required">Telepon / <i>Phone</i> <i
                                        style="color: red">*</i></label>
                                <input type="tel" class="form-control" id="phone" name="phone" required
                                    placeholder="0812********">
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label required">Email / <i>Email</i> <i
                                        style="color: red">*</i></label>
                                <input type="email" class="form-control" id="email" name="email" required
                                    placeholder="email@example.com">
                            </div>

                            <div class="col-12">
                                <label for="skill" class="form-label required">Keterampilan & Kemampuan / <i>Skill
                                        & Ability</i> <i style="color: red">*</i></label>
                                <textarea class="form-control" id="skill" name="skill" required
                                    placeholder="Masukkan Keterampilan & Kemampuan"></textarea>
                            </div>

                            <div class="col-12">
                                <label for="expected_salary" class="form-label required">Gaji yang Diharapkan / <i>Expected Salary</i> <i style="color: red">*</i></label>
                                <input type="text" class="form-control" id="expected_salary" name="expected_salary"
                                    required placeholder="Masukkan Gaji yang Diharapkan">
                                <div class="form-check mt-1">
                                    <input class="form-check-input" type="checkbox" value="1" id="is_negotiable" name="is_negotiable">
                                    <label class="form-check-label" for="is_negotiable">
                                        Dapat Dinegosiasikan / <i>Negotiable</i>
                                    </label>
                                </div>
                            </div>

                            <div class="col-12">
                                <label for="photoInput" class="form-label required">Pas Foto / <i>Photo</i> (4x6) <i
                                        style="color: red">*</i></label>
                                <input type="file" class="form-control" id="photoInput"
                                    accept=".jpg, .jpeg, .png" required>
                                <small class="text-muted">Max 1MB. Format: JPG, JPEG, PNG. Rasio 4x6.</small>
                                <input type="hidden" name="photo" id="photoCroppedBase64">
                            </div>

                            <div class="col-12 border-top pt-3">
                                <h5 class="fw-bold text-primary text-center mb-3">Pendidikan / Education</h5>
                                <div id="educationContainer"></div>
                                <button type="button" class="btn btn-success w-100" id="addEducationBtn">
                                    <i class="ri-add-circle-line me-1"></i> Add Education
                                </button>
                            </div>
                            <div class="col-12 border-top pt-3">
                                <h5 class="fw-bold text-primary text-center mb-3">Pengalaman / Experience</h5>
                                <div id="experienceContainer"></div>
                                <button type="button" class="btn btn-success w-100" id="addExperienceBtn">
                                    <i class="ri-add-circle-line me-1"></i> Add Experience
                                </button>
                            </div>
                            <div class="col-12 border-top pt-3">
                                <div class="cf-turnstile text-center"
                                    data-sitekey="{{ env('CLOUDFLARE_TURNSTILE_SITE_KEY') }}"
                                    data-callback="turnstileCallback"
                                    data-theme="light">
                                </div>
                                <input type="hidden" name="cf-turnstile-response" id="cf-turnstile-response">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="check1" required>
                                    <label class="form-check-label fw-bold" for="check1">
                                        Saya memberikan persetujuan kepada PT Hisamitsu Pharma Indonesia
                                        untuk mengelola data dan
                                        dokumen pribadi saya untuk kepentingan pengelolaan data rekrutmen sesuai
                                        dengan kebijakan yang
                                        berlaku.<br>
                                        <i>I provide consent to PT Hisamitsu Pharma Indonesia to manage my
                                            personal data and documents
                                            for the purpose of recruitment data management in accordance with
                                            applicable policies.</i>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success" id="submitApplicationBtn">
                            <span class="spinner-border spinner-border-sm d-none" role="status"
                                aria-hidden="true"></span>
                            Submit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalUpload" tabindex="-1" aria-labelledby="staticBackdropLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Upload Foto (Rasio 4x6)</h5>
                    <button type="button" class="btn-close" onclick="cancelUpload()" aria-label="Close"
                        data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="img-container">
                        <div class="row">
                            <div class="col-md-8 mb-2">
                                <div style="max-width: 100%; height: 300px; overflow: hidden;">
                                    <img id="image" src="" alt="Gambar untuk di-crop"
                                        style="max-width: 100%; height: auto; display: block;">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <h6 class="text-center fw-bold">Preview 4x6</h6>
                                <div class="preview"
                                    style="height: 240px; overflow: hidden; border: 1px solid #ccc; background: #f7f7f7;">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex gap-2 justify-content-end mt-3">
                        <button type="button" onclick="cancelUpload()" class="btn btn-secondary"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-success" id="crop">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.2/dist/sweetalert2.all.min.js"
        integrity="sha256-4HdbDegPFqVsJaRNvgpTveEgxxl4KHtvqtkZeVsJNI4=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/locales/bootstrap-datepicker.id.min.js">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>
    <script>
        function turnstileCallback(token) {
            document.getElementById('cf-turnstile-response').value = token;
        }
        $(document).ready(function() {
            // Portal
            // const pathSegments = window.location.pathname.split('/');
            // let publishCode = pathSegments[pathSegments.length - 1];
            // const apiUrl = `https://192.168.3.176:8015/recruitment/job/get/${publishCode}`;

            // Intra
            const pathSegments = window.location.pathname.split('/recruitment/job/');
            const publishCode = pathSegments[1];
            const BASE_API_URL = '{{ url('recruitment/job/get') }}';
            const apiUrl = `${BASE_API_URL}/${publishCode}`;
            const applyUrl = '{{ route('job-posting.public.store') }}';

            let originalReferrer = document.referrer;
            if (!originalReferrer) {
                $('#trafficSourceInput').val('Direct Access');
            } else {
                $('#trafficSourceInput').val(originalReferrer);
            }

            let jobData = {};
            const applyButton = $('#applyButton');
            const applyJobModal = new bootstrap.Modal(document.getElementById('applyJobModal'));

            const contentWrapper = $('.page-content-wrapper');
            const preloader = $('#preloader');

            function hidePreloader() {
                preloader.css('opacity', 0);
                setTimeout(() => {
                    preloader.hide();
                    contentWrapper.css('opacity', 1);
                }, 300);
            }

            function showNotFound() {
                contentWrapper.hide();
                $('body').append(`
                    <div class="container d-flex justify-content-center align-items-center" style="min-height: 80vh;">
                        <div class="text-center">
                            <img src="{{ asset('assets/images/404.png') }}" alt="404 Not Found" class="img-fluid" style="max-width: 50vw;">
                            <h2 class="mb-3">Lowongan Tidak Ditemukan</h2>
                            <p class="text-muted mb-3">
                                Kami mohon maaf, Lowongan yang Anda cari Tidak Ditemukan dalam Sistem. Hal ini bisa terjadi karena lowongan sudah ditutup, terisi penuh, atau terdapat kesalahan pada tautan yang Anda gunakan. Kami sarankan untuk memeriksa kembali sumber informasi rekrutmen Anda.
                            </p>
                        </div>
                    </div>
                `);
                hidePreloader();
            }

            const clientDate = new Date();
            const dd = String(clientDate.getDate()).padStart(2, '0');
            const mm = String(clientDate.getMonth() + 1).padStart(2, '0');
            const yyyy = clientDate.getFullYear();
            const formattedClientDate = `${dd}/${mm}/${yyyy}`;
            let currentYear = yyyy.toString();
            let today = formattedClientDate;

            if (publishCode) {
                $.ajax({
                    url: apiUrl,
                    method: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        const job = response.data || {};
                        jobData = job;
                        if (job.title) {
                            $('#pageTitle').text(job.title || 'Pekerjaan Tanpa Judul');
                            $('#jobTitle').text(job.title || 'Judul Tidak Tersedia');
                            $('#jobQualificationContent').html(job.qualification ||
                                '<p class="text-muted">Detail kualifikasi belum tersedia.</p>');
                            const applyEndFormatted = job.apply_end_formatted || 'Ditutup';
                            const isOpen = job.is_open;
                            const todayDate = job.today_date;
                            if (todayDate) {
                                today = todayDate;
                                currentYear = todayDate.substring(6, 10);
                            }
                            initializeYearPicker();
                            $('#applicationDeadline').text(applyEndFormatted);
                            $('#applicationStatusBadge').text(isOpen ?
                                `Buka Hingga ${applyEndFormatted}` : 'Lowongan Telah Ditutup');
                            $('#publishDate').html(
                                `<i class="ri-time-line me-2"></i> Dipublikasi pada ${job.publish_date_formatted || '--'}`
                                );
                            if (isOpen) {
                                $('#applicationStatusBadge').removeClass(
                                    'bg-success-subtle text-success').addClass(
                                    'bg-primary text-dark').html(
                                    `<i class="ri-time-line me-1"></i> Buka Hingga ${applyEndFormatted}`
                                    );
                                applyButton.removeClass('disabled').attr('href', '#');
                            } else {
                                $('#applicationStatusBadge').removeClass(
                                    'bg-success-subtle text-success').addClass(
                                    'bg-danger text-white').html(
                                    '<i class="ri-close-circle-line me-1"></i> Lowongan Telah Ditutup'
                                    );
                                applyButton.text('LOWONGAN DITUTUP').removeClass('btn-success')
                                    .addClass('btn-secondary disabled');
                            }
                            hidePreloader();
                        } else {
                            showNotFound();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error:", error);
                        showNotFound();
                    }
                });
            } else {
                showNotFound();
            }

            function initializeYearPicker() {
                $('.year-picker').datepicker({
                    format: 'yyyy',
                    viewMode: 'years',
                    minViewMode: 'years',
                    autoclose: true,
                    endDate: currentYear.toString()
                });
                $('#birthdate').datepicker({
                    format: 'dd/mm/yyyy',
                    autoclose: true,
                    todayHighlight: true,
                    endDate: today || '0d',
                    language: 'id'
                });
            }

            $('#dateicon').on('click', function() {
                $('#birthdate').focus();
            });

            $('#educationContainer').on('click', '.year-picker-icon', function() {
                const targetInputId = $(this).data('target');
                $(targetInputId).focus();
            });

            // --- Education ---
            let educationCount = 0;

            function createEducationBlock(count) {
                const block = `
            <div class="education-item card mb-3" data-index="${count}">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <span class="fw-bold">(${count + 1})</span>
                    <button type="button" class="btn btn-danger btn-sm remove-education-btn" title="Delete">
                        <i class="ri-delete-bin-line"></i>
                    </button>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="edu_level_${count}" class="form-label required">Level Pendidikan / <i>Education Level</i> <i style="color: red">*</i></label>
                        <select class="form-select" id="edu_level_${count}" name="educations[${count}][level]" required>
                            <option value="" disabled selected>Pilih Level / Select Level</option>
                            <option value="Senior High School">Senior High School (SMA / MA / SMK)</option>
                            <option value="Diploma Degree">Diploma Degree (D1/D2/D3)</option>
                            <option value="Bachelor Degree">Bachelor Degree (D4/S1)</option>
                            <option value="Profession Program">Profession Program</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edu_institution_${count}" class="form-label required">Nama Institusi / <i>Institution Name</i> <i style="color: red">*</i></label>
                            <input type="text" class="form-control" id="edu_institution_${count}" name="educations[${count}][institution_name]" placeholder="Ex: Universitas Indonesia" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edu_major_${count}" class="form-label required">Jurusan / <i>Major</i> <i style="color: red">*</i></label>
                            <input type="text" class="form-control" id="edu_major_${count}" name="educations[${count}][major]" placeholder="Ex: Teknik Mesin" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edu_year_graduated_${count}" class="form-label required">Tahun Lulus / <i>Year Graduated</i> <i style="color: red">*</i></label>
                            <div class="input-group">
                                <input type="text" class="form-control year-picker" id="edu_year_graduated_${count}" name="educations[${count}][year_graduated]" placeholder="Ex: 2020" aria-describedby="yearicon_${count}" required>
                                <span class="input-group-text year-picker-icon" id="yearicon_${count}" data-target="#edu_year_graduated_${count}" style="cursor: pointer;">
                                    <i class="ri-calendar-2-line"></i>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edu_score_gpa_${count}" class="form-label required">IPK / Nilai Ijazah <i style="color: red">*</i></label>
                            <input type="number" step="0.01" class="form-control" id="edu_score_gpa_${count}" name="educations[${count}][score_gpa]" placeholder="Ex: 3.50 (IPK) atau 90.50 (Nilai)" min="0" required>
                        </div>
                    </div>
                    <div>
                        <label for="edu_ijazah_${count}" class="form-label required">File Ijazah / <i>Certificate File</i> <i style="color: red">*</i></label>
                        <input type="file" class="form-control" id="edu_ijazah_${count}" name="educations[${count}][ijazah]" accept=".pdf, .jpg, .jpeg, .png" required>
                        <small class="text-muted">Max 2MB. Format: PDF, JPG, JPEG, PNG.</small>
                    </div>
                </div>
            </div>
        `;
                return block;
            }
            $('#addEducationBtn').on('click', function() {
                $('#educationContainer').append(createEducationBlock(educationCount));
                initializeYearPicker();
                educationCount++;
            });

            $('#educationContainer').on('click', '.remove-education-btn', function() {
                $(this).closest('.education-item').remove();
                reindexEducations();
            });

            function reindexEducations() {
                educationCount = 0;
                $('#educationContainer').find('.education-item').each(function(index) {
                    const newIndex = index;
                    $(this).find('.card-header span').html(`(${newIndex + 1})`);
                    $(this).find('input, select').each(function() {
                        const oldName = $(this).attr('name');
                        if (oldName) {
                            const fieldNameMatch = oldName.match(
                                /\[(level|institution_name|major|year_graduated|score_gpa|ijazah)\]/
                                );
                            if (fieldNameMatch && fieldNameMatch[1]) {
                                const fieldName = fieldNameMatch[1];
                                const newName = `educations[${newIndex}][${fieldName}]`;
                                $(this).attr('name', newName);
                                $(this).attr('id', `edu_${fieldName}_${newIndex}`);
                                $(this).siblings('label').attr('for',
                                    `edu_${fieldName}_${newIndex}`);
                            }
                        }
                    });
                    educationCount++;
                });
            }

            // --- Experience ---
            let experienceCount = 0;

            function createExperienceBlock(count) {
                const block = `
            <div class="experience-item card mb-3" data-index="${count}">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <span class="fw-bold">(${count + 1})</span>
                    <button type="button" class="btn btn-danger btn-sm remove-experience-btn" title="Delete">
                        <i class="ri-delete-bin-line"></i>
                    </button>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="exp_company_${count}" class="form-label required">Nama Perusahaan / <i>Company Name</i> <i style="color: red">*</i></label>
                        <input type="text" class="form-control" id="exp_company_${count}" name="experiences[${count}][company]" placeholder="Masukkan Nama Perusahaan" required>
                    </div>
                    <div class="mb-3">
                        <label for="exp_position_${count}" class="form-label required">Jabatan / <i>Position</i> <i style="color: red">*</i></label>
                        <input type="text" class="form-control" id="exp_position_${count}" name="experiences[${count}][position]" placeholder="Masukkan Jabatan" required>
                    </div>
                    <div>
                        <label for="exp_years_${count}" class="form-label required">Durasi (Tahun) / <i>Duration (Years)</i> <i style="color: red">*</i></label>
                        <input type="number" class="form-control" id="exp_years_${count}" name="experiences[${count}][years]" placeholder="Hanya Angka / Only Number" min="0" max="100" required>
                    </div>
                </div>
            </div>
        `;
                return block;
            }
            $('#addExperienceBtn').on('click', function() {
                $('#experienceContainer').append(createExperienceBlock(experienceCount));
                experienceCount++;
            });

            $('#experienceContainer').on('click', '.remove-experience-btn', function() {
                $(this).closest('.experience-item').remove();
                reindexExperiences();
            });

            function reindexExperiences() {
                experienceCount = 0;
                $('#experienceContainer').find('.experience-item').each(function(index) {
                    const newIndex = index;
                    $(this).find('.card-header span').html(`(${newIndex + 1})`);
                    $(this).find('input').each(function() {
                        const oldName = $(this).attr('name');
                        if (oldName) {
                            const fieldNameMatch = oldName.match(/\[(company|position|years)\]/);
                            if (fieldNameMatch && fieldNameMatch[1]) {
                                const fieldName = fieldNameMatch[1];
                                const newName = `experiences[${newIndex}][${fieldName}]`;
                                $(this).attr('name', newName);
                                $(this).attr('id', `exp_${fieldName}_${newIndex}`);
                            }
                        }
                    });
                    experienceCount++;
                });
            }

            applyJobModal._element.addEventListener('show.bs.modal', function() {
                if ($('#experienceContainer').children().length === 0) {
                    $('#addExperienceBtn').trigger('click');
                }
                if ($('#educationContainer').children().length === 0) {
                    $('#addEducationBtn').trigger('click');
                }
                initializeYearPicker();
            });

            $('#jobApplicationForm').on('reset', function() {
                $('#experienceContainer').empty();
                experienceCount = 0;
                $('#educationContainer').empty();
                educationCount = 0;
            });

            applyButton.on('click', function(e) {
                if (!$(this).hasClass('disabled')) {
                    e.preventDefault();
                    $('#modalJobTitle').text(jobData.title);
                    $('#jobIdInput').val(jobData.id);
                    $('#formErrorAlert').addClass('d-none').empty();
                    applyJobModal.show();
                }
            });

            let swalert;
            $('#jobApplicationForm').on('submit', function(e) {
                e.preventDefault();
                const form = this;
                const turnstileToken = $('#cf-turnstile-response').val();
                if (!turnstileToken) {
                    Swal.fire({
                        title: 'Verifikasi Gagal!',
                        icon: 'error',
                        confirmButtonText: 'OK',
                        customClass: { confirmButton: 'btn btn-danger' }
                    });
                    return;
                }
                if (!form.reportValidity()) {
                    return;
                }
                const educationCount = $('#educationContainer').find('.education-item').length;
                if (educationCount === 0) {
                    Swal.fire({
                        title: 'Pendidikan Belum Lengkap!',
                        text: 'Mohon isi minimal satu riwayat pendidikan',
                        icon: 'error',
                        confirmButtonText: 'OK',
                        customClass: { confirmButton: 'btn btn-danger' }
                    });
                    return;
                }
                Swal.fire({
                    title: 'Apakah data lamaran sudah benar?',
                    text: 'Pastikan semua data yang Anda isikan akurat dan valid sebelum dikirim.',
                    icon: 'info',
                    showCancelButton: true,
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#003DA7',
                    confirmButtonText: 'Ya, Kirim',
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        swalert = Swal.fire({
                            title: 'Memproses Lamaran...',
                            text: 'Mohon tunggu sebentar. Data Anda sedang dikirim.',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        const formData = new FormData(form);
                        return new Promise((resolve, reject) => {
                            $.ajax({
                                url: applyUrl,
                                method: 'POST',
                                data: formData,
                                processData: false,
                                contentType: false,
                                success: function(response) {
                                    resolve(response);
                                },
                                error: function(xhr, status, error) {
                                    reject(xhr);
                                }
                            });
                        });
                    },
                    allowOutsideClick: () => !Swal.isLoading(),
                }).then((result) => {
                    if (result.isConfirmed && result.value) {
                        const response = result.value;
                        if (swalert) {
                            swalert.close();
                        }
                        applyJobModal.hide();
                        $('#jobApplicationForm').trigger('reset');
                        $('#photoInput').removeClass('is-valid');
                        Swal.fire({
                            title: "Berhasil!",
                            text: response.message ||
                                'Lamaran Anda berhasil dikirim. Terima kasih.',
                            icon: "success",
                            buttonsStyling: false,
                            confirmButtonText: "OK",
                            customClass: {
                                confirmButton: "btn btn-success"
                            }
                        });
                    }
                }).catch(xhr => {
                    if (swalert) {
                        swalert.close();
                    }
                    handleErrorResponse(xhr.responseJSON, xhr);
                });
            });

            function handleErrorResponse(responseJson, xhr) {
                let errorMessage = '';
                if (responseJson && responseJson.message) {
                    errorMessage += `<h6 class="text-danger">${responseJson.message}</h6>`;
                } else if (xhr && xhr.status === 419) {
                    errorMessage += `<h6 class="text-danger">Sesi kedaluwarsa. Mohon refresh halaman.</h6>`;
                }
                if (responseJson && responseJson.errors) {
                    errorMessage += '<ul class="text-start mb-0 ps-3">';
                    for (const fieldName in responseJson.errors) {
                        errorMessage += `<li>${responseJson.errors[fieldName][0]}</li>`;
                    }
                    errorMessage += '</ul>';
                }
                if (errorMessage === '') {
                    errorMessage +=
                        '<p class="text-danger">Terjadi kesalahan tak terduga. Mohon refresh halaman.</p>';
                }
                Swal.fire({
                    title: 'Gagal!',
                    html: errorMessage,
                    icon: 'error',
                    buttonsStyling: false,
                    confirmButtonText: 'Tutup',
                    customClass: {
                        confirmButton: 'btn btn-danger'
                    }
                });
            }

            let cropperInstance = null;
            const cropModal = new bootstrap.Modal(document.getElementById('modalUpload'));
            const imageToCropElement = document.getElementById('image');
            $('#photoInput').on('change', function(e) {
                const files = e.target.files;
                if (!files || files.length === 0) return;
                const file = files[0];
                if (file.size > 1024 * 1024) {
                    Swal.fire({
                        title: 'Ukuran File Terlalu Besar',
                        text: 'Pas Foto maksimal 1MB.',
                        icon: 'error',
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'btn btn-danger'
                        }
                    });
                    $(this).val('');
                    return;
                }
                const reader = new FileReader();
                reader.onload = function(e) {
                    if (cropperInstance) {
                        cropperInstance.destroy();
                        cropperInstance = null;
                    }
                    imageToCropElement.src = e.target.result;
                    cropModal.show();
                };
                reader.readAsDataURL(file);
            });
            $('#modalUpload').on('shown.bs.modal', function() {
                if (imageToCropElement.src) {
                    if (cropperInstance) {
                        cropperInstance.destroy();
                        cropperInstance = null;
                    }
                    cropperInstance = new Cropper(imageToCropElement, {
                        aspectRatio: 4 / 6,
                        preview: '.preview',
                        viewMode: 2,
                        dragMode: 'move',
                        autoCropArea: 1.0,
                        responsive: true,
                        movable: true,
                        zoomable: true,
                        rotatable: false,
                        scalable: false,
                        enableResize: true,
                        ready: function() {
                            $(this).cropper('resize');
                        }
                    });
                }
            });
            $('#crop').on('click', function() {
                if (!cropperInstance) return;
                const croppedData = cropperInstance.getCroppedCanvas({
                    width: 600,
                    height: 900,
                    imageSmoothingQuality: 'high',
                }).toDataURL('image/jpeg', 1.0);
                $('#photoCroppedBase64').val(croppedData);
                $('#photoInput').removeClass('is-invalid').addClass('is-valid');
                cropModal.hide();
                const photoInput = $('#photoInput');
                photoInput.focus();
                cropperInstance.destroy();
                cropperInstance = null;
            });
            window.cancelUpload = function() {
                if (cropperInstance) {
                    cropperInstance.destroy();
                    cropperInstance = null;
                }
                $('#photoCroppedBase64').val('');
                $('#photoInput').val('');
                $('#photoInput').removeClass('is-valid');
            };
        });
    </script>
</body>

</html>
