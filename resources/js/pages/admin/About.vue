<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-semibold text-gray-900">Zarządzanie stroną "O nas"</h1>
    </div>
    
    <!-- Loading indicator -->
    <div v-if="loading" class="flex justify-center my-12">
      <svg class="animate-spin h-10 w-10 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
      </svg>
    </div>
    
    <!-- About Page Form -->
    <div v-else class="bg-white shadow overflow-hidden sm:rounded-lg p-6">
      <form @submit.prevent="saveAboutPage">
        <div class="grid grid-cols-1 gap-6">
          <div>
            <label for="title" class="block text-sm font-medium text-gray-700">Tytuł strony</label>
            <input 
              type="text" 
              id="title" 
              v-model="aboutPage.title" 
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
              required
            >
          </div>
          
          <div>
            <label for="subtitle" class="block text-sm font-medium text-gray-700">Podtytuł</label>
            <input 
              type="text" 
              id="subtitle" 
              v-model="aboutPage.subtitle" 
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            >
          </div>
          
          <div>
            <label for="content" class="block text-sm font-medium text-gray-700">Główna treść</label>
            <textarea 
              id="content" 
              v-model="aboutPage.content" 
              rows="8"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
              required
            ></textarea>
            <p class="mt-1 text-xs text-gray-500">Możesz używać składni Markdown do formatowania tekstu.</p>
          </div>
          
          <div>
            <label for="image_url" class="block text-sm font-medium text-gray-700">URL głównego obrazka</label>
            <input 
              type="url" 
              id="image_url" 
              v-model="aboutPage.image_url" 
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            >
          </div>
          
          <div>
            <label for="mission" class="block text-sm font-medium text-gray-700">Nasza misja</label>
            <textarea 
              id="mission" 
              v-model="aboutPage.mission" 
              rows="3"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            ></textarea>
          </div>
          
          <div>
            <label for="vision" class="block text-sm font-medium text-gray-700">Nasza wizja</label>
            <textarea 
              id="vision" 
              v-model="aboutPage.vision" 
              rows="3"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            ></textarea>
          </div>
          
          <div>
            <label for="values" class="block text-sm font-medium text-gray-700">Nasze wartości</label>
            <textarea 
              id="values" 
              v-model="aboutPage.values" 
              rows="3"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            ></textarea>
          </div>
          
          <div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Zespół</h3>
            <div class="space-y-3">
              <div v-for="(member, index) in aboutPage.team" :key="index" class="border border-gray-200 rounded-md p-4">
                <div class="flex justify-between items-start">
                  <h4 class="text-md font-medium text-gray-700">Członek zespołu {{ index + 1 }}</h4>
                  <button 
                    type="button" 
                    @click="removeTeamMember(index)" 
                    class="text-red-600 hover:text-red-900"
                  >
                    Usuń
                  </button>
                </div>
                <div class="mt-3 grid grid-cols-1 gap-3">
                  <div>
                    <label :for="`team-name-${index}`" class="block text-sm font-medium text-gray-700">Imię i nazwisko</label>
                    <input 
                      :id="`team-name-${index}`" 
                      v-model="member.name" 
                      type="text" 
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                      required
                    >
                  </div>
                  <div>
                    <label :for="`team-position-${index}`" class="block text-sm font-medium text-gray-700">Stanowisko</label>
                    <input 
                      :id="`team-position-${index}`" 
                      v-model="member.position" 
                      type="text" 
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                      required
                    >
                  </div>
                  <div>
                    <label :for="`team-bio-${index}`" class="block text-sm font-medium text-gray-700">Biogram</label>
                    <textarea 
                      :id="`team-bio-${index}`" 
                      v-model="member.bio" 
                      rows="2"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    ></textarea>
                  </div>
                  <div>
                    <label :for="`team-photo-${index}`" class="block text-sm font-medium text-gray-700">URL zdjęcia</label>
                    <input 
                      :id="`team-photo-${index}`" 
                      v-model="member.photo_url" 
                      type="url" 
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    >
                  </div>
                </div>
              </div>
              <button 
                type="button" 
                @click="addTeamMember" 
                class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                Dodaj członka zespołu
              </button>
            </div>
          </div>
          
          <div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Historia firmy</h3>
            <div class="space-y-3">
              <div v-for="(milestone, index) in aboutPage.history" :key="index" class="border border-gray-200 rounded-md p-4">
                <div class="flex justify-between items-start">
                  <h4 class="text-md font-medium text-gray-700">Wydarzenie {{ index + 1 }}</h4>
                  <button 
                    type="button" 
                    @click="removeHistoryMilestone(index)" 
                    class="text-red-600 hover:text-red-900"
                  >
                    Usuń
                  </button>
                </div>
                <div class="mt-3 grid grid-cols-1 gap-3">
                  <div>
                    <label :for="`history-year-${index}`" class="block text-sm font-medium text-gray-700">Rok</label>
                    <input 
                      :id="`history-year-${index}`" 
                      v-model="milestone.year" 
                      type="number" 
                      min="1900"
                      max="2100"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                      required
                    >
                  </div>
                  <div>
                    <label :for="`history-title-${index}`" class="block text-sm font-medium text-gray-700">Tytuł</label>
                    <input 
                      :id="`history-title-${index}`" 
                      v-model="milestone.title" 
                      type="text" 
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                      required
                    >
                  </div>
                  <div>
                    <label :for="`history-description-${index}`" class="block text-sm font-medium text-gray-700">Opis</label>
                    <textarea 
                      :id="`history-description-${index}`" 
                      v-model="milestone.description" 
                      rows="2"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    ></textarea>
                  </div>
                </div>
              </div>
              <button 
                type="button" 
                @click="addHistoryMilestone" 
                class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                Dodaj wydarzenie historyczne
              </button>
            </div>
          </div>
          
          <div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">SEO</h3>
            <div class="space-y-3">
              <div>
                <label for="meta_title" class="block text-sm font-medium text-gray-700">Meta tytuł</label>
                <input 
                  type="text" 
                  id="meta_title" 
                  v-model="aboutPage.meta_title" 
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                >
              </div>
              <div>
                <label for="meta_description" class="block text-sm font-medium text-gray-700">Meta opis</label>
                <textarea 
                  id="meta_description" 
                  v-model="aboutPage.meta_description" 
                  rows="2"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                ></textarea>
              </div>
            </div>
          </div>
        </div>
        
        <div class="mt-6 flex justify-end">
          <button 
            type="submit" 
            class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            :disabled="saving"
          >
            <span v-if="saving">Zapisywanie...</span>
            <span v-else>Zapisz zmiany</span>
          </button>
        </div>
      </form>
    </div>
    
    <!-- Preview Modal -->
    <div v-if="showPreview" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center">
      <div class="relative mx-auto p-5 border w-full max-w-4xl shadow-lg rounded-md bg-white overflow-y-auto" style="max-height: 90vh">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-medium text-gray-900">Podgląd strony "O nas"</h3>
          <button @click="showPreview = false" class="text-gray-400 hover:text-gray-500">
            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        
        <div class="preview-content space-y-6">
          <h1 class="text-3xl font-bold text-gray-900">{{ aboutPage.title }}</h1>
          <h2 v-if="aboutPage.subtitle" class="text-xl text-gray-600">{{ aboutPage.subtitle }}</h2>
          
          <div v-if="aboutPage.image_url" class="my-6">
            <img :src="aboutPage.image_url" alt="About Us" class="w-full h-64 object-cover rounded-lg">
          </div>
          
          <div class="prose max-w-none">
            <p class="whitespace-pre-line">{{ aboutPage.content }}</p>
          </div>
          
          <div v-if="aboutPage.mission || aboutPage.vision || aboutPage.values" class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
            <div v-if="aboutPage.mission" class="bg-indigo-50 p-4 rounded-lg">
              <h3 class="text-lg font-medium text-indigo-900 mb-2">Nasza misja</h3>
              <p class="text-indigo-800">{{ aboutPage.mission }}</p>
            </div>
            <div v-if="aboutPage.vision" class="bg-green-50 p-4 rounded-lg">
              <h3 class="text-lg font-medium text-green-900 mb-2">Nasza wizja</h3>
              <p class="text-green-800">{{ aboutPage.vision }}</p>
            </div>
            <div v-if="aboutPage.values" class="bg-yellow-50 p-4 rounded-lg">
              <h3 class="text-lg font-medium text-yellow-900 mb-2">Nasze wartości</h3>
              <p class="text-yellow-800">{{ aboutPage.values }}</p>
            </div>
          </div>
          
          <div v-if="aboutPage.team && aboutPage.team.length > 0" class="mt-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Nasz zespół</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              <div v-for="(member, index) in aboutPage.team" :key="index" class="bg-white border border-gray-200 rounded-lg p-4 text-center">
                <img v-if="member.photo_url" :src="member.photo_url" :alt="member.name" class="w-24 h-24 object-cover rounded-full mx-auto mb-4">
                <div v-else class="w-24 h-24 bg-indigo-100 flex items-center justify-center rounded-full mx-auto mb-4">
                  <span class="text-2xl font-bold text-indigo-600">{{ member.name ? member.name.charAt(0) : '' }}</span>
                </div>
                <h3 class="text-lg font-medium text-gray-900">{{ member.name }}</h3>
                <p class="text-sm text-indigo-600 mb-2">{{ member.position }}</p>
                <p v-if="member.bio" class="text-sm text-gray-600">{{ member.bio }}</p>
              </div>
            </div>
          </div>
          
          <div v-if="aboutPage.history && aboutPage.history.length > 0" class="mt-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Historia firmy</h2>
            <div class="space-y-8">
              <div 
                v-for="(milestone, index) in sortedHistory" 
                :key="index" 
                class="flex items-start"
              >
                <div class="flex-shrink-0 w-24 text-center">
                  <span class="inline-flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 text-indigo-900 font-bold">
                    {{ milestone.year }}
                  </span>
                </div>
                <div class="ml-4">
                  <h3 class="text-lg font-medium text-gray-900">{{ milestone.title }}</h3>
                  <p v-if="milestone.description" class="mt-1 text-gray-600">{{ milestone.description }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import { useAlertStore } from '../../stores/alertStore'

export default {
  name: 'AdminAbout',
  setup() {
    const alertStore = useAlertStore()
    const loading = ref(true)
    const saving = ref(false)
    const showPreview = ref(false)
    const aboutPage = ref({
      title: '',
      subtitle: '',
      content: '',
      image_url: '',
      mission: '',
      vision: '',
      values: '',
      team: [],
      history: [],
      meta_title: '',
      meta_description: ''
    })
    
    // Fetch about page data
    const fetchAboutPage = async () => {
      try {
        loading.value = true
        const response = await axios.get('/api/admin/about')
        
        // Initialize with existing data or defaults
        aboutPage.value = {
          title: response.data.title || 'O nas',
          subtitle: response.data.subtitle || '',
          content: response.data.content || '',
          image_url: response.data.image_url || '',
          mission: response.data.mission || '',
          vision: response.data.vision || '',
          values: response.data.values || '',
          team: response.data.team || [],
          history: response.data.history || [],
          meta_title: response.data.meta_title || '',
          meta_description: response.data.meta_description || ''
        }
      } catch (error) {
        console.error('Error fetching about page data:', error)
        alertStore.error('Wystąpił błąd podczas pobierania danych strony "O nas".')
        
        // Initialize with defaults
        aboutPage.value = {
          title: 'O nas',
          subtitle: '',
          content: '',
          image_url: '',
          mission: '',
          vision: '',
          values: '',
          team: [],
          history: [],
          meta_title: '',
          meta_description: ''
        }
      } finally {
        loading.value = false
      }
    }
    
    // Save about page data
    const saveAboutPage = async () => {
      try {
        saving.value = true
        await axios.put('/api/admin/about', aboutPage.value)
        alertStore.success('Strona "O nas" została zaktualizowana.')
      } catch (error) {
        console.error('Error saving about page:', error)
        alertStore.error('Wystąpił błąd podczas zapisywania strony "O nas".')
      } finally {
        saving.value = false
      }
    }
    
    // Add team member
    const addTeamMember = () => {
      aboutPage.value.team.push({
        name: '',
        position: '',
        bio: '',
        photo_url: ''
      })
    }
    
    // Remove team member
    const removeTeamMember = (index) => {
      aboutPage.value.team.splice(index, 1)
    }
    
    // Add history milestone
    const addHistoryMilestone = () => {
      aboutPage.value.history.push({
        year: new Date().getFullYear(),
        title: '',
        description: ''
      })
    }
    
    // Remove history milestone
    const removeHistoryMilestone = (index) => {
      aboutPage.value.history.splice(index, 1)
    }
    
    // Sort history milestones by year (newest first)
    const sortedHistory = computed(() => {
      if (!aboutPage.value.history) return []
      return [...aboutPage.value.history].sort((a, b) => b.year - a.year)
    })
    
    // Preview about page
    const previewAboutPage = () => {
      showPreview.value = true
    }
    
    onMounted(() => {
      fetchAboutPage()
    })
    
    return {
      loading,
      saving,
      aboutPage,
      showPreview,
      fetchAboutPage,
      saveAboutPage,
      addTeamMember,
      removeTeamMember,
      addHistoryMilestone,
      removeHistoryMilestone,
      previewAboutPage,
      sortedHistory
    }
  }
}
</script> 