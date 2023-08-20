@extends('layouts.app')

@section('content')
@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif
@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<a href="{{ route('admin.categories.index') }}" class="btn {{ Request::routeIs('admin.categories.index') ? 'btn-primary' : 'btn-default' }}">Categories</a>
<a href="{{ route('admin.products.index') }}" class="btn {{ Request::routeIs('admin.products.index') ? 'btn-primary' : 'btn-default' }}">Products</a>
<a href="{{ route('admin.users.index') }}" class="btn {{ Request::routeIs('admin.users.index') ? 'btn-primary' : 'btn-default' }}">Users</a>
<hr>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <a href="{{ route('admin.categories.create') }}" class="btn btn-success mb-3">Add Category</a>

                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categories as $category)
                                <tr>
                                    <td>{{ $category->id }}</td>
                                    <td>{{ $category->name }}</td>
                                    <td>
                                        <a href="{{ route('admin.categories.edit', ['category' => $category->id]) }}" class="btn btn-sm btn-primary">Edit</a>

                                        <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteModal{{ $category->id }}">Delete</button>

                                        <!-- Delete Modal -->
                                        <div class="modal fade" id="deleteModal{{ $category->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel{{ $category->id }}" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header" style="text-align: center;">
                                                        <h5 class="modal-title" id="deleteModalLabel{{ $category->id }}">Delete Confirmation</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p style="text-align: center;">Are you sure you want to delete the category "{{ $category->name }}"?</p>
                                                    </div>
                                                    <div class="modal-footer" style="display: flex; justify-content: space-between; align-items: center;">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                        <form action="{{ route('admin.categories.destroy', ['category' => $category->id]) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger">Delete</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- End Delete Modal -->
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{ $categories->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
