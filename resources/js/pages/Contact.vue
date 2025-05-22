<template>
  <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Kontakt</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
      <!-- Contact Information -->
      <div class="bg-white shadow-sm rounded-lg p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Dane kontaktowe</h2>
        
        <div class="space-y-4">
          <div class="flex items-start">
            <div class="flex-shrink-0 text-brand-600">
              <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
              </svg>
            </div>
            <div class="ml-3">
              <p class="text-lg font-medium text-gray-900">Telefon</p>
              <p class="mt-1 text-md text-gray-600">+48 123 456 789</p>
            </div>
          </div>
          
          <div class="flex items-start">
            <div class="flex-shrink-0 text-brand-600">
              <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
            </div>
            <div class="ml-3">
              <p class="text-lg font-medium text-gray-900">Email</p>
              <p class="mt-1 text-md text-gray-600">kontakt@dartshop.pl</p>
            </div>
          </div>
          
          <div class="flex items-start">
            <div class="flex-shrink-0 text-brand-600">
              <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
            </div>
            <div class="ml-3">
              <p class="text-lg font-medium text-gray-900">Adres</p>
              <p class="mt-1 text-md text-gray-600">ul. Przykładowa 123<br>00-000 Warszawa</p>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Contact Form -->
      <div class="bg-white shadow-sm rounded-lg p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Formularz kontaktowy</h2>
        
        <form @submit.prevent="submitForm" class="space-y-4">
          <div v-if="formMessage" 
               :class="['p-4 rounded-md', formSuccess ? 'bg-green-50 text-green-800' : 'bg-red-50 text-red-800']">
            {{ formMessage }}
          </div>
          
          <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Imię i nazwisko</label>
            <input type="text" id="name" v-model="contactData.name" 
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500" 
                   :class="{ 'border-red-300': errors.name }" required>
            <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name[0] }}</p>
          </div>
          
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" id="email" v-model="contactData.email" 
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500" 
                   :class="{ 'border-red-300': errors.email }" required>
            <p v-if="errors.email" class="mt-1 text-sm text-red-600">{{ errors.email[0] }}</p>
          </div>
          
          <div>
            <label for="subject" class="block text-sm font-medium text-gray-700">Temat</label>
            <input type="text" id="subject" v-model="contactData.subject" 
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500" 
                   :class="{ 'border-red-300': errors.subject }" required>
            <p v-if="errors.subject" class="mt-1 text-sm text-red-600">{{ errors.subject[0] }}</p>
          </div>
          
          <div>
            <label for="message" class="block text-sm font-medium text-gray-700">Wiadomość</label>
            <textarea id="message" v-model="contactData.message" rows="4" 
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500" 
                      :class="{ 'border-red-300': errors.message }" required></textarea>
            <p v-if="errors.message" class="mt-1 text-sm text-red-600">{{ errors.message[0] }}</p>
          </div>
          
          <div>
            <button type="submit" 
                    :disabled="loading"
                    class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-brand-600 hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 disabled:opacity-50 disabled:cursor-not-allowed">
              <svg v-if="loading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ loading ? 'Wysyłanie...' : 'Wyślij wiadomość' }}
            </button>
          </div>
        </form>
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
        
        if (response.data.success) {
          // Reset form after successful submission
          this.contactData = {
            name: '',
            email: '',
            subject: '',
            message: ''
          };
          this.formSuccess = true;
          this.formMessage = response.data.message;
        }
      } catch (error) {
        this.formSuccess = false;
        
        if (error.response && error.response.data && error.response.data.errors) {
          this.errors = error.response.data.errors;
          this.formMessage = 'Proszę poprawić błędy w formularzu.';
        } else {
          this.formMessage = 'Wystąpił błąd podczas wysyłania wiadomości. Spróbuj ponownie później.';
        }
      } finally {
        this.loading = false;
      }
    }
  }
}
</script> 