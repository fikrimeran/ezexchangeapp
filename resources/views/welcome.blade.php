<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>EZExchange - Swap Smarter</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Custom Animation -->
    <style>
    @keyframes fadeUp {
      0% {
        opacity: 0;
        transform: translateY(20px);
      }
      100% {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .animate-fade-up {
      animation: fadeUp 2s ease-out forwards;
    }

    /* Start hidden for scroll animation */
    .hidden-opacity {
      opacity: 0;
      transform: translateY(20px);
    }
    </style>
</head>

<body class="antialiased font-sans text-gray-800
bg-gradient-to-br from-red-200 via-orange-100 to-yellow-100">

    <!-- ================= NAVBAR ================= -->
    @if (Route::has('login'))
    <nav class="fixed top-0 left-0 right-0 p-6 z-10 flex items-center justify-between">
        <!-- Left: EZExchange text link with icon -->
        <div>
            <a href="{{ url('/') }}" 
               class="flex items-center text-red-600 font-bold text-lg hover:text-red-700 transition">
                <i class="bi bi-arrow-repeat mr-2"></i>
                EZExchange
            </a>
        </div>

        <!-- Right: Login/Register links -->
        <div class="text-right">
            @auth
                <a href="{{ url('/user/home') }}"
                   class="text-sm font-semibold text-gray-700 hover:text-red-600 transition">
                    Home
                </a>
            @else
                <a href="{{ route('login') }}"
                   class="text-sm font-semibold text-gray-700 hover:text-red-600 transition">
                    Log in
                </a>

                @if (Route::has('register'))
                    <a href="{{ route('register') }}"
                       class="ml-4 text-sm font-semibold text-gray-700 hover:text-red-600 transition">
                        Register
                    </a>
                @endif
            @endauth
        </div>
    </nav>
    @endif

    <!-- ================= HERO ================= -->
    <section class="flex items-center justify-center min-h-screen px-6 sm:px-12 lg:px-24 animate-fade-up">
        <div class="text-center max-w-3xl bg-white/60 backdrop-blur-md p-10 rounded-3xl shadow-xl">
            <h1 class="text-4xl sm:text-5xl font-bold mb-6">
                Welcome to <span class="text-red-600">EZExchange</span>
            </h1>

            <p class="text-lg sm:text-xl text-gray-700 mb-8 leading-relaxed">
                A smart and simple platform for swapping items in real-time.
                Connect with nearby users, discover great deals, and exchange
                goods easily using our geolocation-powered system.
            </p>

            <a href="{{ route('login') }}"
               class="inline-block px-10 py-4 bg-red-500 text-white rounded-full
                      shadow-lg hover:bg-red-600 transition">
                Get Started
            </a>
        </div>
    </section>

    <!-- ================= ABOUT ================= -->
    <section id="about" class="py-20 px-6 bg-gradient-to-r from-yellow-50 via-orange-50 to-red-50 hidden-opacity">
        <div class="max-w-6xl mx-auto">

            <!-- Title -->
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-red-600 mb-4">
                    About EZExchange
                </h2>
                <p class="text-lg text-gray-700 max-w-3xl mx-auto">
                    <strong>EZExchange</strong> is a peer-to-peer platform for
                    <em>cash-free</em> item exchanges with smart matching
                    and geolocation support.
                </p>
            </div>

            <!-- Features -->
            <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">

                <div class="bg-white p-6 rounded-xl shadow-md hover:shadow-xl transition">
                    <div class="flex items-center mb-3">
                        <i class="bi bi-geo-alt-fill text-red-500 text-2xl mr-3"></i>
                        <h3 class="font-semibold text-lg">Geolocation</h3>
                    </div>
                    <p class="text-gray-600">
                        Trade with users who are truly nearby.
                    </p>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-md hover:shadow-xl transition">
                    <div class="flex items-center mb-3">
                        <i class="bi bi-lightning-charge-fill text-yellow-500 text-2xl mr-3"></i>
                        <h3 class="font-semibold text-lg">Smart Matching</h3>
                    </div>
                    <p class="text-gray-600">
                        Automatic item recommendations based on preferences.
                    </p>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-md hover:shadow-xl transition">
                    <div class="flex items-center mb-3">
                        <i class="bi bi-cash-coin text-green-500 text-2xl mr-3"></i>
                        <h3 class="font-semibold text-lg">Cash-Free</h3>
                    </div>
                    <p class="text-gray-600">
                        Exchange items without using money.
                    </p>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-md hover:shadow-xl transition">
                    <div class="flex items-center mb-3">
                        <i class="bi bi-chat-dots-fill text-blue-500 text-2xl mr-3"></i>
                        <h3 class="font-semibold text-lg">Secure Chat</h3>
                    </div>
                    <p class="text-gray-600">
                        Built-in chat without sharing phone numbers.
                    </p>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-md hover:shadow-xl transition">
                    <div class="flex items-center mb-3">
                        <i class="bi bi-shield-check text-green-600 text-2xl mr-3"></i>
                        <h3 class="font-semibold text-lg">Safe Trading</h3>
                    </div>
                    <p class="text-gray-600">
                        Designed to prevent scams and abuse.
                    </p>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-md hover:shadow-xl transition">
                    <div class="flex items-center mb-3">
                        <i class="bi bi-ui-checks text-gray-500 text-2xl mr-3"></i>
                        <h3 class="font-semibold text-lg">Easy to Use</h3>
                    </div>
                    <p class="text-gray-600">
                        Clean UI suitable for all users.
                    </p>
                </div>

            </div>

            <!-- CTA -->
            <div class="text-center mt-20">
                <a href="{{ route('login') }}"
                   class="inline-block px-12 py-4 bg-red-500 text-white text-lg
                          rounded-full shadow-lg hover:bg-red-600 transition">
                    Start Exploring
                </a>
            </div>

        </div>
    </section>

    <!-- ================= SCROLL ANIMATION SCRIPT ================= -->
    <script>
        const aboutSection = document.querySelector('#about');

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fade-up');
                    entry.target.classList.remove('hidden-opacity');
                    observer.unobserve(entry.target); // animate only once
                }
            });
        }, { threshold: 0.2 });

        observer.observe(aboutSection);
    </script>

</body>
</html>
