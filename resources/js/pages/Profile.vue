<template>
  <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-blue-50 to-purple-50">
    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="text-center mb-12">
        <div class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-100 to-purple-100 rounded-full text-indigo-700 font-semibold text-sm mb-4">
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
          </svg>
          Profil użytkownika
        </div>
        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
          Twój Profil
        </h1>
        <p class="text-gray-600 max-w-2xl mx-auto">
          Zarządzaj swoim kontem, przeglądaj zamówienia i ulubione produkty
        </p>
      </div>

      <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">
        <div class="p-8">
          <!-- Loading state -->
          <div v-if="authStore.isLoading" class="text-center py-16">
            <div class="w-16 h-16 border-4 border-indigo-500 border-t-transparent rounded-full animate-spin mx-auto"></div>
            <p class="mt-4 text-gray-500 font-medium">Ładowanie danych...</p>
          </div>
          
          <!-- Not logged in state -->
          <div v-else-if="!authStore.isLoggedIn && authStore.authInitialized" class="text-center py-16">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
              <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
              </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-3">Nie jesteś zalogowany</h3>
            <p class="text-gray-600 mb-6">Zaloguj się, aby uzyskać dostęp do swojego profilu</p>
            <router-link 
              to="/login" 
              class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 shadow-lg hover:shadow-xl"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
              </svg>
              Zaloguj się
            </router-link>
          </div>
          
          <!-- Main content when logged in -->
          <div v-else>
            <!-- Tabs navigation -->
            <div class="border-b border-gray-200 mb-8">
              <nav class="flex space-x-8" aria-label="Tabs">
                <button 
                  @click="activeTab = 'profile'" 
                  class="py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200"
                  :class="activeTab === 'profile' 
                    ? 'border-indigo-500 text-indigo-600' 
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                >
                  <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Profil
                  </div>
                </button>
                <button 
                  @click="activeTab = 'favorites'" 
                  class="py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200"
                  :class="activeTab === 'favorites' 
                    ? 'border-indigo-500 text-indigo-600' 
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                >
                  <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                    Ulubione produkty
                  </div>
                </button>
                <button 
                  @click="activeTab = 'orders'" 
                  class="py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200"
                  :class="activeTab === 'orders' 
                    ? 'border-indigo-500 text-indigo-600' 
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                >
                  <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M8 11v6a2 2 0 002 2h4a2 2 0 002-2v-6M8 11h8"/>
                    </svg>
                    Zamówienia
                  </div>
                </button>
                <button 
                  @click="activeTab = 'reviews'" 
                  class="py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200"
                  :class="activeTab === 'reviews' 
                    ? 'border-indigo-500 text-indigo-600' 
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                >
                  <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                    </svg>
                    Moje opinie
                  </div>
                </button>
              </nav>
            </div>
            
                         <!-- Profile tab content -->
             <div v-if="activeTab === 'profile'">
               <!-- Profile data -->
               <div v-if="!showChangePassword" class="max-w-2xl mx-auto">
                 <div class="text-center mb-8">
                   <div class="w-24 h-24 rounded-full bg-gradient-to-r from-indigo-500 to-purple-500 flex items-center justify-center text-white text-3xl font-bold mx-auto mb-4">
                     {{ authStore.userInitial }}
                   </div>
                   <h3 class="text-2xl font-bold text-gray-900">{{ authStore.userName }}</h3>
                 </div>
                 
                 <div class="space-y-6">
                   <div class="bg-gray-50 rounded-lg p-6">
                     <div class="flex items-center mb-3">
                       <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                       </svg>
                       <h4 class="text-sm font-semibold text-gray-600">Imię i nazwisko</h4>
                     </div>
                     <p class="text-gray-900 font-medium">{{ authStore.userName }}</p>
                   </div>
                   
                   <div class="bg-gray-50 rounded-lg p-6">
                     <div class="flex items-center mb-3">
                       <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                       </svg>
                       <h4 class="text-sm font-semibold text-gray-600">Email</h4>
                     </div>
                     <p class="text-gray-900 font-medium">{{ authStore.userEmail }}</p>
                     
                     <!-- Email verification status -->
                     <div v-if="authStore.isEmailVerified || authStore.isGoogleUser" class="mt-3 flex items-center text-sm text-green-600">
                       <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                       </svg>
                       {{ authStore.isGoogleUser ? 'Zweryfikowany przez Google' : 'Zweryfikowany' }}
                     </div>
                     <div v-else-if="!authStore.isGoogleUser" class="mt-3">
                       <div class="flex items-center text-sm text-red-600 mb-2">
                         <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                         </svg>
                         Niezweryfikowany
                       </div>
                       <button 
                         @click="resendVerification" 
                         class="text-sm text-indigo-600 hover:text-indigo-700 font-medium"
                         :disabled="isVerificationLoading"
                       >
                         {{ isVerificationLoading ? 'Wysyłanie...' : 'Wyślij link weryfikacyjny' }}
                       </button>
                     </div>
                   </div>
                   
                   <div v-if="status" class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-lg">
                     <div class="flex">
                       <div class="flex-shrink-0">
                         <svg class="h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                           <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                         </svg>
                       </div>
                       <div class="ml-3">
                         <p class="font-medium">{{ status }}</p>
                       </div>
                     </div>
                   </div>
                   
                   <div class="flex flex-col sm:flex-row gap-4">
                     <!-- Password change button - hidden for Google OAuth users -->
                     <button 
                       v-if="!authStore.isGoogleUser"
                       @click="showChangePassword = true" 
                       class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 shadow-lg hover:shadow-xl"
                     >
                       <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1721 9z"/>
                       </svg>
                       Zmień hasło
                     </button>
                     
                     <!-- Info for Google OAuth users with logout button -->
                     <div v-else class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                       <div class="flex items-start justify-between">
                         <div class="flex items-start flex-grow">
                           <svg class="w-5 h-5 text-blue-600 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                           </svg>
                           <div class="flex-grow">
                             <p class="text-sm font-medium text-blue-800 mb-1">Konto Google OAuth</p>
                             <p class="text-sm text-blue-700">
                               Jesteś zalogowany przez Google. Aby zmienić hasło, przejdź do 
                               <a href="https://myaccount.google.com/security" target="_blank" rel="noopener noreferrer" class="font-medium underline hover:no-underline">
                                 ustawień bezpieczeństwa Google
                               </a>.
                             </p>
                           </div>
                         </div>
                         <button 
                           @click="handleLogout" 
                           class="inline-flex items-center px-3 py-2 ml-4 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 flex-shrink-0"
                           :disabled="isLoggingOut"
                         >
                           <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                           </svg>
                           <span v-if="isLoggingOut">Wylogowywanie...</span>
                           <span v-else>Wyloguj</span>
                         </button>
                       </div>
                     </div>
                   </div>
                 </div>
               </div>
               
               <!-- Password change form - only for non-Google users -->
               <div v-else-if="!authStore.isGoogleUser" class="max-w-2xl mx-auto">
                 <div class="mb-8">
                   <h3 class="text-2xl font-bold text-gray-900 mb-2">Zmiana hasła</h3>
                   <p class="text-gray-600">Wprowadź swoje aktualne hasło oraz nowe hasło</p>
                 </div>
                 
                 <div v-if="authStore.hasError" class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6">
                   <div class="flex">
                     <div class="flex-shrink-0">
                       <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                         <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                       </svg>
                     </div>
                     <div class="ml-3">
                       <p class="font-medium">{{ authStore.errorMessage || 'Wystąpił błąd podczas zmiany hasła.' }}</p>
                     </div>
                   </div>
                 </div>
                 
                 <div v-if="passwordChangeStatus" class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-6">
                   <div class="flex">
                     <div class="flex-shrink-0">
                       <svg class="h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                         <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                       </svg>
                     </div>
                     <div class="ml-3">
                       <p class="font-medium">{{ passwordChangeStatus }}</p>
                     </div>
                   </div>
                 </div>
                 
                 <form @submit.prevent="handlePasswordChange" class="space-y-6">
                   <div>
                     <label for="current-password" class="block text-sm font-semibold text-gray-700 mb-2">Aktualne hasło</label>
                     <input 
                       id="current-password" 
                       type="password" 
                       v-model="currentPassword" 
                       required
                       placeholder="Wprowadź aktualne hasło"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200"
                     />
                   </div>
                   
                   <div>
                     <label for="new-password" class="block text-sm font-semibold text-gray-700 mb-2">Nowe hasło</label>
                     <input 
                       id="new-password" 
                       type="password" 
                       v-model="newPassword" 
                       required
                       placeholder="Wprowadź nowe hasło"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200"
                     />
                   </div>
                   
                   <div>
                     <label for="new-password-confirmation" class="block text-sm font-semibold text-gray-700 mb-2">Powtórz nowe hasło</label>
                     <input 
                       id="new-password-confirmation" 
                       type="password" 
                       v-model="newPasswordConfirmation" 
                       required
                       placeholder="Powtórz nowe hasło"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200"
                     />
                   </div>
                   
                   <div class="flex flex-col sm:flex-row gap-4">
                     <button 
                       type="submit" 
                       class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 shadow-lg hover:shadow-xl"
                       :disabled="authStore.isLoading"
                     >
                       <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                       </svg>
                       <span v-if="authStore.isLoading">Zapisywanie...</span>
                       <span v-else>Zapisz nowe hasło</span>
                     </button>
                     
                     <button 
                       type="button"
                       @click="showChangePassword = false" 
                       class="inline-flex items-center px-6 py-3 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200"
                     >
                       <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                       </svg>
                       Anuluj
                     </button>
                   </div>
                 </form>
               </div>
                          </div>
             
             <!-- Favorites tab content -->
             <div v-else-if="activeTab === 'favorites'">
               <div class="mb-8">
                 <h3 class="text-2xl font-bold text-gray-900 mb-2 flex items-center">
                   <svg class="w-6 h-6 text-indigo-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                   </svg>
                   Ulubione produkty
                 </h3>
                 <p class="text-gray-600">Twoja lista ulubionych produktów</p>
               </div>
               
               <div v-if="loadingFavorites" class="text-center py-16">
                 <div class="w-16 h-16 border-4 border-indigo-500 border-t-transparent rounded-full animate-spin mx-auto"></div>
                 <p class="mt-4 text-gray-500 font-medium">Ładowanie ulubionych produktów...</p>
               </div>
               
               <div v-else-if="favorites.length === 0" class="text-center py-16">
                 <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                   <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                   </svg>
                 </div>
                 <h4 class="text-xl font-bold text-gray-900 mb-3">Brak ulubionych produktów</h4>
                 <p class="text-gray-600 mb-6">Nie masz jeszcze żadnych ulubionych produktów.</p>
                 <router-link 
                   to="/products" 
                   class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 shadow-lg hover:shadow-xl"
                 >
                   <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M8 11v6a2 2 0 002 2h4a2 2 0 002-2v-6M8 11h8"/>
                   </svg>
                   Przeglądaj produkty
                 </router-link>
               </div>
               
               <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                 <div v-for="product in favorites" :key="product.id" class="bg-gray-50 rounded-lg p-6 hover:shadow-lg transition-shadow duration-200">
                   <div class="flex">
                     <div class="w-20 h-20 flex-shrink-0 bg-white rounded-lg overflow-hidden shadow-sm">
                       <img 
                         :src="getProductImageUrl(product.image_url, product.name, 80, 80)" 
                         :alt="product.name"
                         class="w-full h-full object-cover"
                         @error="(e) => handleImageError(e, product.name, 80, 80)"
                       />
                     </div>
                     <div class="ml-4 flex-grow">
                       <h4 class="font-semibold text-gray-900 mb-2">{{ product.name }}</h4>
                       <!-- Price section with promotion support -->
                       <div v-if="hasPromotion(product)" class="space-y-1 mb-3">
                         <!-- Original price (crossed out) -->
                         <div class="text-sm text-gray-500 line-through">
                           {{ formatPrice(product.price) }} zł
                         </div>
                         <!-- Promotional price -->
                         <div class="flex items-center space-x-2">
                           <p class="text-lg font-bold text-red-600">{{ formatPrice(product.promotion_price) }} zł</p>
                           <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                             -{{ getDiscountPercentage(product) }}%
                           </span>
                         </div>
                       </div>
                       <!-- Regular price (no promotion) -->
                       <div v-else class="mb-3">
                         <p class="text-indigo-600 font-bold text-lg">{{ formatPrice(product.price) }} zł</p>
                       </div>
                       <div class="flex flex-col gap-2">
                         <router-link 
                           :to="`/products/${product.id}`" 
                           class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-700 font-medium"
                         >
                           <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                           </svg>
                           Zobacz produkt
                         </router-link>
                         <button 
                           @click="removeFromFavorites(product.id)" 
                           class="inline-flex items-center text-sm text-red-600 hover:text-red-700 font-medium"
                         >
                           <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                           </svg>
                           Usuń z ulubionych
                         </button>
                       </div>
                     </div>
                   </div>
                 </div>
               </div>
             </div>
             
             <!-- Orders tab content -->
             <div v-else-if="activeTab === 'orders'">
               <div class="mb-8">
                 <h3 class="text-2xl font-bold text-gray-900 mb-2 flex items-center">
                   <svg class="w-6 h-6 text-indigo-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M8 11v6a2 2 0 002 2h4a2 2 0 002-2v-6M8 11h8"/>
                   </svg>
                   Twoje zamówienia
                 </h3>
                 <p class="text-gray-600">Historia wszystkich Twoich zamówień</p>
               </div>
               
               <div v-if="loadingOrders" class="text-center py-16">
                 <div class="w-16 h-16 border-4 border-indigo-500 border-t-transparent rounded-full animate-spin mx-auto"></div>
                 <p class="mt-4 text-gray-500 font-medium">Ładowanie zamówień...</p>
               </div>
               
               <div v-else-if="orders.length === 0" class="text-center py-16">
                 <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                   <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M8 11v6a2 2 0 002 2h4a2 2 0 002-2v-6M8 11h8"/>
                   </svg>
                 </div>
                 <h4 class="text-xl font-bold text-gray-900 mb-3">Brak zamówień</h4>
                 <p class="text-gray-600 mb-6">Nie złożyłeś jeszcze żadnych zamówień.</p>
                 <router-link 
                   to="/products" 
                   class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 shadow-lg hover:shadow-xl"
                 >
                   <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M8 11v6a2 2 0 002 2h4a2 2 0 002-2v-6M8 11h8"/>
                   </svg>
                   Rozpocznij zakupy
                 </router-link>
               </div>
               
               <div v-else class="space-y-6">
                 <div v-for="order in orders" :key="order.id" class="bg-gray-50 rounded-lg p-6 hover:shadow-lg transition-shadow duration-200">
                   <div class="flex justify-between items-start mb-4">
                     <div>
                       <div class="flex items-center mb-2">
                         <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                         </svg>
                         <span class="text-sm font-semibold text-gray-600">Zamówienie #{{ order.id }}</span>
                       </div>
                       <p class="font-semibold text-gray-900">{{ formatDate(order.created_at) }}</p>
                     </div>
                     <div class="text-right">
                       <p class="text-sm font-semibold text-gray-600 mb-1">Status</p>
                       <span class="inline-flex items-center px-3 py-1 text-sm font-semibold rounded-full" :class="getOrderStatusClass(order.status)">
                         {{ getOrderStatusText(order.status) }}
                       </span>
                     </div>
                   </div>
                   
                   <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                     <div class="bg-white rounded-lg p-4">
                       <p class="text-sm font-semibold text-gray-600 mb-1">Wartość zamówienia</p>
                       <p class="text-lg font-bold text-indigo-600">{{ order.total_formatted }}</p>
                     </div>
                     <div v-if="order.shipping_method" class="bg-white rounded-lg p-4">
                       <p class="text-sm font-semibold text-gray-600 mb-1">Dostawa</p>
                       <p class="font-medium text-gray-900">{{ order.shipping_method }}</p>
                     </div>
                     <div v-if="order.payment_method" class="bg-white rounded-lg p-4">
                       <p class="text-sm font-semibold text-gray-600 mb-1">Płatność</p>
                       <p class="font-medium text-gray-900">{{ order.payment_method }}</p>
                     </div>
                   </div>
                   
                   <div class="border-t border-gray-200 pt-4">
                     <button 
                       @click="toggleOrderDetails(order.id)" 
                       class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-700 font-medium"
                     >
                       <span v-if="expandedOrderId === order.id">Ukryj szczegóły</span>
                       <span v-else>Zobacz szczegóły</span>
                       <svg 
                         class="w-4 h-4 ml-2 transition-transform duration-200" 
                         :class="expandedOrderId === order.id ? 'transform rotate-180' : ''"
                         fill="none" 
                         stroke="currentColor" 
                         viewBox="0 0 24 24"
                       >
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                       </svg>
                     </button>
                     
                     <div v-if="expandedOrderId === order.id" class="mt-4 space-y-3">
                       <div v-for="item in order.items" :key="item.id" class="flex items-center bg-white rounded-lg p-4">
                         <div class="w-12 h-12 flex-shrink-0 bg-gray-100 rounded-lg overflow-hidden">
                           <img 
                             :src="item.product.image_url || '/images/placeholder.png'" 
                             :alt="item.product.name"
                             class="w-full h-full object-cover"
                           />
                         </div>
                         <div class="ml-4 flex-grow">
                           <div class="flex justify-between items-start">
                             <div>
                               <p class="font-semibold text-gray-900">{{ item.product.name }}</p>
                               <p class="text-sm text-gray-500">Ilość: {{ item.quantity }}</p>
                             </div>
                             <p class="font-bold text-indigo-600">{{ item.price_formatted }}</p>
                           </div>
                         </div>
                       </div>
                     </div>
                   </div>
                 </div>
               </div>
             </div>
             
             <!-- Reviews tab content -->
             <div v-else-if="activeTab === 'reviews'">
               <div class="mb-8">
                 <h3 class="text-2xl font-bold text-gray-900 mb-2 flex items-center">
                   <svg class="w-6 h-6 text-indigo-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                   </svg>
                   Twoje opinie
                 </h3>
                 <p class="text-gray-600">Wszystkie opinie, które dodałeś do produktów</p>
               </div>
               
               <div v-if="loadingReviews" class="text-center py-16">
                 <div class="w-16 h-16 border-4 border-indigo-500 border-t-transparent rounded-full animate-spin mx-auto"></div>
                 <p class="mt-4 text-gray-500 font-medium">Ładowanie opinii...</p>
               </div>
               
               <div v-else-if="reviews.length === 0" class="text-center py-16">
                 <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                   <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                   </svg>
                 </div>
                 <h4 class="text-xl font-bold text-gray-900 mb-3">Brak opinii</h4>
                 <p class="text-gray-600 mb-6">Nie dodałeś jeszcze żadnych opinii.</p>
                 <router-link 
                   to="/products" 
                   class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 shadow-lg hover:shadow-xl"
                 >
                   <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M8 11v6a2 2 0 002 2h4a2 2 0 002-2v-6M8 11h8"/>
                   </svg>
                   Przeglądaj produkty
                 </router-link>
               </div>
               
               <div v-else class="space-y-6">
                 <div v-for="review in reviews" :key="review.id" class="bg-gray-50 rounded-lg p-6 hover:shadow-lg transition-shadow duration-200">
                   <div class="flex justify-between items-start mb-4">
                     <div class="flex-grow">
                       <h4 class="font-semibold text-gray-900 text-lg mb-2">{{ review.product.name }}</h4>
                       <div class="flex items-center mb-2">
                         <!-- Stars rating -->
                         <div class="flex mr-3">
                           <template v-for="i in 5" :key="i">
                             <svg 
                               class="w-5 h-5" 
                               :class="i <= review.rating ? 'text-yellow-400' : 'text-gray-300'"
                               fill="currentColor" 
                               viewBox="0 0 20 20" 
                               xmlns="http://www.w3.org/2000/svg"
                             >
                               <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                             </svg>
                           </template>
                         </div>
                         <span class="text-sm text-gray-500">{{ formatDate(review.created_at) }}</span>
                       </div>
                     </div>
                     <div class="flex-shrink-0 ml-4">
                       <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full" :class="review.is_approved ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'">
                         <svg v-if="review.is_approved" class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                         </svg>
                         <svg v-else class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                         </svg>
                         {{ review.is_approved ? 'Zatwierdzona' : 'Oczekuje' }}
                       </span>
                     </div>
                   </div>
                   
                   <div v-if="review.title" class="mb-3">
                     <h5 class="font-semibold text-gray-900">{{ review.title }}</h5>
                   </div>
                   
                   <div class="mb-4">
                     <p class="text-gray-700 leading-relaxed">{{ review.content }}</p>
                   </div>
                   
                   <div class="border-t border-gray-200 pt-4">
                     <router-link 
                       :to="`/products/${review.product.id}`" 
                       class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-700 font-medium"
                     >
                       <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                       </svg>
                       Zobacz produkt
                     </router-link>
                   </div>
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
import { ref, onMounted, watch } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/authStore';
import { useFavoriteStore } from '../stores/favoriteStore';
import { useAlertStore } from '../stores/alertStore';
import { getProductImageUrl, handleImageError } from '../utils/imageHelpers';
import axios from 'axios';

export default {
  name: 'ProfilePage',
  
  setup() {
    // Auth related refs
    const isLoggingOut = ref(false);
    const isVerificationLoading = ref(false);
    const showChangePassword = ref(false);
    const currentPassword = ref('');
    const newPassword = ref('');
    const newPasswordConfirmation = ref('');
    const status = ref('');
    const passwordChangeStatus = ref('');
    
    // Tab management
    const activeTab = ref('profile');
    
    // Favorites tab
    const loadingFavorites = ref(false);
    const favorites = ref([]);
    
    // Orders tab
    const loadingOrders = ref(false);
    const orders = ref([]);
    const expandedOrderId = ref(null);
    
    // Reviews tab
    const loadingReviews = ref(false);
    const reviews = ref([]);
    
    const router = useRouter();
    const authStore = useAuthStore();
    const favoriteStore = useFavoriteStore();
    const alertStore = useAlertStore();
    
    // Fetch data when tab changes
    watch(activeTab, (newTab) => {
      if (newTab === 'favorites') {
        fetchFavorites();
      } else if (newTab === 'orders') {
        fetchOrders();
      } else if (newTab === 'reviews') {
        fetchReviews();
      }
    });
    
    const handleVerificationStatus = () => {
      const verified = router.currentRoute.value.query.verified;
      
      // If user is not logged in but has verification status, redirect to login with parameters
      if (verified && !authStore.isLoggedIn) {
        console.log('User not logged in, redirecting to login with verification status');
        router.push(`/login?redirect=/profile&verified=${verified}`);
        return;
      }
      
      if (verified === 'success') {
        alertStore.success('🎉 Email został pomyślnie zweryfikowany!', 4000);
        authStore.refreshUser();
        // Remove the query parameter from URL
        router.replace({ query: {} });
      } else if (verified === 'already') {
        alertStore.info('ℹ️ Twój email jest już zweryfikowany.', 3000);
        router.replace({ query: {} });
      } else if (verified === 'invalid') {
        alertStore.error('❌ Link weryfikacyjny jest nieprawidłowy lub wygasł. Spróbuj ponownie.', 5000);
        router.replace({ query: {} });
      }
    };
    
    onMounted(async () => {
      // Initialize auth state ONLY if it hasn't been initialized yet
      if (!authStore.authInitialized) {
        console.log('Profile: Auth not initialized, initializing...');
        await authStore.initAuth();
      } else {
        console.log('Profile: Auth already initialized, skipping initAuth');
        console.log('Profile: Current auth state:', {
          isLoggedIn: authStore.isLoggedIn,
          user: authStore.user?.email,
          authInitialized: authStore.authInitialized
        });
      }
      
      // Initialize the favorite store
      favoriteStore.initializeFavorites();
      
      // Handle email verification status
      handleVerificationStatus();
    });
    
    // Profile tab methods
    const handleLogout = async () => {
      isLoggingOut.value = true;
      
      try {
        // Show logout message immediately
        alertStore.success('👋 Do zobaczenia! Zostałeś pomyślnie wylogowany.', 5000);
        
        // Small delay to show message
        await new Promise(resolve => setTimeout(resolve, 100));
        
        const success = await authStore.logout();
        
        if (success) {
          // Navigate to home
          router.push('/');
        }
      } catch (error) {
        console.error('Logout error:', error);
        alertStore.error('Wystąpił błąd podczas wylogowywania.');
      } finally {
        isLoggingOut.value = false;
      }
    };
    
    const resendVerification = async () => {
      // Prevent email verification for Google OAuth users
      if (authStore.isGoogleUser) {
        authStore.hasError = true;
        authStore.errorMessage = 'Użytkownicy Google OAuth mają już zweryfikowany email.';
        return;
      }
      
      isVerificationLoading.value = true;
      
      try {
        const message = await authStore.resendVerificationEmail();
        if (message) {
          status.value = message;
          setTimeout(() => {
            status.value = '';
          }, 5000);
        }
      } finally {
        isVerificationLoading.value = false;
      }
    };
    
    const handlePasswordChange = async () => {
      // Prevent password change for Google OAuth users
      if (authStore.isGoogleUser) {
        authStore.hasError = true;
        authStore.errorMessage = 'Użytkownicy Google OAuth nie mogą zmieniać hasła w tej aplikacji.';
        return;
      }
      
      passwordChangeStatus.value = '';
      
      if (newPassword.value !== newPasswordConfirmation.value) {
        authStore.hasError = true;
        authStore.errorMessage = 'Nowe hasła nie są identyczne.';
        return;
      }
      
      const success = await authStore.updatePassword(
        currentPassword.value,
        newPassword.value,
        newPasswordConfirmation.value
      );
      
      if (success) {
        passwordChangeStatus.value = 'Hasło zostało pomyślnie zmienione.';
        currentPassword.value = '';
        newPassword.value = '';
        newPasswordConfirmation.value = '';
        
        setTimeout(() => {
          showChangePassword.value = false;
          passwordChangeStatus.value = '';
        }, 2000);
      }
    };
    
    // Favorites tab methods
    const fetchFavorites = async () => {
      if (!authStore.isLoggedIn) return;
      
      loadingFavorites.value = true;
      
      try {
        // Load favorites from API
        await favoriteStore.loadFavorites();
        favorites.value = favoriteStore.favorites;
      } catch (error) {
        console.error('Error fetching favorites:', error);
      } finally {
        loadingFavorites.value = false;
      }
    };
    
    const removeFromFavorites = async (productId) => {
      // Find the product in favorites
      const product = favorites.value.find(item => item.id === productId);
      if (product) {
        try {
          await favoriteStore.toggleFavorite(product);
          // Refresh the favorites list
          favorites.value = favoriteStore.favorites;
        } catch (error) {
          console.error('Error removing favorite:', error);
        }
      }
    };
    
    // Orders tab methods
    const fetchOrders = async () => {
      if (!authStore.isLoggedIn) return;
      
      loadingOrders.value = true;
      
      try {
        const response = await axios.get('/api/orders/my-orders');
        orders.value = response.data.data || response.data;
      } catch (error) {
        console.error('Error fetching orders:', error);
      } finally {
        loadingOrders.value = false;
      }
    };
    
    const toggleOrderDetails = (orderId) => {
      if (expandedOrderId.value === orderId) {
        expandedOrderId.value = null;
      } else {
        expandedOrderId.value = orderId;
      }
    };
    
    const getOrderStatusText = (status) => {
      const statusMap = {
        'pending': 'Oczekujące',
        'processing': 'W trakcie realizacji',
        'completed': 'Zrealizowane',
        'shipped': 'Wysłane',
        'delivered': 'Dostarczone',
        'cancelled': 'Anulowane',
        'refunded': 'Zwrócone'
      };
      
      return statusMap[status] || status;
    };
    
    const getOrderStatusClass = (status) => {
      const classMap = {
        'pending': 'text-yellow-600',
        'processing': 'text-blue-600',
        'completed': 'text-green-600',
        'shipped': 'text-purple-600',
        'delivered': 'text-green-600',
        'cancelled': 'text-red-600',
        'refunded': 'text-gray-600'
      };
      
      return classMap[status] || '';
    };
    
    // Reviews tab methods
    const fetchReviews = async () => {
      if (!authStore.isLoggedIn) return;
      
      loadingReviews.value = true;
      
      try {
        const response = await axios.get('/api/reviews/my-reviews');
        reviews.value = response.data.data || response.data;
      } catch (error) {
        console.error('Error fetching reviews:', error);
      } finally {
        loadingReviews.value = false;
      }
    };
    
    // Utility methods
    const formatDate = (dateString) => {
      if (!dateString) return '';
      
      const date = new Date(dateString);
      return date.toLocaleDateString('pl-PL', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      });
    };

    const formatPrice = (price) => {
      return parseFloat(price).toFixed(2);
    };

    // Promotion helper functions
    const hasPromotion = (product) => {
      return product && product.promotion_price !== undefined && 
             product.promotion_price !== null && 
             parseFloat(product.promotion_price) < parseFloat(product.price);
    };

    const getDiscountPercentage = (product) => {
      if (!hasPromotion(product)) return 0;
      const originalPrice = parseFloat(product.price);
      const promotionalPrice = parseFloat(product.promotion_price);
      return Math.round(((originalPrice - promotionalPrice) / originalPrice) * 100);
    };
    
    return {
      // Auth and profile
      authStore,
      isLoggingOut,
      isVerificationLoading,
      showChangePassword,
      currentPassword,
      newPassword,
      newPasswordConfirmation,
      status,
      passwordChangeStatus,
      handleLogout,
      resendVerification,
      handlePasswordChange,
      
      // Tabs
      activeTab,
      
      // Favorites
      favorites,
      loadingFavorites,
      removeFromFavorites,
      
      // Orders
      orders,
      loadingOrders,
      expandedOrderId,
      toggleOrderDetails,
      getOrderStatusText,
      getOrderStatusClass,
      
      // Reviews
      reviews,
      loadingReviews,
      
      // Utilities
      formatDate,
      formatPrice,
      hasPromotion,
      getDiscountPercentage,
      getProductImageUrl,
      handleImageError
    };
  }
}
</script> 