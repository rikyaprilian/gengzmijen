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

            Portal Pelaporan BGN

        </h1>

        <p>

            <i>Karya Gengz Mijen</i>

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
                        Sistem Informasi Human Resource
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

</div>

@endsection