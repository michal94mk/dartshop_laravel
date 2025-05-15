<template>
  <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Promocje</h1>
    
    <div class="my-6">
      <p class="text-xl text-gray-600">To jest strona z promocjami. Tutaj będą wyświetlane aktualne promocje w sklepie.</p>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Promotions',
  data() {
    return {
      promotions: [],
      loading: false
    }
  },
  mounted() {
    // In a real app, this would fetch promotions data from an API
  }
}
</script> 