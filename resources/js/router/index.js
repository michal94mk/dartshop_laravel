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
import Tutorial from '../pages/Tutorial.vue';
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
// Import remaining admin components
import AdminBrands from '../pages/admin/Brands.vue';
import AdminReviews from '../pages/admin/Reviews.vue';
import AdminPromotions from '../pages/admin/Promotions.vue';
import AdminTutorials from '../pages/admin/Tutorials.vue';
import AdminContactMessages from '../pages/admin/ContactMessages.vue';
import AdminAbout from '../pages/admin/About.vue';

const routes = [
  {
    path: '/',
    name: 'home',
    component: Home,
    meta: {
      layout: 'default'
    }
  },
  {
    path: '/products',
    name: 'products',
    component: ProductList,
    meta: { 
      reloadAlways: true,
      layout: 'default'
    }
  },
  {
    path: '/products/:id',
    name: 'product-details',
    component: ProductDetails,
    props: true,
    meta: {
      layout: 'default'
    }
  },
  {
    path: '/cart',
    name: 'cart',
    component: Cart,
    meta: {
      layout: 'default'
    }
  },
  {
    path: '/checkout',
    name: 'checkout',
    component: Checkout,
    meta: {
      layout: 'default'
    }
  },
  {
    path: '/about',
    name: 'about',
    component: About,
    meta: {
      layout: 'default'
    }
  },
  {
    path: '/contact',
    name: 'contact',
    component: Contact,
    meta: {
      layout: 'default'
    }
  },
  {
    path: '/promotions',
    name: 'promotions',
    component: Promotions,
    meta: {
      layout: 'default'
    }
  },
  {
    path: '/tutorials',
    name: 'tutorials',
    component: Tutorials,
    meta: {
      layout: 'default'
    }
  },
  {
    path: '/tutorials/:slug',
    name: 'tutorial',
    component: Tutorial,
    props: true,
    meta: {
      layout: 'default'
    }
  },
  // Auth routes
  {
    path: '/login',
    name: 'login',
    component: Login,
    meta: {
      guest: true,
      layout: 'default'
    }
  },
  {
    path: '/register',
    name: 'register',
    component: Register,
    meta: {
      guest: true,
      layout: 'default'
    }
  },
  {
    path: '/profile',
    name: 'profile',
    component: Profile,
    meta: {
      requiresAuth: true,
      layout: 'default'
    }
  },
  {
    path: '/forgot-password',
    name: 'forgot-password',
    component: ForgotPassword,
    meta: {
      guest: true,
      layout: 'default'
    }
  },
  {
    path: '/reset-password/:token',
    name: 'reset-password',
    component: ResetPassword,
    meta: {
      guest: true,
      layout: 'default'
    }
  },
  {
    path: '/email/verify',
    name: 'verify-email',
    component: VerifyEmail,
    meta: {
      layout: 'default'
    }
  },
  {
    path: '/email/verify/:id',
    name: 'verify-email-with-id',
    component: VerifyEmail,
    props: true,
    meta: {
      layout: 'default'
    }
  },
  // Admin routes
  {
    path: '/admin',
    component: {
      template: '<router-view></router-view>'
    },
    meta: { 
      requiresAuth: true,
      requiresAdmin: true,
      layout: 'admin'
    },
    children: [
      {
        path: '',
        redirect: { name: 'admin-dashboard' }
      },
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
        path: 'brands',
        name: 'admin-brands',
        component: AdminBrands
      },
      {
        path: 'reviews',
        name: 'admin-reviews',
        component: AdminReviews
      },
      {
        path: 'promotions',
        name: 'admin-promotions',
        component: AdminPromotions
      },
      {
        path: 'tutorials',
        name: 'admin-tutorials',
        component: AdminTutorials
      },
      {
        path: 'contact-messages',
        name: 'admin-contact-messages',
        component: AdminContactMessages
      },
      {
        path: 'about',
        name: 'admin-about',
        component: AdminAbout
      }
    ]
  },
  // 404 strona - zawsze jako ostatnia
  {
    path: '/:pathMatch(.*)*',
    name: 'not-found',
    component: NotFound,
    meta: {
      layout: 'default'
    }
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
  
  // Zawsze czekaj na inicjalizację auth state przed podjęciem decyzji o przekierowaniu
  let authInitialized = false;
  if (!authStore.authInitialized) {
    console.log('Auth not initialized, initializing now...');
    try {
      await authStore.initAuth();
      authInitialized = true;
      console.log('Auth initialized successfully:', authStore.isLoggedIn ? 'User is logged in' : 'User is not logged in');
    } catch (error) {
      console.error('Failed to initialize auth:', error);
    }
  } else {
    authInitialized = true;
    console.log('Auth already initialized');
  }
  
  // Sprawdź, czy trasa wymaga autoryzacji
  if (to.matched.some(record => record.meta.requiresAuth)) {
    // Sprawdź, czy użytkownik jest zalogowany
    if (!authStore.isLoggedIn) {
      console.log('Route requires auth but user is not logged in, redirecting to login');
      // Przekieruj na stronę logowania
      next({
        path: '/login',
        query: { redirect: to.fullPath }
      });
    } else {
      // Sprawdź, czy trasa wymaga uprawnień administratora
      if (to.matched.some(record => record.meta.requiresAdmin)) {
        if (!authStore.isAdmin) {
          console.log('Route requires admin but user is not admin, redirecting to home');
          // Przekieruj na stronę główną, jeśli użytkownik nie jest adminem
          next({ path: '/' });
        } else {
          console.log('User is admin, proceeding to admin route');
          next();
        }
      }
      // Sprawdź, czy użytkownik zweryfikował email (jeśli wymagane)
      else if (to.matched.some(record => record.meta.requiresVerified) && 
          authStore.user && !authStore.user.email_verified_at) {
        console.log('Route requires verified email but user email is not verified');
        next({ path: '/email/verify' });
      } else {
        console.log('User is authenticated, proceeding to protected route');
        next();
      }
    }
  } 
  // Sprawdź, czy trasa wymaga, aby użytkownik nie był zalogowany (np. login, register)
  else if (to.matched.some(record => record.meta.guest)) {
    // Jeśli użytkownik jest już zalogowany, przekieruj go na stronę główną
    if (authStore.isLoggedIn) {
      console.log('Guest route but user is logged in, redirecting to home');
      next({ path: '/' });
    } else {
      console.log('User is not logged in, proceeding to guest route');
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
    console.log('No special route conditions, proceeding normally');
    next();
  }
});

export default router; 