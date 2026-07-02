@extends(request()->is('myservice-desk*') ? 'layouts.master' : 'layouts.general')

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
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
@endsection

@section('content') 
   <div class="container-fluid">
      @if (request()->is('myservice-desk*'))
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
      @endif

      <div class="row">
         <div class="col-lg-12">
            <div>
               <div class="d-flex">
                  <!-- Nav tabs -->
                  @if (request()->is('myservice-desk*'))
                     @include('partials.navbar2')
                  @endif
               </div>
               <!-- Navbar -->
               <div class="row pt-4">
                  <div class="mx-auto" style="max-width: 700px">
                     <div class="card">
                        <div class="card-body">
                           <x-service-desk.form :ticket="$ticket ?? null" :categories="$categories" :employees="$employees" />
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
   {{-- <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
   <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script> --}}
   <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
   <!-- Toastr Notifications-->
   <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
   <!-- Select2 -->
   <script src="{{ url('') }}/assets/libs/adminlte/select2/js/4.1.0/select2.min.js"></script>
@endsection