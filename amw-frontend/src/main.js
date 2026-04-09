import { createApp } from 'vue';
import App from './App.vue';
import router from './router';  // Importa el router que ya está configurado
import './assets/tailwind.css';

createApp(App)
    .use(router)  // Usa el router
    .mount('#app');