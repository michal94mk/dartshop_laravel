@props(['promotion'])

<div class="relative bg-white border border-gray-200 rounded-lg shadow-sm flex flex-col overflow-hidden hover:shadow-lg transition-shadow duration-200">
    <!-- Promotion header with color based on discount type -->
    <div class="{{ $promotion->discount_type == 'percentage' ? 'bg-indigo-600' : 'bg-emerald-600' }} text-white p-4">
        <h3 class="text-lg font-bold">{{ $promotion->name }}</h3>
        <p class="text-sm flex items-center mt-1">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-white {{ $promotion->discount_type == 'percentage' ? 'text-indigo-800' : 'text-emerald-800' }}">
                Kod: <span class="font-mono font-bold ml-1">{{ $promotion->code }}</span>
            </span>
        </p>
    </div>
    
    <!-- Status badge -->
    <div class="absolute top-0 right-0 mt-3 mr-3">
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $promotion->isValid() ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
            {{ $promotion->status_label }}
        </span>
    </div>
    
    <!-- Promotion details -->
    <div class="flex-1 p-4 space-y-3 flex flex-col">
        @if($promotion->description)
            <p class="text-sm text-gray-700">{{ $promotion->description }}</p>
            <hr class="border-gray-200">
        @endif
        
        <div class="flex justify-between items-center">
            <div class="text-xl font-bold {{ $promotion->discount_type == 'percentage' ? 'text-indigo-600' : 'text-emerald-600' }}">
                @if($promotion->discount_type == 'percentage')
                    {{ $promotion->discount_value }}%
                @else
                    {{ number_format($promotion->discount_value, 2) }} zł
                @endif
            </div>
            
            <div class="flex flex-col text-right">
                @if($promotion->minimum_order_value)
                    <span class="text-xs text-gray-500">Min. zamówienie:</span>
                    <span class="text-sm font-medium">{{ number_format($promotion->minimum_order_value, 2) }} zł</span>
                @endif
            </div>
        </div>
        
        <div class="space-y-2 text-sm text-gray-500">
            @if($promotion->ends_at)
                <p class="flex items-center justify-between">
                    <span>Ważny do:</span>
                    <span class="font-medium text-gray-700">{{ $promotion->ends_at->format('d.m.Y') }}</span>
                </p>
            @endif
            
            @if($promotion->usage_limit)
                <p class="flex items-center justify-between">
                    <span>Pozostało użyć:</span>
                    <span class="font-medium text-gray-700">{{ max(0, $promotion->usage_limit - $promotion->used_count) }}</span>
                </p>
            @endif
        </div>
        
        <div class="mt-auto pt-3">
            <a href="{{ route('cart.view') }}" class="w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white {{ $promotion->discount_type == 'percentage' ? 'bg-indigo-600 hover:bg-indigo-700' : 'bg-emerald-600 hover:bg-emerald-700' }} focus:outline-none focus:ring-2 focus:ring-offset-2 {{ $promotion->discount_type == 'percentage' ? 'focus:ring-indigo-500' : 'focus:ring-emerald-500' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                Użyj w koszyku
            </a>
        </div>
    </div>
</div> 