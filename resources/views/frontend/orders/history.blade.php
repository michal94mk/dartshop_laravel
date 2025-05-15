@extends('layouts.app')

@section('title', 'Historia zamówień')

@section('content')
<div class="bg-white">
    <div class="max-w-7xl mx-auto px-4 py-16 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-extrabold tracking-tight text-gray-900">Historia zamówień</h1>
        
        <div class="mt-8">
            @if(count($orders) > 0)
                <div class="border rounded-lg shadow overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nr zamówienia
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Data
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Wartość
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Płatność
                                </th>
                                <th scope="col" class="relative px-6 py-3">
                                    <span class="sr-only">Szczegóły</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($orders as $order)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $order->order_number }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $order->created_at->format('d.m.Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if($order->status == \App\Enums\OrderStatus::COMPLETED)
                                                bg-green-100 text-green-800
                                            @elseif($order->status == \App\Enums\OrderStatus::CANCELLED)
                                                bg-red-100 text-red-800
                                            @elseif($order->status == \App\Enums\OrderStatus::DELIVERED)
                                                bg-blue-100 text-blue-800
                                            @elseif($order->status == \App\Enums\OrderStatus::PROCESSING)
                                                bg-yellow-100 text-yellow-800
                                            @elseif($order->status == \App\Enums\OrderStatus::PENDING)
                                                bg-gray-100 text-gray-800
                                            @else
                                                bg-indigo-100 text-indigo-800
                                            @endif
                                        ">
                                            {{ $order->status->label() }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ number_format($order->total, 2) }} zł
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if($order->payment)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if($order->payment->status == \App\Enums\PaymentStatus::COMPLETED)
                                                    bg-green-100 text-green-800
                                                @elseif($order->payment->status == \App\Enums\PaymentStatus::FAILED)
                                                    bg-red-100 text-red-800
                                                @elseif($order->payment->status == \App\Enums\PaymentStatus::PROCESSING)
                                                    bg-yellow-100 text-yellow-800
                                                @elseif($order->payment->status == \App\Enums\PaymentStatus::PENDING)
                                                    bg-gray-100 text-gray-800
                                                @else
                                                    bg-indigo-100 text-indigo-800
                                                @endif
                                            ">
                                                {{ $order->payment->status->label() }}
                                            </span>
                                        @else
                                            <span>-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('order.show', $order->id) }}" class="text-indigo-600 hover:text-indigo-900">Szczegóły</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-6">
                    {{ $orders->links() }}
                </div>
            @else
                <div class="bg-white p-8 rounded-lg border shadow-sm text-center">
                    <div class="text-gray-500 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">Brak zamówień</h3>
                    <p class="mt-1 text-sm text-gray-500">Nie znaleziono żadnych zamówień w Twojej historii.</p>
                    <div class="mt-6">
                        <a href="{{ route('frontend.categories.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Przejdź do sklepu
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 