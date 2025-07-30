<template>
  <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-blue-50 to-purple-50">
    <!-- Hero Section -->
    <div class="relative overflow-hidden bg-gradient-to-r from-indigo-600 to-purple-600 py-16">
      <div class="absolute inset-0 bg-black opacity-20"></div>
      <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">
          Skontaktuj się z nami
        </h1>
        <p class="text-xl text-indigo-100 max-w-2xl mx-auto">
          Jesteśmy tutaj, aby pomóc Ci z każdym pytaniem dotyczącym naszych produktów i usług.
        </p>
      </div>
    </div>

    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Contact Information Cards -->
        <div class="lg:col-span-1">
          <div class="space-y-6">
            <!-- Phone Card -->
            <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border border-gray-100">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-12 h-12 bg-gradient-to-r from-green-400 to-blue-500 rounded-xl flex items-center justify-center">
                    <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                    </svg>
                  </div>
                </div>
                <div class="ml-4">
                  <h3 class="text-lg font-semibold text-gray-900">Telefon</h3>
                  <p class="text-gray-600 mt-1">+48 123 456 789</p>
                  <p class="text-sm text-gray-500 mt-1">Pn-Pt: 8:00-18:00</p>
                </div>
              </div>
            </div>

            <!-- Email Card -->
            <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border border-gray-100">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-12 h-12 bg-gradient-to-r from-purple-400 to-pink-500 rounded-xl flex items-center justify-center">
                    <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                  </div>
                </div>
                <div class="ml-4">
                  <h3 class="text-lg font-semibold text-gray-900">Email</h3>
                  <p class="text-gray-600 mt-1">kontakt@dartshop.pl</p>
                  <p class="text-sm text-gray-500 mt-1">Odpowiadamy w ciągu 24h</p>
                </div>
              </div>
            </div>

            <!-- Address Card -->
            <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border border-gray-100">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-12 h-12 bg-gradient-to-r from-orange-400 to-red-500 rounded-xl flex items-center justify-center">
                    <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                  </div>
                </div>
                <div class="ml-4">
                  <h3 class="text-lg font-semibold text-gray-900">Adres</h3>
                  <p class="text-gray-600 mt-1">ul. Przykładowa 123</p>
                  <p class="text-gray-600">00-000 Warszawa</p>
                </div>
              </div>
            </div>

            <!-- Social Media -->
            <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border border-gray-100">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Śledź nas</h3>
              <div class="flex space-x-4">
                <a href="#" class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white hover:bg-blue-700 transition-colors">
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                  </svg>
                </a>
                <a href="#" class="w-10 h-10 bg-blue-800 rounded-full flex items-center justify-center text-white hover:bg-blue-900 transition-colors">
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M22.46 6c-.77.35-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15 0 1.49.75 2.81 1.91 3.56-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 0 1-1.93.07 4.28 4.28 0 0 0 4 2.98 8.521 8.521 0 0 1-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21 16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.23z"/>
                  </svg>
                </a>
                <a href="#" class="w-10 h-10 bg-pink-600 rounded-full flex items-center justify-center text-white hover:bg-pink-700 transition-colors">
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.174-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.741.098.118.112.222.085.343-.09.375-.293 1.199-.334 1.363-.053.225-.172.271-.402.165-1.495-.69-2.433-2.878-2.433-4.646 0-3.776 2.748-7.252 7.92-7.252 4.158 0 7.392 2.967 7.392 6.923 0 4.135-2.607 7.462-6.233 7.462-1.214 0-2.357-.629-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24.009 12.017 24.009c6.624 0 11.99-5.367 11.99-11.988C24.007 5.367 18.641.001.012.001z"/>
                  </svg>
                </a>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Contact Form -->
        <div class="lg:col-span-2">
          <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
            <div class="text-center mb-8">
              <h2 class="text-3xl font-bold text-gray-900 mb-2">Napisz do nas</h2>
              <p class="text-gray-600">Wypełnij formularz poniżej, a skontaktujemy się z Tobą jak najszybciej</p>
            </div>
            
            <form @submit.prevent="submitForm" class="space-y-6">
              <div v-if="formMessage" 
                   :class="['p-4 rounded-lg border transition-all duration-300', 
                           formSuccess ? 'bg-green-50 text-green-800 border-green-200' : 'bg-red-50 text-red-800 border-red-200']">
                <div class="flex items-center">
                  <svg v-if="formSuccess" class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                  </svg>
                  <svg v-else class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                  </svg>
                  {{ formMessage }}
                </div>
              </div>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Imię i nazwisko *</label>
                  <input type="text" id="name" v-model="contactData.name" 
                         class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200 outline-none" 
                         :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-200': errors.name }" 
                         placeholder="Wpisz swoje imię i nazwisko"
                         required>
                  <p v-if="errors.name" class="mt-2 text-sm text-red-600 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ errors.name[0] }}
                  </p>
                </div>
                
                <div>
                  <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email *</label>
                  <input type="email" id="email" v-model="contactData.email" 
                         class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200 outline-none" 
                         :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-200': errors.email }" 
                         placeholder="twoj@email.com"
                         required>
                          <p v-if="errors.email" class="mt-2 text-sm text-red-600 flex items-center">
          <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
          </svg>
          {{ errors.email[0] }}
        </p>
                </div>
              </div>
              
              <div>
                <label for="subject" class="block text-sm font-semibold text-gray-700 mb-2">Temat *</label>
                <input type="text" id="subject" v-model="contactData.subject" 
                       class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200 outline-none" 
                       :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-200': errors.subject }" 
                       placeholder="O czym chcesz z nami porozmawiać?"
                       required>
                <p v-if="errors.subject" class="mt-2 text-sm text-red-600 flex items-center">
                  <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                  </svg>
                  {{ errors.subject[0] }}
                </p>
              </div>
              
              <div>
                <label for="message" class="block text-sm font-semibold text-gray-700 mb-2">Wiadomość *</label>
                <textarea id="message" v-model="contactData.message" rows="5" 
                          class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200 outline-none resize-none" 
                          :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-200': errors.message }" 
                          placeholder="Opisz szczegółowo swoją sprawę..."
                          required></textarea>
                <p v-if="errors.message" class="mt-2 text-sm text-red-600 flex items-center">
                  <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                  </svg>
                  {{ errors.message[0] }}
                </p>
              </div>
              
              <div class="pt-4">
                <button type="submit" 
                        :disabled="loading"
                        class="w-full inline-flex items-center justify-center px-8 py-4 border border-transparent text-base font-semibold rounded-xl text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                  <svg v-if="loading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  <svg v-else class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                  </svg>
                  {{ loading ? 'Wysyłanie...' : 'Wyślij wiadomość' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- FAQ Section -->
      <div class="mt-16">
        <div class="text-center mb-12">
          <h2 class="text-3xl font-bold text-gray-900 mb-4">Często zadawane pytania</h2>
          <p class="text-gray-600 max-w-2xl mx-auto">Znajdź odpowiedzi na najczęściej zadawane pytania</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900 mb-3">Jak długo trwa realizacja zamówienia?</h3>
            <p class="text-gray-600">Standardowy czas realizacji zamówienia to 2-3 dni robocze. W przypadku produktów na zamówienie czas może się wydłużyć do 7-14 dni.</p>
          </div>
          
          <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900 mb-3">Jakie są koszty dostawy?</h3>
            <p class="text-gray-600">Dostawa za darmo przy zamówieniach powyżej 150 zł. Poniżej tej kwoty koszt dostawy wynosi 15 zł.</p>
          </div>
          
          <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900 mb-3">Czy mogę zwrócić produkt?</h3>
            <p class="text-gray-600">Tak, masz 14 dni na zwrot produktu bez podania przyczyny. Produkt musi być w stanie nienaruszonym.</p>
          </div>
          
          <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900 mb-3">Jak mogę śledzić swoje zamówienie?</h3>
            <p class="text-gray-600">Po wysłaniu zamówienia otrzymasz email z numerem śledzenia. Możesz także sprawdzić status w swoim koncie.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'Contact',
  data() {
    return {
      contactData: {
        name: '',
        email: '',
        subject: '',
        message: ''
      },
      loading: false,
      errors: {},
      formMessage: '',
      formSuccess: false
    }
  },
  methods: {
    async submitForm() {
      this.loading = true;
      this.errors = {};
      this.formMessage = '';
      
      try {
        const response = await axios.post('/api/contact', this.contactData);
        
        if (response.data && response.data.success) {
          // Reset form after successful submission
          this.resetForm();
          this.formSuccess = true;
          this.formMessage = response.data.message || 'Wiadomość została wysłana pomyślnie!';
        } else if (response.data && response.data.message) {
          this.formSuccess = false;
          this.formMessage = response.data.message;
          if (response.data.errors) {
            this.errors = response.data.errors;
          }
        }
      } catch (error) {
        if (error.response && error.response.data) {
          if (error.response.data.errors) {
            this.errors = error.response.data.errors;
            this.formMessage = error.response.data.message || 'Proszę poprawić błędy w formularzu.';
          } else if (error.response.data.message) {
            this.formMessage = error.response.data.message;
          } else {
            this.formMessage = 'Wystąpił błąd podczas wysyłania wiadomości. Spróbuj ponownie później.';
          }
        } else {
          this.formMessage = 'Wystąpił błąd podczas wysyłania wiadomości. Spróbuj ponownie później.';
        }
      } finally {
        this.loading = false;
      }
    },
    
    resetForm() {
      this.contactData = {
        name: '',
        email: '',
        subject: '',
        message: ''
      };
      this.errors = {};
      this.formMessage = '';
      this.formSuccess = false;
    }
  }
}
</script> 