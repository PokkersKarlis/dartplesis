<template>
  <section class="contact-section dk-section reveal" id="kontakti">

    <!-- Header -->
    <div class="contact-header">
      <div class="dk-eyebrow">{{ t('contact.eyebrow') }}</div>
      <h2 class="dk-title">
        {{ t('contact.title') }} <span class="text-teal">{{ t('contact.title_accent') }}</span>
      </h2>
    </div>

    <!-- 3-column grid: info · map · actions -->
    <div class="contact-grid">

      <!-- ── Column 1: contact details ───────────────────────────────────── -->
      <div class="contact-info">

        <div class="contact-item">
          <span class="ci-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
              <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/>
              <circle cx="12" cy="9" r="2.5"/>
            </svg>
          </span>
          <div>
            <div class="ci-label">{{ t('contact.items.address.label') }}</div>
            <div class="ci-value">{{ t('contact.items.address.value') }}</div>
          </div>
        </div>

        <div class="contact-item">
          <span class="ci-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
              <rect x="2" y="4" width="20" height="16" rx="2"/>
              <path d="m2 7 10 7 10-7"/>
            </svg>
          </span>
          <div>
            <div class="ci-label">{{ t('contact.items.email.label') }}</div>
            <div class="ci-value">
              <a href="mailto:info@dartplesis.lv">dkdartplesis@gmail.com</a>
            </div>
          </div>
        </div>

        <div class="contact-item">
          <span class="ci-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
              <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 1.27h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.96a16 16 0 0 0 6.13 6.13l.96-.96a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/>
            </svg>
          </span>
          <div>
            <div class="ci-label">{{ t('contact.items.phone.label') }}</div>
            <div class="ci-value">
              <a :href="`tel:${phoneRaw}`">{{ phoneDisplay }}</a>
            </div>
          </div>
        </div>

        <div class="contact-item">
          <span class="ci-icon">
            <svg viewBox="0 0 24 24" fill="currentColor">
              <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12.07h2.54V9.845c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12.07h2.773l-.443 2.89h-2.33v6.988C20.343 21.128 24 16.991 24 12.073z"/>
            </svg>
          </span>
          <div>
            <div class="ci-label">{{ t('contact.items.fb.label') }}</div>
            <div class="ci-value">
              <a href="https://www.facebook.com/p/DK-D%C4%81rtpl%C4%93sis-61572260401164/" target="_blank" rel="noopener">DK Dārtplēsis</a>
            </div>
          </div>
        </div>

        <div class="contact-item">
          <span class="ci-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="10"/>
              <polyline points="12 6 12 12 16 14"/>
            </svg>
          </span>
          <div>
            <div class="ci-label">{{ t('contact.items.hours.label') }}</div>
            <div class="ci-value">{{ t('contact.items.hours.value') }}</div>
          </div>
        </div>

      </div>

      <!-- ── Column 2: map placeholder ───────────────────────────────────── -->
      <div class="contact-map">
        <div class="map-placeholder">
          <img src="/map.png" :alt="t('contact.map_alt')" class="map-img" />
        </div>
      </div>

    </div>
  </section>
</template>

<script setup>
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

// ← paste your number here (international format, no spaces)
const phoneRaw     = '+37122811821'      // e.g. '+37112345678'
const phoneDisplay = '+371 22 811 821'  // e.g. '+371 12 345 678'
</script>

<style scoped lang="scss">
.contact-section {
  padding-bottom: 4rem;
}

.text-teal { color: #4ecdc4; }

.contact-header {
  margin-bottom: 2.5rem;
}

// ── 3-column grid ─────────────────────────────────────────────────────────────

.contact-grid {
  display: grid;
  grid-template-columns: 1fr 280px 1fr;
  gap: 2.5rem;
  align-items: start;
}

// ── Contact info ──────────────────────────────────────────────────────────────

.contact-info {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.contact-item {
  display: flex;
  align-items: flex-start;
  gap: 0.875rem;
}

.ci-icon {
  flex-shrink: 0;
  width: 36px;
  height: 36px;
  border-radius: 8px;
  background: rgba(78, 205, 196, 0.08);
  border: 1px solid rgba(78, 205, 196, 0.15);
  display: flex;
  align-items: center;
  justify-content: center;
  color: #4ecdc4;

  svg {
    width: 16px;
    height: 16px;
  }
}

.ci-label {
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 0.65rem;
  font-weight: 700;
  letter-spacing: 0.18em;
  text-transform: uppercase;
  color: rgba(255, 255, 255, 0.35);
  margin-bottom: 2px;
}

.ci-value {
  font-size: 0.9rem;
  color: #e0e0e0;
  line-height: 1.4;

  a {
    color: #e0e0e0;
    text-decoration: none;
    transition: color 0.2s;
    &:hover { color: #4ecdc4; }
  }
}

// ── Map placeholder ───────────────────────────────────────────────────────────

.contact-map {
  display: flex;
  align-items: stretch;
}

.map-placeholder {
  width: 100%;
  aspect-ratio: 1 / 1;
  border-radius: 14px;
  border: 1px solid rgba(78, 205, 196, 0.12);
  overflow: hidden;
}

.map-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
  border-radius: inherit;
}

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

// ── Responsive ────────────────────────────────────────────────────────────────

@media (max-width: 1000px) {
  .contact-grid {
    grid-template-columns: 1fr 220px;
    grid-template-rows: auto auto;
  }
  .contact-actions {
    grid-column: 1 / -1;
    flex-direction: row;
    flex-wrap: wrap;
  }
  .action-btn { flex: 1; min-width: 160px; }
}

@media (max-width: 640px) {
  .contact-grid {
    grid-template-columns: 1fr;
  }
  .contact-map { max-width: 280px; }
}
</style>
