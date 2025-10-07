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
                  <p class="text-gray-600 mt-1">
                    <a href="tel:+48123456789" class="hover:text-indigo-600 transition-colors duration-200">+48 123 456 789</a>
                  </p>
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
                  <p class="text-gray-600 mt-1">
                    <a href="mailto:kontakt@dartshop.pl" class="hover:text-indigo-600 transition-colors duration-200">kontakt@dartshop.pl</a>
                  </p>
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