<form action="{{ route('admin.users.update', ['user' => $user->id]) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="form-group">
        <label for="name">Name:</label><br>
        <input type="text" name="name" id="name" class="form-control" value="{{ $user->name }}" required><br>
    </div>
    <button type="submit" class="btn btn-primary">Save changes</button>
</form>
