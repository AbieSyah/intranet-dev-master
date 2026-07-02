<div id="kt_app_sidebar" class="app-sidebar flex-column " data-kt-drawer="true" data-kt-drawer-name="app-sidebar"
    data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="225px"
    data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_app_sidebar_mobile_toggle">
    <div class="app-sidebar-logo px-6" id="kt_app_sidebar_logo">
        <a href="?page=index">
            <img alt="Logo" src="/assets/media/logos/logosalonpas.jpg"
                class="h-50px mx-auto app-sidebar-logo-default theme-light-show" style="padding-left: 40px;" />
        </a>
        <div id="kt_app_sidebar_toggle"
            class="app-sidebar-toggle btn btn-icon btn-shadow btn-sm btn-color-muted btn-active-color-primary h-30px w-30px position-absolute top-50 start-100 translate-middle rotate "
            data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body"
            data-kt-toggle-name="app-sidebar-minimize">
            <i class="ki-outline ki-black-left-line fs-3 rotate-180"></i>
        </div>
    </div>
    @include('partials/menu')
    <div class="app-sidebar-footer flex-column-auto pt-2 pb-6 px-6" id="kt_app_sidebar_footer">
        <span class="text-muted fw-semibold me-1" style="margin-left: 50px;"">2023&copy; Intranet HPI</span>
    </div>
</div>
