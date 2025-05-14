<div class="mb-4">
    <form action="{{ $action }}" method="GET" class="flex items-center">
        <div class="relative flex-grow">
            <input 
                type="text" 
                name="search" 
                value="{{ request('search') }}" 
                placeholder="{{ $placeholder ?? 'Wyszukaj...' }}"
                class="w-full px-4 py-2 border border-gray-300 rounded-l focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
            >
            @if(request()->has('per_page'))
                <input type="hidden" name="per_page" value="{{ request('per_page') }}">
            @endif
        </div>
        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-r">
            <i class="fas fa-search"></i>
        </button>
        @if(request()->has('search'))
            <a href="{{ url()->current() }}{{ request('per_page') ? '?per_page='.request('per_page') : '' }}" class="ml-2 text-gray-600 hover:text-gray-900">
                <i class="fas fa-times-circle"></i> Wyczyść
            </a>
        @endif
    </form>
</div> 