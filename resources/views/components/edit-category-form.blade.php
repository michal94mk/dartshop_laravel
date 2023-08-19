<form action="{{ route('admin.categories.update', ['category' => $category->id]) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="form-group">
        <label for="category_name">Name:</label><br>
        <input type="text" name="name" id="category_name" class="form-control" value="{{ $category->name }}" required><br>
    </div>
    <button type="submit" class="btn btn-primary">Save changes</button>
</form>
