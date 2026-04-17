<template>
  <q-page class="noderigi q-pa-md">
    <div class="container q-mx-auto">
      <NoderigiSubNav />

      <div class="row items-end justify-between q-col-gutter-md q-mb-md">
        <div class="col-12 col-md">
          <div class="text-overline text-grey-6">{{ t('useful.setup.kicker') }}</div>
          <div class="text-h4 text-weight-bold">{{ t('useful.setup.title') }}</div>
          <div class="text-body2 text-grey-6 q-mt-xs">{{ t('useful.setup.subtitle') }}</div>
        </div>
      </div>

      <q-card class="card" flat bordered>
        <q-card-section>
          <DartsSetup />
        </q-card-section>
      </q-card>
    </div>
  </q-page>
</template>

<script setup>
import { useI18n } from 'vue-i18n'
import DartsSetup from 'src/components/DartsSetup.vue'
import NoderigiSubNav from 'src/components/NoderigiSubNav.vue'

const { t } = useI18n()
</script>

<style scoped lang="scss">
.noderigi {
  min-height: 100%;
  background:
    radial-gradient(1000px 700px at 18% 15%, rgba(111, 183, 170, 0.12), transparent 60%),
    radial-gradient(900px 600px at 80% 70%, rgba(231, 76, 60, 0.1), transparent 55%),
    #0a0a0a;
}

.container {
  max-width: 1200px;
}

.card {
  border-radius: 18px;
  border-color: rgba(255, 255, 255, 0.08);
  background: rgba(17, 17, 17, 0.62);
  backdrop-filter: blur(10px);
  box-shadow: 0 12px 32px rgba(0, 0, 0, 0.35);
}
</style>
