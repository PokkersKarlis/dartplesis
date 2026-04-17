<template>
  <q-page class="noderigi q-pa-md">
    <div class="container q-mx-auto">
      <NoderigiSubNav />

      <div class="row items-end justify-between q-col-gutter-md q-mb-md">
        <div class="col-12 col-md">
          <div class="text-overline text-grey-6">{{ t('useful.rules.kicker') }}</div>
          <div class="text-h4 text-weight-bold">{{ t('useful.rules.title') }}</div>
          <div class="text-body2 text-grey-6 q-mt-xs">{{ t('useful.rules.subtitle') }}</div>
        </div>
        <div class="col-12 col-md-auto">
          <q-input
            v-model="searchQuery"
            dense
            standout="bg-grey-10 text-white"
            color="primary"
            :placeholder="t('useful.rules.search_ph')"
            clearable
            class="search"
          >
            <template #prepend><q-icon name="search" /></template>
          </q-input>
        </div>
      </div>

      <!-- Empty state -->
      <div v-if="filteredRules.length === 0" class="empty q-py-xl text-center">
        <q-icon name="search_off" size="48px" color="grey-7" />
        <div class="text-body2 text-grey-6 q-mt-sm">{{ t('useful.rules.empty') }}</div>
      </div>

      <div v-else class="rules-list">
        <q-expansion-item
          v-for="r in filteredRules"
          :key="r.id"
          class="rule"
          expand-separator
          :icon="r.icon"
          :label="r.title"
          :caption="r.tagline"
          header-class="rule-head"
        >
          <q-card flat class="rule-body">
            <q-card-section class="q-pa-md">
              <div class="row q-col-gutter-lg">
                <div class="col-12 col-md-6">
                  <div class="rule-k">
                    <q-icon name="calculate" size="14px" class="q-mr-xs" />
                    {{ t('useful.rules.scoring') }}
                  </div>
                  <div class="rule-t q-mt-xs">{{ r.scoring }}</div>
                </div>
                <div class="col-12 col-md-6">
                  <div class="rule-k">
                    <q-icon name="emoji_events" size="14px" class="q-mr-xs" />
                    {{ t('useful.rules.win') }}
                  </div>
                  <div class="rule-t q-mt-xs">{{ r.win }}</div>
                </div>
              </div>

              <div v-if="r.tips && r.tips.length" class="q-mt-md">
                <q-banner class="pro" rounded dense>
                  <template #avatar>
                    <q-icon name="tips_and_updates" color="accent" />
                  </template>
                  <div>
                    <b>{{ t('useful.rules.tips') }}:</b>
                    <ul class="tips-list q-mb-none">
                      <li v-for="tip in r.tips" :key="tip">{{ tip }}</li>
                    </ul>
                  </div>
                </q-banner>
              </div>
            </q-card-section>
          </q-card>
        </q-expansion-item>
      </div>
    </div>
  </q-page>
</template>

<script setup>
import { computed, ref } from 'vue'
import { useI18n } from 'vue-i18n'
import NoderigiSubNav from 'src/components/NoderigiSubNav.vue'

const { t, tm } = useI18n()
const searchQuery = ref('')

const rules = computed(() => tm('useful.rules.data'))

const filteredRules = computed(() => {
  const q = searchQuery.value.trim().toLowerCase()
  if (!q) return rules.value
  return rules.value.filter(
    (r) =>
      r.title.toLowerCase().includes(q) ||
      r.tagline.toLowerCase().includes(q) ||
      r.scoring.toLowerCase().includes(q) ||
      r.win.toLowerCase().includes(q),
  )
})
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

.rules-list {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.rule {
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 18px;
  overflow: hidden;
  background: rgba(17, 17, 17, 0.62);
  backdrop-filter: blur(10px);
  box-shadow: 0 12px 32px rgba(0, 0, 0, 0.35);
  transition: box-shadow 0.2s ease;

  &:hover { box-shadow: 0 16px 40px rgba(0, 0, 0, 0.45); }

  :deep(.q-expansion-item__container) { background: transparent; }
}

.rule-head {
  background: rgba(10, 10, 10, 0.25);
  min-height: 64px;

  :deep(.q-item__label)          { font-weight: 700; font-size: 1rem; color: #f5f5f5; }
  :deep(.q-item__label--caption) { color: rgba(255, 255, 255, 0.5); font-size: 0.78rem; margin-top: 2px; }
  :deep(.q-icon)                 { color: #4ecdc4; }
}

.rule-body { background: rgba(10, 10, 10, 0.2); }

.rule-k {
  display: flex;
  align-items: center;
  font-size: 11px;
  letter-spacing: 0.16em;
  text-transform: uppercase;
  color: rgba(255, 255, 255, 0.5);
}

.rule-t {
  color: rgba(255, 255, 255, 0.78);
  line-height: 1.65;
  font-size: 0.9rem;
}

.pro {
  background: rgba(255, 255, 255, 0.03);
  border: 1px solid rgba(255, 255, 255, 0.08);
  color: rgba(245, 245, 245, 0.92);
}

.tips-list {
  margin: 6px 0 0;
  padding-left: 18px;

  li { margin-bottom: 4px; line-height: 1.5; font-size: 0.88rem; }
}
</style>
