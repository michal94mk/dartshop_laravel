@extends('layouts.app')

@section('content')
@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
<a href="{{ route('admin.categories.index') }}" class="btn {{ Request::routeIs('admin.categories.index') ? 'btn-primary' : 'btn-default' }}">Categories</a>
<a href="{{ route('admin.brands.index') }}" class="btn {{ Request::routeIs('admin.brands.index') ? 'btn-primary' : 'btn-default' }}">Brands</a>
<a href="{{ route('admin.products.index') }}" class="btn {{ Request::routeIs('admin.products.index') ? 'btn-primary' : 'btn-default' }}">Products</a>
<a href="{{ route('admin.users.index') }}" class="btn {{ Request::routeIs('admin.users.index') ? 'btn-primary' : 'btn-default' }}">Users</a>
<hr>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <a href="{{ route('admin.users.edit', ['user' => $user->id]) }}" class="btn btn-sm btn-primary">Edit</a>

                                        <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteModal{{ $user->id }}">Delete</button>

                                        <!-- Delete Modal -->
                                        <div class="modal fade" id="deleteModal{{ $user->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel{{ $user->id }}" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header" style="text-align: center;">
                                                        <h5 class="modal-title" id="deleteModalLabel{{ $user->id }}">Delete Confirmation</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p style="text-align: center;">Are you sure you want to delete the user "{{ $user->name }}"?</p>
                                                    </div>
                                                    <div class="modal-footer" style="display: flex; justify-content: space-between; align-items: center;">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                        <form action="{{ route('admin.users.destroy', ['user' => $user->id]) }}" method="POST">
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

                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
