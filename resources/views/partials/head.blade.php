<header>
    <div class="container-fluid">
        <div class="profile-foreground position-relative mx-n4 mt-n4">
            <div class="profile-wid-bg">
                <img src="{{  url('') }}/assets/images/salonpas-bg.jpg" alt="" class="profile-wid-img" />
            </div>
        </div>
        <div class="pt-4 mb-4 mb-lg-3 pb-lg-4">
            <div class="row g-4">
                <div class="col-auto">
                    <div class="avatar-lg">
                        @if($user->employee && !empty($user->employee->avatar) && \Storage::disk('public')->exists('avatars/'.auth()->user()->employee->avatar))
                            <img src="{{ asset('storage/avatars/'.$user->employee->avatar) }}" alt="user-img" class="img-thumbnail rounded-circle" />
                        @else
                            <img src="{{  url('') }}/assets/images/users/user-dummy-img.jpg" alt="user-img" class="img-thumbnail rounded-circle" />
                        @endif
                    </div>
                </div>
                <!--end col-->
                <div class="col">
                    <div class="p-2">
                        <h3 class="text-white mb-1">{{$user->employee?->fullname ?? $user->name}}</h3>
                        <p class="text-white-75">{{$user->employee?->email ?? $user->email}}</p>
                        <div class="hstack text-white-50 gap-1">
                        <div class="me-2"><i class="ri-map-pin-user-line me-1 text-white-75 fs-16 align-middle"></i>
                            {{$user->employee?->area?->name ?? 'N/A'}}
                        </div>
                        <div><i class="ri-building-line me-1 text-white-75 fs-16 align-middle"></i>
                            {{$user->employee?->department?->name ?? 'N/A'}}
                        </div>
                        </div>
                    </div>
                </div>
                <!--end col-->
            </div>
            <!--end row-->
        </div>        
        <div class="page-content">
                    <!-- start page title -->
                    @yield('content')                    
                    <!-- end page title -->
                
                <!-- container-fluid -->
            </div>
    </div><!-- container-fluid -->
</header>