<template>
  <div class="space-y-6">
    <!-- Page Header -->
    <div class="bg-white shadow rounded-lg">
      <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex justify-between items-center">
          <div>
            <h1 class="text-2xl font-bold text-gray-900">Regulaminy</h1>
            <p class="mt-1 text-sm text-gray-500">Zarządzaj regulaminami i monitoruj ich akceptację</p>
          </div>
          <button 
            @click="showCreateModal = true" 
            class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-sm font-medium"
          >
            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Nowy Regulamin
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

    <!-- Terms List -->
    <div class="bg-white shadow rounded-lg">
      <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-medium text-gray-900">Lista Regulaminów</h2>
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
        <button @click="fetchTerms" class="mt-2 text-indigo-600 hover:text-indigo-800">
          Spróbuj ponownie
        </button>
      </div>

      <!-- Terms Table -->
      <div v-else-if="terms.length > 0" class="overflow-x-auto">
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
            <tr v-for="term in terms" :key="term.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900">{{ term.title }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-500">{{ term.version }}</div>
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
                <div class="flex justify-end space-x-2">
                  <button 
                    @click="viewTerm(term)"
                    class="text-indigo-600 hover:text-indigo-900"
                    title="Podgląd"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                  </button>
                  <button 
                    @click="editTerm(term)"
                    class="text-blue-600 hover:text-blue-900"
                    title="Edytuj"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                  </button>
                  <button 
                    v-if="!term.is_active"
                    @click="setAsActive(term)"
                    class="text-green-600 hover:text-green-900"
                    title="Ustaw jako aktywny"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                  </button>
                  <button 
                    v-if="!term.is_active"
                    @click="deleteTerm(term)"
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
        <h3 class="mt-2 text-sm font-medium text-gray-900">Brak regulaminów</h3>
        <p class="mt-1 text-sm text-gray-500">Rozpocznij od utworzenia pierwszego regulaminu.</p>
        <div class="mt-6">
          <button 
            @click="showCreateModal = true"
            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Utwórz pierwszy regulamin
          </button>
        </div>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <div v-if="showCreateModal || showEditModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
        <div class="mt-3">
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">
              {{ showCreateModal ? 'Nowy Regulamin' : 'Edytuj Regulamin' }}
            </h3>
            <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <form @submit.prevent="submitForm" class="space-y-4">
            <div>
              <label for="title" class="block text-sm font-medium text-gray-700">Tytuł</label>
              <input 
                type="text" 
                id="title"
                v-model="form.title" 
                required
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
              />
            </div>

            <div>
              <label for="version" class="block text-sm font-medium text-gray-700">Wersja</label>
              <input 
                type="text" 
                id="version"
                v-model="form.version" 
                required
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
              />
            </div>

            <div>
              <label for="effective_date" class="block text-sm font-medium text-gray-700">Data obowiązywania</label>
              <input 
                type="datetime-local" 
                id="effective_date"
                v-model="form.effective_date" 
                required
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
              />
            </div>

            <div>
              <label for="content" class="block text-sm font-medium text-gray-700">Treść</label>
              <textarea 
                id="content"
                v-model="form.content" 
                rows="15"
                required
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                placeholder="Wprowadź treść regulaminu w formacie HTML..."
              ></textarea>
            </div>

            <div class="flex items-center">
              <input 
                type="checkbox" 
                id="is_active"
                v-model="form.is_active"
                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
              />
              <label for="is_active" class="ml-2 block text-sm text-gray-900">
                Ustaw jako aktywny regulamin
              </label>
            </div>

            <div class="flex justify-end space-x-3 pt-4">
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
                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50"
              >
                {{ submitting ? 'Zapisywanie...' : (showCreateModal ? 'Utwórz' : 'Zaktualizuj') }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- View Modal -->
    <div v-if="showViewModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
        <div class="mt-3">
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">{{ viewingTerm?.title }}</h3>
            <button @click="showViewModal = false" class="text-gray-400 hover:text-gray-600">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <div class="mb-4 p-4 bg-gray-50 rounded-lg">
            <div class="grid grid-cols-2 gap-4 text-sm">
              <div>
                <span class="font-medium">Wersja:</span> {{ viewingTerm?.version }}
              </div>
              <div>
                <span class="font-medium">Status:</span> 
                <span :class="viewingTerm?.is_active ? 'text-green-600' : 'text-gray-600'">
                  {{ viewingTerm?.is_active ? 'Aktywny' : 'Nieaktywny' }}
                </span>
              </div>
              <div>
                <span class="font-medium">Data obowiązywania:</span> {{ formatDate(viewingTerm?.effective_date) }}
              </div>
              <div>
                <span class="font-medium">Data utworzenia:</span> {{ formatDate(viewingTerm?.created_at) }}
              </div>
            </div>
          </div>

          <div class="prose max-w-none">
            <div v-html="viewingTerm?.content"></div>
          </div>
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
  name: 'AdminTermsOfService',
  
  setup() {
    const toast = useToast()
    
    // State
    const loading = ref(true)
    const error = ref(null)
    const terms = ref([])
    const stats = ref({
      total_users: 0,
      accepted_users: 0,
      not_accepted_users: 0,
      acceptance_rate: 0
    })
    
    // Modals
    const showCreateModal = ref(false)
    const showEditModal = ref(false)
    const showViewModal = ref(false)
    const submitting = ref(false)
    
    // Form data
    const form = ref({
      title: '',
      content: '',
      version: '1.0',
      effective_date: '',
      is_active: false
    })
    
    const editingTerm = ref(null)
    const viewingTerm = ref(null)
    
    // Methods
    const fetchTerms = async () => {
      try {
        loading.value = true
        error.value = null
        
        const response = await axios.get('/api/admin/terms-of-service')
        terms.value = response.data.terms_of_service
      } catch (err) {
        error.value = 'Nie udało się załadować regulaminów'
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
    
    const submitForm = async () => {
      try {
        submitting.value = true
        
        if (showCreateModal.value) {
          await axios.post('/api/admin/terms-of-service', form.value)
          toast.success('Regulamin został utworzony')
        } else {
          await axios.put(`/api/admin/terms-of-service/${editingTerm.value.id}`, form.value)
          toast.success('Regulamin został zaktualizowany')
        }
        
        closeModal()
        await fetchTerms()
        await fetchStats()
      } catch (err) {
        toast.error('Wystąpił błąd podczas zapisywania')
        console.error('Error submitting form:', err)
      } finally {
        submitting.value = false
      }
    }
    
    const editTerm = (term) => {
      editingTerm.value = term
      form.value = {
        title: term.title,
        content: term.content,
        version: term.version,
        effective_date: new Date(term.effective_date).toISOString().slice(0, 16),
        is_active: term.is_active
      }
      showEditModal.value = true
    }
    
    const viewTerm = (term) => {
      viewingTerm.value = term
      showViewModal.value = true
    }
    
    const setAsActive = async (term) => {
      try {
        await axios.post(`/api/admin/terms-of-service/${term.id}/set-active`)
        toast.success('Regulamin został ustawiony jako aktywny')
        await fetchTerms()
      } catch (err) {
        toast.error('Nie udało się ustawić regulaminu jako aktywny')
        console.error('Error setting as active:', err)
      }
    }
    
    const deleteTerm = async (term) => {
      if (!confirm('Czy na pewno chcesz usunąć ten regulamin?')) return
      
      try {
        await axios.delete(`/api/admin/terms-of-service/${term.id}`)
        toast.success('Regulamin został usunięty')
        await fetchTerms()
        await fetchStats()
      } catch (err) {
        toast.error('Nie udało się usunąć regulaminu')
        console.error('Error deleting term:', err)
      }
    }
    
    const closeModal = () => {
      showCreateModal.value = false
      showEditModal.value = false
      editingTerm.value = null
      form.value = {
        title: '',
        content: '',
        version: '1.0',
        effective_date: '',
        is_active: false
      }
    }
    
    const formatDate = (dateString) => {
      if (!dateString) return ''
      const date = new Date(dateString)
      return date.toLocaleDateString('pl-PL', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      })
    }
    
    onMounted(() => {
      fetchTerms()
      fetchStats()
    })
    
    return {
      loading,
      error,
      terms,
      stats,
      showCreateModal,
      showEditModal,
      showViewModal,
      submitting,
      form,
      editingTerm,
      viewingTerm,
      fetchTerms,
      submitForm,
      editTerm,
      viewTerm,
      setAsActive,
      deleteTerm,
      closeModal,
      formatDate
    }
  }
}
</script> 