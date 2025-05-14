@extends('layouts.admin')

@section('title', 'Wiadomości kontaktowe')

@section('content')
<div class="bg-white shadow rounded-lg overflow-hidden">
    <div class="p-6 bg-white border-b border-gray-200">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Wiadomości kontaktowe</h2>
        </div>
        
        @if($messages->count() > 0)
            <div class="flex justify-between items-center mb-4">
                <div>
                    <p class="text-sm text-gray-600">
                        Wyświetlanie {{ $messages->firstItem() }}-{{ $messages->lastItem() }} z {{ $messages->total() }} wiadomości
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Imię i nazwisko</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Temat</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Akcje</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($messages as $message)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">{{ $message->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $message->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $message->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $message->subject }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $message->created_at->format('d.m.Y H:i') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $message->status_color }}-100 text-{{ $message->status_color }}-800">
                                        {{ $message->status_label }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button type="button" class="text-indigo-600 hover:text-indigo-900 view-message" 
                                                data-id="{{ $message->id }}" data-name="{{ $message->name }}" 
                                                data-email="{{ $message->email }}" data-subject="{{ $message->subject }}"
                                                data-message="{{ $message->message }}" data-reply="{{ $message->reply }}"
                                                data-date="{{ $message->created_at->format('d.m.Y H:i') }}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <form class="inline" action="{{ route('admin.contact.destroy', $message->id) }}" method="POST" onsubmit="return confirm('Czy na pewno chcesz usunąć tę wiadomość?')">
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
                {{ $messages->appends(request()->except('page'))->links() }}
            </div>
        @else
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mt-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-yellow-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            Brak wiadomości kontaktowych w bazie danych.
                        </p>
                    </div>
                </div>
            </div>
        @endif
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
                    <p id="modal-name"></p>
                </div>
                <div>
                    <h4 class="font-bold text-gray-700">Email:</h4>
                    <p id="modal-email"></p>
                </div>
                <div>
                    <h4 class="font-bold text-gray-700">Temat:</h4>
                    <p id="modal-subject"></p>
                </div>
                <div>
                    <h4 class="font-bold text-gray-700">Data:</h4>
                    <p id="modal-date"></p>
                </div>
            </div>
            <div class="mb-6">
                <h4 class="font-bold text-gray-700">Wiadomość:</h4>
                <p id="modal-message" class="mt-2 border rounded-md p-3 bg-gray-50"></p>
            </div>
            <form id="reply-form" action="{{ route('admin.contact.reply', ':id') }}" method="POST">
                @csrf
                <div>
                    <h4 class="font-bold text-gray-700">Odpowiedź:</h4>
                    <textarea id="modal-reply" name="reply" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 mt-2"></textarea>
                </div>
            </form>
        </div>
        <div class="border-t px-6 py-4 flex justify-end space-x-4">
            <button type="button" class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400" id="close-modal">Zamknij</button>
            <button type="button" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700" id="send-reply">Wyślij odpowiedź</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('message-modal');
        const viewButtons = document.querySelectorAll('.view-message');
        const closeButton = document.getElementById('close-modal');
        const sendReplyButton = document.getElementById('send-reply');
        const replyForm = document.getElementById('reply-form');
        
        // Show modal when view button is clicked
        viewButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                const email = this.getAttribute('data-email');
                const subject = this.getAttribute('data-subject');
                const message = this.getAttribute('data-message');
                const reply = this.getAttribute('data-reply');
                const date = this.getAttribute('data-date');
                
                // Update form action
                replyForm.action = replyForm.action.replace(':id', id);
                
                // Update modal content
                document.getElementById('modal-name').textContent = name;
                document.getElementById('modal-email').textContent = email;
                document.getElementById('modal-subject').textContent = subject;
                document.getElementById('modal-message').textContent = message;
                document.getElementById('modal-date').textContent = date;
                document.getElementById('modal-reply').value = reply || '';
                
                // Show modal
                modal.classList.remove('hidden');
                
                // Mark as read via AJAX
                fetch(`/admin/contact/${id}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
            });
        });
        
        // Close modal when close button is clicked
        closeButton.addEventListener('click', function() {
            modal.classList.add('hidden');
        });
        
        // Handle clicking outside modal to close
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.classList.add('hidden');
            }
        });
        
        // Handle reply form submission
        sendReplyButton.addEventListener('click', function() {
            replyForm.submit();
        });
    });
</script>
@endpush 