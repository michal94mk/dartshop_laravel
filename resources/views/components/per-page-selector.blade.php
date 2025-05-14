<div class="flex items-center space-x-2 text-sm text-gray-600">
    <span>Wyniki na stronę:</span>
    <select id="per-page-selector" class="form-select text-sm border-gray-300 rounded-md focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" 
            onchange="window.location.href = this.value">
        @foreach([15, 25, 50, 100] as $value)
            <option value="{{ request()->fullUrlWithQuery(['per_page' => $value, 'page' => 1]) }}" 
                    {{ request()->input('per_page', 15) == $value ? 'selected' : '' }}>
                {{ $value }}
            </option>
        @endforeach
    </select>
</div> 