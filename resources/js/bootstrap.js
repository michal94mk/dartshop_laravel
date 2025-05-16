import _ from 'lodash';
window._ = _;

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

import axios from 'axios';
window.axios = axios;

// Ensure we're not using any cached Axios instances
axios.defaults = axios.defaults || {};
axios.defaults.headers = axios.defaults.headers || {};
axios.defaults.headers.common = axios.defaults.headers.common || {};

// Basic Axios defaults
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.withCredentials = true;

// Make sure CSRF cookies and tokens work properly
axios.defaults.headers.common['X-XSRF-TOKEN'] = document.cookie
  .split('; ')
  .find(cookie => cookie.startsWith('XSRF-TOKEN='))
  ?.split('=')[1];

// Add CSRF token from meta tag
document.addEventListener('DOMContentLoaded', () => {
  const token = document.head.querySelector('meta[name="csrf-token"]');
  if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
    console.log('CSRF token set from meta tag:', token.content);
  } else {
    console.error('CSRF token not found');
  }

  // Use Laravel object if available
  if (window.Laravel && window.Laravel.csrfToken) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = window.Laravel.csrfToken;
    console.log('CSRF token set from window.Laravel:', window.Laravel.csrfToken);
    
    // Set base URL for API if available
    if (window.Laravel.apiUrl) {
      console.log('API base URL set to:', window.Laravel.apiUrl);
    }
  }
});

// Log axios requests and responses for debugging
axios.interceptors.request.use(request => {
    console.log('Starting Request', request);
    return request;
});

axios.interceptors.response.use(
    response => {
        console.log('Response:', response);
        return response;
    },
    error => {
        console.error('API Error:', error);
        return Promise.reject(error);
    }
);

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo';

// import Pusher from 'pusher-js';
// window.Pusher = Pusher;

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: import.meta.env.VITE_PUSHER_APP_KEY,
//     cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
//     forceTLS: true
// });
