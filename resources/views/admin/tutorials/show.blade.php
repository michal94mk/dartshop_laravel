@extends('layouts.admin')

@section('title', 'Podgląd poradnika')

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">{{ $tutorial->title }}</h2>
                <div>
                    <a href="{{ route('admin.tutorials.edit', $tutorial) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mr-2">
                        Edytuj
                    </a>
                    <a href="{{ route('admin.tutorials.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded">
                        Powrót
                    </a>
                </div>
            </div>

            <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-gray-50 p-4 rounded border border-gray-200">
                    <h3 class="font-semibold text-gray-700 mb-2">Informacje</h3>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="text-sm text-gray-500">ID:</div>
                        <div class="text-sm">{{ $tutorial->id }}</div>
                        
                        <div class="text-sm text-gray-500">Slug:</div>
                        <div class="text-sm">{{ $tutorial->slug }}</div>
                        
                        <div class="text-sm text-gray-500">Kategoria:</div>
                        <div class="text-sm">{{ $tutorial->category ?: 'Brak' }}</div>
                        
                        <div class="text-sm text-gray-500">Poziom:</div>
                        <div class="text-sm">
                            @switch($tutorial->difficulty)
                                @case('beginner')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Początkujący
                                    </span>
                                    @break
                                @case('intermediate')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        Średniozaawansowany
                                    </span>
                                    @break
                                @case('advanced')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Zaawansowany
                                    </span>
                                    @break
                                @default
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        Nieokreślony
                                    </span>
                            @endswitch
                        </div>
                        
                        <div class="text-sm text-gray-500">Status:</div>
                        <div class="text-sm">
                            @if($tutorial->is_published)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Opublikowany
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    Szkic
                                </span>
                            @endif
                        </div>
                        
                        <div class="text-sm text-gray-500">Data publikacji:</div>
                        <div class="text-sm">{{ $tutorial->published_at ? $tutorial->published_at->format('d.m.Y H:i') : 'Nieopublikowany' }}</div>
                        
                        <div class="text-sm text-gray-500">Data utworzenia:</div>
                        <div class="text-sm">{{ $tutorial->created_at->format('d.m.Y H:i') }}</div>
                        
                        <div class="text-sm text-gray-500">Ostatnia aktualizacja:</div>
                        <div class="text-sm">{{ $tutorial->updated_at->format('d.m.Y H:i') }}</div>
                    </div>
                </div>
                
                <div class="md:col-span-2 bg-gray-50 p-4 rounded border border-gray-200">
                    <h3 class="font-semibold text-gray-700 mb-2">Meta dane (SEO)</h3>
                    <div class="mb-2">
                        <div class="text-sm text-gray-500">Meta tytuł:</div>
                        <div class="text-sm">{{ $tutorial->meta_title ?: 'Brak' }}</div>
                    </div>
                    <div class="mb-2">
                        <div class="text-sm text-gray-500">Meta opis:</div>
                        <div class="text-sm">{{ $tutorial->meta_description ?: 'Brak' }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Streszczenie:</div>
                        <div class="text-sm">{{ $tutorial->excerpt ?: 'Brak' }}</div>
                    </div>
                </div>
            </div>
            
            <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                @if($tutorial->featured_image)
                    <div class="bg-gray-50 p-4 rounded border border-gray-200">
                        <h3 class="font-semibold text-gray-700 mb-2">Zdjęcie główne</h3>
                        <img src="{{ asset('storage/images/' . $tutorial->featured_image) }}" alt="{{ $tutorial->title }}" class="max-h-64 object-contain mx-auto">
                    </div>
                @endif
                
                @if($tutorial->thumbnail_image)
                    <div class="bg-gray-50 p-4 rounded border border-gray-200">
                        <h3 class="font-semibold text-gray-700 mb-2">Miniatura</h3>
                        <img src="{{ asset('storage/images/' . $tutorial->thumbnail_image) }}" alt="{{ $tutorial->title }}" class="max-h-48 object-contain mx-auto">
                    </div>
                @endif
            </div>
            
            <div class="mb-4">
                <h3 class="font-semibold text-gray-700 mb-2">Treść</h3>
                <div class="bg-gray-50 p-4 rounded border border-gray-200 prose max-w-none">
                    {!! $tutorial->content !!}
                </div>
            </div>
            
            <div class="flex justify-between items-center mt-6">
                <form action="{{ route('admin.tutorials.destroy', $tutorial) }}" method="POST" onsubmit="return confirm('Czy na pewno chcesz usunąć ten poradnik?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                        Usuń poradnik
                    </button>
                </form>
                
                <a href="{{ route('admin.tutorials.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded">
                    Powrót do listy
                </a>
            </div>
        </div>
    </div>
@endsection 