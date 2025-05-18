<template>
  <!-- No changes to template section -->
</template>

<script>
import { useAuthStore } from '../../stores/authStore';

export default {
  name: 'AdminLayout',
  setup() {
    const router = useRouter();
    const sidebarOpen = ref(false);
    const authStore = useAuthStore();
    
    console.log('Current user:', authStore.user);
    console.log('Is admin:', authStore.isAdmin);
    console.log('Has role admin:', authStore.hasRole('admin'));
    
    onMounted(() => {
      // Check if the user is logged in and is an administrator
      if (!authStore.isLoggedIn) {
        router.push('/login');
      } else if (!authStore.isAdmin) {
        router.push('/');
      }
    });
  }
}
</script>

<style>
  /* No changes to style section */
</style> 