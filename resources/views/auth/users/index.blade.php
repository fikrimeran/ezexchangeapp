@extends('layouts.admin')

@section('content')
<div class="container-fluid">

  {{-- ✅ Optional Search Bar --}}
  <form method="GET" action="{{ route('auth.users.index') }}" class="mb-3">
    <div class="input-group">
      <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by name or email...">
      <div class="input-group-append">
        <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
      </div>
    </div>
  </form>

  {{-- ✅ User Table --}}
  @include('auth.users.table')

</div>
@endsection
