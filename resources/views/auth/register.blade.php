@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-center align-items-start" 
     style="height: 100vh; overflow: hidden; padding-top: 60px;"> {{-- Moves card slightly up --}}

    <div class="col-md-5 col-lg-4">
        <div class="card border-0 shadow-lg rounded-4 p-4" 
             style="backdrop-filter: blur(10px); background: rgba(255, 255, 255, 0.95);">

            <div class="text-center mb-4">
                <h3 class="fw-bold text-dark">{{ __('Create Account') }}</h3>
                <p class="text-muted mb-0">Register to get started</p>
            </div>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                {{-- Name --}}
                <div class="mb-3">
                    <label for="name" class="form-label fw-semibold">{{ __('Full Name') }}</label>
                    <input id="name" 
                           type="text" 
                           class="form-control form-control-lg rounded-3 @error('name') is-invalid @enderror" 
                           name="name" 
                           value="{{ old('name') }}" 
                           required autocomplete="name" 
                           autofocus
                           placeholder="Enter your name">

                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">{{ __('Email Address') }}</label>
                    <input id="email" 
                           type="email" 
                           class="form-control form-control-lg rounded-3 @error('email') is-invalid @enderror" 
                           name="email" 
                           value="{{ old('email') }}" 
                           required autocomplete="email"
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
                           required autocomplete="new-password"
                           placeholder="Create a password">

                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Confirm Password --}}
                <div class="mb-4">
                    <label for="password-confirm" class="form-label fw-semibold">{{ __('Confirm Password') }}</label>
                    <input id="password-confirm" 
                           type="password" 
                           class="form-control form-control-lg rounded-3" 
                           name="password_confirmation" 
                           required autocomplete="new-password"
                           placeholder="Re-enter your password">
                </div>

                {{-- Submit Button --}}
                <div class="d-flex justify-content-between align-items-center">
                    <button type="submit" 
                            class="btn btn-primary px-4 py-2 rounded-3 fw-semibold shadow-sm">
                        {{ __('Register') }}
                    </button>

                    <a href="{{ route('login') }}" class="text-decoration-none text-primary fw-semibold">
                        {{ __('Already have an account?') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
