import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../stores/authStore';

// Import critical components directly (for better UX)
import Home from '../pages/Home.vue';
import NotFound from '../pages/NotFound.vue';

// Lazy load all other page components
const ProductList = () => import(/* webpackChunkName: "products" */ '../pages/ProductList.vue');
const ProductDetails = () => import(/* webpackChunkName: "products" */ '../pages/ProductDetails.vue');
const Cart = () => import(/* webpackChunkName: "shopping" */ '../pages/Cart.vue');
const Checkout = () => import(/* webpackChunkName: "shopping" */ '../pages/Checkout.vue');
const PaymentSuccess = () => import(/* webpackChunkName: "shopping" */ '../pages/PaymentSuccess.vue');

// Content pages
const About = () => import(/* webpackChunkName: "content" */ '../pages/About.vue');
const Contact = () => import(/* webpackChunkName: "content" */ '../pages/Contact.vue');
const Privacy = () => import(/* webpackChunkName: "content" */ '../pages/Privacy.vue');
const Terms = () => import(/* webpackChunkName: "content" */ '../pages/Terms.vue');
const Promotions = () => import(/* webpackChunkName: "content" */ '../pages/Promotions.vue');
const Tutorials = () => import(/* webpackChunkName: "content" */ '../pages/Tutorials.vue');
const Tutorial = () => import(/* webpackChunkName: "content" */ '../pages/Tutorial.vue');

// Auth components
const Login = () => import(/* webpackChunkName: "auth" */ '../pages/Login.vue');
const Register = () => import(/* webpackChunkName: "auth" */ '../pages/Register.vue');
const Profile = () => import(/* webpackChunkName: "auth" */ '../pages/Profile.vue');
const ForgotPassword = () => import(/* webpackChunkName: "auth" */ '../pages/ForgotPassword.vue');
const ResetPassword = () => import(/* webpackChunkName: "auth" */ '../pages/ResetPassword.vue');
const VerifyEmail = () => import(/* webpackChunkName: "auth" */ '../pages/VerifyEmail.vue');
const NewsletterVerification = () => import(/* webpackChunkName: "auth" */ '../pages/NewsletterVerification.vue');
const GoogleCallback = () => import(/* webpackChunkName: "auth" */ '../pages/GoogleCallback.vue');
const AutoLogin = () => import(/* webpackChunkName: "auth" */ '../pages/AutoLogin.vue');
// Keep AdminLayout as direct import (needed for route structure)
import AdminLayout from '../components/layouts/AdminLayout.vue';

// Lazy load admin components
const AdminDashboard = () => import(/* webpackChunkName: "admin" */ '../pages/admin/Dashboard.vue');
const AdminProducts = () => import(/* webpackChunkName: "admin" */ '../pages/admin/Products.vue');
const AdminCategories = () => import(/* webpackChunkName: "admin" */ '../pages/admin/Categories.vue');
const AdminUsers = () => import(/* webpackChunkName: "admin" */ '../pages/admin/Users.vue');
const AdminOrders = () => import(/* webpackChunkName: "admin" */ '../pages/admin/Orders.vue');
const AdminBrands = () => import(/* webpackChunkName: "admin" */ '../pages/admin/Brands.vue');
const AdminReviews = () => import(/* webpackChunkName: "admin" */ '../pages/admin/Reviews.vue');
const AdminPromotions = () => import(/* webpackChunkName: "admin" */ '../pages/admin/Promotions.vue');
const AdminTutorials = () => import(/* webpackChunkName: "admin" */ '../pages/admin/Tutorials.vue');
const AdminContactMessages = () => import(/* webpackChunkName: "admin" */ '../pages/admin/ContactMessages.vue');
const AdminAbout = () => import(/* webpackChunkName: "admin" */ '../pages/admin/About.vue');
const AdminNewsletter = () => import(/* webpackChunkName: "admin" */ '../pages/admin/Newsletter.vue');
const AdminPrivacyPolicies = () => import(/* webpackChunkName: "admin" */ '../pages/admin/PrivacyPolicies.vue');
const AdminTermsOfService = () => import(/* webpackChunkName: "admin" */ '../pages/admin/TermsOfService.vue');

const routes = [
  {
    path: '/',
    name: 'home',
    component: Home,
    meta: {
      layout: 'default',
      title: 'DartShop'
    }
  },
  {
    path: '/products',
    name: 'products',
    component: ProductList,
    meta: { 
      reloadAlways: true,
      layout: 'default',
      title: 'Produkty'
    }
  },
  {
    path: '/products/:id',
    name: 'product-details',
    component: ProductDetails,
    props: true,
    meta: {
      layout: 'default',
      title: 'Szczegóły produktu'
    }
  },
  {
    path: '/cart',
    name: 'cart',
    component: Cart,
    meta: {
      layout: 'default',
      title: 'Koszyk'
    }
  },
  {
    path: '/checkout',
    name: 'checkout',
    component: Checkout,
    meta: {
      layout: 'default',
      title: 'Kasa'
    }
  },
  {
    path: '/payment/success',
    name: 'payment-success',
    component: PaymentSuccess,
    meta: {
      layout: 'default',
      title: 'Płatność zakończona'
    }
  },
  {
    path: '/about',
    name: 'about',
    component: About,
    meta: {
      layout: 'default',
      title: 'O nas'
    }
  },
  {
    path: '/contact',
    name: 'contact',
    component: Contact,
    meta: {
      layout: 'default',
      title: 'Kontakt'
    }
  },
  {
    path: '/privacy',
    name: 'privacy',
    component: Privacy,
    meta: {
      layout: 'default',
      title: 'Polityka prywatności'
    }
  },
  {
    path: '/terms',
    name: 'terms',
    component: Terms,
    meta: {
      layout: 'default',
      title: 'Regulamin'
    }
  },
  {
    path: '/promotions',
    name: 'promotions',
    component: Promotions,
    meta: {
      layout: 'default',
      title: 'Promocje'
    }
  },
  {
    path: '/tutorials',
    name: 'tutorials',
    component: Tutorials,
    meta: {
      layout: 'default',
      title: 'Poradniki'
    }
  },
  {
    path: '/tutorials/:slug',
    name: 'tutorial',
    component: Tutorial,
    props: true,
    meta: {
      layout: 'default',
      title: 'Poradnik'
    }
  },
  // Auth routes
  {
    path: '/login',
    name: 'login',
    component: Login,
    meta: {
      guest: true,
      layout: 'default',
      title: 'Logowanie'
    }
  },
  {
    path: '/register',
    name: 'register',
    component: Register,
    meta: {
      guest: true,
      layout: 'default',
      title: 'Rejestracja'
    }
  },
  {
    path: '/profile',
    name: 'profile',
    component: Profile,
    meta: {
      requiresAuth: true,
      layout: 'default',
      title: 'Profil'
    }
  },
  {
    path: '/forgot-password',
    name: 'forgot-password',
    component: ForgotPassword,
    meta: {
      guest: true,
      layout: 'default',
      title: 'Odzyskiwanie hasła'
    }
  },
  {
    path: '/reset-password/:token',
    name: 'reset-password',
    component: ResetPassword,
    meta: {
      guest: true,
      layout: 'default',
      title: 'Resetowanie hasła'
    }
  },
  {
    path: '/email/verify',
    name: 'verify-email',
    component: VerifyEmail,
    meta: {
      layout: 'default',
      title: 'Weryfikacja email'
    }
  },
  {
    path: '/email/verify/:id',
    name: 'verify-email-with-id',
    component: VerifyEmail,
    props: true,
    meta: {
      layout: 'default',
      title: 'Weryfikacja email'
    }
  },
  {
    path: '/email/verify/:id/:hash',
    name: 'verify-email-with-hash',
    component: VerifyEmail,
    props: true,
    meta: {
      layout: 'default',
      title: 'Weryfikacja email'
    }
  },
  {
    path: '/newsletter/verify',
    name: 'newsletter-verify',
    component: NewsletterVerification,
    meta: {
      layout: 'default',
      title: 'Weryfikacja newslettera'
    }
  },
  {
    path: '/newsletter/verified',
    name: 'newsletter-verified',
    component: () => import('../pages/NewsletterVerified.vue'),
    meta: {
      layout: 'default',
      title: 'Newsletter zweryfikowany'
    }
  },
  {
    path: '/newsletter/verify',
    name: 'newsletter-verify-error',
    component: () => import('../pages/NewsletterVerify.vue'),
    meta: {
      layout: 'default',
      title: 'Błąd weryfikacji newslettera'
    }
  },
  {
    path: '/newsletter/unsubscribe',
    name: 'newsletter-unsubscribe',
    component: () => import('../pages/NewsletterUnsubscribe.vue'),
    meta: {
      layout: 'default',
      title: 'Wypisz się z newslettera'
    }
  },
  {
    path: '/auth/google/callback',
    name: 'google-callback',
    component: GoogleCallback,
    meta: {
      layout: 'default',
      title: 'Logowanie Google'
    }
  },
  {
    path: '/auth/auto-login',
    name: 'auto-login',
    component: AutoLogin,
    meta: {
      layout: 'default',
      title: 'Automatyczne logowanie'
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
        component: AdminDashboard,
        meta: {
          title: 'Panel Admina'
        }
      },
      {
        path: 'products',
        name: 'admin-products',
        component: AdminProducts,
        meta: {
          title: 'Admin - Produkty'
        }
      },
      {
        path: 'categories',
        name: 'admin-categories',
        component: AdminCategories,
        meta: {
          title: 'Admin - Kategorie'
        }
      },
      {
        path: 'users',
        name: 'admin-users',
        component: AdminUsers,
        meta: {
          title: 'Admin - Użytkownicy'
        }
      },
      {
        path: 'orders',
        name: 'admin-orders',
        component: AdminOrders,
        meta: {
          title: 'Admin - Zamówienia'
        }
      },
      {
        path: 'brands',
        name: 'admin-brands',
        component: AdminBrands,
        meta: {
          title: 'Admin - Marki'
        }
      },
      {
        path: 'reviews',
        name: 'admin-reviews',
        component: AdminReviews,
        meta: {
          title: 'Admin - Recenzje'
        }
      },
      {
        path: 'promotions',
        name: 'admin-promotions',
        component: AdminPromotions,
        meta: {
          title: 'Admin - Promocje'
        }
      },
      {
        path: 'tutorials',
        name: 'admin-tutorials',
        component: AdminTutorials,
        meta: {
          title: 'Admin - Poradniki'
        }
      },
      {
        path: 'contact-messages',
        name: 'admin-contact-messages',
        component: AdminContactMessages,
        meta: {
          title: 'Admin - Wiadomości'
        }
      },
      {
        path: 'about',
        name: 'admin-about',
        component: AdminAbout,
        meta: {
          title: 'Admin - O nas'
        }
      },
      {
        path: 'newsletter',
        name: 'admin-newsletter',
        component: AdminNewsletter,
        meta: {
          title: 'Admin - Newsletter'
        }
      },
      {
        path: 'privacy-policies',
        name: 'admin-privacy-policies',
        component: AdminPrivacyPolicies,
        meta: {
          title: 'Admin - Prywatność'
        }
      },
      {
        path: 'terms-of-service',
        name: 'admin-terms-of-service',
        component: AdminTermsOfService,
        meta: {
          title: 'Admin - Regulamin'
        }
      }
    ]
  },
  // 404 page - always as last
  {
    path: '/:pathMatch(.*)*',
    name: 'not-found',
    component: NotFound,
    meta: {
      layout: 'default',
      title: 'Strona nie znaleziona'
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

// Router navigation guard
router.beforeEach(async (to, from, next) => {
  
  const authStore = useAuthStore();
  
  // Special check: if logging out from admin panel, skip checks
  if (from.path && from.path.startsWith('/admin') && to.path === '/' && authStore.isLoading) {
    console.log('Logging out from admin panel, allowing navigation');
    next();
    return;
  }
  
  // Special check: if user is logging out and going to home page
  if (authStore.isLoading && to.path === '/') {
    console.log('User is logging out, allowing navigation to home');
    next();
    return;
  }

  // Special check: if returning from Stripe payment
  if (to.name === 'payment-success' && to.query.session_id) {
    console.log('Returning from Stripe payment, refreshing auth state');
    try {
      await authStore.refreshUser();
    } catch (error) {
      console.error('Failed to refresh auth after Stripe payment:', error);
    }
    next();
    return;
  }
  
  // Always wait for auth state initialization before making redirect decisions
  if (!authStore.authInitialized) {
    try {
      await authStore.initAuth();
    } catch (error) {
      console.error('Failed to initialize auth:', error);
    }
  }
  
  // Check if route requires authorization
  if (to.matched.some(record => record.meta.requiresAuth)) {
    // Check if user is logged in
    if (!authStore.isLoggedIn) {
      // Special handling for email verification success
      if (to.path === '/profile' && to.query.verified === 'success') {
        // Try to refresh auth one more time
        try {
          await authStore.refreshUser();
          
          if (authStore.isLoggedIn) {
            next();
            return;
          }
        } catch (error) {
          console.error('Failed to refresh auth after email verification:', error);
        }
      }
      
      // Special check: if coming from Google Callback, wait for auth
      if (from.name === 'google-callback') {
        // Give more time for auth state update
        setTimeout(() => {
          if (authStore.isLoggedIn) {
            next();
          } else {
            next({
              path: '/login',
              query: { redirect: to.fullPath }
            });
          }
        }, 800);
        return;
      }
      
      // Redirect to login page
      next({
        path: '/login',
        query: { redirect: to.fullPath }
      });
      return;
    }
    
    // Check if route requires admin permissions
    if (to.matched.some(record => record.meta.requiresAdmin)) {
      if (!authStore.isAdmin) {
        // Redirect to home page if user is not admin
        next({ path: '/' });
        return;
      } else {
        next();
        return;
      }
    }
    
    // Check if user has verified email (if required)
    if (to.matched.some(record => record.meta.requiresVerified) && 
        authStore.user && !authStore.user.email_verified_at) {
      next({ path: '/email/verify' });
      return;
    }
    
    next();
    return;
  } 
  // Check if route requires user not to be logged in (e.g. login, register)
  else if (to.matched.some(record => record.meta.guest)) {
    // If user is already logged in, redirect to home page
    if (authStore.isLoggedIn) {
      next({ path: '/' });
    } else {
      next();
    }
  } 
  else {
    next();
  }
});

// Set page title after each route change
router.afterEach((to) => {
  // Use the title from route meta or fall back to default
  const defaultTitle = 'DartShop';
  const title = to.meta.title || defaultTitle;
  document.title = title;
});

export default router; 