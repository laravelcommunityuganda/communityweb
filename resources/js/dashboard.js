import { createApp } from 'vue'
import { createPinia } from 'pinia'
import DashboardApp from './DashboardApp.vue'
import router from './router/dashboard'
import './bootstrap'

const app = createApp(DashboardApp)

app.use(createPinia())
app.use(router)

app.mount('#app')
