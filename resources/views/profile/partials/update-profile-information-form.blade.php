<form method="post" action="{{ route('profile.update') }}" class="space-y-6">
    @csrf
    @method('patch')
    
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700">{{ __('Imię i nazwisko') }}</label>
        <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" 
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
        @error('name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
    
    <div>
        <label for="email" class="block text-sm font-medium text-gray-700">{{ __('Email') }}</label>
        <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="username" 
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
        @error('email')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
    
    <div class="flex items-center">
        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            {{ __('Zapisz') }}
        </button>
        
        @if (session('status') === 'profile-updated')
            <p class="ml-3 text-sm text-green-600">{{ __('Zapisano.') }}</p>
        @endif
    </div>
</form>
