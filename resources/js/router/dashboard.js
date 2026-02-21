import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const routes = [
  {
    path: '/',
    name: 'dashboard',
    component: () => import('@/views/Dashboard.vue'),
    meta: { requiresAuth: true },
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
      // Redirect to login page on main site
      window.location.href = '/login'
      return
    }

    // Check if route requires admin role
    if (to.meta.requiresAdmin && !authStore.isAdmin) {
      console.log('Access denied: User is not admin', authStore.user)
      window.location.href = '/'
      return
    }
  }

  next()
})

export default router
