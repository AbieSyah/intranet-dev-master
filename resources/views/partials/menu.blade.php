<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <!-- Dark Logo-->
        <a href="index.html" class="logo logo-dark">
            <span class="logo-sm">
                <img src="{{  url('') }}/assets/images/logo-sm.png" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="{{  url('') }}/assets/images/hisamitsu.png" alt="" height="17">
            </span>
        </a>
        <!-- Light Logo-->
        <a href="{{route('home')}}" class="logo logo-light">
            <span class="logo-sm">
                <img src="{{  url('') }}/assets/images/logo-sm.png" alt="" height="27">
            </span>
            <span class="logo-lg">
                <img src="{{  url('') }}/assets/images/hisamitsu.png" alt="" height="25">
            </span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover" id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    <div id="scrollbar">
        <div class="container-fluid">

            <div id="two-column-menu">
            </div>
            <ul class="navbar-nav" id="navbar-nav">
                @can('hrd.menu')
                <li class="menu-title"><span data-key="t-menu">Menu</span></li>
                    @can('hrd.home.read')
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ request()->is('home*') ? 'active' : '' }}" href="{{ route('home') }}">
                            <i class="ri-home-4-line"></i> <span data-key="t-home">Home</span>
                        </a>
                    </li>
                    @endcan          
                    @can('hrd.employee.read')
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ request()->is('hrd/employee*') ? 'active' : '' }}" href="{{ route('employee.index') }}">
                            <i class="ri-team-line"></i> <span data-key="t-employees">Employees</span>
                        </a>
                    </li>
                    @endcan
                    @can('hrd.evaluation.read')
                    <li class="nav-item {{ request()->is('hrd/evaluation*') ? 'active menu-open' : '' }}">
                        <a class="nav-link menu-link {{ request()->is('hrd/evaluation*') ? 'active' : '' }}" href="#sidebarEvaluation" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarEvaluation">
                            <i class="ri-survey-line"></i> <span data-key="t-evaluations">Evaluations</span>
                        </a>
                        <div class="collapse menu-dropdown {{ request()->is('hrd/evaluation*') ? 'in show' : '' }}" id="sidebarEvaluation">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="{{ route('evaluation.schedule.index') }}" class="nav-link {{ request()->is('hrd/evaluation/schedule*') ? 'active' : '' }}" data-key="t-evaluations-schedule"> Schedule </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('evaluation.index') }}" class="nav-link {{ request()->is('hrd/evaluation/process*') ? 'active' : '' }}" data-key="t-evaluations-process"> On Process </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('evaluation.done.index') }}" class="nav-link {{ request()->is('hrd/evaluation/done*') ? 'active' : '' }}" data-key="t-evaluations-done"> Done </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    @endcan
                    @can('hrd.recruitment.read')
                    <li class="nav-item {{ request()->is('hrd/recruitment*') ? 'active menu-open' : '' }}">
                        <a class="nav-link menu-link {{ request()->is('hrd/recruitment*') ? 'active' : '' }}" href="#sidebarRecruitment" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarRecruitment">
                            <i class="ri-user-search-line"></i> <span data-key="t-recruitments">Recruitment</span>
                        </a>
                        <div class="collapse menu-dropdown {{ request()->is('hrd/recruitment*') ? 'in show' : '' }}" id="sidebarRecruitment">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="{{ route('employee-requisition.index') }}" class="nav-link {{ request()->is('hrd/recruitment/er*') ? 'active' : '' }}" data-key="t-recruitments-employee-requisition"> Employee Requisition </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('job-posting.index') }}" class="nav-link {{ request()->is('hrd/recruitment/jp*') ? 'active' : '' }}" data-key="t-recruitments-job-posting"> Job Posting </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('candidate.index') }}" class="nav-link {{ request()->is('hrd/recruitment/candidate*') ? 'active' : '' }}" data-key="t-recruitments-candidate"> Candidate </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('selection.index') }}" class="nav-link {{ request()->is('hrd/recruitment/selection*') ? 'active' : '' }}" data-key="t-recruitments-selection"> Selection </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    @endcan
                    @can('hrd.internal-rules.read')
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ request()->is('hrd/internal-rules*') ? 'active' : '' }}" href="{{route('internal-rule.index')}}">
                            <i class="ri-user-follow-line"></i> <span data-key="t-internal-rules">Internal Rules</span>
                        </a>
                    </li>                    
                    @endcan
                    @can('hrd.menu.training')
                    <li class="nav-item {{ request()->is('hrd/training*') ? 'active menu-open' : '' }}">
                        <a class="nav-link menu-link {{ request()->is('hrd/training*') ? 'active' : '' }}" href="#sidebarTraining" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarTraining">
                            @if (auth()->user()->hasRole('President Director'))
                                <i class="ri-account-box-line"></i> <span data-key="t-training">
                                    Training @if($notif_pti > 0) <span class="badge bg-danger">!</span>@endif
                                </span>
                            @else
                                <i class="ri-account-box-line"></i> <span data-key="t-training">
                                    Training @if($notif_pti > 0 || $notif_ptt > 0) <span class="badge bg-danger">!</span>@endif
                                </span>
                            @endif                            
                        </a>
                        <div class="collapse menu-dropdown {{ request()->is('hrd/training*') ? 'in show' : '' }}" id="sidebarTraining">
                            <ul class="nav nav-sm flex-column">
                                @can('hrd.training.proggress')
                                {{--<li class="nav-item">
                                    <a href="{{route('training.data.proggress')}}" class="nav-link {{ request()->is('hrd/training/data/proggress') ? 'active' : '' }}" data-key="t-hrd-training-proggress"> Progress Training @if($notif_verified_pti > 0 || $notif_verified_ptt > 0) <span class="badge bg-danger">!</span>@endif</a>
                                </li>--}}
                                @endcan
                                @can('hrd.training.record')
                                <li class="nav-item">
                                    <a href="{{route('training.data.index')}}" class="nav-link {{ request()->is('hrd/training/data/index') ? 'active' : '' }}" data-key="t-hrd-training-data"> Record Training </a>
                                </li>
                                @endcan
                                @can('hrd.training.calendar')
                                <li class="nav-item">
                                    <a href="{{route('training.scheduled.index')}}" class="nav-link {{ request()->is('hrd/training/scheduled*') ? 'active' : '' }}" data-key="t-hrd-training-scheduled"> Scheduled </a>
                                </li>
                                @endcan
                                @can('hrd.training.ptt')
                                <li class="nav-item">
                                    <a href="{{route('training.ptt.index')}}" class="nav-link {{ request()->is('hrd/training/ptt*') ? 'active' : '' }}" data-key="t-hrd-training-ptt">
                                        Rencana Pelatihan @if($notif_ptt > 0) <span class="badge bg-danger">!</span>@endif
                                    </a>
                                </li>
                                @endcan
                                @can('hrd.training.pti')
                                <li class="nav-item">
                                    <a href="{{route('training.pti.index')}}" class="nav-link {{ request()->is('hrd/training/pti*') ? 'active' : '' }}" data-key="t-hrd-training-pti">
                                        Pelaksanaan Pelatihan @if($notif_pti > 0) <span class="badge bg-danger">!</span>@endif
                                    </a>
                                </li>
                                @endcan
                                @can('hrd.training.periode')
                                <li class="nav-item">
                                    <a href="{{route('training.periode')}}" class="nav-link {{ request()->is('hrd/training/periode*') ? 'active' : '' }}" data-key="t-hrd-training-periode"> Periode Training </a>
                                </li>
                                @endcan
                                @can('hrd.training.laporan')
                                <li class="nav-item">
                                    <a href="{{route('training.laporan.index')}}" class="nav-link {{ request()->is('hrd/training/laporan*') ? 'active' : '' }}" data-key="t-hrd-training-laporan"> Laporan Training </a>
                                </li>
                                @endcan
                            </ul>
                        </div>
                    </li>                   
                    @endcan
                    @if (auth()->user()->hasRole('Doctor'))
                    @else
                    @can('hrd.jobdesk.read')
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="{{route('comingsoon')}}">
                            <i class="ri-file-user-line"></i> <span data-key="t-jobdesk">Job Description</span>
                        </a>
                    </li>                    
                    @endcan
                    @can('hrd.news-and-event.read')                    
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ request()->is('hrd/news-and-event*') ? 'active' : '' }}" href="{{route('news-and-event.index')}}">
                            <i class="ri-open-arm-line"></i> <span data-key="t-news-event">News and Event</span>
                        </a>
                    </li>
                    @endcan
                    @can('hrd.calendar.read')                    
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ request()->is('hrd/calendar*') ? 'active' : '' }}" href="{{route('calendar.index')}}">
                            <i class="ri-calendar-line"></i> <span data-key="t-calendar">Calendar</span>
                        </a>
                    </li> 
                    @endcan
                    @can('hrd.booking-room.read')                    
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ request()->is('hrd/booking-room*') ? 'active' : '' }}" href="{{route('booking-room.index')}}">
                            <i class="ri-building-line"></i> <span data-key="t-booking">Booking Room</span>
                        </a>
                    </li> 
                    @endcan
                    @endif                    
                    @can('hrd.menu.clinic')
                    <li class="nav-item {{ request()->is('hrd/clinic') ? 'active menu-open' : '' }}">
                        <a class="nav-link menu-link {{ request()->is('hrd/clinic*') ? 'active' : '' }}" href="#sidebarMultilevel" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarMultilevel">
                            <i class="bx bx-clinic"></i> <span data-key="t-multi-level">Clinic</span>
                        </a>
                        <div class="collapse menu-dropdown  {{ request()->is('hrd/clinic/patient*') ? 'in show' : '' }} {{ request()->is('hrd/clinic/in*') ? 'in show' : '' }} {{ request()->is('hrd/clinic/out*') ? 'in show' : '' }} {{ request()->is('hrd/clinic/opname*') ? 'in show' : '' }} {{ request()->is('hrd/clinic/stock*') ? 'in show' : '' }}" id="sidebarMultilevel">
                            <ul class="nav nav-sm flex-column">
                                @can('hrd.menu.clinic.patient')
                                    @can('hrd.clinic.patient.read')
                                        <li class="nav-item {{ request()->is('hrd/clinic/patient*') ? 'active' : '' }}">
                                            <a href="{{ route('clinic.patient.index') }}" class="nav-link {{ request()->is('hrd/clinic/patient*') ? 'active' : '' }}" data-key="t-patient"> Patient </a>
                                        </li>
                                    @endcan
                                @endcan
                                @can('hrd.menu.clinic.medicine')
                                <li class="nav-item {{ request()->is('hrd/clinic*') ? 'active menu-open' : '' }}">
                                    <a href="#sidebarMedicine" class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarMedicine" data-key="t-level-1.2"> 
                                        Medicine
                                    </a>
                                    <div class="collapse menu-dropdown  {{ request()->is('hrd/clinic/in*') ? 'in show' : '' }} {{ request()->is('hrd/clinic/out*') ? 'in show' : '' }} {{ request()->is('hrd/clinic/opname*') ? 'in show' : '' }} {{ request()->is('hrd/clinic/stock*') ? 'in show' : '' }}" id="sidebarMedicine">
                                        <ul class="nav nav-sm flex-column">
                                            @can('hrd.clinic.stock.read')
                                            <li class="nav-item">
                                                <a href="{{ route('clinic.stock.index') }}" class="nav-link {{ request()->is('hrd/clinic/stock*') ? 'active' : '' }}" data-key="t-medicine-stock"> Data Stock </a>
                                            </li>
                                            @endcan
                                            @can('hrd.clinic.masuk.read')
                                            <li class="nav-item">
                                                <a href="{{ route('clinic.masuk.index') }}" class="nav-link {{ request()->is('hrd/clinic/in*') ? 'active' : '' }}" data-key="t-medicine-in"> Medicine In </a>
                                            </li>
                                            @endcan
                                            @can('hrd.clinic.keluar.read')
                                            <li class="nav-item">
                                                <a href="{{ route('clinic.keluar.index') }}" class="nav-link {{ request()->is('hrd/clinic/out*') ? 'active' : '' }}" data-key="t-medicine-out"> Medicine Out </a>
                                            </li>
                                            @endcan
                                            @can('hrd.clinic.opname.read')
                                                @can('hrd.clinic.opname.create')
                                                <li class="nav-item">
                                                    <a href="{{ route('clinic.opname.create') }}" class="nav-link {{ request()->is('hrd/clinic/opname*') ? 'active' : '' }}" data-key="t-medicine-opname"> Stock Opname </a>
                                                </li>
                                                @endcan
                                            @endcan
                                        </ul>
                                    </div>
                                </li>
                                @endcan
                            </ul>
                        </div>
                    </li>
                    @endcan           
                    @can('hrd.menu.medical-record')
                    <li class="nav-item {{ request()->is('hrd/medical*') ? 'active menu-open' : '' }}">
                        <a class="nav-link menu-link {{ request()->is('hrd/medical*') ? 'active' : '' }}" href="#sidebarMedicalRecord" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarMedicalRecord">
                            <i class="lab las la-notes-medical"></i> <span data-key="t-hrd-mr">Medical Check Up</span>
                        </a>
                        <div class="collapse menu-dropdown {{ request()->is('hrd/medical*') ? 'in show' : '' }}" id="sidebarMedicalRecord">
                            <ul class="nav nav-sm flex-column">
                                @can('hrd.medical-record.reguler.read')
                                <li class="nav-item">
                                    <a href="{{ route('reguler.index') }}" class="nav-link {{ request()->is('hrd/medical/reguler*') ? 'active' : '' }}" data-key="t-hrd-reguler"> Reguler </a>
                                </li>
                                @endcan
                                @can('hrd.medical-record.ireguler.read')
                                <li class="nav-item">
                                    <a href="{{ route('ireguler.index') }}" class="nav-link {{ request()->is('hrd/medical/ireguler*') ? 'active' : '' }}" data-key="t-hrd-ireguler"> Ireguler </a>
                                </li>
                                @endcan
                            </ul>
                        </div>
                    </li>
                    @endcan            
                    @canany(['security.guest.read'])
                        <li class="nav-item {{ request()->is('security/guest*') ? 'active menu-open' : '' }}">
                            <a class="nav-link menu-link {{ request()->is('security/guest*') ? 'active' : '' }}"
                                href="#sidebarSecurty" data-bs-toggle="collapse" role="button" aria-expanded="false"
                                aria-controls="sidebarSecurty">
                                <i class="mdi mdi-security"></i> <span data-key="t-hrd-mr">Security</span>
                            </a>
                            <div class="collapse menu-dropdown
                            {{ request()->is('security/guest*') ? 'in show' : '' }}
                            {{ request()->is('security/employee-attendance-record*') ? 'in show' : '' }}"
                            id="sidebarSecurty">
                                <ul class="nav nav-sm flex-column">
                                    @can('security.guest.read')
                                        <li class="nav-item">
                                            <a href="{{ route('guest.index') }}"
                                                class="nav-link {{ request()->is('security/guest*') ? 'active' : '' }}"
                                                data-key="t-security-guest"> Guest </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('employee-permit.security-index') }}"
                                                class="nav-link {{ request()->is('security/employee-attendance-record*') ? 'active' : '' }}"
                                                data-key="t-security-guest"> Attendance Record </a>
                                        </li>
                                        @endcan
                                </ul>
                            </div>
                        </li>
                    @endcanany
                    @can('hrd.attendance.menu')
                    <li class="nav-item {{ request()->is('hrd/attendance*') ? 'active menu-open' : '' }}">
                        <a class="nav-link menu-link {{ request()->is('hrd/attendance*') ? 'active' : '' }}"
                        {{-- {{ dd(request()->fullUrl()) }} --}}
                        {{-- @dd(request()->path()); --}}
                        href="#sidebarAttendance"
                        data-bs-toggle="collapse"
                        role="button">
                            <i class="bx bx-time"></i>
                            <span>Attendance</span>
                        </a>
                        <div class="collapse menu-dropdown
                            {{ request()->is('hrd/attendance/sub-menu/employee-attendance*') ? 'in show' : '' }}
                            {{ request()->is('hrd/attendance/sub-menu/group-employee-workhour*') ? 'in show' : '' }}
                            {{ request()->is('hrd/attendance/sub-menu/business-trip*') ? 'in show' : '' }}
                            {{ request()->is('hrd/attendance/sub-menu/employee-permission*') ? 'in show' : '' }}
                            {{ request()->is('hrd/attendance/master*') ? 'in show' : '' }}"
                            id="sidebarAttendance">
                            <ul class="nav nav-sm flex-column">
                                @can('hrd.employee-attendance.read')
                                <li class="nav-item">
                                    <a href="{{ route('employee-attendance.index') }}"
                                    class="nav-link {{ request()->is('hrd/attendance/sub-menu/employee-attendance*') ? 'active' : '' }}">
                                        Attendance Record
                                    </a>
                                </li>
                                @endcan
                                @can('hrd.group-employee-workhour.read')
                                <li class="nav-item">
                                    <a href="{{ route('group-employee-workhour.index')}}"
                                    class="nav-link {{ request()->is('hrd/attendance/sub-menu/group-employee-workhour*') ? 'active' : '' }}">
                                        Group Employee
                                    </a>
                                </li>
                                @endcan
                                @can('hrd.attendance-permit.read')
                                <li class="nav-item">
                                    <a href="{{ route('attendance-permit.index')}}"
                                    class="nav-link {{ request()->is('hrd/attendance/sub-menu/employee-permission*') ? 'active' : '' }}">
                                        Employee Permission
                                    </a>
                                </li>
                                @endcan
                                {{-- MASTER --}}
                                @can('hrd.attendance.menu.master')
                                <li class="nav-item {{ request()->is('hrd/attendance/master*') ? 'active menu-open' : '' }}">
                                    <a href="#sidebarAttendanceMaster"
                                    class="nav-link"
                                    data-bs-toggle="collapse"
                                    role="button">
                                        Master
                                    </a>
                                    <div class="collapse menu-dropdown
                                        {{ request()->is('hrd/attendance/master/workhour*') ? 'in show' : '' }}
                                        {{ request()->is('hrd/attendance/master/positioning*') ? 'in show' : '' }}
                                        {{ request()->is('hrd/attendance/master/attendance-calendar*') ? 'in show' : '' }}
                                        {{ request()->is('hrd/attendance/master/leave-setting*') ? 'in show' : '' }}
                                        {{ request()->is('hrd/attendance/master/business-trip-allowance*') ? 'in show' : '' }}"
                                        id="sidebarAttendanceMaster">
                                        <ul class="nav nav-sm flex-column">
                                            {{-- Work Hour --}}
                                            @can('hrd.workhour.read')
                                            <li class="nav-item">
                                                <a href="{{ route('workhour.index') }}"
                                                class="nav-link {{ request()->is('hrd/attendance/master/workhour*') ? 'active' : '' }}">
                                                    Work Hour
                                                </a>
                                            </li>
                                            @endcan
                                            {{-- Positioning --}}
                                            @can('hrd.positioning.read')
                                            <li class="nav-item">
                                                <a href="{{ route('positioning.index') }}"
                                                class="nav-link {{ request()->is('hrd/attendance/master/positioning*') ? 'active' : '' }}">
                                                    Positioning
                                                </a>
                                            </li>
                                            @endcan
                                            {{-- CALENDAR --}}
                                            @can('hrd.calendar.read')
                                            <li class="nav-item">
                                                <a href="{{ route('attendance-calendar.index') }}"
                                                class="nav-link {{ request()->is('hrd/attendance/master/attendance-calendar*') ? 'active' : '' }}">
                                                    Calendar
                                                </a>
                                            </li>
                                            @endcan
                                            @can('hrd.leave-setting.read')
                                            <li class="nav-item">
                                                <a href="{{ route('leave-setting.index') }}"
                                                class="nav-link {{ request()->is('hrd/attendance/master/leave-setting*') ? 'active' : '' }}">
                                                    Leave
                                                </a>
                                            </li>
                                            @endcan
                                            @can('hrd.business-trip-allowance.read')
                                            <li class="nav-item">
                                                <a href="{{ route('business-trip-allowance.index') }}"
                                                class="nav-link {{ request()->is('hrd/attendance/master/business-trip-allowance*') ? 'active' : '' }}">
                                                    Business Trip Allowance
                                                </a>
                                            </li>
                                            @endcan
                                        </ul>
                                    </div>
                                </li>
                                @endcan
                            </ul>
                        </div>
                    </li>
                    @endcan
                    <li class="nav-item {{ request()->is('e-sign*') ? 'active menu-open' : '' }}">
                        <a class="nav-link menu-link {{ request()->is('e-sign*') ? 'active' : '' }}" href="#sidebarESign" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarESign">
                            <i class="ri-file-text-line"></i> <span data-key="t-e-sign">E-Sign Management</span>
                        </a>
                        <div class="collapse menu-dropdown {{ request()->is('e-sign*') ? 'in show' : '' }}" id="sidebarESign">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="{{ route('e-sign.dashboard') }}" class="nav-link {{ request()->is('e-sign/dashboard') ? 'active' : '' }}" data-key="t-e-sign-dashboard">
                                        <i class="ri-dashboard-line me-1"></i> Dashboard
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('e-sign.daftar-surat') }}" class="nav-link {{ request()->is('e-sign/daftar-surat') ? 'active' : '' }}" data-key="t-e-sign-daftar-surat">
                                        <i class="ri-file-list-3-line me-1"></i> Daftar Surat
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('e-sign.jenis-surat') }}" class="nav-link {{ request()->is('e-sign/jenis-surat') ? 'active' : '' }}" data-key="t-e-sign-jenis-surat">
                                        <i class="ri-grid-line me-1"></i> Jenis Surat
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>                    
                    @canany(['itsm.it-asset.read', 'itsm.asset-type.read', 'itsm.asset-disposal.read', 'itsm.service-desk.read', 'itsm.knowledge-base.read', 'itsm.change-management.read'])                        
                        <li class="nav-item {{ 
                            request()->is('administrator/it-asset*') || 
                            request()->is('administrator/asset-disposal*') || 
                            request()->is('administrator/asset-maintenance*') ||
                            request()->is('administrator/service-desk*') || 
                            request()->is('administrator/service-catalog*') || 
                            request()->is('administrator/asset-type*') || 
                            request()->is('administrator/knowledge-base*') || 
                            request()->is('administrator/change-management*')? 'active menu-open' : '' 
                        }}">
                            <a class="nav-link menu-link {{ 
                                request()->is('administrator/it-asset*') || 
                                request()->is('administrator/asset-disposal*') || 
                                request()->is('administrator/asset-maintenance*') ||
                                request()->is('administrator/service-desk*') || 
                                request()->is('administrator/service-catalog*') || 
                                request()->is('administrator/asset-type*') || 
                                request()->is('administrator/knowledge-base*') || 
                                request()->is('administrator/change-management*')? 'active' : '' 
                            }}" href="#sidebarITSM" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarITSM">
                                <i class="ri-computer-line"></i> <span data-key="t-itsm">IT Service Management</span>
                            </a>
                            <div class="collapse menu-dropdown {{ 
                                request()->is('administrator/it-asset*') || 
                                request()->is('administrator/asset-disposal*') || 
                                request()->is('administrator/asset-maintenance*') ||
                                request()->is('administrator/service-desk*') || 
                                request()->is('administrator/service-catalog*') || 
                                request()->is('administrator/asset-type*') || 
                                request()->is('administrator/knowledge-base*') || 
                                request()->is('administrator/change-management*')? 'in show' : '' 
                            }}" id="sidebarITSM">
                                <ul class="nav nav-sm flex-column">
                                    @can('itsm.it-asset.read')
                                    <li class="nav-item">
                                        <a href="{{ route('it_asset.index') }}" class="nav-link {{ request()->is('administrator/it-asset*') || request()->is('administrator/asset-type*') || request()->is('administrator/asset-disposal*') ? 'active' : '' }}" data-key="t-asset"> IT Assets </a>
                                    </li>
                                    @endcan
                                    {{-- @can('administrator.role.read') --}}
                                    <li class="nav-item">
                                        <a href="{{ route('service-management.index') }}" class="nav-link {{ request()->is('administrator/service-desk*') || request()->is('administrator/service-catalog*') ? 'active' : '' }}" data-key="t-sm"> Service Desk </a>
                                    </li>
                                    {{-- @endcan --}}
                                    {{-- @can('itsm.asset-disposal.read') --}}
                                    <li class="nav-item">
                                        <a href="{{ route('service-change.index') }}" class="nav-link {{ request()->is('administrator/change-management*') ? 'active' : '' }}" data-key="t-disposal"> Change Management </a>
                                    </li>
                                    {{-- @endcan --}}
                                    @can('itsm.knowledge-base.read')                                        
                                        <li class="nav-item">
                                            <a href="{{ route('knowledge-base.index') }}" class="nav-link {{ request()->is('administrator/knowledge-base*') ? 'active' : '' }}" data-key="t-disposal"> Knowledge Base </a>
                                        </li>
                                    @endcan
                                </ul>
                            </div>
                        </li> <!-- end Administrator Menu -->
                    @endcanany     
                @endcan
                @can('administrator.menu')
                <li class="menu-title"><span data-key="t-administrator">Administrator</span></li>
                    @can('hrd.menu.master')
                        <li class="nav-item {{ request()->is('hrd/master*') ? 'active menu-open' : '' }}">
                            <a class="nav-link menu-link {{ request()->is('hrd/master*') ? 'active' : '' }}" href="#sidebarHRDMaster" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarHRDMaster">
                            <i class="ri-list-settings-line"></i> <span data-key="t-hrd-master">Masters</span>
                            </a>
                            <div class="collapse menu-dropdown {{ request()->is('hrd/master*') ? 'in show' : '' }}" id="sidebarHRDMaster">
                                <ul class="nav nav-sm flex-column">
                                    @can('hrd.master.vendor.read')
                                    <li class="nav-item">
                                        <a href="{{ route('vendor.index') }}" class="nav-link {{ request()->is('hrd/master/vendor*') ? 'active' : '' }}" data-key="t-vendor"> Vendor </a>
                                    </li>
                                    @endcan
                                    @can('hrd.master.area.read')
                                    <li class="nav-item">
                                        <a href="{{ route('area.index') }}" class="nav-link {{ request()->is('hrd/master/area*') ? 'active' : '' }}" data-key="t-area"> Area </a>
                                    </li>
                                    @endcan
                                    @can('hrd.master.department.read')
                                    <li class="nav-item">
                                        <a href="{{ route('department.index') }}" class="nav-link {{ request()->is('hrd/master/department*') ? 'active' : '' }}" data-key="t-department"> Department </a>
                                    </li>                      
                                    @endcan
                                    @can('hrd.master.section.read')
                                    <li class="nav-item">
                                        <a href="{{ route('section.index') }}" class="nav-link {{ request()->is('hrd/master/section*') ? 'active' : '' }}" data-key="t-section"> Section </a>
                                    </li>
                                    @endcan      
                                    @can('hrd.master.position.read')
                                    <li class="nav-item">
                                        <a href="{{ route('position.index') }}" class="nav-link {{ request()->is('hrd/master/position*') ? 'active' : '' }}" data-key="t-position"> Position </a>
                                    </li>
                                    @endcan      
                                    @can('hrd.master.level.read')
                                    <li class="nav-item">
                                        <a href="{{ route('level.index') }}" class="nav-link {{ request()->is('hrd/master/level*') ? 'active' : '' }}" data-key="t-level"> Level </a>
                                    </li>
                                    @endcan      
                                    @can('hrd.master.leave.read')
                                    <li class="nav-item">
                                        <a href="{{ route('leave.index') }}" class="nav-link {{ request()->is('hrd/master/leave*') ? 'active' : '' }}" data-key="t-leave"> Leave </a>
                                    </li>
                                    @endcan      
                                    @can('hrd.master.room.read')
                                    <li class="nav-item">
                                        <a href="{{ route('room.index') }}" class="nav-link {{ request()->is('hrd/master/room*') ? 'active' : '' }}" data-key="t-room"> Meeting Room</a>
                                    </li>
                                    @endcan      
                                    @can('hrd.master.drug.read')
                                    <li class="nav-item">
                                        <a href="{{ route('drug.index') }}" class="nav-link {{ request()->is('hrd/master/drug*') ? 'active' : '' }}" data-key="t-drug"> Drugs</a>
                                    </li>
                                    @endcan
                                    @can('hrd.master.appraisal.read')
                                    <li class="nav-item">
                                        <a href="{{ route('appraisal.index') }}" class="nav-link {{ request()->is('hrd/master/appraisal*') ? 'active' : '' }}" data-key="t-appraisal"> Appraisal</a>
                                    </li>
                                    @endcan
                                    @can('hrd.master.building.read')
                                    <li class="nav-item">
                                        <a href="{{ route('building.index') }}" class="nav-link {{ request()->is('hrd/master/building*') ? 'active' : '' }}" data-key="t-building"> Organization</a>
                                    </li>
                                    @endcan
                                    @can('hrd.master.line-approval.read')
                                    <li class="nav-item">
                                        <a href="{{ route('line-approval.index') }}" class="nav-link {{ request()->is('hrd/master/line-approval*') ? 'active' : '' }}" data-key="t-line-approval"> Line Approval</a>
                                    </li>
                                    @endcan
                                    @can('hrd.master.hiring.read')
                                    <li class="nav-item">
                                        <a href="{{ route('hiring.index') }}" class="nav-link {{ request()->is('hrd/master/hiring*') ? 'active' : '' }}" data-key="t-hiring"> Hiring</a>
                                    </li>
                                    @endcan
                                    @can('hrd.master.contract.read')
                                    <li class="nav-item">
                                        <a href="{{ route('contract.index') }}" class="nav-link {{ request()->is('hrd/master/contract*') ? 'active' : '' }}" data-key="t-contract"> Contract</a>
                                    </li>
                                    @endcan
                                </ul>
                            </div>
                        </li> <!-- end Hrd Menu -->
                    @endcan
                    @can('administrator.menu.user')
                    <li class="nav-item {{ request()->is('administrator/user*') ? 'active menu-open' : '' }} {{ request()->is('administrator/role*') ? 'active menu-open' : '' }} {{ request()->is('administrator/permission*') ? 'active menu-open' : '' }}">
                        <a class="nav-link menu-link {{ request()->is('administrator/user*') ? 'active' : '' }} {{ request()->is('administrator/role*') ? 'active' : '' }} {{ request()->is('administrator/permission*') ? 'active' : '' }}" href="#sidebarUserManagement" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarUserManagement">
                        <i class="ri-user-settings-line"></i> <span data-key="t-usermanagement">User Management</span>
                        </a>
                        <div class="collapse menu-dropdown {{ request()->is('administrator/user*') ? 'in show' : '' }} {{ request()->is('administrator/role*') ? 'in show' : '' }} {{ request()->is('administrator/permission*') ? 'in show' : '' }}" id="sidebarUserManagement">
                            <ul class="nav nav-sm flex-column">
                                @can('administrator.user.read')
                                <li class="nav-item">
                                    <a href="{{ route('user.index') }}" class="nav-link {{ request()->is('administrator/user*') ? 'active' : '' }}" data-key="t-user"> Users </a>
                                </li>
                                @endcan
                                @can('administrator.role.read')
                                <li class="nav-item">
                                    <a href="{{ route('role.index') }}" class="nav-link {{ request()->is('administrator/role*') ? 'active' : '' }}" data-key="t-role"> Roles </a>
                                </li>
                                @endcan
                                @can('administrator.permission.read')
                                <li class="nav-item">
                                    <a href="{{ route('permission.index') }}" class="nav-link {{ request()->is('administrator/permission*') ? 'active' : '' }}" data-key="t-permission"> Permissions </a>
                                </li>             
                                @endcan              
                            </ul>
                        </div>
                    </li> <!-- end Administrator Menu -->
                    @endcan              
   
                    @can('administrator.log.read')
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->is('administrator/log*') ? 'active' : '' }}" href="{{ route('log.index') }}">
                                <i class="ri-contacts-line"></i> <span data-key="t-log">Log User Activity</span>
                            </a>
                        </li>
                    @endcan

                    @can('about.read')
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->is('about*') ? 'active' : '' }}" href="{{ route('about.index') }}">
                                <i class="ri-information-line"></i> <span data-key="t-about">About</span>
                            </a>
                        </li>
                        
                    @endcan
                @endcan
            </ul>
        </div>
        <!-- Sidebar -->
    </div>

    <div class="sidebar-background"></div>
</div>