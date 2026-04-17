<template>
  <q-page class="noderigi q-pa-md">
    <div class="container q-mx-auto">
      <NoderigiSubNav />

      <div class="row items-end justify-between q-col-gutter-md q-mb-md">
        <div class="col-12 col-md">
          <div class="text-overline text-grey-6">{{ t('useful.checkout.kicker') }}</div>
          <div class="text-h4 text-weight-bold">{{ t('useful.checkout.title') }}</div>
          <div class="text-body2 text-grey-6 q-mt-xs">{{ t('useful.checkout.subtitle') }}</div>
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
            <template #prepend><q-icon name="search" /></template>
          </q-input>
        </div>
      </div>

      <!-- Legend -->
      <div class="legend row items-center q-gutter-sm q-mb-md">
        <span class="legend-label">{{ t('useful.checkout.legend') }}:</span>
        <q-chip dense square color="transparent" class="legend-chip legend-chip--max">161–170</q-chip>
        <q-chip dense square color="transparent" class="legend-chip legend-chip--high">100–160</q-chip>
        <q-chip dense square color="transparent" class="legend-chip legend-chip--mid">61–99</q-chip>
        <q-chip dense square color="transparent" class="legend-chip legend-chip--low">41–60</q-chip>
      </div>

      <q-table
        flat
        bordered
        class="table"
        :rows="filteredCheckouts"
        :columns="checkoutColumns"
        row-key="score"
        :pagination="{ rowsPerPage: 25 }"
        :rows-per-page-options="[10, 25, 50, 0]"
      >
        <template #body-cell-score="props">
          <q-td :props="props" class="mono score" :class="scoreRangeClass(props.value)">
            {{ props.value }}
          </q-td>
        </template>
        <template #body-cell-primary="props">
          <q-td :props="props" class="mono combo">{{ props.value }}</q-td>
        </template>
        <template #body-cell-alt="props">
          <q-td :props="props" class="mono alt">{{ props.value }}</q-td>
        </template>
      </q-table>
    </div>
  </q-page>
</template>

<script setup>
import { computed, ref } from 'vue'
import { useI18n } from 'vue-i18n'
import NoderigiSubNav from 'src/components/NoderigiSubNav.vue'

const { t } = useI18n()
const checkoutQuery = ref('')

// ── Checkout data ─────────────────────────────────────────────────────────────
// Only achievable 3-dart Double Out checkouts from 170 to 41.
// Impossible scores excluded: 169, 168, 166, 165, 163, 162, 159.
// 170 has ONE route only — no alternative exists.
// '-' means no commonly taught alternative.
const CHECKOUTS = {
  170: { p: 'T20, T20, Bull',    a: '-' },
  167: { p: 'T20, T19, Bull',    a: '-' },
  164: { p: 'T20, T18, Bull',    a: '-' },
  161: { p: 'T20, T17, Bull',    a: '-' },
  160: { p: 'T20, T20, D20',     a: '-' },
  158: { p: 'T20, T20, D19',     a: '-' },
  157: { p: 'T20, T19, D20',     a: '-' },
  156: { p: 'T20, T20, D18',     a: '-' },
  155: { p: 'T20, T19, D19',     a: '-' },
  154: { p: 'T20, T18, D20',     a: '-' },
  153: { p: 'T20, T19, D18',     a: '-' },
  152: { p: 'T20, T20, D16',     a: '-' },
  151: { p: 'T20, T17, D20',     a: '-' },
  150: { p: 'T20, T18, D18',     a: 'T20, T10, Bull' },
  149: { p: 'T20, T19, D16',     a: '-' },
  148: { p: 'T20, T16, D20',     a: '-' },
  147: { p: 'T20, T17, D18',     a: 'T19, T18, D18' },
  146: { p: 'T20, T18, D16',     a: '-' },
  145: { p: 'T20, T15, D20',     a: 'T20, T19, D14' },
  144: { p: 'T20, T20, D12',     a: 'T20, T16, D18' },
  143: { p: 'T20, T17, D16',     a: 'T19, T16, D19' },
  142: { p: 'T20, T14, D20',     a: 'T20, T18, D14' },
  141: { p: 'T20, T19, D12',     a: 'T19, T16, D18' },
  140: { p: 'T20, T20, D10',     a: 'T20, T16, D16' },
  139: { p: 'T20, T13, D20',     a: 'T19, T14, D20' },
  138: { p: 'T20, T18, D12',     a: 'T20, T14, D18' },
  137: { p: 'T20, T19, D10',     a: 'T17, T20, D13' },
  136: { p: 'T20, T20, D8',      a: 'T20, T16, D14' },
  135: { p: 'T20, T15, D15',     a: 'T20, T17, D12' },
  134: { p: 'T20, T14, D16',     a: 'T20, T18, D10' },
  133: { p: 'T20, T19, D8',      a: 'T19, T16, D14' },
  132: { p: 'T20, T16, D12',     a: 'T20, T12, D18' },
  131: { p: 'T20, T13, D16',     a: 'T20, T17, D10' },
  130: { p: 'T20, T20, D5',      a: 'T20, T10, D20' },
  129: { p: 'T19, T16, D12',     a: 'T20, T19, D6' },
  128: { p: 'T20, T16, D10',     a: 'T18, T14, D16' },
  127: { p: 'T20, T17, D8',      a: 'T19, T18, D8' },
  126: { p: 'T19, T19, D6',      a: 'T20, T18, D6' },
  125: { p: 'T20, T15, D10',     a: 'T20, T5, Bull' },
  124: { p: 'T20, T16, D8',      a: 'T18, T14, D14' },
  123: { p: 'T20, T13, D12',     a: 'T19, T16, D9' },
  122: { p: 'T18, T18, D7',      a: 'T20, T14, D10' },
  121: { p: 'T20, T11, D14',     a: 'T17, T18, D8' },
  120: { p: 'T20, S20, D20',     a: 'T20, T16, D6' },
  119: { p: 'T20, S19, D20',     a: 'T19, T12, D13' },
  118: { p: 'T20, S18, D20',     a: 'T20, T18, D2' },
  117: { p: 'T20, S17, D20',     a: 'T19, T16, D6' },
  116: { p: 'T20, S16, D20',     a: 'T19, T19, D1' },
  115: { p: 'T20, S15, D20',     a: 'T19, T18, D2' },
  114: { p: 'T20, S14, D20',     a: 'T20, T14, D6' },
  113: { p: 'T20, S13, D20',     a: 'T19, S16, D20' },
  112: { p: 'T20, S12, D20',     a: 'T20, T12, D8' },
  111: { p: 'T20, S11, D20',     a: 'T19, S14, D20' },
  110: { p: 'T20, S10, D20',     a: 'T20, Bull' },
  109: { p: 'T20, S9, D20',      a: 'T19, S12, D20' },
  108: { p: 'T20, S8, D20',      a: 'T19, T11, D9' },
  107: { p: 'T19, S10, D20',     a: 'T20, S7, D20' },
  106: { p: 'T20, S6, D20',      a: 'T19, S9, D20' },
  105: { p: 'T20, S5, D20',      a: 'T19, S8, D20' },
  104: { p: 'T20, S4, D20',      a: 'T18, S10, D20' },
  103: { p: 'T20, S3, D20',      a: 'T17, S12, D20' },
  102: { p: 'T20, S2, D20',      a: 'T18, S8, D20' },
  101: { p: 'T20, S1, D20',      a: 'T17, S10, D20' },
  100: { p: 'T20, D20',          a: 'T20, S20, D10' },
  99:  { p: 'T19, S2, D20',      a: 'T13, T10, D15' },
  98:  { p: 'T20, D19',          a: 'T18, S4, D20' },
  97:  { p: 'T19, D20',          a: 'T20, S17, D10' },
  96:  { p: 'T20, D18',          a: 'T16, S8, D20' },
  95:  { p: 'T19, D19',          a: 'T20, S15, D10' },
  94:  { p: 'T18, D20',          a: 'T20, S14, D10' },
  93:  { p: 'T19, D18',          a: 'T17, S2, D20' },
  92:  { p: 'T20, D16',          a: 'T20, S12, D10' },
  91:  { p: 'T17, D20',          a: 'T19, S14, D10' },
  90:  { p: 'T18, D18',          a: 'T20, D15' },
  89:  { p: 'T19, D16',          a: 'T20, S9, D10' },
  88:  { p: 'T20, D14',          a: 'T18, D17' },
  87:  { p: 'T17, D18',          a: 'T19, S10, D10' },
  86:  { p: 'T18, D16',          a: 'T20, S6, D10' },
  85:  { p: 'T15, D20',          a: 'T19, S8, D10' },
  84:  { p: 'T20, D12',          a: 'T16, D18' },
  83:  { p: 'T17, D16',          a: 'T19, S6, D10' },
  82:  { p: 'T14, D20',          a: 'T18, D14' },
  81:  { p: 'T19, D12',          a: 'T15, D18' },
  80:  { p: 'T20, D10',          a: 'T16, D16' },
  79:  { p: 'T19, D11',          a: 'T13, D20' },
  78:  { p: 'T18, D12',          a: 'T14, D18' },
  77:  { p: 'T19, D10',          a: 'T15, D16' },
  76:  { p: 'T20, D8',           a: 'T16, D14' },
  75:  { p: 'T17, D12',          a: 'T15, D15' },
  74:  { p: 'T14, D16',          a: 'T18, D10' },
  73:  { p: 'T19, D8',           a: 'T17, D11' },
  72:  { p: 'T16, D12',          a: 'T14, D15' },
  71:  { p: 'T13, D16',          a: 'T19, D7' },
  70:  { p: 'T18, D8',           a: 'T10, D20' },
  69:  { p: 'T19, D6',           a: 'T13, D15' },
  68:  { p: 'T20, D4',           a: 'T16, D10' },
  67:  { p: 'T17, D8',           a: 'T9, D20' },
  66:  { p: 'T10, D18',          a: 'T14, D12' },
  65:  { p: 'T19, D4',           a: 'T11, D16' },
  64:  { p: 'T16, D8',           a: 'T14, D11' },
  63:  { p: 'T13, D12',          a: 'T9, D18' },
  62:  { p: 'T10, D16',          a: 'T12, D13' },
  61:  { p: 'T15, D8',           a: 'T7, D20' },
  60:  { p: 'S20, D20',          a: 'T12, D12' },
  59:  { p: 'S19, D20',          a: 'S9, Bull' },
  58:  { p: 'S18, D20',          a: 'S20, D19' },
  57:  { p: 'S17, D20',          a: 'S7, Bull' },
  56:  { p: 'S16, D20',          a: 'S20, D18' },
  55:  { p: 'S15, D20',          a: 'S5, Bull' },
  54:  { p: 'S14, D20',          a: 'S20, D17' },
  53:  { p: 'S13, D20',          a: 'S3, Bull' },
  52:  { p: 'S12, D20',          a: 'S20, D16' },
  51:  { p: 'S11, D20',          a: 'S19, D16' },
  50:  { p: 'Bull',              a: 'S10, D20' },
  49:  { p: 'S9, D20',           a: 'S17, D16' },
  48:  { p: 'S8, D20',           a: 'S16, D16' },
  47:  { p: 'S7, D20',           a: 'S15, D16' },
  46:  { p: 'S6, D20',           a: 'S14, D16' },
  45:  { p: 'S5, D20',           a: 'S13, D16' },
  44:  { p: 'S4, D20',           a: 'S12, D16' },
  43:  { p: 'S3, D20',           a: 'S11, D16' },
  42:  { p: 'S2, D20',           a: 'S10, D16' },
  41:  { p: 'S1, D20',           a: 'S9, D16' },
}

const checkouts = Object.entries(CHECKOUTS)
  .sort(([a], [b]) => Number(b) - Number(a))
  .map(([score, v]) => ({ score: Number(score), primary: v.p, alt: v.a }))

const checkoutColumns = computed(() => [
  { name: 'score',   label: t('useful.checkout.col_score'),   field: 'score',   align: 'left', sortable: true },
  { name: 'primary', label: t('useful.checkout.col_primary'), field: 'primary', align: 'left' },
  { name: 'alt',     label: t('useful.checkout.col_alt'),     field: 'alt',     align: 'left' },
])

const filteredCheckouts = computed(() => {
  const q = checkoutQuery.value.trim().toLowerCase()
  if (!q) return checkouts
  return checkouts.filter(
    (r) =>
      String(r.score).includes(q) ||
      r.primary.toLowerCase().includes(q) ||
      r.alt.toLowerCase().includes(q),
  )
})

function scoreRangeClass(score) {
  if (score >= 161) return 'score--max'
  if (score >= 100) return 'score--high'
  if (score >= 61)  return 'score--mid'
  return 'score--low'
}
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

.search { min-width: min(380px, 100%); }

.legend { flex-wrap: wrap; }

.legend-label {
  font-size: 11px;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: rgba(255, 255, 255, 0.45);
}

.legend-chip {
  font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
  font-size: 0.78rem;
  font-weight: 700;
  border-radius: 6px;
  padding: 2px 10px;
  border: 1px solid;

  &--max { color: #d4a843; border-color: rgba(212, 168, 67, 0.35); background: rgba(212, 168, 67, 0.08) !important; }
  &--high { color: #4ecdc4; border-color: rgba(78, 205, 196, 0.3); background: rgba(78, 205, 196, 0.07) !important; }
  &--mid { color: rgba(245, 245, 245, 0.85); border-color: rgba(255, 255, 255, 0.15); background: rgba(255, 255, 255, 0.04) !important; }
  &--low { color: rgba(255, 255, 255, 0.45); border-color: rgba(255, 255, 255, 0.08); background: transparent !important; }
}

.table {
  border-radius: 18px;
  overflow: hidden;
  border-color: rgba(255, 255, 255, 0.08);
  background: rgba(17, 17, 17, 0.62);
  backdrop-filter: blur(10px);
  box-shadow: 0 12px 32px rgba(0, 0, 0, 0.35);
}

.mono { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; }

.score {
  font-weight: 900;
  font-size: 1rem;
  min-width: 52px;

  &--max  { color: #d4a843; }
  &--high { color: #4ecdc4; }
  &--mid  { color: rgba(245, 245, 245, 0.9); }
  &--low  { color: rgba(255, 255, 255, 0.5); }
}

.combo { color: rgba(245, 245, 245, 0.88); }
.alt   { color: rgba(255, 255, 255, 0.45); }
</style>
