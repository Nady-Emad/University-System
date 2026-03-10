<aside class="sidebar sidebar-doctor">
    <div class="brand d-flex align-items-center gap-2">
        <i class="bi bi-clipboard2-pulse-fill"></i>
        <span>Doctor Portal</span>
    </div>

    <div class="small text-white-50 mb-3">Academic Workspace</div>

    <nav class="nav flex-column">
        <a class="nav-link {{ request()->routeIs('doctor.dashboard') ? 'active' : '' }}" href="{{ route('doctor.dashboard') }}">
            <i class="bi bi-speedometer2 me-2"></i> Dashboard
        </a>
        <a class="nav-link {{ request()->routeIs('doctor.exams*') ? 'active' : '' }}" href="{{ route('doctor.exams') }}">
            <i class="bi bi-journal-text me-2"></i> My Exams
        </a>
        <a class="nav-link {{ request()->routeIs('doctor.results*') ? 'active' : '' }}" href="{{ route('doctor.results') }}">
            <i class="bi bi-journal-check me-2"></i> My Results
        </a>
        <a class="nav-link {{ request()->routeIs('doctor.online-exams*') || request()->routeIs('doctor.questions*') ? 'active' : '' }}" href="{{ route('doctor.online-exams.index') }}">
            <i class="bi bi-pc-display-horizontal me-2"></i> Online Exams
        </a>
    </nav>
</aside>
