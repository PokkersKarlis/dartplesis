<template>
  <q-page class="useful q-pa-md">
    <div class="container q-mx-auto">

      <!-- Header -->
      <div class="head q-mb-md">
        <div class="text-overline text-grey-6">{{ t('nav.useful') }}</div>
        <div class="text-h4 text-weight-bold">{{ t('nav.useful') }}</div>
        <div class="text-body2 text-grey-6 q-mt-xs">{{ t('useful.hub_sub') }}</div>

        <!-- Sticky sub-navigation -->
        <div class="sticky-tabs q-mt-md">
          <q-tabs
            v-model="active"
            dense
            align="left"
            active-color="primary"
            indicator-color="primary"
            class="tabs"
            @update:model-value="goTo"
          >
            <q-tab name="laukums"   :label="t('useful.subnav.setup')"    icon="stadium"         />
            <q-tab name="trenini"   :label="t('useful.subnav.training')" icon="fitness_center"  />
            <q-tab name="checkout"  :label="t('useful.subnav.checkout')" icon="check_circle"    />
            <q-tab name="noteikumi" :label="t('useful.subnav.rules')"    icon="gavel"           />
          </q-tabs>
        </div>
      </div>

      <div class="grid">

        <!-- ── Board setup ─────────────────────────────────────────────────── -->
        <q-card id="laukums" class="card card--wide" flat bordered>
          <q-card-section>
            <DartsSetup />
          </q-card-section>
        </q-card>

        <!-- ── Training drills ────────────────────────────────────────────── -->
        <q-card id="trenini" class="card card--wide" flat bordered>
          <q-card-section class="q-pa-md">
            <div class="row items-start q-col-gutter-md">
              <div class="col">
                <div class="section-title">{{ t('useful.training.title') }}</div>
                <div class="section-sub q-mt-xs">{{ t('useful.training.subtitle') }}</div>
              </div>
            </div>

            <div class="drills-grid q-mt-md">
              <q-card v-for="d in drills" :key="d.id" class="tile" flat bordered>
                <q-card-section class="q-pa-md">
                  <div class="row items-start justify-between q-col-gutter-sm">
                    <div class="col">
                      <div class="text-subtitle1 text-weight-bold">{{ d.title }}</div>
                      <div class="text-caption text-grey-6 q-mt-xs">{{ d.description }}</div>
                    </div>
                    <div class="col-auto">
                      <q-badge :color="d.badgeColor" rounded class="badge">{{ d.difficulty }}</q-badge>
                    </div>
                  </div>

                  <q-separator dark class="q-my-md" />

                  <q-banner class="pro" rounded dense>
                    <template #avatar>
                      <q-icon name="tips_and_updates" color="accent" />
                    </template>
                    <div><b>{{ t('useful.training.tip_label') }}:</b> {{ d.proTip }}</div>
                  </q-banner>
                </q-card-section>
              </q-card>
            </div>
          </q-card-section>
        </q-card>

        <!-- ── Checkout table ─────────────────────────────────────────────── -->
        <q-card id="checkout" class="card card--wide" flat bordered>
          <q-card-section class="q-pa-md">
            <div class="row items-start justify-between q-col-gutter-md">
              <div class="col">
                <div class="section-title">{{ t('useful.checkout.title') }}</div>
                <div class="section-sub q-mt-xs">{{ t('useful.checkout.subtitle') }}</div>
              </div>
              <div class="col-12 col-md-auto">
                <q-input
                  v-model="checkoutQuery"
                  dense
                  standout="bg-grey-10 text-white"
                  color="primary"
                  :placeholder="t('useful.checkout.search_ph')"
                  clearable
                  class="search"
                >
                  <template #prepend>
                    <q-icon name="search" />
                  </template>
                </q-input>
              </div>
            </div>

            <q-separator dark class="q-mt-md" />

            <q-table
              flat
              bordered
              dark
              class="table q-mt-md"
              :rows="filteredCheckouts"
              :columns="checkoutColumns"
              row-key="score"
              :pagination="{ rowsPerPage: 20 }"
              :rows-per-page-options="[10, 20, 50, 0]"
            >
              <template #body-cell-score="props">
                <q-td :props="props" class="mono score">{{ props.value }}</q-td>
              </template>
              <template #body-cell-primary="props">
                <q-td :props="props" class="mono">{{ props.value }}</q-td>
              </template>
              <template #body-cell-alt="props">
                <q-td :props="props" class="mono">{{ props.value }}</q-td>
              </template>
            </q-table>
          </q-card-section>
        </q-card>

        <!-- ── Game rules ──────────────────────────────────────────────────── -->
        <q-card id="noteikumi" class="card card--wide" flat bordered>
          <q-card-section class="q-pa-md">
            <div class="row items-start q-col-gutter-md">
              <div class="col">
                <div class="section-title">{{ t('useful.rules.title') }}</div>
                <div class="section-sub q-mt-xs">{{ t('useful.rules.subtitle') }}</div>
              </div>
            </div>

            <div class="q-mt-md">
              <q-expansion-item
                v-for="r in rulesData"
                :key="r.id"
                class="rule"
                expand-separator
                :label="r.title"
                header-class="rule-head"
              >
                <q-card flat class="rule-body">
                  <q-card-section class="q-pa-md">
                    <div class="row q-col-gutter-lg">
                      <div class="col-12 col-md-6">
                        <div class="rule-k">{{ t('useful.rules.scoring') }}</div>
                        <div class="rule-t q-mt-xs">{{ r.scoring }}</div>
                      </div>
                      <div class="col-12 col-md-6">
                        <div class="rule-k">{{ t('useful.rules.win') }}</div>
                        <div class="rule-t q-mt-xs">{{ r.win }}</div>
                      </div>
                    </div>
                    <div v-if="r.tips && r.tips.length" class="q-mt-md">
                      <div class="rule-k">{{ t('useful.rules.tips') }}</div>
                      <ul class="rule-tips q-mt-xs q-mb-none">
                        <li v-for="(tip, i) in r.tips" :key="i" class="rule-t">{{ tip }}</li>
                      </ul>
                    </div>
                  </q-card-section>
                </q-card>
              </q-expansion-item>
            </div>
          </q-card-section>
        </q-card>

      </div>
    </div>
  </q-page>
</template>

<script setup>
import { computed, nextTick, onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import DartsSetup from 'src/components/DartsSetup.vue'

const { t, tm } = useI18n()
const route  = useRoute()
const router = useRouter()

const active        = ref('laukums')
const checkoutQuery = ref('')

// ── Drills & rules from i18n ───────────────────────────────────────────────
const drills    = computed(() => tm('useful.training.drills'))
const rulesData = computed(() => tm('useful.rules.data'))

// ── Checkout columns (reactive to locale changes) ──────────────────────────
const checkoutColumns = computed(() => [
  { name: 'score',   label: t('useful.checkout.col_score'),   field: 'score',   align: 'left', sortable: true },
  { name: 'primary', label: t('useful.checkout.col_primary'), field: 'primary', align: 'left' },
  { name: 'alt',     label: t('useful.checkout.col_alt'),     field: 'alt',     align: 'left' },
])

// ── Checkout data (pure computation, locale-independent) ───────────────────
const checkoutRows = (() => {
  const rows    = []
  const special = {
    170: { primary: 'T20, T20, Bull', alt: '-' },
    167: { primary: 'T20, T19, Bull', alt: '-' },
    164: { primary: 'T20, T18, Bull', alt: '-' },
    161: { primary: 'T20, T17, Bull', alt: '-' },
  }
  for (let s = 170; s >= 41; s--) {
    if (special[s]) { rows.push({ score: s, ...special[s] }); continue }
    if (s <= 60) {
      if (s % 2 === 0) rows.push({ score: s, primary: `D${s / 2}`, alt: '-' })
      else             rows.push({ score: s, primary: `S1, D${(s - 1) / 2}`, alt: '-' })
      continue
    }
    const remain = s - 60
    if (remain > 40)      rows.push({ score: s, primary: 'T20, T20, D20', alt: '-' })
    else if (remain === 40) rows.push({ score: s, primary: 'T20, D20, -', alt: '-' })
    else if (remain > 1) {
      const d     = remain % 2 === 0 ? remain / 2 : (remain - 1) / 2
      const setup = remain % 2 === 0 ? `S${remain - 2}` : 'S1'
      rows.push({ score: s, primary: `T20, ${setup}, D${d}`, alt: '-' })
    } else rows.push({ score: s, primary: 'T20, S1, D20', alt: '-' })
  }
  return rows
})()

const filteredCheckouts = computed(() => {
  const q = checkoutQuery.value.trim().toLowerCase()
  if (!q) return checkoutRows
  return checkoutRows.filter(r =>
    String(r.score).includes(q) ||
    String(r.primary).toLowerCase().includes(q) ||
    String(r.alt).toLowerCase().includes(q),
  )
})

// ── Navigation ─────────────────────────────────────────────────────────────
function scrollToHash(hash) {
  const id = (hash || '').replace('#', '')
  if (!id) return
  const el = document.getElementById(id)
  if (!el) return
  el.scrollIntoView({ behavior: 'smooth', block: 'start' })
}

async function goTo(id) {
  await router.replace({ path: '/noderigi', hash: `#${id}` })
  await nextTick()
  scrollToHash(`#${id}`)
}

const currentHashId = computed(() => (route.hash || '').replace('#', ''))

onMounted(async () => {
  if (currentHashId.value) {
    active.value = currentHashId.value
    await nextTick()
    scrollToHash(route.hash)
  }
})
</script>

<style scoped lang="scss">
.useful {
  min-height: 100%;
  background:
    radial-gradient(1000px 700px at 18% 15%, rgba(111, 183, 170, 0.12), transparent 60%),
    radial-gradient(900px 600px at 80% 70%, rgba(231, 76, 60, 0.1), transparent 55%),
    #0a0a0a;
}

.container { max-width: 1200px; }

.head { position: relative; }

.sticky-tabs {
  position: sticky;
  top: 0;
  z-index: 10;
  padding: 10px;
  background: linear-gradient(180deg, rgba(10, 10, 10, 0.95) 0%, rgba(10, 10, 10, 0.65) 100%);
  backdrop-filter: blur(14px);
  border: 1px solid rgba(255, 255, 255, 0.06);
  border-radius: 18px;
}

.tabs :deep(.q-tab) { min-height: 44px; }

.grid {
  display: grid;
  grid-template-columns: repeat(12, 1fr);
  gap: 12px;
}

.card {
  grid-column: span 12;
  border-radius: 18px;
  border-color: rgba(255, 255, 255, 0.08);
  background: rgba(17, 17, 17, 0.62);
  backdrop-filter: blur(10px);
  scroll-margin-top: 120px;
  box-shadow: 0 12px 32px rgba(0, 0, 0, 0.35);
}

.card--wide { grid-column: span 12; }

.section-title {
  font-weight: 900;
  letter-spacing: -0.02em;
  font-size: 18px;
}

.section-sub {
  color: rgba(255, 255, 255, 0.65);
  line-height: 1.6;
}

.drills-grid {
  display: grid;
  grid-template-columns: repeat(12, 1fr);
  gap: 12px;
}

.tile {
  grid-column: span 6;
  border-radius: 18px;
  border-color: rgba(255, 255, 255, 0.08);
  background: rgba(10, 10, 10, 0.35);
  transition: transform 0.2s ease, box-shadow 0.2s ease;

  &:hover {
    transform: translateY(-2px);
    box-shadow: 0 18px 36px rgba(0, 0, 0, 0.45);
  }
}

.badge {
  font-weight: 800;
  letter-spacing: 0.06em;
}

.pro {
  background: rgba(255, 255, 255, 0.03);
  border: 1px solid rgba(255, 255, 255, 0.08);
  color: rgba(245, 245, 245, 0.92);
}

.search { min-width: min(520px, 100%); }

.table {
  border-radius: 18px;
  overflow: hidden;
  border-color: rgba(255, 255, 255, 0.08);
  background: rgba(10, 10, 10, 0.35);
}

.mono {
  font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace;
}

.score { font-weight: 900; }

.rule {
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 18px;
  overflow: hidden;
  background: rgba(10, 10, 10, 0.35);
  margin-bottom: 10px;
}

.rule-head { background: rgba(17, 17, 17, 0.55); }

.rule-body { background: transparent; }

.rule-k {
  font-size: 11px;
  letter-spacing: 0.16em;
  text-transform: uppercase;
  color: rgba(255, 255, 255, 0.55);
}

.rule-t {
  color: rgba(255, 255, 255, 0.75);
  line-height: 1.6;
}

.rule-tips {
  padding-left: 1.2rem;
  list-style: disc;

  li { margin-bottom: 4px; }
}

@media (max-width: 900px) {
  .tile { grid-column: span 12; }
}
</style>
