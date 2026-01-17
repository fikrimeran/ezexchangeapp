@extends('layouts.admin')

@section('content')
<div class="container-fluid">
  <div class="card">
    <div class="card-header d-flex align-items-center">
      <h3 class="card-title mb-0">User Details</h3>
      <a href="{{ route('auth.users.index') }}" class="btn btn-secondary btn-sm ml-auto">
        <i class="fas fa-arrow-left"></i> Back
      </a>
    </div>

    <div class="card-body">
      <dl class="row">
        <dt class="col-sm-3">Name:</dt>
        <dd class="col-sm-9">{{ $user->name }}</dd>

        <dt class="col-sm-3">Email:</dt>
        <dd class="col-sm-9">{{ $user->email }}</dd>

        <dt class="col-sm-3">Joined On:</dt>
        <dd class="col-sm-9">{{ $user->created_at->format('d M Y H:i') }}</dd>
      </dl>

      <a href="{{ route('auth.users.edit', $user->id) }}" class="btn btn-primary btn-sm">
        <i class="fas fa-edit"></i> Edit
      </a>

      <!-- ✅ Delete Button with SweetAlert -->
      <button type="button" class="btn btn-danger btn-sm btn-delete" data-id="{{ $user->id }}">
        <i class="fas fa-trash"></i> Delete
      </button>

      <!-- ✅ Hidden Delete Form -->
      <form id="delete-form-{{ $user->id }}" action="{{ route('auth.users.destroy', $user->id) }}" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
      </form>
    </div>
  </div>
</div>

<!-- ✅ SweetAlert2 Script -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  document.querySelector('.btn-delete').addEventListener('click', function() {
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
</script>
@endsection
