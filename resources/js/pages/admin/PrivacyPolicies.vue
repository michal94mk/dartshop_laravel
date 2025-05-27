<template>
  <div class="space-y-6">
    <!-- Page Header -->
    <div class="bg-white shadow rounded-lg">
      <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex justify-between items-center">
          <div>
            <h1 class="text-2xl font-bold text-gray-900">Polityki Prywatności</h1>
            <p class="mt-1 text-sm text-gray-500">Zarządzaj politykami prywatności i monitoruj ich akceptację</p>
          </div>
          <button 
            @click="showCreateModal = true" 
            class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-sm font-medium"
          >
            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Nowa Polityka
          </button>
        </div>
      </div>

      <!-- Stats Section -->
      <div class="px-6 py-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div class="bg-blue-50 p-4 rounded-lg">
            <div class="flex items-center">
              <div class="p-2 bg-blue-100 rounded-md">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Całkowici użytkownicy</p>
                <p class="text-2xl font-semibold text-gray-900">{{ stats.total_users }}</p>
              </div>
            </div>
          </div>

          <div class="bg-green-50 p-4 rounded-lg">
            <div class="flex items-center">
              <div class="p-2 bg-green-100 rounded-md">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Zaakceptowali</p>
                <p class="text-2xl font-semibold text-gray-900">{{ stats.accepted_users }}</p>
              </div>
            </div>
          </div>

          <div class="bg-yellow-50 p-4 rounded-lg">
            <div class="flex items-center">
              <div class="p-2 bg-yellow-100 rounded-md">
                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.728-.833-2.498 0L3.356 16.5c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Nie zaakceptowali</p>
                <p class="text-2xl font-semibold text-gray-900">{{ stats.not_accepted_users }}</p>
              </div>
            </div>
          </div>

          <div class="bg-purple-50 p-4 rounded-lg">
            <div class="flex items-center">
              <div class="p-2 bg-purple-100 rounded-md">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Wskaźnik akceptacji</p>
                <p class="text-2xl font-semibold text-gray-900">{{ stats.acceptance_rate }}%</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Policies List -->
    <div class="bg-white shadow rounded-lg">
      <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-medium text-gray-900">Lista Polityk</h2>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="p-6 text-center">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
        <p class="mt-2 text-gray-600">Ładowanie...</p>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="p-6 text-center">
        <div class="text-red-600 mb-2">
          <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.728-.833-2.498 0L3.356 16.5c-.77.833.192 2.5 1.732 2.5z" />
          </svg>
        </div>
        <p class="text-gray-600">{{ error }}</p>
        <button @click="fetchPolicies" class="mt-2 text-indigo-600 hover:text-indigo-800">
          Spróbuj ponownie
        </button>
      </div>

      <!-- Policies Table -->
      <div v-else-if="policies.length > 0" class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tytuł</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Wersja</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data obowiązywania</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data utworzenia</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Akcje</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="policy in policies" :key="policy.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900">{{ policy.title }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-500">{{ policy.version }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span v-if="policy.is_active" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                  Aktywna
                </span>
                <span v-else class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                  Nieaktywna
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ formatDate(policy.effective_date) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ formatDate(policy.created_at) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <div class="flex justify-end space-x-2">
                  <button 
                    @click="viewPolicy(policy)"
                    class="text-indigo-600 hover:text-indigo-900"
                    title="Podgląd"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                  </button>
                  <button 
                    @click="editPolicy(policy)"
                    class="text-blue-600 hover:text-blue-900"
                    title="Edytuj"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                  </button>
                  <button 
                    v-if="!policy.is_active"
                    @click="setAsActive(policy)"
                    class="text-green-600 hover:text-green-900"
                    title="Ustaw jako aktywną"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                  </button>
                  <button 
                    v-if="!policy.is_active"
                    @click="deletePolicy(policy)"
                    class="text-red-600 hover:text-red-900"
                    title="Usuń"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Empty State -->
      <div v-else class="p-6 text-center">
        <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">Brak polityk prywatności</h3>
        <p class="mt-1 text-sm text-gray-500">Rozpocznij od utworzenia pierwszej polityki prywatności.</p>
        <div class="mt-6">
          <button @click="showCreateModal = true" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Nowa Polityka
          </button>
        </div>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <div v-if="showCreateModal || showEditModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full mx-4 max-h-[90vh] overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
          <h3 class="text-lg font-medium text-gray-900">
            {{ showCreateModal ? 'Nowa Polityka Prywatności' : 'Edytuj Politykę Prywatności' }}
          </h3>
        </div>

        <form @submit.prevent="submitForm" class="overflow-y-auto max-h-[calc(90vh-140px)]">
          <div class="px-6 py-4 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700">Tytuł</label>
                <input 
                  v-model="form.title" 
                  type="text" 
                  required
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Wersja</label>
                <input 
                  v-model="form.version" 
                  type="text" 
                  required
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                />
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700">Data obowiązywania</label>
              <input 
                v-model="form.effective_date" 
                type="datetime-local" 
                required
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
              />
            </div>

            <div>
              <label class="flex items-center">
                <input 
                  v-model="form.is_active" 
                  type="checkbox"
                  class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200"
                />
                <span class="ml-2 text-sm text-gray-700">Ustaw jako aktywną politykę</span>
              </label>
              <p class="mt-1 text-xs text-gray-500">Tylko jedna polityka może być aktywna w tym samym czasie</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700">Treść polityki</label>
              <textarea 
                v-model="form.content" 
                rows="20"
                required
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                placeholder="Wprowadź treść polityki prywatności w formacie HTML..."
              ></textarea>
              <p class="mt-1 text-xs text-gray-500">Możesz używać HTML do formatowania treści</p>
            </div>
          </div>

          <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3">
            <button 
              type="button" 
              @click="closeModal"
              class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50"
            >
              Anuluj
            </button>
            <button 
              type="submit"
              :disabled="submitting"
              class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-indigo-700 disabled:opacity-50"
            >
              {{ submitting ? 'Zapisywanie...' : (showCreateModal ? 'Utwórz' : 'Zapisz') }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- View Modal -->
    <div v-if="showViewModal && viewingPolicy" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full mx-4 max-h-[90vh] overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
          <div class="flex justify-between items-center">
            <h3 class="text-lg font-medium text-gray-900">{{ viewingPolicy.title }}</h3>
            <button @click="showViewModal = false" class="text-gray-400 hover:text-gray-600">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
          <div class="mt-2 flex items-center space-x-4 text-sm text-gray-500">
            <span>Wersja {{ viewingPolicy.version }}</span>
            <span>Obowiązuje od {{ formatDate(viewingPolicy.effective_date) }}</span>
            <span v-if="viewingPolicy.is_active" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
              Aktywna
            </span>
          </div>
        </div>

        <div class="overflow-y-auto max-h-[calc(90vh-140px)] px-6 py-4">
          <div class="prose max-w-none" v-html="viewingPolicy.content"></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useToast } from 'vue-toastification'

export default {
  name: 'AdminPrivacyPolicies',
  
  setup() {
    const toast = useToast()
    
    // State
    const loading = ref(true)
    const error = ref(null)
    const policies = ref([])
    const stats = ref({
      total_users: 0,
      accepted_users: 0,
      not_accepted_users: 0,
      acceptance_rate: 0,
      recent_acceptances: 0
    })
    
    // Modals
    const showCreateModal = ref(false)
    const showEditModal = ref(false)
    const showViewModal = ref(false)
    const viewingPolicy = ref(null)
    const editingPolicy = ref(null)
    const submitting = ref(false)
    
    // Form
    const form = ref({
      title: '',
      version: '',
      content: '',
      effective_date: '',
      is_active: false
    })
    
    // Methods
    const fetchPolicies = async () => {
      try {
        loading.value = true
        error.value = null
        
        const response = await axios.get('/api/admin/privacy-policies')
        policies.value = response.data.privacy_policies || []
      } catch (err) {
        error.value = 'Nie udało się załadować polityk prywatności'
        console.error('Error fetching policies:', err)
      } finally {
        loading.value = false
      }
    }
    
    const fetchStats = async () => {
      try {
        const response = await axios.get('/api/admin/privacy-policies/stats/acceptance')
        stats.value = response.data
      } catch (err) {
        console.error('Error fetching stats:', err)
      }
    }
    
    const resetForm = () => {
      form.value = {
        title: '',
        version: '',
        content: '',
        effective_date: '',
        is_active: false
      }
    }
    
    const closeModal = () => {
      showCreateModal.value = false
      showEditModal.value = false
      editingPolicy.value = null
      resetForm()
    }
    
    const editPolicy = (policy) => {
      editingPolicy.value = policy
      form.value = {
        title: policy.title,
        version: policy.version,
        content: policy.content,
        effective_date: policy.effective_date.substring(0, 16), // Format for datetime-local
        is_active: policy.is_active
      }
      showEditModal.value = true
    }
    
    const viewPolicy = (policy) => {
      viewingPolicy.value = policy
      showViewModal.value = true
    }
    
    const submitForm = async () => {
      try {
        submitting.value = true
        
        if (showCreateModal.value) {
          await axios.post('/api/admin/privacy-policies', form.value)
          toast.success('Polityka prywatności została utworzona')
        } else {
          await axios.put(`/api/admin/privacy-policies/${editingPolicy.value.id}`, form.value)
          toast.success('Polityka prywatności została zaktualizowana')
        }
        
        closeModal()
        await fetchPolicies()
        await fetchStats()
      } catch (err) {
        toast.error('Wystąpił błąd podczas zapisywania')
        console.error('Error submitting form:', err)
      } finally {
        submitting.value = false
      }
    }
    
    const setAsActive = async (policy) => {
      try {
        await axios.post(`/api/admin/privacy-policies/${policy.id}/set-active`)
        toast.success('Polityka została ustawiona jako aktywna')
        await fetchPolicies()
      } catch (err) {
        toast.error('Nie udało się ustawić polityki jako aktywnej')
        console.error('Error setting policy as active:', err)
      }
    }
    
    const deletePolicy = async (policy) => {
      if (!confirm('Czy na pewno chcesz usunąć tę politykę?')) {
        return
      }
      
      try {
        await axios.delete(`/api/admin/privacy-policies/${policy.id}`)
        toast.success('Polityka została usunięta')
        await fetchPolicies()
        await fetchStats()
      } catch (err) {
        toast.error('Nie udało się usunąć polityki')
        console.error('Error deleting policy:', err)
      }
    }
    
    const formatDate = (dateString) => {
      if (!dateString) return ''
      const date = new Date(dateString)
      return date.toLocaleDateString('pl-PL', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      })
    }
    
    // Initialize
    onMounted(async () => {
      await Promise.all([fetchPolicies(), fetchStats()])
    })
    
    return {
      loading,
      error,
      policies,
      stats,
      showCreateModal,
      showEditModal,
      showViewModal,
      viewingPolicy,
      editingPolicy,
      submitting,
      form,
      fetchPolicies,
      resetForm,
      closeModal,
      editPolicy,
      viewPolicy,
      submitForm,
      setAsActive,
      deletePolicy,
      formatDate
    }
  }
}
</script>

<style scoped>
:deep(.prose) {
  max-width: none;
}

:deep(.prose h2) {
  @apply text-xl font-semibold text-gray-900 mt-6 mb-3;
}

:deep(.prose h3) {
  @apply text-lg font-medium text-gray-800 mt-4 mb-2;
}

:deep(.prose p) {
  @apply text-gray-700 mb-3;
}

:deep(.prose ul) {
  @apply list-disc list-inside mb-3 text-gray-700;
}

:deep(.prose li) {
  @apply mb-1;
}

:deep(.prose strong) {
  @apply font-semibold text-gray-900;
}
</style> 