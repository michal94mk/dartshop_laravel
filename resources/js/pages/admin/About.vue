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
        
        <!-- Form Actions -->
        <div class="mt-8 flex flex-col sm:flex-row sm:justify-between">
          <div class="flex space-x-3 mb-4 sm:mb-0">
            <button 
              type="button" 
              @click="fetchVersionHistory" 
              class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              Historia wersji
            </button>
            
            <button 
              type="button" 
              @click="previewAboutPage" 
              class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
              </svg>
              Podgląd
            </button>
          </div>
          
          <div class="flex space-x-3">
            <button 
              type="button"
              @click="openSaveDraftModal"
              class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
              </svg>
              Zapisz wersję roboczą
            </button>
            
            <button 
              type="submit"
              :disabled="saving"
              class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              <svg v-if="saving" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ saving ? 'Zapisywanie...' : 'Zapisz i opublikuj' }}
            </button>
          </div>
        </div>
      </form>
    </div>
    
    <!-- About Page Preview Modal -->
    <div v-if="showPreviewModal" class="fixed z-20 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="showPreviewModal = false"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-6xl sm:w-full">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="flex justify-between items-center mb-4">
              <h3 class="text-lg leading-6 font-medium text-gray-900">Podgląd strony "O nas"</h3>
              <button @click="showPreviewModal = false" class="text-gray-400 hover:text-gray-500">
                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
            
            <div class="bg-gray-50 p-6 rounded-lg">
              <!-- Banner with image -->
              <div class="relative h-64 w-full overflow-hidden rounded-lg mb-8">
                <img 
                  v-if="aboutPage.image_url" 
                  :src="aboutPage.image_url" 
                  class="w-full h-full object-cover"
                  alt="O nas"
                />
                <div v-else class="w-full h-full bg-gray-200 flex items-center justify-center">
                  <span class="text-gray-500">Brak obrazu</span>
                </div>
                <div class="absolute inset-0 bg-gradient-to-b from-transparent to-black opacity-60"></div>
                <div class="absolute bottom-0 left-0 p-6 text-white">
                  <h1 class="text-3xl font-bold">{{ aboutPage.title }}</h1>
                  <p class="text-xl">{{ aboutPage.subtitle }}</p>
                </div>
              </div>
              
              <!-- Main content -->
              <div class="prose prose-indigo max-w-none mb-8" v-html="renderedContent"></div>
              
              <!-- Mission, Vision, Values -->
              <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white p-4 rounded-lg shadow">
                  <h3 class="text-lg font-medium text-gray-900 mb-2">Nasza misja</h3>
                  <p class="text-gray-700" v-html="renderedMission"></p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow">
                  <h3 class="text-lg font-medium text-gray-900 mb-2">Nasza wizja</h3>
                  <p class="text-gray-700" v-html="renderedVision"></p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow">
                  <h3 class="text-lg font-medium text-gray-900 mb-2">Nasze wartości</h3>
                  <p class="text-gray-700" v-html="renderedValues"></p>
                </div>
              </div>
              
              <!-- Team members -->
              <div v-if="aboutPage.team && aboutPage.team.length > 0">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Nasz zespół</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                  <div v-for="(member, index) in aboutPage.team" :key="index" class="bg-white p-4 rounded-lg shadow text-center">
                    <div class="w-24 h-24 mx-auto rounded-full overflow-hidden mb-4">
                      <img v-if="member.photo_url" :src="member.photo_url" alt="Team member" class="w-full h-full object-cover" />
                      <div v-else class="w-full h-full bg-gray-200 flex items-center justify-center">
                        <span class="text-gray-500 text-xl">{{ member.name.charAt(0) }}</span>
                      </div>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">{{ member.name }}</h3>
                    <p class="text-sm text-gray-500">{{ member.position }}</p>
                    <p v-if="member.bio" class="mt-2 text-sm text-gray-700">{{ member.bio }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button
              type="button"
              @click="showPreviewModal = false"
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
            >
              Zamknij podgląd
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Version History Modal -->
    <div v-if="showHistoryModal" class="fixed z-20 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="showHistoryModal = false"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
            <div class="flex justify-between items-center mb-4">
              <h3 class="text-lg leading-6 font-medium text-gray-900">Historia zmian strony "O nas"</h3>
              <button @click="showHistoryModal = false" class="text-gray-400 hover:text-gray-500">
                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
            
            <div class="flow-root mt-6">
              <ul v-if="versionHistory.length" class="-mb-8">
                <li v-for="(version, index) in versionHistory" :key="index">
                  <div class="relative pb-8">
                    <span v-if="index !== versionHistory.length - 1" class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                    <div class="relative flex space-x-3">
                      <div>
                        <span class="h-8 w-8 rounded-full bg-indigo-500 flex items-center justify-center ring-8 ring-white">
                          <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
                          </svg>
                        </span>
                      </div>
                      <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                        <div>
                          <p class="text-sm text-gray-500">
                            <span class="font-medium text-gray-900">Wersja {{ version.version }}</span>
                            <span v-if="version.is_current" class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                              Aktualnie opublikowana
                            </span>
                          </p>
                          <p class="mt-1 text-xs text-gray-500">
                            Zmodyfikowano przez: <span class="font-medium">{{ version.modified_by }}</span>
                          </p>
                        </div>
                        <div class="text-right text-sm whitespace-nowrap text-gray-500">
                          <time :datetime="version.updated_at">{{ formatDate(version.updated_at) }}</time>
                          <div class="mt-2 flex space-x-2 justify-end">
                            <button 
                              @click="previewVersion(version)" 
                              class="text-indigo-600 hover:text-indigo-900 text-xs"
                            >
                              Podgląd
                            </button>
                            <button 
                              v-if="!version.is_current" 
                              @click="restoreVersion(version.id)" 
                              class="text-indigo-600 hover:text-indigo-900 text-xs"
                            >
                              Przywróć
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </li>
              </ul>
              <div v-else class="text-center py-8 text-gray-500">
                Brak historii zmian
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button
              type="button"
              @click="showHistoryModal = false"
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
            >
              Zamknij
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Saving as Draft Modal Confirmation -->
    <div v-if="showSaveDraftModal" class="fixed z-20 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="showSaveDraftModal = false"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
              <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                <svg class="h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                </svg>
              </div>
              <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                  Zapisać jako wersję roboczą?
                </h3>
                <div class="mt-2">
                  <p class="text-sm text-gray-500">
                    Ta wersja zostanie zapisana, ale nie będzie widoczna dla użytkowników. Będziesz mógł ją opublikować później.
                  </p>
                </div>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button 
              type="button" 
              @click="saveAboutPageAsDraft" 
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm"
            >
              Zapisz wersję roboczą
            </button>
            <button 
              type="button" 
              @click="showSaveDraftModal = false" 
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
            >
              Anuluj
            </button>
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
import { marked } from 'marked'
import DOMPurify from 'dompurify'

export default {
  name: 'AdminAbout',
  setup() {
    const alertStore = useAlertStore()
    const loading = ref(true)
    const saving = ref(false)
    const showPreviewModal = ref(false)
    const showHistoryModal = ref(false)
    const showSaveDraftModal = ref(false)
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
      meta_description: '',
      is_draft: false
    })
    const versionHistory = ref([])
    
    // Computed
    const sortedHistory = computed(() => {
      if (!aboutPage.value.history) return []
      return [...aboutPage.value.history].sort((a, b) => a.year - b.year)
    })
    
    // Markdown rendering
    const renderedContent = computed(() => {
      return DOMPurify.sanitize(marked(aboutPage.value.content || ''))
    })
    
    const renderedMission = computed(() => {
      return DOMPurify.sanitize(marked(aboutPage.value.mission || ''))
    })
    
    const renderedVision = computed(() => {
      return DOMPurify.sanitize(marked(aboutPage.value.vision || ''))
    })
    
    const renderedValues = computed(() => {
      return DOMPurify.sanitize(marked(aboutPage.value.values || ''))
    })
    
    // Fetch about page data
    const fetchAboutPage = async () => {
      try {
        loading.value = true
        const response = await axios.get('/api/admin/about')
        aboutPage.value = response.data
        
        // Ensure team and history are arrays
        if (!aboutPage.value.team) aboutPage.value.team = []
        if (!aboutPage.value.history) aboutPage.value.history = []
      } catch (error) {
        console.error('Error fetching about page:', error)
        alertStore.setErrorMessage('Wystąpił błąd podczas pobierania danych strony "O nas".')
      } finally {
        loading.value = false
      }
    }
    
    // Fetch version history
    const fetchVersionHistory = async () => {
      try {
        const response = await axios.get('/api/admin/about/history')
        versionHistory.value = response.data
        showHistoryModal.value = true
      } catch (error) {
        console.error('Error fetching version history:', error)
        alertStore.setErrorMessage('Wystąpił błąd podczas pobierania historii wersji.')
      }
    }
    
    // Save about page
    const saveAboutPage = async () => {
      try {
        saving.value = true
        
        const response = await axios.post('/api/admin/about', {
          ...aboutPage.value,
          is_draft: false
        })
        
        alertStore.setSuccessMessage('Strona "O nas" została zaktualizowana i opublikowana.')
      } catch (error) {
        console.error('Error saving about page:', error)
        alertStore.setErrorMessage('Wystąpił błąd podczas zapisywania strony "O nas".')
      } finally {
        saving.value = false
      }
    }
    
    // Save about page as draft
    const saveAboutPageAsDraft = async () => {
      try {
        saving.value = true
        
        const response = await axios.post('/api/admin/about', {
          ...aboutPage.value,
          is_draft: true
        })
        
        showSaveDraftModal.value = false
        alertStore.setSuccessMessage('Strona "O nas" została zapisana jako wersja robocza.')
      } catch (error) {
        console.error('Error saving about page as draft:', error)
        alertStore.setErrorMessage('Wystąpił błąd podczas zapisywania wersji roboczej.')
      } finally {
        saving.value = false
      }
    }
    
    // Preview a specific version
    const previewVersion = async (version) => {
      try {
        const response = await axios.get(`/api/admin/about/history/${version.id}`)
        const versionData = response.data
        
        // Create a temporary copy for preview
        const previewData = { ...versionData }
        aboutPage.value = previewData
        
        showHistoryModal.value = false
        showPreviewModal.value = true
        
        // After preview is closed, restore the current version
        setTimeout(() => {
          fetchAboutPage()
        }, 300)
      } catch (error) {
        console.error('Error fetching version details:', error)
        alertStore.setErrorMessage('Wystąpił błąd podczas pobierania wersji do podglądu.')
      }
    }
    
    // Restore a specific version
    const restoreVersion = async (versionId) => {
      if (!confirm('Czy na pewno chcesz przywrócić tę wersję? Bieżąca wersja zostanie zastąpiona.')) {
        return
      }
      
      try {
        const response = await axios.post(`/api/admin/about/history/${versionId}/restore`)
        
        showHistoryModal.value = false
        alertStore.setSuccessMessage('Wersja została przywrócona pomyślnie.')
        fetchAboutPage()
      } catch (error) {
        console.error('Error restoring version:', error)
        alertStore.setErrorMessage('Wystąpił błąd podczas przywracania wersji.')
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
    
    // Preview about page
    const previewAboutPage = () => {
      showPreviewModal.value = true
    }
    
    // Open save draft modal
    const openSaveDraftModal = () => {
      showSaveDraftModal.value = true
    }
    
    // Format date
    const formatDate = (dateString) => {
      const options = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' }
      return new Date(dateString).toLocaleDateString('pl-PL', options)
    }
    
    // Lifecycle
    onMounted(() => {
      fetchAboutPage()
    })
    
    return {
      loading,
      saving,
      aboutPage,
      versionHistory,
      showPreviewModal,
      showHistoryModal,
      showSaveDraftModal,
      renderedContent,
      renderedMission,
      renderedVision,
      renderedValues,
      fetchAboutPage,
      fetchVersionHistory,
      saveAboutPage,
      addTeamMember,
      removeTeamMember,
      addHistoryMilestone,
      removeHistoryMilestone,
      previewAboutPage,
      previewVersion,
      restoreVersion,
      saveAboutPageAsDraft,
      openSaveDraftModal,
      formatDate,
      sortedHistory
    }
  }
}
</script> 