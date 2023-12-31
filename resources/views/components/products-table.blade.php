<div class="mb-3">
    <a href="{{ route('admin.products.create') }}" class="btn btn-success">Add product</a>
</div>

<table class="table table-bordered">
    <thead class="thead-light">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Description</th>
            <th>Price</th>
            <th>Category</th>
            <th>Photo</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($products as $product)
            <tr>
                <td>{{ $product->id }}</td>
                <td>{{ $product->name }}</td>
                <td title="{{ $product->description }}">{{ Str::limit($product->description, 30) }}</td>
                <td>{{ $product->price }}</td>
                <td>{{ $product->category->name }}</td>
                <td>
                    @if ($product->image)
                        <img src="{{ asset($product->image) }}" alt="{{ $product->name }} Thumbnail" class="thumbnail" style="max-width: 30px; max-height: 30px; border: 1px solid">
                    @else
                        <span class="text-muted">No photo</span>
                    @endif

                </td>
                <td>
                    <a href="{{ route('admin.products.edit', ['product' => $product->id]) }}" class="btn btn-primary">Edit</a>
                    <a href="{{ route('admin.products.show', ['product' => $product->id]) }}" class="btn btn-warning">Show</a>

                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $product->id }}">
                        Delete
                    </button>

                    <div class="modal fade" id="deleteModal{{ $product->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="deleteModalLabel">Delete confirmation</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    Are you sure you want to delete the product "{{ $product->name }}"?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <form action="{{ route('admin.products.destroy', ['product' => $product->id]) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
{{ $products->links('pagination::bootstrap-4') }}
