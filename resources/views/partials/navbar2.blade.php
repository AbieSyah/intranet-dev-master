    <!-- Nav tabs -->
    <ul class="nav nav-pills animation-nav profile-nav gap-2 gap-lg-3 gap-md-3 flex-grow-1" role="tablist">
        <li class="nav-item">
            <a class="nav-link fs-14 {{ request()->is('myhome*') ? 'active' : '' }}" href="{{ route('profile.home') }}">
                Home
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link fs-14 {{ request()->is('myrule*') ? 'active' : '' }}"
                href="{{ route('profile.internal.rule') }}">
                Internal Rule
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link fs-14 {{ request()->is('mybenefit*') ? 'active' : '' }}"
                href="{{ route('profile.benefit') }}">
                My Benefit
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link fs-14 {{ request()->is('mycalendar*') ? 'active' : '' }}"
                href="{{ route('profile.calendar') }}">
                Calendar
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link fs-14 {{ request()->is('mymedical*') ? 'active' : '' }}"
                href="{{ route('profile.medical') }}">
                Medical Checkup
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link fs-14 {{ request()->is('mypkb*') ? 'active' : '' }}" href="{{ route('profile.pkb') }}">
                PKB
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link fs-14 {{ request()->is('booking-room*') ? 'active' : '' }}"
                href="{{ route('profile.booking') }}">
                Booking Room
            </a>
        </li>
        @can('hrd.menu.profile')
            <li class="nav-item">
                <a class="nav-link fs-14 {{ request()->is('mytraining*') ? 'active' : '' }}"
                    href="{{ route('profile.training') }}">
                    Training
                </a>
            </li>
        @endcan
        <li class="nav-item">
            <a class="nav-link fs-14 {{ request()->is('myevaluation*') ? 'active' : '' }}"
                href="{{ route('profile.evaluation') }}">
                Evaluation
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link fs-14 {{ request()->is('myrecruitment*') ? 'active' : '' }}"
                href="{{ route('recruitment.profile.index') }}">
                Recruitment
            </a>
        </li>
        @canany(['emp.service-desk.update', 'emp.service-desk.view', 'emp.service-desk.create'])                
            <li class="nav-item">
                <a class="nav-link fs-14 {{ request()->is('service-desk*') || request()->is('myservice-desk*') || request()->is('myknowledge-base*') ? 'active' : '' }}"
                    href="{{ route('myservice-desk.index') }}">
                    IT Service Desk
                </a>
            </li>
        @endcanany
        <li class="nav-item">
            <a class="nav-link fs-14 {{ request()->is('employee-leave*') || request()->is('myemployee-leave*') ? 'active' : '' }}"
                href="{{ route('leave-request.profile-index') }}">
                Cuti
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link fs-14 {{ request()->is('attendance-permit*') || request()->is('myattendance-permit*') ? 'active' : '' }}"
                href="{{ route('attendance-permit.profile-index') }}">
                Izin
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link fs-14 {{ request()->is('attendance-late*') ||request()->is('myattendance-late*') ? 'active' : '' }}"
                href="{{ route('attendance-late.profile-index') }}">
                Keterlambatan
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link fs-14 {{ request()->is('claim-overtime*') || request()->is('myclaim-overtime*') ? 'active' : '' }}"
                href="{{ route('claim-overtime.index') }}">
                Claim Lembur
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link fs-14 {{ request()->is('business-trip*') || request()->is('mybusiness-trip*') ? 'active' : '' }}"
                href="{{ route('business-trip.profile-index') }}">
                Business Trip
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link fs-14 {{ request()->is('mye-sign*') ? 'active' : '' }}"
                href="{{ route('e-sign.profile-index') }}">
                E-Sign
            </a>
        </li>
    </ul>
