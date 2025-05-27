<template>
  <div v-if="hasActivePromotion" class="bg-indigo-600 text-center py-2 px-4 text-white">
    <div class="max-w-7xl mx-auto">
      <p>{{ promotionText }}</p>
    </div>
  </div>
</template>

<script>
export default {
  name: 'PromotionBar',
  data() {
    return {
      hasActivePromotion: true,
      promotionText: 'Darmowa dostawa na zamówienia powyżej 200 zł!',
      hasLink: false,
      linkText: '',
      promotionLink: ''
    }
  },
  mounted() {
    // In a real app, this would fetch active promotions from the backend
    this.fetchActivePromotion();
  },
  methods: {
    fetchActivePromotion() {
      // For now we'll use the static data in data()
      // Later this would be an API call
    }
  }
}
</script> 