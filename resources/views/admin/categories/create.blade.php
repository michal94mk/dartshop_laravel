@extends('layouts.app')

@section('content')
<div class="mb-3">
    <a href="{{ route('admin.categories.index') }}" class="btn btn-info">Back</a>
</div>

<form action="{{ route('admin.categories.store') }}" method="POST">
    @csrf
    <div class="form-group">
        <label for="category_name">Name:</label><br>
        <input type="text" name="name" id="category_name" class="form-control" required><br>
    </div>
    <button type="submit" class="btn btn-primary">Save</button>
</form>
@endsection
