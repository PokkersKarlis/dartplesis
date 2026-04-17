<template>
  <q-page class="dk-page">
    <AboutSection />
    <FeaturesSection />
  </q-page>
</template>

<script setup>
import AboutSection    from 'src/components/sections/AboutSection.vue'
import FeaturesSection from 'src/components/sections/FeaturesSection.vue'
import { useScrollReveal } from 'src/composables/useScrollReveal'

useScrollReveal()
</script>

<style scoped lang="scss">
.dk-page {
  background: #0a0a0a;
  min-height: 100vh;
}
</style>
