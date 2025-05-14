@extends('layouts.admin')

@section('title', 'Wiadomości kontaktowe')

@section('content')
<div class="bg-white shadow rounded-lg overflow-hidden">
    <div class="p-6 bg-white border-b border-gray-200">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Wiadomości kontaktowe</h2>
        </div>
        
        <div class="overflow-hidden overflow-x-auto rounded-lg shadow">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Imię i nazwisko</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Temat</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Akcje</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    {{-- Example row, replace with actual data --}}
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">1</td>
                        <td class="px-6 py-4 whitespace-nowrap">Jan Kowalski</td>
                        <td class="px-6 py-4 whitespace-nowrap">jan.kowalski@example.com</td>
                        <td class="px-6 py-4 whitespace-nowrap">Pytanie o produkt</td>
                        <td class="px-6 py-4 whitespace-nowrap">12.05.2023 14:30</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Nieprzeczytana</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <button type="button" class="text-indigo-600 hover:text-indigo-900">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <form class="inline" action="#" method="POST" onsubmit="return confirm('Czy na pewno chcesz usunąć tę wiadomość?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">2</td>
                        <td class="px-6 py-4 whitespace-nowrap">Anna Nowak</td>
                        <td class="px-6 py-4 whitespace-nowrap">anna.nowak@example.com</td>
                        <td class="px-6 py-4 whitespace-nowrap">Zamówienie #12345</td>
                        <td class="px-6 py-4 whitespace-nowrap">10.05.2023 09:15</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Odpowiedziano</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <button type="button" class="text-indigo-600 hover:text-indigo-900">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <form class="inline" action="#" method="POST" onsubmit="return confirm('Czy na pewno chcesz usunąć tę wiadomość?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination example -->
        <div class="mt-4 flex justify-center">
            <nav class="inline-flex rounded-md shadow">
                <a href="#" class="px-3 py-2 bg-white border border-gray-300 text-gray-500 hover:bg-gray-50">Poprzednia</a>
                <a href="#" class="px-3 py-2 bg-blue-50 border border-blue-500 text-blue-600">1</a>
                <a href="#" class="px-3 py-2 bg-white border border-gray-300 text-gray-500 hover:bg-gray-50">2</a>
                <a href="#" class="px-3 py-2 bg-white border border-gray-300 text-gray-500 hover:bg-gray-50">3</a>
                <a href="#" class="px-3 py-2 bg-white border border-gray-300 text-gray-500 hover:bg-gray-50">Następna</a>
            </nav>
        </div>
    </div>
</div>

<!-- Modal for message details (hidden by default) -->
<div class="hidden fixed inset-0 z-50 overflow-auto bg-black bg-opacity-50 flex items-center justify-center" id="message-modal">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4">
        <div class="border-b px-6 py-4">
            <h3 class="text-xl font-bold">Szczegóły wiadomości</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <h4 class="font-bold text-gray-700">Od:</h4>
                    <p id="modal-name">Jan Kowalski</p>
                </div>
                <div>
                    <h4 class="font-bold text-gray-700">Email:</h4>
                    <p id="modal-email">jan.kowalski@example.com</p>
                </div>
                <div>
                    <h4 class="font-bold text-gray-700">Temat:</h4>
                    <p id="modal-subject">Pytanie o produkt</p>
                </div>
                <div>
                    <h4 class="font-bold text-gray-700">Data:</h4>
                    <p id="modal-date">12.05.2023 14:30</p>
                </div>
            </div>
            <div class="mb-6">
                <h4 class="font-bold text-gray-700">Wiadomość:</h4>
                <p id="modal-message" class="mt-2 border rounded-md p-3 bg-gray-50">
                    Dzień dobry, chciałbym zapytać o dostępność produktu XYZ. Kiedy będzie ponownie dostępny w Państwa sklepie?
                </p>
            </div>
            <div>
                <h4 class="font-bold text-gray-700">Odpowiedź:</h4>
                <textarea id="modal-reply" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 mt-2"></textarea>
            </div>
        </div>
        <div class="border-t px-6 py-4 flex justify-end space-x-4">
            <button type="button" class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400" id="close-modal">Zamknij</button>
            <button type="button" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Wyślij odpowiedź</button>
        </div>
    </div>
</div>
@endsection 