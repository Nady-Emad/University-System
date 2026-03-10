<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - University System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/university-system.css') }}" rel="stylesheet">
</head>
<body>
<div class="login-page">
    <div class="card login-card">
        <div class="card-body p-4 p-md-5">
            <div class="text-center mb-4">
                <h1 class="login-title h4 mb-1">University System</h1>
                <p class="text-muted mb-0">Login with your role account</p>
            </div>

            @include('partials.alerts')

            <form method="POST" action="{{ route('login.submit') }}" novalidate>
                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" placeholder="name@university.com" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Enter your password" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2">Login</button>
            </form>

            <div class="small text-muted mt-3 text-center">
                Admin: <strong>admin@university.com / password</strong><br>
                Doctor: <strong>m.ali@university.com / password</strong><br>
                Student: <strong>ahmed.hassan@student.com / password</strong>
            </div>
        </div>
    </div>
</div>
</body>
</html>
