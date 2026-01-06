import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const routes = [
  {
    path: '/',
    name: 'Website',
    component: () => import('../views/Website.vue')
  },
  {
    path: '/login',
    name: 'Login',
    component: () => import('../views/Login.vue'),
    meta: { requiresGuest: true }
  },
  {
    path: '/website',
    name: 'WebsiteAlt',
    component: () => import('../views/Website.vue')
  },
  {
    path: '/verifikator',
    redirect: '/dashboard/verifikator'
  },
  {
    path: '/admin',
    redirect: '/dashboard/admin'
  },
  {
    path: '/pengusulan',
    redirect: () => '/dashboard/pengusulan'
  },
  {
    path: '/pengusulan/:id(\\d+)',
    redirect: to => `/dashboard/pengusulan/${to.params.id}`
  },
  {
    path: '/pengusulan/:id(\\d+)/edit',
    redirect: to => `/dashboard/pengusulan/${to.params.id}/edit`
  },
  {
    path: '/dashboard',
    component: () => import('../layouts/MainLayout.vue'),
    meta: { requiresAuth: true },
    children: [
      {
        path: '',
        name: 'Dashboard',
        component: () => import('../views/Dashboard.vue')
      },
      {
        path: 'pengusulan',
        name: 'PengusulanList',
        component: () => import('../views/PengusulanList.vue')
      },
      {
        path: 'pengusulan/create',
        name: 'PengusulanCreate',
        component: () => import('../views/PengusulanCreate.vue')
      },
      {
        path: 'pengusulan/:id',
        name: 'PengusulanDetail',
        component: () => import('../views/PengusulanDetail.vue')
      },
      {
        path: 'pengusulan/:id/edit',
        name: 'PengusulanEdit',
        component: () => import('../views/PengusulanEdit.vue')
      },
      {
        path: 'verifikator',
        name: 'VerifikatorDashboard',
        component: () => import('../views/VerifikatorDashboard.vue'),
        meta: { requiresVerifikator: true }
      },
      {
        path: 'verifikator/pengusulan/:id',
        name: 'VerifikatorReview',
        component: () => import('../views/VerifikatorReview.vue'),
        meta: { requiresVerifikator: true }
      },
      {
        path: 'admin',
        name: 'AdminDashboard',
        component: () => import('../views/admin/AdminDashboard.vue'),
        meta: { requiresAdmin: true }
      },
      {
        path: 'admin/dinas',
        name: 'AdminDinas',
        component: () => import('../views/admin/AdminDinas.vue'),
        meta: { requiresAdmin: true }
      },
      {
        path: 'admin/users',
        name: 'AdminUsers',
        component: () => import('../views/admin/AdminUsers.vue'),
        meta: { requiresAdmin: true }
      },
      {
        path: 'admin/website',
        name: 'AdminWebsite',
        component: () => import('../views/admin/AdminWebsite.vue'),
        meta: { requiresAdmin: true }
      },
      {
        path: 'admin/berita',
        name: 'AdminBerita',
        component: () => import('../views/admin/AdminBerita.vue'),
        meta: { requiresAdmin: true }
      },
      {
        path: 'admin/infokami',
        name: 'AdminInfokami',
        component: () => import('../views/admin/AdminInfokami.vue'),
        meta: { requiresAdmin: true }
      },
      {
        path: 'admin/banners',
        name: 'AdminBanners',
        component: () => import('../views/admin/AdminBanners.vue'),
        meta: { requiresAdmin: true }
      }
    ]
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore()
  
  // Handle redirects first (before auth checks)
  // This ensures redirects work even if user is not authenticated yet
  if (to.path.startsWith('/pengusulan') && !to.path.startsWith('/dashboard/pengusulan')) {
    // Redirect old pengusulan paths to new dashboard paths
    if (to.path === '/pengusulan') {
      return next({ path: '/dashboard/pengusulan', replace: true })
    } else if (to.path.match(/^\/pengusulan\/(\d+)$/)) {
      const id = to.path.match(/^\/pengusulan\/(\d+)$/)[1]
      return next({ path: `/dashboard/pengusulan/${id}`, replace: true })
    } else if (to.path.match(/^\/pengusulan\/(\d+)\/edit$/)) {
      const id = to.path.match(/^\/pengusulan\/(\d+)\/edit$/)[1]
      return next({ path: `/dashboard/pengusulan/${id}/edit`, replace: true })
    }
  }
  
  // Handle other redirects
  if (to.path === '/verifikator' || to.path.startsWith('/verifikator/')) {
    const newPath = to.path.replace('/verifikator', '/dashboard/verifikator')
    return next({ path: newPath, replace: true })
  }
  
  if (to.path === '/admin' || to.path.startsWith('/admin/')) {
    const newPath = to.path.replace('/admin', '/dashboard/admin')
    return next({ path: newPath, replace: true })
  }
  
  // Fetch user if authenticated but user data not loaded
  if (authStore.isAuthenticated && !authStore.user) {
    try {
      await authStore.fetchUser()
    } catch (error) {
      console.error('Error fetching user:', error)
    }
  }
  
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next({ name: 'Login' })
  } else if (to.meta.requiresGuest && authStore.isAuthenticated) {
    next({ name: 'Dashboard' })
  } else if (to.meta.requiresAdmin && (!authStore.isAuthenticated || authStore.user?.role !== 'admin')) {
    next({ name: 'Dashboard' })
  } else if (to.meta.requiresVerifikator && (!authStore.isAuthenticated || !['verifikator', 'bagian_hukum', 'admin'].includes(authStore.user?.role))) {
    next({ name: 'Dashboard' })
  } else {
    next()
  }
})

export default router

