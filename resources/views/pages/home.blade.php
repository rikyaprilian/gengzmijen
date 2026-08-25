@extends('layouts.app')

@section('title', $settings->portal_name ?? 'Portal Link BGN')

@section('content')

<div class="portal-wrapper">

    <!-- Edit Mode Toolbar -->
    <div id="editModeToolbar" class="edit-mode-only mb-4 p-3 bg-dark text-white rounded-3 shadow d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-warning text-dark px-3 py-2 fs-6">
                <i class="bi bi-pencil-square me-1"></i>MODE EDIT AKTIF
            </span>
            <small class="text-white-50 d-none d-md-inline">Anda dapat menggeser urutan kartu/tautan dan mengelola isi portal.</small>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button type="button" class="btn btn-sm btn-success" id="btnAddCardModal">
                <i class="bi bi-plus-circle me-1"></i>Tambah Link / Group Link Baru
            </button>
            <button type="button" class="btn btn-sm btn-info text-white" id="btnManageCategoriesModal">
                <i class="bi bi-tags me-1"></i>Kelola Kategori
            </button>
            <button type="button" class="btn btn-sm btn-outline-warning" id="btnArchiveModal">
                <i class="bi bi-archive me-1"></i>Arsip
            </button>
            <button type="button" class="btn btn-sm btn-outline-light" id="btnPortalSettingsModal">
                <i class="bi bi-sliders me-1"></i>Pengaturan Portal
            </button>
        </div>
    </div>

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

            {{ $settings->portal_name ?? 'Portal Link BGN' }}

        </h1>

        <p>

            <i>{{ $settings->homepage_message ?? 'Tautan Harian Operasional BGN' }}</i>

        </p>

    </section>

    <section class="search-section">

        <div class="search-box">

            <i class="bi bi-search"></i>

            <input
                id="portal-search"
                type="text"
                class="search-input"
                placeholder="Cari aplikasi atau tautan..."
                autocomplete="off">

        </div>

    </section>

    <x-homepage.category-filter :categories="$categories" />

    <div id="homepage-links" class="cards-sortable-container">

        <hr class="my-4">

        @forelse($cards as $card)

            <x-homepage.card :card="$card" />

        @empty

            <x-homepage.empty />

        @endforelse

    </div>
</div>

<x-modals.archive-modal />

@endsection