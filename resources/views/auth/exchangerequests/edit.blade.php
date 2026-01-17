@extends('layouts.admin')

@section('content')
<div class="container-fluid">
  <div class="card">
    <div class="card-header d-flex align-items-center">
      <h3 class="card-title mb-0">Edit Exchange Request</h3>
      <a href="{{ route('auth.exchangerequests.index') }}" class="btn btn-secondary btn-sm ml-auto">
        <i class="fas fa-arrow-left"></i> Back
      </a>
    </div>

    <div class="card-body">
      {{-- Validation Errors --}}
      @if ($errors->any())
        <div class="alert alert-danger">
          <strong>Whoops!</strong> There were some problems with your input.<br><br>
          <ul>
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form action="{{ route('auth.exchangerequests.update', $exchange->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- From User --}}
        <div class="form-group">
          <label for="from_user_id">From User</label>
          <select name="from_user_id" class="form-control">
            @foreach ($users as $user)
              <option value="{{ $user->id }}" {{ $exchange->from_user_id == $user->id ? 'selected' : '' }}>
                {{ $user->name }}
              </option>
            @endforeach
          </select>
        </div>

        {{-- To User --}}
        <div class="form-group">
          <label for="to_user_id">To User</label>
          <select name="to_user_id" class="form-control">
            @foreach ($users as $user)
              <option value="{{ $user->id }}" {{ $exchange->to_user_id == $user->id ? 'selected' : '' }}>
                {{ $user->name }}
              </option>
            @endforeach
          </select>
        </div>

        {{-- From Item --}}
        <div class="form-group">
          <label for="from_item_id">From Item</label>
          <select name="from_item_id" class="form-control">
            @foreach ($items as $item)
              <option value="{{ $item->id }}" {{ $exchange->from_item_id == $item->id ? 'selected' : '' }}>
                {{ $item->item_name }}
              </option>
            @endforeach
          </select>
        </div>

        {{-- To Item --}}
        <div class="form-group">
          <label for="to_item_id">To Item</label>
          <select name="to_item_id" class="form-control">
            @foreach ($items as $item)
              <option value="{{ $item->id }}" {{ $exchange->to_item_id == $item->id ? 'selected' : '' }}>
                {{ $item->item_name }}
              </option>
            @endforeach
          </select>
        </div>

        {{-- Status --}}
        <div class="form-group">
          <label for="status">Status</label>
          <select name="status" class="form-control">
            <option value="pending" {{ $exchange->status == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="accepted" {{ $exchange->status == 'accepted' ? 'selected' : '' }}>Accepted</option>
            <option value="rejected" {{ $exchange->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
          </select>
        </div>

        <div class="d-flex justify-content-between">
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Update
          </button>
          <a href="{{ route('auth.exchangerequests.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
