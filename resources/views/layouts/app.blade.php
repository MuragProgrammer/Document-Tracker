<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/documents.css') }}">
    <link rel="stylesheet" href="{{ asset('css/documents-view.css') }}">
    <link rel="stylesheet" href="{{ asset('css/reports.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/topbar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/departments.css') }}">
    <link rel="stylesheet" href="{{ asset('css/positions.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sections.css') }}">
    <link rel="stylesheet" href="{{ asset('css/users.css') }}">
    <link rel="stylesheet" href="{{ asset('css/toast.css')}}">
</head>
<body>
<div class="app-container">

    <!-- Mobile Burger Button -->
    <div id="menuToggle" class="menu-container">
        <input type="checkbox" id="menuCheckbox"/>
        <span></span>
        <span></span>
        <span></span>
    </div>

    <!-- Sidebar -->
    <div class="sidebar-container">
        @include('components.sidebar')
    </div>

    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Right Side -->
    <div class="content-wrapper">
        @include('components.topbar')
        <main class="main-content">
            @yield('content')
        </main>
    </div>

</div>

<!-- Toast -->
<div id="toast-container"></div>
<script>
    window.toastSuccess = @json(session('success'));
    window.toastError   = @json(session('error'));
    window.toastErrors  = @json($errors->all());
</script>
<script src="{{ asset('js/toast.js') }}"></script>

<!-- JS -->


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="{{ asset('js/reports.js') }}"></script>
<script src="{{ asset('js/validator.js') }}"></script>
<script src="{{ asset('js/modal/user-modal.js') }}"></script>
<script src="{{ asset('js/modal/section-modal.js') }}"></script>
<script src="{{ asset('js/modal/position-modal.js') }}"></script>
<script src="{{ asset('js/modal/department-modal.js') }}"></script>
<script src="{{ asset('js/modal/dynamic-modal.js') }}"></script>
<script src="{{ asset('js/modal/user-modal.js') }}"></script>
<script src="{{ asset('js/modal/document-modal.js') }}"></script>
<script src="{{ asset('js/users.js') }}"></script>
<script src="{{ asset('js/search.js') }}"></script>
<script src="{{ asset('js/sidebar.js') }}"></script>
<script>
    // Make the logged-in user available in the browser
    window.loggedInUser = @json(auth()->user());

    let inactivityTime = 0;
    const maxInactivity = 300;

    // Reset timer on activity
    function resetInactivityTimer() {
        inactivityTime = 0;
    }

    document.addEventListener("mousemove", resetInactivityTimer);
    document.addEventListener("keydown", resetInactivityTimer);
    document.addEventListener("click", resetInactivityTimer);
    document.addEventListener("scroll", resetInactivityTimer);

    // Check every second
    setInterval(() => {

        inactivityTime++;

        if (inactivityTime >= maxInactivity) {

            // Save reason for logout
            sessionStorage.setItem("logout_reason", "inactivity");

            // Send logout request
            fetch("{{ route('logout') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                    "Content-Type": "application/json"
                }
            }).then(() => {
                window.location.href = "{{ route('login') }}";
            });

        }

    }, 1000);
</script>

@stack('scripts')
</body>
</html>
