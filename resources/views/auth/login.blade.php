@extends('layouts.auth')

@section('content')
  <div class="col-lg-12">
    <div class="card overflow-hidden border-0">
      <div class="row g-0">
        <div class="col-lg-6">
          <div class="p-lg-5 p-4 auth-one-bg h-100">
            <!-- <div class="bg-overlay"></div> -->
            <div class="position-relative h-100 d-flex flex-column">
              <div class="mt-auto">
                <div id="qoutescarouselIndicators" class="carousel slide" data-bs-ride="carousel">
                  <div class="carousel-indicators">
                  </div>
                  <div class="carousel-inner text-center text-white-50 pb-5" style="height:180px;">
                  </div>
                </div>
                <!-- end carousel -->
              </div>
            </div>
          </div>
        </div>
        <!-- end col -->

        <div class="col-lg-6">
          <div class="p-lg-5 p-4">
            <div class="text-center">
              <img src="{{  url('') }}/assets/images/logosalonpas.jpg" alt="" height="60">
              <p class="text-muted mt-3">Sign in to continue to Intranet.</p>
            </div>

            <div class="mt-6">
              <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-3">
                  <label for="email" class="form-label">Email</label>
                  {{-- <select class="form-control select2 @error('email') is-invalid @enderror" required name="email" id="email" data-placeholder="Pilih Nama Anda">
                                    <option selected="true" disabled="true"></option>
                                    @foreach ($users as $emp)
                                        <option value="{{ $emp->employee->email ?? $emp->email }}" >{{ $emp->employee->fullname ?? $emp->name }} -- {{ $emp->employee->nik ?? '111111' }}</option>
                                    @endforeach
                                </select> --}}
                  <input type="email" name="email" id="email" class="form-control" placeholder="Enter email"
                    required>
                  @error('email')
                    <span class="invalid-feedback" role="alert">
                      <strong>{{ $message }}</strong>
                    </span>
                  @enderror
                </div>

                <div class="mb-3">
                  <div class="float-end">
                    <a href="{{ route('password.request') }}" class="text-muted">Forgot password?</a>
                  </div>
                  <label class="form-label" for="password-input">Password</label>
                  <div class="position-relative auth-pass-inputgroup mb-3">
                    <input type="password" name="password" class="form-control pe-5 password-input"
                      placeholder="Enter password" id="password-input">
                    <button
                      class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon"
                      type="button" id="password-addon"><i class="ri-eye-fill align-middle"></i></button>
                  </div>
                </div>

                <!-- <div class="form-check">
                                  <input class="form-check-input" type="checkbox" value="" id="auth-remember-check">
                                  <label class="form-check-label" for="auth-remember-check">Remember me</label>
                              </div> -->

                <div class="mt-4">
                  <button class="btn btn-success w-100" type="submit">Sign In</button>
                </div>
              </form>
            </div>

            <div class="mt-3 text-center">
              <p class="mb-2">Don't have an account ? Please Contact IT Administrator</p>
              @php
                $version = \App\Models\About::latest()->first();
                $versionNumber = $version ? $version->version : 'N/A';
              @endphp
              <p  class="mb-0">Version {{ $versionNumber }}</p>
            </div>
          </div>
        </div>
        <!-- end col -->
      </div>
      <!-- end row -->
    </div>
    <!-- end card -->
  </div>
  <!-- end col -->
@endsection
