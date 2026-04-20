<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Document Tracker</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link rel="stylesheet" href="{{ asset('css/toast.css') }}">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h1>Document Tracker Login</h1>

            {{-- Session error (invalid credentials) --}}
            @if(session('error'))
                <div class="error-message">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Validation errors --}}
            @if ($errors->any())
                <div class="error-message">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group">
                    <label for="username">Username</label>
                    <div class="input-wrapper">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="gray" class="bi bi-person-fill" viewBox="0 0 16 16">
                        <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/>
                        </svg>
                    <input
                        type="text"
                        name="username"
                        id="username"
                        value="{{ old('username') }}"
                        placeholder="Enter your username"
                        autocomplete="username"
                        required
                    >
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrapper">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="gray" class="bi bi-lock-fill" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M8 0a4 4 0 0 1 4 4v2.05a2.5 2.5 0 0 1 2 2.45v5a2.5 2.5 0 0 1-2.5 2.5h-7A2.5 2.5 0 0 1 2 13.5v-5a2.5 2.5 0 0 1 2-2.45V4a4 4 0 0 1 4-4m0 1a3 3 0 0 0-3 3v2h6V4a3 3 0 0 0-3-3"/>
                        </svg>
                    <input
                        type="password"
                        name="password"
                        id="password"
                        placeholder="Enter your password"
                        autocomplete="current-password"
                        required
                    >
                    </div>
                </div>

                <button type="submit">Login</button>
            </form>

            <div class="forgot-password">
                <a href="#">Forgot password?</a>
            </div>
        </div>
    </div>

    <!-- Toast -->
<div id="toast-container"></div>

<script>
    window.toastSuccess = null;
    window.toastError   = null;
    window.toastErrors  = [];
</script>

<script src="{{ asset('js/toast.js') }}"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {

    if (sessionStorage.getItem("logout_reason") === "inactivity") {

        if (window.showToast) {
            showToast("Logged out due to inactivity for 20 minutes", "error");
        }

        sessionStorage.removeItem("logout_reason");
    }

});
</script>
</body>

</html>
