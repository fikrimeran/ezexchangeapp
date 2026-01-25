@extends('layouts.admin')

@section('content')
<div class="container-fluid">
  <div class="card">
    <div class="card-header d-flex align-items-center">
      <h3 class="card-title mb-0">Exchange Request Details</h3>
      <a href="{{ route('auth.exchangerequests.index') }}" class="btn btn-secondary btn-sm ml-auto">
        <i class="fas fa-arrow-left"></i> Back
      </a>
    </div>

    <div class="card-body">
      <dl class="row">
        <dt class="col-sm-3">From User:</dt>
        <dd class="col-sm-9">{{ $exchangerequest->fromUser->name ?? 'N/A' }}</dd>

        <dt class="col-sm-3">To User:</dt>
        <dd class="col-sm-9">{{ $exchangerequest->toUser->name ?? 'N/A' }}</dd>

        <dt class="col-sm-3">From Item:</dt>
        <dd class="col-sm-9">{{ $exchangerequest->fromItem->item_name ?? 'N/A' }}</dd>

        <dt class="col-sm-3">To Item:</dt>
        <dd class="col-sm-9">{{ $exchangerequest->toItem->item_name ?? 'N/A' }}</dd>

        <dt class="col-sm-3">Status:</dt>
        <dd class="col-sm-9">{{ ucfirst($exchangerequest->status) }}</dd>

        <dt class="col-sm-3">Created At:</dt>
        <dd class="col-sm-9">{{ $exchangerequest->created_at->format('d M Y, H:i A') }}</dd>
      </dl>
    </div>
  </div>
</div>
@endsection
