@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">

            <!-- Profile Card -->
            <div class="card shadow-sm border-0">

                <!-- Header -->
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">My Profile</h4>

                    <a href="{{ route('profile.edit') }}" class="btn btn-light btn-sm">
                        <i class="bi bi-pencil-square me-1"></i> Edit Details
                    </a>
                </div>

                <!-- Body -->
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">

                        <!-- Name -->
                        <li class="list-group-item d-flex justify-content-between align-items-center mb-2">
                            <span><strong>Name</strong></span>
                            <span>{{ Auth::user()->name }}</span>
                        </li>

                        <!-- Email -->
                        <li class="list-group-item d-flex justify-content-between align-items-center mb-2">
                            <span><strong>Email</strong></span>
                            <span>{{ Auth::user()->email }}</span>
                        </li>

                        <!-- Email Verification -->
                        <li class="list-group-item d-flex justify-content-between align-items-center mb-2">
                            <span><strong>Email Status</strong></span>
                            @if(Auth::user()->is_verified)
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle-fill me-1"></i> Verified
                                </span>
                            @else
                                <span class="badge bg-warning text-dark">
                                    <i class="bi bi-exclamation-circle-fill me-1"></i> Not Verified
                                </span>
                            @endif
                        </li>

                        <!-- Member Since -->
                        <li class="list-group-item d-flex justify-content-between align-items-center mb-2">
                            <span><strong>Member Since</strong></span>
                            <span>{{ Auth::user()->created_at->format('d F Y') }}</span>
                        </li>

                        <!-- Password -->
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><strong>Password</strong></span>
                            <span>********</span>
                        </li>

                    </ul>
                </div>

            </div>
            <!-- /Profile Card -->

        </div>
    </div>
</div>
@endsection
