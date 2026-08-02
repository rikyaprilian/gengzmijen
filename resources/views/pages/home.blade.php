@extends('layouts.app')

@section('title','Portal Pelaporan BGN')

@section('content')

<div class="portal-wrapper">

    <section class="hero">

        <div class="hero-logo">

            <div class="hero-logo">
                <img
                    src="{{ asset('images/logo-bgn.png') }}"
                    alt="Logo BGN"
                    class="hero-logo-image">
            </div>

        </div>

        <h1>

            {{ $settings['portal_name'] }}

        </h1>

        <p>

            <i>{{ $settings['homepage_message'] }}</i>

        </p>

    </section>

    <section class="search-section">

        <div class="search-box">

            <i class="bi bi-search"></i>

            <input
                type="text"
                placeholder="Cari aplikasi...">

        </div>

    </section>

    <section class="links">

        <a
            href="https://sipgn-siphr.bgn.go.id/"
            target="_blank"
            class="app-card">

            <div class="app-left">

                <div class="app-icon">
                    <i class="bi bi-people"></i>
                </div>

                <div>

                    <div class="app-title">
                        SIPGN SIPHR
                    </div>

                    <div class="app-subtitle">
                        Absensi
                    </div>

                </div>

            </div>

            <button
                class="copy-btn"
                data-url="https://sipgn-siphr.bgn.go.id/">

                <i class="bi bi-copy"></i>

            </button>

        </a>

    </section>

    <section class="links">

        <a
            href="https://portal-sipgn.bgn.go.id/dashboard"
            target="_blank"
            class="app-card">

            <div class="app-left">

                <div class="app-icon">
                    <i class="bi bi-people"></i>
                </div>

                <div>

                    <div class="app-title">
                        Portal Dashboard SIPGN
                    </div>

                    <div class="app-subtitle">
                        Portal
                    </div>

                </div>

            </div>

            <button
                class="copy-btn"
                data-url="https://portal-sipgn.bgn.go.id/dashboard">

                <i class="bi bi-copy"></i>

            </button>

        </a>

    </section>

    <section class="links">

        <a
            href="https://mpm-sipgn.bgn.go.id/dashboard"
            target="_blank"
            class="app-card">

            <div class="app-left">

                <div class="app-icon">
                    <i class="bi bi-people"></i>
                </div>

                <div>

                    <div class="app-title">
                        MPM SIPGN
                    </div>

                    <div class="app-subtitle">
                        Management Penerima Manfaat
                    </div>

                </div>

            </div>

            <button
                class="copy-btn"
                data-url="https://mpm-sipgn.bgn.go.id/dashboard">

                <i class="bi bi-copy"></i>

            </button>

        </a>

    </section>

    <section class="links">

        <a
            href="https://pop-sipgn.bgn.go.id/cooking"
            target="_blank"
            class="app-card">

            <div class="app-left">

                <div class="app-icon">
                    <i class="bi bi-people"></i>
                </div>

                <div>

                    <div class="app-title">
                        POP SIPGN
                    </div>

                    <div class="app-subtitle">
                        Point of Production (PWA PoP)
                    </div>

                </div>

            </div>

            <button
                class="copy-btn"
                data-url="https://pop-sipgn.bgn.go.id/cooking">

                <i class="bi bi-copy"></i>

            </button>

        </a>

    </section>

    <section class="links">

        <a
            href="https://tauwascare.tauwas.bgn.go.id/login"
            target="_blank"
            class="app-card">

            <div class="app-left">

                <div class="app-icon">
                    <i class="bi bi-people"></i>
                </div>

                <div>

                    <div class="app-title">
                        Tawuascare
                    </div>

                    <div class="app-subtitle">
                        Tauwas
                    </div>

                </div>

            </div>

            <button
                class="copy-btn"
                data-url="https://tauwascare.tauwas.bgn.go.id/login">

                <i class="bi bi-copy"></i>

            </button>

        </a>

    </section>


</div>

@endsection