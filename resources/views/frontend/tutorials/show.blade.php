@extends('layouts.app')

@section('title', $tutorial->meta_title ?? $tutorial->title)

@if($tutorial->meta_description)
@section('meta_description', $tutorial->meta_description)
@endif

@section('content')
    <!-- Tutorial Header - Enhanced with better styling -->
    <div class="relative">
        <div class="w-full h-64 sm:h-96 bg-indigo-800 relative">
            @if($tutorial->featured_image)
                <img src="{{ $tutorial->featuredImageUrl }}" alt="{{ $tutorial->title }}" class="w-full h-full object-cover object-center">
                <div class="absolute inset-0 bg-gradient-to-r from-indigo-900 to-indigo-700 mix-blend-multiply opacity-70"></div>
            @else
                <div class="absolute inset-0 bg-gradient-to-r from-indigo-900 to-indigo-700"></div>
                <div class="absolute inset-0 opacity-20" style="background-image: url('data:image/svg+xml,%3Csvg width=%2720%27 height=%2720%27 viewBox=%270 0 20 20%27 xmlns=%27http://www.w3.org/2000/svg%27%3E%3Cg fill=%27%23fff%27 fill-opacity=%270.1%27 fill-rule=%27evenodd%27%3E%3Ccircle cx=%273%27 cy=%273%27 r=%273%27/%3E%3Ccircle cx=%2713%27 cy=%2713%27 r=%273%27/%3E%3C/g%3E%3C/svg%3E');"></div>
            @endif
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="relative -mt-12 sm:-mt-16 lg:-mt-24">
                <div class="bg-white rounded-lg shadow-lg px-6 py-8 md:px-10 md:py-12">
                    <div class="flex flex-wrap items-center mb-6 gap-2">
                        <span class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium
                            {{ $tutorial->difficulty === 'beginner' ? 'bg-green-100 text-green-800' : ($tutorial->difficulty === 'intermediate' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                            @if($tutorial->difficulty === 'beginner')
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                </svg>
                                Początkujący
                            @elseif($tutorial->difficulty === 'intermediate')
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                                Średniozaawansowany
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                </svg>
                                Zaawansowany
                            @endif
                        </span>
                        @if($tutorial->category)
                            <span class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                                {{ $tutorial->category }}
                            </span>
                        @endif
                        <span class="inline-flex items-center text-sm text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <time datetime="{{ $tutorial->published_at->format('Y-m-d') }}">{{ $tutorial->published_at->format('d.m.Y') }}</time>
                        </span>
                    </div>
                    <h1 class="text-3xl font-extrabold text-gray-900 sm:text-4xl lg:text-5xl">{{ $tutorial->title }}</h1>
                    @if($tutorial->excerpt)
                        <p class="mt-4 text-xl text-gray-500">{{ $tutorial->excerpt }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Tutorial Content - Enhanced with table of contents and better styling -->
    <div class="bg-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row gap-10 relative">
                <!-- Table of Contents - on larger screens -->
                <div class="hidden md:block md:w-1/4 lg:w-1/5">
                    <div class="bg-indigo-50 p-4 rounded-lg shadow-sm sticky top-24 max-h-[calc(100vh-150px)] overflow-y-auto">
                        <h3 class="text-lg font-semibold text-indigo-900 mb-3 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                            </svg>
                            Spis treści
                        </h3>
                        <div id="toc-container" class="text-sm">
                            <!-- Filled by JavaScript -->
                            <div class="text-gray-500 text-xs italic">Ładowanie spisu treści...</div>
                        </div>
                    </div>
                </div>
                
                <!-- Main Content -->
                <div class="md:w-3/4 lg:w-4/5">
                    <div class="prose prose-indigo prose-lg max-w-none tutorial-content">
                        {!! $tutorial->content !!}
                    </div>
                    
                    <!-- Share and navigation -->
                    <div class="mt-12 pt-6 border-t border-gray-200">
                        <div class="flex flex-col sm:flex-row justify-between items-center">
                            <!-- Share buttons -->
                            <div class="flex items-center mb-4 sm:mb-0">
                                <span class="text-gray-500 mr-4">Udostępnij:</span>
                                <div class="flex space-x-2">
                                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ url()->current() }}" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:text-blue-800">
                                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M18.77 7.46H14.5v-1.9c0-.9.6-1.1 1-1.1h3V.5h-4.33C10.24.5 9.5 3.44 9.5 5.32v2.15h-3v4h3v12h5v-12h3.85l.42-4z"/></svg>
                                    </a>
                                    <a href="https://twitter.com/intent/tweet?url={{ url()->current() }}&text={{ $tutorial->title }}" target="_blank" rel="noopener noreferrer" class="text-blue-400 hover:text-blue-600">
                                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M23.44 4.83c-.8.37-1.5.38-2.22.02.93-.56.98-.96 1.32-2.02-.88.52-1.86.9-2.9 1.1-.82-.88-2-1.43-3.3-1.43-2.5 0-4.55 2.04-4.55 4.54 0 .36.03.7.1 1.04-3.77-.2-7.12-2-9.36-4.75-.4.67-.6 1.45-.6 2.3 0 1.56.8 2.95 2 3.77-.74-.03-1.44-.23-2.05-.57v.06c0 2.2 1.56 4.03 3.64 4.44-.67.2-1.37.2-2.06.08.58 1.8 2.26 3.12 4.25 3.16C5.78 18.1 3.37 18.74 1 18.46c2 1.3 4.4 2.04 6.97 2.04 8.35 0 12.92-6.92 12.92-12.93 0-.2 0-.4-.02-.6.9-.63 1.96-1.22 2.56-2.14z"/></svg>
                                    </a>
                                    <a href="mailto:?subject={{ $tutorial->title }}&body={{ url()->current() }}" class="text-gray-600 hover:text-gray-800">
                                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
                                    </a>
                                </div>
                            </div>
                            
                            <!-- Navigation -->
                            <div class="flex space-x-2">
                                <a href="{{ route('frontend.tutorials') }}" class="inline-flex items-center justify-center px-4 py-2 border border-indigo-300 text-sm font-medium rounded-md text-indigo-700 bg-white hover:bg-indigo-50 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                    </svg>
                                    Powrót do poradników
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Tutorials - Enhanced with better cards -->
    @if($relatedTutorials->count() > 0)
    <div class="bg-gray-50 py-12">
        <div class="max-w-7xl mx-auto py-8 px-4 sm:py-12 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-extrabold text-gray-900 mb-8 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Podobne poradniki
            </h2>
            
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($relatedTutorials as $relatedTutorial)
                    <div class="flex flex-col rounded-lg shadow-lg overflow-hidden transition-all duration-200 transform hover:-translate-y-2 bg-white">
                        <div class="flex-shrink-0 overflow-hidden h-48">
                            <img class="h-48 w-full object-cover transition-transform duration-500 hover:scale-105" src="{{ $relatedTutorial->thumbnailImageUrl }}" alt="{{ $relatedTutorial->title }}">
                        </div>
                        <div class="flex-1 p-6 flex flex-col justify-between">
                            <div class="flex-1">
                                <div class="flex gap-2 mb-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $relatedTutorial->difficulty === 'beginner' ? 'bg-green-100 text-green-800' : ($relatedTutorial->difficulty === 'intermediate' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ $relatedTutorial->difficulty === 'beginner' ? 'Początkujący' : ($relatedTutorial->difficulty === 'intermediate' ? 'Średniozaawansowany' : 'Zaawansowany') }}
                                    </span>
                                </div>
                                <a href="{{ route('frontend.tutorials.show', $relatedTutorial) }}" class="block group">
                                    <p class="text-xl font-semibold text-gray-900 group-hover:text-indigo-600 transition-colors">{{ $relatedTutorial->title }}</p>
                                    <p class="mt-3 text-base text-gray-500">{{ Str::limit($relatedTutorial->excerpt, 100) }}</p>
                                </a>
                            </div>
                            <div class="mt-6 flex items-center justify-between">
                                <div class="flex items-center text-sm text-gray-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    {{ $relatedTutorial->published_at->format('d.m.Y') }}
                                </div>
                                <a href="{{ route('frontend.tutorials.show', $relatedTutorial) }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-500 group">
                                    <span>Czytaj</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="ml-1 h-4 w-4 transition transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Call to Action - Enhanced with gradient and better styling -->
    <div class="bg-gradient-to-r from-indigo-800 to-indigo-700">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:py-16 lg:px-8 lg:flex lg:items-center lg:justify-between">
            <div>
                <h2 class="text-3xl font-extrabold tracking-tight text-white sm:text-4xl">
                    <span class="block">Chcesz ulepszyć swój sprzęt?</span>
                    <span class="block text-indigo-200">Sprawdź nasze produkty już teraz.</span>
                </h2>
                <p class="mt-4 text-lg text-indigo-100 max-w-3xl">
                    Profesjonalny sprzęt, szeroki wybór lotek, tarcz i akcesoriów. Wszystko, czego potrzebujesz, by podnieść swoje umiejętności na wyższy poziom.
                </p>
            </div>
            <div class="mt-8 flex flex-col sm:flex-row lg:mt-0 lg:flex-shrink-0 gap-3">
                <div class="inline-flex rounded-md shadow">
                    <a href="{{ route('frontend.categories.index') }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-indigo-600 bg-white hover:bg-indigo-50 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        Zobacz ofertę
                    </a>
                </div>
                <div class="inline-flex rounded-md shadow">
                    <a href="{{ route('frontend.tutorials') }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Wszystkie poradniki
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Table of contents generator
        const articleContent = document.querySelector('.tutorial-content');
        const tocContainer = document.getElementById('toc-container');
        
        if (articleContent && tocContainer) {
            // Find all h2 and h3 elements in the article
            const headings = articleContent.querySelectorAll('h2, h3');
            
            if (headings.length > 0) {
                tocContainer.innerHTML = ''; // Clear loading message
                
                headings.forEach((heading, index) => {
                    // Create a unique ID for the heading if it doesn't have one
                    if (!heading.id) {
                        heading.id = 'heading-' + index;
                    }
                    
                    // Add special styling to headings
                    if (heading.tagName === 'H2') {
                        heading.classList.add('relative', 'pl-4', 'mt-10', 'mb-6', 'font-bold', 'text-blue-900', 'before:content-[""]', 'before:absolute', 'before:left-0', 'before:top-0', 'before:bottom-0', 'before:w-1', 'before:bg-gradient-to-b', 'before:from-indigo-600', 'before:to-indigo-400', 'before:rounded-md');
                    } else if (heading.tagName === 'H3') {
                        heading.classList.add('text-indigo-800', 'mt-8', 'mb-4', 'font-semibold');
                    }
                    
                    // Create a table of contents entry
                    const tocEntry = document.createElement('a');
                    tocEntry.href = '#' + heading.id;
                    tocEntry.classList.add('block', 'py-2', 'px-3', 'border-l-2', 'border-transparent', 'hover:border-indigo-500', 'hover:bg-indigo-50/50', 'hover:text-indigo-800', 'transition-all', 'duration-200');
                    
                    // Add different styling for h2 vs h3
                    if (heading.tagName === 'H3') {
                        tocEntry.classList.add('pl-6', 'text-sm', 'text-gray-600');
                    } else {
                        tocEntry.classList.add('font-medium', 'text-gray-800');
                    }
                    
                    tocEntry.textContent = heading.textContent;
                    tocContainer.appendChild(tocEntry);
                    
                    // Smooth scroll to heading when clicking on TOC entry
                    tocEntry.addEventListener('click', function(e) {
                        e.preventDefault();
                        document.querySelector(this.getAttribute('href')).scrollIntoView({ 
                            behavior: 'smooth' 
                        });
                    });
                });
                
                // Add styling to list items in content
                const lists = articleContent.querySelectorAll('ul, ol');
                lists.forEach(list => {
                    list.classList.add('ml-6');
                    const items = list.querySelectorAll('li');
                    items.forEach(item => {
                        item.classList.add('mb-2');
                    });
                });
                
            } else {
                tocContainer.innerHTML = '<div class="text-gray-500 text-xs">Brak nagłówków w artykule</div>';
            }
        }
    });
</script>
@endsection 