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
                        @include('partials.evaluation.profile.eval', [
                            'backRoute'   => route('profile.evaluation'),
                            'reviceRoute' => route('profile.evaluate.revice', ['token' => $token]),
                            'storeRoute'  => route('profile.evaluate.store', ['token' => $token]),
                        ])
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
        <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
        <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    @endsection

    @section('javascript')
        <script>
            $(document).ready(function() {
                function addAttachmentRow(defaultName = '') {
                    const currentTotalRows = $('#attachment-list tr').length;
                    const newCounter = currentTotalRows + 1;
                    const newRow = `
                    <tr class="new-attachment-row">
                        <th scope="row" class="attachment-id">${newCounter}</th>
                        <td>
                            <div class="mb-2">
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    name="new_attachment_names[]" 
                                    placeholder="e.g., Attendance List" 
                                    value="${defaultName}" 
                                    required>
                            </div>
                        </td>
                        <td>
                            <div class="mb-2">
                                <input type="file" class="form-control" name="new_attachments[]" required>
                            </div>
                        </td>
                        <td class="attachment-removal">
                            <button type="button" class="btn btn-danger remove-new-attachment-btn">Delete</button>
                        </td>
                    </tr>`;
                    $('#attachment-list').append(newRow);
                    updateAttachmentNumbers();
                }

                function updateAttachmentNumbers() {
                    $('#attachment-list tr').each(function(index) {
                        $(this).find('.attachment-id').text(index + 1);
                    });
                }

                $('#add-attachment-item').on('click', function() {
                    addAttachmentRow();
                });

                $(document).on('click', '.remove-new-attachment-btn', function() {
                    $(this).closest('tr').remove();
                    updateAttachmentNumbers();
                });

                $(document).on('click', '.remove-existing-attachment-btn', function() {
                    const attachmentId = $(this).data('attachment-id');
                    $(this).closest('tr').remove();
                    $('#evalForm').append(
                        `<input type="hidden" name="deleted_attachments[]" value="${attachmentId}">`);
                    updateAttachmentNumbers();
                });
                updateAttachmentNumbers();
                const totalExistingRows = $('#attachment-list tr').length;
                if (totalExistingRows === 0) {
                    addAttachmentRow('KPI');
                    addAttachmentRow('Attendance');
                }

                // Count Character
                function updateCharacterCount(textareaId, countId, maxLength) {
                    const textarea = document.getElementById(textareaId);
                    const countSpan = document.getElementById(countId);
                    if (!textarea || !countSpan) return;
                    const updateCount = function() {
                        const currentLength = textarea.value.length;
                        const remaining = maxLength - currentLength;
                        countSpan.textContent = remaining;
                        if (remaining < 0) {
                            countSpan.classList.add('text-warning');
                        } else {
                            countSpan.classList.remove('text-warning');
                        }
                    };
                    textarea.addEventListener('input', updateCount);
                    updateCount(); 
                }

                const inputToMonitor = [
                    { id: 'ap_managerial_c', max: 60 },
                    { id: 'ap_ability_response_c', max: 60 },
                    { id: 'ap_leadership_c', max: 60 },
                    { id: 'ap_accuracy_c', max: 60 },
                    { id: 'ap_capability_c', max: 60 },
                    { id: 'ap_initiative_c', max: 60 },
                    { id: 'ap_kaizen_c', max: 60 },
                    { id: 'ap_responsibility_c', max: 60 },
                    { id: 'ap_discipline_c', max: 60 },
                    { id: 'ap_cooperation_c', max: 60 },
                    { id: 'kpi_c', max: 60 },
                    { id: 'attendance_c', max: 60 },
                    { id: 'positive', max: 189 },
                    { id: 'weakness', max: 189 }, 
                ];
                inputToMonitor.forEach(item => {
                    updateCharacterCount(item.id, `count_${item.id}`, item.max); 
                });
            });
        </script>
        @if (session('success'))
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: '{{ session('success') }}',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });
            </script>
        @endif

        @if (session('error'))
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: '{{ session('error') }}',
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'OK'
                });
            </script>
        @endif

        @if ($errors->any())
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    html: `{!! implode('<br>', $errors->all()) !!}`,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'OK'
                });
            </script>
        @endif
    @endsection
