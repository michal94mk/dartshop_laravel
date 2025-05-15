@extends('layouts.admin')

@section('title', 'Podgląd strony O nas')

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">{{ $aboutPage->title }}</h2>
                <div>
                    <a href="{{ route('admin.about-pages.edit', $aboutPage) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mr-2">
                        Edytuj
                    </a>
                    <a href="{{ route('admin.about-pages.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded">
                        Powrót
                    </a>
                </div>
            </div>

            <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-gray-50 p-4 rounded border border-gray-200">
                    <h3 class="font-semibold text-gray-700 mb-2">Informacje</h3>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="text-sm text-gray-500">ID:</div>
                        <div class="text-sm">{{ $aboutPage->id }}</div>
                        
                        <div class="text-sm text-gray-500">Kolejność:</div>
                        <div class="text-sm">{{ $aboutPage->display_order }}</div>
                        
                        <div class="text-sm text-gray-500">Status:</div>
                        <div class="text-sm">
                            @if($aboutPage->is_active)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Aktywna
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    Nieaktywna
                                </span>
                            @endif
                        </div>
                        
                        <div class="text-sm text-gray-500">Utworzono:</div>
                        <div class="text-sm">{{ $aboutPage->created_at->format('d.m.Y H:i') }}</div>
                        
                        <div class="text-sm text-gray-500">Aktualizacja:</div>
                        <div class="text-sm">{{ $aboutPage->updated_at->format('d.m.Y H:i') }}</div>
                    </div>
                </div>
                
                <div class="md:col-span-2 bg-gray-50 p-4 rounded border border-gray-200">
                    <h3 class="font-semibold text-gray-700 mb-2">Meta dane (SEO)</h3>
                    <div class="mb-2">
                        <div class="text-sm text-gray-500">Meta tytuł:</div>
                        <div class="text-sm">{{ $aboutPage->meta_title ?: 'Brak' }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Meta opis:</div>
                        <div class="text-sm">{{ $aboutPage->meta_description ?: 'Brak' }}</div>
                    </div>
                </div>
            </div>
            
            <div class="mb-4">
                <h3 class="font-semibold text-gray-700 mb-2">Treść</h3>
                <div class="bg-gray-50 p-4 rounded border border-gray-200 prose max-w-none">
                    {!! $aboutPage->content !!}
                </div>
            </div>
            
            <div class="flex justify-between items-center mt-6">
                <form action="{{ route('admin.about-pages.destroy', $aboutPage) }}" method="POST" onsubmit="return confirm('Czy na pewno chcesz usunąć tę stronę?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                        Usuń stronę
                    </button>
                </form>
                
                <a href="{{ route('admin.about-pages.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded">
                    Powrót do listy
                </a>
            </div>
        </div>
    </div>
@endsection 