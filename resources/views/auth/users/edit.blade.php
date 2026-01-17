@extends('layouts.admin')

@section('content')
<div class="container-fluid">
  <div class="card">
    <div class="card-header d-flex align-items-center">
      <h3 class="card-title mb-0">Edit User</h3>
      <a href="{{ route('auth.users.index') }}" class="btn btn-secondary btn-sm ml-auto">
        <i class="fas fa-arrow-left"></i> Back
      </a>
    </div>

    <div class="card-body">
      {{-- Validation Errors --}}
      @if ($errors->any())
        <div class="alert alert-danger">
          <strong>Whoops!</strong> Please fix the following:
          <ul class="mb-0">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form action="{{ route('auth.users.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
          <label for="name">Name</label>
          <input 
            type="text" 
            name="name" 
            class="form-control" 
            value="{{ old('name', $user->name) }}">
        </div>

        <div class="form-group">
          <label for="email">Email</label>
          <input 
            type="email" 
            name="email" 
            class="form-control" 
            value="{{ old('email', $user->email) }}">
        </div>

        <div class="form-group">
          <label for="password">Password <small>(leave blank to keep current)</small></label>
          <input 
            type="password" 
            name="password" 
            class="form-control" 
            placeholder="Enter new password if needed">
        </div>

        <button type="submit" class="btn btn-primary">
          <i class="fas fa-save"></i> Update
        </button>
      </form>
    </div>
  </div>
</div>
@endsection
