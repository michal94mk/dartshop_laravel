<template>
  <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">O nas</h1>
    
    <div class="my-6">
      <p class="text-xl text-gray-600">To jest strona o naszym sklepie. Tutaj będziemy opisywać historię sklepu i naszą misję.</p>
    </div>
  </div>
</template>

<script>
export default {
  name: 'About',
  data() {
    return {
      aboutData: null,
      loading: false
    }
  },
  mounted() {
    // In a real app, this would fetch about us data from an API
  }
}
</script> 