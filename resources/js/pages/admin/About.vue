<template>
  <div class="p-6 max-w-7xl mx-auto">
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
            
            <div>
              <label for="image_upload" class="block text-sm font-medium text-gray-700">Obrazek strony</label>
              <div class="mt-1 flex items-center space-x-4">
                <div class="flex-shrink-0 h-40 w-40 bg-gray-100 rounded-md overflow-hidden">
                  <img v-if="aboutUs.image_path" :src="getImageUrl(aboutUs.image_path)" alt="About page image" class="h-40 w-40 object-cover">
                  <div v-else class="h-40 w-40 flex items-center justify-center text-gray-400">
                    <svg class="h-12 w-12" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                    </svg>
                  </div>
                </div>
                <div class="flex-1">
                  <input
                    type="file"
                    id="image_upload"
                    ref="fileInput"
                    @change="handleImageUpload"
                    accept="image/*"
                    class="sr-only"
                  />
                  <div class="space-y-2">
                    <label
                      for="image_upload"
                      class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 cursor-pointer"
                    >
                      Wybierz plik
                    </label>
                    <p v-if="imageUploadError" class="text-sm text-red-500">{{ imageUploadError }}</p>
                    <p v-if="uploadingImage" class="text-sm text-indigo-500">Trwa przesyłanie...</p>
                    <p v-else-if="aboutUs.image_path" class="text-sm text-gray-500">
                      <span class="truncate block max-w-xs">
                        {{ aboutUs.image_path.split('/').pop() }}
                      </span>
                      <button 
                        type="button" 
                        @click="removeImage" 
                        class="ml-2 mt-1 text-red-600 hover:text-red-800 text-xs font-medium"
                      >
                        Usuń
                      </button>
                    </p>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Image Position Selector -->
            <div v-if="aboutUs.image_path">
              <label for="image_position" class="block text-sm font-medium text-gray-700">Pozycja obrazka</label>
                  <select 
                    id="image_position" 
                v-model="aboutUs.image_position" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                  >
                    <option value="left">Po lewej stronie</option>
                    <option value="right">Po prawej stronie</option>
                    <option value="top">Na górze</option>
                    <option value="bottom">Na dole</option>
                  </select>
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
          <!-- Left/Right Layout -->
          <div v-if="aboutUs.image_position === 'left' || aboutUs.image_position === 'right'" 
               class="flex flex-col md:flex-row" 
               :class="{ 'md:flex-row-reverse': aboutUs.image_position === 'right' }">
            
            <!-- Image section -->
            <div v-if="aboutUs.image_path" class="w-full md:w-2/5 lg:w-1/3 flex-shrink-0 mb-8 md:mb-0" 
                 :class="{ 
                   'md:mr-12 lg:mr-16': aboutUs.image_position === 'left', 
                   'md:ml-12 lg:ml-16': aboutUs.image_position === 'right' 
                 }">
              <img 
                :src="getImageUrl(aboutUs.image_path)" 
                :alt="aboutUs.title"
                class="w-full h-auto object-cover rounded-lg shadow-md"
              >
            </div>
            
            <!-- Content section -->
            <div class="flex-1">
              <h1 :class="[aboutUs.header_style || 'text-3xl font-bold text-gray-900', headerOptions.margin]">{{ aboutUs.title }}</h1>
              <div :class="['break-words', headerOptions.contentLayout]" v-html="aboutUs.content"></div>
            </div>
          </div>
          
          <!-- Top/Bottom Layout -->
          <div v-else class="space-y-6">
            <!-- Image at the top -->
            <div v-if="aboutUs.image_path && aboutUs.image_position === 'top'" class="mb-8">
              <img 
                :src="getImageUrl(aboutUs.image_path)" 
                :alt="aboutUs.title"
                class="w-full h-auto max-h-96 object-cover rounded-lg shadow-md"
              >
            </div>
            
            <h1 :class="[aboutUs.header_style || 'text-3xl font-bold text-gray-900', headerOptions.margin]">{{ aboutUs.title }}</h1>
            <div :class="['break-words', headerOptions.contentLayout]" v-html="aboutUs.content"></div>
            
            <!-- Image at the bottom -->
            <div v-if="aboutUs.image_path && aboutUs.image_position === 'bottom'" class="mt-10">
              <img 
                :src="getImageUrl(aboutUs.image_path)" 
                :alt="aboutUs.title"
                class="w-full h-auto max-h-96 object-cover rounded-lg shadow-md"
              >
            </div>
          </div>
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

export default {
  name: 'AdminAbout',
  components: {
    RichTextEditor
  },
  setup() {
    const aboutUs = ref({
      title: '',
      content: '',
      image_path: null,
      image_position: 'right',
      header_style: 'text-3xl font-bold text-gray-900'
    })
    const originalAboutUs = ref(null)
    const loading = ref(true)
    const saving = ref(false)
    const uploadingImage = ref(false)
    const imageUploadError = ref('')
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
    
    // Handle image upload
    const handleImageUpload = async (event) => {
      const file = event.target.files[0]
      if (!file) return
      
      const maxSize = 5 * 1024 * 1024 // 5MB
      if (file.size > maxSize) {
        imageUploadError.value = 'Plik jest zbyt duży. Maksymalny rozmiar to 5MB.'
        return
      }
      
      const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp']
      if (!allowedTypes.includes(file.type)) {
        imageUploadError.value = 'Nieprawidłowy format pliku. Dozwolone formaty: JPEG, PNG, GIF, WEBP.'
        return
      }
      
      uploadingImage.value = true
      imageUploadError.value = ''
      
      const formData = new FormData()
      formData.append('image', file)
      
      try {
        const response = await axios.post('/api/admin/upload/image/about', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        })
        
        aboutUs.value.image_path = response.data.path
      } catch (error) {
        console.error('Error uploading image:', error)
        imageUploadError.value = 'Nie udało się przesłać obrazka. Spróbuj ponownie.'
      } finally {
        uploadingImage.value = false
      }
    }
    
    // Remove image
    const removeImage = () => {
      aboutUs.value.image_path = null
    }
    
    // Get the full URL for an image path
    const getImageUrl = (path) => {
      if (!path) return null
      
      // Check if it's already a full URL
      if (path.startsWith('http')) {
        return path
      }
      
      // Otherwise, construct URL from storage path
      return `/storage/${path}`
    }
    
    onMounted(() => {
      fetchAboutPage()
    })
    
    return {
      aboutUs,
      loading,
      saving,
      uploadingImage,
      imageUploadError,
      saveAboutPage,
      resetForm,
      handleImageUpload,
      removeImage,
      getImageUrl,
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