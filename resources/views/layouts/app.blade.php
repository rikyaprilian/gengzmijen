<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Portal Pelaporan BGN')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-bgn.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <link
        rel="icon"
        type="image/png"
        href="{{ asset('images/logo-bgn.png') }}">

    @vite(['resources/css/app.css','resources/js/app.js'])
</head>

@include('components.manage-modal')

<body>

    <nav class="portal-navbar">

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

                        <h5 class="fw-bold mb-0">
                            Portal Pelaporan BGN
                        </h5>
<!-- 
                        <small class="text-secondary">
                            Tautan Harian Operasional
                        </small> -->

                    </div>

                </div>

                <a
                    href="#"
                    id="portalManageButton"
                    class="manage-button"
                    data-bs-toggle="modal"
                    data-bs-target="#manageModal">

                    <i class="bi bi-gear-fill"></i>

                    <span>

                        Kelola Portal

                    </span>

                </a>

            </div>

        </div>

    </nav>

    @yield('content')

</body>

</html>