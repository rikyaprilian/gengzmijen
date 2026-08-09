<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- PWA & Mobile Optimization -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#2563eb">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Portal BGN">
    <link rel="apple-touch-icon" href="{{ asset('images/logo-bgn.png') }}">

    <title>@yield('title', $settings->portal_name ?? 'Portal Link BGN')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-bgn.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome 6 & Remix Icon CDNs -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css">

    <script>
        window.PORTAL_EDIT_MODE = @json(session('portal_edit_mode', false));
    </script>

    @vite(['resources/css/app.css','resources/js/app.js'])
</head>

<body>

    <nav class="portal-navbar shadow-sm">

        <div class="container portal-container">

            <div class="d-flex justify-content-between align-items-center">

                <div class="d-flex align-items-center">

                    <div class="logo-box">

                        <img
                            src="{{ asset('images/logo-bgn.png') }}"
                            alt="Logo BGN"
                            class="navbar-logo">

                    </div>

                    <div class="ms-3">

                        <h5 class="fw-bold mb-0 text-dark">
                            {{ $settings->portal_name ?? 'Portal Link BGN' }}
                        </h5>

                    </div>

                </div>

                <div class="d-flex align-items-center gap-2">
                    <a
                        href="#"
                        id="portalManageButton"
                        class="manage-button"
                        data-bs-toggle="modal"
                        data-bs-target="#manageModal">

                        <i class="bi bi-gear-fill me-1" id="manageBtnIcon"></i>
                        <span id="manageBtnText">Kelola Portal</span>

                    </a>
                </div>

            </div>

        </div>

    </nav>

    @yield('content')

    @include('components.manage-modal')
    @include('components.modals.card-modal')
    @include('components.modals.link-modal')
    @include('components.modals.category-modal')
    @include('components.modals.settings-modal')

</body>

</html>