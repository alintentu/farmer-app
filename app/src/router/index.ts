import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import type { RouteRecordRaw } from 'vue-router'

const routes: RouteRecordRaw[] = [
  {
    path: '/',
    redirect: '/dashboard'
  },
  {
    path: '/admin/users',
    name: 'AdminUsers',
    component: () => import('@/views/admin/users/UserList.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/admin/users/invite',
    name: 'AdminUserInvite',
    component: () => import('@/views/admin/users/UserInvite.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/admin/users/:id',
    name: 'AdminUserEdit',
    component: () => import('@/views/admin/users/UserEdit.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/admin/content/pdfs',
    name: 'AdminContentPdfs',
    component: () => import('@/views/admin/content/PdfList.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/admin/content/pdfs/new',
    name: 'AdminContentPdfNew',
    component: () => import('@/views/admin/content/PdfUpload.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/admin/content/pdfs/:id',
    name: 'AdminContentPdfShow',
    component: () => import('@/views/admin/content/PdfDetail.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/login',
    name: 'Login',
    component: () => import('@/views/auth/Login.vue'),
    meta: { requiresGuest: true }
  },
  {
    path: '/register',
    name: 'Register',
    component: () => import('@/views/auth/Register.vue'),
    meta: { requiresGuest: true }
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: () => import('@/views/Dashboard.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/modules',
    name: 'Modules',
    component: () => import('@/views/Modules.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/profile',
    name: 'Profile',
    component: () => import('@/views/Profile.vue'),
    meta: { requiresAuth: true }
  },
  // Module routes
  {
    path: '/tasks',
    name: 'Tasks',
    component: () => import('@/views/tasks/Board.vue'),
    meta: { requiresAuth: true, feature: 'tasks' }
  },
  {
    path: '/crm',
    name: 'CRM',
    component: () => import('@/views/crm/Dashboard.vue'),
    meta: { requiresAuth: true, feature: 'crm' }
  },
  {
    path: '/invoicing',
    name: 'Invoicing',
    component: () => import('@/views/invoicing/Dashboard.vue'),
    meta: { requiresAuth: true, feature: 'invoicing' }
  },
  {
    path: '/marketing',
    name: 'Marketing',
    component: () => import('@/views/marketing/Dashboard.vue'),
    meta: { requiresAuth: true, feature: 'marketing' }
  },
  {
    path: '/automation',
    name: 'Automation',
    component: () => import('@/views/automation/Dashboard.vue'),
    meta: { requiresAuth: true, feature: 'automation' }
  },
  {
    path: '/analytics',
    name: 'Analytics',
    component: () => import('@/views/analytics/Dashboard.vue'),
    meta: { requiresAuth: true, feature: 'analytics' }
  },
  {
    path: '/docs',
    name: 'Docs',
    component: () => import('@/views/docs/Dashboard.vue'),
    meta: { requiresAuth: true, feature: 'docs' }
  },
  {
    path: '/helpdesk',
    name: 'Helpdesk',
    component: () => import('@/views/helpdesk/Dashboard.vue'),
    meta: { requiresAuth: true, feature: 'helpdesk' }
  },
  // 404 route
  {
    path: '/:pathMatch(.*)*',
    name: 'NotFound',
    component: () => import('@/views/NotFound.vue')
  }
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
  }
})

// Navigation guards
router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore()
  
  // Wait for auth initialization
  if (!authStore.isAuthenticated && authStore.token) {
    await authStore.fetchUser()
  }
  
  // Check authentication requirements
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next('/login')
    return
  }
  
  if (to.meta.requiresGuest && authStore.isAuthenticated) {
    next('/dashboard')
    return
  }
  
  // Check feature access
  if (to.meta.feature && authStore.user?.tenant) {
    const feature = to.meta.feature as string
    if (!authStore.user.tenant.canAccessFeature(feature)) {
      next('/modules')
      return
    }
  }
  
  next()
})

export default router
