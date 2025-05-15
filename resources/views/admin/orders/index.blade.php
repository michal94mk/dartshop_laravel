@extends('layouts.admin')

@section('title', 'Zamówienia')

@section('content')
<div class="bg-white shadow overflow-hidden sm:rounded-lg">
    <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
        <div>
            <h3 class="text-lg leading-6 font-medium text-gray-900">Zarządzanie zamówieniami</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">Lista wszystkich zamówień w systemie.</p>
        </div>
    </div>
    
    <div class="px-4 py-3 sm:px-6 bg-gray-50 border-t border-b border-gray-200">
        <form action="{{ route('admin.orders.index') }}" method="GET" class="flex flex-col md:flex-row space-y-3 md:space-y-0 md:space-x-4">
            <div class="flex-grow">
                <label for="search" class="sr-only">Szukaj</label>
                <div class="relative rounded-md shadow-sm">
                    <input type="text" name="search" id="search" value="{{ request('search') }}" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pr-10 sm:text-sm border-gray-300 rounded-md" placeholder="Wyszukaj po numerze zamówienia, nazwisku lub emailu...">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="w-full md:w-auto">
                <label for="status" class="sr-only">Status</label>
                <select id="status" name="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                    <option value="">Wszystkie statusy</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status->value }}" {{ request('status') == $status->value ? 'selected' : '' }}>
                            {{ $status->label() }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Filtruj
                </button>
            </div>
        </form>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Nr zamówienia
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Klient
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Data
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Płatność
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Wartość
                    </th>
                    <th scope="col" class="relative px-6 py-3">
                        <span class="sr-only">Akcje</span>
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($orders as $order)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $order->order_number }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $order->first_name }} {{ $order->last_name }}</div>
                            <div class="text-sm text-gray-500">{{ $order->email }}</div>
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
                        <td class="px-6 py-4 whitespace-nowrap">
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
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    {{ $order->payment_method == 'cash_on_delivery' ? 'Płatność przy odbiorze' : 'Brak płatności' }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ number_format($order->total, 2) }} zł
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Szczegóły</a>
                            <a href="{{ route('admin.orders.edit', $order->id) }}" class="text-indigo-600 hover:text-indigo-900">Edytuj</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    @if($orders->isEmpty())
        <div class="px-4 py-5 sm:p-6 text-center">
            <p class="text-sm text-gray-500">Brak zamówień spełniających kryteria wyszukiwania.</p>
        </div>
    @endif
    
    <div class="px-4 py-3 bg-gray-50 border-t border-gray-200 sm:px-6">
        {{ $orders->withQueryString()->links() }}
    </div>
</div>
@endsection 