<header class="topbar px-3 px-lg-4 py-3 d-flex align-items-center justify-content-between">
    <div>
        <h1 class="h5 mb-0">@yield('page-title', 'Admin Dashboard')</h1>
    </div>

    <div class="d-flex align-items-center gap-3">
        <div class="text-end d-none d-sm-block">
            <div class="fw-semibold">{{ auth()->user()->name }}</div>
            <div class="small text-muted text-capitalize">{{ auth()->user()->role }}</div>
        </div>

        <form action="{{ route('logout') }}" method="POST" class="m-0">
            @csrf
            <button type="submit" class="btn btn-outline-danger btn-sm">
                <i class="bi bi-box-arrow-right me-1"></i> Logout
            </button>
        </form>
    </div>
</header>
