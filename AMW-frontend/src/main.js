import { createApp } from 'vue'
import App from './App.vue'
import router from './router'

createApp(App)
    .use(router)  // Usar el router en tu aplicación
    .mount('#app')