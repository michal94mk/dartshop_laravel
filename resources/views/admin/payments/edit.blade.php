@extends('layouts.admin')

@section('title', 'Edycja płatności')

@section('content')
<div class="bg-white shadow overflow-hidden sm:rounded-lg">
    <div class="px-4 py-5 sm:px-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900">Edycja płatności</h3>
        <p class="mt-1 max-w-2xl text-sm text-gray-500">
            ID płatności: {{ $payment->id }} | 
            @if($payment->order)
                Zamówienie: <a href="{{ route('admin.orders.show', $payment->order->id) }}" class="text-indigo-600 hover:text-indigo-900">{{ $payment->order->order_number }}</a>
            @else
                Brak przypisanego zamówienia
            @endif
        </p>
    </div>

    <form action="{{ route('admin.payments.update', $payment->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="border-t border-gray-200 px-4 py-5 sm:p-6">
            <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                <div class="sm:col-span-3">
                    <label for="transaction_id" class="block text-sm font-medium text-gray-700">ID transakcji</label>
                    <div class="mt-1">
                        <p class="block w-full py-2 text-gray-500 sm:text-sm">
                            {{ $payment->transaction_id ?? 'Brak' }}
                        </p>
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <label for="amount" class="block text-sm font-medium text-gray-700">Kwota</label>
                    <div class="mt-1">
                        <p class="block w-full py-2 text-gray-500 sm:text-sm">
                            {{ number_format($payment->amount, 2) }} zł
                        </p>
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <label for="payment_method" class="block text-sm font-medium text-gray-700">Metoda płatności</label>
                    <div class="mt-1">
                        <p class="block w-full py-2 text-gray-500 sm:text-sm">
                            {{ ucfirst($payment->payment_method) }}
                        </p>
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <label for="created_at" class="block text-sm font-medium text-gray-700">Data utworzenia</label>
                    <div class="mt-1">
                        <p class="block w-full py-2 text-gray-500 sm:text-sm">
                            {{ $payment->created_at->format('d.m.Y H:i:s') }}
                        </p>
                    </div>
                </div>

                <div class="sm:col-span-6">
                    <label for="status" class="block text-sm font-medium text-gray-700">Status płatności</label>
                    <div class="mt-1">
                        <select id="status" name="status" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            @foreach($statuses as $status)
                                <option value="{{ $status->value }}" {{ $payment->status == $status ? 'selected' : '' }}>
                                    {{ $status->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('status')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-6">
                    <label for="notes" class="block text-sm font-medium text-gray-700">Notatki</label>
                    <div class="mt-1">
                        <textarea id="notes" name="notes" rows="3" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('notes', $payment->notes) }}</textarea>
                    </div>
                    @error('notes')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="px-4 py-3 bg-gray-50 text-right sm:px-6 border-t border-gray-200">
            <a href="{{ route('admin.payments.index') }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-2">
                Anuluj
            </a>
            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Zapisz zmiany
            </button>
        </div>
    </form>
</div>
@endsection 