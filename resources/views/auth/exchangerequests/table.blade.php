<div class="card">
  <div class="card-header d-flex align-items-center">
    <h3 class="card-title mb-0">Exchange History</h3>
  </div>

  <div class="card-body">
    {{-- ✅ Success Message --}}
    @if ($message = Session::get('success'))
      <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> {{ $message }}
      </div>
    @endif

    {{-- ✅ Exchange History Table --}}
    <table class="table table-bordered table-hover text-center">
      <thead class="thead-dark">
        <tr>
          <th style="width: 50px;">No</th>
          <th>From User</th>
          <th>To User</th>
          <th>From Item</th>
          <th>To Item</th>
          <th>Status</th>
          <th>Created At</th>
          <th style="width: 100px;">Action</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($exchangerequests as $exchange)
          <tr>
            <td>{{ $loop->iteration + ($exchangerequests->currentPage() - 1) * $exchangerequests->perPage() }}</td>
            <td>{{ $exchange->fromUser->name ?? 'N/A' }}</td>
            <td>{{ $exchange->toUser->name ?? 'N/A' }}</td>
            <td>{{ $exchange->fromItem->item_name ?? 'N/A' }}</td>
            <td>{{ $exchange->toItem->item_name ?? 'N/A' }}</td>
            <td>
              <span class="badge badge-{{ $exchange->status === 'accepted' ? 'success' : ($exchange->status === 'pending' ? 'warning' : 'danger') }}">
                {{ ucfirst($exchange->status) }}
              </span>
            </td>
            <td>{{ $exchange->created_at->format('d M Y') }}</td>
            <td>
              {{-- ✅ View only --}}
              <a class="btn btn-info btn-sm" href="{{ route('auth.exchangerequests.show', $exchange->id) }}">
                <i class="fas fa-eye"></i>
              </a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="8" class="text-muted">No exchange history found.</td>
          </tr>
        @endforelse
      </tbody>
    </table>

    {{-- ✅ Pagination --}}
    <div class="d-flex justify-content-center mt-3">
      {!! $exchangerequests->links() !!}
    </div>
  </div>
</div>
