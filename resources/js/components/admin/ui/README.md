# Komponenty UI Panelu Administracyjnego

Ten katalog zawiera zunifikowane komponenty UI dla panelu administracyjnego. Wszystkie komponenty są automatycznie rejestrowane globalnie i mogą być używane w dowolnym miejscu aplikacji.

## Dostępne Komponenty

### AdminButton
Zunifikowany przycisk z różnymi wariantami i rozmiarami.

```vue
<admin-button variant="primary" size="md" @click="handleClick">
  Zapisz
</admin-button>

<admin-button variant="danger" size="sm" :disabled="loading">
  Usuń
</admin-button>

<admin-button variant="secondary" outline>
  Anuluj
</admin-button>
```

**Props:**
- `variant`: 'primary', 'secondary', 'success', 'warning', 'danger', 'info', 'light', 'dark'
- `size`: 'xs', 'sm', 'md', 'lg'
- `outline`: boolean - czy przycisk ma być w wersji outline
- `disabled`: boolean
- `loading`: boolean - pokazuje spinner
- `type`: string - typ przycisku (button, submit, reset)

### AdminTable
Zunifikowana tabela z sortowaniem i responsywnością.

```vue
<admin-table
  :columns="tableColumns"
  :items="items"
  :sort-by="sortField"
  :sort-order="sortOrder"
  @sort="handleSort"
>
  <template #cell-actions="{ item }">
    <action-buttons 
      :item="item" 
      @edit="editItem" 
      @delete="deleteItem"
    />
  </template>
  
  <template #cell-status="{ item, value }">
    <admin-badge :variant="getStatusVariant(value)">
      {{ getStatusLabel(value) }}
    </admin-badge>
  </template>
</admin-table>
```

**Props:**
- `columns`: Array - definicja kolumn tabeli
- `items`: Array - dane do wyświetlenia
- `sortBy`: String - aktualne pole sortowania
- `sortOrder`: String - kierunek sortowania ('asc', 'desc')
- `keyField`: String - pole używane jako klucz (domyślnie 'id')

**Definicja kolumn:**
```javascript
const tableColumns = [
  { key: 'id', label: 'ID', sortable: true },
  { key: 'name', label: 'Nazwa', sortable: true },
  { key: 'created_at', label: 'Data utworzenia', sortable: true, type: 'date' },
  { key: 'status', label: 'Status', align: 'center' },
  { key: 'actions', label: 'Akcje', align: 'right' }
]
```

### AdminBadge
Zunifikowane etykiety/znaczniki.

```vue
<admin-badge variant="green" size="sm">
  Aktywny
</admin-badge>

<admin-badge variant="red" size="xs" rounded="full">
  Nieaktywny
</admin-badge>
```

**Props:**
- `variant`: 'gray', 'red', 'yellow', 'green', 'blue', 'indigo', 'purple', 'pink'
- `size`: 'xs', 'sm', 'md', 'lg'
- `rounded`: 'sm', 'md', 'lg', 'full'

### AdminModal
Zunifikowany modal z różnymi rozmiarami.

```vue
<admin-modal
  :show="showModal"
  title="Tytuł modala"
  size="lg"
  @close="closeModal"
>
  <p>Treść modala</p>
  
  <template #footer>
    <admin-button-group justify="end" spacing="sm">
      <admin-button variant="secondary" outline @click="closeModal">
        Anuluj
      </admin-button>
      <admin-button variant="primary" @click="saveData">
        Zapisz
      </admin-button>
    </admin-button-group>
  </template>
</admin-modal>
```

**Props:**
- `show`: boolean - czy modal jest widoczny
- `title`: string - tytuł modala
- `size`: 'xs', 'sm', 'md', 'lg', 'xl', '2xl', '3xl', '4xl', '5xl', '6xl', '7xl'
- `closable`: boolean - czy można zamknąć modal (domyślnie true)
- `closeOnBackdrop`: boolean - czy zamykać po kliknięciu w tło (domyślnie true)
- `icon`: component - ikona w nagłówku
- `iconVariant`: 'info', 'success', 'warning', 'danger'
- `showDefaultFooter`: boolean - czy pokazać domyślne przyciski
- `confirmText`: string - tekst przycisku potwierdzenia
- `cancelText`: string - tekst przycisku anulowania

### AdminButtonGroup
Komponent do grupowania przycisków.

```vue
<admin-button-group spacing="sm" justify="end">
  <admin-button variant="secondary" outline>Anuluj</admin-button>
  <admin-button variant="primary">Zapisz</admin-button>
</admin-button-group>

<admin-button-group direction="col" spacing="md">
  <admin-button variant="primary">Opcja 1</admin-button>
  <admin-button variant="secondary">Opcja 2</admin-button>
</admin-button-group>
```

**Props:**
- `spacing`: 'xs', 'sm', 'md', 'lg'
- `direction`: 'row', 'col'
- `justify`: 'start', 'center', 'end', 'between', 'around', 'evenly'
- `align`: 'start', 'center', 'end', 'stretch', 'baseline'

## Przykład Kompletnej Implementacji

```vue
<template>
  <div>
    <!-- Nagłówek strony -->
    <page-header title="Zarządzanie produktami">
      <admin-button variant="primary" @click="showAddModal = true">
        Dodaj produkt
      </admin-button>
    </page-header>

    <!-- Filtry -->
    <search-filters
      v-model:search="filters.search"
      v-model:sort-field="filters.sort_field"
      v-model:sort-direction="filters.sort_direction"
      :sort-options="sortOptions"
      @search="fetchData"
      @reset="resetFilters"
    />

    <!-- Tabela -->
    <admin-table
      :columns="tableColumns"
      :items="items.data"
      :sort-by="filters.sort_field"
      :sort-order="filters.sort_direction"
      @sort="handleSort"
    >
      <template #cell-status="{ item, value }">
        <admin-badge :variant="getStatusVariant(value)">
          {{ getStatusLabel(value) }}
        </admin-badge>
      </template>
      
      <template #cell-actions="{ item }">
        <admin-button-group spacing="xs">
          <admin-button variant="primary" size="sm" @click="editItem(item)">
            Edytuj
          </admin-button>
          <admin-button variant="danger" size="sm" @click="deleteItem(item)">
            Usuń
          </admin-button>
        </admin-button-group>
      </template>
    </admin-table>

    <!-- Modal dodawania/edycji -->
    <admin-modal
      :show="showAddModal"
      title="Dodaj nowy produkt"
      size="lg"
      @close="closeModal"
    >
      <!-- Formularz -->
      <form @submit.prevent="saveItem">
        <!-- Pola formularza -->
      </form>
      
      <template #footer>
        <admin-button-group justify="end" spacing="sm">
          <admin-button variant="secondary" outline @click="closeModal">
            Anuluj
          </admin-button>
          <admin-button variant="primary" @click="saveItem">
            Zapisz
          </admin-button>
        </admin-button-group>
      </template>
    </admin-modal>
  </div>
</template>

<script>
export default {
  setup() {
    // Definicja kolumn tabeli
    const tableColumns = [
      { key: 'id', label: 'ID', sortable: true },
      { key: 'name', label: 'Nazwa', sortable: true },
      { key: 'status', label: 'Status', align: 'center' },
      { key: 'created_at', label: 'Data utworzenia', sortable: true, type: 'date' },
      { key: 'actions', label: 'Akcje', align: 'right' }
    ]
    
    // Reszta logiki...
    
    return {
      tableColumns,
      // inne zmienne...
    }
  }
}
</script>
```

## Zasady Stylowania

### Kolory
- **Primary**: Indigo (główne akcje)
- **Secondary**: Gray (akcje drugorzędne)
- **Success**: Green (potwierdzenia, sukces)
- **Warning**: Yellow (ostrzeżenia)
- **Danger**: Red (usuwanie, błędy)
- **Info**: Blue (informacje)

### Rozmiary
- **xs**: Bardzo małe elementy
- **sm**: Małe elementy (domyślnie w tabelach)
- **md**: Średnie elementy (domyślnie w modalach)
- **lg**: Duże elementy (główne akcje)

### Responsywność
Wszystkie komponenty są responsywne i dostosowują się do różnych rozmiarów ekranu.

## Migracja z Istniejących Komponentów

### Przed:
```vue
<button class="px-3 py-1.5 bg-indigo-600 text-white text-sm font-medium rounded hover:bg-indigo-700 transition-colors">
  Edytuj
</button>
```

### Po:
```vue
<admin-button variant="primary" size="sm">
  Edytuj
</admin-button>
```

### Przed:
```vue
<table class="min-w-full divide-y divide-gray-200">
  <!-- Skomplikowana struktura tabeli -->
</table>
```

### Po:
```vue
<admin-table :columns="columns" :items="items" @sort="handleSort">
  <template #cell-actions="{ item }">
    <!-- Akcje -->
  </template>
</admin-table>
```

## Wsparcie

Jeśli masz pytania dotyczące komponentów lub potrzebujesz dodać nową funkcjonalność, skontaktuj się z zespołem frontend. 