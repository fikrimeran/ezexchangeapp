@extends('layouts.admin')

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Add New Category</h3>
            </div>

            <div class="card-body">
                {{-- ✅ Show validation errors --}}
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

                {{-- ✅ Create form --}}
                <form action="{{ route('auth.categories.store') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label for="category_name"><strong>Category Name:</strong></label>
                        <input type="text" name="category_name" value="{{ old('category_name') }}" class="form-control" placeholder="Enter category name">
                    </div>

                    <div class="d-flex justify-content-between">
                        <a class="btn btn-secondary" href="{{ route('auth.categories.index') }}">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-plus-circle"></i> Create Category
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
