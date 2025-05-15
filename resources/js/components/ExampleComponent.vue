<template>
  <div class="bg-white shadow rounded-lg p-6 mb-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">{{ title }}</h2>
    <p class="text-gray-600 mb-4">{{ message }}</p>
    <button 
      @click="count++"
      class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
    >
      You clicked me {{ count }} times
    </button>
  </div>
</template>

<script>
export default {
  name: 'ExampleComponent',
  data() {
    return {
      title: 'Example Vue Component',
      message: 'This component demonstrates Vue.js integration with Laravel and Tailwind CSS',
      count: 0
    }
  }
}
</script>

<style scoped>
/* Additional styles can be added here */
</style> 