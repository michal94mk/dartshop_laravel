<form action="{{ route('products.update', ['product' => $product->id]) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="form-group">
        <label for="product_name">Name:</label><br>
        <input type="text" name="name" id="product_name" class="form-control" value="{{ $product->name }}" required><br>
    </div>
    <div class="form-group">
        <label for="product_description">Description:</label><br>
        <textarea name="description" id="product_description" class="form-control" rows="4" required>{{ $product->description }}</textarea><br>
    </div>
    <div class="form-group">
        <label for="product_price">Price:</label><br>
        <input type="number" name="price" id="product_price" class="form-control" value="{{ $product->price }}" required><br>
    </div>
    <div class="form-group">
        <label for="category_id">Category:</label><br>
        <select name="category_id" id="category_id" class="form-control" required>
            <option value="" disabled>Select a category</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
            @endforeach
        </select><br>
    </div>
    <button type="submit" class="btn btn-primary">Save Changes</button>
</form>
