@extends('layouts.admin')

@section('title', 'Zarządzanie promocjami')

@section('content')
<div class="bg-white shadow rounded-lg overflow-hidden">
    <div class="p-6 bg-white border-b border-gray-200">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Zarządzanie promocjami</h2>
            <a href="{{ route('admin.promotions.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded shadow-sm">
                <i class="fas fa-plus mr-1"></i> Dodaj nową promocję
            </a>
        </div>
        
        @include('components.admin-search-form', [
            'action' => route('admin.promotions.index'),
            'placeholder' => 'Wyszukaj promocje...'
        ])
        
        @if(session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif
    
        @if($promotions->count() > 0)
            <div class="flex justify-between items-center mb-4">
                <div>
                    <p class="text-sm text-gray-600">
                        Wyświetlanie {{ $promotions->firstItem() }}-{{ $promotions->lastItem() }} z {{ $promotions->total() }} promocji
                    </p>
                </div>
                <div>
                    @include('components.per-page-selector')
                </div>
            </div>
            
            <div class="overflow-hidden overflow-x-auto rounded-lg shadow">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nazwa</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kod</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Zniżka</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data rozpoczęcia</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data zakończenia</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Akcje</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($promotions as $promotion)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">{{ $promotion->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $promotion->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $promotion->code }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($promotion->discount_type == 'percentage')
                                        {{ $promotion->discount_value }}%
                                    @else
                                        {{ number_format($promotion->discount_value, 2) }} zł
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $promotion->starts_at->format('d.m.Y H:i') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ $promotion->ends_at ? $promotion->ends_at->format('d.m.Y H:i') : 'Bezterminowo' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $promotion->status_color }}-100 text-{{ $promotion->status_color }}-800">
                                        {{ $promotion->status_label }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('admin.promotions.edit', $promotion->id) }}" class="text-indigo-600 hover:text-indigo-900">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form class="inline" action="{{ route('admin.promotions.destroy', $promotion->id) }}" method="POST" onsubmit="return confirm('Czy na pewno chcesz usunąć tę promocję?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $promotions->appends(request()->except('page'))->links() }}
            </div>
        @else
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mt-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-yellow-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            Brak promocji w bazie danych. Dodaj pierwszą promocję.
                        </p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection 