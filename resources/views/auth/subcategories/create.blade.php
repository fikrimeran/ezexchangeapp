@extends('layouts.admin')

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Add New Subcategory</h3>
            </div>

            <div class="card-body">
                {{-- Validation Errors --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <strong>Whoops!</strong> Please fix the errors below:<br><br>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('auth.subcategories.store') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label for="name">Subcategory Name:</label>
                        <input type="text" name="name" class="form-control" placeholder="Enter subcategory name" value="{{ old('name') }}">
                    </div>

                    <div class="form-group">
                        <label for="category_id">Parent Category:</label>
                        <select name="category_id" class="form-control">
                            <option value="">-- Select Category --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->category_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('auth.subcategories.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
