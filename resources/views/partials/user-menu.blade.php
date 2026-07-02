<div
  class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-275px"
  data-kt-menu="true">
  <div class="menu-item px-3">
    <div class="menu-content d-flex align-items-center px-3">
      <div class="symbol symbol-50px me-5">
        <img alt="Logo" src="{{ auth()->user()->image() }}" />
      </div>
      <div class="d-flex flex-column">
        <div class="fw-bold d-flex align-items-center fs-5">
          {{ Auth::user()->name }}
        </div>
        <a href="#" class="fw-semibold text-muted text-hover-primary fs-7">
          {{ Auth::user()->email }} </a>
      </div>
    </div>
  </div>
  <div class="separator my-2"></div>
  @can('hrd.menu.profile')
  <div class="menu-item px-5">
    <a href="{{ route('profile.index') }}" class="menu-link px-5">
      My Profile
    </a>
  </div>
  @endcan

  <div class="menu-item px-5">
    <a class="menu-link px-5 text-danger" href="{{ route('logout') }}"
      onclick="event.preventDefault();
                            document.getElementById('logout-form').submit();">
      {{ __('Logout') }}
    </a>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
      @csrf
    </form>
  </div>
</div>

<script></script>
