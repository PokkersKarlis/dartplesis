<template>
  <div class="supporters-bar" :class="`supporters-bar--${variant}`">
    <template v-if="supporters.length > 0">
      <div v-if="showLabel" class="sup-label">{{ t('supporters.eyebrow') }}</div>
      <div class="sup-track" :class="{ 'sup-track--scroll': supporters.length > 5 }">
        <a
          v-for="s in supporters"
          :key="s.id"
          :href="s.url ?? undefined"
          :target="s.url ? '_blank' : undefined"
          :rel="s.url ? 'noopener' : undefined"
          class="sup-item"
          :class="{ 'sup-item--linked': !!s.url }"
          :title="s.name"
        >
          <img v-if="s.logo" :src="logoUrl(s.logo)" :alt="s.name" class="sup-logo" />
          <span class="sup-text">{{ s.name }}</span>
        </a>
      </div>
    </template>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useI18n } from 'vue-i18n'

defineProps({
  variant:   { type: String,  default: 'default' },
  showLabel: { type: Boolean, default: true },
})

const { t } = useI18n()
const supporters = ref([])

function logoUrl(path) {
  if (!path) return ''
  if (path.startsWith('http')) return path
  return path.startsWith('/') ? path : `/${path}`
}

onMounted(async () => {
  try {
    const res  = await fetch('/api/supporters', { headers: { Accept: 'application/json' } })
    if (!res.ok) return
    const data = await res.json().catch(() => ({}))
    supporters.value = data.supporters ?? []
  } catch { /* silent */ }
})
</script>

<style scoped lang="scss">
// ── Default (index page) ──────────────────────────────────────────────────────

.supporters-bar {
  width: 100%;
}

.sup-label {
  font-size: 0.62rem;
  font-weight: 700;
  letter-spacing: 0.22em;
  text-transform: uppercase;
  color: rgba(255,255,255,0.3);
  margin-bottom: 1.25rem;
  text-align: center;
}

.sup-track {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: center;
  gap: 1.5rem;

  &--scroll {
    flex-wrap: nowrap;
    overflow-x: auto;
    scrollbar-width: none;
    justify-content: flex-start;
    padding-bottom: 4px;
    &::-webkit-scrollbar { display: none; }
  }
}

.sup-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 6px;
  text-decoration: none;
  flex-shrink: 0;
  padding: 10px 18px;
  border-radius: 10px;
  background: rgba(255, 255, 255, 0.88);
  transition: background 0.2s, transform 0.2s;

  &--linked:hover {
    background: rgba(255, 255, 255, 1);
    transform: translateY(-2px);
  }
}

.sup-logo {
  max-height: 100px;
  height: 100px;
  max-width: auto;
  object-fit: contain;
  display: block;
}

.sup-text {
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 0.75rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: #222;
  text-align: center;
}

// ── Footer variant ────────────────────────────────────────────────────────────

.supporters-bar--footer {
  .sup-label {
    font-size: 0.58rem;
    text-align: center;
    margin-bottom: 0.75rem;
  }

  .sup-track {
    gap: 1rem;
    justify-content: center;
  }

  .sup-logo {
    max-height: 100px;
    max-width: auto;
  }

  .sup-item {
    padding: 6px 12px;
    border-radius: 7px;
    gap: 4px;
  }

  .sup-text {
    font-size: 0.7rem;
    color: #333;
    text-align: center;
  }
}
</style>
