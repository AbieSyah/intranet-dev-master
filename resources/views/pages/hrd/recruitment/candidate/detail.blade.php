@extends('layouts.master')
@section('link')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css" />
    <link rel="stylesheet" href="{{ url('') }}/assets/libs/adminlte/select2/css/4.1.0/select2.min.css">
    <link href="{{ url('') }}/assets/libs/Datatables/DataTables-1.13.1/css/dataTables.bootstrap5.css" rel="stylesheet"
        type="text/css" />
    <link href="{{ url('') }}/assets/libs/Datatables/Buttons-2.3.3/css/buttons.dataTables.css" rel="stylesheet"
        type="text/css" />
    <link href="{{ url('') }}/assets/libs/Datatables/Responsive-2.4.0/css/responsive.bootstrap.css" rel="stylesheet"
        type="text/css" />
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <!-- Select2-->
    <link rel="stylesheet" href="{{ url('') }}/assets/libs/adminlte/select2/css/4.1.0/select2.min.css">
    <style type="text/css">
        body {
            background: #f7fbf8;
        }
        .preview {
            text-align: center;
            overflow: hidden;
            width: 160px;
            height: 160px;
            margin: 10px;
            border: 1px solid red;
        }
        .section {
            margin-top: 150px;
            background: #fff;
            padding: 50px 30px;
        }
        div.dataTables_wrapper {
            width: 100%;
        }
        .select2-container--default .select2-selection--single {
            height: calc(2.25rem + 2px);
            padding: 0.375rem 0.75rem;
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 1.5rem;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 100%;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Detail Candidate</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Recruitment</a></li>
                        <li class="breadcrumb-item active">Candidate</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header align-items-center d-flex justify-content-between">
                    <h3 class="card-title">{{ $c->fullname ?? '' }}</h3>
                    <div class="flex-shrink-0">
                        <a href="{{ url()->previous() }}" class="btn btn-primary btn-label waves-effect waves-light ms-2">
                            <i class="ri-arrow-left-fill label-icon align-middle fs-16 me-2"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 mb-2">
                            <h5 class="text-center text-primary">Candidate Information</h5>
                        </div>
                        @if ($c->photo)
                        <div class="col-12 mb-2 text-center">
                            <img src="{{ asset('storage/candidates/photos/' . $c->photo) }}" 
                                alt="{{ $c->fullname }} Photo" 
                                class="img-thumbnail" 
                                style="width: 200px; height: 300px; object-fit: cover;">
                        </div>
                         @endif
                        <div class="col-lg-4 mb-2">
                            <label for="no_ktp" class="form-label col-form-label fw-semibold">Nomor KTP / KTP Number</label>
                            <input type="text" class="form-control" id="no_ktp" name="no_ktp" value="{{ $c->no_ktp ?? '' }}" disabled>
                        </div>
                        <div class="col-lg-4 mb-2">
                            <label for="fullname" class="form-label col-form-label fw-semibold">Nama Lengkap / Full Name</label>
                            <input type="text" class="form-control" id="fullname" name="fullname" value="{{ $c->fullname ?? '-' }}" disabled>
                        </div>
                        <div class="col-lg-4 mb-2">
                            <label for="nickname" class="form-label col-form-label fw-semibold">Nama Panggilan / Nickname</label>
                            <input type="text" class="form-control" id="nickname" name="nickname" value="{{ $c->nickname ?? '-' }}" disabled>
                        </div>
                        <div class="col-lg-6 mb-2">
                            <label for="ktp_address" class="form-label col-form-label fw-semibold">Alamat Sesuai KTP / KTP Address</label>
                            <input type="text" class="form-control" id="ktp_address" name="ktp_address" value="{{ $c->ktp_address ?? '-' }}" disabled>
                        </div>
                        <div class="col-lg-6 mb-2">
                            <label for="domicile_address" class="form-label col-form-label fw-semibold">Alamat Domisili Saat Ini / Domicile Address</label>
                            <input type="text" class="form-control" id="domicile_address" name="domicile_address" value="{{ $c->domicile_address ?? '-' }}" disabled>
                        </div>
                        <div class="col-lg-4 mb-2">
                            <label for="birthplace" class="form-label col-form-label fw-semibold">Tempat Lahir / Place of Birth</label>
                            <input type="text" class="form-control" id="birthplace" name="birthplace" value="{{ $c->birthplace ?? '-' }}" disabled>
                        </div>
                        <div class="col-lg-4 mb-2">
                            <label for="birthdate" class="form-label col-form-label fw-semibold">Tanggal Lahir / Date of Birth</label>
                            <input type="text" class="form-control" id="birthdate" name="birthdate" value="{{ ($c->birthdate ?? null) ? \Carbon\Carbon::parse($c->birthdate)->format('d/m/Y') : '-' }}" disabled>
                        </div>
                        <div class="col-lg-4 mb-2">
                            <label class="form-label col-form-label fw-semibold">Jenis Kelamin / Gender</label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="gender" value="Male" id="radio-male" {{ old('gender', $c->gender ?? '') == 'Male' ? 'checked' : '' }} disabled>
                                    <label class="form-check-label" for="radio-male">Pria / Male</label>
                                </div>
                                <br>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="gender" value="Female" id="radio-female" {{ old('gender', $c->gender ?? '') == 'Female' ? 'checked' : '' }} disabled>
                                    <label class="form-check-label" for="radio-female">Wanita / Female</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-2">
                            <label for="religion" class="form-label col-form-label fw-semibold">Agama / Religion</label>
                            <select class="form-select select2" id="religion" name="religion" disabled>
                                <option>{{ old('religion', $c->religion ?? '') }}</option>
                                <option {{ old('religion', $c->religion ?? '') == 'Moslem' ? 'selected' : '' }}>Moslem</option>
                                <option {{ old('religion', $c->religion ?? '') == 'Catholic' ? 'selected' : '' }}>Catholic</option>
                                <option {{ old('religion', $c->religion ?? '') == 'Christian' ? 'selected' : '' }}>Christian</option>
                                <option {{ old('religion', $c->religion ?? '') == 'Budhist' ? 'selected' : '' }}>Budhist</option>
                                <option {{ old('religion', $c->religion ?? '') == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                                <option {{ old('religion', $c->religion ?? '') == 'None' ? 'selected' : '' }}>None</option>
                            </select>
                        </div>
                        <div class="col-lg-6 mb-2">
                            <label for="marital" class="form-label col-form-label fw-semibold">Status Perkawinan / Marital</label>
                            <select class="form-select select2" id="marital" name="marital" disabled>
                                <option>{{ old('marital', $c->marital ?? '') }}</option>
                                <option {{ old('marital', $c->marital ?? '') == 'Single' ? 'selected' : '' }}>Single</option>
                                <option {{ old('marital', $c->marital ?? '') == 'Married' ? 'selected' : '' }}>Married</option>
                                <option {{ old('marital', $c->marital ?? '') == 'Divorced' ? 'selected' : '' }}>Divorced</option>
                                <option {{ old('marital', $c->marital ?? '') == 'Widow' ? 'selected' : '' }} 
                                    title="A woman whose her husband has died and has not married again" value="Widow">Widow (Female)</option>
                                <option {{ old('marital', $c->marital ?? '') == 'Widower' ? 'selected' : '' }}
                                    title="A man whose his wife has died and has not married again" value="Widower">Widower (Male)</option>
                            </select>
                        </div>
                        <div class="col-lg-6 mb-2">
                            <label for="height" class="form-label col-form-label fw-semibold">Tinggi Badan / Height</label>
                            <input type="text" class="form-control" id="height" name="height" value="{{ $c->height ?? '-' }} cm" disabled>
                        </div>
                        <div class="col-lg-6 mb-2">
                            <label for="weight" class="form-label col-form-label fw-semibold">Berat Badan / Weight</label>
                            <input type="text" class="form-control" id="weight" name="weight" value="{{ $c->weight ?? '-' }} kg" disabled>
                        </div>
                        <div class="col-lg-6 mb-2">
                            <label for="phone" class="form-label col-form-label fw-semibold">Telepon / Phone</label>
                            <input type="tel" class="form-control" id="phone" name="phone" value="{{ $c->phone ?? '-' }}" disabled>
                        </div>
                        <div class="col-lg-6 mb-2">
                            <label for="email" class="form-label col-form-label fw-semibold">Email / Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ $c->email ?? '-' }}" disabled>
                        </div>
                        <div class="col-12 mb-2">
                            <label for="skill" class="form-label col-form-label fw-semibold">Keterampilan & Kemampuan / Skill & Ability</label>
                            <textarea class="form-control" id="skill" name="skill" disabled>{{ $c->skill ?? '-' }}</textarea>
                        </div>
                        <div class="col-12 mb-2">
                            <label for="expected_salary" class="form-label col-form-label fw-semibold">Gaji yang Diharapkan / Expected Salary</label>
                            <input type="text" class="form-control" id="expected_salary" name="expected_salary" value="{{ $c->expected_salary ?? '-' }}" disabled>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if ($c->educations->isNotEmpty())
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header align-items-center d-flex justify-content-between">
                        <h3 class="card-title">Riwayat Pendidikan / Education Background</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped bordered display nowrap" style="width:100%"
                            id="table_education_history">
                            <thead>
                                <tr>
                                    <th scope="col" class="text-center">No</th>
                                    <th scope="col" class="text-center">Education Level</th>
                                    <th scope="col" class="text-center">Institution Name</th>
                                    <th scope="col" class="text-center">Major / Field of Study</th>
                                    <th scope="col" class="text-center">Year Graduated</th>
                                    <th scope="col" class="text-center">GPA</th>
                                    <th scope="col" class="text-center">File</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($c->educations as $index => $education)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td class="text-center">{{ $education->level ?? '-' }}</td>
                                        <td class="text-center">{{ $education->institution_name ?? '-' }}</td>
                                        <td class="text-center">{{ $education->major ?? '-' }}</td>
                                        <td class="text-center">{{ $education->year_graduated ?? '-' }}</td>
                                        <td class="text-center">{{ $education->score_gpa ?? '-' }}</td>
                                        <td class="text-center">
                                            @if (!empty($education->ijazah))
                                                <a href="{{ asset('storage/candidates/ijazah/' . $education->ijazah) }}" title="View" target="_blank"
                                                    class="btn btn-primary btn-sm"><i class="ri-file-text-line"></i></a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-12">
                <p class="alert alert-danger text-center fw-semibold">
                    Kandidat belum menyertakan data riwayat pendidikan. / Candidate has not provided education history data.
                </p>
            </div>
        </div>
    @endif
    @if ($c->experiences->isNotEmpty())
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header align-items-center d-flex justify-content-between">
                        <h3 class="card-title">Pengalaman Bekerja / Working Experience</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped bordered display nowrap" style="width:100%"
                            id="table_experience_history">
                            <thead>
                                <tr>
                                    <th scope="col" class="text-center">No</th>
                                    <th scope="col" class="text-center">Company Name</th>
                                    <th scope="col" class="text-center">Position</th>
                                    <th scope="col" class="text-center">Duration</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($c->experiences as $index => $experience)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td class="text-center">{{ $experience->company ?? '-' }}</td>
                                        <td class="text-center">{{ $experience->position ?? '-' }}</td>
                                        <td class="text-center">{{ ($experience->years ?? null) ? $experience->years . ' Years' : '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-12">
                <p class="alert alert-danger text-center fw-semibold">
                    Kandidat belum menyertakan data pengalaman kerja. / Candidate has not provided working experience data.
                </p>
            </div>
        </div>
    @endif
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 mb-2">
                            <h5 class="text-center text-primary">Submit Information</h5>
                        </div>
                        <div class="col-lg-6 mb-2">
                            <label class="form-label col-form-label fw-semibold">Job Posting Title</label>
                            <input type="text" class="form-control" 
                                value="{{ optional($c->posting)->title ?? '-' }}" disabled/>
                        </div>
                        <div class="col-lg-6 mb-2">
                            <label class="form-label col-form-label fw-semibold">Submitted Date</label>
                            <input type="text" class="form-control" 
                                value="{{ $c->submit_date ? \Carbon\Carbon::parse($c->submit_date)->format('d F Y - H:i:s T') : '-' }}" 
                                disabled/>
                        </div>
                        <div class="col-lg-6 mb-2">
                            <label class="form-label col-form-label fw-semibold">Position</label>
                            <input type="text" class="form-control" 
                                value="{{ optional($c->position)->nama ?? '-' }}" disabled/>
                        </div>
                        <div class="col-lg-6 mb-2">
                            <label class="form-label col-form-label fw-semibold">Section</label>
                            <input type="text" class="form-control" 
                                value="{{ optional($c->section)->nama ?? '-' }}" disabled/>
                        </div>
                        <div class="col-lg-6 mb-2">
                            <label class="form-label col-form-label fw-semibold">Department</label>
                            <input type="text" class="form-control" 
                                value="{{ optional($c->department)->name ?? '-' }}" disabled/>
                        </div>
                        <div class="col-lg-6 mb-2">
                            <label class="form-label col-form-label fw-semibold">Area</label>
                            <input type="text" class="form-control" 
                                value="{{ optional($c->area)->name ?? '-' }}" disabled/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ url('') }}/assets/js/pages/profile-setting.init.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.js"></script>
    <script src="{{ url('') }}/assets/libs/Datatables/DataTables-1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="{{ url('') }}/assets/libs/Datatables/DataTables-1.13.1/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ url('') }}/assets/libs/Datatables/Responsive-2.4.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="{{ url('') }}/assets/js/pages/datatables.init.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <!-- Select2 -->
    <script src="{{ url('') }}/assets/libs/adminlte/select2/js/4.1.0/select2.min.js"></script>
    <script type="text/javascript">
        $('#birthdate').flatpickr({
            allowInput: true,
            dateFormat: "d/m/Y",
        });
        $(function() {
            $('.select2').select2()
        });
    </script>
    <script>
        @if (Session::has('success'))
            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-bottom-right"
            }
            toastr.success("{{ session('success') }}");
        @endif
        @if (Session::has('error'))
            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-bottom-right"
            }
            toastr.error("{{ session('error') }}");
        @endif
    </script>
    @if ($c->educations->isNotEmpty())
        <script type="text/javascript">
            $(document).ready(function() {
                $('#table_education_history').DataTable({
                    stateSave: false,
                    responsive: true,
                    autoWidth: false,
                });
            });
        </script>
    @endif
    @if ($c->experiences->isNotEmpty())
        <script type="text/javascript">
            $(document).ready(function() {
                $('#table_experience_history').DataTable({
                    stateSave: false,
                    responsive: true,
                    autoWidth: false,
                });
            });
        </script>
    @endif
@endsection
