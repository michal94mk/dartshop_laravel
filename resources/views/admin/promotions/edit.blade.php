@extends('layouts.admin')

@section('title', 'Edycja promocji')

@section('content')
<div class="bg-white shadow rounded-lg overflow-hidden">
    <div class="p-6 bg-white border-b border-gray-200">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Edycja promocji</h2>
            <a href="{{ route('admin.promotions.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded shadow-sm">
                <i class="fas fa-arrow-left mr-1"></i> Powrót do listy
            </a>
        </div>

        @if($errors->any())
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.promotions.update', $promotion->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nazwa promocji</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $promotion->name) }}" 
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                </div>

                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-1">Kod promocyjny</label>
                    <input type="text" name="code" id="code" value="{{ old('code', $promotion->code) }}" 
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                </div>
            </div>

            <div class="mb-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Opis promocji</label>
                <textarea name="description" id="description" rows="3" 
                          class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('description', $promotion->description) }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div>
                    <label for="discount_type" class="block text-sm font-medium text-gray-700 mb-1">Typ zniżki</label>
                    <select name="discount_type" id="discount_type" 
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                        <option value="percentage" {{ old('discount_type', $promotion->discount_type) == 'percentage' ? 'selected' : '' }}>Procentowa (%)</option>
                        <option value="fixed_amount" {{ old('discount_type', $promotion->discount_type) == 'fixed_amount' ? 'selected' : '' }}>Kwotowa (zł)</option>
                    </select>
                </div>

                <div>
                    <label for="discount_value" class="block text-sm font-medium text-gray-700 mb-1">Wartość zniżki</label>
                    <input type="number" name="discount_value" id="discount_value" value="{{ old('discount_value', $promotion->discount_value) }}" step="0.01" min="0" 
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                </div>

                <div>
                    <label for="minimum_order_value" class="block text-sm font-medium text-gray-700 mb-1">Minimalna wartość zamówienia</label>
                    <input type="number" name="minimum_order_value" id="minimum_order_value" value="{{ old('minimum_order_value', $promotion->minimum_order_value) }}" step="0.01" min="0" 
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="starts_at" class="block text-sm font-medium text-gray-700 mb-1">Data rozpoczęcia</label>
                    <input type="datetime-local" name="starts_at" id="starts_at" value="{{ old('starts_at', $promotion->starts_at->format('Y-m-d\TH:i')) }}" 
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                </div>

                <div>
                    <label for="ends_at" class="block text-sm font-medium text-gray-700 mb-1">Data zakończenia (opcjonalnie)</label>
                    <input type="datetime-local" name="ends_at" id="ends_at" value="{{ old('ends_at', $promotion->ends_at ? $promotion->ends_at->format('Y-m-d\TH:i') : '') }}" 
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="usage_limit" class="block text-sm font-medium text-gray-700 mb-1">Limit użyć (opcjonalnie)</label>
                    <input type="number" name="usage_limit" id="usage_limit" value="{{ old('usage_limit', $promotion->usage_limit) }}" min="1" 
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>

                <div class="flex items-center h-full pt-5">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $promotion->is_active) ? 'checked' : '' }} 
                           class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <label for="is_active" class="ml-2 block text-sm font-medium text-gray-700">Promocja aktywna</label>
                </div>
            </div>

            <div class="flex justify-between">
                <div class="text-sm text-gray-600">
                    <p>Użyto: {{ $promotion->used_count }} {{ $promotion->used_count == 1 ? 'raz' : 'razy' }}</p>
                    <p>Utworzono: {{ $promotion->created_at->format('d.m.Y H:i') }}</p>
                    <p>Ostatnia aktualizacja: {{ $promotion->updated_at->format('d.m.Y H:i') }}</p>
                </div>
                
                <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded shadow-sm">
                    Aktualizuj promocję
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 