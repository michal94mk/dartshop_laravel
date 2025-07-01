import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../stores/authStore';

// Import page components
import Home from '../pages/Home.vue';
import ProductList from '../pages/ProductList.vue';
import ProductDetails from '../pages/ProductDetails.vue';
import Cart from '../pages/Cart.vue';
import Checkout from '../pages/Checkout.vue';
import PaymentSuccess from '../pages/PaymentSuccess.vue';
import About from '../pages/About.vue';
import Contact from '../pages/Contact.vue';
import Privacy from '../pages/Privacy.vue';
import Terms from '../pages/Terms.vue';
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
import NewsletterVerification from '../pages/NewsletterVerification.vue';
import GoogleCallback from '../pages/GoogleCallback.vue';
import AutoLogin from '../pages/AutoLogin.vue';
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
import AdminNewsletter from '../pages/admin/Newsletter.vue';
import AdminPrivacyPolicies from '../pages/admin/PrivacyPolicies.vue';
import AdminTermsOfService from '../pages/admin/TermsOfService.vue';

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
    path: '/payment/success',
    name: 'payment-success',
    component: PaymentSuccess,
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
    path: '/privacy',
    name: 'privacy',
    component: Privacy,
    meta: {
      layout: 'default'
    }
  },
  {
    path: '/terms',
    name: 'terms',
    component: Terms,
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
  {
    path: '/email/verify/:id/:hash',
    name: 'verify-email-with-hash',
    component: VerifyEmail,
    props: true,
    meta: {
      layout: 'default'
    }
  },
  {
    path: '/newsletter/verify',
    name: 'newsletter-verify',
    component: NewsletterVerification,
    meta: {
      layout: 'default'
    }
  },
  {
    path: '/auth/google/callback',
    name: 'google-callback',
    component: GoogleCallback,
    meta: {
      layout: 'default'
    }
  },
  {
    path: '/auth/auto-login',
    name: 'auto-login',
    component: AutoLogin,
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
      },
      {
        path: 'newsletter',
        name: 'admin-newsletter',
        component: AdminNewsletter
      },
      {
        path: 'privacy-policies',
        name: 'admin-privacy-policies',
        component: AdminPrivacyPolicies
      },
      {
        path: 'terms-of-service',
        name: 'admin-terms-of-service',
        component: AdminTermsOfService
      }
    ]
  },
  // 404 page - always as last
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
  console.log(`=== Router Guard: ${from.path} -> ${to.path} ===`);
  console.log('Query params:', to.query);
  
  const authStore = useAuthStore();
  
  // Special check: if logging out from admin panel, skip checks
  if (from.path && from.path.startsWith('/admin') && to.path === '/' && authStore.isLoading) {
    console.log('Logout from admin panel detected, allowing redirect to home');
    next();
    return;
  }
  
  // Log current auth state
  console.log('Current auth state:', {
    isLoggedIn: authStore.isLoggedIn,
    authInitialized: authStore.authInitialized,
    user: authStore.user?.email || 'no user',
    hasLocalStorage: !!localStorage.getItem('user')
  });
  
  // Always wait for auth state initialization before making redirect decisions
  let authInitialized = false;
  if (!authStore.authInitialized) {
    console.log('Auth not initialized, initializing now...');
    try {
      await authStore.initAuth();
      authInitialized = true;
      console.log('Auth initialized successfully:', {
        isLoggedIn: authStore.isLoggedIn,
        user: authStore.user?.email || 'no user'
      });
    } catch (error) {
      console.error('Failed to initialize auth:', error);
    }
  } else {
    authInitialized = true;
    console.log('Auth already initialized');
  }
  
  // Check if route requires authorization
  if (to.matched.some(record => record.meta.requiresAuth)) {
    console.log('Route requires auth, checking login status...');
    
    // Check if user is logged in
    if (!authStore.isLoggedIn) {
      console.log('User is NOT logged in, checking special cases...');
      
      // Special handling for email verification success
      if (to.path === '/profile' && to.query.verified === 'success') {
        console.log('Email verification success detected, trying to refresh auth...');
        
        // Try to refresh auth one more time
        try {
          await authStore.refreshUser();
          console.log('Auth refreshed after email verification:', {
            isLoggedIn: authStore.isLoggedIn,
            user: authStore.user?.email || 'no user'
          });
          
          if (authStore.isLoggedIn) {
            console.log('User is now logged in after refresh, proceeding to profile');
            next();
            return;
          }
        } catch (error) {
          console.error('Failed to refresh auth after email verification:', error);
        }
      }
      
      // Special check: if coming from Google Callback, wait for auth
      if (from.name === 'google-callback') {
        console.log('Coming from Google Callback, waiting for auth state...');
        // Give more time for auth state update
        setTimeout(() => {
          console.log('Router Guard: Checking auth state after delay:', {
            isLoggedIn: authStore.isLoggedIn,
            authInitialized: authStore.authInitialized,
            user: authStore.user?.email
          });
          
          if (authStore.isLoggedIn) {
            console.log('Auth state updated, proceeding to protected route');
            next();
          } else {
            console.log('Auth state still not updated, redirecting to login');
            next({
              path: '/login',
              query: { redirect: to.fullPath }
            });
          }
        }, 800);
        return;
      }
      
      console.log('No special cases, redirecting to login');
      console.log('Redirect URL will be:', to.fullPath);
      
      // Redirect to login page
      next({
        path: '/login',
        query: { redirect: to.fullPath }
      });
      return;
    }
    
    console.log('User IS logged in, checking admin permissions...');
    
    // Check if route requires admin permissions
    if (to.matched.some(record => record.meta.requiresAdmin)) {
      if (!authStore.isAdmin) {
        console.log('Route requires admin but user is not admin, redirecting to home');
        // Redirect to home page if user is not admin
        next({ path: '/' });
        return;
      } else {
        console.log('User is admin, proceeding to admin route');
        next();
        return;
      }
    }
    
    // Check if user has verified email (if required)
    if (to.matched.some(record => record.meta.requiresVerified) && 
        authStore.user && !authStore.user.email_verified_at) {
      console.log('Route requires verified email but user email is not verified');
      next({ path: '/email/verify' });
      return;
    }
    
    console.log('User is authenticated, proceeding to protected route');
    next();
    return;
  } 
  // Check if route requires user not to be logged in (e.g. login, register)
  else if (to.matched.some(record => record.meta.guest)) {
    // If user is already logged in, redirect to home page
    if (authStore.isLoggedIn) {
      console.log('Guest route but user is logged in, redirecting to home');
      next({ path: '/' });
    } else {
      console.log('User is not logged in, proceeding to guest route');
      next();
    }
  } 
  // Handle loading of same routes
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