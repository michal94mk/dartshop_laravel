<template>
  <div class="bg-white shadow overflow-hidden sm:rounded-md p-6 text-center text-gray-500 mt-6">
    {{ message }}
  </div>
</template>

<script>
export default {
  name: 'NoDataMessage',
  props: {
    message: {
      type: String,
      default: 'Brak danych do wyświetlenia'
    }
  }
}
</script> 