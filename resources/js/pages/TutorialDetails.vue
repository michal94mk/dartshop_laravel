<template>
  <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Szczegóły poradnika</h1>
    
    <div class="my-6">
      <p class="text-xl text-gray-600">To jest strona ze szczegółami poradnika. Numer poradnika: {{ $route.params.id }}</p>
    </div>
  </div>
</template>

<script>
export default {
  name: 'TutorialDetails',
  props: {
    id: {
      type: String,
      required: true
    }
  },
  data() {
    return {
      tutorial: null,
      loading: false
    }
  },
  mounted() {
    // In a real app, this would fetch tutorial details from an API
  }
}
</script> 