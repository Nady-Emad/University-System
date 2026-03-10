<nav class="navbar navbar-expand-lg bg-white border-bottom">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('student.dashboard') }}">University System</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#studentNav" aria-controls="studentNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="studentNav">
            <ul class="navbar-nav ms-auto me-3 mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}" href="{{ route('student.dashboard') }}">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('student.profile') ? 'active' : '' }}" href="{{ route('student.profile') }}">Profile</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('student.exams') ? 'active' : '' }}" href="{{ route('student.exams') }}">Exams</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('student.online-exams.*') ? 'active' : '' }}" href="{{ route('student.online-exams.index') }}">Take Exam</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('student.results') ? 'active' : '' }}" href="{{ route('student.results') }}">Results</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('student.gpa-simulator') ? 'active' : '' }}" href="{{ route('student.gpa-simulator') }}">GPA Simulator</a>
                </li>
            </ul>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-danger btn-sm">Logout</button>
            </form>
        </div>
    </div>
</nav>
