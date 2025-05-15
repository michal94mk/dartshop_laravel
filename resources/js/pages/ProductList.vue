<template>
  <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Produkty</h1>
    
    <div class="my-6">
      <p class="text-xl text-gray-600">To jest strona z listą produktów. Będzie zawierać filtry, sortowanie i paginację.</p>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ProductList',
  data() {
    return {
      products: [],
      loading: false,
      currentPage: 1
    }
  },
  mounted() {
    // In a real app, this would fetch products from an API
  }
}
</script> 