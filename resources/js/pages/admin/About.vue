<template>
  <div class="space-y-6 bg-white min-h-full lg:pr-6">
    <!-- Page Header -->
    <div class="px-6 py-4">
      <page-header 
        title="O nas"
        :show-add-button="false"
      />
    </div>
    
    <!-- Loading indicator -->
    <loading-spinner v-if="loading" />
    
    <div v-else class="space-y-8">
      <!-- About Page Form -->
      <div class="bg-white shadow sm:rounded-lg p-6">
        <form @submit.prevent="saveAboutPage">
          <div class="space-y-6">
            <div>
              <label for="title" class="block text-sm font-medium text-gray-700">Tytuł strony</label>
              <input 
                type="text" 
                id="title" 
                v-model="aboutUs.title" 
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                placeholder="Wpisz tytuł strony..."
              >
            </div>
            
            <!-- Styl nagłówka -->
            <div>
              <label for="header_style" class="block text-sm font-medium text-gray-700">Styl nagłówka</label>
              <select 
                id="header_style" 
                v-model="aboutUs.header_style" 
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
              >
                <option value="text-3xl font-bold text-gray-900">Standardowy lewy</option>
                <option value="text-3xl font-bold text-gray-900 text-center">Wyśrodkowany</option>
                <option value="text-3xl font-bold text-gray-900 text-right">Wyrównany do prawej</option>
                <option value="text-4xl font-bold text-gray-900">Duży lewy</option>
                <option value="text-4xl font-bold text-gray-900 text-center">Duży wyśrodkowany</option>
                <option value="text-5xl font-bold text-gray-900 text-center">Bardzo duży wyśrodkowany</option>
                <option value="text-3xl font-bold text-gray-900 border-b-2 border-indigo-500 pb-2 inline-block">Z podkreśleniem</option>
                <option value="text-3xl font-bold text-gray-900 border-b-2 border-indigo-500 pb-2 block text-center w-full">Wyśrodkowany z podkreśleniem</option>
                <option value="text-3xl font-bold text-gray-900 mx-auto max-w-lg text-center border-b border-t py-2">Wyśrodkowany z obramowaniem</option>
                <option value="text-4xl font-bold text-indigo-700 text-center uppercase tracking-wide">Duży indigo (nagłówek)</option>
              </select>
            </div>
            
            <!-- Dodatkowe opcje stylu nagłówka -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label for="header_margin" class="block text-sm font-medium text-gray-700">Marginesy nagłówka</label>
                <select 
                  id="header_margin" 
                  v-model="headerOptions.margin" 
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                >
                  <option value="mb-6">Standardowy</option>
                  <option value="mb-8">Duży</option>
                  <option value="mb-10">Bardzo duży</option>
                  <option value="my-6">Góra i dół</option>
                  <option value="my-8">Duży góra i dół</option>
                </select>
              </div>
              
              <div>
                <label for="content_layout" class="block text-sm font-medium text-gray-700">Układ treści</label>
                <select 
                  id="content_layout" 
                  v-model="headerOptions.contentLayout" 
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                >
                  <option value="prose-lg">Standardowy</option>
                  <option value="prose-lg max-w-3xl mx-auto">Wyśrodkowany</option>
                  <option value="prose-xl">Duża czcionka</option>
                  <option value="prose-xl max-w-4xl mx-auto">Duża czcionka wyśrodkowana</option>
                </select>
              </div>
            </div>
            
            <div>
              <label for="content" class="block text-sm font-medium text-gray-700">Treść</label>
              <RichTextEditor 
                id="content" 
                v-model="aboutUs.content" 
                :min-height="'300px'"
                :placeholder="'Wpisz treść strony O nas...'"
                :toolbar="[
                  ['bold', 'italic', 'underline', 'strike'],
                  [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                  [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                  [{ 'align': [] }],
                  [{ 'color': [] }, { 'background': [] }],
                  ['link', 'image'],
                  ['clean']
                ]"
              />
            </div>
            

            
            <div class="flex justify-end space-x-3">
              <button 
                type="button"
                @click="resetForm"
                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                Anuluj zmiany
              </button>
              <button 
                type="submit"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                :disabled="saving"
              >
                <svg v-if="saving" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ saving ? 'Zapisywanie...' : 'Zapisz zmiany' }}
              </button>
            </div>
          </div>
        </form>
      </div>
      
      <!-- Preview Section -->
      <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b">
          <h2 class="text-lg font-medium text-gray-900">Podgląd strony</h2>
          <p class="mt-1 text-sm text-gray-500">Tak będzie wyglądać strona dla użytkowników</p>
        </div>
        
        <!-- Preview Content -->
        <div class="max-w-6xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
          <h1 :class="[aboutUs.header_style || 'text-3xl font-bold text-gray-900', headerOptions.margin]">{{ aboutUs.title }}</h1>
          <div :class="['break-words', headerOptions.contentLayout]" v-html="aboutUs.content"></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useAlertStore } from '../../stores/alertStore'
import RichTextEditor from '../../components/RichTextEditor.vue'
import LoadingSpinner from '../../components/admin/LoadingSpinner.vue'
import PageHeader from '../../components/admin/PageHeader.vue'

export default {
  name: 'AdminAbout',
  components: {
    RichTextEditor,
    LoadingSpinner,
    PageHeader
  },
  setup() {
    const aboutUs = ref({
      title: '',
      content: '',
      header_style: 'text-3xl font-bold text-gray-900'
    })
    const originalAboutUs = ref(null)
    const loading = ref(true)
    const saving = ref(false)
    const alertStore = useAlertStore()
    const headerOptions = ref({
      margin: 'mb-6',
      contentLayout: 'prose-lg'
    })
    
    // Fetch about page data
    const fetchAboutPage = async () => {
      try {
        loading.value = true
        const response = await axios.get('/api/admin/about')
        aboutUs.value = response.data
        
        // Ustaw domyślne style jeśli nie zostały jeszcze ustawione
        if (!aboutUs.value.header_style) {
          aboutUs.value.header_style = 'text-3xl font-bold text-gray-900'
        }
        
        if (aboutUs.value.header_margin) {
          headerOptions.value.margin = aboutUs.value.header_margin
        }
        
        if (aboutUs.value.content_layout) {
          headerOptions.value.contentLayout = aboutUs.value.content_layout
        }
        
        originalAboutUs.value = JSON.parse(JSON.stringify(response.data))
      } catch (error) {
        console.error('Error fetching about page content:', error)
        alertStore.error('Nie udało się załadować danych strony')
      } finally {
        loading.value = false
      }
    }
    
    // Save about page data
    const saveAboutPage = async () => {
      try {
        saving.value = true
        
        // Zapisz opcje nagłówka do obiektu aboutUs
        aboutUs.value.header_margin = headerOptions.value.margin
        aboutUs.value.content_layout = headerOptions.value.contentLayout
        
        const response = await axios.put('/api/admin/about', aboutUs.value)
        originalAboutUs.value = JSON.parse(JSON.stringify(response.data))
        alertStore.success('Strona została zaktualizowana')
      } catch (error) {
        console.error('Error saving about page:', error)
        alertStore.error('Nie udało się zapisać zmian')
      } finally {
        saving.value = false
      }
    }
    
    // Reset form to original data
    const resetForm = () => {
      aboutUs.value = JSON.parse(JSON.stringify(originalAboutUs.value))
      alertStore.info('Zmiany zostały anulowane')
    }
    

    
    onMounted(() => {
      fetchAboutPage()
    })
    
    return {
      aboutUs,
      loading,
      saving,
      saveAboutPage,
      resetForm,
      headerOptions
    }
  }
}
</script>

<style>
.prose img {
  border-radius: 0.375rem;
  max-width: 100%;
  height: auto;
}

.break-words {
  overflow-wrap: break-word;
  word-wrap: break-word;
  word-break: break-word;
  hyphens: auto;
}
</style> 