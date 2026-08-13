<div class="d-flex">
    <ul class="nav nav-pills animation-nav profile-nav gap-2 gap-lg-3 gap-md-3 flex-grow-1">
        @can('emp.menu')
            <!-- @can('emp.employee.read')
            <li class="nav-item">
                <a class="nav-link fs-14 {{ request()->is('home*') ? 'active' : '' }}" href="{{route('home')}}">
                     My Profile
                </a>
            </li>
            @endcan -->
            <li class="nav-item">
                <a class="nav-link fs-14 {{ request()->is('home*') ? 'active' : '' }}" href="{{route('home')}}">
                     Home
                </a>
            </li>
            @can('emp.benefit.read')
            <li class="nav-item">
                <a class="nav-link fs-14 {{ request()->is('employee/benefit*') ? 'active' : '' }}" href="{{route('benefit.emp.index')}}">
                    My Benefit
                </a>
            </li> 
            @endcan        
            @can('emp.internal-rule.read')
            <li class="nav-item">
                <a class="nav-link fs-14 {{ request()->is('employee/internal-rule*') ? 'active' : '' }}" href="{{route('internal-rule.emp.index')}}">
                    Internal Rule
                </a>
            </li> 
            @endcan      
            {{--<li class="nav-item">
                <a class="nav-link fs-14 {{ request()->is('employee/job-description*') ? 'active' : '' }}" href="{{route('comingsoon')}}">
                    Job Description
                </a>
            </li>--}}           
            @can('emp.calendar.read')
            <li class="nav-item">
                <a class="nav-link fs-14 {{ request()->is('employee/calendar*') ? 'active' : '' }}" href="{{route('calendar.emp.index')}}">
                    Calendar
                </a>
            </li>
            @endcan
            {{--<li class="nav-item">
                <a class="nav-link fs-14 {{ request()->is('employee/event*') ? 'active' : '' }}" href="{{route('comingsoon')}}">
                    Event
                </a>
            </li>--}}
            @can('emp.medical.read')
            <li class="nav-item">
                <a class="nav-link fs-14 {{ request()->is('employee/medical*') ? 'active' : '' }}" href="{{route('medical.emp.index')}}">
                    Medical Checkup
                </a>
            </li>
            @endcan
            @can('emp.pkb.read')
            <li class="nav-item">
                <a class="nav-link fs-14 {{ request()->is('employee/pkb*') ? 'active' : '' }}" href="{{route('pkb.emp.index')}}">
                    PKB
                </a>
            </li>
            @endcan
            @can('emp.booking-room.read')
            <li class="nav-item">
                <a class="nav-link fs-14 {{ request()->is('employee/booking-room*') ? 'active' : '' }}" href="{{route('booking-room.emp.index')}}">
                    Booking Room
                </a>
            </li>
            @endcan
            @if(auth()->user()->employee_id == '529' || auth()->user()->employee_id == '624' || auth()->user()->employee_id == '911'|| auth()->user()->employee_id == '1054' || auth()->user()->employee_id == '363')
            <li class="nav-item">
                <a class="nav-link fs-14 {{ request()->is('employee/training*') ? 'active' : '' }}" href="{{route('training.emp.index')}}">
                    Training
                </a>
            </li>
            @endif
            @can('emp.evaluation.read')
            <li class="nav-item">
                <a class="nav-link fs-14 {{ request()->is('employee/evaluation*') ? 'active' : '' }}" href="{{route('evaluation.emp.index')}}">
                    Evaluation
                </a>
            </li>
            @endcan
            @can('emp.recruitment.read')
            <li class="nav-item">
                <a class="nav-link fs-14 {{ request()->is('employee/recruitment*') ? 'active' : '' }}" href="{{route('recruitment.emp.index')}}">
                    Recruitment
                </a>
            </li>
            @endcan
            @canany(['emp.service-desk.read', 'emp.service-desk.view', 'emp.service-desk.create'])                
                <li class="nav-item">
                    <a class="nav-link fs-14 {{ request()->is('service-desk*') || request()->is('knowledge-base*') ? 'active' : '' }}"
                        href="{{ route('service-desk.index') }}">
                        IT Service Desk
                    </a>
                </li>
            @endcanany
            {{-- @can('emp.training.read')
            @endcan --}}
            @can('emp.employee-leave.read')
            <li class="nav-item">
                <a class="nav-link fs-14 {{ request()->is('employee-leave*') || request()->is('myemployee-leave*') ? 'active' : '' }}"
                    href="{{route('leave-request.profile-index')}}">
                    Cuti
                </a>
            </li>
            @endcan
            @can('emp.attendance-permit.read')
            <li class="nav-item">
                <a class="nav-link fs-14 {{ request()->is('attendance-permit*') || request()->is('myattendance-permit*') ? 'active' : '' }}"
                    href="{{route('attendance-permit.profile-index')}}">
                    Izin
                </a>
            </li>
            @endcan
            @can('emp.late.read')
            <li class="nav-item">
                <a class="nav-link fs-14 {{ request()->is('attendance-late*') || request()->is('myattendance-late*') ? 'active' : '' }}"
                    href="{{route('attendance-late.profile-index')}}">
                    Keterlambatan
                </a>
            </li>
            @endcan
            @can('emp.overtime.read')
            <li class="nav-item">
                <a class="nav-link fs-14 {{ request()->is('claim-overtime*') || request()->is('myclaim-overtime*') ? 'active' : '' }}"
                    href="{{route('claim-overtime.index')}}">
                    Claim Lembur
                </a>
            </li>
            @endcan
            @can('emp.business-trip.read')
            <li class="nav-item">
                <a class="nav-link fs-14 {{ request()->is('business-trip*') || request()->is('mybusiness-trip*') ? 'active' : '' }}"
                    href="{{route('business-trip.profile-index')}}">
                    Business Trip
                </a>
            </li>
            @endcan
            @can('e-sign.profile')
            <li class="nav-item">
                <a class="nav-link fs-14 {{ request()->is('mye-sign*') ? 'active' : '' }}"
                    href="{{ route('e-sign.profile-index') }}">
                    E-Sign
                </a>
            </li>
            @endcan
        @endcan
    </ul>
</div>
<br>