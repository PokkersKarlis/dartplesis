<template>
  <section class="dk-section dk-section--dark reveal" id="notikumi">
    <div class="dk-eyebrow">{{ t('events.eyebrow') }}</div>
    <h2 class="dk-title">
      {{ t('events.title') }} <span class="text-teal">{{ t('events.title_accent') }}</span>
    </h2>

    <div class="events-list">
      <div
        v-for="event in events"
        :key="event.title"
        class="event-item"
      >
        <div class="event-date">
          <div class="event-date__day">{{ event.day }}</div>
          <div class="event-date__month">{{ event.month }}</div>
        </div>

        <div class="event-info">
          <h3 class="event-info__title">{{ event.title }}</h3>
          <p class="event-info__meta">{{ event.meta }}</p>
        </div>

        <q-badge
          :class="['event-badge', `event-badge--${event.badgeType}`]"
          :label="t(`events.badges.${event.badgeType}`)"
          outline
        />
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'

const { t, tm } = useI18n()
const events = computed(() => tm('events.items'))
</script>

<style scoped lang="scss">
.text-teal { color: #4ecdc4; }

.events-list {
  margin-top: 2.5rem;
  display: flex;
  flex-direction: column;
  gap: 1px;
  background: rgba(78, 205, 196, 0.15);
}

.event-item {
  display: grid;
  grid-template-columns: 80px 1fr auto;
  align-items: center;
  gap: 2rem;
  background: #161616;
  padding: 1.5rem 2rem;
  transition: background 0.2s;

  &:hover { background: #1c1c1c; }
}

.event-date {
  text-align: center;

  &__day {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 2.5rem;
    color: #4ecdc4;
    line-height: 1;
  }

  &__month {
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.15em;
    text-transform: uppercase;
    color: #888;
  }
}

.event-info {
  &__title {
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 1.1rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    color: #f5f5f5;
    margin-bottom: 0.25rem;
  }

  &__meta {
    font-size: 0.82rem;
    color: #888;
    margin: 0;
  }
}

.event-badge {
  font-family: 'Barlow Condensed', sans-serif !important;
  font-size: 0.7rem !important;
  font-weight: 700 !important;
  letter-spacing: 0.12em !important;
  text-transform: uppercase !important;
  padding: 5px 12px !important;
  border-radius: 0 !important;

  &--open { color: #4ecdc4 !important; border-color: #4ecdc4 !important; }
  &--team { color: #888 !important;    border-color: #888 !important; }
  &--hot  { color: #e74c3c !important; border-color: #e74c3c !important; }
}

@media (max-width: 600px) {
  .event-item { grid-template-columns: 60px 1fr; }
  .event-badge { display: none !important; }
}
</style>
