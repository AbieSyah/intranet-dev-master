<div class="app-sidebar-menu overflow-hidden flex-column-fluid">
  <div id="kt_app_sidebar_menu_wrapper" class="app-sidebar-wrapper">
    <div id="kt_app_sidebar_menu_scroll" class="scroll-y my-5 mx-3" data-kt-scroll="true" data-kt-scroll-activate="true"
      data-kt-scroll-height="auto" data-kt-scroll-dependencies="#kt_app_sidebar_logo, #kt_app_sidebar_footer"
      data-kt-scroll-wrappers="#kt_app_sidebar_menu" data-kt-scroll-offset="5px" data-kt-scroll-save-state="true">
      <div class="menu menu-column menu-rounded menu-sub-indention fw-semibold fs-6" id="#kt_app_sidebar_menu"
        data-kt-menu="true" data-kt-menu-expand="false">
        <div class="menu-item">
          <a class="menu-link" href="{{ route('home') }}">
            <span class="menu-icon">
              <i class="ki-outline ki-element-11 fs-2"></i>
            </span>
            <span class="menu-title">Dashboard</span>
          </a>
        </div>

        @can('hrd.menu')
          <div class="menu-item pt-5">
            <div class="menu-content">
              <span class="menu-heading fw-bold text-uppercase fs-7">HRD</span>
            </div>
          </div>

          @can('hrd.employee.read')
            <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
              <span class="menu-link">
                <span class="menu-icon">
                  <i class="ki-outline ki-people text-success fs-2"></i>
                </span>
                <span class="menu-title">Employees</span>
                <span class="menu-arrow"></span>
              </span>
              <div class="menu-sub menu-sub-accordion">
                <div class="menu-item menu-accordion mb-1">
                  <a class="menu-link" href="{{ route('employee.index') }}">
                    <span class="menu-bullet">
                      <span class="bullet bullet-dot"></span>
                    </span>
                    <span class="menu-title">Data</span>
                  </a>
                </div>
                <div class="menu-item menu-accordion">
                  <a class="menu-link" href="{{ route('employee.report') }}">
                    <span class="menu-bullet">
                      <span class="bullet bullet-dot"></span>
                    </span>
                    <span class="menu-title">Report</span>
                  </a>
                </div>
              </div>
            </div>
          @endcan

          @can('hrd.area.read', 'hrd.department.read')
            <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
              <span class="menu-link">
                <span class="menu-icon">
                  <i class="ki-outline ki-setting-3 text-dark fs-2"></i>
                </span>
                <span class="menu-title">Master Data</span>
                <span class="menu-arrow"></span>
              </span>
              <div class="menu-sub menu-sub-accordion">

                @can('hrd.area.read')
                  <div class="menu-item">
                    <a class="menu-link" href="{{ route('area.index') }}">
                      <span class="menu-bullet">
                        <span class="bullet bullet-dot"></span>
                      </span>
                      <span class="menu-title">Area</span>
                    </a>
                  </div>
                @endcan

                @can('hrd.department.read')
                  <div class="menu-item">
                    <a class="menu-link" href="{{ route('department.index') }}">
                      <span class="menu-bullet">
                        <span class="bullet bullet-dot"></span>
                      </span>
                      <span class="menu-title">Department</span>
                    </a>
                  </div>
                @endcan
              </div>
            </div>
          @endcan
        @endcan

        @can('ga.menu')
          <div class="menu-item pt-5">
            <div class="menu-content">
              <span class="menu-heading fw-bold text-uppercase fs-7">GA</span>
            </div>
          </div>

          <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
            <span class="menu-link">
              <span class="menu-icon">
                <i class="ki-outline ki-pulse text-primary fs-2"></i>
              </span>
              <span class="menu-title">Medical Record</span>
              <span class="menu-arrow"></span>
            </span>
            <div class="menu-sub menu-sub-accordion">
              <div class="menu-item menu-accordion mb-1">
                <a class="menu-link" href="{{ route('medical.index') }}">
                  <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                  </span>
                  <span class="menu-title">Data</span>
                </a>
              </div>
            </div>
          </div>
        @endcan

        @can('administrator.menu')
          <div class="menu-item pt-5">
            <div class="menu-content">
              <span class="menu-heading fw-bold text-uppercase fs-7">Administrator</span>
            </div>
          </div>
          <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
            <span class="menu-link">
              <span class="menu-icon">
                <i class="ki-outline ki-security-user text-primary fs-2"></i>
              </span>
              <span class="menu-title">User Management</span>
              <span class="menu-arrow"></span>
            </span>
            <div class="menu-sub menu-sub-accordion">
              <div class="menu-item">
                <a class="menu-link" href="{{ route('user.index') }}">
                  <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                  </span>
                  <span class="menu-title">Users</span>
                </a>
              </div>
              <div class="menu-item menu-accordion">
                <a class="menu-link" href="{{ route('role.index') }}">
                  <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                  </span>
                  <span class="menu-title">Roles</span>
                </a>
              </div>
              <div class="menu-item">
                <a class="menu-link" href="{{ route('permission.index') }}">
                  <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                  </span>
                  <span class="menu-title">Permissions</span>
                </a>
              </div>
            </div>
          </div>
        @endcan
      </div>
    </div>
  </div>
</div>
