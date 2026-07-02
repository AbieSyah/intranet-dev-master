@extends('layouts.master')
@section('link')
<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- Datatables-->
<link href="/assets/libs/Datatables/DataTables-1.13.1/css/dataTables.bootstrap5.css" rel="stylesheet" type="text/css" />
<link href="/assets/libs/Datatables/Buttons-2.3.3/css/buttons.dataTables.css" rel="stylesheet" type="text/css" />
<link href="/assets/libs/Datatables/Responsive-2.4.0/css/responsive.bootstrap.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css"/>
<style type="text/css">
    body{
        background: #f7fbf8; 
    }
    /* h1{
        font-weight: bold;
        font-size:23px;
    } */
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
    /* input{
        margin-top:40px;
    } */
    .section{
        margin-top:150px;
        background:#fff;
        padding:50px 30px;
    }
    /* .modal-lg{
        max-width: 1000px !important;
    } */
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
                    <ul class="nav nav-pills animation-nav profile-nav gap-2 gap-lg-3 gap-md-3 flex-grow-1" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link fs-14 {{ request()->is('profile/index') ? '' : '' }}" href="{{route('profile')}}">
                                Profile
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link fs-14 {{ request()->is('profile/internal-rules') ? '' : '' }}" href="{{route('profile.internal.rule')}}">
                                Internal Rules
                            </a>
                        </li>
                        <!-- <li class="nav-item">
                            <a class="nav-link fs-14 {{ request()->is('profile/job-description') ? '' : '' }}" href="{{route('comingsoon')}}">
                                Job Description
                            </a>
                        </li> -->
                        <li class="nav-item">
                            <a class="nav-link fs-14 {{ request()->is('profile/calendar') ? '' : 'active' }}" href="{{route('profile.calendar')}}">
                                Calendar
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link fs-14 {{ request()->is('profile/event') ? '' : '' }}" href="{{route('comingsoon')}}">
                                Event
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link fs-14 {{ request()->is('profile/medical') ? '' : '' }}" href="{{route('profile.medical')}}">
                                Medical Checkup
                            </a>
                        </li>
                    </ul>
                </div>
                <!-- Navbar -->
                <div class="row pt-4">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header align-items-center d-flex">
                                <h5 class="card-title">Calendar</h5>
                            </div><!-- end card header -->
                            <div class="card-body">
                                <table class="table table-striped bordered" id="table_calendar">
                                    <thead>
                                        <tr>
                                        <th scope="col" style="text-align:center">NO</th>
                                        <th scope="col" style="text-align:center">TAHUN</th>
                                        <th scope="col" style="text-align:center">ACTION</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
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
@endsection

@section('script')
<!-- Datatables -->
<script src="/assets/libs/Datatables/DataTables-1.13.1/js/jquery.dataTables.min.js"></script>
<script src="/assets/libs/Datatables/DataTables-1.13.1/js/dataTables.bootstrap5.min.js"></script>
<script src="/assets/libs/Datatables/Responsive-2.4.0/js/dataTables.responsive.min.js"></script>
<script src="/assets/js/pages/datatables.init.js"></script>
<!-- profile-setting init js -->
<script src="/assets/js/pages/profile-setting.init.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.js"></script>
@endsection
@section('javascript')
<script>
    $(document).ready(function () {
        $('#table_calendar').DataTable({
        responsive: true,
        autoWidth: false,
        stateSave: true,
        processing: true,
        serverSide: true,
        ajax: "{{ route('profile.calendar') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex' , "className": "text-center"},
                {data: 'tahun', name: 'tahun' , "className": "text-center"},
                {data: 'action', name: 'action', "className": "text-center", orderable: false, searchable: false},
            ]
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