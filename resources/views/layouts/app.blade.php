<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'EZExchange') }}</title>

    <!-- Nunito & Bootstrap Icons -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,600,700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Vite -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <!-- Custom Styles -->
    <style>
        .navbar-gradient {
            background: linear-gradient(90deg, #ff5f6d 0%, #ffc371 100%);
        }
        .navbar .nav-link {
            position: relative;
            padding-bottom: .25rem;
            color: #212529;
        }
        .navbar .nav-link:hover,
        .navbar .nav-link:focus {
            color: #000;
        }
        .navbar .nav-link::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 0;
            height: 2px;
            background: currentColor;
            transition: width .25s ease;
        }
        .navbar .nav-link:hover::after,
        .navbar .nav-link:focus::after,
        .navbar .nav-link.active::after {
            width: 100%;
        }
        .navbar-brand {
            color: #212529 !important;
            font-weight: bold;
        }
        .navbar-brand:hover {
            color: #000 !important;
        }
        body {
            background: linear-gradient(
                to right,
                #fffefb,   /* almost white warm yellow */
                #fffaf5,   /* ultra soft peach */
                #fff7f7    /* very light blush */
            );
            min-height: 100vh;
        }
    </style>
</head>

<body>
<div id="app">
    <nav class="navbar navbar-expand-md navbar-light navbar-gradient shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ url('/user/home') }}">
                <i class="bi bi-arrow-repeat"></i> EZExchange
            </a>

            <button class="navbar-toggler" type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent"
                    aria-controls="navbarSupportedContent"
                    aria-expanded="false"
                    aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto"></ul>

                <ul class="navbar-nav ms-auto align-items-md-center">
                    @auth
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/user/inventory') }}">
                                <i class="bi bi-box2"></i> My Inventory
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/user/explore') }}">
                                <i class="bi bi-compass"></i> Explore
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/user/about') }}">
                                <i class="bi bi-people"></i> About Us
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/user/notification') }}">
                                <i class="bi bi-bell"></i> Notifications
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/user/chat') }}">
                                <i class="bi bi-chat-dots"></i> Chat
                            </a>
                        </li>

                        <!-- User Dropdown -->
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown"
                               class="nav-link dropdown-toggle"
                               href="#"
                               role="button"
                               data-bs-toggle="dropdown"
                               aria-haspopup="true"
                               aria-expanded="false">
                                <i class="bi bi-person-circle"></i> {{ Auth::user()->name }}
                            </a>

                            <div class="dropdown-menu dropdown-menu-end shadow-sm">
                                <a class="dropdown-item" href="{{ url('/user/profile') }}">
                                    <i class="bi bi-person"></i> Profile
                                </a>

                                <div class="dropdown-divider"></div>

                                <a class="dropdown-item"
                                   href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="bi bi-box-arrow-right"></i> {{ __('Logout') }}
                                </a>

                                <form id="logout-form"
                                      action="{{ route('logout') }}"
                                      method="POST"
                                      class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    @else
                        @if (Route::has('login'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">
                                    <i class="bi bi-box-arrow-in-right"></i> {{ __('Login') }}
                                </a>
                            </li>
                        @endif

                        @if (Route::has('register'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">
                                    <i class="bi bi-pencil-square"></i> {{ __('Register') }}
                                </a>
                            </li>
                        @endif
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <main class="py-4">
        <div class="container">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                            aria-label="Close"></button>
                </div>
            @endif
        </div>

        @yield('content')
    </main>
</div>

@stack('scripts')
</body>
</html>
