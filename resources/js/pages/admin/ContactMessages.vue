<template>
  <div class="space-y-6 bg-white min-h-full">
    <!-- Page Header -->
    <div class="px-6 py-4">
      <page-header 
        title="Wiadomości kontaktowe"
        :show-add-button="false"
      />
    </div>

    <!-- Search and filters -->
    <search-filters
      v-if="!loading"
      :filters="filters"
      :sort-options="sortOptions"
      search-label="Wyszukaj"
      search-placeholder="Imię, email, temat lub treść..."
      @update:filters="(newFilters) => { Object.assign(filters, newFilters); filters.page = 1; }"
      @filter-change="fetchMessages"
    >
      <template v-slot:filters>
        <div class="w-full sm:w-auto">
          <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
          <select
            id="status"
            name="status"
            v-model="filters.status"
            @change="() => { filters.page = 1; fetchMessages(); }"
            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
          >
            <option value="">Wszystkie</option>
            <option value="unread">Nieprzeczytane</option>
            <option value="read">Przeczytane</option>
            <option value="replied">Odpowiedziane</option>
          </select>
        </div>
      </template>
    </search-filters>

    <!-- Loading indicator -->
    <loading-spinner v-if="loading" />
    
    <!-- Contact Messages Custom Table -->
    <div v-if="!loading && messages.data && messages.data.length > 0" class="mt-6 bg-white shadow-sm rounded-lg overflow-hidden">
                      <div class="overflow-x-auto -mx-6 px-6" style="scrollbar-width: thin; scrollbar-color: #d1d5db #f3f4f6;">
          <table class="min-w-full divide-y divide-gray-200 whitespace-nowrap">
          <thead class="bg-gray-50">
            <tr>
              <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">
                Imię
              </th>
              <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-48">
                Email
              </th>
              <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">
                Temat
              </th>
              <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-24">
                Status
              </th>
              <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-28">
                Data
              </th>
              <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-36">
                Akcje
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="item in messages.data" :key="item.id" class="hover:bg-gray-50">
              <!-- Name Column -->
              <td class="px-4 py-4">
                <div class="text-sm font-medium text-gray-900 max-w-[140px] truncate" :title="item.name">
                  {{ item.name }}
                </div>
              </td>
              
              <!-- Email Column -->
              <td class="px-3 py-4">
                <div class="text-sm text-gray-900 max-w-[180px] truncate" :title="item.email">
                  {{ item.email }}
                </div>
              </td>
              
              <!-- Subject Column -->
              <td class="px-3 py-4">
                <div class="text-sm text-gray-900 max-w-[140px] truncate" :title="item.subject">
                  {{ item.subject }}
                </div>
              </td>
              
              <!-- Status Column -->
              <td class="px-3 py-4 text-center">
                <admin-badge 
                  :variant="getStatusVariant(item.status)"
                  size="xs"
                >
                  {{ item.status_label || getStatusLabel(item.status) }}
                </admin-badge>
              </td>
              
              <!-- Created At Column -->
              <td class="px-3 py-4 text-center">
                <span class="text-xs text-gray-500">{{ formatDate(item.created_at) }}</span>
              </td>
              
              <!-- Actions Column -->
              <td class="px-4 py-4 text-right">
                <div class="flex items-center justify-end space-x-2">
                  <admin-button
                    @click="viewMessage(item)"
                    variant="warning"
                    size="sm"
                  >
                    Zarządzaj
                  </admin-button>
                  <action-buttons 
                    :item="item" 
                    :show-edit="false"
                    @delete="confirmDelete"
                    justify="end"
                  />
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    
    <!-- Pagination -->
    <pagination 
      v-if="messages.data && messages.data.length > 0 && messages.last_page > 1"
      :pagination="messages" 
      items-label="wiadomości" 
      @page-change="goToPage" 
    />
    
    <!-- No data message -->
    <no-data-message v-if="!loading && (!messages.data || messages.data.length === 0)" message="Brak wiadomości kontaktowych do wyświetlenia" />
    
    <!-- Message Details Modal -->
    <admin-modal 
      :show="showDetailsModal" 
      title="Szczegóły wiadomości"
      size="4xl"
      @close="closeDetails"
    >
      <div v-if="selectedMessage" class="space-y-4">
        <div class="flex justify-between">
          <div>
            <h4 class="text-sm font-medium text-gray-500">Od</h4>
            <p class="mt-1 text-sm text-gray-900">{{ selectedMessage.name }} ({{ selectedMessage.email }})</p>
          </div>
          <div>
            <h4 class="text-sm font-medium text-gray-500">Data</h4>
            <p class="mt-1 text-sm text-gray-900">{{ formatDate(selectedMessage.created_at) }}</p>
          </div>
        </div>
        
        <div>
          <h4 class="text-sm font-medium text-gray-500">Temat</h4>
          <p class="mt-1 text-sm font-semibold text-gray-900">{{ selectedMessage.subject }}</p>
        </div>
        
        <div>
          <h4 class="text-sm font-medium text-gray-500">Wiadomość</h4>
          <div class="mt-1 bg-gray-50 p-3 rounded text-sm text-gray-900">
            <p class="whitespace-pre-line">{{ selectedMessage.message }}</p>
          </div>
        </div>
        
        <div v-if="selectedMessage.notes">
          <h4 class="text-sm font-medium text-gray-500">Notatki</h4>
          <div class="mt-1 bg-yellow-50 p-3 rounded text-sm text-gray-900">
            <p class="whitespace-pre-line">{{ selectedMessage.notes }}</p>
          </div>
        </div>
        
        <div>
          <h4 class="text-sm font-medium text-gray-500">Status</h4>
          <div class="mt-1">
            <select 
              v-model="selectedMessage.status"
              @change="updateStatus"
              class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
            >
              <option value="unread">Nieprzeczytana</option>
              <option value="read">Przeczytana</option>
              <option value="replied">Odpowiedziana</option>
            </select>
          </div>
        </div>
        
        <div>
          <h4 class="text-sm font-medium text-gray-500">Notatki wewnętrzne</h4>
          <textarea 
            v-model="selectedMessage.notes" 
            rows="3"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            placeholder="Dodaj notatki dotyczące tej wiadomości..."
          ></textarea>
        </div>
        
        <!-- New Response System -->
        <div class="mt-6 border-t pt-4">
          <h4 class="text-md font-medium text-gray-700">Odpowiedź bezpośrednia</h4>
          <div class="mt-3 space-y-3">
            <div>
              <label for="response-subject" class="block text-sm font-medium text-gray-700">Temat</label>
              <input 
                type="text" 
                id="response-subject" 
                v-model="responseData.subject" 
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                placeholder="Re: {{selectedMessage.subject}}"
              />
            </div>
            
            <div>
              <label for="response-message" class="block text-sm font-medium text-gray-700">Treść odpowiedzi</label>
              <textarea 
                id="response-message" 
                v-model="responseData.message" 
                rows="5"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                placeholder="Wpisz swoją odpowiedź..."
              ></textarea>
            </div>
            
            <div class="mt-2">
              <label class="inline-flex items-center">
                <input type="checkbox" v-model="responseData.markAsReplied" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-offset-0">
                <span class="ml-2 text-sm text-gray-700">Oznacz jako odpowiedzianą po wysłaniu</span>
              </label>
            </div>
            
            <div class="mt-2">
              <label class="inline-flex items-center">
                <input type="checkbox" v-model="responseData.addToNotes" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-offset-0">
                <span class="ml-2 text-sm text-gray-700">Dodaj odpowiedź do notatek</span>
              </label>
            </div>
            
            <div>
              <admin-button 
                @click="sendResponse" 
                variant="primary"
                :loading="responseSending"
                class="mt-2 w-full"
              >
                Wyślij odpowiedź
              </admin-button>
            </div>
          </div>
        </div>
      </div>
      
      <template #footer>
        <admin-button-group justify="between" spacing="sm">
          <div class="flex space-x-3">
            <admin-button 
              @click="confirmDelete(selectedMessage)" 
              variant="danger"
            >
              Usuń
            </admin-button>
            <admin-button 
              @click="saveNotes" 
              variant="primary"
            >
              Zapisz zmiany
            </admin-button>
          </div>
          <admin-button 
            @click="closeDetails" 
            variant="secondary"
            outline
          >
            Zamknij
          </admin-button>
        </admin-button-group>
      </template>
    </admin-modal>
    
    <!-- Delete Confirmation Modal -->
    <admin-modal 
      :show="showDeleteModal" 
      title="Potwierdź usunięcie"
      size="md"
      @close="showDeleteModal = false"
    >
      <p class="text-sm text-gray-500">
        Czy na pewno chcesz usunąć tę wiadomość?
        <br>
        <span class="font-semibold">{{ messageToDelete.subject }}</span>
        <br>
        od {{ messageToDelete.name }}
      </p>
      
      <template #footer>
        <admin-button-group justify="end" spacing="sm">
          <admin-button 
            @click="showDeleteModal = false" 
            variant="secondary"
            outline
          >
            Anuluj
          </admin-button>
          <admin-button 
            @click="deleteMessage" 
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
import { ref, computed, reactive, onMounted } from 'vue'
import axios from 'axios'
import { useAlertStore } from '../../stores/alertStore'
import { debounce } from 'lodash'
import AdminButtonGroup from '../../components/admin/ui/AdminButtonGroup.vue'
import AdminButton from '../../components/admin/ui/AdminButton.vue'
import AdminBadge from '../../components/admin/ui/AdminBadge.vue'
import AdminModal from '../../components/admin/ui/AdminModal.vue'
import SearchFilters from '../../components/admin/SearchFilters.vue'
import LoadingSpinner from '../../components/admin/LoadingSpinner.vue'
import NoDataMessage from '../../components/admin/NoDataMessage.vue'
import Pagination from '../../components/admin/Pagination.vue'
import PageHeader from '../../components/admin/PageHeader.vue'
import ActionButtons from '../../components/admin/ActionButtons.vue'

export default {
  name: 'AdminContactMessages',
  components: {
    AdminButtonGroup,
    AdminButton,
    AdminBadge,
    AdminModal,
    SearchFilters,
    LoadingSpinner,
    NoDataMessage,
    Pagination,
    PageHeader,
    ActionButtons
  },
  setup() {
    const alertStore = useAlertStore()
    
    // Data
    const loading = ref(true)
    const messages = ref({
      data: [],
      current_page: 1,
      from: 1,
      to: 0,
      total: 0,
      last_page: 1,
      per_page: 10
    })
    const showDetailsModal = ref(false)
    const showDeleteModal = ref(false)
    const selectedMessage = ref(null)
    const messageToDelete = ref(null)
    const responseSending = ref(false)
    
    // Sort options for the filter component
    const sortOptions = [
      { value: 'created_at', label: 'Data wysłania' },
      { value: 'name', label: 'Imię i nazwisko' },
      { value: 'email', label: 'Email' },
      { value: 'status', label: 'Status' }
    ]
    
    // Default filters
    const defaultFilters = {
      search: '',
      status: '',
      sort_field: 'created_at',
      sort_direction: 'desc',
      page: 1
    }
    
    // Filters and pagination
    const filters = reactive({
      search: '',
      status: '',
      sort_field: 'created_at',
      sort_direction: 'desc',
      page: 1
    })
    
    // Response system data
    const responseData = ref({
      subject: '',
      message: '',
      markAsReplied: true,
      addToNotes: true
    })
    
    // Get status variant for AdminBadge
    const getStatusVariant = (status) => {
      switch (status) {
        case 'unread': return 'red'
        case 'read': return 'yellow'
        case 'replied': return 'green'
        default: return 'gray'
      }
    }
    
    // Get status label
    const getStatusLabel = (status) => {
      switch (status) {
        case 'unread': return 'Nieprzeczytana'
        case 'read': return 'Przeczytana'
        case 'replied': return 'Odpowiedziana'
        default: return status
      }
    }
    
    // Fetch all messages
    const fetchMessages = async () => {
      try {
        loading.value = true
        
        const params = {
          page: filters.page,
          search: filters.search,
          status: filters.status,
          sort_field: filters.sort_field,
          sort_direction: filters.sort_direction
        }
        
        console.log('Fetching messages with params:', params)
        
        const response = await axios.get('/api/admin/contact-messages', { params })
        messages.value = response.data.data
      } catch (error) {
        console.error('Error fetching contact messages:', error)
        alertStore.error('Wystąpił błąd podczas pobierania wiadomości.')
      } finally {
        loading.value = false
      }
    }
    
    const debouncedFetchMessages = debounce(fetchMessages, 300)
    
    // View message details
    const viewMessage = (message) => {
      selectedMessage.value = { ...message }
      
      // Prepare response data
      responseData.value.subject = `Re: ${message.subject}`
      responseData.value.message = ''
      responseData.value.markAsReplied = true
      responseData.value.addToNotes = true
      
      showDetailsModal.value = true
      
      // If message is unread, mark it as read
      if (message.status === 'unread') {
        selectedMessage.value.status = 'read'
        updateStatus()
      }
    }
    
    // Close details modal
    const closeDetails = () => {
      showDetailsModal.value = false
      selectedMessage.value = null
    }
    
    // Update message status
    const updateStatus = async () => {
      try {
        const response = await axios.patch(`/api/admin/contact-messages/${selectedMessage.value.id}/status`, {
          status: selectedMessage.value.status
        })
        
        // Update status in messages list
        const index = messages.value.data.findIndex(m => m.id === selectedMessage.value.id)
        if (index !== -1) {
          messages.value.data[index].status = selectedMessage.value.status
        }
        
        alertStore.success(response.data.message || 'Status wiadomości został zaktualizowany.')
      } catch (error) {
        console.error('Error updating message status:', error)
        alertStore.error('Wystąpił błąd podczas aktualizacji statusu wiadomości.')
      }
    }
    
    // Save notes
    const saveNotes = async () => {
      try {
        const response = await axios.patch(`/api/admin/contact-messages/${selectedMessage.value.id}/notes`, {
          notes: selectedMessage.value.notes
        })
        
        // Update notes in messages list
        const index = messages.value.data.findIndex(m => m.id === selectedMessage.value.id)
        if (index !== -1) {
          messages.value.data[index].notes = selectedMessage.value.notes
        }
        
        alertStore.success(response.data.message || 'Notatki zostały zapisane.')
        closeDetails() // Close the modal after successful save
      } catch (error) {
        console.error('Error saving notes:', error)
        alertStore.error('Wystąpił błąd podczas zapisywania notatek.')
      }
    }
    
    // Send response
    const sendResponse = async () => {
      if (!responseData.value.message.trim()) {
        alertStore.error('Treść odpowiedzi nie może być pusta.')
        return
      }
      
      try {
        responseSending.value = true
        
        // Send the response email
        const response = await axios.post(`/api/admin/contact-messages/${selectedMessage.value.id}/respond`, {
          subject: responseData.value.subject,
          message: responseData.value.message
        })
        
        // If user chose to mark as replied
        if (responseData.value.markAsReplied) {
          selectedMessage.value.status = 'replied'
          
          // Update status in DB
          await axios.patch(`/api/admin/contact-messages/${selectedMessage.value.id}/status`, {
            status: 'replied'
          })
          
          // Update status in messages list
          const index = messages.value.data.findIndex(m => m.id === selectedMessage.value.id)
          if (index !== -1) {
            messages.value.data[index].status = 'replied'
          }
        }
        
        // If user chose to add to notes
        if (responseData.value.addToNotes) {
          const dateTime = new Date().toLocaleString('pl-PL')
          const notesAddition = `--- Odpowiedź wysłana ${dateTime} ---\n${responseData.value.message}\n\n`
          
          if (selectedMessage.value.notes) {
            selectedMessage.value.notes = notesAddition + selectedMessage.value.notes
          } else {
            selectedMessage.value.notes = notesAddition
          }
          
          // Save notes
          await axios.patch(`/api/admin/contact-messages/${selectedMessage.value.id}/notes`, {
            notes: selectedMessage.value.notes
          })
          
          // Update notes in messages list
          const index = messages.value.data.findIndex(m => m.id === selectedMessage.value.id)
          if (index !== -1) {
            messages.value.data[index].notes = selectedMessage.value.notes
          }
        }
        
        alertStore.success(response.data.message || 'Odpowiedź została wysłana.')
        closeDetails() // Close the modal after successful response
        
        // Reset response form
        responseData.value.message = ''
      } catch (error) {
        console.error('Error sending response:', error)
        alertStore.error('Wystąpił błąd podczas wysyłania odpowiedzi.')
      } finally {
        responseSending.value = false
      }
    }
    
    // Confirm delete
    const confirmDelete = (message) => {
      messageToDelete.value = message
      showDeleteModal.value = true
      
      // If details modal is open, close it
      if (showDetailsModal.value) {
        showDetailsModal.value = false
      }
    }
    
    // Delete message
    const deleteMessage = async () => {
      try {
        const response = await axios.delete(`/api/admin/contact-messages/${messageToDelete.value.id}`)
        messages.value.data = messages.value.data.filter(message => message.id !== messageToDelete.value.id)
        alertStore.success(response.data.message || 'Wiadomość została usunięta.')
        showDeleteModal.value = false
      } catch (error) {
        console.error('Error deleting message:', error)
        alertStore.error('Wystąpił błąd podczas usuwania wiadomości.')
      }
    }
    
    // Pagination
    const goToPage = (page) => {
      if (page === '...') return
      filters.page = page
      fetchMessages()
    }
    
    // Format date
    const formatDate = (dateString) => {
      if (!dateString) return '-'
      const options = { year: 'numeric', month: 'short', day: 'numeric' }
      return new Date(dateString).toLocaleDateString('pl-PL', options)
    }
    
    onMounted(() => {
      fetchMessages()
    })
    
    return {
      loading,
      messages,
      showDetailsModal,
      showDeleteModal,
      selectedMessage,
      messageToDelete,
      filters,
      sortOptions,
      responseData,
      responseSending,
      getStatusVariant,
      getStatusLabel,
      fetchMessages,
      viewMessage,
      closeDetails,
      updateStatus,
      saveNotes,
      confirmDelete,
      deleteMessage,
      formatDate,
      goToPage,
      sendResponse
    }
  }
}
</script>