<template>
  <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-blue-50 to-purple-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
      <!-- Success Header -->
      <div class="text-center">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
          <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
          </svg>
        </div>
        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">
          Dziękujemy!
        </h2>
        <p class="text-gray-600">
          Twój adres email został pomyślnie zweryfikowany
        </p>
      </div>

      <!-- Success Card -->
      <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">
        <div class="px-8 py-8">
          <div class="text-center space-y-4">
            <div class="text-green-600">
              <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            
            <h3 class="text-xl font-semibold text-gray-900">
              Newsletter aktywowany!
            </h3>
            
            <p class="text-gray-600">
              Od teraz będziesz otrzymywać najnowsze informacje o produktach, promocjach i nowościach w naszym sklepie.
            </p>
            
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mt-6">
              <div class="flex items-start">
                <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                <div class="text-sm text-blue-700">
                  <p class="font-medium mb-1">Co dalej?</p>
                  <p>Sprawdź swoją skrzynkę email - wysłaliśmy Ci powitalny newsletter z dodatkowymi informacjami.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Footer -->
        <div class="px-8 py-6 bg-gray-50 border-t border-gray-200">
          <div class="text-center space-y-4">
            <router-link 
              to="/" 
              class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition-colors duration-200"
            >
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
              </svg>
              Wróć do sklepu
            </router-link>
            
            <div class="text-sm text-gray-500">
              <p>W każdej chwili możesz się wypisać z newslettera klikając link "Wypisz się" w mailach.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'NewsletterVerified',
  meta: {
    title: 'Newsletter zweryfikowany'
  }
}
</script> 