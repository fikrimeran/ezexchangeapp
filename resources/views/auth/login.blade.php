@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-center align-items-start" 
     style="height: 100vh; overflow: hidden; padding-top: 60px;">  {{-- 👈 pushes the card slightly up --}}
    
    <div class="col-md-5 col-lg-4">
        <div class="card border-0 shadow-lg rounded-4 p-4" 
             style="backdrop-filter: blur(10px); background: rgba(255, 255, 255, 0.95);">

            <div class="text-center mb-4">
                <h3 class="fw-bold text-dark">{{ __('Welcome Back!') }}</h3>
                <p class="text-muted mb-0">Sign in to continue</p>
            </div>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                {{-- Email --}}
                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">{{ __('Email Address') }}</label>
                    <input id="email" 
                           type="email" 
                           class="form-control form-control-lg rounded-3 @error('email') is-invalid @enderror" 
                           name="email" 
                           value="{{ old('email') }}" 
                           required autocomplete="email" 
                           autofocus 
                           placeholder="Enter your email">

                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold">{{ __('Password') }}</label>
                    <input id="password" 
                           type="password" 
                           class="form-control form-control-lg rounded-3 @error('password') is-invalid @enderror" 
                           name="password" 
                           required autocomplete="current-password" 
                           placeholder="Enter your password">

                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Remember Me --}}
                <div class="form-check mb-4">
                    <input class="form-check-input" 
                           type="checkbox" 
                           name="remember" 
                           id="remember" 
                           {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label" for="remember">
                        {{ __('Remember Me') }}
                    </label>
                </div>

                {{-- Buttons --}}
                <div class="d-flex justify-content-between align-items-center">
                    <button type="submit" 
                            class="btn btn-primary px-4 py-2 rounded-3 fw-semibold shadow-sm">
                        {{ __('Login') }}
                    </button>

                    @if (Route::has('password.request'))
                        <a class="text-decoration-none text-primary fw-semibold" href="{{ route('password.request') }}">
                            {{ __('Forgot Password?') }}
                        </a>
                    @endif
                </div>
            </form>

            <hr class="my-4">

            <div class="text-center">
                <small class="text-muted">
                    Don’t have an account? 
                    <a href="{{ route('register') }}" class="text-primary fw-semibold text-decoration-none">
                        Register
                    </a>
                </small>
            </div>
        </div>
    </div>
</div>
@endsection
