<template>
  <div class="space-y-6">
    <!-- Page Header -->
    <div class="bg-white shadow rounded-lg">
      <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex justify-between items-center">
          <div>
            <h1 class="text-2xl font-bold text-gray-900">Regulaminy</h1>
            <p class="mt-1 text-sm text-gray-500">Zarządzaj regulaminami sklepu i monitoruj ich akceptację</p>
          </div>
          <admin-button 
            @click="showCreateModal = true" 
            variant="primary"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Nowy Regulamin
          </admin-button>
        </div>
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
    </div>

    <!-- Terms List -->
    <div class="bg-white shadow rounded-lg">
      <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-medium text-gray-900">Lista regulaminów</h2>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="flex justify-center items-center py-12">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
      </div>

      <!-- No terms message -->
      <div v-else-if="terms.length === 0" class="text-center py-12">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">Brak regulaminów</h3>
        <p class="mt-1 text-sm text-gray-500">Zacznij od utworzenia pierwszego regulaminu.</p>
        <div class="mt-6">
          <admin-button @click="showCreateModal = true" variant="primary">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Nowy Regulamin
          </admin-button>
        </div>
      </div>

      <!-- Terms Table -->
      <div v-else class="overflow-x-auto">
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
            <tr v-for="term in terms" :key="term.id">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                {{ term.title }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ term.version }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span v-if="term.is_active" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                  Aktywny
                </span>
                <span v-else class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                  Nieaktywny
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ formatDate(term.effective_date) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ formatDate(term.created_at) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <div class="flex gap-1 justify-end min-w-[200px]">
                  <admin-button 
                    @click="viewTerm(term)"
                    variant="warning"
                    size="sm"
                  >
                    Szczegóły
                  </admin-button>
                  <admin-button 
                    @click="editTerm(term)"
                    variant="warning"
                    size="sm"
                  >
                    Edytuj
                  </admin-button>
                  <admin-button 
                    v-if="!term.is_active"
                    @click="setAsActive(term)"
                    variant="primary"
                    size="sm"
                  >
                    Aktywuj
                  </admin-button>
                  <admin-button 
                    v-if="!term.is_active"
                    @click="confirmDelete(term)"
                    variant="danger"
                    size="sm"
                  >
                    Usuń
                  </admin-button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <div v-if="showCreateModal || showEditModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full mx-4 max-h-[90vh] overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
          <h3 class="text-lg font-medium text-gray-900">
            {{ showCreateModal ? 'Nowy Regulamin' : 'Edytuj Regulamin' }}
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
                <span class="ml-2 text-sm text-gray-700">Ustaw jako aktywny regulamin</span>
              </label>
              <p class="mt-1 text-xs text-gray-500">Tylko jeden regulamin może być aktywny w tym samym czasie</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700">Treść regulaminu</label>
              <textarea 
                v-model="form.content" 
                rows="20"
                required
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                placeholder="Wprowadź treść regulaminu w formacie HTML..."
              ></textarea>
              <p class="mt-1 text-xs text-gray-500">Możesz używać HTML do formatowania treści</p>
            </div>
          </div>

          <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3">
            <admin-button 
              type="button" 
              @click="closeModal"
              variant="secondary"
              outline
            >
              Anuluj
            </admin-button>
            <admin-button 
              type="submit"
              :loading="submitting"
              variant="primary"
            >
              {{ showCreateModal ? 'Utwórz' : 'Zapisz' }}
            </admin-button>
          </div>
        </form>
      </div>
    </div>

    <!-- View Modal -->
    <div v-if="showViewModal && viewingTerm" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full mx-4 max-h-[90vh] overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
          <div class="flex justify-between items-center">
            <h3 class="text-lg font-medium text-gray-900">{{ viewingTerm.title }}</h3>
            <admin-button @click="showViewModal = false" variant="secondary" size="sm">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </admin-button>
          </div>
          <div class="mt-2 flex items-center space-x-4 text-sm text-gray-500">
            <span>Wersja {{ viewingTerm.version }}</span>
            <span>Obowiązuje od {{ formatDate(viewingTerm.effective_date) }}</span>
            <span v-if="viewingTerm.is_active" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
              Aktywny
            </span>
          </div>
        </div>

        <div class="overflow-y-auto max-h-[calc(90vh-140px)] px-6 py-4">
          <div class="prose max-w-none" v-html="viewingTerm.content"></div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="px-6 py-4">
          <div class="flex items-center">
            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
              <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.664-.833-2.464 0L5.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
              </svg>
            </div>
            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
              <h3 class="text-lg leading-6 font-medium text-gray-900">Usuń regulamin</h3>
              <div class="mt-2">
                <p class="text-sm text-gray-500">
                  Czy na pewno chcesz usunąć regulamin "{{ termToDelete?.title }}"? Ta akcja jest nieodwracalna.
                </p>
              </div>
            </div>
          </div>
        </div>
        <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3">
          <admin-button @click="showDeleteModal = false" variant="secondary" outline>
            Anuluj
          </admin-button>
          <admin-button @click="deleteTerm" variant="danger">
            Usuń
          </admin-button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useAlertStore } from '../../stores/alertStore'
import AdminButton from '../../components/admin/ui/AdminButton.vue'

export default {
  name: 'AdminTermsOfService',
  components: {
    AdminButton
  },
  
  setup() {
    const alertStore = useAlertStore()
    
    // State
    const loading = ref(true)
    const error = ref(null)
    const terms = ref([])
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
    const viewingTerm = ref(null)
    const editingTerm = ref(null)
    const termToDelete = ref(null)
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
    const fetchTerms = async () => {
      try {
        loading.value = true
        error.value = null
        
        const response = await axios.get('/api/admin/terms-of-service')
        terms.value = response.data.terms_of_service || []
      } catch (err) {
        error.value = 'Nie udało się załadować regulaminów'
        alertStore.error('Nie udało się załadować regulaminów')
        console.error('Error fetching terms:', err)
      } finally {
        loading.value = false
      }
    }
    
    const fetchStats = async () => {
      try {
        const response = await axios.get('/api/admin/terms-of-service/stats/acceptance')
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
      editingTerm.value = null
      resetForm()
    }
    
    const editTerm = (term) => {
      editingTerm.value = term
      form.value = {
        title: term.title,
        version: term.version,
        content: term.content,
        effective_date: term.effective_date.substring(0, 16), // Format for datetime-local
        is_active: term.is_active
      }
      showEditModal.value = true
    }
    
    const viewTerm = (term) => {
      viewingTerm.value = term
      showViewModal.value = true
    }
    
    const confirmDelete = (term) => {
      termToDelete.value = term
      showDeleteModal.value = true
    }
    
    const deleteTerm = async () => {
      try {
        await axios.delete(`/api/admin/terms-of-service/${termToDelete.value.id}`)
        alertStore.success('Regulamin został usunięty')
        showDeleteModal.value = false
        termToDelete.value = null
        await fetchTerms()
        await fetchStats()
      } catch (err) {
        alertStore.error('Nie udało się usunąć regulaminu')
        console.error('Error deleting term:', err)
      }
    }
    
    const submitForm = async () => {
      try {
        submitting.value = true
        
        if (showCreateModal.value) {
          await axios.post('/api/admin/terms-of-service', form.value)
          alertStore.success('Regulamin został utworzony')
        } else {
          await axios.put(`/api/admin/terms-of-service/${editingTerm.value.id}`, form.value)
          alertStore.success('Regulamin został zaktualizowany')
        }
        
        closeModal()
        await fetchTerms()
        await fetchStats()
      } catch (err) {
        alertStore.error('Wystąpił błąd podczas zapisywania')
        console.error('Error submitting form:', err)
      } finally {
        submitting.value = false
      }
    }
    
    const setAsActive = async (term) => {
      try {
        await axios.post(`/api/admin/terms-of-service/${term.id}/set-active`)
        alertStore.success('Regulamin został ustawiony jako aktywny')
        await fetchTerms()
      } catch (err) {
        alertStore.error('Nie udało się ustawić regulaminu jako aktywnego')
        console.error('Error setting term as active:', err)
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
      await Promise.all([fetchTerms(), fetchStats()])
    })
    
    return {
      loading,
      error,
      terms,
      stats,
      showCreateModal,
      showEditModal,
      showViewModal,
      showDeleteModal,
      viewingTerm,
      editingTerm,
      termToDelete,
      submitting,
      form,
      fetchTerms,
      fetchStats,
      resetForm,
      closeModal,
      editTerm,
      viewTerm,
      confirmDelete,
      deleteTerm,
      submitForm,
      setAsActive,
      formatDate
    }
  }
}
</script> 