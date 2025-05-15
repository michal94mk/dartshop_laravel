@extends('layouts.app')

@section('title', 'Twoje zamówienia')

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
            
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Twoje zamówienia') }}</h3>
                
                @if($orders->isEmpty())
                    <div class="bg-gray-50 p-6 rounded-lg text-center">
                        <p class="text-gray-500">{{ __('Nie masz jeszcze żadnych zamówień.') }}</p>
                        <a href="{{ route('home') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Przeglądaj produkty') }}
                        </a>
                    </div>
                @else
                    <div class="bg-gray-50 rounded-lg overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Numer zamówienia') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Data') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Status') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Suma') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Akcje') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($orders as $order)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            #{{ $order->order_number }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $order->created_at->format('d.m.Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if($order->status->value === 'completed') bg-green-100 text-green-800 
                                                @elseif($order->status->value === 'processing') bg-blue-100 text-blue-800 
                                                @elseif($order->status->value === 'pending') bg-yellow-100 text-yellow-800 
                                                @elseif($order->status->value === 'cancelled') bg-red-100 text-red-800 
                                                @else bg-gray-100 text-gray-800 @endif">
                                                {{ ucfirst($order->status->value) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ number_format($order->total, 2) }} zł
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <a href="{{ route('order.show', $order) }}" class="text-blue-600 hover:text-blue-900">
                                                {{ __('Szczegóły') }}
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $orders->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 