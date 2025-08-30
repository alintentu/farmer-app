import { createApp } from 'vue'
import { createPinia } from 'pinia'
import { createI18n } from 'vue-i18n'
import Toast from 'vue-toastification'

import App from './App.vue'
import router from './router'

import './assets/main.css'
import 'vue-toastification/dist/index.css'

// Create Vue app
const app = createApp(App)

// Create Pinia store
const pinia = createPinia()

// Create i18n instance
const i18n = createI18n({
  legacy: false,
  locale: 'en',
  fallbackLocale: 'en',
  messages: {
    en: {
      // English translations will be added here
    }
  }
})

// Use plugins
app.use(pinia)
app.use(router)
app.use(i18n)
app.use(Toast, {
  position: 'top-right',
  timeout: 5000,
  closeOnClick: true,
  pauseOnFocusLoss: true,
  pauseOnHover: true,
  draggable: true,
  draggablePercent: 0.6,
  showCloseButtonOnHover: false,
  hideProgressBar: false,
  closeButton: 'button',
  icon: true,
  rtl: false
})

// Mount app
app.mount('#app')
