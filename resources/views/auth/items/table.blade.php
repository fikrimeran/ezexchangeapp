<div class="card">
    <div class="card-header d-flex align-items-center">
        <h3 class="card-title mb-0">Items</h3>
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
                    <th style="width:50px;">No</th>
                    <th>Item Name</th>
                    <th>User</th>
                    <th>Category</th>
                    <th>Subcategory</th>
                    <th>Location</th>
                    <th>Available</th>
                    <th style="width:180px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $item)
                    <tr>
                        <td>{{ $loop->iteration + ($items->currentPage() - 1) * $items->perPage() }}</td>
                        <td>{{ $item->item_name }}</td>
                        <td>{{ $item->user->name ?? 'N/A' }}</td>
                        <td>{{ $item->category->category_name ?? 'N/A' }}</td>
                        <td>{{ $item->subcategory->name ?? 'N/A' }}</td>
                        <td>{{ $item->item_location }}</td>
                        <td>{{ $item->is_available ? 'Yes' : 'No' }}</td>
                        <td>
                            {{-- View button --}}
                            <a href="{{ route('auth.items.show', $item->id) }}" class="btn btn-info btn-sm" title="View">
                                <i class="fas fa-eye"></i>
                            </a>

                            {{-- Edit button --}}
                            <a href="{{ route('auth.items.edit', $item->id) }}" class="btn btn-primary btn-sm" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>

                            {{-- Delete button --}}
                            <button type="button" class="btn btn-danger btn-sm btn-delete" data-id="{{ $item->id }}" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>

                            <form id="delete-form-{{ $item->id }}" action="{{ route('auth.items.destroy', $item->id) }}" method="POST" style="display:none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-muted">No items found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination Links using Bootstrap 5 --}}
        <div class="mt-3">
            {{ $items->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

{{-- Delete confirmation using SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.querySelectorAll('.btn-delete').forEach(button => {
    button.addEventListener('click', function () {
        const itemId = this.dataset.id;

        Swal.fire({
            title: 'Are you sure?',
            text: "This item will be deleted permanently!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`delete-form-${itemId}`).submit();
            }
        });
    });
});
</script>
