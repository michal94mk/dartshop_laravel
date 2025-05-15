<template>
  <footer class="bg-white border-t border-gray-200 mt-12">
    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
        <div>
          <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">O nas</h3>
          <p class="mt-4 text-base text-gray-500">
            DartShop to sklep specjalizujący się w sprzedaży wysokiej jakości akcesoriów do gry w dart.
          </p>
        </div>
        <div>
          <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">Kategorie</h3>
          <ul role="list" class="mt-4 space-y-4">
            <li>
              <router-link to="/categories" class="text-base text-gray-500 hover:text-gray-900">Lotki</router-link>
            </li>
            <li>
              <router-link to="/categories" class="text-base text-gray-500 hover:text-gray-900">Tarcze</router-link>
            </li>
            <li>
              <router-link to="/categories" class="text-base text-gray-500 hover:text-gray-900">Akcesoria</router-link>
            </li>
          </ul>
        </div>
        <div>
          <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">Pomoc</h3>
          <ul role="list" class="mt-4 space-y-4">
            <li>
              <router-link to="/contact" class="text-base text-gray-500 hover:text-gray-900">Kontakt</router-link>
            </li>
            <li>
              <router-link to="/promotions" class="text-base text-gray-500 hover:text-gray-900">Promocje</router-link>
            </li>
            <li>
              <router-link to="/delivery" class="text-base text-gray-500 hover:text-gray-900">Dostawa</router-link>
            </li>
            <li>
              <router-link to="/returns" class="text-base text-gray-500 hover:text-gray-900">Zwroty</router-link>
            </li>
          </ul>
        </div>
        <div>
          <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">Śledź nas</h3>
          <div class="flex space-x-6 mt-4">
            <a href="#" class="text-gray-400 hover:text-gray-500">
              <span class="sr-only">Facebook</span>
              <i class="fab fa-facebook text-xl"></i>
            </a>
            <a href="#" class="text-gray-400 hover:text-gray-500">
              <span class="sr-only">Instagram</span>
              <i class="fab fa-instagram text-xl"></i>
            </a>
            <a href="#" class="text-gray-400 hover:text-gray-500">
              <span class="sr-only">Twitter</span>
              <i class="fab fa-twitter text-xl"></i>
            </a>
          </div>
        </div>
      </div>
      <div class="mt-12 border-t border-gray-200 pt-8">
        <p class="text-base text-gray-400 text-center">&copy; {{ currentYear }} DartShop. Wszelkie prawa zastrzeżone.</p>
      </div>
    </div>
  </footer>
</template>

<script>
export default {
  name: 'SiteFooter',
  computed: {
    currentYear() {
      return new Date().getFullYear();
    }
  }
}
</script> 