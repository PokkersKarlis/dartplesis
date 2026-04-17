<template>
  <q-layout view="lHh LpR lff" class="admin-layout">

    <!-- Left sidebar drawer -->
    <q-drawer
      v-model="drawer"
      :width="220"
      :breakpoint="900"
      show-if-above
      class="admin-drawer"
    >
      <div class="drawer-inner column full-height">
        <!-- Brand -->
        <div class="drawer-brand row items-center no-wrap q-px-md q-py-lg">
          <q-icon name="sports_bar" size="28px" color="primary" class="q-mr-sm" />
          <div>
            <div class="brand-name">Dārtplēsis</div>
            <div class="brand-sub">{{ t('admin_nav.panel') }}</div>
          </div>
        </div>

        <q-separator dark style="opacity: 0.12" />

        <!-- Navigation -->
        <q-list class="drawer-nav q-mt-sm">
          <q-item
            v-for="item in navItems"
            :key="item.to"
            :to="item.to"
            exact
            active-class="nav-item--active"
            class="nav-item"
            clickable
            v-ripple
          >
            <q-item-section avatar>
              <q-icon :name="item.icon" size="20px" />
            </q-item-section>
            <q-item-section>{{ item.label }}</q-item-section>
            <q-item-section v-if="item.badge && pendingCount > 0" side>
              <q-badge color="negative" rounded>{{ pendingCount }}</q-badge>
            </q-item-section>
          </q-item>
        </q-list>

        <q-space />

        <!-- User block -->
        <div class="drawer-footer q-pa-md">
          <q-separator dark style="opacity: 0.12; margin-bottom: 12px" />
          <div class="row items-center no-wrap q-gutter-xs">
            <q-avatar size="32px" color="primary" text-color="white" class="q-mr-xs">
              {{ userInitial }}
            </q-avatar>
            <div class="col min-width-0">
              <div class="footer-email ellipsis">{{ state.user?.email }}</div>
              <div class="footer-role">{{ t('admin_nav.administrator') }}</div>
            </div>
            <q-btn flat round icon="logout" size="sm" color="grey-5" :title="t('admin_nav.logout')" @click="onLogout" />
          </div>
        </div>
      </div>
    </q-drawer>

    <!-- Top header -->
    <q-header class="admin-header" elevated>
      <q-toolbar>
        <q-btn
          flat
          round
          :icon="drawer ? 'menu_open' : 'menu'"
          color="grey-4"
          class="lt-md"
          @click="drawer = !drawer"
        />
        <q-toolbar-title class="admin-header-title">
          {{ currentPageTitle }}
        </q-toolbar-title>
        <q-chip
          square
          dense
          color="primary"
          text-color="white"
          icon="shield"
          class="gt-sm"
        >
          {{ state.user?.email }}
        </q-chip>
        <q-btn
          flat
          dense
          no-caps
          icon="logout"
          :label="t('admin_nav.logout')"
          color="grey-5"
          class="q-ml-sm gt-sm"
          @click="onLogout"
        />
      </q-toolbar>
    </q-header>

    <!-- Page content -->
    <q-page-container>
      <router-view />
    </q-page-container>

  </q-layout>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useAuth } from 'src/composables/useAuth'

const { t } = useI18n()
const { state, logout } = useAuth()
const router = useRouter()
const route  = useRoute()
const drawer = ref(false)
const pendingCount = ref(0)

onMounted(async () => {
  try {
    const res  = await fetch('/api/admin/club-requests', { credentials: 'include', headers: { Accept: 'application/json' } })
    const data = await res.json()
    pendingCount.value = data.pendingCount ?? 0
  } catch { console.log('club request failed')}
})

const navItems = computed(() => [
  { to: '/admin/dashboard',  label: t('admin_nav.dashboard'),  icon: 'dashboard'          },
  { to: '/admin/players',    label: t('admin_nav.players'),    icon: 'people'             },
  { to: '/admin/supporters', label: t('admin_nav.supporters'), icon: 'volunteer_activism' },
  { to: '/admin/requests',   label: t('admin_nav.requests'),   icon: 'how_to_reg', badge: true },
  { to: '/admin/blog',       label: t('admin_nav.blog'),       icon: 'article'            },
  { to: '/admin/settings',   label: t('admin_nav.settings'),   icon: 'manage_accounts'    },
])

const currentPageTitle = computed(() => {
  const sorted = [...navItems.value].sort((a, b) => b.to.length - a.to.length)
  const match  = sorted.find(n => route.path.startsWith(n.to))
  return match?.label ?? 'Administration'
})

const userInitial = computed(() => {
  const email = state.user?.email ?? ''
  return email.charAt(0).toUpperCase() || 'A'
})

async function onLogout() {
  await logout()
  await router.replace('/admin/login')
}
</script>

<style scoped lang="scss">
.admin-layout {
  background: #0d0d0d;
}

.admin-drawer {
  background: #0d0d0d !important;
  border-right: 1px solid rgba(255, 255, 255, 0.07) !important;
}

.drawer-inner { background: #0d0d0d; }

.brand-name {
  font-size: 0.95rem;
  font-weight: 900;
  letter-spacing: -0.02em;
  color: #f5f5f5;
  line-height: 1.2;
}
.brand-sub {
  font-size: 0.65rem;
  letter-spacing: 0.14em;
  text-transform: uppercase;
  color: rgba(255, 255, 255, 0.38);
  margin-top: 1px;
}

.drawer-nav {
  padding: 0 8px;
}

.nav-item {
  border-radius: 10px;
  margin-bottom: 2px;
  color: rgba(255, 255, 255, 0.52);
  min-height: 42px;
  font-size: 0.875rem;
  font-weight: 500;
  transition: color 0.15s, background 0.15s;

  &:hover {
    color: #f5f5f5;
    background: rgba(255, 255, 255, 0.06);
  }
}

:deep(.nav-item--active) {
  color: #4ecdc4 !important;
  background: rgba(78, 205, 196, 0.1) !important;

  .q-icon { color: #4ecdc4 !important; }
}

.drawer-footer {
  .footer-email {
    font-size: 0.78rem;
    color: rgba(255, 255, 255, 0.75);
    font-weight: 500;
  }
  .footer-role {
    font-size: 0.68rem;
    color: rgba(255, 255, 255, 0.35);
    letter-spacing: 0.08em;
    text-transform: uppercase;
  }
}

.admin-header {
  background: rgba(13, 13, 13, 0.96) !important;
  border-bottom: 1px solid rgba(255, 255, 255, 0.07);
  backdrop-filter: blur(10px);
}

.admin-header-title {
  font-size: 1rem;
  font-weight: 700;
  color: #f5f5f5;
  letter-spacing: -0.01em;
}
</style>
