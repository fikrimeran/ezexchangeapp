<div class="card">
  <div class="card-header d-flex align-items-center">
    <h3 class="card-title mb-0">List of Subcategories</h3>

    <a class="btn btn-success btn-sm ml-auto" href="{{ route('auth.subcategories.create') }}">
      <i class="fas fa-plus"></i> Add New Subcategory
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
          <th>Subcategory Name</th>
          <th>Parent Category</th>
          <th>Created On</th>
          <th style="width: 200px;">Action</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($subcategories as $subcategory)
          <tr>
            <td>{{ $loop->iteration + ($subcategories->currentPage() - 1) * $subcategories->perPage() }}</td>
            <td>{{ $subcategory->name }}</td>
            <td>{{ $subcategory->category->category_name ?? 'N/A' }}</td>
            <td>{{ $subcategory->created_at->format('d M Y') }}</td>
            <td>
              <a class="btn btn-info btn-sm" href="{{ route('auth.subcategories.show', $subcategory->id) }}">
                <i class="fas fa-eye"></i>
              </a>
              <a class="btn btn-primary btn-sm" href="{{ route('auth.subcategories.edit', $subcategory->id) }}">
                <i class="fas fa-edit"></i>
              </a>
              <button type="button" class="btn btn-danger btn-sm btn-delete" data-id="{{ $subcategory->id }}">
                <i class="fas fa-trash"></i>
              </button>
              <form id="delete-form-{{ $subcategory->id }}" 
                    action="{{ route('auth.subcategories.destroy', $subcategory->id) }}" 
                    method="POST" 
                    style="display:none;">
                @csrf
                @method('DELETE')
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="text-muted">No subcategories found.</td>
          </tr>
        @endforelse
      </tbody>
    </table>

    {{-- ✅ Pagination --}}
    <div class="mt-3">
      {{ $subcategories->links('pagination::bootstrap-5') }}
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.querySelectorAll('.btn-delete').forEach(button => {
  button.addEventListener('click', function() {
    const subcategoryId = this.dataset.id;

    Swal.fire({
      title: 'Are you sure?',
      text: "This subcategory will be deleted permanently!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
      if (result.isConfirmed) {
        document.getElementById(`delete-form-${subcategoryId}`).submit();
      }
    });
  });
});
</script>
