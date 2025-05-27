<template>
  <div>
    <!-- Page Header -->
    <page-header 
      title="Wiadomości kontaktowe"
      subtitle="Lista wszystkich wiadomości kontaktowych z możliwością zarządzania i odpowiadania."
    />

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
    
    <!-- Contact Messages Table -->
    <admin-table
      v-if="!loading && messages.data && messages.data.length"
      :columns="tableColumns"
      :items="messages.data"
      class="mt-6"
    >
      <template #cell-name="{ item }">
        <div class="max-w-[190px]">
          <span class="block text-sm">{{ item.name }}</span>
        </div>
      </template>
      
      <template #cell-email="{ item }">
        <div class="max-w-[270px]">
          <span class="block text-sm">{{ item.email }}</span>
        </div>
      </template>
      
      <template #cell-subject="{ item }">
        <div class="max-w-[170px]">
          <span class="block truncate text-sm" :title="item.subject">{{ item.subject }}</span>
        </div>
      </template>
      
      <template #cell-status="{ item }">
        <admin-badge 
          :variant="getStatusVariant(item.status)"
          size="xs"
        >
          {{ item.status_label || getStatusLabel(item.status) }}
        </admin-badge>
      </template>
      
      <template #cell-actions="{ item }">
        <admin-button-group spacing="xs">
          <admin-button
            @click="viewMessage(item)"
            variant="primary"
            size="sm"
          >
            Zarządzaj
          </admin-button>
          <admin-button
            @click="confirmDelete(item)"
            variant="danger"
            size="sm"
          >
            Usuń
          </admin-button>
        </admin-button-group>
      </template>
    </admin-table>
    
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
    <div v-if="showDetailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center">
      <div class="relative mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-medium text-gray-900">Szczegóły wiadomości</h3>
          <button @click="closeDetails" class="text-gray-400 hover:text-gray-500">
            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        
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
                <button 
                  @click="sendResponse" 
                  class="mt-2 w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                  :disabled="responseSending"
                >
                  <svg v-if="responseSending" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  {{ responseSending ? 'Wysyłanie...' : 'Wyślij odpowiedź' }}
                </button>
              </div>
            </div>
          </div>
          
          <div class="flex justify-between items-center space-x-3 mt-6">
            <div class="flex space-x-3">
              <button 
                @click="confirmDelete(selectedMessage)" 
                class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
              >
                Usuń
              </button>
              <button 
                @click="saveNotes" 
                class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                Zapisz zmiany
              </button>
            </div>
            <button 
              @click="closeDetails" 
              class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
            >
              Zamknij
            </button>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center">
      <div class="relative mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
          <h3 class="text-lg leading-6 font-medium text-gray-900">Potwierdź usunięcie</h3>
          <div class="mt-2 px-7 py-3">
            <p class="text-sm text-gray-500">
              Czy na pewno chcesz usunąć tę wiadomość?
              <br>
              <span class="font-semibold">{{ messageToDelete.subject }}</span>
              <br>
              od {{ messageToDelete.name }}
            </p>
          </div>
          <div class="flex justify-center mt-4 gap-4">
            <button 
              @click="showDeleteModal = false" 
              class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              Anuluj
            </button>
            <button 
              @click="deleteMessage" 
              class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
            >
              Usuń
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, reactive, onMounted } from 'vue'
import axios from 'axios'
import { useAlertStore } from '../../stores/alertStore'
import { debounce } from 'lodash'
import AdminTable from '../../components/admin/ui/AdminTable.vue'
import AdminButtonGroup from '../../components/admin/ui/AdminButtonGroup.vue'
import AdminButton from '../../components/admin/ui/AdminButton.vue'
import AdminBadge from '../../components/admin/ui/AdminBadge.vue'
import SearchFilters from '../../components/admin/SearchFilters.vue'
import LoadingSpinner from '../../components/admin/LoadingSpinner.vue'
import NoDataMessage from '../../components/admin/NoDataMessage.vue'
import Pagination from '../../components/admin/Pagination.vue'
import PageHeader from '../../components/admin/PageHeader.vue'

export default {
  name: 'AdminContactMessages',
  components: {
    AdminTable,
    AdminButtonGroup,
    AdminButton,
    AdminBadge,
    SearchFilters,
    LoadingSpinner,
    NoDataMessage,
    Pagination,
    PageHeader
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
      { value: 'created_at', label: 'Data dodania' },
      { value: 'name', label: 'Imię' },
      { value: 'email', label: 'Email' },
      { value: 'subject', label: 'Temat' },
      { value: 'status', label: 'Status' }
    ]
    
    // Table columns definition
    const tableColumns = [
      { key: 'name', label: 'Imię', width: '200px' },
      { key: 'email', label: 'Email', width: '280px' },
      { key: 'subject', label: 'Temat', width: '180px' },
      { key: 'status', label: 'Status', width: '100px' },
      { key: 'created_at', label: 'Data', type: 'date', width: '100px' },
      { key: 'actions', label: 'Akcje', align: 'right', width: '160px' }
    ]
    
    // Response system data
    const responseData = ref({
      subject: '',
      message: '',
      markAsReplied: true,
      addToNotes: true
    })
    
    // Filters and pagination
    const filters = reactive({
      search: '',
      status: '',
      sort_field: 'created_at',
      sort_direction: 'desc',
      page: 1
    })
    
    // Handle table sorting

    
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
        messages.value = response.data
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
        await axios.patch(`/api/admin/contact-messages/${selectedMessage.value.id}/status`, {
          status: selectedMessage.value.status
        })
        
        // Update status in messages list
        const index = messages.value.data.findIndex(m => m.id === selectedMessage.value.id)
        if (index !== -1) {
          messages.value.data[index].status = selectedMessage.value.status
        }
        
        alertStore.success('Status wiadomości został zaktualizowany.')
      } catch (error) {
        console.error('Error updating message status:', error)
        alertStore.error('Wystąpił błąd podczas aktualizacji statusu wiadomości.')
      }
    }
    
    // Save notes
    const saveNotes = async () => {
      try {
        await axios.patch(`/api/admin/contact-messages/${selectedMessage.value.id}/notes`, {
          notes: selectedMessage.value.notes
        })
        
        // Update notes in messages list
        const index = messages.value.data.findIndex(m => m.id === selectedMessage.value.id)
        if (index !== -1) {
          messages.value.data[index].notes = selectedMessage.value.notes
        }
        
        alertStore.success('Notatki zostały zapisane.')
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
        await axios.post(`/api/admin/contact-messages/${selectedMessage.value.id}/respond`, {
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
        
        alertStore.success('Odpowiedź została wysłana.')
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
        await axios.delete(`/api/admin/contact-messages/${messageToDelete.value.id}`)
        messages.value.data = messages.value.data.filter(message => message.id !== messageToDelete.value.id)
        alertStore.success('Wiadomość została usunięta.')
        showDeleteModal.value = false
      } catch (error) {
        console.error('Error deleting message:', error)
        alertStore.error('Wystąpił błąd podczas usuwania wiadomości.')
      }
    }
    
    // Format date
    const formatDate = (dateString) => {
      const options = { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' }
      return new Date(dateString).toLocaleDateString('pl-PL', options)
    }
    
    // Get status class
    const getStatusClass = (status) => {
      switch (status) {
        case 'unread': return 'bg-yellow-100 text-yellow-800'
        case 'read': return 'bg-blue-100 text-blue-800'
        case 'replied': return 'bg-green-100 text-green-800'
        default: return 'bg-gray-100 text-gray-800'
      }
    }
    
    // Pagination
    const goToPage = (page) => {
      filters.page = page
      fetchMessages()
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
      tableColumns,
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
      getStatusClass,
      goToPage,
      sendResponse
    }
  }
}
</script>