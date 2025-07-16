<template>
  <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-blue-50 to-purple-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
      <!-- Error Header -->
      <div class="text-center">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-red-100 rounded-full mb-4">
          <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </div>
        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">
          Błąd weryfikacji
        </h2>
        <p class="text-gray-600">
          Nie udało się zweryfikować Twojego adresu email
        </p>
      </div>

      <!-- Error Card -->
      <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">
        <div class="px-8 py-8">
          <div class="text-center space-y-4">
            <div class="text-red-600">
              <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
              </svg>
            </div>
            
            <h3 class="text-xl font-semibold text-gray-900">
              Link weryfikacyjny nieprawidłowy
            </h3>
            
            <p class="text-gray-600">
              Link weryfikacyjny mógł wygasnąć lub być nieprawidłowy. Spróbuj ponownie zapisać się do newslettera.
            </p>
            
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mt-6">
              <div class="flex items-start">
                <svg class="w-5 h-5 text-yellow-600 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <div class="text-sm text-yellow-700">
                  <p class="font-medium mb-1">Co mogło się stać?</p>
                  <ul class="list-disc list-inside space-y-1">
                    <li>Link mógł wygasnąć (ważny przez 24 godziny)</li>
                    <li>Link został już użyty</li>
                    <li>Adres email został już zweryfikowany</li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Footer -->
        <div class="px-8 py-6 bg-gray-50 border-t border-gray-200">
          <div class="text-center space-y-4">
            <router-link 
              to="/contact" 
              class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition-colors duration-200"
            >
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
              </svg>
              Zapisz się ponownie
            </router-link>
            
            <div class="text-sm text-gray-500">
              <p>Jeśli problem się powtarza, skontaktuj się z nami.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'NewsletterVerify',
  meta: {
    title: 'Błąd weryfikacji newslettera'
  }
}
</script> 