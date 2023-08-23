@extends('layouts.app')

@section('content')
<div class="mb-3">
    <a href="{{ route('admin.brands.index') }}" class="btn btn-info">Back</a>
</div>
<br>
<form action="{{ route('admin.brands.update', ['brand' => $brand->id]) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="form-group">
        <label for="brand_name">Name:</label><br>
        <input type="text" name="name" id="brand_name" class="form-control" value="{{ $brand->name }}" required><br>
    </div>
    <button type="submit" class="btn btn-primary">Save changes</button>
</form>
@endsection
