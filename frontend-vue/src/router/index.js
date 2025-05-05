import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
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


router.beforeEach(async (to, from) => {
    const authStore = useAuthStore();
    await authStore.apiAuthStore();
  
    if (!authStore.storeUser && to.meta.auth) {
      return { name: 'IndexView' };
    }
  
    if (authStore.storeUser && to.meta.guest) {
      return { name: 'HomeView' };
    }
  });

export default router