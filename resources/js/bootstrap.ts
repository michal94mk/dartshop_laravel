import _ from 'lodash';
import axios, { type AxiosInstance, type InternalAxiosRequestConfig, type AxiosResponse, type AxiosError } from 'axios';

// Type definitions
interface Window {
  _: typeof _;
  axios: AxiosInstance;
}

declare global {
  interface Window {
    _: typeof _;
    axios: AxiosInstance;
  }
}

// Set global lodash
window._ = _;

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

// Set global axios
window.axios = axios;

// Ensure we're not using any cached Axios instances
if (!axios.defaults.headers.common) {
  axios.defaults.headers.common = {};
}

// Basic Axios defaults
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.withCredentials = true;

// Function to get CSRF token from meta tag
const getMetaToken = (): string | null => {
  const metaElement: HTMLMetaElement | null = document.querySelector('meta[name="csrf-token"]');
  return metaElement?.getAttribute('content') || null;
};

// Function to get CSRF token from cookie
const getCookieToken = (): string | undefined => {
  return document.cookie
    .split('; ')
    .find((cookie: string) => cookie.startsWith('XSRF-TOKEN='))
    ?.split('=')[1];
};

// Set up CSRF token in axios headers
const setCsrfToken = (): void => {
  const token: string | undefined = getCookieToken();
  const metaToken: string | null = getMetaToken();
  
  if (token) {
    axios.defaults.headers.common['X-XSRF-TOKEN'] = decodeURIComponent(token);
    if (import.meta.env.DEV) console.log('CSRF token set from cookie:', token);
  }
  
  if (metaToken) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = metaToken;
    if (import.meta.env.DEV) console.log('CSRF token set from meta tag:', metaToken);
  }
};

// Set initial CSRF token
setCsrfToken();

// Add request interceptor to ensure CSRF token is set before each request
axios.interceptors.request.use(
  (config: InternalAxiosRequestConfig): InternalAxiosRequestConfig => {
    setCsrfToken(); // Refresh token before each request
    return config;
  },
  (error: AxiosError): Promise<AxiosError> => {
    return Promise.reject(error);
  }
);

// Add response interceptor to handle CSRF token errors
axios.interceptors.response.use(
  (response: AxiosResponse): AxiosResponse => {
    if (import.meta.env.DEV) console.log('Response:', response);
    return response;
  },
  async (error: AxiosError): Promise<AxiosResponse> => {
    if (error.response?.status === 419) {
      if (import.meta.env.DEV) console.log('CSRF token mismatch detected, refreshing token...');
      try {
        await axios.get('/sanctum/csrf-cookie');
        setCsrfToken();
        // Retry the original request
        return axios(error.config!);
      } catch (refreshError) {
        if (import.meta.env.DEV) console.error('Failed to refresh CSRF token:', refreshError);
      }
    }
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
