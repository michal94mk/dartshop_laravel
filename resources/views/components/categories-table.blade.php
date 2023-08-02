<div class="mb-3">
    <a href="{{ route('categories.create') }}" class="btn btn-success">Dodaj kategorię</a>
</div>

<table class="table table-bordered">
    <thead class="thead-light">
        <tr>
            <th>ID</th>
            <th>Nazwa</th>
            <th>Akcje</th>
        </tr>
    </thead>
    <tbody>
        @foreach($categories as $category)
            <tr>
                <td>{{ $category->id }}</td>
                <td>{{ $category->name }}</td>
                <td>
                    <a href="{{ route('categories.edit', ['category' => $category->id]) }}" class="btn btn-primary">Edytuj</a>
                    <form action="{{ route('categories.destroy', ['category' => $category->id]) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Usuń</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
