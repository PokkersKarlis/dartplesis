<template>
  <section class="dk-section dk-section--dark reveal" id="features">
    <div class="dk-eyebrow">{{ t('features.eyebrow') }}</div>
    <h2 class="dk-title">
      {{ t('features.title') }} <span class="text-teal">{{ t('features.title_accent') }}</span>
    </h2>

    <div class="features-grid">
      <div
        v-for="(feature, i) in features"
        :key="i"
        class="feature-card"
      >
        <span class="feature-card__icon" aria-hidden="true">
          <q-icon :name="featureIcons[i]" size="18px" />
        </span>
        <div class="feature-card__title">{{ feature.title }}</div>
        <p class="feature-card__text">{{ feature.text }}</p>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'

const { t, tm } = useI18n()

const featureIcons = ['fitness_center', 'emoji_events', 'groups', 'bar_chart', 'checkroom', 'public']
const features = computed(() => tm('features.items'))
</script>

<style scoped lang="scss">
.text-teal { color: #4ecdc4; }

.features-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1.5px;
  margin-top: 3rem;
  background: rgba(78, 205, 196, 0.15);
}

.feature-card {
  background: #161616;
  padding: 2.5rem 2rem;
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  position: relative;
  overflow: hidden;
  transition: background 0.3s;

  &::after {
    content: '';
    position: absolute;
    bottom: 0; left: 0; right: 0;
    height: 2px;
    background: #4ecdc4;
    transform: scaleX(0);
    transition: transform 0.3s;
    transform-origin: left;
  }

  &:hover {
    background: #1c1c1c;
    &::after { transform: scaleX(1); }
  }

  &__icon {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    background: rgba(78, 205, 196, 0.08);
    border: 1px solid rgba(78, 205, 196, 0.15);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #4ecdc4;
    flex-shrink: 0;
  }

  &__title {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 1.5rem;
    letter-spacing: 0.05em;
    color: #f5f5f5;
    line-height: 1;
  }

  &__text {
    font-size: 0.9rem;
    line-height: 1.7;
    color: #888;
    margin: 0;
    flex: 1;
  }
}

@media (max-width: 900px) {
  .features-grid { grid-template-columns: 1fr; }
}
</style>
