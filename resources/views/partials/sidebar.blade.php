<aside class="sidebar">
    <div class="brand d-flex align-items-center gap-2">
        <i class="bi bi-mortarboard-fill"></i>
        <span>University System</span>
    </div>

    <div class="small text-white-50 mb-3">Admin Panel</div>

    <nav class="nav flex-column">
        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
            <i class="bi bi-speedometer2 me-2"></i> Dashboard
        </a>
        <a class="nav-link {{ request()->routeIs('students.*') ? 'active' : '' }}" href="{{ route('students.index') }}">
            <i class="bi bi-people-fill me-2"></i> Students
        </a>
        <a class="nav-link {{ request()->routeIs('doctors.*') ? 'active' : '' }}" href="{{ route('doctors.index') }}">
            <i class="bi bi-person-badge-fill me-2"></i> Doctors
        </a>
        <a class="nav-link {{ request()->routeIs('subjects.*') ? 'active' : '' }}" href="{{ route('subjects.index') }}">
            <i class="bi bi-book-fill me-2"></i> Subjects
        </a>
        <a class="nav-link {{ request()->routeIs('exams.*') ? 'active' : '' }}" href="{{ route('exams.index') }}">
            <i class="bi bi-journal-check me-2"></i> Exams
        </a>
        <a class="nav-link {{ request()->routeIs('results.*') ? 'active' : '' }}" href="{{ route('results.index') }}">
            <i class="bi bi-graph-up-arrow me-2"></i> Results & GPA
        </a>
    </nav>
</aside>
