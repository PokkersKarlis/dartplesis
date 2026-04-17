<template>
  <q-page class="noderigi q-pa-md">
    <div class="container q-mx-auto">
      <NoderigiSubNav />

      <!-- Header -->
      <div class="row items-end justify-between q-col-gutter-md q-mb-md">
        <div class="col-12 col-md">
          <div class="text-overline text-grey-6">{{ t('useful.training.kicker') }}</div>
          <div class="text-h4 text-weight-bold">{{ t('useful.training.title') }}</div>
          <div class="text-body2 text-grey-6 q-mt-xs">{{ t('useful.training.subtitle') }}</div>
        </div>
        <div class="col-12 col-md-auto">
          <q-input
            v-model="searchQuery"
            dense
            standout="bg-grey-10 text-white"
            color="primary"
            :placeholder="t('useful.training.search_ph')"
            clearable
            class="search"
          >
            <template #prepend><q-icon name="search" /></template>
          </q-input>
        </div>
      </div>

      <!-- Empty state -->
      <div v-if="filteredDrills.length === 0" class="empty q-py-xl text-center">
        <q-icon name="search_off" size="48px" color="grey-7" />
        <div class="text-body2 text-grey-6 q-mt-sm">{{ t('useful.training.empty') }}</div>
      </div>

      <!-- Drills grid -->
      <div v-else class="drills-grid">
        <q-expansion-item
          v-for="(d, i) in filteredDrills"
          :key="d.id"
          class="drill-item"
          expand-separator
          :default-opened="i === 0"
          header-class="drill-head-wrapper"
        >
          <template #header="{ expanded }">
            <div class="drill-header full-width">
              <div class="drill-meta">
                <div class="drill-name">{{ d.title }}</div>
                <div class="drill-goal">
                  <q-icon name="flag" size="13px" color="accent" class="q-mr-xs" />
                  <span>{{ d.goal }}</span>
                </div>
              </div>
              <div class="drill-aside">
                <q-badge :color="d.badgeColor" rounded class="diff-badge">{{ d.difficulty }}</q-badge>
                <q-icon
                  :name="expanded ? 'keyboard_arrow_up' : 'keyboard_arrow_down'"
                  color="grey-6"
                  size="20px"
                />
              </div>
            </div>
          </template>

          <div class="drill-body q-pa-md">
            <p class="drill-desc">{{ d.description }}</p>

            <q-separator dark class="q-my-md" />

            <q-banner class="pro" rounded dense>
              <template #avatar>
                <q-icon name="tips_and_updates" color="accent" />
              </template>
              <div><b>{{ t('useful.training.tip_label') }}:</b> {{ d.proTip }}</div>
            </q-banner>

            <!-- Timer -->
            <div class="timer-row q-mt-md">
              <div class="timer-display" :class="{ 'timer-display--running': timerState[d.id]?.running }">
                <q-icon name="timer" size="16px" class="q-mr-xs" />
                {{ formatTime(timerState[d.id]?.elapsed ?? 0) }}
              </div>
              <div class="timer-btns">
                <q-btn
                  :color="timerState[d.id]?.running ? 'negative' : 'primary'"
                  unelevated
                  :icon="timerState[d.id]?.running ? 'pause' : 'play_arrow'"
                  :label="timerState[d.id]?.running ? t('useful.training.timer_stop') : t('useful.training.timer_start')"
                  size="sm"
                  @click="toggleTimer(d.id)"
                />
                <q-btn
                  v-if="timerState[d.id]?.elapsed"
                  flat
                  round
                  icon="restart_alt"
                  size="sm"
                  color="grey-6"
                  :title="t('useful.training.timer_reset')"
                  @click="resetTimer(d.id)"
                />
              </div>
            </div>
          </div>
        </q-expansion-item>
      </div>
    </div>
  </q-page>
</template>

<script setup>
import { computed, onUnmounted, ref } from 'vue'
import { useI18n } from 'vue-i18n'
import NoderigiSubNav from 'src/components/NoderigiSubNav.vue'

const { t, tm } = useI18n()

const searchQuery = ref('')

const drills = computed(() => tm('useful.training.drills'))

const filteredDrills = computed(() => {
  const q = searchQuery.value.trim().toLowerCase()
  if (!q) return drills.value
  return drills.value.filter(
    (d) =>
      d.title.toLowerCase().includes(q) ||
      d.difficulty.toLowerCase().includes(q) ||
      d.goal.toLowerCase().includes(q) ||
      d.description.toLowerCase().includes(q),
  )
})

// ── Timer ────────────────────────────────────────────────────────────────────
const timerState = ref({})
let activeInterval = null
let activeId = null

function toggleTimer(id) {
  if (!timerState.value[id]) {
    timerState.value[id] = { elapsed: 0, running: false }
  }

  if (timerState.value[id].running) {
    // Pause
    clearInterval(activeInterval)
    activeInterval = null
    activeId = null
    timerState.value[id].running = false
  } else {
    // Stop any previously running timer
    if (activeId && timerState.value[activeId]) {
      clearInterval(activeInterval)
      timerState.value[activeId].running = false
    }
    // Start this one
    timerState.value[id].running = true
    activeId = id
    activeInterval = setInterval(() => {
      timerState.value[id].elapsed++
    }, 1000)
  }
}

function resetTimer(id) {
  if (activeId === id) {
    clearInterval(activeInterval)
    activeInterval = null
    activeId = null
  }
  timerState.value[id] = { elapsed: 0, running: false }
}

function formatTime(seconds) {
  const m = Math.floor(seconds / 60).toString().padStart(2, '0')
  const s = (seconds % 60).toString().padStart(2, '0')
  return `${m}:${s}`
}

onUnmounted(() => clearInterval(activeInterval))
</script>

<style scoped lang="scss">
.noderigi {
  min-height: 100%;
  background:
    radial-gradient(1000px 700px at 18% 15%, rgba(111, 183, 170, 0.12), transparent 60%),
    radial-gradient(900px 600px at 80% 70%, rgba(231, 76, 60, 0.1), transparent 55%),
    #0a0a0a;
}

.container { max-width: 1200px; }

.search { min-width: min(340px, 100%); }

.empty {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.drills-grid {
  display: grid;
  grid-template-columns: repeat(12, 1fr);
  gap: 12px;
}

:deep(.drill-head-wrapper.q-item) { padding: 0 16px; }

.drill-item {
  grid-column: span 6;
  border-radius: 18px;
  border: 1px solid rgba(255, 255, 255, 0.08);
  background: rgba(17, 17, 17, 0.62);
  backdrop-filter: blur(10px);
  box-shadow: 0 12px 32px rgba(0, 0, 0, 0.35);
  overflow: hidden;
  transition: box-shadow 0.2s ease, transform 0.2s ease;

  &:hover { transform: translateY(-2px); box-shadow: 0 18px 36px rgba(0, 0, 0, 0.45); }

  :deep(.q-expansion-item__container) { background: transparent; }
  :deep(.q-expansion-item__content)   { background: rgba(10, 10, 10, 0.3); }
}

.drill-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  padding: 14px 0;
  width: 100%;
}

.drill-meta { flex: 1; min-width: 0; }

.drill-name {
  font-weight: 700;
  font-size: 1rem;
  letter-spacing: -0.01em;
  color: #f5f5f5;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.drill-goal {
  display: flex;
  align-items: center;
  font-size: 0.75rem;
  color: rgba(255, 255, 255, 0.55);
  margin-top: 4px;
  line-height: 1.4;
}

.drill-aside {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-shrink: 0;
}

.diff-badge { font-weight: 800; letter-spacing: 0.06em; }

.drill-desc {
  color: rgba(255, 255, 255, 0.75);
  line-height: 1.65;
  margin: 0;
  font-size: 0.9rem;
}

.pro {
  background: rgba(255, 255, 255, 0.03);
  border: 1px solid rgba(255, 255, 255, 0.08);
  color: rgba(245, 245, 245, 0.92);
}

// Timer
.timer-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  flex-wrap: wrap;
}

.timer-display {
  display: flex;
  align-items: center;
  font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
  font-size: 1.1rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  color: rgba(255, 255, 255, 0.45);
  transition: color 0.3s;

  &--running { color: #4ecdc4; }
}

.timer-btns {
  display: flex;
  align-items: center;
  gap: 6px;
}

@media (max-width: 900px) {
  .drill-item { grid-column: span 12; }
}
</style>
