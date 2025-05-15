@extends('layouts.app')

@section('title', 'Twój profil')

@section('content')
<div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">{{ __('Twój profil') }}</h2>
            </div>
            
            <div class="mb-6 border-b border-gray-200">
                <nav class="flex space-x-6">
                    <a href="{{ route('profile.edit') }}" class="px-3 py-2 text-sm font-medium {{ request()->routeIs('profile.edit') ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500 hover:text-gray-700' }}">
                        {{ __('Profil') }}
                    </a>
                    <a href="{{ route('profile.orders') }}" class="px-3 py-2 text-sm font-medium {{ request()->routeIs('profile.orders') ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500 hover:text-gray-700' }}">
                        {{ __('Zamówienia') }}
                    </a>
                    <a href="{{ route('profile.payments') }}" class="px-3 py-2 text-sm font-medium {{ request()->routeIs('profile.payments') ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500 hover:text-gray-700' }}">
                        {{ __('Płatności') }}
                    </a>
                    <a href="{{ route('profile.favorites') }}" class="px-3 py-2 text-sm font-medium {{ request()->routeIs('profile.favorites') ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500 hover:text-gray-700' }}">
                        {{ __('Ulubione') }}
                    </a>
                    <a href="{{ route('profile.addresses') }}" class="px-3 py-2 text-sm font-medium {{ request()->routeIs('profile.addresses*') ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500 hover:text-gray-700' }}">
                        {{ __('Adresy') }}
                    </a>
                </nav>
            </div>
            
            <div class="mb-10">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Informacje o profilu') }}</h3>
                <div class="bg-gray-50 p-6 rounded-lg">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>
            
            <div class="mb-10">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Aktualizacja hasła') }}</h3>
                <div class="bg-gray-50 p-6 rounded-lg">
                    @include('profile.partials.update-password-form')
                </div>
            </div>
            
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Usunięcie konta') }}</h3>
                <div class="bg-gray-50 p-6 rounded-lg">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</div>

{{-- @if (session('status') === 'password-updated')
    <div class="modal fade" id="passwordUpdatedModal" tabindex="-1" role="dialog" aria-labelledby="passwordUpdatedModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <p>{{ __('Password updated.') }}</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $('#passwordUpdatedModal').modal('show');
            setTimeout(function () {
                $('#passwordUpdatedModal').modal('hide');
            }, 2000);
        });
    </script>
@endif --}}

@endsection
