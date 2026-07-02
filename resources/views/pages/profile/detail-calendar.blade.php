@extends('layouts.master')
@section('link')
<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- Select2-->
<link rel="stylesheet" href="/assets/libs/adminlte/select2/css/4.1.0/select2.min.css">
<!-- fullcalendar css -->
<link href="/assets/libs/fullcalendar/main.min.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css"/>
<style type="text/css">
    /* body{
        background: #f7fbf8; 
    } */
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
    #calendar .fc-day-sun {
        color: red;
    }
    #calendar .fc-day-sat {
        color: blue;
    }
    table, td {
        border: 0px solid black;
    }
    p.judul-plan {
        text-transform: lowercase;
    } 

    p.judul-plan::first-letter {
        text-transform: uppercase;
    }
    td.first {
        width: 10%;
    }
    td.second {
        width: 1%;
    }
    td.three {
        width: 50%;
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
                        <!-- <li class="nav-item">
                            <a class="nav-link fs-14 {{ request()->is('profile/event') ? '' : '' }}" href="{{route('comingsoon')}}">
                                Event
                            </a>
                        </li> -->
                        <li class="nav-item">
                            <a class="nav-link fs-14 {{ request()->is('profile/medical') ? '' : '' }}" href="{{route('profile.medical')}}">
                                Medical Checkup
                            </a>
                        </li>
                    </ul>
                </div>
                <!-- Navbar -->
                <div class="card mt-4">                    
                    <form action="{{ route('profile.calendar.detail', $kode) }}" method="GET">
                        @csrf
                        <div class="row mt-3 px-3" style="justify-content: space-between;">
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <select class="form-control select2" id="select_type" name="select_type">
                                        @foreach($data_type as $key => $value)
                                            @if($key == $type)
                                            <option value="{{$key}}" selected>{{$value}}</option>
                                            @else
                                            <option value="{{$key}}">{{$value}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <button type="submit" name="filter" id="filter" class="btn btn-soft-primary waves-effect waves-light btn-sm"><i class="ri-filter-2-line me-1 align-bottom"></i> Filters</button>
                                <a href="{{ route('profile.calendar.detail', $kode) }}" class="btn btn-soft-danger waves-effect waves-light btn-sm"><i class="ri-refresh-line me-1 align-bottom"></i> Reset</a>
                            </div>
                        
                            <div class="col-lg-3">
                                <a href="{{ route('profile.calendar') }}" class="btn btn-primary btn-label btn-sm waves-effect waves-light float-end"><i class="ri-arrow-left-fill label-icon align-middle fs-16 me-2"></i> Back</a>
                            </div>
                        </div>
                    </form>
                    <!--end row-->
                    <div class="row mt-4 px-3">
                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-12">
                                    <div class="row">
                                        <div class="col-lg-3">
                                            <div class="card card-h-100">
                                                <div class="card-body">
                                                    <!-- <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#modal-event"><i class="mdi mdi-plus"></i> Create New Event</button> -->
                                                    <button class="btn btn-primary w-100"><i class="ri-calendar-todo-fill align-bottom me-1"></i> Calendar {{$tahun}}</button>
                                                    <div id="external-events">
                                                        <br>
                                                        <p class="text-muted">Note :</p>
                                                        <div class="external-event fc-event bg-soft-danger text-danger" data-class="bg-soft-danger">
                                                            <i class="mdi mdi-checkbox-blank-circle font-size-11 me-2"></i>
                                                            National Leave
                                                        </div>
                                                        <div class="external-event fc-event bg-soft-warning text-warning" data-class="bg-soft-warning">
                                                            <i class="mdi mdi-checkbox-blank-circle font-size-11 me-2"></i>
                                                            National Holiday
                                                        </div>
                                                        <div class="external-event fc-event bg-soft-info text-info" data-class="bg-soft-info">
                                                            <i class="mdi mdi-checkbox-blank-circle font-size-11 me-2"></i>
                                                            Company Leave
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                            <div>
                                                <h5 class="mb-1">Upcoming Events</h5>
                                                <p class="text-muted">Don't miss scheduled events</p>
                                                <div class="pe-2 me-n1 mb-3" data-simplebar style="height: 150px">
                                                    <div id="upcoming-event-list"></div>
                                                </div>
                                            </div>
                                            <a href="{{ route('profile.calendar.download', $kode) }}">
                                                <div class="card">
                                                    <div class="card-body bg-soft-info">
                                                        <div class="d-flex">
                                                            <div class="flex-shrink-0">
                                                                <i data-feather="calendar" class="text-info icon-dual-info"></i>
                                                            </div>
                                                            <div class="flex-grow-1 ms-3">
                                                                <h6 class="fs-15 mt-1">Download Calendar</h6>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end card-->
                                            </a>
                                        </div> <!-- end col-->

                                        <div class="col-lg-9">
                                            <div class="card card-h-100">
                                                <div class="card-body">
                                                    <div id="calendar" style="width: 100%;"></div>
                                                </div>
                                            </div>
                                        </div><!-- end col -->
                                    </div>
                                    <!--end row-->

                                    <div style='clear:both'></div>

                                    <!-- Add New Event MODAL -->
                                    <div class="modal fade" id="event-modal" tabindex="-1">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0">
                                                <div class="modal-header p-3 bg-soft-info">
                                                    <h5 class="modal-title" id="modal-title">Event</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                                                </div>
                                                <div class="modal-body p-4">
                                                    <form action="#" class="form needs-validation" name="event-form" id="form-event" method="POST">
                                                        <div class="text-end">
                                                            <a href="#" class="btn btn-sm btn-soft-primary" id="edit-event-btn" data-id="edit-event" onclick="editEvent(this)" role="button">Edit</a>
                                                        </div>
                                                        <div class="event-details">
                                                            <div class="d-flex mb-2">
                                                                <div class="flex-grow-1 d-flex align-items-center">
                                                                    <div class="flex-shrink-0 me-3">
                                                                        <i class="ri-calendar-event-line text-muted fs-16"></i>
                                                                    </div>
                                                                    <div class="flex-grow-1">
                                                                        <h6 class="d-block fw-semibold mb-0" id="event-start-date-tag"></h6>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="d-flex align-items-center mb-2">
                                                                <div class="flex-shrink-0 me-3">
                                                                    <i class="ri-time-line text-muted fs-16"></i>
                                                                </div>
                                                                <div class="flex-grow-1">
                                                                    <h6 class="d-block fw-semibold mb-0"><span id="event-timepicker1-tag"></span> - <span id="event-timepicker2-tag"></span></h6>
                                                                </div>
                                                            </div>
                                                            <div class="d-flex align-items-center mb-2">
                                                                <div class="flex-shrink-0 me-3">
                                                                    <i class="ri-map-pin-line text-muted fs-16"></i>
                                                                </div>
                                                                <div class="flex-grow-1">
                                                                    <h6 class="d-block fw-semibold mb-0"> <span id="event-location-tag"></span></h6>
                                                                </div>
                                                            </div>
                                                            <div class="d-flex mb-3">
                                                                <div class="flex-shrink-0 me-3">
                                                                    <i class="ri-discuss-line text-muted fs-16"></i>
                                                                </div>
                                                                <div class="flex-grow-1">
                                                                    <p class="d-block text-muted mb-0" id="event-description-tag"></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row event-form">
                                                            <div class="col-12" hidden>
                                                                <div class="mb-3">
                                                                    <label class="form-label">Type</label>
                                                                    <select class="form-select d-none" name="category" id="event-category">
                                                                        <option value="bg-soft-danger">Danger</option>
                                                                        <option value="bg-soft-success">Success</option>
                                                                        <option value="bg-soft-primary">Primary</option>
                                                                        <option value="bg-soft-info">Info</option>
                                                                        <option value="bg-soft-dark">Dark</option>
                                                                        <option value="bg-soft-warning">Warning</option>
                                                                    </select>
                                                                    <div class="invalid-feedback">Please select a valid event category</div>
                                                                </div>
                                                            </div>
                                                            <!--end col-->
                                                        </div>
                                                        <!--end row-->
                                                        <div class="hstack gap-2 justify-content-end">
                                                            <button type="button" class="btn btn-soft-danger" id="btn-delete-event"><i class="ri-close-line align-bottom"></i> Delete</button>
                                                            <button type="submit" class="btn btn-success" id="btn-save-event">Add Event</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div> <!-- end modal-content-->
                                        </div> <!-- end modal dialog-->
                                    </div> <!-- end modal-->
                                    <!-- end modal-->
                                </div>
                            </div> <!-- end row-->
                        </div>
                        <!--end col-->
                    </div>
                    <div id="modal-view-calendar" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header p-3 bg-soft-info">
                                    <h5 class="modal-title"><span id="judul-modal"></span></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>                
                                    <div class="modal-body">
                                        <table style="width:100%">
                                            <tr>
                                                <td class="first">
                                                    <div class="d-flex mb-2">
                                                        <div class="flex-grow-1 d-flex align-items-center fw-semibold">
                                                            Date
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="second">
                                                    <div class="d-flex mb-2">
                                                        <div class="flex-grow-1 d-flex align-items-center fw-semibold">
                                                            :
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="three">
                                                    <div class="d-flex mb-2">
                                                        <div class="flex-grow-1 d-flex align-items-center">
                                                            <div class="flex-shrink-0 me-3">
                                                                <i class="ri-calendar-event-line text-muted fs-16"></i>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <h6 class="d-block mb-0"
                                                                    id="view_tanggal"></h6>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="first">
                                                    <div class="d-flex mb-2">
                                                        <div class="flex-grow-1 d-flex align-items-center fw-semibold">
                                                            Event
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="second">
                                                    <div class="d-flex mb-2">
                                                        <div class="flex-grow-1 d-flex align-items-center fw-semibold">
                                                            :
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="three">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <div class="flex-shrink-0 me-3">
                                                            <i class="ri-todo-line text-muted fs-16"></i>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <h6 class="d-block fw-semibold mb-0"> <span
                                                                    id="view_event"></span>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>                                
                                            <tr>
                                                <td class="first">
                                                    <div class="d-flex mb-2">
                                                        <div class="flex-grow-1 d-flex align-items-center fw-semibold">
                                                            Detail
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="second">
                                                    <div class="d-flex mb-2">
                                                        <div class="flex-grow-1 d-flex align-items-center fw-semibold">
                                                            :
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="three">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <div class="flex-shrink-0 me-3">
                                                            <i class="ri-todo-line text-muted fs-16"></i>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <h6 class="d-block fw-semibold mb-0"> <span
                                                                    id="view_detail"></span>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>                                
                                        </table>                                                
                                    </div>
                                    <div class="modal-footer">
                                    </div>                                  
                            </div><!-- /.modal-content -->
                        </div><!-- /.modal-dialog -->
                    </div><!-- /.modal view calendar -->
                </div>
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
<!-- Select2 -->
<script src="/assets/libs/adminlte/select2/js/4.1.0/select2.min.js"></script>
<!-- calendar min js -->
<script src="/assets/libs/fullcalendar/main.min.js"></script>
<!-- profile-setting init js -->
<script src="/assets/js/pages/profile-setting.init.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.js"></script>
@endsection
@section('javascript')
<script>
    $(function () {    
        $('.select2').select2();
    });
</script>
<script>
var start_date = document.getElementById("tanggal");
var data_all = {{Js::from($data_all)}};
var tahun = {{ Js::from($tahun)}};
start_year = tahun;
let next_year = parseInt(start_year)+1;
end_year = next_year.toString();

function eventClicked() {
    $('#modal-view-calendar').modal('show', true);
}

function upcomingEvent(e) {
    e.reverse(function(e, t) {
        return new Date(e.start) - new Date(t.start)
    }), document.getElementById("upcoming-event-list").innerHTML = null, Array.from(e).forEach(function(e) {
        var t = e.title,
            n = (l = e.end ? (endUpdatedDay = new Date(e.end)).setDate(endUpdatedDay.getDate() - 1) : l) || void 0;
        n = "Invalid Date" == n || null == n ? null : (a = new Date(n).toLocaleDateString("en", {
            year: "numeric",
            month: "numeric",
            day: "numeric"
        }), new Date(a).toLocaleDateString("en-GB", {
            day: "numeric",
            month: "short",
            year: "numeric"
        }).split(" ").join(" "));
        (e.start ? str_dt(e.start) : null) === (l ? str_dt(l) : null) && (n = null);
        var a = e.start,
            d = (a = "Invalid Date" === a || void 0 === a ? null : (d = new Date(a).toLocaleDateString("en", {
                year: "numeric",
                month: "numeric",
                day: "numeric"
            }), new Date(d).toLocaleDateString("en-GB", {
                day: "numeric",
                month: "short",
                year: "numeric"
            }).split(" ").join(" ")), n ? " to " + n : ""),
            n = e.className.split("-"),
            i = e.description || "",
            e = tConvert(getTime(e.start)),
            l = (e == (l = tConvert(getTime(l))) && (e = "Full day event", l = null), l ? " to " + l : "");
        u_event = "<div class='card mb-3'><div class='card-body'><div class='d-flex mb-3'><div class='flex-grow-1'><i class='mdi mdi-checkbox-blank-circle me-2 text-" + n[2] + "'></i><span class='fw-medium'>" + a + d + " </span></div>                                <div class='flex-shrink-0'></div>                            </div>                            <h6 class='card-title fs-14'> " + t + "</h6>                            <p class='text-muted text-truncate-two-lines mb-0'> " + i + "</p>                        </div>                    </div>", document.getElementById("upcoming-event-list").innerHTML += u_event
    })
}

function getTime(e) {
    if (null != (e = new Date(e)).getHours()) return e.getHours() + ":" + (e.getMinutes() ? e.getMinutes() : 0)
}

function tConvert(e) {
    var e = e.split(":"),
        t = e[0],
        e = e[1],
        n = 12 <= t ? "PM" : "AM";
    return (t = (t %= 12) || 12) + ":" + (e = e < 10 ? "0" + e : e) + " " + n
}
document.addEventListener("DOMContentLoaded", function() {
    var g = new bootstrap.Modal(document.getElementById("event-modal"), {
            keyboard: !1
        }),
        d = (document.getElementById("event-modal"), document.getElementById("modal-title")),
        i = document.getElementById("form-event"),
        v = null,
        p = document.getElementsByClassName("needs-validation"),
        e = new Date,
        t = e.getDate(),
        n = e.getMonth(),
        e = e.getFullYear(),
        a = FullCalendar.Draggable,
        l = document.getElementById("external-events"),
        y = data_all,
        e = (new a(l, {
            itemSelector: "#",
            eventData: function(e) {
                return {
                    id: Math.floor(11e3 * Math.random()),
                    title: e.innerText,
                    allDay: !0,
                    start: new Date,
                    className: e.getAttribute("data-class")
                }
            }
        }), document.getElementById("calendar"));

    function o(e) {
        document.getElementById("form-event").reset(), document.getElementById("btn-delete-event").setAttribute("hidden", !0), g.show(), i.classList.remove("was-validated"), i.reset(), v = null, d.innerText = "Add Event", newEventData = e, document.getElementById("edit-event-btn").setAttribute("data-id", "new-event"), document.getElementById("edit-event-btn").click(), document.getElementById("edit-event-btn").setAttribute("hidden", !0)
    }

    function r() {
        return 768 <= window.innerWidth && window.innerWidth < 1200 ? "timeGridWeek" : window.innerWidth <= 768 ? "listMonth" : "dayGridMonth"
    }
    var c = new Choices("#event-category", {
            searchEnabled: !1
        }),
        E = new FullCalendar.Calendar(e, {
            // timeZone: "local",
            validRange: {
                start: start_year,
                end: end_year
            },
            height: 535,
            editable: !1,
            droppable: !1,
            selectable: !0,
            navLinks: !0,
            initialView: r(),
            themeSystem: "bootstrap",
            headerToolbar: {
                left: "prev,next today",
                center: "title",
                right: "dayGridMonth,listMonth"
            },
            windowResize: function(e) {
                var t = r();
                E.changeView(t)
            },
            eventResize: function(t) {
                var e = y.findIndex(function(e) {
                    return e.id == t.event.id
                });
                y[e] && (y[e].title = t.event.title, y[e].start = t.event.start, y[e].end = t.event.end || null, y[e].allDay = t.event.allDay, y[e].className = t.event.classNames[0], y[e].description = t.event._def.extendedProps.description || "", y[e].location = t.event._def.extendedProps.location || ""), upcomingEvent(y)
            },
            eventClick: function(e) {
                document.getElementById("judul-modal").innerHTML = e.event.title;
                var id_calendar = e.event.id;
                //ajax view calendar
                $.ajax({
                    url: "{{ route('calendar.view') }}",
                    type: "POST",
                    data: {
                        id_calendar: id_calendar,
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    success: function(result){
                        //view
                        document.getElementById("view_tanggal").innerHTML = result.tgl;
                        document.getElementById("view_event").innerHTML = result.event;
                        document.getElementById("view_detail").innerHTML = result.leave['nama'];
                    }
                });
                eventClicked(), i.reset();
            },
            events: y,
            eventReceive: function(e) {
                e = {
                    id: parseInt(e.event.id),
                    title: e.event.title,
                    start: e.event.start,
                    allDay: e.event.allDay,
                    className: e.event.classNames[0]
                };
                y.push(e), upcomingEvent(y)
            },
            eventDrop: function(t) {
                var e = y.findIndex(function(e) {
                    return e.id == t.event.id
                });
                y[e] && (y[e].title = t.event.title, y[e].start = t.event.start, y[e].end = t.event.end || null, y[e].allDay = t.event.allDay, y[e].className = t.event.classNames[0], y[e].description = t.event._def.extendedProps.description || "", y[e].location = t.event._def.extendedProps.location || ""), upcomingEvent(y)
            }
        });
    E.render(), upcomingEvent(y), i.addEventListener("submit", function() {
        upcomingEvent(y)
    }), document.getElementById("btn-delete-event").addEventListener("click", function(e) {
        if (v) {
            for (var t = 0; t < y.length; t++) y[t].id == v.id && (y.splice(t, 1), t--);
            upcomingEvent(y), v.remove(), v = null, g.hide()
        }
    }), document.getElementById("btn-new-event").addEventListener("click", function(e) {
         o(), document.getElementById("edit-event-btn").setAttribute("data-id", "new-event"), document.getElementById("edit-event-btn").click(), document.getElementById("edit-event-btn").setAttribute("hidden", !0)
    })
});
var str_dt = function(e) {
    var e = new Date(e),
        t = "" + ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"][e.getMonth()],
        n = "" + e.getDate(),
        e = e.getFullYear();
    return t.length < 2 && (t = "0" + t), [(n = n.length < 2 ? "0" + n : n) + " " + t, e].join(",")
};
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