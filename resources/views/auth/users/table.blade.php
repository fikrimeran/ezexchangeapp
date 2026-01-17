<div class="card">
  <div class="card-header d-flex align-items-center">
    <h3 class="card-title mb-0">List of Users</h3>
    <a class="btn btn-success btn-sm ml-auto" href="{{ route('auth.users.create') }}">
      <i class="fas fa-plus"></i> Add New User
    </a>
  </div>

  <div class="card-body">
    @if ($message = Session::get('success'))
      <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> {{ $message }}
      </div>
    @endif

    <table class="table table-bordered table-hover text-center">
      <thead class="thead-dark">
        <tr>
          <th style="width: 50px;">No</th>
          <th>Name</th>
          <th>Email</th>
          <th>Joined On</th>
          <th style="width: 200px;">Action</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($users as $user)
          <tr>
            <td>{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}</td>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td>{{ $user->created_at->format('d M Y') }}</td>
            <td>
              <a class="btn btn-info btn-sm" href="{{ route('auth.users.show',$user->id) }}">
                <i class="fas fa-eye"></i>
              </a>
              <a class="btn btn-primary btn-sm" href="{{ route('auth.users.edit',$user->id) }}">
                <i class="fas fa-edit"></i>
              </a>

              <button type="button" class="btn btn-danger btn-sm btn-delete" data-id="{{ $user->id }}">
                <i class="fas fa-trash"></i>
              </button>

              <form id="delete-form-{{ $user->id }}" action="{{ route('auth.users.destroy', $user->id) }}" method="POST" style="display:none;">
                @csrf
                @method('DELETE')
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="text-muted">No users found.</td>
          </tr>
        @endforelse
      </tbody>
    </table>

    {{-- ✅ Pagination for 10 users per page, Bootstrap style --}}
    <div class="mt-3">
      {{ $users->links('pagination::bootstrap-5') }}
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.querySelectorAll('.btn-delete').forEach(button => {
  button.addEventListener('click', function() {
    const userId = this.dataset.id;

    Swal.fire({
      title: 'Are you sure?',
      text: "This user will be deleted permanently!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
      if (result.isConfirmed) {
        document.getElementById(`delete-form-${userId}`).submit();
      }
    });
  });
});
</script>
