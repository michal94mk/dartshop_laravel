<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains over 2000 video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the Laravel [Patreon page](https://patreon.com/taylorotwell).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Cubet Techno Labs](https://cubettech.com)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[Many](https://www.many.co.uk)**
- **[Webdock, Fast VPS Hosting](https://www.webdock.io/en)**
- **[DevSquad](https://devsquad.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[OP.GG](https://op.gg)**
- **[WebReinvent](https://webreinvent.com/?utm_source=laravel&utm_medium=github&utm_campaign=patreon-sponsors)**
- **[Lendio](https://lendio.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Nowy interfejs z Tailwind CSS

Aplikacja posiada alternatywny interfejs oparty na Tailwind CSS. Można go włączyć dodając parametr `tailwind` do adresu URL:

- Strona główna z Tailwind: `/tailwind`
- Kategorie z Tailwind: `/categories?tailwind=1`
- Koszyk z Tailwind: `/cart/view?tailwind=1`

### Zalety interfejsu Tailwind:

- Nowoczesny, minimalistyczny design
- W pełni responsywny dla wszystkich urządzeń
- Ulepszone wrażenia użytkownika
- Animacje i przejścia
- Szybsze ładowanie strony
- Lepsze filtry i sortowanie produktów

Aby zainstalować i uruchomić nowy interfejs:

```bash
npm install
npm run build
```

# Standardizing Admin Panel Search and Sorting Components

This guide provides instructions for standardizing the search, filtering and sorting components across all admin panel pages in the application.

## Available Components

The following reusable components have been created:

1. **SearchFilters** - A standardized search and filtering component
2. **LoadingSpinner** - A consistent loading indicator
3. **Pagination** - A standardized pagination component
4. **ActionButtons** - Standardized action buttons for tables (edit/delete)
5. **PageHeader** - A consistent page header with title and add button
6. **NoDataMessage** - A standardized message for when there's no data to display

These components are registered globally in `resources/js/app.js` and can be used in any Vue component.

## How to Standardize an Admin Page

For each admin page (`resources/js/pages/admin/*.vue`), follow these steps:

### 1. Update the Page Header

Replace the custom page header with the standardized component:

```vue
<page-header 
  title="Your Page Title"
  subtitle="Optional subtitle for the page"
  add-button-label="Add Item"
  @add="yourAddMethod"
/>
```

### 2. Replace Search and Filters

Replace the custom search and filter section with the standardized component:

```vue
<search-filters
  v-if="!loading"
  :filters.sync="filters"
  :sort-options="sortOptions"
  search-label="Wyszukaj"
  search-placeholder="Your placeholder..."
  @filter-change="yourFilterMethod"
>
  <template v-slot:filters>
    <!-- Your custom filter elements go here -->
  </template>
</search-filters>
```

Define your sort options in the setup/data section:

```js
const sortOptions = [
  { value: 'created_at', label: 'Data dodania' },
  { value: 'name', label: 'Nazwa' },
  // Add more options as needed
]
```

### 3. Replace Loading Indicator

Replace custom loading indicators with the standardized component:

```vue
<loading-spinner v-if="loading" />
```

### 4. Replace Action Buttons

Replace custom action buttons in tables with the standardized component:

```vue
<action-buttons 
  :item="item"
  @edit="editItem"
  @delete="confirmDelete"
/>
```

### 5. Add No Data Message

Replace custom "no data" messages with the standardized component:

```vue
<no-data-message v-else message="Brak danych do wyświetlenia" />
```

### 6. Replace Pagination

If your page has pagination, replace it with the standardized component:

```vue
<pagination 
  :pagination="paginationObject" 
  items-label="elementów" 
  @page-change="goToPage" 
/>
```

## Example

See `resources/js/pages/admin/Tutorials.vue` for a complete example of how to implement these standardized components.

## Benefits

Standardizing these UI elements provides several benefits:

1. Consistent user experience across the admin panel
2. Easier maintenance and updates
3. Reduced code duplication
4. Faster development of new admin pages
