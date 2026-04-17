<template>
  <div class="subnav q-mb-lg">
    <div class="subnav__track">
      <router-link
        v-for="item in items"
        :key="item.to"
        :to="item.to"
        class="subnav__item"
        :class="{ 'subnav__item--active': isActive(item.to) }"
      >
        <q-icon :name="item.icon" size="16px" class="subnav__icon" />
        <span class="subnav__label">{{ item.label }}</span>
      </router-link>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()
const route = useRoute()

const items = computed(() => [
  { to: '/noderigi/laukums',   label: t('useful.subnav.setup'),    icon: 'stadium' },
  { to: '/noderigi/trenini',   label: t('useful.subnav.training'), icon: 'fitness_center' },
  { to: '/noderigi/checkout',  label: t('useful.subnav.checkout'), icon: 'check_circle_outline' },
  { to: '/noderigi/noteikumi', label: t('useful.subnav.rules'),    icon: 'gavel' },
])

function isActive(to) {
  return route.path === to
}
</script>

<style scoped lang="scss">
.subnav {
  position: sticky;
  top: 0;
  z-index: 9;
  padding: 10px;
  background: linear-gradient(180deg, rgba(10, 10, 10, 0.97) 0%, rgba(10, 10, 10, 0.72) 100%);
  backdrop-filter: blur(16px);
  -webkit-backdrop-filter: blur(16px);
  border: 1px solid rgba(255, 255, 255, 0.06);
  border-radius: 18px;
}

.subnav__track {
  display: flex;
  gap: 4px;
  overflow-x: auto;
  scrollbar-width: none;

  &::-webkit-scrollbar { display: none; }
}

.subnav__item {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 9px 16px;
  border-radius: 12px;
  white-space: nowrap;
  color: rgba(255, 255, 255, 0.48);
  text-decoration: none;
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 0.82rem;
  font-weight: 600;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  transition: color 0.18s, background 0.18s, border-color 0.18s;
  border: 1px solid transparent;
  flex-shrink: 0;

  &:hover {
    color: #4ecdc4;
    background: rgba(78, 205, 196, 0.07);
  }

  &--active {
    color: #4ecdc4;
    background: rgba(78, 205, 196, 0.12);
    border-color: rgba(78, 205, 196, 0.22);
  }
}
</style>
