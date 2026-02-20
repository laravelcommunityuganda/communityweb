import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const routes = [
  {
    path: '/',
    name: 'home',
    component: () => import('@/views/Home.vue'),
  },
  {
    path: '/register',
    name: 'register',
    component: () => import('@/views/auth/Register.vue'),
    meta: { guest: true },
  },
  {
    path: '/login',
    name: 'login',
    component: () => import('@/views/auth/Login.vue'),
    meta: { guest: true },
  },
  {
    path: '/forgot-password',
    name: 'forgot-password',
    component: () => import('@/views/auth/ForgotPassword.vue'),
    meta: { guest: true },
  },
  {
    path: '/dashboard',
    name: 'dashboard',
    component: () => import('@/views/Dashboard.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/community',
    name: 'community',
    component: () => import('@/views/community/Index.vue'),
  },
  {
    path: '/community/:category',
    name: 'community-category',
    component: () => import('@/views/community/Index.vue'),
  },
  {
    path: '/post/:slug',
    name: 'post-show',
    component: () => import('@/views/community/PostShow.vue'),
  },
  {
    path: '/post/create',
    name: 'post-create',
    component: () => import('@/views/community/PostCreate.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/post/:slug/edit',
    name: 'post-edit',
    component: () => import('@/views/community/PostEdit.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/jobs',
    name: 'jobs',
    component: () => import('@/views/jobs/Index.vue'),
  },
  {
    path: '/jobs/:slug',
    name: 'job-show',
    component: () => import('@/views/jobs/Show.vue'),
  },
  {
    path: '/jobs/create',
    name: 'job-create',
    component: () => import('@/views/jobs/Create.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/events',
    name: 'events',
    component: () => import('@/views/events/Index.vue'),
  },
  {
    path: '/events/:slug',
    name: 'event-show',
    component: () => import('@/views/events/Show.vue'),
  },
  {
    path: '/events/create',
    name: 'event-create',
    component: () => import('@/views/events/Create.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/resources',
    name: 'resources',
    component: () => import('@/views/resources/Index.vue'),
  },
  {
    path: '/resources/:slug',
    name: 'resource-show',
    component: () => import('@/views/resources/Show.vue'),
  },
  {
    path: '/donations',
    name: 'donations',
    component: () => import('@/views/Donations.vue'),
  },
  {
    path: '/mentors',
    name: 'mentors',
    component: () => import('@/views/mentors/Index.vue'),
  },
  {
    path: '/profile/:username',
    name: 'profile',
    component: () => import('@/views/profile/Show.vue'),
  },
  {
    path: '/settings',
    name: 'settings',
    component: () => import('@/views/settings/Index.vue'),
    meta: { requiresAuth: true },
    children: [
      {
        path: '',
        name: 'settings-profile',
        component: () => import('@/views/settings/Profile.vue'),
      },
      {
        path: 'account',
        name: 'settings-account',
        component: () => import('@/views/settings/Account.vue'),
      },
      {
        path: 'notifications',
        name: 'settings-notifications',
        component: () => import('@/views/settings/Notifications.vue'),
      },
      {
        path: 'privacy',
        name: 'settings-privacy',
        component: () => import('@/views/settings/Privacy.vue'),
      },
    ],
  },
  {
    path: '/chat',
    name: 'chat',
    component: () => import('@/views/Chat.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/notifications',
    name: 'notifications',
    component: () => import('@/views/Notifications.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/admin',
    name: 'admin',
    component: () => import('@/views/admin/Index.vue'),
    meta: { requiresAuth: true, requiresAdmin: true },
    children: [
      {
        path: '',
        name: 'admin-dashboard',
        component: () => import('@/views/admin/Dashboard.vue'),
      },
      {
        path: 'users',
        name: 'admin-users',
        component: () => import('@/views/admin/Users.vue'),
      },
      {
        path: 'posts',
        name: 'admin-posts',
        component: () => import('@/views/admin/Posts.vue'),
      },
      {
        path: 'jobs',
        name: 'admin-jobs',
        component: () => import('@/views/admin/Jobs.vue'),
      },
      {
        path: 'events',
        name: 'admin-events',
        component: () => import('@/views/admin/Events.vue'),
      },
      {
        path: 'reports',
        name: 'admin-reports',
        component: () => import('@/views/admin/Reports.vue'),
      },
      {
        path: 'categories',
        name: 'admin-categories',
        component: () => import('@/views/admin/Categories.vue'),
      },
      {
        path: 'badges',
        name: 'admin-badges',
        component: () => import('@/views/admin/Badges.vue'),
      },
      {
        path: 'donations',
        name: 'admin-donations',
        component: () => import('@/views/admin/Donations.vue'),
      },
      {
        path: 'settings',
        name: 'admin-settings',
        component: () => import('@/views/admin/Settings.vue'),
      },
    ],
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'not-found',
    component: () => import('@/views/NotFound.vue'),
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition
    } else {
      return { top: 0 }
    }
  },
})

// Navigation guards
router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore()

  // If user has token but no user data, fetch the user
  if (authStore.token && !authStore.user) {
    try {
      await authStore.fetchUser()
    } catch (error) {
      console.error('Failed to fetch user:', error)
    }
  }

  // Check if route requires authentication
  if (to.meta.requiresAuth) {
    if (!authStore.isAuthenticated) {
      return next({
        name: 'login',
        query: { redirect: to.fullPath },
      })
    }

    // Check if route requires admin role
    if (to.meta.requiresAdmin && !authStore.isAdmin) {
      console.log('Access denied: User is not admin', authStore.user)
      return next({ name: 'home' })
    }
  }

  // Redirect authenticated users away from guest routes
  if (to.meta.guest && authStore.isAuthenticated) {
    // Redirect admin to admin dashboard, others to regular dashboard
    if (authStore.isAdmin) {
      return next({ name: 'admin' })
    }
    return next({ name: 'dashboard' })
  }

  next()
})

export default router