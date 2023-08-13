<form action="{{ route('admin.products.store') }}" method="POST">
    @csrf
    <div class="form-group">
        <label for="product_name">Name:</label><br>
        <input type="text" name="name" id="product_name" class="form-control" required><br>
    </div>
    <div class="form-group">
        <label for="product_price">Price:</label><br>
        <input type="number" name="price" id="product_price" class="form-control" required><br>
    </div>
    <div class="form-group">
        <label for="product_description">Description:</label><br>
        <textarea name="description" id="product_description" class="form-control" rows="4" required></textarea><br>
    </div>
    <div class="form-group">
        <label for="category_id">Category:</label><br>
        <select name="category_id" id="category_id" class="form-control" required>
            <option value="" disabled selected>Select a category</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
        </select><br>
    </div>
    <button type="submit" class="btn btn-primary">Save</button>
</form>
