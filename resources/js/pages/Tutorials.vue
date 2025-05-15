<template>
  <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Poradniki</h1>
    
    <div class="my-6">
      <p class="text-xl text-gray-600">To jest strona z listą poradników. Tutaj będą wyświetlane wszystkie dostępne poradniki.</p>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Tutorials',
  data() {
    return {
      tutorials: [],
      loading: false
    }
  },
  mounted() {
    // In a real app, this would fetch tutorials data from an API
  }
}
</script> 