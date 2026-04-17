<template>
  <q-page class="admin-page q-pa-lg">

    <div class="page-header q-mb-xl">
      <div class="page-kicker">{{ t('admin_dashboard.kicker') }}</div>
      <div class="page-title">{{ t('admin_dashboard.title') }}</div>
      <div class="page-sub">{{ t('admin_dashboard.welcome') }}, {{ state.user?.email }}</div>
    </div>

    <!-- Stat cards -->
    <div class="stats-grid q-mb-xl">
      <div class="stat-card" v-for="s in stats" :key="s.key">
        <div class="stat-icon-wrap" :style="{ background: s.bg }">
          <q-icon :name="s.icon" size="22px" :color="s.color" />
        </div>
        <div class="stat-body">
          <div class="stat-value">
            <q-spinner v-if="s.loading" size="1rem" color="grey-6" />
            <template v-else>{{ s.value }}</template>
          </div>
          <div class="stat-label">{{ t(s.labelKey) }}</div>
        </div>
      </div>
    </div>

    <!-- Quick links -->
    <div class="section-title q-mb-sm">{{ t('admin_dashboard.quick_actions') }}</div>
    <div class="actions-grid">
      <q-card
        v-for="a in actions"
        :key="a.to"
        flat
        class="action-card cursor-pointer"
        @click="router.push(a.to)"
      >
        <q-card-section class="row items-center q-gutter-md no-wrap">
          <q-icon :name="a.icon" size="28px" color="primary" />
          <div>
            <div class="action-title">{{ t(a.titleKey) }}</div>
            <div class="action-sub">{{ t(a.subKey) }}</div>
          </div>
          <q-space />
          <q-icon name="chevron_right" color="grey-7" />
        </q-card-section>
      </q-card>
    </div>

  </q-page>
</template>

<script setup>
import { onMounted, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useAuth } from 'src/composables/useAuth'

const { t } = useI18n()
const { state } = useAuth()
const router = useRouter()

const stats = reactive([
  { key: 'players', labelKey: 'admin_dashboard.stat_players', icon: 'people',           color: 'primary',  bg: 'rgba(78,205,196,0.1)',  value: '—', loading: true },
  { key: 'admins',  labelKey: 'admin_dashboard.stat_admins',  icon: 'shield',           color: 'warning',  bg: 'rgba(255,180,0,0.1)',   value: '—', loading: true },
  { key: 'users',   labelKey: 'admin_dashboard.stat_users',   icon: 'manage_accounts',  color: 'positive', bg: 'rgba(85,220,100,0.1)',  value: '—', loading: true },
])

const actions = [
  { to: '/admin/players',    icon: 'person_add',        titleKey: 'admin_dashboard.action_players',     subKey: 'admin_dashboard.action_players_sub'    },
  { to: '/admin/supporters', icon: 'volunteer_activism', titleKey: 'admin_dashboard.action_supporters',  subKey: 'admin_dashboard.action_supporters_sub' },
  { to: '/admin/settings',   icon: 'tune',               titleKey: 'admin_dashboard.action_settings',    subKey: 'admin_dashboard.action_settings_sub'   },
]

async function loadStats() {
  try {
    const [pRes, uRes] = await Promise.all([
      fetch('/api/admin/players', { credentials: 'include', headers: { Accept: 'application/json' } }),
      fetch('/api/admin/users',   { credentials: 'include', headers: { Accept: 'application/json' } }),
    ])

    if (pRes.ok) {
      const d = await pRes.json()
      stats[0].value = d.players?.length ?? 0
    }
    if (uRes.ok) {
      const d = await uRes.json()
      const users = d.users ?? []
      stats[1].value = users.filter(u => u.roles?.includes('ROLE_ADMINISTRATOR')).length
      stats[2].value = users.length
    }
  } catch { /* non-critical */ } finally {
    stats.forEach(s => { s.loading = false })
  }
}

onMounted(loadStats)
</script>

<style scoped lang="scss">
.admin-page {
  min-height: 100%;
  background: #111;
  color: #f5f5f5;
}

.page-kicker {
  font-size: 11px;
  letter-spacing: 0.2em;
  text-transform: uppercase;
  color: rgba(255, 255, 255, 0.4);
}
.page-title {
  font-size: clamp(1.6rem, 3vw, 2.2rem);
  font-weight: 900;
  letter-spacing: -0.03em;
  line-height: 1.1;
}
.page-sub {
  color: rgba(255, 255, 255, 0.45);
  font-size: 0.9rem;
  margin-top: 4px;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
  gap: 14px;
}

.stat-card {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 16px 20px;
  border-radius: 16px;
  border: 1px solid rgba(255, 255, 255, 0.07);
  background: rgba(255, 255, 255, 0.03);
}

.stat-icon-wrap {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.stat-value {
  font-size: 1.6rem;
  font-weight: 900;
  letter-spacing: -0.03em;
  line-height: 1;
}
.stat-label {
  font-size: 0.75rem;
  color: rgba(255, 255, 255, 0.45);
  margin-top: 3px;
}

.section-title {
  font-size: 0.7rem;
  letter-spacing: 0.18em;
  text-transform: uppercase;
  color: rgba(255, 255, 255, 0.4);
  font-weight: 600;
}

.actions-grid {
  display: flex;
  flex-direction: column;
  gap: 10px;
  max-width: 560px;
}

.action-card {
  border-radius: 14px;
  border: 1px solid rgba(255, 255, 255, 0.07);
  background: rgba(255, 255, 255, 0.03);
  transition: background 0.15s, border-color 0.15s;

  &:hover {
    background: rgba(78, 205, 196, 0.05);
    border-color: rgba(78, 205, 196, 0.2);
  }
}
.action-title {
  font-weight: 700;
  font-size: 0.9rem;
  color: #f5f5f5;
}
.action-sub {
  font-size: 0.78rem;
  color: rgba(255, 255, 255, 0.45);
  margin-top: 2px;
}
</style>
