@extends('layouts.admin-tailwind')

@section('title', 'Szczegóły produktu: ' . $product->name)

@section('content')
<div class="bg-white shadow rounded-lg overflow-hidden">
    <div class="p-6 bg-white border-b border-gray-200">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Szczegóły produktu: {{ $product->name }}</h2>
            <div class="flex space-x-2">
                <a href="{{ route('admin.products.edit', ['product' => $product->id, 'tailwind' => 1]) }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded shadow-sm">
                    <i class="fas fa-edit mr-1"></i> Edytuj
                </a>
                <a href="{{ route('admin.products.index', ['tailwind' => 1]) }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded shadow-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Powrót
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-2 space-y-6">
                <div class="bg-gray-50 rounded-lg p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-8">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">ID</h3>
                            <p class="mt-1 text-base font-medium text-gray-900">{{ $product->id }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Nazwa</h3>
                            <p class="mt-1 text-base font-medium text-gray-900">{{ $product->name }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Kategoria</h3>
                            <p class="mt-1 text-base font-medium text-gray-900">
                                {{ $product->category->name }}
                            </p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Marka</h3>
                            <p class="mt-1 text-base font-medium text-gray-900">
                                {{ $product->brand->name }}
                            </p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Cena</h3>
                            <p class="mt-1 text-base font-medium text-gray-900">
                                {{ number_format($product->price, 2) }} zł
                            </p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Data utworzenia</h3>
                            <p class="mt-1 text-base font-medium text-gray-900">
                                {{ $product->created_at->format('d.m.Y H:i') }}
                            </p>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Opis</h3>
                    <div class="bg-gray-50 rounded-lg p-6">
                        <p class="text-gray-700">
                            {{ $product->description ?? 'Brak opisu' }}
                        </p>
                    </div>
                </div>
            </div>

            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-3">Zdjęcie</h3>
                <div class="bg-gray-100 rounded-lg overflow-hidden">
                    @if($product->image)
                        <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="w-full h-auto">
                    @else
                        <div class="h-64 flex items-center justify-center">
                            <div class="text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <p class="mt-2 text-sm text-gray-500">Brak zdjęcia</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 