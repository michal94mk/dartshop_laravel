@extends('layouts.admin-tailwind')

@section('title', isset($product) ? 'Edytuj produkt: ' . $product->name : 'Dodaj nowy produkt')

@section('content')
<div class="bg-white shadow rounded-lg overflow-hidden">
    <div class="p-6 bg-white border-b border-gray-200">
        <div class="mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">
                {{ isset($product) ? 'Edytuj produkt: ' . $product->name : 'Dodaj nowy produkt' }}
            </h2>
        </div>

        <form action="{{ isset($product) ? route('admin.products.update', $product->id) : route('admin.products.store') }}" 
              method="POST" 
              enctype="multipart/form-data"
              class="space-y-6">
            @csrf
            @if(isset($product))
                @method('PUT')
            @endif

            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Nazwa</label>
                <input type="text" name="name" id="name" value="{{ old('name', $product->name ?? '') }}" 
                       class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Opis</label>
                <textarea name="description" id="description" rows="4" 
                          class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">{{ old('description', $product->description ?? '') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Price -->
            <div>
                <label for="price" class="block text-sm font-medium text-gray-700">Cena</label>
                <div class="mt-1 relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">zł</span>
                    </div>
                    <input type="number" step="0.01" min="0" name="price" id="price" value="{{ old('price', $product->price ?? '') }}" 
                           class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 pr-12 sm:text-sm border-gray-300 rounded-md">
                </div>
                @error('price')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Category -->
            <div>
                <label for="category_id" class="block text-sm font-medium text-gray-700">Kategoria</label>
                <select name="category_id" id="category_id" 
                        class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <option value="">Wybierz kategorię</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ (old('category_id', $product->category_id ?? '') == $category->id) ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Brand -->
            <div>
                <label for="brand_id" class="block text-sm font-medium text-gray-700">Marka</label>
                <select name="brand_id" id="brand_id" 
                        class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <option value="">Wybierz markę</option>
                    @foreach($brands as $brand)
                        <option value="{{ $brand->id }}" {{ (old('brand_id', $product->brand_id ?? '') == $brand->id) ? 'selected' : '' }}>
                            {{ $brand->name }}
                        </option>
                    @endforeach
                </select>
                @error('brand_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Image -->
            <div>
                <label for="image" class="block text-sm font-medium text-gray-700">Zdjęcie</label>
                <div class="mt-1 flex items-center">
                    @if(isset($product) && $product->image)
                        <div class="mr-4 flex-shrink-0">
                            <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="h-20 w-20 object-cover rounded-md">
                        </div>
                    @endif
                    <div class="flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md flex-1">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="image" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                    <span>Dodaj zdjęcie</span>
                                    <input id="image" name="image" type="file" class="sr-only" accept="image/*">
                                </label>
                                <p class="pl-1">lub przeciągnij i upuść</p>
                            </div>
                            <p class="text-xs text-gray-500">
                                PNG, JPG, GIF do 2MB
                            </p>
                        </div>
                    </div>
                </div>
                @error('image')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end pt-5">
                <a href="{{ route('admin.products.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Anuluj
                </a>
                <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    {{ isset($product) ? 'Zapisz zmiany' : 'Dodaj produkt' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Image preview
    document.getElementById('image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                // If there's already an image, replace it
                let existingImg = document.querySelector('.mr-4.flex-shrink-0 img');
                
                if (existingImg) {
                    existingImg.src = e.target.result;
                } else {
                    // Create new image container
                    const imgContainer = document.createElement('div');
                    imgContainer.className = 'mr-4 flex-shrink-0';
                    
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'h-20 w-20 object-cover rounded-md';
                    img.alt = 'Preview';
                    
                    imgContainer.appendChild(img);
                    
                    // Add before the upload box
                    const uploadBox = document.querySelector('.flex.justify-center');
                    uploadBox.parentNode.insertBefore(imgContainer, uploadBox);
                }
            }
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush 