<div class="card">
  <div class="card-header d-flex align-items-center">
    <h3 class="card-title mb-0">List of Categories</h3>

    <a class="btn btn-success btn-sm ml-auto" href="{{ route('auth.categories.create') }}">
      <i class="fas fa-plus"></i> Add New Category
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
          <th>Joined On</th>
          <th style="width: 200px;">Action</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($categories as $category)
          <tr>
            <td>{{ $loop->iteration + ($categories->currentPage() - 1) * $categories->perPage() }}</td>
            <td>{{ $category->category_name }}</td>
            <td>{{ $category->created_at->format('d M Y') }}</td>
            <td>
              <a class="btn btn-info btn-sm" href="{{ route('auth.categories.show', $category->id) }}">
                <i class="fas fa-eye"></i>
              </a>
              <a class="btn btn-primary btn-sm" href="{{ route('auth.categories.edit', $category->id) }}">
                <i class="fas fa-edit"></i>
              </a>

              <button type="button" class="btn btn-danger btn-sm btn-delete" data-id="{{ $category->id }}">
                <i class="fas fa-trash"></i>
              </button>

              <form id="delete-form-{{ $category->id }}" action="{{ route('auth.categories.destroy', $category->id) }}" method="POST" style="display:none;">
                @csrf
                @method('DELETE')
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="4" class="text-muted">No categories found.</td>
          </tr>
        @endforelse
      </tbody>
    </table>

    {{-- ✅ Pagination (Bootstrap 5) --}}
    <div class="mt-3">
      {{ $categories->links('pagination::bootstrap-5') }}
    </div>
  </div>
</div>

{{-- SweetAlert2 Delete Confirmation --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.querySelectorAll('.btn-delete').forEach(button => {
  button.addEventListener('click', function() {
    const categoryId = this.dataset.id;

    Swal.fire({
      title: 'Are you sure?',
      text: "This category will be deleted permanently!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
      if (result.isConfirmed) {
        document.getElementById(`delete-form-${categoryId}`).submit();
      }
    });
  });
});
</script>
