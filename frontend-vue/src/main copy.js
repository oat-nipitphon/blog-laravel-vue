import './assets/main.css'
import './assets/tailwind.css'

import VueSweetalert2 from 'vue-sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

// // import Bootstrap 5
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap/dist/js/bootstrap.bundle'

import { createApp, markRaw } from 'vue'
import { createPinia } from 'pinia'

import App from './App.vue'
import router from './router'

// import Toggle from 'vue3-toggle';

const app = createApp(App)
const pinia = createPinia()


pinia.use(({store}) => {
    store.router = markRaw(router)
})

app.use(pinia)
app.use(VueSweetalert2)
app.use(router)
// app.component('Toggle', Toggle);
app.mount('#app')
