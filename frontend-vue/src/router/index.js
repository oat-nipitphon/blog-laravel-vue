import { createRouter, createWebHistory } from 'vue-router'
import IndexView from '@/views/IndexView.vue'
import HomeView from '@/views/HomeView.vue'

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {
            path: '/',
            name: 'IndexView',
            component: IndexView
        },
        {
            path: '/home',
            name: 'HomeView',
            component: HomeView
        },
    ],
})

export default router