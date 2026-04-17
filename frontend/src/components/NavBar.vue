<template>
  <q-header class="dk-header" :class="{ 'dk-header--scrolled': scrolled }">
    <q-toolbar class="dk-toolbar">
      <!-- Logo -->
      <router-link to="/" class="nav-logo">
        <img src="/logo.jpg" alt="DK Dārtplēsis logo" class="nav-logo__img" />
        <span class="nav-logo__text">DK <span class="nav-logo__accent">Dārtplēsis</span></span>
      </router-link>

      <q-space />

      <!-- Desktop Nav Links -->
      <nav class="nav-links gt-sm" aria-label="Main navigation">
      <template v-for="link in navLinks" :key="link.labelKey">
        <router-link
          v-if="!link.children || link.children.length === 0"
          :to="link.to"
          class="nav-link"
          active-class="nav-link--active"
        >
          {{ t(link.labelKey) }}
        </router-link>

        <q-btn-dropdown
          v-else
          flat
          no-caps
          dense
          class="nav-link nav-link--dropdown"
          dropdown-icon="expand_more"
          :label="t(link.labelKey)"
          menu-anchor="bottom left"
          menu-self="top left"
          content-class="dk-dropdown-menu"
        >
          <q-list class="dropdown-list">
            <q-item v-for="sublink in link.children" :key="sublink.to" clickable v-close-popup :to="sublink.to">
              <q-item-section>
                <q-item-label>{{ t(sublink.labelKey) }}</q-item-label>
              </q-item-section>
            </q-item>
          </q-list>
        </q-btn-dropdown>
      </template>
      </nav>

      <!-- Language switcher -->
      <div class="lang-switcher gt-sm">
        <button
          v-for="lang in langs"
          :key="lang.code"
          class="lang-btn"
          :class="{ 'lang-btn--active': locale === lang.code }"
          @click="switchLocale(lang.code)"
        >
          {{ lang.label }}
        </button>
      </div>

      <!-- Desktop CTA -->
      <router-link to="/pievienojies" class="nav-cta gt-sm">
        {{ t('nav.join') }}
      </router-link>

      <!-- Mobile Hamburger -->
      <button
        class="hamburger lt-md"
        :class="{ open: mobileOpen }"
        aria-label="Menu"
        :aria-expanded="mobileOpen"
        @click="mobileOpen = !mobileOpen"
      >
        <span /><span /><span />
      </button>
    </q-toolbar>

    <!-- Mobile Menu -->
    <transition name="mobile-slide">
      <div v-if="mobileOpen" class="mobile-menu" role="navigation">
        <template v-for="link in navLinks" :key="link.labelKey">
          <router-link
            v-if="!link.children || link.children.length === 0"
            :to="link.to"
            class="mobile-link"
            active-class="mobile-link--active"
            @click="mobileOpen = false"
          >
            {{ t(link.labelKey) }}
          </router-link>

          <q-expansion-item
            v-else
            class="mobile-expansion"
            expand-separator
            :label="t(link.labelKey)"
            header-class="mobile-link mobile-link--dropdown"
            expand-icon-class="text-teal-3"
          >
            <q-list>
              <q-item
                v-for="sublink in link.children"
                :key="sublink.to"
                clickable
                v-ripple
                :to="sublink.to"
                class="mobile-sublink"
                @click="mobileOpen = false"
              >
                <q-item-section>{{ t(sublink.labelKey) }}</q-item-section>
              </q-item>
            </q-list>
          </q-expansion-item>
        </template>
        <router-link
          to="/pievienojies"
          class="mobile-link mobile-link--cta"
          @click="mobileOpen = false"
        >
          → {{ t('nav.join') }}
        </router-link>

        <!-- Mobile lang switcher -->
        <div class="mobile-lang">
          <button
            v-for="lang in langs"
            :key="lang.code"
            class="lang-btn"
            :class="{ 'lang-btn--active': locale === lang.code }"
            @click="switchLocale(lang.code)"
          >
            {{ lang.label }}
          </button>
        </div>
      </div>
    </transition>
  </q-header>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useI18n } from 'vue-i18n'

const { t, locale } = useI18n()

const mobileOpen = ref(false)
const scrolled    = ref(false)

const navLinks = [
  { labelKey: 'nav.about',   to: '/par-mums' },
  { labelKey: 'nav.team',    to: '/komanda'  },
  { labelKey: 'nav.events',  to: '/notikumi' },
  { labelKey: 'nav.blog',      to: '/blog'   },
  { labelKey: 'nav.contact', to: '/kontakti' },
  {
    labelKey: 'nav.useful',
    to: '/noderigi',
    children: [
      { labelKey: 'nav.useful_setup',    to: '/noderigi/laukums'   },
      { labelKey: 'nav.useful_drills',   to: '/noderigi/trenini'   },
      { labelKey: 'nav.useful_checkout', to: '/noderigi/checkout'  },
      { labelKey: 'nav.useful_rules',    to: '/noderigi/noteikumi' }
    ]
  },
]

const langs = [
  { code: 'lv', label: 'LV' },
  { code: 'en', label: 'EN' },
]

function switchLocale(code) {
  locale.value = code
  localStorage.setItem('dk_locale', code)
  mobileOpen.value = false
}

function onScroll() {
  scrolled.value = window.scrollY > 40
}

onMounted(() => window.addEventListener('scroll', onScroll))
onUnmounted(() => window.removeEventListener('scroll', onScroll))
</script>

<style scoped lang="scss">
.dk-header {
  background: rgba(10, 10, 10, 0.85) !important;
  backdrop-filter: blur(16px);
  -webkit-backdrop-filter: blur(16px);
  border-bottom: 1px solid rgba(78, 205, 196, 0.15) !important;
  transition: border-color 0.3s;
  height: auto !important;

  &--scrolled { border-bottom-color: rgba(78, 205, 196, 0.28) !important; }
}

.dk-toolbar {
  height: 68px;
  min-height: 68px !important;
  padding: 0 5vw;
}

// Logo
.nav-logo {
  display: flex;
  align-items: center;
  gap: 12px;
  text-decoration: none;
  flex-shrink: 0;

  &__img {
    width: 42px;
    height: 42px;
    object-fit: contain;
    filter: drop-shadow(0 0 8px rgba(78, 205, 196, 0.4));
  }

  &__text {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 1.4rem;
    letter-spacing: 0.08em;
    color: #f5f5f5;
    line-height: 1;
  }

  &__accent { color: #4ecdc4; }
}

// Desktop nav links
.nav-links {
  display: flex;
  gap: 2rem;
  margin-right: 1.5rem;
  align-items: center;
}

.nav-link {
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 0.85rem;
  font-weight: 600;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: #888;
  text-decoration: none;
  transition: color 0.2s;
  position: relative;
  display: inline-flex;
  align-items: center;
  height: 40px;

  &::after {
    content: '';
    position: absolute;
    bottom: -4px;
    left: 0;
    right: 0;
    height: 1.5px;
    background: #4ecdc4;
    transform: scaleX(0);
    transition: transform 0.2s;
    transform-origin: left;
  }

  &:hover,
  &--active {
    color: #4ecdc4;
    &::after { transform: scaleX(1); }
  }
}

.nav-link--dropdown :deep(.q-btn__content) {
  gap: 6px;
}

.nav-link--dropdown :deep(.q-btn__content),
.nav-link--dropdown :deep(.q-btn__content span) {
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 0.85rem;
  font-weight: 600;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: inherit;
}

.nav-link--dropdown:hover {
  color: #4ecdc4;
}

.dropdown-list {
  background: transparent;
  border: none;
  min-width: 220px;
}

.dk-dropdown-menu {
  background: rgba(10, 10, 10, 0.95);
  backdrop-filter: blur(14px);
  -webkit-backdrop-filter: blur(14px);
  border: 1px solid rgba(78, 205, 196, 0.18);
  border-radius: 14px;
  overflow: hidden;
}

.dropdown-list :deep(.q-item) {
  color: #eaeaea;
}

.dropdown-list :deep(.q-item:hover) {
  background: rgba(78, 205, 196, 0.08);
}

// Language switcher
.lang-switcher {
  display: flex;
  gap: 2px;
  margin-right: 1.5rem;
  border: 1px solid rgba(78, 205, 196, 0.2);
}

.lang-btn {
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 0.72rem;
  font-weight: 700;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: #555;
  background: transparent;
  border: none;
  padding: 4px 10px;
  cursor: pointer;
  transition: color 0.2s, background 0.2s;
  line-height: 1;

  &--active {
    color: #0a0a0a;
    background: #4ecdc4;
  }

  &:not(&--active):hover { color: #4ecdc4; }
}

// Desktop CTA
.nav-cta {
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 0.8rem;
  font-weight: 700;
  letter-spacing: 0.14em;
  text-transform: uppercase;
  color: #0a0a0a;
  background: #4ecdc4;
  padding: 8px 20px;
  text-decoration: none;
  clip-path: polygon(8px 0%, 100% 0%, calc(100% - 8px) 100%, 0% 100%);
  transition: background 0.2s, transform 0.2s;
  line-height: 1;

  &:hover {
    background: #6ee0d9;
    transform: translateY(-1px);
    color: #0a0a0a;
  }
}

// Hamburger
.hamburger {
  display: flex;
  flex-direction: column;
  gap: 5px;
  cursor: pointer;
  padding: 4px;
  background: none;
  border: none;
  outline: none;

  span {
    display: block;
    width: 24px;
    height: 2px;
    background: #4ecdc4;
    transition: all 0.3s;
  }

  &.open span:nth-child(1) { transform: rotate(45deg) translate(5px, 5px); }
  &.open span:nth-child(2) { opacity: 0; }
  &.open span:nth-child(3) { transform: rotate(-45deg) translate(5px, -5px); }
}

// Mobile menu
.mobile-menu {
  background: rgba(10, 10, 10, 0.97);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  padding: 2rem 5vw;
  border-bottom: 1px solid rgba(78, 205, 196, 0.15);
  display: flex;
  flex-direction: column;
  gap: 0;
  max-height: calc(100vh - 68px);
  overflow-y: auto;
  overscroll-behavior: contain;
  padding-bottom: 2.5rem;
}

.mobile-expansion :deep(.q-expansion-item__container) {
  border-bottom: 1px solid rgba(78, 205, 196, 0.12);
}

.mobile-expansion :deep(.q-item),
.mobile-expansion :deep(.q-item__label) {
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 1.3rem;
  font-weight: 600;
  letter-spacing: 0.1em;
  text-transform: uppercase;
}

.mobile-sublink {
  padding-left: 18px;
  color: rgba(245, 245, 245, 0.9);
  font-size: 1.05rem;
  font-weight: 500;
  letter-spacing: 0.06em;
  text-transform: none;
}

.mobile-link {
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 1.3rem;
  font-weight: 600;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: #f5f5f5;
  text-decoration: none;
  padding-bottom: 1rem;
  border-bottom: 1px solid rgba(78, 205, 196, 0.15);
  transition: color 0.2s;
  display: block;

  &--active { color: #4ecdc4; }

  &--cta {
    color: #4ecdc4;
    border-bottom: none;
    padding-bottom: 0;
  }
}

.mobile-lang {
  display: flex;
  gap: 2px;
  border: 1px solid rgba(78, 205, 196, 0.2);
  align-self: flex-start;
}

// Transition
.mobile-slide-enter-active,
.mobile-slide-leave-active { transition: all 0.3s ease; }
.mobile-slide-enter-from,
.mobile-slide-leave-to      { opacity: 0; transform: translateY(-10px); }
</style>
