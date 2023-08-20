@extends('layouts.app')

@section('content')
<div class="mb-3">
    <a href="{{ route('admin.users.index') }}" class="btn btn-info">Back</a>
</div>
<br>
<form action="{{ route('admin.users.update', ['user' => $user->id]) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="form-group">
        <label for="name">Name:</label><br>
        <input type="text" name="name" id="name" class="form-control" value="{{ $user->name }}" required><br>
    </div>
    <button type="submit" class="btn btn-primary">Save changes</button>
</form>

@endsection
