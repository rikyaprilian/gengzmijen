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
                id="portal-search"
                type="text"
                class="search-input"
                placeholder="Cari aplikasi..."
                autocomplete="off">

        </div>

    </section>

    <x-homepage.category-filter :categories="$categories" />

    <div id="homepage-links">

        <hr class="my-5">

        @foreach($cards as $card)

            <x-homepage.card :card="$card" />

        @endforeach

    </div>
</div>
@endsection