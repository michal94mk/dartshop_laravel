@extends('layouts.admin')

@section('title', 'Szczegóły płatności')

@section('content')
<div class="bg-white shadow overflow-hidden sm:rounded-lg">
    <div class="px-4 py-5 sm:px-6 flex justify-between">
        <div>
            <h3 class="text-lg leading-6 font-medium text-gray-900">Szczegóły płatności</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">ID płatności: {{ $payment->id }}</p>
        </div>
        <div>
            <a href="{{ route('admin.payments.edit', $payment->id) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Edytuj płatność
            </a>
        </div>
    </div>

    <div class="border-t border-gray-200">
        <dl>
            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">ID transakcji</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $payment->transaction_id ?? 'Brak' }}</dd>
            </div>
            <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Zamówienie</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    @if($payment->order)
                        <a href="{{ route('admin.orders.show', $payment->order->id) }}" class="text-indigo-600 hover:text-indigo-900">
                            {{ $payment->order->order_number }}
                        </a>
                    @else
                        <span class="text-gray-400">Brak przypisanego zamówienia</span>
                    @endif
                </dd>
            </div>
            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Metoda płatności</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ ucfirst($payment->payment_method) }}</dd>
            </div>
            <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Kwota</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ number_format($payment->amount, 2) }} zł</dd>
            </div>
            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Status</dt>
                <dd class="mt-1 text-sm sm:mt-0 sm:col-span-2">
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
                </dd>
            </div>
            <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Data utworzenia</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $payment->created_at->format('d.m.Y H:i:s') }}</dd>
            </div>
            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Data aktualizacji</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $payment->updated_at->format('d.m.Y H:i:s') }}</dd>
            </div>
            @if($payment->notes)
            <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Notatki</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $payment->notes }}</dd>
            </div>
            @endif
        </dl>
    </div>
    
    @if($payment->order)
    <div class="px-4 py-5 sm:px-6 border-t border-gray-200">
        <h4 class="text-lg font-medium text-gray-900">Informacje o zamówieniu</h4>
        
        <div class="mt-4 border rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Produkt
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Cena jedn.
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Ilość
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Wartość
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($payment->order->items as $item)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $item->product_name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ number_format($item->price, 2) }} zł
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $item->quantity }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ number_format($item->price * $item->quantity, 2) }} zł
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-sm font-medium text-gray-900 text-right">
                            Wartość produktów:
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">
                            {{ number_format($payment->order->subtotal, 2) }} zł
                        </td>
                    </tr>
                    @if($payment->order->discount > 0)
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-sm font-medium text-gray-900 text-right">
                            Rabat:
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">
                            -{{ number_format($payment->order->discount, 2) }} zł
                        </td>
                    </tr>
                    @endif
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-sm font-medium text-gray-900 text-right">
                            Koszt dostawy:
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">
                            {{ number_format($payment->order->shipping_cost, 2) }} zł
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-sm font-bold text-gray-900 text-right">
                            Razem:
                        </td>
                        <td class="px-6 py-4 text-sm font-bold text-gray-900">
                            {{ number_format($payment->order->total, 2) }} zł
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    @endif
    
    <div class="px-4 py-3 bg-gray-50 text-right sm:px-6 border-t border-gray-200">
        <a href="{{ route('admin.payments.index') }}" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Wróć do listy
        </a>
    </div>
</div>
@endsection 