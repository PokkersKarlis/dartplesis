<template>
  <section class="hero" id="home">
    <div class="hero-bg" aria-hidden="true" />
    <div class="hero-grid-lines" aria-hidden="true" />
    <img src="/logo.jpg" alt="" class="hero-mascot" aria-hidden="true" />

    <div class="hero-eyebrow">{{ t('hero.eyebrow') }}</div>

    <h1 class="hero-title">
      DK<br />
      <span class="hero-title__accent">Dārt</span><span class="hero-title__stroke">plēsis</span>
    </h1>

    <p class="hero-sub">{{ t('hero.subtitle') }}</p>

    <div class="hero-actions">
      <router-link to="/kontakti" class="dk-btn-primary">{{ t('hero.cta_join') }}</router-link>
      <router-link to="/notikumi" class="dk-btn-ghost">{{ t('hero.cta_events') }}</router-link>
    </div>
  </section>
</template>

<script setup>
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

</script>

<style scoped lang="scss">
.hero {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: flex-start;
  padding: 32px 5vw 60px;
  position: relative;
  overflow: hidden;
}

.hero-bg {
  position: absolute;
  inset: 0;
  background:
    radial-gradient(ellipse 60% 70% at 80% 50%, rgba(78, 205, 196, 0.07) 0%, transparent 70%),
    radial-gradient(ellipse 40% 40% at 20% 80%, rgba(192, 57, 43, 0.08) 0%, transparent 60%);
}

.hero-grid-lines {
  position: absolute;
  inset: 0;
  background-image:
    linear-gradient(rgba(78, 205, 196, 0.04) 1px, transparent 1px),
    linear-gradient(90deg, rgba(78, 205, 196, 0.04) 1px, transparent 1px);
  background-size: 60px 60px;
}

.hero-mascot {
  position: absolute;
  right: -2vw;
  bottom: 0;
  width: clamp(280px, 45vw, 640px);
  opacity: 0.12;
  filter: drop-shadow(0 0 60px rgba(78, 205, 196, 0.3));
  pointer-events: none;
  animation: float 6s ease-in-out infinite;
}

@keyframes float {
  0%, 100% { transform: translateY(0); }
  50%       { transform: translateY(-12px); }
}

.hero-eyebrow {
  font-family: 'Barlow Condensed', sans-serif;
  font-size: clamp(0.75rem, 1.5vw, 0.9rem);
  font-weight: 700;
  letter-spacing: 0.25em;
  text-transform: uppercase;
  color: #4ecdc4;
  margin-bottom: 1rem;
  display: flex;
  align-items: center;
  gap: 12px;
  opacity: 0;
  animation: fadeUp 0.8s 0.2s forwards;
  position: relative;
  z-index: 1;

  &::before {
    content: '';
    display: block;
    width: 32px;
    height: 2px;
    background: #4ecdc4;
    flex-shrink: 0;
  }
}

.hero-title {
  font-family: 'Bebas Neue', sans-serif;
  font-size: clamp(3.5rem, 12vw, 10rem);
  line-height: 0.88;
  letter-spacing: 0.02em;
  color: #f5f5f5;
  margin-bottom: 1.5rem;
  opacity: 0;
  animation: fadeUp 0.8s 0.4s forwards;
  position: relative;
  z-index: 1;

  &__accent { color: #4ecdc4; }

  &__stroke {
    -webkit-text-stroke: 2px #4ecdc4;
    color: transparent;
  }
}

.hero-sub {
  font-size: clamp(1rem, 2.5vw, 1.25rem);
  font-weight: 300;
  color: #888;
  max-width: 520px;
  line-height: 1.7;
  margin-bottom: 2.5rem;
  opacity: 0;
  animation: fadeUp 0.8s 0.6s forwards;
  position: relative;
  z-index: 1;
}

.hero-actions {
  display: flex;
  gap: 1rem;
  flex-wrap: wrap;
  opacity: 0;
  animation: fadeUp 0.8s 0.8s forwards;
  position: relative;
  z-index: 1;
}

.hero-stats {
  display: flex;
  gap: 2.5rem;
  margin-top: 3.5rem;
  opacity: 0;
  animation: fadeUp 0.8s 1s forwards;
  position: relative;
  z-index: 1;
}

.hero-stat {
  &__num {
    font-family: 'Bebas Neue', sans-serif;
    font-size: clamp(1.8rem, 4vw, 2.8rem);
    color: #4ecdc4;
    line-height: 1;
  }

  &__label {
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 0.7rem;
    font-weight: 600;
    letter-spacing: 0.15em;
    text-transform: uppercase;
    color: #888;
  }
}

@keyframes fadeUp {
  from { opacity: 0; transform: translateY(24px); }
  to   { opacity: 1; transform: translateY(0); }
}

@media (max-width: 900px) { .hero-mascot { opacity: 0.06; } }
@media (max-width: 600px) {
  .hero-title { font-size: 3.8rem; }
  .hero-stat__num { font-size: 2rem; }
  .hero-stats { gap: 1.5rem; }
}
</style>
