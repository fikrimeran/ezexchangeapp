@extends('layouts.app')

@section('title', 'About EZExchange')

@section('content')
<div class="container py-5">

    {{-- HERO / INTRO --}}
    <section class="row justify-content-center mb-5">
        <div class="col-lg-10 text-center">
            <h1 class="fw-bold mb-3 display-5 text-primary">
                About <span class="text-dark">EZExchange</span>
            </h1>

            <p class="lead mx-auto" style="max-width: 750px;">
                <strong>EZExchange</strong> is a modern peer-to-peer swapping platform built for hassle-free,
                <em>cash-free</em> item exchanges. With powerful Geolocation and Smart Matching features, you’ll always
                see the most relevant and nearby exchange opportunities.
            </p>
        </div>
    </section>

    {{-- FEATURES --}}
    <section class="mb-5">
        <h2 class="h4 fw-bold text-center mb-4 text-uppercase text-secondary">
            Why Use EZExchange?
        </h2>

        <div class="row g-4">

            {{-- Geolocation --}}
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow border-0 rounded-4 hover-shadow transition">
                    <div class="card-body d-flex">
                        <i class="bi bi-geo-alt-fill fs-2 text-primary me-3"></i>
                        <div>
                            <h3 class="h6 fw-bold mb-1">Geolocation</h3>
                            <p class="text-muted mb-0">Find traders who are truly nearby.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Automatic Suggestions --}}
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow border-0 rounded-4 hover-shadow transition">
                    <div class="card-body d-flex">
                        <i class="bi bi-lightning-charge-fill fs-2 text-warning me-3"></i>
                        <div>
                            <h3 class="h6 fw-bold mb-1">Automatic Suggestions</h3>
                            <p class="text-muted mb-0">Get the best matches based on your preferences.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Cash-Free Barter --}}
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow border-0 rounded-4 hover-shadow transition">
                    <div class="card-body d-flex">
                        <i class="bi bi-cash-coin fs-2 text-success me-3"></i>
                        <div>
                            <h3 class="h6 fw-bold mb-1">Cash-Free Barter</h3>
                            <p class="text-muted mb-0">Exchange items or skills without spending money.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Built-In Chat --}}
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow border-0 rounded-4 hover-shadow transition">
                    <div class="card-body d-flex">
                        <i class="bi bi-chat-dots-fill fs-2 text-info me-3"></i>
                        <div>
                            <h3 class="h6 fw-bold mb-1">Built-In Chat</h3>
                            <p class="text-muted mb-0">Communicate safely without sharing contacts.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Reputation System --}}
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow border-0 rounded-4 hover-shadow transition">
                    <div class="card-body d-flex">
                        <i class="bi bi-shield-check fs-2 text-success me-3"></i>
                        <div>
                            <h3 class="h6 fw-bold mb-1">Trustworthy System</h3>
                            <p class="text-muted mb-0">A reliable and scam-free exchange experience.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- User Friendly --}}
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow border-0 rounded-4 hover-shadow transition">
                    <div class="card-body d-flex">
                        <i class="bi bi-ui-checks fs-2 text-secondary me-3"></i>
                        <div>
                            <h3 class="h6 fw-bold mb-1">User-Friendly Interface</h3>
                            <p class="text-muted mb-0">Simple, intuitive, and easy for anyone to use.</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    {{-- CTA --}}
    <section class="text-center mt-4">
        <a href="{{ url('/user/explore') }}" class="btn btn-primary btn-lg px-5 py-3 rounded-pill shadow-lg">
            Start Exploring
        </a>
    </section>

</div>

<style>
    .hover-shadow:hover {
        transform: translateY(-4px);
        box-shadow: 0px 10px 18px rgba(0, 0, 0, 0.12);
    }

    .transition {
        transition: all 0.25s ease-in-out;
    }
</style>
@endsection
