<form method="post" action="{{ route('profile.update') }}">
    @csrf
    @method('patch')
    <div class="form-group">
        <label for="name">{{ __('Name') }}</label>
        <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" class="form-control">
        @error('name')
            <p>{{ $message }}</p>
        @enderror
    </div>
    <div class="form-group">
        <label for="email">{{ __('Email') }}</label>
        <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="username" class="form-control">
        @error('email')
            <p>{{ $message }}</p>
        @enderror
    </div>
    <div class="form-group">
        <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
        @if (session('status') === 'profile-updated')
            <p>{{ __('Saved.') }}</p>
        @endif
    </div>
</form>
