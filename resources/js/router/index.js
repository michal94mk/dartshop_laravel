import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../stores/authStore';

// Import page components
import Home from '../pages/Home.vue';
import ProductList from '../pages/ProductList.vue';
import ProductDetails from '../pages/ProductDetails.vue';
import Cart from '../pages/Cart.vue';
import Checkout from '../pages/Checkout.vue';
import About from '../pages/About.vue';
import Contact from '../pages/Contact.vue';
import Promotions from '../pages/Promotions.vue';
import Tutorials from '../pages/Tutorials.vue';
import TutorialDetails from '../pages/TutorialDetails.vue';
import NotFound from '../pages/NotFound.vue';
// Import auth components
import Login from '../pages/Login.vue';
import Register from '../pages/Register.vue';
import Profile from '../pages/Profile.vue';
import ForgotPassword from '../pages/ForgotPassword.vue';
import ResetPassword from '../pages/ResetPassword.vue';
import VerifyEmail from '../pages/VerifyEmail.vue';
// Import admin components
import AdminLayout from '../components/layouts/AdminLayout.vue';
import AdminDashboard from '../pages/admin/Dashboard.vue';
import AdminProducts from '../pages/admin/Products.vue';
import AdminCategories from '../pages/admin/Categories.vue';
import AdminUsers from '../pages/admin/Users.vue';
import AdminOrders from '../pages/admin/Orders.vue';

const routes = [
  {
    path: '/',
    name: 'home',
    component: Home,
  },
  {
    path: '/products',
    name: 'products',
    component: ProductList,
    meta: { 
      reloadAlways: true 
    }
  },
  {
    path: '/products/:id',
    name: 'product-details',
    component: ProductDetails,
    props: true,
  },
  {
    path: '/cart',
    name: 'cart',
    component: Cart,
  },
  {
    path: '/checkout',
    name: 'checkout',
    component: Checkout,
  },
  {
    path: '/about',
    name: 'about',
    component: About,
  },
  {
    path: '/contact',
    name: 'contact',
    component: Contact,
  },
  {
    path: '/promotions',
    name: 'promotions',
    component: Promotions,
  },
  {
    path: '/tutorials',
    name: 'tutorials',
    component: Tutorials,
  },
  {
    path: '/tutorials/:id',
    name: 'tutorial-details',
    component: TutorialDetails,
    props: true,
  },
  // Auth routes
  {
    path: '/login',
    name: 'login',
    component: Login,
    meta: {
      guest: true
    }
  },
  {
    path: '/register',
    name: 'register',
    component: Register,
    meta: {
      guest: true
    }
  },
  {
    path: '/profile',
    name: 'profile',
    component: Profile,
    meta: {
      requiresAuth: true
    }
  },
  {
    path: '/forgot-password',
    name: 'forgot-password',
    component: ForgotPassword,
    meta: {
      guest: true
    }
  },
  {
    path: '/reset-password/:token',
    name: 'reset-password',
    component: ResetPassword,
    meta: {
      guest: true
    }
  },
  {
    path: '/email/verify',
    name: 'verify-email',
    component: VerifyEmail
  },
  {
    path: '/email/verify/:id',
    name: 'verify-email-with-id',
    component: VerifyEmail,
    props: true
  },
  // Admin routes
  {
    path: '/admin',
    component: AdminLayout,
    meta: { 
      requiresAuth: true,
      requiresAdmin: true,
      layout: 'admin'
    },
    children: [
      {
        path: 'dashboard',
        name: 'admin-dashboard',
        component: AdminDashboard
      },
      {
        path: 'products',
        name: 'admin-products',
        component: AdminProducts
      },
      {
        path: 'categories',
        name: 'admin-categories',
        component: AdminCategories
      },
      {
        path: 'users',
        name: 'admin-users',
        component: AdminUsers
      },
      {
        path: 'orders',
        name: 'admin-orders',
        component: AdminOrders
      },
      {
        path: '', // Empty path redirects to dashboard
        redirect: { name: 'admin-dashboard' }
      }
    ]
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'not-found',
    component: NotFound,
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior() {
    // Always scroll to top
    return { top: 0 };
  },
});

// Debug router navigation and prevent duplicate navigation
router.beforeEach(async (to, from, next) => {
  console.log(`Router navigating from ${from.path} to ${to.path}`);
  
  const authStore = useAuthStore();
  
  // Inicjalizacja stanu autoryzacji, jeśli jeszcze nie został zainicjalizowany
  if (!authStore.user && !authStore.isLoading) {
    await authStore.initAuth();
  }
  
  // Sprawdź, czy trasa wymaga autoryzacji
  if (to.matched.some(record => record.meta.requiresAuth)) {
    // Sprawdź, czy użytkownik jest zalogowany
    if (!authStore.isLoggedIn) {
      // Przekieruj na stronę logowania
      next({
        path: '/login',
        query: { redirect: to.fullPath }
      });
    } else {
      // Sprawdź, czy trasa wymaga uprawnień administratora
      if (to.matched.some(record => record.meta.requiresAdmin)) {
        if (!authStore.isAdmin) {
          // Przekieruj na stronę główną, jeśli użytkownik nie jest adminem
          next({ path: '/' });
        } else {
          next();
        }
      }
      // Sprawdź, czy użytkownik zweryfikował email (jeśli wymagane)
      else if (to.matched.some(record => record.meta.requiresVerified) && 
          authStore.user && !authStore.user.email_verified_at) {
        next({ path: '/email/verify' });
      } else {
        next();
      }
    }
  } 
  // Sprawdź, czy trasa wymaga, aby użytkownik nie był zalogowany (np. login, register)
  else if (to.matched.some(record => record.meta.guest)) {
    // Jeśli użytkownik jest już zalogowany, przekieruj go na stronę główną
    if (authStore.isLoggedIn) {
      next({ path: '/' });
    } else {
      next();
    }
  } 
  // Obsługa ładowania tych samych tras
  else if (from.name === to.name && from.name === 'products') {
    console.log('Reloading products page with same route');
    
    // Get the ProductList component instance
    const productListInstance = router.currentRoute.value.matched[0].instances.default;
    
    // If the instance exists, manually call loadProducts
    if (productListInstance && typeof productListInstance.loadProducts === 'function') {
      productListInstance.loadProducts();
    }
    
    next();
  } 
  else {
    next();
  }
});

export default router; 