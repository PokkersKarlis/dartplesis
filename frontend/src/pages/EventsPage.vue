<template>
  <q-page class="events-page flex flex-center">
    <div class="uc-wrap">

      <!-- Animated dart -->
      <div class="uc-dart" aria-hidden="true">🎯</div>

      <!-- Eyebrow -->
      <div class="uc-eyebrow">{{ t('events.uc_eyebrow') }}</div>

      <!-- Heading -->
      <h1 class="uc-title">
        {{ t('events.uc_title') }}<span class="uc-accent">.</span>
      </h1>
      <p class="uc-sub">{{ t('events.uc_sub') }}</p>

      <!-- Divider -->
      <div class="uc-line" aria-hidden="true" />

      <!-- Status chips -->
      <div class="uc-chips">
        <span class="uc-chip">
          <span class="uc-dot" />
          {{ t('events.uc_badge') }}
        </span>
        <span class="uc-chip uc-chip--dim">{{ t('events.uc_soon') }}</span>
      </div>

    </div>
  </q-page>
</template>

<script setup>
import { useI18n } from 'vue-i18n'
const { t } = useI18n()
</script>

<style scoped lang="scss">
.events-page {
  background: #0a0a0a;
  min-height: 100vh;
}

.uc-wrap {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  padding: 4rem 2rem;
  max-width: 560px;
}

.uc-dart {
  font-size: 4rem;
  animation: spin-bounce 2.4s ease-in-out infinite;
  margin-bottom: 2rem;
  filter: drop-shadow(0 0 18px rgba(78, 205, 196, 0.4));
}

@keyframes spin-bounce {
  0%   { transform: rotate(-15deg) scale(1);   }
  30%  { transform: rotate(10deg)  scale(1.1); }
  60%  { transform: rotate(-8deg)  scale(0.95);}
  100% { transform: rotate(-15deg) scale(1);   }
}

.uc-eyebrow {
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 0.72rem;
  font-weight: 700;
  letter-spacing: 0.28em;
  text-transform: uppercase;
  color: #4ecdc4;
  margin-bottom: 1rem;
}

.uc-title {
  font-family: 'Bebas Neue', sans-serif;
  font-size: clamp(3rem, 10vw, 5.5rem);
  letter-spacing: 0.04em;
  line-height: 1;
  color: #f5f5f5;
  margin: 0 0 1rem;
}

.uc-accent {
  color: #4ecdc4;
}

.uc-sub {
  font-size: 1rem;
  color: rgba(255, 255, 255, 0.45);
  margin: 0 0 2rem;
  line-height: 1.6;
}

.uc-line {
  width: 60px;
  height: 2px;
  background: linear-gradient(90deg, transparent, #4ecdc4, transparent);
  margin-bottom: 2rem;
  opacity: 0.6;
}

.uc-chips {
  display: flex;
  gap: 0.75rem;
  flex-wrap: wrap;
  justify-content: center;
}

.uc-chip {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 0.75rem;
  font-weight: 600;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  padding: 6px 14px;
  border-radius: 999px;
  border: 1px solid rgba(78, 205, 196, 0.3);
  color: rgba(255, 255, 255, 0.6);
  background: rgba(78, 205, 196, 0.06);

  &--dim {
    border-color: rgba(255, 255, 255, 0.08);
    background: rgba(255, 255, 255, 0.03);
    color: rgba(255, 255, 255, 0.3);
  }
}

.uc-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: #4ecdc4;
  animation: pulse 1.6s ease-in-out infinite;
}

@keyframes pulse {
  0%, 100% { opacity: 1;   transform: scale(1);    }
  50%       { opacity: 0.4; transform: scale(0.75); }
}
</style>
