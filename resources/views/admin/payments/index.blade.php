@extends('layouts.admin')

@section('title', 'Płatności')

@section('content')
<div class="bg-white shadow overflow-hidden sm:rounded-lg">
    <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
        <div>
            <h3 class="text-lg leading-6 font-medium text-gray-900">Zarządzanie płatnościami</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">Lista wszystkich płatności w systemie.</p>
        </div>
    </div>
    
    <div class="px-4 py-3 sm:px-6 bg-gray-50 border-t border-b border-gray-200">
        <form action="{{ route('admin.payments.index') }}" method="GET" class="flex flex-col md:flex-row space-y-3 md:space-y-0 md:space-x-4">
            <div class="flex-grow">
                <label for="search" class="sr-only">Szukaj</label>
                <div class="relative rounded-md shadow-sm">
                    <input type="text" name="search" id="search" value="{{ request('search') }}" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pr-10 sm:text-sm border-gray-300 rounded-md" placeholder="Wyszukaj po numerze zamówienia, nazwisku lub ID transakcji...">
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
                        ID Transakcji
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Nr zamówienia
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Metoda
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Kwota
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Data
                    </th>
                    <th scope="col" class="relative px-6 py-3">
                        <span class="sr-only">Akcje</span>
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($payments as $payment)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $payment->transaction_id ?? 'Brak' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($payment->order)
                                <a href="{{ route('admin.orders.show', $payment->order->id) }}" class="text-indigo-600 hover:text-indigo-900">
                                    {{ $payment->order->order_number }}
                                </a>
                            @else
                                <span class="text-gray-400">Brak zamówienia</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ ucfirst($payment->payment_method) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ number_format($payment->amount, 2) }} zł
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($payment->status == \App\Enums\PaymentStatus::COMPLETED)
                                    bg-green-100 text-green-800
                                @elseif($payment->status == \App\Enums\PaymentStatus::FAILED)
                                    bg-red-100 text-red-800
                                @elseif($payment->status == \App\Enums\PaymentStatus::PROCESSING)
                                    bg-yellow-100 text-yellow-800
                                @elseif($payment->status == \App\Enums\PaymentStatus::PENDING)
                                    bg-gray-100 text-gray-800
                                @else
                                    bg-indigo-100 text-indigo-800
                                @endif
                            ">
                                {{ $payment->status->label() }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $payment->created_at->format('d.m.Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('admin.payments.show', $payment->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Szczegóły</a>
                            <a href="{{ route('admin.payments.edit', $payment->id) }}" class="text-indigo-600 hover:text-indigo-900">Edytuj</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    @if($payments->isEmpty())
        <div class="px-4 py-5 sm:p-6 text-center">
            <p class="text-sm text-gray-500">Brak płatności spełniających kryteria wyszukiwania.</p>
        </div>
    @endif
    
    <div class="px-4 py-3 bg-gray-50 border-t border-gray-200 sm:px-6">
        {{ $payments->withQueryString()->links() }}
    </div>
</div>
@endsection 