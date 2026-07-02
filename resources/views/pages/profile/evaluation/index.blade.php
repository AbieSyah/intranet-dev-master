@extends('layouts.master')
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
    <style type="text/css">
        /* body{
            background: #f7fbf8;
        }    */
        img {
            /* display: block; */
            max-width: 100%;
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

        .table-responsive {
            overflow: visible;
        }

        div.dataTables_wrapper {
            width: 100%;
        }

        .hidden-column {
            display: none;
        }

        .btn-with-badge {
            overflow: visible;
        }

        /* Custom styles for the timeline */
        .timeline {
            position: relative;
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .timeline-item {
            width: 100%;
            position: relative;
            padding-left: 50px;
            padding-top: 6px;
            padding-bottom: 6px;
        }

        .timeline-item:last-child {
            padding-bottom: 0;
        }

        .timeline-marker {
            position: absolute;
            top: 15px;
            left: 14px;
            width: 15px;
            height: 15px;
            border-radius: 50%;
            background-color: #ddd;
            border: 2px solid #ddd;
        }

        .timeline-item.completed .timeline-marker {
            background-color: #0ab39c;
            border-color: #0ab39c;
        }

        .timeline-item.completed .timeline-line {
            background-color: #0ab39c;
        }

        .timeline-line {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            left: 20px;
            width: 2px;
            background-color: #ddd;
        }

        .timeline::after {
            background: none !important;
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

        <div class="row">
            <div class="col-lg-12">
                <div>
                    <div class="d-flex">
                        <!-- Nav tabs -->
                        @include('partials.navbar2')
                    </div>
                    <!-- Navbar -->
                    <div class="row pt-4">
                        <div class="col-12">
                            <div class="card">
                                <ul class="nav nav-tabs nav-tabs-custom nav-success" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link py-3 active" id="tab-rule" data-bs-toggle="tab"
                                            href="#pill-process" role="tab">
                                            <i class="ri-survey-line me-1 align-bottom"></i> On Process
                                            @if ($jml_process > 0)
                                                <span class="badge bg-danger">{{ $jml_process }}</span>
                                            @endif
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link py-3" id="tab-benefit" data-bs-toggle="tab" href="#pill-done"
                                            role="tab">
                                            <i class="bi bi-clipboard-check me-1 align-bottom"></i> Done
                                        </a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane active" id="pill-process" role="tabpanel">
                                        <div class="px-3 mt-4 mb-2 align-items-center d-flex">
                                            <button type="button" name="reset" id="reset-process"
                                                class="btn btn-soft-danger waves-effect waves-light btn-sm"><i
                                                    class="ri-refresh-line me-1 align-bottom"></i> Refresh</button>
                                            <button id="multi-approve-btn-process" type="button" title="Approve"
                                                class="btn btn-success btn-sm waves-effect waves-light position-relative btn-with-badge ms-2"
                                                style="display: none">
                                                <i class="ri-check-line"></i>
                                                <span
                                                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                                    <span id="approve-count-process">0</span>
                                                    <span class="visually-hidden">approve selected</span>
                                                </span>
                                            </button>
                                            <button id="multi-print-btn-process" type="button" title="Resume Evaluation"
                                                class="btn btn-success btn-sm waves-effect waves-light position-relative btn-with-badge ms-2"
                                                style="display: none">
                                                <i class="ri-file-text-line"></i>
                                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                                    <span id="print-count-process">0</span>
                                                    <span class="visually-hidden">print selected</span>
                                                </span>
                                            </button>
                                        </div>
                                        <div class="card-body">
                                            <table class="table table-striped bordered display nowrap" style="width:100%"
                                                id="table_process">
                                                <thead>
                                                    <tr>
                                                        <th style="text-align:center"><input type="checkbox" id="checkAllProcess"></th>
                                                        <th scope="col" style="text-align:center">Action</th>
                                                        <th scope="col" style="text-align:center">NIK</th>
                                                        <th scope="col">Name</th>
                                                        <th scope="col">Department</th>
                                                        <th scope="col">Period</th>
                                                        <th scope="col">Purpose</th>
                                                        <th scope="col" style="text-align:center">Status</th>
                                                        <th scope="col">KPI</th>
                                                        <th scope="col">Attitude & Performance</th>
                                                        <th scope="col">Attendance</th>
                                                        <th scope="col">Total</th>
                                                        <th scope="col">Grade</th>
                                                        <th scope="col">Decision</th>
                                                        <th scope="col" class="hidden-column" style="display:none">
                                                            Created At</th>
                                                        <th scope="col" class="hidden-column" style="display:none">Has
                                                            Action</th>
                                                        <th scope="col" class="hidden-column" style="display:none">
                                                            Role</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="pill-done" role="tabpanel">
                                        <div class="px-3 mt-4 mb-2 align-items-center d-flex row">
                                            <div class="col-md-2 mb-2">
                                                <select class="form-control js-example-basic-single" name="tahun" id="tahun" required>
                                                    @foreach($years as $year)
                                                        <option value="{{ $year }}" @if($year == date('Y')) selected @endif>{{ $year }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <button type="button" name="filter" id="filter-done" class="btn btn-soft-secondary waves-effect waves-light btn-sm me-2"><i class="ri-filter-2-line me-1 align-bottom"></i> Filters</button>
                                                <button type="button" name="refresh" id="reset-done" class="btn btn-soft-danger waves-effect waves-light btn-sm me-2"><i class="ri-refresh-line me-1 align-bottom"></i> Reset</button>
                                                <button id="multi-print-btn-done" type="button" title="Resume Evaluation"
                                                    class="btn btn-success btn-sm waves-effect waves-light position-relative btn-with-badge"
                                                    style="display: none">
                                                    <i class="ri-file-text-line"></i>
                                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                                        <span id="print-count-done">0</span>
                                                        <span class="visually-hidden">print selected</span>
                                                    </span>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <table class="table table-striped bordered display nowrap" style="width:100%"
                                                id="table_done">
                                                <thead>
                                                    <tr>
                                                        <th style="text-align:center"><input type="checkbox" id="checkAllDone"></th>
                                                        <th scope="col" style="text-align:center">Action</th>
                                                        <th scope="col" style="text-align:center">NIK</th>
                                                        <th scope="col">Name</th>
                                                        <th scope="col">Department</th>
                                                        <th scope="col">Period</th>
                                                        <th scope="col">Purpose</th>
                                                        <th scope="col" style="text-align:center">Status</th>
                                                        <th scope="col">KPI</th>
                                                        <th scope="col">Attitude & Performance</th>
                                                        <th scope="col">Attendance</th>
                                                        <th scope="col">Total</th>
                                                        <th scope="col">Grade</th>
                                                        <th scope="col">Decision</th>
                                                        <th scope="col" class="hidden-column" style="display:none">
                                                            Created At</th>
                                                        <th scope="col" class="hidden-column" style="display:none">Has
                                                            Action</th>
                                                        <th scope="col" class="hidden-column" style="display:none">
                                                            Role</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="approveConfirmationModal" class="modal fade flip" tabindex="-1"
                        aria-labelledby="approveConfirmationModalLabel" aria-hidden="true" style="display: none;">
                        <div class="modal-dialog modal-dialog-top">
                            <div class="modal-content">
                                <form class="form" id="approveForm"
                                    action="{{ route('profile.evaluation.approveMultiple') }}" method="post">
                                    @csrf
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="approveConfirmationModalLabel">Approve Evaluations
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body text-center p-5">
                                        <p class="text-muted" id="approveMessage">Are you sure you want to approve these
                                            selected
                                            evaluations?</p>
                                        <div id="approve-id-container"></div>
                                        <input type="hidden" name="role" id="current-role">
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-success">Yes, Approve</button>
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">No</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="trackingModal" tabindex="-1" aria-labelledby="trackingModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="trackingModalLabel">Evaluation Step</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="timeline">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
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
    <!--Modal Sertifikat-->
    <div class="modal fade" id="modalSertifikat" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalgridLabel">Preview certificate</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="show-preview-sertifikat">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Status Modals -->
    <div id="statusModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true"
        style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Ribbon Shape -->
                <div class="card ribbon-box shadow-none mb-lg-0">
                    <div class="card-body">
                        <div id="status_judul"></div>
                        <div class="text-end"><button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"> </button></div>

                        <div class="ribbon-content text-muted mt-4">
                            <div id="status_training"></div>
                        </div>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        <!-- Modal Validation Extension File Upload Gambar -->
        <div class="modal fade" id="secondmodal" aria-hidden="true" aria-labelledby="..." tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body text-center p-5">
                        <lord-icon src="https://cdn.lordicon.com/tdrtiskw.json" trigger="loop"
                            colors="primary:#f7b84b,secondary:#405189" style="width:130px;height:130px">
                        </lord-icon>
                        <div class="mt-4 pt-4">
                            <h4>Whoops, ada yang salah!</h4>
                            <p class="text-muted">Maaf hanya menerima file foto yang bertipe .jpg | .jpeg | .png</p>
                            <!-- Toogle to second dialog -->
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal Upload foto -->
        <div class="modal fade" id="modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
            role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg  modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="myModalLabel">Upload Foto Profile</h5>
                    </div>
                    <div class="modal-body">
                        <div data-simplebar style="max-width: 100%;">
                            <div class="img-container">
                                <div class="row">
                                    <div class="col-md-8">
                                        <img id="image" src="">
                                    </div>
                                    <div class="col-md-4">
                                        <div class="preview"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex gap-2 justify-content-end">
                            <button type="button" onclick="cancelAvatar()" class="btn btn-secondary"
                                data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" id="crop">Crop</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--modal konfirmasi upload foto -->
        <div class="modal fade" id="konfirmasimodal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
            role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body text-center p-5">
                        <form class="form" action="{{ route('profile.upload') }}" method="post"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="mt-4 pt-3">
                                <p class="text-muted mb-4">Apakah Anda Yakin Mengubah Foto Profile Anda?</p>
                                <img src="" style="width: 100px;" class="show-image mb-4">
                                <input type="hidden" name="image_base64">
                                <div class="hstack gap-2 justify-content-center">
                                    <button type="submit" class="btn btn-primary">Ya</button>
                                    <button type="button" onclick="cancelAvatar()" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Tidak</button>
                                    <!-- <button class="btn btn-secondary" data-bs-dismiss="modal">
                                                                Tidak
                                                            </button> -->
                                </div>
                            </div>
                        </form>
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
        @include('partials.evaluation.profile.index', [
            'stepsRoute'          => route('profile.evaluation.steps', ':id'),
            'processRoute'        => route('profile.evaluation.process'),
            'doneRoute'           => route('profile.evaluation.done'),
            'countProcessRoute'   => route('profile.evaluation.countprocess'),
            'approveMultipleRoute'=> route('profile.evaluation.approveMultiple'),
            'printTokenRoute'     => route('profile.evaluation.approveMultiple.print.token'),
            'printRoute'          => route('profile.evaluation.approveMultiple.print', ['token' => ':token']),
        ])
        <script>
            var $modal = $('#modal');
            var image = document.getElementById('image');
            var cropper;

            /*------------------------------------------
            --------------------------------------------
            Image Change Event
            --------------------------------------------
            --------------------------------------------*/
            $("body").on("change", ".image", function(e) {
                var files = e.target.files;
                var done = function(url) {
                    image.src = url;
                    $modal.modal('show');
                };

                var reader;
                var file;
                var url;

                if (files && files.length > 0) {
                    file = files[0];

                    if (URL) {
                        done(URL.createObjectURL(file));
                    } else if (FileReader) {
                        reader = new FileReader();
                        reader.onload = function(e) {
                            done(reader.result);
                        };
                        reader.readAsDataURL(file);
                    }
                }
            });

            /*------------------------------------------
            --------------------------------------------
            Show Model Event
            --------------------------------------------
            --------------------------------------------*/
            $modal.on('shown.bs.modal', function() {
                cropper = new Cropper(image, {
                    aspectRatio: 1,
                    viewMode: 3,
                    preview: '.preview'
                });
            }).on('hidden.bs.modal', function() {
                cropper.destroy();
                cropper = null;
            });

            /*------------------------------------------
            --------------------------------------------
            Crop Button Click Event
            --------------------------------------------
            --------------------------------------------*/
            $("#crop").click(function() {
                canvas = cropper.getCroppedCanvas({
                    // width: 160,
                    // height: 160,
                    width: 200,
                    height: 200,
                });

                canvas.toBlob(function(blob) {
                    url = URL.createObjectURL(blob);
                    var reader = new FileReader();
                    reader.readAsDataURL(blob);
                    reader.onloadend = function() {
                        var base64data = reader.result;
                        $("input[name='image_base64']").val(base64data);
                        $(".show-image").show();
                        $(".show-image").attr("src", base64data);
                        $("#modal").modal('toggle');
                    }
                });

                $("#konfirmasimodal").modal("show");
            });
        </script>
        <script type="text/javascript">
            function cancelAvatar() {
                var avatar = document.getElementById('profile-img-file-input');
                avatar.value = '';
                var pre_avatar = {{ Js::from($user->employee->avatar ?? '') }};
                if (!pre_avatar) {
                    document.getElementById("avatar-user").innerHTML =
                        '<img src="/assets/images/users/user-dummy-img.jpg" class="rounded-circle avatar-xl img-thumbnail user-profile-image" alt="user-profile-image">';
                } else {
                    document.getElementById("avatar-user").innerHTML = '<img src="/storage/avatars/' + pre_avatar +
                        '" class="rounded-circle avatar-xl img-thumbnail user-profile-image" alt="user-profile-image">';
                }
            }

            function clearAvatar() {
                var pre_avatar = {{ Js::from($user->employee->avatar ?? '') }};
                if (!pre_avatar) {
                    document.getElementById("avatar-user").innerHTML =
                        '<img src="/assets/images/users/user-dummy-img.jpg" class="rounded-circle avatar-xl img-thumbnail user-profile-image" alt="user-profile-image">';
                } else {
                    document.getElementById("avatar-user").innerHTML = '<img src="/storage/avatars/' + pre_avatar +
                        '" class="rounded-circle avatar-xl img-thumbnail user-profile-image" alt="user-profile-image">';
                }
                var file_avatar = document.getElementById('profile-img-file-input');
                file_avatar.value = '';

                var remove_avatar = document.getElementById('remove_file');
                remove_avatar.value = '1';
            }

            function avatarValidation() {
                //foto profile
                var profile = document.getElementById('profile-img-file-input');
                var pathProfile = profile.value;

                // tipe file yang diizinkan
                var allowedExtensions =
                    /(\.jpg|\.jpeg|\.png|\.JPG|\.JPEG|\.PNG)$/i;

                //masalah modal
                if (!allowedExtensions.exec(pathProfile)) {
                    $('#secondmodal').modal('show');
                    // alert('Invalid file type');
                    profile.value = '';
                    return false;
                }
            }
        </script>
        <script>
            $(document).ready(function() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
            });
        </script>
        <script>
            @if (Session::has('status'))
                toastr.options = {
                    "closeButton": true,
                    "progressBar": true,
                    "positionClass": "toast-bottom-right"
                }
                toastr.success("{{ session('status') }}");
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
    @endsection
