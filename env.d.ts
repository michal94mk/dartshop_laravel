/// <reference types="vite/client" />

declare module '*.vue' {
  import type { DefineComponent } from 'vue'
  const component: DefineComponent<{}, {}, any>
  export default component
}

declare module 'vue-router' {
  interface RouteMeta {
    requiresAuth?: boolean
    requiresAdmin?: boolean
    requiresVerified?: boolean
    guest?: boolean
    layout?: 'default' | 'admin'
    title?: string
    reloadAlways?: boolean
  }
}

// Laravel global variables
declare global {
  interface Window {
    Laravel: {
      isAdmin?: boolean
      csrfToken?: string
    }
    axios: any
    _: any
    Alpine: any
    debug: (...args: any[]) => void
  }
}

// Vite environment variables
interface ImportMetaEnv {
  readonly VITE_APP_NAME: string
  readonly VITE_PUSHER_APP_KEY: string
  readonly VITE_PUSHER_HOST: string
  readonly VITE_PUSHER_PORT: string
  readonly VITE_PUSHER_SCHEME: string
  readonly VITE_PUSHER_APP_CLUSTER: string
}

interface ImportMeta {
  readonly env: ImportMetaEnv
}
