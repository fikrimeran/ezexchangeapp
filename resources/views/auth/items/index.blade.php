@extends('layouts.admin')

@section('content')
    <h2>List of Items</h2>
    @include('auth.items.table')
@endsection
