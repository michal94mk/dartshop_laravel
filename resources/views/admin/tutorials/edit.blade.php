@extends('layouts.admin')

@section('title', 'Edytuj poradnik')

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Edytuj poradnik</h2>
                <p class="mt-1 text-sm text-gray-600">Zaktualizuj zawartość poradnika.</p>
            </div>

            <form action="{{ route('admin.tutorials.update', $tutorial) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="mb-4">
                    <label for="title" class="block text-sm font-medium text-gray-700">Tytuł</label>
                    <input type="text" name="title" id="title" value="{{ old('title', $tutorial->title) }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-4">
                    <label for="slug" class="block text-sm font-medium text-gray-700">Slug</label>
                    <input type="text" name="slug" id="slug" value="{{ old('slug', $tutorial->slug) }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    <p class="mt-1 text-xs text-gray-500">Pozostaw puste, aby wygenerować automatycznie z tytułu.</p>
                    @error('slug')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-4">
                    <label for="content" class="block text-sm font-medium text-gray-700">Treść</label>
                    <textarea name="content" id="content" rows="15" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">{{ old('content', $tutorial->content) }}</textarea>
                    @error('content')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-4">
                    <label for="excerpt" class="block text-sm font-medium text-gray-700">Streszczenie</label>
                    <textarea name="excerpt" id="excerpt" rows="3" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">{{ old('excerpt', $tutorial->excerpt) }}</textarea>
                    @error('excerpt')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="mb-4">
                        <label for="featured_image" class="block text-sm font-medium text-gray-700">Zdjęcie główne</label>
                        @if($tutorial->featured_image)
                            <div class="mb-2">
                                <img src="{{ asset('storage/images/' . $tutorial->featured_image) }}" alt="{{ $tutorial->title }}" class="h-32 w-auto object-cover rounded">
                                <p class="mt-1 text-xs text-gray-500">Aktualne zdjęcie. Prześlij nowe, aby zastąpić.</p>
                            </div>
                        @endif
                        <input type="file" name="featured_image" id="featured_image" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300">
                        <p class="mt-1 text-xs text-gray-500">Maksymalny rozmiar: 2MB. Dozwolone formaty: jpg, jpeg, png, gif.</p>
                        @error('featured_image')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="thumbnail_image" class="block text-sm font-medium text-gray-700">Miniatura</label>
                        @if($tutorial->thumbnail_image)
                            <div class="mb-2">
                                <img src="{{ asset('storage/images/' . $tutorial->thumbnail_image) }}" alt="{{ $tutorial->title }}" class="h-24 w-auto object-cover rounded">
                                <p class="mt-1 text-xs text-gray-500">Aktualna miniatura. Prześlij nową, aby zastąpić.</p>
                            </div>
                        @endif
                        <input type="file" name="thumbnail_image" id="thumbnail_image" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300">
                        <p class="mt-1 text-xs text-gray-500">Maksymalny rozmiar: 1MB. Dozwolone formaty: jpg, jpeg, png, gif.</p>
                        @error('thumbnail_image')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="mb-4">
                        <label for="category" class="block text-sm font-medium text-gray-700">Kategoria</label>
                        <input type="text" name="category" id="category" value="{{ old('category', $tutorial->category) }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        @error('category')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="difficulty" class="block text-sm font-medium text-gray-700">Poziom trudności</label>
                        <select name="difficulty" id="difficulty" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            <option value="beginner" {{ old('difficulty', $tutorial->difficulty) == 'beginner' ? 'selected' : '' }}>Początkujący</option>
                            <option value="intermediate" {{ old('difficulty', $tutorial->difficulty) == 'intermediate' ? 'selected' : '' }}>Średniozaawansowany</option>
                            <option value="advanced" {{ old('difficulty', $tutorial->difficulty) == 'advanced' ? 'selected' : '' }}>Zaawansowany</option>
                        </select>
                        @error('difficulty')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="published_at" class="block text-sm font-medium text-gray-700">Data publikacji</label>
                        <input type="datetime-local" name="published_at" id="published_at" value="{{ old('published_at', $tutorial->published_at ? $tutorial->published_at->format('Y-m-d\TH:i') : '') }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        <p class="mt-1 text-xs text-gray-500">Pozostaw puste dla daty bieżącej.</p>
                        @error('published_at')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="mb-4">
                        <label for="meta_title" class="block text-sm font-medium text-gray-700">Meta tytuł (SEO)</label>
                        <input type="text" name="meta_title" id="meta_title" value="{{ old('meta_title', $tutorial->meta_title) }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        @error('meta_title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="meta_description" class="block text-sm font-medium text-gray-700">Meta opis (SEO)</label>
                        <textarea name="meta_description" id="meta_description" rows="2" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">{{ old('meta_description', $tutorial->meta_description) }}</textarea>
                        @error('meta_description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="is_published" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" {{ old('is_published', $tutorial->is_published) ? 'checked' : '' }}>
                        <span class="ml-2 text-sm text-gray-600">Opublikowany</span>
                    </label>
                </div>
                
                <div class="flex items-center justify-end">
                    <a href="{{ route('admin.tutorials.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded mr-2">
                        Anuluj
                    </a>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                        Zapisz zmiany
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/36.0.1/classic/ckeditor.js"></script>
<script>
    ClassicEditor
        .create(document.querySelector('#content'))
        .catch(error => {
            console.error(error);
        });
        
    // Automatyczna generacja sluga z tytułu
    document.getElementById('title').addEventListener('blur', function() {
        const titleInput = this.value;
        const slugInput = document.getElementById('slug');
        
        if (slugInput.value === '') {
            const slug = titleInput
                .toLowerCase()
                .replace(/[^\w\s-]/g, '')
                .replace(/[\s_-]+/g, '-')
                .replace(/^-+|-+$/g, '');
                
            slugInput.value = slug;
        }
    });
</script>
@endpush 