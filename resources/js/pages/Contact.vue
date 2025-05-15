<template>
  <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Kontakt</h1>
    
    <div class="my-6">
      <p class="text-xl text-gray-600">To jest strona kontaktowa. Tutaj będzie formularz kontaktowy oraz dane kontaktowe sklepu.</p>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Contact',
  data() {
    return {
      contactData: {
        name: '',
        email: '',
        message: ''
      },
      loading: false
    }
  },
  methods: {
    submitForm() {
      // In a real app, this would send the form data to an API
      alert('Formularz nie jest jeszcze zaimplementowany');
    }
  }
}
</script> 