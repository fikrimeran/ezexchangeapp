@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">

            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Edit Profile</h4>
                </div>

                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="card-body pb-0">
                        {{-- Name --}}
                        <div class="mb-3">
                            <label class="form-label" for="name">Name</label>
                            <input   id="name" name="name" type="text"
                                     class="form-control @error('name') is-invalid @enderror"
                                     value="{{ old('name', Auth::user()->name) }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Email --}}
                        <div class="mb-3">
                            <label class="form-label" for="email">Email</label>
                            <input   id="email" name="email" type="email"
                                     class="form-control @error('email') is-invalid @enderror"
                                     value="{{ old('email', Auth::user()->email) }}" required>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Email Status (read-only) --}}
                        <div class="mb-3">
                            <label class="form-label">Email Status</label>
                            @if(Auth::user()->is_verified)
                                <input type="text" class="form-control" value="Verified" disabled>
                            @else
                                <input type="text" class="form-control" value="Not Verified" disabled>
                            @endif
                        </div>

                        {{-- Member Since (read-only) --}}
                        <div class="mb-3">
                            <label class="form-label">Member Since</label>
                            <input type="text" class="form-control" 
                                   value="{{ Auth::user()->created_at->format('d F Y') }}" disabled>
                        </div>

                        {{-- Password (optional) --}}
                        <div class="mb-3">
                            <label class="form-label" for="password">New Password <small class="text-muted">(leave blank to keep)</small></label>
                            <input   id="password" name="password" type="password"
                                     class="form-control @error('password') is-invalid @enderror">
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Confirm password --}}
                        <div class="mb-4">
                            <label class="form-label" for="password_confirmation">Confirm New Password</label>
                            <input   id="password_confirmation" name="password_confirmation" type="password"
                                     class="form-control">
                        </div>
                    </div>

                    <!-- Footer buttons -->
                    <div class="card-footer bg-light d-flex justify-content-between">
                        <a href="{{ route('profile') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection
