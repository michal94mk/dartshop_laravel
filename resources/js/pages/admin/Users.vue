<template>
  <admin-tabs-layout
    title="Zarządzanie użytkownikami"
    subtitle="Lista wszystkich użytkowników z możliwością edycji, zmiany roli i usuwania kont"
    :tabs="tabs"
    v-model="activeMainTab"
    @tab-change="handleTabChange"
  >
    <!-- Header slot - przyciski globalne -->
    <template #header>
      <admin-button-group spacing="sm">
        <admin-button
          variant="primary"
          @click="openModal({id: null, name: '', email: '', role: 'user', verified: false, password: ''})"
        >
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
          </svg>
          Dodaj użytkownika
        </admin-button>
        <admin-button
          variant="secondary"
          outline
          @click="exportUsers"
        >
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
          </svg>
          Eksportuj
        </admin-button>
      </admin-button-group>
    </template>

    <!-- Toolbar slot - filtry i wyszukiwanie -->
    <template #toolbar>
      <search-filters
        v-if="!loading"
        :filters="filters"
        :sort-options="sortOptions"
        :default-filters="defaultFilters"
        search-label="Wyszukaj"
        search-placeholder="Imię, nazwisko lub email..."
        @update:filters="(newFilters) => { Object.assign(filters, newFilters); filters.page = 1; }"
        @filter-change="fetchUsers"
        @reset-filters="resetFilters"
      >
        <template v-slot:filters>
          <div class="w-full sm:w-auto">
            <label for="role" class="block text-sm font-medium text-gray-700">Rola</label>
            <select
              id="role"
              name="role"
              v-model="filters.role"
              @change="fetchUsers"
              class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
            >
              <option value="">Wszystkie role</option>
              <option value="admin">Administrator</option>
              <option value="user">Użytkownik</option>
            </select>
          </div>
          
          <div class="w-full sm:w-auto">
            <label for="verified" class="block text-sm font-medium text-gray-700">Status</label>
            <select
              id="verified"
              name="verified"
              v-model="filters.verified"
              @change="fetchUsers"
              class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
            >
              <option value="">Wszyscy</option>
              <option value="1">Zweryfikowani</option>
              <option value="0">Niezweryfikowani</option>
            </select>
          </div>
        </template>
      </search-filters>
    </template>

    <!-- Main tab content -->
    <template #default="{ activeTab }">
      <!-- Users list -->
      <admin-tab-panel
        tab-id="list"
        :active-tab="activeTab"
        title="Lista użytkowników"
        description="Przeglądaj i zarządzaj wszystkimi kontami użytkowników"
      >
        <!-- Loading indicator -->
        <loading-spinner v-if="loading" />

        <!-- Users table -->
        <admin-table
          v-if="users.data && users.data.length > 0"
          :columns="tableColumns"
          :items="users.data"
        >
          <template #cell-user="{ item }">
            <div class="flex items-center">
              <div class="h-10 w-10 flex-shrink-0">
                <div class="h-10 w-10 rounded-full bg-indigo-600 flex items-center justify-center text-white font-medium">
                  {{ getUserInitials(item) }}
                </div>
              </div>
              <div class="ml-4">
                <div class="font-medium text-gray-900">{{ getDisplayName(item) }}</div>
                <div class="text-sm text-gray-500">{{ item.email }}</div>
              </div>
            </div>
          </template>
          
          <template #cell-role="{ item }">
            <admin-badge 
              :variant="isUserAdmin(item) ? 'indigo' : 'gray'"
              size="xs"
            >
              {{ isUserAdmin(item) ? 'Administrator' : 'Użytkownik' }}
            </admin-badge>
          </template>
          
          <template #cell-status="{ item }">
            <admin-badge 
              :variant="item.email_verified_at ? 'green' : 'yellow'"
              size="xs"
            >
              {{ item.email_verified_at ? 'Zweryfikowany' : 'Niezweryfikowany' }}
            </admin-badge>
          </template>

          <template #cell-last_login="{ item }">
            <span v-if="item.last_login_at" class="text-sm text-gray-900">
              {{ formatDate(item.last_login_at) }}
            </span>
            <span v-else class="text-sm text-gray-400">Nigdy</span>
          </template>
          
          <template #cell-actions="{ item }">
            <action-buttons 
              :item="item" 
              @edit="openModal" 
              @delete="deleteUser"
              :disable-delete="item.id === currentUserId"
            >
              <template #custom-buttons="{ item }">
                <admin-button
                  v-if="!item.email_verified_at"
                  variant="info"
                  size="sm"
                  @click="verifyUser(item)"
                  title="Zweryfikuj użytkownika"
                >
                  Weryfikuj
                </admin-button>
              </template>
            </action-buttons>
          </template>
        </admin-table>
        
        <!-- No data message -->
        <no-data-message v-else-if="!loading" message="Brak użytkowników do wyświetlenia" />
        
        <!-- Pagination -->
        <pagination 
          v-if="!loading && users.data && users.data.length > 0" 
          :pagination="users" 
          items-label="użytkowników" 
          @page-change="goToPage" 
        />
      </admin-tab-panel>

      <!-- Role i uprawnienia -->
      <admin-tab-panel
        tab-id="roles"
        :active-tab="activeTab"
        title="Role i uprawnienia"
        description="Zarządzaj rolami użytkowników i ich uprawnieniami"
      >
        <div class="space-y-6">
          <!-- Role statistics -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white border border-gray-200 rounded-lg p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-indigo-500 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                      <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                  </div>
                </div>
                <div class="ml-4">
                  <div class="text-sm font-medium text-gray-500">Administratorzy</div>
                  <div class="text-2xl font-bold text-gray-900">{{ getAdminCount() }}</div>
                </div>
              </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                      <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path>
                    </svg>
                  </div>
                </div>
                <div class="ml-4">
                  <div class="text-sm font-medium text-gray-500">Użytkownicy</div>
                  <div class="text-2xl font-bold text-gray-900">{{ getUserCount() }}</div>
                </div>
              </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                  </div>
                </div>
                <div class="ml-4">
                  <div class="text-sm font-medium text-gray-500">Zweryfikowani</div>
                  <div class="text-2xl font-bold text-gray-900">{{ getVerifiedCount() }}</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Role management interface -->
          <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Zarządzanie rolami</h3>
            <div class="space-y-4">
              <div v-for="role in availableRoles" :key="role.value" class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                <div>
                  <div class="font-medium text-gray-900">{{ role.label }}</div>
                  <div class="text-sm text-gray-500">{{ role.description }}</div>
                </div>
                <div class="flex items-center space-x-3">
                  <admin-badge variant="secondary">
                    {{ getUsersByRole(role.value).length }} użytkowników
                  </admin-badge>
                  <admin-button variant="secondary" size="sm">
                    Edytuj uprawnienia
                  </admin-button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </admin-tab-panel>

                <!-- Security settings -->
      <admin-tab-panel
        tab-id="settings"
        :active-tab="activeTab"
        title="Ustawienia bezpieczeństwa"
        description="Konfiguracja bezpieczeństwa kont użytkowników"
      >
        <div class="space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
              <h4 class="text-lg font-medium text-gray-900">Polityka haseł</h4>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Minimalna długość hasła
                </label>
                <input 
                  type="number" 
                  v-model.number="settings.minPasswordLength" 
                  min="6" 
                  max="50"
                  class="block w-full rounded-md border-gray-300"
                />
              </div>

              <div>
                <label class="flex items-center">
                  <input type="checkbox" v-model="settings.requireUppercase" class="rounded border-gray-300">
                  <span class="ml-2 text-sm text-gray-700">Wymagaj wielkich liter</span>
                </label>
              </div>

              <div>
                <label class="flex items-center">
                  <input type="checkbox" v-model="settings.requireNumbers" class="rounded border-gray-300">
                  <span class="ml-2 text-sm text-gray-700">Wymagaj cyfr</span>
                </label>
              </div>

              <div>
                <label class="flex items-center">
                  <input type="checkbox" v-model="settings.requireSpecialChars" class="rounded border-gray-300">
                  <span class="ml-2 text-sm text-gray-700">Wymagaj znaków specjalnych</span>
                </label>
              </div>
            </div>

            <div class="space-y-4">
              <h4 class="text-lg font-medium text-gray-900">Sesje i logowanie</h4>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Czas wygaśnięcia sesji (minuty)
                </label>
                <input 
                  type="number" 
                  v-model.number="settings.sessionTimeout" 
                  min="10" 
                  max="1440"
                  class="block w-full rounded-md border-gray-300"
                />
              </div>

              <div>
                <label class="flex items-center">
                  <input type="checkbox" v-model="settings.requireEmailVerification" class="rounded border-gray-300">
                  <span class="ml-2 text-sm text-gray-700">Wymagaj weryfikacji e-mail</span>
                </label>
              </div>

              <div>
                <label class="flex items-center">
                  <input type="checkbox" v-model="settings.allowSelfRegistration" class="rounded border-gray-300">
                  <span class="ml-2 text-sm text-gray-700">Pozwól na rejestrację</span>
                </label>
              </div>
            </div>
          </div>
        </div>

        <template #footer>
          <admin-button variant="secondary" outline>
            Przywróć domyślne
          </admin-button>
          <admin-button variant="primary" @click="saveSettings" :loading="submitting">
            Zapisz ustawienia
          </admin-button>
        </template>
      </admin-tab-panel>
    </template>
  </admin-tabs-layout>

  <!-- User Modal -->
  <admin-modal
    :show="showModal"
    :title="currentUser.id ? 'Edytuj użytkownika' : 'Dodaj nowego użytkownika'"
    size="lg"
    @close="showModal = false"
  >
    <form @submit.prevent="saveUser" class="space-y-6">
      <!-- Tabs -->
      <div class="border-b border-gray-200">
        <nav class="-mb-px flex" aria-label="Tabs">
          <button
            type="button"
            @click="activeTab = 'details'"
            :class="[
              activeTab === 'details'
                ? 'border-indigo-500 text-indigo-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
              'w-1/2 py-4 px-1 text-center border-b-2 font-medium text-sm'
            ]"
          >
            Podstawowe dane
          </button>
          <button
            type="button"
            @click="activeTab = 'permissions'"
            :class="[
              activeTab === 'permissions'
                ? 'border-indigo-500 text-indigo-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
              'w-1/2 py-4 px-1 text-center border-b-2 font-medium text-sm'
            ]"
          >
            Uprawnienia
          </button>
        </nav>
      </div>
      
      <!-- Basic Details Tab -->
      <div v-if="activeTab === 'details'" class="space-y-4">
        <div>
          <label for="name" class="block text-sm font-medium text-gray-700">Nazwa użytkownika</label>
          <input
            type="text"
            id="name"
            v-model="currentUser.name"
            required
            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
          />
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label for="first_name" class="block text-sm font-medium text-gray-700">Imię</label>
            <input
              type="text"
              id="first_name"
              v-model="currentUser.first_name"
              class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
            />
          </div>
          
          <div>
            <label for="last_name" class="block text-sm font-medium text-gray-700">Nazwisko</label>
            <input
              type="text"
              id="last_name"
              v-model="currentUser.last_name"
              class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
            />
          </div>
        </div>
        
        <div>
          <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
          <input
            type="email"
            id="email"
            v-model="currentUser.email"
            required
            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
          />
        </div>
        
        <div v-if="!currentUser.id">
          <label for="password" class="block text-sm font-medium text-gray-700">Hasło</label>
          <input
            type="password"
            id="password"
            v-model="currentUser.password"
            required
            minlength="8"
            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
          />
        </div>
        
        <div v-if="currentUser.id">
          <label for="new_password" class="block text-sm font-medium text-gray-700">Nowe hasło (pozostaw puste, aby nie zmieniać)</label>
          <input
            type="password"
            id="new_password"
            v-model="currentUser.password"
            minlength="8"
            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
          />
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700">Status</label>
          <div class="mt-2">
            <label class="inline-flex items-center">
              <input 
                type="checkbox" 
                v-model="currentUser.verified" 
                class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
              />
              <span class="ml-2 text-sm text-gray-700">Email zweryfikowany</span>
            </label>
          </div>
        </div>
      </div>
      
      <!-- Permissions Tab -->
      <div v-if="activeTab === 'permissions'" class="space-y-6">
        <!-- Role Selection -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Rola użytkownika</label>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div 
              v-for="role in availableRoles" 
              :key="role.value" 
              @click="currentUser.role = role.value"
              class="relative border rounded-lg p-4 cursor-pointer"
              :class="currentUser.role === role.value ? 'border-indigo-500 bg-indigo-50' : 'border-gray-300 hover:border-indigo-300'"
            >
              <div class="flex items-center justify-between">
                <div>
                  <div class="font-medium text-gray-900">{{ role.label }}</div>
                  <div class="text-sm text-gray-500">{{ role.description }}</div>
                </div>
                <div class="flex-shrink-0">
                  <div 
                    class="w-4 h-4 rounded-full border-2" 
                    :class="currentUser.role === role.value ? 'border-indigo-500 bg-indigo-500' : 'border-gray-300'"
                  >
                    <div v-if="currentUser.role === role.value" class="w-2 h-2 bg-white rounded-full m-0.5"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </form>
    
    <template #footer>
      <admin-button-group justify="end" spacing="sm">
        <admin-button
          @click="showModal = false"
          variant="secondary"
          outline
        >
          Anuluj
        </admin-button>
        <admin-button
          @click="saveUser"
          variant="primary"
          :loading="submitting"
        >
          {{ currentUser.id ? 'Zapisz zmiany' : 'Dodaj użytkownika' }}
        </admin-button>
      </admin-button-group>
    </template>
  </admin-modal>

  <!-- Delete confirmation modal -->
  <admin-modal
    :show="showDeleteModal"
    title="Usuń użytkownika"
    @close="showDeleteModal = false"
  >
    <div class="sm:flex sm:items-start">
      <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
        <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
      </div>
      <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
        <div class="mt-2">
          <p class="text-sm text-gray-500">
            Czy na pewno chcesz usunąć tego użytkownika? Ta operacja jest nieodwracalna.
          </p>
        </div>
      </div>
    </div>
    
    <template #footer>
      <admin-button-group justify="end" spacing="sm">
        <admin-button @click="showDeleteModal = false" variant="secondary" outline>
          Anuluj
        </admin-button>
        <admin-button @click="confirmDelete" variant="danger">
          Usuń użytkownika
        </admin-button>
      </admin-button-group>
    </template>
  </admin-modal>
</template>

<script>
import { ref, reactive, computed, onMounted } from 'vue'
import { useAlertStore } from '../../stores/alertStore'
import axios from 'axios'
import AdminTable from '../../components/admin/ui/AdminTable.vue'
import AdminModal from '../../components/admin/ui/AdminModal.vue'
import AdminButtonGroup from '../../components/admin/ui/AdminButtonGroup.vue'
import AdminButton from '../../components/admin/ui/AdminButton.vue'
import SearchFilters from '../../components/admin/SearchFilters.vue'
import LoadingSpinner from '../../components/admin/LoadingSpinner.vue'
import NoDataMessage from '../../components/admin/NoDataMessage.vue'
import Pagination from '../../components/admin/Pagination.vue'
import ActionButtons from '../../components/admin/ActionButtons.vue'
import AdminTabsLayout from '../../components/admin/AdminTabsLayout.vue'
import AdminTabPanel from '../../components/admin/AdminTabPanel.vue'
import AdminBadge from '../../components/admin/ui/AdminBadge.vue'

export default {
  name: 'AdminUsers',
  components: {
    AdminTable,
    AdminModal,
    AdminButtonGroup,
    AdminButton,
    SearchFilters,
    LoadingSpinner,
    NoDataMessage,
    Pagination,
    ActionButtons,
    AdminTabsLayout,
    AdminTabPanel,
    AdminBadge
  },
  setup() {
    const alertStore = useAlertStore()
    
    // Tabs configuration
    const activeMainTab = ref('list')
    const tabs = [
      {
        id: 'list',
        label: 'Lista użytkowników',
        iconPath: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z',
        badge: {
          value: computed(() => users.value.total || 0),
          variant: 'primary'
        }
      },
      {
        id: 'roles',
        label: 'Role i uprawnienia',
        iconPath: 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
        badge: {
          value: computed(() => availableRoles.value.length),
          variant: 'secondary'
        }
      },
      {
        id: 'settings',
        label: 'Ustawienia',
        iconPath: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z'
      }
    ]

    // Settings configuration
    const settings = reactive({
      minPasswordLength: 8,
      requireUppercase: true,
      requireNumbers: true,
      requireSpecialChars: false,
      sessionTimeout: 120,
      requireEmailVerification: true,
      allowSelfRegistration: true
    })

    // Other reactive data
    const submitting = ref(false)
    const loading = ref(true)
    const users = ref({
      data: [],
      current_page: 1,
      from: 1,
      to: 0,
      total: 0,
      last_page: 1,
      per_page: 10
    })

    // Available roles
    const availableRoles = ref([
      {
        value: 'admin',
        label: 'Administrator',
        description: 'Pełny dostęp do wszystkich funkcji systemu'
      },
      {
        value: 'user',
        label: 'Użytkownik',
        description: 'Standardowy dostęp do funkcji użytkownika'
      }
    ])

    // Current user ID (for preventing self-deletion)
    const currentUserId = ref(null)
    
    // Sort options for the filter component
    const sortOptions = [
      { value: 'created_at', label: 'Data utworzenia' },
      { value: 'name', label: 'Nazwa użytkownika' },
      { value: 'email', label: 'Email' },
      { value: 'last_login_at', label: 'Ostatnie logowanie' }
    ]
    
    // Table columns definition
    const tableColumns = [
      { key: 'user', label: 'Użytkownik', width: '300px' },
      { key: 'role', label: 'Rola', width: '120px' },
      { key: 'status', label: 'Status', width: '120px' },
      { key: 'last_login', label: 'Ostatnie logowanie', width: '150px' },
      { key: 'created_at', label: 'Data utworzenia', type: 'date', width: '150px' },
      { key: 'actions', label: 'Akcje', align: 'right', width: '180px' }
    ]
    
    // Default filters
    const defaultFilters = {
      search: '',
      role: '',
      verified: '',
      sort_field: 'created_at',
      sort_direction: 'desc',
      page: 1
    }
    
    const filters = reactive({ ...defaultFilters })
    
    // Modals
    const showModal = ref(false)
    const showDeleteModal = ref(false)
    const userToDelete = ref(null)
    const activeTab = ref('details')
    const currentUser = ref({
      id: null,
      name: '',
      first_name: '',
      last_name: '',
      email: '',
      role: 'user',
      verified: false,
      password: ''
    })
    
    // Methods
    const fetchUsers = async () => {
      try {
        loading.value = true
        
        const params = {
          page: filters.page,
          search: filters.search,
          role: filters.role,
          verified: filters.verified,
          sort_field: filters.sort_field,
          sort_direction: filters.sort_direction
        }
        
        console.log('Fetching users with params:', params)
        
        const response = await axios.get('/api/admin/users', { params })
        users.value = response.data
      } catch (error) {
        console.error('Error fetching users:', error)
        console.error('Error details:', error.response?.data)
        alertStore.error('Wystąpił błąd podczas pobierania użytkowników.')
      } finally {
        loading.value = false
      }
    }
    
    const goToPage = (page) => {
      if (page === '...') return
      filters.page = page
      fetchUsers()
    }
    
    const openModal = (user = null) => {
      if (user) {
        currentUser.value = {
          id: user.id,
          name: user.name,
          first_name: user.first_name || '',
          last_name: user.last_name || '',
          email: user.email,
          role: user.role || 'user',
          verified: !!user.email_verified_at,
          password: ''
        }
      } else {
        currentUser.value = {
          id: null,
          name: '',
          first_name: '',
          last_name: '',
          email: '',
          role: 'user',
          verified: false,
          password: ''
        }
      }
      
      activeTab.value = 'details'
      showModal.value = true
    }
    
    const saveUser = async () => {
      try {
        submitting.value = true
        
        const userData = {
          name: currentUser.value.name,
          first_name: currentUser.value.first_name,
          last_name: currentUser.value.last_name,
          email: currentUser.value.email,
          role: currentUser.value.role,
          verified: currentUser.value.verified
        }
        
        if (currentUser.value.password) {
          userData.password = currentUser.value.password
        }
        
        if (currentUser.value.id) {
          await axios.put(`/api/admin/users/${currentUser.value.id}`, userData)
          alertStore.success('Użytkownik został zaktualizowany.')
        } else {
          await axios.post('/api/admin/users', userData)
          alertStore.success('Użytkownik został dodany.')
        }
        
        showModal.value = false
        fetchUsers()
      } catch (error) {
        console.error('Error saving user:', error)
        if (error.response && error.response.data && error.response.data.errors) {
          const errors = error.response.data.errors
          const errorMessages = Object.values(errors).flat().join('\n')
          alertStore.error('Błędy walidacji:\n' + errorMessages)
        } else {
          alertStore.error('Wystąpił błąd podczas zapisywania użytkownika.')
        }
      } finally {
        submitting.value = false
      }
    }
    
    const deleteUser = (user) => {
      userToDelete.value = user
      showDeleteModal.value = true
    }
    
    const confirmDelete = async () => {
      try {
        loading.value = true
        
        const userId = typeof userToDelete.value === 'object' 
                      ? userToDelete.value.id 
                      : userToDelete.value
        
        await axios.delete(`/api/admin/users/${userId}`)
        alertStore.success('Użytkownik został usunięty.')
        showDeleteModal.value = false
        fetchUsers()
      } catch (error) {
        console.error('Error deleting user:', error)
        if (error.response?.status === 422) {
          if (error.response.data.message) {
            alertStore.error(error.response.data.message)
          } else {
            alertStore.error('Nie można usunąć tego użytkownika.')
          }
        } else {
          alertStore.error('Wystąpił błąd podczas usuwania użytkownika.')
        }
        showDeleteModal.value = false
      } finally {
        loading.value = false
      }
    }
    
    const verifyUser = async (user) => {
      try {
        await axios.post(`/api/admin/users/${user.id}/verify`)
        alertStore.success('Użytkownik został zweryfikowany.')
        fetchUsers()
      } catch (error) {
        console.error('Error verifying user:', error)
        alertStore.error('Wystąpił błąd podczas weryfikacji użytkownika.')
      }
    }
    
    const formatDate = (dateString) => {
      const options = { year: 'numeric', month: 'short', day: 'numeric' }
      return new Date(dateString).toLocaleDateString('pl-PL', options)
    }
    
    const resetFilters = () => {
      Object.assign(filters, defaultFilters)
      fetchUsers()
    }
    
    const getUserInitials = (user) => {
      if (user.first_name && user.last_name) {
        return (user.first_name.charAt(0) + user.last_name.charAt(0)).toUpperCase()
      }
      return user.name.charAt(0).toUpperCase()
    }
    
    const getDisplayName = (user) => {
      if (user.first_name && user.last_name) {
        return `${user.first_name} ${user.last_name}`
      }
      return user.name
    }
    
    const isUserAdmin = (user) => {
      return user.role === 'admin'
    }

    // Tab and settings methods
    const handleTabChange = (newTab, oldTab) => {
      console.log(`Zmiana zakładki z ${oldTab} na ${newTab}`)
      activeMainTab.value = newTab
    }

    const saveSettings = async () => {
      try {
        submitting.value = true
        
        // Tutaj można dodać logikę zapisywania ustawień do backendu
        // await axios.put('/api/admin/users/settings', settings)
        
        // Na razie tylko symulujemy zapis
        await new Promise(resolve => setTimeout(resolve, 1000))
        
        alertStore.success('Ustawienia zostały zapisane.')
      } catch (error) {
        console.error('Error saving settings:', error)
        alertStore.error('Wystąpił błąd podczas zapisywania ustawień.')
      } finally {
        submitting.value = false
      }
    }

    const exportUsers = async () => {
      try {
        alertStore.info('Rozpoczynanie eksportu użytkowników...')
        
        // Tutaj można dodać logikę eksportu
        // const response = await axios.get('/api/admin/users/export', { responseType: 'blob' })
        
        // Na razie tylko symulujemy eksport
        await new Promise(resolve => setTimeout(resolve, 2000))
        
        alertStore.success('Użytkownicy zostali wyeksportowani.')
      } catch (error) {
        console.error('Error exporting users:', error)
        alertStore.error('Wystąpił błąd podczas eksportu użytkowników.')
      }
    }

    // Statistics methods
    const getAdminCount = () => {
      return users.value.data?.filter(user => user.role === 'admin').length || 0
    }

    const getUserCount = () => {
      return users.value.data?.filter(user => user.role === 'user').length || 0
    }

    const getVerifiedCount = () => {
      return users.value.data?.filter(user => user.email_verified_at).length || 0
    }

    const getUsersByRole = (role) => {
      return users.value.data?.filter(user => user.role === role) || []
    }
    
    // Lifecycle
    onMounted(() => {
      fetchUsers()
      // Get current user ID from authenticated user
      // currentUserId.value = ... (implement based on your auth system)
    })
    
    return {
      loading,
      users,
      filters,
      defaultFilters,
      sortOptions,
      tableColumns,
      showModal,
      showDeleteModal,
      currentUser,
      activeTab,
      activeMainTab,
      tabs,
      settings,
      submitting,
      availableRoles,
      currentUserId,
      fetchUsers,
      goToPage,
      openModal,
      saveUser,
      deleteUser,
      confirmDelete,
      verifyUser,
      formatDate,
      resetFilters,
      getUserInitials,
      getDisplayName,
      isUserAdmin,
      handleTabChange,
      saveSettings,
      exportUsers,
      getAdminCount,
      getUserCount,
      getVerifiedCount,
      getUsersByRole
    }
  }
}
</script> 