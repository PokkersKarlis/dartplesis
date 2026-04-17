<template>
  <section class="join-section" id="pievienojies">
    <div class="join-bg" aria-hidden="true" />

    <div class="join-content">
      <div class="dk-eyebrow dk-eyebrow--center">{{ t('join_cta.eyebrow') }}</div>

      <h2 class="join-title">
        {{ t('join_cta.title') }}
        <span class="join-title__stroke">{{ t('join_cta.title_stroke') }}</span><br />
        {{ t('join_cta.title_end') }}
      </h2>

      <p class="join-sub">{{ t('join_cta.subtitle') }}</p>

      <div class="join-actions">
        <a href="tel:+37122811821" class="dk-btn-ghost">{{ t('join_cta.cta_call') }}</a>
      </div>
    </div>
  </section>
</template>

<script setup>
import { useI18n } from 'vue-i18n'
const { t } = useI18n()
</script>

<style scoped lang="scss">
.join-section {
  text-align: center;
  padding: 100px 5vw;
  position: relative;
  overflow: hidden;
}

.join-bg {
  position: absolute;
  inset: 0;
  background: radial-gradient(ellipse 60% 80% at 50% 50%, rgba(78, 205, 196, 0.09) 0%, transparent 70%);
}

.join-content { position: relative; z-index: 1; }

.join-title {
  font-family: 'Bebas Neue', sans-serif;
  font-size: clamp(3rem, 8vw, 7rem);
  letter-spacing: 0.03em;
  line-height: 0.9;
  color: #f5f5f5;
  margin: 1rem 0 1.5rem;

  &__stroke {
    -webkit-text-stroke: 2px #4ecdc4;
    color: transparent;
  }
}

.join-sub {
  font-size: 1rem;
  color: #888;
  max-width: 480px;
  margin: 0 auto 2.5rem;
  line-height: 1.7;
}

.join-actions {
  display: flex;
  gap: 1rem;
  justify-content: center;
  flex-wrap: wrap;
}
</style>
