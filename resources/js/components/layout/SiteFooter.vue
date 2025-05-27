<template>
  <footer class="bg-gradient-to-r from-indigo-900 via-purple-900 to-indigo-900 text-white border-t border-indigo-700/50 mt-12">
    <div class="max-w-7xl mx-auto py-16 px-4 sm:px-6 lg:px-8">
      <!-- Newsletter Section -->
      <div class="mb-16">
        <h2 class="text-3xl font-bold text-center text-white mb-8">Zapisz się do Newslettera</h2>
        <NewsletterSubscription />
      </div>
      
      <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
        <div class="md:col-span-1">
          <h3 class="text-lg font-bold text-white tracking-wider uppercase mb-4">O DartShop</h3>
          <p class="text-indigo-200 leading-relaxed mb-4">
            DartShop to sklep specjalizujący się w sprzedaży wysokiej jakości akcesoriów do gry w dart. Oferujemy profesjonalny sprzęt dla każdego poziomu zaawansowania.
          </p>
          <div class="flex items-center text-indigo-200 text-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Sprawdzona jakość od 2023
          </div>
        </div>
        
        <div>
          <h3 class="text-lg font-bold text-white tracking-wider uppercase mb-4">Kategorie</h3>
          <ul role="list" class="space-y-3">
            <li>
              <router-link to="/products" class="text-indigo-200 hover:text-white transition-colors duration-200 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                Lotki
              </router-link>
            </li>
            <li>
              <router-link to="/products" class="text-indigo-200 hover:text-white transition-colors duration-200 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                Tarcze
              </router-link>
            </li>
            <li>
              <router-link to="/products" class="text-indigo-200 hover:text-white transition-colors duration-200 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                Akcesoria
              </router-link>
            </li>
          </ul>
        </div>
        
        <div>
          <h3 class="text-lg font-bold text-white tracking-wider uppercase mb-4">Pomoc</h3>
          <ul role="list" class="space-y-3">
            <li>
              <router-link to="/contact" class="text-indigo-200 hover:text-white transition-colors duration-200 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                Kontakt
              </router-link>
            </li>
            <li>
              <router-link to="/promotions" class="text-indigo-200 hover:text-white transition-colors duration-200 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                </svg>
                Promocje
              </router-link>
            </li>
            <li>
              <router-link to="/tutorials" class="text-indigo-200 hover:text-white transition-colors duration-200 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                Poradniki
              </router-link>
            </li>
            <li>
              <router-link to="/about" class="text-indigo-200 hover:text-white transition-colors duration-200 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                O nas
              </router-link>
            </li>
          </ul>
        </div>
        
        <div>
          <h3 class="text-lg font-bold text-white tracking-wider uppercase mb-4">Śledź nas</h3>
          <div class="flex space-x-4 mb-6">
            <a href="#" class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center text-indigo-200 hover:text-white hover:bg-white/20 transition-all duration-200 backdrop-blur-sm">
              <span class="sr-only">Facebook</span>
              <i class="fab fa-facebook text-lg"></i>
            </a>
            <a href="#" class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center text-indigo-200 hover:text-white hover:bg-white/20 transition-all duration-200 backdrop-blur-sm">
              <span class="sr-only">Instagram</span>
              <i class="fab fa-instagram text-lg"></i>
            </a>
            <a href="#" class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center text-indigo-200 hover:text-white hover:bg-white/20 transition-all duration-200 backdrop-blur-sm">
              <span class="sr-only">Twitter</span>
              <i class="fab fa-twitter text-lg"></i>
            </a>
            <a href="#" class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center text-indigo-200 hover:text-white hover:bg-white/20 transition-all duration-200 backdrop-blur-sm">
              <span class="sr-only">YouTube</span>
              <i class="fab fa-youtube text-lg"></i>
            </a>
          </div>
          
          <div class="text-indigo-200 text-sm">
            <p class="mb-2 flex items-center">
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
              </svg>
              hello@dartshop.pl
            </p>
            <p class="flex items-center">
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
              </svg>
              +48 123 456 789
            </p>
          </div>
        </div>
      </div>
      
      <div class="mt-12 pt-8 border-t border-indigo-700/50">
        <div class="flex flex-col md:flex-row justify-between items-center">
          <p class="text-indigo-200 text-center md:text-left">
            &copy; {{ currentYear }} DartShop. Wszelkie prawa zastrzeżone.
          </p>
          <div class="flex space-x-6 mt-4 md:mt-0">
            <router-link to="/privacy" class="text-indigo-200 hover:text-white text-sm transition-colors duration-200">
              Polityka prywatności
            </router-link>
            <router-link to="/terms" class="text-indigo-200 hover:text-white text-sm transition-colors duration-200">
              Regulamin
            </router-link>
            <router-link to="/cookies" class="text-indigo-200 hover:text-white text-sm transition-colors duration-200">
              Cookies
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </footer>
</template>

<script>
import NewsletterSubscription from '../ui/NewsletterSubscription.vue';

export default {
  name: 'SiteFooter',
  components: {
    NewsletterSubscription
  },
  computed: {
    currentYear() {
      return new Date().getFullYear();
    }
  }
}
</script> 