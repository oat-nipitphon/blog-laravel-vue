import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import IndexView from '@/views/IndexView.vue'
import HomeView from '@/views/HomeView.vue'

import CreatePostView from '@/views/post/CreatePostView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'IndexView',
      component: IndexView,
      meta: { guest: true }
    },

    {
      path: '/home',
      name: 'HomeView',
      component: HomeView,
      meta: { auth: true }
    },

    {
      path: '/create_post',
      name: 'CreatePostView',
      component: CreatePostView,
      meta: { auth: true }
    },

  ],
})


router.beforeEach(async (to, from) => {

  const authStore = useAuthStore();
  await authStore.storeAPIStartAuth();

  if (!authStore.users && to.meta.auth) {
    return { name: 'IndexView' };
  }

  if (authStore.users && to.meta.guest ) {
    return { name: 'HomeView' }
  }

});


export default router