@php
    $activePromotions = App\Models\Promotion::where('is_active', true)
        ->where('starts_at', '<=', \Carbon\Carbon::now())
        ->where(function ($query) {
            $query->whereNull('ends_at')
                ->orWhere('ends_at', '>=', \Carbon\Carbon::now());
        })
        ->get();
@endphp

@if($activePromotions->count() > 0)
<div class="bg-indigo-600">
    <div class="max-w-7xl mx-auto py-2 px-3 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between flex-wrap">
            <div class="w-0 flex-1 flex items-center">
                <span class="flex p-1 rounded-lg bg-indigo-800">
                    <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                    </svg>
                </span>
                <p class="ml-3 font-medium text-white truncate">
                    <span class="md:hidden">Aktywne promocje!</span>
                    <span class="hidden md:inline">
                        @if($activePromotions->count() > 1)
                            Aktywne promocje! Dostępne kody: 
                            @foreach($activePromotions->take(2) as $promotion)
                                <span class="font-bold">{{ $promotion->code }}</span>{{ !$loop->last ? ', ' : '' }}
                            @endforeach
                            @if($activePromotions->count() > 2)
                                i inne
                            @endif
                        @else
                            Kod promocyjny: <span class="font-bold">{{ $activePromotions->first()->code }}</span> - 
                            @if($activePromotions->first()->discount_type == 'percentage')
                                {{ $activePromotions->first()->discount_value }}% zniżki!
                            @else
                                {{ number_format($activePromotions->first()->discount_value, 2) }} zł zniżki!
                            @endif
                        @endif
                    </span>
                </p>
            </div>
            <div class="order-3 mt-2 flex-shrink-0 w-full sm:order-2 sm:mt-0 sm:w-auto">
                <a href="{{ route('frontend.promotions') }}" class="flex items-center justify-center px-4 py-1 border border-transparent rounded-md shadow-sm text-sm font-medium text-indigo-600 bg-white hover:bg-indigo-50">
                    Zobacz promocje
                </a>
            </div>
            <div class="order-2 flex-shrink-0 sm:order-3 sm:ml-3">
                <button type="button" class="flex p-1 rounded-md hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-white" id="close-promotion-bar">
                    <span class="sr-only">Zamknij</span>
                    <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const closeButton = document.getElementById('close-promotion-bar');
        const promotionBar = closeButton.closest('.bg-indigo-600');
        
        closeButton.addEventListener('click', function() {
            promotionBar.style.display = 'none';
            
            // Set a cookie to remember that user closed the bar
            const d = new Date();
            d.setTime(d.getTime() + (24*60*60*1000)); // Valid for 24 hours
            document.cookie = "promotion_bar_closed=1;path=/;expires=" + d.toUTCString();
        });
        
        // Check if user has closed the bar before
        if (document.cookie.indexOf('promotion_bar_closed=1') !== -1) {
            promotionBar.style.display = 'none';
        }
    });
</script>
@endif 