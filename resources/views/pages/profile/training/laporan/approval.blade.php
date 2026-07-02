@extends('layouts.master')
@section('link')
<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- Required meta tags -->
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css"/>
<!-- Select2-->
<link rel="stylesheet" href="/assets/libs/adminlte/select2/css/4.1.0/select2.min.css">
<!-- App Css-->
<link href="/assets/css/app.min.css" rel="stylesheet" type="text/css" />
<!-- custom Css-->
<link href="/assets/css/custom.min.css" rel="stylesheet" type="text/css" />
<style type="text/css">
    .select2-container--default .select2-selection--multiple .select2-selection__choice__display { color: #000 }
    .select2-container.select2-container--default.select2-container--open  {
        z-index: 5000;
    }
    div.scrollmenu {
        overflow: auto;
        width: 300%;
    }
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
    .section{
        margin-top:150px;
        background:#fff;
        padding:50px 30px;
    }
    .select2, .select2 option {
        font-size:13px;
    }
    #cek_evaluasi {
        opacity: 0;
        width: 0;
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
                    @if(!empty($user->employee->avatar))
                    <div id="avatar-user">
                        <img src="{{ asset('storage/avatars/'.$user->employee->avatar) }}" class="show-image rounded-circle avatar-xl img-thumbnail user-profile-image" alt="user-profile-image">
                    </div>
                    @else
                    <div id="avatar-user">
                        <img src="{{ asset('storage/avatars/user.jpg') }}" class="rounded-circle avatar-xl img-thumbnail user-profile-image" alt="user-profile-image">
                    </div>
                    @endif
                    <div class="avatar-xs p-0 rounded-circle profile-photo-edit">
                        <input onchange="avatarValidation(this);" id="profile-img-file-input" type="file" name="image" class="image profile-img-file-input" accept="image/jpg,image/jpeg,image/png,image/JPG,image/JPEG,image/PNG">
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
                    <div class="hstack text-white-50 gap-1">
                      <div class="me-2">
                        @if(!empty($user->employee->level->nama))
                            <i class="ri-contacts-book-2-line me-1 text-white-75 fs-16 align-middle"></i>
                            {{$user->employee->level->nama}}
                        @endif
                      </div>
                      <div>
                        @if(!empty($user->employee->position->nama))
                            <i class="ri-contacts-book-line me-1 text-white-75 fs-16 align-middle"></i>
                            {{$user->employee->position->nama}}
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
                            <!-- <h4 class="text-white mb-1">{{$user->employee->nik}}</h4>
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
                    {{-- <ul class="nav nav-pills animation-nav profile-nav gap-2 gap-lg-3 gap-md-3 flex-grow-1" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link fs-14 {{ request()->is('profile-home') ? '' : '' }}" href="{{route('profile.home')}}">
                                Home
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link fs-14 {{ request()->is('profile/internal-rule') ? '' : '' }}" href="{{route('profile.internal.rule')}}">
                                Internal Rule
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link fs-14 {{ request()->is('profile/benefit') ? '' : '' }}" href="{{route('profile.benefit')}}">
                                My Benefit
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link fs-14 {{ request()->is('profile/calendar') ? '' : '' }}" href="{{route('profile.calendar')}}">
                                Calendar
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link fs-14 {{ request()->is('profile/medical') ? '' : '' }}" href="{{route('profile.medical')}}">
                                Medical Checkup
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link fs-14 {{ request()->is('profile/pkb') ? '' : '' }}" href="{{route('profile.pkb')}}">
                               PKB
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link fs-14 {{ request()->is('profile/booking') ? '' : '' }}" href="{{route('profile.booking')}}">
                                Booking Room
                            </a>
                        </li>
                        @can('hrd.menu.profile')
                        <li class="nav-item">
                            <a class="nav-link fs-14 {{ request()->is('profile/training') ? '' : '' }}" href="{{route('profile.training')}}">
                               Training
                            </a>
                        </li>
                        @endcan
                    </ul> --}}
                </div>
                <!-- Navbar -->
                <div class="row pt-4">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">                                        
                                        <form id="Formlaporan" action="{{route('profile.training.laporan.approval.store')}}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            @method('put')
                                            <div class="row mb-3">
                                                <div class="col-lg-6">
                                                    <h4 class="text-primary">Formulir Laporan Pelaksanaan Training</h4>
                                                </div>
                                                <div class="col-lg-6">
                                                    <a href="{{ route('profile.back.approval.laporan') }}" class="btn btn-primary btn-label waves-effect waves-light float-end"><i class="ri-arrow-left-circle-line label-icon align-middle fs-16 me-2"></i> Back</a>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-lg-3">
                                                    <label for="tgl_laporan" class="form-label col-form-label">Tanggal Laporan</label>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="input-group">
                                                        <input type="text" name="tgl_laporan" id="tgl_laporan" class="form-control @error("tgl_laporan") is-invalid @enderror" placeholder="Pilih Tanggal" value="{{date('d F, Y', strtotime($query->tgl_laporan))}}" style="Background-color: #eff2f7;" readonly>
                                                        <span class="input-group-text"><i class="ri-calendar-event-line"></i></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-lg-3">
                                                    <label for="nama_peserta" class="form-label col-form-label">Nama</label>
                                                </div>
                                                <div class="col-lg-6">
                                                    <input type="hidden" class="form-control" id="id_record" name="id_record" placeholder="Masukkan Id Record" value="{{$query->id}}">
                                                    <input type="hidden" class="form-control" id="id_peserta" name="id_peserta" placeholder="Masukkan Id" value="{{$query->id_employee}}">
                                                    <input type="text" class="form-control" id="nama_peserta" name="nama_peserta" placeholder="Masukkan Nama" value="{{$query->employee->fullname}}" style="Background-color: #eff2f7;" readonly>
                                                </div>
                                                <div class="col-lg-3">                                        
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-lg-3">
                                                    <label for="bagian" class="form-label col-form-label">Bagian</label>
                                                </div>
                                                <div class="col-lg-6">
                                                    <input type="text" class="form-control" id="bagian" name="bagian" placeholder="Masukkan Bagian" value="{{$query->employee->section->nama ?? '-'}}" style="Background-color: #eff2f7;" readonly>
                                                </div>
                                                <div class="col-lg-3">                                        
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-lg-3">
                                                    <label for="departement" class="form-label col-form-label">Departemen</label>
                                                </div>
                                                <div class="col-lg-6">
                                                    <input type="text" class="form-control" id="departement" name="departement" placeholder="Masukkan Departemen" value="{{$query->employee->department->name ?? '-'}}" style="Background-color: #eff2f7;" readonly>
                                                </div>
                                                <div class="col-lg-3">                                        
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-lg-3">
                                                    <label for="judul" class="form-label col-form-label">Nama Program Pelatihan</label>
                                                </div>
                                                <div class="col-lg-6">
                                                    <input type="text" class="form-control" id="judul" name="judul" placeholder="Masukkan Program Pelatihan" value="{{$query->judul}}" style="Background-color: #eff2f7;" readonly>
                                                </div>
                                                <div class="col-lg-3">                                        
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-lg-3">
                                                    <label for="tgl_pelaksanaan" class="form-label col-form-label">Tanggal Pelaksanaan</label>
                                                </div>
                                                <div class="col-lg-6">
                                                    <input type="hidden" class="form-control" id="tgl_pelaksanaan" name="tgl_pelaksanaan" placeholder="Masukkan Tanggal" value="{{$query->start_date}} to {{$query->end_date}}" style="Background-color: #eff2f7;" readonly>
                                                    <input type="text" class="form-control" id="nama_tgl_pelaksanaan" name="nama_tgl_pelaksanaan" placeholder="Masukkan Tanggal" value="{{date('d, M Y', strtotime($query->start_date))}} to {{date('d, M Y', strtotime($query->end_date))}}" style="Background-color: #eff2f7;" readonly>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="card-body p-4 border-top border-top-dashed">
                                                    <div class="mb-4">
                                                        <label for="no_1" class="form-label">1. Isi Pelatihan?</label>
                                                        <div class="col-lg-10">
                                                            <textarea class="form-control" id="isi_pelatihan" name="isi_pelatihan" rows="3" style="Background-color: #eff2f7;" readonly>{{$query->isi_pelatihan}}</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="mb-4">
                                                        <label for="no_2" class="form-label">2. Apa yang dipelajari?</label>
                                                        <div class="col-lg-10">
                                                            <textarea class="form-control" id="dipelajari" name="dipelajari" rows="3" style="Background-color: #eff2f7;" readonly>{{$query->dipelajari}}</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="mb-4">
                                                        <label for="no_3" class="form-label">3. Bagaimana anda mengimplementasikan materi training dalam pekerjaan?</label>
                                                        <div class="col-lg-10">
                                                            <textarea class="form-control" id="implementasi" name="implementasi" rows="3" style="Background-color: #eff2f7;" readonly>{{$query->implementasi}}</textarea>
                                                        </div>
                                                    </div>  
                                                    @if($user->employee_id == $query->ttd_atasan)                                                  
                                                    <div class="mb-4">
                                                        <label for="no_4" class="form-label">4. Kolom Supervisor (atasan langsung)<span class="text-danger">*</span></label>
                                                        <label for="no_4" class="form-label">(Setelah menerima laporan hasil pelatihan, harapan atasan terhadap bawahannya untuk menindak lanjuti di masa yang akan datang)</label>
                                                        <div class="col-lg-10">
                                                            <textarea class="form-control" id="hasil" name="hasil" rows="3" required></textarea>
                                                        </div>
                                                    </div>
                                                    @else
                                                    <div class="mb-4">
                                                        <label for="no_4" class="form-label">4. Kolom Supervisor (atasan langsung)<span class="text-danger">*</span></label>
                                                        <label for="no_4" class="form-label">(Setelah menerima laporan hasil pelatihan, harapan atasan terhadap bawahannya untuk menindak lanjuti di masa yang akan datang)</label>
                                                        <div class="col-lg-10">
                                                            <textarea class="form-control" id="hasil" name="hasil" rows="3" style="Background-color: #eff2f7;" readonly>{{$query->hasil ?? ''}}</textarea>
                                                        </div>
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="card-body p-4 border-top border-top-dashed">
                                                    <div class="row mb-4">
                                                        @if(!empty($query->sertifikat))
                                                        <div class="col-lg-2">
                                                            <label class="form-label">Dokumen Sertifikat</label>
                                                            <br>
                                                            <a href="{{route('profile.training.sertifikat',encrypt($query->id))}}" target="_blank" class="btn btn-secondary btn-label waves-effect waves-light"><i class="ri-file-download-line label-icon align-middle fs-16 me-2"></i> Sertifikat</a>
                                                        </div>
                                                        @endif
                                                        @if(!empty($query->materi))
                                                        <div class="col-lg-2">
                                                            <label class="form-label">Dokumen Materi</label>
                                                            <br>
                                                            <a href="{{route('profile.training.materi',encrypt($query->id))}}" target="_blank" class="btn btn-secondary btn-label waves-effect waves-light"><i class="ri-file-download-line label-icon align-middle fs-16 me-2"></i> Materi</a>
                                                        </div>
                                                        @endif
                                                        <div class="col-lg-3">
                                                            <label class="form-label">Preview Laporan</label>
                                                            <br>
                                                            <a href="{{route('profile.training.laporan.pdf',encrypt($query->id))}}" target="_blank" class="btn btn-secondary btn-label waves-effect waves-light"><i class="ri-file-download-line label-icon align-middle fs-16 me-2"></i> Laporan</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>                                           
                                            <div class="row" id="form-submit">
                                                <div class="col-lg-12">
                                                    <div class="hstack gap-2 d-print-none" style="justify-content: flex-end;">
                                                        <button type="submit" id="btn-submit" class="btn btn-primary">Approve</button>
                                                    </div>
                                                </div>
                                            </div>                                            
                                        </form>
                                    </div>
                                </div><!-- end card -->
                            </div>
                            <!--end col-->
                        </div>
                        <!--end row-->
                    </div>
                    <!--end col-->
                </div>
                <!--end row-->
            </div>
        </div>
        <!--end col-->
    </div>
    <!--end row-->
</div><!-- container-fluid -->
<!-- Modal Validation Extension File Upload Gambar -->
<div class="modal fade" id="secondmodal" aria-hidden="true" aria-labelledby="..." tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
          <div class="modal-body text-center p-5">
              <lord-icon
                  src="https://cdn.lordicon.com/tdrtiskw.json"
                  trigger="loop"
                  colors="primary:#f7b84b,secondary:#405189"
                  style="width:130px;height:130px">
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
<div class="modal fade" id="modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
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
                <button type="button" onclick="cancelAvatar()" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="crop">Crop</button>
            </div>            
        </div>
    </div>
  </div>
</div>
<!--modal konfirmasi upload foto -->
<div class="modal fade" id="konfirmasimodal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <form class="form" action="{{ route('profile.upload') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="mt-4 pt-3">
                        <p class="text-muted mb-4">Apakah Anda Yakin Mengubah Foto Profile Anda?</p>
                        <img src="" style="width: 100px;" class="show-image mb-4">
                        <input type="hidden" name="image_base64"> 
                        <div class="hstack gap-2 justify-content-center">
                            <button type="submit" class="btn btn-primary">Ya</button>
                            <button type="button" onclick="cancelAvatar()" class="btn btn-secondary" data-bs-dismiss="modal">Tidak</button>
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
<!--Modal staticbackdrop-->
<div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <img src="/assets/images/loading.gif" style="width:120px;height:120px">                    
                <div class="mt-4">
                    <h4 class="mb-3">Please wait...</h4>
                    <h4 class="mb-3">Do not leave this page</h4>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<!-- Select2 -->
<script src="/assets/libs/adminlte/select2/js/4.1.0/select2.min.js"></script>
<script src="/assets/js/pages/select2.init.js"></script>
<!-- profile-setting init js -->
<script src="/assets/js/pages/profile-setting.init.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.js"></script>
<script src="/assets/libs/simplebar/simplebar.min.js"></script>
<script src="/assets/libs/node-waves/waves.min.js"></script>
<!-- form wizard init -->
<script src="/assets/js/pages/form-wizard.init.js"></script>
<!-- Sweetalert -->
<script src="{{asset('assets/libs/sweetalert2/11/sweetalert2.min.js')}}"></script>
<!-- rater-js plugin -->
<script src="/assets/libs/rater-js/index.js"></script>
<!-- rating init -->
<script src="/assets/js/pages/rating.init.js"></script>
@endsection
@section('javascript')
<script>
    $(".select2").select2();
    
    $(document).ready(function() {
        $("#btn-submit").click(function() {
            $("#Formlaporan").submit(function () {
                $('#staticBackdrop').modal('show', true);
            });
        });
    });
</script>
<script>
    var $modal = $('#modal');
    var image = document.getElementById('image');
    var cropper;

    /*------------------------------------------
    --------------------------------------------
    Image Change Event
    --------------------------------------------
    --------------------------------------------*/
    $("body").on("change", ".image", function(e){
        var files = e.target.files;
        var done = function (url) {
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
                reader.onload = function (e) {
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
    $modal.on('shown.bs.modal', function () {
        cropper = new Cropper(image, {
            aspectRatio: 1,
            viewMode: 3,
            preview: '.preview'
        });
    }).on('hidden.bs.modal', function () {
        cropper.destroy();
        cropper = null;
    });

    /*------------------------------------------
    --------------------------------------------
    Crop Button Click Event
    --------------------------------------------
    --------------------------------------------*/
    $("#crop").click(function(){
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
                $(".show-image").attr("src",base64data);
                $("#modal").modal('toggle');
            }
        });

        $("#konfirmasimodal").modal("show");
    });        
</script>
<script type="text/javascript">
    function cancelAvatar(){
      var avatar = document.getElementById('profile-img-file-input');
      avatar.value = '';
      var pre_avatar = {{ Js::from($user->employee->avatar ?? '') }};
      if(!pre_avatar){
        document.getElementById("avatar-user").innerHTML = '<img src="/assets/images/users/user-dummy-img.jpg" class="rounded-circle avatar-xl img-thumbnail user-profile-image" alt="user-profile-image">';
      }else{
        document.getElementById("avatar-user").innerHTML = '<img src="/storage/avatars/'+pre_avatar+'" class="rounded-circle avatar-xl img-thumbnail user-profile-image" alt="user-profile-image">';
      }      
    }

    function clearAvatar(){
        var pre_avatar = {{ Js::from($user->employee->avatar ?? '') }};
        if(!pre_avatar){
            document.getElementById("avatar-user").innerHTML = '<img src="/assets/images/users/user-dummy-img.jpg" class="rounded-circle avatar-xl img-thumbnail user-profile-image" alt="user-profile-image">';
        }else{
            document.getElementById("avatar-user").innerHTML = '<img src="/storage/avatars/'+pre_avatar+'" class="rounded-circle avatar-xl img-thumbnail user-profile-image" alt="user-profile-image">';
        }
        var file_avatar = document.getElementById('profile-img-file-input');
        file_avatar.value = '';

        var remove_avatar = document.getElementById('remove_file');
        remove_avatar.value = '1';
    }

    function avatarValidation(){
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
@endsection