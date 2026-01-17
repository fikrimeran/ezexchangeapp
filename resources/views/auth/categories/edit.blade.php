@extends('layouts.admin')

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Edit Category</h3>
            </div>

            <form action="{{ route('auth.categories.update', $category->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="card-body">
                    {{-- ✅ Validation Errors --}}
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

                    {{-- ✅ Category Name --}}
                    <div class="form-group">
                        <label for="category_name"><strong>Category Name</strong></label>
                        <input type="text" name="category_name" id="category_name" 
                               value="{{ old('category_name', $category->category_name) }}" 
                               class="form-control" placeholder="Enter category name">
                    </div>
                </div>

                <div class="card-footer d-flex justify-content-between">
                    <a href="{{ route('auth.categories.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection
