@extends('layouts.app')

@section('content')
<div class="mb-3">
    <a href="{{ route('admin.products.index') }}" class="btn btn-info">Back</a>
</div>
<br>
<table class="table table-bordered">
    <thead class="thead-light">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Description</th>
            <th>Price</th>
            <th>Category</th>
            <th>Photo</th>
        </tr>
    </thead>
    <tbody>
            <tr>
                <td>{{ $product->id }}</td>
                <td>{{ $product->name }}</td>
                <td>{{ $product->description }}</td>
                <td>{{ $product->price }}</td>
                <td>{{ $product->category->name }}</td>
                <td>
                    @if ($product->image)
                        <img src="{{ asset($product->image) }}" alt="{{ $product->name }} Thumbnail" class="thumbnail" style="max-width: 300px; max-height: 300px; border: 1px solid">
                    @else
                        <span class="text-muted">No photo</span>
                    @endif

                </td>
            </tr>
    </tbody>
</table>

@endsection
