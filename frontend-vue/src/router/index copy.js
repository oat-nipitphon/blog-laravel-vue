import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import PageNotFound from '@/views/PageNotFound.vue'
import HomeView from '../views/HomeView.vue'
import RegisterView from '@/views/RegisterView.vue'
import LoginView from '@/views/LoginView.vue'
import ForgetYourPasswordView from '@/views/ForgetYourPasswordView.vue'
import DashboardView from '@/views/DashboardView.vue'

// --------------------------------- Zone ADMIN ------------------------------------------------

//  User Profiles 
import AdminDashboardView from '@/views/ADMIN/AdminDashboardView.vue'
import AdminManagerUserProfileView from '@/views/ADMIN/USER/AdminManagerUserProfileView.vue'


//  Posts 
import AdminGetUserProfileEdit from '@/views/ADMIN/USER/AdminGetUserProfileEdit.vue'
import AdminManagerPostView from '@/views/ADMIN/Posts/AdminManagerPostView.vue'


//  Reward 
import AdminManagerReward from '@/views/ADMIN/Reward/AdminManagerReward.vue'
import AdminEditRewardView from '@/views/ADMIN/Reward/AdminEditRewardView.vue'
import AdminNewRewardView from '@/views/ADMIN/Reward/AdminNewRewardView.vue'


// --------------------------------- Zone USER -------------------------------------------------


//  User Profiles 
import DashboardProfile from '@/views/USER/Users/DashboardProfile.vue'

//  Posts 
import CreatePostNewView from '@/views/USER/Posts/CreatePostNewView.vue'
import EditPostView from '@/views/USER/Posts/EditPostView.vue'
import DetailPostView from '@/views/USER/Posts/DetailPostView.vue'
import ReportRecoverPostsView from '@/views/USER/Posts/ReportRecoverPostsView.vue';


//  Reward 
import DashboardRewardView from '@/views/Reward/DashboardRewardView.vue'
import ReportReward from '@/views/Reward/ReportReward.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/testCodeView',
      component: () => import('../views/TestCodeView.vue')
    },
    {
      path: '/:pathMatch(.*)*',
      component: PageNotFound
    },

    {
      path: '/',
      name: 'IndexView',
      component: () => import('@/views/IndexView.vue'),
      meta: { guest:true }
    },
    {
      path: '/home',
      name: 'HomeView',
      component: HomeView,
      meta: { auth: true }
    },
    {
      path: '/RegisterView',
      name: 'RegisterView',
      component: RegisterView,
      meta: { guest: true }
    },
    {
      path: '/LoginView',
      name: 'LoginView',
      component: LoginView,
      meta: { guest: true }
    },
    {
      path: '/ForgetYourPasswordView',
      name: 'ForgetYourPasswordView',
      component: ForgetYourPasswordView,
      meta: { guest: true }
    },
    {
      path: '/DashboardView',
      name: 'DashboardView',
      component: DashboardView,
      meta: { auth: true }
    },


    // User Profile
    {
      path: '/user_profiles/show/:id',
      name: 'DashboardProfile',
      component: DashboardProfile,
      meta: { auth: true }
    },


    // Post
    {
      path: '/CreatePostNewView',
      name: 'CreatePostNewView',
      component: CreatePostNewView,
      meta: { auth: true }
    },
    {
      path: '/posts/:id',
      name: 'EditPostView',
      component: EditPostView,
      meta: { auth: true }
    },
    {
      path: '/DetailPostView',
      name: 'DetailPostView',
      component: DetailPostView,
      meta: { auth: true }
    },
    {
      path: '/posts/report_recover/:userID',
      name: 'ReportRecoverPostsView',
      component: ReportRecoverPostsView,
      meta: { auth: true }
    },


    // Reward
    {
      path: '/reward/DashboardRewardView',
      name: 'DashboardRewardView',
      component: DashboardRewardView,
      meta: { auth: true }
    },
    {
      path: '/cartItems/getReportReward/:userID',
      name: 'ReportReward',
      component: ReportReward,
      meta: { auth: true }
    },


    // Admin Manager
    {
      path: '/AdminDashboardView',
      name: 'AdminDashboardView',
      component: AdminDashboardView,
      meta: { auth: true } 
    },

    // User Profile
    {
      path: '/AdminManagerUserProfileView',
      name: 'AdminManagerUserProfileView',
      component: AdminManagerUserProfileView,
      meta: { auth: true }
    },
    {
      path: '/admin/userProfiles/manager/:userProfileID',
      name: 'AdminGetUserProfileEdit',
      component: AdminGetUserProfileEdit,
      meta: { auth: true }
    },

    // Post
    {
      path: '/AdminManagerPostView',
      name: 'AdminManagerPostView',
      component: AdminManagerPostView,
      meta: { auth: true }
    },

    // Reward
    {
      path: '/reward/AdminManagerReward',
      name: 'AdminManagerReward',
      component: AdminManagerReward,
      meta: { auth: true }
    },
    {
      path: '/new/reward',
      name: ' AdminNewRewardView',
      component:  AdminNewRewardView,
      meta: { auth: true }
    },
    {
      path: '/new/reward',
      name: 'NewRewardView',
      component:  AdminNewRewardView,
      meta: { auth: true }
    },
    {
      path: '/reward/update/:id',
      name: ' AdminEditRewardView',
      component:  AdminEditRewardView,
      meta: { auth: true }
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
    return { name: 'DashboardView' };
  }
});

export default router
