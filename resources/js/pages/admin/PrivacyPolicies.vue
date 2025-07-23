<template>
  <div class="space-y-6 bg-white min-h-full">
    <!-- Page Header -->
    <div class="px-6 py-4">
      <page-header 
        title="Polityki Prywatności"
        add-button-label="Dodaj"
        @add="showCreateModal = true"
      />
    </div>

    <!-- Stats Cards -->
    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg p-4 shadow-sm">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="h-6 w-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM9 9a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
            </div>
            <div class="ml-3">
              <p class="text-sm font-medium text-gray-500">Wszyscy użytkownicy</p>
              <p class="text-lg font-semibold text-gray-900">{{ stats.total_users }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg p-4 shadow-sm">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="h-6 w-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div class="ml-3">
              <p class="text-sm font-medium text-gray-500">Zaakceptowali</p>
              <p class="text-lg font-semibold text-gray-900">{{ stats.accepted_users }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg p-4 shadow-sm">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="h-6 w-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.664-.833-2.464 0L5.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
              </svg>
            </div>
            <div class="ml-3">
              <p class="text-sm font-medium text-gray-500">Nie zaakceptowali</p>
              <p class="text-lg font-semibold text-gray-900">{{ stats.not_accepted_users }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg p-4 shadow-sm">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="h-6 w-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
              </svg>
            </div>
            <div class="ml-3">
              <p class="text-sm font-medium text-gray-500">Procent akceptacji</p>
              <p class="text-lg font-semibold text-gray-900">{{ stats.acceptance_rate }}%</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Policies List -->
    <div class="border-t border-gray-200">
      <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-medium text-gray-900">Lista polityk prywatności</h2>
      </div>

      <!-- Loading -->
      <loading-spinner v-if="loading" />

      <!-- No policies message -->
      <div v-else-if="policies.length === 0" class="text-center py-12">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">Brak polityk prywatności</h3>
        <p class="mt-1 text-sm text-gray-500">Zacznij od utworzenia pierwszej polityki prywatności.</p>
        <div class="mt-6">
          <admin-button @click="showCreateModal = true" variant="primary">
            Dodaj
          </admin-button>
        </div>
      </div>

      <!-- Policies Table -->
                      <div v-else class="overflow-x-auto -mx-6 px-6" style="scrollbar-width: thin; scrollbar-color: #d1d5db #f3f4f6;">
          <table class="min-w-full divide-y divide-gray-200 whitespace-nowrap">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tytuł</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data utworzenia</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Akcje</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="policy in policies" :key="policy.id">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                {{ policy.title }}
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
                {{ formatDate(policy.created_at) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <action-buttons 
                  :item="policy" 
                  :show-details="true"
                  @details="viewPolicy"
                  @edit="editPolicy"
                  @delete="confirmDelete"
                  :show-delete="!policy.is_active"
                  justify="end"
                >
                  <template #status-buttons="{ item }">
                    <admin-button 
                      v-if="!item.is_active"
                      @click="setAsActive(item)"
                      variant="primary"
                      size="sm"
                    >
                      Aktywuj
                    </admin-button>
                  </template>
                </action-buttons>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <AdminModal
      :show="showCreateModal || showEditModal"
      :title="showCreateModal ? 'Nowa polityka prywatności' : 'Edytuj politykę prywatności'"
      size="4xl"
      @close="closeModal"
    >
      <form @submit.prevent="submitForm" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700">Tytuł</label>
          <input 
            v-model="form.title" 
            type="text" 
            required
            :class="[
              'mt-1 block w-full rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm',
              formErrors.title 
                ? 'border-red-300 focus:border-red-500 focus:ring-red-500' 
                : 'border-gray-300'
            ]"
          />
          <p v-if="formErrors.title" class="mt-1 text-sm text-red-600">{{ Array.isArray(formErrors.title) ? formErrors.title[0] : formErrors.title }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700">Wersja</label>
            <input 
              v-model="form.version" 
              type="text" 
              required
              placeholder="np. 1.0"
              :class="[
                'mt-1 block w-full rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm',
                formErrors.version 
                  ? 'border-red-300 focus:border-red-500 focus:ring-red-500' 
                  : 'border-gray-300'
              ]"
            />
            <p v-if="formErrors.version" class="mt-1 text-sm text-red-600">{{ Array.isArray(formErrors.version) ? formErrors.version[0] : formErrors.version }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Data obowiązywania</label>
            <input 
              v-model="form.effective_date" 
              type="date" 
              required
              :class="[
                'mt-1 block w-full rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm',
                formErrors.effective_date 
                  ? 'border-red-300 focus:border-red-500 focus:ring-red-500' 
                  : 'border-gray-300'
              ]"
            />
            <p v-if="formErrors.effective_date" class="mt-1 text-sm text-red-600">{{ Array.isArray(formErrors.effective_date) ? formErrors.effective_date[0] : formErrors.effective_date }}</p>
          </div>
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
            :class="[
              'mt-1 block w-full rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm',
              formErrors.content 
                ? 'border-red-300 focus:border-red-500 focus:ring-red-500' 
                : 'border-gray-300'
            ]"
            placeholder="Wprowadź treść polityki prywatności w formacie HTML..."
          ></textarea>
          <p v-if="formErrors.content" class="mt-1 text-sm text-red-600">{{ Array.isArray(formErrors.content) ? formErrors.content[0] : formErrors.content }}</p>
          <p class="mt-1 text-xs text-gray-500">Możesz używać HTML do formatowania treści</p>
        </div>
      </form>

      <template #footer>
        <AdminButtonGroup justify="end">
          <AdminButton 
            type="button" 
            @click="closeModal"
            variant="secondary"
            outline
          >
            Anuluj
          </AdminButton>
          <AdminButton 
            type="button"
            @click="submitForm"
            :loading="submitting"
            variant="primary"
          >
            {{ showCreateModal ? 'Utwórz' : 'Zapisz' }}
          </AdminButton>
        </AdminButtonGroup>
      </template>
    </AdminModal>

    <!-- View Modal -->
    <AdminModal
      :show="showViewModal && viewingPolicy"
      :title="viewingPolicy?.title"
      size="4xl"
      @close="showViewModal = false"
    >
      <div v-if="viewingPolicy" class="space-y-4">
        <div class="flex items-center space-x-4 text-sm text-gray-500 pb-4 border-b">
          <span v-if="viewingPolicy.is_active" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
            Aktywna
          </span>
          <span v-else class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
            Nieaktywna
          </span>
        </div>
        
        <div class="prose max-w-none" v-html="viewingPolicy.content"></div>
      </div>
    </AdminModal>

    <!-- Delete Confirmation Modal -->
    <admin-modal
      :show="showDeleteModal"
      title="Potwierdź usunięcie"
      size="md"
      icon-variant="danger"
      @close="showDeleteModal = false"
    >
      <div class="text-center">
        <p class="text-sm text-gray-500">
          Czy na pewno chcesz usunąć politykę prywatności "{{ policyToDelete?.title }}"?
          <span v-if="policyToDelete?.is_active" class="mt-2 block font-semibold text-red-600">
            Uwaga: Ta polityka prywatności jest aktualnie aktywna.
          </span>
        </p>
      </div>
      
      <template #footer>
        <admin-button-group justify="center" spacing="sm">
          <admin-button 
            @click="showDeleteModal = false" 
            variant="secondary"
            outline
          >
            Anuluj
          </admin-button>
          <admin-button 
            @click="deletePolicy" 
            variant="danger"
          >
            Usuń
          </admin-button>
        </admin-button-group>
      </template>
    </admin-modal>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useAlertStore } from '../../stores/alertStore'
import AdminButton from '../../components/admin/ui/AdminButton.vue'
import AdminButtonGroup from '../../components/admin/ui/AdminButtonGroup.vue'
import AdminModal from '../../components/admin/ui/AdminModal.vue'
import ActionButtons from '../../components/admin/ActionButtons.vue'
import LoadingSpinner from '../../components/admin/LoadingSpinner.vue'
import PageHeader from '../../components/admin/PageHeader.vue'

export default {
  name: 'AdminPrivacyPolicies',
  components: {
    AdminButton,
    AdminButtonGroup,
    AdminModal,
    ActionButtons,
    LoadingSpinner,
    PageHeader
  },
  
  setup() {
    const alertStore = useAlertStore()
    
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
    const showDeleteModal = ref(false)
    const viewingPolicy = ref(null)
    const editingPolicy = ref(null)
    const policyToDelete = ref(null)
    const submitting = ref(false)
    const formErrors = ref({})
    
    // Form
    const form = ref({
      title: '',
      version: '',
      effective_date: '',
      content: '',
      is_active: false
    })
    
    // Methods
    const fetchPolicies = async () => {
      try {
        loading.value = true
        error.value = null
        
        const response = await axios.get('/api/admin/privacy-policies')
        policies.value = response.data.data || []
      } catch (err) {
        error.value = 'Nie udało się załadować polityk prywatności'
        alertStore.error('Nie udało się załadować polityk prywatności')
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
        effective_date: '',
        content: '',
        is_active: false
      }
      formErrors.value = {}
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
        effective_date: policy.effective_date,
        content: policy.content,
        is_active: policy.is_active
      }
      showEditModal.value = true
    }
    
    const viewPolicy = (policy) => {
      viewingPolicy.value = policy
      showViewModal.value = true
    }
    
    const confirmDelete = (policy) => {
      policyToDelete.value = policy
      showDeleteModal.value = true
    }
    
    const deletePolicy = async () => {
      try {
        await axios.delete(`/api/admin/privacy-policies/${policyToDelete.value.id}`)
        alertStore.success('Polityka prywatności została usunięta')
        showDeleteModal.value = false
        policyToDelete.value = null
        await fetchPolicies()
        await fetchStats()
      } catch (err) {
        alertStore.error('Nie udało się usunąć polityki prywatności')
        console.error('Error deleting policy:', err)
      }
    }
    
    const submitForm = async () => {
      try {
        submitting.value = true
        formErrors.value = {}
        
        if (showCreateModal.value) {
          const response = await axios.post('/api/admin/privacy-policies', form.value)
          alertStore.success(response.data.message || 'Polityka prywatności została utworzona')
        } else {
          const response = await axios.put(`/api/admin/privacy-policies/${editingPolicy.value.id}`, form.value)
          alertStore.success(response.data.message || 'Polityka prywatności została zaktualizowana')
        }
        
        closeModal()
        await fetchPolicies()
        await fetchStats()
      } catch (err) {
        console.error('Error submitting form:', err)
        
        if (err.response && err.response.status === 422) {
          if (err.response.data.errors) {
            formErrors.value = err.response.data.errors
          } else if (err.response.data.message) {
            alertStore.error(err.response.data.message)
          }
        } else {
          alertStore.error('Wystąpił błąd podczas zapisywania')
        }
      } finally {
        submitting.value = false
      }
    }
    
    const setAsActive = async (policy) => {
      try {
        await axios.post(`/api/admin/privacy-policies/${policy.id}/set-active`)
        alertStore.success('Polityka została ustawiona jako aktywna')
        await fetchPolicies()
      } catch (err) {
        alertStore.error('Nie udało się ustawić polityki jako aktywnej')
        console.error('Error setting policy as active:', err)
      }
    }
    
    const formatDate = (dateString) => {
      const date = new Date(dateString)
      return date.toLocaleDateString('pl-PL', {
        year: 'numeric',
        month: 'long',
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
      showDeleteModal,
      viewingPolicy,
      editingPolicy,
      policyToDelete,
      submitting,
      form,
      formErrors,
      fetchPolicies,
      fetchStats,
      resetForm,
      closeModal,
      editPolicy,
      viewPolicy,
      confirmDelete,
      deletePolicy,
      submitForm,
      setAsActive,
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