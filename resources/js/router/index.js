import { createRouter, createWebHistory } from 'vue-router';

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

// Debug router navigation
router.beforeEach((to, from, next) => {
  console.log(`Router navigating from ${from.path} to ${to.path}`);
  next();
});

export default router; 