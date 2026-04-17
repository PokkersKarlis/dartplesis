import { defineRouter } from '#q-app/wrappers'
import {
  createRouter,
  createMemoryHistory,
  createWebHistory,
  createWebHashHistory,
} from 'vue-router'
import { useAuth } from 'src/composables/useAuth'
import routes from './routes'

export default defineRouter(() => {
  const createHistory = process.env.SERVER
    ? createMemoryHistory
    : process.env.VUE_ROUTER_MODE === 'history'
      ? createWebHistory
      : createWebHashHistory

  const Router = createRouter({
    scrollBehavior: () => ({ left: 0, top: 0 }),
    routes,
    history: createHistory(process.env.VUE_ROUTER_BASE),
  })

  Router.beforeEach(async (to) => {
    const { state, fetchMe, isAdmin } = useAuth()

    if (!state.checked) {
      await fetchMe()
    }

    const admin = isAdmin()

    // Protected admin routes → redirect to login
    if (to.meta.requiresAdmin && !admin) {
      return { path: '/admin/login', query: { redirect: to.fullPath } }
    }

    // Login page → skip if already authenticated as admin
    if (to.meta.guestOnly && admin) {
      return { path: '/admin/dashboard' }
    }
  })

  return Router
})
