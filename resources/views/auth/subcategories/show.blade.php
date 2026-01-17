@extends('layouts.admin')

@section('content')
<div class="container-fluid">
  <div class="card">
    <div class="card-header d-flex align-items-center">
      <h3 class="card-title mb-0">Subcategory Details</h3>
      <a href="{{ route('auth.subcategories.index') }}" class="btn btn-secondary btn-sm ml-auto">
        <i class="fas fa-arrow-left"></i> Back
      </a>
    </div>

    <div class="card-body">
      <dl class="row">
        <dt class="col-sm-3">Subcategory Name:</dt>
        <dd class="col-sm-9">{{ $subcategory->name }}</dd>

        <dt class="col-sm-3">Parent Category:</dt>
        <dd class="col-sm-9">{{ $subcategory->category->category_name ?? 'N/A' }}</dd>

        <dt class="col-sm-3">Created On:</dt>
        <dd class="col-sm-9">{{ $subcategory->created_at->format('d M Y H:i') }}</dd>
      </dl>

      {{-- ✅ Edit Button --}}
      <a href="{{ route('auth.subcategories.edit', $subcategory->id) }}" class="btn btn-primary btn-sm">
        <i class="fas fa-edit"></i> Edit
      </a>

      {{-- ✅ Delete Button with SweetAlert --}}
      <button type="button" class="btn btn-danger btn-sm btn-delete" data-id="{{ $subcategory->id }}">
        <i class="fas fa-trash"></i> Delete
      </button>

      {{-- ✅ Hidden Delete Form --}}
      <form id="delete-form-{{ $subcategory->id }}" 
            action="{{ route('auth.subcategories.destroy', $subcategory->id) }}" 
            method="POST" 
            style="display:none;">
        @csrf
        @method('DELETE')
      </form>
    </div>
  </div>
</div>

{{-- ✅ SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const deleteButton = document.querySelector('.btn-delete');

  if(deleteButton){
    deleteButton.addEventListener('click', function() {
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
  }
});
</script>
@endsection
