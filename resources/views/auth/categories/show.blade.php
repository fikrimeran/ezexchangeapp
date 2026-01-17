@extends('layouts.admin')

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Category Details</h3>
            </div>

            <div class="card-body">
                <div class="form-group">
                    <strong>Category Name:</strong>
                    <p class="form-control-plaintext">{{ $category->category_name }}</p>
                </div>

                <div class="form-group">
                    <strong>Created At:</strong>
                    <p class="form-control-plaintext">{{ $category->created_at->format('d M Y, H:i A') }}</p>
                </div>
            </div>

            <div class="card-footer d-flex justify-content-between">
                <a href="{{ route('auth.categories.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>

                <div>
                    <a href="{{ route('auth.categories.edit', $category->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit
                    </a>

                    {{-- Delete Button with Confirmation --}}
                    <button type="button" class="btn btn-danger btn-delete">
                        <i class="fas fa-trash"></i> Delete
                    </button>

                    {{-- Hidden Delete Form --}}
                    <form id="delete-form" action="{{ route('auth.categories.destroy', $category->id) }}" method="POST" style="display:none;">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- ✅ SweetAlert2 Script --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.querySelector('.btn-delete').addEventListener('click', function() {
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
            document.getElementById('delete-form').submit();
        }
    });
});
</script>
@endsection
