import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import i18n from './i18n'
import './assets/tailwind.css'

import AppSidebar from './components/AppSidebar.vue'
import AppTopBar from './components/AppTopBar.vue'

const app = createApp(App)

app.component('AppSidebar', AppSidebar)
app.component('AppTopBar', AppTopBar)

app.use(router)
app.use(i18n)

app.mount('#app')