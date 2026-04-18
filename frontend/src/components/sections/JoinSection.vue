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

      <!-- ── Column 3: action buttons ────────────────────────────────────── -->
      <div class="contact-actions">

        <router-link to="/pievienojies" class="action-btn action-btn--join">
          <svg class="action-btn__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
            <circle cx="9" cy="7" r="4"/>
            <line x1="19" y1="8" x2="19" y2="14"/>
            <line x1="22" y1="11" x2="16" y2="11"/>
          </svg>
          {{ t('contact.join_btn') }}
        </router-link>
      </div>
    </div>
  </section>
</template>

<script setup>
import { useI18n } from 'vue-i18n'
const { t } = useI18n()
</script>

<style scoped lang="scss">
// ── Action buttons ────────────────────────────────────────────────────────────

.contact-actions {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  justify-content: flex-start;
  padding-top: 0.25rem;
}

.action-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.6rem;
  padding: 0.9rem 1.5rem;
  border-radius: 10px;
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 0.9rem;
  font-weight: 700;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  text-decoration: none;
  transition: all 0.2s;
  cursor: pointer;

  &__icon {
    width: 18px;
    height: 18px;
    flex-shrink: 0;
  }

  &--call {
    background: #4ecdc4;
    color: #0a0a0a;

    &:hover {
      background: #6ee0d9;
      transform: translateY(-2px);
      box-shadow: 0 8px 24px rgba(78, 205, 196, 0.3);
    }
  }

  &--join {
    background: transparent;
    color: #f5f5f5;
    border: 1px solid rgba(255, 255, 255, 0.15);

    &:hover {
      border-color: rgba(78, 205, 196, 0.5);
      color: #4ecdc4;
      background: rgba(78, 205, 196, 0.06);
      transform: translateY(-2px);
    }
  }
}

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
