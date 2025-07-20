<template>
  <div class="space-y-6 bg-white min-h-full">
    <!-- Page Header -->
    <div class="px-6 py-4">
      <page-header 
        title="Newsletter"
        :show-add-button="false"
      />
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="h-6 w-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Aktywne subskrypcje</dt>
                <dd class="text-lg font-medium text-gray-900">{{ stats.active || 0 }}</dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="h-6 w-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Oczekujące weryfikacji</dt>
                <dd class="text-lg font-medium text-gray-900">{{ stats.pending || 0 }}</dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"></path>
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Wypisane</dt>
                <dd class="text-lg font-medium text-gray-900">{{ stats.unsubscribed || 0 }}</dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="h-6 w-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Łącznie</dt>
                <dd class="text-lg font-medium text-gray-900">{{ stats.total || 0 }}</dd>
              </dl>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Search and filters -->
    <div>
      <search-filters
        v-if="!loading"
        :filters="filters"
        :sort-options="sortOptions"
        search-label="Wyszukaj email"
        search-placeholder="Wpisz adres email..."
        @update:filters="(newFilters) => { Object.assign(filters, newFilters); filters.page = 1; }"
        @filter-change="loadSubscriptions"
      >
      <template v-slot:filters>
        <div class="w-full sm:w-auto">
          <label for="status-filter" class="block text-sm font-medium text-gray-700">Status</label>
          <select
            id="status-filter"
            v-model="filters.status"
            @change="() => { filters.page = 1; loadSubscriptions(); }"
            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
          >
            <option value="">Wszystkie</option>
            <option value="active">Aktywne</option>
            <option value="pending">Oczekujące</option>
            <option value="unsubscribed">Wypisane</option>
          </select>
        </div>

        <div class="w-full sm:w-auto">
          <label for="date-from" class="block text-sm font-medium text-gray-700">Data od</label>
          <input
            id="date-from"
            v-model="filters.date_from"
            @change="() => { filters.page = 1; loadSubscriptions(); }"
            type="date"
            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
          />
        </div>

        <div class="w-full sm:w-auto">
          <label for="date-to" class="block text-sm font-medium text-gray-700">Data do</label>
          <input
            id="date-to"
            v-model="filters.date_to"
            @change="() => { filters.page = 1; loadSubscriptions(); }"
            type="date"
            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
          />
        </div>

        <div class="w-full flex justify-end">
          <div class="flex items-end">
            <button
              @click="resetFilters"
              type="button"
              class="inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              Resetuj filtry
            </button>
          </div>
        </div>
      </template>
      </search-filters>
    </div>

    <!-- Loading indicator -->
    <div>
      <loading-spinner v-if="loading" />
    </div>
    
    <!-- Newsletter subscriptions list -->
    <div>
      <admin-table
        v-if="subscriptions.length > 0"
        :columns="tableColumns"
        :items="subscriptions"
        class="mt-8"
      >
      <template #cell-email="{ item }">
        <div class="font-medium text-gray-900">{{ item.email }}</div>
      </template>
      
      <template #cell-status="{ item }">
        <span :class="getStatusBadgeClass(item.status)" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full">
          {{ getStatusText(item.status) }}
        </span>
      </template>
      
       <template #cell-verified_at="{ item }">
         {{ item.verified_at ? formatDate(item.verified_at) : '-' }}
       </template>
       
              <template #cell-actions="{ item }">
         <admin-button
           @click="deleteSubscription(item)"
           variant="danger"
           size="sm"
         >
           Usuń
                  </admin-button>
       </template>
     </admin-table>
     
     <!-- Pagination -->
     <pagination 
       v-if="subscriptions.length > 0 && pagination.last_page > 1"
       :pagination="{ ...pagination, data: subscriptions }" 
       items-label="subskrypcji" 
       @page-change="goToPage" 
     />
     
     <!-- No data message -->
     <no-data-message v-if="!loading && subscriptions.length === 0" message="Brak subskrypcji newslettera" />
    </div>
  </div>
</template>

<script>
import { ref, reactive, onMounted, watch } from 'vue';
import api from '../../services/api';
import SearchFilters from '../../components/admin/SearchFilters.vue';
import AdminTable from '../../components/admin/ui/AdminTable.vue';
import AdminButtonGroup from '../../components/admin/ui/AdminButtonGroup.vue';
import AdminButton from '../../components/admin/ui/AdminButton.vue';
import LoadingSpinner from '../../components/admin/LoadingSpinner.vue';
import NoDataMessage from '../../components/admin/NoDataMessage.vue';
import Pagination from '../../components/admin/Pagination.vue';
import PageHeader from '../../components/admin/PageHeader.vue';
import { useAuthStore } from '../../stores/authStore';
import { useRoute } from 'vue-router';

export default {
  name: 'AdminNewsletter',
  components: {
    SearchFilters,
    AdminTable,
    AdminButtonGroup,
    AdminButton,
    LoadingSpinner,
    NoDataMessage,
    Pagination,
    PageHeader
  },
  setup() {
    const loading = ref(false);
    const subscriptions = ref([]);
    const stats = reactive({
      active: 0,
      pending: 0,
      unsubscribed: 0
    });
    
    // Sort options for the filter component
    const sortOptions = [
      { value: 'created_at', label: 'Data subskrypcji' },
      { value: 'email', label: 'Email' },
      { value: 'status', label: 'Status' },
      { value: 'verified_at', label: 'Data weryfikacji' },
      { value: 'unsubscribed_at', label: 'Data wypisania' }
    ];
    
    // Table columns definition
    const tableColumns = [
      { key: 'email', label: 'Email', width: '340px' },
      { key: 'status', label: 'Status', width: '100px' },
      { key: 'created_at', label: 'Data subskrypcji', type: 'datetime', width: '140px' },
      { key: 'verified_at', label: 'Data weryfikacji', width: '140px' },
      { key: 'actions', label: 'Akcje', align: 'right', width: '90px' }
    ];
    
    const filters = reactive({
      search: '',
      status: '',
      date_from: '',
      date_to: '',
      sort_field: 'created_at',
      sort_direction: 'desc',
      page: 1
    });
    
    const pagination = reactive({
      current_page: 1,
      last_page: 1,
      per_page: 10,
      total: 0,
      from: 0,
      to: 0
    });

    const loadSubscriptions = async (page = 1) => {
      loading.value = true;
      try {
        console.log('Loading newsletter subscriptions...');
        const params = {
          page,
          per_page: pagination.per_page,
          ...filters
        };
        
        console.log('Request params:', params);
        // Try using the dedicated method first
        console.log('Trying dedicated API method...');
        const response = await api.getAdminNewsletter(params);
        console.log('Newsletter API response:', response.data);
        
        subscriptions.value = response.data.data || [];
        Object.assign(pagination, response.data.pagination || {});
        Object.assign(stats, response.data.stats || {});
        
        console.log('Subscriptions loaded:', subscriptions.value.length);
        console.log('Stats:', stats);
      } catch (error) {
        console.error('Error loading newsletter subscriptions:', error);
        console.error('Error details:', {
          message: error.message,
          response: error.response?.data,
          status: error.response?.status,
          url: error.config?.url
        });
        
        // Don't show error if user is logging out or not on admin page
        if (error.message === 'Unauthorized admin request blocked') {
          console.log('Admin request blocked - user likely logging out or not authorized');
          return;
        }
        
        // Don't show error if we're not on admin page (user was redirected)
        const currentPath = window.location.pathname;
        if (!currentPath.startsWith('/admin')) {
          console.log('Not on admin page, skipping error display');
          return;
        }
        
        // If unauthorized, try alternative approach
        if (error.response?.status === 401) {
          console.error('Unauthorized - user may not be logged in as admin');
          alert('Błąd autoryzacji - sprawdź czy jesteś zalogowany jako administrator');
        } else if (error.response?.status === 403) {
          console.error('Forbidden - user may not have admin role');
          alert('Brak uprawnień administratora');
        } else if (error.response?.status === 404) {
          console.error('Newsletter endpoint not found');
          alert('Endpoint newslettera nie został znaleziony');
        }
      } finally {
        loading.value = false;
      }
    };

    const resetFilters = () => {
      filters.search = '';
      filters.status = '';
      filters.date_from = '';
      filters.date_to = '';
      filters.sort_field = 'created_at';
      filters.sort_direction = 'desc';
      filters.page = 1;
      loadSubscriptions(1);
    };

    const goToPage = (page) => {
      if (page === '...') return;
      
      filters.page = page;
      loadSubscriptions(page);
    };

    const getStatusBadgeClass = (status) => {
      const classes = {
        active: 'bg-green-100 text-green-800',
        pending: 'bg-yellow-100 text-yellow-800',
        unsubscribed: 'bg-gray-100 text-gray-800'
      };
      return classes[status] || 'bg-gray-100 text-gray-800';
    };

    const getStatusText = (status) => {
      const texts = {
        active: 'Aktywna',
        pending: 'Oczekująca',
        unsubscribed: 'Wypisana'
      };
      return texts[status] || status;
    };

    const formatDate = (dateString) => {
      if (!dateString) return '-';
      return new Date(dateString).toLocaleDateString('pl-PL', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
      });
    };

    const deleteSubscription = async (subscription) => {
      if (!confirm(`Czy na pewno chcesz usunąć subskrypcję ${subscription.email}?`)) {
        return;
      }

      try {
        await api.deleteNewsletterSubscription(subscription.id);
        await loadSubscriptions(pagination.current_page);
      } catch (error) {
        console.error('Error deleting subscription:', error);
        alert('Wystąpił błąd podczas usuwania subskrypcji');
      }
    };

    const authStore = useAuthStore();
    const route = useRoute();

    onMounted(async () => {
      // Check if user is logged in and is admin before fetching data
      if (!authStore.isLoggedIn || !authStore.isAdmin) {
        console.log('User not logged in or not admin, skipping data fetch');
        return;
      }
      
      await loadSubscriptions()
    });

    // Watch for auth state changes (logout)
    watch(() => authStore.isLoggedIn, (newValue) => {
      console.log('Newsletter: Auth state changed, isLoggedIn:', newValue)
      if (!newValue) {
        console.log('Newsletter: User logged out, clearing data')
        loading.value = false
        // Clear data when user logs out
        subscriptions.value = []
        pagination.value = {
          current_page: 1,
          last_page: 1,
          per_page: 10,
          total: 0
        }
        stats.value = {
          total_subscriptions: 0,
          verified_subscriptions: 0,
          unverified_subscriptions: 0
        }
      }
    })
    
    // Watch for route changes to prevent data fetching when not on admin page
    watch(() => route.path, (newPath) => {
      console.log('Newsletter: Route changed to:', newPath)
      if (!newPath.startsWith('/admin')) {
        console.log('Newsletter: Not on admin page, stopping data fetch')
        loading.value = false
      }
    })
    
    // Lifecycle

    return {
      loading,
      subscriptions,
      stats,
      sortOptions,
      tableColumns,
      filters,
      pagination,
      loadSubscriptions,
      resetFilters,
      goToPage,
      getStatusBadgeClass,
      getStatusText,
      formatDate,
      deleteSubscription
    };
  }
};
</script> 