<section>
    <header>
        <p class="mt-1 text-sm text-gray-600">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div class="form-group">
            <label for="current_password">{{ __('Current Password') }}</label>
            <input id="current_password" name="current_password" type="password" class="form-control">
            @error('current_password')
                <p>{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="password">{{ __('New Password') }}</label>
            <input id="password" name="password" type="password" class="form-control">
            @error('password')
                <p>{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="password_confirmation">{{ __('Confirm Password') }}</label>
            <input id="password_confirmation" name="password_confirmation" type="password" class="form-control">
            @error('password_confirmation')
                <p>{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
        </div>
    </form>
</section>
