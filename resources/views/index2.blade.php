@extends('layouts.master')
@section('link')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Toastr Notifications-->
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endsection
@section('content')
    <!-- start page -->
<div class="container-fluid">
    <div class="profile-foreground position-relative mx-n4 mt-n4">
        <div class="profile-wid-bg">
            <img src="/assets/images/salonpas-bg.jpg" alt="" class="profile-wid-img" />
        </div>
    </div>
    <div class="pt-4 mb-4 mb-lg-3 pb-lg-4">
        <div class="row g-4">
            <div class="col-auto">
                <div class="avatar-lg">
                    @if(!empty($user->employee->avatar))
                        <img src="{{ asset('storage/avatars/'.$user->employee->avatar) }}" alt="user-img" class="img-thumbnail rounded-circle" />
                    @else
                        <img src="assets/images/users/user-dummy-img.jpg" alt="user-img" class="img-thumbnail rounded-circle" />
                    @endif
                </div>
            </div>
            <!--end col-->
            <div class="col">
                <div class="p-2">
                    <h3 class="text-white mb-1">{{$user->employee->fullname}}</h3>
                    <p class="text-white-75">{{$user->employee->email}}</p>
                    <div class="hstack text-white-50 gap-1">
                      <div class="me-2"><i class="ri-map-pin-user-line me-1 text-white-75 fs-16 align-middle"></i>
                        {{$user->employee->area->name}}
                      </div>
                      <div><i class="ri-building-line me-1 text-white-75 fs-16 align-middle"></i>
                        {{$user->employee->department->name}}
                      </div>
                    </div>
                </div>
            </div>
            <!--end col-->
            <!-- <div class="col-12 col-lg-auto order-last order-lg-0">
                <div class="row text text-white-50 text-center">
                    <div class="col-lg-6 col-4">
                        <div class="p-2">
                            <h4 class="text-white mb-1">{{$user->employee->nik}}</h4>
                            <p class="fs-14 mb-0">NIK</p>
                        </div>
                    </div>
                </div>
            </div> -->
            <!--end col-->

        </div>
        <!--end row-->
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-3">My Profile</h5>
                            <div data-simplebar style="max-width: 453px;">
                                <table class="table table-borderless table-nowrap mb-0">
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div class="flex-grow-1">
                                                    <p class="mb-1 text-muted">NIK :</p>
                                                    <h6 class="text-truncate mb-0">{{$user->employee->nik}}</h6>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="flex-grow-1">
                                                    <p class="mb-1 text-muted">Name :</p>
                                                    <h6 class="text-truncate mb-0">{{$user->employee->fullname}}</h6>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="flex-grow-1">
                                                    <p class="mb-1 text-muted">Area :</p>
                                                    <h6 class="text-truncate mb-0">{{$user->employee->area->name}}</h6>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="flex-grow-1">
                                                    <p class="mb-1 text-muted">Dept. :</p>
                                                    <h6 class="text-truncate mb-0">{{$user->employee->department->name}}</h6>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="flex-grow-1">
                                                    <p class="mb-1 text-muted">Section :</p>
                                                    <h6 class="text-truncate mb-0">{{$user->employee->section->nama}}</h6>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="flex-grow-1">
                                                    <p class="mb-1 text-muted">Position :</p>
                                                    <h6 class="text-truncate mb-0">{{$user->employee->position->nama}}</h6>
                                                </div>
                                            </td>
                                        </tr>                                                   
                                        <tr>
                                            <td>
                                                <div class="flex-grow-1">
                                                    <p class="mb-1 text-muted">Level :</p>
                                                    <h6 class="text-truncate mb-0">{{$user->employee->level->nama}}</h6>
                                                </div>
                                            </td>
                                        </tr>                                                   
                                    </tbody>
                                </table>
                            </div>
                        </div><!-- end card body -->
                    </div><!-- end card -->
                </div>
                <!--end col-->
                <div class="col-lg-9">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-4 col-md-4">
                                    <div class="d-flex mt-4">                                                    
                                        <div class="flex-grow-1 overflow-hidden">
                                            <p class="mb-1 text-muted">KTP :</p>
                                            @if(!empty($user->employee->no_ktp))
                                            <h6 class="mb-0">{{$user->employee->no_ktp}}</h6>
                                            @else
                                            <h6 class="mb-0">-</h6>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <!--end col-->
                                <div class="col-4 col-md-4">
                                    <div class="d-flex mt-4">
                                        <div class="flex-grow-1 overflow-hidden">
                                            <p class="mb-1 text-muted">Alamat :</p>
                                            <h6 class="mb-0">{{$user->employee->addressktp}}</h6>
                                        </div>
                                    </div>
                                </div>
                                <!--end col-->
                                <div class="col-4 col-md-4">
                                    <div class="d-flex mt-4">
                                        <div class="flex-grow-1 overflow-hidden">
                                            <p class="mb-1 text-muted">Phone :</p>
                                            <h6 class="mb-0">{{$user->employee->hp}}</h6>
                                        </div>
                                    </div>
                                </div>
                                <!--end col-->
                            </div>
                            <!--end row-->
                            <div class="row">
                                <div class="col-4 col-md-4">
                                    <div class="d-flex mt-4">                                                    
                                        <div class="flex-grow-1 overflow-hidden">
                                            <p class="mb-1 text-muted">Tgl. Lahir :</p>
                                            <h6 class="mb-0">{{date('d M Y', strtotime($user->employee->birthdate))}}</h6>
                                        </div>
                                    </div>
                                </div>
                                <!--end col-->
                                <div class="col-4 col-md-4">
                                    <div class="d-flex mt-4">
                                        <div class="flex-grow-1 overflow-hidden">
                                            <p class="mb-1 text-muted">Tempat Lahir :</p>
                                            <h6 class="mb-0">{{$user->employee->birthplace}}</h6>
                                        </div>
                                    </div>
                                </div>
                                <!--end col-->
                                <div class="col-4 col-md-4">
                                    <div class="d-flex mt-4">
                                        <div class="flex-grow-1 overflow-hidden">
                                            <p class="mb-1 text-muted">Jenis Kelamin :</p>
                                            <h6 class="mb-0">{{$user->employee->gender}}</h6>
                                        </div>
                                    </div>
                                </div>
                                <!--end col-->
                            </div>
                            <!--end row-->
                            <div class="row">
                                <div class="col-4 col-md-4">
                                    <div class="d-flex mt-4">                                                    
                                        <div class="flex-grow-1 overflow-hidden">
                                            <p class="mb-1 text-muted">Agama :</p>
                                            <h6 class="mb-0">{{$user->employee->religion}}</h6>
                                        </div>
                                    </div>
                                </div>
                                <!--end col-->
                                <div class="col-4 col-md-4">
                                    <div class="d-flex mt-4">
                                        <div class="flex-grow-1 overflow-hidden">
                                            <p class="mb-1 text-muted">Status Perkawinan :</p>
                                            <h6 class="mb-0">{{$user->employee->marital}}</h6>
                                        </div>
                                    </div>
                                </div>
                                <!--end col-->
                                <div class="col-4 col-md-4">
                                    <div class="d-flex mt-4">
                                        <div class="flex-grow-1 overflow-hidden">
                                            <p class="mb-1 text-muted">Tgl. Join :</p>
                                            <h6 class="mb-0">{{date('d M Y', strtotime($user->employee->joindate))}}</h6>
                                        </div>
                                    </div>
                                </div>
                                <!--end col-->
                            </div>
                            <!--end row-->
                            <div class="row">
                                <div class="col-4 col-md-4">
                                    <div class="d-flex mt-4">                                                    
                                        <div class="flex-grow-1 overflow-hidden">
                                            <p class="mb-1 text-muted">Lokasi Kerja :</p>
                                            <h6 class="mb-0">{{$user->employee->work_location}}</h6>
                                        </div>
                                    </div>
                                </div>
                                <!--end col-->
                            </div>
                            <!--end row-->
                        </div>
                        <!--end card-body-->
                    </div><!-- end card -->
                </div>
                <!--end col-->
            </div>
            <!--end row-->
        </div>
        <!--end col-->
    </div>
    <!--end row-->
</div><!-- container-fluid -->
@endsection
@section('script')
<!-- Toastr Notifications-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
@endsection
@section('javascript')
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
    @if(Session::has('status'))
        toastr.options =
        {
            "closeButton" : true,
            "progressBar" : true,
            "positionClass": "toast-bottom-right"
        }
        toastr.success("{{ session('status') }}");
    @endif
</script>
@endsection
