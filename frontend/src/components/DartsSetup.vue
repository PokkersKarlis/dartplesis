<template>
  <div class="ds">
    <div class="row items-start justify-between q-col-gutter-md">
      <div class="col-12 col-md">
        <div class="kicker">{{ t('useful.area_setup.instruction_title') }}</div>
        <div class="title">{{ t('useful.global.area_setup') }}</div>
        <div class="sub q-mt-xs">
          {{ t('useful.area_setup.title') }}
        </div>
      </div>
    </div>

    <!-- Comparison bento -->
    <div class="grid q-mt-md">
      <q-card class="card span-6" flat bordered>
        <q-card-section class="q-pa-md">
          <div class="row items-center justify-between">
            <div class="text-subtitle1 text-weight-bold">{{ t('useful.area_setup.steel_tip') }}</div>
          </div>

          <div class="specs q-mt-md">
            <div class="spec">
              <div class="spec-i">
                <q-icon name="height" color="primary" size="18px" />
              </div>
              <div class="spec-t">
                <div class="spec-k">{{ t('useful.area_setup.height') }}</div>
                <div class="spec-v">1.73 m</div>
              </div>
            </div>

            <div class="spec">
              <div class="spec-i">
                <q-icon name="straighten" color="primary" size="18px" />
              </div>
              <div class="spec-t">
                <div class="spec-k">{{ t('useful.area_setup.distance') }}</div>
                <div class="spec-v">2.37 m</div>
              </div>
            </div>

            <div class="spec">
              <div class="spec-i">
                <q-icon name="timeline" color="primary" size="18px" />
              </div>
              <div class="spec-t">
                <div class="spec-k">{{ t('useful.area_setup.diagonal') }}</div>
                <div class="spec-v">2.93 m</div>
              </div>
            </div>
          </div>
          <q-banner class="pro q-mt-md" rounded dense>
            <template #avatar>
              <q-icon name="tips_and_updates" color="accent" />
            </template>
            <div>
              <b>{{ t('useful.global.pro_tip') }}:</b> {{ t('useful.tips.distance') }}
            </div>
          </q-banner>
        </q-card-section>
      </q-card>

      <q-card class="card span-6" flat bordered>
        <q-card-section class="q-pa-md">
          <div class="row items-center justify-between">
            <div class="text-subtitle1 text-weight-bold">{{ t('useful.area_setup.soft_tip') }}</div>
          </div>

          <div class="specs q-mt-md">
            <div class="spec">
              <div class="spec-i">
                <q-icon name="height" color="primary" size="18px" />
              </div>
              <div class="spec-t">
                <div class="spec-k">{{ t('useful.area_setup.height') }}</div>
                <div class="spec-v">1.73 m</div>
              </div>
            </div>

            <div class="spec">
              <div class="spec-i">
                <q-icon name="straighten" color="primary" size="18px" />
              </div>
              <div class="spec-t">
                <div class="spec-k">{{ t('useful.area_setup.distance') }}</div>
                <div class="spec-v">2.44 m</div>
              </div>
            </div>

            <div class="spec">
              <div class="spec-i">
                <q-icon name="timeline" color="primary" size="18px" />
              </div>
              <div class="spec-t">
                <div class="spec-k">{{ t('useful.area_setup.diagonal') }}</div>
                <div class="spec-v">2.98 m</div>
              </div>
            </div>
          </div>

          <q-banner class="pro q-mt-md" rounded dense>
            <template #avatar>
              <q-icon name="auto_awesome" color="primary" />
            </template>
            <div>
              <b>{{ t('useful.global.pro_tip') }}:</b> {{ t('useful.tips.light') }}
            </div>
          </q-banner>
        </q-card-section>
      </q-card>

      <!-- ── Triangulation diagram ──────────────────────────────────────────── -->
      <q-card class="card span-12" flat bordered>
        <q-card-section class="q-pa-md">
          <div class="row items-center justify-between q-col-gutter-md">
            <div class="col">
              <div class="text-subtitle1 text-weight-bold">{{ t('useful.area_setup.tri_title') }}</div>
              <div class="text-caption text-grey-6 q-mt-xs">{{ t('useful.area_setup.tri_sub') }}</div>
            </div>
            <div class="col-auto row items-center q-gutter-sm">
              <q-btn-toggle
                v-model="mode"
                no-caps
                unelevated
                rounded
                toggle-color="primary"
                color="grey-10"
                text-color="grey-5"
                toggle-text-color="dark"
                size="sm"
                :options="[
                  { label: t('useful.area_setup.steel_tip'), value: 'steel' },
                  { label: t('useful.area_setup.soft_tip'),  value: 'soft'  },
                ]"
              />
              <q-chip square color="accent" text-color="black">
                {{ current.diagonal }}
              </q-chip>
            </div>
          </div>

          <div class="diagram q-mt-md">
            <svg viewBox="0 0 900 280" role="img" :aria-label="t('useful.area_setup.svg_aria')">
              <defs>
                <linearGradient id="g" x1="0" x2="1">
                  <stop offset="0" stop-color="#4ecdc4" stop-opacity="0.25" />
                  <stop offset="1" stop-color="#e74c3c" stop-opacity="0.18" />
                </linearGradient>
                <filter id="glow" x="-20%" y="-50%" width="140%" height="200%">
                  <feGaussianBlur stdDeviation="6" result="b" />
                  <feMerge>
                    <feMergeNode in="b" />
                    <feMergeNode in="SourceGraphic" />
                  </feMerge>
                </filter>
              </defs>

              <!-- ground line -->
              <line x1="70" y1="230" x2="830" y2="230" stroke="rgba(255,255,255,0.10)" stroke-width="2" />
              <text x="70" y="255" fill="rgba(255,255,255,0.55)" font-size="12">{{ t('useful.area_setup.svg_floor') }}</text>

              <!-- board line -->
              <line x1="160" y1="40" x2="160" y2="230" stroke="rgba(255,255,255,0.10)" stroke-width="2" />
              <text x="134" y="32" fill="rgba(255,255,255,0.55)" font-size="12">{{ t('useful.area_setup.svg_board') }}</text>

              <!-- bull center point -->
              <circle cx="160" cy="110" r="10" fill="#e74c3c" filter="url(#glow)" />
              <circle cx="160" cy="110" r="4" fill="#0a0a0a" />
              <text x="178" y="114" fill="rgba(255,255,255,0.85)" font-size="13">{{ t('useful.area_setup.svg_bull') }}</text>

              <!-- oche point -->
              <circle :cx="ocheX" cy="230" r="10" fill="#4ecdc4" filter="url(#glow)" />
              <circle :cx="ocheX" cy="230" r="4" fill="#0a0a0a" />
              <text :x="ocheX - 26" y="262" fill="rgba(255,255,255,0.85)" font-size="13">{{ t('useful.area_setup.svg_oche') }}</text>

              <!-- horizontal distance -->
              <line x1="160" y1="230" :x2="ocheX" y2="230" stroke="rgba(78,205,196,0.85)" stroke-width="3" />
              <text :x="(160 + ocheX) / 2 - 20" y="214" fill="rgba(255,255,255,0.85)" font-size="12">
                {{ current.throwDistance }}
              </text>

              <!-- height -->
              <line x1="160" y1="110" x2="160" y2="230" stroke="rgba(231,76,60,0.85)" stroke-width="3" />
              <text x="114" y="174" fill="rgba(255,255,255,0.85)" font-size="12" transform="rotate(-90 114 174)">
                {{ current.height }}
              </text>

              <!-- diagonal -->
              <line x1="160" y1="110" :x2="ocheX" y2="230" stroke="url(#g)" stroke-width="6" />
              <text :x="(160 + ocheX) / 2 - 28" y="160" fill="rgba(255,255,255,0.90)" font-size="13">
                {{ current.diagonal }}
              </text>

              <!-- right angle marker -->
              <path d="M160 214 L160 230 L176 230" fill="none" stroke="rgba(255,255,255,0.18)" stroke-width="3" />
            </svg>
          </div>
        </q-card-section>
      </q-card>

      <!-- ── Technical blocks ───────────────────────────────────────────────── -->
      <q-card class="card span-4" flat bordered>
        <q-card-section class="q-pa-md">
          <div class="block-title">
            <q-icon name="crop_free" color="primary" class="q-mr-sm" />
            {{ t('useful.area_setup.zone_title') }}
          </div>
          <div class="block-text q-mt-sm" v-html="t('useful.area_setup.zone_text')" />
          <q-banner class="pro q-mt-md" rounded dense>
            <template #avatar><q-icon name="verified" color="primary" /></template>
            <span v-html="t('useful.area_setup.zone_tip')" />
          </q-banner>
        </q-card-section>
      </q-card>

      <q-card class="card span-4" flat bordered>
        <q-card-section class="q-pa-md">
          <div class="block-title">
            <q-icon name="wb_incandescent" color="accent" class="q-mr-sm" />
            {{ t('useful.area_setup.light_title') }}
          </div>
          <div class="block-text q-mt-sm" v-html="t('useful.area_setup.light_text')" />
          <q-banner class="pro q-mt-md" rounded dense>
            <template #avatar><q-icon name="lightbulb" color="accent" /></template>
            <span v-html="t('useful.area_setup.light_tip')" />
          </q-banner>
        </q-card-section>
      </q-card>

      <q-card class="card span-4" flat bordered>
        <q-card-section class="q-pa-md">
          <div class="block-title">
            <q-icon name="linear_scale" color="negative" class="q-mr-sm" />
            {{ t('useful.area_setup.oche_title') }}
          </div>
          <div class="block-text q-mt-sm" v-html="t('useful.area_setup.oche_text')" />
          <q-banner class="pro q-mt-md" rounded dense>
            <template #avatar><q-icon name="construction" color="negative" /></template>
            <span v-html="t('useful.area_setup.oche_tip')" />
          </q-banner>
        </q-card-section>
      </q-card>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const mode = ref('steel')

const current = computed(() => {
  if (mode.value === 'soft') {
    return {
      height:        '1.73 m',
      throwDistance: '2.44 m',
      diagonal:      '2.98 m',
      ocheX:         760,
    }
  }
  return {
    height:        '1.73 m',
    throwDistance: '2.37 m',
    diagonal:      '2.93 m',
    ocheX:         730,
  }
})

const ocheX = computed(() => current.value.ocheX)
</script>

<style scoped lang="scss">
.ds {
  color: #f5f5f5;
}

.kicker {
  font-size: 11px;
  letter-spacing: 0.22em;
  text-transform: uppercase;
  color: rgba(255, 255, 255, 0.55);
}

.title {
  font-size: clamp(28px, 3vw, 36px);
  font-weight: 900;
  letter-spacing: -0.03em;
}

.sub {
  color: rgba(255, 255, 255, 0.68);
  max-width: 80ch;
  line-height: 1.6;
}

.grid {
  display: grid;
  grid-template-columns: repeat(12, 1fr);
  gap: 12px;
}

.card {
  border-radius: 18px;
  border-color: rgba(255, 255, 255, 0.08);
  background: rgba(17, 17, 17, 0.62);
  backdrop-filter: blur(10px);
  box-shadow: 0 12px 32px rgba(0, 0, 0, 0.35);
}

.span-12 { grid-column: span 12; }
.span-6  { grid-column: span 6;  }
.span-4  { grid-column: span 4;  }

.pro {
  background: rgba(255, 255, 255, 0.03);
  border: 1px solid rgba(255, 255, 255, 0.08);
  color: rgba(245, 245, 245, 0.92);
}

.specs {
  display: grid;
  gap: 10px;
}

.spec {
  display: flex;
  gap: 12px;
  align-items: center;
  padding: 10px 12px;
  border-radius: 14px;
  border: 1px solid rgba(255, 255, 255, 0.08);
  background: rgba(10, 10, 10, 0.35);
}

.spec-i {
  width: 34px;
  height: 34px;
  border-radius: 12px;
  border: 1px solid rgba(255, 255, 255, 0.08);
  background: rgba(255, 255, 255, 0.04);
  display: flex;
  align-items: center;
  justify-content: center;
}

.spec-k {
  font-size: 11px;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: rgba(255, 255, 255, 0.55);
}

.spec-v {
  font-weight: 900;
  letter-spacing: -0.02em;
}

.diagram {
  border-radius: 16px;
  border: 1px solid rgba(255, 255, 255, 0.08);
  background: rgba(10, 10, 10, 0.35);
  overflow: hidden;
  padding: 12px;
}

.diagram svg {
  width: 100%;
  height: auto;
  display: block;
}

.block-title {
  display: flex;
  align-items: center;
  font-weight: 900;
  letter-spacing: -0.02em;
}

.block-text {
  color: rgba(255, 255, 255, 0.7);
  line-height: 1.6;
}

@media (max-width: 900px) {
  .span-6,
  .span-4 {
    grid-column: span 12;
  }
}
</style>
