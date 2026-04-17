<template>
  <section class="dk-section about-section" id="par-mums">

    <!-- Header -->
    <div class="about-header">
      <div class="dk-eyebrow">{{ t('about.eyebrow') }}</div>
      <h2 class="dk-title">
        {{ t('about.title') }} <span class="text-teal">{{ t('about.title_accent') }}</span>
      </h2>
    </div>

    <!-- ── Origin story ────────────────────────────────────────────────────── -->
    <div class="about-grid">
      <div class="about-text">
        <div class="about-tagline">{{ t('about.tagline') }}</div>
        <p>{{ t('about.p1') }}</p>
        <p>{{ t('about.p2') }}</p>
        <div class="dart-line" />
        <p class="about-motto">{{ t('about.motto') }}</p>
      </div>

      <div class="about-visual">
        <q-card flat class="about-logo-card">
          <span class="corner corner--tl" aria-hidden="true" />
          <span class="corner corner--tr" aria-hidden="true" />
          <span class="corner corner--bl" aria-hidden="true" />
          <span class="corner corner--br" aria-hidden="true" />
          <img src="/logo.jpg" alt="DK Dārtplēsis" class="about-logo" />
        </q-card>
      </div>
    </div>

    <!-- ── Mission & Vision ────────────────────────────────────────────────── -->
    <div class="mission-block">
      <div class="mission-icon-wrap" aria-hidden="true">
        <q-icon name="track_changes" size="22px" />
      </div>
      <div>
        <div class="mission-title">{{ t('about.mission_title') }}</div>
        <p class="mission-text">{{ t('about.mission_text') }}</p>
      </div>
    </div>

    <!-- ── Priorities ──────────────────────────────────────────────────────── -->
    <div class="subsection-label">{{ t('about.priorities_title') }}</div>
    <div class="cards-grid">
      <div
        v-for="(item, i) in priorities"
        :key="i"
        class="about-card"
      >
        <span class="about-card__icon" aria-hidden="true">
          <q-icon :name="priorityIcons[i]" size="18px" />
        </span>
        <div class="about-card__title">{{ item.title }}</div>
        <p class="about-card__text">{{ item.text }}</p>
      </div>
    </div>

    <!-- ── Activities ──────────────────────────────────────────────────────── -->
    <div class="subsection-label q-mt-xl">{{ t('about.activities_title') }}</div>
    <p class="activities-intro">{{ t('about.activities_text') }}</p>
    <div class="cards-grid">
      <div
        v-for="(item, i) in activities"
        :key="i"
        class="about-card"
      >
        <span class="about-card__icon" aria-hidden="true">
          <q-icon :name="activityIcons[i]" size="18px" />
        </span>
        <div class="about-card__title">{{ item.title }}</div>
        <p class="about-card__text">{{ item.text }}</p>
      </div>
    </div>

    <!-- ── CTA block ───────────────────────────────────────────────────────── -->
    <div class="about-cta-block">
      <div class="cta-title">{{ t('about.why_title') }}</div>
      <p class="cta-text">{{ t('about.why_text') }}</p>
      <p class="cta-accent">{{ t('about.cta') }}</p>
      <router-link to="/pievienojies" class="cta-btn">
        <q-icon name="person_add" size="18px" class="q-mr-xs" />
        {{ t('about.cta_btn') }}
      </router-link>
    </div>

  </section>
</template>

<script setup>
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'

const { t, tm } = useI18n()

const priorityIcons  = ['groups', 'school', 'trending_up']
const activityIcons  = ['fitness_center', 'emoji_events', 'military_tech']

const priorities = computed(() => tm('about.priorities'))
const activities = computed(() => tm('about.activities'))
</script>

<style scoped lang="scss">
.text-teal { color: #4ecdc4; }

.about-section { padding-bottom: 5rem; }

// ── Header ────────────────────────────────────────────────────────────────────

.about-header { margin-bottom: 3rem; }

// ── Origin story grid ─────────────────────────────────────────────────────────

.about-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 4rem;
  align-items: center;
  margin-bottom: 3.5rem;
}

.about-tagline {
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 0.72rem;
  font-weight: 700;
  letter-spacing: 0.22em;
  text-transform: uppercase;
  color: #4ecdc4;
  margin-bottom: 1.25rem;
}

.about-text p {
  font-size: 1rem;
  line-height: 1.8;
  color: rgba(245, 245, 245, 0.75);
  margin-bottom: 1.2rem;
}

.dart-line {
  width: 40px;
  height: 2px;
  background: #4ecdc4;
  margin: 1.5rem 0;
  opacity: 0.5;
}

.about-motto {
  font-style: italic;
  color: #4ecdc4 !important;
  font-size: 0.95rem !important;
}

.about-visual { position: relative; }

.about-logo-card {
  display: flex;
  align-items: center;
  justify-content: center;
  height: 320px;
  background: #161616 !important;
  border: 1px solid rgba(78, 205, 196, 0.15) !important;
  position: relative;
  overflow: hidden;
  border-radius: 0 !important;

  &::before {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(ellipse at center, rgba(78, 205, 196, 0.18), transparent 70%);
  }
}

.about-logo {
  width: 180px;
  height: 180px;
  object-fit: contain;
  position: relative;
  z-index: 1;
  animation: pulse-glow 4s ease-in-out infinite;
}

@keyframes pulse-glow {
  0%, 100% { filter: drop-shadow(0 0 20px rgba(78, 205, 196, 0.4)); }
  50%       { filter: drop-shadow(0 0 50px rgba(78, 205, 196, 0.7)); }
}

.corner {
  position: absolute;
  width: 20px; height: 20px;
  border-color: #4ecdc4;
  border-style: solid;
  opacity: 0.6;
  z-index: 2;

  &--tl { top: 12px;    left: 12px;  border-width: 2px 0 0 2px; }
  &--tr { top: 12px;    right: 12px; border-width: 2px 2px 0 0; }
  &--bl { bottom: 12px; left: 12px;  border-width: 0 0 2px 2px; }
  &--br { bottom: 12px; right: 12px; border-width: 0 2px 2px 0; }
}

// ── Mission block ─────────────────────────────────────────────────────────────

.mission-block {
  display: flex;
  align-items: flex-start;
  gap: 1.25rem;
  background: rgba(78, 205, 196, 0.05);
  border: 1px solid rgba(78, 205, 196, 0.12);
  border-radius: 14px;
  padding: 1.75rem 2rem;
  margin-bottom: 3rem;
}

.mission-icon-wrap {
  flex-shrink: 0;
  width: 44px;
  height: 44px;
  border-radius: 10px;
  background: rgba(78, 205, 196, 0.08);
  border: 1px solid rgba(78, 205, 196, 0.15);
  display: flex;
  align-items: center;
  justify-content: center;
  color: #4ecdc4;
}

.mission-title {
  font-family: 'Bebas Neue', sans-serif;
  font-size: 1.4rem;
  letter-spacing: 0.06em;
  color: #f5f5f5;
  margin-bottom: 0.5rem;
}

.mission-text {
  font-size: 0.95rem;
  line-height: 1.75;
  color: rgba(245, 245, 245, 0.7);
  margin: 0;
}

// ── Section labels ────────────────────────────────────────────────────────────

.subsection-label {
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 0.7rem;
  font-weight: 700;
  letter-spacing: 0.2em;
  text-transform: uppercase;
  color: rgba(255, 255, 255, 0.38);
  margin-bottom: 1.25rem;
}

.activities-intro {
  font-size: 0.92rem;
  line-height: 1.7;
  color: rgba(245, 245, 245, 0.6);
  margin-bottom: 1.5rem;
}

// ── Cards grid ────────────────────────────────────────────────────────────────

.cards-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1.5px;
  background: rgba(78, 205, 196, 0.12);
}

.about-card {
  background: #161616;
  padding: 1.75rem 1.5rem;
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  position: relative;
  overflow: hidden;
  transition: background 0.25s;

  &::after {
    content: '';
    position: absolute;
    bottom: 0; left: 0; right: 0;
    height: 2px;
    background: #4ecdc4;
    transform: scaleX(0);
    transition: transform 0.25s;
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
    font-size: 1.35rem;
    letter-spacing: 0.04em;
    color: #f5f5f5;
    line-height: 1;
  }

  &__text {
    font-size: 0.875rem;
    line-height: 1.65;
    color: #888;
    margin: 0;
    flex: 1;
  }
}

// ── CTA block ─────────────────────────────────────────────────────────────────

.about-cta-block {
  margin-top: 3.5rem;
  border-top: 1px solid rgba(78, 205, 196, 0.1);
  padding-top: 2.5rem;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  gap: 1rem;
}

.cta-title {
  font-family: 'Bebas Neue', sans-serif;
  font-size: 1.8rem;
  letter-spacing: 0.05em;
  color: #f5f5f5;
  line-height: 1.1;
}

.cta-text {
  font-size: 0.95rem;
  line-height: 1.75;
  color: rgba(245, 245, 245, 0.68);
  margin: 0;
  max-width: 680px;
}

.cta-accent {
  font-size: 0.95rem;
  font-style: italic;
  color: #4ecdc4;
  margin: 0;
}

.cta-btn {
  display: inline-flex;
  align-items: center;
  padding: 0.85rem 1.75rem;
  border-radius: 10px;
  background: #4ecdc4;
  color: #0a0a0a;
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 0.9rem;
  font-weight: 700;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  text-decoration: none;
  transition: background 0.2s, transform 0.2s, box-shadow 0.2s;
  margin-top: 0.25rem;

  &:hover {
    background: #6ee0d9;
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(78, 205, 196, 0.3);
  }
}

// ── Responsive ────────────────────────────────────────────────────────────────

@media (max-width: 900px) {
  .about-grid           { grid-template-columns: 1fr; }
  .about-visual         { order: -1; }
  .about-logo-card      { height: 220px; }
  .cards-grid           { grid-template-columns: 1fr; }
}

@media (max-width: 640px) {
  .cards-grid           { grid-template-columns: 1fr; }
  .mission-block        { flex-direction: column; }
}
</style>
