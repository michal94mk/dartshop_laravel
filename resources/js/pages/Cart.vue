<template>
  <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Koszyk</h1>
    
    <div class="my-6">
      <p class="text-xl text-gray-600">To jest strona koszyka. Tu będzie możliwość zarządzania zawartością koszyka.</p>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Cart',
  data() {
    return {
      cartItems: [],
      loading: false
    }
  },
  mounted() {
    // In a real app, this would fetch cart data from an API
  }
}
</script> 