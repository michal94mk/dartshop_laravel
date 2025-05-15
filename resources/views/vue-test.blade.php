@extends('layouts.app')

@section('title', 'Vue Test')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Vue.js Demo</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Vue component from our file -->
        <example-component></example-component>
        
        <!-- Regular Laravel+Blade content -->
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Laravel Blade Content</h2>
            <p class="text-gray-600 mb-4">This content is rendered using Laravel Blade templates.</p>
            <div class="bg-gray-100 p-4 rounded">
                <p>Current time: {{ now() }}</p>
            </div>
        </div>
    </div>
</div>
@endsection 